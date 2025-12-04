<?php
/**
 * Script de diagnostic pour vérifier pourquoi la notification de dîner n'a pas été envoyée
 */

// Bootstrap Perfex CRM
define('BASEPATH', dirname(__DIR__, 2) . '/');

if (!file_exists(BASEPATH . 'index.php')) {
    die("Erreur: Perfex CRM non trouvé\n");
}

// Load configuration
if (file_exists(BASEPATH . 'application/config/app-config.php')) {
    require_once(BASEPATH . 'application/config/app-config.php');
}

// Check if we can access the database
echo "========================================\n";
echo "DIAGNOSTIC NOTIFICATION DÎNER 18h23\n";
echo "========================================\n\n";

echo "Heure actuelle: " . date('Y-m-d H:i:s') . "\n";
echo "Fuseau horaire: " . date_default_timezone_get() . "\n\n";

// Try to connect to database
if (defined('APP_DB_HOSTNAME') && defined('APP_DB_USERNAME') && defined('APP_DB_NAME')) {
    echo "✓ Configuration BDD détectée\n";
    echo "  Host: " . APP_DB_HOSTNAME . "\n";
    echo "  Database: " . APP_DB_NAME . "\n\n";

    // Try to connect
    $mysqli = @new mysqli(APP_DB_HOSTNAME, APP_DB_USERNAME, APP_DB_PASSWORD, APP_DB_NAME);

    if ($mysqli->connect_error) {
        echo "✗ Erreur de connexion: " . $mysqli->connect_error . "\n\n";
    } else {
        echo "✓ Connexion BDD réussie\n\n";

        // Check notification preferences
        echo "--- PRÉFÉRENCES DE NOTIFICATION ---\n";
        $query = "SELECT * FROM " . db_prefix() . "dietic_notification_preferences ORDER BY patient_id DESC LIMIT 1";
        $result = $mysqli->query($query);

        if ($result && $result->num_rows > 0) {
            $prefs = $result->fetch_assoc();
            echo "✓ Préférences trouvées pour patient ID: " . $prefs['patient_id'] . "\n";
            echo "  - Rappel dîner: " . ($prefs['reminder_dinner'] ? 'ACTIVÉ' : 'DÉSACTIVÉ') . "\n";
            echo "  - Heure dîner: " . $prefs['reminder_dinner_time'] . "\n";
            echo "  - Canal Email: " . ($prefs['channel_email'] ? 'ACTIVÉ' : 'DÉSACTIVÉ') . "\n";
            echo "  - Canal SMS: " . ($prefs['channel_sms'] ? 'ACTIVÉ' : 'DÉSACTIVÉ') . "\n";
            echo "  - Canal WhatsApp: " . ($prefs['channel_whatsapp'] ? 'ACTIVÉ' : 'DÉSACTIVÉ') . "\n\n";

            // Get patient contact info
            $contact_query = "SELECT c.id, c.firstname, c.lastname, c.email, c.phonenumber
                            FROM " . db_prefix() . "contacts c
                            INNER JOIN " . db_prefix() . "dietic_patients p ON c.id = p.contact_id
                            WHERE p.id = " . $prefs['patient_id'];
            $contact_result = $mysqli->query($contact_query);

            if ($contact_result && $contact_result->num_rows > 0) {
                $contact = $contact_result->fetch_assoc();
                echo "--- INFORMATIONS PATIENT ---\n";
                echo "  Nom: " . $contact['firstname'] . " " . $contact['lastname'] . "\n";
                echo "  Email: " . ($contact['email'] ?: 'NON DÉFINI') . "\n";
                echo "  Téléphone: " . ($contact['phonenumber'] ?: 'NON DÉFINI') . "\n\n";
            }

        } else {
            echo "✗ Aucune préférence trouvée\n\n";
        }

        // Check last cron run in Perfex
        echo "--- ÉTAT DU CRON PERFEX ---\n";
        $cron_query = "SELECT * FROM " . db_prefix() . "options WHERE name = 'last_cron_run'";
        $cron_result = $mysqli->query($cron_query);

        if ($cron_result && $cron_result->num_rows > 0) {
            $cron = $cron_result->fetch_assoc();
            $last_run = $cron['value'];
            echo "  Dernière exécution: " . date('Y-m-d H:i:s', $last_run) . "\n";

            $minutes_ago = floor((time() - $last_run) / 60);
            if ($minutes_ago > 10) {
                echo "  ⚠️ ATTENTION: Le cron n'a pas été exécuté depuis {$minutes_ago} minutes!\n";
                echo "  → Le cron système n'est probablement pas configuré\n\n";
            } else {
                echo "  ✓ Le cron s'exécute correctement (il y a {$minutes_ago} min)\n\n";
            }
        } else {
            echo "  ✗ Aucune exécution cron trouvée\n";
            echo "  → Le cron n'est pas configuré ou n'a jamais été exécuté\n\n";
        }

        // Check notification logs
        echo "--- DERNIÈRES NOTIFICATIONS ---\n";
        $log_query = "SELECT * FROM " . db_prefix() . "dietic_patient_notifications
                     ORDER BY created_at DESC LIMIT 5";
        $log_result = $mysqli->query($log_query);

        if ($log_result && $log_result->num_rows > 0) {
            while ($log = $log_result->fetch_assoc()) {
                echo "  • " . $log['created_at'] . " - " . $log['type'] . " (" . $log['channel'] . ") - " . $log['status'] . "\n";
            }
            echo "\n";
        } else {
            echo "  Aucune notification dans les logs\n\n";
        }

        $mysqli->close();
    }
} else {
    echo "✗ Configuration BDD non accessible\n\n";
}

echo "========================================\n";
echo "DIAGNOSTIC TERMINÉ\n";
echo "========================================\n\n";

echo "CAUSE PROBABLE:\n";
echo "Le cron système n'est pas configuré sur le serveur.\n";
echo "Les notifications ne peuvent être envoyées automatiquement sans cron.\n\n";

echo "SOLUTION:\n";
echo "1. Configurer le cron système sur app.dietsenegal.net\n";
echo "2. Ajouter la ligne suivante au crontab:\n";
echo "   */5 * * * * php " . BASEPATH . "index.php cron/index\n\n";

echo "Pour plus d'informations:\n";
echo "  - Voir: modules/dietetic/CRON_SETUP.md\n";
echo "  - Ou utiliser: bash modules/dietetic/setup_cron.sh\n";
