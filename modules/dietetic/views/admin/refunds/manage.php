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
                                    <i class="fa fa-undo"></i>
                                    <?php echo _l('refunds'); ?>
                                    <?php if (isset($pending_count) && $pending_count > 0) { ?>
                                        <span class="label label-warning mleft5"><?php echo $pending_count; ?> <?php echo _l('pending'); ?></span>
                                    <?php } ?>
                                </h4>
                                <hr class="hr-panel-heading" />
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="_buttons">
                                    <?php if (has_permission('dietetic', '', 'create')) { ?>
                                        <a href="<?php echo admin_url('dietetic/refunds/create'); ?>" class="btn btn-info pull-left display-block">
                                            <i class="fa-regular fa-plus tw-mr-1"></i>
                                            <?php echo _l('initiate_refund'); ?>
                                        </a>
                                    <?php } ?>
                                    <a href="<?php echo admin_url('dietetic/refunds/export' . ($this->input->get('status') ? '?status=' . $this->input->get('status') : '')); ?>" class="btn btn-default pull-left mleft5">
                                        <i class="fa fa-download"></i>
                                        <?php echo _l('export'); ?> CSV
                                    </a>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="clearfix mtop20"></div>

                                <!-- Filters -->
                                <div class="row mbot15">
                                    <div class="col-md-12">
                                        <div class="btn-group">
                                            <a href="<?php echo admin_url('dietetic/refunds'); ?>" class="btn btn-default <?php echo !$this->input->get('status') ? 'active' : ''; ?>">
                                                <?php echo _l('all'); ?>
                                            </a>
                                            <a href="<?php echo admin_url('dietetic/refunds?status=pending'); ?>" class="btn btn-default <?php echo $this->input->get('status') == 'pending' ? 'active' : ''; ?>">
                                                <?php echo _l('refund_status_pending'); ?>
                                            </a>
                                            <a href="<?php echo admin_url('dietetic/refunds?status=processing'); ?>" class="btn btn-default <?php echo $this->input->get('status') == 'processing' ? 'active' : ''; ?>">
                                                <?php echo _l('refund_status_processing'); ?>
                                            </a>
                                            <a href="<?php echo admin_url('dietetic/refunds?status=completed'); ?>" class="btn btn-default <?php echo $this->input->get('status') == 'completed' ? 'active' : ''; ?>">
                                                <?php echo _l('refund_status_completed'); ?>
                                            </a>
                                            <a href="<?php echo admin_url('dietetic/refunds?status=failed'); ?>" class="btn btn-default <?php echo $this->input->get('status') == 'failed' ? 'active' : ''; ?>">
                                                <?php echo _l('refund_status_failed'); ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <!-- Simple HTML Table - No DataTables -->
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered">
                                        <thead>
                                            <tr style="background-color: #f4f4f4;">
                                                <th><?php echo _l('id'); ?></th>
                                                <th><?php echo _l('patient'); ?></th>
                                                <th><?php echo _l('invoice'); ?></th>
                                                <th><?php echo _l('refund_type'); ?></th>
                                                <th><?php echo _l('refund_amount'); ?></th>
                                                <th><?php echo _l('reason'); ?></th>
                                                <th><?php echo _l('status'); ?></th>
                                                <th><?php echo _l('date'); ?></th>
                                                <th><?php echo _l('options'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if (!isset($refunds)) {
                                                $refunds = [];
                                            }

                                            if (empty($refunds)) {
                                                ?>
                                                <tr>
                                                    <td colspan="9" class="text-center text-muted">
                                                        <p class="mtop20 mbot20">
                                                            <i class="fa fa-info-circle"></i>
                                                            <?php echo _l('no_refunds_found'); ?>
                                                        </p>
                                                    </td>
                                                </tr>
                                                <?php
                                            } else {
                                                foreach ($refunds as $refund) {
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
                                                        default:
                                                            $status_class = 'default';
                                                    }
                                                    ?>
                                                    <tr>
                                                        <td><strong>#<?php echo $refund->id; ?></strong></td>
                                                        <td>
                                                            <?php if ($refund->patient_id && $refund->patient_name) { ?>
                                                                <a href="<?php echo admin_url('dietetic/patients/view/' . $refund->patient_id); ?>">
                                                                    <?php echo htmlspecialchars($refund->patient_name); ?>
                                                                </a>
                                                            <?php } else { ?>
                                                                <span class="text-muted">-</span>
                                                            <?php } ?>
                                                        </td>
                                                        <td>
                                                            <?php if ($refund->invoice_id && $refund->invoice_number) { ?>
                                                                <a href="<?php echo admin_url('dietetic/invoices/view/' . $refund->invoice_id); ?>">
                                                                    #<?php echo htmlspecialchars($refund->invoice_number); ?>
                                                                </a>
                                                            <?php } else { ?>
                                                                <span class="text-muted">-</span>
                                                            <?php } ?>
                                                        </td>
                                                        <td>
                                                            <span class="label label-<?php echo $refund->refund_type == 'full' ? 'primary' : 'default'; ?>">
                                                                <?php echo _l('refund_type_' . $refund->refund_type); ?>
                                                            </span>
                                                        </td>
                                                        <td><strong><?php echo app_format_money($refund->refund_amount, $refund->currency ?? 'XOF'); ?></strong></td>
                                                        <td>
                                                            <?php
                                                            $reason = $refund->reason ?? '';
                                                            if (strlen($reason) > 50) {
                                                                echo '<span title="' . htmlspecialchars($reason) . '">';
                                                                echo htmlspecialchars(substr($reason, 0, 50)) . '...';
                                                                echo '</span>';
                                                            } else {
                                                                echo htmlspecialchars($reason);
                                                            }
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <span class="label label-<?php echo $status_class; ?>">
                                                                <?php echo _l('refund_status_' . $refund->status); ?>
                                                            </span>
                                                        </td>
                                                        <td><?php echo _dt($refund->created_at); ?></td>
                                                        <td class="text-center">
                                                            <div class="btn-group">
                                                                <a href="<?php echo admin_url('dietetic/refunds/view/' . $refund->id); ?>" class="btn btn-default btn-sm" title="<?php echo _l('view'); ?>">
                                                                    <i class="fa fa-eye"></i>
                                                                </a>
                                                                <?php if (has_permission('dietetic', '', 'edit') && $refund->status == 'pending') { ?>
                                                                    <a href="<?php echo admin_url('dietetic/refunds/approve/' . $refund->id); ?>" class="btn btn-success btn-sm" title="<?php echo _l('approve'); ?>">
                                                                        <i class="fa fa-check"></i>
                                                                    </a>
                                                                    <a href="<?php echo admin_url('dietetic/refunds/reject/' . $refund->id); ?>" class="btn btn-danger btn-sm" title="<?php echo _l('reject'); ?>">
                                                                        <i class="fa fa-times"></i>
                                                                    </a>
                                                                <?php } ?>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>

                                <?php if (!empty($refunds)) { ?>
                                    <div class="text-right text-muted mtop10">
                                        <?php echo count($refunds); ?> <?php echo _l('refund(s)'); ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Disable automatic DataTables initialization -->
<script>
    // Prevent any DataTables auto-initialization
    if (typeof $.fn.dataTable !== 'undefined') {
        $.fn.dataTable.ext.search = [];
    }
</script>

<?php init_tail(); ?>
