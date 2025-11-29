<?php
/**
 * FORCE EXECUTION - Migration 009
 * Recurring Payments & Refunds
 *
 * URL: https://app.dietsenegal.net/modules/dietetic/migrations/force_execute_009.php
 *
 * ⚠️ ATTENTION : Ce fichier force l'exécution de la migration même si déjà marquée comme appliquée
 * Supprimez ce fichier après utilisation pour des raisons de sécurité
 */

// Chargement de la config Perfex
define('BASEPATH', TRUE);
$config_path = dirname(__FILE__) . '/../../../application/config/app-config.php';

if (!file_exists($config_path)) {
    die("Erreur: Fichier de configuration non trouvé");
}

require_once($config_path);

// Connexion directe à la base de données
try {
    $dsn = "mysql:host=" . APP_DB_HOSTNAME . ";dbname=" . APP_DB_NAME . ";charset=utf8mb4";
    $pdo = new PDO($dsn, APP_DB_USERNAME, APP_DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données: " . $e->getMessage());
}

// Définir le préfixe de table
if (!defined('APP_DB_PREFIX')) {
    define('APP_DB_PREFIX', 'tbl');
}

function db_prefix() {
    return defined('APP_DB_PREFIX') ? APP_DB_PREFIX : 'tbl';
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Force Migration 009 - Recurring Payments & Refunds</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body {
            background: #f5f5f5;
            padding: 30px;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .main-panel {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #01807B, #019B95);
            color: white;
            padding: 30px;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        .header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
        }
        .content {
            padding: 30px;
        }
        .alert {
            border-radius: 6px;
            border: none;
            padding: 15px 20px;
            margin-bottom: 20px;
        }
        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border-left: 4px solid #ffc107;
        }
        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border-left: 4px solid #17a2b8;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        .step-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 4px solid #01807B;
        }
        .step-number {
            display: inline-block;
            width: 32px;
            height: 32px;
            background: #01807B;
            color: white;
            border-radius: 50%;
            text-align: center;
            line-height: 32px;
            font-weight: 700;
            margin-right: 10px;
        }
        .btn-execute {
            background: linear-gradient(135deg, #01807B, #019B95);
            color: white;
            border: none;
            padding: 15px 40px;
            font-size: 18px;
            font-weight: 700;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-block;
            text-decoration: none;
        }
        .btn-execute:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(1, 128, 123, 0.3);
            color: white;
            text-decoration: none;
        }
        .execution-log {
            background: #2c3e50;
            color: #ecf0f1;
            padding: 20px;
            border-radius: 6px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            max-height: 600px;
            overflow-y: auto;
            margin-top: 20px;
            line-height: 1.6;
        }
        .log-success { color: #2ecc71; font-weight: bold; }
        .log-error { color: #e74c3c; font-weight: bold; }
        .log-warning { color: #f39c12; font-weight: bold; }
        .log-info { color: #3498db; font-weight: bold; }
        .log-separator {
            color: #7f8c8d;
            border-top: 1px solid #34495e;
            margin: 10px 0;
            padding-top: 10px;
        }
        .btn-back {
            background: #3498db;
            color: white;
            padding: 12px 30px;
            border-radius: 6px;
            text-decoration: none;
            display: inline-block;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-back:hover {
            background: #2980b9;
            color: white;
            text-decoration: none;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="main-panel">
            <div class="header">
                <h1><i class="fa fa-rocket"></i> Force Execute Migration 009</h1>
                <p>Recurring Payments & Refunds System</p>
            </div>

            <div class="content">
                <?php
                $execute = isset($_GET['execute']) && $_GET['execute'] == 'yes';

                if (!$execute) {
                    // Show confirmation screen
                    ?>
                    <div class="alert alert-warning">
                        <i class="fa fa-exclamation-triangle"></i>
                        <strong>ATTENTION :</strong> Cette page va <strong>forcer l'exécution</strong> de la migration 009 même si elle est déjà marquée comme appliquée.
                    </div>

                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i>
                        <strong>Cette migration va :</strong>
                        <ul style="margin: 10px 0 0 20px;">
                            <li>Supprimer l'enregistrement actuel de la migration 009 (si existe)</li>
                            <li>Créer 3 nouvelles tables (recurring_payments, transactions, refunds)</li>
                            <li>Modifier 3 tables existantes (subscriptions, payments, invoices)</li>
                            <li>Ajouter 8 nouveaux paramètres dans settings</li>
                        </ul>
                    </div>

                    <h3 style="margin-top: 30px; color: #2c3e50;">Étapes d'exécution :</h3>

                    <div class="step-card">
                        <span class="step-number">1</span>
                        <strong>Suppression enregistrement migration</strong>
                        <br>
                        <small style="color: #6c757d; margin-left: 42px;">
                            DELETE FROM <?php echo db_prefix(); ?>dietic_migrations WHERE migration_name = '009_add_recurring_payments_and_refunds'
                        </small>
                    </div>

                    <div class="step-card">
                        <span class="step-number">2</span>
                        <strong>Chargement fichier SQL</strong>
                        <br>
                        <small style="color: #6c757d; margin-left: 42px;">
                            add_recurring_payments_and_refunds.sql (8.2 KB)
                        </small>
                    </div>

                    <div class="step-card">
                        <span class="step-number">3</span>
                        <strong>Exécution requêtes SQL</strong>
                        <br>
                        <small style="color: #6c757d; margin-left: 42px;">
                            CREATE TABLE, ALTER TABLE, INSERT INTO (environ 50 requêtes)
                        </small>
                    </div>

                    <div class="step-card">
                        <span class="step-number">4</span>
                        <strong>Enregistrement migration</strong>
                        <br>
                        <small style="color: #6c757d; margin-left: 42px;">
                            INSERT INTO <?php echo db_prefix(); ?>dietic_migrations avec date d'application
                        </small>
                    </div>

                    <div style="text-align: center; margin-top: 40px;">
                        <a href="?execute=yes" class="btn-execute">
                            <i class="fa fa-rocket"></i> EXÉCUTER LA MIGRATION MAINTENANT
                        </a>
                    </div>

                    <div class="alert alert-warning" style="margin-top: 30px;">
                        <i class="fa fa-shield"></i>
                        <strong>Sécurité :</strong> Supprimez ce fichier après utilisation !
                        <br>
                        <code style="background: rgba(0,0,0,0.1); padding: 4px 8px; border-radius: 3px; margin-top: 8px; display: inline-block;">
                            rm modules/dietetic/migrations/force_execute_009.php
                        </code>
                    </div>
                    <?php
                } else {
                    // Execute migration
                    ?>
                    <div class="alert alert-info">
                        <i class="fa fa-cog fa-spin"></i> Exécution de la migration en cours...
                    </div>

                    <div class="execution-log">
                        <?php
                        $errors = 0;
                        $success = 0;
                        $warnings = 0;
                        $startTime = microtime(true);

                        echo "<span class='log-info'>[INFO]</span> Démarrage de la migration 009...\n";
                        echo "<span class='log-info'>[INFO]</span> Base de données: " . APP_DB_NAME . "\n";
                        echo "<span class='log-info'>[INFO]</span> Préfixe: " . db_prefix() . "\n";
                        echo "<div class='log-separator'></div>\n";

                        // Step 1: Delete existing migration record
                        echo "<span class='log-info'>[ÉTAPE 1/4]</span> Suppression enregistrement migration...\n";
                        try {
                            $stmt = $pdo->prepare("DELETE FROM " . db_prefix() . "dietic_migrations WHERE migration_name = ?");
                            $stmt->execute(['009_add_recurring_payments_and_refunds']);
                            $deleted = $stmt->rowCount();

                            if ($deleted > 0) {
                                echo "<span class='log-success'>[✓]</span> {$deleted} enregistrement(s) supprimé(s)\n";
                            } else {
                                echo "<span class='log-warning'>[⚠]</span> Aucun enregistrement à supprimer (normal si première exécution)\n";
                            }
                        } catch (PDOException $e) {
                            echo "<span class='log-error'>[✗]</span> Erreur: " . $e->getMessage() . "\n";
                            $errors++;
                        }
                        echo "<div class='log-separator'></div>\n";

                        // Step 2: Load SQL file
                        echo "<span class='log-info'>[ÉTAPE 2/4]</span> Chargement fichier SQL...\n";
                        $sql_file = dirname(__FILE__) . '/add_recurring_payments_and_refunds.sql';

                        if (!file_exists($sql_file)) {
                            echo "<span class='log-error'>[✗]</span> Fichier SQL non trouvé: $sql_file\n";
                            $errors++;
                        } else {
                            $sql_content = file_get_contents($sql_file);
                            $file_size = number_format(filesize($sql_file) / 1024, 2);
                            echo "<span class='log-success'>[✓]</span> Fichier chargé ({$file_size} KB)\n";

                            // Replace table prefix
                            $sql_content = str_replace('`tbldietic_', '`' . db_prefix() . 'dietic_', $sql_content);

                            // Remove comments
                            $sql_content = preg_replace('/^--.*$/m', '', $sql_content);
                            $sql_content = preg_replace('/\/\*.*?\*\//s', '', $sql_content);

                            // Split statements
                            $statements = array_filter(
                                array_map('trim', explode(';', $sql_content)),
                                function($stmt) { return !empty($stmt); }
                            );

                            echo "<span class='log-info'>[INFO]</span> " . count($statements) . " requêtes à exécuter\n";
                            echo "<div class='log-separator'></div>\n";

                            // Step 3: Execute SQL
                            echo "<span class='log-info'>[ÉTAPE 3/4]</span> Exécution requêtes SQL...\n\n";

                            foreach ($statements as $index => $statement) {
                                $statement = trim($statement);
                                if (empty($statement)) continue;

                                $num = $index + 1;

                                // Get statement type and target
                                if (preg_match('/^CREATE TABLE.*?`([^`]+)`/i', $statement, $matches)) {
                                    $type = "CREATE TABLE";
                                    $target = $matches[1];
                                } elseif (preg_match('/^ALTER TABLE.*?`([^`]+)`/i', $statement, $matches)) {
                                    $type = "ALTER TABLE";
                                    $target = $matches[1];
                                } elseif (preg_match('/^INSERT INTO.*?`([^`]+)`/i', $statement, $matches)) {
                                    $type = "INSERT";
                                    $target = $matches[1];
                                } else {
                                    $type = "SQL";
                                    $target = substr($statement, 0, 40) . "...";
                                }

                                try {
                                    $pdo->exec($statement);
                                    echo "<span class='log-success'>[✓ {$num}]</span> {$type} {$target}\n";
                                    $success++;
                                } catch (PDOException $e) {
                                    $errorCode = $e->getCode();
                                    $errorMsg = $e->getMessage();

                                    // Check if it's a duplicate error (not critical)
                                    if (strpos($errorMsg, 'Duplicate') !== false ||
                                        strpos($errorMsg, 'already exists') !== false ||
                                        $errorCode == '42S01' || // Table already exists
                                        $errorCode == '23000') { // Duplicate entry
                                        echo "<span class='log-warning'>[⚠ {$num}]</span> {$type} {$target} (déjà existe)\n";
                                        $warnings++;
                                    } else {
                                        echo "<span class='log-error'>[✗ {$num}]</span> {$type} {$target}\n";
                                        echo "<span class='log-error'>    └─ Erreur: {$errorMsg}</span>\n";
                                        $errors++;
                                    }
                                }
                            }

                            echo "<div class='log-separator'></div>\n";

                            // Step 4: Register migration
                            echo "<span class='log-info'>[ÉTAPE 4/4]</span> Enregistrement migration...\n";
                            try {
                                $stmt = $pdo->prepare("INSERT INTO " . db_prefix() . "dietic_migrations (migration_name, applied_at) VALUES (?, ?)");
                                $stmt->execute(['009_add_recurring_payments_and_refunds', date('Y-m-d H:i:s')]);
                                echo "<span class='log-success'>[✓]</span> Migration enregistrée dans la base de données\n";
                            } catch (PDOException $e) {
                                echo "<span class='log-error'>[✗]</span> Erreur enregistrement: " . $e->getMessage() . "\n";
                                $errors++;
                            }
                        }

                        $endTime = microtime(true);
                        $duration = round($endTime - $startTime, 2);

                        // Summary
                        echo "<div class='log-separator'></div>\n";
                        echo "================================================================================\n";
                        echo "<span class='log-info'>[RÉSUMÉ]</span> Exécution terminée en {$duration} secondes\n\n";
                        echo "<span class='log-success'>[✓]</span> Succès     : {$success}\n";

                        if ($warnings > 0) {
                            echo "<span class='log-warning'>[⚠]</span> Ignorés    : {$warnings} (déjà existants)\n";
                        }

                        if ($errors > 0) {
                            echo "<span class='log-error'>[✗]</span> Erreurs    : {$errors}\n";
                        }

                        echo "================================================================================\n";
                        ?>
                    </div>

                    <?php if ($errors == 0): ?>
                        <div class="alert alert-success" style="margin-top: 20px;">
                            <i class="fa fa-check-circle"></i>
                            <strong>Migration exécutée avec succès !</strong>
                            <ul style="margin: 10px 0 0 20px;">
                                <li><?php echo $success; ?> opérations réussies</li>
                                <?php if ($warnings > 0): ?>
                                    <li><?php echo $warnings; ?> éléments ignorés (déjà existants - normal)</li>
                                <?php endif; ?>
                                <li>Durée: <?php echo $duration; ?> secondes</li>
                            </ul>
                        </div>

                        <div style="text-align: center; margin-top: 30px;">
                            <a href="https://app.dietsenegal.net/admin/dietetic/migrations" class="btn-back">
                                <i class="fa fa-arrow-left"></i> Retour à la page Migrations
                            </a>
                        </div>

                        <div class="alert alert-warning" style="margin-top: 20px;">
                            <i class="fa fa-trash"></i>
                            <strong>IMPORTANT :</strong> Supprimez ce fichier maintenant pour des raisons de sécurité !
                            <br><br>
                            <strong>Via SSH :</strong>
                            <br>
                            <code style="background: #2c3e50; color: #2ecc71; padding: 8px 12px; border-radius: 3px; margin-top: 10px; display: inline-block; font-size: 14px;">
                                rm modules/dietetic/migrations/force_execute_009.php
                            </code>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-danger" style="margin-top: 20px;">
                            <i class="fa fa-exclamation-triangle"></i>
                            <strong><?php echo $errors; ?> erreur(s) détectée(s) !</strong>
                            <br>
                            Consultez le log ci-dessus pour plus de détails.
                        </div>

                        <div style="text-align: center; margin-top: 20px;">
                            <a href="?execute=yes" class="btn btn-warning" style="padding: 12px 30px; font-weight: 600;">
                                <i class="fa fa-refresh"></i> Réessayer
                            </a>
                        </div>
                    <?php endif; ?>
                    <?php
                }
                ?>
            </div>
        </div>

        <div style="text-align: center; margin-top: 20px; color: #6c757d;">
            <small>Force Execute Migration 009 - Dietzone Project - Phase 10</small>
        </div>
    </div>
</body>
</html>
