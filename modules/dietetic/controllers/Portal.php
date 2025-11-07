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
            'repair_orphans'
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
}

