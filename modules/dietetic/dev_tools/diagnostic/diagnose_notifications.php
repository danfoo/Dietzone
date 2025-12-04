<?php
/**
 * Script de diagnostic complet pour les notifications Dietetic
 * À exécuter sur le serveur : php diagnose_notifications.php
 */

define('BASEPATH', dirname(__DIR__, 2) . '/');

// Load Perfex
if (!file_exists(BASEPATH . 'index.php')) {
    die("❌ Erreur: Perfex CRM non trouvé dans " . BASEPATH . "\n");
}

// Bootstrap Perfex CRM
$_SERVER['CI_ENV'] = 'production';
require_once(BASEPATH . 'index.php');

// Get CI instance
$CI =& get_instance();

echo "========================================\n";
echo "DIAGNOSTIC COMPLET NOTIFICATIONS DIETETIC\n";
echo "========================================\n\n";

$errors = [];
$warnings = [];
$success = [];

// ==================== 1. VÉRIFICATION MODULE ====================
echo "1️⃣  VÉRIFICATION DU MODULE DIETETIC\n";
echo "-----------------------------------\n";

// Check if module exists
if (file_exists(BASEPATH . 'modules/dietetic/dietetic.php')) {
    $success[] = "Module Dietetic trouvé";
    echo "✓ Module installé\n";

    // Check if module is activated
    $CI->db->where('module_name', 'dietetic');
    $module = $CI->db->get(db_prefix() . 'modules')->row();

    if ($module && $module->active == 1) {
        $success[] = "Module Dietetic activé";
        echo "✓ Module activé\n";
    } else {
        $errors[] = "Module Dietetic NON ACTIVÉ dans Admin > Modules";
        echo "❌ Module NON activé\n";
        echo "   → Allez dans Admin > Modules\n";
        echo "   → Activez le module Dietetic\n";
    }

    // Check if hook is registered
    $hook_file = BASEPATH . 'modules/dietetic/dietetic.php';
    $hook_content = file_get_contents($hook_file);

    if (strpos($hook_content, "hooks()->add_action('after_cron_run', 'dietetic_send_scheduled_reminders')") !== false) {
        $success[] = "Hook after_cron_run enregistré";
        echo "✓ Hook cron enregistré\n";
    } else {
        $errors[] = "Hook after_cron_run NON trouvé dans dietetic.php";
        echo "❌ Hook cron NON enregistré\n";
    }

} else {
    $errors[] = "Module Dietetic non installé";
    echo "❌ Module non trouvé\n";
}

echo "\n";

// ==================== 2. VÉRIFICATION BASE DE DONNÉES ====================
echo "2️⃣  VÉRIFICATION BASE DE DONNÉES\n";
echo "--------------------------------\n";

$CI->load->model('dietetic/dietetic_notifications_model');

// Check tables
$tables = [
    'dietic_notification_preferences',
    'dietic_patient_notifications',
    'dietic_notification_settings'
];

foreach ($tables as $table) {
    if ($CI->db->table_exists(db_prefix() . $table)) {
        $success[] = "Table $table existe";
        echo "✓ Table $table\n";
    } else {
        $errors[] = "Table $table manquante";
        echo "❌ Table $table manquante\n";
    }
}

echo "\n";

// ==================== 3. VÉRIFICATION PRÉFÉRENCES PATIENT ====================
echo "3️⃣  VÉRIFICATION PRÉFÉRENCES PATIENTS\n";
echo "------------------------------------\n";

// Get total patients
$CI->db->from(db_prefix() . 'dietic_patients');
$total_patients = $CI->db->count_all_results();
echo "Total patients: $total_patients\n";

// Get patients with preferences
$CI->db->from(db_prefix() . 'dietic_notification_preferences');
$total_prefs = $CI->db->count_all_results();
echo "Avec préférences: $total_prefs\n";

if ($total_patients > 0 && $total_prefs == 0) {
    $warnings[] = "Aucun patient n'a de préférences de notification";
    echo "⚠️  Aucune préférence configurée\n";
} elseif ($total_prefs > 0) {
    $success[] = "$total_prefs patients avec préférences";
    echo "✓ Préférences configurées\n";
}

// Get preferences with dinner reminder enabled
$query = "SELECT p.*, c.firstname, c.lastname, c.email, c.phonenumber
          FROM " . db_prefix() . "dietic_notification_preferences p
          INNER JOIN " . db_prefix() . "dietic_patients dp ON p.patient_id = dp.id
          INNER JOIN " . db_prefix() . "contacts c ON dp.contact_id = c.id
          WHERE p.reminder_dinner = 1
          LIMIT 5";

$result = $CI->db->query($query);

if ($result->num_rows() > 0) {
    echo "\n📋 Patients avec rappel dîner activé:\n";

    foreach ($result->result() as $pref) {
        echo "\n  👤 {$pref->firstname} {$pref->lastname}\n";
        echo "     Patient ID: {$pref->patient_id}\n";
        echo "     Heure dîner: {$pref->reminder_dinner_time}\n";
        echo "     Canaux actifs: ";

        $channels = [];
        if ($pref->channel_email) $channels[] = "Email";
        if ($pref->channel_sms) $channels[] = "SMS";
        if ($pref->channel_whatsapp) $channels[] = "WhatsApp";

        if (empty($channels)) {
            echo "❌ AUCUN\n";
            $warnings[] = "Patient {$pref->patient_id}: Aucun canal activé";
        } else {
            echo implode(", ", $channels) . "\n";
        }

        echo "     Email: " . ($pref->email ?: "❌ NON RENSEIGNÉ") . "\n";
        echo "     Téléphone: " . ($pref->phonenumber ?: "❌ NON RENSEIGNÉ") . "\n";

        // Check if would be eligible now
        $current_time = date('H:i');
        $dinner_time = substr($pref->reminder_dinner_time, 0, 5);

        if ($current_time > $dinner_time) {
            $time_diff = strtotime($current_time) - strtotime($dinner_time);
            $minutes = floor($time_diff / 60);
            echo "     ⏰ Il est passé {$dinner_time} de {$minutes} minutes\n";
            echo "     → Devrait avoir reçu notification si cron fonctionne\n";
        }
    }
} else {
    $warnings[] = "Aucun patient n'a le rappel dîner activé";
    echo "⚠️  Aucun patient avec rappel dîner\n";
}

echo "\n";

// ==================== 4. VÉRIFICATION CONFIGURATION CANAUX ====================
echo "4️⃣  VÉRIFICATION CONFIGURATION CANAUX\n";
echo "------------------------------------\n";

$CI->load->model('dietetic/dietetic_settings_model');

// Email (use Perfex settings)
$email_configured = get_option('smtp_host') && get_option('smtp_username');
if ($email_configured) {
    $success[] = "Email (SMTP) configuré";
    echo "✓ Email: SMTP configuré\n";
} else {
    $warnings[] = "Email: SMTP non configuré";
    echo "⚠️  Email: SMTP non configuré\n";
}

// SMS
$sms_account = $CI->dietetic_settings_model->get_setting('sms_lam_account_id');
$sms_password = $CI->dietetic_settings_model->get_setting('sms_lam_password');

if ($sms_account && $sms_password) {
    $success[] = "SMS (LAM) configuré";
    echo "✓ SMS: LAM configuré (Account: $sms_account)\n";
} else {
    $warnings[] = "SMS: LAM non configuré";
    echo "⚠️  SMS: LAM non configuré\n";
}

// WhatsApp
$whatsapp_key = $CI->dietetic_settings_model->get_setting('whatsapp_api_key');

if ($whatsapp_key) {
    $success[] = "WhatsApp configuré";
    echo "✓ WhatsApp: API Key configurée\n";
} else {
    $warnings[] = "WhatsApp: API Key non configurée";
    echo "⚠️  WhatsApp: API Key non configurée\n";
}

echo "\n";

// ==================== 5. VÉRIFICATION LOGS RÉCENTS ====================
echo "5️⃣  LOGS DES NOTIFICATIONS\n";
echo "-------------------------\n";

$query = "SELECT * FROM " . db_prefix() . "dietic_patient_notifications
          ORDER BY created_at DESC
          LIMIT 10";

$logs = $CI->db->query($query);

if ($logs->num_rows() > 0) {
    echo "Dernières notifications:\n\n";

    foreach ($logs->result() as $log) {
        $status_icon = $log->status == 'sent' ? '✓' : '✗';
        echo "  $status_icon {$log->created_at} - {$log->type} ({$log->channel}) - {$log->status}\n";
    }

    $success[] = "Des notifications ont été envoyées";
} else {
    $warnings[] = "Aucune notification dans les logs";
    echo "⚠️  Aucune notification enregistrée\n";
    echo "   → Le système n'a jamais envoyé de notification\n";
}

echo "\n";

// ==================== 6. TEST FONCTION D'ENVOI ====================
echo "6️⃣  TEST FONCTION D'ENVOI\n";
echo "------------------------\n";

try {
    // Try to get patients for dinner reminder NOW
    $current_hour = (int)date('H');
    $current_minute = (int)date('i');
    $current_time = sprintf('%02d:%02d:00', $current_hour, $current_minute);

    echo "Heure actuelle: $current_time\n";

    $meal_patients = $CI->dietetic_notifications_model->get_patients_for_meal_reminder('dinner');

    if (!empty($meal_patients)) {
        echo "✓ Fonction get_patients_for_meal_reminder() fonctionne\n";
        echo "  Patients éligibles MAINTENANT: " . count($meal_patients) . "\n";

        foreach ($meal_patients as $patient) {
            echo "    - {$patient->firstname} {$patient->lastname}\n";
        }

        $success[] = "Fonction d'envoi opérationnelle";
    } else {
        echo "ℹ️  Aucun patient éligible à cette heure\n";
        echo "   (Les rappels sont envoyés à l'heure configurée ± 5 min)\n";
    }

} catch (Exception $e) {
    $errors[] = "Erreur fonction d'envoi: " . $e->getMessage();
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}

echo "\n";

// ==================== RÉSUMÉ ====================
echo "========================================\n";
echo "RÉSUMÉ DU DIAGNOSTIC\n";
echo "========================================\n\n";

if (!empty($success)) {
    echo "✅ POINTS POSITIFS (" . count($success) . "):\n";
    foreach ($success as $item) {
        echo "  ✓ $item\n";
    }
    echo "\n";
}

if (!empty($warnings)) {
    echo "⚠️  AVERTISSEMENTS (" . count($warnings) . "):\n";
    foreach ($warnings as $item) {
        echo "  ⚠  $item\n";
    }
    echo "\n";
}

if (!empty($errors)) {
    echo "❌ ERREURS CRITIQUES (" . count($errors) . "):\n";
    foreach ($errors as $item) {
        echo "  ✗ $item\n";
    }
    echo "\n";
}

// ==================== RECOMMANDATIONS ====================
echo "========================================\n";
echo "RECOMMANDATIONS\n";
echo "========================================\n\n";

if (!empty($errors)) {
    echo "🔴 ACTIONS URGENTES:\n\n";

    foreach ($errors as $error) {
        if (strpos($error, 'NON ACTIVÉ') !== false) {
            echo "1. ACTIVER LE MODULE DIETETIC\n";
            echo "   → https://app.dietsenegal.net/admin/modules\n";
            echo "   → Cliquez sur \"Activate\" pour le module Dietetic\n\n";
        }

        if (strpos($error, 'Hook') !== false) {
            echo "2. VÉRIFIER LE FICHIER HOOK\n";
            echo "   → /home/trpuftja/app/modules/dietetic/dietetic.php\n";
            echo "   → Doit contenir: hooks()->add_action('after_cron_run', ...)\n\n";
        }
    }
}

if (!empty($warnings)) {
    echo "🟡 À VÉRIFIER:\n\n";

    foreach ($warnings as $warning) {
        if (strpos($warning, 'Aucun canal') !== false) {
            echo "• Activez au moins un canal (Email/SMS/WhatsApp)\n";
            echo "  → https://app.dietsenegal.net/dietetic/portal/notification_preferences\n\n";
        }

        if (strpos($warning, 'préférences') !== false && strpos($warning, 'Aucun') !== false) {
            echo "• Configurez vos préférences de notification\n";
            echo "  → https://app.dietsenegal.net/dietetic/portal/notification_preferences\n\n";
        }

        if (strpos($warning, 'SMTP') !== false) {
            echo "• Configurez SMTP pour les emails\n";
            echo "  → Setup > Settings > Email\n\n";
        }
    }
}

if (empty($errors) && !empty($warnings)) {
    echo "✅ Le système est fonctionnel mais nécessite de la configuration.\n\n";
}

if (empty($errors) && empty($warnings)) {
    echo "✅ Tout semble correct !\n\n";
    echo "Si vous ne recevez toujours pas de notifications:\n";
    echo "1. Vérifiez que l'heure de rappel est bien configurée\n";
    echo "2. Attendez le prochain passage du cron (± 5 min après l'heure)\n";
    echo "3. Vérifiez vos spams/indésirables\n";
    echo "4. Consultez les logs: Admin > Dietetic > Notifications > Logs\n\n";
}

echo "========================================\n";
echo "FIN DU DIAGNOSTIC\n";
echo "========================================\n";
