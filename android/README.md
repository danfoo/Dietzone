# 📱 DietSenegal - Application Android WebView

Application mobile Android pour **DietSenegal** (app.dietsenegal.net) - Version WebView native.

---

## 📋 Prérequis

Avant de compiler l'application, assurez-vous d'avoir :

### 1. **Android Studio**
- Téléchargez et installez [Android Studio](https://developer.android.com/studio) (version 2022.3 ou plus récente)
- Version recommandée : **Android Studio Hedgehog** ou plus récent

### 2. **Java Development Kit (JDK)**
- JDK 17 ou plus récent
- Android Studio l'installe automatiquement, ou téléchargez depuis [Oracle](https://www.oracle.com/java/technologies/downloads/)

### 3. **Android SDK**
- SDK 24 (Android 7.0) minimum
- SDK 34 (Android 14) cible
- Android Studio gère l'installation via SDK Manager

---

## 🚀 Installation et Compilation

### **Méthode 1 : Avec Android Studio (Recommandée)**

1. **Ouvrir le projet** :
   ```bash
   # Ouvrir Android Studio
   File > Open > Sélectionner le dossier /android/
   ```

2. **Synchroniser Gradle** :
   - Android Studio va automatiquement détecter et synchroniser le projet
   - Attendez que "Gradle sync" se termine (barre de progression en bas)
   - Si demandé, acceptez l'installation des composants manquants

3. **Compiler l'APK Debug** :
   ```
   Build > Build Bundle(s) / APK(s) > Build APK(s)
   ```
   - L'APK sera généré dans : `app/build/outputs/apk/debug/app-debug.apk`

4. **Compiler l'APK Release (signée)** :
   ```
   Build > Generate Signed Bundle / APK > APK
   ```
   - Créez un keystore si vous n'en avez pas
   - Suivez l'assistant pour signer l'APK
   - L'APK sera dans : `app/build/outputs/apk/release/app-release.apk`

### **Méthode 2 : Ligne de commande (Terminal)**

#### Sur Linux/Mac :

```bash
cd android/

# Rendre gradlew exécutable
chmod +x gradlew

# Compiler APK Debug
./gradlew assembleDebug

# Compiler APK Release
./gradlew assembleRelease
```

#### Sur Windows :

```cmd
cd android

# Compiler APK Debug
gradlew.bat assembleDebug

# Compiler APK Release
gradlew.bat assembleRelease
```

**Résultat** :
- Debug : `app/build/outputs/apk/debug/app-debug.apk`
- Release : `app/build/outputs/apk/release/app-release.apk`

---

## 📦 Installer l'APK sur un appareil

### **Via Android Studio** :

1. Activez le **mode développeur** sur votre téléphone Android :
   - Paramètres > À propos du téléphone > Appuyez 7 fois sur "Numéro de build"

2. Activez le **débogage USB** :
   - Paramètres > Options pour les développeurs > Débogage USB

3. Connectez votre téléphone via USB

4. Dans Android Studio :
   ```
   Run > Run 'app' (ou appuyez sur Shift+F10)
   ```

### **Via ADB (Android Debug Bridge)** :

```bash
# Installer l'APK
adb install app/build/outputs/apk/debug/app-debug.apk

# Ou forcer la réinstallation
adb install -r app/build/outputs/apk/debug/app-debug.apk
```

### **Installation manuelle** :

1. Transférez l'APK sur votre téléphone
2. Ouvrez le fichier APK
3. Autorisez l'installation depuis des sources inconnues si demandé
4. Suivez les instructions d'installation

---

## 🎨 Personnalisation

### **Changer l'URL de l'application** :

Éditez `MainActivity.java` (ligne 47) :
```java
private static final String BASE_URL = "https://app.dietsenegal.net";
```

### **Changer le nom de l'application** :

Éditez `res/values/strings.xml` :
```xml
<string name="app_name">DietSenegal</string>
```

### **Changer les couleurs** :

Éditez `res/values/colors.xml` :
```xml
<color name="primary">#01807B</color>
<color name="accent">#FFC925</color>
```

### **Changer l'icône de l'application** :

1. Générez vos icônes sur [Android Asset Studio](https://romannurik.github.io/AndroidAssetStudio/icons-launcher.html)
2. Remplacez les fichiers dans :
   ```
   res/mipmap-hdpi/
   res/mipmap-mdpi/
   res/mipmap-xhdpi/
   res/mipmap-xxhdpi/
   res/mipmap-xxxhdpi/
   ```

---

## 🔑 Signer l'APK pour le Play Store

### 1. **Créer un keystore** :

```bash
keytool -genkey -v -keystore dietsenegal-release-key.jks \
  -keyalg RSA -keysize 2048 -validity 10000 \
  -alias dietsenegal
```

### 2. **Configurer la signature** :

Ajoutez dans `app/build.gradle` :
```gradle
android {
    signingConfigs {
        release {
            storeFile file("dietsenegal-release-key.jks")
            storePassword "votre_password"
            keyAlias "dietsenegal"
            keyPassword "votre_password"
        }
    }

    buildTypes {
        release {
            signingConfig signingConfigs.release
        }
    }
}
```

### 3. **Compiler l'APK signée** :

```bash
./gradlew assembleRelease
```

---

## 📱 Fonctionnalités de l'application

✅ **WebView native** avec toutes les fonctionnalités web
✅ **Support JavaScript** complet
✅ **Upload de fichiers** (photos, documents)
✅ **Géolocalisation** (avec permission)
✅ **Pull-to-refresh** (glisser vers le bas pour actualiser)
✅ **Navigation** avec bouton retour
✅ **Téléchargement de fichiers**
✅ **Support des cookies**
✅ **Cache optimisé**
✅ **Splash screen** personnalisé
✅ **Deep linking** (liens vers l'app)
✅ **Mode offline** (cache)
✅ **Support Firebase** (notifications push)

---

## 🔧 Problèmes Courants

### **Gradle sync failed**
```bash
# Solution : Nettoyer le projet
./gradlew clean
```

### **SDK manquant**
```
Tools > SDK Manager > Installer SDK 34
```

### **Build failed - Java version**
```
File > Project Structure > SDK Location
Vérifiez que JDK 17+ est sélectionné
```

### **APK non installée sur le téléphone**
```
Vérifiez que l'APK précédente est désinstallée
Ou utilisez: adb install -r app-debug.apk
```

---

## 📊 Taille de l'APK

- **Debug APK** : ~8-10 MB
- **Release APK (minifiée)** : ~5-7 MB

---

## 🔐 Permissions requises

L'application demande les permissions suivantes :

- ✅ **INTERNET** - Accès web
- ✅ **ACCESS_NETWORK_STATE** - État de la connexion
- ✅ **CAMERA** - Prendre des photos
- ✅ **READ_EXTERNAL_STORAGE** - Lire les fichiers
- ✅ **WRITE_EXTERNAL_STORAGE** - Télécharger des fichiers
- ✅ **ACCESS_FINE_LOCATION** - Géolocalisation précise
- ✅ **POST_NOTIFICATIONS** - Notifications push (Android 13+)

---

## 📝 Structure du projet

```
android/
├── app/
│   ├── src/
│   │   └── main/
│   │       ├── java/com/dietsenegal/app/
│   │       │   ├── MainActivity.java          # Activité principale
│   │       │   └── SplashActivity.java        # Écran de démarrage
│   │       ├── res/
│   │       │   ├── layout/                    # Layouts XML
│   │       │   ├── values/                    # Couleurs, strings, themes
│   │       │   ├── drawable/                  # Images et drawables
│   │       │   ├── mipmap-*/                  # Icônes de l'app
│   │       │   └── xml/                       # Config réseau, file paths
│   │       └── AndroidManifest.xml            # Manifest de l'app
│   ├── build.gradle                           # Config Gradle app
│   └── proguard-rules.pro                     # Règles ProGuard
├── build.gradle                               # Config Gradle projet
├── settings.gradle                            # Settings Gradle
└── gradle.properties                          # Propriétés Gradle
```

---

## 🚀 Prochaines étapes

### **Pour publier sur Google Play Store** :

1. Créez un compte [Google Play Console](https://play.google.com/console)
2. Préparez les assets :
   - Icône haute résolution (512x512)
   - Captures d'écran (min. 2 par format)
   - Description de l'app
   - Politique de confidentialité
3. Générez un **AAB** (Android App Bundle) au lieu d'un APK :
   ```bash
   ./gradlew bundleRelease
   ```
4. Uploadez le fichier `app-release.aab` sur Play Console

---

## 🆘 Support

Pour toute question ou problème :

- **Documentation Android** : https://developer.android.com/guide
- **Gradle** : https://docs.gradle.org/
- **WebView** : https://developer.android.com/guide/webapps/webview

---

## 📄 Licence

Cette application est propriété de **DietSenegal**.
Tous droits réservés © 2025 DietSenegal

---

**Version** : 1.0.0
**Package** : com.dietsenegal.app
**SDK Min** : 24 (Android 7.0)
**SDK Target** : 34 (Android 14)
