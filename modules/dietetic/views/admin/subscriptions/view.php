<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h3>Abonnement #<?php echo $subscription->id; ?></h3>
                                <p class="text-muted">
                                    <?php echo htmlspecialchars($subscription->plan_name_fr ?: $subscription->plan_name); ?>
                                </p>
                            </div>
                            <div class="col-md-4 text-right">
                                <span class="label label-<?php echo $subscription->status == 'active' ? 'success' : ($subscription->status == 'trial' ? 'warning' : 'danger'); ?>" style="font-size: 14px; padding: 8px 15px;">
                                    <?php
                                    $status_labels = [
                                        'trial' => 'Essai',
                                        'active' => 'Actif',
                                        'expired' => 'Expiré',
                                        'cancelled' => 'Annulé',
                                        'pending' => 'En attente'
                                    ];
                                    echo $status_labels[$subscription->status] ?? $subscription->status;
                                    ?>
                                </span>
                            </div>
                        </div>

                        <hr />

                        <!-- Patient & Dietitian Info -->
                        <div class="row">
                            <div class="col-md-6">
                                <h4>Patient</h4>
                                <p><strong><?php echo htmlspecialchars($subscription->patient_name); ?></strong></p>
                                <p class="text-muted"><?php echo htmlspecialchars($subscription->patient_email); ?></p>
                            </div>
                            <div class="col-md-6">
                                <h4>Diététicien</h4>
                                <p><strong><?php echo htmlspecialchars($subscription->dietitian_name); ?></strong></p>
                            </div>
                        </div>

                        <hr />

                        <!-- Subscription Details -->
                        <h4>Détails de l'Abonnement</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Montant:</strong> <?php echo number_format($subscription->amount, 0, ',', ' '); ?> FCFA</p>
                                <p><strong>Cycle de facturation:</strong> <?php echo $subscription->billing_cycle == 'monthly' ? 'Mensuel' : 'Annuel'; ?></p>
                                <p><strong>Date de début:</strong> <?php echo _d($subscription->start_date); ?></p>
                                <p><strong>Date de fin:</strong> <?php echo _d($subscription->end_date); ?></p>
                            </div>
                            <div class="col-md-6">
                                <?php if ($subscription->trial_end_date): ?>
                                    <p><strong>Fin de l'essai:</strong> <?php echo _d($subscription->trial_end_date); ?></p>
                                <?php endif; ?>
                                <?php if ($subscription->next_billing_date): ?>
                                    <p><strong>Prochaine facturation:</strong> <?php echo _d($subscription->next_billing_date); ?></p>
                                <?php endif; ?>
                                <p>
                                    <strong>Source de référence:</strong>
                                    <span class="label label-<?php echo $subscription->referral_source == 'platform' ? 'warning' : 'success'; ?>">
                                        <i class="fa fa-<?php echo $subscription->referral_source == 'platform' ? 'building' : 'user-md'; ?>"></i>
                                        <?php echo ucfirst($subscription->referral_source); ?>
                                    </span>
                                </p>
                                <p><strong>Taux de TVA:</strong> <?php echo number_format($subscription->tax_rate, 2); ?>%</p>
                            </div>
                        </div>

                        <?php if ($subscription->notes): ?>
                            <div class="alert alert-info">
                                <strong>Notes:</strong><br>
                                <?php echo nl2br(htmlspecialchars($subscription->notes)); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($subscription->status == 'cancelled'): ?>
                            <div class="alert alert-danger">
                                <strong>Abonnement annulé</strong><br>
                                Date: <?php echo _dt($subscription->cancelled_at); ?><br>
                                <?php if ($subscription->cancellation_reason): ?>
                                    Raison: <?php echo htmlspecialchars($subscription->cancellation_reason); ?>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <hr />

                        <!-- Actions -->
                        <div class="text-right">
                            <a href="<?php echo admin_url('dietetic/subscriptions'); ?>" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Retour
                            </a>
                            <?php if (dietetic_has_permission('edit') && in_array($subscription->status, ['trial', 'active', 'pending'])): ?>
                                <a href="<?php echo admin_url('dietetic/subscriptions/edit/' . $subscription->id); ?>" class="btn btn-info">
                                    <i class="fa fa-edit"></i> Modifier
                                </a>
                            <?php endif; ?>
                            <?php if (dietetic_has_permission('delete') && $subscription->status != 'cancelled'): ?>
                                <button type="button" class="btn btn-danger" id="btn-cancel">
                                    <i class="fa fa-ban"></i> Annuler l'Abonnement
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Invoices -->
                <?php if (!empty($invoices)): ?>
                    <div class="panel_s">
                        <div class="panel-body">
                            <h4><i class="fa fa-file-text-o"></i> Factures</h4>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Numéro</th>
                                        <th>Date</th>
                                        <th>Montant</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($invoices as $invoice): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($invoice->invoice_number); ?></td>
                                            <td><?php echo _d($invoice->issue_date); ?></td>
                                            <td><?php echo number_format($invoice->total_amount, 0, ',', ' '); ?> FCFA</td>
                                            <td>
                                                <span class="label label-<?php echo $invoice->status == 'paid' ? 'success' : 'warning'; ?>">
                                                    <?php echo ucfirst($invoice->status); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="<?php echo admin_url('dietetic/invoices/view/' . $invoice->id); ?>" class="btn btn-default btn-sm">
                                                    <i class="fa fa-eye"></i> Voir
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-md-4">
                <!-- Stats -->
                <div class="panel_s">
                    <div class="panel-body">
                        <h4><i class="fa fa-bar-chart"></i> Statistiques</h4>
                        <div class="stat-box">
                            <p><strong style="font-size: 24px; color: #27ae60;">
                                <?php echo number_format($total_paid, 0, ',', ' '); ?> FCFA
                            </strong></p>
                            <p class="text-muted">Total payé</p>
                        </div>
                        <div class="stat-box">
                            <p><strong><?php echo count($invoices); ?></strong> Factures</p>
                        </div>
                        <div class="stat-box">
                            <p>
                                <strong>
                                    <?php
                                    $start = new DateTime($subscription->start_date);
                                    $now = new DateTime();
                                    $interval = $start->diff($now);
                                    echo $interval->days;
                                    ?>
                                </strong> Jours d'abonnement
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Timeline -->
                <div class="panel_s">
                    <div class="panel-body">
                        <h4><i class="fa fa-history"></i> Historique</h4>
                        <ul class="list-unstyled">
                            <li style="margin-bottom: 10px;">
                                <i class="fa fa-check text-success"></i>
                                Créé le <?php echo _dt($subscription->created_at); ?>
                            </li>
                            <?php if ($subscription->trial_end_date): ?>
                                <li style="margin-bottom: 10px;">
                                    <i class="fa fa-gift text-warning"></i>
                                    Essai jusqu'au <?php echo _d($subscription->trial_end_date); ?>
                                </li>
                            <?php endif; ?>
                            <?php if ($subscription->status == 'cancelled'): ?>
                                <li style="margin-bottom: 10px;">
                                    <i class="fa fa-ban text-danger"></i>
                                    Annulé le <?php echo _dt($subscription->cancelled_at); ?>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
$(document).ready(function() {
    $('#btn-cancel').on('click', function() {
        var reason = prompt('Raison de l\'annulation :');

        if (reason === null) {
            return;
        }

        $.ajax({
            url: '<?php echo admin_url('dietetic/subscriptions/cancel/' . $subscription->id); ?>',
            type: 'POST',
            data: {
                reason: reason,
                <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert_float('success', response.message);
                    location.reload();
                } else {
                    alert_float('danger', response.message);
                }
            }
        });
    });
});
</script>
