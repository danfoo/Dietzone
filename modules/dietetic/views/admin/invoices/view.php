<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8">
                <div class="panel_s">
                    <div class="panel-body">
                        <!-- Invoice Header -->
                        <div class="row">
                            <div class="col-md-6">
                                <h2 style="margin-top: 0;">FACTURE</h2>
                                <h4><?php echo htmlspecialchars($invoice->invoice_number); ?></h4>
                            </div>
                            <div class="col-md-6 text-right">
                                <span class="label label-<?php echo $invoice->status == 'paid' ? 'success' : ($invoice->status == 'overdue' ? 'danger' : 'warning'); ?>" style="font-size: 16px; padding: 10px 20px;">
                                    <?php
                                    $status_labels = [
                                        'draft' => 'Brouillon',
                                        'sent' => 'Envoyée',
                                        'paid' => 'Payée',
                                        'overdue' => 'En retard',
                                        'cancelled' => 'Annulée'
                                    ];
                                    echo $status_labels[$invoice->status] ?? $invoice->status;
                                    ?>
                                </span>
                            </div>
                        </div>

                        <hr />

                        <!-- Patient & Dietitian Info -->
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Facturé à :</h5>
                                <p>
                                    <strong><?php echo htmlspecialchars($invoice->patient_name); ?></strong><br>
                                    <?php if ($invoice->patient_email): ?>
                                        <?php echo htmlspecialchars($invoice->patient_email); ?><br>
                                    <?php endif; ?>
                                    <?php if ($invoice->patient_phone): ?>
                                        <?php echo htmlspecialchars($invoice->patient_phone); ?><br>
                                    <?php endif; ?>
                                    <?php if ($invoice->patient_address): ?>
                                        <?php echo nl2br(htmlspecialchars($invoice->patient_address)); ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="col-md-6 text-right">
                                <h5>De :</h5>
                                <p>
                                    <strong><?php echo htmlspecialchars($invoice->dietitian_name); ?></strong><br>
                                    Diététicien<br>
                                    <?php if ($invoice->dietitian_email): ?>
                                        <?php echo htmlspecialchars($invoice->dietitian_email); ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>

                        <hr />

                        <!-- Invoice Details -->
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Date d'émission :</strong> <?php echo _d($invoice->issue_date); ?></p>
                                <p><strong>Date d'échéance :</strong> <?php echo _d($invoice->due_date); ?></p>
                            </div>
                            <div class="col-md-6 text-right">
                                <p><strong>Plan :</strong> <?php echo htmlspecialchars($invoice->plan_name_fr ?: $invoice->plan_name); ?></p>
                                <p><strong>Source :</strong>
                                    <span class="label label-<?php echo $invoice->referral_source == 'platform' ? 'warning' : 'success'; ?>">
                                        <?php echo ucfirst($invoice->referral_source); ?>
                                    </span>
                                </p>
                            </div>
                        </div>

                        <hr />

                        <!-- Amounts Table -->
                        <table class="table table-bordered">
                            <thead>
                                <tr style="background: #f8f9fa;">
                                    <th>Description</th>
                                    <th class="text-right">Montant</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($invoice->plan_name_fr ?: $invoice->plan_name); ?></strong>
                                        <br><small class="text-muted">Abonnement au plan de service</small>
                                    </td>
                                    <td class="text-right">
                                        <?php echo number_format($invoice->amount, 0, ',', ' '); ?> FCFA
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-right"><strong>Sous-total (HT)</strong></td>
                                    <td class="text-right"><?php echo number_format($invoice->amount, 0, ',', ' '); ?> FCFA</td>
                                </tr>
                                <tr>
                                    <td class="text-right">TVA (<?php echo number_format($invoice->tax_rate, 2); ?>%)</td>
                                    <td class="text-right"><?php echo number_format($invoice->tax_amount, 0, ',', ' '); ?> FCFA</td>
                                </tr>
                                <tr style="background: #e8f5e9;">
                                    <td class="text-right"><h4 style="margin: 10px 0;"><strong>TOTAL (TTC)</strong></h4></td>
                                    <td class="text-right"><h4 style="margin: 10px 0; color: #27ae60;"><strong><?php echo number_format($invoice->total_amount, 0, ',', ' '); ?> FCFA</strong></h4></td>
                                </tr>
                            </tbody>
                        </table>

                        <?php if ($invoice->notes): ?>
                            <div class="alert alert-info">
                                <strong>Notes :</strong><br>
                                <?php echo nl2br(htmlspecialchars($invoice->notes)); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($invoice->status == 'paid'): ?>
                            <div class="alert alert-success">
                                <i class="fa fa-check-circle"></i>
                                <strong>Facture payée</strong> le <?php echo _dt($invoice->paid_date); ?>
                                <?php if ($invoice->payment_method): ?>
                                    par <?php echo $invoice->payment_method; ?>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <hr />

                        <!-- Actions -->
                        <div class="text-right">
                            <a href="<?php echo admin_url('dietetic/invoices'); ?>" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Retour
                            </a>
                            <a href="<?php echo admin_url('dietetic/invoices/pdf/' . $invoice->id); ?>" class="btn btn-info" target="_blank">
                                <i class="fa fa-file-pdf-o"></i> Télécharger PDF
                            </a>
                            <?php if ($invoice->status != 'paid' && $invoice->status != 'cancelled'): ?>
                                <button type="button" class="btn btn-success" id="btn-mark-paid">
                                    <i class="fa fa-check"></i> Marquer Payée
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Payments -->
                <?php if (!empty($payments)): ?>
                    <div class="panel_s">
                        <div class="panel-body">
                            <h4><i class="fa fa-credit-card"></i> Paiements</h4>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Montant</th>
                                        <th>Méthode</th>
                                        <th>Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($payments as $payment): ?>
                                        <tr>
                                            <td><?php echo _dt($payment->payment_date); ?></td>
                                            <td><?php echo number_format($payment->amount, 0, ',', ' '); ?> FCFA</td>
                                            <td><?php echo ucfirst($payment->payment_method); ?></td>
                                            <td>
                                                <span class="label label-<?php echo $payment->status == 'completed' ? 'success' : 'warning'; ?>">
                                                    <?php echo ucfirst($payment->status); ?>
                                                </span>
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
                <div class="panel_s">
                    <div class="panel-body">
                        <h4><i class="fa fa-info-circle"></i> Informations</h4>
                        <p><strong>Créée le :</strong><br><?php echo _dt($invoice->created_at); ?></p>
                        <?php if ($invoice->updated_at): ?>
                            <p><strong>Modifiée le :</strong><br><?php echo _dt($invoice->updated_at); ?></p>
                        <?php endif; ?>
                        <p><strong>Abonnement :</strong><br>
                            <a href="<?php echo admin_url('dietetic/subscriptions/view/' . $invoice->subscription_id); ?>">
                                Voir l'abonnement
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
$(document).ready(function() {
    $('#btn-mark-paid').on('click', function() {
        if (!confirm('Marquer cette facture comme payée ?')) {
            return;
        }

        $.ajax({
            url: '<?php echo admin_url('dietetic/invoices/mark_paid/' . $invoice->id); ?>',
            type: 'POST',
            data: {
                payment_method: 'cash',
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
