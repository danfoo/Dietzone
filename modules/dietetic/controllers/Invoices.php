<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Invoices extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('dietetic/dietetic_invoices_model');
        $this->load->model('dietetic/dietetic_subscriptions_model');
        $this->load->model('dietetic/dietetic_patients_model');
        $this->load->helper('dietetic/dietetic');

        if (!dietetic_has_permission('view')) {
            access_denied('dietetic');
        }
    }

    /**
     * List all invoices
     */
    public function index()
    {
        $data['title'] = 'Factures';

        // Pagination
        $per_page = 20;
        $page = $this->input->get('page') ? (int)$this->input->get('page') : 1;
        $offset = ($page - 1) * $per_page;

        // Filters
        $where = [];
        $status_filter = $this->input->get('status');
        $patient_filter = $this->input->get('patient');
        $date_from = $this->input->get('date_from');
        $date_to = $this->input->get('date_to');

        if ($status_filter && $status_filter !== 'all') {
            $where['i.status'] = $status_filter;
        }

        if ($patient_filter) {
            $where['i.patient_id'] = $patient_filter;
        }

        // Get invoices with WHERE clause
        $total_invoices = $this->dietetic_invoices_model->count_all($where);
        $data['invoices'] = $this->dietetic_invoices_model->get_all($where, $per_page, $offset);

        // Get statistics
        $data['stats'] = $this->dietetic_invoices_model->get_statistics();

        // Get patients for filter
        $data['patients'] = $this->dietetic_patients_model->get_all();

        // Pagination data
        $data['total_invoices'] = $total_invoices;
        $data['per_page'] = $per_page;
        $data['current_page'] = $page;
        $data['total_pages'] = ceil($total_invoices / $per_page);
        $data['status_filter'] = $status_filter;
        $data['patient_filter'] = $patient_filter;

        $this->load->view('admin/invoices/list', $data);
    }

    /**
     * View invoice details
     */
    public function view($id)
    {
        $data['invoice'] = $this->dietetic_invoices_model->get($id);

        if (!$data['invoice']) {
            show_404();
        }

        $data['title'] = 'Facture ' . $data['invoice']->invoice_number;

        // Get payments for this invoice
        $this->load->model('dietetic/dietetic_payments_model');
        $data['payments'] = $this->dietetic_payments_model->get_by_invoice($id);

        $this->load->view('admin/invoices/view', $data);
    }

    /**
     * Generate invoice for subscription
     */
    public function generate($subscription_id)
    {
        if (!dietetic_has_permission('create')) {
            access_denied('dietetic');
        }

        $subscription = $this->dietetic_subscriptions_model->get($subscription_id);

        if (!$subscription) {
            set_alert('danger', 'Abonnement introuvable');
            redirect(admin_url('dietetic/invoices'));
            return;
        }

        // Check if there's already a pending invoice
        $existing = $this->dietetic_invoices_model->get_by_subscription($subscription_id);
        $has_pending = false;
        foreach ($existing as $inv) {
            if (in_array($inv->status, ['draft', 'sent'])) {
                $has_pending = true;
                break;
            }
        }

        if ($has_pending) {
            set_alert('warning', 'Une facture en attente existe déjà pour cet abonnement');
            redirect(admin_url('dietetic/subscriptions/view/' . $subscription_id));
            return;
        }

        // Calculate amounts
        $amount = $subscription->amount;
        $tax_rate = $subscription->tax_rate;
        $tax_amount = $amount * ($tax_rate / 100);
        $total_amount = $amount + $tax_amount;

        // Create invoice
        $invoice_data = [
            'subscription_id' => $subscription_id,
            'patient_id' => $subscription->patient_id,
            'dietitian_id' => $subscription->dietitian_id,
            'issue_date' => date('Y-m-d'),
            'due_date' => date('Y-m-d', strtotime('+15 days')), // 15 days payment term
            'amount' => $amount,
            'tax_rate' => $tax_rate,
            'tax_amount' => $tax_amount,
            'total_amount' => $total_amount,
            'status' => 'draft'
        ];

        $invoice_id = $this->dietetic_invoices_model->add($invoice_data);

        if ($invoice_id) {
            set_alert('success', 'Facture générée avec succès');
            redirect(admin_url('dietetic/invoices/view/' . $invoice_id));
        } else {
            set_alert('danger', 'Erreur lors de la génération de la facture');
            redirect(admin_url('dietetic/subscriptions/view/' . $subscription_id));
        }
    }

    /**
     * Send invoice to patient
     */
    public function send($id)
    {
        if (!dietetic_has_permission('edit')) {
            ajax_access_denied();
        }

        if ($this->dietetic_invoices_model->send_invoice($id)) {
            echo json_encode(['success' => true, 'message' => 'Facture envoyée avec succès']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'envoi']);
        }
    }

    /**
     * Mark invoice as paid (manual)
     */
    public function mark_paid($id)
    {
        if (!dietetic_has_permission('edit')) {
            ajax_access_denied();
        }

        $payment_method = $this->input->post('payment_method') ?: 'cash';

        if ($this->dietetic_invoices_model->mark_as_paid($id, $payment_method)) {
            echo json_encode(['success' => true, 'message' => 'Facture marquée comme payée']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
        }
    }

    /**
     * Download invoice as PDF
     */
    public function pdf($id)
    {
        $invoice = $this->dietetic_invoices_model->get($id);

        if (!$invoice) {
            show_404();
        }

        // Load PDF library
        require_once(APPPATH . 'libraries/pdf/App_pdf.php');

        $data['invoice'] = $invoice;

        // Generate HTML from template
        $html = $this->load->view('admin/invoices/pdf_template', $data, true);

        // Create PDF
        $pdf = new App_pdf();

        // Set document properties
        $pdf->SetTitle('Facture ' . $invoice->invoice_number);
        $pdf->SetAuthor(get_option('companyname'));
        $pdf->SetCreator('DIETZONE');
        $pdf->SetSubject('Facture de service');

        // Set margins
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(true, 15);

        // Add page
        $pdf->AddPage();

        // Write HTML
        $pdf->writeHTML($html, true, false, true, false, '');

        // Output PDF
        $filename = 'Facture_' . $invoice->invoice_number . '.pdf';
        $pdf->Output($filename, 'D');
    }

    /**
     * Update overdue invoices (Cron job)
     */
    public function check_overdue()
    {
        if (!is_admin()) {
            access_denied('dietetic');
        }

        $count = $this->dietetic_invoices_model->update_overdue_invoices();

        echo json_encode([
            'success' => true,
            'message' => $count . ' facture(s) mise(s) à jour en retard'
        ]);
    }

    /**
     * Cancel invoice
     */
    public function cancel($id)
    {
        if (!dietetic_has_permission('delete')) {
            ajax_access_denied();
        }

        $invoice = $this->dietetic_invoices_model->get($id);

        if (!$invoice || $invoice->status == 'paid') {
            echo json_encode(['success' => false, 'message' => 'Impossible d\'annuler cette facture']);
            return;
        }

        if ($this->dietetic_invoices_model->update($id, ['status' => 'cancelled'])) {
            echo json_encode(['success' => true, 'message' => 'Facture annulée']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'annulation']);
        }
    }

    /**
     * Delete invoice (draft/cancelled only)
     */
    public function delete($id)
    {
        if (!dietetic_has_permission('delete')) {
            ajax_access_denied();
        }

        if ($this->dietetic_invoices_model->delete($id)) {
            echo json_encode(['success' => true, 'message' => 'Facture supprimée']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression']);
        }
    }
}
