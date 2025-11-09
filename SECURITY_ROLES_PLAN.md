# 🔒 Plan d'amélioration - Gestion des Rôles et Permissions

## 📋 Analyse de l'existant

### ✅ Ce qui existe déjà

**Helper functions (dietetic_helper.php)** :
- `dietetic_has_permission()` - Vérification permission globale
- `dietetic_is_admin()` - Check si admin
- `dietetic_get_staff_user_id()` - ID du staff actuel
- `dietetic_can_access_patient()` - Vérifie accès à un patient
- `dietetic_apply_dietitian_filter()` - Filtre SQL par diététicien
- `dietetic_get_accessible_patient_ids()` - Liste des patients accessibles
- `dietetic_can_manage_assignments()` - Gestion des affectations

### ❌ Ce qui manque

1. **Application incomplète des filtres**
   - Contrôleurs ne filtrent pas toujours par diététicien
   - Modèles retournent tous les enregistrements sans filtre
   - Pas de vérification d'accès avant les actions

2. **Permissions granulaires manquantes**
   - Pas de distinction entre "voir ses propres" vs "voir tous"
   - Pas de permission spécifique pour programmes/consultations/enquêtes

3. **Sécurité**
   - Actions (edit/delete) pas toujours vérifiées
   - Accès directs par ID non contrôlés
   - Pas de logs d'accès non autorisés

---

## 🎯 Objectifs

### 1. Rôles définis

| Rôle | Permissions |
|------|-------------|
| **Admin** | Tout voir, tout faire |
| **Diététicien** | Voir/gérer UNIQUEMENT ses patients/programmes/consultations/enquêtes |
| **Patient** (Portal) | Voir ses propres données uniquement |

### 2. Règles métier

**Diététicien NON admin** :
- ✅ Voir uniquement les patients qui lui sont assignés
- ✅ Créer des programmes/consultations pour ses patients seulement
- ✅ Voir les enquêtes de ses patients uniquement
- ❌ Ne PEUT PAS voir/modifier les patients d'un autre diététicien
- ❌ Ne PEUT PAS assigner/retirer des diététiciens (admin only)

**Admin** :
- ✅ Voir TOUS les patients, programmes, consultations, enquêtes
- ✅ Assigner/retirer des diététiciens
- ✅ Gérer les paramètres globaux
- ✅ Accéder aux migrations et configurations

---

## 🔧 Plan d'implémentation

### Phase 1 : Sécurisation des Modèles

**Fichiers à modifier** :
- `dietetic_patients_model.php`
- `dietetic_programs_model.php`
- `dietetic_consultations_model.php`
- `dietetic_food_surveys_model.php`
- `dietetic_measurements_model.php`

**Modifications** :
- Ajouter filtre par diététicien dans get_all()
- Ajouter vérification d'accès dans get($id)
- Filtrer les résultats dans toutes les méthodes de liste

### Phase 2 : Sécurisation des Contrôleurs

**Fichiers à modifier** :
- `Patients.php`
- `Programs.php`
- `Consultations.php`
- `Food_surveys.php`
- `Measurements.php`

**Modifications** :
- Vérifier l'accès avant view/edit/delete
- Filtrer les listes par accessible_patient_ids
- Bloquer les actions non autorisées
- Logger les tentatives d'accès refusées

### Phase 3 : Middleware de sécurité

**Nouveau fichier** : `Dietetic_security.php` (library)
- Centraliser les vérifications d'accès
- Méthodes réutilisables pour tous les contrôleurs
- Logging automatique des tentatives

### Phase 4 : Améliorations UI

**Modifications vues** :
- Cacher les boutons d'actions non autorisées
- Afficher message si liste vide (pas de patients assignés)
- Indicateur visuel "Mes patients" vs "Tous les patients" (admin)

### Phase 5 : Tests

**Scénarios à tester** :
1. Diététicien A ne voit que ses patients
2. Diététicien A ne peut pas accéder à patient de Diététicien B
3. Admin voit tout
4. Tentative d'accès direct par URL bloquée
5. Logs des accès refusés

---

## 📁 Structure des modifications

```
modules/dietetic/
├── helpers/
│   └── dietetic_helper.php          [✅ Existe, à compléter]
├── libraries/
│   └── Dietetic_security.php         [🆕 À créer]
├── models/
│   ├── Dietetic_patients_model.php   [🔧 À sécuriser]
│   ├── Dietetic_programs_model.php   [🔧 À sécuriser]
│   ├── Dietetic_consultations_model.php [🔧 À sécuriser]
│   ├── Dietetic_food_surveys_model.php  [🔧 À sécuriser]
│   └── Dietetic_measurements_model.php  [🔧 À sécuriser]
├── controllers/
│   ├── Patients.php                  [🔧 À sécuriser]
│   ├── Programs.php                  [🔧 À sécuriser]
│   ├── Consultations.php             [🔧 À sécuriser]
│   ├── Food_surveys.php              [🔧 À sécuriser]
│   └── Measurements.php              [🔧 À sécuriser]
└── views/
    └── admin/                        [🔧 Adaptations UI]
```

---

## 🚀 Implémentation prioritaire

### Ordre d'exécution

1. ✅ Créer `Dietetic_security.php` (bibliothèque centralisée)
2. ✅ Mettre à jour `dietetic_helper.php` (fonctions manquantes)
3. ✅ Sécuriser modèles (filtres SQL)
4. ✅ Sécuriser contrôleurs (vérifications d'accès)
5. ✅ Adapter les vues (UI/UX)
6. ✅ Tests et validation

### Estimation

- **Temps total** : 2-3 heures
- **Complexité** : Moyenne
- **Impact** : Critique (sécurité)

---

## 📝 Checklist de validation

**Pour chaque entité (Patient/Programme/Consultation/Enquête)** :

**Modèle** :
- [ ] `get_all()` filtre par diététicien si non-admin
- [ ] `get($id)` vérifie l'accès avant de retourner
- [ ] Méthodes de liste filtrent par accessible_patient_ids
- [ ] Aucune requête ne retourne de données non autorisées

**Contrôleur** :
- [ ] `index()` applique le filtre de diététicien
- [ ] `view($id)` vérifie l'accès au patient
- [ ] `create()` assigne automatiquement au diététicien actuel
- [ ] `edit($id)` vérifie propriété avant modification
- [ ] `delete($id)` vérifie propriété avant suppression
- [ ] Actions AJAX vérifiées également

**Vue** :
- [ ] Boutons conditionnels selon permissions
- [ ] Messages appropriés si liste vide
- [ ] Pas de liens vers entités non accessibles

**Tests** :
- [ ] Diététicien A voit uniquement ses données
- [ ] URL directe vers donnée d'un autre = accès refusé
- [ ] Admin voit tout
- [ ] Logs des accès refusés fonctionnent

---

## 🔍 Points critiques de sécurité

**1. Jamais faire confiance à l'input utilisateur**
```php
// ❌ MAUVAIS
$patient = $this->model->get($_GET['id']);

// ✅ BON
$patient = $this->model->get($_GET['id']);
if (!dietetic_can_access_patient($patient->id)) {
    access_denied();
}
```

**2. Toujours filtrer en SQL, pas en PHP**
```php
// ❌ MAUVAIS (récupère tout puis filtre)
$all = $this->get_all();
$filtered = array_filter($all, function($p) {
    return $p->dietitian_id == get_staff_user_id();
});

// ✅ BON (filtre en SQL)
$this->db->where('dietitian_id', get_staff_user_id());
$filtered = $this->db->get()->result();
```

**3. Logs des tentatives d'accès**
```php
if (!dietetic_can_access_patient($id)) {
    log_activity('Unauthorized access attempt: Patient ID ' . $id);
    access_denied();
}
```

---

## 📊 Métriques de succès

Après implémentation :
- ✅ 0 fuite de données entre diététiciens
- ✅ 100% des endpoints sécurisés
- ✅ Logs complets des accès
- ✅ Tests de sécurité passés
- ✅ Interface claire sur permissions

---

Ce plan garantit une sécurité complète et une séparation stricte des données entre diététiciens.
