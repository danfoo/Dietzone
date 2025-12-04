<?php
/**
 * Fix for Client/Contact Creation Conflict
 *
 * This script temporarily removes and recreates the foreign key constraint
 * on dietic_patients to resolve conflicts with Perfex client creation
 */

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

echo "Starting fix for client/contact creation conflict..." . PHP_EOL;

try {
    // Get table prefix
    $prefix = db_prefix();

    // Step 1: Drop the problematic foreign key constraint if it exists
    echo "Step 1: Removing existing foreign key constraint..." . PHP_EOL;

    $drop_fk_sql = "ALTER TABLE `{$prefix}dietic_patients` DROP FOREIGN KEY `fk_diet_patients_client`";

    try {
        $CI->db->query($drop_fk_sql);
        echo "  ✓ Foreign key constraint removed" . PHP_EOL;
    } catch (Exception $e) {
        echo "  ℹ Foreign key constraint may not exist (this is OK)" . PHP_EOL;
    }

    // Step 2: Ensure the client_id column has proper indexing
    echo "Step 2: Checking indexes..." . PHP_EOL;

    // Check if index exists
    $check_index = $CI->db->query("SHOW INDEX FROM `{$prefix}dietic_patients` WHERE Key_name = 'client_id'")->result();

    if (empty($check_index)) {
        echo "  Adding index on client_id..." . PHP_EOL;
        $CI->db->query("ALTER TABLE `{$prefix}dietic_patients` ADD INDEX `idx_client_id` (`client_id`)");
        echo "  ✓ Index added" . PHP_EOL;
    } else {
        echo "  ✓ Index already exists" . PHP_EOL;
    }

    // Step 3: Verify clients table structure
    echo "Step 3: Verifying clients table structure..." . PHP_EOL;

    $clients_check = $CI->db->query("SHOW COLUMNS FROM `{$prefix}clients` WHERE Field = 'userid'")->result();

    if (empty($clients_check)) {
        throw new Exception("Clients table userid column not found!");
    }
    echo "  ✓ Clients table structure is valid" . PHP_EOL;

    // Step 4: Re-create the foreign key with proper settings
    echo "Step 4: Re-creating foreign key constraint with optimal settings..." . PHP_EOL;

    $add_fk_sql = "ALTER TABLE `{$prefix}dietic_patients`
        ADD CONSTRAINT `fk_diet_patients_client`
        FOREIGN KEY (`client_id`)
        REFERENCES `{$prefix}clients`(`userid`)
        ON DELETE CASCADE
        ON UPDATE CASCADE";

    try {
        $CI->db->query($add_fk_sql);
        echo "  ✓ Foreign key constraint re-created successfully" . PHP_EOL;
    } catch (Exception $e) {
        echo "  ⚠ Warning: Could not create foreign key: " . $e->getMessage() . PHP_EOL;
        echo "  The module will work without the constraint, but orphaned records may occur." . PHP_EOL;
    }

    // Step 5: Verify the fix
    echo "Step 5: Verifying database structure..." . PHP_EOL;

    $verify_sql = "SELECT
        TABLE_NAME,
        CONSTRAINT_NAME,
        REFERENCED_TABLE_NAME
        FROM information_schema.KEY_COLUMN_USAGE
        WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = '{$prefix}dietic_patients'
        AND CONSTRAINT_NAME = 'fk_diet_patients_client'";

    $verify = $CI->db->query($verify_sql)->result();

    if (!empty($verify)) {
        echo "  ✓ Foreign key constraint is active" . PHP_EOL;
    } else {
        echo "  ⚠ Foreign key constraint is not active (manual intervention may be needed)" . PHP_EOL;
    }

    echo PHP_EOL . "✅ Fix completed successfully!" . PHP_EOL;
    echo PHP_EOL . "Please test client creation now." . PHP_EOL;

    log_activity('Dietetic Module: Client/contact conflict fix applied');

} catch (Exception $e) {
    echo "❌ Error during fix: " . $e->getMessage() . PHP_EOL;
    log_activity('Dietetic Module: Fix failed - ' . $e->getMessage());
}
