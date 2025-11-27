<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
.subscription-row {
    transition: all 0.2s;
}

.subscription-row:hover {
    background-color: #f8f9fa !important;
}

.status-badge {
    padding: 5px 12px;
    border-radius: 15px;
    font-size: 11px;
    font-weight: bold;
    text-transform: uppercase;
}

.status-badge.trial {
    background: #fff3cd;
    color: #856404;
}

.status-badge.active {
    background: #d4edda;
    color: #155724;
}

.status-badge.expired {
    background: #f8d7da;
    color: #721c24;
}

.status-badge.cancelled {
    background: #e2e3e5;
    color: #383d41;
}

.status-badge.pending {
    background: #cce5ff;
    color: #004085;
}

.referral-badge {
    padding: 3px 8px;
    border-radius: 10px;
    font-size: 10px;
}

.referral-badge.platform {
    background: #ffe5cc;
    color: #e67e22;
}

.referral-badge.dietitian {
    background: #d4edda;
    color: #27ae60;
}

.stat-card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.08);
}

.stat-card .value {
    font-size: 32px;
    font-weight: bold;
    margin-bottom: 5px;
}

.stat-card .label {
    font-size: 13px;
    color: #7f8c8d;
    text-transform: uppercase;
}
</style>

<div id="wrapper">
    <div class="content">
        <!-- Page Header -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 8px; padding: 30px;">
                        <div class="row">
                            <div class="col-md-8">
                                <h2 style="color: white; margin: 0;">
                                    <i class="fa fa-refresh" style="font-size: 36px; margin-right: 15px;"></i>
                                    Abonnements
                                </h2>
                                <p style="color: rgba(255,255,255,0.9); margin: 10px 0 0 0;">
                                    Gérez les abonnements des patients aux plans de service
                                </p>
                            </div>
                            <div class="col-md-4 text-right">
                                <?php if (dietetic_has_permission('create')): ?>
                                    <a href="<?php echo admin_url('dietetic/subscriptions/create'); ?>" class="btn btn-success btn-lg">
                                        <i class="fa fa-plus-circle"></i> Nouvel Abonnement
                                    </a>
                                <?php endif; ?>
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
                    <div class="value" style="color: #f39c12;"><?php echo $stats['trial']; ?></div>
                    <div class="label">Essai</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-card">
                    <div class="value" style="color: #27ae60;"><?php echo $stats['active']; ?></div>
                    <div class="label">Actifs</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-card">
                    <div class="value" style="color: #e74c3c;"><?php echo $stats['expired']; ?></div>
                    <div class="label">Expirés</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-card">
                    <div class="value" style="color: #95a5a6;"><?php echo $stats['cancelled']; ?></div>
                    <div class="label">Annulés</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-card">
                    <div class="value" style="color: #9b59b6;"><?php echo $stats['platform_referrals']; ?>/<?php echo $stats['dietitian_referrals']; ?></div>
                    <div class="label">P / D</div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <form method="GET" action="<?php echo admin_url('dietetic/subscriptions'); ?>">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Statut</label>
                                        <select name="status" class="form-control" onchange="this.form.submit()">
                                            <option value="all">Tous</option>
                                            <option value="trial" <?php echo ($status_filter == 'trial') ? 'selected' : ''; ?>>Essai</option>
                                            <option value="active" <?php echo ($status_filter == 'active') ? 'selected' : ''; ?>>Actifs</option>
                                            <option value="pending" <?php echo ($status_filter == 'pending') ? 'selected' : ''; ?>>En attente</option>
                                            <option value="expired" <?php echo ($status_filter == 'expired') ? 'selected' : ''; ?>>Expirés</option>
                                            <option value="cancelled" <?php echo ($status_filter == 'cancelled') ? 'selected' : ''; ?>>Annulés</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Source de Référence</label>
                                        <select name="referral" class="form-control" onchange="this.form.submit()">
                                            <option value="all">Toutes</option>
                                            <option value="platform" <?php echo ($referral_filter == 'platform') ? 'selected' : ''; ?>>Plateforme</option>
                                            <option value="dietitian" <?php echo ($referral_filter == 'dietitian') ? 'selected' : ''; ?>>Diététicien</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Plan de Service</label>
                                        <select name="plan" class="form-control" onchange="this.form.submit()">
                                            <option value="all">Tous</option>
                                            <?php foreach ($plans as $plan): ?>
                                                <option value="<?php echo $plan->id; ?>" <?php echo ($plan_filter == $plan->id) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($plan->name_fr ?: $plan->name); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label>&nbsp;</label>
                                    <p class="text-muted">
                                        <?php echo $total_subscriptions; ?> abonnement(s)
                                    </p>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subscriptions Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (empty($subscriptions)): ?>
                            <div class="alert alert-info text-center">
                                <i class="fa fa-info-circle fa-3x" style="margin-bottom: 15px;"></i>
                                <h4>Aucun abonnement</h4>
                                <p>Commencez par créer un abonnement pour un patient</p>
                                <?php if (dietetic_has_permission('create')): ?>
                                    <a href="<?php echo admin_url('dietetic/subscriptions/create'); ?>" class="btn btn-success">
                                        <i class="fa fa-plus-circle"></i> Créer un abonnement
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr style="background: #f8f9fa;">
                                            <th>Patient</th>
                                            <th>Plan</th>
                                            <th>Diététicien</th>
                                            <th>Montant</th>
                                            <th>Période</th>
                                            <th>Source</th>
                                            <th>Statut</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($subscriptions as $sub): ?>
                                            <tr class="subscription-row">
                                                <td>
                                                    <strong><?php echo htmlspecialchars($sub->patient_name); ?></strong>
                                                </td>
                                                <td>
                                                    <?php echo htmlspecialchars($sub->plan_name_fr ?: $sub->plan_name); ?>
                                                </td>
                                                <td>
                                                    <small class="text-muted"><?php echo htmlspecialchars($sub->dietitian_name); ?></small>
                                                </td>
                                                <td>
                                                    <strong><?php echo number_format($sub->amount, 0, ',', ' '); ?> FCFA</strong>
                                                    <br><small class="text-muted"><?php echo $sub->billing_cycle == 'monthly' ? '/mois' : '/an'; ?></small>
                                                </td>
                                                <td>
                                                    <small>
                                                        <?php echo _d($sub->start_date); ?> <br>
                                                        au <?php echo _d($sub->end_date); ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <span class="referral-badge <?php echo $sub->referral_source; ?>">
                                                        <i class="fa fa-<?php echo $sub->referral_source == 'platform' ? 'building' : 'user-md'; ?>"></i>
                                                        <?php echo ucfirst($sub->referral_source); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="status-badge <?php echo $sub->status; ?>">
                                                        <?php
                                                        $status_labels = [
                                                            'trial' => 'Essai',
                                                            'active' => 'Actif',
                                                            'expired' => 'Expiré',
                                                            'cancelled' => 'Annulé',
                                                            'pending' => 'En attente'
                                                        ];
                                                        echo $status_labels[$sub->status] ?? $sub->status;
                                                        ?>
                                                    </span>
                                                    <?php if ($sub->trial_end_date && $sub->status == 'trial'): ?>
                                                        <br><small class="text-muted">Fin: <?php echo _d($sub->trial_end_date); ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group">
                                                        <a href="<?php echo admin_url('dietetic/subscriptions/view/' . $sub->id); ?>" class="btn btn-default btn-sm" title="Voir">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                        <?php if (dietetic_has_permission('edit') && in_array($sub->status, ['trial', 'active', 'pending'])): ?>
                                                            <a href="<?php echo admin_url('dietetic/subscriptions/edit/' . $sub->id); ?>" class="btn btn-info btn-sm" title="Modifier">
                                                                <i class="fa fa-edit"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                        <?php if (dietetic_has_permission('delete') && $sub->status != 'cancelled'): ?>
                                                            <button type="button" class="btn btn-danger btn-sm btn-cancel-subscription" data-id="<?php echo $sub->id; ?>" data-patient="<?php echo htmlspecialchars($sub->patient_name); ?>" title="Annuler">
                                                                <i class="fa fa-ban"></i>
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
                                        <p class="text-muted">
                                            Page <?php echo $current_page; ?> sur <?php echo $total_pages; ?>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <nav>
                                            <ul class="pagination pull-right">
                                                <?php if ($current_page > 1): ?>
                                                    <li><a href="<?php echo admin_url('dietetic/subscriptions?page=' . ($current_page - 1)); ?>">&laquo;</a></li>
                                                <?php else: ?>
                                                    <li class="disabled"><span>&laquo;</span></li>
                                                <?php endif; ?>

                                                <?php for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++): ?>
                                                    <li class="<?php echo ($i == $current_page) ? 'active' : ''; ?>">
                                                        <a href="<?php echo admin_url('dietetic/subscriptions?page=' . $i); ?>"><?php echo $i; ?></a>
                                                    </li>
                                                <?php endfor; ?>

                                                <?php if ($current_page < $total_pages): ?>
                                                    <li><a href="<?php echo admin_url('dietetic/subscriptions?page=' . ($current_page + 1)); ?>">&raquo;</a></li>
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
    // Cancel subscription
    $('.btn-cancel-subscription').on('click', function() {
        var subId = $(this).data('id');
        var patientName = $(this).data('patient');

        var reason = prompt('Raison de l\'annulation pour ' + patientName + ' :');

        if (reason === null) {
            return; // Cancelled
        }

        $.ajax({
            url: '<?php echo admin_url('dietetic/subscriptions/cancel/'); ?>' + subId,
            type: 'POST',
            data: {
                reason: reason,
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
