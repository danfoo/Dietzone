# Système Multi-Diététiciens et Permissions

## Vue d'ensemble

Ce système permet à un patient d'être suivi par **plusieurs diététiciens** simultanément, avec un contrôle des permissions pour que chaque diététicien ne voit que ses propres patients.

## Fonctionnalités

### 1. Relation Many-to-Many Patient-Diététicien

- **Avant** : Un patient → Un seul diététicien (`dietitian_id` dans `dietic_patients`)
- **Maintenant** : Un patient → Plusieurs diététiciens (table `dietic_patient_dietitians`)

### 2. Permissions par Diététicien

- **Admins** : Voient tous les patients et peuvent gérer les assignations
- **Diététiciens** : Ne voient QUE les patients qui leur sont assignés
- **Patients** : Peuvent avoir plusieurs diététiciens (notation multiple possible)

### 3. Diététicien Principal

- Chaque patient peut avoir un **diététicien principal** (`is_primary = 1`)
- Utilisé pour les notifications et comme contact principal
- Un seul diététicien principal par patient

## Installation

### Étape 1 : Installer le système multi-diététiciens

Accédez à : `https://app.dietsenegal.net/admin/dietetic/dietitians/install_assignments`

Cette installation va :
- ✅ Créer la table `dietic_patient_dietitians`
- ✅ Ajouter les clés étrangères
- ✅ **Migrer automatiquement** les assignations existantes depuis `dietic_patients.dietitian_id`

### Étape 2 : Vérifier la migration

Après l'installation, vérifiez que :
- Tous les patients avec un `dietitian_id` ont une assignation dans la nouvelle table
- Le diététicien existant est marqué comme `is_primary = 1`

## Structure de la Table

### `dietic_patient_dietitians`

| Colonne | Type | Description |
|---------|------|-------------|
| `id` | int(11) | ID unique de l'assignation |
| `patient_id` | int(11) | Référence au patient |
| `dietitian_id` | int(11) | Référence au diététicien (staff) |
| `is_primary` | tinyint(1) | 1 = Diététicien principal |
| `assigned_date` | datetime | Date d'assignation |
| `assigned_by` | int(11) | Staff qui a fait l'assignation |
| `notes` | text | Notes sur cette assignation |
| `status` | varchar(20) | active, inactive |
| `created_at` | datetime | Date de création |
| `updated_at` | datetime | Date de mise à jour |

## Modèle : `Dietetic_patient_dietitians_model`

### Méthodes principales

```php
// Obtenir tous les diététiciens d'un patient
$dietitians = $this->dietetic_patient_dietitians_model->get_patient_dietitians($patient_id);

// Obtenir tous les patients d'un diététicien
$patients = $this->dietetic_patient_dietitians_model->get_dietitian_patients($dietitian_id);

// Vérifier si un diététicien a accès à un patient
$has_access = $this->dietetic_patient_dietitians_model->has_access($patient_id, $dietitian_id);

// Assigner un diététicien à un patient
$assignment_id = $this->dietetic_patient_dietitians_model->assign([
    'patient_id' => $patient_id,
    'dietitian_id' => $dietitian_id,
    'is_primary' => 1,
    'notes' => 'Diététicien principal'
]);

// Définir un diététicien comme principal
$this->dietetic_patient_dietitians_model->set_primary($patient_id, $dietitian_id);

// Retirer un diététicien (soft delete)
$this->dietetic_patient_dietitians_model->remove($patient_id, $dietitian_id, false);
```

## Fonctions Helper

### Vérification des permissions

```php
// Vérifier si l'utilisateur actuel est admin
if (dietetic_is_admin()) {
    // Voir tous les patients
}

// Vérifier si un diététicien peut accéder à un patient
if (dietetic_can_access_patient($patient_id)) {
    // Afficher les détails du patient
}

// Obtenir les IDs des patients accessibles
$patient_ids = dietetic_get_accessible_patient_ids();
// Retourne null pour admins (= tous)
// Retourne array d'IDs pour diététiciens

// Vérifier si l'utilisateur peut gérer les assignations
if (dietetic_can_manage_assignments()) {
    // Afficher les boutons d'assignation
}
```

### Filtrage des requêtes

```php
// Appliquer automatiquement le filtre diététicien
$this->db->select('p.*');
$this->db->from(db_prefix() . 'dietic_patients p');
dietetic_apply_dietitian_filter($this->db, 'pd'); // Filtre si non-admin
$patients = $this->db->get()->result();
```

## Utilisation dans les Contrôleurs

### Exemple : Filtrer les patients

```php
public function index()
{
    // Cette méthode filtre automatiquement selon les permissions
    $data['patients'] = $this->dietetic_patients_model->get_all();
    // Admins : tous les patients
    // Diététiciens : seulement leurs patients assignés
}
```

### Exemple : Vérifier l'accès à un patient

```php
public function view($patient_id)
{
    // Vérifier l'accès
    if (!dietetic_can_access_patient($patient_id)) {
        access_denied('Dietetic - Patient Access');
    }

    // Charger les données du patient
    $data['patient'] = $this->dietetic_patients_model->get($patient_id);
}
```

## Impact sur le Système de Notation

### Avant (1 diététicien par patient)

- Un patient ne pouvait noter qu'un seul diététicien
- La colonne `dietitian_id` dans `dietic_patients` définissait le diététicien

### Maintenant (plusieurs diététiciens par patient)

- Un patient peut noter **chaque diététicien** qui le suit
- La table `dietic_ratings` reste identique (patient_id + dietitian_id)
- Un patient peut avoir plusieurs notes (une par diététicien assigné)

### Mise à jour de la vue patient

```php
// Afficher tous les diététiciens du patient avec option de notation
$dietitians = $this->dietetic_patient_dietitians_model->get_patient_dietitians($patient->id);

foreach ($dietitians as $dietitian) {
    // Vérifier si le patient a déjà noté ce diététicien
    $existing_rating = $this->dietetic_ratings_model->get_by_patient_dietitian(
        $patient->id,
        $dietitian->dietitian_id
    );

    // Afficher bouton "Noter" ou "Modifier la note"
}
```

## Migration Automatique

L'installation migrate automatiquement les données :

```sql
INSERT INTO `dietic_patient_dietitians` (patient_id, dietitian_id, is_primary, assigned_date, created_at)
SELECT
    id as patient_id,
    dietitian_id,
    1 as is_primary,
    created_at as assigned_date,
    created_at
FROM `dietic_patients`
WHERE dietitian_id IS NOT NULL
ON DUPLICATE KEY UPDATE is_primary = 1;
```

## Compatibilité

### Colonne `dietitian_id` dans `dietic_patients`

- ✅ **Conservée** pour compatibilité ascendante
- ✅ Peut servir de référence au "diététicien principal"
- ✅ Les anciennes requêtes fonctionnent toujours
- ⚠️ Recommandé d'utiliser la nouvelle table pour les nouvelles fonctionnalités

### Anciennes requêtes

```php
// Ancienne méthode (toujours fonctionnelle)
$dietitian_id = $patient->dietitian_id;

// Nouvelle méthode (recommandée)
$primary_dietitian = $this->dietetic_patient_dietitians_model->get_primary_dietitian($patient->id);
$dietitian_id = $primary_dietitian ? $primary_dietitian->dietitian_id : null;
```

## Prochaines Étapes

### À Implémenter

1. **Interface d'assignation dans la vue patient**
   - Bouton "Assigner un diététicien" (admins uniquement)
   - Liste des diététiciens assignés
   - Définir le diététicien principal

2. **Mise à jour des modèles**
   - `Dietetic_patients_model` : Utiliser la nouvelle table pour filtrage
   - `Dietetic_programs_model` : Filtrer par assignations
   - `Dietetic_consultations_model` : Filtrer par assignations

3. **Vue portal patient**
   - Afficher tous les diététiciens assignés
   - Permettre de noter chaque diététicien
   - Voir les coordonnées de tous les diététiciens

4. **Dashboard diététicien**
   - Statistiques sur ses patients assignés uniquement
   - Liste filtrée des programmes
   - Liste filtrée des consultations

## Sécurité

### Règles de permissions

1. **Admins (`is_admin()` = true)**
   - Voient TOUS les patients
   - Peuvent assigner/retirer des diététiciens
   - Peuvent voir toutes les statistiques

2. **Diététiciens (staff non-admin)**
   - Voient UNIQUEMENT leurs patients assignés (`status = 'active'`)
   - Ne peuvent PAS modifier les assignations
   - Ne voient que leurs propres statistiques

3. **Patients (portal)**
   - Voient TOUS leurs diététiciens assignés
   - Peuvent noter chaque diététicien
   - Ne peuvent PAS modifier les assignations

### Vérifications de sécurité

Toujours vérifier l'accès dans les contrôleurs :

```php
// MAUVAIS - Pas de vérification
$patient = $this->dietetic_patients_model->get($patient_id);

// BON - Vérification d'accès
if (!dietetic_can_access_patient($patient_id)) {
    access_denied('Dietetic - Patient Access');
}
$patient = $this->dietetic_patients_model->get($patient_id);
```

## Support

Pour toute question ou problème, contactez l'équipe de développement.

---

**Version** : 1.0
**Date** : 2025-01-05
**Module** : Dietetic CRM
