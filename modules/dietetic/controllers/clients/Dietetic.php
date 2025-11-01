<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dietetic extends ClientsController
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('dietetic/dietetic_patients_model');
        $this->load->model('dietetic/dietetic_measurements_model');
        $this->load->model('dietetic/dietetic_programs_model');
        $this->load->model('dietetic/dietetic_meal_plans_model');
        $this->load->model('dietetic/dietetic_consultations_model');
        $this->load->helper('dietetic/dietetic');
    }

    /**
     * Portal dashboard
     */
    public function index()
    {
        if (!is_client_logged_in()) {
            redirect(site_url('authentication/login'));
        }

        // Get patient record for current client
        $patient = $this->dietetic_patients_model->get_by_client(get_client_user_id());

        if (!$patient) {
            $data['no_patient'] = true;
            $data['title'] = 'Dietetic Program';
            $this->data($data);
            $this->view('no_access');
            $this->layout();
            return;
        }

        $data['patient'] = $patient;
        $data['title'] = 'My Dietetic Program';

        // Get active program safely
        try {
            $data['active_program'] = $this->dietetic_programs_model->get_active_program($patient->id);
        } catch (Exception $e) {
            $data['active_program'] = null;
        }

        // Get latest measurement safely
        try {
            $data['latest_measurement'] = $this->dietetic_measurements_model->get_latest($patient->id);
        } catch (Exception $e) {
            $data['latest_measurement'] = null;
        }

        // Get weight progress safely
        try {
            $data['weight_progress'] = $this->dietetic_measurements_model->get_weight_progress($patient->id);
        } catch (Exception $e) {
            $progress = new stdClass();
            $progress->initial_weight = null;
            $progress->current_weight = null;
            $progress->weight_change = null;
            $progress->percentage_change = null;
            $data['weight_progress'] = $progress;
        }

        // Get upcoming consultations safely
        try {
            $data['upcoming_consultations'] = $this->dietetic_consultations_model->get_by_patient($patient->id, 3);
        } catch (Exception $e) {
            $data['upcoming_consultations'] = [];
        }

        $this->data($data);
        $this->view('dashboard');
        $this->layout();
    }
}
