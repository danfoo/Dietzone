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

        // Handle form submission (check if it's a POST request)
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            // DEBUG: Log that we entered the POST block
            log_activity('🔍 [DEBUG] POST detected - form submitted');

            // Log all POST data (without passwords)
            $all_post = $this->input->post();
            $safe_post = $all_post;
            if (isset($safe_post['sms_lam_password'])) {
                $safe_post['sms_lam_password'] = '***HIDDEN***';
            }
            log_activity('🔍 [DEBUG] All POST data: ' . json_encode($safe_post));

            // Verify CSRF token manually if there's an issue
            $csrf_token_name = $this->security->get_csrf_token_name();
            $csrf_hash = $this->input->post($csrf_token_name);

            log_activity('🔍 [DEBUG] CSRF token name: ' . $csrf_token_name);
            log_activity('🔍 [DEBUG] CSRF hash from POST: ' . ($csrf_hash ? 'EXISTS' : 'NULL'));
            log_activity('🔍 [DEBUG] CSRF hash from security: ' . $this->security->get_csrf_hash());

            if (!$csrf_hash || $csrf_hash !== $this->security->get_csrf_hash()) {
                log_activity('❌ [DEBUG] CSRF validation FAILED');
                set_alert('danger', 'Erreur de sécurité : jeton CSRF invalide. Veuillez réessayer.');
                redirect(admin_url('dietetic/notifications/settings'));
                return;
            }

            log_activity('✅ [DEBUG] CSRF validation PASSED');

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

            // Log for debugging (hide password)
            $safe_settings = $settings;
            if (isset($safe_settings['sms_lam_password'])) {
                $safe_settings['sms_lam_password'] = $settings['sms_lam_password'] ? '***SET***' : '***EMPTY***';
            }
            log_activity('🔍 [DEBUG] Settings to save: ' . json_encode($safe_settings));

            $success_count = 0;
            $error_count = 0;
            $skipped_count = 0;

            foreach ($settings as $key => $value) {
                try {
                    // Log each setting before saving
                    $display_value = ($key === 'sms_lam_password' && $value) ? '***SET***' : $value;
                    log_activity("🔍 [DEBUG] Processing setting: {$key} = " . var_export($display_value, true));

                    $result = $this->dietetic_notifications_model->update_setting($key, $value);

                    if ($result) {
                        // Check if it was actually saved or skipped
                        if ($value === null || $value === '') {
                            $skipped_count++;
                            log_activity("⚠️ [DEBUG] Setting {$key} SKIPPED (empty value)");
                        } else {
                            $success_count++;
                            log_activity("✅ [DEBUG] Setting {$key} SAVED successfully");
                        }
                    } else {
                        $error_count++;
                        log_activity("❌ [DEBUG] Setting {$key} FAILED to save");
                    }
                } catch (Exception $e) {
                    $error_count++;
                    log_activity('❌ [DEBUG] Exception for setting ' . $key . ': ' . $e->getMessage());
                }
            }

            log_activity("📊 [DEBUG] Final results: {$success_count} saved, {$skipped_count} skipped, {$error_count} failed");

            if ($error_count > 0) {
                set_alert('warning', "Paramètres partiellement enregistrés ({$success_count} réussis, {$error_count} échoués). Consultez les logs pour plus de détails.");
            } else {
                set_alert('success', "Paramètres de notification mis à jour avec succès ({$success_count} enregistrés, {$skipped_count} ignorés car vides)");
            }
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
            case 'lam_update':
                // Check if new LAM SMS settings exist
                $this->load->model('dietetic/dietetic_notifications_model');

                $new_settings_exist = true;
                $new_settings = ['sms_lam_account_id', 'sms_lam_password', 'sms_lam_ret_url', 'sms_lam_priority'];

                foreach ($new_settings as $setting) {
                    $value = $this->dietetic_notifications_model->get_setting($setting);
                    if ($value === null) {
                        $new_settings_exist = false;
                        break;
                    }
                }

                // Also check if old settings still exist (means update is needed)
                $old_settings_exist = false;
                $old_settings = ['lam_api_url', 'lam_api_key', 'sms_lam_api_key'];

                foreach ($old_settings as $setting) {
                    $this->db->where('setting_key', $setting);
                    if ($this->db->count_all_results(db_prefix() . 'dietic_notification_settings') > 0) {
                        $old_settings_exist = true;
                        break;
                    }
                }

                $installed = $new_settings_exist && !$old_settings_exist;
                $message = $installed ? 'Installé' : ($old_settings_exist ? 'Mise à jour requise' : 'À installer');
                break;

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
                'lam_update' => 'update_lam_sms_config.sql',
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

    /**
     * LAM SMS Configuration Cleanup Page
     * URL: /admin/dietetic/notifications/cleanup
     */
    public function cleanup()
    {
        if (!is_admin()) {
            access_denied('Cleanup');
        }

        // Load notifications model if not already loaded
        if (!isset($this->dietetic_notifications_model)) {
            $this->load->model('dietetic/dietetic_notifications_model');
        }

        $data['title'] = 'Nettoyage Configuration LAM SMS';

        // Check for old LAM keys
        $old_keys = ['lam_api_url', 'lam_api_key', 'lam_api_sender', 'sms_lam_api_key'];
        $data['old_keys_found'] = [];

        foreach ($old_keys as $key) {
            $this->db->where('setting_key', $key);
            $result = $this->db->get(db_prefix() . 'dietic_notification_settings')->row();
            if ($result) {
                $data['old_keys_found'][] = [
                    'key' => $key,
                    'value' => $result->setting_value
                ];
            }
        }

        // Check for new LAM keys
        $new_keys = ['sms_lam_account_id', 'sms_lam_password', 'sms_lam_sender_id', 'sms_lam_ret_url', 'sms_lam_priority'];
        $data['new_keys_status'] = [];

        foreach ($new_keys as $key) {
            $value = $this->dietetic_notifications_model->get_setting($key);
            $data['new_keys_status'][] = [
                'key' => $key,
                'exists' => $value !== null,
                'value' => $value
            ];
        }

        $this->load->view('admin/notifications/cleanup', $data);
    }

    /**
     * Execute cleanup of old LAM SMS keys (AJAX)
     */
    public function execute_cleanup()
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
            $table = db_prefix() . 'dietic_notification_settings';
            $old_keys = ['lam_api_url', 'lam_api_key', 'lam_api_sender', 'sms_lam_api_key'];

            // Delete old keys
            $deleted_count = 0;
            foreach ($old_keys as $key) {
                $this->db->where('setting_key', $key);
                if ($this->db->delete($table)) {
                    if ($this->db->affected_rows() > 0) {
                        $deleted_count++;
                    }
                }
            }

            // Load notifications model
            if (!isset($this->dietetic_notifications_model)) {
                $this->load->model('dietetic/dietetic_notifications_model');
            }

            // Ensure new keys exist with default values
            $new_keys_defaults = [
                'sms_lam_account_id' => '',
                'sms_lam_password' => '',
                'sms_lam_sender_id' => 'API_LAMSMS',
                'sms_lam_ret_url' => '',
                'sms_lam_priority' => '2',
                'sms_provider' => 'lam'
            ];

            $created_count = 0;
            foreach ($new_keys_defaults as $key => $default_value) {
                // Check if key exists
                $existing = $this->dietetic_notifications_model->get_setting($key);

                if ($existing === null) {
                    // Create with default value
                    $insert_data = [
                        'setting_key' => $key,
                        'setting_value' => $default_value,
                        'updated_at' => date('Y-m-d H:i:s')
                    ];

                    if ($this->db->insert($table, $insert_data)) {
                        $created_count++;
                    }
                }
            }

            // Verify cleanup was successful
            $remaining_old_keys = 0;
            foreach ($old_keys as $key) {
                $this->db->where('setting_key', $key);
                $remaining_old_keys += $this->db->count_all_results($table);
            }

            log_activity("LAM SMS Cleanup: {$deleted_count} anciennes clés supprimées, {$created_count} nouvelles clés créées");

            echo json_encode([
                'success' => true,
                'message' => "Nettoyage terminé avec succès!",
                'deleted_count' => $deleted_count,
                'created_count' => $created_count,
                'remaining_old_keys' => $remaining_old_keys
            ]);

        } catch (Exception $e) {
            log_activity('Cleanup Error: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Erreur lors du nettoyage: ' . $e->getMessage()
            ]);
        }
    }
}
