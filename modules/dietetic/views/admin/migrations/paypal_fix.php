<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <!-- Header -->
                        <div class="clearfix">
                            <div class="pull-left">
                                <h4 class="tw-mt-0 tw-font-bold tw-text-neutral-700">
                                    <i class="fa fa-database tw-mr-1"></i>
                                    Migration: Fix PayPal Session Loss
                                </h4>
                                <p class="text-muted">
                                    Correction du problème de session perdue lors du callback PayPal
                                </p>
                            </div>
                        </div>

                        <hr class="hr-panel-heading" />

                        <!-- État de la Migration -->
                        <div class="row">
                            <div class="col-md-12">
                                <?php if ($table_exists): ?>
                                    <!-- Table existe déjà -->
                                    <div class="alert alert-success">
                                        <i class="fa fa-check-circle"></i>
                                        <strong>Migration déjà appliquée</strong>
                                        <p class="tw-mb-0">La table <code>tbldietic_payment_tokens</code> existe déjà dans la base de données.</p>
                                    </div>

                                    <!-- Statistiques -->
                                    <div class="row mtop20">
                                        <div class="col-md-3 col-sm-6">
                                            <div class="tw-mb-3 md:tw-mb-0">
                                                <div class="tw-flex tw-items-center tw-justify-between tw-p-4 tw-bg-white tw-border tw-border-solid tw-border-neutral-300 tw-rounded-lg">
                                                    <div>
                                                        <p class="tw-mb-0 tw-text-neutral-600 tw-font-medium">Total Tokens</p>
                                                        <h3 class="tw-mb-0 tw-mt-1 tw-font-semibold tw-text-neutral-800">
                                                            <?php echo $total_tokens; ?>
                                                        </h3>
                                                    </div>
                                                    <div class="tw-ml-3">
                                                        <i class="fa fa-ticket fa-2x text-info"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <?php if (isset($tokens_by_status)): ?>
                                            <?php foreach ($tokens_by_status as $stat): ?>
                                                <div class="col-md-3 col-sm-6">
                                                    <div class="tw-mb-3 md:tw-mb-0">
                                                        <div class="tw-flex tw-items-center tw-justify-between tw-p-4 tw-bg-white tw-border tw-border-solid tw-border-neutral-300 tw-rounded-lg">
                                                            <div>
                                                                <p class="tw-mb-0 tw-text-neutral-600 tw-font-medium">
                                                                    <?php echo ucfirst($stat->status); ?>
                                                                </p>
                                                                <h3 class="tw-mb-0 tw-mt-1 tw-font-semibold tw-text-neutral-800">
                                                                    <?php echo $stat->count; ?>
                                                                </h3>
                                                            </div>
                                                            <div class="tw-ml-3">
                                                                <?php
                                                                $icon = '';
                                                                $color = '';
                                                                switch ($stat->status) {
                                                                    case 'pending':
                                                                        $icon = 'fa-clock-o';
                                                                        $color = 'text-warning';
                                                                        break;
                                                                    case 'processing':
                                                                        $icon = 'fa-spinner';
                                                                        $color = 'text-info';
                                                                        break;
                                                                    case 'completed':
                                                                        $icon = 'fa-check-circle';
                                                                        $color = 'text-success';
                                                                        break;
                                                                    case 'cancelled':
                                                                        $icon = 'fa-times-circle';
                                                                        $color = 'text-danger';
                                                                        break;
                                                                    default:
                                                                        $icon = 'fa-question-circle';
                                                                        $color = 'text-muted';
                                                                }
                                                                ?>
                                                                <i class="fa <?php echo $icon; ?> fa-2x <?php echo $color; ?>"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Derniers Tokens -->
                                    <?php if (isset($recent_tokens) && !empty($recent_tokens)): ?>
                                        <div class="mtop30">
                                            <h4 class="tw-font-semibold">Derniers Tokens de Paiement</h4>
                                            <div class="table-responsive">
                                                <table class="table table-striped dt-table">
                                                    <thead>
                                                        <tr>
                                                            <th>ID</th>
                                                            <th>Invoice ID</th>
                                                            <th>Client ID</th>
                                                            <th>Gateway</th>
                                                            <th>Montant</th>
                                                            <th>Statut</th>
                                                            <th>Créé le</th>
                                                            <th>Expire le</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($recent_tokens as $token): ?>
                                                            <tr>
                                                                <td><?php echo $token->id; ?></td>
                                                                <td>
                                                                    <a href="<?php echo admin_url('invoices/list_invoices/' . $token->invoice_id); ?>">
                                                                        #<?php echo $token->invoice_id; ?>
                                                                    </a>
                                                                </td>
                                                                <td><?php echo $token->client_id; ?></td>
                                                                <td>
                                                                    <span class="label label-default">
                                                                        <?php echo strtoupper($token->gateway); ?>
                                                                    </span>
                                                                </td>
                                                                <td><?php echo number_format($token->amount, 2); ?> <?php echo $token->currency; ?></td>
                                                                <td>
                                                                    <?php
                                                                    $status_colors = [
                                                                        'pending' => 'warning',
                                                                        'processing' => 'info',
                                                                        'completed' => 'success',
                                                                        'cancelled' => 'danger',
                                                                        'expired' => 'default'
                                                                    ];
                                                                    $color = isset($status_colors[$token->status]) ? $status_colors[$token->status] : 'default';
                                                                    ?>
                                                                    <span class="label label-<?php echo $color; ?>">
                                                                        <?php echo ucfirst($token->status); ?>
                                                                    </span>
                                                                </td>
                                                                <td><?php echo date('d/m/Y H:i', strtotime($token->created_at)); ?></td>
                                                                <td>
                                                                    <?php
                                                                    $expires = strtotime($token->expires_at);
                                                                    $is_expired = $expires < time();
                                                                    ?>
                                                                    <span class="<?php echo $is_expired ? 'text-danger' : ''; ?>">
                                                                        <?php echo date('d/m/Y H:i', $expires); ?>
                                                                        <?php if ($is_expired): ?>
                                                                            <i class="fa fa-exclamation-triangle"></i>
                                                                        <?php endif; ?>
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Informations -->
                                    <div class="mtop30">
                                        <div class="alert alert-info">
                                            <h4><i class="fa fa-info-circle"></i> Informations sur la Migration</h4>
                                            <ul class="tw-mb-0">
                                                <li>Cette migration a créé la table <code>tbldietic_payment_tokens</code></li>
                                                <li>Cette table stocke les informations de paiement en base de données au lieu de la session</li>
                                                <li>Cela résout le problème de session perdue lors des redirections PayPal</li>
                                                <li>Les tokens expirent automatiquement après 1 heure</li>
                                            </ul>
                                        </div>

                                        <div class="alert alert-warning">
                                            <h4><i class="fa fa-wrench"></i> Nettoyage Recommandé</h4>
                                            <p>Pour nettoyer les tokens expirés, exécutez cette requête SQL :</p>
                                            <pre style="background: #f5f5f5; padding: 10px; border-radius: 4px;">DELETE FROM <?php echo db_prefix(); ?>dietic_payment_tokens
WHERE expires_at < NOW()
OR (status IN ('completed', 'cancelled') AND created_at < DATE_SUB(NOW(), INTERVAL 7 DAY));</pre>
                                        </div>
                                    </div>

                                <?php else: ?>
                                    <!-- Table n'existe pas encore -->
                                    <div class="alert alert-warning">
                                        <i class="fa fa-exclamation-triangle"></i>
                                        <strong>Migration non appliquée</strong>
                                        <p class="tw-mb-0">La table <code>tbldietic_payment_tokens</code> n'existe pas encore. Cliquez sur le bouton ci-dessous pour appliquer la migration.</p>
                                    </div>

                                    <!-- Description de la Migration -->
                                    <div class="panel panel-info mtop20">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <i class="fa fa-question-circle"></i> Qu'est-ce que cette migration va faire ?
                                            </h4>
                                        </div>
                                        <div class="panel-body">
                                            <h5><strong>Problème Résolu :</strong></h5>
                                            <p>
                                                Après avoir effectué un paiement PayPal, les utilisateurs rencontrent une erreur
                                                <code>NS_ERROR_NET_ERROR_RESPONSE</code> lors de la redirection de callback.
                                                Cela est dû à la perte de session lors de la redirection depuis PayPal.com.
                                            </p>

                                            <h5 class="mtop20"><strong>Solution :</strong></h5>
                                            <ul>
                                                <li>Créer une table <code>tbldietic_payment_tokens</code> pour stocker les informations de paiement</li>
                                                <li>Stocker l'order_id PayPal en base de données au lieu de la session</li>
                                                <li>Récupérer les informations depuis la BD lors du callback</li>
                                                <li>Validation sécurisée avec client_id</li>
                                                <li>Expiration automatique des tokens après 1 heure</li>
                                            </ul>

                                            <h5 class="mtop20"><strong>Fichiers SQL à exécuter :</strong></h5>
                                            <code>modules/dietetic/migrations/fix_paypal_session_issue.sql</code>

                                            <h5 class="mtop20"><strong>Sécurité :</strong></h5>
                                            <ul class="tw-mb-0">
                                                <li><i class="fa fa-check text-success"></i> Validation du client_id dans le callback</li>
                                                <li><i class="fa fa-check text-success"></i> Tokens avec expiration (1 heure)</li>
                                                <li><i class="fa fa-check text-success"></i> Index unique pour éviter les duplicatas</li>
                                                <li><i class="fa fa-check text-success"></i> Statuts de cycle de vie complets</li>
                                            </ul>
                                        </div>
                                    </div>

                                    <!-- Formulaire d'Application -->
                                    <div class="mtop30">
                                        <?php echo form_open(admin_url('dietetic/apply_paypal_fix')); ?>
                                            <input type="hidden" name="apply_migration" value="1">

                                            <div class="text-center">
                                                <button type="submit" class="btn btn-primary btn-lg"
                                                        onclick="return confirm('Êtes-vous sûr de vouloir appliquer cette migration ?');">
                                                    <i class="fa fa-database"></i>
                                                    Appliquer la Migration Maintenant
                                                </button>
                                            </div>

                                            <p class="text-center text-muted mtop15">
                                                <small>
                                                    <i class="fa fa-info-circle"></i>
                                                    Cette opération est sûre. Si la table existe déjà, elle ne sera pas modifiée.
                                                </small>
                                            </p>
                                        <?php echo form_close(); ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Liens Utiles -->
                                <div class="mtop30">
                                    <h4>Documentation et Ressources</h4>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="panel panel-default">
                                                <div class="panel-body text-center">
                                                    <i class="fa fa-book fa-3x text-primary"></i>
                                                    <h5 class="mtop15">Documentation Complète</h5>
                                                    <p class="text-muted">Guide détaillé du fix</p>
                                                    <a href="<?php echo base_url('PAYPAL_SESSION_FIX.md'); ?>"
                                                       class="btn btn-default btn-sm" target="_blank">
                                                        <i class="fa fa-external-link"></i> Voir Documentation
                                                    </a>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="panel panel-default">
                                                <div class="panel-body text-center">
                                                    <i class="fa fa-code fa-3x text-success"></i>
                                                    <h5 class="mtop15">Fichier SQL</h5>
                                                    <p class="text-muted">Migration SQL source</p>
                                                    <code class="small">migrations/fix_paypal_session_issue.sql</code>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="panel panel-default">
                                                <div class="panel-body text-center">
                                                    <i class="fa fa-wrench fa-3x text-warning"></i>
                                                    <h5 class="mtop15">Paramètres PayPal</h5>
                                                    <p class="text-muted">Configuration passerelle</p>
                                                    <a href="<?php echo admin_url('dietetic/payment_settings'); ?>"
                                                       class="btn btn-default btn-sm">
                                                        <i class="fa fa-cog"></i> Configurer
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Boutons Retour -->
                                <div class="btn-bottom-toolbar text-right mtop30">
                                    <a href="<?php echo admin_url('dietetic/patients'); ?>" class="btn btn-default">
                                        <i class="fa fa-arrow-left"></i> Retour au Module
                                    </a>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
$(function() {
    // Initialize DataTables if table exists
    if ($('.dt-table').length > 0) {
        $('.dt-table').DataTable({
            "order": [[0, "desc"]],
            "pageLength": 10,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json"
            }
        });
    }
});
</script>
