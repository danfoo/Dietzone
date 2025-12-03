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
        $this->load->model('dietetic/dietetic_food_surveys_model');
        $this->load->model('dietetic/dietetic_daily_tracking_model');
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
            'consultation',
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
            // Audio notes methods
            'upload_audio_note',
            'get_audio_notes',
            'delete_audio_note',
            // Recommendation audio notes methods
            'upload_recommendation_audio_response',
            'get_recommendation_audio_notes',
            'delete_recommendation_audio_response',
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
            'remove_from_favorites',
            // Legal pages
            'privacy',
            'terms',
            // Blog methods
            'blog',
            'blog_article',
            'blog_search',
            // Gamification
            'achievements',
            'gamification_diagnostic',
            'test_api_php',
            // Profile methods
            'profile',
            'update_password',
            'update_profile',
            'update_emergency_contact',
            'upload_document',
            'upload_profile_photo',
            'download_document',
            // Daily tracking API methods
            'api_get_daily_tracking',
            'api_update_water',
            'api_toggle_meal',
            'api_update_activity',
            'api_update_calories',
            'api_get_streak',
            // Statistics and evolution
            'statistics',
            'api_get_evolution_data',
            'api_add_statistic_note',
            'api_delete_statistic_note',
            'api_get_calorie_goal',
            'api_get_calorie_goal_debug',
            // Hydration tracking API methods
            'api_get_hydration_data',
            'api_add_hydration',
            'api_update_hydration_goal',
            // Activity tracking methods
            'activities',
            'get_activities',
            'get_my_activities',
            'add_activity',
            'delete_activity',
            'test_activity_post',
            'api_get_today_activities',
            // Invoice and payment methods
            'invoices',
            'invoice',
            // Subscription management methods
            'subscriptions',
            'subscription',
            // Meal reminders migration
            'add_meal_reminders_columns',
            // Diagnostic tools
            'diagnostic_notifications',
            'diagnostic_system',
            'test_notification_manual',
            'migrate_notifications_to_patient_table',
            'debug_meal_reminder',
            'check_cron_execution',
            'check_perfex_cron',
            'test_cron_complete'
        ];

        // If method doesn't exist, treat it as index with the method name as a parameter
        if (!in_array($method, $valid_methods)) {
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

    /**
     * Check if gamification system is ready to use
     *
     * @return bool True if gamification tables exist and model can be loaded
     */
    private function is_gamification_ready()
    {
        try {
            // Check if all required tables exist
            $required_tables = [
                db_prefix() . 'dietic_badge_definitions',
                db_prefix() . 'dietic_patient_points',
                db_prefix() . 'dietic_patient_badges',
                db_prefix() . 'dietic_points_history'
            ];

            foreach ($required_tables as $table) {
                if (!$this->db->table_exists($table)) {
                    return false;
                }
            }

            // Try to load the model
            $this->load->model('dietetic/dietetic_gamification_model');
            return true;
        } catch (Exception $e) {
            log_activity('Gamification readiness check failed: ' . $e->getMessage());
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

        // ========================================
        // MISE À JOUR AUTOMATIQUE DES STATUTS
        // ========================================
        // Mettre à jour automatiquement les programmes expirés
        try {
            $this->dietetic_programs_model->update_expired_programs();
        } catch (Exception $e) {
            log_activity('Error updating expired programs: ' . $e->getMessage());
        }

        // Mettre à jour automatiquement les enquêtes alimentaires expirées
        try {
            if ($this->db->table_exists(db_prefix() . 'dietic_food_surveys')) {
                $this->load->model('dietetic/dietetic_food_surveys_model');
                $this->dietetic_food_surveys_model->update_expired_surveys();
            }
        } catch (Exception $e) {
            log_activity('Error updating expired food surveys: ' . $e->getMessage());
        }

        // Mettre à jour automatiquement les consultations passées
        try {
            $this->dietetic_consultations_model->update_past_consultations();
        } catch (Exception $e) {
            log_activity('Error updating past consultations: ' . $e->getMessage());
        }

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

        // Get daily tracking data
        try {
            if ($this->db->table_exists(db_prefix() . 'dietic_daily_tracking')) {
                $data['daily_tracking'] = $this->dietetic_daily_tracking_model->get_today($patient->id);
                $data['tracking_streak'] = $this->dietetic_daily_tracking_model->calculate_streak($patient->id);
                $data['tracking_completion'] = $this->dietetic_daily_tracking_model->get_completion_percentage($patient->id);
            } else {
                // Table doesn't exist yet, set default values
                $data['daily_tracking'] = (object)[
                    'water_glasses' => 0,
                    'breakfast_checked' => 0,
                    'lunch_checked' => 0,
                    'dinner_checked' => 0,
                    'activity_minutes' => 0,
                    'calories_consumed' => null
                ];
                $data['tracking_streak'] = 0;
                $data['tracking_completion'] = 0;
            }
        } catch (Exception $e) {
            log_activity('Error loading daily tracking: ' . $e->getMessage());
            $data['daily_tracking'] = (object)[
                'water_glasses' => 0,
                'breakfast_checked' => 0,
                'lunch_checked' => 0,
                'dinner_checked' => 0,
                'activity_minutes' => 0,
                'calories_consumed' => null
            ];
            $data['tracking_streak'] = 0;
            $data['tracking_completion'] = 0;
        }

        // Get recent blog articles for carousel (if blog enabled)
        try {
            if ($this->db->table_exists(db_prefix() . 'dietic_blog_articles')) {
                $this->load->model('dietetic/dietetic_blog_model');
                // Get 6 most recent published articles
                $data['blog_articles'] = $this->dietetic_blog_model->get_published(6, 0);
            } else {
                $data['blog_articles'] = [];
            }
        } catch (Exception $e) {
            $data['blog_articles'] = [];
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
                    'neck' => !empty($this->input->post('neck')) ? floatval($this->input->post('neck')) : null,
                    'chest' => !empty($this->input->post('chest')) ? floatval($this->input->post('chest')) : null,
                    'arms' => !empty($this->input->post('arms')) ? floatval($this->input->post('arms')) : null,
                    'thighs' => !empty($this->input->post('thighs')) ? floatval($this->input->post('thighs')) : null,
                    'calf' => !empty($this->input->post('calf')) ? floatval($this->input->post('calf')) : null,
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

        // Get ALL programs (active, completed, cancelled) for displaying history
        try {
            $all_programs = $this->dietetic_programs_model->get_all_by_patient($patient->id);
            $data['programs'] = $all_programs;

            // Separate active and historical programs
            $data['active_programs'] = [];
            $data['historical_programs'] = [];

            foreach ($all_programs as $program) {
                // Get meal plans for this program
                $program->meal_plans = $this->dietetic_meal_plans_model->get_by_program($program->id);

                if ($program->status === 'active') {
                    $data['active_programs'][] = $program;
                } else {
                    $data['historical_programs'][] = $program;
                }
            }

            // Keep backward compatibility - set the first active program as active_program
            $data['active_program'] = !empty($data['active_programs']) ? $data['active_programs'][0] : null;
            $data['meal_plans'] = $data['active_program'] ? $data['active_program']->meal_plans : [];
        } catch (Exception $e) {
            $data['programs'] = [];
            $data['active_programs'] = [];
            $data['historical_programs'] = [];
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

        $this->load->view('portal/consultations/index', $data);
    }

    /**
     * View consultation details
     *
     * @param int $id Consultation ID
     */
    public function consultation($id = null)
    {
        if (!is_client_logged_in()) {
            redirect(site_url('authentication/login'));
            return;
        }

        if (!$id) {
            redirect(site_url('dietetic/portal/consultations'));
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

        // Get consultation
        try {
            $consultation = $this->dietetic_consultations_model->get($id);
        } catch (Exception $e) {
            $consultation = null;
        }

        // Verify consultation belongs to this patient
        if (!$consultation || $consultation->patient_id != $patient->id) {
            show_404();
            return;
        }

        $data = [];
        $data['patient'] = $patient;
        $data['consultation'] = $consultation;
        $data['title'] = 'Détails de la Consultation';

        // Get dietitian info
        $this->load->model('staff_model');
        $data['dietitian'] = $this->staff_model->get($consultation->dietitian_id);

        $this->load->view('portal/consultations/view', $data);
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

        // Get all surveys for this patient (active, completed, cancelled)
        $all_surveys = $this->dietetic_food_surveys_model->get_by_patient($patient->id);

        // Separate active and historical surveys
        $data['active_surveys'] = [];
        $data['historical_surveys'] = [];

        foreach ($all_surveys as $survey) {
            // Calculate completion percentage
            $survey->completion_percentage = $this->dietetic_food_surveys_model->get_completion_percentage($survey->id);

            if ($survey->status === 'active') {
                $data['active_surveys'][] = $survey;
            } else {
                $data['historical_surveys'][] = $survey;
            }
        }

        // Keep backward compatibility
        $data['surveys'] = $all_surveys;

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
            'reminder_breakfast' => $this->input->post('reminder_breakfast') ? 1 : 0,
            'reminder_breakfast_time' => $this->input->post('reminder_breakfast_time') ?: '08:00:00',
            'reminder_lunch' => $this->input->post('reminder_lunch') ? 1 : 0,
            'reminder_lunch_time' => $this->input->post('reminder_lunch_time') ?: '12:30:00',
            'reminder_dinner' => $this->input->post('reminder_dinner') ? 1 : 0,
            'reminder_dinner_time' => $this->input->post('reminder_dinner_time') ?: '19:00:00',
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
     * Comprehensive diagnostic page for notification system
     * Tests all aspects: login, patient record, database tables, API calls
     * Access: /dietetic/portal/diagnostic_notifications
     */
    public function diagnostic_notifications()
    {
        // Allow both admin and logged-in clients
        if (!is_staff_logged_in() && !is_client_logged_in()) {
            redirect('authentication/login');
        }

        $data = [];
        $data['title'] = 'Test du Système de Notifications';

        // Test 1: Check if user is logged in
        $data['is_staff_logged_in'] = is_staff_logged_in();
        $data['is_client_logged_in'] = is_client_logged_in();

        // Test 2: Get client ID and patient
        if (is_client_logged_in()) {
            $data['client_id'] = get_client_user_id();

            // Test 3: Get patient
            $patient = $this->dietetic_patients_model->get_by_client($data['client_id']);
            $data['patient'] = $patient;
            $data['patient_exists'] = !empty($patient);

            // Test 4: Check if tables exist
            $data['notification_logs_exists'] = $this->db->table_exists(db_prefix() . 'dietic_notification_logs');
            $data['patient_notifications_exists'] = $this->db->table_exists(db_prefix() . 'dietic_patient_notifications');
            $data['notification_settings_exists'] = $this->db->table_exists(db_prefix() . 'dietic_notification_settings');

            // Test 5: Count notifications in logs table
            if ($data['notification_logs_exists'] && isset($patient)) {
                $this->db->select('COUNT(*) as total');
                $this->db->from(db_prefix() . 'dietic_notification_logs');
                $this->db->where_in('patient_id', [$patient->id, 0]);
                $this->db->where('status', 'sent');
                $query = $this->db->get();
                $result = $query->row();
                $data['notification_logs_count'] = $result->total;

                // Get last SQL query for debugging
                $data['sql_query_logs'] = $this->db->last_query();
            } else {
                $data['notification_logs_count'] = null;
                $data['sql_query_logs'] = null;
            }

            // Test 6: Count notifications in patient_notifications table
            if ($data['patient_notifications_exists'] && isset($patient)) {
                $this->db->select('COUNT(*) as total');
                $this->db->from(db_prefix() . 'dietic_patient_notifications');
                $this->db->where('patient_id', $patient->id);
                $query = $this->db->get();
                $result = $query->row();
                $data['patient_notifications_count'] = $result->total;

                $data['sql_query_patient_notif'] = $this->db->last_query();
            } else {
                $data['patient_notifications_count'] = null;
                $data['sql_query_patient_notif'] = null;
            }

            // Test 7: API endpoint URL
            $data['api_test_url'] = site_url('dietetic/portal/get_notifications');
        } else {
            // Staff logged in - show limited info
            $data['client_id'] = null;
            $data['patient'] = null;
            $data['patient_exists'] = false;

            // Still show table existence
            $data['notification_logs_exists'] = $this->db->table_exists(db_prefix() . 'dietic_notification_logs');
            $data['patient_notifications_exists'] = $this->db->table_exists(db_prefix() . 'dietic_patient_notifications');
            $data['notification_settings_exists'] = $this->db->table_exists(db_prefix() . 'dietic_notification_settings');

            $data['notification_logs_count'] = null;
            $data['patient_notifications_count'] = null;
            $data['sql_query_logs'] = null;
            $data['sql_query_patient_notif'] = null;
            $data['api_test_url'] = null;
        }

        // Load diagnostic view
        $this->load->view('dietetic/portal_diagnostic_notifications', $data);
    }

    /**
     * Diagnostic complet du système de notifications
     * Access: /dietetic/portal/diagnostic_system
     * Accessible par admin et patients connectés
     */
    public function diagnostic_system()
    {
        // Allow both admin and logged-in clients
        if (!is_staff_logged_in() && !is_client_logged_in()) {
            redirect('authentication/login');
        }

        // Run diagnostics
        $diagnostic = [];
        $errors = [];
        $warnings = [];
        $success = [];

        try {
            // 1. Check module activation
            $this->db->select('*');
            $this->db->from(db_prefix() . 'modules');
            $this->db->where('module_name', 'dietetic');
            $query = $this->db->get();
            $module = $query->num_rows() > 0 ? $query->row() : null;
            $diagnostic['module_active'] = ($module && $module->active == 1);

            if ($diagnostic['module_active']) {
                $success[] = "Module Dietetic activé";
            } else {
                $errors[] = "Module Dietetic NON activé - Allez dans Admin > Modules pour l'activer";
            }

            // 2. Check cron
            $this->db->select('*');
            $this->db->from(db_prefix() . 'options');
            $this->db->where('name', 'last_cron_run');
            $query = $this->db->get();
            $cron = $query->num_rows() > 0 ? $query->row() : null;
            $diagnostic['last_cron_run'] = $cron ? $cron->value : null;
            $diagnostic['cron_minutes_ago'] = $diagnostic['last_cron_run'] ? floor((time() - $diagnostic['last_cron_run']) / 60) : null;

            if ($diagnostic['cron_minutes_ago'] === null) {
                $errors[] = "Cron jamais exécuté";
            } elseif ($diagnostic['cron_minutes_ago'] > 10) {
                $warnings[] = "Cron inactif depuis " . $diagnostic['cron_minutes_ago'] . " minutes";
            } else {
                $success[] = "Cron actif (il y a " . $diagnostic['cron_minutes_ago'] . " min)";
            }

            // 3. Check patients with preferences
            if ($this->db->table_exists(db_prefix() . 'dietic_patients')) {
                $this->db->select('COUNT(*) as total');
                $this->db->from(db_prefix() . 'dietic_patients');
                $query = $this->db->get();
                $diagnostic['total_patients'] = $query->row()->total;
            } else {
                $diagnostic['total_patients'] = 0;
                $warnings[] = "Table dietic_patients n'existe pas";
            }

            if ($this->db->table_exists(db_prefix() . 'dietic_notification_preferences')) {
                $this->db->select('COUNT(*) as total');
                $this->db->from(db_prefix() . 'dietic_notification_preferences');
                $query = $this->db->get();
                $diagnostic['patients_with_prefs'] = $query->row()->total;

                if ($diagnostic['patients_with_prefs'] == 0) {
                    $warnings[] = "Aucun patient avec préférences configurées";
                } else {
                    $success[] = $diagnostic['patients_with_prefs'] . " patient(s) avec préférences";
                }
            } else {
                $diagnostic['patients_with_prefs'] = 0;
                $warnings[] = "Table dietic_notification_preferences n'existe pas";
            }

            // 4. Get patients with dinner reminder
            $diagnostic['dinner_patients'] = [];
            if ($this->db->table_exists(db_prefix() . 'dietic_notification_preferences') &&
                $this->db->table_exists(db_prefix() . 'dietic_patients')) {

                $this->db->select('p.*, dp.email, dp.phone, c.firstname, c.lastname');
                $this->db->from(db_prefix() . 'dietic_notification_preferences p');
                $this->db->join(db_prefix() . 'dietic_patients dp', 'p.patient_id = dp.id');
                $this->db->join(db_prefix() . 'contacts c', 'dp.client_id = c.userid AND c.is_primary = 1', 'left');
                $this->db->where('p.reminder_dinner', 1);
                $this->db->limit(10);

                try {
                    $query = $this->db->get();
                    if ($query->num_rows() > 0) {
                        $diagnostic['dinner_patients'] = $query->result_array();
                        $success[] = count($diagnostic['dinner_patients']) . " patient(s) avec rappel dîner";
                    } else {
                        $warnings[] = "Aucun patient avec rappel dîner activé";
                    }
                } catch (Exception $e) {
                    $warnings[] = "Aucun patient avec rappel dîner activé (erreur de requête)";
                }
            }

            // 5. Check channel configuration
            $settings = [];
            if ($this->db->table_exists(db_prefix() . 'dietic_notification_settings')) {
                $this->db->select('*');
                $this->db->from(db_prefix() . 'dietic_notification_settings');
                $this->db->where_in('setting_key', ['sms_lam_account_id', 'sms_lam_password', 'whatsapp_api_key']);
                $query = $this->db->get();

                if ($query->num_rows() > 0) {
                    foreach ($query->result() as $row) {
                        $settings[$row->setting_key] = !empty($row->setting_value);
                    }
                }
            }

            $this->db->select('*');
            $this->db->from(db_prefix() . 'options');
            $this->db->where_in('name', ['smtp_host', 'smtp_username']);
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                foreach ($query->result() as $row) {
                    $settings[$row->name] = !empty($row->value);
                }
            }

            $diagnostic['smtp_configured'] = isset($settings['smtp_host']) && isset($settings['smtp_username']);
            $diagnostic['sms_configured'] = isset($settings['sms_lam_account_id']) && isset($settings['sms_lam_password']);
            $diagnostic['whatsapp_configured'] = isset($settings['whatsapp_api_key']);

            // 6. Get recent notifications
            $diagnostic['recent_notifications'] = [];
            if ($this->db->table_exists(db_prefix() . 'dietic_patient_notifications')) {
                $this->db->select('*');
                $this->db->from(db_prefix() . 'dietic_patient_notifications');
                $this->db->order_by('created_at', 'DESC');
                $this->db->limit(10);
                $query = $this->db->get();

                if ($query->num_rows() > 0) {
                    $diagnostic['recent_notifications'] = $query->result_array();
                }
            }

        } catch (Exception $e) {
            $errors[] = "Erreur lors du diagnostic: " . $e->getMessage();
        }

        // Generate HTML output
        $this->output_diagnostic_html($diagnostic, $success, $warnings, $errors);
    }

    /**
     * Output HTML for diagnostic system
     */
    private function output_diagnostic_html($diagnostic, $success, $warnings, $errors)
    {
        header('Content-Type: text/html; charset=utf-8');
        ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnostic Notifications - Dietetic</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            min-height: 100vh;
        }
        .container { max-width: 1200px; margin: 0 auto; }
        .header {
            background: white;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            text-align: center;
        }
        .header h1 { color: #01807B; font-size: 28px; margin-bottom: 10px; }
        .header .subtitle { color: #666; font-size: 14px; }
        .summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        .summary-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .summary-card.success { background: linear-gradient(135deg, #4caf50, #45a049); color: white; }
        .summary-card.error { background: linear-gradient(135deg, #f44336, #e53935); color: white; }
        .summary-card.warning { background: linear-gradient(135deg, #ff9800, #fb8c00); color: white; }
        .summary-number { font-size: 48px; font-weight: 700; margin-bottom: 5px; }
        .summary-label { font-size: 14px; opacity: 0.9; }
        .card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .card-title {
            font-size: 20px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 3px solid #01807B;
        }
        .status-item {
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 8px;
            border-left: 4px solid;
        }
        .status-item.success { background: #e8f5e9; border-color: #4caf50; }
        .status-item.error { background: #ffebee; border-color: #f44336; }
        .status-item.warning { background: #fff3e0; border-color: #ff9800; }
        .status-label { font-weight: 600; font-size: 16px; margin-bottom: 5px; }
        .status-detail { font-size: 14px; color: #666; }
        .patient-card {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 10px;
            border-left: 4px solid #01807B;
        }
        .patient-name { font-weight: 700; color: #01807B; margin-bottom: 8px; }
        .patient-info { font-size: 13px; color: #666; margin-bottom: 4px; }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            margin-right: 5px;
        }
        .badge.success { background: #4caf50; color: white; }
        .badge.error { background: #f44336; color: white; }
        .recommendation-box {
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            border: 2px solid #2196f3;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }
        .recommendation-box.success {
            background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
            border-color: #4caf50;
        }
        .recommendation-title {
            font-weight: 700;
            color: #1565c0;
            margin-bottom: 15px;
            font-size: 18px;
        }
        .recommendation-title.success { color: #2e7d32; }
        ul { margin-left: 20px; }
        li { margin-bottom: 8px; color: #1976d2; }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background: #01807B;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 10px 5px;
        }
        .button:hover { background: #01605B; }
        .actions { text-align: center; margin-top: 30px; }
        @media (max-width: 768px) {
            .summary { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔍 Diagnostic des Notifications</h1>
            <div class="subtitle">
                app.dietsenegal.net • <?php echo date('d/m/Y H:i:s'); ?>
            </div>
        </div>

        <!-- Summary -->
        <div class="summary">
            <div class="summary-card <?php echo count($success) > 0 ? 'success' : 'error'; ?>">
                <div class="summary-number"><?php echo count($success); ?></div>
                <div class="summary-label">✅ Points Positifs</div>
            </div>
            <div class="summary-card <?php echo count($warnings) > 0 ? 'warning' : 'success'; ?>">
                <div class="summary-number"><?php echo count($warnings); ?></div>
                <div class="summary-label">⚠️ Avertissements</div>
            </div>
            <div class="summary-card <?php echo count($errors) > 0 ? 'error' : 'success'; ?>">
                <div class="summary-number"><?php echo count($errors); ?></div>
                <div class="summary-label">❌ Erreurs</div>
            </div>
        </div>

        <!-- Module Status -->
        <div class="card">
            <div class="card-title">📦 État du Module</div>
            <div class="status-item <?php echo $diagnostic['module_active'] ? 'success' : 'error'; ?>">
                <div class="status-label"><?php echo $diagnostic['module_active'] ? '✅' : '❌'; ?> Module Dietetic</div>
                <div class="status-detail">
                    <?php if ($diagnostic['module_active']): ?>
                        Le module est activé et opérationnel
                    <?php else: ?>
                        <strong>❌ Le module n'est PAS activé !</strong><br>
                        Action : Allez dans <a href="<?php echo admin_url('modules'); ?>" target="_blank" style="color: #01807B; font-weight: 600;">Admin > Modules</a> et cliquez sur "Activate"
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Cron Status -->
        <div class="card">
            <div class="card-title">⏰ État du Cron</div>
            <?php if ($diagnostic['last_cron_run']): ?>
                <div class="status-item <?php echo $diagnostic['cron_minutes_ago'] < 10 ? 'success' : 'warning'; ?>">
                    <div class="status-label"><?php echo $diagnostic['cron_minutes_ago'] < 10 ? '✅' : '⚠️'; ?> Cron Perfex</div>
                    <div class="status-detail">
                        Dernière exécution : il y a <strong><?php echo $diagnostic['cron_minutes_ago']; ?></strong> minute(s)<br>
                        <small><?php echo date('d/m/Y H:i:s', $diagnostic['last_cron_run']); ?></small>
                        <?php if ($diagnostic['cron_minutes_ago'] > 10): ?>
                            <br><strong style="color: #f57c00;">⚠️ Le cron devrait s'exécuter toutes les 5 minutes !</strong>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="status-item error">
                    <div class="status-label">❌ Cron Non Configuré</div>
                    <div class="status-detail">Le cron n'a jamais été exécuté</div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Patients -->
        <div class="card">
            <div class="card-title">👥 Patients avec Rappel Dîner</div>
            <?php if (count($diagnostic['dinner_patients']) > 0): ?>
                <?php foreach ($diagnostic['dinner_patients'] as $patient): ?>
                    <div class="patient-card">
                        <div class="patient-name">
                            <?php echo htmlspecialchars($patient['firstname'] . ' ' . $patient['lastname']); ?>
                        </div>
                        <div class="patient-info">
                            ⏰ Heure : <strong><?php echo substr($patient['reminder_dinner_time'], 0, 5); ?></strong>
                        </div>
                        <div class="patient-info">
                            📧 Email : <?php echo $patient['email'] ?: '<span style="color: #f44336;">❌ Non renseigné</span>'; ?>
                        </div>
                        <div class="patient-info">
                            📱 Téléphone : <?php echo $patient['phone'] ?: '<span style="color: #f44336;">❌ Non renseigné</span>'; ?>
                        </div>
                        <div style="margin-top: 8px;">
                            <?php if ($patient['channel_email']): ?><span class="badge success">Email</span><?php endif; ?>
                            <?php if ($patient['channel_sms']): ?><span class="badge success">SMS</span><?php endif; ?>
                            <?php if ($patient['channel_whatsapp']): ?><span class="badge success">WhatsApp</span><?php endif; ?>
                            <?php if (!$patient['channel_email'] && !$patient['channel_sms'] && !$patient['channel_whatsapp']): ?>
                                <span class="badge error">❌ Aucun canal activé</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="status-item warning">
                    <div class="status-label">⚠️ Aucun Patient</div>
                    <div class="status-detail">Aucun patient n'a activé le rappel de dîner</div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Recommendations -->
        <?php if (count($errors) > 0 || count($warnings) > 0): ?>
        <div class="recommendation-box">
            <div class="recommendation-title">💡 Actions Recommandées</div>
            <?php if (count($errors) > 0): ?>
                <strong style="color: #d32f2f;">🚨 ERREURS CRITIQUES :</strong>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li style="color: #d32f2f;"><strong><?php echo $error; ?></strong></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <?php if (count($warnings) > 0): ?>
                <strong style="color: #f57c00;">⚠️ AVERTISSEMENTS :</strong>
                <ul>
                    <?php foreach ($warnings as $warning): ?>
                        <li style="color: #f57c00;"><?php echo $warning; ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <div class="recommendation-box success">
            <div class="recommendation-title success">✅ Tout est en ordre !</div>
            <p style="color: #1b5e20;">
                Votre système est correctement configuré. Les notifications seront envoyées aux heures prévues (± 5 minutes).
            </p>
        </div>
        <?php endif; ?>

        <div class="actions">
            <a href="<?php echo admin_url(); ?>" class="button">🏠 Dashboard Admin</a>
            <a href="<?php echo admin_url('modules'); ?>" class="button">📦 Modules</a>
            <a href="<?php echo site_url('dietetic/portal/diagnostic_system'); ?>" class="button">🔄 Rafraîchir</a>
        </div>

        <div style="text-align: center; margin-top: 30px; color: white; font-size: 12px;">
            Diagnostic Dietetic • <?php echo date('Y'); ?>
        </div>
    </div>
</body>
</html>
        <?php
    }

    /**
     * Test manuel d'envoi de notification
     * Access: /dietetic/portal/test_notification_manual
     * Permet de déclencher manuellement une notification pour déboguer
     */
    public function test_notification_manual()
    {
        // Allow both admin and logged-in clients
        if (!is_staff_logged_in() && !is_client_logged_in()) {
            redirect('authentication/login');
        }

        // Load model
        $this->load->model('dietetic/dietetic_notifications_model');

        header('Content-Type: text/html; charset=utf-8');

        echo '<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Notification Manuelle</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            min-height: 100vh;
        }
        .container { max-width: 1200px; margin: 0 auto; }
        .card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .title {
            font-size: 28px;
            font-weight: 700;
            color: #01807B;
            margin-bottom: 20px;
            text-align: center;
        }
        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: #2c3e50;
            margin: 20px 0 10px 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #01807B;
        }
        pre {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 8px;
            overflow-x: auto;
            font-size: 13px;
            border-left: 4px solid #01807B;
        }
        .success {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #4caf50;
            margin: 10px 0;
        }
        .error {
            background: #ffebee;
            color: #c62828;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #f44336;
            margin: 10px 0;
        }
        .warning {
            background: #fff3e0;
            color: #e65100;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #ff9800;
            margin: 10px 0;
        }
        .info {
            background: #e3f2fd;
            color: #1565c0;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #2196f3;
            margin: 10px 0;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background: #01807B;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 10px 5px;
        }
        .button:hover { background: #01605B; }
        .actions { text-align: center; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="title">🧪 Test Manuel de Notification</div>
            <div class="info">
                <strong>📍 Objectif :</strong> Déclencher manuellement une notification de rappel dîner pour identifier les problèmes.
            </div>';

        echo '<div class="section-title">1️⃣ Récupération des patients avec rappel dîner activé</div>';

        try {
            // Désactiver temporairement la vérification de l'heure pour le test
            // Récupérer les patients avec reminder_dinner = 1
            $this->db->select('p.*, prefs.*, p.email, p.phone, c.firstname, c.lastname');
            $this->db->from(db_prefix() . 'dietic_notification_preferences prefs');
            $this->db->join(db_prefix() . 'dietic_patients p', 'p.id = prefs.patient_id');
            $this->db->join(db_prefix() . 'contacts c', 'c.userid = p.client_id AND c.is_primary = 1', 'left');
            $this->db->where('prefs.reminder_dinner', 1);
            $this->db->limit(1);

            $query = $this->db->get();

            echo '<div class="info"><strong>🔍 Requête SQL :</strong><pre>' . $this->db->last_query() . '</pre></div>';

            if ($query->num_rows() > 0) {
                $patient = $query->row();
                echo '<div class="success">✅ Patient trouvé : ' . htmlspecialchars($patient->firstname . ' ' . $patient->lastname) . '</div>';
                echo '<pre>' . print_r($patient, true) . '</pre>';

                echo '<div class="section-title">2️⃣ Configuration des canaux</div>';
                echo '<div class="info">';
                echo '📧 Email: ' . ($patient->channel_email ? '✅ Activé (' . $patient->email . ')' : '❌ Désactivé') . '<br>';
                echo '📱 SMS: ' . ($patient->channel_sms ? '✅ Activé (' . $patient->phone . ')' : '❌ Désactivé') . '<br>';
                echo '💬 WhatsApp: ' . ($patient->channel_whatsapp ? '✅ Activé' : '❌ Désactivé') . '<br>';
                echo '🔔 Push: ' . ($patient->channel_push ? '✅ Activé' : '❌ Désactivé');
                echo '</div>';

                echo '<div class="section-title">3️⃣ Envoi de la notification de test</div>';

                // Créer un objet patient formaté pour send_meal_reminder
                $test_patient = new stdClass();
                $test_patient->patient_id = $patient->patient_id;
                $test_patient->firstname = $patient->firstname;
                $test_patient->lastname = $patient->lastname;
                $test_patient->email = $patient->email;
                $test_patient->phonenumber = $patient->phone;
                $test_patient->channel_email = $patient->channel_email;
                $test_patient->channel_sms = $patient->channel_sms;
                $test_patient->channel_whatsapp = $patient->channel_whatsapp;
                $test_patient->channel_push = $patient->channel_push;

                echo '<div class="warning">⏳ Envoi en cours...</div>';

                $result = $this->dietetic_notifications_model->send_meal_reminder($test_patient, 'dinner');

                echo '<div class="section-title">4️⃣ Résultat de l\'envoi</div>';

                if (is_array($result)) {
                    $has_success = false;
                    foreach ($result as $channel => $status) {
                        if ($status === true) {
                            echo '<div class="success">✅ ' . ucfirst($channel) . ' : Envoyé avec succès</div>';
                            $has_success = true;
                        } else {
                            echo '<div class="error">❌ ' . ucfirst($channel) . ' : Échec (' . (is_string($status) ? $status : 'erreur inconnue') . ')</div>';
                        }
                    }

                    if ($has_success) {
                        echo '<div class="success"><strong>🎉 Au moins une notification a été envoyée avec succès !</strong></div>';
                    } else {
                        echo '<div class="error"><strong>❌ Aucune notification n\'a pu être envoyée.</strong></div>';
                    }
                } else {
                    echo '<div class="error">❌ Résultat inattendu : <pre>' . print_r($result, true) . '</pre></div>';
                }

                echo '<div class="section-title">5️⃣ Détails complets du résultat</div>';
                echo '<pre>' . print_r($result, true) . '</pre>';

            } else {
                echo '<div class="error">❌ Aucun patient trouvé avec rappel dîner activé.</div>';
                echo '<div class="warning">Vérifiez que vous avez bien activé le rappel dîner dans vos préférences.</div>';
            }

        } catch (Exception $e) {
            echo '<div class="error">❌ ERREUR : ' . htmlspecialchars($e->getMessage()) . '</div>';
            echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
        }

        echo '<div class="actions">
                <a href="' . site_url('dietetic/portal/diagnostic_system') . '" class="button">🔍 Diagnostic Système</a>
                <a href="' . site_url('dietetic/portal/notification_preferences') . '" class="button">⚙️ Préférences</a>
                <a href="' . site_url('dietetic/portal/test_notification_manual') . '" class="button">🔄 Relancer le test</a>
            </div>
        </div>
    </div>
</body>
</html>';
    }

    /**
     * Migrate notifications from logs to patient_notifications table
     * Fixes the issue where notifications exist in logs but not in patient table
     * Access: /dietetic/portal/migrate_notifications_to_patient_table
     */
    public function migrate_notifications_to_patient_table()
    {
        // Allow both admin and logged-in clients
        if (!is_staff_logged_in() && !is_client_logged_in()) {
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            return;
        }

        // Check if both tables exist
        if (!$this->db->table_exists(db_prefix() . 'dietic_notification_logs')) {
            echo json_encode(['success' => false, 'message' => 'Source table (notification_logs) does not exist']);
            return;
        }

        if (!$this->db->table_exists(db_prefix() . 'dietic_patient_notifications')) {
            echo json_encode(['success' => false, 'message' => 'Destination table (patient_notifications) does not exist']);
            return;
        }

        try {
            // Start transaction
            $this->db->trans_start();

            // Build the INSERT SELECT query
            $sql = "
                INSERT INTO `" . db_prefix() . "dietic_patient_notifications` (
                    `patient_id`,
                    `notification_type`,
                    `title`,
                    `message`,
                    `icon`,
                    `url`,
                    `is_read`,
                    `created_at`,
                    `updated_at`
                )
                SELECT
                    nl.`patient_id`,
                    nl.`notification_type`,
                    CASE nl.`notification_type`
                        WHEN 'weight_reminder' THEN 'Rappel de Pesée'
                        WHEN 'water_reminder' THEN 'Rappel d\'Hydratation'
                        WHEN 'recommendation' THEN 'Nouvelle Recommandation'
                        WHEN 'consultation' THEN 'Consultation'
                        WHEN 'milestone' THEN 'Jalon Atteint'
                        WHEN 'program' THEN 'Programme Diététique'
                        WHEN 'food_entry' THEN 'Saisie Alimentaire'
                        WHEN 'test' THEN 'Test Notification'
                        ELSE 'Notification'
                    END as `title`,
                    nl.`message`,
                    CASE nl.`notification_type`
                        WHEN 'weight_reminder' THEN 'fa-balance-scale'
                        WHEN 'water_reminder' THEN 'fa-tint'
                        WHEN 'recommendation' THEN 'fa-comments'
                        WHEN 'consultation' THEN 'fa-calendar'
                        WHEN 'milestone' THEN 'fa-trophy'
                        WHEN 'program' THEN 'fa-leaf'
                        WHEN 'food_entry' THEN 'fa-cutlery'
                        WHEN 'test' THEN 'fa-flask'
                        ELSE 'fa-bell'
                    END as `icon`,
                    NULL as `url`,
                    0 as `is_read`,
                    nl.`created_at`,
                    NOW() as `updated_at`
                FROM `" . db_prefix() . "dietic_notification_logs` nl
                WHERE nl.`patient_id` > 0
                  AND nl.`status` = 'sent'
                  AND NOT EXISTS (
                      SELECT 1
                      FROM `" . db_prefix() . "dietic_patient_notifications` pn
                      WHERE pn.`patient_id` = nl.`patient_id`
                        AND pn.`notification_type` = nl.`notification_type`
                        AND pn.`message` = nl.`message`
                        AND pn.`created_at` = nl.`created_at`
                  )
            ";

            // Execute the query
            $this->db->query($sql);
            $affected_rows = $this->db->affected_rows();

            // Complete transaction
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Transaction failed',
                    'migrated' => 0
                ]);
                return;
            }

            // Count notifications per patient after migration
            $count_query = $this->db->query("
                SELECT
                    patient_id,
                    COUNT(*) as notification_count,
                    SUM(CASE WHEN is_read = 0 THEN 1 ELSE 0 END) as unread_count
                FROM `" . db_prefix() . "dietic_patient_notifications`
                GROUP BY patient_id
                ORDER BY patient_id
            ");
            $patient_stats = $count_query->result_array();

            // Log the migration
            log_activity('Notifications migrated from logs to patient_notifications: ' . $affected_rows . ' notifications');

            echo json_encode([
                'success' => true,
                'message' => 'Migration completed successfully',
                'migrated' => $affected_rows,
                'patient_stats' => $patient_stats
            ]);

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_activity('Error migrating notifications: ' . $e->getMessage());

            echo json_encode([
                'success' => false,
                'message' => 'Error during migration: ' . $e->getMessage(),
                'migrated' => 0
            ]);
        }
    }

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

        // Get client data for header
        $this->load->model('clients_model');
        $client = $this->clients_model->get($patient->client_id);

        $data = [];
        $data['title'] = 'Bibliothèque de Recettes';
        $data['patient'] = $patient;
        $data['client'] = $client;
        $data['page_title'] = 'Bibliothèque de Recettes';
        $data['active_page'] = 'recipes';

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

        // Check if recipe is assigned to patient
        $is_assigned = $this->dietetic_recipes_model->is_assigned_to_patient($recipe_id, $patient->id);

        if (!$is_assigned) {
            // Show restriction message for non-assigned recipes
            $data = [];
            $data['title'] = 'Recette non disponible';
            $data['recipe'] = $recipe;
            $data['patient'] = $patient;
            $this->load->view('portal/recipes/restricted', $data);
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

    /**
     * Display Privacy Policy page (public access)
     */
    public function privacy()
    {
        $this->load->model('dietetic/dietetic_settings_model');

        $data['title'] = 'Politique de Confidentialité';
        $data['content'] = $this->dietetic_settings_model->get_setting('privacy_policy') ?? '<p>Aucune politique de confidentialité n\'a été définie.</p>';

        $this->load->view('portal_legal_page', $data);
    }

    /**
     * Display Terms of Service page (public access)
     */
    public function terms()
    {
        $this->load->model('dietetic/dietetic_settings_model');

        $data['title'] = 'Conditions Générales d\'Utilisation';
        $data['content'] = $this->dietetic_settings_model->get_setting('terms_of_service') ?? '<p>Aucune condition d\'utilisation n\'a été définie.</p>';

        $this->load->view('portal_legal_page', $data);
    }

    /**
     * Display patient profile page
     */
    public function profile()
    {
        // DEBUG: Enable error display
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        // Check if client is logged in
        if (!is_client_logged_in()) {
            log_activity('Profile access denied - not logged in');
            redirect(site_url('authentication/login'));
            return;
        }

        $client_id = get_client_user_id();
        log_activity('Profile accessed by client ID: ' . $client_id);

        // Load required models
        $this->load->model('clients_model');
        $this->load->model('dietetic/dietetic_patients_model');

        // Get patient
        try {
            $patient = $this->dietetic_patients_model->get_by_client($client_id);
            log_activity('Patient lookup result: ' . ($patient ? 'Found ID ' . $patient->id : 'NOT FOUND'));
        } catch (Exception $e) {
            log_activity('Error loading patient profile: ' . $e->getMessage());
            echo '<pre>ERROR: ' . $e->getMessage() . '</pre>';
            $patient = null;
        }

        if (!$patient) {
            log_activity('No patient found for client ID: ' . $client_id);
            echo '<h1>DEBUG: Patient not found for client ID: ' . $client_id . '</h1>';
            echo '<p>Checking database...</p>';

            // Debug query
            $query = $this->db->get_where(db_prefix() . 'dietic_patients', ['client_id' => $client_id]);
            echo '<pre>Query result: ' . print_r($query->result(), true) . '</pre>';
            echo '<p><a href="' . site_url('dietetic/portal') . '">Retour à l\'accueil</a></p>';
            return;
        }

        log_activity('Loading profile for patient ID: ' . $patient->id);

        $data = [];
        $data['patient'] = $patient;
        $data['title'] = 'Mon Profil';
        $data['active_page'] = 'profile';

        // Get client info
        $client = $this->clients_model->get($patient->client_id);
        if (!$client) {
            log_activity('Client not found for patient ID: ' . $patient->id);
            echo '<h1>DEBUG: Client not found</h1>';
            return;
        }
        $data['client'] = $client;

        // Get contact info for profile image
        $contact = null;
        $contact_id = null;

        if (!empty($client->default_contact)) {
            $contact_id = $client->default_contact;
        } else {
            // Get all contacts for this client
            $contacts = $this->clients_model->get_contacts($client->userid);
            if (!empty($contacts)) {
                $contact_id = $contacts[0]['id'];
            }
        }

        if ($contact_id) {
            $contact = $this->clients_model->get_contact($contact_id);
        }

        $data['contact'] = $contact;

        // Get latest measurement for current weight
        try {
            $latest_measurement = $this->dietetic_patients_model->get_latest_measurement($patient->id, true);
            $data['latest_measurement'] = $latest_measurement;
        } catch (Exception $e) {
            log_activity('Error loading latest measurement: ' . $e->getMessage());
            $data['latest_measurement'] = null;
        }

        // Calculate age from birth_date
        if (!empty($patient->birth_date)) {
            try {
                $birth_date = new DateTime($patient->birth_date);
                $today = new DateTime();
                $age = $today->diff($birth_date)->y;
                $data['age'] = $age;
            } catch (Exception $e) {
                $data['age'] = null;
            }
        } else {
            $data['age'] = null;
        }

        // Get patient documents
        $this->load->model('dietetic/dietetic_patient_documents_model');

        // Ensure table exists
        if (!$this->dietetic_patient_documents_model->table_exists()) {
            $this->dietetic_patient_documents_model->create_table();
        }

        try {
            $data['documents'] = $this->dietetic_patient_documents_model->get_by_patient($patient->id);
        } catch (Exception $e) {
            log_activity('Error loading patient documents: ' . $e->getMessage());
            $data['documents'] = [];
        }

        log_activity('Loading profile view');
        $this->load->view('portal_profile', $data);
    }

    /**
     * Update patient password via AJAX
     */
    public function update_password()
    {
        // Check if client is logged in
        if (!is_client_logged_in()) {
            echo json_encode(['success' => false, 'message' => 'Non authentifié']);
            return;
        }

        // Get posted data
        $current_password = $this->input->post('current_password');
        $new_password = $this->input->post('new_password');
        $confirm_password = $this->input->post('confirm_password');

        // Validate inputs
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            echo json_encode(['success' => false, 'message' => 'Tous les champs sont requis']);
            return;
        }

        if ($new_password !== $confirm_password) {
            echo json_encode(['success' => false, 'message' => 'Les mots de passe ne correspondent pas']);
            return;
        }

        if (strlen($new_password) < 6) {
            echo json_encode(['success' => false, 'message' => 'Le mot de passe doit contenir au moins 6 caractères']);
            return;
        }

        $client_id = get_client_user_id();

        // Load clients model
        $this->load->model('clients_model');

        // Get current client data
        $client = $this->clients_model->get($client_id);

        // Verify current password
        $this->load->library('App_password_hasher');
        $hasher = new App_password_hasher();

        if (!$hasher->CheckPassword($current_password, $client->password)) {
            echo json_encode(['success' => false, 'message' => 'Mot de passe actuel incorrect']);
            return;
        }

        // Update password
        $hashed_password = $hasher->HashPassword($new_password);

        $this->db->where('userid', $client_id);
        $this->db->update(db_prefix() . 'clients', [
            'password' => $hashed_password,
            'last_password_change' => date('Y-m-d H:i:s')
        ]);

        log_activity('Patient Password Changed [Client ID: ' . $client_id . ']');

        echo json_encode(['success' => true, 'message' => 'Mot de passe modifié avec succès']);
    }

    /**
     * Update patient profile information via AJAX
     */
    public function update_profile()
    {
        // Check if client is logged in
        if (!is_client_logged_in()) {
            echo json_encode(['success' => false, 'message' => 'Non authentifié']);
            return;
        }

        $client_id = get_client_user_id();

        // Get patient
        $patient = $this->dietetic_patients_model->get_by_client($client_id);

        if (!$patient) {
            echo json_encode(['success' => false, 'message' => 'Patient non trouvé']);
            return;
        }

        // Get posted data
        $phone = $this->input->post('phone');
        $dietary_preferences = $this->input->post('dietary_preferences');
        $allergies = $this->input->post('allergies');

        // Update patient data
        $update_data = [
            'phone' => $phone,
            'dietary_preferences' => $dietary_preferences,
            'allergies' => $allergies,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->db->where('id', $patient->id);
        $this->db->update(db_prefix() . 'dietic_patients', $update_data);

        log_activity('Patient Profile Updated [Patient ID: ' . $patient->id . ']');

        echo json_encode(['success' => true, 'message' => 'Profil mis à jour avec succès']);
    }

    /**
     * Update emergency contact via AJAX
     */
    public function update_emergency_contact()
    {
        // Check if client is logged in
        if (!is_client_logged_in()) {
            echo json_encode(['success' => false, 'message' => 'Non authentifié']);
            return;
        }

        $client_id = get_client_user_id();

        // Get patient
        $patient = $this->dietetic_patients_model->get_by_client($client_id);

        if (!$patient) {
            echo json_encode(['success' => false, 'message' => 'Patient non trouvé']);
            return;
        }

        // Get posted data
        $emergency_contact = $this->input->post('emergency_contact');
        $emergency_phone = $this->input->post('emergency_phone');

        // Update patient data
        $update_data = [
            'emergency_contact' => $emergency_contact,
            'emergency_phone' => $emergency_phone,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->db->where('id', $patient->id);
        $this->db->update(db_prefix() . 'dietic_patients', $update_data);

        log_activity('Emergency Contact Updated [Patient ID: ' . $patient->id . ']');

        echo json_encode(['success' => true, 'message' => 'Contact d\'urgence mis à jour avec succès']);
    }

    /**
     * Upload medical document
     */
    public function upload_document()
    {
        // Set JSON header
        header('Content-Type: application/json');

        // Check if client is logged in
        if (!is_client_logged_in()) {
            echo json_encode(['success' => false, 'message' => 'Non authentifié']);
            return;
        }

        $client_id = get_client_user_id();

        // Load models
        $this->load->model('dietetic/dietetic_patients_model');
        $this->load->model('dietetic/dietetic_patient_documents_model');

        // Ensure table exists
        if (!$this->dietetic_patient_documents_model->table_exists()) {
            $this->dietetic_patient_documents_model->create_table();
        }

        // Get patient
        $patient = $this->dietetic_patients_model->get_by_client($client_id);

        if (!$patient) {
            echo json_encode(['success' => false, 'message' => 'Patient non trouvé']);
            return;
        }

        // Check if file was uploaded
        if (!isset($_FILES['document']) || $_FILES['document']['error'] !== UPLOAD_ERR_OK) {
            $error_msg = 'Aucun fichier uploadé';
            if (isset($_FILES['document']['error'])) {
                switch ($_FILES['document']['error']) {
                    case UPLOAD_ERR_INI_SIZE:
                    case UPLOAD_ERR_FORM_SIZE:
                        $error_msg = 'Fichier trop volumineux';
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        $error_msg = 'Aucun fichier sélectionné';
                        break;
                    default:
                        $error_msg = 'Erreur lors de l\'upload (Code: ' . $_FILES['document']['error'] . ')';
                }
            }
            echo json_encode(['success' => false, 'message' => $error_msg]);
            return;
        }

        // Validate file type
        $allowed_types = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
        $file_type = $_FILES['document']['type'];
        $extension = strtolower(pathinfo($_FILES['document']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['pdf', 'jpg', 'jpeg', 'png'];

        if (!in_array($file_type, $allowed_types) && !in_array($extension, $allowed_extensions)) {
            echo json_encode(['success' => false, 'message' => 'Type de fichier non autorisé (PDF ou images uniquement)']);
            return;
        }

        // Validate file size (10MB max)
        if ($_FILES['document']['size'] > 10 * 1024 * 1024) {
            echo json_encode(['success' => false, 'message' => 'Fichier trop volumineux (max 10MB)']);
            return;
        }

        // Create upload directory if it doesn't exist
        $upload_base = FCPATH . 'uploads/dietetic/documents/patient_' . $patient->id;
        if (!is_dir($upload_base)) {
            if (!mkdir($upload_base, 0755, true)) {
                echo json_encode(['success' => false, 'message' => 'Impossible de créer le dossier d\'upload']);
                return;
            }
        }

        // Generate unique filename
        $original_name = pathinfo($_FILES['document']['name'], PATHINFO_FILENAME);
        // Sanitize filename
        $original_name = preg_replace('/[^a-zA-Z0-9_-]/', '_', $original_name);
        $filename = $original_name . '_' . time() . '.' . $extension;
        $filepath = $upload_base . '/' . $filename;

        // Move uploaded file
        if (move_uploaded_file($_FILES['document']['tmp_name'], $filepath)) {
            // Save document metadata to database
            $document_data = [
                'patient_id' => $patient->id,
                'filename' => $filename,
                'original_filename' => $_FILES['document']['name'],
                'file_type' => $file_type,
                'file_size' => $_FILES['document']['size']
            ];

            $document_id = $this->dietetic_patient_documents_model->add($document_data);

            log_activity('Medical Document Uploaded [Patient ID: ' . $patient->id . ', File: ' . $filename . ', Document ID: ' . $document_id . ']');

            echo json_encode([
                'success' => true,
                'message' => 'Document uploadé avec succès',
                'filename' => $filename,
                'document_id' => $document_id
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la sauvegarde du fichier']);
        }
    }

    /**
     * Upload profile photo
     */
    public function upload_profile_photo()
    {
        // Set JSON header
        header('Content-Type: application/json');

        // Check if client is logged in
        if (!is_client_logged_in()) {
            echo json_encode(['success' => false, 'message' => 'Non authentifié']);
            return;
        }

        $client_id = get_client_user_id();

        // Load models
        $this->load->model('clients_model');
        $this->load->model('dietetic/dietetic_patients_model');

        // Get patient
        $patient = $this->dietetic_patients_model->get_by_client($client_id);

        if (!$patient) {
            echo json_encode(['success' => false, 'message' => 'Patient non trouvé']);
            return;
        }

        // Get client info
        $client = $this->clients_model->get($patient->client_id);
        if (!$client) {
            echo json_encode(['success' => false, 'message' => 'Client non trouvé']);
            return;
        }

        // Get contact ID - use default_contact or find first contact for this client
        $contact_id = null;
        if (!empty($client->default_contact)) {
            $contact_id = $client->default_contact;
        } else {
            // Get all contacts for this client
            $contacts = $this->clients_model->get_contacts($client->userid);
            if (!empty($contacts)) {
                $contact_id = $contacts[0]['id'];
            }
        }

        if (!$contact_id) {
            echo json_encode(['success' => false, 'message' => 'Aucun contact trouvé pour ce client']);
            return;
        }

        // Check if file was uploaded
        if (!isset($_FILES['profile_image']) || $_FILES['profile_image']['error'] !== UPLOAD_ERR_OK) {
            $error_msg = 'Aucune image uploadée';
            if (isset($_FILES['profile_image']['error'])) {
                switch ($_FILES['profile_image']['error']) {
                    case UPLOAD_ERR_INI_SIZE:
                    case UPLOAD_ERR_FORM_SIZE:
                        $error_msg = 'Image trop volumineuse';
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        $error_msg = 'Aucune image sélectionnée';
                        break;
                    default:
                        $error_msg = 'Erreur lors de l\'upload (Code: ' . $_FILES['profile_image']['error'] . ')';
                }
            }
            echo json_encode(['success' => false, 'message' => $error_msg]);
            return;
        }

        // Validate file type (images only)
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $file_type = $_FILES['profile_image']['type'];
        $extension = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($file_type, $allowed_types) && !in_array($extension, $allowed_extensions)) {
            echo json_encode(['success' => false, 'message' => 'Type de fichier non autorisé (images uniquement)']);
            return;
        }

        // Validate file size (5MB max for profile images)
        if ($_FILES['profile_image']['size'] > 5 * 1024 * 1024) {
            echo json_encode(['success' => false, 'message' => 'Image trop volumineuse (max 5MB)']);
            return;
        }

        // Create upload directory for contact profile images (Perfex standard path)
        $upload_path = FCPATH . 'uploads/client_profile_images/' . $contact_id;
        if (!is_dir($upload_path)) {
            if (!mkdir($upload_path, 0755, true)) {
                echo json_encode(['success' => false, 'message' => 'Impossible de créer le dossier d\'upload']);
                return;
            }
        }

        // Remove old profile image if exists
        $contact = $this->clients_model->get_contact($contact_id);
        if ($contact && !empty($contact->profile_image)) {
            $old_image_path = $upload_path . '/' . $contact->profile_image;
            if (file_exists($old_image_path)) {
                @unlink($old_image_path);
            }
            // Also remove thumb
            $thumb_path = $upload_path . '/thumb_' . $contact->profile_image;
            if (file_exists($thumb_path)) {
                @unlink($thumb_path);
            }
        }

        // Generate unique filename
        $filename = 'profile_' . time() . '.' . $extension;
        $filepath = $upload_path . '/' . $filename;

        // Move uploaded file
        if (!move_uploaded_file($_FILES['profile_image']['tmp_name'], $filepath)) {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la sauvegarde de l\'image']);
            return;
        }

        // Create thumbnail using Perfex's image library
        $this->load->library('image_lib');

        $config['image_library'] = 'gd2';
        $config['source_image'] = $filepath;
        $config['new_image'] = $upload_path . '/thumb_' . $filename;
        $config['maintain_ratio'] = true;
        $config['width'] = 160;
        $config['height'] = 160;

        $this->image_lib->initialize($config);
        $this->image_lib->resize();
        $this->image_lib->clear();

        // Update contact record in database
        $this->db->where('id', $contact_id);
        $this->db->update(db_prefix() . 'contacts', [
            'profile_image' => $filename,
            'last_ip' => $this->input->ip_address(),
            'last_login' => date('Y-m-d H:i:s')
        ]);

        log_activity('Contact Profile Image Updated [Contact ID: ' . $contact_id . ', File: ' . $filename . ']');

        // Build direct URL to the uploaded image
        $image_url = base_url('uploads/client_profile_images/' . $contact_id . '/thumb_' . $filename);

        echo json_encode([
            'success' => true,
            'message' => 'Photo de profil mise à jour avec succès',
            'image_url' => $image_url
        ]);
    }

    /**
     * Download patient document
     *
     * @param int $document_id
     */
    public function download_document($document_id)
    {
        // Check if client is logged in
        if (!is_client_logged_in()) {
            show_404();
            return;
        }

        $client_id = get_client_user_id();

        // Load models
        $this->load->model('dietetic/dietetic_patients_model');
        $this->load->model('dietetic/dietetic_patient_documents_model');

        // Get patient
        $patient = $this->dietetic_patients_model->get_by_client($client_id);

        if (!$patient) {
            show_404();
            return;
        }

        // Get document
        $document = $this->dietetic_patient_documents_model->get($document_id);

        if (!$document) {
            show_404();
            return;
        }

        // Verify that document belongs to this patient (security check)
        if ($document->patient_id != $patient->id) {
            show_404();
            return;
        }

        // Build file path
        $filepath = FCPATH . 'uploads/dietetic/documents/patient_' . $patient->id . '/' . $document->filename;

        // Check if file exists
        if (!file_exists($filepath)) {
            show_404();
            return;
        }

        // Force download
        $this->load->helper('download');
        force_download($document->original_filename, file_get_contents($filepath));

        log_activity('Medical Document Downloaded [Patient ID: ' . $patient->id . ', Document ID: ' . $document_id . ', File: ' . $document->original_filename . ']');
    }

    // ============================================================
    // DAILY TRACKING API METHODS
    // ============================================================

    /**
     * API: Get today's tracking data
     * Returns JSON with current water, meals, activity, etc.
     *
     * @return void (outputs JSON)
     */
    public function api_get_daily_tracking()
    {
        header('Content-Type: application/json');

        if (!is_client_logged_in()) {
            echo json_encode(['success' => false, 'error' => 'Not authenticated']);
            return;
        }

        $client_id = get_client_user_id();
        $patient = $this->dietetic_patients_model->get_by_client($client_id);

        if (!$patient) {
            echo json_encode(['success' => false, 'error' => 'Patient not found']);
            return;
        }

        try {
            $tracking = $this->dietetic_daily_tracking_model->get_today($patient->id);
            $streak = $this->dietetic_daily_tracking_model->calculate_streak($patient->id);
            $completion = $this->dietetic_daily_tracking_model->get_completion_percentage($patient->id);

            echo json_encode([
                'success' => true,
                'data' => [
                    'tracking' => $tracking,
                    'streak' => $streak,
                    'completion' => $completion
                ]
            ]);
        } catch (Exception $e) {
            log_activity('Error getting daily tracking: ' . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Server error']);
        }
    }

    /**
     * API: Update water glasses count
     * POST: {action: 'increment' | 'decrement'}
     *
     * @return void (outputs JSON)
     */
    public function api_update_water()
    {
        header('Content-Type: application/json');

        if (!is_client_logged_in()) {
            echo json_encode(['success' => false, 'error' => 'Not authenticated']);
            return;
        }

        $client_id = get_client_user_id();
        $patient = $this->dietetic_patients_model->get_by_client($client_id);

        if (!$patient) {
            echo json_encode(['success' => false, 'error' => 'Patient not found']);
            return;
        }

        // Support both JSON and FormData
        $input = $_POST;
        if (empty($input)) {
            $input = json_decode(file_get_contents('php://input'), true) ?: [];
        }

        $action = $input['action'] ?? 'increment';
        $amount = ($action === 'increment') ? 1 : -1;

        try {
            $success = $this->dietetic_daily_tracking_model->update_water($patient->id, $amount);

            if ($success) {
                $tracking = $this->dietetic_daily_tracking_model->get_today($patient->id);
                echo json_encode([
                    'success' => true,
                    'water_glasses' => $tracking->water_glasses
                ]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Update failed']);
            }
        } catch (Exception $e) {
            log_activity('Error updating water: ' . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Server error']);
        }
    }

    /**
     * API: Toggle meal check (breakfast, lunch, dinner)
     * POST: {meal: 'breakfast' | 'lunch' | 'dinner', checked: true|false}
     *
     * @return void (outputs JSON)
     */
    public function api_toggle_meal()
    {
        header('Content-Type: application/json');
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');

        if (!is_client_logged_in()) {
            echo json_encode(['success' => false, 'error' => 'Not authenticated']);
            return;
        }

        $client_id = get_client_user_id();
        $patient = $this->dietetic_patients_model->get_by_client($client_id);

        if (!$patient) {
            echo json_encode(['success' => false, 'error' => 'Patient not found']);
            return;
        }

        // Support both JSON and FormData
        $input = $_POST;
        if (empty($input)) {
            $input = json_decode(file_get_contents('php://input'), true) ?: [];
        }

        $meal = $input['meal'] ?? '';
        $checked = isset($input['checked']) ? filter_var($input['checked'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : null;

        if (!in_array($meal, ['breakfast', 'lunch', 'dinner'])) {
            echo json_encode(['success' => false, 'error' => 'Invalid meal type']);
            return;
        }

        try {
            $success = $this->dietetic_daily_tracking_model->toggle_meal($patient->id, $meal, $checked);

            if ($success) {
                $tracking = $this->dietetic_daily_tracking_model->get_today($patient->id);
                $field = $meal . '_checked';

                // Award points if meal was checked (validated)
                if ($checked && $this->is_gamification_ready()) {
                    try {
                        $meal_names = [
                            'breakfast' => 'Petit-déjeuner',
                            'lunch' => 'Déjeuner',
                            'dinner' => 'Dîner'
                        ];
                        $this->dietetic_gamification_model->award_points(
                            $patient->id,
                            5,
                            'meal_validated',
                            $meal_names[$meal] . ' validé'
                        );
                        // Check for badge unlocks
                        $this->dietetic_gamification_model->check_and_award_badges($patient->id);
                    } catch (Exception $e) {
                        // Silently log gamification errors - don't break the main flow
                        log_activity('Gamification error in toggle_meal: ' . $e->getMessage());
                    }
                }

                echo json_encode([
                    'success' => true,
                    'checked' => (bool)$tracking->$field
                ]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Update failed']);
            }
        } catch (Exception $e) {
            log_activity('Error toggling meal: ' . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Server error']);
        }
    }

    /**
     * API: Update activity minutes
     * POST: {minutes: int, activity_type?: string}
     *
     * @return void (outputs JSON)
     */
    public function api_update_activity()
    {
        header('Content-Type: application/json');

        if (!is_client_logged_in()) {
            echo json_encode(['success' => false, 'error' => 'Not authenticated']);
            return;
        }

        $client_id = get_client_user_id();
        $patient = $this->dietetic_patients_model->get_by_client($client_id);

        if (!$patient) {
            echo json_encode(['success' => false, 'error' => 'Patient not found']);
            return;
        }

        // Support both JSON and FormData
        $input = $_POST;
        if (empty($input)) {
            $input = json_decode(file_get_contents('php://input'), true) ?: [];
        }

        $minutes = (int)($input['minutes'] ?? 0);
        $activity_type = $input['activity_type'] ?? null;

        try {
            $success = $this->dietetic_daily_tracking_model->update_activity($patient->id, $minutes, $activity_type);

            if ($success) {
                $tracking = $this->dietetic_daily_tracking_model->get_today($patient->id);
                echo json_encode([
                    'success' => true,
                    'activity_minutes' => $tracking->activity_minutes
                ]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Update failed']);
            }
        } catch (Exception $e) {
            log_activity('Error updating activity: ' . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Server error']);
        }
    }

    /**
     * API: Update calories consumed
     * POST: {calories: int}
     *
     * @return void (outputs JSON)
     */
    public function api_update_calories()
    {
        header('Content-Type: application/json');

        if (!is_client_logged_in()) {
            echo json_encode(['success' => false, 'error' => 'Not authenticated']);
            return;
        }

        $client_id = get_client_user_id();
        $patient = $this->dietetic_patients_model->get_by_client($client_id);

        if (!$patient) {
            echo json_encode(['success' => false, 'error' => 'Patient not found']);
            return;
        }

        // Support both JSON and FormData
        $input = $_POST;
        if (empty($input)) {
            $input = json_decode(file_get_contents('php://input'), true) ?: [];
        }

        $calories = (int)($input['calories'] ?? 0);

        try {
            $success = $this->dietetic_daily_tracking_model->update_calories($patient->id, $calories);

            if ($success) {
                $tracking = $this->dietetic_daily_tracking_model->get_today($patient->id);
                echo json_encode([
                    'success' => true,
                    'calories_consumed' => $tracking->calories_consumed
                ]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Update failed']);
            }
        } catch (Exception $e) {
            log_activity('Error updating calories: ' . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Server error']);
        }
    }

    /**
     * API: Get current streak
     *
     * @return void (outputs JSON)
     */
    public function api_get_streak()
    {
        header('Content-Type: application/json');

        if (!is_client_logged_in()) {
            echo json_encode(['success' => false, 'error' => 'Not authenticated']);
            return;
        }

        $client_id = get_client_user_id();
        $patient = $this->dietetic_patients_model->get_by_client($client_id);

        if (!$patient) {
            echo json_encode(['success' => false, 'error' => 'Patient not found']);
            return;
        }

        try {
            $streak = $this->dietetic_daily_tracking_model->calculate_streak($patient->id);
            echo json_encode([
                'success' => true,
                'streak' => $streak
            ]);
        } catch (Exception $e) {
            log_activity('Error getting streak: ' . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Server error']);
        }
    }

    /**
     * Upload audio note for food survey entry
     * API endpoint for voice notes recording
     */
    public function upload_audio_note()
    {
        header('Content-Type: application/json');

        if (!is_client_logged_in()) {
            echo json_encode(['success' => false, 'message' => 'Non authentifié']);
            return;
        }

        $client_id = get_client_user_id();
        $patient = $this->dietetic_patients_model->get_by_client($client_id);

        if (!$patient) {
            echo json_encode(['success' => false, 'message' => 'Patient non trouvé']);
            return;
        }

        try {
            $entry_id = $this->input->post('entry_id');
            $meal_type = $this->input->post('meal_type');
            $duration = $this->input->post('duration');

            if (!$entry_id || !$meal_type) {
                echo json_encode(['success' => false, 'message' => 'Paramètres manquants']);
                return;
            }

            // Verify entry belongs to patient
            $entry = $this->dietetic_food_surveys_model->get_entry($entry_id);
            if (!$entry) {
                echo json_encode(['success' => false, 'message' => 'Entrée non trouvée']);
                return;
            }

            $survey = $this->dietetic_food_surveys_model->get($entry->survey_id);
            if ($survey->patient_id != $patient->id) {
                echo json_encode(['success' => false, 'message' => 'Accès refusé']);
                return;
            }

            // Check if file was uploaded
            if (!isset($_FILES['audio']) || $_FILES['audio']['error'] !== UPLOAD_ERR_OK) {
                echo json_encode(['success' => false, 'message' => 'Aucun fichier audio reçu']);
                return;
            }

            $file = $_FILES['audio'];

            // Validate file type by extension and MIME
            $allowed_extensions = ['webm', 'mp3', 'wav', 'ogg', 'm4a', 'mp4', 'mpeg'];
            $allowed_mimes = [
                'audio/webm',
                'video/webm',
                'audio/mpeg',
                'audio/mp3',
                'audio/wav',
                'audio/wave',
                'audio/x-wav',
                'audio/ogg',
                'audio/mp4',
                'audio/x-m4a',
                'audio/m4a',
                'application/octet-stream'
            ];

            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            // Get MIME type
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            // Check extension first (more reliable for audio files)
            if (!in_array($extension, $allowed_extensions)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Extension non autorisée. Extensions acceptées: ' . implode(', ', $allowed_extensions)
                ]);
                return;
            }

            // Validate file size (max 10MB)
            $max_size = 10 * 1024 * 1024; // 10MB
            if ($file['size'] > $max_size) {
                echo json_encode(['success' => false, 'message' => 'Fichier trop volumineux (max 10MB)']);
                return;
            }

            // Create upload directory if it doesn't exist
            $upload_path = FCPATH . 'uploads/dietetic/audio_notes/';
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0755, true);
            }

            // Generate unique filename
            $filename = 'audio_' . uniqid() . '_' . time() . '.' . $extension;
            $destination = $upload_path . $filename;

            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $destination)) {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'enregistrement du fichier']);
                return;
            }

            @chmod($destination, 0644);

            // Save to database
            $audio_data = [
                'entry_id' => $entry_id,
                'meal_type' => $meal_type,
                'audio_file' => $filename,
                'duration' => $duration,
                'file_size' => $file['size'],
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->db->insert(db_prefix() . 'dietic_food_survey_audio_notes', $audio_data);

            echo json_encode([
                'success' => true,
                'message' => 'Note vocale enregistrée',
                'audio_id' => $this->db->insert_id()
            ]);

        } catch (Exception $e) {
            log_activity('Error uploading audio note: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Erreur serveur: ' . $e->getMessage()]);
        }
    }

    /**
     * Get audio notes for a specific entry and meal type
     */
    public function get_audio_notes($entry_id, $meal_type)
    {
        header('Content-Type: application/json');

        if (!is_client_logged_in()) {
            echo json_encode(['success' => false, 'message' => 'Non authentifié']);
            return;
        }

        $client_id = get_client_user_id();
        $patient = $this->dietetic_patients_model->get_by_client($client_id);

        if (!$patient) {
            echo json_encode(['success' => false, 'message' => 'Patient non trouvé']);
            return;
        }

        try {
            // Verify entry belongs to patient
            $entry = $this->dietetic_food_surveys_model->get_entry($entry_id);
            if (!$entry) {
                echo json_encode(['success' => false, 'message' => 'Entrée non trouvée']);
                return;
            }

            $survey = $this->dietetic_food_surveys_model->get($entry->survey_id);
            if ($survey->patient_id != $patient->id) {
                echo json_encode(['success' => false, 'message' => 'Accès refusé']);
                return;
            }

            // Get audio notes
            $this->db->where('entry_id', $entry_id);
            $this->db->where('meal_type', $meal_type);
            $this->db->order_by('created_at', 'DESC');
            $audios = $this->db->get(db_prefix() . 'dietic_food_survey_audio_notes')->result_array();

            echo json_encode([
                'success' => true,
                'audios' => $audios
            ]);

        } catch (Exception $e) {
            log_activity('Error getting audio notes: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Erreur serveur']);
        }
    }

    /**
     * Delete audio note
     */
    public function delete_audio_note()
    {
        header('Content-Type: application/json');

        if (!is_client_logged_in()) {
            echo json_encode(['success' => false, 'message' => 'Non authentifié']);
            return;
        }

        $client_id = get_client_user_id();
        $patient = $this->dietetic_patients_model->get_by_client($client_id);

        if (!$patient) {
            echo json_encode(['success' => false, 'message' => 'Patient non trouvé']);
            return;
        }

        try {
            $audio_id = $this->input->post('audio_id');

            if (!$audio_id) {
                echo json_encode(['success' => false, 'message' => 'ID manquant']);
                return;
            }

            // Get audio note
            $audio = $this->db->get_where(db_prefix() . 'dietic_food_survey_audio_notes', ['id' => $audio_id])->row();

            if (!$audio) {
                echo json_encode(['success' => false, 'message' => 'Note vocale non trouvée']);
                return;
            }

            // Verify it belongs to patient
            $entry = $this->dietetic_food_surveys_model->get_entry($audio->entry_id);
            $survey = $this->dietetic_food_surveys_model->get($entry->survey_id);

            if ($survey->patient_id != $patient->id) {
                echo json_encode(['success' => false, 'message' => 'Accès refusé']);
                return;
            }

            // Delete file
            $file_path = FCPATH . 'uploads/dietetic/audio_notes/' . $audio->audio_file;
            if (file_exists($file_path)) {
                unlink($file_path);
            }

            // Delete from database
            $this->db->delete(db_prefix() . 'dietic_food_survey_audio_notes', ['id' => $audio_id]);

            echo json_encode([
                'success' => true,
                'message' => 'Note vocale supprimée'
            ]);

        } catch (Exception $e) {
            log_activity('Error deleting audio note: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Erreur serveur']);
        }
    }

    /**
     * Upload audio note for recommendation (Patient response)
     */
    public function upload_recommendation_audio_response()
    {
        // Set JSON header first
        header('Content-Type: application/json');

        // Disable error display to prevent HTML output
        @ini_set('display_errors', 0);

        if (!is_client_logged_in()) {
            echo json_encode(['success' => false, 'message' => 'Non authentifié']);
            return;
        }

        $client_id = get_client_user_id();
        $patient = $this->dietetic_patients_model->get_by_client($client_id);

        if (!$patient) {
            echo json_encode(['success' => false, 'message' => 'Patient non trouvé']);
            return;
        }

        try {
            $recommendation_id = $this->input->post('recommendation_id');
            $duration = $this->input->post('duration');

            if (!$recommendation_id) {
                echo json_encode(['success' => false, 'message' => 'ID de recommandation manquant']);
                return;
            }

            // Verify recommendation exists (simple query)
            $this->db->where('id', $recommendation_id);
            $recommendation = $this->db->get(db_prefix() . 'dietic_food_survey_recommendations')->row();

            if (!$recommendation) {
                echo json_encode(['success' => false, 'message' => 'Recommandation non trouvée']);
                return;
            }

            // Verify patient owns this recommendation's entry (simple queries)
            $this->db->where('id', $recommendation->entry_id);
            $entry = $this->db->get(db_prefix() . 'dietic_food_survey_entries')->row();

            if (!$entry) {
                echo json_encode(['success' => false, 'message' => 'Entrée non trouvée']);
                return;
            }

            $this->db->where('id', $entry->survey_id);
            $survey = $this->db->get(db_prefix() . 'dietic_food_surveys')->row();

            if (!$survey || $survey->patient_id != $patient->id) {
                echo json_encode(['success' => false, 'message' => 'Accès refusé']);
                return;
            }

            // Check if file was uploaded
            if (!isset($_FILES['audio']) || $_FILES['audio']['error'] !== UPLOAD_ERR_OK) {
                echo json_encode(['success' => false, 'message' => 'Aucun fichier audio reçu']);
                return;
            }

            $file = $_FILES['audio'];

            // Validate file type by extension
            $allowed_extensions = ['webm', 'mp3', 'wav', 'ogg', 'm4a', 'mp4', 'mpeg'];
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if (!in_array($extension, $allowed_extensions)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Extension non autorisée'
                ]);
                return;
            }

            // Validate file size (max 10MB)
            $max_size = 10 * 1024 * 1024;
            if ($file['size'] > $max_size) {
                echo json_encode(['success' => false, 'message' => 'Fichier trop volumineux (max 10MB)']);
                return;
            }

            // Create upload directory if it doesn't exist
            $upload_path = FCPATH . 'uploads/dietetic/recommendation_audio/';
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0755, true);
            }

            // Generate unique filename
            $filename = 'rec_audio_patient_' . uniqid() . '_' . time() . '.' . $extension;
            $destination = $upload_path . $filename;

            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $destination)) {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'enregistrement du fichier']);
                return;
            }

            @chmod($destination, 0644);

            // Save to database
            $audio_data = [
                'recommendation_id' => $recommendation_id,
                'sender_type' => 'patient',
                'sender_id' => $client_id,
                'audio_file' => $filename,
                'duration' => $duration,
                'file_size' => $file['size'],
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->db->insert(db_prefix() . 'dietic_recommendation_audio_notes', $audio_data);

            echo json_encode([
                'success' => true,
                'message' => 'Note vocale enregistrée',
                'audio_id' => $this->db->insert_id()
            ]);

        } catch (Exception $e) {
            log_activity('Error uploading recommendation audio response: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Erreur serveur: ' . $e->getMessage()]);
        }
    }

    /**
     * Get audio notes for a recommendation (Patient view)
     */
    public function get_recommendation_audio_notes($recommendation_id)
    {
        header('Content-Type: application/json');
        @ini_set('display_errors', 0);

        if (!is_client_logged_in()) {
            echo json_encode(['success' => false, 'message' => 'Non authentifié']);
            return;
        }

        $client_id = get_client_user_id();
        $patient = $this->dietetic_patients_model->get_by_client($client_id);

        if (!$patient) {
            echo json_encode(['success' => false, 'message' => 'Patient non trouvé']);
            return;
        }

        try {
            // Verify recommendation belongs to patient (simple queries)
            $this->db->where('id', $recommendation_id);
            $recommendation = $this->db->get(db_prefix() . 'dietic_food_survey_recommendations')->row();

            if (!$recommendation) {
                echo json_encode(['success' => false, 'message' => 'Recommandation non trouvée']);
                return;
            }

            $this->db->where('id', $recommendation->entry_id);
            $entry = $this->db->get(db_prefix() . 'dietic_food_survey_entries')->row();

            if (!$entry) {
                echo json_encode(['success' => false, 'message' => 'Entrée non trouvée']);
                return;
            }

            $this->db->where('id', $entry->survey_id);
            $survey = $this->db->get(db_prefix() . 'dietic_food_surveys')->row();

            if (!$survey || $survey->patient_id != $patient->id) {
                echo json_encode(['success' => false, 'message' => 'Accès refusé']);
                return;
            }

            // Get audio notes
            if (!$this->db->table_exists(db_prefix() . 'dietic_recommendation_audio_notes')) {
                echo json_encode(['success' => true, 'audios' => []]);
                return;
            }

            $this->db->where('recommendation_id', $recommendation_id);
            $this->db->order_by('created_at', 'ASC');
            $audios = $this->db->get(db_prefix() . 'dietic_recommendation_audio_notes')->result_array();

            // Add sender names
            foreach ($audios as &$audio) {
                if ($audio['sender_type'] === 'dietitian') {
                    $staff = $this->db->get_where('staff', ['staffid' => $audio['sender_id']])->row();
                    $audio['sender_name'] = $staff ? ($staff->firstname . ' ' . $staff->lastname) : 'Diététicien';
                } else {
                    $contact = $this->db->get_where(db_prefix() . 'contacts', ['id' => $audio['sender_id']])->row();
                    $audio['sender_name'] = $contact ? ($contact->firstname . ' ' . $contact->lastname) : 'Patient';
                }
            }

            echo json_encode(['success' => true, 'audios' => $audios]);

        } catch (Exception $e) {
            log_activity('Error getting recommendation audio notes: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Erreur serveur']);
        }
    }

    /**
     * Delete recommendation audio note (Patient)
     */
    public function delete_recommendation_audio_response()
    {
        header('Content-Type: application/json');
        @ini_set('display_errors', 0);

        if (!is_client_logged_in()) {
            echo json_encode(['success' => false, 'message' => 'Non authentifié']);
            return;
        }

        $client_id = get_client_user_id();

        try {
            $audio_id = $this->input->post('audio_id');

            if (!$audio_id) {
                echo json_encode(['success' => false, 'message' => 'ID audio manquant']);
                return;
            }

            // Get audio info
            $audio = $this->db->get_where(db_prefix() . 'dietic_recommendation_audio_notes', ['id' => $audio_id])->row();

            if (!$audio) {
                echo json_encode(['success' => false, 'message' => 'Audio non trouvé']);
                return;
            }

            // Verify ownership (patient can only delete their own audios)
            if ($audio->sender_type === 'patient' && $audio->sender_id == $client_id) {
                // Delete file
                $file_path = FCPATH . 'uploads/dietetic/recommendation_audio/' . $audio->audio_file;
                if (file_exists($file_path)) {
                    unlink($file_path);
                }

                // Delete from database
                $this->db->delete(db_prefix() . 'dietic_recommendation_audio_notes', ['id' => $audio_id]);

                echo json_encode(['success' => true, 'message' => 'Note vocale supprimée']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Non autorisé']);
            }

        } catch (Exception $e) {
            log_activity('Error deleting recommendation audio response: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Erreur serveur']);
        }
    }

    /**
     * Statistics page - Evolution charts
     */
    public function statistics()
    {
        // Check if client is logged in
        if (!is_client_logged_in()) {
            redirect(site_url('authentication/login'));
        }

        $client_id = get_client_user_id();
        $patient = $this->dietetic_patients_model->get_by_client($client_id);

        if (!$patient) {
            $this->load->view('portal_no_access');
            return;
        }

        $data = [];
        $data['patient'] = $patient;
        $data['title'] = 'Mes Statistiques';

        // Get client info
        $this->load->model('clients_model');
        $client = $this->clients_model->get($patient->client_id);
        $data['client'] = $client;

        $this->load->view('portal/statistics', $data);
    }

    /**
     * API: Get evolution data for charts
     */
    public function api_get_evolution_data()
    {
        header('Content-Type: application/json');

        if (!is_client_logged_in()) {
            echo json_encode(['success' => false, 'message' => 'Non authentifié']);
            return;
        }

        $client_id = get_client_user_id();
        $patient = $this->dietetic_patients_model->get_by_client($client_id);

        if (!$patient) {
            echo json_encode(['success' => false, 'message' => 'Patient non trouvé']);
            return;
        }

        try {
            $period = $this->input->get('period') ?: '6months'; // 1month, 3months, 6months, 1year, all

            // Calculate date range
            $end_date = date('Y-m-d');
            switch ($period) {
                case '1month':
                    $start_date = date('Y-m-d', strtotime('-1 month'));
                    break;
                case '3months':
                    $start_date = date('Y-m-d', strtotime('-3 months'));
                    break;
                case '1year':
                    $start_date = date('Y-m-d', strtotime('-1 year'));
                    break;
                case 'all':
                    $start_date = '2000-01-01';
                    break;
                case '6months':
                default:
                    $start_date = date('Y-m-d', strtotime('-6 months'));
                    break;
            }

            // Get measurements
            $this->db->select('*');
            $this->db->where('patient_id', $patient->id);
            $this->db->where('measurement_date >=', $start_date);
            $this->db->where('measurement_date <=', $end_date);
            $this->db->order_by('measurement_date', 'ASC');
            $measurements = $this->db->get(db_prefix() . 'dietic_measurements')->result_array();

            // Get food survey compliance (if table exists)
            $compliance = [];
            if ($this->db->table_exists(db_prefix() . 'dietic_food_surveys') &&
                $this->db->table_exists(db_prefix() . 'dietic_food_survey_entries')) {

                $this->db->select('DATE(' . db_prefix() . 'dietic_food_survey_entries.created_at) as date, COUNT(*) as count');
                $this->db->from(db_prefix() . 'dietic_food_survey_entries');
                $this->db->join(db_prefix() . 'dietic_food_surveys s', 's.id = ' . db_prefix() . 'dietic_food_survey_entries.survey_id');
                $this->db->where('s.patient_id', $patient->id);
                $this->db->where('DATE(' . db_prefix() . 'dietic_food_survey_entries.created_at) >=', $start_date);
                $this->db->where('DATE(' . db_prefix() . 'dietic_food_survey_entries.created_at) <=', $end_date);
                $this->db->group_by('DATE(' . db_prefix() . 'dietic_food_survey_entries.created_at)');
                $compliance = $this->db->get()->result_array();
            }

            // Calculate statistics
            $stats = [];
            if (!empty($measurements)) {
                $first = $measurements[0];
                $last = $measurements[count($measurements) - 1];

                $stats['weight'] = [
                    'initial' => $first['weight'],
                    'current' => $last['weight'],
                    'change' => $last['weight'] - $first['weight'],
                    'target' => $patient->target_weight
                ];

                $stats['bmi'] = [
                    'initial' => $first['bmi'],
                    'current' => $last['bmi'],
                    'change' => $last['bmi'] - $first['bmi']
                ];

                if ($first['waist_circumference'] && $last['waist_circumference']) {
                    $stats['waist'] = [
                        'initial' => $first['waist_circumference'],
                        'current' => $last['waist_circumference'],
                        'change' => $last['waist_circumference'] - $first['waist_circumference']
                    ];
                }

                if ($first['hip_circumference'] && $last['hip_circumference']) {
                    $stats['hip'] = [
                        'initial' => $first['hip_circumference'],
                        'current' => $last['hip_circumference'],
                        'change' => $last['hip_circumference'] - $first['hip_circumference']
                    ];
                }
            }

            // Calculate progress towards goal
            if ($patient->target_weight && !empty($measurements)) {
                $initial_weight = $measurements[0]['weight'];
                $current_weight = $measurements[count($measurements) - 1]['weight'];
                $target_weight = $patient->target_weight;

                $total_to_lose = abs($initial_weight - $target_weight);
                $already_lost = abs($initial_weight - $current_weight);
                $progress_percent = $total_to_lose > 0 ? ($already_lost / $total_to_lose) * 100 : 0;

                $stats['goal_progress'] = [
                    'percent' => round($progress_percent, 1),
                    'remaining' => abs($current_weight - $target_weight)
                ];
            }

            // Calculate compliance rate (% of days with entries)
            $total_days = (strtotime($end_date) - strtotime($start_date)) / 86400;
            $days_with_entries = count($compliance);
            $compliance_rate = $total_days > 0 ? ($days_with_entries / $total_days) * 100 : 0;

            // Calculate trends and predictions
            $trends = [];
            $insights = [];

            if (!empty($measurements) && count($measurements) >= 2) {
                $first_measurement = $measurements[0];
                $last_measurement = $measurements[count($measurements) - 1];

                // Calculate time span in weeks
                $first_date = strtotime($first_measurement['measurement_date']);
                $last_date = strtotime($last_measurement['measurement_date']);
                $weeks = max(1, ($last_date - $first_date) / (7 * 86400));

                // Weight loss rate (kg/week)
                $weight_change = $first_measurement['weight'] - $last_measurement['weight'];
                $rate_per_week = $weeks > 0 ? $weight_change / $weeks : 0;

                $trends['rate_per_week'] = round($rate_per_week, 2);
                $trends['total_weeks'] = round($weeks, 1);

                // Linear regression for trend line
                $n = count($measurements);
                $sum_x = 0;
                $sum_y = 0;
                $sum_xy = 0;
                $sum_x2 = 0;

                foreach ($measurements as $i => $m) {
                    $x = $i; // Index as x
                    $y = floatval($m['weight']);
                    $sum_x += $x;
                    $sum_y += $y;
                    $sum_xy += $x * $y;
                    $sum_x2 += $x * $x;
                }

                $slope = ($n * $sum_xy - $sum_x * $sum_y) / ($n * $sum_x2 - $sum_x * $sum_x);
                $intercept = ($sum_y - $slope * $sum_x) / $n;

                // Generate trend line points
                $trend_line = [];
                foreach ($measurements as $i => $m) {
                    $trend_line[] = [
                        'date' => $m['measurement_date'],
                        'value' => round($slope * $i + $intercept, 2)
                    ];
                }

                $trends['trend_line'] = $trend_line;
                $trends['slope'] = round($slope, 3);

                // Prediction: when will target be reached?
                if ($patient->target_weight && $rate_per_week > 0.1) {
                    $remaining_kg = abs($last_measurement['weight'] - $patient->target_weight);
                    $weeks_to_goal = $remaining_kg / $rate_per_week;
                    $estimated_date = date('Y-m-d', strtotime("+$weeks_to_goal weeks"));

                    $trends['estimated_goal_date'] = $estimated_date;
                    $trends['weeks_to_goal'] = round($weeks_to_goal, 1);
                }

                // Generate insights
                if (abs($rate_per_week) >= 0.5) {
                    if ($rate_per_week > 0) {
                        $insights[] = [
                            'type' => 'positive',
                            'icon' => 'fa-thumbs-up',
                            'message' => sprintf('Excellent ! Vous perdez en moyenne %.1f kg par semaine', $rate_per_week)
                        ];
                    } else {
                        $insights[] = [
                            'type' => 'warning',
                            'icon' => 'fa-info-circle',
                            'message' => sprintf('Attention : vous avez pris %.1f kg par semaine', abs($rate_per_week))
                        ];
                    }
                } elseif (abs($rate_per_week) > 0) {
                    $insights[] = [
                        'type' => 'neutral',
                        'icon' => 'fa-balance-scale',
                        'message' => sprintf('Votre poids reste stable (%.1f kg par semaine)', abs($rate_per_week))
                    ];
                }

                // BMI insight
                if (isset($stats['bmi']['current'])) {
                    $bmi = $stats['bmi']['current'];
                    if ($bmi < 18.5) {
                        $insights[] = [
                            'type' => 'info',
                            'icon' => 'fa-tachometer',
                            'message' => 'Votre IMC est en dessous de la normale (insuffisance pondérale)'
                        ];
                    } elseif ($bmi >= 18.5 && $bmi < 25) {
                        $insights[] = [
                            'type' => 'positive',
                            'icon' => 'fa-check-circle',
                            'message' => 'Votre IMC est dans la fourchette normale'
                        ];
                    } elseif ($bmi >= 25 && $bmi < 30) {
                        $insights[] = [
                            'type' => 'warning',
                            'icon' => 'fa-exclamation-triangle',
                            'message' => 'Votre IMC indique un surpoids'
                        ];
                    } else {
                        $insights[] = [
                            'type' => 'warning',
                            'icon' => 'fa-exclamation-triangle',
                            'message' => 'Votre IMC indique une obésité'
                        ];
                    }
                }

                // Goal achievement prediction insight
                if (isset($trends['weeks_to_goal'])) {
                    $weeks = $trends['weeks_to_goal'];
                    if ($weeks <= 4) {
                        $insights[] = [
                            'type' => 'positive',
                            'icon' => 'fa-flag-checkered',
                            'message' => sprintf('À ce rythme, vous atteindrez votre objectif dans environ %.0f semaines !', $weeks)
                        ];
                    } elseif ($weeks <= 12) {
                        $insights[] = [
                            'type' => 'info',
                            'icon' => 'fa-calendar',
                            'message' => sprintf('Vous devriez atteindre votre objectif dans environ %.0f semaines', $weeks)
                        ];
                    }
                }
            }

            // Get notes for this period
            $notes = [];
            if ($this->db->table_exists(db_prefix() . 'dietic_statistics_notes')) {
                $this->db->where('patient_id', $patient->id);
                $this->db->where('note_date >=', $start_date);
                $this->db->where('note_date <=', $end_date);
                $this->db->order_by('note_date', 'ASC');
                $notes = $this->db->get(db_prefix() . 'dietic_statistics_notes')->result_array();
            }

            echo json_encode([
                'success' => true,
                'measurements' => $measurements,
                'compliance' => $compliance,
                'stats' => $stats,
                'compliance_rate' => round($compliance_rate, 1),
                'period' => $period,
                'trends' => $trends,
                'insights' => $insights,
                'notes' => $notes
            ]);

        } catch (Exception $e) {
            log_activity('Error getting evolution data: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Erreur serveur: ' . $e->getMessage(),
                'trace' => ENVIRONMENT === 'development' ? $e->getTraceAsString() : null
            ]);
        }
    }

    /**
     * API: Add a statistic note
     * POST: {note_date, note_text, note_type, icon, color}
     */
    public function api_add_statistic_note()
    {
        header('Content-Type: application/json');

        if (!is_client_logged_in()) {
            echo json_encode(['success' => false, 'message' => 'Non authentifié']);
            return;
        }

        $client_id = get_client_user_id();
        $patient = $this->dietetic_patients_model->get_by_client($client_id);

        if (!$patient) {
            echo json_encode(['success' => false, 'message' => 'Patient non trouvé']);
            return;
        }

        try {
            // Check if table exists
            if (!$this->db->table_exists(db_prefix() . 'dietic_statistics_notes')) {
                echo json_encode(['success' => false, 'message' => 'Fonctionnalité non disponible']);
                return;
            }

            // Get POST data
            $note_date = $this->input->post('note_date');
            $note_text = $this->input->post('note_text');
            $note_type = $this->input->post('note_type') ?: 'general';
            $icon = $this->input->post('icon') ?: 'fa-sticky-note';
            $color = $this->input->post('color') ?: '#01807B';

            if (empty($note_date) || empty($note_text)) {
                echo json_encode(['success' => false, 'message' => 'Date et texte requis']);
                return;
            }

            // Insert note
            $data = [
                'patient_id' => $patient->id,
                'note_date' => $note_date,
                'note_text' => $note_text,
                'note_type' => $note_type,
                'icon' => $icon,
                'color' => $color,
                'created_by' => $client_id,
                'created_by_type' => 'patient',
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->db->insert(db_prefix() . 'dietic_statistics_notes', $data);
            $note_id = $this->db->insert_id();

            if ($note_id) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Note ajoutée avec succès',
                    'note_id' => $note_id
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout']);
            }

        } catch (Exception $e) {
            log_activity('Error adding statistic note: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Erreur serveur: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * API: Delete a statistic note
     * POST: {note_id}
     */
    public function api_delete_statistic_note()
    {
        header('Content-Type: application/json');

        if (!is_client_logged_in()) {
            echo json_encode(['success' => false, 'message' => 'Non authentifié']);
            return;
        }

        $client_id = get_client_user_id();
        $patient = $this->dietetic_patients_model->get_by_client($client_id);

        if (!$patient) {
            echo json_encode(['success' => false, 'message' => 'Patient non trouvé']);
            return;
        }

        try {
            // Check if table exists
            if (!$this->db->table_exists(db_prefix() . 'dietic_statistics_notes')) {
                echo json_encode(['success' => false, 'message' => 'Fonctionnalité non disponible']);
                return;
            }

            $note_id = $this->input->post('note_id');

            if (empty($note_id)) {
                echo json_encode(['success' => false, 'message' => 'ID de note requis']);
                return;
            }

            // Verify note belongs to patient
            $this->db->where('id', $note_id);
            $this->db->where('patient_id', $patient->id);
            $note = $this->db->get(db_prefix() . 'dietic_statistics_notes')->row();

            if (!$note) {
                echo json_encode(['success' => false, 'message' => 'Note non trouvée']);
                return;
            }

            // Delete note
            $this->db->where('id', $note_id);
            $this->db->delete(db_prefix() . 'dietic_statistics_notes');

            echo json_encode([
                'success' => true,
                'message' => 'Note supprimée avec succès'
            ]);

        } catch (Exception $e) {
            log_activity('Error deleting statistic note: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Erreur serveur: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * API: Get patient's calorie goal
     * Returns: daily calorie goal and objective
     */
    public function api_get_calorie_goal()
    {
        header('Content-Type: application/json');

        if (!is_client_logged_in()) {
            echo json_encode(['success' => false, 'message' => 'Non authentifie']);
            return;
        }

        $client_id = get_client_user_id();
        $patient = $this->dietetic_patients_model->get_by_client($client_id);

        if (!$patient) {
            echo json_encode(['success' => false, 'message' => 'Patient non trouve']);
            return;
        }

        try {
            // Load nutrition calculator
            $this->load->library('dietetic/dietetic_nutrition_calculator');

            // Get current weight
            $current_weight = null;
            if (!empty($patient->latest_measurement) && !empty($patient->latest_measurement->weight)) {
                $current_weight = floatval($patient->latest_measurement->weight);
            } elseif (!empty($patient->initial_weight)) {
                $current_weight = floatval($patient->initial_weight);
            }

            // Check for missing data and provide specific messages
            $missing_fields = [];
            if (!$current_weight) $missing_fields[] = 'poids';
            if (!$patient->height) $missing_fields[] = 'taille';
            if (empty($patient->birth_date)) $missing_fields[] = 'date de naissance';

            if (!empty($missing_fields)) {
                echo json_encode([
                    'success' => false,
                    'incomplete_profile' => true,
                    'missing_fields' => $missing_fields,
                    'message' => 'Veuillez completer votre profil pour voir votre objectif calorique'
                ]);
                return;
            }

            // Calculate age
            $dob = new DateTime($patient->birth_date);
            $now = new DateTime();
            $age = $dob->diff($now)->y;

            // Get activity level and goal
            $activity_level = floatval($patient->activity_level ?: 1.55);
            $goal = $patient->goal ?: 'weight_loss';

            // Prepare analysis data
            $patient_data = [
                'weight' => $current_weight,
                'height' => floatval($patient->height),
                'age' => $age,
                'gender' => $patient->gender,
                'activity_level' => $activity_level,
                'goal' => $goal
            ];

            // Calculate nutrition analysis
            $nutrition_analysis = $this->dietetic_nutrition_calculator->complete_nutrition_analysis($patient_data);

            // Get calorie goal
            $calorie_goal = $nutrition_analysis['calorie_needs']['calories'];

            // Convert goal to readable text
            $goal_text = 'Maintien du poids';
            switch ($goal) {
                case 'weight_loss':
                    $goal_text = 'Perte de poids';
                    break;
                case 'weight_gain':
                    $goal_text = 'Prise de poids';
                    break;
                case 'muscle_gain':
                    $goal_text = 'Prise de muscle';
                    break;
                case 'maintenance':
                default:
                    $goal_text = 'Maintien du poids';
                    break;
            }

            echo json_encode([
                'success' => true,
                'calorie_goal' => $calorie_goal,
                'goal' => $goal_text,
                'goal_code' => $goal
            ]);

        } catch (Exception $e) {
            log_activity('Error getting calorie goal for patient ' . $patient->id . ': ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Erreur serveur: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * DEBUG: Get patient's calorie goal with detailed output
     */
    public function api_get_calorie_goal_debug()
    {
        ini_set('display_errors', 1);
        error_reporting(E_ALL);

        echo "<h2>DEBUG - Calorie Goal API</h2>";
        echo "<pre>";

        echo "Step 1: Check if client logged in...\n";
        if (!is_client_logged_in()) {
            echo "ERROR: Client not logged in\n";
            return;
        }
        echo "OK - Client logged in\n\n";

        $client_id = get_client_user_id();
        echo "Step 2: Get client ID: " . $client_id . "\n\n";

        echo "Step 3: Get patient by client...\n";
        $patient = $this->dietetic_patients_model->get_by_client($client_id);

        if (!$patient) {
            echo "ERROR: Patient not found\n";
            return;
        }
        echo "OK - Patient found (ID: " . $patient->id . ")\n\n";

        echo "Step 4: Check patient data:\n";
        echo "  - Gender: " . ($patient->gender ?? 'NULL') . "\n";
        echo "  - Height: " . ($patient->height ?? 'NULL') . " cm\n";
        echo "  - Date of birth: " . ($patient->birth_date ?? 'NULL') . "\n";
        echo "  - Initial weight: " . ($patient->initial_weight ?? 'NULL') . " kg\n";
        echo "  - Activity level: " . ($patient->activity_level ?? 'NULL') . "\n";
        echo "  - Goal: " . ($patient->goal ?? 'NULL') . "\n\n";

        echo "Step 5: Check latest_measurement:\n";
        if (!empty($patient->latest_measurement)) {
            echo "  - Latest measurement exists\n";
            echo "  - Weight: " . ($patient->latest_measurement->weight ?? 'NULL') . " kg\n";
        } else {
            echo "  - No latest_measurement\n";
        }
        echo "\n";

        try {
            echo "Step 6: Load nutrition calculator...\n";
            $this->load->library('dietetic/dietetic_nutrition_calculator');
            echo "OK - Library loaded\n\n";

            echo "Step 7: Get current weight...\n";
            $current_weight = null;
            if (!empty($patient->latest_measurement) && !empty($patient->latest_measurement->weight)) {
                $current_weight = floatval($patient->latest_measurement->weight);
                echo "  - Using latest_measurement weight: " . $current_weight . " kg\n";
            } elseif (!empty($patient->initial_weight)) {
                $current_weight = floatval($patient->initial_weight);
                echo "  - Using initial_weight: " . $current_weight . " kg\n";
            } else {
                echo "  - ERROR: No weight available\n";
            }
            echo "\n";

            if (!$current_weight || !$patient->height || !$patient->birth_date) {
                echo "ERROR: Insufficient data\n";
                echo "  - Weight: " . ($current_weight ?: 'MISSING') . "\n";
                echo "  - Height: " . ($patient->height ?: 'MISSING') . "\n";
                echo "  - DOB: " . ($patient->birth_date ?: 'MISSING') . "\n";
                return;
            }

            echo "Step 8: Calculate age...\n";
            $dob = new DateTime($patient->birth_date);
            $now = new DateTime();
            $age = $dob->diff($now)->y;
            echo "  - Age: " . $age . " years\n\n";

            echo "Step 9: Prepare patient data...\n";
            $activity_level = floatval($patient->activity_level ?: 1.55);
            $goal = $patient->goal ?: 'weight_loss';

            $patient_data = [
                'weight' => $current_weight,
                'height' => floatval($patient->height),
                'age' => $age,
                'gender' => $patient->gender,
                'activity_level' => $activity_level,
                'goal' => $goal
            ];

            echo "  Patient data:\n";
            print_r($patient_data);
            echo "\n";

            echo "Step 10: Calculate nutrition analysis...\n";
            $nutrition_analysis = $this->dietetic_nutrition_calculator->complete_nutrition_analysis($patient_data);
            echo "OK - Analysis completed\n\n";

            echo "Step 11: Extract calorie goal...\n";
            echo "  Nutrition analysis structure:\n";
            echo "  - calorie_needs: ";
            print_r($nutrition_analysis['calorie_needs']);
            echo "\n";

            $calorie_goal = $nutrition_analysis['calorie_needs']['calories'];
            echo "  - Calorie goal: " . $calorie_goal . " kcal\n\n";

            echo "Step 12: Convert goal to text...\n";
            $goal_text = 'Maintien du poids';
            switch ($goal) {
                case 'weight_loss':
                    $goal_text = 'Perte de poids';
                    break;
                case 'weight_gain':
                    $goal_text = 'Prise de poids';
                    break;
                case 'muscle_gain':
                    $goal_text = 'Prise de muscle';
                    break;
            }
            echo "  - Goal text: " . $goal_text . "\n\n";

            echo "=== SUCCESS ===\n";
            echo "Final JSON response would be:\n";
            print_r([
                'success' => true,
                'calorie_goal' => $calorie_goal,
                'goal' => $goal_text,
                'goal_code' => $goal
            ]);

        } catch (Exception $e) {
            echo "\n\n=== EXCEPTION ===\n";
            echo "Message: " . $e->getMessage() . "\n";
            echo "File: " . $e->getFile() . "\n";
            echo "Line: " . $e->getLine() . "\n";
            echo "Trace:\n" . $e->getTraceAsString() . "\n";
        }

        echo "</pre>";
    }

    /**
     * API: Get today's hydration data
     * Returns: today's total consumption, goal, and history
     */
    public function api_get_hydration_data()
    {
        header('Content-Type: application/json');

        if (!is_client_logged_in()) {
            echo json_encode(['success' => false, 'message' => 'Non authentifié']);
            return;
        }

        $client_id = get_client_user_id();
        $patient = $this->dietetic_patients_model->get_by_client($client_id);

        if (!$patient) {
            echo json_encode(['success' => false, 'message' => 'Patient non trouvé']);
            return;
        }

        try {
            // Check if tables exist
            if (!$this->db->table_exists(db_prefix() . 'dietic_hydration_tracking')) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Fonctionnalité non disponible'
                ]);
                return;
            }

            $today = date('Y-m-d');

            // Get today's total
            $this->db->select_sum('quantity_ml');
            $this->db->where('patient_id', $patient->id);
            $this->db->where('tracking_date', $today);
            $result = $this->db->get(db_prefix() . 'dietic_hydration_tracking')->row();
            $today_total = $result->quantity_ml ? intval($result->quantity_ml) : 0;

            // Get or create goal
            $this->db->where('patient_id', $patient->id);
            $goal_row = $this->db->get(db_prefix() . 'dietic_hydration_goals')->row();

            if (!$goal_row) {
                // Create default goal
                $this->db->insert(db_prefix() . 'dietic_hydration_goals', [
                    'patient_id' => $patient->id,
                    'daily_goal_ml' => 2000,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                $daily_goal = 2000;
            } else {
                $daily_goal = intval($goal_row->daily_goal_ml);
            }

            // Get today's entries with time
            $this->db->where('patient_id', $patient->id);
            $this->db->where('tracking_date', $today);
            $this->db->order_by('tracking_time', 'ASC');
            $today_entries = $this->db->get(db_prefix() . 'dietic_hydration_tracking')->result_array();

            // Get last 7 days history
            $history = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = date('Y-m-d', strtotime("-$i days"));

                $this->db->select_sum('quantity_ml');
                $this->db->where('patient_id', $patient->id);
                $this->db->where('tracking_date', $date);
                $day_result = $this->db->get(db_prefix() . 'dietic_hydration_tracking')->row();

                $history[] = [
                    'date' => $date,
                    'date_formatted' => date('d/m', strtotime($date)),
                    'day_name' => date('D', strtotime($date)),
                    'total_ml' => $day_result->quantity_ml ? intval($day_result->quantity_ml) : 0,
                    'percentage' => $day_result->quantity_ml ? min(100, round(($day_result->quantity_ml / $daily_goal) * 100)) : 0
                ];
            }

            // Calculate percentage
            $percentage = min(100, round(($today_total / $daily_goal) * 100));

            echo json_encode([
                'success' => true,
                'today_total' => $today_total,
                'daily_goal' => $daily_goal,
                'percentage' => $percentage,
                'entries' => $today_entries,
                'history' => $history
            ]);

        } catch (Exception $e) {
            log_activity('Error getting hydration data: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Erreur serveur: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * API: Add hydration entry
     * POST: {quantity_ml}
     */
    public function api_add_hydration()
    {
        // Clear any previous output
        ob_clean();

        header('Content-Type: application/json');
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');

        if (!is_client_logged_in()) {
            echo json_encode(['success' => false, 'message' => 'Non authentifié']);
            exit;
        }

        $client_id = get_client_user_id();
        $patient = $this->dietetic_patients_model->get_by_client($client_id);

        if (!$patient) {
            echo json_encode(['success' => false, 'message' => 'Patient non trouvé']);
            exit;
        }

        try {
            // Check if table exists
            if (!$this->db->table_exists(db_prefix() . 'dietic_hydration_tracking')) {
                echo json_encode(['success' => false, 'message' => 'Fonctionnalité non disponible']);
                exit;
            }

            $quantity_ml = intval($this->input->post('quantity_ml'));

            if ($quantity_ml <= 0 || $quantity_ml > 2000) {
                echo json_encode(['success' => false, 'message' => 'Quantité invalide (1-2000ml)']);
                exit;
            }

            // Insert entry
            $data = [
                'patient_id' => $patient->id,
                'quantity_ml' => $quantity_ml,
                'tracking_date' => date('Y-m-d'),
                'tracking_time' => date('H:i:s'),
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->db->insert(db_prefix() . 'dietic_hydration_tracking', $data);
            $entry_id = $this->db->insert_id();

            if ($entry_id) {
                // Award points for hydration tracking
                if ($this->is_gamification_ready()) {
                    try {
                        $this->dietetic_gamification_model->award_points(
                            $patient->id,
                            3,
                            'hydration_logged',
                            "Hydratation enregistrée: {$quantity_ml}ml",
                            $entry_id
                        );
                        // Check for badge unlocks
                        $this->dietetic_gamification_model->check_and_award_badges($patient->id);
                    } catch (Exception $e) {
                        // Silently log gamification errors - don't break the main flow
                        log_activity('Gamification error in api_add_hydration: ' . $e->getMessage());
                    }
                }

                // Get new total
                $this->db->select_sum('quantity_ml');
                $this->db->where('patient_id', $patient->id);
                $this->db->where('tracking_date', date('Y-m-d'));
                $result = $this->db->get(db_prefix() . 'dietic_hydration_tracking')->row();
                $new_total = $result->quantity_ml ? intval($result->quantity_ml) : 0;

                // Get goal
                $this->db->where('patient_id', $patient->id);
                $goal_row = $this->db->get(db_prefix() . 'dietic_hydration_goals')->row();
                $daily_goal = $goal_row ? intval($goal_row->daily_goal_ml) : 2000;

                $percentage = min(100, round(($new_total / $daily_goal) * 100));

                echo json_encode([
                    'success' => true,
                    'message' => 'Hydratation enregistrée',
                    'entry_id' => $entry_id,
                    'new_total' => $new_total,
                    'percentage' => $percentage
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'enregistrement']);
            }

        } catch (Exception $e) {
            log_activity('Error adding hydration: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Erreur serveur: ' . $e->getMessage()
            ]);
        }
        exit;
    }

    /**
     * API: Update hydration goal
     * POST: {daily_goal_ml}
     */
    public function api_update_hydration_goal()
    {
        header('Content-Type: application/json');

        if (!is_client_logged_in()) {
            echo json_encode(['success' => false, 'message' => 'Non authentifié']);
            return;
        }

        $client_id = get_client_user_id();
        $patient = $this->dietetic_patients_model->get_by_client($client_id);

        if (!$patient) {
            echo json_encode(['success' => false, 'message' => 'Patient non trouvé']);
            return;
        }

        try {
            if (!$this->db->table_exists(db_prefix() . 'dietic_hydration_goals')) {
                echo json_encode(['success' => false, 'message' => 'Fonctionnalité non disponible']);
                return;
            }

            $daily_goal_ml = intval($this->input->post('daily_goal_ml'));

            if ($daily_goal_ml < 500 || $daily_goal_ml > 5000) {
                echo json_encode(['success' => false, 'message' => 'Objectif invalide (500-5000ml)']);
                return;
            }

            // Check if goal exists
            $this->db->where('patient_id', $patient->id);
            $existing = $this->db->get(db_prefix() . 'dietic_hydration_goals')->row();

            if ($existing) {
                // Update
                $this->db->where('patient_id', $patient->id);
                $this->db->update(db_prefix() . 'dietic_hydration_goals', [
                    'daily_goal_ml' => $daily_goal_ml,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            } else {
                // Insert
                $this->db->insert(db_prefix() . 'dietic_hydration_goals', [
                    'patient_id' => $patient->id,
                    'daily_goal_ml' => $daily_goal_ml,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }

            echo json_encode([
                'success' => true,
                'message' => 'Objectif mis à jour',
                'daily_goal_ml' => $daily_goal_ml
            ]);

        } catch (Exception $e) {
            log_activity('Error updating hydration goal: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Erreur serveur: ' . $e->getMessage()
            ]);
        }
    }

    // ========================================
    // ACTIVITY TRACKING METHODS
    // ========================================

    /**
     * Activities page - view for patients to track their activities
     */
    public function activities()
    {
        // Verify logged in patient
        $patient = $this->get_logged_in_patient();
        if (!$patient) {
            redirect(site_url('dietetic/portal'));
            return;
        }

        // Load activities model
        $this->load->model('dietetic/dietetic_activities_model');

        $data['patient'] = $patient;
        $this->load->view('portal/activities', $data);
    }

    /**
     * Get all available activities (for dropdown)
     */
    public function get_activities()
    {
        header('Content-Type: application/json');

        // Load model if not loaded
        if (!isset($this->dietetic_activities_model)) {
            $this->load->model('dietetic/dietetic_activities_model');
        }

        try {
            $activities = $this->dietetic_activities_model->get_all_activities(true);

            echo json_encode([
                'success' => true,
                'activities' => $activities
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Erreur lors du chargement des activités'
            ]);
        }
    }

    /**
     * Get patient's activities with statistics
     */
    public function get_my_activities()
    {
        header('Content-Type: application/json');

        $patient = $this->get_logged_in_patient();
        if (!$patient) {
            echo json_encode([
                'success' => false,
                'message' => 'Non connecté'
            ]);
            return;
        }

        // Load model if not loaded
        if (!isset($this->dietetic_activities_model)) {
            $this->load->model('dietetic/dietetic_activities_model');
        }

        try {
            $activities = $this->dietetic_activities_model->get_patient_activities($patient->id);
            $stats = $this->dietetic_activities_model->get_patient_stats($patient->id);

            echo json_encode([
                'success' => true,
                'activities' => $activities,
                'stats' => $stats
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Erreur lors du chargement'
            ]);
        }
    }

    /**
     * Test method to debug POST data
     */
    public function test_activity_post()
    {
        echo "<h1>Raw POST Data:</h1>";
        echo "<pre>";
        print_r($_POST);
        echo "</pre>";

        echo "<h1>CSRF Token Info:</h1>";
        $csrf_name = $this->security->get_csrf_token_name();
        $csrf_hash = $this->security->get_csrf_hash();

        echo "Token Name: " . $csrf_name . "<br>";
        echo "Expected Hash: " . $csrf_hash . "<br>";
        echo "Received Token from input->post(): " . $this->input->post($csrf_name) . "<br>";
        echo "Received Token from _POST: " . (isset($_POST[$csrf_name]) ? $_POST[$csrf_name] : 'NOT IN POST') . "<br>";

        echo "<br><strong>Looking for field name:</strong> &lt;input name=\"" . $csrf_name . "\"&gt;<br>";
        echo "<br><strong>CSRF Validation would expect to find:</strong> \$_POST['" . $csrf_name . "'] = '" . $csrf_hash . "'<br>";

        echo "<h1>Patient Info:</h1>";
        $patient = $this->get_logged_in_patient();
        if ($patient) {
            echo "Patient ID: " . $patient->id . "<br>";
            echo "Patient Name: " . ($patient->firstname ?? '') . " " . ($patient->lastname ?? '') . "<br>";
        } else {
            echo "NO PATIENT FOUND!<br>";
        }

        echo "<hr>";
        echo "<h2>Next Step:</h2>";
        echo "<p>Please check the page source (view source) and search for 'csrf_field' or 'csrf_token_name' to verify the hidden field exists in the form HTML.</p>";

        die();
    }

    /**
     * Add patient activity - Supports both POST redirect and JSON response
     */
    public function add_activity()
    {
        // Check if this is a redirect request (from dashboard)
        $redirect_to_dashboard = $this->input->post('redirect_to_dashboard');

        if (!$redirect_to_dashboard) {
            header('Content-Type: application/json');
            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            header('Pragma: no-cache');
        }

        $patient = $this->get_logged_in_patient();
        if (!$patient) {
            if ($redirect_to_dashboard) {
                set_alert('danger', 'Non connecté');
                redirect(site_url('dietetic/portal'));
                return;
            }
            echo json_encode(['success' => false, 'message' => 'Non connecté']);
            return;
        }

        // Validate required fields
        $activity_id = $this->input->post('activity_id');
        $duration_minutes = $this->input->post('duration_minutes');
        $kcal_burned = $this->input->post('kcal_burned');
        $activity_date = $this->input->post('activity_date');

        if (!$activity_id || !$duration_minutes || !$kcal_burned || !$activity_date) {
            if ($redirect_to_dashboard) {
                set_alert('danger', 'Tous les champs obligatoires doivent être remplis');
                redirect(site_url('dietetic/portal'));
                return;
            }
            echo json_encode(['success' => false, 'message' => 'Tous les champs obligatoires doivent être remplis']);
            return;
        }

        // Load model if not loaded
        if (!isset($this->dietetic_activities_model)) {
            $this->load->model('dietetic/dietetic_activities_model');
        }

        // Prepare data
        $data = [
            'patient_id' => $patient->id,
            'activity_id' => $activity_id,
            'duration_minutes' => $duration_minutes,
            'kcal_burned' => $kcal_burned,
            'activity_date' => $activity_date,
            'activity_time' => $this->input->post('activity_time'),
            'notes' => $this->input->post('notes'),
            'created_at' => date('Y-m-d H:i:s')
        ];

        try {
            $result = $this->dietetic_activities_model->add_patient_activity($data);

            if ($result) {
                // Award points for activity
                if ($this->is_gamification_ready()) {
                    try {
                        $this->dietetic_gamification_model->award_points(
                            $patient->id,
                            15,
                            'activity_logged',
                            "Activité sportive: {$duration_minutes} min - {$kcal_burned} kcal",
                            $result
                        );
                        // Check for badge unlocks
                        $this->dietetic_gamification_model->check_and_award_badges($patient->id);
                    } catch (Exception $e) {
                        // Silently log gamification errors - don't break the main flow
                        log_activity('Gamification error in add_activity: ' . $e->getMessage());
                    }
                }

                if ($redirect_to_dashboard) {
                    set_alert('success', 'Activité ajoutée avec succès');
                    redirect(site_url('dietetic/portal'));
                    return;
                }
                echo json_encode([
                    'success' => true,
                    'message' => 'Activité ajoutée avec succès',
                    'csrf_token' => $this->security->get_csrf_hash()
                ]);
            } else {
                // Get database error
                $db_error = $this->db->error();
                $error_message = 'Erreur lors de l\'ajout de l\'activité';
                if (!empty($db_error['message'])) {
                    $error_message .= ': ' . $db_error['message'];
                }

                if ($redirect_to_dashboard) {
                    set_alert('danger', $error_message);
                    redirect(site_url('dietetic/portal'));
                    return;
                }
                echo json_encode([
                    'success' => false,
                    'message' => $error_message,
                    'db_error' => $db_error,
                    'csrf_token' => $this->security->get_csrf_hash()
                ]);
            }
        } catch (Exception $e) {
            if ($redirect_to_dashboard) {
                set_alert('danger', 'Erreur: ' . $e->getMessage());
                redirect(site_url('dietetic/portal'));
                return;
            }
            echo json_encode([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage(),
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
        }
    }

    /**
     * Delete patient activity with CSRF protection
     */
    public function delete_activity($activity_id)
    {
        header('Content-Type: application/json');

        $patient = $this->get_logged_in_patient();
        if (!$patient) {
            echo json_encode([
                'success' => false,
                'message' => 'Non connecté',
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
            return;
        }

        // CSRF Protection - CodeIgniter validates automatically via security library
        // No need for manual validation as CI framework already handles it

        if (!$activity_id) {
            echo json_encode([
                'success' => false,
                'message' => 'ID d\'activité requis',
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
            return;
        }

        // Load model if not loaded
        if (!isset($this->dietetic_activities_model)) {
            $this->load->model('dietetic/dietetic_activities_model');
        }

        // Verify the activity belongs to this patient
        $activity = $this->dietetic_activities_model->get_patient_activity($activity_id);
        if (!$activity || $activity->patient_id != $patient->id) {
            echo json_encode([
                'success' => false,
                'message' => 'Activité non trouvée ou accès refusé',
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
            return;
        }

        try {
            $result = $this->dietetic_activities_model->delete_patient_activity($activity_id);

            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Activité supprimée avec succès',
                    'csrf_token' => $this->security->get_csrf_hash()
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Erreur lors de la suppression',
                    'csrf_token' => $this->security->get_csrf_hash()
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Erreur serveur: ' . $e->getMessage(),
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
        }
    }

    /**
     * API: Get today's activities for dashboard widget
     * Returns activities count, total minutes, and total calories for today
     */
    public function api_get_today_activities()
    {
        header('Content-Type: application/json');

        $patient = $this->get_logged_in_patient();
        if (!$patient) {
            echo json_encode([
                'success' => false,
                'message' => 'Non connecté'
            ]);
            return;
        }

        // Load activities model
        if (!isset($this->dietetic_activities_model)) {
            $this->load->model('dietetic/dietetic_activities_model');
        }

        try {
            // Get today's date
            $today = date('Y-m-d');

            // Get today's activities
            $activities = $this->dietetic_activities_model->get_patient_activities_by_date_range(
                $patient->id,
                $today,
                $today
            );

            // Calculate totals
            $total_minutes = 0;
            $total_kcal = 0;

            foreach ($activities as $activity) {
                $total_minutes += $activity->duration_minutes;
                $total_kcal += $activity->kcal_burned;
            }

            echo json_encode([
                'success' => true,
                'count' => count($activities),
                'total_minutes' => $total_minutes,
                'total_kcal' => $total_kcal,
                'activities' => $activities
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Erreur lors du chargement: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get logged in patient
     * Helper method to get current patient from logged in client
     *
     * @return object|null Patient object or null if not found
     */
    private function get_logged_in_patient()
    {
        // Check if client is logged in
        if (!is_client_logged_in()) {
            return null;
        }

        $client_id = get_client_user_id();

        // Get patient
        try {
            $patient = $this->dietetic_patients_model->get_by_client($client_id);
            return $patient;
        } catch (Exception $e) {
            log_activity('[DIETETIC ERROR] Failed to get patient: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Display patient subscriptions
     */
    public function subscriptions()
    {
        $patient = $this->get_logged_in_patient();

        if (!$patient) {
            redirect(site_url('authentication/login'));
        }

        $this->load->model('dietetic/dietetic_subscriptions_model');
        $this->load->model('dietetic/dietetic_service_plans_model');
        $this->load->model('dietetic/dietetic_recurring_payments_model');

        // Get patient's subscriptions
        $subscriptions = $this->dietetic_subscriptions_model->get_by_patient($patient->id);

        // For each subscription, get recurring payment info if applicable
        foreach ($subscriptions as $subscription) {
            if (!empty($subscription->recurring_payment_id)) {
                $subscription->recurring_payment = $this->dietetic_recurring_payments_model->get($subscription->recurring_payment_id);
            }
        }

        $data['subscriptions'] = $subscriptions;
        $data['patient'] = $patient;
        $data['title'] = 'Mes Abonnements';
        $data['active_page'] = 'subscriptions';

        // Get client info
        $this->load->model('clients_model');
        $data['client'] = $this->clients_model->get($patient->client_id);

        $this->data($data);
        $this->view('portal_subscriptions');
        $this->layout();
    }

    /**
     * View single subscription
     */
    public function subscription($id)
    {
        $patient = $this->get_logged_in_patient();

        if (!$patient) {
            redirect(site_url('authentication/login'));
        }

        $this->load->model('dietetic/dietetic_subscriptions_model');
        $this->load->model('dietetic/dietetic_recurring_payments_model');
        $this->load->model('dietetic/dietetic_invoices_model');

        $subscription = $this->dietetic_subscriptions_model->get($id);

        if (!$subscription || $subscription->patient_id != $patient->id) {
            show_404();
        }

        // Get recurring payment if applicable
        if (!empty($subscription->recurring_payment_id)) {
            $subscription->recurring_payment = $this->dietetic_recurring_payments_model->get($subscription->recurring_payment_id);

            // Get transactions for this recurring payment
            $subscription->recurring_transactions = $this->dietetic_recurring_payments_model->get_transactions($subscription->recurring_payment_id);
        }

        // Get invoices for this subscription
        $invoices = $this->dietetic_invoices_model->get_all(['i.subscription_id' => $id]);

        $data['subscription'] = $subscription;
        $data['invoices'] = $invoices;
        $data['patient'] = $patient;
        $data['title'] = 'Détails Abonnement';
        $data['active_page'] = 'subscriptions';

        // Get client info
        $this->load->model('clients_model');
        $data['client'] = $this->clients_model->get($patient->client_id);

        $this->data($data);
        $this->view('portal_subscription_view');
        $this->layout();
    }

    /**
     * Display patient invoices
     */
    public function invoices()
    {
        $patient = $this->get_logged_in_patient();

        if (!$patient) {
            redirect(site_url('authentication/login'));
        }

        $this->load->model('dietetic/dietetic_invoices_model');
        $this->load->model('dietetic/dietetic_subscriptions_model');

        // Get patient's invoices
        $invoices = $this->dietetic_invoices_model->get_by_patient($patient->id);

        $data['invoices'] = $invoices;
        $data['patient'] = $patient;
        $data['title'] = 'Mes Factures';

        // Check which payment methods are enabled
        $data['paypal_enabled'] = (bool) dietetic_get_option('paypal_enabled');
        $data['wave_enabled'] = (bool) dietetic_get_option('wave_enabled');
        $data['orange_money_enabled'] = (bool) dietetic_get_option('orange_money_enabled');
        $data['any_payment_enabled'] = $data['paypal_enabled'] || $data['wave_enabled'] || $data['orange_money_enabled'];

        $this->data($data);
        $this->view('portal/invoices');
        $this->layout();
    }

    /**
     * View single invoice
     */
    public function invoice($id)
    {
        $patient = $this->get_logged_in_patient();

        if (!$patient) {
            redirect(site_url('authentication/login'));
        }

        $this->load->model('dietetic/dietetic_invoices_model');
        $this->load->model('dietetic/dietetic_payments_model');

        $invoice = $this->dietetic_invoices_model->get($id);

        if (!$invoice || $invoice->patient_id != $patient->id) {
            show_404();
        }

        // Get payments for this invoice
        $payments = $this->dietetic_payments_model->get_all(['p.invoice_id' => $id]);

        $data['invoice'] = $invoice;
        $data['payments'] = $payments;
        $data['patient'] = $patient;
        $data['title'] = 'Facture ' . $invoice->invoice_number;

        // Check which payment methods are enabled
        $data['paypal_enabled'] = (bool) dietetic_get_option('paypal_enabled');
        $data['wave_enabled'] = (bool) dietetic_get_option('wave_enabled');
        $data['orange_money_enabled'] = (bool) dietetic_get_option('orange_money_enabled');
        $data['any_payment_enabled'] = $data['paypal_enabled'] || $data['wave_enabled'] || $data['orange_money_enabled'];

        $this->data($data);
        $this->view('portal/invoice');
        $this->layout();
    }

    // ==================== BLOG / CONSEILS ====================

    /**
     * Display blog articles list
     */
    public function blog()
    {
        // Check if patient is logged in
        if (!is_client_logged_in()) {
            redirect(site_url('authentication/login'));
            return;
        }

        $patient = $this->get_logged_in_patient();

        if (!$patient) {
            redirect(site_url('clients/login'));
            return;
        }

        $this->load->model('dietetic/dietetic_blog_model');

        // Pagination
        $per_page = 10;
        $page = $this->input->get('page', true) ? (int)$this->input->get('page', true) : 1;
        $offset = ($page - 1) * $per_page;

        // Filter by category
        $category = $this->input->get('category', true);

        // Get articles
        $articles = $this->dietetic_blog_model->get_published($per_page, $offset, $category);

        // Count total articles (filtered by category if applicable)
        if ($category) {
            $total_articles = count($this->dietetic_blog_model->get_published(999999, 0, $category));
        } else {
            $total_articles = $this->dietetic_blog_model->get_count([
                'status' => 'published',
                'published_at <=' => date('Y-m-d H:i:s')
            ]);
        }

        // Get categories
        $categories = $this->dietetic_blog_model->get_all_categories();

        // Get featured articles
        $featured = $this->dietetic_blog_model->get_featured(3);

        $data['articles'] = $articles;
        $data['categories'] = $categories;
        $data['featured'] = $featured;
        $data['current_category'] = $category;
        $data['total_pages'] = ceil($total_articles / $per_page);
        $data['current_page'] = $page;
        $data['patient'] = $patient;
        $data['title'] = 'Conseils & Blog';
        $data['active_page'] = 'blog';

        // Get client info
        $this->load->model('clients_model');
        $data['client'] = $this->clients_model->get($patient->client_id);

        // Load view directly - the view already includes portal_header and portal_footer
        $this->load->view('dietetic/portal/blog/index', $data);
    }

    /**
     * Display single blog article
     */
    public function blog_article($slug)
    {
        // Check if patient is logged in
        if (!is_client_logged_in()) {
            redirect(site_url('authentication/login'));
            return;
        }

        $patient = $this->get_logged_in_patient();

        if (!$patient) {
            redirect(site_url('clients/login'));
            return;
        }

        $this->load->model('dietetic/dietetic_blog_model');

        $article = $this->dietetic_blog_model->get($slug);

        if (!$article || $article->status != 'published') {
            show_404();
        }

        // Increment views
        $this->dietetic_blog_model->increment_views($article->id, $patient->id);

        // Get related articles from same category
        $related = [];
        if ($article->category) {
            $all_in_category = $this->dietetic_blog_model->get_published(4, 0, $article->category);
            // Remove current article from related
            $related = array_filter($all_in_category, function($a) use ($article) {
                return $a->id != $article->id;
            });
            $related = array_slice($related, 0, 3);
        }

        $data['article'] = $article;
        $data['related'] = $related;
        $data['patient'] = $patient;
        $data['title'] = $article->title;
        $data['active_page'] = 'blog';

        // Get client info
        $this->load->model('clients_model');
        $data['client'] = $this->clients_model->get($patient->client_id);

        // Load view directly - the view already includes portal_header and portal_footer
        $this->load->view('dietetic/portal/blog/article', $data);
    }

    /**
     * Search blog articles
     */
    public function blog_search()
    {
        // Check if patient is logged in
        if (!is_client_logged_in()) {
            redirect(site_url('authentication/login'));
            return;
        }

        $patient = $this->get_logged_in_patient();

        if (!$patient) {
            redirect(site_url('clients/login'));
            return;
        }

        $this->load->model('dietetic/dietetic_blog_model');

        $query = $this->input->get('q', true);

        $per_page = 10;
        $page = $this->input->get('page', true) ? (int)$this->input->get('page', true) : 1;
        $offset = ($page - 1) * $per_page;

        $articles = $this->dietetic_blog_model->search($query, $per_page, $offset);

        $data['articles'] = $articles;
        $data['search_query'] = $query;
        $data['patient'] = $patient;
        $data['title'] = 'Recherche: ' . $query;
        $data['active_page'] = 'blog';

        // Get client info
        $this->load->model('clients_model');
        $data['client'] = $this->clients_model->get($patient->client_id);

        // Load view directly - the view already includes portal_header and portal_footer
        $this->load->view('dietetic/portal/blog/search', $data);
    }

    /**
     * DIAGNOSTIC TOOL - Gamification System
     * Access: /dietetic/portal/gamification_diagnostic
     */
    public function gamification_diagnostic()
    {
        // Must be logged in as admin or patient
        if (!is_client_logged_in() && !is_staff_logged_in()) {
            die('Access denied. Please login first.');
        }

        // Get patient for testing
        $patient = null;
        if (is_client_logged_in()) {
            $client_id = get_client_user_id();
            $patient = $this->dietetic_patients_model->get_by_client($client_id);
        } else {
            // For admin, get first patient for testing
            $this->db->limit(1);
            $patient = $this->db->get(db_prefix() . 'dietic_patients')->row();
        }

        header('Content-Type: text/html; charset=utf-8');
        echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Diagnostic Gamification</title>';
        echo '<style>
            body { font-family: monospace; padding: 20px; background: #1a1a1a; color: #0f0; }
            h1, h2 { color: #0ff; border-bottom: 2px solid #0ff; padding-bottom: 5px; }
            .success { color: #0f0; font-weight: bold; }
            .error { color: #f00; font-weight: bold; }
            .warning { color: #ff0; font-weight: bold; }
            .info { color: #0ff; }
            pre { background: #000; padding: 10px; border-left: 3px solid #0f0; overflow-x: auto; }
            .test-section { margin: 20px 0; padding: 15px; border: 1px solid #333; background: #222; }
            .test-item { margin: 10px 0; padding: 5px; }
        </style></head><body>';

        echo '<h1>🔍 DIAGNOSTIC COMPLET DU SYSTÈME DE GAMIFICATION</h1>';
        echo '<p class="info">Date: ' . date('Y-m-d H:i:s') . '</p>';
        echo '<p class="info">Patient ID: ' . ($patient ? $patient->id : 'N/A') . '</p>';
        echo '<hr>';

        // TEST 1: Database Tables
        echo '<div class="test-section">';
        echo '<h2>TEST 1: Vérification des Tables de Base de Données</h2>';

        $required_tables = [
            'dietic_badge_definitions' => 'Définitions des badges',
            'dietic_patient_points' => 'Points des patients',
            'dietic_patient_badges' => 'Badges débloqués',
            'dietic_points_history' => 'Historique des points'
        ];

        $all_tables_exist = true;
        foreach ($required_tables as $table => $description) {
            $full_table_name = db_prefix() . $table;
            $exists = $this->db->table_exists($full_table_name);
            echo '<div class="test-item">';
            echo ($exists ? '✅ ' : '❌ ') . '<span class="' . ($exists ? 'success' : 'error') . '">';
            echo $full_table_name . '</span> - ' . $description;

            if ($exists) {
                // Count rows
                $count = $this->db->count_all($full_table_name);
                echo ' (' . $count . ' entrées)';
            }
            echo '</div>';

            if (!$exists) $all_tables_exist = false;
        }
        echo '</div>';

        // TEST 2: Model Loading
        echo '<div class="test-section">';
        echo '<h2>TEST 2: Chargement du Modèle de Gamification</h2>';

        try {
            $this->load->model('dietetic/dietetic_gamification_model');
            echo '<div class="test-item success">✅ Modèle chargé avec succès</div>';

            // Test method exists
            $methods = get_class_methods($this->dietetic_gamification_model);
            echo '<div class="test-item info">Méthodes disponibles: ' . count($methods) . '</div>';
            echo '<pre>' . implode("\n", array_slice($methods, 0, 10)) . "\n... (et " . (count($methods) - 10) . " autres)</pre>";
        } catch (Exception $e) {
            echo '<div class="test-item error">❌ Erreur de chargement: ' . $e->getMessage() . '</div>';
            echo '<pre>' . $e->getTraceAsString() . '</pre>';
        }
        echo '</div>';

        // TEST 3: is_gamification_ready() method
        echo '<div class="test-section">';
        echo '<h2>TEST 3: Méthode is_gamification_ready()</h2>';

        $is_ready = $this->is_gamification_ready();
        echo '<div class="test-item ' . ($is_ready ? 'success' : 'error') . '">';
        echo ($is_ready ? '✅ Gamification READY' : '❌ Gamification NOT READY');
        echo '</div>';
        echo '</div>';

        // TEST 4: Patient Points Record (only if tables exist)
        if ($all_tables_exist && $patient) {
            echo '<div class="test-section">';
            echo '<h2>TEST 4: Enregistrement Points du Patient</h2>';

            $this->db->where('patient_id', $patient->id);
            $patient_points = $this->db->get(db_prefix() . 'dietic_patient_points')->row();

            if ($patient_points) {
                echo '<div class="test-item success">✅ Patient a un enregistrement de points</div>';
                echo '<pre>' . print_r($patient_points, true) . '</pre>';
            } else {
                echo '<div class="test-item warning">⚠️ Patient n\'a PAS d\'enregistrement de points (sera créé automatiquement)</div>';

                // Try to create it
                try {
                    if (method_exists($this->dietetic_gamification_model, 'get_patient_points')) {
                        $points = $this->dietetic_gamification_model->get_patient_points($patient->id);
                        echo '<div class="test-item success">✅ Enregistrement créé automatiquement</div>';
                        echo '<pre>' . print_r($points, true) . '</pre>';
                    }
                } catch (Exception $e) {
                    echo '<div class="test-item error">❌ Erreur lors de la création: ' . $e->getMessage() . '</div>';
                }
            }
            echo '</div>';
        }

        // TEST 5: Test award_points() method
        if ($is_ready && $patient) {
            echo '<div class="test-section">';
            echo '<h2>TEST 5: Test de la Méthode award_points()</h2>';

            try {
                // Get points before
                $this->db->where('patient_id', $patient->id);
                $before = $this->db->get(db_prefix() . 'dietic_patient_points')->row();
                $points_before = $before ? $before->total_points : 0;

                echo '<div class="test-item info">Points avant: ' . $points_before . '</div>';

                // Award 1 point for testing
                $result = $this->dietetic_gamification_model->award_points(
                    $patient->id,
                    1,
                    'diagnostic_test',
                    'Test diagnostic du système'
                );

                // Get points after
                $this->db->where('patient_id', $patient->id);
                $after = $this->db->get(db_prefix() . 'dietic_patient_points')->row();
                $points_after = $after ? $after->total_points : 0;

                echo '<div class="test-item info">Points après: ' . $points_after . '</div>';

                if ($result && $points_after > $points_before) {
                    echo '<div class="test-item success">✅ Attribution de points FONCTIONNE</div>';
                } else {
                    echo '<div class="test-item error">❌ Attribution de points ÉCHOUE</div>';
                }
            } catch (Exception $e) {
                echo '<div class="test-item error">❌ Erreur: ' . $e->getMessage() . '</div>';
                echo '<pre>' . $e->getTraceAsString() . '</pre>';
            }
            echo '</div>';
        }

        // TEST 6: Test API toggle_meal with full error capture
        echo '<div class="test-section">';
        echo '<h2>TEST 6: Simulation API toggle_meal()</h2>';

        if ($patient) {
            ob_start();
            error_reporting(E_ALL);
            ini_set('display_errors', 1);

            try {
                // Simulate the exact flow
                $test_meal = 'breakfast';
                $test_checked = true;

                echo '<div class="test-item info">Simulation: Validation du petit-déjeuner...</div>';

                // Test the daily tracking update
                $success = $this->dietetic_daily_tracking_model->toggle_meal($patient->id, $test_meal, $test_checked);

                echo '<div class="test-item ' . ($success ? 'success' : 'error') . '">';
                echo ($success ? '✅' : '❌') . ' Toggle meal DB: ' . ($success ? 'OK' : 'FAIL');
                echo '</div>';

                if ($success && $test_checked && $is_ready) {
                    echo '<div class="test-item info">Tentative d\'attribution de points...</div>';

                    try {
                        $this->dietetic_gamification_model->award_points(
                            $patient->id,
                            5,
                            'meal_validated',
                            'Petit-déjeuner validé (test diagnostic)'
                        );
                        echo '<div class="test-item success">✅ Points attribués avec succès</div>';
                    } catch (Exception $e) {
                        echo '<div class="test-item error">❌ Erreur attribution: ' . $e->getMessage() . '</div>';
                        echo '<pre>' . $e->getTraceAsString() . '</pre>';
                    }
                }

            } catch (Exception $e) {
                echo '<div class="test-item error">❌ Exception: ' . $e->getMessage() . '</div>';
                echo '<pre>' . $e->getTraceAsString() . '</pre>';
            }

            $output = ob_get_clean();
            echo $output;
        } else {
            echo '<div class="test-item warning">⚠️ Pas de patient pour tester</div>';
        }
        echo '</div>';

        // TEST 7: PHP Error Log Check
        echo '<div class="test-section">';
        echo '<h2>TEST 7: Dernières Erreurs PHP</h2>';

        $error_log = ini_get('error_log');
        echo '<div class="test-item info">Error log path: ' . ($error_log ? $error_log : 'default') . '</div>';

        if ($error_log && file_exists($error_log)) {
            $last_lines = shell_exec('tail -50 ' . escapeshellarg($error_log));
            echo '<pre>' . htmlspecialchars($last_lines) . '</pre>';
        } else {
            echo '<div class="test-item warning">⚠️ Impossible d\'accéder au error log</div>';
        }
        echo '</div>';

        // SUMMARY
        echo '<div class="test-section">';
        echo '<h2>📊 RÉSUMÉ DU DIAGNOSTIC</h2>';

        echo '<div class="test-item">';
        echo '<strong>Tables:</strong> ' . ($all_tables_exist ? '<span class="success">✅ Toutes présentes</span>' : '<span class="error">❌ Manquantes</span>');
        echo '</div>';

        echo '<div class="test-item">';
        echo '<strong>Gamification Ready:</strong> ' . ($is_ready ? '<span class="success">✅ OUI</span>' : '<span class="error">❌ NON</span>');
        echo '</div>';

        echo '<div class="test-item">';
        echo '<strong>Recommandation:</strong> ';
        if (!$all_tables_exist) {
            echo '<span class="error">Exécutez la migration SQL depuis Admin → Diététique → Notifications → Migrations</span>';
        } elseif (!$is_ready) {
            echo '<span class="warning">Tables présentes mais système pas prêt - Vérifiez les erreurs ci-dessus</span>';
        } else {
            echo '<span class="success">Système opérationnel - Les erreurs viennent d\'ailleurs</span>';
        }
        echo '</div>';
        echo '</div>';

        echo '</body></html>';
        exit;
    }

    /**
     * TEST API WITH PHP FORMS (NO JAVASCRIPT)
     * Access: /dietetic/portal/test_api_php
     */
    public function test_api_php()
    {
        // Enable error reporting for debugging
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        try {
            if (!is_client_logged_in()) {
                die('Please login as patient first');
            }

            $client_id = get_client_user_id();
            $patient = $this->dietetic_patients_model->get_by_client($client_id);

            if (!$patient) {
                die('Patient not found');
            }

            // Handle form submissions
            $result = null;
            $test_type = null;

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $test_type = $this->input->post('test_type');

                switch ($test_type) {
                    case 'toggle_meal':
                        $meal = $this->input->post('meal');
                        $checked = $this->input->post('checked') === '1';

                        $success = $this->dietetic_daily_tracking_model->toggle_meal($patient->id, $meal, $checked);

                        if ($success && $checked && $this->is_gamification_ready()) {
                            try {
                                $meal_names = [
                                    'breakfast' => 'Petit-déjeuner',
                                    'lunch' => 'Déjeuner',
                                    'dinner' => 'Dîner'
                                ];
                                $this->dietetic_gamification_model->award_points(
                                    $patient->id,
                                    5,
                                    'meal_validated',
                                    $meal_names[$meal] . ' validé (test PHP)'
                                );
                                $this->dietetic_gamification_model->check_and_award_badges($patient->id);
                                $result = ['success' => true, 'message' => 'Repas validé + 5 points attribués'];
                            } catch (Exception $e) {
                                $result = ['success' => false, 'message' => 'Erreur gamification: ' . $e->getMessage()];
                            }
                        } else {
                            $result = ['success' => $success, 'message' => $success ? 'Repas validé (pas de points)' : 'Échec validation'];
                        }
                        break;

                    case 'add_hydration':
                        $quantity_ml = (int)$this->input->post('quantity_ml');

                        if ($quantity_ml > 0 && $quantity_ml <= 2000) {
                            $data = [
                                'patient_id' => $patient->id,
                                'quantity_ml' => $quantity_ml,
                                'tracking_date' => date('Y-m-d'),
                                'tracking_time' => date('H:i:s'),
                                'created_at' => date('Y-m-d H:i:s')
                            ];

                            $this->db->insert(db_prefix() . 'dietic_hydration_tracking', $data);
                            $entry_id = $this->db->insert_id();

                            if ($entry_id && $this->is_gamification_ready()) {
                                try {
                                    $this->dietetic_gamification_model->award_points(
                                        $patient->id,
                                        3,
                                        'hydration_logged',
                                        "Hydratation: {$quantity_ml}ml (test PHP)",
                                        $entry_id
                                    );
                                    $this->dietetic_gamification_model->check_and_award_badges($patient->id);
                                    $result = ['success' => true, 'message' => "Hydratation enregistrée ({$quantity_ml}ml) + 3 points"];
                                } catch (Exception $e) {
                                    $result = ['success' => false, 'message' => 'Erreur gamification: ' . $e->getMessage()];
                                }
                            } else {
                                $result = ['success' => $entry_id > 0, 'message' => $entry_id ? 'Hydratation enregistrée (pas de points)' : 'Échec enregistrement'];
                            }
                        } else {
                            $result = ['success' => false, 'message' => 'Quantité invalide (1-2000ml)'];
                        }
                        break;

                    case 'add_activity':
                        // Load model
                        if (!isset($this->dietetic_activities_model)) {
                            $this->load->model('dietetic/dietetic_activities_model');
                        }

                        $activity_data = [
                            'patient_id' => $patient->id,
                            'activity_id' => 1, // Default activity
                            'duration_minutes' => (int)$this->input->post('duration_minutes'),
                            'kcal_burned' => (int)$this->input->post('kcal_burned'),
                            'activity_date' => date('Y-m-d'),
                            'activity_time' => date('H:i:s'),
                            'notes' => 'Test PHP',
                            'created_at' => date('Y-m-d H:i:s')
                        ];

                        $activity_result = $this->dietetic_activities_model->add_patient_activity($activity_data);

                        if ($activity_result && $this->is_gamification_ready()) {
                            try {
                                $this->dietetic_gamification_model->award_points(
                                    $patient->id,
                                    15,
                                    'activity_logged',
                                    "Activité: {$activity_data['duration_minutes']}min (test PHP)",
                                    $activity_result
                                );
                                $this->dietetic_gamification_model->check_and_award_badges($patient->id);
                                $result = ['success' => true, 'message' => 'Activité ajoutée + 15 points'];
                            } catch (Exception $e) {
                                $result = ['success' => false, 'message' => 'Erreur gamification: ' . $e->getMessage()];
                            }
                        } else {
                            $result = ['success' => $activity_result > 0, 'message' => $activity_result ? 'Activité ajoutée (pas de points)' : 'Échec ajout'];
                        }
                        break;
                }
            }

            // Get current points
            $gamification_ready = $this->is_gamification_ready();
            if ($gamification_ready) {
                $this->db->where('patient_id', $patient->id);
                $patient_points = $this->db->get(db_prefix() . 'dietic_patient_points')->row();
                $current_points = $patient_points ? $patient_points->total_points : 0;
            } else {
                $current_points = 'N/A (gamification not ready)';
            }

            // Get CSRF token info
            $csrf_name = $this->security->get_csrf_token_name();
            $csrf_hash = $this->security->get_csrf_hash();

            // Display HTML page
            ?>
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>Test API PHP - Sans JavaScript</title>
                <style>
                    body { font-family: Arial; padding: 20px; background: #f5f5f5; }
                    .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                    h1 { color: #333; border-bottom: 3px solid #4CAF50; padding-bottom: 10px; }
                    h2 { color: #555; margin-top: 30px; }
                    .info-box { background: #e3f2fd; padding: 15px; border-left: 4px solid #2196F3; margin: 20px 0; }
                    .result { padding: 15px; margin: 20px 0; border-radius: 5px; font-weight: bold; }
                    .result.success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
                    .result.error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
                    .form-section { background: #f9f9f9; padding: 20px; margin: 20px 0; border-radius: 5px; border: 1px solid #ddd; }
                    .form-group { margin: 15px 0; }
                    label { display: block; font-weight: bold; margin-bottom: 5px; color: #555; }
                    input[type="number"], select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
                    button { background: #4CAF50; color: white; padding: 12px 30px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; font-weight: bold; }
                    button:hover { background: #45a049; }
                    .points-display { font-size: 24px; color: #4CAF50; font-weight: bold; text-align: center; padding: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 10px; margin: 20px 0; }
                </style>
            </head>
            <body>
                <div class="container">
                    <h1>🧪 TEST API GAMIFICATION - PHP PUR (SANS JAVASCRIPT)</h1>

                    <div class="info-box">
                        <strong>Patient ID:</strong> <?php echo htmlspecialchars($patient->id); ?><br>
                        <strong>Gamification Ready:</strong> <?php echo $gamification_ready ? '✅ OUI' : '❌ NON'; ?>
                    </div>

                    <div class="points-display">
                        🏆 Points Actuels: <?php echo htmlspecialchars($current_points); ?>
                    </div>

                    <?php if ($result): ?>
                        <div class="result <?php echo $result['success'] ? 'success' : 'error'; ?>">
                            <?php echo $result['success'] ? '✅' : '❌'; ?> <?php echo htmlspecialchars($result['message']); ?>
                        </div>
                    <?php endif; ?>

                    <!-- TEST 1: Toggle Meal -->
                    <div class="form-section">
                        <h2>TEST 1: Valider un Repas (+5 points)</h2>
                        <form method="POST">
                            <input type="hidden" name="test_type" value="toggle_meal">
                            <input type="hidden" name="<?php echo htmlspecialchars($csrf_name); ?>" value="<?php echo htmlspecialchars($csrf_hash); ?>">

                            <div class="form-group">
                                <label>Repas:</label>
                                <select name="meal" required>
                                    <option value="breakfast">Petit-déjeuner</option>
                                    <option value="lunch">Déjeuner</option>
                                    <option value="dinner">Dîner</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Action:</label>
                                <select name="checked" required>
                                    <option value="1">Valider (cocher)</option>
                                    <option value="0">Invalider (décocher)</option>
                                </select>
                            </div>

                            <button type="submit">🍽️ Tester Validation Repas</button>
                        </form>
                    </div>

                    <!-- TEST 2: Add Hydration -->
                    <div class="form-section">
                        <h2>TEST 2: Ajouter Hydratation (+3 points)</h2>
                        <form method="POST">
                            <input type="hidden" name="test_type" value="add_hydration">
                            <input type="hidden" name="<?php echo htmlspecialchars($csrf_name); ?>" value="<?php echo htmlspecialchars($csrf_hash); ?>">

                            <div class="form-group">
                                <label>Quantité (ml):</label>
                                <input type="number" name="quantity_ml" min="1" max="2000" value="250" required>
                            </div>

                            <button type="submit">💧 Tester Hydratation</button>
                        </form>
                    </div>

                    <!-- TEST 3: Add Activity -->
                    <div class="form-section">
                        <h2>TEST 3: Ajouter Activité (+15 points)</h2>
                        <form method="POST">
                            <input type="hidden" name="test_type" value="add_activity">
                            <input type="hidden" name="<?php echo htmlspecialchars($csrf_name); ?>" value="<?php echo htmlspecialchars($csrf_hash); ?>">

                            <div class="form-group">
                                <label>Durée (minutes):</label>
                                <input type="number" name="duration_minutes" min="1" value="30" required>
                            </div>

                            <div class="form-group">
                                <label>Calories brûlées:</label>
                                <input type="number" name="kcal_burned" min="1" value="150" required>
                            </div>

                            <button type="submit">🏃 Tester Activité</button>
                        </form>
                    </div>

                    <div class="info-box" style="margin-top: 30px;">
                        <strong>ℹ️ Comment utiliser :</strong><br>
                        1. Cliquez sur un bouton de test<br>
                        2. La page se recharge avec le résultat<br>
                        3. Vérifiez que les points augmentent<br>
                        4. Pas d'erreur = Backend fonctionne ✅<br>
                        5. Erreur = Problème serveur à identifier 🐛
                    </div>
                </div>
            </body>
            </html>
            <?php
        } catch (Exception $e) {
            // Display error in a user-friendly way
            ?>
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>Erreur - Test API PHP</title>
                <style>
                    body { font-family: Arial; padding: 20px; background: #f5f5f5; }
                    .error-container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                    .error-title { color: #d32f2f; border-bottom: 3px solid #d32f2f; padding-bottom: 10px; }
                    .error-details { background: #ffebee; padding: 15px; border-left: 4px solid #d32f2f; margin: 20px 0; }
                    pre { background: #f5f5f5; padding: 15px; border-radius: 5px; overflow-x: auto; }
                </style>
            </head>
            <body>
                <div class="error-container">
                    <h1 class="error-title">❌ Erreur PHP</h1>
                    <div class="error-details">
                        <strong>Message:</strong> <?php echo htmlspecialchars($e->getMessage()); ?><br>
                        <strong>File:</strong> <?php echo htmlspecialchars($e->getFile()); ?><br>
                        <strong>Line:</strong> <?php echo htmlspecialchars($e->getLine()); ?>
                    </div>
                    <h2>Stack Trace:</h2>
                    <pre><?php echo htmlspecialchars($e->getTraceAsString()); ?></pre>
                </div>
            </body>
            </html>
            <?php
        }
        exit;
    }

    /**
     * Display achievements and badges page
     */
    public function achievements()
    {
        // Check if patient is logged in
        if (!is_client_logged_in()) {
            redirect(site_url('authentication/login'));
            return;
        }

        $patient = $this->get_logged_in_patient();

        if (!$patient) {
            redirect(site_url('clients/login'));
            return;
        }

        $this->load->model('dietetic/dietetic_gamification_model');

        // Get all badge definitions grouped by category
        $all_badges = $this->dietetic_gamification_model->get_all_badges_with_progress($patient->id);

        // Get patient badges (unlocked)
        $patient_badges = $this->dietetic_gamification_model->get_patient_badges($patient->id);

        // Get patient points and level
        $patient_points = $this->dietetic_gamification_model->get_patient_points($patient->id);

        // Get points history
        $points_history = $this->dietetic_gamification_model->get_points_history($patient->id, 50);

        // Get level info
        $current_level_info = $this->dietetic_gamification_model->get_level_info($patient_points->current_level);
        $next_level_info = $this->dietetic_gamification_model->get_next_level_info($patient_points->current_level);

        $data['all_badges'] = $all_badges;
        $data['patient_badges'] = $patient_badges;
        $data['patient_points'] = $patient_points;
        $data['points_history'] = $points_history;
        $data['current_level_info'] = $current_level_info;
        $data['next_level_info'] = $next_level_info;
        $data['patient'] = $patient;
        $data['title'] = 'Mes Succès';
        $data['active_page'] = 'achievements';

        // Get client info
        $this->load->model('clients_model');
        $data['client'] = $this->clients_model->get($patient->client_id);

        // Load view
        $this->load->view('dietetic/portal/achievements/index', $data);
    }

    /**
     * Add meal reminders columns to notification_preferences table
     * Migration method - run once to add new columns
     */
    public function add_meal_reminders_columns()
    {
        // Admin only
        if (!is_staff_logged_in()) {
            echo json_encode(['success' => false, 'message' => 'Acces non autorise']);
            return;
        }

        $table = db_prefix() . 'dietic_notification_preferences';

        echo "<h2>Migration: Ajout colonnes rappels de repas</h2>";
        echo "<pre>";

        try {
            // Check if table exists
            if (!$this->db->table_exists($table)) {
                echo "ERROR: Table $table n existe pas\n";
                return;
            }

            echo "Table $table trouvee\n\n";

            // Check if columns already exist
            $fields = $this->db->field_data($table);
            $existing_fields = array_column($fields, 'name');

            $columns_to_add = [
                'reminder_breakfast' => "INT(1) DEFAULT 1 COMMENT 'Enable breakfast reminder'",
                'reminder_breakfast_time' => "TIME DEFAULT '08:00:00' COMMENT 'Breakfast reminder time'",
                'reminder_lunch' => "INT(1) DEFAULT 1 COMMENT 'Enable lunch reminder'",
                'reminder_lunch_time' => "TIME DEFAULT '12:30:00' COMMENT 'Lunch reminder time'",
                'reminder_dinner' => "INT(1) DEFAULT 1 COMMENT 'Enable dinner reminder'",
                'reminder_dinner_time' => "TIME DEFAULT '19:00:00' COMMENT 'Dinner reminder time'"
            ];

            foreach ($columns_to_add as $column => $definition) {
                if (in_array($column, $existing_fields)) {
                    echo "Colonne $column existe deja\n";
                } else {
                    $sql = "ALTER TABLE $table ADD COLUMN $column $definition";
                    if ($this->db->query($sql)) {
                        echo "Colonne $column ajoutee\n";
                    } else {
                        echo "ERREUR lors de l ajout de $column\n";
                    }
                }
            }

            echo "\n=== MIGRATION TERMINEE ===\n";
            echo "Colonnes rappels de repas ajoutees avec succes\n";
            echo "\nVous pouvez maintenant configurer les rappels de repas dans les preferences patient.\n";

        } catch (Exception $e) {
            echo "\nEXCEPTION: " . $e->getMessage() . "\n";
            echo "File: " . $e->getFile() . "\n";
            echo "Line: " . $e->getLine() . "\n";
        }

        echo "</pre>";
    }

    /**
     * Debug meal reminder - Outil de diagnostic pour les rappels de repas
     * Affiche les détails de configuration et teste la logique de fenêtre de temps
     * Access: /dietetic/portal/debug_meal_reminder?meal_type=breakfast&patient_id=X
     */
    public function debug_meal_reminder()
    {
        // Allow both admin and logged-in clients
        if (!is_staff_logged_in() && !is_client_logged_in()) {
            redirect('authentication/login');
        }

        $meal_type = $this->input->get('meal_type') ?: 'breakfast';
        $patient_id = $this->input->get('patient_id');

        // Load models
        $this->load->model('dietetic_notifications_model');

        echo '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Debug Rappel de Repas</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        h1 { color: #2c3e50; border-bottom: 3px solid #3498db; padding-bottom: 10px; }
        h2 { color: #34495e; margin-top: 30px; border-left: 4px solid #3498db; padding-left: 10px; }
        .info-box { background: #ecf0f1; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .warning { background: #fff3cd; color: #856404; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 12px; text-align: left; border: 1px solid #ddd; }
        th { background: #3498db; color: white; }
        tr:nth-child(even) { background: #f9f9f9; }
        .code { background: #2c3e50; color: #ecf0f1; padding: 15px; border-radius: 5px; overflow-x: auto; margin: 10px 0; font-family: monospace; }
        .badge { display: inline-block; padding: 3px 8px; border-radius: 3px; font-size: 12px; font-weight: bold; }
        .badge-yes { background: #28a745; color: white; }
        .badge-no { background: #dc3545; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Debug Rappel de Repas</h1>
        <div class="info-box">
            <strong>Heure actuelle:</strong> ' . date('Y-m-d H:i:s') . '<br>
            <strong>Type de repas:</strong> ' . htmlspecialchars($meal_type) . '<br>
            <strong>Patient ID:</strong> ' . ($patient_id ?: 'Tous') . '
        </div>';

        // Test de la logique de fenêtre de temps
        echo '<h2>1. Test de la fenêtre de temps (5 minutes)</h2>';
        $current_time = date('H:i:00');
        $time_5min_ago = date('H:i:00', strtotime('-5 minutes'));

        echo '<div class="info-box">';
        echo '<strong>Heure actuelle:</strong> ' . $current_time . '<br>';
        echo '<strong>5 minutes avant:</strong> ' . $time_5min_ago . '<br>';
        echo '<strong>Fenêtre de rappel:</strong> ' . $time_5min_ago . ' < reminder_time <= ' . $current_time;
        echo '</div>';

        // Get patients with meal reminder enabled
        echo '<h2>2. Patients avec rappel ' . htmlspecialchars($meal_type) . ' activé</h2>';

        $column_enabled = 'reminder_' . $meal_type;
        $column_time = 'reminder_' . $meal_type . '_time';

        $this->db->select('p.id as patient_id, p.email, p.phone as phonenumber, c.firstname, c.lastname,
                          prefs.' . $column_enabled . ' as enabled,
                          prefs.' . $column_time . ' as reminder_time,
                          prefs.channel_email, prefs.channel_sms, prefs.channel_whatsapp, prefs.channel_push');
        $this->db->from(db_prefix() . 'dietic_notification_preferences as prefs');
        $this->db->join(db_prefix() . 'dietic_patients as p', 'p.id = prefs.patient_id');
        $this->db->join(db_prefix() . 'contacts as c', 'c.userid = p.client_id AND c.is_primary = 1', 'left');
        $this->db->where('prefs.' . $column_enabled, 1);

        if ($patient_id) {
            $this->db->where('p.id', $patient_id);
        }

        $patients = $this->db->get()->result();

        if (empty($patients)) {
            echo '<div class="warning">❌ Aucun patient n\'a activé le rappel ' . htmlspecialchars($meal_type) . '</div>';
        } else {
            echo '<table>';
            echo '<tr>
                    <th>Patient ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Heure rappel</th>
                    <th>Dans fenêtre?</th>
                    <th>Canaux</th>
                  </tr>';

            foreach ($patients as $p) {
                $in_window = ($p->reminder_time > $time_5min_ago && $p->reminder_time <= $current_time);
                $channels = [];
                if ($p->channel_email) $channels[] = 'Email';
                if ($p->channel_sms) $channels[] = 'SMS';
                if ($p->channel_whatsapp) $channels[] = 'WhatsApp';
                if ($p->channel_push) $channels[] = 'Push';

                echo '<tr>';
                echo '<td>' . $p->patient_id . '</td>';
                echo '<td>' . htmlspecialchars($p->firstname . ' ' . $p->lastname) . '</td>';
                echo '<td>' . htmlspecialchars($p->email) . '</td>';
                echo '<td>' . htmlspecialchars($p->phonenumber) . '</td>';
                echo '<td><strong>' . $p->reminder_time . '</strong></td>';
                echo '<td><span class="badge badge-' . ($in_window ? 'yes' : 'no') . '">' . ($in_window ? 'OUI' : 'NON') . '</span></td>';
                echo '<td>' . implode(', ', $channels) . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        }

        // Check for already sent notifications today
        echo '<h2>3. Notifications déjà envoyées aujourd\'hui</h2>';

        $today_start = date('Y-m-d 00:00:00');
        $today_end = date('Y-m-d 23:59:59');

        $this->db->select('pn.*, p.email, c.firstname, c.lastname');
        $this->db->from(db_prefix() . 'dietic_patient_notifications as pn');
        $this->db->join(db_prefix() . 'dietic_patients as p', 'p.id = pn.patient_id');
        $this->db->join(db_prefix() . 'contacts as c', 'c.userid = p.client_id AND c.is_primary = 1', 'left');
        $this->db->where('pn.notification_type', 'reminder_' . $meal_type);
        $this->db->where('pn.created_at >=', $today_start);
        $this->db->where('pn.created_at <=', $today_end);

        if ($patient_id) {
            $this->db->where('pn.patient_id', $patient_id);
        }

        $this->db->order_by('pn.created_at', 'DESC');
        $sent_notifications = $this->db->get()->result();

        if (empty($sent_notifications)) {
            echo '<div class="info-box">ℹ️ Aucune notification ' . htmlspecialchars($meal_type) . ' envoyée aujourd\'hui</div>';
        } else {
            echo '<table>';
            echo '<tr>
                    <th>Patient</th>
                    <th>Email</th>
                    <th>Type</th>
                    <th>Message</th>
                    <th>Envoyé à</th>
                  </tr>';

            foreach ($sent_notifications as $notif) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($notif->firstname . ' ' . $notif->lastname) . '</td>';
                echo '<td>' . htmlspecialchars($notif->email) . '</td>';
                echo '<td>' . htmlspecialchars($notif->notification_type) . '</td>';
                echo '<td>' . htmlspecialchars(substr($notif->message, 0, 50)) . '...</td>';
                echo '<td>' . $notif->created_at . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        }

        // Test de la requête SQL exacte utilisée par le cron
        echo '<h2>4. Test de la requête SQL du cron</h2>';

        echo '<div class="code">';
        echo 'SELECT p.*, prefs.*, p.email, p.phone as phonenumber, c.firstname, c.lastname<br>';
        echo 'FROM ' . db_prefix() . 'dietic_notification_preferences as prefs<br>';
        echo 'JOIN ' . db_prefix() . 'dietic_patients as p ON p.id = prefs.patient_id<br>';
        echo 'LEFT JOIN ' . db_prefix() . 'contacts as c ON c.userid = p.client_id AND c.is_primary = 1<br>';
        echo 'WHERE prefs.' . $column_enabled . ' = 1<br>';
        echo 'AND prefs.' . $column_time . ' > \'' . $time_5min_ago . '\'<br>';
        echo 'AND prefs.' . $column_time . ' <= \'' . $current_time . '\'';
        echo '</div>';

        // Execute the exact query
        $this->db->select('p.*, prefs.*, p.email, p.phone as phonenumber, c.firstname, c.lastname');
        $this->db->from(db_prefix() . 'dietic_notification_preferences as prefs');
        $this->db->join(db_prefix() . 'dietic_patients as p', 'p.id = prefs.patient_id');
        $this->db->join(db_prefix() . 'contacts as c', 'c.userid = p.client_id AND c.is_primary = 1', 'left');
        $this->db->where('prefs.' . $column_enabled, 1);
        $this->db->where('prefs.' . $column_time . ' >', $time_5min_ago);
        $this->db->where('prefs.' . $column_time . ' <=', $current_time);

        if ($patient_id) {
            $this->db->where('p.id', $patient_id);
        }

        $query_results = $this->db->get()->result();

        echo '<h3>Résultats de la requête:</h3>';
        if (empty($query_results)) {
            echo '<div class="warning">❌ Aucun patient ne correspond aux critères de la fenêtre de temps actuelle</div>';
        } else {
            echo '<div class="success">✅ ' . count($query_results) . ' patient(s) trouvé(s) dans la fenêtre de temps</div>';

            echo '<table>';
            echo '<tr>
                    <th>Patient ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Heure rappel</th>
                  </tr>';

            foreach ($query_results as $p) {
                echo '<tr>';
                echo '<td>' . $p->patient_id . '</td>';
                echo '<td>' . htmlspecialchars($p->firstname . ' ' . $p->lastname) . '</td>';
                echo '<td>' . htmlspecialchars($p->email) . '</td>';
                echo '<td>' . htmlspecialchars($p->phonenumber) . '</td>';
                echo '<td><strong>' . $p->{$column_time} . '</strong></td>';
                echo '</tr>';
            }
            echo '</table>';
        }

        // Recommendations
        echo '<h2>5. Recommandations</h2>';
        echo '<div class="info-box">';

        if (empty($patients)) {
            echo '❌ <strong>Problème:</strong> Aucun patient n\'a activé le rappel ' . htmlspecialchars($meal_type) . '<br>';
            echo '➡️ <strong>Solution:</strong> Vérifiez les préférences de notifications du patient';
        } elseif (empty($query_results)) {
            echo '⚠️ <strong>Problème:</strong> Des patients ont activé le rappel mais aucun n\'est dans la fenêtre de temps actuelle<br>';
            echo '➡️ <strong>Solution:</strong> Vérifiez que l\'heure du rappel correspond à l\'heure d\'exécution du cron<br>';
            echo '➡️ Le cron doit s\'exécuter toutes les 5 minutes pour capturer tous les rappels';
        } else {
            echo '✅ <strong>Tout semble correct!</strong> Les patients sont dans la fenêtre de temps<br>';
            echo '➡️ Si les notifications ne sont pas envoyées, vérifiez:<br>';
            echo '&nbsp;&nbsp;&nbsp;1. Que le cron s\'exécute bien<br>';
            echo '&nbsp;&nbsp;&nbsp;2. Que les canaux de notification sont configurés (Email, SMS, etc.)<br>';
            echo '&nbsp;&nbsp;&nbsp;3. Les logs dans dietic_notification_logs pour voir les erreurs';
        }

        echo '</div>';

        // Access link for specific patient
        if (!$patient_id && !empty($patients)) {
            echo '<h2>6. Tester un patient spécifique</h2>';
            echo '<div class="info-box">';
            foreach ($patients as $p) {
                $test_url = site_url('dietetic/portal/debug_meal_reminder?meal_type=' . $meal_type . '&patient_id=' . $p->patient_id);
                echo '🔗 <a href="' . $test_url . '">Tester ' . htmlspecialchars($p->firstname . ' ' . $p->lastname) . '</a><br>';
            }
            echo '</div>';
        }

        echo '
    </div>
</body>
</html>';
    }

    /**
     * Check cron execution history
     * Vérifie les exécutions du cron et les notifications envoyées
     * Access: /dietetic/portal/check_cron_execution
     */
    public function check_cron_execution()
    {
        // Allow both admin and logged-in clients
        if (!is_staff_logged_in() && !is_client_logged_in()) {
            redirect('authentication/login');
        }

        echo '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Historique Cron & Notifications</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1400px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        h1 { color: #2c3e50; border-bottom: 3px solid #3498db; padding-bottom: 10px; }
        h2 { color: #34495e; margin-top: 30px; border-left: 4px solid #3498db; padding-left: 10px; }
        .info-box { background: #ecf0f1; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .warning { background: #fff3cd; color: #856404; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; font-size: 13px; }
        th, td { padding: 10px; text-align: left; border: 1px solid #ddd; }
        th { background: #3498db; color: white; font-weight: bold; }
        tr:nth-child(even) { background: #f9f9f9; }
        .code { background: #2c3e50; color: #ecf0f1; padding: 15px; border-radius: 5px; overflow-x: auto; margin: 10px 0; font-family: monospace; }
        .badge { display: inline-block; padding: 3px 8px; border-radius: 3px; font-size: 12px; font-weight: bold; }
        .badge-success { background: #28a745; color: white; }
        .badge-email { background: #007bff; color: white; }
        .badge-sms { background: #ffc107; color: #000; }
        .badge-whatsapp { background: #25D366; color: white; }
        .badge-push { background: #6f42c1; color: white; }
        .time-8am { background: #ffffcc; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📊 Historique Cron & Notifications</h1>
        <div class="info-box">
            <strong>Heure actuelle:</strong> ' . date('Y-m-d H:i:s') . '<br>
            <strong>Timezone:</strong> ' . date_default_timezone_get() . '
        </div>';

        // Check if notification_logs table exists
        if (!$this->db->table_exists(db_prefix() . 'dietic_notification_logs')) {
            echo '<div class="error">❌ La table dietic_notification_logs n\'existe pas</div>';
        } else {
            // Get today's notifications grouped by hour
            echo '<h2>1. Notifications envoyées aujourd\'hui (par heure)</h2>';

            $today_start = date('Y-m-d 00:00:00');
            $today_end = date('Y-m-d 23:59:59');

            $sql = "
                SELECT
                    DATE_FORMAT(created_at, '%Y-%m-%d %H:00:00') as hour,
                    COUNT(*) as total,
                    SUM(CASE WHEN channel = 'email' THEN 1 ELSE 0 END) as email_count,
                    SUM(CASE WHEN channel = 'sms' THEN 1 ELSE 0 END) as sms_count,
                    SUM(CASE WHEN channel = 'whatsapp' THEN 1 ELSE 0 END) as whatsapp_count,
                    SUM(CASE WHEN channel = 'push' THEN 1 ELSE 0 END) as push_count
                FROM " . db_prefix() . "dietic_notification_logs
                WHERE created_at >= ?
                AND created_at <= ?
                GROUP BY hour
                ORDER BY hour DESC
            ";

            $query = $this->db->query($sql, [$today_start, $today_end]);
            $hourly_stats = $query->result();

            if (empty($hourly_stats)) {
                echo '<div class="warning">⚠️ Aucune notification envoyée aujourd\'hui</div>';
            } else {
                echo '<table>';
                echo '<tr>
                        <th>Heure</th>
                        <th>Total</th>
                        <th>Email</th>
                        <th>SMS</th>
                        <th>WhatsApp</th>
                        <th>Push</th>
                      </tr>';

                foreach ($hourly_stats as $stat) {
                    $hour_formatted = date('H:i', strtotime($stat->hour));
                    $is_8am = (substr($stat->hour, 11, 2) == '08');

                    echo '<tr' . ($is_8am ? ' class="time-8am"' : '') . '>';
                    echo '<td><strong>' . $stat->hour . '</strong></td>';
                    echo '<td><strong>' . $stat->total . '</strong></td>';
                    echo '<td>' . $stat->email_count . '</td>';
                    echo '<td>' . $stat->sms_count . '</td>';
                    echo '<td>' . $stat->whatsapp_count . '</td>';
                    echo '<td>' . $stat->push_count . '</td>';
                    echo '</tr>';
                }
                echo '</table>';

                // Check if 8am notifications exist
                $has_8am = false;
                foreach ($hourly_stats as $stat) {
                    if (substr($stat->hour, 11, 2) == '08') {
                        $has_8am = true;
                        break;
                    }
                }

                if (!$has_8am) {
                    echo '<div class="error">❌ <strong>PROBLÈME DÉTECTÉ:</strong> Aucune notification envoyée à 8h00 ce matin!</div>';
                    echo '<div class="info-box">';
                    echo '➡️ <strong>Cause probable:</strong> Le cron ne s\'est pas exécuté entre 8h00 et 8h05<br>';
                    echo '➡️ <strong>Solution:</strong> Vérifier la configuration du cron job dans Perfex CRM';
                    echo '</div>';
                }
            }

            // Get detailed notifications for breakfast today
            echo '<h2>2. Détails des notifications breakfast aujourd\'hui</h2>';

            $this->db->select('nl.*, p.email, c.firstname, c.lastname');
            $this->db->from(db_prefix() . 'dietic_notification_logs as nl');
            $this->db->join(db_prefix() . 'dietic_patients as p', 'p.id = nl.patient_id', 'left');
            $this->db->join(db_prefix() . 'contacts as c', 'c.userid = p.client_id AND c.is_primary = 1', 'left');
            $this->db->where('nl.notification_type', 'reminder_breakfast');
            $this->db->where('nl.created_at >=', $today_start);
            $this->db->where('nl.created_at <=', $today_end);
            $this->db->order_by('nl.created_at', 'DESC');

            $breakfast_logs = $this->db->get()->result();

            if (empty($breakfast_logs)) {
                echo '<div class="error">❌ Aucune notification breakfast envoyée aujourd\'hui</div>';
            } else {
                echo '<div class="success">✅ ' . count($breakfast_logs) . ' notification(s) breakfast envoyée(s) aujourd\'hui</div>';

                echo '<table>';
                echo '<tr>
                        <th>Patient</th>
                        <th>Email</th>
                        <th>Canal</th>
                        <th>Statut</th>
                        <th>Heure d\'envoi</th>
                        <th>Message</th>
                      </tr>';

                foreach ($breakfast_logs as $log) {
                    $channel_badge = '';
                    switch ($log->channel) {
                        case 'email': $channel_badge = '<span class="badge badge-email">Email</span>'; break;
                        case 'sms': $channel_badge = '<span class="badge badge-sms">SMS</span>'; break;
                        case 'whatsapp': $channel_badge = '<span class="badge badge-whatsapp">WhatsApp</span>'; break;
                        case 'push': $channel_badge = '<span class="badge badge-push">Push</span>'; break;
                    }

                    $status_badge = $log->status == 'sent' ?
                        '<span class="badge badge-success">Envoyé</span>' :
                        '<span class="badge badge-error">Échec</span>';

                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($log->firstname . ' ' . $log->lastname) . '</td>';
                    echo '<td>' . htmlspecialchars($log->email) . '</td>';
                    echo '<td>' . $channel_badge . '</td>';
                    echo '<td>' . $status_badge . '</td>';
                    echo '<td><strong>' . $log->created_at . '</strong></td>';
                    echo '<td>' . htmlspecialchars(substr($log->message, 0, 50)) . '...</td>';
                    echo '</tr>';
                }
                echo '</table>';
            }

            // Get last 100 notifications (all types)
            echo '<h2>3. Dernières 100 notifications (tous types)</h2>';

            $this->db->select('nl.*, p.email, c.firstname, c.lastname');
            $this->db->from(db_prefix() . 'dietic_notification_logs as nl');
            $this->db->join(db_prefix() . 'dietic_patients as p', 'p.id = nl.patient_id', 'left');
            $this->db->join(db_prefix() . 'contacts as c', 'c.userid = p.client_id AND c.is_primary = 1', 'left');
            $this->db->order_by('nl.created_at', 'DESC');
            $this->db->limit(100);

            $recent_logs = $this->db->get()->result();

            if (empty($recent_logs)) {
                echo '<div class="warning">⚠️ Aucune notification dans les logs</div>';
            } else {
                echo '<div class="info-box">Affichage des ' . count($recent_logs) . ' dernières notifications</div>';

                echo '<table>';
                echo '<tr>
                        <th>Patient</th>
                        <th>Type</th>
                        <th>Canal</th>
                        <th>Statut</th>
                        <th>Heure</th>
                      </tr>';

                foreach ($recent_logs as $log) {
                    $channel_badge = '';
                    switch ($log->channel) {
                        case 'email': $channel_badge = '<span class="badge badge-email">Email</span>'; break;
                        case 'sms': $channel_badge = '<span class="badge badge-sms">SMS</span>'; break;
                        case 'whatsapp': $channel_badge = '<span class="badge badge-whatsapp">WhatsApp</span>'; break;
                        case 'push': $channel_badge = '<span class="badge badge-push">Push</span>'; break;
                    }

                    $status_badge = $log->status == 'sent' ?
                        '<span class="badge badge-success">Envoyé</span>' :
                        '<span class="badge badge-error">Échec</span>';

                    $is_breakfast_8am = ($log->notification_type == 'reminder_breakfast' &&
                                        substr($log->created_at, 11, 2) == '08');

                    echo '<tr' . ($is_breakfast_8am ? ' class="time-8am"' : '') . '>';
                    echo '<td>' . htmlspecialchars($log->firstname . ' ' . $log->lastname) . '</td>';
                    echo '<td>' . htmlspecialchars($log->notification_type) . '</td>';
                    echo '<td>' . $channel_badge . '</td>';
                    echo '<td>' . $status_badge . '</td>';
                    echo '<td>' . $log->created_at . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            }
        }

        // Check Perfex cron configuration
        echo '<h2>4. Configuration Cron Perfex</h2>';
        echo '<div class="info-box">';
        echo '<strong>Vérifications à faire:</strong><br><br>';
        echo '1. <strong>Cron Job Perfex:</strong> Vérifier que le cron est configuré pour s\'exécuter toutes les 5 minutes<br>';
        echo '&nbsp;&nbsp;&nbsp;<code>*/5 * * * * php /path/to/perfex/index.php cron/run</code><br><br>';
        echo '2. <strong>Module Dietetic Hook:</strong> Vérifier que le hook after_cron_run est actif<br>';
        echo '&nbsp;&nbsp;&nbsp;Fichier: modules/dietetic/dietetic.php<br><br>';
        echo '3. <strong>Logs Serveur:</strong> Consulter les logs du serveur pour voir les exécutions du cron<br>';
        echo '&nbsp;&nbsp;&nbsp;<code>grep "cron" /var/log/apache2/access.log</code> ou <code>/var/log/nginx/access.log</code><br><br>';
        echo '4. <strong>Test Manuel:</strong> Exécuter le cron manuellement pour tester<br>';
        echo '&nbsp;&nbsp;&nbsp;<code>php /path/to/perfex/index.php cron/run</code>';
        echo '</div>';

        // Recommendations
        echo '<h2>5. Diagnostic Final</h2>';

        if (empty($hourly_stats)) {
            echo '<div class="error">';
            echo '❌ <strong>PROBLÈME CRITIQUE:</strong> Aucune notification n\'a été envoyée aujourd\'hui<br><br>';
            echo '<strong>Causes possibles:</strong><br>';
            echo '1. Le cron job n\'est pas configuré ou ne s\'exécute pas<br>';
            echo '2. Le module Dietetic n\'est pas activé<br>';
            echo '3. Le hook after_cron_run n\'est pas enregistré<br><br>';
            echo '<strong>Actions à prendre:</strong><br>';
            echo '➡️ Vérifier la configuration du cron job dans le cPanel ou via SSH<br>';
            echo '➡️ Tester l\'exécution manuelle du cron: <code>php index.php cron/run</code><br>';
            echo '➡️ Vérifier les logs du serveur pour voir si le cron est appelé';
            echo '</div>';
        } else {
            $has_8am = false;
            foreach ($hourly_stats as $stat) {
                if (substr($stat->hour, 11, 2) == '08') {
                    $has_8am = true;
                    break;
                }
            }

            if (!$has_8am && date('H') >= 8) {
                echo '<div class="error">';
                echo '❌ <strong>PROBLÈME:</strong> Le cron a envoyé des notifications mais PAS à 8h00<br><br>';
                echo '<strong>Cause probable:</strong><br>';
                echo 'Le cron job ne s\'est pas exécuté entre 8h00 et 8h05 ce matin<br><br>';
                echo '<strong>Solutions:</strong><br>';
                echo '➡️ Vérifier que le cron s\'exécute TOUTES LES 5 MINUTES (pas toutes les heures)<br>';
                echo '➡️ Configuration correcte: <code>*/5 * * * *</code> (toutes les 5 minutes)<br>';
                echo '➡️ Configuration incorrecte: <code>0 * * * *</code> (toutes les heures à :00)';
                echo '</div>';
            } else {
                echo '<div class="success">';
                echo '✅ Le système de notifications fonctionne correctement';
                echo '</div>';
            }
        }

        echo '
    </div>
</body>
</html>';
    }

    /**
     * Vérifier si le cron de Perfex s'exécute réellement
     * URL: /dietetic/portal/check_perfex_cron
     */
    public function check_perfex_cron()
    {
        // Allow both admin and logged-in clients
        if (!is_staff_logged_in() && !is_client_logged_in()) {
            redirect('authentication/login');
        }

        $this->db->order_by('id', 'DESC');
        $this->db->limit(50);
        $this->db->like('description', 'Dietetic Cron', 'both');
        $logs = $this->db->get(db_prefix() . 'activity_log')->result_array();

        echo '<!DOCTYPE html>
<html>
<head>
    <title>Vérification Cron Perfex - Dietetic</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 3px solid #4CAF50; padding-bottom: 10px; }
        h2 { color: #555; margin-top: 30px; border-bottom: 2px solid #2196F3; padding-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th { background: #4CAF50; color: white; padding: 12px; text-align: left; font-weight: bold; }
        td { padding: 10px; border-bottom: 1px solid #ddd; }
        tr:hover { background: #f5f5f5; }
        .success { color: #4CAF50; font-weight: bold; }
        .error { color: #f44336; font-weight: bold; }
        .warning { color: #ff9800; font-weight: bold; }
        .info { background: #e3f2fd; padding: 15px; border-left: 4px solid #2196F3; margin: 20px 0; border-radius: 4px; }
        .error-box { background: #ffebee; padding: 15px; border-left: 4px solid #f44336; margin: 20px 0; border-radius: 4px; }
        .success-box { background: #e8f5e9; padding: 15px; border-left: 4px solid #4CAF50; margin: 20px 0; border-radius: 4px; }
        code { background: #f5f5f5; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
        .timestamp { color: #666; font-size: 0.9em; }
        .cron-status { padding: 15px; margin: 20px 0; border-radius: 8px; }
        .status-running { background: #e8f5e9; border: 2px solid #4CAF50; }
        .status-stopped { background: #ffebee; border: 2px solid #f44336; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Vérification du Cron Perfex</h1>
        <p><strong>Date/Heure actuelle :</strong> ' . date('Y-m-d H:i:s') . '</p>
        ';

        // Analyse des logs
        $cron_executions = [];
        $last_start = null;
        $last_end = null;
        $errors = [];

        foreach ($logs as $log) {
            if (strpos($log['description'], 'Démarrage') !== false) {
                $last_start = $log['date'];
            } elseif (strpos($log['description'], 'Fin à') !== false) {
                $last_end = $log['date'];
                $cron_executions[] = $log;
            } elseif (strpos($log['description'], 'Error') !== false) {
                $errors[] = $log;
            }
        }

        // Statut du cron
        $now = time();
        $last_run_time = $last_end ? strtotime($last_end) : ($last_start ? strtotime($last_start) : null);
        $minutes_since_last_run = $last_run_time ? round(($now - $last_run_time) / 60) : null;

        echo '<div class="cron-status ' . ($minutes_since_last_run && $minutes_since_last_run <= 10 ? 'status-running' : 'status-stopped') . '">';

        if ($last_run_time) {
            if ($minutes_since_last_run <= 10) {
                echo '<p class="success">✅ <strong>CRON ACTIF</strong> - Dernière exécution il y a ' . $minutes_since_last_run . ' minute(s)</p>';
                echo '<p>Le cron fonctionne correctement (exécution toutes les 5 minutes attendue).</p>';
            } else {
                echo '<p class="error">❌ <strong>CRON ARRÊTÉ</strong> - Dernière exécution il y a ' . $minutes_since_last_run . ' minute(s)</p>';
                echo '<p>Le cron devrait s\'exécuter toutes les 5 minutes. Il semble arrêté depuis plus de 10 minutes.</p>';
            }
            echo '<p><strong>Dernière exécution :</strong> ' . ($last_end ?: $last_start) . '</p>';
        } else {
            echo '<p class="error">❌ <strong>AUCUNE EXÉCUTION DÉTECTÉE</strong></p>';
            echo '<p>Aucune trace d\'exécution du cron dans les logs. Le cron ne semble jamais avoir démarré.</p>';
        }

        echo '</div>';

        // Erreurs récentes
        if (!empty($errors)) {
            echo '<div class="error-box">';
            echo '<h3>⚠️ Erreurs récentes (' . count($errors) . ')</h3>';
            echo '<table>';
            echo '<tr><th>Date</th><th>Message</th></tr>';
            foreach (array_slice($errors, 0, 10) as $error) {
                echo '<tr>';
                echo '<td class="timestamp">' . htmlspecialchars($error['date']) . '</td>';
                echo '<td class="error">' . htmlspecialchars($error['description']) . '</td>';
                echo '</tr>';
            }
            echo '</table>';
            echo '</div>';
        }

        // Historique des exécutions
        echo '<h2>📊 Historique des 20 dernières exécutions</h2>';

        if (!empty($cron_executions)) {
            echo '<table>';
            echo '<tr><th>Date</th><th>Résultat</th></tr>';
            foreach (array_slice($cron_executions, 0, 20) as $exec) {
                echo '<tr>';
                echo '<td class="timestamp">' . htmlspecialchars($exec['date']) . '</td>';
                echo '<td>' . htmlspecialchars($exec['description']) . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo '<div class="error-box"><p>Aucune exécution complète enregistrée.</p></div>';
        }

        // Recommandations
        echo '<h2>💡 Diagnostic et Solutions</h2>';

        if (!$last_run_time || $minutes_since_last_run > 10) {
            echo '<div class="error-box">';
            echo '<h3>❌ Problème: Le cron ne s\'exécute pas automatiquement</h3>';
            echo '<p><strong>Configuration actuelle du cron serveur :</strong></p>';
            echo '<code>*/5 * * * * /usr/bin/php /home/trpuftja/app/index.php cron/index</code>';
            echo '<p><strong>Vérifications à effectuer :</strong></p>';
            echo '<ol>';
            echo '<li><strong>Vérifier que le cron est bien configuré dans cPanel :</strong>';
            echo '<ul>';
            echo '<li>Connectez-vous à cPanel</li>';
            echo '<li>Allez dans "Cron Jobs"</li>';
            echo '<li>Vérifiez que la tâche existe et est active</li>';
            echo '<li>Vérifiez que le chemin est correct : <code>/usr/bin/php /home/trpuftja/app/index.php cron/index</code></li>';
            echo '</ul></li>';
            echo '<li><strong>Tester manuellement le cron via SSH :</strong>';
            echo '<pre style="background:#f5f5f5;padding:10px;border-radius:4px;">cd /home/trpuftja/app
php index.php cron/index</pre>';
            echo '<p>Puis rechargez cette page pour voir si une nouvelle exécution apparaît.</p>';
            echo '</li>';
            echo '<li><strong>Vérifier les permissions :</strong>';
            echo '<pre style="background:#f5f5f5;padding:10px;border-radius:4px;">ls -la /home/trpuftja/app/index.php</pre>';
            echo '<p>Le fichier doit être lisible et exécutable.</p>';
            echo '</li>';
            echo '<li><strong>Vérifier les logs du serveur :</strong>';
            echo '<ul>';
            echo '<li>Dans cPanel, consultez les logs d\'erreurs</li>';
            echo '<li>Cherchez des erreurs liées au cron</li>';
            echo '</ul></li>';
            echo '</ol>';
            echo '</div>';
        } else {
            echo '<div class="success-box">';
            echo '<h3>✅ Le cron fonctionne correctement</h3>';
            echo '<p>Le cron s\'exécute automatiquement toutes les 5 minutes comme prévu.</p>';
            echo '<p>Si vous ne recevez toujours pas de notifications, le problème se situe dans la logique de détection des rappels à envoyer.</p>';
            echo '</div>';
        }

        echo '<div class="info">';
        echo '<h3>ℹ️ Comment fonctionne le système</h3>';
        echo '<p><strong>1. Cron du serveur</strong> (toutes les 5 minutes) :</p>';
        echo '<code>*/5 * * * * /usr/bin/php /home/trpuftja/app/index.php cron/index</code>';
        echo '<p>↓</p>';
        echo '<p><strong>2. Perfex exécute son cron</strong> (<code>/application/controllers/Cron.php</code>)</p>';
        echo '<p>↓</p>';
        echo '<p><strong>3. Perfex déclenche le hook</strong> <code>after_cron_run</code></p>';
        echo '<p>↓</p>';
        echo '<p><strong>4. Notre fonction s\'exécute</strong> : <code>dietetic_send_scheduled_reminders()</code></p>';
        echo '<p>↓</p>';
        echo '<p><strong>5. Les notifications sont envoyées</strong> selon les configurations des patients</p>';
        echo '</div>';

        echo '<div style="margin-top: 30px; padding: 15px; background: #f5f5f5; border-radius: 4px;">';
        echo '<p><strong>🔄 Actions rapides :</strong></p>';
        echo '<ul>';
        echo '<li><a href="' . base_url('dietetic/portal/check_perfex_cron') . '" style="color: #2196F3;">Recharger cette page</a> pour voir les nouvelles exécutions</li>';
        echo '<li><a href="' . base_url('dietetic/portal/debug_meal_reminder?meal_type=breakfast') . '" style="color: #2196F3;">Tester la détection des rappels de petit-déjeuner</a></li>';
        echo '<li><a href="' . base_url('dietetic/portal/check_cron_execution') . '" style="color: #2196F3;">Voir l\'historique des notifications envoyées</a></li>';
        echo '</ul>';
        echo '</div>';

        echo '
    </div>
</body>
</html>';
    }

    /**
     * Tester l'exécution complète du cron manuellement
     * URL: /dietetic/portal/test_cron_complete
     */
    public function test_cron_complete()
    {
        // Permettre l'accès aux admins et clients connectés
        if (!is_staff_logged_in() && !is_client_logged_in()) {
            redirect('authentication/login');
        }

        $this->load->model('dietetic_notifications_model');

        $start_time = microtime(true);
        $total_sent = 0;
        $total_failed = 0;
        $details = [];
        $errors = [];

        echo '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Test Cron Complet - Dietetic</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1400px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 3px solid #4CAF50; padding-bottom: 10px; }
        h2 { color: #555; margin-top: 30px; border-bottom: 2px solid #2196F3; padding-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th { background: #4CAF50; color: white; padding: 12px; text-align: left; }
        td { padding: 10px; border-bottom: 1px solid #ddd; }
        tr:hover { background: #f5f5f5; }
        .badge { display: inline-block; padding: 4px 8px; border-radius: 3px; font-size: 0.9em; font-weight: bold; }
        .badge-success { background: #4CAF50; color: white; }
        .badge-error { background: #f44336; color: white; }
        .info { background: #e3f2fd; padding: 15px; border-left: 4px solid #2196F3; margin: 20px 0; border-radius: 4px; }
        .success { background: #e8f5e9; padding: 15px; border-left: 4px solid #4CAF50; margin: 20px 0; border-radius: 4px; }
        .warning { background: #fff3e0; padding: 15px; border-left: 4px solid #ff9800; margin: 20px 0; border-radius: 4px; }
        .error { background: #ffebee; padding: 15px; border-left: 4px solid #f44336; margin: 20px 0; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Test Complet du Cron Dietetic</h1>
        <p><strong>Date/Heure :</strong> ' . date('Y-m-d H:i:s') . ' (Timezone: Africa/Dakar)</p>';

        try {
            // ==================== MEAL REMINDERS ====================
            $meal_types = ['breakfast' => '🥐 Petit-Déjeuner', 'lunch' => '🍽️ Déjeuner', 'dinner' => '🍴 Dîner'];
            foreach ($meal_types as $meal_type => $meal_label) {
                echo '<h2>' . $meal_label . '</h2>';

                $meal_patients = $this->dietetic_notifications_model->get_patients_for_meal_reminder($meal_type);

                // Afficher la fenêtre de temps
                $current_time = date('H:i:00');
                $time_5min_ago = date('H:i:00', strtotime('-5 minutes'));
                echo '<div class="info">';
                echo '<p><strong>Fenêtre de temps :</strong> ' . $time_5min_ago . ' &lt; heure_configurée ≤ ' . $current_time . '</p>';
                echo '<p><strong>Patients trouvés :</strong> ' . count($meal_patients) . '</p>';
                echo '</div>';

                if (!empty($meal_patients)) {
                    echo '<table><tr><th>Patient</th><th>Heure configurée</th><th>Email</th><th>Téléphone</th><th>Résultat</th></tr>';
                    foreach ($meal_patients as $patient) {
                        $result = $this->dietetic_notifications_model->send_meal_reminder($patient, $meal_type);
                        $success_count = is_array($result) ? count(array_filter($result, function($r) { return $r === true; })) : 0;
                        $total_sent += $success_count;
                        if (empty($success_count)) {
                            $total_failed++;
                        }

                        $configured_time = $patient->{'reminder_' . $meal_type . '_time'} ?? 'N/A';

                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($patient->firstname ?? 'N/A') . '</td>';
                        echo '<td><strong>' . htmlspecialchars($configured_time) . '</strong></td>';
                        echo '<td>' . htmlspecialchars($patient->email ?? 'N/A') . '</td>';
                        echo '<td>' . htmlspecialchars($patient->phonenumber ?? 'N/A') . '</td>';
                        echo '<td>' . ($success_count > 0 ? '<span class="badge badge-success">✓ Envoyé (' . $success_count . ')</span>' : '<span class="badge badge-error">✗ Échec</span>') . '</td>';
                        echo '</tr>';
                    }
                    echo '</table>';
                } else {
                    echo '<p class="warning">Aucun patient dans la fenêtre de temps.</p>';
                }
            }

            // ==================== WEIGHT REMINDERS ====================
            echo '<h2>⚖️ Rappels de Pesée</h2>';
            $weight_patients = $this->dietetic_notifications_model->get_patients_for_weight_reminder();
            echo '<p><strong>Patients trouvés :</strong> ' . count($weight_patients) . '</p>';

            if (!empty($weight_patients)) {
                echo '<table><tr><th>Patient</th><th>Email</th><th>Résultat</th></tr>';
                foreach ($weight_patients as $patient) {
                    $result = $this->dietetic_notifications_model->send_weight_reminder($patient);
                    $success_count = is_array($result) ? count(array_filter($result, function($r) { return $r === true; })) : 0;
                    $total_sent += $success_count;

                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($patient->firstname ?? 'N/A') . '</td>';
                    echo '<td>' . htmlspecialchars($patient->email ?? 'N/A') . '</td>';
                    echo '<td>' . ($success_count > 0 ? '<span class="badge badge-success">✓ ' . $success_count . '</span>' : '<span class="badge badge-error">✗</span>') . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            }

            // ==================== WATER REMINDERS ====================
            echo '<h2>💧 Rappels d\'Hydratation</h2>';
            $water_patients = $this->dietetic_notifications_model->get_patients_for_water_reminder();
            echo '<p><strong>Patients trouvés :</strong> ' . count($water_patients) . '</p>';

            if (!empty($water_patients)) {
                echo '<table><tr><th>Patient</th><th>Email</th><th>Résultat</th></tr>';
                foreach ($water_patients as $patient) {
                    $result = $this->dietetic_notifications_model->send_water_reminder($patient);
                    $success_count = is_array($result) ? count(array_filter($result, function($r) { return $r === true; })) : 0;
                    $total_sent += $success_count;

                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($patient->firstname ?? 'N/A') . '</td>';
                    echo '<td>' . htmlspecialchars($patient->email ?? 'N/A') . '</td>';
                    echo '<td>' . ($success_count > 0 ? '<span class="badge badge-success">✓ ' . $success_count . '</span>' : '<span class="badge badge-error">✗</span>') . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            }

            // ==================== CONSULTATION REMINDERS ====================
            echo '<h2>📅 Rappels de Consultation (J-1)</h2>';
            $consultations_day = $this->dietetic_notifications_model->get_consultations_for_day_reminder();
            echo '<p><strong>Consultations trouvées :</strong> ' . count($consultations_day) . '</p>';

            if (!empty($consultations_day)) {
                echo '<table><tr><th>Patient</th><th>Date</th><th>Heure</th><th>Diététicien</th><th>Résultat</th></tr>';
                foreach ($consultations_day as $consultation) {
                    $dietitian_name = $consultation->dietitian_firstname . ' ' . $consultation->dietitian_lastname;
                    $result = $this->dietetic_notifications_model->notify_consultation_reminder_day(
                        $consultation->patient_id,
                        $consultation->consultation_date,
                        $consultation->consultation_time,
                        $dietitian_name
                    );
                    $success_count = is_array($result) ? count(array_filter($result, function($r) { return $r === true; })) : 0;
                    $total_sent += $success_count;

                    echo '<tr>';
                    echo '<td>Patient #' . $consultation->patient_id . '</td>';
                    echo '<td>' . date('Y-m-d', strtotime($consultation->consultation_date)) . '</td>';
                    echo '<td>' . htmlspecialchars($consultation->consultation_time ?? 'N/A') . '</td>';
                    echo '<td>' . htmlspecialchars($dietitian_name) . '</td>';
                    echo '<td>' . ($success_count > 0 ? '<span class="badge badge-success">✓ ' . $success_count . '</span>' : '<span class="badge badge-error">✗</span>') . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            }

            echo '<h2>⏰ Rappels de Consultation (H-1)</h2>';
            $consultations_hour = $this->dietetic_notifications_model->get_consultations_for_hour_reminder();
            echo '<p><strong>Consultations trouvées :</strong> ' . count($consultations_hour) . '</p>';

            if (!empty($consultations_hour)) {
                echo '<table><tr><th>Patient</th><th>Date/Heure</th><th>Diététicien</th><th>Résultat</th></tr>';
                foreach ($consultations_hour as $consultation) {
                    $dietitian_name = $consultation->dietitian_firstname . ' ' . $consultation->dietitian_lastname;
                    $result = $this->dietetic_notifications_model->notify_consultation_reminder_hour(
                        $consultation->patient_id,
                        $consultation->consultation_time,
                        $dietitian_name
                    );
                    $success_count = is_array($result) ? count(array_filter($result, function($r) { return $r === true; })) : 0;
                    $total_sent += $success_count;

                    echo '<tr>';
                    echo '<td>Patient #' . $consultation->patient_id . '</td>';
                    echo '<td>' . htmlspecialchars($consultation->consultation_date) . '</td>';
                    echo '<td>' . htmlspecialchars($dietitian_name) . '</td>';
                    echo '<td>' . ($success_count > 0 ? '<span class="badge badge-success">✓ ' . $success_count . '</span>' : '<span class="badge badge-error">✗</span>') . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            }

        } catch (Exception $e) {
            echo '<div class="error">';
            echo '<h3>❌ Erreur</h3>';
            echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '</div>';
            $errors[] = $e->getMessage();
        }

        $execution_time = round((microtime(true) - $start_time) * 1000, 2);

        echo '<h2>📊 Résumé</h2>';
        echo '<div class="' . ($total_sent > 0 ? 'success' : 'warning') . '">';
        echo '<p><strong>Notifications envoyées :</strong> <span class="badge badge-success">' . $total_sent . '</span></p>';
        echo '<p><strong>Échecs :</strong> <span class="badge badge-' . ($total_failed > 0 ? 'error' : 'success') . '">' . $total_failed . '</span></p>';
        echo '<p><strong>Temps d\'exécution :</strong> ' . $execution_time . ' ms</p>';
        echo '<p><strong>Erreurs :</strong> ' . count($errors) . '</p>';
        echo '</div>';

        // Logs récents
        echo '<h2>📝 Logs Récents (5 derniers)</h2>';
        $logs = $this->db->order_by('id', 'DESC')
                        ->limit(5)
                        ->like('description', 'Dietetic Cron', 'both')
                        ->get(db_prefix() . 'activity_log')
                        ->result_array();

        if (!empty($logs)) {
            echo '<table><tr><th>Date</th><th>Description</th></tr>';
            foreach ($logs as $log) {
                echo '<tr><td>' . htmlspecialchars($log['date']) . '</td><td>' . htmlspecialchars($log['description']) . '</td></tr>';
            }
            echo '</table>';
        } else {
            echo '<p class="warning">Aucun log trouvé. Le fichier dietetic.php doit être mis à jour sur le serveur.</p>';
        }

        echo '<div style="margin-top: 30px;"><a href="' . base_url('dietetic/portal/test_cron_complete') . '" style="display:inline-block;padding:10px 20px;background:#4CAF50;color:white;text-decoration:none;border-radius:4px;">🔄 Relancer le test</a></div>';

        echo '
    </div>
</body>
</html>';
    }
}

