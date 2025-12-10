<?php
require_once(__DIR__ . '/application/config/app-config.php');

$conn = new mysqli(APP_DB_HOSTNAME, APP_DB_USERNAME, APP_DB_PASSWORD, APP_DB_NAME);
if ($conn->connect_error) die("Connexion échouée");

$phone = $_GET['phone'] ?? '+221775003371';
$variations = [
    $phone,
    ltrim($phone, '+'),
    preg_replace('/[^0-9]/', '', $phone),
    str_replace('+', '', $phone)
];

echo "<h1>Recherche: $phone</h1>";
echo "<h2>Variations: " . implode(', ', $variations) . "</h2><hr>";

// 1. CLIENTS
echo "<h3>1. tblclients:</h3>";
$sql = "SELECT userid, company, phonenumber FROM tblclients WHERE phonenumber IN ('" . implode("','", $variations) . "')";
echo "<code>$sql</code><br>";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<div style='background:yellow;padding:10px;'>";
        echo "✅ TROUVÉ! User ID: {$row['userid']}, Company: {$row['company']}, Phone: {$row['phonenumber']}";
        echo "</div>";
    }
} else {
    echo "❌ Rien<br>";
}

// 2. CONTACTS
echo "<h3>2. tblcontacts:</h3>";
$sql = "SELECT id, userid, firstname, lastname, email, phonenumber FROM tblcontacts WHERE phonenumber IN ('" . implode("','", $variations) . "')";
echo "<code>$sql</code><br>";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<div style='background:yellow;padding:10px;'>";
        echo "✅ TROUVÉ! Contact ID: {$row['id']}, User ID: {$row['userid']}, Nom: {$row['firstname']} {$row['lastname']}, Email: {$row['email']}, Phone: {$row['phonenumber']}";
        echo "</div>";
    }
} else {
    echo "❌ Rien<br>";
}

// 3. TOUS LES NUMÉROS
echo "<hr><h3>3. TOUS les numéros (20 premiers):</h3>";
echo "<h4>tblclients:</h4>";
$result = $conn->query("SELECT phonenumber, company FROM tblclients WHERE phonenumber IS NOT NULL AND phonenumber != '' LIMIT 20");
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "{$row['phonenumber']} - {$row['company']}<br>";
    }
} else {
    echo "<strong style='color:red;'>⚠️ AUCUN numéro dans tblclients!</strong><br>";
}

echo "<h4>tblcontacts:</h4>";
$result = $conn->query("SELECT phonenumber, firstname, lastname FROM tblcontacts WHERE phonenumber IS NOT NULL AND phonenumber != '' LIMIT 20");
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "{$row['phonenumber']} - {$row['firstname']} {$row['lastname']}<br>";
    }
} else {
    echo "<strong style='color:red;'>⚠️ AUCUN numéro dans tblcontacts!</strong><br>";
}

$conn->close();
?>
