<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dietetic_patient_documents_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all documents for a patient
     *
     * @param int $patient_id
     * @return array
     */
    public function get_by_patient($patient_id)
    {
        $this->db->where('patient_id', $patient_id);
        $this->db->order_by('uploaded_at', 'DESC');
        return $this->db->get(db_prefix() . 'dietic_patient_documents')->result();
    }

    /**
     * Get a specific document
     *
     * @param int $id
     * @return object|null
     */
    public function get($id)
    {
        return $this->db->get_where(db_prefix() . 'dietic_patient_documents', ['id' => $id])->row();
    }

    /**
     * Add a new document
     *
     * @param array $data
     * @return int
     */
    public function add($data)
    {
        $data['uploaded_at'] = date('Y-m-d H:i:s');
        $this->db->insert(db_prefix() . 'dietic_patient_documents', $data);
        return $this->db->insert_id();
    }

    /**
     * Delete a document
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete(db_prefix() . 'dietic_patient_documents');
    }

    /**
     * Check if table exists
     *
     * @return bool
     */
    public function table_exists()
    {
        return $this->db->table_exists(db_prefix() . 'dietic_patient_documents');
    }

    /**
     * Create table for patient documents
     *
     * @return bool
     */
    public function create_table()
    {
        if ($this->table_exists()) {
            return true;
        }

        $sql = "CREATE TABLE `" . db_prefix() . "dietic_patient_documents` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `patient_id` int(11) NOT NULL,
            `filename` varchar(255) NOT NULL,
            `original_filename` varchar(255) NOT NULL,
            `file_type` varchar(50) DEFAULT NULL,
            `file_size` int(11) DEFAULT NULL,
            `uploaded_at` datetime NOT NULL,
            PRIMARY KEY (`id`),
            KEY `patient_id` (`patient_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        return $this->db->query($sql);
    }
}
