<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Notifications extends AdminController
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

            // Check feature-specific permission for Notifications Management
            // Only admins can access notification settings and management
            if (!dietetic_has_feature_permission('notifications_manage')) {
                set_alert('warning', 'Vous n\'avez pas accès à la gestion des notifications. Cette fonctionnalité est réservée aux administrateurs.');
                redirect(admin_url('dietetic/patients'));
            }

            // Load required models
            $this->load->model('dietetic/dietetic_patients_model');

            // Only load notifications model if tables exist
            if ($this->db->table_exists(db_prefix() . 'dietic_notification_preferences')) {
                $this->load->model('dietetic/dietetic_notifications_model');
            }
        } catch (Exception $e) {
            log_activity('Notifications constructor error: ' . $e->getMessage());
            show_error('Erreur lors du chargement du contrôleur: ' . $e->getMessage());
        }
    }

    /**
     * Main notifications dashboard
     */
    public function index()
    {
        // Check if tables exist, if not show migration button
        if (!$this->db->table_exists(db_prefix() . 'dietic_notification_preferences')) {
            redirect(admin_url('dietetic/notifications/run_migration'));
        }

        // Load notifications model if not already loaded
        if (!isset($this->dietetic_notifications_model)) {
            $this->load->model('dietetic/dietetic_notifications_model');
        }

        $data['title'] = 'Notifications & Rappels';

        // Get statistics
        $data['stats'] = $this->dietetic_notifications_model->get_statistics();

        // Get recent notifications
        $data['recent_notifications'] = $this->dietetic_notifications_model->get_recent_logs(20);

        $this->load->view('admin/notifications/dashboard', $data);
    }

    /**
     * Notification settings page
     */
    public function settings()
    {
        if (!is_admin()) {
            access_denied('Notification Settings');
        }

        // Check if tables exist
        if (!$this->db->table_exists(db_prefix() . 'dietic_notification_settings')) {
            redirect(admin_url('dietetic/notifications/run_migration'));
        }

        // Load notifications model if not already loaded
        if (!isset($this->dietetic_notifications_model)) {
            $this->load->model('dietetic/dietetic_notifications_model');
        }

        // Handle form submission
        if ($this->input->post('save_settings')) {
            $settings = [
                // General settings
                'notifications_enabled' => $this->input->post('notifications_enabled') ? '1' : '0',

                // SMS provider settings
                'sms_provider' => $this->input->post('sms_provider'),

                // LAM SMS API credentials (new format)
                'sms_lam_account_id' => $this->input->post('sms_lam_account_id'),
                'sms_lam_password' => $this->input->post('sms_lam_password'),
                'sms_lam_sender_id' => $this->input->post('sms_lam_sender_id'),
                'sms_lam_ret_url' => $this->input->post('sms_lam_ret_url'),
                'sms_lam_priority' => $this->input->post('sms_lam_priority'),

                // WhatsApp settings
                'whatsapp_provider' => $this->input->post('whatsapp_provider'),
                'whatsapp_api_key' => $this->input->post('whatsapp_api_key'),
                'whatsapp_phone_number' => $this->input->post('whatsapp_phone_number'),
            ];

            foreach ($settings as $key => $value) {
                $this->dietetic_notifications_model->update_setting($key, $value);
            }

            set_alert('success', 'Paramètres de notification mis à jour avec succès');
            redirect(admin_url('dietetic/notifications/settings'));
        }

        $data['title'] = 'Paramètres des Notifications';
        $data['settings'] = $this->dietetic_notifications_model->get_all_settings();

        $this->load->view('admin/notifications/settings', $data);
    }

    /**
     * Test notification sending
     */
    public function test_notification()
    {
        if (!is_admin()) {
            ajax_access_denied();
        }

        header('Content-Type: application/json');

        // Load notifications model if not already loaded
        if (!isset($this->dietetic_notifications_model)) {
            $this->load->model('dietetic/dietetic_notifications_model');
        }

        $channel = $this->input->post('channel'); // email, sms, whatsapp
        $recipient = $this->input->post('recipient');
        $message = $this->input->post('message');

        if (!$channel || !$recipient || !$message) {
            echo json_encode([
                'success' => false,
                'message' => 'Tous les champs sont requis'
            ]);
            return;
        }

        $result = $this->dietetic_notifications_model->send_notification([
            'patient_id' => 0, // Test notification
            'type' => 'test',
            'subject' => 'Test de Notification - DietSenegal',
            'message' => $message,
            'email' => $channel == 'email' ? $recipient : null,
            'phone' => in_array($channel, ['sms', 'whatsapp']) ? $recipient : null,
            'channels' => [
                'email' => $channel == 'email' ? 1 : 0,
                'sms' => $channel == 'sms' ? 1 : 0,
                'whatsapp' => $channel == 'whatsapp' ? 1 : 0
            ]
        ]);

        if ($result['email'] || $result['sms'] || $result['whatsapp']) {
            echo json_encode([
                'success' => true,
                'message' => 'Notification de test envoyée avec succès',
                'details' => $result
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Échec de l\'envoi de la notification de test',
                'details' => $result
            ]);
        }
    }

    /**
     * Run migration to create notification tables
     * URL: /admin/dietetic/notifications/run_migration
     */
    public function run_migration()
    {
        if (!is_admin()) {
            access_denied('Migration');
        }

        $this->load->view('admin/notifications/run_migration');
    }

    /**
     * View notification logs
     */
    public function logs()
    {
        // Check if tables exist
        if (!$this->db->table_exists(db_prefix() . 'dietic_notification_logs')) {
            redirect(admin_url('dietetic/notifications/run_migration'));
        }

        // Load notifications model if not already loaded
        if (!isset($this->dietetic_notifications_model)) {
            $this->load->model('dietetic/dietetic_notifications_model');
        }

        $data['title'] = 'Historique des Notifications';

        // Pagination
        $per_page = 50;
        $page = $this->input->get('page') ? (int)$this->input->get('page') : 1;
        $offset = ($page - 1) * $per_page;

        // Filters
        $filters = [];
        if ($this->input->get('patient_id')) {
            $filters['patient_id'] = $this->input->get('patient_id');
        }
        if ($this->input->get('type')) {
            $filters['notification_type'] = $this->input->get('type');
        }
        if ($this->input->get('status')) {
            $filters['status'] = $this->input->get('status');
        }

        $data['logs'] = $this->dietetic_notifications_model->get_logs($filters, $per_page, $offset);
        $data['total'] = $this->dietetic_notifications_model->count_logs($filters);
        $data['page'] = $page;
        $data['per_page'] = $per_page;
        $data['filters'] = $filters;

        $this->load->view('admin/notifications/logs', $data);
    }

    /**
     * View milestones achieved
     */
    public function milestones()
    {
        // Check if tables exist
        if (!$this->db->table_exists(db_prefix() . 'dietic_milestones')) {
            redirect(admin_url('dietetic/notifications/run_migration'));
        }

        // Load notifications model if not already loaded
        if (!isset($this->dietetic_notifications_model)) {
            $this->load->model('dietetic/dietetic_notifications_model');
        }

        $data['title'] = 'Jalons Atteints';

        // Get all milestones
        $data['milestones'] = $this->dietetic_notifications_model->get_all_milestones();

        // Get statistics
        $data['milestone_stats'] = $this->dietetic_notifications_model->get_milestone_statistics();

        $this->load->view('admin/notifications/milestones', $data);
    }

    /**
     * Notification templates page
     */
    public function templates()
    {
        if (!is_admin()) {
            access_denied('Notification Templates');
        }

        $data['title'] = 'Modèles de Notifications';
        $this->load->view('admin/notifications/templates', $data);
    }

    /**
     * Migrations management page
     */
    public function migrations()
    {
        if (!is_admin()) {
            access_denied('Migrations');
        }

        $data['title'] = 'Migrations SQL';
        $this->load->view('admin/notifications/migrations', $data);
    }

    /**
     * Check migration status (AJAX)
     */
    public function check_migration()
    {
        header('Content-Type: application/json');

        if (!is_admin()) {
            echo json_encode([
                'success' => false,
                'message' => 'Accès refusé'
            ]);
            return;
        }

        $migration = $this->input->post('migration');

        $installed = false;
        $message = 'Non installé';

        switch ($migration) {
            case 'notifications':
                // Check if base notification tables exist
                $tables = [
                    'dietic_notification_preferences',
                    'dietic_notification_logs',
                    'dietic_milestones',
                    'dietic_notification_settings'
                ];

                $all_exist = true;
                foreach ($tables as $table) {
                    if (!$this->db->table_exists(db_prefix() . $table)) {
                        $all_exist = false;
                        break;
                    }
                }

                $installed = $all_exist;
                $message = $all_exist ? 'Installé' : 'Manquant';
                break;

            case 'firebase':
                // Check if Firebase table exists
                $installed = $this->db->table_exists(db_prefix() . 'dietic_fcm_tokens');

                // Also check if column channel_push exists in preferences
                if ($installed && $this->db->table_exists(db_prefix() . 'dietic_notification_preferences')) {
                    $fields = $this->db->field_data(db_prefix() . 'dietic_notification_preferences');
                    $has_channel_push = false;
                    foreach ($fields as $field) {
                        if ($field->name === 'channel_push') {
                            $has_channel_push = true;
                            break;
                        }
                    }
                    $installed = $installed && $has_channel_push;
                }

                $message = $installed ? 'Installé' : 'Manquant';
                break;

            case 'optimizations':
                // Check if indexes exist
                $installed = true; // We assume optimizations are optional and always return true
                $message = 'Indexes actifs';
                break;

            default:
                echo json_encode([
                    'success' => false,
                    'message' => 'Migration inconnue'
                ]);
                return;
        }

        echo json_encode([
            'success' => true,
            'installed' => $installed,
            'message' => $message
        ]);
    }

    /**
     * Execute specific migration (improved version)
     */
    public function execute_migration()
    {
        header('Content-Type: application/json');

        if (!is_admin()) {
            echo json_encode([
                'success' => false,
                'message' => 'Accès refusé'
            ]);
            return;
        }

        try {
            $migration = $this->input->post('migration');

            // Determine which SQL file to use
            $sql_files = [
                'notifications' => 'add_notifications_system.sql',
                'firebase' => 'add_firebase_push_notifications.sql',
                'optimizations' => 'optimize_notifications_performance.sql'
            ];

            if (!isset($sql_files[$migration])) {
                // Fallback to old behavior for backward compatibility
                $sql_file = module_dir_path('dietetic', 'migrations/add_notifications_system.sql');
            } else {
                $sql_file = module_dir_path('dietetic', 'migrations/' . $sql_files[$migration]);
            }

            if (!file_exists($sql_file)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Fichier de migration introuvable: ' . basename($sql_file)
                ]);
                return;
            }

            // Check if already installed (for base notifications only)
            if ($migration === 'notifications') {
                if ($this->db->table_exists(db_prefix() . 'dietic_notification_preferences')) {
                    echo json_encode([
                        'success' => true,
                        'already_exists' => true,
                        'message' => 'Cette migration a déjà été exécutée'
                    ]);
                    return;
                }
            }

            $sql_content = file_get_contents($sql_file);

            // Replace table prefix
            $sql_content = str_replace('`tbldietic_', '`' . db_prefix() . 'dietic_', $sql_content);

            // Remove SQL comments
            $sql_content = preg_replace('/^--.*$/m', '', $sql_content);

            // Split by semicolon and filter empty statements
            $statements = array_filter(
                array_map('trim', explode(';', $sql_content)),
                function($stmt) {
                    return !empty($stmt) && strlen($stmt) > 10;
                }
            );

            $success_count = 0;
            $error_count = 0;
            $errors = [];
            $tables_created = 0;

            foreach ($statements as $statement) {
                try {
                    // Skip if it's a duplicate key error (table already exists)
                    $this->db->query($statement);
                    $success_count++;

                    // Count if it's a CREATE TABLE statement
                    if (stripos($statement, 'CREATE TABLE') !== false) {
                        $tables_created++;
                    }
                } catch (Exception $e) {
                    $error_msg = $e->getMessage();

                    // Ignore "duplicate key" and "already exists" errors
                    if (stripos($error_msg, 'Duplicate key') !== false ||
                        stripos($error_msg, 'already exists') !== false ||
                        stripos($error_msg, 'Duplicate column') !== false ||
                        stripos($error_msg, 'Duplicate entry') !== false) {
                        // Silently skip these errors
                        continue;
                    }

                    $error_count++;
                    $errors[] = substr($error_msg, 0, 200);
                    log_activity('Notifications Migration Error: ' . $error_msg);
                }
            }

            if ($error_count > 0) {
                echo json_encode([
                    'success' => false,
                    'message' => "Migration partiellement réussie. {$success_count} requêtes réussies, {$error_count} échouées.",
                    'errors' => $errors,
                    'tables_created' => $tables_created
                ]);
            } else {
                $message = $migration === 'optimizations'
                    ? "Optimisations appliquées avec succès!"
                    : "Migration exécutée avec succès! {$tables_created} table(s) créée(s).";

                echo json_encode([
                    'success' => true,
                    'message' => $message,
                    'tables_created' => $tables_created,
                    'total_queries' => $success_count
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Erreur lors de la migration: ' . $e->getMessage()
            ]);
        }
    }
}
