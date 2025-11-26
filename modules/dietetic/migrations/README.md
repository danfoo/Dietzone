# 🗄️ Migrations - Module Diététique

## 📝 Vue d'ensemble

Ce dossier contient les migrations de base de données pour le module Dietetic. Les migrations permettent de modifier la structure de la base de données de manière contrôlée et traçable.

## 🔐 Accès et Sécurité

### Accès à la page des migrations

1. Connectez-vous en tant qu'**administrateur**
2. Allez à : `Admin → Dietetic → Migrations` ou directement :
   ```
   https://votresite.com/admin/dietetic/migrations
   ```

### ✅ Protection CSRF

**Toutes les migrations sont protégées par CSRF token** - Le système génère automatiquement un token pour chaque formulaire.

## 📋 Migrations Disponibles

### 1. Migration des Champs d'Anamnèse
**Fichier:** `anamnesis_fields_migration.sql`
**Description:** Ajoute **60+ nouveaux champs** à la table `dietic_patients` pour un suivi d'anamnèse complet.

**Sections ajoutées:**
- Informations personnelles (4 champs)
- Informations spécifiques femmes (4 champs)
- Mesures anthropométriques
- Habitudes de vie et santé mentale
- Historique médical et familial
- Habitudes alimentaires et digestives

**Application:**
```
https://votredomaine.com/admin/dietetic/apply_anamnesis_migration
```

### 2. Mesures Tour de Cou et Mollets
**Fichier:** `add_neck_calf_measurements.php`
**Date:** 2025-11-26
**Description:** Ajoute les colonnes `neck` et `calf` à la table `tbldietic_measurements`.

**Colonnes ajoutées:**
- `neck` - decimal(5,2) - Tour de cou en cm
- `calf` - decimal(5,2) - Tour de mollets en cm

**Utilisation:** Complète les données anthropométriques pour un suivi plus précis des patients.

## 🚀 Appliquer une Migration

### Via l'interface web (Recommandé)

1. Accédez à `Admin → Dietetic → Migrations`
2. Localisez la migration à appliquer (statut "En attente")
3. Cliquez sur le bouton **"Appliquer"**
4. Confirmez l'action dans la boîte de dialogue
5. La migration sera appliquée avec protection CSRF automatique

### Sécurité

- ✅ Seuls les **administrateurs** peuvent accéder
- ✅ Chaque requête est **protégée par CSRF token**
- ✅ Confirmation obligatoire avant application
- ✅ Traçabilité via `log_activity()`
- ✅ Vérification d'existence avant modification
- ✅ Protection contre la double application

## 📊 Traçabilité

Chaque migration appliquée est enregistrée dans :
- **Table:** `tbldietic_migrations`
- **Champs:**
  - `migration_name` - Nom du fichier
  - `applied_at` - Date et heure d'application

## ✅ Vérification Post-Migration

1. ✅ Migration marquée comme "Appliquée" dans l'interface
2. ✅ Colonnes présentes dans la base de données
3. ✅ Formulaires s'affichent correctement
4. ✅ Sauvegarde des données fonctionne

## ⚠️ Bonnes Pratiques

1. **Toujours faire une sauvegarde** de la base de données avant
2. **Tester les migrations** en développement d'abord
3. **Ne jamais supprimer** les fichiers de migration appliqués
4. **Vérifier les logs** après application

## 🔧 Dépannage

### La migration n'apparaît pas
- Vérifiez l'extension `.php`
- Vérifiez le format de la classe `Migration_nom`
- Videz le cache du navigateur

### Erreur lors de l'application
- Consultez les logs dans Perfex
- Vérifiez les permissions DB
- Vérifiez que la table existe

Bon travail ! 🎉
