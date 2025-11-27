<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Service_plans extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('dietetic/dietetic_service_plans_model');
        $this->load->helper('dietetic/dietetic');

        // Only admins can manage service plans
        if (!is_admin()) {
            access_denied('dietetic');
        }
    }

    /**
     * List all service plans
     */
    public function index()
    {
        $data['title'] = 'Plans de Service';

        // Pagination
        $per_page = 20;
        $page = $this->input->get('page') ? (int)$this->input->get('page') : 1;
        $offset = ($page - 1) * $per_page;

        // Get filters
        $where = [];
        $status_filter = $this->input->get('status');

        if ($status_filter && $status_filter !== 'all') {
            $where['is_active'] = ($status_filter === 'active') ? 1 : 0;
        }

        // Get plans
        $total_plans = $this->dietetic_service_plans_model->count_all($where);
        $data['plans'] = $this->dietetic_service_plans_model->get_all($where, $per_page, $offset);

        // Add stats to each plan
        foreach ($data['plans'] as &$plan) {
            $plan = $this->dietetic_service_plans_model->get_with_stats($plan->id);
        }

        // Pagination data
        $data['total_plans'] = $total_plans;
        $data['per_page'] = $per_page;
        $data['current_page'] = $page;
        $data['total_pages'] = ceil($total_plans / $per_page);
        $data['status_filter'] = $status_filter;

        $this->load->view('admin/service_plans/list', $data);
    }

    /**
     * Create new service plan
     */
    public function create()
    {
        if ($this->input->post()) {
            $data = $this->input->post();

            // Process features (convert from array to JSON)
            if (isset($data['features']) && is_array($data['features'])) {
                $data['features'] = array_filter($data['features']);
            }

            $plan_id = $this->dietetic_service_plans_model->add($data);

            if ($plan_id) {
                set_alert('success', 'Plan de service créé avec succès');
                redirect(admin_url('dietetic/service_plans'));
            } else {
                set_alert('danger', 'Erreur lors de la création du plan');
            }
        }

        $data['title'] = 'Nouveau Plan de Service';
        $this->load->view('admin/service_plans/form', $data);
    }

    /**
     * Edit service plan
     */
    public function edit($id)
    {
        $data['plan'] = $this->dietetic_service_plans_model->get($id);

        if (!$data['plan']) {
            show_404();
        }

        if ($this->input->post()) {
            $update_data = $this->input->post();

            // Process features
            if (isset($update_data['features']) && is_array($update_data['features'])) {
                $update_data['features'] = array_filter($update_data['features']);
            }

            if ($this->dietetic_service_plans_model->update($id, $update_data)) {
                set_alert('success', 'Plan de service mis à jour avec succès');
                redirect(admin_url('dietetic/service_plans'));
            } else {
                set_alert('danger', 'Erreur lors de la mise à jour du plan');
            }
        }

        $data['title'] = 'Modifier le Plan de Service';
        $data['features'] = $this->dietetic_service_plans_model->get_features($data['plan']);

        $this->load->view('admin/service_plans/form', $data);
    }

    /**
     * View service plan details
     */
    public function view($id)
    {
        $data['plan'] = $this->dietetic_service_plans_model->get_with_stats($id);

        if (!$data['plan']) {
            show_404();
        }

        $data['title'] = $data['plan']->name;
        $data['features'] = $this->dietetic_service_plans_model->get_features($data['plan']);

        // Get active subscriptions for this plan
        $this->load->model('dietetic/dietetic_subscriptions_model');
        $data['subscriptions'] = $this->dietetic_subscriptions_model->get_all(['s.service_plan_id' => $id], 10);

        $this->load->view('admin/service_plans/view', $data);
    }

    /**
     * Delete service plan
     */
    public function delete($id)
    {
        if (!is_admin()) {
            ajax_access_denied();
        }

        if ($this->dietetic_service_plans_model->delete($id)) {
            echo json_encode(['success' => true, 'message' => 'Plan supprimé avec succès']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression']);
        }
    }

    /**
     * Toggle active status
     */
    public function toggle_active($id)
    {
        if (!is_admin()) {
            ajax_access_denied();
        }

        if ($this->dietetic_service_plans_model->toggle_active($id)) {
            echo json_encode(['success' => true, 'message' => 'Statut mis à jour']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
        }
    }
}
