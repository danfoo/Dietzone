<?php
/**
 * Widget de Recommandations Nutritionnelles Intelligentes
 *
 * Génère des recommandations personnalisées basées sur l'analyse du patient
 * Version avec gestion d'erreurs robuste
 */

defined('BASEPATH') or exit('No direct script access allowed');

// ==================== VÉRIFICATIONS DE DONNÉES ====================

// Vérifier que les données minimales existent
if (!isset($patient->weight) || !isset($patient->height) || $patient->height <= 0) {
    // Pas d'affichage si données insuffisantes (déjà affiché dans nutrition_analysis)
    return;
}

try {
    // Get patient data with safe defaults
    $weight = !empty($patient->current_weight) ? floatval($patient->current_weight) : floatval($patient->weight);
    $height = floatval($patient->height);
    $bmi = dietetic_calculate_bmi($weight, $height);

    if (!$bmi) {
        return; // Cannot calculate recommendations without BMI
    }

    $bmi_category = dietetic_get_bmi_category($bmi);

    // Generate recommendations based on patient profile
    $recommendations = [];
    $nutrition_tips = [];
    $health_alerts = [];

    // ==================== BMI-BASED RECOMMENDATIONS ====================

    if ($bmi < 18.5) {
        // Underweight
        $recommendations[] = [
            'icon' => 'fa-arrow-up',
            'color' => '#3498db',
            'title' => 'Prise de poids progressive',
            'description' => 'Augmenter progressivement l\'apport calorique de 300-500 kcal/jour pour une prise de poids saine d\'environ 0.5 kg par semaine.'
        ];
        $nutrition_tips[] = [
            'icon' => 'fa-apple',
            'tip' => 'Privilégier les aliments riches en nutriments : noix, avocats, huile d\'olive, poissons gras.'
        ];
        $nutrition_tips[] = [
            'icon' => 'fa-cutlery',
            'tip' => 'Augmenter la fréquence des repas : 5-6 petits repas par jour.'
        ];
    } elseif ($bmi >= 25 && $bmi < 30) {
        // Overweight
        $recommendations[] = [
            'icon' => 'fa-balance-scale',
            'color' => '#f39c12',
            'title' => 'Rééquilibrage alimentaire',
            'description' => 'Déficit calorique modéré de 300-500 kcal/jour pour une perte progressive et durable de 0.5 kg par semaine.'
        ];
        $nutrition_tips[] = [
            'icon' => 'fa-leaf',
            'tip' => 'Augmenter la consommation de légumes (minimum 300g par repas).'
        ];
        $nutrition_tips[] = [
            'icon' => 'fa-tint',
            'tip' => 'Boire 2-3 litres d\'eau par jour pour favoriser la satiété.'
        ];
        $health_alerts[] = [
            'type' => 'warning',
            'message' => 'Risque modéré de maladies métaboliques. Contrôle régulier du poids recommandé.'
        ];
    } elseif ($bmi >= 30) {
        // Obesity
        $recommendations[] = [
            'icon' => 'fa-exclamation-triangle',
            'color' => '#e74c3c',
            'title' => 'Perte de poids supervisée',
            'description' => 'Programme de perte de poids structuré avec déficit de 500-750 kcal/jour. Objectif : 5-10% du poids initial en 6 mois.'
        ];
        $nutrition_tips[] = [
            'icon' => 'fa-ban',
            'tip' => 'Éliminer les sucres rapides et les aliments ultra-transformés.'
        ];
        $nutrition_tips[] = [
            'icon' => 'fa-heartbeat',
            'tip' => 'Associer une activité physique progressive (30 min/jour minimum).'
        ];
        $health_alerts[] = [
            'type' => 'danger',
            'message' => 'Risque élevé de diabète type 2, hypertension et maladies cardiovasculaires. Suivi médical nécessaire.'
        ];
        $health_alerts[] = [
            'type' => 'info',
            'message' => 'Envisager une consultation avec un médecin pour exclure des causes médicales.'
        ];
    } else {
        // Normal weight
        $recommendations[] = [
            'icon' => 'fa-check-circle',
            'color' => '#27ae60',
            'title' => 'Maintien du poids optimal',
            'description' => 'Poids idéal atteint ! Maintenir un équilibre entre apport et dépense énergétique.'
        ];
        $nutrition_tips[] = [
            'icon' => 'fa-balance-scale',
            'tip' => 'Maintenir une alimentation équilibrée et variée.'
        ];
        $nutrition_tips[] = [
            'icon' => 'fa-running',
            'tip' => 'Pratiquer une activité physique régulière (150 min/semaine).'
        ];
    }

    // ==================== AGE-BASED RECOMMENDATIONS ====================

    if (!empty($patient->birth_date) && $patient->birth_date != '0000-00-00') {
        try {
            $birth_date = new DateTime($patient->birth_date);
            $today = new DateTime();
            $age = $birth_date->diff($today)->y;

            if ($age >= 50) {
                $nutrition_tips[] = [
                    'icon' => 'fa-medkit',
                    'tip' => 'Calcium & Vitamine D : Laitages, poissons gras, exposition solaire modérée.'
                ];
                $nutrition_tips[] = [
                    'icon' => 'fa-heart',
                    'tip' => 'Oméga-3 pour la santé cardiovasculaire : poissons gras 2-3x/semaine.'
                ];
            }

            if ($age < 30) {
                $nutrition_tips[] = [
                    'icon' => 'fa-bolt',
                    'tip' => 'Métabolisme élevé : Profiter de cette période pour établir de bonnes habitudes.'
                ];
            }
        } catch (Exception $e) {
            // Ignorer l'erreur de date
        }
    }

    // ==================== GENDER-BASED RECOMMENDATIONS ====================

    if (!empty($patient->gender)) {
        if ($patient->gender === 'female') {
            $nutrition_tips[] = [
                'icon' => 'fa-female',
                'tip' => 'Fer : Viandes rouges, légumineuses, légumes verts foncés. Associer avec vitamine C.'
            ];
            $nutrition_tips[] = [
                'icon' => 'fa-medkit',
                'tip' => 'Calcium : 1000-1200 mg/jour (laitages, amandes, sardines).'
            ];
        } else {
            $nutrition_tips[] = [
                'icon' => 'fa-male',
                'tip' => 'Protéines : 1.6-2.2 g/kg pour maintenir la masse musculaire.'
            ];
        }
    }

    // ==================== ACTIVITY-BASED RECOMMENDATIONS ====================

    $activity_level = !empty($patient->activity_level) ? $patient->activity_level : 'moderate';

    if ($activity_level === 'sedentary' || $activity_level === 'light') {
        $recommendations[] = [
            'icon' => 'fa-walking',
            'color' => '#95a5a6',
            'title' => 'Augmenter l\'activité physique',
            'description' => 'Activité physique faible détectée. Objectif : 30 minutes d\'activité modérée par jour.'
        ];
        $nutrition_tips[] = [
            'icon' => 'fa-clock-o',
            'tip' => 'Commencer progressivement : 10 minutes de marche 3x/jour.'
        ];
    }

    if ($activity_level === 'active' || $activity_level === 'very_active') {
        $nutrition_tips[] = [
            'icon' => 'fa-battery-full',
            'tip' => 'Glucides pré-entraînement : fruits, flocons d\'avoine 1-2h avant l\'effort.'
        ];
        $nutrition_tips[] = [
            'icon' => 'fa-recycle',
            'tip' => 'Récupération : protéines + glucides dans les 30 min post-exercice.'
        ];
    }

    // ==================== MEDICAL CONDITIONS ====================

    if (!empty($patient->medical_conditions)) {
        $conditions_lower = strtolower($patient->medical_conditions);

        if (strpos($conditions_lower, 'diabète') !== false || strpos($conditions_lower, 'diabete') !== false) {
            $health_alerts[] = [
                'type' => 'danger',
                'message' => 'DIABÈTE : Contrôler l\'index glycémique des aliments. Privilégier les glucides complexes.'
            ];
            $nutrition_tips[] = [
                'icon' => 'fa-bar-chart',
                'tip' => 'IG bas : céréales complètes, légumineuses, légumes verts.'
            ];
        }

        if (strpos($conditions_lower, 'hypertension') !== false) {
            $health_alerts[] = [
                'type' => 'warning',
                'message' => 'HYPERTENSION : Limiter le sodium à 2000 mg/jour. Éviter le sel de table.'
            ];
            $nutrition_tips[] = [
                'icon' => 'fa-ban',
                'tip' => 'Réduire les aliments transformés, charcuteries, fromages salés.'
            ];
        }

        if (strpos($conditions_lower, 'cholestérol') !== false || strpos($conditions_lower, 'cholesterol') !== false) {
            $health_alerts[] = [
                'type' => 'warning',
                'message' => 'CHOLESTÉROL : Limiter les graisses saturées et trans. Privilégier les oméga-3.'
            ];
            $nutrition_tips[] = [
                'icon' => 'fa-fish',
                'tip' => 'Poissons gras, huile d\'olive, noix, avocats pour les bonnes graisses.'
            ];
        }
    }

    // ==================== ALLERGIES ====================

    if (!empty($patient->allergies)) {
        $health_alerts[] = [
            'type' => 'danger',
            'message' => 'ALLERGIES : ' . htmlspecialchars($patient->allergies) . ' - Adapter le plan alimentaire en conséquence.'
        ];
    }

    // ==================== HYDRATION ====================

    $water_needs_ml = $weight * 35;
    if (!empty($patient->activity_level) && ($patient->activity_level === 'active' || $patient->activity_level === 'very_active')) {
        $water_needs_ml *= 1.2;
    }

    $recommendations[] = [
        'icon' => 'fa-tint',
        'color' => '#3498db',
        'title' => 'Hydratation optimale',
        'description' => 'Objectif : ' . round($water_needs_ml / 1000, 1) . ' litres d\'eau par jour (environ ' . round($water_needs_ml / 250) . ' verres).'
    ];

    $nutrition_tips[] = [
        'icon' => 'fa-clock-o',
        'tip' => 'Boire régulièrement tout au long de la journée, pas seulement quand on a soif.'
    ];

    // ==================== MEAL FREQUENCY ====================

    $recommendations[] = [
        'icon' => 'fa-calendar',
        'color' => '#9b59b6',
        'title' => 'Fréquence des repas',
        'description' => $bmi < 18.5 ? '5-6 petits repas par jour pour faciliter la prise de poids.' : '3 repas principaux + 2 collations saines.'
    ];

} catch (Exception $e) {
    // En cas d'erreur, ne pas afficher le widget
    return;
}

// Si aucune recommandation n'a été générée, ne pas afficher le widget
if (empty($recommendations) && empty($nutrition_tips)) {
    return;
}
?>

<style>
/* ============================================
   NUTRITION RECOMMENDATIONS WIDGET
   ============================================ */

.recommendations-container {
    margin-top: 20px;
}

.recommendation-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 16px;
    border-left: 5px solid;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}

.recommendation-card:hover {
    box-shadow: 0 4px 16px rgba(0,0,0,0.12);
    transform: translateX(3px);
}

.recommendation-header {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 12px;
}

.recommendation-icon {
    width: 56px;
    height: 56px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    color: white;
    flex-shrink: 0;
}

.recommendation-content h5 {
    margin: 0 0 8px 0;
    font-size: 18px;
    font-weight: 700;
    color: #2c3e50;
}

.recommendation-content p {
    margin: 0;
    font-size: 14px;
    color: #7f8c8d;
    line-height: 1.6;
}

/* Tips List */
.tips-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 12px;
    margin-top: 20px;
}

.tip-item {
    background: #f8f9fa;
    padding: 16px;
    border-radius: 10px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    transition: all 0.3s ease;
    border-left: 3px solid #01807B;
}

.tip-item:hover {
    background: #e9ecef;
    transform: translateX(3px);
}

.tip-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #01807B 0%, #015a57 100%);
    color: white;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}

.tip-text {
    flex: 1;
    font-size: 14px;
    color: #2c3e50;
    line-height: 1.5;
    font-weight: 500;
}

/* Health Alerts */
.health-alert {
    padding: 16px;
    border-radius: 10px;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 14px;
    font-weight: 600;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
}

.health-alert i {
    font-size: 24px;
}

.health-alert.danger {
    background: #fee;
    border-left: 4px solid #e74c3c;
    color: #c0392b;
}

.health-alert.warning {
    background: #fff3cd;
    border-left: 4px solid #f39c12;
    color: #856404;
}

.health-alert.info {
    background: #d1ecf1;
    border-left: 4px solid #17a2b8;
    color: #0c5460;
}

.health-alert.success {
    background: #d4edda;
    border-left: 4px solid #28a745;
    color: #155724;
}
</style>

<div class="recommendations-container">
    <!-- Section Header -->
    <div class="row">
        <div class="col-md-12">
            <h3 style="color: #01807B; font-weight: 700; margin-bottom: 20px;">
                <i class="fa fa-lightbulb-o"></i> Recommandations Nutritionnelles Personnalisées
            </h3>
        </div>
    </div>

    <!-- Health Alerts -->
    <?php if (!empty($health_alerts)): ?>
    <div class="row">
        <div class="col-md-12">
            <?php foreach ($health_alerts as $alert): ?>
            <div class="health-alert <?php echo $alert['type']; ?>">
                <i class="fa fa-exclamation-circle"></i>
                <span><?php echo $alert['message']; ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Main Recommendations -->
    <?php if (!empty($recommendations)): ?>
    <div class="row">
        <?php foreach ($recommendations as $rec): ?>
        <div class="col-md-6">
            <div class="recommendation-card" style="border-color: <?php echo $rec['color']; ?>;">
                <div class="recommendation-header">
                    <div class="recommendation-icon" style="background: <?php echo $rec['color']; ?>;">
                        <i class="fa <?php echo $rec['icon']; ?>"></i>
                    </div>
                    <div class="recommendation-content">
                        <h5><?php echo $rec['title']; ?></h5>
                        <p><?php echo $rec['description']; ?></p>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Nutrition Tips -->
    <?php if (!empty($nutrition_tips)): ?>
    <div class="row">
        <div class="col-md-12">
            <h4 style="color: #2c3e50; font-weight: 700; margin-top: 30px; margin-bottom: 20px;">
                <i class="fa fa-star"></i> Conseils Nutritionnels Ciblés
            </h4>
            <div class="tips-grid">
                <?php foreach ($nutrition_tips as $tip): ?>
                <div class="tip-item">
                    <div class="tip-icon">
                        <i class="fa <?php echo $tip['icon']; ?>"></i>
                    </div>
                    <div class="tip-text"><?php echo $tip['tip']; ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Scientific Note -->
    <div class="row">
        <div class="col-md-12">
            <div style="background: #e8f5e9; border-left: 4px solid #4caf50; border-radius: 8px; padding: 16px; margin-top: 30px; font-size: 13px; color: #2e7d32;">
                <i class="fa fa-info-circle"></i> <strong>Note professionnelle:</strong>
                Ces recommandations sont générées automatiquement sur la base de données scientifiques validées et de l'analyse du profil du patient.
                Elles constituent un guide général et doivent être adaptées au cas individuel lors de la consultation diététique.
                En cas de pathologies, un suivi médical est fortement recommandé.
            </div>
        </div>
    </div>
</div>
