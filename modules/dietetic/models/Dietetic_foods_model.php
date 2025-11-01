<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dietetic_foods_model extends App_Model
{
    private $table = 'dietic_foods';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get food by ID
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
     * Get all foods
     *
     * @param array $where
     * @return array
     */
    public function get_all($where = [])
    {
        if (!empty($where)) {
            $this->db->where($where);
        }

        $this->db->order_by('food_name', 'ASC');

        return $this->db->get(db_prefix() . $this->table)->result();
    }

    /**
     * Get active foods
     *
     * @return array
     */
    public function get_active()
    {
        $this->db->where('is_active', 1);
        $this->db->order_by('food_name', 'ASC');

        return $this->db->get(db_prefix() . $this->table)->result();
    }

    /**
     * Get foods by category
     *
     * @param string $category
     * @return array
     */
    public function get_by_category($category)
    {
        $this->db->where('category', $category);
        $this->db->where('is_active', 1);
        $this->db->order_by('food_name', 'ASC');

        return $this->db->get(db_prefix() . $this->table)->result();
    }

    /**
     * Search foods
     *
     * @param string $search
     * @return array
     */
    public function search($search)
    {
        $this->db->group_start();
        $this->db->like('food_name', $search);
        $this->db->or_like('food_name_fr', $search);
        $this->db->or_like('category', $search);
        $this->db->group_end();
        $this->db->where('is_active', 1);
        $this->db->order_by('food_name', 'ASC');

        return $this->db->get(db_prefix() . $this->table)->result();
    }

    /**
     * Add new food
     *
     * @param array $data
     * @return int|bool
     */
    public function add($data)
    {
        $data['created_by'] = get_staff_user_id();
        $data['created_at'] = date('Y-m-d H:i:s');

        if ($this->db->insert(db_prefix() . $this->table, $data)) {
            $food_id = $this->db->insert_id();
            log_activity('New Food Added to Database [ID: ' . $food_id . ']');
            return $food_id;
        }

        return false;
    }

    /**
     * Update food
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        $this->db->where('id', $id);
        return $this->db->update(db_prefix() . $this->table, $data);
    }

    /**
     * Delete food
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        // Check if food is used in any meal
        $this->db->where('food_id', $id);
        $usage_count = $this->db->count_all_results(db_prefix() . 'dietic_meal_foods');

        if ($usage_count > 0) {
            // Don't delete, just deactivate
            return $this->update($id, ['is_active' => 0]);
        }

        $this->db->where('id', $id);
        return $this->db->delete(db_prefix() . $this->table);
    }

    /**
     * Calculate nutritional values for quantity
     *
     * @param int $food_id
     * @param float $quantity
     * @param string $unit
     * @return object|null
     */
    public function calculate_nutrition($food_id, $quantity, $unit = 'g')
    {
        $food = $this->get($food_id);

        if (!$food) {
            return null;
        }

        // Convert to serving size ratio
        $ratio = $quantity / $food->serving_size;

        $nutrition = new stdClass();
        $nutrition->calories = $food->calories * $ratio;
        $nutrition->protein = $food->protein * $ratio;
        $nutrition->carbs = $food->carbs * $ratio;
        $nutrition->fats = $food->fats * $ratio;
        $nutrition->fiber = $food->fiber * $ratio;
        $nutrition->sugar = $food->sugar * $ratio;
        $nutrition->sodium = $food->sodium * $ratio;

        return $nutrition;
    }

    /**
     * Get total count
     *
     * @return int
     */
    public function get_total_count()
    {
        return $this->db->count_all(db_prefix() . $this->table);
    }

    /**
     * Import foods from CSV
     *
     * @param string $csv_file
     * @return array ['success' => int, 'errors' => array]
     */
    public function import_from_csv($csv_file)
    {
        $success_count = 0;
        $errors = [];

        if (($handle = fopen($csv_file, 'r')) !== false) {
            // Skip header row
            $header = fgetcsv($handle);

            while (($row = fgetcsv($handle)) !== false) {
                try {
                    $data = [
                        'food_name'    => $row[0],
                        'food_name_fr' => $row[1] ?? null,
                        'category'     => $row[2] ?? 'other',
                        'serving_size' => $row[3] ?? 100,
                        'serving_unit' => $row[4] ?? 'g',
                        'calories'     => $row[5] ?? 0,
                        'protein'      => $row[6] ?? 0,
                        'carbs'        => $row[7] ?? 0,
                        'fats'         => $row[8] ?? 0,
                        'fiber'        => $row[9] ?? 0,
                        'sugar'        => $row[10] ?? 0,
                        'sodium'       => $row[11] ?? 0,
                    ];

                    if ($this->add($data)) {
                        $success_count++;
                    }
                } catch (Exception $e) {
                    $errors[] = 'Row error: ' . $e->getMessage();
                }
            }

            fclose($handle);
        }

        return ['success' => $success_count, 'errors' => $errors];
    }
}
