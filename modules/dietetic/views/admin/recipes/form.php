<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
.panel-heading {
    background: #e74c3c;
    color: white;
    font-weight: 600;
}

.ingredient-row, .instruction-row {
    margin-bottom: 10px;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 4px;
}

.btn-add-item {
    margin-top: 10px;
}

.tag-input {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    min-height: 45px;
}

.tag-item {
    background: #e74c3c;
    color: white;
    padding: 5px 12px;
    border-radius: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
}

.tag-item .remove-tag {
    cursor: pointer;
    font-weight: bold;
}

.suggested-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 10px;
}

.suggested-tag {
    background: #ecf0f1;
    color: #2c3e50;
    padding: 5px 12px;
    border-radius: 20px;
    cursor: pointer;
    font-size: 13px;
    transition: all 0.3s ease;
}

.suggested-tag:hover {
    background: #e74c3c;
    color: white;
}

.nutrition-readonly {
    background-color: #e9ecef;
    cursor: not-allowed;
}
</style>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <h4 class="tw-mt-0 tw-font-bold tw-text-xl tw-flex tw-items-center">
                    <i class="fa fa-book tw-mr-2"></i>
                    <?php echo isset($recipe) ? 'Modifier Recette' : 'Nouvelle Recette'; ?>
                </h4>

                <div class="panel_s">
                    <div class="panel-body">
                        <?php echo form_open_multipart(admin_url('dietetic/recipes/' . (isset($recipe) ? 'edit/' . $recipe->id : 'create'))); ?>

                        <!-- Informations principales -->
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <i class="fa fa-info-circle"></i> Informations Principales
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="name" class="control-label">
                                                Nom de la recette <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control" name="name" id="name"
                                                   value="<?php echo isset($recipe) ? htmlspecialchars($recipe->name) : ''; ?>"
                                                   required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="preparation_time" class="control-label">
                                                Temps de préparation (minutes)
                                            </label>
                                            <input type="number" class="form-control" name="preparation_time" id="preparation_time"
                                                   value="<?php echo isset($recipe) ? $recipe->preparation_time : ''; ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="category" class="control-label">
                                                Catégorie
                                            </label>
                                            <select class="form-control selectpicker" name="category" id="category">
                                                <option value="">-- Sélectionner --</option>
                                                <option value="breakfast" <?php echo (isset($recipe) && $recipe->category == 'breakfast') ? 'selected' : ''; ?>>
                                                    Petit-déjeuner
                                                </option>
                                                <option value="lunch" <?php echo (isset($recipe) && $recipe->category == 'lunch') ? 'selected' : ''; ?>>
                                                    Déjeuner
                                                </option>
                                                <option value="dinner" <?php echo (isset($recipe) && $recipe->category == 'dinner') ? 'selected' : ''; ?>>
                                                    Dîner
                                                </option>
                                                <option value="snack" <?php echo (isset($recipe) && $recipe->category == 'snack') ? 'selected' : ''; ?>>
                                                    Collation
                                                </option>
                                                <option value="smoothie" <?php echo (isset($recipe) && $recipe->category == 'smoothie') ? 'selected' : ''; ?>>
                                                    Smoothie
                                                </option>
                                                <option value="juice" <?php echo (isset($recipe) && $recipe->category == 'juice') ? 'selected' : ''; ?>>
                                                    Jus naturel
                                                </option>
                                                <option value="beverage" <?php echo (isset($recipe) && $recipe->category == 'beverage') ? 'selected' : ''; ?>>
                                                    Boisson
                                                </option>
                                                <option value="dessert" <?php echo (isset($recipe) && $recipe->category == 'dessert') ? 'selected' : ''; ?>>
                                                    Dessert
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="description" class="control-label">
                                        Description
                                    </label>
                                    <textarea class="form-control" name="description" id="description" rows="4"><?php echo isset($recipe) ? htmlspecialchars($recipe->description) : ''; ?></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Ingrédients -->
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <i class="fa fa-list"></i> Ingrédients de la bibliothèque alimentaire
                            </div>
                            <div class="panel-body">
                                <div id="ingredients-container">
                                    <?php if (isset($recipe) && !empty($recipe->ingredients)) : ?>
                                        <?php foreach ($recipe->ingredients as $index => $ingredient) : ?>
                                            <div class="ingredient-row">
                                                <div class="row">
                                                    <div class="col-md-5">
                                                        <select class="form-control selectpicker food-select" name="food_id[]" data-live-search="true" required>
                                                            <option value="">-- Sélectionner un aliment --</option>
                                                            <?php if (isset($foods) && !empty($foods)) : ?>
                                                                <?php foreach ($foods as $food) : ?>
                                                                    <option value="<?php echo $food->id; ?>"
                                                                            data-calories="<?php echo $food->calories; ?>"
                                                                            data-protein="<?php echo $food->protein; ?>"
                                                                            data-carbs="<?php echo $food->carbs; ?>"
                                                                            data-fat="<?php echo $food->fats; ?>"
                                                                            <?php echo $food->food_name == $ingredient->ingredient_name ? 'selected' : ''; ?>>
                                                                        <?php echo htmlspecialchars($food->food_name); ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            <?php endif; ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" step="0.01" class="form-control ingredient-quantity" name="ingredient_quantity[]"
                                                               placeholder="Quantité (g)" value="<?php echo $ingredient->quantity; ?>" required>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="text" class="form-control" name="ingredient_unit[]"
                                                               placeholder="Unité" value="<?php echo htmlspecialchars($ingredient->unit); ?>" readonly>
                                                    </div>
                                                    <div class="col-md-1">
                                                        <button type="button" class="btn btn-danger btn-sm remove-ingredient">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <div class="ingredient-row">
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <select class="form-control selectpicker food-select" name="food_id[]" data-live-search="true" required>
                                                        <option value="">-- Sélectionner un aliment --</option>
                                                        <?php if (isset($foods) && !empty($foods)) : ?>
                                                            <?php foreach ($foods as $food) : ?>
                                                                <option value="<?php echo $food->id; ?>"
                                                                        data-calories="<?php echo $food->calories; ?>"
                                                                        data-protein="<?php echo $food->protein; ?>"
                                                                        data-carbs="<?php echo $food->carbs; ?>"
                                                                        data-fat="<?php echo $food->fats; ?>">
                                                                    <?php echo htmlspecialchars($food->food_name); ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="number" step="0.01" class="form-control ingredient-quantity" name="ingredient_quantity[]"
                                                           placeholder="Quantité (g)" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="text" class="form-control" name="ingredient_unit[]" placeholder="g" value="g" readonly>
                                                </div>
                                                <div class="col-md-1">
                                                    <button type="button" class="btn btn-danger btn-sm remove-ingredient">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <button type="button" class="btn btn-info btn-sm btn-add-item" id="add-ingredient">
                                    <i class="fa fa-plus"></i> Ajouter un ingrédient
                                </button>
                            </div>
                        </div>

                        <!-- Instructions -->
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <i class="fa fa-tasks"></i> Instructions de Préparation
                            </div>
                            <div class="panel-body">
                                <div id="instructions-container">
                                    <?php if (isset($recipe) && !empty($recipe->instructions)) : ?>
                                        <?php foreach ($recipe->instructions as $index => $instruction) : ?>
                                            <div class="instruction-row">
                                                <div class="row">
                                                    <div class="col-md-1">
                                                        <strong class="step-number">Étape <?php echo $index + 1; ?></strong>
                                                    </div>
                                                    <div class="col-md-10">
                                                        <textarea class="form-control" name="instruction[]" rows="2" required><?php echo htmlspecialchars($instruction->instruction); ?></textarea>
                                                    </div>
                                                    <div class="col-md-1">
                                                        <button type="button" class="btn btn-danger btn-sm remove-instruction">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <div class="instruction-row">
                                            <div class="row">
                                                <div class="col-md-1">
                                                    <strong class="step-number">Étape 1</strong>
                                                </div>
                                                <div class="col-md-10">
                                                    <textarea class="form-control" name="instruction[]" rows="2" required></textarea>
                                                </div>
                                                <div class="col-md-1">
                                                    <button type="button" class="btn btn-danger btn-sm remove-instruction">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <button type="button" class="btn btn-info btn-sm btn-add-item" id="add-instruction">
                                    <i class="fa fa-plus"></i> Ajouter une étape
                                </button>
                            </div>
                        </div>

                        <!-- Informations Nutritionnelles (calculées automatiquement) -->
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <i class="fa fa-heartbeat"></i> Informations Nutritionnelles (calculées automatiquement)
                            </div>
                            <div class="panel-body">
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle"></i> Les valeurs nutritionnelles sont calculées automatiquement en fonction des ingrédients sélectionnés.
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Calories (kcal)</label>
                                            <input type="number" step="0.1" class="form-control nutrition-readonly" name="calories" id="calc-calories" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Protéines (g)</label>
                                            <input type="number" step="0.1" class="form-control nutrition-readonly" name="protein" id="calc-protein" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Glucides (g)</label>
                                            <input type="number" step="0.1" class="form-control nutrition-readonly" name="carbs" id="calc-carbs" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Lipides (g)</label>
                                            <input type="number" step="0.1" class="form-control nutrition-readonly" name="fat" id="calc-fat" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tags -->
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <i class="fa fa-tags"></i> Tags / Étiquettes
                            </div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <label>Tags</label>
                                    <div class="tag-input" id="tag-input">
                                        <?php if (isset($recipe) && !empty($recipe->tags)) : ?>
                                            <?php foreach ($recipe->tags as $tag) : ?>
                                                <div class="tag-item">
                                                    <span><?php echo htmlspecialchars($tag); ?></span>
                                                    <span class="remove-tag">×</span>
                                                    <input type="hidden" name="tags[]" value="<?php echo htmlspecialchars($tag); ?>">
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                    <input type="text" class="form-control" id="new-tag-input" placeholder="Ajouter un tag (Entrée pour valider)" style="margin-top: 10px;">

                                    <div class="suggested-tags">
                                        <strong style="width: 100%; margin-bottom: 10px;">Suggestions :</strong>
                                        <span class="suggested-tag">Végétarien</span>
                                        <span class="suggested-tag">Sans gluten</span>
                                        <span class="suggested-tag">Sans lactose</span>
                                        <span class="suggested-tag">Faible en calories</span>
                                        <span class="suggested-tag">Riche en protéines</span>
                                        <span class="suggested-tag">Sans sucre</span>
                                        <span class="suggested-tag">Rapide</span>
                                        <span class="suggested-tag">Facile</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Photos -->
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <i class="fa fa-camera"></i> Photos
                            </div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <label>Ajouter des photos</label>
                                    <input type="file" name="photos[]" class="form-control" accept="image/*" multiple>
                                    <small class="text-muted">La première photo sera la photo principale. Formats acceptés : JPG, PNG, GIF (max 5MB par photo)</small>
                                </div>

                                <?php if (isset($recipe) && !empty($recipe->photos)) : ?>
                                    <div class="row">
                                        <?php foreach ($recipe->photos as $photo) : ?>
                                            <div class="col-md-3">
                                                <div class="thumbnail">
                                                    <img src="<?php echo base_url($photo->photo_url); ?>" alt="Photo">
                                                    <?php if ($photo->is_main) : ?>
                                                        <span class="label label-success">Photo principale</span>
                                                    <?php endif; ?>
                                                    <button type="button" class="btn btn-danger btn-xs btn-block delete-photo"
                                                            data-photo-id="<?php echo $photo->id; ?>">
                                                        <i class="fa fa-trash"></i> Supprimer
                                                    </button>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="form-group text-right">
                            <a href="<?php echo admin_url('dietetic/recipes'); ?>" class="btn btn-default">
                                <i class="fa fa-times"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check"></i> <?php echo isset($recipe) ? 'Mettre à jour' : 'Créer la recette'; ?>
                            </button>
                        </div>

                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Store all foods data in JavaScript for dynamic ingredient rows
var foodsData = <?php echo json_encode(isset($foods) ? $foods : []); ?>;

$(document).ready(function() {
    // Initialize selectpicker
    $('.selectpicker').selectpicker('refresh');

    // Calculate nutrition automatically
    function calculateNutrition() {
        let totalCalories = 0;
        let totalProtein = 0;
        let totalCarbs = 0;
        let totalFat = 0;

        $('.ingredient-row').each(function() {
            const select = $(this).find('.food-select');
            const quantity = parseFloat($(this).find('.ingredient-quantity').val()) || 0;

            if (select.val()) {
                const option = select.find('option:selected');
                const calories = parseFloat(option.data('calories')) || 0;
                const protein = parseFloat(option.data('protein')) || 0;
                const carbs = parseFloat(option.data('carbs')) || 0;
                const fat = parseFloat(option.data('fat')) || 0;

                // Calculate based on quantity (per 100g)
                totalCalories += (calories * quantity) / 100;
                totalProtein += (protein * quantity) / 100;
                totalCarbs += (carbs * quantity) / 100;
                totalFat += (fat * quantity) / 100;
            }
        });

        $('#calc-calories').val(totalCalories.toFixed(1));
        $('#calc-protein').val(totalProtein.toFixed(1));
        $('#calc-carbs').val(totalCarbs.toFixed(1));
        $('#calc-fat').val(totalFat.toFixed(1));
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
                    escapeHtml(food.food_name) +
                    '</option>';
            });
        }

        return optionsHtml;
    }

    // Escape HTML to prevent XSS
    function escapeHtml(text) {
        var map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    // Add ingredient
    $('#add-ingredient').click(function() {
        const html = `
            <div class="ingredient-row">
                <div class="row">
                    <div class="col-md-5">
                        <select class="form-control selectpicker food-select" name="food_id[]" data-live-search="true" required>
                            ${generateFoodOptions()}
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="number" step="0.01" class="form-control ingredient-quantity" name="ingredient_quantity[]"
                               placeholder="Quantité (g)" required>
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="ingredient_unit[]" value="g" readonly>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger btn-sm remove-ingredient">
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
        } else {
            alert('Vous devez avoir au moins un ingrédient');
        }
    });

    // Update nutrition when ingredient or quantity changes
    $(document).on('change', '.food-select, .ingredient-quantity', function() {
        calculateNutrition();
    });

    // Calculate on page load if editing
    <?php if (isset($recipe)) : ?>
    setTimeout(calculateNutrition, 500);
    <?php endif; ?>

    // Add instruction
    $('#add-instruction').click(function() {
        const stepNumber = $('.instruction-row').length + 1;
        const html = `
            <div class="instruction-row">
                <div class="row">
                    <div class="col-md-1">
                        <strong class="step-number">Étape ${stepNumber}</strong>
                    </div>
                    <div class="col-md-10">
                        <textarea class="form-control" name="instruction[]" rows="2" required></textarea>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger btn-sm remove-instruction">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        $('#instructions-container').append(html);
    });

    // Remove instruction
    $(document).on('click', '.remove-instruction', function() {
        if ($('.instruction-row').length > 1) {
            $(this).closest('.instruction-row').remove();
            updateInstructionNumbers();
        } else {
            alert('Vous devez avoir au moins une étape');
        }
    });

    // Update instruction numbers
    function updateInstructionNumbers() {
        $('.instruction-row').each(function(index) {
            $(this).find('.step-number').text('Étape ' + (index + 1));
        });
    }

    // Tags management
    function addTag(tagName) {
        // Check if tag already exists
        const exists = $('input[name="tags[]"][value="' + tagName + '"]').length > 0;
        if (exists) {
            return;
        }

        const html = `
            <div class="tag-item">
                <span>${tagName}</span>
                <span class="remove-tag">×</span>
                <input type="hidden" name="tags[]" value="${tagName}">
            </div>
        `;
        $('#tag-input').append(html);
    }

    // Add tag from input
    $('#new-tag-input').keypress(function(e) {
        if (e.which === 13) {
            e.preventDefault();
            const tagName = $(this).val().trim();
            if (tagName) {
                addTag(tagName);
                $(this).val('');
            }
        }
    });

    // Add suggested tag
    $(document).on('click', '.suggested-tag', function() {
        const tagName = $(this).text();
        addTag(tagName);
    });

    // Remove tag
    $(document).on('click', '.remove-tag', function() {
        $(this).closest('.tag-item').remove();
    });

    // Delete photo
    $('.delete-photo').click(function() {
        if (!confirm('Supprimer cette photo ?')) {
            return;
        }

        const photoId = $(this).data('photo-id');
        const btn = $(this);

        $.post('<?php echo admin_url('dietetic/recipes/delete_photo/'); ?>' + photoId, function(response) {
            const data = JSON.parse(response);
            if (data.success) {
                btn.closest('.col-md-3').remove();
                alert_float('success', data.message);
            } else {
                alert_float('danger', data.message);
            }
        });
    });
});
</script>

<?php init_tail(); ?>
