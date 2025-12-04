<?php
/**
 * Diagnostic Script for 404 Error on Client Pages
 *
 * This script helps identify the exact cause of 404 errors on /admin/clients/client/
 */

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

echo "=== Dietetic Module: 404 Diagnostic Tool ===" . PHP_EOL;
echo "Analyzing potential causes of 404 errors..." . PHP_EOL . PHP_EOL;

$issues_found = 0;
$warnings = 0;

try {
    // Test 1: Check if hooks are causing errors
    echo "[Test 1/8] Testing customer_profile_tabs hook..." . PHP_EOL;

    ob_start();
    try {
        dietetic_add_customer_profile_tab(8); // Test with client ID 8
        $output = ob_get_clean();

        if (strpos($output, 'error') !== false || strpos($output, 'warning') !== false) {
            echo "  ⚠ Hook may be producing errors" . PHP_EOL;
            $warnings++;
        } else {
            echo "  ✓ Hook executes without visible errors" . PHP_EOL;
        }
    } catch (Exception $e) {
        ob_end_clean();
        echo "  ❌ Hook throws exception: " . $e->getMessage() . PHP_EOL;
        $issues_found++;
    }

    // Test 2: Check if models can be loaded
    echo PHP_EOL . "[Test 2/8] Testing model loading..." . PHP_EOL;

    try {
        $CI->load->model('dietetic/dietetic_patients_model');
        echo "  ✓ dietetic_patients_model loads successfully" . PHP_EOL;
    } catch (Exception $e) {
        echo "  ❌ Cannot load dietetic_patients_model: " . $e->getMessage() . PHP_EOL;
        $issues_found++;
    }

    try {
        $CI->load->model('clients_model');
        echo "  ✓ clients_model loads successfully" . PHP_EOL;
    } catch (Exception $e) {
        echo "  ❌ Cannot load clients_model: " . $e->getMessage() . PHP_EOL;
        $issues_found++;
    }

    // Test 3: Check if client 8 exists
    echo PHP_EOL . "[Test 3/8] Checking if client ID 8 exists..." . PHP_EOL;

    $prefix = db_prefix();
    $client = $CI->db->query("SELECT userid, company FROM {$prefix}clients WHERE userid = 8")->row();

    if ($client) {
        echo "  ✓ Client 8 exists: {$client->company}" . PHP_EOL;
    } else {
        echo "  ⚠ Client 8 does not exist in database" . PHP_EOL;
        $warnings++;
    }

    // Test 4: Check for routing conflicts
    echo PHP_EOL . "[Test 4/8] Checking for routing conflicts..." . PHP_EOL;

    $dietetic_controllers = [
        'Consultations', 'Dietetic', 'Dietitians', 'Foods',
        'Measurements', 'Patients', 'Portal', 'Programs'
    ];

    $conflicts = [];
    foreach ($dietetic_controllers as $controller) {
        if (in_array(strtolower($controller), ['client', 'clients', 'contact', 'contacts'])) {
            $conflicts[] = $controller;
        }
    }

    if (empty($conflicts)) {
        echo "  ✓ No direct controller name conflicts found" . PHP_EOL;
    } else {
        echo "  ❌ Controller name conflicts: " . implode(', ', $conflicts) . PHP_EOL;
        $issues_found++;
    }

    // Test 5: Check database connectivity
    echo PHP_EOL . "[Test 5/8] Testing database connectivity..." . PHP_EOL;

    try {
        $result = $CI->db->query("SELECT 1")->row();
        echo "  ✓ Database connection is active" . PHP_EOL;
    } catch (Exception $e) {
        echo "  ❌ Database connection error: " . $e->getMessage() . PHP_EOL;
        $issues_found++;
    }

    // Test 6: Check foreign key constraints status
    echo PHP_EOL . "[Test 6/8] Checking foreign key constraints..." . PHP_EOL;

    $fk_check = "SELECT CONSTRAINT_NAME, TABLE_NAME, REFERENCED_TABLE_NAME
        FROM information_schema.KEY_COLUMN_USAGE
        WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = '{$prefix}dietic_patients'
        AND REFERENCED_TABLE_NAME IS NOT NULL";

    $fks = $CI->db->query($fk_check)->result();

    if (empty($fks)) {
        echo "  ⚠ No foreign keys found (may cause data integrity issues)" . PHP_EOL;
        $warnings++;
    } else {
        echo "  ✓ Found " . count($fks) . " foreign key constraint(s)" . PHP_EOL;
        foreach ($fks as $fk) {
            echo "    - {$fk->CONSTRAINT_NAME}: {$fk->TABLE_NAME} -> {$fk->REFERENCED_TABLE_NAME}" . PHP_EOL;
        }
    }

    // Test 7: Check for PHP errors in logs
    echo PHP_EOL . "[Test 7/8] Checking for recent activity log errors..." . PHP_EOL;

    $recent_logs = $CI->db->query("SELECT * FROM {$prefix}activity_log
        WHERE description LIKE '%Dietetic%'
        OR description LIKE '%error%'
        ORDER BY date DESC
        LIMIT 10")->result();

    if (empty($recent_logs)) {
        echo "  ✓ No recent Dietetic-related log entries" . PHP_EOL;
    } else {
        echo "  ℹ Found " . count($recent_logs) . " recent log entries:" . PHP_EOL;
        foreach ($recent_logs as $log) {
            $short_desc = substr($log->description, 0, 80);
            echo "    - [{$log->date}] {$short_desc}" . PHP_EOL;
        }
    }

    // Test 8: Check module activation status
    echo PHP_EOL . "[Test 8/8] Checking module activation status..." . PHP_EOL;

    $module_status = $CI->db->query("SELECT * FROM {$prefix}modules WHERE module_name = 'dietetic'")->row();

    if ($module_status) {
        echo "  ✓ Module is registered in database" . PHP_EOL;
        echo "    - Active: " . ($module_status->active ? 'Yes' : 'No') . PHP_EOL;
        echo "    - Installed: " . (isset($module_status->installed) && $module_status->installed ? 'Yes' : 'N/A') . PHP_EOL;
    } else {
        echo "  ❌ Module is NOT registered in database" . PHP_EOL;
        $issues_found++;
    }

    // Final report
    echo PHP_EOL . "=== Diagnostic Summary ===" . PHP_EOL;

    if ($issues_found === 0 && $warnings === 0) {
        echo "✅ No issues detected. The 404 error may be caused by:" . PHP_EOL;
        echo "   1. CodeIgniter routing cache (try clearing cache)" . PHP_EOL;
        echo "   2. .htaccess configuration" . PHP_EOL;
        echo "   3. Server-level redirects" . PHP_EOL;
        echo "   4. Permission issues" . PHP_EOL;
    } else {
        echo "⚠️  Diagnostic Results:" . PHP_EOL;
        echo "   - Critical Issues: {$issues_found}" . PHP_EOL;
        echo "   - Warnings: {$warnings}" . PHP_EOL;
        echo PHP_EOL;
        echo "Recommended Actions:" . PHP_EOL;

        if ($issues_found > 0) {
            echo "   1. Fix critical issues listed above" . PHP_EOL;
            echo "   2. Check PHP error logs for fatal errors" . PHP_EOL;
            echo "   3. Try disabling/re-enabling the module" . PHP_EOL;
        }

        if ($warnings > 0) {
            echo "   4. Address warnings to improve module stability" . PHP_EOL;
        }
    }

    echo PHP_EOL . "Next Steps:" . PHP_EOL;
    echo "   1. Temporarily disable the module and test /admin/clients/client/8" . PHP_EOL;
    echo "   2. If it works with module disabled, the issue is confirmed to be module-related" . PHP_EOL;
    echo "   3. Check PHP error log at the exact moment you access /admin/clients/client/8" . PHP_EOL;
    echo "   4. Run: tail -f /var/log/apache2/error.log (or nginx equivalent)" . PHP_EOL;

    log_activity('Dietetic Module: 404 diagnostic completed - Issues: ' . $issues_found . ', Warnings: ' . $warnings);

} catch (Exception $e) {
    echo PHP_EOL . "❌ Diagnostic failed with error: " . $e->getMessage() . PHP_EOL;
    echo "Stack trace:" . PHP_EOL . $e->getTraceAsString() . PHP_EOL;
    log_activity('Dietetic Module: 404 diagnostic failed - ' . $e->getMessage());
}
