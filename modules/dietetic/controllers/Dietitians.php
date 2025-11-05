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
            $data['error'] = 'Le système de notation n\'est pas encore installé. <a href="' . admin_url('dietetic/dietitians/install') . '" class="btn btn-primary btn-sm"><i class="fa fa-download"></i> Installer le système de notation</a>';
        }

        $this->load->view('admin/dietitians/list', $data);
    }

    /**
     * Install ratings system
     */
    public function install()
    {
        if (!is_admin()) {
            access_denied('Dietetic - Install Ratings');
        }

        $data['title'] = 'Installation du Système de Notation';

        // Check if table already exists
        $table_name = db_prefix() . 'dietic_ratings';
        $data['table_exists'] = $this->db->table_exists($table_name);

        if ($data['table_exists']) {
            $data['message'] = 'La table existe déjà dans votre base de données.';
            $data['message_type'] = 'warning';
        } else {
            // If POST request, perform installation
            if ($this->input->post('confirm_install')) {
                $result = $this->perform_installation();
                $data['installation_result'] = $result;
                $data['table_exists'] = $this->db->table_exists($table_name);
            }
        }

        $this->load->view('admin/dietitians/install', $data);
    }

    /**
     * Perform the actual installation
     */
    private function perform_installation()
    {
        $result = [
            'success' => false,
            'messages' => [],
            'errors' => []
        ];

        $table_name = db_prefix() . 'dietic_ratings';

        try {
            // Create table
            $sql = "CREATE TABLE IF NOT EXISTS `{$table_name}` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `patient_id` int(11) NOT NULL COMMENT 'Reference to dietic_patients.id',
                `dietitian_id` int(11) NOT NULL COMMENT 'Reference to staff.staffid',
                `overall_rating` decimal(2,1) NOT NULL DEFAULT 0.0 COMMENT 'Overall rating 0.0 to 5.0',
                `professionalism_rating` int(1) DEFAULT NULL COMMENT 'Rating 1-5',
                `listening_rating` int(1) DEFAULT NULL COMMENT 'Rating 1-5',
                `advice_rating` int(1) DEFAULT NULL COMMENT 'Rating 1-5',
                `results_rating` int(1) DEFAULT NULL COMMENT 'Rating 1-5',
                `availability_rating` int(1) DEFAULT NULL COMMENT 'Rating 1-5',
                `comment` text DEFAULT NULL COMMENT 'Written review/comment',
                `is_public` tinyint(1) DEFAULT 1,
                `is_verified` tinyint(1) DEFAULT 1,
                `created_at` datetime NOT NULL,
                `updated_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `unique_patient_dietitian` (`patient_id`, `dietitian_id`),
                KEY `idx_dietitian` (`dietitian_id`),
                KEY `idx_patient` (`patient_id`),
                KEY `idx_overall_rating` (`overall_rating`)
            ) ENGINE=InnoDB DEFAULT CHARSET=" . $this->db->char_set . " COLLATE=" . $this->db->dbcollat;

            if ($this->db->query($sql)) {
                $result['messages'][] = 'Table créée avec succès';
            } else {
                $result['errors'][] = 'Erreur lors de la création de la table';
                return $result;
            }

            // Add foreign key for patient_id
            try {
                $fk_patient = "ALTER TABLE `{$table_name}`
                    ADD CONSTRAINT `fk_diet_ratings_patient`
                    FOREIGN KEY (`patient_id`) REFERENCES `" . db_prefix() . "dietic_patients`(`id`) ON DELETE CASCADE";
                $this->db->query($fk_patient);
                $result['messages'][] = 'Clé étrangère patient ajoutée';
            } catch (Exception $e) {
                if (strpos($e->getMessage(), 'Duplicate') === false) {
                    $result['errors'][] = 'Clé étrangère patient: ' . $e->getMessage();
                }
            }

            // Add foreign key for dietitian_id (staff)
            try {
                $fk_staff = "ALTER TABLE `{$table_name}`
                    ADD CONSTRAINT `fk_diet_ratings_staff`
                    FOREIGN KEY (`dietitian_id`) REFERENCES `" . db_prefix() . "staff`(`staffid`) ON DELETE CASCADE";
                $this->db->query($fk_staff);
                $result['messages'][] = 'Clé étrangère diététicien ajoutée';
            } catch (Exception $e) {
                if (strpos($e->getMessage(), 'Duplicate') === false) {
                    $result['errors'][] = 'Clé étrangère diététicien: ' . $e->getMessage();
                }
            }

            $result['success'] = true;

        } catch (Exception $e) {
            $result['errors'][] = 'Erreur: ' . $e->getMessage();
        }

        return $result;
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
