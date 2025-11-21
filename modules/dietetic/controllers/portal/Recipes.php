<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Portal Recipes Controller - For patient access
 */
class Recipes extends ClientsController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('dietetic/dietetic_recipes_model');
        $this->load->model('dietetic/dietetic_patients_model');
    }

    /**
     * List recipes assigned to the logged-in patient
     */
    public function index()
    {
        if (!is_client_logged_in()) {
            redirect(site_url('authentication/login'));
        }

        $client_id = get_client_user_id();

        // Find patient linked to this client
        $this->db->where('client_id', $client_id);
        $patient = $this->db->get(db_prefix() . 'dietic_patients')->row();

        if (!$patient) {
            $data['title'] = 'Mes Recettes';
            $data['error'] = 'Aucun profil patient trouvé pour votre compte.';
            $data['recipes'] = [];
            $this->load->view('portal/recipes/list', $data);
            return;
        }

        // Get recipes assigned to this patient
        $data['recipes'] = $this->dietetic_recipes_model->get_patient_recipes($patient->id);
        $data['patient'] = $patient;
        $data['title'] = 'Mes Recettes';

        $this->load->view('portal/recipes/list', $data);
    }

    /**
     * View recipe detail
     */
    public function view($id)
    {
        if (!is_client_logged_in()) {
            redirect(site_url('authentication/login'));
        }

        $client_id = get_client_user_id();

        // Find patient linked to this client
        $this->db->where('client_id', $client_id);
        $patient = $this->db->get(db_prefix() . 'dietic_patients')->row();

        if (!$patient) {
            show_404();
        }

        // Get recipe
        $recipe = $this->dietetic_recipes_model->get($id);

        if (!$recipe || $recipe->status != 'approved') {
            show_404();
        }

        // Verify this recipe is assigned to this patient
        $this->db->where('recipe_id', $id);
        $this->db->where('patient_id', $patient->id);
        $assignment = $this->db->get(db_prefix() . 'dietic_recipe_assignments')->row();

        if (!$assignment) {
            // Recipe not assigned to this patient
            show_404();
        }

        $data['recipe'] = $recipe;
        $data['patient'] = $patient;
        $data['assignment'] = $assignment;
        $data['title'] = $recipe->name;

        $this->load->view('portal/recipes/view', $data);
    }
}
