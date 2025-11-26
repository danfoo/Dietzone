<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Migration: Add neck and calf measurements
 * Date: 2025-11-26
 *
 * Adds neck and calf circumference columns to the measurements table
 */
class Migration_add_neck_calf_measurements
{
    private $ci;

    public function __construct()
    {
        $this->ci = &get_instance();
    }

    /**
     * Apply migration
     */
    public function up()
    {
        $table_name = db_prefix() . 'dietic_measurements';

        // Check if neck column exists, if not add it
        if (!$this->column_exists($table_name, 'neck')) {
            $this->ci->db->query("
                ALTER TABLE `{$table_name}`
                ADD COLUMN `neck` decimal(5,2) DEFAULT NULL COMMENT 'Tour de cou in cm'
                AFTER `hips`
            ");
            log_activity('Migration: Added neck column to measurements table');
        }

        // Check if calf column exists, if not add it
        if (!$this->column_exists($table_name, 'calf')) {
            $this->ci->db->query("
                ALTER TABLE `{$table_name}`
                ADD COLUMN `calf` decimal(5,2) DEFAULT NULL COMMENT 'Tour de mollets in cm'
                AFTER `thighs`
            ");
            log_activity('Migration: Added calf column to measurements table');
        }

        log_activity('Migration applied: add_neck_calf_measurements');
    }

    /**
     * Rollback migration (optional)
     */
    public function down()
    {
        $table_name = db_prefix() . 'dietic_measurements';

        // Remove calf column if exists
        if ($this->column_exists($table_name, 'calf')) {
            $this->ci->db->query("ALTER TABLE `{$table_name}` DROP COLUMN `calf`");
        }

        // Remove neck column if exists
        if ($this->column_exists($table_name, 'neck')) {
            $this->ci->db->query("ALTER TABLE `{$table_name}` DROP COLUMN `neck`");
        }

        log_activity('Migration rolled back: add_neck_calf_measurements');
    }

    /**
     * Check if column exists in table
     *
     * @param string $table_name
     * @param string $column_name
     * @return bool
     */
    private function column_exists($table_name, $column_name)
    {
        $query = $this->ci->db->query("
            SELECT COUNT(*) as count
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE table_schema = DATABASE()
            AND table_name = '{$table_name}'
            AND column_name = '{$column_name}'
        ");

        $result = $query->row();
        return $result->count > 0;
    }
}
