<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4><i class="fa fa-broom"></i> Cleanup Duplicate Payment Settings</h4>
                        <hr />

                        <?php
                        $CI = &get_instance();

                        // Check if cleanup is requested
                        if (isset($_GET['action']) && $_GET['action'] == 'cleanup') {
                            echo "<div class='alert alert-info'><strong>Cleaning up...</strong></div>";

                            // Delete duplicate settings
                            $duplicates = [
                                'payment_wave_enabled',
                                'payment_wave_api_key',
                                'payment_wave_secret_key',
                                'payment_wave_merchant_id',
                                'payment_wave_currency',
                                'payment_paypal_enabled',
                                'payment_paypal_client_id',
                                'payment_paypal_secret_key',
                                'payment_paypal_mode',
                                'payment_paypal_currency'
                            ];

                            $deleted_count = 0;
                            foreach ($duplicates as $key) {
                                $CI->db->where('setting_key', $key);
                                if ($CI->db->delete(db_prefix() . 'dietic_settings')) {
                                    if ($CI->db->affected_rows() > 0) {
                                        $deleted_count++;
                                        echo "<p class='text-success'>✓ Deleted: <strong>$key</strong></p>";
                                    }
                                }
                            }

                            echo "<div class='alert alert-success mtop20'>";
                            echo "<strong>✓ Cleanup completed!</strong><br>";
                            echo "Deleted $deleted_count duplicate settings.";
                            echo "</div>";

                            echo "<a href='" . admin_url('dietetic/cleanup_payment_settings') . "' class='btn btn-info'>Refresh Page</a> ";
                            echo "<a href='" . admin_url('dietetic/settings') . "' class='btn btn-primary'>Go to Settings</a>";
                        } else {
                            // Show duplicates
                            echo "<h5>Duplicate Settings Found</h5>";
                            echo "<p class='text-muted'>These settings have the 'payment_' prefix and are duplicates. They can be safely removed.</p>";

                            $duplicates_check = $CI->db->select('*')
                                ->from(db_prefix() . 'dietic_settings')
                                ->like('setting_key', 'payment_', 'after')
                                ->get()
                                ->result();

                            if (empty($duplicates_check)) {
                                echo "<div class='alert alert-success'>";
                                echo "<strong>✓ No duplicates found!</strong> Your database is clean.";
                                echo "</div>";
                                echo "<a href='" . admin_url('dietetic/settings') . "' class='btn btn-primary'>Go to Settings</a>";
                            } else {
                                echo "<table class='table table-bordered table-striped'>";
                                echo "<thead><tr><th>Setting Key</th><th>Value</th><th>Type</th></tr></thead>";
                                echo "<tbody>";
                                foreach ($duplicates_check as $dup) {
                                    echo "<tr>";
                                    echo "<td><code>" . htmlspecialchars($dup->setting_key) . "</code></td>";
                                    echo "<td>" . htmlspecialchars($dup->setting_value) . "</td>";
                                    echo "<td>" . htmlspecialchars($dup->setting_type) . "</td>";
                                    echo "</tr>";
                                }
                                echo "</tbody></table>";

                                echo "<div class='alert alert-warning mtop20'>";
                                echo "<strong>⚠️ Warning:</strong> This will permanently delete " . count($duplicates_check) . " duplicate settings.";
                                echo "</div>";

                                echo "<a href='" . admin_url('dietetic/cleanup_payment_settings?action=cleanup') . "' class='btn btn-danger' onclick='return confirm(\"Are you sure you want to delete these duplicate settings?\");'>";
                                echo "<i class='fa fa-trash'></i> Delete Duplicates";
                                echo "</a> ";
                                echo "<a href='" . admin_url('dietetic/settings') . "' class='btn btn-default'>Cancel</a>";
                            }
                        }
                        ?>

                        <hr class="mtop30" />

                        <h5>Valid Payment Settings (Will NOT be deleted)</h5>
                        <p class='text-muted'>These are the correct settings that the system uses.</p>

                        <?php
                        $valid_settings = $CI->db->select('*')
                            ->from(db_prefix() . 'dietic_settings')
                            ->where('(setting_key LIKE "wave_%" OR setting_key LIKE "paypal_%" OR setting_key LIKE "orange_money_%")', NULL, FALSE)
                            ->where('setting_key NOT LIKE "payment_%"', NULL, FALSE)
                            ->get()
                            ->result();

                        if (!empty($valid_settings)) {
                            echo "<table class='table table-bordered'>";
                            echo "<thead><tr><th>Setting Key</th><th>Value</th><th>Type</th><th>Description</th></tr></thead>";
                            echo "<tbody>";
                            foreach ($valid_settings as $setting) {
                                echo "<tr>";
                                echo "<td><strong>" . htmlspecialchars($setting->setting_key) . "</strong></td>";
                                echo "<td>" . htmlspecialchars($setting->setting_value) . "</td>";
                                echo "<td>" . htmlspecialchars($setting->setting_type) . "</td>";
                                echo "<td><small>" . htmlspecialchars($setting->description) . "</small></td>";
                                echo "</tr>";
                            }
                            echo "</tbody></table>";
                        } else {
                            echo "<div class='alert alert-danger'>";
                            echo "<strong>⚠️ Error:</strong> No valid payment settings found! Please run the migration first.";
                            echo "</div>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
