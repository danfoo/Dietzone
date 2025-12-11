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
        <!-- Back button -->
        <div class="mb-3">
            <a href="<?php echo site_url('dietetic/portal/invoices'); ?>" class="btn btn-default">
                <i class="fa fa-arrow-left"></i> Retour aux factures
            </a>
        </div>

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
            <div class="table-responsive">
                <table class="invoice-items-table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th class="text-center">Quantité</th>
                            <th class="text-right">Prix unitaire</th>
                            <th class="text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($invoice->items) && !empty($invoice->items)): ?>
                            <?php foreach ($invoice->items as $item): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($item->description); ?></strong>
                                        <?php if (isset($item->long_description) && !empty($item->long_description)): ?>
                                            <br><small class="text-muted"><?php echo nl2br(htmlspecialchars($item->long_description)); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center"><?php echo (float)$item->qty; ?></td>
                                    <td class="text-right"><?php echo format_money_safe($item->rate, $currency); ?></td>
                                    <td class="text-right"><strong><?php echo format_money_safe($item->rate * $item->qty, $currency); ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted">Aucun article</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Invoice Totals -->
        <div class="invoice-totals-section">
            <div class="totals-table">
                <div class="total-row">
                    <span class="total-label">Sous-total:</span>
                    <span class="total-value"><?php echo format_money_safe($invoice->subtotal, $currency); ?></span>
                </div>

                <?php if (isset($invoice->discount_total) && $invoice->discount_total > 0): ?>
                    <div class="total-row">
                        <span class="total-label">Réduction:</span>
                        <span class="total-value text-success">-<?php echo format_money_safe($invoice->discount_total, $currency); ?></span>
                    </div>
                <?php endif; ?>

                <?php if (isset($invoice->adjustment) && $invoice->adjustment != 0): ?>
                    <div class="total-row">
                        <span class="total-label">Ajustement:</span>
                        <span class="total-value"><?php echo format_money_safe($invoice->adjustment, $currency); ?></span>
                    </div>
                <?php endif; ?>

                <div class="total-row total-row-main">
                    <span class="total-label"><strong>TOTAL:</strong></span>
                    <span class="total-value total-amount"><?php echo format_money_safe($invoice->total, $currency); ?></span>
                </div>

                <?php if ($invoice->total_paid > 0): ?>
                    <div class="total-row">
                        <span class="total-label">Montant payé:</span>
                        <span class="total-value text-success"><?php echo format_money_safe($invoice->total_paid, $currency); ?></span>
                    </div>
                <?php endif; ?>

                <?php if ($invoice->balance > 0): ?>
                    <div class="total-row total-row-balance">
                        <span class="total-label"><strong>SOLDE DÛ:</strong></span>
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

.invoice-items-table,
.payments-table {
    width: 100%;
    border-collapse: collapse;
}

.invoice-items-table thead,
.payments-table thead {
    background: #f8f9fa;
}

.invoice-items-table th,
.invoice-items-table td,
.payments-table th,
.payments-table td {
    padding: 12px;
    border-bottom: 1px solid #e9ecef;
}

.invoice-items-table th,
.payments-table th {
    font-weight: 600;
    color: #333;
}

.totals-table {
    max-width: 400px;
    margin-left: auto;
}

.total-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #f0f0f0;
}

.total-row-main {
    border-top: 2px solid #01807B;
    border-bottom: 2px solid #01807B;
    padding: 15px 0;
    font-size: 18px;
}

.total-row-balance {
    background: #fff3cd;
    padding: 15px;
    margin-top: 10px;
    border-radius: 8px;
    border: none;
    font-size: 18px;
}

.total-amount {
    color: #01807B;
    font-size: 24px;
}

.balance-amount {
    color: #856404;
    font-size: 20px;
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

    .totals-table {
        max-width: 100%;
    }

    .invoice-actions {
        flex-direction: column;
    }

    .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<?php $this->load->view('portal/includes/portal_footer'); ?>
