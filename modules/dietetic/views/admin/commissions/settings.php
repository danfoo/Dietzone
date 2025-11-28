<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
.commission-card {
    background: white;
    border-radius: 10px;
    padding: 25px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    margin-bottom: 20px;
    border-left: 4px solid #3498db;
}

.commission-card.platform {
    border-left-color: #e67e22;
}

.commission-card.dietitian {
    border-left-color: #27ae60;
}

.commission-card h4 {
    margin-top: 0;
    color: #2c3e50;
}

.percentage-display {
    font-size: 48px;
    font-weight: bold;
    margin: 20px 0;
}

.percentage-display.dietitian-color {
    color: #27ae60;
}

.percentage-display.platform-color {
    color: #e67e22;
}

.stat-box {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    text-align: center;
    margin-bottom: 15px;
}

.stat-box .value {
    font-size: 24px;
    font-weight: bold;
    color: #2c3e50;
}

.stat-box .label {
    font-size: 12px;
    color: #7f8c8d;
    text-transform: uppercase;
    margin-top: 5px;
}

.preview-box {
    background: #ecf0f1;
    border: 2px solid #3498db;
    border-radius: 8px;
    padding: 20px;
    margin-top: 15px;
    display: none;
}

.preview-box.show {
    display: block;
}

.preview-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #bdc3c7;
}

.preview-row:last-child {
    border-bottom: none;
    font-weight: bold;
    font-size: 18px;
}
</style>

<div id="wrapper">
    <div class="content">
        <!-- Page Header -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 8px; padding: 30px;">
                        <h2 style="color: white; margin: 0;">
                            <i class="fa fa-percent" style="font-size: 36px; margin-right: 15px;"></i>
                            Configuration des Commissions
                        </h2>
                        <p style="color: rgba(255,255,255,0.9); margin: 10px 0 0 0;">
                            Gérez les pourcentages de partage de revenus entre diététiciens et plateforme
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Platform Statistics -->
        <?php if ($platform_stats): ?>
        <div class="row">
            <div class="col-md-4">
                <div class="stat-box">
                    <div class="value" style="color: #e67e22;">
                        <?php echo number_format($platform_stats['total_platform_revenue'], 0, ',', ' '); ?> FCFA
                    </div>
                    <div class="label">Revenus Plateforme</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-box">
                    <div class="value" style="color: #27ae60;">
                        <?php echo number_format($platform_stats['total_dietitian_payments'], 0, ',', ' '); ?> FCFA
                    </div>
                    <div class="label">Paiements Diététiciens</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-box">
                    <div class="value" style="color: #f39c12;">
                        <?php echo number_format($platform_stats['pending_dietitian_payments'], 0, ',', ' '); ?> FCFA
                    </div>
                    <div class="label">En Attente de Paiement</div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Current Settings -->
        <div class="row">
            <!-- Platform Referrals -->
            <div class="col-md-6">
                <div class="commission-card platform">
                    <h4>
                        <i class="fa fa-building"></i> Patients Apportés par la Plateforme
                    </h4>

                    <?php if ($current_settings['platform']): ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="percentage-display dietitian-color">
                                    <?php echo number_format($current_settings['platform']->dietitian_percentage, 0); ?>%
                                </div>
                                <p style="color: #27ae60; font-weight: bold;">Diététicien</p>
                            </div>
                            <div class="col-md-6">
                                <div class="percentage-display platform-color">
                                    <?php echo number_format($current_settings['platform']->platform_percentage, 0); ?>%
                                </div>
                                <p style="color: #e67e22; font-weight: bold;">Plateforme</p>
                            </div>
                        </div>

                        <div class="alert alert-info" style="margin-top: 15px;">
                            <i class="fa fa-info-circle"></i>
                            <strong>Effectif depuis :</strong> <?php echo _d($current_settings['platform']->effective_from); ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="fa fa-exclamation-triangle"></i>
                            Aucune configuration active
                        </div>
                    <?php endif; ?>

                    <button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#modal-update-platform">
                        <i class="fa fa-edit"></i> Modifier la Configuration
                    </button>
                </div>
            </div>

            <!-- Dietitian Referrals -->
            <div class="col-md-6">
                <div class="commission-card dietitian">
                    <h4>
                        <i class="fa fa-user-md"></i> Patients Apportés par le Diététicien
                    </h4>

                    <?php if ($current_settings['dietitian']): ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="percentage-display dietitian-color">
                                    <?php echo number_format($current_settings['dietitian']->dietitian_percentage, 0); ?>%
                                </div>
                                <p style="color: #27ae60; font-weight: bold;">Diététicien</p>
                            </div>
                            <div class="col-md-6">
                                <div class="percentage-display platform-color">
                                    <?php echo number_format($current_settings['dietitian']->platform_percentage, 0); ?>%
                                </div>
                                <p style="color: #e67e22; font-weight: bold;">Plateforme</p>
                            </div>
                        </div>

                        <div class="alert alert-info" style="margin-top: 15px;">
                            <i class="fa fa-info-circle"></i>
                            <strong>Effectif depuis :</strong> <?php echo _d($current_settings['dietitian']->effective_from); ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="fa fa-exclamation-triangle"></i>
                            Aucune configuration active
                        </div>
                    <?php endif; ?>

                    <button type="button" class="btn btn-success btn-block" data-toggle="modal" data-target="#modal-update-dietitian">
                        <i class="fa fa-edit"></i> Modifier la Configuration
                    </button>
                </div>
            </div>
        </div>

        <!-- Commission Calculator -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4><i class="fa fa-calculator"></i> Calculateur de Commission</h4>
                        <p class="text-muted">Prévisualisez le partage des revenus pour un montant donné</p>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Montant (FCFA)</label>
                                    <input type="number" id="calc-amount" class="form-control" value="10000" min="0" step="100">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Type de Référence</label>
                                    <select id="calc-source" class="form-control">
                                        <option value="platform">Plateforme (<?php echo $current_settings['platform'] ? $current_settings['platform']->dietitian_percentage . '/' . $current_settings['platform']->platform_percentage : '-'; ?>)</option>
                                        <option value="dietitian">Diététicien (<?php echo $current_settings['dietitian'] ? $current_settings['dietitian']->dietitian_percentage . '/' . $current_settings['dietitian']->platform_percentage : '-'; ?>)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="button" id="btn-calculate" class="btn btn-info btn-block">
                                        <i class="fa fa-calculator"></i> Calculer
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div id="calc-preview" class="preview-box"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Update Platform Commission -->
<div class="modal fade" id="modal-update-platform" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?php echo admin_url('dietetic/commissions/update'); ?>" method="POST">
                <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                <input type="hidden" name="referral_source" value="platform">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">
                        <i class="fa fa-building"></i> Configuration - Patients Plateforme
                    </h4>
                </div>

                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i>
                        Les pourcentages doivent totaliser <strong>100%</strong>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Diététicien (%)</label>
                                <input type="number" name="dietitian_percentage" class="form-control percentage-input" value="<?php echo $current_settings['platform'] ? $current_settings['platform']->dietitian_percentage : 60; ?>" min="0" max="100" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Plateforme (%)</label>
                                <input type="number" name="platform_percentage" class="form-control percentage-input" value="<?php echo $current_settings['platform'] ? $current_settings['platform']->platform_percentage : 40; ?>" min="0" max="100" step="0.01" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Effectif à partir du</label>
                        <input type="date" name="effective_from" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                        <small class="text-muted">Les anciens taux seront automatiquement désactivés</small>
                    </div>

                    <div class="form-group">
                        <label>Notes (optionnel)</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Raison du changement..."></textarea>
                    </div>

                    <div class="alert alert-warning percentage-warning" style="display: none;">
                        <i class="fa fa-exclamation-triangle"></i>
                        La somme des pourcentages doit être égale à 100%
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Update Dietitian Commission -->
<div class="modal fade" id="modal-update-dietitian" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?php echo admin_url('dietetic/commissions/update'); ?>" method="POST">
                <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                <input type="hidden" name="referral_source" value="dietitian">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">
                        <i class="fa fa-user-md"></i> Configuration - Patients Diététicien
                    </h4>
                </div>

                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i>
                        Les pourcentages doivent totaliser <strong>100%</strong>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Diététicien (%)</label>
                                <input type="number" name="dietitian_percentage" class="form-control percentage-input" value="<?php echo $current_settings['dietitian'] ? $current_settings['dietitian']->dietitian_percentage : 80; ?>" min="0" max="100" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Plateforme (%)</label>
                                <input type="number" name="platform_percentage" class="form-control percentage-input" value="<?php echo $current_settings['dietitian'] ? $current_settings['dietitian']->platform_percentage : 20; ?>" min="0" max="100" step="0.01" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Effectif à partir du</label>
                        <input type="date" name="effective_from" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                        <small class="text-muted">Les anciens taux seront automatiquement désactivés</small>
                    </div>

                    <div class="form-group">
                        <label>Notes (optionnel)</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Raison du changement..."></textarea>
                    </div>

                    <div class="alert alert-warning percentage-warning" style="display: none;">
                        <i class="fa fa-exclamation-triangle"></i>
                        La somme des pourcentages doit être égale à 100%
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-save"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
$(document).ready(function() {
    // Validate percentages
    $('.percentage-input').on('input', function() {
        var $form = $(this).closest('form');
        var $inputs = $form.find('.percentage-input');
        var $warning = $form.find('.percentage-warning');

        var total = 0;
        $inputs.each(function() {
            total += parseFloat($(this).val()) || 0;
        });

        if (Math.abs(total - 100) > 0.01) {
            $warning.show();
            $form.find('button[type="submit"]').prop('disabled', true);
        } else {
            $warning.hide();
            $form.find('button[type="submit"]').prop('disabled', false);
        }
    });

    // Calculator
    $('#btn-calculate').on('click', function() {
        var amount = parseFloat($('#calc-amount').val()) || 0;
        var source = $('#calc-source').val();

        if (amount <= 0) {
            alert('Veuillez entrer un montant valide');
            return;
        }

        var dietitianPercentage, platformPercentage;

        <?php if ($current_settings['platform'] && $current_settings['dietitian']): ?>
        if (source === 'platform') {
            dietitianPercentage = <?php echo $current_settings['platform']->dietitian_percentage; ?>;
            platformPercentage = <?php echo $current_settings['platform']->platform_percentage; ?>;
        } else {
            dietitianPercentage = <?php echo $current_settings['dietitian']->dietitian_percentage; ?>;
            platformPercentage = <?php echo $current_settings['dietitian']->platform_percentage; ?>;
        }

        var dietitianShare = amount * (dietitianPercentage / 100);
        var platformShare = amount * (platformPercentage / 100);

        var html = '<div class="preview-row">' +
            '<span>Montant Total:</span>' +
            '<span><strong>' + formatCurrency(amount) + ' FCFA</strong></span>' +
            '</div>' +
            '<div class="preview-row" style="color: #27ae60;">' +
            '<span>Part Diététicien (' + dietitianPercentage + '%):</span>' +
            '<span><strong>' + formatCurrency(dietitianShare) + ' FCFA</strong></span>' +
            '</div>' +
            '<div class="preview-row" style="color: #e67e22;">' +
            '<span>Part Plateforme (' + platformPercentage + '%):</span>' +
            '<span><strong>' + formatCurrency(platformShare) + ' FCFA</strong></span>' +
            '</div>';

        $('#calc-preview').html(html).addClass('show');
        <?php else: ?>
        alert('Aucune configuration de commission trouvée');
        <?php endif; ?>
    });

    function formatCurrency(value) {
        return new Intl.NumberFormat('fr-FR', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(value);
    }
});
</script>
