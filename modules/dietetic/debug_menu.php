<?php
/**
 * Script de débogage pour le menu Recettes
 * Accès: https://votre-site.com/modules/dietetic/debug_menu.php
 */

// Charger Perfex CRM
require_once __DIR__ . '/../../application/config/database.php';

// Configuration de la base de données
$conn = mysqli_connect($db['default']['hostname'], $db['default']['username'], $db['default']['password'], $db['default']['database']);

if (!$conn) {
    die("Connexion échouée: " . mysqli_connect_error());
}

echo "<!DOCTYPE html>";
echo "<html><head><title>Debug Menu Recettes</title>";
echo "<style>body{font-family:Arial;padding:20px;} .success{color:green;} .error{color:red;} .info{color:blue;} pre{background:#f5f5f5;padding:10px;border-radius:5px;}</style>";
echo "</head><body>";
echo "<h1>🔍 Débogage Menu Recettes</h1>";

// 1. Vérifier l'existence de la table
$table_name = $db['default']['dbprefix'] . 'dietic_recipes';
$check_table = mysqli_query($conn, "SHOW TABLES LIKE '$table_name'");
$table_exists = mysqli_num_rows($check_table) > 0;

echo "<h2>1. Vérification de la table</h2>";
if ($table_exists) {
    echo "<p class='success'>✅ Table '$table_name' existe</p>";

    // Compter les recettes
    $count_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM `$table_name`");
    $count = mysqli_fetch_assoc($count_query)['total'];
    echo "<p class='info'>📊 Nombre de recettes: <strong>$count</strong></p>";
} else {
    echo "<p class='error'>❌ Table '$table_name' n'existe PAS</p>";
    echo "<p class='info'>💡 Solution: Exécutez la migration pour créer les tables de recettes</p>";
}

// 2. Vérifier le fichier dietetic.php
echo "<h2>2. Vérification du code dans dietetic.php</h2>";
$dietetic_file = __DIR__ . '/dietetic.php';
$content = file_get_contents($dietetic_file);

if (strpos($content, 'Bibliothèque de Recettes') !== false) {
    echo "<p class='success'>✅ Code du menu trouvé dans dietetic.php</p>";

    // Extraire le code du menu
    preg_match('/\/\/ Recipes Library.*?(\})/s', $content, $matches);
    if (!empty($matches[0])) {
        echo "<p class='info'>📝 Code du menu:</p>";
        echo "<pre>" . htmlspecialchars($matches[0]) . "</pre>";
    }
} else {
    echo "<p class='error'>❌ Code du menu NON trouvé dans dietetic.php</p>";
    echo "<p class='info'>💡 Solution: Le code du menu doit être ajouté au fichier dietetic.php</p>";
}

// 3. Vérifier le contrôleur Recipes.php
echo "<h2>3. Vérification du contrôleur</h2>";
$controller_file = __DIR__ . '/controllers/Recipes.php';
if (file_exists($controller_file)) {
    echo "<p class='success'>✅ Contrôleur 'Recipes.php' existe</p>";
    echo "<p class='info'>📂 Chemin: modules/dietetic/controllers/Recipes.php</p>";
} else {
    echo "<p class='error'>❌ Contrôleur 'Recipes.php' n'existe PAS</p>";
}

// 4. Vérifier les vues admin
echo "<h2>4. Vérification des vues admin</h2>";
$views = ['list.php', 'form.php', 'view.php'];
$views_path = __DIR__ . '/views/admin/recipes/';
$all_views_exist = true;

foreach ($views as $view) {
    $view_file = $views_path . $view;
    if (file_exists($view_file)) {
        echo "<p class='success'>✅ Vue '$view' existe</p>";
    } else {
        echo "<p class='error'>❌ Vue '$view' n'existe PAS</p>";
        $all_views_exist = false;
    }
}

// 5. Vérifier les vues portal
echo "<h2>5. Vérification des vues portail patient</h2>";
$portal_views = ['list.php', 'view.php', 'favorites.php'];
$portal_views_path = __DIR__ . '/views/portal/recipes/';

foreach ($portal_views as $view) {
    $view_file = $portal_views_path . $view;
    if (file_exists($view_file)) {
        echo "<p class='success'>✅ Vue portail '$view' existe</p>";
    } else {
        echo "<p class='error'>❌ Vue portail '$view' n'existe PAS</p>";
    }
}

// 6. Vérifier le modèle
echo "<h2>6. Vérification du modèle</h2>";
$model_file = __DIR__ . '/models/Dietetic_recipes_model.php';
if (file_exists($model_file)) {
    echo "<p class='success'>✅ Modèle 'Dietetic_recipes_model.php' existe</p>";

    // Vérifier les méthodes importantes
    $model_content = file_get_contents($model_file);
    $required_methods = ['get_approved', 'get_patient_recipes', 'get_favorites', 'get_all_tags'];

    foreach ($required_methods as $method) {
        if (strpos($model_content, "function $method") !== false) {
            echo "<p class='success'>  ✅ Méthode '$method()' présente</p>";
        } else {
            echo "<p class='error'>  ❌ Méthode '$method()' manquante</p>";
        }
    }
} else {
    echo "<p class='error'>❌ Modèle 'Dietetic_recipes_model.php' n'existe PAS</p>";
}

// 7. Instructions pour rafraîchir le cache
echo "<h2>7. 🔧 Solutions pour afficher le menu admin</h2>";
echo "<div style='background:#fffbcc;padding:15px;border-left:4px solid #ffeb3b;margin:10px 0;'>";
echo "<h3>Si la table existe mais le menu ne s'affiche pas:</h3>";
echo "<ol>";
echo "<li><strong>Videz le cache Perfex:</strong><br>";
echo "   - Allez dans Setup → Settings → General<br>";
echo "   - Cliquez sur 'Clear All Cache' ou videz le dossier <code>application/cache/</code></li>";
echo "<li><strong>Déconnectez-vous et reconnectez-vous</strong> à l'interface admin</li>";
echo "<li><strong>Vérifiez vos permissions:</strong><br>";
echo "   - Le module Dietetic doit être activé<br>";
echo "   - Votre compte doit avoir les permissions 'View' sur le module</li>";
echo "<li><strong>Rechargez la page avec Ctrl+F5</strong> pour forcer le rafraîchissement</li>";
echo "</ol>";
echo "</div>";

echo "<h2>8. ✅ Résumé</h2>";
echo "<ul>";
echo "<li>Table recettes: " . ($table_exists ? "<span class='success'>✅ OK</span>" : "<span class='error'>❌ NON</span>") . "</li>";
echo "<li>Code menu dietetic.php: " . (strpos($content, 'Bibliothèque de Recettes') !== false ? "<span class='success'>✅ OK</span>" : "<span class='error'>❌ NON</span>") . "</li>";
echo "<li>Contrôleur: " . (file_exists($controller_file) ? "<span class='success'>✅ OK</span>" : "<span class='error'>❌ NON</span>") . "</li>";
echo "<li>Vues admin: " . ($all_views_exist ? "<span class='success'>✅ OK</span>" : "<span class='error'>❌ NON</span>") . "</li>";
echo "<li>Modèle: " . (file_exists($model_file) ? "<span class='success'>✅ OK</span>" : "<span class='error'>❌ NON</span>") . "</li>";
echo "</ul>";

echo "<hr><p style='color:#888;font-size:12px;'>Script de débogage généré automatiquement</p>";
echo "</body></html>";

mysqli_close($conn);
