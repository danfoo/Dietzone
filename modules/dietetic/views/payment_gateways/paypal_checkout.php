<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="paypal-checkout">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <i class="fa fa-paypal"></i> Paiement PayPal
                        </h3>
                    </div>
                    <div class="panel-body">
                        <!-- Invoice Summary -->
                        <div class="invoice-summary" style="background: #f0f8ff; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                            <h4>Facture <?php echo htmlspecialchars($invoice->invoice_number); ?></h4>
                            <p class="text-muted" style="margin: 0;">
                                <strong>Montant à payer :</strong>
                                <span style="font-size: 24px; color: #0070ba;">
                                    <?php
                                    $amount_usd = $invoice->total_amount / 650; // Approximate XOF to USD conversion
                                    echo number_format($amount_usd, 2);
                                    ?> USD
                                </span>
                                <br>
                                <small>(Environ <?php echo number_format($invoice->total_amount, 0, ',', ' '); ?> FCFA)</small>
                            </p>
                        </div>

                        <!-- PayPal Button Container -->
                        <div id="paypal-button-container"></div>

                        <!-- Loading indicator -->
                        <div id="loading" style="display: none; text-align: center; padding: 20px;">
                            <i class="fa fa-spinner fa-spin" style="font-size: 32px; color: #0070ba;"></i>
                            <p>Traitement du paiement en cours...</p>
                        </div>

                        <!-- Error message -->
                        <div id="error-message" class="alert alert-danger" style="display: none; margin-top: 20px;">
                            <i class="fa fa-exclamation-circle"></i>
                            <span id="error-text"></span>
                        </div>

                        <div style="margin-top: 20px; text-align: center;">
                            <a href="<?php echo site_url('dietetic/payment_gateways/pay/' . $invoice->id); ?>" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Choisir une autre méthode
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Security Info -->
                <div class="panel panel-default">
                    <div class="panel-body text-center">
                        <i class="fa fa-shield" style="font-size: 32px; color: #0070ba;"></i>
                        <p style="margin-top: 10px; margin-bottom: 0;">
                            <strong>Paiement sécurisé par PayPal</strong><br>
                            <small class="text-muted">Protection des acheteurs - Remboursement garanti</small>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- PayPal SDK -->
<script src="https://www.paypal.com/sdk/js?client-id=<?php echo $paypal_client_id; ?>&currency=USD"></script>

<script>
paypal.Buttons({
    createOrder: function(data, actions) {
        return actions.order.create({
            purchase_units: [{
                description: 'Facture <?php echo htmlspecialchars($invoice->invoice_number); ?>',
                amount: {
                    currency_code: 'USD',
                    value: '<?php echo number_format($amount_usd, 2, '.', ''); ?>'
                },
                custom_id: '<?php echo $invoice->id; ?>'
            }]
        });
    },
    onApprove: function(data, actions) {
        // Show loading
        document.getElementById('paypal-button-container').style.display = 'none';
        document.getElementById('loading').style.display = 'block';

        return actions.order.capture().then(function(details) {
            // Redirect to success page
            window.location.href = '<?php echo site_url('dietetic/payment_gateways/paypal_success'); ?>?orderID=' + data.orderID + '&invoice_id=<?php echo $invoice->id; ?>';
        });
    },
    onError: function(err) {
        document.getElementById('loading').style.display = 'none';
        document.getElementById('error-message').style.display = 'block';
        document.getElementById('error-text').textContent = 'Une erreur est survenue lors du paiement. Veuillez réessayer.';
        console.error('PayPal Error:', err);
    },
    onCancel: function(data) {
        document.getElementById('error-message').style.display = 'block';
        document.getElementById('error-text').textContent = 'Paiement annulé. Vous pouvez réessayer ou choisir une autre méthode de paiement.';
    }
}).render('#paypal-button-container');
</script>

<style>
#paypal-button-container {
    margin-top: 20px;
    margin-bottom: 20px;
}
</style>
