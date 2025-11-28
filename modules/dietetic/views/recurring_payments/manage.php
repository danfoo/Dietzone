<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-refresh"></i> <?php echo _l('recurring_payments'); ?>
                </h3>
            </div>
            <div class="panel-body">
                <!-- Statistics Cards -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="panel panel-success">
                            <div class="panel-body text-center">
                                <h3 id="active-count">-</h3>
                                <p>Actifs</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="panel panel-warning">
                            <div class="panel-body text-center">
                                <h3 id="paused-count">-</h3>
                                <p>En pause</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="panel panel-danger">
                            <div class="panel-body text-center">
                                <h3 id="failed-count">-</h3>
                                <p>Échoués</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="panel panel-info">
                            <div class="panel-body text-center">
                                <h3 id="mrr-amount">-</h3>
                                <p>MRR (FCFA)</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="row">
                    <div class="col-md-12">
                        <a href="<?php echo admin_url('dietetic/recurring_payments/create'); ?>" class="btn btn-primary">
                            <i class="fa fa-plus"></i> Nouveau paiement récurrent
                        </a>
                        <a href="<?php echo admin_url('dietetic/cron/manual_trigger'); ?>" class="btn btn-info">
                            <i class="fa fa-play"></i> Traiter maintenant (manuel)
                        </a>
                        <a href="<?php echo admin_url('dietetic/recurring_payments/export'); ?>" class="btn btn-default">
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
                            <a href="?status=active" class="btn btn-success <?php echo (isset($_GET['status']) && $_GET['status'] == 'active') ? 'active' : ''; ?>">
                                Actifs
                            </a>
                            <a href="?status=paused" class="btn btn-warning <?php echo (isset($_GET['status']) && $_GET['status'] == 'paused') ? 'active' : ''; ?>">
                                En pause
                            </a>
                            <a href="?status=failed" class="btn btn-danger <?php echo (isset($_GET['status']) && $_GET['status'] == 'failed') ? 'active' : ''; ?>">
                                Échoués
                            </a>
                            <a href="?status=cancelled" class="btn btn-default <?php echo (isset($_GET['status']) && $_GET['status'] == 'cancelled') ? 'active' : ''; ?>">
                                Annulés
                            </a>
                        </div>
                    </div>
                </div>

                <hr>

                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="recurring-payments-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Patient</th>
                                <th>Abonnement</th>
                                <th>Montant</th>
                                <th>Fréquence</th>
                                <th>Prochain paiement</th>
                                <th>Statut</th>
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

<script>
$(document).ready(function() {
    // Load statistics
    $.get('<?php echo admin_url('dietetic/recurring_payments/statistics'); ?>', function(data) {
        $('#active-count').text(data.active_count || 0);
        $('#paused-count').text(data.paused_count || 0);
        $('#failed-count').text(data.failed_count || 0);
        $('#mrr-amount').text(data.mrr ? data.mrr.toLocaleString() : '0');
    });

    // Initialize DataTable (if using DataTables)
    // $('#recurring-payments-table').DataTable({
    //     ajax: '<?php echo admin_url('dietetic/recurring_payments/table'); ?>',
    //     // ... configuration
    // });
});
</script>
