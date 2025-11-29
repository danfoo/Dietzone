<?php
/**
 * Test script to debug refunds page 500 error
 * Access via: /modules/dietetic/test_refunds.php
 */

define('BASEPATH', TRUE);

// Load Perfex configuration
$config_path = dirname(__FILE__) . '/../../application/config/app-config.php';
require_once($config_path);

// Connect to database
try {
    $dsn = "mysql:host=" . APP_DB_HOSTNAME . ";dbname=" . APP_DB_NAME . ";charset=utf8mb4";
    $pdo = new PDO($dsn, APP_DB_USERNAME, APP_DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

function db_prefix() {
    return defined('APP_DB_PREFIX') ? APP_DB_PREFIX : 'tbl';
}

echo "<h2>Test Refunds Query</h2>";

// Test the exact query from the model
$sql = "SELECT r.*,
        p.payment_reference,
        i.invoice_number,
        c.company as patient_name,
        CONCAT(st.firstname, ' ', st.lastname) as initiated_by_name
    FROM " . db_prefix() . "dietic_refunds r
    LEFT JOIN " . db_prefix() . "dietic_payments p ON p.id = r.payment_id
    LEFT JOIN " . db_prefix() . "dietic_invoices i ON i.id = r.invoice_id
    LEFT JOIN " . db_prefix() . "dietic_patients pat ON pat.id = r.patient_id
    LEFT JOIN " . db_prefix() . "clients c ON c.userid = pat.client_id
    LEFT JOIN " . db_prefix() . "staff st ON st.staffid = r.initiated_by
    ORDER BY r.created_at DESC";

echo "<h3>SQL Query:</h3>";
echo "<pre>" . htmlspecialchars($sql) . "</pre>";

try {
    $stmt = $pdo->query($sql);
    $refunds = $stmt->fetchAll(PDO::FETCH_OBJ);

    echo "<h3>Results:</h3>";
    echo "<p>Found " . count($refunds) . " refund(s)</p>";

    if (count($refunds) > 0) {
        echo "<h4>First Refund Data:</h4>";
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        echo "<tr><th>Field</th><th>Value</th><th>Type</th></tr>";

        $first_refund = $refunds[0];
        foreach ($first_refund as $key => $value) {
            $type = gettype($value);
            $display_value = $value === null ? '<em>NULL</em>' : htmlspecialchars($value);
            echo "<tr>";
            echo "<td><strong>" . htmlspecialchars($key) . "</strong></td>";
            echo "<td>" . $display_value . "</td>";
            echo "<td>" . $type . "</td>";
            echo "</tr>";
        }
        echo "</table>";

        echo "<h4>Check Required Fields:</h4>";
        $required_fields = ['id', 'patient_id', 'patient_name', 'invoice_id', 'invoice_number',
                           'refund_type', 'refund_amount', 'currency', 'reason', 'status', 'created_at'];

        echo "<ul>";
        foreach ($required_fields as $field) {
            if (isset($first_refund->$field)) {
                if ($first_refund->$field === null) {
                    echo "<li style='color: orange;'>✓ <strong>$field</strong>: exists but is NULL</li>";
                } else {
                    echo "<li style='color: green;'>✓ <strong>$field</strong>: " . htmlspecialchars($first_refund->$field) . "</li>";
                }
            } else {
                echo "<li style='color: red;'>✗ <strong>$field</strong>: MISSING</li>";
            }
        }
        echo "</ul>";

        // Test PHP functions that might fail
        echo "<h4>Test PHP Functions:</h4>";
        echo "<ul>";

        // Test character_limiter on reason
        if (function_exists('character_limiter')) {
            echo "<li>character_limiter() function exists</li>";
        } else {
            echo "<li style='color: red;'>character_limiter() function NOT FOUND - this is likely the issue!</li>";
        }

        // Test app_format_money
        if (function_exists('app_format_money')) {
            echo "<li>app_format_money() function exists</li>";
        } else {
            echo "<li style='color: red;'>app_format_money() function NOT FOUND - this could be an issue!</li>";
        }

        echo "</ul>";
    }

} catch (PDOException $e) {
    echo "<p style='color: red;'>Error executing query: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='debug_views.php'>Back to Debug Views</a></p>";
