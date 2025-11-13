<?php
/**
 * Script de vérification du statut Firebase
 * Accès: https://app.dietsenegal.net/check_firebase_status.php
 */

define('BASEPATH', TRUE);
require_once('application/config/database.php');

echo "<h1>Vérification Statut Migration Firebase</h1>";
echo "<style>body{font-family:sans-serif;padding:20px}pre{background:#f5f5f5;padding:10px;border-radius:5px}.ok{color:green;font-weight:bold}.error{color:red;font-weight:bold}.warning{color:orange;font-weight:bold}</style>";

// Connexion DB
$conn = new mysqli($db['default']['hostname'], $db['default']['username'], $db['default']['password'], $db['default']['database']);
if ($conn->connect_error) {
    echo "<p class='error'>❌ Erreur de connexion: " . $conn->connect_error . "</p>";
    exit;
}

$table_prefix = isset($db['default']['dbprefix']) ? $db['default']['dbprefix'] : 'tbl';

echo "<h2>Test 1: Table dietic_fcm_tokens</h2>";
$table_name = $table_prefix . 'dietic_fcm_tokens';
$result = $conn->query("SHOW TABLES LIKE '$table_name'");
if ($result && $result->num_rows > 0) {
    echo "<p class='ok'>✅ Table $table_name existe</p>";

    // Compter les tokens
    $count_result = $conn->query("SELECT COUNT(*) as count FROM $table_name");
    if ($count_result) {
        $count = $count_result->fetch_assoc()['count'];
        echo "<p>📊 Nombre de tokens FCM: <strong>$count</strong></p>";
    }

    // Structure de la table
    echo "<h3>Structure de la table:</h3>";
    $structure = $conn->query("DESCRIBE $table_name");
    if ($structure) {
        echo "<table border='1' cellpadding='5'><tr><th>Champ</th><th>Type</th><th>Null</th><th>Key</th></tr>";
        while($row = $structure->fetch_assoc()) {
            echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td><td>{$row['Null']}</td><td>{$row['Key']}</td></tr>";
        }
        echo "</table>";
    }
} else {
    echo "<p class='error'>❌ Table $table_name n'existe PAS</p>";
    echo "<p>Cette table devrait être créée par la migration Firebase.</p>";
}

echo "<h2>Test 2: Colonne channel_push dans dietic_notification_preferences</h2>";
$prefs_table = $table_prefix . 'dietic_notification_preferences';
$result = $conn->query("SHOW TABLES LIKE '$prefs_table'");
if ($result && $result->num_rows > 0) {
    echo "<p class='ok'>✅ Table $prefs_table existe</p>";

    // Vérifier la colonne channel_push
    $columns = $conn->query("SHOW COLUMNS FROM $prefs_table LIKE 'channel_push'");
    if ($columns && $columns->num_rows > 0) {
        $col = $columns->fetch_assoc();
        echo "<p class='ok'>✅ Colonne 'channel_push' existe</p>";
        echo "<p>Type: <strong>{$col['Type']}</strong>, Default: <strong>{$col['Default']}</strong></p>";
    } else {
        echo "<p class='error'>❌ Colonne 'channel_push' n'existe PAS</p>";
        echo "<p>Cette colonne devrait être ajoutée par la migration Firebase.</p>";
    }

    // Afficher toutes les colonnes
    echo "<h3>Toutes les colonnes de la table:</h3>";
    $all_cols = $conn->query("SHOW COLUMNS FROM $prefs_table");
    if ($all_cols) {
        echo "<ul>";
        while($col = $all_cols->fetch_assoc()) {
            echo "<li><strong>{$col['Field']}</strong> ({$col['Type']})</li>";
        }
        echo "</ul>";
    }
} else {
    echo "<p class='error'>❌ Table $prefs_table n'existe PAS</p>";
}

echo "<h2>Test 3: Colonne channel dans dietic_notification_logs</h2>";
$logs_table = $table_prefix . 'dietic_notification_logs';
$result = $conn->query("SHOW TABLES LIKE '$logs_table'");
if ($result && $result->num_rows > 0) {
    echo "<p class='ok'>✅ Table $logs_table existe</p>";

    // Vérifier le type enum de la colonne channel
    $columns = $conn->query("SHOW COLUMNS FROM $logs_table LIKE 'channel'");
    if ($columns && $columns->num_rows > 0) {
        $col = $columns->fetch_assoc();
        echo "<p>Colonne 'channel' - Type: <strong>{$col['Type']}</strong></p>";

        if (strpos($col['Type'], 'push') !== false) {
            echo "<p class='ok'>✅ Le type enum inclut 'push'</p>";
        } else {
            echo "<p class='warning'>⚠️ Le type enum n'inclut PAS 'push' - Migration incomplète</p>";
        }
    }
} else {
    echo "<p class='error'>❌ Table $logs_table n'existe PAS</p>";
}

echo "<h2>Test 4: Paramètres Firebase dans dietic_notification_settings</h2>";
$settings_table = $table_prefix . 'dietic_notification_settings';
$result = $conn->query("SELECT * FROM $settings_table WHERE setting_key LIKE '%firebase%' OR setting_key LIKE '%push%'");
if ($result && $result->num_rows > 0) {
    echo "<p class='ok'>✅ Paramètres Firebase trouvés:</p>";
    echo "<table border='1' cellpadding='5'><tr><th>Clé</th><th>Valeur</th><th>Description</th></tr>";
    while($row = $result->fetch_assoc()) {
        $value = empty($row['setting_value']) ? '<em>Vide</em>' : '[CONFIGURÉ]';
        echo "<tr><td>{$row['setting_key']}</td><td>$value</td><td>{$row['description']}</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p class='error'>❌ Aucun paramètre Firebase trouvé</p>";
}

echo "<h2>📋 Résumé et Diagnostic</h2>";

// Calculer le statut
$fcm_table_exists = $conn->query("SHOW TABLES LIKE '{$table_prefix}dietic_fcm_tokens'")->num_rows > 0;
$channel_push_exists = false;

if ($conn->query("SHOW TABLES LIKE '{$table_prefix}dietic_notification_preferences'")->num_rows > 0) {
    $col_check = $conn->query("SHOW COLUMNS FROM {$table_prefix}dietic_notification_preferences LIKE 'channel_push'");
    $channel_push_exists = $col_check && $col_check->num_rows > 0;
}

$firebase_fully_installed = $fcm_table_exists && $channel_push_exists;

echo "<table border='1' cellpadding='10' style='font-size:16px'>";
echo "<tr><th>Critère</th><th>Statut</th></tr>";
echo "<tr><td>Table dietic_fcm_tokens</td><td>" . ($fcm_table_exists ? "<span class='ok'>✅ OK</span>" : "<span class='error'>❌ MANQUANT</span>") . "</td></tr>";
echo "<tr><td>Colonne channel_push</td><td>" . ($channel_push_exists ? "<span class='ok'>✅ OK</span>" : "<span class='error'>❌ MANQUANT</span>") . "</td></tr>";
echo "<tr><td><strong>Firebase Installé</strong></td><td>" . ($firebase_fully_installed ? "<span class='ok'>✅ OUI</span>" : "<span class='error'>❌ NON</span>") . "</td></tr>";
echo "</table>";

if (!$firebase_fully_installed) {
    echo "<h3 class='error'>❌ Migration Firebase INCOMPLÈTE</h3>";
    echo "<p><strong>Action recommandée:</strong></p>";
    echo "<ol>";
    if (!$fcm_table_exists) {
        echo "<li>La table dietic_fcm_tokens n'existe pas - Exécuter la migration Firebase</li>";
    }
    if (!$channel_push_exists) {
        echo "<li>La colonne channel_push n'existe pas - Exécuter la migration Firebase</li>";
    }
    echo "<li>Aller sur: <a href='/admin/dietetic/notifications/migrations'>Admin → Notifications → Migrations</a></li>";
    echo "<li>Cliquer sur 'Exécuter' pour la migration Firebase</li>";
    echo "</ol>";
} else {
    echo "<h3 class='ok'>✅ Migration Firebase COMPLÈTE</h3>";
    echo "<p>Le statut devrait s'afficher comme 'Installé' dans l'interface admin.</p>";
    echo "<p>Si ce n'est pas le cas, il peut y avoir un problème de cache. Essayez de:</p>";
    echo "<ul>";
    echo "<li>Rafraîchir la page des migrations (Ctrl+F5)</li>";
    echo "<li>Vider le cache du navigateur</li>";
    echo "<li>Vérifier la console JavaScript pour des erreurs</li>";
    echo "</ul>";
}

$conn->close();

echo "<hr><p><em>Diagnostic terminé - " . date('Y-m-d H:i:s') . "</em></p>";
?>
