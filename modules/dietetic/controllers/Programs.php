<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Programs extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        // Load Perfex models
        $this->load->model('clients_model');
        $this->load->model('staff_model');

        // Load dietetic models
        $this->load->model('dietetic/dietetic_programs_model');
        $this->load->model('dietetic/dietetic_patients_model');
        $this->load->model('dietetic/dietetic_meal_plans_model');
        $this->load->model('dietetic/dietetic_foods_model');
        $this->load->model('dietetic/dietetic_recipes_model');
        $this->load->helper('dietetic/dietetic');

        if (!dietetic_has_permission('view')) {
            access_denied('dietetic');
        }
    }

    /**
     * List all programs
     */
    public function index()
    {
        $data['title'] = _l('dietetic_programs');
        $data['programs'] = $this->dietetic_programs_model->get_all();

        // Calculate statistics
        $all_programs = $data['programs'];
        $data['total_count'] = count($all_programs);
        $data['active_count'] = 0;
        $data['completed_count'] = 0;
        $data['this_month_count'] = 0;

        $current_month = date('Y-m');

        foreach ($all_programs as $program) {
            if ($program->status == 'active') {
                $data['active_count']++;
            } elseif ($program->status == 'completed') {
                $data['completed_count']++;
            }

            if (substr($program->start_date, 0, 7) == $current_month) {
                $data['this_month_count']++;
            }
        }

        $this->load->view('admin/programs/list', $data);
    }

    /**
     * View program detail
     *
     * @param int $id
     */
    public function view($id)
    {
        $data['program'] = $this->dietetic_programs_model->get($id);

        if (!$data['program']) {
            show_404();
        }

        $data['title'] = $data['program']->program_name;
        $data['patient'] = $this->dietetic_patients_model->get($data['program']->patient_id);
        $data['meal_plans'] = $this->dietetic_programs_model->get_meal_plans($id);

        // Auto-calculate nutritional objectives from anamnesis data
        $data['calculated_objectives'] = null;
        if ($data['patient']) {
            $this->load->library('dietetic/Dietetic_nutrition_calculator');

            // Get patient latest measurement for current weight
            $this->db->where('patient_id', $data['patient']->id);
            $this->db->order_by('measurement_date', 'DESC');
            $this->db->limit(1);
            $latest_measurement = $this->db->get(db_prefix() . 'dietic_measurements')->row();

            $current_weight = $latest_measurement ? $latest_measurement->weight : null;

            // Calculate age from birth_date
            $age = null;
            if ($data['patient']->birth_date) {
                $birthdate = new DateTime($data['patient']->birth_date);
                $today = new DateTime();
                $age = $birthdate->diff($today)->y;
            }

            // Only calculate if we have the minimum required data
            if ($current_weight && $data['patient']->height && $age) {
                // Map activity level from patient data to calculator constants
                $activity_map = [
                    'sedentary' => Dietetic_nutrition_calculator::ACTIVITY_SEDENTARY,
                    'light' => Dietetic_nutrition_calculator::ACTIVITY_LIGHT,
                    'moderate' => Dietetic_nutrition_calculator::ACTIVITY_MODERATE,
                    'active' => Dietetic_nutrition_calculator::ACTIVITY_ACTIVE,
                    'very_active' => Dietetic_nutrition_calculator::ACTIVITY_VERY_ACTIVE
                ];

                $activity_level = $activity_map[$data['patient']->activity_level] ?? Dietetic_nutrition_calculator::ACTIVITY_MODERATE;

                // Determine goal from program objective or default to maintenance
                $goal = Dietetic_nutrition_calculator::GOAL_MAINTENANCE;
                if ($data['program']->objective) {
                    $objective_lower = strtolower($data['program']->objective);
                    if (strpos($objective_lower, 'perte') !== false || strpos($objective_lower, 'perd') !== false || strpos($objective_lower, 'maigrir') !== false) {
                        $goal = Dietetic_nutrition_calculator::GOAL_WEIGHT_LOSS;
                    } elseif (strpos($objective_lower, 'prise') !== false || strpos($objective_lower, 'gagn') !== false || strpos($objective_lower, 'gross') !== false) {
                        $goal = Dietetic_nutrition_calculator::GOAL_WEIGHT_GAIN;
                    } elseif (strpos($objective_lower, 'muscle') !== false || strpos($objective_lower, 'muscul') !== false) {
                        $goal = Dietetic_nutrition_calculator::GOAL_MUSCLE_GAIN;
                    }
                }

                $patient_data = [
                    'weight' => $current_weight,
                    'height' => $data['patient']->height,
                    'age' => $age,
                    'gender' => $data['patient']->gender,
                    'activity_level' => $activity_level,
                    'goal' => $goal,
                    'waist' => $data['patient']->waist_circumference ?? null,
                    'neck' => $data['patient']->neck_circumference ?? null,
                    'hip' => $data['patient']->hip_circumference ?? null
                ];

                $data['calculated_objectives'] = $this->dietetic_nutrition_calculator->complete_nutrition_analysis($patient_data);
            }
        }

        $this->load->view('admin/programs/view', $data);
    }

    /**
     * Create new program
     */
    public function create()
    {
        if (!dietetic_has_permission('create')) {
            access_denied('dietetic');
        }

        if ($this->input->post()) {
            $data = $this->input->post();

            // Set dietitian
            if (!isset($data['dietitian_id'])) {
                $data['dietitian_id'] = get_staff_user_id();
            }

            $program_id = $this->dietetic_programs_model->add($data);

            if ($program_id) {
                // Send notification to patient
                try {
                    if ($this->db->table_exists(db_prefix() . 'dietic_notification_preferences') && isset($data['patient_id'])) {
                        $this->load->model('dietetic/dietetic_notifications_model');

                        // Get program info
                        $program = $this->dietetic_programs_model->get($program_id);

                        // Get dietitian info
                        $dietitian_id = $data['dietitian_id'] ?? get_staff_user_id();
                        $dietitian = $this->staff_model->get($dietitian_id);
                        $dietitian_name = $dietitian ? ($dietitian->firstname . ' ' . $dietitian->lastname) : 'Votre diététicien';

                        // Send notification
                        $this->dietetic_notifications_model->notify_program_assigned(
                            $data['patient_id'],
                            $program->name,
                            $dietitian_name
                        );
                    }
                } catch (Exception $e) {
                    log_activity('Program notification error: ' . $e->getMessage());
                }

                set_alert('success', _l('added_successfully'));
                redirect(admin_url('dietetic/programs/view/' . $program_id));
            } else {
                set_alert('danger', _l('dietetic_error_add_failed'));
            }
        }

        $data['title'] = _l('dietetic_new_program');
        $data['patients'] = $this->dietetic_patients_model->get_all();
        $data['staff'] = $this->staff_model->get();

        // Auto-calculate nutritional objectives from anamnesis data if patient_id is provided
        $data['calculated_objectives'] = null;
        $patient_id = $this->input->get('patient_id');

        if ($patient_id) {
            $patient = $this->dietetic_patients_model->get($patient_id);

            if ($patient) {
                $this->load->library('dietetic/Dietetic_nutrition_calculator');

                // Get patient latest measurement for current weight
                $this->db->where('patient_id', $patient->id);
                $this->db->order_by('measurement_date', 'DESC');
                $this->db->limit(1);
                $latest_measurement = $this->db->get(db_prefix() . 'dietic_measurements')->row();

                $current_weight = $latest_measurement ? $latest_measurement->weight : null;

                // Calculate age from birth_date
                $age = null;
                if ($patient->birth_date) {
                    $birthdate = new DateTime($patient->birth_date);
                    $today = new DateTime();
                    $age = $birthdate->diff($today)->y;
                }

                // Only calculate if we have the minimum required data
                if ($current_weight && $patient->height && $age) {
                    // Map activity level from patient data to calculator constants
                    $activity_map = [
                        'sedentary' => Dietetic_nutrition_calculator::ACTIVITY_SEDENTARY,
                        'light' => Dietetic_nutrition_calculator::ACTIVITY_LIGHT,
                        'moderate' => Dietetic_nutrition_calculator::ACTIVITY_MODERATE,
                        'active' => Dietetic_nutrition_calculator::ACTIVITY_ACTIVE,
                        'very_active' => Dietetic_nutrition_calculator::ACTIVITY_VERY_ACTIVE
                    ];

                    $activity_level = $activity_map[$patient->activity_level] ?? Dietetic_nutrition_calculator::ACTIVITY_MODERATE;

                    // Default to maintenance for new programs
                    $goal = Dietetic_nutrition_calculator::GOAL_MAINTENANCE;

                    $patient_data = [
                        'weight' => $current_weight,
                        'height' => $patient->height,
                        'age' => $age,
                        'gender' => $patient->gender,
                        'activity_level' => $activity_level,
                        'goal' => $goal,
                        'waist' => $patient->waist_circumference ?? null,
                        'neck' => $patient->neck_circumference ?? null,
                        'hip' => $patient->hip_circumference ?? null
                    ];

                    $data['calculated_objectives'] = $this->dietetic_nutrition_calculator->complete_nutrition_analysis($patient_data);
                }
            }
        }

        $this->load->view('admin/programs/form', $data);
    }

    /**
     * Edit program
     *
     * @param int $id
     */
    public function edit($id)
    {
        if (!dietetic_has_permission('edit')) {
            access_denied('dietetic');
        }

        $data['program'] = $this->dietetic_programs_model->get($id);

        if (!$data['program']) {
            show_404();
        }

        if ($this->input->post()) {
            $update_data = $this->input->post();

            if ($this->dietetic_programs_model->update($id, $update_data)) {
                // Send notification to patient
                try {
                    if ($this->db->table_exists(db_prefix() . 'dietic_notification_preferences') && $data['program']->patient_id) {
                        $this->load->model('dietetic/dietetic_notifications_model');

                        // Get updated program info
                        $program = $this->dietetic_programs_model->get($id);

                        // Get dietitian info
                        $dietitian_id = $program->dietitian_id;
                        $dietitian = $this->staff_model->get($dietitian_id);
                        $dietitian_name = $dietitian ? ($dietitian->firstname . ' ' . $dietitian->lastname) : 'Votre diététicien';

                        // Send notification
                        $this->dietetic_notifications_model->notify_program_updated(
                            $program->patient_id,
                            $program->name,
                            $dietitian_name
                        );
                    }
                } catch (Exception $e) {
                    log_activity('Program update notification error: ' . $e->getMessage());
                }

                set_alert('success', _l('updated_successfully'));
                redirect(admin_url('dietetic/programs/view/' . $id));
            } else {
                set_alert('danger', _l('dietetic_error_update_failed'));
            }
        }

        $data['title'] = _l('dietetic_edit_program');
        $data['patients'] = $this->dietetic_patients_model->get_all();
        $data['staff'] = $this->staff_model->get();

        // Auto-calculate nutritional objectives from anamnesis data
        $data['calculated_objectives'] = null;
        $patient = $this->dietetic_patients_model->get($data['program']->patient_id);

        if ($patient) {
            $this->load->library('dietetic/Dietetic_nutrition_calculator');

            // Get patient latest measurement for current weight
            $this->db->where('patient_id', $patient->id);
            $this->db->order_by('measurement_date', 'DESC');
            $this->db->limit(1);
            $latest_measurement = $this->db->get(db_prefix() . 'dietic_measurements')->row();

            $current_weight = $latest_measurement ? $latest_measurement->weight : null;

            // Calculate age from birth_date
            $age = null;
            if ($patient->birth_date) {
                $birthdate = new DateTime($patient->birth_date);
                $today = new DateTime();
                $age = $birthdate->diff($today)->y;
            }

            // Only calculate if we have the minimum required data
            if ($current_weight && $patient->height && $age) {
                // Map activity level from patient data to calculator constants
                $activity_map = [
                    'sedentary' => Dietetic_nutrition_calculator::ACTIVITY_SEDENTARY,
                    'light' => Dietetic_nutrition_calculator::ACTIVITY_LIGHT,
                    'moderate' => Dietetic_nutrition_calculator::ACTIVITY_MODERATE,
                    'active' => Dietetic_nutrition_calculator::ACTIVITY_ACTIVE,
                    'very_active' => Dietetic_nutrition_calculator::ACTIVITY_VERY_ACTIVE
                ];

                $activity_level = $activity_map[$patient->activity_level] ?? Dietetic_nutrition_calculator::ACTIVITY_MODERATE;

                // Determine goal from program objective or default to maintenance
                $goal = Dietetic_nutrition_calculator::GOAL_MAINTENANCE;
                if ($data['program']->objective) {
                    $objective_lower = strtolower($data['program']->objective);
                    if (strpos($objective_lower, 'perte') !== false || strpos($objective_lower, 'perd') !== false || strpos($objective_lower, 'maigrir') !== false) {
                        $goal = Dietetic_nutrition_calculator::GOAL_WEIGHT_LOSS;
                    } elseif (strpos($objective_lower, 'prise') !== false || strpos($objective_lower, 'gagn') !== false || strpos($objective_lower, 'gross') !== false) {
                        $goal = Dietetic_nutrition_calculator::GOAL_WEIGHT_GAIN;
                    } elseif (strpos($objective_lower, 'muscle') !== false || strpos($objective_lower, 'muscul') !== false) {
                        $goal = Dietetic_nutrition_calculator::GOAL_MUSCLE_GAIN;
                    }
                }

                $patient_data = [
                    'weight' => $current_weight,
                    'height' => $patient->height,
                    'age' => $age,
                    'gender' => $patient->gender,
                    'activity_level' => $activity_level,
                    'goal' => $goal,
                    'waist' => $patient->waist_circumference ?? null,
                    'neck' => $patient->neck_circumference ?? null,
                    'hip' => $patient->hip_circumference ?? null
                ];

                $data['calculated_objectives'] = $this->dietetic_nutrition_calculator->complete_nutrition_analysis($patient_data);
            }
        }

        $this->load->view('admin/programs/form', $data);
    }

    /**
     * Delete program
     *
     * @param int $id
     */
    public function delete($id)
    {
        if (!dietetic_has_permission('delete')) {
            ajax_access_denied();
        }

        if ($this->dietetic_programs_model->delete($id)) {
            echo json_encode(['success' => true, 'message' => _l('deleted')]);
        } else {
            echo json_encode(['success' => false, 'message' => _l('dietetic_error_delete_failed')]);
        }
    }

    /**
     * View meal plan
     *
     * @param int $id Meal plan ID
     */
    public function meal_plan($id)
    {
        $data['meal_plan'] = $this->dietetic_meal_plans_model->get($id);

        if (!$data['meal_plan']) {
            show_404();
        }

        $data['program'] = $this->dietetic_programs_model->get($data['meal_plan']->program_id);
        $data['patient'] = $this->dietetic_patients_model->get($data['program']->patient_id);
        $data['meals_by_day'] = $this->dietetic_meal_plans_model->get_meals_by_day($id);
        $data['nutrition_totals'] = $this->dietetic_meal_plans_model->calculate_plan_nutrition($id);
        $data['title'] = $data['meal_plan']->plan_name;

        $this->load->view('admin/programs/meal_plan_view', $data);
    }

    /**
     * Create meal plan for program
     *
     * @param int $program_id
     */
    public function create_meal_plan($program_id)
    {
        if (!dietetic_has_permission('create')) {
            access_denied('dietetic');
        }

        $data['program'] = $this->dietetic_programs_model->get($program_id);

        if (!$data['program']) {
            show_404();
        }

        if ($this->input->post()) {
            $plan_data = $this->input->post();
            $plan_data['program_id'] = $program_id;

            $plan_id = $this->dietetic_meal_plans_model->add($plan_data);

            if ($plan_id) {
                set_alert('success', _l('added_successfully'));
                redirect(admin_url('dietetic/programs/meal_plan/' . $plan_id));
            } else {
                set_alert('danger', _l('dietetic_error_add_failed'));
            }
        }

        $data['title'] = _l('dietetic_new_meal_plan');
        $data['foods'] = $this->dietetic_foods_model->get_active();

        $this->load->view('admin/programs/meal_plan_form', $data);
    }

    /**
     * Edit meal plan
     *
     * @param int $id
     */
    public function edit_meal_plan($id)
    {
        if (!dietetic_has_permission('edit')) {
            access_denied('dietetic');
        }

        $data['meal_plan'] = $this->dietetic_meal_plans_model->get($id);

        if (!$data['meal_plan']) {
            show_404();
        }

        $data['program'] = $this->dietetic_programs_model->get($data['meal_plan']->program_id);

        if ($this->input->post()) {
            $update_data = $this->input->post();

            if ($this->dietetic_meal_plans_model->update($id, $update_data)) {
                set_alert('success', _l('updated_successfully'));
                redirect(admin_url('dietetic/programs/meal_plan/' . $id));
            } else {
                set_alert('danger', _l('dietetic_error_update_failed'));
            }
        }

        $data['title'] = _l('dietetic_edit_meal_plan');
        $data['foods'] = $this->dietetic_foods_model->get_active();
        $data['meals_by_day'] = $this->dietetic_meal_plans_model->get_meals_by_day($id);

        $this->load->view('admin/programs/meal_plan_form', $data);
    }

    /**
     * Add meal via AJAX
     */
    public function add_meal()
    {
        if (!dietetic_has_permission('create')) {
            ajax_access_denied();
        }

        if ($this->input->post()) {
            $data = $this->input->post();

            $meal_id = $this->dietetic_meal_plans_model->add_meal($data);

            if ($meal_id) {
                echo json_encode(['success' => true, 'meal_id' => $meal_id, 'message' => _l('added_successfully')]);
            } else {
                echo json_encode(['success' => false, 'message' => _l('dietetic_error_add_failed')]);
            }
        }
    }

    /**
     * Add food to meal via AJAX
     */
    public function add_food_to_meal()
    {
        if (!dietetic_has_permission('edit')) {
            ajax_access_denied();
        }

        if ($this->input->post()) {
            $data = $this->input->post();

            $id = $this->dietetic_meal_plans_model->add_food_to_meal($data);

            if ($id) {
                echo json_encode(['success' => true, 'id' => $id, 'message' => _l('added_successfully')]);
            } else {
                echo json_encode(['success' => false, 'message' => _l('dietetic_error_add_failed')]);
            }
        }
    }

    /**
     * Remove food from meal via AJAX
     *
     * @param int $id
     */
    public function remove_food_from_meal($id)
    {
        if (!dietetic_has_permission('edit')) {
            ajax_access_denied();
        }

        if ($this->dietetic_meal_plans_model->remove_food_from_meal($id)) {
            echo json_encode(['success' => true, 'message' => _l('deleted')]);
        } else {
            echo json_encode(['success' => false, 'message' => _l('dietetic_error_delete_failed')]);
        }
    }

    /**
     * Meal management - Create/Edit meal (dedicated pages, not AJAX)
     */
    public function meal($action = 'create', $id = null)
    {
        if ($action == 'create') {
            if (!dietetic_has_permission('create')) {
                access_denied('dietetic');
            }

            $meal_plan_id = $this->input->get('meal_plan_id') ?: $this->input->post('meal_plan_id');

            if (!$meal_plan_id) {
                set_alert('danger', 'Meal plan ID required');
                redirect(admin_url('dietetic/programs'));
                return;
            }

            $data['meal_plan'] = $this->dietetic_meal_plans_model->get($meal_plan_id);
            if (!$data['meal_plan']) {
                show_404();
            }

            $data['program'] = $this->dietetic_programs_model->get($data['meal_plan']->program_id);

            // Load approved recipes for selection
            $data['recipes'] = $this->dietetic_recipes_model->get_all([], 'approved');

            if ($this->input->post()) {
                $recipe_id = $this->input->post('recipe_id');

                // If a recipe is selected, get recipe data
                if ($recipe_id) {
                    $recipe = $this->dietetic_recipes_model->get($recipe_id);
                    if (!$recipe) {
                        set_alert('danger', 'Recipe not found');
                        redirect($_SERVER['HTTP_REFERER']);
                        return;
                    }
                }

                // Map form fields to database columns
                $meal_data = [
                    'meal_plan_id' => $this->input->post('meal_plan_id'),
                    'day_of_week' => $this->input->post('day_number'), // Map day_number to day_of_week
                    'meal_type' => $this->input->post('meal_type'),
                    'meal_name' => $recipe_id && $recipe ? $recipe->recipe_name : $this->input->post('meal_name'),
                    'meal_time' => $this->input->post('meal_time'),
                    'instructions' => $recipe_id && $recipe ? $recipe->description : $this->input->post('instructions'),
                    'display_order' => 0
                ];

                $meal_id = $this->dietetic_meal_plans_model->add_meal($meal_data);

                if ($meal_id) {
                    // If a recipe was selected, copy ingredients to meal
                    if ($recipe_id && $recipe && !empty($recipe->ingredients)) {
                        foreach ($recipe->ingredients as $ingredient) {
                            // Get food by name or create reference
                            $this->db->where('food_name', $ingredient->ingredient_name);
                            $food = $this->db->get(db_prefix() . 'dietic_foods')->row();

                            if ($food) {
                                $meal_food_data = [
                                    'meal_id' => $meal_id,
                                    'food_id' => $food->id,
                                    'quantity' => $ingredient->quantity,
                                    'display_order' => $ingredient->order_number
                                ];
                                $this->dietetic_meal_plans_model->add_meal_food($meal_food_data);
                            }
                        }
                    }

                    set_alert('success', 'Meal created successfully' . ($recipe_id ? ' from recipe' : ''));
                    redirect(admin_url('dietetic/programs/meal/edit/' . $meal_id));
                } else {
                    set_alert('danger', 'Failed to create meal');
                }
            }

            $data['title'] = 'Add Meal';
            $this->load->view('admin/programs/meal_form', $data);

        } elseif ($action == 'edit') {
            if (!dietetic_has_permission('edit')) {
                access_denied('dietetic');
            }

            $data['meal'] = $this->dietetic_meal_plans_model->get_meal($id);
            if (!$data['meal']) {
                show_404();
            }

            $data['meal_plan'] = $this->dietetic_meal_plans_model->get($data['meal']->meal_plan_id);
            $data['program'] = $this->dietetic_programs_model->get($data['meal_plan']->program_id);
            $data['meal_foods'] = $this->dietetic_meal_plans_model->get_meal_foods($id);

            if ($this->input->post()) {
                // Map form fields to database columns
                $meal_data = [
                    'day_of_week' => $this->input->post('day_number'), // Map day_number to day_of_week
                    'meal_type' => $this->input->post('meal_type'),
                    'meal_name' => $this->input->post('meal_name'),
                    'meal_time' => $this->input->post('meal_time'),
                    'instructions' => $this->input->post('instructions')
                ];

                if ($this->dietetic_meal_plans_model->update_meal($id, $meal_data)) {
                    set_alert('success', 'Meal updated successfully');
                } else {
                    set_alert('danger', 'Failed to update meal');
                }
                redirect(admin_url('dietetic/programs/meal/edit/' . $id));
            }

            $data['title'] = 'Edit Meal';
            $this->load->view('admin/programs/meal_form', $data);
        }
    }

    /**
     * Meal food management - Add/Edit food in meal
     */
    public function meal_food($action = 'create', $id = null)
    {
        if ($action == 'create') {
            if (!dietetic_has_permission('create')) {
                access_denied('dietetic');
            }

            $meal_id = $this->input->get('meal_id') ?: $this->input->post('meal_id');

            if (!$meal_id) {
                set_alert('danger', 'Meal ID required');
                redirect(admin_url('dietetic/programs'));
                return;
            }

            $data['meal'] = $this->dietetic_meal_plans_model->get_meal($meal_id);
            if (!$data['meal']) {
                show_404();
            }

            $data['foods'] = $this->dietetic_foods_model->get_all();
            $data['days'] = ['1' => 'Monday', '2' => 'Tuesday', '3' => 'Wednesday', '4' => 'Thursday', '5' => 'Friday', '6' => 'Saturday', '7' => 'Sunday'];

            if ($this->input->post()) {
                $food_data = $this->input->post();
                $food_id = $this->dietetic_meal_plans_model->add_food_to_meal($food_data);

                if ($food_id) {
                    set_alert('success', 'Food added to meal successfully');
                    redirect(admin_url('dietetic/programs/meal/edit/' . $meal_id));
                } else {
                    set_alert('danger', 'Failed to add food');
                }
            }

            $data['title'] = 'Add Food to Meal';
            $this->load->view('admin/programs/meal_food_form', $data);

        } elseif ($action == 'edit') {
            if (!dietetic_has_permission('edit')) {
                access_denied('dietetic');
            }

            $data['meal_food'] = $this->dietetic_meal_plans_model->get_meal_food($id);
            if (!$data['meal_food']) {
                show_404();
            }

            $data['meal'] = $this->dietetic_meal_plans_model->get_meal($data['meal_food']->meal_id);
            $data['foods'] = $this->dietetic_foods_model->get_all();
            $data['days'] = ['1' => 'Monday', '2' => 'Tuesday', '3' => 'Wednesday', '4' => 'Thursday', '5' => 'Friday', '6' => 'Saturday', '7' => 'Sunday'];

            if ($this->input->post()) {
                $food_data = $this->input->post();
                if ($this->dietetic_meal_plans_model->update_food_in_meal($id, $food_data)) {
                    set_alert('success', 'Food updated successfully');
                } else {
                    set_alert('danger', 'Failed to update food');
                }
                redirect(admin_url('dietetic/programs/meal/edit/' . $data['meal']->id));
            }

            $data['title'] = 'Edit Food in Meal';
            $this->load->view('admin/programs/meal_food_form', $data);

        } elseif ($action == 'delete') {
            if (!dietetic_has_permission('delete')) {
                access_denied('dietetic');
            }

            $meal_food = $this->dietetic_meal_plans_model->get_meal_food($id);
            if ($meal_food) {
                if ($this->dietetic_meal_plans_model->remove_food_from_meal($id)) {
                    set_alert('success', 'Food removed from meal');
                } else {
                    set_alert('danger', 'Failed to remove food');
                }
                redirect(admin_url('dietetic/programs/meal/edit/' . $meal_food->meal_id));
            } else {
                show_404();
            }
        }
    }

    /**
     * Generate PDF for meal plan
     *
     * @param int $id
     */
    public function generate_pdf($id)
    {
        $this->load->library('dietetic/dietetic_pdf');

        $meal_plan = $this->dietetic_meal_plans_model->get($id);

        if (!$meal_plan) {
            show_404();
        }

        $program = $this->dietetic_programs_model->get($meal_plan->program_id);
        $patient = $this->dietetic_patients_model->get($program->patient_id);

        $pdf_data = [
            'meal_plan' => $meal_plan,
            'program'   => $program,
            'patient'   => $patient,
            'meals_by_day' => $this->dietetic_meal_plans_model->get_meals_by_day($id),
        ];

        $this->dietetic_pdf->generate_meal_plan_pdf($pdf_data);
    }
}
