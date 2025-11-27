<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
.plan-card {
    background: white;
    border-radius: 10px;
    padding: 25px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    margin-bottom: 20px;
    transition: all 0.3s;
    border-left: 4px solid #3498db;
}

.plan-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
}

.plan-card.inactive {
    opacity: 0.6;
    border-left-color: #95a5a6;
}

.plan-header {
    display: flex;
    justify-content: space-between;
    align-items: start;
    margin-bottom: 15px;
}

.plan-title {
    font-size: 20px;
    font-weight: bold;
    color: #2c3e50;
    margin: 0;
}

.plan-price {
    font-size: 28px;
    font-weight: bold;
    color: #27ae60;
}

.plan-duration {
    color: #7f8c8d;
    font-size: 13px;
}

.plan-features {
    margin: 15px 0;
}

.feature-item {
    padding: 5px 0;
    color: #34495e;
}

.feature-item i {
    color: #27ae60;
    margin-right: 8px;
}

.plan-stats {
    background: #ecf0f1;
    padding: 15px;
    border-radius: 5px;
    margin-top: 15px;
}

.stat-item {
    display: inline-block;
    margin-right: 20px;
}

.stat-item .value {
    font-weight: bold;
    color: #2c3e50;
}

.stat-item .label {
    font-size: 12px;
    color: #7f8c8d;
}

.status-badge {
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
}

.status-badge.active {
    background: #d4edda;
    color: #155724;
}

.status-badge.inactive {
    background: #f8d7da;
    color: #721c24;
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
                                    <i class="fa fa-th-list" style="font-size: 36px; margin-right: 15px;"></i>
                                    Plans de Service
                                </h2>
                                <p style="color: rgba(255,255,255,0.9); margin: 10px 0 0 0;">
                                    Gérez vos offres de service et abonnements
                                </p>
                            </div>
                            <div class="col-md-4 text-right">
                                <a href="<?php echo admin_url('dietetic/service_plans/create'); ?>" class="btn btn-success btn-lg">
                                    <i class="fa fa-plus-circle"></i> Nouveau Plan
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <form method="GET" action="<?php echo admin_url('dietetic/service_plans'); ?>">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Statut</label>
                                        <select name="status" class="form-control" onchange="this.form.submit()">
                                            <option value="all" <?php echo (!$status_filter || $status_filter == 'all') ? 'selected' : ''; ?>>Tous</option>
                                            <option value="active" <?php echo ($status_filter == 'active') ? 'selected' : ''; ?>>Actifs</option>
                                            <option value="inactive" <?php echo ($status_filter == 'inactive') ? 'selected' : ''; ?>>Inactifs</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-9 text-right">
                                    <p class="text-muted" style="margin-top: 30px;">
                                        <i class="fa fa-info-circle"></i>
                                        <?php echo $total_plans; ?> plan(s) au total
                                    </p>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Plans Grid -->
        <div class="row">
            <?php if (empty($plans)): ?>
                <div class="col-md-12">
                    <div class="alert alert-info text-center">
                        <i class="fa fa-info-circle fa-3x" style="margin-bottom: 15px;"></i>
                        <h4>Aucun plan de service</h4>
                        <p>Commencez par créer votre premier plan de service</p>
                        <a href="<?php echo admin_url('dietetic/service_plans/create'); ?>" class="btn btn-success">
                            <i class="fa fa-plus-circle"></i> Créer un plan
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($plans as $plan): ?>
                    <div class="col-md-6">
                        <div class="plan-card <?php echo $plan->is_active ? '' : 'inactive'; ?>">
                            <div class="plan-header">
                                <div>
                                    <h3 class="plan-title">
                                        <?php echo htmlspecialchars($plan->name_fr ?: $plan->name); ?>
                                    </h3>
                                    <p class="plan-duration">
                                        <?php echo $plan->duration_value . ' ' . _l('dietetic_duration_' . $plan->duration_unit); ?>
                                        <?php if ($plan->trial_days > 0): ?>
                                            <span class="label label-warning" style="margin-left: 10px;">
                                                <i class="fa fa-gift"></i> <?php echo $plan->trial_days; ?> jours d'essai
                                            </span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <div class="plan-price">
                                        <?php echo number_format($plan->price, 0, ',', ' '); ?> FCFA
                                    </div>
                                    <small class="text-muted">/ <?php echo $plan->billing_cycle == 'monthly' ? 'mois' : 'an'; ?></small>
                                    <div style="margin-top: 10px;">
                                        <span class="status-badge <?php echo $plan->is_active ? 'active' : 'inactive'; ?>">
                                            <?php echo $plan->is_active ? 'Actif' : 'Inactif'; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <?php if ($plan->description_fr || $plan->description): ?>
                                <p class="text-muted" style="margin-bottom: 15px;">
                                    <?php echo htmlspecialchars($plan->description_fr ?: $plan->description); ?>
                                </p>
                            <?php endif; ?>

                            <?php
                            $features = json_decode($plan->features, true);
                            if ($features && is_array($features) && count($features) > 0):
                            ?>
                                <div class="plan-features">
                                    <?php foreach (array_slice($features, 0, 4) as $feature): ?>
                                        <div class="feature-item">
                                            <i class="fa fa-check-circle"></i>
                                            <?php echo htmlspecialchars($feature); ?>
                                        </div>
                                    <?php endforeach; ?>
                                    <?php if (count($features) > 4): ?>
                                        <div class="feature-item text-muted">
                                            <i class="fa fa-ellipsis-h"></i>
                                            +<?php echo count($features) - 4; ?> autres fonctionnalités
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <div class="plan-stats">
                                <div class="stat-item">
                                    <span class="value"><?php echo $plan->active_subscriptions; ?></span>
                                    <span class="label">Abonnements actifs</span>
                                </div>
                                <div class="stat-item">
                                    <span class="value"><?php echo $plan->total_subscriptions; ?></span>
                                    <span class="label">Total abonnements</span>
                                </div>
                                <div class="stat-item">
                                    <span class="value"><?php echo number_format($plan->total_revenue, 0, ',', ' '); ?> FCFA</span>
                                    <span class="label">Revenus générés</span>
                                </div>
                            </div>

                            <div style="margin-top: 15px; text-align: right;">
                                <div class="btn-group">
                                    <a href="<?php echo admin_url('dietetic/service_plans/view/' . $plan->id); ?>" class="btn btn-default btn-sm" title="Détails">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    <a href="<?php echo admin_url('dietetic/service_plans/edit/' . $plan->id); ?>" class="btn btn-info btn-sm" title="Modifier">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-<?php echo $plan->is_active ? 'warning' : 'success'; ?> btn-sm btn-toggle-status" data-id="<?php echo $plan->id; ?>" data-current-status="<?php echo $plan->is_active; ?>" title="<?php echo $plan->is_active ? 'Désactiver' : 'Activer'; ?>">
                                        <i class="fa fa-<?php echo $plan->is_active ? 'ban' : 'check'; ?>"></i>
                                    </button>
                                    <?php if ($plan->total_subscriptions == 0): ?>
                                        <button type="button" class="btn btn-danger btn-sm btn-delete-plan" data-id="<?php echo $plan->id; ?>" data-name="<?php echo htmlspecialchars($plan->name); ?>" title="Supprimer">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="panel_s">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="text-muted">
                                        Page <?php echo $current_page; ?> sur <?php echo $total_pages; ?>
                                        (<?php echo $total_plans; ?> plans au total)
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <nav aria-label="Pagination">
                                        <ul class="pagination pull-right">
                                            <?php if ($current_page > 1): ?>
                                                <li>
                                                    <a href="<?php echo admin_url('dietetic/service_plans?page=' . ($current_page - 1) . ($status_filter ? '&status=' . $status_filter : '')); ?>">
                                                        <span aria-hidden="true">&laquo;</span>
                                                    </a>
                                                </li>
                                            <?php else: ?>
                                                <li class="disabled"><span>&laquo;</span></li>
                                            <?php endif; ?>

                                            <?php
                                            $show_pages = 5;
                                            $half_show = floor($show_pages / 2);
                                            $start_page = max(1, $current_page - $half_show);
                                            $end_page = min($total_pages, $current_page + $half_show);

                                            if ($current_page <= $half_show) {
                                                $end_page = min($total_pages, $show_pages);
                                            }
                                            if ($current_page > $total_pages - $half_show) {
                                                $start_page = max(1, $total_pages - $show_pages + 1);
                                            }

                                            if ($start_page > 1):
                                            ?>
                                                <li><a href="<?php echo admin_url('dietetic/service_plans?page=1' . ($status_filter ? '&status=' . $status_filter : '')); ?>">1</a></li>
                                                <?php if ($start_page > 2): ?>
                                                    <li class="disabled"><span>...</span></li>
                                                <?php endif; ?>
                                            <?php endif; ?>

                                            <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                                                <li class="<?php echo ($i == $current_page) ? 'active' : ''; ?>">
                                                    <a href="<?php echo admin_url('dietetic/service_plans?page=' . $i . ($status_filter ? '&status=' . $status_filter : '')); ?>">
                                                        <?php echo $i; ?>
                                                    </a>
                                                </li>
                                            <?php endfor; ?>

                                            <?php if ($end_page < $total_pages): ?>
                                                <?php if ($end_page < $total_pages - 1): ?>
                                                    <li class="disabled"><span>...</span></li>
                                                <?php endif; ?>
                                                <li><a href="<?php echo admin_url('dietetic/service_plans?page=' . $total_pages . ($status_filter ? '&status=' . $status_filter : '')); ?>"><?php echo $total_pages; ?></a></li>
                                            <?php endif; ?>

                                            <?php if ($current_page < $total_pages): ?>
                                                <li>
                                                    <a href="<?php echo admin_url('dietetic/service_plans?page=' . ($current_page + 1) . ($status_filter ? '&status=' . $status_filter : '')); ?>">
                                                        <span aria-hidden="true">&raquo;</span>
                                                    </a>
                                                </li>
                                            <?php else: ?>
                                                <li class="disabled"><span>&raquo;</span></li>
                                            <?php endif; ?>
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php init_tail(); ?>

<script>
$(document).ready(function() {
    // Toggle status
    $('.btn-toggle-status').on('click', function() {
        var planId = $(this).data('id');
        var currentStatus = $(this).data('current-status');
        var action = currentStatus == 1 ? 'désactiver' : 'activer';

        if (!confirm('Voulez-vous vraiment ' + action + ' ce plan de service ?')) {
            return;
        }

        $.ajax({
            url: '<?php echo admin_url('dietetic/service_plans/toggle_active/'); ?>' + planId,
            type: 'POST',
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

    // Delete plan
    $('.btn-delete-plan').on('click', function() {
        var planId = $(this).data('id');
        var planName = $(this).data('name');

        if (!confirm('Êtes-vous sûr de vouloir supprimer le plan "' + planName + '" ?\n\nCette action est irréversible.')) {
            return;
        }

        $.ajax({
            url: '<?php echo admin_url('dietetic/service_plans/delete/'); ?>' + planId,
            type: 'POST',
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
