<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $this->load->view('authentication/includes/head'); ?>

<div class="container">
    <div class="row mtop50">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-warning">
                <div class="panel-heading text-center">
                    <h3><i class="fa fa-exclamation-triangle"></i> <?php echo _l('access_denied'); ?></h3>
                </div>
                <div class="panel-body text-center">
                    <p class="lead"><?php echo _l('dietetic_error_no_patient'); ?></p>
                    <p><?php echo _l('You do not have a dietetic patient record. Please contact your dietitian or support team.'); ?></p>
                    <div class="mtop30">
                        <a href="<?php echo site_url(); ?>" class="btn btn-primary">
                            <i class="fa fa-home"></i> <?php echo _l('Go to Dashboard'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('authentication/includes/footer'); ?>
