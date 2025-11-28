# Guide Technique : Système de Suivi des Activités Sportives

## Vue d'Ensemble

Le système de suivi des activités permet aux patients de :
- Enregistrer leurs activités physiques quotidiennes
- Calculer automatiquement les calories brûlées
- Visualiser leurs statistiques d'exercice
- Suivre leur progression dans l'espace admin

## Architecture

### Structure des Fichiers

```
modules/dietetic/
├── controllers/
│   ├── Portal.php              # Endpoints API patient
│   └── Patients.php            # Intégration admin
├── views/
│   ├── portal_dashboard.php    # Widget activités (dashboard patient)
│   ├── portal/
│   │   └── activities.php      # Page complète activités
│   └── admin/
│       └── patients/
│           └── view.php        # Tableau de suivi quotidien
└── models/
    └── dietetic_activities_model.php
```

### Base de Données

#### Tables Principales

**1. `tbldietic_activities` (Table de référence)**
```sql
CREATE TABLE tbldietic_activities (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    category VARCHAR(100),
    kcal_per_minute DECIMAL(5,2),
    created_at DATETIME
);
```

**Catégories disponibles:**
- Cardio (course, vélo, natation, etc.)
- Musculation (poids, résistance, etc.)
- Sports collectifs (football, basketball, etc.)
- Yoga/Étirements
- Activités quotidiennes

**2. `tbldietic_patient_activities` (Données patient)**
```sql
CREATE TABLE tbldietic_patient_activities (
    id INT PRIMARY KEY AUTO_INCREMENT,
    patient_id INT NOT NULL,
    activity_id INT NOT NULL,
    duration_minutes INT NOT NULL,
    kcal_burned DECIMAL(8,2) NOT NULL,
    activity_date DATE NOT NULL,
    activity_time TIME NULL,
    notes TEXT NULL,
    created_at DATETIME,
    FOREIGN KEY (patient_id) REFERENCES tbldietic_patients(id),
    FOREIGN KEY (activity_id) REFERENCES tbldietic_activities(id),
    INDEX idx_patient_date (patient_id, activity_date)
);
```

**Index importants:**
- `idx_patient_date` : Optimise les requêtes par patient et date
- Clé primaire `id` : Recherche rapide pour suppression

## Endpoints API

### Patient Portal (`/dietetic/portal/`)

#### 1. Liste des Activités Disponibles

**Route:** `GET /dietetic/portal/get_activities`

**Authentification:** Requiert session patient active

**Réponse:**
```json
{
    "success": true,
    "activities": [
        {
            "id": 1,
            "name": "Course à pied",
            "category": "Cardio",
            "kcal_per_minute": 10.5
        },
        // ...
    ]
}
```

**Utilisation:**
```javascript
$.ajax({
    url: site_url + 'dietetic/portal/get_activities',
    type: 'GET',
    success: function(response) {
        // Peupler le dropdown
    }
});
```

---

#### 2. Mes Activités

**Route:** `GET /dietetic/portal/get_my_activities`

**Authentification:** Requiert session patient active

**Réponse:**
```json
{
    "success": true,
    "activities": [
        {
            "id": 123,
            "activity_name": "Course à pied",
            "category": "Cardio",
            "duration_minutes": 30,
            "kcal_burned": 315.00,
            "activity_date": "2025-11-27",
            "activity_time": "08:30:00",
            "notes": "Séance matinale"
        }
    ],
    "stats": {
        "total_activities": 15,
        "total_minutes": 450,
        "total_kcal": 4725.50
    }
}
```

---

#### 3. Ajouter une Activité

**Route:** `POST /dietetic/portal/add_activity`

**Authentification:** Requiert session patient active + CSRF token

**Paramètres:**
```javascript
{
    "activity_id": 5,                    // Required
    "duration_minutes": 30,              // Required
    "kcal_burned": 315.00,              // Required (calculé côté client)
    "activity_date": "2025-11-27",      // Required (format: Y-m-d)
    "activity_time": "08:30",           // Optional (format: H:i)
    "notes": "Séance matinale",         // Optional
    "redirect_to_dashboard": false,     // Optional (true pour redirect au lieu de JSON)
    "csrf_token_name": "csrf_hash_value" // Required (nom dynamique)
}
```

**Réponse Succès:**
```json
{
    "success": true,
    "message": "Activité ajoutée avec succès",
    "csrf_token": "new_csrf_hash_value"
}
```

**Réponse Erreur:**
```json
{
    "success": false,
    "message": "Description de l'erreur",
    "csrf_token": "new_csrf_hash_value"
}
```

**Exemple d'utilisation:**
```javascript
let formData = {
    activity_id: 5,
    duration_minutes: 30,
    kcal_burned: 315,
    activity_date: '2025-11-27'
};
formData[csrfTokenName] = $('#csrf_token_field').val();

$.ajax({
    url: site_url + 'dietetic/portal/add_activity',
    type: 'POST',
    data: formData,
    success: function(response) {
        if (response.success) {
            showNotification('Activité enregistrée !', 'success');
            $('#csrf_token_field').val(response.csrf_token);
            loadMyActivities(); // Refresh list
        }
    }
});
```

---

#### 4. Supprimer une Activité

**Route:** `POST /dietetic/portal/delete_activity/{id}`

**Authentification:** Requiert session patient active + CSRF token

**Sécurité:**
- Vérifie que l'activité appartient au patient connecté
- Refuse la suppression si `activity.patient_id !== current_patient.id`

**Paramètres:**
```javascript
{
    "csrf_token_name": "csrf_hash_value" // Required (nom dynamique)
}
```

**Réponse Succès:**
```json
{
    "success": true,
    "message": "Activité supprimée avec succès",
    "csrf_token": "new_csrf_hash_value"
}
```

**Exemple d'utilisation:**
```javascript
function deleteActivity(id) {
    if (!confirm('Voulez-vous vraiment supprimer cette activité ?')) {
        return;
    }

    let deleteData = {};
    deleteData[csrfTokenName] = $('#csrf_token_field').val();

    $.ajax({
        url: site_url + 'dietetic/portal/delete_activity/' + id,
        type: 'POST',
        data: deleteData,
        success: function(response) {
            if (response.success) {
                showNotification('Activité supprimée', 'success');
                $('#csrf_token_field').val(response.csrf_token);
                loadMyActivities(); // Refresh list
            }
        }
    });
}
```

---

#### 5. Activités du Jour (Widget Dashboard)

**Route:** `GET /dietetic/portal/api_get_today_activities`

**Authentification:** Requiert session patient active

**Description:** Version légère pour le widget dashboard (uniquement aujourd'hui)

**Réponse:**
```json
{
    "success": true,
    "activities": [
        {
            "id": 123,
            "activity_name": "Course à pied",
            "duration_minutes": 30,
            "kcal_burned": 315.00,
            "activity_time": "08:30:00"
        }
    ],
    "stats": {
        "count": 2,
        "total_minutes": 45,
        "total_kcal": 475.50
    }
}
```

## Gestion CSRF

### Problématique

CodeIgniter régénère le token CSRF à chaque requête POST pour sécurité maximale.

### Solution Implémentée

1. **Nom de token dynamique** (pas hardcodé)
   ```php
   // Dans la vue
   <input type="hidden"
          name="<?php echo $this->security->get_csrf_token_name(); ?>"
          value="<?php echo $this->security->get_csrf_hash(); ?>"
          id="csrf_token_field">
   ```

2. **JavaScript : Récupération du nom**
   ```javascript
   const csrfTokenName = '<?php echo $this->security->get_csrf_token_name(); ?>';
   ```

3. **Envoi dynamique dans AJAX**
   ```javascript
   let postData = {
       // ... autres données
   };
   postData[csrfTokenName] = $('#csrf_token_field').val();

   $.ajax({
       data: postData,
       // ...
   });
   ```

4. **Backend : Retour du nouveau token**
   ```php
   echo json_encode([
       'success' => true,
       'csrf_token' => $this->security->get_csrf_hash() // Nouveau token
   ]);
   ```

5. **Frontend : Mise à jour du champ**
   ```javascript
   if (response.csrf_token) {
       $('#csrf_token_field').val(response.csrf_token);
   }
   ```

### ⚠️ Erreur Commune à Éviter

**NE PAS faire de validation manuelle du CSRF :**
```php
// ❌ INCORRECT - Cause des faux positifs
if ($csrf_token !== $this->security->get_csrf_hash()) {
    // Erreur : le hash change à chaque appel de get_csrf_hash()
}

// ✅ CORRECT - Laisser CI valider automatiquement
// Pas de validation manuelle nécessaire
// Si la méthode est atteinte, le token est valide
```

## Calculs des Calories

### Formule

```
Calories brûlées = kcal_per_minute × duration_minutes
```

### Implémentation JavaScript

```javascript
function calculateKcal() {
    let selectedOption = $('#activitySelect option:selected');
    let kcalPerMin = parseFloat(selectedOption.data('kcal')) || 0;
    let duration = parseInt($('#durationInput').val()) || 0;

    selectedActivityKcalPerMin = kcalPerMin;
    let totalKcal = Math.round(kcalPerMin * duration);

    $('#kcalValue').text(totalKcal);
}

// Écoute des changements
$('#activitySelect, #durationInput').on('change input', calculateKcal);
```

### Exemple

**Activité :** Course à pied (10.5 kcal/min)
**Durée :** 30 minutes
**Résultat :** 10.5 × 30 = **315 kcal**

## Composant Dropdown Personnalisé

### Pourquoi ?

Le select natif ne permet pas :
- Recherche en temps réel
- Affichage des calories par minute
- Style personnalisé cohérent cross-browser

### Structure HTML

```html
<div class="custom-select-wrapper">
    <div class="custom-select-trigger" id="customSelectTrigger">
        <span id="selectedActivityText">Rechercher et sélectionner...</span>
        <i class="fa fa-chevron-down"></i>
    </div>
    <div class="custom-select-dropdown" id="customSelectDropdown">
        <div class="custom-select-search">
            <i class="fa fa-search"></i>
            <input type="text" id="activitySearchInput" placeholder="Rechercher...">
        </div>
        <div class="custom-select-options" id="customSelectOptions">
            <!-- Options générées dynamiquement -->
        </div>
    </div>
</div>
```

### JavaScript

```javascript
// Générer les options
function populateCustomDropdown(activities) {
    let optionsHTML = '';

    // Grouper par catégorie
    let categories = {};
    activities.forEach(activity => {
        if (!categories[activity.category]) {
            categories[activity.category] = [];
        }
        categories[activity.category].push(activity);
    });

    // Créer les options groupées
    Object.keys(categories).forEach(category => {
        optionsHTML += '<div class="custom-select-category">' + category + '</div>';
        categories[category].forEach(activity => {
            optionsHTML += `
                <div class="custom-select-option"
                     data-value="${activity.id}"
                     data-kcal="${activity.kcal_per_minute}">
                    <span>${activity.name}</span>
                    <span class="custom-select-option-kcal">
                        ${activity.kcal_per_minute} kcal/min
                    </span>
                </div>`;
        });
    });

    $('#customSelectOptions').html(optionsHTML);
}

// Recherche en temps réel
$('#activitySearchInput').on('input', function() {
    let searchTerm = $(this).val().toLowerCase();

    $('.custom-select-option').each(function() {
        let text = $(this).text().toLowerCase();
        $(this).toggle(text.includes(searchTerm));
    });
});

// Sélection
$(document).on('click', '.custom-select-option', function() {
    let value = $(this).data('value');
    let text = $(this).find('span:first').text();
    let kcal = $(this).data('kcal');

    $('#selectedActivityText').text(text);
    $('#activitySelect').val(value).trigger('change');
    $('#customSelectDropdown').slideUp(200);
});
```

## Système de Notifications

### Problème

La fonction `alert_float()` de Perfex CRM n'est disponible que dans l'admin, pas dans le portail patient.

### Solution : showNotification()

```javascript
function showNotification(message, type) {
    // type: 'success' ou 'danger'
    const bgColor = type === 'success' ? '#48bb78' : '#e74c3c';
    const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';

    const toast = $('<div>')
        .css({
            position: 'fixed',
            top: '20px',
            right: '20px',
            background: bgColor,
            color: 'white',
            padding: '14px 20px',
            borderRadius: '10px',
            zIndex: 10000,
            boxShadow: '0 4px 16px rgba(0,0,0,0.25)',
            fontSize: '14px',
            fontWeight: '600',
            display: 'flex',
            alignItems: 'center',
            gap: '10px',
            minWidth: '250px',
            maxWidth: '400px'
        })
        .html('<i class="fa ' + icon + '"></i> ' + message)
        .appendTo('body');

    setTimeout(() => toast.fadeOut(300, () => toast.remove()), 3500);
}
```

### Utilisation

```javascript
// Succès
showNotification('Activité enregistrée !', 'success');

// Erreur
showNotification('Erreur lors de l\'ajout', 'danger');
```

## Intégration Admin

### Fichier : `controllers/Patients.php`

#### Méthode : `view($id)`

Ajout du chargement des activités pour chaque jour de suivi :

```php
// Add sports activities data to each tracking day
if ($this->db->table_exists(db_prefix() . 'dietic_patient_activities')) {
    $this->load->model('dietetic/dietetic_activities_model');

    foreach ($data['daily_tracking'] as &$day) {
        $activities = $this->dietetic_activities_model->get_patient_activities_by_date_range(
            $id,
            $day->tracking_date,
            $day->tracking_date
        );

        $total_minutes = 0;
        $total_kcal = 0;

        if (!empty($activities)) {
            foreach ($activities as $activity) {
                $total_minutes += $activity->duration_minutes;
                $total_kcal += $activity->kcal_burned;
            }
        }

        $day->activities_minutes = $total_minutes;
        $day->activities_kcal = $total_kcal;
        $day->activities_count = count($activities);
    }
}
```

### Fichier : `views/admin/patients/view.php`

#### Colonne Activité

```php
<td class="text-center">
    <?php if (isset($day->activities_minutes) && $day->activities_minutes > 0) { ?>
        <span class="badge" style="background: #fa709a;">
            <?php echo $day->activities_minutes; ?> min
            <?php if ($day->activities_count > 1) { ?>
                <i class="fa fa-list" title="<?php echo $day->activities_count; ?> activités"></i>
            <?php } ?>
        </span>
    <?php } else { ?>
        <span class="text-muted">-</span>
    <?php } ?>
</td>
```

#### Calcul de Complétion

```php
$completion = 0;

// 1. Repas (25%)
if ($day->meals_logged >= 3) $completion += 25;

// 2. Eau (25%) - Basé sur 2000ml
if (isset($day->hydration_ml) && $day->hydration_ml >= 2000) {
    $completion += 25;
} else if (isset($day->hydration_ml) && $day->hydration_ml > 0) {
    $completion += round(($day->hydration_ml / 2000) * 25);
}

// 3. Activité (25%) - Basé sur 30 minutes
$activity_mins = isset($day->activities_minutes) ? $day->activities_minutes : 0;
if ($activity_mins >= 30) {
    $completion += 25;
} else if ($activity_mins > 0) {
    $completion += round(($activity_mins / 30) * 25);
}

// 4. Calories brûlées (25%)
$calories = isset($day->activities_kcal) && $day->activities_kcal > 0
    ? $day->activities_kcal
    : 0;
if ($calories > 0) $completion += 25;
```

## Responsive Design

### Breakpoints

```css
/* Mobile (< 768px) */
@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr); /* 2 colonnes */
        gap: 10px;
    }

    .activities-page {
        padding: 12px;
    }
}

/* Tablet/Desktop (>= 769px) */
@media (min-width: 769px) {
    .stats-grid {
        grid-template-columns: repeat(3, 1fr); /* 3 colonnes */
        gap: 16px;
    }

    .activities-page {
        max-width: 900px;
        padding: 24px;
    }
}

/* Large Desktop (>= 1024px) */
@media (min-width: 1024px) {
    .activities-page {
        max-width: 1100px;
    }
}
```

### Mobile-First Principles

1. **Fonts compactes** : 9-13px (vs 14-16px desktop)
2. **Boutons tactiles** : 36x36px minimum
3. **Espacement réduit** : 12-16px (vs 20-24px desktop)
4. **Grid flexible** : 2 colonnes mobile, 3 desktop

## Dépannage

### Problème : "Token CSRF invalide"

**Cause possible :** Validation manuelle du token dans le backend

**Solution :**
```php
// Supprimer cette validation :
if ($csrf_token !== $this->security->get_csrf_hash()) {
    // ...
}

// CodeIgniter valide automatiquement
```

---

### Problème : L'historique ne se rafraîchit pas

**Cause possible :** Token CSRF non retourné dans la réponse

**Solution :**
```php
// Toujours inclure le nouveau token
echo json_encode([
    'success' => true,
    'csrf_token' => $this->security->get_csrf_hash()
]);
```

---

### Problème : "alert_float is not defined"

**Cause :** Fonction admin utilisée dans le portail patient

**Solution :** Utiliser `showNotification()` à la place

---

### Problème : Calories incorrectes

**Vérifications :**
1. Vérifier `kcal_per_minute` dans `tbldietic_activities`
2. Vérifier que `data-kcal` est bien passé au dropdown
3. Vérifier le calcul JavaScript : `kcalPerMin × duration`

---

### Problème : Activités d'un autre patient visibles

**Cause :** Pas de filtrage par patient_id

**Solution :**
```php
// Dans le modèle
$this->db->where('patient_id', $patient_id);
$activities = $this->db->get('dietic_patient_activities')->result();
```

## Performance

### Optimisations Recommandées

1. **Index Base de Données**
   ```sql
   CREATE INDEX idx_patient_date
   ON tbldietic_patient_activities(patient_id, activity_date);
   ```

2. **Pagination** (si > 100 activités)
   ```php
   $this->db->limit(20, $offset);
   ```

3. **Cache des activités de référence**
   ```php
   // Cache 1 heure (activités rarement modifiées)
   $this->db->cache_on();
   $activities = $this->db->get('dietic_activities')->result();
   $this->db->cache_off();
   ```

## Tests

### Test Unitaire : Calcul Calories

```javascript
// Test calculateKcal()
$('#activitySelect').val(5).data('kcal', 10.5);
$('#durationInput').val(30);
calculateKcal();

console.assert($('#kcalValue').text() === '315', 'Calcul incorrect');
```

### Test Intégration : Ajout Activité

```javascript
// 1. Charger la page
// 2. Sélectionner activité
// 3. Entrer durée
// 4. Soumettre formulaire
// 5. Vérifier notification succès
// 6. Vérifier activité dans historique
```

### Test Sécurité : Suppression Activité

```bash
# Tenter de supprimer l'activité d'un autre patient
curl -X POST https://app.dietsenegal.net/dietetic/portal/delete_activity/999 \
     -H "Cookie: ..." \
     -d "csrf_token=..."

# Résultat attendu : {"success": false, "message": "Activité non trouvée ou accès refusé"}
```

## Maintenance

### Ajouter une Nouvelle Activité

```sql
INSERT INTO tbldietic_activities (name, category, kcal_per_minute, created_at)
VALUES ('Escalade', 'Sports collectifs', 8.5, NOW());
```

### Modifier les Calories d'une Activité

```sql
UPDATE tbldietic_activities
SET kcal_per_minute = 11.0
WHERE name = 'Course à pied';
```

### Nettoyer les Anciennes Activités

```sql
-- Supprimer activités > 2 ans
DELETE FROM tbldietic_patient_activities
WHERE activity_date < DATE_SUB(NOW(), INTERVAL 2 YEAR);
```

## Changelog

- **2025-11-27** : Version initiale
  - Système complet ajout/suppression/affichage
  - CSRF tokens dynamiques
  - Notifications personnalisées
  - Intégration admin

---

**Auteur :** Équipe Développement Dietzone
**Version :** 1.0.0
**Dernière MAJ :** 27 Novembre 2025
