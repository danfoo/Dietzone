<?php
/**
 * Script de diagnostic pour templates et SMS
 * URL: https://app.dietsenegal.net/modules/dietetic/diagnostic_templates_sms.php
 */

define('BASEPATH', true);
require_once(__DIR__ . '/../../application/config/app-config.php');
require_once(__DIR__ . '/helpers/dietetic_helper.php');

// Connexion à la base de données
$conn = mysqli_connect(APP_DB_HOSTNAME, APP_DB_USERNAME, APP_DB_PASSWORD, APP_DB_NAME);

if (!$conn) {
    die("<h1 style='color:red;'>❌ Erreur de connexion à la base de données</h1>" . mysqli_connect_error());
}

echo "<h1>🔍 Diagnostic Templates & SMS</h1>";
echo "<hr>";

// ========================================
// 1. VÉRIFIER LES TEMPLATES DANS LA BASE
// ========================================
echo "<h2>1. Vérification des templates dans la base de données</h2>";

$template_keys = [
    'template_patient_registration_subject',
    'template_patient_registration_body',
    'template_patient_registration_sms_body',
    'template_patient_registration_whatsapp_body',
    'template_password_reset_subject',
    'template_password_reset_body',
    'template_password_reset_sms_body',
    'template_password_reset_whatsapp_body'
];

$templates_found = 0;
$templates_missing = 0;

echo "<table border='1' cellpadding='5' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Template</th><th>Status</th><th>Longueur</th></tr>";

foreach ($template_keys as $key) {
    $key_escaped = mysqli_real_escape_string($conn, $key);
    $sql = "SELECT setting_value FROM tbldietic_settings WHERE setting_key = '$key_escaped'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $value_length = strlen($row['setting_value']);
        echo "<tr><td>$key</td><td style='color:green;'>✓ Trouvé</td><td>{$value_length} car</td></tr>";
        $templates_found++;

        // Afficher le contenu des SMS
        if (strpos($key, '_sms_body') !== false) {
            echo "<tr><td colspan='3' style='background:#f5f5f5;'><strong>Contenu SMS:</strong><br><pre>" . htmlspecialchars($row['setting_value']) . "</pre></td></tr>";
        }
    } else {
        echo "<tr><td>$key</td><td style='color:red;'>✗ Manquant</td><td>-</td></tr>";
        $templates_missing++;
    }
}

echo "</table>";
echo "<p><strong>Résumé:</strong> $templates_found trouvés, $templates_missing manquants</p>";

if ($templates_missing > 0) {
    echo "<p style='color:red;'><strong>⚠️ Vous devez exécuter le script update_notification_templates.php !</strong></p>";
    echo "<p><a href='update_notification_templates.php' style='display:inline-block;padding:10px 20px;background:#01807B;color:white;text-decoration:none;border-radius:5px;'>Exécuter maintenant</a></p>";
}

// ========================================
// 2. VÉRIFIER CONFIGURATION SMS
// ========================================
echo "<hr>";
echo "<h2>2. Configuration SMS LAM</h2>";

$sms_account_id = null;
$sms_password = null;

$sql = "SELECT setting_key, setting_value FROM tbldietic_settings WHERE setting_key IN ('sms_lam_account_id', 'sms_lam_password')";
$result = mysqli_query($conn, $sql);

echo "<table border='1' cellpadding='5' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Paramètre</th><th>Status</th><th>Valeur</th></tr>";

$credentials_ok = true;

while ($row = mysqli_fetch_assoc($result)) {
    $masked_value = !empty($row['setting_value']) ? str_repeat('*', min(strlen($row['setting_value']), 20)) : '(vide)';
    $status = !empty($row['setting_value']) ? "<span style='color:green;'>✓ Configuré</span>" : "<span style='color:red;'>✗ Manquant</span>";

    echo "<tr><td>{$row['setting_key']}</td><td>$status</td><td>$masked_value</td></tr>";

    if (empty($row['setting_value'])) {
        $credentials_ok = false;
    }

    if ($row['setting_key'] == 'sms_lam_account_id') {
        $sms_account_id = $row['setting_value'];
    }
    if ($row['setting_key'] == 'sms_lam_password') {
        $sms_password = $row['setting_value'];
    }
}

echo "</table>";

if (!$credentials_ok) {
    echo "<p style='color:red;'><strong>⚠️ Les credentials LAM SMS ne sont pas configurés !</strong></p>";
    echo "<p>Allez dans <strong>Admin > DietZone > Settings</strong> et configurez:</p>";
    echo "<ul>";
    echo "<li>sms_lam_account_id</li>";
    echo "<li>sms_lam_password</li>";
    echo "</ul>";
}

// ========================================
// 3. TEST D'ENVOI SMS (si credentials OK)
// ========================================
echo "<hr>";
echo "<h2>3. Test d'envoi SMS</h2>";

if ($credentials_ok && isset($_GET['test_phone'])) {
    $test_phone = $_GET['test_phone'];
    $test_message = "DietZone - Test SMS: " . date('H:i:s');

    echo "<p>Envoi SMS de test au numéro: <strong>$test_phone</strong></p>";
    echo "<p>Message: <em>$test_message</em></p>";

    // Charger la fonction dietetic_send_sms
    if (function_exists('dietetic_send_sms')) {
        $result = dietetic_send_sms($test_phone, $test_message);

        echo "<h3>Résultat:</h3>";
        echo "<pre>" . print_r($result, true) . "</pre>";

        if ($result['success']) {
            echo "<p style='color:green;'><strong>✓ SMS envoyé avec succès !</strong></p>";
        } else {
            echo "<p style='color:red;'><strong>✗ Échec: " . htmlspecialchars($result['message']) . "</strong></p>";
        }
    } else {
        echo "<p style='color:red;'>✗ Fonction dietetic_send_sms non trouvée</p>";
    }
} else {
    echo "<form method='GET'>";
    echo "<p>Entrez un numéro de téléphone pour tester l'envoi SMS:</p>";
    echo "<input type='tel' name='test_phone' placeholder='+221771234567' required style='padding:10px; font-size:16px; width:300px;'>";
    echo "<button type='submit' style='padding:10px 20px; background:#01807B; color:white; border:none; cursor:pointer; font-size:16px;'>Envoyer SMS test</button>";
    echo "</form>";

    if (!$credentials_ok) {
        echo "<p style='color:orange;'><em>⚠️ Configurez d'abord les credentials LAM SMS pour tester</em></p>";
    }
}

// ========================================
// 4. VÉRIFIER CODES OTP RÉCENTS
// ========================================
echo "<hr>";
echo "<h2>4. Codes OTP récents (dernières 24h)</h2>";

$sql = "SELECT * FROM tbldietic_otp_codes WHERE created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR) ORDER BY created_at DESC LIMIT 10";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Téléphone</th><th>Code</th><th>Type</th><th>Utilisé</th><th>Créé</th><th>Expire</th></tr>";

    while ($row = mysqli_fetch_assoc($result)) {
        $used_badge = $row['used'] == 1 ? "<span style='color:green;'>✓ Oui</span>" : "<span style='color:orange;'>Non</span>";
        $expired = strtotime($row['expires_at']) < time() ? " <em>(expiré)</em>" : "";

        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['phone']}</td>";
        echo "<td><strong>{$row['code']}</strong></td>";
        echo "<td>{$row['type']}</td>";
        echo "<td>$used_badge</td>";
        echo "<td>{$row['created_at']}</td>";
        echo "<td>{$row['expires_at']}$expired</td>";
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "<p><em>Aucun code OTP trouvé dans les dernières 24h</em></p>";
}

// ========================================
// RÉCAPITULATIF
// ========================================
echo "<hr>";
echo "<h2>📋 Récapitulatif</h2>";

echo "<ul>";
echo "<li><strong>Templates:</strong> " . ($templates_missing == 0 ? "<span style='color:green;'>✓ OK</span>" : "<span style='color:red;'>✗ Manquants</span>") . "</li>";
echo "<li><strong>Credentials SMS:</strong> " . ($credentials_ok ? "<span style='color:green;'>✓ OK</span>" : "<span style='color:red;'>✗ Non configurés</span>") . "</li>";
echo "</ul>";

echo "<h3>Actions recommandées:</h3>";
echo "<ol>";
if ($templates_missing > 0) {
    echo "<li style='color:red;'><strong>Exécuter update_notification_templates.php pour créer les templates</strong></li>";
}
if (!$credentials_ok) {
    echo "<li style='color:red;'><strong>Configurer sms_lam_account_id et sms_lam_password dans Admin > DietZone > Settings</strong></li>";
}
echo "<li>Tester l'envoi SMS avec le formulaire ci-dessus</li>";
echo "<li>Vérifier les logs d'activité Perfex pour les erreurs SMS</li>";
echo "</ol>";

mysqli_close($conn);
?>
