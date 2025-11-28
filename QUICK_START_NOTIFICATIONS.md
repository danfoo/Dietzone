# 🚀 Guide de Démarrage Rapide - Notifications

**Phase 7 implémentée avec succès !** ✅

---

## ⚡ Résumé des Fonctionnalités

### 📧 Envoi de Factures par Email
- ✅ Email professionnel avec logo
- ✅ PDF de la facture en pièce jointe
- ✅ Récapitulatif détaillé dans l'email
- ✅ Mise à jour automatique du statut

### 💰 Notifications de Paiement
- ✅ Email de confirmation de paiement
- ✅ SMS via LAM API
- ✅ Détails complets de la transaction
- ✅ Double canal (Email + SMS)

---

## 🎯 Configuration Requise (Avant de Tester)

### 1. Configuration Email SMTP

**Menu** : Setup → Email → SMTP Settings

Assurez-vous que ces champs sont remplis :
- **SMTP Host** : smtp.gmail.com (exemple)
- **SMTP Port** : 587
- **SMTP Username** : votre-email@gmail.com
- **SMTP Password** : votre-mot-de-passe-app
- **From Email** : votre-email@gmail.com
- **From Name** : Nom de votre entreprise

💡 **Testez** : Setup → Email → Test Email

### 2. Configuration SMS LAM (Optionnel)

**Menu** : Diététique → Paramètres

Sections SMS :
- **sms_lam_account_id** : Votre ID de compte LAM
- **sms_lam_password** : Votre mot de passe LAM
- **sms_lam_sender_id** : API_LAMSMS (ou votre nom d'expéditeur)

---

## ✅ Tests Rapides

### Test 1 : Envoyer une Facture (2 minutes)

1. **Aller dans** : Diététique → Factures
2. **Cliquer** sur une facture (ou en créer une nouvelle)
3. **Cliquer** sur le bouton **"Envoyer"**
4. **Vérifier** :
   - ✅ Message de succès : "Facture envoyée avec succès"
   - ✅ Statut de la facture → "Envoyée"
   - ✅ Email reçu par le patient (vérifiez la boîte de réception)
   - ✅ PDF joint à l'email

### Test 2 : Notification de Paiement (3 minutes)

1. **Aller dans** : Diététique → Paiements
2. **Cliquer** sur **"Nouveau Paiement"**
3. **Remplir** :
   - Facture : Sélectionner une facture
   - Montant : Entrer le montant
   - Méthode : Espèces (ou autre)
   - **Statut : COMPLÉTÉ** ⚠️ (Important pour déclencher la notification)
4. **Enregistrer**
5. **Vérifier** :
   - ✅ Email de confirmation reçu
   - ✅ SMS reçu (si numéro et LAM configurés)
   - ✅ Facture marquée "Payée"

---

## 🔍 Où Vérifier les Résultats ?

### Logs d'Activité

**Menu** : Admin → Utilities → Activity Log

**Rechercher** :
- "Invoice Sent" → pour voir les factures envoyées
- "Payment Notification" → pour voir les paiements notifiés

### Emails Envoyés

Vérifiez la boîte email du patient (contact principal du client)

### SMS Envoyés

Si LAM configuré, vérifiez le téléphone du patient

---

## ⚠️ Dépannage Rapide

### ❌ Email non reçu ?

**Solutions** :
1. Vérifier Setup → Email → SMTP Settings
2. Tester l'envoi : Setup → Email → Test
3. Vérifier le dossier spam
4. Consulter les logs : Admin → Utilities → Activity Log

### ❌ SMS non reçu ?

**Solutions** :
1. Vérifier Diététique → Paramètres → SMS
2. Vérifier que le patient a un numéro de téléphone
3. Vérifier le format du numéro (doit commencer par 221 pour Sénégal)
4. Vérifier le crédit du compte LAM

### ❌ PDF corrompu ?

**Solutions** :
1. Tester le téléchargement PDF depuis la liste des factures
2. Vérifier les permissions du répertoire temp
3. Consulter les logs PHP

---

## 📊 Exemple d'Email de Facture

```
De: Votre Entreprise <votre-email@gmail.com>
À: patient@example.com
Sujet: Facture INV-2025-0001 - Votre Entreprise

┌─────────────────────────────────────┐
│        [Logo de l'entreprise]       │
│                                     │
│  Bonjour [Nom du Patient],          │
│                                     │
│  Veuillez trouver ci-joint votre    │
│  facture pour les services          │
│  diététiques.                       │
│                                     │
│  Numéro de facture : INV-2025-0001  │
│  Date d'émission   : 28/11/2025     │
│  Date d'échéance   : 28/12/2025     │
│  Montant total     : 50 000 FCFA    │
│                                     │
│  📎 Pièce jointe :                  │
│     Facture_INV-2025-0001.pdf       │
│                                     │
│  Cordialement,                      │
│  L'équipe Votre Entreprise          │
└─────────────────────────────────────┘
```

## 📊 Exemple d'Email de Paiement

```
De: Votre Entreprise <votre-email@gmail.com>
À: patient@example.com
Sujet: Confirmation de paiement - Facture INV-2025-0001

┌─────────────────────────────────────┐
│        [Logo de l'entreprise]       │
│                                     │
│              ✓                      │
│       Paiement Confirmé             │
│                                     │
│  Bonjour [Nom du Patient],          │
│                                     │
│  Nous confirmons la réception de    │
│  votre paiement :                   │
│                                     │
│         50 000 FCFA                 │
│                                     │
│  Référence : PAY-123                │
│  Facture   : INV-2025-0001          │
│  Date      : 28/11/2025 à 14:30     │
│  Méthode   : Espèces                │
│  Statut    : ✓ Paiement validé      │
│                                     │
│  Merci pour votre confiance.        │
│                                     │
│  Cordialement,                      │
│  L'équipe Votre Entreprise          │
└─────────────────────────────────────┘
```

## 📊 Exemple de SMS

```
Votre Entreprise: Paiement de 50 000 FCFA
reçu pour la facture INV-2025-0001. Merci!
```

---

## 📖 Documentation Complète

Pour plus de détails, consultez :

- **PHASE_7_NOTIFICATIONS.md** - Documentation technique complète
- **modules/dietetic/README.md** - Documentation du module

---

## 🎉 Bravo !

Vous avez maintenant un système complet de notifications automatiques pour votre application de diététique !

**Prochaines étapes suggérées** :
- Configurer les rappels automatiques pour les factures impayées
- Personnaliser les templates d'emails
- Intégrer PayPal, Wave ou Orange Money

---

**Besoin d'aide ?**
- Consultez PHASE_7_NOTIFICATIONS.md section "Troubleshooting"
- Vérifiez les logs dans Admin → Utilities → Activity Log
- Contactez le support technique
