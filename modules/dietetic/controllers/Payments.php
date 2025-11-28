<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Payments extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('dietetic/dietetic_payments_model');
        $this->load->model('dietetic/dietetic_invoices_model');
        $this->load->model('dietetic/dietetic_subscriptions_model');
        $this->load->helper('dietetic/dietetic');

        if (!dietetic_has_permission('view')) {
            access_denied('dietetic');
        }
    }

    /**
     * Record payment for an invoice
     */
    public function record($invoice_id)
    {
        // Only admins and users with create permission can record payments
        if (!is_admin() && !dietetic_has_permission('create')) {
            access_denied('dietetic');
        }

        $data['invoice'] = $this->dietetic_invoices_model->get($invoice_id);

        if (!$data['invoice']) {
            show_404();
        }

        // Check if invoice is already paid
        if ($data['invoice']->status == 'paid') {
            set_alert('warning', 'Cette facture est déjà payée');
            redirect(admin_url('dietetic/invoices/view/' . $invoice_id));
            return;
        }

        if ($this->input->post()) {
            // Only get the fields we need
            $payment_data = [
                'invoice_id' => $invoice_id,
                'subscription_id' => $data['invoice']->subscription_id,
                'patient_id' => $data['invoice']->patient_id,
                'dietitian_id' => $data['invoice']->dietitian_id,
                'amount' => $this->input->post('amount'),
                'payment_method' => $this->input->post('payment_method'),
                'payment_date' => $this->input->post('payment_date') ?: date('Y-m-d'),
                'reference' => $this->input->post('reference'),
                'notes' => $this->input->post('notes'),
                'status' => 'completed' // Manual payments are immediately completed
            ];

            // Validate amount
            if ($payment_data['amount'] <= 0) {
                set_alert('danger', 'Le montant doit être supérieur à 0');
            } else {
                $payment_id = $this->dietetic_payments_model->add($payment_data);

                if ($payment_id) {
                    set_alert('success', 'Paiement enregistré avec succès');
                    redirect(admin_url('dietetic/invoices/view/' . $invoice_id));
                } else {
                    set_alert('danger', 'Erreur lors de l\'enregistrement du paiement');
                }
            }
        }

        $data['title'] = 'Enregistrer un Paiement';
        $data['invoice_id'] = $invoice_id;

        $this->load->view('admin/payments/record', $data);
    }

    /**
     * View payment details
     */
    public function view($id)
    {
        $data['payment'] = $this->dietetic_payments_model->get($id);

        if (!$data['payment']) {
            show_404();
        }

        $data['title'] = 'Paiement #' . $id;

        $this->load->view('admin/payments/view', $data);
    }

    /**
     * Cancel a payment (admin only)
     */
    public function cancel($id)
    {
        if (!is_admin()) {
            ajax_access_denied();
        }

        $payment = $this->dietetic_payments_model->get($id);

        if (!$payment) {
            echo json_encode(['success' => false, 'message' => 'Paiement introuvable']);
            return;
        }

        if ($payment->status == 'cancelled') {
            echo json_encode(['success' => false, 'message' => 'Ce paiement est déjà annulé']);
            return;
        }

        if ($this->dietetic_payments_model->cancel($id)) {
            echo json_encode(['success' => true, 'message' => 'Paiement annulé avec succès']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'annulation']);
        }
    }

    /**
     * Initiate PayPal payment (for future implementation)
     */
    public function paypal_checkout($invoice_id)
    {
        // This will be implemented when PayPal integration is ready
        set_alert('info', 'L\'intégration PayPal sera disponible prochainement');
        redirect(admin_url('dietetic/invoices/view/' . $invoice_id));
    }

    /**
     * PayPal callback handler (for future implementation)
     */
    public function paypal_callback()
    {
        // This will handle PayPal IPN/webhook callbacks
        // For now, just log the attempt
        log_activity('PayPal callback received (not yet implemented)');
    }
}
