# Migration : Système de Notation des Diététiciens

## Instructions d'Installation

### 1. Appliquer la Migration SQL

Pour créer la table `dietetic_ratings` dans votre base de données, vous avez deux options :

#### Option A : Via l'interface web (Recommandé)

1. **Accédez à la page de migration** :
   ```
   https://votre-site.com/modules/dietetic/migrations/apply_migrations.php
   ```

2. La page affichera le résultat de la migration avec des messages de succès ou d'erreur.

3. Une fois la migration appliquée, vous verrez :
   ```
   ✓ Migration executed successfully
   ✓ Foreign key added successfully
   ✓ Foreign key added successfully

   Ratings table migration completed!
   ```

#### Option B : Via phpMyAdmin ou ligne de commande MySQL

1. **Ouvrez le fichier SQL** : `add_ratings_table.sql`

2. **Remplacez le préfixe** :
   - Remplacez `tbldietic_` par votre préfixe de base de données (par exemple `tbldietic_` si vous utilisez le préfixe par défaut)

3. **Exécutez le SQL** dans votre base de données

### 2. Vérifier que la Table est Créée

Exécutez cette requête SQL pour vérifier :

```sql
DESCRIBE tbldietic_ratings;
```

Vous devriez voir les colonnes :
- `id`, `patient_id`, `dietitian_id`
- `overall_rating`
- `professionalism_rating`, `listening_rating`, `advice_rating`, `results_rating`, `availability_rating`
- `comment`, `is_public`, `is_verified`
- `created_at`, `updated_at`

### 3. Vérifier les Menus

Après l'installation, vérifiez que les nouveaux menus apparaissent :

#### Partie Admin :
- Menu "Diététique" → "Diététiciens" (nouveau!)
  - URL : `/admin/dietetic/dietitians`

#### Partie Patient (Portal) :
- Navigation "Mon Diététicien" dans le portal patient
  - URL : `/dietetic/portal/my_dietitians`

### 4. Tester le Système

#### A. Côté Admin

1. **Accédez à la liste des diététiciens** :
   - Allez dans `Diététique > Diététiciens`
   - Vous devriez voir tous les membres du staff qui ont des patients assignés
   - Vérifiez que les statistiques s'affichent (nombre de patients, consultations, programmes)

2. **Consultez un profil de diététicien** :
   - Cliquez sur "Voir le Profil" d'un diététicien
   - Vérifiez que la page affiche :
     - Les informations du diététicien
     - Les statistiques
     - La moyenne des notes (si des notes existent)
     - Les barres de progression par critère
     - La liste des avis des patients

3. **Testez les actions admin** :
   - Masquer/Afficher un avis
   - Supprimer un avis (si nécessaire)

#### B. Côté Patient

1. **Connectez-vous en tant que patient** (client ayant un diététicien assigné)

2. **Accédez à "Mon Diététicien"** :
   - Naviguez vers `/dietetic/portal/my_dietitians`
   - Vérifiez que vous voyez :
     - Le profil de votre diététicien
     - Sa note moyenne
     - Votre avis (si vous en avez déjà laissé un)
     - Le bouton "Noter mon diététicien"

3. **Testez la notation** :
   - Cliquez sur "Noter mon diététicien"
   - Vérifiez que le formulaire affiche 5 critères avec des étoiles interactives :
     * Professionnalisme
     * Écoute
     * Conseils Pratiques
     * Résultats Obtenus
     * Disponibilité
   - Notez chaque critère en cliquant sur les étoiles
   - Vérifiez que la note globale se calcule automatiquement en haut
   - Ajoutez un commentaire (optionnel)
   - Soumettez le formulaire

4. **Vérifiez la note enregistrée** :
   - Retournez à "Mon Diététicien"
   - Vérifiez que votre avis s'affiche
   - Essayez de modifier votre avis
   - Contactez le diététicien par email (bouton de contact)

### 5. Cas d'Erreurs à Tester

1. **Patient sans consultation complétée** :
   - Essayez de noter avec un patient qui n'a pas de consultation "completed"
   - Le système devrait afficher : "Vous devez avoir au moins une consultation complétée pour noter votre diététicien"

2. **Note sans étoile** :
   - Essayez de soumettre le formulaire sans noter aucun critère
   - Le système devrait afficher une alerte : "Veuillez noter au moins un critère"

3. **Modification d'une note existante** :
   - Un patient qui a déjà noté devrait pouvoir modifier sa note
   - Le bouton devrait dire "Modifier mon avis" au lieu de "Noter mon diététicien"

### 6. Vérifications de Sécurité

- ✅ Un patient ne peut noter que son propre diététicien
- ✅ Un patient ne peut noter qu'après avoir eu au moins une consultation complétée
- ✅ Un patient ne peut avoir qu'une seule note par diététicien (UNIQUE constraint)
- ✅ Les admins peuvent masquer ou supprimer des avis inappropriés
- ✅ Le calcul de la note globale est automatique (moyenne des 5 critères)

## Structure des Fichiers Créés

### Contrôleurs
- `controllers/Dietitians.php` - Gestion des diététiciens côté admin
- `controllers/Portal.php` - Méthodes `my_dietitians()` et `rate_dietitian()` ajoutées

### Modèles
- `models/Dietetic_ratings_model.php` - Modèle complet pour gérer les notes

### Vues Admin
- `views/admin/dietitians/list.php` - Liste des diététiciens avec notes
- `views/admin/dietitians/view.php` - Profil détaillé d'un diététicien

### Vues Portal
- `views/portal_my_dietitians.php` - Page "Mon Diététicien" pour le patient
- `views/portal_rate_dietitian.php` - Formulaire de notation interactif

### Migrations
- `migrations/add_ratings_table.sql` - Création de la table
- `migrations/apply_migrations.php` - Script d'application automatique

## Support

Si vous rencontrez des problèmes :

1. Vérifiez les logs PHP : `/path/to/perfex/logs/`
2. Vérifiez que la table existe : `SHOW TABLES LIKE '%dietic_ratings%'`
3. Vérifiez que les foreign keys sont créées : `SHOW CREATE TABLE tbldietic_ratings`
4. Vérifiez les permissions du module Dietetic dans Perfex CRM

## Fonctionnalités Futures (Optionnelles)

- [ ] Notifications par email au diététicien lors d'une nouvelle note
- [ ] Statistiques avancées sur le dashboard
- [ ] Export des avis en PDF/Excel
- [ ] Système de réponse du diététicien aux avis
- [ ] Badges pour les diététiciens les mieux notés
