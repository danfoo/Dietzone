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

// Allow direct access for this specific file
define('BASEPATH', true);

// Load Perfex CRM
require_once(dirname(__FILE__) . '/../../../application/config/app-config.php');
require_once(dirname(__FILE__) . '/../../../application/libraries/App_Controller.php');

// Start CodeIgniter
$CI = &get_instance();
$CI->load->database();

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
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
        }
        .btn-execute:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(1, 128, 123, 0.3);
        }
        .execution-log {
            background: #2c3e50;
            color: #ecf0f1;
            padding: 20px;
            border-radius: 6px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            max-height: 500px;
            overflow-y: auto;
            margin-top: 20px;
        }
        .log-success { color: #2ecc71; }
        .log-error { color: #e74c3c; }
        .log-warning { color: #f39c12; }
        .log-info { color: #3498db; }
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
                            <li>Supprimer l'enregistrement actuel de la migration 009</li>
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
                            DELETE FROM tbldietic_migrations WHERE migration_name = '009_add_recurring_payments_and_refunds'
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
                            INSERT INTO tbldietic_migrations avec date d'application
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

                        // Step 1: Delete existing migration record
                        echo "<span class='log-info'>[INFO]</span> Étape 1/4 : Suppression enregistrement migration...\n";
                        $CI->db->where('migration_name', '009_add_recurring_payments_and_refunds');
                        $deleted = $CI->db->delete(db_prefix() . 'dietic_migrations');

                        if ($deleted) {
                            echo "<span class='log-success'>[OK]</span> Enregistrement migration supprimé\n\n";
                        } else {
                            echo "<span class='log-warning'>[WARN]</span> Aucun enregistrement à supprimer (normal si première exécution)\n\n";
                        }

                        // Step 2: Load SQL file
                        echo "<span class='log-info'>[INFO]</span> Étape 2/4 : Chargement fichier SQL...\n";
                        $sql_file = dirname(__FILE__) . '/add_recurring_payments_and_refunds.sql';

                        if (!file_exists($sql_file)) {
                            echo "<span class='log-error'>[ERROR]</span> Fichier SQL non trouvé : $sql_file\n";
                            $errors++;
                        } else {
                            $sql_content = file_get_contents($sql_file);
                            $file_size = number_format(filesize($sql_file) / 1024, 2);
                            echo "<span class='log-success'>[OK]</span> Fichier chargé ({$file_size} KB)\n\n";

                            // Step 3: Execute SQL
                            echo "<span class='log-info'>[INFO]</span> Étape 3/4 : Exécution requêtes SQL...\n";

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

                            echo "<span class='log-info'>[INFO]</span> " . count($statements) . " requêtes à exécuter\n\n";

                            foreach ($statements as $index => $statement) {
                                $statement = trim($statement);
                                if (empty($statement)) continue;

                                $num = $index + 1;

                                // Get statement type
                                if (preg_match('/^CREATE TABLE.*?`([^`]+)`/i', $statement, $matches)) {
                                    $type = "CREATE TABLE";
                                    $target = $matches[1];
                                } elseif (preg_match('/^ALTER TABLE.*?`([^`]+)`/i', $statement, $matches)) {
                                    $type = "ALTER TABLE";
                                    $target = $matches[1];
                                } elseif (preg_match('/^INSERT INTO.*?`([^`]+)`/i', $statement, $matches)) {
                                    $type = "INSERT INTO";
                                    $target = $matches[1];
                                } else {
                                    $type = "SQL";
                                    $target = substr($statement, 0, 50) . "...";
                                }

                                try {
                                    $result = $CI->db->query($statement);

                                    if ($result) {
                                        echo "<span class='log-success'>[OK {$num}]</span> {$type} {$target}\n";
                                        $success++;
                                    } else {
                                        $error = $CI->db->error();
                                        if ($error['code'] == 1060 || $error['code'] == 1061 || $error['code'] == 1050) {
                                            // Duplicate column/key/table - not critical
                                            echo "<span class='log-warning'>[SKIP {$num}]</span> {$type} {$target} (déjà existe)\n";
                                            $warnings++;
                                        } else {
                                            echo "<span class='log-error'>[ERROR {$num}]</span> {$type} {$target}\n";
                                            echo "<span class='log-error'>    └─ Code: {$error['code']}, Message: {$error['message']}</span>\n";
                                            $errors++;
                                        }
                                    }
                                } catch (Exception $e) {
                                    echo "<span class='log-error'>[ERROR {$num}]</span> {$type} {$target}\n";
                                    echo "<span class='log-error'>    └─ Exception: {$e->getMessage()}</span>\n";
                                    $errors++;
                                }
                            }

                            echo "\n";

                            // Step 4: Register migration
                            echo "<span class='log-info'>[INFO]</span> Étape 4/4 : Enregistrement migration...\n";
                            $migration_data = [
                                'migration_name' => '009_add_recurring_payments_and_refunds',
                                'applied_at' => date('Y-m-d H:i:s')
                            ];

                            if ($CI->db->insert(db_prefix() . 'dietic_migrations', $migration_data)) {
                                echo "<span class='log-success'>[OK]</span> Migration enregistrée dans la base de données\n\n";
                            } else {
                                echo "<span class='log-error'>[ERROR]</span> Échec enregistrement migration\n\n";
                                $errors++;
                            }
                        }

                        // Summary
                        echo "================================================================================\n";
                        echo "<span class='log-info'>[RÉSUMÉ]</span> Exécution terminée\n\n";
                        echo "<span class='log-success'>[✓]</span> Succès : {$success}\n";

                        if ($warnings > 0) {
                            echo "<span class='log-warning'>[⚠]</span> Ignorés : {$warnings} (déjà existants)\n";
                        }

                        if ($errors > 0) {
                            echo "<span class='log-error'>[✗]</span> Erreurs : {$errors}\n";
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
                                    <li><?php echo $warnings; ?> éléments ignorés (déjà existants)</li>
                                <?php endif; ?>
                            </ul>
                        </div>

                        <div style="text-align: center; margin-top: 30px;">
                            <a href="<?php echo admin_url('dietetic/migrations'); ?>" class="btn btn-primary btn-lg">
                                <i class="fa fa-arrow-left"></i> Retour à la page Migrations
                            </a>
                        </div>

                        <div class="alert alert-warning" style="margin-top: 20px;">
                            <i class="fa fa-trash"></i>
                            <strong>IMPORTANT :</strong> Supprimez ce fichier maintenant !
                            <br>
                            <code style="background: rgba(0,0,0,0.1); padding: 2px 8px; border-radius: 3px; margin-top: 10px; display: inline-block;">
                                rm /home/user/Dietzone/modules/dietetic/migrations/force_execute_009.php
                            </code>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-danger" style="margin-top: 20px;">
                            <i class="fa fa-exclamation-triangle"></i>
                            <strong>Erreurs détectées !</strong>
                            <br>
                            Consultez le log ci-dessus pour plus de détails.
                        </div>

                        <div style="text-align: center; margin-top: 20px;">
                            <a href="?execute=yes" class="btn btn-warning">
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
            <small>Force Execute Migration 009 - Dietzone Project</small>
        </div>
    </div>
</body>
</html>
