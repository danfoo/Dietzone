<?php
/**
 * Script de test direct de l'API LAM SMS
 * URL: https://app.dietsenegal.net/modules/dietetic/test_sms_api.php?phone=+221XXXXXXXXX
 */

define('BASEPATH', true);

// Tenter de charger le fichier de config
if (file_exists(__DIR__ . '/../../application/config/app-config.php')) {
    require_once(__DIR__ . '/../../application/config/app-config.php');
} elseif (file_exists(__DIR__ . '/../../application/config/database.php')) {
    require_once(__DIR__ . '/../../application/config/database.php');
}

// Si pas de constantes définies, utiliser valeurs par défaut (à adapter)
if (!defined('APP_DB_HOSTNAME')) {
    define('APP_DB_HOSTNAME', 'localhost');
    define('APP_DB_USERNAME', 'root');
    define('APP_DB_PASSWORD', '');
    define('APP_DB_NAME', 'perfex');
}

require_once(__DIR__ . '/helpers/dietetic_helper.php');

// Connexion à la base de données
$conn = mysqli_connect(APP_DB_HOSTNAME, APP_DB_USERNAME, APP_DB_PASSWORD, APP_DB_NAME);

if (!$conn) {
    die("<h1 style='color:red;'>❌ Erreur de connexion à la base de données</h1>" . mysqli_connect_error());
}

echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Test API LAM SMS</title>";
echo "<style>body{font-family:Arial;padding:20px;} pre{background:#f5f5f5;padding:15px;border-radius:5px;overflow:auto;} .success{color:green;} .error{color:red;} .warning{color:orange;}</style>";
echo "</head><body>";

echo "<h1>🧪 Test API LAM SMS</h1>";
echo "<hr>";

// Debug: Afficher toutes les tables qui contiennent "settings"
echo "<h2>0. Debug - Tables disponibles</h2>";
$tables_result = mysqli_query($conn, "SHOW TABLES LIKE '%settings%'");
echo "<ul>";
while ($table_row = mysqli_fetch_array($tables_result)) {
    echo "<li>{$table_row[0]}</li>";
}
echo "</ul>";

// Essayer différents préfixes de table (NOTIFICATIONS settings en priorité!)
$possible_tables = ['tbldietic_notification_settings', 'dietic_notification_settings', 'tbldietic_settings', 'dietic_settings'];
$settings_table = null;

echo "<h3>Recherche de la bonne table de settings...</h3>";
foreach ($possible_tables as $table) {
    $check = mysqli_query($conn, "SHOW TABLES LIKE '$table'");
    if ($check && mysqli_num_rows($check) > 0) {
        echo "<p class='success'>✓ Table existante: <strong>$table</strong></p>";

        // Vérifier si cette table contient les credentials SMS
        $test_query = "SELECT COUNT(*) as count FROM $table WHERE setting_key LIKE '%sms%' OR setting_key LIKE '%lam%'";
        $test_result = mysqli_query($conn, $test_query);
        if ($test_result) {
            $count_row = mysqli_fetch_assoc($test_result);
            echo "<p style='margin-left:20px;'>→ Contient <strong>{$count_row['count']}</strong> clé(s) SMS/LAM</p>";

            // Utiliser la première table qui contient des clés SMS
            if ($count_row['count'] > 0 && !$settings_table) {
                $settings_table = $table;
                echo "<p class='success' style='margin-left:20px;'><strong>→ ✅ UTILISATION DE CETTE TABLE</strong></p>";
            }
        }
    }
}

if (!$settings_table) {
    echo "<p class='error'>❌ Aucune table de settings avec credentials SMS trouvée !</p>";
    echo "<p>Essayez de vérifier manuellement dans votre base de données.</p>";
    echo "</body></html>";
    mysqli_close($conn);
    exit;
}

// Récupérer les credentials
$sql = "SELECT setting_key, setting_value FROM $settings_table WHERE setting_key IN ('sms_lam_account_id', 'sms_lam_password', 'sms_lam_sender_id')";
$result = mysqli_query($conn, $sql);

// Debug: Si aucun résultat, afficher TOUTES les clés SMS disponibles
if (!$result || mysqli_num_rows($result) == 0) {
    echo "<p class='warning'>⚠️ Aucune clé SMS trouvée avec les noms standards.</p>";
    echo "<p>Recherche de toutes les clés contenant 'sms' ou 'lam'...</p>";

    $debug_sql = "SELECT setting_key, setting_value FROM $settings_table WHERE setting_key LIKE '%sms%' OR setting_key LIKE '%lam%'";
    $debug_result = mysqli_query($conn, $debug_sql);

    if ($debug_result && mysqli_num_rows($debug_result) > 0) {
        echo "<h3>Clés SMS trouvées dans la base:</h3>";
        echo "<ul>";
        while ($row = mysqli_fetch_assoc($debug_result)) {
            $masked_value = !empty($row['setting_value']) ? str_repeat('*', 10) : '(vide)';
            echo "<li><strong>{$row['setting_key']}</strong> = $masked_value</li>";
        }
        echo "</ul>";
    }
}

$credentials = [];
while ($row = mysqli_fetch_assoc($result)) {
    $credentials[$row['setting_key']] = $row['setting_value'];
}

echo "<h2>1. Configuration LAM SMS</h2>";
echo "<table border='1' cellpadding='5' style='border-collapse:collapse;'>";
echo "<tr><th>Paramètre</th><th>Valeur</th><th>Status</th></tr>";

foreach ($credentials as $key => $value) {
    $masked = !empty($value) ? str_repeat('*', min(strlen($value), 15)) : '(vide)';
    $status = !empty($value) ? "<span class='success'>✓ OK</span>" : "<span class='error'>✗ Manquant</span>";
    echo "<tr><td>$key</td><td>$masked</td><td>$status</td></tr>";
}
echo "</table>";

if (empty($credentials['sms_lam_account_id']) || empty($credentials['sms_lam_password'])) {
    echo "<p class='error'><strong>⚠️ Configuration incomplète. Impossible de tester.</strong></p>";
    echo "</body></html>";
    exit;
}

// Test d'envoi
if (isset($_GET['phone'])) {
    $phone = $_GET['phone'];

    echo "<hr>";
    echo "<h2>2. Test d'envoi SMS</h2>";
    echo "<p><strong>Numéro destinataire:</strong> $phone</p>";

    // Nettoyer et formater le numéro
    $phone_clean = preg_replace('/[^0-9]/', '', $phone);
    if (!preg_match('/^221/', $phone_clean) && strlen($phone_clean) == 9) {
        $phone_clean = '221' . $phone_clean;
    }
    // Ajouter le +
    if (!preg_match('/^\+/', $phone_clean)) {
        $phone_clean = '+' . $phone_clean;
    }

    echo "<p><strong>Numéro formaté:</strong> $phone_clean</p>";

    $message = "DietZone - Test SMS: " . date('H:i:s');
    echo "<p><strong>Message:</strong> $message</p>";
    echo "<p><strong>Longueur:</strong> " . strlen($message) . " caractères</p>";

    // Construire la requête API LAM SMS
    $url = 'https://lamsms.lafricamobile.com/api';

    $data = [
        'accountid' => $credentials['sms_lam_account_id'],
        'password' => $credentials['sms_lam_password'],
        'sender' => $credentials['sms_lam_sender_id'] ?? 'API_LAMSMS',
        'ret_id' => 'dietetic_test_' . time(),
        'ret_url' => 'https://app.dietsenegal.net/dietetic/sms_callback',
        'priority' => '2',
        'text' => $message,
        'to' => [$phone_clean]  // Format simple array (corrigé)
    ];

    echo "<hr>";
    echo "<h3>3. Requête envoyée à LAM SMS API</h3>";
    echo "<p><strong>URL:</strong> $url</p>";
    echo "<pre>" . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>";

    // Envoyer via cURL
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json'
        ]
    ]);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    $curl_info = curl_getinfo($ch);
    curl_close($ch);

    echo "<hr>";
    echo "<h3>4. Réponse de l'API</h3>";
    echo "<p><strong>HTTP Code:</strong> $http_code</p>";

    if ($curl_error) {
        echo "<p class='error'><strong>Erreur cURL:</strong> $curl_error</p>";
    }

    echo "<p><strong>Réponse brute:</strong></p>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";

    // Essayer de décoder le JSON
    $response_json = json_decode($response, true);
    if ($response_json) {
        echo "<p><strong>Réponse JSON décodée:</strong></p>";
        echo "<pre>" . print_r($response_json, true) . "</pre>";
    }

    echo "<hr>";
    echo "<h3>5. Diagnostic</h3>";

    if ($http_code == 200 || $http_code == 201) {
        echo "<p class='success'><strong>✅ SMS envoyé avec succès !</strong></p>";
        echo "<p>Le SMS devrait arriver dans quelques secondes.</p>";
    } else {
        echo "<p class='error'><strong>❌ Échec de l'envoi SMS</strong></p>";

        // Analyser les erreurs courantes
        if ($http_code == 401 || $http_code == 403) {
            echo "<p class='error'>→ Erreur d'authentification. Vérifiez vos credentials LAM.</p>";
        } elseif ($http_code == 400) {
            echo "<p class='error'>→ Requête invalide. Vérifiez le format de la requête.</p>";
        } elseif ($http_code == 0) {
            echo "<p class='error'>→ Impossible de contacter l'API LAM. Problème réseau ou URL incorrecte.</p>";
        } else {
            echo "<p class='error'>→ Erreur HTTP $http_code</p>";
        }

        echo "<p><strong>Info cURL complète:</strong></p>";
        echo "<pre>" . print_r($curl_info, true) . "</pre>";
    }

    // Sauvegarder le log dans la base
    $log_message = "TEST SMS API - Phone: $phone_clean - HTTP: $http_code - Response: $response";
    mysqli_query($conn, "INSERT INTO tbllogs (description, date) VALUES ('" . mysqli_real_escape_string($conn, $log_message) . "', NOW())");

} else {
    echo "<hr>";
    echo "<h2>2. Formulaire de test</h2>";
    echo "<form method='GET'>";
    echo "<p>Entrez un numéro de téléphone pour tester l'envoi SMS:</p>";
    echo "<input type='tel' name='phone' placeholder='+221771234567' required style='padding:10px; font-size:16px; width:300px;'>";
    echo "<button type='submit' style='padding:10px 20px; background:#01807B; color:white; border:none; cursor:pointer; font-size:16px;'>Envoyer SMS test</button>";
    echo "</form>";
}

mysqli_close($conn);
echo "</body></html>";
?>
