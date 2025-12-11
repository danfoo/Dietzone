<?php
/**
 * Test d'authentification - Diagnostic
 * URL: /modules/dietetic/test_auth.php
 */

// Charger Perfex CRM
require_once(__DIR__ . '/../../autoload.php');
require_once(__DIR__ . '/../../config/app.php');

$CI =& get_instance();
$CI->load->database();
$CI->load->library('session');

// Charger le helper
$CI->load->helper('dietetic/dietetic');

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Test Authentification</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; }
        h1 { color: #01807B; }
        .success { color: #28a745; background: #d4edda; padding: 10px; margin: 10px 0; border-left: 4px solid #28a745; }
        .error { color: #dc3545; background: #f8d7da; padding: 10px; margin: 10px 0; border-left: 4px solid #dc3545; }
        .info { color: #004085; background: #d1ecf1; padding: 10px; margin: 10px 0; border-left: 4px solid #17a2b8; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; font-weight: 600; }
        code { background: #f8f9fa; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Test d'Authentification Client</h1>

        <h2>1. Variables de Session</h2>
        <table>
            <tr>
                <th>Variable</th>
                <th>Valeur</th>
                <th>Type</th>
            </tr>
            <tr>
                <td><code>client_logged_in</code></td>
                <td><?php echo $CI->session->userdata('client_logged_in') ? '<strong>TRUE</strong>' : 'FALSE'; ?></td>
                <td><?php echo gettype($CI->session->userdata('client_logged_in')); ?></td>
            </tr>
            <tr>
                <td><code>client_user_id</code></td>
                <td><?php echo $CI->session->userdata('client_user_id') ?: '(vide)'; ?></td>
                <td><?php echo gettype($CI->session->userdata('client_user_id')); ?></td>
            </tr>
        </table>

        <h2>2. Fonctions d'Authentification</h2>

        <?php
        echo '<h3>is_client_logged_in()</h3>';
        if (function_exists('is_client_logged_in')) {
            $result = is_client_logged_in();
            if ($result) {
                echo '<div class="success">✓ Fonction existe et retourne: <strong>TRUE</strong></div>';
            } else {
                echo '<div class="error">✗ Fonction existe mais retourne: <strong>FALSE</strong></div>';
            }
        } else {
            echo '<div class="error">✗ Fonction is_client_logged_in() n\'existe pas!</div>';
        }

        echo '<h3>get_client_user_id()</h3>';
        if (function_exists('get_client_user_id')) {
            $user_id = get_client_user_id();
            if ($user_id) {
                echo '<div class="success">✓ Fonction existe et retourne: <strong>' . $user_id . '</strong></div>';
            } else {
                echo '<div class="error">✗ Fonction existe mais retourne: <strong>NULL/FALSE</strong></div>';
            }
        } else {
            echo '<div class="error">✗ Fonction get_client_user_id() n\'existe pas!</div>';
        }
        ?>

        <h2>3. Vérification Patient</h2>
        <?php
        if (function_exists('get_client_user_id') && get_client_user_id()) {
            $client_id = get_client_user_id();

            $CI->load->model('dietetic/dietetic_patients_model');
            try {
                $patient = $CI->dietetic_patients_model->get_by_client($client_id);
                if ($patient) {
                    echo '<div class="success">✓ Patient trouvé!</div>';
                    echo '<table>';
                    echo '<tr><th>Propriété</th><th>Valeur</th></tr>';
                    echo '<tr><td>Patient ID</td><td>' . $patient->id . '</td></tr>';
                    echo '<tr><td>Client ID</td><td>' . $patient->client_id . '</td></tr>';
                    echo '<tr><td>Status</td><td>' . $patient->status . '</td></tr>';
                    echo '</table>';
                } else {
                    echo '<div class="error">✗ Aucun patient trouvé pour client_id: ' . $client_id . '</div>';
                }
            } catch (Exception $e) {
                echo '<div class="error">✗ Erreur: ' . htmlspecialchars($e->getMessage()) . '</div>';
            }
        } else {
            echo '<div class="info">Client non connecté - impossible de chercher le patient</div>';
        }
        ?>

        <h2>4. Toutes les Variables de Session</h2>
        <pre><?php print_r($CI->session->all_userdata()); ?></pre>

        <h2>5. Test de Redirection</h2>
        <?php if (function_exists('is_client_logged_in') && is_client_logged_in()): ?>
            <div class="success">
                ✓ Vous êtes connecté!
                <a href="<?php echo site_url('dietetic/portal/invoices'); ?>" style="display:inline-block;padding:10px 20px;background:#01807B;color:white;text-decoration:none;border-radius:4px;margin-top:10px;">
                    Tester la page Factures →
                </a>
            </div>
        <?php else: ?>
            <div class="error">
                ✗ Vous n'êtes PAS connecté
                <a href="<?php echo site_url('dietetic/portal'); ?>" style="display:inline-block;padding:10px 20px;background:#01807B;color:white;text-decoration:none;border-radius:4px;margin-top:10px;">
                    Se connecter →
                </a>
            </div>
        <?php endif; ?>

        <hr style="margin: 30px 0;">
        <p style="text-align: center; color: #6c757d;">
            Test effectué à: <?php echo date('d/m/Y H:i:s'); ?>
        </p>
    </div>
</body>
</html>
