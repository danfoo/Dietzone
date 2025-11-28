<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dietetic_subscriptions_model extends App_Model
{
    private $table = 'dietic_subscriptions';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get subscription by ID
     *
     * @param int $id
     * @return object|null
     */
    public function get($id)
    {
        $this->db->select('s.*, ' .
            'c.company as patient_name, ' .
            'CONCAT(st.firstname, " ", st.lastname) as dietitian_name, st.email as dietitian_email, ' .
            'sp.name as plan_name, sp.name_fr as plan_name_fr');
        $this->db->from(db_prefix() . $this->table . ' s');
        $this->db->join(db_prefix() . 'dietic_patients p', 'p.id = s.patient_id', 'left');
        $this->db->join(db_prefix() . 'clients c', 'c.userid = p.client_id', 'left');
        $this->db->join(db_prefix() . 'staff st', 'st.staffid = s.dietitian_id', 'left');
        $this->db->join(db_prefix() . 'dietic_service_plans sp', 'sp.id = s.service_plan_id', 'left');
        $this->db->where('s.id', $id);

        return $this->db->get()->row();
    }

    /**
     * Get all subscriptions
     *
     * @param array $where
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function get_all($where = [], $limit = null, $offset = null)
    {
        $this->db->select('s.*, ' .
            'c.company as patient_name, ' .
            'CONCAT(st.firstname, " ", st.lastname) as dietitian_name, ' .
            'sp.name as plan_name, sp.name_fr as plan_name_fr');
        $this->db->from(db_prefix() . $this->table . ' s');
        $this->db->join(db_prefix() . 'dietic_patients p', 'p.id = s.patient_id', 'left');
        $this->db->join(db_prefix() . 'clients c', 'c.userid = p.client_id', 'left');
        $this->db->join(db_prefix() . 'staff st', 'st.staffid = s.dietitian_id', 'left');
        $this->db->join(db_prefix() . 'dietic_service_plans sp', 'sp.id = s.service_plan_id', 'left');

        if (!empty($where)) {
            $this->db->where($where);
        }

        // Apply staff permissions
        if (!is_admin()) {
            $this->db->where('s.dietitian_id', get_staff_user_id());
        }

        $this->db->order_by('s.created_at', 'DESC');

        if ($limit !== null) {
            $this->db->limit($limit, $offset);
        }

        return $this->db->get()->result();
    }

    /**
     * Get subscriptions by patient
     *
     * @param int $patient_id
     * @return array
     */
    public function get_by_patient($patient_id)
    {
        return $this->get_all(['s.patient_id' => $patient_id]);
    }

    /**
     * Get subscriptions by dietitian
     *
     * @param int $dietitian_id
     * @return array
     */
    public function get_by_dietitian($dietitian_id)
    {
        return $this->get_all(['s.dietitian_id' => $dietitian_id]);
    }

    /**
     * Get active subscription for patient
     *
     * @param int $patient_id
     * @return object|null
     */
    public function get_active_for_patient($patient_id)
    {
        $this->db->where('patient_id', $patient_id);
        $this->db->where_in('status', ['active', 'trial']);
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit(1);

        return $this->db->get(db_prefix() . $this->table)->row();
    }

    /**
     * Count all subscriptions
     *
     * @param array $where
     * @return int
     */
    public function count_all($where = [])
    {
        $this->db->from(db_prefix() . $this->table . ' s');

        if (!empty($where)) {
            $this->db->where($where);
        }

        // Apply staff permissions
        if (!is_admin()) {
            $this->db->where('s.dietitian_id', get_staff_user_id());
        }

        return $this->db->count_all_results();
    }

    /**
     * Add new subscription
     *
     * @param array $data
     * @return int|bool
     */
    public function add($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');

        if ($this->db->insert(db_prefix() . $this->table, $data)) {
            $subscription_id = $this->db->insert_id();

            log_activity('New Subscription Created [ID: ' . $subscription_id .
                ', Patient: ' . $data['patient_id'] . ']');

            return $subscription_id;
        }

        return false;
    }

    /**
     * Update subscription
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
            log_activity('Subscription Updated [ID: ' . $id . ']');
        }

        return $result;
    }

    /**
     * Cancel subscription
     *
     * @param int $id
     * @param string $reason
     * @return bool
     */
    public function cancel($id, $reason = null)
    {
        $data = [
            'status' => 'cancelled',
            'cancelled_at' => date('Y-m-d H:i:s'),
            'cancelled_by' => get_staff_user_id(),
            'cancellation_reason' => $reason
        ];

        $result = $this->update($id, $data);

        if ($result) {
            log_activity('Subscription Cancelled [ID: ' . $id . ']');
        }

        return $result;
    }

    /**
     * Activate subscription (from trial or pending)
     *
     * @param int $id
     * @return bool
     */
    public function activate($id)
    {
        $subscription = $this->get($id);

        if (!$subscription) {
            return false;
        }

        $data = [
            'status' => 'active'
        ];

        // Calculate next billing date based on billing cycle
        if ($subscription->billing_cycle == 'monthly') {
            $data['next_billing_date'] = date('Y-m-d', strtotime('+1 month'));
        } else { // yearly
            $data['next_billing_date'] = date('Y-m-d', strtotime('+1 year'));
        }

        return $this->update($id, $data);
    }

    /**
     * Check and update expired subscriptions
     *
     * @return int Number of subscriptions updated
     */
    public function update_expired_subscriptions()
    {
        $this->db->where('status', 'active');
        $this->db->where('end_date <=', date('Y-m-d'));

        $expired_subscriptions = $this->db->get(db_prefix() . $this->table)->result();

        $count = 0;
        foreach ($expired_subscriptions as $sub) {
            if ($this->update($sub->id, ['status' => 'expired'])) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Check and update trial subscriptions
     *
     * @return int Number of subscriptions updated
     */
    public function update_trial_subscriptions()
    {
        $this->db->where('status', 'trial');
        $this->db->where('trial_end_date <=', date('Y-m-d'));

        $trial_subscriptions = $this->db->get(db_prefix() . $this->table)->result();

        $count = 0;
        foreach ($trial_subscriptions as $sub) {
            // Change status to pending (waiting for payment)
            if ($this->update($sub->id, ['status' => 'pending'])) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Get subscriptions due for billing
     *
     * @return array
     */
    public function get_due_for_billing()
    {
        $this->db->select('s.*, ' .
            'c.company as patient_name, c.email as patient_email, ' .
            'CONCAT(st.firstname, " ", st.lastname) as dietitian_name');
        $this->db->from(db_prefix() . $this->table . ' s');
        $this->db->join(db_prefix() . 'dietic_patients p', 'p.id = s.patient_id', 'left');
        $this->db->join(db_prefix() . 'clients c', 'c.userid = p.client_id', 'left');
        $this->db->join(db_prefix() . 'staff st', 'st.staffid = s.dietitian_id', 'left');
        $this->db->where('s.status', 'active');
        $this->db->where('s.next_billing_date <=', date('Y-m-d'));

        return $this->db->get()->result();
    }

    /**
     * Check if patient has access (active subscription or trial)
     *
     * @param int $patient_id
     * @return bool
     */
    public function patient_has_access($patient_id)
    {
        $this->db->where('patient_id', $patient_id);
        $this->db->where_in('status', ['active', 'trial']);
        $count = $this->db->count_all_results(db_prefix() . $this->table);

        return $count > 0;
    }

    /**
     * Get subscription statistics
     *
     * @param array $filters
     * @return array
     */
    public function get_statistics($filters = [])
    {
        $stats = [];

        // Total subscriptions
        $stats['total'] = $this->count_all();

        // Active subscriptions
        $stats['active'] = $this->count_all(['s.status' => 'active']);

        // Trial subscriptions
        $stats['trial'] = $this->count_all(['s.status' => 'trial']);

        // Expired subscriptions
        $stats['expired'] = $this->count_all(['s.status' => 'expired']);

        // Cancelled subscriptions
        $stats['cancelled'] = $this->count_all(['s.status' => 'cancelled']);

        // By referral source
        $stats['platform_referrals'] = $this->count_all(['s.referral_source' => 'platform']);
        $stats['dietitian_referrals'] = $this->count_all(['s.referral_source' => 'dietitian']);

        return $stats;
    }

    /**
     * Delete subscription
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        // Check if subscription has invoices
        $this->db->where('subscription_id', $id);
        $invoice_count = $this->db->count_all_results(db_prefix() . 'dietic_invoices');

        if ($invoice_count > 0) {
            // Don't delete, just cancel
            return $this->cancel($id, 'Deleted by admin');
        }

        $this->db->where('id', $id);
        $result = $this->db->delete(db_prefix() . $this->table);

        if ($result) {
            log_activity('Subscription Deleted [ID: ' . $id . ']');
        }

        return $result;
    }
}
