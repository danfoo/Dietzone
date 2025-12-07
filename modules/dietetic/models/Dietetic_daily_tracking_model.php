<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Model pour le suivi quotidien des patients (daily tracking)
 * Gère l'hydratation, les repas, l'activité physique et les calories
 *
 * @author Eric Gilles SAGNA
 * @website https://maestrodan.art
 */
class Dietetic_daily_tracking_model extends App_Model
{
    private $table = 'dietic_daily_tracking';

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('dietetic/dietetic');
    }

    /**
     * Get tracking by ID
     *
     * @param int $id
     * @param bool $check_access
     * @return object|null
     */
    public function get($id, $check_access = true)
    {
        $this->db->where('id', $id);
        $tracking = $this->db->get(db_prefix() . $this->table)->row();

        if ($tracking && $check_access) {
            if (!dietetic_can_access_patient($tracking->patient_id)) {
                return null;
            }
        }

        return $tracking;
    }

    /**
     * Get today's tracking for a patient
     * Si aucun enregistrement n'existe, retourne un objet vide avec des valeurs par défaut
     *
     * @param int $patient_id
     * @return object
     */
    public function get_today($patient_id)
    {
        // DEBUG: Check permission result
        $has_permission = dietetic_can_access_patient($patient_id);
        log_message('debug', 'DAILY TRACKING GET_TODAY - Patient ID: ' . $patient_id);
        log_message('debug', 'DAILY TRACKING GET_TODAY - Permission Check: ' . ($has_permission ? 'GRANTED' : 'DENIED'));

        // Check access permissions
        if (!$has_permission) {
            log_activity('Unauthorized attempt to access daily tracking for Patient ID ' . $patient_id);
            log_message('error', 'DAILY TRACKING - PERMISSION DENIED for patient ' . $patient_id . ' - Returning empty tracking');
            return $this->get_empty_tracking($patient_id);
        }

        $today = date('Y-m-d');

        $this->db->where('patient_id', $patient_id);
        $this->db->where('tracking_date', $today);
        $tracking = $this->db->get(db_prefix() . $this->table)->row();

        // DEBUG: Log what we retrieved
        log_message('debug', 'DAILY TRACKING GET_TODAY for patient ' . $patient_id . ' on ' . $today);
        log_message('debug', 'DAILY TRACKING FOUND: ' . ($tracking ? 'YES' : 'NO'));
        if ($tracking) {
            log_message('debug', 'DAILY TRACKING DATA: ' . print_r($tracking, true));
        }

        // Si pas de tracking aujourd'hui, retourner valeurs par défaut
        if (!$tracking) {
            return $this->get_empty_tracking($patient_id);
        }

        return $tracking;
    }

    /**
     * Get tracking for a specific date
     *
     * @param int $patient_id
     * @param string $date Format: Y-m-d
     * @return object|null
     */
    public function get_by_date($patient_id, $date)
    {
        if (!dietetic_can_access_patient($patient_id)) {
            return null;
        }

        $this->db->where('patient_id', $patient_id);
        $this->db->where('tracking_date', $date);
        return $this->db->get(db_prefix() . $this->table)->row();
    }

    /**
     * Get tracking history for a patient
     *
     * @param int $patient_id
     * @param int $days Nombre de jours à récupérer (par défaut 30)
     * @return array
     */
    public function get_history($patient_id, $days = 30)
    {
        if (!dietetic_can_access_patient($patient_id)) {
            return [];
        }

        $date_from = date('Y-m-d', strtotime("-{$days} days"));

        $this->db->where('patient_id', $patient_id);
        $this->db->where('tracking_date >=', $date_from);
        $this->db->order_by('tracking_date', 'DESC');

        return $this->db->get(db_prefix() . $this->table)->result();
    }

    /**
     * Create or update today's tracking
     * Utilise INSERT ... ON DUPLICATE KEY UPDATE pour gérer automatiquement create/update
     *
     * @param int $patient_id
     * @param array $data Données à mettre à jour (water_glasses, breakfast_checked, etc.)
     * @return bool
     */
    public function update_today($patient_id, $data)
    {
        if (!dietetic_can_access_patient($patient_id)) {
            log_activity('Unauthorized attempt to update daily tracking for Patient ID ' . $patient_id);
            return false;
        }

        $today = date('Y-m-d');

        // Préparer les données
        $tracking_data = [
            'patient_id' => $patient_id,
            'tracking_date' => $today,
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Fusionner avec les données fournies
        $tracking_data = array_merge($tracking_data, $data);

        // Construire la requête INSERT ... ON DUPLICATE KEY UPDATE
        $table = db_prefix() . $this->table;

        // Colonnes pour INSERT
        $columns = array_keys($tracking_data);
        $values = array_values($tracking_data);

        // Construire les placeholders
        $placeholders = array_fill(0, count($values), '?');

        // Construire la partie UPDATE (toutes les colonnes sauf patient_id et tracking_date)
        $update_parts = [];
        foreach ($data as $key => $value) {
            $update_parts[] = "`{$key}` = VALUES(`{$key}`)";
        }
        $update_parts[] = "`updated_at` = CURRENT_TIMESTAMP";

        $sql = "INSERT INTO `{$table}` (" . implode(', ', array_map(function($col) { return "`{$col}`"; }, $columns)) . ")
                VALUES (" . implode(', ', $placeholders) . ")
                ON DUPLICATE KEY UPDATE " . implode(', ', $update_parts);

        try {
            // DEBUG: Log the SQL query
            log_message('debug', 'DAILY TRACKING SQL: ' . $sql);
            log_message('debug', 'DAILY TRACKING VALUES: ' . print_r($values, true));

            $result = $this->db->query($sql, $values);

            // DEBUG: Check affected rows
            log_message('debug', 'DAILY TRACKING AFFECTED ROWS: ' . $this->db->affected_rows());

            // Verify the data was saved
            $verify = $this->get_today($patient_id);
            log_message('debug', 'DAILY TRACKING VERIFY AFTER SAVE: ' . print_r($verify, true));

            return true;
        } catch (Exception $e) {
            log_activity('Error updating daily tracking: ' . $e->getMessage());
            log_message('error', 'DAILY TRACKING SQL ERROR: ' . $e->getMessage());
            log_message('error', 'DAILY TRACKING SQL: ' . $sql);
            return false;
        }
    }

    /**
     * Increment water glasses
     *
     * @param int $patient_id
     * @param int $amount Nombre de verres à ajouter (peut être négatif pour décrémenter)
     * @return bool
     */
    public function update_water($patient_id, $amount = 1)
    {
        $tracking = $this->get_today($patient_id);
        $current = $tracking->water_glasses ?? 0;
        $new_value = max(0, min(20, $current + $amount)); // Limiter entre 0 et 20

        return $this->update_today($patient_id, ['water_glasses' => $new_value]);
    }

    /**
     * Toggle meal check (breakfast, lunch, dinner)
     *
     * @param int $patient_id
     * @param string $meal_type 'breakfast', 'lunch', ou 'dinner'
     * @param bool|null $checked Si null, toggle automatiquement
     * @return bool
     */
    public function toggle_meal($patient_id, $meal_type, $checked = null)
    {
        $valid_meals = ['breakfast', 'lunch', 'dinner'];
        if (!in_array($meal_type, $valid_meals)) {
            return false;
        }

        $field = $meal_type . '_checked';

        if ($checked === null) {
            // Auto-toggle
            $tracking = $this->get_today($patient_id);
            $current = $tracking->$field ?? 0;
            $checked = !$current;
        }

        return $this->update_today($patient_id, [$field => $checked ? 1 : 0]);
    }

    /**
     * Add or update activity minutes
     *
     * @param int $patient_id
     * @param int $minutes
     * @param string|null $activity_type
     * @return bool
     */
    public function update_activity($patient_id, $minutes, $activity_type = null)
    {
        $data = ['activity_minutes' => max(0, min(600, $minutes))]; // Limiter à 0-600 min

        if ($activity_type !== null) {
            $data['activity_type'] = $activity_type;
        }

        return $this->update_today($patient_id, $data);
    }

    /**
     * Update calories consumed
     *
     * @param int $patient_id
     * @param int $calories
     * @return bool
     */
    public function update_calories($patient_id, $calories)
    {
        return $this->update_today($patient_id, ['calories_consumed' => max(0, $calories)]);
    }

    /**
     * Update mood
     *
     * @param int $patient_id
     * @param string $mood 'great', 'good', 'neutral', 'bad'
     * @return bool
     */
    public function update_mood($patient_id, $mood)
    {
        $valid_moods = ['great', 'good', 'neutral', 'bad'];
        if (!in_array($mood, $valid_moods)) {
            return false;
        }

        return $this->update_today($patient_id, ['mood' => $mood]);
    }

    /**
     * Update notes
     *
     * @param int $patient_id
     * @param string $notes
     * @return bool
     */
    public function update_notes($patient_id, $notes)
    {
        return $this->update_today($patient_id, ['notes' => $notes]);
    }

    /**
     * Calculate streak (nombre de jours consécutifs avec au moins une action)
     * Une "action" = au moins un des critères suivants:
     * - water_glasses > 0
     * - breakfast_checked OR lunch_checked OR dinner_checked
     * - activity_minutes > 0
     *
     * @param int $patient_id
     * @return int Nombre de jours consécutifs
     */
    public function calculate_streak($patient_id)
    {
        if (!dietetic_can_access_patient($patient_id)) {
            return 0;
        }

        // Récupérer les 365 derniers jours (max streak possible)
        $this->db->select('tracking_date, water_glasses, breakfast_checked, lunch_checked, dinner_checked, activity_minutes');
        $this->db->where('patient_id', $patient_id);
        $this->db->where('tracking_date >=', date('Y-m-d', strtotime('-365 days')));
        $this->db->order_by('tracking_date', 'DESC');
        $history = $this->db->get(db_prefix() . $this->table)->result();

        if (empty($history)) {
            return 0;
        }

        $streak = 0;
        $expected_date = date('Y-m-d');

        foreach ($history as $day) {
            // Vérifier si cette journée a au moins une action
            $has_activity = (
                $day->water_glasses > 0 ||
                $day->breakfast_checked == 1 ||
                $day->lunch_checked == 1 ||
                $day->dinner_checked == 1 ||
                $day->activity_minutes > 0
            );

            if (!$has_activity) {
                // Pas d'activité ce jour, le streak est rompu
                break;
            }

            // Vérifier que c'est bien le jour attendu (consécutif)
            if ($day->tracking_date != $expected_date) {
                break;
            }

            $streak++;
            // Préparer la date attendue pour le prochain jour (veille)
            $expected_date = date('Y-m-d', strtotime($day->tracking_date . ' -1 day'));
        }

        return $streak;
    }

    /**
     * Get completion percentage for today
     * Basé sur 4 critères: eau (25%), 3 repas (25%), activité (25%), calories (25%)
     *
     * @param int $patient_id
     * @return int Pourcentage de 0 à 100
     */
    public function get_completion_percentage($patient_id)
    {
        $tracking = $this->get_today($patient_id);
        $score = 0;

        // Eau (25 points si >= 8 verres)
        if ($tracking->water_glasses >= 8) {
            $score += 25;
        } elseif ($tracking->water_glasses > 0) {
            $score += round(($tracking->water_glasses / 8) * 25);
        }

        // Repas (25 points si les 3 validés)
        $meals_checked = ($tracking->breakfast_checked ? 1 : 0) +
                        ($tracking->lunch_checked ? 1 : 0) +
                        ($tracking->dinner_checked ? 1 : 0);
        $score += round(($meals_checked / 3) * 25);

        // Activité (25 points si >= 30 min)
        if ($tracking->activity_minutes >= 30) {
            $score += 25;
        } elseif ($tracking->activity_minutes > 0) {
            $score += round(($tracking->activity_minutes / 30) * 25);
        }

        // Calories (25 points si renseignées)
        if ($tracking->calories_consumed > 0) {
            $score += 25;
        }

        return min(100, $score);
    }

    /**
     * Get empty tracking object with default values
     *
     * @param int $patient_id
     * @return object
     */
    private function get_empty_tracking($patient_id)
    {
        return (object)[
            'id' => null,
            'patient_id' => $patient_id,
            'tracking_date' => date('Y-m-d'),
            'water_glasses' => 0,
            'breakfast_checked' => 0,
            'lunch_checked' => 0,
            'dinner_checked' => 0,
            'activity_minutes' => 0,
            'activity_type' => null,
            'calories_consumed' => null,
            'mood' => null,
            'notes' => null,
            'created_at' => null,
            'updated_at' => null
        ];
    }

    /**
     * Delete tracking record
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $tracking = $this->get($id);
        if (!$tracking) {
            return false;
        }

        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . $this->table);

        log_activity('Daily Tracking Deleted [ID: ' . $id . ', Patient: ' . $tracking->patient_id . ']');

        return true;
    }

    /**
     * Get weekly summary statistics
     *
     * @param int $patient_id
     * @return object
     */
    public function get_weekly_summary($patient_id)
    {
        if (!dietetic_can_access_patient($patient_id)) {
            return null;
        }

        $week_start = date('Y-m-d', strtotime('monday this week'));
        $week_end = date('Y-m-d', strtotime('sunday this week'));

        $this->db->select('
            COUNT(*) as days_tracked,
            SUM(water_glasses) as total_water,
            AVG(water_glasses) as avg_water,
            SUM(breakfast_checked) as breakfasts,
            SUM(lunch_checked) as lunches,
            SUM(dinner_checked) as dinners,
            SUM(activity_minutes) as total_activity,
            AVG(activity_minutes) as avg_activity,
            AVG(calories_consumed) as avg_calories
        ');
        $this->db->where('patient_id', $patient_id);
        $this->db->where('tracking_date >=', $week_start);
        $this->db->where('tracking_date <=', $week_end);

        return $this->db->get(db_prefix() . $this->table)->row();
    }
}
