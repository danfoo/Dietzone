<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h4><i class="fa fa-cog"></i> Paramètres du Module Dietetic</h4>
                            </div>
                            <div class="col-md-4 text-right">
                                <a href="<?php echo admin_url('dietetic/settings_debug'); ?>" class="btn btn-default btn-sm" target="_blank">
                                    <i class="fa fa-bug"></i> Debug Settings
                                </a>
                            </div>
                        </div>
                        <hr />

                        <?php if (isset($_SESSION['alert_message'])) { ?>
                            <div class="alert alert-<?php echo $_SESSION['alert_type']; ?> alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                <?php echo $_SESSION['alert_message']; ?>
                            </div>
                            <?php unset($_SESSION['alert_message'], $_SESSION['alert_type']); ?>
                        <?php } ?>

                        <?php echo form_open($this->uri->uri_string()); ?>

                        <!-- ============================================ -->
                        <!-- PAYMENT GATEWAYS CONFIGURATION -->
                        <!-- ============================================ -->
                        <div class="panel panel-primary" style="border-color: #01807B;">
                            <div class="panel-heading" style="background: linear-gradient(135deg, #01807B 0%, #019d96 100%); border-color: #01807B;">
                                <h3 class="panel-title" style="color: white; font-size: 16px;">
                                    <i class="fa fa-credit-card"></i> Payment Gateways Configuration
                                </h3>
                            </div>
                            <div class="panel-body">
                                <p class="text-muted">
                                    Configure payment methods for service subscriptions. Enable the gateways you want to use and enter the required API credentials.
                                </p>

                                <!-- Wave Payment Gateway -->
                                <div class="mtop20">
                                    <h4 style="border-bottom: 2px solid #01807B; padding-bottom: 10px; color: #01807B;">
                                        <i class="fa fa-credit-card"></i> Wave Payment Gateway
                                    </h4>

                                    <?php
                                    $wave_settings = [];
                                    foreach ($settings as $s) {
                                        if (strpos($s->setting_key, 'wave_') === 0) {
                                            $wave_settings[] = $s;
                                        }
                                    }

                                    if (empty($wave_settings)) {
                                        echo '<div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> Aucun paramètre Wave trouvé dans la base de données.</div>';
                                    } else {
                                        foreach ($wave_settings as $setting) {
                                            $label = str_replace('wave_', '', $setting->setting_key);
                                            $label = ucwords(str_replace('_', ' ', $label));
                                            ?>
                                            <div class="form-group">
                                                <label for="<?php echo $setting->setting_key; ?>">
                                                    <strong><?php echo $label; ?></strong>
                                                </label>
                                                <?php if ($setting->setting_type == 'boolean') { ?>
                                                    <select name="<?php echo $setting->setting_key; ?>" class="form-control">
                                                        <option value="0" <?php echo $setting->setting_value == '0' ? 'selected' : ''; ?>>Désactivé</option>
                                                        <option value="1" <?php echo $setting->setting_value == '1' ? 'selected' : ''; ?>>Activé</option>
                                                    </select>
                                                <?php } else { ?>
                                                    <input type="<?php echo (strpos($setting->setting_key, 'key') !== false || strpos($setting->setting_key, 'secret') !== false) ? 'password' : 'text'; ?>"
                                                           name="<?php echo $setting->setting_key; ?>"
                                                           class="form-control"
                                                           value="<?php echo htmlspecialchars($setting->setting_value); ?>"
                                                           placeholder="<?php echo $label; ?>" />
                                                <?php } ?>
                                                <?php if (!empty($setting->description)) { ?>
                                                    <small class="text-muted"><i class="fa fa-info-circle"></i> <?php echo $setting->description; ?></small>
                                                <?php } ?>
                                            </div>
                                            <?php
                                        }
                                    }
                                    ?>
                                </div>

                                <!-- PayPal Payment Gateway -->
                                <div class="mtop30">
                                    <h4 style="border-bottom: 2px solid #0070ba; padding-bottom: 10px; color: #0070ba;">
                                        <i class="fa fa-paypal"></i> PayPal Payment Gateway
                                    </h4>

                                    <?php
                                    $paypal_settings = [];
                                    foreach ($settings as $s) {
                                        if (strpos($s->setting_key, 'paypal_') === 0) {
                                            $paypal_settings[] = $s;
                                        }
                                    }

                                    if (empty($paypal_settings)) {
                                        echo '<div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> Aucun paramètre PayPal trouvé dans la base de données.</div>';
                                    } else {
                                        foreach ($paypal_settings as $setting) {
                                            $label = str_replace('paypal_', '', $setting->setting_key);
                                            $label = ucwords(str_replace('_', ' ', $label));
                                            ?>
                                            <div class="form-group">
                                                <label for="<?php echo $setting->setting_key; ?>">
                                                    <strong><?php echo $label; ?></strong>
                                                </label>
                                                <?php if ($setting->setting_type == 'boolean') { ?>
                                                    <select name="<?php echo $setting->setting_key; ?>" class="form-control">
                                                        <option value="0" <?php echo $setting->setting_value == '0' ? 'selected' : ''; ?>>Désactivé</option>
                                                        <option value="1" <?php echo $setting->setting_value == '1' ? 'selected' : ''; ?>>Activé</option>
                                                    </select>
                                                <?php } elseif ($setting->setting_key == 'paypal_mode') { ?>
                                                    <select name="<?php echo $setting->setting_key; ?>" class="form-control">
                                                        <option value="sandbox" <?php echo $setting->setting_value == 'sandbox' ? 'selected' : ''; ?>>Sandbox (Test)</option>
                                                        <option value="live" <?php echo $setting->setting_value == 'live' ? 'selected' : ''; ?>>Live (Production)</option>
                                                    </select>
                                                <?php } else { ?>
                                                    <input type="<?php echo (strpos($setting->setting_key, 'secret') !== false || strpos($setting->setting_key, 'client_id') !== false) ? 'password' : 'text'; ?>"
                                                           name="<?php echo $setting->setting_key; ?>"
                                                           class="form-control"
                                                           value="<?php echo htmlspecialchars($setting->setting_value); ?>"
                                                           placeholder="<?php echo $label; ?>" />
                                                <?php } ?>
                                                <?php if (!empty($setting->description)) { ?>
                                                    <small class="text-muted"><i class="fa fa-info-circle"></i> <?php echo $setting->description; ?></small>
                                                <?php } ?>
                                            </div>
                                            <?php
                                        }
                                    }
                                    ?>
                                </div>

                                <!-- Orange Money Payment Gateway -->
                                <div class="mtop30">
                                    <h4 style="border-bottom: 2px solid #ff7900; padding-bottom: 10px; color: #ff7900;">
                                        <i class="fa fa-mobile"></i> Orange Money Payment Gateway
                                    </h4>

                                    <?php
                                    $om_settings = [];
                                    foreach ($settings as $s) {
                                        if (strpos($s->setting_key, 'orange_money_') === 0) {
                                            $om_settings[] = $s;
                                        }
                                    }

                                    if (empty($om_settings)) {
                                        echo '<div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> Aucun paramètre Orange Money trouvé dans la base de données.</div>';
                                    } else {
                                        foreach ($om_settings as $setting) {
                                            $label = str_replace('orange_money_', '', $setting->setting_key);
                                            $label = ucwords(str_replace('_', ' ', $label));
                                            ?>
                                            <div class="form-group">
                                                <label for="<?php echo $setting->setting_key; ?>">
                                                    <strong><?php echo $label; ?></strong>
                                                </label>
                                                <?php if ($setting->setting_type == 'boolean') { ?>
                                                    <select name="<?php echo $setting->setting_key; ?>" class="form-control">
                                                        <option value="0" <?php echo $setting->setting_value == '0' ? 'selected' : ''; ?>>Désactivé</option>
                                                        <option value="1" <?php echo $setting->setting_value == '1' ? 'selected' : ''; ?>>Activé</option>
                                                    </select>
                                                <?php } else { ?>
                                                    <input type="<?php echo (strpos($setting->setting_key, 'key') !== false) ? 'password' : 'text'; ?>"
                                                           name="<?php echo $setting->setting_key; ?>"
                                                           class="form-control"
                                                           value="<?php echo htmlspecialchars($setting->setting_value); ?>"
                                                           placeholder="<?php echo $label; ?>" />
                                                <?php } ?>
                                                <?php if (!empty($setting->description)) { ?>
                                                    <small class="text-muted"><i class="fa fa-info-circle"></i> <?php echo $setting->description; ?></small>
                                                <?php } ?>
                                            </div>
                                            <?php
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>

                        <!-- ============================================ -->
                        <!-- SMS INTEGRATION -->
                        <!-- ============================================ -->
                        <div class="panel panel-default mtop30">
                            <div class="panel-heading">
                                <h3 class="panel-title"><i class="fa fa-mobile"></i> SMS Integration (LAM API)</h3>
                            </div>
                            <div class="panel-body">
                                <?php
                                $lam_settings = [];
                                foreach ($settings as $s) {
                                    if (strpos($s->setting_key, 'lam_api') === 0) {
                                        $lam_settings[] = $s;
                                    }
                                }

                                foreach ($lam_settings as $setting) {
                                    ?>
                                    <div class="form-group">
                                        <label for="<?php echo $setting->setting_key; ?>">
                                            <?php echo _l('dietetic_setting_' . $setting->setting_key); ?>
                                        </label>
                                        <?php if ($setting->setting_type == 'boolean') { ?>
                                            <select name="<?php echo $setting->setting_key; ?>" class="form-control">
                                                <option value="0" <?php echo $setting->setting_value == '0' ? 'selected' : ''; ?>>No</option>
                                                <option value="1" <?php echo $setting->setting_value == '1' ? 'selected' : ''; ?>>Yes</option>
                                            </select>
                                        <?php } else { ?>
                                            <input type="<?php echo $setting->setting_type == 'number' ? 'number' : 'text'; ?>"
                                                   name="<?php echo $setting->setting_key; ?>"
                                                   class="form-control"
                                                   value="<?php echo htmlspecialchars($setting->setting_value); ?>" />
                                        <?php } ?>
                                        <?php if (!empty($setting->description)) { ?>
                                            <small class="text-muted"><?php echo $setting->description; ?></small>
                                        <?php } ?>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>

                        <!-- ============================================ -->
                        <!-- REMINDERS -->
                        <!-- ============================================ -->
                        <div class="panel panel-default mtop30">
                            <div class="panel-heading">
                                <h3 class="panel-title"><i class="fa fa-bell"></i> Reminders & Notifications</h3>
                            </div>
                            <div class="panel-body">
                                <?php
                                $reminder_settings = [];
                                foreach ($settings as $s) {
                                    if (strpos($s->setting_key, 'reminder_') === 0) {
                                        $reminder_settings[] = $s;
                                    }
                                }

                                foreach ($reminder_settings as $setting) {
                                    ?>
                                    <div class="form-group">
                                        <label for="<?php echo $setting->setting_key; ?>">
                                            <?php echo _l('dietetic_setting_' . $setting->setting_key); ?>
                                        </label>
                                        <?php if ($setting->setting_type == 'boolean') { ?>
                                            <select name="<?php echo $setting->setting_key; ?>" class="form-control">
                                                <option value="0" <?php echo $setting->setting_value == '0' ? 'selected' : ''; ?>>No</option>
                                                <option value="1" <?php echo $setting->setting_value == '1' ? 'selected' : ''; ?>>Yes</option>
                                            </select>
                                        <?php } else { ?>
                                            <input type="<?php echo $setting->setting_type == 'number' ? 'number' : 'text'; ?>"
                                                   name="<?php echo $setting->setting_key; ?>"
                                                   class="form-control"
                                                   value="<?php echo htmlspecialchars($setting->setting_value); ?>" />
                                        <?php } ?>
                                        <?php if (!empty($setting->description)) { ?>
                                            <small class="text-muted"><?php echo $setting->description; ?></small>
                                        <?php } ?>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>

                        <!-- ============================================ -->
                        <!-- GENERAL SETTINGS -->
                        <!-- ============================================ -->
                        <div class="panel panel-default mtop30">
                            <div class="panel-heading">
                                <h3 class="panel-title"><i class="fa fa-cog"></i> General Settings</h3>
                            </div>
                            <div class="panel-body">
                                <?php
                                $general_settings = [];
                                foreach ($settings as $s) {
                                    // Exclude payment, lam, and reminder settings
                                    if (strpos($s->setting_key, 'wave_') !== 0 &&
                                        strpos($s->setting_key, 'paypal_') !== 0 &&
                                        strpos($s->setting_key, 'orange_money_') !== 0 &&
                                        strpos($s->setting_key, 'payment_') !== 0 &&
                                        strpos($s->setting_key, 'lam_api') !== 0 &&
                                        strpos($s->setting_key, 'reminder_') !== 0) {
                                        $general_settings[] = $s;
                                    }
                                }

                                foreach ($general_settings as $setting) {
                                    ?>
                                    <div class="form-group">
                                        <label for="<?php echo $setting->setting_key; ?>">
                                            <?php echo _l('dietetic_setting_' . $setting->setting_key); ?>
                                        </label>
                                        <?php if ($setting->setting_type == 'boolean') { ?>
                                            <select name="<?php echo $setting->setting_key; ?>" class="form-control">
                                                <option value="0" <?php echo $setting->setting_value == '0' ? 'selected' : ''; ?>>No</option>
                                                <option value="1" <?php echo $setting->setting_value == '1' ? 'selected' : ''; ?>>Yes</option>
                                            </select>
                                        <?php } else { ?>
                                            <input type="<?php echo $setting->setting_type == 'number' ? 'number' : 'text'; ?>"
                                                   name="<?php echo $setting->setting_key; ?>"
                                                   class="form-control"
                                                   value="<?php echo htmlspecialchars($setting->setting_value); ?>" />
                                        <?php } ?>
                                        <?php if (!empty($setting->description)) { ?>
                                            <small class="text-muted"><?php echo $setting->description; ?></small>
                                        <?php } ?>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>

                        <!-- Save Button -->
                        <div class="btn-bottom-toolbar text-right mtop30">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fa fa-check"></i> <?php echo _l('settings_save'); ?>
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
