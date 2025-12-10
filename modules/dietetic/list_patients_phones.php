<?php
// Diagnostic script - Direct database access
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Navigate to Perfex root
$perfex_root = dirname(dirname(__DIR__));
chdir($perfex_root);

// Define all required constants
define('FCPATH', $perfex_root . '/');
define('APPPATH', $perfex_root . '/application/');
defined('BASEPATH') OR define('BASEPATH', APPPATH);

// Load app-config directly for database credentials
$app_config_file = APPPATH . 'config/app-config.php';
if (file_exists($app_config_file)) {
    require_once($app_config_file);
} else {
    die("❌ Fichier app-config.php introuvable");
}

// Connect to database using PDO
try {
    $dsn = "mysql:host=" . APP_DB_HOSTNAME . ";dbname=" . APP_DB_NAME . ";charset=utf8";
    $db = new PDO($dsn, APP_DB_USERNAME, APP_DB_PASSWORD);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Erreur connexion DB: " . $e->getMessage());
}

$prefix = APP_DB_PREFIX;

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
        .sql-box { display: block; padding: 10px; background: #2d2d2d; color: #fff; overflow-x: auto; margin: 10px 0; }
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
        echo "⚠️ <strong>PROBLÈME IDENTIFIÉ:</strong> Les numéros sont dans <code>tblclients.phonenumber</code> ({$client_count}), pas dans <code>tblcontacts.phonenumber</code> ({$contact_count}) !<br><br>";
        echo "🔧 <strong>Solution:</strong> Le code Portal.php doit chercher dans <code>tblclients.phonenumber</code> au lieu de <code>tblcontacts.phonenumber</code>";
    } else if ($contact_count > 0) {
        echo "✅ Les numéros sont dans <code>tblcontacts.phonenumber</code> (correct pour le code actuel)";
    } else {
        echo "⚠️ Aucun numéro trouvé dans les deux tables !";
    }
    echo "</div>";

    // 2. Liste des patients
    echo "<h2>2️⃣ Les 20 derniers patients diététiques</h2>";

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

        // Highlight where the phone is
        if ($row['client_phone']) {
            $client_phone = "<code style='background: #90EE90; font-weight: bold;'>{$row['client_phone']}</code>";
        } else {
            $client_phone = '<span class="warning">VIDE</span>';
        }

        if ($row['contact_phone']) {
            $contact_phone = "<code style='background: #90EE90; font-weight: bold;'>{$row['contact_phone']}</code>";
        } else {
            $contact_phone = '<span class="warning">VIDE</span>';
        }

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
    echo "<h2>3️⃣ Tester la recherche par téléphone</h2>";
    echo "<form method='GET' style='margin: 20px 0;'>";
    echo "<input type='text' name='test_phone' placeholder='Entrez un numéro (ex: +221771234567)' style='padding: 10px; width: 400px;' value='" . htmlspecialchars($_GET['test_phone'] ?? '') . "'>";
    echo "<button type='submit' style='padding: 10px 20px; background: #01807B; color: white; border: none; cursor: pointer;'>🔍 Tester</button>";
    echo "</form>";

    if (!empty($_GET['test_phone'])) {
        $test_phone = $_GET['test_phone'];
        echo "<div class='info'><strong>Test avec:</strong> <code>{$test_phone}</code></div>";

        // Test dans CONTACTS (code ACTUEL dans Portal.php)
        echo "<h3>A) Recherche dans tblcontacts (CODE ACTUEL Portal.php):</h3>";
        $sql = "SELECT ct.email, ct.phonenumber, ct.firstname, ct.lastname, ct.userid
FROM {$prefix}contacts ct
JOIN {$prefix}dietic_patients p ON p.client_id = ct.userid
WHERE ct.is_primary = 1 AND ct.phonenumber = '{$test_phone}'";

        echo "<code class='sql-box'>{$sql}</code>";

        $stmt = $db->prepare("SELECT ct.email, ct.phonenumber, ct.firstname, ct.lastname, ct.userid FROM {$prefix}contacts ct JOIN {$prefix}dietic_patients p ON p.client_id = ct.userid WHERE ct.is_primary = 1 AND ct.phonenumber = :phone");
        $stmt->execute([':phone' => $test_phone]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            echo "<p class='success'>✅ TROUVÉ dans contacts !</p>";
            echo "<p>Nom: <strong>{$result['firstname']} {$result['lastname']}</strong> - Email: <code>{$result['email']}</code></p>";
        } else {
            echo "<p class='warning'>❌ NON TROUVÉ dans contacts (c'est pour ça que la connexion échoue !)</p>";
        }

        // Test dans CLIENTS (LA SOLUTION)
        echo "<h3>B) Recherche dans tblclients (SOLUTION):</h3>";
        $sql2 = "SELECT c.userid, c.phonenumber, ct.email, ct.firstname, ct.lastname
FROM {$prefix}clients c
LEFT JOIN {$prefix}contacts ct ON ct.userid = c.userid AND ct.is_primary = 1
JOIN {$prefix}dietic_patients p ON p.client_id = c.userid
WHERE c.phonenumber = '{$test_phone}'";

        echo "<code class='sql-box'>{$sql2}</code>";

        $stmt2 = $db->prepare("SELECT c.userid, c.phonenumber, ct.email, ct.firstname, ct.lastname FROM {$prefix}clients c LEFT JOIN {$prefix}contacts ct ON ct.userid = c.userid AND ct.is_primary = 1 JOIN {$prefix}dietic_patients p ON p.client_id = c.userid WHERE c.phonenumber = :phone");
        $stmt2->execute([':phone' => $test_phone]);
        $result2 = $stmt2->fetch(PDO::FETCH_ASSOC);

        if ($result2) {
            echo "<p class='success'>✅✅✅ TROUVÉ dans clients ! C'EST LÀ QUE SONT VOS NUMÉROS !</p>";
            echo "<p>Nom: <strong>{$result2['firstname']} {$result2['lastname']}</strong> - Email: <code>{$result2['email']}</code></p>";
            echo "<div class='info' style='background: #fff3cd; border-left-color: #ffc107;'>";
            echo "💡 <strong>SOLUTION:</strong><br>";
            echo "Le code Portal.php cherche dans <code>tblcontacts.phonenumber</code> (VIDE)<br>";
            echo "Mais vos numéros sont dans <code>tblclients.phonenumber</code> (REMPLI)<br><br>";
            echo "Je vais modifier le code pour chercher dans <code>tblclients</code> au lieu de <code>tblcontacts</code>";
            echo "</div>";
        } else {
            echo "<p class='warning'>❌ NON TROUVÉ dans clients non plus</p>";

            // Essai sans le +
            $phone_no_plus = ltrim($test_phone, '+');
            if ($phone_no_plus != $test_phone) {
                echo "<p>Essai sans le + : <code>{$phone_no_plus}</code></p>";

                $stmt3 = $db->prepare("SELECT c.phonenumber, ct.firstname, ct.lastname FROM {$prefix}clients c LEFT JOIN {$prefix}contacts ct ON ct.userid = c.userid WHERE c.phonenumber = :phone");
                $stmt3->execute([':phone' => $phone_no_plus]);
                $result3 = $stmt3->fetch(PDO::FETCH_ASSOC);

                if ($result3) {
                    echo "<p class='success'>✅ TROUVÉ sans le + : {$result3['firstname']} {$result3['lastname']}</p>";
                }
            }

            // Recherche approximative
            echo "<p>Recherche approximative...</p>";
            $phone_clean = preg_replace('/[^0-9]/', '', $test_phone);
            $stmt4 = $db->prepare("SELECT c.phonenumber, ct.firstname, ct.lastname FROM {$prefix}clients c LEFT JOIN {$prefix}contacts ct ON ct.userid = c.userid WHERE c.phonenumber LIKE :phone LIMIT 5");
            $stmt4->execute([':phone' => "%{$phone_clean}%"]);
            $similar = $stmt4->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($similar)) {
                echo "<p class='success'>Numéros similaires trouvés dans tblclients:</p><ul>";
                foreach ($similar as $s) {
                    echo "<li><code>{$s['phonenumber']}</code> - {$s['firstname']} {$s['lastname']}</li>";
                }
                echo "</ul>";
            }
        }
    }

    // 4. Doublons
    echo "<h2>4️⃣ Vérification des doublons</h2>";

    echo "<h3>Dans tblclients:</h3>";
    $stmt = $db->query("SELECT phonenumber, COUNT(*) as count FROM {$prefix}clients WHERE phonenumber IS NOT NULL AND phonenumber != '' GROUP BY phonenumber HAVING count > 1");
    $duplicates_clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($duplicates_clients)) {
        echo "<p class='success'>✅ Aucun doublon dans clients</p>";
    } else {
        echo "<table><tr><th>Numéro</th><th>Occurrences</th></tr>";
        foreach ($duplicates_clients as $dup) {
            echo "<tr><td class='warning'><code>{$dup['phonenumber']}</code></td><td>{$dup['count']}</td></tr>";
        }
        echo "</table>";
    }

    echo "<h3>Dans tblcontacts:</h3>";
    $stmt = $db->query("SELECT phonenumber, COUNT(*) as count FROM {$prefix}contacts WHERE phonenumber IS NOT NULL AND phonenumber != '' AND is_primary = 1 GROUP BY phonenumber HAVING count > 1");
    $duplicates_contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($duplicates_contacts)) {
        echo "<p class='success'>✅ Aucun doublon dans contacts</p>";
    } else {
        echo "<table><tr><th>Numéro</th><th>Occurrences</th></tr>";
        foreach ($duplicates_contacts as $dup) {
            echo "<tr><td class='warning'><code>{$dup['phonenumber']}</code></td><td>{$dup['count']}</td></tr>";
        }
        echo "</table>";
    }
    ?>
</body>
</html>
