-- Dietetic Module Uninstallation SQL
-- Removes all tables created by the dietetic module

-- Drop tables in reverse order to avoid foreign key constraints
DROP TABLE IF EXISTS `tbldietic_documents`;
DROP TABLE IF EXISTS `tbldietic_reminders`;
DROP TABLE IF EXISTS `tbldietic_meal_foods`;
DROP TABLE IF EXISTS `tbldietic_meals`;
DROP TABLE IF EXISTS `tbldietic_meal_plans`;
DROP TABLE IF EXISTS `tbldietic_programs`;
DROP TABLE IF EXISTS `tbldietic_consultations`;
DROP TABLE IF EXISTS `tbldietic_measurements`;
DROP TABLE IF EXISTS `tbldietic_foods`;
DROP TABLE IF EXISTS `tbldietic_patients`;
DROP TABLE IF EXISTS `tbldietic_settings`;
