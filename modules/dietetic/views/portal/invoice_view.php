<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php $this->load->view('portal/includes/portal_header'); ?>

<?php
// Helper function for formatting money
if (!function_exists('format_money_safe')) {
    function format_money_safe($amount, $currency = 'XOF') {
        if (function_exists('app_format_money')) {
            return app_format_money($amount, $currency);
        }
        return number_format($amount, 0, ',', ' ') . ' ' . $currency;
    }
}

// Helper function for formatting dates
if (!function_exists('format_date_safe')) {
    function format_date_safe($date) {
        if (function_exists('_d')) {
            return _d($date);
        }
        return date('d/m/Y', strtotime($date));
    }
}

// Helper function for invoice number
if (!function_exists('format_invoice_num_safe')) {
    function format_invoice_num_safe($id) {
        if (function_exists('format_invoice_number')) {
            return format_invoice_number($id);
        }
        return str_pad($id, 6, '0', STR_PAD_LEFT);
    }
}

// Status mapping
$status_class = '';
$status_text = '';
$status_icon = '';

switch ((int)$invoice->status) {
    case 1:
        $status_class = 'status-unpaid';
        $status_text = 'Impayée';
        $status_icon = 'fa-exclamation-circle';
        break;
    case 2:
        $status_class = 'status-paid';
        $status_text = 'Payée';
        $status_icon = 'fa-check-circle';
        break;
    case 3:
        $status_class = 'status-partial';
        $status_text = 'Partiellement payée';
        $status_icon = 'fa-adjust';
        break;
    case 4:
        $status_class = 'status-overdue';
        $status_text = 'En retard';
        $status_icon = 'fa-clock-o';
        break;
    case 5:
        $status_class = 'status-cancelled';
        $status_text = 'Annulée';
        $status_icon = 'fa-ban';
        break;
    default:
        $status_class = 'status-unpaid';
        $status_text = 'Non défini';
        $status_icon = 'fa-question-circle';
}

$currency = isset($invoice->currency_name) ? $invoice->currency_name : 'XOF';
?>

<div class="portal-content">
    <div class="container-fluid">
        <!-- Invoice Header -->
        <div class="invoice-header-card">
            <div class="invoice-header-top">
                <div class="invoice-number-section">
                    <h1 class="invoice-number">
                        <i class="fa fa-file-text"></i>
                        Facture #<?php echo format_invoice_num_safe($invoice->id); ?>
                    </h1>
                    <span class="invoice-status <?php echo $status_class; ?>">
                        <i class="fa <?php echo $status_icon; ?>"></i>
                        <?php echo $status_text; ?>
                    </span>
                </div>

                <div class="invoice-dates">
                    <div class="date-item">
                        <span class="date-label">Date:</span>
                        <span class="date-value"><?php echo format_date_safe($invoice->date); ?></span>
                    </div>
                    <div class="date-item">
                        <span class="date-label">Échéance:</span>
                        <span class="date-value"><?php echo format_date_safe($invoice->duedate); ?></span>
                    </div>
                </div>
            </div>

            <!-- Invoice parties -->
            <div class="invoice-parties">
                <div class="party-section">
                    <h3>Facturer à:</h3>
                    <div class="party-info">
                        <?php if (isset($client) && $client): ?>
                            <p><strong><?php
                                if (!empty($client->company)) {
                                    echo htmlspecialchars($client->company);
                                } else {
                                    echo htmlspecialchars($patient->firstname . ' ' . $patient->lastname);
                                }
                            ?></strong></p>
                            <?php if (!empty($client->address)): ?>
                                <p><?php echo nl2br(htmlspecialchars($client->address)); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($client->city)): ?>
                                <p><?php echo htmlspecialchars($client->city); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($client->phonenumber)): ?>
                                <p><i class="fa fa-phone"></i> <?php echo htmlspecialchars($client->phonenumber); ?></p>
                            <?php endif; ?>
                        <?php else: ?>
                            <p><strong><?php echo htmlspecialchars($patient->firstname . ' ' . $patient->lastname); ?></strong></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="party-section">
                    <h3>Fournisseur:</h3>
                    <div class="party-info">
                        <p><strong>DietZone</strong></p>
                        <p>Dakar, Sénégal</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoice Items -->
        <div class="invoice-items-section">
            <h2>Articles</h2>

            <?php if (isset($invoice->items) && !empty($invoice->items)): ?>
                <!-- Mobile-friendly card layout for items -->
                <div class="items-cards-container">
                    <?php foreach ($invoice->items as $index => $item): ?>
                        <div class="item-card">
                            <div class="item-card-header">
                                <span class="item-number">#<?php echo $index + 1; ?></span>
                                <span class="item-total-badge">
                                    <?php echo format_money_safe($item->rate * $item->qty, $currency); ?>
                                </span>
                            </div>

                            <div class="item-card-body">
                                <div class="item-description">
                                    <strong><?php echo htmlspecialchars($item->description); ?></strong>
                                    <?php if (isset($item->long_description) && !empty($item->long_description)): ?>
                                        <p class="item-long-desc"><?php echo nl2br(htmlspecialchars($item->long_description)); ?></p>
                                    <?php endif; ?>
                                </div>

                                <div class="item-details-grid">
                                    <div class="item-detail">
                                        <span class="detail-label">Quantité</span>
                                        <span class="detail-value"><?php echo (float)$item->qty; ?></span>
                                    </div>
                                    <div class="item-detail">
                                        <span class="detail-label">Prix unitaire</span>
                                        <span class="detail-value"><?php echo format_money_safe($item->rate, $currency); ?></span>
                                    </div>
                                    <div class="item-detail item-detail-total">
                                        <span class="detail-label">Total</span>
                                        <span class="detail-value total-value"><?php echo format_money_safe($item->rate * $item->qty, $currency); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state-small">
                    <p class="text-muted">Aucun article</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Invoice Totals -->
        <div class="invoice-totals-section">
            <h2 class="totals-title">Récapitulatif</h2>
            <div class="totals-card">
                <div class="total-row total-row-subtle">
                    <span class="total-label">
                        <i class="fa fa-calculator"></i> Sous-total
                    </span>
                    <span class="total-value"><?php echo format_money_safe($invoice->subtotal, $currency); ?></span>
                </div>

                <?php if (isset($invoice->discount_total) && $invoice->discount_total > 0): ?>
                    <div class="total-row total-row-subtle total-row-success">
                        <span class="total-label">
                            <i class="fa fa-tag"></i> Réduction
                        </span>
                        <span class="total-value">-<?php echo format_money_safe($invoice->discount_total, $currency); ?></span>
                    </div>
                <?php endif; ?>

                <?php if (isset($invoice->adjustment) && $invoice->adjustment != 0): ?>
                    <div class="total-row total-row-subtle">
                        <span class="total-label">
                            <i class="fa fa-adjust"></i> Ajustement
                        </span>
                        <span class="total-value"><?php echo format_money_safe($invoice->adjustment, $currency); ?></span>
                    </div>
                <?php endif; ?>

                <div class="total-row total-row-main">
                    <span class="total-label">
                        <i class="fa fa-money"></i> Total
                    </span>
                    <span class="total-value total-amount"><?php echo format_money_safe($invoice->total, $currency); ?></span>
                </div>

                <?php if ($invoice->total_paid > 0): ?>
                    <div class="total-row total-row-subtle total-row-success">
                        <span class="total-label">
                            <i class="fa fa-check-circle"></i> Montant payé
                        </span>
                        <span class="total-value"><?php echo format_money_safe($invoice->total_paid, $currency); ?></span>
                    </div>
                <?php endif; ?>

                <?php if ($invoice->balance > 0): ?>
                    <div class="total-row total-row-balance">
                        <span class="total-label">
                            <i class="fa fa-exclamation-circle"></i> Solde dû
                        </span>
                        <span class="total-value balance-amount"><?php echo format_money_safe($invoice->balance, $currency); ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Payments History -->
        <?php if (isset($invoice->payments) && !empty($invoice->payments)): ?>
            <div class="payments-section">
                <h2>Historique des paiements</h2>
                <div class="table-responsive">
                    <table class="payments-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Mode de paiement</th>
                                <th>Note</th>
                                <th class="text-right">Montant</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($invoice->payments as $payment): ?>
                                <tr>
                                    <td><?php echo format_date_safe($payment->date); ?></td>
                                    <td>
                                        <?php
                                        if (isset($payment->paymentmode)) {
                                            $this->db->where('id', $payment->paymentmode);
                                            $mode = $this->db->get(db_prefix() . 'payment_modes')->row();
                                            echo $mode ? htmlspecialchars($mode->name) : 'N/A';
                                        } else {
                                            echo 'N/A';
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo isset($payment->note) && !empty($payment->note) ? htmlspecialchars($payment->note) : '-'; ?></td>
                                    <td class="text-right text-success"><strong><?php echo format_money_safe($payment->amount, $currency); ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <!-- Invoice Notes -->
        <?php if ((isset($invoice->clientnote) && !empty($invoice->clientnote)) || (isset($invoice->terms) && !empty($invoice->terms))): ?>
            <div class="invoice-notes-section">
                <?php if (isset($invoice->clientnote) && !empty($invoice->clientnote)): ?>
                    <div class="note-box">
                        <h3><i class="fa fa-info-circle"></i> Note au client</h3>
                        <div class="note-content"><?php echo nl2br(htmlspecialchars($invoice->clientnote)); ?></div>
                    </div>
                <?php endif; ?>

                <?php if (isset($invoice->terms) && !empty($invoice->terms)): ?>
                    <div class="note-box">
                        <h3><i class="fa fa-gavel"></i> Conditions générales</h3>
                        <div class="note-content"><?php echo nl2br(htmlspecialchars($invoice->terms)); ?></div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Action Buttons -->
        <div class="invoice-actions">
            <a href="<?php echo site_url('dietetic/portal/invoices'); ?>" class="btn btn-default">
                <i class="fa fa-arrow-left"></i> Retour aux factures
            </a>

            <?php if ($invoice->status != 2 && $invoice->balance > 0): ?>
                <button class="btn btn-success" onclick="alert('La fonctionnalité de paiement en ligne sera bientôt disponible!')">
                    <i class="fa fa-credit-card"></i> Payer maintenant
                </button>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.invoice-header-card {
    background: white;
    border-radius: 12px;
    padding: 30px;
    margin-bottom: 25px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.invoice-header-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #f0f0f0;
}

.invoice-number-section {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.invoice-number {
    font-size: 28px;
    color: #01807B;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.invoice-status {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 14px;
    width: fit-content;
}

.status-paid {
    background: #d4edda;
    color: #155724;
}

.status-unpaid {
    background: #fff3cd;
    color: #856404;
}

.status-partial {
    background: #d1ecf1;
    color: #0c5460;
}

.status-overdue {
    background: #f8d7da;
    color: #721c24;
}

.status-cancelled {
    background: #e2e3e5;
    color: #383d41;
}

.invoice-dates {
    display: flex;
    flex-direction: column;
    gap: 8px;
    text-align: right;
}

.date-item {
    display: flex;
    gap: 10px;
    align-items: center;
    justify-content: flex-end;
}

.date-label {
    color: #666;
    font-weight: 500;
}

.date-value {
    font-weight: 600;
    color: #333;
}

.invoice-parties {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
}

.party-section h3 {
    color: #01807B;
    font-size: 16px;
    margin-bottom: 10px;
    font-weight: 600;
}

.party-info {
    color: #555;
    line-height: 1.6;
}

.party-info p {
    margin: 5px 0;
}

.invoice-items-section,
.invoice-totals-section,
.payments-section,
.invoice-notes-section {
    background: white;
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.invoice-items-section h2,
.payments-section h2 {
    color: #01807B;
    font-size: 20px;
    margin-bottom: 20px;
    font-weight: 600;
}

/* Mobile-friendly items cards */
.items-cards-container {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.item-card {
    background: #f8f9fa;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid #e9ecef;
    transition: all 0.3s;
}

.item-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border-color: #01807B;
}

.item-card-header {
    background: linear-gradient(135deg, #01807B 0%, #019d96 100%);
    padding: 12px 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.item-number {
    color: white;
    font-weight: 600;
    font-size: 14px;
}

.item-total-badge {
    background: white;
    color: #01807B;
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 700;
    font-size: 16px;
}

.item-card-body {
    padding: 15px;
}

.item-description {
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 1px solid #e9ecef;
}

.item-description strong {
    color: #333;
    font-size: 16px;
    display: block;
    margin-bottom: 5px;
}

.item-long-desc {
    color: #666;
    font-size: 14px;
    margin: 8px 0 0 0;
    line-height: 1.5;
}

.item-details-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
}

.item-detail {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.detail-label {
    color: #666;
    font-size: 12px;
    text-transform: uppercase;
    font-weight: 500;
}

.detail-value {
    color: #333;
    font-size: 15px;
    font-weight: 600;
}

.item-detail-total .detail-value {
    color: #01807B;
    font-size: 18px;
}

.empty-state-small {
    text-align: center;
    padding: 30px;
    color: #999;
}

.payments-table {
    width: 100%;
    border-collapse: collapse;
}

.payments-table thead {
    background: #f8f9fa;
}

.payments-table th,
.payments-table td {
    padding: 12px;
    border-bottom: 1px solid #e9ecef;
}

.payments-table th {
    font-weight: 600;
    color: #333;
}

/* Totals section */
.totals-title {
    color: #01807B;
    font-size: 20px;
    margin-bottom: 15px;
    font-weight: 600;
}

.totals-card {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 20px;
    max-width: 450px;
    margin-left: auto;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}

.total-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #e9ecef;
    transition: all 0.2s;
}

.total-row:hover {
    background: rgba(1, 128, 123, 0.03);
    margin: 0 -10px;
    padding-left: 10px;
    padding-right: 10px;
    border-radius: 6px;
}

.total-row:last-child {
    border-bottom: none;
}

.total-row-subtle .total-label {
    color: #6c757d;
    font-size: 14px;
    font-weight: 500;
}

.total-row-subtle .total-value {
    color: #495057;
    font-size: 15px;
    font-weight: 600;
}

.total-row-success .total-value {
    color: #28a745;
}

.total-row-main {
    background: linear-gradient(135deg, #01807B 0%, #019d96 100%);
    margin: 15px -20px;
    padding: 15px 20px !important;
    border-radius: 8px;
    border: none !important;
}

.total-row-main .total-label {
    color: white;
    font-size: 16px;
    font-weight: 600;
}

.total-row-main .total-value {
    color: white;
}

.total-row-balance {
    background: linear-gradient(135deg, #fff9e6 0%, #fff3cd 100%);
    padding: 15px;
    margin: 15px -20px -20px -20px;
    border-radius: 0 0 12px 12px;
    border: none !important;
    border-top: 2px dashed #ffc107 !important;
}

.total-row-balance .total-label {
    color: #856404;
    font-size: 15px;
    font-weight: 600;
}

.total-row-balance .total-value {
    color: #856404;
}

.total-label {
    display: flex;
    align-items: center;
    gap: 8px;
}

.total-label i {
    font-size: 14px;
    opacity: 0.7;
}

.total-amount {
    color: white;
    font-size: 18px;
    font-weight: 700;
}

.balance-amount {
    color: #856404;
    font-size: 17px;
    font-weight: 700;
}

.note-box {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 15px;
    border-left: 4px solid #01807B;
}

.note-box h3 {
    color: #01807B;
    font-size: 16px;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.note-content {
    color: #555;
    line-height: 1.6;
}

.invoice-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-top: 30px;
    padding: 20px 0;
}

.btn {
    padding: 12px 30px;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s;
    border: none;
    cursor: pointer;
}

.btn-default {
    background: #6c757d;
    color: white;
}

.btn-default:hover {
    background: #5a6268;
    color: white;
    text-decoration: none;
}

.btn-success {
    background: #28a745;
    color: white;
}

.btn-success:hover {
    background: #218838;
}

@media (max-width: 768px) {
    .invoice-header-top {
        flex-direction: column;
        gap: 20px;
    }

    .invoice-dates {
        text-align: left;
    }

    .date-item {
        justify-content: flex-start;
    }

    .invoice-parties {
        grid-template-columns: 1fr;
    }

    .invoice-number {
        font-size: 22px;
    }

    .totals-card {
        max-width: 100%;
    }

    .total-row {
        padding: 10px 0;
    }

    .total-row-subtle .total-label {
        font-size: 13px;
    }

    .total-row-subtle .total-value {
        font-size: 14px;
    }

    .invoice-actions {
        flex-direction: column;
    }

    .btn {
        width: 100%;
        justify-content: center;
    }

    /* Mobile adjustments for item cards */
    .item-details-grid {
        grid-template-columns: 1fr;
        gap: 10px;
    }

    .item-detail {
        flex-direction: row;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid #e9ecef;
    }

    .item-detail:last-child {
        border-bottom: none;
    }

    .item-detail-total {
        background: #f8f9fa;
        margin: 10px -15px -15px -15px;
        padding: 12px 15px !important;
        border-bottom: none !important;
    }

    .detail-label {
        font-size: 13px;
    }

    .detail-value {
        font-size: 14px;
    }

    .item-total-badge {
        font-size: 14px;
        padding: 4px 10px;
    }
}
</style>

<?php $this->load->view('portal/includes/portal_footer'); ?>
