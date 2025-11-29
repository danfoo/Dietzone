<?php
/**
 * Debug Views - Dietetic Module
 *
 * URL: https://app.dietsenegal.net/modules/dietetic/debug_views.php
 *
 * Ce fichier permet de débugger les problèmes avec les vues admin.
 */

define('BASEPATH', TRUE);
$config_path = dirname(__FILE__) . '/../../application/config/app-config.php';

if (!file_exists($config_path)) {
    die("Erreur: Fichier de configuration non trouvé");
}

require_once($config_path);

// Connexion PDO
try {
    $dsn = "mysql:host=" . APP_DB_HOSTNAME . ";dbname=" . APP_DB_NAME . ";charset=utf8mb4";
    $pdo = new PDO($dsn, APP_DB_USERNAME, APP_DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données: " . $e->getMessage());
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
    <title>Debug Views - Dietetic Module</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body {
            background: #f5f5f5;
            padding: 30px;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        .debug-panel {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .debug-panel h3 {
            margin-top: 0;
            color: #01807B;
            border-bottom: 2px solid #01807B;
            padding-bottom: 10px;
        }
        .status-ok {
            color: #28a745;
            font-weight: bold;
        }
        .status-error {
            color: #dc3545;
            font-weight: bold;
        }
        .status-warning {
            color: #ffc107;
            font-weight: bold;
        }
        .code-block {
            background: #2c3e50;
            color: #ecf0f1;
            padding: 15px;
            border-radius: 6px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            overflow-x: auto;
        }
        table {
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fa fa-bug"></i> Debug Views - Dietetic Module</h1>
        <p class="lead">Diagnostic des vues Admin pour Recurring Payments et Refunds</p>

        <!-- Check 1: Database Tables -->
        <div class="debug-panel">
            <h3><i class="fa fa-database"></i> 1. Vérification Tables Base de Données</h3>

            <?php
            $tables_to_check = [
                'dietic_recurring_payments',
                'dietic_recurring_payment_transactions',
                'dietic_refunds'
            ];

            foreach ($tables_to_check as $table) {
                $full_table = db_prefix() . $table;
                try {
                    $stmt = $pdo->query("SHOW TABLES LIKE '$full_table'");
                    $exists = $stmt->rowCount() > 0;

                    if ($exists) {
                        // Count records
                        $stmt = $pdo->query("SELECT COUNT(*) as count FROM $full_table");
                        $count = $stmt->fetch(PDO::FETCH_OBJ)->count;
                        echo "<p><span class='status-ok'>✓</span> Table <strong>$full_table</strong> existe ($count enregistrement(s))</p>";
                    } else {
                        echo "<p><span class='status-error'>✗</span> Table <strong>$full_table</strong> n'existe pas</p>";
                    }
                } catch (PDOException $e) {
                    echo "<p><span class='status-error'>✗</span> Erreur: " . $e->getMessage() . "</p>";
                }
            }
            ?>
        </div>

        <!-- Check 2: View Files -->
        <div class="debug-panel">
            <h3><i class="fa fa-file-code-o"></i> 2. Vérification Fichiers de Vues</h3>

            <?php
            $views_to_check = [
                'admin/recurring_payments/manage.php',
                'admin/recurring_payments/view.php',
                'admin/recurring_payments/form.php',
                'admin/recurring_payments/cancel.php',
                'admin/refunds/manage.php',
                'admin/refunds/view.php',
                'admin/refunds/form.php',
                'admin/refunds/approve.php',
                'admin/refunds/reject.php',
                'admin/refunds/process.php',
                'admin/refunds/cancel.php'
            ];

            foreach ($views_to_check as $view) {
                $file_path = dirname(__FILE__) . '/views/' . $view;
                if (file_exists($file_path)) {
                    $size = filesize($file_path);
                    $lines = count(file($file_path));
                    echo "<p><span class='status-ok'>✓</span> <strong>$view</strong> ($lines lignes, " . number_format($size) . " octets)</p>";
                } else {
                    echo "<p><span class='status-error'>✗</span> <strong>$view</strong> n'existe pas</p>";
                }
            }
            ?>
        </div>

        <!-- Check 3: Controller Files -->
        <div class="debug-panel">
            <h3><i class="fa fa-cogs"></i> 3. Vérification Controllers</h3>

            <?php
            $controllers = [
                'Recurring_payments.php',
                'Refunds.php'
            ];

            foreach ($controllers as $controller) {
                $file_path = dirname(__FILE__) . '/controllers/' . $controller;
                if (file_exists($file_path)) {
                    $size = filesize($file_path);
                    $lines = count(file($file_path));

                    // Check for problematic method calls
                    $content = file_get_contents($file_path);
                    $issues = [];

                    if (strpos($content, 'get_recurring_statistics') !== false) {
                        $issues[] = "Appel à get_recurring_statistics() (méthode inexistante)";
                    }
                    if (strpos($content, 'count_by_status') !== false && strpos($content, '// Count pending') === false) {
                        $issues[] = "Appel à count_by_status() (méthode inexistante)";
                    }

                    echo "<p><span class='status-ok'>✓</span> <strong>$controller</strong> ($lines lignes)</p>";

                    if (!empty($issues)) {
                        echo "<div class='alert alert-warning'>";
                        echo "<strong>Problèmes potentiels:</strong><ul>";
                        foreach ($issues as $issue) {
                            echo "<li>$issue</li>";
                        }
                        echo "</ul></div>";
                    }
                } else {
                    echo "<p><span class='status-error'>✗</span> <strong>$controller</strong> n'existe pas</p>";
                }
            }
            ?>
        </div>

        <!-- Check 4: Model Files -->
        <div class="debug-panel">
            <h3><i class="fa fa-cube"></i> 4. Vérification Modèles</h3>

            <?php
            $models = [
                'Dietetic_recurring_payments_model.php',
                'Dietetic_refunds_model.php'
            ];

            foreach ($models as $model) {
                $file_path = dirname(__FILE__) . '/models/' . $model;
                if (file_exists($file_path)) {
                    $content = file_get_contents($file_path);
                    $lines = count(file($file_path));

                    // Check for important methods
                    $methods = [];
                    if (strpos($content, 'function get(') !== false) $methods[] = 'get()';
                    if (strpos($content, 'function get_all(') !== false) $methods[] = 'get_all()';
                    if (strpos($content, 'function get_transactions(') !== false) $methods[] = 'get_transactions()';
                    if (strpos($content, 'function add(') !== false) $methods[] = 'add()';
                    if (strpos($content, 'function update(') !== false) $methods[] = 'update()';

                    echo "<p><span class='status-ok'>✓</span> <strong>$model</strong> ($lines lignes)</p>";
                    echo "<p style='margin-left: 30px;'><small>Méthodes: " . implode(', ', $methods) . "</small></p>";
                } else {
                    echo "<p><span class='status-error'>✗</span> <strong>$model</strong> n'existe pas</p>";
                }
            }
            ?>
        </div>

        <!-- Check 5: Sample Data -->
        <div class="debug-panel">
            <h3><i class="fa fa-list"></i> 5. Données de Test</h3>

            <?php
            // Check if there's any data
            echo "<h4>Recurring Payments:</h4>";
            try {
                $stmt = $pdo->query("SELECT * FROM " . db_prefix() . "dietic_recurring_payments LIMIT 3");
                $payments = $stmt->fetchAll(PDO::FETCH_OBJ);

                if (!empty($payments)) {
                    echo "<table class='table table-bordered table-sm'>";
                    echo "<tr><th>ID</th><th>Patient ID</th><th>Amount</th><th>Frequency</th><th>Status</th></tr>";
                    foreach ($payments as $p) {
                        echo "<tr>";
                        echo "<td>$p->id</td>";
                        echo "<td>$p->patient_id</td>";
                        echo "<td>$p->amount</td>";
                        echo "<td>$p->frequency</td>";
                        echo "<td>$p->status</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p class='text-muted'><em>Aucun paiement récurrent dans la base</em></p>";
                }
            } catch (PDOException $e) {
                echo "<p class='status-error'>Erreur: " . $e->getMessage() . "</p>";
            }

            echo "<h4>Refunds:</h4>";
            try {
                $stmt = $pdo->query("SELECT * FROM " . db_prefix() . "dietic_refunds LIMIT 3");
                $refunds = $stmt->fetchAll(PDO::FETCH_OBJ);

                if (!empty($refunds)) {
                    echo "<table class='table table-bordered table-sm'>";
                    echo "<tr><th>ID</th><th>Patient ID</th><th>Amount</th><th>Type</th><th>Status</th></tr>";
                    foreach ($refunds as $r) {
                        echo "<tr>";
                        echo "<td>$r->id</td>";
                        echo "<td>$r->patient_id</td>";
                        echo "<td>$r->refund_amount</td>";
                        echo "<td>$r->refund_type</td>";
                        echo "<td>$r->status</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p class='text-muted'><em>Aucun remboursement dans la base</em></p>";
                }
            } catch (PDOException $e) {
                echo "<p class='status-error'>Erreur: " . $e->getMessage() . "</p>";
            }
            ?>
        </div>

        <!-- Check 6: Recommendations -->
        <div class="debug-panel">
            <h3><i class="fa fa-lightbulb-o"></i> 6. Recommandations</h3>

            <div class="alert alert-info">
                <strong>Pour tester les pages:</strong>
                <ul>
                    <li>Liste Recurring Payments: <code>/admin/dietetic/recurring_payments</code></li>
                    <li>Liste Refunds: <code>/admin/dietetic/refunds</code></li>
                </ul>
            </div>

            <div class="alert alert-warning">
                <strong>Si erreur 500 persiste:</strong>
                <ol>
                    <li>Vérifier les logs PHP du serveur</li>
                    <li>Activer le mode debug dans Perfex CRM</li>
                    <li>Vérifier que toutes les méthodes appelées existent dans les modèles</li>
                    <li>Vérifier les permissions fichiers (chmod 644 pour les vues)</li>
                </ol>
            </div>

            <div class="alert alert-success">
                <strong>Si tout fonctionne:</strong>
                <p>Supprimez ce fichier de debug pour des raisons de sécurité:</p>
                <code>rm modules/dietetic/debug_views.php</code>
            </div>
        </div>
    </div>
</body>
</html>
