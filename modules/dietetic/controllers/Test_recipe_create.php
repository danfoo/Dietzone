<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Test Recipe Create - Debug
 */
class Test_recipe_create extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('dietetic/dietetic_foods_model');
        $this->load->model('dietetic/dietetic_recipes_model');
    }

    public function index()
    {
        echo '<h1>Test Recipe Create - Debug</h1>';
        echo '<style>body{font-family:Arial;padding:20px;} table{border-collapse:collapse;margin:20px 0;} th,td{border:1px solid #ddd;padding:8px;text-align:left;} th{background:#e74c3c;color:white;} .success{color:green;font-weight:bold;} .error{color:red;font-weight:bold;}</style>';

        // Test 1: Vérifier les données des aliments
        echo '<h2>Test 1: Vérifier les données des aliments</h2>';
        $foods = $this->dietetic_foods_model->get_active();

        echo '<p>Nombre d\'aliments actifs: <strong>' . count($foods) . '</strong></p>';

        if (count($foods) > 0) {
            echo '<table>';
            echo '<thead><tr>';
            echo '<th>ID</th>';
            echo '<th>Nom</th>';
            echo '<th>Calories</th>';
            echo '<th>Protéines</th>';
            echo '<th>Glucides</th>';
            echo '<th>Lipides</th>';
            echo '</tr></thead><tbody>';

            $count = 0;
            foreach ($foods as $food) {
                echo '<tr>';
                echo '<td>' . $food->id . '</td>';
                echo '<td>' . htmlspecialchars($food->food_name) . '</td>';
                echo '<td>' . (isset($food->calories) ? $food->calories : '<span class="error">MANQUANT</span>') . '</td>';
                echo '<td>' . (isset($food->protein) ? $food->protein : '<span class="error">MANQUANT</span>') . '</td>';
                echo '<td>' . (isset($food->carbs) ? $food->carbs : '<span class="error">MANQUANT</span>') . '</td>';
                echo '<td>' . (isset($food->fats) ? $food->fats : '<span class="error">MANQUANT</span>') . '</td>';
                echo '</tr>';

                $count++;
                if ($count >= 10) {
                    echo '<tr><td colspan="6"><em>... et ' . (count($foods) - 10) . ' autres aliments</em></td></tr>';
                    break;
                }
            }

            echo '</tbody></table>';
        } else {
            echo '<p class="error">❌ Aucun aliment actif trouvé</p>';
        }

        // Test 2: Simuler une création de recette
        echo '<h2>Test 2: Simuler une création de recette</h2>';

        $test_data = [
            'name' => 'Recette Test Debug',
            'description' => 'Test de création',
            'preparation_time' => 30,
            'category' => 'breakfast',
            'ingredients' => [
                [
                    'name' => 'Test Aliment',
                    'food_id' => 1,
                    'quantity' => 100,
                    'unit' => 'g'
                ]
            ],
            'instructions' => [
                'Étape 1 test'
            ],
            'nutrition' => [
                'calories' => 150.5,
                'protein' => 10.2,
                'carbs' => 20.3,
                'fat' => 5.1
            ],
            'tags' => ['test', 'debug']
        ];

        echo '<h3>Données à envoyer:</h3>';
        echo '<pre>' . print_r($test_data, true) . '</pre>';

        try {
            // Tester l'ajout (avec capture d'erreur)
            echo '<h3>Tentative d\'ajout...</h3>';

            // Enable error reporting
            error_reporting(E_ALL);
            ini_set('display_errors', 1);

            $recipe_id = $this->dietetic_recipes_model->add($test_data);

            if ($recipe_id) {
                echo '<p class="success">✅ Recette créée avec succès! ID: ' . $recipe_id . '</p>';

                // Récupérer la recette pour vérifier
                $recipe = $this->dietetic_recipes_model->get($recipe_id);
                echo '<h3>Recette récupérée:</h3>';
                echo '<pre>' . print_r($recipe, true) . '</pre>';

                // Supprimer la recette de test
                $this->db->where('id', $recipe_id);
                $this->db->delete(db_prefix() . 'dietic_recipes');
                echo '<p><em>Recette de test supprimée</em></p>';
            } else {
                echo '<p class="error">❌ Échec de la création (retourne FALSE)</p>';
            }
        } catch (Exception $e) {
            echo '<p class="error">❌ ERREUR: ' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
        }

        // Test 3: Vérifier les erreurs de base de données
        echo '<h2>Test 3: Vérifier les erreurs de base de données</h2>';
        $db_error = $this->db->error();
        if (!empty($db_error['message'])) {
            echo '<p class="error">❌ Erreur DB: ' . htmlspecialchars($db_error['message']) . '</p>';
        } else {
            echo '<p class="success">✅ Pas d\'erreur de base de données</p>';
        }

        // Test 4: Vérifier la structure de la table recipes
        echo '<h2>Test 4: Structure de la table recipes</h2>';
        $fields = $this->db->field_data(db_prefix() . 'dietic_recipes');
        echo '<table>';
        echo '<thead><tr><th>Champ</th><th>Type</th><th>Max Length</th></tr></thead><tbody>';
        foreach ($fields as $field) {
            echo '<tr>';
            echo '<td>' . $field->name . '</td>';
            echo '<td>' . $field->type . '</td>';
            echo '<td>' . (isset($field->max_length) ? $field->max_length : 'N/A') . '</td>';
            echo '</tr>';
        }
        echo '</tbody></table>';

        // Test 5: Tester le JSON encode des données
        echo '<h2>Test 5: Tester JSON encode</h2>';
        $test_ingredients = [
            [
                'name' => 'Test',
                'food_id' => 1,
                'quantity' => 100,
                'unit' => 'g'
            ]
        ];

        $json_result = json_encode($test_ingredients);
        if ($json_result === false) {
            echo '<p class="error">❌ Erreur JSON: ' . json_last_error_msg() . '</p>';
        } else {
            echo '<p class="success">✅ JSON encode fonctionne: ' . htmlspecialchars($json_result) . '</p>';
        }
    }
}
