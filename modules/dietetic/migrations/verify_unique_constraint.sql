-- Script de vérification de la contrainte UNIQUE
-- Exécutez ce script pour vérifier si la contrainte a bien été créée

-- 1. Vérifier si la contrainte UNIQUE existe
SELECT
    CONSTRAINT_NAME,
    CONSTRAINT_TYPE,
    TABLE_NAME
FROM
    information_schema.TABLE_CONSTRAINTS
WHERE
    TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'tbldietic_daily_tracking'
    AND CONSTRAINT_TYPE = 'UNIQUE';

-- 2. Voir les détails de la contrainte
SHOW CREATE TABLE `tbldietic_daily_tracking`;

-- 3. Compter les doublons potentiels (doit retourner 0)
SELECT
    patient_id,
    tracking_date,
    COUNT(*) as count
FROM
    `tbldietic_daily_tracking`
GROUP BY
    patient_id,
    tracking_date
HAVING
    COUNT(*) > 1;

-- 4. Voir tous les enregistrements d'aujourd'hui pour un patient
-- Remplacez X par l'ID du patient
-- SELECT * FROM `tbldietic_daily_tracking`
-- WHERE patient_id = X AND tracking_date = CURDATE();
