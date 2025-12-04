<?php
/**
 * Script de test pour le Cron des Notifications Dietetic
 *
 * Ce script permet de tester manuellement l'envoi des notifications
 * sans attendre l'exécution du cron automatique.
 *
 * Usage:
 *   php modules/dietetic/test_cron.php
 *
 * Ou via navigateur (en mode développement uniquement) :
 *   https://app.dietsenegal.net/modules/dietetic/test_cron.php
 */

// Security check - Only allow in development or via CLI
if (php_sapi_name() !== 'cli') {
    // If not CLI, check for dev mode or secret key
    $dev_mode = isset($_GET['dev_mode']) && $_GET['dev_mode'] === '1';
    $secret_key = isset($_GET['key']) ? $_GET['key'] : '';
    $valid_key = 'dietetic_test_2024'; // Change this in production

    if (!$dev_mode || $secret_key !== $valid_key) {
        die("Accès refusé. Utilisez la ligne de commande ou ajoutez ?dev_mode=1&key=dietetic_test_2024");
    }
}

// Set execution mode
$is_cli = php_sapi_name() === 'cli';

// Bootstrap Perfex CRM
define('BASEPATH', dirname(__DIR__, 2) . '/');

require_once(BASEPATH . 'application/config/app-config.php');
require_once(BASEPATH . 'application/config/config.php');

// Load CodeIgniter
$_SERVER['CI_ENV'] = 'development';
require_once(BASEPATH . 'index.php');

// Get CI instance
$CI =& get_instance();

// Load required models
$CI->load->model('dietetic/dietetic_notifications_model');

// Output header
function output($msg, $type = 'info') {
    global $is_cli;

    $colors = [
        'success' => "\033[32m",
        'error' => "\033[31m",
        'warning' => "\033[33m",
        'info' => "\033[36m",
        'reset' => "\033[0m"
    ];

    if ($is_cli) {
        echo $colors[$type] . $msg . $colors['reset'] . "\n";
    } else {
        $html_colors = [
            'success' => 'green',
            'error' => 'red',
            'warning' => 'orange',
            'info' => 'blue'
        ];
        echo "<div style='color: {$html_colors[$type]}; font-family: monospace;'>" . htmlspecialchars($msg) . "</div>";
    }
}

if (!$is_cli) {
    echo "<html><head><title>Test Cron Dietetic</title></head><body style='padding: 20px; background: #f5f5f5;'>";
    echo "<h1>Test du Cron des Notifications Dietetic</h1><hr>";
}

output("========================================", 'info');
output("TEST CRON NOTIFICATIONS DIETETIC", 'info');
output("Démarré à : " . date('Y-m-d H:i:s'), 'info');
output("========================================", 'info');
output("", 'info');

$total_sent = 0;
$total_failed = 0;
$results = [];

// ==================== TEST 1: WEIGHT REMINDERS ====================
output("1️⃣  Test des rappels de pesée...", 'info');

try {
    $weight_patients = $CI->dietetic_notifications_model->get_patients_for_weight_reminder();

    if (!empty($weight_patients)) {
        output("   ✓ Trouvé " . count($weight_patients) . " patient(s) éligible(s)", 'success');

        foreach ($weight_patients as $patient) {
            output("   → Patient : {$patient->firstname} {$patient->lastname}", 'info');
            $result = $CI->dietetic_notifications_model->send_weight_reminder($patient);

            $success_count = array_filter($result, function($r) { return $r === true; });
            if (!empty($success_count)) {
                $total_sent += count($success_count);
                output("     ✓ Envoyé via : " . implode(', ', array_keys($success_count)), 'success');
            } else {
                $total_failed++;
                output("     ✗ Échec d'envoi", 'error');
            }
        }
    } else {
        output("   ℹ Aucun patient à rappeler pour le moment", 'warning');
    }

    $results['weight'] = ['found' => count($weight_patients ?? []), 'sent' => $total_sent];

} catch (Exception $e) {
    output("   ✗ Erreur : " . $e->getMessage(), 'error');
    $results['weight'] = ['error' => $e->getMessage()];
}

output("", 'info');

// ==================== TEST 2: WATER REMINDERS ====================
output("2️⃣  Test des rappels d'eau...", 'info');

try {
    $water_patients = $CI->dietetic_notifications_model->get_patients_for_water_reminder();

    if (!empty($water_patients)) {
        output("   ✓ Trouvé " . count($water_patients) . " patient(s) éligible(s)", 'success');

        foreach ($water_patients as $patient) {
            output("   → Patient : {$patient->firstname} {$patient->lastname}", 'info');
            $result = $CI->dietetic_notifications_model->send_water_reminder($patient);

            $success_count = array_filter($result, function($r) { return $r === true; });
            if (!empty($success_count)) {
                $sent_count = count($success_count);
                $total_sent += $sent_count;
                output("     ✓ Envoyé via : " . implode(', ', array_keys($success_count)), 'success');
            } else {
                $total_failed++;
                output("     ✗ Échec d'envoi", 'error');
            }
        }
    } else {
        output("   ℹ Aucun patient à rappeler pour le moment", 'warning');
    }

    $results['water'] = ['found' => count($water_patients ?? [])];

} catch (Exception $e) {
    output("   ✗ Erreur : " . $e->getMessage(), 'error');
    $results['water'] = ['error' => $e->getMessage()];
}

output("", 'info');

// ==================== TEST 3: MEAL REMINDERS ====================
output("3️⃣  Test des rappels de repas...", 'info');

$meal_types = ['breakfast' => 'Petit-déjeuner', 'lunch' => 'Déjeuner', 'dinner' => 'Dîner'];

foreach ($meal_types as $meal_type => $meal_label) {
    output("   📍 {$meal_label}...", 'info');

    try {
        $meal_patients = $CI->dietetic_notifications_model->get_patients_for_meal_reminder($meal_type);

        if (!empty($meal_patients)) {
            output("      ✓ Trouvé " . count($meal_patients) . " patient(s)", 'success');

            foreach ($meal_patients as $patient) {
                $result = $CI->dietetic_notifications_model->send_meal_reminder($patient, $meal_type);

                $success_count = array_filter($result, function($r) { return $r === true; });
                if (!empty($success_count)) {
                    $total_sent += count($success_count);
                    output("      ✓ {$patient->firstname} {$patient->lastname} : " . implode(', ', array_keys($success_count)), 'success');
                } else {
                    $total_failed++;
                    output("      ✗ Échec : {$patient->firstname} {$patient->lastname}", 'error');
                }
            }
        } else {
            output("      ℹ Aucun patient", 'warning');
        }

        $results['meal_' . $meal_type] = ['found' => count($meal_patients ?? [])];

    } catch (Exception $e) {
        output("      ✗ Erreur : " . $e->getMessage(), 'error');
        $results['meal_' . $meal_type] = ['error' => $e->getMessage()];
    }
}

output("", 'info');

// ==================== TEST 4: CONSULTATION REMINDERS ====================
output("4️⃣  Test des rappels de consultation...", 'info');

try {
    // J-1
    output("   📍 Rappels J-1 (24h avant)...", 'info');
    $consultations_day = $CI->dietetic_notifications_model->get_consultations_for_day_reminder();

    if (!empty($consultations_day)) {
        output("      ✓ Trouvé " . count($consultations_day) . " consultation(s)", 'success');

        foreach ($consultations_day as $consultation) {
            $dietitian_name = $consultation->dietitian_firstname . ' ' . $consultation->dietitian_lastname;
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
                    output("      ✓ Patient {$consultation->patient_id} : " . implode(', ', array_keys($success_count)), 'success');
                }
            }
        }
    } else {
        output("      ℹ Aucune consultation demain", 'warning');
    }

    // H-1
    output("   📍 Rappels H-1 (1h avant)...", 'info');
    $consultations_hour = $CI->dietetic_notifications_model->get_consultations_for_hour_reminder();

    if (!empty($consultations_hour)) {
        output("      ✓ Trouvé " . count($consultations_hour) . " consultation(s)", 'success');

        foreach ($consultations_hour as $consultation) {
            $dietitian_name = $consultation->dietitian_firstname . ' ' . $consultation->dietitian_lastname;
            $result = $CI->dietetic_notifications_model->notify_consultation_reminder_hour(
                $consultation->patient_id,
                $consultation->consultation_time,
                $dietitian_name
            );

            if ($result) {
                $success_count = array_filter($result, function($r) { return $r === true; });
                if (!empty($success_count)) {
                    $total_sent += count($success_count);
                    output("      ✓ Patient {$consultation->patient_id} : " . implode(', ', array_keys($success_count)), 'success');
                }
            }
        }
    } else {
        output("      ℹ Aucune consultation dans 1h", 'warning');
    }

    $results['consultations'] = [
        'day' => count($consultations_day ?? []),
        'hour' => count($consultations_hour ?? [])
    ];

} catch (Exception $e) {
    output("   ✗ Erreur : " . $e->getMessage(), 'error');
    $results['consultations'] = ['error' => $e->getMessage()];
}

output("", 'info');

// ==================== SUMMARY ====================
output("========================================", 'info');
output("RÉSUMÉ DU TEST", 'info');
output("========================================", 'info');
output("📤 Total envoyé : {$total_sent}", $total_sent > 0 ? 'success' : 'warning');
output("❌ Total échoué : {$total_failed}", $total_failed > 0 ? 'error' : 'success');
output("🕒 Terminé à : " . date('Y-m-d H:i:s'), 'info');
output("========================================", 'info');

if (!$is_cli) {
    echo "</body></html>";
}

// Return results as JSON if requested
if (isset($_GET['json'])) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'timestamp' => date('Y-m-d H:i:s'),
        'total_sent' => $total_sent,
        'total_failed' => $total_failed,
        'details' => $results
    ], JSON_PRETTY_PRINT);
}
