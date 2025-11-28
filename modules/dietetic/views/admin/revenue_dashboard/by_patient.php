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
                                    <i class="fa fa-users"></i> Revenus par Patient
                                </h2>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="<?php echo admin_url('dietetic/revenue_dashboard'); ?>" class="btn btn-default">
                                    <i class="fa fa-arrow-left"></i> Retour au Dashboard
                                </a>
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

                        <!-- Patients Revenue Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped dataTable" id="patientsRevenueTable">
                                <thead>
                                    <tr>
                                        <th>Patient</th>
                                        <th class="text-center">Abonnements Actifs</th>
                                        <th class="text-right">Total Facturé</th>
                                        <th class="text-right">Total Payé</th>
                                        <th class="text-right">Revenu Diététicien</th>
                                        <th class="text-right">Commission Plateforme</th>
                                        <th>Dernier Paiement</th>
                                        <th class="text-center">Actions</th>
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
                                                <td>
                                                    <a href="<?php echo admin_url('clients/client/' . $patient->patient_id); ?>">
                                                        <strong><?php echo htmlspecialchars($patient->patient_name); ?></strong>
                                                    </a>
                                                </td>
                                                <td class="text-center">
                                                    <?php if ($patient->active_subscriptions > 0): ?>
                                                        <span class="label label-success"><?php echo $patient->active_subscriptions; ?></span>
                                                    <?php else: ?>
                                                        <span class="label label-default">0</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-right"><?php echo number_format($patient->total_invoiced, 0, ',', ' '); ?> FCFA</td>
                                                <td class="text-right"><strong style="color: #27ae60;"><?php echo number_format($patient->total_paid, 0, ',', ' '); ?> FCFA</strong></td>
                                                <td class="text-right" style="background: #eafaf1;">
                                                    <strong style="color: #27ae60;"><?php echo number_format($patient->dietitian_share, 0, ',', ' '); ?> FCFA</strong>
                                                </td>
                                                <td class="text-right" style="background: #fdedec;">
                                                    <span style="color: #e74c3c;"><?php echo number_format($patient->platform_share, 0, ',', ' '); ?> FCFA</span>
                                                </td>
                                                <td>
                                                    <?php if ($patient->last_payment_date): ?>
                                                        <?php echo _d($patient->last_payment_date); ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">N/A</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <a href="<?php echo admin_url('dietetic/subscriptions?patient_id=' . $patient->patient_id); ?>" class="btn btn-sm btn-default" title="Voir abonnements">
                                                        <i class="fa fa-refresh"></i>
                                                    </a>
                                                    <a href="<?php echo admin_url('dietetic/invoices?patient_id=' . $patient->patient_id); ?>" class="btn btn-sm btn-info" title="Voir factures">
                                                        <i class="fa fa-file-text"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center text-muted">
                                                <p style="padding: 40px 0;">
                                                    <i class="fa fa-inbox fa-3x" style="opacity: 0.3;"></i><br><br>
                                                    Aucun revenu trouvé pour cette période
                                                </p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                                <?php if (!empty($patients_revenue)): ?>
                                    <tfoot>
                                        <tr style="background: #f8f9fa; font-weight: bold;">
                                            <td colspan="2" class="text-right"><strong>TOTAUX :</strong></td>
                                            <td class="text-right"><?php echo number_format($total_invoiced, 0, ',', ' '); ?> FCFA</td>
                                            <td class="text-right"><strong style="color: #27ae60;"><?php echo number_format($total_paid, 0, ',', ' '); ?> FCFA</strong></td>
                                            <td class="text-right" style="background: #eafaf1;"><strong style="color: #27ae60;"><?php echo number_format($total_dietitian, 0, ',', ' '); ?> FCFA</strong></td>
                                            <td class="text-right" style="background: #fdedec;"><strong style="color: #e74c3c;"><?php echo number_format($total_platform, 0, ',', ' '); ?> FCFA</strong></td>
                                            <td colspan="2"></td>
                                        </tr>
                                    </tfoot>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#patientsRevenueTable').DataTable({
        "pageLength": 25,
        "order": [[3, 'desc']], // Order by Total Payé descending
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json"
        },
        "columnDefs": [
            { "orderable": false, "targets": 7 } // Disable sorting on Actions column
        ]
    });
});
</script>
