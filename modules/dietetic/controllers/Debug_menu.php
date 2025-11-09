<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Quick Menu Debug Controller
 * Access via: /admin/dietetic/debug_menu
 * DELETE THIS FILE after testing
 */
class Debug_menu extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('dietetic/dietetic');
    }

    public function index()
    {
        echo "<h1>🔍 Diagnostic du Menu Enquêtes Alimentaires</h1>";
        echo "<hr>";

        // 1. Current user info
        echo "<h2>1. Utilisateur Actuel</h2>";
        $current_staff_id = get_staff_user_id();
        echo "<p><strong>Staff ID actuel (get_staff_user_id()):</strong> {$current_staff_id}</p>";

        $dietetic_staff_id = dietetic_get_staff_user_id();
        echo "<p><strong>Staff ID (dietetic_get_staff_user_id()):</strong> {$dietetic_staff_id}</p>";

        $is_admin = is_admin();
        echo "<p><strong>is_admin():</strong> " . ($is_admin ? '✅ TRUE' : '❌ FALSE') . "</p>";

        $dietetic_is_admin = dietetic_is_admin();
        echo "<p><strong>dietetic_is_admin():</strong> " . ($dietetic_is_admin ? '✅ TRUE' : '❌ FALSE') . "</p>";

        // Get current user full info
        $this->db->select('staffid, firstname, lastname, email, admin, active');
        $this->db->where('staffid', $current_staff_id);
        $current_user = $this->db->get(db_prefix() . 'staff')->row();

        if ($current_user) {
            echo "<h3>Détails Utilisateur:</h3>";
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>Colonne</th><th>Valeur</th></tr>";
            echo "<tr><td>Staff ID</td><td>{$current_user->staffid}</td></tr>";
            echo "<tr><td>Nom</td><td>{$current_user->firstname} {$current_user->lastname}</td></tr>";
            echo "<tr><td>Email</td><td>{$current_user->email}</td></tr>";
            echo "<tr><td>Admin (DB)</td><td>" . ($current_user->admin ? '✅ 1' : '❌ 0') . "</td></tr>";
            echo "<tr><td>Active</td><td>" . ($current_user->active ? '✅ 1' : '❌ 0') . "</td></tr>";
            echo "</table>";
        }
        echo "<hr>";

        // 2. Test permission function WITHOUT staff_id parameter (like the menu does)
        echo "<h2>2. Test dietetic_has_feature_permission('food_surveys') - SANS staff_id</h2>";
        echo "<p><em>C'est exactement comment le menu l'appelle</em></p>";

        $has_permission = dietetic_has_feature_permission('food_surveys');
        echo "<p><strong>Résultat:</strong> " . ($has_permission ? '✅ TRUE (Menu VISIBLE)' : '❌ FALSE (Menu CACHÉ)') . "</p>";
        echo "<hr>";

        // 3. Test permission function WITH staff_id parameter
        echo "<h2>3. Test dietetic_has_feature_permission('food_surveys', {$current_staff_id}) - AVEC staff_id</h2>";

        $has_permission_explicit = dietetic_has_feature_permission('food_surveys', $current_staff_id);
        echo "<p><strong>Résultat:</strong> " . ($has_permission_explicit ? '✅ TRUE' : '❌ FALSE') . "</p>";
        echo "<hr>";

        // 4. Check permissions in database for current user
        echo "<h2>4. Permissions en Base de Données pour l'Utilisateur Actuel</h2>";

        $this->db->where('staff_id', $current_staff_id);
        $perms_in_db = $this->db->get(db_prefix() . 'dietic_staff_permissions')->result();

        if (count($perms_in_db) > 0) {
            echo "<p>✅ Permissions trouvées: " . count($perms_in_db) . "</p>";
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>Permission Key</th><th>Value</th><th>Granted At</th></tr>";
            foreach ($perms_in_db as $perm) {
                echo "<tr>";
                echo "<td>{$perm->permission_key}</td>";
                echo "<td>" . ($perm->permission_value ? '✅ 1' : '❌ 0') . "</td>";
                echo "<td>{$perm->granted_at}</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>❌ Aucune permission trouvée pour ce staff_id dans la base de données</p>";
        }
        echo "<hr>";

        // 5. Step-by-step function execution
        echo "<h2>5. Exécution Pas-à-Pas de dietetic_has_feature_permission()</h2>";

        echo "<p><strong>Étape 1:</strong> staff_id = null ? → ";
        echo "Oui, donc staff_id = dietetic_get_staff_user_id() = {$dietetic_staff_id}</p>";

        echo "<p><strong>Étape 2:</strong> staff_id est vide ? → ";
        echo ($dietetic_staff_id ? "Non, on continue" : "Oui, RETURN FALSE") . "</p>";

        if ($dietetic_staff_id) {
            echo "<p><strong>Étape 3:</strong> Vérifier si staff_id={$dietetic_staff_id} est admin dans la DB...</p>";

            $this->db->select('admin');
            $this->db->where('staffid', $dietetic_staff_id);
            $staff_check = $this->db->get(db_prefix() . 'staff')->row();

            if ($staff_check) {
                echo "<p>→ Staff trouvé, admin = " . ($staff_check->admin ? '1' : '0') . "</p>";
                if ($staff_check->admin == 1) {
                    echo "<p style='color: green;'><strong>→ RETURN TRUE (Admin bypass)</strong></p>";
                } else {
                    echo "<p><strong>Étape 4:</strong> Pas admin, vérifier dans la table des permissions...</p>";

                    $this->db->where('staff_id', $dietetic_staff_id);
                    $this->db->where('permission_key', 'food_surveys');
                    $perm_check = $this->db->get(db_prefix() . 'dietic_staff_permissions')->row();

                    if ($perm_check) {
                        echo "<p>→ Permission trouvée, value = {$perm_check->permission_value}</p>";
                        echo "<p style='color: " . ($perm_check->permission_value ? 'green' : 'red') . ";'><strong>→ RETURN " . ($perm_check->permission_value ? 'TRUE' : 'FALSE') . "</strong></p>";
                    } else {
                        echo "<p>→ Permission non trouvée</p>";
                        echo "<p><strong>Étape 5:</strong> Vérifier valeur par défaut...</p>";

                        $default = dietetic_get_option('permissions_default_food_surveys', false);
                        echo "<p>→ permissions_default_food_surveys = " . ($default ? 'TRUE' : 'FALSE') . "</p>";
                        echo "<p style='color: " . ($default ? 'green' : 'red') . ";'><strong>→ RETURN " . ($default ? 'TRUE' : 'FALSE') . "</strong></p>";
                    }
                }
            } else {
                echo "<p style='color: red;'>→ Staff NON trouvé ! RETURN FALSE</p>";
            }
        }
        echo "<hr>";

        // 6. Solution
        echo "<h2>6. 💡 Solution</h2>";

        if ($has_permission) {
            echo "<div style='background: #4CAF50; color: white; padding: 15px;'>";
            echo "<h3>✅ Le menu DEVRAIT être visible</h3>";
            echo "<p>La fonction retourne TRUE. Si vous ne voyez pas le menu, videz le cache du navigateur.</p>";
            echo "</div>";
        } else {
            echo "<div style='background: #f44336; color: white; padding: 15px;'>";
            echo "<h3>❌ Le menu est CACHÉ</h3>";

            if ($current_user && $current_user->admin == 1) {
                echo "<p><strong>Problème identifié:</strong> Vous êtes admin mais la fonction ne vous reconnaît pas comme tel.</p>";
                echo "<p><strong>Cause probable:</strong> Bug dans la vérification admin de dietetic_has_feature_permission()</p>";
            } else {
                echo "<p><strong>Problème identifié:</strong> Vous n'êtes pas admin ET vous n'avez pas de permission explicite.</p>";
                echo "<p><strong>Solution:</strong></p>";
                echo "<ol>";
                echo "<li>Allez sur: <a href='" . admin_url('dietetic/staff_permissions') . "' style='color: white;'>Page des Permissions</a></li>";
                echo "<li>Activez 'Enquêtes Alimentaires' pour votre compte (Staff ID: {$current_staff_id})</li>";
                echo "<li>Ou connectez-vous avec un compte admin</li>";
                echo "</ol>";
            }
            echo "</div>";
        }

        echo "<hr>";
        echo "<p><em>⚠️ Copiez TOUT le contenu de cette page et donnez-le moi</em></p>";
    }
}
