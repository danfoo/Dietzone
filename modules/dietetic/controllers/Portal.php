<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Portal extends App_Controller
{
    private $ratings_model_loaded = false;

    public function __construct()
    {
        parent::__construct();

        // Load helper functions
        $this->load->helper('dietetic/dietetic');

        // Load models
        $this->load->model('dietetic/dietetic_patients_model');
        $this->load->model('dietetic/dietetic_measurements_model');
        $this->load->model('dietetic/dietetic_programs_model');
        $this->load->model('dietetic/dietetic_consultations_model');
    }

    /**
     * Lazy load ratings model - only load when needed and check if table exists
     *
     * @return bool True if model loaded successfully
     */
    private function load_ratings_model()
    {
        if ($this->ratings_model_loaded) {
            return true;
        }

        try {
            // Check if table exists first
            $table_name = db_prefix() . 'dietic_ratings';
            if (!$this->db->table_exists($table_name)) {
                return false;
            }

            $this->load->model('dietetic/dietetic_ratings_model');
            $this->ratings_model_loaded = true;
            return true;
        } catch (Exception $e) {
            return false;
        }
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

            // Helper function to convert empty strings to NULL for numeric fields
            $numeric_or_null = function($value) {
                return ($value === '' || $value === null) ? null : $value;
            };

            $measurement_data = [
                'patient_id' => $patient->id,
                'measurement_date' => $this->input->post('measurement_date'),
                'weight' => $weight,
                'bmi' => $bmi,
                'body_fat' => $body_fat,
                'muscle_mass' => $muscle_mass,
                'waist' => $numeric_or_null($this->input->post('waist')),
                'hips' => $numeric_or_null($this->input->post('hips')),
                'chest' => $numeric_or_null($this->input->post('chest')),
                'arms' => $numeric_or_null($this->input->post('arms')),
                'thighs' => $numeric_or_null($this->input->post('thighs')),
                'notes' => $this->input->post('notes'),
                'added_by' => $client_id,
                'added_by_type' => 'client'
            ];

            try {
                $measurement_id = $this->dietetic_measurements_model->add($measurement_data);

                if ($measurement_id) {
                    // Send notification to dietitian (wrapped in try-catch to not block measurement creation)
                    if ($patient->dietitian_id) {
                        try {
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
                        } catch (Exception $e) {
                            // Log error but don't fail the measurement creation
                            log_activity('Dietetic notification error: ' . $e->getMessage());
                        }
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
                    $data['patient'] = $patient;
                }
            } catch (Exception $e) {
                // Return JSON for AJAX requests
                if ($this->input->is_ajax_request()) {
                    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
                    return;
                }

                $data['error'] = 'Erreur: ' . $e->getMessage();
                $data['patient'] = $patient;
            }
        }

        // Display form for GET requests or after errors
        if (!isset($data)) {
            $data = [];
        }
        if (!isset($data['patient'])) {
            $data['patient'] = $patient;
        }
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
     * View meal plans - redirects to list if no ID provided
     * This method serves as both index and detail view
     */
    public function view_meal_plan($meal_plan_id = null)
    {
        // Debug logging
        log_activity('[DIETETIC DEBUG] view_meal_plan called with ID: ' . var_export($meal_plan_id, true));

        if (!is_client_logged_in()) {
            log_activity('[DIETETIC DEBUG] User not logged in, redirecting to login');
            redirect(site_url('authentication/login'));
            return;
        }

        // If no meal plan ID provided, redirect to meal plans list
        // This handles both /view_meal_plan and /view_meal_plan/
        if (empty($meal_plan_id) || !is_numeric($meal_plan_id)) {
            log_activity('[DIETETIC DEBUG] Invalid or missing meal plan ID (' . var_export($meal_plan_id, true) . '), redirecting to list');
            redirect(site_url('dietetic/portal/meal_plans'));
            return;
        }

        $client_id = get_client_user_id();
        log_activity('[DIETETIC DEBUG] Client ID: ' . $client_id);

        // Get patient
        try {
            $patient = $this->dietetic_patients_model->get_by_client($client_id);
        } catch (Exception $e) {
            log_activity('[DIETETIC DEBUG] Exception getting patient: ' . $e->getMessage());
            $patient = null;
        }

        if (!$patient) {
            log_activity('[DIETETIC DEBUG] No patient found for client');
            $this->load->view('portal_no_access');
            return;
        }

        log_activity('[DIETETIC DEBUG] Patient found: ID = ' . $patient->id);

        // Load meal plans model
        $this->load->model('dietetic/dietetic_meal_plans_model');

        // Get meal plan
        $meal_plan = $this->dietetic_meal_plans_model->get($meal_plan_id);

        if (!$meal_plan) {
            log_activity('[DIETETIC DEBUG] Meal plan not found: ID = ' . $meal_plan_id);
            show_404();
            return;
        }

        log_activity('[DIETETIC DEBUG] Meal plan found: ID = ' . $meal_plan->id . ', program_id = ' . $meal_plan->program_id);

        // Get program
        $program = $this->dietetic_programs_model->get($meal_plan->program_id);

        if (!$program) {
            log_activity('[DIETETIC DEBUG] Program not found: ID = ' . $meal_plan->program_id);
            show_404();
            return;
        }

        log_activity('[DIETETIC DEBUG] Program found: ID = ' . $program->id . ', patient_id = ' . $program->patient_id);

        // Verify this meal plan belongs to the patient's program
        if ($program->patient_id != $patient->id) {
            log_activity('[DIETETIC DEBUG] Access denied: Program patient_id (' . $program->patient_id . ') != Patient ID (' . $patient->id . ')');
            show_404();
            return;
        }

        log_activity('[DIETETIC DEBUG] Access granted, loading view');

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
     * Alias for view_meal_plan without underscore (for routing testing)
     * Recommended to use this URL format: /dietetic/portal/viewmealplan/123
     * @param int $meal_plan_id
     */
    public function viewmealplan($meal_plan_id = null)
    {
        return $this->view_meal_plan($meal_plan_id);
    }

    /**
     * Alternative method name for better URL compatibility
     * Access via: /dietetic/portal/mealplan/123
     * @param int $meal_plan_id
     */
    public function mealplan($meal_plan_id = null)
    {
        return $this->view_meal_plan($meal_plan_id);
    }

    /**
     * View meal plan using GET parameter (workaround for routing issues)
     * Access via: /dietetic/portal/meal_plan_view?id=123
     * This is a fallback method that uses query parameters instead of URL segments
     */
    public function meal_plan_view()
    {
        $meal_plan_id = $this->input->get('id');

        if (empty($meal_plan_id)) {
            // Also check POST in case it's sent that way
            $meal_plan_id = $this->input->post('id');
        }

        log_activity('[DIETETIC DEBUG] meal_plan_view (GET method) called with ID: ' . var_export($meal_plan_id, true));

        return $this->view_meal_plan($meal_plan_id);
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

    /**
     * View my dietitians - shows dietitians that follow this patient
     */
    public function my_dietitians()
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
        $data['title'] = 'Mon Diététicien';

        // Load models
        $this->load->model('staff_model');

        // Get current dietitian
        $data['dietitian'] = $this->staff_model->get($patient->dietitian_id);

        // Get dietitian's average rating (with error handling for missing table)
        if ($this->load_ratings_model()) {
            try {
                $data['dietitian_rating'] = $this->dietetic_ratings_model->get_dietitian_average($patient->dietitian_id);
                $data['my_rating'] = $this->dietetic_ratings_model->get_by_patient_dietitian($patient->id, $patient->dietitian_id);
                $data['can_rate'] = $this->dietetic_ratings_model->can_rate($patient->id, $patient->dietitian_id);
            } catch (Exception $e) {
                $data['dietitian_rating'] = null;
                $data['my_rating'] = null;
                $data['can_rate'] = false;
                $data['error'] = 'Erreur lors du chargement des notes: ' . $e->getMessage();
            }
        } else {
            // Table doesn't exist yet
            $data['dietitian_rating'] = null;
            $data['my_rating'] = null;
            $data['can_rate'] = false;
            $data['error'] = 'Le système de notation n\'est pas encore activé. Contactez l\'administrateur.';
        }

        $this->load->view('portal_my_dietitians', $data);
    }

    /**
     * Rate dietitian - submit or update rating
     */
    public function rate_dietitian($dietitian_id = null)
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

        // Load ratings model
        if (!$this->load_ratings_model()) {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['success' => false, 'message' => 'Le système de notation n\'est pas encore installé']);
                return;
            }
            set_alert('danger', 'Le système de notation n\'est pas encore installé. Contactez l\'administrateur.');
            redirect(site_url('dietetic/portal/my_dietitians'));
            return;
        }

        // If no dietitian_id provided, use patient's current dietitian
        if (!$dietitian_id) {
            $dietitian_id = $patient->dietitian_id;
        }

        // Check if patient can rate this dietitian
        if (!$this->dietetic_ratings_model->can_rate($patient->id, $dietitian_id)) {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['success' => false, 'message' => 'Vous devez avoir au moins une consultation complétée pour noter votre diététicien']);
                return;
            }
            set_alert('danger', 'Vous devez avoir au moins une consultation complétée pour noter votre diététicien');
            redirect(site_url('dietetic/portal/my_dietitians'));
            return;
        }

        // Handle form submission
        if ($this->input->post()) {
            $rating_data = [
                'patient_id' => $patient->id,
                'dietitian_id' => $dietitian_id,
                'professionalism_rating' => $this->input->post('professionalism_rating'),
                'listening_rating' => $this->input->post('listening_rating'),
                'advice_rating' => $this->input->post('advice_rating'),
                'results_rating' => $this->input->post('results_rating'),
                'availability_rating' => $this->input->post('availability_rating'),
                'comment' => $this->input->post('comment'),
                'is_public' => 1,
            ];

            try {
                $rating_id = $this->dietetic_ratings_model->add($rating_data);

                if ($rating_id) {
                    if ($this->input->is_ajax_request()) {
                        echo json_encode(['success' => true, 'message' => 'Votre note a été enregistrée avec succès!']);
                        return;
                    }

                    set_alert('success', 'Votre note a été enregistrée avec succès!');
                    redirect(site_url('dietetic/portal/my_dietitians'));
                    return;
                } else {
                    if ($this->input->is_ajax_request()) {
                        echo json_encode(['success' => false, 'message' => 'Échec de l\'enregistrement de la note']);
                        return;
                    }

                    $data['error'] = 'Échec de l\'enregistrement de la note.';
                }
            } catch (Exception $e) {
                if ($this->input->is_ajax_request()) {
                    echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
                    return;
                }

                $data['error'] = 'Erreur: ' . $e->getMessage();
            }
        }

        // Display rating form
        if (!isset($data)) {
            $data = [];
        }

        $data['patient'] = $patient;
        $data['title'] = 'Noter Mon Diététicien';

        // Load staff model to get dietitian info
        $this->load->model('staff_model');
        $data['dietitian'] = $this->staff_model->get($dietitian_id);

        // Get existing rating if any
        $data['existing_rating'] = $this->dietetic_ratings_model->get_by_patient_dietitian($patient->id, $dietitian_id);

        $this->load->view('portal_rate_dietitian', $data);
    }
}

