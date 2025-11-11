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

.templates-container {
    max-width: 1200px;
    margin: 0 auto;
}

.templates-header {
    margin-bottom: 30px;
}

.templates-header h1 {
    color: #2d3748;
    font-size: 24px;
    margin-bottom: 5px;
}

.templates-header p {
    color: #718096;
    font-size: 14px;
}

.template-tabs {
    border-bottom: 2px solid #e2e8f0;
    margin-bottom: 30px;
}

.template-tabs button {
    padding: 12px 24px;
    background: none;
    border: none;
    border-bottom: 3px solid transparent;
    color: #718096;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}

.template-tabs button.active {
    color: #01807B;
    border-bottom-color: #01807B;
}

.template-tabs button:hover {
    color: #01807B;
}

.template-card {
    background: white;
    border-radius: 8px;
    padding: 25px;
    margin-bottom: 20px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.template-card h3 {
    color: #2d3748;
    font-size: 18px;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.template-card h3 i {
    color: #01807B;
}

.template-description {
    color: #718096;
    font-size: 14px;
    margin-bottom: 15px;
    padding: 10px;
    background: #f7fafc;
    border-radius: 5px;
}

.template-variables {
    background: #ebf8ff;
    border: 1px solid #90cdf4;
    border-radius: 5px;
    padding: 15px;
    margin-bottom: 20px;
}

.template-variables h4 {
    color: #2c5282;
    font-size: 14px;
    margin: 0 0 10px 0;
}

.template-variables code {
    background: #2c5282;
    color: white;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 12px;
    margin: 0 3px;
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

.form-group input[type="text"],
.form-group textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #cbd5e0;
    border-radius: 5px;
    font-size: 14px;
}

.form-group textarea {
    min-height: 150px;
    font-family: 'Courier New', monospace;
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #01807B;
    box-shadow: 0 0 0 3px rgba(1, 128, 123, 0.1);
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

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

.char-count {
    float: right;
    color: #718096;
    font-size: 12px;
    margin-top: 5px;
}

.char-count.warning {
    color: #f59e0b;
}

.char-count.error {
    color: #ef4444;
}
</style>

<div id="wrapper">
    <div class="content">
        <!-- Notifications Navigation -->
        <div class="notifications-nav">
            <div class="notifications-nav-container">
                <ul>
                    <li><a href="<?php echo admin_url('dietetic/notifications/settings'); ?>"><i class="fa fa-cog"></i> Configuration</a></li>
                    <li><a href="<?php echo admin_url('dietetic/notifications/templates'); ?>" class="active"><i class="fa fa-file-text-o"></i> Modèles</a></li>
                    <li><a href="<?php echo admin_url('dietetic/notifications/logs'); ?>"><i class="fa fa-list"></i> Historique</a></li>
                    <li><a href="<?php echo admin_url('dietetic/notifications/milestones'); ?>"><i class="fa fa-trophy"></i> Jalons</a></li>
                </ul>
            </div>
        </div>

        <div class="templates-container">
            <div class="templates-header">
                <h1><i class="fa fa-file-text-o"></i> Modèles de Notifications</h1>
                <p>Personnalisez les messages envoyés aux patients par Email, SMS et WhatsApp</p>
            </div>

            <?php echo form_open(admin_url('dietetic/notifications/templates')); ?>

            <div class="template-tabs">
                <button type="button" class="tab-button active" onclick="showTab('email')">
                    <i class="fa fa-envelope"></i> Email
                </button>
                <button type="button" class="tab-button" onclick="showTab('sms')">
                    <i class="fa fa-mobile"></i> SMS
                </button>
                <button type="button" class="tab-button" onclick="showTab('whatsapp')">
                    <i class="fa fa-whatsapp"></i> WhatsApp
                </button>
            </div>

            <!-- Email Templates -->
            <div id="email-tab" class="tab-content active">
                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    <strong>Note:</strong> Modifiez les templates selon vos besoins. Les variables entre accolades seront automatiquement remplacées.
                </div>

                <!-- Recommendation Template -->
                <div class="template-card">
                    <h3><i class="fa fa-lightbulb-o"></i> Nouvelle Recommandation</h3>
                    <div class="template-description">
                        Template utilisé lorsqu'un diététicien ajoute une recommandation au patient
                    </div>

                    <div class="template-variables">
                        <h4>Variables disponibles:</h4>
                        <code>{patient_name}</code>
                        <code>{dietitian_name}</code>
                        <code>{meal_type}</code>
                        <code>{recommendation_text}</code>
                        <code>{portal_url}</code>
                    </div>

                    <div class="form-group">
                        <label>Sujet</label>
                        <input type="text" name="email_recommendation_subject" value="<?php echo htmlspecialchars($templates['email_recommendation']['subject'] ?? '💡 Nouvelle Recommandation Diététique'); ?>">
                    </div>
                    <div class="form-group">
                        <label>Message</label>
                        <textarea name="email_recommendation_body"><?php echo htmlspecialchars($templates['email_recommendation']['body'] ?? 'Bonjour {patient_name},

Votre diététicien {dietitian_name} a ajouté une nouvelle recommandation pour votre {meal_type}.

Consultez vos recommandations sur votre portail patient :
{portal_url}

Suivez ces conseils pour progresser ! 💪'); ?></textarea>
                    </div>
                    <button type="submit" name="template_key" value="email_recommendation" class="save-button">
                        <i class="fa fa-save"></i> Enregistrer ce modèle
                    </button>
                </div>

                <!-- Consultation Template -->
                <div class="template-card">
                    <h3><i class="fa fa-calendar"></i> Consultation Planifiée</h3>
                    <div class="template-description">
                        Template envoyé lors de la planification d'une nouvelle consultation
                    </div>

                    <div class="template-variables">
                        <h4>Variables disponibles:</h4>
                        <code>{patient_name}</code>
                        <code>{dietitian_name}</code>
                        <code>{consultation_date}</code>
                        <code>{consultation_time}</code>
                        <code>{consultation_type}</code>
                    </div>

                    <div class="form-group">
                        <label>Sujet</label>
                        <input type="text" name="email_consultation_subject" value="<?php echo htmlspecialchars($templates['email_consultation']['subject'] ?? '📅 Nouvelle Consultation Planifiée'); ?>">
                    </div>
                    <div class="form-group">
                        <label>Message</label>
                        <textarea name="email_consultation_body"><?php echo htmlspecialchars($templates['email_consultation']['body'] ?? 'Bonjour {patient_name},

Une nouvelle consultation a été planifiée :

👨‍⚕️ Avec : {dietitian_name}
📆 Date : {consultation_date}
🕐 Heure : {consultation_time}
📝 Type : {consultation_type}

Nous avons hâte de vous voir ! 😊'); ?></textarea>
                    </div>
                    <button type="submit" name="template_key" value="email_consultation" class="save-button">
                        <i class="fa fa-save"></i> Enregistrer ce modèle
                    </button>
                </div>

                <!-- Milestone Template -->
                <div class="template-card">
                    <h3><i class="fa fa-trophy"></i> Jalon Atteint</h3>
                    <div class="template-description">
                        Template de célébration envoyé lors de l'atteinte d'un objectif
                    </div>

                    <div class="template-variables">
                        <h4>Variables disponibles:</h4>
                        <code>{patient_name}</code>
                        <code>{milestone_type}</code>
                        <code>{weight_lost}</code>
                        <code>{starting_weight}</code>
                        <code>{current_weight}</code>
                    </div>

                    <div class="form-group">
                        <label>Sujet</label>
                        <input type="text" name="email_milestone_subject" value="<?php echo htmlspecialchars($templates['email_milestone']['subject'] ?? '🎉 Bravo ! Vous avez atteint un objectif !'); ?>">
                    </div>
                    <div class="form-group">
                        <label>Message</label>
                        <textarea name="email_milestone_body"><?php echo htmlspecialchars($templates['email_milestone']['body'] ?? 'Félicitations {patient_name} !

Vous venez d\'atteindre un jalon important : {weight_lost}kg perdus ! 🎊

Poids de départ : {starting_weight}kg
Poids actuel : {current_weight}kg

C\'est une victoire à célébrer ! Continuez comme ça, vous êtes sur la bonne voie ! 💪✨'); ?></textarea>
                    </div>
                    <button type="submit" name="template_key" value="email_milestone" class="save-button">
                        <i class="fa fa-save"></i> Enregistrer ce modèle
                    </button>
                </div>
            </div>

            <!-- SMS Templates -->
            <div id="sms-tab" class="tab-content">
                <div class="alert alert-warning">
                    <i class="fa fa-exclamation-triangle"></i>
                    <strong>Limite SMS:</strong> Les SMS sont limités à 160 caractères. Au-delà, le message sera divisé en plusieurs SMS.
                </div>

                <!-- Hydration Reminder -->
                <div class="template-card">
                    <h3><i class="fa fa-tint"></i> Rappel Hydratation</h3>
                    <div class="template-description">
                        Message court envoyé 3x/jour pour rappeler de boire de l'eau
                    </div>

                    <div class="form-group">
                        <label>Message SMS (max 160 caractères)</label>
                        <textarea name="sms_hydration_body" maxlength="160" onkeyup="updateCharCount(this, 160)"><?php echo htmlspecialchars($templates['sms_hydration']['body'] ?? '💧 N\'oubliez pas de boire de l\'eau ! Votre corps vous remerciera. 🎯 Objectif : 2L/jour'); ?></textarea>
                        <small class="char-count" id="char-count-sms_hydration_body">0/160</small>
                    </div>
                    <button type="submit" name="template_key" value="sms_hydration" class="save-button">
                        <i class="fa fa-save"></i> Enregistrer ce modèle
                    </button>
                </div>

                <!-- Weight Reminder -->
                <div class="template-card">
                    <h3><i class="fa fa-scale"></i> Rappel Pesée</h3>
                    <div class="template-description">
                        Rappel hebdomadaire pour enregistrer le poids
                    </div>

                    <div class="form-group">
                        <label>Message SMS</label>
                        <textarea name="sms_weight_reminder_body" maxlength="160" onkeyup="updateCharCount(this, 160)"><?php echo htmlspecialchars($templates['sms_weight_reminder']['body'] ?? '📊 C\'est l\'heure de votre pesée hebdomadaire ! Prenez quelques minutes pour enregistrer votre poids. 💪'); ?></textarea>
                        <small class="char-count" id="char-count-sms_weight_reminder_body">0/160</small>
                    </div>
                    <button type="submit" name="template_key" value="sms_weight_reminder" class="save-button">
                        <i class="fa fa-save"></i> Enregistrer ce modèle
                    </button>
                </div>

                <!-- Consultation Reminder -->
                <div class="template-card">
                    <h3><i class="fa fa-clock-o"></i> Rappel Consultation</h3>
                    <div class="template-description">
                        Rappel 1 jour avant la consultation
                    </div>

                    <div class="form-group">
                        <label>Message SMS</label>
                        <textarea name="sms_consultation_reminder_body" maxlength="160" onkeyup="updateCharCount(this, 160)"><?php echo htmlspecialchars($templates['sms_consultation_reminder']['body'] ?? '⏰ Rappel : Votre consultation est demain ! 👨‍⚕️ N\'oubliez pas votre rendez-vous. 📋'); ?></textarea>
                        <small class="char-count" id="char-count-sms_consultation_reminder_body">0/160</small>
                    </div>
                    <button type="submit" name="template_key" value="sms_consultation_reminder" class="save-button">
                        <i class="fa fa-save"></i> Enregistrer ce modèle
                    </button>
                </div>
            </div>

            <!-- WhatsApp Templates -->
            <div id="whatsapp-tab" class="tab-content">
                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    <strong>WhatsApp Business:</strong> Les messages WhatsApp peuvent inclure des emojis, des sauts de ligne et des liens.
                </div>

                <!-- Program Assigned -->
                <div class="template-card">
                    <h3><i class="fa fa-clipboard"></i> Programme Assigné</h3>
                    <div class="template-description">
                        Message envoyé lors de l'assignation d'un nouveau programme diététique
                    </div>

                    <div class="template-variables">
                        <h4>Variables disponibles:</h4>
                        <code>{patient_name}</code>
                        <code>{dietitian_name}</code>
                        <code>{program_name}</code>
                        <code>{portal_url}</code>
                    </div>

                    <div class="form-group">
                        <label>Message WhatsApp</label>
                        <textarea name="whatsapp_program_assigned_body"><?php echo htmlspecialchars($templates['whatsapp_program_assigned']['body'] ?? 'Bonjour {patient_name},

📋 Votre diététicien {dietitian_name} vous a assigné un nouveau programme :

🎯 {program_name}

Consultez votre portail patient pour voir les détails et commencer votre programme.

🔗 {portal_url}

Bonne continuation ! 💪'); ?></textarea>
                    </div>
                    <button type="submit" name="template_key" value="whatsapp_program_assigned" class="save-button">
                        <i class="fa fa-save"></i> Enregistrer ce modèle
                    </button>
                </div>

                <!-- Food Entry Reminder -->
                <div class="template-card">
                    <h3><i class="fa fa-file-text"></i> Journal Alimentaire</h3>
                    <div class="template-description">
                        Rappel quotidien pour remplir le journal alimentaire
                    </div>

                    <div class="template-variables">
                        <h4>Variables disponibles:</h4>
                        <code>{patient_name}</code>
                        <code>{portal_url}</code>
                    </div>

                    <div class="form-group">
                        <label>Message WhatsApp</label>
                        <textarea name="whatsapp_food_entry_reminder_body"><?php echo htmlspecialchars($templates['whatsapp_food_entry_reminder']['body'] ?? 'Bonjour {patient_name},

📝 N\'oubliez pas de remplir votre journal alimentaire d\'aujourd\'hui !

Quelques minutes suffisent pour noter vos repas et boissons.

🔗 {portal_url}

Votre suivi régulier est la clé du succès ! 🌟'); ?></textarea>
                    </div>
                    <button type="submit" name="template_key" value="whatsapp_food_entry_reminder" class="save-button">
                        <i class="fa fa-save"></i> Enregistrer ce modèle
                    </button>
                </div>
            </div>

            <?php echo form_close(); ?>

            <div style="margin-top: 30px; padding: 20px; background: #f7fafc; border-radius: 8px;">
                <h4 style="margin-top: 0;">
                    <i class="fa fa-lightbulb-o"></i> Bonnes Pratiques
                </h4>
                <ul style="color: #718096;">
                    <li><strong>Email:</strong> Peut contenir du HTML, des liens et des images. Idéal pour les messages détaillés.</li>
                    <li><strong>SMS:</strong> Court et direct. Utilisez des emojis avec parcimonie. Maximum 160 caractères recommandé.</li>
                    <li><strong>WhatsApp:</strong> Permet les emojis, sauts de ligne et liens. Ton plus personnel et conversationnel.</li>
                    <li><strong>Variables:</strong> Utilisez les variables entre accolades (ex: {patient_name}) pour personnaliser vos messages.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
function showTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });

    // Deactivate all buttons
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('active');
    });

    // Show selected tab
    document.getElementById(tabName + '-tab').classList.add('active');

    // Activate corresponding button
    event.target.closest('.tab-button').classList.add('active');
}

function updateCharCount(textarea, maxLength) {
    const charCountId = 'char-count-' + textarea.name;
    const charCountEl = document.getElementById(charCountId);
    if (charCountEl) {
        const currentLength = textarea.value.length;
        charCountEl.textContent = currentLength + '/' + maxLength;

        // Update color based on length
        charCountEl.classList.remove('warning', 'error');
        if (currentLength > maxLength) {
            charCountEl.classList.add('error');
        } else if (currentLength > maxLength * 0.8) {
            charCountEl.classList.add('warning');
        }
    }
}

// Initialize character counts on page load
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('textarea[maxlength]').forEach(textarea => {
        updateCharCount(textarea, parseInt(textarea.getAttribute('maxlength')));
    });
});
</script>
