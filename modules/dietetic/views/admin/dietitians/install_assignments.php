<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <i class="fa fa-users"></i> <?php echo $title; ?>
                        </h4>
                        <hr class="hr-panel-heading" />

                        <?php if (isset($installation_result)): ?>
                            <?php if ($installation_result['success']): ?>
                                <div class="alert alert-success">
                                    <h4><i class="fa fa-check-circle"></i> Installation réussie !</h4>
                                    <p>Le système multi-diététiciens a été installé avec succès.</p>

                                    <?php if (!empty($installation_result['messages'])): ?>
                                        <ul>
                                            <?php foreach ($installation_result['messages'] as $msg): ?>
                                                <li><?php echo $msg; ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                </div>

                                <div class="alert alert-info">
                                    <h4><i class="fa fa-info-circle"></i> Prochaines étapes :</h4>
                                    <ol>
                                        <li>Les assignations existantes ont été migrées automatiquement</li>
                                        <li>Vous pouvez maintenant assigner plusieurs diététiciens à un patient</li>
                                        <li>Chaque diététicien ne voit que ses propres patients</li>
                                        <li>Seuls les admins peuvent gérer les assignations</li>
                                    </ol>
                                </div>

                                <a href="<?php echo admin_url('dietetic/patients'); ?>" class="btn btn-primary">
                                    <i class="fa fa-users"></i> Gérer les Patients
                                </a>
                            <?php else: ?>
                                <div class="alert alert-danger">
                                    <h4><i class="fa fa-exclamation-triangle"></i> Erreur lors de l'installation</h4>
                                    <?php if (!empty($installation_result['errors'])): ?>
                                        <ul>
                                            <?php foreach ($installation_result['errors'] as $error): ?>
                                                <li><?php echo $error; ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                </div>

                                <a href="<?php echo admin_url('dietetic/dietitians/install_assignments'); ?>" class="btn btn-warning">
                                    <i class="fa fa-refresh"></i> Réessayer
                                </a>
                            <?php endif; ?>

                        <?php elseif ($table_exists): ?>
                            <div class="alert alert-warning">
                                <h4><i class="fa fa-info-circle"></i> Système déjà installé</h4>
                                <p>La table <code><?php echo db_prefix(); ?>dietic_patient_dietitians</code> existe déjà dans votre base de données.</p>
                                <p>Le système multi-diététiciens est déjà opérationnel.</p>
                            </div>

                            <a href="<?php echo admin_url('dietetic/patients'); ?>" class="btn btn-primary">
                                <i class="fa fa-users"></i> Gérer les Patients
                            </a>

                        <?php else: ?>
                            <div class="alert alert-info">
                                <h4><i class="fa fa-info-circle"></i> À propos du système multi-diététiciens</h4>
                                <p>Cette installation permet à un patient d'être suivi par <strong>plusieurs diététiciens</strong> simultanément.</p>

                                <p><strong>Avantages :</strong></p>
                                <ul>
                                    <li><i class="fa fa-users text-primary"></i> <strong>Travail en équipe</strong> : Plusieurs diététiciens peuvent collaborer sur le même patient</li>
                                    <li><i class="fa fa-lock text-success"></i> <strong>Sécurité</strong> : Chaque diététicien ne voit que SES patients assignés</li>
                                    <li><i class="fa fa-user-md text-info"></i> <strong>Diététicien principal</strong> : Désignez un diététicien principal par patient</li>
                                    <li><i class="fa fa-shield text-warning"></i> <strong>Permissions</strong> : Seuls les admins gèrent les assignations</li>
                                </ul>

                                <p><strong>Fonctionnement :</strong></p>
                                <ol>
                                    <li>Les admins peuvent assigner/retirer des diététiciens aux patients</li>
                                    <li>Un diététicien ne voit que les patients qui lui sont assignés</li>
                                    <li>Les admins voient tous les patients</li>
                                    <li>Les données existantes seront migrées automatiquement</li>
                                </ol>

                                <p><strong>Impact :</strong></p>
                                <ul>
                                    <li><span class="label label-success">Compatible</span> Vos données existantes seront préservées</li>
                                    <li><span class="label label-info">Migration auto</span> Les assignations actuelles seront migrées</li>
                                    <li><span class="label label-warning">Nouveau</span> Nouvelle interface pour gérer les assignations</li>
                                </ul>
                            </div>

                            <div class="alert alert-warning">
                                <i class="fa fa-warning"></i> <strong>Important :</strong> Cette opération va créer une nouvelle table dans votre base de données. Assurez-vous d'avoir une sauvegarde récente avant de continuer.
                            </div>

                            <form method="post" action="<?php echo admin_url('dietetic/dietitians/install_assignments'); ?>">
                                <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                                <input type="hidden" name="confirm_install" value="1">

                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fa fa-download"></i> Installer le Système Multi-Diététiciens
                                </button>

                                <a href="<?php echo admin_url('dietetic'); ?>" class="btn btn-default btn-lg">
                                    <i class="fa fa-times"></i> Annuler
                                </a>
                            </form>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
