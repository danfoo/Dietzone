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
 * Define module menu items
 */
function dietetic_module_init_menu_items()
{
    $CI = &get_instance();

    if (has_permission('dietetic', '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item('dietetic', [
            'name'     => _l('dietetic'),
            'icon'     => 'fa fa-heartbeat',
            'href'     => admin_url('dietetic'),
            'position' => 15,
        ]);

        // Dashboard
        $CI->app_menu->add_sidebar_children_item('dietetic', [
            'slug'     => 'dietetic-dashboard',
            'name'     => _l('dietetic_dashboard'),
            'icon'     => 'fa fa-tachometer',
            'href'     => admin_url('dietetic'),
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
            'icon'     => 'fa fa-calendar-check-o',
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

        // Dietitians (with ratings)
        $CI->app_menu->add_sidebar_children_item('dietetic', [
            'slug'     => 'dietetic-dietitians',
            'name'     => 'Diététiciens',
            'icon'     => 'fa fa-user-md',
            'href'     => admin_url('dietetic/dietitians'),
            'position' => 5,
        ]);

        // Foods Database
        $CI->app_menu->add_sidebar_children_item('dietetic', [
            'slug'     => 'dietetic-foods',
            'name'     => _l('dietetic_foods'),
            'icon'     => 'fa fa-cutlery',
            'href'     => admin_url('dietetic/foods'),
            'position' => 6,
        ]);

        // Settings
        $CI->app_menu->add_sidebar_children_item('dietetic', [
            'slug'     => 'dietetic-settings',
            'name'     => _l('settings'),
            'icon'     => 'fa fa-cog',
            'href'     => admin_url('dietetic/settings'),
            'position' => 99,
        ]);
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
 * Add custom CSS and JS for admin area
 */
hooks()->add_action('app_admin_head', 'dietetic_add_head_components');

function dietetic_add_head_components()
{
    $CI = &get_instance();
    $module_path = module_dir_url(DIETETIC_MODULE_NAME);

    if (strpos($_SERVER['REQUEST_URI'], '/admin/dietetic') !== false) {
        echo '<link href="' . $module_path . 'assets/css/dietetic.css?v=' . time() . '" rel="stylesheet" type="text/css" />';
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
            echo '<li class="customers-nav-item-dietetic">
                    <a href="' . site_url('dietetic/portal') . '">
                        <i class="fa fa-heartbeat"></i> My Program
                    </a>
                  </li>';
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
 */
hooks()->add_action('after_cron_run', 'dietetic_send_scheduled_reminders');

function dietetic_send_scheduled_reminders()
{
    $CI = &get_instance();
    $CI->load->model('dietetic/dietetic_reminders_model');
    $CI->dietetic_reminders_model->send_pending_reminders();
}

/**
 * Add permissions
 */
hooks()->add_action('admin_init', 'dietetic_permissions');

function dietetic_permissions()
{
    $capabilities = [];

    $capabilities['capabilities'] = [
        'view'   => _l('permission_view') . '(' . _l('permission_global') . ')',
        'create' => _l('permission_create'),
        'edit'   => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];

    register_staff_capabilities('dietetic', $capabilities, _l('dietetic'));
}
