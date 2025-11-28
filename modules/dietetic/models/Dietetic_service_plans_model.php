<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dietetic_service_plans_model extends App_Model
{
    private $table = 'dietic_service_plans';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get service plan by ID
     *
     * @param int $id
     * @return object|null
     */
    public function get($id)
    {
        $this->db->where('id', $id);
        return $this->db->get(db_prefix() . $this->table)->row();
    }

    /**
     * Get all service plans
     *
     * @param array $where
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function get_all($where = [], $limit = null, $offset = null)
    {
        if (!empty($where)) {
            $this->db->where($where);
        }

        $this->db->order_by('created_at', 'DESC');

        if ($limit !== null) {
            $this->db->limit($limit, $offset);
        }

        return $this->db->get(db_prefix() . $this->table)->result();
    }

    /**
     * Get active service plans
     *
     * @return array
     */
    public function get_active()
    {
        $this->db->where('is_active', 1);
        $this->db->order_by('price', 'ASC');
        return $this->db->get(db_prefix() . $this->table)->result();
    }

    /**
     * Count all service plans
     *
     * @param array $where
     * @return int
     */
    public function count_all($where = [])
    {
        if (!empty($where)) {
            $this->db->where($where);
        }

        return $this->db->count_all_results(db_prefix() . $this->table);
    }

    /**
     * Add new service plan
     *
     * @param array $data
     * @return int|bool
     */
    public function add($data)
    {
        $data['created_by'] = get_staff_user_id();
        $data['created_at'] = date('Y-m-d H:i:s');

        // Encode features array to JSON if present
        if (isset($data['features']) && is_array($data['features'])) {
            $data['features'] = json_encode($data['features']);
        }

        if ($this->db->insert(db_prefix() . $this->table, $data)) {
            $plan_id = $this->db->insert_id();
            log_activity('New Service Plan Created [ID: ' . $plan_id . ', Name: ' . $data['name'] . ']');
            return $plan_id;
        }

        return false;
    }

    /**
     * Update service plan
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        // Encode features array to JSON if present
        if (isset($data['features']) && is_array($data['features'])) {
            $data['features'] = json_encode($data['features']);
        }

        $this->db->where('id', $id);
        $result = $this->db->update(db_prefix() . $this->table, $data);

        if ($result) {
            log_activity('Service Plan Updated [ID: ' . $id . ']');
        }

        return $result;
    }

    /**
     * Delete service plan
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        // Check if plan is used in any active subscription
        $this->db->where('service_plan_id', $id);
        $this->db->where_in('status', ['active', 'trial']);
        $usage_count = $this->db->count_all_results(db_prefix() . 'dietic_subscriptions');

        if ($usage_count > 0) {
            // Don't delete, just deactivate
            return $this->update($id, ['is_active' => 0]);
        }

        $this->db->where('id', $id);
        $result = $this->db->delete(db_prefix() . $this->table);

        if ($result) {
            log_activity('Service Plan Deleted [ID: ' . $id . ']');
        }

        return $result;
    }

    /**
     * Toggle active status
     *
     * @param int $id
     * @return bool
     */
    public function toggle_active($id)
    {
        $plan = $this->get($id);

        if (!$plan) {
            return false;
        }

        $new_status = $plan->is_active ? 0 : 1;

        return $this->update($id, ['is_active' => $new_status]);
    }

    /**
     * Get plan with subscription count
     *
     * @param int $id
     * @return object|null
     */
    public function get_with_stats($id)
    {
        $plan = $this->get($id);

        if (!$plan) {
            return null;
        }

        // Count active subscriptions
        $this->db->where('service_plan_id', $id);
        $this->db->where_in('status', ['active', 'trial']);
        $plan->active_subscriptions = $this->db->count_all_results(db_prefix() . 'dietic_subscriptions');

        // Count total subscriptions
        $this->db->where('service_plan_id', $id);
        $plan->total_subscriptions = $this->db->count_all_results(db_prefix() . 'dietic_subscriptions');

        // Calculate total revenue from this plan
        $this->db->select_sum('total_amount');
        $this->db->join(db_prefix() . 'dietic_subscriptions s', 's.id = i.subscription_id');
        $this->db->where('s.service_plan_id', $id);
        $this->db->where('i.status', 'paid');
        $revenue_result = $this->db->get(db_prefix() . 'dietic_invoices i')->row();
        $plan->total_revenue = $revenue_result ? (float)$revenue_result->total_amount : 0;

        return $plan;
    }

    /**
     * Get plan features as array
     *
     * @param object|int $plan
     * @return array
     */
    public function get_features($plan)
    {
        if (is_numeric($plan)) {
            $plan = $this->get($plan);
        }

        if (!$plan || !$plan->features) {
            return [];
        }

        $features = json_decode($plan->features, true);

        return is_array($features) ? $features : [];
    }

    /**
     * Calculate plan price with tax
     *
     * @param object|int $plan
     * @param float $tax_rate
     * @return array
     */
    public function calculate_price($plan, $tax_rate = 0)
    {
        if (is_numeric($plan)) {
            $plan = $this->get($plan);
        }

        if (!$plan) {
            return null;
        }

        $price = (float)$plan->price;
        $tax_amount = $price * ($tax_rate / 100);
        $total = $price + $tax_amount;

        return [
            'price' => $price,
            'tax_rate' => $tax_rate,
            'tax_amount' => $tax_amount,
            'total' => $total
        ];
    }
}
