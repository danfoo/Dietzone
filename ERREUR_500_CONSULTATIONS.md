# 🔧 Résolution Erreur 500 - Formulaire Consultations

## Problème

Lorsque vous soumettez le formulaire de consultation, vous obtenez l'erreur:
```
POST https://app.dietsenegal.net/admin/dietetic/consultations/create
net::ERR_HTTP_RESPONSE_CODE_FAILURE 500 (Internal Server Error)
```

## Cause

L'erreur 500 est causée par des **colonnes manquantes dans la base de données**. Le nouveau formulaire utilise les champs:
- `consultation_mode` (in_person / online)
- `online_platform` (zoom, google_meet, teams, whatsapp, etc.)
- `meeting_link` (URL de la réunion)

Ces colonnes n'existent pas encore dans votre table `tbldietic_consultations`.

## ✅ Solution Automatique (Recommandée)

### Étape 1: Exécuter la Migration Automatique

Visitez cette URL dans votre navigateur:

```
https://app.dietsenegal.net/admin/dietetic/migrate_consultations
```

Cette page va:
1. ✅ Vérifier quelles colonnes manquent
2. ✅ Les ajouter automatiquement
3. ✅ Créer les index nécessaires
4. ✅ Afficher un rapport détaillé

### Étape 2: Vérifier le Résultat

Après l'exécution, vous devriez voir:
- ✓ Message de succès en vert
- ✓ Tableau avec toutes les colonnes (nouvelles colonnes surlignées)
- ✓ Liens pour tester le formulaire

### Étape 3: Tester

Retournez sur le formulaire de création de consultation:
```
https://app.dietsenegal.net/admin/dietetic/consultations/create
```

Le formulaire devrait maintenant fonctionner correctement! 🎉

---

## 🛠️ Solution Manuelle (Alternative)

Si la migration automatique ne fonctionne pas, vous pouvez appliquer les changements manuellement via phpMyAdmin:

### Via phpMyAdmin:

1. Connectez-vous à phpMyAdmin
2. Sélectionnez votre base de données
3. Cliquez sur l'onglet "SQL"
4. Copiez-collez ce code SQL:

```sql
-- Ajouter la colonne consultation_mode
ALTER TABLE `tbldietic_consultations`
ADD COLUMN `consultation_mode` VARCHAR(20) DEFAULT 'in_person'
COMMENT 'in_person or online'
AFTER `consultation_type`;

-- Ajouter la colonne online_platform
ALTER TABLE `tbldietic_consultations`
ADD COLUMN `online_platform` VARCHAR(50) DEFAULT NULL
COMMENT 'zoom, google_meet, teams, whatsapp, skype, other'
AFTER `consultation_mode`;

-- Ajouter la colonne meeting_link
ALTER TABLE `tbldietic_consultations`
ADD COLUMN `meeting_link` VARCHAR(500) DEFAULT NULL
COMMENT 'URL for online meetings'
AFTER `online_platform`;

-- Ajouter les index pour améliorer les performances
ALTER TABLE `tbldietic_consultations`
ADD INDEX `consultation_mode` (`consultation_mode`);

ALTER TABLE `tbldietic_consultations`
ADD INDEX `online_platform` (`online_platform`);
```

5. Cliquez sur "Exécuter"

### Via Ligne de Commande:

```bash
mysql -u votre_utilisateur -p votre_base_de_donnees < modules/dietetic/migrations/add_communication_channels.sql
```

---

## 🧪 Vérification

Pour vérifier que tout est correct, vous pouvez exécuter cette requête SQL:

```sql
SHOW COLUMNS FROM tbldietic_consultations;
```

Vous devriez voir les 3 nouvelles colonnes:
- `consultation_mode`
- `online_platform`
- `meeting_link`

---

## 📝 Notes Importantes

⚠️ **Sauvegardez votre base de données** avant d'appliquer la migration (par précaution)

✅ **Ces modifications sont rétrocompatibles**: les consultations existantes ne seront pas affectées

✅ **Valeurs par défaut**: Les consultations existantes auront automatiquement `consultation_mode = 'in_person'`

---

## 🆘 Besoin d'Aide?

Si vous rencontrez toujours des problèmes après avoir appliqué la migration:

1. **Vérifiez les logs PHP**: Regardez `/var/log/apache2/error.log` ou équivalent
2. **Vérifiez les permissions**: Assurez-vous que l'utilisateur MySQL a les droits ALTER TABLE
3. **Contactez le support**: Fournissez le message d'erreur exact

---

## 🎯 Résumé Rapide

```bash
# Solution en 1 étape:
1. Visitez: https://app.dietsenegal.net/admin/dietetic/migrate_consultations

# C'est tout! ✨
```
