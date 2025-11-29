<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_blog_system extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();

        // Create blog articles table
        if (!$CI->db->table_exists(db_prefix() . 'dietic_blog_articles')) {
            $CI->db->query("CREATE TABLE `" . db_prefix() . "dietic_blog_articles` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `title` varchar(255) NOT NULL,
                `slug` varchar(255) NOT NULL,
                `excerpt` text,
                `content` longtext NOT NULL,
                `featured_image` varchar(255) DEFAULT NULL,
                `category` varchar(100) DEFAULT NULL,
                `tags` text,
                `author_id` int(11) NOT NULL COMMENT 'Staff member ID',
                `status` enum('draft','published','archived') NOT NULL DEFAULT 'draft',
                `views_count` int(11) NOT NULL DEFAULT 0,
                `published_at` datetime DEFAULT NULL,
                `created_at` datetime NOT NULL,
                `updated_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `slug` (`slug`),
                KEY `author_id` (`author_id`),
                KEY `status` (`status`),
                KEY `category` (`category`),
                KEY `published_at` (`published_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
        }

        // Create blog categories table
        if (!$CI->db->table_exists(db_prefix() . 'dietic_blog_categories')) {
            $CI->db->query("CREATE TABLE `" . db_prefix() . "dietic_blog_categories` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(100) NOT NULL,
                `slug` varchar(100) NOT NULL,
                `description` text,
                `color` varchar(7) DEFAULT '#01807B',
                `icon` varchar(50) DEFAULT 'fa-newspaper-o',
                `order` int(11) NOT NULL DEFAULT 0,
                `created_at` datetime NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `slug` (`slug`),
                KEY `order` (`order`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
        }

        // Insert default categories
        if ($CI->db->table_exists(db_prefix() . 'dietic_blog_categories')) {
            $default_categories = [
                [
                    'name' => 'Nutrition',
                    'slug' => 'nutrition',
                    'description' => 'Conseils et informations sur la nutrition',
                    'color' => '#4CAF50',
                    'icon' => 'fa-apple',
                    'order' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'name' => 'Recettes Santé',
                    'slug' => 'recettes-sante',
                    'description' => 'Recettes saines et équilibrées',
                    'color' => '#FF9800',
                    'icon' => 'fa-cutlery',
                    'order' => 2,
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'name' => 'Perte de Poids',
                    'slug' => 'perte-de-poids',
                    'description' => 'Conseils pour une perte de poids saine',
                    'color' => '#E91E63',
                    'icon' => 'fa-heart',
                    'order' => 3,
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'name' => 'Bien-être',
                    'slug' => 'bien-etre',
                    'description' => 'Conseils pour un mode de vie sain',
                    'color' => '#9C27B0',
                    'icon' => 'fa-leaf',
                    'order' => 4,
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'name' => 'Sport & Activité',
                    'slug' => 'sport-activite',
                    'description' => 'Conseils sur l\'activité physique',
                    'color' => '#2196F3',
                    'icon' => 'fa-heartbeat',
                    'order' => 5,
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ];

            foreach ($default_categories as $category) {
                // Check if category doesn't already exist
                $exists = $CI->db->get_where(db_prefix() . 'dietic_blog_categories', ['slug' => $category['slug']])->row();
                if (!$exists) {
                    $CI->db->insert(db_prefix() . 'dietic_blog_categories', $category);
                }
            }
        }

        // Create blog comments table (optional - for future)
        if (!$CI->db->table_exists(db_prefix() . 'dietic_blog_comments')) {
            $CI->db->query("CREATE TABLE `" . db_prefix() . "dietic_blog_comments` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `article_id` int(11) NOT NULL,
                `patient_id` int(11) DEFAULT NULL,
                `author_name` varchar(100) NOT NULL,
                `author_email` varchar(100) DEFAULT NULL,
                `content` text NOT NULL,
                `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
                `created_at` datetime NOT NULL,
                PRIMARY KEY (`id`),
                KEY `article_id` (`article_id`),
                KEY `patient_id` (`patient_id`),
                KEY `status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
        }

        // Create blog article views tracking table
        if (!$CI->db->table_exists(db_prefix() . 'dietic_blog_article_views')) {
            $CI->db->query("CREATE TABLE `" . db_prefix() . "dietic_blog_article_views` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `article_id` int(11) NOT NULL,
                `patient_id` int(11) DEFAULT NULL,
                `ip_address` varchar(45) DEFAULT NULL,
                `user_agent` varchar(255) DEFAULT NULL,
                `viewed_at` datetime NOT NULL,
                PRIMARY KEY (`id`),
                KEY `article_id` (`article_id`),
                KEY `patient_id` (`patient_id`),
                KEY `viewed_at` (`viewed_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
        }
    }

    public function down()
    {
        $CI = &get_instance();

        // Drop tables in reverse order (due to potential foreign keys)
        $tables = [
            db_prefix() . 'dietic_blog_article_views',
            db_prefix() . 'dietic_blog_comments',
            db_prefix() . 'dietic_blog_categories',
            db_prefix() . 'dietic_blog_articles'
        ];

        foreach ($tables as $table) {
            if ($CI->db->table_exists($table)) {
                $CI->db->query("DROP TABLE `$table`");
            }
        }
    }
}
