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

// ==================== CONSULTATION REMINDERS (DAY BEFORE) ====================
echo "Checking consultation reminders (1 day before)...\n";

$consultations_day = $CI->dietetic_notifications_model->get_consultations_for_day_reminder();

if (!empty($consultations_day)) {
    echo "Found " . count($consultations_day) . " consultations tomorrow\n";

    foreach ($consultations_day as $consultation) {
        $dietitian_name = $consultation->dietitian_firstname . ' ' . $consultation->dietitian_lastname;
        echo "  - Reminder for patient {$consultation->patient_id} (consultation with {$dietitian_name})\n";

        $result = $CI->dietetic_notifications_model->notify_consultation_reminder_day(
            $consultation->patient_id,
            $consultation->consultation_date,
            $consultation->consultation_time,
            $dietitian_name
        );

        if ($result) {
            $success_count = array_filter($result, function($r) { return $r === true; });
            if (!empty($success_count)) {
                $total_sent += count($success_count);
                echo "    ✓ Sent via: " . implode(', ', array_keys($success_count)) . "\n";
            } else {
                $total_failed++;
                echo "    ✗ Failed to send\n";
            }
        } else {
            echo "    - Skipped (preferences disabled)\n";
        }
    }
} else {
    echo "No consultations tomorrow\n";
}

echo "\n";

// ==================== CONSULTATION REMINDERS (1 HOUR BEFORE) ====================
echo "Checking consultation reminders (1 hour before)...\n";

$consultations_hour = $CI->dietetic_notifications_model->get_consultations_for_hour_reminder();

if (!empty($consultations_hour)) {
    echo "Found " . count($consultations_hour) . " consultations starting in ~1 hour\n";

    foreach ($consultations_hour as $consultation) {
        $dietitian_name = $consultation->dietitian_firstname . ' ' . $consultation->dietitian_lastname;
        echo "  - Reminder for patient {$consultation->patient_id}\n";

        $result = $CI->dietetic_notifications_model->notify_consultation_reminder_hour(
            $consultation->patient_id,
            $consultation->consultation_time,
            $dietitian_name
        );

        if ($result) {
            $success_count = array_filter($result, function($r) { return $r === true; });
            if (!empty($success_count)) {
                $total_sent += count($success_count);
                echo "    ✓ Sent via: " . implode(', ', array_keys($success_count)) . "\n";
            } else {
                $total_failed++;
                echo "    ✗ Failed to send\n";
            }
        } else {
            echo "    - Skipped (preferences disabled)\n";
        }
    }
} else {
    echo "No consultations starting soon\n";
}

echo "\n";

// ==================== FOOD ENTRY REMINDERS ====================
// Only run at specific times (e.g., 18:00 to remind for the day)
$current_hour = (int)date('H');

if ($current_hour == 18) { // 6 PM reminder
    echo "Checking food entry reminders...\n";

    $patients_to_remind = $CI->dietetic_notifications_model->get_patients_for_food_entry_reminder();

    if (!empty($patients_to_remind)) {
        echo "Found " . count($patients_to_remind) . " patients who haven't submitted today\n";

        foreach ($patients_to_remind as $patient) {
            echo "  - Reminder for patient {$patient->patient_id}\n";

            $result = $CI->dietetic_notifications_model->send_food_entry_reminder($patient->patient_id);

            if ($result) {
                $success_count = array_filter($result, function($r) { return $r === true; });
                if (!empty($success_count)) {
                    $total_sent += count($success_count);
                    echo "    ✓ Sent via: " . implode(', ', array_keys($success_count)) . "\n";
                } else {
                    $total_failed++;
                    echo "    ✗ Failed to send\n";
                }
            } else {
                echo "    - Skipped (preferences disabled)\n";
            }
        }
    } else {
        echo "All patients have submitted their food entry or reminders are disabled\n";
    }

    echo "\n";
} else {
    echo "Food entry reminders: Not scheduled at this hour (runs at 18:00)\n\n";
}

// ==================== SUMMARY ====================
echo "========================================\n";
echo "Summary:\n";
echo "  Total sent: {$total_sent}\n";
echo "  Total failed: {$total_failed}\n";
echo "Completed at: " . date('Y-m-d H:i:s') . "\n";
echo "========================================\n";
