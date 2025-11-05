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
            $weight = $this->input->post('weight');
            $body_fat_input = $this->input->post('body_fat');
            $muscle_mass_input = $this->input->post('muscle_mass');
            $patient = $data['patient'];

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

            // Filter and prepare data
            $post_data = [
                'patient_id' => $this->input->post('patient_id'),
                'measurement_date' => $this->input->post('measurement_date'),
                'weight' => $weight,
                'bmi' => $bmi,
                'body_fat' => $body_fat,
                'muscle_mass' => $muscle_mass,
                'waist' => $this->input->post('waist'),
                'hips' => $this->input->post('hips'),
                'chest' => $this->input->post('chest'),
                'arms' => $this->input->post('arms'),
                'thighs' => $this->input->post('thigh'),
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
            $weight = $this->input->post('weight');
            $body_fat_input = $this->input->post('body_fat');
            $muscle_mass_input = $this->input->post('muscle_mass');
            $patient = $data['patient'];

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

            // Filter POST data to only include valid fields
            $update_data = [
                'measurement_date' => $this->input->post('measurement_date'),
                'weight' => $weight,
                'bmi' => $bmi,
                'body_fat' => $body_fat,
                'muscle_mass' => $muscle_mass,
                'waist' => $this->input->post('waist'),
                'hips' => $this->input->post('hips'),
                'chest' => $this->input->post('chest'),
                'arms' => $this->input->post('arms'),
                'thighs' => $this->input->post('thigh'),
                'notes' => $this->input->post('notes'),
            ];

            if ($this->dietetic_measurements_model->update($id, $update_data)) {
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
