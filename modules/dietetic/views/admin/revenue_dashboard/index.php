<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h2 style="margin-top: 0;">
                                    <i class="fa fa-line-chart"></i> Dashboard Revenus
                                </h2>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="<?php echo admin_url('dietetic/revenue_dashboard/export_csv?' . http_build_query(['start_date' => $start_date, 'end_date' => $end_date, 'dietitian_id' => $dietitian_id])); ?>" class="btn btn-success">
                                    <i class="fa fa-file-excel-o"></i> Exporter CSV
                                </a>
                                <a href="<?php echo admin_url('dietetic/revenue_dashboard/export_pdf?' . http_build_query(['start_date' => $start_date, 'end_date' => $end_date, 'dietitian_id' => $dietitian_id])); ?>" class="btn btn-danger">
                                    <i class="fa fa-file-pdf-o"></i> Exporter PDF
                                </a>
                            </div>
                        </div>

                        <hr />

                        <!-- Filters -->
                        <form method="get" class="form-inline" style="margin-bottom: 20px;">
                            <div class="form-group">
                                <label for="start_date">Du :</label>
                                <input type="date" name="start_date" id="start_date" class="form-control" value="<?php echo $start_date; ?>" required>
                            </div>
                            <div class="form-group" style="margin-left: 10px;">
                                <label for="end_date">Au :</label>
                                <input type="date" name="end_date" id="end_date" class="form-control" value="<?php echo $end_date; ?>" required>
                            </div>
                            <?php if ($is_admin): ?>
                                <div class="form-group" style="margin-left: 10px;">
                                    <label for="dietitian_id">Diététicien :</label>
                                    <select name="dietitian_id" id="dietitian_id" class="form-control selectpicker" data-live-search="true">
                                        <option value="">Tous les diététiciens</option>
                                        <?php
                                        $this->db->select('staffid, firstname, lastname');
                                        $this->db->from(db_prefix() . 'staff');
                                        $this->db->where('active', 1);
                                        $this->db->order_by('firstname', 'ASC');
                                        $staff_members = $this->db->get()->result();
                                        foreach ($staff_members as $staff):
                                        ?>
                                            <option value="<?php echo $staff->staffid; ?>" <?php echo $dietitian_id == $staff->staffid ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($staff->firstname . ' ' . $staff->lastname); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            <?php endif; ?>
                            <button type="submit" class="btn btn-info" style="margin-left: 10px;">
                                <i class="fa fa-filter"></i> Filtrer
                            </button>
                        </form>

                        <!-- Statistics Cards -->
                        <div class="row">
                            <div class="col-md-3">
                                <div class="panel_s stat-card-purple">
                                    <div class="panel-body text-center" style="padding: 20px;">
                                        <h1 style="margin: 0; color: white !important;"><?php echo number_format($stats->total_revenue, 0, ',', ' '); ?></h1>
                                        <p style="margin: 5px 0 0 0; color: white !important; opacity: 0.9;">FCFA - Revenu Total</p>
                                        <small style="color: white !important; opacity: 0.8;"><?php echo $stats->total_payments; ?> paiements</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="panel_s stat-card-pink">
                                    <div class="panel-body text-center" style="padding: 20px;">
                                        <h1 style="margin: 0; color: white !important;"><?php echo number_format($stats->dietitian_share, 0, ',', ' '); ?></h1>
                                        <p style="margin: 5px 0 0 0; color: white !important; opacity: 0.9;">FCFA - Part Diététicien</p>
                                        <small style="color: white !important; opacity: 0.8;"><?php echo $stats->total_payments > 0 ? number_format(($stats->dietitian_share / $stats->total_revenue) * 100, 1) : 0; ?>% du total</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="panel_s stat-card-blue">
                                    <div class="panel-body text-center" style="padding: 20px;">
                                        <h1 style="margin: 0; color: white !important;"><?php echo number_format($stats->active_subscriptions); ?></h1>
                                        <p style="margin: 5px 0 0 0; color: white !important; opacity: 0.9;">Abonnements Actifs</p>
                                        <small style="color: white !important; opacity: 0.8;">Moyenne: <?php echo number_format($stats->avg_payment, 0, ',', ' '); ?> FCFA</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="panel_s stat-card-orange">
                                    <div class="panel-body text-center" style="padding: 20px;">
                                        <h1 style="margin: 0; color: white !important;"><?php echo number_format($stats->pending_amount, 0, ',', ' '); ?></h1>
                                        <p style="margin: 5px 0 0 0; color: white !important; opacity: 0.9;">FCFA - En Attente</p>
                                        <small style="color: white !important; opacity: 0.8;"><?php echo $stats->pending_invoices; ?> factures</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Charts Row -->
                        <div class="row" style="margin-top: 20px;">
                            <!-- Revenue Evolution Chart -->
                            <div class="col-md-8">
                                <div class="panel_s">
                                    <div class="panel-body">
                                        <h4><i class="fa fa-line-chart"></i> Évolution des Revenus (12 derniers mois)</h4>
                                        <canvas id="revenueChart" height="80"></canvas>
                                    </div>
                                </div>
                            </div>

                            <!-- Revenue Distribution -->
                            <div class="col-md-4">
                                <div class="panel_s">
                                    <div class="panel-body">
                                        <h4><i class="fa fa-pie-chart"></i> Répartition des Revenus</h4>
                                        <canvas id="distributionChart" height="160"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Top Patients Table -->
                        <div class="row" style="margin-top: 20px;">
                            <div class="col-md-12">
                                <div class="panel_s">
                                    <div class="panel-body">
                                        <h4>
                                            <i class="fa fa-star"></i> Top 10 Patients par Revenu
                                            <a href="<?php echo admin_url('dietetic/revenue_dashboard/by_patient?' . http_build_query(['start_date' => $start_date, 'end_date' => $end_date, 'dietitian_id' => $dietitian_id])); ?>" class="btn btn-sm btn-info pull-right">
                                                <i class="fa fa-list"></i> Voir tous les patients
                                            </a>
                                        </h4>
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Patient</th>
                                                    <th class="text-right">Paiements</th>
                                                    <th class="text-right">Total Payé</th>
                                                    <th class="text-right">Part Diététicien</th>
                                                    <th>Dernier Paiement</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($top_patients)): ?>
                                                    <?php $rank = 1; foreach ($top_patients as $patient): ?>
                                                        <tr>
                                                            <td><?php echo $rank++; ?></td>
                                                            <td>
                                                                <a href="<?php echo admin_url('clients/client/' . $patient->patient_id); ?>">
                                                                    <?php echo htmlspecialchars($patient->patient_name); ?>
                                                                </a>
                                                            </td>
                                                            <td class="text-right"><?php echo $patient->payment_count; ?></td>
                                                            <td class="text-right"><strong><?php echo number_format($patient->total_paid, 0, ',', ' '); ?> FCFA</strong></td>
                                                            <td class="text-right" style="color: #27ae60;"><?php echo number_format($patient->dietitian_share, 0, ',', ' '); ?> FCFA</td>
                                                            <td><?php echo _d($patient->last_payment_date); ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="6" class="text-center text-muted">Aucun paiement trouvé pour cette période</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Payments -->
                        <div class="row" style="margin-top: 20px;">
                            <div class="col-md-12">
                                <div class="panel_s">
                                    <div class="panel-body">
                                        <h4><i class="fa fa-clock-o"></i> Paiements Récents</h4>
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Facture</th>
                                                    <th>Patient</th>
                                                    <th>Méthode</th>
                                                    <th class="text-right">Montant</th>
                                                    <th>Statut</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($recent_payments)): ?>
                                                    <?php foreach ($recent_payments as $payment): ?>
                                                        <tr>
                                                            <td><?php echo _d($payment->payment_date); ?></td>
                                                            <td>
                                                                <a href="<?php echo admin_url('dietetic/invoices/view/' . $payment->invoice_id); ?>">
                                                                    <?php echo htmlspecialchars($payment->invoice_number); ?>
                                                                </a>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($payment->patient_name); ?></td>
                                                            <td>
                                                                <?php
                                                                $methods = [
                                                                    'card' => 'Carte',
                                                                    'bank_transfer' => 'Virement',
                                                                    'cash' => 'Espèces',
                                                                    'mobile_money' => 'Mobile Money',
                                                                    'wave' => 'Wave',
                                                                    'orange_money' => 'Orange Money',
                                                                    'paypal' => 'PayPal'
                                                                ];
                                                                echo $methods[$payment->payment_method] ?? ucfirst($payment->payment_method);
                                                                ?>
                                                            </td>
                                                            <td class="text-right"><strong><?php echo number_format($payment->amount, 0, ',', ' '); ?> FCFA</strong></td>
                                                            <td>
                                                                <span class="label label-<?php echo $payment->status == 'completed' ? 'success' : 'warning'; ?>">
                                                                    <?php echo ucfirst($payment->status); ?>
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="6" class="text-center text-muted">Aucun paiement récent</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<style>
/* Force colored backgrounds on statistics cards */
.panel_s.stat-card-purple,
.panel_s.stat-card-purple .panel-body {
    background-color: #667eea !important;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    color: white !important;
}

.panel_s.stat-card-pink,
.panel_s.stat-card-pink .panel-body {
    background-color: #f5576c !important;
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%) !important;
    color: white !important;
}

.panel_s.stat-card-blue,
.panel_s.stat-card-blue .panel-body {
    background-color: #4facfe !important;
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%) !important;
    color: white !important;
}

.panel_s.stat-card-orange,
.panel_s.stat-card-orange .panel-body {
    background-color: #fa709a !important;
    background: linear-gradient(135deg, #fa709a 0%, #fee140 100%) !important;
    color: white !important;
}

/* Force white text on all elements inside stat cards */
.stat-card-purple *,
.stat-card-pink *,
.stat-card-blue *,
.stat-card-orange * {
    color: white !important;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<script>
$(document).ready(function() {
    // Revenue Evolution Chart
    var evolutionData = <?php echo json_encode($evolution); ?>;

    var ctx = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: evolutionData.map(d => d.month),
            datasets: [
                {
                    label: 'Revenu Total',
                    data: evolutionData.map(d => d.total),
                    borderColor: 'rgb(102, 126, 234)',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Part Diététicien',
                    data: evolutionData.map(d => d.dietitian_share),
                    borderColor: 'rgb(39, 174, 96)',
                    backgroundColor: 'rgba(39, 174, 96, 0.1)',
                    tension: 0.4,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y.toLocaleString('fr-FR') + ' FCFA';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString('fr-FR') + ' FCFA';
                        }
                    }
                }
            }
        }
    });

    // Distribution Chart
    var stats = <?php echo json_encode($stats); ?>;
    var ctx2 = document.getElementById('distributionChart').getContext('2d');
    new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: ['Diététicien', 'Plateforme'],
            datasets: [{
                data: [stats.dietitian_share, stats.platform_share],
                backgroundColor: [
                    'rgb(39, 174, 96)',
                    'rgb(231, 76, 60)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            var total = context.dataset.data.reduce((a, b) => a + b, 0);
                            var percentage = ((context.parsed / total) * 100).toFixed(1);
                            return context.label + ': ' + context.parsed.toLocaleString('fr-FR') + ' FCFA (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
});
</script>
