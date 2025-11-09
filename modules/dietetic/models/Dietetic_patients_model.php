<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dietetic_patients_model extends App_Model
{
    private $table = 'dietic_patients';

    public function __construct()
    {
        parent::__construct();

        // Load required Perfex models
        $this->load->model('clients_model');
        $this->load->model('staff_model');

        // Load dietetic models for permissions
        $this->load->helper('dietetic/dietetic');
    }

    /**
     * Get patient by ID
     *
     * @param int $id
     * @param bool $check_access If true, verify user has access to this patient
     * @return object|null
     */
    public function get($id, $check_access = true)
    {
        // Check access permissions if not admin
        if ($check_access && !dietetic_can_access_patient($id)) {
            return null;
        }

        $this->db->where('id', $id);
        $patient = $this->db->get(db_prefix() . $this->table)->row();

        if ($patient) {
            // Get client info
            $patient->client = $this->clients_model->get($patient->client_id);

            // Get dietitian info (primary dietitian)
            $patient->dietitian = $this->staff_model->get($patient->dietitian_id);

            // Get all assigned dietitians (many-to-many)
            if ($this->db->table_exists(db_prefix() . 'dietic_patient_dietitians')) {
                $this->load->model('dietetic/dietetic_patient_dietitians_model');
                $patient->dietitians = $this->dietetic_patient_dietitians_model->get_patient_dietitians($id, 'active');
                $patient->dietitians_count = count($patient->dietitians);
            } else {
                $patient->dietitians = [];
                $patient->dietitians_count = 0;
            }

            // Get latest measurement
            $patient->latest_measurement = $this->get_latest_measurement($id);

            // Get active programs count
            $patient->active_programs = $this->count_active_programs($id);
        }

        return $patient;
    }

    /**
     * Get all patients with filters
     *
     * @param array $where
     * @return array
     */
    public function get_all($where = [])
    {
        $this->db->select('p.*, ' .
            'c.company as client_name, ' .
            'CONCAT(s.firstname, " ", s.lastname) as dietitian_name', false);
        $this->db->from(db_prefix() . $this->table . ' p');
        $this->db->join(db_prefix() . 'clients c', 'c.userid = p.client_id', 'left');
        $this->db->join(db_prefix() . 'staff s', 's.staffid = p.dietitian_id', 'left');

        if (!empty($where)) {
            $this->db->where($where);
        }

        // Apply staff permissions using new many-to-many system
        if ($this->db->table_exists(db_prefix() . 'dietic_patient_dietitians')) {
            // Use new permission system
            dietetic_apply_dietitian_filter($this->db, 'pd');
        } else {
            // Fallback to old system if table doesn't exist yet
            if (!is_admin()) {
                $this->db->where('p.dietitian_id', get_staff_user_id());
            }
        }

        $this->db->group_by('p.id'); // Group by to avoid duplicates from join
        $this->db->order_by('p.created_at', 'DESC');

        return $this->db->get()->result();
    }

    /**
     * Get patient by client ID
     *
     * @param int $client_id
     * @return object|null
     */
    public function get_by_client($client_id)
    {
        $this->db->where('client_id', $client_id);
        $patient = $this->db->get(db_prefix() . $this->table)->row();

        if ($patient) {
            // Don't check access for portal patients (they access their own record)
            return $this->get($patient->id, false);
        }

        return null;
    }

    /**
     * Add new patient
     *
     * @param array $data
     * @return int|bool Patient ID or false
     */
    public function add($data)
    {
        // Check if client already has a patient record
        $existing = $this->get_by_client($data['client_id']);
        if ($existing) {
            return false;
        }

        // Calculate BMI if weight and height provided
        if (!empty($data['initial_weight']) && !empty($data['height'])) {
            $data['bmi'] = dietetic_calculate_bmi($data['initial_weight'], $data['height']);
        }

        $data['created_at'] = date('Y-m-d H:i:s');

        if ($this->db->insert(db_prefix() . $this->table, $data)) {
            $patient_id = $this->db->insert_id();

            // Create dietitian assignment if dietitian_id is provided
            if (!empty($data['dietitian_id']) && $this->db->table_exists(db_prefix() . 'dietic_patient_dietitians')) {
                $this->load->model('dietetic/dietetic_patient_dietitians_model');
                $this->dietetic_patient_dietitians_model->assign([
                    'patient_id' => $patient_id,
                    'dietitian_id' => $data['dietitian_id'],
                    'is_primary' => 1, // First dietitian is primary
                    'notes' => 'Assignation initiale lors de la création du patient'
                ]);
            }

            // Add initial measurement if weight provided
            if (!empty($data['initial_weight'])) {
                $this->load->model('dietetic/dietetic_measurements_model');
                $this->dietetic_measurements_model->add([
                    'patient_id'       => $patient_id,
                    'measurement_date' => date('Y-m-d'),
                    'weight'           => $data['initial_weight'],
                    'bmi'              => isset($data['bmi']) ? $data['bmi'] : null,
                    'notes'            => 'Initial measurement',
                    'added_by'         => get_staff_user_id(),
                    'added_by_type'    => 'staff',
                ]);
            }

            log_activity('New Dietetic Patient Created [ID: ' . $patient_id . ']');
            return $patient_id;
        }

        return false;
    }

    /**
     * Update patient
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data)
    {
        // Check access permissions
        if (!dietetic_can_access_patient($id)) {
            log_activity('Unauthorized attempt to update patient [ID: ' . $id . ']');
            return false;
        }

        $data['updated_at'] = date('Y-m-d H:i:s');

        $this->db->where('id', $id);

        if ($this->db->update(db_prefix() . $this->table, $data)) {
            log_activity('Dietetic Patient Updated [ID: ' . $id . ']');
            return true;
        }

        return false;
    }

    /**
     * Delete patient
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        // Check access permissions
        if (!dietetic_can_access_patient($id)) {
            log_activity('Unauthorized attempt to delete patient [ID: ' . $id . ']');
            return false;
        }

        $this->db->where('id', $id);

        if ($this->db->delete(db_prefix() . $this->table)) {
            log_activity('Dietetic Patient Deleted [ID: ' . $id . ']');
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
    public function get_latest_measurement($patient_id)
    {
        // Check access permissions
        if (!dietetic_can_access_patient($patient_id)) {
            return null;
        }

        $this->db->where('patient_id', $patient_id);
        $this->db->order_by('measurement_date', 'DESC');
        $this->db->order_by('id', 'DESC'); // Secondary sort by ID to get most recently added
        $this->db->limit(1);

        return $this->db->get(db_prefix() . 'dietic_measurements')->row();
    }

    /**
     * Get weight evolution data for charts
     *
     * @param int $patient_id
     * @param int $limit
     * @return array
     */
    public function get_weight_evolution($patient_id, $limit = 12)
    {
        // Check access permissions
        if (!dietetic_can_access_patient($patient_id)) {
            return [];
        }

        $this->db->select('measurement_date, weight, bmi');
        $this->db->where('patient_id', $patient_id);
        $this->db->where('weight IS NOT NULL');
        $this->db->order_by('measurement_date', 'ASC');
        $this->db->limit($limit);

        return $this->db->get(db_prefix() . 'dietic_measurements')->result();
    }

    /**
     * Count active programs for patient
     *
     * @param int $patient_id
     * @return int
     */
    public function count_active_programs($patient_id)
    {
        // Check access permissions
        if (!dietetic_can_access_patient($patient_id)) {
            return 0;
        }

        $this->db->where('patient_id', $patient_id);
        $this->db->where('status', 'active');

        return $this->db->count_all_results(db_prefix() . 'dietic_programs');
    }

    /**
     * Get patients by dietitian
     *
     * @param int $dietitian_id
     * @param string $status
     * @return array
     */
    public function get_by_dietitian($dietitian_id, $status = null)
    {
        $this->db->where('dietitian_id', $dietitian_id);

        if ($status) {
            $this->db->where('status', $status);
        }

        return $this->db->get(db_prefix() . $this->table)->result();
    }

    /**
     * Get statistics
     *
     * @return object
     */
    public function get_statistics()
    {
        $stats = new stdClass();

        // Total patients
        if (!is_admin()) {
            $this->db->where('dietitian_id', get_staff_user_id());
        }
        $stats->total_patients = $this->db->count_all_results(db_prefix() . $this->table);

        // Active patients
        $this->db->where('status', 'active');
        if (!is_admin()) {
            $this->db->where('dietitian_id', get_staff_user_id());
        }
        $stats->active_patients = $this->db->count_all_results(db_prefix() . $this->table);

        // New patients this month
        $this->db->where('MONTH(created_at)', date('m'));
        $this->db->where('YEAR(created_at)', date('Y'));
        if (!is_admin()) {
            $this->db->where('dietitian_id', get_staff_user_id());
        }
        $stats->new_this_month = $this->db->count_all_results(db_prefix() . $this->table);

        return $stats;
    }

    /**
     * Search patients
     *
     * @param string $search
     * @return array
     */
    public function search($search)
    {
        $this->db->select(db_prefix() . $this->table . '.*, ' .
            db_prefix() . 'clients.company as client_name');
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . db_prefix() . $this->table . '.client_id', 'left');

        $this->db->group_start();
        $this->db->like(db_prefix() . 'clients.company', $search);
        $this->db->or_like(db_prefix() . $this->table . '.phone', $search);
        $this->db->or_like(db_prefix() . $this->table . '.email', $search);
        $this->db->group_end();

        if (!is_admin()) {
            $this->db->where(db_prefix() . $this->table . '.dietitian_id', get_staff_user_id());
        }

        return $this->db->get(db_prefix() . $this->table)->result();
    }
}
