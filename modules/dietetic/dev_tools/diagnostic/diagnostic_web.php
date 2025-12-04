<?php
/**
 * Page Web de Diagnostic des Notifications Dietetic
 * Accessible via : https://app.dietsenegal.net/modules/dietetic/diagnostic_web.php
 */

// Configuration
define('BASEPATH', dirname(__DIR__, 2) . '/');

if (!file_exists(BASEPATH . 'index.php')) {
    die("❌ Erreur: Perfex CRM non trouvé");
}

if (file_exists(BASEPATH . 'application/config/app-config.php')) {
    require_once(BASEPATH . 'application/config/app-config.php');
}

// Database configuration
$db_host = defined('APP_DB_HOSTNAME') ? APP_DB_HOSTNAME : 'localhost';
$db_user = defined('APP_DB_USERNAME') ? APP_DB_USERNAME : '';
$db_pass = defined('APP_DB_PASSWORD') ? APP_DB_PASSWORD : '';
$db_name = defined('APP_DB_NAME') ? APP_DB_NAME : '';
$db_prefix = defined('APP_DB_PREFIX') ? APP_DB_PREFIX : 'tbl';

// Connect to database
$mysqli = @new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($mysqli->connect_error) {
    die("❌ Erreur de connexion à la base de données: " . $mysqli->connect_error);
}

// Run diagnostics
$diagnostic = [];
$errors = [];
$warnings = [];
$success = [];

// 1. Check module activation
$query = "SELECT * FROM {$db_prefix}modules WHERE module_name = 'dietetic'";
$result = $mysqli->query($query);
$module = $result ? $result->fetch_assoc() : null;
$diagnostic['module_active'] = ($module && $module['active'] == 1);

if ($diagnostic['module_active']) {
    $success[] = "Module Dietetic activé";
} else {
    $errors[] = "Module Dietetic NON activé - Allez dans Admin > Modules pour l'activer";
}

// 2. Check cron
$query = "SELECT * FROM {$db_prefix}options WHERE name = 'last_cron_run'";
$result = $mysqli->query($query);
$cron = $result ? $result->fetch_assoc() : null;
$diagnostic['last_cron_run'] = $cron ? $cron['value'] : null;
$diagnostic['cron_minutes_ago'] = $diagnostic['last_cron_run'] ? floor((time() - $diagnostic['last_cron_run']) / 60) : null;

if ($diagnostic['cron_minutes_ago'] === null) {
    $errors[] = "Cron jamais exécuté";
} elseif ($diagnostic['cron_minutes_ago'] > 10) {
    $warnings[] = "Cron inactif depuis " . $diagnostic['cron_minutes_ago'] . " minutes";
} else {
    $success[] = "Cron actif (il y a " . $diagnostic['cron_minutes_ago'] . " min)";
}

// 3. Check patients with preferences
$query = "SELECT COUNT(*) as total FROM {$db_prefix}dietic_patients";
$result = $mysqli->query($query);
$diagnostic['total_patients'] = $result ? $result->fetch_assoc()['total'] : 0;

$query = "SELECT COUNT(*) as total FROM {$db_prefix}dietic_notification_preferences";
$result = $mysqli->query($query);
$diagnostic['patients_with_prefs'] = $result ? $result->fetch_assoc()['total'] : 0;

if ($diagnostic['patients_with_prefs'] == 0) {
    $warnings[] = "Aucun patient avec préférences configurées";
} else {
    $success[] = $diagnostic['patients_with_prefs'] . " patient(s) avec préférences";
}

// 4. Get patients with dinner reminder
$query = "SELECT p.*, c.firstname, c.lastname, c.email, c.phonenumber
          FROM {$db_prefix}dietic_notification_preferences p
          INNER JOIN {$db_prefix}dietic_patients dp ON p.patient_id = dp.id
          INNER JOIN {$db_prefix}contacts c ON dp.contact_id = c.id
          WHERE p.reminder_dinner = 1
          LIMIT 10";
$result = $mysqli->query($query);
$diagnostic['dinner_patients'] = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $diagnostic['dinner_patients'][] = $row;
    }
}

if (count($diagnostic['dinner_patients']) == 0) {
    $warnings[] = "Aucun patient avec rappel dîner activé";
} else {
    $success[] = count($diagnostic['dinner_patients']) . " patient(s) avec rappel dîner";
}

// 5. Check channel configuration
$query = "SELECT * FROM {$db_prefix}dietic_notification_settings WHERE name IN ('sms_lam_account_id', 'sms_lam_password', 'whatsapp_api_key')";
$result = $mysqli->query($query);
$settings = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $settings[$row['name']] = !empty($row['value']);
    }
}

$query = "SELECT * FROM {$db_prefix}options WHERE name IN ('smtp_host', 'smtp_username')";
$result = $mysqli->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $settings[$row['name']] = !empty($row['value']);
    }
}

$diagnostic['smtp_configured'] = isset($settings['smtp_host']) && isset($settings['smtp_username']);
$diagnostic['sms_configured'] = isset($settings['sms_lam_account_id']) && isset($settings['sms_lam_password']);
$diagnostic['whatsapp_configured'] = isset($settings['whatsapp_api_key']);

// 6. Get recent notifications
$query = "SELECT * FROM {$db_prefix}dietic_patient_notifications ORDER BY created_at DESC LIMIT 10";
$result = $mysqli->query($query);
$diagnostic['recent_notifications'] = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $diagnostic['recent_notifications'][] = $row;
    }
}

$mysqli->close();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnostic Notifications - Dietetic</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .header {
            background: white;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            text-align: center;
        }
        .header h1 {
            color: #01807B;
            font-size: 28px;
            margin-bottom: 10px;
        }
        .header .subtitle {
            color: #666;
            font-size: 14px;
        }
        .summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        .summary-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .summary-card.success {
            background: linear-gradient(135deg, #4caf50, #45a049);
            color: white;
        }
        .summary-card.error {
            background: linear-gradient(135deg, #f44336, #e53935);
            color: white;
        }
        .summary-card.warning {
            background: linear-gradient(135deg, #ff9800, #fb8c00);
            color: white;
        }
        .summary-number {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        .summary-label {
            font-size: 14px;
            opacity: 0.9;
        }
        .card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .card-title {
            font-size: 20px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 3px solid #01807B;
        }
        .status-item {
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 8px;
            border-left: 4px solid;
        }
        .status-item.success {
            background: #e8f5e9;
            border-color: #4caf50;
        }
        .status-item.error {
            background: #ffebee;
            border-color: #f44336;
        }
        .status-item.warning {
            background: #fff3e0;
            border-color: #ff9800;
        }
        .status-label {
            font-weight: 600;
            font-size: 16px;
            margin-bottom: 5px;
        }
        .status-detail {
            font-size: 14px;
            color: #666;
        }
        .patient-card {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 10px;
            border-left: 4px solid #01807B;
        }
        .patient-name {
            font-weight: 700;
            color: #01807B;
            margin-bottom: 8px;
        }
        .patient-info {
            font-size: 13px;
            color: #666;
            margin-bottom: 4px;
        }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            margin-right: 5px;
        }
        .badge.success {
            background: #4caf50;
            color: white;
        }
        .badge.error {
            background: #f44336;
            color: white;
        }
        .recommendation-box {
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            border: 2px solid #2196f3;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }
        .recommendation-box.success {
            background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
            border-color: #4caf50;
        }
        .recommendation-title {
            font-weight: 700;
            color: #1565c0;
            margin-bottom: 15px;
            font-size: 18px;
        }
        .recommendation-title.success {
            color: #2e7d32;
        }
        ul {
            margin-left: 20px;
        }
        li {
            margin-bottom: 8px;
            color: #1976d2;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background: #01807B;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 10px 5px;
        }
        .button:hover {
            background: #01605B;
        }
        .actions {
            text-align: center;
            margin-top: 30px;
        }
        @media (max-width: 768px) {
            .summary {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔍 Diagnostic des Notifications</h1>
            <div class="subtitle">
                app.dietsenegal.net • <?php echo date('d/m/Y H:i:s'); ?>
            </div>
        </div>

        <!-- Summary -->
        <div class="summary">
            <div class="summary-card <?php echo count($success) > 0 ? 'success' : 'error'; ?>">
                <div class="summary-number"><?php echo count($success); ?></div>
                <div class="summary-label">✅ Points Positifs</div>
            </div>
            <div class="summary-card <?php echo count($warnings) > 0 ? 'warning' : 'success'; ?>">
                <div class="summary-number"><?php echo count($warnings); ?></div>
                <div class="summary-label">⚠️ Avertissements</div>
            </div>
            <div class="summary-card <?php echo count($errors) > 0 ? 'error' : 'success'; ?>">
                <div class="summary-number"><?php echo count($errors); ?></div>
                <div class="summary-label">❌ Erreurs</div>
            </div>
        </div>

        <!-- Module Status -->
        <div class="card">
            <div class="card-title">📦 État du Module</div>
            <div class="status-item <?php echo $diagnostic['module_active'] ? 'success' : 'error'; ?>">
                <div class="status-label"><?php echo $diagnostic['module_active'] ? '✅' : '❌'; ?> Module Dietetic</div>
                <div class="status-detail">
                    <?php if ($diagnostic['module_active']): ?>
                        Le module est activé et opérationnel
                    <?php else: ?>
                        <strong>❌ Le module n'est PAS activé !</strong><br>
                        Action : Allez dans <a href="https://app.dietsenegal.net/admin/modules" target="_blank" style="color: #01807B; font-weight: 600;">Admin > Modules</a> et cliquez sur "Activate"
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Cron Status -->
        <div class="card">
            <div class="card-title">⏰ État du Cron</div>
            <?php if ($diagnostic['last_cron_run']): ?>
                <div class="status-item <?php echo $diagnostic['cron_minutes_ago'] < 10 ? 'success' : 'warning'; ?>">
                    <div class="status-label"><?php echo $diagnostic['cron_minutes_ago'] < 10 ? '✅' : '⚠️'; ?> Cron Perfex</div>
                    <div class="status-detail">
                        Dernière exécution : il y a <strong><?php echo $diagnostic['cron_minutes_ago']; ?></strong> minute(s)<br>
                        <small><?php echo date('d/m/Y H:i:s', $diagnostic['last_cron_run']); ?></small>
                        <?php if ($diagnostic['cron_minutes_ago'] > 10): ?>
                            <br><strong style="color: #f57c00;">⚠️ Le cron devrait s'exécuter toutes les 5 minutes !</strong>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="status-item error">
                    <div class="status-label">❌ Cron Non Configuré</div>
                    <div class="status-detail">Le cron n'a jamais été exécuté</div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Patients -->
        <div class="card">
            <div class="card-title">👥 Patients avec Rappel Dîner</div>
            <?php if (count($diagnostic['dinner_patients']) > 0): ?>
                <?php foreach ($diagnostic['dinner_patients'] as $patient): ?>
                    <div class="patient-card">
                        <div class="patient-name">
                            <?php echo htmlspecialchars($patient['firstname'] . ' ' . $patient['lastname']); ?>
                        </div>
                        <div class="patient-info">
                            ⏰ Heure : <strong><?php echo substr($patient['reminder_dinner_time'], 0, 5); ?></strong>
                        </div>
                        <div class="patient-info">
                            📧 Email : <?php echo $patient['email'] ?: '<span style="color: #f44336;">❌ Non renseigné</span>'; ?>
                        </div>
                        <div class="patient-info">
                            📱 Téléphone : <?php echo $patient['phonenumber'] ?: '<span style="color: #f44336;">❌ Non renseigné</span>'; ?>
                        </div>
                        <div style="margin-top: 8px;">
                            <?php if ($patient['channel_email']): ?><span class="badge success">Email</span><?php endif; ?>
                            <?php if ($patient['channel_sms']): ?><span class="badge success">SMS</span><?php endif; ?>
                            <?php if ($patient['channel_whatsapp']): ?><span class="badge success">WhatsApp</span><?php endif; ?>
                            <?php if (!$patient['channel_email'] && !$patient['channel_sms'] && !$patient['channel_whatsapp']): ?>
                                <span class="badge error">❌ Aucun canal activé</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="status-item warning">
                    <div class="status-label">⚠️ Aucun Patient</div>
                    <div class="status-detail">Aucun patient n'a activé le rappel de dîner</div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Recommendations -->
        <?php if (count($errors) > 0 || count($warnings) > 0): ?>
        <div class="recommendation-box">
            <div class="recommendation-title">💡 Actions Recommandées</div>
            <?php if (count($errors) > 0): ?>
                <strong style="color: #d32f2f;">🚨 ERREURS CRITIQUES :</strong>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li style="color: #d32f2f;"><strong><?php echo $error; ?></strong></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <?php if (count($warnings) > 0): ?>
                <strong style="color: #f57c00;">⚠️ AVERTISSEMENTS :</strong>
                <ul>
                    <?php foreach ($warnings as $warning): ?>
                        <li style="color: #f57c00;"><?php echo $warning; ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <div class="recommendation-box success">
            <div class="recommendation-title success">✅ Tout est en ordre !</div>
            <p style="color: #1b5e20;">
                Votre système est correctement configuré. Les notifications seront envoyées aux heures prévues (± 5 minutes).
            </p>
        </div>
        <?php endif; ?>

        <div class="actions">
            <a href="https://app.dietsenegal.net/admin" class="button">🏠 Dashboard Admin</a>
            <a href="https://app.dietsenegal.net/admin/modules" class="button">📦 Modules</a>
            <a href="?refresh=1" class="button">🔄 Rafraîchir</a>
        </div>

        <div style="text-align: center; margin-top: 30px; color: white; font-size: 12px;">
            Diagnostic Dietetic • <?php echo date('Y'); ?>
        </div>
    </div>
</body>
</html>
