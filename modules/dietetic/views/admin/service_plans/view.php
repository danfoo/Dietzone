<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h3><?php echo htmlspecialchars($plan->name_fr ?: $plan->name); ?></h3>
                                <p class="text-muted"><?php echo htmlspecialchars($plan->description_fr ?: $plan->description); ?></p>
                            </div>
                            <div class="col-md-4 text-right">
                                <h2 style="color: #27ae60; margin: 0;">
                                    <?php echo number_format($plan->price, 0, ',', ' '); ?> FCFA
                                </h2>
                                <small class="text-muted">/ <?php echo $plan->billing_cycle == 'monthly' ? 'mois' : 'an'; ?></small>
                            </div>
                        </div>

                        <hr />

                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Durée:</strong> <?php echo $plan->duration_value . ' ' . $plan->duration_unit; ?></p>
                                <p><strong>Période d'essai:</strong> <?php echo $plan->trial_days > 0 ? $plan->trial_days . ' jours' : 'Aucune'; ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Statut:</strong>
                                    <span class="label label-<?php echo $plan->is_active ? 'success' : 'danger'; ?>">
                                        <?php echo $plan->is_active ? 'Actif' : 'Inactif'; ?>
                                    </span>
                                </p>
                                <p><strong>Créé par:</strong> Admin</p>
                            </div>
                        </div>

                        <?php if (!empty($features)): ?>
                            <hr />
                            <h4>Fonctionnalités</h4>
                            <ul>
                                <?php foreach ($features as $feature): ?>
                                    <li><?php echo htmlspecialchars($feature); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>

                        <hr />

                        <a href="<?php echo admin_url('dietetic/service_plans'); ?>" class="btn btn-default">
                            <i class="fa fa-arrow-left"></i> Retour
                        </a>
                        <a href="<?php echo admin_url('dietetic/service_plans/edit/' . $plan->id); ?>" class="btn btn-info">
                            <i class="fa fa-edit"></i> Modifier
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4>Statistiques</h4>
                        <div class="stat-box">
                            <p><strong><?php echo $plan->active_subscriptions; ?></strong> Abonnements actifs</p>
                        </div>
                        <div class="stat-box">
                            <p><strong><?php echo $plan->total_subscriptions; ?></strong> Total abonnements</p>
                        </div>
                        <div class="stat-box">
                            <p><strong><?php echo number_format($plan->total_revenue, 0, ',', ' '); ?> FCFA</strong> Revenus</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
