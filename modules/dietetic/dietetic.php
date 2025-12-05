<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Dietetic Management
Description: Complete dietetic management system connecting dietitians with their patients
Version: 1.0.0
Author: Perfex CRM
Author URI: https://perfexcrm.com
Requires at least: 2.3.*
*/

define('DIETETIC_MODULE_NAME', 'dietetic');
define('DIETETIC_MODULE_PATH', __DIR__ . '/');
define('DIETETIC_MODULE_UPLOAD_FOLDER', module_dir_path(DIETETIC_MODULE_NAME, 'uploads'));

/**
 * Register activation module hook
 */
register_activation_hook(DIETETIC_MODULE_NAME, 'dietetic_module_activation_hook');

function dietetic_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
 * Register deactivation module hook
 */
register_deactivation_hook(DIETETIC_MODULE_NAME, 'dietetic_module_deactivation_hook');

function dietetic_module_deactivation_hook()
{
    // Optional cleanup tasks on deactivation (not uninstall)
}

/**
 * Register uninstall module hook
 */
register_uninstall_hook(DIETETIC_MODULE_NAME, 'dietetic_module_uninstall_hook');

function dietetic_module_uninstall_hook()
{
    $CI = &get_instance();

    // Load the uninstall SQL
    $uninstall_sql = file_get_contents(__DIR__ . '/uninstall.sql');

    // Execute each SQL statement
    $statements = array_filter(array_map('trim', explode(';', $uninstall_sql)));

    foreach ($statements as $statement) {
        if (!empty($statement)) {
            $CI->db->query($statement);
        }
    }

    // Remove upload folder if exists
    if (is_dir(DIETETIC_MODULE_UPLOAD_FOLDER)) {
        delete_dir(DIETETIC_MODULE_UPLOAD_FOLDER);
    }
}

/**
 * Register language files
 */
register_language_files(DIETETIC_MODULE_NAME, [DIETETIC_MODULE_NAME]);

/**
 * Init module menu items in setup in admin_init hook
 */
hooks()->add_action('admin_init', 'dietetic_module_init_menu_items');

/**
 * Redirect non-admin users with dietetic access to dietetic dashboard
 */
hooks()->add_action('pre_controller', 'dietetic_redirect_to_dashboard');

/**
 * Add JavaScript to force recipes menu in admin
 */
hooks()->add_action('app_admin_footer', 'dietetic_force_recipes_menu_js');

function dietetic_force_recipes_menu_js()
{
    // Charger le script qui force l'ajout du menu Recettes
    echo '<script src="' . module_dir_url('dietetic', 'assets/js/force_recipes_menu.js') . '?v=' . time() . '"></script>';
}

/**
 * Add JavaScript to force activities menu in admin
 */
hooks()->add_action('app_admin_footer', 'dietetic_force_activities_menu_js');

function dietetic_force_activities_menu_js()
{
    // Charger le script qui force l'ajout du menu Activités
    echo '<script src="' . module_dir_url('dietetic', 'assets/js/force_activities_menu.js') . '?v=' . time() . '"></script>';
}

/**
 * Add JavaScript to force blog menu in admin
 */
hooks()->add_action('app_admin_footer', 'dietetic_force_blog_menu_js');

function dietetic_force_blog_menu_js()
{
    // Charger le script qui force l'ajout du menu Blog & Conseils
    echo '<script src="' . module_dir_url('dietetic', 'assets/js/force_blog_menu.js') . '?v=' . time() . '"></script>';
}


/**
 * Define module menu items
 */
function dietetic_module_init_menu_items()
{
    $CI = &get_instance();

    // Load dietetic helper for permission functions
    $CI->load->helper('dietetic/dietetic');

    // Check if user has access to dietetic module
    $has_view_permission = has_permission('dietetic', '', 'view') || is_admin();

    if ($has_view_permission) {
        $CI->app_menu->add_sidebar_menu_item('dietetic', [
            'name'     => _l('dietetic'),
            'icon'     => 'fa fa-heartbeat',
            'href'     => admin_url('dietetic/dashboard'),
            'position' => 15,
        ]);

        // Dashboard
        $CI->app_menu->add_sidebar_children_item('dietetic', [
            'slug'     => 'dietetic-dashboard',
            'name'     => _l('dietetic_dashboard'),
            'icon'     => 'fa fa-tachometer',
            'href'     => admin_url('dietetic/dashboard'),
            'position' => 1,
        ]);

        // Patients
        $CI->app_menu->add_sidebar_children_item('dietetic', [
            'slug'     => 'dietetic-patients',
            'name'     => _l('dietetic_patients'),
            'icon'     => 'fa fa-users',
            'href'     => admin_url('dietetic/patients'),
            'position' => 2,
        ]);

        // Consultations
        $CI->app_menu->add_sidebar_children_item('dietetic', [
            'slug'     => 'dietetic-consultations',
            'name'     => _l('dietetic_consultations'),
            'icon'     => 'fa fa-stethoscope',
            'href'     => admin_url('dietetic/consultations'),
            'position' => 3,
        ]);

        // Programs
        $CI->app_menu->add_sidebar_children_item('dietetic', [
            'slug'     => 'dietetic-programs',
            'name'     => _l('dietetic_programs'),
            'icon'     => 'fa fa-list-alt',
            'href'     => admin_url('dietetic/programs'),
            'position' => 4,
        ]);

        // Food Surveys (Enquêtes Alimentaires) - Show if table exists
        if ($CI->db->table_exists(db_prefix() . 'dietic_food_surveys')) {
            $CI->app_menu->add_sidebar_children_item('dietetic', [
                'slug'     => 'dietetic-food-surveys',
                'name'     => 'Enquêtes Alimentaires',
                'icon'     => 'fa fa-camera',
                'href'     => admin_url('dietetic/food_surveys'),
                'position' => 5,
            ]);
        }

        // Activities - FORCÉ sans condition pour permettre l'accès avant migration
        $CI->app_menu->add_sidebar_children_item('dietetic', [
            'slug'     => 'dietetic-activities',
            'name'     => 'Activités Sportives',
            'icon'     => 'fa fa-heartbeat',
            'href'     => admin_url('dietetic/activities/manage'),
            'position' => 5.3,
        ]);

        // Notifications - Show if table exists and user is admin
        if ($CI->db->table_exists(db_prefix() . 'dietic_notification_preferences') && is_admin()) {
            $CI->app_menu->add_sidebar_children_item('dietetic', [
                'slug'     => 'dietetic-notifications',
                'name'     => 'Notifications',
                'icon'     => 'fa fa-bell',
                'href'     => admin_url('dietetic/notifications'),
                'position' => 5.5,
            ]);
        }

        // Dietitians - Show to admins only
        if (is_admin()) {
            $CI->app_menu->add_sidebar_children_item('dietetic', [
                'slug'     => 'dietetic-dietitians',
                'name'     => 'Diététiciens',
                'icon'     => 'fa fa-user-md',
                'href'     => admin_url('dietetic/dietitians'),
                'position' => 6,
            ]);
        }

        // Foods Database - Show to everyone with view permission
        $CI->app_menu->add_sidebar_children_item('dietetic', [
            'slug'     => 'dietetic-foods',
            'name'     => _l('dietetic_foods'),
            'icon'     => 'fa fa-cutlery',
            'href'     => admin_url('dietetic/foods'),
            'position' => 7,
        ]);

        // Recipes Library - FORCÉ sans condition pour debug
        $CI->app_menu->add_sidebar_children_item('dietetic', [
            'slug'     => 'dietetic-recipes',
            'name'     => 'Bibliothèque de Recettes',
            'icon'     => 'fa fa-book',
            'href'     => admin_url('dietetic/recipes'),
            'position' => 7.5,
        ]);

        // Blog / Conseils - Show to everyone with view permission
        $CI->app_menu->add_sidebar_children_item('dietetic', [
            'slug'     => 'dietetic-blog',
            'name'     => 'Blog & Conseils',
            'icon'     => 'fa fa-newspaper-o',
            'href'     => admin_url('dietetic/blog'),
            'position' => 7.6,
        ]);

        // Staff Permissions - Admin only
        if (is_admin()) {
            $CI->app_menu->add_sidebar_children_item('dietetic', [
                'slug'     => 'dietetic-staff-permissions',
                'name'     => 'Permissions Diététiciens',
                'icon'     => 'fa fa-shield',
                'href'     => admin_url('dietetic/staff_permissions'),
                'position' => 98,
            ]);

            // Legal Pages Management - Admin only
            $CI->app_menu->add_sidebar_children_item('dietetic', [
                'slug'     => 'dietetic-legal-pages',
                'name'     => 'Pages Légales',
                'icon'     => 'fa fa-gavel',
                'href'     => admin_url('dietetic/legal_pages/manage'),
                'position' => 98.5,
            ]);
        }

        // Settings - requires settings permission
        if (has_permission('dietetic', '', 'settings') || is_admin()) {
            $CI->app_menu->add_sidebar_children_item('dietetic', [
                'slug'     => 'dietetic-settings',
                'name'     => _l('settings'),
                'icon'     => 'fa fa-cog',
                'href'     => admin_url('dietetic/settings'),
                'position' => 99,
            ]);
        }
    }
}

/**
 * Add customer profile tab
 * TEMPORARILY DISABLED TO FIX 404 ERROR
 */
// hooks()->add_action('customer_profile_tabs', 'dietetic_add_customer_profile_tab');

function dietetic_add_customer_profile_tab($client_id)
{
    // Completely disable any potential errors by wrapping everything
    if (!function_exists('has_permission') || !function_exists('admin_url') || !function_exists('_l')) {
        return; // Core Perfex functions not loaded yet
    }

    // Only show tab if viewing an existing client (not on create page)
    if (!has_permission('dietetic', '', 'view') || empty($client_id) || !is_numeric($client_id)) {
        return; // No permission or invalid client ID
    }

    // Use output buffering to prevent any echo from breaking the page
    ob_start();
    try {
        echo '<li role="presentation">
                <a href="' . admin_url('dietetic/patients/client_view/' . $client_id) . '" data-group="dietetic">
                    <i class="fa fa-heartbeat"></i> ' . _l('dietetic_follow_up') . '
                </a>
              </li>';

        // Flush the buffer if no errors
        ob_end_flush();
    } catch (Throwable $e) {
        // Catch ALL errors (including PHP 7+ errors like TypeError)
        ob_end_clean(); // Discard any output
        // Log but don't break the page
        if (function_exists('log_activity')) {
            log_activity('Dietetic tab error: ' . $e->getMessage());
        }
    }
}

/**
 * Add legal pages links to header
 */
hooks()->add_action('app_admin_footer', 'dietetic_add_legal_links_to_header');
hooks()->add_action('app_customers_portal_footer', 'dietetic_add_legal_links_to_header');

/**
 * Add custom CSS for admin area (in head)
 */
hooks()->add_action('app_admin_head', 'dietetic_add_head_components');

function dietetic_add_head_components()
{
    $CI = &get_instance();
    $module_path = module_dir_url(DIETETIC_MODULE_NAME);

    if (strpos($_SERVER['REQUEST_URI'], '/admin/dietetic') !== false) {
        echo '<link href="' . $module_path . 'assets/css/dietetic.css?v=' . time() . '" rel="stylesheet" type="text/css" />';
    }
}

/**
 * Add custom JS for admin area (in footer, after jQuery is loaded)
 */
hooks()->add_action('app_admin_footer', 'dietetic_add_footer_components');

function dietetic_add_footer_components()
{
    $CI = &get_instance();
    $module_path = module_dir_url(DIETETIC_MODULE_NAME);

    // Load full dietetic.js only on dietetic pages
    if (strpos($_SERVER['REQUEST_URI'], '/admin/dietetic') !== false) {
        echo '<script src="' . $module_path . 'assets/js/dietetic.js?v=' . time() . '"></script>';
    }
}

/**
 * Add custom CSS and JS for client portal
 */
hooks()->add_action('app_customers_portal_head', 'dietetic_add_portal_head_components');

function dietetic_add_portal_head_components()
{
    $CI = &get_instance();
    $module_path = module_dir_url(DIETETIC_MODULE_NAME);

    echo '<link href="' . $module_path . 'assets/css/dietetic_portal.css?v=' . time() . '" rel="stylesheet" type="text/css" />';
    echo '<script src="' . $module_path . 'assets/js/dietetic_portal.js?v=' . time() . '"></script>';
}

/**
 * Add client portal menu item
 */
hooks()->add_action('customers_navigation_start', 'dietetic_add_portal_menu');

function dietetic_add_portal_menu()
{
    // Check all required functions exist
    if (!function_exists('is_client_logged_in') || !function_exists('get_client_user_id') ||
        !function_exists('site_url') || !function_exists('module_dir_path')) {
        return; // Core functions not loaded
    }

    if (!is_client_logged_in()) {
        return;
    }

    // Use output buffering to prevent breaking the page
    ob_start();
    try {
        $CI = &get_instance();

        if (!$CI) {
            ob_end_clean();
            return;
        }

        // Check if dietetic_patients_model exists before loading
        $model_path = module_dir_path(DIETETIC_MODULE_NAME, 'models/Dietetic_patients_model.php');

        if (!file_exists($model_path)) {
            ob_end_clean();
            return;
        }

        $CI->load->model('dietetic/dietetic_patients_model');

        if (!isset($CI->dietetic_patients_model)) {
            ob_end_clean();
            return;
        }

        $client_id = get_client_user_id();
        if (empty($client_id)) {
            ob_end_clean();
            return;
        }

        $patient = $CI->dietetic_patients_model->get_by_client($client_id);

        if ($patient) {
            // Check if food surveys are enabled
            $food_surveys_enabled = $CI->db->table_exists(db_prefix() . 'dietic_food_surveys');

            echo '<li class="customers-nav-item-dietetic">
                    <a href="' . site_url('dietetic/portal') . '">
                        <i class="fa fa-heartbeat"></i> Mon Programme
                    </a>
                  </li>';

            // Add food surveys menu item if enabled
            if ($food_surveys_enabled) {
                echo '<li class="customers-nav-item-dietetic-surveys">
                        <a href="' . site_url('dietetic/portal/food_surveys') . '">
                            <i class="fa fa-clipboard-list"></i> Mes Enquêtes Alimentaires
                        </a>
                      </li>';
            }
        }

        ob_end_flush();
    } catch (Throwable $e) {
        // Catch ALL errors including PHP 7+ Errors
        ob_end_clean();
        // Log if possible, but don't break
        if (function_exists('log_activity')) {
            log_activity('Dietetic portal menu error: ' . $e->getMessage());
        }
    }
}

/**
 * Register cron job for sending reminders
 * Integrated with Perfex CRM cron system
 */
hooks()->add_action('after_cron_run', 'dietetic_send_scheduled_reminders');

function dietetic_send_scheduled_reminders()
{
    $CI = &get_instance();
    $CI->load->model('dietetic/dietetic_notifications_model');

    // TOUJOURS logger le début d'exécution pour vérifier que le cron fonctionne
    log_activity('Dietetic Cron: Démarrage à ' . date('Y-m-d H:i:s'));

    // Compteurs pour le reporting
    $total_sent = 0;
    $total_failed = 0;

    try {
        // ==================== WEIGHT REMINDERS ====================
        // NEW APPROACH: Broadcast to ALL users (web + mobile) using OneSignal segments
        $weight_patients = $CI->dietetic_notifications_model->get_patients_for_weight_reminder();
        if (!empty($weight_patients)) {
            log_activity("Dietetic Cron: Sending weight reminder to ALL users (broadcast mode)");

            // Send ONE broadcast notification to ALL users (web + mobile)
            $result = $CI->dietetic_notifications_model->send_broadcast_weight_reminder();

            if ($result['success']) {
                $total_sent++;
                log_activity("Dietetic Cron: Weight broadcast sent successfully - Recipients: " . ($result['recipients'] ?? 0));
            } else {
                $total_failed++;
                log_activity("Dietetic Cron: Weight broadcast failed - Error: " . ($result['error'] ?? 'unknown'));
            }
        }

        // ==================== WATER REMINDERS ====================
        // NEW APPROACH: Broadcast to ALL users (web + mobile) using OneSignal segments
        $water_patients = $CI->dietetic_notifications_model->get_patients_for_water_reminder();
        if (!empty($water_patients)) {
            log_activity("Dietetic Cron: Sending water reminder to ALL users (broadcast mode)");

            // Send ONE broadcast notification to ALL users (web + mobile)
            $result = $CI->dietetic_notifications_model->send_broadcast_water_reminder();

            if ($result['success']) {
                $total_sent++;
                log_activity("Dietetic Cron: Water broadcast sent successfully - Recipients: " . ($result['recipients'] ?? 0));
            } else {
                $total_failed++;
                log_activity("Dietetic Cron: Water broadcast failed - Error: " . ($result['error'] ?? 'unknown'));
            }
        }

        // ==================== MEAL REMINDERS ====================
        // NEW APPROACH: Broadcast to ALL users (web + mobile) using OneSignal segments
        // This ensures reminders reach everyone, including mobile app users
        $meal_types = ['breakfast', 'lunch', 'dinner'];
        foreach ($meal_types as $meal_type) {
            // Check if any patient has this meal reminder enabled
            $meal_patients = $CI->dietetic_notifications_model->get_patients_for_meal_reminder($meal_type);

            if (!empty($meal_patients)) {
                log_activity("Dietetic Cron: Sending {$meal_type} reminder to ALL users (broadcast mode)");

                // Send ONE broadcast notification to ALL users (web + mobile)
                $result = $CI->dietetic_notifications_model->send_broadcast_meal_reminder($meal_type);

                if ($result['success']) {
                    $total_sent++;
                    log_activity("Dietetic Cron: {$meal_type} broadcast sent successfully - Recipients: " . ($result['recipients'] ?? 0));
                } else {
                    $total_failed++;
                    log_activity("Dietetic Cron: {$meal_type} broadcast failed - Error: " . ($result['error'] ?? 'unknown'));
                }
            }
        }

        // ==================== CONSULTATION REMINDERS (DAY BEFORE) ====================
        $consultations_day = $CI->dietetic_notifications_model->get_consultations_for_day_reminder();
        if (!empty($consultations_day)) {
            foreach ($consultations_day as $consultation) {
                $dietitian_name = $consultation->dietitian_firstname . ' ' . $consultation->dietitian_lastname;
                $result = $CI->dietetic_notifications_model->notify_consultation_reminder_day(
                    $consultation->patient_id,
                    $consultation->consultation_date,
                    $consultation->consultation_time,
                    $dietitian_name
                );
                if ($result) {
                    $success_count = array_filter($result, function($r) { return $r === true; });
                    $total_sent += count($success_count);
                    if (empty($success_count)) {
                        $total_failed++;
                    }
                }
            }
        }

        // ==================== CONSULTATION REMINDERS (1 HOUR BEFORE) ====================
        $consultations_hour = $CI->dietetic_notifications_model->get_consultations_for_hour_reminder();
        if (!empty($consultations_hour)) {
            foreach ($consultations_hour as $consultation) {
                $dietitian_name = $consultation->dietitian_firstname . ' ' . $consultation->dietitian_lastname;
                $result = $CI->dietetic_notifications_model->notify_consultation_reminder_hour(
                    $consultation->patient_id,
                    $consultation->consultation_time,
                    $dietitian_name
                );
                if ($result) {
                    $success_count = array_filter($result, function($r) { return $r === true; });
                    $total_sent += count($success_count);
                    if (empty($success_count)) {
                        $total_failed++;
                    }
                }
            }
        }

        // ==================== FOOD ENTRY REMINDERS (18:00 only) ====================
        $current_hour = (int)date('H');
        if ($current_hour == 18) {
            $patients_to_remind = $CI->dietetic_notifications_model->get_patients_for_food_entry_reminder();
            if (!empty($patients_to_remind)) {
                foreach ($patients_to_remind as $patient) {
                    $result = $CI->dietetic_notifications_model->send_food_entry_reminder($patient->patient_id);
                    if ($result) {
                        $success_count = array_filter($result, function($r) { return $r === true; });
                        $total_sent += count($success_count);
                        if (empty($success_count)) {
                            $total_failed++;
                        }
                    }
                }
            }
        }

        // TOUJOURS logger la fin d'exécution, même si aucune notification envoyée
        log_activity(sprintf(
            'Dietetic Cron: Fin à %s - %d notifications envoyées, %d échecs',
            date('Y-m-d H:i:s'),
            $total_sent,
            $total_failed
        ));

    } catch (Exception $e) {
        log_activity('Dietetic Cron Error: ' . $e->getMessage());
    }
}

/**
 * Register cron job for processing recurring payments
 * Integrated with Perfex CRM cron system - Phase 9
 */
hooks()->add_action('after_cron_run', 'dietetic_process_recurring_payments');

function dietetic_process_recurring_payments()
{
    $CI = &get_instance();
    $CI->load->model('dietetic/dietetic_recurring_payments_model');
    $CI->load->model('dietetic/dietetic_settings_model');

    try {
        // Check if recurring payments are enabled
        $enabled = $CI->dietetic_settings_model->get_setting('recurring_payments_enabled');
        if ($enabled != '1') {
            return; // Silently skip if disabled
        }

        $start_time = microtime(true);
        $processed_count = 0;
        $success_count = 0;
        $failed_count = 0;

        // Get all due payments
        $due_payments = $CI->dietetic_recurring_payments_model->get_due_payments();

        foreach ($due_payments as $payment) {
            $processed_count++;

            try {
                $result = $CI->dietetic_recurring_payments_model->process_payment($payment->id);

                if ($result) {
                    $success_count++;
                } else {
                    $failed_count++;
                }
            } catch (Exception $e) {
                $failed_count++;
                log_activity(sprintf(
                    'Dietetic Recurring Payment Error (Payment #%d): %s',
                    $payment->id,
                    $e->getMessage()
                ));
            }
        }

        $execution_time = round(microtime(true) - $start_time, 2);

        // Log activity if payments were processed
        if ($processed_count > 0) {
            log_activity(sprintf(
                'Dietetic Recurring Payments: %d traités, %d réussis, %d échecs (%.2fs)',
                $processed_count,
                $success_count,
                $failed_count,
                $execution_time
            ));
        }

    } catch (Exception $e) {
        log_activity('Dietetic Recurring Payments Cron Error: ' . $e->getMessage());
    }
}

/**
 * Add permissions
 */
hooks()->add_action('admin_init', 'dietetic_permissions');

function dietetic_permissions()
{
    $capabilities = [];

    $capabilities['capabilities'] = [
        'view'   => _l('permission_view') . ' (' . _l('permission_global') . ')',
        'create' => _l('permission_create'),
        'edit'   => _l('permission_edit'),
        'delete' => _l('permission_delete'),
        'settings' => _l('dietetic_permission_settings'),
        'manage_foods' => _l('dietetic_permission_manage_foods'),
        'view_dietitians' => _l('dietetic_permission_view_dietitians'),
        'edit_all_recipes' => _l('dietetic_permission_edit_all_recipes'),
        'delete_all_recipes' => _l('dietetic_permission_delete_all_recipes'),
    ];

    register_staff_capabilities('dietetic', $capabilities, _l('dietetic'));
}

/**
 * Redirect non-admin staff to dietetic dashboard instead of main dashboard
 */
function dietetic_redirect_to_dashboard()
{
    // Only check if user is logged in and accessing admin area
    if (!is_staff_logged_in()) {
        return;
    }

    $CI = &get_instance();

    // Get current URI
    $current_uri = $_SERVER['REQUEST_URI'] ?? '';

    // Only redirect if user is accessing the main dashboard (/admin or /admin/dashboard)
    if (!preg_match('#/admin/?$|/admin/dashboard/?$#', $current_uri)) {
        return;
    }

    // Don't redirect admins - they need access to the full Perfex dashboard
    if (is_admin()) {
        return;
    }

    // Check if user has access to dietetic module
    if (!has_permission('dietetic', '', 'view')) {
        return;
    }

    // Redirect to dietetic dashboard
    redirect(admin_url('dietetic/dashboard'));
}

/**
 * Add legal pages links to header navigation
 */
function dietetic_add_legal_links_to_header()
{
    // Determine if we're in admin or client portal
    $is_admin_area = is_staff_logged_in();
    $privacy_url = $is_admin_area ? admin_url('dietetic/legal_pages/privacy') : site_url('dietetic/portal/privacy');
    $terms_url = $is_admin_area ? admin_url('dietetic/legal_pages/terms') : site_url('dietetic/portal/terms');
    ?>
    <script>
    jQuery(document).ready(function($) {
        // Add legal links to the top navigation bar
        var legalLinksHTML = '<li class="dropdown legal-pages-dropdown" style="margin-left: 15px;">' +
            '<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">' +
            '<i class="fa fa-gavel"></i> Pages Légales <span class="caret"></span>' +
            '</a>' +
            '<ul class="dropdown-menu">' +
            '<li><a href="<?php echo $privacy_url; ?>" target="_blank">' +
            '<i class="fa fa-shield"></i> Politique de Confidentialité</a></li>' +
            '<li><a href="<?php echo $terms_url; ?>" target="_blank">' +
            '<i class="fa fa-file-text"></i> Conditions d\'Utilisation</a></li>' +
            '</ul>' +
            '</li>';

        // Try to add to navbar (admin interface)
        if ($('nav.navbar .navbar-nav').length > 0) {
            $('nav.navbar .navbar-nav').first().append(legalLinksHTML);
        }

        // Try to add to header (client portal)
        if ($('.header .navbar-nav').length > 0) {
            $('.header .navbar-nav').first().append(legalLinksHTML);
        }

        // Alternative: add to top-right navbar
        if ($('.navbar-right').length > 0 && $('.legal-pages-dropdown').length === 0) {
            $('.navbar-right').first().prepend(legalLinksHTML);
        }
    });
    </script>

    <style>
    .legal-pages-dropdown a {
        white-space: nowrap;
    }

    .legal-pages-dropdown .dropdown-menu {
        min-width: 250px;
    }

    .legal-pages-dropdown .dropdown-menu i {
        margin-right: 8px;
        width: 16px;
        text-align: center;
    }
    </style>
    <?php
}
