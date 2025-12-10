<?php
// Diagnostic script - Load Perfex
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Navigate to Perfex root
$perfex_root = dirname(dirname(__DIR__));
chdir($perfex_root);

// Define constants
define('FCPATH', $perfex_root . '/');
defined('BASEPATH') OR define('BASEPATH', $perfex_root . '/application/');

// Load Perfex config
require_once(FCPATH . 'application/config/app-config.php');
require_once(FCPATH . 'application/config/config.php');

// Database config
$db_config_file = FCPATH . 'application/config/app-config.php';
if (file_exists($db_config_file)) {
    require_once($db_config_file);
}

// Connect to database directly
try {
    $db = new PDO(
        "mysql:host=" . (defined('APP_DB_HOSTNAME') ? APP_DB_HOSTNAME : 'localhost') . ";dbname=" . (defined('APP_DB_NAME') ? APP_DB_NAME : ''),
        defined('APP_DB_USERNAME') ? APP_DB_USERNAME : '',
        defined('APP_DB_PASSWORD') ? APP_DB_PASSWORD : ''
    );
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->exec("SET NAMES utf8");
} catch (PDOException $e) {
    die("❌ Erreur connexion DB: " . $e->getMessage());
}

$prefix = defined('APP_DB_PREFIX') ? APP_DB_PREFIX : 'tbl';

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Diagnostic Téléphones</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #f5f5f5; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; background: white; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 13px; }
        th { background-color: #01807B; color: white; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .warning { color: red; font-weight: bold; }
        .success { color: green; font-weight: bold; }
        .info { background: #d1ecf1; padding: 15px; margin: 20px 0; border-left: 4px solid #0c5460; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-size: 12px; }
        h1, h2, h3 { color: #01807B; }
    </style>
</head>
<body>
    <h1>🔍 Diagnostic Numéros de Téléphone</h1>

    <?php
    // 1. Où sont les numéros ?
    echo "<h2>1️⃣ Localisation des numéros</h2>";

    $stmt = $db->query("SELECT COUNT(*) as count FROM {$prefix}clients WHERE phonenumber IS NOT NULL AND phonenumber != ''");
    $client_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "<p>📊 <strong>tblclients.phonenumber:</strong> {$client_count} clients avec numéro</p>";

    $stmt = $db->query("SELECT COUNT(*) as count FROM {$prefix}contacts WHERE phonenumber IS NOT NULL AND phonenumber != '' AND is_primary = 1");
    $contact_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "<p>📊 <strong>tblcontacts.phonenumber:</strong> {$contact_count} contacts (primary) avec numéro</p>";

    echo "<div class='info'>";
    if ($client_count > $contact_count) {
        echo "⚠️ <strong>IMPORTANT:</strong> Les numéros sont majoritairement dans <code>tblclients</code>, pas dans <code>tblcontacts</code> !<br>";
        echo "Le code Portal.php doit être modifié pour chercher dans <code>tblclients.phonenumber</code> au lieu de <code>tblcontacts.phonenumber</code>";
    } else if ($contact_count > 0) {
        echo "✅ Les numéros sont dans <code>tblcontacts.phonenumber</code> (correct)";
    } else {
        echo "⚠️ Aucun numéro trouvé dans les deux tables !";
    }
    echo "</div>";

    // 2. Liste des patients
    echo "<h2>2️⃣ Les 20 derniers patients</h2>";

    $query = "
        SELECT
            p.id as patient_id,
            p.client_id,
            c.phonenumber as client_phone,
            ct.firstname,
            ct.lastname,
            ct.email,
            ct.phonenumber as contact_phone,
            LENGTH(ct.password) as password_length
        FROM {$prefix}dietic_patients p
        LEFT JOIN {$prefix}clients c ON c.userid = p.client_id
        LEFT JOIN {$prefix}contacts ct ON ct.userid = p.client_id AND ct.is_primary = 1
        ORDER BY p.id DESC
        LIMIT 20
    ";

    $stmt = $db->query($query);
    $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<table>";
    echo "<tr>
        <th>ID</th>
        <th>Nom</th>
        <th>Email</th>
        <th>📱 CLIENT.phonenumber</th>
        <th>📱 CONTACT.phonenumber</th>
        <th>Password?</th>
    </tr>";

    foreach ($patients as $row) {
        $name = trim($row['firstname'] . ' ' . $row['lastname']);
        $client_phone = $row['client_phone'] ?: '<span class="warning">VIDE</span>';
        $contact_phone = $row['contact_phone'] ?: '<span class="warning">VIDE</span>';
        $password = $row['password_length'] > 0 ? "<span class='success'>✓</span>" : "<span class='warning'>✗</span>";

        echo "<tr>";
        echo "<td>{$row['patient_id']}</td>";
        echo "<td><strong>{$name}</strong></td>";
        echo "<td><code>{$row['email']}</code></td>";
        echo "<td>{$client_phone}</td>";
        echo "<td>{$contact_phone}</td>";
        echo "<td>{$password}</td>";
        echo "</tr>";
    }

    echo "</table>";

    // 3. Formulaire test
    echo "<h2>3️⃣ Tester la recherche</h2>";
    echo "<form method='GET' style='margin: 20px 0;'>";
    echo "<input type='text' name='test_phone' placeholder='Entrez un numéro (ex: +221771234567)' style='padding: 10px; width: 400px;'>";
    echo "<button type='submit' style='padding: 10px 20px; background: #01807B; color: white; border: none; cursor: pointer;'>🔍 Tester</button>";
    echo "</form>";

    if (!empty($_GET['test_phone'])) {
        $test_phone = $_GET['test_phone'];
        echo "<div class='info'><strong>Test avec:</strong> <code>{$test_phone}</code></div>";

        // Test dans CONTACTS (code actuel Portal)
        echo "<h3>A) Recherche dans tblcontacts (code actuel):</h3>";
        $sql = "
            SELECT ct.email, ct.phonenumber, ct.firstname, ct.lastname, ct.userid
            FROM {$prefix}contacts ct
            JOIN {$prefix}dietic_patients p ON p.client_id = ct.userid
            WHERE ct.is_primary = 1 AND ct.phonenumber = :phone
        ";
        echo "<code style='display: block; padding: 10px; background: #2d2d2d; color: #fff;'>{$sql}</code>";

        $stmt = $db->prepare($sql);
        $stmt->execute([':phone' => $test_phone]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            echo "<p class='success'>✅ TROUVÉ dans contacts !</p>";
            echo "<p>Nom: <strong>{$result['firstname']} {$result['lastname']}</strong> - Email: <code>{$result['email']}</code></p>";
        } else {
            echo "<p class='warning'>❌ NON TROUVÉ dans contacts</p>";
        }

        // Test dans CLIENTS
        echo "<h3>B) Recherche dans tblclients:</h3>";
        $sql2 = "
            SELECT c.userid, c.company, c.phonenumber, ct.email, ct.firstname, ct.lastname
            FROM {$prefix}clients c
            LEFT JOIN {$prefix}contacts ct ON ct.userid = c.userid AND ct.is_primary = 1
            JOIN {$prefix}dietic_patients p ON p.client_id = c.userid
            WHERE c.phonenumber = :phone
        ";
        echo "<code style='display: block; padding: 10px; background: #2d2d2d; color: #fff;'>{$sql2}</code>";

        $stmt2 = $db->prepare($sql2);
        $stmt2->execute([':phone' => $test_phone]);
        $result2 = $stmt2->fetch(PDO::FETCH_ASSOC);

        if ($result2) {
            echo "<p class='success'>✅ TROUVÉ dans clients !</p>";
            echo "<p>Nom: <strong>{$result2['firstname']} {$result2['lastname']}</strong> - Email: <code>{$result2['email']}</code></p>";
            echo "<div class='info'>💡 <strong>Solution:</strong> Le code Portal.php doit chercher dans <code>tblclients.phonenumber</code> au lieu de <code>tblcontacts.phonenumber</code></div>";
        } else {
            echo "<p class='warning'>❌ NON TROUVÉ dans clients</p>";

            // Recherche approximative
            echo "<p>Recherche approximative...</p>";
            $phone_clean = preg_replace('/[^0-9+]/', '', $test_phone);
            $stmt3 = $db->prepare("SELECT phonenumber, firstname, lastname FROM {$prefix}contacts WHERE phonenumber LIKE :phone LIMIT 5");
            $stmt3->execute([':phone' => "%{$phone_clean}%"]);
            $similar = $stmt3->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($similar)) {
                echo "<p class='success'>Numéros similaires trouvés:</p><ul>";
                foreach ($similar as $s) {
                    echo "<li><code>{$s['phonenumber']}</code> - {$s['firstname']} {$s['lastname']}</li>";
                }
                echo "</ul>";
            }
        }
    }

    // 4. Doublons
    echo "<h2>4️⃣ Doublons</h2>";
    $stmt = $db->query("
        SELECT phonenumber, COUNT(*) as count
        FROM {$prefix}contacts
        WHERE phonenumber IS NOT NULL AND phonenumber != '' AND is_primary = 1
        GROUP BY phonenumber
        HAVING count > 1
    ");
    $duplicates = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($duplicates)) {
        echo "<p class='success'>✅ Aucun doublon</p>";
    } else {
        echo "<table><tr><th>Numéro</th><th>Occurrences</th></tr>";
        foreach ($duplicates as $dup) {
            echo "<tr><td class='warning'><code>{$dup['phonenumber']}</code></td><td>{$dup['count']}</td></tr>";
        }
        echo "</table>";
    }
    ?>
</body>
</html>
