# 🗄️ Migration des Champs d'Anamnèse - Module Diététique

## 📝 Description

Cette migration ajoute **60+ nouveaux champs** à la table `dietic_patients` pour permettre un suivi d'anamnèse complet et professionnel des patients.

## 🎯 Champs Ajoutés

### Section 1: Informations Personnelles (4 champs)
- `title` - Civilité (M./Mme/Mlle)
- `occupation` - Profession  
- `work_type` - Type de travail (sédentaire → très physique)
- `address` - Adresse complète

### Section 1.5: Informations Spécifiques Femmes (4 champs)
- `is_pregnant` - Grossesse en cours (yes/no)
- `pregnancy_months` - Mois de grossesse (1-9)
- `breastfeeding` - Allaitement (yes/no)
- `menstrual_cycle` - Cycle menstruel

## 🚀 Comment Appliquer la Migration

### Étape 1: Vérifier l'état actuel
Accédez à: `https://votredomaine.com/admin/dietetic/check_anamnesis_fields`

### Étape 2: Appliquer la migration
Accédez à: `https://votredomaine.com/admin/dietetic/apply_anamnesis_migration`

⚠️ **Attention:** Cette opération nécessite les droits administrateur.

## ✅ Vérification Post-Migration

1. ✅ Tous les champs présents dans la base de données
2. ✅ Le formulaire patient s'affiche correctement
3. ✅ Les calculs automatiques fonctionnent
4. ✅ Les champs conditionnels s'affichent correctement
5. ✅ La sauvegarde des données fonctionne

Bon travail ! 🎉
