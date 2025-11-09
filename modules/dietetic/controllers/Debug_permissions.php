<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Temporary debug controller to diagnose permissions table issue
 * Access via: /admin/dietetic/debug_permissions
 * DELETE THIS FILE after diagnosis
 */
class Debug_permissions extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        // Only admins can access
        if (!is_admin()) {
            access_denied('Debug Permissions');
        }
    }

    public function index()
    {
        echo "<h1>Diagnostic du Système de Permissions</h1>";
        echo "<hr>";

        // 1. Check database prefix
        echo "<h2>1. Préfixe de la Base de Données</h2>";
        echo "<p><strong>db_prefix():</strong> " . db_prefix() . "</p>";
        echo "<p><strong>Table recherchée:</strong> " . db_prefix() . "dietic_staff_permissions</p>";
        echo "<hr>";

        // 2. Check if table exists with standard prefix
        echo "<h2>2. Vérification de l'Existence de la Table</h2>";
        $table_name = db_prefix() . 'dietic_staff_permissions';
        $exists = $this->db->table_exists($table_name);
        echo "<p><strong>Table '{$table_name}' existe:</strong> " . ($exists ? "✅ OUI" : "❌ NON") . "</p>";

        // Also check with hardcoded tbl prefix
        $hardcoded_table = 'tbldietic_staff_permissions';
        $exists_hardcoded = $this->db->table_exists($hardcoded_table);
        echo "<p><strong>Table '{$hardcoded_table}' existe:</strong> " . ($exists_hardcoded ? "✅ OUI" : "❌ NON") . "</p>";
        echo "<hr>";

        // 3. List all dietetic tables
        echo "<h2>3. Tables Dietetic Existantes</h2>";
        echo "<ul>";
        $tables = $this->db->list_tables();
        foreach ($tables as $table) {
            if (strpos($table, 'dietic') !== false) {
                echo "<li>{$table}</li>";
            }
        }
        echo "</ul>";
        echo "<hr>";

        // 4. Try to query the table
        echo "<h2>4. Test de Requête sur la Table</h2>";
        try {
            $query = $this->db->get($table_name);
            echo "<p>✅ Requête réussie! Nombre d'enregistrements: " . $query->num_rows() . "</p>";

            if ($query->num_rows() > 0) {
                echo "<h3>Permissions Existantes:</h3>";
                echo "<table border='1' cellpadding='5'>";
                echo "<tr><th>ID</th><th>Staff ID</th><th>Permission Key</th><th>Enabled</th><th>Granted At</th></tr>";
                foreach ($query->result() as $row) {
                    echo "<tr>";
                    echo "<td>{$row->id}</td>";
                    echo "<td>{$row->staff_id}</td>";
                    echo "<td>{$row->permission_key}</td>";
                    echo "<td>" . ($row->permission_value ? '✅' : '❌') . "</td>";
                    echo "<td>{$row->granted_at}</td>";
                    echo "</tr>";
                }
                echo "</table>";
            }
        } catch (Exception $e) {
            echo "<p>❌ Erreur lors de la requête: " . $e->getMessage() . "</p>";
        }
        echo "<hr>";

        // 5. Check helper functions
        echo "<h2>5. Fonctions Helper</h2>";
        $this->load->helper('dietetic/dietetic');

        echo "<p><strong>dietetic_get_available_permissions() existe:</strong> " .
             (function_exists('dietetic_get_available_permissions') ? "✅ OUI" : "❌ NON") . "</p>";

        if (function_exists('dietetic_get_available_permissions')) {
            $permissions = dietetic_get_available_permissions();
            echo "<p><strong>Permissions disponibles:</strong></p><ul>";
            foreach ($permissions as $key => $info) {
                echo "<li><strong>{$key}</strong>: {$info['label']}</li>";
            }
            echo "</ul>";
        }
        echo "<hr>";

        // 6. Check staff data retrieval
        echo "<h2>6. Test de Récupération des Données Staff</h2>";
        $this->db->select('staffid, firstname, lastname, email, admin, active, is_not_staff');
        $this->db->where('active', 1);
        $this->db->limit(5);
        $staff_query = $this->db->get(db_prefix() . 'staff');

        if ($staff_query->num_rows() > 0) {
            echo "<p>✅ Staff trouvés: " . $staff_query->num_rows() . " (limité à 5)</p>";
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>ID</th><th>Nom</th><th>Email</th><th>Admin</th><th>Active</th></tr>";
            foreach ($staff_query->result() as $staff) {
                echo "<tr>";
                echo "<td>{$staff->staffid}</td>";
                echo "<td>{$staff->firstname} {$staff->lastname}</td>";
                echo "<td>{$staff->email}</td>";
                echo "<td>" . ($staff->admin ? 'Oui' : 'Non') . "</td>";
                echo "<td>" . ($staff->active ? 'Oui' : 'Non') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>❌ Aucun staff actif trouvé</p>";
        }
        echo "<hr>";

        // 7. Recommendation
        echo "<h2>7. Recommandation</h2>";
        if (!$exists) {
            echo "<div style='background: #f44336; color: white; padding: 15px; border-radius: 5px;'>";
            echo "<h3>❌ PROBLÈME DÉTECTÉ</h3>";
            echo "<p>La table des permissions n'existe pas dans votre base de données.</p>";
            echo "<p><strong>Solution:</strong></p>";
            echo "<ol>";
            echo "<li>Allez sur: <a href='" . admin_url('dietetic/notifications/migrations') . "' style='color: white; text-decoration: underline;'>Page des Migrations</a></li>";
            echo "<li>Exécutez la migration <strong>'Système de Permissions Granulaires'</strong></li>";
            echo "<li>Vérifiez que le statut passe à 'Installé'</li>";
            echo "<li>Revenez sur cette page pour re-vérifier</li>";
            echo "</ol>";
            echo "</div>";
        } else {
            echo "<div style='background: #4CAF50; color: white; padding: 15px; border-radius: 5px;'>";
            echo "<h3>✅ TOUT EST OK</h3>";
            echo "<p>La table existe. Vous pouvez maintenant accéder à:</p>";
            echo "<p><a href='" . admin_url('dietetic/staff_permissions') . "' style='color: white; text-decoration: underline; font-size: 18px;'>Page de Gestion des Permissions</a></p>";
            echo "</div>";
        }
        echo "<hr>";

        echo "<p><em>⚠️ IMPORTANT: Supprimez ce fichier après diagnostic (modules/dietetic/controllers/Debug_permissions.php)</em></p>";
    }
}
