<?php
/**
 * Script d'installation automatique pour le système de notation
 * Accédez à ce fichier via : /modules/dietetic/install_ratings.php
 */

// Security check - must be accessed from browser
if (php_sapi_name() === 'cli') {
    die('This script must be run from a web browser');
}

// Get Perfex base path (go up 2 levels from modules/dietetic/)
define('APP_BASE_PATH', realpath(__DIR__ . '/../../') . '/');

// Bootstrap Perfex
if (file_exists(APP_BASE_PATH . 'application/config/app-config.php')) {
    require_once(APP_BASE_PATH . 'application/config/app-config.php');
} else {
    die('Cannot find Perfex CRM installation');
}

// Get database connection from Perfex config
if (file_exists(APP_BASE_PATH . 'application/config/database.php')) {
    require_once(APP_BASE_PATH . 'application/config/database.php');
} else {
    die('Cannot find database configuration');
}

// Get database credentials
$db_config = $db['default'];
$db_prefix = isset($db_config['dbprefix']) ? $db_config['dbprefix'] : '';

// Connect to database
$mysqli = new mysqli(
    $db_config['hostname'],
    $db_config['username'],
    $db_config['password'],
    $db_config['database']
);

if ($mysqli->connect_error) {
    die('Database connection failed: ' . $mysqli->connect_error);
}

$mysqli->set_charset('utf8');

echo '<!DOCTYPE html>
<html>
<head>
    <title>Installation du Système de Notation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #16a085;
            border-bottom: 3px solid #16a085;
            padding-bottom: 10px;
        }
        .success {
            background: #d4edda;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin: 10px 0;
            border-radius: 4px;
            color: #155724;
        }
        .error {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            padding: 15px;
            margin: 10px 0;
            border-radius: 4px;
            color: #721c24;
        }
        .info {
            background: #d1ecf1;
            border-left: 4px solid #17a2b8;
            padding: 15px;
            margin: 10px 0;
            border-radius: 4px;
            color: #0c5460;
        }
        .warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 10px 0;
            border-radius: 4px;
            color: #856404;
        }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #16a085;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .btn:hover {
            background: #138d75;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🌟 Installation du Système de Notation des Diététiciens</h1>
';

// Check if table already exists
$table_name = $db_prefix . 'dietic_ratings';
$check_query = "SHOW TABLES LIKE '$table_name'";
$result = $mysqli->query($check_query);

if ($result->num_rows > 0) {
    echo '<div class="warning">';
    echo '<strong>⚠️ Table déjà existante</strong><br>';
    echo 'La table <code>' . $table_name . '</code> existe déjà dans votre base de données.';
    echo '</div>';

    // Show table structure
    echo '<div class="info">';
    echo '<strong>📋 Structure de la table :</strong><br>';
    $structure = $mysqli->query("DESCRIBE $table_name");
    echo '<table border="1" cellpadding="5" style="margin-top: 10px; border-collapse: collapse;">';
    echo '<tr><th>Colonne</th><th>Type</th><th>Null</th><th>Clé</th></tr>';
    while ($row = $structure->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . $row['Field'] . '</td>';
        echo '<td>' . $row['Type'] . '</td>';
        echo '<td>' . $row['Null'] . '</td>';
        echo '<td>' . $row['Key'] . '</td>';
        echo '</tr>';
    }
    echo '</table>';
    echo '</div>';

} else {
    echo '<div class="info">';
    echo '<strong>ℹ️ Installation en cours...</strong><br>';
    echo 'Création de la table <code>' . $table_name . '</code>...';
    echo '</div>';

    // Read SQL file
    $sql_file = __DIR__ . '/migrations/add_ratings_table.sql';

    if (!file_exists($sql_file)) {
        echo '<div class="error">';
        echo '<strong>❌ Erreur</strong><br>';
        echo 'Fichier SQL introuvable : ' . $sql_file;
        echo '</div>';
    } else {
        $sql = file_get_contents($sql_file);

        // Replace table prefix
        $sql = str_replace('`tbldietic_', '`' . $db_prefix . 'dietic_', $sql);

        // Execute SQL
        $success = true;
        $statements = array_filter(array_map('trim', explode(';', $sql)));

        foreach ($statements as $statement) {
            if (!empty($statement)) {
                if ($mysqli->query($statement)) {
                    echo '<div class="success">✓ ' . substr($statement, 0, 50) . '...</div>';
                } else {
                    echo '<div class="error">✗ Erreur : ' . $mysqli->error . '</div>';
                    $success = false;
                }
            }
        }

        // Add foreign keys
        echo '<br><strong>Ajout des contraintes de clés étrangères...</strong><br>';

        $foreign_keys = [
            "ALTER TABLE `{$table_name}`
             ADD CONSTRAINT `fk_diet_ratings_patient`
             FOREIGN KEY (`patient_id`) REFERENCES `{$db_prefix}dietic_patients`(`id`) ON DELETE CASCADE",

            "ALTER TABLE `{$table_name}`
             ADD CONSTRAINT `fk_diet_ratings_staff`
             FOREIGN KEY (`dietitian_id`) REFERENCES `{$db_prefix}staff`(`staffid`) ON DELETE CASCADE"
        ];

        foreach ($foreign_keys as $fk_sql) {
            if ($mysqli->query($fk_sql)) {
                echo '<div class="success">✓ Clé étrangère ajoutée</div>';
            } else {
                // Foreign key might already exist, that's OK
                if (strpos($mysqli->error, 'Duplicate') === false && strpos($mysqli->error, 'already exists') === false) {
                    echo '<div class="warning">⚠️ ' . $mysqli->error . '</div>';
                } else {
                    echo '<div class="info">ℹ️ Clé étrangère existe déjà (ignoré)</div>';
                }
            }
        }

        if ($success) {
            echo '<br><div class="success">';
            echo '<strong>✅ Installation réussie !</strong><br>';
            echo 'La table <code>' . $table_name . '</code> a été créée avec succès.';
            echo '</div>';
        }
    }
}

// Close connection
$mysqli->close();

echo '
        <div class="info" style="margin-top: 30px;">
            <strong>📚 Prochaines étapes :</strong><br>
            <ol>
                <li>Accédez à la <a href="/admin/dietetic/dietitians">liste des diététiciens</a></li>
                <li>Consultez les profils des diététiciens</li>
                <li>Connectez-vous en tant que patient pour noter votre diététicien</li>
            </ol>
        </div>

        <a href="/admin/dietetic/dietitians" class="btn">🎯 Accéder aux Diététiciens</a>
    </div>
</body>
</html>
';
?>
