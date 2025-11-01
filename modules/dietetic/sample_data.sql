-- Sample Food Data for Dietetic Module
-- This file contains sample foods to populate the food database

-- Vegetables
INSERT INTO `tbldietic_foods` (`food_name`, `food_name_fr`, `category`, `serving_size`, `serving_unit`, `calories`, `protein`, `carbs`, `fats`, `fiber`, `sugar`, `sodium`, `is_active`, `created_at`) VALUES
('Broccoli', 'Brocoli', 'vegetables', 100, 'g', 34, 2.8, 7, 0.4, 2.6, 1.7, 33, 1, NOW()),
('Spinach', 'Épinards', 'vegetables', 100, 'g', 23, 2.9, 3.6, 0.4, 2.2, 0.4, 79, 1, NOW()),
('Carrots', 'Carottes', 'vegetables', 100, 'g', 41, 0.9, 10, 0.2, 2.8, 4.7, 69, 1, NOW()),
('Tomatoes', 'Tomates', 'vegetables', 100, 'g', 18, 0.9, 3.9, 0.2, 1.2, 2.6, 5, 1, NOW()),
('Bell Pepper', 'Poivron', 'vegetables', 100, 'g', 31, 1, 6, 0.3, 2.1, 4.2, 4, 1, NOW()),
('Cucumber', 'Concombre', 'vegetables', 100, 'g', 16, 0.7, 3.6, 0.1, 0.5, 1.7, 2, 1, NOW()),
('Lettuce', 'Laitue', 'vegetables', 100, 'g', 15, 1.4, 2.9, 0.2, 1.3, 0.8, 28, 1, NOW()),
('Zucchini', 'Courgette', 'vegetables', 100, 'g', 17, 1.2, 3.1, 0.3, 1, 2.5, 8, 1, NOW());

-- Fruits
INSERT INTO `tbldietic_foods` (`food_name`, `food_name_fr`, `category`, `serving_size`, `serving_unit`, `calories`, `protein`, `carbs`, `fats`, `fiber`, `sugar`, `sodium`, `is_active`, `created_at`) VALUES
('Apple', 'Pomme', 'fruits', 100, 'g', 52, 0.3, 14, 0.2, 2.4, 10, 1, 1, NOW()),
('Banana', 'Banane', 'fruits', 100, 'g', 89, 1.1, 23, 0.3, 2.6, 12, 1, 1, NOW()),
('Orange', 'Orange', 'fruits', 100, 'g', 47, 0.9, 12, 0.1, 2.4, 9, 0, 1, NOW()),
('Strawberries', 'Fraises', 'fruits', 100, 'g', 32, 0.7, 7.7, 0.3, 2, 4.9, 1, 1, NOW()),
('Blueberries', 'Myrtilles', 'fruits', 100, 'g', 57, 0.7, 14, 0.3, 2.4, 10, 1, 1, NOW()),
('Avocado', 'Avocat', 'fruits', 100, 'g', 160, 2, 8.5, 15, 6.7, 0.7, 7, 1, NOW()),
('Grapes', 'Raisins', 'fruits', 100, 'g', 69, 0.7, 18, 0.2, 0.9, 15, 2, 1, NOW());

-- Proteins
INSERT INTO `tbldietic_foods` (`food_name`, `food_name_fr`, `category`, `serving_size`, `serving_unit`, `calories`, `protein`, `carbs`, `fats`, `fiber`, `sugar`, `sodium`, `is_active`, `created_at`) VALUES
('Chicken Breast', 'Blanc de Poulet', 'proteins', 100, 'g', 165, 31, 0, 3.6, 0, 0, 74, 1, NOW()),
('Salmon', 'Saumon', 'proteins', 100, 'g', 208, 20, 0, 13, 0, 0, 59, 1, NOW()),
('Tuna', 'Thon', 'proteins', 100, 'g', 132, 28, 0, 1.3, 0, 0, 47, 1, NOW()),
('Eggs', 'Œufs', 'proteins', 100, 'g', 155, 13, 1.1, 11, 0, 1.1, 124, 1, NOW()),
('Turkey Breast', 'Blanc de Dinde', 'proteins', 100, 'g', 135, 30, 0, 0.7, 0, 0, 55, 1, NOW()),
('Beef Sirloin', 'Bœuf Faux-Filet', 'proteins', 100, 'g', 271, 25, 0, 19, 0, 0, 52, 1, NOW()),
('Tofu', 'Tofu', 'proteins', 100, 'g', 76, 8, 1.9, 4.8, 0.3, 0.7, 7, 1, NOW());

-- Grains
INSERT INTO `tbldietic_foods` (`food_name`, `food_name_fr`, `category`, `serving_size`, `serving_unit`, `calories`, `protein`, `carbs`, `fats`, `fiber`, `sugar`, `sodium`, `is_active`, `created_at`) VALUES
('Brown Rice', 'Riz Brun', 'grains', 100, 'g', 370, 7.9, 77, 2.9, 3.5, 0.9, 5, 1, NOW()),
('Quinoa', 'Quinoa', 'grains', 100, 'g', 368, 14, 64, 6, 7, 0, 5, 1, NOW()),
('Oatmeal', 'Flocons d\'Avoine', 'grains', 100, 'g', 389, 17, 66, 6.9, 11, 0, 2, 1, NOW()),
('Whole Wheat Bread', 'Pain Complet', 'grains', 100, 'g', 247, 13, 41, 3.4, 6.8, 5.6, 435, 1, NOW()),
('Pasta', 'Pâtes', 'grains', 100, 'g', 371, 13, 75, 1.5, 3.2, 2.7, 6, 1, NOW()),
('Couscous', 'Couscous', 'grains', 100, 'g', 376, 13, 77, 0.6, 5, 0.2, 8, 1, NOW());

-- Dairy
INSERT INTO `tbldietic_foods` (`food_name`, `food_name_fr`, `category`, `serving_size`, `serving_unit`, `calories`, `protein`, `carbs`, `fats`, `fiber`, `sugar`, `sodium`, `is_active`, `created_at`) VALUES
('Greek Yogurt', 'Yaourt Grec', 'dairy', 100, 'g', 59, 10, 3.6, 0.4, 0, 3.6, 36, 1, NOW()),
('Milk 2%', 'Lait 2%', 'dairy', 100, 'ml', 50, 3.3, 4.8, 2, 0, 4.8, 44, 1, NOW()),
('Cheddar Cheese', 'Fromage Cheddar', 'dairy', 100, 'g', 403, 25, 1.3, 33, 0, 0.5, 621, 1, NOW()),
('Cottage Cheese', 'Fromage Cottage', 'dairy', 100, 'g', 98, 11, 3.4, 4.3, 0, 2.7, 364, 1, NOW()),
('Mozzarella', 'Mozzarella', 'dairy', 100, 'g', 280, 28, 3.1, 17, 0, 1, 373, 1, NOW());

-- Fats & Oils
INSERT INTO `tbldietic_foods` (`food_name`, `food_name_fr`, `category`, `serving_size`, `serving_unit`, `calories`, `protein`, `carbs`, `fats`, `fiber`, `sugar`, `sodium`, `is_active`, `created_at`) VALUES
('Olive Oil', 'Huile d\'Olive', 'fats', 100, 'ml', 884, 0, 0, 100, 0, 0, 2, 1, NOW()),
('Almonds', 'Amandes', 'fats', 100, 'g', 579, 21, 22, 50, 13, 4.4, 1, 1, NOW()),
('Walnuts', 'Noix', 'fats', 100, 'g', 654, 15, 14, 65, 6.7, 2.6, 2, 1, NOW()),
('Peanut Butter', 'Beurre de Cacahuète', 'fats', 100, 'g', 588, 25, 20, 50, 6, 9.2, 17, 1, NOW()),
('Butter', 'Beurre', 'fats', 100, 'g', 717, 0.9, 0.1, 81, 0, 0.1, 11, 1, NOW());

-- Beverages
INSERT INTO `tbldietic_foods` (`food_name`, `food_name_fr`, `category`, `serving_size`, `serving_unit`, `calories`, `protein`, `carbs`, `fats`, `fiber`, `sugar`, `sodium`, `is_active`, `created_at`) VALUES
('Water', 'Eau', 'beverages', 100, 'ml', 0, 0, 0, 0, 0, 0, 0, 1, NOW()),
('Green Tea', 'Thé Vert', 'beverages', 100, 'ml', 1, 0, 0, 0, 0, 0, 1, 1, NOW()),
('Coffee (black)', 'Café (noir)', 'beverages', 100, 'ml', 2, 0.3, 0, 0, 0, 0, 2, 1, NOW()),
('Orange Juice', 'Jus d\'Orange', 'beverages', 100, 'ml', 45, 0.7, 10, 0.2, 0.2, 8.4, 1, 1, NOW());

-- Snacks
INSERT INTO `tbldietic_foods` (`food_name`, `food_name_fr`, `category`, `serving_size`, `serving_unit`, `calories`, `protein`, `carbs`, `fats`, `fiber`, `sugar`, `sodium`, `is_active`, `created_at`) VALUES
('Protein Bar', 'Barre Protéinée', 'snacks', 100, 'g', 380, 20, 40, 12, 5, 20, 200, 1, NOW()),
('Rice Cakes', 'Galettes de Riz', 'snacks', 100, 'g', 387, 8, 82, 2.8, 3.8, 1.5, 253, 1, NOW()),
('Dark Chocolate 70%', 'Chocolat Noir 70%', 'snacks', 100, 'g', 598, 7.8, 45, 43, 11, 24, 20, 1, NOW());

-- Condiments
INSERT INTO `tbldietic_foods` (`food_name`, `food_name_fr`, `category`, `serving_size`, `serving_unit`, `calories`, `protein`, `carbs`, `fats`, `fiber`, `sugar`, `sodium`, `is_active`, `created_at`) VALUES
('Honey', 'Miel', 'condiments', 100, 'g', 304, 0.3, 82, 0, 0.2, 82, 4, 1, NOW()),
('Mustard', 'Moutarde', 'condiments', 100, 'g', 66, 4, 7, 3.3, 5, 0.3, 1135, 1, NOW()),
('Balsamic Vinegar', 'Vinaigre Balsamique', 'condiments', 100, 'ml', 88, 0.5, 17, 0, 0, 14, 23, 1, NOW()),
('Soy Sauce', 'Sauce Soja', 'condiments', 100, 'ml', 60, 10, 6, 0.1, 0.8, 1.7, 5637, 1, NOW());
