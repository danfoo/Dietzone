<?php

defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . 'libraries/pdf/App_pdf.php');

class Dietetic_pdf extends App_pdf
{
    protected $CI;

    public function __construct()
    {
        parent::__construct();
        $this->CI = &get_instance();
        $this->CI->load->helper('dietetic/dietetic');
    }

    /**
     * Prepare PDF - Required abstract method from App_pdf
     */
    public function prepare($data)
    {
        // No specific preparation needed for dietetic PDFs
        return $data;
    }

    /**
     * Get file path - Required abstract method from App_pdf
     */
    public function file_path()
    {
        // Return empty as we output directly to browser
        return '';
    }

    /**
     * Get type - Required abstract method from App_pdf
     */
    public function type()
    {
        return 'dietetic';
    }

    /**
     * Generate meal plan PDF
     *
     * @param array $data
     */
    public function generate_meal_plan_pdf($data)
    {
        $meal_plan = $data['meal_plan'];
        $program = $data['program'];
        $patient = $data['patient'];
        $meals_by_day = $data['meals_by_day'];

        // Create PDF
        $pdf = new App_pdf();

        // Set document properties
        $pdf->SetTitle(_l('dietetic_meal_plan') . ' - ' . $meal_plan->plan_name);
        $pdf->SetAuthor(get_option('companyname'));

        // Add page
        $pdf->AddPage();

        // Logo
        $company_logo = get_option('company_logo');
        if ($company_logo && file_exists(FCPATH . 'uploads/company/' . $company_logo)) {
            $pdf->Image(FCPATH . 'uploads/company/' . $company_logo, 10, 10, 30);
        }

        // Header
        $pdf->SetFont('Arial', 'B', 20);
        $pdf->Cell(0, 10, _l('dietetic_meal_plan'), 0, 1, 'C');
        $pdf->Ln(5);

        // Patient and program info
        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(50, 6, _l('dietetic_patient') . ':', 0, 0);
        $pdf->SetFont('Arial', 'B', 11);
        $client_name = (isset($patient->client) && isset($patient->client->company)) ? $patient->client->company : 'N/A';
        $pdf->Cell(0, 6, $client_name, 0, 1);

        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(50, 6, _l('dietetic_program') . ':', 0, 0);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(0, 6, $program->program_name, 0, 1);

        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(50, 6, _l('dietetic_week') . ':', 0, 0);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(0, 6, $meal_plan->week_number, 0, 1);

        $pdf->Ln(5);

        // Daily targets
        if ($program->daily_calories || $program->daily_protein) {
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 8, _l('dietetic_daily_targets'), 0, 1);

            $pdf->SetFont('Arial', '', 10);

            if ($program->daily_calories) {
                $pdf->Cell(0, 5, _l('dietetic_calories') . ': ' . $program->daily_calories . ' kcal', 0, 1);
            }
            if ($program->daily_protein) {
                $pdf->Cell(0, 5, _l('dietetic_protein') . ': ' . dietetic_format_nutrition($program->daily_protein), 0, 1);
            }
            if ($program->daily_carbs) {
                $pdf->Cell(0, 5, _l('dietetic_carbs') . ': ' . dietetic_format_nutrition($program->daily_carbs), 0, 1);
            }
            if ($program->daily_fats) {
                $pdf->Cell(0, 5, _l('dietetic_fats') . ': ' . dietetic_format_nutrition($program->daily_fats), 0, 1);
            }

            $pdf->Ln(5);
        }

        // Meals by day
        foreach ($meals_by_day as $day => $meals) {
            if (empty($meals)) {
                continue;
            }

            // New page for each day
            if ($day > 1) {
                $pdf->AddPage();
            }

            // Day header
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->SetFillColor(52, 152, 219);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(0, 10, dietetic_get_day_name($day), 0, 1, 'C', true);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Ln(3);

            foreach ($meals as $meal) {
                // Meal type header
                $pdf->SetFont('Arial', 'B', 12);
                $pdf->SetFillColor(230, 230, 230);
                $pdf->Cell(0, 8, ucfirst(str_replace('_', ' ', $meal->meal_type)) . ($meal->meal_time ? ' - ' . substr($meal->meal_time, 0, 5) : ''), 0, 1, 'L', true);

                if ($meal->meal_name) {
                    $pdf->SetFont('Arial', 'I', 10);
                    $pdf->Cell(0, 5, $meal->meal_name, 0, 1);
                }

                // Foods
                if (!empty($meal->foods)) {
                    $pdf->SetFont('Arial', '', 9);

                    foreach ($meal->foods as $food) {
                        $ratio = $food->quantity / $food->serving_size;
                        $calories = round($food->calories * $ratio);

                        $pdf->Cell(5, 5, '-', 0, 0);
                        $pdf->Cell(80, 5, $food->food_name, 0, 0);
                        $pdf->Cell(40, 5, $food->quantity . ' ' . $food->unit, 0, 0);
                        $pdf->Cell(0, 5, $calories . ' kcal', 0, 1);
                    }
                }

                // Instructions
                if ($meal->instructions) {
                    $pdf->SetFont('Arial', 'I', 9);
                    $pdf->MultiCell(0, 4, $meal->instructions);
                }

                // Nutrition totals for meal
                if (!empty($meal->foods)) {
                    $meal_calories = 0;
                    $meal_protein = 0;
                    $meal_carbs = 0;
                    $meal_fats = 0;

                    foreach ($meal->foods as $food) {
                        $ratio = $food->quantity / $food->serving_size;
                        $meal_calories += $food->calories * $ratio;
                        $meal_protein += $food->protein * $ratio;
                        $meal_carbs += $food->carbs * $ratio;
                        $meal_fats += $food->fats * $ratio;
                    }

                    $pdf->SetFont('Arial', 'B', 9);
                    $pdf->Cell(0, 5, sprintf(
                        '%s: %.0f kcal | P: %.1fg | C: %.1fg | F: %.1fg',
                        _l('dietetic_total'),
                        $meal_calories,
                        $meal_protein,
                        $meal_carbs,
                        $meal_fats
                    ), 0, 1);
                }

                $pdf->Ln(3);
            }
        }

        // Instructions/Notes
        if ($program->instructions || $meal_plan->notes) {
            $pdf->AddPage();

            $pdf->SetFont('Arial', 'B', 14);
            $pdf->Cell(0, 10, _l('dietetic_instructions'), 0, 1);

            $pdf->SetFont('Arial', '', 10);

            if ($program->instructions) {
                $pdf->MultiCell(0, 5, $program->instructions);
                $pdf->Ln(3);
            }

            if ($meal_plan->notes) {
                $pdf->MultiCell(0, 5, $meal_plan->notes);
            }
        }

        // Footer on all pages
        $pdf->SetY(-20);
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(0, 10, get_option('companyname') . ' | ' . _l('dietetic_generated_on') . ' ' . date('Y-m-d H:i'), 0, 0, 'C');

        // Output
        $filename = 'plan_alimentaire_' . $meal_plan->id . '_' . date('Ymd') . '.pdf';

        // Ensure upload directory exists
        $upload_dir = dietetic_upload_path('meal_plans');
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        // Save to uploads folder
        $save_path = $upload_dir . '/' . $filename;
        try {
            $pdf->Output($save_path, 'F');

            // Update meal plan with PDF path
            $this->CI->load->model('dietetic/dietetic_meal_plans_model');
            $this->CI->dietetic_meal_plans_model->update($meal_plan->id, [
                'pdf_path' => 'meal_plans/' . $filename,
            ]);
        } catch (Exception $e) {
            log_activity('PDF generation error: ' . $e->getMessage());
        }

        // Download
        $pdf->Output($filename, 'D');
    }

    /**
     * Generate consultation report PDF
     *
     * @param object $consultation
     */
    public function generate_consultation_report($consultation)
    {
        $pdf = new App_pdf();

        $pdf->SetTitle(_l('dietetic_consultation_report'));
        $pdf->AddPage();

        // Header
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->Cell(0, 10, _l('dietetic_consultation_report'), 0, 1, 'C');
        $pdf->Ln(5);

        // Consultation details
        $pdf->SetFont('Arial', '', 11);

        $details = [
            _l('dietetic_date') => _dt($consultation->consultation_date),
            _l('dietetic_patient') => $consultation->client_name,
            _l('dietetic_dietitian') => $consultation->dietitian_name,
            _l('dietetic_type') => ucfirst(str_replace('_', ' ', $consultation->consultation_type)),
            _l('dietetic_duration') => $consultation->duration . ' ' . _l('dietetic_minutes'),
        ];

        foreach ($details as $label => $value) {
            $pdf->Cell(50, 6, $label . ':', 0, 0);
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(0, 6, $value, 0, 1);
            $pdf->SetFont('Arial', '', 11);
        }

        $pdf->Ln(5);

        // Observations
        if ($consultation->observations) {
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 8, _l('dietetic_observations'), 0, 1);
            $pdf->SetFont('Arial', '', 10);
            $pdf->MultiCell(0, 5, $consultation->observations);
            $pdf->Ln(3);
        }

        // Recommendations
        if ($consultation->recommendations) {
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 8, _l('dietetic_recommendations'), 0, 1);
            $pdf->SetFont('Arial', '', 10);
            $pdf->MultiCell(0, 5, $consultation->recommendations);
        }

        // Output
        $filename = 'consultation_' . $consultation->id . '_' . date('Ymd') . '.pdf';
        $pdf->Output($filename, 'D');
    }

    /**
     * Generate nutrition analysis PDF report
     *
     * @param object $patient Patient data with nutrition analysis
     * @param array $nutrition_analysis Nutrition analysis data
     * @param array $recommendations Recommendations data (optional)
     */
    public function generate_nutrition_analysis_pdf($patient, $nutrition_analysis, $recommendations = [])
    {
        $pdf = new App_pdf();

        // Set document properties
        $client_name = (isset($patient->client) && isset($patient->client->company))
            ? $patient->client->company
            : 'Patient';
        $pdf->SetTitle('Analyse Nutritionnelle - ' . $client_name);
        $pdf->SetAuthor(get_option('companyname'));

        // Add page
        $pdf->AddPage();

        // ==================== HEADER ====================

        // Logo
        $company_logo = get_option('company_logo');
        if ($company_logo && file_exists(FCPATH . 'uploads/company/' . $company_logo)) {
            $pdf->Image(FCPATH . 'uploads/company/' . $company_logo, 10, 10, 30);
        }

        // Title
        $pdf->SetFont('Arial', 'B', 22);
        $pdf->SetTextColor(1, 128, 123); // #01807B
        $pdf->Cell(0, 15, 'ANALYSE NUTRITIONNELLE', 0, 1, 'C');
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(3);

        // ==================== PATIENT INFO ====================

        $pdf->SetFillColor(241, 243, 245); // Light gray background
        $pdf->Rect(10, $pdf->GetY(), 190, 25, 'F');

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(40, 6, 'Patient :', 0, 0);
        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(70, 6, $client_name, 0, 0);

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(30, 6, 'Date :', 0, 0);
        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(0, 6, date('d/m/Y'), 0, 1);

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(40, 6, 'Sexe :', 0, 0);
        $pdf->SetFont('Arial', '', 11);
        $gender_text = (!empty($patient->gender) && $patient->gender === 'female') ? 'Femme' : 'Homme';
        $pdf->Cell(70, 6, $gender_text, 0, 0);

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(30, 6, 'Age :', 0, 0);
        $pdf->SetFont('Arial', '', 11);
        $age = 'N/A';
        if (!empty($patient->birth_date) && $patient->birth_date != '0000-00-00') {
            try {
                $birth_date = new DateTime($patient->birth_date);
                $today = new DateTime();
                $age = $birth_date->diff($today)->y . ' ans';
            } catch (Exception $e) {
                $age = 'N/A';
            }
        }
        $pdf->Cell(0, 6, $age, 0, 1);

        // Get current weight
        $current_weight = null;
        if (!empty($patient->latest_measurement) && !empty($patient->latest_measurement->weight)) {
            $current_weight = floatval($patient->latest_measurement->weight);
        } elseif (!empty($patient->initial_weight)) {
            $current_weight = floatval($patient->initial_weight);
        }

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(40, 6, 'Poids :', 0, 0);
        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(70, 6, ($current_weight ? $current_weight . ' kg' : 'N/A'), 0, 0);

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(30, 6, 'Taille :', 0, 0);
        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(0, 6, (!empty($patient->height) ? $patient->height . ' cm' : 'N/A'), 0, 1);

        $pdf->Ln(8);

        // ==================== BMR & TDEE ====================

        if (!empty($nutrition_analysis['bmr']) && !empty($nutrition_analysis['tdee'])) {
            // Section header
            $pdf->SetFillColor(1, 128, 123);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->Cell(0, 10, 'METABOLISME ET DEPENSE ENERGETIQUE', 0, 1, 'L', true);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Ln(3);

            // BMR
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(100, 8, 'Metabolisme de Base (BMR)', 0, 0);
            $pdf->SetFont('Arial', 'B', 16);
            $pdf->SetTextColor(1, 128, 123);
            $pdf->Cell(0, 8, number_format($nutrition_analysis['bmr']['value'], 0) . ' kcal/jour', 0, 1, 'R');
            $pdf->SetTextColor(0, 0, 0);

            $pdf->SetFont('Arial', 'I', 9);
            $pdf->SetTextColor(127, 140, 141);
            $pdf->Cell(0, 5, 'Formule : ' . $nutrition_analysis['bmr']['formula'], 0, 1);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Ln(2);

            // TDEE
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(100, 8, 'Depense Energetique Totale (TDEE)', 0, 0);
            $pdf->SetFont('Arial', 'B', 16);
            $pdf->SetTextColor(1, 128, 123);
            $pdf->Cell(0, 8, number_format($nutrition_analysis['tdee']['value'], 0) . ' kcal/jour', 0, 1, 'R');
            $pdf->SetTextColor(0, 0, 0);

            $pdf->SetFont('Arial', 'I', 9);
            $pdf->SetTextColor(127, 140, 141);
            $activity_labels = [
                'sedentary' => 'Sedentaire (peu ou pas d exercice)',
                'light' => 'Legerement actif (exercice 1-3j/semaine)',
                'moderate' => 'Moderement actif (exercice 3-5j/semaine)',
                'active' => 'Tres actif (exercice 6-7j/semaine)',
                'very_active' => 'Extremement actif (exercice intense quotidien)'
            ];
            $activity_text = isset($activity_labels[$nutrition_analysis['tdee']['activity_level']])
                ? $activity_labels[$nutrition_analysis['tdee']['activity_level']]
                : 'Moderement actif';
            $pdf->Cell(0, 5, 'Niveau d activite : ' . $activity_text, 0, 1);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Ln(5);
        }

        // ==================== CALORIE GOAL ====================

        if (!empty($nutrition_analysis['calorie_needs'])) {
            $needs = $nutrition_analysis['calorie_needs'];

            $pdf->SetFillColor(231, 76, 60);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->Cell(0, 10, 'OBJECTIF CALORIQUE', 0, 1, 'L', true);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Ln(3);

            // Goal box
            $pdf->SetFillColor(255, 245, 245);
            $pdf->Rect(10, $pdf->GetY(), 190, 20, 'F');

            $pdf->SetFont('Arial', 'B', 14);
            $pdf->Cell(100, 10, 'Apport calorique recommande', 0, 0);
            $pdf->SetFont('Arial', 'B', 20);
            $pdf->SetTextColor(231, 76, 60);
            $pdf->Cell(0, 10, number_format($needs['daily_calories'], 0) . ' kcal', 0, 1, 'R');
            $pdf->SetTextColor(0, 0, 0);

            $pdf->SetFont('Arial', '', 10);
            $goal_labels = [
                'weight_loss' => 'Perte de poids',
                'weight_gain' => 'Prise de poids',
                'maintenance' => 'Maintien du poids',
                'muscle_gain' => 'Prise de masse musculaire'
            ];
            $goal_text = isset($goal_labels[$needs['goal']])
                ? $goal_labels[$needs['goal']]
                : 'Maintien du poids';
            $pdf->Cell(0, 6, 'Objectif : ' . $goal_text, 0, 1);
            $pdf->Ln(5);
        }

        // ==================== MACRONUTRIENTS ====================

        if (!empty($nutrition_analysis['macros'])) {
            $macros = $nutrition_analysis['macros'];

            $pdf->SetFillColor(52, 73, 94);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->Cell(0, 10, 'REPARTITION DES MACRONUTRIMENTS', 0, 1, 'L', true);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Ln(3);

            // Macros in columns
            $col_width = 63;

            // Proteins
            $pdf->SetFillColor(231, 76, 60);
            $pdf->Rect(10, $pdf->GetY(), $col_width, 25, 'F');
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell($col_width, 6, 'PROTEINES', 0, 0, 'C');
            $pdf->SetFillColor(52, 152, 219);
            $pdf->Rect(10 + $col_width, $pdf->GetY() - 6, $col_width, 25, 'F');
            $pdf->Cell($col_width, 6, 'GLUCIDES', 0, 0, 'C');
            $pdf->SetFillColor(243, 156, 18);
            $pdf->Rect(10 + $col_width * 2, $pdf->GetY() - 6, $col_width, 25, 'F');
            $pdf->Cell($col_width, 6, 'LIPIDES', 0, 1, 'C');

            $pdf->SetFont('Arial', 'B', 20);
            $pdf->Cell($col_width, 10, number_format($macros['protein_grams'], 0) . 'g', 0, 0, 'C');
            $pdf->Cell($col_width, 10, number_format($macros['carbs_grams'], 0) . 'g', 0, 0, 'C');
            $pdf->Cell($col_width, 10, number_format($macros['fat_grams'], 0) . 'g', 0, 1, 'C');

            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell($col_width, 5, '(' . $macros['protein_percentage'] . '% - ' . number_format($macros['protein_calories'], 0) . ' kcal)', 0, 0, 'C');
            $pdf->Cell($col_width, 5, '(' . $macros['carbs_percentage'] . '% - ' . number_format($macros['carbs_calories'], 0) . ' kcal)', 0, 0, 'C');
            $pdf->Cell($col_width, 5, '(' . $macros['fat_percentage'] . '% - ' . number_format($macros['fat_calories'], 0) . ' kcal)', 0, 1, 'C');

            $pdf->SetTextColor(0, 0, 0);
            $pdf->Ln(5);
        }

        // ==================== BODY COMPOSITION ====================

        if (!empty($nutrition_analysis['body_composition'])) {
            $pdf->AddPage();

            $bc = $nutrition_analysis['body_composition'];

            $pdf->SetFillColor(243, 156, 18);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->Cell(0, 10, 'COMPOSITION CORPORELLE', 0, 1, 'L', true);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Ln(3);

            // Body fat percentage - Big display
            $pdf->SetFillColor(255, 248, 240);
            $pdf->Rect(10, $pdf->GetY(), 190, 30, 'F');

            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(100, 15, 'Pourcentage de masse grasse', 0, 0);
            $pdf->SetFont('Arial', 'B', 28);
            $pdf->SetTextColor(243, 156, 18);
            $pdf->Cell(0, 15, $bc['body_fat_percentage'] . '%', 0, 1, 'R');
            $pdf->SetTextColor(0, 0, 0);

            $pdf->SetFont('Arial', 'I', 10);
            $pdf->SetTextColor(127, 140, 141);
            $pdf->Cell(0, 8, 'Categorie : ' . $bc['category'], 0, 1);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Ln(5);

            // Detailed composition
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 8, 'Repartition de la masse corporelle', 0, 1);

            $items = [
                ['Masse grasse', $bc['fat_mass'] . ' kg'],
                ['Masse maigre', $bc['lean_body_mass'] . ' kg'],
                ['Masse musculaire', $bc['muscle_mass'] . ' kg'],
                ['Eau corporelle', $bc['body_water'] . ' kg']
            ];

            $pdf->SetFont('Arial', '', 11);
            foreach ($items as $item) {
                $pdf->Cell(100, 8, '  ' . $item[0], 1, 0);
                $pdf->SetFont('Arial', 'B', 11);
                $pdf->Cell(0, 8, $item[1], 1, 1, 'R');
                $pdf->SetFont('Arial', '', 11);
            }

            $pdf->Ln(3);
            $pdf->SetFont('Arial', 'I', 9);
            $pdf->SetTextColor(127, 140, 141);
            $pdf->MultiCell(0, 4, 'Calcule selon la formule de la US Navy (Hodgdon & Beckett, 1984) basee sur les circonferences corporelles.');
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Ln(5);
        }

        // ==================== WATER NEEDS ====================

        if (!empty($nutrition_analysis['water_needs'])) {
            $water = $nutrition_analysis['water_needs'];

            $pdf->SetFillColor(52, 152, 219);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->Cell(0, 10, 'BESOINS EN EAU', 0, 1, 'L', true);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Ln(3);

            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(100, 10, 'Hydratation quotidienne recommandee', 0, 0);
            $pdf->SetFont('Arial', 'B', 24);
            $pdf->SetTextColor(52, 152, 219);
            $pdf->Cell(0, 10, $water['daily_liters'] . ' L', 0, 1, 'R');
            $pdf->SetTextColor(0, 0, 0);

            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(0, 6, 'Soit environ ' . $water['glasses_250ml'] . ' verres de 250ml par jour', 0, 1);
            $pdf->Ln(5);
        }

        // ==================== RECOMMENDATIONS ====================

        if (!empty($recommendations) && !empty($recommendations['recommendations'])) {
            if (!empty($nutrition_analysis['body_composition'])) {
                // Already on page 2
            } else {
                $pdf->AddPage();
            }

            $pdf->SetFillColor(46, 204, 113);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->Cell(0, 10, 'RECOMMANDATIONS NUTRITIONNELLES', 0, 1, 'L', true);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Ln(3);

            foreach ($recommendations['recommendations'] as $rec) {
                $pdf->SetFont('Arial', 'B', 11);
                $pdf->SetTextColor(46, 204, 113);
                $pdf->Cell(0, 7, '> ' . $rec['title'], 0, 1);
                $pdf->SetTextColor(0, 0, 0);

                $pdf->SetFont('Arial', '', 10);
                $pdf->MultiCell(0, 5, $rec['description']);
                $pdf->Ln(2);
            }
        }

        // ==================== FOOTER ====================

        $pdf->SetY(-20);
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->SetTextColor(127, 140, 141);
        $pdf->Cell(0, 10, get_option('companyname') . ' | Genere le ' . date('d/m/Y a H:i'), 0, 0, 'C');

        // Output
        $filename = 'analyse_nutritionnelle_' . $patient->id . '_' . date('Ymd') . '.pdf';
        $pdf->Output($filename, 'D');
    }
}
