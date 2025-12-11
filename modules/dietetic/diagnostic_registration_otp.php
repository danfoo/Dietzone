<?php
/**
 * Script de diagnostic pour l'inscription avec OTP
 * URL: https://app.dietsenegal.net/modules/dietetic/diagnostic_registration_otp.php
 */

// Configuration de la base de données (à adapter selon votre config)
define('DB_HOST', 'localhost');
define('DB_NAME', 'dietzone');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_PREFIX', 'tbl');

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnostic Inscription OTP</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #01807B;
            margin-bottom: 10px;
            font-size: 28px;
        }
        h2 {
            color: #333;
            margin: 30px 0 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #01807B;
            font-size: 20px;
        }
        h3 {
            color: #555;
            margin: 20px 0 10px;
            font-size: 16px;
        }
        .success {
            color: #28a745;
            padding: 12px;
            background: #d4edda;
            border-left: 4px solid #28a745;
            margin: 10px 0;
            border-radius: 4px;
        }
        .error {
            color: #dc3545;
            padding: 12px;
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            margin: 10px 0;
            border-radius: 4px;
        }
        .warning {
            color: #856404;
            padding: 12px;
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            margin: 10px 0;
            border-radius: 4px;
        }
        .info {
            color: #004085;
            padding: 12px;
            background: #d1ecf1;
            border-left: 4px solid #17a2b8;
            margin: 10px 0;
            border-radius: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
        }
        pre {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            overflow-x: auto;
            margin: 10px 0;
            border: 1px solid #dee2e6;
        }
        code {
            background: #f8f9fa;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
        }
        .status-ok { color: #28a745; font-weight: bold; }
        .status-error { color: #dc3545; font-weight: bold; }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #01807B;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 10px 5px 10px 0;
        }
        .btn:hover {
            background: #016661;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Diagnostic Inscription OTP</h1>
        <p style="color: #6c757d; margin-bottom: 30px;">Vérification de la configuration et du fonctionnement de l'inscription avec OTP</p>

        <?php
        // Connexion à la base de données
        try {
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

            if ($conn->connect_error) {
                throw new Exception("Erreur de connexion: " . $conn->connect_error);
            }

            echo '<div class="success">✓ Connexion à la base de données réussie</div>';

        } catch (Exception $e) {
            echo '<div class="error">✗ ' . htmlspecialchars($e->getMessage()) . '</div>';
            echo '<div class="warning">Veuillez vérifier les constantes de connexion en haut du fichier</div>';
            exit;
        }

        // 1. Vérifier la structure de la table dietic_otp_codes
        echo "<h2>1. Structure de la table OTP</h2>";

        $table_name = DB_PREFIX . 'dietic_otp_codes';
        $check_table = $conn->query("SHOW TABLES LIKE '$table_name'");

        if ($check_table && $check_table->num_rows > 0) {
            echo "<div class='success'>✓ Table <code>$table_name</code> existe</div>";

            // Vérifier la structure
            $structure = $conn->query("DESCRIBE $table_name");
            if ($structure) {
                echo "<h3>Colonnes de la table:</h3>";
                echo "<table><thead><tr><th>Colonne</th><th>Type</th><th>Null</th><th>Défaut</th></tr></thead><tbody>";
                while ($row = $structure->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td><code>{$row['Field']}</code></td>";
                    echo "<td>{$row['Type']}</td>";
                    echo "<td>{$row['Null']}</td>";
                    echo "<td>" . ($row['Default'] ?: 'NULL') . "</td>";
                    echo "</tr>";
                }
                echo "</tbody></table>";
            }
        } else {
            echo "<div class='error'>✗ Table <code>$table_name</code> n'existe pas!</div>";
            echo "<div class='warning'>La table doit être créée. SQL requis:</div>";
            echo "<pre>CREATE TABLE `$table_name` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `phone` varchar(20) NOT NULL,
  `code` varchar(10) NOT NULL,
  `type` varchar(50) NOT NULL,
  `used` tinyint(1) DEFAULT 0,
  `used_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `expires_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `phone` (`phone`),
  KEY `code` (`code`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;</pre>";
        }

        // 2. Vérifier les derniers codes OTP créés
        echo "<h2>2. Derniers codes OTP générés</h2>";

        $recent_otps = $conn->query("
            SELECT
                id,
                phone,
                code,
                type,
                used,
                used_at,
                created_at,
                expires_at,
                CASE
                    WHEN expires_at > NOW() THEN 'Valide'
                    ELSE 'Expiré'
                END as status,
                TIMESTAMPDIFF(MINUTE, created_at, NOW()) as minutes_ago
            FROM $table_name
            WHERE type = 'registration'
            ORDER BY created_at DESC
            LIMIT 10
        ");

        if ($recent_otps && $recent_otps->num_rows > 0) {
            echo "<table><thead><tr><th>ID</th><th>Téléphone</th><th>Code</th><th>Type</th><th>Utilisé</th><th>Status</th><th>Créé il y a</th></tr></thead><tbody>";
            while ($row = $recent_otps->fetch_assoc()) {
                $used_class = $row['used'] ? 'status-error' : 'status-ok';
                $status_class = $row['status'] == 'Valide' ? 'status-ok' : 'status-error';
                echo "<tr>";
                echo "<td>{$row['id']}</td>";
                echo "<td>{$row['phone']}</td>";
                echo "<td><strong>{$row['code']}</strong></td>";
                echo "<td>{$row['type']}</td>";
                echo "<td class='$used_class'>" . ($row['used'] ? 'Oui' : 'Non') . "</td>";
                echo "<td class='$status_class'>{$row['status']}</td>";
                echo "<td>{$row['minutes_ago']} min</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<div class='info'>Aucun code OTP d'inscription trouvé dans la base de données</div>";
        }

        // 3. Vérifier les logs d'activité récents
        echo "<h2>3. Logs d'activité récents (inscription)</h2>";

        $logs_table = DB_PREFIX . 'activitylog';
        $recent_logs = $conn->query("
            SELECT
                id,
                description,
                date,
                TIMESTAMPDIFF(MINUTE, date, NOW()) as minutes_ago
            FROM $logs_table
            WHERE description LIKE '%INSCRIPTION%'
            OR description LIKE '%OTP%'
            OR description LIKE '%SMS%'
            ORDER BY date DESC
            LIMIT 20
        ");

        if ($recent_logs && $recent_logs->num_rows > 0) {
            echo "<table><thead><tr><th>ID</th><th>Description</th><th>Date</th><th>Il y a</th></tr></thead><tbody>";
            while ($row = $recent_logs->fetch_assoc()) {
                $class = '';
                if (strpos($row['description'], 'ERREUR') !== false) {
                    $class = 'status-error';
                } elseif (strpos($row['description'], 'envoyé') !== false) {
                    $class = 'status-ok';
                }
                echo "<tr>";
                echo "<td>{$row['id']}</td>";
                echo "<td class='$class'>" . htmlspecialchars($row['description']) . "</td>";
                echo "<td>{$row['date']}</td>";
                echo "<td>{$row['minutes_ago']} min</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<div class='info'>Aucun log d'inscription trouvé</div>";
        }

        // 4. Vérifier les fichiers requis
        echo "<h2>4. Vérification des fichiers</h2>";

        $files_to_check = [
            'Controller' => '../../../modules/dietetic/controllers/Portal.php',
            'Vue OTP' => '../../../modules/dietetic/views/portal/verify_registration_otp.php',
            'Helper' => '../../../modules/dietetic/helpers/dietetic_helper.php'
        ];

        echo "<table><thead><tr><th>Fichier</th><th>Chemin</th><th>Status</th></tr></thead><tbody>";
        foreach ($files_to_check as $name => $path) {
            $full_path = __DIR__ . '/' . $path;
            $exists = file_exists($full_path);
            $status_class = $exists ? 'status-ok' : 'status-error';
            echo "<tr>";
            echo "<td><strong>$name</strong></td>";
            echo "<td><code>$path</code></td>";
            echo "<td class='$status_class'>" . ($exists ? '✓ Existe' : '✗ Manquant') . "</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";

        // 5. Vérifier la configuration LAM SMS
        echo "<h2>5. Configuration LAM SMS</h2>";

        $settings_table = DB_PREFIX . 'dietic_notification_settings';
        $sms_settings = $conn->query("
            SELECT setting_key, setting_value
            FROM $settings_table
            WHERE setting_key IN ('sms_lam_account_id', 'sms_lam_password', 'sms_lam_sender_id')
        ");

        if ($sms_settings && $sms_settings->num_rows > 0) {
            echo "<table><thead><tr><th>Paramètre</th><th>Valeur</th><th>Status</th></tr></thead><tbody>";
            while ($row = $sms_settings->fetch_assoc()) {
                $has_value = !empty($row['setting_value']);
                $status_class = $has_value ? 'status-ok' : 'status-error';
                $display_value = $has_value ? str_repeat('*', min(15, strlen($row['setting_value']))) : '(vide)';
                echo "<tr>";
                echo "<td><code>{$row['setting_key']}</code></td>";
                echo "<td>$display_value</td>";
                echo "<td class='$status_class'>" . ($has_value ? '✓ Configuré' : '✗ Vide') . "</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<div class='error'>✗ Aucune configuration SMS trouvée</div>";
        }

        // 6. Tester la route verify_registration_otp
        echo "<h2>6. Test de la route OTP</h2>";

        $base_url = 'https://app.dietsenegal.net';
        $otp_url = $base_url . '/dietetic/portal/verify_registration_otp';

        echo "<div class='info'>";
        echo "<p><strong>URL de validation OTP:</strong> <code>$otp_url</code></p>";
        echo "<p>Cette route doit être accessible après l'inscription.</p>";
        echo "<a href='$otp_url' class='btn' target='_blank'>Tester la route OTP</a>";
        echo "</div>";

        // 7. Instructions de test
        echo "<h2>7. Comment tester l'inscription</h2>";

        echo "<div class='info'>";
        echo "<ol style='margin-left: 20px; line-height: 1.8;'>";
        echo "<li>Allez sur <a href='$base_url/dietetic/portal' target='_blank'>$base_url/dietetic/portal</a></li>";
        echo "<li>Cliquez sur \"S'inscrire\" ou \"Créer un compte\"</li>";
        echo "<li>Remplissez le formulaire d'inscription</li>";
        echo "<li><strong>Attendu:</strong> Redirection vers la page de validation OTP</li>";
        echo "<li><strong>Attendu:</strong> Réception d'un SMS avec le code</li>";
        echo "<li>Vérifiez les logs d'activité dans la section 3 ci-dessus</li>";
        echo "</ol>";
        echo "</div>";

        // 8. Résumé et recommandations
        echo "<h2>8. Résumé et recommandations</h2>";

        $issues = [];

        // Vérifier si la table existe
        $table_exists = $conn->query("SHOW TABLES LIKE '$table_name'");
        if (!$table_exists || $table_exists->num_rows == 0) {
            $issues[] = "La table <code>$table_name</code> n'existe pas - créez-la avec le SQL fourni ci-dessus";
        }

        // Vérifier si des codes OTP ont été créés récemment
        $recent_count = $conn->query("SELECT COUNT(*) as count FROM $table_name WHERE created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)");
        if ($recent_count) {
            $count_row = $recent_count->fetch_assoc();
            if ($count_row['count'] == 0) {
                $issues[] = "Aucun code OTP créé dans la dernière heure - le formulaire d'inscription ne génère peut-être pas les codes";
            }
        }

        if (empty($issues)) {
            echo "<div class='success'>";
            echo "<p><strong>✓ Tous les éléments essentiels sont en place!</strong></p>";
            echo "<p>Si l'inscription ne fonctionne toujours pas, vérifiez:</p>";
            echo "<ul style='margin-left: 20px; margin-top: 10px;'>";
            echo "<li>Les logs d'erreur PHP: <code>/var/log/apache2/error.log</code></li>";
            echo "<li>Les logs d'erreur MySQL</li>";
            echo "<li>La session PHP (vérifiez que les sessions fonctionnent)</li>";
            echo "</ul>";
            echo "</div>";
        } else {
            echo "<div class='error'>";
            echo "<p><strong>⚠️ Problèmes détectés:</strong></p>";
            echo "<ul style='margin-left: 20px; margin-top: 10px;'>";
            foreach ($issues as $issue) {
                echo "<li>$issue</li>";
            }
            echo "</ul>";
            echo "</div>";
        }

        $conn->close();
        ?>

        <hr style="margin: 30px 0;">
        <p style="text-align: center; color: #6c757d; font-size: 13px;">
            Script de diagnostic - Dernière mise à jour: <?php echo date('d/m/Y H:i:s'); ?>
        </p>
    </div>
</body>
</html>
