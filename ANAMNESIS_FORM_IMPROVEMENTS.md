# Améliorations du Formulaire d'Anamnèse - Patient

## Champs à ajouter à la base de données

### Table: `tbldietic_patients`

```sql
-- Informations personnelles
ALTER TABLE `tbldietic_patients`
ADD COLUMN `title` VARCHAR(10) NULL COMMENT 'Civilité: M., Mme, Mlle' AFTER `gender`,
ADD COLUMN `occupation` VARCHAR(100) NULL COMMENT 'Profession' AFTER `title`,
ADD COLUMN `address` TEXT NULL COMMENT 'Adresse complète' AFTER `occupation`,

-- Antécédents et historique médical
ADD COLUMN `family_history` TEXT NULL COMMENT 'Antécédents familiaux' AFTER `medical_conditions`,
ADD COLUMN `supplements` TEXT NULL COMMENT 'Compléments alimentaires' AFTER `medications`,

-- Mode de vie
ADD COLUMN `sleep_hours` DECIMAL(3,1) NULL COMMENT 'Heures de sommeil par nuit' AFTER `activity_level`,
ADD COLUMN `stress_level` ENUM('low','moderate','high','very_high') NULL COMMENT 'Niveau de stress' AFTER `sleep_hours`,
ADD COLUMN `smoking` ENUM('no','yes','former') NULL COMMENT 'Tabagisme' AFTER `stress_level`,
ADD COLUMN `alcohol_consumption` ENUM('never','occasional','regular','frequent') NULL COMMENT 'Consommation d\'alcool' AFTER `smoking`,
ADD COLUMN `physical_activity_details` TEXT NULL COMMENT 'Détails activité physique' AFTER `alcohol_consumption`,

-- Système digestif
ADD COLUMN `digestive_symptoms` TEXT NULL COMMENT 'Symptômes digestifs' AFTER `allergies`,
ADD COLUMN `bowel_frequency` VARCHAR(50) NULL COMMENT 'Fréquence des selles' AFTER `digestive_symptoms`,
ADD COLUMN `water_intake` DECIMAL(4,2) NULL COMMENT 'Consommation d\'eau en litres/jour' AFTER `bowel_frequency`,
ADD COLUMN `food_intolerances` TEXT NULL COMMENT 'Intolérances alimentaires' AFTER `water_intake`,

-- Informations spécifiques femmes
ADD COLUMN `is_pregnant` ENUM('no','yes') DEFAULT 'no' COMMENT 'État de grossesse' AFTER `gender`,
ADD COLUMN `pregnancy_months` TINYINT NULL COMMENT 'Mois de grossesse' AFTER `is_pregnant`,
ADD COLUMN `breastfeeding` ENUM('no','yes') DEFAULT 'no' COMMENT 'Allaitement' AFTER `pregnancy_months`,
ADD COLUMN `menstrual_cycle` VARCHAR(50) NULL COMMENT 'Cycle menstruel' AFTER `breastfeeding`,

-- Mensurations complémentaires
ADD COLUMN `waist_circumference` DECIMAL(5,2) NULL COMMENT 'Tour de taille en cm' AFTER `height`,
ADD COLUMN `hip_circumference` DECIMAL(5,2) NULL COMMENT 'Tour de hanches en cm' AFTER `waist_circumference`,
ADD COLUMN `neck_circumference` DECIMAL(5,2) NULL COMMENT 'Tour de cou en cm' AFTER `hip_circumference`,
ADD COLUMN `chest_circumference` DECIMAL(5,2) NULL COMMENT 'Tour de poitrine en cm' AFTER `neck_circumference`,
ADD COLUMN `arm_circumference` DECIMAL(5,2) NULL COMMENT 'Tour de bras en cm' AFTER `chest_circumference`,

-- Examens et bilans
ADD COLUMN `recent_exams` TEXT NULL COMMENT 'Examens médicaux récents' AFTER `medications`,
ADD COLUMN `last_blood_test_date` DATE NULL COMMENT 'Date dernier bilan sanguin' AFTER `recent_exams`,
ADD COLUMN `blood_test_results` TEXT NULL COMMENT 'Résultats analyses' AFTER `last_blood_test_date`;
```

## Organisation des sections du formulaire

### Section 1: Informations Personnelles et Démographiques
- Nom/Client (existant)
- **Civilité** (M., Mme, Mlle) - NOUVEAU
- Sexe (existant)
- Date de naissance (existant)
- **Profession** - NOUVEAU
- **Adresse complète** - NOUVEAU
- Téléphone (existant)
- Email (existant)
- Diététicien assigné (existant)
- Statut (existant)

### Section 2: Informations Spécifiques Femmes (conditionnelle)
*Apparaît uniquement si sexe = Femme*
- **Grossesse** (Oui/Non) - NOUVEAU
- **Mois de grossesse** (si oui) - NOUVEAU
- **Allaitement** (Oui/Non) - NOUVEAU
- **Cycle menstruel** (régulier/irrégulier) - NOUVEAU

### Section 3: Données Physiques & Mensurations
- Poids initial (existant)
- Poids objectif (existant)
- Taille (existant)
- **Tour de taille** - NOUVEAU
- **Tour de hanches** - NOUVEAU
- **Tour de cou** - NOUVEAU
- **Tour de poitrine** - NOUVEAU
- **Tour de bras** - NOUVEAU
- IMC (auto-calculé)
- Objectif (existant)

### Section 4: Antécédents & Historique Médical
- Pathologies actuelles (existant)
- **Antécédents familiaux** (diabète, HTA, obésité, etc.) - NOUVEAU
- Allergies (existant)
- Médicaments actuels (existant)
- **Compléments alimentaires** (vitamines, minéraux, etc.) - NOUVEAU
- **Examens médicaux récents** - NOUVEAU
- **Date dernier bilan sanguin** - NOUVEAU
- **Résultats d'analyses** (glycémie, cholestérol, etc.) - NOUVEAU

### Section 5: Système Digestif
- **Symptômes digestifs** (ballonnements, constipation, diarrhée, reflux, etc.) - NOUVEAU
- **Fréquence des selles** (par jour/semaine) - NOUVEAU
- **Intolérances alimentaires** (lactose, gluten, etc.) - NOUVEAU
- **Consommation d'eau** (litres/jour) - NOUVEAU
- Préférences alimentaires (existant)

### Section 6: Mode de Vie
- Niveau d'activité physique (existant)
- **Détails activité physique** (type, fréquence, durée) - NOUVEAU
- **Heures de sommeil** par nuit - NOUVEAU
- **Niveau de stress** (faible/modéré/élevé/très élevé) - NOUVEAU
- **Tabagisme** (non/oui/ancien fumeur) - NOUVEAU
- **Consommation d'alcool** (jamais/occasionnel/régulier/fréquent) - NOUVEAU
- Notes sur le mode de vie (existant)

### Section 7: Contact d'Urgence (existant)
- Nom du contact
- Téléphone

### Section 8: Documents Médicaux (existant)
- Upload de documents

## Suggestions professionnelles

### 1. **Validation automatique**
- Calcul automatique de l'IMC
- Calcul du rapport taille/hanches
- Alerte si valeurs anormales

### 2. **Interface intelligente**
- Champs conditionnels (grossesse n'apparaît que pour les femmes)
- Auto-complétion pour les pathologies courantes
- Suggestions pour les intolérances alimentaires

### 3. **Aide contextuelle**
- Info-bulles explicatives pour chaque champ
- Exemples de réponses
- Guides de mesure (comment mesurer le tour de taille, etc.)

### 4. **Score d'anamnèse**
- Indicateur de complétude du dossier (%)
- Liste des informations manquantes importantes

### 5. **Export et impression**
- Génération PDF de l'anamnèse complète
- Format professionnel pour partage avec autres praticiens

### 6. **Suivi temporel**
- Historique des modifications
- Comparaison des valeurs dans le temps
- Graphiques d'évolution

## Design recommandé

### Couleurs par section
- **Informations personnelles**: Bleu (#3498db)
- **Femmes**: Rose (#e91e63)
- **Mensurations**: Vert (#2ecc71)
- **Médical**: Rouge (#e74c3c)
- **Digestif**: Orange (#f39c12)
- **Mode de vie**: Violet (#9b59b6)
- **Urgence**: Orange foncé (#F3911D)
- **Documents**: Gris (#7f8c8d)

### Icons FontAwesome
- fa-user-circle: Informations personnelles
- fa-venus: Femmes
- fa-heartbeat: Mensurations
- fa-medkit: Médical
- fa-cutlery: Digestif
- fa-life-ring: Mode de vie
- fa-phone-square: Urgence
- fa-file-text: Documents
