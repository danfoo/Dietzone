<?php
/**
 * Page Web de Diagnostic des Notifications Dietetic
 * Accessible via : https://app.dietsenegal.net/modules/dietetic/diagnostic_web.php
 */

define('BASEPATH', dirname(__DIR__, 2) . '/');

if (!file_exists(BASEPATH . 'index.php')) {
    die("❌ Erreur: Perfex CRM non trouvé");
}

if (file_exists(BASEPATH . 'application/config/app-config.php')) {
    require_once(BASEPATH . 'application/config/app-config.php');
}

$db_host = defined('APP_DB_HOSTNAME') ? APP_DB_HOSTNAME : 'localhost';
$db_user = defined('APP_DB_USERNAME') ? APP_DB_USERNAME : '';
$db_pass = defined('APP_DB_PASSWORD') ? APP_DB_PASSWORD : '';
$db_name = defined('APP_DB_NAME') ? APP_DB_NAME : '';
$db_prefix = defined('APP_DB_PREFIX') ? APP_DB_PREFIX : 'tbl';

$mysqli = @new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($mysqli->connect_error) {
    die("❌ Erreur de connexion à la base de données");
}

echo "Diagnostic chargé avec succès!";
$mysqli->close();
?>
