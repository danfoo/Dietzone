<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <!-- Header -->
                        <div class="clearfix">
                            <div class="pull-left">
                                <h4 class="tw-mt-0 tw-font-bold tw-text-neutral-700">
                                    <i class="fa fa-user-md tw-mr-1"></i>
                                    Migration: Système de Profil Diététicien
                                </h4>
                                <p class="text-muted">
                                    Ajout des profils diététiciens avec codes de référence et gestion des spécialités
                                </p>
                            </div>
                        </div>

                        <hr class="hr-panel-heading" />

                        <!-- Description de la Migration -->
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i>
                            <strong>Cette migration ajoute :</strong>
                            <ul class="tw-mt-2 tw-mb-0">
                                <li>Colonnes de profil à la table <code>tblstaff</code> (codes référence, spécialités, bio, etc.)</li>
                                <li>Table <code>tbldietic_specialties</code> avec 12 spécialités prédéfinies</li>
                                <li>Table <code>tbldietic_referrals</code> pour tracker les références patients</li>
                                <li>Vue SQL pour statistiques des diététiciens</li>
                            </ul>
                        </div>

                        <!-- État de la Migration -->
                        <div class="row">
                            <div class="col-md-12">
                                <?php if ($migration_applied): ?>
                                    <!-- Migration déjà appliquée -->
                                    <div class="alert alert-success">
                                        <i class="fa fa-check-circle"></i>
                                        <strong>Migration déjà appliquée</strong>
                                        <p class="tw-mb-0">Le système de profil diététicien est déjà en place.</p>
                                    </div>

                                    <!-- Statistiques -->
                                    <div class="row mtop20">
                                        <div class="col-md-3 col-sm-6">
                                            <div class="tw-mb-3 md:tw-mb-0">
                                                <div class="tw-flex tw-items-center tw-justify-between tw-p-4 tw-bg-white tw-border tw-border-solid tw-border-neutral-300 tw-rounded-lg">
                                                    <div>
                                                        <p class="tw-mb-0 tw-text-neutral-600 tw-font-medium">Spécialités</p>
                                                        <h3 class="tw-mb-0 tw-mt-1 tw-font-semibold tw-text-neutral-800">
                                                            <?php echo isset($stats['total_specialties']) ? $stats['total_specialties'] : 0; ?>
                                                        </h3>
                                                    </div>
                                                    <div class="tw-ml-3">
                                                        <i class="fa fa-stethoscope fa-2x text-success"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-3 col-sm-6">
                                            <div class="tw-mb-3 md:tw-mb-0">
                                                <div class="tw-flex tw-items-center tw-justify-between tw-p-4 tw-bg-white tw-border tw-border-solid tw-border-neutral-300 tw-rounded-lg">
                                                    <div>
                                                        <p class="tw-mb-0 tw-text-neutral-600 tw-font-medium">Codes Générés</p>
                                                        <h3 class="tw-mb-0 tw-mt-1 tw-font-semibold tw-text-neutral-800">
                                                            <?php echo isset($stats['dietitians_with_codes']) ? $stats['dietitians_with_codes'] : 0; ?>
                                                        </h3>
                                                    </div>
                                                    <div class="tw-ml-3">
                                                        <i class="fa fa-qrcode fa-2x text-info"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-3 col-sm-6">
                                            <div class="tw-mb-3 md:tw-mb-0">
                                                <div class="tw-flex tw-items-center tw-justify-between tw-p-4 tw-bg-white tw-border tw-border-solid tw-border-neutral-300 tw-rounded-lg">
                                                    <div>
                                                        <p class="tw-mb-0 tw-text-neutral-600 tw-font-medium">Références</p>
                                                        <h3 class="tw-mb-0 tw-mt-1 tw-font-semibold tw-text-neutral-800">
                                                            <?php echo isset($stats['total_referrals']) ? $stats['total_referrals'] : 0; ?>
                                                        </h3>
                                                    </div>
                                                    <div class="tw-ml-3">
                                                        <i class="fa fa-user-plus fa-2x text-warning"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-3 col-sm-6">
                                            <div class="tw-mb-3 md:tw-mb-0">
                                                <div class="tw-flex tw-items-center tw-justify-between tw-p-4 tw-bg-white tw-border tw-border-solid tw-border-neutral-300 tw-rounded-lg">
                                                    <div>
                                                        <p class="tw-mb-0 tw-text-neutral-600 tw-font-medium">Profils Complets</p>
                                                        <h3 class="tw-mb-0 tw-mt-1 tw-font-semibold tw-text-neutral-800">
                                                            <?php echo isset($stats['profiles_completed']) ? $stats['profiles_completed'] : 0; ?>
                                                        </h3>
                                                    </div>
                                                    <div class="tw-ml-3">
                                                        <i class="fa fa-check-circle fa-2x text-success"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Liste des Spécialités -->
                                    <?php if (isset($specialties) && !empty($specialties)): ?>
                                        <div class="mtop30">
                                            <h4 class="tw-font-semibold">
                                                <i class="fa fa-list"></i> Spécialités Disponibles
                                            </h4>
                                            <div class="row">
                                                <?php foreach ($specialties as $specialty): ?>
                                                    <div class="col-md-3 col-sm-4 col-xs-6">
                                                        <div class="tw-mb-3">
                                                            <div class="tw-flex tw-items-center tw-p-3 tw-bg-white tw-border tw-border-solid tw-border-neutral-200 tw-rounded-lg">
                                                                <i class="fa <?php echo $specialty['icon']; ?> fa-2x tw-mr-3" style="color: <?php echo $specialty['color']; ?>;"></i>
                                                                <div>
                                                                    <p class="tw-mb-0 tw-font-medium tw-text-sm">
                                                                        <?php echo htmlspecialchars($specialty['name_fr']); ?>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Dernières Références -->
                                    <?php if (isset($recent_referrals) && !empty($recent_referrals)): ?>
                                        <div class="mtop30">
                                            <h4 class="tw-font-semibold">
                                                <i class="fa fa-users"></i> Dernières Références
                                            </h4>
                                            <div class="table-responsive">
                                                <table class="table table-striped dt-table">
                                                    <thead>
                                                        <tr>
                                                            <th>ID</th>
                                                            <th>Diététicien</th>
                                                            <th>Code Référence</th>
                                                            <th>Patient ID</th>
                                                            <th>Source</th>
                                                            <th>Date</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($recent_referrals as $ref): ?>
                                                            <tr>
                                                                <td><?php echo $ref['id']; ?></td>
                                                                <td>
                                                                    <i class="fa fa-user-md text-info"></i>
                                                                    <?php echo htmlspecialchars($ref['dietitian_name']); ?>
                                                                </td>
                                                                <td>
                                                                    <span class="label label-primary">
                                                                        <?php echo htmlspecialchars($ref['referral_code']); ?>
                                                                    </span>
                                                                </td>
                                                                <td><?php echo $ref['patient_id']; ?></td>
                                                                <td>
                                                                    <span class="label label-default">
                                                                        <?php echo htmlspecialchars($ref['source']); ?>
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    <i class="fa fa-calendar"></i>
                                                                    <?php echo date('d/m/Y H:i', strtotime($ref['referred_at'])); ?>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                <?php else: ?>
                                    <!-- Migration non appliquée -->
                                    <div class="alert alert-warning">
                                        <i class="fa fa-exclamation-triangle"></i>
                                        <strong>Migration non appliquée</strong>
                                        <p>Le système de profil diététicien n'est pas encore installé.</p>
                                    </div>

                                    <!-- Formulaire d'application -->
                                    <div class="mtop20">
                                        <h4 class="tw-font-semibold">Appliquer la Migration</h4>
                                        <p class="text-muted">
                                            Cette opération va créer les tables et colonnes nécessaires pour le système de profil diététicien.
                                            Les données existantes ne seront pas affectées.
                                        </p>

                                        <?php echo form_open(admin_url('dietetic/apply_dietitian_profile_migration'), ['id' => 'migration-form']); ?>
                                            <div class="form-group">
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" name="confirm" id="confirm-checkbox" required>
                                                        Je confirme vouloir appliquer cette migration
                                                    </label>
                                                </div>
                                            </div>

                                            <button type="submit" class="btn btn-primary" id="apply-btn" disabled>
                                                <i class="fa fa-database"></i>
                                                Appliquer la Migration
                                            </button>

                                            <a href="<?php echo admin_url('dietetic/migrations'); ?>" class="btn btn-default">
                                                <i class="fa fa-arrow-left"></i>
                                                Retour
                                            </a>
                                        <?php echo form_close(); ?>
                                    </div>

                                    <!-- Détails Techniques -->
                                    <div class="mtop30">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a data-toggle="collapse" href="#technical-details">
                                                        <i class="fa fa-code"></i> Détails Techniques
                                                        <i class="fa fa-chevron-down pull-right"></i>
                                                    </a>
                                                </h4>
                                            </div>
                                            <div id="technical-details" class="panel-collapse collapse">
                                                <div class="panel-body">
                                                    <h5><strong>Modifications à la table tblstaff :</strong></h5>
                                                    <ul>
                                                        <li><code>dietitian_referral_code</code> VARCHAR(20) - Code unique de référence</li>
                                                        <li><code>dietitian_specialties</code> TEXT - Spécialités (JSON)</li>
                                                        <li><code>dietitian_years_experience</code> INT(3) - Années d'expérience</li>
                                                        <li><code>dietitian_bio</code> TEXT - Biographie professionnelle</li>
                                                        <li><code>dietitian_languages</code> VARCHAR(255) - Langues parlées</li>
                                                        <li><code>dietitian_certifications</code> TEXT - Certifications (JSON)</li>
                                                        <li><code>dietitian_profile_updated_at</code> DATETIME - Date mise à jour profil</li>
                                                    </ul>

                                                    <h5 class="mtop20"><strong>Tables créées :</strong></h5>
                                                    <ul>
                                                        <li><code>tbldietic_specialties</code> - Catalogue des spécialités</li>
                                                        <li><code>tbldietic_referrals</code> - Tracking des références patients</li>
                                                    </ul>

                                                    <h5 class="mtop20"><strong>Vue SQL créée :</strong></h5>
                                                    <ul>
                                                        <li><code>dietitian_stats_view</code> - Statistiques agrégées par diététicien</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkbox = document.getElementById('confirm-checkbox');
    const applyBtn = document.getElementById('apply-btn');

    if (checkbox && applyBtn) {
        checkbox.addEventListener('change', function() {
            applyBtn.disabled = !this.checked;
        });
    }

    const form = document.getElementById('migration-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!confirm('Êtes-vous sûr de vouloir appliquer cette migration ? Cette opération modifiera la structure de la base de données.')) {
                e.preventDefault();
                return false;
            }

            applyBtn.disabled = true;
            applyBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Application en cours...';
        });
    }
});
</script>

<?php init_tail(); ?>
