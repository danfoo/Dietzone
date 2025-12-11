# Inscription avec Validation OTP

## Vue d'ensemble

Le système d'inscription des patients nécessite maintenant une **validation par code OTP** envoyé par SMS pour sécuriser les inscriptions et éviter les comptes fictifs.

## Flux d'inscription complet

```
┌─────────────────────────────────────────────────────────────────┐
│ 1. FORMULAIRE D'INSCRIPTION                                     │
│    https://app.dietsenegal.net/dietetic/portal                  │
│    - Prénom, Nom                                                │
│    - Email                                                      │
│    - Téléphone                                                  │
│    - Mot de passe                                               │
└──────────────────┬──────────────────────────────────────────────┘
                   │ POST /dietetic/portal/register
                   ▼
┌─────────────────────────────────────────────────────────────────┐
│ 2. GÉNÉRATION ET ENVOI DU CODE OTP                             │
│    ✓ Validation des champs (nom, email, téléphone, etc.)      │
│    ✓ Vérification des doublons (email + téléphone)            │
│    ✓ Génération d'un code à 6 chiffres (ex: 482759)           │
│    ✓ Stockage temporaire dans session (15 min max)            │
│    ✓ Enregistrement dans tbldietic_otp_codes                  │
│    ✓ Envoi SMS: "DietZone - Code: 482759. Valide 5 min."      │
│    ✓ Envoi Email (backup): Code avec design HTML              │
└──────────────────┬──────────────────────────────────────────────┘
                   │ Redirect to /verify_registration_otp
                   ▼
┌─────────────────────────────────────────────────────────────────┐
│ 3. PAGE DE VALIDATION OTP                                       │
│    https://app.dietsenegal.net/dietetic/portal/                │
│         verify_registration_otp                                 │
│                                                                 │
│    Interface moderne avec:                                     │
│    • 6 champs séparés pour le code OTP                         │
│    • Timer de 5 minutes (affichage dynamique)                  │
│    • Bouton "Renvoyer le code" (cooldown 30s)                  │
│    • Auto-focus et navigation clavier                          │
│    • Support copier-coller                                     │
│    • Numéro masqué: +221****4567                               │
└──────────────────┬──────────────────────────────────────────────┘
                   │ POST avec code OTP
                   ▼
┌─────────────────────────────────────────────────────────────────┐
│ 4. VÉRIFICATION DU CODE                                         │
│    ✓ Recherche du code dans tbldietic_otp_codes               │
│    ✓ Vérification:                                             │
│      - Téléphone correspond                                    │
│      - Type = 'registration'                                   │
│      - Code non utilisé (used = 0)                             │
│      - Pas expiré (expires_at > now)                           │
│                                                                 │
│    Si INVALIDE: Message d'erreur + possibilité de renvoyer    │
│    Si VALIDE: Continuer ↓                                      │
└──────────────────┬──────────────────────────────────────────────┘
                   │
                   ▼
┌─────────────────────────────────────────────────────────────────┐
│ 5. CRÉATION DU COMPTE                                           │
│    Transaction MySQL:                                          │
│    1. Créer CLIENT dans tblclients                             │
│    2. Créer CONTACT dans tblcontacts                           │
│       • password hashé                                         │
│       • email_verified_at = now()                              │
│       • phonenumber_verified_at = now() ✨ NOUVEAU             │
│    3. Créer PATIENT dans tbldietic_patients                    │
│    4. Marquer l'OTP comme utilisé (used = 1)                   │
└──────────────────┬──────────────────────────────────────────────┘
                   │
                   ▼
┌─────────────────────────────────────────────────────────────────┐
│ 6. POST-INSCRIPTION                                             │
│    ✓ Supprimer les données temporaires de la session          │
│    ✓ Envoyer notifications multi-canal:                       │
│      - Email: Identifiants + lien de connexion                │
│      - SMS: Résumé compact                                     │
│      - WhatsApp: Message formaté avec emojis                   │
│    ✓ Connecter automatiquement le patient                     │
│    ✓ Rediriger vers le dashboard du portail                   │
└─────────────────────────────────────────────────────────────────┘
```

## Endpoints et routes

### 1. POST `/dietetic/portal/register`
**Description:** Inscription initiale (génère et envoie l'OTP)

**Données POST:**
```php
[
    'firstname' => 'Mamadou',
    'lastname' => 'Diop',
    'email' => 'mamadou@example.com',
    'phone_full' => '+221771234567',
    'password' => 'monMotDePasse123',
    'password_confirm' => 'monMotDePasse123'
]
```

**Actions:**
- Validation des champs
- Vérification doublons (email + téléphone)
- Génération code OTP (6 chiffres)
- Stockage session `pending_registration`
- Insertion dans `tbldietic_otp_codes`
- Envoi SMS + Email
- Redirect vers `verify_registration_otp`

**Logs générés:**
```
INSCRIPTION MOBILE - Code OTP envoyé par SMS au +221771234567 pour Mamadou Diop
```

---

### 2. GET/POST `/dietetic/portal/verify_registration_otp`
**Description:** Afficher et traiter le formulaire de validation OTP

**GET:**
- Affiche le formulaire de validation
- Vérifie qu'il y a une inscription en attente (session)
- Vérifie que l'inscription n'est pas expirée (15 min max)
- Affiche le numéro masqué

**POST:**
```php
[
    'otp_code' => '482759'
]
```

**Actions:**
- Recherche du code dans la BDD
- Vérification validité (téléphone, type, non utilisé, non expiré)
- Si valide:
  - Marquer code comme utilisé
  - Créer le compte complet
  - Envoyer notifications
  - Connecter automatiquement
  - Redirect vers dashboard
- Si invalide:
  - Message d'erreur
  - Possibilité de renvoyer le code

**Logs générés (succès):**
```
INSCRIPTION MOBILE (OTP validé) - Nouveau patient créé: Mamadou Diop (Client ID: 123, Email: mamadou@example.com, Téléphone: +221771234567)
```

---

### 3. POST `/dietetic/portal/resend_registration_otp`
**Description:** Renvoyer un nouveau code OTP

**Actions:**
- Vérifier qu'il y a une inscription en attente
- Générer un nouveau code OTP
- Insérer dans `tbldietic_otp_codes`
- Envoyer par SMS
- Redirect vers `verify_registration_otp`

**Cooldown:** 30 secondes (géré côté client JavaScript)

**Logs générés:**
```
INSCRIPTION MOBILE - Nouveau code OTP envoyé par SMS au +221771234567
```

## Base de données

### Table: `tbldietic_otp_codes`

**Exemple d'enregistrement:**
```sql
INSERT INTO tbldietic_otp_codes VALUES (
    NULL,                       -- id (auto-increment)
    '+221771234567',            -- phone
    '482759',                   -- code
    'registration',             -- type
    0,                          -- used
    NULL,                       -- used_at
    '2024-12-11 10:30:00',      -- created_at
    '2024-12-11 10:35:00'       -- expires_at (5 minutes)
);
```

**Types possibles:**
- `registration` - Code pour inscription
- `password_reset` - Code pour réinitialisation mot de passe
- `phone_verification` - Code pour vérification téléphone

---

### Table: `tblcontacts`

**Nouveau champ ajouté:**
```sql
phonenumber_verified_at DATETIME NULL
```

Ce champ est rempli lors de la validation OTP :
```php
'phonenumber_verified_at' => date('Y-m-d H:i:s')
```

Cela permet de distinguer les numéros vérifiés des numéros non vérifiés.

---

### Session temporaire

**Clé session:** `pending_registration`

**Données stockées:**
```php
[
    'firstname' => 'Mamadou',
    'lastname' => 'Diop',
    'email' => 'mamadou@example.com',
    'phone_full' => '+221771234567',
    'password' => 'monMotDePasse123',    // En clair temporairement
    'timestamp' => 1702289400             // Pour expiration (15 min)
]
```

**Durée de vie:** 15 minutes maximum

**Suppression:**
- Après validation OTP réussie
- Après expiration du délai

## Sécurité

### 1. Protection contre les doublons

Avant de générer l'OTP, vérification de l'unicité:

**Email:**
```php
$this->db->where('email', $email);
$email_exists = $this->db->count_all_results(db_prefix() . 'contacts') > 0;
```

**Téléphone** (plusieurs variations):
```php
$phone_variations = [
    '771234567',
    '+221771234567',
    '221771234567',
    // ...
];
```

### 2. Expiration des codes

**Codes OTP:** 5 minutes
```php
$expires_at = date('Y-m-d H:i:s', strtotime('+5 minutes'));
```

**Session d'inscription:** 15 minutes
```php
if (time() - $pending['timestamp'] > 900) {
    // Expirée
}
```

### 3. Protection contre le spam

**Côté serveur:**
- Codes expirables
- Marquage des codes utilisés
- Vérification des doublons

**Côté client:**
- Cooldown de 30 secondes sur le bouton "Renvoyer"
- Timer visuel (5 minutes)

### 4. Stockage sécurisé

**Mot de passe:**
- Stocké en clair dans la session temporaire (15 min max)
- Hashé avec `app_hash_password()` lors de la création du compte
- Jamais stocké en clair dans la BDD

**Code OTP:**
- Stocké en clair dans la BDD (nécessaire pour validation)
- Marqué comme utilisé après validation
- Expiré automatiquement après 5 minutes

## Messages SMS/Email

### SMS (max 160 caractères)

**Envoi du code OTP:**
```
DietZone - Code de validation: 482759. Valide 5 min. Ne pas partager.
```
**Longueur:** 71 caractères ✓

---

### Email OTP (backup)

**Sujet:** `DietZone - Code de validation de votre inscription`

**Corps:** HTML avec design moderne
- Code en gros (32px)
- Instructions claires
- Avertissements de sécurité
- Durée de validité (5 min)

---

### SMS post-inscription

```
DietZone: Email: mamadou@example.com / Pass: monMotDePasse123
app.dietsenegal.net/dietetic/portal
```
**Longueur:** Variable (< 160 caractères)

## Interface utilisateur

### Formulaire de validation OTP

**Design:**
- ✨ 6 champs séparés (1 chiffre chacun)
- 📱 Optimisé mobile (input numérique)
- ⏱️ Timer de 5 minutes avec code couleur
- 🔄 Bouton "Renvoyer" avec cooldown
- 📋 Support copier-coller de codes
- ⌨️ Navigation clavier (tab, backspace)
- ✅ Auto-focus sur le premier champ

**États du timer:**
- ✅ **Vert** (> 2 minutes): Normal
- ⚠️ **Orange** (1-2 minutes): Avertissement
- 🔴 **Rouge** (< 1 minute): Urgent
- ❌ **Rouge + message** (expiré): Bloqué

**Validation:**
- Bouton désactivé tant que les 6 chiffres ne sont pas entrés
- Champs désactivés après expiration du code
- Animation de "shake" en cas d'erreur

### Responsive

**Mobile:**
- Champs OTP: 45x55px
- Padding réduit
- Font-size: 22px

**Desktop:**
- Champs OTP: 50x60px
- Padding standard
- Font-size: 24px

## Comment tester

### 1. Inscription d'un nouveau patient

**URL:** `https://app.dietsenegal.net/dietetic/portal`

**Étapes:**
1. Cliquer sur "S'inscrire" ou "Créer un compte"
2. Remplir le formulaire:
   ```
   Prénom: Test
   Nom: Patient
   Email: test.patient@example.com
   Téléphone: +221771234567
   Mot de passe: Test1234
   Confirmer: Test1234
   ```
3. Cliquer sur "S'inscrire"

**Résultat attendu:**
- ✅ Redirection vers `/verify_registration_otp`
- ✅ Message: "Un code a été envoyé par SMS au +221****4567"
- ✅ Réception d'un SMS avec le code (ex: 482759)
- ✅ Réception d'un email avec le code

---

### 2. Validation du code OTP

**Page:** `https://app.dietsenegal.net/dietetic/portal/verify_registration_otp`

**Étapes:**
1. Entrer les 6 chiffres reçus par SMS: `4 8 2 7 5 9`
2. Cliquer sur "Valider mon inscription"

**Résultat attendu:**
- ✅ Compte créé dans la BDD
- ✅ Notifications envoyées (Email, SMS, WhatsApp)
- ✅ Connexion automatique
- ✅ Redirection vers le dashboard
- ✅ Message: "Bienvenue Test ! Votre compte a été créé et validé avec succès."

**Logs à vérifier:**
```sql
SELECT * FROM tblactivitylog
WHERE description LIKE '%INSCRIPTION MOBILE%'
ORDER BY date DESC
LIMIT 5;
```

---

### 3. Cas d'erreur: Code invalide

**Étapes:**
1. Entrer un code incorrect: `1 2 3 4 5 6`
2. Cliquer sur "Valider"

**Résultat attendu:**
- ❌ Message d'erreur: "Code invalide ou expiré"
- ✅ Possibilité de réessayer
- ✅ Possibilité de renvoyer un nouveau code

---

### 4. Cas d'erreur: Code expiré

**Étapes:**
1. Attendre 5 minutes sans entrer le code
2. Observer le timer passer à 0:00

**Résultat attendu:**
- ❌ Champs OTP désactivés
- ❌ Bouton "Valider" désactivé
- ⚠️ Message: "Code expiré. Demandez un nouveau code."
- ✅ Bouton "Renvoyer le code" actif

---

### 5. Renvoyer un nouveau code

**Étapes:**
1. Cliquer sur "Renvoyer le code"

**Résultat attendu:**
- ✅ Nouveau code généré et envoyé par SMS
- ✅ Message: "Un nouveau code a été envoyé par SMS"
- ✅ Timer réinitialisé à 5:00
- ✅ Bouton "Renvoyer" désactivé pendant 30 secondes (cooldown)

---

### 6. Cas d'erreur: Email déjà utilisé

**Étapes:**
1. Tenter de s'inscrire avec un email existant

**Résultat attendu:**
- ❌ Message: "Un compte existe déjà avec cet email"
- ✅ Aucun code OTP envoyé
- ✅ Redirection vers la page d'inscription

---

### 7. Vérifier en base de données

**Codes OTP générés:**
```sql
SELECT * FROM tbldietic_otp_codes
WHERE type = 'registration'
ORDER BY created_at DESC
LIMIT 10;
```

**Nouveau patient créé:**
```sql
SELECT
    c.userid,
    c.company as nom_complet,
    cont.email,
    cont.phonenumber,
    cont.email_verified_at,
    cont.phonenumber_verified_at,
    p.id as patient_id,
    p.status
FROM tblclients c
JOIN tblcontacts cont ON cont.userid = c.userid AND cont.is_primary = 1
JOIN tbldietic_patients p ON p.client_id = c.userid
WHERE cont.email = 'test.patient@example.com';
```

**Champs à vérifier:**
- ✅ `phonenumber_verified_at` doit être rempli (date/heure)
- ✅ `email_verified_at` doit être rempli
- ✅ `p.status` = 'active'

## Dépannage

### Problème: SMS non reçu

**Vérifications:**
1. Credentials LAM SMS configurés:
   ```sql
   SELECT * FROM tbldietic_notification_settings
   WHERE setting_key IN ('sms_lam_account_id', 'sms_lam_password');
   ```

2. Consulter les logs:
   ```sql
   SELECT * FROM tblactivitylog
   WHERE description LIKE '%SMS%'
   ORDER BY date DESC
   LIMIT 10;
   ```

3. Tester l'API SMS directement:
   ```
   https://app.dietsenegal.net/modules/dietetic/test_sms_api.php?phone=221771234567
   ```

---

### Problème: Session expirée

**Erreur:** "Aucune inscription en attente. Veuillez recommencer."

**Causes:**
- Session PHP expirée
- Délai de 15 minutes dépassé
- Cookies bloqués

**Solution:** Recommencer l'inscription depuis le début

---

### Problème: Code toujours invalide

**Vérifications:**
1. Vérifier que le code n'est pas expiré:
   ```sql
   SELECT *,
          TIMESTAMPDIFF(MINUTE, NOW(), expires_at) as minutes_restantes
   FROM tbldietic_otp_codes
   WHERE phone = '+221771234567'
   AND type = 'registration'
   ORDER BY created_at DESC
   LIMIT 1;
   ```

2. Vérifier que le code n'a pas déjà été utilisé:
   ```sql
   SELECT * FROM tbldietic_otp_codes
   WHERE code = '482759'
   AND used = 1;
   ```

---

### Problème: Interface OTP ne s'affiche pas

**Vérifications:**
1. Vérifier le chemin de la vue:
   ```
   /modules/dietetic/views/portal/verify_registration_otp.php
   ```

2. Vérifier les permissions du fichier:
   ```bash
   ls -l modules/dietetic/views/portal/verify_registration_otp.php
   ```

3. Consulter les logs d'erreur PHP:
   ```bash
   tail -f /var/log/apache2/error.log
   ```

## Personnalisation

### Modifier la durée de validité du code OTP

**Fichier:** `modules/dietetic/controllers/Portal.php`

**Ligne 797-798:**
```php
// 5 minutes par défaut
$expires_at = date('Y-m-d H:i:s', strtotime('+5 minutes'));
```

**Pour changer à 10 minutes:**
```php
$expires_at = date('Y-m-d H:i:s', strtotime('+10 minutes'));
```

**⚠️ Important:** Également modifier le timer JavaScript dans la vue:
```javascript
// Ligne 347
let timeLeft = 600; // 10 minutes en secondes
```

---

### Modifier le message SMS

**Fichier:** `modules/dietetic/controllers/Portal.php`

**Ligne 808:**
```php
$sms_message = "DietZone - Code de validation: {$otp_code}. Valide 5 min. Ne pas partager.";
```

**⚠️ Limite:** 160 caractères maximum pour un seul SMS

---

### Modifier le cooldown du bouton "Renvoyer"

**Fichier:** `modules/dietetic/views/portal/verify_registration_otp.php`

**Ligne 399:**
```javascript
resendCooldown = 30; // 30 secondes par défaut
```

---

### Activer la soumission automatique

**Fichier:** `modules/dietetic/views/portal/verify_registration_otp.php`

**Ligne 430-433:** Décommenter cette ligne:
```javascript
// Uncomment to enable auto-submit:
otpInputs[5].addEventListener('input', autoSubmit);
```

Le formulaire sera soumis automatiquement dès que les 6 chiffres sont entrés.

## Avantages de cette implémentation

✅ **Sécurité renforcée:**
- Vérification du numéro de téléphone
- Protection contre les comptes fictifs
- Codes expirables

✅ **UX optimisée:**
- Interface moderne et intuitive
- Feedback visuel (timer, couleurs)
- Support mobile optimisé

✅ **Fiabilité:**
- Envoi multi-canal (SMS + Email)
- Possibilité de renvoyer le code
- Logs détaillés pour debugging

✅ **Flexibilité:**
- Facile à personnaliser
- Compatible avec le système existant
- Aucune modification de la BDD requise (sauf nouveau champ optionnel)

---

**Dernière mise à jour:** 11 décembre 2024
**Version:** 1.0
**Auteur:** Claude (Anthropic)
