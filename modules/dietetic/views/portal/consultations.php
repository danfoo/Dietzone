<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $this->load->view('authentication/includes/head'); ?>

<div class="container">
    <div class="row mtop30">
        <div class="col-md-12">
            <h3><?php echo _l('dietetic_my_consultations'); ?></h3>
            <hr />

            <div class="panel panel-default">
                <div class="panel-body">
                    <?php if (!empty($consultations)) { ?>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th><?php echo _l('dietetic_date'); ?></th>
                                    <th><?php echo _l('dietetic_type'); ?></th>
                                    <th><?php echo _l('dietetic_status'); ?></th>
                                    <th><?php echo _l('dietetic_duration'); ?></th>
                                    <th><?php echo _l('dietetic_location'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($consultations as $consultation) { ?>
                                    <tr>
                                        <td><?php echo _dt($consultation->consultation_date); ?></td>
                                        <td><?php echo ucfirst(str_replace('_', ' ', $consultation->consultation_type)); ?></td>
                                        <td><?php echo dietetic_consultation_status_badge($consultation->status); ?></td>
                                        <td><?php echo $consultation->duration; ?> min</td>
                                        <td><?php echo $consultation->location ?? '-'; ?></td>
                                    </tr>
                                    <?php if ($consultation->notes) { ?>
                                        <tr>
                                            <td colspan="5">
                                                <small class="text-muted">
                                                    <strong><?php echo _l('dietetic_notes'); ?>:</strong> <?php echo nl2br($consultation->notes); ?>
                                                </small>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                <?php } ?>
                            </tbody>
                        </table>
                    <?php } else { ?>
                        <div class="text-center">
                            <p class="lead text-muted"><?php echo _l('dietetic_no_consultations'); ?></p>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <div class="mtop30">
                <a href="<?php echo site_url('dietetic/portal'); ?>" class="btn btn-default">
                    <i class="fa fa-arrow-left"></i> <?php echo _l('back'); ?>
                </a>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('authentication/includes/footer'); ?>
