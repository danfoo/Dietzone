<?php
defined('BASEPATH') or exit('No direct script access allowed');
init_head();

/**
 * PAGE DE DIAGNOSTIC DU CALLBACK PAYPAL (VERSION ADMIN)
 *
 * URL: /admin/dietetic/test_paypal_callback/{invoice_id}
 */

if (!isset($invoice_id) || !$invoice_id) {
    echo '<div class="alert alert-danger">Aucun invoice_id fourni.</div>';
    init_tail();
    exit;
}
?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div style="max-width: 900px; margin: 0 auto;">
                            <div style="background: linear-gradient(135deg, #01807B, #019B95); color: white; padding: 30px; text-align: center; border-radius: 8px; margin-bottom: 30px;">
                                <h1 style="margin: 0; font-size: 28px;">🔍 Diagnostic PayPal Callback</h1>
                                <p style="margin: 10px 0 0 0;">Test du flux de paiement pour Invoice #<?php echo $invoice_id; ?></p>
                            </div>

                            <?php
                            $all_success = true;
                            $step_num = 1;

                            // STEP 1: Vérifier table payment_tokens existe
                            $table_exists = $this->db->table_exists(db_prefix() . 'dietic_payment_tokens');
                            ?>

                            <div class="alert <?php echo $table_exists ? 'alert-success' : 'alert-danger'; ?>">
                                <h4><?php echo $table_exists ? '✅' : '❌'; ?> Étape <?php echo $step_num++; ?>: Table payment_tokens</h4>
                                <?php if ($table_exists): ?>
                                    <p>La table <code><?php echo db_prefix(); ?>dietic_payment_tokens</code> existe.</p>
                                <?php else: ?>
                                    <p><strong>ERREUR:</strong> La table n'existe pas!</p>
                                    <p>➡️ <a href="<?php echo admin_url('dietetic/apply_paypal_fix'); ?>" class="btn btn-primary">Appliquer la migration</a></p>
                                    <?php $all_success = false; ?>
                                <?php endif; ?>
                            </div>

                            <!-- STEP 2: Helper functions -->
                            <div class="alert <?php echo function_exists('dietetic_get_payment_token') ? 'alert-success' : 'alert-danger'; ?>">
                                <h4><?php echo function_exists('dietetic_get_payment_token') ? '✅' : '❌'; ?> Étape <?php echo $step_num++; ?>: Helper Functions</h4>
                                <?php if (function_exists('dietetic_get_payment_token')): ?>
                                    <p>Les fonctions helper existent.</p>
                                <?php else: ?>
                                    <p><strong>ERREUR:</strong> Les fonctions helper n'existent pas!</p>
                                    <p>Le code n'est pas déployé ou le fichier helper n'est pas chargé.</p>
                                    <?php $all_success = false; ?>
                                <?php endif; ?>
                            </div>

                            <?php if ($table_exists && function_exists('dietetic_get_payment_token')): ?>
                                <!-- STEP 3: Payment token -->
                                <?php $payment_token = dietetic_get_payment_token($invoice_id, 'paypal'); ?>

                                <div class="alert <?php echo $payment_token ? 'alert-success' : 'alert-warning'; ?>">
                                    <h4><?php echo $payment_token ? '✅' : '⚠️'; ?> Étape <?php echo $step_num++; ?>: Payment Token</h4>

                                    <?php if ($payment_token): ?>
                                        <p>Un payment_token actif a été trouvé pour invoice #<?php echo $invoice_id; ?></p>

                                        <table class="table table-bordered">
                                            <tr><th style="width: 200px;">Propriété</th><th>Valeur</th></tr>
                                            <tr><td>ID</td><td><?php echo $payment_token->id; ?></td></tr>
                                            <tr><td>Invoice ID</td><td><?php echo $payment_token->invoice_id; ?></td></tr>
                                            <tr><td>Client ID</td><td><?php echo $payment_token->client_id; ?></td></tr>
                                            <tr><td>Gateway</td><td><?php echo $payment_token->gateway; ?></td></tr>
                                            <tr><td>Order ID</td><td><?php echo $payment_token->order_id ?: '<em>null</em>'; ?></td></tr>
                                            <tr><td>Montant</td><td><?php echo $payment_token->amount . ' ' . $payment_token->currency; ?></td></tr>
                                            <tr><td>Statut</td><td><span class="label label-info"><?php echo $payment_token->status; ?></span></td></tr>
                                            <tr><td>Créé</td><td><?php echo $payment_token->created_at; ?></td></tr>
                                            <tr><td>Expire</td><td><?php echo $payment_token->expires_at; ?></td></tr>
                                        </table>
                                    <?php else: ?>
                                        <p><strong>ATTENTION:</strong> Aucun payment_token actif trouvé pour invoice #<?php echo $invoice_id; ?></p>

                                        <p>Cela signifie:</p>
                                        <ul>
                                            <li>Le paiement n'a pas été initié via le nouveau système</li>
                                            <li>Le token a expiré (> 1 heure)</li>
                                            <li>Le token a déjà été marqué completed/cancelled</li>
                                        </ul>

                                        <?php
                                        // Chercher tous les tokens
                                        $all_tokens = $this->db->select('*')
                                            ->from(db_prefix() . 'dietic_payment_tokens')
                                            ->where('invoice_id', $invoice_id)
                                            ->order_by('created_at', 'DESC')
                                            ->get()
                                            ->result();
                                        ?>

                                        <?php if (!empty($all_tokens)): ?>
                                            <p><strong>Tokens trouvés (tous statuts):</strong></p>
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Status</th>
                                                        <th>Order ID</th>
                                                        <th>Créé</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($all_tokens as $t): ?>
                                                        <tr>
                                                            <td><?php echo $t->id; ?></td>
                                                            <td><span class="label label-warning"><?php echo $t->status; ?></span></td>
                                                            <td><?php echo $t->order_id ?: '<em>null</em>'; ?></td>
                                                            <td><?php echo $t->created_at; ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        <?php endif; ?>

                                        <p style="margin-top: 15px;">
                                            <a href="<?php echo site_url('dietetic/portal/invoice/' . $invoice_id); ?>" class="btn btn-primary" target="_blank">
                                                <i class="fa fa-external-link"></i> Voir facture et initier paiement
                                            </a>
                                        </p>
                                    <?php endif; ?>
                                </div>

                                <!-- STEP 4: Session -->
                                <?php
                                $session_logged_in = $this->session->userdata('client_logged_in');
                                $session_user_id = $this->session->userdata('client_user_id');
                                ?>

                                <div class="alert <?php echo ($session_logged_in && $session_user_id) ? 'alert-success' : 'alert-warning'; ?>">
                                    <h4><?php echo ($session_logged_in && $session_user_id) ? '✅' : '⚠️'; ?> Étape <?php echo $step_num++; ?>: Session</h4>

                                    <?php if ($session_logged_in && $session_user_id): ?>
                                        <p>Client connecté via session: <strong>ID <?php echo $session_user_id; ?></strong></p>
                                    <?php else: ?>
                                        <p>Session non trouvée - Mais c'est OK, le callback va la restaurer depuis payment_token.</p>
                                    <?php endif; ?>
                                </div>

                                <!-- FINAL -->
                                <?php if ($all_success && $payment_token): ?>
                                    <div class="alert alert-success">
                                        <h4>🎉 Diagnostic Réussi!</h4>
                                        <p>Tous les éléments sont en place. Le callback devrait fonctionner.</p>

                                        <h5 style="margin-top: 20px;">🧪 URL du callback :</h5>
                                        <div style="background: #2d2d2d; color: #f8f8f2; padding: 15px; border-radius: 4px; overflow-x: auto; font-family: monospace;">
                                            <?php echo site_url('dietetic/portal/paypal_callback/success/' . $invoice_id . '?token=PAYPAL_TOKEN&PayerID=PAYER_ID'); ?>
                                        </div>

                                        <p style="margin-top: 15px;">
                                            <strong>Note:</strong> Remplacez PAYPAL_TOKEN et PAYER_ID par les vrais paramètres lors du callback.
                                        </p>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-danger">
                                        <h4>❌ Diagnostic Échoué</h4>
                                        <p>Certains éléments sont manquants. Suivez les instructions ci-dessus pour corriger.</p>
                                    </div>
                                <?php endif; ?>

                            <?php endif; ?>

                            <!-- Info système -->
                            <div class="panel panel-default mtop20">
                                <div class="panel-heading">
                                    <h4 class="panel-title">ℹ️ Informations Système</h4>
                                </div>
                                <div class="panel-body">
                                    <table class="table">
                                        <tr><th style="width: 200px;">Information</th><th>Valeur</th></tr>
                                        <tr><td>URL Courante</td><td><code><?php echo current_url(); ?></code></td></tr>
                                        <tr><td>Base URL</td><td><code><?php echo base_url(); ?></code></td></tr>
                                        <tr><td>Site URL</td><td><code><?php echo site_url(); ?></code></td></tr>
                                        <tr><td>DB Prefix</td><td><code><?php echo db_prefix(); ?></code></td></tr>
                                        <tr><td>PHP Version</td><td><?php echo PHP_VERSION; ?></td></tr>
                                    </table>
                                </div>
                            </div>

                            <!-- Ressources -->
                            <div class="panel panel-info mtop20">
                                <div class="panel-heading">
                                    <h4 class="panel-title">📚 Ressources</h4>
                                </div>
                                <div class="panel-body">
                                    <ul>
                                        <li><a href="<?php echo admin_url('dietetic/apply_paypal_fix'); ?>">Page de Migration</a></li>
                                        <li><a href="<?php echo base_url('PAYPAL_SESSION_FIX.md'); ?>" target="_blank">Documentation Complète</a></li>
                                        <li><a href="<?php echo admin_url('utilities/activity_log'); ?>">Activity Logs</a></li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Bouton retour -->
                            <div class="text-right mtop20">
                                <a href="<?php echo admin_url('dietetic/patients'); ?>" class="btn btn-default">
                                    <i class="fa fa-arrow-left"></i> Retour
                                </a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
