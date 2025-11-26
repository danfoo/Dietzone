<?php
/**
 * Script de vérification des champs d'anamnèse dans la table patients
 * URL d'accès: https://votredomaine.com/admin/dietetic/check_anamnesis_fields
 */

defined('BASEPATH') or exit('No direct script access allowed');

// Get database instance
$CI =& get_instance();
$CI->load->database();

echo "<!DOCTYPE html>";
echo "<html><head><meta charset='UTF-8'><title>Vérification Migration Anamnèse</title>";
echo "<style>
body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
.container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
h2 { color: #01807B; border-bottom: 3px solid #01807B; padding-bottom: 10px; }
h3 { color: #333; margin-top: 30px; }
.status-good { color: green; font-weight: bold; font-size: 18px; }
.status-bad { color: red; font-weight: bold; font-size: 18px; }
table { width: 100%; border-collapse: collapse; margin: 20px 0; }
table th { background: #01807B; color: white; padding: 12px; text-align: left; }
table td { padding: 10px; border-bottom: 1px solid #ddd; }
table tr:hover { background: #f9f9f9; }
.highlight { background: #d4edda !important; }
.missing { background: #f8d7da !important; }
.section { background: #e8f4f3; padding: 15px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #01807B; }
.section-title { font-weight: bold; color: #01807B; font-size: 16px; margin-bottom: 10px; }
ul { list-style-type: none; padding: 0; }
ul li { padding: 5px 0; padding-left: 20px; }
ul li:before { content: '✗ '; color: red; font-weight: bold; }
.btn { display: inline-block; padding: 12px 24px; background: #01807B; color: white; text-decoration: none; border-radius: 5px; margin: 10px 5px 10px 0; font-weight: bold; }
.btn:hover { background: #015a57; }
.btn-danger { background: #dc3545; }
.btn-danger:hover { background: #c82333; }
pre { background: #f5f5f5; padding: 15px; border-radius: 5px; overflow-x: auto; border: 1px solid #ddd; }
.count { background: #01807B; color: white; padding: 5px 10px; border-radius: 20px; font-size: 14px; }
</style></head><body>";

echo "<div class='container'>";
echo "<h2>🔍 Vérification des Champs d'Anamnèse - Table Patients</h2>";

// Liste complète des champs requis organisés par section
$required_fields = [
    'Section 1: Informations Personnelles' => [
        'title' => 'Civilité (M./Mme/Mlle)',
        'occupation' => 'Profession',
        'work_type' => 'Type de travail',
        'address' => 'Adresse complète',
    ],
    'Section 1.5: Informations Spécifiques Femmes' => [
        'is_pregnant' => 'Grossesse en cours',
        'pregnancy_months' => 'Mois de grossesse',
        'breastfeeding' => 'Allaitement',
        'menstrual_cycle' => 'Cycle menstruel',
    ],
    'Section 2: Données Physiques & Mensurations' => [
        'waist_circumference' => 'Tour de taille',
        'hip_circumference' => 'Tour de hanches',
        'neck_circumference' => 'Tour de cou',
        'chest_circumference' => 'Tour de poitrine',
        'arm_circumference' => 'Tour de bras',
        'thigh_circumference' => 'Tour de cuisse',
        'calf_circumference' => 'Tour de mollet',
        'physical_activity_details' => 'Détails activité physique',
    ],
    'Section 3: Antécédents & Historique Médical' => [
        'supplements' => 'Compléments alimentaires & vitamines',
        'recent_blood_work' => 'Dernières analyses sanguines',
        'weight_history' => 'Historique de poids',
        'previous_diets' => 'Régimes précédents',
        'weight_gain_triggers' => 'Facteurs de prise de poids',
        'family_history' => 'Maladies familiales',
        'surgeries' => 'Chirurgies & hospitalisations',
        'sleep_hours' => 'Heures de sommeil/nuit',
        'sleep_quality' => 'Qualité du sommeil',
        'stress_level' => 'Niveau de stress',
        'mental_health' => 'Santé mentale',
        'stress_eating' => 'Alimentation émotionnelle',
        'eating_disorders_history' => 'Historique troubles alimentaires',
    ],
    'Section 4: Système Digestif & Intolérances' => [
        'digestive_issues' => 'Problèmes digestifs',
        'bowel_frequency' => 'Fréquence des selles',
        'water_intake' => 'Consommation d\'eau',
        'digestive_details' => 'Détails problèmes digestifs',
        'lactose_intolerance' => 'Intolérance au lactose',
        'gluten_intolerance' => 'Intolérance au gluten',
        'fructose_intolerance' => 'Intolérance au fructose',
        'fodmap_sensitivity' => 'Sensibilité aux FODMAPs',
        'histamine_intolerance' => 'Intolérance à l\'histamine',
        'caffeine_sensitivity' => 'Sensibilité à la caféine',
        'food_dislikes' => 'Aliments non tolérés',
        'favorite_foods' => 'Aliments favoris',
        'cultural_food_preferences' => 'Préférences culturelles',
        'cooking_skills' => 'Compétences culinaires',
    ],
    'Section 5: Mode de Vie Détaillé' => [
        'meals_per_day' => 'Nombre de repas/jour',
        'snacks_per_day' => 'Collations/jour',
        'breakfast_time' => 'Heure petit-déjeuner',
        'dinner_time' => 'Heure dîner',
        'eating_speed' => 'Vitesse d\'alimentation',
        'eating_environment' => 'Environnement de repas',
        'meal_preparation' => 'Préparation des repas',
        'typical_day_diet' => 'Journée alimentaire typique',
        'coffee_per_day' => 'Cafés/jour',
        'tea_per_day' => 'Thés/jour',
        'soda_per_week' => 'Sodas/semaine',
        'alcohol_per_week' => 'Verres d\'alcool/semaine',
        'sweet_cravings' => 'Envies de sucré',
        'salt_preference' => 'Préférence pour le sel',
        'smoking' => 'Tabagisme',
        'time_for_cooking' => 'Temps pour cuisiner',
        'budget_level' => 'Niveau de budget',
        'barriers_to_change' => 'Obstacles au changement',
        'motivation_level' => 'Niveau de motivation',
    ],
];

// Récupérer toutes les colonnes existantes
$query = $CI->db->query("SHOW COLUMNS FROM " . db_prefix() . "dietic_patients");
$columns = $query->result();

$existing_columns = [];
foreach ($columns as $column) {
    $existing_columns[] = $column->Field;
}

// Compter les champs manquants
$all_required = [];
foreach ($required_fields as $section => $fields) {
    foreach ($fields as $field => $description) {
        $all_required[$field] = $description;
    }
}

$missing_fields_all = [];
foreach ($all_required as $field => $description) {
    if (!in_array($field, $existing_columns)) {
        $missing_fields_all[$field] = $description;
    }
}

// Afficher le statut global
echo "<h3>📊 Statut Global</h3>";
$total_required = count($all_required);
$total_existing = $total_required - count($missing_fields_all);
$percentage = round(($total_existing / $total_required) * 100, 1);

echo "<div class='section'>";
echo "<p><strong>Total champs requis:</strong> <span class='count'>{$total_required}</span></p>";
echo "<p><strong>Champs existants:</strong> <span class='count'>{$total_existing}</span></p>";
echo "<p><strong>Champs manquants:</strong> <span class='count'>" . count($missing_fields_all) . "</span></p>";
echo "<p><strong>Progression:</strong> <span class='count'>{$percentage}%</span></p>";
echo "</div>";

if (empty($missing_fields_all)) {
    echo "<p class='status-good'>✅ EXCELLENT ! Tous les champs d'anamnèse sont présents dans la base de données !</p>";
    echo "<p>Le formulaire d'anamnèse est prêt à l'utilisation.</p>";
} else {
    echo "<p class='status-bad'>⚠️ ATTENTION ! " . count($missing_fields_all) . " champs manquent dans la base de données.</p>";
    echo "<p>Vous devez appliquer la migration pour ajouter ces champs.</p>";

    echo "<div style='margin: 20px 0;'>";
    echo "<a href='" . admin_url('dietetic/apply_anamnesis_migration') . "' class='btn' onclick='return confirm(\"Êtes-vous sûr de vouloir appliquer cette migration ? Cette opération modifiera la structure de la base de données.\");'>🚀 Appliquer la Migration</a>";
    echo "</div>";
}

// Afficher les détails par section
echo "<h3>📋 Détails par Section</h3>";

foreach ($required_fields as $section => $fields) {
    $missing_in_section = [];

    foreach ($fields as $field => $description) {
        if (!in_array($field, $existing_columns)) {
            $missing_in_section[$field] = $description;
        }
    }

    $section_total = count($fields);
    $section_existing = $section_total - count($missing_in_section);
    $section_percentage = round(($section_existing / $section_total) * 100, 1);

    echo "<div class='section'>";
    echo "<div class='section-title'>{$section}</div>";
    echo "<p><strong>Progression:</strong> {$section_existing}/{$section_total} ({$section_percentage}%)</p>";

    if (empty($missing_in_section)) {
        echo "<p style='color: green;'>✅ Tous les champs de cette section sont présents</p>";
    } else {
        echo "<p style='color: red;'>❌ Champs manquants dans cette section:</p>";
        echo "<ul>";
        foreach ($missing_in_section as $field => $description) {
            echo "<li><strong>{$field}</strong> - {$description}</li>";
        }
        echo "</ul>";
    }
    echo "</div>";
}

// Afficher le tableau complet des colonnes
echo "<h3>📊 Toutes les Colonnes de la Table</h3>";
echo "<table>";
echo "<tr><th>Nom du Champ</th><th>Type</th><th>Null</th><th>Default</th><th>Statut</th></tr>";
foreach ($columns as $column) {
    $is_new_field = array_key_exists($column->Field, $all_required);
    $row_class = $is_new_field ? 'highlight' : '';

    echo "<tr class='{$row_class}'>";
    echo "<td><strong>" . $column->Field . "</strong>";
    if ($is_new_field) {
        echo " <span style='background: #01807B; color: white; padding: 2px 6px; border-radius: 3px; font-size: 11px;'>ANAMNÈSE</span>";
    }
    echo "</td>";
    echo "<td>" . $column->Type . "</td>";
    echo "<td>" . $column->Null . "</td>";
    echo "<td>" . ($column->Default ?? 'NULL') . "</td>";
    echo "<td>";
    if ($is_new_field) {
        echo "<span style='color: green;'>✓ Présent</span>";
    } else {
        echo "<span style='color: #666;'>• Existant</span>";
    }
    echo "</td>";
    echo "</tr>";
}
echo "</table>";

// Boutons d'action
echo "<h3>🛠️ Actions</h3>";
echo "<div style='margin: 20px 0;'>";

if (!empty($missing_fields_all)) {
    echo "<a href='" . admin_url('dietetic/apply_anamnesis_migration') . "' class='btn' onclick='return confirm(\"Êtes-vous sûr de vouloir appliquer cette migration ?\");'>🚀 Appliquer la Migration Maintenant</a>";
}

echo "<a href='" . admin_url('dietetic/patients') . "' class='btn'>📋 Retour aux Patients</a>";
echo "<a href='javascript:window.location.reload();' class='btn'>🔄 Rafraîchir</a>";
echo "</div>";

echo "<div style='margin-top: 30px; padding: 15px; background: #f9f9f9; border-radius: 5px; border-left: 4px solid #666;'>";
echo "<p style='color: #666; font-size: 13px; margin: 0;'>";
echo "<strong>Note:</strong> Cette migration ajoute 60+ nouveaux champs pour un suivi d'anamnèse complet et professionnel. ";
echo "Les champs incluent les informations personnelles, données physiques, antécédents médicaux, intolérances alimentaires, et mode de vie détaillé.";
echo "</p>";
echo "</div>";

echo "</div>"; // container
echo "</body></html>";
