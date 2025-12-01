# 🔬 Guide du Calculateur Nutritionnel Avancé

## 📋 Vue d'ensemble

Le **Calculateur Nutritionnel Avancé** est un système professionnel basé sur des formules scientifiques validées, conçu pour fournir des analyses précises et des recommandations personnalisées aux patients.

**Date de création**: 1 Décembre 2025
**Auteur**: Eric Gilles SAGNA (Lead Developer)
**Version**: 1.0.0

---

## 🎯 Fonctionnalités Implémentées

### ✅ 1. Calcul Automatique des Besoins Caloriques

#### Formules Disponibles

1. **Harris-Benedict (Révisée 1984)**
   - Formule historique, largement utilisée
   - **Hommes**: BMR = 88.362 + (13.397 × poids) + (4.799 × taille) - (5.677 × âge)
   - **Femmes**: BMR = 447.593 + (9.247 × poids) + (3.098 × taille) - (4.330 × âge)

2. **Mifflin-St Jeor (1990)** ⭐ Recommandée
   - Formule la plus précise selon les études récentes
   - Recommandée par l'Academy of Nutrition and Dietetics
   - **Formule**: BMR = (10 × poids) + (6.25 × taille) - (5 × âge) + s
   - s = +5 pour les hommes, -161 pour les femmes

3. **Katch-McArdle**
   - Basée sur la masse maigre (composition corporelle)
   - La plus précise si la composition corporelle est connue
   - **Formule**: BMR = 370 + (21.6 × masse maigre)

#### Niveaux d'Activité Physique (PAL)

| Niveau | Coefficient | Description |
|--------|------------|-------------|
| Sédentaire | 1.2 | Peu ou pas d'exercice |
| Légère | 1.375 | Exercice 1-3 jours/semaine |
| Modérée | 1.55 | Exercice 3-5 jours/semaine |
| Active | 1.725 | Exercice 6-7 jours/semaine |
| Très active | 1.9 | Exercice intense quotidien |

#### Calcul de la TDEE (Total Daily Energy Expenditure)

```
TDEE = BMR × Coefficient d'activité
```

#### Ajustements selon l'Objectif

| Objectif | Ajustement | Résultat Attendu |
|----------|-----------|------------------|
| Perte de poids | -500 kcal/jour | ~0.5 kg/semaine |
| Maintien | 0 kcal/jour | Poids stable |
| Prise de poids | +300 kcal/jour | ~0.3 kg/semaine |
| Prise musculaire | +500 kcal/jour | Masse musculaire + |

---

### ✅ 2. Analyse de Composition Corporelle

#### Formule US Navy (Pourcentage de Masse Grasse)

**Avantages**:
- Simple et accessible
- Pas besoin d'équipement spécialisé
- Précision correcte (±3%)

**Mesures Requises**:
- Tour de taille
- Tour de cou
- Tour de hanches (femmes uniquement)
- Taille

**Formules**:

**Hommes**:
```
%MG = 495 / (1.0324 - 0.19077 × log10(taille - cou) + 0.15456 × log10(taille)) - 450
```

**Femmes**:
```
%MG = 495 / (1.29579 - 0.35004 × log10(taille + hanche - cou) + 0.22100 × log10(taille)) - 450
```

#### Catégories de Masse Grasse

**Hommes**:
| Catégorie | Pourcentage |
|-----------|------------|
| Essentielle | < 6% |
| Athlète | 6-13% |
| Fitness | 14-17% |
| Acceptable | 18-24% |
| Obésité | > 25% |

**Femmes**:
| Catégorie | Pourcentage |
|-----------|------------|
| Essentielle | < 14% |
| Athlète | 14-20% |
| Fitness | 21-24% |
| Acceptable | 25-31% |
| Obésité | > 32% |

#### Calculs Dérivés

- **Masse Grasse**: Poids × (%MG / 100)
- **Masse Maigre**: Poids - Masse Grasse
- **Eau Corporelle**: Masse Maigre × 0.73 (environ 73%)
- **Masse Musculaire**: Masse Maigre × 0.50 (environ 50%)

---

### ✅ 3. Répartition des Macronutriments

#### Principes de Répartition

La répartition est ajustée selon l'objectif:

**Perte de Poids**:
- **Protéines**: 2.0 g/kg (préserver la masse musculaire)
- **Lipides**: 25% des calories
- **Glucides**: Reste des calories

**Maintien**:
- **Protéines**: 1.6 g/kg
- **Lipides**: 25% des calories
- **Glucides**: Reste des calories

**Prise de Masse Musculaire**:
- **Protéines**: 2.2 g/kg (croissance musculaire)
- **Lipides**: 20% des calories
- **Glucides**: Reste des calories (énergie)

#### Fibres Alimentaires

**Recommandation**: 14g de fibres par 1000 kcal
- Exemple: 2000 kcal/jour → 28g de fibres

---

### ✅ 4. Besoins en Eau

#### Formule de Base

```
Besoin en eau (ml) = Poids (kg) × 35 ml/kg
```

#### Ajustements

- **Personnes actives**: +20% (activité physique intense)
- **Climat chaud**: +500-1000 ml/jour
- **Allaitement**: +700-1000 ml/jour

#### Recommandations Pratiques

- Répartir tout au long de la journée
- Boire avant d'avoir soif
- Augmenter lors d'exercice physique
- Surveiller la couleur des urines (indicateur)

---

### ✅ 5. Système de Recommandations Intelligentes

Le système génère automatiquement des recommandations personnalisées basées sur:

#### Critères d'Analyse

1. **IMC (Indice de Masse Corporelle)**
   - Sous-poids (< 18.5)
   - Normal (18.5-24.9)
   - Surpoids (25-29.9)
   - Obésité (≥ 30)

2. **Âge du Patient**
   - Besoins spécifiques selon l'âge
   - Calcium/Vitamine D après 50 ans

3. **Sexe**
   - Besoins en fer (femmes)
   - Besoins protéiques (hommes)

4. **Niveau d'Activité**
   - Recommandations nutritionnelles spécifiques
   - Timing des nutriments

5. **Conditions Médicales**
   - Diabète → Contrôle index glycémique
   - Hypertension → Réduction sodium
   - Cholestérol → Gestion des lipides

6. **Allergies**
   - Adaptations alimentaires nécessaires

#### Types de Recommandations

1. **Recommandations Principales** (Cards)
   - Objectifs caloriques
   - Stratégie nutritionnelle
   - Activité physique

2. **Conseils Nutritionnels Ciblés**
   - Aliments à privilégier
   - Fréquence des repas
   - Timing nutritionnel

3. **Alertes Santé**
   - Risques identifiés
   - Actions préventives
   - Suivi médical recommandé

---

## 📁 Architecture des Fichiers

```
modules/dietetic/
├── libraries/
│   └── Dietetic_nutrition_calculator.php    # Bibliothèque de calculs
│
├── views/admin/patients/widgets/
│   ├── nutrition_analysis.php               # Widget d'analyse
│   └── nutrition_recommendations.php        # Widget de recommandations
│
└── views/admin/patients/
    └── view.php                              # Page patient (intégration)
```

---

## 🔧 Utilisation de la Bibliothèque

### Chargement

```php
$this->load->library('dietetic/dietetic_nutrition_calculator');
```

### Exemples d'Utilisation

#### 1. Calcul du BMR

```php
// Méthode recommandée (Mifflin-St Jeor)
$bmr = $this->dietetic_nutrition_calculator->calculate_bmr_mifflin_st_jeor(
    $weight = 70,    // kg
    $height = 170,   // cm
    $age = 30,       // années
    $gender = 'male'
);

// Résultat: ['bmr' => 1650, 'formula' => 'Mifflin-St Jeor (1990)', 'description' => '...']
```

#### 2. Calcul de la TDEE

```php
$tdee = $this->dietetic_nutrition_calculator->calculate_tdee(
    $bmr = 1650,
    $activity_level = Dietetic_nutrition_calculator::ACTIVITY_MODERATE
);

// Résultat: ['tdee' => 2558, 'activity_level' => 1.55, 'activity_description' => 'Modérément actif']
```

#### 3. Besoins Caloriques selon l'Objectif

```php
$calories = $this->dietetic_nutrition_calculator->calculate_calorie_needs(
    $tdee = 2558,
    $goal = Dietetic_nutrition_calculator::GOAL_WEIGHT_LOSS
);

// Résultat: ['calories' => 2058, 'tdee' => 2558, 'deficit_surplus' => -500, 'goal' => 'weight_loss']
```

#### 4. Composition Corporelle

```php
$body_fat = $this->dietetic_nutrition_calculator->calculate_body_fat_navy(
    $gender = 'male',
    $height = 170,
    $waist = 85,
    $neck = 38,
    $hip = null  // Uniquement pour les femmes
);

// Résultat: ['body_fat_percentage' => 18.5, 'formula' => 'US Navy', 'category' => 'Fitness']
```

#### 5. Macronutriments

```php
$macros = $this->dietetic_nutrition_calculator->calculate_macros(
    $calories = 2058,
    $goal = Dietetic_nutrition_calculator::GOAL_WEIGHT_LOSS,
    $weight = 70
);

// Résultat:
// [
//     'protein' => ['grams' => 140, 'calories' => 560, 'percentage' => 27],
//     'carbs' => ['grams' => 232, 'calories' => 927, 'percentage' => 45],
//     'fats' => ['grams' => 64, 'calories' => 571, 'percentage' => 28],
//     'fiber' => 29
// ]
```

#### 6. Analyse Complète

```php
$analysis = $this->dietetic_nutrition_calculator->complete_nutrition_analysis([
    'weight' => 70,
    'height' => 170,
    'age' => 30,
    'gender' => 'male',
    'activity_level' => Dietetic_nutrition_calculator::ACTIVITY_MODERATE,
    'goal' => Dietetic_nutrition_calculator::GOAL_WEIGHT_LOSS,
    'waist' => 85,
    'neck' => 38
]);

// Retourne: BMR, TDEE, besoins caloriques, macros, eau, composition corporelle
```

---

## 🎨 Interface Utilisateur

### Widget d'Analyse Nutritionnelle

**Affichage en 3 colonnes**:

1. **Colonne 1**: BMR & TDEE
   - Métabolisme de base
   - Dépense énergétique totale
   - Formules comparées

2. **Colonne 2**: Calories & Macros
   - Objectif calorique
   - Répartition macronutriments (3 cards colorées)
   - Fibres recommandées

3. **Colonne 3**: Composition & Eau
   - Composition corporelle (si données disponibles)
   - Besoins en hydratation
   - Indicateurs visuels

### Widget de Recommandations

**Structure**:
1. Alertes santé (si applicable)
2. Recommandations principales (cards)
3. Conseils nutritionnels ciblés (grid)
4. Note scientifique

---

## 📊 Bases Scientifiques

### Références

1. **Mifflin MD, St Jeor ST, et al.** (1990). "A new predictive equation for resting energy expenditure in healthy individuals". *Am J Clin Nutr*. 51(2):241-7.

2. **Harris JA, Benedict FG** (1918, révisé 1984). "A Biometric Study of Basal Metabolism in Man". *Carnegie Institution of Washington*.

3. **US Navy Body Fat Formula** (Hodgdon & Beckett, 1984). "Prediction of percent body fat for U.S. Navy men and women".

4. **Academy of Nutrition and Dietetics** - Evidence Analysis Library.

5. **World Health Organization (WHO)** - FAO/WHO/UNU Expert Consultation on Energy and Protein Requirements.

---

## 🧪 Tests et Validation

### Scénarios de Test

1. **Patient en surpoids**
   - IMC > 25
   - Recommandations de perte de poids
   - Déficit calorique calculé

2. **Patient sous-poids**
   - IMC < 18.5
   - Recommandations de prise de poids
   - Surplus calorique calculé

3. **Patient avec conditions médicales**
   - Diabète, hypertension, cholestérol
   - Alertes santé affichées
   - Recommandations adaptées

4. **Patient sans mesures corporelles**
   - Composition corporelle non calculable
   - Message informatif affiché
   - Autres calculs disponibles

### Validation des Formules

Toutes les formules ont été validées avec des cas de test:
- Valeurs normales
- Valeurs extrêmes
- Comparaison avec calculateurs externes

---

## 🚀 Prochaines Améliorations Possibles

1. **Formules additionnelles**
   - Formule de Cunningham
   - Formule de l'OMS

2. **Analyse avancée**
   - Score de risque cardiovasculaire
   - Calcul du métabolisme adaptatif
   - Prédiction de perte/gain de poids

3. **Visualisations**
   - Graphiques de répartition macros
   - Timeline de progression prédite
   - Comparaison objectif/réel

4. **Export**
   - PDF du plan nutritionnel complet
   - Rapport détaillé pour le patient
   - Graphiques imprimables

---

## 💡 Bonnes Pratiques

### Pour les Diététiciens

1. **Vérifier les données d'entrée**
   - Poids, taille, âge exacts
   - Niveau d'activité réaliste
   - Mesures corporelles précises

2. **Interpréter les résultats**
   - Les formules donnent des estimations
   - Ajuster selon la réponse individuelle
   - Suivre l'évolution sur 2-4 semaines

3. **Utiliser avec le patient**
   - Expliquer les calculs
   - Impliquer dans les objectifs
   - Réévaluer régulièrement

### Pour les Développeurs

1. **Maintenance**
   - Garder les formules à jour
   - Vérifier les sources scientifiques
   - Tester après chaque modification

2. **Performance**
   - Les calculs sont légers
   - Pas de cache nécessaire
   - Calcul à la demande

3. **Extensibilité**
   - Structure modulaire
   - Facile d'ajouter de nouvelles formules
   - Widgets indépendants

---

## 📞 Support

Pour toute question ou amélioration:
- Consulter ce guide
- Vérifier les commentaires dans le code
- Contacter le développeur

---

**Développé avec ❤️ par Eric Gilles SAGNA**
**Lead Developer - Expertise Perfex CRM & Diététique**
**https://maestrodan.art**
