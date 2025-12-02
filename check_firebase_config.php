<?php
/**
 * Script de diagnostic Firebase Push Notifications
 * À exécuter via : https://app.dietsenegal.net/check_firebase_config.php
 */

// Load CodeIgniter bootstrap
require_once('application/config/database.php');

// Database connection
$conn = new mysqli(
    $db['default']['hostname'],
    $db['default']['username'],
    $db['default']['password'],
    $db['default']['database']
);

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

echo "<h1>🔍 Diagnostic Firebase Push Notifications</h1>";
echo "<style>body { font-family: Arial, sans-serif; padding: 20px; } table { border-collapse: collapse; width: 100%; margin: 20px 0; } th, td { border: 1px solid #ddd; padding: 12px; text-align: left; } th { background-color: #01807B; color: white; } .success { color: #22543d; background: #f0fff4; padding: 10px; border-radius: 5px; } .error { color: #c53030; background: #fff5f5; padding: 10px; border-radius: 5px; } .warning { color: #744210; background: #fffaf0; padding: 10px; border-radius: 5px; }</style>";

// Check if notification settings table exists
$table_exists = $conn->query("SHOW TABLES LIKE 'tbldietic_notification_settings'");
if ($table_exists->num_rows == 0) {
    echo "<div class='error'>❌ Table 'tbldietic_notification_settings' n'existe pas!</div>";
    echo "<p>Vous devez exécuter les migrations de notification.</p>";
    $conn->close();
    exit;
}

echo "<div class='success'>✅ Table 'tbldietic_notification_settings' existe</div>";

// Fetch Firebase settings
$query = "SELECT setting_key, setting_value
          FROM tbldietic_notification_settings
          WHERE setting_key LIKE 'firebase%'
          OR setting_key = 'push_enabled'
          ORDER BY setting_key";

$result = $conn->query($query);

echo "<h2>📋 Configuration Firebase actuelle</h2>";
echo "<table>";
echo "<tr><th>Clé</th><th>Valeur</th><th>Status</th></tr>";

$settings = [];
$required_fields = [
    'push_enabled' => 'Push notifications activées',
    'firebase_api_key' => 'API Key (Firebase Console)',
    'firebase_project_id' => 'Project ID',
    'firebase_messaging_sender_id' => 'Messaging Sender ID',
    'firebase_app_id' => 'App ID',
    'firebase_vapid_key' => 'VAPID Key (Cloud Messaging)',
];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $settings[$row['setting_key']] = $row['setting_value'];

        $key = $row['setting_key'];
        $value = $row['setting_value'];
        $display_value = '';
        $status = '';

        if (empty($value)) {
            $display_value = '<em style="color: #999;">Non configuré</em>';
            $status = "❌ Manquant";
        } else {
            // Mask sensitive values
            if (strlen($value) > 20) {
                $display_value = substr($value, 0, 20) . '...' . substr($value, -5);
            } else {
                $display_value = $value;
            }
            $status = "✅ Configuré";
        }

        echo "<tr>";
        echo "<td><strong>" . htmlspecialchars($key) . "</strong></td>";
        echo "<td><code>" . htmlspecialchars($display_value) . "</code></td>";
        echo "<td>" . $status . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='3' style='text-align: center; color: #999;'>Aucune configuration trouvée</td></tr>";
}

echo "</table>";

// Check for missing required fields
echo "<h2>✅ Vérification des champs requis</h2>";
$missing_fields = [];
foreach ($required_fields as $field => $description) {
    if (!isset($settings[$field]) || empty($settings[$field])) {
        $missing_fields[] = "$description ($field)";
    }
}

if (empty($missing_fields)) {
    echo "<div class='success'>✅ Tous les champs requis sont configurés!</div>";
} else {
    echo "<div class='error'>";
    echo "<strong>❌ Champs manquants :</strong><br>";
    echo "<ul>";
    foreach ($missing_fields as $field) {
        echo "<li>$field</li>";
    }
    echo "</ul>";
    echo "</div>";

    echo "<div class='warning'>";
    echo "<h3>📝 Comment configurer Firebase :</h3>";
    echo "<ol>";
    echo "<li>Allez sur <a href='https://console.firebase.google.com' target='_blank'>Firebase Console</a></li>";
    echo "<li>Sélectionnez votre projet ou créez-en un nouveau</li>";
    echo "<li>Allez dans <strong>Paramètres du projet</strong> (icône engrenage)</li>";
    echo "<li>Sous <strong>Vos applications</strong>, ajoutez une application Web si ce n'est pas déjà fait</li>";
    echo "<li>Copiez les valeurs de configuration (apiKey, projectId, messagingSenderId, appId)</li>";
    echo "<li>Allez dans <strong>Cloud Messaging</strong> &gt; <strong>Configuration Web</strong></li>";
    echo "<li>Générez une paire de clés Web (VAPID key) si elle n'existe pas</li>";
    echo "<li>Entrez ces valeurs dans : <strong>Admin Dietetic &gt; Notifications &gt; Paramètres Firebase</strong></li>";
    echo "</ol>";
    echo "</div>";
}

// Check FCM tokens table
echo "<h2>📱 Tokens FCM enregistrés</h2>";
$token_query = "SELECT COUNT(*) as total,
                       SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active
                FROM tbldietic_fcm_tokens";
$token_result = $conn->query($token_query);

if ($token_result) {
    $token_data = $token_result->fetch_assoc();
    echo "<div class='success'>";
    echo "Total de tokens: <strong>" . $token_data['total'] . "</strong><br>";
    echo "Tokens actifs: <strong>" . $token_data['active'] . "</strong>";
    echo "</div>";

    // Show recent tokens
    $recent_query = "SELECT patient_id, device_type, device_name, is_active, created_at, last_used_at
                     FROM tbldietic_fcm_tokens
                     ORDER BY created_at DESC LIMIT 5";
    $recent_result = $conn->query($recent_query);

    if ($recent_result && $recent_result->num_rows > 0) {
        echo "<h3>📋 Derniers tokens enregistrés</h3>";
        echo "<table>";
        echo "<tr><th>Patient ID</th><th>Type</th><th>Device</th><th>Status</th><th>Créé le</th><th>Dernier usage</th></tr>";
        while ($row = $recent_result->fetch_assoc()) {
            $status = $row['is_active'] ? '<span style="color: green;">✅ Actif</span>' : '<span style="color: red;">❌ Inactif</span>';
            echo "<tr>";
            echo "<td>" . $row['patient_id'] . "</td>";
            echo "<td>" . $row['device_type'] . "</td>";
            echo "<td>" . htmlspecialchars(substr($row['device_name'], 0, 30)) . "...</td>";
            echo "<td>$status</td>";
            echo "<td>" . $row['created_at'] . "</td>";
            echo "<td>" . $row['last_used_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} else {
    echo "<div class='error'>❌ Impossible de récupérer les tokens</div>";
}

// Check Service Worker accessibility
echo "<h2>🛠️ Service Worker</h2>";
$sw_path = __DIR__ . '/firebase-messaging-sw.js';
if (file_exists($sw_path)) {
    echo "<div class='success'>✅ Service Worker existe : <code>/firebase-messaging-sw.js</code></div>";
    echo "<p>Accessible via : <a href='" . "https://app.dietsenegal.net/firebase-messaging-sw.js" . "' target='_blank'>Tester le Service Worker</a></p>";
} else {
    echo "<div class='error'>❌ Service Worker introuvable : <code>/firebase-messaging-sw.js</code></div>";
}

// Recommendations
echo "<h2>💡 Recommandations</h2>";
echo "<ul>";
if (isset($settings['push_enabled']) && $settings['push_enabled'] == '1') {
    echo "<li class='success'>✅ Les notifications push sont activées</li>";
} else {
    echo "<li class='error'>❌ Les notifications push sont désactivées. Activez-les dans les paramètres.</li>";
}

if (!empty($missing_fields)) {
    echo "<li class='error'>❌ Complétez la configuration Firebase avec les champs manquants</li>";
} else {
    echo "<li class='success'>✅ Configuration Firebase complète</li>";
}

echo "<li>🔐 Assurez-vous que HTTPS est activé (Firebase requiert HTTPS)</li>";
echo "<li>🌐 Vérifiez que le domaine est autorisé dans Firebase Console &gt; Authentication &gt; Authorized domains</li>";
echo "</ul>";

$conn->close();

echo "<hr>";
echo "<p style='text-align: center; color: #999;'>Script de diagnostic - " . date('Y-m-d H:i:s') . "</p>";
?>
