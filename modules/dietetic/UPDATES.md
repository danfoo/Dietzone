# Mises à Jour du Module Dietetic

## 2025-11-27 - Système de Suivi des Activités Sportives

### 🎯 Fonctionnalités Ajoutées

#### 1. Widget Activités sur le Dashboard Patient
**Fichier:** `views/portal_dashboard.php`

- **Affichage compact des activités du jour**
  - Total des calories brûlées en temps réel
  - Bouton "+" pour ajout rapide d'activité
  - Liste des activités avec durée et calories
  - Modal popup pour sélection rapide d'activité

- **Dropdown personnalisé avec recherche**
  - Remplacement du select natif par un composant searchable
  - Affichage des calories/minute pour chaque activité
  - Regroupement par catégories (Cardio, Musculation, etc.)
  - Design compact (9-11px fonts) optimisé pour mobile

- **Design mobile-first**
  - Sans bordures lourdes, utilise des bordures subtiles (1px)
  - Boutons circulaires pour actions (36x36px)
  - Responsive avec breakpoints à 768px et 1024px

#### 2. Page Dédiée aux Activités
**Fichier:** `views/portal/activities.php`

- **Statistiques en temps réel**
  - Nombre total d'activités enregistrées
  - Minutes totales d'exercice
  - Calories totales brûlées
  - Grid responsive (2 colonnes mobile, 3 colonnes desktop)

- **Formulaire d'ajout d'activité**
  - Sélection d'activité avec calcul automatique des calories
  - Durée en minutes
  - Date et heure (optionnelle)
  - Notes personnelles (optionnel)
  - Affichage en temps réel des calories estimées

- **Historique complet**
  - Liste de toutes les activités du patient
  - Badges colorés par catégorie
  - Détails : durée, calories, notes
  - Bouton de suppression (icône circulaire uniquement)

#### 3. Intégration Admin - Suivi Quotidien Patient
**Fichiers:** `controllers/Patients.php`, `views/admin/patients/view.php`

- **Nouvelles colonnes dans le tableau de suivi**
  - Colonne "Activité" : affiche les minutes totales d'exercice
  - Colonne "Calories" : affiche les calories brûlées (remplace calories consommées)
  - Indicateur visuel si plusieurs activités le même jour

- **Calcul de complétion mis à jour**
  - Eau : basé sur 2000ml (remplace les 8 verres)
  - Activité : 30 minutes d'exercice = 25% de complétion
  - Calories : activités enregistrées = 25% de complétion
  - Total : 100% si tous les critères sont remplis

### 🔧 Corrections Techniques

#### CSRF Token Management
**Fichier:** `controllers/Portal.php`

**Problème Initial:**
- Erreur HTTP 419 lors des requêtes POST
- Token CSRF invalide lors de la suppression
- L'historique ne se rafraîchissait pas après l'ajout

**Solutions Appliquées:**

1. **Utilisation du nom de token dynamique**
   ```php
   // Avant (hardcodé)
   <input type="hidden" name="csrf_token" value="...">

   // Après (dynamique)
   <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>"
          value="<?php echo $this->security->get_csrf_hash(); ?>">
   ```

2. **Suppression de la validation manuelle redondante**
   - CodeIgniter valide automatiquement les tokens CSRF
   - La vérification manuelle causait des faux positifs (hash régénéré)
   - Ligne supprimée: `if (!$csrf_token || $csrf_token !== $this->security->get_csrf_hash())`

3. **Ajout du token dans les réponses JSON**
   ```php
   echo json_encode([
       'success' => true,
       'message' => 'Activité ajoutée avec succès',
       'csrf_token' => $this->security->get_csrf_hash()  // Nouveau token pour prochaine requête
   ]);
   ```

#### Fonction de Notification Personnalisée
**Fichier:** `views/portal/activities.php`

**Problème:** `alert_float is not defined` - fonction Perfex admin non disponible dans le portail patient

**Solution:** Création de `showNotification(message, type)`
- Toast notifications avec auto-dismiss (3.5s)
- Icônes différenciées (✓ succès, ⚠ erreur)
- Couleurs : vert (#48bb78) pour succès, rouge (#e74c3c) pour erreurs
- Position fixe en haut à droite
- Responsive et accessible

```javascript
function showNotification(message, type) {
    const bgColor = type === 'success' ? '#48bb78' : '#e74c3c';
    const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';

    const toast = $('<div>')
        .css({
            position: 'fixed',
            top: '20px',
            right: '20px',
            background: bgColor,
            color: 'white',
            // ... styles additionnels
        })
        .html('<i class="fa ' + icon + '"></i> ' + message)
        .appendTo('body');

    setTimeout(() => toast.fadeOut(300, () => toast.remove()), 3500);
}
```

### 📦 Base de Données

**Table Utilisée:** `tbldietic_patient_activities`

**Structure:**
```sql
- id (int, auto_increment, PK)
- patient_id (int, FK -> tbldietic_patients.id)
- activity_id (int, FK -> tbldietic_activities.id)
- duration_minutes (int)
- kcal_burned (decimal)
- activity_date (date)
- activity_time (time, nullable)
- notes (text, nullable)
- created_at (datetime)
```

**Table de Référence:** `tbldietic_activities` (70+ activités pré-existantes)
```sql
- id
- name
- category (Cardio, Musculation, Sports collectifs, Yoga/Étirements, etc.)
- kcal_per_minute
```

### 🎨 Design et UX

#### Principes de Design Appliqués

1. **Mobile-First**
   - Padding adaptatif : 12px mobile, 24px desktop
   - Grid flexible : 2 colonnes → 3 colonnes selon viewport
   - Fonts compactes : 9-13px pour maximiser l'espace

2. **Design Minimaliste**
   - Suppression des box-shadows lourdes
   - Bordures subtiles (1px #f0f0f0)
   - Boutons circulaires sans texte (icônes uniquement)
   - Espacement généreux mais contrôlé

3. **Feedback Visuel**
   - Hover effects sur les cartes (translateY)
   - Transitions fluides (0.2s ease)
   - Badges colorés par catégorie
   - Indicateurs de progression (completion %)

4. **Accessibilité**
   - Labels explicites sur tous les champs
   - Attributs title sur les boutons icônes
   - Contrastes conformes WCAG
   - Messages d'erreur clairs

### 📝 API Endpoints Ajoutés

#### Portal.php (Patient)

1. **GET** `/dietetic/portal/get_activities`
   - Retourne la liste complète des activités disponibles
   - Groupées par catégorie
   - Inclut kcal_per_minute pour chaque activité

2. **GET** `/dietetic/portal/get_my_activities`
   - Retourne les activités du patient connecté
   - Inclut les statistiques : total activités, minutes, calories
   - Format : `{success, activities[], stats{}}`

3. **POST** `/dietetic/portal/add_activity`
   - Ajoute une nouvelle activité pour le patient
   - Paramètres : activity_id, duration_minutes, kcal_burned, activity_date, activity_time, notes
   - Support double mode : JSON response OU redirect (pour dashboard widget)
   - Retourne le nouveau CSRF token

4. **POST** `/dietetic/portal/delete_activity/{id}`
   - Supprime une activité du patient
   - Vérifie que l'activité appartient au patient (sécurité)
   - Retourne le nouveau CSRF token

5. **GET** `/dietetic/portal/api_get_today_activities`
   - API légère pour le widget dashboard
   - Retourne uniquement les activités du jour
   - Statistiques journalières : count, total_minutes, total_kcal

### 🔐 Sécurité

#### Mesures Implémentées

1. **CSRF Protection**
   - Tokens dynamiques régénérés à chaque requête
   - Validation automatique par CodeIgniter
   - Token inclus dans chaque réponse JSON

2. **Authentification Patient**
   - Vérification `is_client_logged_in()` sur chaque endpoint
   - Helper `get_logged_in_patient()` pour récupérer le patient actuel
   - Association client_id → patient_id sécurisée

3. **Validation des Données**
   - Vérification des champs obligatoires
   - Type checking (int, decimal, date)
   - Sanitization automatique via CodeIgniter Input class

4. **Contrôle d'Accès**
   - Un patient ne peut voir/modifier que ses propres activités
   - Vérification `$activity->patient_id === $patient->id` avant suppression
   - Messages d'erreur génériques (pas de leak d'info)

### 🧪 Testing Checklist

- [x] Ajout d'activité depuis le dashboard
- [x] Ajout d'activité depuis la page dédiée
- [x] Suppression d'activité
- [x] Rafraîchissement automatique de l'historique
- [x] Calcul correct des calories (kcal/min × durée)
- [x] Affichage des statistiques en temps réel
- [x] Notifications de succès/erreur
- [x] Gestion des tokens CSRF
- [x] Responsive design (mobile, tablet, desktop)
- [x] Intégration admin - affichage des données
- [x] Calcul de complétion avec nouvelles règles (2000ml eau)

### 📊 Impact Performance

- **Requêtes DB ajoutées:** +2 par chargement de page admin (activités + stats)
- **Optimisation:** Index sur `patient_id` et `activity_date` dans `tbldietic_patient_activities`
- **Cache:** Aucun cache implémenté (données temps réel requises)
- **Taille JS:** +~200 lignes (composant dropdown + logique activités)

### 🚀 Prochaines Améliorations Suggérées

1. **Graphiques & Analytics**
   - Graphique de progression hebdomadaire/mensuelle
   - Comparaison objectifs vs réalisé
   - Tendances des activités préférées

2. **Gamification**
   - Badges pour objectifs atteints (streaks, records)
   - Points par minute d'exercice
   - Classement anonyme (optionnel)

3. **Notifications**
   - Rappels quotidiens d'activité
   - Alertes objectif non atteint
   - Félicitations pour records

4. **Export & Partage**
   - Export PDF du rapport mensuel
   - Partage avec le diététicien
   - Intégration Google Fit / Apple Health

### 📞 Support

Pour tout problème ou question :
- Email: support@dietsenegal.net
- Documentation: https://app.dietsenegal.net/dietetic/docs
- Tickets: GitHub Issues

---

**Version:** 1.2.0
**Date:** 27 Novembre 2025
**Auteur:** Équipe Développement Dietzone
**Statut:** ✅ En Production
