<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="row">
    <div class="col-md-8">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-undo"></i> Remboursement <?php echo htmlspecialchars($refund->refund_number); ?>
                </h3>
            </div>
            <div class="panel-body">
                <!-- Status and Actions -->
                <div class="row">
                    <div class="col-md-6">
                        <h4>
                            Statut:
                            <?php
                            $status_badges = [
                                'pending' => '<span class="label label-warning">En attente</span>',
                                'approved' => '<span class="label label-info">Approuvé</span>',
                                'processing' => '<span class="label label-info">En traitement</span>',
                                'completed' => '<span class="label label-success">Complété</span>',
                                'rejected' => '<span class="label label-danger">Rejeté</span>',
                                'cancelled' => '<span class="label label-default">Annulé</span>'
                            ];
                            echo $status_badges[$refund->status] ?? $refund->status;
                            ?>
                        </h4>
                    </div>
                    <div class="col-md-6 text-right">
                        <?php if ($refund->status == 'pending'): ?>
                            <a href="<?php echo admin_url('dietetic/refunds/approve/' . $refund->id); ?>"
                               class="btn btn-success">
                                <i class="fa fa-check"></i> Approuver
                            </a>
                            <a href="<?php echo admin_url('dietetic/refunds/reject/' . $refund->id); ?>"
                               class="btn btn-danger">
                                <i class="fa fa-times"></i> Rejeter
                            </a>
                        <?php elseif ($refund->status == 'approved' || $refund->status == 'processing'): ?>
                            <a href="<?php echo admin_url('dietetic/refunds/process/' . $refund->id); ?>"
                               class="btn btn-primary">
                                <i class="fa fa-cog"></i> Traiter
                            </a>
                        <?php endif; ?>

                        <?php if (in_array($refund->status, ['pending', 'approved'])): ?>
                            <a href="<?php echo admin_url('dietetic/refunds/cancel/' . $refund->id); ?>"
                               class="btn btn-default">
                                <i class="fa fa-ban"></i> Annuler
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <hr>

                <!-- Refund Details -->
                <div class="row">
                    <div class="col-md-12">
                        <h4>Détails du remboursement</h4>
                        <div class="well" style="background: #f9f9f9;">
                            <div class="row">
                                <div class="col-md-6">
                                    <h2 style="margin-top: 0; color: #d9534f;">
                                        <?php echo number_format($refund->refund_amount, 0, ',', ' '); ?> FCFA
                                    </h2>
                                    <p>
                                        Type: <strong><?php echo $refund->refund_type == 'full' ? 'Complet' : 'Partiel'; ?></strong>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Méthode:</strong> <?php echo ucfirst(str_replace('_', ' ', $refund->refund_method)); ?></p>
                                    <?php if ($refund->refund_reference): ?>
                                        <p><strong>Référence:</strong> <?php echo htmlspecialchars($refund->refund_reference); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Patient and Payment Info -->
                <div class="row">
                    <div class="col-md-6">
                        <h4>Patient</h4>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Nom:</strong></td>
                                <td>
                                    <a href="<?php echo admin_url('dietetic/patients/view/' . $patient->id); ?>">
                                        <?php echo htmlspecialchars($patient->firstname . ' ' . $patient->lastname); ?>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td><?php echo htmlspecialchars($patient->email ?? 'N/A'); ?></td>
                            </tr>
                        </table>
                    </div>

                    <div class="col-md-6">
                        <h4>Paiement original</h4>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Facture:</strong></td>
                                <td>
                                    <a href="<?php echo admin_url('dietetic/invoices/view/' . $invoice->id); ?>">
                                        <?php echo htmlspecialchars($invoice->invoice_number); ?>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Montant payé:</strong></td>
                                <td><?php echo number_format($payment->amount, 0, ',', ' '); ?> FCFA</td>
                            </tr>
                            <tr>
                                <td><strong>Méthode:</strong></td>
                                <td><?php echo ucfirst(str_replace('_', ' ', $payment->payment_method)); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Date:</strong></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($payment->payment_date)); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <hr>

                <!-- Reason -->
                <?php if ($refund->reason): ?>
                <div class="row">
                    <div class="col-md-12">
                        <h4>Raison du remboursement</h4>
                        <div class="alert alert-info">
                            <?php echo nl2br(htmlspecialchars($refund->reason)); ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Processing Info -->
                <?php if ($refund->status == 'rejected' && $refund->notes): ?>
                <div class="row">
                    <div class="col-md-12">
                        <h4>Raison du rejet</h4>
                        <div class="alert alert-danger">
                            <?php echo nl2br(htmlspecialchars($refund->notes)); ?>
                        </div>
                    </div>
                </div>
                <?php elseif ($refund->status == 'completed' && $refund->notes): ?>
                <div class="row">
                    <div class="col-md-12">
                        <h4>Notes de traitement</h4>
                        <div class="alert alert-success">
                            <?php echo nl2br(htmlspecialchars($refund->notes)); ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Timeline -->
                <div class="row">
                    <div class="col-md-12">
                        <h4>Chronologie</h4>
                        <table class="table table-striped">
                            <tr>
                                <td width="30%"><strong>Demande initiée:</strong></td>
                                <td>
                                    <?php echo date('d/m/Y H:i', strtotime($refund->created_at)); ?>
                                    <?php if ($refund->initiated_by): ?>
                                        par Staff ID #<?php echo $refund->initiated_by; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php if ($refund->approved_at): ?>
                            <tr>
                                <td><strong>Approuvé:</strong></td>
                                <td>
                                    <?php echo date('d/m/Y H:i', strtotime($refund->approved_at)); ?>
                                    <?php if ($refund->approved_by): ?>
                                        par Staff ID #<?php echo $refund->approved_by; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endif; ?>
                            <?php if ($refund->processed_at): ?>
                            <tr>
                                <td><strong>Traité:</strong></td>
                                <td>
                                    <?php echo date('d/m/Y H:i', strtotime($refund->processed_at)); ?>
                                    <?php if ($refund->processed_by): ?>
                                        par Staff ID #<?php echo $refund->processed_by; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-md-4">
        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-info-circle"></i> Informations
                </h3>
            </div>
            <div class="panel-body">
                <dl>
                    <dt>Numéro de remboursement:</dt>
                    <dd><strong><?php echo htmlspecialchars($refund->refund_number); ?></strong></dd>

                    <dt>Type:</dt>
                    <dd>
                        <?php if ($refund->refund_type == 'full'): ?>
                            <span class="label label-primary">Remboursement complet</span>
                        <?php else: ?>
                            <span class="label label-info">Remboursement partiel</span>
                        <?php endif; ?>
                    </dd>

                    <dt>Montant:</dt>
                    <dd><h4><?php echo number_format($refund->refund_amount, 0, ',', ' '); ?> FCFA</h4></dd>

                    <dt>Traitement automatique:</dt>
                    <dd>
                        <?php if ($refund->auto_process): ?>
                            <i class="fa fa-check text-success"></i> Oui
                        <?php else: ?>
                            <i class="fa fa-times text-muted"></i> Non (manuel)
                        <?php endif; ?>
                    </dd>
                </dl>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Actions rapides</h3>
            </div>
            <div class="panel-body">
                <a href="<?php echo admin_url('dietetic/invoices/view/' . $invoice->id); ?>"
                   class="btn btn-block btn-default">
                    <i class="fa fa-file-text-o"></i> Voir la facture
                </a>
                <a href="<?php echo admin_url('dietetic/patients/view/' . $patient->id); ?>"
                   class="btn btn-block btn-default">
                    <i class="fa fa-user"></i> Voir le patient
                </a>
                <a href="<?php echo admin_url('dietetic/refunds'); ?>"
                   class="btn btn-block btn-default">
                    <i class="fa fa-list"></i> Tous les remboursements
                </a>
            </div>
        </div>
    </div>
</div>
