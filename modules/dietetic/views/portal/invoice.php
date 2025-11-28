<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="patient-invoice-detail">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">
                <i class="fa fa-file-text-o"></i> Facture <?php echo htmlspecialchars($invoice->invoice_number); ?>
            </h3>
        </div>
        <div class="panel-body">
            <!-- Invoice Header -->
            <div class="row">
                <div class="col-md-6">
                    <h4>Informations</h4>
                    <table class="table table-borderless">
                        <tr>
                            <td width="40%"><strong>Numéro :</strong></td>
                            <td><?php echo htmlspecialchars($invoice->invoice_number); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Date d'émission :</strong></td>
                            <td><?php echo date('d/m/Y', strtotime($invoice->issue_date)); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Date d'échéance :</strong></td>
                            <td><?php echo date('d/m/Y', strtotime($invoice->due_date)); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Statut :</strong></td>
                            <td>
                                <?php
                                $status_badges = [
                                    'draft' => '<span class="label label-default">Brouillon</span>',
                                    'sent' => '<span class="label label-info">Envoyée</span>',
                                    'paid' => '<span class="label label-success">Payée</span>',
                                    'overdue' => '<span class="label label-danger">En retard</span>',
                                    'cancelled' => '<span class="label label-default">Annulée</span>'
                                ];
                                echo isset($status_badges[$invoice->status]) ? $status_badges[$invoice->status] : $invoice->status;
                                ?>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <div class="well text-center" style="background: #f0f8ff; border: 2px solid #007bff;">
                        <h2 style="margin-top: 0; color: #007bff;">
                            <?php echo number_format($invoice->total_amount, 0, ',', ' '); ?> FCFA
                        </h2>
                        <p class="text-muted" style="margin: 0;">Montant Total</p>

                        <?php if (in_array($invoice->status, ['sent', 'overdue'])): ?>
                            <?php if ($any_payment_enabled): ?>
                            <hr>
                            <a href="<?php echo site_url('dietetic/payment_gateways/pay/' . $invoice->id); ?>"
                               class="btn btn-success btn-lg btn-block">
                                <i class="fa fa-credit-card"></i> Payer en ligne
                            </a>
                            <?php else: ?>
                            <hr>
                            <p class="text-muted">
                                <i class="fa fa-info-circle"></i>
                                Le paiement en ligne n'est pas encore disponible
                            </p>
                            <?php endif; ?>
                        <?php elseif ($invoice->status == 'paid'): ?>
                            <hr>
                            <p style="color: #28a745; font-size: 18px;">
                                <i class="fa fa-check-circle"></i>
                                <strong>Facture payée</strong>
                            </p>
                            <?php if ($invoice->paid_date): ?>
                            <small class="text-muted">
                                Payée le <?php echo date('d/m/Y', strtotime($invoice->paid_date)); ?>
                            </small>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <hr>

            <!-- Service Details -->
            <div class="row">
                <div class="col-md-12">
                    <h4>Détails du service</h4>
                    <?php if ($invoice->plan_name): ?>
                    <p><strong>Plan :</strong> <?php echo htmlspecialchars($invoice->plan_name); ?></p>
                    <?php endif; ?>

                    <?php if ($invoice->subscription_id): ?>
                    <p><strong>Abonnement :</strong> #<?php echo $invoice->subscription_id; ?></p>
                    <?php endif; ?>

                    <?php if ($invoice->notes): ?>
                    <p><strong>Notes :</strong><br><?php echo nl2br(htmlspecialchars($invoice->notes)); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!empty($payments)): ?>
            <hr>
            <!-- Payment History -->
            <div class="row">
                <div class="col-md-12">
                    <h4>Historique des paiements</h4>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Montant</th>
                                    <th>Méthode</th>
                                    <th>Référence</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($payments as $payment): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y H:i', strtotime($payment->payment_date)); ?></td>
                                    <td><strong><?php echo number_format($payment->amount, 0, ',', ' '); ?> FCFA</strong></td>
                                    <td>
                                        <?php
                                        $method_labels = [
                                            'cash' => 'Espèces',
                                            'bank_transfer' => 'Virement bancaire',
                                            'card' => 'Carte bancaire',
                                            'mobile_money' => 'Mobile Money',
                                            'paypal' => 'PayPal',
                                            'wave' => 'Wave',
                                            'orange_money' => 'Orange Money',
                                            'other' => 'Autre'
                                        ];
                                        echo isset($method_labels[$payment->payment_method])
                                            ? $method_labels[$payment->payment_method]
                                            : htmlspecialchars($payment->payment_method);
                                        ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($payment->payment_reference ?? 'N/A'); ?></td>
                                    <td>
                                        <?php if ($payment->status == 'completed'): ?>
                                            <span class="label label-success">Complété</span>
                                        <?php elseif ($payment->status == 'pending'): ?>
                                            <span class="label label-warning">En attente</span>
                                        <?php elseif ($payment->status == 'failed'): ?>
                                            <span class="label label-danger">Échoué</span>
                                        <?php else: ?>
                                            <span class="label label-default"><?php echo htmlspecialchars($payment->status); ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div style="margin-top: 30px; text-align: center;">
                <a href="<?php echo site_url('dietetic/portal/invoices'); ?>" class="btn btn-default">
                    <i class="fa fa-arrow-left"></i> Retour aux factures
                </a>
            </div>
        </div>
    </div>
</div>
