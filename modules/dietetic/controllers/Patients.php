<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Patients extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        // Load Perfex models
        $this->load->model('clients_model');
        $this->load->model('staff_model');

        // Load Dietetic models
        $this->load->model('dietetic/dietetic_patients_model');
        $this->load->model('dietetic/dietetic_measurements_model');
        $this->load->model('dietetic/dietetic_consultations_model');
        $this->load->model('dietetic/dietetic_programs_model');
        $this->load->model('dietetic/dietetic_patient_documents_model');
        $this->load->model('dietetic/dietetic_daily_tracking_model');
        $this->load->helper('dietetic/dietetic');

        if (!dietetic_has_permission('view')) {
            access_denied('dietetic');
        }
    }

    /**
     * List all patients
     */
    public function index()
    {
        $data['title'] = _l('dietetic_patients');

        // Pagination configuration
        $per_page = 20; // Nombre de patients par page
        $page = $this->input->get('page') ? (int)$this->input->get('page') : 1;
        $offset = ($page - 1) * $per_page;

        // Count total patients
        $total_patients = $this->dietetic_patients_model->count_all();

        // Get patients for current page
        $data['patients'] = $this->dietetic_patients_model->get_all([], $per_page, $offset);

        // Pagination data
        $data['total_patients'] = $total_patients;
        $data['per_page'] = $per_page;
        $data['current_page'] = $page;
        $data['total_pages'] = ceil($total_patients / $per_page);

        $this->load->view('admin/patients/list', $data);
    }

    /**
     * View patient detail
     *
     * @param int $id
     */
    public function view($id)
    {
        $data['patient'] = $this->dietetic_patients_model->get($id);

        if (!$data['patient']) {
            show_404();
        }

        $data['title'] = $data['patient']->client->company . ' - ' . _l('dietetic_patient_profile');

        // Get measurements
        $data['measurements'] = $this->dietetic_measurements_model->get_by_patient($id, 10);

        // Get weight evolution
        $data['weight_evolution'] = $this->dietetic_patients_model->get_weight_evolution($id);

        // Get consultations
        $data['consultations'] = $this->dietetic_consultations_model->get_by_patient($id, 5);

        // Get programs
        $data['programs'] = $this->dietetic_programs_model->get_by_patient($id);

        // Get weight progress
        $data['weight_progress'] = $this->dietetic_measurements_model->get_weight_progress($id);

        // Get food surveys (if installed)
        $data['food_surveys'] = [];
        if ($this->db->table_exists(db_prefix() . 'dietic_food_surveys')) {
            $this->load->model('dietetic/dietetic_food_surveys_model');
            $data['food_surveys'] = $this->dietetic_food_surveys_model->get_by_patient($id);
        }

        // Get documents
        $data['documents'] = [];
        if ($this->dietetic_patient_documents_model->table_exists()) {
            $data['documents'] = $this->dietetic_patient_documents_model->get_by_patient($id);
        }

        // Get daily tracking data (if table exists)
        $data['daily_tracking'] = [];
        $data['daily_tracking_weekly_summary'] = null;
        $data['daily_tracking_streak'] = 0;
        if ($this->db->table_exists(db_prefix() . 'dietic_daily_tracking')) {
            // Get last 14 days of tracking
            $data['daily_tracking'] = $this->dietetic_daily_tracking_model->get_history($id, 14);

            // Get weekly summary
            $data['daily_tracking_weekly_summary'] = $this->dietetic_daily_tracking_model->get_weekly_summary($id);

            // Get current streak
            $data['daily_tracking_streak'] = $this->dietetic_daily_tracking_model->calculate_streak($id);

            // Add hydration data to each tracking day (if hydration table exists)
            if ($this->db->table_exists(db_prefix() . 'dietic_hydration_tracking')) {
                foreach ($data['daily_tracking'] as &$day) {
                    $this->db->select_sum('quantity_ml');
                    $this->db->where('patient_id', $id);
                    $this->db->where('tracking_date', $day->tracking_date);
                    $hydration_result = $this->db->get(db_prefix() . 'dietic_hydration_tracking')->row();
                    $day->hydration_ml = $hydration_result->quantity_ml ? intval($hydration_result->quantity_ml) : 0;
                }
            } else {
                // If hydration table doesn't exist, set to 0
                foreach ($data['daily_tracking'] as &$day) {
                    $day->hydration_ml = 0;
                }
            }

            // Add sports activities data to each tracking day (if activities table exists)
            if ($this->db->table_exists(db_prefix() . 'dietic_patient_activities')) {
                $this->load->model('dietetic/dietetic_activities_model');
                foreach ($data['daily_tracking'] as &$day) {
                    // Get activities for this specific day
                    $activities = $this->dietetic_activities_model->get_patient_activities_by_date_range(
                        $id,
                        $day->tracking_date,
                        $day->tracking_date
                    );

                    // Calculate totals
                    $total_minutes = 0;
                    $total_kcal = 0;
                    if (!empty($activities)) {
                        foreach ($activities as $activity) {
                            $total_minutes += $activity->duration_minutes;
                            $total_kcal += $activity->kcal_burned;
                        }
                    }

                    $day->activities_minutes = $total_minutes;
                    $day->activities_kcal = $total_kcal;
                    $day->activities_count = count($activities);
                }
            } else {
                // If activities table doesn't exist, set defaults
                foreach ($data['daily_tracking'] as &$day) {
                    $day->activities_minutes = 0;
                    $day->activities_kcal = 0;
                    $day->activities_count = 0;
                }
            }
        }

        // Get hydration tracking data (if table exists)
        $data['hydration_data'] = null;
        if ($this->db->table_exists(db_prefix() . 'dietic_hydration_tracking')) {
            $today = date('Y-m-d');

            // Get today's total
            $this->db->select_sum('quantity_ml');
            $this->db->where('patient_id', $id);
            $this->db->where('tracking_date', $today);
            $result = $this->db->get(db_prefix() . 'dietic_hydration_tracking')->row();
            $today_total = $result->quantity_ml ? intval($result->quantity_ml) : 0;

            // Get or create goal
            $this->db->where('patient_id', $id);
            $goal_row = $this->db->get(db_prefix() . 'dietic_hydration_goals')->row();
            $daily_goal = $goal_row ? intval($goal_row->daily_goal_ml) : 2000;

            // Get last 7 days history
            $history = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = date('Y-m-d', strtotime("-$i days"));

                $this->db->select_sum('quantity_ml');
                $this->db->where('patient_id', $id);
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

            $data['hydration_data'] = [
                'today_total' => $today_total,
                'daily_goal' => $daily_goal,
                'percentage' => min(100, round(($today_total / $daily_goal) * 100)),
                'history' => $history
            ];
        }

        $this->load->view('admin/patients/view', $data);
    }

    /**
     * Download nutrition analysis PDF for a patient
     */
    public function download_nutrition_analysis_pdf($id)
    {
        try {
            // Get patient data
            $patient = $this->dietetic_patients_model->get($id);

            if (!$patient) {
                show_404();
            }

            // Load nutrition calculator
            $this->load->library('dietetic/dietetic_nutrition_calculator');

            // Get current weight
            $current_weight = null;
            if (!empty($patient->latest_measurement) && !empty($patient->latest_measurement->weight)) {
                $current_weight = floatval($patient->latest_measurement->weight);
            } elseif (!empty($patient->initial_weight)) {
                $current_weight = floatval($patient->initial_weight);
            }

            // Check if we have minimum data
            if (!$current_weight || empty($patient->height) || $patient->height <= 0) {
                set_alert('danger', 'Impossible de generer le PDF : donnees minimales manquantes (poids et taille).');
                redirect(admin_url('dietetic/patients/view/' . $id));
                return;
            }

            // Calculate age
            $age = 30;
            if (!empty($patient->birth_date) && $patient->birth_date != '0000-00-00') {
                try {
                    $birth_date = new DateTime($patient->birth_date);
                    $today = new DateTime();
                    $age = $birth_date->diff($today)->y;
                } catch (Exception $e) {
                    $age = 30;
                }
            }

            // Get gender
            $gender = !empty($patient->gender) ? $patient->gender : 'male';

            // Get activity level
            $activity_level_map = [
                'sedentary' => Dietetic_nutrition_calculator::ACTIVITY_SEDENTARY,
                'light' => Dietetic_nutrition_calculator::ACTIVITY_LIGHT,
                'moderate' => Dietetic_nutrition_calculator::ACTIVITY_MODERATE,
                'active' => Dietetic_nutrition_calculator::ACTIVITY_ACTIVE,
                'very_active' => Dietetic_nutrition_calculator::ACTIVITY_VERY_ACTIVE
            ];
            $activity_level = isset($activity_level_map[$patient->activity_level])
                ? $activity_level_map[$patient->activity_level]
                : Dietetic_nutrition_calculator::ACTIVITY_MODERATE;

            // Get goal
            $goal_map = [
                'weight_loss' => Dietetic_nutrition_calculator::GOAL_WEIGHT_LOSS,
                'weight_gain' => Dietetic_nutrition_calculator::GOAL_WEIGHT_GAIN,
                'maintenance' => Dietetic_nutrition_calculator::GOAL_MAINTENANCE,
                'muscle_gain' => Dietetic_nutrition_calculator::GOAL_MUSCLE_GAIN
            ];
            $patient_goal = !empty($patient->goal) ? $patient->goal : 'maintenance';
            $goal = isset($goal_map[$patient_goal])
                ? $goal_map[$patient_goal]
                : Dietetic_nutrition_calculator::GOAL_MAINTENANCE;

            // Prepare analysis data
            $patient_analysis_data = [
                'weight' => $current_weight,
                'height' => floatval($patient->height),
                'age' => $age,
                'gender' => $gender,
                'activity_level' => $activity_level,
                'goal' => $goal
            ];

            // Add body measurements if available
            // Priority 1: from latest_measurement
            // Priority 2: from patient profile (_circumference fields)

            // Waist
            if (!empty($patient->latest_measurement->waist) && $patient->latest_measurement->waist > 0) {
                $patient_analysis_data['waist'] = floatval($patient->latest_measurement->waist);
            } elseif (!empty($patient->waist_circumference) && $patient->waist_circumference > 0) {
                $patient_analysis_data['waist'] = floatval($patient->waist_circumference);
            }

            // Neck
            if (!empty($patient->latest_measurement->neck) && $patient->latest_measurement->neck > 0) {
                $patient_analysis_data['neck'] = floatval($patient->latest_measurement->neck);
            } elseif (!empty($patient->neck_circumference) && $patient->neck_circumference > 0) {
                $patient_analysis_data['neck'] = floatval($patient->neck_circumference);
            }

            // Hip (for females)
            if (!empty($patient->latest_measurement->hips) && $patient->latest_measurement->hips > 0) {
                $patient_analysis_data['hip'] = floatval($patient->latest_measurement->hips);
            } elseif (!empty($patient->hip_circumference) && $patient->hip_circumference > 0) {
                $patient_analysis_data['hip'] = floatval($patient->hip_circumference);
            }

            // Perform complete nutrition analysis
            $nutrition_analysis = $this->dietetic_nutrition_calculator->complete_nutrition_analysis($patient_analysis_data);

            // Generate recommendations
            $recommendations = $this->_generate_nutrition_recommendations($patient, $nutrition_analysis);

            // Load PDF library and generate
            $this->load->library('dietetic/dietetic_pdf');
            $this->dietetic_pdf->generate_nutrition_analysis_pdf($patient, $nutrition_analysis, $recommendations);
        } catch (Exception $e) {
            // Log the error
            log_activity('PDF Generation Error for patient ' . $id . ': ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());

            // Show error to user
            set_alert('danger', 'Erreur lors de la generation du PDF : ' . $e->getMessage());
            redirect(admin_url('dietetic/patients/view/' . $id));
        }
    }

    /**
     * DEBUG VERSION - Download nutrition analysis PDF with error display
     */
    public function download_nutrition_analysis_pdf_debug($id)
    {
        // Enable error display
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        echo "<h2>DEBUG MODE - PDF Generation</h2>";
        echo "<pre>";

        try {
            echo "Step 1: Loading patient data...\n";
            $patient = $this->dietetic_patients_model->get($id);

            if (!$patient) {
                die("ERROR: Patient not found!");
            }
            echo "✓ Patient loaded: " . $patient->client->company . "\n\n";

            echo "Step 2: Loading nutrition calculator...\n";
            $this->load->library('dietetic/dietetic_nutrition_calculator');
            echo "✓ Calculator loaded\n\n";

            echo "Step 3: Getting patient weight...\n";
            $current_weight = null;
            if (!empty($patient->latest_measurement) && !empty($patient->latest_measurement->weight)) {
                $current_weight = floatval($patient->latest_measurement->weight);
                echo "✓ Weight from latest_measurement: " . $current_weight . " kg\n";
            } elseif (!empty($patient->initial_weight)) {
                $current_weight = floatval($patient->initial_weight);
                echo "✓ Weight from initial_weight: " . $current_weight . " kg\n";
            } else {
                die("ERROR: No weight data found!");
            }
            echo "\n";

            echo "Step 4: Checking minimum data...\n";
            if (!$current_weight || empty($patient->height) || $patient->height <= 0) {
                die("ERROR: Missing minimum data (weight or height)!");
            }
            echo "✓ Minimum data OK (Weight: " . $current_weight . " kg, Height: " . $patient->height . " cm)\n\n";

            echo "Step 5: Calculating age...\n";
            $age = 30;
            if (!empty($patient->birth_date) && $patient->birth_date != '0000-00-00') {
                $birth_date = new DateTime($patient->birth_date);
                $today = new DateTime();
                $age = $birth_date->diff($today)->y;
            }
            echo "✓ Age: " . $age . " years\n\n";

            echo "Step 6: Preparing analysis data...\n";
            $gender = !empty($patient->gender) ? $patient->gender : 'male';

            $activity_level_map = [
                'sedentary' => Dietetic_nutrition_calculator::ACTIVITY_SEDENTARY,
                'light' => Dietetic_nutrition_calculator::ACTIVITY_LIGHT,
                'moderate' => Dietetic_nutrition_calculator::ACTIVITY_MODERATE,
                'active' => Dietetic_nutrition_calculator::ACTIVITY_ACTIVE,
                'very_active' => Dietetic_nutrition_calculator::ACTIVITY_VERY_ACTIVE
            ];
            $activity_level = isset($activity_level_map[$patient->activity_level])
                ? $activity_level_map[$patient->activity_level]
                : Dietetic_nutrition_calculator::ACTIVITY_MODERATE;

            $goal_map = [
                'weight_loss' => Dietetic_nutrition_calculator::GOAL_WEIGHT_LOSS,
                'weight_gain' => Dietetic_nutrition_calculator::GOAL_WEIGHT_GAIN,
                'maintenance' => Dietetic_nutrition_calculator::GOAL_MAINTENANCE,
                'muscle_gain' => Dietetic_nutrition_calculator::GOAL_MUSCLE_GAIN
            ];
            $patient_goal = !empty($patient->goal) ? $patient->goal : 'maintenance';
            $goal = isset($goal_map[$patient_goal])
                ? $goal_map[$patient_goal]
                : Dietetic_nutrition_calculator::GOAL_MAINTENANCE;

            echo "✓ Gender: " . $gender . "\n";
            echo "✓ Activity level: " . $activity_level . "\n";
            echo "✓ Goal: " . $goal . "\n\n";

            echo "Step 7: Performing nutrition analysis...\n";
            $patient_analysis_data = [
                'weight' => $current_weight,
                'height' => floatval($patient->height),
                'age' => $age,
                'gender' => $gender,
                'activity_level' => $activity_level,
                'goal' => $goal
            ];

            $nutrition_analysis = $this->dietetic_nutrition_calculator->complete_nutrition_analysis($patient_analysis_data);
            echo "✓ Nutrition analysis completed\n";
            echo "  BMR: " . $nutrition_analysis['bmr']['value'] . " kcal\n";
            echo "  TDEE: " . $nutrition_analysis['tdee']['tdee'] . " kcal\n\n";

            echo "Step 8: Generating recommendations...\n";
            $recommendations = $this->_generate_nutrition_recommendations($patient, $nutrition_analysis);
            echo "✓ Recommendations generated (" . count($recommendations['recommendations']) . " items)\n\n";

            echo "Step 9: Loading PDF library...\n";
            $this->load->library('dietetic/dietetic_pdf');
            echo "✓ PDF library loaded\n\n";

            echo "Step 10: Generating PDF...\n";
            $this->dietetic_pdf->generate_nutrition_analysis_pdf($patient, $nutrition_analysis, $recommendations);

            echo "✓ PDF GENERATION SUCCESS!\n";
        } catch (Exception $e) {
            echo "\n\n=== EXCEPTION CAUGHT ===\n";
            echo "Message: " . $e->getMessage() . "\n";
            echo "File: " . $e->getFile() . "\n";
            echo "Line: " . $e->getLine() . "\n";
            echo "Trace:\n" . $e->getTraceAsString() . "\n";
        }

        echo "</pre>";
        die();
    }

    /**
     * Generate nutrition recommendations for PDF
     * (Similar to nutrition_recommendations widget)
     */
    private function _generate_nutrition_recommendations($patient, $nutrition_analysis)
    {
        $weight = null;
        if (!empty($patient->latest_measurement) && !empty($patient->latest_measurement->weight)) {
            $weight = floatval($patient->latest_measurement->weight);
        } elseif (!empty($patient->initial_weight)) {
            $weight = floatval($patient->initial_weight);
        }

        $height = floatval($patient->height);
        $bmi = dietetic_calculate_bmi($weight, $height);

        if (!$bmi) {
            return [];
        }

        $recommendations = [];

        // BMI-based recommendations
        if ($bmi < 18.5) {
            $recommendations[] = [
                'title' => 'Augmentation progressive de l\'apport calorique',
                'description' => 'Visez une augmentation de 300-500 calories par jour pour favoriser une prise de poids saine. Privilegiez les aliments nutritifs et denses en calories.'
            ];
        } elseif ($bmi >= 25 && $bmi < 30) {
            $recommendations[] = [
                'title' => 'Deficit calorique modere',
                'description' => 'Un deficit de 300-500 calories par jour permet une perte de poids progressive et durable (0,5 kg par semaine).'
            ];
        } elseif ($bmi >= 30) {
            $recommendations[] = [
                'title' => 'Suivi medical recommande',
                'description' => 'Consultez regulierement un professionnel de sante pour un accompagnement personnalise dans votre demarche de perte de poids.'
            ];
        }

        // Activity recommendations
        $recommendations[] = [
            'title' => 'Activite physique reguliere',
            'description' => 'Pratiquez au moins 150 minutes d\'activite moderee par semaine. Combinez exercices cardiovasculaires et renforcement musculaire.'
        ];

        // Hydration
        $recommendations[] = [
            'title' => 'Hydratation adequate',
            'description' => 'Maintenez une hydratation optimale tout au long de la journee. L\'eau est essentielle pour tous les processus metaboliques.'
        ];

        // Macronutrients balance
        $recommendations[] = [
            'title' => 'Equilibre des macronutriments',
            'description' => 'Respectez la repartition recommandee : proteines pour la satiete et la masse musculaire, glucides pour l\'energie, lipides pour les hormones.'
        ];

        return ['recommendations' => $recommendations];
    }

    /**
     * Create new patient
     */
    public function create()
    {
        if (!dietetic_has_permission('create')) {
            access_denied('dietetic');
        }

        if ($this->input->post()) {
            // Wrap in try-catch to capture any errors
            try {
                $data = $this->input->post();

                // Filter POST data to only include valid database columns
                $data = $this->_filter_patient_data($data);

                // Set dietitian - force to current user if not admin
                if (!is_admin()) {
                    // Non-admins can only create patients for themselves
                    $data['dietitian_id'] = get_staff_user_id();
                } elseif (!isset($data['dietitian_id'])) {
                    // Admins: default to themselves if not specified
                    $data['dietitian_id'] = get_staff_user_id();
                }

                $patient_id = $this->dietetic_patients_model->add($data);

                if ($patient_id) {
                    // Handle document uploads if any
                    $this->_handle_document_uploads($patient_id);

                    set_alert('success', _l('added_successfully'));

                    // Check if AJAX request
                    if ($this->input->is_ajax_request()) {
                        echo json_encode(['success' => true, 'patient_id' => $patient_id]);
                        return;
                    }

                    redirect(admin_url('dietetic/patients/view/' . $patient_id));
                } else {
                    set_alert('danger', _l('dietetic_error_patient_exists'));

                    // Check if AJAX request
                    if ($this->input->is_ajax_request()) {
                        echo json_encode(['success' => false, 'message' => _l('dietetic_error_patient_exists')]);
                        return;
                    }
                }
            } catch (Exception $e) {
                // Log the error with full details
                log_activity('Patient creation error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());

                // Show error to user
                set_alert('danger', 'Erreur lors de la création du patient: ' . $e->getMessage());

                // Check if AJAX request
                if ($this->input->is_ajax_request()) {
                    echo json_encode(['success' => false, 'message' => $e->getMessage(), 'error' => true]);
                    return;
                }

                // Redirect back to form
                redirect(admin_url('dietetic/patients/create'));
                return;
            }
        }

        $data['title'] = _l('dietetic_new_patient');

        // Get clients - filtered by permissions
        $current_staff_id = get_staff_user_id();

        // Super admin (user ID 1) sees all clients
        if (is_admin() && $current_staff_id == 1) {
            $data['clients'] = $this->clients_model->get();
        } else {
            // Other dietitians only see clients that:
            // 1. Don't have a patient record yet (available for new patient creation)
            // Clients with existing patients are NOT shown to ensure dietitians only see their assigned patients

            $all_clients = $this->clients_model->get();
            $filtered_clients = [];

            foreach ($all_clients as $client) {
                // Check if this client has a patient
                // Use direct query to bypass access check that get_by_client uses
                $this->db->where('client_id', $client['userid']);
                $existing_patient = $this->db->get(db_prefix() . 'dietic_patients')->row();

                if (!$existing_patient) {
                    // Client has no patient yet - available for creation
                    $filtered_clients[] = $client;
                }
                // Note: We don't include clients that already have patients, even if assigned to this dietitian
                // Those patients are accessible via the patient list page instead
            }

            $data['clients'] = $filtered_clients;
        }

        // Get staff members (dietitians) - filtered by permissions
        if (is_admin()) {
            // Admins can assign to any dietitian
            $data['staff'] = $this->staff_model->get('', ['active' => 1]);
        } else {
            // Non-admins can only assign to themselves
            $current_staff_id = get_staff_user_id();
            $data['staff'] = $this->staff_model->get('', ['staffid' => $current_staff_id, 'active' => 1]);
        }

        $this->load->view('admin/patients/form', $data);
    }

    /**
     * Edit patient
     *
     * @param int $id
     */
    public function edit($id)
    {
        if (!dietetic_has_permission('edit')) {
            access_denied('dietetic');
        }

        $data['patient'] = $this->dietetic_patients_model->get($id);

        if (!$data['patient']) {
            show_404();
        }

        if ($this->input->post()) {
            // Get all POST data
            $update_data = $this->input->post();

            // Filter POST data to only include valid database columns
            $update_data = $this->_filter_patient_data($update_data);

            // Security: Non-admins cannot change the dietitian
            if (!is_admin() && isset($update_data['dietitian_id'])) {
                // Force dietitian_id to remain the current user for non-admins
                $update_data['dietitian_id'] = get_staff_user_id();
            }

            if ($this->dietetic_patients_model->update($id, $update_data)) {
                set_alert('success', _l('updated_successfully'));
                redirect(admin_url('dietetic/patients/view/' . $id));
            } else {
                set_alert('danger', _l('dietetic_error_update_failed'));
            }
        }

        $data['title'] = _l('dietetic_edit_patient');

        // Get staff members (dietitians) - filtered by permissions
        if (is_admin()) {
            // Admins can assign to any dietitian
            $data['staff'] = $this->staff_model->get('', ['active' => 1]);
        } else {
            // Non-admins can only assign to themselves
            $current_staff_id = get_staff_user_id();
            $data['staff'] = $this->staff_model->get('', ['staffid' => $current_staff_id, 'active' => 1]);
        }

        $this->load->view('admin/patients/form', $data);
    }

    /**
     * Delete patient
     *
     * IMPORTANT: Seuls les administrateurs peuvent supprimer des patients
     * pour des raisons de comptabilité et de conformité RGPD
     *
     * @param int $id
     */
    public function delete($id)
    {
        // Vérification 1: Permission delete requise
        if (!dietetic_has_permission('delete')) {
            ajax_access_denied();
        }

        // Vérification 2: Seuls les administrateurs peuvent supprimer
        // Les diététiciens ne peuvent PAS supprimer de patients (comptabilité + RGPD)
        if (!is_admin()) {
            echo json_encode([
                'success' => false,
                'message' => 'Seuls les administrateurs peuvent supprimer des patients.'
            ]);
            return;
        }

        if ($this->dietetic_patients_model->delete($id)) {
            echo json_encode(['success' => true, 'message' => _l('deleted')]);
        } else {
            echo json_encode(['success' => false, 'message' => _l('dietetic_error_delete_failed')]);
        }
    }

    /**
     * View patient from client profile tab
     *
     * @param int $client_id
     */
    public function client_view($client_id)
    {
        $patient = $this->dietetic_patients_model->get_by_client($client_id);

        if ($patient) {
            redirect(admin_url('dietetic/patients/view/' . $patient->id));
        } else {
            // Show create form
            redirect(admin_url('dietetic/patients/create?client_id=' . $client_id));
        }
    }

    /**
     * Search patients via AJAX
     */
    public function search()
    {
        $search = $this->input->get('q');

        if (empty($search)) {
            echo json_encode([]);
            return;
        }

        $results = $this->dietetic_patients_model->search($search);

        $formatted = [];
        foreach ($results as $patient) {
            $formatted[] = [
                'id'   => $patient->id,
                'text' => $patient->client_name . ' (#' . $patient->id . ')',
            ];
        }

        echo json_encode($formatted);
    }

    /**
     * Get patient data for charts via AJAX
     *
     * @param int $id
     */
    public function chart_data($id)
    {
        $patient = $this->dietetic_patients_model->get($id);

        if (!$patient) {
            echo json_encode(['success' => false]);
            return;
        }

        // Get weight evolution
        $measurements = $this->dietetic_patients_model->get_weight_evolution($id, 12);

        $labels = [];
        $weights = [];
        $bmis = [];

        foreach ($measurements as $measurement) {
            $labels[] = date('M d', strtotime($measurement->measurement_date));
            $weights[] = (float)$measurement->weight;
            $bmis[] = (float)$measurement->bmi;
        }

        echo json_encode([
            'success' => true,
            'data'    => [
                'labels'  => $labels,
                'weights' => $weights,
                'bmis'    => $bmis,
            ],
        ]);
    }

    /**
     * Assign a dietitian to a patient (AJAX)
     *
     * @param int $patient_id
     */
    public function assign_dietitian($patient_id)
    {
        // Check permissions: Admins OR dietitian who owns the patient can manage assignments
        $current_staff_id = get_staff_user_id();

        if (!is_admin() && !dietetic_can_access_patient($patient_id, $current_staff_id)) {
            echo json_encode(['success' => false, 'message' => 'Accès refusé']);
            return;
        }

        $dietitian_id = $this->input->post('dietitian_id');
        $is_primary = $this->input->post('is_primary');
        $notes = $this->input->post('notes');

        if (!$dietitian_id) {
            echo json_encode(['success' => false, 'message' => 'Diététicien non spécifié']);
            return;
        }

        // Check if table exists
        if (!$this->db->table_exists(db_prefix() . 'dietic_patient_dietitians')) {
            echo json_encode(['success' => false, 'message' => 'Le système multi-diététiciens n\'est pas installé. Veuillez installer le système via admin/dietetic/dietitians/install_assignments']);
            return;
        }

        $this->load->model('dietetic/dietetic_patient_dietitians_model');

        $assignment_data = [
            'patient_id' => $patient_id,
            'dietitian_id' => $dietitian_id,
            'is_primary' => $is_primary ? 1 : 0,
            'notes' => $notes
        ];

        $result = $this->dietetic_patient_dietitians_model->assign($assignment_data);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Diététicien assigné avec succès']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'assignation']);
        }
    }

    /**
     * Set a dietitian as primary for a patient (AJAX)
     *
     * @param int $patient_id
     */
    public function set_primary_dietitian($patient_id)
    {
        // Check permissions: Admins OR dietitian who owns the patient can manage assignments
        $current_staff_id = get_staff_user_id();

        if (!is_admin() && !dietetic_can_access_patient($patient_id, $current_staff_id)) {
            echo json_encode(['success' => false, 'message' => 'Accès refusé']);
            return;
        }

        $dietitian_id = $this->input->post('dietitian_id');

        if (!$dietitian_id) {
            echo json_encode(['success' => false, 'message' => 'Diététicien non spécifié']);
            return;
        }

        // Check if table exists
        if (!$this->db->table_exists(db_prefix() . 'dietic_patient_dietitians')) {
            echo json_encode(['success' => false, 'message' => 'Le système multi-diététiciens n\'est pas installé']);
            return;
        }

        $this->load->model('dietetic/dietetic_patient_dietitians_model');

        $result = $this->dietetic_patient_dietitians_model->set_primary($patient_id, $dietitian_id);

        if ($result) {
            // Also update the main dietitian_id in patients table for compatibility
            $this->dietetic_patients_model->update($patient_id, ['dietitian_id' => $dietitian_id]);

            echo json_encode(['success' => true, 'message' => 'Diététicien principal défini avec succès']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la modification']);
        }
    }

    /**
     * Remove a dietitian from a patient (AJAX)
     *
     * @param int $patient_id
     */
    public function remove_dietitian($patient_id)
    {
        // Check permissions: Admins OR dietitian who owns the patient can manage assignments
        $current_staff_id = get_staff_user_id();

        if (!is_admin() && !dietetic_can_access_patient($patient_id, $current_staff_id)) {
            echo json_encode(['success' => false, 'message' => 'Accès refusé']);
            return;
        }

        $dietitian_id = $this->input->post('dietitian_id');

        if (!$dietitian_id) {
            echo json_encode(['success' => false, 'message' => 'Diététicien non spécifié']);
            return;
        }

        // Check if table exists
        if (!$this->db->table_exists(db_prefix() . 'dietic_patient_dietitians')) {
            echo json_encode(['success' => false, 'message' => 'Le système multi-diététiciens n\'est pas installé']);
            return;
        }

        $this->load->model('dietetic/dietetic_patient_dietitians_model');

        // Check if this is the primary dietitian
        $assignment = $this->dietetic_patient_dietitians_model->get_assignment($patient_id, $dietitian_id);

        if ($assignment && $assignment->is_primary) {
            $count = $this->dietetic_patient_dietitians_model->count_dietitians($patient_id, 'active');
            if ($count <= 1) {
                echo json_encode(['success' => false, 'message' => 'Impossible de retirer le seul diététicien assigné. Assignez d\'abord un autre diététicien.']);
                return;
            }
        }

        $result = $this->dietetic_patient_dietitians_model->remove($patient_id, $dietitian_id, false); // Soft delete

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Diététicien retiré avec succès']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors du retrait']);
        }
    }

    /**
     * Upload document for a patient (AJAX)
     *
     * @param int $patient_id
     */
    public function upload_document($patient_id)
    {
        // Set JSON header
        header('Content-Type: application/json');

        if (!dietetic_has_permission('edit')) {
            echo json_encode(['success' => false, 'message' => 'Accès refusé']);
            return;
        }

        // Ensure table exists
        if (!$this->dietetic_patient_documents_model->table_exists()) {
            $this->dietetic_patient_documents_model->create_table();
        }

        // Check if patient exists
        $patient = $this->dietetic_patients_model->get($patient_id);
        if (!$patient) {
            echo json_encode(['success' => false, 'message' => 'Patient non trouvé']);
            return;
        }

        // Check if file was uploaded
        if (!isset($_FILES['document']) || $_FILES['document']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'Aucun fichier uploadé']);
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
        $upload_base = FCPATH . 'uploads/dietetic/documents/patient_' . $patient_id;
        if (!is_dir($upload_base)) {
            if (!mkdir($upload_base, 0755, true)) {
                echo json_encode(['success' => false, 'message' => 'Impossible de créer le dossier d\'upload']);
                return;
            }
        }

        // Generate unique filename
        $original_name = pathinfo($_FILES['document']['name'], PATHINFO_FILENAME);
        $original_name = preg_replace('/[^a-zA-Z0-9_-]/', '_', $original_name);
        $filename = $original_name . '_' . time() . '.' . $extension;
        $filepath = $upload_base . '/' . $filename;

        // Move uploaded file
        if (move_uploaded_file($_FILES['document']['tmp_name'], $filepath)) {
            // Save document metadata to database
            $document_data = [
                'patient_id' => $patient_id,
                'filename' => $filename,
                'original_filename' => $_FILES['document']['name'],
                'file_type' => $file_type,
                'file_size' => $_FILES['document']['size']
            ];

            $document_id = $this->dietetic_patient_documents_model->add($document_data);

            log_activity('Medical Document Uploaded [Patient ID: ' . $patient_id . ', File: ' . $filename . ', Document ID: ' . $document_id . ']');

            echo json_encode([
                'success' => true,
                'message' => 'Document uploadé avec succès',
                'document_id' => $document_id
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'upload du fichier']);
        }
    }

    /**
     * Download document
     *
     * @param int $document_id
     */
    public function download_document($document_id)
    {
        if (!dietetic_has_permission('view')) {
            show_404();
            return;
        }

        // Get document
        $document = $this->dietetic_patient_documents_model->get($document_id);

        if (!$document) {
            show_404();
            return;
        }

        // Check if user has access to this patient
        if (!dietetic_can_access_patient($document->patient_id)) {
            access_denied('dietetic');
            return;
        }

        // Build file path
        $filepath = FCPATH . 'uploads/dietetic/documents/patient_' . $document->patient_id . '/' . $document->filename;

        if (!file_exists($filepath)) {
            show_404();
            return;
        }

        // Force download
        header('Content-Description: File Transfer');
        header('Content-Type: ' . $document->file_type);
        header('Content-Disposition: attachment; filename="' . $document->original_filename . '"');
        header('Content-Length: ' . filesize($filepath));
        header('Pragma: public');
        header('Cache-Control: must-revalidate');
        header('Expires: 0');

        readfile($filepath);
        exit;
    }

    /**
     * Delete document (AJAX)
     *
     * @param int $document_id
     */
    public function delete_document($document_id)
    {
        // Set JSON header
        header('Content-Type: application/json');

        if (!dietetic_has_permission('delete')) {
            echo json_encode(['success' => false, 'message' => 'Accès refusé']);
            return;
        }

        // Get document
        $document = $this->dietetic_patient_documents_model->get($document_id);

        if (!$document) {
            echo json_encode(['success' => false, 'message' => 'Document non trouvé']);
            return;
        }

        // Check if user has access to this patient
        if (!dietetic_can_access_patient($document->patient_id)) {
            echo json_encode(['success' => false, 'message' => 'Accès refusé']);
            return;
        }

        // Delete file from disk
        $filepath = FCPATH . 'uploads/dietetic/documents/patient_' . $document->patient_id . '/' . $document->filename;
        if (file_exists($filepath)) {
            unlink($filepath);
        }

        // Delete from database
        if ($this->dietetic_patient_documents_model->delete($document_id)) {
            log_activity('Medical Document Deleted [Patient ID: ' . $document->patient_id . ', Document ID: ' . $document_id . ']');
            echo json_encode(['success' => true, 'message' => 'Document supprimé avec succès']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression']);
        }
    }

    /**
     * Handle document uploads for a patient
     *
     * @param int $patient_id
     * @return void
     */
    private function _handle_document_uploads($patient_id)
    {
        // Ensure table exists
        if (!$this->dietetic_patient_documents_model->table_exists()) {
            $this->dietetic_patient_documents_model->create_table();
        }

        // Check if documents were uploaded
        if (!isset($_FILES['documents']) || empty($_FILES['documents']['name'][0])) {
            return;
        }

        // Create upload directory if it doesn't exist
        $upload_base = FCPATH . 'uploads/dietetic/documents/patient_' . $patient_id;
        if (!is_dir($upload_base)) {
            if (!mkdir($upload_base, 0755, true)) {
                log_activity('Failed to create upload directory for patient ' . $patient_id);
                return;
            }
        }

        // Process each uploaded file
        $files_count = count($_FILES['documents']['name']);

        for ($i = 0; $i < $files_count; $i++) {
            // Check if file was uploaded successfully
            if ($_FILES['documents']['error'][$i] !== UPLOAD_ERR_OK) {
                continue;
            }

            // Validate file type
            $allowed_types = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
            $file_type = $_FILES['documents']['type'][$i];
            $extension = strtolower(pathinfo($_FILES['documents']['name'][$i], PATHINFO_EXTENSION));
            $allowed_extensions = ['pdf', 'jpg', 'jpeg', 'png'];

            if (!in_array($file_type, $allowed_types) && !in_array($extension, $allowed_extensions)) {
                continue;
            }

            // Validate file size (10MB max)
            if ($_FILES['documents']['size'][$i] > 10 * 1024 * 1024) {
                continue;
            }

            // Generate unique filename
            $original_name = pathinfo($_FILES['documents']['name'][$i], PATHINFO_FILENAME);
            // Sanitize filename
            $original_name = preg_replace('/[^a-zA-Z0-9_-]/', '_', $original_name);
            $filename = $original_name . '_' . time() . '_' . $i . '.' . $extension;
            $filepath = $upload_base . '/' . $filename;

            // Move uploaded file
            if (move_uploaded_file($_FILES['documents']['tmp_name'][$i], $filepath)) {
                // Save document metadata to database
                $document_data = [
                    'patient_id' => $patient_id,
                    'filename' => $filename,
                    'original_filename' => $_FILES['documents']['name'][$i],
                    'file_type' => $file_type,
                    'file_size' => $_FILES['documents']['size'][$i]
                ];

                $document_id = $this->dietetic_patient_documents_model->add($document_data);

                log_activity('Medical Document Uploaded [Patient ID: ' . $patient_id . ', File: ' . $filename . ', Document ID: ' . $document_id . ']');
            }
        }
    }

    /**
     * Filter POST data to only include valid database columns
     * This prevents SQL errors when the form has fields that don't exist in the database
     *
     * @param array $data
     * @return array
     */
    private function _filter_patient_data($data)
    {
        // Get valid columns from database
        $table_name = db_prefix() . 'dietic_patients';
        $query = $this->db->query("DESCRIBE `{$table_name}`");

        if (!$query) {
            // If query fails, return data as-is and let the model handle it
            return $data;
        }

        $valid_columns = [];
        foreach ($query->result_array() as $row) {
            $valid_columns[] = $row['Field'];
        }

        // Filter data to only include valid columns
        $filtered_data = [];
        foreach ($data as $key => $value) {
            if (in_array($key, $valid_columns)) {
                $filtered_data[$key] = $value;
            } else {
                // Log skipped fields for debugging
                log_activity('Skipped invalid field in patient form: ' . $key);
            }
        }

        return $filtered_data;
    }

    /**
     * API: Get patient statistics for admin view
     * GET /admin/dietetic/patients/api_get_patient_statistics/{patient_id}?period=6months
     */
    public function api_get_patient_statistics($patient_id)
    {
        header('Content-Type: application/json');

        if (!is_admin() && !has_permission('dietetic', '', 'view')) {
            echo json_encode(['success' => false, 'message' => 'Accès refusé']);
            return;
        }

        try {
            // Get patient
            $patient = $this->dietetic_patients_model->get($patient_id);

            if (!$patient) {
                echo json_encode(['success' => false, 'message' => 'Patient non trouvé']);
                return;
            }

            $period = $this->input->get('period') ?: '6months';

            // Calculate date range based on period
            $end_date = date('Y-m-d');
            switch ($period) {
                case '1month':
                    $start_date = date('Y-m-d', strtotime('-1 month'));
                    break;
                case '3months':
                    $start_date = date('Y-m-d', strtotime('-3 months'));
                    break;
                case '6months':
                    $start_date = date('Y-m-d', strtotime('-6 months'));
                    break;
                case '1year':
                    $start_date = date('Y-m-d', strtotime('-1 year'));
                    break;
                case 'all':
                    $start_date = '1900-01-01'; // Get all records
                    break;
                default:
                    $start_date = date('Y-m-d', strtotime('-6 months'));
            }

            // Get measurements
            $this->db->select('*');
            $this->db->where('patient_id', $patient->id);
            $this->db->where('measurement_date >=', $start_date);
            $this->db->where('measurement_date <=', $end_date);
            $this->db->order_by('measurement_date', 'ASC');
            $measurements = $this->db->get(db_prefix() . 'dietic_measurements')->result_array();

            // Calculate statistics
            $stats = [];

            if (!empty($measurements)) {
                $first = $measurements[0];
                $last = $measurements[count($measurements) - 1];

                // Weight stats
                if (isset($first['weight']) && isset($last['weight'])) {
                    $stats['weight'] = [
                        'initial' => $first['weight'],
                        'current' => $last['weight'],
                        'change' => $last['weight'] - $first['weight'],
                        'target' => $patient->target_weight
                    ];
                }

                // BMI stats
                if (isset($first['height']) && $first['height'] > 0) {
                    $height_m = $first['height'] / 100;
                    if (isset($first['weight'])) {
                        $initial_bmi = $first['weight'] / ($height_m * $height_m);
                    }
                    if (isset($last['weight'])) {
                        $current_bmi = $last['weight'] / ($height_m * $height_m);
                    }

                    if (isset($initial_bmi) && isset($current_bmi)) {
                        $stats['bmi'] = [
                            'initial' => $initial_bmi,
                            'current' => $current_bmi,
                            'change' => $current_bmi - $initial_bmi
                        ];
                    }
                }

                // Waist stats
                if (isset($first['waist_circumference']) && isset($last['waist_circumference'])) {
                    $stats['waist'] = [
                        'initial' => $first['waist_circumference'],
                        'current' => $last['waist_circumference'],
                        'change' => $last['waist_circumference'] - $first['waist_circumference']
                    ];
                }

                // Hip stats
                if (isset($first['hip_circumference']) && isset($last['hip_circumference'])) {
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

            // Calculate compliance rate (% of days with entries)
            $total_days = (strtotime($end_date) - strtotime($start_date)) / 86400;
            $days_with_entries = count($compliance);
            $compliance_rate = $total_days > 0 ? ($days_with_entries / $total_days) * 100 : 0;

            // Calculate trends and predictions (same as portal)
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
                    $x = $i;
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
                            'message' => sprintf('Excellent ! Le patient perd en moyenne %.1f kg par semaine', $rate_per_week)
                        ];
                    } else {
                        $insights[] = [
                            'type' => 'warning',
                            'icon' => 'fa-info-circle',
                            'message' => sprintf('Attention : le patient a pris %.1f kg par semaine', abs($rate_per_week))
                        ];
                    }
                } elseif (abs($rate_per_week) > 0) {
                    $insights[] = [
                        'type' => 'neutral',
                        'icon' => 'fa-balance-scale',
                        'message' => sprintf('Poids stable (%.1f kg par semaine)', abs($rate_per_week))
                    ];
                }

                // BMI insight
                if (isset($stats['bmi']['current'])) {
                    $bmi = $stats['bmi']['current'];
                    if ($bmi < 18.5) {
                        $insights[] = [
                            'type' => 'info',
                            'icon' => 'fa-tachometer',
                            'message' => 'IMC en dessous de la normale (insuffisance pondérale)'
                        ];
                    } elseif ($bmi >= 18.5 && $bmi < 25) {
                        $insights[] = [
                            'type' => 'positive',
                            'icon' => 'fa-check-circle',
                            'message' => 'IMC dans la fourchette normale'
                        ];
                    } elseif ($bmi >= 25 && $bmi < 30) {
                        $insights[] = [
                            'type' => 'warning',
                            'icon' => 'fa-exclamation-triangle',
                            'message' => 'IMC indique un surpoids'
                        ];
                    } else {
                        $insights[] = [
                            'type' => 'warning',
                            'icon' => 'fa-exclamation-triangle',
                            'message' => 'IMC indique une obésité'
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
                            'message' => sprintf('À ce rythme, objectif atteint dans environ %.0f semaines !', $weeks)
                        ];
                    } elseif ($weeks <= 12) {
                        $insights[] = [
                            'type' => 'info',
                            'icon' => 'fa-calendar',
                            'message' => sprintf('Objectif attendu dans environ %.0f semaines', $weeks)
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
            log_activity('Error getting patient statistics: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Erreur serveur: ' . $e->getMessage(),
                'trace' => ENVIRONMENT === 'development' ? $e->getTraceAsString() : null
            ]);
        }
    }

    /**
     * API: Add a statistic note (Admin)
     * POST: {patient_id, note_date, note_text, note_type, icon, color}
     */
    public function api_add_statistic_note()
    {
        header('Content-Type: application/json');

        if (!is_admin() && !has_permission('dietetic', '', 'view')) {
            echo json_encode(['success' => false, 'message' => 'Accès refusé']);
            return;
        }

        $patient_id = $this->input->post('patient_id');

        if (empty($patient_id)) {
            echo json_encode(['success' => false, 'message' => 'Patient ID requis']);
            return;
        }

        // Verify patient exists
        $patient = $this->dietetic_patients_model->get($patient_id);
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
            $icon = $this->input->post('note_icon') ?: 'fa-sticky-note';
            $color = $this->input->post('color') ?: '#01807B';

            if (empty($note_date) || empty($note_text)) {
                echo json_encode(['success' => false, 'message' => 'Date et texte requis']);
                return;
            }

            // Insert note
            $data = [
                'patient_id' => $patient_id,
                'note_date' => $note_date,
                'note_text' => $note_text,
                'note_type' => $note_type,
                'icon' => $icon,
                'color' => $color,
                'created_by' => get_staff_user_id(),
                'created_by_type' => 'staff',
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->db->insert(db_prefix() . 'dietic_statistics_notes', $data);
            $note_id = $this->db->insert_id();

            if ($note_id) {
                log_activity('Dietetic: Note added to patient ' . $patient_id . ' statistics by staff');
                echo json_encode([
                    'success' => true,
                    'message' => 'Note ajoutée avec succès',
                    'note_id' => $note_id
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout']);
            }

        } catch (Exception $e) {
            log_activity('Error adding statistic note (admin): ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Erreur serveur: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * API: Delete a statistic note (Admin)
     * POST: {note_id, patient_id}
     */
    public function api_delete_statistic_note()
    {
        header('Content-Type: application/json');

        if (!is_admin() && !has_permission('dietetic', '', 'view')) {
            echo json_encode(['success' => false, 'message' => 'Accès refusé']);
            return;
        }

        try {
            // Check if table exists
            if (!$this->db->table_exists(db_prefix() . 'dietic_statistics_notes')) {
                echo json_encode(['success' => false, 'message' => 'Fonctionnalité non disponible']);
                return;
            }

            $note_id = $this->input->post('note_id');
            $patient_id = $this->input->post('patient_id');

            if (empty($note_id) || empty($patient_id)) {
                echo json_encode(['success' => false, 'message' => 'ID de note et patient requis']);
                return;
            }

            // Verify note belongs to patient
            $this->db->where('id', $note_id);
            $this->db->where('patient_id', $patient_id);
            $note = $this->db->get(db_prefix() . 'dietic_statistics_notes')->row();

            if (!$note) {
                echo json_encode(['success' => false, 'message' => 'Note non trouvée']);
                return;
            }

            // Delete note
            $this->db->where('id', $note_id);
            $this->db->delete(db_prefix() . 'dietic_statistics_notes');

            log_activity('Dietetic: Note deleted from patient ' . $patient_id . ' statistics by staff');
            echo json_encode([
                'success' => true,
                'message' => 'Note supprimée avec succès'
            ]);

        } catch (Exception $e) {
            log_activity('Error deleting statistic note (admin): ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Erreur serveur: ' . $e->getMessage()
            ]);
        }
    }
}
