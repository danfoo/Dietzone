<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <i class="fa fa-database"></i> Ajouter Collation aux Enquêtes Alimentaires
                        </h3>
                    </div>
                    <div class="panel-body">
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i>
                            <strong>Information :</strong> Cette migration va ajouter les colonnes nécessaires pour gérer les collations dans les enquêtes alimentaires.
                        </div>

                        <h4>Modifications qui seront appliquées :</h4>
                        <ul class="list-unstyled" style="margin: 20px 0;">
                            <li style="margin-bottom: 10px;">
                                <i class="fa fa-check text-success"></i>
                                Ajout des colonnes <code>snack_photo</code>, <code>snack_time</code>, <code>snack_notes</code> à la table <code>dietic_food_survey_entries</code>
                            </li>
                            <li style="margin-bottom: 10px;">
                                <i class="fa fa-check text-success"></i>
                                Mise à jour de l'ENUM <code>meal_type</code> pour inclure la valeur <strong>'snack'</strong> dans la table <code>dietic_food_survey_recommendations</code>
                            </li>
                        </ul>

                        <div class="alert alert-warning">
                            <i class="fa fa-exclamation-triangle"></i>
                            <strong>Attention :</strong> Cette opération modifie la structure de la base de données. Assurez-vous d'avoir une sauvegarde récente avant de continuer.
                        </div>

                        <form method="post" action="<?php echo admin_url('dietetic/food_surveys/add_snack'); ?>">
                            <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                            <input type="hidden" name="do_migrate" value="1">

                            <div class="text-center" style="margin-top: 30px;">
                                <a href="<?php echo admin_url('dietetic/food_surveys'); ?>" class="btn btn-default">
                                    <i class="fa fa-times"></i> Annuler
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-check"></i> Exécuter la Migration
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
