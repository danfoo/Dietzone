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

            // Check permissions
            if (!dietetic_has_permission('view')) {
                access_denied('dietetic');
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

        // Handle form submission
        if ($this->input->post('save_settings')) {
            $settings = [
                'sms_provider' => $this->input->post('sms_provider'),
                'sms_lam_api_key' => $this->input->post('sms_lam_api_key'),
                'sms_lam_sender_id' => $this->input->post('sms_lam_sender_id'),
                'whatsapp_provider' => $this->input->post('whatsapp_provider'),
                'whatsapp_api_key' => $this->input->post('whatsapp_api_key'),
                'whatsapp_phone_number' => $this->input->post('whatsapp_phone_number'),
                'notifications_enabled' => $this->input->post('notifications_enabled') ? '1' : '0',
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
     * Execute migration via AJAX
     */
    public function execute_migration()
    {
        if (!is_admin()) {
            ajax_access_denied();
        }

        header('Content-Type: application/json');

        try {
            // Check if tables already exist
            if ($this->db->table_exists(db_prefix() . 'dietic_notification_preferences')) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Les tables de notifications existent déjà.',
                    'already_exists' => true
                ]);
                return;
            }

            // Read and execute migration SQL file
            $sql_file = __DIR__ . '/../migrations/add_notifications_system.sql';

            if (!file_exists($sql_file)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Fichier de migration introuvable: ' . $sql_file
                ]);
                return;
            }

            $sql_content = file_get_contents($sql_file);

            // Replace table prefix
            $sql_content = str_replace('`tbldietic_', '`' . db_prefix() . 'dietic_', $sql_content);

            // Split by semicolon
            $statements = array_filter(array_map('trim', explode(';', $sql_content)));

            $success_count = 0;
            $error_count = 0;
            $errors = [];

            foreach ($statements as $statement) {
                if (!empty($statement) && !preg_match('/^--/', $statement)) {
                    try {
                        $this->db->query($statement);
                        $success_count++;
                    } catch (Exception $e) {
                        $error_count++;
                        $errors[] = substr($e->getMessage(), 0, 200);
                        log_activity('Notifications Migration Error: ' . $e->getMessage());
                    }
                }
            }

            if ($error_count > 0) {
                echo json_encode([
                    'success' => false,
                    'message' => "Migration partiellement réussie. {$success_count} requêtes réussies, {$error_count} échouées.",
                    'errors' => $errors
                ]);
            } else {
                echo json_encode([
                    'success' => true,
                    'message' => "Migration exécutée avec succès ! {$success_count} tables créées."
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Erreur lors de la migration : ' . $e->getMessage()
            ]);
        }
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

        $data['title'] = 'Jalons Atteints';

        // Get all milestones
        $data['milestones'] = $this->dietetic_notifications_model->get_all_milestones();

        // Get statistics
        $data['milestone_stats'] = $this->dietetic_notifications_model->get_milestone_statistics();

        $this->load->view('admin/notifications/milestones', $data);
    }
}
