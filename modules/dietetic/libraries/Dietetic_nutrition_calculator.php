<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Dietetic Nutrition Calculator Library
 *
 * Calculateurs nutritionnels professionnels basés sur des formules scientifiques validées
 *
 * @author Eric Gilles SAGNA (Lead Developer)
 * @website https://maestrodan.art
 * @version 1.0.0
 */
class Dietetic_nutrition_calculator
{
    /**
     * Niveaux d'activité physique (PAL - Physical Activity Level)
     * Basé sur les recommandations OMS/FAO
     */
    const ACTIVITY_SEDENTARY = 1.2;      // Sédentaire (peu ou pas d'exercice)
    const ACTIVITY_LIGHT = 1.375;        // Légère (exercice 1-3 jours/semaine)
    const ACTIVITY_MODERATE = 1.55;      // Modérée (exercice 3-5 jours/semaine)
    const ACTIVITY_ACTIVE = 1.725;       // Active (exercice 6-7 jours/semaine)
    const ACTIVITY_VERY_ACTIVE = 1.9;    // Très active (exercice intense quotidien)

    /**
     * Objectifs nutritionnels
     */
    const GOAL_WEIGHT_LOSS = 'weight_loss';           // Perte de poids
    const GOAL_WEIGHT_GAIN = 'weight_gain';           // Prise de poids
    const GOAL_MAINTENANCE = 'maintenance';           // Maintien
    const GOAL_MUSCLE_GAIN = 'muscle_gain';           // Prise de masse musculaire

    private $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
    }

    // ==================== MÉTABOLISME DE BASE (BMR) ====================

    /**
     * Calcul du Métabolisme de Base selon Harris-Benedict (Révisée 1984)
     *
     * Formule la plus utilisée historiquement
     *
     * @param float $weight Poids en kg
     * @param float $height Taille en cm
     * @param int $age Âge en années
     * @param string $gender 'male' ou 'female'
     * @return array ['bmr' => float, 'formula' => string]
     */
    public function calculate_bmr_harris_benedict($weight, $height, $age, $gender)
    {
        if ($gender === 'male') {
            // Hommes: BMR = 88.362 + (13.397 × poids) + (4.799 × taille) - (5.677 × âge)
            $bmr = 88.362 + (13.397 * $weight) + (4.799 * $height) - (5.677 * $age);
        } else {
            // Femmes: BMR = 447.593 + (9.247 × poids) + (3.098 × taille) - (4.330 × âge)
            $bmr = 447.593 + (9.247 * $weight) + (3.098 * $height) - (4.330 * $age);
        }

        return [
            'bmr' => round($bmr, 0),
            'formula' => 'Harris-Benedict (1984)',
            'description' => 'Formule historique, validée scientifiquement'
        ];
    }

    /**
     * Calcul du Métabolisme de Base selon Mifflin-St Jeor (1990)
     *
     * Formule la plus précise selon les études récentes
     * Recommandée par l'Academy of Nutrition and Dietetics
     *
     * @param float $weight Poids en kg
     * @param float $height Taille en cm
     * @param int $age Âge en années
     * @param string $gender 'male' ou 'female'
     * @return array ['bmr' => float, 'formula' => string]
     */
    public function calculate_bmr_mifflin_st_jeor($weight, $height, $age, $gender)
    {
        // BMR = (10 × poids) + (6.25 × taille) - (5 × âge) + s
        // s = +5 pour les hommes, -161 pour les femmes

        $bmr_base = (10 * $weight) + (6.25 * $height) - (5 * $age);

        if ($gender === 'male') {
            $bmr = $bmr_base + 5;
        } else {
            $bmr = $bmr_base - 161;
        }

        return [
            'bmr' => round($bmr, 0),
            'formula' => 'Mifflin-St Jeor (1990)',
            'description' => 'Formule la plus précise (recommandée)'
        ];
    }

    /**
     * Calcul du Métabolisme de Base selon Katch-McArdle
     *
     * Basée sur la masse maigre (plus précise si composition corporelle connue)
     *
     * @param float $lean_body_mass Masse maigre en kg
     * @return array ['bmr' => float, 'formula' => string]
     */
    public function calculate_bmr_katch_mcardle($lean_body_mass)
    {
        // BMR = 370 + (21.6 × masse maigre)
        $bmr = 370 + (21.6 * $lean_body_mass);

        return [
            'bmr' => round($bmr, 0),
            'formula' => 'Katch-McArdle',
            'description' => 'Basée sur la masse maigre (très précise)'
        ];
    }

    // ==================== DÉPENSE ÉNERGÉTIQUE TOTALE (TDEE) ====================

    /**
     * Calcul de la Dépense Énergétique Totale Quotidienne (TDEE)
     *
     * TDEE = BMR × Niveau d'activité physique
     *
     * @param float $bmr Métabolisme de base
     * @param float $activity_level Coefficient d'activité (constantes ACTIVITY_*)
     * @return array ['tdee' => float, 'activity_description' => string]
     */
    public function calculate_tdee($bmr, $activity_level)
    {
        $tdee = $bmr * $activity_level;

        $activity_descriptions = [
            self::ACTIVITY_SEDENTARY => 'Sédentaire',
            self::ACTIVITY_LIGHT => 'Légèrement actif',
            self::ACTIVITY_MODERATE => 'Modérément actif',
            self::ACTIVITY_ACTIVE => 'Actif',
            self::ACTIVITY_VERY_ACTIVE => 'Très actif'
        ];

        return [
            'tdee' => round($tdee, 0),
            'activity_level' => $activity_level,
            'activity_description' => $activity_descriptions[$activity_level] ?? 'Non défini'
        ];
    }

    /**
     * Calcul des besoins caloriques selon l'objectif
     *
     * @param float $tdee Dépense énergétique totale
     * @param string $goal Objectif (constantes GOAL_*)
     * @return array ['calories' => float, 'deficit_surplus' => int]
     */
    public function calculate_calorie_needs($tdee, $goal)
    {
        $adjustments = [
            self::GOAL_WEIGHT_LOSS => -500,      // Déficit de 500 kcal/jour (perte ~0.5kg/semaine)
            self::GOAL_MAINTENANCE => 0,          // Maintien
            self::GOAL_WEIGHT_GAIN => 300,        // Surplus de 300 kcal/jour (prise ~0.3kg/semaine)
            self::GOAL_MUSCLE_GAIN => 500         // Surplus de 500 kcal/jour (prise musculaire)
        ];

        $adjustment = $adjustments[$goal] ?? 0;
        $target_calories = $tdee + $adjustment;

        return [
            'calories' => round($target_calories, 0),
            'tdee' => round($tdee, 0),
            'deficit_surplus' => $adjustment,
            'goal' => $goal
        ];
    }

    // ==================== COMPOSITION CORPORELLE ====================

    /**
     * Calcul du pourcentage de masse grasse selon la formule de la Navy
     *
     * Méthode simple et accessible sans équipement spécialisé
     *
     * @param string $gender 'male' ou 'female'
     * @param float $height Taille en cm
     * @param float $waist Tour de taille en cm
     * @param float $neck Tour de cou en cm
     * @param float $hip Tour de hanches en cm (femmes uniquement)
     * @return array ['body_fat_percentage' => float, 'category' => string]
     */
    public function calculate_body_fat_navy($gender, $height, $waist, $neck, $hip = null)
    {
        if ($gender === 'male') {
            // Hommes: %MG = 495 / (1.0324 - 0.19077 × log10(taille - cou) + 0.15456 × log10(taille)) - 450
            $body_fat = 495 / (1.0324 - 0.19077 * log10($waist - $neck) + 0.15456 * log10($height)) - 450;
        } else {
            // Femmes: %MG = 495 / (1.29579 - 0.35004 × log10(taille + hanche - cou) + 0.22100 × log10(taille)) - 450
            if ($hip === null) {
                return ['error' => 'Tour de hanches requis pour les femmes'];
            }
            $body_fat = 495 / (1.29579 - 0.35004 * log10($waist + $hip - $neck) + 0.22100 * log10($height)) - 450;
        }

        return [
            'body_fat_percentage' => round($body_fat, 1),
            'formula' => 'US Navy',
            'category' => $this->get_body_fat_category($body_fat, $gender)
        ];
    }

    /**
     * Calcul de la masse grasse et masse maigre
     *
     * @param float $weight Poids total en kg
     * @param float $body_fat_percentage Pourcentage de masse grasse
     * @return array
     */
    public function calculate_body_composition($weight, $body_fat_percentage)
    {
        $fat_mass = ($weight * $body_fat_percentage) / 100;
        $lean_body_mass = $weight - $fat_mass;

        // Estimation de l'eau corporelle (environ 73% de la masse maigre)
        $body_water = $lean_body_mass * 0.73;

        // Estimation de la masse musculaire (environ 45-50% du poids corporel)
        $muscle_mass = $lean_body_mass * 0.50;

        return [
            'total_weight' => round($weight, 1),
            'fat_mass' => round($fat_mass, 1),
            'lean_body_mass' => round($lean_body_mass, 1),
            'body_water' => round($body_water, 1),
            'muscle_mass' => round($muscle_mass, 1),
            'body_fat_percentage' => round($body_fat_percentage, 1)
        ];
    }

    /**
     * Obtenir la catégorie de masse grasse
     *
     * @param float $body_fat Pourcentage de masse grasse
     * @param string $gender Sexe
     * @return string
     */
    private function get_body_fat_category($body_fat, $gender)
    {
        if ($gender === 'male') {
            if ($body_fat < 6) return 'Essentielle';
            if ($body_fat < 14) return 'Athlète';
            if ($body_fat < 18) return 'Fitness';
            if ($body_fat < 25) return 'Acceptable';
            return 'Obésité';
        } else {
            if ($body_fat < 14) return 'Essentielle';
            if ($body_fat < 21) return 'Athlète';
            if ($body_fat < 25) return 'Fitness';
            if ($body_fat < 32) return 'Acceptable';
            return 'Obésité';
        }
    }

    // ==================== MACRONUTRIMENTS ====================

    /**
     * Calcul de la répartition des macronutriments
     *
     * @param float $calories Apport calorique cible
     * @param string $goal Objectif nutritionnel
     * @param float $weight Poids en kg (pour le calcul des protéines)
     * @return array
     */
    public function calculate_macros($calories, $goal, $weight)
    {
        // Répartitions selon les objectifs
        $macro_ratios = [
            self::GOAL_WEIGHT_LOSS => [
                'protein_per_kg' => 2.0,  // 2g/kg pour préserver la masse musculaire
                'fat_percentage' => 25,    // 25% des calories en lipides
                // Le reste en glucides
            ],
            self::GOAL_MAINTENANCE => [
                'protein_per_kg' => 1.6,
                'fat_percentage' => 25,
            ],
            self::GOAL_WEIGHT_GAIN => [
                'protein_per_kg' => 1.8,
                'fat_percentage' => 25,
            ],
            self::GOAL_MUSCLE_GAIN => [
                'protein_per_kg' => 2.2,  // Protéines élevées pour la croissance musculaire
                'fat_percentage' => 20,
            ]
        ];

        $ratios = $macro_ratios[$goal] ?? $macro_ratios[self::GOAL_MAINTENANCE];

        // Calcul des protéines
        $protein_grams = $weight * $ratios['protein_per_kg'];
        $protein_calories = $protein_grams * 4; // 4 kcal/g

        // Calcul des lipides
        $fat_calories = $calories * ($ratios['fat_percentage'] / 100);
        $fat_grams = $fat_calories / 9; // 9 kcal/g

        // Calcul des glucides (le reste)
        $carbs_calories = $calories - $protein_calories - $fat_calories;
        $carbs_grams = $carbs_calories / 4; // 4 kcal/g

        return [
            'calories' => round($calories, 0),
            'protein' => [
                'grams' => round($protein_grams, 0),
                'calories' => round($protein_calories, 0),
                'percentage' => round(($protein_calories / $calories) * 100, 0)
            ],
            'carbs' => [
                'grams' => round($carbs_grams, 0),
                'calories' => round($carbs_calories, 0),
                'percentage' => round(($carbs_calories / $calories) * 100, 0)
            ],
            'fats' => [
                'grams' => round($fat_grams, 0),
                'calories' => round($fat_calories, 0),
                'percentage' => round(($fat_calories / $calories) * 100, 0)
            ],
            'fiber' => round($calories / 1000 * 14, 0) // Recommandation: 14g de fibres par 1000 kcal
        ];
    }

    // ==================== BESOINS EN EAU ====================

    /**
     * Calcul des besoins en eau quotidiens
     *
     * @param float $weight Poids en kg
     * @param float $activity_level Niveau d'activité
     * @return array
     */
    public function calculate_water_needs($weight, $activity_level = self::ACTIVITY_MODERATE)
    {
        // Formule de base: 35ml par kg de poids corporel
        $base_water = $weight * 35;

        // Ajustement selon l'activité physique
        if ($activity_level >= self::ACTIVITY_ACTIVE) {
            $base_water *= 1.2; // +20% pour les personnes actives
        }

        return [
            'daily_ml' => round($base_water, 0),
            'daily_liters' => round($base_water / 1000, 1),
            'glasses_250ml' => round($base_water / 250, 0)
        ];
    }

    // ==================== ANALYSE COMPLÈTE ====================

    /**
     * Analyse nutritionnelle complète d'un patient
     *
     * @param array $patient_data Données du patient
     * @return array Analyse complète
     */
    public function complete_nutrition_analysis($patient_data)
    {
        $weight = $patient_data['weight'];
        $height = $patient_data['height'];
        $age = $patient_data['age'];
        $gender = $patient_data['gender'];
        $activity_level = $patient_data['activity_level'] ?? self::ACTIVITY_MODERATE;
        $goal = $patient_data['goal'] ?? self::GOAL_MAINTENANCE;

        // 1. Métabolisme de base (2 formules pour comparaison)
        $bmr_harris = $this->calculate_bmr_harris_benedict($weight, $height, $age, $gender);
        $bmr_mifflin = $this->calculate_bmr_mifflin_st_jeor($weight, $height, $age, $gender);

        // On utilise Mifflin-St Jeor comme formule principale (plus précise)
        $bmr = $bmr_mifflin['bmr'];

        // 2. Dépense énergétique totale
        $tdee = $this->calculate_tdee($bmr, $activity_level);

        // 3. Besoins caloriques selon l'objectif
        $calorie_needs = $this->calculate_calorie_needs($tdee['tdee'], $goal);

        // 4. Répartition des macronutriments
        $macros = $this->calculate_macros($calorie_needs['calories'], $goal, $weight);

        // 5. Besoins en eau
        $water_needs = $this->calculate_water_needs($weight, $activity_level);

        // 6. Composition corporelle (si données disponibles)
        $body_composition = null;
        if (isset($patient_data['waist']) && isset($patient_data['neck'])) {
            $hip = isset($patient_data['hip']) ? $patient_data['hip'] : null;
            $body_fat = $this->calculate_body_fat_navy($gender, $height, $patient_data['waist'], $patient_data['neck'], $hip);

            if (!isset($body_fat['error'])) {
                $body_composition = $this->calculate_body_composition($weight, $body_fat['body_fat_percentage']);
                $body_composition['category'] = $body_fat['category'];
            }
        }

        return [
            'bmr' => [
                'value' => $bmr,
                'harris_benedict' => $bmr_harris,
                'mifflin_st_jeor' => $bmr_mifflin
            ],
            'tdee' => $tdee,
            'calorie_needs' => $calorie_needs,
            'macros' => $macros,
            'water_needs' => $water_needs,
            'body_composition' => $body_composition
        ];
    }
}
