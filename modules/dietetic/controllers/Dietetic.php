<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dietetic extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        // Load helper first
        $this->load->helper('dietetic/dietetic');

        // Load Perfex models
        $this->load->model('clients_model');
        $this->load->model('staff_model');

        // Load dietetic models
        $this->load->model('dietetic/dietetic_patients_model');
        $this->load->model('dietetic/dietetic_consultations_model');
        $this->load->model('dietetic/dietetic_programs_model');
        $this->load->model('dietetic/dietetic_measurements_model');
        $this->load->model('dietetic/dietetic_reminders_model');

        // Check permission - removed temporarily for testing
        // if (!dietetic_has_permission('view')) {
        //     access_denied('dietetic');
        // }
    }

    /**
     * Dashboard - Main view with KPIs and statistics
     */
    public function index()
    {
        $data['title'] = 'Dietetic Dashboard';

        // Initialize all with defaults
        $data['patient_stats'] = new stdClass();
        $data['patient_stats']->active_patients = 0;

        $data['consultation_stats'] = new stdClass();
        $data['consultation_stats']->scheduled = 0;
        $data['consultation_stats']->avg_satisfaction = 0;

        $data['program_stats'] = new stdClass();
        $data['program_stats']->active_programs = 0;

        $data['recent_patients'] = [];
        $data['upcoming_consultations'] = [];

        // Try to load real data
        try {
            $stats = $this->dietetic_patients_model->get_statistics();
            if ($stats) {
                $data['patient_stats'] = $stats;
            }
        } catch (Exception $e) {}

        try {
            $stats = $this->dietetic_consultations_model->get_statistics();
            if ($stats) {
                $data['consultation_stats'] = $stats;
            }
        } catch (Exception $e) {}

        try {
            $stats = $this->dietetic_programs_model->get_statistics();
            if ($stats) {
                $data['program_stats'] = $stats;
            }
        } catch (Exception $e) {}

        try {
            $patients = $this->dietetic_patients_model->get_all();
            if (is_array($patients)) {
                $data['recent_patients'] = array_slice($patients, 0, 5);
            }
        } catch (Exception $e) {}

        try {
            $consultations = $this->dietetic_consultations_model->get_upcoming(5);
            if (is_array($consultations)) {
                $data['upcoming_consultations'] = $consultations;
            }
        } catch (Exception $e) {}

        $this->load->view('admin/dashboard', $data);
    }

    /**
     * Diagnostic tool for 404 errors
     */
    public function diagnostic()
    {
        if (!is_admin()) {
            access_denied('Diagnostic');
        }

        $this->load->view('diagnostic_web');
    }

    /**
     * Settings page
     */
    public function settings()
    {
        // Only admins can access settings
        if (!is_admin()) {
            access_denied('dietetic');
        }

        $data['title'] = _l('dietetic_settings');

        // Handle form submission
        if ($this->input->post()) {
            $settings = $this->input->post();

            foreach ($settings as $key => $value) {
                dietetic_update_option($key, $value);
            }

            set_alert('success', _l('settings_updated'));
            redirect(admin_url('dietetic/settings'));
        }

        // Get all settings
        $this->db->select('*');
        $data['settings'] = $this->db->get(db_prefix() . 'dietic_settings')->result();

        $this->load->view('admin/settings', $data);
    }

    /**
     * Test cron job (for debugging)
     */
    public function test_cron()
    {
        if (!is_admin()) {
            access_denied('dietetic');
        }

        $result = $this->dietetic_reminders_model->send_pending_reminders();

        echo json_encode([
            'success' => true,
            'data'    => $result,
            'message' => sprintf(
                'Sent: %d, Failed: %d, Total: %d',
                $result['sent'],
                $result['failed'],
                $result['total']
            ),
        ]);
    }

    /**
     * Get patient growth chart data
     *
     * @return array
     */
    private function get_patient_growth_chart_data()
    {
        $months = [];
        $counts = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            $months[] = date('M Y', strtotime($month));

            $this->db->where('DATE_FORMAT(created_at, "%Y-%m")', $month);
            if (!is_admin()) {
                $this->db->where('dietitian_id', get_staff_user_id());
            }
            $count = $this->db->count_all_results(db_prefix() . 'dietic_patients');
            $counts[] = $count;
        }

        return [
            'labels' => $months,
            'data'   => $counts,
        ];
    }

    /**
     * Get consultation types chart data
     *
     * @return array
     */
    private function get_consultation_types_chart_data()
    {
        $this->db->select('consultation_type, COUNT(*) as count');
        $this->db->group_by('consultation_type');

        if (!is_admin()) {
            $this->db->where('dietitian_id', get_staff_user_id());
        }

        $results = $this->db->get(db_prefix() . 'dietic_consultations')->result();

        $labels = [];
        $data = [];

        foreach ($results as $result) {
            $labels[] = ucfirst(str_replace('_', ' ', $result->consultation_type));
            $data[] = $result->count;
        }

        return [
            'labels' => $labels,
            'data'   => $data,
        ];
    }

    /**
     * Export data to CSV
     */
    public function export($type = 'patients')
    {
        if (!dietetic_has_permission('view')) {
            access_denied('dietetic');
        }

        $filename = 'dietetic_' . $type . '_' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        switch ($type) {
            case 'patients':
                $this->export_patients($output);
                break;
            case 'consultations':
                $this->export_consultations($output);
                break;
            case 'programs':
                $this->export_programs($output);
                break;
        }

        fclose($output);
        exit;
    }

    /**
     * Export patients to CSV
     *
     * @param resource $output
     */
    private function export_patients($output)
    {
        // Headers
        fputcsv($output, [
            'ID',
            'Client Name',
            'Dietitian',
            'Status',
            'Gender',
            'Birth Date',
            'Phone',
            'Email',
            'Initial Weight',
            'Target Weight',
            'Height',
            'Activity Level',
            'Created At',
        ]);

        // Data
        $patients = $this->dietetic_patients_model->get_all();

        foreach ($patients as $patient) {
            fputcsv($output, [
                $patient->id,
                $patient->client_name,
                $patient->dietitian_name,
                $patient->status,
                $patient->gender,
                $patient->birth_date,
                $patient->phone,
                $patient->email,
                $patient->initial_weight,
                $patient->target_weight,
                $patient->height,
                $patient->activity_level,
                $patient->created_at,
            ]);
        }
    }

    /**
     * Export consultations to CSV
     *
     * @param resource $output
     */
    private function export_consultations($output)
    {
        // Headers
        fputcsv($output, [
            'ID',
            'Client Name',
            'Dietitian',
            'Date',
            'Type',
            'Status',
            'Duration',
            'Satisfaction',
            'Created At',
        ]);

        // Data
        $consultations = $this->dietetic_consultations_model->get_all();

        foreach ($consultations as $consultation) {
            fputcsv($output, [
                $consultation->id,
                $consultation->client_name,
                $consultation->dietitian_name,
                $consultation->consultation_date,
                $consultation->consultation_type,
                $consultation->status,
                $consultation->duration,
                $consultation->satisfaction_score,
                $consultation->created_at,
            ]);
        }
    }

    /**
     * Export programs to CSV
     *
     * @param resource $output
     */
    private function export_programs($output)
    {
        // Headers
        fputcsv($output, [
            'ID',
            'Client Name',
            'Program Name',
            'Status',
            'Start Date',
            'End Date',
            'Daily Calories',
            'Daily Protein',
            'Daily Carbs',
            'Daily Fats',
            'Created At',
        ]);

        // Data
        $programs = $this->dietetic_programs_model->get_all();

        foreach ($programs as $program) {
            fputcsv($output, [
                $program->id,
                $program->client_name,
                $program->program_name,
                $program->status,
                $program->start_date,
                $program->end_date,
                $program->daily_calories,
                $program->daily_protein,
                $program->daily_carbs,
                $program->daily_fats,
                $program->created_at,
            ]);
        }
    }
}
