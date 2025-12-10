<?php
defined('BASEPATH') or define('BASEPATH', true);
chdir(__DIR__ . '/../..');
require_once('application/libraries/App_controller.php');

$CI = &get_instance();
$CI->load->database();

echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Diagnostic Téléphones</title>";
echo "<style>
body { font-family: monospace; padding: 20px; background: #f5f5f5; }
table { border-collapse: collapse; width: 100%; margin: 20px 0; background: white; }
th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
th { background-color: #01807B; color: white; }
tr:nth-child(even) { background-color: #f9f9f9; }
.warning { color: red; font-weight: bold; }
.success { color: green; font-weight: bold; }
.info { background: #d1ecf1; padding: 15px; margin: 20px 0; border-left: 4px solid #0c5460; }
code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; }
h1, h2, h3 { color: #01807B; }
</style></head><body>";

echo "<h1>🔍 Diagnostic Complet - Numéros de Téléphone</h1>";

// 1. Vérifier où sont les numéros : clients ou contacts ?
echo "<h2>1️⃣ Localisation des numéros de téléphone</h2>";

echo "<h3>Dans tblclients:</h3>";
$CI->db->select('COUNT(*) as count');
$CI->db->where('phonenumber IS NOT NULL');
$CI->db->where('phonenumber !=', '');
$client_phones = $CI->db->get(db_prefix() . 'clients')->row();
echo "<p>Clients avec numéro: <strong>{$client_phones->count}</strong></p>";

echo "<h3>Dans tblcontacts:</h3>";
$CI->db->select('COUNT(*) as count');
$CI->db->where('phonenumber IS NOT NULL');
$CI->db->where('phonenumber !=', '');
$CI->db->where('is_primary', 1);
$contact_phones = $CI->db->get(db_prefix() . 'contacts')->row();
echo "<p>Contacts (primary) avec numéro: <strong>{$contact_phones->count}</strong></p>";

// 2. Lister les 20 derniers patients
echo "<h2>2️⃣ Les 20 derniers patients diététiques</h2>";

$query = $CI->db->query("
    SELECT
        p.id as patient_id,
        p.client_id,
        c.phonenumber as client_phone,
        ct.firstname,
        ct.lastname,
        ct.email,
        ct.phonenumber as contact_phone,
        LENGTH(ct.password) as password_length
    FROM " . db_prefix() . "dietic_patients p
    LEFT JOIN " . db_prefix() . "clients c ON c.userid = p.client_id
    LEFT JOIN " . db_prefix() . "contacts ct ON ct.userid = p.client_id AND ct.is_primary = 1
    ORDER BY p.id DESC
    LIMIT 20
");

echo "<table>";
echo "<tr>
    <th>ID</th>
    <th>Nom</th>
    <th>Email</th>
    <th>📱 CLIENT.phonenumber</th>
    <th>📱 CONTACT.phonenumber</th>
    <th>Password?</th>
</tr>";

foreach ($query->result() as $row) {
    $name = trim($row->firstname . ' ' . $row->lastname);
    $client_phone = $row->client_phone ?: '<span class="warning">VIDE</span>';
    $contact_phone = $row->contact_phone ?: '<span class="warning">VIDE</span>';
    $password = $row->password_length > 0 ? "<span class='success'>✓</span>" : "<span class='warning'>✗</span>";

    echo "<tr>";
    echo "<td>{$row->patient_id}</td>";
    echo "<td><strong>{$name}</strong></td>";
    echo "<td><code>{$row->email}</code></td>";
    echo "<td>{$client_phone}</td>";
    echo "<td>{$contact_phone}</td>";
    echo "<td>{$password}</td>";
    echo "</tr>";
}

echo "</table>";

// 3. Test de recherche
echo "<h2>3️⃣ Tester la recherche par téléphone</h2>";
echo "<form method='GET' style='margin: 20px 0;'>";
echo "<input type='text' name='test_phone' placeholder='Entrez un numéro (ex: +221771234567)' style='padding: 10px; width: 400px; font-family: monospace;'>";
echo "<button type='submit' style='padding: 10px 20px; background: #01807B; color: white; border: none; cursor: pointer;'>🔍 Tester</button>";
echo "</form>";

if (!empty($_GET['test_phone'])) {
    $test_phone = $_GET['test_phone'];
    echo "<div class='info'><strong>Test avec:</strong> <code>{$test_phone}</code></div>";

    // Test dans CONTACTS (ce qu'utilise Portal.php)
    echo "<h3>Recherche dans tblcontacts (utilisé par Portal.php):</h3>";

    $CI->db->select('ct.email, ct.phonenumber, ct.firstname, ct.lastname, ct.userid, p.client_id');
    $CI->db->from(db_prefix() . 'contacts ct');
    $CI->db->join(db_prefix() . 'dietic_patients p', 'p.client_id = ct.userid');
    $CI->db->where('ct.is_primary', 1);
    $CI->db->where('ct.phonenumber', $test_phone);

    // Afficher la requête SQL
    echo "<p><strong>Requête SQL:</strong></p>";
    echo "<code style='display: block; padding: 10px; background: #2d2d2d; color: #fff; overflow-x: auto;'>";
    echo $CI->db->get_compiled_select();
    echo "</code>";

    // Exécuter la requête
    $CI->db->select('ct.email, ct.phonenumber, ct.firstname, ct.lastname, ct.userid, p.client_id');
    $CI->db->from(db_prefix() . 'contacts ct');
    $CI->db->join(db_prefix() . 'dietic_patients p', 'p.client_id = ct.userid');
    $CI->db->where('ct.is_primary', 1);
    $CI->db->where('ct.phonenumber', $test_phone);
    $result = $CI->db->get()->row();

    if ($result) {
        echo "<p class='success'>✅ TROUVÉ !</p>";
        echo "<table>";
        echo "<tr><th>Champ</th><th>Valeur</th></tr>";
        echo "<tr><td>Nom</td><td><strong>{$result->firstname} {$result->lastname}</strong></td></tr>";
        echo "<tr><td>Email</td><td><code>{$result->email}</code></td></tr>";
        echo "<tr><td>Téléphone</td><td><code>{$result->phonenumber}</code></td></tr>";
        echo "<tr><td>User ID</td><td>{$result->userid}</td></tr>";
        echo "<tr><td>Client ID</td><td>{$result->client_id}</td></tr>";
        echo "</table>";
    } else {
        echo "<p class='warning'>❌ NON TROUVÉ avec ce format exact</p>";

        // Essayer sans le +
        $phone_no_plus = ltrim($test_phone, '+');
        echo "<p>Essai sans le + : <code>{$phone_no_plus}</code></p>";

        $CI->db->select('ct.email, ct.phonenumber, ct.firstname, ct.lastname');
        $CI->db->from(db_prefix() . 'contacts ct');
        $CI->db->join(db_prefix() . 'dietic_patients p', 'p.client_id = ct.userid');
        $CI->db->where('ct.is_primary', 1);
        $CI->db->where('ct.phonenumber', $phone_no_plus);
        $result2 = $CI->db->get()->row();

        if ($result2) {
            echo "<p class='success'>✅ TROUVÉ sans le + !</p>";
            echo "<p>Nom: <strong>{$result2->firstname} {$result2->lastname}</strong> - Email: <code>{$result2->email}</code></p>";
        } else {
            echo "<p class='warning'>❌ Toujours pas trouvé</p>";

            // Recherche LIKE
            echo "<p>Recherche approximative (LIKE %{$phone_no_plus}%)...</p>";
            $CI->db->select('ct.email, ct.phonenumber, ct.firstname, ct.lastname');
            $CI->db->from(db_prefix() . 'contacts ct');
            $CI->db->where('ct.is_primary', 1);
            $CI->db->like('ct.phonenumber', $phone_no_plus);
            $CI->db->limit(5);
            $similar = $CI->db->get()->result();

            if (!empty($similar)) {
                echo "<p class='success'>Numéros similaires trouvés:</p>";
                echo "<ul>";
                foreach ($similar as $s) {
                    echo "<li><code>{$s->phonenumber}</code> - {$s->firstname} {$s->lastname}</li>";
                }
                echo "</ul>";
            } else {
                echo "<p class='warning'>Aucun numéro similaire</p>";
            }
        }
    }

    // Test dans CLIENTS
    echo "<h3>Recherche dans tblclients:</h3>";
    $CI->db->select('c.userid, c.company, c.phonenumber');
    $CI->db->from(db_prefix() . 'clients c');
    $CI->db->where('c.phonenumber', $test_phone);
    $client_result = $CI->db->get()->row();

    if ($client_result) {
        echo "<p class='success'>✅ TROUVÉ dans clients: {$client_result->company} (ID: {$client_result->userid})</p>";
    } else {
        echo "<p class='warning'>❌ Non trouvé dans clients</p>";
    }
}

// 4. Vérifier les doublons
echo "<h2>4️⃣ Vérification des doublons</h2>";
$duplicates = $CI->db->query("
    SELECT phonenumber, COUNT(*) as count
    FROM " . db_prefix() . "contacts
    WHERE phonenumber IS NOT NULL
    AND phonenumber != ''
    AND is_primary = 1
    GROUP BY phonenumber
    HAVING count > 1
")->result();

if (empty($duplicates)) {
    echo "<p class='success'>✅ Aucun doublon trouvé</p>";
} else {
    echo "<p class='warning'>⚠️ Doublons trouvés:</p>";
    echo "<table><tr><th>Numéro</th><th>Occurrences</th></tr>";
    foreach ($duplicates as $dup) {
        echo "<tr><td class='warning'><code>{$dup->phonenumber}</code></td><td>{$dup->count}</td></tr>";
    }
    echo "</table>";
}

echo "</body></html>";
