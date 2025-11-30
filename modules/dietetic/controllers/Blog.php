<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Blog extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('dietetic/dietetic_blog_model');
        $this->load->helper('dietetic/dietetic');
    }

    /**
     * List all blog articles
     */
    public function index()
    {
        if (!has_permission('dietetic', '', 'view') && !is_admin()) {
            access_denied('Blog');
        }

        $data['title'] = _l('blog_articles');
        $data['articles'] = $this->dietetic_blog_model->get_all();
        $data['categories'] = $this->dietetic_blog_model->get_all_categories();

        $this->load->view('admin/blog/manage', $data);
    }

    /**
     * Add new article
     */
    public function create()
    {
        if (!has_permission('dietetic', '', 'create') && !is_admin()) {
            access_denied('Blog');
        }

        if ($this->input->post()) {
            $data = [
                'title' => $this->input->post('title', true),
                'slug' => $this->input->post('slug', true),
                'excerpt' => $this->input->post('excerpt', true),
                'content' => $this->input->post('content', false), // Allow HTML
                'category' => $this->input->post('category', true),
                'tags' => $this->input->post('tags', true),
                'status' => $this->input->post('status', true),
                'published_at' => $this->input->post('published_at', true)
            ];

            // Handle featured image upload
            if (!empty($_FILES['featured_image']['name'])) {
                $data['featured_image'] = $this->handle_image_upload();
            }

            $article_id = $this->dietetic_blog_model->add($data);

            if ($article_id) {
                set_alert('success', _l('blog_article_added_successfully'));
                redirect(admin_url('dietetic/blog/edit/' . $article_id));
            } else {
                set_alert('danger', _l('blog_article_add_failed'));
            }
        }

        $data['title'] = _l('new_blog_article');
        $data['categories'] = $this->dietetic_blog_model->get_all_categories();

        $this->load->view('admin/blog/form', $data);
    }

    /**
     * Edit existing article
     */
    public function edit($id)
    {
        if (!has_permission('dietetic', '', 'edit') && !is_admin()) {
            access_denied('Blog');
        }

        $data['article'] = $this->dietetic_blog_model->get($id);

        if (!$data['article']) {
            show_404();
        }

        if ($this->input->post()) {
            $update_data = [
                'title' => $this->input->post('title', true),
                'slug' => $this->input->post('slug', true),
                'excerpt' => $this->input->post('excerpt', true),
                'content' => $this->input->post('content', false),
                'category' => $this->input->post('category', true),
                'tags' => $this->input->post('tags', true),
                'status' => $this->input->post('status', true),
                'published_at' => $this->input->post('published_at', true)
            ];

            // Handle featured image upload
            if (!empty($_FILES['featured_image']['name'])) {
                // Delete old image
                if ($data['article']->featured_image) {
                    $old_image = FCPATH . 'uploads/blog/' . $data['article']->featured_image;
                    if (file_exists($old_image)) {
                        @unlink($old_image);
                    }
                }
                $update_data['featured_image'] = $this->handle_image_upload();
            }

            if ($this->dietetic_blog_model->update($id, $update_data)) {
                set_alert('success', _l('blog_article_updated_successfully'));
            } else {
                set_alert('warning', _l('blog_article_no_changes'));
            }

            redirect(admin_url('dietetic/blog/edit/' . $id));
        }

        $data['title'] = _l('edit_blog_article');
        $data['categories'] = $this->dietetic_blog_model->get_all_categories();

        $this->load->view('admin/blog/form', $data);
    }

    /**
     * Delete article
     */
    public function delete($id)
    {
        if (!has_permission('dietetic', '', 'delete') && !is_admin()) {
            access_denied('Blog');
        }

        if ($this->dietetic_blog_model->delete($id)) {
            set_alert('success', _l('blog_article_deleted_successfully'));
        } else {
            set_alert('danger', _l('blog_article_delete_failed'));
        }

        redirect(admin_url('dietetic/blog'));
    }

    /**
     * Change article status (draft/published/archived)
     */
    public function change_status($id)
    {
        if (!has_permission('dietetic', '', 'edit') && !is_admin()) {
            ajax_access_denied();
        }

        $status = $this->input->post('status');

        $data = ['status' => $status];

        if ($status == 'published') {
            $article = $this->dietetic_blog_model->get($id);
            if (!$article->published_at) {
                $data['published_at'] = date('Y-m-d H:i:s');
            }
        }

        if ($this->dietetic_blog_model->update($id, $data)) {
            echo json_encode(['success' => true, 'message' => _l('blog_status_changed')]);
        } else {
            echo json_encode(['success' => false, 'message' => _l('something_went_wrong')]);
        }
    }

    /**
     * Delete featured image
     */
    public function delete_image($id)
    {
        if (!has_permission('dietetic', '', 'edit') && !is_admin()) {
            ajax_access_denied();
        }

        $article = $this->dietetic_blog_model->get($id);

        if ($article && $article->featured_image) {
            $image_path = FCPATH . 'uploads/blog/' . $article->featured_image;
            if (file_exists($image_path)) {
                @unlink($image_path);
            }

            $this->dietetic_blog_model->update($id, ['featured_image' => null]);

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }

    /**
     * Handle image upload
     * @return string|bool Filename on success, false on failure
     */
    private function handle_image_upload()
    {
        // Store in main Perfex uploads folder to preserve images during module updates
        $upload_path = FCPATH . 'uploads/blog';

        // Create directory if it doesn't exist
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0755, true);
        }

        $config['upload_path'] = $upload_path;
        $config['allowed_types'] = 'gif|jpg|jpeg|png|webp';
        $config['max_size'] = 5120; // 5MB
        $config['encrypt_name'] = true;

        $this->load->library('upload', $config);

        if ($this->upload->do_upload('featured_image')) {
            $upload_data = $this->upload->data();
            return $upload_data['file_name'];
        }

        return false;
    }

    // ==================== CATEGORIES ====================

    /**
     * Manage categories
     */
    public function categories()
    {
        if (!is_admin()) {
            access_denied('Blog Categories');
        }

        if ($this->input->post()) {
            $action = $this->input->post('action');

            if ($action == 'add') {
                $data = [
                    'name' => $this->input->post('name', true),
                    'slug' => $this->input->post('slug', true),
                    'description' => $this->input->post('description', true),
                    'color' => $this->input->post('color', true),
                    'icon' => $this->input->post('icon', true),
                    'order' => $this->input->post('order', true)
                ];

                if ($this->dietetic_blog_model->add_category($data)) {
                    set_alert('success', _l('blog_category_added'));
                } else {
                    set_alert('danger', _l('blog_category_add_failed'));
                }
            } elseif ($action == 'edit') {
                $id = $this->input->post('category_id');
                $data = [
                    'name' => $this->input->post('name', true),
                    'slug' => $this->input->post('slug', true),
                    'description' => $this->input->post('description', true),
                    'color' => $this->input->post('color', true),
                    'icon' => $this->input->post('icon', true),
                    'order' => $this->input->post('order', true)
                ];

                if ($this->dietetic_blog_model->update_category($id, $data)) {
                    set_alert('success', _l('blog_category_updated'));
                } else {
                    set_alert('warning', _l('blog_category_no_changes'));
                }
            }

            redirect(admin_url('dietetic/blog/categories'));
        }

        $data['title'] = _l('blog_categories');
        $data['categories'] = $this->dietetic_blog_model->get_all_categories();

        $this->load->view('admin/blog/categories', $data);
    }

    /**
     * Delete category
     */
    public function delete_category($id)
    {
        if (!is_admin()) {
            access_denied('Blog Categories');
        }

        if ($this->dietetic_blog_model->delete_category($id)) {
            set_alert('success', _l('blog_category_deleted'));
        } else {
            set_alert('danger', _l('blog_category_delete_failed_has_articles'));
        }

        redirect(admin_url('dietetic/blog/categories'));
    }
}
