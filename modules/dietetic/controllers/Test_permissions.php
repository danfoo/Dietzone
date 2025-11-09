<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Test Controller for Permissions System
 * Access via: /admin/dietetic/test_permissions
 * DELETE THIS FILE after testing
 */
class Test_permissions extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        // Only admins can test
        if (!is_admin()) {
            access_denied('Test Permissions');
        }

        $this->load->helper('dietetic/dietetic');
    }

    public function index()
    {
        echo "<h1>🧪 Test du Système de Permissions</h1>";
        echo "<p><em>Ce test va créer/modifier/vérifier une permission de test</em></p>";
        echo "<hr>";

        // Get first non-admin staff for testing
        $this->db->select('staffid, firstname, lastname, email, admin');
        $this->db->where('active', 1);
        $this->db->where('admin', 0);
        $this->db->limit(1);
        $test_staff = $this->db->get(db_prefix() . 'staff')->row();

        if (!$test_staff) {
            echo "<div style='background: #f44336; color: white; padding: 15px;'>";
            echo "❌ Aucun diététicien non-admin trouvé pour le test. Impossible de continuer.";
            echo "</div>";
            return;
        }

        echo "<h2>1. Sujet du Test</h2>";
        echo "<p><strong>Staff ID:</strong> {$test_staff->staffid}</p>";
        echo "<p><strong>Nom:</strong> {$test_staff->firstname} {$test_staff->lastname}</p>";
        echo "<p><strong>Email:</strong> {$test_staff->email}</p>";
        echo "<p><strong>Admin:</strong> " . ($test_staff->admin ? 'Oui' : 'Non') . "</p>";
        echo "<hr>";

        // Test 1: Check current permissions
        echo "<h2>2. Permissions Actuelles (AVANT modification)</h2>";
        $current_perms = dietetic_get_staff_permissions($test_staff->staffid);
        echo "<pre>" . print_r($current_perms, true) . "</pre>";
        echo "<hr>";

        // Test 2: Grant a test permission
        echo "<h2>3. Attribution de la Permission 'food_surveys' = ACTIVÉE</h2>";
        $grant_result = dietetic_grant_permission($test_staff->staffid, 'food_surveys', true, 'Test automatique');
        echo "<p><strong>Résultat de dietetic_grant_permission():</strong> " . ($grant_result ? '✅ TRUE' : '❌ FALSE') . "</p>";

        if ($grant_result) {
            echo "<p style='color: green;'>✅ La fonction indique que la permission a été sauvegardée</p>";
        } else {
            echo "<p style='color: red;'>❌ La fonction indique un échec</p>";
        }
        echo "<hr>";

        // Test 3: Verify in database
        echo "<h2>4. Vérification Directe dans la Base de Données</h2>";
        $this->db->where('staff_id', $test_staff->staffid);
        $this->db->where('permission_key', 'food_surveys');
        $db_permission = $this->db->get(db_prefix() . 'dietic_staff_permissions')->row();

        if ($db_permission) {
            echo "<p>✅ Permission trouvée dans la base de données</p>";
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>Colonne</th><th>Valeur</th></tr>";
            echo "<tr><td>ID</td><td>{$db_permission->id}</td></tr>";
            echo "<tr><td>Staff ID</td><td>{$db_permission->staff_id}</td></tr>";
            echo "<tr><td>Permission Key</td><td>{$db_permission->permission_key}</td></tr>";
            echo "<tr><td>Permission Value</td><td><strong>" . ($db_permission->permission_value ? '✅ 1 (Activée)' : '❌ 0 (Désactivée)') . "</strong></td></tr>";
            echo "<tr><td>Granted By</td><td>{$db_permission->granted_by}</td></tr>";
            echo "<tr><td>Granted At</td><td>{$db_permission->granted_at}</td></tr>";
            echo "<tr><td>Updated At</td><td>{$db_permission->updated_at}</td></tr>";
            echo "<tr><td>Notes</td><td>{$db_permission->notes}</td></tr>";
            echo "</table>";
        } else {
            echo "<p style='color: red;'>❌ Permission NON trouvée dans la base de données !</p>";
            echo "<p>Cela signifie que dietetic_grant_permission() n'a pas sauvegardé la permission.</p>";
        }
        echo "<hr>";

        // Test 4: Check with helper function
        echo "<h2>5. Vérification via dietetic_get_staff_permissions()</h2>";
        $updated_perms = dietetic_get_staff_permissions($test_staff->staffid);
        echo "<pre>" . print_r($updated_perms, true) . "</pre>";

        if (isset($updated_perms['food_surveys']) && $updated_perms['food_surveys']) {
            echo "<p style='color: green;'>✅ La permission 'food_surveys' est ACTIVÉE selon dietetic_get_staff_permissions()</p>";
        } else {
            echo "<p style='color: red;'>❌ La permission 'food_surveys' n'est PAS activée selon dietetic_get_staff_permissions()</p>";
        }
        echo "<hr>";

        // Test 5: Check with has_permission function
        echo "<h2>6. Vérification via dietetic_has_feature_permission()</h2>";

        // Save current user ID
        $current_user = get_staff_user_id();

        // Test for the test staff
        $has_perm = dietetic_has_feature_permission('food_surveys', $test_staff->staffid);
        echo "<p><strong>dietetic_has_feature_permission('food_surveys', {$test_staff->staffid}):</strong> " . ($has_perm ? '✅ TRUE' : '❌ FALSE') . "</p>";

        if ($has_perm) {
            echo "<p style='color: green;'>✅ Le diététicien a accès selon dietetic_has_feature_permission()</p>";
        } else {
            echo "<p style='color: red;'>❌ Le diététicien n'a PAS accès selon dietetic_has_feature_permission()</p>";
        }
        echo "<hr>";

        // Test 6: Try to disable the permission
        echo "<h2>7. Désactivation de la Permission 'food_surveys'</h2>";
        $revoke_result = dietetic_grant_permission($test_staff->staffid, 'food_surveys', false, 'Test de désactivation');
        echo "<p><strong>Résultat:</strong> " . ($revoke_result ? '✅ TRUE' : '❌ FALSE') . "</p>";

        // Verify
        $this->db->where('staff_id', $test_staff->staffid);
        $this->db->where('permission_key', 'food_surveys');
        $db_permission_after = $this->db->get(db_prefix() . 'dietic_staff_permissions')->row();

        if ($db_permission_after) {
            echo "<p><strong>Valeur dans la DB après désactivation:</strong> " . ($db_permission_after->permission_value ? '1 (Activée)' : '0 (Désactivée)') . "</p>";
        }

        $has_perm_after = dietetic_has_feature_permission('food_surveys', $test_staff->staffid);
        echo "<p><strong>dietetic_has_feature_permission() après désactivation:</strong> " . ($has_perm_after ? 'TRUE' : 'FALSE') . "</p>";
        echo "<hr>";

        // Test 7: Test AJAX endpoint
        echo "<h2>8. Test de l'Endpoint AJAX</h2>";
        echo "<p>L'endpoint AJAX est: <code>" . admin_url('dietetic/staff_permissions/update_permission') . "</code></p>";
        echo "<p>Paramètres attendus: staff_id, permission_key, enabled</p>";

        echo "<h3>Test en JavaScript Console</h3>";
        echo "<p>Copiez et exécutez ce code dans la console JavaScript de votre navigateur :</p>";
        echo "<pre style='background: #f5f5f5; padding: 10px; overflow: auto;'>";
        echo "$.ajax({
    url: '" . admin_url('dietetic/staff_permissions/update_permission') . "',
    type: 'POST',
    dataType: 'json',
    data: {
        staff_id: {$test_staff->staffid},
        permission_key: 'food_surveys',
        enabled: 1
    },
    success: function(response) {
        console.log('Succès:', response);
        alert('Réponse: ' + JSON.stringify(response));
    },
    error: function(xhr, status, error) {
        console.error('Erreur:', error);
        alert('Erreur: ' + error);
    }
});";
        echo "</pre>";
        echo "<hr>";

        // Summary
        echo "<h2>9. 📊 Résumé du Diagnostic</h2>";
        echo "<table border='1' cellpadding='10' style='width: 100%;'>";
        echo "<tr><th>Test</th><th>Résultat</th><th>Statut</th></tr>";

        $tests = [
            'dietetic_grant_permission() retourne TRUE' => $grant_result,
            'Permission trouvée dans la base de données' => isset($db_permission),
            'Permission value = 1 dans la DB' => (isset($db_permission) && $db_permission->permission_value == 1),
            'dietetic_get_staff_permissions() retourne la permission' => isset($updated_perms['food_surveys']),
            'dietetic_has_feature_permission() retourne TRUE' => $has_perm,
        ];

        foreach ($tests as $test_name => $result) {
            $status = $result ? '✅ PASS' : '❌ FAIL';
            $color = $result ? 'green' : 'red';
            echo "<tr>";
            echo "<td>{$test_name}</td>";
            echo "<td>" . ($result ? 'Oui' : 'Non') . "</td>";
            echo "<td style='color: {$color}; font-weight: bold;'>{$status}</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<hr>";

        echo "<p><strong>Prochaine étape:</strong> Copiez TOUS ces résultats et donnez-les moi pour analyse.</p>";
        echo "<p><em>⚠️ N'oubliez pas de supprimer ce fichier après: modules/dietetic/controllers/Test_permissions.php</em></p>";
    }
}
