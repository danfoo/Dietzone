<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Dietetic Module Helper Functions
 */

// Charger la configuration du module
require_once(__DIR__ . '/../config.php');

// ==================== DEBUG & LOGGING ====================

/**
 * Log conditionnel pour le mode debug
 * N'enregistre que si DIETETIC_DEBUG est activé
 *
 * @param string $message Message à logger
 * @param string $type Type de log (info, warning, error, debug)
 * @return void
 */
function dietetic_debug_log($message, $type = 'debug')
{
    if (!defined('DIETETIC_DEBUG') || !DIETETIC_DEBUG) {
        return; // Debug désactivé, ne rien logger
    }

    // Préfixes selon le type
    $prefixes = [
        'debug' => '🔍 [DEBUG]',
        'info' => 'ℹ️ [INFO]',
        'warning' => '⚠️ [WARNING]',
        'error' => '❌ [ERROR]',
        'success' => '✅ [SUCCESS]'
    ];

    $prefix = isset($prefixes[$type]) ? $prefixes[$type] : '📝 [LOG]';

    log_activity($prefix . ' ' . $message);
}

/**
 * Log d'erreur critique (toujours enregistré, même en production)
 *
 * @param string $message Message d'erreur
 * @param array $context Contexte additionnel
 * @return void
 */
function dietetic_log_error($message, $context = [])
{
    $log_message = '❌ [DIETETIC ERROR] ' . $message;

    if (!empty($context)) {
        $log_message .= ' | Context: ' . json_encode($context);
    }

    log_activity($log_message);
}

// ==================== BMI & CALCULATIONS ====================

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
 * This function now checks the custom permissions table (tbldietic_staff_permissions)
 * which is managed via Diététique → Permissions Diététiciens
 *
 * @param string $capability
 * @return bool
 */
function dietetic_has_permission($capability = 'view')
{
    // Admins always have all permissions
    if (is_admin()) {
        return true;
    }

    // Use the custom permissions system (tbldietic_staff_permissions)
    // This checks the permissions set in: Diététique → Permissions Diététiciens
    return dietetic_has_feature_permission($capability);
}

/**
 * Check if current user can edit a specific recipe
 * Users can only edit their own recipes unless they have 'edit_all_recipes' permission or are admin
 *
 * @param object|int $recipe Recipe object or recipe ID
 * @return bool
 */
function dietetic_can_edit_recipe($recipe)
{
    // Admin can edit all recipes
    if (is_admin()) {
        return true;
    }

    // Check if user has general edit permission
    if (!dietetic_has_permission('edit')) {
        return false;
    }

    // Load CI instance to access models if needed
    $CI =& get_instance();

    // If recipe is an ID, load the full recipe
    if (is_numeric($recipe)) {
        $CI->load->model('dietetic/dietetic_recipes_model');
        $recipe = $CI->dietetic_recipes_model->get($recipe);
    }

    // If recipe doesn't exist, deny access
    if (!$recipe || !isset($recipe->dietitian_id)) {
        return false;
    }

    // Check if user owns this recipe
    $current_user_id = get_staff_user_id();
    if ($recipe->dietitian_id == $current_user_id) {
        return true;
    }

    // Check if user has permission to edit all recipes
    if (has_permission('dietetic', '', 'edit_all_recipes')) {
        return true;
    }

    return false;
}

/**
 * Check if current user can delete a specific recipe
 * Users can only delete their own recipes unless they have 'delete_all_recipes' permission or are admin
 *
 * @param object|int $recipe Recipe object or recipe ID
 * @return bool
 */
function dietetic_can_delete_recipe($recipe)
{
    // Admin can delete all recipes
    if (is_admin()) {
        return true;
    }

    // Check if user has general delete permission
    if (!dietetic_has_permission('delete')) {
        return false;
    }

    // Load CI instance to access models if needed
    $CI =& get_instance();

    // If recipe is an ID, load the full recipe
    if (is_numeric($recipe)) {
        $CI->load->model('dietetic/dietetic_recipes_model');
        $recipe = $CI->dietetic_recipes_model->get($recipe);
    }

    // If recipe doesn't exist, deny access
    if (!$recipe || !isset($recipe->dietitian_id)) {
        return false;
    }

    // Check if user owns this recipe
    $current_user_id = get_staff_user_id();
    if ($recipe->dietitian_id == $current_user_id) {
        return true;
    }

    // Check if user has permission to delete all recipes
    if (has_permission('dietetic', '', 'delete_all_recipes')) {
        return true;
    }

    return false;
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
 * Documentation: https://developers.lafricamobile.com/docs/sms/introduction
 *
 * @param string $phone
 * @param string $message
 * @return array ['success' => bool, 'message' => string]
 */
function dietetic_send_sms($phone, $message)
{
    $account_id = dietetic_get_option('sms_lam_account_id');
    $password = dietetic_get_option('sms_lam_password');
    $sender = dietetic_get_option('sms_lam_sender_id', 'API_LAMSMS');

    if (empty($account_id) || empty($password)) {
        return ['success' => false, 'message' => 'LAM SMS credentials not configured (account_id and password required)'];
    }

    // LAM SMS API endpoint
    $url = 'https://lamsms.lafricamobile.com/api';

    // Get additional settings
    $ret_url = dietetic_get_option('sms_lam_ret_url', site_url('dietetic/sms_callback'));
    $priority = dietetic_get_option('sms_lam_priority', '2');

    // Format phone number for LAM API
    // Ensure phone starts with country code (e.g., 221 for Senegal)
    $phone = preg_replace('/[^0-9]/', '', $phone);
    if (!preg_match('/^221/', $phone) && strlen($phone) == 9) {
        $phone = '221' . $phone;
    }

    // Add + prefix for SMS format (required by LAM SMS API)
    if (!preg_match('/^\+/', $phone)) {
        $phone = '+' . $phone;
    }

    // Prepare LAM API request
    $data = [
        'accountid' => $account_id,
        'password' => $password,
        'sender' => $sender,
        'ret_id' => 'dietetic_' . time(),
        'ret_url' => $ret_url,
        'priority' => $priority,
        'text' => $message,
        'to' => [$phone]  // Format simple array pour SMS LAM (différent de WhatsApp)
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json'
        ]
    ]);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);

    // Log the request for debugging
    log_activity('LAM SMS sent to ' . $phone . ' - HTTP Code: ' . $http_code . ' - Response: ' . $response);

    if ($http_code == 200 || $http_code == 201) {
        return ['success' => true, 'message' => 'SMS sent successfully', 'response' => json_decode($response, true)];
    }

    $error_msg = $curl_error ?: $response;
    return ['success' => false, 'message' => 'Failed to send SMS: ' . $error_msg];
}

/**
 * Send WhatsApp message via LAM API
 *
 * @param string $phone Phone number (with or without country code)
 * @param string $message Message text
 * @return array Result with 'success' and 'message' keys
 */
function dietetic_send_whatsapp($phone, $message)
{
    $account_id = dietetic_get_option('whatsapp_lam_account_id');
    $password = dietetic_get_option('whatsapp_lam_password');

    if (empty($account_id) || empty($password)) {
        return ['success' => false, 'message' => 'LAM WhatsApp credentials not configured (account_id and password required)'];
    }

    // LAM WhatsApp API endpoint
    $url = 'https://lamwhatsapp.lafricamobile.com/api';

    // Get additional settings
    $ret_url = dietetic_get_option('whatsapp_lam_ret_url', site_url('dietetic/whatsapp_callback'));

    // Format phone number for LAM API
    // Ensure phone starts with country code (e.g., 221 for Senegal)
    $phone = preg_replace('/[^0-9]/', '', $phone);
    if (!preg_match('/^221/', $phone) && strlen($phone) == 9) {
        $phone = '221' . $phone;
    }

    // Add + prefix for WhatsApp format
    if (!preg_match('/^\+/', $phone)) {
        $phone = '+' . $phone;
    }

    // Prepare LAM API request
    $data = [
        'accountid' => $account_id,
        'password' => $password,
        'ret_id' => 'dietetic_wa_' . time(),
        'ret_url' => $ret_url,
        'text' => $message,
        'to' => [
            [
                'ret_id_1' => $phone
            ]
        ]
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json'
        ]
    ]);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);

    // Log the request for debugging
    log_activity('LAM WhatsApp sent to ' . $phone . ' - HTTP Code: ' . $http_code . ' - Response: ' . $response);

    if ($http_code == 200 || $http_code == 201) {
        return ['success' => true, 'message' => 'WhatsApp sent successfully', 'response' => json_decode($response, true)];
    }

    $error_msg = $curl_error ?: $response;
    return ['success' => false, 'message' => 'Failed to send WhatsApp: ' . $error_msg];
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
    try {
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
    } catch (Exception $e) {
        // Log error and deny access by default
        log_activity('Error in dietetic_can_access_patient: ' . $e->getMessage());
        return false;
    }
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

    // STRICT FILTERING: Only super admin (user ID 1) sees ALL patients
    // ALL other users (including regular admins) only see their assigned patients
    if ($staff_id == 1 && dietetic_is_admin()) {
        return; // Super admin (user ID 1) sees all
    }

    // For all other users (dietitians, admins with ID != 1), apply strict filtering
    if ($staff_id) {
        // Use LEFT JOIN to include patients without assignments in patient_dietitians table
        // This handles both the new system (patient_dietitians) and old system (dietitian_id field)
        $db->join(db_prefix() . 'dietic_patient_dietitians ' . $table_alias,
                  $table_alias . '.patient_id = p.id AND ' . $table_alias . '.status = "active"', 'left');

        // Show patients where EITHER:
        // 1. Staff is assigned in patient_dietitians table (new many-to-many system)
        // 2. Staff matches the dietitian_id field (old system / fallback for legacy patients)
        $db->group_start();
        $db->where($table_alias . '.dietitian_id', $staff_id);
        $db->or_where('p.dietitian_id', $staff_id);
        $db->group_end();
    } else {
        // No staff user and not a client = no access
        $db->where('1', '0'); // Always false - no patients visible
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
 * Examples: 'food_surveys', 'notifications_manage', 'reports_advanced', 'view', 'create', 'edit', 'delete'
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
        // Table doesn't exist yet - use defaults from dietetic_get_available_permissions()
        $available_permissions = dietetic_get_available_permissions();
        if (isset($available_permissions[$permission_key]['default'])) {
            return (bool)$available_permissions[$permission_key]['default'];
        }
        return false; // Deny by default if permission not defined
    }

    // Query permission - use get_where to avoid Query Builder conflicts
    $permission = $CI->db->get_where(db_prefix() . 'dietic_staff_permissions', [
        'staff_id' => $staff_id,
        'permission_key' => $permission_key
    ])->row();

    if ($permission) {
        return (bool)$permission->permission_value;
    }

    // No record found - use default from dietetic_get_available_permissions()
    $available_permissions = dietetic_get_available_permissions();
    if (isset($available_permissions[$permission_key]['default'])) {
        return (bool)$available_permissions[$permission_key]['default'];
    }

    // Fallback to settings table
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
        // Core permissions
        'view' => [
            'label' => 'Voir',
            'description' => 'Voir les données (patients, consultations, programmes, etc.)',
            'default' => true,
        ],
        'create' => [
            'label' => 'Créer',
            'description' => 'Créer des abonnements, patients, consultations, etc.',
            'default' => false,
        ],
        'edit' => [
            'label' => 'Modifier',
            'description' => 'Modifier des données existantes',
            'default' => false,
        ],
        'delete' => [
            'label' => 'Supprimer',
            'description' => 'Supprimer/Annuler des données',
            'default' => false,
        ],

        // Feature-specific permissions
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

/**
 * Translate consultation type to French
 *
 * @param string $type Consultation type (initial, follow_up, emergency, etc.)
 * @return string Translated consultation type
 */
function dietetic_consultation_type_label($type)
{
    $labels = [
        'initial' => 'Consultation Initiale',
        'follow_up' => 'Suivi',
        'emergency' => 'Urgence',
        'checkup' => 'Bilan',
        'control' => 'Contrôle',
    ];

    return $labels[$type] ?? ucfirst(str_replace('_', ' ', $type));
}

// ==================== FREEMIUM MODEL ====================

/**
 * Check if a patient has an active premium program
 * Returns true if patient has at least one active program that hasn't expired
 *
 * @param int $patient_id Patient ID
 * @return bool True if user is premium (has active program), false if free tier
 */
function is_premium_user($patient_id = null)
{
    // If no patient_id provided, try to get current patient from client session
    if ($patient_id === null) {
        if (!is_client_logged_in()) {
            return false;
        }

        $CI = &get_instance();
        $client_id = get_client_user_id();

        // Get patient from client_id
        $patient = $CI->db->get_where(db_prefix() . 'dietic_patients', ['client_id' => $client_id])->row();

        if (!$patient) {
            return false;
        }

        $patient_id = $patient->id;
    }

    if (!$patient_id) {
        return false;
    }

    $CI = &get_instance();

    // Check if patient has at least one active program
    // Program is considered active if:
    // 1. status = 'active'
    // 2. end_date is NULL (unlimited) OR end_date >= today
    $CI->db->where('patient_id', $patient_id);
    $CI->db->where('status', 'active');
    $CI->db->group_start();
    $CI->db->where('end_date IS NULL');
    $CI->db->or_where('end_date >=', date('Y-m-d'));
    $CI->db->group_end();

    $active_programs = $CI->db->get(db_prefix() . 'dietic_programs')->num_rows();

    return $active_programs > 0;
}

/**
 * Verify if current user has access to a premium feature
 * If not premium, redirects to upgrade page or returns error
 *
 * @param string $feature_name Name of the feature being checked (for logging/display)
 * @param bool $redirect If true, redirects to upgrade page. If false, returns boolean
 * @param string $redirect_url Custom redirect URL (default: portal upgrade page)
 * @return bool True if has access, false otherwise (only when $redirect = false)
 */
function verify_premium_feature($feature_name = 'premium feature', $redirect = true, $redirect_url = null)
{
    // Get current patient ID
    $patient_id = null;

    if (is_client_logged_in()) {
        $CI = &get_instance();
        $client_id = get_client_user_id();

        $patient = $CI->db->get_where(db_prefix() . 'dietic_patients', ['client_id' => $client_id])->row();

        if ($patient) {
            $patient_id = $patient->id;
        }
    }

    // Check if user is premium
    if (is_premium_user($patient_id)) {
        return true;
    }

    // User is NOT premium - handle based on $redirect parameter
    if ($redirect) {
        // Log the blocked access attempt
        log_activity(sprintf(
            'Blocked free user from accessing premium feature: %s (Patient ID: %s)',
            $feature_name,
            $patient_id ?? 'unknown'
        ));

        // Set alert message
        set_alert('warning', sprintf(
            'Cette fonctionnalité est réservée aux abonnés Premium. Mettez à niveau votre programme pour y accéder.',
            $feature_name
        ));

        // Determine redirect URL
        if ($redirect_url === null) {
            $redirect_url = site_url('dietetic/portal/upgrade');
        }

        // Redirect to upgrade page
        redirect($redirect_url);
        return false; // Never reached, but for clarity
    }

    // Don't redirect, just return false
    return false;
}

/**
 * Get premium badge HTML component
 * Displays a "Premium" or "Free" badge with styling
 *
 * @param int $patient_id Patient ID (null = current patient)
 * @param string $size Size: 'small', 'medium', 'large' (default: 'medium')
 * @param bool $show_free If true, shows "Free" badge for non-premium users. If false, shows nothing
 * @return string HTML badge
 */
function get_premium_badge($patient_id = null, $size = 'medium', $show_free = true)
{
    $is_premium = is_premium_user($patient_id);

    // Size configurations
    $sizes = [
        'small' => [
            'padding' => '4px 8px',
            'font_size' => '11px',
            'icon_size' => '12px',
        ],
        'medium' => [
            'padding' => '6px 12px',
            'font_size' => '13px',
            'icon_size' => '14px',
        ],
        'large' => [
            'padding' => '10px 16px',
            'font_size' => '15px',
            'icon_size' => '16px',
        ],
    ];

    $size_config = $sizes[$size] ?? $sizes['medium'];

    if ($is_premium) {
        // Premium badge - Gold gradient
        return sprintf(
            '<span class="premium-badge premium-badge-%s" style="
                display: inline-flex;
                align-items: center;
                gap: 6px;
                background: linear-gradient(135deg, #FFD700 0%%, #FFA500 100%%);
                color: #000;
                font-weight: 600;
                border-radius: 20px;
                padding: %s;
                font-size: %s;
                box-shadow: 0 2px 8px rgba(255, 215, 0, 0.3);
                border: 1px solid rgba(255, 215, 0, 0.5);
            ">
                <i class="fa fa-crown" style="font-size: %s;"></i>
                Premium
            </span>',
            htmlspecialchars($size),
            $size_config['padding'],
            $size_config['font_size'],
            $size_config['icon_size']
        );
    } else {
        // Free badge - Gray
        if (!$show_free) {
            return '';
        }

        return sprintf(
            '<span class="free-badge free-badge-%s" style="
                display: inline-flex;
                align-items: center;
                gap: 6px;
                background: #f0f0f0;
                color: #666;
                font-weight: 500;
                border-radius: 20px;
                padding: %s;
                font-size: %s;
                border: 1px solid #ddd;
            ">
                <i class="fa fa-user" style="font-size: %s;"></i>
                Gratuit
            </span>',
            htmlspecialchars($size),
            $size_config['padding'],
            $size_config['font_size'],
            $size_config['icon_size']
        );
    }
}

/**
 * Get premium feature lock HTML component
 * Displays a lock overlay for premium features
 *
 * @param string $feature_name Feature name to display
 * @param string $upgrade_url URL to upgrade page (default: portal/upgrade)
 * @param bool $inline If true, displays inline. If false, displays as overlay
 * @return string HTML lock component
 */
function get_premium_lock($feature_name = 'cette fonctionnalité', $upgrade_url = null, $inline = false)
{
    if ($upgrade_url === null) {
        $upgrade_url = site_url('dietetic/portal/upgrade');
    }

    if ($inline) {
        // Inline lock (for buttons, small elements)
        return sprintf(
            '<div class="premium-lock-inline" style="
                display: inline-flex;
                align-items: center;
                gap: 8px;
                background: linear-gradient(135deg, #01807B 0%%, #01655f 100%%);
                color: white;
                padding: 10px 16px;
                border-radius: 8px;
                text-decoration: none;
                font-weight: 500;
                transition: transform 0.2s;
            " onmouseover="this.style.transform=\'scale(1.05)\'" onmouseout="this.style.transform=\'scale(1)\'">
                <i class="fa fa-lock"></i>
                <span>Premium Requis</span>
            </div>'
        );
    } else {
        // Overlay lock (for larger sections)
        return sprintf(
            '<div class="premium-lock-overlay" style="
                position: relative;
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                border: 2px dashed #01807B;
                border-radius: 12px;
                padding: 40px 20px;
                text-align: center;
                min-height: 200px;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 20px;
            ">
                <div style="
                    width: 80px;
                    height: 80px;
                    background: linear-gradient(135deg, #01807B 0%%, #01655f 100%%);
                    border-radius: 50%%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    box-shadow: 0 4px 20px rgba(1, 128, 123, 0.3);
                ">
                    <i class="fa fa-lock" style="font-size: 36px; color: white;"></i>
                </div>

                <div>
                    <h4 style="margin: 0 0 10px 0; color: #333;">Fonctionnalité Premium</h4>
                    <p style="color: #666; margin: 0 0 20px 0;">
                        %s est réservée aux abonnés Premium.
                    </p>
                </div>

                <a href="%s" class="btn btn-primary" style="
                    background: linear-gradient(135deg, #01807B 0%%, #01655f 100%%);
                    border: none;
                    padding: 12px 30px;
                    border-radius: 25px;
                    color: white;
                    text-decoration: none;
                    font-weight: 600;
                    display: inline-flex;
                    align-items: center;
                    gap: 8px;
                    box-shadow: 0 4px 15px rgba(1, 128, 123, 0.3);
                    transition: transform 0.2s;
                " onmouseover="this.style.transform=\'translateY(-2px)\'" onmouseout="this.style.transform=\'translateY(0)\'">
                    <i class="fa fa-crown"></i>
                    Passer à Premium
                </a>
            </div>',
            htmlspecialchars($feature_name),
            htmlspecialchars($upgrade_url)
        );
    }
}

/**
 * Get premium features comparison for free vs premium users
 * Useful for upgrade pages
 *
 * @return array Associative array of features with free/premium availability
 */
function get_premium_features_comparison()
{
    return [
        'historique' => [
            'name' => 'Historique Complet',
            'description' => 'Accès à tout votre historique (pesées, repas, photos)',
            'free' => true,
            'premium' => true,
            'icon' => 'fa-history',
        ],
        'profil' => [
            'name' => 'Gestion du Profil',
            'description' => 'Modifier vos informations personnelles',
            'free' => true,
            'premium' => true,
            'icon' => 'fa-user',
        ],
        'pesees' => [
            'name' => 'Enregistrement Pesées',
            'description' => 'Suivre votre poids régulièrement',
            'free' => '1 par semaine',
            'premium' => 'Illimité',
            'icon' => 'fa-balance-scale',
        ],
        'journal' => [
            'name' => 'Journal Alimentaire',
            'description' => 'Noter vos repas quotidiens',
            'free' => 'Basique (sans analyse)',
            'premium' => 'Complet avec analyse nutritionnelle',
            'icon' => 'fa-cutlery',
        ],
        'photos' => [
            'name' => 'Photos de Progression',
            'description' => 'Suivre votre transformation visuellement',
            'free' => 'Consultation uniquement',
            'premium' => 'Ajout illimité',
            'icon' => 'fa-camera',
        ],
        'messagerie' => [
            'name' => 'Messagerie Diététicien',
            'description' => 'Communiquer avec votre diététicien',
            'free' => false,
            'premium' => true,
            'icon' => 'fa-comments',
        ],
        'plans' => [
            'name' => 'Plans de Repas',
            'description' => 'Plans nutritionnels personnalisés',
            'free' => false,
            'premium' => true,
            'icon' => 'fa-file-text',
        ],
        'notifications' => [
            'name' => 'Rappels Automatiques',
            'description' => 'Notifications pour repas, hydratation, pesée',
            'free' => false,
            'premium' => true,
            'icon' => 'fa-bell',
        ],
        'rdv' => [
            'name' => 'Prise de Rendez-vous',
            'description' => 'Réserver des consultations en ligne',
            'free' => false,
            'premium' => true,
            'icon' => 'fa-calendar',
        ],
        'recettes' => [
            'name' => 'Bibliothèque Recettes',
            'description' => 'Accès aux recettes diététiques',
            'free' => '5 recettes',
            'premium' => 'Bibliothèque complète',
            'icon' => 'fa-book',
        ],
    ];
}
