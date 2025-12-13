<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dietetic extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        // Load helper first
        $this->load->helper('dietetic/dietetic');

        // Load Perfex models
        $this->load->model('clients_model');
        $this->load->model('staff_model');

        // Load dietetic models
        $this->load->model('dietetic/dietetic_patients_model');
        $this->load->model('dietetic/dietetic_consultations_model');
        $this->load->model('dietetic/dietetic_programs_model');
        $this->load->model('dietetic/dietetic_measurements_model');
        $this->load->model('dietetic/dietetic_reminders_model');

        // Check permission
        if (!dietetic_has_permission('view')) {
            access_denied('dietetic');
        }
    }

    /**
     * Dashboard - Main view with KPIs and statistics
     */
    public function index()
    {
        // Redirect to patients list by default for better UX
        redirect(admin_url('dietetic/patients'));
    }

    /**
     * Dashboard view with statistics
     */
    public function dashboard()
    {
        $data['title'] = 'Dietetic Dashboard';

        // Initialize all with defaults
        $data['patient_stats'] = new stdClass();
        $data['patient_stats']->active_patients = 0;

        $data['consultation_stats'] = new stdClass();
        $data['consultation_stats']->scheduled = 0;
        $data['consultation_stats']->avg_satisfaction = 0;

        $data['program_stats'] = new stdClass();
        $data['program_stats']->active_programs = 0;

        $data['recent_patients'] = [];
        $data['upcoming_consultations'] = [];

        // Try to load real data
        try {
            $stats = $this->dietetic_patients_model->get_statistics();
            if ($stats) {
                $data['patient_stats'] = $stats;
            }
        } catch (Exception $e) {}

        try {
            $stats = $this->dietetic_consultations_model->get_statistics();
            if ($stats) {
                $data['consultation_stats'] = $stats;
            }
        } catch (Exception $e) {}

        try {
            $stats = $this->dietetic_programs_model->get_statistics();
            if ($stats) {
                $data['program_stats'] = $stats;
            }
        } catch (Exception $e) {}

        try {
            $patients = $this->dietetic_patients_model->get_all();
            if (is_array($patients)) {
                $data['recent_patients'] = array_slice($patients, 0, 5);
            }
        } catch (Exception $e) {}

        try {
            $consultations = $this->dietetic_consultations_model->get_upcoming(5);
            if (is_array($consultations)) {
                $data['upcoming_consultations'] = $consultations;
            }
        } catch (Exception $e) {}

        $this->load->view('admin/dashboard', $data);
    }

    /**
     * Diagnostic tool for 404 errors
     */
    public function diagnostic()
    {
        if (!is_admin()) {
            access_denied('Diagnostic');
        }

        $this->load->view('diagnostic_web');
    }

    /**
     * Settings page
     */
    public function settings()
    {
        // Check specific permission for settings access
        if (!dietetic_has_permission('settings')) {
            access_denied('dietetic');
        }

        $data['title'] = _l('dietetic_settings');

        // Handle form submission
        if ($this->input->post()) {
            $settings = $this->input->post();

            foreach ($settings as $key => $value) {
                dietetic_update_option($key, $value);
            }

            set_alert('success', _l('settings_updated'));
            redirect(admin_url('dietetic/settings'));
        }

        // Get all settings
        $this->db->select('*');
        $data['settings'] = $this->db->get(db_prefix() . 'dietic_settings')->result();

        $this->load->view('admin/settings', $data);
    }

    /**
     * Debug settings page to check if payment settings exist
     */
    public function settings_debug()
    {
        if (!is_admin()) {
            access_denied('dietetic');
        }

        $this->load->view('admin/settings_debug');
    }

    /**
     * Payment gateways settings page
     */
    public function payment_settings()
    {
        if (!is_admin()) {
            access_denied('dietetic');
        }

        $data['title'] = 'Configuration des Passerelles de Paiement';

        // Handle form submission
        if ($this->input->post()) {
            $settings = $this->input->post();

            foreach ($settings as $key => $value) {
                dietetic_update_option($key, $value);
            }

            set_alert('success', 'Paramètres de paiement enregistrés avec succès');
            redirect(admin_url('dietetic/payment_settings'));
        }

        // Get all settings
        $this->db->select('*');
        $data['settings'] = $this->db->get(db_prefix() . 'dietic_settings')->result();

        $this->load->view('admin/payment_settings', $data);
    }

    /**
     * Cleanup duplicate payment settings
     */
    public function cleanup_payment_settings()
    {
        if (!is_admin()) {
            access_denied('dietetic');
        }

        $this->load->view('admin/cleanup_payment_settings');
    }

    /**
     * Test cron job (for debugging)
     */
    public function test_cron()
    {
        if (!is_admin()) {
            access_denied('dietetic');
        }

        $result = $this->dietetic_reminders_model->send_pending_reminders();

        echo json_encode([
            'success' => true,
            'data'    => $result,
            'message' => sprintf(
                'Sent: %d, Failed: %d, Total: %d',
                $result['sent'],
                $result['failed'],
                $result['total']
            ),
        ]);
    }

    /**
     * Get patient growth chart data
     *
     * @return array
     */
    private function get_patient_growth_chart_data()
    {
        $months = [];
        $counts = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            $months[] = date('M Y', strtotime($month));

            $this->db->where('DATE_FORMAT(created_at, "%Y-%m")', $month);
            if (!is_admin()) {
                $this->db->where('dietitian_id', get_staff_user_id());
            }
            $count = $this->db->count_all_results(db_prefix() . 'dietic_patients');
            $counts[] = $count;
        }

        return [
            'labels' => $months,
            'data'   => $counts,
        ];
    }

    /**
     * Get consultation types chart data
     *
     * @return array
     */
    private function get_consultation_types_chart_data()
    {
        $this->db->select('consultation_type, COUNT(*) as count');
        $this->db->group_by('consultation_type');

        if (!is_admin()) {
            $this->db->where('dietitian_id', get_staff_user_id());
        }

        $results = $this->db->get(db_prefix() . 'dietic_consultations')->result();

        $labels = [];
        $data = [];

        foreach ($results as $result) {
            $labels[] = ucfirst(str_replace('_', ' ', $result->consultation_type));
            $data[] = $result->count;
        }

        return [
            'labels' => $labels,
            'data'   => $data,
        ];
    }

    /**
     * Export data to CSV
     */
    public function export($type = 'patients')
    {
        if (!dietetic_has_permission('view')) {
            access_denied('dietetic');
        }

        $filename = 'dietetic_' . $type . '_' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        switch ($type) {
            case 'patients':
                $this->export_patients($output);
                break;
            case 'consultations':
                $this->export_consultations($output);
                break;
            case 'programs':
                $this->export_programs($output);
                break;
        }

        fclose($output);
        exit;
    }

    /**
     * Export patients to CSV
     *
     * @param resource $output
     */
    private function export_patients($output)
    {
        // Headers
        fputcsv($output, [
            'ID',
            'Client Name',
            'Dietitian',
            'Status',
            'Gender',
            'Birth Date',
            'Phone',
            'Email',
            'Initial Weight',
            'Target Weight',
            'Height',
            'Activity Level',
            'Created At',
        ]);

        // Data
        $patients = $this->dietetic_patients_model->get_all();

        foreach ($patients as $patient) {
            fputcsv($output, [
                $patient->id,
                $patient->client_name,
                $patient->dietitian_name,
                $patient->status,
                $patient->gender,
                $patient->birth_date,
                $patient->phone,
                $patient->email,
                $patient->initial_weight,
                $patient->target_weight,
                $patient->height,
                $patient->activity_level,
                $patient->created_at,
            ]);
        }
    }

    /**
     * Export consultations to CSV
     *
     * @param resource $output
     */
    private function export_consultations($output)
    {
        // Headers
        fputcsv($output, [
            'ID',
            'Client Name',
            'Dietitian',
            'Date',
            'Type',
            'Status',
            'Duration',
            'Satisfaction',
            'Created At',
        ]);

        // Data
        $consultations = $this->dietetic_consultations_model->get_all();

        foreach ($consultations as $consultation) {
            fputcsv($output, [
                $consultation->id,
                $consultation->client_name,
                $consultation->dietitian_name,
                $consultation->consultation_date,
                $consultation->consultation_type,
                $consultation->status,
                $consultation->duration,
                $consultation->satisfaction_score,
                $consultation->created_at,
            ]);
        }
    }

    /**
     * Export programs to CSV
     *
     * @param resource $output
     */
    private function export_programs($output)
    {
        // Headers
        fputcsv($output, [
            'ID',
            'Client Name',
            'Program Name',
            'Status',
            'Start Date',
            'End Date',
            'Daily Calories',
            'Daily Protein',
            'Daily Carbs',
            'Daily Fats',
            'Created At',
        ]);

        // Data
        $programs = $this->dietetic_programs_model->get_all();

        foreach ($programs as $program) {
            fputcsv($output, [
                $program->id,
                $program->client_name,
                $program->program_name,
                $program->status,
                $program->start_date,
                $program->end_date,
                $program->daily_calories,
                $program->daily_protein,
                $program->daily_carbs,
                $program->daily_fats,
                $program->created_at,
            ]);
        }
    }

    /**
     * Page de vérification des champs d'anamnèse
     * URL: admin/dietetic/check_anamnesis_fields
     */
    public function check_anamnesis_fields()
    {
        // Charger la vue de vérification
        $migration_path = dirname(__DIR__) . '/migrations/check_anamnesis_fields.php';

        if (!file_exists($migration_path)) {
            show_error('Fichier de migration non trouvé : ' . $migration_path);
            return;
        }

        include $migration_path;
    }

    /**
     * Page d'application de la migration d'anamnèse
     * URL: admin/dietetic/apply_anamnesis_migration
     */
    public function apply_anamnesis_migration()
    {
        // Vérifier les permissions admin
        if (!is_admin()) {
            access_denied('Migration - Administrateur requis');
            return;
        }

        // Charger la vue d'application de migration
        $migration_path = dirname(__DIR__) . '/migrations/apply_anamnesis_migration.php';

        if (!file_exists($migration_path)) {
            show_error('Fichier de migration non trouvé : ' . $migration_path);
            return;
        }

        include $migration_path;
    }

    /**
     * Page de correction des champs manquants
     * URL: admin/dietetic/fix_missing_anamnesis_fields
     */
    public function fix_missing_anamnesis_fields()
    {
        // Vérifier les permissions admin
        if (!is_admin()) {
            access_denied('Correction - Administrateur requis');
            return;
        }

        // Charger la vue de correction
        $fix_path = dirname(__DIR__) . '/migrations/fix_missing_fields.php';

        if (!file_exists($fix_path)) {
            show_error('Fichier de correction non trouvé : ' . $fix_path);
            return;
        }

        include $fix_path;
    }

    /**
     * Page de diagnostic des champs du formulaire
     * URL: admin/dietetic/check_form_fields
     */
    public function check_form_fields()
    {
        // Vérifier les permissions admin
        if (!is_admin()) {
            access_denied('Diagnostic - Administrateur requis');
            return;
        }

        // Charger la vue de diagnostic
        $diagnostic_path = dirname(__DIR__) . '/migrations/check_form_fields.php';

        if (!file_exists($diagnostic_path)) {
            show_error('Fichier de diagnostic non trouvé : ' . $diagnostic_path);
            return;
        }

        include $diagnostic_path;
    }

    /**
     * Liste simple des colonnes de la table patients
     * URL: admin/dietetic/list_db_columns
     */
    public function list_db_columns()
    {
        // Vérifier les permissions admin
        if (!is_admin()) {
            access_denied('Liste colonnes - Administrateur requis');
            return;
        }

        // Charger la vue
        $list_path = dirname(__DIR__) . '/migrations/list_db_columns.php';

        if (!file_exists($list_path)) {
            show_error('Fichier non trouvé : ' . $list_path);
            return;
        }

        include $list_path;
    }

    /**
     * Diagnostic rate limiting
     * URL: admin/dietetic/diagnose_rate_limit
     */
    public function diagnose_rate_limit()
    {
        // Vérifier les permissions admin
        if (!is_admin()) {
            access_denied('Diagnostic - Administrateur requis');
            return;
        }

        // Charger la vue de diagnostic
        $diag_path = dirname(__DIR__) . '/migrations/diagnose_rate_limit.php';

        if (!file_exists($diag_path)) {
            show_error('Fichier de diagnostic non trouvé : ' . $diag_path);
            return;
        }

        include $diag_path;
    }

    /**
     * Migration: Création de la table de suivi quotidien
     * URL: admin/dietetic/create_daily_tracking_table
     */
    public function create_daily_tracking_table()
    {
        // Vérifier les permissions admin
        if (!is_admin()) {
            access_denied('Migration - Administrateur requis');
            return;
        }

        // Charger le script de migration
        $migration_path = dirname(__DIR__) . '/migrations/create_daily_tracking_table.php';

        if (!file_exists($migration_path)) {
            show_error('Fichier de migration non trouvé : ' . $migration_path);
            return;
        }

        include $migration_path;
    }

    /**
     * Migrations manager - Display available migrations
     */
    public function migrations()
    {
        // Check admin permission
        if (!is_admin()) {
            access_denied('Migrations - Administrateur requis');
            return;
        }

        // Handle POST - Apply migration
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $migration_file = $this->input->post('migration_file');

            if (!$migration_file) {
                set_alert('danger', 'Migration non spécifiée');
                redirect(admin_url('dietetic/migrations'));
                return;
            }

            // Security check - only allow php files
            if (pathinfo($migration_file, PATHINFO_EXTENSION) !== 'php') {
                set_alert('danger', 'Type de fichier invalide');
                redirect(admin_url('dietetic/migrations'));
                return;
            }

            $migration_path = dirname(__DIR__) . '/migrations/' . $migration_file;

            if (!file_exists($migration_path)) {
                set_alert('danger', 'Fichier de migration non trouvé');
                redirect(admin_url('dietetic/migrations'));
                return;
            }

            try {
                // Start output buffering to capture migration output
                ob_start();

                // Include and execute migration
                require_once($migration_path);

                // Get migration output
                $migration_output = ob_get_clean();

                // Get class name from file
                $migration_name = pathinfo($migration_file, PATHINFO_FILENAME);
                $class_name = 'Migration_' . $migration_name;

                // Try to execute class-based migration if class exists
                if (class_exists($class_name)) {
                    $migration = new $class_name();
                    $migration->up();
                }
                // Otherwise, the migration was executed directly (procedural style)

                // Record migration as applied
                $this->record_migration($migration_name);

                // If migration produced HTML output, display it directly
                if (!empty($migration_output)) {
                    echo $migration_output;
                    // Don't redirect, let user see the migration output
                    return;
                } else {
                    set_alert('success', 'Migration appliquée avec succès: ' . $migration_file);
                }

                log_activity('Migration appliquée: ' . $migration_file);

            } catch (Exception $e) {
                // Clean output buffer in case of error
                if (ob_get_level() > 0) {
                    ob_end_clean();
                }
                log_activity('Migration error: ' . $e->getMessage());
                set_alert('danger', 'Erreur lors de l\'application de la migration: ' . $e->getMessage());
            }

            redirect(admin_url('dietetic/migrations'));
            return;
        }

        // GET request - Display migrations list
        $data['title'] = 'Migrations de base de données';

        // Get all migration files
        $migrations_dir = dirname(__DIR__) . '/migrations/';
        $migration_files = [];

        if (is_dir($migrations_dir)) {
            $files = scandir($migrations_dir);
            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                    $migration_files[] = $file;
                }
            }
        }

        // Check which migrations have been applied
        $applied_migrations = $this->get_applied_migrations();

        $data['migrations'] = [];
        foreach ($migration_files as $file) {
            $migration_name = pathinfo($file, PATHINFO_FILENAME);
            $data['migrations'][] = [
                'file' => $file,
                'name' => $migration_name,
                'title' => $this->format_migration_title($migration_name),
                'applied' => in_array($migration_name, $applied_migrations),
                'date_applied' => $this->get_migration_date($migration_name)
            ];
        }

        $this->load->view('admin/migrations/list', $data);
    }

    /**
     * Apply a specific migration
     */
    public function apply_migration()
    {
        // Check admin permission
        if (!is_admin()) {
            echo json_encode(['success' => false, 'message' => 'Accès refusé']);
            return;
        }

        header('Content-Type: application/json');

        $migration_file = $this->input->post('migration_file');

        if (!$migration_file) {
            echo json_encode(['success' => false, 'message' => 'Migration non spécifiée']);
            return;
        }

        // Security check - only allow php files
        if (pathinfo($migration_file, PATHINFO_EXTENSION) !== 'php') {
            echo json_encode(['success' => false, 'message' => 'Type de fichier invalide']);
            return;
        }

        $migration_path = dirname(__DIR__) . '/migrations/' . $migration_file;

        if (!file_exists($migration_path)) {
            echo json_encode(['success' => false, 'message' => 'Fichier de migration non trouvé']);
            return;
        }

        try {
            // Include and execute migration
            require_once($migration_path);

            // Get class name from file
            $migration_name = pathinfo($migration_file, PATHINFO_FILENAME);
            $class_name = 'Migration_' . $migration_name;

            if (!class_exists($class_name)) {
                echo json_encode(['success' => false, 'message' => 'Classe de migration non trouvée: ' . $class_name]);
                return;
            }

            $migration = new $class_name();
            $migration->up();

            // Record migration as applied
            $this->record_migration($migration_name);

            echo json_encode([
                'success' => true,
                'message' => 'Migration appliquée avec succès'
            ]);

        } catch (Exception $e) {
            log_activity('Migration error: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get list of applied migrations
     */
    private function get_applied_migrations()
    {
        // Create migrations table if it doesn't exist
        $this->create_migrations_table();

        $this->db->select('migration_name');
        $query = $this->db->get(db_prefix() . 'dietetic_migrations');

        $migrations = [];
        foreach ($query->result() as $row) {
            $migrations[] = $row->migration_name;
        }

        return $migrations;
    }

    /**
     * Get migration application date
     */
    private function get_migration_date($migration_name)
    {
        $this->db->where('migration_name', $migration_name);
        $query = $this->db->get(db_prefix() . 'dietetic_migrations');

        if ($query->num_rows() > 0) {
            return $query->row()->applied_at;
        }

        return null;
    }

    /**
     * Record migration as applied
     */
    private function record_migration($migration_name)
    {
        $this->db->insert(db_prefix() . 'dietetic_migrations', [
            'migration_name' => $migration_name,
            'applied_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Create migrations tracking table
     */
    private function create_migrations_table()
    {
        $table_name = db_prefix() . 'dietetic_migrations';

        if (!$this->db->table_exists($table_name)) {
            $this->db->query("
                CREATE TABLE `{$table_name}` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `migration_name` varchar(255) NOT NULL,
                    `applied_at` datetime NOT NULL,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `migration_name` (`migration_name`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ");
        }
    }

    /**
     * Format migration name for display
     */
    private function format_migration_title($name)
    {
        // Convert snake_case to Title Case
        $name = str_replace('_', ' ', $name);
        return ucwords($name);
    }

    /**
     * Apply PayPal Session Fix Migration
     * Page admin pour appliquer la migration de correction du bug de session PayPal
     */
    public function apply_paypal_fix()
    {
        // Seuls les admins peuvent accéder
        if (!is_admin()) {
            access_denied('dietetic');
        }

        $data['title'] = 'Migration PayPal Session Fix';

        // Vérifier si la table existe déjà
        $table_exists = $this->db->table_exists(db_prefix() . 'dietic_payment_tokens');
        $data['table_exists'] = $table_exists;

        // Si la table existe, récupérer des stats
        if ($table_exists) {
            // Nombre total de tokens
            $data['total_tokens'] = $this->db->count_all(db_prefix() . 'dietic_payment_tokens');

            // Tokens par statut
            $this->db->select('status, COUNT(*) as count');
            $this->db->from(db_prefix() . 'dietic_payment_tokens');
            $this->db->group_by('status');
            $data['tokens_by_status'] = $this->db->get()->result();

            // Derniers tokens
            $this->db->select('*');
            $this->db->from(db_prefix() . 'dietic_payment_tokens');
            $this->db->order_by('created_at', 'DESC');
            $this->db->limit(10);
            $data['recent_tokens'] = $this->db->get()->result();
        }

        // Traiter la soumission du formulaire (application de la migration)
        if ($this->input->post('apply_migration')) {
            $this->apply_migration_sql();
            return;
        }

        $this->load->view('admin/migrations/paypal_fix', $data);
    }

    /**
     * Execute PayPal fix migration
     * Méthode privée pour exécuter la migration SQL
     */
    private function apply_migration_sql()
    {
        try {
            // Charger le fichier SQL
            $migration_file = DIETETIC_MODULE_PATH . 'migrations/fix_paypal_session_issue.sql';

            if (!file_exists($migration_file)) {
                set_alert('danger', 'Fichier de migration introuvable: ' . $migration_file);
                redirect(admin_url('dietetic/apply_paypal_fix'));
                return;
            }

            // Lire le contenu SQL
            $sql_content = file_get_contents($migration_file);

            // Remplacer les préfixes
            $sql_content = str_replace('tbldietic_', db_prefix() . 'dietic_', $sql_content);

            // Nettoyer les commentaires SQL
            $sql_content = preg_replace('/^--.*$/m', '', $sql_content);
            $sql_content = preg_replace('/\/\*.*?\*\//s', '', $sql_content);

            // Séparer les instructions SQL
            $statements = array_filter(
                array_map('trim', explode(';', $sql_content)),
                function($stmt) {
                    return !empty($stmt) && strlen($stmt) > 10;
                }
            );

            $success_count = 0;
            $error_count = 0;
            $errors = [];

            // Exécuter chaque instruction
            foreach ($statements as $statement) {
                try {
                    $this->db->query($statement);
                    $success_count++;
                } catch (Exception $e) {
                    $error_msg = $e->getMessage();

                    // Ignorer les erreurs "already exists"
                    if (
                        strpos($error_msg, 'already exists') === false &&
                        strpos($error_msg, 'Duplicate') === false
                    ) {
                        $error_count++;
                        $errors[] = substr($error_msg, 0, 200);
                    } else {
                        $success_count++; // Compter comme succès si déjà existe
                    }
                }
            }

            if ($error_count === 0) {
                log_activity('Migration PayPal Session Fix appliquée avec succès');
                set_alert('success', sprintf(
                    'Migration appliquée avec succès! %d instructions SQL exécutées.',
                    $success_count
                ));
            } else {
                log_activity('Migration PayPal Session Fix terminée avec erreurs: ' . json_encode($errors));
                set_alert('warning', sprintf(
                    'Migration terminée avec %d erreurs sur %d instructions. Erreurs: %s',
                    $error_count,
                    $success_count + $error_count,
                    implode(', ', array_slice($errors, 0, 3))
                ));
            }

        } catch (Exception $e) {
            log_activity('Erreur lors de la migration PayPal Session Fix: ' . $e->getMessage());
            set_alert('danger', 'Erreur lors de l\'application de la migration: ' . $e->getMessage());
        }

        redirect(admin_url('dietetic/apply_paypal_fix'));
    }
}
