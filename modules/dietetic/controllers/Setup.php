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
            'tbldietic_patient_fcm_tokens',
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
}
