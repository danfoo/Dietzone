<?php
/**
 * Temporary Hook Disabler
 *
 * This file can be included in dietetic.php to temporarily disable problematic hooks
 * Use this for testing to confirm if hooks are causing the 404 error
 *
 * INSTRUCTIONS:
 * 1. Add this line to the TOP of dietetic.php (after the opening <?php tag):
 *    require_once(__DIR__ . '/disable_hooks_temporarily.php');
 *
 * 2. Test if /admin/clients/client/8 works
 *
 * 3. If it works, the issue is confirmed to be in the hooks
 *
 * 4. Remove the require_once line to re-enable hooks
 */

defined('BASEPATH') or exit('No direct script access allowed');

// Create dummy functions that do nothing
if (!function_exists('dietetic_add_customer_profile_tab_disabled')) {
    function dietetic_add_customer_profile_tab_disabled($client_id) {
        // Do nothing - hook is disabled for testing
        log_activity('Dietetic: customer_profile_tabs hook is DISABLED for testing');
    }
}

if (!function_exists('dietetic_add_portal_menu_disabled')) {
    function dietetic_add_portal_menu_disabled() {
        // Do nothing - hook is disabled for testing
    }
}

// Log that hooks are disabled
if (function_exists('log_activity')) {
    log_activity('Dietetic Module: Hooks temporarily DISABLED for 404 diagnosis');
}

echo "<!-- Dietetic Module: Hooks are temporarily DISABLED for testing -->\n";
