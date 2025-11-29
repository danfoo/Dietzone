<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <h4 class="no-margin">
                                    <i class="fa fa-refresh"></i>
                                    <?php echo _l('recurring_payments'); ?>
                                </h4>
                                <hr class="hr-panel-heading" />
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="_buttons">
                                    <?php if (has_permission('dietetic', '', 'create')) { ?>
                                        <a href="<?php echo admin_url('dietetic/recurring_payments/create'); ?>" class="btn btn-info pull-left display-block">
                                            <i class="fa-regular fa-plus tw-mr-1"></i>
                                            <?php echo _l('new_recurring_payment'); ?>
                                        </a>
                                    <?php } ?>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="clearfix mtop20"></div>

                                <!-- Table -->
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th><?php echo _l('id'); ?></th>
                                            <th><?php echo _l('patient'); ?></th>
                                            <th><?php echo _l('subscription'); ?></th>
                                            <th><?php echo _l('amount'); ?></th>
                                            <th><?php echo _l('frequency'); ?></th>
                                            <th><?php echo _l('next_payment_date'); ?></th>
                                            <th><?php echo _l('status'); ?></th>
                                            <th><?php echo _l('options'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (!isset($recurring_payments)) {
                                            $recurring_payments = [];
                                        }

                                        foreach ($recurring_payments as $rp) {
                                            $status_class = '';
                                            switch ($rp->status) {
                                                case 'active':
                                                    $status_class = 'success';
                                                    break;
                                                case 'paused':
                                                    $status_class = 'warning';
                                                    break;
                                                case 'cancelled':
                                                case 'failed':
                                                    $status_class = 'danger';
                                                    break;
                                                default:
                                                    $status_class = 'default';
                                            }
                                            ?>
                                            <tr>
                                                <td><?php echo $rp->id; ?></td>
                                                <td>
                                                    <?php if ($rp->patient_id && $rp->patient_name) { ?>
                                                        <a href="<?php echo admin_url('dietetic/patients/view/' . $rp->patient_id); ?>">
                                                            <?php echo $rp->patient_name; ?>
                                                        </a>
                                                    <?php } else { ?>
                                                        <span class="text-muted">-</span>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <?php if ($rp->subscription_id && $rp->service_plan_name) { ?>
                                                        <a href="<?php echo admin_url('dietetic/subscriptions/view/' . $rp->subscription_id); ?>">
                                                            <?php echo $rp->service_plan_name; ?>
                                                        </a>
                                                    <?php } else { ?>
                                                        <span class="text-muted">-</span>
                                                    <?php } ?>
                                                </td>
                                                <td><?php echo app_format_money($rp->amount, $rp->currency ?? 'XOF'); ?></td>
                                                <td><?php echo _l('recurring_payment_frequency_' . $rp->frequency); ?></td>
                                                <td><?php echo _d($rp->next_payment_date); ?></td>
                                                <td>
                                                    <span class="label label-<?php echo $status_class; ?>">
                                                        <?php echo _l('recurring_payment_status_' . $rp->status); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="<?php echo admin_url('dietetic/recurring_payments/view/' . $rp->id); ?>" class="btn btn-default btn-icon">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <?php if (has_permission('dietetic', '', 'edit')) { ?>
                                                        <a href="<?php echo admin_url('dietetic/recurring_payments/edit/' . $rp->id); ?>" class="btn btn-default btn-icon">
                                                            <i class="fa fa-pencil-square-o"></i>
                                                        </a>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
