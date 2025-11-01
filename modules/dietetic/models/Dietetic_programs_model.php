<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dietetic_programs_model extends App_Model
{
    private $table = 'dietic_programs';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get program by ID
     *
     * @param int $id
     * @return object|null
     */
    public function get($id)
    {
        $this->db->select(db_prefix() . $this->table . '.*, ' .
            db_prefix() . 'clients.company as client_name, ' .
            'CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as dietitian_name');
        $this->db->join(db_prefix() . 'dietic_patients', db_prefix() . 'dietic_patients.id = ' . db_prefix() . $this->table . '.patient_id', 'left');
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . db_prefix() . 'dietic_patients.client_id', 'left');
        $this->db->join('tblstaff', 'tblstaff.staffid = ' . db_prefix() . $this->table . '.dietitian_id', 'left');
        $this->db->where(db_prefix() . $this->table . '.id', $id);

        $program = $this->db->get(db_prefix() . $this->table)->row();

        if ($program) {
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
        $this->db->select(db_prefix() . $this->table . '.*, ' .
            db_prefix() . 'clients.company as client_name, ' .
            'CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as dietitian_name');
        $this->db->join(db_prefix() . 'dietic_patients', db_prefix() . 'dietic_patients.id = ' . db_prefix() . $this->table . '.patient_id', 'left');
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . db_prefix() . 'dietic_patients.client_id', 'left');
        $this->db->join('tblstaff', 'tblstaff.staffid = ' . db_prefix() . $this->table . '.dietitian_id', 'left');

        if (!empty($where)) {
            $this->db->where($where);
        }

        if (!is_admin()) {
            $this->db->where(db_prefix() . $this->table . '.dietitian_id', get_staff_user_id());
        }

        $this->db->order_by(db_prefix() . $this->table . '.created_at', 'DESC');

        return $this->db->get(db_prefix() . $this->table)->result();
    }

    /**
     * Get programs by patient
     *
     * @param int $patient_id
     * @return array
     */
    public function get_by_patient($patient_id)
    {
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
        $this->db->where('id', $id);
        return $this->db->delete(db_prefix() . $this->table);
    }

    /**
     * Get meal plans for program
     *
     * @param int $program_id
     * @return array
     */
    public function get_meal_plans($program_id)
    {
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
