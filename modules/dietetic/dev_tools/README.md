# 🛠️ Dev Tools - Outils de Développement

Ce dossier contient tous les scripts de développement, diagnostic, test et maintenance du module Dietetic.

**⚠️ ATTENTION** : Ces outils sont destinés aux développeurs et ne doivent PAS être utilisés en production sans supervision.

---

## 📁 Structure

### `/diagnostic/` - Outils de Diagnostic
Scripts pour diagnostiquer et identifier les problèmes :
- Vérification du cron
- Diagnostic des notifications
- Diagnostic des erreurs 404
- Diagnostic web interactif

### `/test/` - Scripts de Test
Scripts pour tester les fonctionnalités :
- Test du cron manuel
- Test des notifications
- Test des rappels

### `/fix/` - Scripts de Réparation
Scripts pour corriger des problèmes spécifiques :
- Correction des conflits clients
- Correction des erreurs 404
- Réparation des meal plans orphelins
- Désactivation temporaire des hooks

### `/migration/` - Migrations Manuelles
Scripts de migration de base de données (obsolètes si migrations auto fonctionnent) :
- Migration des tables food surveys
- Ajout de colonnes meal_type
- Installation manuelle de tables

### `/setup/` - Scripts de Setup Manuel
Scripts de configuration manuelle (utiliser l'interface admin de préférence) :
- Configuration des notifications
- Installation manuelle de tables

---

## 🚀 Utilisation

### En Développement
Ces scripts peuvent être exécutés directement :
```bash
php /path/to/modules/dietetic/dev_tools/diagnostic/diagnostic.php
```

### En Production
**❌ NE PAS UTILISER** sauf en cas d'urgence et sous supervision d'un développeur.

---

## 📝 Notes

- **Date de création** : 2025-12-04
- **Phase** : Phase 1 - Nettoyage du code
- **Raison** : Séparer les outils de dev du code de production pour une meilleure organisation et sécurité

---

## 🔗 Retour au Module
[← Retour à la documentation principale](../README.md)
