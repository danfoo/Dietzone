<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4><?php echo $title; ?></h4>
                        <hr />

                        <?php echo form_open($this->uri->uri_string()); ?>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="food_name"><?php echo _l('dietetic_food'); ?> (English) *</label>
                                    <input type="text" class="form-control" name="food_name" value="<?php echo isset($food) ? $food->food_name : ''; ?>" required />
                                </div>

                                <div class="form-group">
                                    <label for="food_name_fr"><?php echo _l('dietetic_food'); ?> (French)</label>
                                    <input type="text" class="form-control" name="food_name_fr" value="<?php echo isset($food) ? $food->food_name_fr : ''; ?>" />
                                </div>

                                <div class="form-group">
                                    <label for="category"><?php echo _l('category'); ?> *</label>
                                    <select name="category" class="form-control" required>
                                        <option value="">-- <?php echo _l('select'); ?> --</option>
                                        <?php foreach (dietetic_get_food_categories() as $key => $label) { ?>
                                            <option value="<?php echo $key; ?>" <?php echo set_select('category', $key, isset($food) && $food->category == $key); ?>>
                                                <?php echo $label; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="serving_size"><?php echo _l('dietetic_serving_size'); ?> *</label>
                                            <input type="number" step="0.1" class="form-control" name="serving_size" value="<?php echo isset($food) ? $food->serving_size : '100'; ?>" required />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="serving_unit"><?php echo _l('dietetic_unit'); ?></label>
                                            <input type="text" class="form-control" name="serving_unit" value="<?php echo isset($food) ? $food->serving_unit : 'g'; ?>" />
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="allergens"><?php echo _l('dietetic_allergens'); ?></label>
                                    <input type="text" class="form-control" name="allergens" value="<?php echo isset($food) ? $food->allergens : ''; ?>" placeholder="e.g., gluten, dairy, nuts" />
                                </div>

                                <div class="form-group">
                                    <label for="is_active"><?php echo _l('active'); ?></label>
                                    <select name="is_active" class="form-control">
                                        <option value="1" <?php echo set_select('is_active', '1', (isset($food) && $food->is_active == '1') || !isset($food)); ?>>Yes</option>
                                        <option value="0" <?php echo set_select('is_active', '0', isset($food) && $food->is_active == '0'); ?>>No</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h5><?php echo _l('dietetic_nutritional_information'); ?> (per serving)</h5>

                                <div class="form-group">
                                    <label for="calories"><?php echo _l('dietetic_calories'); ?> (kcal) *</label>
                                    <input type="number" step="0.1" class="form-control" name="calories" value="<?php echo isset($food) ? $food->calories : ''; ?>" required />
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="protein"><?php echo _l('dietetic_protein'); ?> (g)</label>
                                            <input type="number" step="0.1" class="form-control" name="protein" value="<?php echo isset($food) ? $food->protein : '0'; ?>" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="carbs"><?php echo _l('dietetic_carbs'); ?> (g)</label>
                                            <input type="number" step="0.1" class="form-control" name="carbs" value="<?php echo isset($food) ? $food->carbs : '0'; ?>" />
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="fats"><?php echo _l('dietetic_fats'); ?> (g)</label>
                                            <input type="number" step="0.1" class="form-control" name="fats" value="<?php echo isset($food) ? $food->fats : '0'; ?>" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="fiber"><?php echo _l('dietetic_fiber'); ?> (g)</label>
                                            <input type="number" step="0.1" class="form-control" name="fiber" value="<?php echo isset($food) ? $food->fiber : '0'; ?>" />
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="sugar"><?php echo _l('dietetic_sugar'); ?> (g)</label>
                                            <input type="number" step="0.1" class="form-control" name="sugar" value="<?php echo isset($food) ? $food->sugar : '0'; ?>" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="sodium"><?php echo _l('dietetic_sodium'); ?> (mg)</label>
                                            <input type="number" step="0.1" class="form-control" name="sodium" value="<?php echo isset($food) ? $food->sodium : '0'; ?>" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="btn-bottom-toolbar text-right">
                            <a href="<?php echo admin_url('dietetic/foods'); ?>" class="btn btn-default"><?php echo _l('cancel'); ?></a>
                            <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                        </div>

                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
