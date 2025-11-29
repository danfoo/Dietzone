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
                            <?php echo _l('recurring_payment'); ?> #<?php echo $recurring->id; ?>
                        </h4>
                        <hr class="hr-panel-heading" />

                        <div class="row">
                            <div class="col-md-6">
                                <h5><strong><?php echo _l('recurring_payment_details'); ?></strong></h5>
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <td><strong><?php echo _l('patient'); ?>:</strong></td>
                                            <td>
                                                <a href="<?php echo admin_url('dietetic/patients/view/' . $recurring->patient_id); ?>">
                                                    <?php echo $recurring->patient_name; ?>
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong><?php echo _l('subscription'); ?>:</strong></td>
                                            <td>
                                                <a href="<?php echo admin_url('dietetic/subscriptions/view/' . $recurring->subscription_id); ?>">
                                                    <?php echo $recurring->service_plan_name; ?>
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong><?php echo _l('amount'); ?>:</strong></td>
                                            <td><?php echo app_format_money($recurring->amount, $recurring->currency); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong><?php echo _l('frequency'); ?>:</strong></td>
                                            <td><?php echo _l('recurring_payment_frequency_' . $recurring->frequency); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong><?php echo _l('payment_method'); ?>:</strong></td>
                                            <td><?php echo ucfirst($recurring->payment_method); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong><?php echo _l('status'); ?>:</strong></td>
                                            <td>
                                                <?php
                                                $status_class = $recurring->status == 'active' ? 'success' : ($recurring->status == 'paused' ? 'warning' : 'danger');
                                                ?>
                                                <span class="label label-<?php echo $status_class; ?>">
                                                    <?php echo _l('recurring_payment_status_' . $recurring->status); ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong><?php echo _l('start_date'); ?>:</strong></td>
                                            <td><?php echo _d($recurring->start_date); ?></td>
                                        </tr>
                                        <?php if ($recurring->end_date): ?>
                                        <tr>
                                            <td><strong><?php echo _l('end_date'); ?>:</strong></td>
                                            <td><?php echo _d($recurring->end_date); ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        <tr>
                                            <td><strong><?php echo _l('next_payment_date'); ?>:</strong></td>
                                            <td><?php echo _d($recurring->next_payment_date); ?></td>
                                        </tr>
                                        <?php if ($recurring->last_payment_date): ?>
                                        <tr>
                                            <td><strong><?php echo _l('last_payment_date'); ?>:</strong></td>
                                            <td><?php echo _d($recurring->last_payment_date); ?></td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="col-md-6">
                                <h5><strong><?php echo _l('actions'); ?></strong></h5>
                                <div class="btn-group-vertical btn-block">
                                    <?php if ($recurring->status == 'active' && has_permission('dietetic', '', 'edit')) { ?>
                                        <a href="<?php echo admin_url('dietetic/recurring_payments/pause/' . $recurring->id); ?>" class="btn btn-warning">
                                            <i class="fa fa-pause"></i> <?php echo _l('recurring_payment_pause'); ?>
                                        </a>
                                    <?php } ?>
                                    <?php if ($recurring->status == 'paused' && has_permission('dietetic', '', 'edit')) { ?>
                                        <a href="<?php echo admin_url('dietetic/recurring_payments/resume/' . $recurring->id); ?>" class="btn btn-success">
                                            <i class="fa fa-play"></i> <?php echo _l('recurring_payment_resume'); ?>
                                        </a>
                                    <?php } ?>
                                    <?php if (in_array($recurring->status, ['active', 'paused']) && has_permission('dietetic', '', 'delete')) { ?>
                                        <a href="<?php echo admin_url('dietetic/recurring_payments/cancel/' . $recurring->id); ?>" class="btn btn-danger" onclick="return confirm('<?php echo _l('confirm_cancel_recurring_payment'); ?>');">
                                            <i class="fa fa-times"></i> <?php echo _l('recurring_payment_cancel'); ?>
                                        </a>
                                    <?php } ?>
                                    <?php if (has_permission('dietetic', '', 'edit')) { ?>
                                        <a href="<?php echo admin_url('dietetic/recurring_payments/edit/' . $recurring->id); ?>" class="btn btn-info">
                                            <i class="fa fa-pencil"></i> <?php echo _l('edit'); ?>
                                        </a>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>

                        <hr />

                        <h5><strong><?php echo _l('recurring_payment_transactions'); ?></strong></h5>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th><?php echo _l('id'); ?></th>
                                    <th><?php echo _l('scheduled_date'); ?></th>
                                    <th><?php echo _l('processed_date'); ?></th>
                                    <th><?php echo _l('amount'); ?></th>
                                    <th><?php echo _l('status'); ?></th>
                                    <th><?php echo _l('invoice'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($transactions)): ?>
                                    <?php foreach ($transactions as $trans): ?>
                                        <tr>
                                            <td><?php echo $trans->id; ?></td>
                                            <td><?php echo _d($trans->scheduled_date); ?></td>
                                            <td><?php echo $trans->processed_date ? _dt($trans->processed_date) : '-'; ?></td>
                                            <td><?php echo app_format_money($trans->amount, $recurring->currency); ?></td>
                                            <td>
                                                <?php
                                                $trans_status_class = '';
                                                switch ($trans->status) {
                                                    case 'completed':
                                                        $trans_status_class = 'success';
                                                        break;
                                                    case 'pending':
                                                        $trans_status_class = 'warning';
                                                        break;
                                                    case 'processing':
                                                        $trans_status_class = 'info';
                                                        break;
                                                    case 'failed':
                                                        $trans_status_class = 'danger';
                                                        break;
                                                }
                                                ?>
                                                <span class="label label-<?php echo $trans_status_class; ?>">
                                                    <?php echo ucfirst($trans->status); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($trans->invoice_id): ?>
                                                    <a href="<?php echo admin_url('dietetic/invoices/view/' . $trans->invoice_id); ?>">
                                                        <?php echo $trans->invoice_number; ?>
                                                    </a>
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center"><?php echo _l('no_transactions_yet'); ?></td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
