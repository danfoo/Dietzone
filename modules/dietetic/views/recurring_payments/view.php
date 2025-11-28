<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="row">
    <div class="col-md-8">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-refresh"></i> Paiement Récurrent #<?php echo $recurring->id; ?>
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
                                'active' => '<span class="label label-success">Actif</span>',
                                'paused' => '<span class="label label-warning">En pause</span>',
                                'cancelled' => '<span class="label label-default">Annulé</span>',
                                'expired' => '<span class="label label-info">Expiré</span>',
                                'failed' => '<span class="label label-danger">Échoué</span>'
                            ];
                            echo $status_badges[$recurring->status] ?? $recurring->status;
                            ?>
                        </h4>
                    </div>
                    <div class="col-md-6 text-right">
                        <?php if ($recurring->status == 'active'): ?>
                            <a href="<?php echo admin_url('dietetic/recurring_payments/pause/' . $recurring->id); ?>"
                               class="btn btn-warning">
                                <i class="fa fa-pause"></i> Mettre en pause
                            </a>
                            <a href="<?php echo admin_url('dietetic/recurring_payments/process/' . $recurring->id); ?>"
                               class="btn btn-info">
                                <i class="fa fa-play"></i> Traiter maintenant
                            </a>
                        <?php elseif ($recurring->status == 'paused'): ?>
                            <a href="<?php echo admin_url('dietetic/recurring_payments/resume/' . $recurring->id); ?>"
                               class="btn btn-success">
                                <i class="fa fa-play"></i> Reprendre
                            </a>
                        <?php elseif ($recurring->status == 'failed'): ?>
                            <a href="<?php echo admin_url('dietetic/recurring_payments/retry/' . $recurring->id); ?>"
                               class="btn btn-primary">
                                <i class="fa fa-refresh"></i> Réessayer
                            </a>
                        <?php endif; ?>

                        <?php if (in_array($recurring->status, ['active', 'paused'])): ?>
                            <a href="<?php echo admin_url('dietetic/recurring_payments/cancel/' . $recurring->id); ?>"
                               class="btn btn-danger">
                                <i class="fa fa-times"></i> Annuler
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <hr>

                <!-- Details -->
                <div class="row">
                    <div class="col-md-6">
                        <h4>Informations du patient</h4>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Patient:</strong></td>
                                <td>
                                    <a href="<?php echo admin_url('dietetic/patients/view/' . $patient->id); ?>">
                                        <?php echo htmlspecialchars($patient->firstname . ' ' . $patient->lastname); ?>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Abonnement:</strong></td>
                                <td>
                                    <a href="<?php echo admin_url('dietetic/subscriptions/view/' . $subscription->id); ?>">
                                        #<?php echo $subscription->id; ?> - <?php echo htmlspecialchars($subscription->plan_name ?? 'N/A'); ?>
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="col-md-6">
                        <h4>Détails du paiement</h4>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Montant:</strong></td>
                                <td><h3 style="margin: 0;"><?php echo number_format($recurring->amount, 0, ',', ' '); ?> FCFA</h3></td>
                            </tr>
                            <tr>
                                <td><strong>Fréquence:</strong></td>
                                <td>
                                    <?php
                                    $frequencies = [
                                        'daily' => 'Quotidien',
                                        'weekly' => 'Hebdomadaire',
                                        'monthly' => 'Mensuel',
                                        'quarterly' => 'Trimestriel',
                                        'yearly' => 'Annuel'
                                    ];
                                    echo $frequencies[$recurring->frequency] ?? $recurring->frequency;
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Méthode:</strong></td>
                                <td><?php echo ucfirst(str_replace('_', ' ', $recurring->payment_method)); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Prochain paiement:</strong></td>
                                <td><strong><?php echo date('d/m/Y', strtotime($recurring->next_payment_date)); ?></strong></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <hr>

                <!-- Additional Info -->
                <div class="row">
                    <div class="col-md-12">
                        <h4>Informations supplémentaires</h4>
                        <table class="table table-striped">
                            <tr>
                                <td width="30%"><strong>Créé le:</strong></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($recurring->created_at)); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Dernier paiement:</strong></td>
                                <td><?php echo $recurring->last_payment_date ? date('d/m/Y', strtotime($recurring->last_payment_date)) : 'Aucun'; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Nombre de tentatives:</strong></td>
                                <td><?php echo $recurring->retry_count; ?> / <?php echo $recurring->max_retries; ?></td>
                            </tr>
                            <?php if ($recurring->end_date): ?>
                            <tr>
                                <td><strong>Date de fin:</strong></td>
                                <td><?php echo date('d/m/Y', strtotime($recurring->end_date)); ?></td>
                            </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transaction History -->
        <?php if (!empty($transactions)): ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-history"></i> Historique des transactions
                </h3>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Facture</th>
                                <th>Paiement</th>
                                <th>Montant</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($transactions as $txn): ?>
                            <tr>
                                <td><?php echo date('d/m/Y H:i', strtotime($txn->created_at)); ?></td>
                                <td>
                                    <?php if ($txn->invoice_id): ?>
                                        <a href="<?php echo admin_url('dietetic/invoices/view/' . $txn->invoice_id); ?>">
                                            <?php echo htmlspecialchars($txn->invoice_number ?? '#' . $txn->invoice_id); ?>
                                        </a>
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($txn->payment_id): ?>
                                        <a href="<?php echo admin_url('dietetic/payments/view/' . $txn->payment_id); ?>">
                                            #<?php echo $txn->payment_id; ?>
                                        </a>
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </td>
                                <td><?php echo number_format($txn->amount, 0, ',', ' '); ?> FCFA</td>
                                <td>
                                    <?php if ($txn->status == 'success'): ?>
                                        <span class="label label-success">Succès</span>
                                    <?php elseif ($txn->status == 'failed'): ?>
                                        <span class="label label-danger">Échoué</span>
                                    <?php else: ?>
                                        <span class="label label-default"><?php echo htmlspecialchars($txn->status); ?></span>
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
    </div>

    <!-- Statistics Sidebar -->
    <div class="col-md-4">
        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-bar-chart"></i> Statistiques
                </h3>
            </div>
            <div class="panel-body">
                <?php if (!empty($statistics)): ?>
                    <div class="text-center">
                        <h3><?php echo $statistics['total_processed'] ?? 0; ?></h3>
                        <p>Paiements traités</p>
                    </div>
                    <hr>
                    <div class="text-center">
                        <h3 class="text-success"><?php echo $statistics['successful_payments'] ?? 0; ?></h3>
                        <p>Réussis</p>
                    </div>
                    <hr>
                    <div class="text-center">
                        <h3 class="text-danger"><?php echo $statistics['failed_payments'] ?? 0; ?></h3>
                        <p>Échoués</p>
                    </div>
                    <hr>
                    <div class="text-center">
                        <h3><?php echo number_format($statistics['total_amount'] ?? 0, 0, ',', ' '); ?> FCFA</h3>
                        <p>Montant total collecté</p>
                    </div>
                <?php else: ?>
                    <p class="text-center text-muted">Aucune statistique disponible</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Actions rapides</h3>
            </div>
            <div class="panel-body">
                <a href="<?php echo admin_url('dietetic/recurring_payments/edit/' . $recurring->id); ?>"
                   class="btn btn-block btn-default">
                    <i class="fa fa-edit"></i> Modifier
                </a>
                <a href="<?php echo admin_url('dietetic/recurring_payments'); ?>"
                   class="btn btn-block btn-default">
                    <i class="fa fa-list"></i> Tous les paiements récurrents
                </a>
            </div>
        </div>
    </div>
</div>
