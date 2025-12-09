-- Migration: Add patient self-booking fields to consultations table
-- Date: 2025-12-09
-- Description: Adds booked_by_patient field and extends status enum to support 'pending' status

-- Add booked_by_patient field to track if consultation was booked by patient
ALTER TABLE `tbldietic_consultations`
ADD COLUMN `booked_by_patient` TINYINT(1) DEFAULT 0 COMMENT 'Whether the consultation was booked by the patient (1) or by staff (0)';

-- Modify status field to include 'pending' status for appointment requests
-- Note: Since we can't directly modify ENUM in MySQL, we'll use a workaround
-- The status field is currently VARCHAR(20), so 'pending' will work without modification
-- But let's add a comment to document the new status value

-- Update the status field comment to include 'pending'
ALTER TABLE `tbldietic_consultations`
MODIFY COLUMN `status` VARCHAR(20) DEFAULT 'scheduled'
COMMENT 'scheduled, pending, confirmed, completed, cancelled, no_show, rejected';

-- Add index on booked_by_patient for faster queries
ALTER TABLE `tbldietic_consultations`
ADD INDEX `idx_booked_by_patient` (`booked_by_patient`);

-- Add composite index for filtering pending appointments by dietitian
ALTER TABLE `tbldietic_consultations`
ADD INDEX `idx_dietitian_status` (`dietitian_id`, `status`);
