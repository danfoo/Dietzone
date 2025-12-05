# 🚀 GUIDE COMPLET DE SOUMISSION GOOGLE PLAY STORE

## 📋 TABLE DES MATIÈRES

1. [Préparation Avant Soumission](#1-préparation-avant-soumission)
2. [Création du Compte Développeur](#2-création-du-compte-développeur)
3. [Préparation de l'APK](#3-préparation-de-lapk)
4. [Création de l'Application](#4-création-de-lapplication)
5. [Configuration de la Fiche](#5-configuration-de-la-fiche)
6. [Soumission et Révision](#6-soumission-et-révision)
7. [Après Publication](#7-après-publication)
8. [Résolution des Problèmes](#8-résolution-des-problèmes)

---

## 1. PRÉPARATION AVANT SOUMISSION

### ✅ Checklist des éléments requis

#### Assets Visuels
- [ ] **Icône de l'application** (512x512px, PNG, pas de transparence)
- [ ] **Feature Graphic** (1024x500px, PNG ou JPG)
- [ ] **Screenshots** (minimum 2, recommandé 8)
  - Taille : 1080x1920px (portrait) ou 1920x1080px (paysage)
  - Format : PNG ou JPG
  - Pas de contenu sensible ou trompeur

#### Textes Marketing
- [ ] **Titre de l'app** (50 caractères max)
- [ ] **Description courte** (80 caractères max)
- [ ] **Description longue** (4000 caractères max)
- [ ] **Catégorie** (Santé & Fitness)
- [ ] **Mots-clés** (optionnel mais recommandé)

#### Informations Légales
- [ ] **Politique de confidentialité** (URL publique accessible)
- [ ] **Email de contact** valide
- [ ] **Adresse physique** complète au Sénégal
- [ ] **Numéro de téléphone**

#### Fichier APK/AAB
- [ ] **APK signé** ou **Android App Bundle (AAB)** de Median
- [ ] **Testé** sur plusieurs appareils
- [ ] **Fonctionnel** sans bugs majeurs
- [ ] **Taille** optimisée (< 100 MB recommandé)

#### Documents Additionnels
- [ ] **Certificat de signature** (keystore) - conservez-le précieusement !
- [ ] **Vidéo promotionnelle** (optionnel)

---

## 2. CRÉATION DU COMPTE DÉVELOPPEUR

### Étape 2.1 : Inscription

1. **Accédez à Google Play Console**
   ```
   https://play.google.com/console/signup
   ```

2. **Connectez-vous avec un compte Google**
   - Utilisez un compte professionnel (maestrodan@...)
   - **NE PAS UTILISER** de compte personnel
   - Ce compte sera le propriétaire permanent

3. **Acceptez les Accords**
   - Lisez et acceptez l'Accord de Distribution pour les Développeurs
   - Acceptez les Conditions d'Utilisation

### Étape 2.2 : Paiement des Frais

1. **Frais uniques : 25 USD**
   - Paiement unique (jamais à renouveler)
   - Carte bancaire internationale requise
   - Délai de traitement : instantané à 48h

2. **Méthodes de paiement acceptées**
   - Visa
   - Mastercard
   - American Express

### Étape 2.3 : Vérification d'Identité

Google demande désormais une vérification d'identité :

1. **Informations personnelles**
   - Nom complet : Eric Gilles SAGNA
   - Date de naissance
   - Adresse complète au Sénégal

2. **Vérification d'identité** (peut inclure)
   - Pièce d'identité officielle (Carte d'identité, Passeport)
   - Selfie avec pièce d'identité
   - Document prouvant l'adresse

3. **Vérification d'entreprise** (pour Maestrodan)
   - Numéro d'enregistrement d'entreprise
   - Documents officiels de l'entreprise
   - Preuve d'adresse de l'entreprise

**⏱️ Délai de vérification : 3-10 jours ouvrables**

### Étape 2.4 : Configuration du Compte

1. **Nom du développeur public**
   ```
   Maestrodan
   ```

2. **Email de contact public**
   ```
   contact@dietsenegal.net
   ```

3. **Site web**
   ```
   https://app.dietsenegal.net
   ```

4. **Pays du développeur**
   ```
   Sénégal
   ```

---

## 3. PRÉPARATION DE L'APK

### Étape 3.1 : Obtenir l'APK depuis Median

1. **Connectez-vous à votre compte Median**
   ```
   https://median.co/
   ```

2. **Accédez à votre projet Dietzone**

3. **Configurez les paramètres Android**
   - **Package Name** (ex: `com.maestrodan.dietzone`)
   - **Version Code** (commencez à 1)
   - **Version Name** (ex: `1.0.0`)

4. **Configuration OneSignal**
   - Ajoutez votre OneSignal App ID
   - Vérifiez que les notifications sont activées

5. **Build l'APK ou AAB**
   - Choisissez **Android App Bundle (AAB)** (recommandé par Google)
   - ou **APK** si AAB n'est pas disponible
   - Téléchargez le fichier

### Étape 3.2 : Signature de l'APK

**Si Median signe automatiquement :**
- ✅ Pas de manipulation nécessaire
- Conservez les informations de signature

**Si signature manuelle requise :**

1. **Générer un Keystore** (première fois seulement)
   ```bash
   keytool -genkey -v -keystore dietzone-release.keystore \
   -alias dietzone -keyalg RSA -keysize 2048 -validity 10000
   ```

2. **Signer l'APK**
   ```bash
   jarsigner -verbose -sigalg SHA256withRSA -digestalg SHA-256 \
   -keystore dietzone-release.keystore app-release-unsigned.apk dietzone
   ```

3. **⚠️ CRITIQUE : Sauvegardez le Keystore**
   - Conservez `dietzone-release.keystore`
   - Notez le mot de passe
   - **SANS CE FICHIER, VOUS NE POURREZ JAMAIS METTRE À JOUR L'APP**

### Étape 3.3 : Test de l'APK

1. **Installation sur appareil réel**
   ```bash
   adb install app-release.apk
   ```

2. **Tests obligatoires**
   - [ ] L'app s'installe sans erreur
   - [ ] L'app se lance correctement
   - [ ] Login fonctionne
   - [ ] Notifications push fonctionnent
   - [ ] Navigation fluide sans crash
   - [ ] Tous les écrans principaux s'affichent

3. **Test sur plusieurs appareils Android**
   - Android 5.0 minimum
   - Différentes tailles d'écran
   - Différents fabricants (Samsung, Xiaomi, etc.)

---

## 4. CRÉATION DE L'APPLICATION

### Étape 4.1 : Créer une Nouvelle Application

1. **Dans Google Play Console, cliquez sur "Créer une application"**

2. **Remplissez les informations de base**

   **Nom de l'application**
   ```
   Dietzone - Coaching Nutritionnel
   ```

   **Langue par défaut**
   ```
   Français (France)
   ```

   **Type d'application**
   ```
   ☑ Application
   ☐ Jeu
   ```

   **Gratuite ou payante**
   ```
   ☑ Gratuite
   ☐ Payante
   ```

3. **Déclarations**
   - [ ] ☑ Je confirme que cette application est conforme aux Règles relatives aux Programmes pour les Développeurs
   - [ ] ☑ Je confirme que cette application respecte les lois américaines sur le contrôle des exportations

4. **Cliquez sur "Créer l'application"**

### Étape 4.2 : Configuration Initiale

Google va vous demander de compléter plusieurs sections. Suivez l'ordre suggéré :

---

## 5. CONFIGURATION DE LA FICHE

### Section 5.1 : Informations sur l'Application

#### **Fiche de Présentation**

1. **Description de l'app**
   - **Titre** : `Dietzone - Coaching Nutritionnel`
   - **Description courte** : Copiez depuis `PLAY_STORE_MARKETING_TEXTS.md`
   - **Description complète** : Copiez la description longue

2. **Assets graphiques**

   **Icône de l'application**
   - Téléchargez votre icône 512x512px
   - Aperçu instantané

   **Feature Graphic**
   - Téléchargez votre bannière 1024x500px
   - S'affichera en haut de la fiche Play Store

   **Screenshots téléphone**
   - Téléchargez minimum 2, maximum 8 screenshots
   - Format : 1080x1920px (portrait)
   - Ordre : Dashboard → Plans → Recettes → Graphiques

   **Screenshots tablette 7"** (optionnel)
   - Si vous avez une version tablette optimisée

   **Screenshots tablette 10"** (optionnel)

3. **Vidéo YouTube** (optionnel)
   - URL complète de votre vidéo promotionnelle

4. **Catégorie**
   - **Catégorie** : `Santé et remise en forme`
   - **Tags** (si disponible) : Nutrition, Diététique, Santé

5. **Coordonnées**
   - **Site web** : `https://app.dietsenegal.net`
   - **Email** : `contact@dietsenegal.net`
   - **Numéro de téléphone** : `+221 XX XXX XX XX`

6. **Politique de confidentialité**
   ```
   https://app.dietsenegal.net/admin/dietetic/legal_pages/privacy
   ```

### Section 5.2 : Classification du Contenu

1. **Questionnaire de contenu**

   **Catégorie d'âge et contenu**
   - ☑ Tous publics (Everyone)
   - ou ☑ Adolescents (Teen) si données sensibles

   **Contenu interactif**
   - ☑ Les utilisateurs peuvent interagir
   - ☑ Partage d'informations utilisateur
   - ☐ Achats numériques (si applicable)

2. **Répondez au questionnaire**
   - Questions sur violence, contenu explicite, etc.
   - Pour Dietzone : Répondez "Non" à tout contenu sensible

3. **Déclaration des annonces**
   - ☐ Non, cette app ne contient pas de publicités

### Section 5.3 : Prix et Distribution

1. **Prix**
   - ☑ Gratuit

2. **Pays de distribution**
   - ☑ Tous les pays
   - ou sélectionnez : Sénégal, Mali, Côte d'Ivoire, etc.

3. **Programmes et fonctionnalités**
   - ☐ Google Play for Education (non applicable)
   - ☐ Conçu pour les familles (non applicable)

4. **Consentement marketing**
   - ☑ Autoriser Google à partager des informations marketing (optionnel)

### Section 5.4 : Évaluation du Contenu

1. **Remplissez le questionnaire IARC**
   - Automatique via Play Console
   - Questions sur violence, sexe, langage grossier, etc.
   - Pour Dietzone : Sélectionnez le niveau le plus bas

2. **Classification obtenue**
   - Vous obtiendrez une classification par région
   - Ex : PEGI 3 (Europe), ESRB Everyone (USA)

### Section 5.5 : Données de Sécurité

**⚠️ SECTION CRITIQUE DEPUIS 2022**

1. **Collecte et partage de données**

   **Données collectées :**
   - ☑ Oui, cette app collecte des données

   **Types de données collectées :**
   - ☑ Informations personnelles (nom, email, téléphone)
   - ☑ Informations de santé (poids, mesures, objectifs)
   - ☑ Informations financières (historique paiements)
   - ☑ Données d'utilisation de l'app

2. **But de la collecte**
   - ☑ Fonctionnalités de l'app
   - ☑ Analyses
   - ☐ Publicité ou marketing

3. **Partage de données**
   - ☑ Oui, partagées avec des prestataires de services
   - Listez : OneSignal (notifications), LAM SMS

4. **Sécurité des données**
   - ☑ Données chiffrées en transit (HTTPS)
   - ☑ Vous permettez aux utilisateurs de demander la suppression
   - ☑ Vous suivez les bonnes pratiques de sécurité

5. **Lien vers la politique de confidentialité**
   ```
   https://app.dietsenegal.net/admin/dietetic/legal_pages/privacy
   ```

---

## 6. SOUMISSION ET RÉVISION

### Étape 6.1 : Production

1. **Accédez à "Production" dans le menu gauche**

2. **Créez une nouvelle version**
   - Cliquez sur "Créer une nouvelle version"

3. **Téléchargez l'APK/AAB**
   - Glissez-déposez votre fichier
   - ou utilisez le bouton "Parcourir les fichiers"
   - Attendez la fin du téléchargement et de l'analyse

4. **Nom de la version** (Version Name)
   ```
   1.0.0
   ```

5. **Notes de version** (Release notes)

   **En français :**
   ```
   🎉 Première version de Dietzone !

   ✨ Fonctionnalités :
   • Suivi nutritionnel personnalisé
   • Plans alimentaires sur mesure
   • Rappels automatiques (repas, hydratation, pesée)
   • Historique des consultations
   • Graphiques de progression
   • Bibliothèque de recettes

   📱 Profitez d'un accompagnement diététique professionnel directement depuis votre smartphone !
   ```

   **En anglais (si vous distribuez internationalement) :**
   ```
   🎉 First release of Dietzone!

   ✨ Features:
   • Personalized nutritional monitoring
   • Custom meal plans
   • Automatic reminders (meals, hydration, weigh-in)
   • Consultation history
   • Progress charts
   • Recipe library

   📱 Enjoy professional dietary support directly from your smartphone!
   ```

6. **Code de version** (Version Code)
   ```
   1
   ```
   *(Incrémentez à chaque mise à jour : 2, 3, 4...)*

### Étape 6.2 : Révision Finale

1. **Vérifiez la checklist Play Console**
   - Google affiche une liste de vérification
   - Complétez toutes les sections manquantes

2. **Aperçu de la fiche**
   - Cliquez sur "Aperçu" pour voir comment votre fiche apparaîtra
   - Vérifiez tous les textes, images, screenshots

3. **Résolvez les erreurs et avertissements**
   - ❌ Erreurs (rouge) : DOIVENT être résolues
   - ⚠️ Avertissements (jaune) : Recommandé de résoudre

### Étape 6.3 : Soumission

1. **Cliquez sur "Envoyer pour examen"**

2. **Confirmation**
   - Google affiche un récapitulatif
   - Vérifiez une dernière fois
   - Confirmez la soumission

3. **⏱️ Délai de révision**
   - **Première soumission** : 7 à 14 jours
   - **Mises à jour** : 1 à 3 jours
   - **Urgences** : Demandez une révision accélérée (rare)

### Étape 6.4 : Statuts Possibles

Pendant la révision, vous verrez différents statuts :

- **⏳ En cours de révision** : Google analyse votre app
- **✅ Approuvée** : Félicitations ! Votre app est publiée
- **❌ Rejetée** : Voir section Résolution des Problèmes ci-dessous

---

## 7. APRÈS PUBLICATION

### Étape 7.1 : Vérification

1. **Recherchez votre app sur Play Store**
   ```
   Recherchez : "Dietzone" ou "Dietzone Coaching"
   ```

2. **Vérifiez la fiche**
   - Tous les textes sont corrects
   - Screenshots s'affichent bien
   - Bouton "Installer" fonctionne

3. **Installez sur un appareil test**
   - Téléchargez depuis Play Store
   - Testez toutes les fonctionnalités

### Étape 7.2 : Communication

1. **Annoncez le lancement**
   - Email à vos patients existants
   - Post sur réseaux sociaux
   - Communiqué de presse (optionnel)

2. **Mettez à jour votre site web**
   - Ajoutez un badge "Disponible sur Google Play"
   - Lien direct vers la fiche Play Store

3. **Badge Play Store**
   ```
   https://play.google.com/store/apps/details?id=com.maestrodan.dietzone
   ```
   *(Remplacez par votre vrai package name)*

### Étape 7.3 : Monitoring

1. **Consultez les statistiques**
   - Nombre d'installations
   - Nombre de désinstallations
   - Notes et avis
   - Rapports de crash

2. **Répondez aux avis**
   - Remerciez les avis positifs
   - Résolvez les problèmes des avis négatifs
   - Montrez que vous êtes actif

3. **Mettez à jour régulièrement**
   - Corrections de bugs
   - Nouvelles fonctionnalités
   - Améliorations de performance

---

## 8. RÉSOLUTION DES PROBLÈMES

### Problème 8.1 : App Rejetée

**Raisons courantes de rejet :**

#### ❌ Politique de confidentialité non conforme
**Solution :**
- Vérifiez que l'URL fonctionne
- Assurez-vous qu'elle mentionne toutes les données collectées
- Mettez à jour si nécessaire

#### ❌ Permissions excessives
**Solution :**
- Retirez les permissions non utilisées dans Median
- Justifiez chaque permission dans la description

#### ❌ Contenu trompeur ou offensant
**Solution :**
- Modifiez les screenshots problématiques
- Reformulez les textes marketing

#### ❌ Fonctionnalité cassée détectée par Google
**Solution :**
- Testez l'app sur plusieurs appareils
- Corrigez les bugs
- Resoumettez une nouvelle version

#### ❌ Données de sécurité incorrectes
**Solution :**
- Revérifiez la section "Données de sécurité"
- Soyez précis sur les données collectées
- Mettez à jour la politique de confidentialité

### Problème 8.2 : Compte Suspendu

**Si votre compte est suspendu :**

1. **Lisez attentivement l'email de Google**
   - Identifiez la raison exacte

2. **Appelez à révision**
   - Expliquez la situation
   - Montrez votre bonne foi
   - Promettez de corriger

3. **Prévenez en créant un compte propre dès le début**

### Problème 8.3 : Erreurs d'Upload

#### Erreur : "Version code doit être supérieur"
**Solution :**
- Incrémentez le version code (1 → 2 → 3...)

#### Erreur : "Signature incompatible"
**Solution :**
- Utilisez le même keystore que la version précédente
- **Si keystore perdu = impossible de mettre à jour**

#### Erreur : "Package name en conflit"
**Solution :**
- Changez le package name dans Median
- Ex : `com.maestrodan.dietzone.v2`

### Problème 8.4 : APK Trop Lourd

**Si APK > 100 MB :**

1. **Optimisez les assets**
   - Compressez les images
   - Retirez les ressources inutilisées

2. **Utilisez Android App Bundle (AAB)**
   - Google optimise automatiquement
   - Taille de téléchargement réduite

3. **Expansion files** (si vraiment nécessaire)
   - Pour apps > 100 MB

---

## 📱 LIENS UTILES

### Google Play Console
```
https://play.google.com/console
```

### Documentation Officielle
```
https://developer.android.com/distribute/google-play
```

### Règles du Programme
```
https://play.google.com/about/developer-content-policy/
```

### Support Google Play
```
https://support.google.com/googleplay/android-developer
```

### Median Documentation
```
https://median.co/docs
```

---

## ✅ CHECKLIST FINALE AVANT SOUMISSION

- [ ] Compte développeur créé et vérifié
- [ ] 25$ payés
- [ ] APK/AAB signé et testé
- [ ] Tous les textes marketing rédigés (français + anglais si international)
- [ ] Icône 512x512px uploadée
- [ ] Feature graphic 1024x500px uploadée
- [ ] Minimum 2 screenshots uploadés
- [ ] Politique de confidentialité accessible
- [ ] Section "Données de sécurité" complétée
- [ ] Classification du contenu terminée
- [ ] Notes de version rédigées
- [ ] Tous les avertissements/erreurs résolus
- [ ] Aperçu de la fiche vérifié
- [ ] Keystore sauvegardé en lieu sûr
- [ ] Email de contact valide

---

## 🎉 FÉLICITATIONS !

Si vous avez suivi ce guide, votre app Dietzone devrait être en cours de révision ou déjà publiée sur Google Play Store !

**Prochaines étapes :**
1. Partagez le lien Play Store
2. Encouragez vos utilisateurs à laisser des avis
3. Planifiez les mises à jour régulières
4. Analysez les statistiques pour améliorer

---

**Document créé pour Dietzone by Maestrodan**
**Date : Décembre 2024**
**Contact : contact@dietsenegal.net**

**Bonne chance avec votre publication ! 🚀**
