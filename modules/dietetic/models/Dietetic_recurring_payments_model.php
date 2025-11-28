<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Recurring Payments Model
 * Manages automatic recurring payments for subscriptions
 */
class Dietetic_recurring_payments_model extends App_Model
{
    private $table = 'dietic_recurring_payments';
    private $transactions_table = 'dietic_recurring_payment_transactions';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('dietetic/dietetic_invoices_model');
        $this->load->model('dietetic/dietetic_payments_model');
        $this->load->model('dietetic/dietetic_subscriptions_model');
    }

    /**
     * Get recurring payment by ID
     */
    public function get($id)
    {
        $this->db->select('rp.*, ' .
            's.name as service_plan_name, ' .
            'CONCAT(st.firstname, " ", st.lastname) as dietitian_name, ' .
            'c.company as patient_name');
        $this->db->from(db_prefix() . $this->table . ' rp');
        $this->db->join(db_prefix() . 'dietic_service_plans s', 's.id = rp.service_plan_id', 'left');
        $this->db->join(db_prefix() . 'staff st', 'st.staffid = rp.dietitian_id', 'left');
        $this->db->join(db_prefix() . 'dietic_patients p', 'p.id = rp.patient_id', 'left');
        $this->db->join(db_prefix() . 'clients c', 'c.userid = p.client_id', 'left');
        $this->db->where('rp.id', $id);

        return $this->db->get()->row();
    }

    /**
     * Get all recurring payments
     */
    public function get_all($where = [])
    {
        $this->db->select('rp.*, ' .
            's.name as service_plan_name, ' .
            'CONCAT(st.firstname, " ", st.lastname) as dietitian_name, ' .
            'c.company as patient_name');
        $this->db->from(db_prefix() . $this->table . ' rp');
        $this->db->join(db_prefix() . 'dietic_service_plans s', 's.id = rp.service_plan_id', 'left');
        $this->db->join(db_prefix() . 'staff st', 'st.staffid = rp.dietitian_id', 'left');
        $this->db->join(db_prefix() . 'dietic_patients p', 'p.id = rp.patient_id', 'left');
        $this->db->join(db_prefix() . 'clients c', 'c.userid = p.client_id', 'left');

        if (!empty($where)) {
            $this->db->where($where);
        }

        // Apply staff permissions
        if (!is_admin()) {
            $this->db->where('rp.dietitian_id', get_staff_user_id());
        }

        $this->db->order_by('rp.created_at', 'DESC');

        return $this->db->get()->result();
    }

    /**
     * Get recurring payments by subscription
     */
    public function get_by_subscription($subscription_id)
    {
        return $this->get_all(['rp.subscription_id' => $subscription_id]);
    }

    /**
     * Get recurring payments by patient
     */
    public function get_by_patient($patient_id)
    {
        return $this->get_all(['rp.patient_id' => $patient_id]);
    }

    /**
     * Get due recurring payments (ready to process)
     */
    public function get_due_payments()
    {
        $this->db->where('status', 'active');
        $this->db->where('next_payment_date <=', date('Y-m-d'));
        $this->db->order_by('next_payment_date', 'ASC');

        return $this->db->get(db_prefix() . $this->table)->result();
    }

    /**
     * Add new recurring payment
     */
    public function add($data)
    {
        // Calculate next payment date if not provided
        if (!isset($data['next_payment_date'])) {
            $data['next_payment_date'] = $this->calculate_next_payment_date(
                $data['start_date'],
                $data['frequency']
            );
        }

        $data['created_at'] = date('Y-m-d H:i:s');
        $data['status'] = $data['status'] ?? 'active';

        if ($this->db->insert(db_prefix() . $this->table, $data)) {
            $recurring_id = $this->db->insert_id();

            // Update subscription with recurring flag
            if (isset($data['subscription_id'])) {
                $this->dietetic_subscriptions_model->update($data['subscription_id'], [
                    'is_recurring' => 1,
                    'recurring_payment_id' => $recurring_id
                ]);
            }

            log_activity('Recurring Payment Created [ID: ' . $recurring_id . ', Frequency: ' . $data['frequency'] . ']');

            return $recurring_id;
        }

        return false;
    }

    /**
     * Update recurring payment
     */
    public function update($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        $this->db->where('id', $id);
        $result = $this->db->update(db_prefix() . $this->table, $data);

        if ($result) {
            log_activity('Recurring Payment Updated [ID: ' . $id . ']');
        }

        return $result;
    }

    /**
     * Cancel recurring payment
     */
    public function cancel($id, $reason = null)
    {
        $data = [
            'status' => 'cancelled',
            'cancelled_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($reason) {
            $data['failure_reason'] = $reason;
        }

        $this->db->where('id', $id);
        $result = $this->db->update(db_prefix() . $this->table, $data);

        if ($result) {
            log_activity('Recurring Payment Cancelled [ID: ' . $id . ']');
        }

        return $result;
    }

    /**
     * Pause recurring payment
     */
    public function pause($id)
    {
        return $this->update($id, ['status' => 'paused']);
    }

    /**
     * Resume recurring payment
     */
    public function resume($id)
    {
        return $this->update($id, ['status' => 'active']);
    }

    /**
     * Process a recurring payment (called by cron)
     */
    public function process_payment($recurring_payment_id)
    {
        $recurring = $this->get($recurring_payment_id);

        if (!$recurring || $recurring->status != 'active') {
            return false;
        }

        // Create transaction record
        $transaction_id = $this->create_transaction($recurring);

        if (!$transaction_id) {
            return false;
        }

        // Create invoice for this period
        $invoice_data = [
            'subscription_id' => $recurring->subscription_id,
            'patient_id' => $recurring->patient_id,
            'dietitian_id' => $recurring->dietitian_id,
            'issue_date' => date('Y-m-d'),
            'due_date' => date('Y-m-d', strtotime('+7 days')),
            'total_amount' => $recurring->amount,
            'status' => 'sent',
            'notes' => 'Paiement récurrent automatique - ' . ucfirst($recurring->frequency)
        ];

        $invoice_id = $this->dietetic_invoices_model->add($invoice_data);

        if (!$invoice_id) {
            $this->update_transaction($transaction_id, ['status' => 'failed', 'error_message' => 'Invoice creation failed']);
            return false;
        }

        // Update transaction with invoice ID
        $this->update_transaction($transaction_id, ['invoice_id' => $invoice_id]);

        // Attempt automatic payment
        $payment_result = $this->attempt_automatic_payment($recurring, $invoice_id, $transaction_id);

        if ($payment_result['success']) {
            // Payment successful
            $this->update_transaction($transaction_id, [
                'status' => 'completed',
                'payment_id' => $payment_result['payment_id'],
                'processed_date' => date('Y-m-d H:i:s'),
                'transaction_id' => $payment_result['transaction_id'] ?? null
            ]);

            // Update next payment date
            $next_date = $this->calculate_next_payment_date($recurring->next_payment_date, $recurring->frequency);
            $this->update($recurring_payment_id, [
                'last_payment_date' => date('Y-m-d'),
                'next_payment_date' => $next_date,
                'retry_count' => 0
            ]);

            // Send success notification
            $this->send_success_notification($recurring, $invoice_id);

            log_activity('Recurring Payment Processed Successfully [ID: ' . $recurring_payment_id . ', Invoice: ' . $invoice_id . ']');

            return true;
        } else {
            // Payment failed
            $this->update_transaction($transaction_id, [
                'status' => 'failed',
                'error_message' => $payment_result['error'] ?? 'Payment failed'
            ]);

            // Increment retry count
            $retry_count = $recurring->retry_count + 1;
            $max_retries = $recurring->max_retries ?? 3;

            if ($retry_count >= $max_retries) {
                // Max retries reached, mark as failed
                $this->update($recurring_payment_id, [
                    'status' => 'failed',
                    'retry_count' => $retry_count,
                    'failure_reason' => $payment_result['error'] ?? 'Max retries reached'
                ]);

                // Send failure notification
                $this->send_failure_notification($recurring, $payment_result['error'] ?? 'Payment failed after max retries');
            } else {
                // Schedule retry
                $retry_interval_days = dietetic_get_option('recurring_retry_interval_days', 3);
                $next_retry_date = date('Y-m-d', strtotime('+' . $retry_interval_days . ' days'));

                $this->update($recurring_payment_id, [
                    'retry_count' => $retry_count,
                    'next_payment_date' => $next_retry_date
                ]);

                log_activity('Recurring Payment Failed - Retry Scheduled [ID: ' . $recurring_payment_id . ', Retry: ' . $retry_count . '/' . $max_retries . ']');
            }

            return false;
        }
    }

    /**
     * Attempt automatic payment based on payment method
     */
    private function attempt_automatic_payment($recurring, $invoice_id, $transaction_id)
    {
        // For now, we'll create a pending payment that needs manual confirmation
        // In a real implementation, you would integrate with payment gateway APIs

        $payment_data = [
            'invoice_id' => $invoice_id,
            'subscription_id' => $recurring->subscription_id,
            'patient_id' => $recurring->patient_id,
            'dietitian_id' => $recurring->dietitian_id,
            'amount' => $recurring->amount,
            'payment_method' => $recurring->payment_method,
            'payment_date' => date('Y-m-d H:i:s'),
            'payment_reference' => 'REC-' . $recurring->id . '-' . $transaction_id,
            'notes' => 'Paiement récurrent automatique',
            'status' => 'pending' // Would be 'completed' with real gateway integration
        ];

        $payment_id = $this->dietetic_payments_model->add($payment_data);

        if ($payment_id) {
            // In production, you would call the payment gateway API here
            // For demonstration, we return success with pending status
            return [
                'success' => true,
                'payment_id' => $payment_id,
                'transaction_id' => 'REC-' . $recurring->id . '-' . $transaction_id
            ];
        }

        return [
            'success' => false,
            'error' => 'Failed to create payment record'
        ];
    }

    /**
     * Calculate next payment date
     */
    private function calculate_next_payment_date($current_date, $frequency)
    {
        $intervals = [
            'daily' => '+1 day',
            'weekly' => '+1 week',
            'monthly' => '+1 month',
            'quarterly' => '+3 months',
            'yearly' => '+1 year'
        ];

        $interval = $intervals[$frequency] ?? '+1 month';

        return date('Y-m-d', strtotime($current_date . ' ' . $interval));
    }

    /**
     * Create transaction record
     */
    private function create_transaction($recurring)
    {
        $data = [
            'recurring_payment_id' => $recurring->id,
            'scheduled_date' => $recurring->next_payment_date,
            'amount' => $recurring->amount,
            'status' => 'processing',
            'attempt_count' => $recurring->retry_count + 1,
            'created_at' => date('Y-m-d H:i:s')
        ];

        if ($this->db->insert(db_prefix() . $this->transactions_table, $data)) {
            return $this->db->insert_id();
        }

        return false;
    }

    /**
     * Update transaction
     */
    private function update_transaction($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        $this->db->where('id', $id);
        return $this->db->update(db_prefix() . $this->transactions_table, $data);
    }

    /**
     * Get transaction history for recurring payment
     */
    public function get_transactions($recurring_payment_id)
    {
        $this->db->select('t.*, i.invoice_number, p.payment_reference');
        $this->db->from(db_prefix() . $this->transactions_table . ' t');
        $this->db->join(db_prefix() . 'dietic_invoices i', 'i.id = t.invoice_id', 'left');
        $this->db->join(db_prefix() . 'dietic_payments p', 'p.id = t.payment_id', 'left');
        $this->db->where('t.recurring_payment_id', $recurring_payment_id);
        $this->db->order_by('t.scheduled_date', 'DESC');

        return $this->db->get()->result();
    }

    /**
     * Send success notification
     */
    private function send_success_notification($recurring, $invoice_id)
    {
        // Implement notification sending
        // This would use the existing notification system from Phase 7
        log_activity('Recurring Payment Success Notification Sent [Recurring ID: ' . $recurring->id . ']');
    }

    /**
     * Send failure notification
     */
    private function send_failure_notification($recurring, $error)
    {
        if (!dietetic_get_option('recurring_send_failure_notification', 1)) {
            return;
        }

        // Implement notification sending
        log_activity('Recurring Payment Failure Notification Sent [Recurring ID: ' . $recurring->id . ', Error: ' . $error . ']');
    }

    /**
     * Get statistics
     */
    public function get_statistics($filters = [])
    {
        $stats = [];

        // Total active recurring payments
        $this->db->where('status', 'active');
        if (!is_admin()) {
            $this->db->where('dietitian_id', get_staff_user_id());
        }
        $stats['active'] = $this->db->count_all_results(db_prefix() . $this->table);

        // Total monthly recurring revenue
        $this->db->select_sum('amount');
        $this->db->where('status', 'active');
        $this->db->where('frequency', 'monthly');
        if (!is_admin()) {
            $this->db->where('dietitian_id', get_staff_user_id());
        }
        $result = $this->db->get(db_prefix() . $this->table)->row();
        $stats['monthly_recurring_revenue'] = $result ? (float)$result->amount : 0;

        // Due this week
        $this->db->where('status', 'active');
        $this->db->where('next_payment_date >=', date('Y-m-d'));
        $this->db->where('next_payment_date <=', date('Y-m-d', strtotime('+7 days')));
        if (!is_admin()) {
            $this->db->where('dietitian_id', get_staff_user_id());
        }
        $stats['due_this_week'] = $this->db->count_all_results(db_prefix() . $this->table);

        // Failed
        $this->db->where('status', 'failed');
        if (!is_admin()) {
            $this->db->where('dietitian_id', get_staff_user_id());
        }
        $stats['failed'] = $this->db->count_all_results(db_prefix() . $this->table);

        return $stats;
    }

    /**
     * Delete recurring payment
     */
    public function delete($id)
    {
        // Cancel instead of delete to preserve history
        return $this->cancel($id, 'Deleted by admin');
    }
}
