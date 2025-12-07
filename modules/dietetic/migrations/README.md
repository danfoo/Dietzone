# Migrations de base de données - Module Dietetic

## Comment appliquer une migration

### Option 1: Via phpMyAdmin (Recommandé)
1. Connectez-vous à phpMyAdmin
2. Sélectionnez votre base de données
3. Cliquez sur l'onglet "SQL"
4. Copiez le contenu du fichier `.sql` de migration
5. Collez-le dans la zone de texte
6. Cliquez sur "Exécuter"

### Option 2: Via ligne de commande MySQL
```bash
mysql -u username -p database_name < add_unique_constraint_daily_tracking.sql
```

## Migrations disponibles

### add_unique_constraint_daily_tracking.sql
**Problème résolu**: Les cases à cocher des repas (Petit déjeuner, Déjeuner, Dîner) se décochent après actualisation de la page.

**Cause**: La table `dietic_daily_tracking` n'a pas de contrainte UNIQUE sur `(patient_id, tracking_date)`, ce qui empêche la requête `INSERT ... ON DUPLICATE KEY UPDATE` de fonctionner correctement.

**Solution**:
1. Supprime les enregistrements en double (garde le plus récent)
2. Ajoute une contrainte UNIQUE sur `(patient_id, tracking_date)`

**À exécuter**: Une seule fois, dès que possible.
