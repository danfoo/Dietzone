<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <i class="fa fa-times"></i>
                            <?php echo $title; ?>
                        </h4>
                        <hr class="hr-panel-heading" />

                        <?php if ($refund && $refund->status == 'pending'): ?>
                            <div class="alert alert-warning">
                                <i class="fa fa-exclamation-triangle"></i>
                                <strong><?php echo _l('refund_rejection_warning'); ?></strong>
                                <p><?php echo _l('refund_rejection_warning_text'); ?></p>
                            </div>

                            <!-- Refund Details Summary -->
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-bordered table-sm">
                                        <tbody>
                                            <tr>
                                                <td><strong><?php echo _l('patient'); ?>:</strong></td>
                                                <td><?php echo $refund->patient_name; ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong><?php echo _l('refund_amount'); ?>:</strong></td>
                                                <td><strong class="text-danger"><?php echo app_format_money($refund->refund_amount, $refund->currency); ?></strong></td>
                                            </tr>
                                            <tr>
                                                <td><strong><?php echo _l('refund_type'); ?>:</strong></td>
                                                <td>
                                                    <span class="label label-<?php echo $refund->refund_type == 'full' ? 'primary' : 'default'; ?>">
                                                        <?php echo _l('refund_type_' . $refund->refund_type); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h5><strong><?php echo _l('refund_reason'); ?>:</strong></h5>
                                    <div class="alert alert-info">
                                        <?php echo nl2br(htmlspecialchars($refund->reason)); ?>
                                    </div>
                                </div>
                            </div>

                            <hr />

                            <!-- Rejection Form -->
                            <?php echo form_open(admin_url('dietetic/refunds/reject/' . $refund->id)); ?>

                            <div class="form-group">
                                <label for="rejection_reason" class="control-label">
                                    <?php echo _l('rejection_reason'); ?> <span class="text-danger">*</span>
                                </label>
                                <textarea name="rejection_reason" id="rejection_reason" class="form-control" rows="5" required placeholder="<?php echo _l('rejection_reason_placeholder'); ?>"></textarea>
                                <small class="text-muted"><?php echo _l('rejection_reason_help_text'); ?></small>
                            </div>

                            <hr />

                            <div class="btn-group">
                                <button type="submit" class="btn btn-danger">
                                    <i class="fa fa-times"></i> <?php echo _l('reject_refund'); ?>
                                </button>
                                <a href="<?php echo admin_url('dietetic/refunds/view/' . $refund->id); ?>" class="btn btn-default">
                                    <i class="fa fa-arrow-left"></i> <?php echo _l('cancel'); ?>
                                </a>
                            </div>

                            <?php echo form_close(); ?>

                        <?php else: ?>
                            <div class="alert alert-danger">
                                <i class="fa fa-exclamation-triangle"></i>
                                <?php echo _l('refund_cannot_be_rejected'); ?>
                            </div>
                            <a href="<?php echo admin_url('dietetic/refunds'); ?>" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> <?php echo _l('back'); ?>
                            </a>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
