<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Portal extends App_Controller
{
    private $ratings_model_loaded = false;

    public function __construct()
    {
        parent::__construct();

        // Disable CSRF protection for AJAX notification methods
        $csrf_exclude_uris = [
            'dietetic/portal/get_notifications',
            'dietetic/portal/mark_notification_read',
            'dietetic/portal/delete_notification',
            'dietetic/portal/mark_all_notifications_read',
            'dietetic/portal/save_fcm_token',
            'dietetic/portal/delete_fcm_token'
        ];

        $current_uri = uri_string();
        if (in_array($current_uri, $csrf_exclude_uris)) {
            $this->config->set_item('csrf_protection', FALSE);
        }

        // Load helper functions
        $this->load->helper('dietetic/dietetic');

        // Load models
        $this->load->model('dietetic/dietetic_patients_model');
        $this->load->model('dietetic/dietetic_measurements_model');
        $this->load->model('dietetic/dietetic_programs_model');
        $this->load->model('dietetic/dietetic_consultations_model');
        $this->load->model('dietetic/dietetic_food_surveys_model');
    }

    /**
     * CodeIgniter _remap() method - handles all routing for this controller
     * This is necessary because Perfex CRM doesn't automatically route URL segments to module controller methods
     *
     * URL format: /dietetic/portal/{method}/{param1}/{param2}/...
     *
     * Example: /dietetic/portal/view_meal_plan/123
     *   - $method = "view_meal_plan"
     *   - $params = [123]
     */
    public function _remap($method, $params = [])
    {
        log_activity('[DIETETIC DEBUG] _remap called - Method: ' . $method . ', Params: ' . json_encode($params));

        // List of valid methods in this controller
        $valid_methods = [
            'index',
            'measurements',
            'add_measurement',
            'meal_plans',
            'view_meal_plan',
            'viewmealplan',
            'mealplan',
            'meal_plan_view',
            'consultations',
            'my_dietitians',
            'rate_dietitian',
            'test',
            'test_with_param',
            'repair_orphans',
            'food_surveys',
            'food_survey_submit',
            'save_daily_entry',
            'view_recommendations',
            'add_comment',
            'upload_photo',
            'delete_photo',
            'notification_preferences',
            'save_notification_preferences',
            'save_fcm_token',
            'delete_fcm_token',
            'get_firebase_config',
            'get_notifications',
            'delete_notification',
            'mark_notification_read',
            'mark_all_notifications_read',
            'debug_prefs',
            'debug_firebase',
            'run_firebase_fix',
            'check_notifications_system',
            'debug_notifications_raw',
            'create_patient_notifications_table',
            'add_test_notifications',
            'debug_notifications_api',
            'check_current_user',
            'fix_notifications_table',
            'install_patient_notifications',
            'install_recipe_library',
            // Recipe methods
            'recipes',
            'recipe_view',
            'recipes_favorites',
            'recipe_rate',
            'add_to_favorites',
            'remove_from_favorites'
        ];

        // If method doesn't exist, treat it as index with the method name as a parameter
        if (!in_array($method, $valid_methods)) {
            log_activity('[DIETETIC DEBUG] Method not found: ' . $method . ', redirecting to index');
            // Method not found, call index instead
            return call_user_func_array([$this, 'index'], array_merge([$method], $params));
        }

        // Call the requested method with all parameters
        log_activity('[DIETETIC DEBUG] Calling method: ' . $method . ' with params: ' . json_encode($params));
        return call_user_func_array([$this, $method], $params);
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

        // Get client info for patient name
        $this->load->model('clients_model');
        $client = $this->clients_model->get($patient->client_id);
        $data['client'] = $client;

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

        // Get weight evolution for chart (last 5 entries only for better visibility)
        try {
            // Get all measurements (up to 1000) to ensure we get the most recent ones
            $all_evolution = $this->dietetic_patients_model->get_weight_evolution($patient->id, 1000);
            // Keep only the last 5 entries (most recent)
            $data['weight_evolution'] = array_slice($all_evolution, -5);
        } catch (Exception $e) {
            $data['weight_evolution'] = [];
        }

        // Get active food survey
        try {
            if ($this->db->table_exists(db_prefix() . 'dietic_food_surveys')) {
                $this->load->model('dietetic/dietetic_food_surveys_model');
                $active_surveys = $this->dietetic_food_surveys_model->get_active_by_patient($patient->id);
                $data['active_survey'] = !empty($active_surveys) ? $active_surveys[0] : null;

                // Get completion percentage if there's an active survey
                if ($data['active_survey']) {
                    $data['survey_completion'] = $this->dietetic_food_surveys_model->get_completion_percentage($data['active_survey']->id);
                } else {
                    $data['survey_completion'] = 0;
                }
            } else {
                $data['active_survey'] = null;
                $data['survey_completion'] = 0;
            }
        } catch (Exception $e) {
            $data['active_survey'] = null;
            $data['survey_completion'] = 0;
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
     * Add measurement from portal - Rewritten for robustness
     */
    public function add_measurement()
    {
        // Step 1: Check authentication
        if (!is_client_logged_in()) {
            if ($this->input->is_ajax_request()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Non connecté']);
                die();
            }
            header('Location: ' . site_url('authentication/login'), true, 302);
            die();
        }

        $client_id = get_client_user_id();

        // Step 2: Get patient record
        try {
            $patient = $this->dietetic_patients_model->get_by_client($client_id);
        } catch (Exception $e) {
            log_activity('Portal add_measurement - Error getting patient: ' . $e->getMessage());
            $patient = null;
        }

        if (!$patient) {
            if ($this->input->is_ajax_request()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Patient non trouvé']);
                die();
            }
            $this->load->view('portal_no_access');
            die();
        }

        // Step 3: Handle form submission
        if ($this->input->post()) {
            log_activity('Portal add_measurement - POST received for patient: ' . $patient->id);

            try {
                // Get form data
                $weight = $this->input->post('weight');
                $measurement_date = $this->input->post('measurement_date');

                // Validate required fields
                if (empty($weight) || empty($measurement_date)) {
                    throw new Exception('Le poids et la date sont requis');
                }

                // Calculate BMI if height available
                $bmi = null;
                if (!empty($patient->height) && $weight > 0) {
                    $height_m = floatval($patient->height) / 100;
                    if ($height_m > 0) {
                        $bmi = floatval($weight) / ($height_m * $height_m);
                        $bmi = round($bmi, 2);
                    }
                }

                // Calculate body fat percentage if not provided
                $body_fat = $this->input->post('body_fat');
                if (empty($body_fat) && $bmi && !empty($patient->birth_date)) {
                    try {
                        $birth_date = new DateTime($patient->birth_date);
                        $today = new DateTime();
                        $age = $today->diff($birth_date)->y;
                        $gender = ($patient->gender === 'male') ? 1 : 0;
                        $body_fat = (1.20 * $bmi) + (0.23 * $age) - (10.8 * $gender) - 5.4;
                        $body_fat = max(0, min(100, round($body_fat, 2)));
                    } catch (Exception $e) {
                        log_activity('Portal add_measurement - Body fat calculation error: ' . $e->getMessage());
                        $body_fat = null;
                    }
                }

                // Calculate muscle mass if not provided
                $muscle_mass = $this->input->post('muscle_mass');
                if (empty($muscle_mass) && !empty($weight) && !empty($body_fat)) {
                    $body_fat_kg = floatval($weight) * (floatval($body_fat) / 100);
                    $muscle_mass = ((floatval($weight) - $body_fat_kg) / floatval($weight)) * 100;
                    $muscle_mass = max(0, min(100, round($muscle_mass, 2)));
                }

                // Prepare measurement data
                $measurement_data = [
                    'patient_id' => $patient->id,
                    'measurement_date' => $measurement_date,
                    'weight' => !empty($weight) ? floatval($weight) : null,
                    'bmi' => $bmi,
                    'body_fat' => !empty($body_fat) ? floatval($body_fat) : null,
                    'muscle_mass' => !empty($muscle_mass) ? floatval($muscle_mass) : null,
                    'waist' => !empty($this->input->post('waist')) ? floatval($this->input->post('waist')) : null,
                    'hips' => !empty($this->input->post('hips')) ? floatval($this->input->post('hips')) : null,
                    'chest' => !empty($this->input->post('chest')) ? floatval($this->input->post('chest')) : null,
                    'arms' => !empty($this->input->post('arms')) ? floatval($this->input->post('arms')) : null,
                    'thighs' => !empty($this->input->post('thighs')) ? floatval($this->input->post('thighs')) : null,
                    'notes' => $this->input->post('notes'),
                    'added_by' => $client_id,
                    'added_by_type' => 'client'
                ];

                log_activity('Portal add_measurement - Attempting to save measurement');

                // Save measurement
                $measurement_id = $this->dietetic_measurements_model->add($measurement_data);

                if (!$measurement_id) {
                    throw new Exception('Échec de l\'enregistrement de la mesure');
                }

                log_activity('Portal add_measurement - Measurement saved successfully: ' . $measurement_id);

                // Check for milestones after successful measurement
                try {
                    $this->load->model('dietetic/dietetic_notifications_model');
                    $this->dietetic_notifications_model->check_milestones($patient->id);
                    log_activity('Portal add_measurement - Milestone check completed for patient: ' . $patient->id);
                } catch (Exception $e) {
                    log_activity('Portal add_measurement - Milestone check error: ' . $e->getMessage());
                    // Don't fail the measurement save if milestone check fails
                }

                // AJAX request response
                if ($this->input->is_ajax_request()) {
                    // Clear any output buffers for AJAX
                    while (ob_get_level() > 0) {
                        ob_end_clean();
                    }
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => true,
                        'message' => 'Mesure ajoutée avec succès!',
                        'measurement_id' => $measurement_id
                    ]);
                    die();
                }

                // For regular form submission - immediate redirect
                // Clear all output buffers first
                while (ob_get_level() > 0) {
                    ob_end_clean();
                }

                // Set success message in session
                $_SESSION['message-success'] = 'Mesure ajoutée avec succès!';

                // Immediate redirect without any further processing
                header('Location: ' . site_url('dietetic/portal/measurements'), true, 302);
                exit(0);

            } catch (Exception $e) {
                log_activity('Portal add_measurement - Error: ' . $e->getMessage());

                if ($this->input->is_ajax_request()) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => false,
                        'message' => 'Erreur: ' . $e->getMessage()
                    ]);
                    die();
                }

                // Show form with error
                $data['error'] = 'Erreur: ' . $e->getMessage();
                $data['patient'] = $patient;
                $this->load->model('clients_model');
                $data['client'] = $this->clients_model->get($patient->client_id);
                $this->load->view('portal_add_measurement', $data);
                return;
            }
        }

        // Step 4: Display form (GET request)
        $data = [];
        $data['patient'] = $patient;
        $this->load->model('clients_model');
        $data['client'] = $this->clients_model->get($patient->client_id);
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

        // Get program WITHOUT access check (we'll verify patient ownership manually below)
        // Pass false as second parameter to bypass staff permission checks for client portal
        $program = $this->dietetic_programs_model->get($meal_plan->program_id, false);

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
     * Test method to verify routing is working
     * Access via: /dietetic/portal/test
     */
    public function test()
    {
        echo "<h1>✓ Routing Test Successful!</h1>";
        echo "<p>If you see this message, the Portal controller is accessible.</p>";
        echo "<p>Current URI: " . htmlspecialchars($_SERVER['REQUEST_URI']) . "</p>";
        echo "<p>Controller: Portal</p>";
        echo "<p>Method: test()</p>";
        echo "<hr>";
        echo "<p><strong>Test URLs:</strong></p>";
        echo "<ul>";
        echo "<li><a href='" . site_url('dietetic/portal/test_with_param/123') . "'>Test with parameter</a></li>";
        echo "<li><a href='" . site_url('dietetic/portal/view_meal_plan/2') . "'>view_meal_plan/2 (original)</a></li>";
        echo "<li><a href='" . site_url('dietetic/portal/viewmealplan/2') . "'>viewmealplan/2 (no underscore)</a></li>";
        echo "<li><a href='" . site_url('dietetic/portal/mealplan/2') . "'>mealplan/2 (short)</a></li>";
        echo "<li><a href='" . site_url('dietetic/portal/meal_plan_view?id=2') . "'>meal_plan_view?id=2 (GET param)</a></li>";
        echo "</ul>";
        exit;
    }

    /**
     * Test method with parameter
     */
    public function test_with_param($id = null)
    {
        echo "<h1>✓ Routing with Parameter Test Successful!</h1>";
        echo "<p>Received parameter: <strong>" . htmlspecialchars($id) . "</strong></p>";
        echo "<p>If you see this with the correct ID, routing with parameters works.</p>";
        echo "<p><a href='" . site_url('dietetic/portal/test') . "'>← Back to test menu</a></p>";
        exit;
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

    /**
     * Repair orphan meal plans (meal plans with missing programs)
     * This is a diagnostic and repair tool accessible only to logged-in users
     * Access via: /dietetic/portal/repair_orphans
     */
    public function repair_orphans()
    {
        // Require staff access (admin or dietitian) for security
        if (!is_staff_logged_in()) {
            echo '<h1>Access Denied</h1>';
            echo '<p>This diagnostic tool requires staff access.</p>';
            echo '<p>Please <a href="' . admin_url() . '">login as staff</a> to use this tool.</p>';
            return;
        }

        $action = $this->input->get('action') ?? 'diagnostic';

        echo "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Réparation des Meal Plans Orphelins</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1200px; margin: 20px auto; padding: 20px; background: #f5f5f5; }
        h1 { color: #2c3e50; border-bottom: 3px solid #3498db; padding-bottom: 10px; }
        h2 { color: #34495e; margin-top: 30px; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .warning { background: #fff3cd; border: 1px solid #ffeaa7; color: #856404; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; background: white; margin: 20px 0; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        th { background: #3498db; color: white; padding: 12px; text-align: left; }
        td { padding: 10px; border-bottom: 1px solid #ddd; }
        tr:hover { background: #f8f9fa; }
        .actions { margin: 30px 0; }
        button, .btn { background: #3498db; color: white; border: none; padding: 12px 24px; font-size: 16px; cursor: pointer; border-radius: 5px; margin-right: 10px; text-decoration: none; display: inline-block; }
        button:hover, .btn:hover { background: #2980b9; }
        button.danger { background: #e74c3c; }
        button.danger:hover { background: #c0392b; }
        .stat { display: inline-block; background: white; padding: 20px; margin: 10px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); min-width: 200px; }
        .stat-value { font-size: 36px; font-weight: bold; color: #3498db; }
        .stat-label { color: #7f8c8d; font-size: 14px; text-transform: uppercase; }
    </style>
</head>
<body>
    <h1>🔧 Réparation des Meal Plans Orphelins</h1>
    <p>Ce script détecte et répare les meal plans qui référencent des programmes inexistants.</p>
";

        if ($action === 'diagnostic') {
            echo "<div class='info'><strong>MODE: DIAGNOSTIC</strong> - Aucune modification ne sera effectuée</div>";

            // Find all orphan meal plans
            $this->load->model('dietetic/dietetic_meal_plans_model');

            $query = "
                SELECT
                    mp.id as meal_plan_id,
                    mp.program_id as missing_program_id,
                    mp.plan_name,
                    mp.week_number,
                    mp.start_date,
                    mp.end_date,
                    mp.created_at
                FROM " . db_prefix() . "dietic_meal_plans mp
                LEFT JOIN " . db_prefix() . "dietic_programs p ON p.id = mp.program_id
                WHERE p.id IS NULL
                ORDER BY mp.id
            ";

            $orphans = $this->db->query($query)->result_array();

            // Statistics
            $total_meal_plans = $this->db->count_all_results(db_prefix() . 'dietic_meal_plans');
            $orphan_count = count($orphans);
            $healthy_count = $total_meal_plans - $orphan_count;

            echo "<h2>📊 Statistiques</h2>";
            echo "<div class='stat'>
                    <div class='stat-value'>$total_meal_plans</div>
                    <div class='stat-label'>Total Meal Plans</div>
                  </div>";
            echo "<div class='stat'>
                    <div class='stat-value' style='color: #e74c3c;'>$orphan_count</div>
                    <div class='stat-label'>Meal Plans Orphelins</div>
                  </div>";
            echo "<div class='stat'>
                    <div class='stat-value' style='color: #27ae60;'>$healthy_count</div>
                    <div class='stat-label'>Meal Plans Valides</div>
                  </div>";

            if ($orphan_count === 0) {
                echo "<div class='success'>✅ <strong>Aucun meal plan orphelin détecté !</strong> Toutes les références sont valides.</div>";
            } else {
                echo "<div class='warning'>⚠️ <strong>$orphan_count meal plan(s) orphelin(s) détecté(s)</strong></div>";

                // Display orphan list
                echo "<h2>📋 Liste des Meal Plans Orphelins</h2>";
                echo "<table>";
                echo "<tr>
                        <th>Meal Plan ID</th>
                        <th>Nom du Plan</th>
                        <th>Programme Manquant (ID)</th>
                        <th>Semaine</th>
                        <th>Date Début</th>
                        <th>Date Fin</th>
                        <th>Créé le</th>
                      </tr>";

                foreach ($orphans as $orphan) {
                    echo "<tr>";
                    echo "<td><strong>#{$orphan['meal_plan_id']}</strong></td>";
                    echo "<td>" . htmlspecialchars($orphan['plan_name'] ?? 'Sans nom') . "</td>";
                    echo "<td><span style='color: #e74c3c;'>Programme #{$orphan['missing_program_id']} (inexistant)</span></td>";
                    echo "<td>Semaine {$orphan['week_number']}</td>";
                    echo "<td>" . ($orphan['start_date'] ?? 'N/A') . "</td>";
                    echo "<td>" . ($orphan['end_date'] ?? 'N/A') . "</td>";
                    echo "<td>" . $orphan['created_at'] . "</td>";
                    echo "</tr>";
                }

                echo "</table>";

                // Get patient info
                $patient = $this->db->get_where(db_prefix() . 'dietic_patients', ['id' => 1])->row();

                if ($patient) {
                    $client = $this->db->get_where(db_prefix() . 'clients', ['userid' => $patient->client_id])->row();

                    echo "<h2>🔧 Proposition de Réparation</h2>";
                    echo "<div class='info'>";
                    echo "<p><strong>Pour chaque meal plan orphelin, le script va :</strong></p>";
                    echo "<ol>";
                    echo "<li>Créer un programme de remplacement</li>";
                    echo "<li>Associer ce programme au patient ID 1 ({$client->company})</li>";
                    echo "<li>Utiliser le diététicien du patient (ID: {$patient->dietitian_id})</li>";
                    echo "<li>Définir des valeurs par défaut appropriées</li>";
                    echo "</ol>";
                    echo "</div>";

                    echo "<div class='actions'>";
                    echo "<a href='" . site_url('dietetic/portal/repair_orphans?action=repair') . "' class='btn' onclick=\"return confirm('Êtes-vous sûr de vouloir réparer tous les meal plans orphelins ?');\">🔧 Lancer la Réparation</a>";
                    echo "<a href='" . admin_url('dietetic/programs') . "' class='btn' style='background: #95a5a6;'>📋 Voir les Programmes</a>";
                    echo "</div>";
                } else {
                    echo "<div class='error'>❌ <strong>Erreur :</strong> Patient ID 1 introuvable.</div>";
                }
            }

        } elseif ($action === 'repair') {
            echo "<div class='warning'><strong>MODE: RÉPARATION</strong> - Modifications en cours...</div>";

            // Get patient info
            $patient = $this->db->get_where(db_prefix() . 'dietic_patients', ['id' => 1])->row();

            if (!$patient) {
                echo "<div class='error'>❌ <strong>Erreur :</strong> Patient ID 1 introuvable.</div>";
                die();
            }

            $client = $this->db->get_where(db_prefix() . 'clients', ['userid' => $patient->client_id])->row();

            // Find all orphan meal plans
            $query = "
                SELECT
                    mp.id as meal_plan_id,
                    mp.program_id as missing_program_id,
                    mp.plan_name,
                    mp.week_number,
                    mp.start_date,
                    mp.end_date,
                    mp.created_at
                FROM " . db_prefix() . "dietic_meal_plans mp
                LEFT JOIN " . db_prefix() . "dietic_programs p ON p.id = mp.program_id
                WHERE p.id IS NULL
                ORDER BY mp.id
            ";

            $orphans = $this->db->query($query)->result_array();

            if (count($orphans) === 0) {
                echo "<div class='success'>✅ Aucun meal plan orphelin à réparer.</div>";
            } else {
                echo "<h2>🔧 Réparation en cours...</h2>";

                $repaired = 0;
                $errors = 0;

                // Start transaction
                $this->db->trans_start();

                try {
                    foreach ($orphans as $orphan) {
                        $meal_plan_id = $orphan['meal_plan_id'];
                        $missing_program_id = $orphan['missing_program_id'];
                        $plan_name = $orphan['plan_name'] ?? "Plan de repas #{$meal_plan_id}";

                        // Create replacement program
                        $program_data = [
                            'id' => $missing_program_id,
                            'patient_id' => $patient->id,
                            'dietitian_id' => $patient->dietitian_id,
                            'program_name' => "Programme pour " . $plan_name,
                            'description' => "Programme créé automatiquement pour réparer le meal plan #{$meal_plan_id}",
                            'start_date' => $orphan['start_date'] ?? date('Y-m-d'),
                            'end_date' => $orphan['end_date'],
                            'status' => 'active',
                            'created_at' => $orphan['created_at'],
                            'updated_at' => date('Y-m-d H:i:s')
                        ];

                        $this->db->insert(db_prefix() . 'dietic_programs', $program_data);

                        echo "<div class='success'>";
                        echo "✅ <strong>Meal Plan #{$meal_plan_id}</strong>: Programme #{$missing_program_id} créé avec succès";
                        echo "<br>&nbsp;&nbsp;&nbsp;&nbsp;→ Nom: " . htmlspecialchars($program_data['program_name']);
                        echo "<br>&nbsp;&nbsp;&nbsp;&nbsp;→ Patient: {$client->company}";
                        echo "<br>&nbsp;&nbsp;&nbsp;&nbsp;→ Diététicien: ID #{$patient->dietitian_id}";
                        echo "</div>";

                        $repaired++;
                    }

                    // Complete transaction
                    $this->db->trans_complete();

                    if ($this->db->trans_status() === FALSE) {
                        throw new Exception("Erreur lors de la transaction");
                    }

                    echo "<div class='success'>";
                    echo "<h3>✅ Réparation terminée avec succès !</h3>";
                    echo "<p><strong>$repaired</strong> meal plan(s) réparé(s)</p>";
                    echo "</div>";

                    echo "<div class='actions'>";
                    echo "<a href='" . site_url('dietetic/portal/repair_orphans') . "' class='btn'>📊 Voir le Diagnostic</a>";
                    echo "<a href='" . site_url('dietetic/portal/view_meal_plan/2') . "' class='btn' style='background: #27ae60;'>🧪 Tester Meal Plan #2</a>";
                    echo "<a href='" . admin_url('dietetic/programs') . "' class='btn' style='background: #95a5a6;'>📋 Voir les Programmes</a>";
                    echo "</div>";

                    // Log the repair
                    log_activity("Dietetic: Meal plans orphelins réparés - $repaired programmes créés");

                } catch (Exception $e) {
                    // Rollback on error
                    $this->db->trans_rollback();

                    echo "<div class='error'>";
                    echo "<h3>❌ Erreur lors de la réparation</h3>";
                    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
                    echo "<p>Aucune modification n'a été effectuée (transaction annulée).</p>";
                    echo "</div>";
                }
            }
        }

        echo "
    <hr>
    <p style='color: #7f8c8d; font-size: 12px;'>
        Script de réparation - Module Dietetic Perfex CRM<br>
        Date: " . date('Y-m-d H:i:s') . "
    </p>
</body>
</html>
";
    }

    /**
     * List food surveys for the logged-in patient
     */
    public function food_surveys()
    {
        // Check if client is logged in
        if (!is_client_logged_in()) {
            redirect(site_url('authentication/login'));
        }

        $client_id = get_client_user_id();

        // Get patient
        $patient = $this->dietetic_patients_model->get_by_client($client_id);

        if (!$patient) {
            $this->load->view('portal_no_access');
            return;
        }

        $data = [];
        $data['patient'] = $patient;
        $data['title'] = 'Mes Enquêtes Alimentaires';

        // Get client info for patient name
        $this->load->model('clients_model');
        $client = $this->clients_model->get($patient->client_id);
        $data['client'] = $client;

        // Get all surveys for this patient
        $data['surveys'] = $this->dietetic_food_surveys_model->get_by_patient($patient->id);

        // Calculate completion percentages
        foreach ($data['surveys'] as &$survey) {
            $survey->completion_percentage = $this->dietetic_food_surveys_model->get_completion_percentage($survey->id);
        }

        $this->load->view('portal/food_surveys/list', $data);
    }

    /**
     * Daily food survey submission form
     *
     * @param int $survey_id
     * @param string $date Optional date in Y-m-d format
     */
    public function food_survey_submit($survey_id, $date = null)
    {
        // Check if client is logged in
        if (!is_client_logged_in()) {
            redirect(site_url('authentication/login'));
        }

        $client_id = get_client_user_id();

        // Get patient
        $patient = $this->dietetic_patients_model->get_by_client($client_id);

        if (!$patient) {
            $this->load->view('portal_no_access');
            return;
        }

        // Get survey
        $survey = $this->dietetic_food_surveys_model->get($survey_id, false); // Don't check access here, we'll verify patient_id manually below

        if (!$survey || $survey->patient_id != $patient->id) {
            show_404();
        }

        $data = [];
        $data['patient'] = $patient;
        $data['survey'] = $survey;
        $data['title'] = 'Soumission Quotidienne - ' . $survey->survey_name;

        // Get client info
        $this->load->model('clients_model');
        $client = $this->clients_model->get($patient->client_id);
        $data['client'] = $client;

        // Determine which date to display
        if ($date && preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $selected_date = $date;
        } else {
            $selected_date = date('Y-m-d');
        }

        $data['selected_date'] = $selected_date;
        $data['is_today'] = ($selected_date == date('Y-m-d'));

        // Get entry for the selected date
        $data['today_entry'] = $this->dietetic_food_surveys_model->get_entry_by_date($survey_id, $selected_date);

        // Get existing beverages if entry exists
        if ($data['today_entry']) {
            $data['beverages'] = $this->dietetic_food_surveys_model->get_beverages($data['today_entry']->id);
        } else {
            $data['beverages'] = [];
        }

        // Calculate previous and next dates for navigation
        $prev_date = date('Y-m-d', strtotime($selected_date . ' -1 day'));
        $next_date = date('Y-m-d', strtotime($selected_date . ' +1 day'));
        $data['prev_date'] = $prev_date;
        $data['next_date'] = $next_date;
        $data['can_go_next'] = (strtotime($next_date) <= strtotime(date('Y-m-d')));

        $this->load->view('portal/food_surveys/submit', $data);
    }

    /**
     * Save daily entry (AJAX)
     */
    public function save_daily_entry()
    {
        header('Content-Type: application/json');

        try {
            // Check if client is logged in
            if (!is_client_logged_in()) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Non authentifié'
                ]);
                return;
            }

            $client_id = get_client_user_id();

            // Get patient
            $patient = $this->dietetic_patients_model->get_by_client($client_id);

            if (!$patient) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Patient non trouvé'
                ]);
                return;
            }

            // Get survey
            $survey_id = $this->input->post('survey_id');
            $survey = $this->dietetic_food_surveys_model->get($survey_id, false); // Don't check access here, we'll verify patient_id manually below

            if (!$survey || $survey->patient_id != $patient->id) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Enquête non trouvée ou accès refusé'
                ]);
                return;
            }

            // Prepare entry data
            $entry_data = [
                'survey_id' => $survey_id,
                'entry_date' => $this->input->post('entry_date') ?: date('Y-m-d'),
                'breakfast_photo' => $this->input->post('breakfast_photo'),
                'breakfast_time' => $this->input->post('breakfast_time'),
                'breakfast_notes' => $this->input->post('breakfast_notes'),
                'lunch_photo' => $this->input->post('lunch_photo'),
                'lunch_time' => $this->input->post('lunch_time'),
                'lunch_notes' => $this->input->post('lunch_notes'),
                'dinner_photo' => $this->input->post('dinner_photo'),
                'dinner_time' => $this->input->post('dinner_time'),
                'dinner_notes' => $this->input->post('dinner_notes'),
                'snack_photo' => $this->input->post('snack_photo'),
                'snack_time' => $this->input->post('snack_time'),
                'snack_notes' => $this->input->post('snack_notes'),
                'water_quantity_ml' => $this->input->post('water_quantity_ml'),
                'submitted_at' => date('Y-m-d H:i:s')
            ];

            // Check if save_entry method exists
            if (!method_exists($this->dietetic_food_surveys_model, 'save_entry')) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Méthode save_entry non trouvée dans le modèle'
                ]);
                return;
            }

            // Save entry
            $entry_id = $this->dietetic_food_surveys_model->save_entry($entry_data);

            if ($entry_id) {
                // Delete existing beverages first to prevent duplication
                $this->dietetic_food_surveys_model->delete_beverages_by_entry($entry_id);

                // Save beverages if provided
                $beverages = $this->input->post('beverages');
                if (is_array($beverages) && count($beverages) > 0) {
                    foreach ($beverages as $beverage) {
                        if (!empty($beverage['name']) && !empty($beverage['quantity']) && !empty($beverage['time'])) {
                            $beverage_data = [
                                'entry_id' => $entry_id,
                                'beverage_name' => $beverage['name'],
                                'quantity_ml' => $beverage['quantity'],
                                'consumption_time' => $beverage['time'],
                                'notes' => $beverage['notes'] ?? null
                            ];
                            $this->dietetic_food_surveys_model->add_beverage($beverage_data);
                        }
                    }
                }

                // Send notifications to dietitian AND patient
                try {
                    if ($this->db->table_exists(db_prefix() . 'dietic_notification_preferences')) {
                        $this->load->model('dietetic/dietetic_notifications_model');
                        $this->load->model('clients_model');

                        // Get patient name
                        $client = $this->clients_model->get($patient->client_id);
                        $patient_name = $client ? $client->company : 'Un patient';

                        // 1. Notify dietitian (email only)
                        if ($patient->dietitian_id) {
                            $this->dietetic_notifications_model->notify_dietitian_food_entry(
                                $patient->dietitian_id,
                                $patient_name,
                                $entry_data['entry_date']
                            );
                        }

                        // 2. Notify patient (email/SMS/WhatsApp based on preferences)
                        $this->dietetic_notifications_model->notify_patient_food_entry_received(
                            $patient->id,
                            $entry_data['entry_date']
                        );
                    }
                } catch (Exception $e) {
                    log_activity('Food entry notification error: ' . $e->getMessage());
                    // Don't fail the whole operation if notification fails
                }

                echo json_encode([
                    'success' => true,
                    'message' => 'Entrée enregistrée avec succès',
                    'entry_id' => $entry_id
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Erreur lors de l\'enregistrement de l\'entrée'
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Erreur serveur: ' . $e->getMessage(),
                'error_line' => $e->getLine(),
                'error_file' => basename($e->getFile())
            ]);
        }
    }

    /**
     * View recommendations for a survey
     *
     * @param int $survey_id
     */
    public function view_recommendations($survey_id)
    {
        // Check if client is logged in
        if (!is_client_logged_in()) {
            redirect(site_url('authentication/login'));
        }

        $client_id = get_client_user_id();

        // Get patient
        $patient = $this->dietetic_patients_model->get_by_client($client_id);

        if (!$patient) {
            $this->load->view('portal_no_access');
            return;
        }

        // Get survey
        $survey = $this->dietetic_food_surveys_model->get($survey_id, false); // Don't check access here, we'll verify patient_id manually below

        if (!$survey || $survey->patient_id != $patient->id) {
            show_404();
        }

        $data = [];
        $data['patient'] = $patient;
        $data['survey'] = $survey;
        $data['title'] = 'Recommandations - ' . $survey->survey_name;

        // Get client info
        $this->load->model('clients_model');
        $client = $this->clients_model->get($patient->client_id);
        $data['client'] = $client;

        // Get all entries with recommendations
        $entries = $this->dietetic_food_surveys_model->get_entries($survey_id);
        $data['entries'] = [];

        foreach ($entries as $entry) {
            if ($entry->has_recommendation) {
                // Get recommendations grouped by meal type
                $entry->recommendations_by_meal = $this->dietetic_food_surveys_model->get_recommendations_by_meal($entry->id);

                // Get comments for each recommendation in each meal type
                foreach ($entry->recommendations_by_meal as $meal_type => &$recommendations) {
                    foreach ($recommendations as &$recommendation) {
                        $recommendation->comments = $this->dietetic_food_surveys_model->get_comments($recommendation->id);
                    }
                }

                $data['entries'][] = $entry;
            }
        }

        $this->load->view('portal/food_surveys/recommendations', $data);
    }

    /**
     * Add comment to a recommendation (AJAX)
     */
    public function add_comment()
    {
        header('Content-Type: application/json');

        // Check if client is logged in
        if (!is_client_logged_in()) {
            echo json_encode([
                'success' => false,
                'message' => 'Non authentifié'
            ]);
            return;
        }

        $client_id = get_client_user_id();

        // Get patient
        $patient = $this->dietetic_patients_model->get_by_client($client_id);

        if (!$patient) {
            echo json_encode([
                'success' => false,
                'message' => 'Patient non trouvé'
            ]);
            return;
        }

        $recommendation_id = $this->input->post('recommendation_id');
        $comment_text = $this->input->post('comment_text');

        if (!$recommendation_id || !$comment_text) {
            echo json_encode([
                'success' => false,
                'message' => 'Données manquantes'
            ]);
            return;
        }

        // Verify recommendation belongs to patient's survey
        // (Add security check here if needed)

        $data = [
            'recommendation_id' => $recommendation_id,
            'comment_text' => $comment_text
        ];

        $comment_id = $this->dietetic_food_surveys_model->add_comment($data);

        if ($comment_id) {
            echo json_encode([
                'success' => true,
                'message' => 'Commentaire ajouté avec succès',
                'comment_id' => $comment_id
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Erreur lors de l\'ajout du commentaire'
            ]);
        }
    }

    /**
     * Upload photo for food survey entry
     */
    public function upload_photo()
    {
        header('Content-Type: application/json');

        // Check if client is logged in
        if (!is_client_logged_in()) {
            echo json_encode([
                'success' => false,
                'message' => 'Non authentifié'
            ]);
            return;
        }

        // Check if file was uploaded
        if (!isset($_FILES['photo'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Aucun fichier dans la requête'
            ]);
            return;
        }

        if ($_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
            $error_message = 'Erreur upload: ';
            switch ($_FILES['photo']['error']) {
                case UPLOAD_ERR_INI_SIZE:
                    $error_message .= 'Le fichier dépasse upload_max_filesize';
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    $error_message .= 'Le fichier dépasse MAX_FILE_SIZE';
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $error_message .= 'Fichier partiellement téléchargé';
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $error_message .= 'Aucun fichier téléchargé';
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $error_message .= 'Dossier temporaire manquant';
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $error_message .= 'Échec écriture sur disque';
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $error_message .= 'Extension PHP a arrêté le téléchargement';
                    break;
                default:
                    $error_message .= 'Erreur inconnue (' . $_FILES['photo']['error'] . ')';
            }
            echo json_encode([
                'success' => false,
                'message' => $error_message
            ]);
            return;
        }

        $file = $_FILES['photo'];

        // Validate file type
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime_type, $allowed_types)) {
            echo json_encode([
                'success' => false,
                'message' => 'Type de fichier non autorisé. Seules les images (JPEG, PNG, GIF) sont acceptées.'
            ]);
            return;
        }

        // Validate file size (max 5MB)
        $max_size = 5 * 1024 * 1024; // 5MB in bytes
        if ($file['size'] > $max_size) {
            echo json_encode([
                'success' => false,
                'message' => 'Le fichier est trop volumineux. Taille maximale: 5MB.'
            ]);
            return;
        }

        // Create upload directory if it doesn't exist
        $upload_path = FCPATH . 'uploads/dietetic/food_surveys/';
        if (!is_dir($upload_path)) {
            if (!mkdir($upload_path, 0755, true)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Impossible de créer le dossier d\'upload'
                ]);
                return;
            }
        }

        // Check directory permissions
        if (!is_writable($upload_path)) {
            @chmod($upload_path, 0755);
            if (!is_writable($upload_path)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Le dossier d\'upload n\'est pas accessible en écriture'
                ]);
                return;
            }
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'meal_' . uniqid() . '_' . time() . '.' . $extension;
        $destination = $upload_path . $filename;

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            // Set correct permissions on uploaded file
            @chmod($destination, 0644);

            // Create thumbnail for faster loading
            $this->create_thumbnail($destination, $upload_path . 'thumb_' . $filename);

            echo json_encode([
                'success' => true,
                'message' => 'Photo téléchargée avec succès',
                'filename' => $filename,
                'url' => base_url('uploads/dietetic/food_surveys/' . $filename),
                'thumbnail_url' => base_url('uploads/dietetic/food_surveys/thumb_' . $filename)
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Erreur: impossible de déplacer le fichier. Vérifiez les permissions du dossier uploads/dietetic/food_surveys/'
            ]);
        }
    }

    /**
     * Delete uploaded photo
     */
    public function delete_photo()
    {
        header('Content-Type: application/json');

        // Check if client is logged in
        if (!is_client_logged_in()) {
            echo json_encode([
                'success' => false,
                'message' => 'Non authentifié'
            ]);
            return;
        }

        $filename = $this->input->post('filename');

        if (!$filename) {
            echo json_encode([
                'success' => false,
                'message' => 'Nom de fichier manquant'
            ]);
            return;
        }

        // Security: prevent directory traversal
        $filename = basename($filename);

        $upload_path = FCPATH . 'uploads/dietetic/food_surveys/';
        $file_path = $upload_path . $filename;
        $thumb_path = $upload_path . 'thumb_' . $filename;

        $success = false;

        // Delete main file
        if (file_exists($file_path)) {
            $success = unlink($file_path);
        }

        // Delete thumbnail
        if (file_exists($thumb_path)) {
            unlink($thumb_path);
        }

        if ($success) {
            echo json_encode([
                'success' => true,
                'message' => 'Photo supprimée avec succès'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Photo non trouvée ou erreur lors de la suppression'
            ]);
        }
    }

    /**
     * Create thumbnail from image
     */
    private function create_thumbnail($source, $destination, $max_width = 400, $max_height = 400)
    {
        // Get image info
        $image_info = getimagesize($source);
        if (!$image_info) {
            return false;
        }

        list($width, $height, $type) = $image_info;

        // Create image resource based on type
        switch ($type) {
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($source);
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($source);
                break;
            case IMAGETYPE_GIF:
                $image = imagecreatefromgif($source);
                break;
            default:
                return false;
        }

        // Calculate new dimensions
        $ratio = min($max_width / $width, $max_height / $height);
        $new_width = (int)($width * $ratio);
        $new_height = (int)($height * $ratio);

        // Create thumbnail
        $thumb = imagecreatetruecolor($new_width, $new_height);

        // Preserve transparency for PNG and GIF
        if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_GIF) {
            imagealphablending($thumb, false);
            imagesavealpha($thumb, true);
            $transparent = imagecolorallocatealpha($thumb, 255, 255, 255, 127);
            imagefilledrectangle($thumb, 0, 0, $new_width, $new_height, $transparent);
        }

        // Resize
        imagecopyresampled($thumb, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

        // Save thumbnail
        switch ($type) {
            case IMAGETYPE_JPEG:
                imagejpeg($thumb, $destination, 85);
                break;
            case IMAGETYPE_PNG:
                imagepng($thumb, $destination, 8);
                break;
            case IMAGETYPE_GIF:
                imagegif($thumb, $destination);
                break;
        }

        // Clean up
        imagedestroy($image);
        imagedestroy($thumb);

        return true;
    }

    /**
     * Notification preferences page
     */
    public function notification_preferences()
    {
        // Check if client is logged in
        if (!is_client_logged_in()) {
            redirect(site_url('authentication/login'));
        }

        // Get patient record
        $client_id = get_client_user_id();
        $patient = $this->dietetic_patients_model->get_by_client($client_id);

        if (!$patient) {
            set_alert('danger', 'Patient non trouvé');
            redirect(site_url('dietetic/portal'));
        }

        // Check if notifications tables exist
        if (!$this->db->table_exists(db_prefix() . 'dietic_notification_preferences')) {
            set_alert('warning', 'Le système de notifications n\'est pas encore installé');
            redirect(site_url('dietetic/portal'));
        }

        // Load notifications model
        $this->load->model('dietetic/dietetic_notifications_model');

        // Get or create preferences
        $data['preferences'] = $this->dietetic_notifications_model->get_preferences($patient->id);
        if (!$data['preferences']) {
            $this->dietetic_notifications_model->create_default_preferences($patient->id);
            $data['preferences'] = $this->dietetic_notifications_model->get_preferences($patient->id);
        }

        $data['patient'] = $patient;
        $data['title'] = 'Mes Préférences de Notification';

        $this->load->view('portal/notifications/preferences', $data);
    }

    /**
     * Save notification preferences
     */
    public function save_notification_preferences()
    {
        // Check if client is logged in
        if (!is_client_logged_in()) {
            log_activity('📋 [NOTIF PREFS] Access denied - not logged in');
            echo json_encode(['success' => false, 'message' => 'Non autorisé']);
            return;
        }

        header('Content-Type: application/json');

        // Get patient record
        $client_id = get_client_user_id();
        $patient = $this->dietetic_patients_model->get_by_client($client_id);

        if (!$patient) {
            log_activity('📋 [NOTIF PREFS] Patient not found for client_id: ' . $client_id);
            echo json_encode(['success' => false, 'message' => 'Patient non trouvé']);
            return;
        }

        log_activity('📋 [NOTIF PREFS] Starting save for patient_id: ' . $patient->id);

        // Log all POST data (for debugging)
        $all_post = $this->input->post();
        log_activity('📋 [NOTIF PREFS] POST data received: ' . json_encode($all_post));

        // Load notifications model
        $this->load->model('dietetic/dietetic_notifications_model');

        // Prepare preferences data
        $preferences = [
            'reminder_weight' => $this->input->post('reminder_weight') ? 1 : 0,
            'reminder_weight_day' => $this->input->post('reminder_weight_day') ?: 'friday',
            'reminder_weight_time' => $this->input->post('reminder_weight_time') ?: '09:00:00',
            'reminder_water' => $this->input->post('reminder_water') ? 1 : 0,
            'reminder_water_times' => $this->input->post('reminder_water_times') ?: '10:00,14:00,18:00',
            'notify_recommendation' => $this->input->post('notify_recommendation') ? 1 : 0,
            'notify_consultation' => $this->input->post('notify_consultation') ? 1 : 0,
            'notify_milestone' => $this->input->post('notify_milestone') ? 1 : 0,
            'notify_program' => $this->input->post('notify_program') ? 1 : 0,
            'notify_food_entry' => $this->input->post('notify_food_entry') ? 1 : 0,
            'channel_email' => $this->input->post('channel_email') ? 1 : 0,
            'channel_sms' => $this->input->post('channel_sms') ? 1 : 0,
            'channel_whatsapp' => $this->input->post('channel_whatsapp') ? 1 : 0,
            'channel_push' => $this->input->post('channel_push') ? 1 : 0,
        ];

        log_activity('📋 [NOTIF PREFS] Preferences to save: ' . json_encode($preferences));

        // Update preferences
        $result = $this->dietetic_notifications_model->update_preferences($patient->id, $preferences);

        log_activity('📋 [NOTIF PREFS] Update result: ' . ($result ? 'SUCCESS' : 'FAILED'));

        if ($result) {
            // Verify the save by reading back
            $saved_prefs = $this->dietetic_notifications_model->get_preferences($patient->id);
            log_activity('📋 [NOTIF PREFS] Saved preferences verified: ' . json_encode($saved_prefs));

            echo json_encode([
                'success' => true,
                'message' => 'Vos préférences ont été enregistrées avec succès'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement des préférences'
            ]);
        }
    }

    /**
     * Get Firebase configuration for push notifications
     */
    public function get_firebase_config()
    {
        header('Content-Type: application/json');

        // Load Firebase library
        $this->load->library('dietetic/firebase_cloud_messaging');

        $config = $this->firebase_cloud_messaging->get_web_config();

        if ($config) {
            echo json_encode([
                'success' => true,
                'config' => $config
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Firebase push notifications not configured'
            ]);
        }
    }

    /**
     * Save FCM token to database
     */
    public function save_fcm_token()
    {
        // Prevent any output buffering issues
        if (ob_get_level() > 0) {
            ob_clean();
        }
        header('Content-Type: application/json');
        http_response_code(200);

        try {
            log_activity('[FCM DEBUG] save_fcm_token called');

            if (!is_client_logged_in()) {
                log_activity('[FCM DEBUG] User not logged in');
                echo json_encode([
                    'success' => false,
                    'message' => 'Not authenticated'
                ]);
                return;
            }

            // Get patient record
            $client_id = get_client_user_id();
            log_activity('[FCM DEBUG] Client ID: ' . $client_id);

            $patient = $this->dietetic_patients_model->get_by_client($client_id);

            if (!$patient) {
                log_activity('[FCM DEBUG] Patient not found for client: ' . $client_id);
                echo json_encode([
                    'success' => false,
                    'message' => 'Patient not found'
                ]);
                return;
            }

            log_activity('[FCM DEBUG] Patient found: ' . $patient->id);

            // Get POST data - support both JSON and form-encoded
            $token = $this->input->post('token');
            $device_type = $this->input->post('device_type');
            $device_name = $this->input->post('device_name');

            // If not form POST, try JSON
            if (empty($token)) {
                $json = file_get_contents('php://input');
                $data = json_decode($json, true);
                $token = $data['token'] ?? null;
                $device_type = $data['device_type'] ?? null;
                $device_name = $data['device_name'] ?? null;
                log_activity('[FCM DEBUG] Received data via JSON: ' . ($data ? 'Valid' : 'Invalid'));
            } else {
                log_activity('[FCM DEBUG] Received data via POST form');
            }

            if (empty($token)) {
                log_activity('[FCM DEBUG] Token missing in request');
                echo json_encode([
                    'success' => false,
                    'message' => 'Token is required'
                ]);
                return;
            }

            log_activity('[FCM DEBUG] Token received, length: ' . strlen($token));

            // Load Firebase library
            $this->load->library('dietetic/firebase_cloud_messaging');
            log_activity('[FCM DEBUG] Firebase library loaded');

            // Prepare device info
            $device_info = [
                'device_name' => $device_name ?? 'Unknown',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
                'ip_address' => $this->input->ip_address(),
            ];

            // Register token
            log_activity('[FCM DEBUG] Calling register_token for patient: ' . $patient->id);
            $result = $this->firebase_cloud_messaging->register_token(
                $patient->id,
                $token,
                $device_type ?? 'web',
                $device_info
            );

            log_activity('[FCM DEBUG] register_token result: ' . ($result ? 'SUCCESS' : 'FAILED'));

            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Token registered successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to register token'
                ]);
            }
        } catch (Exception $e) {
            log_activity('[FCM ERROR] Exception: ' . $e->getMessage());
            log_activity('[FCM ERROR] Trace: ' . $e->getTraceAsString());

            echo json_encode([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Delete FCM token from database
     */
    public function delete_fcm_token()
    {
        header('Content-Type: application/json');

        if (!is_client_logged_in()) {
            echo json_encode([
                'success' => false,
                'message' => 'Not authenticated'
            ]);
            return;
        }

        // Get POST data
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (empty($data['token'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Token is required'
            ]);
            return;
        }

        // Load Firebase library
        $this->load->library('dietetic/firebase_cloud_messaging');

        // Unregister token
        $result = $this->firebase_cloud_messaging->unregister_token($data['token']);

        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Token deleted successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Token not found or already deleted'
            ]);
        }
    }

    // ==================== NOTIFICATIONS API ====================

    /**
     * Get patient notifications (AJAX)
     */
    public function get_notifications()
    {
        header('Content-Type: application/json');

        try {
            if (!is_client_logged_in()) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Not authenticated'
                ]);
                return;
            }

            // Get patient
            $client_id = get_client_user_id();
            $patient = $this->dietetic_patients_model->get_by_client($client_id);

            if (!$patient) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Patient not found'
                ]);
                return;
            }

            // Check if notifications table exists
            if (!$this->db->table_exists(db_prefix() . 'dietic_notification_logs')) {
                echo json_encode([
                    'success' => true,
                    'notifications' => [],
                    'unread_count' => 0,
                    'total' => 0,
                    'info' => 'Notifications system not yet installed'
                ]);
                return;
            }

            // Load notifications model
            $this->load->model('dietetic/dietetic_notifications_model');

            // TEMPORARY FIX: Check if patient_notifications table exists
            // If not, use notification_logs as fallback
            if (!$this->db->table_exists(db_prefix() . 'dietic_patient_notifications')) {
                log_activity('🔔 [NOTIF] Using notification_logs as fallback for patient_id=' . $patient->id);

                try {
                    // Use logs table as fallback
                    // Include both patient-specific (patient_id) AND system notifications (patient_id=0)
                    $this->db->select('id, patient_id, notification_type, message, channel, status, created_at');
                    $this->db->from(db_prefix() . 'dietic_notification_logs');
                    $this->db->where_in('patient_id', [$patient->id, 0]); // Include both patient and system notifications
                    $this->db->where('status', 'sent'); // Only sent notifications
                    $this->db->order_by('created_at', 'DESC');
                    $this->db->limit(50);

                    $query = $this->db->get();
                    $notifications = $query->result();

                    $sql_executed = $this->db->last_query();
                    log_activity('🔔 [NOTIF] SQL: ' . $sql_executed);
                    log_activity('🔔 [NOTIF] Query executed. Found ' . count($notifications) . ' notifications for patient_id=' . $patient->id);

                    // Format for frontend
                    $formatted_notifications = [];
                    foreach ($notifications as $notification) {
                        $type = $notification->notification_type ?? 'info';
                        $formatted_notifications[] = [
                            'id' => $notification->id,
                            'type' => $type,
                            'title' => $this->get_notification_title($type),
                            'message' => $notification->message ?? '',
                            'icon' => $this->get_notification_icon($type),
                            'url' => null,
                            'is_read' => false,
                            'time_ago' => $this->time_ago($notification->created_at),
                            'created_at' => $notification->created_at
                        ];
                    }

                    log_activity('🔔 [NOTIF] Successfully formatted ' . count($formatted_notifications) . ' notifications');

                    echo json_encode([
                        'success' => true,
                        'notifications' => $formatted_notifications,
                        'unread_count' => count($formatted_notifications),
                        'total' => count($formatted_notifications)
                    ]);
                    return;
                } catch (Exception $e) {
                    log_activity('❌ [NOTIF ERROR] ' . $e->getMessage());
                    echo json_encode([
                        'success' => false,
                        'message' => 'Database error: ' . $e->getMessage()
                    ]);
                    return;
                }
            }

            // Get limit from query parameter
            $limit = $this->input->get('limit') ? (int)$this->input->get('limit') : 50;
            $unread_only = $this->input->get('unread_only') === 'true';

            // Get notifications
            $notifications = $this->dietetic_notifications_model->get_patient_notifications(
                $patient->id,
                $limit,
                $unread_only
            );

            // Get unread count
            $unread_count = $this->dietetic_notifications_model->get_unread_count($patient->id);

            // Format notifications for frontend
            $formatted_notifications = [];
            foreach ($notifications as $notification) {
                $formatted_notifications[] = [
                    'id' => $notification->id,
                    'type' => $notification->notification_type,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'icon' => $notification->icon,
                    'url' => $notification->url,
                    'is_read' => (bool)$notification->is_read,
                    'time_ago' => $this->time_ago($notification->created_at),
                    'created_at' => $notification->created_at
                ];
            }

            echo json_encode([
                'success' => true,
                'notifications' => $formatted_notifications,
                'unread_count' => $unread_count,
                'total' => count($formatted_notifications)
            ]);
        } catch (Exception $e) {
            log_activity('Error in get_notifications: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error loading notifications',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Delete a notification (AJAX)
     */
    public function delete_notification()
    {
        header('Content-Type: application/json');

        try {
            if (!is_client_logged_in()) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Not authenticated'
                ]);
                return;
            }

            // Get patient
            $client_id = get_client_user_id();
            $patient = $this->dietetic_patients_model->get_by_client($client_id);

            if (!$patient) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Patient not found'
                ]);
                return;
            }

            // Get notification ID from GET or POST
            $notification_id = $this->input->get('notification_id') ?: $this->input->post('notification_id');

            // Also try from JSON body (for POST requests)
            if (empty($notification_id)) {
                $json = file_get_contents('php://input');
                $data = json_decode($json, true);
                $notification_id = isset($data['notification_id']) ? $data['notification_id'] : null;
            }

            if (empty($notification_id)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Notification ID is required'
                ]);
                return;
            }

            // TEMPORARY: Since we're using notification_logs as fallback,
            // we can't really "delete" system notifications (patient_id=0)
            // So we'll just return success for the UI to remove it from display

            // Check if patient_notifications table exists
            if ($this->db->table_exists(db_prefix() . 'dietic_patient_notifications')) {
                // Use the model if table exists
                $this->load->model('dietetic/dietetic_notifications_model');
                $result = $this->dietetic_notifications_model->delete_patient_notification(
                    $notification_id,
                    $patient->id
                );
                $unread_count = $this->dietetic_notifications_model->get_unread_count($patient->id);
            } else {
                // Fallback: Just return success without actually deleting from logs
                // (logs should not be deleted, they're for record keeping)
                $result = true;

                // Count remaining notifications
                $this->db->from(db_prefix() . 'dietic_notification_logs');
                $this->db->where_in('patient_id', [$patient->id, 0]);
                $this->db->where('id !=', $notification_id); // Exclude the "deleted" one
                $unread_count = $this->db->count_all_results();
            }

            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Notification supprimée',
                    'unread_count' => $unread_count
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Notification introuvable'
                ]);
            }
        } catch (Exception $e) {
            log_activity('❌ [NOTIF DELETE ERROR] ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Erreur lors de la suppression: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Mark notification as read (AJAX)
     */
    public function mark_notification_read()
    {
        header('Content-Type: application/json');

        if (!is_client_logged_in()) {
            echo json_encode([
                'success' => false,
                'message' => 'Not authenticated'
            ]);
            return;
        }

        // Get patient
        $client_id = get_client_user_id();
        $patient = $this->dietetic_patients_model->get_by_client($client_id);

        if (!$patient) {
            echo json_encode([
                'success' => false,
                'message' => 'Patient not found'
            ]);
            return;
        }

        // Get notification ID from GET or POST
        $notification_id = $this->input->get('notification_id') ?: $this->input->post('notification_id');

        // Also try from JSON body (for POST requests)
        if (empty($notification_id)) {
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);
            $notification_id = isset($data['notification_id']) ? $data['notification_id'] : null;
        }

        if (empty($notification_id)) {
            echo json_encode([
                'success' => false,
                'message' => 'Notification ID is required'
            ]);
            return;
        }

        // Check if patient_notifications table exists
        if ($this->db->table_exists(db_prefix() . 'dietic_patient_notifications')) {
            // Use the model if table exists
            $this->load->model('dietetic/dietetic_notifications_model');

            // Mark as read
            $result = $this->dietetic_notifications_model->mark_as_read(
                $notification_id,
                $patient->id
            );

            if ($result) {
                // Get updated unread count
                $unread_count = $this->dietetic_notifications_model->get_unread_count($patient->id);

                echo json_encode([
                    'success' => true,
                    'message' => 'Notification marked as read',
                    'unread_count' => $unread_count
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Notification not found'
                ]);
            }
        } else {
            // Fallback: When using logs table, we can't mark as read
            // (logs are read-only for record keeping)
            // Just return success for UI

            // Count remaining unread notifications (all from logs)
            $this->db->from(db_prefix() . 'dietic_notification_logs');
            $this->db->where_in('patient_id', [$patient->id, 0]);
            $this->db->where('status', 'sent');
            $unread_count = $this->db->count_all_results();

            echo json_encode([
                'success' => true,
                'message' => 'Notification marked as read',
                'unread_count' => $unread_count
            ]);
        }
    }

    /**
     * Mark all notifications as read (AJAX)
     */
    public function mark_all_notifications_read()
    {
        header('Content-Type: application/json');

        if (!is_client_logged_in()) {
            echo json_encode([
                'success' => false,
                'message' => 'Not authenticated'
            ]);
            return;
        }

        // Get patient
        $client_id = get_client_user_id();
        $patient = $this->dietetic_patients_model->get_by_client($client_id);

        if (!$patient) {
            echo json_encode([
                'success' => false,
                'message' => 'Patient not found'
            ]);
            return;
        }

        // Check if patient_notifications table exists
        if ($this->db->table_exists(db_prefix() . 'dietic_patient_notifications')) {
            // Use the model if table exists
            $this->load->model('dietetic/dietetic_notifications_model');

            // Mark all as read
            $result = $this->dietetic_notifications_model->mark_all_as_read($patient->id);

            echo json_encode([
                'success' => true,
                'message' => 'All notifications marked as read',
                'unread_count' => 0
            ]);
        } else {
            // Fallback: When using logs table, we can't mark as read
            // (logs are read-only for record keeping)
            // Just return success for UI
            echo json_encode([
                'success' => true,
                'message' => 'All notifications marked as read',
                'unread_count' => 0
            ]);
        }
    }

    /**
     * Helper function to convert timestamp to "time ago" format
     */
    private function time_ago($timestamp)
    {
        $time = strtotime($timestamp);
        $diff = time() - $time;

        if ($diff < 60) {
            return 'À l\'instant';
        } elseif ($diff < 3600) {
            $minutes = floor($diff / 60);
            return 'Il y a ' . $minutes . ' min';
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return 'Il y a ' . $hours . 'h';
        } elseif ($diff < 604800) {
            $days = floor($diff / 86400);
            return 'Il y a ' . $days . ' jour' . ($days > 1 ? 's' : '');
        } else {
            return date('d/m/Y', $time);
        }
    }

    /**
     * Get notification icon based on type
     */
    private function get_notification_icon($type)
    {
        $icons = [
            'weight_reminder' => 'fa-balance-scale',
            'water_reminder' => 'fa-tint',
            'recommendation' => 'fa-comments',
            'consultation' => 'fa-calendar',
            'milestone' => 'fa-trophy',
            'program' => 'fa-leaf',
            'food_entry' => 'fa-cutlery',
            'test' => 'fa-flask',
            'info' => 'fa-info-circle'
        ];

        return $icons[$type] ?? 'fa-bell';
    }

    /**
     * Get notification title based on type
     */
    private function get_notification_title($type)
    {
        $titles = [
            'weight_reminder' => 'Rappel de Pesée',
            'water_reminder' => 'Rappel d\'Hydratation',
            'recommendation' => 'Nouvelle Recommandation',
            'consultation' => 'Consultation',
            'milestone' => 'Jalon Atteint',
            'program' => 'Programme Diététique',
            'food_entry' => 'Saisie Alimentaire',
            'test' => 'Test Notification',
            'info' => 'Information'
        ];

        return $titles[$type] ?? 'Notification';
    }

    // ==================== DIAGNOSTIC TOOLS ====================

    /**
     * Diagnostic page for notification preferences
     * Access: /dietetic/portal/debug_prefs
     */
    public function debug_prefs()
    {
        // Check if client is logged in
        if (!is_client_logged_in()) {
            redirect(site_url('authentication/login'));
        }

        // Get patient record
        $client_id = get_client_user_id();
        $patient = $this->dietetic_patients_model->get_by_client($client_id);

        if (!$patient) {
            set_alert('danger', 'Patient non trouvé');
            redirect(site_url('dietetic/portal'));
        }

        echo "<h1>Diagnostic Préférences de Notifications</h1>";
        echo "<style>body{font-family:sans-serif;padding:20px}pre{background:#f5f5f5;padding:10px;border-radius:5px}.ok{color:green;font-weight:bold}.error{color:red;font-weight:bold}.warning{color:orange;font-weight:bold}</style>";

        echo "<h2>Patient</h2>";
        echo "<p>ID: <strong>{$patient->id}</strong></p>";
        echo "<p>Nom: <strong>{$patient->firstname} {$patient->lastname}</strong></p>";

        // Load notifications model
        $this->load->model('dietetic/dietetic_notifications_model');

        echo "<h2>Préférences actuelles</h2>";
        $prefs = $this->dietetic_notifications_model->get_preferences($patient->id);
        if ($prefs) {
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>Paramètre</th><th>Valeur</th></tr>";
            foreach ($prefs as $key => $value) {
                if ($key !== 'id' && $key !== 'patient_id') {
                    $display_value = $value;
                    if (is_numeric($value)) {
                        $display_value = $value ? '✓ Activé' : '✗ Désactivé';
                    }
                    echo "<tr><td><strong>$key</strong></td><td>$display_value</td></tr>";
                }
            }
            echo "</table>";
            echo "<p><em>Dernière mise à jour: {$prefs->updated_at}</em></p>";
        } else {
            echo "<p class='error'>❌ Aucune préférence trouvée pour ce patient</p>";
        }

        echo "<h2>Fichier preferences.php</h2>";
        $prefs_file = FCPATH . 'modules/dietetic/views/portal/notifications/preferences.php';
        if (file_exists($prefs_file)) {
            echo "<p class='ok'>✅ Fichier existe</p>";
            echo "<p>Dernière modification: <strong>" . date("Y-m-d H:i:s", filemtime($prefs_file)) . "</strong></p>";

            $content = file_get_contents($prefs_file);
            if (strpos($content, 'CSRF Token') !== false) {
                echo "<p class='ok'>✅ Token CSRF présent</p>";
            } else {
                echo "<p class='error'>❌ Token CSRF absent</p>";
            }
        } else {
            echo "<p class='error'>❌ Fichier n'existe pas à: $prefs_file</p>";
        }

        echo "<h2>Contrôleur Portal.php</h2>";
        $portal_file = FCPATH . 'modules/dietetic/controllers/Portal.php';
        if (file_exists($portal_file)) {
            echo "<p class='ok'>✅ Fichier existe</p>";
            echo "<p>Dernière modification: <strong>" . date("Y-m-d H:i:s", filemtime($portal_file)) . "</strong></p>";

            $content = file_get_contents($portal_file);
            if (strpos($content, '[NOTIF PREFS]') !== false) {
                echo "<p class='ok'>✅ Logs de débogage présents</p>";
            } else {
                echo "<p class='error'>❌ Logs de débogage absents</p>";
            }
        }

        echo "<h2>Logs d'activité récents</h2>";
        $this->db->order_by('date', 'DESC');
        $this->db->limit(10);
        $this->db->like('description', 'NOTIF PREFS');
        $logs = $this->db->get(db_prefix() . 'activity_log')->result();

        if (!empty($logs)) {
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>Date</th><th>Description</th></tr>";
            foreach ($logs as $log) {
                echo "<tr><td>{$log->date}</td><td>" . htmlspecialchars($log->description) . "</td></tr>";
            }
            echo "</table>";
        } else {
            echo "<p class='warning'>⚠️ Aucun log trouvé (normal si aucune sauvegarde récente)</p>";
        }

        echo "<hr><p><a href='" . site_url('dietetic/portal/notification_preferences') . "'>Retour aux préférences</a></p>";
    }

    /**
     * Diagnostic page for Firebase migration status
     * Access: /dietetic/portal/debug_firebase
     */
    public function debug_firebase()
    {
        // Check if client is logged in
        if (!is_client_logged_in()) {
            redirect(site_url('authentication/login'));
        }

        echo "<h1>Diagnostic Migration Firebase</h1>";
        echo "<style>body{font-family:sans-serif;padding:20px}pre{background:#f5f5f5;padding:10px;border-radius:5px}.ok{color:green;font-weight:bold}.error{color:red;font-weight:bold}.warning{color:orange;font-weight:bold}</style>";

        echo "<h2>Test 1: Table dietic_fcm_tokens</h2>";
        $fcm_table_exists = $this->db->table_exists(db_prefix() . 'dietic_fcm_tokens');
        if ($fcm_table_exists) {
            echo "<p class='ok'>✅ Table existe</p>";

            $count = $this->db->count_all(db_prefix() . 'dietic_fcm_tokens');
            echo "<p>Nombre de tokens: <strong>$count</strong></p>";
        } else {
            echo "<p class='error'>❌ Table n'existe PAS</p>";
        }

        echo "<h2>Test 2: Colonne channel_push dans préférences</h2>";
        $prefs_table_exists = $this->db->table_exists(db_prefix() . 'dietic_notification_preferences');
        if ($prefs_table_exists) {
            echo "<p class='ok'>✅ Table dietic_notification_preferences existe</p>";

            $fields = $this->db->field_data(db_prefix() . 'dietic_notification_preferences');
            $has_channel_push = false;
            foreach ($fields as $field) {
                if ($field->name === 'channel_push') {
                    $has_channel_push = true;
                    echo "<p class='ok'>✅ Colonne 'channel_push' existe</p>";
                    echo "<p>Type: <strong>{$field->type}</strong></p>";
                    break;
                }
            }

            if (!$has_channel_push) {
                echo "<p class='error'>❌ Colonne 'channel_push' n'existe PAS</p>";
            }

            echo "<h3>Toutes les colonnes:</h3><ul>";
            foreach ($fields as $field) {
                echo "<li>{$field->name} ({$field->type})</li>";
            }
            echo "</ul>";
        } else {
            echo "<p class='error'>❌ Table dietic_notification_preferences n'existe PAS</p>";
        }

        echo "<h2>Test 3: Paramètres Firebase</h2>";
        $this->db->like('setting_key', 'firebase');
        $this->db->or_like('setting_key', 'push_enabled');
        $settings = $this->db->get(db_prefix() . 'dietic_notification_settings')->result();

        if (!empty($settings)) {
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>Clé</th><th>Valeur</th></tr>";
            foreach ($settings as $setting) {
                $value = empty($setting->setting_value) ? '<em>Vide</em>' : '[CONFIGURÉ]';
                echo "<tr><td>{$setting->setting_key}</td><td>$value</td></tr>";
            }
            echo "</table>";
        } else {
            echo "<p class='error'>❌ Aucun paramètre Firebase</p>";
        }

        echo "<h2>Résumé</h2>";
        $firebase_installed = $fcm_table_exists && $has_channel_push;
        if ($firebase_installed) {
            echo "<p class='ok' style='font-size:18px'>✅ Migration Firebase INSTALLÉE</p>";
        } else {
            echo "<p class='error' style='font-size:18px'>❌ Migration Firebase INCOMPLÈTE</p>";
            echo "<p>Veuillez exécuter la migration depuis: <a href='" . admin_url('dietetic/notifications/migrations') . "'>Admin → Notifications → Migrations</a></p>";
        }

        echo "<hr><p><a href='" . site_url('dietetic/portal') . "'>Retour au portail</a></p>";
    }

    /**
     * Execute Firebase migration fix automatically
     * URL: /dietetic/portal/run_firebase_fix
     */
    public function run_firebase_fix()
    {
        // Check if client is logged in
        if (!is_client_logged_in()) {
            redirect(site_url('authentication/login'));
        }

        echo "<h1>🔧 Correction Migration Firebase</h1>";
        echo "<style>body{font-family:sans-serif;padding:20px;max-width:800px;margin:0 auto}pre{background:#f5f5f5;padding:10px;border-radius:5px;overflow-x:auto}.ok{color:green;font-weight:bold}.error{color:red;font-weight:bold}.warning{color:orange;font-weight:bold}.info{color:#0066cc;font-weight:bold}hr{margin:30px 0;border:none;border-top:2px solid #ddd}</style>";

        echo "<p>Cette page va automatiquement corriger la migration Firebase en ajoutant les colonnes manquantes.</p>";
        echo "<hr>";

        $errors = [];
        $success = [];

        // Test 1: Check and add channel_push column to preferences table
        echo "<h2>Étape 1: Colonne channel_push dans tbldietic_notification_preferences</h2>";

        $prefs_table = db_prefix() . 'dietic_notification_preferences';
        $prefs_table_exists = $this->db->table_exists($prefs_table);

        if ($prefs_table_exists) {
            $fields = $this->db->field_data($prefs_table);
            $has_channel_push = false;

            foreach ($fields as $field) {
                if ($field->name === 'channel_push') {
                    $has_channel_push = true;
                    break;
                }
            }

            if ($has_channel_push) {
                echo "<p class='ok'>✅ Colonne 'channel_push' existe déjà - Aucune action nécessaire</p>";
                $success[] = "Colonne channel_push existe";
            } else {
                echo "<p class='warning'>⚠️ Colonne 'channel_push' manquante - Ajout en cours...</p>";

                // Add the column
                $sql = "ALTER TABLE `{$prefs_table}` ADD COLUMN `channel_push` tinyint(1) DEFAULT 1 AFTER `channel_whatsapp`";

                try {
                    $result = $this->db->query($sql);
                    if ($result) {
                        echo "<p class='ok'>✅ Colonne 'channel_push' ajoutée avec succès!</p>";
                        $success[] = "Colonne channel_push ajoutée";
                        log_activity('🔧 [FIREBASE FIX] Colonne channel_push ajoutée à ' . $prefs_table);
                    } else {
                        echo "<p class='error'>❌ Erreur lors de l'ajout de la colonne</p>";
                        $errors[] = "Échec ajout channel_push";
                    }
                } catch (Exception $e) {
                    echo "<p class='error'>❌ Erreur: " . $e->getMessage() . "</p>";
                    $errors[] = "Exception: " . $e->getMessage();
                }
            }
        } else {
            echo "<p class='error'>❌ Table {$prefs_table} n'existe pas!</p>";
            $errors[] = "Table préférences inexistante";
        }

        // Test 2: Check and update channel enum in logs table
        echo "<hr><h2>Étape 2: Enum 'channel' dans tbldietic_notification_logs</h2>";

        $logs_table = db_prefix() . 'dietic_notification_logs';
        $logs_table_exists = $this->db->table_exists($logs_table);

        if ($logs_table_exists) {
            // Get current enum values
            $result = $this->db->query("SHOW COLUMNS FROM `{$logs_table}` LIKE 'channel'");
            $row = $result->row_array();

            if ($row) {
                $current_type = $row['Type'];
                echo "<p class='info'>ℹ️ Type actuel: <code>{$current_type}</code></p>";

                // Check if 'push' is already in the enum
                if (strpos($current_type, "'push'") !== false) {
                    echo "<p class='ok'>✅ Valeur 'push' existe déjà dans l'enum - Aucune action nécessaire</p>";
                    $success[] = "Enum channel contient push";
                } else {
                    echo "<p class='warning'>⚠️ Valeur 'push' manquante - Mise à jour en cours...</p>";

                    // Update the enum
                    $sql = "ALTER TABLE `{$logs_table}` MODIFY COLUMN `channel` enum('email','sms','whatsapp','push') NOT NULL";

                    try {
                        $result = $this->db->query($sql);
                        if ($result) {
                            echo "<p class='ok'>✅ Enum 'channel' mis à jour avec succès!</p>";
                            $success[] = "Enum channel mis à jour";
                            log_activity('🔧 [FIREBASE FIX] Enum channel mis à jour dans ' . $logs_table);
                        } else {
                            echo "<p class='error'>❌ Erreur lors de la mise à jour de l'enum</p>";
                            $errors[] = "Échec mise à jour enum";
                        }
                    } catch (Exception $e) {
                        echo "<p class='error'>❌ Erreur: " . $e->getMessage() . "</p>";
                        $errors[] = "Exception: " . $e->getMessage();
                    }
                }
            } else {
                echo "<p class='error'>❌ Impossible de lire la colonne 'channel'</p>";
                $errors[] = "Lecture colonne channel échouée";
            }
        } else {
            echo "<p class='error'>❌ Table {$logs_table} n'existe pas!</p>";
            $errors[] = "Table logs inexistante";
        }

        // Summary
        echo "<hr><h2>📊 Résumé</h2>";

        if (count($errors) === 0) {
            echo "<div style='background:#d4edda;border:1px solid #c3e6cb;padding:20px;border-radius:8px'>";
            echo "<h3 style='color:#155724;margin-top:0'>✅ Migration Firebase Complète!</h3>";
            echo "<p style='color:#155724;margin-bottom:0'>Toutes les modifications ont été appliquées avec succès.</p>";
            echo "</div>";

            echo "<h3>Actions effectuées:</h3><ul>";
            foreach ($success as $item) {
                echo "<li class='ok'>✅ {$item}</li>";
            }
            echo "</ul>";

            echo "<p style='margin-top:30px'><strong>Prochaine étape:</strong> Allez dans l'admin pour vérifier que le statut de la migration est maintenant 'Installé'.</p>";
            echo "<p><a href='" . admin_url('dietetic/notifications/migrations') . "' class='btn btn-primary' style='display:inline-block;padding:10px 20px;background:#01807B;color:white;text-decoration:none;border-radius:5px'>Voir les Migrations</a></p>";
        } else {
            echo "<div style='background:#f8d7da;border:1px solid #f5c6cb;padding:20px;border-radius:8px'>";
            echo "<h3 style='color:#721c24;margin-top:0'>⚠️ Problèmes détectés</h3>";
            echo "<ul style='color:#721c24;margin-bottom:0'>";
            foreach ($errors as $error) {
                echo "<li>{$error}</li>";
            }
            echo "</ul>";
            echo "</div>";

            if (!empty($success)) {
                echo "<h3>Actions réussies:</h3><ul>";
                foreach ($success as $item) {
                    echo "<li class='ok'>✅ {$item}</li>";
                }
                echo "</ul>";
            }
        }

        echo "<hr>";
        echo "<p><a href='" . site_url('dietetic/portal/debug_firebase') . "'>🔍 Voir le diagnostic Firebase</a> | ";
        echo "<a href='" . site_url('dietetic/portal/notification_preferences') . "'>⚙️ Préférences de notifications</a> | ";
        echo "<a href='" . site_url('dietetic/portal') . "'>🏠 Retour au portail</a></p>";
    }

    /**
     * Check if notifications system is properly installed
     * URL: /dietetic/portal/check_notifications_system
     */
    public function check_notifications_system()
    {
        // Check if client is logged in
        if (!is_client_logged_in()) {
            redirect(site_url('authentication/login'));
        }

        echo "<h1>🔔 Diagnostic Système de Notifications</h1>";
        echo "<style>body{font-family:sans-serif;padding:20px;max-width:800px;margin:0 auto}pre{background:#f5f5f5;padding:10px;border-radius:5px;overflow-x:auto}.ok{color:green;font-weight:bold}.error{color:red;font-weight:bold}.warning{color:orange;font-weight:bold}.info{color:#0066cc;font-weight:bold}hr{margin:30px 0;border:none;border-top:2px solid #ddd}table{width:100%;border-collapse:collapse;margin:15px 0}table td,table th{padding:8px;border:1px solid #ddd;text-align:left}</style>";

        echo "<p>Vérification de l'installation du système de notifications...</p><hr>";

        $all_good = true;

        // Test 1: Check notification_logs table
        echo "<h2>Test 1: Table des logs de notifications</h2>";
        $log_table = db_prefix() . 'dietic_notification_logs';
        if ($this->db->table_exists($log_table)) {
            echo "<p class='ok'>✅ Table '{$log_table}' existe</p>";

            $count = $this->db->count_all($log_table);
            echo "<p class='info'>ℹ️ Nombre de notifications enregistrées: <strong>{$count}</strong></p>";

            // Show table structure
            $fields = $this->db->field_data($log_table);
            echo "<details><summary>Voir la structure de la table</summary>";
            echo "<table><tr><th>Colonne</th><th>Type</th></tr>";
            foreach ($fields as $field) {
                echo "<tr><td>{$field->name}</td><td>{$field->type}</td></tr>";
            }
            echo "</table></details>";
        } else {
            echo "<p class='error'>❌ Table '{$log_table}' n'existe PAS</p>";
            echo "<p class='warning'>⚠️ Le système de notifications n'est pas installé. Les notifications ne peuvent pas être affichées.</p>";
            $all_good = false;
        }

        // Test 2: Check preferences table
        echo "<hr><h2>Test 2: Table des préférences</h2>";
        $prefs_table = db_prefix() . 'dietic_notification_preferences';
        if ($this->db->table_exists($prefs_table)) {
            echo "<p class='ok'>✅ Table '{$prefs_table}' existe</p>";

            $count = $this->db->count_all($prefs_table);
            echo "<p class='info'>ℹ️ Nombre de préférences configurées: <strong>{$count}</strong></p>";
        } else {
            echo "<p class='error'>❌ Table '{$prefs_table}' n'existe PAS</p>";
            $all_good = false;
        }

        // Test 3: Check settings table
        echo "<hr><h2>Test 3: Table des paramètres</h2>";
        $settings_table = db_prefix() . 'dietic_notification_settings';
        if ($this->db->table_exists($settings_table)) {
            echo "<p class='ok'>✅ Table '{$settings_table}' existe</p>";

            $settings = $this->db->get($settings_table)->result();
            if (!empty($settings)) {
                echo "<table><tr><th>Paramètre</th><th>Valeur</th></tr>";
                foreach ($settings as $setting) {
                    $value = empty($setting->setting_value) ? '<em>Vide</em>' : '[CONFIGURÉ]';
                    echo "<tr><td>{$setting->setting_key}</td><td>{$value}</td></tr>";
                }
                echo "</table>";
            } else {
                echo "<p class='warning'>⚠️ Aucun paramètre configuré</p>";
            }
        } else {
            echo "<p class='error'>❌ Table '{$settings_table}' n'existe PAS</p>";
            $all_good = false;
        }

        // Test 4: Check FCM tokens table (Firebase)
        echo "<hr><h2>Test 4: Table des tokens Firebase (Push)</h2>";
        $fcm_table = db_prefix() . 'dietic_fcm_tokens';
        if ($this->db->table_exists($fcm_table)) {
            echo "<p class='ok'>✅ Table '{$fcm_table}' existe</p>";

            $count = $this->db->count_all($fcm_table);
            echo "<p class='info'>ℹ️ Nombre de tokens enregistrés: <strong>{$count}</strong></p>";
        } else {
            echo "<p class='warning'>⚠️ Table '{$fcm_table}' n'existe pas (optionnel - pour notifications push)</p>";
        }

        // Test 5: Check if notifications model exists
        echo "<hr><h2>Test 5: Modèle de notifications</h2>";
        $model_path = FCPATH . 'modules/dietetic/models/Dietetic_notifications_model.php';
        if (file_exists($model_path)) {
            echo "<p class='ok'>✅ Modèle de notifications existe</p>";
            echo "<p class='info'>ℹ️ Chemin: <code>{$model_path}</code></p>";
        } else {
            echo "<p class='error'>❌ Modèle de notifications introuvable</p>";
            $all_good = false;
        }

        // Summary
        echo "<hr><h2>📊 Résumé</h2>";
        if ($all_good) {
            echo "<div style='background:#d4edda;border:1px solid #c3e6cb;padding:20px;border-radius:8px'>";
            echo "<h3 style='color:#155724;margin-top:0'>✅ Système de Notifications Installé!</h3>";
            echo "<p style='color:#155724;margin-bottom:0'>Toutes les tables nécessaires sont présentes. Les notifications devraient s'afficher correctement.</p>";
            echo "</div>";

            echo "<p style='margin-top:20px'><strong>Pour voir les notifications:</strong></p>";
            echo "<ol>";
            echo "<li>Ouvrez la page du portail patient</li>";
            echo "<li>Cliquez sur l'icône 🔔 en haut à droite</li>";
            echo "<li>Les notifications s'afficheront dans le panneau</li>";
            echo "</ol>";
        } else {
            echo "<div style='background:#f8d7da;border:1px solid #f5c6cb;padding:20px;border-radius:8px'>";
            echo "<h3 style='color:#721c24;margin-top:0'>⚠️ Système de Notifications Incomplet</h3>";
            echo "<p style='color:#721c24;margin-bottom:0'>Certaines tables sont manquantes. Le système de notifications doit être installé depuis l'admin.</p>";
            echo "</div>";

            echo "<p style='margin-top:20px'><strong>Pour installer le système:</strong></p>";
            echo "<ol>";
            echo "<li>Connectez-vous en tant qu'admin</li>";
            echo "<li>Allez dans <strong>Diététique → Notifications → Migrations</strong></li>";
            echo "<li>Installez la migration 'Système de Notifications'</li>";
            echo "</ol>";
        }

        echo "<hr>";
        echo "<p><a href='" . site_url('dietetic/portal/debug_firebase') . "'>🔍 Diagnostic Firebase</a> | ";
        echo "<a href='" . site_url('dietetic/portal/notification_preferences') . "'>⚙️ Préférences</a> | ";
        echo "<a href='" . site_url('dietetic/portal') . "'>🏠 Retour au portail</a></p>";
    }

    /**
     * Debug notifications - show raw data
     * URL: /dietetic/portal/debug_notifications_raw
     */
    public function debug_notifications_raw()
    {
        // Check if client is logged in
        if (!is_client_logged_in()) {
            redirect(site_url('authentication/login'));
        }

        echo "<h1>🔔 Debug Notifications Brutes</h1>";
        echo "<style>body{font-family:sans-serif;padding:20px;max-width:1200px;margin:0 auto}pre{background:#f5f5f5;padding:15px;border-radius:5px;overflow-x:auto;white-space:pre-wrap;word-wrap:break-word}.ok{color:green;font-weight:bold}.error{color:red;font-weight:bold}.info{color:#0066cc;font-weight:bold}hr{margin:30px 0;border:none;border-top:2px solid #ddd}table{width:100%;border-collapse:collapse;margin:15px 0}table td,table th{padding:8px;border:1px solid #ddd;text-align:left;font-size:13px}table th{background:#f7f7f7}</style>";

        // Get patient
        $client_id = get_client_user_id();
        $patient = $this->dietetic_patients_model->get_by_client($client_id);

        echo "<p><strong>Patient ID:</strong> {$patient->id}</p>";
        echo "<p><strong>Client ID:</strong> {$client_id}</p>";
        echo "<hr>";

        // Check table existence
        $logs_table = db_prefix() . 'dietic_notification_logs';
        if (!$this->db->table_exists($logs_table)) {
            echo "<p class='error'>❌ Table {$logs_table} n'existe PAS</p>";
            echo "<p><a href='" . site_url('dietetic/portal') . "'>Retour</a></p>";
            return;
        }

        echo "<p class='ok'>✅ Table {$logs_table} existe</p>";

        // Get ALL notifications for this patient
        echo "<hr><h2>Notifications dans la base de données</h2>";
        $this->db->select('*');
        $this->db->from($logs_table);
        $this->db->where('patient_id', $patient->id);
        $this->db->order_by('created_at', 'DESC');
        $notifications = $this->db->get()->result();

        echo "<p class='info'>ℹ️ Nombre total de notifications pour patient_id={$patient->id}: <strong>" . count($notifications) . "</strong></p>";

        if (count($notifications) > 0) {
            echo "<table>";
            echo "<tr><th>ID</th><th>Type</th><th>Message</th><th>Channel</th><th>Status</th><th>Date</th></tr>";
            foreach ($notifications as $notif) {
                echo "<tr>";
                echo "<td>{$notif->id}</td>";
                echo "<td>{$notif->notification_type}</td>";
                echo "<td>" . substr($notif->message, 0, 100) . (strlen($notif->message) > 100 ? '...' : '') . "</td>";
                echo "<td>{$notif->channel}</td>";
                echo "<td>" . ($notif->status ?? '-') . "</td>";
                echo "<td>{$notif->created_at}</td>";
                echo "</tr>";
            }
            echo "</table>";

            echo "<hr><h2>JSON brut retourné par l'API</h2>";
            echo "<p>Voici ce que l'API get_notifications retourne :</p>";

            // Simulate what get_notifications would return
            $formatted_notifications = [];
            foreach ($notifications as $notification) {
                $type = $notification->notification_type ?? 'info';
                $formatted_notifications[] = [
                    'id' => $notification->id,
                    'type' => $type,
                    'title' => $this->get_notification_title($type),
                    'message' => $notification->message ?? '',
                    'icon' => $this->get_notification_icon($type),
                    'url' => null,
                    'is_read' => false,
                    'time_ago' => $this->time_ago($notification->created_at),
                    'created_at' => $notification->created_at
                ];
            }

            $api_response = [
                'success' => true,
                'notifications' => $formatted_notifications,
                'unread_count' => count($formatted_notifications),
                'total' => count($formatted_notifications)
            ];

            echo "<pre>" . json_encode($api_response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>";
        } else {
            echo "<p class='error'>❌ Aucune notification trouvée pour ce patient</p>";

            echo "<hr><h2>Vérification : Toutes les notifications dans la table</h2>";
            $this->db->select('patient_id, COUNT(*) as count');
            $this->db->from($logs_table);
            $this->db->group_by('patient_id');
            $all_notifs = $this->db->get()->result();

            if (count($all_notifs) > 0) {
                echo "<p class='info'>Notifications par patient_id:</p>";
                echo "<table><tr><th>Patient ID</th><th>Nombre</th></tr>";
                foreach ($all_notifs as $row) {
                    echo "<tr><td>{$row->patient_id}</td><td>{$row->count}</td></tr>";
                }
                echo "</table>";
            } else {
                echo "<p class='error'>La table est complètement vide (aucune notification pour aucun patient)</p>";
            }
        }

        echo "<hr>";
        echo "<p><a href='" . site_url('dietetic/portal') . "'>🏠 Retour au portail</a> | ";
        echo "<a href='" . site_url('dietetic/portal/check_notifications_system') . "'>🔍 Diagnostic système</a></p>";
    }

    /**
     * Create patient_notifications table
     * URL: /dietetic/portal/create_patient_notifications_table
     */
    public function create_patient_notifications_table()
    {
        // Check if client is logged in
        if (!is_client_logged_in()) {
            redirect(site_url('authentication/login'));
        }

        echo "<h1>📊 Création Table Patient Notifications</h1>";
        echo "<style>body{font-family:sans-serif;padding:20px;max-width:800px;margin:0 auto}pre{background:#f5f5f5;padding:10px;border-radius:5px;overflow-x:auto}.ok{color:green;font-weight:bold}.error{color:red;font-weight:bold}.warning{color:orange;font-weight:bold}.info{color:#0066cc;font-weight:bold}hr{margin:30px 0;border:none;border-top:2px solid #ddd}</style>";

        echo "<p>Création de la table <code>tbldietic_patient_notifications</code> pour les notifications in-app...</p>";
        echo "<hr>";

        $table_name = db_prefix() . 'dietic_patient_notifications';

        // Check if table already exists
        if ($this->db->table_exists($table_name)) {
            echo "<div style='background:#fff3cd;border:1px solid #ffc107;padding:20px;border-radius:8px'>";
            echo "<h3 style='color:#856404;margin-top:0'>⚠️ Table Déjà Existante</h3>";
            echo "<p style='color:#856404;margin-bottom:0'>La table <strong>{$table_name}</strong> existe déjà. Aucune action nécessaire.</p>";
            echo "</div>";

            // Show table structure
            $fields = $this->db->field_data($table_name);
            echo "<hr><h3>Structure actuelle:</h3>";
            echo "<table style='width:100%;border-collapse:collapse;margin:15px 0'>";
            echo "<tr style='background:#f7f7f7'><th style='padding:8px;border:1px solid #ddd'>Colonne</th><th style='padding:8px;border:1px solid #ddd'>Type</th></tr>";
            foreach ($fields as $field) {
                echo "<tr><td style='padding:8px;border:1px solid #ddd'>{$field->name}</td><td style='padding:8px;border:1px solid #ddd'>{$field->type}</td></tr>";
            }
            echo "</table>";
        } else {
            // Create table
            $sql = "CREATE TABLE `{$table_name}` (
                `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `patient_id` INT(11) UNSIGNED NOT NULL,
                `notification_type` VARCHAR(50) NOT NULL DEFAULT 'info',
                `title` VARCHAR(255) NOT NULL,
                `message` TEXT NOT NULL,
                `icon` VARCHAR(50) DEFAULT 'fa-bell',
                `url` VARCHAR(500) DEFAULT NULL,
                `is_read` TINYINT(1) NOT NULL DEFAULT 0,
                `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `read_at` DATETIME DEFAULT NULL,
                `deleted_at` DATETIME DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `idx_patient_id` (`patient_id`),
                KEY `idx_is_read` (`is_read`),
                KEY `idx_created_at` (`created_at`),
                KEY `idx_patient_unread` (`patient_id`, `is_read`, `created_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Notifications in-app affichées dans le panneau patient'";

            try {
                $result = $this->db->query($sql);

                if ($result) {
                    echo "<div style='background:#d4edda;border:1px solid #c3e6cb;padding:20px;border-radius:8px'>";
                    echo "<h3 style='color:#155724;margin-top:0'>✅ Table Créée Avec Succès!</h3>";
                    echo "<p style='color:#155724'>La table <strong>{$table_name}</strong> a été créée avec succès.</p>";
                    echo "</div>";

                    log_activity('📊 [MIGRATION] Table ' . $table_name . ' créée avec succès');

                    // Show structure
                    $fields = $this->db->field_data($table_name);
                    echo "<hr><h3>Structure de la table:</h3>";
                    echo "<table style='width:100%;border-collapse:collapse;margin:15px 0'>";
                    echo "<tr style='background:#f7f7f7'><th style='padding:8px;border:1px solid #ddd'>Colonne</th><th style='padding:8px;border:1px solid #ddd'>Type</th></tr>";
                    foreach ($fields as $field) {
                        echo "<tr><td style='padding:8px;border:1px solid #ddd'>{$field->name}</td><td style='padding:8px;border:1px solid #ddd'>{$field->type}</td></tr>";
                    }
                    echo "</table>";

                    echo "<hr>";
                    echo "<div style='background:#e3f2fd;border:1px solid #2196F3;padding:15px;border-radius:8px'>";
                    echo "<h4 style='color:#1976D2;margin-top:0'>💡 Prochaines Étapes</h4>";
                    echo "<ul style='color:#1976D2;margin:10px 0'>";
                    echo "<li>Les notifications s'afficheront maintenant depuis cette nouvelle table</li>";
                    echo "<li>Le système utilisera automatiquement cette table au lieu du fallback</li>";
                    echo "<li>Les fonctions marquer comme lu / supprimer fonctionneront correctement</li>";
                    echo "</ul>";
                    echo "</div>";
                } else {
                    echo "<div style='background:#f8d7da;border:1px solid #f5c6cb;padding:20px;border-radius:8px'>";
                    echo "<h3 style='color:#721c24;margin-top:0'>❌ Erreur de Création</h3>";
                    echo "<p style='color:#721c24'>La requête SQL a échoué. Vérifiez les permissions de la base de données.</p>";
                    echo "</div>";
                }
            } catch (Exception $e) {
                echo "<div style='background:#f8d7da;border:1px solid #f5c6cb;padding:20px;border-radius:8px'>";
                echo "<h3 style='color:#721c24;margin-top:0'>❌ Exception SQL</h3>";
                echo "<p style='color:#721c24'>Erreur: " . $e->getMessage() . "</p>";
                echo "</div>";

                log_activity('❌ [MIGRATION ERROR] ' . $e->getMessage());
            }
        }

        echo "<hr>";
        echo "<p><a href='" . site_url('dietetic/portal/check_notifications_system') . "'>🔍 Diagnostic système</a> | ";
        echo "<a href='" . site_url('dietetic/portal') . "'>🏠 Retour au portail</a></p>";
    }

    /**
     * TEST METHOD: Add demo notifications
     * Access: /dietetic/portal/add_test_notifications
     */
    public function add_test_notifications()
    {
        if (!is_client_logged_in()) {
            show_error('Please login first');
            return;
        }

        // Get patient
        $client_id = get_client_user_id();
        $patient = $this->dietetic_patients_model->get_by_client($client_id);

        if (!$patient) {
            show_error('Patient not found');
            return;
        }

        // Check if notification logs table exists
        if (!$this->db->table_exists(db_prefix() . 'dietic_notification_logs')) {
            show_error('Notifications table does not exist. Please install the notifications system first.');
            return;
        }

        // Add test notifications
        $test_notifications = [
            [
                'patient_id' => $patient->id,
                'notification_type' => 'recommendation_added',
                'message' => 'Votre diététicien a ajouté une recommandation sur vos repas d\'hier.',
                'channel' => 'push',
                'status' => 'sent',
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 hours'))
            ],
            [
                'patient_id' => $patient->id,
                'notification_type' => 'consultation_reminder',
                'message' => 'Rappel: Vous avez une consultation demain à 10h00.',
                'channel' => 'push',
                'status' => 'sent',
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 day'))
            ],
            [
                'patient_id' => $patient->id,
                'notification_type' => 'weight_reminder',
                'message' => 'N\'oubliez pas de soumettre votre pesée hebdomadaire.',
                'channel' => 'push',
                'status' => 'sent',
                'created_at' => date('Y-m-d H:i:s', strtotime('-3 days'))
            ],
            [
                'patient_id' => $patient->id,
                'notification_type' => 'milestone_achieved',
                'message' => 'Félicitations ! Vous avez atteint votre objectif de 5kg perdus !',
                'channel' => 'push',
                'status' => 'sent',
                'created_at' => date('Y-m-d H:i:s', strtotime('-5 days'))
            ],
            [
                'patient_id' => 0, // System notification
                'notification_type' => 'system',
                'message' => 'Nouvelle fonctionnalité disponible dans votre portail !',
                'channel' => 'push',
                'status' => 'sent',
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 week'))
            ]
        ];

        $inserted = 0;
        foreach ($test_notifications as $notification) {
            $result = $this->db->insert(db_prefix() . 'dietic_notification_logs', $notification);
            if ($result) {
                $inserted++;
            }
        }

        echo "<h1>✅ Notifications de test ajoutées</h1>";
        echo "<p>$inserted notifications ont été ajoutées pour le patient ID: {$patient->id}</p>";
        echo "<p><a href='" . site_url('dietetic/portal') . "'>→ Retour au portail</a></p>";
        echo "<p><a href='" . site_url('dietetic/portal/debug_notifications_api') . "'>🔍 Debug: Voir ce que retourne l'API</a></p>";
        echo "<p>Rafraîchissez la page du portail et ouvrez le panel de notifications pour les voir.</p>";
    }

    /**
     * DEBUG METHOD: Show what the API returns
     * Access: /dietetic/portal/debug_notifications_api
     */
    public function debug_notifications_api()
    {
        if (!is_client_logged_in()) {
            show_error('Please login first');
            return;
        }

        // Get patient
        $client_id = get_client_user_id();
        $patient = $this->dietetic_patients_model->get_by_client($client_id);

        if (!$patient) {
            show_error('Patient not found');
            return;
        }

        echo "<h1>🔍 Debug: API get_notifications</h1>";
        echo "<p><strong>Patient ID:</strong> {$patient->id}</p>";
        echo "<p><strong>Client ID:</strong> {$client_id}</p>";
        echo "<hr>";

        // DIAGNOSTIC: Check if patient_notifications table is blocking the fallback
        $table_patient_notif = db_prefix() . 'dietic_patient_notifications';
        if ($this->db->table_exists($table_patient_notif)) {
            $count_patient_notif = $this->db->count_all($table_patient_notif);

            echo "<div style='background: #fff3cd; padding: 20px; border-radius: 8px; border: 2px solid #ffc107; margin-bottom: 20px;'>";
            echo "<h2>⚠️ PROBLÈME DÉTECTÉ</h2>";
            echo "<p><strong>La table <code>{$table_patient_notif}</code> existe</strong> mais contient seulement <strong>{$count_patient_notif}</strong> notification(s).</p>";
            echo "<p>Cette table bloque l'utilisation du fallback vers <code>dietic_notification_logs</code>.</p>";
            echo "<p><strong>Solution :</strong> Supprimer cette table pour forcer le fallback.</p>";

            // Handle the drop action
            if ($this->input->get('action') === 'drop_table' && $this->input->get('confirm') === 'yes') {
                try {
                    $this->db->query("DROP TABLE IF EXISTS {$table_patient_notif}");
                    echo "<div style='background: #d4edda; padding: 15px; margin: 10px 0; border-radius: 5px; border: 2px solid #28a745;'>";
                    echo "<h3 style='color: #155724; margin: 0;'>✅ Table supprimée avec succès !</h3>";
                    echo "<p style='margin: 10px 0 0 0;'>Le système utilise maintenant le fallback. Rafraîchissez votre portail.</p>";
                    echo "</div>";
                    echo "<p><a href='" . site_url('dietetic/portal') . "' style='display: inline-block; padding: 10px 20px; background: #01807B; color: white; text-decoration: none; border-radius: 5px; font-weight: bold;'>🔔 Voir mes notifications sur le portail</a></p>";
                } catch (Exception $e) {
                    echo "<div style='background: #f8d7da; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
                    echo "<p style='color: #721c24; margin: 0;'>❌ Erreur: " . $e->getMessage() . "</p>";
                    echo "</div>";
                }
            } else {
                echo "<p><a href='" . site_url('dietetic/portal/debug_notifications_api') . "?action=drop_table&confirm=yes' style='display: inline-block; padding: 12px 24px; background: #dc3545; color: white; text-decoration: none; border-radius: 5px; font-weight: bold;' onclick='return confirm(\"Êtes-vous sûr de vouloir supprimer la table " . $table_patient_notif . " ?\");'>🗑️ SUPPRIMER LA TABLE ET ACTIVER LE FALLBACK</a></p>";
            }

            echo "</div>";
        } else {
            echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 8px; border: 2px solid #0066cc; margin-bottom: 20px;'>";
            echo "<h2>💡 Recommandation</h2>";
            echo "<p>La table <code>{$table_patient_notif}</code> n'existe pas. Le système utilise le fallback vers <code>dietic_notification_logs</code>.</p>";
            echo "<p><strong>⚠️ Avec le fallback, les notifications marquées comme lues ou supprimées réapparaissent après actualisation.</strong></p>";
            echo "<p><strong>Solution :</strong> Installer la table patient_notifications pour persister l'état des notifications.</p>";
            echo "<p><a href='" . site_url('dietetic/portal/install_patient_notifications') . "' style='display: inline-block; padding: 12px 24px; background: #01807B; color: white; text-decoration: none; border-radius: 5px; font-weight: bold;'>📦 INSTALLER LA TABLE PATIENT_NOTIFICATIONS</a></p>";
            echo "</div>";
        }

        // Check if table exists
        $table_name = db_prefix() . 'dietic_notification_logs';
        $table_exists = $this->db->table_exists($table_name);
        echo "<p><strong>Table {$table_name} exists:</strong> " . ($table_exists ? '✅ Yes' : '❌ No') . "</p>";

        if (!$table_exists) {
            echo "<p style='color: red;'>La table n'existe pas. Installez le système de notifications.</p>";
            return;
        }

        // Count total notifications in table
        $total_in_table = $this->db->count_all($table_name);
        echo "<p><strong>Total notifications in table:</strong> $total_in_table</p>";

        // Count for this patient
        $this->db->where_in('patient_id', [$patient->id, 0]);
        $count_for_patient = $this->db->count_all_results($table_name);
        echo "<p><strong>Notifications for patient {$patient->id} (including system):</strong> $count_for_patient</p>";

        // Execute the actual query used by get_notifications
        $this->db->select('id, patient_id, notification_type, message, channel, status, created_at');
        $this->db->from($table_name);
        $this->db->where_in('patient_id', [$patient->id, 0]);
        $this->db->where('status', 'sent');
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit(50);

        $query = $this->db->get();
        $notifications = $query->result();

        echo "<p><strong>SQL Query:</strong></p>";
        echo "<pre style='background: #f5f5f5; padding: 10px; border: 1px solid #ccc;'>" . $this->db->last_query() . "</pre>";

        echo "<p><strong>Results:</strong> " . count($notifications) . " notifications found</p>";

        if (count($notifications) > 0) {
            echo "<h2>📋 Notifications Raw Data:</h2>";
            echo "<table border='1' cellpadding='5' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr style='background: #01807B; color: white;'>";
            echo "<th>ID</th><th>Patient ID</th><th>Type</th><th>Message</th><th>Channel</th><th>Status</th><th>Created At</th>";
            echo "</tr>";

            foreach ($notifications as $notif) {
                echo "<tr>";
                echo "<td>{$notif->id}</td>";
                echo "<td>{$notif->patient_id}</td>";
                echo "<td>{$notif->notification_type}</td>";
                echo "<td>" . substr($notif->message, 0, 50) . "...</td>";
                echo "<td>{$notif->channel}</td>";
                echo "<td>{$notif->status}</td>";
                echo "<td>{$notif->created_at}</td>";
                echo "</tr>";
            }

            echo "</table>";

            echo "<h2>📦 Formatted JSON (what API returns):</h2>";

            // Format as the API does
            $formatted_notifications = [];
            foreach ($notifications as $notification) {
                $type = $notification->notification_type ?? 'info';
                $formatted_notifications[] = [
                    'id' => $notification->id,
                    'type' => $type,
                    'title' => $this->get_notification_title($type),
                    'message' => $notification->message ?? '',
                    'icon' => $this->get_notification_icon($type),
                    'url' => null,
                    'is_read' => false,
                    'time_ago' => $this->time_ago($notification->created_at),
                    'created_at' => $notification->created_at
                ];
            }

            $api_response = [
                'success' => true,
                'notifications' => $formatted_notifications,
                'unread_count' => count($formatted_notifications),
                'total' => count($formatted_notifications)
            ];

            echo "<pre style='background: #f5f5f5; padding: 10px; border: 1px solid #ccc; max-height: 400px; overflow: auto;'>";
            echo json_encode($api_response, JSON_PRETTY_PRINT);
            echo "</pre>";
        } else {
            echo "<p style='color: red;'><strong>Aucune notification trouvée !</strong></p>";
            echo "<p>Vérifiez que des notifications existent pour le patient ID: {$patient->id}</p>";
        }

        echo "<hr>";
        echo "<p><a href='" . site_url('dietetic/portal/add_test_notifications') . "'>➕ Ajouter des notifications de test</a></p>";
        echo "<p><a href='" . site_url('dietetic/portal/check_current_user') . "'>👤 Vérifier quel utilisateur est connecté</a></p>";
        echo "<p><a href='" . site_url('dietetic/portal') . "'>🏠 Retour au portail</a></p>";
    }

    /**
     * DEBUG: Check current logged in user
     * Access: /dietetic/portal/check_current_user
     */
    public function check_current_user()
    {
        echo "<h1>🔍 Diagnostic: Utilisateur Connecté</h1>";

        if (!is_client_logged_in()) {
            echo "<div style='background: #f8d7da; padding: 20px; border-radius: 8px; border: 2px solid #dc3545;'>";
            echo "<h2>🚫 Non connecté</h2>";
            echo "<p>Vous n'êtes pas connecté au portail patient.</p>";
            echo "<p><a href='" . site_url('authentication/login') . "' style='display: inline-block; padding: 10px 20px; background: #01807B; color: white; text-decoration: none; border-radius: 5px;'>→ Se connecter</a></p>";
            echo "</div>";
            return;
        }

        $client_id = get_client_user_id();
        $patient = $this->dietetic_patients_model->get_by_client($client_id);

        echo "<div style='background: #d4edda; padding: 20px; border-radius: 8px; border: 2px solid #28a745; margin-bottom: 20px;'>";
        echo "<h2>✅ Vous êtes connecté</h2>";
        echo "<p><strong>Client ID:</strong> {$client_id}</p>";

        if ($patient) {
            echo "<p><strong>Patient ID:</strong> {$patient->id}</p>";

            // Count notifications for this patient
            $this->db->where_in('patient_id', [$patient->id, 0]);
            $this->db->where('status', 'sent');
            $notif_count = $this->db->count_all_results(db_prefix() . 'dietic_notification_logs');

            echo "<p><strong>Notifications disponibles:</strong> {$notif_count}</p>";

            if ($notif_count > 0) {
                echo "<p style='color: green; font-weight: bold;'>✅ Des notifications existent pour votre compte !</p>";
                echo "<p>Si vous ne les voyez pas dans le panel, rafraîchissez la page du portail.</p>";
            } else {
                echo "<p style='color: orange;'>⚠️ Aucune notification n'existe encore pour votre compte.</p>";
                echo "<p><a href='" . site_url('dietetic/portal/add_test_notifications') . "' style='display: inline-block; padding: 10px 20px; background: #01807B; color: white; text-decoration: none; border-radius: 5px; margin-top: 10px;'>➕ Créer des notifications de test</a></p>";
            }
        } else {
            echo "<p style='color: red;'><strong>❌ Aucun patient trouvé pour ce client</strong></p>";
            echo "<p>Votre compte client (ID: {$client_id}) n'est pas lié à un dossier patient diététique.</p>";
        }
        echo "</div>";

        // Show patient 1 info if different
        $patient_1 = $this->db->select('p.id, p.client_id, c.email, c.company')
                              ->from(db_prefix() . 'dietic_patients p')
                              ->join(db_prefix() . 'clients c', 'c.userid = p.client_id')
                              ->where('p.id', 1)
                              ->get()
                              ->row();

        if ($patient_1 && (!$patient || $patient->id != 1)) {
            echo "<div style='background: #fff3cd; padding: 20px; border-radius: 8px; border: 2px solid #ffc107;'>";
            echo "<h2>ℹ️ Information: Patient ID 1</h2>";
            echo "<p>Les notifications de test ont été créées pour le <strong>Patient ID 1</strong></p>";
            echo "<p><strong>Client ID:</strong> {$patient_1->client_id}</p>";
            echo "<p><strong>Email:</strong> {$patient_1->email}</p>";
            echo "<p><strong>Nom:</strong> {$patient_1->company}</p>";

            if ($patient && $patient->id != 1) {
                echo "<hr>";
                echo "<p style='color: #856404;'><strong>⚠️ Vous êtes connecté avec le Patient ID {$patient->id}</strong></p>";
                echo "<p>Pour voir les notifications de test, vous devez vous connecter avec l'email: <strong>{$patient_1->email}</strong></p>";
            }
            echo "</div>";
        }

        echo "<hr>";
        echo "<p><a href='" . site_url('dietetic/portal/fix_notifications_table') . "' style='display: inline-block; padding: 10px 20px; background: #ffc107; color: #000; text-decoration: none; border-radius: 5px;'>🔧 Corriger les notifications</a></p>";
        echo "<p><a href='" . site_url('dietetic/portal') . "' style='display: inline-block; padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px;'>🏠 Retour au portail</a></p>";
    }

    /**
     * FIX: Drop patient_notifications table to force fallback to logs
     * Access: /dietetic/portal/fix_notifications_table
     */
    public function fix_notifications_table()
    {
        if (!is_client_logged_in()) {
            show_error('Please login first');
            return;
        }

        echo "<h1>🔧 Correction du Système de Notifications</h1>";

        $table_patient_notif = db_prefix() . 'dietic_patient_notifications';
        $table_logs = db_prefix() . 'dietic_notification_logs';

        // Check if patient_notifications table exists
        if ($this->db->table_exists($table_patient_notif)) {
            echo "<div style='background: #fff3cd; padding: 20px; border-radius: 8px; border: 2px solid #ffc107; margin-bottom: 20px;'>";
            echo "<h2>⚠️ Problème Identifié</h2>";
            echo "<p>La table <code>{$table_patient_notif}</code> existe mais est probablement vide.</p>";
            echo "<p>Cela empêche l'utilisation du fallback vers <code>{$table_logs}</code> qui contient vos notifications.</p>";
            echo "</div>";

            // Count in patient_notifications
            $count_patient_notif = $this->db->count_all($table_patient_notif);
            echo "<p><strong>Notifications dans {$table_patient_notif}:</strong> {$count_patient_notif}</p>";

            // Count in logs
            $this->db->where('status', 'sent');
            $count_logs = $this->db->count_all_results($table_logs);
            echo "<p><strong>Notifications dans {$table_logs}:</strong> {$count_logs}</p>";

            echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 8px; border: 2px solid #0066cc; margin: 20px 0;'>";
            echo "<h2>💡 Solution</h2>";
            echo "<p>Nous allons <strong>supprimer temporairement</strong> la table {$table_patient_notif} pour forcer l'utilisation du fallback.</p>";
            echo "<p>Vos notifications seront alors chargées depuis {$table_logs}.</p>";
            echo "</div>";

            // Drop the table
            echo "<p>Suppression de la table {$table_patient_notif}...</p>";

            try {
                $this->db->query("DROP TABLE IF EXISTS {$table_patient_notif}");

                echo "<div style='background: #d4edda; padding: 20px; border-radius: 8px; border: 2px solid #28a745; margin: 20px 0;'>";
                echo "<h2>✅ Succès !</h2>";
                echo "<p>La table {$table_patient_notif} a été supprimée.</p>";
                echo "<p>Le système utilisera maintenant {$table_logs} comme fallback.</p>";
                echo "<p style='font-weight: bold; color: #155724;'>🎉 Vos notifications devraient maintenant s'afficher !</p>";
                echo "</div>";

                echo "<p><a href='" . site_url('dietetic/portal') . "' style='display: inline-block; padding: 15px 30px; background: #01807B; color: white; text-decoration: none; border-radius: 5px; font-size: 18px; font-weight: bold;'>🔔 Voir mes notifications</a></p>";

            } catch (Exception $e) {
                echo "<div style='background: #f8d7da; padding: 20px; border-radius: 8px; border: 2px solid #dc3545;'>";
                echo "<h2>❌ Erreur</h2>";
                echo "<p>Impossible de supprimer la table: " . $e->getMessage() . "</p>";
                echo "</div>";
            }

        } else {
            echo "<div style='background: #d4edda; padding: 20px; border-radius: 8px; border: 2px solid #28a745;'>";
            echo "<h2>✅ Table Correcte</h2>";
            echo "<p>La table {$table_patient_notif} n'existe pas.</p>";
            echo "<p>Le système utilise déjà {$table_logs} comme fallback.</p>";
            echo "<p>Si les notifications ne s'affichent toujours pas, vérifiez la console navigateur pour les erreurs JavaScript.</p>";
            echo "</div>";
        }

        echo "<hr>";
        echo "<p><a href='" . site_url('dietetic/portal/check_current_user') . "'>👤 Vérifier l'utilisateur connecté</a></p>";
        echo "<p><a href='" . site_url('dietetic/portal/debug_notifications_api') . "'>🔍 Debug API</a></p>";
        echo "<p><a href='" . site_url('dietetic/portal') . "'>🏠 Retour au portail</a></p>";
    }

    /**
     * INSTALL: Create patient_notifications table and migrate data from logs
     * Access: /dietetic/portal/install_patient_notifications
     */
    public function install_patient_notifications()
    {
        if (!is_client_logged_in()) {
            show_error('Please login first');
            return;
        }

        echo "<h1>📦 Installation de la Table patient_notifications</h1>";

        $table_patient_notif = db_prefix() . 'dietic_patient_notifications';
        $table_logs = db_prefix() . 'dietic_notification_logs';

        // Check if logs table exists
        if (!$this->db->table_exists($table_logs)) {
            echo "<div style='background: #f8d7da; padding: 20px; border-radius: 8px; border: 2px solid #dc3545;'>";
            echo "<h2>❌ Erreur</h2>";
            echo "<p>La table source <code>{$table_logs}</code> n'existe pas.</p>";
            echo "</div>";
            return;
        }

        // Check if patient_notifications already exists
        if ($this->db->table_exists($table_patient_notif)) {
            echo "<div style='background: #fff3cd; padding: 20px; border-radius: 8px; border: 2px solid #ffc107; margin-bottom: 20px;'>";
            echo "<h2>⚠️ Table Existante</h2>";
            echo "<p>La table <code>{$table_patient_notif}</code> existe déjà.</p>";

            $count = $this->db->count_all($table_patient_notif);
            echo "<p>Elle contient actuellement <strong>{$count}</strong> notification(s).</p>";

            if ($this->input->get('force') === 'yes') {
                echo "<p style='color: #dc3545;'><strong>Mode FORCE activé : suppression et recréation...</strong></p>";
                $this->db->query("DROP TABLE IF EXISTS {$table_patient_notif}");
                echo "<p>✅ Table supprimée.</p>";
            } else {
                echo "<p><a href='" . site_url('dietetic/portal/install_patient_notifications?force=yes') . "' style='display: inline-block; padding: 10px 20px; background: #dc3545; color: white; text-decoration: none; border-radius: 5px;' onclick='return confirm(\"Êtes-vous sûr ? Cela supprimera toutes les notifications et l\\'état lu/non lu actuel.\");'>🔄 Forcer la réinstallation</a></p>";
                echo "</div>";
                echo "<p><a href='" . site_url('dietetic/portal') . "'>🏠 Retour au portail</a></p>";
                return;
            }
            echo "</div>";
        }

        try {
            // Create patient_notifications table
            echo "<h2>1️⃣ Création de la table {$table_patient_notif}</h2>";

            $sql = "
            CREATE TABLE IF NOT EXISTS `{$table_patient_notif}` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `patient_id` int(11) NOT NULL,
                `notification_type` varchar(100) DEFAULT NULL,
                `title` varchar(255) NOT NULL,
                `message` text NOT NULL,
                `icon` varchar(100) DEFAULT 'fa-bell',
                `url` varchar(500) DEFAULT NULL,
                `is_read` tinyint(1) DEFAULT 0,
                `created_at` datetime NOT NULL,
                `updated_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `patient_id` (`patient_id`),
                KEY `is_read` (`is_read`),
                KEY `created_at` (`created_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ";

            $this->db->query($sql);
            echo "<p style='color: #28a745;'>✅ Table créée avec succès.</p>";

            // Migrate data from logs
            echo "<h2>2️⃣ Migration des données depuis {$table_logs}</h2>";

            // Get current patient
            $client_id = get_client_user_id();
            $patient = $this->dietetic_patients_model->get_by_client($client_id);

            if ($patient) {
                // Get all notifications for this patient from logs
                $this->db->select('id, patient_id, notification_type, message, channel, status, created_at');
                $this->db->from($table_logs);
                $this->db->where_in('patient_id', [$patient->id, 0]); // Include patient and system notifications
                $this->db->where('status', 'sent');
                $this->db->order_by('created_at', 'DESC');

                $notifications = $this->db->get()->result();

                echo "<p>Trouvé <strong>" . count($notifications) . "</strong> notification(s) à migrer.</p>";

                $inserted = 0;
                foreach ($notifications as $notif) {
                    // Determine notification type and title
                    $type = $notif->notification_type ?: 'info';
                    $title = $this->get_notification_title($type);
                    $icon = $this->get_notification_icon($type);

                    // Insert into patient_notifications
                    $data = [
                        'patient_id' => $notif->patient_id,
                        'notification_type' => $type,
                        'title' => $title,
                        'message' => $notif->message,
                        'icon' => $icon,
                        'url' => null,
                        'is_read' => 0, // All unread by default
                        'created_at' => $notif->created_at,
                        'updated_at' => $notif->created_at
                    ];

                    if ($this->db->insert($table_patient_notif, $data)) {
                        $inserted++;
                    }
                }

                echo "<p style='color: #28a745;'>✅ <strong>{$inserted}</strong> notification(s) migrée(s) avec succès.</p>";
            }

            echo "<div style='background: #d4edda; padding: 20px; border-radius: 8px; border: 2px solid #28a745; margin: 20px 0;'>";
            echo "<h2>🎉 Installation Réussie !</h2>";
            echo "<p>La table <code>{$table_patient_notif}</code> a été créée et les données ont été migrées.</p>";
            echo "<p><strong>Les notifications peuvent maintenant être marquées comme lues et supprimées de façon persistante.</strong></p>";
            echo "</div>";

            echo "<p><a href='" . site_url('dietetic/portal') . "' style='display: inline-block; padding: 15px 30px; background: #01807B; color: white; text-decoration: none; border-radius: 5px; font-size: 18px; font-weight: bold;'>🔔 Voir mes notifications</a></p>";

        } catch (Exception $e) {
            echo "<div style='background: #f8d7da; padding: 20px; border-radius: 8px; border: 2px solid #dc3545;'>";
            echo "<h2>❌ Erreur</h2>";
            echo "<p>Une erreur est survenue lors de l'installation : " . $e->getMessage() . "</p>";
            echo "</div>";
        }

        echo "<hr>";
        echo "<p><a href='" . site_url('dietetic/portal/debug_notifications_api') . "'>🔍 Debug API</a></p>";
        echo "<p><a href='" . site_url('dietetic/portal') . "'>🏠 Retour au portail</a></p>";
    }

    // =====================================
    // RECIPE LIBRARY METHODS
    // =====================================

    /**
     * Liste des recettes disponibles pour le patient
     */
    public function recipes()
    {
        if (!is_client_logged_in()) {
            redirect(site_url('authentication/login'));
            return;
        }

        $this->load->model('dietetic/dietetic_recipes_model');
        $this->load->model('dietetic/dietetic_patients_model');

        // Get patient
        $patient = $this->dietetic_patients_model->get_by_client(get_client_user_id());

        if (!$patient) {
            show_error('Profil patient non trouvé');
            return;
        }

        $data = [];
        $data['title'] = 'Bibliothèque de Recettes';
        $data['patient'] = $patient;

        // Filtres
        $filters = [];
        if ($this->input->get('category')) {
            $filters['category'] = $this->input->get('category');
        }
        if ($this->input->get('search')) {
            $filters['search'] = $this->input->get('search');
        }

        // Get assigned recipes
        $data['assigned_recipes'] = $this->dietetic_recipes_model->get_patient_recipes($patient->id);

        // Get all approved recipes (for discovery)
        $data['all_recipes'] = $this->dietetic_recipes_model->get_approved($filters);

        // Get favorites
        $data['favorites'] = $this->dietetic_recipes_model->get_favorites($patient->id);

        // Get all tags for filtering
        $data['all_tags'] = $this->dietetic_recipes_model->get_all_tags();

        $this->load->view('portal/recipes/list', $data);
    }

    /**
     * Voir les détails d'une recette
     */
    public function recipe_view($recipe_id)
    {
        if (!is_client_logged_in()) {
            redirect(site_url('authentication/login'));
            return;
        }

        $this->load->model('dietetic/dietetic_recipes_model');
        $this->load->model('dietetic/dietetic_patients_model');

        // Get patient
        $patient = $this->dietetic_patients_model->get_by_client(get_client_user_id());

        if (!$patient) {
            show_error('Profil patient non trouvé');
            return;
        }

        // Get recipe (only approved recipes for patients)
        $recipe = $this->dietetic_recipes_model->get($recipe_id, true);

        if (!$recipe) {
            show_404();
            return;
        }

        $data = [];
        $data['title'] = $recipe->name;
        $data['patient'] = $patient;
        $data['recipe'] = $recipe;
        $data['is_favorite'] = $this->dietetic_recipes_model->is_favorite($recipe_id, $patient->id);
        $data['ratings'] = $this->dietetic_recipes_model->get_ratings($recipe_id);

        // Get patient's rating for this recipe
        $data['my_rating'] = null;
        foreach ($data['ratings'] as $rating) {
            if ($rating->patient_id == $patient->id) {
                $data['my_rating'] = $rating;
                break;
            }
        }

        $this->load->view('portal/recipes/view', $data);
    }

    /**
     * Mes recettes favorites
     */
    public function recipes_favorites()
    {
        if (!is_client_logged_in()) {
            redirect(site_url('authentication/login'));
            return;
        }

        $this->load->model('dietetic/dietetic_recipes_model');
        $this->load->model('dietetic/dietetic_patients_model');

        // Get patient
        $patient = $this->dietetic_patients_model->get_by_client(get_client_user_id());

        if (!$patient) {
            show_error('Profil patient non trouvé');
            return;
        }

        $data = [];
        $data['title'] = 'Mes Recettes Favorites';
        $data['patient'] = $patient;
        $data['favorites'] = $this->dietetic_recipes_model->get_favorites($patient->id);

        $this->load->view('portal/recipes/favorites', $data);
    }

    /**
     * Noter une recette (AJAX)
     */
    public function recipe_rate()
    {
        if (!is_client_logged_in()) {
            echo json_encode(['success' => false, 'message' => 'Non authentifié']);
            return;
        }

        $this->load->model('dietetic/dietetic_recipes_model');
        $this->load->model('dietetic/dietetic_patients_model');

        $recipe_id = $this->input->post('recipe_id');
        $rating = $this->input->post('rating');
        $comment = $this->input->post('comment');

        if (!$recipe_id || !$rating) {
            echo json_encode(['success' => false, 'message' => 'Données manquantes']);
            return;
        }

        // Get patient
        $patient = $this->dietetic_patients_model->get_by_client(get_client_user_id());

        if (!$patient) {
            echo json_encode(['success' => false, 'message' => 'Profil patient non trouvé']);
            return;
        }

        // Validate rating (1-5)
        $rating = max(1, min(5, (int)$rating));

        $result = $this->dietetic_recipes_model->rate($recipe_id, $patient->id, $rating, $comment);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Note enregistrée avec succès']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'enregistrement']);
        }
    }

    /**
     * Ajouter une recette aux favoris (AJAX)
     */
    public function add_to_favorites()
    {
        if (!is_client_logged_in()) {
            echo json_encode(['success' => false, 'message' => 'Non authentifié']);
            return;
        }

        $this->load->model('dietetic/dietetic_recipes_model');
        $this->load->model('dietetic/dietetic_patients_model');

        $recipe_id = $this->input->post('recipe_id');

        if (!$recipe_id) {
            echo json_encode(['success' => false, 'message' => 'ID de recette manquant']);
            return;
        }

        // Get patient
        $patient = $this->dietetic_patients_model->get_by_client(get_client_user_id());

        if (!$patient) {
            echo json_encode(['success' => false, 'message' => 'Profil patient non trouvé']);
            return;
        }

        $result = $this->dietetic_recipes_model->add_to_favorites($recipe_id, $patient->id);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Ajouté aux favoris']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout']);
        }
    }

    /**
     * Retirer une recette des favoris (AJAX)
     */
    public function remove_from_favorites()
    {
        if (!is_client_logged_in()) {
            echo json_encode(['success' => false, 'message' => 'Non authentifié']);
            return;
        }

        $this->load->model('dietetic/dietetic_recipes_model');
        $this->load->model('dietetic/dietetic_patients_model');

        $recipe_id = $this->input->post('recipe_id');

        if (!$recipe_id) {
            echo json_encode(['success' => false, 'message' => 'ID de recette manquant']);
            return;
        }

        // Get patient
        $patient = $this->dietetic_patients_model->get_by_client(get_client_user_id());

        if (!$patient) {
            echo json_encode(['success' => false, 'message' => 'Profil patient non trouvé']);
            return;
        }

        $result = $this->dietetic_recipes_model->remove_from_favorites($recipe_id, $patient->id);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Retiré des favoris']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors du retrait']);
        }
    }

    /**
     * INSTALL: Create recipe library tables
     * Access: /dietetic/portal/install_recipe_library
     */
    public function install_recipe_library()
    {
        if (!is_client_logged_in()) {
            show_error('Please login first');
            return;
        }

        echo "<h1>📚 Installation de la Bibliothèque de Recettes</h1>";
        echo "<p>Cette installation va créer toutes les tables nécessaires pour gérer la bibliothèque de recettes.</p>";

        $tables_created = 0;
        $errors = [];

        try {
            // 1. Table des recettes principales
            echo "<h2>1️⃣ Création de la table des recettes</h2>";
            $sql = "
            CREATE TABLE IF NOT EXISTS `" . db_prefix() . "dietic_recipes` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `dietitian_id` int(11) NOT NULL COMMENT 'ID du diététicien créateur',
                `name` varchar(255) NOT NULL,
                `description` text,
                `preparation_time` int(11) DEFAULT NULL COMMENT 'Temps en minutes',
                `category` varchar(50) DEFAULT NULL COMMENT 'breakfast, lunch, dinner, snack',
                `status` varchar(20) DEFAULT 'pending' COMMENT 'pending, approved, rejected',
                `approved_by_admin_id` int(11) DEFAULT NULL,
                `approved_at` datetime DEFAULT NULL,
                `rejection_reason` text,
                `created_at` datetime NOT NULL,
                `updated_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `dietitian_id` (`dietitian_id`),
                KEY `status` (`status`),
                KEY `category` (`category`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ";
            $this->db->query($sql);
            echo "<p style='color: #28a745;'>✅ Table dietic_recipes créée</p>";
            $tables_created++;

            // 2. Table des ingrédients
            echo "<h2>2️⃣ Création de la table des ingrédients</h2>";
            $sql = "
            CREATE TABLE IF NOT EXISTS `" . db_prefix() . "dietic_recipe_ingredients` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `recipe_id` int(11) NOT NULL,
                `ingredient_name` varchar(255) NOT NULL,
                `quantity` decimal(10,2) DEFAULT NULL,
                `unit` varchar(50) DEFAULT NULL COMMENT 'g, kg, ml, l, cuillère, etc.',
                `order` int(11) DEFAULT 0,
                PRIMARY KEY (`id`),
                KEY `recipe_id` (`recipe_id`),
                CONSTRAINT `fk_recipe_ingredients` FOREIGN KEY (`recipe_id`) REFERENCES `" . db_prefix() . "dietic_recipes` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ";
            $this->db->query($sql);
            echo "<p style='color: #28a745;'>✅ Table dietic_recipe_ingredients créée</p>";
            $tables_created++;

            // 3. Table des instructions
            echo "<h2>3️⃣ Création de la table des instructions</h2>";
            $sql = "
            CREATE TABLE IF NOT EXISTS `" . db_prefix() . "dietic_recipe_instructions` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `recipe_id` int(11) NOT NULL,
                `step_number` int(11) NOT NULL,
                `instruction` text NOT NULL,
                PRIMARY KEY (`id`),
                KEY `recipe_id` (`recipe_id`),
                CONSTRAINT `fk_recipe_instructions` FOREIGN KEY (`recipe_id`) REFERENCES `" . db_prefix() . "dietic_recipes` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ";
            $this->db->query($sql);
            echo "<p style='color: #28a745;'>✅ Table dietic_recipe_instructions créée</p>";
            $tables_created++;

            // 4. Table des valeurs nutritionnelles
            echo "<h2>4️⃣ Création de la table des valeurs nutritionnelles</h2>";
            $sql = "
            CREATE TABLE IF NOT EXISTS `" . db_prefix() . "dietic_recipe_nutrition` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `recipe_id` int(11) NOT NULL,
                `calories` decimal(10,2) DEFAULT NULL COMMENT 'kcal',
                `proteins` decimal(10,2) DEFAULT NULL COMMENT 'g',
                `carbs` decimal(10,2) DEFAULT NULL COMMENT 'g',
                `fats` decimal(10,2) DEFAULT NULL COMMENT 'g',
                `fiber` decimal(10,2) DEFAULT NULL COMMENT 'g',
                `sodium` decimal(10,2) DEFAULT NULL COMMENT 'mg',
                `sugar` decimal(10,2) DEFAULT NULL COMMENT 'g',
                PRIMARY KEY (`id`),
                KEY `recipe_id` (`recipe_id`),
                CONSTRAINT `fk_recipe_nutrition` FOREIGN KEY (`recipe_id`) REFERENCES `" . db_prefix() . "dietic_recipes` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ";
            $this->db->query($sql);
            echo "<p style='color: #28a745;'>✅ Table dietic_recipe_nutrition créée</p>";
            $tables_created++;

            // 5. Table des photos
            echo "<h2>5️⃣ Création de la table des photos</h2>";
            $sql = "
            CREATE TABLE IF NOT EXISTS `" . db_prefix() . "dietic_recipe_photos` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `recipe_id` int(11) NOT NULL,
                `file_path` varchar(500) NOT NULL,
                `is_primary` tinyint(1) DEFAULT 0,
                `uploaded_at` datetime NOT NULL,
                PRIMARY KEY (`id`),
                KEY `recipe_id` (`recipe_id`),
                CONSTRAINT `fk_recipe_photos` FOREIGN KEY (`recipe_id`) REFERENCES `" . db_prefix() . "dietic_recipes` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ";
            $this->db->query($sql);
            echo "<p style='color: #28a745;'>✅ Table dietic_recipe_photos créée</p>";
            $tables_created++;

            // 6. Table des tags
            echo "<h2>6️⃣ Création de la table des tags</h2>";
            $sql = "
            CREATE TABLE IF NOT EXISTS `" . db_prefix() . "dietic_recipe_tags` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `recipe_id` int(11) NOT NULL,
                `tag_name` varchar(100) NOT NULL COMMENT 'végétarien, sans gluten, etc.',
                PRIMARY KEY (`id`),
                KEY `recipe_id` (`recipe_id`),
                KEY `tag_name` (`tag_name`),
                CONSTRAINT `fk_recipe_tags` FOREIGN KEY (`recipe_id`) REFERENCES `" . db_prefix() . "dietic_recipes` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ";
            $this->db->query($sql);
            echo "<p style='color: #28a745;'>✅ Table dietic_recipe_tags créée</p>";
            $tables_created++;

            // 7. Table des assignations aux patients
            echo "<h2>7️⃣ Création de la table des assignations</h2>";
            $sql = "
            CREATE TABLE IF NOT EXISTS `" . db_prefix() . "dietic_recipe_assignments` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `recipe_id` int(11) NOT NULL,
                `patient_id` int(11) NOT NULL,
                `assigned_by_dietitian_id` int(11) NOT NULL,
                `assigned_at` datetime NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `unique_assignment` (`recipe_id`, `patient_id`),
                KEY `recipe_id` (`recipe_id`),
                KEY `patient_id` (`patient_id`),
                CONSTRAINT `fk_recipe_assignments` FOREIGN KEY (`recipe_id`) REFERENCES `" . db_prefix() . "dietic_recipes` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ";
            $this->db->query($sql);
            echo "<p style='color: #28a745;'>✅ Table dietic_recipe_assignments créée</p>";
            $tables_created++;

            // 8. Table des notes et commentaires
            echo "<h2>8️⃣ Création de la table des notes et commentaires</h2>";
            $sql = "
            CREATE TABLE IF NOT EXISTS `" . db_prefix() . "dietic_recipe_ratings` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `recipe_id` int(11) NOT NULL,
                `patient_id` int(11) NOT NULL,
                `rating` tinyint(1) NOT NULL COMMENT '1-5 étoiles',
                `comment` text,
                `created_at` datetime NOT NULL,
                `updated_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `unique_rating` (`recipe_id`, `patient_id`),
                KEY `recipe_id` (`recipe_id`),
                KEY `patient_id` (`patient_id`),
                CONSTRAINT `fk_recipe_ratings` FOREIGN KEY (`recipe_id`) REFERENCES `" . db_prefix() . "dietic_recipes` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ";
            $this->db->query($sql);
            echo "<p style='color: #28a745;'>✅ Table dietic_recipe_ratings créée</p>";
            $tables_created++;

            // 9. Table des favoris
            echo "<h2>9️⃣ Création de la table des favoris</h2>";
            $sql = "
            CREATE TABLE IF NOT EXISTS `" . db_prefix() . "dietic_recipe_favorites` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `recipe_id` int(11) NOT NULL,
                `patient_id` int(11) NOT NULL,
                `created_at` datetime NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `unique_favorite` (`recipe_id`, `patient_id`),
                KEY `recipe_id` (`recipe_id`),
                KEY `patient_id` (`patient_id`),
                CONSTRAINT `fk_recipe_favorites` FOREIGN KEY (`recipe_id`) REFERENCES `" . db_prefix() . "dietic_recipes` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ";
            $this->db->query($sql);
            echo "<p style='color: #28a745;'>✅ Table dietic_recipe_favorites créée</p>";
            $tables_created++;

            echo "<div style='background: #d4edda; padding: 20px; border-radius: 8px; border: 2px solid #28a745; margin: 20px 0;'>";
            echo "<h2>🎉 Installation Réussie !</h2>";
            echo "<p><strong>{$tables_created}</strong> tables ont été créées avec succès.</p>";
            echo "<ul style='text-align: left;'>";
            echo "<li>✅ Recettes</li>";
            echo "<li>✅ Ingrédients</li>";
            echo "<li>✅ Instructions</li>";
            echo "<li>✅ Valeurs nutritionnelles</li>";
            echo "<li>✅ Photos</li>";
            echo "<li>✅ Tags</li>";
            echo "<li>✅ Assignations aux patients</li>";
            echo "<li>✅ Notes et commentaires</li>";
            echo "<li>✅ Favoris</li>";
            echo "</ul>";
            echo "<p>La bibliothèque de recettes est maintenant prête à être utilisée !</p>";
            echo "</div>";

        } catch (Exception $e) {
            echo "<div style='background: #f8d7da; padding: 20px; border-radius: 8px; border: 2px solid #dc3545;'>";
            echo "<h2>❌ Erreur</h2>";
            echo "<p>Une erreur est survenue lors de l'installation : " . $e->getMessage() . "</p>";
            echo "</div>";
        }

        echo "<hr>";
        echo "<p><a href='" . site_url('dietetic/portal') . "'>🏠 Retour au portail</a></p>";
    }
}

