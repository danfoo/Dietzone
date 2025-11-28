<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="patient-invoices">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">
                <i class="fa fa-file-text-o"></i> Mes Factures
            </h3>
        </div>
        <div class="panel-body">
            <?php if (empty($invoices)): ?>
                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    Vous n'avez aucune facture pour le moment.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Numéro</th>
                                <th>Date</th>
                                <th>Échéance</th>
                                <th>Montant</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($invoices as $invoice): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($invoice->invoice_number); ?></strong>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($invoice->issue_date)); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($invoice->due_date)); ?></td>
                                <td>
                                    <strong><?php echo number_format($invoice->total_amount, 0, ',', ' '); ?> FCFA</strong>
                                </td>
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
                                <td>
                                    <a href="<?php echo site_url('dietetic/portal/invoice/' . $invoice->id); ?>"
                                       class="btn btn-sm btn-default">
                                        <i class="fa fa-eye"></i> Voir
                                    </a>
                                    <?php if (in_array($invoice->status, ['sent', 'overdue']) && $any_payment_enabled): ?>
                                    <a href="<?php echo site_url('dietetic/payment_gateways/pay/' . $invoice->id); ?>"
                                       class="btn btn-sm btn-success">
                                        <i class="fa fa-credit-card"></i> Payer
                                    </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Summary -->
                <div class="row" style="margin-top: 20px;">
                    <div class="col-md-4">
                        <div class="panel panel-default">
                            <div class="panel-body text-center">
                                <h4 style="margin-top: 0; color: #28a745;">
                                    <?php
                                    $total_paid = 0;
                                    foreach ($invoices as $inv) {
                                        if ($inv->status == 'paid') {
                                            $total_paid += $inv->total_amount;
                                        }
                                    }
                                    echo number_format($total_paid, 0, ',', ' ');
                                    ?> FCFA
                                </h4>
                                <p class="text-muted" style="margin: 0;">Total Payé</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="panel panel-default">
                            <div class="panel-body text-center">
                                <h4 style="margin-top: 0; color: #dc3545;">
                                    <?php
                                    $total_outstanding = 0;
                                    foreach ($invoices as $inv) {
                                        if (in_array($inv->status, ['sent', 'overdue'])) {
                                            $total_outstanding += $inv->total_amount;
                                        }
                                    }
                                    echo number_format($total_outstanding, 0, ',', ' ');
                                    ?> FCFA
                                </h4>
                                <p class="text-muted" style="margin: 0;">En Attente</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="panel panel-default">
                            <div class="panel-body text-center">
                                <h4 style="margin-top: 0; color: #007bff;">
                                    <?php echo count($invoices); ?>
                                </h4>
                                <p class="text-muted" style="margin: 0;">Total Factures</p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.patient-invoices .table > tbody > tr > td {
    vertical-align: middle;
}
</style>
