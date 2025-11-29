<?php
/**
 * Script de création de la table migrations et reset de la migration 009
 *
 * USAGE : Accédez à ce fichier via votre navigateur
 * URL : https://app.dietsenegal.net/modules/dietetic/migrations/create_table_and_reset.php
 *
 * Ce script :
 * 1. Crée la table tbldietic_migrations si elle n'existe pas
 * 2. Supprime l'enregistrement de la migration 009 pour permettre ré-exécution
 * 3. Affiche un rapport détaillé
 */

// Charger CodeIgniter
define('BASEPATH', true);
require_once(__DIR__ . '/../../../application/libraries/App_Controller.php');

$CI = &get_instance();

if (!$CI) {
    die('❌ Erreur : CodeIgniter non chargé. Vérifiez le chemin du fichier.');
}

// HTML Header
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Création Table Migrations & Reset</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .panel {
            background: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 3px solid #007bff;
            padding-bottom: 10px;
        }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            border-left: 4px solid #28a745;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            border-left: 4px solid #dc3545;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            border-left: 4px solid #17a2b8;
        }
        .step {
            background: #fff3cd;
            padding: 10px 15px;
            margin: 10px 0;
            border-radius: 5px;
            border-left: 4px solid #ffc107;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 5px;
            font-weight: bold;
        }
        .btn:hover {
            background: #0056b3;
        }
        .btn-success {
            background: #28a745;
        }
        .btn-success:hover {
            background: #1e7e34;
        }
        pre {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            border: 1px solid #dee2e6;
        }
    </style>
</head>
<body>
    <div class="panel">
        <h1>🔧 Création Table Migrations & Reset Migration 009</h1>

<?php

$success_count = 0;
$error_count = 0;
$messages = [];

// Étape 1 : Créer la table migrations
echo '<div class="step"><strong>Étape 1 :</strong> Création de la table tbldietic_migrations...</div>';

$create_table_sql = "
CREATE TABLE IF NOT EXISTS `" . db_prefix() . "dietic_migrations` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `migration_name` varchar(255) NOT NULL,
    `applied_at` datetime NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `migration_name` (`migration_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
";

try {
    $CI->db->query($create_table_sql);
    $success_count++;
    echo '<div class="success">✓ Table <code>' . db_prefix() . 'dietic_migrations</code> créée avec succès (ou existe déjà)</div>';
} catch (Exception $e) {
    $error_count++;
    echo '<div class="error">✗ Erreur lors de la création de la table : ' . htmlspecialchars($e->getMessage()) . '</div>';
}

// Étape 2 : Vérifier que la table existe
echo '<div class="step"><strong>Étape 2 :</strong> Vérification de l\'existence de la table...</div>';

$table_exists = $CI->db->table_exists(db_prefix() . 'dietic_migrations');

if ($table_exists) {
    echo '<div class="success">✓ La table existe bien dans la base de données</div>';

    // Étape 3 : Supprimer l'enregistrement de la migration 009
    echo '<div class="step"><strong>Étape 3 :</strong> Suppression de l\'enregistrement de la migration 009...</div>';

    try {
        $CI->db->where('migration_name', '009_add_recurring_payments_and_refunds');
        $CI->db->delete(db_prefix() . 'dietic_migrations');

        $affected_rows = $CI->db->affected_rows();

        if ($affected_rows > 0) {
            $success_count++;
            echo '<div class="success">✓ Enregistrement de la migration 009 supprimé avec succès (' . $affected_rows . ' ligne(s))</div>';
        } else {
            echo '<div class="info">ℹ Aucun enregistrement de migration 009 trouvé (c\'est normal si c\'est la première exécution)</div>';
        }
    } catch (Exception $e) {
        $error_count++;
        echo '<div class="error">✗ Erreur lors de la suppression : ' . htmlspecialchars($e->getMessage()) . '</div>';
    }

    // Étape 4 : Vérifier le contenu de la table
    echo '<div class="step"><strong>Étape 4 :</strong> Vérification des migrations enregistrées...</div>';

    try {
        $migrations = $CI->db->get(db_prefix() . 'dietic_migrations')->result();

        if (count($migrations) > 0) {
            echo '<div class="info">';
            echo '<strong>Migrations actuellement enregistrées :</strong><br>';
            echo '<ul>';
            foreach ($migrations as $migration) {
                echo '<li>' . htmlspecialchars($migration->migration_name) . ' (appliquée le ' . $migration->applied_at . ')</li>';
            }
            echo '</ul>';
            echo '</div>';
        } else {
            echo '<div class="info">ℹ Aucune migration enregistrée dans la base de données</div>';
        }
    } catch (Exception $e) {
        echo '<div class="error">✗ Erreur lors de la lecture : ' . htmlspecialchars($e->getMessage()) . '</div>';
    }

} else {
    $error_count++;
    echo '<div class="error">✗ La table n\'a pas pu être créée. Vérifiez les permissions de la base de données.</div>';
}

// Résumé
echo '<hr style="margin: 30px 0;">';
echo '<h2>📊 Résumé</h2>';

if ($error_count == 0) {
    echo '<div class="success">';
    echo '<strong>✓ Toutes les opérations réussies !</strong><br>';
    echo 'La migration 009 est maintenant prête à être exécutée.';
    echo '</div>';

    echo '<h3>🚀 Prochaines étapes :</h3>';
    echo '<ol>';
    echo '<li>Retournez sur la page des migrations</li>';
    echo '<li>Rafraîchissez la page (F5)</li>';
    echo '<li>Cliquez sur le bouton "Appliquer" pour la migration 009</li>';
    echo '</ol>';

    echo '<div style="text-align: center; margin-top: 30px;">';
    echo '<a href="' . admin_url('dietetic/migrations') . '" class="btn btn-success">➜ Aller aux Migrations</a>';
    echo '</div>';

} else {
    echo '<div class="error">';
    echo '<strong>✗ Des erreurs se sont produites</strong><br>';
    echo 'Nombre d\'erreurs : ' . $error_count;
    echo '</div>';

    echo '<div class="info">';
    echo '<strong>Solutions possibles :</strong><br>';
    echo '<ul>';
    echo '<li>Vérifiez que l\'utilisateur MySQL a les permissions CREATE et DELETE</li>';
    echo '<li>Vérifiez que la base de données est accessible</li>';
    echo '<li>Contactez votre administrateur système si le problème persiste</li>';
    echo '</ul>';
    echo '</div>';
}

// Informations de debug
echo '<hr style="margin: 30px 0;">';
echo '<h3>🔍 Informations de Debug</h3>';
echo '<pre>';
echo 'Nom de la table : ' . db_prefix() . 'dietic_migrations' . "\n";
echo 'Base de données : ' . $CI->db->database . "\n";
echo 'Préfixe des tables : ' . db_prefix() . "\n";
echo 'Version PHP : ' . phpversion() . "\n";
echo '</pre>';

?>

    </div>
</body>
</html>
