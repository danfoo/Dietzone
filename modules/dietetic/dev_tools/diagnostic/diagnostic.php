<!DOCTYPE html>
<html>
<head>
    <title>Debug Menu Recettes</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #e74c3c; border-bottom: 3px solid #e74c3c; padding-bottom: 10px; }
        h2 { color: #2c3e50; margin-top: 30px; }
        .success { color: #27ae60; font-weight: bold; }
        .error { color: #e74c3c; font-weight: bold; }
        .info { color: #3498db; }
        .warning { color: #f39c12; font-weight: bold; }
        pre { background: #f8f9fa; padding: 15px; border-left: 4px solid #3498db; overflow-x: auto; }
        .check-item { padding: 10px; margin: 5px 0; background: #f8f9fa; border-radius: 4px; }
        .solution-box { background: #fff3cd; padding: 20px; border-left: 4px solid #ffc107; margin: 20px 0; }
        .command { background: #2c3e50; color: #ecf0f1; padding: 3px 8px; border-radius: 3px; font-family: monospace; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Diagnostic Menu Recettes Dietetic</h1>

<?php
// Configuration
$db_config_file = __DIR__ . '/../../application/config/database.php';

// Vérifier que le fichier de config existe
if (!file_exists($db_config_file)) {
    echo "<p class='error'>❌ Fichier de configuration de base de données non trouvé</p>";
    echo "<p>Chemin recherché: <code>" . $db_config_file . "</code></p>";
    exit;
}

// Charger la configuration
require_once $db_config_file;

// Connexion à la base de données
$conn = @mysqli_connect($db['default']['hostname'], $db['default']['username'], $db['default']['password'], $db['default']['database']);

if (!$conn) {
    echo "<p class='error'>❌ Impossible de se connecter à la base de données</p>";
    echo "<p>Erreur: " . mysqli_connect_error() . "</p>";
    exit;
}

mysqli_set_charset($conn, 'utf8');

$prefix = $db['default']['dbprefix'];
$all_ok = true;

// 1. Vérifier l'existence de la table
echo "<h2>1. Vérification de la table recettes</h2>";
$table_name = $prefix . 'dietic_recipes';
$check_table = mysqli_query($conn, "SHOW TABLES LIKE '$table_name'");
$table_exists = mysqli_num_rows($check_table) > 0;

echo "<div class='check-item'>";
if ($table_exists) {
    echo "<p class='success'>✅ Table <code>$table_name</code> existe</p>";

    // Compter les recettes
    $count_query = mysqli_query($conn, "SELECT COUNT(*) as total,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
        SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected
        FROM `$table_name`");
    $stats = mysqli_fetch_assoc($count_query);

    echo "<p class='info'>📊 Statistiques:</p>";
    echo "<ul>";
    echo "<li>Total: <strong>{$stats['total']}</strong> recettes</li>";
    echo "<li>En attente: <strong>{$stats['pending']}</strong></li>";
    echo "<li>Approuvées: <strong>{$stats['approved']}</strong></li>";
    echo "<li>Rejetées: <strong>{$stats['rejected']}</strong></li>";
    echo "</ul>";
} else {
    echo "<p class='error'>❌ Table <code>$table_name</code> n'existe PAS</p>";
    echo "<p class='warning'>⚠️ Vous devez exécuter la migration pour créer les tables</p>";
    $all_ok = false;
}
echo "</div>";

// 2. Vérifier les fichiers clés
echo "<h2>2. Vérification des fichiers</h2>";

$files_to_check = [
    'Contrôleur Admin' => __DIR__ . '/controllers/Recipes.php',
    'Modèle' => __DIR__ . '/models/Dietetic_recipes_model.php',
    'Vue Liste Admin' => __DIR__ . '/views/admin/recipes/list.php',
    'Vue Formulaire Admin' => __DIR__ . '/views/admin/recipes/form.php',
    'Vue Détail Admin' => __DIR__ . '/views/admin/recipes/view.php',
    'Vue Liste Portal' => __DIR__ . '/views/portal/recipes/list.php',
    'Helper' => __DIR__ . '/helpers/dietetic_helper.php',
];

foreach ($files_to_check as $name => $path) {
    echo "<div class='check-item'>";
    if (file_exists($path)) {
        echo "<p class='success'>✅ $name existe</p>";
        echo "<p style='font-size:11px;color:#888;'>" . str_replace(__DIR__, 'modules/dietetic', $path) . "</p>";
    } else {
        echo "<p class='error'>❌ $name manquant</p>";
        echo "<p style='font-size:11px;color:#888;'>" . str_replace(__DIR__, 'modules/dietetic', $path) . "</p>";
        $all_ok = false;
    }
    echo "</div>";
}

// 3. Vérifier le code du menu dans dietetic.php
echo "<h2>3. Vérification du menu dans dietetic.php</h2>";
$dietetic_file = __DIR__ . '/dietetic.php';
$dietetic_content = file_get_contents($dietetic_file);

echo "<div class='check-item'>";
if (strpos($dietetic_content, 'Bibliothèque de Recettes') !== false) {
    echo "<p class='success'>✅ Code du menu trouvé dans dietetic.php</p>";

    // Extraire le code
    if (preg_match('/\/\/ Recipes Library.*?(?=\/\/|\}$)/s', $dietetic_content, $matches)) {
        echo "<p class='info'>Code du menu:</p>";
        echo "<pre>" . htmlspecialchars(trim($matches[0])) . "</pre>";
    }
} else {
    echo "<p class='error'>❌ Code du menu NON trouvé dans dietetic.php</p>";
    $all_ok = false;
}
echo "</div>";

// 4. Vérifier que le helper charge la fonction dietetic_has_permission
echo "<h2>4. Vérification des fonctions du helper</h2>";
$helper_file = __DIR__ . '/helpers/dietetic_helper.php';
if (file_exists($helper_file)) {
    $helper_content = file_get_contents($helper_file);
    echo "<div class='check-item'>";
    if (strpos($helper_content, 'function dietetic_has_permission') !== false) {
        echo "<p class='success'>✅ Fonction <code>dietetic_has_permission()</code> trouvée</p>";
    } else {
        echo "<p class='error'>❌ Fonction <code>dietetic_has_permission()</code> manquante</p>";
        $all_ok = false;
    }
    echo "</div>";
}

// 5. Tester l'URL du contrôleur
echo "<h2>5. Test de l'URL du contrôleur</h2>";
echo "<div class='check-item'>";
$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
$admin_url = $base_url . "/admin/dietetic/recipes";
echo "<p class='info'>📍 URL du contrôleur: <a href='$admin_url' target='_blank'>$admin_url</a></p>";
echo "<p style='font-size:12px;color:#666;'>Cliquez sur le lien ci-dessus pour tester l'accès</p>";
echo "</div>";

// 6. Résumé et solutions
echo "<h2>6. Résumé et Solutions</h2>";

if ($all_ok && $table_exists) {
    echo "<div style='background:#d4edda;padding:20px;border-left:4px solid #28a745;margin:20px 0;'>";
    echo "<h3 style='color:#155724;margin-top:0;'>✅ Tous les fichiers sont en place!</h3>";
    echo "<p><strong>Le menu n'apparaît pas?</strong> Voici les étapes à suivre:</p>";
    echo "<ol>";
    echo "<li><strong>Vider le cache Perfex:</strong><br>";
    echo "   Allez dans <span class='command'>Setup → Settings → General</span><br>";
    echo "   Cliquez sur <span class='command'>Clear All Cache</span></li>";
    echo "<li><strong>Déconnexion/Reconnexion:</strong><br>";
    echo "   Déconnectez-vous complètement de l'interface admin<br>";
    echo "   Reconnectez-vous</li>";
    echo "<li><strong>Vider le cache du navigateur:</strong><br>";
    echo "   Appuyez sur <span class='command'>Ctrl+Shift+Delete</span> (Windows)<br>";
    echo "   ou <span class='command'>Cmd+Shift+Delete</span> (Mac)<br>";
    echo "   Ou simplement <span class='command'>Ctrl+F5</span> pour rafraîchir</li>";
    echo "<li><strong>Vérifier les permissions:</strong><br>";
    echo "   Allez dans <span class='command'>Setup → Staff → [Votre compte] → Permissions</span><br>";
    echo "   Assurez-vous que le module <strong>Dietetic</strong> a les permissions <strong>View</strong></li>";
    echo "</ol>";
    echo "</div>";
} else {
    echo "<div class='solution-box'>";
    echo "<h3 style='color:#856404;margin-top:0;'>⚠️ Des éléments sont manquants</h3>";

    if (!$table_exists) {
        echo "<p><strong>La table n'existe pas.</strong> Exécutez la migration:</p>";
        echo "<ol>";
        echo "<li>Allez sur: <code>$base_url/admin/dietetic/notifications/migrations</code></li>";
        echo "<li>Cliquez sur <strong>Installer Recipe Library</strong></li>";
        echo "</ol>";
    }

    if (!$all_ok) {
        echo "<p><strong>Des fichiers sont manquants.</strong> Assurez-vous que tous les commits ont été récupérés:</p>";
        echo "<pre>git pull origin claude/continue-dietzone-project-01S24KTSE5AMoww38BXPw6r8</pre>";
    }
    echo "</div>";
}

// 7. Informations de version
echo "<h2>7. Informations</h2>";
echo "<div class='check-item'>";
echo "<p><strong>Date du diagnostic:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<p><strong>Version PHP:</strong> " . phpversion() . "</p>";
echo "<p><strong>Version MySQL:</strong> " . mysqli_get_server_info($conn) . "</p>";
echo "<p><strong>Préfixe de table:</strong> <code>$prefix</code></p>";
echo "</div>";

mysqli_close($conn);
?>

        <hr>
        <p style="text-align:center;color:#888;font-size:12px;">
            Script de diagnostic automatique - Dietetic Module
        </p>
    </div>
</body>
</html>
