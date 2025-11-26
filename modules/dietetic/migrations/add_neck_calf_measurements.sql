-- Migration: Add neck and calf measurements to measurements table
-- Date: 2025-11-26

-- Add neck column if it doesn't exist
SET @dbname = DATABASE();
SET @tablename = 'tbldietic_measurements';
SET @columnname = 'neck';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' decimal(5,2) DEFAULT NULL COMMENT ''Tour de cou in cm'' AFTER hips')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Add calf column if it doesn't exist
SET @columnname = 'calf';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' decimal(5,2) DEFAULT NULL COMMENT ''Tour de mollets in cm'' AFTER thighs')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;
