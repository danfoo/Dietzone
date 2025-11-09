-- Migration: Add Staff Permissions System for Granular Access Control
-- Description: Allows admins to control which features each dietitian can access
-- Date: 2025-11-09

-- Create staff permissions table
CREATE TABLE IF NOT EXISTS `tbldietic_staff_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `staff_id` int(11) NOT NULL COMMENT 'FK to tblstaff',
  `permission_key` varchar(100) NOT NULL COMMENT 'e.g., food_surveys, notifications_settings',
  `permission_value` tinyint(1) DEFAULT 1 COMMENT '1 = enabled, 0 = disabled',
  `granted_by` int(11) DEFAULT NULL COMMENT 'Admin who granted this permission',
  `granted_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `notes` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_staff_permission` (`staff_id`, `permission_key`),
  KEY `idx_staff_id` (`staff_id`),
  KEY `idx_permission_key` (`permission_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default permissions for existing staff
-- This query will give all existing dietetic staff access to all features by default
INSERT INTO `tbldietic_staff_permissions` (`staff_id`, `permission_key`, `permission_value`, `granted_by`, `notes`)
SELECT
    s.staffid,
    'food_surveys',
    1,
    1, -- Granted by admin (staffid = 1)
    'Default permission granted during system upgrade'
FROM `tblstaff` s
WHERE s.active = 1
  AND s.staffid NOT IN (SELECT staff_id FROM `tbldietic_staff_permissions` WHERE permission_key = 'food_surveys')
ON DUPLICATE KEY UPDATE permission_value = permission_value; -- Don't override existing

-- Available permission keys:
-- 'food_surveys'         - Access to Food Surveys module (Enquêtes Alimentaires)
-- 'notifications_manage' - Manage notification settings (Admin only by default)
-- 'reports_advanced'     - Access advanced reporting features
-- 'settings_module'      - Access module settings

-- Note: Admins (is_admin = 1) bypass all these permission checks
-- Note: If a permission is not in the table, default behavior is to DENY access (secure by default)

-- Create settings to control default behavior
INSERT INTO `tbldietic_settings` (`setting_key`, `setting_value`, `setting_type`, `description`)
VALUES
('permissions_default_food_surveys', '0', 'boolean', 'Default permission for new staff: Food Surveys access')
ON DUPLICATE KEY UPDATE `description` = VALUES(`description`);

INSERT INTO `tbldietic_settings` (`setting_key`, `setting_value`, `setting_type`, `description`)
VALUES
('permissions_default_notifications_manage', '0', 'boolean', 'Default permission for new staff: Manage notifications settings')
ON DUPLICATE KEY UPDATE `description` = VALUES(`description`);
