<?php
/**
 * Script de mise à jour des templates de notification
 * Ajoute les templates pour inscription patient et réinitialisation mot de passe
 *
 * Usage: Accéder via navigateur: https://app.dietsenegal.net/modules/dietetic/update_notification_templates.php
 */

define('BASEPATH', true);
require_once(__DIR__ . '/../../application/config/app-config.php');

// Connexion à la base de données
$conn = mysqli_connect(APP_DB_HOSTNAME, APP_DB_USERNAME, APP_DB_PASSWORD, APP_DB_NAME);

if (!$conn) {
    die("Erreur de connexion: " . mysqli_connect_error());
}

echo "<h1>Mise à jour des templates de notification DietZone</h1>";
echo "<hr>";

// Fonction pour insérer ou mettre à jour un template
function update_template($conn, $key, $value) {
    $key_escaped = mysqli_real_escape_string($conn, $key);
    $value_escaped = mysqli_real_escape_string($conn, $value);

    // Vérifier si le template existe
    $check_sql = "SELECT COUNT(*) as count FROM tbldietic_settings WHERE setting_key = '$key_escaped'";
    $result = mysqli_query($conn, $check_sql);
    $row = mysqli_fetch_assoc($result);

    if ($row['count'] > 0) {
        // Mettre à jour
        $sql = "UPDATE tbldietic_settings SET setting_value = '$value_escaped' WHERE setting_key = '$key_escaped'";
        $action = "Mis à jour";
    } else {
        // Insérer
        $sql = "INSERT INTO tbldietic_settings (setting_key, setting_value) VALUES ('$key_escaped', '$value_escaped')";
        $action = "Créé";
    }

    if (mysqli_query($conn, $sql)) {
        echo "<p style='color: green;'>✓ $action: $key</p>";
        return true;
    } else {
        echo "<p style='color: red;'>✗ Erreur pour $key: " . mysqli_error($conn) . "</p>";
        return false;
    }
}

// ========================================
// TEMPLATE 1: INSCRIPTION PATIENT
// ========================================
echo "<h2>1. Template Inscription Patient</h2>";

// Email - Subject
$template_key = 'template_patient_registration_subject';
$template_value = 'Bienvenue sur DietZone - Vos identifiants de connexion';
update_template($conn, $template_key, $template_value);

// Email - Body
$template_key = 'template_patient_registration_body';
$template_value = '<h2>Bienvenue sur DietZone !</h2>
<p>Bonjour <strong>{patient_name}</strong>,</p>
<p>Votre compte patient a été créé avec succès.</p>

<h3>Vos identifiants de connexion :</h3>
<ul>
    <li><strong>Email :</strong> {email}</li>
    <li><strong>Téléphone :</strong> {phone}</li>
    <li><strong>Mot de passe :</strong> {password}</li>
</ul>

<p><a href="{login_url}" style="display:inline-block;padding:10px 20px;background:#01807B;color:white;text-decoration:none;border-radius:5px;">Se connecter</a></p>

<p><em>Nous vous recommandons de changer votre mot de passe après votre première connexion.</em></p>

<p>Cordialement,<br>L\'équipe DietZone</p>';
update_template($conn, $template_key, $template_value);

// SMS - Body
$template_key = 'template_patient_registration_sms_body';
$template_value = 'DietZone: Email: {email} / Pass: {password}
app.dietsenegal.net/dietetic/portal';
update_template($conn, $template_key, $template_value);

// WhatsApp - Body
$template_key = 'template_patient_registration_whatsapp_body';
$template_value = '🎉 *Bienvenue sur DietZone !*

Votre compte a été créé avec succès.

*Vos identifiants de connexion :*
📧 Email: {email}
📱 Téléphone: {phone}
🔐 Mot de passe: {password}

🔗 Connectez-vous sur:
{login_url}

💡 _Nous vous recommandons de changer votre mot de passe après votre première connexion._';
update_template($conn, $template_key, $template_value);

// ========================================
// TEMPLATE 2: RÉINITIALISATION MOT DE PASSE
// ========================================
echo "<h2>2. Template Réinitialisation Mot de Passe</h2>";

// Email - Subject
$template_key = 'template_password_reset_subject';
$template_value = 'DietZone - Code de réinitialisation mot de passe';
update_template($conn, $template_key, $template_value);

// Email - Body
$template_key = 'template_password_reset_body';
$template_value = '<h2>Réinitialisation de mot de passe</h2>
<p>Bonjour <strong>{patient_name}</strong>,</p>
<p>Vous avez demandé la réinitialisation de votre mot de passe.</p>

<div style="background:#f5f5f5;padding:20px;margin:20px 0;text-align:center;">
    <p style="font-size:14px;color:#666;margin:0 0 10px;">Votre code de réinitialisation :</p>
    <h1 style="font-size:32px;color:#01807B;letter-spacing:8px;margin:10px 0;">{code}</h1>
    <p style="font-size:12px;color:#999;margin:10px 0 0;">Valide pendant 5 minutes</p>
</div>

<p><strong style="color:#dc3545;">⚠️ Important :</strong></p>
<ul>
    <li>Ne partagez jamais ce code avec qui que ce soit</li>
    <li>L\'équipe DietZone ne vous demandera jamais ce code</li>
    <li>Si vous n\'avez pas demandé cette réinitialisation, ignorez ce message</li>
</ul>

<p>Cordialement,<br>L\'équipe DietZone</p>';
update_template($conn, $template_key, $template_value);

// SMS - Body
$template_key = 'template_password_reset_sms_body';
$template_value = 'DietZone - Code: {code}. Valide 5 min. Ne pas partager.';
update_template($conn, $template_key, $template_value);

// WhatsApp - Body
$template_key = 'template_password_reset_whatsapp_body';
$template_value = '🔐 *DietZone - Réinitialisation mot de passe*

Bonjour {patient_firstname},

Votre code de réinitialisation :
*{code}*

⏱ Valide 5 minutes

⚠️ Ne partagez ce code avec personne.';
update_template($conn, $template_key, $template_value);

// ========================================
// METTRE À JOUR LA LISTE DES TEMPLATES
// ========================================
echo "<h2>3. Mise à jour de la liste des templates</h2>";

// Vérifier si notification_template_types existe
$check_sql = "SELECT COUNT(*) as count FROM tbldietic_settings WHERE setting_key = 'notification_template_types'";
$result = mysqli_query($conn, $check_sql);
$row = mysqli_fetch_assoc($result);

// Liste des templates existants + nouveaux
$template_types = [
    // Nouveaux templates
    'patient_registration',
    'password_reset',
    // Templates existants (copié de Notifications.php)
    'breakfast_reminder', 'lunch_reminder', 'dinner_reminder', 'snack_reminder',
    'program_assigned', 'consultation_reminder', 'food_entry_reminder',
    'weight_reminder', 'milestone',
    'water_reminder',
    'new_message',
    'email_recommendation', 'email_consultation', 'email_milestone',
    'sms_hydration', 'sms_weight_reminder', 'sms_consultation_reminder',
    'whatsapp_program_assigned', 'whatsapp_food_entry_reminder'
];

$template_types_json = json_encode($template_types);

if ($row['count'] > 0) {
    $template_types_escaped = mysqli_real_escape_string($conn, $template_types_json);
    $sql = "UPDATE tbldietic_settings SET setting_value = '$template_types_escaped' WHERE setting_key = 'notification_template_types'";
    $action = "Mis à jour";
} else {
    $template_types_escaped = mysqli_real_escape_string($conn, $template_types_json);
    $sql = "INSERT INTO tbldietic_settings (setting_key, setting_value) VALUES ('notification_template_types', '$template_types_escaped')";
    $action = "Créé";
}

if (mysqli_query($conn, $sql)) {
    echo "<p style='color: green;'>✓ $action: notification_template_types</p>";
} else {
    echo "<p style='color: red;'>✗ Erreur: " . mysqli_error($conn) . "</p>";
}

// ========================================
// RÉCAPITULATIF
// ========================================
echo "<hr>";
echo "<h2>✅ Mise à jour terminée !</h2>";
echo "<p><strong>Templates ajoutés/mis à jour :</strong></p>";
echo "<ul>";
echo "<li>patient_registration (Email, SMS, WhatsApp)</li>";
echo "<li>password_reset (Email, SMS, WhatsApp)</li>";
echo "</ul>";

echo "<p><strong>Variables disponibles :</strong></p>";
echo "<h3>patient_registration:</h3>";
echo "<ul>";
echo "<li>{patient_name} - Nom complet du patient</li>";
echo "<li>{email} - Adresse email</li>";
echo "<li>{phone} - Numéro de téléphone</li>";
echo "<li>{password} - Mot de passe généré</li>";
echo "<li>{login_url} - URL de connexion</li>";
echo "</ul>";

echo "<h3>password_reset:</h3>";
echo "<ul>";
echo "<li>{patient_name} - Nom complet du patient</li>";
echo "<li>{patient_firstname} - Prénom du patient</li>";
echo "<li>{code} - Code OTP à 6 chiffres</li>";
echo "</ul>";

echo "<hr>";
echo "<p><a href='/admin/dietetic/notifications/templates' style='display:inline-block;padding:10px 20px;background:#01807B;color:white;text-decoration:none;border-radius:5px;'>Voir les templates dans l'admin</a></p>";

echo "<p style='color: #999; margin-top: 40px;'><em>Note: Ce script peut être exécuté plusieurs fois sans problème. Il mettra à jour les templates existants.</em></p>";

mysqli_close($conn);
?>
