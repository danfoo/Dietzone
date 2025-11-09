<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dietetic_programs_model extends App_Model
{
    private $table = 'dietic_programs';

    public function __construct()
    {
        parent::__construct();

        // Load required Perfex models
        $this->load->model('clients_model');
        $this->load->model('staff_model');

        // Load dietetic helper for permissions
        $this->load->helper('dietetic/dietetic');
    }

    /**
     * Get program by ID
     *
     * @param int $id
     * @param bool $check_access If true, verify user has access to this program
     * @return object|null
     */
    public function get($id, $check_access = true)
    {
        $this->db->select('prog.*, ' .
            'c.company as client_name, ' .
            'CONCAT(s.firstname, " ", s.lastname) as dietitian_name');
        $this->db->from(db_prefix() . $this->table . ' prog');
        $this->db->join(db_prefix() . 'dietic_patients p', 'p.id = prog.patient_id', 'left');
        $this->db->join(db_prefix() . 'clients c', 'c.userid = p.client_id', 'left');
        $this->db->join(db_prefix() . 'staff s', 's.staffid = prog.dietitian_id', 'left');
        $this->db->where('prog.id', $id);

        $program = $this->db->get()->row();

        if ($program) {
            // Check access permissions if not admin
            if ($check_access && !dietetic_can_access_patient($program->patient_id)) {
                return null;
            }

            // Get meal plans count
            $this->db->where('program_id', $id);
            $program->meal_plans_count = $this->db->count_all_results(db_prefix() . 'dietic_meal_plans');
        }

        return $program;
    }

    /**
     * Get all programs
     *
     * @param array $where
     * @return array
     */
    public function get_all($where = [])
    {
        $this->db->select('prog.*, ' .
            'c.company as client_name, ' .
            'CONCAT(s.firstname, " ", s.lastname) as dietitian_name', false);
        $this->db->from(db_prefix() . $this->table . ' prog');
        $this->db->join(db_prefix() . 'dietic_patients p', 'p.id = prog.patient_id', 'left');
        $this->db->join(db_prefix() . 'clients c', 'c.userid = p.client_id', 'left');
        $this->db->join(db_prefix() . 'staff s', 's.staffid = prog.dietitian_id', 'left');

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
                $this->db->where('prog.dietitian_id', get_staff_user_id());
            }
        }

        $this->db->group_by('prog.id'); // Group by to avoid duplicates from join
        $this->db->order_by('prog.created_at', 'DESC');

        return $this->db->get()->result();
    }

    /**
     * Get programs by patient
     *
     * @param int $patient_id
     * @return array
     */
    public function get_by_patient($patient_id)
    {
        // Check access permissions
        if (!dietetic_can_access_patient($patient_id)) {
            log_activity('Unauthorized attempt to access programs for Patient ID ' . $patient_id);
            return [];
        }

        $this->db->where('patient_id', $patient_id);
        $this->db->order_by('created_at', 'DESC');

        return $this->db->get(db_prefix() . $this->table)->result();
    }

    /**
     * Get active program for patient
     *
     * @param int $patient_id
     * @return object|null
     */
    public function get_active_program($patient_id)
    {
        // Check access permissions
        if (!dietetic_can_access_patient($patient_id)) {
            log_activity('Unauthorized attempt to access active program for Patient ID ' . $patient_id);
            return null;
        }

        $this->db->where('patient_id', $patient_id);
        $this->db->where('status', 'active');
        $this->db->order_by('start_date', 'DESC');
        $this->db->limit(1);

        return $this->db->get(db_prefix() . $this->table)->row();
    }

    /**
     * Add new program
     *
     * @param array $data
     * @return int|bool
     */
    public function add($data)
    {
        // Check access permissions to patient
        if (isset($data['patient_id']) && !dietetic_can_access_patient($data['patient_id'])) {
            log_activity('Unauthorized attempt to create program for Patient ID ' . $data['patient_id']);
            return false;
        }

        $data['created_at'] = date('Y-m-d H:i:s');

        if ($this->db->insert(db_prefix() . $this->table, $data)) {
            $program_id = $this->db->insert_id();
            log_activity('New Dietetic Program Created [ID: ' . $program_id . ']');
            return $program_id;
        }

        return false;
    }

    /**
     * Update program
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data)
    {
        // Get program to check access
        $program = $this->get($id);
        if (!$program) {
            return false;
        }

        // Check access permissions
        if (!dietetic_can_access_patient($program->patient_id)) {
            log_activity('Unauthorized attempt to update program [ID: ' . $id . ']');
            return false;
        }

        $data['updated_at'] = date('Y-m-d H:i:s');

        $this->db->where('id', $id);

        if ($this->db->update(db_prefix() . $this->table, $data)) {
            log_activity('Dietetic Program Updated [ID: ' . $id . ']');
            return true;
        }

        return false;
    }

    /**
     * Delete program
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        // Get program to check access
        $program = $this->get($id, false); // Don't check access yet, we'll do it manually
        if (!$program) {
            return false;
        }

        // Check access permissions
        if (!dietetic_can_access_patient($program->patient_id)) {
            log_activity('Unauthorized attempt to delete program [ID: ' . $id . ']');
            return false;
        }

        $this->db->where('id', $id);
        if ($this->db->delete(db_prefix() . $this->table)) {
            log_activity('Dietetic Program Deleted [ID: ' . $id . ']');
            return true;
        }

        return false;
    }

    /**
     * Get meal plans for program
     *
     * @param int $program_id
     * @return array
     */
    public function get_meal_plans($program_id)
    {
        // Get program to check access
        $program = $this->get($program_id);
        if (!$program) {
            return [];
        }

        // Check access permissions (already done by get() method)

        $this->db->where('program_id', $program_id);
        $this->db->order_by('week_number', 'ASC');

        return $this->db->get(db_prefix() . 'dietic_meal_plans')->result();
    }

    /**
     * Get statistics
     *
     * @return object
     */
    public function get_statistics()
    {
        $stats = new stdClass();

        // Active programs
        $this->db->where('status', 'active');
        if (!is_admin()) {
            $this->db->where('dietitian_id', get_staff_user_id());
        }
        $stats->active_programs = $this->db->count_all_results(db_prefix() . $this->table);

        // Completed programs
        $this->db->where('status', 'completed');
        if (!is_admin()) {
            $this->db->where('dietitian_id', get_staff_user_id());
        }
        $stats->completed_programs = $this->db->count_all_results(db_prefix() . $this->table);

        // Programs ending soon (within 7 days)
        $this->db->where('status', 'active');
        $this->db->where('end_date IS NOT NULL');
        $this->db->where('end_date <=', date('Y-m-d', strtotime('+7 days')));
        $this->db->where('end_date >=', date('Y-m-d'));
        if (!is_admin()) {
            $this->db->where('dietitian_id', get_staff_user_id());
        }
        $stats->ending_soon = $this->db->count_all_results(db_prefix() . $this->table);

        return $stats;
    }
}
