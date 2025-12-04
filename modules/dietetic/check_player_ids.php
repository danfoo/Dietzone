<?php
/**
 * Check OneSignal Player IDs in Database - Version compatible
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

// Define all CodeIgniter constants needed
$base_path = dirname(__FILE__) . '/../../';

if (!defined('BASEPATH')) {
    define('BASEPATH', $base_path);
}
if (!defined('APPPATH')) {
    define('APPPATH', $base_path . 'application/');
}
if (!defined('ENVIRONMENT')) {
    define('ENVIRONMENT', 'production');
}

echo "1. Lecture directe du fichier database.php...\n";

// Instead of including, we'll read the file and extract database credentials manually
$db_config_path = $base_path . 'application/config/database.php';

if (!file_exists($db_config_path)) {
    die("❌ Erreur : Fichier database.php introuvable\n");
}

// Read the file content
$config_content = file_get_contents($db_config_path);

// Extract database credentials using regex
preg_match("/\['hostname'\]\s*=\s*['\"]([^'\"]+)['\"]/", $config_content, $hostname);
preg_match("/\['username'\]\s*=\s*['\"]([^'\"]+)['\"]/", $config_content, $username);
preg_match("/\['password'\]\s*=\s*['\"]([^'\"]+)['\"]/", $config_content, $password);
preg_match("/\['database'\]\s*=\s*['\"]([^'\"]+)['\"]/", $config_content, $database);
preg_match("/\['dbprefix'\]\s*=\s*['\"]([^'\"]*)['\"]/", $config_content, $dbprefix);

$db_host = $hostname[1] ?? 'localhost';
$db_user = $username[1] ?? '';
$db_pass = $password[1] ?? '';
$db_name = $database[1] ?? '';
$db_prefix = $dbprefix[1] ?? 'tbl';

if (empty($db_user) || empty($db_name)) {
    die("❌ Erreur : Impossible d'extraire les informations de connexion\n");
}

echo "   ✅ Configuration extraite\n\n";

echo "2. Connexion à la base de données...\n";
echo "   Hôte : $db_host\n";
echo "   Base : $db_name\n";
echo "   Préfixe : " . ($db_prefix ?: '(aucun)') . "\n";

// Connect to database
$mysqli = @new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($mysqli->connect_error) {
    die("❌ Erreur de connexion : " . $mysqli->connect_error . "\n");
}

echo "   ✅ Connecté avec succès\n\n";

// Set charset
$mysqli->set_charset('utf8mb4');

// Table name
$table_name = $db_prefix . 'dietic_fcm_tokens';

echo "3. Vérification de la table...\n";
echo "   Table : $table_name\n";

// Check if table exists
$table_check = $mysqli->query("SHOW TABLES LIKE '$table_name'");
if (!$table_check || $table_check->num_rows == 0) {
    $mysqli->close();
    die("❌ Erreur : La table $table_name n'existe pas\n");
}
echo "   ✅ Table existe\n\n";

// Check if onesignal_player_id column exists
echo "4. Vérification de la colonne onesignal_player_id...\n";
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

    echo "🔍 ÉTAPES DE DIAGNOSTIC :\n\n";

    echo "1. Ouvrez le portail patient dans votre navigateur\n";
    echo "   URL : https://app.dietsenegal.net/dietetic/portal\n\n";

    echo "2. Ouvrez la console développeur (F12 → Console)\n\n";

    echo "3. Cherchez les messages [OneSignal]\n";
    echo "   Vous devriez voir :\n";
    echo "   ✅ [OneSignal] Player ID: 44564b9b-b78a-4dda-a085-89e0548cd365\n";
    echo "   ✅ [OneSignal] Player ID saved successfully\n\n";

    echo "4. Si vous voyez une ERREUR comme :\n";
    echo "   ❌ [OneSignal] Failed to save Player ID\n";
    echo "   ❌ [OneSignal] Error saving Player ID\n";
    echo "   ❌ Server returned non-JSON response\n";
    echo "   → Copiez le message d'erreur complet\n\n";

    echo "5. Vérifiez aussi dans : Admin → Utilities → Activity Log\n";
    echo "   Cherchez '[OneSignal]' pour voir les tentatives d'enregistrement\n\n";

    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "🔧 CAUSES POSSIBLES :\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

    echo "A. Le fichier portal_footer.php n'a pas été uploadé\n";
    echo "   → Le code qui enregistre le Player ID est manquant\n\n";

    echo "B. Erreur JavaScript dans la console\n";
    echo "   → Le script s'arrête avant d'enregistrer\n\n";

    echo "C. Erreur 403 ou 500 sur save_onesignal_player_id\n";
    echo "   → Vérifiez l'onglet Network (F12 → Network)\n";
    echo "   → Cherchez la requête POST vers save_onesignal_player_id\n";
    echo "   → Regardez le status code et la réponse\n\n";

} else {
    echo "✅ SUCCÈS !\n\n";
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

    echo "🎉 La page de test devrait maintenant fonctionner !\n";
    echo "   → https://app.dietsenegal.net/admin/dietetic/notifications/test_push\n\n";
}

$mysqli->close();

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Diagnostic terminé\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
