-- Migration: Add UNIQUE constraint to dietic_daily_tracking table
-- This fixes the issue where meal checkboxes don't persist after page refresh
--
-- The INSERT ... ON DUPLICATE KEY UPDATE in Dietetic_daily_tracking_model::update_today()
-- requires a UNIQUE constraint on (patient_id, tracking_date) to work correctly

-- Step 1: Remove duplicate entries (keep the most recent one for each patient/date combination)
DELETE t1 FROM `tbldietic_daily_tracking` t1
INNER JOIN `tbldietic_daily_tracking` t2
WHERE
    t1.patient_id = t2.patient_id
    AND t1.tracking_date = t2.tracking_date
    AND t1.id < t2.id;

-- Step 2: Add UNIQUE constraint
ALTER TABLE `tbldietic_daily_tracking`
ADD UNIQUE KEY `unique_patient_date` (`patient_id`, `tracking_date`);
