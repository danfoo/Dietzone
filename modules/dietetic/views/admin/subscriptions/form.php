<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4>
                            <i class="fa fa-<?php echo isset($subscription) ? 'edit' : 'plus-circle'; ?>"></i>
                            <?php echo isset($subscription) ? 'Modifier l\'Abonnement' : 'Nouvel Abonnement'; ?>
                        </h4>
                        <hr />

                        <form action="<?php echo isset($subscription) ? admin_url('dietetic/subscriptions/edit/' . $subscription->id) : admin_url('dietetic/subscriptions/create'); ?>" method="POST" id="subscription-form">
                            <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>

                            <!-- Patient Selection -->
                            <div class="form-group">
                                <label>Patient <span class="text-danger">*</span></label>
                                <select name="patient_id" id="patient-select" class="form-control selectpicker" data-live-search="true" required <?php echo isset($subscription) ? 'disabled' : ''; ?>>
                                    <option value="">Sélectionnez un patient</option>
                                    <?php foreach ($patients as $patient): ?>
                                        <option value="<?php echo $patient->id; ?>" <?php echo (isset($subscription) && $subscription->patient_id == $patient->id) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($patient->company); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($subscription)): ?>
                                    <input type="hidden" name="patient_id" value="<?php echo $subscription->patient_id; ?>">
                                <?php endif; ?>
                                <div id="patient-warning" class="alert alert-warning" style="display: none; margin-top: 10px;">
                                    <i class="fa fa-exclamation-triangle"></i> Ce patient a déjà un abonnement actif
                                </div>
                            </div>

                            <!-- Service Plan Selection -->
                            <div class="form-group">
                                <label>Plan de Service <span class="text-danger">*</span></label>
                                <select name="service_plan_id" id="plan-select" class="form-control" required>
                                    <option value="">Sélectionnez un plan</option>
                                    <?php foreach ($plans as $plan): ?>
                                        <option value="<?php echo $plan->id; ?>" <?php echo (isset($subscription) && $subscription->service_plan_id == $plan->id) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($plan->name_fr ?: $plan->name); ?> - <?php echo number_format($plan->price, 0, ',', ' '); ?> FCFA/<?php echo $plan->billing_cycle == 'monthly' ? 'mois' : 'an'; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Plan Details (Dynamic) -->
                            <div id="plan-details" class="panel panel-info" style="display: none;">
                                <div class="panel-heading">
                                    <h4 class="panel-title">Détails du Plan</h4>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Prix:</strong> <span id="plan-price">-</span> FCFA</p>
                                            <p><strong>Durée:</strong> <span id="plan-duration">-</span></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Facturation:</strong> <span id="plan-billing">-</span></p>
                                            <p><strong>Essai gratuit:</strong> <span id="plan-trial">-</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Dietitian -->
                            <div class="form-group">
                                <label>Diététicien Assigné <span class="text-danger">*</span></label>
                                <select name="dietitian_id" class="form-control selectpicker" data-live-search="true" required>
                                    <option value="">Sélectionnez un diététicien</option>
                                    <?php foreach ($staff as $member): ?>
                                        <option value="<?php echo $member['staffid']; ?>" <?php echo (isset($subscription) && $subscription->dietitian_id == $member['staffid']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($member['firstname'] . ' ' . $member['lastname']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Referral Source -->
                            <div class="form-group">
                                <label>Source de Référence <span class="text-danger">*</span></label>
                                <div>
                                    <label class="radio-inline">
                                        <input type="radio" name="referral_source" value="platform" <?php echo (!isset($subscription) || $subscription->referral_source == 'platform') ? 'checked' : ''; ?> required>
                                        <i class="fa fa-building"></i> Plateforme (60/40)
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="referral_source" value="dietitian" <?php echo (isset($subscription) && $subscription->referral_source == 'dietitian') ? 'checked' : ''; ?> required>
                                        <i class="fa fa-user-md"></i> Diététicien (80/20)
                                    </label>
                                </div>
                                <small class="text-muted">
                                    Détermine le partage de revenus entre le diététicien et la plateforme
                                </small>
                            </div>

                            <!-- Start Date -->
                            <?php if (!isset($subscription)): ?>
                                <div class="form-group">
                                    <label>Date de Début</label>
                                    <input type="date" name="start_date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                                    <small class="text-muted">Laisser vide pour commencer aujourd'hui</small>
                                </div>
                            <?php endif; ?>

                            <!-- Tax Rate -->
                            <div class="form-group">
                                <label>Taux de TVA (%)</label>
                                <input type="number" name="tax_rate" class="form-control" value="<?php echo isset($subscription) ? $subscription->tax_rate : '18'; ?>" min="0" max="100" step="0.01">
                            </div>

                            <!-- Notes -->
                            <div class="form-group">
                                <label>Notes (optionnel)</label>
                                <textarea name="notes" class="form-control" rows="3"><?php echo isset($subscription) ? htmlspecialchars($subscription->notes) : ''; ?></textarea>
                            </div>

                            <hr />

                            <div class="text-right">
                                <a href="<?php echo admin_url('dietetic/subscriptions'); ?>" class="btn btn-default">
                                    <i class="fa fa-times"></i> Annuler
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> <?php echo isset($subscription) ? 'Mettre à jour' : 'Créer l\'Abonnement'; ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4><i class="fa fa-info-circle"></i> Information</h4>
                        <ul class="list-unstyled">
                            <li style="margin-bottom: 10px;">
                                <i class="fa fa-check text-success"></i>
                                Un patient ne peut avoir qu'<strong>un seul abonnement actif</strong> à la fois
                            </li>
                            <li style="margin-bottom: 10px;">
                                <i class="fa fa-gift text-warning"></i>
                                Si le plan inclut une <strong>période d'essai</strong>, elle commence automatiquement
                            </li>
                            <li style="margin-bottom: 10px;">
                                <i class="fa fa-calendar text-info"></i>
                                Les dates de fin et de facturation sont <strong>calculées automatiquement</strong>
                            </li>
                            <li style="margin-bottom: 10px;">
                                <i class="fa fa-percent text-primary"></i>
                                Le partage de revenus dépend de la <strong>source de référence</strong>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
$(document).ready(function() {
    // Initialize selectpicker if available
    if ($.fn.selectpicker) {
        $('.selectpicker').selectpicker();
    }

    // Load plan details on selection
    $('#plan-select').on('change', function() {
        var planId = $(this).val();

        if (!planId) {
            $('#plan-details').hide();
            return;
        }

        $.ajax({
            url: '<?php echo admin_url('dietetic/subscriptions/get_plan_details/'); ?>' + planId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var plan = response.plan;

                    $('#plan-price').text(formatNumber(plan.price));
                    $('#plan-duration').text(plan.duration_value + ' ' + plan.duration_unit);
                    $('#plan-billing').text(plan.billing_cycle == 'monthly' ? 'Mensuel' : 'Annuel');
                    $('#plan-trial').text(plan.trial_days > 0 ? plan.trial_days + ' jours' : 'Aucun');

                    $('#plan-details').fadeIn();
                }
            }
        });
    });

    // Check if patient has active subscription
    $('#patient-select').on('change', function() {
        var patientId = $(this).val();

        if (!patientId) {
            $('#patient-warning').hide();
            return;
        }

        $.ajax({
            url: '<?php echo admin_url('dietetic/subscriptions/check_patient_subscription/'); ?>' + patientId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.has_active) {
                    $('#patient-warning').fadeIn();
                } else {
                    $('#patient-warning').hide();
                }
            }
        });
    });

    // Trigger plan details load if editing
    <?php if (isset($subscription)): ?>
        $('#plan-select').trigger('change');
    <?php endif; ?>

    function formatNumber(num) {
        return new Intl.NumberFormat('fr-FR').format(num);
    }
});
</script>
