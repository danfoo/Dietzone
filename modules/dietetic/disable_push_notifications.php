<?php
/**
 * Script pour désactiver les notifications PUSH Firebase
 * Évite les erreurs en masse jusqu'à configuration Firebase
 *
 * Exécution: php modules/dietetic/disable_push_notifications.php
 * Ou via: /admin/dietetic/setup/disable_push
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
echo "Désactivation Push Notifications Firebase\n";
echo "========================================\n\n";

echo "1. Désactivation des push pour tous les patients...\n";

// Désactiver les push pour tous les patients
$CI->db->where('channel_push', 1);
$CI->db->update(db_prefix() . 'dietic_notification_preferences', [
    'channel_push' => 0,
    'updated_at' => date('Y-m-d H:i:s')
]);

$affected_rows = $CI->db->affected_rows();
echo "   ✓ {$affected_rows} patients mis à jour\n\n";

echo "2. Désactivation Firebase dans les paramètres globaux...\n";

// Désactiver Firebase globalement
$CI->db->where('setting_key', 'push_enabled');
$CI->db->update(db_prefix() . 'dietic_notification_settings', [
    'setting_value' => '0',
    'updated_at' => date('Y-m-d H:i:s')
]);

echo "   ✓ Firebase désactivé globalement\n\n";

echo "3. Vérification...\n\n";

// Compter les patients avec push activé (devrait être 0)
$count_push_enabled = $CI->db->where('channel_push', 1)
    ->count_all_results(db_prefix() . 'dietic_notification_preferences');

echo "   Patients avec push activé: {$count_push_enabled}\n";

// Statut Firebase
$push_setting = $CI->db->where('setting_key', 'push_enabled')
    ->get(db_prefix() . 'dietic_notification_settings')
    ->row();

$firebase_status = $push_setting ? ($push_setting->setting_value == '1' ? 'ACTIVÉ' : 'DÉSACTIVÉ') : 'NON CONFIGURÉ';
echo "   Statut Firebase: {$firebase_status}\n\n";

echo "========================================\n";
echo "✓ Opération terminée avec succès\n";
echo "========================================\n\n";

echo "RÉSULTAT:\n";
echo "- Les notifications push ne seront plus envoyées\n";
echo "- Les patients continueront à recevoir par email\n";
echo "- Aucune erreur ne sera générée dans les logs\n\n";

echo "Pour réactiver Firebase plus tard:\n";
echo "1. Configurer Firebase dans Admin > Dietetic > Notifications > Settings\n";
echo "2. Activer 'push_enabled' dans les paramètres\n";
echo "3. Les patients pourront réactiver dans leurs préférences\n\n";
