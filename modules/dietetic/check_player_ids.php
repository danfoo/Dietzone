<?php
/**
 * Check OneSignal Player IDs in Database
 *
 * Usage: Access via browser to see Player IDs status
 */

// Set content type to plain text for better readability
header('Content-Type: text/plain; charset=utf-8');

// Load database configuration
// We need to define BASEPATH to avoid "No direct script access allowed" error
if (!defined('BASEPATH')) {
    define('BASEPATH', dirname(__FILE__) . '/../../');
}

$db_config_path = dirname(__FILE__) . '/../../application/config/database.php';

if (!file_exists($db_config_path)) {
    die("❌ Erreur : Fichier database.php introuvable\n");
}

include($db_config_path);

// Get active database config
$db_config = $db[$active_group] ?? [];

if (empty($db_config)) {
    die("❌ Erreur : Configuration de base de données introuvable\n");
}

// Connect to database
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

// Get table prefix
$db_prefix = isset($db_config['db_prefix']) ? $db_config['db_prefix'] : 'tbl';
$table_name = $db_prefix . 'dietic_fcm_tokens';

echo "📋 Table utilisée : $table_name\n\n";

// Check if table exists
$table_check = $mysqli->query("SHOW TABLES LIKE '$table_name'");
if ($table_check->num_rows == 0) {
    die("❌ Erreur : La table $table_name n'existe pas\n");
}

echo "✅ Table existe\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "🔍 VÉRIFICATION DES PLAYER IDS ONESIGNAL\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Check if onesignal_player_id column exists
$columns_query = $mysqli->query("SHOW COLUMNS FROM $table_name LIKE 'onesignal_player_id'");
if ($columns_query->num_rows == 0) {
    echo "❌ PROBLÈME : La colonne 'onesignal_player_id' n'existe pas dans la table !\n\n";
    echo "🔧 SOLUTION : Exécutez la migration OneSignal :\n";
    echo "   URL : https://app.dietsenegal.net/admin/dietetic/notifications/deploy_onesignal\n\n";
    $mysqli->close();
    exit;
}

echo "✅ Colonne 'onesignal_player_id' existe\n\n";

// Get all records
$query = "SELECT
    id,
    patient_id,
    token,
    onesignal_player_id,
    device_type,
    device_name,
    is_active,
    created_at,
    updated_at
FROM $table_name
ORDER BY updated_at DESC";

$result = $mysqli->query($query);

if (!$result) {
    die("❌ Erreur SQL : " . $mysqli->error . "\n");
}

echo "📊 RÉSULTATS :\n\n";
echo "Total d'enregistrements : " . $result->num_rows . "\n\n";

if ($result->num_rows == 0) {
    echo "⚠️  Aucun enregistrement trouvé dans la table\n\n";
    echo "🔧 DIAGNOSTIC :\n";
    echo "   1. Le patient doit se connecter au portail patient\n";
    echo "   2. Cliquer sur 'Activer' dans la bannière de notification\n";
    echo "   3. Accepter les notifications dans la popup du navigateur\n";
    echo "   4. Le Player ID sera alors enregistré automatiquement\n\n";
} else {
    $count_with_player_id = 0;
    $count_without_player_id = 0;
    $count_active = 0;

    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "LISTE DES ENREGISTREMENTS :\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

    while ($row = $result->fetch_assoc()) {
        $has_player_id = !empty($row['onesignal_player_id']);
        $is_active = $row['is_active'] == 1;

        if ($has_player_id) $count_with_player_id++;
        else $count_without_player_id++;

        if ($is_active) $count_active++;

        echo "📱 ID: " . $row['id'] . "\n";
        echo "   Patient ID: " . $row['patient_id'] . "\n";
        echo "   Device Type: " . ($row['device_type'] ?? 'N/A') . "\n";
        echo "   Device Name: " . ($row['device_name'] ?? 'N/A') . "\n";
        echo "   Status: " . ($is_active ? '✅ Actif' : '❌ Inactif') . "\n";

        if ($has_player_id) {
            echo "   OneSignal Player ID: ✅ " . substr($row['onesignal_player_id'], 0, 20) . "...\n";
        } else {
            echo "   OneSignal Player ID: ❌ VIDE\n";
        }

        if (!empty($row['token'])) {
            echo "   Firebase Token: " . substr($row['token'], 0, 20) . "... (legacy)\n";
        }

        echo "   Créé: " . $row['created_at'] . "\n";
        echo "   Mis à jour: " . $row['updated_at'] . "\n";
        echo "\n";
    }

    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "📊 STATISTIQUES :\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

    echo "Total : " . $result->num_rows . "\n";
    echo "Avec Player ID OneSignal : " . $count_with_player_id . " ✅\n";
    echo "Sans Player ID : " . $count_without_player_id . " ❌\n";
    echo "Actifs : " . $count_active . "\n\n";

    if ($count_with_player_id == 0) {
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "⚠️  PROBLÈME DÉTECTÉ\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

        echo "Aucun Player ID OneSignal n'est enregistré dans la base de données.\n\n";

        echo "🔧 DIAGNOSTIC POSSIBLE :\n\n";

        echo "1. ❌ La méthode save_onesignal_player_id() échoue\n";
        echo "   → Vérifiez les logs Activity Log dans Perfex\n";
        echo "   → Cherchez '[OneSignal]' pour voir les erreurs\n\n";

        echo "2. ❌ Erreur 403 (CSRF token)\n";
        echo "   → Le CSRF token n'est pas valide\n";
        echo "   → Vérifiez que portal_footer.php a été uploadé\n\n";

        echo "3. ❌ Le JavaScript échoue silencieusement\n";
        echo "   → Ouvrez la console du navigateur (F12)\n";
        echo "   → Cherchez les erreurs JavaScript\n\n";

        echo "4. ❌ La requête AJAX n'atteint pas le serveur\n";
        echo "   → Vérifiez l'onglet Network dans les outils développeur\n";
        echo "   → Cherchez la requête POST vers save_onesignal_player_id\n\n";

        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "🔍 VÉRIFICATIONS À FAIRE :\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

        echo "1. Dans le portail patient, ouvrez la console (F12)\n";
        echo "2. Regardez les messages [OneSignal]\n";
        echo "3. Cherchez spécifiquement :\n";
        echo "   - '[OneSignal] Player ID saved successfully' ✅\n";
        echo "   - '[OneSignal] Failed to save Player ID' ❌\n";
        echo "   - '[OneSignal] Error saving Player ID' ❌\n\n";

        echo "4. Dans Perfex Admin → Utilities → Activity Log\n";
        echo "5. Cherchez '[OneSignal]' pour voir les tentatives\n\n";
    } else {
        echo "✅ Tout semble correct !\n";
        echo "   Les Player IDs sont bien enregistrés dans la base de données.\n\n";
    }
}

$mysqli->close();

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Script terminé\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
