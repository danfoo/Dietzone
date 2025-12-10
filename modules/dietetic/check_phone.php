<?php
// Script de vérification rapide - quel numéro chercher ?
error_reporting(E_ALL);
ini_set('display_errors', 1);

$perfex_root = dirname(dirname(__DIR__));
require_once($perfex_root . '/application/config/app-config.php');

try {
    $db = new PDO(
        "mysql:host=" . APP_DB_HOSTNAME . ";dbname=" . APP_DB_NAME,
        APP_DB_USERNAME,
        APP_DB_PASSWORD
    );
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur DB: " . $e->getMessage());
}

$prefix = APP_DB_PREFIX;

echo "<h1>Vérification numéros</h1>";
echo "<p>Entrez votre numéro pour voir s'il existe dans la base:</p>";
echo "<form method='GET'>";
echo "<input type='text' name='phone' value='" . ($_GET['phone'] ?? '') . "' placeholder='+221771234567' style='padding:10px;width:300px;'>";
echo "<button type='submit' style='padding:10px;'>Chercher</button>";
echo "</form><hr>";

if (!empty($_GET['phone'])) {
    $search = $_GET['phone'];
    echo "<h2>Recherche pour: <code>{$search}</code></h2>";

    // Nettoyer comme dans Portal.php
    $phone = preg_replace('/[\s\-\(\)]/', '', $search);
    if (!strpos($phone, '+') === 0) {
        $phone = '+' . $phone;
    }
    echo "<p>Après nettoyage: <code>{$phone}</code></p>";

    $phone_no_plus = ltrim($phone, '+');
    echo "<p>Sans +: <code>{$phone_no_plus}</code></p><hr>";

    // TOUS les numéros dans contacts
    echo "<h3>Tous les numéros dans tblcontacts (is_primary=1):</h3>";
    $stmt = $db->query("SELECT phonenumber, email, firstname, lastname FROM {$prefix}contacts WHERE is_primary=1 AND phonenumber IS NOT NULL AND phonenumber != '' LIMIT 50");
    $all_contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<table border='1' style='border-collapse:collapse;'><tr><th>Phone</th><th>Email</th><th>Nom</th></tr>";
    foreach ($all_contacts as $c) {
        $highlight = ($c['phonenumber'] == $phone || $c['phonenumber'] == $phone_no_plus || $c['phonenumber'] == $search) ? 'background:yellow;' : '';
        echo "<tr style='{$highlight}'><td><code>{$c['phonenumber']}</code></td><td>{$c['email']}</td><td>{$c['firstname']} {$c['lastname']}</td></tr>";
    }
    echo "</table><hr>";

    // TOUS les numéros dans clients
    echo "<h3>Tous les numéros dans tblclients:</h3>";
    $stmt = $db->query("SELECT c.phonenumber, c.company, ct.email, ct.firstname, ct.lastname FROM {$prefix}clients c LEFT JOIN {$prefix}contacts ct ON ct.userid=c.userid AND ct.is_primary=1 WHERE c.phonenumber IS NOT NULL AND c.phonenumber != '' LIMIT 50");
    $all_clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<table border='1' style='border-collapse:collapse;'><tr><th>Phone</th><th>Email</th><th>Nom</th></tr>";
    foreach ($all_clients as $c) {
        $highlight = ($c['phonenumber'] == $phone || $c['phonenumber'] == $phone_no_plus || $c['phonenumber'] == $search) ? 'background:yellow;' : '';
        echo "<tr style='{$highlight}'><td><code>{$c['phonenumber']}</code></td><td>{$c['email']}</td><td>{$c['firstname']} {$c['lastname']}</td></tr>";
    }
    echo "</table><hr>";

    // Logs récents
    echo "<h3>Dernières tentatives LOGIN MOBILE:</h3>";
    $stmt = $db->query("SELECT date, description FROM {$prefix}activity_log WHERE description LIKE '%LOGIN MOBILE%' ORDER BY date DESC LIMIT 10");
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>";
    foreach ($logs as $log) {
        echo "[{$log['date']}] {$log['description']}\n";
    }
    echo "</pre>";
}
?>
