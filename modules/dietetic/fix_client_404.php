<?php
/**
 * Advanced Fix for Client/Contact Creation Conflict
 *
 * This script resolves 404 errors on /admin/clients/client/ when the module is active
 *
 * The problem can be caused by:
 * - Foreign key constraints that are too strict
 * - Database locks during client creation
 * - Transaction conflicts with Perfex's client creation process
 */

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

echo "=== Dietetic Module: Advanced Client/Contact Fix ===" . PHP_EOL;
echo "Fixing 404 errors on client creation pages..." . PHP_EOL . PHP_EOL;

try {
    $prefix = db_prefix();

    // Step 1: Check current FK constraints
    echo "[1/6] Checking foreign key constraints..." . PHP_EOL;

    $check_fk_sql = "SELECT
        CONSTRAINT_NAME,
        TABLE_NAME,
        REFERENCED_TABLE_NAME,
        UPDATE_RULE,
        DELETE_RULE
        FROM information_schema.REFERENTIAL_CONSTRAINTS
        WHERE CONSTRAINT_SCHEMA = DATABASE()
        AND TABLE_NAME = '{$prefix}dietic_patients'";

    $existing_fks = $CI->db->query($check_fk_sql)->result();

    if (!empty($existing_fks)) {
        echo "  Found " . count($existing_fks) . " foreign key constraint(s)" . PHP_EOL;
        foreach ($existing_fks as $fk) {
            echo "  - {$fk->CONSTRAINT_NAME}: {$fk->TABLE_NAME} -> {$fk->REFERENCED_TABLE_NAME}" . PHP_EOL;
            echo "    DELETE: {$fk->DELETE_RULE}, UPDATE: {$fk->UPDATE_RULE}" . PHP_EOL;
        }
    } else {
        echo "  No foreign keys found" . PHP_EOL;
    }

    // Step 2: Temporarily disable foreign key checks
    echo PHP_EOL . "[2/6] Temporarily disabling foreign key checks..." . PHP_EOL;
    $CI->db->query("SET FOREIGN_KEY_CHECKS = 0");
    echo "  ✓ Foreign key checks disabled" . PHP_EOL;

    // Step 3: Drop problematic constraint if it exists
    echo PHP_EOL . "[3/6] Removing problematic constraints..." . PHP_EOL;

    $constraints_to_remove = ['fk_diet_patients_client', 'fk_diet_patients_staff'];

    foreach ($constraints_to_remove as $constraint) {
        try {
            $drop_sql = "ALTER TABLE `{$prefix}dietic_patients` DROP FOREIGN KEY `{$constraint}`";
            $CI->db->query($drop_sql);
            echo "  ✓ Removed constraint: {$constraint}" . PHP_EOL;
        } catch (Exception $e) {
            echo "  ℹ Constraint {$constraint} may not exist (OK)" . PHP_EOL;
        }
    }

    // Step 4: Verify table structure and data integrity
    echo PHP_EOL . "[4/6] Verifying data integrity..." . PHP_EOL;

    // Check for orphaned records
    $orphaned_check = "SELECT COUNT(*) as count
        FROM `{$prefix}dietic_patients` p
        LEFT JOIN `{$prefix}clients` c ON p.client_id = c.userid
        WHERE c.userid IS NULL";

    $orphaned = $CI->db->query($orphaned_check)->row();

    if ($orphaned->count > 0) {
        echo "  ⚠ Warning: Found {$orphaned->count} orphaned patient record(s)" . PHP_EOL;
        echo "  These will be preserved but may need manual cleanup" . PHP_EOL;
    } else {
        echo "  ✓ No orphaned records found" . PHP_EOL;
    }

    // Step 5: Re-create constraints with optimal settings
    echo PHP_EOL . "[5/6] Re-creating foreign key constraints with optimal settings..." . PHP_EOL;

    // Client FK - with CASCADE for both DELETE and UPDATE
    $client_fk_sql = "ALTER TABLE `{$prefix}dietic_patients`
        ADD CONSTRAINT `fk_diet_patients_client`
        FOREIGN KEY (`client_id`)
        REFERENCES `{$prefix}clients`(`userid`)
        ON DELETE CASCADE
        ON UPDATE CASCADE";

    try {
        $CI->db->query($client_fk_sql);
        echo "  ✓ Client foreign key created (CASCADE/CASCADE)" . PHP_EOL;
    } catch (Exception $e) {
        echo "  ⚠ Could not create client FK: " . substr($e->getMessage(), 0, 100) . PHP_EOL;
        echo "    Module will work without strict FK, but manual cleanup may be needed" . PHP_EOL;
    }

    // Staff FK - with RESTRICT to prevent accidental deletions
    $staff_fk_sql = "ALTER TABLE `{$prefix}dietic_patients`
        ADD CONSTRAINT `fk_diet_patients_staff`
        FOREIGN KEY (`dietitian_id`)
        REFERENCES `{$prefix}staff`(`staffid`)
        ON DELETE RESTRICT
        ON UPDATE CASCADE";

    try {
        $CI->db->query($staff_fk_sql);
        echo "  ✓ Staff foreign key created (RESTRICT/CASCADE)" . PHP_EOL;
    } catch (Exception $e) {
        echo "  ⚠ Could not create staff FK: " . substr($e->getMessage(), 0, 100) . PHP_EOL;
    }

    // Step 6: Re-enable foreign key checks
    echo PHP_EOL . "[6/6] Re-enabling foreign key checks..." . PHP_EOL;
    $CI->db->query("SET FOREIGN_KEY_CHECKS = 1");
    echo "  ✓ Foreign key checks re-enabled" . PHP_EOL;

    // Final verification
    echo PHP_EOL . "=== Final Verification ===" . PHP_EOL;

    $final_check = $CI->db->query($check_fk_sql)->result();

    if (!empty($final_check)) {
        echo "✅ Foreign key constraints are now active:" . PHP_EOL;
        foreach ($final_check as $fk) {
            echo "  - {$fk->CONSTRAINT_NAME}" . PHP_EOL;
        }
    } else {
        echo "⚠️  No foreign key constraints active (module will work but without strict referential integrity)" . PHP_EOL;
    }

    echo PHP_EOL . "=== Fix Completed Successfully ===" . PHP_EOL;
    echo PHP_EOL . "✅ You can now try to create a client at /admin/clients/client/" . PHP_EOL;
    echo PHP_EOL . "If you still experience issues:" . PHP_EOL;
    echo "  1. Check PHP error logs for fatal errors" . PHP_EOL;
    echo "  2. Check Perfex activity log (Admin > Utilities > Activity Log)" . PHP_EOL;
    echo "  3. Try accessing /admin/clients directly" . PHP_EOL;
    echo "  4. Temporarily disable the module to confirm it's the cause" . PHP_EOL;

    log_activity('Dietetic Module: Advanced client/contact conflict fix applied successfully');

} catch (Exception $e) {
    echo PHP_EOL . "❌ Error during fix: " . $e->getMessage() . PHP_EOL;
    echo "Stack trace:" . PHP_EOL . $e->getTraceAsString() . PHP_EOL;

    // Try to re-enable FK checks even if there was an error
    try {
        $CI->db->query("SET FOREIGN_KEY_CHECKS = 1");
    } catch (Exception $e2) {
        // Ignore
    }

    log_activity('Dietetic Module: Advanced fix failed - ' . $e->getMessage());
}
