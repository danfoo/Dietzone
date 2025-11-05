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
}
