<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Portal extends App_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('dietetic/dietetic_patients_model');
        $this->load->model('dietetic/dietetic_measurements_model');
        $this->load->model('dietetic/dietetic_programs_model');
        $this->load->model('dietetic/dietetic_consultations_model');
    }

    public function index()
    {
        // Check if client is logged in
        if (!is_client_logged_in()) {
            redirect(site_url('authentication/login'));
        }

        $client_id = get_client_user_id();

        // Get patient
        try {
            $patient = $this->dietetic_patients_model->get_by_client($client_id);
        } catch (Exception $e) {
            $patient = null;
        }

        if (!$patient) {
            $this->load->view('portal_no_access');
            return;
        }

        $data = [];
        $data['patient'] = $patient;
        $data['title'] = 'My Dietetic Program';

        // Get active program
        try {
            $data['active_program'] = $this->dietetic_programs_model->get_active_program($patient->id);
        } catch (Exception $e) {
            $data['active_program'] = null;
        }

        // Get latest measurement
        try {
            $data['latest_measurement'] = $this->dietetic_measurements_model->get_latest($patient->id);
        } catch (Exception $e) {
            $data['latest_measurement'] = null;
        }

        // Get weight progress
        try {
            $data['weight_progress'] = $this->dietetic_measurements_model->get_weight_progress($patient->id);
        } catch (Exception $e) {
            $progress = new stdClass();
            $progress->weight_change = null;
            $data['weight_progress'] = $progress;
        }

        // Get upcoming consultations
        try {
            $data['upcoming_consultations'] = $this->dietetic_consultations_model->get_by_patient($patient->id, 3);
        } catch (Exception $e) {
            $data['upcoming_consultations'] = [];
        }

        $this->load->view('portal_dashboard', $data);
    }

    /**
     * Add measurement from portal
     */
    public function add_measurement()
    {
        if (!is_client_logged_in()) {
            echo json_encode(['success' => false, 'message' => 'Not authorized']);
            return;
        }

        $client_id = get_client_user_id();

        // Get patient
        try {
            $patient = $this->dietetic_patients_model->get_by_client($client_id);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Patient not found']);
            return;
        }

        if (!$patient) {
            echo json_encode(['success' => false, 'message' => 'No patient record found']);
            return;
        }

        if ($this->input->post()) {
            $measurement_data = [
                'patient_id' => $patient->id,
                'measurement_date' => $this->input->post('measurement_date'),
                'weight' => $this->input->post('weight'),
                'body_fat' => $this->input->post('body_fat'),
                'waist' => $this->input->post('waist'),
                'hips' => $this->input->post('hips'),
                'notes' => $this->input->post('notes'),
                'added_by' => $client_id,
                'added_by_type' => 'client'
            ];

            try {
                $measurement_id = $this->dietetic_measurements_model->add($measurement_data);

                if ($measurement_id) {
                    echo json_encode(['success' => true, 'message' => 'Measurement added successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to save measurement']);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
        }
    }
}
