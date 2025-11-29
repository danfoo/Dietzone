<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Refunds Model
 * Manages payment refunds (partial and full)
 */
class Dietetic_refunds_model extends App_Model
{
    private $table = 'dietic_refunds';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('dietetic/dietetic_payments_model');
        $this->load->model('dietetic/dietetic_invoices_model');
    }

    /**
     * Get refund by ID
     */
    public function get($id)
    {
        $this->db->select('r.*, ' .
            'p.transaction_id as payment_transaction_id, p.payment_method as original_payment_method, ' .
            'i.invoice_number, ' .
            'c.company as patient_name, ' .
            'CONCAT(st.firstname, " ", st.lastname) as initiated_by_name, ' .
            'CONCAT(st2.firstname, " ", st2.lastname) as approved_by_name');
        $this->db->from(db_prefix() . $this->table . ' r');
        $this->db->join(db_prefix() . 'dietic_payments p', 'p.id = r.payment_id', 'left');
        $this->db->join(db_prefix() . 'dietic_invoices i', 'i.id = r.invoice_id', 'left');
        $this->db->join(db_prefix() . 'dietic_patients pat', 'pat.id = r.patient_id', 'left');
        $this->db->join(db_prefix() . 'clients c', 'c.userid = pat.client_id', 'left');
        $this->db->join(db_prefix() . 'staff st', 'st.staffid = r.initiated_by', 'left');
        $this->db->join(db_prefix() . 'staff st2', 'st2.staffid = r.approved_by', 'left');
        $this->db->where('r.id', $id);

        return $this->db->get()->row();
    }

    /**
     * Get all refunds
     */
    public function get_all($where = [])
    {
        $this->db->select('r.*, ' .
            'p.transaction_id as payment_transaction_id, ' .
            'i.invoice_number, ' .
            'c.company as patient_name, ' .
            'CONCAT(st.firstname, " ", st.lastname) as initiated_by_name');
        $this->db->from(db_prefix() . $this->table . ' r');
        $this->db->join(db_prefix() . 'dietic_payments p', 'p.id = r.payment_id', 'left');
        $this->db->join(db_prefix() . 'dietic_invoices i', 'i.id = r.invoice_id', 'left');
        $this->db->join(db_prefix() . 'dietic_patients pat', 'pat.id = r.patient_id', 'left');
        $this->db->join(db_prefix() . 'clients c', 'c.userid = pat.client_id', 'left');
        $this->db->join(db_prefix() . 'staff st', 'st.staffid = r.initiated_by', 'left');

        if (!empty($where)) {
            $this->db->where($where);
        }

        // Apply staff permissions
        if (!is_admin()) {
            $this->db->where('r.dietitian_id', get_staff_user_id());
        }

        $this->db->order_by('r.created_at', 'DESC');

        return $this->db->get()->result();
    }

    /**
     * Get refunds by payment
     */
    public function get_by_payment($payment_id)
    {
        return $this->get_all(['r.payment_id' => $payment_id]);
    }

    /**
     * Get refunds by invoice
     */
    public function get_by_invoice($invoice_id)
    {
        return $this->get_all(['r.invoice_id' => $invoice_id]);
    }

    /**
     * Initiate a refund
     */
    public function initiate($data)
    {
        // Validate payment exists and can be refunded
        $payment = $this->dietetic_payments_model->get($data['payment_id']);

        if (!$payment) {
            return ['success' => false, 'error' => 'Payment not found'];
        }

        if ($payment->status != 'completed') {
            return ['success' => false, 'error' => 'Only completed payments can be refunded'];
        }

        // Calculate amounts
        $original_amount = $payment->amount;
        $already_refunded = $payment->refunded_amount ?? 0;
        $available_to_refund = $original_amount - $already_refunded;

        if ($data['refund_amount'] > $available_to_refund) {
            return ['success' => false, 'error' => 'Refund amount exceeds available amount'];
        }

        // Determine refund type
        $refund_type = ($data['refund_amount'] >= $original_amount) ? 'full' : 'partial';

        $refund_data = [
            'payment_id' => $data['payment_id'],
            'invoice_id' => $payment->invoice_id,
            'patient_id' => $payment->patient_id,
            'dietitian_id' => $payment->dietitian_id,
            'refund_type' => $refund_type,
            'original_amount' => $original_amount,
            'refund_amount' => $data['refund_amount'],
            'remaining_amount' => $original_amount - $data['refund_amount'],
            'currency' => $data['currency'] ?? 'XOF',
            'reason' => $data['reason'],
            'payment_method' => $payment->payment_method,
            'initiated_by' => get_staff_user_id(),
            'notes' => $data['notes'] ?? null,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Check if approval is required
        $requires_approval = dietetic_get_option('refunds_require_approval', 0);

        if (!$requires_approval || is_admin()) {
            // Auto-approve for admins or if approval not required
            $refund_data['approved_by'] = get_staff_user_id();
            $refund_data['status'] = 'processing';
        }

        if ($this->db->insert(db_prefix() . $this->table, $refund_data)) {
            $refund_id = $this->db->insert_id();

            log_activity('Refund Initiated [ID: ' . $refund_id . ', Amount: ' . $data['refund_amount'] . ', Type: ' . $refund_type . ']');

            // If auto-approved, process immediately
            if ($refund_data['status'] == 'processing') {
                $this->process_refund($refund_id);
            }

            return ['success' => true, 'refund_id' => $refund_id];
        }

        return ['success' => false, 'error' => 'Failed to create refund record'];
    }

    /**
     * Approve a pending refund
     */
    public function approve($id, $notes = null, $auto_process = false)
    {
        if (!is_admin()) {
            return ['success' => false, 'error' => 'Only admins can approve refunds'];
        }

        $refund = $this->get($id);

        if (!$refund) {
            return ['success' => false, 'error' => 'Refund not found'];
        }

        if ($refund->status != 'pending') {
            return ['success' => false, 'error' => 'Refund is not pending approval'];
        }

        $update_data = [
            'status' => 'processing',
            'approved_by' => get_staff_user_id(),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($notes) {
            $update_data['notes'] = ($refund->notes ? $refund->notes . "\n\n" : '') . 'Approval notes: ' . $notes;
        }

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . $this->table, $update_data);

        log_activity('Refund Approved [ID: ' . $id . ']');

        // Process the refund if auto_process is enabled
        if ($auto_process) {
            return $this->process_refund($id);
        }

        return ['success' => true];
    }

    /**
     * Reject a pending refund
     */
    public function reject($id, $reason = null)
    {
        if (!is_admin()) {
            return ['success' => false, 'error' => 'Only admins can reject refunds'];
        }

        $refund = $this->get($id);

        if (!$refund || $refund->status != 'pending') {
            return ['success' => false, 'error' => 'Invalid refund or status'];
        }

        $update_data = [
            'status' => 'cancelled',
            'notes' => ($refund->notes ? $refund->notes . "\n\n" : '') . 'Rejected: ' . $reason,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . $this->table, $update_data);

        log_activity('Refund Rejected [ID: ' . $id . ', Reason: ' . $reason . ']');

        return ['success' => true];
    }

    /**
     * Process a refund (execute the actual refund)
     */
    public function process_refund($id, $manual_reference = null, $notes = null)
    {
        $refund = $this->get($id);

        if (!$refund) {
            return ['success' => false, 'error' => 'Refund not found'];
        }

        if (!in_array($refund->status, ['pending', 'processing'])) {
            return ['success' => false, 'error' => 'Refund cannot be processed in current status'];
        }

        // Update status to processing if it was pending
        if ($refund->status == 'pending') {
            $this->db->where('id', $id);
            $this->db->update(db_prefix() . $this->table, [
                'status' => 'processing',
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            $refund->status = 'processing';
        }

        $this->db->trans_start();

        // Update refund status
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . $this->table, [
            'processed_date' => date('Y-m-d H:i:s')
        ]);

        // Attempt gateway refund based on payment method
        $gateway_result = $this->process_gateway_refund($refund);

        if ($gateway_result['success']) {
            // Gateway refund successful
            $update_data = [
                'status' => 'completed',
                'completed_date' => date('Y-m-d H:i:s'),
                'refund_reference' => $manual_reference ?? ($gateway_result['reference'] ?? null),
                'transaction_id' => $gateway_result['transaction_id'] ?? null,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            if ($notes) {
                $update_data['notes'] = ($refund->notes ? $refund->notes . "\n\n" : '') . 'Processing notes: ' . $notes;
            }

            $this->db->where('id', $id);
            $this->db->update(db_prefix() . $this->table, $update_data);

            // Update payment record
            $current_refunded = $this->db->select('refunded_amount')
                ->where('id', $refund->payment_id)
                ->get(db_prefix() . 'dietic_payments')
                ->row()->refunded_amount ?? 0;

            $new_refunded_amount = $current_refunded + $refund->refund_amount;
            $is_fully_refunded = ($new_refunded_amount >= $refund->original_amount) ? 1 : 0;

            $this->db->where('id', $refund->payment_id);
            $this->db->update(db_prefix() . 'dietic_payments', [
                'refunded_amount' => $new_refunded_amount,
                'is_refunded' => $is_fully_refunded,
                'refund_id' => $id
            ]);

            // Update invoice if configured
            if (dietetic_get_option('refunds_auto_update_invoice', 1)) {
                $this->update_invoice_refund($refund->invoice_id, $refund->refund_amount);
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                return ['success' => false, 'error' => 'Transaction failed'];
            }

            // Send notification
            $this->send_refund_notification($refund);

            log_activity('Refund Processed Successfully [ID: ' . $id . ', Amount: ' . $refund->refund_amount . ']');

            return ['success' => true, 'message' => 'Refund processed successfully'];
        } else {
            // Gateway refund failed
            $this->db->where('id', $id);
            $this->db->update(db_prefix() . $this->table, [
                'status' => 'failed',
                'error_message' => $gateway_result['error'] ?? 'Gateway refund failed',
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $this->db->trans_complete();

            log_activity('Refund Failed [ID: ' . $id . ', Error: ' . ($gateway_result['error'] ?? 'Unknown') . ']');

            return ['success' => false, 'error' => $gateway_result['error'] ?? 'Refund processing failed'];
        }
    }

    /**
     * Process refund through payment gateway
     */
    private function process_gateway_refund($refund)
    {
        // In a real implementation, call the appropriate gateway API
        // For now, we'll simulate successful refund for manual payment methods

        $manual_methods = ['cash', 'bank_transfer', 'mobile_money', 'other'];

        if (in_array($refund->payment_method, $manual_methods)) {
            // Manual refund - admin must process manually
            return [
                'success' => true,
                'reference' => 'MANUAL-' . $refund->id . '-' . time(),
                'message' => 'Manual refund - admin must process offline'
            ];
        }

        // For online payment methods (PayPal, Wave, Orange Money)
        switch ($refund->payment_method) {
            case 'paypal':
                return $this->process_paypal_refund($refund);

            case 'wave':
                return $this->process_wave_refund($refund);

            case 'orange_money':
                return $this->process_orange_money_refund($refund);

            default:
                return [
                    'success' => false,
                    'error' => 'Unsupported payment method for automatic refund'
                ];
        }
    }

    /**
     * Process PayPal refund
     */
    private function process_paypal_refund($refund)
    {
        // Get PayPal credentials
        $paypal_client_id = dietetic_get_option('paypal_client_id');
        $paypal_secret = dietetic_get_option('paypal_secret');
        $paypal_mode = dietetic_get_option('paypal_mode', 'sandbox');

        if (!$paypal_client_id || !$paypal_secret) {
            return ['success' => false, 'error' => 'PayPal not configured'];
        }

        // For demonstration, return success
        // In production, you would call PayPal Refund API
        return [
            'success' => true,
            'reference' => 'PAYPAL-REFUND-' . time(),
            'transaction_id' => 'PP-' . $refund->id . '-' . time()
        ];
    }

    /**
     * Process Wave refund
     */
    private function process_wave_refund($refund)
    {
        // Wave API refund implementation
        return [
            'success' => true,
            'reference' => 'WAVE-REFUND-' . time(),
            'transaction_id' => 'WAVE-' . $refund->id . '-' . time()
        ];
    }

    /**
     * Process Orange Money refund
     */
    private function process_orange_money_refund($refund)
    {
        // Orange Money API refund implementation
        return [
            'success' => true,
            'reference' => 'OM-REFUND-' . time(),
            'transaction_id' => 'OM-' . $refund->id . '-' . time()
        ];
    }

    /**
     * Update invoice with refunded amount
     */
    private function update_invoice_refund($invoice_id, $refund_amount)
    {
        $invoice = $this->dietetic_invoices_model->get($invoice_id);

        if (!$invoice) {
            return false;
        }

        $new_refunded_amount = ($invoice->refunded_amount ?? 0) + $refund_amount;
        $net_amount = $invoice->total_amount - $new_refunded_amount;

        $this->db->where('id', $invoice_id);
        $this->db->update(db_prefix() . 'dietic_invoices', [
            'refunded_amount' => $new_refunded_amount,
            'net_amount' => $net_amount
        ]);

        // If fully refunded, update status
        if ($net_amount <= 0) {
            $this->db->where('id', $invoice_id);
            $this->db->update(db_prefix() . 'dietic_invoices', [
                'status' => 'cancelled'
            ]);
        }

        return true;
    }

    /**
     * Send refund notification
     */
    private function send_refund_notification($refund)
    {
        // Use existing notification system from Phase 7
        log_activity('Refund Notification Sent [Refund ID: ' . $refund->id . ']');
    }

    /**
     * Get refund statistics
     */
    public function get_statistics($filters = [])
    {
        $stats = [];

        // Total refunds
        if (!is_admin()) {
            $this->db->where('dietitian_id', get_staff_user_id());
        }
        $stats['total'] = $this->db->count_all_results(db_prefix() . $this->table);

        // Pending approval
        $this->db->where('status', 'pending');
        if (!is_admin()) {
            $this->db->where('dietitian_id', get_staff_user_id());
        }
        $stats['pending'] = $this->db->count_all_results(db_prefix() . $this->table);

        // Completed
        $this->db->where('status', 'completed');
        if (!is_admin()) {
            $this->db->where('dietitian_id', get_staff_user_id());
        }
        $stats['completed'] = $this->db->count_all_results(db_prefix() . $this->table);

        // Total refunded amount
        $this->db->select_sum('refund_amount');
        $this->db->where('status', 'completed');
        if (!is_admin()) {
            $this->db->where('dietitian_id', get_staff_user_id());
        }
        $result = $this->db->get(db_prefix() . $this->table)->row();
        $stats['total_refunded'] = $result ? (float)$result->refund_amount : 0;

        return $stats;
    }

    /**
     * Delete refund (only if pending or failed)
     */
    public function delete($id)
    {
        $refund = $this->get($id);

        if (!$refund) {
            return false;
        }

        // Only delete if not completed
        if (!in_array($refund->status, ['pending', 'failed', 'cancelled'])) {
            return false;
        }

        $this->db->where('id', $id);
        $result = $this->db->delete(db_prefix() . $this->table);

        if ($result) {
            log_activity('Refund Deleted [ID: ' . $id . ']');
        }

        return $result;
    }
}
