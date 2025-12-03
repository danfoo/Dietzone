<?php
/**
 * Page de test pour exécuter le cron manuellement
 * URL: https://app.dietsenegal.net/modules/dietetic/test_cron_manual.php
 *
 * Cette page permet de tester le cron sans attendre l'exécution automatique
 */

// Charger Perfex
define('APP_MODULES_PATH', dirname(dirname(__DIR__)) . '/');
define('FCPATH', dirname(dirname(dirname(__FILE__))) . '/');

require_once(FCPATH . 'application/config/app-config.php');
require_once(FCPATH . 'application/libraries/App_merge_fields.php');

// Bootstrap Perfex
$_SERVER['CI_ENV'] = defined('ENVIRONMENT') ? ENVIRONMENT : 'production';
require_once(FCPATH . 'application/config/hooks.php');

// Charger CodeIgniter
require_once(FCPATH . 'index.php');

// Obtenir l'instance de CodeIgniter
$CI = &get_instance();

// Vérifier l'authentification (admin seulement)
if (!function_exists('is_admin') || !is_admin()) {
    die('⛔ Accès refusé. Vous devez être connecté en tant qu\'administrateur.');
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Test Cron Manuel - Dietetic</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1400px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 3px solid #4CAF50; padding-bottom: 10px; }
        h2 { color: #555; margin-top: 30px; border-bottom: 2px solid #2196F3; padding-bottom: 8px; }
        .info { background: #e3f2fd; padding: 15px; border-left: 4px solid #2196F3; margin: 20px 0; border-radius: 4px; }
        .success { background: #e8f5e9; padding: 15px; border-left: 4px solid #4CAF50; margin: 20px 0; border-radius: 4px; }
        .error { background: #ffebee; padding: 15px; border-left: 4px solid #f44336; margin: 20px 0; border-radius: 4px; }
        .warning { background: #fff3e0; padding: 15px; border-left: 4px solid #ff9800; margin: 20px 0; border-radius: 4px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th { background: #4CAF50; color: white; padding: 12px; text-align: left; font-weight: bold; }
        td { padding: 10px; border-bottom: 1px solid #ddd; }
        tr:hover { background: #f5f5f5; }
        .badge { display: inline-block; padding: 4px 8px; border-radius: 3px; font-size: 0.9em; font-weight: bold; }
        .badge-success { background: #4CAF50; color: white; }
        .badge-info { background: #2196F3; color: white; }
        .badge-warning { background: #ff9800; color: white; }
        .badge-error { background: #f44336; color: white; }
        pre { background: #f5f5f5; padding: 15px; border-radius: 4px; overflow-x: auto; }
        .btn { display: inline-block; padding: 10px 20px; background: #4CAF50; color: white; text-decoration: none; border-radius: 4px; font-weight: bold; }
        .btn:hover { background: #45a049; }
        code { background: #f5f5f5; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Test d'Exécution du Cron Dietetic</h1>
        <p><strong>Date/Heure actuelle :</strong> <?= date('Y-m-d H:i:s') ?> (Timezone: <?= date_default_timezone_get() ?>)</p>

        <?php
        // Exécuter le cron
        $start_time = microtime(true);

        echo '<div class="info">';
        echo '<h3>📋 Démarrage de l\'exécution du cron...</h3>';
        echo '</div>';

        // Charger le modèle
        $CI->load->model('dietetic/dietetic_notifications_model');

        // Compteurs
        $details = [];
        $total_sent = 0;
        $total_failed = 0;
        $errors = [];

        try {
            // ==================== WEIGHT REMINDERS ====================
            echo '<h2>⚖️ Rappels de Pesée</h2>';
            $weight_patients = $CI->dietetic_notifications_model->get_patients_for_weight_reminder();
            echo '<p>Patients trouvés : <strong>' . count($weight_patients) . '</strong></p>';

            if (!empty($weight_patients)) {
                echo '<table><tr><th>Patient</th><th>Email</th><th>Téléphone</th><th>Résultat</th></tr>';
                foreach ($weight_patients as $patient) {
                    $result = $CI->dietetic_notifications_model->send_weight_reminder($patient);
                    $success_count = is_array($result) ? count(array_filter($result, function($r) { return $r === true; })) : 0;
                    $total_sent += $success_count;
                    if (empty($success_count)) {
                        $total_failed++;
                    }

                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($patient->firstname ?? 'N/A') . '</td>';
                    echo '<td>' . htmlspecialchars($patient->email ?? 'N/A') . '</td>';
                    echo '<td>' . htmlspecialchars($patient->phonenumber ?? 'N/A') . '</td>';
                    echo '<td>' . ($success_count > 0 ? '<span class="badge badge-success">✓ Envoyé (' . $success_count . ')</span>' : '<span class="badge badge-error">✗ Échec</span>') . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            } else {
                echo '<p class="warning">Aucun patient à notifier pour les rappels de pesée.</p>';
            }

            // ==================== WATER REMINDERS ====================
            echo '<h2>💧 Rappels d\'Hydratation</h2>';
            $water_patients = $CI->dietetic_notifications_model->get_patients_for_water_reminder();
            echo '<p>Patients trouvés : <strong>' . count($water_patients) . '</strong></p>';

            if (!empty($water_patients)) {
                echo '<table><tr><th>Patient</th><th>Email</th><th>Téléphone</th><th>Résultat</th></tr>';
                foreach ($water_patients as $patient) {
                    $result = $CI->dietetic_notifications_model->send_water_reminder($patient);
                    $success_count = is_array($result) ? count(array_filter($result, function($r) { return $r === true; })) : 0;
                    $total_sent += $success_count;
                    if (empty($success_count)) {
                        $total_failed++;
                    }

                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($patient->firstname ?? 'N/A') . '</td>';
                    echo '<td>' . htmlspecialchars($patient->email ?? 'N/A') . '</td>';
                    echo '<td>' . htmlspecialchars($patient->phonenumber ?? 'N/A') . '</td>';
                    echo '<td>' . ($success_count > 0 ? '<span class="badge badge-success">✓ Envoyé (' . $success_count . ')</span>' : '<span class="badge badge-error">✗ Échec</span>') . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            } else {
                echo '<p class="warning">Aucun patient à notifier pour les rappels d\'hydratation.</p>';
            }

            // ==================== MEAL REMINDERS ====================
            $meal_types = ['breakfast' => '🥐 Petit-Déjeuner', 'lunch' => '🍽️ Déjeuner', 'dinner' => '🍴 Dîner'];
            foreach ($meal_types as $meal_type => $meal_label) {
                echo '<h2>' . $meal_label . '</h2>';

                $meal_patients = $CI->dietetic_notifications_model->get_patients_for_meal_reminder($meal_type);
                echo '<p>Patients trouvés : <strong>' . count($meal_patients) . '</strong></p>';

                // Afficher la fenêtre de temps
                $current_time = date('H:i:00');
                $time_5min_ago = date('H:i:00', strtotime('-5 minutes'));
                echo '<p><em>Fenêtre de temps : ' . $time_5min_ago . ' &lt; heure_configurée ≤ ' . $current_time . '</em></p>';

                if (!empty($meal_patients)) {
                    echo '<table><tr><th>Patient</th><th>Heure configurée</th><th>Email</th><th>Téléphone</th><th>Résultat</th></tr>';
                    foreach ($meal_patients as $patient) {
                        $result = $CI->dietetic_notifications_model->send_meal_reminder($patient, $meal_type);
                        $success_count = is_array($result) ? count(array_filter($result, function($r) { return $r === true; })) : 0;
                        $total_sent += $success_count;
                        if (empty($success_count)) {
                            $total_failed++;
                        }

                        $configured_time = $patient->{'reminder_' . $meal_type . '_time'} ?? 'N/A';

                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($patient->firstname ?? 'N/A') . '</td>';
                        echo '<td><strong>' . htmlspecialchars($configured_time) . '</strong></td>';
                        echo '<td>' . htmlspecialchars($patient->email ?? 'N/A') . '</td>';
                        echo '<td>' . htmlspecialchars($patient->phonenumber ?? 'N/A') . '</td>';
                        echo '<td>' . ($success_count > 0 ? '<span class="badge badge-success">✓ Envoyé (' . $success_count . ')</span>' : '<span class="badge badge-error">✗ Échec</span>') . '</td>';
                        echo '</tr>';
                    }
                    echo '</table>';
                } else {
                    echo '<p class="warning">Aucun patient dans la fenêtre de temps pour ' . strtolower($meal_label) . '.</p>';
                }
            }

            // ==================== CONSULTATION REMINDERS (DAY BEFORE) ====================
            echo '<h2>📅 Rappels de Consultation (J-1)</h2>';
            $consultations_day = $CI->dietetic_notifications_model->get_consultations_for_day_reminder();
            echo '<p>Consultations trouvées : <strong>' . count($consultations_day) . '</strong></p>';

            if (!empty($consultations_day)) {
                echo '<table><tr><th>Patient</th><th>Date consultation</th><th>Heure</th><th>Diététicien</th><th>Résultat</th></tr>';
                foreach ($consultations_day as $consultation) {
                    $dietitian_name = $consultation->dietitian_firstname . ' ' . $consultation->dietitian_lastname;
                    $result = $CI->dietetic_notifications_model->notify_consultation_reminder_day(
                        $consultation->patient_id,
                        $consultation->consultation_date,
                        $consultation->consultation_time,
                        $dietitian_name
                    );

                    $success_count = is_array($result) ? count(array_filter($result, function($r) { return $r === true; })) : 0;
                    $total_sent += $success_count;
                    if (empty($success_count)) {
                        $total_failed++;
                    }

                    echo '<tr>';
                    echo '<td>Patient #' . $consultation->patient_id . '</td>';
                    echo '<td>' . date('Y-m-d', strtotime($consultation->consultation_date)) . '</td>';
                    echo '<td>' . htmlspecialchars($consultation->consultation_time ?? 'N/A') . '</td>';
                    echo '<td>' . htmlspecialchars($dietitian_name) . '</td>';
                    echo '<td>' . ($success_count > 0 ? '<span class="badge badge-success">✓ Envoyé (' . $success_count . ')</span>' : '<span class="badge badge-error">✗ Échec</span>') . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            } else {
                echo '<p class="warning">Aucune consultation demain nécessitant un rappel.</p>';
            }

            // ==================== CONSULTATION REMINDERS (1 HOUR BEFORE) ====================
            echo '<h2>⏰ Rappels de Consultation (H-1)</h2>';
            $consultations_hour = $CI->dietetic_notifications_model->get_consultations_for_hour_reminder();
            echo '<p>Consultations trouvées : <strong>' . count($consultations_hour) . '</strong></p>';

            if (!empty($consultations_hour)) {
                echo '<table><tr><th>Patient</th><th>Date/Heure consultation</th><th>Diététicien</th><th>Résultat</th></tr>';
                foreach ($consultations_hour as $consultation) {
                    $dietitian_name = $consultation->dietitian_firstname . ' ' . $consultation->dietitian_lastname;
                    $result = $CI->dietetic_notifications_model->notify_consultation_reminder_hour(
                        $consultation->patient_id,
                        $consultation->consultation_time,
                        $dietitian_name
                    );

                    $success_count = is_array($result) ? count(array_filter($result, function($r) { return $r === true; })) : 0;
                    $total_sent += $success_count;
                    if (empty($success_count)) {
                        $total_failed++;
                    }

                    echo '<tr>';
                    echo '<td>Patient #' . $consultation->patient_id . '</td>';
                    echo '<td>' . htmlspecialchars($consultation->consultation_date) . '</td>';
                    echo '<td>' . htmlspecialchars($dietitian_name) . '</td>';
                    echo '<td>' . ($success_count > 0 ? '<span class="badge badge-success">✓ Envoyé (' . $success_count . ')</span>' : '<span class="badge badge-error">✗ Échec</span>') . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            } else {
                echo '<p class="warning">Aucune consultation dans l\'heure à venir.</p>';
            }

            // ==================== FOOD ENTRY REMINDERS ====================
            echo '<h2>📝 Rappels de Saisie Alimentaire (18h uniquement)</h2>';
            $current_hour = (int)date('H');
            echo '<p>Heure actuelle : <strong>' . $current_hour . ':00</strong></p>';

            if ($current_hour == 18) {
                $patients_to_remind = $CI->dietetic_notifications_model->get_patients_for_food_entry_reminder();
                echo '<p>Patients trouvés : <strong>' . count($patients_to_remind) . '</strong></p>';

                if (!empty($patients_to_remind)) {
                    echo '<table><tr><th>Patient</th><th>Résultat</th></tr>';
                    foreach ($patients_to_remind as $patient) {
                        $result = $CI->dietetic_notifications_model->send_food_entry_reminder($patient->patient_id);
                        $success_count = is_array($result) ? count(array_filter($result, function($r) { return $r === true; })) : 0;
                        $total_sent += $success_count;
                        if (empty($success_count)) {
                            $total_failed++;
                        }

                        echo '<tr>';
                        echo '<td>Patient #' . $patient->patient_id . '</td>';
                        echo '<td>' . ($success_count > 0 ? '<span class="badge badge-success">✓ Envoyé (' . $success_count . ')</span>' : '<span class="badge badge-error">✗ Échec</span>') . '</td>';
                        echo '</tr>';
                    }
                    echo '</table>';
                }
            } else {
                echo '<p class="info">Les rappels de saisie alimentaire ne sont envoyés qu\'à 18h00. Il est actuellement ' . $current_hour . ':' . date('i') . '.</p>';
            }

        } catch (Exception $e) {
            echo '<div class="error">';
            echo '<h3>❌ Erreur pendant l\'exécution</h3>';
            echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
            echo '</div>';
            $errors[] = $e->getMessage();
        }

        $execution_time = round((microtime(true) - $start_time) * 1000, 2);

        // Résumé final
        echo '<h2>📊 Résumé de l\'Exécution</h2>';
        echo '<div class="' . ($total_sent > 0 ? 'success' : 'warning') . '">';
        echo '<table style="background: transparent; margin: 0;">';
        echo '<tr><td><strong>Notifications envoyées :</strong></td><td><span class="badge badge-success">' . $total_sent . '</span></td></tr>';
        echo '<tr><td><strong>Échecs :</strong></td><td><span class="badge badge-' . ($total_failed > 0 ? 'error' : 'info') . '">' . $total_failed . '</span></td></tr>';
        echo '<tr><td><strong>Temps d\'exécution :</strong></td><td>' . $execution_time . ' ms</td></tr>';
        echo '<tr><td><strong>Erreurs :</strong></td><td>' . (empty($errors) ? '<span class="badge badge-success">Aucune</span>' : '<span class="badge badge-error">' . count($errors) . '</span>') . '</td></tr>';
        echo '</table>';
        echo '</div>';

        // Vérifier les logs dans activity_log
        echo '<h2>📝 Logs Générés</h2>';
        $logs = $CI->db->order_by('id', 'DESC')
                       ->limit(5)
                       ->like('description', 'Dietetic Cron', 'both')
                       ->get(db_prefix() . 'activity_log')
                       ->result_array();

        if (!empty($logs)) {
            echo '<table><tr><th>Date</th><th>Description</th></tr>';
            foreach ($logs as $log) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($log['date']) . '</td>';
                echo '<td>' . htmlspecialchars($log['description']) . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo '<p class="warning">Aucun log trouvé. Cela peut indiquer que le fichier dietetic.php n\'a pas été mis à jour sur le serveur.</p>';
        }
        ?>

        <div style="margin-top: 30px; padding: 15px; background: #f5f5f5; border-radius: 4px;">
            <p><strong>🔄 Actions :</strong></p>
            <ul>
                <li><a href="?refresh=1" class="btn">Relancer le test</a></li>
                <li><a href="/dietetic/portal/check_cron_execution">Voir l'historique complet des notifications</a></li>
            </ul>
        </div>
    </div>
</body>
</html>
