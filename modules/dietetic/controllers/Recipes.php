<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Recipes Controller
 * Gestion de la bibliothèque de recettes (Admin)
 */
class Recipes extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        // Load models
        $this->load->model('dietetic/dietetic_recipes_model');
        $this->load->model('dietetic/dietetic_patients_model');
        $this->load->helper('dietetic/dietetic');

        // Check permissions
        if (!dietetic_has_permission('view')) {
            access_denied('dietetic');
        }
    }

    /**
     * List all recipes
     */
    public function index()
    {
        $status = $this->input->get('status') ?: 'all';

        $data['title'] = 'Bibliothèque de Recettes';
        $data['recipes'] = $this->dietetic_recipes_model->get_all([], $status);
        $data['current_status'] = $status;
        $data['stats'] = $this->dietetic_recipes_model->get_statistics();

        $this->load->view('admin/recipes/list', $data);
    }

    /**
     * View recipe detail
     *
     * @param int $id
     */
    public function view($id)
    {
        $data['recipe'] = $this->dietetic_recipes_model->get($id);

        if (!$data['recipe']) {
            show_404();
        }

        $data['title'] = $data['recipe']->name;
        $data['ratings'] = $this->dietetic_recipes_model->get_ratings($id);

        $this->load->view('admin/recipes/view', $data);
    }

    /**
     * Create new recipe
     */
    public function create()
    {
        if (!dietetic_has_permission('create')) {
            access_denied('dietetic');
        }

        if ($this->input->post()) {
            $post_data = $this->input->post();

            // Préparer les données
            $data = [
                'name' => $post_data['name'],
                'description' => $post_data['description'],
                'preparation_time' => $post_data['preparation_time'],
                'category' => $post_data['category'],
                'ingredients' => [],
                'instructions' => [],
                'nutrition' => [],
                'tags' => []
            ];

            // Traiter les ingrédients depuis la bibliothèque alimentaire
            if (!empty($post_data['food_id'])) {
                $this->load->model('dietetic/dietetic_foods_model');
                foreach ($post_data['food_id'] as $i => $food_id) {
                    if (!empty($food_id)) {
                        $food = $this->dietetic_foods_model->get($food_id);
                        $data['ingredients'][] = [
                            'name' => $food ? $food->name : '',
                            'food_id' => $food_id,
                            'quantity' => $post_data['ingredient_quantity'][$i] ?? null,
                            'unit' => $post_data['ingredient_unit'][$i] ?? 'g'
                        ];
                    }
                }
            }

            // Traiter les instructions
            if (!empty($post_data['instruction'])) {
                foreach ($post_data['instruction'] as $instruction) {
                    if (!empty($instruction)) {
                        $data['instructions'][] = $instruction;
                    }
                }
            }

            // Traiter les informations nutritionnelles
            if (!empty($post_data['calories']) || !empty($post_data['protein'])) {
                $data['nutrition'] = [
                    'calories' => $post_data['calories'] ?? null,
                    'protein' => $post_data['protein'] ?? null,
                    'carbs' => $post_data['carbs'] ?? null,
                    'fat' => $post_data['fat'] ?? null,
                    'fiber' => $post_data['fiber'] ?? null,
                    'sodium' => $post_data['sodium'] ?? null,
                    'sugar' => $post_data['sugar'] ?? null
                ];
            }

            // Traiter les tags
            if (!empty($post_data['tags'])) {
                $data['tags'] = is_array($post_data['tags'])
                    ? $post_data['tags']
                    : explode(',', $post_data['tags']);
            }

            $recipe_id = $this->dietetic_recipes_model->add($data);

            if ($recipe_id) {
                // Upload de photos si présentes
                if (!empty($_FILES['photos']['name'][0])) {
                    $this->handle_photo_upload($recipe_id);
                }

                set_alert('success', 'Recette créée avec succès. En attente d\'approbation.');
                redirect(admin_url('dietetic/recipes/view/' . $recipe_id));
            } else {
                set_alert('danger', 'Erreur lors de la création de la recette');
            }
        }

        $data['title'] = 'Nouvelle Recette';
        $data['all_tags'] = $this->dietetic_recipes_model->get_all_tags();

        // Load foods from database
        $this->load->model('dietetic/dietetic_foods_model');
        $data['foods'] = $this->dietetic_foods_model->get_active();

        $this->load->view('admin/recipes/form', $data);
    }

    /**
     * Edit recipe
     *
     * @param int $id
     */
    public function edit($id)
    {
        if (!dietetic_has_permission('edit')) {
            access_denied('dietetic');
        }

        $data['recipe'] = $this->dietetic_recipes_model->get($id);

        if (!$data['recipe']) {
            show_404();
        }

        if ($this->input->post()) {
            $post_data = $this->input->post();

            // Préparer les données
            $update_data = [
                'name' => $post_data['name'],
                'description' => $post_data['description'],
                'preparation_time' => $post_data['preparation_time'],
                'category' => $post_data['category'],
                'ingredients' => [],
                'instructions' => [],
                'nutrition' => [],
                'tags' => []
            ];

            // Traiter les ingrédients depuis la bibliothèque alimentaire
            if (!empty($post_data['food_id'])) {
                $this->load->model('dietetic/dietetic_foods_model');
                foreach ($post_data['food_id'] as $i => $food_id) {
                    if (!empty($food_id)) {
                        $food = $this->dietetic_foods_model->get($food_id);
                        $update_data['ingredients'][] = [
                            'name' => $food ? $food->name : '',
                            'food_id' => $food_id,
                            'quantity' => $post_data['ingredient_quantity'][$i] ?? null,
                            'unit' => $post_data['ingredient_unit'][$i] ?? 'g'
                        ];
                    }
                }
            }

            // Traiter les instructions
            if (!empty($post_data['instruction'])) {
                foreach ($post_data['instruction'] as $instruction) {
                    if (!empty($instruction)) {
                        $update_data['instructions'][] = $instruction;
                    }
                }
            }

            // Traiter les informations nutritionnelles
            if (!empty($post_data['calories']) || !empty($post_data['protein'])) {
                $update_data['nutrition'] = [
                    'calories' => $post_data['calories'] ?? null,
                    'protein' => $post_data['protein'] ?? null,
                    'carbs' => $post_data['carbs'] ?? null,
                    'fat' => $post_data['fat'] ?? null,
                    'fiber' => $post_data['fiber'] ?? null,
                    'sodium' => $post_data['sodium'] ?? null,
                    'sugar' => $post_data['sugar'] ?? null
                ];
            }

            // Traiter les tags
            if (!empty($post_data['tags'])) {
                $update_data['tags'] = is_array($post_data['tags'])
                    ? $post_data['tags']
                    : explode(',', $post_data['tags']);
            }

            if ($this->dietetic_recipes_model->update($id, $update_data)) {
                // Upload de nouvelles photos si présentes
                if (!empty($_FILES['photos']['name'][0])) {
                    $this->handle_photo_upload($id);
                }

                set_alert('success', 'Recette mise à jour avec succès');
                redirect(admin_url('dietetic/recipes/view/' . $id));
            } else {
                set_alert('danger', 'Erreur lors de la mise à jour');
            }
        }

        $data['title'] = 'Modifier Recette';
        $data['all_tags'] = $this->dietetic_recipes_model->get_all_tags();

        // Load foods from database
        $this->load->model('dietetic/dietetic_foods_model');
        $data['foods'] = $this->dietetic_foods_model->get_active();

        $this->load->view('admin/recipes/form', $data);
    }

    /**
     * Delete recipe
     *
     * @param int $id
     */
    public function delete($id)
    {
        if (!dietetic_has_permission('delete')) {
            ajax_access_denied();
        }

        if ($this->dietetic_recipes_model->delete($id)) {
            set_alert('success', 'Recette supprimée avec succès');
        } else {
            set_alert('warning', 'La recette a été archivée car elle est assignée à des patients');
        }

        redirect(admin_url('dietetic/recipes'));
    }

    /**
     * Approve recipe
     *
     * @param int $id
     */
    public function approve($id)
    {
        if (!is_admin()) {
            access_denied('dietetic');
        }

        if ($this->dietetic_recipes_model->approve($id)) {
            set_alert('success', 'Recette approuvée avec succès');
        } else {
            set_alert('danger', 'Erreur lors de l\'approbation');
        }

        redirect(admin_url('dietetic/recipes/view/' . $id));
    }

    /**
     * Reject recipe
     *
     * @param int $id
     */
    public function reject($id)
    {
        if (!is_admin()) {
            access_denied('dietetic');
        }

        $reason = $this->input->post('rejection_reason');

        if (empty($reason)) {
            set_alert('danger', 'Veuillez fournir une raison de rejet');
            redirect(admin_url('dietetic/recipes/view/' . $id));
            return;
        }

        if ($this->dietetic_recipes_model->reject($id, $reason)) {
            set_alert('success', 'Recette rejetée');
        } else {
            set_alert('danger', 'Erreur lors du rejet');
        }

        redirect(admin_url('dietetic/recipes/view/' . $id));
    }

    /**
     * Assign recipe to patient
     */
    public function assign()
    {
        if (!dietetic_has_permission('edit')) {
            ajax_access_denied();
        }

        $recipe_id = $this->input->post('recipe_id');
        $patient_id = $this->input->post('patient_id');
        $notes = $this->input->post('notes');

        if (empty($recipe_id) || empty($patient_id)) {
            echo json_encode(['success' => false, 'message' => 'Données manquantes']);
            return;
        }

        $assignment_id = $this->dietetic_recipes_model->assign_to_patient($recipe_id, $patient_id, null, $notes);

        if ($assignment_id) {
            echo json_encode(['success' => true, 'message' => 'Recette assignée au patient']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'assignation']);
        }
    }

    /**
     * Unassign recipe from patient
     */
    public function unassign()
    {
        if (!dietetic_has_permission('edit')) {
            ajax_access_denied();
        }

        $recipe_id = $this->input->post('recipe_id');
        $patient_id = $this->input->post('patient_id');

        if (empty($recipe_id) || empty($patient_id)) {
            echo json_encode(['success' => false, 'message' => 'Données manquantes']);
            return;
        }

        if ($this->dietetic_recipes_model->unassign_from_patient($recipe_id, $patient_id)) {
            echo json_encode(['success' => true, 'message' => 'Assignation supprimée']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression']);
        }
    }

    /**
     * Delete photo
     *
     * @param int $photo_id
     */
    public function delete_photo($photo_id)
    {
        if (!dietetic_has_permission('edit')) {
            ajax_access_denied();
        }

        if ($this->dietetic_recipes_model->delete_photo($photo_id)) {
            echo json_encode(['success' => true, 'message' => 'Photo supprimée']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression']);
        }
    }

    /**
     * Handle photo upload
     *
     * @param int $recipe_id
     * @return bool
     */
    private function handle_photo_upload($recipe_id)
    {
        // Configuration de l'upload
        $upload_path = FCPATH . 'uploads/dietetic/recipes/';

        // Créer le dossier s'il n'existe pas
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0755, true);
        }

        $config['upload_path'] = $upload_path;
        $config['allowed_types'] = 'jpg|jpeg|png|gif';
        $config['max_size'] = 5120; // 5MB
        $config['encrypt_name'] = true;

        $this->load->library('upload', $config);

        $uploaded_count = 0;

        // Upload multiple files
        $files = $_FILES['photos'];
        $file_count = count($files['name']);

        for ($i = 0; $i < $file_count; $i++) {
            if (empty($files['name'][$i])) {
                continue;
            }

            $_FILES['photo']['name'] = $files['name'][$i];
            $_FILES['photo']['type'] = $files['type'][$i];
            $_FILES['photo']['tmp_name'] = $files['tmp_name'][$i];
            $_FILES['photo']['error'] = $files['error'][$i];
            $_FILES['photo']['size'] = $files['size'][$i];

            $this->upload->initialize($config);

            if ($this->upload->do_upload('photo')) {
                $upload_data = $this->upload->data();
                $photo_url = 'uploads/dietetic/recipes/' . $upload_data['file_name'];

                // La première photo est la photo principale
                $is_main = ($i === 0 && $uploaded_count === 0);

                $this->dietetic_recipes_model->add_photo($recipe_id, $photo_url, $is_main);
                $uploaded_count++;
            } else {
                log_activity('Recipe photo upload error: ' . $this->upload->display_errors());
            }
        }

        return $uploaded_count > 0;
    }

    /**
     * Get patients for assignment (AJAX)
     */
    public function get_patients()
    {
        if (!dietetic_has_permission('view')) {
            ajax_access_denied();
        }

        $patients = $this->dietetic_patients_model->get_all();

        $result = [];
        foreach ($patients as $patient) {
            $result[] = [
                'id' => $patient->id,
                'name' => $patient->client_name,
                'email' => $patient->email ?? ''
            ];
        }

        echo json_encode($result);
    }
}
