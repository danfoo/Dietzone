<?php
/**
 * Test de persistance des sessions
 * URL: /modules/dietetic/test_session.php?action={set|check|destroy}
 */

// Charger Perfex CRM
require_once(__DIR__ . '/../../autoload.php');
require_once(__DIR__ . '/../../config/app.php');

$CI =& get_instance();
$CI->load->library('session');

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Test Session</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; }
        h1 { color: #01807B; }
        .success { color: #28a745; background: #d4edda; padding: 10px; margin: 10px 0; border-left: 4px solid #28a745; }
        .error { color: #dc3545; background: #f8d7da; padding: 10px; margin: 10px 0; border-left: 4px solid #dc3545; }
        .info { color: #004085; background: #d1ecf1; padding: 10px; margin: 10px 0; border-left: 4px solid #17a2b8; }
        code { background: #f8f9fa; padding: 2px 6px; border-radius: 3px; }
        .btn { display: inline-block; padding: 10px 20px; background: #01807B; color: white; text-decoration: none; border-radius: 6px; margin: 5px; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; font-weight: 600; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Test de Persistance des Sessions</h1>

        <?php
        $action = $_GET['action'] ?? 'check';

        // Session ID
        $session_id = session_id() ?: 'Session non démarrée';

        if ($action === 'set') {
            // Set test session variables
            $CI->session->set_userdata('test_timestamp', time());
            $CI->session->set_userdata('test_value', 'Hello World!');
            $CI->session->set_userdata('client_logged_in', true);
            $CI->session->set_userdata('client_user_id', 999);

            echo '<div class="success">✓ Variables de session définies avec succès!</div>';
            echo '<p>Timestamp: <strong>' . time() . '</strong></p>';
            echo '<p>Maintenant, cliquez sur "Vérifier la session" pour voir si les variables persistent.</p>';

        } elseif ($action === 'destroy') {
            // Destroy session
            $CI->session->sess_destroy();

            echo '<div class="info">Session détruite</div>';

        } else {
            // Check session
            $test_timestamp = $CI->session->userdata('test_timestamp');
            $test_value = $CI->session->userdata('test_value');
            $client_logged_in = $CI->session->userdata('client_logged_in');
            $client_user_id = $CI->session->userdata('client_user_id');

            if ($test_timestamp) {
                $age = time() - $test_timestamp;
                echo '<div class="success">✓ Session fonctionne! Les variables persistent.</div>';
                echo '<p>La session a été créée il y a <strong>' . $age . ' secondes</strong></p>';
            } else {
                echo '<div class="error">✗ Aucune variable de test trouvée. Cliquez sur "Définir la session" d\'abord.</div>';
            }
        }
        ?>

        <h2>Actions</h2>
        <div>
            <a href="?action=set" class="btn">1. Définir la session</a>
            <a href="?action=check" class="btn">2. Vérifier la session</a>
            <a href="?action=destroy" class="btn">3. Détruire la session</a>
        </div>

        <h2>Informations de Session</h2>
        <table>
            <tr>
                <th>Propriété</th>
                <th>Valeur</th>
            </tr>
            <tr>
                <td><code>Session ID</code></td>
                <td><?php echo $session_id; ?></td>
            </tr>
            <tr>
                <td><code>test_timestamp</code></td>
                <td><?php echo $CI->session->userdata('test_timestamp') ?: '(vide)'; ?></td>
            </tr>
            <tr>
                <td><code>test_value</code></td>
                <td><?php echo $CI->session->userdata('test_value') ?: '(vide)'; ?></td>
            </tr>
            <tr>
                <td><code>client_logged_in</code></td>
                <td><?php echo var_export($CI->session->userdata('client_logged_in'), true); ?></td>
            </tr>
            <tr>
                <td><code>client_user_id</code></td>
                <td><?php echo $CI->session->userdata('client_user_id') ?: '(vide)'; ?></td>
            </tr>
        </table>

        <h2>Toutes les Variables de Session</h2>
        <pre><?php print_r($CI->session->all_userdata()); ?></pre>

        <h2>Configuration Session (php.ini)</h2>
        <table>
            <tr>
                <td><code>session.cookie_path</code></td>
                <td><?php echo ini_get('session.cookie_path'); ?></td>
            </tr>
            <tr>
                <td><code>session.cookie_domain</code></td>
                <td><?php echo ini_get('session.cookie_domain') ?: '(vide)'; ?></td>
            </tr>
            <tr>
                <td><code>session.cookie_lifetime</code></td>
                <td><?php echo ini_get('session.cookie_lifetime'); ?> secondes</td>
            </tr>
            <tr>
                <td><code>session.cookie_httponly</code></td>
                <td><?php echo ini_get('session.cookie_httponly') ? 'true' : 'false'; ?></td>
            </tr>
            <tr>
                <td><code>session.cookie_secure</code></td>
                <td><?php echo ini_get('session.cookie_secure') ? 'true' : 'false'; ?></td>
            </tr>
            <tr>
                <td><code>session.save_path</code></td>
                <td><?php echo ini_get('session.save_path') ?: '(vide)'; ?></td>
            </tr>
        </table>

        <h2>Test de Navigation</h2>
        <div class="info">
            <p>Pour tester la persistance:</p>
            <ol>
                <li>Cliquez sur "Définir la session"</li>
                <li>Attendez 2 secondes</li>
                <li>Cliquez sur "Vérifier la session"</li>
                <li>Si la session fonctionne, vous verrez le timestamp et les variables</li>
                <li>Si la session ne persiste pas, les variables seront vides</li>
            </ol>
            <p><strong>Ensuite, testez avec le portail:</strong></p>
            <ol>
                <li>Cliquez sur "Définir la session" (définit client_logged_in=true et client_user_id=999)</li>
                <li>Allez sur <a href="<?php echo site_url('dietetic/portal'); ?>"><?php echo site_url('dietetic/portal'); ?></a></li>
                <li>Vérifiez si vous êtes "connecté" (même si c'est un faux client)</li>
            </ol>
        </div>

        <hr style="margin: 30px 0;">
        <p style="text-align: center; color: #6c757d;">
            Test effectué à: <?php echo date('d/m/Y H:i:s'); ?>
        </p>
    </div>
</body>
</html>
