<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Portal extends ClientsController
{
    public function __construct()
    {
        parent::__construct();

        // Load Perfex models
        $this->load->model('clients_model');
        $this->load->model('staff_model');

        // Load dietetic models
        $this->load->model('dietetic/dietetic_patients_model');
        $this->load->model('dietetic/dietetic_measurements_model');
        $this->load->model('dietetic/dietetic_programs_model');
        $this->load->model('dietetic/dietetic_meal_plans_model');
        $this->load->model('dietetic/dietetic_consultations_model');
        $this->load->helper('dietetic/dietetic');

        // Check if client is logged in
        if (!is_client_logged_in()) {
            redirect(site_url('authentication/login'));
        }
    }

    /**
     * Portal dashboard
     */
    public function index()
    {
        // Get patient record for current client
        $patient = $this->dietetic_patients_model->get_by_client(get_client_user_id());

        if (!$patient) {
            $data['no_patient'] = true;
            $this->data($data);
            $this->view('portal/no_access');
            $this->layout();
            return;
        }

        $data['patient'] = $patient;
        $data['title'] = _l('dietetic_my_program');

        // Get active program
        $data['active_program'] = $this->dietetic_programs_model->get_active_program($patient->id);

        // Get latest measurement
        $data['latest_measurement'] = $this->dietetic_measurements_model->get_latest($patient->id);

        // Get weight progress
        $data['weight_progress'] = $this->dietetic_measurements_model->get_weight_progress($patient->id);

        // Get weight evolution for chart
        $data['weight_evolution'] = $this->dietetic_patients_model->get_weight_evolution($patient->id, 12);

        // Get upcoming consultations
        $data['upcoming_consultations'] = $this->dietetic_consultations_model->get_by_patient($patient->id, 3);

        // Get all programs
        $data['programs'] = $this->dietetic_programs_model->get_by_patient($patient->id);

        $this->data($data);
        $this->view('portal/dashboard');
        $this->layout();
    }

    /**
     * View meal plan
     *
     * @param int $id
     */
    public function meal_plan($id)
    {
        $meal_plan = $this->dietetic_meal_plans_model->get($id);

        if (!$meal_plan) {
            show_404();
        }

        // Verify this meal plan belongs to the client
        $program = $this->dietetic_programs_model->get($meal_plan->program_id);
        $patient = $this->dietetic_patients_model->get($program->patient_id);

        if ($patient->client_id != get_client_user_id()) {
            show_404();
        }

        $data['meal_plan'] = $meal_plan;
        $data['program'] = $program;
        $data['patient'] = $patient;
        $data['meals_by_day'] = $this->dietetic_meal_plans_model->get_meals_by_day($id);
        $data['nutrition_totals'] = $this->dietetic_meal_plans_model->calculate_plan_nutrition($id);
        $data['title'] = $meal_plan->plan_name;

        $this->data($data);
        $this->view('portal/meal_plan');
        $this->layout();
    }

    /**
     * My measurements
     */
    public function measurements()
    {
        $patient = $this->dietetic_patients_model->get_by_client(get_client_user_id());

        if (!$patient) {
            show_404();
        }

        $data['patient'] = $patient;
        $data['title'] = _l('dietetic_my_measurements');
        $data['measurements'] = $this->dietetic_measurements_model->get_by_patient($patient->id);
        $data['weight_evolution'] = $this->dietetic_patients_model->get_weight_evolution($patient->id);

        $this->data($data);
        $this->view('portal/measurements');
        $this->layout();
    }

    /**
     * Add measurement from portal
     */
    public function add_measurement()
    {
        if (!dietetic_get_option('enable_client_measurements', true)) {
            echo json_encode(['success' => false, 'message' => _l('dietetic_client_measurements_disabled')]);
            return;
        }

        $patient = $this->dietetic_patients_model->get_by_client(get_client_user_id());

        if (!$patient) {
            echo json_encode(['success' => false, 'message' => _l('dietetic_error_no_patient')]);
            return;
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            $data['patient_id'] = $patient->id;
            $data['added_by'] = get_client_user_id();
            $data['added_by_type'] = 'client';

            $measurement_id = $this->dietetic_measurements_model->add($data);

            if ($measurement_id) {
                echo json_encode(['success' => true, 'message' => _l('added_successfully')]);
            } else {
                echo json_encode(['success' => false, 'message' => _l('dietetic_error_add_failed')]);
            }
        }
    }

    /**
     * My consultations
     */
    public function consultations()
    {
        $patient = $this->dietetic_patients_model->get_by_client(get_client_user_id());

        if (!$patient) {
            show_404();
        }

        $data['patient'] = $patient;
        $data['title'] = _l('dietetic_my_consultations');
        $data['consultations'] = $this->dietetic_consultations_model->get_by_patient($patient->id);

        $this->data($data);
        $this->view('portal/consultations');
        $this->layout();
    }

    /**
     * Download PDF meal plan
     *
     * @param int $id
     */
    public function download_meal_plan($id)
    {
        $meal_plan = $this->dietetic_meal_plans_model->get($id);

        if (!$meal_plan) {
            show_404();
        }

        // Verify this meal plan belongs to the client
        $program = $this->dietetic_programs_model->get($meal_plan->program_id);
        $patient = $this->dietetic_patients_model->get($program->patient_id);

        if ($patient->client_id != get_client_user_id()) {
            show_404();
        }

        $this->load->library('dietetic/dietetic_pdf');

        $pdf_data = [
            'meal_plan'    => $meal_plan,
            'program'      => $program,
            'patient'      => $patient,
            'meals_by_day' => $this->dietetic_meal_plans_model->get_meals_by_day($id),
        ];

        $this->dietetic_pdf->generate_meal_plan_pdf($pdf_data);
    }
}
