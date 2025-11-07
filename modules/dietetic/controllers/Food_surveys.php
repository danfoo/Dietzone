<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Food_surveys extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        // Load models
        $this->load->model('dietetic/dietetic_food_surveys_model');
        $this->load->model('dietetic/dietetic_patients_model');
        $this->load->model('dietetic/dietetic_programs_model');
        $this->load->model('staff_model');
        $this->load->helper('dietetic/dietetic');

        if (!dietetic_has_permission('view')) {
            access_denied('dietetic');
        }
    }

    /**
     * List all food surveys
     */
    public function index()
    {
        $data['title'] = 'Enquêtes Alimentaires';
        $data['surveys'] = $this->dietetic_food_surveys_model->get_all();

        // Calculate completion percentages
        foreach ($data['surveys'] as &$survey) {
            $survey->completion_percentage = $this->dietetic_food_surveys_model->get_completion_percentage($survey->id);
        }

        $this->load->view('admin/food_surveys/list', $data);
    }

    /**
     * View food survey detail
     *
     * @param int $id
     */
    public function view($id)
    {
        $data['survey'] = $this->dietetic_food_surveys_model->get($id);

        if (!$data['survey']) {
            show_404();
        }

        $data['title'] = $data['survey']->survey_name;
        $data['entries'] = $this->dietetic_food_surveys_model->get_entries($id);
        $data['completion_percentage'] = $this->dietetic_food_surveys_model->get_completion_percentage($id);

        $this->load->view('admin/food_surveys/view', $data);
    }

    /**
     * Create new food survey
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

            $survey_id = $this->dietetic_food_surveys_model->add($data);

            if ($survey_id) {
                set_alert('success', 'Enquête alimentaire créée avec succès');
                redirect(admin_url('dietetic/food_surveys/view/' . $survey_id));
            } else {
                set_alert('danger', 'Erreur lors de la création de l\'enquête alimentaire');
            }
        }

        $data['title'] = 'Nouvelle Enquête Alimentaire';
        $data['patients'] = $this->dietetic_patients_model->get_all();
        $data['programs'] = $this->dietetic_programs_model->get_all();
        $data['staff'] = $this->staff_model->get();

        $this->load->view('admin/food_surveys/form', $data);
    }

    /**
     * Edit food survey
     *
     * @param int $id
     */
    public function edit($id)
    {
        if (!dietetic_has_permission('edit')) {
            access_denied('dietetic');
        }

        $data['survey'] = $this->dietetic_food_surveys_model->get($id);

        if (!$data['survey']) {
            show_404();
        }

        if ($this->input->post()) {
            $update_data = $this->input->post();

            if ($this->dietetic_food_surveys_model->update($id, $update_data)) {
                set_alert('success', 'Enquête alimentaire mise à jour avec succès');
                redirect(admin_url('dietetic/food_surveys/view/' . $id));
            } else {
                set_alert('danger', 'Erreur lors de la mise à jour de l\'enquête alimentaire');
            }
        }

        $data['title'] = 'Modifier l\'Enquête Alimentaire';
        $data['patients'] = $this->dietetic_patients_model->get_all();
        $data['programs'] = $this->dietetic_programs_model->get_all();
        $data['staff'] = $this->staff_model->get();

        $this->load->view('admin/food_surveys/form', $data);
    }

    /**
     * Delete food survey
     *
     * @param int $id
     */
    public function delete($id)
    {
        if (!dietetic_has_permission('delete')) {
            ajax_access_denied();
        }

        if ($this->dietetic_food_surveys_model->delete($id)) {
            echo json_encode(['success' => true, 'message' => 'Enquête alimentaire supprimée avec succès']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression']);
        }
    }

    /**
     * View daily entry details
     *
     * @param int $entry_id
     */
    public function view_entry($entry_id)
    {
        $data['entry'] = $this->dietetic_food_surveys_model->get_entry($entry_id);

        if (!$data['entry']) {
            show_404();
        }

        $data['survey'] = $this->dietetic_food_surveys_model->get($data['entry']->survey_id);
        $data['beverages'] = $this->dietetic_food_surveys_model->get_beverages($entry_id);
        $data['recommendations'] = $this->dietetic_food_surveys_model->get_recommendations($entry_id);

        // Get comments for each recommendation
        foreach ($data['recommendations'] as &$recommendation) {
            $recommendation->comments = $this->dietetic_food_surveys_model->get_comments($recommendation->id);
        }

        $data['title'] = 'Entrée du ' . date('d/m/Y', strtotime($data['entry']->entry_date));

        $this->load->view('admin/food_surveys/view_entry', $data);
    }

    /**
     * Add recommendation to an entry
     */
    public function add_recommendation()
    {
        if (!dietetic_has_permission('edit')) {
            ajax_access_denied();
        }

        if ($this->input->post()) {
            $data = [
                'entry_id' => $this->input->post('entry_id'),
                'dietitian_id' => get_staff_user_id(),
                'recommendation_text' => $this->input->post('recommendation_text')
            ];

            $recommendation_id = $this->dietetic_food_surveys_model->add_recommendation($data);

            if ($recommendation_id) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Recommandation ajoutée avec succès'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Erreur lors de l\'ajout de la recommandation'
                ]);
            }
        }
    }

    /**
     * Update recommendation
     */
    public function update_recommendation($id)
    {
        if (!dietetic_has_permission('edit')) {
            ajax_access_denied();
        }

        if ($this->input->post()) {
            $data = [
                'recommendation_text' => $this->input->post('recommendation_text')
            ];

            if ($this->dietetic_food_surveys_model->update_recommendation($id, $data)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Recommandation mise à jour avec succès'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Erreur lors de la mise à jour de la recommandation'
                ]);
            }
        }
    }

    /**
     * Delete recommendation
     */
    public function delete_recommendation($id)
    {
        if (!dietetic_has_permission('delete')) {
            ajax_access_denied();
        }

        if ($this->dietetic_food_surveys_model->delete_recommendation($id)) {
            echo json_encode([
                'success' => true,
                'message' => 'Recommandation supprimée avec succès'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Erreur lors de la suppression de la recommandation'
            ]);
        }
    }

    /**
     * Change survey status
     */
    public function change_status($id)
    {
        if (!dietetic_has_permission('edit')) {
            ajax_access_denied();
        }

        $status = $this->input->post('status');

        if ($this->dietetic_food_surveys_model->update($id, ['status' => $status])) {
            echo json_encode([
                'success' => true,
                'message' => 'Statut mis à jour avec succès'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du statut'
            ]);
        }
    }
}
