<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Settings extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        // Only admins can access settings
        if (!is_admin()) {
            access_denied('dietetic');
        }
    }

    /**
     * Main settings page
     */
    public function index()
    {
        $data['title'] = 'Paramètres du Module Diététique';
        $this->load->view('admin/settings/index', $data);
    }

    /**
     * Patient booking migration page
     */
    public function patient_booking_migration()
    {
        $data['title'] = 'Migration : Système de Prise de Rendez-vous Patient';
        $this->load->view('admin/settings/patient_booking_migration', $data);
    }

    /**
     * Diagnostic page for appointment booking system
     */
    public function diagnostic_booking()
    {
        $data['title'] = 'Diagnostic - Système de Prise de Rendez-vous';

        // Check availability table
        $data['availability_table_exists'] = $this->db->table_exists(db_prefix() . 'dietic_dietitian_availability');
        $data['types_table_exists'] = $this->db->table_exists(db_prefix() . 'dietic_consultation_types');

        // Get consultation types
        if ($data['types_table_exists']) {
            $data['consultation_types'] = $this->db->get(db_prefix() . 'dietic_consultation_types')->result();
        } else {
            $data['consultation_types'] = [];
        }

        // Get staff members
        $this->db->select('staffid, CONCAT(firstname, " ", lastname) as name, email');
        $this->db->where('active', 1);
        $data['staff'] = $this->db->get(db_prefix() . 'staff')->result();

        // Get existing availabilities
        if ($data['availability_table_exists']) {
            $this->db->select('da.*, CONCAT(s.firstname, " ", s.lastname) as dietitian_name');
            $this->db->from(db_prefix() . 'dietic_dietitian_availability da');
            $this->db->join(db_prefix() . 'staff s', 's.staffid = da.dietitian_id');
            $this->db->order_by('da.dietitian_id, da.day_of_week, da.start_time');
            $data['availabilities'] = $this->db->get()->result();
        } else {
            $data['availabilities'] = [];
        }

        // Check patient-dietitian assignments
        $data['assignments_table_exists'] = $this->db->table_exists(db_prefix() . 'dietic_patient_dietitians');
        if ($data['assignments_table_exists']) {
            $data['assignments_count'] = $this->db->count_all(db_prefix() . 'dietic_patient_dietitians');
        } else {
            $data['assignments_count'] = 0;
        }

        // Check booked_by_patient field
        $columns = $this->db->list_fields(db_prefix() . 'dietic_consultations');
        $data['has_booking_field'] = in_array('booked_by_patient', $columns);

        $this->load->view('admin/settings/diagnostic_booking', $data);
    }

    /**
     * Create sample availability data for a dietitian
     */
    public function create_sample_availability()
    {
        if (!is_admin()) {
            ajax_access_denied();
        }

        header('Content-Type: application/json');

        $dietitian_id = $this->input->post('dietitian_id');

        if (!$dietitian_id) {
            echo json_encode(['success' => false, 'message' => 'ID diététicien requis']);
            return;
        }

        // Check if dietitian already has availabilities
        $existing = $this->db->where('dietitian_id', $dietitian_id)
            ->count_all_results(db_prefix() . 'dietic_dietitian_availability');

        if ($existing > 0) {
            echo json_encode(['success' => false, 'message' => 'Ce diététicien a déjà des disponibilités configurées']);
            return;
        }

        // Create sample schedule: Monday to Friday, 9-12 and 14-18
        $sample_availabilities = [
            ['day_of_week' => 1, 'start_time' => '09:00:00', 'end_time' => '12:00:00'], // Lundi matin
            ['day_of_week' => 1, 'start_time' => '14:00:00', 'end_time' => '18:00:00'], // Lundi après-midi
            ['day_of_week' => 2, 'start_time' => '09:00:00', 'end_time' => '12:00:00'], // Mardi matin
            ['day_of_week' => 2, 'start_time' => '14:00:00', 'end_time' => '18:00:00'], // Mardi après-midi
            ['day_of_week' => 3, 'start_time' => '09:00:00', 'end_time' => '12:00:00'], // Mercredi matin
            ['day_of_week' => 4, 'start_time' => '09:00:00', 'end_time' => '12:00:00'], // Jeudi matin
            ['day_of_week' => 4, 'start_time' => '14:00:00', 'end_time' => '18:00:00'], // Jeudi après-midi
            ['day_of_week' => 5, 'start_time' => '09:00:00', 'end_time' => '12:00:00'], // Vendredi matin
        ];

        $inserted = 0;
        foreach ($sample_availabilities as $avail) {
            $data = [
                'dietitian_id' => $dietitian_id,
                'day_of_week' => $avail['day_of_week'],
                'start_time' => $avail['start_time'],
                'end_time' => $avail['end_time'],
                'slot_duration' => 60,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ];

            if ($this->db->insert(db_prefix() . 'dietic_dietitian_availability', $data)) {
                $inserted++;
            }
        }

        if ($inserted > 0) {
            log_activity('Sample availability created for dietitian ID: ' . $dietitian_id);
            echo json_encode([
                'success' => true,
                'message' => "{$inserted} créneaux de disponibilité créés avec succès",
                'inserted' => $inserted
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la création des disponibilités']);
        }
    }

    /**
     * Run database migration for services & billing
     */
    public function run_migration()
    {
        if (!is_admin()) {
            ajax_access_denied();
        }

        $migration_file = DIETETIC_MODULE_PATH . 'migrations/add_services_subscriptions_billing.sql';

        if (!file_exists($migration_file)) {
            echo json_encode([
                'success' => false,
                'message' => 'Fichier de migration introuvable'
            ]);
            return;
        }

        // Load the migration SQL
        $migration_sql = file_get_contents($migration_file);

        // Replace table prefix
        $migration_sql = str_replace('tbldietic_', db_prefix() . 'dietic_', $migration_sql);

        // Remove comments
        $migration_sql = preg_replace('/^--.*$/m', '', $migration_sql);
        $migration_sql = preg_replace('/\/\*.*?\*\//s', '', $migration_sql);

        // Split into statements
        $statements = array_filter(array_map('trim', explode(';', $migration_sql)));

        $success_count = 0;
        $error_count = 0;
        $errors = [];
        $created_tables = [];

        foreach ($statements as $statement) {
            if (empty($statement) || strlen($statement) < 10) {
                continue;
            }

            try {
                $this->db->query($statement);
                $success_count++;

                // Extract table name for logging
                if (preg_match('/CREATE TABLE.*?`([^`]+)`/i', $statement, $matches)) {
                    $created_tables[] = $matches[1];
                }

            } catch (Exception $e) {
                $error_msg = $e->getMessage();

                // Ignore "already exists" errors
                if (
                    strpos($error_msg, 'already exists') === false &&
                    strpos($error_msg, 'Duplicate') === false &&
                    strpos($error_msg, 'duplicate key') === false
                ) {
                    $error_count++;
                    $errors[] = substr($error_msg, 0, 200);
                }
            }
        }

        if ($error_count === 0) {
            log_activity('Dietetic Services & Billing Migration Executed Successfully');

            echo json_encode([
                'success' => true,
                'message' => 'Migration exécutée avec succès!',
                'details' => [
                    'success_count' => $success_count,
                    'created_tables' => $created_tables
                ]
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Migration terminée avec des erreurs',
                'errors' => $errors,
                'success_count' => $success_count
            ]);
        }
    }

    /**
     * Check if migration has been run
     */
    public function check_migration()
    {
        if (!is_admin()) {
            ajax_access_denied();
        }

        // Check if service_plans table exists
        $table_exists = $this->db->table_exists(db_prefix() . 'dietic_service_plans');

        echo json_encode([
            'migration_run' => $table_exists,
            'message' => $table_exists
                ? 'Les tables sont déjà créées'
                : 'La migration doit être exécutée'
        ]);
    }

    /**
     * Run patient booking migration
     * Adds booked_by_patient field and indexes
     */
    public function run_patient_booking_migration()
    {
        if (!is_admin()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Non autorisé']);
            return;
        }

        header('Content-Type: application/json');

        try {
            $table_name = db_prefix() . 'dietic_consultations';

            $success_count = 0;
            $warnings = [];
            $errors = [];

            // 1. Add booked_by_patient column
            try {
                $sql = "ALTER TABLE `{$table_name}`
                        ADD COLUMN `booked_by_patient` TINYINT(1) DEFAULT 0
                        COMMENT 'Whether the consultation was booked by the patient (1) or by staff (0)'";
                $this->db->query($sql);
                $success_count++;
            } catch (Exception $e) {
                if (strpos($e->getMessage(), 'Duplicate column') !== false) {
                    $warnings[] = 'Colonne booked_by_patient existe déjà';
                } else {
                    $errors[] = 'Erreur colonne: ' . $e->getMessage();
                }
            }

            // 2. Modify status column comment
            try {
                $sql = "ALTER TABLE `{$table_name}`
                        MODIFY COLUMN `status` VARCHAR(20) DEFAULT 'scheduled'
                        COMMENT 'scheduled, pending, confirmed, completed, cancelled, no_show, rejected'";
                $this->db->query($sql);
                $success_count++;
            } catch (Exception $e) {
                $warnings[] = 'Modification status: ' . substr($e->getMessage(), 0, 100);
            }

            // 3. Add index on booked_by_patient
            try {
                $sql = "ALTER TABLE `{$table_name}`
                        ADD INDEX `idx_booked_by_patient` (`booked_by_patient`)";
                $this->db->query($sql);
                $success_count++;
            } catch (Exception $e) {
                if (strpos($e->getMessage(), 'Duplicate key') !== false) {
                    $warnings[] = 'Index idx_booked_by_patient existe déjà';
                } else {
                    $warnings[] = 'Index booked_by_patient: ' . substr($e->getMessage(), 0, 100);
                }
            }

            // 4. Add composite index
            try {
                $sql = "ALTER TABLE `{$table_name}`
                        ADD INDEX `idx_dietitian_status` (`dietitian_id`, `status`)";
                $this->db->query($sql);
                $success_count++;
            } catch (Exception $e) {
                if (strpos($e->getMessage(), 'Duplicate key') !== false) {
                    $warnings[] = 'Index idx_dietitian_status existe déjà';
                } else {
                    $warnings[] = 'Index dietitian_status: ' . substr($e->getMessage(), 0, 100);
                }
            }

            // Verify column was added
            $columns = $this->db->list_fields($table_name);
            $has_field = in_array('booked_by_patient', $columns);

            if ($has_field) {
                log_activity('Dietetic Patient Booking Migration Executed Successfully');

                echo json_encode([
                    'success' => true,
                    'message' => 'Migration exécutée avec succès!',
                    'details' => [
                        'success_count' => $success_count,
                        'field_added' => 'booked_by_patient',
                        'warnings' => $warnings,
                        'errors' => $errors
                    ]
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Le champ n\'a pas pu être ajouté',
                    'errors' => $errors,
                    'warnings' => $warnings,
                    'success_count' => $success_count
                ]);
            }

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Erreur générale: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Check if patient booking migration has been run
     */
    public function check_patient_booking_migration()
    {
        if (!is_admin()) {
            ajax_access_denied();
        }

        // Check if booked_by_patient column exists
        $table_name = db_prefix() . 'dietic_consultations';
        $columns = $this->db->list_fields($table_name);
        $column_exists = in_array('booked_by_patient', $columns);

        echo json_encode([
            'migration_run' => $column_exists,
            'message' => $column_exists
                ? 'Le champ booked_by_patient existe déjà'
                : 'La migration doit être exécutée'
        ]);
    }
}
