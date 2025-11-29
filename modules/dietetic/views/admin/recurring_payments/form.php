<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <i class="fa fa-refresh"></i>
                            <?php echo $title; ?>
                        </h4>
                        <hr class="hr-panel-heading" />

                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i>
                            Cette fonctionnalité sera disponible prochainement.
                            <br>
                            Pour créer un paiement récurrent, veuillez :
                            <ol>
                                <li>Créer un abonnement pour le patient</li>
                                <li>Activer le paiement récurrent depuis la page de l'abonnement</li>
                            </ol>
                        </div>

                        <a href="<?php echo admin_url('dietetic/recurring_payments'); ?>" class="btn btn-default">
                            <i class="fa fa-arrow-left"></i> <?php echo _l('back'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
