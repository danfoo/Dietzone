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

        // Get food surveys (if installed)
        $data['food_surveys'] = [];
        if ($this->db->table_exists(db_prefix() . 'dietic_food_surveys')) {
            $this->load->model('dietetic/dietetic_food_surveys_model');
            $data['food_surveys'] = $this->dietetic_food_surveys_model->get_by_patient($id);
        }

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
                set_alert('success', _l('added_successfully'));
                redirect(admin_url('dietetic/patients/view/' . $patient_id));
            } else {
                set_alert('danger', _l('dietetic_error_patient_exists'));
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
            // 1. Don't have a patient record yet, OR
            // 2. Have a patient assigned to them

            $all_clients = $this->clients_model->get();
            $filtered_clients = [];

            foreach ($all_clients as $client) {
                // Check if this client has a patient
                $existing_patient = $this->dietetic_patients_model->get_by_client($client['userid']);

                if (!$existing_patient) {
                    // Client has no patient yet - available for creation
                    $filtered_clients[] = $client;
                } elseif ($this->db->table_exists(db_prefix() . 'dietic_patient_dietitians')) {
                    // Check if dietitian is assigned to this patient
                    $this->load->model('dietetic/dietetic_patient_dietitians_model');
                    if ($this->dietetic_patient_dietitians_model->has_access($existing_patient->id, $current_staff_id)) {
                        $filtered_clients[] = $client;
                    }
                } elseif ($existing_patient->dietitian_id == $current_staff_id) {
                    // Fallback: dietitian owns this patient
                    $filtered_clients[] = $client;
                }
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
            // Filter POST data to only include valid patient fields
            $allowed_fields = [
                'dietitian_id', 'status', 'gender', 'birth_date', 'phone', 'email',
                'emergency_contact', 'emergency_phone', 'medical_conditions', 'allergies',
                'medications', 'lifestyle_notes', 'dietary_preferences', 'activity_level',
                'initial_weight', 'target_weight', 'height', 'objective'
            ];

            $update_data = [];
            foreach ($allowed_fields as $field) {
                if ($this->input->post($field) !== null) {
                    $update_data[$field] = $this->input->post($field);
                }
            }

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
        // Only admins can manage assignments
        if (!is_admin()) {
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
        // Only admins can manage assignments
        if (!is_admin()) {
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
        // Only admins can manage assignments
        if (!is_admin()) {
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
}
