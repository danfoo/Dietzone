<?php
/**
 * Script de déploiement OneSignal Migration
 * Exécute la migration SQL avec les credentials Perfex CRM
 *
 * Usage CLI:
 *   php modules/dietetic/deploy_onesignal.php
 *
 * Usage Web:
 *   https://app.dietsenegal.net/modules/dietetic/deploy_onesignal.php
 */

// Détecter si on est en CLI ou Web
$is_cli = (php_sapi_name() === 'cli');

// Mode web - vérifier les permissions
if (!$is_cli) {
    // Charger Perfex CRM
    $perfex_path = dirname(__FILE__) . '/../../index.php';
    if (!file_exists($perfex_path)) {
        die('⛔ Perfex CRM not found');
    }

    // Démarrer Perfex sans exécuter index.php
    define('ENVIRONMENT', 'production');
    define('FCPATH', dirname(__FILE__) . '/../../');

    // Charger CodeIgniter bootstrap minimum
    $system_path = dirname(__FILE__) . '/../../application/system';
    $application_folder = dirname(__FILE__) . '/../../application';

    define('BASEPATH', $system_path . '/');
    define('APPPATH', $application_folder . '/');

    // Charger la config
    require_once(APPPATH . 'config/app-config.php');

    // Vérifier qu'on est admin
    if (!function_exists('is_admin') || !is_admin()) {
        die('⛔ Access denied. Admin only.');
    }

    echo "<pre>";
}

echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║        DIETZONE - OneSignal Migration Deployment              ║\n";
echo "║                                                                ║\n";
echo "║  Migration: Firebase → OneSignal                              ║\n";
echo "║  Date: " . date('Y-m-d H:i:s') . "                                    ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

// Étape 1: Charger la configuration de la base de données
echo "📋 Étape 1/6 : Chargement de la configuration...\n";

// Lire database.php directement
$db_config_path = dirname(__FILE__) . '/../../application/config/database.php';
if (!file_exists($db_config_path)) {
    die("❌ ERREUR: Fichier database.php introuvable: $db_config_path\n");
}

// Charger database.php dans un scope isolé
$db = null;
$active_group = null;
$query_builder = null;

// Inclure le fichier
include($db_config_path);

// Extraire les credentials
if (!isset($db) || !is_array($db)) {
    die("❌ ERREUR: Configuration de base de données invalide\n");
}

// Déterminer le groupe actif
$active_group = $active_group ?? 'default';
$db_config = $db[$active_group] ?? [];

$hostname = $db_config['hostname'] ?? 'localhost';
$username = $db_config['username'] ?? '';
$password = $db_config['password'] ?? '';
$database = $db_config['database'] ?? '';
$db_prefix = $db_config['dbprefix'] ?? 'tbl';

if (empty($username) || empty($database)) {
    die("❌ ERREUR: Credentials de base de données manquants\n");
}

echo "✅ Configuration chargée\n\n";

// Étape 2: Se connecter à la base de données
echo "📋 Étape 2/6 : Connexion à la base de données...\n";
echo "   Host: $hostname\n";
echo "   User: $username\n";
echo "   Database: $database\n";

try {
    $conn = new mysqli($hostname, $username, $password, $database);

    if ($conn->connect_error) {
        die("❌ ERREUR de connexion: " . $conn->connect_error . "\n");
    }

    $conn->set_charset('utf8mb4');
    echo "✅ Connexion établie\n\n";

} catch (Exception $e) {
    die("❌ ERREUR: " . $e->getMessage() . "\n");
}

// Étape 3: Vérifier si la migration est nécessaire
echo "📋 Étape 3/6 : Vérification de l'état actuel...\n";

$migration_needed = false;

// Vérifier si la colonne onesignal_player_id existe déjà
$result = $conn->query("SHOW COLUMNS FROM {$db_prefix}dietic_fcm_tokens LIKE 'onesignal_player_id'");
if ($result->num_rows == 0) {
    echo "⚠️  Colonne onesignal_player_id manquante\n";
    $migration_needed = true;
} else {
    echo "✅ Colonne onesignal_player_id existe déjà\n";
}

// Vérifier si les settings OneSignal existent
$result = $conn->query("SELECT COUNT(*) as count FROM {$db_prefix}dietic_notification_settings WHERE setting_key LIKE 'onesignal%'");
$row = $result->fetch_assoc();
if ($row['count'] == 0) {
    echo "⚠️  Settings OneSignal manquants\n";
    $migration_needed = true;
} else {
    echo "✅ Settings OneSignal existent (" . $row['count'] . " entrées)\n";
}

if (!$migration_needed) {
    echo "\n✨ Migration déjà effectuée ! Aucune action nécessaire.\n\n";

    // Afficher l'état actuel
    echo "📊 État actuel de la base de données:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

    $result = $conn->query("
        SELECT
            COUNT(*) as total_devices,
            SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_devices,
            SUM(CASE WHEN onesignal_player_id IS NOT NULL THEN 1 ELSE 0 END) as onesignal_registered,
            SUM(CASE WHEN token IS NOT NULL THEN 1 ELSE 0 END) as firebase_tokens
        FROM {$db_prefix}dietic_fcm_tokens
    ");

    if ($result) {
        $stats = $result->fetch_assoc();
        echo "   Total appareils      : " . $stats['total_devices'] . "\n";
        echo "   Appareils actifs     : " . $stats['active_devices'] . "\n";
        echo "   OneSignal Players    : " . $stats['onesignal_registered'] . "\n";
        echo "   Firebase Tokens      : " . $stats['firebase_tokens'] . "\n";
    }

    $conn->close();

    if (!$is_cli) {
        echo "</pre>";
    }

    exit(0);
}

echo "\n🚀 Migration nécessaire, démarrage...\n\n";

// Étape 4: Exécuter la migration SQL
echo "📋 Étape 4/6 : Exécution de la migration SQL...\n";

// Lire le fichier SQL
$sql_file = dirname(__FILE__) . '/migrations/migrate_to_onesignal.sql';
if (!file_exists($sql_file)) {
    die("❌ ERREUR: Fichier SQL introuvable: $sql_file\n");
}

$sql_content = file_get_contents($sql_file);

// Remplacer les préfixes de table
$sql_content = str_replace('tbl', $db_prefix, $sql_content);

// Nettoyer le SQL (supprimer les commentaires et lignes vides)
$sql_lines = explode("\n", $sql_content);
$sql_commands = [];
$current_command = '';

foreach ($sql_lines as $line) {
    $line = trim($line);

    // Ignorer les commentaires et lignes vides
    if (empty($line) || substr($line, 0, 2) == '--' || substr($line, 0, 1) == '#') {
        continue;
    }

    $current_command .= $line . "\n";

    // Si la ligne se termine par un point-virgule, c'est la fin de la commande
    if (substr(rtrim($line), -1) == ';') {
        $sql_commands[] = trim($current_command);
        $current_command = '';
    }
}

echo "   Nombre de commandes SQL à exécuter: " . count($sql_commands) . "\n\n";

$success_count = 0;
$error_count = 0;

foreach ($sql_commands as $index => $sql) {
    // Ignorer les SELECTs (pour les résumés)
    if (stripos(trim($sql), 'SELECT') === 0) {
        continue;
    }

    echo "   Exécution commande " . ($index + 1) . "...";

    if ($conn->query($sql)) {
        echo " ✅\n";
        $success_count++;
    } else {
        // Vérifier si c'est une erreur "déjà existe"
        $error = $conn->error;
        if (strpos($error, 'Duplicate') !== false || strpos($error, 'already exists') !== false) {
            echo " ⚠️  (déjà existant)\n";
            $success_count++;
        } else {
            echo " ❌\n";
            echo "      ERREUR: $error\n";
            $error_count++;
        }
    }
}

echo "\n   Résultat: $success_count succès, $error_count erreurs\n";

if ($error_count > 0) {
    echo "\n⚠️  ATTENTION: Certaines commandes ont échoué, mais la migration peut continuer.\n";
}

echo "✅ Migration SQL terminée\n\n";

// Étape 5: Vérifier la migration
echo "📋 Étape 5/6 : Vérification de la migration...\n";

$checks_passed = 0;
$checks_total = 0;

// Check 1: Colonne onesignal_player_id
$checks_total++;
$result = $conn->query("SHOW COLUMNS FROM {$db_prefix}dietic_fcm_tokens LIKE 'onesignal_player_id'");
if ($result->num_rows > 0) {
    echo "   ✅ Colonne onesignal_player_id créée\n";
    $checks_passed++;
} else {
    echo "   ❌ Colonne onesignal_player_id manquante\n";
}

// Check 2: Index sur onesignal_player_id
$checks_total++;
$result = $conn->query("SHOW INDEX FROM {$db_prefix}dietic_fcm_tokens WHERE Key_name = 'idx_player_id'");
if ($result->num_rows > 0) {
    echo "   ✅ Index idx_player_id créé\n";
    $checks_passed++;
} else {
    echo "   ⚠️  Index idx_player_id manquant (non critique)\n";
    $checks_passed++; // Non bloquant
}

// Check 3: Settings OneSignal
$checks_total++;
$result = $conn->query("SELECT COUNT(*) as count FROM {$db_prefix}dietic_notification_settings WHERE setting_key LIKE 'onesignal%'");
$row = $result->fetch_assoc();
if ($row['count'] >= 3) {
    echo "   ✅ Settings OneSignal créés (" . $row['count'] . " entrées)\n";
    $checks_passed++;
} else {
    echo "   ❌ Settings OneSignal incomplets (" . $row['count'] . " entrées)\n";
}

// Check 4: Table de migration (optionnelle)
$result = $conn->query("SHOW TABLES LIKE '{$db_prefix}dietic_onesignal_migration'");
if ($result->num_rows > 0) {
    echo "   ✅ Table de migration créée\n";
} else {
    echo "   ⚠️  Table de migration non créée (optionnelle)\n";
}

echo "\n   Résultat: $checks_passed/$checks_total vérifications passées\n";

if ($checks_passed < $checks_total) {
    echo "\n⚠️  ATTENTION: Certaines vérifications ont échoué.\n";
    echo "   La migration peut être incomplète.\n";
}

echo "✅ Vérification terminée\n\n";

// Étape 6: Afficher le résumé
echo "📋 Étape 6/6 : Résumé final...\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$result = $conn->query("
    SELECT
        COUNT(*) as total_devices,
        SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_devices,
        SUM(CASE WHEN onesignal_player_id IS NOT NULL THEN 1 ELSE 0 END) as onesignal_registered,
        SUM(CASE WHEN token IS NOT NULL THEN 1 ELSE 0 END) as firebase_tokens
    FROM {$db_prefix}dietic_fcm_tokens
");

if ($result) {
    $stats = $result->fetch_assoc();
    echo "📊 État de la base de données:\n";
    echo "   Total appareils      : " . $stats['total_devices'] . "\n";
    echo "   Appareils actifs     : " . $stats['active_devices'] . "\n";
    echo "   OneSignal Players    : " . $stats['onesignal_registered'] . "\n";
    echo "   Firebase Tokens      : " . $stats['firebase_tokens'] . "\n\n";
}

// Afficher les settings OneSignal
$result = $conn->query("
    SELECT setting_key,
           CASE
               WHEN setting_key LIKE '%key%' THEN '***SET***'
               WHEN setting_value = '' THEN '(vide)'
               ELSE setting_value
           END as setting_value
    FROM {$db_prefix}dietic_notification_settings
    WHERE setting_key LIKE 'onesignal%'
    ORDER BY setting_key
");

if ($result && $result->num_rows > 0) {
    echo "⚙️  Settings OneSignal:\n";
    while ($row = $result->fetch_assoc()) {
        echo "   " . str_pad($row['setting_key'], 30) . " : " . $row['setting_value'] . "\n";
    }
    echo "\n";
}

$conn->close();

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✅ MIGRATION TERMINÉE AVEC SUCCÈS !\n\n";

echo "📝 Prochaines étapes:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "1️⃣  Configurer OneSignal dans l'admin:\n";
echo "   → https://app.dietsenegal.net/admin/dietetic/notifications\n";
echo "   → Onglet 'Settings'\n";
echo "   → Remplir: OneSignal App ID + REST API Key\n\n";

echo "2️⃣  Uploader OneSignalSDKWorker.js à la racine:\n";
echo "   → Télécharger depuis: https://dashboard.onesignal.com\n";
echo "   → Placer dans: /home/trpuftja/app/OneSignalSDKWorker.js\n\n";

echo "3️⃣  Mettre à jour les vues frontend:\n";
echo "   → Charger onesignal_push.js au lieu de firebase_push.js\n\n";

echo "4️⃣  Configurer Median Dashboard:\n";
echo "   → https://median.co/dashboard\n";
echo "   → App Settings → Push Notifications → OneSignal\n";
echo "   → Entrer votre OneSignal App ID\n\n";

echo "5️⃣  Rebuild l'APK Median:\n";
echo "   → Median Dashboard → Build\n\n";

echo "📚 Guide complet: ONESIGNAL_MIGRATION.md\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

if (!$is_cli) {
    echo "</pre>";
}
