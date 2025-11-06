<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Diagnostic Web Tool for view_meal_plan 404 Error
 *
 * Instructions:
 * 1. Place this file in: modules/dietetic/diagnostic_web.php
 * 2. Access via: https://app.dietsenegal.net/modules/dietetic/diagnostic_web.php
 * 3. Check the results to identify the issue
 */

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Diagnostic Diététique</title>";
echo "<style>body{font-family:monospace;padding:20px;background:#f5f5f5}";
echo ".ok{color:green;font-weight:bold}.error{color:red;font-weight:bold}";
echo ".warning{color:orange;font-weight:bold}h2{border-bottom:2px solid #333;padding-bottom:5px}";
echo "pre{background:#fff;padding:10px;border:1px solid #ddd;overflow-x:auto}</style></head><body>";

echo "<h1>🔍 Diagnostic Module Diététique - Erreur 404 View Meal Plan</h1>";
echo "<p><strong>Date:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<hr>";

// Test 1: Check if controller file exists
echo "<h2>1. Vérification Fichier Contrôleur Portal</h2>";
$portal_file = __DIR__ . '/controllers/Portal.php';
if (file_exists($portal_file)) {
    echo "<p class='ok'>✓ Fichier Portal.php existe</p>";
    echo "<p>Chemin: <code>$portal_file</code></p>";

    // Check file permissions
    $perms = fileperms($portal_file);
    $perms_str = substr(sprintf('%o', $perms), -4);
    echo "<p>Permissions: <code>$perms_str</code></p>";

    // Check file size
    $size = filesize($portal_file);
    echo "<p>Taille: <code>" . number_format($size) . " octets</code></p>";
} else {
    echo "<p class='error'>✗ Fichier Portal.php INTROUVABLE</p>";
}

// Test 2: Check if method exists in file
echo "<h2>2. Vérification Méthode view_meal_plan</h2>";
if (file_exists($portal_file)) {
    $content = file_get_contents($portal_file);

    if (strpos($content, 'function view_meal_plan') !== false) {
        echo "<p class='ok'>✓ Méthode view_meal_plan() trouvée dans le fichier</p>";

        // Check for both methods
        if (strpos($content, 'function viewmealplan') !== false) {
            echo "<p class='ok'>✓ Méthode alternative viewmealplan() trouvée</p>";
        } else {
            echo "<p class='warning'>⚠ Méthode alternative viewmealplan() NON trouvée</p>";
        }

        // Extract method signature
        preg_match('/public function view_meal_plan\((.*?)\)/s', $content, $matches);
        if (!empty($matches[0])) {
            echo "<p>Signature: <code>" . htmlspecialchars($matches[0]) . "</code></p>";
        }
    } else {
        echo "<p class='error'>✗ Méthode view_meal_plan() NON trouvée dans le fichier</p>";
    }
}

// Test 3: Check class structure
echo "<h2>3. Vérification Structure de la Classe</h2>";
if (file_exists($portal_file)) {
    $content = file_get_contents($portal_file);

    // Check class declaration
    if (preg_match('/class Portal extends (\w+)/i', $content, $matches)) {
        echo "<p class='ok'>✓ Classe Portal déclarée</p>";
        echo "<p>Étend: <code>{$matches[1]}</code></p>";
    } else {
        echo "<p class='error'>✗ Déclaration de classe Portal NON trouvée</p>";
    }

    // List all public methods
    preg_match_all('/public function (\w+)\(/i', $content, $methods);
    if (!empty($methods[1])) {
        echo "<p class='ok'>✓ Méthodes publiques trouvées (" . count($methods[1]) . ")</p>";
        echo "<pre>";
        foreach ($methods[1] as $method) {
            echo "- $method()\n";
        }
        echo "</pre>";
    }
}

// Test 4: Check if dietetic module is in correct location
echo "<h2>4. Vérification Emplacement Module</h2>";
$module_path = __DIR__;
echo "<p>Chemin actuel: <code>$module_path</code></p>";

if (strpos($module_path, 'modules/dietetic') !== false) {
    echo "<p class='ok'>✓ Module dans le bon répertoire (modules/dietetic)</p>";
} else {
    echo "<p class='error'>✗ Module PAS dans modules/dietetic</p>";
}

// Test 5: Check views directory
echo "<h2>5. Vérification Fichier Vue</h2>";
$view_file = __DIR__ . '/views/portal_meal_plan_view.php';
if (file_exists($view_file)) {
    echo "<p class='ok'>✓ Vue portal_meal_plan_view.php existe</p>";
    $view_size = filesize($view_file);
    echo "<p>Taille: <code>" . number_format($view_size) . " octets</code></p>";
} else {
    echo "<p class='error'>✗ Vue portal_meal_plan_view.php INTROUVABLE</p>";
}

// Test 6: Check database tables
echo "<h2>6. Vérification Tables Base de Données</h2>";
try {
    // Try to connect to database
    $db_config_file = dirname(dirname(dirname(__DIR__))) . '/application/config/database.php';

    if (file_exists($db_config_file)) {
        echo "<p class='ok'>✓ Fichier config database.php trouvé</p>";

        // Include config
        include($db_config_file);

        if (isset($db['default'])) {
            $dbconfig = $db['default'];
            $conn = @mysqli_connect(
                $dbconfig['hostname'],
                $dbconfig['username'],
                $dbconfig['password'],
                $dbconfig['database']
            );

            if ($conn) {
                echo "<p class='ok'>✓ Connexion base de données réussie</p>";

                // Check tables
                $prefix = $dbconfig['dbprefix'];
                $tables_to_check = [
                    'dietic_meal_plans',
                    'dietic_programs',
                    'dietic_patients'
                ];

                foreach ($tables_to_check as $table) {
                    $full_table = $prefix . $table;
                    $result = mysqli_query($conn, "SHOW TABLES LIKE '$full_table'");
                    if ($result && mysqli_num_rows($result) > 0) {
                        echo "<p class='ok'>✓ Table $full_table existe</p>";

                        // Count rows
                        $count_result = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM $full_table");
                        $count_row = mysqli_fetch_assoc($count_result);
                        echo "<p>&nbsp;&nbsp;&nbsp;→ {$count_row['cnt']} enregistrements</p>";
                    } else {
                        echo "<p class='error'>✗ Table $full_table INTROUVABLE</p>";
                    }
                }

                mysqli_close($conn);
            } else {
                echo "<p class='error'>✗ Échec connexion base de données: " . mysqli_connect_error() . "</p>";
            }
        }
    } else {
        echo "<p class='warning'>⚠ Fichier database.php non trouvé (normal si hors Perfex)</p>";
    }
} catch (Exception $e) {
    echo "<p class='warning'>⚠ Erreur lors de la vérification DB: " . $e->getMessage() . "</p>";
}

// Test 7: URL Testing
echo "<h2>7. URLs à Tester</h2>";
echo "<p>Testez ces URLs dans le navigateur (connecté en tant que patient):</p>";
echo "<ol>";
echo "<li><strong>URL originale:</strong><br>";
echo "<code>https://app.dietsenegal.net/dietetic/portal/view_meal_plan/2</code></li>";
echo "<li><strong>URL alternative (sans underscore):</strong><br>";
echo "<code>https://app.dietsenegal.net/dietetic/portal/viewmealplan/2</code></li>";
echo "<li><strong>URL liste des plans:</strong><br>";
echo "<code>https://app.dietsenegal.net/dietetic/portal/meal_plans</code></li>";
echo "</ol>";

// Test 8: Recommendations
echo "<h2>8. Prochaines Étapes</h2>";
echo "<p><strong>Si tout est ✓ ci-dessus mais 404 persiste:</strong></p>";
echo "<ul>";
echo "<li>1. Vérifier les logs Perfex dans <code>Setup → Activity Log</code> pour les entrées <code>[DIETETIC DEBUG]</code></li>";
echo "<li>2. Vérifier les logs d'erreur PHP du serveur</li>";
echo "<li>3. Essayer l'URL alternative sans underscore</li>";
echo "<li>4. Vérifier que le module est bien activé dans Perfex</li>";
echo "<li>5. Vider le cache du navigateur</li>";
echo "</ul>";

echo "<p><strong>Si des ✗ apparaissent ci-dessus:</strong></p>";
echo "<ul>";
echo "<li>Corriger les fichiers manquants ou mal placés</li>";
echo "<li>Vérifier les permissions de fichiers (644 pour .php)</li>";
echo "<li>Re-déployer le code depuis git</li>";
echo "</ul>";

echo "<hr>";
echo "<p><em>Diagnostic généré le " . date('Y-m-d H:i:s') . "</em></p>";
echo "</body></html>";
