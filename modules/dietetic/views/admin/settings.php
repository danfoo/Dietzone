<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4><i class="fa fa-credit-card"></i> Configuration des Passerelles de Paiement</h4>
                        <hr />

                        <?php echo form_open($this->uri->uri_string()); ?>

                        <!-- Wave Payment Gateway -->
                        <div class="panel panel-success mtop20">
                            <div class="panel-heading" style="background: #01807B; border-color: #01807B;">
                                <h3 class="panel-title" style="color: white; font-size: 16px;">
                                    <i class="fa fa-credit-card"></i> Wave Payment Gateway
                                </h3>
                            </div>
                            <div class="panel-body">
                                <?php
                                $wave_settings = [];
                                foreach ($settings as $s) {
                                    if (strpos($s->setting_key, 'wave_') === 0) {
                                        $wave_settings[] = $s;
                                    }
                                }

                                if (empty($wave_settings)) {
                                    echo '<div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> Aucun paramètre Wave trouvé. <a href="' . admin_url('dietetic/settings_debug') . '" target="_blank">Vérifier la base de données</a></div>';
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
                                                       placeholder="Entrez <?php echo strtolower($label); ?>" />
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

                        <!-- PayPal Payment Gateway -->
                        <div class="panel panel-primary mtop20">
                            <div class="panel-heading" style="background: #0070ba; border-color: #0070ba;">
                                <h3 class="panel-title" style="color: white; font-size: 16px;">
                                    <i class="fa fa-paypal"></i> PayPal Payment Gateway
                                </h3>
                            </div>
                            <div class="panel-body">
                                <?php
                                $paypal_settings = [];
                                foreach ($settings as $s) {
                                    if (strpos($s->setting_key, 'paypal_') === 0) {
                                        $paypal_settings[] = $s;
                                    }
                                }

                                if (empty($paypal_settings)) {
                                    echo '<div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> Aucun paramètre PayPal trouvé. <a href="' . admin_url('dietetic/settings_debug') . '" target="_blank">Vérifier la base de données</a></div>';
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
                                                       placeholder="Entrez <?php echo strtolower($label); ?>" />
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

                        <!-- Orange Money Payment Gateway -->
                        <div class="panel panel-warning mtop20">
                            <div class="panel-heading" style="background: #ff7900; border-color: #ff7900;">
                                <h3 class="panel-title" style="color: white; font-size: 16px;">
                                    <i class="fa fa-mobile"></i> Orange Money Payment Gateway
                                </h3>
                            </div>
                            <div class="panel-body">
                                <?php
                                $om_settings = [];
                                foreach ($settings as $s) {
                                    if (strpos($s->setting_key, 'orange_money_') === 0) {
                                        $om_settings[] = $s;
                                    }
                                }

                                if (empty($om_settings)) {
                                    echo '<div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> Aucun paramètre Orange Money trouvé. <a href="' . admin_url('dietetic/settings_debug') . '" target="_blank">Vérifier la base de données</a></div>';
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
                                                       placeholder="Entrez <?php echo strtolower($label); ?>" />
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

                        <!-- Save Button -->
                        <div class="btn-bottom-toolbar text-right mtop30">
                            <a href="<?php echo admin_url('dietetic/settings'); ?>" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Retour
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fa fa-check"></i> Enregistrer les modifications
                            </button>
                        </div>

                        <?php echo form_close(); ?>

                        <!-- Debug Link -->
                        <div class="text-center mtop20">
                            <a href="<?php echo admin_url('dietetic/settings_debug'); ?>" class="btn btn-default btn-sm" target="_blank">
                                <i class="fa fa-bug"></i> Debug - Vérifier la base de données
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
