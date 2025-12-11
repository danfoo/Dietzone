# Mise à jour des templates de notification

Ce guide explique comment mettre à jour les templates de notification pour l'inscription patient et la réinitialisation de mot de passe.

## 🚀 Exécution du script

### Méthode 1: Via navigateur (recommandé)

1. Accédez à l'URL suivante dans votre navigateur:
   ```
   https://app.dietsenegal.net/modules/dietetic/update_notification_templates.php
   ```

2. Le script va automatiquement:
   - Créer ou mettre à jour les templates dans la base de données
   - Afficher un rapport détaillé des opérations effectuées
   - Vous rediriger vers la page des templates

3. Vous verrez un message de confirmation pour chaque template créé/mis à jour

### Méthode 2: Via ligne de commande

```bash
cd /home/user/Dietzone/modules/dietetic
php update_notification_templates.php
```

## 📝 Templates créés

Le script crée/met à jour 2 nouveaux types de templates:

### 1. `patient_registration`
Template pour l'inscription d'un nouveau patient

**Variables disponibles:**
- `{patient_name}` - Nom complet du patient
- `{email}` - Adresse email
- `{phone}` - Numéro de téléphone
- `{password}` - Mot de passe généré
- `{login_url}` - URL de connexion au portail

**Canaux:**
- ✉️ Email: Message HTML avec identifiants et lien de connexion
- 📱 SMS: Message court (<160 caractères) avec identifiants essentiels
- 💬 WhatsApp: Message formaté Markdown avec icônes

### 2. `password_reset`
Template pour la réinitialisation de mot de passe

**Variables disponibles:**
- `{patient_name}` - Nom complet du patient
- `{patient_firstname}` - Prénom du patient
- `{code}` - Code OTP à 6 chiffres

**Canaux:**
- ✉️ Email: Message HTML avec code en grand format et consignes de sécurité
- 📱 SMS: Message court (<160 caractères) avec code
- 💬 WhatsApp: Message formaté Markdown avec code et avertissement sécurité

## 🔍 Vérification

Après exécution du script:

1. Allez sur: https://app.dietsenegal.net/admin/dietetic/notifications/templates

2. Vérifiez que vous voyez les nouveaux templates:
   - **patient_registration** (en haut de la liste, section "Authentification & Sécurité")
   - **password_reset** (en haut de la liste, section "Authentification & Sécurité")

3. Vous pouvez modifier les templates via l'interface si nécessaire

## 📋 Longueur des messages SMS

Les messages SMS respectent la limite de 160 caractères:

**patient_registration:**
```
DietZone: Email: patient@exemple.com / Pass: mot123
app.dietsenegal.net/dietetic/portal
```
Longueur: ~80-120 caractères (selon email et mot de passe)

**password_reset:**
```
DietZone - Code: 123456. Valide 5 min. Ne pas partager.
```
Longueur: ~55 caractères

## ⚠️ Notes importantes

- Le script peut être exécuté plusieurs fois sans problème
- Si un template existe déjà, il sera **mis à jour** (pas de doublon)
- Les templates existants ne sont **pas modifiés**
- Les variables `{xxx}` doivent être remplacées dynamiquement dans le code Portal.php

## 🔧 Fichiers modifiés

1. **`update_notification_templates.php`** - Script de mise à jour (nouveau)
2. **`controllers/Notifications.php`** - Liste des templates mise à jour
3. **`controllers/Portal.php`** - Utilise déjà ces templates dans les méthodes:
   - `send_registration_notifications()` pour l'inscription
   - `forgot_password()` pour le reset password

## 📞 Support

En cas de problème:
1. Vérifiez les logs d'activité Perfex
2. Vérifiez que la table `tbldietic_settings` existe
3. Vérifiez les permissions de la base de données

## ✅ Checklist

- [ ] Script exécuté avec succès
- [ ] Templates visibles dans l'admin
- [ ] Test inscription patient → SMS/Email/WhatsApp reçus
- [ ] Test mot de passe oublié → SMS/Email/WhatsApp reçus avec code
- [ ] Messages SMS < 160 caractères
