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
                set_alert('success', _l('added_successfully'));
                redirect(admin_url('dietetic/programs/view/' . $program_id));
            } else {
                set_alert('danger', _l('dietetic_error_add_failed'));
            }
        }

        $data['title'] = _l('dietetic_new_program');
        $data['patients'] = $this->dietetic_patients_model->get_all();
        $data['staff'] = $this->staff_model->get();

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
                set_alert('success', _l('updated_successfully'));
                redirect(admin_url('dietetic/programs/view/' . $id));
            } else {
                set_alert('danger', _l('dietetic_error_update_failed'));
            }
        }

        $data['title'] = _l('dietetic_edit_program');
        $data['patients'] = $this->dietetic_patients_model->get_all();
        $data['staff'] = $this->staff_model->get();

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
