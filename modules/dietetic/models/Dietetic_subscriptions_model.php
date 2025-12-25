<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dietetic_subscriptions_model extends App_Model
{
    private $table = 'dietic_subscriptions';

    public function __construct()
    {
        parent::__construct();

        // Load required models
        $this->load->model('dietetic/dietetic_patients_model');
        $this->load->model('staff_model');
    }

    /**
     * Get subscription by ID
     *
     * @param int $id
     * @return object|null
     */
    public function get($id)
    {
        $this->db->where('id', $id);
        $subscription = $this->db->get(db_prefix() . $this->table)->row();

        if ($subscription) {
            // Get patient info
            $subscription->patient = $this->dietetic_patients_model->get($subscription->patient_id, false);

            // Get dietitian info
            $subscription->dietitian = $this->staff_model->get($subscription->dietitian_id);

            // Get service plan info if exists
            if (isset($subscription->service_plan_id)) {
                $subscription->service_plan = $this->db->get_where(
                    db_prefix() . 'dietic_service_plans',
                    ['id' => $subscription->service_plan_id]
                )->row();
            }
        }

        return $subscription;
    }

    /**
     * Get all subscriptions with filters
     *
     * @param array $where
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function get_all($where = [], $limit = null, $offset = null)
    {
        $this->db->select('s.*, ' .
            'CONCAT(st.firstname, " ", st.lastname) as dietitian_name', false);
        $this->db->from(db_prefix() . $this->table . ' s');
        $this->db->join(db_prefix() . 'staff st', 'st.staffid = s.dietitian_id', 'left');

        if (!empty($where)) {
            $this->db->where($where);
        }

        if ($limit !== null) {
            $this->db->limit($limit, $offset);
        }

        $this->db->order_by('s.created_at', 'DESC');

        return $this->db->get()->result();
    }

    /**
     * Get active subscription for a patient
     *
     * @param int $patient_id
     * @return object|null
     */
    public function get_active_subscription($patient_id)
    {
        $this->db->where('patient_id', $patient_id);
        $this->db->where_in('status', ['trial', 'active']);
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit(1);

        $subscription = $this->db->get(db_prefix() . $this->table)->row();

        if ($subscription) {
            // Get dietitian info
            $subscription->dietitian = $this->staff_model->get($subscription->dietitian_id);
        }

        return $subscription;
    }

    /**
     * Create a new subscription
     *
     * @param array $data
     * @return int|false Subscription ID or false on failure
     */
    public function add($data)
    {
        // Set default values
        if (!isset($data['status'])) {
            $data['status'] = 'pending';
        }

        if (!isset($data['referral_source'])) {
            $data['referral_source'] = 'platform';
        }

        if (!isset($data['start_date'])) {
            $data['start_date'] = date('Y-m-d');
        }

        // Calculate end_date if service_plan_id is provided
        if (isset($data['service_plan_id']) && !isset($data['end_date'])) {
            $plan = $this->db->get_where(
                db_prefix() . 'dietic_service_plans',
                ['id' => $data['service_plan_id']]
            )->row();

            if ($plan) {
                $duration_value = $plan->duration_value ?? 1;
                $duration_unit = $plan->duration_unit ?? 'month';

                $data['end_date'] = date('Y-m-d', strtotime($data['start_date'] . ' +' . $duration_value . ' ' . $duration_unit));

                // Set trial end date if applicable
                if (isset($plan->trial_days) && $plan->trial_days > 0) {
                    $data['trial_end_date'] = date('Y-m-d', strtotime($data['start_date'] . ' +' . $plan->trial_days . ' days'));
                    $data['status'] = 'trial';
                } else {
                    $data['status'] = 'active';
                }

                // Set billing cycle and amount
                if (!isset($data['billing_cycle'])) {
                    $data['billing_cycle'] = $plan->billing_cycle ?? 'monthly';
                }

                if (!isset($data['amount'])) {
                    $data['amount'] = $plan->price ?? 0;
                }

                // Calculate next billing date
                if (!isset($data['next_billing_date'])) {
                    $billing_start = $data['trial_end_date'] ?? $data['start_date'];
                    if ($data['billing_cycle'] == 'yearly') {
                        $data['next_billing_date'] = date('Y-m-d', strtotime($billing_start . ' +1 year'));
                    } else {
                        $data['next_billing_date'] = date('Y-m-d', strtotime($billing_start . ' +1 month'));
                    }
                }
            }
        }

        // For one-time service subscriptions (from Perfex items, not service plans)
        if (!isset($data['service_plan_id']) && isset($data['duration_months'])) {
            $duration = $data['duration_months'];
            $data['end_date'] = date('Y-m-d', strtotime($data['start_date'] . ' +' . $duration . ' months'));
            $data['status'] = 'active';
            unset($data['duration_months']); // Remove temporary field
        }

        $this->db->insert(db_prefix() . $this->table, $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            log_activity('Subscription Created [ID: ' . $insert_id . ', Patient: ' . $data['patient_id'] . ', Dietitian: ' . $data['dietitian_id'] . ']');
        }

        return $insert_id;
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
     * @param int $cancelled_by Staff ID
     * @param string $reason
     * @return bool
     */
    public function cancel($id, $cancelled_by, $reason = '')
    {
        $data = [
            'status' => 'cancelled',
            'cancelled_at' => date('Y-m-d H:i:s'),
            'cancelled_by' => $cancelled_by,
            'cancellation_reason' => $reason
        ];

        return $this->update($id, $data);
    }

    /**
     * Activate subscription (after payment)
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

        // Determine status based on trial
        $status = 'active';
        if ($subscription->trial_end_date && strtotime($subscription->trial_end_date) > time()) {
            $status = 'trial';
        }

        return $this->update($id, ['status' => $status]);
    }

    /**
     * Get subscriptions for a dietitian
     *
     * @param int $dietitian_id
     * @param string $status
     * @return array
     */
    public function get_by_dietitian($dietitian_id, $status = null)
    {
        $this->db->where('dietitian_id', $dietitian_id);

        if ($status !== null) {
            if (is_array($status)) {
                $this->db->where_in('status', $status);
            } else {
                $this->db->where('status', $status);
            }
        }

        $this->db->order_by('created_at', 'DESC');

        return $this->db->get(db_prefix() . $this->table)->result();
    }

    /**
     * Count active subscriptions for a dietitian (workload)
     *
     * @param int $dietitian_id
     * @return int
     */
    public function count_active_by_dietitian($dietitian_id)
    {
        return $this->db->where('dietitian_id', $dietitian_id)
            ->where_in('status', ['trial', 'active'])
            ->count_all_results(db_prefix() . $this->table);
    }

    /**
     * Get expiring subscriptions (for renewal reminders)
     *
     * @param int $days Number of days before expiration
     * @return array
     */
    public function get_expiring_subscriptions($days = 7)
    {
        $future_date = date('Y-m-d', strtotime('+' . $days . ' days'));

        $this->db->where('status', 'active');
        $this->db->where('end_date <=', $future_date);
        $this->db->where('end_date >=', date('Y-m-d'));

        return $this->db->get(db_prefix() . $this->table)->result();
    }

    /**
     * Get expired subscriptions to auto-cancel
     *
     * @return array
     */
    public function get_expired_subscriptions()
    {
        $this->db->where('status', 'active');
        $this->db->where('end_date <', date('Y-m-d'));

        return $this->db->get(db_prefix() . $this->table)->result();
    }

    /**
     * Delete subscription
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        $result = $this->db->delete(db_prefix() . $this->table);

        if ($result) {
            log_activity('Subscription Deleted [ID: ' . $id . ']');
        }

        return $result;
    }
}
