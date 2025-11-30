# 🎮 Guide d'Intégration - Système de Gamification

## 📦 Fichiers Créés

### 1. Migration SQL
- **Fichier**: `migrations/add_achievements_badges.sql`
- **Contenu**: 4 nouvelles tables + 20 badges par défaut

### 2. Modèle
- **Fichier**: `models/Dietetic_gamification_model.php`
- **Fonctions**: Gestion badges, points, niveaux, notifications

### 3. Widget Visuel
- **Fichier**: `views/portal/widgets/achievements_badges.php`
- **Design**: Badge wall moderne + progression de niveau

---

## 🚀 Étapes d'Installation

### Étape 1: Exécuter la Migration SQL

**Via l'interface admin** (Recommandé):
1. Aller dans: **Admin → Diététique → Notifications → Migrations**
2. Chercher la migration "Achievements & Badges"
3. Cliquer sur "Installer"

**Ou via phpMyAdmin**:
```sql
-- Copier/coller le contenu de migrations/add_achievements_badges.sql
```

### Étape 2: Intégrer le Widget dans le Dashboard

Ouvrir le fichier: `views/portal_dashboard.php`

**Chercher la ligne** (environ ligne 3415):
```php
<?php } ?>

<!-- Program Card -->
<?php if ($active_program) { ?>
```

**Insérer AVANT cette ligne**:
```php
<!-- Achievements & Badges Widget -->
<?php $this->load->view('dietetic/portal/widgets/achievements_badges', ['patient' => $patient]); ?>
```

### Étape 3: Activer la Vérification Automatique des Badges

**Option A: Ajouter dans le Contrôleur Portal (Recommandé)**

Ouvrir: `controllers/Portal.php`

Dans la fonction `dashboard()`, après avoir récupéré `$patient`, ajouter:

```php
// Check and award badges automatically
$this->load->model('dietetic/dietetic_gamification_model');
$this->dietetic_gamification_model->check_and_award_badges($patient->id);
```

**Option B: Hook après validation quotidienne**

Dans `controllers/Portal.php`, fonction `validate_tracking()`:

Après la ligne de validation réussie, ajouter:
```php
// Award points and check badges
$this->load->model('dietetic/dietetic_gamification_model');

// Award daily checkin points
$this->dietetic_gamification_model->award_points(
    $patient_id,
    10, // points
    'daily_checkin',
    'Validation quotidienne'
);

// Check for new badges
$this->dietetic_gamification_model->check_and_award_badges($patient_id);
```

### Étape 4: Ajouter les Points pour les Actions

**Dans chaque action qui mérite des points**, ajouter:

```php
$this->load->model('dietetic/dietetic_gamification_model');

// Exemple: Après validation d'un repas
$this->dietetic_gamification_model->award_points(
    $patient_id,
    5,
    'meal_logged',
    'Repas validé'
);

// Exemple: Après pesée
$this->dietetic_gamification_model->award_points(
    $patient_id,
    20,
    'weigh_in',
    'Pesée hebdomadaire'
);

// Exemple: Après activité physique
$this->dietetic_gamification_model->award_points(
    $patient_id,
    15,
    'activity',
    'Activité physique enregistrée',
    $activity_id
);
```

---

## 📋 Badges Disponibles (20 badges)

### 🔥 Streak (5 badges)
- **Premier Pas**: 1 jour (10 pts)
- **Guerrier Hebdo**: 7 jours (50 pts)
- **Maître du Mois**: 30 jours (200 pts)
- **Inarrêtable**: 100 jours (500 pts)
- **Légende**: 365 jours (1000 pts)

### 🏆 Perte de Poids (5 badges)
- 5kg, 10kg, 15kg, 20kg, 25kg

### 🍽️ Nutrition (3 badges)
- 7, 30, 90 jours de repas validés

### 💧 Hydratation (3 badges)
- 7, 30, 90 jours d'hydratation

### 🏃 Activité (3 badges)
- 5, 20, 50 activités

### 🎯 Objectif (1 badge)
- Poids cible atteint

---

## ⭐ Système de Niveaux

| Niveau | Points Min | Icône | Couleur |
|--------|-----------|-------|---------|
| Débutant | 0 | fa-star | Gris |
| Bronze | 100 | fa-certificate | Bronze |
| Argent | 300 | fa-shield | Argent |
| Or | 600 | fa-star | Or |
| Platine | 1000 | fa-diamond | Platine |
| Diamant | 2000+ | fa-gem | Diamant |

---

## 💰 Attribution des Points

| Action | Points | Type |
|--------|--------|------|
| Validation quotidienne | +10 | daily_checkin |
| Repas validé | +5 | meal_logged |
| Eau validée | +3 | water_logged |
| Pesée hebdo | +20 | weigh_in |
| Activité physique | +15 | activity |
| Consultation | +25 | consultation |
| Streak 7j | +50 | streak_bonus |
| Streak 30j | +200 | streak_bonus |
| Badge débloqué | Variable | badge_unlocked |

---

## 🔔 Notifications Automatiques

Le système envoie automatiquement:
- 🏆 **Badge débloqué**: Notification push quand nouveau badge
- ⭐ **Nouveau niveau**: Notification push lors du level up
- 🔥 **Streak en danger**: Rappel si risque de perdre la série (à implémenter)

---

## 🧪 Tests Recommandés

### Test 1: Vérifier l'Installation
```sql
-- Vérifier que les tables existent
SHOW TABLES LIKE 'tbldietic_badge_%';
SHOW TABLES LIKE 'tbldietic_patient_points';

-- Vérifier les badges par défaut
SELECT COUNT(*) FROM tbldietic_badge_definitions;
-- Doit retourner: 20
```

### Test 2: Test Manuel d'Attribution
Via phpMyAdmin ou interface PHP:
```php
$CI = &get_instance();
$CI->load->model('dietetic/dietetic_gamification_model');

// Débloquer un badge test
$patient_id = 1; // ID du patient test
$CI->dietetic_gamification_model->unlock_badge($patient_id, 'first_day');

// Attribuer des points
$CI->dietetic_gamification_model->award_points($patient_id, 50, 'test', 'Test points');

// Vérifier
$points = $CI->dietetic_gamification_model->get_patient_points($patient_id);
var_dump($points);
```

### Test 3: Vérifier l'Affichage
1. Se connecter en tant que patient
2. Aller sur le dashboard
3. Vérifier que la section "Mes Succès" s'affiche
4. Vérifier le niveau et la progression
5. Cliquer sur un badge pour voir le tooltip

---

## 📊 Monitoring & Analytics

### Requêtes Utiles

**Voir les badges les plus débloqués**:
```sql
SELECT
    bd.name,
    COUNT(*) as unlock_count
FROM tbldietic_patient_badges pb
JOIN tbldietic_badge_definitions bd ON bd.id = pb.badge_id
GROUP BY pb.badge_id
ORDER BY unlock_count DESC;
```

**Voir le top des patients par points**:
```sql
SELECT
    p.id,
    c.company as patient_name,
    pp.total_points,
    pp.current_level
FROM tbldietic_patient_points pp
JOIN tbldietic_patients p ON p.id = pp.patient_id
JOIN tblclients c ON c.userid = p.client_id
ORDER BY pp.total_points DESC
LIMIT 10;
```

**Historique des points d'un patient**:
```sql
SELECT *
FROM tbldietic_points_history
WHERE patient_id = 1
ORDER BY created_at DESC
LIMIT 50;
```

---

## 🎨 Personnalisation

### Modifier les Couleurs des Niveaux
Éditer: `models/Dietetic_gamification_model.php`

Chercher `private $levels = [` et modifier les couleurs.

### Ajouter un Nouveau Badge
```sql
INSERT INTO tbldietic_badge_definitions
(badge_key, name, description, icon, color, category, requirement_type, requirement_value, points, display_order)
VALUES
('custom_badge', 'Mon Badge', 'Description', 'fa-star', '#FF0000', 'custom', 'custom_action', 1, 100, 100);
```

### Modifier les Points par Action
```sql
UPDATE tbldietic_notification_settings
SET setting_value = '15'  -- Nouvelle valeur
WHERE setting_key = 'points_daily_checkin';
```

---

## 🐛 Troubleshooting

### Les badges ne s'affichent pas
1. Vérifier que la migration a bien été exécutée
2. Vérifier les logs PHP pour les erreurs
3. Vérifier que le widget est bien inclus dans le dashboard

### Les points ne sont pas attribués
1. Vérifier que `award_points()` est appelé après chaque action
2. Vérifier les logs de `tbldietic_points_history`
3. Vérifier que le patient_id est correct

### Les notifications ne sont pas envoyées
1. Vérifier que Firebase/Push est configuré
2. Vérifier les préférences de notification du patient
3. Vérifier les logs de `tbldietic_notification_logs`

---

## 🔮 Prochaines Améliorations Possibles

1. **Classement (Leaderboard)** - Top des patients par points
2. **Défis hebdomadaires** - Objectifs temporaires
3. **Récompenses** - Débloquer des contenus premium
4. **Partage social** - Partager ses achievements
5. **Badges saisonniers** - Événements spéciaux
6. **Système de quêtes** - Objectifs à étapes multiples

---

## 📞 Support

Pour toute question ou problème:
- Vérifier les logs: `application/logs/`
- Vérifier la console JavaScript (F12)
- Contacter le développeur

---

**Date de création**: 30 janvier 2025
**Version**: 1.0
**Compatibilité**: Dietzone v2.0+
