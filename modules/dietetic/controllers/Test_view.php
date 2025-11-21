<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Test_view extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index($id = 1)
    {
        if (!is_admin()) {
            access_denied('Test');
        }

        echo "<!DOCTYPE html><html><head><title>Test View Recipe</title>";
        echo "<style>body{font-family:Arial;padding:20px;background:#1e1e1e;color:#d4d4d4;} .success{color:#4ec9b0;} .error{color:#f48771;} pre{background:#252526;padding:15px;border:1px solid #3e3e42;overflow-x:auto;} .section{background:#252526;padding:20px;margin:20px 0;border-radius:8px;border:1px solid #3e3e42;} h2{color:#4ec9b0;}</style>";
        echo "</head><body>";

        echo "<h1>🧪 Test View Recipe ID: $id</h1>";

        $CI = &get_instance();

        // TEST 1: Charger le modèle
        echo "<div class='section'>";
        echo "<h2>TEST 1: Charger le modèle</h2>";
        try {
            $CI->load->model('dietetic/dietetic_recipes_model');
            echo "<p class='success'>✅ Modèle chargé</p>";
        } catch (Exception $e) {
            echo "<p class='error'>❌ Erreur: " . $e->getMessage() . "</p>";
            echo "</body></html>";
            return;
        }
        echo "</div>";

        // TEST 2: Récupérer la recette
        echo "<div class='section'>";
        echo "<h2>TEST 2: Récupérer la recette</h2>";
        try {
            $recipe = $CI->dietetic_recipes_model->get($id);

            if (!$recipe) {
                echo "<p class='error'>❌ Recette ID $id non trouvée</p>";
                echo "</body></html>";
                return;
            }

            echo "<p class='success'>✅ Recette trouvée</p>";
            echo "<p><strong>Nom:</strong> " . htmlspecialchars($recipe->name) . "</p>";
            echo "<p><strong>Status:</strong> " . $recipe->status . "</p>";
        } catch (Exception $e) {
            echo "<p class='error'>❌ Erreur lors de get(): " . $e->getMessage() . "</p>";
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
            echo "</body></html>";
            return;
        }
        echo "</div>";

        // TEST 3: Vérifier les propriétés
        echo "<div class='section'>";
        echo "<h2>TEST 3: Propriétés de la recette</h2>";

        $properties = ['ingredients', 'instructions', 'nutrition', 'photos', 'tags', 'average_rating', 'ratings_count'];

        foreach ($properties as $prop) {
            if (isset($recipe->$prop)) {
                $value = $recipe->$prop;
                if (is_array($value)) {
                    echo "<p class='success'>✅ $prop: " . count($value) . " élément(s)</p>";
                } else {
                    echo "<p class='success'>✅ $prop: " . htmlspecialchars($value) . "</p>";
                }
            } else {
                echo "<p class='error'>❌ $prop: NON défini</p>";
            }
        }
        echo "</div>";

        // TEST 4: Inspecter les ingrédients
        echo "<div class='section'>";
        echo "<h2>TEST 4: Ingrédients</h2>";
        if (isset($recipe->ingredients) && !empty($recipe->ingredients)) {
            echo "<p class='success'>✅ " . count($recipe->ingredients) . " ingrédient(s)</p>";
            echo "<pre>" . print_r($recipe->ingredients, true) . "</pre>";
        } else {
            echo "<p>Aucun ingrédient</p>";
        }
        echo "</div>";

        // TEST 5: Inspecter les photos
        echo "<div class='section'>";
        echo "<h2>TEST 5: Photos</h2>";
        if (isset($recipe->photos) && !empty($recipe->photos)) {
            echo "<p class='success'>✅ " . count($recipe->photos) . " photo(s)</p>";
            foreach ($recipe->photos as $photo) {
                echo "<div style='margin:10px 0;padding:10px;background:#1e1e1e;'>";
                echo "<p><strong>ID:</strong> " . $photo->id . "</p>";
                echo "<p><strong>URL:</strong> " . htmlspecialchars($photo->photo_url) . "</p>";
                echo "<p><strong>is_main existe?</strong> " . (isset($photo->is_main) ? 'OUI' : 'NON') . "</p>";
                if (isset($photo->is_main)) {
                    echo "<p><strong>is_main value:</strong> " . $photo->is_main . "</p>";
                }
                echo "</div>";
            }
        } else {
            echo "<p>Aucune photo</p>";
        }
        echo "</div>";

        // TEST 6: Récupérer les ratings
        echo "<div class='section'>";
        echo "<h2>TEST 6: Récupérer les ratings</h2>";
        try {
            $ratings = $CI->dietetic_recipes_model->get_ratings($id);
            echo "<p class='success'>✅ get_ratings() exécuté</p>";
            echo "<p>Nombre de ratings: " . count($ratings) . "</p>";
        } catch (Exception $e) {
            echo "<p class='error'>❌ Erreur: " . $e->getMessage() . "</p>";
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
        }
        echo "</div>";

        // TEST 7: Charger la vue
        echo "<div class='section'>";
        echo "<h2>TEST 7: Charger la vue</h2>";
        try {
            $data = [];
            $data['recipe'] = $recipe;
            $data['title'] = $recipe->name;
            $data['ratings'] = isset($ratings) ? $ratings : [];

            ob_start();
            $CI->load->view('dietetic/admin/recipes/view', $data);
            $output = ob_get_clean();

            echo "<p class='success'>✅ Vue chargée sans erreur!</p>";
            echo "<p>Taille: " . strlen($output) . " octets</p>";

            // Afficher un extrait
            echo "<p><strong>Extrait (premiers 500 caractères):</strong></p>";
            echo "<pre>" . htmlspecialchars(substr($output, 0, 500)) . "</pre>";

        } catch (Exception $e) {
            echo "<p class='error'>❌ Erreur lors du chargement de la vue: " . $e->getMessage() . "</p>";
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
        }
        echo "</div>";

        echo "<div class='section' style='background:#3a2a1a;border-color:#f39c12;'>";
        echo "<h2>🔧 Résultat</h2>";
        echo "<p>Si tous les tests passent mais que /admin/dietetic/recipes/view/$id donne erreur 500,</p>";
        echo "<p>le problème est dans le contrôleur Recipes lui-même.</p>";
        echo "<hr>";
        echo "<a href='" . admin_url('dietetic/recipes/view/' . $id) . "' style='color:#3498db;'>Tester la vraie page maintenant</a>";
        echo "</div>";

        echo "</body></html>";
    }
}
