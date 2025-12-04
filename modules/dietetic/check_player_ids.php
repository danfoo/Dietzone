<?php
/**
 * Check OneSignal Player IDs in Database - Version robuste
 *
 * Usage: Access via browser to see Player IDs status
 */

// Afficher les erreurs pour diagnostiquer
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Set content type
header('Content-Type: text/plain; charset=utf-8');

echo "🔍 Diagnostic OneSignal Player IDs\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Define BASEPATH to avoid CodeIgniter protection
if (!defined('BASEPATH')) {
    define('BASEPATH', dirname(__FILE__) . '/../../');
}

// Load database configuration
$db_config_path = dirname(__FILE__) . '/../../application/config/database.php';

echo "1. Vérification du fichier database.php...\n";
if (!file_exists($db_config_path)) {
    die("❌ Erreur : Fichier database.php introuvable à : $db_config_path\n");
}
echo "   ✅ Fichier trouvé\n\n";

// Include database config
echo "2. Chargement de la configuration...\n";
try {
    include($db_config_path);
    echo "   ✅ Configuration chargée\n\n";
} catch (Exception $e) {
    die("❌ Erreur lors du chargement : " . $e->getMessage() . "\n");
}

// Get active database config
if (!isset($db) || !isset($active_group)) {
    die("❌ Erreur : Variables de configuration manquantes\n");
}

$db_config = $db[$active_group] ?? [];

if (empty($db_config)) {
    die("❌ Erreur : Configuration de base de données vide\n");
}

echo "3. Connexion à la base de données...\n";
echo "   Hôte : " . $db_config['hostname'] . "\n";
echo "   Base : " . $db_config['database'] . "\n";

// Connect to database
$mysqli = @new mysqli(
    $db_config['hostname'],
    $db_config['username'],
    $db_config['password'],
    $db_config['database']
);

if ($mysqli->connect_error) {
    die("❌ Erreur de connexion : " . $mysqli->connect_error . "\n");
}

echo "   ✅ Connecté avec succès\n\n";

// Set charset
$mysqli->set_charset('utf8mb4');

// Get table prefix
$db_prefix = isset($db_config['dbprefix']) ? $db_config['dbprefix'] : 'tbl';
$table_name = $db_prefix . 'dietic_fcm_tokens';

echo "4. Vérification de la table...\n";
echo "   Table : $table_name\n";

// Check if table exists
$table_check = $mysqli->query("SHOW TABLES LIKE '$table_name'");
if (!$table_check || $table_check->num_rows == 0) {
    $mysqli->close();
    die("❌ Erreur : La table $table_name n'existe pas\n");
}
echo "   ✅ Table existe\n\n";

// Check if onesignal_player_id column exists
echo "5. Vérification de la colonne onesignal_player_id...\n";
$columns_query = $mysqli->query("SHOW COLUMNS FROM $table_name LIKE 'onesignal_player_id'");

if (!$columns_query || $columns_query->num_rows == 0) {
    echo "   ❌ La colonne 'onesignal_player_id' N'EXISTE PAS\n\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "🔧 SOLUTION\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    echo "La migration OneSignal n'a pas été exécutée.\n\n";
    echo "Exécutez la migration ici :\n";
    echo "https://app.dietsenegal.net/admin/dietetic/notifications/deploy_onesignal\n\n";
    $mysqli->close();
    exit;
}
echo "   ✅ Colonne existe\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📊 ANALYSE DES DONNÉES\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Count total records
$result = $mysqli->query("SELECT COUNT(*) as total FROM $table_name");
$row = $result->fetch_assoc();
$total_records = $row['total'];
echo "Total d'enregistrements : $total_records\n\n";

// Count with Player ID
$result = $mysqli->query("
    SELECT COUNT(*) as total
    FROM $table_name
    WHERE onesignal_player_id IS NOT NULL
    AND onesignal_player_id != ''
");
$row = $result->fetch_assoc();
$with_player_id = $row['total'];

// Count active with Player ID
$result = $mysqli->query("
    SELECT COUNT(*) as total
    FROM $table_name
    WHERE onesignal_player_id IS NOT NULL
    AND onesignal_player_id != ''
    AND is_active = 1
");
$row = $result->fetch_assoc();
$active_with_player_id = $row['total'];

echo "Avec Player ID : $with_player_id\n";
echo "Actifs avec Player ID : $active_with_player_id\n\n";

if ($with_player_id == 0) {
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "⚠️  PROBLÈME DÉTECTÉ\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

    echo "Aucun Player ID OneSignal n'est enregistré dans la base de données.\n\n";

    echo "Pourtant, OneSignal Dashboard montre 3 devices actifs.\n";
    echo "Cela signifie que le Player ID n'est PAS sauvegardé sur le serveur.\n\n";

    echo "🔍 VÉRIFICATIONS À FAIRE :\n\n";

    echo "1. Dans le portail patient, ouvrez la console (F12)\n";
    echo "2. Cherchez les messages [OneSignal]\n";
    echo "3. Vous devriez voir :\n";
    echo "   [OneSignal] Player ID: xxxxxxxx-xxxx-xxxx\n";
    echo "   [OneSignal] Player ID saved successfully ✅\n\n";

    echo "4. Si vous voyez une ERREUR, copiez-la\n\n";

    echo "5. Vérifiez aussi dans Admin → Activity Log\n";
    echo "   Cherchez '[OneSignal]'\n\n";

} else {
    echo "✅ SUCCÈS\n\n";
    echo "$active_with_player_id Player ID(s) OneSignal actif(s) dans la base\n\n";

    // Show details
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "📋 DÉTAILS DES PLAYER IDS\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

    $result = $mysqli->query("
        SELECT
            id,
            patient_id,
            onesignal_player_id,
            device_type,
            is_active,
            created_at,
            updated_at
        FROM $table_name
        WHERE onesignal_player_id IS NOT NULL
        AND onesignal_player_id != ''
        ORDER BY updated_at DESC
    ");

    while ($row = $result->fetch_assoc()) {
        echo "Player ID: " . $row['onesignal_player_id'] . "\n";
        echo "  Patient ID: " . $row['patient_id'] . "\n";
        echo "  Device: " . ($row['device_type'] ?? 'N/A') . "\n";
        echo "  Status: " . ($row['is_active'] ? 'Actif ✅' : 'Inactif ❌') . "\n";
        echo "  Créé: " . $row['created_at'] . "\n";
        echo "  Mis à jour: " . $row['updated_at'] . "\n\n";
    }
}

$mysqli->close();

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Diagnostic terminé\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
