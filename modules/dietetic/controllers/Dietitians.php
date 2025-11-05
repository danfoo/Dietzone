<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dietitians extends AdminController
{
    private $ratings_model_loaded = false;

    public function __construct()
    {
        parent::__construct();

        // Load models
        $this->load->model('staff_model');
        $this->load->model('dietetic/dietetic_patients_model');
        $this->load->helper('dietetic/dietetic');

        if (!dietetic_has_permission('view')) {
            access_denied('dietetic');
        }
    }

    /**
     * Lazy load ratings model - only load when needed and check if table exists
     *
     * @return bool True if model loaded successfully
     */
    private function load_ratings_model()
    {
        if ($this->ratings_model_loaded) {
            return true;
        }

        try {
            // Check if table exists first
            $table_name = db_prefix() . 'dietic_ratings';
            if (!$this->db->table_exists($table_name)) {
                return false;
            }

            $this->load->model('dietetic/dietetic_ratings_model');
            $this->ratings_model_loaded = true;
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * List all dietitians with their ratings
     */
    public function index()
    {
        $data['title'] = 'Diététiciens';

        if ($this->load_ratings_model()) {
            try {
                $data['dietitians'] = $this->dietetic_ratings_model->get_all_dietitians_with_ratings();
            } catch (Exception $e) {
                $data['dietitians'] = [];
                $data['error'] = 'Erreur lors du chargement des données: ' . $e->getMessage();
            }
        } else {
            // Ratings table doesn't exist yet
            $data['dietitians'] = [];
            $data['error'] = 'Le système de notation n\'est pas encore installé. <a href="' . base_url('modules/dietetic/install_ratings.php') . '" target="_blank" class="btn btn-primary btn-sm"><i class="fa fa-download"></i> Installer le système de notation</a>';
        }

        $this->load->view('admin/dietitians/list', $data);
    }

    /**
     * View dietitian profile with detailed ratings
     *
     * @param int $id Staff ID
     */
    public function view($id)
    {
        $data['dietitian'] = $this->staff_model->get($id);

        if (!$data['dietitian']) {
            show_404();
        }

        $data['title'] = $data['dietitian']->firstname . ' ' . $data['dietitian']->lastname;

        // Get ratings if table exists
        if ($this->load_ratings_model()) {
            try {
                $data['ratings'] = $this->dietetic_ratings_model->get_by_dietitian($id, false); // Include all ratings
                $data['average_ratings'] = $this->dietetic_ratings_model->get_dietitian_average($id);
            } catch (Exception $e) {
                $data['ratings'] = [];
                $data['average_ratings'] = null;
                $data['error'] = 'Erreur lors du chargement des notes: ' . $e->getMessage();
            }
        } else {
            $data['ratings'] = [];
            $data['average_ratings'] = null;
            $data['error'] = 'Le système de notation n\'est pas encore installé.';
        }

        // Get statistics
        $data['stats'] = $this->get_dietitian_stats($id);

        $this->load->view('admin/dietitians/view', $data);
    }

    /**
     * Get dietitian statistics
     *
     * @param int $dietitian_id
     * @return object
     */
    private function get_dietitian_stats($dietitian_id)
    {
        $stats = new stdClass();

        // Total patients
        $this->db->where('dietitian_id', $dietitian_id);
        $stats->total_patients = $this->db->count_all_results(db_prefix() . 'dietic_patients');

        // Active patients
        $this->db->where('dietitian_id', $dietitian_id);
        $this->db->where('status', 'active');
        $stats->active_patients = $this->db->count_all_results(db_prefix() . 'dietic_patients');

        // Total consultations
        $this->db->where('dietitian_id', $dietitian_id);
        $stats->total_consultations = $this->db->count_all_results(db_prefix() . 'dietic_consultations');

        // Completed consultations
        $this->db->where('dietitian_id', $dietitian_id);
        $this->db->where('status', 'completed');
        $stats->completed_consultations = $this->db->count_all_results(db_prefix() . 'dietic_consultations');

        // Total programs
        $this->db->where('dietitian_id', $dietitian_id);
        $stats->total_programs = $this->db->count_all_results(db_prefix() . 'dietic_programs');

        // Active programs
        $this->db->where('dietitian_id', $dietitian_id);
        $this->db->where('status', 'active');
        $stats->active_programs = $this->db->count_all_results(db_prefix() . 'dietic_programs');

        return $stats;
    }

    /**
     * Delete a rating (admin only)
     *
     * @param int $id Rating ID
     */
    public function delete_rating($id)
    {
        if (!is_admin()) {
            ajax_access_denied();
        }

        if (!$this->load_ratings_model()) {
            echo json_encode(['success' => false, 'message' => 'Le système de notation n\'est pas installé']);
            return;
        }

        if ($this->dietetic_ratings_model->delete($id)) {
            echo json_encode(['success' => true, 'message' => 'Note supprimée avec succès']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression']);
        }
    }

    /**
     * Toggle rating visibility
     *
     * @param int $id Rating ID
     */
    public function toggle_rating_visibility($id)
    {
        if (!is_admin()) {
            ajax_access_denied();
        }

        if (!$this->load_ratings_model()) {
            echo json_encode(['success' => false, 'message' => 'Le système de notation n\'est pas installé']);
            return;
        }

        $rating = $this->dietetic_ratings_model->get($id);

        if ($rating) {
            $new_status = $rating->is_public ? 0 : 1;
            $this->dietetic_ratings_model->update($id, ['is_public' => $new_status]);

            echo json_encode([
                'success' => true,
                'message' => $new_status ? 'Note rendue publique' : 'Note masquée',
                'is_public' => $new_status
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Note introuvable']);
        }
    }
}
