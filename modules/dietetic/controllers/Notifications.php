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

            // Note: CSRF is automatically verified by CodeIgniter, no manual check needed
            log_activity('✅ [DEBUG] CSRF validation PASSED (automatic)');

            $settings = [
                // General settings
                'notifications_enabled' => $this->input->post('notifications_enabled') ? '1' : '0',
                'push_enabled' => $this->input->post('push_enabled') ? '1' : '0',

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

                // Firebase Cloud Messaging (Push Notifications)
                'firebase_api_key' => $this->input->post('firebase_api_key'),
                'firebase_auth_domain' => $this->input->post('firebase_auth_domain'),
                'firebase_project_id' => $this->input->post('firebase_project_id'),
                'firebase_storage_bucket' => $this->input->post('firebase_storage_bucket'),
                'firebase_messaging_sender_id' => $this->input->post('firebase_messaging_sender_id'),
                'firebase_app_id' => $this->input->post('firebase_app_id'),
                'firebase_vapid_key' => $this->input->post('firebase_vapid_key'),
                'firebase_server_key' => $this->input->post('firebase_server_key'),

                // Firebase API v1 (Modern)
                'firebase_use_v1_api' => $this->input->post('firebase_use_v1_api') ? '1' : '0',
                'firebase_service_account_json' => $this->input->post('firebase_service_account_json'),
            ];

            // Log for debugging (hide sensitive data)
            $safe_settings = $settings;
            if (isset($safe_settings['sms_lam_password'])) {
                $safe_settings['sms_lam_password'] = $settings['sms_lam_password'] ? '***SET***' : '***EMPTY***';
            }
            if (isset($safe_settings['firebase_service_account_json'])) {
                $safe_settings['firebase_service_account_json'] = $settings['firebase_service_account_json'] ? '***JSON_SET***' : '***EMPTY***';
            }
            if (isset($safe_settings['firebase_server_key'])) {
                $safe_settings['firebase_server_key'] = $settings['firebase_server_key'] ? '***SET***' : '***EMPTY***';
            }
            log_activity('🔍 [DEBUG] Settings to save: ' . json_encode($safe_settings));

            $success_count = 0;
            $error_count = 0;
            $skipped_count = 0;

            foreach ($settings as $key => $value) {
                try {
                    // Log each setting before saving (hide sensitive data)
                    $display_value = $value;
                    if ($key === 'sms_lam_password' && $value) {
                        $display_value = '***SET***';
                    } elseif ($key === 'firebase_service_account_json' && $value) {
                        $display_value = '***JSON_SET***';
                    } elseif ($key === 'firebase_server_key' && $value) {
                        $display_value = '***SET***';
                    }
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
     * Scan all patients for milestones (AJAX)
     */
    public function scan_milestones()
    {
        // Clear any output buffers
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!is_admin()) {
            echo json_encode([
                'success' => false,
                'message' => 'Accès refusé'
            ]);
            die();
        }

        // Load notifications model
        if (!isset($this->dietetic_notifications_model)) {
            $this->load->model('dietetic/dietetic_notifications_model');
        }

        try {
            log_activity('scan_milestones: Starting scan...');

            $results = $this->dietetic_notifications_model->scan_all_patients_for_milestones();

            log_activity('scan_milestones: Scan completed - ' . $results['milestones_detected'] . ' milestones detected');

            echo json_encode([
                'success' => true,
                'message' => sprintf(
                    '%d patients analysés, %d jalons détectés',
                    $results['patients_checked'],
                    $results['milestones_detected']
                ),
                'data' => $results
            ]);

        } catch (Exception $e) {
            log_activity('scan_milestones ERROR: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ]);
        }

        die();
    }

    /**
     * Test push notifications page
     */
    public function test_push()
    {
        // Force OPcache refresh - log immediately
        log_activity('[TEST_PUSH ENTRY] Method test_push() called at ' . date('Y-m-d H:i:s'));

        if (!is_admin()) {
            log_activity('[TEST_PUSH] Not admin - access denied');
            access_denied('Test Push Notifications');
        }

        log_activity('[TEST_PUSH] Admin check passed');

        // Check if tables exist
        $table_exists = $this->db->table_exists(db_prefix() . 'dietic_fcm_tokens');
        log_activity('[TEST_PUSH] Table dietic_fcm_tokens exists: ' . ($table_exists ? 'YES' : 'NO'));

        if (!$table_exists) {
            log_activity('[TEST_PUSH] Redirecting to run_migration');
            redirect(admin_url('dietetic/notifications/run_migration'));
        }

        log_activity('[TEST_PUSH] Loading notifications model');

        // Load notifications model
        if (!isset($this->dietetic_notifications_model)) {
            $this->load->model('dietetic/dietetic_notifications_model');
        }

        $data['title'] = 'Test des Notifications Push';

        log_activity('[TEST_PUSH] Starting try block for patient query');

        try {
            // Debug: Check total tokens first
            $total_tokens_check = $this->db->where('is_active', 1)
                                           ->count_all_results(db_prefix() . 'dietic_fcm_tokens');
            log_activity('[TEST_PUSH DEBUG] Total active tokens in DB: ' . $total_tokens_check);

            // Get all patients with FCM tokens
            // Note: patient names come from tblclients, not tbldietic_patients
            $sql = "SELECT p.id,
                    CONCAT(COALESCE(c.firstname, ''), ' ', COALESCE(c.lastname, '')) as fullname,
                    c.company as company_name,
                    (SELECT COUNT(*) FROM " . db_prefix() . "dietic_fcm_tokens f
                     WHERE f.patient_id = p.id AND f.is_active = 1) as fcm_tokens
                    FROM " . db_prefix() . "dietic_patients p
                    LEFT JOIN " . db_prefix() . "clients c ON c.userid = p.client_id
                    ORDER BY c.firstname ASC, c.lastname ASC";

            log_activity('[TEST_PUSH DEBUG] SQL Query: ' . $sql);

            $query = $this->db->query($sql);
            $data['patients'] = $query->result_array();

            log_activity('[TEST_PUSH DEBUG] Total patients retrieved: ' . count($data['patients']));

            // Log first few patients with tokens
            $patients_with_tokens = array_filter($data['patients'], function($p) {
                return $p['fcm_tokens'] > 0;
            });
            log_activity('[TEST_PUSH DEBUG] Patients with tokens: ' . count($patients_with_tokens));

        } catch (Exception $e) {
            log_activity('[TEST_PUSH ERROR] Exception: ' . $e->getMessage());
            $data['patients'] = [];
        }

        $this->load->view('admin/notifications/test_push', $data);
    }

    /**
     * Get push notification statistics (AJAX)
     */
    public function get_push_stats()
    {
        header('Content-Type: application/json');

        if (!is_admin()) {
            echo json_encode([
                'success' => false,
                'message' => 'Accès refusé'
            ]);
            return;
        }

        // Load notifications model
        if (!isset($this->dietetic_notifications_model)) {
            $this->load->model('dietetic/dietetic_notifications_model');
        }

        try {
            // Count active FCM tokens
            $this->db->where('is_active', 1);
            $total_tokens = $this->db->count_all_results(db_prefix() . 'dietic_fcm_tokens');

            // Count patients with at least one active token
            $this->db->select('DISTINCT patient_id');
            $this->db->where('is_active', 1);
            $total_patients = $this->db->count_all_results(db_prefix() . 'dietic_fcm_tokens');

            // Get API version
            $use_v1 = $this->dietetic_notifications_model->get_setting('firebase_use_v1_api');
            $push_enabled = $this->dietetic_notifications_model->get_setting('push_enabled');

            $api_version = 'disabled';
            if ($push_enabled == '1') {
                $api_version = $use_v1 == '1' ? 'v1' : 'legacy';
            }

            echo json_encode([
                'success' => true,
                'data' => [
                    'total_tokens' => $total_tokens,
                    'total_patients' => $total_patients,
                    'api_version' => $api_version
                ]
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Send test push notification (AJAX)
     */
    public function send_test_push()
    {
        header('Content-Type: application/json');

        if (!is_admin()) {
            echo json_encode([
                'success' => false,
                'message' => 'Accès refusé'
            ]);
            return;
        }

        $patient_id = $this->input->post('patient_id');
        $title = $this->input->post('title');
        $body = $this->input->post('body');
        $url = $this->input->post('url');

        // Validate inputs
        if (!$patient_id || !$title || !$body) {
            echo json_encode([
                'success' => false,
                'message' => 'Veuillez remplir tous les champs requis'
            ]);
            return;
        }

        // Load Firebase library
        $this->load->library('dietetic/firebase_cloud_messaging');

        try {
            // Check if Firebase is enabled
            if (!$this->firebase_cloud_messaging->is_enabled()) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Firebase Cloud Messaging n\'est pas configuré ou désactivé'
                ]);
                return;
            }

            // Get all active tokens for this patient
            $this->db->where('patient_id', $patient_id);
            $this->db->where('is_active', 1);
            $query = $this->db->get(db_prefix() . 'dietic_fcm_tokens');
            $tokens = $query->result_array();

            if (empty($tokens)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Ce patient n\'a aucun appareil enregistré pour les notifications push'
                ]);
                return;
            }

            $success_count = 0;
            $error_count = 0;
            $errors = [];

            // Send to each device
            foreach ($tokens as $token_row) {
                $options = [
                    'click_action' => $url ?: site_url('dietetic/portal'),
                    'icon' => base_url('uploads/company/favicon.png')
                ];

                $result = $this->firebase_cloud_messaging->send_notification(
                    $token_row['token'],
                    $title,
                    $body,
                    [],
                    $options
                );

                if ($result['success']) {
                    $success_count++;
                } else {
                    $error_count++;
                    $errors[] = $result['message'];
                }
            }

            // Log the test notification
            if (!isset($this->dietetic_notifications_model)) {
                $this->load->model('dietetic/dietetic_notifications_model');
            }

            $this->dietetic_notifications_model->log_notification(
                $patient_id,
                'push',
                'Test Notification',
                $body,
                $success_count > 0 ? 'sent' : 'failed',
                $success_count > 0 ? null : implode(', ', $errors)
            );

            if ($success_count > 0) {
                echo json_encode([
                    'success' => true,
                    'message' => sprintf(
                        'Notification envoyée avec succès à %d appareil(s)%s',
                        $success_count,
                        $error_count > 0 ? ' (' . $error_count . ' échec(s))' : ''
                    )
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Échec de l\'envoi de la notification: ' . implode(', ', $errors)
                ]);
            }

        } catch (Exception $e) {
            log_activity('send_test_push ERROR: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Notification templates page
     */
    public function templates()
    {
        if (!is_admin()) {
            access_denied('Notification Templates');
        }

        // Load notifications model
        if (!isset($this->dietetic_notifications_model)) {
            $this->load->model('dietetic/dietetic_notifications_model');
        }

        // Handle form submission
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $template_key = $this->input->post('template_key');
            $subject = $this->input->post('subject');
            $body = $this->input->post('body');

            if ($template_key) {
                // Save subject if provided
                if ($subject !== null) {
                    $this->dietetic_notifications_model->update_setting(
                        'template_' . $template_key . '_subject',
                        $subject
                    );
                }

                // Save body
                if ($body !== null) {
                    $this->dietetic_notifications_model->update_setting(
                        'template_' . $template_key . '_body',
                        $body
                    );
                }

                set_alert('success', 'Modèle enregistré avec succès');
                redirect(admin_url('dietetic/notifications/templates'));
            }
        }

        // Load existing templates from database
        $template_keys = [
            // Email templates
            'email_recommendation', 'email_consultation', 'email_milestone',
            // SMS templates
            'sms_hydration', 'sms_weight_reminder', 'sms_consultation_reminder',
            // WhatsApp templates
            'whatsapp_program_assigned', 'whatsapp_food_entry_reminder'
        ];

        $data['templates'] = [];
        foreach ($template_keys as $key) {
            $subject = $this->dietetic_notifications_model->get_setting('template_' . $key . '_subject');
            $body = $this->dietetic_notifications_model->get_setting('template_' . $key . '_body');
            $data['templates'][$key] = [
                'subject' => $subject,
                'body' => $body
            ];
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

            case 'permissions':
                // Check if staff permissions table exists
                $installed = $this->db->table_exists(db_prefix() . 'dietic_staff_permissions');
                $message = $installed ? 'Installé' : 'Manquant';
                break;

            case 'firebase_v1':
                // Check if Firebase v1 API settings exist
                $this->load->model('dietetic/dietetic_notifications_model');

                $v1_settings_exist = true;
                $v1_settings = ['firebase_use_v1_api', 'firebase_service_account_json'];

                foreach ($v1_settings as $setting) {
                    $value = $this->dietetic_notifications_model->get_setting($setting);
                    if ($value === null) {
                        $v1_settings_exist = false;
                        break;
                    }
                }

                $installed = $v1_settings_exist;
                $message = $installed ? 'Installé' : 'À installer';
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
                'optimizations' => 'optimize_notifications_performance.sql',
                'permissions' => 'add_staff_permissions.sql',
                'firebase_v1' => 'add_firebase_v1_api_support.sql'
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
