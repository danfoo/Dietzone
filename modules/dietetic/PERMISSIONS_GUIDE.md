# Guide des Permissions - Module Diététique

## 🔐 Permissions Disponibles

Le module Diététique dispose maintenant de **7 permissions granulaires** que vous pouvez attribuer individuellement aux membres du staff :

| Permission | Nom dans Perfex | Description | Accès accordé |
|------------|-----------------|-------------|---------------|
| **View (Global)** | `Affichage (Globale)` | Permission de base | Accès au module et au dashboard |
| **Create** | `Créer` | Créer des ressources | Créer patients, consultations, programmes |
| **Edit** | `Modifier` | Modifier des ressources | Modifier patients, consultations, programmes |
| **Delete** | `Supprimer` | Supprimer des ressources | Supprimer patients, consultations, programmes (admins seulement recommandé) |
| **Settings** | `Accéder aux Paramètres` | ⭐ NOUVEAU | Accès à la page Paramètres du module |
| **Manage Foods** | `Gérer la Base Alimentaire` | ⭐ NOUVEAU | Créer, modifier les aliments |
| **View Dietitians** | `Voir la Liste des Diététiciens` | ⭐ NOUVEAU | Accès à la liste des diététiciens et leurs notes |

---

## 📋 Comment Configurer les Permissions

### Étape 1 : Accéder à la Configuration des Rôles

1. Allez dans **Setup** (Configuration)
2. Cliquez sur **Roles** (Rôles)
3. Sélectionnez le rôle à modifier (ex: "Diététicien", "Assistant", etc.)

### Étape 2 : Configurer les Permissions du Module Diététique

Dans la section **Diététique**, vous verrez maintenant toutes les permissions :

```
☐ Affichage (Globale)
☐ Créer
☐ Modifier
☐ Supprimer
☐ Accéder aux Paramètres
☐ Gérer la Base Alimentaire
☐ Voir la Liste des Diététiciens
```

Cochez les cases appropriées selon le rôle.

---

## 👥 Exemples de Configurations par Rôle

### 🩺 **Rôle : Diététicien**

**Permissions recommandées :**
- ✅ Affichage (Globale)
- ✅ Créer
- ✅ Modifier
- ❌ Supprimer (sécurité - réservé aux admins)
- ❌ Accéder aux Paramètres (réservé aux admins)
- ✅ **Gérer la Base Alimentaire** ← Important !
- ✅ **Voir la Liste des Diététiciens**

**Ce que le diététicien peut faire :**
- ✅ Voir ses patients assignés uniquement
- ✅ Créer/modifier consultations et programmes
- ✅ Ajouter/modifier des aliments dans la base alimentaire
- ✅ Voir la liste des autres diététiciens (utile pour collaboration)
- ❌ Ne peut pas accéder aux paramètres système
- ❌ Ne peut pas voir les patients d'autres diététiciens

**Menu visible :**
```
Diététique
├── Tableau de bord
├── Patients (uniquement ses patients)
├── Consultations
├── Programmes
├── Diététiciens
└── Base Alimentaire
```

---

### 👨‍💼 **Rôle : Manager / Responsable**

**Permissions recommandées :**
- ✅ Affichage (Globale)
- ✅ Créer
- ✅ Modifier
- ❌ Supprimer (prudence)
- ✅ **Accéder aux Paramètres**
- ✅ **Gérer la Base Alimentaire**
- ✅ **Voir la Liste des Diététiciens**

**Ce que le manager peut faire :**
- ✅ Voir TOUS les patients (globale)
- ✅ Configurer les paramètres du module
- ✅ Gérer la base alimentaire
- ✅ Superviser les diététiciens

**Menu visible :**
```
Diététique
├── Tableau de bord
├── Patients (tous)
├── Consultations
├── Programmes
├── Diététiciens
├── Base Alimentaire
└── Paramètres
```

---

### 📝 **Rôle : Assistant / Secrétaire**

**Permissions recommandées :**
- ✅ Affichage (Globale)
- ✅ Créer (pour planifier RDV)
- ❌ Modifier (sauf si besoin)
- ❌ Supprimer
- ❌ Accéder aux Paramètres
- ❌ Gérer la Base Alimentaire
- ✅ **Voir la Liste des Diététiciens** (pour planifier RDV)

**Ce que l'assistant peut faire :**
- ✅ Voir tous les patients
- ✅ Créer des consultations (prise de RDV)
- ✅ Voir la liste des diététiciens
- ❌ Ne peut pas modifier les programmes alimentaires
- ❌ Ne peut pas gérer les aliments

**Menu visible :**
```
Diététique
├── Tableau de bord
├── Patients
├── Consultations
├── Programmes (lecture seule)
└── Diététiciens
```

---

### 🔧 **Rôle : Administrateur**

**Permissions recommandées :**
- ✅ **TOUTES** les permissions

**Les admins ont accès complet à tout** (bypass automatique des restrictions).

---

## 🎯 Impact des Nouvelles Permissions

### 1️⃣ **Permission "Accéder aux Paramètres"**

**Sans cette permission :**
- ❌ L'item "Paramètres" n'apparaît PAS dans le menu
- ❌ Accès direct refusé (erreur 403)

**Avec cette permission :**
- ✅ Item "Paramètres" visible dans le menu
- ✅ Peut configurer : API SMS, rappels automatiques, etc.

**Fichiers concernés :**
- `modules/dietetic/controllers/Dietetic.php` → méthode `settings()`

---

### 2️⃣ **Permission "Gérer la Base Alimentaire"**

**Sans cette permission :**
- ❌ L'item "Base Alimentaire" n'apparaît PAS dans le menu
- ❌ Ne peut pas créer/modifier d'aliments
- ❌ Bloqué lors de la création de plans de repas

**Avec cette permission :**
- ✅ Item "Base Alimentaire" visible dans le menu
- ✅ Bouton "Nouvel Aliment" accessible
- ✅ Peut créer et modifier des aliments
- ❌ Ne peut PAS supprimer (réservé aux admins pour sécurité)

**Fichiers concernés :**
- `modules/dietetic/controllers/Foods.php` → méthodes `create()` et `edit()`

**⚠️ Important :** Les diététiciens DOIVENT avoir cette permission pour être autonomes !

---

### 3️⃣ **Permission "Voir la Liste des Diététiciens"**

**Sans cette permission :**
- ❌ L'item "Diététiciens" n'apparaît PAS dans le menu
- ❌ Ne peut pas voir les notes/évaluations des diététiciens

**Avec cette permission :**
- ✅ Item "Diététiciens" visible dans le menu
- ✅ Peut voir la liste de tous les diététiciens
- ✅ Peut voir les notes/évaluations moyennes
- ✅ Utile pour collaboration et référencement entre diététiciens

**Fichiers concernés :**
- `modules/dietetic/controllers/Dietitians.php` → méthode `index()`

---

## 🔄 Migration des Permissions Existantes

### Pour les utilisateurs existants :

Les anciennes permissions continuent de fonctionner :
- ✅ Les admins ont toujours accès complet
- ✅ Les utilisateurs avec permission "View" peuvent toujours accéder au module

**Mais attention :**
- ⚠️ Les diététiciens existants n'auront PAS automatiquement les nouvelles permissions
- ⚠️ Vous devez **manuellement activer** "Gérer la Base Alimentaire" pour eux

---

## 📝 Checklist Post-Installation

Après avoir déployé cette mise à jour :

1. ✅ Aller dans **Setup → Roles**
2. ✅ Pour CHAQUE rôle "Diététicien" :
   - Cocher **"Gérer la Base Alimentaire"**
   - Cocher **"Voir la Liste des Diététiciens"** (optionnel mais recommandé)
3. ✅ Pour les rôles "Manager/Responsable" :
   - Cocher **"Accéder aux Paramètres"**
   - Cocher **"Gérer la Base Alimentaire"**
   - Cocher **"Voir la Liste des Diététiciens"**
4. ✅ Tester en se connectant avec un compte diététicien
5. ✅ Vérifier que le menu "Base Alimentaire" est visible
6. ✅ Vérifier que la création d'aliment fonctionne

---

## 🐛 Dépannage

### Problème : "Le diététicien ne peut pas ajouter d'aliments"

**Solution :**
1. Vérifier que le rôle du diététicien a la permission **"Gérer la Base Alimentaire"** activée
2. Se déconnecter et se reconnecter pour rafraîchir les permissions
3. Vider le cache du navigateur si nécessaire

### Problème : "Le menu Paramètres/Base Alimentaire n'apparaît pas"

**Solution :**
1. Vérifier les permissions du rôle dans Setup → Roles
2. Les items de menu sont automatiquement masqués si la permission est absente
3. C'est normal ! Activez la permission appropriée

### Problème : "Les diététiciens voient tous les patients"

**Solution :**
1. Ce problème a été corrigé dans cette mise à jour
2. La fonction `dietetic_is_admin()` ne donne plus les droits admin à tous
3. Seuls les vrais admins voient tous les patients
4. Les diététiciens ne voient que leurs patients assignés

---

## 🔗 Fichiers Modifiés

Cette mise à jour modifie les fichiers suivants :

1. **`modules/dietetic/dietetic.php`**
   - Ajout des 3 nouvelles permissions dans `dietetic_permissions()`
   - Menu conditionnel selon les permissions

2. **`modules/dietetic/language/french/dietetic_lang.php`**
   - Traductions des nouvelles permissions

3. **`modules/dietetic/controllers/Dietetic.php`**
   - Vérification permission `settings` au lieu de `is_admin()`

4. **`modules/dietetic/controllers/Foods.php`**
   - Vérification permission `manage_foods` pour create/edit

5. **`modules/dietetic/controllers/Dietitians.php`**
   - Vérification permission `view_dietitians` pour index

6. **`modules/dietetic/helpers/dietetic_helper.php`**
   - Correction de `dietetic_is_admin()` (commit précédent)

---

## 📞 Support

Si vous rencontrez des problèmes avec les permissions :

1. Vérifiez d'abord ce guide
2. Consultez les logs d'activité Perfex
3. Testez avec un compte admin pour confirmer que ça fonctionne
4. Comparez les permissions du rôle qui fonctionne vs celui qui ne fonctionne pas

---

**Date de création :** 2025-11-06
**Version module :** 1.0.0
**Commit :** À venir
**Branche :** `claude/build-dietetic-crm-module-011CUg57kDmPcHyfwwy6HAYZ`
