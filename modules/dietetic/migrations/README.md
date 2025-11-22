# Migrations de la Base de Données - Module Diététique

Ce dossier contient les scripts de migration SQL pour mettre à jour la structure de la base de données.

## Comment Appliquer les Migrations

### Option 1: Via phpMyAdmin ou Interface SQL

1. Connectez-vous à votre base de données via phpMyAdmin
2. Sélectionnez votre base de données
3. Allez dans l'onglet "SQL"
4. Copiez-collez le contenu du fichier de migration
5. Cliquez sur "Exécuter"

### Option 2: Via Ligne de Commande

```bash
mysql -u votre_utilisateur -p votre_base_de_donnees < add_communication_channels.sql
```

## Migrations Disponibles

### add_communication_channels.sql (2025-01-22)

**Description**: Ajoute les champs pour les canaux de communication en ligne dans la table consultations.

**Nouveaux Champs**:
- `consultation_mode`: VARCHAR(20) - Mode de consultation (in_person ou online)
- `online_platform`: VARCHAR(50) - Plateforme utilisée (zoom, google_meet, teams, whatsapp, skype, other)
- `meeting_link`: VARCHAR(500) - Lien de la réunion en ligne

**Impact**: Permet de gérer les consultations en ligne via Zoom, Google Meet, Microsoft Teams, WhatsApp, Skype, etc.

**Compatible avec**: Toutes les versions existantes du module

**Rollback**: Si nécessaire, vous pouvez supprimer les colonnes avec:
```sql
ALTER TABLE \`tbldietic_consultations\` DROP COLUMN \`consultation_mode\`;
ALTER TABLE \`tbldietic_consultations\` DROP COLUMN \`online_platform\`;
ALTER TABLE \`tbldietic_consultations\` DROP COLUMN \`meeting_link\`;
```

## Vérification

Après avoir appliqué la migration, vous pouvez vérifier que les colonnes ont bien été ajoutées:

```sql
DESCRIBE tbldietic_consultations;
```

Vous devriez voir les 3 nouvelles colonnes dans la structure de la table.

## Notes Importantes

- ⚠️ **Sauvegardez toujours votre base de données avant d'appliquer une migration**
- Les migrations sont conçues pour être appliquées dans l'ordre chronologique
- Si une migration échoue, vérifiez les messages d'erreur et assurez-vous que votre version MySQL/MariaDB est compatible
- Les migrations sont idempotentes autant que possible (utilisation de IF NOT EXISTS, etc.)
