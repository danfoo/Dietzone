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

        // Get weight evolution for chart
        try {
            $data['weight_evolution'] = $this->dietetic_patients_model->get_weight_evolution($patient->id);
        } catch (Exception $e) {
            $data['weight_evolution'] = [];
        }

        $this->load->view('portal_dashboard', $data);
    }

    /**
     * View all measurements
     */
    public function measurements()
    {
        if (!is_client_logged_in()) {
            redirect(site_url('authentication/login'));
            return;
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
        $data['title'] = 'Mes Mesures';

        // Get all measurements
        try {
            $data['measurements'] = $this->dietetic_measurements_model->get_by_patient($patient->id);
        } catch (Exception $e) {
            $data['measurements'] = [];
        }

        // Get weight evolution for chart
        try {
            $data['weight_evolution'] = $this->dietetic_patients_model->get_weight_evolution($patient->id);
        } catch (Exception $e) {
            $data['weight_evolution'] = [];
        }

        $this->load->view('portal/measurements', $data);
    }

    /**
     * Add measurement from portal - supports both AJAX and regular form submission
     */
    public function add_measurement()
    {
        if (!is_client_logged_in()) {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['success' => false, 'message' => 'Not logged in']);
                return;
            }
            redirect(site_url('authentication/login'));
            return;
        }

        $client_id = get_client_user_id();

        // Get patient
        try {
            $patient = $this->dietetic_patients_model->get_by_client($client_id);
        } catch (Exception $e) {
            $patient = null;
        }

        if (!$patient) {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['success' => false, 'message' => 'Patient not found']);
                return;
            }
            $this->load->view('portal_no_access');
            return;
        }

        // Handle form submission (both AJAX and regular)
        if ($this->input->post()) {
            $weight = $this->input->post('weight');
            $body_fat_input = $this->input->post('body_fat');
            $muscle_mass_input = $this->input->post('muscle_mass');

            // Calculate BMI if height is available
            $bmi = null;
            if ($patient->height && $weight) {
                $height_m = $patient->height / 100; // Convert cm to meters
                $bmi = $weight / ($height_m * $height_m);
            }

            // Auto-calculate body fat percentage if not provided
            $body_fat = $body_fat_input;
            if (empty($body_fat) && $bmi && $patient->birth_date) {
                // Calculate age
                $birth_date = new DateTime($patient->birth_date);
                $today = new DateTime();
                $age = $today->diff($birth_date)->y;

                // Gender: 1 for male, 0 for female
                $gender = ($patient->gender === 'male') ? 1 : 0;

                // Body Fat % = (1.20 × BMI) + (0.23 × Age) − (10.8 × Gender) − 5.4
                $body_fat = (1.20 * $bmi) + (0.23 * $age) - (10.8 * $gender) - 5.4;
                $body_fat = max(0, min(100, $body_fat)); // Clamp between 0-100
            }

            // Auto-calculate muscle mass if not provided
            $muscle_mass = $muscle_mass_input;
            if (empty($muscle_mass) && $weight && $body_fat) {
                // Muscle Mass = Weight − (Weight × Body Fat % / 100)
                $body_fat_kg = $weight * ($body_fat / 100);
                $muscle_mass = (($weight - $body_fat_kg) / $weight) * 100;
                $muscle_mass = max(0, min(100, $muscle_mass)); // Clamp between 0-100
            }

            $measurement_data = [
                'patient_id' => $patient->id,
                'measurement_date' => $this->input->post('measurement_date'),
                'weight' => $weight,
                'bmi' => $bmi,
                'body_fat' => $body_fat,
                'muscle_mass' => $muscle_mass,
                'waist' => $this->input->post('waist'),
                'hips' => $this->input->post('hips'),
                'chest' => $this->input->post('chest'),
                'arms' => $this->input->post('arms'),
                'thighs' => $this->input->post('thighs'),
                'notes' => $this->input->post('notes'),
                'added_by' => $client_id,
                'added_by_type' => 'client'
            ];

            try {
                $measurement_id = $this->dietetic_measurements_model->add($measurement_data);

                if ($measurement_id) {
                    // Send notification to dietitian
                    if ($patient->dietitian_id) {
                        // Get client info for patient name
                        $this->load->model('clients_model');
                        $client = $this->clients_model->get($patient->client_id);
                        $patient_name = $client ? $client->company : 'Patient';

                        // Notify dietitian about new measurement
                        dietetic_notify_measurement_added(
                            $patient->id,
                            $patient->dietitian_id,
                            $patient_name,
                            $weight
                        );
                    }

                    // Return JSON for AJAX requests
                    if ($this->input->is_ajax_request()) {
                        echo json_encode(['success' => true, 'message' => 'Measurement added successfully!']);
                        return;
                    }

                    // For regular form submission, set success message
                    $data['success'] = 'Mesure ajoutée avec succès!';
                    $data['patient'] = $patient;
                    $this->load->view('portal_add_measurement', $data);
                    return;
                } else {
                    // Return JSON for AJAX requests
                    if ($this->input->is_ajax_request()) {
                        echo json_encode(['success' => false, 'message' => 'Failed to save measurement']);
                        return;
                    }

                    $data['error'] = 'Échec de l\'enregistrement de la mesure.';
                }
            } catch (Exception $e) {
                // Return JSON for AJAX requests
                if ($this->input->is_ajax_request()) {
                    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
                    return;
                }

                $data['error'] = 'Erreur: ' . $e->getMessage();
            }
        }

        // Display form for GET requests
        $data = [];
        $data['patient'] = $patient;
        $this->load->view('portal_add_measurement', $data);
    }

    /**
     * View meal plans
     */
    public function meal_plans()
    {
        if (!is_client_logged_in()) {
            redirect(site_url('authentication/login'));
            return;
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
        $data['title'] = 'Mes Plans Alimentaires';

        // Load meal plans model
        $this->load->model('dietetic/dietetic_meal_plans_model');

        // Get active program
        try {
            $active_program = $this->dietetic_programs_model->get_active_program($patient->id);
            $data['active_program'] = $active_program;

            if ($active_program) {
                // Get meal plans for this program
                $data['meal_plans'] = $this->dietetic_meal_plans_model->get_by_program($active_program->id);
            } else {
                $data['meal_plans'] = [];
            }
        } catch (Exception $e) {
            $data['active_program'] = null;
            $data['meal_plans'] = [];
        }

        $this->load->view('portal_meal_plans', $data);
    }

    /**
     * View meal plan details
     */
    public function view_meal_plan($meal_plan_id)
    {
        if (!is_client_logged_in()) {
            redirect(site_url('authentication/login'));
            return;
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

        // Load meal plans model
        $this->load->model('dietetic/dietetic_meal_plans_model');

        // Get meal plan
        $meal_plan = $this->dietetic_meal_plans_model->get($meal_plan_id);

        if (!$meal_plan) {
            show_404();
            return;
        }

        // Get program
        $program = $this->dietetic_programs_model->get($meal_plan->program_id);

        // Verify this meal plan belongs to the patient's program
        if ($program->patient_id != $patient->id) {
            show_404();
            return;
        }

        $data = [];
        $data['patient'] = $patient;
        $data['meal_plan'] = $meal_plan;
        $data['program'] = $program;
        $data['title'] = $meal_plan->plan_name;

        // Get meals grouped by day
        $data['meals_by_day'] = $this->dietetic_meal_plans_model->get_meals_by_day($meal_plan_id);

        // Calculate nutrition
        $data['nutrition_totals'] = $this->dietetic_meal_plans_model->calculate_plan_nutrition($meal_plan_id);

        $this->load->view('portal_meal_plan_view', $data);
    }

    /**
     * View consultations
     */
    public function consultations()
    {
        if (!is_client_logged_in()) {
            redirect(site_url('authentication/login'));
            return;
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
        $data['title'] = 'Mes Consultations';

        // Get all consultations for this patient
        try {
            $data['consultations'] = $this->dietetic_consultations_model->get_by_patient($patient->id);
        } catch (Exception $e) {
            $data['consultations'] = [];
        }

        $this->load->view('portal_consultations', $data);
    }
}
