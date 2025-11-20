<?php
/**
 * Script de débogage temporaire pour le menu Recettes
 * À placer dans modules/dietetic/test_menu.php
 * Accès: https://app.dietsenegal.net/admin/dietetic/test_menu
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Test_menu extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if (!is_admin()) {
            access_denied('Test Menu');
        }

        echo "<!DOCTYPE html><html><head><title>Test Menu Recettes</title>";
        echo "<style>body{font-family:Arial;padding:20px;background:#f5f5f5;} .success{color:#27ae60;font-weight:bold;} .error{color:#e74c3c;font-weight:bold;} .info{color:#3498db;} pre{background:#f8f9fa;padding:15px;border-left:4px solid #3498db;overflow-x:auto;} .section{background:white;padding:20px;margin:20px 0;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,0.1);} h2{color:#2c3e50;border-bottom:2px solid #e74c3c;padding-bottom:10px;}</style>";
        echo "</head><body>";

        echo "<h1>🔍 Test du Menu Recettes</h1>";

        // Test 1: Vérifier l'existence de la table
        echo "<div class='section'>";
        echo "<h2>1. Vérification de la table</h2>";
        $table_name = db_prefix() . 'dietic_recipes';
        $table_exists = $this->db->table_exists($table_name);

        if ($table_exists) {
            echo "<p class='success'>✅ Table '$table_name' existe</p>";

            // Compter les recettes
            $count = $this->db->count_all($table_name);
            echo "<p class='info'>📊 Nombre de recettes: <strong>$count</strong></p>";
        } else {
            echo "<p class='error'>❌ Table '$table_name' n'existe PAS</p>";
            echo "<p class='error'>🚨 C'EST LE PROBLÈME! La table n'existe pas.</p>";
            echo "<p>Solution: Créez la table via la migration</p>";
        }
        echo "</div>";

        // Test 2: Vérifier les autres tables de recettes
        echo "<div class='section'>";
        echo "<h2>2. Vérification des tables liées</h2>";
        $related_tables = [
            'dietic_recipe_ingredients',
            'dietic_recipe_instructions',
            'dietic_recipe_nutrition',
            'dietic_recipe_photos',
            'dietic_recipe_tags',
            'dietic_recipe_assignments',
            'dietic_recipe_ratings',
            'dietic_recipe_favorites'
        ];

        foreach ($related_tables as $table) {
            $full_name = db_prefix() . $table;
            $exists = $this->db->table_exists($full_name);
            if ($exists) {
                echo "<p class='success'>✅ $full_name</p>";
            } else {
                echo "<p class='error'>❌ $full_name</p>";
            }
        }
        echo "</div>";

        // Test 3: Lister toutes les tables qui commencent par dietic
        echo "<div class='section'>";
        echo "<h2>3. Toutes les tables Dietetic</h2>";
        $query = $this->db->query("SHOW TABLES LIKE '" . db_prefix() . "dietic%'");
        $tables = $query->result_array();

        if (empty($tables)) {
            echo "<p class='error'>❌ Aucune table trouvée</p>";
        } else {
            echo "<p class='info'>Tables trouvées:</p>";
            echo "<ul>";
            foreach ($tables as $table) {
                $table_name = array_values($table)[0];
                echo "<li><code>$table_name</code></li>";
            }
            echo "</ul>";
        }
        echo "</div>";

        // Test 4: Vérifier le menu actuel
        echo "<div class='section'>";
        echo "<h2>4. Menu Dietetic actuel</h2>";

        // Récupérer le menu
        $menu = $this->app_menu->get_menu();

        echo "<p class='info'>Recherche du menu 'dietetic'...</p>";

        $dietetic_menu_found = false;
        $recipes_menu_found = false;

        if (isset($menu['dietetic'])) {
            $dietetic_menu_found = true;
            echo "<p class='success'>✅ Menu principal 'Dietetic' trouvé</p>";

            if (isset($menu['dietetic']['children'])) {
                echo "<p class='info'>Sous-menus trouvés (" . count($menu['dietetic']['children']) . "):</p>";
                echo "<ul>";
                foreach ($menu['dietetic']['children'] as $child) {
                    echo "<li>";
                    echo "<strong>" . $child['name'] . "</strong> ";
                    echo "(slug: " . $child['slug'] . ", position: " . (isset($child['position']) ? $child['position'] : 'N/A') . ")";

                    if ($child['slug'] === 'dietetic-recipes') {
                        echo " <span class='success'>← TROUVÉ!</span>";
                        $recipes_menu_found = true;
                    }

                    echo "</li>";
                }
                echo "</ul>";

                if ($recipes_menu_found) {
                    echo "<p class='success'>✅ Le menu 'Bibliothèque de Recettes' est enregistré dans le système!</p>";
                    echo "<p class='error'>🤔 Mais pourquoi ne s'affiche-t-il pas?</p>";
                    echo "<p class='info'>Possibilités:</p>";
                    echo "<ul>";
                    echo "<li>Cache du navigateur (Ctrl+Shift+Delete)</li>";
                    echo "<li>Cache Perfex (Setup → Settings → Clear All Cache)</li>";
                    echo "<li>Problème de permissions</li>";
                    echo "<li>JavaScript ou CSS cache le menu</li>";
                    echo "</ul>";
                } else {
                    echo "<p class='error'>❌ Le menu 'Bibliothèque de Recettes' n'est PAS dans la liste</p>";
                    echo "<p class='error'>Raison: La table n'existe probablement pas</p>";
                }
            }
        } else {
            echo "<p class='error'>❌ Menu principal 'Dietetic' NON trouvé</p>";
        }
        echo "</div>";

        // Test 5: Forcer l'ajout du menu
        echo "<div class='section'>";
        echo "<h2>5. Test: Forcer l'ajout du menu</h2>";

        try {
            // Tenter d'ajouter le menu manuellement
            $this->app_menu->add_sidebar_children_item('dietetic', [
                'slug'     => 'dietetic-recipes-test',
                'name'     => '🧪 RECETTES TEST',
                'icon'     => 'fa fa-flask',
                'href'     => admin_url('dietetic/recipes'),
                'position' => 7.6,
            ]);

            echo "<p class='success'>✅ Menu test ajouté avec succès</p>";
            echo "<p class='info'>Vérifiez maintenant si vous voyez '🧪 RECETTES TEST' dans le menu latéral</p>";
            echo "<p class='info'>Si vous le voyez, c'est que le problème vient de la condition table_exists()</p>";
        } catch (Exception $e) {
            echo "<p class='error'>❌ Erreur lors de l'ajout: " . $e->getMessage() . "</p>";
        }
        echo "</div>";

        // Test 6: Solution
        echo "<div class='section' style='background:#fff3cd;'>";
        echo "<h2>6. 🔧 Solution</h2>";

        if (!$table_exists) {
            echo "<p><strong>La table n'existe pas!</strong></p>";
            echo "<p>Vous devez créer la table via la migration:</p>";
            echo "<ol>";
            echo "<li>Allez sur: <a href='" . admin_url('dietetic/notifications/migrations') . "' target='_blank'>Page des migrations</a></li>";
            echo "<li>Cherchez la section <strong>Recipe Library Tables</strong></li>";
            echo "<li>Cliquez sur le bouton <strong>Install Recipe Library</strong></li>";
            echo "<li>Attendez la confirmation</li>";
            echo "<li>Rafraîchissez cette page pour vérifier</li>";
            echo "</ol>";
        } else {
            echo "<p><strong>La table existe mais le menu ne s'affiche pas</strong></p>";
            echo "<p>Solutions:</p>";
            echo "<ol>";
            echo "<li><strong>Videz le cache:</strong> Setup → Settings → General → Clear All Cache</li>";
            echo "<li><strong>Déconnectez-vous complètement</strong> puis reconnectez-vous</li>";
            echo "<li><strong>Videz le cache navigateur:</strong> Ctrl+Shift+Delete</li>";
            echo "<li><strong>Testez en navigation privée</strong></li>";
            echo "</ol>";
        }
        echo "</div>";

        echo "</body></html>";
    }
}
