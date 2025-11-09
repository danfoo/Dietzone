<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Dietetic Security Library
 *
 * Centralized security and access control for the Dietetic module
 * Ensures that dietitians only see their own patients and related data
 */
class Dietetic_security
{
    private $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->helper('dietetic/dietetic');
    }

    // ==================== PATIENT ACCESS ====================

    /**
     * Check if current user can access a patient
     * Throws access_denied() if not allowed
     *
     * @param int $patient_id
     * @param bool $redirect If true, redirects instead of showing 403
     * @return void
     */
    public function require_patient_access($patient_id, $redirect = false)
    {
        if (!$this->can_access_patient($patient_id)) {
            $this->log_unauthorized_attempt('patient', $patient_id);

            if ($redirect) {
                set_alert('danger', 'Vous n\'avez pas accès à ce patient');
                redirect(admin_url('dietetic/patients'));
            } else {
                access_denied('dietetic_patient');
            }
        }
    }

    /**
     * Check if current user can access a patient (boolean check)
     *
     * @param int $patient_id
     * @return bool
     */
    public function can_access_patient($patient_id)
    {
        // Admins can access all patients
        if (is_admin()) {
            return true;
        }

        $staff_id = get_staff_user_id();
        if (!$staff_id) {
            return false;
        }

        // Check if patient is assigned to this dietitian
        $this->CI->load->model('dietetic/dietetic_patient_dietitians_model');
        return $this->CI->dietetic_patient_dietitians_model->has_access($patient_id, $staff_id);
    }

    /**
     * Get list of patient IDs accessible to current user
     *
     * @return array|null Array of IDs or null for admin (= all patients)
     */
    public function get_accessible_patient_ids()
    {
        // Admins see all
        if (is_admin()) {
            return null;
        }

        $staff_id = get_staff_user_id();
        if (!$staff_id) {
            return [];
        }

        $this->CI->load->model('dietetic/dietetic_patient_dietitians_model');
        $assignments = $this->CI->dietetic_patient_dietitians_model->get_dietitian_patients($staff_id, 'active');

        $patient_ids = [];
        foreach ($assignments as $assignment) {
            $patient_ids[] = $assignment->patient_id;
        }

        return $patient_ids;
    }

    // ==================== PROGRAM ACCESS ====================

    /**
     * Check if current user can access a program
     *
     * @param int $program_id
     * @param bool $redirect
     * @return void
     */
    public function require_program_access($program_id, $redirect = false)
    {
        if (!$this->can_access_program($program_id)) {
            $this->log_unauthorized_attempt('program', $program_id);

            if ($redirect) {
                set_alert('danger', 'Vous n\'avez pas accès à ce programme');
                redirect(admin_url('dietetic/programs'));
            } else {
                access_denied('dietetic_program');
            }
        }
    }

    /**
     * Check if current user can access a program
     *
     * @param int $program_id
     * @return bool
     */
    public function can_access_program($program_id)
    {
        if (is_admin()) {
            return true;
        }

        // Load program and check patient access
        $this->CI->load->model('dietetic/dietetic_programs_model');
        $program = $this->CI->dietetic_programs_model->get($program_id);

        if (!$program) {
            return false;
        }

        return $this->can_access_patient($program->patient_id);
    }

    // ==================== CONSULTATION ACCESS ====================

    /**
     * Check if current user can access a consultation
     *
     * @param int $consultation_id
     * @param bool $redirect
     * @return void
     */
    public function require_consultation_access($consultation_id, $redirect = false)
    {
        if (!$this->can_access_consultation($consultation_id)) {
            $this->log_unauthorized_attempt('consultation', $consultation_id);

            if ($redirect) {
                set_alert('danger', 'Vous n\'avez pas accès à cette consultation');
                redirect(admin_url('dietetic/consultations'));
            } else {
                access_denied('dietetic_consultation');
            }
        }
    }

    /**
     * Check if current user can access a consultation
     *
     * @param int $consultation_id
     * @return bool
     */
    public function can_access_consultation($consultation_id)
    {
        if (is_admin()) {
            return true;
        }

        // Load consultation and check patient access
        $this->CI->load->model('dietetic/dietetic_consultations_model');
        $consultation = $this->CI->dietetic_consultations_model->get($consultation_id);

        if (!$consultation) {
            return false;
        }

        return $this->can_access_patient($consultation->patient_id);
    }

    // ==================== FOOD SURVEY ACCESS ====================

    /**
     * Check if current user can access a food survey
     *
     * @param int $survey_id
     * @param bool $redirect
     * @return void
     */
    public function require_survey_access($survey_id, $redirect = false)
    {
        if (!$this->can_access_survey($survey_id)) {
            $this->log_unauthorized_attempt('food_survey', $survey_id);

            if ($redirect) {
                set_alert('danger', 'Vous n\'avez pas accès à cette enquête');
                redirect(admin_url('dietetic/food_surveys'));
            } else {
                access_denied('dietetic_food_survey');
            }
        }
    }

    /**
     * Check if current user can access a food survey
     *
     * @param int $survey_id
     * @return bool
     */
    public function can_access_survey($survey_id)
    {
        if (is_admin()) {
            return true;
        }

        // Check if food surveys table exists
        if (!$this->CI->db->table_exists(db_prefix() . 'dietic_food_surveys')) {
            return false;
        }

        // Load survey and check patient access
        $this->CI->load->model('dietetic/dietetic_food_surveys_model');
        $survey = $this->CI->dietetic_food_surveys_model->get($survey_id);

        if (!$survey) {
            return false;
        }

        return $this->can_access_patient($survey->patient_id);
    }

    // ==================== MEASUREMENT ACCESS ====================

    /**
     * Check if current user can access a measurement
     *
     * @param int $measurement_id
     * @param bool $redirect
     * @return void
     */
    public function require_measurement_access($measurement_id, $redirect = false)
    {
        if (!$this->can_access_measurement($measurement_id)) {
            $this->log_unauthorized_attempt('measurement', $measurement_id);

            if ($redirect) {
                set_alert('danger', 'Vous n\'avez pas accès à cette mesure');
                redirect(admin_url('dietetic/measurements'));
            } else {
                access_denied('dietetic_measurement');
            }
        }
    }

    /**
     * Check if current user can access a measurement
     *
     * @param int $measurement_id
     * @return bool
     */
    public function can_access_measurement($measurement_id)
    {
        if (is_admin()) {
            return true;
        }

        // Load measurement and check patient access
        $this->CI->load->model('dietetic/dietetic_measurements_model');
        $measurement = $this->CI->dietetic_measurements_model->get($measurement_id);

        if (!$measurement) {
            return false;
        }

        return $this->can_access_patient($measurement->patient_id);
    }

    // ==================== PERMISSION CHECKS ====================

    /**
     * Check if user has a specific dietetic permission
     *
     * @param string $capability (view, create, edit, delete, settings, etc.)
     * @return bool
     */
    public function has_permission($capability = 'view')
    {
        return has_permission('dietetic', '', $capability);
    }

    /**
     * Require a specific permission or deny access
     *
     * @param string $capability
     * @return void
     */
    public function require_permission($capability = 'view')
    {
        if (!$this->has_permission($capability)) {
            access_denied('dietetic');
        }
    }

    /**
     * Check if user can manage dietitian assignments
     * Only admins can assign/remove dietitians
     *
     * @return bool
     */
    public function can_manage_assignments()
    {
        return is_admin();
    }

    /**
     * Require admin access or deny
     *
     * @param bool $redirect
     * @return void
     */
    public function require_admin($redirect = false)
    {
        if (!is_admin()) {
            if ($redirect) {
                set_alert('danger', 'Cette action nécessite des droits administrateur');
                redirect(admin_url('dietetic'));
            } else {
                access_denied('admin');
            }
        }
    }

    // ==================== SQL FILTERS ====================

    /**
     * Apply dietitian filter to a database query
     * Filters results to only show patients assigned to current dietitian
     *
     * @param object $db CI database object
     * @param string $patient_table_alias Alias for patients table (default: 'p')
     * @param string $join_alias Alias for patient_dietitians join table (default: 'pd')
     * @return void
     */
    public function apply_patient_filter(&$db, $patient_table_alias = 'p', $join_alias = 'pd')
    {
        // Admins see all
        if (is_admin()) {
            return;
        }

        $staff_id = get_staff_user_id();

        if ($staff_id) {
            // Join with patient_dietitians and filter
            $db->join(
                db_prefix() . 'dietic_patient_dietitians ' . $join_alias,
                $join_alias . '.patient_id = ' . $patient_table_alias . '.id',
                'inner'
            );
            $db->where($join_alias . '.dietitian_id', $staff_id);
            $db->where($join_alias . '.status', 'active');
        } else {
            // No staff = no access
            $db->where('1', '0'); // Always false
        }
    }

    /**
     * Filter results by accessible patient IDs using WHERE IN
     *
     * @param object $db CI database object
     * @param string $patient_id_column Column name (default: 'patient_id')
     * @return void
     */
    public function filter_by_accessible_patients(&$db, $patient_id_column = 'patient_id')
    {
        $patient_ids = $this->get_accessible_patient_ids();

        if ($patient_ids === null) {
            // Admin - no filter
            return;
        }

        if (empty($patient_ids)) {
            // No patients - return nothing
            $db->where('1', '0');
        } else {
            // Filter by patient IDs
            $db->where_in($patient_id_column, $patient_ids);
        }
    }

    // ==================== LOGGING ====================

    /**
     * Log unauthorized access attempt
     *
     * @param string $entity_type (patient, program, consultation, etc.)
     * @param int $entity_id
     * @return void
     */
    private function log_unauthorized_attempt($entity_type, $entity_id)
    {
        $staff_id = get_staff_user_id();
        $staff_name = $staff_id ? get_staff_full_name($staff_id) : 'Unknown';

        log_activity(sprintf(
            'Unauthorized access attempt: %s tried to access %s ID %d',
            $staff_name,
            $entity_type,
            $entity_id
        ));
    }

    // ==================== HELPERS ====================

    /**
     * Get current staff user ID
     *
     * @return int|null
     */
    public function get_current_staff_id()
    {
        return get_staff_user_id();
    }

    /**
     * Check if current user is admin
     *
     * @return bool
     */
    public function is_admin()
    {
        return is_admin();
    }

    /**
     * Get count of accessible patients for current user
     *
     * @return int
     */
    public function count_accessible_patients()
    {
        $patient_ids = $this->get_accessible_patient_ids();

        if ($patient_ids === null) {
            // Admin - count all
            $this->CI->load->model('dietetic/dietetic_patients_model');
            return $this->CI->dietetic_patients_model->count_all();
        }

        return count($patient_ids);
    }
}
