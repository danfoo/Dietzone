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

        // TODO: Send payment notification

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
}
