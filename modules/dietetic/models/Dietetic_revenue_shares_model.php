<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dietetic_revenue_shares_model extends App_Model
{
    private $table = 'dietic_revenue_shares';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get revenue share by ID
     *
     * @param int $id
     * @return object|null
     */
    public function get($id)
    {
        $this->db->select('rs.*, ' .
            'i.invoice_number, ' .
            'pat.company as patient_name, ' .
            'CONCAT(st.firstname, " ", st.lastname) as dietitian_name, ' .
            'sp.name as plan_name');
        $this->db->from(db_prefix() . $this->table . ' rs');
        $this->db->join(db_prefix() . 'dietic_invoices i', 'i.id = rs.invoice_id', 'left');
        $this->db->join(db_prefix() . 'dietic_patients pat', 'pat.id = rs.patient_id', 'left');
        $this->db->join(db_prefix() . 'staff st', 'st.staffid = rs.dietitian_id', 'left');
        $this->db->join(db_prefix() . 'dietic_subscriptions s', 's.id = rs.subscription_id', 'left');
        $this->db->join(db_prefix() . 'dietic_service_plans sp', 'sp.id = s.service_plan_id', 'left');
        $this->db->where('rs.id', $id);

        return $this->db->get()->row();
    }

    /**
     * Get all revenue shares
     *
     * @param array $where
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function get_all($where = [], $limit = null, $offset = null)
    {
        $this->db->select('rs.*, ' .
            'i.invoice_number, i.issue_date, ' .
            'pat.company as patient_name, ' .
            'CONCAT(st.firstname, " ", st.lastname) as dietitian_name');
        $this->db->from(db_prefix() . $this->table . ' rs');
        $this->db->join(db_prefix() . 'dietic_invoices i', 'i.id = rs.invoice_id', 'left');
        $this->db->join(db_prefix() . 'dietic_patients pat', 'pat.id = rs.patient_id', 'left');
        $this->db->join(db_prefix() . 'staff st', 'st.staffid = rs.dietitian_id', 'left');

        if (!empty($where)) {
            $this->db->where($where);
        }

        // Apply staff permissions
        if (!is_admin()) {
            $this->db->where('rs.dietitian_id', get_staff_user_id());
        }

        $this->db->order_by('rs.created_at', 'DESC');

        if ($limit !== null) {
            $this->db->limit($limit, $offset);
        }

        return $this->db->get()->result();
    }

    /**
     * Get revenue shares by dietitian
     *
     * @param int $dietitian_id
     * @param array $filters
     * @return array
     */
    public function get_by_dietitian($dietitian_id, $filters = [])
    {
        $where = array_merge(['rs.dietitian_id' => $dietitian_id], $filters);
        return $this->get_all($where);
    }

    /**
     * Get revenue shares by patient
     *
     * @param int $patient_id
     * @return array
     */
    public function get_by_patient($patient_id)
    {
        return $this->get_all(['rs.patient_id' => $patient_id]);
    }

    /**
     * Count all revenue shares
     *
     * @param array $where
     * @return int
     */
    public function count_all($where = [])
    {
        $this->db->from(db_prefix() . $this->table . ' rs');

        if (!empty($where)) {
            $this->db->where($where);
        }

        // Apply staff permissions
        if (!is_admin()) {
            $this->db->where('rs.dietitian_id', get_staff_user_id());
        }

        return $this->db->count_all_results();
    }

    /**
     * Add new revenue share
     *
     * @param array $data
     * @return int|bool
     */
    public function add($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');

        if ($this->db->insert(db_prefix() . $this->table, $data)) {
            $revenue_share_id = $this->db->insert_id();

            log_activity('Revenue Share Created [ID: ' . $revenue_share_id .
                ', Dietitian Share: ' . $data['dietitian_share'] . ']');

            return $revenue_share_id;
        }

        return false;
    }

    /**
     * Update revenue share
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
            log_activity('Revenue Share Updated [ID: ' . $id . ']');
        }

        return $result;
    }

    /**
     * Mark revenue share as paid
     *
     * @param int $id
     * @param string $payment_reference
     * @return bool
     */
    public function mark_as_paid($id, $payment_reference = null)
    {
        return $this->update($id, [
            'status' => 'paid',
            'paid_date' => date('Y-m-d H:i:s'),
            'payment_reference' => $payment_reference
        ]);
    }

    /**
     * Mark multiple revenue shares as paid
     *
     * @param array $ids
     * @param string $payment_reference
     * @return int Number updated
     */
    public function mark_multiple_as_paid($ids, $payment_reference = null)
    {
        if (empty($ids)) {
            return 0;
        }

        $this->db->where_in('id', $ids);
        $this->db->update(db_prefix() . $this->table, [
            'status' => 'paid',
            'paid_date' => date('Y-m-d H:i:s'),
            'payment_reference' => $payment_reference,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return $this->db->affected_rows();
    }

    /**
     * Get pending revenue shares for dietitian
     *
     * @param int $dietitian_id
     * @return array
     */
    public function get_pending_for_dietitian($dietitian_id)
    {
        return $this->get_by_dietitian($dietitian_id, ['rs.status' => 'pending']);
    }

    /**
     * Get total pending amount for dietitian
     *
     * @param int $dietitian_id
     * @return float
     */
    public function get_pending_amount_for_dietitian($dietitian_id)
    {
        $this->db->select_sum('dietitian_share');
        $this->db->where('dietitian_id', $dietitian_id);
        $this->db->where('status', 'pending');
        $result = $this->db->get(db_prefix() . $this->table)->row();

        return $result ? (float)$result->dietitian_share : 0;
    }

    /**
     * Get total paid amount for dietitian
     *
     * @param int $dietitian_id
     * @param array $filters (date_from, date_to, etc.)
     * @return float
     */
    public function get_paid_amount_for_dietitian($dietitian_id, $filters = [])
    {
        $this->db->select_sum('dietitian_share');
        $this->db->where('dietitian_id', $dietitian_id);
        $this->db->where('status', 'paid');

        if (isset($filters['date_from'])) {
            $this->db->where('paid_date >=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $this->db->where('paid_date <=', $filters['date_to']);
        }

        $result = $this->db->get(db_prefix() . $this->table)->row();

        return $result ? (float)$result->dietitian_share : 0;
    }

    /**
     * Get revenue statistics for dietitian
     *
     * @param int $dietitian_id
     * @return array
     */
    public function get_dietitian_statistics($dietitian_id)
    {
        $stats = [];

        // Total earned (all time)
        $this->db->select_sum('dietitian_share');
        $this->db->where('dietitian_id', $dietitian_id);
        $this->db->where('status', 'paid');
        $result = $this->db->get(db_prefix() . $this->table)->row();
        $stats['total_earned'] = $result ? (float)$result->dietitian_share : 0;

        // Pending amount
        $stats['pending_amount'] = $this->get_pending_amount_for_dietitian($dietitian_id);

        // This month
        $stats['this_month'] = $this->get_paid_amount_for_dietitian($dietitian_id, [
            'date_from' => date('Y-m-01'),
            'date_to' => date('Y-m-t')
        ]);

        // This year
        $stats['this_year'] = $this->get_paid_amount_for_dietitian($dietitian_id, [
            'date_from' => date('Y-01-01'),
            'date_to' => date('Y-12-31')
        ]);

        // Count by referral source
        $this->db->where('dietitian_id', $dietitian_id);
        $this->db->where('referral_source', 'platform');
        $stats['platform_count'] = $this->db->count_all_results(db_prefix() . $this->table);

        $this->db->where('dietitian_id', $dietitian_id);
        $this->db->where('referral_source', 'dietitian');
        $stats['dietitian_count'] = $this->db->count_all_results(db_prefix() . $this->table);

        // Count patients
        $this->db->select('COUNT(DISTINCT patient_id) as patient_count');
        $this->db->where('dietitian_id', $dietitian_id);
        $result = $this->db->get(db_prefix() . $this->table)->row();
        $stats['total_patients'] = $result ? (int)$result->patient_count : 0;

        return $stats;
    }

    /**
     * Get revenue by period for dietitian
     *
     * @param int $dietitian_id
     * @param string $period (month, year)
     * @param int $limit
     * @return array
     */
    public function get_revenue_by_period($dietitian_id, $period = 'month', $limit = 12)
    {
        $date_format = ($period == 'month') ? '%Y-%m' : '%Y';

        $this->db->select('DATE_FORMAT(i.issue_date, "' . $date_format . '") as period, ' .
            'SUM(rs.dietitian_share) as revenue, ' .
            'COUNT(DISTINCT rs.patient_id) as patient_count');
        $this->db->from(db_prefix() . $this->table . ' rs');
        $this->db->join(db_prefix() . 'dietic_invoices i', 'i.id = rs.invoice_id');
        $this->db->where('rs.dietitian_id', $dietitian_id);
        $this->db->where('rs.status', 'paid');
        $this->db->group_by('period');
        $this->db->order_by('period', 'DESC');
        $this->db->limit($limit);

        return $this->db->get()->result();
    }

    /**
     * Get revenue breakdown by patient for dietitian
     *
     * @param int $dietitian_id
     * @param array $filters
     * @return array
     */
    public function get_revenue_by_patient($dietitian_id, $filters = [])
    {
        $this->db->select('p.id as patient_id, p.company as patient_name, ' .
            'SUM(rs.dietitian_share) as total_revenue, ' .
            'COUNT(rs.id) as payment_count, ' .
            'MAX(i.issue_date) as last_payment_date');
        $this->db->from(db_prefix() . $this->table . ' rs');
        $this->db->join(db_prefix() . 'dietic_invoices i', 'i.id = rs.invoice_id');
        $this->db->join(db_prefix() . 'dietic_patients p', 'p.id = rs.patient_id');
        $this->db->where('rs.dietitian_id', $dietitian_id);
        $this->db->where('rs.status', 'paid');

        if (isset($filters['date_from'])) {
            $this->db->where('rs.paid_date >=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $this->db->where('rs.paid_date <=', $filters['date_to']);
        }

        $this->db->group_by('rs.patient_id');
        $this->db->order_by('total_revenue', 'DESC');

        return $this->db->get()->result();
    }

    /**
     * Get platform statistics
     *
     * @return array
     */
    public function get_platform_statistics()
    {
        if (!is_admin()) {
            return [];
        }

        $stats = [];

        // Total platform revenue
        $this->db->select_sum('platform_share');
        $this->db->where('status', 'paid');
        $result = $this->db->get(db_prefix() . $this->table)->row();
        $stats['total_platform_revenue'] = $result ? (float)$result->platform_share : 0;

        // Total dietitian payments
        $this->db->select_sum('dietitian_share');
        $this->db->where('status', 'paid');
        $result = $this->db->get(db_prefix() . $this->table)->row();
        $stats['total_dietitian_payments'] = $result ? (float)$result->dietitian_share : 0;

        // Pending dietitian payments
        $this->db->select_sum('dietitian_share');
        $this->db->where('status', 'pending');
        $result = $this->db->get(db_prefix() . $this->table)->row();
        $stats['pending_dietitian_payments'] = $result ? (float)$result->dietitian_share : 0;

        return $stats;
    }

    /**
     * Delete revenue share
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $revenue_share = $this->get($id);

        if (!$revenue_share || $revenue_share->status == 'paid') {
            return false;
        }

        $this->db->where('id', $id);
        $result = $this->db->delete(db_prefix() . $this->table);

        if ($result) {
            log_activity('Revenue Share Deleted [ID: ' . $id . ']');
        }

        return $result;
    }
}
