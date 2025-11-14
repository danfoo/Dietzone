<?php
$active_page = 'notifications';
$page_title = 'Mes Préférences de Notification';
$this->load->view('portal/includes/portal_header');
?>

<style>
    :root {
        --primary-color: #01807B;
        --secondary-color: #FFA74D;
    }

    .preferences-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 0 15px;
    }

    .preferences-header {
        background: white;
        border-radius: 12px;
        padding: 30px;
        margin-bottom: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .preferences-header h1 {
        color: var(--primary-color);
        font-size: 28px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .preferences-header p {
        color: #718096;
        margin: 0;
        font-size: 15px;
    }

    .preference-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .preference-card h3 {
        color: #2d3748;
        font-size: 20px;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .preference-card h3 i {
        color: var(--primary-color);
    }

    .preference-item {
        margin-bottom: 20px;
        padding: 15px;
        background: #f7fafc;
        border-radius: 8px;
        display: flex;
        justify-content: space-between;
        align-items: start;
    }

    .preference-info {
        flex: 1;
    }

    .preference-info h4 {
        color: #2d3748;
        font-size: 16px;
        margin: 0 0 5px 0;
    }

    .preference-info p {
        color: #718096;
        font-size: 14px;
        margin: 0;
    }

    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 30px;
        flex-shrink: 0;
    }

    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #cbd5e0;
        transition: .4s;
        border-radius: 30px;
    }

    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 22px;
        width: 22px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked + .toggle-slider {
        background-color: var(--primary-color);
    }

    input:checked + .toggle-slider:before {
        transform: translateX(30px);
    }

    .preference-details {
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid #e2e8f0;
    }

    .preference-details.hidden {
        display: none;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        color: #2d3748;
        font-weight: 600;
        font-size: 14px;
    }

    .form-group select,
    .form-group input[type="time"],
    .form-group input[type="text"] {
        width: 100%;
        padding: 10px;
        border: 1px solid #cbd5e0;
        border-radius: 5px;
        font-size: 14px;
    }

    .form-group select:focus,
    .form-group input:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(1, 128, 123, 0.1);
    }

    .channels-section {
        margin-top: 20px;
    }

    .channel-option {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
        padding: 10px;
        background: white;
        border-radius: 5px;
        border: 2px solid #e2e8f0;
    }

    .channel-option.active {
        border-color: var(--primary-color);
        background: rgba(1, 128, 123, 0.05);
    }

    .channel-option input[type="checkbox"] {
        width: 20px;
        height: 20px;
        cursor: pointer;
    }

    .channel-option label {
        margin: 0;
        cursor: pointer;
        font-size: 15px;
        color: #2d3748;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .save-button {
        width: 100%;
        padding: 15px;
        background: linear-gradient(135deg, var(--primary-color) 0%, #026660 100%);
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }

    .save-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2);
    }

    .back-link {
        display: inline-block;
        margin: 20px 0;
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 600;
        background: white;
        padding: 10px 20px;
        border-radius: 8px;
        transition: all 0.3s;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .back-link:hover {
        background: var(--primary-color);
        color: white;
        text-decoration: none;
        transform: translateX(-5px);
    }

    .alert-custom {
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: none;
    }

    .alert-custom.success {
        background: #f0fff4;
        border: 1px solid #9ae6b4;
        color: #22543d;
    }

    .alert-custom.error {
        background: #fff5f5;
        border: 1px solid #fc8181;
        color: #c53030;
    }

    @media (max-width: 768px) {
        .preferences-header {
            padding: 20px;
        }

        .preference-card {
            padding: 15px;
        }

        .preference-item {
            flex-direction: column;
            gap: 15px;
        }
    }
</style>

<div class="preferences-container">
    <div class="preferences-header">
        <h1>
            <i class="fa fa-bell"></i>
            Mes Préférences de Notification
        </h1>
        <p>Gérez comment et quand vous souhaitez recevoir vos notifications</p>
    </div>

    <div id="alertBox" class="alert-custom"></div>

    <!-- Test AJAX Buttons (for debugging) -->
    <div style="background: #fff3cd; border: 1px solid #ffc107; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
        <p style="margin: 0 0 10px 0; color: #856404; font-size: 14px;">
            <strong>🔧 Tests de diagnostic:</strong>
        </p>
        <button type="button" id="testAjaxBtn" class="btn btn-warning btn-sm" style="margin-right: 10px;">
            <i class="fa fa-flask"></i> Test connexion AJAX
        </button>
        <button type="button" id="testSaveBtn" class="btn btn-info btn-sm">
            <i class="fa fa-save"></i> Test sauvegarde préférences
        </button>
        <div id="testResult" style="margin-top: 10px; font-weight: bold;"></div>
    </div>

    <form id="preferencesForm">
        <!-- CSRF Token -->
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

        <!-- Reminders Section -->
        <div class="preference-card">
            <h3><i class="fa fa-clock-o"></i> Rappels Automatiques</h3>

            <!-- Weight Reminder -->
            <div class="preference-item">
                <div class="preference-info">
                    <h4>Rappel de Pesée Hebdomadaire</h4>
                    <p>Recevez un rappel chaque semaine pour enregistrer votre poids</p>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" name="reminder_weight" id="reminder_weight" <?php echo $preferences->reminder_weight ? 'checked' : ''; ?>>
                    <span class="toggle-slider"></span>
                </label>
            </div>

            <div id="weight_details" class="preference-details <?php echo !$preferences->reminder_weight ? 'hidden' : ''; ?>">
                <div class="form-group">
                    <label>Jour de la semaine</label>
                    <select name="reminder_weight_day">
                        <option value="monday" <?php echo $preferences->reminder_weight_day == 'monday' ? 'selected' : ''; ?>>Lundi</option>
                        <option value="tuesday" <?php echo $preferences->reminder_weight_day == 'tuesday' ? 'selected' : ''; ?>>Mardi</option>
                        <option value="wednesday" <?php echo $preferences->reminder_weight_day == 'wednesday' ? 'selected' : ''; ?>>Mercredi</option>
                        <option value="thursday" <?php echo $preferences->reminder_weight_day == 'thursday' ? 'selected' : ''; ?>>Jeudi</option>
                        <option value="friday" <?php echo $preferences->reminder_weight_day == 'friday' ? 'selected' : ''; ?>>Vendredi</option>
                        <option value="saturday" <?php echo $preferences->reminder_weight_day == 'saturday' ? 'selected' : ''; ?>>Samedi</option>
                        <option value="sunday" <?php echo $preferences->reminder_weight_day == 'sunday' ? 'selected' : ''; ?>>Dimanche</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Heure du rappel</label>
                    <input type="time" name="reminder_weight_time" value="<?php echo $preferences->reminder_weight_time; ?>">
                </div>
            </div>

            <!-- Water Reminder -->
            <div class="preference-item">
                <div class="preference-info">
                    <h4>Rappel d'Hydratation</h4>
                    <p>Recevez des rappels quotidiens pour boire de l'eau</p>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" name="reminder_water" id="reminder_water" <?php echo $preferences->reminder_water ? 'checked' : ''; ?>>
                    <span class="toggle-slider"></span>
                </label>
            </div>

            <div id="water_details" class="preference-details <?php echo !$preferences->reminder_water ? 'hidden' : ''; ?>">
                <div class="form-group">
                    <label>Heures des rappels (séparées par des virgules)</label>
                    <input type="text" name="reminder_water_times" value="<?php echo $preferences->reminder_water_times; ?>" placeholder="10:00,14:00,18:00">
                    <small style="color: #718096; font-size: 13px;">Exemple: 10:00,14:00,18:00 pour 3 rappels par jour</small>
                </div>
            </div>
        </div>

        <!-- Events Notifications -->
        <div class="preference-card">
            <h3><i class="fa fa-star"></i> Notifications d'Événements</h3>

            <div class="preference-item">
                <div class="preference-info">
                    <h4>Nouvelles Recommandations</h4>
                    <p>Soyez notifié quand votre diététicien ajoute une recommandation</p>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" name="notify_recommendation" <?php echo $preferences->notify_recommendation ? 'checked' : ''; ?>>
                    <span class="toggle-slider"></span>
                </label>
            </div>

            <div class="preference-item">
                <div class="preference-info">
                    <h4>Consultations</h4>
                    <p>Recevez des rappels pour vos consultations à venir</p>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" name="notify_consultation" <?php echo $preferences->notify_consultation ? 'checked' : ''; ?>>
                    <span class="toggle-slider"></span>
                </label>
            </div>

            <div class="preference-item">
                <div class="preference-info">
                    <h4>Célébrations de Jalons</h4>
                    <p>Recevez des félicitations quand vous atteignez vos objectifs (5kg, 10kg, 15kg perdus)</p>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" name="notify_milestone" <?php echo $preferences->notify_milestone ? 'checked' : ''; ?>>
                    <span class="toggle-slider"></span>
                </label>
            </div>

            <div class="preference-item">
                <div class="preference-info">
                    <h4>Programmes Diététiques</h4>
                    <p>Soyez notifié lorsqu'un programme vous est assigné ou modifié</p>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" name="notify_program" <?php echo $preferences->notify_program ? 'checked' : ''; ?>>
                    <span class="toggle-slider"></span>
                </label>
            </div>

            <div class="preference-item">
                <div class="preference-info">
                    <h4>Rappel Journal Alimentaire</h4>
                    <p>Recevez un rappel quotidien pour remplir votre journal alimentaire (18h)</p>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" name="notify_food_entry" <?php echo $preferences->notify_food_entry ? 'checked' : ''; ?>>
                    <span class="toggle-slider"></span>
                </label>
            </div>
        </div>

        <!-- Channels -->
        <div class="preference-card">
            <h3><i class="fa fa-envelope"></i> Canaux de Communication</h3>
            <p style="color: #718096; margin-bottom: 15px;">Choisissez comment vous souhaitez recevoir vos notifications</p>

            <div class="channels-section">
                <div class="channel-option <?php echo $preferences->channel_email ? 'active' : ''; ?>">
                    <input type="checkbox" name="channel_email" id="channel_email" <?php echo $preferences->channel_email ? 'checked' : ''; ?>>
                    <label for="channel_email">
                        <i class="fa fa-envelope"></i>
                        Email
                    </label>
                </div>

                <div class="channel-option <?php echo $preferences->channel_sms ? 'active' : ''; ?>">
                    <input type="checkbox" name="channel_sms" id="channel_sms" <?php echo $preferences->channel_sms ? 'checked' : ''; ?>>
                    <label for="channel_sms">
                        <i class="fa fa-mobile"></i>
                        SMS
                    </label>
                </div>

                <div class="channel-option <?php echo $preferences->channel_whatsapp ? 'active' : ''; ?>">
                    <input type="checkbox" name="channel_whatsapp" id="channel_whatsapp" <?php echo $preferences->channel_whatsapp ? 'checked' : ''; ?>>
                    <label for="channel_whatsapp">
                        <i class="fa fa-whatsapp"></i>
                        WhatsApp
                    </label>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <button type="submit" class="save-button">
            <i class="fa fa-save"></i> Enregistrer mes Préférences
        </button>
    </form>

    <a href="<?php echo site_url('dietetic/portal'); ?>" class="back-link">
        <i class="fa fa-arrow-left"></i> Retour au Portail
    </a>
</div>

<?php $this->load->view('portal/includes/portal_footer'); ?>

<script>
    // Script placé APRÈS le footer pour que jQuery soit chargé
    $(document).ready(function() {
        console.log('✅ Document ready - jQuery version:', jQuery.fn.jquery);

        // Test AJAX connection button
        $('#testAjaxBtn').on('click', function(e) {
            e.preventDefault();
            console.log('🧪 Test AJAX button clicked');
            alert('Bouton cliqué! Test en cours...');

            var resultSpan = $('#testResult');
            resultSpan.html('<i class="fa fa-spinner fa-spin"></i> Test en cours...').css('color', '#0066cc');

            var testUrl = '<?php echo site_url('dietetic/portal/test_ajax_endpoint'); ?>';
            console.log('🔵 URL de test:', testUrl);

            $.ajax({
                url: testUrl,
                type: 'POST',
                data: {
                    test: 'ajax_connectivity',
                    timestamp: new Date().toISOString(),
                    '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
                },
                dataType: 'json',
                success: function(response) {
                    console.log('✅ Test AJAX Success:', response);
                    alert('Succès! ' + response.message);
                    resultSpan.html('<i class="fa fa-check-circle"></i> ' + response.message).css('color', '#28a745');
                },
                error: function(xhr, status, error) {
                    console.error('❌ Test AJAX Error:', {xhr: xhr, status: status, error: error, responseText: xhr.responseText});
                    alert('Erreur AJAX: ' + status + ' - ' + error);
                    resultSpan.html('<i class="fa fa-times-circle"></i> Échec: ' + error).css('color', '#dc3545');
                }
            });
        });

        // Test save preferences button
        $('#testSaveBtn').on('click', function(e) {
            e.preventDefault();
            console.log('💾 Test Save button clicked');

            var resultDiv = $('#testResult');
            resultDiv.html('<i class="fa fa-spinner fa-spin"></i> Test sauvegarde en cours...').css('color', '#0066cc');

            var saveUrl = '<?php echo site_url('dietetic/portal/save_notification_preferences'); ?>';
            console.log('🔵 URL de sauvegarde:', saveUrl);

            // Test data
            var testData = {
                reminder_weight: 1,
                channel_email: 1,
                channel_sms: 0,
                '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
            };

            console.log('🔵 Test data:', testData);

            $.ajax({
                url: saveUrl,
                type: 'POST',
                data: testData,
                dataType: 'json',
                success: function(response) {
                    console.log('✅ Save Test Success:', response);
                    resultDiv.html('<i class="fa fa-check-circle"></i> Sauvegarde réussie: ' + response.message).css('color', '#28a745');
                },
                error: function(xhr, status, error) {
                    console.error('❌ Save Test Error:', {xhr: xhr, status: status, error: error, responseText: xhr.responseText});
                    resultDiv.html('<i class="fa fa-times-circle"></i> Erreur: ' + status + ' - ' + error).css('color', '#dc3545');
                }
            });
        });

        // Toggle weight reminder details
        $('#reminder_weight').change(function() {
            if ($(this).is(':checked')) {
                $('#weight_details').removeClass('hidden');
            } else {
                $('#weight_details').addClass('hidden');
            }
        });

        // Toggle water reminder details
        $('#reminder_water').change(function() {
            if ($(this).is(':checked')) {
                $('#water_details').removeClass('hidden');
            } else {
                $('#water_details').addClass('hidden');
            }
        });

        // Toggle channel option styling
        $('input[name^="channel_"]').change(function() {
            if ($(this).is(':checked')) {
                $(this).closest('.channel-option').addClass('active');
            } else {
                $(this).closest('.channel-option').removeClass('active');
            }
        });

        // Handle form submission
        console.log('📝 Attaching form submit handler to #preferencesForm');

        $('#preferencesForm').on('submit', function(e) {
            e.preventDefault();
            console.log('🔵 Form submission triggered!');

            const btn = $('.save-button');
            const originalText = btn.html();
            const formData = $(this).serialize();

            console.log('🔵 Form data:', formData);
            console.log('🔵 AJAX URL:', '<?php echo site_url('dietetic/portal/save_notification_preferences'); ?>');

            btn.html('<i class="fa fa-spinner fa-spin"></i> Enregistrement...').prop('disabled', true);

            $.ajax({
                url: '<?php echo site_url('dietetic/portal/save_notification_preferences'); ?>',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    console.log('✅ AJAX Success:', response);
                    showAlert(response.success ? 'success' : 'error', response.message);

                    btn.html(originalText).prop('disabled', false);

                    if (response.success) {
                        // Scroll to top to show alert
                        $('html, body').animate({scrollTop: 0}, 300);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('❌ AJAX Error:', {
                        status: status,
                        error: error,
                        responseText: xhr.responseText,
                        statusCode: xhr.status
                    });
                    showAlert('error', 'Une erreur est survenue lors de l\'enregistrement');
                    btn.html(originalText).prop('disabled', false);
                }
            });
        });

        // Verify handler is attached
        console.log('✅ Form submit handler attached. Form exists:', $('#preferencesForm').length > 0);

        function showAlert(type, message) {
            const alertBox = $('#alertBox');
            alertBox.removeClass('success error');
            alertBox.addClass(type);
            alertBox.html('<i class="fa fa-' + (type === 'success' ? 'check-circle' : 'exclamation-circle') + '"></i> ' + message);
            alertBox.fadeIn();

            setTimeout(function() {
                alertBox.fadeOut();
            }, 5000);
        }
    });
</script>
