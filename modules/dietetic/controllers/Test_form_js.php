<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Test_form_js extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('dietetic/dietetic');
    }

    public function index()
    {
        if (!function_exists('dietetic_has_permission') || !dietetic_has_permission('create')) {
            access_denied('dietetic');
        }

        $this->load->model('dietetic/dietetic_foods_model');
        $this->load->model('dietetic/dietetic_recipes_model');

        $data['foods'] = $this->dietetic_foods_model->get_active();
        $data['title'] = 'Test Formulaire JavaScript';
        $data['recipe'] = null; // Mode création

        echo "<!DOCTYPE html><html><head>";
        echo "<title>Test Formulaire</title>";
        echo '<link rel="stylesheet" href="' . base_url('assets/plugins/bootstrap/css/bootstrap.min.css') . '">';
        echo '<link rel="stylesheet" href="' . base_url('assets/plugins/bootstrap-select/css/bootstrap-select.min.css') . '">';
        echo '<script src="' . base_url('assets/plugins/jquery/jquery.min.js') . '"></script>';
        echo '<script src="' . base_url('assets/plugins/bootstrap/js/bootstrap.min.js') . '"></script>';
        echo '<script src="' . base_url('assets/plugins/bootstrap-select/js/bootstrap-select.min.js') . '"></script>';
        echo '<style>body{padding:20px;} .test-section{background:#f8f9fa;padding:15px;margin:20px 0;border-radius:5px;} .success{color:#27ae60;} .error{color:#e74c3c;}</style>';
        echo "</head><body>";

        echo "<h1>🧪 Test JavaScript Formulaire Recettes</h1>";

        // TEST 1: jQuery
        echo "<div class='test-section'>";
        echo "<h2>TEST 1: jQuery</h2>";
        echo "<p id='jquery-test'>❌ jQuery NON chargé</p>";
        echo "<script>$(document).ready(function(){ $('#jquery-test').html('✅ <span class=\"success\">jQuery chargé et fonctionnel</span>'); });</script>";
        echo "</div>";

        // TEST 2: Bootstrap Select
        echo "<div class='test-section'>";
        echo "<h2>TEST 2: Bootstrap Select Plugin</h2>";
        echo "<select class='selectpicker' data-live-search='true'>";
        echo "<option>Test Option 1</option>";
        echo "<option>Test Option 2</option>";
        echo "</select>";
        echo "<p id='selectpicker-test' style='margin-top:10px;'>En attente...</p>";
        echo "<script>$(document).ready(function(){ ";
        echo "if (typeof $.fn.selectpicker !== 'undefined') { ";
        echo "$('.selectpicker').selectpicker(); ";
        echo "$('#selectpicker-test').html('✅ <span class=\"success\">Bootstrap Select fonctionnel</span>'); ";
        echo "} else { ";
        echo "$('#selectpicker-test').html('❌ <span class=\"error\">Bootstrap Select NON chargé</span>'); ";
        echo "} ";
        echo "});</script>";
        echo "</div>";

        // TEST 3: Données foods
        echo "<div class='test-section'>";
        echo "<h2>TEST 3: Données Foods</h2>";
        echo "<p>Nombre d'aliments: <strong>" . count($data['foods']) . "</strong></p>";
        if (count($data['foods']) > 0) {
            echo "<p class='success'>✅ Aliments chargés</p>";
            echo "<p>Premier aliment: <code>" . htmlspecialchars($data['foods'][0]->food_name) . "</code></p>";
        } else {
            echo "<p class='error'>❌ Aucun aliment trouvé</p>";
        }
        echo "</div>";

        // TEST 4: Bouton Ajouter Ingrédient
        echo "<div class='test-section'>";
        echo "<h2>TEST 4: Bouton Ajouter Ingrédient</h2>";
        echo "<div id='ingredients-container'>";
        echo "<div class='ingredient-row'>Ligne d'ingrédient existante</div>";
        echo "</div>";
        echo "<button type='button' class='btn btn-info' id='add-ingredient'>Ajouter un ingrédient</button>";
        echo "<p id='add-ingredient-test' style='margin-top:10px;'>Cliquez sur le bouton ci-dessus</p>";

        echo "<script>";
        echo "var foodsData = " . json_encode($data['foods']) . ";";
        echo "console.log('foodsData loaded:', foodsData.length, 'items');";

        echo "$(document).ready(function() {";
        echo "  console.log('Ready event fired');";
        echo "  $('#add-ingredient').click(function() {";
        echo "    console.log('Add ingredient clicked');";
        echo "    var count = $('.ingredient-row').length;";
        echo "    $('#ingredients-container').append('<div class=\"ingredient-row\">Nouvelle ligne ' + (count + 1) + '</div>');";
        echo "    $('#add-ingredient-test').html('✅ <span class=\"success\">Bouton fonctionne! Lignes: ' + (count + 1) + '</span>');";
        echo "  });";
        echo "});";
        echo "</script>";
        echo "</div>";

        // TEST 5: Bouton Ajouter Instruction
        echo "<div class='test-section'>";
        echo "<h2>TEST 5: Bouton Ajouter Instruction</h2>";
        echo "<div id='instructions-container'>";
        echo "<div class='instruction-row'>Instruction existante</div>";
        echo "</div>";
        echo "<button type='button' class='btn btn-info' id='add-instruction'>Ajouter une étape</button>";
        echo "<p id='add-instruction-test' style='margin-top:10px;'>Cliquez sur le bouton ci-dessus</p>";

        echo "<script>";
        echo "$(document).ready(function() {";
        echo "  $('#add-instruction').click(function() {";
        echo "    console.log('Add instruction clicked');";
        echo "    var count = $('.instruction-row').length;";
        echo "    $('#instructions-container').append('<div class=\"instruction-row\">Nouvelle instruction ' + (count + 1) + '</div>');";
        echo "    $('#add-instruction-test').html('✅ <span class=\"success\">Bouton fonctionne! Étapes: ' + (count + 1) + '</span>');";
        echo "  });";
        echo "});";
        echo "</script>";
        echo "</div>";

        // TEST 6: Calcul nutrition
        echo "<div class='test-section'>";
        echo "<h2>TEST 6: Calcul Nutrition Automatique</h2>";
        echo "<label>Sélectionnez un aliment:</label>";
        echo "<select class='selectpicker food-select-test' data-live-search='true'>";
        echo "<option value=''>-- Sélectionner --</option>";
        foreach ($data['foods'] as $food) {
            echo "<option value='{$food->id}' data-calories='{$food->calories}' data-protein='{$food->protein}' data-carbs='{$food->carbs}' data-fat='{$food->fats}'>";
            echo htmlspecialchars($food->food_name);
            echo "</option>";
        }
        echo "</select>";
        echo "<br><br>";
        echo "<label>Quantité (g):</label>";
        echo "<input type='number' class='form-control quantity-test' style='width:200px;' value='100'>";
        echo "<br>";
        echo "<p>Calories calculées: <strong id='calc-result'>0</strong></p>";

        echo "<script>";
        echo "$(document).ready(function() {";
        echo "  $('.selectpicker').selectpicker('refresh');";
        echo "  ";
        echo "  function testCalculate() {";
        echo "    var option = $('.food-select-test').find('option:selected');";
        echo "    var quantity = parseFloat($('.quantity-test').val()) || 0;";
        echo "    var calories = parseFloat(option.data('calories')) || 0;";
        echo "    var result = (calories * quantity) / 100;";
        echo "    $('#calc-result').text(result.toFixed(1));";
        echo "    console.log('Calcul:', calories, '*', quantity, '/ 100 =', result);";
        echo "  }";
        echo "  ";
        echo "  $('.food-select-test, .quantity-test').on('change input', testCalculate);";
        echo "});";
        echo "</script>";
        echo "</div>";

        echo "<div class='test-section' style='background:#d4edda;'>";
        echo "<h2>📋 Instructions</h2>";
        echo "<ol>";
        echo "<li>Vérifiez que TOUS les tests ci-dessus sont ✅ verts</li>";
        echo "<li>Ouvrez la <strong>Console navigateur</strong> (F12 → Console)</li>";
        echo "<li>Vérifiez qu'il n'y a <strong>PAS d'erreurs rouges</strong></li>";
        echo "<li>Testez chaque bouton et chaque fonctionnalité</li>";
        echo "<li>Si tout fonctionne ici mais pas sur le vrai formulaire, le problème est dans le template</li>";
        echo "</ol>";
        echo "</div>";

        echo "</body></html>";
    }
}
