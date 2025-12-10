<?php
/**
 * Script de diagnostic pour tester la connexion Auth
 *
 * Utilisation:
 * 1. Ouvrir dans le navigateur: http://votre-domaine.com/modules/dietetic/test_auth_debug.php
 * 2. Entrer votre numéro de téléphone
 * 3. Voir les informations de debug
 */

// Charger le framework Perfex
define('BASEPATH', true);
require_once(__DIR__ . '/../../application/config/config.php');
require_once(__DIR__ . '/../../application/config/database.php');

// Connexion à la base de données
$mysqli = new mysqli(
    $db['default']['hostname'],
    $db['default']['username'],
    $db['default']['password'],
    $db['default']['database']
);

if ($mysqli->connect_error) {
    die('Erreur de connexion : ' . $mysqli->connect_error);
}

$mysqli->set_charset('utf8mb4');

// Fonction pour nettoyer le numéro de téléphone
function clean_phone($phone) {
    $phone = preg_replace('/[\s\-\(\)]/', '', $phone);
    if (!str_starts_with($phone, '+')) {
        $phone = '+' . $phone;
    }
    return $phone;
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Authentification - DietZone</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #01807B;
            margin-top: 0;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #2c3e50;
        }
        input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            box-sizing: border-box;
        }
        button {
            background: #01807B;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
        }
        button:hover {
            background: #016663;
        }
        .result {
            margin-top: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #01807B;
        }
        .success {
            border-left-color: #28a745;
            background: #d4edda;
        }
        .error {
            border-left-color: #dc3545;
            background: #f8d7da;
        }
        .info {
            margin-top: 10px;
            padding: 10px;
            background: #e7f3f1;
            border-radius: 5px;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #f8f9fa;
            font-weight: 600;
        }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Diagnostic Authentification DietZone</h1>
        <p>Ce script vous aide à diagnostiquer les problèmes de connexion.</p>

        <form method="POST">
            <div class="form-group">
                <label>Numéro de téléphone :</label>
                <input type="text" name="phone" placeholder="+221 77 123 45 67" required
                       value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                <div class="info">
                    Formats acceptés : +221771234567, 771234567, +221 77 123 45 67, etc.
                </div>
            </div>
            <button type="submit">Tester</button>
        </form>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['phone'])) {
            $phone_input = $_POST['phone'];
            $phone_cleaned = clean_phone($phone_input);

            echo '<div class="result">';
            echo '<h3>📊 Résultats du test</h3>';

            echo '<h4>1. Nettoyage du numéro</h4>';
            echo '<table>';
            echo '<tr><th>Entrée</th><td><code>' . htmlspecialchars($phone_input) . '</code></td></tr>';
            echo '<tr><th>Nettoyé</th><td><code>' . htmlspecialchars($phone_cleaned) . '</code></td></tr>';
            echo '</table>';

            // Chercher le patient
            $stmt = $mysqli->prepare("
                SELECT
                    p.id as patient_id,
                    p.client_id,
                    ct.contactid,
                    ct.firstname,
                    ct.lastname,
                    ct.email,
                    ct.phonenumber,
                    ct.is_primary,
                    ct.active,
                    c.company,
                    LENGTH(ct.password) as password_length,
                    ct.password IS NOT NULL as has_password
                FROM tbldietic_patients p
                JOIN tblcontacts ct ON ct.userid = p.client_id AND ct.is_primary = 1
                JOIN tblclients c ON c.userid = p.client_id
                WHERE ct.phonenumber = ?
            ");

            $stmt->bind_param('s', $phone_cleaned);
            $stmt->execute();
            $result = $stmt->get_result();

            echo '<h4>2. Recherche dans la base de données</h4>';

            if ($result->num_rows > 0) {
                $patient = $result->fetch_assoc();
                echo '<div class="success">';
                echo '<strong>✅ Patient trouvé !</strong>';
                echo '</div>';

                echo '<table>';
                echo '<tr><th>ID Patient</th><td>' . $patient['patient_id'] . '</td></tr>';
                echo '<tr><th>ID Client</th><td>' . $patient['client_id'] . '</td></tr>';
                echo '<tr><th>ID Contact</th><td>' . $patient['contactid'] . '</td></tr>';
                echo '<tr><th>Nom complet</th><td>' . htmlspecialchars($patient['firstname'] . ' ' . $patient['lastname']) . '</td></tr>';
                echo '<tr><th>Email</th><td>' . htmlspecialchars($patient['email']) . '</td></tr>';
                echo '<tr><th>Téléphone (BDD)</th><td><code>' . htmlspecialchars($patient['phonenumber']) . '</code></td></tr>';
                echo '<tr><th>Téléphone match ?</th><td>' . ($patient['phonenumber'] === $phone_cleaned ? '✅ OUI' : '❌ NON') . '</td></tr>';
                echo '<tr><th>Contact primaire</th><td>' . ($patient['is_primary'] ? '✅' : '❌') . '</td></tr>';
                echo '<tr><th>Contact actif</th><td>' . ($patient['active'] ? '✅' : '❌') . '</td></tr>';
                echo '<tr><th>A un mot de passe</th><td>' . ($patient['has_password'] ? '✅ OUI (' . $patient['password_length'] . ' caractères)' : '❌ NON') . '</td></tr>';
                echo '</table>';

                if (!$patient['has_password']) {
                    echo '<div class="error" style="margin-top: 15px;">';
                    echo '<strong>⚠️ PROBLÈME : Aucun mot de passe défini</strong><br>';
                    echo 'Ce compte patient n\'a pas de mot de passe. Vous devez en créer un via l\'inscription ou la réinitialisation.';
                    echo '</div>';
                }

                // Vérifier si c'est un mot de passe hashé
                if ($patient['has_password'] && $patient['password_length'] > 50) {
                    echo '<div class="info" style="margin-top: 15px;">';
                    echo '<strong>✅ Mot de passe bien hashé</strong><br>';
                    echo 'Le mot de passe est sécurisé (longueur ' . $patient['password_length'] . ' caractères = hash).';
                    echo '</div>';
                }

            } else {
                echo '<div class="error">';
                echo '<strong>❌ Aucun patient trouvé</strong>';
                echo '</div>';

                // Chercher des numéros similaires
                echo '<h4>3. Recherche de numéros similaires</h4>';
                $search_pattern = '%' . substr($phone_cleaned, -8) . '%';
                $stmt2 = $mysqli->prepare("
                    SELECT DISTINCT ct.phonenumber, ct.firstname, ct.lastname
                    FROM tblcontacts ct
                    JOIN tbldietic_patients p ON ct.userid = p.client_id
                    WHERE ct.phonenumber LIKE ?
                    LIMIT 10
                ");
                $stmt2->bind_param('s', $search_pattern);
                $stmt2->execute();
                $similar = $stmt2->get_result();

                if ($similar->num_rows > 0) {
                    echo '<p>Numéros trouvés dans la base (8 derniers chiffres similaires) :</p>';
                    echo '<table>';
                    echo '<tr><th>Nom</th><th>Téléphone</th></tr>';
                    while ($row = $similar->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($row['firstname'] . ' ' . $row['lastname']) . '</td>';
                        echo '<td><code>' . htmlspecialchars($row['phonenumber']) . '</code></td>';
                        echo '</tr>';
                    }
                    echo '</table>';

                    echo '<div class="info" style="margin-top: 15px;">';
                    echo '<strong>💡 Suggestion :</strong> Vérifiez le format de votre numéro de téléphone. ';
                    echo 'Il doit correspondre exactement à celui dans la base de données.';
                    echo '</div>';
                } else {
                    echo '<p>Aucun numéro similaire trouvé dans la base.</p>';
                    echo '<div class="info">';
                    echo '<strong>💡 Ce compte n\'existe pas encore.</strong> Utilisez l\'onglet "Inscription" pour créer un compte.';
                    echo '</div>';
                }
            }

            echo '</div>';
        }
        ?>

        <div class="info" style="margin-top: 30px;">
            <h4>📝 Comment corriger les problèmes ?</h4>
            <ul>
                <li><strong>Patient non trouvé :</strong> Vérifiez le format du numéro ou créez un compte via "Inscription"</li>
                <li><strong>Pas de mot de passe :</strong> Utilisez "Mot de passe oublié" pour en créer un</li>
                <li><strong>Numéro différent :</strong> Le numéro doit être au format international (+221...)</li>
            </ul>
        </div>
    </div>
</body>
</html>
