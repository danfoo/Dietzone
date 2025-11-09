<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dietetic_measurements_model extends App_Model
{
    private $table = 'dietic_measurements';

    public function __construct()
    {
        parent::__construct();

        // Load dietetic helper for permissions
        $this->load->helper('dietetic/dietetic');
    }

    /**
     * Get measurement by ID
     *
     * @param int $id
     * @param bool $check_access If true, verify user has access to this measurement
     * @return object|null
     */
    public function get($id, $check_access = true)
    {
        $this->db->where('id', $id);
        $measurement = $this->db->get(db_prefix() . $this->table)->row();

        if ($measurement && $check_access) {
            // Check access permissions
            if (!dietetic_can_access_patient($measurement->patient_id)) {
                return null;
            }
        }

        return $measurement;
    }

    /**
     * Get all measurements for a patient
     *
     * @param int $patient_id
     * @param int $limit
     * @return array
     */
    public function get_by_patient($patient_id, $limit = null)
    {
        // Check access permissions
        if (!dietetic_can_access_patient($patient_id)) {
            log_activity('Unauthorized attempt to access measurements for Patient ID ' . $patient_id);
            return [];
        }

        $this->db->where('patient_id', $patient_id);
        $this->db->order_by('measurement_date', 'DESC');

        if ($limit) {
            $this->db->limit($limit);
        }

        return $this->db->get(db_prefix() . $this->table)->result();
    }

    /**
     * Add new measurement
     *
     * @param array $data
     * @return int|bool
     */
    public function add($data)
    {
        // Check access permissions to patient
        if (isset($data['patient_id']) && !dietetic_can_access_patient($data['patient_id'])) {
            log_activity('Unauthorized attempt to add measurement for Patient ID ' . $data['patient_id']);
            return false;
        }

        // Auto-calculate BMI if weight and patient height available
        if (!empty($data['weight']) && !isset($data['bmi'])) {
            $this->load->model('dietetic/dietetic_patients_model');
            $patient = $this->dietetic_patients_model->get($data['patient_id']);

            if ($patient && $patient->height) {
                $data['bmi'] = dietetic_calculate_bmi($data['weight'], $patient->height);
            }
        }

        $data['created_at'] = date('Y-m-d H:i:s');

        if ($this->db->insert(db_prefix() . $this->table, $data)) {
            $measurement_id = $this->db->insert_id();
            log_activity('New Measurement Added for Patient [ID: ' . $data['patient_id'] . ']');
            return $measurement_id;
        }

        return false;
    }

    /**
     * Update measurement
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data)
    {
        // Get measurement to check access
        $measurement = $this->get($id);
        if (!$measurement) {
            return false;
        }

        // Check access permissions
        if (!dietetic_can_access_patient($measurement->patient_id)) {
            log_activity('Unauthorized attempt to update measurement [ID: ' . $id . ']');
            return false;
        }

        // Recalculate BMI if weight changed
        if (!empty($data['weight'])) {
            $this->load->model('dietetic/dietetic_patients_model');
            $patient = $this->dietetic_patients_model->get($measurement->patient_id);

            if ($patient && $patient->height) {
                $data['bmi'] = dietetic_calculate_bmi($data['weight'], $patient->height);
            }
        }

        $this->db->where('id', $id);
        if ($this->db->update(db_prefix() . $this->table, $data)) {
            log_activity('Measurement Updated [ID: ' . $id . ']');
            return true;
        }

        return false;
    }

    /**
     * Delete measurement
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        // Get measurement to check access
        $measurement = $this->get($id, false); // Don't check access yet, we'll do it manually
        if (!$measurement) {
            return false;
        }

        // Check access permissions
        if (!dietetic_can_access_patient($measurement->patient_id)) {
            log_activity('Unauthorized attempt to delete measurement [ID: ' . $id . ']');
            return false;
        }

        $this->db->where('id', $id);
        if ($this->db->delete(db_prefix() . $this->table)) {
            log_activity('Measurement Deleted [ID: ' . $id . ']');
            return true;
        }

        return false;
    }

    /**
     * Get latest measurement for patient
     *
     * @param int $patient_id
     * @return object|null
     */
    public function get_latest($patient_id)
    {
        // Check access permissions
        if (!dietetic_can_access_patient($patient_id)) {
            log_activity('Unauthorized attempt to access latest measurement for Patient ID ' . $patient_id);
            return null;
        }

        $this->db->where('patient_id', $patient_id);
        $this->db->order_by('measurement_date', 'DESC');
        $this->db->order_by('id', 'DESC'); // Secondary sort by ID to get most recently added
        $this->db->limit(1);

        return $this->db->get(db_prefix() . $this->table)->row();
    }

    /**
     * Get weight progress (difference from initial to latest)
     *
     * @param int $patient_id
     * @return object
     */
    public function get_weight_progress($patient_id)
    {
        // Check access permissions
        if (!dietetic_can_access_patient($patient_id)) {
            log_activity('Unauthorized attempt to access weight progress for Patient ID ' . $patient_id);
            return (object) [
                'initial_weight' => null,
                'current_weight' => null,
                'weight_change' => null,
                'percentage_change' => null
            ];
        }

        $progress = new stdClass();

        // Get initial measurement
        $this->db->where('patient_id', $patient_id);
        $this->db->order_by('measurement_date', 'ASC');
        $this->db->limit(1);
        $initial = $this->db->get(db_prefix() . $this->table)->row();

        // Get latest measurement
        $latest = $this->get_latest($patient_id);

        $progress->initial_weight = $initial ? $initial->weight : null;
        $progress->current_weight = $latest ? $latest->weight : null;
        $progress->weight_change = null;
        $progress->percentage_change = null;

        if ($initial && $latest && $initial->weight && $latest->weight) {
            $progress->weight_change = $latest->weight - $initial->weight;
            $progress->percentage_change = ($progress->weight_change / $initial->weight) * 100;
        }

        return $progress;
    }
}
