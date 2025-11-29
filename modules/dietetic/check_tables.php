<?php
/**
 * Check table structures for payments and invoices
 */

define('BASEPATH', TRUE);

$config_path = dirname(__FILE__) . '/../../application/config/app-config.php';
require_once($config_path);

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

echo "<h2>Table Structure Check</h2>";

$tables = [
    'dietic_payments' => 'Payments Table',
    'dietic_invoices' => 'Invoices Table',
    'dietic_refunds' => 'Refunds Table'
];

foreach ($tables as $table => $label) {
    $full_table = db_prefix() . $table;

    echo "<h3>$label ($full_table)</h3>";

    try {
        $stmt = $pdo->query("DESCRIBE $full_table");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";

        foreach ($columns as $col) {
            echo "<tr>";
            echo "<td><strong>" . htmlspecialchars($col['Field']) . "</strong></td>";
            echo "<td>" . htmlspecialchars($col['Type']) . "</td>";
            echo "<td>" . htmlspecialchars($col['Null']) . "</td>";
            echo "<td>" . htmlspecialchars($col['Key']) . "</td>";
            echo "<td>" . htmlspecialchars($col['Default'] ?? 'NULL') . "</td>";
            echo "<td>" . htmlspecialchars($col['Extra']) . "</td>";
            echo "</tr>";
        }

        echo "</table>";
        echo "<p>Total columns: " . count($columns) . "</p>";

    } catch (PDOException $e) {
        echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    }

    echo "<hr>";
}
