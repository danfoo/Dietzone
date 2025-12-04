<?php
/**
 * Migration Script: Add meal_type to food_survey_recommendations
 *
 * This script adds a meal_type column to allow recommendations per meal (breakfast, lunch, dinner)
 * instead of only global recommendations.
 *
 * Usage: Access this file via browser, then delete it after execution
 */

// Security: Enable/Disable script execution
define('MIGRATION_ENABLED', true);

if (!MIGRATION_ENABLED) {
    die('Migration is disabled. Set MIGRATION_ENABLED to true to execute.');
}

// Require Perfex CRM bootstrap
require_once(__DIR__ . '/../../application/config/app-config.php');

// Database connection
$db_hostname = defined('APP_DB_HOSTNAME') ? APP_DB_HOSTNAME : 'localhost';
$db_username = defined('APP_DB_USERNAME') ? APP_DB_USERNAME : '';
$db_password = defined('APP_DB_PASSWORD') ? APP_DB_PASSWORD : '';
$db_name = defined('APP_DB_NAME') ? APP_DB_NAME : '';

try {
    $pdo = new PDO(
        "mysql:host={$db_hostname};dbname={$db_name};charset=utf8mb4",
        $db_username,
        $db_password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
        ]
    );
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Migration: Add meal_type to Recommendations</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 800px;
            width: 100%;
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #01807B 0%, #019B95 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .header p {
            opacity: 0.9;
            font-size: 16px;
        }

        .content {
            padding: 30px;
        }

        .info-box {
            background: #f0f9ff;
            border-left: 4px solid #0ea5e9;
            padding: 20px;
            margin-bottom: 25px;
            border-radius: 8px;
        }

        .info-box h3 {
            color: #0369a1;
            margin-bottom: 10px;
            font-size: 18px;
        }

        .info-box p {
            color: #475569;
            line-height: 1.6;
        }

        .info-box ul {
            margin-top: 10px;
            padding-left: 20px;
        }

        .info-box li {
            margin: 5px 0;
            color: #475569;
        }

        .status {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .status.success {
            background: #d1fae5;
            color: #065f46;
            border-left: 4px solid #10b981;
        }

        .status.error {
            background: #fee2e2;
            color: #991b1b;
            border-left: 4px solid #ef4444;
        }

        .status.warning {
            background: #fef3c7;
            color: #92400e;
            border-left: 4px solid #f59e0b;
        }

        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #01807B 0%, #019B95 100%);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
            text-align: center;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(1, 128, 123, 0.3);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .code {
            background: #1e293b;
            color: #e2e8f0;
            padding: 15px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            overflow-x: auto;
            margin: 15px 0;
        }

        .footer {
            background: #f8fafc;
            padding: 20px 30px;
            text-align: center;
            color: #64748b;
            font-size: 14px;
        }

        .progress {
            width: 100%;
            height: 4px;
            background: #e2e8f0;
            border-radius: 2px;
            overflow: hidden;
            margin-top: 15px;
        }

        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #01807B 0%, #019B95 100%);
            width: 0;
            transition: width 0.3s ease;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔧 Migration de la Base de Données</h1>
            <p>Ajout du champ meal_type aux recommandations</p>
        </div>

        <div class="content">
            <?php
            $migration_executed = false;
            $migration_success = false;
            $error_message = '';

            if (isset($_POST['execute_migration'])) {
                $migration_executed = true;

                try {
                    // Start transaction
                    $pdo->beginTransaction();

                    // Check if column already exists
                    $stmt = $pdo->query("SHOW COLUMNS FROM `tbldietic_food_survey_recommendations` LIKE 'meal_type'");
                    $column_exists = $stmt->fetch();

                    if ($column_exists) {
                        throw new Exception("Le champ 'meal_type' existe déjà dans la table.");
                    }

                    // Add meal_type column
                    $sql = "ALTER TABLE `tbldietic_food_survey_recommendations`
                            ADD COLUMN `meal_type` ENUM('breakfast', 'lunch', 'dinner', 'global') DEFAULT 'global'
                            AFTER `entry_id`,
                            ADD INDEX `idx_meal_type` (`meal_type`)";

                    $pdo->exec($sql);

                    // Commit transaction
                    $pdo->commit();

                    $migration_success = true;
                } catch (Exception $e) {
                    // Rollback on error
                    if ($pdo->inTransaction()) {
                        $pdo->rollBack();
                    }
                    $error_message = $e->getMessage();
                }
            }

            // Display current status
            try {
                $stmt = $pdo->query("SHOW COLUMNS FROM `tbldietic_food_survey_recommendations` LIKE 'meal_type'");
                $column_exists = $stmt->fetch();
            } catch (Exception $e) {
                $column_exists = false;
            }
            ?>

            <?php if ($migration_executed): ?>
                <?php if ($migration_success): ?>
                    <div class="status success">
                        <strong>✅ Migration réussie!</strong>
                        <p>Le champ 'meal_type' a été ajouté avec succès à la table tbldietic_food_survey_recommendations.</p>
                    </div>
                <?php else: ?>
                    <div class="status error">
                        <strong>❌ Erreur lors de la migration</strong>
                        <p><?php echo htmlspecialchars($error_message); ?></p>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="info-box">
                <h3>📋 Informations sur cette migration</h3>
                <p>Cette migration ajoute la possibilité de créer des recommandations spécifiques par repas :</p>
                <ul>
                    <li><strong>breakfast</strong> - Recommandation pour le petit-déjeuner</li>
                    <li><strong>lunch</strong> - Recommandation pour le déjeuner</li>
                    <li><strong>dinner</strong> - Recommandation pour le dîner</li>
                    <li><strong>global</strong> - Recommandation globale pour toute la journée (par défaut)</li>
                </ul>
            </div>

            <?php if ($column_exists): ?>
                <div class="status success">
                    <strong>✓ État actuel</strong>
                    <p>Le champ 'meal_type' existe déjà dans la table. Migration déjà appliquée.</p>
                </div>
            <?php else: ?>
                <div class="status warning">
                    <strong>⚠ État actuel</strong>
                    <p>Le champ 'meal_type' n'existe pas encore. La migration doit être exécutée.</p>
                </div>

                <form method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir exécuter cette migration ?');">
                    <button type="submit" name="execute_migration" class="btn">
                        🚀 Exécuter la migration
                    </button>
                </form>
            <?php endif; ?>

            <div class="info-box" style="margin-top: 30px; background: #fef3c7; border-left-color: #f59e0b;">
                <h3>🔒 Sécurité importante</h3>
                <p>Après l'exécution réussie de cette migration :</p>
                <ul>
                    <li>Désactivez ce script en changeant <code>MIGRATION_ENABLED</code> à <code>false</code></li>
                    <li>Ou supprimez complètement ce fichier du serveur</li>
                </ul>
                <div class="code">rm /path/to/modules/dietetic/add_meal_type_to_recommendations.php</div>
            </div>
        </div>

        <div class="footer">
            <p>Migration Script - Food Surveys Meal-Specific Recommendations</p>
            <p>Date: <?php echo date('d/m/Y H:i:s'); ?></p>
        </div>
    </div>
</body>
</html>
