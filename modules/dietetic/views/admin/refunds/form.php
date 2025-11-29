<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <i class="fa fa-undo"></i>
                            <?php echo $title; ?>
                        </h4>
                        <hr class="hr-panel-heading" />

                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i>
                            Cette fonctionnalité sera disponible prochainement.
                            <br>
                            Pour initier un remboursement, veuillez :
                            <ol>
                                <li>Accéder à la facture concernée</li>
                                <li>Cliquer sur "Initier un remboursement"</li>
                                <li>Suivre les instructions</li>
                            </ol>
                        </div>

                        <a href="<?php echo admin_url('dietetic/refunds'); ?>" class="btn btn-default">
                            <i class="fa fa-arrow-left"></i> <?php echo _l('back'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
