<?php
/**
 * Fichier de diagnostic pour le module Dietetic
 * Uploadez ce fichier dans modules/dietetic/
 * Puis accédez à : https://app.dietsenegal.net/modules/dietetic/diagnostic.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 Diagnostic Module Dietetic</h1>";
echo "<hr>";

// 1. Vérifier que PHP fonctionne
echo "<h2>✅ PHP fonctionne</h2>";
echo "<p>Version PHP : " . phpversion() . "</p>";
echo "<hr>";

// 2. Vérifier les fichiers critiques
echo "<h2>📁 Fichiers du Module</h2>";

$files_to_check = [
    'dietetic.php',
    'controllers/Portal.php',
    'models/Dietetic_patients_model.php',
    'models/Dietetic_meal_plans_model.php',
    'views/portal_meal_plans.php',
    'views/portal_meal_plan_view.php',
];

foreach ($files_to_check as $file) {
    if (file_exists(__DIR__ . '/' . $file)) {
        echo "✅ $file existe<br>";
    } else {
        echo "❌ $file MANQUANT<br>";
    }
}

echo "<hr>";

// 3. Vérifier la syntaxe PHP des fichiers critiques
echo "<h2>🔧 Vérification Syntaxe PHP</h2>";

$php_files = [
    'dietetic.php',
    'controllers/Portal.php',
];

foreach ($php_files as $file) {
    $filepath = __DIR__ . '/' . $file;
    if (file_exists($filepath)) {
        $output = [];
        $return_var = 0;
        exec("php -l " . escapeshellarg($filepath) . " 2>&1", $output, $return_var);

        if ($return_var === 0) {
            echo "✅ $file - Syntaxe OK<br>";
        } else {
            echo "❌ $file - ERREUR SYNTAXE:<br>";
            echo "<pre style='color:red;'>" . implode("\n", $output) . "</pre>";
        }
    }
}

echo "<hr>";

// 4. Vérifier les permissions
echo "<h2>🔐 Permissions</h2>";

$dirs = ['controllers', 'models', 'views'];
foreach ($dirs as $dir) {
    if (is_dir(__DIR__ . '/' . $dir)) {
        $perms = substr(sprintf('%o', fileperms(__DIR__ . '/' . $dir)), -4);
        echo "$dir : $perms<br>";
    }
}

echo "<hr>";

// 5. Tester le chargement du contrôleur Portal
echo "<h2>🎯 Test Chargement Portal.php</h2>";

try {
    $portal_file = __DIR__ . '/controllers/Portal.php';
    if (file_exists($portal_file)) {
        $content = file_get_contents($portal_file);

        // Chercher les méthodes importantes
        if (strpos($content, 'public function meal_plans()') !== false) {
            echo "✅ Méthode meal_plans() trouvée<br>";
        } else {
            echo "❌ Méthode meal_plans() MANQUANTE<br>";
        }

        if (strpos($content, 'Check if we\'re viewing a specific meal plan') !== false) {
            echo "✅ Code de redirection view trouvé (changements déployés)<br>";
        } else {
            echo "⚠️ Code de redirection NON trouvé (changements non déployés)<br>";
        }

        if (strpos($content, 'public function view_meal_plan') !== false) {
            echo "✅ Méthode view_meal_plan() trouvée<br>";
        } else {
            echo "❌ Méthode view_meal_plan() MANQUANTE<br>";
        }

        // Chercher les erreurs de syntaxe évidentes
        if (preg_match('/\?>\s*<\?php/', $content)) {
            echo "⚠️ Tags PHP multiples détectés<br>";
        }

    } else {
        echo "❌ Portal.php n'existe pas !<br>";
    }
} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "<br>";
}

echo "<hr>";

// 6. Test de connexion à la base de données (si possible)
echo "<h2>💾 Base de Données</h2>";

$config_file = '../../application/config/database.php';
if (file_exists($config_file)) {
    echo "✅ Fichier de config DB existe<br>";

    // Ne pas afficher les credentials par sécurité
    echo "<p>Pour vérifier la connexion DB, allez dans Perfex → Setup → System Info</p>";
} else {
    echo "⚠️ Impossible de trouver la config DB<br>";
}

echo "<hr>";

// 7. Informations serveur
echo "<h2>🖥️ Informations Serveur</h2>";
echo "Système : " . php_uname() . "<br>";
echo "Serveur Web : " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
echo "Document Root : " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Script Path : " . __FILE__ . "<br>";

echo "<hr>";
echo "<h2>✅ Diagnostic Terminé</h2>";
echo "<p>Envoyez une capture d'écran de cette page au développeur.</p>";
?>
