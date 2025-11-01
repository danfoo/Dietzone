<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $this->load->view('authentication/includes/head'); ?>

<div class="container">
    <div class="row mtop30">
        <div class="col-md-12">
            <h3><?php echo _l('dietetic_my_measurements'); ?></h3>
            <hr />

            <div class="row">
                <div class="col-md-8">
                    <div class="panel panel-default">
                        <div class="panel-heading"><?php echo _l('dietetic_measurements'); ?></div>
                        <div class="panel-body">
                            <?php if (!empty($measurements)) { ?>
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th><?php echo _l('dietetic_date'); ?></th>
                                            <th><?php echo _l('dietetic_weight'); ?></th>
                                            <th><?php echo _l('dietetic_bmi'); ?></th>
                                            <th><?php echo _l('dietetic_body_fat'); ?></th>
                                            <th><?php echo _l('dietetic_notes'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($measurements as $measurement) { ?>
                                            <tr>
                                                <td><?php echo _d($measurement->measurement_date); ?></td>
                                                <td><?php echo $measurement->weight ? $measurement->weight . ' kg' : '-'; ?></td>
                                                <td><?php echo $measurement->bmi ?? '-'; ?></td>
                                                <td><?php echo $measurement->body_fat ? $measurement->body_fat . '%' : '-'; ?></td>
                                                <td><?php echo $measurement->notes ?? '-'; ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            <?php } else { ?>
                                <p class="text-muted"><?php echo _l('dietetic_no_data'); ?></p>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="panel panel-default mtop30">
                        <div class="panel-heading"><?php echo _l('dietetic_weight_evolution'); ?></div>
                        <div class="panel-body">
                            <canvas id="weightEvolutionChart" height="100"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="panel panel-info">
                        <div class="panel-heading"><?php echo _l('dietetic_add_measurement'); ?></div>
                        <div class="panel-body">
                            <form id="portal_add_measurement_form">
                                <div class="form-group">
                                    <label><?php echo _l('dietetic_date'); ?></label>
                                    <input type="date" class="form-control" name="measurement_date" value="<?php echo date('Y-m-d'); ?>" required />
                                </div>

                                <div class="form-group">
                                    <label><?php echo _l('dietetic_weight'); ?> (kg) *</label>
                                    <input type="number" step="0.1" class="form-control" name="weight" required />
                                </div>

                                <div class="form-group">
                                    <label><?php echo _l('dietetic_body_fat'); ?> (%)</label>
                                    <input type="number" step="0.1" class="form-control" name="body_fat" />
                                </div>

                                <div class="form-group">
                                    <label><?php echo _l('dietetic_waist'); ?> (cm)</label>
                                    <input type="number" step="0.1" class="form-control" name="waist" />
                                </div>

                                <div class="form-group">
                                    <label><?php echo _l('dietetic_notes'); ?></label>
                                    <textarea class="form-control" name="notes" rows="3"></textarea>
                                </div>

                                <button type="button" onclick="dietetic_portal.addMeasurement()" class="btn btn-info btn-block">
                                    <i class="fa fa-plus"></i> <?php echo _l('submit'); ?>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mtop30">
                <a href="<?php echo site_url('dietetic/portal'); ?>" class="btn btn-default">
                    <i class="fa fa-arrow-left"></i> <?php echo _l('back'); ?>
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(function() {
    <?php if (!empty($weight_evolution)) { ?>
        var labels = [<?php foreach ($weight_evolution as $m) { echo '"' . _d($m->measurement_date) . '",'; } ?>];
        var weights = [<?php foreach ($weight_evolution as $m) { echo $m->weight . ','; } ?>];
        var bmis = [<?php foreach ($weight_evolution as $m) { echo ($m->bmi ?? 0) . ','; } ?>];

        if (typeof dietetic_portal !== 'undefined') {
            dietetic_portal.loadWeightChart('weightEvolutionChart', weights, bmis, labels);
        }
    <?php } ?>
});
</script>

<?php $this->load->view('authentication/includes/footer'); ?>
