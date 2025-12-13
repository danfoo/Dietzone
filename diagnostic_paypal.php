<?php
/**
 * DIAGNOSTIC PAYPAL - Standalone
 *
 * Placez ce fichier à la racine de Perfex CRM
 * Accès: https://app.dietsenegal.net/diagnostic_paypal.php?invoice_id=7
 */

// Configuration
define('INVOICE_ID', isset($_GET['invoice_id']) ? (int)$_GET['invoice_id'] : null);

if (!INVOICE_ID) {
    die('<h1>❌ Erreur</h1><p>Ajoutez ?invoice_id=7 à l\'URL</p>');
}

// Charger l'environnement Perfex CRM
if (file_exists('application/config/app-config.php')) {
    require_once('application/config/app-config.php');
}

// Connexion base de données
$db_host = defined('APP_DB_HOSTNAME') ? APP_DB_HOSTNAME : 'localhost';
$db_name = defined('APP_DB_NAME') ? APP_DB_NAME : '';
$db_user = defined('APP_DB_USERNAME') ? APP_DB_USERNAME : '';
$db_pass = defined('APP_DB_PASSWORD') ? APP_DB_PASSWORD : '';

if (empty($db_name)) {
    // Essayer avec les anciennes constantes
    if (file_exists('application/config/database.php')) {
        include('application/config/database.php');
        if (isset($db['default'])) {
            $db_host = $db['default']['hostname'];
            $db_name = $db['default']['database'];
            $db_user = $db['default']['username'];
            $db_pass = $db['default']['password'];
        }
    }
}

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('<h1>❌ Erreur BD</h1><p>' . $e->getMessage() . '</p>');
}

// Obtenir le préfixe
$prefix = defined('APP_DB_PREFIX') ? APP_DB_PREFIX : 'tbl';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnostic PayPal - Invoice #<?php echo INVOICE_ID; ?></title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; background: #f5f5f5; }
        .container { max-width: 900px; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #01807B, #019B95); color: white; padding: 20px; text-align: center; border-radius: 8px; margin-bottom: 30px; }
        .step { margin-bottom: 20px; }
        pre { background: #2d2d2d; color: #f8f8f2; padding: 15px; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔍 Diagnostic PayPal Callback</h1>
            <p>Invoice #<?php echo INVOICE_ID; ?></p>
        </div>

        <?php
        $step = 1;

        // STEP 1: Table existe ?
        try {
            $stmt = $pdo->query("SHOW TABLES LIKE '{$prefix}dietic_payment_tokens'");
            $table_exists = $stmt->rowCount() > 0;
        } catch (Exception $e) {
            $table_exists = false;
        }
        ?>

        <div class="step">
            <div class="alert alert-<?php echo $table_exists ? 'success' : 'danger'; ?>">
                <h4><?php echo $table_exists ? '✅' : '❌'; ?> Étape <?php echo $step++; ?>: Table payment_tokens</h4>
                <?php if ($table_exists): ?>
                    <p>La table <code><?php echo $prefix; ?>dietic_payment_tokens</code> <strong>existe</strong>.</p>
                <?php else: ?>
                    <p><strong>ERREUR:</strong> La table <code><?php echo $prefix; ?>dietic_payment_tokens</code> n'existe pas!</p>
                    <p><strong>Action requise:</strong></p>
                    <ol>
                        <li>Aller sur: <a href="https://app.dietsenegal.net/admin/dietetic/apply_paypal_fix" target="_blank">Page de Migration</a></li>
                        <li>Cliquer "Appliquer la Migration"</li>
                        <li>Rafraîchir cette page</li>
                    </ol>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($table_exists): ?>
            <!-- STEP 2: Chercher payment_token -->
            <?php
            try {
                $stmt = $pdo->prepare("
                    SELECT * FROM {$prefix}dietic_payment_tokens
                    WHERE invoice_id = :invoice_id
                    AND gateway = 'paypal'
                    AND status = 'pending'
                    AND expires_at > NOW()
                    ORDER BY created_at DESC
                    LIMIT 1
                ");
                $stmt->execute(['invoice_id' => INVOICE_ID]);
                $token = $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                $token = null;
                $error = $e->getMessage();
            }
            ?>

            <div class="step">
                <div class="alert alert-<?php echo $token ? 'success' : 'warning'; ?>">
                    <h4><?php echo $token ? '✅' : '⚠️'; ?> Étape <?php echo $step++; ?>: Payment Token</h4>

                    <?php if ($token): ?>
                        <p><strong>Token trouvé!</strong> Voici les détails :</p>
                        <table class="table table-bordered">
                            <tr><th style="width: 200px;">Propriété</th><th>Valeur</th></tr>
                            <tr><td>ID</td><td><?php echo $token['id']; ?></td></tr>
                            <tr><td>Invoice ID</td><td><?php echo $token['invoice_id']; ?></td></tr>
                            <tr><td>Client ID</td><td><?php echo $token['client_id']; ?></td></tr>
                            <tr><td>Gateway</td><td><?php echo $token['gateway']; ?></td></tr>
                            <tr><td>Order ID PayPal</td><td><?php echo $token['order_id'] ?: '<em>null</em>'; ?></td></tr>
                            <tr><td>Montant</td><td><?php echo $token['amount'] . ' ' . $token['currency']; ?></td></tr>
                            <tr><td>Statut</td><td><span class="label label-info"><?php echo $token['status']; ?></span></td></tr>
                            <tr><td>Créé le</td><td><?php echo $token['created_at']; ?></td></tr>
                            <tr><td>Expire le</td><td><?php echo $token['expires_at']; ?></td></tr>
                        </table>

                        <?php if (empty($token['order_id'])): ?>
                            <div class="alert alert-danger">
                                <strong>⚠️ Problème:</strong> Le Order ID PayPal est vide!
                                <p>Le paiement n'a probablement pas été initié correctement.</p>
                            </div>
                        <?php endif; ?>

                    <?php else: ?>
                        <p><strong>Aucun payment_token actif trouvé</strong> pour invoice #<?php echo INVOICE_ID; ?></p>

                        <?php
                        // Chercher tous les tokens pour cette invoice
                        $stmt = $pdo->prepare("
                            SELECT * FROM {$prefix}dietic_payment_tokens
                            WHERE invoice_id = :invoice_id
                            ORDER BY created_at DESC
                        ");
                        $stmt->execute(['invoice_id' => INVOICE_ID]);
                        $all_tokens = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        ?>

                        <?php if ($all_tokens): ?>
                            <p><strong>Tokens trouvés (tous statuts):</strong></p>
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Statut</th>
                                        <th>Order ID</th>
                                        <th>Créé le</th>
                                        <th>Expire le</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($all_tokens as $t): ?>
                                        <tr>
                                            <td><?php echo $t['id']; ?></td>
                                            <td><span class="label label-warning"><?php echo $t['status']; ?></span></td>
                                            <td><?php echo $t['order_id'] ?: '<em>null</em>'; ?></td>
                                            <td><?php echo $t['created_at']; ?></td>
                                            <td><?php echo $t['expires_at']; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <strong>Aucun token trouvé</strong> - Vous devez initier un nouveau paiement PayPal.
                            </div>
                        <?php endif; ?>

                        <hr>
                        <p><strong>Action requise:</strong></p>
                        <ol>
                            <li>Aller sur la facture: <a href="https://app.dietsenegal.net/dietetic/portal/invoice/<?php echo INVOICE_ID; ?>" target="_blank">Invoice #<?php echo INVOICE_ID; ?></a></li>
                            <li>Cliquer "Payer avec PayPal"</li>
                            <li>NE PAS compléter le paiement encore</li>
                            <li>Rafraîchir cette page</li>
                            <li>Vous devriez voir le token créé</li>
                        </ol>
                    <?php endif; ?>
                </div>
            </div>

            <!-- STEP 3: Résumé -->
            <?php if ($token && !empty($token['order_id'])): ?>
                <div class="step">
                    <div class="alert alert-success">
                        <h4>🎉 Diagnostic Réussi!</h4>
                        <p>Tous les éléments sont en place. Le callback PayPal <strong>devrait fonctionner</strong>.</p>

                        <h5 style="margin-top: 20px;">🧪 URL du callback (pour référence):</h5>
                        <pre>https://app.dietsenegal.net/dietetic/portal/paypal_callback/success/<?php echo INVOICE_ID; ?>?token=PAYPAL_TOKEN&PayerID=PAYER_ID</pre>

                        <h5 style="margin-top: 20px;">📝 Prochaines étapes:</h5>
                        <ol>
                            <li>Complétez le paiement sur PayPal</li>
                            <li>Vérifiez que vous êtes redirigé correctement</li>
                            <li>Vérifiez que la facture est marquée "Payée"</li>
                        </ol>
                    </div>
                </div>
            <?php endif; ?>

        <?php endif; ?>

        <!-- Infos système -->
        <div class="step">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">ℹ️ Informations Système</h4>
                </div>
                <div class="panel-body">
                    <table class="table table-sm">
                        <tr><th style="width: 200px;">Information</th><th>Valeur</th></tr>
                        <tr><td>URL de cette page</td><td><code><?php echo 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?></code></td></tr>
                        <tr><td>DB Prefix</td><td><code><?php echo $prefix; ?></code></td></tr>
                        <tr><td>PHP Version</td><td><?php echo PHP_VERSION; ?></td></tr>
                        <tr><td>Date/Heure serveur</td><td><?php echo date('Y-m-d H:i:s'); ?></td></tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Ressources -->
        <div class="alert alert-info">
            <h4>📚 Ressources Utiles</h4>
            <ul>
                <li><a href="https://app.dietsenegal.net/admin/dietetic/apply_paypal_fix" target="_blank">Page de Migration</a></li>
                <li><a href="https://app.dietsenegal.net/admin/utilities/activity_log" target="_blank">Activity Logs</a></li>
                <li><a href="https://app.dietsenegal.net/dietetic/portal/invoice/<?php echo INVOICE_ID; ?>" target="_blank">Voir la Facture #<?php echo INVOICE_ID; ?></a></li>
            </ul>
        </div>

        <div class="text-center" style="margin-top: 30px;">
            <a href="<?php echo $_SERVER['PHP_SELF']; ?>?invoice_id=<?php echo INVOICE_ID; ?>" class="btn btn-primary">
                <i class="glyphicon glyphicon-refresh"></i> Rafraîchir le Diagnostic
            </a>
        </div>
    </div>
</body>
</html>
