<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Activities extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        // Load helper
        $this->load->helper('dietetic/dietetic');

        // Load models
        $this->load->model('dietetic_activities_model');

        // Check permission
        if (!dietetic_has_permission('view')) {
            access_denied('dietetic');
        }
    }

    /**
     * Get all available activities
     */
    public function get_activities()
    {
        header('Content-Type: application/json');

        try {
            $activities = $this->dietetic_activities_model->get_all_activities();

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
     * Get patient activities with statistics
     */
    public function get_patient_activities($patient_id)
    {
        header('Content-Type: application/json');

        if (!$patient_id) {
            echo json_encode([
                'success' => false,
                'message' => 'Patient ID requis'
            ]);
            return;
        }

        try {
            $activities = $this->dietetic_activities_model->get_patient_activities($patient_id);
            $stats = $this->dietetic_activities_model->get_patient_stats($patient_id);

            echo json_encode([
                'success' => true,
                'activities' => $activities,
                'stats' => $stats
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Erreur lors du chargement des activités du patient'
            ]);
        }
    }

    /**
     * Add patient activity with CSRF protection
     */
    public function add_patient_activity()
    {
        header('Content-Type: application/json');

        // CSRF Protection
        $csrf_token = $this->input->post('csrf_token');
        if (!$csrf_token || $csrf_token !== $this->security->get_csrf_hash()) {
            echo json_encode([
                'success' => false,
                'message' => 'Token CSRF invalide',
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
            return;
        }

        // Validate required fields
        $patient_id = $this->input->post('patient_id');
        $activity_id = $this->input->post('activity_id');
        $duration_minutes = $this->input->post('duration_minutes');
        $kcal_burned = $this->input->post('kcal_burned');
        $activity_date = $this->input->post('activity_date');

        if (!$patient_id || !$activity_id || !$duration_minutes || !$kcal_burned || !$activity_date) {
            echo json_encode([
                'success' => false,
                'message' => 'Tous les champs obligatoires doivent être remplis',
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
            return;
        }

        // Prepare data
        $data = [
            'patient_id' => $patient_id,
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
                echo json_encode([
                    'success' => true,
                    'message' => 'Activité ajoutée avec succès',
                    'csrf_token' => $this->security->get_csrf_hash()
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Erreur lors de l\'ajout de l\'activité',
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
     * Delete patient activity with CSRF protection
     */
    public function delete_patient_activity($activity_id)
    {
        header('Content-Type: application/json');

        // CSRF Protection
        $csrf_token = $this->input->post('csrf_token');
        if (!$csrf_token || $csrf_token !== $this->security->get_csrf_hash()) {
            echo json_encode([
                'success' => false,
                'message' => 'Token CSRF invalide',
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
            return;
        }

        if (!$activity_id) {
            echo json_encode([
                'success' => false,
                'message' => 'ID d\'activité requis',
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
     * Admin: Manage activities (CRUD for activity database)
     */
    public function manage()
    {
        if (!dietetic_has_permission('manage')) {
            access_denied('dietetic');
        }

        // Load activities directly
        $data['title'] = 'Gestion des Activités';
        $data['activities'] = $this->dietetic_activities_model->get_all_activities();
        $this->load->view('admin/activities/manage', $data);
    }

    /**
     * Add new activity to database (admin only)
     */
    public function add_activity()
    {
        header('Content-Type: application/json');

        if (!dietetic_has_permission('manage')) {
            echo json_encode([
                'success' => false,
                'message' => 'Permission refusée'
            ]);
            return;
        }

        // CSRF Protection - Use dynamic token name
        $csrf_token_name = $this->security->get_csrf_token_name();
        $csrf_token = $this->input->post($csrf_token_name);
        if (!$csrf_token || $csrf_token !== $this->security->get_csrf_hash()) {
            echo json_encode([
                'success' => false,
                'message' => 'Token CSRF invalide',
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
            return;
        }

        $name = $this->input->post('name');
        $kcal_per_minute = $this->input->post('kcal_per_minute');
        $category = $this->input->post('category');

        if (!$name || !$kcal_per_minute || !$category) {
            echo json_encode([
                'success' => false,
                'message' => 'Tous les champs sont requis',
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
            return;
        }

        $data = [
            'name' => $name,
            'kcal_per_minute' => $kcal_per_minute,
            'category' => $category,
            'description' => $this->input->post('description'),
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ];

        try {
            $result = $this->dietetic_activities_model->add_activity($data);

            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Activité créée avec succès',
                    'csrf_token' => $this->security->get_csrf_hash()
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Erreur lors de la création',
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
     * Update activity
     */
    public function update_activity($activity_id)
    {
        header('Content-Type: application/json');

        if (!dietetic_has_permission('manage')) {
            echo json_encode([
                'success' => false,
                'message' => 'Permission refusée'
            ]);
            return;
        }

        // CSRF Protection - Use dynamic token name
        $csrf_token_name = $this->security->get_csrf_token_name();
        $csrf_token = $this->input->post($csrf_token_name);
        if (!$csrf_token || $csrf_token !== $this->security->get_csrf_hash()) {
            echo json_encode([
                'success' => false,
                'message' => 'Token CSRF invalide',
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
            return;
        }

        $data = [
            'name' => $this->input->post('name'),
            'kcal_per_minute' => $this->input->post('kcal_per_minute'),
            'category' => $this->input->post('category'),
            'description' => $this->input->post('description'),
            'is_active' => $this->input->post('is_active'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        try {
            $result = $this->dietetic_activities_model->update_activity($activity_id, $data);

            echo json_encode([
                'success' => $result,
                'message' => $result ? 'Activité mise à jour' : 'Erreur lors de la mise à jour',
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Erreur serveur: ' . $e->getMessage(),
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
        }
    }

    /**
     * Delete activity from database
     */
    public function delete_activity($activity_id)
    {
        header('Content-Type: application/json');

        if (!dietetic_has_permission('manage')) {
            echo json_encode([
                'success' => false,
                'message' => 'Permission refusée'
            ]);
            return;
        }

        // CSRF Protection - Use dynamic token name
        $csrf_token_name = $this->security->get_csrf_token_name();
        $csrf_token = $this->input->post($csrf_token_name);
        if (!$csrf_token || $csrf_token !== $this->security->get_csrf_hash()) {
            echo json_encode([
                'success' => false,
                'message' => 'Token CSRF invalide',
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
            return;
        }

        try {
            $result = $this->dietetic_activities_model->delete_activity($activity_id);

            echo json_encode([
                'success' => $result,
                'message' => $result ? 'Activité supprimée' : 'Erreur lors de la suppression',
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Erreur serveur: ' . $e->getMessage(),
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
        }
    }
}
