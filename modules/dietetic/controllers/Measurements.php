<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Measurements extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        // Load Perfex models
        $this->load->model('clients_model');
        $this->load->model('staff_model');

        // Load dietetic models
        $this->load->model('dietetic/dietetic_measurements_model');
        $this->load->model('dietetic/dietetic_patients_model');
        $this->load->helper('dietetic/dietetic');

        if (!dietetic_has_permission('view')) {
            access_denied('dietetic');
        }
    }

    /**
     * Create new measurement
     */
    public function create()
    {
        // Simplified permission check
        if (!is_admin() && !has_permission('dietetic', '', 'create')) {
            access_denied('dietetic');
        }

        $patient_id = $this->input->get('patient_id') ?: $this->input->post('patient_id');

        if (!$patient_id) {
            set_alert('danger', 'Patient ID is required');
            redirect(admin_url('dietetic/patients'));
            return;
        }

        $data['patient'] = $this->dietetic_patients_model->get($patient_id);

        if (!$data['patient']) {
            set_alert('danger', 'Patient not found');
            redirect(admin_url('dietetic/patients'));
            return;
        }

        if ($this->input->post()) {
            $post_data = [
                'patient_id' => $this->input->post('patient_id'),
                'measurement_date' => $this->input->post('measurement_date'),
                'weight' => $this->input->post('weight'),
                'body_fat' => $this->input->post('body_fat'),
                'muscle_mass' => $this->input->post('muscle_mass'),
                'water_percentage' => $this->input->post('water_percentage'),
                'waist' => $this->input->post('waist'),
                'hips' => $this->input->post('hips'),
                'chest' => $this->input->post('chest'),
                'thigh' => $this->input->post('thigh'),
                'notes' => $this->input->post('notes'),
                'added_by' => get_staff_user_id(),
                'added_by_type' => 'staff'
            ];

            try {
                $measurement_id = $this->dietetic_measurements_model->add($post_data);

                if ($measurement_id) {
                    set_alert('success', 'Measurement added successfully');
                    redirect(admin_url('dietetic/patients/view/' . $patient_id));
                    return;
                } else {
                    set_alert('danger', 'Failed to add measurement');
                }
            } catch (Exception $e) {
                set_alert('danger', 'Error: ' . $e->getMessage());
            }
        }

        $data['title'] = 'Add Measurement';
        $this->load->view('admin/measurements/form', $data);
    }

    /**
     * Edit measurement
     */
    public function edit($id)
    {
        if (!dietetic_has_permission('edit')) {
            access_denied('dietetic');
        }

        $data['measurement'] = $this->dietetic_measurements_model->get($id);
        if (!$data['measurement']) {
            show_404();
        }

        $data['patient'] = $this->dietetic_patients_model->get($data['measurement']->patient_id);
        if (!$data['patient']) {
            show_404();
        }

        if ($this->input->post()) {
            $post_data = $this->input->post();

            if ($this->dietetic_measurements_model->update($id, $post_data)) {
                set_alert('success', _l('updated_successfully'));
            } else {
                set_alert('danger', _l('dietetic_error_update_failed'));
            }

            redirect(admin_url('dietetic/patients/view/' . $data['patient']->id));
        }

        $data['title'] = _l('dietetic_edit_measurement');
        $this->load->view('admin/measurements/form', $data);
    }

    /**
     * Delete measurement
     */
    public function delete($id)
    {
        if (!dietetic_has_permission('delete')) {
            access_denied('dietetic');
        }

        $measurement = $this->dietetic_measurements_model->get($id);
        if (!$measurement) {
            show_404();
        }

        if ($this->dietetic_measurements_model->delete($id)) {
            set_alert('success', _l('deleted'));
        } else {
            set_alert('danger', _l('dietetic_error_delete_failed'));
        }

        redirect(admin_url('dietetic/patients/view/' . $measurement->patient_id));
    }
}
