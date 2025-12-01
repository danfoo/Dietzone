<?php
/**
 * Diagnostic Push Notifications
 * URL: /modules/dietetic/diagnose_push.php
 *
 * Ce fichier diagnostique l'état complet du système de notifications push
 */

define('BASEPATH', true);
require_once(__DIR__ . '/../../application/config/database.php');

// Database connection
$db_config = $db['default'];
$mysqli = new mysqli(
    $db_config['hostname'],
    $db_config['username'],
    $db_config['password'],
    $db_config['database']
);

if ($mysqli->connect_error) {
    die('Database Connection Error: ' . $mysqli->connect_error);
}

// Get table prefix
$prefix = $db_config['dbprefix'];

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>🔔 Diagnostic Push Notifications</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .section {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 { color: #01807B; }
        h2 {
            color: #2d3748;
            border-bottom: 2px solid #01807B;
            padding-bottom: 10px;
        }
        .status {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            margin-left: 10px;
        }
        .status.ok { background: #c6f6d5; color: #22543d; }
        .status.error { background: #fed7d7; color: #c53030; }
        .status.warning { background: #feebc8; color: #7c2d12; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        th {
            background: #f7fafc;
            font-weight: 600;
        }
        code {
            background: #f7fafc;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 13px;
        }
        .file-check {
            margin: 10px 0;
            padding: 10px;
            background: #f7fafc;
            border-left: 4px solid #01807B;
        }
        pre {
            background: #2d3748;
            color: #e2e8f0;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <h1>🔔 Diagnostic Complet - Notifications Push</h1>
    <p>Généré le : <strong><?php echo date('d/m/Y H:i:s'); ?></strong></p>

    <!-- 1. Service Worker Files -->
    <div class="section">
        <h2>1. Fichiers Service Worker</h2>

        <?php
        $sw_path = __DIR__ . '/../../firebase-messaging-sw.js';
        $sw_exists = file_exists($sw_path);
        ?>

        <div class="file-check">
            <strong>📄 firebase-messaging-sw.js</strong>
            <span class="status <?php echo $sw_exists ? 'ok' : 'error'; ?>">
                <?php echo $sw_exists ? '✓ EXISTE' : '✗ MANQUANT'; ?>
            </span>
            <br>
            <code><?php echo $sw_path; ?></code>

            <?php if ($sw_exists): ?>
                <br><br>
                <strong>Taille:</strong> <?php echo number_format(filesize($sw_path)); ?> octets<br>
                <strong>Dernière modification:</strong> <?php echo date('d/m/Y H:i:s', filemtime($sw_path)); ?>

                <?php
                $sw_content = file_get_contents($sw_path);
                if (preg_match('/Version: (.+)/', $sw_content, $matches)) {
                    echo '<br><strong>Version détectée:</strong> ' . htmlspecialchars($matches[1]);
                }

                // Check for v3 marker
                if (strpos($sw_content, 'v3') !== false) {
                    echo '<br><span class="status ok">✓ Version v3 (correcte)</span>';
                } else {
                    echo '<br><span class="status warning">⚠ Version < v3 (ancienne)</span>';
                }

                // Check for importScripts
                if (strpos($sw_content, 'importScripts') !== false) {
                    echo '<br><span class="status ok">✓ importScripts présent</span>';
                } else {
                    echo '<br><span class="status error">✗ importScripts manquant</span>';
                }
                ?>

                <br><br>
                <strong>Premiers 10 lignes:</strong>
                <pre><?php
                $lines = explode("\n", $sw_content);
                echo htmlspecialchars(implode("\n", array_slice($lines, 0, 10)));
                ?></pre>
            <?php endif; ?>
        </div>
    </div>

    <!-- 2. Firebase Configuration -->
    <div class="section">
        <h2>2. Configuration Firebase</h2>

        <?php
        $query = "SELECT setting_key, setting_value
                  FROM {$prefix}dietic_notification_settings
                  WHERE setting_key LIKE 'firebase_%' OR setting_key = 'push_enabled'
                  ORDER BY setting_key";
        $result = $mysqli->query($query);

        $firebase_config = [];
        while ($row = $result->fetch_assoc()) {
            $firebase_config[$row['setting_key']] = $row['setting_value'];
        }
        ?>

        <table>
            <tr>
                <th>Paramètre</th>
                <th>Valeur</th>
                <th>Status</th>
            </tr>

            <?php
            $required_keys = [
                'push_enabled' => 'Notifications Push Activées',
                'firebase_api_key' => 'API Key',
                'firebase_project_id' => 'Project ID',
                'firebase_messaging_sender_id' => 'Sender ID',
                'firebase_app_id' => 'App ID',
                'firebase_vapid_key' => 'VAPID Key',
                'firebase_use_v1_api' => 'API v1 (Moderne)',
                'firebase_service_account_json' => 'Service Account JSON'
            ];

            foreach ($required_keys as $key => $label) {
                $value = isset($firebase_config[$key]) ? $firebase_config[$key] : '';
                $has_value = !empty($value);

                // Hide sensitive values
                $display_value = '';
                if ($has_value) {
                    if (in_array($key, ['firebase_api_key', 'firebase_vapid_key'])) {
                        $display_value = substr($value, 0, 20) . '... (' . strlen($value) . ' chars)';
                    } elseif ($key === 'firebase_service_account_json') {
                        $display_value = 'JSON configuré (' . strlen($value) . ' chars)';
                    } else {
                        $display_value = htmlspecialchars($value);
                    }
                }

                $status_class = $has_value ? 'ok' : 'error';
                $status_text = $has_value ? '✓ Configuré' : '✗ Manquant';

                echo "<tr>";
                echo "<td><strong>{$label}</strong></td>";
                echo "<td><code>{$display_value}</code></td>";
                echo "<td><span class='status {$status_class}'>{$status_text}</span></td>";
                echo "</tr>";
            }
            ?>
        </table>
    </div>

    <!-- 3. FCM Tokens -->
    <div class="section">
        <h2>3. Tokens FCM Enregistrés</h2>

        <?php
        $query = "SELECT COUNT(*) as total FROM {$prefix}dietic_fcm_tokens";
        $result = $mysqli->query($query);
        $total_tokens = $result->fetch_assoc()['total'];

        $query = "SELECT COUNT(*) as active FROM {$prefix}dietic_fcm_tokens WHERE is_active = 1";
        $result = $mysqli->query($query);
        $active_tokens = $result->fetch_assoc()['active'];

        $query = "SELECT COUNT(DISTINCT patient_id) as patients FROM {$prefix}dietic_fcm_tokens WHERE is_active = 1";
        $result = $mysqli->query($query);
        $patients_with_tokens = $result->fetch_assoc()['patients'];
        ?>

        <table>
            <tr>
                <th>Métrique</th>
                <th>Valeur</th>
            </tr>
            <tr>
                <td>Total Tokens</td>
                <td><strong><?php echo $total_tokens; ?></strong></td>
            </tr>
            <tr>
                <td>Tokens Actifs</td>
                <td><strong><?php echo $active_tokens; ?></strong></td>
            </tr>
            <tr>
                <td>Patients avec Push</td>
                <td><strong><?php echo $patients_with_tokens; ?></strong></td>
            </tr>
        </table>

        <?php if ($active_tokens > 0): ?>
            <br>
            <h3>Derniers Tokens Enregistrés</h3>
            <?php
            $query = "SELECT t.*, p.id as patient_id, c.company as patient_name
                      FROM {$prefix}dietic_fcm_tokens t
                      LEFT JOIN {$prefix}dietic_patients p ON p.id = t.patient_id
                      LEFT JOIN {$prefix}clients c ON c.userid = p.client_id
                      WHERE t.is_active = 1
                      ORDER BY t.created_at DESC
                      LIMIT 5";
            $result = $mysqli->query($query);
            ?>
            <table>
                <tr>
                    <th>Patient</th>
                    <th>Token (début)</th>
                    <th>Type</th>
                    <th>Créé le</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['patient_name'] ?: 'N/A'); ?></td>
                    <td><code><?php echo substr($row['token'], 0, 30); ?>...</code></td>
                    <td><?php echo htmlspecialchars($row['device_type']); ?></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></td>
                </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <br>
            <div class="status warning">
                ⚠️ Aucun token FCM enregistré. Les patients doivent activer les notifications depuis le portail.
            </div>
        <?php endif; ?>
    </div>

    <!-- 4. Notification Logs -->
    <div class="section">
        <h2>4. Historique Notifications Push</h2>

        <?php
        $query = "SELECT COUNT(*) as total FROM {$prefix}dietic_notification_logs WHERE channel = 'push'";
        $result = $mysqli->query($query);
        $total_push = $result->fetch_assoc()['total'];

        $query = "SELECT COUNT(*) as sent FROM {$prefix}dietic_notification_logs WHERE channel = 'push' AND status = 'sent'";
        $result = $mysqli->query($query);
        $sent_push = $result->fetch_assoc()['sent'];

        $query = "SELECT COUNT(*) as failed FROM {$prefix}dietic_notification_logs WHERE channel = 'push' AND status = 'failed'";
        $result = $mysqli->query($query);
        $failed_push = $result->fetch_assoc()['failed'];
        ?>

        <table>
            <tr>
                <th>Métrique</th>
                <th>Valeur</th>
            </tr>
            <tr>
                <td>Total Notifications Push</td>
                <td><strong><?php echo $total_push; ?></strong></td>
            </tr>
            <tr>
                <td>Envoyées avec Succès</td>
                <td><strong><?php echo $sent_push; ?></strong></td>
            </tr>
            <tr>
                <td>Échouées</td>
                <td><strong><?php echo $failed_push; ?></strong></td>
            </tr>
        </table>

        <?php if ($total_push > 0): ?>
            <br>
            <h3>Dernières Notifications Push</h3>
            <?php
            $query = "SELECT l.*, c.company as patient_name
                      FROM {$prefix}dietic_notification_logs l
                      LEFT JOIN {$prefix}dietic_patients p ON p.id = l.patient_id
                      LEFT JOIN {$prefix}clients c ON c.userid = p.client_id
                      WHERE l.channel = 'push'
                      ORDER BY l.sent_at DESC
                      LIMIT 10";
            $result = $mysqli->query($query);
            ?>
            <table>
                <tr>
                    <th>Date</th>
                    <th>Patient</th>
                    <th>Type</th>
                    <th>Message</th>
                    <th>Status</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo date('d/m H:i', strtotime($row['sent_at'])); ?></td>
                    <td><?php echo htmlspecialchars($row['patient_name'] ?: 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($row['notification_type']); ?></td>
                    <td><?php echo htmlspecialchars(substr($row['message'], 0, 50)); ?>...</td>
                    <td>
                        <span class="status <?php echo $row['status'] === 'sent' ? 'ok' : 'error'; ?>">
                            <?php echo $row['status'] === 'sent' ? '✓ Envoyé' : '✗ Échec'; ?>
                        </span>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        <?php endif; ?>
    </div>

    <!-- 5. Recommendations -->
    <div class="section">
        <h2>5. Recommandations</h2>

        <?php
        $issues = [];

        if (!$sw_exists) {
            $issues[] = "❌ Le fichier Service Worker n'existe pas à la racine du site";
        } elseif (strpos($sw_content, 'v3') === false) {
            $issues[] = "⚠️ Le Service Worker n'est pas à la version v3 (dernière version)";
        }

        if (empty($firebase_config['push_enabled']) || $firebase_config['push_enabled'] != '1') {
            $issues[] = "❌ Les notifications push ne sont pas activées dans les paramètres";
        }

        if (empty($firebase_config['firebase_vapid_key'])) {
            $issues[] = "❌ La clé VAPID Firebase n'est pas configurée";
        }

        if (empty($firebase_config['firebase_service_account_json'])) {
            $issues[] = "❌ Le Service Account JSON Firebase n'est pas configuré";
        }

        if ($active_tokens == 0) {
            $issues[] = "⚠️ Aucun patient n'a activé les notifications push";
        }

        if (empty($issues)) {
            echo '<div class="status ok">✓ Tout est configuré correctement !</div>';
        } else {
            echo '<ul>';
            foreach ($issues as $issue) {
                echo '<li>' . $issue . '</li>';
            }
            echo '</ul>';
        }
        ?>
    </div>

    <!-- 6. Test URL -->
    <div class="section">
        <h2>6. Test d'Accès au Service Worker</h2>
        <p>Testez si le Service Worker est accessible depuis le navigateur :</p>
        <p>
            <a href="/firebase-messaging-sw.js" target="_blank" style="color: #01807B; font-weight: bold;">
                → Ouvrir /firebase-messaging-sw.js
            </a>
        </p>
        <p>
            <a href="/firebase-messaging-sw.js?v=3" target="_blank" style="color: #01807B; font-weight: bold;">
                → Ouvrir /firebase-messaging-sw.js?v=3 (avec cache-busting)
            </a>
        </p>
    </div>
</body>
</html>

<?php
$mysqli->close();
?>
