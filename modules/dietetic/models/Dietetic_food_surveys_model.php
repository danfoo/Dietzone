<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dietetic_food_surveys_model extends App_Model
{
    private $table_surveys = 'tbldietic_food_surveys';
    private $table_entries = 'tbldietic_food_survey_entries';
    private $table_beverages = 'tbldietic_food_survey_beverages';
    private $table_recommendations = 'tbldietic_food_survey_recommendations';
    private $table_comments = 'tbldietic_food_survey_comments';

    public function __construct()
    {
        parent::__construct();
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
            CONCAT(tblclients.company) as patient_name,
            CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as dietitian_name,
            tbldietic_programs.program_name');
        $this->db->from($this->table_surveys);
        $this->db->join('tbldietic_patients', 'tbldietic_patients.id = ' . $this->table_surveys . '.patient_id', 'left');
        $this->db->join('tblclients', 'tblclients.userid = tbldietic_patients.client_id', 'left');
        $this->db->join('tblstaff', 'tblstaff.staffid = ' . $this->table_surveys . '.dietitian_id', 'left');
        $this->db->join('tbldietic_programs', 'tbldietic_programs.id = ' . $this->table_surveys . '.program_id', 'left');

        if (!empty($where)) {
            $this->db->where($where);
        }

        $this->db->order_by($this->table_surveys . '.created_at', 'DESC');

        return $this->db->get()->result();
    }

    /**
     * Get single food survey
     *
     * @param int $id
     * @return object|null
     */
    public function get($id)
    {
        $this->db->select($this->table_surveys . '.*,
            CONCAT(tblclients.company) as patient_name,
            tbldietic_patients.client_id,
            CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as dietitian_name,
            tbldietic_programs.program_name');
        $this->db->from($this->table_surveys);
        $this->db->join('tbldietic_patients', 'tbldietic_patients.id = ' . $this->table_surveys . '.patient_id', 'left');
        $this->db->join('tblclients', 'tblclients.userid = tbldietic_patients.client_id', 'left');
        $this->db->join('tblstaff', 'tblstaff.staffid = ' . $this->table_surveys . '.dietitian_id', 'left');
        $this->db->join('tbldietic_programs', 'tbldietic_programs.id = ' . $this->table_surveys . '.program_id', 'left');
        $this->db->where($this->table_surveys . '.id', $id);

        return $this->db->get()->row();
    }

    /**
     * Add new food survey
     *
     * @param array $data
     * @return int|bool
     */
    public function add($data)
    {
        // Calculate end date based on duration
        if (isset($data['start_date']) && isset($data['duration_days'])) {
            $start_date = new DateTime($data['start_date']);
            $start_date->modify('+' . $data['duration_days'] . ' days');
            $data['end_date'] = $start_date->format('Y-m-d');
        }

        $data['created_at'] = date('Y-m-d H:i:s');

        if ($this->db->insert($this->table_surveys, $data)) {
            return $this->db->insert_id();
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
        // Recalculate end date if start_date or duration changed
        if (isset($data['start_date']) || isset($data['duration_days'])) {
            $survey = $this->get($id);
            $start_date = isset($data['start_date']) ? $data['start_date'] : $survey->start_date;
            $duration = isset($data['duration_days']) ? $data['duration_days'] : $survey->duration_days;

            $date = new DateTime($start_date);
            $date->modify('+' . $duration . ' days');
            $data['end_date'] = $date->format('Y-m-d');
        }

        $data['updated_at'] = date('Y-m-d H:i:s');

        $this->db->where('id', $id);
        return $this->db->update($this->table_surveys, $data);
    }

    /**
     * Delete food survey
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->table_surveys);
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
     * Get recommendations for an entry
     *
     * @param int $entry_id
     * @return array
     */
    public function get_recommendations($entry_id)
    {
        $this->db->select($this->table_recommendations . '.*,
            CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as dietitian_name,
            tblstaff.profile_image');
        $this->db->from($this->table_recommendations);
        $this->db->join('tblstaff', 'tblstaff.staffid = ' . $this->table_recommendations . '.dietitian_id', 'left');
        $this->db->where('entry_id', $entry_id);
        $this->db->order_by($this->table_recommendations . '.created_at', 'DESC');

        return $this->db->get()->result();
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
        return $this->get_all([
            $this->table_surveys . '.patient_id' => $patient_id,
            $this->table_surveys . '.status' => 'active'
        ]);
    }

    /**
     * Get completion percentage
     *
     * @param int $survey_id
     * @return float
     */
    public function get_completion_percentage($survey_id)
    {
        $survey = $this->get($survey_id);
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
}
