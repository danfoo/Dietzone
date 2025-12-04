<?php
/**
 * Script de Diagnostic Push Notifications Firebase
 * Identifie la cause exacte des échecs
 *
 * Exécution: php modules/dietetic/diagnose_push_notifications.php
 * Ou via: /admin/dietetic/setup/diagnose_push
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
echo "DIAGNOSTIC Push Notifications Firebase\n";
echo "========================================\n\n";

// 1. Vérifier les paramètres Firebase
echo "1. Vérification des paramètres Firebase...\n\n";

$firebase_settings = $CI->db->where_in('setting_key', [
    'push_enabled',
    'firebase_server_key',
    'firebase_vapid_key',
    'firebase_project_id',
    'firebase_service_account_json',
    'firebase_use_v1_api'
])->get(db_prefix() . 'dietic_notification_settings')->result();

$settings_map = [];
foreach ($firebase_settings as $setting) {
    $settings_map[$setting->setting_key] = $setting->setting_value;
}

// Status général
$push_enabled = $settings_map['push_enabled'] ?? '0';
echo "   Push Enabled: " . ($push_enabled == '1' ? '✓ OUI' : '✗ NON') . "\n";

// Server Key (Legacy API)
$server_key = $settings_map['firebase_server_key'] ?? '';
$has_server_key = !empty($server_key);
echo "   Server Key (Legacy): " . ($has_server_key ? '✓ Configuré (' . strlen($server_key) . ' chars)' : '✗ Manquant') . "\n";

// VAPID Key
$vapid_key = $settings_map['firebase_vapid_key'] ?? '';
$has_vapid = !empty($vapid_key);
echo "   VAPID Key: " . ($has_vapid ? '✓ Configuré (' . strlen($vapid_key) . ' chars)' : '✗ Manquant') . "\n";

// Project ID
$project_id = $settings_map['firebase_project_id'] ?? '';
$has_project_id = !empty($project_id);
echo "   Project ID: " . ($has_project_id ? "✓ {$project_id}" : '✗ Manquant') . "\n";

// Service Account JSON (v1 API)
$service_account = $settings_map['firebase_service_account_json'] ?? '';
$has_service_account = !empty($service_account) && json_decode($service_account) !== null;
echo "   Service Account JSON: " . ($has_service_account ? '✓ Configuré (' . strlen($service_account) . ' chars)' : '✗ Manquant') . "\n";

// API Version
$use_v1_api = $settings_map['firebase_use_v1_api'] ?? '0';
echo "   API Version: " . ($use_v1_api == '1' ? 'v1 (Modern)' : 'Legacy') . "\n\n";

// 2. Vérifier la librairie Firebase
echo "2. Vérification de la librairie Firebase...\n\n";

$library_path = FCPATH . 'modules/dietetic/libraries/Firebase_cloud_messaging.php';
$library_exists = file_exists($library_path);
echo "   Librairie: " . ($library_exists ? '✓ Existe' : '✗ Manquante') . "\n";

if ($library_exists) {
    try {
        $CI->load->library('dietetic/firebase_cloud_messaging');
        echo "   Chargement: ✓ Succès\n";

        $is_enabled = $CI->firebase_cloud_messaging->is_enabled();
        echo "   is_enabled(): " . ($is_enabled ? '✓ TRUE' : '✗ FALSE') . "\n\n";

        if (!$is_enabled) {
            echo "   ⚠️  PROBLÈME IDENTIFIÉ:\n";
            if ($use_v1_api == '1') {
                echo "      API v1 sélectionnée mais: \n";
                if (!$has_project_id) echo "      - Project ID manquant\n";
                if (!$has_service_account) echo "      - Service Account JSON manquant ou invalide\n";
            } else {
                echo "      API Legacy sélectionnée mais: \n";
                if (!$has_server_key) echo "      - Server Key manquant\n";
            }
            echo "\n";
        }
    } catch (Exception $e) {
        echo "   Chargement: ✗ Échec - " . $e->getMessage() . "\n\n";
    }
} else {
    echo "   ⚠️  Librairie Firebase manquante!\n\n";
}

// 3. Vérifier les tokens FCM des patients
echo "3. Vérification des tokens FCM patients...\n\n";

$total_patients = $CI->db->count_all(db_prefix() . 'dietic_patients');
echo "   Total patients: {$total_patients}\n";

$patients_with_tokens = $CI->db->select('DISTINCT patient_id')
    ->from(db_prefix() . 'dietic_fcm_tokens')
    ->where('is_active', 1)
    ->count_all_results();
echo "   Patients avec tokens FCM actifs: {$patients_with_tokens}\n";

$total_tokens = $CI->db->where('is_active', 1)
    ->count_all_results(db_prefix() . 'dietic_fcm_tokens');
echo "   Total tokens FCM actifs: {$total_tokens}\n\n";

if ($patients_with_tokens == 0) {
    echo "   ⚠️  PROBLÈME IDENTIFIÉ:\n";
    echo "      Aucun patient n'a de token FCM enregistré.\n";
    echo "      Les patients doivent s'inscrire aux notifications push\n";
    echo "      depuis le portail patient (bouton dans l'interface).\n\n";
}

// 4. Vérifier les préférences patients
echo "4. Vérification des préférences patients...\n\n";

$patients_push_enabled = $CI->db->where('channel_push', 1)
    ->count_all_results(db_prefix() . 'dietic_notification_preferences');
echo "   Patients avec push activé: {$patients_push_enabled}\n\n";

if ($patients_push_enabled == 0) {
    echo "   ⚠️  PROBLÈME IDENTIFIÉ:\n";
    echo "      Aucun patient n'a activé les notifications push\n";
    echo "      dans ses préférences.\n\n";
}

// 5. Vérifier les dernières erreurs
echo "5. Dernières erreurs push (5 plus récentes)...\n\n";

$recent_errors = $CI->db->select('created_at, patient_id, error_message')
    ->from(db_prefix() . 'dietic_notification_logs')
    ->where('channel', 'push')
    ->where('status', 'failed')
    ->order_by('created_at', 'DESC')
    ->limit(5)
    ->get()
    ->result();

if (!empty($recent_errors)) {
    foreach ($recent_errors as $error) {
        echo "   [{$error->created_at}] Patient #{$error->patient_id}\n";
        echo "   Erreur: {$error->error_message}\n\n";
    }
} else {
    echo "   ✓ Aucune erreur récente\n\n";
}

// 6. Résumé et recommandations
echo "========================================\n";
echo "RÉSUMÉ ET RECOMMANDATIONS\n";
echo "========================================\n\n";

$issues = [];

if ($push_enabled != '1') {
    $issues[] = "Push désactivé globalement";
}

if ($use_v1_api == '1') {
    if (!$has_project_id || !$has_service_account) {
        $issues[] = "API v1 incomplète (Project ID ou Service Account manquant)";
    }
} else {
    if (!$has_server_key) {
        $issues[] = "Server Key manquant pour Legacy API";
    }
}

if ($patients_with_tokens == 0) {
    $issues[] = "Aucun patient n'a de token FCM";
}

if ($patients_push_enabled == 0) {
    $issues[] = "Aucun patient n'a activé les push";
}

if (empty($issues)) {
    echo "✓ Configuration semble correcte\n\n";
    echo "Testez l'envoi d'une notification de test:\n";
    echo "Admin > Dietetic > Notifications > Test\n\n";
} else {
    echo "⚠️  PROBLÈMES DÉTECTÉS:\n\n";
    foreach ($issues as $i => $issue) {
        echo "   " . ($i + 1) . ". {$issue}\n";
    }
    echo "\n";

    echo "ACTIONS RECOMMANDÉES:\n\n";

    if ($push_enabled != '1') {
        echo "   1. Activer push_enabled:\n";
        echo "      UPDATE tbldietic_notification_settings\n";
        echo "      SET setting_value = '1'\n";
        echo "      WHERE setting_key = 'push_enabled';\n\n";
    }

    if ($use_v1_api == '1' && (!$has_project_id || !$has_service_account)) {
        echo "   2. Configurer API v1 Firebase:\n";
        echo "      - Project ID depuis Firebase Console\n";
        echo "      - Service Account JSON (télécharger depuis Settings > Service Accounts)\n";
        echo "      OU basculer vers Legacy API:\n";
        echo "      UPDATE tbldietic_notification_settings\n";
        echo "      SET setting_value = '0'\n";
        echo "      WHERE setting_key = 'firebase_use_v1_api';\n\n";
    }

    if (!$has_server_key && $use_v1_api != '1') {
        echo "   3. Configurer Server Key (Legacy API):\n";
        echo "      Firebase Console > Project Settings > Cloud Messaging\n";
        echo "      Copier le Server Key\n\n";
    }

    if ($patients_with_tokens == 0) {
        echo "   4. Enregistrer des tokens FCM:\n";
        echo "      Les patients doivent cliquer sur 'Activer notifications'\n";
        echo "      dans le portail patient.\n\n";
    }

    if ($patients_push_enabled == 0) {
        echo "   5. Activer push pour les patients:\n";
        echo "      Portail Patient > Notifications > Préférences\n";
        echo "      OU activer par défaut:\n";
        echo "      UPDATE tbldietic_notification_preferences\n";
        echo "      SET channel_push = 1;\n\n";
    }
}

echo "========================================\n";
echo "Fin du diagnostic\n";
echo "========================================\n";
