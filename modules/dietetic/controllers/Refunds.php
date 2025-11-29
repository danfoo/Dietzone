<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Refunds Controller
 * Admin interface for managing payment refunds
 *
 * Phase 9 - Recurring Payments & Refunds
 */
class Refunds extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('dietetic/dietetic_refunds_model');
        $this->load->model('dietetic/dietetic_payments_model');
        $this->load->model('dietetic/dietetic_invoices_model');
        $this->load->model('dietetic/dietetic_patients_model');
    }

    /**
     * List all refunds
     */
    public function index()
    {
        if (!has_permission('dietetic', '', 'view')) {
            access_denied('dietetic');
        }

        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('dietetic_refunds');
        }

        // Build where clause based on status filter
        $where = [];
        $status_filter = $this->input->get('status');
        if ($status_filter) {
            $where['r.status'] = $status_filter;
        }

        // Get refunds list
        $data['refunds'] = $this->dietetic_refunds_model->get_all($where);

        $data['title'] = _l('refunds');

        // Count pending refunds manually
        $pending_refunds = $this->dietetic_refunds_model->get_all(['r.status' => 'pending']);
        $data['pending_count'] = count($pending_refunds);

        $this->load->view('admin/refunds/manage', $data);
    }

    /**
     * View refund details
     */
    public function view($id)
    {
        if (!has_permission('dietetic', '', 'view')) {
            access_denied('dietetic');
        }

        $refund = $this->dietetic_refunds_model->get($id);

        if (!$refund) {
            show_404();
        }

        // Get related data
        $data['refund'] = $refund;
        $data['payment'] = $this->dietetic_payments_model->get($refund->payment_id);
        $data['invoice'] = $this->dietetic_invoices_model->get($refund->invoice_id);
        $data['patient'] = $this->dietetic_patients_model->get($refund->patient_id);
        $data['title'] = _l('refund') . ' #' . $refund->id;

        $this->load->view('admin/refunds/view', $data);
    }

    /**
     * Initiate new refund
     */
    public function create($payment_id = null)
    {
        if (!has_permission('dietetic', '', 'create')) {
            access_denied('dietetic');
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            $data['initiated_by'] = get_staff_user_id();

            $result = $this->dietetic_refunds_model->initiate($data);

            if ($result['success']) {
                set_alert('success', _l('refund_initiated_successfully'));
                redirect(admin_url('dietetic/refunds/view/' . $result['refund_id']));
            } else {
                set_alert('danger', $result['error'] ?? _l('refund_initiation_failed'));
            }
        }

        // Prepare form data
        $data['payment'] = null;
        $data['invoice'] = null;
        $data['patient'] = null;

        if ($payment_id) {
            $data['payment'] = $this->dietetic_payments_model->get($payment_id);
            if ($data['payment']) {
                $data['invoice'] = $this->dietetic_invoices_model->get($data['payment']->invoice_id);
                $data['patient'] = $this->dietetic_patients_model->get($data['payment']->patient_id);
            }
        }

        $data['title'] = _l('initiate_refund');
        $this->load->view('admin/refunds/form', $data);
    }

    /**
     * Approve refund
     */
    public function approve($id)
    {
        if (!has_permission('dietetic', '', 'edit')) {
            access_denied('dietetic');
        }

        if ($this->input->post()) {
            $notes = $this->input->post('approval_notes');
            $auto_process = $this->input->post('auto_process') == '1';

            $result = $this->dietetic_refunds_model->approve($id, $notes, $auto_process);

            if ($result['success']) {
                set_alert('success', _l('refund_approved_successfully'));
            } else {
                set_alert('danger', $result['error'] ?? _l('refund_approval_failed'));
            }

            redirect(admin_url('dietetic/refunds/view/' . $id));
        }

        $data['refund'] = $this->dietetic_refunds_model->get($id);
        $data['payment'] = $this->dietetic_payments_model->get($data['refund']->payment_id);
        $data['title'] = _l('approve_refund');

        $this->load->view('admin/refunds/approve', $data);
    }

    /**
     * Reject refund
     */
    public function reject($id)
    {
        if (!has_permission('dietetic', '', 'edit')) {
            access_denied('dietetic');
        }

        if ($this->input->post()) {
            $reason = $this->input->post('rejection_reason');

            if (empty($reason)) {
                set_alert('danger', _l('rejection_reason_required'));
                redirect(admin_url('dietetic/refunds/view/' . $id));
                return;
            }

            $result = $this->dietetic_refunds_model->reject($id, $reason);

            if ($result['success']) {
                set_alert('success', _l('refund_rejected_successfully'));
            } else {
                set_alert('danger', $result['error'] ?? _l('refund_rejection_failed'));
            }

            redirect(admin_url('dietetic/refunds/view/' . $id));
        }

        $data['refund'] = $this->dietetic_refunds_model->get($id);
        $data['title'] = _l('reject_refund');

        $this->load->view('admin/refunds/reject', $data);
    }

    /**
     * Process refund manually
     */
    public function process($id)
    {
        if (!has_permission('dietetic', '', 'edit')) {
            access_denied('dietetic');
        }

        if ($this->input->post()) {
            $manual_reference = $this->input->post('manual_reference');
            $notes = $this->input->post('processing_notes');

            $result = $this->dietetic_refunds_model->process_refund($id, $manual_reference, $notes);

            if ($result['success']) {
                set_alert('success', _l('refund_processed_successfully'));
            } else {
                set_alert('danger', $result['error'] ?? _l('refund_processing_failed'));
            }

            redirect(admin_url('dietetic/refunds/view/' . $id));
        }

        $data['refund'] = $this->dietetic_refunds_model->get($id);
        $data['payment'] = $this->dietetic_payments_model->get($data['refund']->payment_id);
        $data['title'] = _l('process_refund');

        $this->load->view('admin/refunds/process', $data);
    }

    /**
     * Cancel refund request
     */
    public function cancel($id)
    {
        if (!has_permission('dietetic', '', 'delete')) {
            access_denied('dietetic');
        }

        if ($this->input->post()) {
            $reason = $this->input->post('cancellation_reason');

            $success = $this->dietetic_refunds_model->update($id, [
                'status' => 'cancelled',
                'processed_at' => date('Y-m-d H:i:s'),
                'processed_by' => get_staff_user_id(),
                'notes' => $reason
            ]);

            if ($success) {
                // Log activity
                $this->dietetic_refunds_model->log_refund_activity(
                    $id,
                    'cancelled',
                    'Refund cancelled by administrator: ' . $reason
                );

                set_alert('success', _l('refund_cancelled'));
            } else {
                set_alert('danger', _l('refund_cancel_failed'));
            }

            redirect(admin_url('dietetic/refunds'));
        }

        $data['refund'] = $this->dietetic_refunds_model->get($id);
        $data['title'] = _l('cancel_refund');

        $this->load->view('admin/refunds/cancel', $data);
    }

    /**
     * Get refund statistics
     */
    public function statistics()
    {
        if (!has_permission('dietetic', '', 'view')) {
            access_denied('dietetic');
        }

        $period = $this->input->get('period') ?? 'month';
        $stats = $this->dietetic_refunds_model->get_statistics($period);

        header('Content-Type: application/json');
        echo json_encode($stats);
    }

    /**
     * Export refunds to CSV
     */
    public function export()
    {
        if (!has_permission('dietetic', '', 'view')) {
            access_denied('dietetic');
        }

        $status = $this->input->get('status');

        // Build where clause based on status filter
        $where = [];
        if ($status) {
            $where['r.status'] = $status;
        }

        $refunds = $this->dietetic_refunds_model->get_all($where);

        // Generate CSV
        $this->load->helper('download');
        $csv_data = "Refund ID,Patient,Invoice,Payment,Amount,Type,Status,Reason,Initiated Date,Processed Date\n";

        foreach ($refunds as $refund) {
            $csv_data .= sprintf(
                "%s,%s,%s,%s,%s,%s,%s,%s,%s,%s\n",
                $refund->id,
                '"' . ($refund->patient_name ?? '') . '"',
                $refund->invoice_number ?? '',
                $refund->payment_transaction_id ?? '',
                $refund->refund_amount,
                $refund->refund_type,
                $refund->status,
                '"' . str_replace('"', '""', $refund->reason ?? '') . '"',
                $refund->created_at,
                $refund->processed_date ?? ''
            );
        }

        force_download('refunds_' . date('Y-m-d') . '.csv', $csv_data);
    }

    /**
     * AJAX: Get refunds table data
     */
    public function table()
    {
        if (!has_permission('dietetic', '', 'view')) {
            ajax_access_denied();
        }

        $this->app->get_table_data(module_views_path('dietetic', 'refunds/table'));
    }

    /**
     * Bulk approve refunds
     */
    public function bulk_approve()
    {
        if (!has_permission('dietetic', '', 'edit')) {
            access_denied('dietetic');
        }

        $refund_ids = $this->input->post('refund_ids');

        if (empty($refund_ids) || !is_array($refund_ids)) {
            set_alert('danger', _l('no_refunds_selected'));
            redirect(admin_url('dietetic/refunds'));
            return;
        }

        $success_count = 0;
        $fail_count = 0;

        foreach ($refund_ids as $refund_id) {
            $result = $this->dietetic_refunds_model->approve($refund_id, 'Bulk approved by administrator');

            if ($result['success']) {
                $success_count++;
            } else {
                $fail_count++;
            }
        }

        if ($success_count > 0) {
            set_alert('success', sprintf(_l('refunds_bulk_approved'), $success_count));
        }

        if ($fail_count > 0) {
            set_alert('warning', sprintf(_l('refunds_bulk_approve_failed'), $fail_count));
        }

        redirect(admin_url('dietetic/refunds'));
    }

    /**
     * Check refund eligibility for a payment
     */
    public function check_eligibility($payment_id)
    {
        if (!has_permission('dietetic', '', 'view')) {
            ajax_access_denied();
        }

        $payment = $this->dietetic_payments_model->get($payment_id);

        if (!$payment) {
            header('Content-Type: application/json');
            echo json_encode(['eligible' => false, 'reason' => 'Payment not found']);
            return;
        }

        $eligible = true;
        $reason = '';
        $max_refund = 0;

        if ($payment->status != 'completed') {
            $eligible = false;
            $reason = 'Only completed payments can be refunded';
        } else {
            $already_refunded = $payment->refunded_amount ?? 0;
            $max_refund = $payment->amount - $already_refunded;

            if ($max_refund <= 0) {
                $eligible = false;
                $reason = 'Payment has been fully refunded';
            }
        }

        header('Content-Type: application/json');
        echo json_encode([
            'eligible' => $eligible,
            'reason' => $reason,
            'max_refund_amount' => $max_refund,
            'payment_amount' => $payment->amount,
            'already_refunded' => $payment->refunded_amount ?? 0
        ]);
    }
}
