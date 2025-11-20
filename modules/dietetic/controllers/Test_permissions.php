<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Test_permissions extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        echo "<!DOCTYPE html><html><head><title>Test Permissions</title>";
        echo "<style>body{font-family:Arial;padding:20px;background:#f5f5f5;} .success{color:#27ae60;font-weight:bold;} .error{color:#e74c3c;font-weight:bold;} .info{color:#3498db;} pre{background:#f8f9fa;padding:15px;border-left:4px solid #3498db;overflow-x:auto;} .section{background:white;padding:20px;margin:20px 0;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,0.1);} h2{color:#2c3e50;border-bottom:2px solid #e74c3c;padding-bottom:10px;}</style>";
        echo "</head><body>";

        echo "<h1>🔍 Test Permissions & Module</h1>";

        // Test 1: Informations utilisateur
        echo "<div class='section'>";
        echo "<h2>1. Informations utilisateur</h2>";
        echo "<p><strong>User ID:</strong> " . get_staff_user_id() . "</p>";
        echo "<p><strong>Est Admin:</strong> " . (is_admin() ? '<span class="success">OUI ✅</span>' : '<span class="error">NON ❌</span>') . "</p>";
        echo "<p><strong>Staff logged in:</strong> " . (is_staff_logged_in() ? '<span class="success">OUI ✅</span>' : '<span class="error">NON ❌</span>') . "</p>";

        // Get staff info
        $this->load->model('staff_model');
        $staff = $this->staff_model->get(get_staff_user_id());
        if ($staff) {
            echo "<p><strong>Nom:</strong> " . $staff->firstname . " " . $staff->lastname . "</p>";
            echo "<p><strong>Email:</strong> " . $staff->email . "</p>";
            echo "<p><strong>Role:</strong> " . ($staff->admin == 1 ? 'Administrator' : 'Staff') . "</p>";
        }
        echo "</div>";

        // Test 2: Permissions Dietetic
        echo "<div class='section'>";
        echo "<h2>2. Permissions Module Dietetic</h2>";

        $permissions = ['view', 'create', 'edit', 'delete', 'settings'];

        foreach ($permissions as $perm) {
            $has_perm = has_permission('dietetic', '', $perm);
            if ($has_perm) {
                echo "<p class='success'>✅ Permission '$perm': OUI</p>";
            } else {
                echo "<p class='error'>❌ Permission '$perm': NON</p>";
            }
        }

        // Vérifier si au moins view permission
        $has_view = has_permission('dietetic', '', 'view') || is_admin();
        echo "<hr>";
        echo "<p><strong>Accès au module (view OU admin):</strong> ";
        if ($has_view) {
            echo '<span class="success">OUI ✅</span>';
        } else {
            echo '<span class="error">NON ❌ - C\'EST LE PROBLÈME!</span>';
        }
        echo "</p>";
        echo "</div>";

        // Test 3: Vérifier les permissions dans la base
        echo "<div class='section'>";
        echo "<h2>3. Permissions dans la base de données</h2>";

        $staff_id = get_staff_user_id();
        $query = $this->db->query("SELECT * FROM " . db_prefix() . "staff_permissions WHERE staff_id = ?", [$staff_id]);
        $perms_db = $query->result_array();

        if (empty($perms_db)) {
            echo "<p class='info'>Aucune permission trouvée en base (normal si admin)</p>";
        } else {
            echo "<p class='info'>Permissions trouvées:</p>";
            echo "<pre>";
            foreach ($perms_db as $perm) {
                echo "Feature: {$perm['feature']}, Capability: {$perm['capability']}\n";
            }
            echo "</pre>";

            // Chercher spécifiquement dietetic
            $dietetic_perms = array_filter($perms_db, function($p) {
                return $p['feature'] === 'dietetic';
            });

            if (empty($dietetic_perms)) {
                echo "<p class='error'>❌ Aucune permission 'dietetic' trouvée</p>";
            } else {
                echo "<p class='success'>✅ Permissions 'dietetic' trouvées</p>";
            }
        }
        echo "</div>";

        // Test 4: Modules activés
        echo "<div class='section'>";
        echo "<h2>4. Modules activés</h2>";

        $this->load->model('modules_model');
        $modules = $this->modules_model->get();

        $dietetic_module = null;
        foreach ($modules as $module) {
            if ($module['system_name'] === 'dietetic') {
                $dietetic_module = $module;
                break;
            }
        }

        if ($dietetic_module) {
            echo "<p class='success'>✅ Module Dietetic trouvé</p>";
            echo "<p><strong>Active:</strong> " . ($dietetic_module['active'] == 1 ? '<span class="success">OUI ✅</span>' : '<span class="error">NON ❌ - ACTIVEZ-LE!</span>') . "</p>";
            echo "<p><strong>System Name:</strong> " . $dietetic_module['system_name'] . "</p>";
            echo "<pre>" . print_r($dietetic_module, true) . "</pre>";
        } else {
            echo "<p class='error'>❌ Module Dietetic NON trouvé dans la liste des modules</p>";
        }
        echo "</div>";

        // Test 5: Vérifier le hook
        echo "<div class='section'>";
        echo "<h2>5. Test du hook admin_init</h2>";

        echo "<p class='info'>Le menu devrait être ajouté via le hook 'admin_init'</p>";

        // Vérifier si la fonction existe
        if (function_exists('dietetic_module_init_menu_items')) {
            echo "<p class='success'>✅ Fonction 'dietetic_module_init_menu_items' existe</p>";

            // Appeler manuellement la fonction
            try {
                ob_start();
                dietetic_module_init_menu_items();
                $output = ob_get_clean();

                echo "<p class='success'>✅ Fonction exécutée sans erreur</p>";

                // Maintenant vérifier le menu
                $menu = $this->app_menu->get_menu();

                if (isset($menu['dietetic'])) {
                    echo "<p class='success'>✅ Menu 'dietetic' MAINTENANT trouvé après appel manuel!</p>";
                    echo "<p class='info'>Sous-menus:</p>";
                    echo "<ul>";
                    if (isset($menu['dietetic']['children'])) {
                        foreach ($menu['dietetic']['children'] as $child) {
                            echo "<li>" . $child['name'] . " (" . $child['slug'] . ")</li>";
                        }
                    }
                    echo "</ul>";
                } else {
                    echo "<p class='error'>❌ Menu 'dietetic' toujours pas trouvé même après appel manuel</p>";
                    echo "<p class='error'>Cela signifie que la condition dans la fonction retourne FALSE</p>";
                }

            } catch (Exception $e) {
                echo "<p class='error'>❌ Erreur lors de l'exécution: " . $e->getMessage() . "</p>";
            }
        } else {
            echo "<p class='error'>❌ Fonction 'dietetic_module_init_menu_items' n'existe PAS</p>";
        }
        echo "</div>";

        // Test 6: Solution
        echo "<div class='section' style='background:#fff3cd;'>";
        echo "<h2>6. 🔧 Solution</h2>";

        if (!$has_view) {
            echo "<h3 style='color:#856404;'>Problème: Pas de permission VIEW</h3>";
            echo "<p><strong>Solution:</strong></p>";
            echo "<ol>";
            echo "<li>Allez dans <strong>Setup → Staff</strong></li>";
            echo "<li>Cliquez sur votre compte</li>";
            echo "<li>Onglet <strong>Permissions</strong></li>";
            echo "<li>Cherchez le module <strong>Dietetic</strong></li>";
            echo "<li>Cochez au moins <strong>View</strong></li>";
            echo "<li>Sauvegardez</li>";
            echo "<li>Déconnectez-vous et reconnectez-vous</li>";
            echo "</ol>";
        }

        if ($dietetic_module && $dietetic_module['active'] != 1) {
            echo "<h3 style='color:#856404;'>Problème: Module non activé</h3>";
            echo "<p><strong>Solution:</strong></p>";
            echo "<ol>";
            echo "<li>Allez dans <strong>Setup → Modules</strong></li>";
            echo "<li>Cherchez le module <strong>Dietetic</strong></li>";
            echo "<li>Cliquez sur <strong>Activate</strong></li>";
            echo "<li>Rechargez la page</li>";
            echo "</ol>";
        }

        echo "</div>";

        echo "</body></html>";
    }
}
