<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <i class="fa fa-cog"></i>
                            <?php echo $title; ?>
                        </h4>
                        <hr class="hr-panel-heading" />

                        <?php if ($refund && in_array($refund->status, ['pending', 'processing'])): ?>
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i>
                                <strong><?php echo _l('manual_refund_processing'); ?></strong>
                                <p><?php echo _l('manual_refund_processing_text'); ?></p>
                            </div>

                            <!-- Refund Details Summary -->
                            <div class="row">
                                <div class="col-md-6">
                                    <h5><strong><?php echo _l('refund_details'); ?></strong></h5>
                                    <table class="table table-bordered table-sm">
                                        <tbody>
                                            <tr>
                                                <td><strong><?php echo _l('patient'); ?>:</strong></td>
                                                <td><?php echo $refund->patient_name; ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong><?php echo _l('payment_reference'); ?>:</strong></td>
                                                <td><?php echo $payment->payment_reference ?? 'N/A'; ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong><?php echo _l('refund_amount'); ?>:</strong></td>
                                                <td><strong class="text-info"><?php echo app_format_money($refund->refund_amount, $refund->currency); ?></strong></td>
                                            </tr>
                                            <tr>
                                                <td><strong><?php echo _l('payment_method'); ?>:</strong></td>
                                                <td><?php echo ucfirst($refund->payment_method); ?></td>
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
                                    <div class="alert alert-warning">
                                        <?php echo nl2br(htmlspecialchars($refund->reason)); ?>
                                    </div>

                                    <?php if ($refund->notes): ?>
                                        <h5><strong><?php echo _l('notes'); ?>:</strong></h5>
                                        <div class="alert alert-info">
                                            <?php echo nl2br(htmlspecialchars($refund->notes)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <hr />

                            <!-- Processing Form -->
                            <?php echo form_open(admin_url('dietetic/refunds/process/' . $refund->id)); ?>

                            <div class="alert alert-success">
                                <i class="fa fa-check-circle"></i>
                                <strong><?php echo _l('process_refund_instructions'); ?></strong>
                                <ol>
                                    <li><?php echo _l('process_refund_step_1'); ?></li>
                                    <li><?php echo _l('process_refund_step_2'); ?></li>
                                    <li><?php echo _l('process_refund_step_3'); ?></li>
                                </ol>
                            </div>

                            <div class="form-group">
                                <label for="manual_reference" class="control-label">
                                    <?php echo _l('refund_transaction_reference'); ?> <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="manual_reference" id="manual_reference" class="form-control" required
                                    placeholder="<?php echo _l('refund_reference_placeholder'); ?>">
                                <small class="text-muted"><?php echo _l('refund_reference_help_text'); ?></small>
                            </div>

                            <div class="form-group">
                                <label for="processing_notes"><?php echo _l('processing_notes'); ?></label>
                                <textarea name="processing_notes" id="processing_notes" class="form-control" rows="4"
                                    placeholder="<?php echo _l('processing_notes_placeholder'); ?>"></textarea>
                                <small class="text-muted"><?php echo _l('optional'); ?></small>
                            </div>

                            <hr />

                            <div class="btn-group">
                                <button type="submit" class="btn btn-info">
                                    <i class="fa fa-check"></i> <?php echo _l('mark_refund_as_processed'); ?>
                                </button>
                                <a href="<?php echo admin_url('dietetic/refunds/view/' . $refund->id); ?>" class="btn btn-default">
                                    <i class="fa fa-arrow-left"></i> <?php echo _l('cancel'); ?>
                                </a>
                            </div>

                            <?php echo form_close(); ?>

                        <?php else: ?>
                            <div class="alert alert-danger">
                                <i class="fa fa-exclamation-triangle"></i>
                                <?php echo _l('refund_cannot_be_processed'); ?>
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
