<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dietetic_food_surveys_model extends App_Model
{
    // NOTE: Table names include the 'tbl' prefix - do NOT use db_prefix() with these
    private $table_surveys = 'tbldietic_food_surveys';
    private $table_entries = 'tbldietic_food_survey_entries';
    private $table_beverages = 'tbldietic_food_survey_beverages';
    private $table_recommendations = 'tbldietic_food_survey_recommendations';
    private $table_comments = 'tbldietic_food_survey_comments';

    public function __construct()
    {
        parent::__construct();

        // Load dietetic helper for permissions
        $this->load->helper('dietetic/dietetic');
    }

    /**
     * Get all food surveys
     *
     * @param array $where
     * @return array
     */
    public function get_all($where = [])
    {
        $this->db->select($this->table_surveys . '.*,
            CONCAT(c.company) as patient_name,
            CONCAT(s.firstname, " ", s.lastname) as dietitian_name,
            prog.program_name');
        $this->db->from($this->table_surveys);
        $this->db->join('tbldietic_patients p', 'p.id = ' . $this->table_surveys . '.patient_id', 'left');
        $this->db->join('tblclients c', 'c.userid = p.client_id', 'left');
        $this->db->join('tblstaff s', 's.staffid = ' . $this->table_surveys . '.dietitian_id', 'left');
        $this->db->join('tbldietic_programs prog', 'prog.id = ' . $this->table_surveys . '.program_id', 'left');

        if (!empty($where)) {
            $this->db->where($where);
        }

        // Apply staff permissions using new many-to-many system
        if ($this->db->table_exists(db_prefix() . 'dietic_patient_dietitians')) {
            // Use new permission system - joins on p.id
            dietetic_apply_dietitian_filter($this->db, 'pd');
        } else {
            // Fallback to old system if table doesn't exist yet
            if (!is_admin()) {
                $this->db->where($this->table_surveys . '.dietitian_id', get_staff_user_id());
            }
        }

        $this->db->group_by($this->table_surveys . '.id'); // Group by to avoid duplicates from join
        $this->db->order_by($this->table_surveys . '.created_at', 'DESC');

        return $this->db->get()->result();
    }

    /**
     * Get single food survey
     *
     * @param int $id
     * @param bool $check_access If true, verify user has access to this survey
     * @return object|null
     */
    public function get($id, $check_access = true)
    {
        $this->db->select($this->table_surveys . '.*,
            CONCAT(c.company) as patient_name,
            p.client_id,
            CONCAT(s.firstname, " ", s.lastname) as dietitian_name,
            prog.program_name');
        $this->db->from($this->table_surveys);
        $this->db->join('tbldietic_patients p', 'p.id = ' . $this->table_surveys . '.patient_id', 'left');
        $this->db->join('tblclients c', 'c.userid = p.client_id', 'left');
        $this->db->join('tblstaff s', 's.staffid = ' . $this->table_surveys . '.dietitian_id', 'left');
        $this->db->join('tbldietic_programs prog', 'prog.id = ' . $this->table_surveys . '.program_id', 'left');
        $this->db->where($this->table_surveys . '.id', $id);

        $survey = $this->db->get()->row();

        if ($survey && $check_access) {
            // Check access permissions if not admin
            if (!dietetic_can_access_patient($survey->patient_id)) {
                return null;
            }
        }

        return $survey;
    }

    /**
     * Add new food survey
     *
     * @param array $data
     * @return int|bool
     */
    public function add($data)
    {
        // Check access permissions to patient
        if (isset($data['patient_id']) && !dietetic_can_access_patient($data['patient_id'])) {
            log_activity('Unauthorized attempt to create food survey for Patient ID ' . $data['patient_id']);
            return false;
        }

        // Calculate end date based on duration
        if (isset($data['start_date']) && isset($data['duration_days'])) {
            $start_date = new DateTime($data['start_date']);
            $start_date->modify('+' . $data['duration_days'] . ' days');
            $data['end_date'] = $start_date->format('Y-m-d');
        }

        $data['created_at'] = date('Y-m-d H:i:s');

        if ($this->db->insert($this->table_surveys, $data)) {
            $survey_id = $this->db->insert_id();
            log_activity('New Food Survey Created [ID: ' . $survey_id . ']');

            // Send notification to patient
            if (isset($data['patient_id']) && isset($data['name'])) {
                try {
                    $this->load->model('dietetic/dietetic_notifications_model');

                    // Get dietitian name
                    $dietitian_id = isset($data['dietitian_id']) ? $data['dietitian_id'] : get_staff_user_id();
                    $this->db->select('CONCAT(firstname, " ", lastname) as name');
                    $this->db->where('staffid', $dietitian_id);
                    $dietitian = $this->db->get(db_prefix() . 'staff')->row();
                    $dietitian_name = $dietitian ? $dietitian->name : 'Votre diététicien';

                    $this->dietetic_notifications_model->notify_food_survey_assigned(
                        $data['patient_id'],
                        $data['name'],
                        $data['start_date'] ?? date('Y-m-d'),
                        $data['duration_days'] ?? 7,
                        $dietitian_name
                    );
                } catch (Exception $e) {
                    log_activity('Food survey notification failed [ID: ' . $survey_id . ']: ' . $e->getMessage());
                }
            }

            return $survey_id;
        }

        return false;
    }

    /**
     * Update food survey
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data)
    {
        // Get survey to check access
        $survey = $this->get($id);
        if (!$survey) {
            return false;
        }

        // Check access permissions
        if (!dietetic_can_access_patient($survey->patient_id)) {
            log_activity('Unauthorized attempt to update food survey [ID: ' . $id . ']');
            return false;
        }

        // Recalculate end date if start_date or duration changed
        if (isset($data['start_date']) || isset($data['duration_days'])) {
            $start_date = isset($data['start_date']) ? $data['start_date'] : $survey->start_date;
            $duration = isset($data['duration_days']) ? $data['duration_days'] : $survey->duration_days;

            $date = new DateTime($start_date);
            $date->modify('+' . $duration . ' days');
            $data['end_date'] = $date->format('Y-m-d');
        }

        $data['updated_at'] = date('Y-m-d H:i:s');

        $this->db->where('id', $id);
        if ($this->db->update($this->table_surveys, $data)) {
            log_activity('Food Survey Updated [ID: ' . $id . ']');
            return true;
        }

        return false;
    }

    /**
     * Delete food survey
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        // Get survey to check access
        $survey = $this->get($id, false); // Don't check access yet, we'll do it manually
        if (!$survey) {
            return false;
        }

        // Check access permissions
        if (!dietetic_can_access_patient($survey->patient_id)) {
            log_activity('Unauthorized attempt to delete food survey [ID: ' . $id . ']');
            return false;
        }

        $this->db->where('id', $id);
        if ($this->db->delete($this->table_surveys)) {
            log_activity('Food Survey Deleted [ID: ' . $id . ']');
            return true;
        }

        return false;
    }

    /**
     * Get all entries for a survey
     *
     * @param int $survey_id
     * @return array
     */
    public function get_entries($survey_id)
    {
        $this->db->select($this->table_entries . '.*,
            (SELECT COUNT(*) FROM ' . $this->table_recommendations . '
             WHERE ' . $this->table_recommendations . '.entry_id = ' . $this->table_entries . '.id) as recommendation_count');
        $this->db->from($this->table_entries);
        $this->db->where('survey_id', $survey_id);
        $this->db->order_by('entry_date', 'DESC');

        return $this->db->get()->result();
    }

    /**
     * Get single entry
     *
     * @param int $id
     * @return object|null
     */
    public function get_entry($id)
    {
        $this->db->where('id', $id);
        return $this->db->get($this->table_entries)->row();
    }

    /**
     * Get entry by survey and date
     *
     * @param int $survey_id
     * @param string $date
     * @return object|null
     */
    public function get_entry_by_date($survey_id, $date)
    {
        $this->db->where('survey_id', $survey_id);
        $this->db->where('entry_date', $date);
        return $this->db->get($this->table_entries)->row();
    }

    /**
     * Add or update daily entry
     *
     * @param array $data
     * @return int|bool
     */
    public function save_entry($data)
    {
        // Check if entry exists for this survey and date
        $existing = $this->get_entry_by_date($data['survey_id'], $data['entry_date']);

        if ($existing) {
            // Update existing entry - merge with existing data to preserve other meals
            // Only update fields that have non-empty values
            $update_data = [];

            // Always update these fields
            $update_data['updated_at'] = date('Y-m-d H:i:s');
            $update_data['submitted_at'] = $data['submitted_at'];

            // Breakfast fields - only update if provided
            if (!empty($data['breakfast_photo'])) {
                $update_data['breakfast_photo'] = $data['breakfast_photo'];
            }
            if (!empty($data['breakfast_time']) || isset($data['breakfast_time'])) {
                $update_data['breakfast_time'] = $data['breakfast_time'];
            }
            if (!empty($data['breakfast_notes']) || isset($data['breakfast_notes'])) {
                $update_data['breakfast_notes'] = $data['breakfast_notes'];
            }

            // Lunch fields - only update if provided
            if (!empty($data['lunch_photo'])) {
                $update_data['lunch_photo'] = $data['lunch_photo'];
            }
            if (!empty($data['lunch_time']) || isset($data['lunch_time'])) {
                $update_data['lunch_time'] = $data['lunch_time'];
            }
            if (!empty($data['lunch_notes']) || isset($data['lunch_notes'])) {
                $update_data['lunch_notes'] = $data['lunch_notes'];
            }

            // Dinner fields - only update if provided
            if (!empty($data['dinner_photo'])) {
                $update_data['dinner_photo'] = $data['dinner_photo'];
            }
            if (!empty($data['dinner_time']) || isset($data['dinner_time'])) {
                $update_data['dinner_time'] = $data['dinner_time'];
            }
            if (!empty($data['dinner_notes']) || isset($data['dinner_notes'])) {
                $update_data['dinner_notes'] = $data['dinner_notes'];
            }

            // Snack/Collation fields - only update if provided
            if (!empty($data['snack_photo'])) {
                $update_data['snack_photo'] = $data['snack_photo'];
            }
            if (!empty($data['snack_time']) || isset($data['snack_time'])) {
                $update_data['snack_time'] = $data['snack_time'];
            }
            if (!empty($data['snack_notes']) || isset($data['snack_notes'])) {
                $update_data['snack_notes'] = $data['snack_notes'];
            }

            // Water quantity - update if provided
            if (isset($data['water_quantity_ml']) && $data['water_quantity_ml'] !== '') {
                $update_data['water_quantity_ml'] = $data['water_quantity_ml'];
            }

            $this->db->where('id', $existing->id);
            if ($this->db->update($this->table_entries, $update_data)) {
                return $existing->id;
            }
            return false;
        } else {
            // Insert new entry
            $data['created_at'] = date('Y-m-d H:i:s');
            if ($this->db->insert($this->table_entries, $data)) {
                return $this->db->insert_id();
            }
            return false;
        }
    }

    /**
     * Get beverages for an entry
     *
     * @param int $entry_id
     * @return array
     */
    public function get_beverages($entry_id)
    {
        $this->db->where('entry_id', $entry_id);
        $this->db->order_by('consumption_time', 'ASC');
        return $this->db->get($this->table_beverages)->result();
    }

    /**
     * Add beverage
     *
     * @param array $data
     * @return int|bool
     */
    public function add_beverage($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');

        if ($this->db->insert($this->table_beverages, $data)) {
            return $this->db->insert_id();
        }

        return false;
    }

    /**
     * Delete beverage
     *
     * @param int $id
     * @return bool
     */
    public function delete_beverage($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->table_beverages);
    }

    /**
     * Delete all beverages for an entry
     *
     * @param int $entry_id
     * @return bool
     */
    public function delete_beverages_by_entry($entry_id)
    {
        $this->db->where('entry_id', $entry_id);
        return $this->db->delete($this->table_beverages);
    }

    /**
     * Get recommendations for an entry
     *
     * @param int $entry_id
     * @param string $meal_type Optional filter by meal type (breakfast, lunch, dinner, global)
     * @return array
     */
    public function get_recommendations($entry_id, $meal_type = null)
    {
        $this->db->select($this->table_recommendations . '.*,
            CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as dietitian_name,
            tblstaff.profile_image');
        $this->db->from($this->table_recommendations);
        $this->db->join('tblstaff', 'tblstaff.staffid = ' . $this->table_recommendations . '.dietitian_id', 'left');
        $this->db->where('entry_id', $entry_id);

        if ($meal_type !== null) {
            $this->db->where('meal_type', $meal_type);
        }

        $this->db->order_by($this->table_recommendations . '.created_at', 'DESC');

        return $this->db->get()->result();
    }

    /**
     * Get recommendations by meal type for an entry
     *
     * @param int $entry_id
     * @return array Array with keys 'breakfast', 'lunch', 'dinner', 'snack', 'global'
     */
    public function get_recommendations_by_meal($entry_id)
    {
        $recommendations = $this->get_recommendations($entry_id);

        $grouped = [
            'breakfast' => [],
            'lunch' => [],
            'dinner' => [],
            'snack' => [],
            'global' => []
        ];

        // Check if meal_type column exists (for backward compatibility)
        $columns = $this->db->list_fields($this->table_recommendations);
        $has_meal_type_column = in_array('meal_type', $columns);

        foreach ($recommendations as $recommendation) {
            // Determine meal type
            if (!$has_meal_type_column) {
                // Column doesn't exist, treat all as global
                $meal_type = 'global';
            } elseif (isset($recommendation->meal_type) && !empty($recommendation->meal_type)) {
                // Column exists and has a value
                $meal_type = $recommendation->meal_type;
            } else {
                // Column exists but is NULL or empty, default to global
                $meal_type = 'global';
            }

            // Make sure the meal_type is valid
            if (!isset($grouped[$meal_type])) {
                $meal_type = 'global';
            }

            $grouped[$meal_type][] = $recommendation;
        }

        return $grouped;
    }

    /**
     * Add recommendation
     *
     * @param array $data
     * @return int|bool
     */
    public function add_recommendation($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');

        // Check if meal_type column exists (for backward compatibility)
        $columns = $this->db->list_fields($this->table_recommendations);
        if (!in_array('meal_type', $columns) && isset($data['meal_type'])) {
            // Column doesn't exist yet, remove it to avoid SQL error
            unset($data['meal_type']);
        }

        if ($this->db->insert($this->table_recommendations, $data)) {
            $recommendation_id = $this->db->insert_id();

            // Mark entry as having recommendation
            $this->db->where('id', $data['entry_id']);
            $this->db->update($this->table_entries, ['has_recommendation' => 1]);

            return $recommendation_id;
        }

        return false;
    }

    /**
     * Update recommendation
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update_recommendation($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        $this->db->where('id', $id);
        return $this->db->update($this->table_recommendations, $data);
    }

    /**
     * Delete recommendation
     *
     * @param int $id
     * @return bool
     */
    public function delete_recommendation($id)
    {
        // Get entry_id before deleting
        $recommendation = $this->db->where('id', $id)->get($this->table_recommendations)->row();

        if ($recommendation) {
            $this->db->where('id', $id);
            $deleted = $this->db->delete($this->table_recommendations);

            if ($deleted) {
                // Check if entry still has recommendations
                $count = $this->db->where('entry_id', $recommendation->entry_id)
                    ->count_all_results($this->table_recommendations);

                if ($count == 0) {
                    $this->db->where('id', $recommendation->entry_id);
                    $this->db->update($this->table_entries, ['has_recommendation' => 0]);
                }
            }

            return $deleted;
        }

        return false;
    }

    /**
     * Get comments for a recommendation
     *
     * @param int $recommendation_id
     * @return array
     */
    public function get_comments($recommendation_id)
    {
        $this->db->where('recommendation_id', $recommendation_id);
        $this->db->order_by('created_at', 'ASC');
        return $this->db->get($this->table_comments)->result();
    }

    /**
     * Add comment
     *
     * @param array $data
     * @return int|bool
     */
    public function add_comment($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');

        if ($this->db->insert($this->table_comments, $data)) {
            return $this->db->insert_id();
        }

        return false;
    }

    /**
     * Delete comment
     *
     * @param int $id
     * @return bool
     */
    public function delete_comment($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->table_comments);
    }

    /**
     * Get surveys by patient
     *
     * @param int $patient_id
     * @return array
     */
    public function get_by_patient($patient_id)
    {
        // Check access permissions
        if (!dietetic_can_access_patient($patient_id)) {
            log_activity('Unauthorized attempt to access food surveys for Patient ID ' . $patient_id);
            return [];
        }

        return $this->get_all([$this->table_surveys . '.patient_id' => $patient_id]);
    }

    /**
     * Get active surveys by patient
     *
     * @param int $patient_id
     * @return array
     */
    public function get_active_by_patient($patient_id)
    {
        // Check access permissions
        if (!dietetic_can_access_patient($patient_id)) {
            log_activity('Unauthorized attempt to access active food surveys for Patient ID ' . $patient_id);
            return [];
        }

        $today = date('Y-m-d');

        $this->db->where('patient_id', $patient_id);
        $this->db->where('status', 'active');
        // Exclure les enquêtes dont la date de fin est dépassée
        $this->db->group_start();
        $this->db->where('end_date >=', $today);
        $this->db->or_where('end_date IS NULL');
        $this->db->group_end();
        $this->db->order_by('created_at', 'DESC');

        // NOTE: $this->table_surveys already includes 'tbl' prefix
        return $this->db->get($this->table_surveys)->result();
    }

    /**
     * Get completion percentage
     *
     * @param int $survey_id
     * @return float
     */
    public function get_completion_percentage($survey_id)
    {
        $survey = $this->get($survey_id); // This already checks access
        if (!$survey) {
            return 0;
        }

        $total_days = $survey->duration_days;

        $this->db->where('survey_id', $survey_id);
        $this->db->where('submitted_at IS NOT NULL');
        $completed_days = $this->db->count_all_results($this->table_entries);

        if ($total_days == 0) {
            return 0;
        }

        return round(($completed_days / $total_days) * 100, 2);
    }

    /**
     * Mettre à jour automatiquement les enquêtes alimentaires expirées
     * Met le statut à 'completed' pour les enquêtes dont la end_date est dépassée
     *
     * @return int Nombre d'enquêtes mises à jour
     */
    public function update_expired_surveys()
    {
        $today = date('Y-m-d');

        // Mettre à jour les enquêtes dont la date de fin est dépassée
        $this->db->where('status', 'active');
        $this->db->where('end_date <', $today);
        $this->db->update(db_prefix() . $this->table, [
            'status' => 'completed',
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $affected_rows = $this->db->affected_rows();

        if ($affected_rows > 0) {
            log_activity("Auto-completed $affected_rows expired food surveys");
        }

        return $affected_rows;
    }
}
