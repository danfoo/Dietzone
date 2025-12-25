-- Migration: Add new specialties and update existing ones
-- Description: Add 3 new specialties and update 2 existing ones
-- Date: 2025-12-14

-- Update existing specialties with new names
UPDATE `tbldietic_specialties`
SET `name_fr` = 'Nutrition pédiatrique'
WHERE `name_fr` = 'Pédiatrie';

UPDATE `tbldietic_specialties`
SET `name_fr` = 'Troubles du comportement alimentaire (TCA)'
WHERE `name_fr` = 'Troubles alimentaires';

-- Add new specialties if they don't exist
INSERT INTO `tbldietic_specialties` (`name_fr`, `name_en`, `icon`, `color`, `display_order`)
SELECT 'Nutrition de la femme', 'Women\'s Nutrition', 'fa-venus', '#e91e8f', 13
WHERE NOT EXISTS (
    SELECT 1 FROM `tbldietic_specialties` WHERE `name_fr` = 'Nutrition de la femme'
);

INSERT INTO `tbldietic_specialties` (`name_fr`, `name_en`, `icon`, `color`, `display_order`)
SELECT 'Nutrition santé publique & collective', 'Public & Community Health Nutrition', 'fa-users', '#2980b9', 14
WHERE NOT EXISTS (
    SELECT 1 FROM `tbldietic_specialties` WHERE `name_fr` = 'Nutrition santé publique & collective'
);

INSERT INTO `tbldietic_specialties` (`name_fr`, `name_en`, `icon`, `color`, `display_order`)
SELECT 'Nutrition fonctionnelle & préventive', 'Functional & Preventive Nutrition', 'fa-shield', '#16a085', 15
WHERE NOT EXISTS (
    SELECT 1 FROM `tbldietic_specialties` WHERE `name_fr` = 'Nutrition fonctionnelle & préventive'
);

-- Success message
SELECT 'New specialties added successfully!' as status;
