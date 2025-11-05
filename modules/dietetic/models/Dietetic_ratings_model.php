<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dietetic_ratings_model extends App_Model
{
    private $table = 'dietic_ratings';

    public function __construct()
    {
        parent::__construct();

        // Load required Perfex models
        $this->load->model('staff_model');
        $this->load->model('dietetic/dietetic_patients_model');
    }

    /**
     * Get rating by ID
     *
     * @param int $id
     * @return object|null
     */
    public function get($id)
    {
        $this->db->where('id', $id);
        $rating = $this->db->get(db_prefix() . $this->table)->row();

        if ($rating) {
            // Get patient info
            $rating->patient = $this->dietetic_patients_model->get($rating->patient_id);

            // Get dietitian info
            $rating->dietitian = $this->staff_model->get($rating->dietitian_id);
        }

        return $rating;
    }

    /**
     * Get rating by patient and dietitian
     *
     * @param int $patient_id
     * @param int $dietitian_id
     * @return object|null
     */
    public function get_by_patient_dietitian($patient_id, $dietitian_id)
    {
        $this->db->where('patient_id', $patient_id);
        $this->db->where('dietitian_id', $dietitian_id);

        return $this->db->get(db_prefix() . $this->table)->row();
    }

    /**
     * Get all ratings for a dietitian
     *
     * @param int $dietitian_id
     * @param bool $public_only
     * @return array
     */
    public function get_by_dietitian($dietitian_id, $public_only = true)
    {
        $this->db->select(db_prefix() . $this->table . '.*, ' .
            db_prefix() . 'clients.company as patient_name');
        $this->db->join(db_prefix() . 'dietic_patients',
            db_prefix() . 'dietic_patients.id = ' . db_prefix() . $this->table . '.patient_id', 'left');
        $this->db->join(db_prefix() . 'clients',
            db_prefix() . 'clients.userid = ' . db_prefix() . 'dietic_patients.client_id', 'left');

        $this->db->where(db_prefix() . $this->table . '.dietitian_id', $dietitian_id);

        if ($public_only) {
            $this->db->where(db_prefix() . $this->table . '.is_public', 1);
        }

        $this->db->order_by(db_prefix() . $this->table . '.created_at', 'DESC');

        return $this->db->get(db_prefix() . $this->table)->result();
    }

    /**
     * Get all ratings by a patient
     *
     * @param int $patient_id
     * @return array
     */
    public function get_by_patient($patient_id)
    {
        $this->db->select(db_prefix() . $this->table . '.*, ' .
            'CONCAT(' . db_prefix() . 'staff.firstname, " ", ' . db_prefix() . 'staff.lastname) as dietitian_name');
        $this->db->join(db_prefix() . 'staff',
            db_prefix() . 'staff.staffid = ' . db_prefix() . $this->table . '.dietitian_id', 'left');

        $this->db->where(db_prefix() . $this->table . '.patient_id', $patient_id);
        $this->db->order_by(db_prefix() . $this->table . '.created_at', 'DESC');

        return $this->db->get(db_prefix() . $this->table)->result();
    }

    /**
     * Calculate average ratings for a dietitian
     *
     * @param int $dietitian_id
     * @return object
     */
    public function get_dietitian_average($dietitian_id)
    {
        $this->db->select('
            COUNT(*) as total_ratings,
            AVG(overall_rating) as avg_overall,
            AVG(professionalism_rating) as avg_professionalism,
            AVG(listening_rating) as avg_listening,
            AVG(advice_rating) as avg_advice,
            AVG(results_rating) as avg_results,
            AVG(availability_rating) as avg_availability
        ');
        $this->db->where('dietitian_id', $dietitian_id);
        $this->db->where('is_public', 1);

        return $this->db->get(db_prefix() . $this->table)->row();
    }

    /**
     * Add new rating
     *
     * @param array $data
     * @return int|bool
     */
    public function add($data)
    {
        // Calculate overall rating from criteria
        if (!isset($data['overall_rating'])) {
            $criteria = ['professionalism_rating', 'listening_rating', 'advice_rating', 'results_rating', 'availability_rating'];
            $sum = 0;
            $count = 0;

            foreach ($criteria as $criterion) {
                if (!empty($data[$criterion])) {
                    $sum += $data[$criterion];
                    $count++;
                }
            }

            $data['overall_rating'] = $count > 0 ? round($sum / $count, 1) : 0;
        }

        // Set defaults
        if (!isset($data['is_public'])) {
            $data['is_public'] = 1;
        }

        if (!isset($data['is_verified'])) {
            $data['is_verified'] = 1;
        }

        $data['created_at'] = date('Y-m-d H:i:s');

        // Check if rating already exists
        $existing = $this->get_by_patient_dietitian($data['patient_id'], $data['dietitian_id']);

        if ($existing) {
            // Update existing rating
            return $this->update($existing->id, $data);
        }

        // Insert new rating
        $this->db->insert(db_prefix() . $this->table, $data);

        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            log_activity('New Dietitian Rating Added [ID: ' . $insert_id . ']');
        }

        return $insert_id;
    }

    /**
     * Update rating
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data)
    {
        // Recalculate overall rating if criteria changed
        $criteria = ['professionalism_rating', 'listening_rating', 'advice_rating', 'results_rating', 'availability_rating'];
        $has_criteria_change = false;

        foreach ($criteria as $criterion) {
            if (isset($data[$criterion])) {
                $has_criteria_change = true;
                break;
            }
        }

        if ($has_criteria_change) {
            // Get current data
            $current = $this->get($id);
            $merged = array_merge((array)$current, $data);

            $sum = 0;
            $count = 0;

            foreach ($criteria as $criterion) {
                if (!empty($merged[$criterion])) {
                    $sum += $merged[$criterion];
                    $count++;
                }
            }

            $data['overall_rating'] = $count > 0 ? round($sum / $count, 1) : 0;
        }

        $data['updated_at'] = date('Y-m-d H:i:s');

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . $this->table, $data);

        if ($this->db->affected_rows() > 0) {
            log_activity('Dietitian Rating Updated [ID: ' . $id . ']');
            return true;
        }

        return false;
    }

    /**
     * Delete rating
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . $this->table);

        if ($this->db->affected_rows() > 0) {
            log_activity('Dietitian Rating Deleted [ID: ' . $id . ']');
            return true;
        }

        return false;
    }

    /**
     * Get all dietitians with their average ratings
     *
     * @return array
     */
    public function get_all_dietitians_with_ratings()
    {
        $this->db->select('
            ' . db_prefix() . 'staff.staffid,
            ' . db_prefix() . 'staff.firstname,
            ' . db_prefix() . 'staff.lastname,
            ' . db_prefix() . 'staff.email,
            ' . db_prefix() . 'staff.profile_image,
            COUNT(DISTINCT ' . db_prefix() . 'dietic_patients.id) as total_patients,
            COUNT(DISTINCT ' . db_prefix() . 'dietic_consultations.id) as total_consultations,
            COUNT(DISTINCT ' . db_prefix() . 'dietic_programs.id) as total_programs,
            COUNT(DISTINCT ' . db_prefix() . $this->table . '.id) as total_ratings,
            AVG(' . db_prefix() . $this->table . '.overall_rating) as avg_rating
        ');

        $this->db->from(db_prefix() . 'staff');

        // Join with dietetic tables
        $this->db->join(db_prefix() . 'dietic_patients',
            db_prefix() . 'dietic_patients.dietitian_id = ' . db_prefix() . 'staff.staffid', 'left');
        $this->db->join(db_prefix() . 'dietic_consultations',
            db_prefix() . 'dietic_consultations.dietitian_id = ' . db_prefix() . 'staff.staffid', 'left');
        $this->db->join(db_prefix() . 'dietic_programs',
            db_prefix() . 'dietic_programs.dietitian_id = ' . db_prefix() . 'staff.staffid', 'left');
        $this->db->join(db_prefix() . $this->table,
            db_prefix() . $this->table . '.dietitian_id = ' . db_prefix() . 'staff.staffid AND ' .
            db_prefix() . $this->table . '.is_public = 1', 'left');

        // Filter only active staff who have patients (are dietitians)
        $this->db->where(db_prefix() . 'staff.active', 1);
        $this->db->group_by(db_prefix() . 'staff.staffid');
        $this->db->having('total_patients >', 0);
        $this->db->order_by('avg_rating', 'DESC');

        return $this->db->get()->result();
    }

    /**
     * Check if patient can rate dietitian (has worked with them)
     *
     * @param int $patient_id
     * @param int $dietitian_id
     * @return bool
     */
    public function can_rate($patient_id, $dietitian_id)
    {
        // Check if patient exists and is assigned to this dietitian
        $this->db->where('id', $patient_id);
        $this->db->where('dietitian_id', $dietitian_id);
        $patient = $this->db->get(db_prefix() . 'dietic_patients')->row();

        if (!$patient) {
            return false;
        }

        // Check if they have had at least one completed consultation
        $this->db->where('patient_id', $patient_id);
        $this->db->where('dietitian_id', $dietitian_id);
        $this->db->where('status', 'completed');
        $consultation = $this->db->get(db_prefix() . 'dietic_consultations')->row();

        return $consultation ? true : false;
    }
}
