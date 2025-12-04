<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Migration Script: Add Snack (Collation) to Food Surveys
 *
 * This script adds snack columns to food_survey_entries table
 * and updates ENUM values for meal_type to include 'snack'
 *
 * Run this script from: admin/dietetic/food_surveys/add_snack
 */

// This is a migration script that needs to be run via a controller
// See Food_surveys controller for the route that executes this

class Add_snack_migration
{
    private $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
    }

    /**
     * Run the migration
     */
    public function run()
    {
        $results = [];

        try {
            // Step 1: Add snack columns to food_survey_entries table
            $results[] = $this->add_snack_columns();

            // Step 2: Update meal_type ENUM in recommendations table
            $results[] = $this->update_meal_type_enum();

            return [
                'success' => true,
                'message' => 'Migration terminée avec succès',
                'details' => $results
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Erreur lors de la migration: ' . $e->getMessage(),
                'details' => $results
            ];
        }
    }

    /**
     * Add snack columns to food_survey_entries table
     */
    private function add_snack_columns()
    {
        $table = db_prefix() . 'dietic_food_survey_entries';

        // Check if snack_photo column already exists
        if ($this->CI->db->field_exists('snack_photo', $table)) {
            return [
                'step' => 'add_snack_columns',
                'status' => 'skipped',
                'message' => 'Les colonnes collation existent déjà'
            ];
        }

        // Add snack columns after dinner columns
        $sql = "ALTER TABLE `{$table}`
                ADD COLUMN `snack_photo` varchar(255) DEFAULT NULL AFTER `dinner_notes`,
                ADD COLUMN `snack_time` time DEFAULT NULL AFTER `snack_photo`,
                ADD COLUMN `snack_notes` text DEFAULT NULL AFTER `snack_time`";

        $this->CI->db->query($sql);

        return [
            'step' => 'add_snack_columns',
            'status' => 'success',
            'message' => 'Colonnes collation ajoutées avec succès'
        ];
    }

    /**
     * Update meal_type ENUM to include 'snack'
     */
    private function update_meal_type_enum()
    {
        $table = db_prefix() . 'dietic_food_survey_recommendations';

        // Check if table exists and has meal_type column
        if (!$this->CI->db->table_exists($table)) {
            return [
                'step' => 'update_meal_type_enum',
                'status' => 'skipped',
                'message' => 'Table recommendations n\'existe pas'
            ];
        }

        if (!$this->CI->db->field_exists('meal_type', $table)) {
            return [
                'step' => 'update_meal_type_enum',
                'status' => 'skipped',
                'message' => 'Colonne meal_type n\'existe pas encore'
            ];
        }

        // Update ENUM to include 'snack'
        $sql = "ALTER TABLE `{$table}`
                MODIFY COLUMN `meal_type` ENUM('breakfast', 'lunch', 'dinner', 'snack', 'global') DEFAULT 'global'";

        $this->CI->db->query($sql);

        return [
            'step' => 'update_meal_type_enum',
            'status' => 'success',
            'message' => 'ENUM meal_type mis à jour avec snack'
        ];
    }

    /**
     * Check if migration is needed
     */
    public function is_needed()
    {
        $table = db_prefix() . 'dietic_food_survey_entries';

        if (!$this->CI->db->table_exists($table)) {
            return false; // Table doesn't exist yet
        }

        // Check if snack_photo column exists
        return !$this->CI->db->field_exists('snack_photo', $table);
    }
}
