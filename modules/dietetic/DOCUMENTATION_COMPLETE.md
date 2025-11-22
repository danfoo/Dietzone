# 📚 Documentation Complète - Module Diététique

**Dernière mise à jour**: 22 novembre 2025
**Auteur**: Claude AI Assistant
**Version**: 1.0.0

---

## 📋 Table des matières

1. [Vue d'ensemble](#vue-densemble)
2. [Système de Notifications Push](#système-de-notifications-push)
3. [Système de Permissions](#système-de-permissions)
4. [Pages Légales](#pages-légales)
5. [Redirections Automatiques](#redirections-automatiques)
6. [Sécurité - Contrôle d'accès aux Recettes](#sécurité---contrôle-daccès-aux-recettes)
7. [Architecture et Fichiers](#architecture-et-fichiers)
8. [Hooks et Intégrations](#hooks-et-intégrations)
9. [API et Endpoints](#api-et-endpoints)
10. [Troubleshooting](#troubleshooting)

---

## 🌟 Vue d'ensemble

Le module diététique de Perfex CRM offre une solution complète de gestion pour les diététiciens et leurs patients.

### Fonctionnalités principales

- ✅ Gestion des patients (profils, mesures, objectifs)
- ✅ Programmes nutritionnels personnalisés
- ✅ Plans de repas hebdomadaires
- ✅ Base de données alimentaire complète
- ✅ Bibliothèque de recettes avec système de favoris
- ✅ Consultations et rendez-vous
- ✅ **Notifications push en temps réel (Firebase Cloud Messaging)**
- ✅ Portail patient mobile-friendly
- ✅ Enquêtes alimentaires avec photos
- ✅ Pages légales (Politique de confidentialité, CGU)
- ✅ Système de permissions granulaires

---

## 🔔 Système de Notifications Push

### Architecture Firebase Cloud Messaging (FCM)

Le système de notifications utilise Firebase Cloud Messaging pour envoyer des notifications push aux patients via leur navigateur ou application mobile.

#### 1. Configuration Firebase

**Fichier**: `modules/dietetic/controllers/Notifications.php`

**Configuration requise**:
```php
// Clé serveur FCM à configurer dans les paramètres
$fcm_server_key = get_option('dietetic_fcm_server_key');
$fcm_api_url = 'https://fcm.googleapis.com/fcm/send';
```

**Comment obtenir la clé**:
1. Accéder à [Firebase Console](https://console.firebase.google.com/)
2. Sélectionner votre projet
3. Settings → Cloud Messaging → Server key

#### 2. Enregistrement des tokens

**Table**: `tbldietic_patient_fcm_tokens`

```sql
CREATE TABLE `tbldietic_patient_fcm_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `fcm_token` text NOT NULL,
  `device_type` varchar(50) DEFAULT 'web',
  `created_at` datetime NOT NULL,
  `last_used_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `patient_id` (`patient_id`)
);
```

**Processus**:
1. Patient accède au portail → JavaScript demande permission notifications
2. Firebase génère un token unique
3. Token envoyé via AJAX: `/dietetic/portal/save_fcm_token`
4. Token stocké dans la base de données lié au patient

#### 3. Préférences de notifications

**Table**: `tbldietic_notification_preferences`

```sql
CREATE TABLE `tbldietic_notification_preferences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `meal_reminders` tinyint(1) DEFAULT 1,
  `measurement_reminders` tinyint(1) DEFAULT 1,
  `consultation_reminders` tinyint(1) DEFAULT 1,
  `recipe_updates` tinyint(1) DEFAULT 1,
  `program_updates` tinyint(1) DEFAULT 1,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `patient_id` (`patient_id`)
);
```

**Page de gestion**: `/dietetic/portal/notification_preferences`

Les patients peuvent activer/désactiver chaque type de notification.

#### 4. Envoi de notifications

**Contrôleur**: `modules/dietetic/controllers/Notifications.php`

**Méthode principale**: `send_push_notification($patient_id, $title, $body, $data = [])`

**Exemple d'utilisation**:
```php
$this->load->model('dietetic/dietetic_notifications_model');

$this->dietetic_notifications_model->send_push_notification(
    $patient_id,
    'Nouvelle recette disponible!',
    'Découvrez la recette: Salade César Légère',
    [
        'type' => 'recipe',
        'recipe_id' => 123,
        'url' => site_url('dietetic/portal/recipes/view/123')
    ]
);
```

#### 5. Types de notifications automatiques

| Type | Déclencheur | Template |
|------|-------------|----------|
| **Consultation** | 24h avant RDV | "Rappel: Consultation demain à {heure}" |
| **Mesure** | Rappel hebdomadaire | "N'oubliez pas d'enregistrer vos mesures" |
| **Recette** | Nouvelle assignation | "Nouvelle recette: {nom}" |
| **Programme** | Mise à jour | "Votre programme a été mis à jour" |
| **Repas** | Rappel horaire | "Il est l'heure de: {type_repas}" |

#### 6. Frontend JavaScript (Service Worker)

**Fichier**: `modules/dietetic/assets/js/firebase-messaging-sw.js`

```javascript
// Service Worker pour gérer les notifications en arrière-plan
importScripts('https://www.gstatic.com/firebasejs/9.x.x/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/9.x.x/firebase-messaging-compat.js');

firebase.initializeApp({
    apiKey: "YOUR_API_KEY",
    authDomain: "YOUR_AUTH_DOMAIN",
    projectId: "YOUR_PROJECT_ID",
    storageBucket: "YOUR_STORAGE_BUCKET",
    messagingSenderId: "YOUR_SENDER_ID",
    appId: "YOUR_APP_ID"
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage((payload) => {
    const notificationTitle = payload.notification.title;
    const notificationOptions = {
        body: payload.notification.body,
        icon: '/assets/images/logo.png'
    };

    self.registration.showNotification(notificationTitle, notificationOptions);
});
```

#### 7. Affichage dans le portail

**Panel de notifications**: Accessible via l'icône cloche dans le header

**Fichier**: `modules/dietetic/views/portal/includes/portal_header.php`

- Badge rouge avec nombre de notifications non lues
- Panel déroulant avec liste des notifications
- Actions: Marquer comme lu, Supprimer, Tout marquer comme lu
- Chargement dynamique via AJAX

---

## 🔐 Système de Permissions

### Permissions globales du module

**Fichier**: `modules/dietetic/dietetic.php` - fonction `dietetic_permissions()`

| Permission | Clé | Description | Accès accordé |
|------------|-----|-------------|---------------|
| **Affichage Global** | `view` | Permission de base | Accès au module et au dashboard |
| **Créer** | `create` | Créer des ressources | Créer patients, consultations, programmes |
| **Modifier** | `edit` | Modifier des ressources | Modifier patients, consultations, programmes |
| **Supprimer** | `delete` | Supprimer des ressources | Supprimer (recommandé admins uniquement) |
| **Paramètres** | `settings` | Accéder aux paramètres | Configuration du module |
| **Gérer Aliments** | `manage_foods` | Gérer base alimentaire | Créer/modifier aliments |
| **Voir Diététiciens** | `view_dietitians` | Liste diététiciens | Voir notes et évaluations |
| **Modifier Toutes Recettes** | `edit_all_recipes` | Modifier recettes autres | Modifier recettes d'autres diététiciens |
| **Supprimer Toutes Recettes** | `delete_all_recipes` | Supprimer recettes autres | Supprimer recettes d'autres diététiciens |

### Configuration des permissions

**Location**: Setup → Roles → Sélectionner rôle → Section "Dietetic"

### Permissions personnalisées (Staff)

**Table**: `tbldietic_staff_permissions`

Permet un contrôle encore plus granulaire par diététicien:

```sql
CREATE TABLE `tbldietic_staff_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `staff_id` int(11) NOT NULL,
  `permission_key` varchar(100) NOT NULL,
  `permission_value` tinyint(1) DEFAULT 1,
  `granted_by` int(11) DEFAULT NULL,
  `granted_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_staff_permission` (`staff_id`, `permission_key`)
);
```

**Permissions disponibles**:
- `food_surveys` - Accès aux enquêtes alimentaires
- `notifications_manage` - Gérer paramètres notifications (admin)
- `reports_advanced` - Rapports avancés
- `settings_module` - Paramètres du module

**Interface**: `/admin/dietetic/staff_permissions`

### Fonctions helper de vérification

**Fichier**: `modules/dietetic/helpers/dietetic_helper.php`

```php
// Vérifier permission basique
dietetic_has_permission($permission)

// Vérifier si peut éditer une recette spécifique
dietetic_can_edit_recipe($recipe)

// Vérifier si peut supprimer une recette spécifique
dietetic_can_delete_recipe($recipe)

// Vérifier si utilisateur est admin diététique
dietetic_is_admin()
```

---

## 📄 Pages Légales

### Configuration

Les administrateurs peuvent gérer le contenu des pages légales depuis:

**Location**: Diététique → Pages Légales

### Stockage

**Table**: `tbldietic_settings`

```sql
-- Clés utilisées
setting_key = 'privacy_policy'
setting_key = 'terms_of_service'
```

### Modèle

**Fichier**: `modules/dietetic/models/Dietetic_settings_model.php`

```php
// Récupérer contenu
$content = $this->dietetic_settings_model->get_setting('privacy_policy');

// Mettre à jour contenu
$this->dietetic_settings_model->update_setting('privacy_policy', $html_content);
```

### Pages accessibles

#### Pour les admins/staff:
- `/admin/dietetic/legal_pages/manage` - Édition
- `/admin/dietetic/legal_pages/privacy` - Affichage Politique
- `/admin/dietetic/legal_pages/terms` - Affichage CGU

#### Pour les patients:
- `/dietetic/portal/privacy` - Politique de Confidentialité
- `/dietetic/portal/terms` - Conditions d'Utilisation

### Intégration dans les menus

**Menu latéral patient** (`portal_header.php`):
```php
<a href="<?php echo site_url('dietetic/portal/privacy'); ?>" class="menu-item">
    <i class="fa fa-shield"></i>
    <span>Politique de Confidentialité</span>
</a>

<a href="<?php echo site_url('dietetic/portal/terms'); ?>" class="menu-item">
    <i class="fa fa-file-text"></i>
    <span>Conditions d'Utilisation</span>
</a>
```

**Dropdown header** (admin + patient):
Ajouté automatiquement via JavaScript dans le header

### Support HTML

Les pages légales supportent du HTML complet pour formatage:
- Titres: `<h1>`, `<h2>`, `<h3>`
- Paragraphes: `<p>`
- Listes: `<ul>`, `<ol>`, `<li>`
- Liens: `<a href="...">`
- Gras: `<strong>`
- Code: `<code>`

---

## 🔄 Redirections Automatiques

### Staff → Dashboard Diététique

**Fichier**: `modules/dietetic/dietetic.php`
**Fonction**: `dietetic_redirect_to_dashboard()`
**Hook**: `pre_controller`

#### Comportement:
- ✅ Diététicien non-admin accède à `/admin` → Redirigé vers `/admin/dietetic/dashboard`
- ✅ Admin → Pas de redirection (garde accès complet)
- ✅ Staff sans permission dietetic → Pas de redirection

#### Code:
```php
hooks()->add_action('pre_controller', 'dietetic_redirect_to_dashboard');

function dietetic_redirect_to_dashboard()
{
    if (!is_staff_logged_in()) return;

    $current_uri = $_SERVER['REQUEST_URI'] ?? '';

    // Seulement depuis /admin ou /admin/dashboard
    if (!preg_match('#/admin/?$|/admin/dashboard/?$#', $current_uri)) {
        return;
    }

    if (is_admin()) return; // Admins gardent accès complet

    if (!has_permission('dietetic', '', 'view')) return;

    redirect(admin_url('dietetic/dashboard'));
}
```

### Patients → Portail Diététique (DÉSACTIVÉ)

**Statut**: Temporairement désactivé à la demande de l'utilisateur

**Pour réactiver plus tard**, le code serait:
```php
hooks()->add_action('after_contact_login', 'dietetic_redirect_patient_after_login');
hooks()->add_action('pre_controller', 'dietetic_redirect_to_dashboard'); // Ajouter section patients
```

---

## 🔒 Sécurité - Contrôle d'accès aux Recettes

### Problème résolu

**Avant**: Les diététiciens pouvaient modifier/supprimer les recettes de n'importe quel autre diététicien

**Après**: Les diététiciens ne peuvent modifier/supprimer que leurs propres recettes (sauf permission spéciale)

### Implémentation

#### 1. Fonctions Helper

**Fichier**: `modules/dietetic/helpers/dietetic_helper.php` (lignes 337-429)

```php
/**
 * Vérifie si l'utilisateur peut éditer une recette
 * @param mixed $recipe - ID ou objet recette
 * @return bool
 */
function dietetic_can_edit_recipe($recipe)
{
    // Admin = toujours autorisé
    if (is_admin()) return true;

    // Doit avoir permission 'edit' de base
    if (!dietetic_has_permission('edit')) return false;

    $CI =& get_instance();

    // Charger recette si ID fourni
    if (is_numeric($recipe)) {
        $CI->load->model('dietetic/dietetic_recipes_model');
        $recipe = $CI->dietetic_recipes_model->get($recipe);
    }

    if (!$recipe || !isset($recipe->dietitian_id)) {
        return false;
    }

    // Propriétaire = autorisé
    $current_user_id = get_staff_user_id();
    if ($recipe->dietitian_id == $current_user_id) {
        return true;
    }

    // Permission spéciale 'edit_all_recipes'
    if (has_permission('dietetic', '', 'edit_all_recipes')) {
        return true;
    }

    return false;
}

/**
 * Vérifie si l'utilisateur peut supprimer une recette
 * Même logique que edit
 */
function dietetic_can_delete_recipe($recipe)
{
    // Code similaire avec 'delete_all_recipes'
}
```

#### 2. Contrôleur Recipes.php

**Fichier**: `modules/dietetic/controllers/Recipes.php`

**Méthode edit()** (lignes 168-180):
```php
public function edit($id)
{
    $data['recipe'] = $this->dietetic_recipes_model->get($id);

    if (!$data['recipe']) {
        show_404();
    }

    // ✅ VÉRIFICATION DE SÉCURITÉ
    if (!dietetic_can_edit_recipe($data['recipe'])) {
        set_alert('danger', 'Vous ne pouvez modifier que vos propres recettes.');
        redirect(admin_url('dietetic/recipes/view/' . $id));
    }

    // Suite du code...
}
```

**Méthode delete()** (lignes 268-285):
```php
public function delete($id)
{
    // ✅ VÉRIFICATION DE SÉCURITÉ
    if (!dietetic_can_delete_recipe($id)) {
        set_alert('danger', 'Vous ne pouvez supprimer que vos propres recettes.');
        redirect(admin_url('dietetic/recipes'));
    }

    // Suite du code...
}
```

#### 3. Vue - Boutons conditionnels

**Fichier**: `modules/dietetic/views/admin/recipes/list.php` (lignes 526-544)

```php
<?php if (dietetic_can_edit_recipe($recipe)) : ?>
    <a href="<?php echo admin_url('dietetic/recipes/edit/' . $recipe->id); ?>"
       class="btn btn-default btn-sm">
        <i class="fa fa-pencil"></i> Modifier
    </a>
<?php endif; ?>

<?php if (dietetic_can_delete_recipe($recipe)) : ?>
    <a href="<?php echo admin_url('dietetic/recipes/delete/' . $recipe->id); ?>"
       class="btn btn-danger btn-sm"
       onclick="return confirm('Supprimer cette recette ?');">
        <i class="fa fa-trash"></i> Supprimer
    </a>
<?php endif; ?>
```

### Attribution des permissions

**Setup → Staff → Permissions → Dietetic**

Pour permettre à un utilisateur de modifier/supprimer toutes les recettes:
- ☑ Modifier Toutes les Recettes (`edit_all_recipes`)
- ☑ Supprimer Toutes les Recettes (`delete_all_recipes`)

---

## 📁 Architecture et Fichiers

### Structure du module

```
modules/dietetic/
├── assets/
│   ├── css/
│   │   ├── dietetic.css
│   │   └── dietetic_portal.css
│   └── js/
│       ├── dietetic.js
│       ├── dietetic_portal.js
│       └── firebase-messaging-sw.js
├── controllers/
│   ├── Dietetic.php (Dashboard admin)
│   ├── Portal.php (Dashboard patient)
│   ├── Patients.php
│   ├── Consultations.php
│   ├── Programs.php
│   ├── Recipes.php
│   ├── Foods.php
│   ├── Notifications.php
│   ├── Legal_pages.php ← NOUVEAU
│   └── Staff_permissions.php
├── models/
│   ├── Dietetic_patients_model.php
│   ├── Dietetic_consultations_model.php
│   ├── Dietetic_programs_model.php
│   ├── Dietetic_recipes_model.php
│   ├── Dietetic_foods_model.php
│   ├── Dietetic_notifications_model.php
│   ├── Dietetic_settings_model.php ← NOUVEAU
│   └── Dietetic_measurements_model.php
├── views/
│   ├── admin/
│   │   ├── dashboard.php
│   │   ├── patients/
│   │   ├── consultations/
│   │   ├── recipes/
│   │   ├── foods/
│   │   ├── notifications/
│   │   └── legal_pages/ ← NOUVEAU
│   │       ├── manage.php
│   │       └── view.php
│   ├── portal/
│   │   ├── includes/
│   │   │   ├── portal_header.php
│   │   │   └── portal_footer.php
│   │   ├── recipes/
│   │   ├── food_surveys/
│   │   ├── notifications/
│   │   └── legal_pages/ ← NOUVEAU (non utilisé)
│   ├── portal_dashboard.php
│   ├── portal_consultations.php
│   ├── portal_meal_plans.php
│   ├── portal_recipes.php
│   ├── portal_legal_page.php ← NOUVEAU
│   └── ...
├── helpers/
│   └── dietetic_helper.php (fonctions utilitaires)
├── language/
│   └── french/
│       └── dietetic_lang.php
├── migrations/
│   └── add_staff_permissions.sql
├── install.php
├── install.sql
├── uninstall.sql
└── dietetic.php (fichier principal du module)
```

### Fichiers clés

| Fichier | Rôle | Importance |
|---------|------|------------|
| `dietetic.php` | Point d'entrée du module, hooks, permissions, menus | ⭐⭐⭐⭐⭐ |
| `dietetic_helper.php` | Fonctions utilitaires, vérifications permissions | ⭐⭐⭐⭐⭐ |
| `Portal.php` | Contrôleur principal du portail patient | ⭐⭐⭐⭐⭐ |
| `Notifications.php` | Gestion notifications push FCM | ⭐⭐⭐⭐ |
| `Legal_pages.php` | Gestion pages légales | ⭐⭐⭐ |
| `Dietetic_settings_model.php` | Stockage paramètres | ⭐⭐⭐ |

---

## 🔗 Hooks et Intégrations

### Hooks Perfex utilisés

```php
// Menu admin
hooks()->add_action('admin_init', 'dietetic_module_init_menu_items');

// Permissions
hooks()->add_action('admin_init', 'dietetic_permissions');

// Redirections
hooks()->add_action('pre_controller', 'dietetic_redirect_to_dashboard');

// CSS/JS admin
hooks()->add_action('app_admin_head', 'dietetic_add_head_components');
hooks()->add_action('app_admin_footer', 'dietetic_add_footer_components');

// CSS/JS portail client
hooks()->add_action('app_customers_portal_head', 'dietetic_add_portal_head_components');

// Menu portail client
hooks()->add_action('customers_navigation_start', 'dietetic_add_portal_menu');

// Liens pages légales
hooks()->add_action('app_admin_footer', 'dietetic_add_legal_links_to_header');
hooks()->add_action('app_customers_portal_footer', 'dietetic_add_legal_links_to_header');

// Cron notifications
hooks()->add_action('after_cron_run', 'dietetic_send_scheduled_reminders');
```

---

## 🌐 API et Endpoints

### Endpoints publics (portail patient)

| Endpoint | Méthode | Description |
|----------|---------|-------------|
| `/dietetic/portal` | GET | Dashboard patient |
| `/dietetic/portal/meal_plans` | GET | Plans de repas |
| `/dietetic/portal/measurements` | GET | Historique mesures |
| `/dietetic/portal/add_measurement` | GET/POST | Ajouter mesure |
| `/dietetic/portal/consultations` | GET | Mes consultations |
| `/dietetic/portal/recipes` | GET | Bibliothèque recettes |
| `/dietetic/portal/recipe_view/{id}` | GET | Détails recette |
| `/dietetic/portal/food_surveys` | GET | Enquêtes alimentaires |
| `/dietetic/portal/notification_preferences` | GET/POST | Préférences notifs |
| `/dietetic/portal/privacy` | GET | Politique confidentialité |
| `/dietetic/portal/terms` | GET | CGU |

### Endpoints AJAX (notifications)

| Endpoint | Méthode | Description |
|----------|---------|-------------|
| `/dietetic/portal/save_fcm_token` | POST | Enregistrer token FCM |
| `/dietetic/portal/delete_fcm_token` | POST | Supprimer token |
| `/dietetic/portal/get_notifications` | GET | Récupérer notifications |
| `/dietetic/portal/mark_notification_read` | POST | Marquer comme lu |
| `/dietetic/portal/delete_notification` | POST | Supprimer notification |
| `/dietetic/portal/mark_all_notifications_read` | POST | Tout marquer comme lu |

### Endpoints admin

| Endpoint | Méthode | Description |
|----------|---------|-------------|
| `/admin/dietetic/dashboard` | GET | Dashboard admin |
| `/admin/dietetic/patients` | GET | Liste patients |
| `/admin/dietetic/recipes` | GET | Gestion recettes |
| `/admin/dietetic/foods` | GET | Base alimentaire |
| `/admin/dietetic/notifications` | GET | Config notifications |
| `/admin/dietetic/legal_pages/manage` | GET/POST | Gérer pages légales |
| `/admin/dietetic/staff_permissions` | GET | Permissions staff |

---

## 🐛 Troubleshooting

### Problème: Notifications push ne fonctionnent pas

**Vérifications**:
1. ✅ Clé serveur FCM configurée dans paramètres
2. ✅ Patient a donné permission notifications navigateur
3. ✅ Token FCM enregistré dans base de données
4. ✅ Préférences patient activées pour ce type de notif
5. ✅ Service worker correctement chargé

**Logs à vérifier**:
```sql
SELECT * FROM tbldietic_patient_fcm_tokens WHERE patient_id = X;
SELECT * FROM tbldietic_notification_preferences WHERE patient_id = X;
```

### Problème: Permissions recettes ne s'affichent pas

**Solution**:
1. Vider cache navigateur (Ctrl + F5)
2. Se déconnecter et reconnecter
3. Vérifier Setup → Roles → Permissions Dietetic
4. Les permissions sont ajoutées automatiquement si module activé

### Problème: Pages légales affichent erreur 500

**Cause**: Vue incorrecte utilisée

**Solution**: Vérifier que `portal_legal_page.php` existe à la racine de `/modules/dietetic/views/`

### Problème: Redirection patients ne fonctionne pas

**Note**: Cette fonctionnalité est actuellement **désactivée** volontairement

**Pour réactiver**: Décommenter les hooks dans `dietetic.php`

---

## 📝 Notes pour futures sessions

### Fonctionnalités à implémenter

- [ ] Application mobile WebView APK
- [ ] Redirection automatique patients (actuellement désactivée)
- [ ] Export PDF plans de repas amélioré
- [ ] Statistiques avancées dashboard
- [ ] Intégration paiement en ligne

### Bugs connus

- Aucun bug critique connu à ce jour

### Améliorations suggérées

- Cache pour améliorer performances
- Compression images dans enquêtes alimentaires
- Mode hors ligne pour application mobile
- Graphiques interactifs avec Chart.js
- Export Excel pour rapports

---

## 📞 Support

Pour toute question sur ce module, référez-vous à:
- Ce fichier de documentation
- Les commentaires dans le code source
- Les fichiers README spécifiques (PERMISSIONS_GUIDE.md, etc.)

**Développeur**: Claude AI Assistant
**Date création documentation**: 22 novembre 2025
**Dernière session**: continuation-01C5JP5sZ8UnS9SyfgeW9iys

---

*Fin de la documentation*
