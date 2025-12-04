<?php
/**
 * OneSignal Settings Diagnostic Script
 *
 * Ce script vérifie l'état des paramètres OneSignal dans la base de données
 * Usage: Accéder via navigateur ou CLI
 */

// Charger la configuration de la base de données
$db_config_path = dirname(__FILE__) . '/../../application/config/database.php';

if (!file_exists($db_config_path)) {
    die("❌ Erreur : Fichier database.php introuvable à : $db_config_path\n");
}

include($db_config_path);

// Récupérer la configuration active
$db_config = $db[$active_group] ?? [];

if (empty($db_config)) {
    die("❌ Erreur : Configuration de base de données introuvable\n");
}

// Connexion à la base de données
$mysqli = new mysqli(
    $db_config['hostname'],
    $db_config['username'],
    $db_config['password'],
    $db_config['database']
);

if ($mysqli->connect_error) {
    die("❌ Erreur de connexion : " . $mysqli->connect_error . "\n");
}

echo "✅ Connexion à la base de données réussie\n\n";

// Récupérer le préfixe de table
$db_prefix = isset($db_config['db_prefix']) ? $db_config['db_prefix'] : 'tbl';
$table_name = $db_prefix . 'dietic_notification_settings';

echo "📋 Table utilisée : $table_name\n\n";

// Vérifier que la table existe
$table_check = $mysqli->query("SHOW TABLES LIKE '$table_name'");
if ($table_check->num_rows == 0) {
    die("❌ Erreur : La table $table_name n'existe pas\n");
}

echo "✅ Table existe\n\n";

// Récupérer les paramètres OneSignal
$query = "SELECT * FROM $table_name WHERE setting_key LIKE 'onesignal%' ORDER BY setting_key";
$result = $mysqli->query($query);

if (!$result) {
    die("❌ Erreur SQL : " . $mysqli->error . "\n");
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "🔍 PARAMÈTRES ONESIGNAL DANS LA BASE DE DONNÉES\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

if ($result->num_rows == 0) {
    echo "❌ Aucun paramètre OneSignal trouvé dans la base de données\n\n";
    echo "🔧 SOLUTION : Exécutez la migration OneSignal\n";
    echo "   URL : https://app.dietsenegal.net/admin/dietetic/notifications/deploy_onesignal\n\n";
} else {
    $settings_found = [];

    while ($row = $result->fetch_assoc()) {
        $settings_found[$row['setting_key']] = $row['setting_value'];

        $key = $row['setting_key'];
        $value = $row['setting_value'];
        $display_value = $value;

        // Masquer les clés sensibles
        if (in_array($key, ['onesignal_rest_api_key', 'onesignal_user_auth_key']) && !empty($value)) {
            $display_value = substr($value, 0, 10) . '...' . substr($value, -5);
        }

        $status = empty($value) ? '❌ VIDE' : '✅ DÉFINI';

        echo "• $key\n";
        echo "  Valeur : $display_value\n";
        echo "  Status : $status\n\n";
    }

    // Vérifier les paramètres requis
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "📊 DIAGNOSTIC\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

    $required = [
        'onesignal_app_id' => 'OneSignal App ID',
        'onesignal_rest_api_key' => 'OneSignal REST API Key',
        'onesignal_web_enabled' => 'OneSignal Web Activé'
    ];

    $optional = [
        'onesignal_user_auth_key' => 'OneSignal User Auth Key (optionnel)'
    ];

    $all_good = true;

    foreach ($required as $key => $label) {
        if (isset($settings_found[$key]) && !empty($settings_found[$key])) {
            echo "✅ $label : Configuré\n";
        } else {
            echo "❌ $label : MANQUANT ou VIDE\n";
            $all_good = false;
        }
    }

    echo "\n";

    foreach ($optional as $key => $label) {
        if (isset($settings_found[$key]) && !empty($settings_found[$key])) {
            echo "✅ $label : Configuré\n";
        } else {
            echo "ℹ️  $label : Non configuré (optionnel)\n";
        }
    }

    echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

    if ($all_good) {
        echo "✅ STATUT : OneSignal est correctement configuré !\n";
    } else {
        echo "❌ STATUT : Configuration incomplète\n\n";
        echo "🔧 SOLUTION :\n";
        echo "   1. Allez sur : https://app.dietsenegal.net/admin/dietetic/notifications/settings\n";
        echo "   2. Faites défiler jusqu'à la section OneSignal (orange)\n";
        echo "   3. Remplissez les champs manquants\n";
        echo "   4. Cliquez sur 'Enregistrer les paramètres'\n";
    }

    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
}

$mysqli->close();
