<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <i class="fa fa-database"></i> Migration: Système de Facturation des Programmes
                        </h3>
                    </div>
                    <div class="panel-body">

                        <?php if (isset($migration_status)): ?>

                            <?php if ($migration_status === 'already_applied'): ?>
                                <!-- Migration déjà appliquée -->
                                <div class="alert alert-info">
                                    <h4><i class="fa fa-check-circle"></i> Migration déjà appliquée</h4>
                                    <p>Les colonnes de facturation existent déjà dans votre base de données.</p>

                                    <h5>Colonnes existantes dans tbldietic_programs :</h5>
                                    <ul>
                                        <?php foreach ($existing_columns as $col): ?>
                                            <li><code><?php echo $col; ?></code></li>
                                        <?php endforeach; ?>
                                    </ul>

                                    <hr>
                                    <a href="<?php echo admin_url('dietetic/programs'); ?>" class="btn btn-primary">
                                        <i class="fa fa-arrow-left"></i> Retour aux Programmes
                                    </a>
                                </div>

                            <?php elseif ($migration_status === 'success'): ?>
                                <!-- Migration réussie -->
                                <div class="alert alert-success">
                                    <h4><i class="fa fa-check-circle"></i> Migration exécutée avec succès !</h4>
                                    <p>Les colonnes de facturation ont été ajoutées à votre base de données.</p>

                                    <h5>Colonnes ajoutées :</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Table tblitems :</strong>
                                            <ul>
                                                <li>service_discount_6_months</li>
                                                <li>service_discount_12_months</li>
                                                <li>service_type</li>
                                                <li>service_required_specialty</li>
                                                <li>service_is_recurring</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Table tbldietic_programs :</strong>
                                            <ul>
                                                <li>service_id</li>
                                                <li>duration_months</li>
                                                <li>payment_mode</li>
                                                <li>monthly_price</li>
                                                <li>total_price</li>
                                                <li>discount_applied</li>
                                                <li>billing_status</li>
                                                <li>next_billing_date</li>
                                                <li>last_billing_date</li>
                                                <li>grace_period_end</li>
                                                <li>total_invoices_expected</li>
                                                <li>total_invoices_paid</li>
                                            </ul>
                                        </div>
                                    </div>

                                    <hr>
                                    <h5>Prochaines étapes :</h5>
                                    <ol>
                                        <li>Créer vos services dans <strong>Setup → Items</strong></li>
                                        <li>Configurer les réductions (6 mois, 12 mois)</li>
                                        <li>Créer un programme avec facturation automatique</li>
                                    </ol>

                                    <hr>
                                    <a href="<?php echo admin_url('dietetic/programs/create'); ?>" class="btn btn-success">
                                        <i class="fa fa-plus"></i> Créer un Programme
                                    </a>
                                    <a href="<?php echo admin_url('items'); ?>" class="btn btn-info">
                                        <i class="fa fa-cog"></i> Gérer les Services
                                    </a>
                                </div>

                            <?php elseif ($migration_status === 'error'): ?>
                                <!-- Erreur -->
                                <div class="alert alert-danger">
                                    <h4><i class="fa fa-exclamation-triangle"></i> Erreur lors de la migration</h4>
                                    <p><strong>Message d'erreur :</strong></p>
                                    <pre><?php echo htmlspecialchars($error_message); ?></pre>

                                    <hr>
                                    <p>Veuillez contacter le support ou exécuter la migration manuellement via phpMyAdmin.</p>
                                    <a href="<?php echo admin_url('dietetic/run_billing_migration'); ?>" class="btn btn-warning">
                                        <i class="fa fa-refresh"></i> Réessayer
                                    </a>
                                </div>
                            <?php endif; ?>

                        <?php else: ?>
                            <!-- Formulaire de confirmation -->
                            <div class="alert alert-warning">
                                <h4><i class="fa fa-info-circle"></i> À propos de cette migration</h4>
                                <p>Cette migration va ajouter les champs nécessaires pour le <strong>système de facturation automatique des programmes</strong>.</p>
                            </div>

                            <h4>Modifications qui seront appliquées :</h4>

                            <div class="row" style="margin-top: 20px;">
                                <div class="col-md-6">
                                    <div class="panel panel-info">
                                        <div class="panel-heading">
                                            <strong>Table: tblitems</strong>
                                        </div>
                                        <div class="panel-body">
                                            <p>Ajout de 6 colonnes pour les services :</p>
                                            <ul style="font-size: 12px;">
                                                <li><code>service_duration_months</code></li>
                                                <li><code>service_type</code></li>
                                                <li><code>service_discount_6_months</code></li>
                                                <li><code>service_discount_12_months</code></li>
                                                <li><code>service_required_specialty</code></li>
                                                <li><code>service_is_recurring</code></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="panel panel-info">
                                        <div class="panel-heading">
                                            <strong>Table: tbldietic_programs</strong>
                                        </div>
                                        <div class="panel-body">
                                            <p>Ajout de 13 colonnes pour la facturation :</p>
                                            <ul style="font-size: 12px;">
                                                <li><code>service_id</code></li>
                                                <li><code>duration_months</code></li>
                                                <li><code>payment_mode</code></li>
                                                <li><code>monthly_price</code></li>
                                                <li><code>total_price</code></li>
                                                <li><code>discount_applied</code></li>
                                                <li><code>billing_status</code></li>
                                                <li><code>next_billing_date</code></li>
                                                <li><code>last_billing_date</code></li>
                                                <li><code>grace_period_end</code></li>
                                                <li><code>total_invoices_expected</code></li>
                                                <li><code>total_invoices_paid</code></li>
                                                <li>+ 3 index pour les performances</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-info" style="margin-top: 20px;">
                                <h5><i class="fa fa-shield"></i> Sécurité</h5>
                                <ul>
                                    <li><i class="fa fa-check text-success"></i> Aucune donnée existante ne sera modifiée</li>
                                    <li><i class="fa fa-check text-success"></i> Seulement des ajouts de colonnes vides</li>
                                    <li><i class="fa fa-check text-success"></i> Peut être annulé si nécessaire</li>
                                </ul>
                            </div>

                            <hr>

                            <form method="POST" action="<?php echo admin_url('dietetic/run_billing_migration'); ?>">
                                <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                                <input type="hidden" name="confirm_migration" value="1">

                                <div class="text-center">
                                    <button type="submit" class="btn btn-lg btn-primary">
                                        <i class="fa fa-play"></i> Exécuter la Migration
                                    </button>
                                    <a href="<?php echo admin_url('dietetic/programs'); ?>" class="btn btn-lg btn-default">
                                        <i class="fa fa-times"></i> Annuler
                                    </a>
                                </div>
                            </form>

                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
