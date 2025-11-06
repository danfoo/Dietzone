<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Foods extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('dietetic/dietetic_foods_model');
        $this->load->helper('dietetic/dietetic');

        if (!dietetic_has_permission('view')) {
            access_denied('dietetic');
        }
    }

    /**
     * List all foods
     */
    public function index()
    {
        $data['title'] = _l('dietetic_foods');
        $data['foods'] = $this->dietetic_foods_model->get_all();
        $data['total_count'] = $this->dietetic_foods_model->get_total_count();

        $this->load->view('admin/foods/list', $data);
    }

    /**
     * View food detail
     *
     * @param int $id
     */
    public function view($id)
    {
        $data['food'] = $this->dietetic_foods_model->get($id);

        if (!$data['food']) {
            show_404();
        }

        $data['title'] = $data['food']->food_name;

        $this->load->view('admin/foods/view', $data);
    }

    /**
     * Create new food
     * Any staff with view permission can create foods (dietitians need this to build meal plans)
     */
    public function create()
    {
        // View permission is already checked in constructor
        // No additional permission check needed - all dietitians can create foods

        if ($this->input->post()) {
            $data = $this->input->post();

            $food_id = $this->dietetic_foods_model->add($data);

            if ($food_id) {
                set_alert('success', _l('added_successfully'));
                redirect(admin_url('dietetic/foods'));
            } else {
                set_alert('danger', _l('dietetic_error_add_failed'));
            }
        }

        $data['title'] = _l('dietetic_new_food');

        $this->load->view('admin/foods/form', $data);
    }

    /**
     * Edit food
     * Any staff with view permission can edit foods (dietitians need to update nutrition info)
     *
     * @param int $id
     */
    public function edit($id)
    {
        // View permission is already checked in constructor
        // No additional permission check needed - all dietitians can edit foods

        $data['food'] = $this->dietetic_foods_model->get($id);

        if (!$data['food']) {
            show_404();
        }

        if ($this->input->post()) {
            $update_data = $this->input->post();

            if ($this->dietetic_foods_model->update($id, $update_data)) {
                set_alert('success', _l('updated_successfully'));
                redirect(admin_url('dietetic/foods'));
            } else {
                set_alert('danger', _l('dietetic_error_update_failed'));
            }
        }

        $data['title'] = _l('dietetic_edit_food');

        $this->load->view('admin/foods/form', $data);
    }

    /**
     * Delete food
     * Only admins can delete foods to prevent accidental data loss
     *
     * @param int $id
     */
    public function delete($id)
    {
        if (!is_admin()) {
            ajax_access_denied();
        }

        if ($this->dietetic_foods_model->delete($id)) {
            echo json_encode(['success' => true, 'message' => _l('deleted')]);
        } else {
            echo json_encode(['success' => false, 'message' => _l('dietetic_error_delete_failed')]);
        }
    }

    /**
     * Search foods via AJAX
     */
    public function search()
    {
        $search = $this->input->get('q');

        if (empty($search)) {
            echo json_encode([]);
            return;
        }

        $results = $this->dietetic_foods_model->search($search);

        $formatted = [];
        foreach ($results as $food) {
            // Show French name if available, otherwise English name
            $display_name = !empty($food->food_name_fr) ? $food->food_name_fr : $food->food_name;

            $formatted[] = [
                'id'   => $food->id,
                'text' => $display_name . ' (' . $food->calories . ' kcal/' . $food->serving_size . $food->serving_unit . ')',
                'data' => $food,
            ];
        }

        echo json_encode($formatted);
    }

    /**
     * Get food nutrition data via AJAX
     *
     * @param int $id
     */
    public function get_nutrition($id)
    {
        $food = $this->dietetic_foods_model->get($id);

        if (!$food) {
            echo json_encode(['success' => false]);
            return;
        }

        $quantity = $this->input->get('quantity');
        $unit = $this->input->get('unit');

        if ($quantity && $unit) {
            $nutrition = $this->dietetic_foods_model->calculate_nutrition($id, $quantity, $unit);
            echo json_encode(['success' => true, 'food' => $food, 'nutrition' => $nutrition]);
        } else {
            echo json_encode(['success' => true, 'food' => $food]);
        }
    }

    /**
     * Import foods from CSV
     */
    public function import()
    {
        if (!dietetic_has_permission('create')) {
            access_denied('dietetic');
        }

        if ($this->input->post() && isset($_FILES['csv_file'])) {
            $file = $_FILES['csv_file'];

            if ($file['error'] == 0) {
                $result = $this->dietetic_foods_model->import_from_csv($file['tmp_name']);

                if ($result['success'] > 0) {
                    set_alert('success', sprintf(_l('dietetic_import_success'), $result['success']));
                } else {
                    set_alert('warning', _l('dietetic_import_no_records'));
                }

                if (!empty($result['errors'])) {
                    set_alert('warning', implode('<br>', $result['errors']));
                }
            } else {
                set_alert('danger', _l('dietetic_import_error'));
            }

            redirect(admin_url('dietetic/foods'));
        }

        $data['title'] = _l('dietetic_import_foods');

        $this->load->view('admin/foods/import', $data);
    }

    /**
     * Export foods to CSV
     */
    public function export()
    {
        if (!dietetic_has_permission('view')) {
            access_denied('dietetic');
        }

        $filename = 'dietetic_foods_' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // Headers
        fputcsv($output, [
            'ID',
            'Food Name',
            'Food Name FR',
            'Category',
            'Serving Size',
            'Serving Unit',
            'Calories',
            'Protein (g)',
            'Carbs (g)',
            'Fats (g)',
            'Fiber (g)',
            'Sugar (g)',
            'Sodium (mg)',
            'Allergens',
            'Active',
        ]);

        // Data
        $foods = $this->dietetic_foods_model->get_all();

        foreach ($foods as $food) {
            fputcsv($output, [
                $food->id,
                $food->food_name,
                $food->food_name_fr,
                $food->category,
                $food->serving_size,
                $food->serving_unit,
                $food->calories,
                $food->protein,
                $food->carbs,
                $food->fats,
                $food->fiber,
                $food->sugar,
                $food->sodium,
                $food->allergens,
                $food->is_active,
            ]);
        }

        fclose($output);
        exit;
    }

    /**
     * Bulk delete foods
     */
    public function bulk_delete()
    {
        if (!dietetic_has_permission('delete')) {
            ajax_access_denied();
        }

        $ids = $this->input->post('ids');

        if (empty($ids) || !is_array($ids)) {
            echo json_encode(['success' => false, 'message' => 'Aucun aliment sélectionné']);
            return;
        }

        $deleted_count = 0;
        foreach ($ids as $id) {
            if ($this->dietetic_foods_model->delete($id)) {
                $deleted_count++;
            }
        }

        if ($deleted_count > 0) {
            echo json_encode([
                'success' => true,
                'message' => sprintf('%d aliment(s) supprimé(s) avec succès', $deleted_count)
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Erreur lors de la suppression'
            ]);
        }
    }

    /**
     * Remove duplicate food entries (keep the oldest one)
     * Can be called manually by admin to clean up duplicates
     */
    public function cleanup_duplicates()
    {
        if (!is_admin()) {
            access_denied('dietetic');
        }

        $removed_count = $this->dietetic_foods_model->remove_duplicates();

        if ($removed_count > 0) {
            set_alert('success', sprintf('Successfully removed %d duplicate food entries.', $removed_count));
        } else {
            set_alert('info', 'No duplicate food entries found.');
        }

        redirect(admin_url('dietetic/foods'));
    }
}
