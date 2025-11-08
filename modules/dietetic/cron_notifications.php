<?php
/**
 * Dietetic Notifications Cron Job
 *
 * This script should be run every hour via cron
 * Cron command: 0 * * * * php /path/to/cron_notifications.php
 *
 * Or setup in Perfex CRM: Setup > Settings > Cron Job
 */

defined('BASEPATH') or define('BASEPATH', true);

require_once(__DIR__ . '/../../application/config/app-config.php');
require_once(__DIR__ . '/../../application/config/config.php');

// Bootstrap CodeIgniter
$_SERVER['CI_ENV'] = 'production';
require_once(BASEPATH . 'core/CodeIgniter.php');

// Load CI instance
$CI =& get_instance();

// Load required models
$CI->load->model('dietetic/dietetic_notifications_model');

echo "========================================\n";
echo "Dietetic Notifications Cron Job\n";
echo "Started at: " . date('Y-m-d H:i:s') . "\n";
echo "========================================\n\n";

$total_sent = 0;
$total_failed = 0;

// ==================== WEIGHT REMINDERS ====================
echo "Checking weight reminders...\n";

$weight_patients = $CI->dietetic_notifications_model->get_patients_for_weight_reminder();

if (!empty($weight_patients)) {
    echo "Found " . count($weight_patients) . " patients for weight reminder\n";

    foreach ($weight_patients as $patient) {
        echo "  - Sending to: {$patient->firstname} {$patient->lastname}\n";
        $result = $CI->dietetic_notifications_model->send_weight_reminder($patient);

        $success_count = array_filter($result, function($r) { return $r === true; });
        if (!empty($success_count)) {
            $total_sent += count($success_count);
            echo "    ✓ Sent via: " . implode(', ', array_keys($success_count)) . "\n";
        } else {
            $total_failed++;
            echo "    ✗ Failed to send\n";
        }
    }
} else {
    echo "No patients need weight reminder at this time\n";
}

echo "\n";

// ==================== WATER REMINDERS ====================
echo "Checking water reminders...\n";

$water_patients = $CI->dietetic_notifications_model->get_patients_for_water_reminder();

if (!empty($water_patients)) {
    echo "Found " . count($water_patients) . " patients for water reminder\n";

    foreach ($water_patients as $patient) {
        echo "  - Sending to: {$patient->firstname} {$patient->lastname}\n";
        $result = $CI->dietetic_notifications_model->send_water_reminder($patient);

        $success_count = array_filter($result, function($r) { return $r === true; });
        if (!empty($success_count)) {
            $total_sent += count($success_count);
            echo "    ✓ Sent via: " . implode(', ', array_keys($success_count)) . "\n";
        } else {
            $total_failed++;
            echo "    ✗ Failed to send\n";
        }
    }
} else {
    echo "No patients need water reminder at this time\n";
}

echo "\n";

// ==================== SUMMARY ====================
echo "========================================\n";
echo "Summary:\n";
echo "  Total sent: {$total_sent}\n";
echo "  Total failed: {$total_failed}\n";
echo "Completed at: " . date('Y-m-d H:i:s') . "\n";
echo "========================================\n";
