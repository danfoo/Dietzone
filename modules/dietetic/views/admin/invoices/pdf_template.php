<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Facture <?php echo htmlspecialchars($invoice->invoice_number); ?></title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11pt;
            color: #333;
            line-height: 1.5;
        }

        .header {
            margin-bottom: 30px;
            border-bottom: 3px solid #3498db;
            padding-bottom: 20px;
        }

        .company-info {
            float: left;
            width: 50%;
        }

        .invoice-info {
            float: right;
            width: 45%;
            text-align: right;
        }

        .invoice-title {
            font-size: 28pt;
            font-weight: bold;
            color: #2c3e50;
            margin: 0;
        }

        .invoice-number {
            font-size: 14pt;
            color: #7f8c8d;
            margin: 5px 0;
        }

        .status-badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 10pt;
            margin-top: 10px;
        }

        .status-paid {
            background-color: #d4edda;
            color: #155724;
        }

        .status-sent {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-draft {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .status-overdue {
            background-color: #f8d7da;
            color: #721c24;
        }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }

        .billing-info {
            margin-top: 30px;
            margin-bottom: 30px;
        }

        .billing-from, .billing-to {
            float: left;
            width: 48%;
        }

        .billing-to {
            float: right;
        }

        .section-title {
            font-size: 11pt;
            font-weight: bold;
            color: #7f8c8d;
            text-transform: uppercase;
            margin-bottom: 10px;
            border-bottom: 1px solid #ecf0f1;
            padding-bottom: 5px;
        }

        .section-content {
            font-size: 10pt;
            line-height: 1.6;
        }

        .invoice-details {
            margin: 30px 0;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }

        .detail-row {
            margin-bottom: 8px;
        }

        .detail-label {
            font-weight: bold;
            color: #2c3e50;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
        }

        .items-table thead {
            background-color: #3498db;
            color: white;
        }

        .items-table th {
            padding: 12px;
            text-align: left;
            font-weight: bold;
        }

        .items-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #ecf0f1;
        }

        .items-table .text-right {
            text-align: right;
        }

        .items-table .description {
            width: 70%;
        }

        .items-table .amount {
            width: 30%;
        }

        .totals-row {
            background-color: #f8f9fa;
        }

        .grand-total-row {
            background-color: #e8f5e9;
            font-weight: bold;
            font-size: 12pt;
        }

        .grand-total-row td {
            padding: 15px 12px;
            border-top: 2px solid #27ae60;
        }

        .total-amount {
            color: #27ae60;
            font-size: 14pt;
        }

        .notes {
            margin: 30px 0;
            padding: 15px;
            background-color: #e7f3ff;
            border-left: 4px solid #3498db;
            border-radius: 3px;
        }

        .notes-title {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .payment-info {
            margin: 30px 0;
            padding: 15px;
            background-color: #d4edda;
            border-left: 4px solid #28a745;
            border-radius: 3px;
        }

        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #ecf0f1;
            font-size: 9pt;
            color: #7f8c8d;
            text-align: center;
        }

        .payment-terms {
            margin-top: 30px;
            font-size: 9pt;
            color: #7f8c8d;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header clearfix">
        <div class="company-info">
            <h1 style="margin: 0; color: #3498db;">DIETZONE</h1>
            <p style="margin: 5px 0 0 0; color: #7f8c8d;">Plateforme de gestion diététique</p>
        </div>
        <div class="invoice-info">
            <h1 class="invoice-title">FACTURE</h1>
            <p class="invoice-number"><?php echo htmlspecialchars($invoice->invoice_number); ?></p>
            <span class="status-badge status-<?php echo $invoice->status; ?>">
                <?php
                $status_labels = [
                    'draft' => 'Brouillon',
                    'sent' => 'Envoyée',
                    'paid' => 'Payée',
                    'overdue' => 'En retard',
                    'cancelled' => 'Annulée'
                ];
                echo $status_labels[$invoice->status] ?? ucfirst($invoice->status);
                ?>
            </span>
        </div>
    </div>

    <!-- Billing Information -->
    <div class="billing-info clearfix">
        <div class="billing-from">
            <div class="section-title">De</div>
            <div class="section-content">
                <strong><?php echo htmlspecialchars($invoice->dietitian_name); ?></strong><br>
                Diététicien<br>
                <?php if ($invoice->dietitian_email): ?>
                    <?php echo htmlspecialchars($invoice->dietitian_email); ?><br>
                <?php endif; ?>
            </div>
        </div>

        <div class="billing-to">
            <div class="section-title">Facturé à</div>
            <div class="section-content">
                <strong><?php echo htmlspecialchars($invoice->patient_name); ?></strong><br>
                <?php if ($invoice->patient_email): ?>
                    <?php echo htmlspecialchars($invoice->patient_email); ?><br>
                <?php endif; ?>
                <?php if ($invoice->patient_phone): ?>
                    <?php echo htmlspecialchars($invoice->patient_phone); ?><br>
                <?php endif; ?>
                <?php if ($invoice->patient_address): ?>
                    <?php echo nl2br(htmlspecialchars($invoice->patient_address)); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Invoice Details -->
    <div class="invoice-details clearfix">
        <div style="float: left; width: 48%;">
            <div class="detail-row">
                <span class="detail-label">Date d'émission :</span>
                <?php echo _d($invoice->issue_date); ?>
            </div>
            <div class="detail-row">
                <span class="detail-label">Date d'échéance :</span>
                <?php echo _d($invoice->due_date); ?>
            </div>
        </div>
        <div style="float: right; width: 48%; text-align: right;">
            <div class="detail-row">
                <span class="detail-label">Plan :</span>
                <?php echo htmlspecialchars($invoice->plan_name_fr ?: $invoice->plan_name); ?>
            </div>
            <div class="detail-row">
                <span class="detail-label">Source :</span>
                <?php echo ucfirst($invoice->referral_source); ?>
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th class="description">Description</th>
                <th class="amount text-right">Montant</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="description">
                    <strong><?php echo htmlspecialchars($invoice->plan_name_fr ?: $invoice->plan_name); ?></strong><br>
                    <small style="color: #7f8c8d;">Abonnement au plan de service</small>
                </td>
                <td class="amount text-right">
                    <?php echo number_format($invoice->amount, 0, ',', ' '); ?> FCFA
                </td>
            </tr>
            <tr class="totals-row">
                <td class="text-right"><strong>Sous-total (HT)</strong></td>
                <td class="text-right"><?php echo number_format($invoice->amount, 0, ',', ' '); ?> FCFA</td>
            </tr>
            <tr class="totals-row">
                <td class="text-right">TVA (<?php echo number_format($invoice->tax_rate, 2); ?>%)</td>
                <td class="text-right"><?php echo number_format($invoice->tax_amount, 0, ',', ' '); ?> FCFA</td>
            </tr>
            <tr class="grand-total-row">
                <td class="text-right">TOTAL (TTC)</td>
                <td class="text-right total-amount">
                    <?php echo number_format($invoice->total_amount, 0, ',', ' '); ?> FCFA
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Notes -->
    <?php if ($invoice->notes): ?>
        <div class="notes">
            <div class="notes-title">Notes :</div>
            <?php echo nl2br(htmlspecialchars($invoice->notes)); ?>
        </div>
    <?php endif; ?>

    <!-- Payment Information -->
    <?php if ($invoice->status == 'paid'): ?>
        <div class="payment-info">
            <strong>Facture payée</strong> le <?php echo _dt($invoice->paid_date); ?>
            <?php if ($invoice->payment_method): ?>
                par <?php echo ucfirst($invoice->payment_method); ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Payment Terms -->
    <div class="payment-terms">
        <strong>Conditions de paiement :</strong><br>
        Paiement dû sous 15 jours à compter de la date d'émission.<br>
        <br>
        <strong>Méthodes de paiement acceptées :</strong><br>
        Carte bancaire, Virement bancaire, Espèces, Mobile Money, Wave, Orange Money<br>
        <br>
        En cas de retard de paiement, des pénalités pourront être appliquées conformément aux conditions générales de vente.
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>DIETZONE - Plateforme de gestion diététique</p>
        <p>Ce document est une facture électronique générée automatiquement.</p>
        <p>Pour toute question concernant cette facture, veuillez contacter votre diététicien.</p>
    </div>
</body>
</html>
