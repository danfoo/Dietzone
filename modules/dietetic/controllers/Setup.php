<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Setup Controller
 * Gère la configuration et l'installation des composants du module
 */
class Setup extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Configuration du système de notifications
     * Accès: /admin/dietetic/setup/notifications
     */
    public function notifications()
    {
        if (!is_admin()) {
            access_denied('Setup Notifications');
        }

        // Exécuter le script de setup
        ob_start();
        include(FCPATH . 'modules/dietetic/setup_notifications.php');
        $output = ob_get_clean();

        // Convertir la sortie en HTML
        $html_output = '<pre style="background: #1e1e1e; color: #d4d4d4; padding: 20px; border-radius: 5px; font-family: monospace; font-size: 13px; line-height: 1.6;">';
        $html_output .= htmlspecialchars($output);
        $html_output .= '</pre>';

        // Afficher dans une page admin
        $data['title'] = 'Configuration Notifications';
        $data['output'] = $html_output;

        $this->load->view('admin/setup/notifications_setup', $data);
    }

    /**
     * Vérification de l'état du système de notifications
     * Accès: /admin/dietetic/setup/check_notifications
     */
    public function check_notifications()
    {
        if (!is_admin()) {
            access_denied('Check Notifications');
        }

        $data['title'] = 'Vérification Système de Notifications';

        // Tables requises
        $required_tables = [
            'tbldietic_notification_preferences',
            'tbldietic_notification_logs',
            'tbldietic_milestones',
            'tbldietic_notification_settings',
            'tbldietic_fcm_tokens',
        ];

        $tables_status = [];
        foreach ($required_tables as $table) {
            $query = $this->db->query("SHOW TABLES LIKE '{$table}'");
            $tables_status[$table] = $query->num_rows() > 0;
        }

        $data['tables_status'] = $tables_status;

        // Statistiques
        $data['stats'] = [];

        if ($tables_status['tbldietic_notification_preferences']) {
            $data['stats']['total_patients_with_prefs'] = $this->db->count_all(db_prefix() . 'dietic_notification_preferences');

            $total_patients = $this->db->count_all(db_prefix() . 'dietic_patients');
            $data['stats']['total_patients'] = $total_patients;
            $data['stats']['patients_without_prefs'] = $total_patients - $data['stats']['total_patients_with_prefs'];
        }

        if ($tables_status['tbldietic_notification_logs']) {
            $data['stats']['total_notifications_sent'] = $this->db->count_all(db_prefix() . 'dietic_notification_logs');

            // Notifications des 7 derniers jours
            $this->db->where('created_at >=', date('Y-m-d H:i:s', strtotime('-7 days')));
            $data['stats']['notifications_last_7_days'] = $this->db->count_all_results(db_prefix() . 'dietic_notification_logs');

            // Notifications aujourd'hui
            $this->db->where('DATE(created_at)', date('Y-m-d'));
            $data['stats']['notifications_today'] = $this->db->count_all_results(db_prefix() . 'dietic_notification_logs');
        }

        // Vérifier si le cron Perfex est configuré
        $data['perfex_cron_configured'] = !empty(get_option('cron_has_run_from_cli'));
        $data['last_cron_run'] = get_option('last_cron_run');

        $this->load->view('admin/setup/check_notifications', $data);
    }

    /**
     * Configuration du cron (instructions)
     * Accès: /admin/dietetic/setup/cron_instructions
     */
    public function cron_instructions()
    {
        if (!is_admin()) {
            access_denied('Cron Instructions');
        }

        $data['title'] = 'Configuration du Cron Job';
        $data['project_path'] = FCPATH;

        $this->load->view('admin/setup/cron_instructions', $data);
    }

    /**
     * Diagnostic des notifications push Firebase (HTML)
     * Accès: /admin/dietetic/setup/diagnose_push
     */
    public function diagnose_push()
    {
        if (!is_admin()) {
            access_denied('Diagnose Push Notifications');
        }

        $this->load->model('dietetic/dietetic_notifications_model');

        $data['title'] = 'Diagnostic Push Notifications Firebase';

        // 1. Check Service Worker file
        $sw_path = FCPATH . 'firebase-messaging-sw.js';
        $data['sw_exists'] = file_exists($sw_path);
        $data['sw_path'] = $sw_path;

        if ($data['sw_exists']) {
            $data['sw_size'] = filesize($sw_path);
            $data['sw_modified'] = date('d/m/Y H:i:s', filemtime($sw_path));
            $sw_content = file_get_contents($sw_path);
            $data['sw_content'] = $sw_content;
            $data['sw_has_v3'] = strpos($sw_content, 'v3') !== false;
            $data['sw_preview'] = implode("\n", array_slice(explode("\n", $sw_content), 0, 15));
        }

        // 2. Firebase Configuration
        $firebase_keys = [
            'push_enabled', 'firebase_api_key', 'firebase_project_id',
            'firebase_messaging_sender_id', 'firebase_app_id', 'firebase_vapid_key',
            'firebase_use_v1_api', 'firebase_service_account_json'
        ];

        $data['firebase_config'] = [];
        foreach ($firebase_keys as $key) {
            $value = $this->dietetic_notifications_model->get_setting($key);
            $data['firebase_config'][$key] = [
                'value' => $value,
                'has_value' => !empty($value)
            ];
        }

        // 3. FCM Tokens Stats
        $this->db->where('is_active', 1);
        $data['total_tokens'] = $this->db->count_all_results(db_prefix() . 'dietic_fcm_tokens');

        $this->db->select('DISTINCT patient_id');
        $this->db->where('is_active', 1);
        $data['patients_with_tokens'] = $this->db->count_all_results(db_prefix() . 'dietic_fcm_tokens');

        // Latest tokens
        $this->db->select('t.*, p.id as patient_id, c.company as patient_name');
        $this->db->from(db_prefix() . 'dietic_fcm_tokens t');
        $this->db->join(db_prefix() . 'dietic_patients p', 'p.id = t.patient_id', 'left');
        $this->db->join(db_prefix() . 'clients c', 'c.userid = p.client_id', 'left');
        $this->db->where('t.is_active', 1);
        $this->db->order_by('t.created_at', 'DESC');
        $this->db->limit(5);
        $data['latest_tokens'] = $this->db->get()->result_array();

        // 4. Notification Logs
        $this->db->where('channel', 'push');
        $data['total_push'] = $this->db->count_all_results(db_prefix() . 'dietic_notification_logs');

        $this->db->where('channel', 'push');
        $this->db->where('status', 'sent');
        $data['sent_push'] = $this->db->count_all_results(db_prefix() . 'dietic_notification_logs');

        $this->db->where('channel', 'push');
        $this->db->where('status', 'failed');
        $data['failed_push'] = $this->db->count_all_results(db_prefix() . 'dietic_notification_logs');

        // Latest push notifications
        $this->db->select('l.*, c.company as patient_name');
        $this->db->from(db_prefix() . 'dietic_notification_logs l');
        $this->db->join(db_prefix() . 'dietic_patients p', 'p.id = l.patient_id', 'left');
        $this->db->join(db_prefix() . 'clients c', 'c.userid = p.client_id', 'left');
        $this->db->where('l.channel', 'push');
        $this->db->order_by('l.sent_at', 'DESC');
        $this->db->limit(10);
        $data['latest_push'] = $this->db->get()->result_array();

        $this->load->view('admin/setup/diagnose_push', $data);
    }

    /**
     * Désactiver les notifications push Firebase
     * Accès: /admin/dietetic/setup/disable_push
     */
    public function disable_push()
    {
        if (!is_admin()) {
            access_denied('Disable Push Notifications');
        }

        // Exécuter le script de désactivation
        ob_start();
        include(FCPATH . 'modules/dietetic/disable_push_notifications.php');
        $output = ob_get_clean();

        // Convertir la sortie en HTML
        $html_output = '<pre style="background: #1e1e1e; color: #d4d4d4; padding: 20px; border-radius: 5px; font-family: monospace; font-size: 13px; line-height: 1.6;">';
        $html_output .= htmlspecialchars($output);
        $html_output .= '</pre>';

        // Message de succès
        set_alert('success', 'Les notifications push Firebase ont été désactivées avec succès.');

        // Afficher dans une page admin
        $data['title'] = 'Désactivation Push Notifications';
        $data['output'] = $html_output;

        $this->load->view('admin/setup/notifications_setup', $data);
    }
}
