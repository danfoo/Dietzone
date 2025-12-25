<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * PAGE DE DIAGNOSTIC DU CALLBACK PAYPAL
 *
 * Cette page teste le flux complet du callback PayPal sans faire de redirections
 * pour identifier exactement où le problème se situe.
 *
 * URL: /dietetic/portal/test_paypal_callback/{invoice_id}
 * Exemple: https://app.dietsenegal.net/dietetic/portal/test_paypal_callback/7
 */

// Get parameters
$invoice_id = isset($_GET['invoice_id']) ? (int)$_GET['invoice_id'] : null;
$status = isset($_GET['status']) ? $_GET['status'] : 'success';

if (!$invoice_id) {
    echo "<h1>❌ Erreur</h1>";
    echo "<p>Aucun invoice_id fourni. Utilisez: <code>?invoice_id=7</code></p>";
    exit;
}

// Load CodeIgniter
$CI = &get_instance();

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnostic PayPal Callback - Invoice #<?php echo $invoice_id; ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #01807B, #019B95);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        .content {
            padding: 30px;
        }
        .step {
            background: #f8f9fa;
            border-left: 4px solid #ccc;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .step.success {
            border-left-color: #28a745;
            background: #d4edda;
        }
        .step.error {
            border-left-color: #dc3545;
            background: #f8d7da;
        }
        .step.warning {
            border-left-color: #ffc107;
            background: #fff3cd;
        }
        .step h3 {
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .icon {
            font-size: 24px;
        }
        .code {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
            margin: 10px 0;
            font-family: 'Courier New', monospace;
            font-size: 13px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        .data-table th,
        .data-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .data-table th {
            background: #f1f1f1;
            font-weight: 600;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-success { background: #28a745; color: white; }
        .badge-danger { background: #dc3545; color: white; }
        .badge-warning { background: #ffc107; color: #000; }
        .badge-info { background: #17a2b8; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔍 Diagnostic PayPal Callback</h1>
            <p>Test du flux de paiement pour Invoice #<?php echo $invoice_id; ?></p>
        </div>

        <div class="content">
            <?php
            $all_success = true;
            $step_num = 1;

            // STEP 1: Vérifier table payment_tokens existe
            echo '<div class="step ';
            $table_exists = $CI->db->table_exists(db_prefix() . 'dietic_payment_tokens');
            if ($table_exists) {
                echo 'success">';
                echo '<h3><span class="icon">✅</span> Étape ' . $step_num++ . ': Table payment_tokens</h3>';
                echo '<p>La table <code>' . db_prefix() . 'dietic_payment_tokens</code> existe.</p>';
            } else {
                echo 'error">';
                echo '<h3><span class="icon">❌</span> Étape ' . $step_num++ . ': Table payment_tokens</h3>';
                echo '<p><strong>ERREUR:</strong> La table <code>' . db_prefix() . 'dietic_payment_tokens</code> n\'existe pas!</p>';
                echo '<p>➡️ Appliquer la migration: <a href="' . admin_url('dietetic/apply_paypal_fix') . '" target="_blank">' . admin_url('dietetic/apply_paypal_fix') . '</a></p>';
                $all_success = false;
            }
            echo '</div>';

            // STEP 2: Vérifier helper functions existent
            echo '<div class="step ';
            if (function_exists('dietetic_get_payment_token')) {
                echo 'success">';
                echo '<h3><span class="icon">✅</span> Étape ' . $step_num++ . ': Helper Functions</h3>';
                echo '<p>Les fonctions helper <code>dietetic_get_payment_token()</code> existent.</p>';
            } else {
                echo 'error">';
                echo '<h3><span class="icon">❌</span> Étape ' . $step_num++ . ': Helper Functions</h3>';
                echo '<p><strong>ERREUR:</strong> Les fonctions helper n\'existent pas!</p>';
                echo '<p>➡️ Vérifier que <code>modules/dietetic/helpers/dietetic_helper.php</code> contient les nouvelles fonctions.</p>';
                $all_success = false;
            }
            echo '</div>';

            if ($table_exists) {
                // STEP 3: Chercher payment_token pour cet invoice
                echo '<div class="step ';
                $payment_token = dietetic_get_payment_token($invoice_id, 'paypal');

                if ($payment_token) {
                    echo 'success">';
                    echo '<h3><span class="icon">✅</span> Étape ' . $step_num++ . ': Payment Token Trouvé</h3>';
                    echo '<p>Un payment_token actif a été trouvé pour l\'invoice #' . $invoice_id . '</p>';

                    echo '<table class="data-table">';
                    echo '<tr><th>Propriété</th><th>Valeur</th></tr>';
                    echo '<tr><td>ID</td><td>' . $payment_token->id . '</td></tr>';
                    echo '<tr><td>Invoice ID</td><td>' . $payment_token->invoice_id . '</td></tr>';
                    echo '<tr><td>Client ID</td><td>' . $payment_token->client_id . '</td></tr>';
                    echo '<tr><td>Gateway</td><td>' . $payment_token->gateway . '</td></tr>';
                    echo '<tr><td>Order ID</td><td>' . ($payment_token->order_id ?: '<em>null</em>') . '</td></tr>';
                    echo '<tr><td>Montant</td><td>' . $payment_token->amount . ' ' . $payment_token->currency . '</td></tr>';
                    echo '<tr><td>Statut</td><td><span class="badge badge-info">' . $payment_token->status . '</span></td></tr>';
                    echo '<tr><td>Créé</td><td>' . $payment_token->created_at . '</td></tr>';
                    echo '<tr><td>Expire</td><td>' . $payment_token->expires_at . '</td></tr>';
                    echo '</table>';
                } else {
                    echo 'warning">';
                    echo '<h3><span class="icon">⚠️</span> Étape ' . $step_num++ . ': Payment Token</h3>';
                    echo '<p><strong>ATTENTION:</strong> Aucun payment_token actif trouvé pour invoice #' . $invoice_id . '</p>';
                    echo '<p>Cela signifie:</p>';
                    echo '<ul style="margin-left: 20px;">';
                    echo '<li>Soit le paiement n\'a pas été initié via le nouveau système</li>';
                    echo '<li>Soit le token a expiré (> 1 heure)</li>';
                    echo '<li>Soit le token a déjà été marqué completed/cancelled</li>';
                    echo '</ul>';

                    // Chercher tous les tokens pour cet invoice
                    $CI->db->select('*');
                    $CI->db->from(db_prefix() . 'dietic_payment_tokens');
                    $CI->db->where('invoice_id', $invoice_id);
                    $CI->db->order_by('created_at', 'DESC');
                    $all_tokens = $CI->db->get()->result();

                    if (!empty($all_tokens)) {
                        echo '<p><strong>Tokens trouvés (tous statuts):</strong></p>';
                        echo '<table class="data-table">';
                        echo '<tr><th>ID</th><th>Status</th><th>Order ID</th><th>Créé</th></tr>';
                        foreach ($all_tokens as $t) {
                            echo '<tr>';
                            echo '<td>' . $t->id . '</td>';
                            echo '<td><span class="badge badge-warning">' . $t->status . '</span></td>';
                            echo '<td>' . ($t->order_id ?: '<em>null</em>') . '</td>';
                            echo '<td>' . $t->created_at . '</td>';
                            echo '</tr>';
                        }
                        echo '</table>';
                    }
                }
                echo '</div>';

                // STEP 4: Vérifier session
                echo '<div class="step ';
                $session_logged_in = $CI->session->userdata('client_logged_in');
                $session_user_id = $CI->session->userdata('client_user_id');

                if ($session_logged_in && $session_user_id) {
                    echo 'success">';
                    echo '<h3><span class="icon">✅</span> Étape ' . $step_num++ . ': Session Active</h3>';
                    echo '<p>Client connecté via session: <strong>ID ' . $session_user_id . '</strong></p>';
                } else {
                    echo 'warning">';
                    echo '<h3><span class="icon">⚠️</span> Étape ' . $step_num++ . ': Session</h3>';
                    echo '<p><strong>Session non trouvée</strong> - Mais c\'est OK, le callback va la restaurer depuis payment_token.</p>';
                }
                echo '</div>';

                // STEP 5: Test is_client_logged_in()
                echo '<div class="step ';
                if (function_exists('is_client_logged_in')) {
                    $logged_in = is_client_logged_in();
                    if ($logged_in) {
                        echo 'success">';
                        echo '<h3><span class="icon">✅</span> Étape ' . $step_num++ . ': is_client_logged_in()</h3>';
                        echo '<p>La fonction détecte un client connecté (session ou cookies).</p>';
                    } else {
                        echo 'warning">';
                        echo '<h3><span class="icon">⚠️</span> Étape ' . $step_num++ . ': is_client_logged_in()</h3>';
                        echo '<p>La fonction ne détecte pas de client connecté.</p>';
                        echo '<p>Le callback va restaurer la session depuis le payment_token.</p>';
                    }
                } else {
                    echo 'error">';
                    echo '<h3><span class="icon">❌</span> Étape ' . $step_num++ . ': is_client_logged_in()</h3>';
                    echo '<p><strong>ERREUR:</strong> Fonction is_client_logged_in() n\'existe pas!</p>';
                }
                echo '</div>';
            }

            // STEP FINAL: Instructions
            if ($all_success && $payment_token) {
                echo '<div class="step success">';
                echo '<h3><span class="icon">🎉</span> Diagnostic Réussi!</h3>';
                echo '<p>Tous les éléments sont en place. Le callback devrait fonctionner.</p>';
                echo '<h4 style="margin-top: 20px;">🧪 Tester le vrai callback:</h4>';
                echo '<p>Utilisez cette URL (remplacez le token par le vrai token PayPal):</p>';
                echo '<div class="code">';
                echo site_url('dietetic/portal/paypal_callback/success/' . $invoice_id . '?token=PAYPAL_TOKEN_HERE&PayerID=PAYER_ID_HERE');
                echo '</div>';
                echo '</div>';
            } else {
                echo '<div class="step error">';
                echo '<h3><span class="icon">❌</span> Diagnostic Échoué</h3>';
                echo '<p>Certains éléments sont manquants. Suivez les instructions ci-dessus pour corriger.</p>';
                echo '</div>';
            }

            // Informations supplémentaires
            echo '<div class="step">';
            echo '<h3><span class="icon">ℹ️</span> Informations Système</h3>';
            echo '<table class="data-table">';
            echo '<tr><th>Information</th><th>Valeur</th></tr>';
            echo '<tr><td>URL Courante</td><td><code>' . current_url() . '</code></td></tr>';
            echo '<tr><td>Base URL</td><td><code>' . base_url() . '</code></td></tr>';
            echo '<tr><td>Site URL</td><td><code>' . site_url() . '</code></td></tr>';
            echo '<tr><td>DB Prefix</td><td><code>' . db_prefix() . '</code></td></tr>';
            echo '<tr><td>PHP Version</td><td>' . PHP_VERSION . '</td></tr>';
            echo '<tr><td>CodeIgniter Version</td><td>' . CI_VERSION . '</td></tr>';
            echo '</table>';
            echo '</div>';
            ?>

            <div style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 4px;">
                <h3>📚 Ressources</h3>
                <ul style="margin-left: 20px; margin-top: 10px;">
                    <li><a href="<?php echo admin_url('dietetic/apply_paypal_fix'); ?>" target="_blank">Page de Migration</a></li>
                    <li><a href="<?php echo base_url('PAYPAL_SESSION_FIX.md'); ?>" target="_blank">Documentation Complète</a></li>
                    <li><a href="<?php echo admin_url('utilities/activity_log'); ?>" target="_blank">Activity Logs</a></li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>
