<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dietetic_payments_model extends App_Model
{
    private $table = 'dietic_payments';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('dietetic/dietetic_invoices_model');
        $this->load->model('dietetic/dietetic_revenue_shares_model');
    }

    /**
     * Get payment by ID
     *
     * @param int $id
     * @return object|null
     */
    public function get($id)
    {
        $this->db->select('p.*, ' .
            'i.invoice_number, i.total_amount as invoice_total, ' .
            'pat.company as patient_name, ' .
            'CONCAT(st.firstname, " ", st.lastname) as dietitian_name, ' .
            'CONCAT(rec.firstname, " ", rec.lastname) as recorded_by_name');
        $this->db->from(db_prefix() . $this->table . ' p');
        $this->db->join(db_prefix() . 'dietic_invoices i', 'i.id = p.invoice_id', 'left');
        $this->db->join(db_prefix() . 'dietic_patients pat', 'pat.id = p.patient_id', 'left');
        $this->db->join(db_prefix() . 'staff st', 'st.staffid = i.dietitian_id', 'left');
        $this->db->join(db_prefix() . 'staff rec', 'rec.staffid = p.recorded_by', 'left');
        $this->db->where('p.id', $id);

        return $this->db->get()->row();
    }

    /**
     * Get all payments
     *
     * @param array $where
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function get_all($where = [], $limit = null, $offset = null)
    {
        $this->db->select('p.*, ' .
            'i.invoice_number, ' .
            'pat.company as patient_name, ' .
            'CONCAT(st.firstname, " ", st.lastname) as dietitian_name');
        $this->db->from(db_prefix() . $this->table . ' p');
        $this->db->join(db_prefix() . 'dietic_invoices i', 'i.id = p.invoice_id', 'left');
        $this->db->join(db_prefix() . 'dietic_patients pat', 'pat.id = p.patient_id', 'left');
        $this->db->join(db_prefix() . 'staff st', 'st.staffid = i.dietitian_id', 'left');

        if (!empty($where)) {
            $this->db->where($where);
        }

        // Apply staff permissions
        if (!is_admin()) {
            $this->db->where('i.dietitian_id', get_staff_user_id());
        }

        $this->db->order_by('p.payment_date', 'DESC');

        if ($limit !== null) {
            $this->db->limit($limit, $offset);
        }

        return $this->db->get()->result();
    }

    /**
     * Get payments by invoice
     *
     * @param int $invoice_id
     * @return array
     */
    public function get_by_invoice($invoice_id)
    {
        return $this->get_all(['p.invoice_id' => $invoice_id]);
    }

    /**
     * Get payments by patient
     *
     * @param int $patient_id
     * @return array
     */
    public function get_by_patient($patient_id)
    {
        return $this->get_all(['p.patient_id' => $patient_id]);
    }

    /**
     * Count all payments
     *
     * @param array $where
     * @return int
     */
    public function count_all($where = [])
    {
        $this->db->from(db_prefix() . $this->table . ' p');
        $this->db->join(db_prefix() . 'dietic_invoices i', 'i.id = p.invoice_id', 'left');

        if (!empty($where)) {
            $this->db->where($where);
        }

        // Apply staff permissions
        if (!is_admin()) {
            $this->db->where('i.dietitian_id', get_staff_user_id());
        }

        return $this->db->count_all_results();
    }

    /**
     * Record new payment
     *
     * @param array $data
     * @return int|bool
     */
    public function add($data)
    {
        $data['recorded_by'] = get_staff_user_id();
        $data['created_at'] = date('Y-m-d H:i:s');

        // Start transaction
        $this->db->trans_start();

        // Insert payment
        $this->db->insert(db_prefix() . $this->table, $data);
        $payment_id = $this->db->insert_id();

        if (!$payment_id) {
            $this->db->trans_rollback();
            return false;
        }

        // If payment is completed, update invoice and create revenue share
        if ($data['status'] == 'completed') {
            $invoice = $this->dietetic_invoices_model->get($data['invoice_id']);

            if ($invoice) {
                // Mark invoice as paid
                $this->dietetic_invoices_model->mark_as_paid($data['invoice_id'], $data['payment_method']);

                // Create revenue share entry
                $this->load->model('dietetic/dietetic_commission_settings_model');
                $this->load->model('dietetic/dietetic_subscriptions_model');

                $subscription = $this->dietetic_subscriptions_model->get($invoice->subscription_id);

                if ($subscription) {
                    // Get active commission setting for this referral source
                    $commission_setting = $this->dietetic_commission_settings_model->get_active_for_source($subscription->referral_source);

                    if ($commission_setting) {
                        $revenue_data = [
                            'invoice_id' => $invoice->id,
                            'payment_id' => $payment_id,
                            'subscription_id' => $invoice->subscription_id,
                            'dietitian_id' => $invoice->dietitian_id,
                            'patient_id' => $invoice->patient_id,
                            'total_amount' => $invoice->total_amount,
                            'dietitian_percentage' => $commission_setting->dietitian_percentage,
                            'platform_percentage' => $commission_setting->platform_percentage,
                            'dietitian_share' => $invoice->total_amount * ($commission_setting->dietitian_percentage / 100),
                            'platform_share' => $invoice->total_amount * ($commission_setting->platform_percentage / 100),
                            'referral_source' => $subscription->referral_source,
                            'commission_setting_id' => $commission_setting->id,
                            'status' => 'pending'
                        ];

                        $this->load->model('dietetic/dietetic_revenue_shares_model');
                        $this->dietetic_revenue_shares_model->add($revenue_data);
                    }
                }
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return false;
        }

        log_activity('New Payment Recorded [ID: ' . $payment_id . ', Amount: ' . $data['amount'] . ']');

        // Send payment notifications (email and SMS)
        if ($data['status'] == 'completed') {
            $this->send_payment_notifications($payment_id);
        }

        return $payment_id;
    }

    /**
     * Update payment
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        $this->db->where('id', $id);
        $result = $this->db->update(db_prefix() . $this->table, $data);

        if ($result) {
            log_activity('Payment Updated [ID: ' . $id . ']');
        }

        return $result;
    }

    /**
     * Get payment statistics
     *
     * @param array $filters
     * @return array
     */
    public function get_statistics($filters = [])
    {
        $stats = [];

        // Total payments
        $stats['total'] = $this->count_all(['p.status' => 'completed']);

        // Total amount
        $this->db->select_sum('amount');
        $this->db->from(db_prefix() . $this->table . ' p');
        $this->db->join(db_prefix() . 'dietic_invoices i', 'i.id = p.invoice_id', 'left');
        $this->db->where('p.status', 'completed');
        if (!is_admin()) {
            $this->db->where('i.dietitian_id', get_staff_user_id());
        }
        $result = $this->db->get()->row();
        $stats['total_amount'] = $result ? (float)$result->amount : 0;

        // By payment method
        $payment_methods = ['card', 'bank_transfer', 'cash', 'mobile_money', 'paypal', 'wave', 'orange_money'];
        foreach ($payment_methods as $method) {
            $stats['by_method'][$method] = $this->count_all(['p.payment_method' => $method, 'p.status' => 'completed']);
        }

        // Pending payments
        $stats['pending'] = $this->count_all(['p.status' => 'pending']);

        return $stats;
    }

    /**
     * Get total paid for invoice
     *
     * @param int $invoice_id
     * @return float
     */
    public function get_total_paid_for_invoice($invoice_id)
    {
        $this->db->select_sum('amount');
        $this->db->where('invoice_id', $invoice_id);
        $this->db->where('status', 'completed');
        $result = $this->db->get(db_prefix() . $this->table)->row();

        return $result ? (float)$result->amount : 0;
    }

    /**
     * Delete payment
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $payment = $this->get($id);

        if (!$payment) {
            return false;
        }

        // Only delete if pending or failed
        if (!in_array($payment->status, ['pending', 'failed'])) {
            return false;
        }

        $this->db->where('id', $id);
        $result = $this->db->delete(db_prefix() . $this->table);

        if ($result) {
            log_activity('Payment Deleted [ID: ' . $id . ']');
        }

        return $result;
    }

    /**
     * Send payment confirmation notifications (Email and SMS)
     *
     * @param int $payment_id
     * @return bool
     */
    private function send_payment_notifications($payment_id)
    {
        $payment = $this->get($payment_id);

        if (!$payment) {
            return false;
        }

        // Get invoice details
        $invoice = $this->dietetic_invoices_model->get($payment->invoice_id);

        if (!$invoice) {
            return false;
        }

        // Get patient contact information
        $this->db->select('p.*, c.company as patient_name, c.phonenumber as phone, c.userid');
        $this->db->from(db_prefix() . 'dietic_patients p');
        $this->db->join(db_prefix() . 'clients c', 'c.userid = p.client_id', 'left');
        $this->db->where('p.id', $invoice->patient_id);
        $patient = $this->db->get()->row();

        if (!$patient) {
            return false;
        }

        // Get primary contact email
        $this->db->select('email');
        $this->db->from(db_prefix() . 'contacts');
        $this->db->where('userid', $patient->userid);
        $this->db->where('is_primary', 1);
        $contact = $this->db->get()->row();

        $email = $contact && $contact->email ? $contact->email : null;
        $phone = $patient->phone;

        $sent_email = false;
        $sent_sms = false;

        // Send Email Notification
        if ($email) {
            $sent_email = $this->send_payment_email($payment, $invoice, $patient, $email);
        }

        // Send SMS Notification
        if ($phone && function_exists('dietetic_send_sms')) {
            $sent_sms = $this->send_payment_sms($payment, $invoice, $patient, $phone);
        }

        // Log results
        if ($sent_email || $sent_sms) {
            $channels = [];
            if ($sent_email) $channels[] = 'Email';
            if ($sent_sms) $channels[] = 'SMS';
            log_activity('Payment Notification Sent [Payment ID: ' . $payment_id . ', Channels: ' . implode(', ', $channels) . ']');
            return true;
        } else {
            log_activity('Payment Notification Failed [Payment ID: ' . $payment_id . ']');
            return false;
        }
    }

    /**
     * Send payment confirmation email
     *
     * @param object $payment
     * @param object $invoice
     * @param object $patient
     * @param string $email
     * @return bool
     */
    private function send_payment_email($payment, $invoice, $patient, $email)
    {
        try {
            $this->load->library('email');

            $patient_name = $patient->patient_name ?: 'Client';
            $company_name = get_option('companyname');
            $company_logo = get_option('company_logo');
            $logo_url = $company_logo ? base_url('uploads/company/' . $company_logo) : '';

            $subject = 'Confirmation de paiement - Facture ' . $invoice->invoice_number;

            // Build HTML email
            $html = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { text-align: center; margin-bottom: 30px; }
                    .logo { max-width: 200px; margin-bottom: 20px; }
                    .content { background: #f0f8f0; padding: 20px; border-radius: 5px; border-left: 4px solid #4CAF50; }
                    .success-icon { color: #4CAF50; font-size: 48px; text-align: center; margin-bottom: 20px; }
                    .payment-details { margin: 20px 0; background: white; padding: 15px; border-radius: 5px; }
                    .payment-details table { width: 100%; border-collapse: collapse; }
                    .payment-details td { padding: 8px; border-bottom: 1px solid #ddd; }
                    .payment-details td:first-child { font-weight: bold; width: 40%; }
                    .amount { font-size: 24px; color: #4CAF50; font-weight: bold; text-align: center; margin: 20px 0; }
                    .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #666; }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">';

            if ($logo_url) {
                $html .= '<img src="' . $logo_url . '" alt="' . $company_name . '" class="logo">';
            }

            $html .= '
                        <h2>' . $company_name . '</h2>
                    </div>

                    <div class="content">
                        <div class="success-icon">✓</div>

                        <h2 style="text-align: center; color: #4CAF50;">Paiement Confirmé</h2>

                        <p>Bonjour <strong>' . htmlspecialchars($patient_name) . '</strong>,</p>

                        <p>Nous vous confirmons la bonne réception de votre paiement.</p>

                        <div class="amount">' . number_format($payment->amount, 0, ',', ' ') . ' FCFA</div>

                        <div class="payment-details">
                            <table>
                                <tr>
                                    <td>Référence de paiement</td>
                                    <td>' . htmlspecialchars($payment->payment_reference ?: 'N/A') . '</td>
                                </tr>
                                <tr>
                                    <td>Facture</td>
                                    <td>' . htmlspecialchars($invoice->invoice_number) . '</td>
                                </tr>
                                <tr>
                                    <td>Date de paiement</td>
                                    <td>' . date('d/m/Y à H:i', strtotime($payment->payment_date)) . '</td>
                                </tr>
                                <tr>
                                    <td>Méthode de paiement</td>
                                    <td>' . htmlspecialchars($this->get_payment_method_label($payment->payment_method)) . '</td>
                                </tr>
                                <tr>
                                    <td>Statut</td>
                                    <td style="color: #4CAF50; font-weight: bold;">✓ Paiement validé</td>
                                </tr>
                            </table>
                        </div>

                        <p>Merci pour votre confiance.</p>

                        <p>Cordialement,<br>
                        <strong>L\'équipe ' . $company_name . '</strong></p>
                    </div>

                    <div class="footer">
                        <p>' . get_option('company_address') . '</p>
                        <p>Email: ' . get_option('smtp_email') . ' | Tél: ' . get_option('company_phonenumber') . '</p>
                        <p>&copy; ' . date('Y') . ' ' . $company_name . '. Tous droits réservés.</p>
                    </div>
                </div>
            </body>
            </html>';

            $config = [
                'mailtype' => 'html',
                'charset' => 'utf-8',
                'newline' => "\r\n"
            ];

            $this->email->initialize($config);
            $this->email->from(get_option('smtp_email'), $company_name);
            $this->email->to($email);
            $this->email->subject($subject);
            $this->email->message($html);

            return $this->email->send();
        } catch (Exception $e) {
            log_activity('Payment Email Exception [Payment ID: ' . $payment->id . ']: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send payment confirmation SMS
     *
     * @param object $payment
     * @param object $invoice
     * @param object $patient
     * @param string $phone
     * @return bool
     */
    private function send_payment_sms($payment, $invoice, $patient, $phone)
    {
        $company_name = get_option('companyname');

        $message = sprintf(
            "%s: Paiement de %s FCFA reçu pour la facture %s. Merci!",
            $company_name,
            number_format($payment->amount, 0, ',', ' '),
            $invoice->invoice_number
        );

        $result = dietetic_send_sms($phone, $message);

        return $result['success'];
    }

    /**
     * Get payment method label in French
     *
     * @param string $method
     * @return string
     */
    private function get_payment_method_label($method)
    {
        $methods = [
            'cash' => 'Espèces',
            'bank_transfer' => 'Virement bancaire',
            'card' => 'Carte bancaire',
            'mobile_money' => 'Mobile Money',
            'paypal' => 'PayPal',
            'wave' => 'Wave',
            'orange_money' => 'Orange Money',
            'other' => 'Autre'
        ];

        return isset($methods[$method]) ? $methods[$method] : ucfirst(str_replace('_', ' ', $method));
    }
}
