<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>

                        <h4 class="no-margin">
                            <i class="fa fa-cog"></i> <?php echo $title; ?>
                        </h4>

                        <hr class="hr-panel-heading">

                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i>
                            Ce script vérifie et installe automatiquement les tables nécessaires au système de notifications.
                        </div>

                        <?php echo $output; ?>

                        <hr>

                        <div class="text-center mtop20">
                            <a href="<?php echo admin_url('dietetic/setup/check_notifications'); ?>" class="btn btn-primary">
                                <i class="fa fa-check-circle"></i> Vérifier l'État du Système
                            </a>

                            <a href="<?php echo admin_url('dietetic/dashboard'); ?>" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Retour au Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
