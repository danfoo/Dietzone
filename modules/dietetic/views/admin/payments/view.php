<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8">
                <div class="panel_s">
                    <div class="panel-body">
                        <!-- Payment Header -->
                        <div class="row">
                            <div class="col-md-6">
                                <h2 style="margin-top: 0;">PAIEMENT #<?php echo $payment->id; ?></h2>
                            </div>
                            <div class="col-md-6 text-right">
                                <span class="label label-<?php echo $payment->status == 'completed' ? 'success' : ($payment->status == 'pending' ? 'warning' : 'danger'); ?>" style="font-size: 16px; padding: 10px 20px;">
                                    <?php
                                    $status_labels = [
                                        'pending' => 'En attente',
                                        'completed' => 'Complété',
                                        'failed' => 'Échoué',
                                        'refunded' => 'Remboursé',
                                        'cancelled' => 'Annulé'
                                    ];
                                    echo $status_labels[$payment->status] ?? ucfirst($payment->status);
                                    ?>
                                </span>
                            </div>
                        </div>

                        <hr />

                        <!-- Payment Details -->
                        <div class="row">
                            <div class="col-md-6">
                                <h4>Informations du Paiement</h4>
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="40%">Montant</th>
                                        <td><strong style="font-size: 18px; color: #27ae60;"><?php echo number_format($payment->amount, 0, ',', ' '); ?> FCFA</strong></td>
                                    </tr>
                                    <tr>
                                        <th>Méthode de Paiement</th>
                                        <td>
                                            <?php
                                            $payment_methods = [
                                                'card' => 'Carte bancaire',
                                                'bank_transfer' => 'Virement bancaire',
                                                'cash' => 'Espèces',
                                                'mobile_money' => 'Mobile Money',
                                                'wave' => 'Wave',
                                                'orange_money' => 'Orange Money',
                                                'paypal' => 'PayPal'
                                            ];
                                            echo $payment_methods[$payment->payment_method] ?? ucfirst($payment->payment_method);
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Date du Paiement</th>
                                        <td><?php echo _d($payment->payment_date); ?></td>
                                    </tr>
                                    <?php if ($payment->reference): ?>
                                        <tr>
                                            <th>Référence</th>
                                            <td><code><?php echo htmlspecialchars($payment->reference); ?></code></td>
                                        </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <th>Enregistré le</th>
                                        <td><?php echo _dt($payment->created_at); ?></td>
                                    </tr>
                                </table>
                            </div>

                            <div class="col-md-6">
                                <h4>Informations Associées</h4>
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="40%">Facture</th>
                                        <td>
                                            <a href="<?php echo admin_url('dietetic/invoices/view/' . $payment->invoice_id); ?>">
                                                Facture #<?php echo $payment->invoice_id; ?>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Abonnement</th>
                                        <td>
                                            <a href="<?php echo admin_url('dietetic/subscriptions/view/' . $payment->subscription_id); ?>">
                                                Abonnement #<?php echo $payment->subscription_id; ?>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Patient</th>
                                        <td>
                                            <a href="<?php echo admin_url('clients/client/' . $payment->patient_id); ?>">
                                                <?php echo htmlspecialchars($payment->patient_name ?? 'Patient #' . $payment->patient_id); ?>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Diététicien</th>
                                        <td>
                                            <a href="<?php echo admin_url('staff/profile/' . $payment->dietitian_id); ?>">
                                                <?php echo htmlspecialchars($payment->dietitian_name ?? 'Diététicien #' . $payment->dietitian_id); ?>
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <?php if ($payment->notes): ?>
                            <hr />
                            <div class="alert alert-info">
                                <strong>Notes :</strong><br>
                                <?php echo nl2br(htmlspecialchars($payment->notes)); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($payment->status == 'completed'): ?>
                            <div class="alert alert-success">
                                <i class="fa fa-check-circle"></i>
                                <strong>Paiement complété avec succès.</strong>
                                Les commissions ont été automatiquement calculées et enregistrées.
                            </div>
                        <?php endif; ?>

                        <hr />

                        <!-- Actions -->
                        <div class="text-right">
                            <a href="<?php echo admin_url('dietetic/invoices/view/' . $payment->invoice_id); ?>" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Retour à la Facture
                            </a>
                            <?php if (is_admin() && $payment->status == 'completed'): ?>
                                <button type="button" class="btn btn-danger" id="btn-cancel-payment">
                                    <i class="fa fa-times"></i> Annuler le Paiement
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4><i class="fa fa-info-circle"></i> Statut</h4>
                        <p><strong>Statut actuel :</strong><br>
                            <span class="label label-<?php echo $payment->status == 'completed' ? 'success' : ($payment->status == 'pending' ? 'warning' : 'danger'); ?>">
                                <?php
                                $status_labels = [
                                    'pending' => 'En attente',
                                    'completed' => 'Complété',
                                    'failed' => 'Échoué',
                                    'refunded' => 'Remboursé',
                                    'cancelled' => 'Annulé'
                                ];
                                echo $status_labels[$payment->status] ?? ucfirst($payment->status);
                                ?>
                            </span>
                        </p>
                        <?php if ($payment->updated_at): ?>
                            <p><strong>Dernière modification :</strong><br><?php echo _dt($payment->updated_at); ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="panel_s">
                    <div class="panel-body">
                        <h4><i class="fa fa-percent"></i> Commissions</h4>
                        <p class="text-muted">
                            Les commissions ont été calculées automatiquement lors de l'enregistrement du paiement.
                        </p>
                        <a href="<?php echo admin_url('dietetic/commissions'); ?>" class="btn btn-sm btn-info btn-block">
                            <i class="fa fa-eye"></i> Voir les Commissions
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
$(document).ready(function() {
    $('#btn-cancel-payment').on('click', function() {
        if (!confirm('Êtes-vous sûr de vouloir annuler ce paiement ?\n\nATTENTION : Cette action annulera également les commissions associées et remettra la facture en statut "non payée".')) {
            return;
        }

        $.ajax({
            url: '<?php echo admin_url('dietetic/payments/cancel/' . $payment->id); ?>',
            type: 'POST',
            data: {
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
