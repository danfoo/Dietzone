<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Recurring Payments Controller
 * Admin interface for managing recurring payment subscriptions
 *
 * Phase 9 - Recurring Payments & Refunds
 */
class Recurring_payments extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('dietetic/dietetic_recurring_payments_model');
        $this->load->model('dietetic/dietetic_subscriptions_model');
        $this->load->model('dietetic/dietetic_patients_model');
    }

    /**
     * List all recurring payments
     */
    public function index()
    {
        if (!has_permission('dietetic', '', 'view')) {
            access_denied('dietetic');
        }

        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('dietetic_recurring_payments');
        }

        $data['title'] = _l('recurring_payments');
        $this->load->view('admin/recurring_payments/manage', $data);
    }

    /**
     * View recurring payment details
     */
    public function view($id)
    {
        if (!has_permission('dietetic', '', 'view')) {
            access_denied('dietetic');
        }

        $recurring = $this->dietetic_recurring_payments_model->get($id);

        if (!$recurring) {
            show_404();
        }

        // Get related data
        $data['recurring'] = $recurring;
        $data['subscription'] = $this->dietetic_subscriptions_model->get($recurring->subscription_id);
        $data['patient'] = $this->dietetic_patients_model->get($recurring->patient_id);
        $data['transactions'] = $this->dietetic_recurring_payments_model->get_transactions($id);
        // $data['statistics'] = $this->dietetic_recurring_payments_model->get_recurring_statistics($id); // TODO: Implement this method
        $data['title'] = _l('recurring_payment') . ' #' . $id;

        $this->load->view('admin/recurring_payments/view', $data);
    }

    /**
     * Create new recurring payment
     */
    public function create($subscription_id = null)
    {
        if (!has_permission('dietetic', '', 'create')) {
            access_denied('dietetic');
        }

        if ($this->input->post()) {
            $data = $this->input->post();

            $result = $this->dietetic_recurring_payments_model->add($data);

            if ($result) {
                set_alert('success', _l('recurring_payment_created_successfully'));
                redirect(admin_url('dietetic/recurring_payments/view/' . $result));
            } else {
                set_alert('danger', _l('recurring_payment_creation_failed'));
            }
        }

        // Prepare form data
        $data['subscription'] = null;
        if ($subscription_id) {
            $data['subscription'] = $this->dietetic_subscriptions_model->get($subscription_id);
            if ($data['subscription']) {
                $data['patient'] = $this->dietetic_patients_model->get($data['subscription']->patient_id);
            }
        }

        $data['title'] = _l('new_recurring_payment');
        $this->load->view('admin/recurring_payments/form', $data);
    }

    /**
     * Edit recurring payment
     */
    public function edit($id)
    {
        if (!has_permission('dietetic', '', 'edit')) {
            access_denied('dietetic');
        }

        $recurring = $this->dietetic_recurring_payments_model->get($id);

        if (!$recurring) {
            show_404();
        }

        if ($this->input->post()) {
            $data = $this->input->post();

            $success = $this->dietetic_recurring_payments_model->update($id, $data);

            if ($success) {
                set_alert('success', _l('recurring_payment_updated_successfully'));
                redirect(admin_url('dietetic/recurring_payments/view/' . $id));
            } else {
                set_alert('danger', _l('recurring_payment_update_failed'));
            }
        }

        $data['recurring'] = $recurring;
        $data['subscription'] = $this->dietetic_subscriptions_model->get($recurring->subscription_id);
        $data['patient'] = $this->dietetic_patients_model->get($recurring->patient_id);
        $data['title'] = _l('edit_recurring_payment');

        $this->load->view('admin/recurring_payments/form', $data);
    }

    /**
     * Pause recurring payment
     */
    public function pause($id)
    {
        if (!has_permission('dietetic', '', 'edit')) {
            access_denied('dietetic');
        }

        $success = $this->dietetic_recurring_payments_model->pause($id);

        if ($success) {
            set_alert('success', _l('recurring_payment_paused'));
        } else {
            set_alert('danger', _l('recurring_payment_pause_failed'));
        }

        redirect(admin_url('dietetic/recurring_payments/view/' . $id));
    }

    /**
     * Resume recurring payment
     */
    public function resume($id)
    {
        if (!has_permission('dietetic', '', 'edit')) {
            access_denied('dietetic');
        }

        $success = $this->dietetic_recurring_payments_model->resume($id);

        if ($success) {
            set_alert('success', _l('recurring_payment_resumed'));
        } else {
            set_alert('danger', _l('recurring_payment_resume_failed'));
        }

        redirect(admin_url('dietetic/recurring_payments/view/' . $id));
    }

    /**
     * Cancel recurring payment
     */
    public function cancel($id)
    {
        if (!has_permission('dietetic', '', 'delete')) {
            access_denied('dietetic');
        }

        if ($this->input->post('reason')) {
            $reason = $this->input->post('reason');
            $success = $this->dietetic_recurring_payments_model->cancel($id, $reason);

            if ($success) {
                set_alert('success', _l('recurring_payment_cancelled'));
                redirect(admin_url('dietetic/recurring_payments'));
            } else {
                set_alert('danger', _l('recurring_payment_cancel_failed'));
            }
        }

        $data['recurring'] = $this->dietetic_recurring_payments_model->get($id);
        $data['title'] = _l('cancel_recurring_payment');

        $this->load->view('admin/recurring_payments/cancel', $data);
    }

    /**
     * Process payment manually (force process)
     */
    public function process($id)
    {
        if (!has_permission('dietetic', '', 'edit')) {
            access_denied('dietetic');
        }

        $success = $this->dietetic_recurring_payments_model->process_payment($id);

        if ($success) {
            set_alert('success', _l('recurring_payment_processed'));
        } else {
            set_alert('danger', _l('recurring_payment_process_failed'));
        }

        redirect(admin_url('dietetic/recurring_payments/view/' . $id));
    }

    /**
     * Retry failed payment
     */
    public function retry($id)
    {
        if (!has_permission('dietetic', '', 'edit')) {
            access_denied('dietetic');
        }

        $success = $this->dietetic_recurring_payments_model->retry_payment($id);

        if ($success) {
            set_alert('success', _l('recurring_payment_retry_initiated'));
        } else {
            set_alert('danger', _l('recurring_payment_retry_failed'));
        }

        redirect(admin_url('dietetic/recurring_payments/view/' . $id));
    }

    /**
     * Get statistics for dashboard
     */
    public function statistics()
    {
        if (!has_permission('dietetic', '', 'view')) {
            access_denied('dietetic');
        }

        $stats = $this->dietetic_recurring_payments_model->get_statistics();

        header('Content-Type: application/json');
        echo json_encode($stats);
    }

    /**
     * Export recurring payments to CSV
     */
    public function export()
    {
        if (!has_permission('dietetic', '', 'view')) {
            access_denied('dietetic');
        }

        $status = $this->input->get('status');
        $recurring_payments = $this->dietetic_recurring_payments_model->get_all($status);

        // Generate CSV
        $this->load->helper('download');
        $csv_data = "ID,Patient,Subscription,Amount,Frequency,Next Payment,Status,Created\n";

        foreach ($recurring_payments as $rp) {
            $csv_data .= sprintf(
                "%d,%s,%d,%s,%s,%s,%s,%s\n",
                $rp->id,
                '"' . $rp->patient_name . '"',
                $rp->subscription_id,
                $rp->amount,
                $rp->frequency,
                $rp->next_payment_date,
                $rp->status,
                $rp->created_at
            );
        }

        force_download('recurring_payments_' . date('Y-m-d') . '.csv', $csv_data);
    }

    /**
     * AJAX: Get recurring payments table data
     */
    public function table()
    {
        if (!has_permission('dietetic', '', 'view')) {
            ajax_access_denied();
        }

        $this->app->get_table_data(module_views_path('dietetic', 'recurring_payments/table'));
    }

    /**
     * Delete recurring payment (soft delete by marking as cancelled)
     */
    public function delete($id)
    {
        if (!has_permission('dietetic', '', 'delete')) {
            access_denied('dietetic');
        }

        // Instead of hard delete, we cancel with reason "Deleted by admin"
        $success = $this->dietetic_recurring_payments_model->cancel($id, 'Deleted by administrator');

        if ($success) {
            set_alert('success', _l('recurring_payment_deleted'));
        } else {
            set_alert('danger', _l('recurring_payment_delete_failed'));
        }

        redirect(admin_url('dietetic/recurring_payments'));
    }
}
