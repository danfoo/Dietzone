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
                            <?php echo _l('refund'); ?> #<?php echo $refund->id; ?>
                        </h4>
                        <hr class="hr-panel-heading" />

                        <div class="row">
                            <div class="col-md-6">
                                <h5><strong><?php echo _l('refund_details'); ?></strong></h5>
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <td><strong><?php echo _l('patient'); ?>:</strong></td>
                                            <td>
                                                <?php if ($refund->patient_id && $refund->patient_name) { ?>
                                                    <a href="<?php echo admin_url('dietetic/patients/view/' . $refund->patient_id); ?>">
                                                        <?php echo htmlspecialchars($refund->patient_name); ?>
                                                    </a>
                                                <?php } else { ?>
                                                    <span class="text-muted">-</span>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong><?php echo _l('invoice'); ?>:</strong></td>
                                            <td>
                                                <?php if ($refund->invoice_id && $refund->invoice_number) { ?>
                                                    <a href="<?php echo admin_url('dietetic/invoices/view/' . $refund->invoice_id); ?>">
                                                        #<?php echo htmlspecialchars($refund->invoice_number); ?>
                                                    </a>
                                                <?php } else { ?>
                                                    <span class="text-muted">-</span>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong><?php echo _l('refund_type'); ?>:</strong></td>
                                            <td>
                                                <span class="label label-<?php echo $refund->refund_type == 'full' ? 'primary' : 'default'; ?>">
                                                    <?php echo _l('refund_type_' . $refund->refund_type); ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong><?php echo _l('original_amount'); ?>:</strong></td>
                                            <td><?php echo app_format_money($refund->original_amount, $refund->currency ?? 'XOF'); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong><?php echo _l('refund_amount'); ?>:</strong></td>
                                            <td><strong><?php echo app_format_money($refund->refund_amount, $refund->currency ?? 'XOF'); ?></strong></td>
                                        </tr>
                                        <tr>
                                            <td><strong><?php echo _l('remaining_amount'); ?>:</strong></td>
                                            <td><?php echo app_format_money($refund->remaining_amount ?? 0, $refund->currency ?? 'XOF'); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong><?php echo _l('payment_method'); ?>:</strong></td>
                                            <td><?php echo $refund->payment_method ? ucfirst($refund->payment_method) : '-'; ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong><?php echo _l('status'); ?>:</strong></td>
                                            <td>
                                                <?php
                                                $status_class = '';
                                                switch ($refund->status) {
                                                    case 'completed':
                                                        $status_class = 'success';
                                                        break;
                                                    case 'pending':
                                                        $status_class = 'warning';
                                                        break;
                                                    case 'processing':
                                                        $status_class = 'info';
                                                        break;
                                                    case 'failed':
                                                    case 'cancelled':
                                                        $status_class = 'danger';
                                                        break;
                                                }
                                                ?>
                                                <span class="label label-<?php echo $status_class; ?>">
                                                    <?php echo _l('refund_status_' . $refund->status); ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong><?php echo _l('initiated_by'); ?>:</strong></td>
                                            <td><?php echo get_staff_full_name($refund->initiated_by); ?></td>
                                        </tr>
                                        <?php if ($refund->approved_by): ?>
                                        <tr>
                                            <td><strong><?php echo _l('approved_by'); ?>:</strong></td>
                                            <td><?php echo get_staff_full_name($refund->approved_by); ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        <tr>
                                            <td><strong><?php echo _l('created_at'); ?>:</strong></td>
                                            <td><?php echo _dt($refund->created_at); ?></td>
                                        </tr>
                                        <?php if ($refund->processed_date): ?>
                                        <tr>
                                            <td><strong><?php echo _l('processed_date'); ?>:</strong></td>
                                            <td><?php echo _dt($refund->processed_date); ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        <?php if ($refund->completed_date): ?>
                                        <tr>
                                            <td><strong><?php echo _l('completed_date'); ?>:</strong></td>
                                            <td><?php echo _dt($refund->completed_date); ?></td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>

                                <h5><strong><?php echo _l('reason'); ?></strong></h5>
                                <div class="alert alert-info">
                                    <?php echo nl2br(htmlspecialchars($refund->reason ?? '')); ?>
                                </div>

                                <?php if ($refund->notes): ?>
                                <h5><strong><?php echo _l('notes'); ?></strong></h5>
                                <div class="alert alert-warning">
                                    <?php echo nl2br(htmlspecialchars($refund->notes)); ?>
                                </div>
                                <?php endif; ?>

                                <?php if ($refund->error_message): ?>
                                <h5><strong><?php echo _l('error_message'); ?></strong></h5>
                                <div class="alert alert-danger">
                                    <?php echo nl2br(htmlspecialchars($refund->error_message)); ?>
                                </div>
                                <?php endif; ?>
                            </div>

                            <div class="col-md-6">
                                <h5><strong><?php echo _l('actions'); ?></strong></h5>
                                <div class="btn-group-vertical btn-block">
                                    <?php if ($refund->status == 'pending' && has_permission('dietetic', '', 'edit')) { ?>
                                        <a href="<?php echo admin_url('dietetic/refunds/approve/' . $refund->id); ?>" class="btn btn-success" onclick="return confirm('<?php echo _l('confirm_approve_refund'); ?>');">
                                            <i class="fa fa-check"></i> <?php echo _l('refund_approve'); ?>
                                        </a>
                                        <a href="<?php echo admin_url('dietetic/refunds/reject/' . $refund->id); ?>" class="btn btn-danger" onclick="return confirm('<?php echo _l('confirm_reject_refund'); ?>');">
                                            <i class="fa fa-times"></i> <?php echo _l('refund_reject'); ?>
                                        </a>
                                    <?php } ?>
                                    <?php if (in_array($refund->status, ['pending', 'processing']) && has_permission('dietetic', '', 'edit')) { ?>
                                        <a href="<?php echo admin_url('dietetic/refunds/process/' . $refund->id); ?>" class="btn btn-info" onclick="return confirm('<?php echo _l('confirm_process_refund'); ?>');">
                                            <i class="fa fa-cog"></i> <?php echo _l('refund_process'); ?>
                                        </a>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
