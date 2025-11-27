<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <i class="fa fa-<?php echo isset($plan) ? 'edit' : 'plus-circle'; ?>"></i>
                            <?php echo isset($plan) ? 'Modifier le Plan de Service' : 'Nouveau Plan de Service'; ?>
                        </h4>
                        <hr />

                        <form action="<?php echo isset($plan) ? admin_url('dietetic/service_plans/edit/' . $plan->id) : admin_url('dietetic/service_plans/create'); ?>" method="POST">
                            <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>

                            <!-- Basic Information -->
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h4 class="panel-title">Informations de Base</h4>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="name">Nom du Plan (English) <span class="text-danger">*</span></label>
                                                <input type="text" name="name" id="name" class="form-control" value="<?php echo isset($plan) ? htmlspecialchars($plan->name) : ''; ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="name_fr">Nom du Plan (Français)</label>
                                                <input type="text" name="name_fr" id="name_fr" class="form-control" value="<?php echo isset($plan) ? htmlspecialchars($plan->name_fr) : ''; ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="description">Description (English)</label>
                                                <textarea name="description" id="description" class="form-control" rows="3"><?php echo isset($plan) ? htmlspecialchars($plan->description) : ''; ?></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="description_fr">Description (Français)</label>
                                                <textarea name="description_fr" id="description_fr" class="form-control" rows="3"><?php echo isset($plan) ? htmlspecialchars($plan->description_fr) : ''; ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Pricing & Duration -->
                            <div class="panel panel-success">
                                <div class="panel-heading">
                                    <h4 class="panel-title">Tarification & Durée</h4>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="price">Prix (FCFA) <span class="text-danger">*</span></label>
                                                <input type="number" name="price" id="price" class="form-control" value="<?php echo isset($plan) ? $plan->price : ''; ?>" min="0" step="100" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="billing_cycle">Cycle de Facturation <span class="text-danger">*</span></label>
                                                <select name="billing_cycle" id="billing_cycle" class="form-control" required>
                                                    <option value="monthly" <?php echo (isset($plan) && $plan->billing_cycle == 'monthly') ? 'selected' : ''; ?>>Mensuel</option>
                                                    <option value="yearly" <?php echo (isset($plan) && $plan->billing_cycle == 'yearly') ? 'selected' : ''; ?>>Annuel</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="duration_value">Durée <span class="text-danger">*</span></label>
                                                <input type="number" name="duration_value" id="duration_value" class="form-control" value="<?php echo isset($plan) ? $plan->duration_value : '1'; ?>" min="1" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="duration_unit">Unité <span class="text-danger">*</span></label>
                                                <select name="duration_unit" id="duration_unit" class="form-control" required>
                                                    <option value="day" <?php echo (isset($plan) && $plan->duration_unit == 'day') ? 'selected' : ''; ?>>Jour(s)</option>
                                                    <option value="week" <?php echo (isset($plan) && $plan->duration_unit == 'week') ? 'selected' : ''; ?>>Semaine(s)</option>
                                                    <option value="month" <?php echo (isset($plan) && $plan->duration_unit == 'month') ? 'selected' : ''; ?>>Mois</option>
                                                    <option value="year" <?php echo (isset($plan) && $plan->duration_unit == 'year') ? 'selected' : ''; ?>>Année(s)</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="trial_days">Période d'essai (jours)</label>
                                                <input type="number" name="trial_days" id="trial_days" class="form-control" value="<?php echo isset($plan) ? $plan->trial_days : '0'; ?>" min="0">
                                                <small class="text-muted">0 = pas d'essai gratuit</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Features -->
                            <div class="panel panel-info">
                                <div class="panel-heading">
                                    <h4 class="panel-title">Fonctionnalités Incluses</h4>
                                </div>
                                <div class="panel-body">
                                    <div id="features-container">
                                        <?php
                                        if (isset($features) && is_array($features) && count($features) > 0) {
                                            foreach ($features as $index => $feature) {
                                                echo '<div class="feature-row" style="margin-bottom: 10px;">
                                                    <div class="row">
                                                        <div class="col-md-10">
                                                            <input type="text" name="features[]" class="form-control" value="' . htmlspecialchars($feature) . '" placeholder="Ex: Consultations illimitées">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <button type="button" class="btn btn-danger btn-block btn-remove-feature">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>';
                                            }
                                        }
                                        ?>
                                    </div>

                                    <button type="button" id="btn-add-feature" class="btn btn-success btn-sm">
                                        <i class="fa fa-plus-circle"></i> Ajouter une fonctionnalité
                                    </button>
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">Statut</h4>
                                </div>
                                <div class="panel-body">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="is_active" value="1" <?php echo (isset($plan) && $plan->is_active) || !isset($plan) ? 'checked' : ''; ?>>
                                            Plan actif (visible pour les nouveaux abonnements)
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="text-right">
                                <a href="<?php echo admin_url('dietetic/service_plans'); ?>" class="btn btn-default">
                                    <i class="fa fa-times"></i> Annuler
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> <?php echo isset($plan) ? 'Mettre à jour' : 'Créer'; ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
$(document).ready(function() {
    // Add feature
    $('#btn-add-feature').on('click', function() {
        var html = '<div class="feature-row" style="margin-bottom: 10px;">' +
            '<div class="row">' +
            '<div class="col-md-10">' +
            '<input type="text" name="features[]" class="form-control" placeholder="Ex: Consultations illimitées">' +
            '</div>' +
            '<div class="col-md-2">' +
            '<button type="button" class="btn btn-danger btn-block btn-remove-feature">' +
            '<i class="fa fa-trash"></i>' +
            '</button>' +
            '</div>' +
            '</div>' +
            '</div>';

        $('#features-container').append(html);
    });

    // Remove feature
    $(document).on('click', '.btn-remove-feature', function() {
        $(this).closest('.feature-row').remove();
    });

    // Add at least one feature row if none exist
    if ($('#features-container .feature-row').length === 0) {
        $('#btn-add-feature').click();
    }
});
</script>
