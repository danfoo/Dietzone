<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dietetic_meal_plans_model extends App_Model
{
    private $table = 'dietic_meal_plans';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get meal plan by ID
     *
     * @param int $id
     * @param bool $with_meals Include meals and foods
     * @return object|null
     */
    public function get($id, $with_meals = true)
    {
        $this->db->where('id', $id);
        $plan = $this->db->get(db_prefix() . $this->table)->row();

        if ($plan && $with_meals) {
            $plan->meals = $this->get_meals($id);
        }

        return $plan;
    }

    /**
     * Get meal plans by program
     *
     * @param int $program_id
     * @return array
     */
    public function get_by_program($program_id)
    {
        $this->db->where('program_id', $program_id);
        $this->db->order_by('week_number', 'ASC');

        return $this->db->get(db_prefix() . $this->table)->result();
    }

    /**
     * Add new meal plan
     *
     * @param array $data
     * @return int|bool
     */
    public function add($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');

        if ($this->db->insert(db_prefix() . $this->table, $data)) {
            $plan_id = $this->db->insert_id();
            log_activity('New Meal Plan Created [ID: ' . $plan_id . ']');
            return $plan_id;
        }

        return false;
    }

    /**
     * Update meal plan
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
     * Delete meal plan
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete(db_prefix() . $this->table);
    }

    /**
     * Get meals for meal plan
     *
     * @param int $meal_plan_id
     * @return array
     */
    public function get_meals($meal_plan_id)
    {
        $this->db->where('meal_plan_id', $meal_plan_id);
        $this->db->order_by('day_of_week', 'ASC');
        $this->db->order_by('display_order', 'ASC');

        $meals = $this->db->get(db_prefix() . 'dietic_meals')->result();

        // Get foods for each meal
        foreach ($meals as &$meal) {
            $meal->foods = $this->get_meal_foods($meal->id);
        }

        return $meals;
    }

    /**
     * Get foods for a meal
     *
     * @param int $meal_id
     * @return array
     */
    public function get_meal_foods($meal_id)
    {
        $this->db->select(db_prefix() . 'dietic_meal_foods.*, ' .
            db_prefix() . 'dietic_foods.food_name, ' .
            db_prefix() . 'dietic_foods.food_name_fr, ' .
            db_prefix() . 'dietic_foods.category, ' .
            db_prefix() . 'dietic_foods.calories, ' .
            db_prefix() . 'dietic_foods.protein, ' .
            db_prefix() . 'dietic_foods.carbs, ' .
            db_prefix() . 'dietic_foods.fats, ' .
            db_prefix() . 'dietic_foods.fiber, ' .
            db_prefix() . 'dietic_foods.serving_size, ' .
            db_prefix() . 'dietic_foods.serving_unit');
        $this->db->join(db_prefix() . 'dietic_foods', db_prefix() . 'dietic_foods.id = ' . db_prefix() . 'dietic_meal_foods.food_id', 'left');
        $this->db->where('meal_id', $meal_id);
        $this->db->order_by('display_order', 'ASC');

        return $this->db->get(db_prefix() . 'dietic_meal_foods')->result();
    }

    /**
     * Add meal to plan
     *
     * @param array $data
     * @return int|bool
     */
    public function add_meal($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');

        if ($this->db->insert(db_prefix() . 'dietic_meals', $data)) {
            return $this->db->insert_id();
        }

        return false;
    }

    /**
     * Update meal
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update_meal($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update(db_prefix() . 'dietic_meals', $data);
    }

    /**
     * Delete meal
     *
     * @param int $id
     * @return bool
     */
    public function delete_meal($id)
    {
        // Delete associated foods first
        $this->db->where('meal_id', $id);
        $this->db->delete(db_prefix() . 'dietic_meal_foods');

        // Delete meal
        $this->db->where('id', $id);
        return $this->db->delete(db_prefix() . 'dietic_meals');
    }

    /**
     * Add food to meal
     *
     * @param array $data
     * @return int|bool
     */
    public function add_food_to_meal($data)
    {
        if ($this->db->insert(db_prefix() . 'dietic_meal_foods', $data)) {
            return $this->db->insert_id();
        }

        return false;
    }

    /**
     * Remove food from meal
     *
     * @param int $id
     * @return bool
     */
    public function remove_food_from_meal($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete(db_prefix() . 'dietic_meal_foods');
    }

    /**
     * Update meal food
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update_meal_food($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update(db_prefix() . 'dietic_meal_foods', $data);
    }

    /**
     * Calculate total nutrition for meal plan
     *
     * @param int $meal_plan_id
     * @return object
     */
    public function calculate_plan_nutrition($meal_plan_id)
    {
        $meals = $this->get_meals($meal_plan_id);

        $totals = new stdClass();
        $totals->calories = 0;
        $totals->protein = 0;
        $totals->carbs = 0;
        $totals->fats = 0;
        $totals->fiber = 0;

        foreach ($meals as $meal) {
            foreach ($meal->foods as $food) {
                // Calculate ratio based on quantity
                $ratio = $food->quantity / $food->serving_size;

                $totals->calories += $food->calories * $ratio;
                $totals->protein += $food->protein * $ratio;
                $totals->carbs += $food->carbs * $ratio;
                $totals->fats += $food->fats * $ratio;
                $totals->fiber += $food->fiber * $ratio;
            }
        }

        return $totals;
    }

    /**
     * Get meals grouped by day
     *
     * @param int $meal_plan_id
     * @return array
     */
    public function get_meals_by_day($meal_plan_id)
    {
        $meals = $this->get_meals($meal_plan_id);
        $grouped = [];

        for ($day = 1; $day <= 7; $day++) {
            $grouped[$day] = array_filter($meals, function($meal) use ($day) {
                return $meal->day_of_week == $day;
            });
        }

        return $grouped;
    }

    /**
     * Duplicate meal plan to new week
     *
     * @param int $meal_plan_id
     * @param int $new_week_number
     * @return int|bool New meal plan ID
     */
    public function duplicate_plan($meal_plan_id, $new_week_number)
    {
        $original_plan = $this->get($meal_plan_id, true);

        if (!$original_plan) {
            return false;
        }

        // Create new plan
        $new_plan_data = [
            'program_id'  => $original_plan->program_id,
            'week_number' => $new_week_number,
            'plan_name'   => $original_plan->plan_name . ' (Week ' . $new_week_number . ')',
            'notes'       => $original_plan->notes,
        ];

        $new_plan_id = $this->add($new_plan_data);

        if (!$new_plan_id) {
            return false;
        }

        // Duplicate meals
        foreach ($original_plan->meals as $meal) {
            $meal_data = [
                'meal_plan_id'  => $new_plan_id,
                'day_of_week'   => $meal->day_of_week,
                'meal_type'     => $meal->meal_type,
                'meal_name'     => $meal->meal_name,
                'meal_time'     => $meal->meal_time,
                'description'   => $meal->description,
                'instructions'  => $meal->instructions,
                'display_order' => $meal->display_order,
            ];

            $new_meal_id = $this->add_meal($meal_data);

            if ($new_meal_id) {
                // Duplicate foods
                foreach ($meal->foods as $food) {
                    $this->add_food_to_meal([
                        'meal_id'       => $new_meal_id,
                        'food_id'       => $food->food_id,
                        'quantity'      => $food->quantity,
                        'unit'          => $food->unit,
                        'display_order' => $food->display_order,
                    ]);
                }
            }
        }

        return $new_plan_id;
    }
}
