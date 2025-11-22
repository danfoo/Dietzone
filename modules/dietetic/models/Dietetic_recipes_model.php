<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Dietetic Recipes Model
 * Gestion de la bibliothèque de recettes
 */
class Dietetic_recipes_model extends App_Model
{
    private $table = 'dietic_recipes';
    private $table_ingredients = 'dietic_recipe_ingredients';
    private $table_instructions = 'dietic_recipe_instructions';
    private $table_nutrition = 'dietic_recipe_nutrition';
    private $table_photos = 'dietic_recipe_photos';
    private $table_tags = 'dietic_recipe_tags';
    private $table_assignments = 'dietic_recipe_assignments';
    private $table_ratings = 'dietic_recipe_ratings';
    private $table_favorites = 'dietic_recipe_favorites';

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('dietetic/dietetic');
    }

    /**
     * Get recipe by ID
     *
     * @param int $id
     * @param bool $check_status Whether to check if recipe is approved (for public display)
     * @return object|null
     */
    public function get($id, $check_status = false)
    {
        $this->db->select('r.*, ' .
            'CONCAT(s.firstname, " ", s.lastname) as dietitian_name, ' .
            's.staffid as dietitian_staff_id, ' .
            'CONCAT(adm.firstname, " ", adm.lastname) as approved_by_name');
        $this->db->from(db_prefix() . $this->table . ' r');
        $this->db->join(db_prefix() . 'staff s', 's.staffid = r.dietitian_id', 'left');
        $this->db->join(db_prefix() . 'staff adm', 'adm.staffid = r.approved_by_admin_id', 'left');
        $this->db->where('r.id', $id);

        // Si check_status = true, n'afficher que les recettes approuvées
        if ($check_status) {
            $this->db->where('r.status', 'approved');
        }

        $recipe = $this->db->get()->row();

        if ($recipe) {
            // Charger les données associées
            $recipe->ingredients = $this->get_ingredients($id);
            $recipe->instructions = $this->get_instructions($id);
            $recipe->nutrition = $this->get_nutrition($id);
            $recipe->photos = $this->get_photos($id);
            $recipe->tags = $this->get_tags($id);
            $recipe->average_rating = $this->get_average_rating($id);
            $recipe->ratings_count = $this->get_ratings_count($id);
        }

        return $recipe;
    }

    /**
     * Get all recipes
     *
     * @param array $where Additional where conditions
     * @param string $status Filter by status (all, pending, approved, rejected)
     * @return array
     */
    public function get_all($where = [], $status = 'all')
    {
        $this->db->select('r.*, ' .
            'CONCAT(s.firstname, " ", s.lastname) as dietitian_name, ' .
            's.staffid as dietitian_staff_id');
        $this->db->from(db_prefix() . $this->table . ' r');
        $this->db->join(db_prefix() . 'staff s', 's.staffid = r.dietitian_id', 'left');

        if (!empty($where)) {
            $this->db->where($where);
        }

        // Filter by status
        if ($status !== 'all') {
            $this->db->where('r.status', $status);
        }

        $this->db->order_by('r.created_at', 'DESC');

        $recipes = $this->db->get()->result();

        // Ajouter les informations supplémentaires
        foreach ($recipes as &$recipe) {
            $recipe->ingredients_count = $this->get_ingredients_count($recipe->id);
            $recipe->average_rating = $this->get_average_rating($recipe->id);
            $recipe->ratings_count = $this->get_ratings_count($recipe->id);
            $recipe->main_photo = $this->get_main_photo($recipe->id);
        }

        return $recipes;
    }

    /**
     * Get approved recipes (for public display)
     *
     * @param array $filters (category, tags, search, dietitian_id)
     * @return array
     */
    public function get_approved($filters = [])
    {
        $this->db->select('r.*, ' .
            'CONCAT(s.firstname, " ", s.lastname) as dietitian_name');
        $this->db->from(db_prefix() . $this->table . ' r');
        $this->db->join(db_prefix() . 'staff s', 's.staffid = r.dietitian_id', 'left');
        $this->db->where('r.status', 'approved');

        // Filter by category
        if (!empty($filters['category'])) {
            $this->db->where('r.category', $filters['category']);
        }

        // Filter by dietitian
        if (!empty($filters['dietitian_id'])) {
            $this->db->where('r.dietitian_id', $filters['dietitian_id']);
        }

        // Search in name and description
        if (!empty($filters['search'])) {
            $this->db->group_start();
            $this->db->like('r.name', $filters['search']);
            $this->db->or_like('r.description', $filters['search']);
            $this->db->group_end();
        }

        // Filter by tags (si spécifié)
        if (!empty($filters['tags'])) {
            $this->db->join(db_prefix() . $this->table_tags . ' rt', 'rt.recipe_id = r.id', 'inner');
            $this->db->where_in('rt.tag_name', $filters['tags']);
            $this->db->group_by('r.id');
        }

        $this->db->order_by('r.created_at', 'DESC');

        $recipes = $this->db->get()->result();

        // Ajouter les informations supplémentaires
        foreach ($recipes as &$recipe) {
            $recipe->main_photo = $this->get_main_photo($recipe->id);
            $recipe->average_rating = $this->get_average_rating($recipe->id);
            $recipe->ratings_count = $this->get_ratings_count($recipe->id);
            $recipe->tags = $this->get_tags($recipe->id);
        }

        return $recipes;
    }

    /**
     * Add new recipe
     *
     * @param array $data Recipe main data
     * @return int|bool Recipe ID or false
     */
    public function add($data)
    {
        $this->db->trans_start();

        // Préparer les données principales
        $recipe_data = [
            'dietitian_id' => isset($data['dietitian_id']) ? $data['dietitian_id'] : get_staff_user_id(),
            'name' => $data['name'],
            'description' => isset($data['description']) ? $data['description'] : null,
            'preparation_time' => isset($data['preparation_time']) ? $data['preparation_time'] : null,
            'category' => isset($data['category']) ? $data['category'] : null,
            'status' => 'pending', // Par défaut en attente d'approbation
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->db->insert(db_prefix() . $this->table, $recipe_data);
        $recipe_id = $this->db->insert_id();

        if ($recipe_id) {
            // Ajouter les ingrédients
            if (!empty($data['ingredients'])) {
                $this->add_ingredients($recipe_id, $data['ingredients']);
            }

            // Ajouter les instructions
            if (!empty($data['instructions'])) {
                $this->add_instructions($recipe_id, $data['instructions']);
            }

            // Ajouter les informations nutritionnelles
            if (!empty($data['nutrition'])) {
                $this->add_nutrition($recipe_id, $data['nutrition']);
            }

            // Ajouter les tags
            if (!empty($data['tags'])) {
                $this->add_tags($recipe_id, $data['tags']);
            }

            log_activity('New Recipe Created [ID: ' . $recipe_id . ', Name: ' . $data['name'] . ']');
        }

        $this->db->trans_complete();

        return $this->db->trans_status() ? $recipe_id : false;
    }

    /**
     * Update recipe
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data)
    {
        $this->db->trans_start();

        // Préparer les données principales
        $recipe_data = [];
        $allowed_fields = ['name', 'description', 'preparation_time', 'category'];

        foreach ($allowed_fields as $field) {
            if (isset($data[$field])) {
                $recipe_data[$field] = $data[$field];
            }
        }

        $recipe_data['updated_at'] = date('Y-m-d H:i:s');

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . $this->table, $recipe_data);

        // Mettre à jour les ingrédients
        if (isset($data['ingredients'])) {
            $this->delete_ingredients($id);
            $this->add_ingredients($id, $data['ingredients']);
        }

        // Mettre à jour les instructions
        if (isset($data['instructions'])) {
            $this->delete_instructions($id);
            $this->add_instructions($id, $data['instructions']);
        }

        // Mettre à jour les informations nutritionnelles
        if (isset($data['nutrition'])) {
            $this->delete_nutrition($id);
            $this->add_nutrition($id, $data['nutrition']);
        }

        // Mettre à jour les tags
        if (isset($data['tags'])) {
            $this->delete_tags($id);
            $this->add_tags($id, $data['tags']);
        }

        log_activity('Recipe Updated [ID: ' . $id . ']');

        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    /**
     * Delete recipe
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        // Vérifier que la recette n'est pas assignée à des patients
        $this->db->where('recipe_id', $id);
        $assignments_count = $this->db->count_all_results(db_prefix() . $this->table_assignments);

        if ($assignments_count > 0) {
            // Ne pas supprimer si assignée, juste archiver
            return $this->update($id, ['status' => 'archived']);
        }

        $this->db->trans_start();

        // Supprimer les données associées
        $this->delete_ingredients($id);
        $this->delete_instructions($id);
        $this->delete_nutrition($id);
        $this->delete_photos($id);
        $this->delete_tags($id);
        $this->delete_ratings($id);
        $this->delete_favorites($id);

        // Supprimer la recette
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . $this->table);

        log_activity('Recipe Deleted [ID: ' . $id . ']');

        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    /**
     * Approve recipe
     *
     * @param int $id
     * @param int $admin_id
     * @return bool
     */
    public function approve($id, $admin_id = null)
    {
        if (!$admin_id) {
            $admin_id = get_staff_user_id();
        }

        // Get recipe info before update (for notification)
        $recipe = $this->get($id);

        $data = [
            'status' => 'approved',
            'approved_by_admin_id' => $admin_id,
            'approved_at' => date('Y-m-d H:i:s'),
            'rejection_reason' => null
        ];

        $this->db->where('id', $id);
        $result = $this->db->update(db_prefix() . $this->table, $data);

        if ($result && $recipe) {
            log_activity('Recipe Approved [ID: ' . $id . ']');

            // Notify dietitian creator
            if ($recipe->dietitian_id) {
                $this->load->model('dietetic/dietetic_notifications_model');
                $this->dietetic_notifications_model->notify_recipe_approved(
                    $recipe->dietitian_id,
                    $recipe->name
                );
            }
        }

        return $result;
    }

    /**
     * Reject recipe
     *
     * @param int $id
     * @param string $reason
     * @param int $admin_id
     * @return bool
     */
    public function reject($id, $reason, $admin_id = null)
    {
        if (!$admin_id) {
            $admin_id = get_staff_user_id();
        }

        // Get recipe info before update (for notification)
        $recipe = $this->get($id);

        $data = [
            'status' => 'rejected',
            'approved_by_admin_id' => $admin_id,
            'approved_at' => date('Y-m-d H:i:s'),
            'rejection_reason' => $reason
        ];

        $this->db->where('id', $id);
        $result = $this->db->update(db_prefix() . $this->table, $data);

        if ($result && $recipe) {
            log_activity('Recipe Rejected [ID: ' . $id . ', Reason: ' . $reason . ']');

            // Notify dietitian creator
            if ($recipe->dietitian_id) {
                $this->load->model('dietetic/dietetic_notifications_model');
                $this->dietetic_notifications_model->notify_recipe_rejected(
                    $recipe->dietitian_id,
                    $recipe->name,
                    $reason
                );
            }
        }

        return $result;
    }

    // =====================================
    // INGREDIENTS
    // =====================================

    /**
     * Get ingredients for recipe
     */
    public function get_ingredients($recipe_id)
    {
        $this->db->where('recipe_id', $recipe_id);
        $this->db->order_by('`order`', 'ASC');
        return $this->db->get(db_prefix() . $this->table_ingredients)->result();
    }

    /**
     * Get ingredients count
     */
    public function get_ingredients_count($recipe_id)
    {
        $this->db->where('recipe_id', $recipe_id);
        return $this->db->count_all_results(db_prefix() . $this->table_ingredients);
    }

    /**
     * Add ingredients
     */
    private function add_ingredients($recipe_id, $ingredients)
    {
        foreach ($ingredients as $order => $ingredient) {
            $data = [
                'recipe_id' => $recipe_id,
                'ingredient_name' => $ingredient['name'],
                'quantity' => isset($ingredient['quantity']) ? $ingredient['quantity'] : null,
                'unit' => isset($ingredient['unit']) ? $ingredient['unit'] : null,
                'order' => $order
            ];
            $this->db->insert(db_prefix() . $this->table_ingredients, $data);
        }
    }

    /**
     * Delete ingredients
     */
    private function delete_ingredients($recipe_id)
    {
        $this->db->where('recipe_id', $recipe_id);
        $this->db->delete(db_prefix() . $this->table_ingredients);
    }

    // =====================================
    // INSTRUCTIONS
    // =====================================

    /**
     * Get instructions for recipe
     */
    public function get_instructions($recipe_id)
    {
        $this->db->where('recipe_id', $recipe_id);
        $this->db->order_by('step_number', 'ASC');
        return $this->db->get(db_prefix() . $this->table_instructions)->result();
    }

    /**
     * Add instructions
     */
    private function add_instructions($recipe_id, $instructions)
    {
        foreach ($instructions as $step_number => $instruction) {
            $data = [
                'recipe_id' => $recipe_id,
                'step_number' => $step_number + 1,
                'instruction' => is_array($instruction) ? $instruction['text'] : $instruction
            ];
            $this->db->insert(db_prefix() . $this->table_instructions, $data);
        }
    }

    /**
     * Delete instructions
     */
    private function delete_instructions($recipe_id)
    {
        $this->db->where('recipe_id', $recipe_id);
        $this->db->delete(db_prefix() . $this->table_instructions);
    }

    // =====================================
    // NUTRITION
    // =====================================

    /**
     * Get nutrition info for recipe
     */
    public function get_nutrition($recipe_id)
    {
        $this->db->where('recipe_id', $recipe_id);
        $nutrition = $this->db->get(db_prefix() . $this->table_nutrition)->row();

        // Map database column names (proteins, fats) to standard names (protein, fat)
        if ($nutrition) {
            if (isset($nutrition->proteins)) {
                $nutrition->protein = $nutrition->proteins;
            }
            if (isset($nutrition->fats)) {
                $nutrition->fat = $nutrition->fats;
            }
        }

        return $nutrition;
    }

    /**
     * Add nutrition info
     */
    private function add_nutrition($recipe_id, $nutrition)
    {
        $data = [
            'recipe_id' => $recipe_id,
            'calories' => isset($nutrition['calories']) ? $nutrition['calories'] : null,
            'proteins' => isset($nutrition['protein']) ? $nutrition['protein'] : null, // Note: column is 'proteins' with 's'
            'carbs' => isset($nutrition['carbs']) ? $nutrition['carbs'] : null,
            'fats' => isset($nutrition['fat']) ? $nutrition['fat'] : null, // Note: column is 'fats' with 's'
            'fiber' => isset($nutrition['fiber']) ? $nutrition['fiber'] : null,
            'sodium' => isset($nutrition['sodium']) ? $nutrition['sodium'] : null,
            'sugar' => isset($nutrition['sugar']) ? $nutrition['sugar'] : null
        ];
        $this->db->insert(db_prefix() . $this->table_nutrition, $data);
    }

    /**
     * Delete nutrition info
     */
    private function delete_nutrition($recipe_id)
    {
        $this->db->where('recipe_id', $recipe_id);
        $this->db->delete(db_prefix() . $this->table_nutrition);
    }

    // =====================================
    // PHOTOS
    // =====================================

    /**
     * Get photos for recipe
     */
    public function get_photos($recipe_id)
    {
        $this->db->where('recipe_id', $recipe_id);

        // La table utilise is_primary au lieu de is_main
        if ($this->db->field_exists('is_primary', db_prefix() . $this->table_photos)) {
            $this->db->order_by('is_primary', 'DESC');
        }
        // Vérifier si la colonne display_order existe avant de l'utiliser
        if ($this->db->field_exists('display_order', db_prefix() . $this->table_photos)) {
            $this->db->order_by('display_order', 'ASC');
        } else {
            // Sinon, trier par ID
            $this->db->order_by('id', 'ASC');
        }

        $photos = $this->db->get(db_prefix() . $this->table_photos)->result();

        // Mapper les noms de colonnes de la BDD vers les noms standard
        foreach ($photos as $photo) {
            if (isset($photo->file_path)) {
                $photo->photo_url = $photo->file_path;
            }
            if (isset($photo->is_primary)) {
                $photo->is_main = $photo->is_primary;
            }
        }

        return $photos;
    }

    /**
     * Get main photo for recipe
     */
    public function get_main_photo($recipe_id)
    {
        $this->db->where('recipe_id', $recipe_id);

        // La table utilise is_primary au lieu de is_main
        if ($this->db->field_exists('is_primary', db_prefix() . $this->table_photos)) {
            $this->db->where('is_primary', 1);
        }

        $this->db->order_by('id', 'ASC'); // Prendre la première photo
        $this->db->limit(1);
        $photo = $this->db->get(db_prefix() . $this->table_photos)->row();

        // Mapper les noms de colonnes de la BDD vers les noms standard
        if ($photo) {
            if (isset($photo->file_path)) {
                $photo->photo_url = $photo->file_path;
            }
            if (isset($photo->is_primary)) {
                $photo->is_main = $photo->is_primary;
            }
        }

        return $photo;
    }

    /**
     * Add photo
     *
     * @param int $recipe_id
     * @param string $photo_url
     * @param bool $is_main
     * @return int|bool
     */
    public function add_photo($recipe_id, $photo_url, $is_main = false)
    {
        // La table utilise file_path au lieu de photo_url
        $data = [
            'recipe_id' => $recipe_id,
            'file_path' => $photo_url,  // Note: column is 'file_path' not 'photo_url'
            'uploaded_at' => date('Y-m-d H:i:s')
        ];

        // La table utilise is_primary au lieu de is_main
        if ($this->db->field_exists('is_primary', db_prefix() . $this->table_photos)) {
            // Si c'est la photo principale, désactiver les autres
            if ($is_main) {
                $this->db->where('recipe_id', $recipe_id);
                $this->db->update(db_prefix() . $this->table_photos, ['is_primary' => 0]);
            }
            $data['is_primary'] = $is_main ? 1 : 0;
        }

        $this->db->insert(db_prefix() . $this->table_photos, $data);
        return $this->db->insert_id();
    }

    /**
     * Delete photo
     */
    public function delete_photo($photo_id)
    {
        $this->db->where('id', $photo_id);
        return $this->db->delete(db_prefix() . $this->table_photos);
    }

    /**
     * Delete all photos for recipe
     */
    private function delete_photos($recipe_id)
    {
        $this->db->where('recipe_id', $recipe_id);
        $this->db->delete(db_prefix() . $this->table_photos);
    }

    // =====================================
    // TAGS
    // =====================================

    /**
     * Get tags for recipe
     */
    public function get_tags($recipe_id)
    {
        $this->db->where('recipe_id', $recipe_id);
        $tags = $this->db->get(db_prefix() . $this->table_tags)->result();
        return array_column($tags, 'tag_name');
    }

    /**
     * Add tags
     */
    private function add_tags($recipe_id, $tags)
    {
        foreach ($tags as $tag) {
            if (!empty($tag)) {
                $data = [
                    'recipe_id' => $recipe_id,
                    'tag_name' => trim($tag)
                ];
                $this->db->insert(db_prefix() . $this->table_tags, $data);
            }
        }
    }

    /**
     * Delete tags
     */
    private function delete_tags($recipe_id)
    {
        $this->db->where('recipe_id', $recipe_id);
        $this->db->delete(db_prefix() . $this->table_tags);
    }

    /**
     * Get all unique tags
     */
    public function get_all_tags()
    {
        $this->db->select('tag_name, COUNT(*) as count');
        $this->db->from(db_prefix() . $this->table_tags);
        $this->db->group_by('tag_name');
        $this->db->order_by('count', 'DESC');
        return $this->db->get()->result();
    }

    // =====================================
    // ASSIGNMENTS
    // =====================================

    /**
     * Assign recipe to patient
     *
     * @param int $recipe_id
     * @param int $patient_id
     * @param int $dietitian_id
     * @param string $notes
     * @return int|bool
     */
    public function assign_to_patient($recipe_id, $patient_id, $dietitian_id = null, $notes = null)
    {
        if (!$dietitian_id) {
            $dietitian_id = get_staff_user_id();
        }

        // Vérifier si déjà assignée
        $this->db->where('recipe_id', $recipe_id);
        $this->db->where('patient_id', $patient_id);
        $existing = $this->db->get(db_prefix() . $this->table_assignments)->row();

        if ($existing) {
            return $existing->id;
        }

        $data = [
            'recipe_id' => $recipe_id,
            'patient_id' => $patient_id,
            'assigned_by_dietitian_id' => $dietitian_id,
            'assigned_at' => date('Y-m-d H:i:s')
        ];

        // Add notes if column exists
        if ($this->db->field_exists('notes', db_prefix() . $this->table_assignments)) {
            $data['notes'] = $notes;
        }

        $this->db->insert(db_prefix() . $this->table_assignments, $data);
        $assignment_id = $this->db->insert_id();

        if ($assignment_id) {
            log_activity('Recipe Assigned [Recipe ID: ' . $recipe_id . ', Patient ID: ' . $patient_id . ']');

            // Notify patient
            $recipe = $this->get($recipe_id);
            if ($recipe) {
                $this->load->model('dietetic/dietetic_notifications_model');

                // Get dietitian name
                $this->db->select('CONCAT(firstname, " ", lastname) as name');
                $this->db->where('staffid', $dietitian_id);
                $dietitian = $this->db->get(db_prefix() . 'staff')->row();
                $dietitian_name = $dietitian ? $dietitian->name : 'Votre diététicien';

                $this->dietetic_notifications_model->notify_recipe_assigned(
                    $patient_id,
                    $recipe->name,
                    $dietitian_name
                );
            }
        }

        return $assignment_id;
    }

    /**
     * Unassign recipe from patient
     */
    public function unassign_from_patient($recipe_id, $patient_id)
    {
        $this->db->where('recipe_id', $recipe_id);
        $this->db->where('patient_id', $patient_id);
        return $this->db->delete(db_prefix() . $this->table_assignments);
    }

    /**
     * Get patients assigned to a recipe
     */
    public function get_assigned_patients($recipe_id)
    {
        $this->db->select('p.*, c.company as client_name, ct.email, ra.assigned_at, ' .
            'CONCAT(s.firstname, " ", s.lastname) as assigned_by_name');
        $this->db->from(db_prefix() . $this->table_assignments . ' ra');
        $this->db->join(db_prefix() . 'dietic_patients p', 'p.id = ra.patient_id', 'inner');
        $this->db->join(db_prefix() . 'clients c', 'c.userid = p.client_id', 'left');
        $this->db->join(db_prefix() . 'contacts ct', 'ct.userid = c.userid AND ct.is_primary = 1', 'left');
        $this->db->join(db_prefix() . 'staff s', 's.staffid = ra.assigned_by_dietitian_id', 'left');
        $this->db->where('ra.recipe_id', $recipe_id);
        $this->db->order_by('ra.assigned_at', 'DESC');

        return $this->db->get()->result();
    }

    /**
     * Get recipes assigned to patient
     */
    public function get_patient_recipes($patient_id)
    {
        $this->db->select('r.*, ra.assigned_at, ' .
            'CONCAT(s.firstname, " ", s.lastname) as dietitian_name');
        $this->db->from(db_prefix() . $this->table_assignments . ' ra');
        $this->db->join(db_prefix() . $this->table . ' r', 'r.id = ra.recipe_id', 'inner');
        $this->db->join(db_prefix() . 'staff s', 's.staffid = ra.assigned_by_dietitian_id', 'left');
        $this->db->where('ra.patient_id', $patient_id);
        $this->db->where('r.status', 'approved');
        $this->db->order_by('ra.assigned_at', 'DESC');

        $recipes = $this->db->get()->result();

        foreach ($recipes as &$recipe) {
            $recipe->main_photo = $this->get_main_photo($recipe->id);
            $recipe->average_rating = $this->get_average_rating($recipe->id);
            $recipe->ratings_count = $this->get_ratings_count($recipe->id);
            $recipe->tags = $this->get_tags($recipe->id);
            $recipe->notes = null; // Column doesn't exist yet in database
        }

        return $recipes;
    }

    /**
     * Check if recipe is assigned to patient
     *
     * @param int $recipe_id
     * @param int $patient_id
     * @return bool
     */
    public function is_assigned_to_patient($recipe_id, $patient_id)
    {
        $this->db->where('recipe_id', $recipe_id);
        $this->db->where('patient_id', $patient_id);
        $count = $this->db->count_all_results(db_prefix() . $this->table_assignments);
        return $count > 0;
    }

    // =====================================
    // RATINGS
    // =====================================

    /**
     * Get average rating for recipe
     */
    public function get_average_rating($recipe_id)
    {
        $this->db->select_avg('rating');
        $this->db->where('recipe_id', $recipe_id);
        $result = $this->db->get(db_prefix() . $this->table_ratings)->row();
        return ($result && $result->rating !== null) ? round($result->rating, 1) : 0;
    }

    /**
     * Get ratings count
     */
    public function get_ratings_count($recipe_id)
    {
        $this->db->where('recipe_id', $recipe_id);
        return $this->db->count_all_results(db_prefix() . $this->table_ratings);
    }

    /**
     * Get ratings for recipe
     */
    public function get_ratings($recipe_id)
    {
        $this->db->select('rr.*, c.company as patient_name');
        $this->db->from(db_prefix() . $this->table_ratings . ' rr');
        $this->db->join(db_prefix() . 'dietic_patients dp', 'dp.id = rr.patient_id', 'left');
        $this->db->join(db_prefix() . 'clients c', 'c.userid = dp.client_id', 'left');
        $this->db->where('rr.recipe_id', $recipe_id);
        $this->db->order_by('rr.created_at', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Add or update rating
     *
     * @param int $recipe_id
     * @param int $patient_id
     * @param int $rating (1-5)
     * @param string $comment
     * @return bool
     */
    public function rate($recipe_id, $patient_id, $rating, $comment = null)
    {
        // Vérifier si le patient a déjà noté cette recette
        $this->db->where('recipe_id', $recipe_id);
        $this->db->where('patient_id', $patient_id);
        $existing = $this->db->get(db_prefix() . $this->table_ratings)->row();

        $data = [
            'rating' => max(1, min(5, $rating)), // Entre 1 et 5
            'comment' => $comment,
            'created_at' => date('Y-m-d H:i:s')
        ];

        if ($existing) {
            // Mettre à jour
            $this->db->where('id', $existing->id);
            return $this->db->update(db_prefix() . $this->table_ratings, $data);
        } else {
            // Ajouter
            $data['recipe_id'] = $recipe_id;
            $data['patient_id'] = $patient_id;
            return $this->db->insert(db_prefix() . $this->table_ratings, $data);
        }
    }

    /**
     * Delete ratings
     */
    private function delete_ratings($recipe_id)
    {
        $this->db->where('recipe_id', $recipe_id);
        $this->db->delete(db_prefix() . $this->table_ratings);
    }

    // =====================================
    // FAVORITES
    // =====================================

    /**
     * Add to favorites
     */
    public function add_to_favorites($recipe_id, $patient_id)
    {
        // Vérifier si déjà en favori
        $this->db->where('recipe_id', $recipe_id);
        $this->db->where('patient_id', $patient_id);
        $existing = $this->db->get(db_prefix() . $this->table_favorites)->row();

        if ($existing) {
            return true;
        }

        $data = [
            'recipe_id' => $recipe_id,
            'patient_id' => $patient_id
        ];

        return $this->db->insert(db_prefix() . $this->table_favorites, $data);
    }

    /**
     * Remove from favorites
     */
    public function remove_from_favorites($recipe_id, $patient_id)
    {
        $this->db->where('recipe_id', $recipe_id);
        $this->db->where('patient_id', $patient_id);
        return $this->db->delete(db_prefix() . $this->table_favorites);
    }

    /**
     * Check if recipe is favorite for patient
     */
    public function is_favorite($recipe_id, $patient_id)
    {
        $this->db->where('recipe_id', $recipe_id);
        $this->db->where('patient_id', $patient_id);
        $count = $this->db->count_all_results(db_prefix() . $this->table_favorites);
        return $count > 0;
    }

    /**
     * Get favorite recipes for patient
     */
    public function get_favorites($patient_id)
    {
        $this->db->select('r.*');
        $this->db->from(db_prefix() . $this->table_favorites . ' rf');
        $this->db->join(db_prefix() . $this->table . ' r', 'r.id = rf.recipe_id', 'inner');
        $this->db->where('rf.patient_id', $patient_id);
        $this->db->where('r.status', 'approved');
        $this->db->order_by('r.created_at', 'DESC');

        $recipes = $this->db->get()->result();

        foreach ($recipes as &$recipe) {
            $recipe->main_photo = $this->get_main_photo($recipe->id);
            $recipe->average_rating = $this->get_average_rating($recipe->id);
            $recipe->ratings_count = $this->get_ratings_count($recipe->id);
            $recipe->tags = $this->get_tags($recipe->id);
        }

        return $recipes;
    }

    /**
     * Delete favorites
     */
    private function delete_favorites($recipe_id)
    {
        $this->db->where('recipe_id', $recipe_id);
        $this->db->delete(db_prefix() . $this->table_favorites);
    }

    // =====================================
    // STATISTICS
    // =====================================

    /**
     * Get statistics
     */
    public function get_statistics()
    {
        $stats = new stdClass();

        // Total recipes
        $stats->total = $this->db->count_all(db_prefix() . $this->table);

        // Pending approval
        $this->db->where('status', 'pending');
        $stats->pending = $this->db->count_all_results(db_prefix() . $this->table);

        // Approved
        $this->db->where('status', 'approved');
        $stats->approved = $this->db->count_all_results(db_prefix() . $this->table);

        // Rejected
        $this->db->where('status', 'rejected');
        $stats->rejected = $this->db->count_all_results(db_prefix() . $this->table);

        return $stats;
    }
}
