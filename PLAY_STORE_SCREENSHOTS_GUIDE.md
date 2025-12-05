# 📸 GUIDE CRÉATION SCREENSHOTS PLAY STORE - DIETZONE

## 📋 TABLE DES MATIÈRES

1. [Exigences Techniques](#1-exigences-techniques)
2. [Méthodes de Capture](#2-méthodes-de-capture)
3. [Ordre et Contenu Recommandés](#3-ordre-et-contenu-recommandés)
4. [Amélioration des Screenshots](#4-amélioration-des-screenshots)
5. [Templates et Outils](#5-templates-et-outils)
6. [Checklist Finale](#6-checklist-finale)

---

## 1. EXIGENCES TECHNIQUES

### 📏 Dimensions

#### **Téléphones (OBLIGATOIRE)**
```
Format Portrait : 1080 x 1920 pixels (ratio 16:9)
ou
Format Paysage : 1920 x 1080 pixels (ratio 16:9)
```

#### **Tablettes 7" (Optionnel)**
```
Format Portrait : 1200 x 1920 pixels
ou
Format Paysage : 1920 x 1200 pixels
```

#### **Tablettes 10" (Optionnel)**
```
Format Portrait : 1600 x 2560 pixels
ou
Format Paysage : 2560 x 1600 pixels
```

### 📊 Spécifications

- **Format** : PNG ou JPG
- **Nombre minimum** : 2 screenshots
- **Nombre maximum** : 8 screenshots
- **Taille fichier** : < 8 MB par image
- **Espace colorimétrique** : sRGB

### ❌ À ÉVITER

- Captures floues ou pixelisées
- Contenu sensible ou offensant
- Informations confidentielles de vrais patients
- Captures d'écrans vides ou avec des erreurs
- Trop de texte qui cache l'interface

---

## 2. MÉTHODES DE CAPTURE

### Méthode 2.1 : Capture Directe depuis l'APK (RECOMMANDÉ)

#### **Sur Android Physique**

1. **Installez l'APK sur votre téléphone**
   ```bash
   adb install app-release.apk
   ```

2. **Naviguez vers les écrans à capturer**

3. **Prenez les screenshots**
   - **Samsung** : Bouton Power + Volume Bas
   - **Xiaomi/Redmi** : Bouton Power + Volume Bas
   - **Google Pixel** : Bouton Power + Volume Bas
   - Ou utilisez l'assistant : "Ok Google, prends un screenshot"

4. **Récupérez les screenshots**
   ```bash
   adb pull /sdcard/Pictures/Screenshots/ .
   ```

#### **Sur Émulateur Android Studio**

1. **Lancez Android Studio**

2. **Créez un appareil virtuel (AVD)**
   - Ouvrez AVD Manager
   - Créez un Pixel 5 ou Pixel 6
   - API Level 30+ (Android 11+)

3. **Installez l'APK**
   ```bash
   adb -e install app-release.apk
   ```

4. **Prenez les screenshots**
   - Bouton caméra dans les contrôles de l'émulateur
   - Ou : Menu AVD → Extended Controls → Screenshots

5. **Dimensions automatiquement ajustées**

### Méthode 2.2 : Screenshots depuis le Web (avec Device Toolbar)

Si votre app est essentiellement une webapp dans Median :

1. **Ouvrez Chrome DevTools**
   - `F12` ou Clic droit → Inspecter

2. **Activez Device Toolbar**
   - `Ctrl+Shift+M` (Windows/Linux)
   - `Cmd+Shift+M` (Mac)

3. **Sélectionnez un appareil**
   - iPhone X / Pixel 5 / Galaxy S20
   - Ou créez un appareil personnalisé

4. **Ajustez les dimensions**
   ```
   Width: 1080px
   Height: 1920px
   ```

5. **Naviguez et capturez**
   - Utilisez l'outil screenshot intégré
   - Ou utilisez une extension Chrome

### Méthode 2.3 : Utiliser des Données de Test

**IMPORTANT : N'utilisez JAMAIS de vraies données patients**

Créez des données de test réalistes mais fictives :

```
Patient Test :
- Nom : Marie Dupont
- Email : marie.test@example.com
- Téléphone : +221 77 XXX XX XX
- Poids initial : 85 kg
- Poids actuel : 78 kg
- Objectif : 70 kg
```

---

## 3. ORDRE ET CONTENU RECOMMANDÉS

### 📱 Screenshot 1 : ÉCRAN D'ACCUEIL / DASHBOARD

**Objectif** : Montrer la vue principale après connexion

**Éléments à afficher :**
- Nom du patient (test)
- Statistiques principales (poids, IMC)
- Prochaine consultation
- Notifications récentes
- Navigation claire

**Texte overlay suggéré :**
```
"Tableau de bord personnalisé"
ou
"Suivez vos progrès en temps réel"
```

---

### 📱 Screenshot 2 : PLAN ALIMENTAIRE PERSONNALISÉ

**Objectif** : Mettre en avant les plans de repas

**Éléments à afficher :**
- Plan de la journée (petit-déj, déjeuner, dîner)
- Détails nutritionnels (calories, protéines)
- Recettes ou aliments recommandés
- Interface claire et attractive

**Texte overlay suggéré :**
```
"Plans alimentaires sur mesure"
ou
"Repas équilibrés adaptés à vos objectifs"
```

---

### 📱 Screenshot 3 : BIBLIOTHÈQUE DE RECETTES

**Objectif** : Montrer la richesse du contenu

**Éléments à afficher :**
- Liste de recettes avec photos
- Filtres (catégories, calories)
- Icônes nutritionnelles
- Interface appétissante

**Texte overlay suggéré :**
```
"Des centaines de recettes saines"
ou
"Cuisine savoureuse et équilibrée"
```

---

### 📱 Screenshot 4 : GRAPHIQUE DE PROGRESSION

**Objectif** : Visualiser les résultats

**Éléments à afficher :**
- Courbe d'évolution du poids
- Mesures corporelles (tour de taille, etc.)
- Jalons atteints (badges)
- Tendance positive

**Texte overlay suggéré :**
```
"Visualisez vos résultats"
ou
"Progression claire et motivante"
```

---

### 📱 Screenshot 5 : NOTIFICATIONS ET RAPPELS

**Objectif** : Montrer l'accompagnement quotidien

**Éléments à afficher :**
- Liste de notifications
- Rappel de repas avec icône
- Rappel d'hydratation
- Rappel de pesée
- Interface de paramétrage

**Texte overlay suggéré :**
```
"Rappels automatiques personnalisés"
ou
"Ne ratez plus aucun repas"
```

---

### 📱 Screenshot 6 : PAGE RENDEZ-VOUS

**Objectif** : Montrer la facilité de prise de RDV

**Éléments à afficher :**
- Calendrier de disponibilités
- Informations diététicien(ne)
- Historique consultations
- Bouton "Prendre RDV"

**Texte overlay suggéré :**
```
"Prenez rendez-vous en un clic"
ou
"Votre diététicien(ne) toujours disponible"
```

---

### 📱 Screenshot 7 : PROFIL PATIENT

**Objectif** : Personnalisation et données

**Éléments à afficher :**
- Photo de profil (avatar)
- Informations personnelles
- Objectifs nutritionnels
- Préférences alimentaires
- Allergies/intolérances

**Texte overlay suggéré :**
```
"Profil 100% personnalisé"
ou
"Adapté à vos besoins uniques"
```

---

### 📱 Screenshot 8 : ÉCRAN DE CONSULTATION

**Objectif** : Montrer le suivi professionnel

**Éléments à afficher :**
- Notes de consultation
- Recommandations du diététicien
- Documents partagés
- Messagerie sécurisée

**Texte overlay suggéré :**
```
"Suivi professionnel personnalisé"
ou
"Communication directe avec votre diététicien(ne)"
```

---

## 4. AMÉLIORATION DES SCREENSHOTS

### Méthode 4.1 : Ajouter des Overlays de Texte

**Outils recommandés :**
- **Canva** (en ligne, gratuit) - https://canva.com
- **Figma** (en ligne, gratuit) - https://figma.com
- **Photoshop** (payant)
- **GIMP** (gratuit, open source)

#### **Template Canva Simple**

1. **Créez un design 1080x1920px**

2. **Importez votre screenshot**

3. **Ajoutez une barre de texte**
   - Position : Haut ou bas
   - Couleur : Dégradé violet (#667eea → #764ba2)
   - Transparence : 85%

4. **Ajoutez le texte**
   - Police : Montserrat Bold ou Poppins Bold
   - Taille : 48-60px
   - Couleur : Blanc (#FFFFFF)
   - Alignement : Centré

5. **Ajoutez une icône** (optionnel)
   - Icône pertinente (🥗, 📊, 🔔)
   - Taille : 80x80px

#### **Exemple de Layout**

```
┌─────────────────────────────────┐
│  🥗 Plans alimentaires         │ ← Barre overlay (10% haut)
│  personnalisés                  │
├─────────────────────────────────┤
│                                 │
│                                 │
│   [SCREENSHOT DE L'APP]         │
│                                 │
│                                 │
│                                 │
│                                 │
│                                 │
│                                 │
│                                 │
└─────────────────────────────────┘
```

### Méthode 4.2 : Créer un Mockup sur Appareil

**Outils en ligne :**

1. **MockUPhone** - https://mockuphone.com
   - Gratuit
   - Glissez-déposez votre screenshot
   - Sélectionnez un appareil (Pixel, Samsung, etc.)
   - Téléchargez le mockup

2. **Shots.so** - https://shots.so
   - Gratuit avec branding
   - Plus d'options de personnalisation

3. **Mockup World** - https://www.mockupworld.co
   - Templates gratuits à télécharger

#### **Avantages des Mockups**
- ✅ Aspect professionnel
- ✅ Contexte visuel (main tenant le téléphone)
- ✅ Se démarque dans le Play Store

#### **Inconvénients**
- ❌ Peut réduire la taille du screenshot visible
- ❌ Google préfère souvent les screenshots "propres"

**Recommandation** : Utilisez pour 1-2 screenshots max, pas tous.

### Méthode 4.3 : Retouches Légères

**Ajustements recommandés :**

1. **Luminosité/Contraste**
   - Augmentez légèrement la luminosité (+5 à +10%)
   - Augmentez le contraste (+10%)

2. **Netteté**
   - Appliquez un filtre de netteté léger

3. **Couleurs**
   - Saturation +5% pour rendre plus vivant
   - N'exagérez pas !

4. **Recadrage** (si nécessaire)
   - Centrez l'élément principal
   - Respectez les proportions

**Outil en ligne gratuit** : Photopea - https://www.photopea.com
(Alternative gratuite à Photoshop)

---

## 5. TEMPLATES ET OUTILS

### Template 5.1 : Barre de Texte Overlay (Canva)

**Design pré-fait à dupliquer :**

1. Créez un rectangle :
   - Largeur : 1080px
   - Hauteur : 200px
   - Position Y : 0 (haut) ou 1720 (bas)
   - Couleur : Dégradé linéaire
     - Gauche : #667eea
     - Droite : #764ba2
   - Opacité : 90%

2. Ajoutez le texte :
   - Police : Montserrat Bold
   - Taille : 56px
   - Couleur : #FFFFFF
   - Ombre portée : Légère (2px, 20% opacité)
   - Interligne : 1.2

3. Sauvegardez comme template

### Template 5.2 : Frame Device (Figma)

**Créez un template réutilisable :**

1. **Créez un Frame 1080x1920px**

2. **Importez l'image d'un téléphone** (PNG transparent)
   - Recherchez "Pixel 5 mockup PNG transparent"
   - Ou utilisez un template Figma Community

3. **Placez vos screenshots dans le frame**

4. **Dupliquez pour chaque screenshot**

### Tools et Resources

#### **Générateurs de Screenshots**

1. **App Mockup** - https://app-mockup.com
   - Spécialisé Play Store/App Store
   - Génère 8 screenshots en quelques clics

2. **PlaceIt** - https://placeit.net
   - Mockups professionnels
   - Payant mais haute qualité

3. **Screely** - https://screely.com
   - Crée des mockups de navigateur
   - Gratuit

#### **Banques d'Images Libres**

Pour enrichir vos screenshots (si nécessaire) :

1. **Unsplash** - https://unsplash.com
   - Photos haute résolution gratuites
   - Catégories : food, health, lifestyle

2. **Pexels** - https://pexels.com
   - Vidéos et photos gratuites

3. **Icons8** - https://icons8.com
   - Icônes et illustrations

#### **Palettes de Couleurs**

Gardez la cohérence avec l'identité Dietzone :

**Couleurs principales :**
```
Violet primaire : #667eea
Violet foncé : #764ba2
Rose clair : #f093fb
Blanc : #ffffff
Gris texte : #475569
Fond : #f5f7fa
```

---

## 6. CHECKLIST FINALE

### ✅ Vérification Technique

- [ ] **Dimensions** : 1080x1920px (téléphones)
- [ ] **Format** : PNG ou JPG
- [ ] **Taille** : < 8 MB par fichier
- [ ] **Nombre** : Minimum 2, recommandé 4-8
- [ ] **Qualité** : Nette, pas floue
- [ ] **Orientation** : Portrait (vertical)

### ✅ Vérification Contenu

- [ ] **Données test uniquement** (pas de vraies données patients)
- [ ] **Interface complète** visible (pas de chargements)
- [ ] **Aucune erreur** affichée
- [ ] **Textes lisibles** (pas trop petits)
- [ ] **Cohérence visuelle** entre screenshots

### ✅ Vérification Marketing

- [ ] **Ordre logique** (dashboard → fonctionnalités → résultats)
- [ ] **Variété** (pas 8 fois le même type d'écran)
- [ ] **Mise en valeur** des fonctionnalités clés
- [ ] **Attractivité** visuelle
- [ ] **Overlays clairs** mais pas envahissants

### ✅ Vérification Légale

- [ ] **Aucune donnée confidentielle**
- [ ] **Aucun contenu offensant**
- [ ] **Respect droits d'auteur** (photos, icônes)
- [ ] **Conformité Play Store policies**

---

## 📋 EXEMPLE DE NOM DE FICHIERS

Organisez vos screenshots clairement :

```
dietzone_screenshot_01_dashboard.png
dietzone_screenshot_02_meal_plan.png
dietzone_screenshot_03_recipes.png
dietzone_screenshot_04_progress.png
dietzone_screenshot_05_notifications.png
dietzone_screenshot_06_appointment.png
dietzone_screenshot_07_profile.png
dietzone_screenshot_08_consultation.png
```

---

## 💡 CONSEILS PROFESSIONNELS

### Do's ✅

- ✅ Utilisez des données de test réalistes et cohérentes
- ✅ Montrez l'interface en action (pas vide)
- ✅ Ajoutez des overlays de texte courts et percutants
- ✅ Variez les types d'écrans montrés
- ✅ Mettez en avant les USPs (Unique Selling Points)
- ✅ Testez l'ordre des screenshots (les 2 premiers sont cruciaux)

### Don'ts ❌

- ❌ Ne montrez pas d'écrans vides ou de chargement
- ❌ N'utilisez pas de vraies données patients
- ❌ N'ajoutez pas trop de texte qui cache l'interface
- ❌ Ne faites pas de captures floues ou pixelisées
- ❌ N'utilisez pas d'images stock qui n'ont rien à voir avec l'app
- ❌ Ne mentez pas sur les fonctionnalités (risque de rejet)

---

## 🎨 INSPIRATION

### Apps Similaires à Analyser

Recherchez sur Play Store et analysez leurs screenshots :

1. **MyFitnessPal**
   - Excellents overlays de texte
   - Progression claire

2. **Yuka**
   - Design simple et épuré
   - Mise en avant des features

3. **Lifesum**
   - Mockups sur appareils
   - Visuels attractifs

4. **Foodvisor**
   - Screenshots avec contexte
   - Textes courts et impactants

**Regardez les meilleures apps santé** et inspirez-vous (sans copier) !

---

## 🚀 WORKFLOW RECOMMANDÉ

1. **Jour 1 : Captures**
   - Installez l'APK
   - Créez des données de test
   - Capturez 15-20 screenshots
   - Sélectionnez les 8 meilleurs

2. **Jour 2 : Édition**
   - Redimensionnez si nécessaire (1080x1920px)
   - Ajoutez les overlays de texte
   - Appliquez les retouches légères
   - Créez 1-2 mockups sur appareil

3. **Jour 3 : Finalisation**
   - Revue finale avec collègues
   - Demandez des avis externes
   - Export en haute qualité
   - Organisation et nommage des fichiers

4. **Jour 4 : Upload**
   - Upload sur Google Play Console
   - Prévisualisation
   - Ajustements si nécessaire

---

## 📞 BESOIN D'AIDE ?

Si vous n'êtes pas à l'aise avec la création graphique :

### Options :

1. **Fiverr** - https://fiverr.com
   - Cherchez : "play store screenshots design"
   - Budget : 20-50$
   - Délai : 2-7 jours

2. **Upwork** - https://upwork.com
   - Designers freelance
   - Plus cher mais plus pro

3. **99designs** - https://99designs.com
   - Concours de design
   - Plusieurs propositions

4. **Contactez une agence locale au Sénégal**
   - Support en personne
   - Compréhension du marché local

---

**Document créé pour Dietzone by Maestrodan**
**Date : Décembre 2024**
**Contact : contact@dietsenegal.net**

**Bon courage pour vos screenshots ! 📸🎨**
