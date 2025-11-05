<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <i class="fa fa-star"></i> <?php echo $title; ?>
                        </h4>
                        <hr class="hr-panel-heading" />

                        <?php if (isset($installation_result)): ?>
                            <?php if ($installation_result['success']): ?>
                                <div class="alert alert-success">
                                    <h4><i class="fa fa-check-circle"></i> Installation réussie !</h4>
                                    <p>Le système de notation a été installé avec succès.</p>

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
                                        <li>Accédez à la liste des diététiciens pour voir leurs statistiques</li>
                                        <li>Consultez les profils détaillés des diététiciens</li>
                                        <li>Les patients peuvent maintenant noter leurs diététiciens</li>
                                    </ol>
                                </div>

                                <a href="<?php echo admin_url('dietetic/dietitians'); ?>" class="btn btn-primary">
                                    <i class="fa fa-users"></i> Voir les Diététiciens
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

                                <a href="<?php echo admin_url('dietetic/dietitians/install'); ?>" class="btn btn-warning">
                                    <i class="fa fa-refresh"></i> Réessayer
                                </a>
                            <?php endif; ?>

                        <?php elseif ($table_exists): ?>
                            <div class="alert alert-warning">
                                <h4><i class="fa fa-info-circle"></i> Système déjà installé</h4>
                                <p>La table <code><?php echo db_prefix(); ?>dietic_ratings</code> existe déjà dans votre base de données.</p>
                                <p>Le système de notation est déjà opérationnel.</p>
                            </div>

                            <a href="<?php echo admin_url('dietetic/dietitians'); ?>" class="btn btn-primary">
                                <i class="fa fa-users"></i> Voir les Diététiciens
                            </a>

                        <?php else: ?>
                            <div class="alert alert-info">
                                <h4><i class="fa fa-info-circle"></i> À propos du système de notation</h4>
                                <p>Cette installation va créer la table nécessaire pour permettre aux patients de noter leurs diététiciens.</p>

                                <p><strong>Fonctionnalités incluses :</strong></p>
                                <ul>
                                    <li><i class="fa fa-star text-warning"></i> Notation sur 5 étoiles avec 5 critères différents</li>
                                    <li><i class="fa fa-comment"></i> Commentaires écrits par les patients</li>
                                    <li><i class="fa fa-user"></i> Affichage du nom des patients ayant noté</li>
                                    <li><i class="fa fa-line-chart"></i> Calcul automatique des moyennes</li>
                                    <li><i class="fa fa-eye"></i> Gestion de la visibilité des notes</li>
                                    <li><i class="fa fa-edit"></i> Possibilité de modifier sa note</li>
                                </ul>

                                <p><strong>Critères de notation :</strong></p>
                                <ol>
                                    <li>Professionnalisme</li>
                                    <li>Qualité d'écoute</li>
                                    <li>Qualité des conseils</li>
                                    <li>Résultats obtenus</li>
                                    <li>Disponibilité</li>
                                </ol>
                            </div>

                            <div class="alert alert-warning">
                                <i class="fa fa-warning"></i> <strong>Important :</strong> Cette opération va créer une nouvelle table dans votre base de données. Assurez-vous d'avoir une sauvegarde récente avant de continuer.
                            </div>

                            <form method="post" action="<?php echo admin_url('dietetic/dietitians/install'); ?>">
                                <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                                <input type="hidden" name="confirm_install" value="1">

                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fa fa-download"></i> Installer le Système de Notation
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
