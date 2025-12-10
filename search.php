<?php
// Script PHP pur - pas de CodeIgniter
define('BASEPATH', true);
require_once(__DIR__ . '/application/config/app-config.php');

$conn = mysqli_connect(APP_DB_HOSTNAME, APP_DB_USERNAME, APP_DB_PASSWORD, APP_DB_NAME);
if (!$conn) die("Erreur connexion: " . mysqli_connect_error());

$phone = $_GET['phone'] ?? '+221775003371';
$phone_no_plus = ltrim($phone, '+');
?>
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Recherche</title></head>
<body style="font-family:monospace;padding:20px;background:#f5f5f5;">

<h1>Recherche: <?= htmlspecialchars($phone) ?></h1>
<p>Variations: <code><?= htmlspecialchars($phone) ?></code> et <code><?= htmlspecialchars($phone_no_plus) ?></code></p>
<hr>

<h2>1. Dans tblclients</h2>
<?php
$sql = "SELECT userid, company, phonenumber FROM tblclients
        WHERE phonenumber = '$phone' OR phonenumber = '$phone_no_plus'";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        echo '<div style="background:yellow;padding:15px;border:3px solid green;margin:10px 0;">';
        echo '<h3 style="color:green;margin:0;">✅ TROUVÉ!</h3>';
        echo 'User ID: <strong>' . $row['userid'] . '</strong><br>';
        echo 'Company: ' . htmlspecialchars($row['company']) . '<br>';
        echo 'Phone: <code>' . htmlspecialchars($row['phonenumber']) . '</code>';
        echo '</div>';
    }
} else {
    echo '<p style="color:red;font-weight:bold;">❌ Pas trouvé dans tblclients</p>';
}
?>

<h2>2. Dans tblcontacts</h2>
<?php
$sql = "SELECT id, userid, firstname, lastname, email, phonenumber, is_primary
        FROM tblcontacts
        WHERE phonenumber = '$phone' OR phonenumber = '$phone_no_plus'";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        echo '<div style="background:yellow;padding:15px;border:3px solid green;margin:10px 0;">';
        echo '<h3 style="color:green;margin:0;">✅ TROUVÉ!</h3>';
        echo 'Contact ID: ' . $row['id'] . '<br>';
        echo 'User ID: <strong>' . $row['userid'] . '</strong><br>';
        echo 'Nom: ' . htmlspecialchars($row['firstname'] . ' ' . $row['lastname']) . '<br>';
        echo 'Email: ' . htmlspecialchars($row['email']) . '<br>';
        echo 'Phone: <code>' . htmlspecialchars($row['phonenumber']) . '</code><br>';
        echo 'Primary: ' . ($row['is_primary'] ? 'Oui' : 'Non');
        echo '</div>';
    }
} else {
    echo '<p style="color:red;font-weight:bold;">❌ Pas trouvé dans tblcontacts</p>';
}
?>

<hr>
<h2>3. Exemples de numéros (10 premiers)</h2>

<h3>tblclients:</h3>
<ul>
<?php
$result = mysqli_query($conn, "SELECT phonenumber, company FROM tblclients WHERE phonenumber IS NOT NULL AND phonenumber != '' LIMIT 10");
if ($result && mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        echo '<li><code>' . htmlspecialchars($row['phonenumber']) . '</code> - ' . htmlspecialchars($row['company']) . '</li>';
    }
} else {
    echo '<li style="color:red;font-weight:bold;">⚠️ AUCUN numéro!</li>';
}
?>
</ul>

<h3>tblcontacts:</h3>
<ul>
<?php
$result = mysqli_query($conn, "SELECT phonenumber, firstname, lastname FROM tblcontacts WHERE phonenumber IS NOT NULL AND phonenumber != '' LIMIT 10");
if ($result && mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        echo '<li><code>' . htmlspecialchars($row['phonenumber']) . '</code> - ' . htmlspecialchars($row['firstname'] . ' ' . $row['lastname']) . '</li>';
    }
} else {
    echo '<li style="color:red;font-weight:bold;">⚠️ AUCUN numéro!</li>';
}
?>
</ul>

</body>
</html>
<?php mysqli_close($conn); ?>
