<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4><?php echo $title; ?></h4>
                        <hr />

                        <div class="alert alert-info">
                            <h5>CSV Format</h5>
                            <p>Your CSV file should have the following columns in this order:</p>
                            <ol>
                                <li>Food Name (English)</li>
                                <li>Food Name (French)</li>
                                <li>Category</li>
                                <li>Serving Size</li>
                                <li>Serving Unit</li>
                                <li>Calories</li>
                                <li>Protein (g)</li>
                                <li>Carbs (g)</li>
                                <li>Fats (g)</li>
                                <li>Fiber (g)</li>
                                <li>Sugar (g)</li>
                                <li>Sodium (mg)</li>
                            </ol>
                        </div>

                        <?php echo form_open_multipart($this->uri->uri_string()); ?>

                        <div class="form-group">
                            <label for="csv_file">CSV File *</label>
                            <input type="file" class="form-control" name="csv_file" accept=".csv" required />
                            <small class="text-muted">Maximum file size: 2MB</small>
                        </div>

                        <div class="btn-bottom-toolbar text-right">
                            <a href="<?php echo admin_url('dietetic/foods'); ?>" class="btn btn-default"><?php echo _l('cancel'); ?></a>
                            <button type="submit" class="btn btn-info">
                                <i class="fa fa-upload"></i> <?php echo _l('import'); ?>
                            </button>
                        </div>

                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
