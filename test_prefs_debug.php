<?php
/**
 * Script de diagnostic pour vérifier l'état des préférences de notifications
 * Accès: https://app.dietsenegal.net/test_prefs_debug.php
 */

define('BASEPATH', TRUE);
require_once('application/config/database.php');

echo "<h1>Diagnostic Système de Préférences de Notifications</h1>";
echo "<style>body{font-family:sans-serif;padding:20px}pre{background:#f5f5f5;padding:10px;border-radius:5px}.ok{color:green}.error{color:red}</style>";

// 1. Vérifier la connexion DB
echo "<h2>1. Connexion Base de Données</h2>";
$conn = new mysqli($db['default']['hostname'], $db['default']['username'], $db['default']['password'], $db['default']['database']);
if ($conn->connect_error) {
    echo "<p class='error'>❌ Erreur de connexion: " . $conn->connect_error . "</p>";
    exit;
}
echo "<p class='ok'>✅ Connexion réussie</p>";

// 2. Vérifier la table des préférences
echo "<h2>2. Table des Préférences</h2>";
$table_prefix = isset($db['default']['dbprefix']) ? $db['default']['dbprefix'] : 'tbl';
$prefs_table = $table_prefix . 'dietic_notification_preferences';

$result = $conn->query("SHOW TABLES LIKE '$prefs_table'");
if ($result->num_rows > 0) {
    echo "<p class='ok'>✅ Table $prefs_table existe</p>";

    // Compter les préférences
    $count_result = $conn->query("SELECT COUNT(*) as count FROM $prefs_table");
    $count = $count_result->fetch_assoc()['count'];
    echo "<p>📊 Nombre de préférences enregistrées: <strong>$count</strong></p>";

    // Afficher quelques préférences
    echo "<h3>Dernières préférences:</h3>";
    $prefs_result = $conn->query("SELECT * FROM $prefs_table ORDER BY updated_at DESC LIMIT 5");
    if ($prefs_result && $prefs_result->num_rows > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>patient_id</th><th>reminder_weight</th><th>reminder_water</th><th>notify_recommendation</th><th>channel_email</th><th>channel_push</th><th>updated_at</th></tr>";
        while($row = $prefs_result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['patient_id']}</td>";
            echo "<td>" . ($row['reminder_weight'] ? '✓' : '✗') . "</td>";
            echo "<td>" . ($row['reminder_water'] ? '✓' : '✗') . "</td>";
            echo "<td>" . ($row['notify_recommendation'] ? '✓' : '✗') . "</td>";
            echo "<td>" . ($row['channel_email'] ? '✓' : '✗') . "</td>";
            echo "<td>" . ($row['channel_push'] ? '✓' : '✗') . "</td>";
            echo "<td>{$row['updated_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} else {
    echo "<p class='error'>❌ Table $prefs_table n'existe pas</p>";
}

// 3. Vérifier le fichier preferences.php
echo "<h2>3. Fichier preferences.php</h2>";
$prefs_file = __DIR__ . '/modules/dietetic/views/portal/notifications/preferences.php';
if (file_exists($prefs_file)) {
    echo "<p class='ok'>✅ Fichier existe</p>";
    echo "<p>📅 Dernière modification: <strong>" . date("Y-m-d H:i:s", filemtime($prefs_file)) . "</strong></p>";

    // Vérifier la présence du token CSRF
    $content = file_get_contents($prefs_file);
    if (strpos($content, 'CSRF Token') !== false) {
        echo "<p class='ok'>✅ Token CSRF présent dans le fichier</p>";
    } else {
        echo "<p class='error'>❌ Token CSRF NON trouvé dans le fichier</p>";
    }

    if (strpos($content, 'get_csrf_token_name') !== false) {
        echo "<p class='ok'>✅ Code token CSRF présent</p>";
    } else {
        echo "<p class='error'>❌ Code token CSRF absent</p>";
    }
} else {
    echo "<p class='error'>❌ Fichier n'existe pas</p>";
}

// 4. Vérifier le contrôleur Portal.php
echo "<h2>4. Contrôleur Portal.php</h2>";
$portal_file = __DIR__ . '/modules/dietetic/controllers/Portal.php';
if (file_exists($portal_file)) {
    echo "<p class='ok'>✅ Fichier existe</p>";
    echo "<p>📅 Dernière modification: <strong>" . date("Y-m-d H:i:s", filemtime($portal_file)) . "</strong></p>";

    $content = file_get_contents($portal_file);
    if (strpos($content, '[NOTIF PREFS]') !== false) {
        echo "<p class='ok'>✅ Logs de débogage présents</p>";
    } else {
        echo "<p class='error'>❌ Logs de débogage absents</p>";
    }
} else {
    echo "<p class='error'>❌ Fichier n'existe pas</p>";
}

// 5. Vérifier le modèle
echo "<h2>5. Modèle Dietetic_notifications_model.php</h2>";
$model_file = __DIR__ . '/modules/dietetic/models/Dietetic_notifications_model.php';
if (file_exists($model_file)) {
    echo "<p class='ok'>✅ Fichier existe</p>";
    echo "<p>📅 Dernière modification: <strong>" . date("Y-m-d H:i:s", filemtime($model_file)) . "</strong></p>";

    $content = file_get_contents($model_file);
    if (strpos($content, '[MODEL] update_preferences') !== false) {
        echo "<p class='ok'>✅ Logs de débogage présents dans le modèle</p>";
    } else {
        echo "<p class='error'>❌ Logs de débogage absents du modèle</p>";
    }
} else {
    echo "<p class='error'>❌ Fichier n'existe pas</p>";
}

// 6. Vérifier les logs d'activité récents
echo "<h2>6. Logs d'Activité Récents (Préférences)</h2>";
$logs_table = $table_prefix . 'activity_log';
$result = $conn->query("SELECT * FROM $logs_table WHERE description LIKE '%NOTIF PREFS%' OR description LIKE '%MODEL%' ORDER BY date DESC LIMIT 10");
if ($result && $result->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Date</th><th>Description</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>{$row['date']}</td><td>" . htmlspecialchars($row['description']) . "</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p>ℹ️ Aucun log trouvé (normal si aucune tentative de sauvegarde récente)</p>";
}

// 7. Infos PHP
echo "<h2>7. Configuration PHP</h2>";
echo "<p>Version PHP: <strong>" . phpversion() . "</strong></p>";
echo "<p>Chemin du script: <strong>" . __DIR__ . "</strong></p>";
echo "<p>OpCache activé: <strong>" . (function_exists('opcache_get_status') && opcache_get_status() ? 'OUI' : 'NON') . "</strong></p>";

if (function_exists('opcache_get_status') && opcache_get_status()) {
    echo "<p class='error'>⚠️ OpCache est activé. Si les modifications ne sont pas prises en compte, vider le cache OpCache.</p>";
    echo "<pre>sudo systemctl reload php-fpm  # ou php7.x-fpm</pre>";
}

$conn->close();

echo "<hr><p><em>Diagnostic terminé - " . date('Y-m-d H:i:s') . "</em></p>";
?>
