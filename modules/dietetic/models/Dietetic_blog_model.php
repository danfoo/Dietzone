<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dietetic_blog_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get single article by ID or slug
     * @param mixed $id Article ID or slug
     * @return object|null
     */
    public function get($id)
    {
        // Check if searching by slug or ID
        if (is_numeric($id)) {
            $this->db->where('a.id', $id);
        } else {
            $this->db->where('a.slug', $id);
        }

        $this->db->select('a.*, s.firstname, s.lastname, CONCAT(s.firstname, " ", s.lastname) as author_name');
        $this->db->from(db_prefix() . 'dietic_blog_articles a');
        $this->db->join(db_prefix() . 'staff s', 's.staffid = a.author_id', 'left');

        $article = $this->db->get()->row();

        if ($article) {
            // Get category details
            if ($article->category) {
                $article->category_details = $this->get_category($article->category);
            }

            // Convert tags from comma-separated to array
            if ($article->tags) {
                $article->tags_array = explode(',', $article->tags);
                $article->tags_array = array_map('trim', $article->tags_array);
            } else {
                $article->tags_array = [];
            }
        }

        return $article;
    }

    /**
     * Get all articles with optional filtering
     * @param array $where Filter conditions
     * @param int $limit Limit results
     * @param int $offset Offset for pagination
     * @return array
     */
    public function get_all($where = [], $limit = null, $offset = 0)
    {
        $this->db->select('a.*, s.firstname, s.lastname, CONCAT(s.firstname, " ", s.lastname) as author_name');
        $this->db->from(db_prefix() . 'dietic_blog_articles a');
        $this->db->join(db_prefix() . 'staff s', 's.staffid = a.author_id', 'left');

        if (!empty($where)) {
            $this->db->where($where);
        }

        $this->db->order_by('a.published_at', 'DESC');
        $this->db->order_by('a.created_at', 'DESC');

        if ($limit !== null) {
            $this->db->limit($limit, $offset);
        }

        $articles = $this->db->get()->result();

        // Enrich each article with category details
        foreach ($articles as $article) {
            if ($article->category) {
                $article->category_details = $this->get_category($article->category);
            }

            // Convert tags
            if ($article->tags) {
                $article->tags_array = explode(',', $article->tags);
                $article->tags_array = array_map('trim', $article->tags_array);
            } else {
                $article->tags_array = [];
            }
        }

        return $articles;
    }

    /**
     * Get published articles for portal
     * @param int $limit
     * @param int $offset
     * @param string $category Optional category filter
     * @return array
     */
    public function get_published($limit = null, $offset = 0, $category = null)
    {
        $where = [
            'a.status' => 'published',
            'a.published_at <=' => date('Y-m-d H:i:s')
        ];

        if ($category) {
            $where['a.category'] = $category;
        }

        return $this->get_all($where, $limit, $offset);
    }

    /**
     * Get total count of articles
     * @param array $where
     * @return int
     */
    public function get_count($where = [])
    {
        if (!empty($where)) {
            $this->db->where($where);
        }

        return $this->db->count_all_results(db_prefix() . 'dietic_blog_articles');
    }

    /**
     * Get featured articles
     * @param int $limit
     * @return array
     */
    public function get_featured($limit = 3)
    {
        // Get most viewed published articles
        $where = [
            'a.status' => 'published',
            'a.published_at <=' => date('Y-m-d H:i:s')
        ];

        $this->db->select('a.*, s.firstname, s.lastname, CONCAT(s.firstname, " ", s.lastname) as author_name');
        $this->db->from(db_prefix() . 'dietic_blog_articles a');
        $this->db->join(db_prefix() . 'staff s', 's.staffid = a.author_id', 'left');
        $this->db->where($where);
        $this->db->order_by('a.views_count', 'DESC');
        $this->db->order_by('a.published_at', 'DESC');
        $this->db->limit($limit);

        return $this->db->get()->result();
    }

    /**
     * Add new article
     * @param array $data
     * @return int|bool Article ID on success, false on failure
     */
    public function add($data)
    {
        // Generate slug from title if not provided
        if (empty($data['slug']) && !empty($data['title'])) {
            $data['slug'] = $this->generate_slug($data['title']);
        }

        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        // Set author to current staff if not provided
        if (empty($data['author_id'])) {
            $data['author_id'] = get_staff_user_id();
        }

        // Set published_at if status is published and published_at is empty
        if ($data['status'] == 'published' && empty($data['published_at'])) {
            $data['published_at'] = date('Y-m-d H:i:s');
        }

        $this->db->insert(db_prefix() . 'dietic_blog_articles', $data);

        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            log_activity('New Blog Article Created [ID: ' . $insert_id . ', Title: ' . $data['title'] . ']');
            return $insert_id;
        }

        return false;
    }

    /**
     * Update article
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        // If status changed to published and published_at is empty, set it
        if (isset($data['status']) && $data['status'] == 'published') {
            $article = $this->get($id);
            if (!$article->published_at) {
                $data['published_at'] = date('Y-m-d H:i:s');
            }
        }

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'dietic_blog_articles', $data);

        if ($this->db->affected_rows() > 0) {
            log_activity('Blog Article Updated [ID: ' . $id . ']');
            return true;
        }

        return false;
    }

    /**
     * Delete article
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $article = $this->get($id);

        if (!$article) {
            return false;
        }

        // Delete featured image if exists
        if ($article->featured_image) {
            $image_path = module_dir_path(DIETETIC_MODULE_NAME, 'uploads/blog/' . $article->featured_image);
            if (file_exists($image_path)) {
                @unlink($image_path);
            }
        }

        // Delete article
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'dietic_blog_articles');

        if ($this->db->affected_rows() > 0) {
            // Delete related comments
            $this->db->where('article_id', $id);
            $this->db->delete(db_prefix() . 'dietic_blog_comments');

            // Delete related views
            $this->db->where('article_id', $id);
            $this->db->delete(db_prefix() . 'dietic_blog_article_views');

            log_activity('Blog Article Deleted [ID: ' . $id . ', Title: ' . $article->title . ']');
            return true;
        }

        return false;
    }

    /**
     * Increment article views
     * @param int $id Article ID
     * @param int $patient_id Patient ID (optional)
     * @return bool
     */
    public function increment_views($id, $patient_id = null)
    {
        // Log the view
        $view_data = [
            'article_id' => $id,
            'patient_id' => $patient_id,
            'ip_address' => $this->input->ip_address(),
            'user_agent' => $this->input->user_agent(),
            'viewed_at' => date('Y-m-d H:i:s')
        ];

        $this->db->insert(db_prefix() . 'dietic_blog_article_views', $view_data);

        // Increment views count
        $this->db->where('id', $id);
        $this->db->set('views_count', 'views_count + 1', false);
        $this->db->update(db_prefix() . 'dietic_blog_articles');

        return true;
    }

    /**
     * Generate unique slug from title
     * @param string $title
     * @param int $id Article ID for updating
     * @return string
     */
    private function generate_slug($title, $id = null)
    {
        // Convert to lowercase and remove special characters
        $slug = strtolower($title);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        $slug = trim($slug, '-');

        // Check if slug exists
        $original_slug = $slug;
        $counter = 1;

        while (true) {
            $this->db->where('slug', $slug);
            if ($id) {
                $this->db->where('id !=', $id);
            }
            $count = $this->db->count_all_results(db_prefix() . 'dietic_blog_articles');

            if ($count == 0) {
                break;
            }

            $slug = $original_slug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    // ==================== CATEGORIES ====================

    /**
     * Get category by ID or slug
     * @param mixed $id
     * @return object|null
     */
    public function get_category($id)
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
        } else {
            $this->db->where('slug', $id);
        }

        return $this->db->get(db_prefix() . 'dietic_blog_categories')->row();
    }

    /**
     * Get all categories
     * @return array
     */
    public function get_all_categories()
    {
        $this->db->order_by('order', 'ASC');
        $this->db->order_by('name', 'ASC');
        return $this->db->get(db_prefix() . 'dietic_blog_categories')->result();
    }

    /**
     * Add category
     * @param array $data
     * @return int|bool
     */
    public function add_category($data)
    {
        // Generate slug if not provided
        if (empty($data['slug']) && !empty($data['name'])) {
            $data['slug'] = $this->generate_category_slug($data['name']);
        }

        $data['created_at'] = date('Y-m-d H:i:s');

        $this->db->insert(db_prefix() . 'dietic_blog_categories', $data);
        return $this->db->insert_id();
    }

    /**
     * Update category
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update_category($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'dietic_blog_categories', $data);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Delete category
     * @param int $id
     * @return bool
     */
    public function delete_category($id)
    {
        // Don't delete if articles are using this category
        $count = $this->db->where('category', $id)->count_all_results(db_prefix() . 'dietic_blog_articles');

        if ($count > 0) {
            return false; // Cannot delete category with articles
        }

        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'dietic_blog_categories');

        return $this->db->affected_rows() > 0;
    }

    /**
     * Generate unique category slug
     * @param string $name
     * @param int $id
     * @return string
     */
    private function generate_category_slug($name, $id = null)
    {
        $slug = strtolower($name);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        $slug = trim($slug, '-');

        $original_slug = $slug;
        $counter = 1;

        while (true) {
            $this->db->where('slug', $slug);
            if ($id) {
                $this->db->where('id !=', $id);
            }
            $count = $this->db->count_all_results(db_prefix() . 'dietic_blog_categories');

            if ($count == 0) {
                break;
            }

            $slug = $original_slug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Search articles
     * @param string $query Search query
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function search($query, $limit = 10, $offset = 0)
    {
        $this->db->select('a.*, s.firstname, s.lastname, CONCAT(s.firstname, " ", s.lastname) as author_name');
        $this->db->from(db_prefix() . 'dietic_blog_articles a');
        $this->db->join(db_prefix() . 'staff s', 's.staffid = a.author_id', 'left');

        $this->db->group_start();
        $this->db->like('a.title', $query);
        $this->db->or_like('a.excerpt', $query);
        $this->db->or_like('a.content', $query);
        $this->db->or_like('a.tags', $query);
        $this->db->group_end();

        $this->db->where('a.status', 'published');
        $this->db->where('a.published_at <=', date('Y-m-d H:i:s'));

        $this->db->order_by('a.published_at', 'DESC');
        $this->db->limit($limit, $offset);

        return $this->db->get()->result();
    }
}
