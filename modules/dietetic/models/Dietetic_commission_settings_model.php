<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dietetic_commission_settings_model extends App_Model
{
    private $table = 'dietic_commission_settings';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get commission setting by ID
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
     * Get all commission settings
     *
     * @param array $where
     * @return array
     */
    public function get_all($where = [])
    {
        if (!empty($where)) {
            $this->db->where($where);
        }

        $this->db->order_by('effective_from', 'DESC');

        return $this->db->get(db_prefix() . $this->table)->result();
    }

    /**
     * Get active commission setting for referral source
     *
     * @param string $referral_source (platform|dietitian)
     * @param string $date
     * @return object|null
     */
    public function get_active_for_source($referral_source, $date = null)
    {
        if ($date === null) {
            $date = date('Y-m-d');
        }

        $this->db->where('referral_source', $referral_source);
        $this->db->where('is_active', 1);
        $this->db->where('effective_from <=', $date);

        // Check if there's an effective_to date
        $this->db->group_start();
        $this->db->where('effective_to IS NULL');
        $this->db->or_where('effective_to >=', $date);
        $this->db->group_end();

        $this->db->order_by('effective_from', 'DESC');
        $this->db->limit(1);

        return $this->db->get(db_prefix() . $this->table)->row();
    }

    /**
     * Get current active settings for both sources
     *
     * @return array
     */
    public function get_current_settings()
    {
        $settings = [];

        $settings['platform'] = $this->get_active_for_source('platform');
        $settings['dietitian'] = $this->get_active_for_source('dietitian');

        return $settings;
    }

    /**
     * Add new commission setting
     *
     * @param array $data
     * @return int|bool
     */
    public function add($data)
    {
        $data['created_by'] = get_staff_user_id();
        $data['created_at'] = date('Y-m-d H:i:s');

        // Validate percentages add up to 100
        $total = (float)$data['dietitian_percentage'] + (float)$data['platform_percentage'];
        if ($total != 100.00) {
            return false;
        }

        // Start transaction
        $this->db->trans_start();

        // If this is active, deactivate previous settings for this source
        if (isset($data['is_active']) && $data['is_active'] == 1) {
            $this->db->where('referral_source', $data['referral_source']);
            $this->db->where('is_active', 1);
            $this->db->update(db_prefix() . $this->table, [
                'is_active' => 0,
                'effective_to' => date('Y-m-d', strtotime($data['effective_from'] . ' -1 day'))
            ]);
        }

        // Insert new setting
        $this->db->insert(db_prefix() . $this->table, $data);
        $setting_id = $this->db->insert_id();

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return false;
        }

        log_activity('New Commission Setting Created [Source: ' . $data['referral_source'] .
            ', Dietitian: ' . $data['dietitian_percentage'] . '%, Platform: ' . $data['platform_percentage'] . '%]');

        return $setting_id;
    }

    /**
     * Update commission setting
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data)
    {
        // Validate percentages add up to 100 if provided
        if (isset($data['dietitian_percentage']) && isset($data['platform_percentage'])) {
            $total = (float)$data['dietitian_percentage'] + (float)$data['platform_percentage'];
            if ($total != 100.00) {
                return false;
            }
        }

        $data['updated_at'] = date('Y-m-d H:i:s');

        $this->db->where('id', $id);
        $result = $this->db->update(db_prefix() . $this->table, $data);

        if ($result) {
            log_activity('Commission Setting Updated [ID: ' . $id . ']');
        }

        return $result;
    }

    /**
     * Deactivate commission setting
     *
     * @param int $id
     * @param string $effective_to
     * @return bool
     */
    public function deactivate($id, $effective_to = null)
    {
        if ($effective_to === null) {
            $effective_to = date('Y-m-d');
        }

        return $this->update($id, [
            'is_active' => 0,
            'effective_to' => $effective_to
        ]);
    }

    /**
     * Get commission history for referral source
     *
     * @param string $referral_source
     * @return array
     */
    public function get_history($referral_source)
    {
        $this->db->select('cs.*, CONCAT(s.firstname, " ", s.lastname) as created_by_name');
        $this->db->from(db_prefix() . $this->table . ' cs');
        $this->db->join(db_prefix() . 'staff s', 's.staffid = cs.created_by', 'left');
        $this->db->where('cs.referral_source', $referral_source);
        $this->db->order_by('cs.effective_from', 'DESC');

        return $this->db->get()->result();
    }

    /**
     * Calculate commission for amount
     *
     * @param float $amount
     * @param string $referral_source
     * @param string $date
     * @return array|null
     */
    public function calculate_commission($amount, $referral_source, $date = null)
    {
        $setting = $this->get_active_for_source($referral_source, $date);

        if (!$setting) {
            return null;
        }

        $dietitian_share = $amount * ($setting->dietitian_percentage / 100);
        $platform_share = $amount * ($setting->platform_percentage / 100);

        return [
            'total_amount' => $amount,
            'dietitian_percentage' => $setting->dietitian_percentage,
            'platform_percentage' => $setting->platform_percentage,
            'dietitian_share' => round($dietitian_share, 2),
            'platform_share' => round($platform_share, 2),
            'commission_setting_id' => $setting->id
        ];
    }

    /**
     * Delete commission setting
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $setting = $this->get($id);

        if (!$setting) {
            return false;
        }

        // Check if setting is used in any revenue share
        $this->db->where('commission_setting_id', $id);
        $usage_count = $this->db->count_all_results(db_prefix() . 'dietic_revenue_shares');

        if ($usage_count > 0) {
            // Don't delete, just deactivate
            return $this->deactivate($id);
        }

        $this->db->where('id', $id);
        $result = $this->db->delete(db_prefix() . $this->table);

        if ($result) {
            log_activity('Commission Setting Deleted [ID: ' . $id . ']');
        }

        return $result;
    }
}
