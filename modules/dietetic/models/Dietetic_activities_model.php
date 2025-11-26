<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dietetic_activities_model extends App_Model
{
    private $activities_table = 'dietic_activities';
    private $patient_activities_table = 'dietic_patient_activities';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all activities from database
     *
     * @param bool $active_only - Get only active activities
     * @return array
     */
    public function get_all_activities($active_only = true)
    {
        if ($active_only) {
            $this->db->where('is_active', 1);
        }

        $this->db->order_by('category', 'ASC');
        $this->db->order_by('name', 'ASC');

        return $this->db->get(db_prefix() . $this->activities_table)->result();
    }

    /**
     * Get activity by ID
     *
     * @param int $id
     * @return object|null
     */
    public function get_activity($id)
    {
        $this->db->where('id', $id);
        return $this->db->get(db_prefix() . $this->activities_table)->row();
    }

    /**
     * Add new activity to database
     *
     * @param array $data
     * @return bool
     */
    public function add_activity($data)
    {
        return $this->db->insert(db_prefix() . $this->activities_table, $data);
    }

    /**
     * Update activity
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update_activity($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update(db_prefix() . $this->activities_table, $data);
    }

    /**
     * Delete activity (soft delete - set is_active to 0)
     *
     * @param int $id
     * @return bool
     */
    public function delete_activity($id)
    {
        // Soft delete - just deactivate
        $this->db->where('id', $id);
        return $this->db->update(db_prefix() . $this->activities_table, ['is_active' => 0]);
    }

    /**
     * Get all activities for a patient
     *
     * @param int $patient_id
     * @param int $limit
     * @return array
     */
    public function get_patient_activities($patient_id, $limit = null)
    {
        $this->db->select(
            db_prefix() . $this->patient_activities_table . '.*, ' .
            db_prefix() . $this->activities_table . '.name as activity_name, ' .
            db_prefix() . $this->activities_table . '.category, ' .
            db_prefix() . $this->activities_table . '.kcal_per_minute'
        );

        $this->db->join(
            db_prefix() . $this->activities_table,
            db_prefix() . $this->activities_table . '.id = ' .
            db_prefix() . $this->patient_activities_table . '.activity_id',
            'left'
        );

        $this->db->where(db_prefix() . $this->patient_activities_table . '.patient_id', $patient_id);
        $this->db->order_by(db_prefix() . $this->patient_activities_table . '.activity_date', 'DESC');
        $this->db->order_by(db_prefix() . $this->patient_activities_table . '.activity_time', 'DESC');

        if ($limit) {
            $this->db->limit($limit);
        }

        return $this->db->get(db_prefix() . $this->patient_activities_table)->result();
    }

    /**
     * Get patient activity by ID
     *
     * @param int $id
     * @return object|null
     */
    public function get_patient_activity($id)
    {
        $this->db->select(
            db_prefix() . $this->patient_activities_table . '.*, ' .
            db_prefix() . $this->activities_table . '.name as activity_name, ' .
            db_prefix() . $this->activities_table . '.category'
        );

        $this->db->join(
            db_prefix() . $this->activities_table,
            db_prefix() . $this->activities_table . '.id = ' .
            db_prefix() . $this->patient_activities_table . '.activity_id',
            'left'
        );

        $this->db->where(db_prefix() . $this->patient_activities_table . '.id', $id);

        return $this->db->get(db_prefix() . $this->patient_activities_table)->row();
    }

    /**
     * Add patient activity
     *
     * @param array $data
     * @return bool
     */
    public function add_patient_activity($data)
    {
        return $this->db->insert(db_prefix() . $this->patient_activities_table, $data);
    }

    /**
     * Update patient activity
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update_patient_activity($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update(db_prefix() . $this->patient_activities_table, $data);
    }

    /**
     * Delete patient activity
     *
     * @param int $id
     * @return bool
     */
    public function delete_patient_activity($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete(db_prefix() . $this->patient_activities_table);
    }

    /**
     * Get patient activity statistics
     *
     * @param int $patient_id
     * @param string $period - 'week', 'month', 'year', 'all'
     * @return object
     */
    public function get_patient_stats($patient_id, $period = 'all')
    {
        $this->db->select('
            COUNT(*) as total_activities,
            SUM(duration_minutes) as total_minutes,
            SUM(kcal_burned) as total_kcal,
            AVG(duration_minutes) as avg_duration,
            AVG(kcal_burned) as avg_kcal
        ');

        $this->db->where('patient_id', $patient_id);

        // Apply period filter
        if ($period !== 'all') {
            switch ($period) {
                case 'week':
                    $this->db->where('activity_date >=', date('Y-m-d', strtotime('-7 days')));
                    break;
                case 'month':
                    $this->db->where('activity_date >=', date('Y-m-d', strtotime('-30 days')));
                    break;
                case 'year':
                    $this->db->where('activity_date >=', date('Y-m-d', strtotime('-365 days')));
                    break;
            }
        }

        $result = $this->db->get(db_prefix() . $this->patient_activities_table)->row();

        // Return default values if no data
        if (!$result || $result->total_activities == 0) {
            return (object)[
                'total_activities' => 0,
                'total_minutes' => 0,
                'total_kcal' => 0,
                'avg_duration' => 0,
                'avg_kcal' => 0
            ];
        }

        return $result;
    }

    /**
     * Get activities by category for a patient
     *
     * @param int $patient_id
     * @return array
     */
    public function get_patient_activities_by_category($patient_id)
    {
        $this->db->select(
            db_prefix() . $this->activities_table . '.category, ' .
            'COUNT(*) as count, ' .
            'SUM(' . db_prefix() . $this->patient_activities_table . '.duration_minutes) as total_minutes, ' .
            'SUM(' . db_prefix() . $this->patient_activities_table . '.kcal_burned) as total_kcal'
        );

        $this->db->join(
            db_prefix() . $this->activities_table,
            db_prefix() . $this->activities_table . '.id = ' .
            db_prefix() . $this->patient_activities_table . '.activity_id',
            'left'
        );

        $this->db->where(db_prefix() . $this->patient_activities_table . '.patient_id', $patient_id);
        $this->db->group_by(db_prefix() . $this->activities_table . '.category');
        $this->db->order_by('total_kcal', 'DESC');

        return $this->db->get(db_prefix() . $this->patient_activities_table)->result();
    }

    /**
     * Get patient activities for a specific date range
     *
     * @param int $patient_id
     * @param string $start_date
     * @param string $end_date
     * @return array
     */
    public function get_patient_activities_by_date_range($patient_id, $start_date, $end_date)
    {
        $this->db->select(
            db_prefix() . $this->patient_activities_table . '.*, ' .
            db_prefix() . $this->activities_table . '.name as activity_name, ' .
            db_prefix() . $this->activities_table . '.category'
        );

        $this->db->join(
            db_prefix() . $this->activities_table,
            db_prefix() . $this->activities_table . '.id = ' .
            db_prefix() . $this->patient_activities_table . '.activity_id',
            'left'
        );

        $this->db->where(db_prefix() . $this->patient_activities_table . '.patient_id', $patient_id);
        $this->db->where(db_prefix() . $this->patient_activities_table . '.activity_date >=', $start_date);
        $this->db->where(db_prefix() . $this->patient_activities_table . '.activity_date <=', $end_date);
        $this->db->order_by(db_prefix() . $this->patient_activities_table . '.activity_date', 'DESC');

        return $this->db->get(db_prefix() . $this->patient_activities_table)->result();
    }

    /**
     * Get most performed activities for a patient
     *
     * @param int $patient_id
     * @param int $limit
     * @return array
     */
    public function get_patient_top_activities($patient_id, $limit = 5)
    {
        $this->db->select(
            db_prefix() . $this->activities_table . '.name, ' .
            db_prefix() . $this->activities_table . '.category, ' .
            'COUNT(*) as count, ' .
            'SUM(' . db_prefix() . $this->patient_activities_table . '.duration_minutes) as total_minutes, ' .
            'SUM(' . db_prefix() . $this->patient_activities_table . '.kcal_burned) as total_kcal'
        );

        $this->db->join(
            db_prefix() . $this->activities_table,
            db_prefix() . $this->activities_table . '.id = ' .
            db_prefix() . $this->patient_activities_table . '.activity_id',
            'left'
        );

        $this->db->where(db_prefix() . $this->patient_activities_table . '.patient_id', $patient_id);
        $this->db->group_by(db_prefix() . $this->patient_activities_table . '.activity_id');
        $this->db->order_by('count', 'DESC');
        $this->db->limit($limit);

        return $this->db->get(db_prefix() . $this->patient_activities_table)->result();
    }
}
