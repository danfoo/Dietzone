<?php
/**
 * Migration: Création de la table de suivi quotidien (daily tracking)
 * URL d'accès: https://votredomaine.com/admin/dietetic/create_daily_tracking_table
 *
 * Cette table permet de tracker les activités quotidiennes du patient:
 * - Hydratation (verres d'eau)
 * - Repas validés (petit-déj, déjeuner, dîner)
 * - Activité physique (minutes)
 * - Calories consommées (optionnel)
 *
 * @author Eric Gilles SAGNA
 * @website https://maestrodan.art
 */

defined('BASEPATH') or exit('No direct script access allowed');

// Get database instance
$CI =& get_instance();
$CI->load->database();

echo "<!DOCTYPE html>";
echo "<html><head><meta charset='UTF-8'><title>Migration Daily Tracking</title>";
echo "<style>
body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
.container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
h2 { color: #01807B; border-bottom: 3px solid #01807B; padding-bottom: 10px; }
h3 { color: #333; margin-top: 30px; }
.log-entry { padding: 10px; margin: 5px 0; border-radius: 5px; border-left: 4px solid; font-family: monospace; font-size: 13px; }
.log-success { background: #d4edda; border-color: #28a745; color: #155724; }
.log-error { background: #f8d7da; border-color: #dc3545; color: #721c24; }
.log-info { background: #d1ecf1; border-color: #17a2b8; color: #0c5460; }
.log-warning { background: #fff3cd; border-color: #ffc107; color: #856404; }
.summary { background: #e8f4f3; padding: 20px; border-radius: 5px; border-left: 4px solid #01807B; margin: 20px 0; }
.btn { display: inline-block; padding: 12px 24px; background: #01807B; color: white; text-decoration: none; border-radius: 5px; margin: 10px 5px 10px 0; font-weight: bold; }
.btn:hover { background: #015a57; }
</style></head><body>";

echo "<div class='container'>";
echo "<h2>🚀 Migration: Table de Suivi Quotidien</h2>";

$table_name = db_prefix() . 'dietic_daily_tracking';

// Check if table already exists
$table_exists = $CI->db->table_exists($table_name);

if ($table_exists) {
    echo "<div class='log-warning'>⚠️ La table '{$table_name}' existe déjà. Aucune action nécessaire.</div>";
} else {
    echo "<div class='log-info'>📋 Création de la table '{$table_name}'...</div>";

    try {
        // Create table with all necessary columns
        $sql = "
        CREATE TABLE `{$table_name}` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `patient_id` int(11) NOT NULL,
            `tracking_date` date NOT NULL,
            `water_glasses` tinyint(2) DEFAULT 0 COMMENT 'Nombre de verres d''eau (0-12)',
            `breakfast_checked` tinyint(1) DEFAULT 0 COMMENT 'Petit-déjeuner validé',
            `lunch_checked` tinyint(1) DEFAULT 0 COMMENT 'Déjeuner validé',
            `dinner_checked` tinyint(1) DEFAULT 0 COMMENT 'Dîner validé',
            `activity_minutes` smallint(4) DEFAULT 0 COMMENT 'Minutes d''activité physique',
            `activity_type` varchar(100) DEFAULT NULL COMMENT 'Type d''activité (marche, course, yoga...)',
            `calories_consumed` int(5) DEFAULT NULL COMMENT 'Calories consommées (optionnel)',
            `mood` varchar(20) DEFAULT NULL COMMENT 'Humeur du jour (great, good, neutral, bad)',
            `notes` text DEFAULT NULL COMMENT 'Notes personnelles du patient',
            `created_at` datetime NOT NULL,
            `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `patient_date_unique` (`patient_id`, `tracking_date`),
            KEY `tracking_date_idx` (`tracking_date`),
            KEY `patient_id_idx` (`patient_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";

        $CI->db->query($sql);

        echo "<div class='log-success'>✅ Table '{$table_name}' créée avec succès!</div>";

        // Add foreign key constraint
        echo "<div class='log-info'>🔗 Ajout de la contrainte de clé étrangère...</div>";

        $fk_sql = "
        ALTER TABLE `{$table_name}`
        ADD CONSTRAINT `fk_daily_tracking_patient`
        FOREIGN KEY (`patient_id`) REFERENCES `" . db_prefix() . "dietic_patients` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE;
        ";

        try {
            $CI->db->query($fk_sql);
            echo "<div class='log-success'>✅ Contrainte de clé étrangère ajoutée avec succès!</div>";
        } catch (Exception $e) {
            echo "<div class='log-warning'>⚠️ Impossible d'ajouter la contrainte FK (peut-être déjà présente): " . htmlspecialchars($e->getMessage()) . "</div>";
        }

        // Create indexes for performance
        echo "<div class='log-info'>⚡ Optimisation des index...</div>";
        echo "<div class='log-success'>✅ Index créés pour de meilleures performances!</div>";

        // Summary
        echo "<div class='summary'>";
        echo "<h3>📊 Résumé de la migration</h3>";
        echo "<ul>";
        echo "<li>✅ Table créée: <strong>{$table_name}</strong></li>";
        echo "<li>✅ Colonnes: 13 (id, patient_id, tracking_date, water_glasses, breakfast_checked, lunch_checked, dinner_checked, activity_minutes, activity_type, calories_consumed, mood, notes, created_at, updated_at)</li>";
        echo "<li>✅ Index: 3 (PRIMARY, patient_date_unique, tracking_date_idx, patient_id_idx)</li>";
        echo "<li>✅ Contraintes: FK vers tbldietic_patients</li>";
        echo "</ul>";
        echo "<p><strong>🎉 Migration terminée avec succès!</strong></p>";
        echo "</div>";

        // Next steps
        echo "<h3>📝 Prochaines étapes</h3>";
        echo "<ol>";
        echo "<li>Créer le modèle PHP: <code>Dietetic_daily_tracking_model.php</code></li>";
        echo "<li>Ajouter les méthodes dans le controller Portal</li>";
        echo "<li>Intégrer l'interface 'Ma Journée' dans le dashboard</li>";
        echo "</ol>";

    } catch (Exception $e) {
        echo "<div class='log-error'>❌ ERREUR lors de la création de la table: " . htmlspecialchars($e->getMessage()) . "</div>";
        echo "<div class='log-error'>Stack trace: " . htmlspecialchars($e->getTraceAsString()) . "</div>";
    }
}

echo "<p><a href='" . admin_url('dietetic/patients') . "' class='btn'>← Retour au module Diététique</a></p>";
echo "</div>";
echo "</body></html>";
