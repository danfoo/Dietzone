<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
.notifications-nav {
    background: white;
    border-bottom: 1px solid #e2e8f0;
    margin-bottom: 30px;
}

.notifications-nav-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0;
}

.notifications-nav ul {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    gap: 0;
}

.notifications-nav li {
    margin: 0;
}

.notifications-nav a {
    display: block;
    padding: 15px 25px;
    color: #718096;
    text-decoration: none;
    font-weight: 600;
    border-bottom: 3px solid transparent;
    transition: all 0.3s;
}

.notifications-nav a:hover {
    color: #01807B;
    background: #f7fafc;
}

.notifications-nav a.active {
    color: #01807B;
    border-bottom-color: #01807B;
}

.notifications-nav i {
    margin-right: 5px;
}

.settings-container {
    max-width: 1000px;
    margin: 0 auto;
}

.settings-header {
    margin-bottom: 30px;
}

.settings-header h1 {
    color: #2d3748;
    font-size: 24px;
    margin-bottom: 5px;
}

.settings-header p {
    color: #718096;
    font-size: 14px;
}

.settings-section {
    background: white;
    border-radius: 8px;
    padding: 25px;
    margin-bottom: 20px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.settings-section h3 {
    color: #2d3748;
    font-size: 18px;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 2px solid #e2e8f0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.settings-section h3 i {
    color: #01807B;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    color: #2d3748;
    font-weight: 600;
}

.form-group .help-text {
    display: block;
    margin-top: 5px;
    font-size: 13px;
    color: #718096;
}

.form-group input[type="text"],
.form-group input[type="password"],
.form-group select {
    width: 100%;
    padding: 10px;
    border: 1px solid #cbd5e0;
    border-radius: 5px;
    font-size: 14px;
}

.form-group input[type="text"]:focus,
.form-group input[type="password"]:focus,
.form-group select:focus {
    outline: none;
    border-color: #01807B;
    box-shadow: 0 0 0 3px rgba(1, 128, 123, 0.1);
}

.toggle-switch {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 30px;
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
    background-color: #01807B;
}

input:checked + .toggle-slider:before {
    transform: translateX(30px);
}

.master-toggle {
    background: linear-gradient(135deg, #01807B 0%, #026660 100%);
    color: white;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.master-toggle-content h3 {
    margin: 0 0 5px 0;
    font-size: 18px;
}

.master-toggle-content p {
    margin: 0;
    opacity: 0.9;
    font-size: 14px;
}

.test-notification {
    background: #ebf8ff;
    border: 1px solid #90cdf4;
    border-radius: 8px;
    padding: 20px;
    margin-top: 20px;
}

.test-notification h4 {
    color: #2c5282;
    margin-top: 0;
    margin-bottom: 15px;
}

.test-form {
    display: grid;
    grid-template-columns: 150px 1fr 150px;
    gap: 10px;
    align-items: end;
}

.test-form select,
.test-form input {
    padding: 10px;
    border: 1px solid #90cdf4;
    border-radius: 5px;
}

.test-button {
    padding: 10px 20px;
    background: #2c5282;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s;
}

.test-button:hover {
    background: #2a4365;
}

.save-button {
    padding: 12px 30px;
    background: linear-gradient(135deg, #01807B 0%, #026660 100%);
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}

.save-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.provider-info {
    background: #f7fafc;
    padding: 15px;
    border-radius: 5px;
    margin-top: 10px;
    font-size: 13px;
    color: #4a5568;
}

.provider-info code {
    background: #e2e8f0;
    padding: 2px 6px;
    border-radius: 3px;
    font-family: 'Courier New', monospace;
}
</style>

<div id="wrapper">
    <div class="content">
        <!-- Notifications Navigation -->
        <div class="notifications-nav">
            <div class="notifications-nav-container">
                <ul>
                    <li><a href="<?php echo admin_url('dietetic/notifications/settings'); ?>" class="active"><i class="fa fa-cog"></i> Configuration</a></li>
                    <li><a href="<?php echo admin_url('dietetic/notifications/templates'); ?>"><i class="fa fa-file-text-o"></i> Modèles</a></li>
                    <li><a href="<?php echo admin_url('dietetic/notifications/logs'); ?>"><i class="fa fa-list"></i> Historique</a></li>
                    <li><a href="<?php echo admin_url('dietetic/notifications/milestones'); ?>"><i class="fa fa-trophy"></i> Jalons</a></li>
                </ul>
            </div>
        </div>

        <div class="settings-container">
            <div class="settings-header">
                <h1><i class="fa fa-cog"></i> Paramètres des Notifications</h1>
                <p>Configurez les services SMS, WhatsApp et gérez les préférences de notification</p>
            </div>

            <form method="POST" action="<?php echo admin_url('dietetic/notifications/settings'); ?>">
                <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                <!-- Master Toggle -->
                <div class="master-toggle">
                    <div class="master-toggle-content">
                        <h3>Système de Notifications</h3>
                        <p>Activer ou désactiver toutes les notifications du système</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="notifications_enabled" <?php echo ($settings['notifications_enabled'] ?? '1') == '1' ? 'checked' : ''; ?>>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <!-- SMS Settings (LAM) -->
                <div class="settings-section">
                    <h3><i class="fa fa-mobile"></i> Configuration SMS - LAM</h3>

                    <div class="form-group">
                        <label>Fournisseur SMS</label>
                        <select name="sms_provider">
                            <option value="lam" <?php echo ($settings['sms_provider'] ?? 'lam') == 'lam' ? 'selected' : ''; ?>>LAM (L'Africamobile - Recommandé pour Sénégal)</option>
                            <option value="custom" <?php echo ($settings['sms_provider'] ?? '') == 'custom' ? 'selected' : ''; ?>>Personnalisé</option>
                        </select>
                        <span class="help-text">Sélectionnez votre fournisseur de service SMS</span>
                    </div>

                    <div class="form-group">
                        <label>Account ID LAM <span style="color: red;">*</span></label>
                        <input type="text" name="sms_lam_account_id" value="<?php echo $settings['sms_lam_account_id'] ?? ''; ?>" placeholder="Votre Account ID LAM" required>
                        <span class="help-text">Identifiant de votre compte LAM SMS</span>
                    </div>

                    <div class="form-group">
                        <label>Mot de passe LAM <span style="color: red;">*</span></label>
                        <input type="password" name="sms_lam_password" value="<?php echo $settings['sms_lam_password'] ?? ''; ?>" placeholder="Votre mot de passe LAM" required>
                        <span class="help-text">Mot de passe de votre compte LAM SMS</span>
                    </div>

                    <div class="form-group">
                        <label>Sender ID (Nom de l'expéditeur)</label>
                        <input type="text" name="sms_lam_sender_id" value="<?php echo $settings['sms_lam_sender_id'] ?? 'API_LAMSMS'; ?>" placeholder="API_LAMSMS" maxlength="11">
                        <span class="help-text">Maximum 11 caractères alphanumériques (par défaut: API_LAMSMS)</span>
                    </div>

                    <div class="form-group">
                        <label>URL de retour (Callback URL)</label>
                        <input type="text" name="sms_lam_ret_url" value="<?php echo $settings['sms_lam_ret_url'] ?? site_url('dietetic/sms_callback'); ?>" placeholder="<?php echo site_url('dietetic/sms_callback'); ?>">
                        <span class="help-text">URL pour recevoir les notifications de statut d'envoi SMS</span>
                    </div>

                    <div class="form-group">
                        <label>Priorité SMS</label>
                        <select name="sms_lam_priority">
                            <option value="1" <?php echo ($settings['sms_lam_priority'] ?? '2') == '1' ? 'selected' : ''; ?>>1 - Haute priorité</option>
                            <option value="2" <?php echo ($settings['sms_lam_priority'] ?? '2') == '2' ? 'selected' : ''; ?>>2 - Priorité normale (recommandé)</option>
                            <option value="3" <?php echo ($settings['sms_lam_priority'] ?? '2') == '3' ? 'selected' : ''; ?>>3 - Basse priorité</option>
                        </select>
                        <span class="help-text">Niveau de priorité pour l'envoi des SMS</span>
                    </div>

                    <div class="provider-info">
                        <strong>Configuration LAM SMS :</strong><br>
                        LAM (L'Africamobile) est le fournisseur de services SMS pour le Sénégal. Pour obtenir vos identifiants :
                        <ol style="margin: 10px 0 0 20px;">
                            <li>Visitez <a href="https://developers.lafricamobile.com" target="_blank">developers.lafricamobile.com</a></li>
                            <li>Créez un compte ou connectez-vous</li>
                            <li>Obtenez votre <strong>Account ID</strong> et <strong>mot de passe</strong></li>
                            <li>Configurez votre Sender ID (nom d'expéditeur)</li>
                        </ol>
                        <div style="margin-top: 10px; padding: 10px; background: #fff3cd; border-radius: 5px;">
                            <strong>📚 Documentation:</strong> <a href="https://developers.lafricamobile.com/docs/sms/introduction" target="_blank">Guide d'intégration LAM SMS</a>
                        </div>
                    </div>
                </div>

                <!-- WhatsApp Settings -->
                <div class="settings-section">
                    <h3><i class="fa fa-whatsapp"></i> Configuration WhatsApp</h3>

                    <div class="form-group">
                        <label>Fournisseur WhatsApp</label>
                        <select name="whatsapp_provider">
                            <option value="twilio" <?php echo ($settings['whatsapp_provider'] ?? 'twilio') == 'twilio' ? 'selected' : ''; ?>>Twilio</option>
                            <option value="meta" <?php echo ($settings['whatsapp_provider'] ?? '') == 'meta' ? 'selected' : ''; ?>>Meta (WhatsApp Business API)</option>
                            <option value="custom" <?php echo ($settings['whatsapp_provider'] ?? '') == 'custom' ? 'selected' : ''; ?>>Personnalisé</option>
                        </select>
                        <span class="help-text">Sélectionnez votre fournisseur WhatsApp Business API</span>
                    </div>

                    <div class="form-group">
                        <label>Clé API</label>
                        <input type="password" name="whatsapp_api_key" value="<?php echo $settings['whatsapp_api_key'] ?? ''; ?>" placeholder="Votre clé API WhatsApp">
                        <span class="help-text">Token d'authentification de votre fournisseur</span>
                    </div>

                    <div class="form-group">
                        <label>Numéro WhatsApp Business</label>
                        <input type="text" name="whatsapp_phone_number" value="<?php echo $settings['whatsapp_phone_number'] ?? ''; ?>" placeholder="+221XXXXXXXXX">
                        <span class="help-text">Format international (ex: +221XXXXXXXXX)</span>
                    </div>

                    <div class="provider-info">
                        <strong>Recommandation :</strong><br>
                        Pour une intégration simple et fiable, nous recommandons <strong>Twilio</strong> qui offre :
                        <ul style="margin: 10px 0 0 20px;">
                            <li>Configuration rapide (moins de 15 minutes)</li>
                            <li>Support excellent</li>
                            <li>Tarifs compétitifs pour le Sénégal</li>
                            <li>Documentation complète en français</li>
                        </ul>
                    </div>
                </div>

                <!-- Test Notification -->
                <div class="settings-section">
                    <h3><i class="fa fa-flask"></i> Tester les Notifications</h3>

                    <div class="test-notification">
                        <h4>Envoyer une notification de test</h4>
                        <div class="test-form">
                            <select id="test_channel">
                                <option value="email">Email</option>
                                <option value="sms">SMS</option>
                                <option value="whatsapp">WhatsApp</option>
                            </select>
                            <input type="text" id="test_recipient" placeholder="Destinataire (email, téléphone...)">
                            <button type="button" class="test-button" onclick="sendTestNotification()">
                                <i class="fa fa-send"></i> Tester
                            </button>
                        </div>
                        <div id="testResult" style="margin-top: 10px;"></div>
                    </div>
                </div>

                <!-- Save Button -->
                <div style="text-align: center; margin-top: 30px;">
                    <button type="submit" name="save_settings" class="save-button">
                        <i class="fa fa-save"></i> Enregistrer les Paramètres
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
function sendTestNotification() {
    const channel = document.getElementById('test_channel').value;
    const recipient = document.getElementById('test_recipient').value;
    const resultDiv = document.getElementById('testResult');

    if (!recipient) {
        resultDiv.innerHTML = '<div style="color: #c53030; padding: 10px; background: #fff5f5; border-radius: 5px; margin-top: 10px;">' +
            '<i class="fa fa-exclamation-circle"></i> Veuillez entrer un destinataire' +
            '</div>';
        return;
    }

    resultDiv.innerHTML = '<div style="color: #2c5282; padding: 10px;">' +
        '<i class="fa fa-spinner fa-spin"></i> Envoi en cours...' +
        '</div>';

    $.ajax({
        url: admin_url + 'dietetic/notifications/test_notification',
        type: 'POST',
        data: {
            channel: channel,
            recipient: recipient,
            message: 'Ceci est un message de test du système de notifications DietSenegal. Si vous recevez ce message, votre configuration fonctionne correctement ! 🎉'
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                resultDiv.innerHTML = '<div style="color: #22543d; padding: 10px; background: #f0fff4; border-radius: 5px; margin-top: 10px;">' +
                    '<i class="fa fa-check-circle"></i> ' + response.message +
                    '</div>';
            } else {
                resultDiv.innerHTML = '<div style="color: #c53030; padding: 10px; background: #fff5f5; border-radius: 5px; margin-top: 10px;">' +
                    '<i class="fa fa-exclamation-circle"></i> ' + response.message +
                    '</div>';
            }
        },
        error: function() {
            resultDiv.innerHTML = '<div style="color: #c53030; padding: 10px; background: #fff5f5; border-radius: 5px; margin-top: 10px;">' +
                '<i class="fa fa-exclamation-circle"></i> Erreur lors de l\'envoi du test' +
                '</div>';
        }
    });
}
</script>
