<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Gamification Model
 * Gère les badges, points, niveaux et achievements
 */
class Dietetic_gamification_model extends App_Model
{
    private $table_badge_definitions = 'dietic_badge_definitions';
    private $table_patient_badges = 'dietic_patient_badges';
    private $table_patient_points = 'dietic_patient_points';
    private $table_points_history = 'dietic_points_history';

    // Configuration des niveaux
    private $levels = [
        'debutant' => ['min' => 0, 'name' => 'Débutant', 'icon' => 'fa-star', 'color' => '#95A5A6'],
        'bronze' => ['min' => 100, 'name' => 'Bronze', 'icon' => 'fa-certificate', 'color' => '#CD7F32'],
        'argent' => ['min' => 300, 'name' => 'Argent', 'icon' => 'fa-shield', 'color' => '#C0C0C0'],
        'or' => ['min' => 600, 'name' => 'Or', 'icon' => 'fa-star', 'color' => '#FFD700'],
        'platine' => ['min' => 1000, 'name' => 'Platine', 'icon' => 'fa-diamond', 'color' => '#E5E4E2'],
        'diamant' => ['min' => 2000, 'name' => 'Diamant', 'icon' => 'fa-gem', 'color' => '#B9F2FF']
    ];

    public function __construct()
    {
        parent::__construct();
    }

    // ==================== BADGES ====================

    /**
     * Get all badge definitions
     */
    public function get_all_badges($active_only = true)
    {
        if ($active_only) {
            $this->db->where('is_active', 1);
        }
        $this->db->order_by('display_order', 'ASC');
        return $this->db->get(db_prefix() . $this->table_badge_definitions)->result();
    }

    /**
     * Get badges by category
     */
    public function get_badges_by_category($category)
    {
        $this->db->where('category', $category);
        $this->db->where('is_active', 1);
        $this->db->order_by('requirement_value', 'ASC');
        return $this->db->get(db_prefix() . $this->table_badge_definitions)->result();
    }

    /**
     * Get patient unlocked badges
     */
    public function get_patient_badges($patient_id, $seen_only = false)
    {
        $this->db->select('pb.*, bd.badge_key, bd.name, bd.description, bd.icon, bd.color, bd.category, bd.points');
        $this->db->from(db_prefix() . $this->table_patient_badges . ' pb');
        $this->db->join(db_prefix() . $this->table_badge_definitions . ' bd', 'bd.id = pb.badge_id');
        $this->db->where('pb.patient_id', $patient_id);

        if ($seen_only) {
            $this->db->where('pb.seen', 1);
        }

        $this->db->order_by('pb.unlocked_at', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Get patient badge wall (all badges with unlock status)
     */
    public function get_patient_badge_wall($patient_id)
    {
        // Get all badges
        $all_badges = $this->get_all_badges();

        // Get unlocked badges
        $unlocked = $this->get_patient_badges($patient_id);
        $unlocked_ids = array_column($unlocked, 'badge_id');

        // Combine
        $badge_wall = [];
        foreach ($all_badges as $badge) {
            $badge_wall[] = [
                'id' => $badge->id,
                'badge_key' => $badge->badge_key,
                'name' => $badge->name,
                'description' => $badge->description,
                'icon' => $badge->icon,
                'color' => $badge->color,
                'category' => $badge->category,
                'requirement_type' => $badge->requirement_type,
                'requirement_value' => $badge->requirement_value,
                'points' => $badge->points,
                'unlocked' => in_array($badge->id, $unlocked_ids),
                'unlocked_at' => in_array($badge->id, $unlocked_ids)
                    ? $this->get_badge_unlock_date($patient_id, $badge->id)
                    : null
            ];
        }

        return $badge_wall;
    }

    /**
     * Get badge unlock date
     */
    private function get_badge_unlock_date($patient_id, $badge_id)
    {
        $this->db->select('unlocked_at');
        $this->db->where('patient_id', $patient_id);
        $this->db->where('badge_id', $badge_id);
        $result = $this->db->get(db_prefix() . $this->table_patient_badges)->row();
        return $result ? $result->unlocked_at : null;
    }

    /**
     * Unlock badge for patient
     */
    public function unlock_badge($patient_id, $badge_key, $progress_value = null)
    {
        // Get badge definition
        $this->db->where('badge_key', $badge_key);
        $badge = $this->db->get(db_prefix() . $this->table_badge_definitions)->row();

        if (!$badge) {
            log_activity("Badge key '{$badge_key}' not found");
            return false;
        }

        // Check if already unlocked
        $this->db->where('patient_id', $patient_id);
        $this->db->where('badge_id', $badge->id);
        $existing = $this->db->get(db_prefix() . $this->table_patient_badges)->row();

        if ($existing) {
            return false; // Already unlocked
        }

        // Insert badge unlock
        $data = [
            'patient_id' => $patient_id,
            'badge_id' => $badge->id,
            'progress_value' => $progress_value,
            'unlocked_at' => date('Y-m-d H:i:s'),
            'notification_sent' => 0,
            'seen' => 0
        ];

        $this->db->insert(db_prefix() . $this->table_patient_badges, $data);
        $unlock_id = $this->db->insert_id();

        if ($unlock_id) {
            // Award points
            $this->award_points($patient_id, $badge->points, 'badge_unlocked',
                "Badge débloqué: {$badge->name}", $badge->id);

            // Send notification
            $this->send_badge_notification($patient_id, $badge);

            log_activity("Badge '{$badge->name}' unlocked for patient {$patient_id}");
            return $unlock_id;
        }

        return false;
    }

    /**
     * Mark badges as seen
     */
    public function mark_badges_as_seen($patient_id, $badge_ids = null)
    {
        $this->db->where('patient_id', $patient_id);

        if ($badge_ids) {
            $this->db->where_in('badge_id', $badge_ids);
        }

        return $this->db->update(db_prefix() . $this->table_patient_badges, ['seen' => 1]);
    }

    /**
     * Get unseen badges count
     */
    public function get_unseen_badges_count($patient_id)
    {
        $this->db->where('patient_id', $patient_id);
        $this->db->where('seen', 0);
        return $this->db->count_all_results(db_prefix() . $this->table_patient_badges);
    }

    // ==================== POINTS & LEVELS ====================

    /**
     * Get patient points and level info
     */
    public function get_patient_points($patient_id)
    {
        $this->db->where('patient_id', $patient_id);
        $points = $this->db->get(db_prefix() . $this->table_patient_points)->row();

        if (!$points) {
            // Create default entry
            $this->initialize_patient_points($patient_id);
            return $this->get_patient_points($patient_id);
        }

        // Add level information
        $points->level_info = $this->get_level_info($points->current_level);
        $points->next_level = $this->get_next_level($points->current_level);
        $points->progress_to_next = $this->calculate_level_progress($points->total_points, $points->current_level);

        return $points;
    }

    /**
     * Initialize points for new patient
     */
    private function initialize_patient_points($patient_id)
    {
        $data = [
            'patient_id' => $patient_id,
            'total_points' => 0,
            'current_level' => 'debutant',
            'level_progress' => 0,
            'points_today' => 0,
            'points_this_week' => 0,
            'points_this_month' => 0,
            'last_activity_date' => date('Y-m-d')
        ];

        return $this->db->insert(db_prefix() . $this->table_patient_points, $data);
    }

    /**
     * Award points to patient
     */
    public function award_points($patient_id, $points, $action_type, $description = null, $reference_id = null)
    {
        if ($points <= 0) {
            return false;
        }

        // Get current points
        $patient_points = $this->get_patient_points($patient_id);

        // Calculate new totals
        $new_total = $patient_points->total_points + $points;
        $today = date('Y-m-d');

        // Prepare update
        $update_data = [
            'total_points' => $new_total,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Update daily/weekly/monthly if same period
        if ($patient_points->last_activity_date == $today) {
            $update_data['points_today'] = $patient_points->points_today + $points;
        } else {
            $update_data['points_today'] = $points;
            $update_data['last_activity_date'] = $today;
        }

        // Check week
        $week_start = date('Y-m-d', strtotime('monday this week'));
        if ($patient_points->last_activity_date >= $week_start) {
            $update_data['points_this_week'] = $patient_points->points_this_week + $points;
        } else {
            $update_data['points_this_week'] = $points;
        }

        // Check month
        $month_start = date('Y-m-01');
        if ($patient_points->last_activity_date >= $month_start) {
            $update_data['points_this_month'] = $patient_points->points_this_month + $points;
        } else {
            $update_data['points_this_month'] = $points;
        }

        // Check for level up
        $old_level = $patient_points->current_level;
        $new_level = $this->calculate_level($new_total);

        if ($new_level != $old_level) {
            $update_data['current_level'] = $new_level;
            // Send level up notification
            $this->send_level_up_notification($patient_id, $new_level);
        }

        // Update points
        $this->db->where('patient_id', $patient_id);
        $this->db->update(db_prefix() . $this->table_patient_points, $update_data);

        // Log in history
        $history_data = [
            'patient_id' => $patient_id,
            'points' => $points,
            'action_type' => $action_type,
            'action_description' => $description,
            'reference_id' => $reference_id
        ];

        $this->db->insert(db_prefix() . $this->table_points_history, $history_data);

        log_activity("Awarded {$points} points to patient {$patient_id} for {$action_type}");

        return true;
    }

    /**
     * Get points history for patient
     */
    public function get_points_history($patient_id, $limit = 50)
    {
        $this->db->where('patient_id', $patient_id);
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($limit);
        return $this->db->get(db_prefix() . $this->table_points_history)->result();
    }

    /**
     * Calculate level based on total points
     */
    private function calculate_level($total_points)
    {
        $level = 'debutant';

        foreach ($this->levels as $key => $info) {
            if ($total_points >= $info['min']) {
                $level = $key;
            }
        }

        return $level;
    }

    /**
     * Get level information
     */
    private function get_level_info($level_key)
    {
        return isset($this->levels[$level_key]) ? $this->levels[$level_key] : $this->levels['debutant'];
    }

    /**
     * Get next level
     */
    private function get_next_level($current_level)
    {
        $levels_array = array_keys($this->levels);
        $current_index = array_search($current_level, $levels_array);

        if ($current_index === false || $current_index >= count($levels_array) - 1) {
            return null; // Max level reached
        }

        $next_key = $levels_array[$current_index + 1];
        return $this->levels[$next_key];
    }

    /**
     * Calculate progress to next level (percentage)
     */
    private function calculate_level_progress($total_points, $current_level)
    {
        $current_info = $this->get_level_info($current_level);
        $next_level = $this->get_next_level($current_level);

        if (!$next_level) {
            return 100; // Max level
        }

        $points_in_level = $total_points - $current_info['min'];
        $points_needed = $next_level['min'] - $current_info['min'];

        if ($points_needed <= 0) {
            return 100;
        }

        return min(100, round(($points_in_level / $points_needed) * 100));
    }

    // ==================== BADGE CHECKING ====================

    /**
     * Check and award all eligible badges for a patient
     */
    public function check_and_award_badges($patient_id)
    {
        $this->load->model('dietetic/dietetic_daily_tracking_model');
        $this->load->model('dietetic/dietetic_measurements_model');
        $this->load->model('dietetic/dietetic_activities_model');

        $new_badges = 0;

        // Check streak badges
        $streak = $this->dietetic_daily_tracking_model->calculate_streak($patient_id);
        $new_badges += $this->check_streak_badges($patient_id, $streak);

        // Check weight loss badges
        $new_badges += $this->check_weight_loss_badges($patient_id);

        // Check nutrition badges
        $new_badges += $this->check_nutrition_badges($patient_id);

        // Check hydration badges
        $new_badges += $this->check_hydration_badges($patient_id);

        // Check activity badges
        $new_badges += $this->check_activity_badges($patient_id);

        return $new_badges;
    }

    /**
     * Check streak badges
     */
    private function check_streak_badges($patient_id, $streak)
    {
        $badges_to_check = [
            1 => 'first_day',
            7 => 'week_warrior',
            30 => 'month_master',
            100 => 'unstoppable',
            365 => 'legend'
        ];

        $new_badges = 0;

        foreach ($badges_to_check as $days => $badge_key) {
            if ($streak >= $days) {
                if ($this->unlock_badge($patient_id, $badge_key, $streak)) {
                    $new_badges++;
                }
            }
        }

        return $new_badges;
    }

    /**
     * Check weight loss badges
     */
    private function check_weight_loss_badges($patient_id)
    {
        $measurements = $this->dietetic_measurements_model->get_by_patient($patient_id);

        if (empty($measurements) || count($measurements) < 2) {
            return 0;
        }

        $first_measurement = end($measurements);
        $latest_measurement = reset($measurements);

        $starting_weight = floatval($first_measurement->weight);
        $current_weight = floatval($latest_measurement->weight);
        $weight_lost = $starting_weight - $current_weight;

        if ($weight_lost <= 0) {
            return 0;
        }

        $badges_to_check = [
            5 => 'weight_5kg',
            10 => 'weight_10kg',
            15 => 'weight_15kg',
            20 => 'weight_20kg',
            25 => 'weight_25kg'
        ];

        $new_badges = 0;

        foreach ($badges_to_check as $kg => $badge_key) {
            if ($weight_lost >= $kg) {
                if ($this->unlock_badge($patient_id, $badge_key, $weight_lost)) {
                    $new_badges++;
                }
            }
        }

        return $new_badges;
    }

    /**
     * Check nutrition badges
     */
    private function check_nutrition_badges($patient_id)
    {
        // Count days with meals logged
        $this->db->select('COUNT(DISTINCT tracking_date) as days_count');
        $this->db->where('patient_id', $patient_id);
        $this->db->group_start();
        $this->db->where('breakfast_checked', 1);
        $this->db->or_where('lunch_checked', 1);
        $this->db->or_where('dinner_checked', 1);
        $this->db->group_end();
        $result = $this->db->get(db_prefix() . 'dietic_daily_tracking')->row();

        $days_logged = $result ? $result->days_count : 0;

        $badges_to_check = [
            7 => 'meal_beginner',
            30 => 'meal_expert',
            90 => 'meal_master'
        ];

        $new_badges = 0;

        foreach ($badges_to_check as $days => $badge_key) {
            if ($days_logged >= $days) {
                if ($this->unlock_badge($patient_id, $badge_key, $days_logged)) {
                    $new_badges++;
                }
            }
        }

        return $new_badges;
    }

    /**
     * Check hydration badges
     */
    private function check_hydration_badges($patient_id)
    {
        // Count days with water logged
        $this->db->select('COUNT(DISTINCT tracking_date) as days_count');
        $this->db->where('patient_id', $patient_id);
        $this->db->where('water_glasses >', 0);
        $result = $this->db->get(db_prefix() . 'dietic_daily_tracking')->row();

        $days_logged = $result ? $result->days_count : 0;

        $badges_to_check = [
            7 => 'water_warrior',
            30 => 'hydration_hero',
            90 => 'water_master'
        ];

        $new_badges = 0;

        foreach ($badges_to_check as $days => $badge_key) {
            if ($days_logged >= $days) {
                if ($this->unlock_badge($patient_id, $badge_key, $days_logged)) {
                    $new_badges++;
                }
            }
        }

        return $new_badges;
    }

    /**
     * Check activity badges
     */
    private function check_activity_badges($patient_id)
    {
        // Count activities
        $this->db->where('patient_id', $patient_id);
        $activities_count = $this->db->count_all_results(db_prefix() . 'dietic_patient_activities');

        $badges_to_check = [
            5 => 'active_start',
            20 => 'fitness_fan',
            50 => 'sport_champion'
        ];

        $new_badges = 0;

        foreach ($badges_to_check as $count => $badge_key) {
            if ($activities_count >= $count) {
                if ($this->unlock_badge($patient_id, $badge_key, $activities_count)) {
                    $new_badges++;
                }
            }
        }

        return $new_badges;
    }

    // ==================== NOTIFICATIONS ====================

    /**
     * Send badge unlock notification
     */
    private function send_badge_notification($patient_id, $badge)
    {
        $this->load->model('dietetic/dietetic_notifications_model');

        $title = "🏆 Nouveau badge débloqué !";
        $message = "Félicitations ! Vous avez débloqué le badge '{$badge->name}' - {$badge->description}";

        $this->dietetic_notifications_model->send_notification([
            'patient_id' => $patient_id,
            'type' => 'badge_unlocked',
            'subject' => $title,
            'message' => $message,
            'channels' => [
                'push' => 1,
                'email' => 0,
                'sms' => 0,
                'whatsapp' => 0
            ]
        ]);

        // Mark as sent
        $this->db->where('patient_id', $patient_id);
        $this->db->where('badge_id', $badge->id);
        $this->db->update(db_prefix() . $this->table_patient_badges, ['notification_sent' => 1]);
    }

    /**
     * Send level up notification
     */
    private function send_level_up_notification($patient_id, $new_level)
    {
        $this->load->model('dietetic/dietetic_notifications_model');

        $level_info = $this->get_level_info($new_level);

        $title = "⭐ Nouveau niveau atteint !";
        $message = "Bravo ! Vous êtes maintenant niveau {$level_info['name']} !";

        $this->dietetic_notifications_model->send_notification([
            'patient_id' => $patient_id,
            'type' => 'level_up',
            'subject' => $title,
            'message' => $message,
            'channels' => [
                'push' => 1,
                'email' => 0,
                'sms' => 0,
                'whatsapp' => 0
            ]
        ]);
    }

    // ==================== STATISTICS ====================

    /**
     * Get gamification statistics
     */
    public function get_statistics()
    {
        // Total badges unlocked
        $total_badges_unlocked = $this->db->count_all_results(db_prefix() . $this->table_patient_badges);

        // Total points awarded
        $this->db->select_sum('points');
        $result = $this->db->get(db_prefix() . $this->table_points_history)->row();
        $total_points_awarded = $result ? $result->points : 0;

        // Patients with badges
        $this->db->select('DISTINCT patient_id');
        $patients_with_badges = $this->db->count_all_results(db_prefix() . $this->table_patient_badges);

        // Most unlocked badge
        $this->db->select('bd.name, COUNT(*) as unlock_count');
        $this->db->from(db_prefix() . $this->table_patient_badges . ' pb');
        $this->db->join(db_prefix() . $this->table_badge_definitions . ' bd', 'bd.id = pb.badge_id');
        $this->db->group_by('pb.badge_id');
        $this->db->order_by('unlock_count', 'DESC');
        $this->db->limit(1);
        $most_unlocked = $this->db->get()->row();

        return [
            'total_badges_unlocked' => $total_badges_unlocked,
            'total_points_awarded' => $total_points_awarded,
            'patients_with_badges' => $patients_with_badges,
            'most_unlocked_badge' => $most_unlocked
        ];
    }
}
