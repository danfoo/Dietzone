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
                                <h4><?php echo _l('dietetic_settings'); ?></h4>
                            </div>
                            <div class="col-md-4 text-right">
                                <a href="<?php echo admin_url('dietetic/settings_debug'); ?>" class="btn btn-default btn-sm" target="_blank">
                                    <i class="fa fa-bug"></i> Debug Settings
                                </a>
                            </div>
                        </div>
                        <hr />

                        <?php echo form_open($this->uri->uri_string()); ?>

                        <!-- SMS Integration Section -->
                        <h5><i class="fa fa-mobile"></i> <?php echo _l('SMS Integration (LAM API)'); ?></h5>
                        <hr />

                        <?php foreach ($settings as $setting) { ?>
                            <?php if (strpos($setting->setting_key, 'lam_api') === 0) { ?>
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
                                               value="<?php echo $setting->setting_value; ?>" />
                                    <?php } ?>
                                    <?php if ($setting->description) { ?>
                                        <small class="text-muted"><?php echo $setting->description; ?></small>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        <?php } ?>

                        <h5 class="mtop30"><i class="fa fa-bell"></i> <?php echo _l('dietetic_reminders'); ?></h5>
                        <hr />

                        <?php foreach ($settings as $setting) { ?>
                            <?php if (strpos($setting->setting_key, 'reminder_') === 0) { ?>
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
                                               value="<?php echo $setting->setting_value; ?>" />
                                    <?php } ?>
                                    <?php if ($setting->description) { ?>
                                        <small class="text-muted"><?php echo $setting->description; ?></small>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        <?php } ?>

                        <!-- Payment Gateways Section Header -->
                        <div class="mtop30" style="background: linear-gradient(135deg, #01807B 0%, #019d96 100%); padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                            <h4 style="color: white; margin: 0;">
                                <i class="fa fa-credit-card"></i> Payment Gateways Configuration
                            </h4>
                            <p style="color: rgba(255,255,255,0.9); margin: 5px 0 0 0; font-size: 13px;">
                                Configure payment methods for service subscriptions
                            </p>
                        </div>

                        <!-- Wave Payment Settings -->
                        <h5><i class="fa fa-credit-card"></i> Wave Payment Gateway</h5>
                        <hr />

                        <?php foreach ($settings as $setting) { ?>
                            <?php if (strpos($setting->setting_key, 'wave_') === 0) { ?>
                                <div class="form-group">
                                    <label for="<?php echo $setting->setting_key; ?>">
                                        <?php
                                        // Display friendly label
                                        $label = str_replace('wave_', '', $setting->setting_key);
                                        $label = ucwords(str_replace('_', ' ', $label));
                                        echo $label;
                                        ?>
                                    </label>
                                    <?php if ($setting->setting_type == 'boolean') { ?>
                                        <select name="<?php echo $setting->setting_key; ?>" class="form-control">
                                            <option value="0" <?php echo $setting->setting_value == '0' ? 'selected' : ''; ?>>Disabled</option>
                                            <option value="1" <?php echo $setting->setting_value == '1' ? 'selected' : ''; ?>>Enabled</option>
                                        </select>
                                    <?php } else { ?>
                                        <input type="<?php echo (strpos($setting->setting_key, 'key') !== false || strpos($setting->setting_key, 'secret') !== false) ? 'password' : 'text'; ?>"
                                               name="<?php echo $setting->setting_key; ?>"
                                               class="form-control"
                                               value="<?php echo htmlspecialchars($setting->setting_value); ?>"
                                               placeholder="<?php echo $label; ?>" />
                                    <?php } ?>
                                    <?php if ($setting->description) { ?>
                                        <small class="text-muted"><?php echo $setting->description; ?></small>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        <?php } ?>

                        <!-- PayPal Payment Settings -->
                        <h5 class="mtop30"><i class="fa fa-paypal"></i> PayPal Payment Gateway</h5>
                        <hr />

                        <?php foreach ($settings as $setting) { ?>
                            <?php if (strpos($setting->setting_key, 'paypal_') === 0) { ?>
                                <div class="form-group">
                                    <label for="<?php echo $setting->setting_key; ?>">
                                        <?php
                                        // Display friendly label
                                        $label = str_replace('paypal_', '', $setting->setting_key);
                                        $label = ucwords(str_replace('_', ' ', $label));
                                        echo $label;
                                        ?>
                                    </label>
                                    <?php if ($setting->setting_type == 'boolean') { ?>
                                        <select name="<?php echo $setting->setting_key; ?>" class="form-control">
                                            <option value="0" <?php echo $setting->setting_value == '0' ? 'selected' : ''; ?>>Disabled</option>
                                            <option value="1" <?php echo $setting->setting_value == '1' ? 'selected' : ''; ?>>Enabled</option>
                                        </select>
                                    <?php } elseif ($setting->setting_key == 'paypal_mode') { ?>
                                        <select name="<?php echo $setting->setting_key; ?>" class="form-control">
                                            <option value="sandbox" <?php echo $setting->setting_value == 'sandbox' ? 'selected' : ''; ?>>Sandbox (Test)</option>
                                            <option value="live" <?php echo $setting->setting_value == 'live' ? 'selected' : ''; ?>>Live (Production)</option>
                                        </select>
                                    <?php } else { ?>
                                        <input type="<?php echo (strpos($setting->setting_key, 'secret') !== false) ? 'password' : 'text'; ?>"
                                               name="<?php echo $setting->setting_key; ?>"
                                               class="form-control"
                                               value="<?php echo htmlspecialchars($setting->setting_value); ?>"
                                               placeholder="<?php echo $label; ?>" />
                                    <?php } ?>
                                    <?php if ($setting->description) { ?>
                                        <small class="text-muted"><?php echo $setting->description; ?></small>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        <?php } ?>

                        <!-- Orange Money Payment Settings -->
                        <h5 class="mtop30"><i class="fa fa-mobile"></i> Orange Money Payment Gateway</h5>
                        <hr />

                        <?php foreach ($settings as $setting) { ?>
                            <?php if (strpos($setting->setting_key, 'orange_money_') === 0) { ?>
                                <div class="form-group">
                                    <label for="<?php echo $setting->setting_key; ?>">
                                        <?php
                                        // Display friendly label
                                        $label = str_replace('orange_money_', '', $setting->setting_key);
                                        $label = ucwords(str_replace('_', ' ', $label));
                                        echo $label;
                                        ?>
                                    </label>
                                    <?php if ($setting->setting_type == 'boolean') { ?>
                                        <select name="<?php echo $setting->setting_key; ?>" class="form-control">
                                            <option value="0" <?php echo $setting->setting_value == '0' ? 'selected' : ''; ?>>Disabled</option>
                                            <option value="1" <?php echo $setting->setting_value == '1' ? 'selected' : ''; ?>>Enabled</option>
                                        </select>
                                    <?php } else { ?>
                                        <input type="<?php echo (strpos($setting->setting_key, 'key') !== false) ? 'password' : 'text'; ?>"
                                               name="<?php echo $setting->setting_key; ?>"
                                               class="form-control"
                                               value="<?php echo htmlspecialchars($setting->setting_value); ?>"
                                               placeholder="<?php echo $label; ?>" />
                                    <?php } ?>
                                    <?php if ($setting->description) { ?>
                                        <small class="text-muted"><?php echo $setting->description; ?></small>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        <?php } ?>

                        <h5 class="mtop30"><i class="fa fa-cog"></i> <?php echo _l('general'); ?></h5>
                        <hr />

                        <?php foreach ($settings as $setting) { ?>
                            <?php if (strpos($setting->setting_key, 'lam_api') !== 0 && strpos($setting->setting_key, 'reminder_') !== 0 && strpos($setting->setting_key, 'wave_') !== 0 && strpos($setting->setting_key, 'paypal_') !== 0 && strpos($setting->setting_key, 'orange_money_') !== 0) { ?>
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
                                               value="<?php echo $setting->setting_value; ?>" />
                                    <?php } ?>
                                    <?php if ($setting->description) { ?>
                                        <small class="text-muted"><?php echo $setting->description; ?></small>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        <?php } ?>

                        <div class="btn-bottom-toolbar text-right">
                            <button type="submit" class="btn btn-info"><?php echo _l('settings_save'); ?></button>
                        </div>

                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
