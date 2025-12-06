<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php $this->load->view('portal/includes/portal_header'); ?>

<div class="portal-content">
    <div class="container-fluid">
        <!-- Dashboard Header -->
        <div class="dashboard-header">
            <div class="header-icon">
                <i class="fa fa-file-text"></i>
            </div>
            <div class="header-text">
                <h1>Mes Factures</h1>
                <p><?php echo count($invoices); ?> facture<?php echo count($invoices) > 1 ? 's' : ''; ?></p>
            </div>
        </div>

        <?php if (empty($invoices)): ?>
            <!-- Empty State -->
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fa fa-file-text-o"></i>
                </div>
                <h3>Aucune facture</h3>
                <p>Vous n'avez pas encore de factures.</p>
                <a href="<?php echo site_url('dietetic/portal'); ?>" class="btn-back">
                    <i class="fa fa-arrow-left"></i> Retour au tableau de bord
                </a>
            </div>
        <?php else: ?>
            <!-- Invoices Cards -->
            <div class="invoices-grid">
                <?php foreach ($invoices as $invoice): ?>
                    <?php
                    // Determine status
                    $status_class = '';
                    $status_text = '';
                    $status_icon = '';

                    if ($invoice->status == 1) {
                        $status_class = 'status-unpaid';
                        $status_text = 'Impayée';
                        $status_icon = 'fa-exclamation-circle';
                    } elseif ($invoice->status == 2) {
                        $status_class = 'status-paid';
                        $status_text = 'Payée';
                        $status_icon = 'fa-check-circle';
                    } elseif ($invoice->status == 3) {
                        $status_class = 'status-partial';
                        $status_text = 'Partiellement payée';
                        $status_icon = 'fa-adjust';
                    } elseif ($invoice->status == 4) {
                        $status_class = 'status-overdue';
                        $status_text = 'En retard';
                        $status_icon = 'fa-clock-o';
                    } elseif ($invoice->status == 5) {
                        $status_class = 'status-cancelled';
                        $status_text = 'Annulée';
                        $status_icon = 'fa-ban';
                    } elseif ($invoice->status == 6) {
                        $status_class = 'status-draft';
                        $status_text = 'Brouillon';
                        $status_icon = 'fa-file-o';
                    }
                    ?>

                    <a href="<?php echo site_url('dietetic/portal/invoice/' . $invoice->id); ?>" class="invoice-card">
                        <div class="invoice-card-header">
                            <div class="invoice-number">
                                <i class="fa fa-hashtag"></i>
                                <?php
                                // Format invoice number
                                if (function_exists('format_invoice_number')) {
                                    echo format_invoice_number($invoice->id);
                                } else {
                                    echo str_pad($invoice->number, 6, '0', STR_PAD_LEFT);
                                }
                                ?>
                            </div>
                            <span class="invoice-status <?php echo $status_class; ?>">
                                <i class="fa <?php echo $status_icon; ?>"></i>
                                <?php echo $status_text; ?>
                            </span>
                        </div>

                        <div class="invoice-card-body">
                            <div class="invoice-date">
                                <div class="date-label">
                                    <i class="fa fa-calendar"></i> Date
                                </div>
                                <div class="date-value">
                                    <?php
                                    // Format date
                                    if (function_exists('_d')) {
                                        echo _d($invoice->date);
                                    } else {
                                        echo date('d/m/Y', strtotime($invoice->date));
                                    }
                                    ?>
                                </div>
                            </div>

                            <div class="invoice-date">
                                <div class="date-label">
                                    <i class="fa fa-calendar-check-o"></i> Échéance
                                </div>
                                <div class="date-value">
                                    <?php
                                    // Format due date
                                    if (function_exists('_d')) {
                                        echo _d($invoice->duedate);
                                    } else {
                                        echo date('d/m/Y', strtotime($invoice->duedate));
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>

                        <div class="invoice-card-footer">
                            <span class="view-link">
                                Voir les détails <i class="fa fa-arrow-right"></i>
                            </span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Back Button -->
            <div class="back-section">
                <a href="<?php echo site_url('dietetic/portal'); ?>" class="btn-back">
                    <i class="fa fa-arrow-left"></i> Retour au tableau de bord
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
/* Mobile-First Design */
.portal-content {
    padding: 20px 15px;
    min-height: calc(100vh - 200px);
    background: #f8f9fa;
}

/* Dashboard Header */
.dashboard-header {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 25px;
    padding: 20px;
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.header-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    background: linear-gradient(135deg, #01807B 0%, #01655f 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    color: white;
    flex-shrink: 0;
}

.header-text h1 {
    font-size: 24px;
    font-weight: 700;
    color: #2c3e50;
    margin: 0 0 5px 0;
}

.header-text p {
    font-size: 14px;
    color: #7f8c8d;
    margin: 0;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.empty-icon {
    font-size: 64px;
    color: #e0e0e0;
    margin-bottom: 20px;
}

.empty-state h3 {
    color: #666;
    font-size: 20px;
    margin-bottom: 10px;
}

.empty-state p {
    color: #999;
    font-size: 14px;
    margin-bottom: 25px;
}

/* Invoices Grid - Mobile First */
.invoices-grid {
    display: flex;
    flex-direction: column;
    gap: 15px;
    margin-bottom: 25px;
}

/* Invoice Card */
.invoice-card {
    display: block;
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    text-decoration: none;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.invoice-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(1, 128, 123, 0.15);
    border-color: #01807B;
    text-decoration: none;
}

.invoice-card-header {
    padding: 20px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
}

.invoice-number {
    font-size: 18px;
    font-weight: 700;
    color: #01807B;
}

.invoice-number i {
    font-size: 14px;
    opacity: 0.7;
}

/* Status Badges */
.invoice-status {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    white-space: nowrap;
}

.status-paid {
    background: #d4edda;
    color: #155724;
}

.status-unpaid {
    background: #f8d7da;
    color: #721c24;
}

.status-partial {
    background: #fff3cd;
    color: #856404;
}

.status-overdue {
    background: #f8d7da;
    color: #721c24;
}

.status-cancelled {
    background: #e2e3e5;
    color: #383d41;
}

.status-draft {
    background: #d1ecf1;
    color: #0c5460;
}

/* Card Body */
.invoice-card-body {
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.invoice-date {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.date-label {
    font-size: 14px;
    color: #7f8c8d;
    display: flex;
    align-items: center;
    gap: 5px;
}

.date-label i {
    color: #01807B;
}

.date-value {
    font-size: 15px;
    font-weight: 600;
    color: #2c3e50;
}

/* Card Footer */
.invoice-card-footer {
    padding: 15px 20px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    text-align: right;
}

.view-link {
    font-size: 14px;
    font-weight: 600;
    color: #01807B;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.view-link i {
    transition: transform 0.3s ease;
}

.invoice-card:hover .view-link i {
    transform: translateX(4px);
}

/* Back Section */
.back-section {
    text-align: center;
    padding: 20px 0;
}

.btn-back {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: white;
    color: #2c3e50;
    border: 2px solid #e9ecef;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
}

.btn-back:hover {
    background: #01807B;
    color: white;
    border-color: #01807B;
    text-decoration: none;
}

/* Tablet & Desktop - 2 columns grid */
@media (min-width: 768px) {
    .portal-content {
        padding: 30px 20px;
    }

    .dashboard-header {
        padding: 25px;
        margin-bottom: 30px;
    }

    .header-icon {
        width: 70px;
        height: 70px;
        font-size: 32px;
    }

    .header-text h1 {
        font-size: 28px;
    }

    .invoices-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
}

/* Large Desktop - 3 columns grid */
@media (min-width: 1200px) {
    .portal-content {
        padding: 40px 30px;
    }

    .invoices-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 25px;
    }
}
</style>

<?php $this->load->view('portal/includes/portal_footer'); ?>
