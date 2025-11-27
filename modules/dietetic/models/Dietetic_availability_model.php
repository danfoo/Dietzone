<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Dietetic Availability Model
 *
 * Manages dietitian availability schedules and slot checking
 */
class Dietetic_availability_model extends App_Model
{
    private $table = 'dietic_dietitian_availability';
    private $types_table = 'dietic_consultation_types';

    public function __construct()
    {
        parent::__construct();
        $this->table = db_prefix() . $this->table;
        $this->types_table = db_prefix() . $this->types_table;
    }

    /**
     * Get all availability slots for a dietitian
     *
     * @param int $dietitian_id
     * @param bool $active_only
     * @return array
     */
    public function get_by_dietitian($dietitian_id, $active_only = true)
    {
        $this->db->where('dietitian_id', $dietitian_id);

        if ($active_only) {
            $this->db->where('is_active', 1);
        }

        $this->db->order_by('day_of_week', 'ASC');
        $this->db->order_by('start_time', 'ASC');

        return $this->db->get($this->table)->result();
    }

    /**
     * Get availability slots for a specific day of week
     *
     * @param int $dietitian_id
     * @param int $day_of_week (0-6)
     * @return array
     */
    public function get_by_day($dietitian_id, $day_of_week)
    {
        $this->db->where('dietitian_id', $dietitian_id);
        $this->db->where('day_of_week', $day_of_week);
        $this->db->where('is_active', 1);
        $this->db->order_by('start_time', 'ASC');

        return $this->db->get($this->table)->result();
    }

    /**
     * Add new availability slot
     *
     * @param array $data
     * @return int|bool Insert ID on success, false on failure
     */
    public function add($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');

        if ($this->db->insert($this->table, $data)) {
            return $this->db->insert_id();
        }

        return false;
    }

    /**
     * Update availability slot
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    /**
     * Delete availability slot
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }

    /**
     * Get single availability slot
     *
     * @param int $id
     * @return object|null
     */
    public function get($id)
    {
        $this->db->where('id', $id);
        return $this->db->get($this->table)->row();
    }

    /**
     * Check if a dietitian is available at a specific datetime
     *
     * @param int $dietitian_id
     * @param string $datetime (Y-m-d H:i:s or Y-m-d H:i)
     * @param int $duration_minutes
     * @param int|null $exclude_consultation_id (for editing existing)
     * @return array ['available' => bool, 'reason' => string, 'conflicts' => array]
     */
    public function check_availability($dietitian_id, $datetime, $duration_minutes, $exclude_consultation_id = null)
    {
        // Parse datetime
        $dt = new DateTime($datetime);
        $day_of_week = (int)$dt->format('w'); // 0 (Sunday) to 6 (Saturday)
        $time = $dt->format('H:i:s');
        $date = $dt->format('Y-m-d');

        // Calculate end time
        $end_dt = clone $dt;
        $end_dt->add(new DateInterval('PT' . $duration_minutes . 'M'));
        $end_time = $end_dt->format('H:i:s');

        // Step 1: Check if dietitian has availability defined for this day
        $availability_slots = $this->get_by_day($dietitian_id, $day_of_week);

        if (empty($availability_slots)) {
            return [
                'available' => false,
                'reason' => 'Le diététicien ne travaille pas ce jour-là',
                'conflicts' => []
            ];
        }

        // Step 2: Check if requested time falls within any availability slot
        $within_working_hours = false;

        foreach ($availability_slots as $slot) {
            // Check if consultation time overlaps with this availability slot
            if ($time >= $slot->start_time && $end_time <= $slot->end_time) {
                $within_working_hours = true;
                break;
            }
        }

        if (!$within_working_hours) {
            return [
                'available' => false,
                'reason' => 'Hors des horaires de travail du diététicien',
                'conflicts' => []
            ];
        }

        // Step 3: Check for conflicts with existing consultations
        $this->load->model('dietetic/dietetic_consultations_model');

        $this->db->select('id, consultation_date, duration, patient_id, status');
        $this->db->from(db_prefix() . 'dietic_consultations');
        $this->db->where('dietitian_id', $dietitian_id);
        $this->db->where('DATE(consultation_date)', $date);
        $this->db->where_in('status', ['scheduled', 'confirmed']); // Only check active consultations

        if ($exclude_consultation_id) {
            $this->db->where('id !=', $exclude_consultation_id);
        }

        $existing_consultations = $this->db->get()->result();

        $conflicts = [];

        foreach ($existing_consultations as $consultation) {
            $consult_start = new DateTime($consultation->consultation_date);
            $consult_end = clone $consult_start;
            $consult_end->add(new DateInterval('PT' . $consultation->duration . 'M'));

            // Check if times overlap
            // Overlap occurs if: (start1 < end2) AND (start2 < end1)
            if ($dt < $consult_end && $end_dt > $consult_start) {
                $conflicts[] = [
                    'id' => $consultation->id,
                    'start' => $consult_start->format('H:i'),
                    'end' => $consult_end->format('H:i'),
                    'patient_id' => $consultation->patient_id
                ];
            }
        }

        if (!empty($conflicts)) {
            return [
                'available' => false,
                'reason' => 'Le créneau est déjà réservé',
                'conflicts' => $conflicts
            ];
        }

        // All checks passed
        return [
            'available' => true,
            'reason' => 'Créneau disponible',
            'conflicts' => []
        ];
    }

    /**
     * Get available time slots for a specific date
     *
     * @param int $dietitian_id
     * @param string $date (Y-m-d)
     * @param int $consultation_type_id (optional)
     * @return array Array of available slots with start_time and end_time
     */
    public function get_available_slots($dietitian_id, $date, $consultation_type_id = null)
    {
        $dt = new DateTime($date);
        $day_of_week = (int)$dt->format('w');

        // Get availability for this day
        $availability_slots = $this->get_by_day($dietitian_id, $day_of_week);

        if (empty($availability_slots)) {
            return [];
        }

        // Get consultation type duration
        $duration = 60; // Default
        if ($consultation_type_id) {
            $type = $this->get_consultation_type($consultation_type_id);
            if ($type) {
                $duration = $type->duration;
            }
        }

        // Get existing consultations for this date
        $this->db->select('consultation_date, duration');
        $this->db->from(db_prefix() . 'dietic_consultations');
        $this->db->where('dietitian_id', $dietitian_id);
        $this->db->where('DATE(consultation_date)', $date);
        $this->db->where_in('status', ['scheduled', 'confirmed']);
        $existing_consultations = $this->db->get()->result();

        $available_slots = [];

        // For each availability block, generate time slots
        foreach ($availability_slots as $block) {
            $slot_duration = $block->slot_duration ?? 60;

            $current_time = new DateTime($date . ' ' . $block->start_time);
            $end_time = new DateTime($date . ' ' . $block->end_time);

            while ($current_time < $end_time) {
                $slot_end = clone $current_time;
                $slot_end->add(new DateInterval('PT' . $duration . 'M'));

                // Check if this slot fits within the availability block
                if ($slot_end <= $end_time) {
                    // Check if slot conflicts with existing consultations
                    $has_conflict = false;

                    foreach ($existing_consultations as $consultation) {
                        $consult_start = new DateTime($consultation->consultation_date);
                        $consult_end = clone $consult_start;
                        $consult_end->add(new DateInterval('PT' . $consultation->duration . 'M'));

                        // Check overlap
                        if ($current_time < $consult_end && $slot_end > $consult_start) {
                            $has_conflict = true;
                            break;
                        }
                    }

                    if (!$has_conflict) {
                        // Check if slot is not in the past
                        $now = new DateTime();
                        if ($current_time > $now) {
                            $available_slots[] = [
                                'start_time' => $current_time->format('H:i'),
                                'end_time' => $slot_end->format('H:i'),
                                'datetime' => $current_time->format('Y-m-d H:i:s')
                            ];
                        }
                    }
                }

                // Move to next slot
                $current_time->add(new DateInterval('PT' . $slot_duration . 'M'));
            }
        }

        return $available_slots;
    }

    /**
     * Get all consultation types
     *
     * @param bool $active_only
     * @return array
     */
    public function get_consultation_types($active_only = true)
    {
        if ($active_only) {
            $this->db->where('is_active', 1);
        }

        $this->db->order_by('display_order', 'ASC');
        return $this->db->get($this->types_table)->result();
    }

    /**
     * Get single consultation type
     *
     * @param int $id
     * @return object|null
     */
    public function get_consultation_type($id)
    {
        $this->db->where('id', $id);
        return $this->db->get($this->types_table)->row();
    }

    /**
     * Get consultation type by slug
     *
     * @param string $slug
     * @return object|null
     */
    public function get_consultation_type_by_slug($slug)
    {
        $this->db->where('slug', $slug);
        return $this->db->get($this->types_table)->row();
    }

    /**
     * Add consultation type
     *
     * @param array $data
     * @return int|bool
     */
    public function add_consultation_type($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');

        if ($this->db->insert($this->types_table, $data)) {
            return $this->db->insert_id();
        }

        return false;
    }

    /**
     * Update consultation type
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update_consultation_type($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        $this->db->where('id', $id);
        return $this->db->update($this->types_table, $data);
    }

    /**
     * Delete consultation type
     *
     * @param int $id
     * @return bool
     */
    public function delete_consultation_type($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->types_table);
    }

    /**
     * Get dietitian's working days (days with availability)
     *
     * @param int $dietitian_id
     * @return array Array of day numbers (0-6)
     */
    public function get_working_days($dietitian_id)
    {
        $this->db->select('DISTINCT day_of_week');
        $this->db->where('dietitian_id', $dietitian_id);
        $this->db->where('is_active', 1);
        $this->db->order_by('day_of_week', 'ASC');

        $result = $this->db->get($this->table)->result();

        return array_map(function($row) {
            return (int)$row->day_of_week;
        }, $result);
    }
}
