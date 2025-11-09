<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Dietetic Module Helper Functions
 */

/**
 * Calculate BMI (Body Mass Index)
 *
 * @param float $weight Weight in kg
 * @param float $height Height in cm
 * @return float|null BMI value or null if invalid
 */
function dietetic_calculate_bmi($weight, $height)
{
    if (empty($weight) || empty($height) || $height <= 0) {
        return null;
    }

    // Convert height from cm to meters
    $height_m = $height / 100;

    return round($weight / ($height_m * $height_m), 2);
}

/**
 * Get BMI category
 *
 * @param float $bmi
 * @return string Category name
 */
function dietetic_get_bmi_category($bmi)
{
    if ($bmi < 18.5) {
        return _l('dietetic_bmi_underweight');
    } elseif ($bmi < 25) {
        return _l('dietetic_bmi_normal');
    } elseif ($bmi < 30) {
        return _l('dietetic_bmi_overweight');
    } else {
        return _l('dietetic_bmi_obese');
    }
}

/**
 * Calculate daily calorie needs using Harris-Benedict equation
 *
 * @param string $gender 'male' or 'female'
 * @param float $weight Weight in kg
 * @param float $height Height in cm
 * @param int $age Age in years
 * @param string $activity_level Activity level
 * @return int Estimated daily calories
 */
function dietetic_calculate_daily_calories($gender, $weight, $height, $age, $activity_level = 'moderate')
{
    // Calculate Basal Metabolic Rate (BMR)
    if ($gender == 'male') {
        $bmr = 88.362 + (13.397 * $weight) + (4.799 * $height) - (5.677 * $age);
    } else {
        $bmr = 447.593 + (9.247 * $weight) + (3.098 * $height) - (4.330 * $age);
    }

    // Activity multipliers
    $activity_multipliers = [
        'sedentary'    => 1.2,   // Little or no exercise
        'light'        => 1.375, // Light exercise 1-3 days/week
        'moderate'     => 1.55,  // Moderate exercise 3-5 days/week
        'active'       => 1.725, // Hard exercise 6-7 days/week
        'very_active'  => 1.9,   // Very hard exercise & physical job
    ];

    $multiplier = isset($activity_multipliers[$activity_level]) ? $activity_multipliers[$activity_level] : 1.55;

    return round($bmr * $multiplier);
}

/**
 * Format nutritional value for display
 *
 * @param float $value
 * @param string $unit
 * @param int $decimals
 * @return string
 */
function dietetic_format_nutrition($value, $unit = 'g', $decimals = 1)
{
    if (is_null($value)) {
        return '-';
    }

    return number_format($value, $decimals) . ' ' . $unit;
}

/**
 * Get patient status badge HTML
 *
 * @param string $status
 * @return string
 */
function dietetic_patient_status_badge($status)
{
    $badges = [
        'active'   => '<span class="label label-success">' . _l('dietetic_status_active') . '</span>',
        'inactive' => '<span class="label label-warning">' . _l('dietetic_status_inactive') . '</span>',
        'archived' => '<span class="label label-default">' . _l('dietetic_status_archived') . '</span>',
    ];

    return isset($badges[$status]) ? $badges[$status] : '<span class="label label-default">' . $status . '</span>';
}

/**
 * Get consultation status badge HTML
 *
 * @param string $status
 * @return string
 */
function dietetic_consultation_status_badge($status)
{
    $badges = [
        'scheduled' => '<span class="label label-info">' . _l('dietetic_consultation_scheduled') . '</span>',
        'completed' => '<span class="label label-success">' . _l('dietetic_consultation_completed') . '</span>',
        'cancelled' => '<span class="label label-danger">' . _l('dietetic_consultation_cancelled') . '</span>',
        'no_show'   => '<span class="label label-warning">' . _l('dietetic_consultation_no_show') . '</span>',
    ];

    return isset($badges[$status]) ? $badges[$status] : '<span class="label label-default">' . $status . '</span>';
}

/**
 * Get program status badge HTML
 *
 * @param string $status
 * @return string
 */
function dietetic_program_status_badge($status)
{
    $badges = [
        'active'    => '<span class="label label-success">' . _l('dietetic_program_active') . '</span>',
        'completed' => '<span class="label label-primary">' . _l('dietetic_program_completed') . '</span>',
        'cancelled' => '<span class="label label-danger">' . _l('dietetic_program_cancelled') . '</span>',
    ];

    return isset($badges[$status]) ? $badges[$status] : '<span class="label label-default">' . $status . '</span>';
}

/**
 * Get activity level options
 *
 * @return array
 */
function dietetic_get_activity_levels()
{
    return [
        'sedentary'   => _l('dietetic_activity_sedentary'),
        'light'       => _l('dietetic_activity_light'),
        'moderate'    => _l('dietetic_activity_moderate'),
        'active'      => _l('dietetic_activity_active'),
        'very_active' => _l('dietetic_activity_very_active'),
    ];
}

/**
 * Get meal type options
 *
 * @return array
 */
function dietetic_get_meal_types()
{
    return [
        'breakfast'      => _l('dietetic_meal_breakfast'),
        'snack_am'       => _l('dietetic_meal_snack_am'),
        'lunch'          => _l('dietetic_meal_lunch'),
        'snack_pm'       => _l('dietetic_meal_snack_pm'),
        'dinner'         => _l('dietetic_meal_dinner'),
        'snack_evening'  => _l('dietetic_meal_snack_evening'),
    ];
}

/**
 * Get food category options
 *
 * @return array
 */
function dietetic_get_food_categories()
{
    return [
        'vegetables'  => _l('dietetic_category_vegetables'),
        'fruits'      => _l('dietetic_category_fruits'),
        'proteins'    => _l('dietetic_category_proteins'),
        'grains'      => _l('dietetic_category_grains'),
        'dairy'       => _l('dietetic_category_dairy'),
        'fats'        => _l('dietetic_category_fats'),
        'beverages'   => _l('dietetic_category_beverages'),
        'snacks'      => _l('dietetic_category_snacks'),
        'condiments'  => _l('dietetic_category_condiments'),
        'other'       => _l('dietetic_category_other'),
    ];
}

/**
 * Get localized food name based on system language
 *
 * @param object $food Food object with food_name and food_name_fr fields
 * @return string Localized food name
 */
function dietetic_get_food_name($food)
{
    if (!$food) {
        return '';
    }

    // Get current language
    $CI = &get_instance();
    $language = $CI->config->item('language');

    // Default to French if food_name_fr exists, otherwise use food_name
    if (!empty($food->food_name_fr)) {
        return $food->food_name_fr;
    }

    return $food->food_name;
}

/**
 * Get day of week name
 *
 * @param int $day 1-7 (Monday-Sunday)
 * @return string
 */
function dietetic_get_day_name($day)
{
    $days = [
        1 => _l('dietetic_monday'),
        2 => _l('dietetic_tuesday'),
        3 => _l('dietetic_wednesday'),
        4 => _l('dietetic_thursday'),
        5 => _l('dietetic_friday'),
        6 => _l('dietetic_saturday'),
        7 => _l('dietetic_sunday'),
    ];

    return isset($days[$day]) ? $days[$day] : '';
}

/**
 * Get module setting value
 *
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function dietetic_get_option($key, $default = null)
{
    $CI = &get_instance();
    $CI->db->where('setting_key', $key);
    $setting = $CI->db->get(db_prefix() . 'dietic_settings')->row();

    if ($setting) {
        if ($setting->setting_type == 'boolean') {
            return (bool)$setting->setting_value;
        } elseif ($setting->setting_type == 'number') {
            return (int)$setting->setting_value;
        } elseif ($setting->setting_type == 'json') {
            return json_decode($setting->setting_value, true);
        }
        return $setting->setting_value;
    }

    return $default;
}

/**
 * Update module setting
 *
 * @param string $key
 * @param mixed $value
 * @return bool
 */
function dietetic_update_option($key, $value)
{
    $CI = &get_instance();

    // Get setting type
    $CI->db->where('setting_key', $key);
    $setting = $CI->db->get(db_prefix() . 'dietic_settings')->row();

    if ($setting) {
        if ($setting->setting_type == 'json' && is_array($value)) {
            $value = json_encode($value);
        }

        $CI->db->where('setting_key', $key);
        return $CI->db->update(db_prefix() . 'dietic_settings', [
            'setting_value' => $value,
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);
    }

    return false;
}

/**
 * Format date for display
 *
 * @param string $date
 * @param bool $include_time
 * @return string
 */
function dietetic_format_date($date, $include_time = false)
{
    if (empty($date) || $date == '0000-00-00' || $date == '0000-00-00 00:00:00') {
        return '-';
    }

    $format = get_option('dateformat');
    if ($include_time) {
        $format .= ' ' . get_option('timeformat');
    }

    return _dt($date, $format);
}

/**
 * Check if user has dietetic permission
 *
 * @param string $capability
 * @return bool
 */
function dietetic_has_permission($capability = 'view')
{
    return has_permission('dietetic', '', $capability);
}

/**
 * Get upload path for dietetic files
 *
 * @param string $subdir
 * @return string
 */
function dietetic_upload_path($subdir = '')
{
    $path = DIETETIC_MODULE_UPLOAD_FOLDER;
    if (!empty($subdir)) {
        $path .= '/' . trim($subdir, '/');
    }
    return $path;
}

/**
 * Handle file upload
 *
 * @param array $file $_FILES array element
 * @param string $subdir Subdirectory
 * @param array $allowed_types Allowed file types
 * @return array ['success' => bool, 'file_path' => string, 'error' => string]
 */
function dietetic_handle_file_upload($file, $subdir = 'documents', $allowed_types = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'])
{
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        return ['success' => false, 'error' => 'No file uploaded'];
    }

    $upload_path = dietetic_upload_path($subdir);

    if (!is_dir($upload_path)) {
        mkdir($upload_path, 0755, true);
    }

    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($file_extension, $allowed_types)) {
        return ['success' => false, 'error' => 'File type not allowed'];
    }

    $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file['name']);
    $file_path = $upload_path . '/' . $filename;

    if (move_uploaded_file($file['tmp_name'], $file_path)) {
        return [
            'success'   => true,
            'file_path' => $subdir . '/' . $filename,
            'filename'  => $filename,
        ];
    }

    return ['success' => false, 'error' => 'Failed to move uploaded file'];
}

/**
 * Notify dietitian about patient measurement update
 *
 * @param int $patient_id Patient ID
 * @param int $dietitian_id Dietitian staff ID
 * @param string $patient_name Patient name
 * @param float $weight New weight
 * @return bool
 */
function dietetic_notify_measurement_added($patient_id, $dietitian_id, $patient_name, $weight)
{
    $CI = &get_instance();

    // Create notification link
    $link = admin_url('dietetic/patients/view/' . $patient_id);

    // Notification description
    $description = sprintf(
        'Le patient %s a enregistré une nouvelle mesure (Poids: %.1f kg)',
        $patient_name,
        $weight
    );

    // Use Perfex notification system if available
    if (function_exists('add_notification')) {
        $notification_data = [
            'description'     => $description,
            'touserid'        => $dietitian_id,
            'link'            => $link,
            'additional_data' => serialize([
                'patient_id' => $patient_id,
                'weight'     => $weight,
            ]),
        ];

        return add_notification($notification_data);
    }

    // Fallback: Log activity
    log_activity(sprintf(
        'Patient Measurement Added [Patient ID: %d, Dietitian ID: %d, Weight: %.1f kg]',
        $patient_id,
        $dietitian_id,
        $weight
    ));

    return true;
}

/**
 * Send SMS via LAM API
 *
 * @param string $phone
 * @param string $message
 * @return array ['success' => bool, 'message' => string]
 */
function dietetic_send_sms($phone, $message)
{
    $api_url = dietetic_get_option('lam_api_url');
    $api_key = dietetic_get_option('lam_api_key');
    $sender = dietetic_get_option('lam_api_sender', 'Dietetic');

    if (empty($api_url) || empty($api_key)) {
        return ['success' => false, 'message' => 'LAM SMS API not configured'];
    }

    // Clean phone number
    $phone = preg_replace('/[^0-9+]/', '', $phone);

    $data = [
        'api_key' => $api_key,
        'sender'  => $sender,
        'to'      => $phone,
        'message' => $message,
    ];

    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code == 200) {
        return ['success' => true, 'message' => 'SMS sent successfully'];
    }

    return ['success' => false, 'message' => 'Failed to send SMS: ' . $response];
}

/**
 * Check if current user is admin
 * Note: Only real admins should bypass patient filtering and access restrictions
 *
 * @return bool
 */
function dietetic_is_admin()
{
    return is_admin();
}

/**
 * Get current staff user ID
 *
 * @return int|null
 */
function dietetic_get_staff_user_id()
{
    return get_staff_user_id();
}

/**
 * Check if a dietitian has access to a patient
 * Also allows patients to access their own data from the client portal
 *
 * @param int $patient_id
 * @param int $dietitian_id If null, uses current staff user
 * @return bool
 */
function dietetic_can_access_patient($patient_id, $dietitian_id = null)
{
    $CI = &get_instance();

    // Check if this is a client (patient) accessing their own data
    if (is_client_logged_in()) {
        $client_id = get_client_user_id();

        // Query database directly to avoid model conflicts
        $patient = $CI->db->get_where(db_prefix() . 'dietic_patients', ['id' => $patient_id])->row();

        if ($patient && isset($patient->client_id) && (int)$patient->client_id === (int)$client_id) {
            // Patient is accessing their own data
            return true;
        }
    }

    // Only super admin (user ID 1) can access all patients
    // Other admins must be assigned to the patient
    if (dietetic_is_admin() && dietetic_get_staff_user_id() == 1) {
        return true;
    }

    // Check staff access
    if ($dietitian_id === null) {
        $dietitian_id = dietetic_get_staff_user_id();
    }

    if (!$dietitian_id) {
        return false;
    }

    // Check if patient_dietitians table exists
    if (!$CI->db->table_exists(db_prefix() . 'dietic_patient_dietitians')) {
        // Fallback to old system - check dietitian_id in patients table
        $patient = $CI->db->get_where(db_prefix() . 'dietic_patients', ['id' => $patient_id])->row();
        return $patient && (int)$patient->dietitian_id === (int)$dietitian_id;
    }

    // Check access in patient_dietitians table first
    $CI->load->model('dietetic/dietetic_patient_dietitians_model');

    if ($CI->dietetic_patient_dietitians_model->has_access($patient_id, $dietitian_id)) {
        return true;
    }

    // Fallback: If no assignment found, check dietitian_id in patients table
    // This handles patients created before the many-to-many system or missing assignments
    $patient = $CI->db->get_where(db_prefix() . 'dietic_patients', ['id' => $patient_id])->row();
    return $patient && (int)$patient->dietitian_id === (int)$dietitian_id;
}

/**
 * Apply dietitian filter to database query if not admin
 * This filters patients to only show those assigned to current dietitian
 *
 * @param object $db CI database object
 * @param string $table_alias Alias for patient_dietitians table
 * @return void
 */
function dietetic_apply_dietitian_filter(&$db, $table_alias = 'pd')
{
    // Check if this is a client (patient) accessing their own data
    if (is_client_logged_in()) {
        $client_id = get_client_user_id();
        // Filter to show only their own patient records
        $db->where('p.client_id', $client_id);
        return;
    }

    $staff_id = dietetic_get_staff_user_id();

    // Only super admin (user ID 1) sees all patients
    // Other staff members (including admins) only see assigned patients
    if (dietetic_is_admin() && $staff_id == 1) {
        return; // Super admin sees all
    }

    if ($staff_id) {
        // Use LEFT JOIN to include patients without assignments
        // This handles both the new system (patient_dietitians) and old system (dietitian_id field)
        $db->join(db_prefix() . 'dietic_patient_dietitians ' . $table_alias,
                  $table_alias . '.patient_id = p.id AND ' . $table_alias . '.status = "active"', 'left');

        // Show patients where EITHER:
        // 1. Staff is assigned in patient_dietitians table (new system)
        // 2. Staff matches the dietitian_id field (old system / fallback)
        $db->group_start();
        $db->where($table_alias . '.dietitian_id', $staff_id);
        $db->or_where('p.dietitian_id', $staff_id);
        $db->group_end();
    } else {
        // No staff user and not a client = no access
        $db->where('1', '0'); // Always false
    }
}

/**
 * Get list of patient IDs accessible to current dietitian
 *
 * @param int $dietitian_id If null, uses current staff user
 * @return array Array of patient IDs
 */
function dietetic_get_accessible_patient_ids($dietitian_id = null)
{
    // Only super admin (user ID 1) sees all patients
    if (dietetic_is_admin() && dietetic_get_staff_user_id() == 1) {
        return null; // null means "all patients"
    }

    if ($dietitian_id === null) {
        $dietitian_id = dietetic_get_staff_user_id();
    }

    if (!$dietitian_id) {
        return []; // Empty array = no patients
    }

    $CI = &get_instance();

    // Check if patient_dietitians table exists
    if (!$CI->db->table_exists(db_prefix() . 'dietic_patient_dietitians')) {
        // Fallback to old system - query patients table directly
        $CI->db->select('id');
        $CI->db->where('dietitian_id', $dietitian_id);
        $results = $CI->db->get(db_prefix() . 'dietic_patients')->result();

        $patient_ids = [];
        foreach ($results as $row) {
            $patient_ids[] = $row->id;
        }
        return $patient_ids;
    }

    $CI->load->model('dietetic/dietetic_patient_dietitians_model');

    $assignments = $CI->dietetic_patient_dietitians_model->get_dietitian_patients($dietitian_id, 'active');
    $patient_ids = [];

    foreach ($assignments as $assignment) {
        $patient_ids[] = $assignment->patient_id;
    }

    return $patient_ids;
}

/**
 * Check if current user can manage patient assignments
 * Only admins can assign/remove dietitians to patients
 *
 * @return bool
 */
function dietetic_can_manage_assignments()
{
    return is_admin();
}

/**
 * Check if current staff has a specific granular permission
 * Examples: 'food_surveys', 'notifications_manage', 'reports_advanced'
 *
 * @param string $permission_key Permission key to check
 * @param int $staff_id If null, uses current staff user
 * @return bool
 */
function dietetic_has_feature_permission($permission_key, $staff_id = null)
{
    if ($staff_id === null) {
        $staff_id = dietetic_get_staff_user_id();
    }

    if (!$staff_id) {
        return false;
    }

    $CI = &get_instance();

    // Check if the SPECIFIC staff member (not current user) is an admin
    // Admins bypass all permission checks
    // Use get_where to avoid Query Builder conflicts with active queries
    $staff = $CI->db->get_where(db_prefix() . 'staff', ['staffid' => $staff_id])->row();

    if ($staff && $staff->admin == 1) {
        return true;
    }

    // Check if permissions table exists
    if (!$CI->db->table_exists(db_prefix() . 'dietic_staff_permissions')) {
        // Table doesn't exist yet - deny by default (secure)
        return false;
    }

    // Query permission - use get_where to avoid Query Builder conflicts
    $permission = $CI->db->get_where(db_prefix() . 'dietic_staff_permissions', [
        'staff_id' => $staff_id,
        'permission_key' => $permission_key
    ])->row();

    if ($permission) {
        return (bool)$permission->permission_value;
    }

    // No record found - check default setting
    $default_setting = dietetic_get_option('permissions_default_' . $permission_key, false);
    return (bool)$default_setting;
}

/**
 * Grant a specific permission to a staff member
 * Only admins can grant permissions
 *
 * @param int $staff_id Staff ID to grant permission to
 * @param string $permission_key Permission key
 * @param bool $enabled True to enable, false to disable
 * @param string $notes Optional notes
 * @return bool
 */
function dietetic_grant_permission($staff_id, $permission_key, $enabled = true, $notes = null)
{
    if (!is_admin()) {
        return false;
    }

    $CI = &get_instance();

    // Check if table exists
    if (!$CI->db->table_exists(db_prefix() . 'dietic_staff_permissions')) {
        return false;
    }

    // Check if permission already exists
    $CI->db->where('staff_id', $staff_id);
    $CI->db->where('permission_key', $permission_key);
    $existing = $CI->db->get(db_prefix() . 'dietic_staff_permissions')->row();

    $data = [
        'permission_value' => $enabled ? 1 : 0,
        'granted_by' => get_staff_user_id(),
        'updated_at' => date('Y-m-d H:i:s'),
    ];

    if ($notes !== null) {
        $data['notes'] = $notes;
    }

    if ($existing) {
        // Update existing
        $CI->db->where('id', $existing->id);
        return $CI->db->update(db_prefix() . 'dietic_staff_permissions', $data);
    } else {
        // Insert new
        $data['staff_id'] = $staff_id;
        $data['permission_key'] = $permission_key;
        $data['granted_at'] = date('Y-m-d H:i:s');
        return $CI->db->insert(db_prefix() . 'dietic_staff_permissions', $data);
    }
}

/**
 * Get all permissions for a staff member
 *
 * @param int $staff_id If null, uses current staff user
 * @return array Associative array of permission_key => enabled
 */
function dietetic_get_staff_permissions($staff_id = null)
{
    if ($staff_id === null) {
        $staff_id = dietetic_get_staff_user_id();
    }

    if (!$staff_id) {
        return [];
    }

    $CI = &get_instance();

    // Check if table exists
    if (!$CI->db->table_exists(db_prefix() . 'dietic_staff_permissions')) {
        return [];
    }

    $CI->db->where('staff_id', $staff_id);
    $permissions = $CI->db->get(db_prefix() . 'dietic_staff_permissions')->result();

    $result = [];
    foreach ($permissions as $perm) {
        $result[$perm->permission_key] = (bool)$perm->permission_value;
    }

    return $result;
}

/**
 * Get available permission keys and their descriptions
 *
 * @return array
 */
function dietetic_get_available_permissions()
{
    return [
        'food_surveys' => [
            'label' => 'Enquêtes Alimentaires',
            'description' => 'Accès au module des enquêtes alimentaires',
            'default' => false,
        ],
        'notifications_manage' => [
            'label' => 'Gestion des Notifications',
            'description' => 'Accès aux paramètres et gestion des notifications',
            'default' => false,
            'admin_only' => true, // This permission is reserved for admins
        ],
        'reports_advanced' => [
            'label' => 'Rapports Avancés',
            'description' => 'Accès aux rapports et statistiques avancés',
            'default' => false,
        ],
        'settings_module' => [
            'label' => 'Paramètres du Module',
            'description' => 'Accès aux paramètres généraux du module diététique',
            'default' => false,
            'admin_only' => true,
        ],
    ];
}
