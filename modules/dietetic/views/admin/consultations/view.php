<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4><?php echo _l('dietetic_consultation') . ' #' . $consultation->id; ?></h4>
                        <hr />

                        <div class="row">
                            <div class="col-md-6">
                                <h5><?php echo _l('details'); ?></h5>

                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong><?php echo _l('dietetic_patient'); ?>:</strong></td>
                                        <td><a href="<?php echo admin_url('dietetic/patients/view/' . $patient->id); ?>"><?php echo $consultation->client_name; ?></a></td>
                                    </tr>
                                    <tr>
                                        <td><strong><?php echo _l('dietetic_dietitian'); ?>:</strong></td>
                                        <td><?php echo $consultation->dietitian_name; ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong><?php echo _l('dietetic_date'); ?>:</strong></td>
                                        <td><?php echo _dt($consultation->consultation_date); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong><?php echo _l('dietetic_type'); ?>:</strong></td>
                                        <td><?php echo ucfirst(str_replace('_', ' ', $consultation->consultation_type)); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong><?php echo _l('dietetic_status'); ?>:</strong></td>
                                        <td><?php echo dietetic_consultation_status_badge($consultation->status); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong><?php echo _l('dietetic_duration'); ?>:</strong></td>
                                        <td><?php echo $consultation->duration; ?> minutes</td>
                                    </tr>
                                    <?php if ($consultation->location) { ?>
                                        <tr>
                                            <td><strong><?php echo _l('dietetic_location'); ?>:</strong></td>
                                            <td><?php echo $consultation->location; ?></td>
                                        </tr>
                                    <?php } ?>
                                    <?php if ($consultation->weight_at_visit) { ?>
                                        <tr>
                                            <td><strong><?php echo _l('dietetic_weight'); ?>:</strong></td>
                                            <td><?php echo $consultation->weight_at_visit; ?> kg</td>
                                        </tr>
                                    <?php } ?>
                                    <?php if ($consultation->satisfaction_score) { ?>
                                        <tr>
                                            <td><strong><?php echo _l('dietetic_satisfaction'); ?>:</strong></td>
                                            <td><?php echo $consultation->satisfaction_score; ?>/5 ⭐</td>
                                        </tr>
                                    <?php } ?>
                                </table>
                            </div>

                            <div class="col-md-6">
                                <?php if ($consultation->reason) { ?>
                                    <h5><?php echo _l('dietetic_reason'); ?></h5>
                                    <p><?php echo nl2br($consultation->reason); ?></p>
                                <?php } ?>

                                <?php if ($consultation->next_consultation_date) { ?>
                                    <h5><?php echo _l('dietetic_next_consultation'); ?></h5>
                                    <p><?php echo _dt($consultation->next_consultation_date); ?></p>
                                <?php } ?>
                            </div>
                        </div>

                        <?php if ($consultation->notes) { ?>
                            <hr />
                            <h5><?php echo _l('dietetic_notes'); ?></h5>
                            <div class="well"><?php echo nl2br($consultation->notes); ?></div>
                        <?php } ?>

                        <?php if ($consultation->observations) { ?>
                            <hr />
                            <h5><?php echo _l('dietetic_observations'); ?></h5>
                            <div class="well"><?php echo nl2br($consultation->observations); ?></div>
                        <?php } ?>

                        <?php if ($consultation->recommendations) { ?>
                            <hr />
                            <h5><?php echo _l('dietetic_recommendations'); ?></h5>
                            <div class="well"><?php echo nl2br($consultation->recommendations); ?></div>
                        <?php } ?>

                        <hr />
                        <div class="btn-bottom-toolbar">
                            <a href="<?php echo admin_url('dietetic/consultations'); ?>" class="btn btn-default"><?php echo _l('back'); ?></a>
                            <?php if (dietetic_has_permission('edit')) { ?>
                                <a href="<?php echo admin_url('dietetic/consultations/edit/' . $consultation->id); ?>" class="btn btn-info">
                                    <i class="fa fa-pencil"></i> <?php echo _l('edit'); ?>
                                </a>
                            <?php } ?>
                            <?php if ($consultation->status == 'scheduled' && dietetic_has_permission('edit')) { ?>
                                <a href="#" onclick="if(confirm('Mark as completed?')) { $.post('<?php echo admin_url('dietetic/consultations/mark_completed/' . $consultation->id); ?>', function(r) { if(r.success) location.reload(); }); } return false;" class="btn btn-success">
                                    <i class="fa fa-check"></i> <?php echo _l('dietetic_consultation_marked_completed'); ?>
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
