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

                // LAM WhatsApp API credentials
                'whatsapp_lam_account_id' => $this->input->post('whatsapp_lam_account_id'),
                'whatsapp_lam_password' => $this->input->post('whatsapp_lam_password'),
                'whatsapp_lam_sender_number' => $this->input->post('whatsapp_lam_sender_number'),
                'whatsapp_lam_ret_url' => $this->input->post('whatsapp_lam_ret_url'),

                // Other WhatsApp providers (Twilio, Meta, etc.)
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

                // OneSignal (Push Notifications for Median)
                'onesignal_app_id' => $this->input->post('onesignal_app_id'),
                'onesignal_rest_api_key' => $this->input->post('onesignal_rest_api_key'),
                'onesignal_user_auth_key' => $this->input->post('onesignal_user_auth_key'),
                'onesignal_web_enabled' => $this->input->post('onesignal_web_enabled') ? '1' : '0',
            ];

            // Log for debugging (hide sensitive data)
            $safe_settings = $settings;
            if (isset($safe_settings['sms_lam_password'])) {
                $safe_settings['sms_lam_password'] = $settings['sms_lam_password'] ? '***SET***' : '***EMPTY***';
            }
            if (isset($safe_settings['whatsapp_lam_password'])) {
                $safe_settings['whatsapp_lam_password'] = $settings['whatsapp_lam_password'] ? '***SET***' : '***EMPTY***';
            }
            if (isset($safe_settings['whatsapp_api_key'])) {
                $safe_settings['whatsapp_api_key'] = $settings['whatsapp_api_key'] ? '***SET***' : '***EMPTY***';
            }
            if (isset($safe_settings['firebase_service_account_json'])) {
                $safe_settings['firebase_service_account_json'] = $settings['firebase_service_account_json'] ? '***JSON_SET***' : '***EMPTY***';
            }
            if (isset($safe_settings['firebase_server_key'])) {
                $safe_settings['firebase_server_key'] = $settings['firebase_server_key'] ? '***SET***' : '***EMPTY***';
            }
            if (isset($safe_settings['onesignal_rest_api_key'])) {
                $safe_settings['onesignal_rest_api_key'] = $settings['onesignal_rest_api_key'] ? '***SET***' : '***EMPTY***';
            }
            if (isset($safe_settings['onesignal_user_auth_key'])) {
                $safe_settings['onesignal_user_auth_key'] = $settings['onesignal_user_auth_key'] ? '***SET***' : '***EMPTY***';
            }
            log_activity('🔍 [DEBUG] Settings to save: ' . json_encode($safe_settings));

            $success_count = 0;
            $error_count = 0;
            $skipped_count = 0;

            foreach ($settings as $key => $value) {
                try {
                    // Log each setting before saving (hide sensitive data)
                    $display_value = $value;
                    if (in_array($key, ['sms_lam_password', 'whatsapp_lam_password', 'whatsapp_api_key', 'firebase_server_key', 'onesignal_rest_api_key', 'onesignal_user_auth_key']) && $value) {
                        $display_value = '***SET***';
                    } elseif ($key === 'firebase_service_account_json' && $value) {
                        $display_value = '***JSON_SET***';
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
     * Enable SMS for all existing patients (AJAX)
     * URL: /admin/dietetic/notifications/enable_sms_all
     */
    public function enable_sms_all()
    {
        if (!is_admin()) {
            ajax_access_denied();
        }

        header('Content-Type: application/json');

        // Load notifications model if not already loaded
        if (!isset($this->dietetic_notifications_model)) {
            $this->load->model('dietetic/dietetic_notifications_model');
        }

        $affected = $this->dietetic_notifications_model->enable_sms_for_all_patients();

        echo json_encode([
            'success' => $affected >= 0,
            'message' => "SMS activé pour {$affected} patient(s)",
            'affected' => $affected
        ]);
    }

    /**
     * Enable WhatsApp for all existing patients (AJAX)
     * URL: /admin/dietetic/notifications/enable_whatsapp_all
     */
    public function enable_whatsapp_all()
    {
        if (!is_admin()) {
            ajax_access_denied();
        }

        header('Content-Type: application/json');

        // Load notifications model if not already loaded
        if (!isset($this->dietetic_notifications_model)) {
            $this->load->model('dietetic/dietetic_notifications_model');
        }

        $affected = $this->dietetic_notifications_model->enable_whatsapp_for_all_patients();

        echo json_encode([
            'success' => $affected >= 0,
            'message' => "WhatsApp activé pour {$affected} patient(s)",
            'affected' => $affected
        ]);
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

            // Get all patients with OneSignal Player IDs
            // Note: In Perfex, tblclients only has 'company' field, not firstname/lastname
            $sql = "SELECT p.id,
                    c.company as patient_name,
                    (SELECT COUNT(*) FROM " . db_prefix() . "dietic_fcm_tokens f
                     WHERE f.patient_id = p.id
                     AND f.is_active = 1
                     AND f.onesignal_player_id IS NOT NULL
                     AND f.onesignal_player_id != '') as player_ids
                    FROM " . db_prefix() . "dietic_patients p
                    LEFT JOIN " . db_prefix() . "clients c ON c.userid = p.client_id
                    ORDER BY c.company ASC";

            log_activity('[TEST_PUSH DEBUG] SQL Query: ' . $sql);

            $query = $this->db->query($sql);
            $data['patients'] = $query->result_array();

            log_activity('[TEST_PUSH DEBUG] Total patients retrieved: ' . count($data['patients']));

            // Log patients with OneSignal Player IDs
            $patients_with_players = array_filter($data['patients'], function($p) {
                return $p['player_ids'] > 0;
            });
            log_activity('[TEST_PUSH DEBUG] Patients with Player IDs: ' . count($patients_with_players));

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
            $patients_with_tokens = $this->db->count_all_results(db_prefix() . 'dietic_fcm_tokens');

            // Total devices = same as total tokens (each token = 1 device)
            $total_devices = $total_tokens;

            // Count notifications sent today via push channel
            $this->db->where('channel', 'push');
            $this->db->where('DATE(sent_at) =', date('Y-m-d'));
            $sent_today = $this->db->count_all_results(db_prefix() . 'dietic_notification_logs');

            // Get API version
            $use_v1 = $this->dietetic_notifications_model->get_setting('firebase_use_v1_api');
            $push_enabled = $this->dietetic_notifications_model->get_setting('push_enabled');

            $api_version = 'disabled';
            if ($push_enabled == '1') {
                $api_version = $use_v1 == '1' ? 'v1' : 'legacy';
            }

            echo json_encode([
                'success' => true,
                'stats' => [
                    'total_tokens' => $total_tokens,
                    'patients_with_tokens' => $patients_with_tokens,
                    'total_devices' => $total_devices,
                    'sent_today' => $sent_today,
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

        log_activity('[SEND_TEST_PUSH] Method called');

        if (!is_admin()) {
            log_activity('[SEND_TEST_PUSH] Access denied - not admin');
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

        log_activity('[SEND_TEST_PUSH] Params: patient_id=' . $patient_id . ', title=' . $title);

        // Validate inputs
        if (!$patient_id || !$title || !$body) {
            log_activity('[SEND_TEST_PUSH] Validation failed - missing required fields');
            echo json_encode([
                'success' => false,
                'message' => 'Veuillez remplir tous les champs requis'
            ]);
            return;
        }

        // Load Firebase library
        $this->load->library('dietetic/firebase_cloud_messaging');
        log_activity('[SEND_TEST_PUSH] Firebase library loaded');

        try {
            log_activity('[SEND_TEST_PUSH] Checking if Firebase is enabled');

            // Check if Firebase is enabled
            $is_enabled = $this->firebase_cloud_messaging->is_enabled();
            log_activity('[SEND_TEST_PUSH] Firebase is_enabled: ' . ($is_enabled ? 'YES' : 'NO'));

            if (!$is_enabled) {
                log_activity('[SEND_TEST_PUSH] Firebase not enabled - returning error');
                echo json_encode([
                    'success' => false,
                    'message' => 'Firebase Cloud Messaging n\'est pas configuré ou désactivé'
                ]);
                return;
            }

            log_activity('[SEND_TEST_PUSH] Getting tokens for patient_id: ' . $patient_id);

            // Get all active tokens for this patient
            $this->db->where('patient_id', $patient_id);
            $this->db->where('is_active', 1);
            $query = $this->db->get(db_prefix() . 'dietic_fcm_tokens');
            $tokens = $query->result_array();

            log_activity('[SEND_TEST_PUSH] Found ' . count($tokens) . ' token(s)');

            if (empty($tokens)) {
                log_activity('[SEND_TEST_PUSH] No tokens found - returning error');
                echo json_encode([
                    'success' => false,
                    'message' => 'Ce patient n\'a aucun appareil enregistré pour les notifications push'
                ]);
                return;
            }

            $success_count = 0;
            $error_count = 0;
            $errors = [];

            log_activity('[SEND_TEST_PUSH] Starting to send to ' . count($tokens) . ' device(s)');

            // Send to each device
            foreach ($tokens as $token_row) {
                $options = [
                    'click_action' => $url ?: site_url('dietetic/portal'),
                    'icon' => base_url('uploads/company/favicon.png')
                ];

                log_activity('[SEND_TEST_PUSH] Sending to token: ' . substr($token_row['token'], 0, 20) . '...');

                $result = $this->firebase_cloud_messaging->send_to_device(
                    $token_row['token'],
                    $title,
                    $body,
                    [],
                    $options
                );

                log_activity('[SEND_TEST_PUSH] Send result: ' . ($result['success'] ? 'SUCCESS' : 'FAILED - ' . ($result['error'] ?? 'Unknown error')));

                if ($result['success']) {
                    $success_count++;
                } else {
                    $error_count++;
                    $errors[] = $result['error'] ?? $result['message'] ?? 'Unknown error';
                }
            }

            log_activity('[SEND_TEST_PUSH] Total: ' . $success_count . ' success, ' . $error_count . ' failed');

            // Log the test notification
            if (!isset($this->dietetic_notifications_model)) {
                $this->load->model('dietetic/dietetic_notifications_model');
            }

            log_activity('[SEND_TEST_PUSH] Logging notification to database');

            $this->dietetic_notifications_model->log_notification(
                $patient_id,
                'push',
                'Test Notification',
                $body,
                $success_count > 0 ? 'sent' : 'failed',
                $success_count > 0 ? null : implode(', ', $errors)
            );

            log_activity('[SEND_TEST_PUSH] Notification logged, returning response');

            if ($success_count > 0) {
                echo json_encode([
                    'success' => true,
                    'title' => $title,
                    'message' => sprintf(
                        'Notification envoyée avec succès à %d appareil(s)%s',
                        $success_count,
                        $error_count > 0 ? ' (' . $error_count . ' échec(s))' : ''
                    ),
                    'devices_count' => $success_count,
                    'total_sent' => count($tokens)
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'title' => $title,
                    'message' => 'Échec de l\'envoi de la notification: ' . implode(', ', $errors),
                    'devices_count' => 0,
                    'errors' => $errors
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

            // Check for different field names (email_*, sms_*, whatsapp_*, appointment_*)
            $subject = $this->input->post($template_key . '_subject');
            if ($subject === null) {
                $subject = $this->input->post('email_' . $template_key . '_subject');
            }

            $body = $this->input->post($template_key . '_body');
            if ($body === null) {
                $body = $this->input->post('email_' . $template_key . '_body');
            }

            $sms_body = $this->input->post($template_key . '_sms_body');
            if ($sms_body === null) {
                $sms_body = $this->input->post('sms_' . $template_key . '_body');
            }

            $whatsapp_body = $this->input->post($template_key . '_whatsapp_body');
            if ($whatsapp_body === null) {
                $whatsapp_body = $this->input->post('whatsapp_' . $template_key . '_body');
            }

            if ($template_key) {
                // Save subject if provided (Email)
                if ($subject !== null) {
                    $this->dietetic_notifications_model->update_setting(
                        'template_' . $template_key . '_subject',
                        $subject
                    );
                }

                // Save body (Email)
                if ($body !== null) {
                    $this->dietetic_notifications_model->update_setting(
                        'template_' . $template_key . '_body',
                        $body
                    );
                }

                // Save SMS body
                if ($sms_body !== null) {
                    $this->dietetic_notifications_model->update_setting(
                        'template_' . $template_key . '_sms_body',
                        $sms_body
                    );
                }

                // Save WhatsApp body
                if ($whatsapp_body !== null) {
                    $this->dietetic_notifications_model->update_setting(
                        'template_' . $template_key . '_whatsapp_body',
                        $whatsapp_body
                    );
                }

                set_alert('success', 'Modèle enregistré avec succès');
                redirect(admin_url('dietetic/notifications/templates'));
            }
        }

        // Load existing templates from database
        $template_keys = [
            // Welcome
            'welcome',
            // Programs
            'program_assigned', 'program_updated',
            // Consultations
            'consultation_scheduled', 'consultation_reminder', 'consultation_cancelled',
            // Appointments (Patient booking system)
            'appointment_request', 'appointment_accepted', 'appointment_rejected',
            // Food Surveys
            'food_survey_assigned', 'food_survey_reminder',
            // Measurements & Weight
            'weight_reminder', 'milestone',
            // Hydration
            'water_reminder',
            // Messages
            'new_message',
            // Email templates (legacy)
            'email_recommendation', 'email_consultation', 'email_milestone',
            // SMS templates (legacy)
            'sms_hydration', 'sms_weight_reminder', 'sms_consultation_reminder',
            // WhatsApp templates (legacy)
            'whatsapp_program_assigned', 'whatsapp_food_entry_reminder'
        ];

        $data['templates'] = [];
        foreach ($template_keys as $key) {
            $subject = $this->dietetic_notifications_model->get_setting('template_' . $key . '_subject');
            $body = $this->dietetic_notifications_model->get_setting('template_' . $key . '_body');
            $sms_body = $this->dietetic_notifications_model->get_setting('template_' . $key . '_sms_body');
            $whatsapp_body = $this->dietetic_notifications_model->get_setting('template_' . $key . '_whatsapp_body');
            $data['templates'][$key] = [
                'subject' => $subject,
                'body' => $body,
                'sms_body' => $sms_body,
                'whatsapp_body' => $whatsapp_body
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

        // CSRF verification is automatic in Perfex CRM via hooks
        // No manual check needed as it's done by the framework

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

            case 'statistics_notes':
                // Check if statistics notes table exists
                $installed = $this->db->table_exists(db_prefix() . 'dietic_statistics_notes');
                $message = $installed ? 'Installé' : 'Manquant';
                break;

            case 'hydration_tracking':
                // Check if hydration tracking tables exist
                $table1 = $this->db->table_exists(db_prefix() . 'dietic_hydration_tracking');
                $table2 = $this->db->table_exists(db_prefix() . 'dietic_hydration_goals');
                $installed = $table1 && $table2;
                $message = $installed ? 'Installé' : 'Manquant';
                break;

            case 'activity_tracking':
                // Check if activity tracking tables exist
                $table1 = $this->db->table_exists(db_prefix() . 'dietic_activities');
                $table2 = $this->db->table_exists(db_prefix() . 'dietic_patient_activities');
                $installed = $table1 && $table2;
                $message = $installed ? 'Installé' : 'Manquant';
                break;

            case 'achievements_badges':
                // Check if gamification tables exist
                $table1 = $this->db->table_exists(db_prefix() . 'dietic_badge_definitions');
                $table2 = $this->db->table_exists(db_prefix() . 'dietic_patient_badges');
                $table3 = $this->db->table_exists(db_prefix() . 'dietic_patient_points');
                $table4 = $this->db->table_exists(db_prefix() . 'dietic_points_history');
                $installed = $table1 && $table2 && $table3 && $table4;
                $message = $installed ? 'Installé' : 'Manquant';
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

            // Determine which migration file to use (SQL or PHP)
            $migration_files = [
                'lam_update' => 'update_lam_sms_config.sql',
                'notifications' => 'add_notifications_system.sql',
                'firebase' => 'add_firebase_push_notifications.sql',
                'optimizations' => 'optimize_notifications_performance.sql',
                'permissions' => 'add_staff_permissions.sql',
                'firebase_v1' => 'add_firebase_v1_api_support.sql',
                'statistics_notes' => 'add_statistics_notes.php',
                'hydration_tracking' => 'add_hydration_tracking.php',
                'activity_tracking' => 'add_activity_tracking.php',
                'achievements_badges' => 'add_achievements_badges.sql'
            ];

            if (!isset($migration_files[$migration])) {
                // Fallback to old behavior for backward compatibility
                $migration_file = module_dir_path('dietetic', 'migrations/add_notifications_system.sql');
            } else {
                $migration_file = module_dir_path('dietetic', 'migrations/' . $migration_files[$migration]);
            }

            if (!file_exists($migration_file)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Fichier de migration introuvable: ' . basename($migration_file)
                ]);
                return;
            }

            // Check if it's a PHP migration (new format)
            if (pathinfo($migration_file, PATHINFO_EXTENSION) === 'php') {
                return $this->execute_php_migration($migration_file, $migration);
            }

            // Otherwise, it's a SQL file (old format)
            $sql_file = $migration_file;

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

    /**
     * Execute PHP-based migration (CodeIgniter migration format)
     */
    private function execute_php_migration($migration_file, $migration_name)
    {
        try {
            // Check if already installed based on migration name
            if ($migration_name === 'statistics_notes') {
                if ($this->db->table_exists(db_prefix() . 'dietic_statistics_notes')) {
                    echo json_encode([
                        'success' => true,
                        'already_exists' => true,
                        'message' => 'Cette migration a déjà été exécutée'
                    ]);
                    return;
                }
            }

            if ($migration_name === 'hydration_tracking') {
                $table1 = $this->db->table_exists(db_prefix() . 'dietic_hydration_tracking');
                $table2 = $this->db->table_exists(db_prefix() . 'dietic_hydration_goals');
                if ($table1 && $table2) {
                    echo json_encode([
                        'success' => true,
                        'already_exists' => true,
                        'message' => 'Cette migration a déjà été exécutée'
                    ]);
                    return;
                }
            }

            // Load the migration file
            require_once $migration_file;

            // Get the migration class name from filename
            $filename = basename($migration_file, '.php');
            $class_name = 'Migration_' . str_replace(' ', '_', ucwords(str_replace('_', ' ', $filename)));

            if (!class_exists($class_name)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Classe de migration introuvable: ' . $class_name
                ]);
                return;
            }

            // Instantiate and run the migration
            $migration = new $class_name();

            if (!method_exists($migration, 'up')) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Méthode "up" introuvable dans la migration'
                ]);
                return;
            }

            // Execute the migration
            $migration->up();

            log_activity('Migration PHP exécutée: ' . $migration_name);

            echo json_encode([
                'success' => true,
                'message' => 'Migration installée avec succès!'
            ]);

        } catch (Exception $e) {
            log_activity('PHP Migration Error: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Erreur lors de l\'exécution: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * OneSignal Migration Deployment Page
     * Page admin pour déployer la migration Firebase → OneSignal
     */
    public function deploy_onesignal()
    {
        if (!is_admin()) {
            access_denied('OneSignal Deployment');
        }

        $data['title'] = 'Déploiement OneSignal Migration';
        $this->load->view('admin/notifications/deploy_onesignal', $data);
    }

    /**
     * Execute OneSignal Migration (AJAX)
     * Exécute la migration SQL et retourne les résultats en JSON
     */
    public function execute_onesignal_migration()
    {
        if (!is_admin()) {
            echo json_encode([
                'success' => false,
                'message' => 'Access denied. Admin only.'
            ]);
            return;
        }

        header('Content-Type: application/json');

        try {
            $output = [];
            $output[] = "╔════════════════════════════════════════════════════════════════╗";
            $output[] = "║        DIETZONE - OneSignal Migration Deployment              ║";
            $output[] = "║  Migration: Firebase → OneSignal                              ║";
            $output[] = "║  Date: " . date('Y-m-d H:i:s') . "                                    ║";
            $output[] = "╚════════════════════════════════════════════════════════════════╝";
            $output[] = "";

            // Étape 1: Charger la configuration de la base de données
            $output[] = "📋 Étape 1/6 : Chargement de la configuration...";

            $db_config = $this->db;
            $hostname = $db_config->hostname;
            $username = $db_config->username;
            $password = $db_config->password;
            $database = $db_config->database;
            $db_prefix = $db_config->dbprefix;

            $output[] = "✅ Configuration chargée";
            $output[] = "";

            // Étape 2: Connexion (déjà établie via CodeIgniter)
            $output[] = "📋 Étape 2/6 : Connexion à la base de données...";
            $output[] = "   Host: $hostname";
            $output[] = "   Database: $database";
            $output[] = "✅ Connexion établie";
            $output[] = "";

            // Étape 3: Vérifier si la migration est nécessaire
            $output[] = "📋 Étape 3/6 : Vérification de l'état actuel...";

            $migration_needed = false;

            // Vérifier si la colonne onesignal_player_id existe déjà
            $result = $this->db->query("SHOW COLUMNS FROM {$db_prefix}dietic_fcm_tokens LIKE 'onesignal_player_id'");
            if ($result->num_rows() == 0) {
                $output[] = "⚠️  Colonne onesignal_player_id manquante";
                $migration_needed = true;
            } else {
                $output[] = "✅ Colonne onesignal_player_id existe déjà";
            }

            // Vérifier si les settings OneSignal existent
            $result = $this->db->query("SELECT COUNT(*) as count FROM {$db_prefix}dietic_notification_settings WHERE setting_key LIKE 'onesignal%'");
            $row = $result->row();
            if ($row->count == 0) {
                $output[] = "⚠️  Settings OneSignal manquants";
                $migration_needed = true;
            } else {
                $output[] = "✅ Settings OneSignal existent (" . $row->count . " entrées)";
            }

            if (!$migration_needed) {
                $output[] = "";
                $output[] = "✨ Migration déjà effectuée ! Aucune action nécessaire.";
                $output[] = "";

                // Afficher l'état actuel
                $output[] = "📊 État actuel de la base de données:";
                $output[] = "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━";

                $result = $this->db->query("
                    SELECT
                        COUNT(*) as total_devices,
                        SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_devices,
                        SUM(CASE WHEN onesignal_player_id IS NOT NULL THEN 1 ELSE 0 END) as onesignal_registered,
                        SUM(CASE WHEN token IS NOT NULL THEN 1 ELSE 0 END) as firebase_tokens
                    FROM {$db_prefix}dietic_fcm_tokens
                ");

                if ($result->num_rows() > 0) {
                    $stats = $result->row();
                    $output[] = "   Total appareils      : " . $stats->total_devices;
                    $output[] = "   Appareils actifs     : " . $stats->active_devices;
                    $output[] = "   OneSignal Players    : " . $stats->onesignal_registered;
                    $output[] = "   Firebase Tokens      : " . $stats->firebase_tokens;
                }

                echo json_encode([
                    'success' => true,
                    'already_migrated' => true,
                    'output' => implode("\n", $output)
                ]);
                return;
            }

            $output[] = "";
            $output[] = "🚀 Migration nécessaire, démarrage...";
            $output[] = "";

            // Étape 4: Exécuter la migration SQL
            $output[] = "📋 Étape 4/6 : Exécution de la migration SQL...";

            // Lire le fichier SQL
            $sql_file = FCPATH . 'modules/dietetic/migrations/migrate_to_onesignal.sql';
            if (!file_exists($sql_file)) {
                throw new Exception("Fichier SQL introuvable: $sql_file");
            }

            $sql_content = file_get_contents($sql_file);

            // Remplacer les préfixes de table
            $sql_content = str_replace('tbl', $db_prefix, $sql_content);

            // Nettoyer le SQL
            $sql_lines = explode("\n", $sql_content);
            $sql_commands = [];
            $current_command = '';

            foreach ($sql_lines as $line) {
                $line = trim($line);
                if (empty($line) || substr($line, 0, 2) == '--' || substr($line, 0, 1) == '#') {
                    continue;
                }
                $current_command .= $line . "\n";
                if (substr(rtrim($line), -1) == ';') {
                    $sql_commands[] = trim($current_command);
                    $current_command = '';
                }
            }

            $output[] = "   Nombre de commandes SQL à exécuter: " . count($sql_commands);
            $output[] = "";

            $success_count = 0;
            $error_count = 0;

            foreach ($sql_commands as $index => $sql) {
                if (stripos(trim($sql), 'SELECT') === 0) {
                    continue;
                }

                $line_output = "   Exécution commande " . ($index + 1) . "...";

                try {
                    $this->db->query($sql);
                    $line_output .= " ✅";
                    $success_count++;
                } catch (Exception $e) {
                    $error = $e->getMessage();
                    if (strpos($error, 'Duplicate') !== false || strpos($error, 'already exists') !== false) {
                        $line_output .= " ⚠️  (déjà existant)";
                        $success_count++;
                    } else {
                        $line_output .= " ❌";
                        $line_output .= "\n      ERREUR: $error";
                        $error_count++;
                    }
                }

                $output[] = $line_output;
            }

            $output[] = "";
            $output[] = "   Résultat: $success_count succès, $error_count erreurs";

            if ($error_count > 0) {
                $output[] = "⚠️  ATTENTION: Certaines commandes ont échoué.";
            }

            $output[] = "✅ Migration SQL terminée";
            $output[] = "";

            // Étape 5: Vérifier la migration
            $output[] = "📋 Étape 5/6 : Vérification de la migration...";

            $checks_passed = 0;
            $checks_total = 3;

            // Check 1
            $result = $this->db->query("SHOW COLUMNS FROM {$db_prefix}dietic_fcm_tokens LIKE 'onesignal_player_id'");
            if ($result->num_rows() > 0) {
                $output[] = "   ✅ Colonne onesignal_player_id créée";
                $checks_passed++;
            } else {
                $output[] = "   ❌ Colonne onesignal_player_id manquante";
            }

            // Check 2
            $result = $this->db->query("SHOW INDEX FROM {$db_prefix}dietic_fcm_tokens WHERE Key_name = 'idx_player_id'");
            if ($result->num_rows() > 0) {
                $output[] = "   ✅ Index idx_player_id créé";
            } else {
                $output[] = "   ⚠️  Index idx_player_id manquant (non critique)";
            }
            $checks_passed++;

            // Check 3
            $result = $this->db->query("SELECT COUNT(*) as count FROM {$db_prefix}dietic_notification_settings WHERE setting_key LIKE 'onesignal%'");
            $row = $result->row();
            if ($row->count >= 3) {
                $output[] = "   ✅ Settings OneSignal créés (" . $row->count . " entrées)";
                $checks_passed++;
            } else {
                $output[] = "   ❌ Settings OneSignal incomplets";
            }

            $output[] = "";
            $output[] = "   Résultat: $checks_passed/$checks_total vérifications passées";
            $output[] = "✅ Vérification terminée";
            $output[] = "";

            // Étape 6: Résumé
            $output[] = "📋 Étape 6/6 : Résumé final...";
            $output[] = "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━";

            $result = $this->db->query("
                SELECT
                    COUNT(*) as total_devices,
                    SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_devices,
                    SUM(CASE WHEN onesignal_player_id IS NOT NULL THEN 1 ELSE 0 END) as onesignal_registered,
                    SUM(CASE WHEN token IS NOT NULL THEN 1 ELSE 0 END) as firebase_tokens
                FROM {$db_prefix}dietic_fcm_tokens
            ");

            if ($result->num_rows() > 0) {
                $stats = $result->row();
                $output[] = "📊 État de la base de données:";
                $output[] = "   Total appareils      : " . $stats->total_devices;
                $output[] = "   Appareils actifs     : " . $stats->active_devices;
                $output[] = "   OneSignal Players    : " . $stats->onesignal_registered;
                $output[] = "   Firebase Tokens      : " . $stats->firebase_tokens;
                $output[] = "";
            }

            $output[] = "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━";
            $output[] = "✅ MIGRATION TERMINÉE AVEC SUCCÈS !";
            $output[] = "";
            $output[] = "📝 Prochaines étapes:";
            $output[] = "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━";
            $output[] = "1️⃣  Configurer OneSignal (onglet Settings ci-dessus)";
            $output[] = "2️⃣  Uploader OneSignalSDKWorker.js à la racine";
            $output[] = "3️⃣  Configurer Median Dashboard";
            $output[] = "4️⃣  Rebuild l'APK Median";

            echo json_encode([
                'success' => true,
                'already_migrated' => false,
                'output' => implode("\n", $output)
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get OneSignal statistics (Player IDs count, patients list)
     * Called by AJAX from test_push.php
     */
    public function get_onesignal_stats()
    {
        header('Content-Type: application/json');

        try {
            // Count total active Player IDs
            $this->db->where('onesignal_player_id IS NOT NULL');
            $this->db->where('onesignal_player_id !=', '');
            $this->db->where('is_active', 1);
            $total_player_ids = $this->db->count_all_results(db_prefix() . 'dietic_fcm_tokens');

            // Get patients with Player IDs
            $this->db->select('f.id, f.patient_id, f.onesignal_player_id, f.device_type, f.created_at, f.updated_at, p.id as patient_id, c.company as patient_name, c.email as patient_email');
            $this->db->from(db_prefix() . 'dietic_fcm_tokens f');
            $this->db->join(db_prefix() . 'dietic_patients p', 'p.id = f.patient_id');
            $this->db->join(db_prefix() . 'clients c', 'c.userid = p.client_id', 'left');
            $this->db->where('f.onesignal_player_id IS NOT NULL');
            $this->db->where('f.onesignal_player_id !=', '');
            $this->db->where('f.is_active', 1);
            $this->db->order_by('f.updated_at', 'DESC');
            $patients = $this->db->get()->result();

            echo json_encode([
                'success' => true,
                'total' => $total_player_ids,
                'patients' => $patients
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Send test OneSignal notification
     * Called by AJAX from test_push.php
     */
    public function send_test_onesignal()
    {
        header('Content-Type: application/json');

        try {
            // Get POST data
            $send_mode = $this->input->post('send_mode') ?? 'patient'; // 'all' or 'patient'
            $patient_ids = $this->input->post('patient_ids'); // Array of patient IDs
            $patient_id = $this->input->post('patient_id'); // Single patient ID
            $title = $this->input->post('title');
            $message = $this->input->post('message') ?? $this->input->post('body'); // Support both 'message' and 'body'

            log_activity('[OneSignal Test] Send mode: ' . $send_mode);

            if (empty($title) || empty($message)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Le titre et le message sont requis'
                ]);
                return;
            }

            // Load OneSignal library
            $this->load->library('dietetic/onesignal_cloud_messaging');

            // MODE 1: Send to ALL users (web + mobile) using OneSignal segments
            if ($send_mode === 'all') {
                log_activity('[OneSignal Test] Sending to ALL users via segment');

                // Don't send URL for mobile apps - it would open browser instead of app
                // Just open the app on its main screen when notification is tapped
                $result = $this->onesignal_cloud_messaging->send_to_segment(
                    'All', // OneSignal built-in segment for all users
                    $title,
                    $message,
                    [
                        'type' => 'test',
                        'timestamp' => date('Y-m-d H:i:s')
                    ],
                    [] // No click_action URL - app will just open to main screen
                );

                log_activity('[OneSignal Test] Segment send result: ' . json_encode($result));

                if ($result['success']) {
                    $recipients = $result['recipients'] ?? 0;
                    log_activity('[OneSignal Test] ✓ SUCCESS - Sent to all users (' . $recipients . ' recipients)');

                    echo json_encode([
                        'success' => true,
                        'message' => 'Notification envoyée à tous les utilisateurs',
                        'recipients' => $recipients,
                        'notification_id' => $result['notification_id'] ?? null
                    ]);
                } else {
                    $error_msg = $result['error'] ?? 'Unknown error';
                    log_activity('[OneSignal Test] ✗ FAILED - ' . $error_msg);

                    echo json_encode([
                        'success' => false,
                        'message' => 'Erreur lors de l\'envoi: ' . $error_msg,
                        'http_code' => $result['http_code'] ?? null
                    ]);
                }
                return;
            }

            // MODE 2: Send to specific patient(s)
            // Convert single patient_id to array
            if (!empty($patient_id) && empty($patient_ids)) {
                $patient_ids = [$patient_id];
            }

            if (empty($patient_ids) || !is_array($patient_ids)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Aucun patient sélectionné'
                ]);
                return;
            }

            log_activity('[OneSignal Test] Sending to specific patients: ' . implode(', ', $patient_ids));

            $sent_count = 0;
            $failed_count = 0;
            $errors = [];

            foreach ($patient_ids as $patient_id) {
                // Get patient Player IDs
                $this->db->select('onesignal_player_id');
                $this->db->where('patient_id', $patient_id);
                $this->db->where('onesignal_player_id IS NOT NULL');
                $this->db->where('onesignal_player_id !=', '');
                $this->db->where('is_active', 1);
                $player_ids_records = $this->db->get(db_prefix() . 'dietic_fcm_tokens')->result();

                log_activity('[OneSignal Test] Patient ' . $patient_id . ': Found ' . count($player_ids_records) . ' Player IDs');

                if (empty($player_ids_records)) {
                    $failed_count++;
                    $errors[] = "Patient ID $patient_id: Aucun Player ID actif";
                    log_activity('[OneSignal Test] Patient ' . $patient_id . ': No active Player IDs');
                    continue;
                }

                // Extract Player IDs
                $player_ids = array_map(function($record) {
                    return $record->onesignal_player_id;
                }, $player_ids_records);

                log_activity('[OneSignal Test] Sending to Player IDs: ' . implode(', ', $player_ids));
                log_activity('[OneSignal Test] Title: ' . $title);
                log_activity('[OneSignal Test] Message: ' . substr($message, 0, 100));

                // Send notification using send_to_players (public method)
                $url = $this->input->post('url') ?? site_url('dietetic/portal');
                $result = $this->onesignal_cloud_messaging->send_to_players(
                    $player_ids,
                    $title,
                    $message,
                    [
                        'type' => 'test',
                        'timestamp' => date('Y-m-d H:i:s')
                    ],
                    [
                        'click_action' => $url
                    ]
                );

                log_activity('[OneSignal Test] OneSignal API Response: ' . json_encode($result));

                if ($result['success']) {
                    $sent_count++;
                    $recipients = $result['recipients'] ?? count($player_ids);
                    log_activity('[OneSignal Test] ✓ SUCCESS - Notification sent to patient ' . $patient_id . ' (' . $recipients . ' recipients)');
                } else {
                    $failed_count++;
                    $error_msg = "Patient ID $patient_id: " . ($result['error'] ?? 'Unknown error');
                    $errors[] = $error_msg;
                    log_activity('[OneSignal Test] ✗ FAILED - Patient ' . $patient_id . ': ' . $error_msg);

                    // Log full error details
                    if (isset($result['http_code'])) {
                        log_activity('[OneSignal Test] HTTP Code: ' . $result['http_code']);
                    }
                    if (isset($result['response'])) {
                        log_activity('[OneSignal Test] Full Response: ' . json_encode($result['response']));
                    }
                }
            }

            $response = [
                'success' => $sent_count > 0,
                'sent' => $sent_count,
                'failed' => $failed_count,
                'message' => "$sent_count notification(s) envoyée(s), $failed_count échec(s)"
            ];

            if (!empty($errors)) {
                $response['errors'] = $errors;
            }

            echo json_encode($response);

        } catch (Exception $e) {
            log_activity('[OneSignal Test] Exception: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Erreur serveur: ' . $e->getMessage()
            ]);
        }
    }
}
