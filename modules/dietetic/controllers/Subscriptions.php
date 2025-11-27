<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Subscriptions extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('dietetic/dietetic_subscriptions_model');
        $this->load->model('dietetic/dietetic_service_plans_model');
        $this->load->model('dietetic/dietetic_patients_model');
        $this->load->helper('dietetic/dietetic');

        if (!dietetic_has_permission('view')) {
            access_denied('dietetic');
        }
    }

    /**
     * List all subscriptions
     */
    public function index()
    {
        $data['title'] = 'Abonnements';

        // Pagination
        $per_page = 20;
        $page = $this->input->get('page') ? (int)$this->input->get('page') : 1;
        $offset = ($page - 1) * $per_page;

        // Filters
        $where = [];
        $status_filter = $this->input->get('status');
        $referral_filter = $this->input->get('referral');
        $plan_filter = $this->input->get('plan');

        if ($status_filter && $status_filter !== 'all') {
            $where['s.status'] = $status_filter;
        }

        if ($referral_filter && $referral_filter !== 'all') {
            $where['s.referral_source'] = $referral_filter;
        }

        if ($plan_filter && $plan_filter !== 'all') {
            $where['s.service_plan_id'] = $plan_filter;
        }

        // Get subscriptions
        $total_subscriptions = $this->dietetic_subscriptions_model->count_all($where);
        $data['subscriptions'] = $this->dietetic_subscriptions_model->get_all($where, $per_page, $offset);

        // Get statistics
        $data['stats'] = $this->dietetic_subscriptions_model->get_statistics();

        // Get all plans for filter
        $data['plans'] = $this->dietetic_service_plans_model->get_all();

        // Pagination data
        $data['total_subscriptions'] = $total_subscriptions;
        $data['per_page'] = $per_page;
        $data['current_page'] = $page;
        $data['total_pages'] = ceil($total_subscriptions / $per_page);
        $data['status_filter'] = $status_filter;
        $data['referral_filter'] = $referral_filter;
        $data['plan_filter'] = $plan_filter;

        $this->load->view('admin/subscriptions/list', $data);
    }

    /**
     * Create new subscription
     */
    public function create()
    {
        if (!dietetic_has_permission('create')) {
            access_denied('dietetic');
        }

        if ($this->input->post()) {
            $data = $this->input->post();

            // Get plan details
            $plan = $this->dietetic_service_plans_model->get($data['service_plan_id']);

            if (!$plan) {
                set_alert('danger', 'Plan de service introuvable');
                redirect(admin_url('dietetic/subscriptions/create'));
                return;
            }

            // Calculate dates
            $start_date = $data['start_date'] ?: date('Y-m-d');
            $data['start_date'] = $start_date;

            // Trial period
            if ($plan->trial_days > 0) {
                $data['trial_end_date'] = date('Y-m-d', strtotime($start_date . ' +' . $plan->trial_days . ' days'));
                $data['status'] = 'trial';
            } else {
                $data['status'] = 'pending'; // Waiting for first payment
            }

            // Calculate end date based on duration
            $duration_string = '+' . $plan->duration_value . ' ' . $plan->duration_unit;
            $data['end_date'] = date('Y-m-d', strtotime($start_date . ' ' . $duration_string));

            // Set amount from plan
            $data['amount'] = $plan->price;
            $data['billing_cycle'] = $plan->billing_cycle;

            // Calculate next billing date
            if ($data['billing_cycle'] == 'monthly') {
                $billing_start = $data['trial_end_date'] ?: $start_date;
                $data['next_billing_date'] = date('Y-m-d', strtotime($billing_start . ' +1 month'));
            } else {
                $billing_start = $data['trial_end_date'] ?: $start_date;
                $data['next_billing_date'] = date('Y-m-d', strtotime($billing_start . ' +1 year'));
            }

            $subscription_id = $this->dietetic_subscriptions_model->add($data);

            if ($subscription_id) {
                set_alert('success', 'Abonnement créé avec succès');
                redirect(admin_url('dietetic/subscriptions/view/' . $subscription_id));
            } else {
                set_alert('danger', 'Erreur lors de la création de l\'abonnement');
            }
        }

        $data['title'] = 'Nouvel Abonnement';

        // Get active plans
        $data['plans'] = $this->dietetic_service_plans_model->get_active();

        // Get patients
        $data['patients'] = $this->dietetic_patients_model->get_all();

        // Get staff (dietitians)
        $this->load->model('staff_model');
        $data['staff'] = $this->staff_model->get();

        $this->load->view('admin/subscriptions/form', $data);
    }

    /**
     * Edit subscription
     */
    public function edit($id)
    {
        if (!dietetic_has_permission('edit')) {
            access_denied('dietetic');
        }

        $data['subscription'] = $this->dietetic_subscriptions_model->get($id);

        if (!$data['subscription']) {
            show_404();
        }

        if ($this->input->post()) {
            $update_data = $this->input->post();

            if ($this->dietetic_subscriptions_model->update($id, $update_data)) {
                set_alert('success', 'Abonnement mis à jour avec succès');
                redirect(admin_url('dietetic/subscriptions/view/' . $id));
            } else {
                set_alert('danger', 'Erreur lors de la mise à jour');
            }
        }

        $data['title'] = 'Modifier l\'Abonnement';
        $data['plans'] = $this->dietetic_service_plans_model->get_all();
        $data['patients'] = $this->dietetic_patients_model->get_all();

        $this->load->model('staff_model');
        $data['staff'] = $this->staff_model->get();

        $this->load->view('admin/subscriptions/form', $data);
    }

    /**
     * View subscription details
     */
    public function view($id)
    {
        $data['subscription'] = $this->dietetic_subscriptions_model->get($id);

        if (!$data['subscription']) {
            show_404();
        }

        $data['title'] = 'Abonnement #' . $id;

        // Get invoices for this subscription
        $this->load->model('dietetic/dietetic_invoices_model');
        $data['invoices'] = $this->dietetic_invoices_model->get_by_subscription($id);

        // Get payments
        $this->load->model('dietetic/dietetic_payments_model');
        $total_paid = 0;
        foreach ($data['invoices'] as $invoice) {
            if ($invoice->status == 'paid') {
                $total_paid += $invoice->total_amount;
            }
        }
        $data['total_paid'] = $total_paid;

        $this->load->view('admin/subscriptions/view', $data);
    }

    /**
     * Cancel subscription
     */
    public function cancel($id)
    {
        if (!dietetic_has_permission('delete')) {
            ajax_access_denied();
        }

        $reason = $this->input->post('reason');

        if ($this->dietetic_subscriptions_model->cancel($id, $reason)) {
            echo json_encode(['success' => true, 'message' => 'Abonnement annulé avec succès']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'annulation']);
        }
    }

    /**
     * Activate subscription
     */
    public function activate($id)
    {
        if (!dietetic_has_permission('edit')) {
            ajax_access_denied();
        }

        if ($this->dietetic_subscriptions_model->activate($id)) {
            echo json_encode(['success' => true, 'message' => 'Abonnement activé avec succès']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'activation']);
        }
    }

    /**
     * Get plan details (AJAX)
     */
    public function get_plan_details($plan_id)
    {
        $plan = $this->dietetic_service_plans_model->get_with_stats($plan_id);

        if (!$plan) {
            echo json_encode(['success' => false]);
            return;
        }

        echo json_encode([
            'success' => true,
            'plan' => [
                'id' => $plan->id,
                'name' => $plan->name_fr ?: $plan->name,
                'price' => $plan->price,
                'billing_cycle' => $plan->billing_cycle,
                'duration_value' => $plan->duration_value,
                'duration_unit' => $plan->duration_unit,
                'trial_days' => $plan->trial_days,
                'features' => json_decode($plan->features)
            ]
        ]);
    }

    /**
     * Check for patients with active subscriptions
     */
    public function check_patient_subscription($patient_id)
    {
        $has_active = $this->dietetic_subscriptions_model->patient_has_access($patient_id);

        echo json_encode([
            'has_active' => $has_active
        ]);
    }
}
