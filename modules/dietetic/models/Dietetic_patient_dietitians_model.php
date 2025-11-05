<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dietetic_patient_dietitians_model extends App_Model
{
    private $table = 'dietic_patient_dietitians';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all dietitians for a patient
     *
     * @param int $patient_id
     * @param string $status active, inactive, or null for all
     * @return array
     */
    public function get_patient_dietitians($patient_id, $status = 'active')
    {
        $this->db->select('pd.*, s.firstname, s.lastname, s.email, s.phonenumber, s.profile_image');
        $this->db->from(db_prefix() . $this->table . ' pd');
        $this->db->join(db_prefix() . 'staff s', 's.staffid = pd.dietitian_id');
        $this->db->where('pd.patient_id', $patient_id);

        if ($status !== null) {
            $this->db->where('pd.status', $status);
        }

        $this->db->order_by('pd.is_primary', 'DESC');
        $this->db->order_by('pd.assigned_date', 'DESC');

        return $this->db->get()->result();
    }

    /**
     * Get all patients for a dietitian
     *
     * @param int $dietitian_id
     * @param string $status active, inactive, or null for all
     * @return array
     */
    public function get_dietitian_patients($dietitian_id, $status = 'active')
    {
        $this->db->select('pd.*, p.*, c.company as patient_name, c.phonenumber, c.email as patient_email');
        $this->db->from(db_prefix() . $this->table . ' pd');
        $this->db->join(db_prefix() . 'dietic_patients p', 'p.id = pd.patient_id');
        $this->db->join(db_prefix() . 'clients c', 'c.userid = p.client_id');
        $this->db->where('pd.dietitian_id', $dietitian_id);

        if ($status !== null) {
            $this->db->where('pd.status', $status);
        }

        $this->db->order_by('pd.is_primary', 'DESC');
        $this->db->order_by('pd.assigned_date', 'DESC');

        return $this->db->get()->result();
    }

    /**
     * Check if a dietitian is assigned to a patient
     *
     * @param int $patient_id
     * @param int $dietitian_id
     * @return object|null
     */
    public function get_assignment($patient_id, $dietitian_id)
    {
        return $this->db->where('patient_id', $patient_id)
            ->where('dietitian_id', $dietitian_id)
            ->get(db_prefix() . $this->table)
            ->row();
    }

    /**
     * Check if a dietitian has access to a patient
     *
     * @param int $patient_id
     * @param int $dietitian_id
     * @return bool
     */
    public function has_access($patient_id, $dietitian_id)
    {
        $count = $this->db->where('patient_id', $patient_id)
            ->where('dietitian_id', $dietitian_id)
            ->where('status', 'active')
            ->count_all_results(db_prefix() . $this->table);

        return $count > 0;
    }

    /**
     * Assign a dietitian to a patient
     *
     * @param array $data
     * @return int|bool Assignment ID or false
     */
    public function assign($data)
    {
        // Check if assignment already exists
        $existing = $this->get_assignment($data['patient_id'], $data['dietitian_id']);

        if ($existing) {
            // Update existing assignment
            $update_data = [
                'status' => isset($data['status']) ? $data['status'] : 'active',
                'is_primary' => isset($data['is_primary']) ? $data['is_primary'] : 0,
                'notes' => isset($data['notes']) ? $data['notes'] : $existing->notes,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $this->db->where('id', $existing->id);
            $this->db->update(db_prefix() . $this->table, $update_data);

            return $existing->id;
        }

        // Create new assignment
        $insert_data = [
            'patient_id' => $data['patient_id'],
            'dietitian_id' => $data['dietitian_id'],
            'is_primary' => isset($data['is_primary']) ? $data['is_primary'] : 0,
            'assigned_date' => date('Y-m-d H:i:s'),
            'assigned_by' => isset($data['assigned_by']) ? $data['assigned_by'] : get_staff_user_id(),
            'notes' => isset($data['notes']) ? $data['notes'] : null,
            'status' => isset($data['status']) ? $data['status'] : 'active',
            'created_at' => date('Y-m-d H:i:s')
        ];

        // If this is primary, remove primary flag from other dietitians
        if ($insert_data['is_primary']) {
            $this->db->where('patient_id', $data['patient_id']);
            $this->db->update(db_prefix() . $this->table, ['is_primary' => 0]);
        }

        $this->db->insert(db_prefix() . $this->table, $insert_data);
        return $this->db->insert_id();
    }

    /**
     * Remove/deactivate a dietitian from a patient
     *
     * @param int $patient_id
     * @param int $dietitian_id
     * @param bool $permanent If true, delete; if false, just deactivate
     * @return bool
     */
    public function remove($patient_id, $dietitian_id, $permanent = false)
    {
        $this->db->where('patient_id', $patient_id);
        $this->db->where('dietitian_id', $dietitian_id);

        if ($permanent) {
            return $this->db->delete(db_prefix() . $this->table);
        } else {
            return $this->db->update(db_prefix() . $this->table, [
                'status' => 'inactive',
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
    }

    /**
     * Set a dietitian as primary for a patient
     *
     * @param int $patient_id
     * @param int $dietitian_id
     * @return bool
     */
    public function set_primary($patient_id, $dietitian_id)
    {
        // Remove primary flag from all dietitians for this patient
        $this->db->where('patient_id', $patient_id);
        $this->db->update(db_prefix() . $this->table, ['is_primary' => 0]);

        // Set this dietitian as primary
        $this->db->where('patient_id', $patient_id);
        $this->db->where('dietitian_id', $dietitian_id);
        return $this->db->update(db_prefix() . $this->table, [
            'is_primary' => 1,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Get primary dietitian for a patient
     *
     * @param int $patient_id
     * @return object|null
     */
    public function get_primary_dietitian($patient_id)
    {
        $this->db->select('pd.*, s.firstname, s.lastname, s.email, s.phonenumber');
        $this->db->from(db_prefix() . $this->table . ' pd');
        $this->db->join(db_prefix() . 'staff s', 's.staffid = pd.dietitian_id');
        $this->db->where('pd.patient_id', $patient_id);
        $this->db->where('pd.is_primary', 1);
        $this->db->where('pd.status', 'active');

        return $this->db->get()->row();
    }

    /**
     * Get count of patients for a dietitian
     *
     * @param int $dietitian_id
     * @param string $status
     * @return int
     */
    public function count_patients($dietitian_id, $status = 'active')
    {
        $this->db->where('dietitian_id', $dietitian_id);

        if ($status !== null) {
            $this->db->where('status', $status);
        }

        return $this->db->count_all_results(db_prefix() . $this->table);
    }

    /**
     * Get count of dietitians for a patient
     *
     * @param int $patient_id
     * @param string $status
     * @return int
     */
    public function count_dietitians($patient_id, $status = 'active')
    {
        $this->db->where('patient_id', $patient_id);

        if ($status !== null) {
            $this->db->where('status', $status);
        }

        return $this->db->count_all_results(db_prefix() . $this->table);
    }
}
