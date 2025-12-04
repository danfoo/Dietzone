<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <!-- Header -->
                        <div class="row">
                            <div class="col-md-12">
                                <h4 class="no-margin">
                                    <i class="fa fa-rocket"></i>
                                    Déploiement OneSignal Migration
                                </h4>
                                <hr class="hr-panel-heading">
                            </div>
                        </div>

                        <!-- Info Alert -->
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i>
                            <strong>Migration Firebase → OneSignal</strong>
                            <p class="mbot0">
                                Cette migration permet d'intégrer OneSignal pour les push notifications,
                                nécessaire pour générer l'APK avec <strong>Median</strong>.
                            </p>
                        </div>

                        <!-- Current Status -->
                        <div class="row" id="current-status">
                            <div class="col-md-6">
                                <div class="well">
                                    <h5><i class="fa fa-database"></i> État actuel</h5>
                                    <p class="text-muted">Vérification en cours...</p>
                                    <div id="status-loading">
                                        <i class="fa fa-spinner fa-spin"></i> Chargement...
                                    </div>
                                    <div id="status-content" style="display:none;">
                                        <!-- Contenu dynamique -->
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="well">
                                    <h5><i class="fa fa-check-circle"></i> Ce qui sera fait</h5>
                                    <ul>
                                        <li>Ajout colonne <code>onesignal_player_id</code></li>
                                        <li>Création des settings OneSignal</li>
                                        <li>Création des index de performance</li>
                                        <li>Table de migration (optionnelle)</li>
                                    </ul>
                                    <p class="text-muted mbot0">
                                        <i class="fa fa-shield"></i>
                                        Migration <strong>non-destructive</strong> : conserve toutes les données Firebase existantes.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Action Button -->
                        <div class="row mtop20">
                            <div class="col-md-12 text-center">
                                <button type="button"
                                        class="btn btn-lg btn-primary"
                                        id="btn-deploy"
                                        onclick="deployOneSignal()">
                                    <i class="fa fa-rocket"></i>
                                    Démarrer la migration
                                </button>
                                <button type="button"
                                        class="btn btn-lg btn-success"
                                        id="btn-already-done"
                                        style="display:none;"
                                        onclick="window.location.href='<?= admin_url('dietetic/notifications/settings') ?>'">
                                    <i class="fa fa-cog"></i>
                                    Aller aux paramètres OneSignal
                                </button>
                            </div>
                        </div>

                        <!-- Output Console -->
                        <div class="row mtop30" id="output-container" style="display:none;">
                            <div class="col-md-12">
                                <h5><i class="fa fa-terminal"></i> Console de déploiement</h5>
                                <pre id="output-console" style="background:#2d2d2d; color:#f8f8f8; padding:20px; border-radius:5px; max-height:500px; overflow-y:auto; font-family:monospace; font-size:12px;"></pre>
                            </div>
                        </div>

                        <!-- Next Steps -->
                        <div class="row mtop30" id="next-steps" style="display:none;">
                            <div class="col-md-12">
                                <div class="alert alert-success">
                                    <h4><i class="fa fa-check-circle"></i> Migration réussie !</h4>
                                    <p><strong>Prochaines étapes :</strong></p>
                                    <ol>
                                        <li>
                                            <strong>Configurer OneSignal</strong> :
                                            <a href="<?= admin_url('dietetic/notifications/settings') ?>" class="btn btn-xs btn-info">
                                                <i class="fa fa-cog"></i> Paramètres
                                            </a>
                                            <ul class="mtop10">
                                                <li>Créer un compte sur <a href="https://onesignal.com" target="_blank">OneSignal.com</a></li>
                                                <li>Récupérer : <strong>App ID</strong> + <strong>REST API Key</strong></li>
                                                <li>Les ajouter dans l'onglet Settings</li>
                                            </ul>
                                        </li>
                                        <li class="mtop10">
                                            <strong>Uploader OneSignalSDKWorker.js</strong>
                                            <ul class="mtop10">
                                                <li>Télécharger depuis <a href="https://dashboard.onesignal.com" target="_blank">OneSignal Dashboard</a></li>
                                                <li>Placer à la racine : <code>/home/trpuftja/app/OneSignalSDKWorker.js</code></li>
                                            </ul>
                                        </li>
                                        <li class="mtop10">
                                            <strong>Configurer Median</strong>
                                            <ul class="mtop10">
                                                <li>Aller sur <a href="https://median.co/dashboard" target="_blank">Median Dashboard</a></li>
                                                <li>App Settings → Push Notifications → OneSignal</li>
                                                <li>Entrer votre OneSignal App ID</li>
                                                <li>Rebuild l'APK</li>
                                            </ul>
                                        </li>
                                    </ol>
                                    <p class="mtop20">
                                        <a href="<?= base_url('ONESIGNAL_MIGRATION.md') ?>" target="_blank" class="btn btn-default">
                                            <i class="fa fa-book"></i> Guide complet
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Back Button -->
                        <div class="row mtop20">
                            <div class="col-md-12">
                                <a href="<?= admin_url('dietetic/notifications') ?>" class="btn btn-default">
                                    <i class="fa fa-arrow-left"></i> Retour aux notifications
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Check current status on page load
$(document).ready(function() {
    checkCurrentStatus();
});

function checkCurrentStatus() {
    $.ajax({
        url: '<?= admin_url('dietetic/notifications/execute_onesignal_migration') ?>',
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            $('#status-loading').hide();
            $('#status-content').show();

            if (response.success && response.already_migrated) {
                $('#status-content').html(`
                    <div class="alert alert-success">
                        <i class="fa fa-check-circle"></i>
                        <strong>Migration déjà effectuée</strong>
                    </div>
                    <p class="text-muted">OneSignal est déjà configuré dans votre base de données.</p>
                `);
                $('#btn-deploy').hide();
                $('#btn-already-done').show();
            } else {
                $('#status-content').html(`
                    <div class="alert alert-warning">
                        <i class="fa fa-exclamation-triangle"></i>
                        <strong>Migration nécessaire</strong>
                    </div>
                    <p class="text-muted">La migration OneSignal n'a pas encore été exécutée.</p>
                `);
            }
        },
        error: function() {
            $('#status-loading').hide();
            $('#status-content').show().html(`
                <div class="alert alert-danger">
                    <i class="fa fa-times-circle"></i>
                    Erreur lors de la vérification du statut
                </div>
            `);
        }
    });
}

function deployOneSignal() {
    var btn = $('#btn-deploy');
    var originalHtml = btn.html();

    // Disable button
    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Déploiement en cours...');

    // Show output console
    $('#output-container').show();
    $('#output-console').text('Initialisation...\n');

    // Execute migration
    $.ajax({
        url: '<?= admin_url('dietetic/notifications/execute_onesignal_migration') ?>',
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#output-console').text(response.output);

                if (!response.already_migrated) {
                    // Show next steps
                    $('#next-steps').slideDown();
                    btn.hide();
                    $('#btn-already-done').show();

                    // Show success alert
                    alert_float('success', 'Migration OneSignal terminée avec succès !');
                } else {
                    alert_float('info', 'Migration déjà effectuée');
                }

                // Refresh status
                checkCurrentStatus();
            } else {
                $('#output-console').text('ERREUR: ' + response.message);
                alert_float('danger', 'Erreur lors de la migration');
                btn.prop('disabled', false).html(originalHtml);
            }
        },
        error: function(xhr, status, error) {
            $('#output-console').text('ERREUR AJAX: ' + error + '\n\nRéponse: ' + xhr.responseText);
            alert_float('danger', 'Erreur de connexion au serveur');
            btn.prop('disabled', false).html(originalHtml);
        }
    });
}
</script>

<?php init_tail(); ?>
