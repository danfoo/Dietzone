<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Patients extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('dietetic/dietetic_patients_model');
        $this->load->model('dietetic/dietetic_measurements_model');
        $this->load->model('dietetic/dietetic_consultations_model');
        $this->load->model('dietetic/dietetic_programs_model');
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
        $data['patients'] = $this->dietetic_patients_model->get_all();

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

        $this->load->view('admin/patients/view', $data);
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
            $data = $this->input->post();

            // Set dietitian
            if (!isset($data['dietitian_id'])) {
                $data['dietitian_id'] = get_staff_user_id();
            }

            $patient_id = $this->dietetic_patients_model->add($data);

            if ($patient_id) {
                set_alert('success', _l('added_successfully'));
                redirect(admin_url('dietetic/patients/view/' . $patient_id));
            } else {
                set_alert('danger', _l('dietetic_error_patient_exists'));
            }
        }

        $data['title'] = _l('dietetic_new_patient');

        // Get all clients
        $data['clients'] = $this->clients_model->get();

        // Get staff members (dietitians)
        $data['staff'] = $this->staff_model->get();

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
            $update_data = $this->input->post();

            if ($this->dietetic_patients_model->update($id, $update_data)) {
                set_alert('success', _l('updated_successfully'));
                redirect(admin_url('dietetic/patients/view/' . $id));
            } else {
                set_alert('danger', _l('dietetic_error_update_failed'));
            }
        }

        $data['title'] = _l('dietetic_edit_patient');

        // Get staff members (dietitians)
        $data['staff'] = $this->staff_model->get();

        $this->load->view('admin/patients/form', $data);
    }

    /**
     * Delete patient
     *
     * @param int $id
     */
    public function delete($id)
    {
        if (!dietetic_has_permission('delete')) {
            ajax_access_denied();
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
     * Add measurement via AJAX
     */
    public function add_measurement()
    {
        if (!dietetic_has_permission('edit')) {
            ajax_access_denied();
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            $data['added_by'] = get_staff_user_id();
            $data['added_by_type'] = 'staff';

            $measurement_id = $this->dietetic_measurements_model->add($data);

            if ($measurement_id) {
                echo json_encode(['success' => true, 'message' => _l('added_successfully')]);
            } else {
                echo json_encode(['success' => false, 'message' => _l('dietetic_error_add_failed')]);
            }
        }
    }

    /**
     * Delete measurement via AJAX
     *
     * @param int $id
     */
    public function delete_measurement($id)
    {
        if (!dietetic_has_permission('delete')) {
            ajax_access_denied();
        }

        if ($this->dietetic_measurements_model->delete($id)) {
            echo json_encode(['success' => true, 'message' => _l('deleted')]);
        } else {
            echo json_encode(['success' => false, 'message' => _l('dietetic_error_delete_failed')]);
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
}
