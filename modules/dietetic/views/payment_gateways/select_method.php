<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="payment-method-selection">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <i class="fa fa-credit-card"></i> Payer la facture <?php echo htmlspecialchars($invoice->invoice_number); ?>
                        </h3>
                    </div>
                    <div class="panel-body">
                        <!-- Invoice Summary -->
                        <div class="invoice-summary" style="background: #f9f9f9; padding: 20px; border-radius: 5px; margin-bottom: 30px;">
                            <h4 style="margin-top: 0;">Résumé de la facture</h4>
                            <table class="table table-borderless">
                                <tr>
                                    <td width="40%"><strong>Numéro de facture</strong></td>
                                    <td><?php echo htmlspecialchars($invoice->invoice_number); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Date d'émission</strong></td>
                                    <td><?php echo date('d/m/Y', strtotime($invoice->issue_date)); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Date d'échéance</strong></td>
                                    <td><?php echo date('d/m/Y', strtotime($invoice->due_date)); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Montant total</strong></td>
                                    <td><h3 style="margin: 0; color: #28a745;"><?php echo number_format($invoice->total_amount, 0, ',', ' '); ?> FCFA</h3></td>
                                </tr>
                            </table>
                        </div>

                        <!-- Payment Methods -->
                        <h4><i class="fa fa-hand-o-right"></i> Choisissez votre méthode de paiement</h4>
                        <p class="text-muted">Sélectionnez la méthode de paiement que vous souhaitez utiliser</p>

                        <div class="payment-methods" style="margin-top: 20px;">
                            <?php if ($paypal_enabled): ?>
                            <div class="payment-method-card" style="border: 2px solid #0070ba; border-radius: 8px; padding: 20px; margin-bottom: 15px; cursor: pointer; transition: all 0.3s;" onclick="window.location='<?php echo site_url('dietetic/payment_gateways/paypal_checkout/' . $invoice->id); ?>'">
                                <div class="row">
                                    <div class="col-md-2 text-center">
                                        <i class="fa fa-paypal" style="font-size: 48px; color: #0070ba;"></i>
                                    </div>
                                    <div class="col-md-8">
                                        <h4 style="margin-top: 0;"><strong>PayPal</strong></h4>
                                        <p class="text-muted" style="margin-bottom: 0;">
                                            Payez en toute sécurité avec votre compte PayPal ou par carte bancaire
                                        </p>
                                    </div>
                                    <div class="col-md-2 text-center">
                                        <i class="fa fa-chevron-right" style="font-size: 24px; color: #0070ba; margin-top: 15px;"></i>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php if ($wave_enabled): ?>
                            <div class="payment-method-card" style="border: 2px solid #f46524; border-radius: 8px; padding: 20px; margin-bottom: 15px; cursor: pointer; transition: all 0.3s;" onclick="window.location='<?php echo site_url('dietetic/payment_gateways/wave_checkout/' . $invoice->id); ?>'">
                                <div class="row">
                                    <div class="col-md-2 text-center">
                                        <i class="fa fa-mobile" style="font-size: 48px; color: #f46524;"></i>
                                    </div>
                                    <div class="col-md-8">
                                        <h4 style="margin-top: 0;"><strong>Wave</strong></h4>
                                        <p class="text-muted" style="margin-bottom: 0;">
                                            Payez avec votre compte Wave (Sénégal)
                                        </p>
                                    </div>
                                    <div class="col-md-2 text-center">
                                        <i class="fa fa-chevron-right" style="font-size: 24px; color: #f46524; margin-top: 15px;"></i>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php if ($orange_money_enabled): ?>
                            <div class="payment-method-card" style="border: 2px solid #ff6600; border-radius: 8px; padding: 20px; margin-bottom: 15px; cursor: pointer; transition: all 0.3s;" onclick="window.location='<?php echo site_url('dietetic/payment_gateways/orange_money_checkout/' . $invoice->id); ?>'">
                                <div class="row">
                                    <div class="col-md-2 text-center">
                                        <i class="fa fa-mobile-phone" style="font-size: 48px; color: #ff6600;"></i>
                                    </div>
                                    <div class="col-md-8">
                                        <h4 style="margin-top: 0;"><strong>Orange Money</strong></h4>
                                        <p class="text-muted" style="margin-bottom: 0;">
                                            Payez avec votre compte Orange Money
                                        </p>
                                    </div>
                                    <div class="col-md-2 text-center">
                                        <i class="fa fa-chevron-right" style="font-size: 24px; color: #ff6600; margin-top: 15px;"></i>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php if (!$paypal_enabled && !$wave_enabled && !$orange_money_enabled): ?>
                            <div class="alert alert-warning">
                                <i class="fa fa-exclamation-triangle"></i>
                                Aucune méthode de paiement en ligne n'est actuellement disponible. Veuillez contacter l'administrateur.
                            </div>
                            <?php endif; ?>
                        </div>

                        <div style="margin-top: 30px; text-align: center;">
                            <a href="<?php echo site_url('dietetic/portal'); ?>" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Retour au tableau de bord
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Security Notice -->
                <div class="panel panel-default">
                    <div class="panel-body text-center">
                        <i class="fa fa-lock" style="font-size: 24px; color: #28a745;"></i>
                        <p style="margin-top: 10px; margin-bottom: 0;">
                            <strong>Paiement sécurisé</strong><br>
                            <small class="text-muted">Toutes les transactions sont cryptées et sécurisées</small>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.payment-method-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
</style>
