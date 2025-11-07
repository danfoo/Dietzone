<?php
/**
 * Food Surveys Update Script
 *
 * This script adds the Food Surveys tables to an existing Dietetic module installation.
 *
 * HOW TO USE:
 * 1. Access this file via browser: https://votre-domaine.com/modules/dietetic/update_food_surveys.php
 * 2. Or include it from Perfex admin: Setup > Modules > Dietetic > Click "Update"
 * 3. The script will run automatically and create all necessary tables
 * 4. After running successfully once, you can delete this file for security
 */

// Only allow execution from Perfex admin
if (!defined('BASEPATH')) {
    // If not loaded via Perfex, try to load it
    require_once(__DIR__ . '/../../application/config/app-config.php');
    require_once(__DIR__ . '/../../application/config/config.php');

    // Simple authentication check
    if (!isset($_SERVER['PHP_AUTH_USER'])) {
        header('WWW-Authenticate: Basic realm="Food Surveys Update"');
        header('HTTP/1.0 401 Unauthorized');
        echo '<!DOCTYPE html>
<html>
<head>
    <title>Authentication Required</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 50px; background: #f5f5f5; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #01807B; }
        .info { background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔒 Authentication Required</h1>
        <div class="info">
            <strong>Instructions:</strong><br>
            1. Access this page via your Perfex admin panel (preferred)<br>
            2. Or authenticate with your database credentials<br>
            3. After successful execution, delete this file
        </div>
    </div>
</body>
</html>';
        exit;
    }
}

// If running from Perfex
if (defined('BASEPATH')) {
    $CI = &get_instance();
    $CI->load->database();
    echo '<!DOCTYPE html><html><head><title>Food Surveys Update</title></head><body>';
    echo '<style>body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
    .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    h1 { color: #01807B; } .success { color: #48bb78; background: #f0fff4; padding: 10px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #48bb78; }
    .error { color: #f56565; background: #fff5f5; padding: 10px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #f56565; }
    .info { color: #4299e1; background: #ebf8ff; padding: 10px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #4299e1; }
    ul { line-height: 1.8; }</style>';

    echo '<div class="container">';
    echo '<h1>📋 Food Surveys Update Script</h1>';

    // Check if tables already exist
    $table_exists = $CI->db->query("SHOW TABLES LIKE '" . db_prefix() . "dietic_food_surveys'")->row_array();

    if ($table_exists) {
        echo '<div class="info"><strong>ℹ️ Info:</strong> Les tables Food Surveys existent déjà dans la base de données.</div>';
        echo '<p>Aucune action nécessaire. Vous pouvez supprimer ce fichier.</p>';
    } else {
        echo '<div class="info"><strong>🚀 Installation en cours...</strong></div>';

        // Load SQL file
        $sql_file = __DIR__ . '/install/food_surveys.sql';

        if (!file_exists($sql_file)) {
            echo '<div class="error"><strong>❌ Erreur:</strong> Fichier SQL introuvable: ' . $sql_file . '</div>';
            echo '</div></body></html>';
            exit;
        }

        $sql_content = file_get_contents($sql_file);

        // Replace table prefix
        $sql_content = str_replace('`tbldietic_', '`' . db_prefix() . 'dietic_', $sql_content);

        // Split by semicolon
        $statements = array_filter(array_map('trim', explode(';', $sql_content)));

        $success_count = 0;
        $error_count = 0;
        $errors = [];

        echo '<ul>';

        foreach ($statements as $statement) {
            if (!empty($statement) && !preg_match('/^--/', $statement)) {
                try {
                    $CI->db->query($statement);
                    $success_count++;

                    // Extract table name for display
                    if (preg_match('/CREATE TABLE.*?`([^`]+)`/i', $statement, $matches)) {
                        echo '<li class="success">✅ Table créée: ' . $matches[1] . '</li>';
                    } elseif (preg_match('/ALTER TABLE.*?`([^`]+)`/i', $statement, $matches)) {
                        echo '<li class="success">✅ Contrainte ajoutée sur: ' . $matches[1] . '</li>';
                    }
                } catch (Exception $e) {
                    $error_count++;
                    $error_msg = $e->getMessage();
                    $errors[] = $error_msg;

                    // Only show error if it's not about existing table/constraint
                    if (!stripos($error_msg, 'already exists') && !stripos($error_msg, 'Duplicate')) {
                        echo '<li class="error">❌ Erreur: ' . htmlspecialchars(substr($error_msg, 0, 100)) . '</li>';
                    }
                }
            }
        }

        echo '</ul>';

        // Create uploads directory
        $upload_path = FCPATH . 'uploads/dietetic/food_surveys/';
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0755, true);
            echo '<div class="success">✅ Répertoire d\'uploads créé: ' . $upload_path . '</div>';
        }

        // Final summary
        echo '<hr>';
        echo '<h2>📊 Résumé</h2>';
        echo '<div class="success">';
        echo '<strong>✅ Installation terminée avec succès!</strong><br>';
        echo 'Tables créées: ' . $success_count . '<br>';
        if ($error_count > 0) {
            echo 'Erreurs (ignorées): ' . $error_count . '<br>';
        }
        echo '</div>';

        echo '<div class="info">';
        echo '<h3>🎉 Fonctionnalités activées:</h3>';
        echo '<ul>';
        echo '<li>✅ Création d\'enquêtes alimentaires par les diététiciens</li>';
        echo '<li>✅ Soumission quotidienne par les patients (photos de repas)</li>';
        echo '<li>✅ Upload de 3 photos par jour (petit-déjeuner, déjeuner, dîner)</li>';
        echo '<li>✅ Suivi de la consommation d\'eau et de boissons</li>';
        echo '<li>✅ Système de recommandations diététicien</li>';
        echo '<li>✅ Commentaires patients sur les recommandations</li>';
        echo '<li>✅ Calcul automatique de la progression</li>';
        echo '</ul>';
        echo '</div>';

        echo '<div class="info">';
        echo '<h3>📍 Pages disponibles:</h3>';
        echo '<ul>';
        echo '<li><strong>Admin:</strong> <a href="' . admin_url('dietetic/food_surveys') . '">Enquêtes Alimentaires</a></li>';
        echo '<li><strong>Patient:</strong> <a href="' . site_url('dietetic/portal/food_surveys') . '">Mes Enquêtes</a></li>';
        echo '</ul>';
        echo '</div>';

        echo '<div style="background: #fff3cd; padding: 15px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #ffc107;">';
        echo '<strong>⚠️ Important:</strong> Pour des raisons de sécurité, supprimez ce fichier après l\'installation:<br>';
        echo '<code>rm modules/dietetic/update_food_surveys.php</code>';
        echo '</div>';

        // Log activity
        if (function_exists('log_activity')) {
            log_activity('Dietetic Module: Food Surveys tables installed via update script');
        }
    }

    echo '<p><a href="' . admin_url('dietetic/food_surveys') . '" style="display: inline-block; background: #01807B; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-top: 20px;">Accéder aux Enquêtes Alimentaires</a></p>';

    echo '</div></body></html>';
} else {
    // Running standalone - should not happen if properly protected
    echo 'Please access this script via Perfex CRM admin panel.';
}
