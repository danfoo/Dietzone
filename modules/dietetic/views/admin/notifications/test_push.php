<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
.test-push-container {
    max-width: 800px;
    margin: 30px auto;
    padding: 0 20px;
}

.test-push-header {
    margin-bottom: 30px;
}

.test-push-header h1 {
    color: #2d3748;
    font-size: 28px;
    margin-bottom: 5px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.test-push-header p {
    color: #718096;
    font-size: 14px;
}

.test-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    padding: 30px;
    margin-bottom: 20px;
}

.test-card h3 {
    color: #2d3748;
    font-size: 18px;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #e2e8f0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.test-card h3 i {
    color: #4285F4;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    color: #2d3748;
    font-weight: 600;
    font-size: 14px;
}

.form-group input,
.form-group textarea,
.form-group select {
    width: 100%;
    padding: 10px;
    border: 1px solid #cbd5e0;
    border-radius: 5px;
    font-size: 14px;
}

.form-group textarea {
    resize: vertical;
    min-height: 100px;
}

.form-group .help-text {
    display: block;
    margin-top: 5px;
    font-size: 13px;
    color: #718096;
}

.btn-test-send {
    padding: 12px 30px;
    background: linear-gradient(135deg, #4285F4 0%, #3367D6 100%);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-test-send:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 15px rgba(66, 133, 244, 0.3);
}

.btn-test-send:disabled {
    background: #cbd5e0;
    cursor: not-allowed;
    transform: none;
}

.result-box {
    margin-top: 20px;
    padding: 15px;
    border-radius: 8px;
    display: none;
}

.result-box.success {
    background: #d5f4e6;
    border-left: 4px solid #0e6655;
    color: #0e6655;
}

.result-box.error {
    background: #fadbd8;
    border-left: 4px solid #943126;
    color: #943126;
}

.result-box i {
    margin-right: 8px;
}

.info-box {
    background: #e3f2fd;
    border-left: 4px solid #2196F3;
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 20px;
}

.info-box h4 {
    color: #1565C0;
    margin: 0 0 10px 0;
    font-size: 16px;
}

.info-box ul {
    margin: 10px 0 0 20px;
    color: #1976D2;
}

.info-box li {
    margin-bottom: 5px;
}

.quick-test-buttons {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 20px;
}

.btn-quick-test {
    padding: 8px 16px;
    background: white;
    color: #4285F4;
    border: 2px solid #4285F4;
    border-radius: 6px;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-quick-test:hover {
    background: #4285F4;
    color: white;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
}

.stat-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 15px;
    border-radius: 8px;
    text-align: center;
}

.stat-card .stat-value {
    font-size: 28px;
    font-weight: bold;
    margin-bottom: 5px;
}

.stat-card .stat-label {
    font-size: 12px;
    opacity: 0.9;
}
</style>

<div id="wrapper">
    <div class="content">
        <!-- Notifications Navigation -->
        <div class="notifications-nav">
            <div class="notifications-nav-container">
                <ul>
                    <li><a href="<?php echo admin_url('dietetic/notifications/settings'); ?>"><i class="fa fa-cog"></i> Configuration</a></li>
                    <li><a href="<?php echo admin_url('dietetic/notifications/templates'); ?>"><i class="fa fa-file-text-o"></i> Modèles</a></li>
                    <li><a href="<?php echo admin_url('dietetic/notifications/logs'); ?>"><i class="fa fa-list"></i> Historique</a></li>
                    <li><a href="<?php echo admin_url('dietetic/notifications/milestones'); ?>"><i class="fa fa-trophy"></i> Jalons</a></li>
                    <li><a href="<?php echo admin_url('dietetic/notifications/test_push'); ?>" class="active"><i class="fa fa-flask"></i> Test Push</a></li>
                </ul>
            </div>
        </div>

        <div class="test-push-container">
            <div class="test-push-header">
                <h1>
                    <i class="fa fa-flask"></i>
                    Test des Notifications Push
                </h1>
                <p>Testez l'envoi de notifications push Firebase vers vos patients</p>
            </div>

            <!-- Stats -->
            <div class="stats-grid">
                <div class="stat-card" style="background: linear-gradient(135deg, #4285F4 0%, #3367D6 100%);">
                    <div class="stat-value" id="total-tokens">-</div>
                    <div class="stat-label">Tokens FCM Actifs</div>
                </div>
                <div class="stat-card" style="background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);">
                    <div class="stat-value" id="total-patients">-</div>
                    <div class="stat-label">Patients Inscrits</div>
                </div>
                <div class="stat-card" style="background: linear-gradient(135deg, #f6ad55 0%, #ed8936 100%);">
                    <div class="stat-value" id="push-status">-</div>
                    <div class="stat-label">API Firebase</div>
                </div>
            </div>

            <!-- Info Box -->
            <div class="info-box">
                <h4><i class="fa fa-info-circle"></i> À propos des tests</h4>
                <ul>
                    <li>Seuls les patients ayant activé les notifications push recevront les messages</li>
                    <li>Les tokens FCM sont automatiquement enregistrés lors de l'activation</li>
                    <li>L'API utilisée dépend de votre configuration (Legacy ou v1)</li>
                </ul>
            </div>

            <!-- Test Form -->
            <div class="test-card">
                <h3><i class="fa fa-paper-plane"></i> Envoyer une Notification de Test</h3>

                <form id="testPushForm">
                    <div class="form-group">
                        <label>Patient Destinataire</label>
                        <select name="patient_id" id="patient_id" required>
                            <option value="">-- Sélectionnez un patient --</option>
                            <?php if (isset($patients) && !empty($patients)): ?>
                                <?php foreach ($patients as $patient): ?>
                                    <option value="<?php echo $patient['id']; ?>">
                                        <?php echo htmlspecialchars($patient['firstname'] . ' ' . $patient['lastname']); ?>
                                        <?php if (!empty($patient['fcm_tokens'])): ?>
                                            (<?php echo $patient['fcm_tokens']; ?> appareil<?php echo $patient['fcm_tokens'] > 1 ? 's' : ''; ?>)
                                        <?php else: ?>
                                            (Pas de token)
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <span class="help-text">Sélectionnez le patient qui recevra la notification</span>
                    </div>

                    <div class="quick-test-buttons">
                        <button type="button" class="btn-quick-test" onclick="fillQuickTest('appointment')">
                            <i class="fa fa-calendar"></i> Rappel RDV
                        </button>
                        <button type="button" class="btn-quick-test" onclick="fillQuickTest('weight')">
                            <i class="fa fa-balance-scale"></i> Rappel Pesée
                        </button>
                        <button type="button" class="btn-quick-test" onclick="fillQuickTest('message')">
                            <i class="fa fa-comment"></i> Nouveau Message
                        </button>
                        <button type="button" class="btn-quick-test" onclick="fillQuickTest('achievement')">
                            <i class="fa fa-trophy"></i> Objectif Atteint
                        </button>
                    </div>

                    <div class="form-group">
                        <label>Titre de la Notification</label>
                        <input type="text" name="title" id="title" required placeholder="Ex: Rappel de rendez-vous">
                        <span class="help-text">Le titre principal de la notification</span>
                    </div>

                    <div class="form-group">
                        <label>Message</label>
                        <textarea name="body" id="body" required placeholder="Ex: N'oubliez pas votre rendez-vous demain à 10h00"></textarea>
                        <span class="help-text">Le contenu du message de notification</span>
                    </div>

                    <div class="form-group">
                        <label>URL de Destination (Optionnel)</label>
                        <input type="text" name="url" id="url" placeholder="Ex: <?php echo site_url('dietetic/portal/appointments'); ?>">
                        <span class="help-text">Page à ouvrir lorsque l'utilisateur clique sur la notification</span>
                    </div>

                    <button type="submit" class="btn-test-send" id="sendBtn">
                        <i class="fa fa-paper-plane"></i>
                        Envoyer la Notification
                    </button>

                    <div class="result-box" id="resultBox"></div>
                </form>
            </div>

            <!-- Back Link -->
            <a href="<?php echo admin_url('dietetic/notifications/settings'); ?>" class="btn btn-default">
                <i class="fa fa-arrow-left"></i>
                Retour à la configuration
            </a>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
// Load stats on page load
$(document).ready(function() {
    loadStats();
});

// Load statistics
function loadStats() {
    $.ajax({
        url: admin_url + 'dietetic/notifications/get_push_stats',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#total-tokens').text(response.data.total_tokens || 0);
                $('#total-patients').text(response.data.total_patients || 0);
                $('#push-status').html(response.data.api_version === 'v1'
                    ? '<i class="fa fa-check"></i> v1'
                    : response.data.api_version === 'legacy'
                        ? 'Legacy'
                        : 'Désactivé');
            }
        },
        error: function() {
            console.error('Failed to load stats');
        }
    });
}

// Fill quick test templates
function fillQuickTest(type) {
    const templates = {
        appointment: {
            title: 'Rappel de rendez-vous',
            body: 'N\'oubliez pas votre rendez-vous de suivi nutritionnel demain à 10h00 avec votre diététicien.',
            url: '<?php echo site_url("dietetic/portal/appointments"); ?>'
        },
        weight: {
            title: 'Rappel de pesée',
            body: 'Il est temps de vous peser ! N\'oubliez pas d\'enregistrer votre poids dans votre journal.',
            url: '<?php echo site_url("dietetic/portal/weights"); ?>'
        },
        message: {
            title: 'Nouveau message',
            body: 'Vous avez reçu un nouveau message de votre diététicien. Consultez-le maintenant.',
            url: '<?php echo site_url("dietetic/portal/messages"); ?>'
        },
        achievement: {
            title: 'Félicitations ! 🎉',
            body: 'Vous avez atteint votre objectif de perte de poids ! Continuez sur cette belle lancée.',
            url: '<?php echo site_url("dietetic/portal/dashboard"); ?>'
        }
    };

    const template = templates[type];
    if (template) {
        $('#title').val(template.title);
        $('#body').val(template.body);
        $('#url').val(template.url);
    }
}

// Handle form submission
$('#testPushForm').on('submit', function(e) {
    e.preventDefault();

    const btn = $('#sendBtn');
    const resultBox = $('#resultBox');
    const originalHtml = btn.html();

    // Validate patient selection
    if (!$('#patient_id').val()) {
        showResult('error', 'Veuillez sélectionner un patient');
        return;
    }

    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Envoi en cours...');
    resultBox.hide();

    const formData = {
        patient_id: $('#patient_id').val(),
        title: $('#title').val(),
        body: $('#body').val(),
        url: $('#url').val()
    };

    $.ajax({
        url: admin_url + 'dietetic/notifications/send_test_push',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showResult('success', response.message);
                // Reload stats
                loadStats();
            } else {
                showResult('error', response.message);
            }
            btn.prop('disabled', false).html(originalHtml);
        },
        error: function(xhr) {
            const message = xhr.responseJSON?.message || 'Erreur lors de l\'envoi de la notification';
            showResult('error', message);
            btn.prop('disabled', false).html(originalHtml);
        }
    });
});

// Show result message
function showResult(type, message) {
    const resultBox = $('#resultBox');
    const icon = type === 'success' ? 'check-circle' : 'exclamation-circle';

    resultBox.removeClass('success error')
        .addClass(type)
        .html('<i class="fa fa-' + icon + '"></i> ' + message)
        .fadeIn();

    // Auto-hide after 5 seconds
    setTimeout(function() {
        resultBox.fadeOut();
    }, 5000);
}
</script>
