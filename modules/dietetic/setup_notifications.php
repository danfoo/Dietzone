<?php
/**
 * Script de Configuration Automatique des Notifications
 * Ce script vérifie et installe les tables de notifications si nécessaire
 *
 * Accès: /admin/dietetic/setup/notifications
 * Ou exécuter: php modules/dietetic/setup_notifications.php
 */

defined('BASEPATH') or exit('No direct script access allowed');

// Si exécuté en CLI
if (php_sapi_name() === 'cli') {
    define('BASEPATH', true);
    require_once(__DIR__ . '/../../application/config/app-config.php');
    require_once(__DIR__ . '/../../application/config/config.php');
    $_SERVER['CI_ENV'] = 'production';
    require_once(BASEPATH . 'core/CodeIgniter.php');
}

$CI = &get_instance();

echo "========================================\n";
echo "DIETETIC - Configuration Notifications\n";
echo "========================================\n\n";

// Liste des tables requises
$required_tables = [
    'tbldietic_notification_preferences',
    'tbldietic_notification_logs',
    'tbldietic_milestones',
    'tbldietic_notification_settings',
    'tbldietic_fcm_tokens',
];

echo "1. Vérification des tables...\n\n";

$missing_tables = [];
$existing_tables = [];

foreach ($required_tables as $table) {
    $query = $CI->db->query("SHOW TABLES LIKE '{$table}'");

    if ($query->num_rows() > 0) {
        echo "   ✓ {$table} - EXISTE\n";
        $existing_tables[] = $table;
    } else {
        echo "   ✗ {$table} - MANQUANTE\n";
        $missing_tables[] = $table;
    }
}

echo "\n";

// Si toutes les tables existent
if (empty($missing_tables)) {
    echo "========================================\n";
    echo "✓ Toutes les tables existent!\n";
    echo "========================================\n\n";

    echo "2. Vérification des préférences par défaut...\n\n";

    // Créer les préférences pour les patients qui n'en ont pas
    $patients_without_prefs = $CI->db->query("
        SELECT p.id, p.firstname, p.lastname
        FROM " . db_prefix() . "dietic_patients p
        LEFT JOIN " . db_prefix() . "dietic_notification_preferences np ON p.id = np.patient_id
        WHERE np.id IS NULL
    ")->result();

    if (!empty($patients_without_prefs)) {
        echo "   Trouvé " . count($patients_without_prefs) . " patients sans préférences\n";

        foreach ($patients_without_prefs as $patient) {
            $CI->db->insert(db_prefix() . 'dietic_notification_preferences', [
                'patient_id' => $patient->id,
                'reminder_weight' => 1,
                'reminder_weight_day' => 'friday',
                'reminder_weight_time' => '09:00:00',
                'reminder_water' => 1,
                'reminder_water_times' => '10:00,14:00,18:00',
                'reminder_breakfast' => 1,
                'reminder_breakfast_time' => '08:00:00',
                'reminder_lunch' => 1,
                'reminder_lunch_time' => '12:30:00',
                'reminder_dinner' => 1,
                'reminder_dinner_time' => '19:00:00',
                'notify_recommendation' => 1,
                'notify_consultation' => 1,
                'notify_milestone' => 1,
                'notify_program' => 1,
                'notify_food_entry' => 1,
                'channel_email' => 1,
                'channel_sms' => 0,
                'channel_whatsapp' => 0,
                'channel_push' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            echo "   ✓ Préférences créées pour: {$patient->firstname} {$patient->lastname}\n";
        }
    } else {
        echo "   ✓ Tous les patients ont des préférences\n";
    }

    echo "\n========================================\n";
    echo "Configuration terminée avec succès!\n";
    echo "========================================\n\n";

    echo "PROCHAINES ÉTAPES:\n\n";
    echo "1. Vérifier la configuration email dans Perfex:\n";
    echo "   Admin > Setup > Settings > Email\n\n";

    echo "2. Le cron s'exécutera automatiquement avec Perfex CRM\n";
    echo "   Fréquence: Toutes les heures (selon config Perfex)\n\n";

    echo "3. Les patients peuvent configurer leurs préférences:\n";
    echo "   Portail Patient > Notifications > Préférences\n\n";

    echo "4. Voir les logs dans:\n";
    echo "   Admin > Activity Log (rechercher 'Dietetic Cron')\n\n";

    exit(0);
}

// Si des tables manquent, les créer
echo "========================================\n";
echo "2. Installation des tables manquantes...\n";
echo "========================================\n\n";

$migration_file = __DIR__ . '/migrations/add_notifications_system.sql';

if (!file_exists($migration_file)) {
    echo "✗ ERREUR: Fichier de migration introuvable:\n";
    echo "   {$migration_file}\n\n";
    exit(1);
}

echo "Exécution de la migration...\n";

try {
    $sql = file_get_contents($migration_file);

    // Séparer les requêtes SQL
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        function($stmt) {
            return !empty($stmt) &&
                   !preg_match('/^--/', $stmt) &&
                   !preg_match('/^\/\*/', $stmt);
        }
    );

    foreach ($statements as $statement) {
        if (!empty($statement)) {
            $CI->db->query($statement);
        }
    }

    echo "   ✓ Migration exécutée avec succès\n\n";

    // Re-vérifier les tables
    echo "3. Vérification finale...\n\n";

    $all_created = true;
    foreach ($required_tables as $table) {
        $query = $CI->db->query("SHOW TABLES LIKE '{$table}'");

        if ($query->num_rows() > 0) {
            echo "   ✓ {$table}\n";
        } else {
            echo "   ✗ {$table} - ÉCHEC\n";
            $all_created = false;
        }
    }

    echo "\n";

    if ($all_created) {
        echo "========================================\n";
        echo "✓ Installation réussie!\n";
        echo "========================================\n\n";

        echo "Le système de notifications est maintenant actif.\n";
        echo "Les rappels automatiques seront envoyés via le cron Perfex.\n\n";
    } else {
        echo "========================================\n";
        echo "✗ Installation partielle\n";
        echo "========================================\n\n";
        echo "Certaines tables n'ont pas pu être créées.\n";
        echo "Vérifiez les permissions de la base de données.\n\n";
    }

} catch (Exception $e) {
    echo "✗ ERREUR lors de l'installation:\n";
    echo "   " . $e->getMessage() . "\n\n";
    exit(1);
}
