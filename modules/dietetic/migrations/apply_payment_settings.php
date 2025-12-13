<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Apply payment gateway settings migration
 * This adds Wave, PayPal, and Orange Money payment settings
 */

$CI = &get_instance();

echo "<h3>Applying Payment Gateway Settings Migration...</h3>";

// Load the SQL migration file
$migration_sql = file_get_contents(__DIR__ . '/add_payment_gateway_settings.sql');

// Replace table prefix placeholders
$migration_sql = str_replace('`tbldietic_', '`' . db_prefix() . 'dietic_', $migration_sql);

// Split by semicolon and execute each statement
$statements = array_filter(array_map('trim', explode(';', $migration_sql)));

$success_count = 0;
$error_count = 0;

foreach ($statements as $statement) {
    if (!empty($statement) && !preg_match('/^--/', $statement)) {
        try {
            $CI->db->query($statement);
            echo "✓ Statement executed successfully<br>";
            $success_count++;
        } catch (Exception $e) {
            echo "✗ Error: " . $e->getMessage() . "<br>";
            $error_count++;
        }
    }
}

echo "<br><strong>Migration completed!</strong><br>";
echo "Success: $success_count<br>";
echo "Errors: $error_count<br>";

// Verify the settings were added
echo "<h4>Verifying payment settings...</h4>";

$payment_settings = $CI->db->select('setting_key, setting_value')
    ->from(db_prefix() . 'dietic_settings')
    ->like('setting_key', 'wave_', 'after')
    ->or_like('setting_key', 'paypal_', 'after')
    ->or_like('setting_key', 'orange_money_', 'after')
    ->get()
    ->result();

if ($payment_settings) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Setting Key</th><th>Value</th></tr>";
    foreach ($payment_settings as $setting) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($setting->setting_key) . "</td>";
        echo "<td>" . htmlspecialchars($setting->setting_value) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>⚠ No payment settings found in database</p>";
}

echo "<br><a href='" . admin_url('dietetic/settings') . "'>Go to Settings Page</a>";
