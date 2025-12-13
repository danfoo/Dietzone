<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
.settings-card {
    background: white;
    border-radius: 10px;
    padding: 25px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    margin-bottom: 20px;
}

.settings-card h4 {
    color: #2c3e50;
    margin-top: 0;
    border-bottom: 2px solid #3498db;
    padding-bottom: 10px;
    margin-bottom: 20px;
}

.migration-status {
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.migration-status.pending {
    background: #fff3cd;
    border-left: 4px solid #ffc107;
}

.migration-status.completed {
    background: #d4edda;
    border-left: 4px solid #28a745;
}

.migration-status.error {
    background: #f8d7da;
    border-left: 4px solid #dc3545;
}

.btn-run-migration {
    padding: 12px 30px;
    font-size: 16px;
    font-weight: bold;
}

.migration-log {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 5px;
    padding: 15px;
    max-height: 400px;
    overflow-y: auto;
    font-family: 'Courier New', monospace;
    font-size: 13px;
    display: none;
}

.migration-log.show {
    display: block;
}

.log-line {
    margin: 5px 0;
}

.log-line.success {
    color: #28a745;
}

.log-line.error {
    color: #dc3545;
}

.log-line.info {
    color: #17a2b8;
}
</style>

<div id="wrapper">
    <div class="content">
        <!-- Page Header -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 8px; padding: 30px;">
                        <h2 style="color: white; margin: 0;">
                            <i class="fa fa-cog fa-spin" style="font-size: 36px; margin-right: 15px;"></i>
                            Paramètres du Module Diététique
                        </h2>
                        <p style="color: rgba(255,255,255,0.9); margin: 10px 0 0 0;">
                            Configuration et gestion des fonctionnalités avancées
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Migration Section -->
        <div class="row">
            <div class="col-md-12">
                <div class="settings-card">
                    <h4>
                        <i class="fa fa-database"></i> Migration Base de Données
                    </h4>

                    <div id="migration-status" class="migration-status pending">
                        <div class="d-flex align-items-center">
                            <i class="fa fa-spinner fa-spin" style="font-size: 24px; margin-right: 15px;"></i>
                            <div>
                                <strong>Vérification en cours...</strong>
                                <p style="margin: 5px 0 0 0;">Veuillez patienter pendant la vérification du statut de la migration.</p>
                            </div>
                        </div>
                    </div>

                    <div id="migration-actions" style="display: none;">
                        <h5>📦 Modules à installer :</h5>
                        <ul style="margin-bottom: 20px;">
                            <li><strong>Plans de Service</strong> - Gestion des offres et abonnements</li>
                            <li><strong>Abonnements Patients</strong> - Suivi des souscriptions</li>
                            <li><strong>Facturation</strong> - Génération et gestion des factures</li>
                            <li><strong>Paiements</strong> - Enregistrement des paiements (PayPal, Virement, etc.)</li>
                            <li><strong>Commissions</strong> - Calcul automatique des revenus (60/40 ou 80/20)</li>
                            <li><strong>Dashboard Revenus</strong> - Suivi des revenus par diététicien</li>
                        </ul>

                        <div class="alert alert-warning">
                            <i class="fa fa-exclamation-triangle"></i>
                            <strong>Important :</strong> Cette migration va créer 8 nouvelles tables dans la base de données.
                            Assurez-vous d'avoir une sauvegarde avant de continuer.
                        </div>

                        <button type="button" id="btn-run-migration" class="btn btn-primary btn-run-migration">
                            <i class="fa fa-play-circle"></i> Exécuter la Migration
                        </button>

                        <button type="button" id="btn-check-migration" class="btn btn-default" style="margin-left: 10px;">
                            <i class="fa fa-refresh"></i> Vérifier à nouveau
                        </button>
                    </div>

                    <div id="migration-completed" style="display: none;">
                        <div class="alert alert-success">
                            <i class="fa fa-check-circle"></i>
                            <strong>Migration déjà effectuée !</strong> Les tables sont déjà créées dans la base de données.
                        </div>

                        <h5>✅ Tables créées :</h5>
                        <ul>
                            <li><?php echo db_prefix(); ?>dietic_service_plans</li>
                            <li><?php echo db_prefix(); ?>dietic_subscriptions</li>
                            <li><?php echo db_prefix(); ?>dietic_invoices</li>
                            <li><?php echo db_prefix(); ?>dietic_payments</li>
                            <li><?php echo db_prefix(); ?>dietic_commission_settings</li>
                            <li><?php echo db_prefix(); ?>dietic_revenue_shares</li>
                            <li><?php echo db_prefix(); ?>dietic_commission_payments</li>
                            <li><?php echo db_prefix(); ?>dietic_invoice_sequence</li>
                        </ul>
                    </div>

                    <div id="migration-log" class="migration-log"></div>
                </div>
            </div>
        </div>

        <!-- Future Settings Sections -->
        <div class="row">
            <div class="col-md-6">
                <div class="settings-card">
                    <h4>
                        <i class="fa fa-percent"></i> Configuration Commissions
                    </h4>
                    <p class="text-muted">
                        Gérer les pourcentages de commission entre diététiciens et plateforme.
                    </p>
                    <a href="<?php echo admin_url('dietetic/commissions/settings'); ?>" class="btn btn-info">
                        <i class="fa fa-cog"></i> Configurer
                    </a>
                </div>
            </div>

            <div class="col-md-6">
                <div class="settings-card">
                    <h4>
                        <i class="fa fa-file-text-o"></i> Templates de Factures
                    </h4>
                    <p class="text-muted">
                        Personnaliser l'apparence et le contenu des factures PDF.
                    </p>
                    <a href="<?php echo admin_url('dietetic/invoices/templates'); ?>" class="btn btn-info" disabled>
                        <i class="fa fa-paint-brush"></i> Personnaliser (Bientôt disponible)
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="settings-card">
                    <h4>
                        <i class="fa fa-credit-card"></i> Passerelles de Paiement
                    </h4>
                    <p class="text-muted">
                        Configurer Wave, PayPal et Orange Money pour les paiements de services.
                    </p>
                    <a href="<?php echo admin_url('dietetic'); ?>" class="btn btn-success">
                        <i class="fa fa-cog"></i> Configurer
                    </a>
                </div>
            </div>

            <div class="col-md-6">
                <div class="settings-card">
                    <h4>
                        <i class="fa fa-bell"></i> Notifications
                    </h4>
                    <p class="text-muted">
                        Configurer les notifications pour les paiements et abonnements.
                    </p>
                    <a href="<?php echo admin_url('dietetic/settings/notifications'); ?>" class="btn btn-info" disabled>
                        <i class="fa fa-bell-o"></i> Configurer (Bientôt disponible)
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
$(document).ready(function() {
    // Check migration status on load
    checkMigrationStatus();

    // Run migration button
    $('#btn-run-migration').on('click', function() {
        if (!confirm('Êtes-vous sûr de vouloir exécuter la migration ?\n\nCela va créer 8 nouvelles tables dans la base de données.')) {
            return;
        }

        var $btn = $(this);
        var originalHtml = $btn.html();

        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Migration en cours...');

        $('#migration-log').html('').addClass('show');
        addLogLine('info', '🚀 Démarrage de la migration...');

        $.ajax({
            url: '<?php echo admin_url('dietetic/settings/run_migration'); ?>',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    addLogLine('success', '✅ Migration réussie!');
                    addLogLine('info', '📊 ' + response.details.success_count + ' commandes exécutées');

                    if (response.details.created_tables && response.details.created_tables.length > 0) {
                        addLogLine('info', '📦 Tables créées:');
                        response.details.created_tables.forEach(function(table) {
                            addLogLine('success', '   ✓ ' + table);
                        });
                    }

                    setTimeout(function() {
                        alert_float('success', response.message);
                        location.reload();
                    }, 2000);
                } else {
                    addLogLine('error', '❌ Migration échouée: ' + response.message);
                    if (response.errors && response.errors.length > 0) {
                        response.errors.forEach(function(error) {
                            addLogLine('error', '   • ' + error);
                        });
                    }
                    alert_float('danger', response.message);
                    $btn.prop('disabled', false).html(originalHtml);
                }
            },
            error: function(xhr, status, error) {
                addLogLine('error', '❌ Erreur AJAX: ' + error);
                alert_float('danger', 'Erreur lors de l\'exécution de la migration');
                $btn.prop('disabled', false).html(originalHtml);
            }
        });
    });

    // Check migration button
    $('#btn-check-migration').on('click', function() {
        checkMigrationStatus();
    });

    function checkMigrationStatus() {
        $('#migration-status').removeClass('completed error').addClass('pending')
            .html('<div class="d-flex align-items-center"><i class="fa fa-spinner fa-spin" style="font-size: 24px; margin-right: 15px;"></i><div><strong>Vérification en cours...</strong><p style="margin: 5px 0 0 0;">Veuillez patienter pendant la vérification du statut de la migration.</p></div></div>');

        $.ajax({
            url: '<?php echo admin_url('dietetic/settings/check_migration'); ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.migration_run) {
                    $('#migration-status').removeClass('pending').addClass('completed')
                        .html('<div class="d-flex align-items-center"><i class="fa fa-check-circle" style="font-size: 24px; color: #28a745; margin-right: 15px;"></i><div><strong>Migration effectuée</strong><p style="margin: 5px 0 0 0;">' + response.message + '</p></div></div>');
                    $('#migration-actions').hide();
                    $('#migration-completed').show();
                } else {
                    $('#migration-status').removeClass('pending').addClass('pending')
                        .html('<div class="d-flex align-items-center"><i class="fa fa-exclamation-triangle" style="font-size: 24px; color: #ffc107; margin-right: 15px;"></i><div><strong>Migration requise</strong><p style="margin: 5px 0 0 0;">' + response.message + '</p></div></div>');
                    $('#migration-actions').show();
                    $('#migration-completed').hide();
                }
            },
            error: function() {
                $('#migration-status').removeClass('pending').addClass('error')
                    .html('<div class="d-flex align-items-center"><i class="fa fa-times-circle" style="font-size: 24px; color: #dc3545; margin-right: 15px;"></i><div><strong>Erreur de vérification</strong><p style="margin: 5px 0 0 0;">Impossible de vérifier le statut de la migration</p></div></div>');
            }
        });
    }

    function addLogLine(type, message) {
        var $log = $('#migration-log');
        var $line = $('<div class="log-line ' + type + '"></div>').text(message);
        $log.append($line);
        $log.scrollTop($log[0].scrollHeight);
    }
});
</script>
