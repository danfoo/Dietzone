<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-undo"></i> <?php echo _l('refunds'); ?>
                    <?php if (isset($pending_count) && $pending_count > 0): ?>
                        <span class="badge badge-danger"><?php echo $pending_count; ?> en attente</span>
                    <?php endif; ?>
                </h3>
            </div>
            <div class="panel-body">
                <!-- Statistics Cards -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="panel panel-warning">
                            <div class="panel-body text-center">
                                <h3 id="pending-count">-</h3>
                                <p>En attente</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="panel panel-info">
                            <div class="panel-body text-center">
                                <h3 id="processing-count">-</h3>
                                <p>En traitement</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="panel panel-success">
                            <div class="panel-body text-center">
                                <h3 id="completed-count">-</h3>
                                <p>Complétés</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="panel panel-default">
                            <div class="panel-body text-center">
                                <h3 id="total-refunded">-</h3>
                                <p>Total remboursé (FCFA)</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="row">
                    <div class="col-md-12">
                        <a href="<?php echo admin_url('dietetic/refunds/create'); ?>" class="btn btn-primary">
                            <i class="fa fa-plus"></i> Nouveau remboursement
                        </a>
                        <button type="button" class="btn btn-success" id="bulk-approve-btn" disabled>
                            <i class="fa fa-check"></i> Approuver sélection
                        </button>
                        <a href="<?php echo admin_url('dietetic/refunds/export'); ?>" class="btn btn-default">
                            <i class="fa fa-download"></i> Exporter
                        </a>
                        <hr>
                    </div>
                </div>

                <!-- Filters -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="btn-group" role="group">
                            <a href="?status=all" class="btn btn-default <?php echo (!isset($_GET['status']) || $_GET['status'] == 'all') ? 'active' : ''; ?>">
                                Tous
                            </a>
                            <a href="?status=pending" class="btn btn-warning <?php echo (isset($_GET['status']) && $_GET['status'] == 'pending') ? 'active' : ''; ?>">
                                En attente
                            </a>
                            <a href="?status=approved" class="btn btn-info <?php echo (isset($_GET['status']) && $_GET['status'] == 'approved') ? 'active' : ''; ?>">
                                Approuvés
                            </a>
                            <a href="?status=processing" class="btn btn-info <?php echo (isset($_GET['status']) && $_GET['status'] == 'processing') ? 'active' : ''; ?>">
                                En traitement
                            </a>
                            <a href="?status=completed" class="btn btn-success <?php echo (isset($_GET['status']) && $_GET['status'] == 'completed') ? 'active' : ''; ?>">
                                Complétés
                            </a>
                            <a href="?status=rejected" class="btn btn-danger <?php echo (isset($_GET['status']) && $_GET['status'] == 'rejected') ? 'active' : ''; ?>">
                                Rejetés
                            </a>
                        </div>
                    </div>
                </div>

                <hr>

                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="refunds-table">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="select-all"></th>
                                <th>Numéro</th>
                                <th>Patient</th>
                                <th>Facture</th>
                                <th>Montant</th>
                                <th>Type</th>
                                <th>Statut</th>
                                <th>Date demande</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data loaded via AJAX or server-side -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Actions Form -->
<form id="bulk-approve-form" method="post" action="<?php echo admin_url('dietetic/refunds/bulk_approve'); ?>" style="display: none;">
    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
    <div id="bulk-refund-ids"></div>
</form>

<script>
$(document).ready(function() {
    // Load statistics
    $.get('<?php echo admin_url('dietetic/refunds/statistics'); ?>', function(data) {
        $('#pending-count').text(data.pending_count || 0);
        $('#processing-count').text(data.processing_count || 0);
        $('#completed-count').text(data.completed_count || 0);
        $('#total-refunded').text(data.total_refunded ? data.total_refunded.toLocaleString() : '0');
    });

    // Select all checkbox
    $('#select-all').on('change', function() {
        $('.refund-checkbox').prop('checked', $(this).prop('checked'));
        updateBulkButton();
    });

    // Individual checkboxes
    $(document).on('change', '.refund-checkbox', function() {
        updateBulkButton();
    });

    // Bulk approve button
    $('#bulk-approve-btn').on('click', function() {
        var selected = [];
        $('.refund-checkbox:checked').each(function() {
            selected.push($(this).val());
        });

        if (selected.length === 0) {
            alert('Veuillez sélectionner au moins un remboursement');
            return;
        }

        if (!confirm('Êtes-vous sûr de vouloir approuver ' + selected.length + ' remboursement(s) ?')) {
            return;
        }

        // Add hidden inputs
        $('#bulk-refund-ids').empty();
        selected.forEach(function(id) {
            $('#bulk-refund-ids').append('<input type="hidden" name="refund_ids[]" value="' + id + '">');
        });

        // Submit form
        $('#bulk-approve-form').submit();
    });

    function updateBulkButton() {
        var checkedCount = $('.refund-checkbox:checked').length;
        $('#bulk-approve-btn').prop('disabled', checkedCount === 0);
        $('#bulk-approve-btn').text('Approuver sélection (' + checkedCount + ')');
    }
});
</script>

<style>
.badge-danger {
    background-color: #d9534f;
    color: white;
    padding: 3px 7px;
    border-radius: 10px;
    font-size: 12px;
    margin-left: 5px;
}
</style>
