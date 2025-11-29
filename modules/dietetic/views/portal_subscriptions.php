<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $this->load->view('portal/includes/portal_header'); ?>

<style>
    .subscription-container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 20px 15px;
    }

    .page-title {
        font-size: 28px;
        font-weight: 700;
        color: #2c3e50;
        margin: 0 0 25px 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .page-title i {
        color: #01807B;
    }

    .subscriptions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }

    .subscription-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        transition: all 0.3s;
        border: 2px solid transparent;
        position: relative;
        overflow: hidden;
    }

    .subscription-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #01807B, #F3911D);
    }

    .subscription-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        border-color: #01807B;
    }

    .subscription-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 16px;
    }

    .subscription-plan {
        font-size: 22px;
        font-weight: 700;
        color: #2c3e50;
        margin: 0;
    }

    .subscription-status {
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-active {
        background: #d4edda;
        color: #155724;
    }

    .status-expired {
        background: #f8d7da;
        color: #721c24;
    }

    .status-cancelled {
        background: #fff3cd;
        color: #856404;
    }

    .status-pending {
        background: #cce5ff;
        color: #004085;
    }

    .subscription-info {
        margin: 16px 0;
    }

    .info-row {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 0;
        font-size: 14px;
        color: #6c757d;
        border-bottom: 1px solid #f0f0f0;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-row i {
        width: 24px;
        text-align: center;
        color: #01807B;
        font-size: 16px;
    }

    .info-label {
        font-weight: 600;
        color: #495057;
        min-width: 100px;
    }

    .info-value {
        flex: 1;
        color: #2c3e50;
    }

    .subscription-price {
        font-size: 32px;
        font-weight: 700;
        color: #01807B;
        margin: 16px 0;
        text-align: center;
    }

    .subscription-price small {
        font-size: 14px;
        font-weight: 400;
        color: #6c757d;
    }

    .recurring-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: linear-gradient(135deg, #01807B, #019B95);
        color: white;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        margin-top: 12px;
    }

    .recurring-badge i {
        animation: rotate 2s linear infinite;
    }

    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .subscription-actions {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }

    .btn {
        flex: 1;
        padding: 12px 20px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        text-align: center;
        text-decoration: none;
        transition: all 0.3s;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-primary {
        background: linear-gradient(135deg, #01807B, #019B95);
        color: white;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #026660, #01807B);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(1, 128, 123, 0.3);
        color: white;
        text-decoration: none;
    }

    .btn-outline {
        background: white;
        color: #01807B;
        border: 2px solid #01807B;
    }

    .btn-outline:hover {
        background: #01807B;
        color: white;
        text-decoration: none;
    }

    .empty-state {
        text-align: center;
        padding: 80px 20px;
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    }

    .empty-state i {
        font-size: 80px;
        color: #e0e0e0;
        margin-bottom: 24px;
    }

    .empty-state h3 {
        font-size: 24px;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 12px;
    }

    .empty-state p {
        font-size: 16px;
        color: #6c757d;
        margin-bottom: 24px;
    }

    .progress-bar {
        background: #e9ecef;
        border-radius: 10px;
        height: 8px;
        overflow: hidden;
        margin-top: 12px;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #01807B, #019B95);
        border-radius: 10px;
        transition: width 0.3s;
    }

    .progress-text {
        font-size: 12px;
        color: #6c757d;
        margin-top: 6px;
        text-align: center;
    }

    @media (max-width: 768px) {
        .subscriptions-grid {
            grid-template-columns: 1fr;
        }

        .subscription-actions {
            flex-direction: column;
        }

        .page-title {
            font-size: 24px;
        }
    }
</style>

<div class="subscription-container">
    <h1 class="page-title">
        <i class="fa fa-refresh"></i>
        Mes Abonnements
    </h1>

    <?php if (!empty($subscriptions)): ?>
        <div class="subscriptions-grid">
            <?php foreach ($subscriptions as $subscription): ?>
                <div class="subscription-card">
                    <div class="subscription-header">
                        <h3 class="subscription-plan">
                            <?php echo htmlspecialchars($subscription->plan_name_fr ?: $subscription->plan_name); ?>
                        </h3>
                        <span class="subscription-status status-<?php echo strtolower($subscription->status); ?>">
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

                    <div class="subscription-price">
                        <?php echo number_format($subscription->amount, 0, ',', ' '); ?> FCFA
                        <br><small>par mois</small>
                    </div>

                    <div class="subscription-info">
                        <div class="info-row">
                            <i class="fa fa-calendar"></i>
                            <span class="info-label">Début :</span>
                            <span class="info-value"><?php echo date('d/m/Y', strtotime($subscription->start_date)); ?></span>
                        </div>

                        <?php if ($subscription->end_date): ?>
                            <div class="info-row">
                                <i class="fa fa-calendar-times-o"></i>
                                <span class="info-label">Fin :</span>
                                <span class="info-value"><?php echo date('d/m/Y', strtotime($subscription->end_date)); ?></span>
                            </div>
                        <?php endif; ?>

                        <div class="info-row">
                            <i class="fa fa-user-md"></i>
                            <span class="info-label">Diététicien :</span>
                            <span class="info-value"><?php echo htmlspecialchars($subscription->dietitian_name); ?></span>
                        </div>

                        <?php if ($subscription->duration_days): ?>
                            <div class="info-row">
                                <i class="fa fa-clock-o"></i>
                                <span class="info-label">Durée :</span>
                                <span class="info-value"><?php echo $subscription->duration_days; ?> jours</span>
                            </div>

                            <?php
                            // Calculate progress
                            $start = strtotime($subscription->start_date);
                            $end = $subscription->end_date ? strtotime($subscription->end_date) : time();
                            $total_duration = $subscription->duration_days * 86400;
                            $elapsed = time() - $start;
                            $progress = min(100, max(0, ($elapsed / $total_duration) * 100));
                            $days_remaining = max(0, ceil(($total_duration - $elapsed) / 86400));
                            ?>

                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?php echo $progress; ?>%"></div>
                            </div>
                            <div class="progress-text">
                                <?php if ($subscription->status == 'active' && $days_remaining > 0): ?>
                                    <?php echo $days_remaining; ?> jour<?php echo $days_remaining > 1 ? 's' : ''; ?> restant<?php echo $days_remaining > 1 ? 's' : ''; ?>
                                <?php else: ?>
                                    Terminé
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($subscription->is_recurring)): ?>
                        <div class="recurring-badge">
                            <i class="fa fa-refresh"></i>
                            Paiement Récurrent
                        </div>
                    <?php endif; ?>

                    <div class="subscription-actions">
                        <a href="<?php echo site_url('dietetic/portal/subscription/' . $subscription->id); ?>" class="btn btn-primary">
                            <i class="fa fa-eye"></i>
                            Voir Détails
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="fa fa-inbox"></i>
            <h3>Aucun Abonnement</h3>
            <p>Vous n'avez pas encore d'abonnement actif.</p>
        </div>
    <?php endif; ?>
</div>

<?php $this->load->view('portal/includes/portal_footer'); ?>
