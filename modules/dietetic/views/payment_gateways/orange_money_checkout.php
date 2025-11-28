<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="orange-money-checkout">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel" style="border: 2px solid #ff6600;">
                    <div class="panel-heading" style="background-color: #ff6600; color: white;">
                        <h3 class="panel-title">
                            <i class="fa fa-mobile-phone"></i> Paiement Orange Money
                        </h3>
                    </div>
                    <div class="panel-body">
                        <!-- Invoice Summary -->
                        <div class="invoice-summary" style="background: #fff5f0; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                            <h4>Facture <?php echo htmlspecialchars($invoice->invoice_number); ?></h4>
                            <p class="text-muted" style="margin: 0;">
                                <strong>Montant à payer :</strong>
                                <span style="font-size: 24px; color: #ff6600;">
                                    <?php echo number_format($invoice->total_amount, 0, ',', ' '); ?> FCFA
                                </span>
                            </p>
                        </div>

                        <!-- Payment Form -->
                        <form id="orange-money-form">
                            <input type="hidden" name="invoice_id" value="<?php echo $invoice->id; ?>">

                            <div class="form-group">
                                <label for="phone">
                                    <i class="fa fa-phone"></i> Numéro de téléphone Orange Money
                                </label>
                                <div class="input-group">
                                    <span class="input-group-addon">+221</span>
                                    <input type="tel"
                                           class="form-control"
                                           id="phone"
                                           name="phone"
                                           placeholder="77 123 45 67"
                                           pattern="[0-9]{9}"
                                           required
                                           style="font-size: 18px; height: 45px;">
                                </div>
                                <small class="help-block">
                                    Entrez votre numéro Orange Money (9 chiffres)
                                </small>
                            </div>

                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i>
                                <strong>Instructions :</strong>
                                <ol style="margin-bottom: 0; padding-left: 20px;">
                                    <li>Entrez votre numéro Orange Money ci-dessus</li>
                                    <li>Cliquez sur "Payer maintenant"</li>
                                    <li>Vous recevrez une notification sur votre téléphone</li>
                                    <li>Composez <strong>#144*82#</strong> pour confirmer le paiement</li>
                                    <li>Entrez votre code PIN Orange Money</li>
                                </ol>
                            </div>

                            <button type="submit" class="btn btn-lg btn-block" style="background-color: #ff6600; color: white; height: 50px; font-size: 18px;">
                                <i class="fa fa-lock"></i> Payer maintenant
                            </button>
                        </form>

                        <!-- Loading indicator -->
                        <div id="loading" style="display: none; text-align: center; padding: 30px;">
                            <i class="fa fa-spinner fa-spin" style="font-size: 48px; color: #ff6600;"></i>
                            <h4 style="margin-top: 20px; color: #ff6600;">Initialisation du paiement...</h4>
                            <p class="text-muted">Veuillez patienter</p>
                        </div>

                        <!-- Success message -->
                        <div id="success-message" class="alert alert-success" style="display: none; margin-top: 20px;">
                            <i class="fa fa-check-circle"></i>
                            <strong>Paiement initié !</strong>
                            <p style="margin-top: 10px;">
                                Vérifiez votre téléphone et composez <strong>#144*82#</strong> pour confirmer le paiement.
                            </p>
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
                        <i class="fa fa-shield" style="font-size: 32px; color: #ff6600;"></i>
                        <p style="margin-top: 10px; margin-bottom: 0;">
                            <strong>Paiement sécurisé par Orange Money</strong><br>
                            <small class="text-muted">Transaction sécurisée et cryptée</small>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('orange-money-form').addEventListener('submit', function(e) {
    e.preventDefault();

    // Hide form, show loading
    document.getElementById('orange-money-form').style.display = 'none';
    document.getElementById('loading').style.display = 'block';
    document.getElementById('error-message').style.display = 'none';
    document.getElementById('success-message').style.display = 'none';

    // Get form data
    var formData = new FormData(this);

    // Send AJAX request
    fetch('<?php echo site_url('dietetic/payment_gateways/orange_money_process'); ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('loading').style.display = 'none';

        if (data.success) {
            document.getElementById('success-message').style.display = 'block';

            // Poll for payment confirmation (optional)
            setTimeout(function() {
                window.location.href = '<?php echo site_url('dietetic/portal/invoices'); ?>';
            }, 10000); // Redirect after 10 seconds
        } else {
            document.getElementById('error-message').style.display = 'block';
            document.getElementById('error-text').textContent = data.message || 'Une erreur est survenue. Veuillez réessayer.';
            document.getElementById('orange-money-form').style.display = 'block';
        }
    })
    .catch(error => {
        document.getElementById('loading').style.display = 'none';
        document.getElementById('error-message').style.display = 'block';
        document.getElementById('error-text').textContent = 'Erreur de connexion. Veuillez vérifier votre connexion internet et réessayer.';
        document.getElementById('orange-money-form').style.display = 'block';
        console.error('Error:', error);
    });
});

// Auto-format phone number
document.getElementById('phone').addEventListener('input', function(e) {
    var value = e.target.value.replace(/\D/g, '');
    if (value.length > 9) {
        value = value.substring(0, 9);
    }
    e.target.value = value.replace(/(\d{2})(\d{3})(\d{2})(\d{2})/, '$1 $2 $3 $4').trim();
});
</script>
