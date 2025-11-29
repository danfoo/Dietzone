<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $this->load->view('portal/includes/portal_header'); ?>

<style>
    .subscription-detail-container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 20px 15px;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #01807B;
        text-decoration: none;
        font-weight: 600;
        margin-bottom: 20px;
        transition: all 0.3s;
    }

    .back-link:hover {
        color: #026660;
        text-decoration: none;
        transform: translateX(-4px);
    }

    .subscription-header-card {
        background: linear-gradient(135deg, #01807B, #019B95);
        color: white;
        border-radius: 16px;
        padding: 32px;
        margin-bottom: 24px;
        box-shadow: 0 4px 20px rgba(1, 128, 123, 0.2);
    }

    .subscription-title {
        font-size: 32px;
        font-weight: 700;
        margin: 0 0 12px 0;
    }

    .subscription-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        align-items: center;
        opacity: 0.95;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
    }

    .meta-item i {
        font-size: 16px;
    }

    .section-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 20px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    }

    .section-title {
        font-size: 20px;
        font-weight: 700;
        color: #2c3e50;
        margin: 0 0 20px 0;
        display: flex;
        align-items: center;
        gap: 10px;
        padding-bottom: 12px;
        border-bottom: 2px solid #f0f0f0;
    }

    .section-title i {
        color: #01807B;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 16px;
    }

    .info-item {
        padding: 16px;
        background: #f8f9fa;
        border-radius: 10px;
        border-left: 4px solid #01807B;
    }

    .info-item-label {
        font-size: 12px;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
        font-weight: 600;
    }

    .info-item-value {
        font-size: 18px;
        font-weight: 700;
        color: #2c3e50;
    }

    .table-responsive {
        overflow-x: auto;
        margin-top: 16px;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table thead {
        background: #f8f9fa;
    }

    .data-table th {
        padding: 14px 16px;
        text-align: left;
        font-weight: 700;
        font-size: 13px;
        color: #495057;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #dee2e6;
    }

    .data-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #f0f0f0;
        color: #2c3e50;
    }

    .data-table tr:hover {
        background: #f8f9fa;
    }

    .badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .badge-success {
        background: #d4edda;
        color: #155724;
    }

    .badge-warning {
        background: #fff3cd;
        color: #856404;
    }

    .badge-danger {
        background: #f8d7da;
        color: #721c24;
    }

    .badge-info {
        background: #d1ecf1;
        color: #0c5460;
    }

    .empty-message {
        text-align: center;
        padding: 40px 20px;
        color: #6c757d;
    }

    .empty-message i {
        font-size: 48px;
        margin-bottom: 16px;
        opacity: 0.5;
    }

    .amount {
        font-weight: 700;
        color: #01807B;
    }

    .recurring-info-card {
        background: linear-gradient(135deg, rgba(1, 128, 123, 0.1), rgba(1, 155, 149, 0.1));
        border: 2px solid #01807B;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .recurring-info-title {
        font-size: 18px;
        font-weight: 700;
        color: #01807B;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .recurring-info-title i {
        animation: rotate 2s linear infinite;
    }

    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .timeline {
        position: relative;
        padding-left: 30px;
        margin-top: 20px;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 8px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e0e0e0;
    }

    .timeline-item {
        position: relative;
        padding-bottom: 24px;
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: -25px;
        top: 4px;
        width: 12px;
        height: 12px;
        background: white;
        border: 3px solid #01807B;
        border-radius: 50%;
        z-index: 1;
    }

    .timeline-item.success::before {
        border-color: #28a745;
        background: #28a745;
    }

    .timeline-item.failed::before {
        border-color: #dc3545;
        background: #dc3545;
    }

    .timeline-item.pending::before {
        border-color: #ffc107;
        background: #ffc107;
    }

    .timeline-content {
        background: white;
        padding: 16px;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    }

    .timeline-date {
        font-size: 12px;
        color: #6c757d;
        margin-bottom: 6px;
        font-weight: 600;
    }

    .timeline-title {
        font-size: 15px;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 4px;
    }

    .timeline-description {
        font-size: 13px;
        color: #6c757d;
    }

    @media (max-width: 768px) {
        .subscription-header-card {
            padding: 24px;
        }

        .subscription-title {
            font-size: 24px;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .data-table {
            font-size: 13px;
        }

        .data-table th,
        .data-table td {
            padding: 10px 12px;
        }
    }
</style>

<div class="subscription-detail-container">
    <a href="<?php echo site_url('dietetic/portal/subscriptions'); ?>" class="back-link">
        <i class="fa fa-arrow-left"></i>
        Retour aux Abonnements
    </a>

    <div class="subscription-header-card">
        <h1 class="subscription-title">
            <?php echo htmlspecialchars($subscription->plan_name_fr ?: $subscription->plan_name); ?>
        </h1>
        <div class="subscription-meta">
            <div class="meta-item">
                <i class="fa fa-tag"></i>
                <span class="badge badge-<?php
                    echo $subscription->status == 'active' ? 'success' :
                        ($subscription->status == 'expired' ? 'danger' :
                        ($subscription->status == 'cancelled' ? 'warning' : 'info'));
                ?>">
                    <?php
                    $status_labels = [
                        'active' => 'Actif',
                        'expired' => 'Expiré',
                        'cancelled' => 'Annulé',
                        'pending' => 'En attente'
                    ];
                    echo $status_labels[$subscription->status] ?? $subscription->status;
                    ?>
                </span>
            </div>
            <div class="meta-item">
                <i class="fa fa-user-md"></i>
                <?php echo htmlspecialchars($subscription->dietitian_name); ?>
            </div>
            <div class="meta-item">
                <i class="fa fa-calendar"></i>
                Depuis le <?php echo date('d/m/Y', strtotime($subscription->start_date)); ?>
            </div>
        </div>
    </div>

    <!-- Recurring Payment Info -->
    <?php if (!empty($subscription->is_recurring) && !empty($subscription->recurring_payment)): ?>
        <div class="recurring-info-card">
            <div class="recurring-info-title">
                <i class="fa fa-refresh"></i>
                Paiement Récurrent Actif
            </div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-item-label">Fréquence</div>
                    <div class="info-item-value">
                        <?php
                        $frequency_labels = [
                            'monthly' => 'Mensuel',
                            'quarterly' => 'Trimestriel',
                            'yearly' => 'Annuel'
                        ];
                        echo $frequency_labels[$subscription->recurring_payment->frequency] ?? $subscription->recurring_payment->frequency;
                        ?>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-item-label">Montant</div>
                    <div class="info-item-value amount">
                        <?php echo number_format($subscription->recurring_payment->amount, 0, ',', ' '); ?> FCFA
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-item-label">Prochain Paiement</div>
                    <div class="info-item-value">
                        <?php echo date('d/m/Y', strtotime($subscription->recurring_payment->next_payment_date)); ?>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-item-label">Statut</div>
                    <div class="info-item-value">
                        <span class="badge badge-<?php
                            echo $subscription->recurring_payment->status == 'active' ? 'success' :
                                ($subscription->recurring_payment->status == 'paused' ? 'warning' : 'danger');
                        ?>">
                            <?php echo ucfirst($subscription->recurring_payment->status); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Subscription Details -->
    <div class="section-card">
        <h2 class="section-title">
            <i class="fa fa-info-circle"></i>
            Détails de l'Abonnement
        </h2>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-item-label">Date de Début</div>
                <div class="info-item-value"><?php echo date('d/m/Y', strtotime($subscription->start_date)); ?></div>
            </div>
            <?php if ($subscription->end_date): ?>
                <div class="info-item">
                    <div class="info-item-label">Date de Fin</div>
                    <div class="info-item-value"><?php echo date('d/m/Y', strtotime($subscription->end_date)); ?></div>
                </div>
            <?php endif; ?>
            <div class="info-item">
                <div class="info-item-label">Montant</div>
                <div class="info-item-value amount"><?php echo number_format($subscription->amount, 0, ',', ' '); ?> FCFA</div>
            </div>
            <?php if ($subscription->duration_days): ?>
                <div class="info-item">
                    <div class="info-item-label">Durée</div>
                    <div class="info-item-value"><?php echo $subscription->duration_days; ?> jours</div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recurring Transactions Timeline -->
    <?php if (!empty($subscription->recurring_transactions)): ?>
        <div class="section-card">
            <h2 class="section-title">
                <i class="fa fa-history"></i>
                Historique des Paiements Récurrents
            </h2>
            <div class="timeline">
                <?php foreach ($subscription->recurring_transactions as $transaction): ?>
                    <div class="timeline-item <?php echo strtolower($transaction->status); ?>">
                        <div class="timeline-content">
                            <div class="timeline-date">
                                <?php echo date('d/m/Y à H:i', strtotime($transaction->transaction_date)); ?>
                            </div>
                            <div class="timeline-title">
                                <span class="amount"><?php echo number_format($transaction->amount, 0, ',', ' '); ?> FCFA</span>
                                -
                                <span class="badge badge-<?php
                                    echo $transaction->status == 'success' ? 'success' :
                                        ($transaction->status == 'failed' ? 'danger' :
                                        ($transaction->status == 'pending' ? 'warning' : 'info'));
                                ?>">
                                    <?php
                                    $transaction_status = [
                                        'success' => 'Réussi',
                                        'failed' => 'Échoué',
                                        'pending' => 'En attente',
                                        'refunded' => 'Remboursé'
                                    ];
                                    echo $transaction_status[$transaction->status] ?? $transaction->status;
                                    ?>
                                </span>
                            </div>
                            <?php if (!empty($transaction->notes)): ?>
                                <div class="timeline-description">
                                    <?php echo htmlspecialchars($transaction->notes); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Related Invoices -->
    <?php if (!empty($invoices)): ?>
        <div class="section-card">
            <h2 class="section-title">
                <i class="fa fa-file-text"></i>
                Factures Associées
            </h2>
            <div class="table-responsive">
                <table class="data-table">
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
                                <td><strong><?php echo htmlspecialchars($invoice->invoice_number); ?></strong></td>
                                <td><?php echo date('d/m/Y', strtotime($invoice->invoice_date)); ?></td>
                                <td class="amount"><?php echo number_format($invoice->total_amount, 0, ',', ' '); ?> FCFA</td>
                                <td>
                                    <span class="badge badge-<?php
                                        echo $invoice->status == 'paid' ? 'success' :
                                            ($invoice->status == 'unpaid' ? 'warning' :
                                            ($invoice->status == 'overdue' ? 'danger' : 'info'));
                                    ?>">
                                        <?php
                                        $invoice_status = [
                                            'paid' => 'Payée',
                                            'unpaid' => 'Impayée',
                                            'overdue' => 'En retard',
                                            'cancelled' => 'Annulée'
                                        ];
                                        echo $invoice_status[$invoice->status] ?? $invoice->status;
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?php echo site_url('dietetic/portal/invoice/' . $invoice->id); ?>" style="color: #01807B; text-decoration: none; font-weight: 600;">
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

<?php $this->load->view('portal/includes/portal_footer'); ?>
