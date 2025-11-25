<?php
/**
 * Debug script to check consultation SMS notifications
 * Access via: https://app.dietsenegal.net/modules/dietetic/debug_consultation_sms.php
 *
 * IMPORTANT: Delete this file after debugging!
 */

defined('BASEPATH') or define('BASEPATH', '../../application/');
require_once(dirname(__FILE__) . '/../../application/config/database.php');

$db_config = $db['default'];

try {
    $pdo = new PDO(
        "mysql:host={$db_config['hostname']};dbname={$db_config['database']}",
        $db_config['username'],
        $db_config['password']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h1>Consultation SMS Notification Debug</h1>";
    echo "<p>Checking last 10 consultation notifications...</p>";

    // Get last consultation notifications
    $stmt = $pdo->query("
        SELECT id, patient_id, notification_type, channel, status, recipient,
               error_message, created_at, sent_at
        FROM tbldietic_notification_logs
        WHERE notification_type = 'consultation_scheduled'
        ORDER BY created_at DESC
        LIMIT 10
    ");

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($results)) {
        echo "<p style='color: orange;'><strong>No consultation_scheduled notifications found in logs!</strong></p>";
        echo "<p>This means send_notification() is not being called or the notifications are using a different type.</p>";
    } else {
        echo "<table border='1' cellpadding='5' cellspacing='0' style='border-collapse: collapse;'>";
        echo "<thead><tr>";
        echo "<th>ID</th><th>Patient</th><th>Channel</th><th>Status</th>";
        echo "<th>Recipient</th><th>Error</th><th>Created At</th><th>Sent At</th>";
        echo "</tr></thead><tbody>";

        foreach ($results as $row) {
            $color = $row['status'] === 'sent' ? '#d4edda' : '#f8d7da';
            echo "<tr style='background-color: {$color};'>";
            echo "<td>{$row['id']}</td>";
            echo "<td>{$row['patient_id']}</td>";
            echo "<td><strong>{$row['channel']}</strong></td>";
            echo "<td>{$row['status']}</td>";
            echo "<td>{$row['recipient']}</td>";
            echo "<td>" . ($row['error_message'] ?: '-') . "</td>";
            echo "<td>{$row['created_at']}</td>";
            echo "<td>" . ($row['sent_at'] ?: '-') . "</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
    }

    // Check patient notification preferences
    echo "<hr><h2>Patient #9 Notification Preferences</h2>";
    $stmt = $pdo->query("
        SELECT * FROM tbldietic_notification_preferences WHERE patient_id = 9
    ");
    $prefs = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($prefs) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Setting</th><th>Value</th></tr>";
        foreach ($prefs as $key => $value) {
            echo "<tr><td>{$key}</td><td>" . ($value ?: 'NULL') . "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'><strong>No preferences found for patient #9!</strong></p>";
    }

    // Check LAM SMS configuration
    echo "<hr><h2>LAM SMS Configuration</h2>";
    $stmt = $pdo->query("
        SELECT setting_name, setting_value
        FROM tbldietetic_settings
        WHERE setting_name LIKE '%sms%' OR setting_name LIKE '%lam%'
    ");
    $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($settings) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Setting Name</th><th>Value</th></tr>";
        foreach ($settings as $setting) {
            $value = $setting['setting_value'];
            // Mask password
            if (strpos($setting['setting_name'], 'password') !== false) {
                $value = str_repeat('*', strlen($value));
            }
            echo "<tr><td>{$setting['setting_name']}</td><td>{$value}</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'><strong>No SMS/LAM settings found!</strong></p>";
    }

} catch (Exception $e) {
    echo "<p style='color: red;'><strong>Error: " . htmlspecialchars($e->getMessage()) . "</strong></p>";
}

echo "<hr>";
echo "<p style='color: red;'><strong>⚠️ IMPORTANT: Delete this file after debugging!</strong></p>";
echo "<p>File path: /home/user/Dietzone/modules/dietetic/debug_consultation_sms.php</p>";
?>
