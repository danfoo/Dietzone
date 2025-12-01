<?php
/**
 * Widget d'Analyse Nutritionnelle Avancée
 *
 * Affiche les calculs scientifiques de besoins nutritionnels du patient
 * Version avec gestion d'erreurs robuste
 */

defined('BASEPATH') or exit('No direct script access allowed');

// ==================== VÉRIFICATIONS DE DONNÉES ====================

// Déterminer le poids actuel : dernière mesure OU poids initial
$current_weight = null;
if (!empty($patient->latest_measurement) && !empty($patient->latest_measurement->weight)) {
    $current_weight = floatval($patient->latest_measurement->weight);
} elseif (!empty($patient->initial_weight)) {
    $current_weight = floatval($patient->initial_weight);
}

// Vérifier que les données minimales existent
if (!$current_weight || empty($patient->height) || $patient->height <= 0) {
    ?>
    <div class="alert alert-warning" style="margin-top: 20px;">
        <i class="fa fa-exclamation-triangle"></i>
        <strong>Analyse nutritionnelle indisponible</strong><br>
        Les données minimales requises (poids et taille) ne sont pas disponibles pour ce patient.
        Veuillez compléter le profil du patient pour activer l'analyse nutritionnelle.
    </div>
    <?php
    return;
}

// ==================== PRÉPARATION DES DONNÉES ====================

try {
    // Load nutrition calculator
    $CI = &get_instance();
    $CI->load->library('dietetic/dietetic_nutrition_calculator');

    // Prepare patient data with safe defaults
    $weight = $current_weight;
    $height = floatval($patient->height);

    // Calculer l'âge (par défaut 30 ans si date de naissance non disponible)
    $age = 30;
    if (!empty($patient->birth_date) && $patient->birth_date != '0000-00-00') {
        try {
            $birth_date = new DateTime($patient->birth_date);
            $today = new DateTime();
            $age = $birth_date->diff($today)->y;
        } catch (Exception $e) {
            $age = 30; // Valeur par défaut
        }
    }

    // Sexe (par défaut male si non défini)
    $gender = !empty($patient->gender) ? $patient->gender : 'male';

    // Niveau d'activité (par défaut modéré)
    $activity_level_map = [
        'sedentary' => Dietetic_nutrition_calculator::ACTIVITY_SEDENTARY,
        'light' => Dietetic_nutrition_calculator::ACTIVITY_LIGHT,
        'moderate' => Dietetic_nutrition_calculator::ACTIVITY_MODERATE,
        'active' => Dietetic_nutrition_calculator::ACTIVITY_ACTIVE,
        'very_active' => Dietetic_nutrition_calculator::ACTIVITY_VERY_ACTIVE,
    ];

    $patient_activity = !empty($patient->activity_level) ? $patient->activity_level : 'moderate';
    $activity_level = isset($activity_level_map[$patient_activity])
        ? $activity_level_map[$patient_activity]
        : Dietetic_nutrition_calculator::ACTIVITY_MODERATE;

    // Objectif (par défaut maintien)
    $goal_map = [
        'weight_loss' => Dietetic_nutrition_calculator::GOAL_WEIGHT_LOSS,
        'weight_gain' => Dietetic_nutrition_calculator::GOAL_WEIGHT_GAIN,
        'maintenance' => Dietetic_nutrition_calculator::GOAL_MAINTENANCE,
        'muscle_gain' => Dietetic_nutrition_calculator::GOAL_MUSCLE_GAIN,
    ];

    $patient_goal = !empty($patient->goal) ? $patient->goal : 'maintenance';
    $goal = isset($goal_map[$patient_goal])
        ? $goal_map[$patient_goal]
        : Dietetic_nutrition_calculator::GOAL_MAINTENANCE;

    // Prepare analysis data
    $patient_analysis_data = [
        'weight' => $weight,
        'height' => $height,
        'age' => $age,
        'gender' => $gender,
        'activity_level' => $activity_level,
        'goal' => $goal
    ];

    // Ajouter les mesures corporelles si disponibles (depuis latest_measurement)
    if (!empty($patient->latest_measurement)) {
        if (!empty($patient->latest_measurement->waist) && $patient->latest_measurement->waist > 0) {
            $patient_analysis_data['waist'] = floatval($patient->latest_measurement->waist);
        }
        if (!empty($patient->latest_measurement->neck) && $patient->latest_measurement->neck > 0) {
            $patient_analysis_data['neck'] = floatval($patient->latest_measurement->neck);
        }
        if (!empty($patient->latest_measurement->hips) && $patient->latest_measurement->hips > 0) {
            $patient_analysis_data['hip'] = floatval($patient->latest_measurement->hips);
        }
    }

    // Effectuer l'analyse complète
    $nutrition_analysis = $CI->dietetic_nutrition_calculator->complete_nutrition_analysis($patient_analysis_data);

} catch (Exception $e) {
    ?>
    <div class="alert alert-danger" style="margin-top: 20px;">
        <i class="fa fa-exclamation-circle"></i>
        <strong>Erreur lors de l'analyse nutritionnelle</strong><br>
        <?php echo htmlspecialchars($e->getMessage()); ?>
    </div>
    <?php
    return;
}

// Vérifier que l'analyse a réussi
if (empty($nutrition_analysis)) {
    ?>
    <div class="alert alert-warning" style="margin-top: 20px;">
        <i class="fa fa-exclamation-triangle"></i>
        <strong>Analyse nutritionnelle non disponible</strong><br>
        Impossible de calculer l'analyse nutritionnelle avec les données actuelles.
    </div>
    <?php
    return;
}

// ==================== AFFICHAGE ====================
?>

<style>
/* ============================================
   NUTRITION ANALYSIS WIDGET - Professional Design
   ============================================ */

.nutrition-analysis-container {
    margin-top: 20px;
}

.nutrition-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}

.nutrition-card:hover {
    box-shadow: 0 4px 16px rgba(0,0,0,0.12);
    transform: translateY(-2px);
}

.nutrition-card-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
    padding-bottom: 12px;
    border-bottom: 2px solid #f0f0f0;
}

.nutrition-card-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
}

.nutrition-card-title {
    flex: 1;
}

.nutrition-card-title h4 {
    margin: 0;
    font-size: 18px;
    font-weight: 700;
    color: #2c3e50;
}

.nutrition-card-title p {
    margin: 4px 0 0 0;
    font-size: 13px;
    color: #7f8c8d;
}

/* Metric Display */
.nutrition-metric {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #ecf0f1;
}

.nutrition-metric:last-child {
    border-bottom: none;
}

.nutrition-metric-label {
    font-weight: 600;
    color: #34495e;
    font-size: 14px;
}

.nutrition-metric-value {
    font-size: 18px;
    font-weight: 700;
    color: #01807B;
}

.nutrition-metric-unit {
    font-size: 13px;
    color: #7f8c8d;
    margin-left: 4px;
}

/* Big Number Display */
.big-number-display {
    text-align: center;
    padding: 24px;
    background: linear-gradient(135deg, #01807B 0%, #015a57 100%);
    border-radius: 12px;
    color: white;
    margin-bottom: 20px;
}

.big-number-value {
    font-size: 48px;
    font-weight: 800;
    line-height: 1;
    margin-bottom: 8px;
}

.big-number-label {
    font-size: 14px;
    opacity: 0.9;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* Macros Grid */
.macros-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    margin-top: 16px;
}

.macro-card {
    text-align: center;
    padding: 16px;
    border-radius: 10px;
    background: #f8f9fa;
    transition: all 0.3s ease;
}

.macro-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.macro-card.protein {
    background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
    color: white;
}

.macro-card.carbs {
    background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
    color: white;
}

.macro-card.fats {
    background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
    color: white;
}

.macro-value {
    font-size: 32px;
    font-weight: 800;
    line-height: 1;
    margin-bottom: 4px;
}

.macro-unit {
    font-size: 12px;
    opacity: 0.9;
}

.macro-label {
    font-size: 13px;
    font-weight: 600;
    margin-top: 8px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.macro-percentage {
    font-size: 11px;
    opacity: 0.8;
    margin-top: 4px;
}

/* Body Composition Grid */
.body-comp-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
    margin-top: 16px;
}

.body-comp-item {
    padding: 16px;
    background: #f8f9fa;
    border-radius: 10px;
    text-align: center;
    transition: all 0.3s ease;
}

.body-comp-item:hover {
    background: #e9ecef;
    transform: scale(1.05);
}

.body-comp-value {
    font-size: 24px;
    font-weight: 700;
    color: #2c3e50;
}

.body-comp-label {
    font-size: 12px;
    color: #7f8c8d;
    margin-top: 4px;
    font-weight: 600;
}

/* Formula Badge */
.formula-badge {
    display: inline-block;
    padding: 4px 12px;
    background: #ecf0f1;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    color: #7f8c8d;
    margin-top: 8px;
}

/* Info Alert */
.nutrition-info {
    background: #e8f5e9;
    border-left: 4px solid #4caf50;
    padding: 12px 16px;
    border-radius: 4px;
    margin-top: 16px;
    font-size: 13px;
    color: #2e7d32;
}

.nutrition-info i {
    margin-right: 8px;
}

/* Warning Alert */
.nutrition-warning {
    background: #fff3e0;
    border-left: 4px solid #ff9800;
    padding: 12px 16px;
    border-radius: 4px;
    margin-top: 16px;
    font-size: 13px;
    color: #e65100;
}

.nutrition-warning i {
    margin-right: 8px;
}

/* Responsive */
@media (max-width: 768px) {
    .macros-grid {
        grid-template-columns: 1fr;
    }

    .body-comp-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="nutrition-analysis-container">
    <!-- Section Header -->
    <div class="row">
        <div class="col-md-12">
            <h3 style="color: #01807B; font-weight: 700; margin-bottom: 20px;">
                <i class="fa fa-calculator"></i> Analyse Nutritionnelle Scientifique
            </h3>
        </div>
    </div>

    <div class="row">
        <!-- Column 1: BMR & TDEE -->
        <div class="col-md-4">
            <!-- BMR Card -->
            <div class="nutrition-card">
                <div class="nutrition-card-header">
                    <div class="nutrition-card-icon" style="background: linear-gradient(135deg, #01807B 0%, #015a57 100%);">
                        <i class="fa fa-heartbeat"></i>
                    </div>
                    <div class="nutrition-card-title">
                        <h4>Métabolisme de Base</h4>
                        <p>Énergie au repos</p>
                    </div>
                </div>

                <div class="big-number-display">
                    <div class="big-number-value"><?php echo number_format($nutrition_analysis['bmr']['value'], 0); ?></div>
                    <div class="big-number-label">kcal/jour</div>
                    <span class="formula-badge" style="background: rgba(255,255,255,0.2); color: white;">
                        <?php echo $nutrition_analysis['bmr']['mifflin_st_jeor']['formula']; ?>
                    </span>
                </div>

                <div class="nutrition-metric">
                    <span class="nutrition-metric-label">Harris-Benedict</span>
                    <span class="nutrition-metric-value">
                        <?php echo number_format($nutrition_analysis['bmr']['harris_benedict']['bmr'], 0); ?>
                        <span class="nutrition-metric-unit">kcal</span>
                    </span>
                </div>

                <div class="nutrition-info">
                    <i class="fa fa-info-circle"></i>
                    <strong>Métabolisme de base (BMR):</strong> Calories brûlées au repos pour les fonctions vitales.
                </div>
            </div>

            <!-- TDEE Card -->
            <div class="nutrition-card">
                <div class="nutrition-card-header">
                    <div class="nutrition-card-icon" style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);">
                        <i class="fa fa-bolt"></i>
                    </div>
                    <div class="nutrition-card-title">
                        <h4>Dépense Énergétique</h4>
                        <p>Avec activité physique</p>
                    </div>
                </div>

                <div class="big-number-display" style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);">
                    <div class="big-number-value"><?php echo number_format($nutrition_analysis['tdee']['tdee'], 0); ?></div>
                    <div class="big-number-label">kcal/jour (TDEE)</div>
                </div>

                <div class="nutrition-metric">
                    <span class="nutrition-metric-label">Niveau d'activité</span>
                    <span class="nutrition-metric-value" style="font-size: 14px; color: #3498db;">
                        <?php echo $nutrition_analysis['tdee']['activity_description']; ?>
                    </span>
                </div>

                <div class="nutrition-metric">
                    <span class="nutrition-metric-label">Coefficient</span>
                    <span class="nutrition-metric-value">
                        ×<?php echo $nutrition_analysis['tdee']['activity_level']; ?>
                    </span>
                </div>

                <div class="nutrition-info">
                    <i class="fa fa-info-circle"></i>
                    <strong>TDEE:</strong> Total Daily Energy Expenditure - Calories totales brûlées par jour.
                </div>
            </div>
        </div>

        <!-- Column 2: Calorie Needs & Macros -->
        <div class="col-md-4">
            <!-- Calorie Target Card -->
            <div class="nutrition-card">
                <div class="nutrition-card-header">
                    <div class="nutrition-card-icon" style="background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);">
                        <i class="fa fa-bullseye"></i>
                    </div>
                    <div class="nutrition-card-title">
                        <h4>Objectif Calorique</h4>
                        <p>Selon votre objectif</p>
                    </div>
                </div>

                <div class="big-number-display" style="background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);">
                    <div class="big-number-value"><?php echo number_format($nutrition_analysis['calorie_needs']['calories'], 0); ?></div>
                    <div class="big-number-label">kcal/jour cible</div>
                </div>

                <div class="nutrition-metric">
                    <span class="nutrition-metric-label">Déficit/Surplus</span>
                    <span class="nutrition-metric-value" style="color: <?php echo $nutrition_analysis['calorie_needs']['deficit_surplus'] < 0 ? '#e74c3c' : '#27ae60'; ?>;">
                        <?php echo $nutrition_analysis['calorie_needs']['deficit_surplus'] > 0 ? '+' : ''; ?>
                        <?php echo $nutrition_analysis['calorie_needs']['deficit_surplus']; ?>
                        <span class="nutrition-metric-unit">kcal/jour</span>
                    </span>
                </div>

                <?php if ($nutrition_analysis['calorie_needs']['deficit_surplus'] < 0): ?>
                <div class="nutrition-info">
                    <i class="fa fa-arrow-down"></i>
                    <strong>Perte de poids:</strong> Déficit de <?php echo abs($nutrition_analysis['calorie_needs']['deficit_surplus']); ?> kcal/jour = ~0.5 kg/semaine
                </div>
                <?php elseif ($nutrition_analysis['calorie_needs']['deficit_surplus'] > 0): ?>
                <div class="nutrition-info" style="background: #fff3e0; border-color: #ff9800; color: #e65100;">
                    <i class="fa fa-arrow-up"></i>
                    <strong>Prise de poids:</strong> Surplus de <?php echo $nutrition_analysis['calorie_needs']['deficit_surplus']; ?> kcal/jour
                </div>
                <?php else: ?>
                <div class="nutrition-info">
                    <i class="fa fa-check"></i>
                    <strong>Maintien:</strong> Apport = Dépense énergétique
                </div>
                <?php endif; ?>
            </div>

            <!-- Macros Card -->
            <div class="nutrition-card">
                <div class="nutrition-card-header">
                    <div class="nutrition-card-icon" style="background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%);">
                        <i class="fa fa-pie-chart"></i>
                    </div>
                    <div class="nutrition-card-title">
                        <h4>Macronutriments</h4>
                        <p>Répartition optimale</p>
                    </div>
                </div>

                <div class="macros-grid">
                    <!-- Protéines -->
                    <div class="macro-card protein">
                        <div class="macro-value"><?php echo $nutrition_analysis['macros']['protein']['grams']; ?></div>
                        <div class="macro-unit">grammes</div>
                        <div class="macro-label">Protéines</div>
                        <div class="macro-percentage"><?php echo $nutrition_analysis['macros']['protein']['percentage']; ?>%</div>
                    </div>

                    <!-- Glucides -->
                    <div class="macro-card carbs">
                        <div class="macro-value"><?php echo $nutrition_analysis['macros']['carbs']['grams']; ?></div>
                        <div class="macro-unit">grammes</div>
                        <div class="macro-label">Glucides</div>
                        <div class="macro-percentage"><?php echo $nutrition_analysis['macros']['carbs']['percentage']; ?>%</div>
                    </div>

                    <!-- Lipides -->
                    <div class="macro-card fats">
                        <div class="macro-value"><?php echo $nutrition_analysis['macros']['fats']['grams']; ?></div>
                        <div class="macro-unit">grammes</div>
                        <div class="macro-label">Lipides</div>
                        <div class="macro-percentage"><?php echo $nutrition_analysis['macros']['fats']['percentage']; ?>%</div>
                    </div>
                </div>

                <div class="nutrition-metric" style="margin-top: 16px;">
                    <span class="nutrition-metric-label"><i class="fa fa-leaf"></i> Fibres recommandées</span>
                    <span class="nutrition-metric-value">
                        <?php echo $nutrition_analysis['macros']['fiber']; ?>
                        <span class="nutrition-metric-unit">g/jour</span>
                    </span>
                </div>

                <div class="nutrition-info">
                    <i class="fa fa-info-circle"></i>
                    Répartition calculée selon votre objectif nutritionnel.
                </div>
            </div>
        </div>

        <!-- Column 3: Body Composition & Water -->
        <div class="col-md-4">
            <?php if (!empty($nutrition_analysis['body_composition'])): ?>
            <!-- Body Composition Card -->
            <div class="nutrition-card">
                <div class="nutrition-card-header">
                    <div class="nutrition-card-icon" style="background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);">
                        <i class="fa fa-user"></i>
                    </div>
                    <div class="nutrition-card-title">
                        <h4>Composition Corporelle</h4>
                        <p>Analyse détaillée</p>
                    </div>
                </div>

                <div class="big-number-display" style="background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);">
                    <div class="big-number-value"><?php echo $nutrition_analysis['body_composition']['body_fat_percentage']; ?>%</div>
                    <div class="big-number-label">Masse Grasse</div>
                    <span class="formula-badge" style="background: rgba(255,255,255,0.2); color: white;">
                        Formule US Navy
                    </span>
                </div>

                <div class="nutrition-metric">
                    <span class="nutrition-metric-label">Catégorie</span>
                    <span class="nutrition-metric-value" style="font-size: 14px; color: #f39c12;">
                        <?php echo $nutrition_analysis['body_composition']['category']; ?>
                    </span>
                </div>

                <div class="body-comp-grid">
                    <div class="body-comp-item">
                        <div class="body-comp-value"><?php echo $nutrition_analysis['body_composition']['fat_mass']; ?> kg</div>
                        <div class="body-comp-label">Masse Grasse</div>
                    </div>
                    <div class="body-comp-item">
                        <div class="body-comp-value"><?php echo $nutrition_analysis['body_composition']['lean_body_mass']; ?> kg</div>
                        <div class="body-comp-label">Masse Maigre</div>
                    </div>
                    <div class="body-comp-item">
                        <div class="body-comp-value"><?php echo $nutrition_analysis['body_composition']['muscle_mass']; ?> kg</div>
                        <div class="body-comp-label">Masse Musculaire</div>
                    </div>
                    <div class="body-comp-item">
                        <div class="body-comp-value"><?php echo $nutrition_analysis['body_composition']['body_water']; ?> kg</div>
                        <div class="body-comp-label">Eau Corporelle</div>
                    </div>
                </div>

                <div class="nutrition-info">
                    <i class="fa fa-info-circle"></i>
                    Calculé selon la formule de la US Navy (circonférences).
                </div>
            </div>
            <?php else: ?>
            <!-- Missing Data Alert -->
            <div class="nutrition-card">
                <div class="nutrition-card-header">
                    <div class="nutrition-card-icon" style="background: linear-gradient(135deg, #95a5a6 0%, #7f8c8d 100%);">
                        <i class="fa fa-user"></i>
                    </div>
                    <div class="nutrition-card-title">
                        <h4>Composition Corporelle</h4>
                        <p>Données manquantes</p>
                    </div>
                </div>

                <div class="nutrition-warning">
                    <i class="fa fa-exclamation-triangle"></i>
                    <strong>Mesures manquantes:</strong> Pour calculer la composition corporelle, veuillez ajouter les circonférences (taille, cou, hanches) dans les mesures du patient.
                </div>

                <div style="text-align: center; padding: 40px 20px;">
                    <i class="fa fa-ruler" style="font-size: 48px; color: #bdc3c7; margin-bottom: 16px;"></i>
                    <p style="color: #7f8c8d; font-size: 14px;">
                        Ajoutez les mesures corporelles pour débloquer cette analyse
                    </p>
                </div>
            </div>
            <?php endif; ?>

            <!-- Water Needs Card -->
            <div class="nutrition-card">
                <div class="nutrition-card-header">
                    <div class="nutrition-card-icon" style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);">
                        <i class="fa fa-tint"></i>
                    </div>
                    <div class="nutrition-card-title">
                        <h4>Besoins en Eau</h4>
                        <p>Hydratation quotidienne</p>
                    </div>
                </div>

                <div class="big-number-display" style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);">
                    <div class="big-number-value"><?php echo $nutrition_analysis['water_needs']['daily_liters']; ?></div>
                    <div class="big-number-label">Litres / jour</div>
                </div>

                <div class="nutrition-metric">
                    <span class="nutrition-metric-label"><i class="fa fa-glass"></i> Verres de 250ml</span>
                    <span class="nutrition-metric-value">
                        <?php echo $nutrition_analysis['water_needs']['glasses_250ml']; ?>
                        <span class="nutrition-metric-unit">verres</span>
                    </span>
                </div>

                <div class="nutrition-metric">
                    <span class="nutrition-metric-label">Volume total</span>
                    <span class="nutrition-metric-value">
                        <?php echo number_format($nutrition_analysis['water_needs']['daily_ml'], 0); ?>
                        <span class="nutrition-metric-unit">ml</span>
                    </span>
                </div>

                <div class="nutrition-info">
                    <i class="fa fa-lightbulb-o"></i>
                    <strong>Astuce:</strong> Buvez régulièrement tout au long de la journée, pas seulement quand vous avez soif.
                </div>
            </div>
        </div>
    </div>

    <!-- Scientific Note -->
    <div class="row">
        <div class="col-md-12">
            <div style="background: #ecf0f1; border-radius: 8px; padding: 16px; margin-top: 20px; font-size: 12px; color: #7f8c8d;">
                <i class="fa fa-flask"></i> <strong>Bases scientifiques:</strong>
                Formules validées par les recherches scientifiques en nutrition (Harris-Benedict 1984, Mifflin-St Jeor 1990, US Navy Body Fat).
                Ces calculs sont des estimations et peuvent varier selon les individus. Ils constituent une base solide pour l'élaboration de programmes nutritionnels personnalisés.
            </div>
        </div>
    </div>
</div>
