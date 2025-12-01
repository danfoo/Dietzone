<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <i class="fa fa-mobile"></i> <?php echo $title; ?>
                        </h4>

                        <hr class="hr-panel-heading">

                        <!-- Alert Zone -->
                        <div id="alertZone"></div>

                        <!-- Statistics Cards -->
                        <div class="row mtop20">
                            <div class="col-md-3">
                                <div class="panel_s" style="border-left: 4px solid #01807B;">
                                    <div class="panel-body text-center">
                                        <h3 class="bold no-margin" style="color: #01807B;" id="totalTokens">
                                            <i class="fa fa-spinner fa-spin"></i>
                                        </h3>
                                        <p class="text-muted no-margin">Tokens FCM Actifs</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="panel_s" style="border-left: 4px solid #F3911D;">
                                    <div class="panel-body text-center">
                                        <h3 class="bold no-margin" style="color: #F3911D;" id="totalPatients">
                                            <i class="fa fa-spinner fa-spin"></i>
                                        </h3>
                                        <p class="text-muted no-margin">Patients avec Push</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="panel_s" style="border-left: 4px solid #48bb78;">
                                    <div class="panel-body text-center">
                                        <h3 class="bold no-margin" style="color: #48bb78;" id="totalDevices">
                                            <i class="fa fa-spinner fa-spin"></i>
                                        </h3>
                                        <p class="text-muted no-margin">Appareils Enregistrés</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="panel_s" style="border-left: 4px solid #667eea;">
                                    <div class="panel-body text-center">
                                        <h3 class="bold no-margin" style="color: #667eea;" id="sentToday">
                                            <i class="fa fa-spinner fa-spin"></i>
                                        </h3>
                                        <p class="text-muted no-margin">Envoyées Aujourd'hui</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Test Form -->
                        <div class="panel_s mtop20">
                            <div class="panel-body">
                                <h4><i class="fa fa-paper-plane"></i> Envoyer une Notification de Test</h4>
                                <hr>

                                <form id="testPushForm">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="patient_id">
                                                    <i class="fa fa-user"></i> Sélectionner un Patient *
                                                </label>
                                                <select name="patient_id" id="patient_id" class="form-control selectpicker" data-live-search="true" required>
                                                    <option value="">-- Choisir un patient --</option>
                                                    <?php if (!empty($patients)): ?>
                                                        <?php foreach ($patients as $patient): ?>
                                                            <?php if ($patient['fcm_tokens'] > 0): ?>
                                                                <option value="<?php echo $patient['id']; ?>" data-tokens="<?php echo $patient['fcm_tokens']; ?>">
                                                                    <?php echo htmlspecialchars($patient['patient_name']); ?>
                                                                    (<?php echo $patient['fcm_tokens']; ?> appareil<?php echo $patient['fcm_tokens'] > 1 ? 's' : ''; ?>)
                                                                </option>
                                                            <?php endif; ?>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </select>
                                                <?php if (empty($patients) || !array_filter($patients, function($p) { return $p['fcm_tokens'] > 0; })): ?>
                                                    <p class="text-warning mtop10">
                                                        <i class="fa fa-exclamation-triangle"></i>
                                                        Aucun patient n'a de token FCM enregistré.
                                                        <a href="<?php echo admin_url('dietetic/setup/diagnose_push'); ?>">
                                                            Voir le diagnostic
                                                        </a>
                                                    </p>
                                                <?php endif; ?>
                                            </div>

                                            <div class="form-group">
                                                <label for="notification_type">
                                                    <i class="fa fa-tag"></i> Type de Notification
                                                </label>
                                                <select name="notification_type" id="notification_type" class="form-control">
                                                    <option value="test">Test Général</option>
                                                    <option value="reminder_weight">Rappel Pesée</option>
                                                    <option value="reminder_water">Rappel Hydratation</option>
                                                    <option value="reminder_meal">Rappel Repas</option>
                                                    <option value="consultation">Rappel Consultation</option>
                                                    <option value="milestone">Célébration Jalon</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="title">
                                                    <i class="fa fa-header"></i> Titre *
                                                </label>
                                                <input type="text" name="title" id="title" class="form-control"
                                                       value="🔔 Test de Notification Push" required maxlength="100">
                                                <small class="text-muted">Maximum 100 caractères</small>
                                            </div>

                                            <div class="form-group">
                                                <label for="message">
                                                    <i class="fa fa-comment"></i> Message *
                                                </label>
                                                <textarea name="message" id="message" class="form-control" rows="3"
                                                          required maxlength="200">Ceci est une notification de test depuis DietZone. Si vous la recevez, les notifications push fonctionnent correctement ! 👍</textarea>
                                                <small class="text-muted">Maximum 200 caractères</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mtop15">
                                        <div class="col-md-12">
                                            <button type="submit" class="btn btn-primary btn-lg" id="sendBtn">
                                                <i class="fa fa-paper-plane"></i> Envoyer la Notification de Test
                                            </button>

                                            <a href="<?php echo admin_url('dietetic/setup/diagnose_push'); ?>" class="btn btn-info">
                                                <i class="fa fa-stethoscope"></i> Diagnostic Push
                                            </a>

                                            <a href="<?php echo admin_url('dietetic/notifications/logs'); ?>" class="btn btn-default">
                                                <i class="fa fa-list"></i> Voir les Logs
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Recent Test Results -->
                        <div class="panel_s mtop20">
                            <div class="panel-body">
                                <h4><i class="fa fa-history"></i> Historique des Tests</h4>
                                <hr>
                                <div id="testHistory">
                                    <p class="text-muted text-center">
                                        <i class="fa fa-info-circle"></i> Les résultats de vos tests apparaîtront ici
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Instructions -->
                        <div class="alert alert-info mtop20">
                            <h4><i class="fa fa-info-circle"></i> Comment tester les notifications push ?</h4>
                            <ol class="mtop10">
                                <li>Sélectionnez un patient qui a au moins 1 token FCM enregistré</li>
                                <li>Personnalisez le titre et le message si nécessaire</li>
                                <li>Cliquez sur "Envoyer la Notification de Test"</li>
                                <li>Le patient devrait recevoir la notification sur son appareil</li>
                                <li>Vérifiez les logs pour confirmer l'envoi</li>
                            </ol>
                            <p class="mtop10 bold">
                                <i class="fa fa-lightbulb-o"></i> Astuce :
                                Les patients doivent avoir cliqué sur "Activer les Notifications" dans le portail patient pour avoir un token FCM.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
$(document).ready(function() {
    // Load statistics on page load
    loadPushStats();

    // Reload stats every 30 seconds
    setInterval(loadPushStats, 30000);

    // Handle form submission
    $('#testPushForm').on('submit', function(e) {
        e.preventDefault();

        const btn = $('#sendBtn');
        const originalHtml = btn.html();

        // Validate
        const patientId = $('#patient_id').val();
        if (!patientId) {
            showAlert('warning', 'Veuillez sélectionner un patient');
            return;
        }

        const title = $('#title').val();
        const message = $('#message').val();

        if (!title || !message) {
            showAlert('warning', 'Veuillez remplir tous les champs obligatoires');
            return;
        }

        // Disable button
        btn.html('<i class="fa fa-spinner fa-spin"></i> Envoi en cours...').prop('disabled', true);

        // Send test notification
        $.ajax({
            url: '<?php echo admin_url('dietetic/notifications/send_test_push'); ?>',
            type: 'POST',
            data: {
                patient_id: patientId,
                notification_type: $('#notification_type').val(),
                title: title,
                body: message, // Changed from 'message' to 'body' to match controller
                url: '<?php echo site_url('dietetic/portal'); ?>'
            },
            dataType: 'json',
            success: function(response) {
                btn.html(originalHtml).prop('disabled', false);

                if (response.success) {
                    showAlert('success', response.message);
                    addToHistory(response);
                    loadPushStats(); // Refresh stats
                } else {
                    showAlert('danger', response.message || 'Erreur lors de l\'envoi');
                }
            },
            error: function(xhr, status, error) {
                btn.html(originalHtml).prop('disabled', false);
                showAlert('danger', 'Erreur serveur: ' + error);
            }
        });
    });

    // Load push notification statistics
    function loadPushStats() {
        $.ajax({
            url: '<?php echo admin_url('dietetic/notifications/get_push_stats'); ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#totalTokens').text(response.stats.total_tokens);
                    $('#totalPatients').text(response.stats.patients_with_tokens);
                    $('#totalDevices').text(response.stats.total_devices);
                    $('#sentToday').text(response.stats.sent_today);
                }
            },
            error: function() {
                $('#totalTokens').text('--');
                $('#totalPatients').text('--');
                $('#totalDevices').text('--');
                $('#sentToday').text('--');
            }
        });
    }

    // Add test result to history
    function addToHistory(result) {
        const historyDiv = $('#testHistory');

        // Remove "no results" message if present
        if (historyDiv.find('.text-muted').length > 0) {
            historyDiv.empty();
        }

        const timestamp = new Date().toLocaleString('fr-FR');
        const statusBadge = result.success
            ? '<span class="label label-success"><i class="fa fa-check"></i> Envoyé</span>'
            : '<span class="label label-danger"><i class="fa fa-times"></i> Échec</span>';

        const html = `
            <div class="alert ${result.success ? 'alert-success' : 'alert-danger'}" style="margin-bottom: 10px;">
                <div class="row">
                    <div class="col-md-8">
                        <strong>${result.title}</strong><br>
                        <small>${result.message}</small>
                    </div>
                    <div class="col-md-4 text-right">
                        ${statusBadge}<br>
                        <small class="text-muted">${timestamp}</small><br>
                        <small class="text-muted">Patient #${$('#patient_id option:selected').text()}</small>
                        ${result.devices_count ? '<br><small>' + result.devices_count + ' appareil(s)</small>' : ''}
                    </div>
                </div>
            </div>
        `;

        historyDiv.prepend(html);

        // Keep only last 5 results
        if (historyDiv.children().length > 5) {
            historyDiv.children().last().remove();
        }
    }

    // Show alert message
    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' :
                          type === 'warning' ? 'alert-warning' : 'alert-danger';

        const icon = type === 'success' ? 'check-circle' :
                    type === 'warning' ? 'exclamation-triangle' : 'times-circle';

        const html = `
            <div class="alert ${alertClass} alert-dismissible fade in" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <i class="fa fa-${icon}"></i> ${message}
            </div>
        `;

        $('#alertZone').html(html);

        // Auto-dismiss after 5 seconds
        setTimeout(function() {
            $('#alertZone .alert').fadeOut(function() {
                $(this).remove();
            });
        }, 5000);

        // Scroll to top
        $('html, body').animate({scrollTop: 0}, 300);
    }
});
</script>
