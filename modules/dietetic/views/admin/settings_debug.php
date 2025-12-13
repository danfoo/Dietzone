<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4>Debug - Payment Settings</h4>
                        <hr />

                        <?php
                        $CI = &get_instance();

                        echo "<h5>All Settings in Database</h5>";
                        $all_settings = $CI->db->select('*')
                            ->from(db_prefix() . 'dietic_settings')
                            ->order_by('setting_key', 'ASC')
                            ->get()
                            ->result();

                        echo "<table class='table table-bordered table-striped'>";
                        echo "<thead><tr><th>ID</th><th>Setting Key</th><th>Value</th><th>Type</th><th>Description</th></tr></thead>";
                        echo "<tbody>";
                        foreach ($all_settings as $s) {
                            echo "<tr>";
                            echo "<td>" . $s->id . "</td>";
                            echo "<td><strong>" . htmlspecialchars($s->setting_key) . "</strong></td>";
                            echo "<td>" . htmlspecialchars($s->setting_value) . "</td>";
                            echo "<td>" . htmlspecialchars($s->setting_type) . "</td>";
                            echo "<td>" . htmlspecialchars($s->description) . "</td>";
                            echo "</tr>";
                        }
                        echo "</tbody></table>";

                        echo "<hr />";
                        echo "<h5>Wave Settings</h5>";
                        $wave_settings = $CI->db->select('*')
                            ->from(db_prefix() . 'dietic_settings')
                            ->like('setting_key', 'wave_', 'after')
                            ->get()
                            ->result();

                        if (empty($wave_settings)) {
                            echo "<p style='color: red;'>❌ No Wave settings found!</p>";
                        } else {
                            echo "<ul>";
                            foreach ($wave_settings as $s) {
                                echo "<li><strong>" . $s->setting_key . "</strong> = " . $s->setting_value . " (Type: " . $s->setting_type . ")</li>";
                            }
                            echo "</ul>";
                        }

                        echo "<hr />";
                        echo "<h5>PayPal Settings</h5>";
                        $paypal_settings = $CI->db->select('*')
                            ->from(db_prefix() . 'dietic_settings')
                            ->like('setting_key', 'paypal_', 'after')
                            ->get()
                            ->result();

                        if (empty($paypal_settings)) {
                            echo "<p style='color: red;'>❌ No PayPal settings found!</p>";
                        } else {
                            echo "<ul>";
                            foreach ($paypal_settings as $s) {
                                echo "<li><strong>" . $s->setting_key . "</strong> = " . $s->setting_value . " (Type: " . $s->setting_type . ")</li>";
                            }
                            echo "</ul>";
                        }

                        echo "<hr />";
                        echo "<h5>Orange Money Settings</h5>";
                        $om_settings = $CI->db->select('*')
                            ->from(db_prefix() . 'dietic_settings')
                            ->like('setting_key', 'orange_money_', 'after')
                            ->get()
                            ->result();

                        if (empty($om_settings)) {
                            echo "<p style='color: red;'>❌ No Orange Money settings found!</p>";
                        } else {
                            echo "<ul>";
                            foreach ($om_settings as $s) {
                                echo "<li><strong>" . $s->setting_key . "</strong> = " . $s->setting_value . " (Type: " . $s->setting_type . ")</li>";
                            }
                            echo "</ul>";
                        }
                        ?>

                        <hr />
                        <a href="<?php echo admin_url('dietetic/settings'); ?>" class="btn btn-primary">Go to Settings Page</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
