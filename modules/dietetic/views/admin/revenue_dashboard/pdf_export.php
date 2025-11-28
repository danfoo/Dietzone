<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Rapport de Revenus</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #333;
        }
        h1 {
            color: #2c3e50;
            font-size: 24px;
            margin-bottom: 10px;
            border-bottom: 3px solid #3498db;
            padding-bottom: 10px;
        }
        h2 {
            color: #34495e;
            font-size: 16px;
            margin-top: 20px;
            margin-bottom: 10px;
            background: #ecf0f1;
            padding: 8px;
        }
        .header-info {
            margin-bottom: 20px;
            background: #f8f9fa;
            padding: 10px;
            border-left: 4px solid #3498db;
        }
        .stats-grid {
            width: 100%;
            margin-bottom: 20px;
        }
        .stat-box {
            display: inline-block;
            width: 23%;
            padding: 15px;
            margin-right: 2%;
            text-align: center;
            border: 1px solid #ddd;
            background: #f8f9fa;
        }
        .stat-box h3 {
            font-size: 24px;
            margin: 0;
            color: #2c3e50;
        }
        .stat-box p {
            margin: 5px 0 0 0;
            font-size: 10px;
            color: #7f8c8d;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table th {
            background: #34495e;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 10px;
        }
        table td {
            padding: 6px 8px;
            border-bottom: 1px solid #ecf0f1;
            font-size: 10px;
        }
        table tr:nth-child(even) {
            background: #f8f9fa;
        }
        table tfoot td {
            font-weight: bold;
            background: #ecf0f1;
            border-top: 2px solid #34495e;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 9px;
            color: #7f8c8d;
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>Rapport de Revenus - Dietzone</h1>

    <div class="header-info">
        <p><strong>Diététicien :</strong> <?php echo htmlspecialchars($dietitian_name); ?></p>
        <p><strong>Période :</strong> Du <?php echo _d($start_date); ?> au <?php echo _d($end_date); ?></p>
        <p><strong>Généré le :</strong> <?php echo _dt(date('Y-m-d H:i:s')); ?></p>
    </div>

    <h2>Statistiques Générales</h2>
    <div class="stats-grid">
        <div class="stat-box">
            <h3><?php echo number_format($stats->total_revenue, 0, ',', ' '); ?></h3>
            <p>FCFA - Revenu Total</p>
            <p><?php echo $stats->total_payments; ?> paiements</p>
        </div>
        <div class="stat-box">
            <h3><?php echo number_format($stats->dietitian_share, 0, ',', ' '); ?></h3>
            <p>FCFA - Part Diététicien</p>
            <p><?php echo $stats->total_payments > 0 ? number_format(($stats->dietitian_share / $stats->total_revenue) * 100, 1) : 0; ?>% du total</p>
        </div>
        <div class="stat-box">
            <h3><?php echo number_format($stats->active_subscriptions); ?></h3>
            <p>Abonnements Actifs</p>
            <p>Moyenne: <?php echo number_format($stats->avg_payment, 0, ',', ' '); ?> FCFA</p>
        </div>
        <div class="stat-box" style="margin-right: 0;">
            <h3><?php echo number_format($stats->pending_amount, 0, ',', ' '); ?></h3>
            <p>FCFA - En Attente</p>
            <p><?php echo $stats->pending_invoices; ?> factures</p>
        </div>
    </div>

    <h2>Revenus par Patient</h2>
    <table>
        <thead>
            <tr>
                <th>Patient</th>
                <th class="text-center">Abonnements</th>
                <th class="text-right">Total Facturé</th>
                <th class="text-right">Total Payé</th>
                <th class="text-right">Revenu Diététicien</th>
                <th class="text-right">Commission Plateforme</th>
                <th class="text-center">Dernier Paiement</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($patients_revenue)): ?>
                <?php
                $total_invoiced = 0;
                $total_paid = 0;
                $total_dietitian = 0;
                $total_platform = 0;

                foreach ($patients_revenue as $patient):
                    $total_invoiced += $patient->total_invoiced;
                    $total_paid += $patient->total_paid;
                    $total_dietitian += $patient->dietitian_share;
                    $total_platform += $patient->platform_share;
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($patient->patient_name); ?></td>
                        <td class="text-center"><?php echo $patient->active_subscriptions; ?></td>
                        <td class="text-right"><?php echo number_format($patient->total_invoiced, 0, ',', ' '); ?> FCFA</td>
                        <td class="text-right"><?php echo number_format($patient->total_paid, 0, ',', ' '); ?> FCFA</td>
                        <td class="text-right"><?php echo number_format($patient->dietitian_share, 0, ',', ' '); ?> FCFA</td>
                        <td class="text-right"><?php echo number_format($patient->platform_share, 0, ',', ' '); ?> FCFA</td>
                        <td class="text-center">
                            <?php echo $patient->last_payment_date ? _d($patient->last_payment_date) : 'N/A'; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">Aucun revenu trouvé pour cette période</td>
                </tr>
            <?php endif; ?>
        </tbody>
        <?php if (!empty($patients_revenue)): ?>
            <tfoot>
                <tr>
                    <td colspan="2" class="text-right">TOTAUX :</td>
                    <td class="text-right"><?php echo number_format($total_invoiced, 0, ',', ' '); ?> FCFA</td>
                    <td class="text-right"><?php echo number_format($total_paid, 0, ',', ' '); ?> FCFA</td>
                    <td class="text-right"><?php echo number_format($total_dietitian, 0, ',', ' '); ?> FCFA</td>
                    <td class="text-right"><?php echo number_format($total_platform, 0, ',', ' '); ?> FCFA</td>
                    <td></td>
                </tr>
            </tfoot>
        <?php endif; ?>
    </table>

    <div class="footer">
        <p>Ce rapport a été généré automatiquement par Dietzone - Système de Gestion Diététique</p>
        <p>© <?php echo date('Y'); ?> Dietzone. Tous droits réservés.</p>
    </div>
</body>
</html>
