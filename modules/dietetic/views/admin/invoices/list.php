<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
.invoice-row:hover {
    background-color: #f8f9fa !important;
}

.status-badge {
    padding: 5px 12px;
    border-radius: 15px;
    font-size: 11px;
    font-weight: bold;
}

.status-badge.draft {
    background: #e2e3e5;
    color: #383d41;
}

.status-badge.sent {
    background: #cce5ff;
    color: #004085;
}

.status-badge.paid {
    background: #d4edda;
    color: #155724;
}

.status-badge.overdue {
    background: #f8d7da;
    color: #721c24;
}

.status-badge.cancelled {
    background: #d6d8db;
    color: #1b1e21;
}

.stat-card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.08);
}

.stat-card .value {
    font-size: 28px;
    font-weight: bold;
    margin-bottom: 5px;
}

.stat-card .label {
    font-size: 12px;
    color: #7f8c8d;
    text-transform: uppercase;
}
</style>

<div id="wrapper">
    <div class="content">
        <!-- Header -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 8px; padding: 30px;">
                        <div class="row">
                            <div class="col-md-8">
                                <h2 style="color: white; margin: 0;">
                                    <i class="fa fa-file-text-o" style="font-size: 36px; margin-right: 15px;"></i>
                                    Factures
                                </h2>
                                <p style="color: rgba(255,255,255,0.9); margin: 10px 0 0 0;">
                                    Gestion des factures et paiements
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="row" style="margin-bottom: 20px;">
            <div class="col-md-2">
                <div class="stat-card">
                    <div class="value" style="color: #3498db;"><?php echo $stats['total']; ?></div>
                    <div class="label">Total</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-card">
                    <div class="value" style="color: #95a5a6;"><?php echo $stats['draft']; ?></div>
                    <div class="label">Brouillons</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-card">
                    <div class="value" style="color: #3498db;"><?php echo $stats['sent']; ?></div>
                    <div class="label">Envoyées</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-card">
                    <div class="value" style="color: #27ae60;"><?php echo $stats['paid']; ?></div>
                    <div class="label">Payées</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-card">
                    <div class="value" style="color: #e74c3c;"><?php echo $stats['overdue']; ?></div>
                    <div class="label">En retard</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-card">
                    <div class="value" style="color: #27ae60;"><?php echo number_format($stats['total_paid']/1000, 0); ?>K</div>
                    <div class="label">Payé (FCFA)</div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <form method="GET">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Statut</label>
                                        <select name="status" class="form-control" onchange="this.form.submit()">
                                            <option value="all">Tous</option>
                                            <option value="draft" <?php echo ($status_filter == 'draft') ? 'selected' : ''; ?>>Brouillons</option>
                                            <option value="sent" <?php echo ($status_filter == 'sent') ? 'selected' : ''; ?>>Envoyées</option>
                                            <option value="paid" <?php echo ($status_filter == 'paid') ? 'selected' : ''; ?>>Payées</option>
                                            <option value="overdue" <?php echo ($status_filter == 'overdue') ? 'selected' : ''; ?>>En retard</option>
                                            <option value="cancelled" <?php echo ($status_filter == 'cancelled') ? 'selected' : ''; ?>>Annulées</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Patient</label>
                                        <select name="patient" class="form-control" onchange="this.form.submit()">
                                            <option value="">Tous</option>
                                            <?php foreach ($patients as $patient): ?>
                                                <option value="<?php echo $patient->id; ?>" <?php echo ($patient_filter == $patient->id) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($patient->company); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label>&nbsp;</label>
                                    <p class="text-muted"><?php echo $total_invoices; ?> facture(s)</p>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoices Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (empty($invoices)): ?>
                            <div class="alert alert-info text-center">
                                <i class="fa fa-info-circle fa-3x" style="margin-bottom: 15px;"></i>
                                <h4>Aucune facture</h4>
                                <p>Les factures seront générées automatiquement pour les abonnements</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr style="background: #f8f9fa;">
                                            <th>Numéro</th>
                                            <th>Patient</th>
                                            <th>Plan</th>
                                            <th>Date</th>
                                            <th>Échéance</th>
                                            <th>Montant</th>
                                            <th>Statut</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($invoices as $inv): ?>
                                            <tr class="invoice-row">
                                                <td>
                                                    <strong><?php echo htmlspecialchars($inv->invoice_number); ?></strong>
                                                </td>
                                                <td>
                                                    <?php echo htmlspecialchars($inv->patient_name); ?>
                                                    <br><small class="text-muted"><?php echo htmlspecialchars($inv->dietitian_name); ?></small>
                                                </td>
                                                <td>
                                                    <small><?php echo htmlspecialchars($inv->plan_name); ?></small>
                                                </td>
                                                <td><?php echo _d($inv->issue_date); ?></td>
                                                <td>
                                                    <?php echo _d($inv->due_date); ?>
                                                    <?php if ($inv->status == 'overdue'): ?>
                                                        <br><small class="text-danger">
                                                            <i class="fa fa-exclamation-triangle"></i> En retard
                                                        </small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <strong><?php echo number_format($inv->total_amount, 0, ',', ' '); ?> FCFA</strong>
                                                    <br><small class="text-muted">HT: <?php echo number_format($inv->amount, 0, ',', ' '); ?></small>
                                                </td>
                                                <td>
                                                    <span class="status-badge <?php echo $inv->status; ?>">
                                                        <?php
                                                        $status_labels = [
                                                            'draft' => 'Brouillon',
                                                            'sent' => 'Envoyée',
                                                            'paid' => 'Payée',
                                                            'overdue' => 'En retard',
                                                            'cancelled' => 'Annulée'
                                                        ];
                                                        echo $status_labels[$inv->status] ?? $inv->status;
                                                        ?>
                                                    </span>
                                                    <?php if ($inv->paid_date && $inv->status == 'paid'): ?>
                                                        <br><small class="text-muted">Le <?php echo _d($inv->paid_date); ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group">
                                                        <a href="<?php echo admin_url('dietetic/invoices/view/' . $inv->id); ?>" class="btn btn-default btn-sm" title="Voir">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                        <a href="<?php echo admin_url('dietetic/invoices/pdf/' . $inv->id); ?>" class="btn btn-info btn-sm" title="Télécharger PDF" target="_blank">
                                                            <i class="fa fa-file-pdf-o"></i>
                                                        </a>
                                                        <?php if ($inv->status != 'paid' && $inv->status != 'cancelled'): ?>
                                                            <button type="button" class="btn btn-success btn-sm btn-mark-paid" data-id="<?php echo $inv->id; ?>" title="Marquer payée">
                                                                <i class="fa fa-check"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <?php if ($total_pages > 1): ?>
                                <hr />
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="text-muted">Page <?php echo $current_page; ?> sur <?php echo $total_pages; ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <nav>
                                            <ul class="pagination pull-right">
                                                <?php if ($current_page > 1): ?>
                                                    <li><a href="<?php echo admin_url('dietetic/invoices?page=' . ($current_page - 1)); ?>">&laquo;</a></li>
                                                <?php else: ?>
                                                    <li class="disabled"><span>&laquo;</span></li>
                                                <?php endif; ?>

                                                <?php for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++): ?>
                                                    <li class="<?php echo ($i == $current_page) ? 'active' : ''; ?>">
                                                        <a href="<?php echo admin_url('dietetic/invoices?page=' . $i); ?>"><?php echo $i; ?></a>
                                                    </li>
                                                <?php endfor; ?>

                                                <?php if ($current_page < $total_pages): ?>
                                                    <li><a href="<?php echo admin_url('dietetic/invoices?page=' . ($current_page + 1)); ?>">&raquo;</a></li>
                                                <?php else: ?>
                                                    <li class="disabled"><span>&raquo;</span></li>
                                                <?php endif; ?>
                                            </ul>
                                        </nav>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
$(document).ready(function() {
    // Mark as paid
    $('.btn-mark-paid').on('click', function() {
        var invoiceId = $(this).data('id');

        if (!confirm('Marquer cette facture comme payée ?')) {
            return;
        }

        $.ajax({
            url: '<?php echo admin_url('dietetic/invoices/mark_paid/'); ?>' + invoiceId,
            type: 'POST',
            data: {
                payment_method: 'cash',
                <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert_float('success', response.message);
                    location.reload();
                } else {
                    alert_float('danger', response.message);
                }
            }
        });
    });
});
</script>
