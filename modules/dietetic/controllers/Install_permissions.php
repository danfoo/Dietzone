<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Installation Controller for Staff Permissions Table
 * Access via: /admin/dietetic/install_permissions
 * DELETE THIS FILE after installation
 */
class Install_permissions extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        // Only admins can install
        if (!is_admin()) {
            access_denied('Install Permissions');
        }
    }

    public function index()
    {
        echo "<h1>Installation du Système de Permissions</h1>";
        echo "<hr>";

        // Check if table already exists
        $table_name = db_prefix() . 'dietic_staff_permissions';
        if ($this->db->table_exists($table_name)) {
            echo "<div style='background: #4CAF50; color: white; padding: 15px; border-radius: 5px;'>";
            echo "<h3>✅ Table Déjà Installée</h3>";
            echo "<p>La table '{$table_name}' existe déjà dans votre base de données.</p>";
            echo "<p><a href='" . admin_url('dietetic/staff_permissions') . "' style='color: white; text-decoration: underline; font-size: 18px;'>Aller à la Gestion des Permissions →</a></p>";
            echo "</div>";
            return;
        }

        echo "<h2>Étape 1: Création de la table</h2>";

        // Create the table
        $sql = "CREATE TABLE IF NOT EXISTS `{$table_name}` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `staff_id` int(11) NOT NULL COMMENT 'FK to tblstaff',
          `permission_key` varchar(100) NOT NULL COMMENT 'e.g., food_surveys, notifications_settings',
          `permission_value` tinyint(1) DEFAULT 1 COMMENT '1 = enabled, 0 = disabled',
          `granted_by` int(11) DEFAULT NULL COMMENT 'Admin who granted this permission',
          `granted_at` datetime DEFAULT CURRENT_TIMESTAMP,
          `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
          `notes` text DEFAULT NULL,
          PRIMARY KEY (`id`),
          UNIQUE KEY `unique_staff_permission` (`staff_id`, `permission_key`),
          KEY `idx_staff_id` (`staff_id`),
          KEY `idx_permission_key` (`permission_key`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        try {
            $this->db->query($sql);
            echo "<p>✅ Table '{$table_name}' créée avec succès!</p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Erreur lors de la création de la table: " . $e->getMessage() . "</p>";
            echo "<p><strong>SQL exécuté:</strong></p>";
            echo "<pre style='background: #f5f5f5; padding: 10px; overflow: auto;'>" . htmlspecialchars($sql) . "</pre>";
            return;
        }

        echo "<hr>";
        echo "<h2>Étape 2: Insertion des permissions par défaut</h2>";

        // Insert default permissions for existing staff
        $sql_insert = "INSERT INTO `{$table_name}` (`staff_id`, `permission_key`, `permission_value`, `granted_by`, `notes`)
        SELECT
            s.staffid,
            'food_surveys',
            1,
            1,
            'Default permission granted during system installation'
        FROM `" . db_prefix() . "staff` s
        WHERE s.active = 1
          AND s.staffid NOT IN (SELECT staff_id FROM `{$table_name}` WHERE permission_key = 'food_surveys')
        ON DUPLICATE KEY UPDATE permission_value = permission_value";

        try {
            $this->db->query($sql_insert);
            $affected_rows = $this->db->affected_rows();
            echo "<p>✅ Permissions par défaut insérées avec succès! ({$affected_rows} enregistrements)</p>";
        } catch (Exception $e) {
            echo "<p style='color: orange;'>⚠️ Avertissement lors de l'insertion des permissions: " . $e->getMessage() . "</p>";
            echo "<p>La table a été créée, mais les permissions par défaut n'ont pas pu être ajoutées.</p>";
        }

        echo "<hr>";
        echo "<h2>Étape 3: Configuration des paramètres par défaut</h2>";

        // Check if settings table exists
        $settings_table = db_prefix() . 'dietic_settings';
        if ($this->db->table_exists($settings_table)) {
            // Insert default settings
            $settings = [
                [
                    'setting_key' => 'permissions_default_food_surveys',
                    'setting_value' => '0',
                    'setting_type' => 'boolean',
                    'description' => 'Default permission for new staff: Food Surveys access'
                ],
                [
                    'setting_key' => 'permissions_default_notifications_manage',
                    'setting_value' => '0',
                    'setting_type' => 'boolean',
                    'description' => 'Default permission for new staff: Manage notifications settings'
                ]
            ];

            foreach ($settings as $setting) {
                try {
                    // Check if setting already exists
                    $this->db->where('setting_key', $setting['setting_key']);
                    $exists = $this->db->get($settings_table)->row();

                    if (!$exists) {
                        $this->db->insert($settings_table, $setting);
                        echo "<p>✅ Paramètre '{$setting['setting_key']}' créé</p>";
                    } else {
                        echo "<p>ℹ️ Paramètre '{$setting['setting_key']}' existe déjà</p>";
                    }
                } catch (Exception $e) {
                    echo "<p style='color: orange;'>⚠️ Avertissement: " . $e->getMessage() . "</p>";
                }
            }
        } else {
            echo "<p style='color: orange;'>⚠️ Table des paramètres non trouvée. Les paramètres par défaut n'ont pas été créés.</p>";
        }

        echo "<hr>";
        echo "<h2>✅ Installation Terminée!</h2>";

        // Verify installation
        if ($this->db->table_exists($table_name)) {
            echo "<div style='background: #4CAF50; color: white; padding: 20px; border-radius: 5px; margin: 20px 0;'>";
            echo "<h3>🎉 Installation Réussie!</h3>";

            // Count permissions
            $count = $this->db->count_all_results($table_name);
            echo "<p>La table a été créée avec succès.</p>";
            echo "<p><strong>Nombre de permissions dans la base:</strong> {$count}</p>";

            echo "<h4>Prochaines étapes:</h4>";
            echo "<ol style='text-align: left;'>";
            echo "<li>Accédez à la <a href='" . admin_url('dietetic/staff_permissions') . "' style='color: white; text-decoration: underline;'><strong>Page de Gestion des Permissions</strong></a></li>";
            echo "<li>Configurez les permissions pour chaque diététicien</li>";
            echo "<li>Supprimez ce fichier: <code>modules/dietetic/controllers/Install_permissions.php</code></li>";
            echo "<li>Supprimez aussi: <code>modules/dietetic/controllers/Debug_permissions.php</code></li>";
            echo "</ol>";
            echo "</div>";
        } else {
            echo "<div style='background: #f44336; color: white; padding: 20px; border-radius: 5px;'>";
            echo "<h3>❌ Erreur d'Installation</h3>";
            echo "<p>La table n'a pas pu être créée. Contactez votre administrateur système.</p>";
            echo "</div>";
        }
    }
}
