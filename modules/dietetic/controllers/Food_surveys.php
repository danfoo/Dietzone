<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Food_surveys extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        try {
            // Load helper first
            $this->load->helper('dietetic/dietetic');

            // Check basic permissions
            if (!dietetic_has_permission('view')) {
                access_denied('dietetic');
            }

            // Check feature-specific permission for Food Surveys
            if (!dietetic_has_feature_permission('food_surveys')) {
                set_alert('warning', 'Vous n\'avez pas accès au module Enquêtes Alimentaires. Contactez votre administrateur.');
                redirect(admin_url('dietetic/patients'));
            }

            // Always load these models (they come from the main dietetic module)
            $this->load->model('dietetic/dietetic_patients_model');
            $this->load->model('dietetic/dietetic_programs_model');
            $this->load->model('staff_model');

            // Only load food surveys model if tables exist
            if ($this->db->table_exists(db_prefix() . 'dietic_food_surveys')) {
                $this->load->model('dietetic/dietetic_food_surveys_model');
            }
        } catch (Exception $e) {
            log_activity('Food_surveys constructor error: ' . $e->getMessage());
            show_error('Erreur lors du chargement du contrôleur: ' . $e->getMessage());
        }
    }

    /**
     * List all food surveys
     */
    public function index()
    {
        // Check if tables exist, if not show install button
        if (!$this->db->table_exists(db_prefix() . 'dietic_food_surveys')) {
            $this->install_tables();
            return;
        }

        $data['title'] = 'Enquêtes Alimentaires';
        $data['surveys'] = $this->dietetic_food_surveys_model->get_all();

        // Calculate completion percentages
        foreach ($data['surveys'] as &$survey) {
            $survey->completion_percentage = $this->dietetic_food_surveys_model->get_completion_percentage($survey->id);
        }

        $this->load->view('admin/food_surveys/list', $data);
    }

    /**
     * Install Food Surveys tables
     */
    public function install_tables()
    {
        if (!dietetic_has_permission('create')) {
            access_denied('dietetic');
        }

        // If form submitted, do the installation
        if ($this->input->post('do_install')) {
            $sql_file = __DIR__ . '/../install/food_surveys.sql';

            if (!file_exists($sql_file)) {
                set_alert('danger', 'Fichier SQL introuvable');
                redirect(admin_url('dietetic/food_surveys'));
            }

            $sql_content = file_get_contents($sql_file);

            // Replace table prefix
            $sql_content = str_replace('`tbldietic_', '`' . db_prefix() . 'dietic_', $sql_content);

            // Split by semicolon
            $statements = array_filter(array_map('trim', explode(';', $sql_content)));

            $success_count = 0;
            $error_count = 0;

            foreach ($statements as $statement) {
                if (!empty($statement) && !preg_match('/^--/', $statement)) {
                    try {
                        $this->db->query($statement);
                        $success_count++;
                    } catch (Exception $e) {
                        $error_count++;
                        log_activity('Food Surveys Install Error: ' . substr($e->getMessage(), 0, 200));
                    }
                }
            }

            // Create uploads directory
            $upload_path = FCPATH . 'uploads/dietetic/food_surveys/';
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0755, true);
            }

            if ($success_count > 0) {
                set_alert('success', 'Tables Food Surveys installées avec succès! (' . $success_count . ' opérations)');
                log_activity('Dietetic Module: Food Surveys tables installed');
            } else {
                set_alert('warning', 'Installation terminée avec quelques avertissements');
            }

            redirect(admin_url('dietetic/food_surveys'));
        }

        // Show installation page
        $data['title'] = 'Installation Food Surveys';
        $this->load->view('admin/food_surveys/install', $data);
    }

    /**
     * View food survey detail
     *
     * @param int $id
     */
    public function view($id)
    {
        // Check if tables exist, if not redirect to install
        if (!$this->db->table_exists(db_prefix() . 'dietic_food_surveys')) {
            redirect(admin_url('dietetic/food_surveys'));
        }

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
        // Debug: Log entry to this method
        log_activity('DEBUG: Entering create() method');

        // Check if tables exist, if not redirect to install
        if (!$this->db->table_exists(db_prefix() . 'dietic_food_surveys')) {
            log_activity('DEBUG: Tables do not exist, redirecting to install');
            redirect(admin_url('dietetic/food_surveys'));
            exit; // Force exit after redirect
        }

        log_activity('DEBUG: Tables exist, proceeding with create');

        if (!dietetic_has_permission('create')) {
            access_denied('dietetic');
        }

        if ($this->input->post()) {
            $data = $this->input->post();

            // Set dietitian
            if (!isset($data['dietitian_id'])) {
                $data['dietitian_id'] = get_staff_user_id();
            }

            try {
                $survey_id = $this->dietetic_food_surveys_model->add($data);

                if ($survey_id) {
                    set_alert('success', 'Enquête alimentaire créée avec succès');
                    redirect(admin_url('dietetic/food_surveys/view/' . $survey_id));
                } else {
                    set_alert('danger', 'Erreur lors de la création de l\'enquête alimentaire');
                }
            } catch (Exception $e) {
                log_activity('ERROR in create: ' . $e->getMessage());
                set_alert('danger', 'Erreur: ' . $e->getMessage());
            }
        }

        $data['title'] = 'Nouvelle Enquête Alimentaire';

        // Get pre-selected patient from URL if provided
        $data['selected_patient_id'] = $this->input->get('patient_id');

        log_activity('DEBUG: About to load patients model');
        try {
            $data['patients'] = $this->dietetic_patients_model->get_all();
            log_activity('DEBUG: Patients loaded: ' . count($data['patients']));
        } catch (Exception $e) {
            log_activity('ERROR loading patients: ' . $e->getMessage());
            show_error('Erreur lors du chargement des patients: ' . $e->getMessage());
        }

        log_activity('DEBUG: About to load programs model');
        try {
            $data['programs'] = $this->dietetic_programs_model->get_all();
            log_activity('DEBUG: Programs loaded: ' . count($data['programs']));
        } catch (Exception $e) {
            log_activity('ERROR loading programs: ' . $e->getMessage());
            show_error('Erreur lors du chargement des programmes: ' . $e->getMessage());
        }

        log_activity('DEBUG: About to load staff model');
        try {
            $data['staff'] = $this->staff_model->get();
            log_activity('DEBUG: Staff loaded: ' . count($data['staff']));
        } catch (Exception $e) {
            log_activity('ERROR loading staff: ' . $e->getMessage());
            show_error('Erreur lors du chargement du staff: ' . $e->getMessage());
        }

        log_activity('DEBUG: About to load view form.php');
        $this->load->view('admin/food_surveys/form_simple', $data);
        log_activity('DEBUG: View loaded successfully');
    }

    /**
     * Edit food survey
     *
     * @param int $id
     */
    public function edit($id)
    {
        // Check if tables exist, if not redirect to install
        if (!$this->db->table_exists(db_prefix() . 'dietic_food_surveys')) {
            redirect(admin_url('dietetic/food_surveys'));
        }

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

        $this->load->view('admin/food_surveys/form_simple', $data);
    }

    /**
     * Delete food survey
     *
     * @param int $id
     */
    public function delete($id)
    {
        // Check if tables exist
        if (!$this->db->table_exists(db_prefix() . 'dietic_food_surveys')) {
            echo json_encode(['success' => false, 'message' => 'Tables non installées']);
            return;
        }

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
        // Check if tables exist, if not redirect to install
        if (!$this->db->table_exists(db_prefix() . 'dietic_food_surveys')) {
            redirect(admin_url('dietetic/food_surveys'));
        }

        $data['entry'] = $this->dietetic_food_surveys_model->get_entry($entry_id);

        if (!$data['entry']) {
            show_404();
        }

        $data['survey'] = $this->dietetic_food_surveys_model->get($data['entry']->survey_id);
        $data['beverages'] = $this->dietetic_food_surveys_model->get_beverages($entry_id);

        // Get audio notes for this entry
        $data['audio_notes'] = [];
        if ($this->db->table_exists(db_prefix() . 'dietic_food_survey_audio_notes')) {
            $this->db->where('entry_id', $entry_id);
            $this->db->order_by('created_at', 'ASC');
            $audio_results = $this->db->get(db_prefix() . 'dietic_food_survey_audio_notes')->result();

            // Group by meal type
            foreach ($audio_results as $audio) {
                $data['audio_notes'][$audio->meal_type][] = $audio;
            }
        }

        // Get recommendations grouped by meal type
        $recommendations_by_meal = $this->dietetic_food_surveys_model->get_recommendations_by_meal($entry_id);

        // Get comments for each recommendation in each meal
        foreach ($recommendations_by_meal as $meal_type => &$recommendations) {
            foreach ($recommendations as &$recommendation) {
                $recommendation->comments = $this->dietetic_food_surveys_model->get_comments($recommendation->id);
            }
        }

        $data['recommendations_by_meal'] = $recommendations_by_meal;

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
                'meal_type' => $this->input->post('meal_type') ?: 'global',
                'dietitian_id' => get_staff_user_id(),
                'recommendation_text' => $this->input->post('recommendation_text')
            ];

            $recommendation_id = $this->dietetic_food_surveys_model->add_recommendation($data);

            if ($recommendation_id) {
                // Send notification to patient
                try {
                    if ($this->db->table_exists(db_prefix() . 'dietic_notification_preferences')) {
                        // Get entry to find patient
                        $entry = $this->dietetic_food_surveys_model->get_entry($data['entry_id']);
                        if ($entry) {
                            $survey = $this->dietetic_food_surveys_model->get($entry->survey_id);
                            if ($survey && $survey->patient_id) {
                                $this->load->model('dietetic/dietetic_notifications_model');
                                $this->load->model('staff_model');

                                // Get dietitian info
                                $dietitian = $this->staff_model->get(get_staff_user_id());
                                $dietitian_name = $dietitian ? ($dietitian->firstname . ' ' . $dietitian->lastname) : 'Votre diététicien';

                                // Send notification
                                $this->dietetic_notifications_model->notify_patient_recommendation(
                                    $survey->patient_id,
                                    $dietitian_name,
                                    $data['meal_type']
                                );
                            }
                        }
                    }
                } catch (Exception $e) {
                    log_activity('Recommendation notification error: ' . $e->getMessage());
                }

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

    /**
     * Upload meal photo
     * Used by patients to upload photos of their meals
     */
    public function upload_photo()
    {
        header('Content-Type: application/json');

        // Check if file was uploaded
        if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode([
                'success' => false,
                'message' => 'Aucun fichier téléchargé ou erreur lors du téléchargement'
            ]);
            return;
        }

        $file = $_FILES['photo'];

        // Validate file type
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime_type, $allowed_types)) {
            echo json_encode([
                'success' => false,
                'message' => 'Type de fichier non autorisé. Seules les images (JPEG, PNG, GIF) sont acceptées.'
            ]);
            return;
        }

        // Validate file size (max 5MB)
        $max_size = 5 * 1024 * 1024; // 5MB in bytes
        if ($file['size'] > $max_size) {
            echo json_encode([
                'success' => false,
                'message' => 'Le fichier est trop volumineux. Taille maximale: 5MB.'
            ]);
            return;
        }

        // Create upload directory if it doesn't exist
        $upload_path = FCPATH . 'uploads/dietetic/food_surveys/';
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0755, true);
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'meal_' . uniqid() . '_' . time() . '.' . $extension;
        $destination = $upload_path . $filename;

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            // Optionally create thumbnail (for faster loading)
            $this->create_thumbnail($destination, $upload_path . 'thumb_' . $filename);

            echo json_encode([
                'success' => true,
                'message' => 'Photo téléchargée avec succès',
                'filename' => $filename,
                'url' => base_url('uploads/dietetic/food_surveys/' . $filename),
                'thumbnail_url' => base_url('uploads/dietetic/food_surveys/thumb_' . $filename)
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Erreur lors du déplacement du fichier'
            ]);
        }
    }

    /**
     * Delete uploaded photo
     */
    public function delete_photo()
    {
        header('Content-Type: application/json');

        $filename = $this->input->post('filename');

        if (!$filename) {
            echo json_encode([
                'success' => false,
                'message' => 'Nom de fichier manquant'
            ]);
            return;
        }

        // Security: prevent directory traversal
        $filename = basename($filename);

        $upload_path = FCPATH . 'uploads/dietetic/food_surveys/';
        $file_path = $upload_path . $filename;
        $thumb_path = $upload_path . 'thumb_' . $filename;

        $success = false;

        // Delete main file
        if (file_exists($file_path)) {
            $success = unlink($file_path);
        }

        // Delete thumbnail
        if (file_exists($thumb_path)) {
            unlink($thumb_path);
        }

        if ($success) {
            echo json_encode([
                'success' => true,
                'message' => 'Photo supprimée avec succès'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Erreur lors de la suppression de la photo'
            ]);
        }
    }

    /**
     * Create thumbnail from image
     *
     * @param string $source Source image path
     * @param string $destination Destination thumbnail path
     * @param int $thumb_width Thumbnail width (default 300px)
     */
    private function create_thumbnail($source, $destination, $thumb_width = 300)
    {
        // Get image info
        $info = getimagesize($source);
        if (!$info) {
            return false;
        }

        $width = $info[0];
        $height = $info[1];
        $mime = $info['mime'];

        // Calculate thumbnail height maintaining aspect ratio
        $thumb_height = floor($height * ($thumb_width / $width));

        // Create source image resource
        switch ($mime) {
            case 'image/jpeg':
            case 'image/jpg':
                $source_image = imagecreatefromjpeg($source);
                break;
            case 'image/png':
                $source_image = imagecreatefrompng($source);
                break;
            case 'image/gif':
                $source_image = imagecreatefromgif($source);
                break;
            default:
                return false;
        }

        // Create thumbnail image
        $thumb_image = imagecreatetruecolor($thumb_width, $thumb_height);

        // Preserve transparency for PNG and GIF
        if ($mime == 'image/png' || $mime == 'image/gif') {
            imagealphablending($thumb_image, false);
            imagesavealpha($thumb_image, true);
            $transparent = imagecolorallocatealpha($thumb_image, 255, 255, 255, 127);
            imagefilledrectangle($thumb_image, 0, 0, $thumb_width, $thumb_height, $transparent);
        }

        // Resize image
        imagecopyresampled($thumb_image, $source_image, 0, 0, 0, 0, $thumb_width, $thumb_height, $width, $height);

        // Save thumbnail
        $result = false;
        switch ($mime) {
            case 'image/jpeg':
            case 'image/jpg':
                $result = imagejpeg($thumb_image, $destination, 85);
                break;
            case 'image/png':
                $result = imagepng($thumb_image, $destination, 8);
                break;
            case 'image/gif':
                $result = imagegif($thumb_image, $destination);
                break;
        }

        // Free memory
        imagedestroy($source_image);
        imagedestroy($thumb_image);

        return $result;
    }

    /**
     * Run migration to add meal_type column to recommendations
     * URL: /admin/dietetic/food_surveys/run_migration
     */
    public function run_migration()
    {
        if (!is_admin()) {
            access_denied('Migration');
        }

        $this->load->view('admin/food_surveys/run_migration');
    }

    /**
     * Execute migration via AJAX
     */
    public function execute_migration()
    {
        if (!is_admin()) {
            ajax_access_denied();
        }

        header('Content-Type: application/json');

        try {
            // Check if column already exists
            $columns = $this->db->list_fields('tbldietic_food_survey_recommendations');

            if (in_array('meal_type', $columns)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'La colonne meal_type existe déjà dans la table.',
                    'already_exists' => true
                ]);
                return;
            }

            // Add meal_type column with snack option
            $sql = "ALTER TABLE `" . db_prefix() . "dietic_food_survey_recommendations`
                    ADD COLUMN `meal_type` ENUM('breakfast', 'lunch', 'dinner', 'snack', 'global') DEFAULT 'global'
                    AFTER `entry_id`,
                    ADD INDEX `idx_meal_type` (`meal_type`)";

            $this->db->query($sql);

            echo json_encode([
                'success' => true,
                'message' => 'Migration exécutée avec succès ! La colonne meal_type a été ajoutée.'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Erreur lors de la migration : ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Add snack (collation) to food surveys
     * Adds snack_photo, snack_time, snack_notes columns
     */
    public function add_snack()
    {
        if (!is_admin()) {
            access_denied('dietetic');
        }

        require_once(__DIR__ . '/../add_snack_to_food_surveys.php');
        $migration = new Add_snack_migration();

        // Check if migration is needed
        if (!$migration->is_needed()) {
            set_alert('info', 'Les colonnes collation existent déjà');
            redirect(admin_url('dietetic/food_surveys'));
            return;
        }

        // If form submitted, run migration
        if ($this->input->post('do_migrate')) {
            $result = $migration->run();

            if ($result['success']) {
                set_alert('success', $result['message']);
            } else {
                set_alert('danger', $result['message']);
            }

            redirect(admin_url('dietetic/food_surveys'));
            return;
        }

        // Show confirmation page
        $data['title'] = 'Ajouter Collation aux Enquêtes Alimentaires';
        $this->load->view('admin/food_surveys/add_snack_confirmation', $data);
    }

    /**
     * Upload audio note for recommendation (Admin/Dietitian)
     */
    public function upload_recommendation_audio()
    {
        // Set JSON header first
        header('Content-Type: application/json');

        // Disable error display to prevent HTML output
        @ini_set('display_errors', 0);

        if (!is_staff_logged_in()) {
            echo json_encode(['success' => false, 'message' => 'Non authentifié']);
            return;
        }

        try {
            $recommendation_id = $this->input->post('recommendation_id');
            $duration = $this->input->post('duration');

            if (!$recommendation_id) {
                echo json_encode(['success' => false, 'message' => 'ID de recommandation manquant']);
                return;
            }

            // Verify recommendation exists (simple query to avoid model issues)
            $this->db->where('id', $recommendation_id);
            $recommendation = $this->db->get(db_prefix() . 'dietic_food_survey_recommendations')->row();

            if (!$recommendation) {
                echo json_encode(['success' => false, 'message' => 'Recommandation non trouvée']);
                return;
            }

            // Check if file was uploaded
            if (!isset($_FILES['audio']) || $_FILES['audio']['error'] !== UPLOAD_ERR_OK) {
                echo json_encode(['success' => false, 'message' => 'Aucun fichier audio reçu']);
                return;
            }

            $file = $_FILES['audio'];

            // Validate file type by extension
            $allowed_extensions = ['webm', 'mp3', 'wav', 'ogg', 'm4a', 'mp4', 'mpeg'];
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if (!in_array($extension, $allowed_extensions)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Extension non autorisée'
                ]);
                return;
            }

            // Validate file size (max 10MB)
            $max_size = 10 * 1024 * 1024;
            if ($file['size'] > $max_size) {
                echo json_encode(['success' => false, 'message' => 'Fichier trop volumineux (max 10MB)']);
                return;
            }

            // Create upload directory if it doesn't exist
            $upload_path = FCPATH . 'uploads/dietetic/recommendation_audio/';
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0755, true);
            }

            // Generate unique filename
            $filename = 'rec_audio_' . uniqid() . '_' . time() . '.' . $extension;
            $destination = $upload_path . $filename;

            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $destination)) {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'enregistrement du fichier']);
                return;
            }

            @chmod($destination, 0644);

            // Save to database
            $audio_data = [
                'recommendation_id' => $recommendation_id,
                'sender_type' => 'dietitian',
                'sender_id' => get_staff_user_id(),
                'audio_file' => $filename,
                'duration' => $duration,
                'file_size' => $file['size'],
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->db->insert(db_prefix() . 'dietic_recommendation_audio_notes', $audio_data);

            echo json_encode([
                'success' => true,
                'message' => 'Note vocale enregistrée',
                'audio_id' => $this->db->insert_id()
            ]);

        } catch (Exception $e) {
            log_activity('Error uploading recommendation audio: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Erreur serveur: ' . $e->getMessage()]);
        }
    }

    /**
     * Get audio notes for a recommendation
     */
    public function get_recommendation_audios($recommendation_id)
    {
        header('Content-Type: application/json');
        @ini_set('display_errors', 0);

        try {
            if (!$this->db->table_exists(db_prefix() . 'dietic_recommendation_audio_notes')) {
                echo json_encode(['success' => true, 'audios' => []]);
                return;
            }

            $this->db->where('recommendation_id', $recommendation_id);
            $this->db->order_by('created_at', 'ASC');
            $audios = $this->db->get(db_prefix() . 'dietic_recommendation_audio_notes')->result_array();

            // Add sender names
            foreach ($audios as &$audio) {
                if ($audio['sender_type'] === 'dietitian') {
                    $staff = $this->db->get_where('staff', ['staffid' => $audio['sender_id']])->row();
                    $audio['sender_name'] = $staff ? ($staff->firstname . ' ' . $staff->lastname) : 'Diététicien';
                } else {
                    $contact = $this->db->get_where(db_prefix() . 'contacts', ['id' => $audio['sender_id']])->row();
                    $audio['sender_name'] = $contact ? ($contact->firstname . ' ' . $contact->lastname) : 'Patient';
                }
            }

            echo json_encode(['success' => true, 'audios' => $audios]);

        } catch (Exception $e) {
            log_activity('Error getting recommendation audios: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Erreur serveur']);
        }
    }

    /**
     * Delete recommendation audio note
     */
    public function delete_recommendation_audio()
    {
        header('Content-Type: application/json');
        @ini_set('display_errors', 0);

        try {
            $audio_id = $this->input->post('audio_id');

            if (!$audio_id) {
                echo json_encode(['success' => false, 'message' => 'ID audio manquant']);
                return;
            }

            // Get audio info
            $audio = $this->db->get_where(db_prefix() . 'dietic_recommendation_audio_notes', ['id' => $audio_id])->row();

            if (!$audio) {
                echo json_encode(['success' => false, 'message' => 'Audio non trouvé']);
                return;
            }

            // Verify ownership (staff can delete their own audios)
            if (is_staff_logged_in() && $audio->sender_type === 'dietitian' && $audio->sender_id == get_staff_user_id()) {
                // Delete file
                $file_path = FCPATH . 'uploads/dietetic/recommendation_audio/' . $audio->audio_file;
                if (file_exists($file_path)) {
                    unlink($file_path);
                }

                // Delete from database
                $this->db->delete(db_prefix() . 'dietic_recommendation_audio_notes', ['id' => $audio_id]);

                echo json_encode(['success' => true, 'message' => 'Note vocale supprimée']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Non autorisé']);
            }

        } catch (Exception $e) {
            log_activity('Error deleting recommendation audio: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Erreur serveur']);
        }
    }
}
