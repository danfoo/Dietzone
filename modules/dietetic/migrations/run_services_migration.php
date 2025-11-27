<?php
/**
 * Run Services & Billing Migration
 *
 * This script can be executed via:
 * 1. Direct URL: /modules/dietetic/migrations/run_services_migration.php
 * 2. Or included from another file
 */

// Allow execution from CLI or web
if (php_sapi_name() !== 'cli') {
    define('BASEPATH', true);
    require_once(__DIR__ . '/../../../application/libraries/App_Controller.php');
}

$CI = &get_instance();

if (!$CI) {
    die('CodeIgniter instance not found. Please run this from the Perfex CRM context.');
}

echo "🚀 Starting Services & Billing Migration...\n\n";

// Load the migration SQL file
$migration_sql = file_get_contents(__DIR__ . '/add_services_subscriptions_billing.sql');

if (!$migration_sql) {
    die("❌ Error: Migration file not found!\n");
}

// Replace table prefix
$migration_sql = str_replace('tbldietic_', db_prefix() . 'dietic_', $migration_sql);

// Remove comments and split into statements
$migration_sql = preg_replace('/^--.*$/m', '', $migration_sql); // Remove single-line comments
$migration_sql = preg_replace('/\/\*.*?\*\//s', '', $migration_sql); // Remove multi-line comments

// Split by semicolon
$statements = array_filter(array_map('trim', explode(';', $migration_sql)));

$success_count = 0;
$error_count = 0;
$errors = [];

echo "📊 Total statements to execute: " . count($statements) . "\n\n";

foreach ($statements as $index => $statement) {
    if (empty($statement) || strlen($statement) < 10) {
        continue;
    }

    try {
        $CI->db->query($statement);
        $success_count++;

        // Extract table name for logging
        if (preg_match('/CREATE TABLE.*?`([^`]+)`/i', $statement, $matches)) {
            echo "✅ Created table: {$matches[1]}\n";
        } elseif (preg_match('/ALTER TABLE.*?`([^`]+)`/i', $statement, $matches)) {
            echo "✅ Altered table: {$matches[1]}\n";
        } elseif (preg_match('/INSERT INTO.*?`([^`]+)`/i', $statement, $matches)) {
            echo "✅ Inserted into: {$matches[1]}\n";
        } else {
            echo "✅ Statement " . ($index + 1) . " executed successfully\n";
        }

    } catch (Exception $e) {
        $error_count++;
        $error_msg = $e->getMessage();
        $errors[] = [
            'statement' => substr($statement, 0, 100) . '...',
            'error' => $error_msg
        ];

        // Only show error if it's not a "table already exists" or "duplicate key" error
        if (
            strpos($error_msg, 'already exists') === false &&
            strpos($error_msg, 'Duplicate') === false &&
            strpos($error_msg, 'duplicate') === false
        ) {
            echo "⚠️  Error in statement " . ($index + 1) . ": " . substr($error_msg, 0, 100) . "\n";
        } else {
            // Ignore "already exists" errors (migration already run)
            $error_count--;
        }
    }
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "📈 Migration Summary:\n";
echo "   ✅ Successful: $success_count\n";
echo "   ⚠️  Errors: $error_count\n";
echo str_repeat("=", 60) . "\n\n";

if ($error_count > 0 && count($errors) > 0) {
    echo "❌ Errors encountered:\n";
    foreach ($errors as $i => $error) {
        echo "\n" . ($i + 1) . ". Statement: " . $error['statement'] . "\n";
        echo "   Error: " . $error['error'] . "\n";
    }
}

if ($error_count === 0) {
    echo "🎉 Migration completed successfully!\n\n";

    // Log activity
    if (function_exists('log_activity')) {
        log_activity('Dietetic Services & Billing tables created');
    }

    echo "✨ The following tables have been created:\n";
    echo "   - " . db_prefix() . "dietic_service_plans\n";
    echo "   - " . db_prefix() . "dietic_subscriptions\n";
    echo "   - " . db_prefix() . "dietic_invoices\n";
    echo "   - " . db_prefix() . "dietic_payments\n";
    echo "   - " . db_prefix() . "dietic_commission_settings\n";
    echo "   - " . db_prefix() . "dietic_revenue_shares\n";
    echo "   - " . db_prefix() . "dietic_commission_payments\n";
    echo "   - " . db_prefix() . "dietic_invoice_sequence\n\n";

    echo "📝 Default commission settings have been inserted:\n";
    echo "   - Platform referrals: 60% dietitian / 40% platform\n";
    echo "   - Dietitian referrals: 80% dietitian / 20% platform\n\n";
} else {
    echo "⚠️  Migration completed with some errors. Please review the errors above.\n\n";
}

return ($error_count === 0);
