<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Payment Gateways Controller
 * Handles online payment integrations (PayPal, Wave, Orange Money)
 */
class Payment_gateways extends ClientsController
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('dietetic/dietetic_invoices_model');
        $this->load->model('dietetic/dietetic_payments_model');
        $this->load->model('dietetic/dietetic_patients_model');
        $this->load->helper('dietetic/dietetic');
    }

    /**
     * Get logged in patient or redirect
     */
    private function get_logged_in_patient()
    {
        if (!is_client_logged_in()) {
            redirect(site_url('clients/login'));
        }

        $client_id = get_client_user_id();
        $patient = $this->dietetic_patients_model->get_by_client($client_id);

        if (!$patient) {
            set_alert('danger', 'Patient record not found');
            redirect(site_url('clients/profile'));
        }

        return $patient;
    }

    /**
     * Main payment selection page
     */
    public function pay($invoice_id)
    {
        $patient = $this->get_logged_in_patient();

        $invoice = $this->dietetic_invoices_model->get($invoice_id);

        if (!$invoice || $invoice->patient_id != $patient->id) {
            show_404();
        }

        // Check if already paid
        if ($invoice->status == 'paid') {
            set_alert('info', 'Cette facture est déjà payée');
            redirect(site_url('dietetic/portal'));
        }

        $data['invoice'] = $invoice;
        $data['patient'] = $patient;
        $data['title'] = 'Payer la facture ' . $invoice->invoice_number;

        // Get available payment methods
        $data['paypal_enabled'] = (bool) dietetic_get_option('paypal_enabled');
        $data['wave_enabled'] = (bool) dietetic_get_option('wave_enabled');
        $data['orange_money_enabled'] = (bool) dietetic_get_option('orange_money_enabled');

        $this->data($data);
        $this->view('payment_gateways/select_method');
        $this->layout();
    }

    // ==========================================
    // PAYPAL INTEGRATION
    // ==========================================

    /**
     * Initiate PayPal payment
     */
    public function paypal_checkout($invoice_id)
    {
        $patient = $this->get_logged_in_patient();
        $invoice = $this->dietetic_invoices_model->get($invoice_id);

        if (!$invoice || $invoice->patient_id != $patient->id) {
            show_404();
        }

        if (!dietetic_get_option('paypal_enabled')) {
            set_alert('danger', 'PayPal n\'est pas activé');
            redirect(site_url('dietetic/portal'));
        }

        $paypal_client_id = dietetic_get_option('paypal_client_id');
        $paypal_secret = dietetic_get_option('paypal_secret');
        $paypal_mode = dietetic_get_option('paypal_mode', 'sandbox');

        if (!$paypal_client_id || !$paypal_secret) {
            set_alert('danger', 'PayPal n\'est pas configuré correctement');
            redirect(site_url('dietetic/portal'));
        }

        $data['invoice'] = $invoice;
        $data['patient'] = $patient;
        $data['paypal_client_id'] = $paypal_client_id;
        $data['paypal_mode'] = $paypal_mode;
        $data['return_url'] = site_url('dietetic/payment_gateways/paypal_success');
        $data['cancel_url'] = site_url('dietetic/payment_gateways/pay/' . $invoice_id);
        $data['title'] = 'Paiement PayPal';

        $this->data($data);
        $this->view('payment_gateways/paypal_checkout');
        $this->layout();
    }

    /**
     * PayPal success callback
     */
    public function paypal_success()
    {
        $patient = $this->get_logged_in_patient();

        $order_id = $this->input->get('orderID') ?: $this->input->post('orderID');
        $invoice_id = $this->input->get('invoice_id') ?: $this->input->post('invoice_id');

        if (!$order_id || !$invoice_id) {
            set_alert('danger', 'Informations de paiement invalides');
            redirect(site_url('dietetic/portal'));
        }

        $invoice = $this->dietetic_invoices_model->get($invoice_id);

        if (!$invoice || $invoice->patient_id != $patient->id) {
            show_404();
        }

        // Verify payment with PayPal API
        $payment_verified = $this->verify_paypal_payment($order_id);

        if ($payment_verified && $payment_verified['status'] == 'COMPLETED') {
            // Record payment
            $payment_data = [
                'invoice_id' => $invoice_id,
                'subscription_id' => $invoice->subscription_id,
                'patient_id' => $invoice->patient_id,
                'dietitian_id' => $invoice->dietitian_id,
                'amount' => $payment_verified['amount'],
                'payment_method' => 'paypal',
                'payment_date' => date('Y-m-d H:i:s'),
                'payment_reference' => $order_id,
                'transaction_id' => $payment_verified['transaction_id'],
                'notes' => 'PayPal Order ID: ' . $order_id,
                'status' => 'completed'
            ];

            $payment_id = $this->dietetic_payments_model->add($payment_data);

            if ($payment_id) {
                set_alert('success', 'Paiement PayPal effectué avec succès!');
                redirect(site_url('dietetic/portal/invoices'));
            } else {
                set_alert('danger', 'Erreur lors de l\'enregistrement du paiement');
                redirect(site_url('dietetic/portal'));
            }
        } else {
            set_alert('danger', 'Le paiement PayPal n\'a pas pu être vérifié');
            redirect(site_url('dietetic/payment_gateways/pay/' . $invoice_id));
        }
    }

    /**
     * Verify PayPal payment
     */
    private function verify_paypal_payment($order_id)
    {
        $paypal_client_id = dietetic_get_option('paypal_client_id');
        $paypal_secret = dietetic_get_option('paypal_secret');
        $paypal_mode = dietetic_get_option('paypal_mode', 'sandbox');

        $base_url = $paypal_mode == 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';

        // Get access token
        $token_url = $base_url . '/v1/oauth2/token';

        $ch = curl_init($token_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_USERPWD, $paypal_client_id . ':' . $paypal_secret);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json', 'Accept-Language: en_US']);

        $token_response = curl_exec($ch);
        curl_close($ch);

        $token_data = json_decode($token_response, true);

        if (!isset($token_data['access_token'])) {
            log_activity('PayPal token error: ' . $token_response);
            return false;
        }

        $access_token = $token_data['access_token'];

        // Get order details
        $order_url = $base_url . '/v2/checkout/orders/' . $order_id;

        $ch = curl_init($order_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $access_token
        ]);

        $order_response = curl_exec($ch);
        curl_close($ch);

        $order_data = json_decode($order_response, true);

        if (!isset($order_data['status'])) {
            log_activity('PayPal order verification error: ' . $order_response);
            return false;
        }

        log_activity('PayPal payment verified [Order: ' . $order_id . ', Status: ' . $order_data['status'] . ']');

        return [
            'status' => $order_data['status'],
            'amount' => $order_data['purchase_units'][0]['amount']['value'] ?? 0,
            'currency' => $order_data['purchase_units'][0]['amount']['currency_code'] ?? 'USD',
            'transaction_id' => $order_data['purchase_units'][0]['payments']['captures'][0]['id'] ?? $order_id
        ];
    }

    // ==========================================
    // WAVE INTEGRATION
    // ==========================================

    /**
     * Initiate Wave payment
     */
    public function wave_checkout($invoice_id)
    {
        $patient = $this->get_logged_in_patient();
        $invoice = $this->dietetic_invoices_model->get($invoice_id);

        if (!$invoice || $invoice->patient_id != $patient->id) {
            show_404();
        }

        if (!dietetic_get_option('wave_enabled')) {
            set_alert('danger', 'Wave n\'est pas activé');
            redirect(site_url('dietetic/portal'));
        }

        $wave_api_key = dietetic_get_option('wave_api_key');
        $wave_merchant_id = dietetic_get_option('wave_merchant_id');

        if (!$wave_api_key || !$wave_merchant_id) {
            set_alert('danger', 'Wave n\'est pas configuré correctement');
            redirect(site_url('dietetic/portal'));
        }

        // Create Wave payment request
        $payment_url = $this->create_wave_payment($invoice, $patient);

        if ($payment_url) {
            redirect($payment_url);
        } else {
            set_alert('danger', 'Erreur lors de la création du paiement Wave');
            redirect(site_url('dietetic/payment_gateways/pay/' . $invoice_id));
        }
    }

    /**
     * Create Wave payment
     */
    private function create_wave_payment($invoice, $patient)
    {
        $wave_api_key = dietetic_get_option('wave_api_key');
        $wave_api_url = 'https://api.wave.com/v1/checkout/sessions';

        $data = [
            'amount' => $invoice->total_amount,
            'currency' => 'XOF', // West African CFA Franc
            'error_url' => site_url('dietetic/payment_gateways/wave_cancel/' . $invoice->id),
            'success_url' => site_url('dietetic/payment_gateways/wave_success/' . $invoice->id),
            'metadata' => [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'patient_id' => $patient->id
            ]
        ];

        $ch = curl_init($wave_api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $wave_api_key,
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $response_data = json_decode($response, true);

        if ($http_code == 201 && isset($response_data['wave_launch_url'])) {
            log_activity('Wave payment created [Invoice: ' . $invoice->invoice_number . ']');
            return $response_data['wave_launch_url'];
        }

        log_activity('Wave payment creation failed [Invoice: ' . $invoice->invoice_number . ']: ' . $response);
        return false;
    }

    /**
     * Wave success callback
     */
    public function wave_success($invoice_id)
    {
        $patient = $this->get_logged_in_patient();
        $invoice = $this->dietetic_invoices_model->get($invoice_id);

        if (!$invoice || $invoice->patient_id != $patient->id) {
            show_404();
        }

        $wave_transaction_id = $this->input->get('wave_transaction_id') ?: $this->input->get('id');

        if (!$wave_transaction_id) {
            set_alert('danger', 'Transaction ID manquant');
            redirect(site_url('dietetic/portal'));
        }

        // Record payment
        $payment_data = [
            'invoice_id' => $invoice_id,
            'subscription_id' => $invoice->subscription_id,
            'patient_id' => $invoice->patient_id,
            'dietitian_id' => $invoice->dietitian_id,
            'amount' => $invoice->total_amount,
            'payment_method' => 'wave',
            'payment_date' => date('Y-m-d H:i:s'),
            'payment_reference' => $wave_transaction_id,
            'transaction_id' => $wave_transaction_id,
            'notes' => 'Wave Transaction ID: ' . $wave_transaction_id,
            'status' => 'completed'
        ];

        $payment_id = $this->dietetic_payments_model->add($payment_data);

        if ($payment_id) {
            set_alert('success', 'Paiement Wave effectué avec succès!');
            redirect(site_url('dietetic/portal/invoices'));
        } else {
            set_alert('danger', 'Erreur lors de l\'enregistrement du paiement');
            redirect(site_url('dietetic/portal'));
        }
    }

    /**
     * Wave cancel callback
     */
    public function wave_cancel($invoice_id)
    {
        set_alert('warning', 'Paiement Wave annulé');
        redirect(site_url('dietetic/payment_gateways/pay/' . $invoice_id));
    }

    // ==========================================
    // ORANGE MONEY INTEGRATION
    // ==========================================

    /**
     * Initiate Orange Money payment
     */
    public function orange_money_checkout($invoice_id)
    {
        $patient = $this->get_logged_in_patient();
        $invoice = $this->dietetic_invoices_model->get($invoice_id);

        if (!$invoice || $invoice->patient_id != $patient->id) {
            show_404();
        }

        if (!dietetic_get_option('orange_money_enabled')) {
            set_alert('danger', 'Orange Money n\'est pas activé');
            redirect(site_url('dietetic/portal'));
        }

        $data['invoice'] = $invoice;
        $data['patient'] = $patient;
        $data['title'] = 'Paiement Orange Money';

        $this->data($data);
        $this->view('payment_gateways/orange_money_checkout');
        $this->layout();
    }

    /**
     * Process Orange Money payment
     */
    public function orange_money_process()
    {
        $patient = $this->get_logged_in_patient();

        $invoice_id = $this->input->post('invoice_id');
        $phone = $this->input->post('phone');

        $invoice = $this->dietetic_invoices_model->get($invoice_id);

        if (!$invoice || $invoice->patient_id != $patient->id) {
            echo json_encode(['success' => false, 'message' => 'Facture invalide']);
            return;
        }

        $om_merchant_key = dietetic_get_option('orange_money_merchant_key');
        $om_api_url = dietetic_get_option('orange_money_api_url', 'https://api.orange.com/orange-money-webpay/dev/v1');

        if (!$om_merchant_key) {
            echo json_encode(['success' => false, 'message' => 'Orange Money n\'est pas configuré']);
            return;
        }

        // Initiate Orange Money payment
        $payment_result = $this->initiate_orange_money_payment($invoice, $phone);

        if ($payment_result && $payment_result['success']) {
            echo json_encode([
                'success' => true,
                'message' => 'Paiement initié. Vérifiez votre téléphone pour confirmer.',
                'transaction_id' => $payment_result['transaction_id']
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => $payment_result['message'] ?? 'Erreur lors de l\'initiation du paiement'
            ]);
        }
    }

    /**
     * Initiate Orange Money payment
     */
    private function initiate_orange_money_payment($invoice, $phone)
    {
        $om_merchant_key = dietetic_get_option('orange_money_merchant_key');
        $om_api_url = dietetic_get_option('orange_money_api_url', 'https://api.orange.com/orange-money-webpay/dev/v1');

        // Format phone number (remove spaces, add country code if missing)
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($phone) == 9) {
            $phone = '221' . $phone; // Senegal country code
        }

        $data = [
            'merchant_key' => $om_merchant_key,
            'currency' => 'XOF',
            'order_id' => 'INV-' . $invoice->id . '-' . time(),
            'amount' => $invoice->total_amount,
            'return_url' => site_url('dietetic/payment_gateways/orange_money_callback'),
            'cancel_url' => site_url('dietetic/payment_gateways/pay/' . $invoice->id),
            'notif_url' => site_url('dietetic/payment_gateways/orange_money_webhook'),
            'lang' => 'fr',
            'reference' => $invoice->invoice_number,
            'customer_phone' => $phone
        ];

        $ch = curl_init($om_api_url . '/webpayment');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $om_merchant_key
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $response_data = json_decode($response, true);

        if ($http_code == 200 && isset($response_data['payment_url'])) {
            log_activity('Orange Money payment initiated [Invoice: ' . $invoice->invoice_number . ', Phone: ' . $phone . ']');
            return [
                'success' => true,
                'payment_url' => $response_data['payment_url'],
                'transaction_id' => $response_data['pay_token'] ?? null
            ];
        }

        log_activity('Orange Money payment failed [Invoice: ' . $invoice->invoice_number . ']: ' . $response);
        return [
            'success' => false,
            'message' => $response_data['message'] ?? 'Erreur de connexion à Orange Money'
        ];
    }

    /**
     * Orange Money callback
     */
    public function orange_money_callback()
    {
        $patient = $this->get_logged_in_patient();

        $transaction_id = $this->input->get('transaction_id');
        $invoice_id = $this->input->get('invoice_id');
        $status = $this->input->get('status');

        if ($status == 'SUCCESS' && $transaction_id && $invoice_id) {
            $invoice = $this->dietetic_invoices_model->get($invoice_id);

            if ($invoice && $invoice->patient_id == $patient->id) {
                // Record payment
                $payment_data = [
                    'invoice_id' => $invoice_id,
                    'subscription_id' => $invoice->subscription_id,
                    'patient_id' => $invoice->patient_id,
                    'dietitian_id' => $invoice->dietitian_id,
                    'amount' => $invoice->total_amount,
                    'payment_method' => 'orange_money',
                    'payment_date' => date('Y-m-d H:i:s'),
                    'payment_reference' => $transaction_id,
                    'transaction_id' => $transaction_id,
                    'notes' => 'Orange Money Transaction ID: ' . $transaction_id,
                    'status' => 'completed'
                ];

                $payment_id = $this->dietetic_payments_model->add($payment_data);

                if ($payment_id) {
                    set_alert('success', 'Paiement Orange Money effectué avec succès!');
                    redirect(site_url('dietetic/portal/invoices'));
                    return;
                }
            }
        }

        set_alert('danger', 'Erreur lors du paiement Orange Money');
        redirect(site_url('dietetic/portal'));
    }

    /**
     * Orange Money webhook (for async notifications)
     */
    public function orange_money_webhook()
    {
        $raw_input = file_get_contents('php://input');
        log_activity('Orange Money webhook received: ' . $raw_input);

        $data = json_decode($raw_input, true);

        if ($data && isset($data['transaction_id'], $data['status'])) {
            // Process webhook data
            // This is for asynchronous payment status updates
            log_activity('Orange Money webhook processed [Transaction: ' . $data['transaction_id'] . ', Status: ' . $data['status'] . ']');
        }

        echo 'OK';
    }
}
