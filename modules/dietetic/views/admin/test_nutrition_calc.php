<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
body { padding: 20px; font-family: Arial; }
.test-container { max-width: 800px; margin: 0 auto; }
.ingredient-row { margin-bottom: 15px; padding: 15px; background: #f8f9fa; border-radius: 4px; }
.nutrition-result { padding: 20px; background: #e8f5e9; border-radius: 4px; margin-top: 20px; }
.nutrition-result h3 { margin-top: 0; color: #2e7d32; }
.nutrition-values { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; }
.nutrition-item { text-align: center; padding: 10px; background: white; border-radius: 4px; }
.nutrition-item strong { display: block; font-size: 24px; color: #e74c3c; }
.debug-info { padding: 15px; background: #fff3cd; border-radius: 4px; margin-top: 20px; font-family: monospace; font-size: 12px; }
</style>

<div class="test-container">
    <h1>🧪 Test Calcul Nutrition en Direct</h1>

    <div class="alert alert-info">
        <strong>Instructions:</strong> Sélectionnez un aliment, entrez une quantité en grammes, et voyez le calcul automatique en temps réel.
    </div>

    <div id="ingredients-container">
        <div class="ingredient-row">
            <h4>Ingrédient 1</h4>
            <div class="row">
                <div class="col-md-6">
                    <label>Aliment:</label>
                    <select class="form-control selectpicker food-select" data-live-search="true">
                        <option value="">-- Sélectionner un aliment --</option>
                        <?php if (isset($foods) && !empty($foods)) : ?>
                            <?php foreach ($foods as $food) : ?>
                                <option value="<?php echo $food->id; ?>"
                                        data-calories="<?php echo $food->calories ?? 0; ?>"
                                        data-protein="<?php echo $food->protein ?? 0; ?>"
                                        data-carbs="<?php echo $food->carbs ?? 0; ?>"
                                        data-fat="<?php echo $food->fats ?? 0; ?>">
                                    <?php echo htmlspecialchars($food->food_name); ?>
                                    (Cal: <?php echo $food->calories ?? 0; ?>,
                                     Prot: <?php echo $food->protein ?? 0; ?>g,
                                     Gluc: <?php echo $food->carbs ?? 0; ?>g,
                                     Lip: <?php echo $food->fats ?? 0; ?>g)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Quantité (g):</label>
                    <input type="number" step="0.01" class="form-control ingredient-quantity"
                           placeholder="Entrez la quantité en grammes" value="100">
                </div>
                <div class="col-md-2">
                    <label>&nbsp;</label>
                    <button type="button" class="btn btn-danger btn-block remove-ingredient">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <button type="button" class="btn btn-info" id="add-ingredient">
        <i class="fa fa-plus"></i> Ajouter un ingrédient
    </button>

    <div class="nutrition-result">
        <h3>📊 Résultats Nutritionnels (calculés automatiquement)</h3>
        <div class="nutrition-values">
            <div class="nutrition-item">
                <strong id="calc-calories">0.0</strong>
                <span>Calories (kcal)</span>
            </div>
            <div class="nutrition-item">
                <strong id="calc-protein">0.0</strong>
                <span>Protéines (g)</span>
            </div>
            <div class="nutrition-item">
                <strong id="calc-carbs">0.0</strong>
                <span>Glucides (g)</span>
            </div>
            <div class="nutrition-item">
                <strong id="calc-fat">0.0</strong>
                <span>Lipides (g)</span>
            </div>
        </div>
    </div>

    <div class="debug-info">
        <h4>🐛 Debug Console</h4>
        <div id="debug-output">En attente...</div>
    </div>

    <div style="margin-top: 20px;">
        <a href="<?php echo admin_url('dietetic/recipes/create'); ?>" class="btn btn-primary">
            ← Retour au formulaire de recette
        </a>
    </div>
</div>

<?php init_tail(); ?>

<script>
var foodsData = <?php echo json_encode(isset($foods) ? $foods : []); ?>;
var debugOutput = [];

function log(message, data) {
    var timestamp = new Date().toLocaleTimeString();
    var logEntry = timestamp + ' - ' + message;
    if (data) {
        logEntry += ': ' + JSON.stringify(data);
    }
    debugOutput.push(logEntry);
    $('#debug-output').html(debugOutput.join('<br>'));
    console.log('[Nutrition Test]', message, data || '');
}

$(document).ready(function() {
    log('Page chargée', { foodsCount: foodsData.length });

    // Initialize selectpicker
    $('.selectpicker').selectpicker('refresh');
    log('Selectpicker initialisé');

    // Calculate nutrition automatically
    function calculateNutrition() {
        let totalCalories = 0;
        let totalProtein = 0;
        let totalCarbs = 0;
        let totalFat = 0;
        let ingredientDetails = [];

        $('.ingredient-row').each(function(index) {
            const select = $(this).find('.food-select');
            const quantity = parseFloat($(this).find('.ingredient-quantity').val()) || 0;

            if (select.val()) {
                const option = select.find('option:selected');
                const foodName = option.text().split('(')[0].trim();
                const calories = parseFloat(option.data('calories')) || 0;
                const protein = parseFloat(option.data('protein')) || 0;
                const carbs = parseFloat(option.data('carbs')) || 0;
                const fat = parseFloat(option.data('fat')) || 0;

                log('Ingrédient ' + (index + 1), {
                    name: foodName,
                    quantity: quantity,
                    calories: calories,
                    protein: protein,
                    carbs: carbs,
                    fat: fat
                });

                // Calculate based on quantity (per 100g)
                const calcCalories = (calories * quantity) / 100;
                const calcProtein = (protein * quantity) / 100;
                const calcCarbs = (carbs * quantity) / 100;
                const calcFat = (fat * quantity) / 100;

                totalCalories += calcCalories;
                totalProtein += calcProtein;
                totalCarbs += calcCarbs;
                totalFat += calcFat;

                ingredientDetails.push({
                    name: foodName,
                    quantity: quantity,
                    calories: calcCalories.toFixed(1),
                    protein: calcProtein.toFixed(1),
                    carbs: calcCarbs.toFixed(1),
                    fat: calcFat.toFixed(1)
                });
            }
        });

        $('#calc-calories').text(totalCalories.toFixed(1));
        $('#calc-protein').text(totalProtein.toFixed(1));
        $('#calc-carbs').text(totalCarbs.toFixed(1));
        $('#calc-fat').text(totalFat.toFixed(1));

        log('Calcul final', {
            totalCalories: totalCalories.toFixed(1),
            totalProtein: totalProtein.toFixed(1),
            totalCarbs: totalCarbs.toFixed(1),
            totalFat: totalFat.toFixed(1),
            ingredients: ingredientDetails
        });
    }

    // Generate options HTML from foodsData
    function generateFoodOptions() {
        let optionsHtml = '<option value="">-- Sélectionner un aliment --</option>';

        if (foodsData && foodsData.length > 0) {
            foodsData.forEach(function(food) {
                optionsHtml += '<option value="' + food.id + '"' +
                    ' data-calories="' + (food.calories || 0) + '"' +
                    ' data-protein="' + (food.protein || 0) + '"' +
                    ' data-carbs="' + (food.carbs || 0) + '"' +
                    ' data-fat="' + (food.fats || 0) + '">' +
                    food.food_name +
                    ' (Cal: ' + (food.calories || 0) + ', ' +
                    'Prot: ' + (food.protein || 0) + 'g, ' +
                    'Gluc: ' + (food.carbs || 0) + 'g, ' +
                    'Lip: ' + (food.fats || 0) + 'g)' +
                    '</option>';
            });
        }

        return optionsHtml;
    }

    // Add ingredient
    $('#add-ingredient').click(function() {
        const stepNumber = $('.ingredient-row').length + 1;
        log('Ajout ingrédient ' + stepNumber);

        const html = `
            <div class="ingredient-row">
                <h4>Ingrédient ${stepNumber}</h4>
                <div class="row">
                    <div class="col-md-6">
                        <label>Aliment:</label>
                        <select class="form-control selectpicker food-select" data-live-search="true">
                            ${generateFoodOptions()}
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>Quantité (g):</label>
                        <input type="number" step="0.01" class="form-control ingredient-quantity"
                               placeholder="Entrez la quantité en grammes" value="100">
                    </div>
                    <div class="col-md-2">
                        <label>&nbsp;</label>
                        <button type="button" class="btn btn-danger btn-block remove-ingredient">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        $('#ingredients-container').append(html);
        $('.selectpicker').selectpicker('refresh');
    });

    // Remove ingredient
    $(document).on('click', '.remove-ingredient', function() {
        if ($('.ingredient-row').length > 1) {
            $(this).closest('.ingredient-row').remove();
            calculateNutrition();
            log('Ingrédient supprimé');
        } else {
            alert('Vous devez avoir au moins un ingrédient');
        }
    });

    // Update nutrition when ingredient or quantity changes
    $(document).on('change', '.food-select', function() {
        log('Aliment sélectionné', {
            value: $(this).val(),
            text: $(this).find('option:selected').text()
        });
        calculateNutrition();
    });

    $(document).on('input change', '.ingredient-quantity', function() {
        log('Quantité modifiée', { value: $(this).val() });
        calculateNutrition();
    });

    log('Tous les événements attachés');
});
</script>
