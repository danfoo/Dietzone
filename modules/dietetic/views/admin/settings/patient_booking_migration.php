<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <i class="fa fa-database"></i> Migration : Système de Prise de Rendez-vous Patient
                        </h4>
                        <hr class="hr-panel-heading">

                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i>
                            Cette migration ajoute les champs nécessaires pour permettre aux patients de prendre rendez-vous directement depuis leur portail.
                        </div>

                        <h5>Modifications apportées :</h5>
                        <ul>
                            <li>Ajout du champ <code>booked_by_patient</code> (TINYINT) à la table consultations</li>
                            <li>Extension du statut pour supporter 'pending', 'confirmed', 'rejected'</li>
                            <li>Ajout d'index pour améliorer les performances</li>
                        </ul>

                        <hr>

                        <div id="migrationStatus" class="hide">
                            <!-- Status will be shown here -->
                        </div>

                        <div id="migrationResult" class="hide">
                            <!-- Result will be shown here -->
                        </div>

                        <button type="button" class="btn btn-primary" id="btnCheckMigration">
                            <i class="fa fa-search"></i> Vérifier le statut
                        </button>

                        <button type="button" class="btn btn-success" id="btnRunMigration">
                            <i class="fa fa-play"></i> Exécuter la migration
                        </button>

                        <a href="<?php echo admin_url('dietetic/settings'); ?>" class="btn btn-default">
                            <i class="fa fa-arrow-left"></i> Retour aux paramètres
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Check migration status on page load
    checkMigration();

    // Check migration button
    $('#btnCheckMigration').on('click', function() {
        checkMigration();
    });

    // Run migration button
    $('#btnRunMigration').on('click', function() {
        runMigration();
    });

    function checkMigration() {
        $('#migrationStatus').html('<i class="fa fa-spinner fa-spin"></i> Vérification en cours...').removeClass('hide');

        $.ajax({
            url: '<?php echo admin_url('dietetic/settings/check_patient_booking_migration'); ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.migration_run) {
                    $('#migrationStatus').html(
                        '<div class="alert alert-success">' +
                        '<i class="fa fa-check-circle"></i> ' + response.message +
                        '</div>'
                    );
                    $('#btnRunMigration').prop('disabled', true).html('<i class="fa fa-check"></i> Migration déjà exécutée');
                } else {
                    $('#migrationStatus').html(
                        '<div class="alert alert-warning">' +
                        '<i class="fa fa-exclamation-triangle"></i> ' + response.message +
                        '</div>'
                    );
                    $('#btnRunMigration').prop('disabled', false);
                }
            },
            error: function() {
                $('#migrationStatus').html(
                    '<div class="alert alert-danger">' +
                    '<i class="fa fa-times-circle"></i> Erreur lors de la vérification' +
                    '</div>'
                );
            }
        });
    }

    function runMigration() {
        if (!confirm('Êtes-vous sûr de vouloir exécuter cette migration ?')) {
            return;
        }

        $('#btnRunMigration').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Exécution en cours...');
        $('#migrationResult').removeClass('hide').html('<i class="fa fa-spinner fa-spin"></i> Exécution de la migration...');

        $.ajax({
            url: '<?php echo admin_url('dietetic/settings/run_patient_booking_migration'); ?>',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var html = '<div class="alert alert-success">' +
                               '<h4><i class="fa fa-check-circle"></i> ' + response.message + '</h4>';

                    if (response.details) {
                        html += '<ul>';
                        html += '<li>Requêtes exécutées : <strong>' + response.details.success_count + '</strong></li>';
                        html += '<li>Champ ajouté : <code>' + response.details.field_added + '</code></li>';

                        if (response.details.warnings && response.details.warnings.length > 0) {
                            html += '<li>Avertissements :<ul>';
                            response.details.warnings.forEach(function(warning) {
                                html += '<li>' + warning + '</li>';
                            });
                            html += '</ul></li>';
                        }

                        html += '</ul>';
                    }

                    html += '</div>';

                    $('#migrationResult').html(html);
                    $('#btnRunMigration').html('<i class="fa fa-check"></i> Migration exécutée');

                    // Recheck status
                    setTimeout(function() {
                        checkMigration();
                    }, 1000);
                } else {
                    var html = '<div class="alert alert-danger">' +
                               '<h4><i class="fa fa-times-circle"></i> ' + response.message + '</h4>';

                    if (response.errors && response.errors.length > 0) {
                        html += '<ul>';
                        response.errors.forEach(function(error) {
                            html += '<li>' + error + '</li>';
                        });
                        html += '</ul>';
                    }

                    html += '<p>Requêtes réussies : ' + response.success_count + '</p>';
                    html += '</div>';

                    $('#migrationResult').html(html);
                    $('#btnRunMigration').prop('disabled', false).html('<i class="fa fa-play"></i> Réessayer');
                }
            },
            error: function(xhr, status, error) {
                $('#migrationResult').html(
                    '<div class="alert alert-danger">' +
                    '<h4><i class="fa fa-times-circle"></i> Erreur serveur</h4>' +
                    '<p>' + error + '</p>' +
                    '</div>'
                );
                $('#btnRunMigration').prop('disabled', false).html('<i class="fa fa-play"></i> Réessayer');
            }
        });
    }
});
</script>

<?php init_tail(); ?>
