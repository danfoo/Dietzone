<?php
/**
 * Script d'installation de la Politique de Confidentialité
 *
 * Ce script insère automatiquement le contenu de la politique de confidentialité
 * dans la base de données Dietzone.
 *
 * INSTRUCTIONS:
 * 1. Accédez à ce script via votre navigateur: https://app.dietsenegal.net/modules/dietetic/install_privacy_policy.php
 * 2. Le script insérera automatiquement le contenu
 * 3. Supprimez ce fichier après utilisation pour des raisons de sécurité
 *
 * OU utilisez la méthode manuelle:
 * 1. Ouvrez le fichier PRIVACY_POLICY_CONTENT.html
 * 2. Copiez tout le contenu
 * 3. Allez dans Admin → Dietetic → Gestion des Pages Légales
 * 4. Collez le contenu dans le champ "Politique de Confidentialité"
 * 5. Cliquez sur "Enregistrer"
 */

// Sécurité: Ce script ne doit être exécuté qu'une seule fois
$lock_file = __DIR__ . '/.privacy_policy_installed';

if (file_exists($lock_file)) {
    die('<div style="padding: 20px; background: #fee2e2; color: #991b1b; border-left: 4px solid #dc2626;">
        <h2>⚠️ Script déjà exécuté</h2>
        <p>La politique de confidentialité a déjà été installée.</p>
        <p>Si vous souhaitez la réinstaller, supprimez le fichier <code>.privacy_policy_installed</code> et rechargez cette page.</p>
        <p><strong>Pour des raisons de sécurité, supprimez ce fichier après utilisation:</strong></p>
        <code>rm ' . __FILE__ . '</code>
    </div>');
}

// Charger le contenu HTML
$html_content = file_get_contents(__DIR__ . '/PRIVACY_POLICY_CONTENT.html');

if (!$html_content) {
    die('<div style="padding: 20px; background: #fee2e2; color: #991b1b;">
        <h2>❌ Erreur</h2>
        <p>Impossible de lire le fichier PRIVACY_POLICY_CONTENT.html</p>
    </div>');
}

// Charger Perfex
require_once(__DIR__ . '/../../application/libraries/App_modules.php');
require_once(__DIR__ . '/../../application/config/config.php');
require_once(__DIR__ . '/../../application/config/database.php');

// Créer une connexion à la base de données
$db = new mysqli(
    $db['default']['hostname'],
    $db['default']['username'],
    $db['default']['password'],
    $db['default']['database']
);

if ($db->connect_error) {
    die('<div style="padding: 20px; background: #fee2e2; color: #991b1b;">
        <h2>❌ Erreur de connexion</h2>
        <p>Impossible de se connecter à la base de données: ' . $db->connect_error . '</p>
    </div>');
}

// Échapper le contenu HTML
$escaped_content = $db->real_escape_string($html_content);

// Vérifier si l'entrée existe déjà
$check_query = "SELECT * FROM " . $db['default']['dbprefix'] . "dietic_settings WHERE setting_key = 'privacy_policy'";
$result = $db->query($check_query);

if ($result->num_rows > 0) {
    // Mettre à jour
    $update_query = "UPDATE " . $db['default']['dbprefix'] . "dietic_settings
                     SET setting_value = '{$escaped_content}', updated_at = NOW()
                     WHERE setting_key = 'privacy_policy'";

    if ($db->query($update_query)) {
        $message = '<h2>✅ Politique de Confidentialité mise à jour</h2>';
        $action = 'mis à jour';
    } else {
        die('<div style="padding: 20px; background: #fee2e2; color: #991b1b;">
            <h2>❌ Erreur lors de la mise à jour</h2>
            <p>' . $db->error . '</p>
        </div>');
    }
} else {
    // Insérer
    $insert_query = "INSERT INTO " . $db['default']['dbprefix'] . "dietic_settings
                     (setting_key, setting_value, created_at, updated_at)
                     VALUES ('privacy_policy', '{$escaped_content}', NOW(), NOW())";

    if ($db->query($insert_query)) {
        $message = '<h2>✅ Politique de Confidentialité installée</h2>';
        $action = 'installé';
    } else {
        die('<div style="padding: 20px; background: #fee2e2; color: #991b1b;">
            <h2>❌ Erreur lors de l\'insertion</h2>
            <p>' . $db->error . '</p>
        </div>');
    }
}

// Créer le fichier lock
file_put_contents($lock_file, date('Y-m-d H:i:s'));

// Fermer la connexion
$db->close();

// Afficher le message de succès
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation Politique de Confidentialité</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            width: 100%;
        }
        h2 {
            color: #059669;
            margin-top: 0;
        }
        .success-icon {
            font-size: 64px;
            text-align: center;
            margin-bottom: 20px;
        }
        .info {
            background: #f0f9ff;
            padding: 15px;
            border-left: 4px solid #0284c7;
            margin: 20px 0;
        }
        .warning {
            background: #fef3c7;
            padding: 15px;
            border-left: 4px solid #f59e0b;
            margin: 20px 0;
        }
        .danger {
            background: #fee2e2;
            padding: 15px;
            border-left: 4px solid #dc2626;
            margin: 20px 0;
        }
        a {
            color: #667eea;
            text-decoration: none;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            color: #c7254e;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 20px;
            text-align: center;
        }
        .btn:hover {
            background: #5568d3;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-icon">✅</div>
        <?php echo $message; ?>

        <div class="info">
            <p><strong>📄 Contenu <?php echo $action; ?> avec succès!</strong></p>
            <p>La politique de confidentialité est maintenant accessible à l'adresse:</p>
            <p><a href="https://app.dietsenegal.net/admin/dietetic/legal_pages/privacy" target="_blank">
                https://app.dietsenegal.net/admin/dietetic/legal_pages/privacy
            </a></p>
        </div>

        <div class="warning">
            <p><strong>⚠️ Actions recommandées:</strong></p>
            <ol>
                <li>Vérifiez le contenu sur la page publique</li>
                <li>Modifiez si nécessaire via <a href="https://app.dietsenegal.net/admin/dietetic/legal_pages/manage" target="_blank">la page de gestion</a></li>
                <li>Ajoutez ce lien dans votre app mobile (Play Store, settings, etc.)</li>
            </ol>
        </div>

        <div class="danger">
            <p><strong>🔒 IMPORTANT - Sécurité:</strong></p>
            <p>Pour des raisons de sécurité, supprimez immédiatement ce fichier d'installation:</p>
            <code>rm <?php echo __FILE__; ?></code>
            <p style="margin-top: 10px;">Ou via FTP/cPanel, supprimez:</p>
            <code>/modules/dietetic/install_privacy_policy.php</code>
        </div>

        <div style="text-align: center;">
            <a href="https://app.dietsenegal.net/admin/dietetic/legal_pages/privacy" class="btn" target="_blank">
                Voir la Politique de Confidentialité
            </a>
        </div>

        <p style="text-align: center; margin-top: 30px; color: #64748b; font-size: 14px;">
            &copy; 2024 Maestrodan - Dietzone
        </p>
    </div>
</body>
</html>
