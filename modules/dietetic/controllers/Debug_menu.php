<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Debug_menu extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if (!is_admin()) {
            access_denied('Debug');
        }

        echo "<!DOCTYPE html><html><head><title>Debug Menu Simple</title>";
        echo "<style>body{font-family:monospace;padding:20px;background:#1e1e1e;color:#d4d4d4;} .success{color:#4ec9b0;} .error{color:#f48771;} .info{color:#569cd6;} pre{background:#252526;padding:15px;border-left:4px solid #007acc;overflow-x:auto;color:#d4d4d4;} .section{background:#252526;padding:20px;margin:20px 0;border-radius:8px;border:1px solid #3e3e42;} h2{color:#4ec9b0;border-bottom:2px solid #007acc;padding-bottom:10px;}</style>";
        echo "</head><body>";

        echo "<h1>🐛 Debug Menu - Analyse Complète</h1>";

        // ÉTAPE 1: Vérifier la condition du menu
        echo "<div class='section'>";
        echo "<h2>ÉTAPE 1: Vérification de la condition</h2>";

        $CI = &get_instance();
        $has_view = has_permission('dietetic', '', 'view') || is_admin();

        echo "<p><strong>has_permission('dietetic', '', 'view'):</strong> " . (has_permission('dietetic', '', 'view') ? '<span class="success">TRUE</span>' : '<span class="error">FALSE</span>') . "</p>";
        echo "<p><strong>is_admin():</strong> " . (is_admin() ? '<span class="success">TRUE</span>' : '<span class="error">FALSE</span>') . "</p>";
        echo "<p><strong>Résultat (view OU admin):</strong> " . ($has_view ? '<span class="success">TRUE</span>' : '<span class="error">FALSE</span>') . "</p>";

        if (!$has_view) {
            echo "<p class='error'>❌ LA CONDITION EST FALSE - Le menu ne sera JAMAIS ajouté!</p>";
        } else {
            echo "<p class='success'>✅ La condition est TRUE - Le menu DEVRAIT être ajouté</p>";
        }
        echo "</div>";

        // ÉTAPE 2: Tester l'ajout manuel du menu
        echo "<div class='section'>";
        echo "<h2>ÉTAPE 2: Test d'ajout manuel du menu</h2>";

        try {
            // Charger le helper
            $CI->load->helper('dietetic/dietetic');
            echo "<p class='success'>✅ Helper chargé</p>";

            // Tester si la fonction existe
            if (!function_exists('dietetic_module_init_menu_items')) {
                echo "<p class='error'>❌ Fonction dietetic_module_init_menu_items n'existe PAS</p>";
            } else {
                echo "<p class='success'>✅ Fonction dietetic_module_init_menu_items existe</p>";

                // Appeler la fonction
                echo "<p class='info'>Appel de la fonction...</p>";
                dietetic_module_init_menu_items();
                echo "<p class='success'>✅ Fonction exécutée sans erreur</p>";
            }
        } catch (Exception $e) {
            echo "<p class='error'>❌ ERREUR: " . $e->getMessage() . "</p>";
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
        }
        echo "</div>";

        // ÉTAPE 3: Vérifier le menu après appel
        echo "<div class='section'>";
        echo "<h2>ÉTAPE 3: Contenu du menu</h2>";

        try {
            echo "<p class='info'>Récupération du menu...</p>";

            if (!isset($CI->app_menu)) {
                echo "<p class='error'>❌ app_menu n'existe pas!</p>";
            } else {
                echo "<p class='success'>✅ app_menu existe</p>";
            }

            $menu = $CI->app_menu->get_menu();

            echo "<p class='success'>✅ get_menu() exécuté sans erreur</p>";

            if (!is_array($menu)) {
                echo "<p class='error'>❌ Menu n'est pas un array! Type: " . gettype($menu) . "</p>";
                echo "<pre>" . print_r($menu, true) . "</pre>";
            } else {
                echo "<p><strong>Nombre total de menus:</strong> " . count($menu) . "</p>";
            }

        } catch (Exception $e) {
            echo "<p class='error'>❌ ERREUR lors de get_menu(): " . $e->getMessage() . "</p>";
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
        }

        if (isset($menu) && is_array($menu) && isset($menu['dietetic'])) {
            echo "<p class='success'>✅ Menu 'dietetic' TROUVÉ!</p>";
            echo "<pre>" . print_r($menu['dietetic'], true) . "</pre>";

            if (isset($menu['dietetic']['children'])) {
                echo "<p><strong>Sous-menus (" . count($menu['dietetic']['children']) . "):</strong></p>";
                foreach ($menu['dietetic']['children'] as $child) {
                    $is_recipes = ($child['slug'] === 'dietetic-recipes');
                    echo "<div style='padding:10px;background:" . ($is_recipes ? '#1e3a1e' : '#252526') . ";margin:5px 0;border-left:3px solid " . ($is_recipes ? '#4ec9b0' : '#3e3e42') . ";'>";
                    echo "<strong>" . $child['name'] . "</strong><br>";
                    echo "Slug: " . $child['slug'] . "<br>";
                    echo "Position: " . (isset($child['position']) ? $child['position'] : 'N/A');
                    if ($is_recipes) {
                        echo " <span class='success'>← RECETTES!</span>";
                    }
                    echo "</div>";
                }
            }
        } elseif (isset($menu) && is_array($menu)) {
            echo "<p class='error'>❌ Menu 'dietetic' NON trouvé</p>";
            echo "<p class='info'>Menus disponibles (" . count($menu) . "):</p>";
            echo "<pre>" . print_r(array_keys($menu), true) . "</pre>";

            echo "<p class='info'>Détails complets du premier menu (pour debug):</p>";
            if (count($menu) > 0) {
                $first_menu = reset($menu);
                echo "<pre>" . print_r($first_menu, true) . "</pre>";
            }
        } else {
            echo "<p class='error'>❌ Variable menu invalide ou vide</p>";
        }
        echo "</div>";

        // ÉTAPE 4: Vérifier les tables
        echo "<div class='section'>";
        echo "<h2>ÉTAPE 4: Vérification des tables recettes</h2>";

        $table_exists = $CI->db->table_exists(db_prefix() . 'dietic_recipes');
        echo "<p><strong>table_exists('dietic_recipes'):</strong> " . ($table_exists ? '<span class="success">TRUE</span>' : '<span class="error">FALSE</span>') . "</p>";

        if ($table_exists) {
            $count = $CI->db->count_all(db_prefix() . 'dietic_recipes');
            echo "<p><strong>Nombre de recettes:</strong> " . $count . "</p>";
        }
        echo "</div>";

        // ÉTAPE 5: Forcer l'ajout du menu Recettes
        echo "<div class='section'>";
        echo "<h2>ÉTAPE 5: Forcer l'ajout du menu Recettes</h2>";

        try {
            $CI->app_menu->add_sidebar_children_item('dietetic', [
                'slug'     => 'dietetic-recipes-force',
                'name'     => '🔥 RECETTES FORCÉ',
                'icon'     => 'fa fa-fire',
                'href'     => admin_url('dietetic/recipes'),
                'position' => 7.6,
            ]);

            echo "<p class='success'>✅ Menu 'RECETTES FORCÉ' ajouté avec succès</p>";
            echo "<p class='info'>Regardez maintenant dans la sidebar si vous voyez '🔥 RECETTES FORCÉ'</p>";

        } catch (Exception $e) {
            echo "<p class='error'>❌ Erreur: " . $e->getMessage() . "</p>";
        }
        echo "</div>";

        // ÉTAPE 6: Solution
        echo "<div class='section' style='background:#3a2a1a;border-color:#f39c12;'>";
        echo "<h2>🔧 DIAGNOSTIC</h2>";

        if (!$has_view) {
            echo "<p class='error'>PROBLÈME: Pas de permissions</p>";
        } elseif (!isset($menu['dietetic'])) {
            echo "<p class='error'>PROBLÈME: Le menu Dietetic n'existe pas du tout</p>";
            echo "<p>Cela signifie que le hook admin_init ne s'exécute PAS ou que le fichier dietetic.php n'est pas chargé</p>";
        } elseif (!$table_exists) {
            echo "<p class='error'>PROBLÈME: La table dietic_recipes n'existe pas</p>";
        } else {
            $recipes_found = false;
            if (isset($menu['dietetic']['children'])) {
                foreach ($menu['dietetic']['children'] as $child) {
                    if ($child['slug'] === 'dietetic-recipes') {
                        $recipes_found = true;
                        break;
                    }
                }
            }

            if ($recipes_found) {
                echo "<p class='success'>✅ LE MENU RECETTES EST ENREGISTRÉ!</p>";
                echo "<p>Mais vous ne le voyez pas? Solutions:</p>";
                echo "<ul>";
                echo "<li>Vider TOUT le cache navigateur (Ctrl+Shift+Delete)</li>";
                echo "<li>Tester en navigation privée</li>";
                echo "<li>Vider le cache Perfex: Setup → Settings → Clear All Cache</li>";
                echo "<li>Problème CSS qui cache l'élément?</li>";
                echo "</ul>";
            } else {
                echo "<p class='error'>PROBLÈME: Le menu Recettes n'est PAS dans la liste des sous-menus</p>";
                echo "<p>Mais la table existe et les conditions sont bonnes...</p>";
                echo "<p>Cela signifie que le if() qui vérifie table_exists retourne FALSE au moment de l'exécution du hook</p>";
            }
        }
        echo "</div>";

        echo "</body></html>";
    }
}
