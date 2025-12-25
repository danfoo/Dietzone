<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php $this->load->view('portal/includes/portal_header'); ?>

<?php
// Calculate statistics
$stats = [
    'total' => count($invoices),
    'paid' => 0,
    'unpaid' => 0,
    'overdue' => 0,
    'partial' => 0
];

foreach ($invoices as $invoice) {
    if ($invoice->status == 2) {
        $stats['paid']++;
    } elseif ($invoice->status == 1) {
        $stats['unpaid']++;
    } elseif ($invoice->status == 4) {
        $stats['overdue']++;
    } elseif ($invoice->status == 3) {
        $stats['partial']++;
    }
}
?>

<div class="portal-content">
    <div class="container-fluid">
        <!-- Dashboard Header -->
        <div class="dashboard-header">
            <div class="header-main">
                <div class="header-icon">
                    <i class="fa fa-file-text"></i>
                </div>
                <div class="header-text">
                    <h1>Mes Factures</h1>
                    <p><?php echo $stats['total']; ?> facture<?php echo $stats['total'] > 1 ? 's' : ''; ?> au total</p>
                </div>
            </div>

            <?php if (!empty($invoices)): ?>
                <!-- Statistics Cards -->
                <div class="stats-grid">
                    <div class="stat-card stat-paid">
                        <div class="stat-icon">
                            <i class="fa fa-check-circle"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-value"><?php echo $stats['paid']; ?></div>
                            <div class="stat-label">Payée<?php echo $stats['paid'] > 1 ? 's' : ''; ?></div>
                        </div>
                    </div>

                    <div class="stat-card stat-unpaid">
                        <div class="stat-icon">
                            <i class="fa fa-exclamation-circle"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-value"><?php echo $stats['unpaid']; ?></div>
                            <div class="stat-label">Impayée<?php echo $stats['unpaid'] > 1 ? 's' : ''; ?></div>
                        </div>
                    </div>

                    <?php if ($stats['overdue'] > 0): ?>
                        <div class="stat-card stat-overdue">
                            <div class="stat-icon">
                                <i class="fa fa-clock-o"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-value"><?php echo $stats['overdue']; ?></div>
                                <div class="stat-label">En retard</div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <?php if (empty($invoices)): ?>
            <!-- Empty State -->
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fa fa-file-text-o"></i>
                </div>
                <h3>Aucune facture</h3>
                <p>Vous n'avez pas encore de factures.</p>
            </div>
        <?php else: ?>
            <!-- Invoices Accordion -->
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

                    // Calculate amounts
                    $total = $invoice->total;
                    $total_paid = 0;

                    // Get total paid amount
                    if (isset($invoice->payments) && is_array($invoice->payments)) {
                        foreach ($invoice->payments as $payment) {
                            $total_paid += $payment->amount;
                        }
                    }

                    $balance = $total - $total_paid;
                    ?>

                    <div class="invoice-card accordion-item" data-invoice-id="<?php echo $invoice->id; ?>">
                        <div class="invoice-card-header accordion-trigger">
                            <div class="header-left">
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
                            <div class="chevron-icon">
                                <i class="fa fa-chevron-down"></i>
                            </div>
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

                        <!-- Collapsible Content -->
                        <div class="invoice-details-collapse">
                            <div class="invoice-details">
                                <div class="details-section">
                                    <h4>Informations financières</h4>
                                    <div class="financial-grid">
                                        <div class="financial-item">
                                            <div class="financial-label">
                                                <i class="fa fa-money"></i> Montant total
                                            </div>
                                            <div class="financial-value">
                                                <?php
                                                // Format money
                                                if (function_exists('app_format_money')) {
                                                    echo app_format_money($total, $invoice->currency_name);
                                                } else {
                                                    $currency = isset($invoice->currency_name) ? $invoice->currency_name : 'XOF';
                                                    echo number_format($total, 0, ',', ' ') . ' ' . $currency;
                                                }
                                                ?>
                                            </div>
                                        </div>

                                        <div class="financial-item">
                                            <div class="financial-label">
                                                <i class="fa fa-credit-card"></i> Montant payé
                                            </div>
                                            <div class="financial-value text-success">
                                                <?php
                                                // Format money paid
                                                if (function_exists('app_format_money')) {
                                                    echo app_format_money($total_paid, $invoice->currency_name);
                                                } else {
                                                    $currency = isset($invoice->currency_name) ? $invoice->currency_name : 'XOF';
                                                    echo number_format($total_paid, 0, ',', ' ') . ' ' . $currency;
                                                }
                                                ?>
                                            </div>
                                        </div>

                                        <?php if ($balance > 0): ?>
                                            <div class="financial-item">
                                                <div class="financial-label">
                                                    <i class="fa fa-calculator"></i> Solde restant
                                                </div>
                                                <div class="financial-value text-danger">
                                                    <?php
                                                    // Format balance
                                                    if (function_exists('app_format_money')) {
                                                        echo app_format_money($balance, $invoice->currency_name);
                                                    } else {
                                                        $currency = isset($invoice->currency_name) ? $invoice->currency_name : 'XOF';
                                                        echo number_format($balance, 0, ',', ' ') . ' ' . $currency;
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="details-actions">
                                    <a href="<?php echo site_url('dietetic/portal/invoice/' . $invoice->id); ?>"
                                       class="btn-action btn-primary">
                                        <i class="fa fa-eye"></i> Voir la facture
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
/* Mobile-First Design */
.portal-content {
    padding: none;
    min-height: calc(100vh - 200px);
    background: none;
}

.container-fluid {
    padding-right: 0px;
    padding-left: 0px;
    margin-right: auto;
    margin-left: auto;
}

/* Dashboard Header */
.dashboard-header {
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    margin-bottom: 25px;
    overflow: hidden;
}

.header-main {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 20px;
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

/* Statistics Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0;
    border-top: 1px solid #e9ecef;
}

.stat-card {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 15px;
    border-bottom: 1px solid #e9ecef;
    border-right: 1px solid #e9ecef;
    transition: background 0.3s ease;
}

.stat-card:nth-child(2n) {
    border-right: none;
}

.stat-card:last-child {
    border-bottom: none;
    grid-column: -2 / -1;
}

.stat-card:nth-last-child(2) {
    border-bottom: none;
}

.stat-card:hover {
    background: #f8f9fa;
}

.stat-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}

.stat-paid .stat-icon {
    background: #d4edda;
    color: #155724;
}

.stat-unpaid .stat-icon {
    background: #f8d7da;
    color: #721c24;
}

.stat-overdue .stat-icon {
    background: #fff3cd;
    color: #856404;
}

.stat-info {
    flex: 1;
}

.stat-value {
    font-size: 24px;
    font-weight: 700;
    line-height: 1;
    margin-bottom: 4px;
}

.stat-paid .stat-value {
    color: #155724;
}

.stat-unpaid .stat-value {
    color: #721c24;
}

.stat-overdue .stat-value {
    color: #856404;
}

.stat-label {
    font-size: 13px;
    color: #7f8c8d;
    font-weight: 500;
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
    margin: 0;
}

/* Invoices Grid - Mobile First */
.invoices-grid {
    display: flex;
    flex-direction: column;
    gap: 15px;
    margin-bottom: 25px;
}

/* Invoice Card - Accordion */
.invoice-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.invoice-card:hover {
    box-shadow: 0 4px 12px rgba(1, 128, 123, 0.15);
}

.invoice-card.active {
    border-color: #01807B;
    box-shadow: 0 8px 20px rgba(1, 128, 123, 0.2);
}

.accordion-trigger {
    cursor: pointer;
    user-select: none;
}

.invoice-card-header {
    padding: 20px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
}

.header-left {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    flex: 1;
}

.invoice-number {
    font-size: 14px;
    font-weight: 700;
    color: #01807B;
}

.invoice-number i {
    font-size: 14px;
    opacity: 0.7;
}

.chevron-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    color: #01807B;
    transition: all 0.3s ease;
    flex-shrink: 0;
}

.invoice-card.active .chevron-icon {
    background: #01807B;
    color: white;
    transform: rotate(180deg);
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

/* Collapsible Details */
.invoice-details-collapse {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.4s ease-in-out;
}

.invoice-card.active .invoice-details-collapse {
    max-height: 1000px;
}

.invoice-details {
    padding: 0 20px 20px 20px;
    border-top: 1px solid #e9ecef;
}

.details-section {
    padding: 20px 0;
}

.details-section h4 {
    font-size: 16px;
    font-weight: 700;
    color: #2c3e50;
    margin: 0 0 15px 0;
}

.financial-grid {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.financial-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px;
    background: #f8f9fa;
    border-radius: 8px;
}

.financial-label {
    font-size: 13px;
    color: #7f8c8d;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 6px;
}

.financial-label i {
    color: #01807B;
}

.financial-value {
    font-size: 16px;
    font-weight: 700;
    color: #2c3e50;
}

.financial-value.text-success {
    color: #155724;
}

.financial-value.text-danger {
    color: #721c24;
}

/* Action Buttons */
.details-actions {
    padding-top: 20px;
}

.btn-action {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 14px 20px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    border: none;
}

.btn-primary {
    background: linear-gradient(135deg, #01807B 0%, #01655f 100%);
    color: white;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #01655f 0%, #014d49 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(1, 128, 123, 0.3);
    text-decoration: none;
    color: white;
}

.btn-secondary {
    background: white;
    color: #2c3e50;
    border: 2px solid #e9ecef;
}

.btn-secondary:hover {
    background: #f8f9fa;
    border-color: #01807B;
    color: #01807B;
    text-decoration: none;
}

/* Tablet - Stats keep 2 columns */
@media (min-width: 600px) {
    /* Stats already in 2 columns by default */
}

/* Tablet & Desktop */
@media (min-width: 768px) {
    .portal-content {
        padding: 20px 15px;
    }

    .dashboard-header {
        margin-bottom: 30px;
    }

    .header-main {
        padding: 25px;
    }

    .header-icon {
        width: 70px;
        height: 70px;
        font-size: 32px;
    }

    .header-text h1 {
        font-size: 28px;
    }

    /* Keep stats in 2 columns */

    .invoices-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
}

/* Large Desktop */
@media (min-width: 1200px) {
    .portal-content {
        padding: 20px 15px;
    }

    .invoices-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 30px;
    }
}
</style>

<script>
// Accordion functionality
document.addEventListener('DOMContentLoaded', function() {
    const accordionTriggers = document.querySelectorAll('.accordion-trigger');

    accordionTriggers.forEach(trigger => {
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const card = this.closest('.invoice-card');
            const isActive = card.classList.contains('active');

            // Close all other accordions
            document.querySelectorAll('.invoice-card.active').forEach(activeCard => {
                if (activeCard !== card) {
                    activeCard.classList.remove('active');
                }
            });

            // Toggle current accordion
            if (isActive) {
                card.classList.remove('active');
            } else {
                card.classList.add('active');
            }
        });
    });
});
</script>

<?php $this->load->view('portal/includes/portal_footer'); ?>
