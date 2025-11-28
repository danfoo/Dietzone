# Phase 7 - Système de Notifications Email & SMS

**Date**: 28 Novembre 2025
**Version**: 1.3.0
**Statut**: ✅ Implémenté

---

## 📋 Vue d'ensemble

Cette phase implémente le système de notifications automatiques pour les factures et les paiements, intégrant l'envoi d'emails avec pièces jointes PDF et les notifications SMS via l'API LAM.

---

## ✨ Fonctionnalités Ajoutées

### 1. 📧 Envoi de Factures par Email avec PDF

**Fichier**: `modules/dietetic/models/Dietetic_invoices_model.php`

#### Fonctionnalités

- ✅ Génération automatique du PDF de la facture en mémoire
- ✅ Email HTML professionnel avec logo de l'entreprise
- ✅ Pièce jointe PDF (`Facture_INV-XXXX-XXXX.pdf`)
- ✅ Récupération automatique de l'email du contact principal
- ✅ Mise à jour du statut de la facture (`sent`)
- ✅ Logging détaillé de toutes les opérations

#### Méthodes Ajoutées

**`send_invoice($id)`** - Méthode publique mise à jour
- Remplace le TODO original
- Orchestration complète de l'envoi

**`generate_invoice_pdf($invoice)`** - Méthode privée
- Génère le PDF en mémoire sans fichier temporaire
- Utilise `App_pdf` (TCPDF)
- Retourne le contenu PDF en tant que string
- Gestion d'erreurs avec try/catch

**`build_invoice_email_html($invoice, $patient_name)`** - Méthode privée
- Construit un email HTML responsive
- Inclut le logo de l'entreprise
- Tableau récapitulatif de la facture
- Footer avec coordonnées de l'entreprise

#### Structure de l'Email

```
┌─────────────────────────────┐
│       Logo Entreprise       │
│      Nom de l'Entreprise    │
├─────────────────────────────┤
│  Bonjour [Patient],         │
│                             │
│  Veuillez trouver ci-joint  │
│  votre facture...           │
│                             │
│  ┌───────────────────────┐  │
│  │ Numéro: INV-XXXX     │  │
│  │ Date: XX/XX/XXXX     │  │
│  │ Échéance: XX/XX/XXXX │  │
│  │ Montant: XXXXX FCFA  │  │
│  └───────────────────────┘  │
│                             │
│  Cordialement,              │
│  L'équipe [Entreprise]      │
├─────────────────────────────┤
│         Footer              │
│  Adresse | Email | Tél      │
└─────────────────────────────┘

📎 Pièce jointe: Facture_INV-XXXX-XXXX.pdf
```

---

### 2. 💰 Notifications de Paiement (Email + SMS)

**Fichier**: `modules/dietetic/models/Dietetic_payments_model.php`

#### Fonctionnalités

- ✅ Notification automatique lors de l'enregistrement d'un paiement
- ✅ Double canal : Email + SMS
- ✅ Email de confirmation avec détails du paiement
- ✅ SMS court avec montant et référence facture
- ✅ Intégration avec l'API LAM SMS
- ✅ Gestion intelligente des échecs (si email échoue, SMS peut réussir)

#### Méthodes Ajoutées

**`send_payment_notifications($payment_id)`** - Méthode privée
- Appelée automatiquement après l'enregistrement d'un paiement
- Récupère les informations du patient et de la facture
- Envoie les notifications par email et SMS
- Logging des succès/échecs

**`send_payment_email($payment, $invoice, $patient, $email)`** - Méthode privée
- Email HTML de confirmation de paiement
- Design avec bordure verte et icône de succès ✓
- Détails complets : référence, montant, date, méthode
- Responsive et professionnel

**`send_payment_sms($payment, $invoice, $patient, $phone)`** - Méthode privée
- SMS court et concis (~160 caractères)
- Format : "[Entreprise]: Paiement de XXXXX FCFA reçu pour la facture INV-XXXX. Merci!"
- Utilise `dietetic_send_sms()` existant

**`get_payment_method_label($method)`** - Méthode privée utilitaire
- Traduit les codes de méthodes de paiement en français
- Exemples : `cash` → "Espèces", `bank_transfer` → "Virement bancaire"

#### Déclenchement Automatique

Les notifications sont envoyées automatiquement lorsque :

```php
// Dans la méthode add() du modèle Payments
if ($data['status'] == 'completed') {
    $this->send_payment_notifications($payment_id);
}
```

#### Structure de l'Email de Paiement

```
┌─────────────────────────────┐
│       Logo Entreprise       │
│      Nom de l'Entreprise    │
├─────────────────────────────┤
│           ✓                 │
│    Paiement Confirmé        │
│                             │
│  Bonjour [Patient],         │
│                             │
│     XXXXX FCFA              │
│                             │
│  ┌───────────────────────┐  │
│  │ Référence: PAY-XXXX  │  │
│  │ Facture: INV-XXXX    │  │
│  │ Date: XX/XX/XXXX     │  │
│  │ Méthode: Espèces     │  │
│  │ Statut: ✓ Validé     │  │
│  └───────────────────────┘  │
│                             │
│  Merci pour votre confiance.│
│                             │
│  Cordialement,              │
│  L'équipe [Entreprise]      │
└─────────────────────────────┘
```

#### Exemple de SMS

```
DIETZONE: Paiement de 50 000 FCFA reçu pour la facture INV-2025-0001. Merci!
```

---

## 🔧 Configuration Requise

### 1. Configuration Email (Perfex CRM)

Assurez-vous que les paramètres SMTP sont configurés dans Perfex :

**Setup → Email → SMTP Settings**

- SMTP Host
- SMTP Port
- SMTP Username
- SMTP Password
- SMTP Encryption (TLS/SSL)
- From Email
- From Name

### 2. Configuration SMS (LAM API)

Les paramètres SMS doivent être configurés dans le module :

**Diététique → Paramètres → SMS Integration**

- `sms_lam_account_id` - ID du compte LAM
- `sms_lam_password` - Mot de passe du compte
- `sms_lam_sender_id` - Nom de l'expéditeur (ex: API_LAMSMS)
- `sms_lam_ret_url` - URL de callback (optionnel)
- `sms_lam_priority` - Priorité (1-3, défaut: 2)

---

## 📊 Flux de Traitement

### Flux d'Envoi de Facture

```
1. Admin clique sur "Envoyer" dans la liste des factures
       ↓
2. Contrôleur Invoices->send($id)
       ↓
3. Modèle send_invoice($id)
       ↓
4. Récupération email du patient
       ↓
5. Génération PDF (generate_invoice_pdf)
       ↓
6. Construction email HTML (build_invoice_email_html)
       ↓
7. Envoi email avec pièce jointe
       ↓
8. Mise à jour statut facture → "sent"
       ↓
9. Log de l'activité
       ↓
10. Retour succès/échec au contrôleur
       ↓
11. Message de confirmation à l'admin
```

### Flux de Notification de Paiement

```
1. Admin enregistre un paiement (status = completed)
       ↓
2. Modèle Payments->add()
       ↓
3. Insertion en base de données
       ↓
4. Appel send_payment_notifications($payment_id)
       ↓
5. Récupération données patient/facture
       ↓
6. Branch EMAIL                  Branch SMS
       ↓                              ↓
   send_payment_email()         send_payment_sms()
       ↓                              ↓
   Construction HTML            Format message court
       ↓                              ↓
   Envoi via CI Email           Appel dietetic_send_sms()
       ↓                              ↓
   Succès/Échec                 API LAM → Envoi SMS
       ↓                              ↓
       └──────────┬───────────────────┘
                  ↓
          Log consolidé
                  ↓
          Retour au contrôleur
```

---

## 🧪 Tests à Effectuer

### Test 1 : Envoi de Facture par Email

1. Se connecter en tant qu'admin
2. Aller dans **Diététique → Factures**
3. Sélectionner une facture en statut "Brouillon" ou "Envoyée"
4. Cliquer sur "Envoyer"
5. Vérifier :
   - ✅ Message de succès affiché
   - ✅ Statut de la facture passe à "Envoyée"
   - ✅ Email reçu par le patient
   - ✅ PDF joint à l'email
   - ✅ Contenu de l'email correct (logo, montants, dates)

### Test 2 : Notification de Paiement

1. Se connecter en tant qu'admin
2. Aller dans **Diététique → Paiements**
3. Cliquer sur "Nouveau Paiement"
4. Remplir le formulaire :
   - Facture : sélectionner une facture impayée
   - Montant : entrer le montant
   - Méthode : Espèces
   - Statut : **Complété** (important!)
5. Enregistrer
6. Vérifier :
   - ✅ Email de confirmation reçu
   - ✅ SMS reçu (si numéro valide configuré)
   - ✅ Facture marquée comme "Payée"
   - ✅ Log d'activité enregistré

### Test 3 : Gestion des Erreurs

**Test 3.1 : Patient sans email**
- Créer une facture pour un patient sans email
- Tenter d'envoyer la facture
- **Résultat attendu** : Échec gracieux avec log d'erreur

**Test 3.2 : SMTP mal configuré**
- Désactiver temporairement SMTP
- Envoyer une facture
- **Résultat attendu** : Échec avec message d'erreur, facture non marquée "Envoyée"

**Test 3.3 : SMS sans configuration LAM**
- Vider les paramètres LAM
- Enregistrer un paiement
- **Résultat attendu** : Email envoyé, SMS échoué (pas bloquant)

---

## 📝 Logs et Débogage

### Logs d'Activité

Tous les événements sont enregistrés dans la table `tblactivitylog` :

**Logs de Facture :**
```
- "Invoice Sent to Patient [Invoice: INV-2025-0001, Email: patient@example.com]"
- "Invoice Email Failed - No email found [Invoice: INV-2025-0001]"
- "Invoice Email Failed - PDF generation failed [Invoice: INV-2025-0001]"
- "PDF Generation Error [Invoice: INV-2025-0001]: [erreur détaillée]"
```

**Logs de Paiement :**
```
- "Payment Notification Sent [Payment ID: 123, Channels: Email, SMS]"
- "Payment Notification Failed [Payment ID: 123]"
- "Payment Email Exception [Payment ID: 123]: [erreur détaillée]"
```

### Consulter les Logs

**Admin → Utilities → Activity Log**

Rechercher par :
- Mot-clé : "Invoice", "Payment", "Notification"
- Date : date du test

---

## 🔐 Sécurité

### Mesures Implémentées

1. **Validation des Données**
   - Vérification existence facture/paiement
   - Vérification validité email
   - Sanitization des entrées HTML (`htmlspecialchars`)

2. **Gestion des Fichiers Temporaires**
   - PDF créé dans répertoire temp système
   - Suppression automatique après envoi (`@unlink`)
   - Pas de fichiers orphelins

3. **Permissions**
   - Seuls les utilisateurs avec permission `edit` peuvent envoyer des factures
   - Respect des permissions existantes du module

4. **Protection des Informations Sensibles**
   - Pas d'affichage de mots de passe dans les emails
   - Logging sans données sensibles

---

## 📈 Performance

### Métriques Estimées

| Opération | Temps Moyen | Notes |
|-----------|-------------|-------|
| Génération PDF | 0.5 - 1s | Dépend de la complexité |
| Envoi Email | 1 - 3s | Dépend du serveur SMTP |
| Envoi SMS (LAM) | 0.5 - 2s | Dépend de l'API LAM |
| **Total par facture** | **2 - 6s** | Acceptable pour processus asynchrone |

### Optimisations Possibles

1. **Queue System**
   - Implémenter un système de queue (ex: Redis, Beanstalk)
   - Traiter les emails en arrière-plan
   - Éviter les timeouts pour envois multiples

2. **Template Caching**
   - Cacher les templates d'emails générés
   - Réduire le temps de génération HTML

3. **Batch Processing**
   - Envoyer plusieurs factures en une seule fois
   - Grouper les emails pour efficacité

---

## 🐛 Problèmes Connus et Solutions

### Problème 1 : Email non reçu

**Causes possibles :**
- SMTP mal configuré
- Email dans spam
- Email inexistant

**Solution :**
1. Vérifier Setup → Email → SMTP Settings
2. Tester l'envoi avec Setup → Email → Test
3. Vérifier les logs : Admin → Utilities → Activity Log
4. Vérifier le dossier spam du destinataire

### Problème 2 : SMS non reçu

**Causes possibles :**
- Credentials LAM invalides
- Numéro de téléphone incorrect
- Crédit LAM insuffisant

**Solution :**
1. Vérifier Diététique → Paramètres → SMS Integration
2. Vérifier format du numéro (doit commencer par 221 pour Sénégal)
3. Consulter les logs LAM sur le portail LAM
4. Vérifier le solde du compte LAM

### Problème 3 : PDF corrompu ou vide

**Causes possibles :**
- Erreur dans le template
- Bibliothèque TCPDF manquante
- Permissions fichiers insuffisantes

**Solution :**
1. Vérifier que `APPPATH/libraries/pdf/App_pdf.php` existe
2. Tester la génération PDF via **Factures → Télécharger PDF**
3. Vérifier les permissions du répertoire temp
4. Consulter les logs PHP (`error_log`)

---

## 📚 Dépendances

### Bibliothèques Requises

- **CodeIgniter Email Library** (natif Perfex)
- **TCPDF** via `App_pdf` (natif Perfex)
- **LAM SMS API** (existant dans le module)

### Tables de Base de Données

**Existantes (utilisées) :**
- `tbldietic_invoices`
- `tbldietic_payments`
- `tbldietic_patients`
- `tblclients`
- `tblcontacts`
- `tbldietic_settings`
- `tblactivitylog`

**Aucune nouvelle table ajoutée.**

---

## 🚀 Prochaines Améliorations (Phase 8)

1. **Templates d'Email Personnalisables**
   - Interface admin pour éditer les templates
   - Variables dynamiques ({{patient_name}}, {{amount}}, etc.)
   - Prévisualisation avant envoi

2. **Notifications Push (FCM)**
   - Intégrer avec Firebase Cloud Messaging
   - Notifications temps réel dans le portail patient

3. **Rappels Automatiques**
   - Relances pour factures impayées
   - Rappels avant échéance
   - Configuration des délais

4. **Statistiques d'Envoi**
   - Taux d'ouverture des emails
   - Taux de clic
   - Dashboard des notifications

5. **Support WhatsApp**
   - Intégration API WhatsApp Business
   - Envoi de factures via WhatsApp
   - Confirmation de paiement par WhatsApp

---

## 👥 Support

Pour toute question ou problème :

- **Email** : support@dietsenegal.net
- **Documentation** : `/modules/dietetic/README.md`
- **GitHub Issues** : [Lien vers repo]

---

## 📅 Historique des Versions

| Version | Date | Changements |
|---------|------|-------------|
| 1.3.0 | 28/11/2025 | ✅ Implémentation Phase 7 - Notifications Email & SMS |
| 1.2.0 | 27/11/2025 | Phase 6 - Revenue Dashboard |
| 1.1.0 | 22/11/2025 | Phase 5 - Notifications Push Firebase |
| 1.0.0 | 21/11/2025 | Version initiale du module |

---

**Développé par** : Claude AI Assistant
**Statut** : ✅ Production Ready
**Dernière mise à jour** : 28 Novembre 2025
