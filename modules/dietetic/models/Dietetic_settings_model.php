<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Dietetic Settings Model
 * Handles module settings including legal pages
 */
class Dietetic_settings_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get a setting value by key
     * @param string $key
     * @return string|null
     */
    public function get_setting($key)
    {
        $this->db->where('setting_key', $key);
        $result = $this->db->get(db_prefix() . 'dietic_settings')->row();

        return $result ? $result->setting_value : null;
    }

    /**
     * Update or insert a setting
     * @param string $key
     * @param string $value
     * @return bool
     */
    public function update_setting($key, $value)
    {
        // Check if setting exists
        $this->db->where('setting_key', $key);
        $existing = $this->db->get(db_prefix() . 'dietic_settings')->row();

        if ($existing) {
            // Update existing setting
            $this->db->where('setting_key', $key);
            return $this->db->update(db_prefix() . 'dietic_settings', [
                'setting_value' => $value,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            // Insert new setting
            return $this->db->insert(db_prefix() . 'dietic_settings', [
                'setting_key' => $key,
                'setting_value' => $value,
                'setting_type' => 'text',
                'description' => 'Legal page content',
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
    }

    /**
     * Get all settings as key-value pairs
     * @return array
     */
    public function get_all_settings()
    {
        $settings = [];
        $results = $this->db->get(db_prefix() . 'dietic_settings')->result_array();

        foreach ($results as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }

        return $settings;
    }

    /**
     * Delete a setting by key
     * @param string $key
     * @return bool
     */
    public function delete_setting($key)
    {
        $this->db->where('setting_key', $key);
        return $this->db->delete(db_prefix() . 'dietic_settings');
    }
}
