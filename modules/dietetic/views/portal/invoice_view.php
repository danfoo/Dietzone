<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php $this->load->view('portal/includes/portal_header'); ?>

<?php
// Status mapping
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
}

// Calculate totals
$subtotal = 0;
if (isset($invoice->items)) {
    foreach ($invoice->items as $item) {
        $subtotal += $item->rate * $item->qty;
    }
}
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
                        Facture #<?php echo format_invoice_number($invoice->id); ?>
                    </h1>
                    <span class="invoice-status <?php echo $status_class; ?>">
                        <i class="fa <?php echo $status_icon; ?>"></i>
                        <?php echo $status_text; ?>
                    </span>
                </div>

                <div class="invoice-dates">
                    <div class="date-item">
                        <span class="date-label">Date:</span>
                        <span class="date-value"><?php echo _d($invoice->date); ?></span>
                    </div>
                    <div class="date-item">
                        <span class="date-label">Échéance:</span>
                        <span class="date-value"><?php echo _d($invoice->duedate); ?></span>
                    </div>
                </div>
            </div>

            <!-- Invoice parties -->
            <div class="invoice-parties">
                <div class="party-section">
                    <h3>Facturer à:</h3>
                    <div class="party-info">
                        <?php if ($client): ?>
                            <p><strong><?php echo $client->company ?: ($patient->firstname . ' ' . $patient->lastname); ?></strong></p>
                            <?php if (!empty($client->address)): ?>
                                <p><?php echo nl2br($client->address); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($client->city)): ?>
                                <p><?php echo $client->city; ?></p>
                            <?php endif; ?>
                            <?php if (!empty($client->phonenumber)): ?>
                                <p><i class="fa fa-phone"></i> <?php echo $client->phonenumber; ?></p>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="party-section">
                    <h3>Fournisseur:</h3>
                    <div class="party-info">
                        <?php
                        // Get company name from settings
                        $this->db->where('name', 'companyname');
                        $company = $this->db->get(db_prefix() . 'options')->row();
                        ?>
                        <p><strong><?php echo $company ? $company->value : 'DietZone'; ?></strong></p>
                        <?php
                        // Get company address
                        $this->db->where('name', 'company_address');
                        $address = $this->db->get(db_prefix() . 'options')->row();
                        if ($address && !empty($address->value)):
                        ?>
                            <p><?php echo nl2br($address->value); ?></p>
                        <?php endif; ?>
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
                                        <strong><?php echo $item->description; ?></strong>
                                        <?php if (!empty($item->long_description)): ?>
                                            <br><small class="text-muted"><?php echo nl2br($item->long_description); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center"><?php echo $item->qty; ?></td>
                                    <td class="text-right"><?php echo app_format_money($item->rate, $invoice->currency_name); ?></td>
                                    <td class="text-right"><strong><?php echo app_format_money($item->rate * $item->qty, $invoice->currency_name); ?></strong></td>
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
                    <span class="total-value"><?php echo app_format_money($invoice->subtotal, $invoice->currency_name); ?></span>
                </div>

                <?php if ($invoice->discount_total > 0): ?>
                    <div class="total-row">
                        <span class="total-label">Réduction:</span>
                        <span class="total-value text-success">-<?php echo app_format_money($invoice->discount_total, $invoice->currency_name); ?></span>
                    </div>
                <?php endif; ?>

                <?php if ($invoice->adjustment != 0): ?>
                    <div class="total-row">
                        <span class="total-label">Ajustement:</span>
                        <span class="total-value"><?php echo app_format_money($invoice->adjustment, $invoice->currency_name); ?></span>
                    </div>
                <?php endif; ?>

                <div class="total-row total-row-main">
                    <span class="total-label"><strong>TOTAL:</strong></span>
                    <span class="total-value total-amount"><?php echo app_format_money($invoice->total, $invoice->currency_name); ?></span>
                </div>

                <?php if ($invoice->total_paid > 0): ?>
                    <div class="total-row">
                        <span class="total-label">Montant payé:</span>
                        <span class="total-value text-success"><?php echo app_format_money($invoice->total_paid, $invoice->currency_name); ?></span>
                    </div>
                <?php endif; ?>

                <?php if ($invoice->balance > 0): ?>
                    <div class="total-row total-row-balance">
                        <span class="total-label"><strong>SOLDE DÛ:</strong></span>
                        <span class="total-value balance-amount"><?php echo app_format_money($invoice->balance, $invoice->currency_name); ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Payments History -->
        <?php if (!empty($invoice->payments)): ?>
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
                                    <td><?php echo _d($payment->date); ?></td>
                                    <td>
                                        <?php
                                        // Get payment mode name
                                        $this->db->where('id', $payment->paymentmode);
                                        $mode = $this->db->get(db_prefix() . 'payment_modes')->row();
                                        echo $mode ? $mode->name : '-';
                                        ?>
                                    </td>
                                    <td><?php echo !empty($payment->note) ? $payment->note : '-'; ?></td>
                                    <td class="text-right text-success"><strong><?php echo app_format_money($payment->amount, $invoice->currency_name); ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <!-- Invoice Notes -->
        <?php if (!empty($invoice->adminnote) || !empty($invoice->clientnote) || !empty($invoice->terms)): ?>
            <div class="invoice-notes-section">
                <?php if (!empty($invoice->clientnote)): ?>
                    <div class="note-box">
                        <h3><i class="fa fa-info-circle"></i> Note au client</h3>
                        <div class="note-content"><?php echo nl2br($invoice->clientnote); ?></div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($invoice->terms)): ?>
                    <div class="note-box">
                        <h3><i class="fa fa-gavel"></i> Conditions générales</h3>
                        <div class="note-content"><?php echo nl2br($invoice->terms); ?></div>
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
