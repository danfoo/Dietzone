<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Revenue_dashboard extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        // Load models
        $this->load->model('dietetic/dietetic_payments_model');
        $this->load->model('dietetic/dietetic_invoices_model');
        $this->load->model('dietetic/dietetic_revenue_shares_model');
        $this->load->model('dietetic/dietetic_subscriptions_model');
        $this->load->helper('dietetic/dietetic');

        // Check permission
        if (!is_admin() && !dietetic_has_permission('view')) {
            access_denied('Revenue Dashboard');
        }
    }

    /**
     * Main dashboard page
     */
    public function index()
    {
        $data['title'] = 'Dashboard Revenus';

        // Get current dietitian ID
        $dietitian_id = $this->_get_current_dietitian_id();
        $data['dietitian_id'] = $dietitian_id;
        $data['is_admin'] = is_admin();

        // Get date range from filters (default: current month)
        $start_date = $this->input->get('start_date') ?: date('Y-m-01');
        $end_date = $this->input->get('end_date') ?: date('Y-m-t');
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;

        // Get statistics
        $data['stats'] = $this->_get_revenue_statistics($dietitian_id, $start_date, $end_date);

        // Get revenue evolution (last 12 months)
        $data['evolution'] = $this->_get_revenue_evolution($dietitian_id, 12);

        // Get top patients by revenue
        $data['top_patients'] = $this->_get_top_patients_by_revenue($dietitian_id, $start_date, $end_date, 10);

        // Get recent payments
        $data['recent_payments'] = $this->_get_recent_payments($dietitian_id, 10);

        $this->load->view('admin/revenue_dashboard/index', $data);
    }

    /**
     * Revenue by patient page
     */
    public function by_patient()
    {
        $data['title'] = 'Revenus par Patient';

        $dietitian_id = $this->_get_current_dietitian_id();
        $data['dietitian_id'] = $dietitian_id;
        $data['is_admin'] = is_admin();

        // Get date range
        $start_date = $this->input->get('start_date') ?: date('Y-m-01');
        $end_date = $this->input->get('end_date') ?: date('Y-m-t');
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;

        // Get all patients with revenue
        $data['patients_revenue'] = $this->_get_all_patients_revenue($dietitian_id, $start_date, $end_date);

        $this->load->view('admin/revenue_dashboard/by_patient', $data);
    }

    /**
     * Export revenue report to CSV
     */
    public function export_csv()
    {
        $dietitian_id = $this->_get_current_dietitian_id();
        $start_date = $this->input->get('start_date') ?: date('Y-m-01');
        $end_date = $this->input->get('end_date') ?: date('Y-m-t');

        $patients_revenue = $this->_get_all_patients_revenue($dietitian_id, $start_date, $end_date);

        // Set headers for CSV download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="revenus_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');

        // UTF-8 BOM for Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Headers
        fputcsv($output, [
            'Patient',
            'Abonnements Actifs',
            'Total Factures',
            'Total Payé',
            'Revenu Diététicien',
            'Commission Plateforme',
            'Dernier Paiement'
        ], ';');

        // Data rows
        foreach ($patients_revenue as $row) {
            fputcsv($output, [
                $row->patient_name,
                $row->active_subscriptions,
                number_format($row->total_invoiced, 0, ',', ' ') . ' FCFA',
                number_format($row->total_paid, 0, ',', ' ') . ' FCFA',
                number_format($row->dietitian_share, 0, ',', ' ') . ' FCFA',
                number_format($row->platform_share, 0, ',', ' ') . ' FCFA',
                $row->last_payment_date ? _d($row->last_payment_date) : 'N/A'
            ], ';');
        }

        fclose($output);
        exit;
    }

    /**
     * Export revenue report to PDF (printable HTML)
     */
    public function export_pdf()
    {
        $dietitian_id = $this->_get_current_dietitian_id();
        $start_date = $this->input->get('start_date') ?: date('Y-m-01');
        $end_date = $this->input->get('end_date') ?: date('Y-m-t');

        $data['patients_revenue'] = $this->_get_all_patients_revenue($dietitian_id, $start_date, $end_date);
        $data['stats'] = $this->_get_revenue_statistics($dietitian_id, $start_date, $end_date);
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        $data['dietitian_name'] = $this->_get_dietitian_name($dietitian_id);
        $data['title'] = 'Rapport de Revenus';

        // Load a printable HTML view that can be printed to PDF by the browser
        $this->load->view('admin/revenue_dashboard/pdf_export', $data);
    }

    /**
     * Get revenue data for charts (AJAX)
     */
    public function get_chart_data()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $dietitian_id = $this->_get_current_dietitian_id();
        $months = $this->input->get('months') ?: 12;

        $evolution = $this->_get_revenue_evolution($dietitian_id, $months);

        echo json_encode([
            'success' => true,
            'data' => $evolution
        ]);
    }

    /**
     * Get current dietitian ID
     * Admins can view all, non-admins see only their own
     */
    private function _get_current_dietitian_id()
    {
        // If admin and a specific dietitian is selected
        if (is_admin() && $this->input->get('dietitian_id')) {
            return (int)$this->input->get('dietitian_id');
        }

        // Non-admin users see only their own data
        if (!is_admin()) {
            return get_staff_user_id();
        }

        // Admin without filter sees all (return null)
        return null;
    }

    /**
     * Get revenue statistics for a period
     */
    private function _get_revenue_statistics($dietitian_id, $start_date, $end_date)
    {
        $where = "DATE(p.payment_date) BETWEEN '{$start_date}' AND '{$end_date}' AND p.status = 'completed'";

        if ($dietitian_id) {
            $where .= " AND p.dietitian_id = {$dietitian_id}";
        }

        // Total revenue
        $this->db->select('
            COUNT(p.id) as total_payments,
            COALESCE(SUM(p.amount), 0) as total_revenue,
            COALESCE(SUM(rs.dietitian_share), 0) as dietitian_share,
            COALESCE(SUM(rs.platform_share), 0) as platform_share,
            COALESCE(AVG(p.amount), 0) as avg_payment
        ');
        $this->db->from(db_prefix() . 'dietic_payments p');
        $this->db->join(db_prefix() . 'dietic_revenue_shares rs', 'rs.payment_id = p.id', 'left');
        $this->db->where($where);
        $stats = $this->db->get()->row();

        // Active subscriptions
        $this->db->select('COUNT(*) as active_subscriptions');
        $this->db->from(db_prefix() . 'dietic_subscriptions');
        $this->db->where('status', 'active');
        if ($dietitian_id) {
            $this->db->where('dietitian_id', $dietitian_id);
        }
        $active_subs = $this->db->get()->row();
        $stats->active_subscriptions = $active_subs->active_subscriptions;

        // Pending invoices
        $this->db->select('COUNT(*) as pending_invoices, COALESCE(SUM(total_amount), 0) as pending_amount');
        $this->db->from(db_prefix() . 'dietic_invoices');
        $this->db->where_in('status', ['sent', 'overdue']);
        if ($dietitian_id) {
            $this->db->where('dietitian_id', $dietitian_id);
        }
        $pending = $this->db->get()->row();
        $stats->pending_invoices = $pending->pending_invoices;
        $stats->pending_amount = $pending->pending_amount;

        return $stats;
    }

    /**
     * Get revenue evolution over time
     */
    private function _get_revenue_evolution($dietitian_id, $months = 12)
    {
        $evolution = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $month_start = date('Y-m-01', strtotime("-{$i} months"));
            $month_end = date('Y-m-t', strtotime("-{$i} months"));

            $where = "DATE(p.payment_date) BETWEEN '{$month_start}' AND '{$month_end}' AND p.status = 'completed'";

            if ($dietitian_id) {
                $where .= " AND p.dietitian_id = {$dietitian_id}";
            }

            $this->db->select('
                COALESCE(SUM(p.amount), 0) as total,
                COALESCE(SUM(rs.dietitian_share), 0) as dietitian_share,
                COALESCE(SUM(rs.platform_share), 0) as platform_share,
                COUNT(p.id) as payment_count
            ');
            $this->db->from(db_prefix() . 'dietic_payments p');
            $this->db->join(db_prefix() . 'dietic_revenue_shares rs', 'rs.payment_id = p.id', 'left');
            $this->db->where($where);
            $result = $this->db->get()->row();

            $evolution[] = [
                'month' => date('M Y', strtotime($month_start)),
                'month_num' => date('Y-m', strtotime($month_start)),
                'total' => (float)$result->total,
                'dietitian_share' => (float)$result->dietitian_share,
                'platform_share' => (float)$result->platform_share,
                'payment_count' => (int)$result->payment_count
            ];
        }

        return $evolution;
    }

    /**
     * Get top patients by revenue
     */
    private function _get_top_patients_by_revenue($dietitian_id, $start_date, $end_date, $limit = 10)
    {
        $where = "DATE(p.payment_date) BETWEEN '{$start_date}' AND '{$end_date}' AND p.status = 'completed'";

        if ($dietitian_id) {
            $where .= " AND p.dietitian_id = {$dietitian_id}";
        }

        $this->db->select('
            c.company as patient_name,
            p.patient_id,
            COUNT(p.id) as payment_count,
            COALESCE(SUM(p.amount), 0) as total_paid,
            COALESCE(SUM(rs.dietitian_share), 0) as dietitian_share,
            MAX(p.payment_date) as last_payment_date
        ');
        $this->db->from(db_prefix() . 'dietic_payments p');
        $this->db->join(db_prefix() . 'dietic_revenue_shares rs', 'rs.payment_id = p.id', 'left');
        $this->db->join(db_prefix() . 'clients c', 'c.userid = p.patient_id');
        $this->db->where($where);
        $this->db->group_by('p.patient_id');
        $this->db->order_by('total_paid', 'DESC');
        $this->db->limit($limit);

        return $this->db->get()->result();
    }

    /**
     * Get all patients with revenue details
     */
    private function _get_all_patients_revenue($dietitian_id, $start_date, $end_date)
    {
        $where_payments = "DATE(p.payment_date) BETWEEN '{$start_date}' AND '{$end_date}' AND p.status = 'completed'";

        if ($dietitian_id) {
            $where_payments .= " AND p.dietitian_id = {$dietitian_id}";
        }

        // Get all patients with payments
        $this->db->select('
            c.company as patient_name,
            p.patient_id,
            COUNT(DISTINCT s.id) as active_subscriptions,
            COUNT(DISTINCT i.id) as total_invoices,
            COALESCE(SUM(p.amount), 0) as total_paid,
            COALESCE(SUM(rs.dietitian_share), 0) as dietitian_share,
            COALESCE(SUM(rs.platform_share), 0) as platform_share,
            MAX(p.payment_date) as last_payment_date
        ');
        $this->db->from(db_prefix() . 'dietic_payments p');
        $this->db->join(db_prefix() . 'clients c', 'c.userid = p.patient_id');
        $this->db->join(db_prefix() . 'dietic_revenue_shares rs', 'rs.payment_id = p.id', 'left');
        $this->db->join(db_prefix() . 'dietic_subscriptions s', 's.patient_id = p.patient_id AND s.status = "active"', 'left');
        $this->db->join(db_prefix() . 'dietic_invoices i', 'i.patient_id = p.patient_id', 'left');
        $this->db->where($where_payments);
        $this->db->group_by('p.patient_id');
        $this->db->order_by('total_paid', 'DESC');

        // Calculate total invoiced amount separately
        $results = $this->db->get()->result();

        foreach ($results as &$result) {
            // Get total invoiced amount for this patient
            $this->db->select('COALESCE(SUM(total_amount), 0) as total_invoiced');
            $this->db->from(db_prefix() . 'dietic_invoices');
            $this->db->where('patient_id', $result->patient_id);
            if ($dietitian_id) {
                $this->db->where('dietitian_id', $dietitian_id);
            }
            $invoice_total = $this->db->get()->row();
            $result->total_invoiced = $invoice_total->total_invoiced;
        }

        return $results;
    }

    /**
     * Get recent payments
     */
    private function _get_recent_payments($dietitian_id, $limit = 10)
    {
        $this->db->select('
            p.*,
            c.company as patient_name,
            i.invoice_number
        ');
        $this->db->from(db_prefix() . 'dietic_payments p');
        $this->db->join(db_prefix() . 'clients c', 'c.userid = p.patient_id');
        $this->db->join(db_prefix() . 'dietic_invoices i', 'i.id = p.invoice_id', 'left');

        if ($dietitian_id) {
            $this->db->where('p.dietitian_id', $dietitian_id);
        }

        $this->db->order_by('p.payment_date', 'DESC');
        $this->db->limit($limit);

        return $this->db->get()->result();
    }

    /**
     * Get dietitian name
     */
    private function _get_dietitian_name($dietitian_id)
    {
        if (!$dietitian_id) {
            return 'Tous les diététiciens';
        }

        $this->db->select('firstname, lastname');
        $this->db->from(db_prefix() . 'staff');
        $this->db->where('staffid', $dietitian_id);
        $staff = $this->db->get()->row();

        return $staff ? $staff->firstname . ' ' . $staff->lastname : 'N/A';
    }
}
