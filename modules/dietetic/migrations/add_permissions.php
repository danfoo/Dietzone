<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_permissions extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();

        // Add module permissions for staff roles
        $permissions = [
            [
                'name' => 'dietetic',
                'shortname' => 'view',
            ],
            [
                'name' => 'dietetic',
                'shortname' => 'create',
            ],
            [
                'name' => 'dietetic',
                'shortname' => 'edit',
            ],
            [
                'name' => 'dietetic',
                'shortname' => 'delete',
            ],
            [
                'name' => 'dietetic',
                'shortname' => 'manage',
            ],
        ];

        foreach ($permissions as $permission) {
            // Check if permission already exists
            $exists = $CI->db->get_where(db_prefix() . 'permissions', [
                'name' => $permission['name'],
                'shortname' => $permission['shortname']
            ])->row();

            if (!$exists) {
                $CI->db->insert(db_prefix() . 'permissions', $permission);
            }
        }
    }

    public function down()
    {
        // Optional: Remove permissions on downgrade
        // Not recommended for production
    }
}
