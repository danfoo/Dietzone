<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$active_page = 'invoices';
$page_title = 'Erreur de Paiement';
$this->load->view('portal/includes/portal_header');

// Helper function for formatting money
if (!function_exists('format_money_safe')) {
    function format_money_safe($amount, $currency = 'XOF') {
        if (function_exists('app_format_money')) {
            return app_format_money($amount, $currency);
        }
        return number_format($amount, 0, ',', ' ') . ' ' . $currency;
    }
}
?>

<style>
:root {
    --primary-color: #01807B;
    --danger-color: #f56565;
    --danger-light: #FFF5F5;
    --text-dark: #1a202c;
    --text-light: #718096;
}

* {
    box-sizing: border-box;
}

body {
    background: linear-gradient(135deg, #f0f4f8 0%, #e8eff5 50%, #f5f7fa 100%);
    background-attachment: fixed;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.payment-result-container {
    max-width: 600px;
    margin: 60px auto;
    padding: 20px;
}

.payment-result-card {
    background: white;
    border-radius: 24px;
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    animation: slideUp 0.6s ease-out;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.result-header {
    background: linear-gradient(135deg, var(--danger-color) 0%, #e53e3e 100%);
    padding: 50px 30px;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.result-header::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
    animation: rotate 20s linear infinite;
}

@keyframes rotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.result-icon {
    width: 100px;
    height: 100px;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    font-size: 50px;
    color: white;
    position: relative;
    z-index: 1;
    animation: shake 0.5s ease-in-out;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-10px); }
    75% { transform: translateX(10px); }
}

.result-header h1 {
    color: white;
    font-size: 28px;
    font-weight: 800;
    margin: 0 0 10px 0;
    position: relative;
    z-index: 1;
}

.result-header p {
    color: rgba(255, 255, 255, 0.95);
    font-size: 16px;
    margin: 0;
    position: relative;
    z-index: 1;
}

.result-body {
    padding: 40px 30px;
}

.error-box {
    background: var(--danger-light);
    border-left: 4px solid var(--danger-color);
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 30px;
}

.error-box h3 {
    color: var(--text-dark);
    font-size: 16px;
    font-weight: 700;
    margin: 0 0 10px 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.error-box h3 i {
    color: var(--danger-color);
}

.error-box p {
    color: var(--text-light);
    font-size: 14px;
    line-height: 1.6;
    margin: 0;
}

.help-box {
    background: #f0f8ff;
    border-left: 4px solid var(--primary-color);
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 30px;
}

.help-box h3 {
    color: var(--text-dark);
    font-size: 16px;
    font-weight: 700;
    margin: 0 0 12px 0;
}

.help-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.help-list li {
    color: var(--text-light);
    font-size: 14px;
    padding: 8px 0;
    display: flex;
    align-items: start;
    gap: 10px;
}

.help-list li i {
    color: var(--primary-color);
    margin-top: 2px;
}

.invoice-details {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 30px;
}

.invoice-detail-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #e0e0e0;
}

.invoice-detail-row:last-child {
    border-bottom: none;
}

.detail-label {
    color: var(--text-light);
    font-size: 14px;
    font-weight: 600;
}

.detail-value {
    color: var(--text-dark);
    font-size: 16px;
    font-weight: 700;
}

.action-buttons {
    display: flex;
    gap: 12px;
    flex-direction: column;
}

.btn {
    padding: 16px 24px;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 700;
    text-decoration: none;
    text-align: center;
    transition: all 0.3s ease;
    cursor: pointer;
    border: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary-color) 0%, #015a57 100%);
    color: white;
    box-shadow: 0 4px 16px rgba(1, 128, 123, 0.3);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(1, 128, 123, 0.4);
}

.btn-secondary {
    background: white;
    color: var(--text-dark);
    border: 2px solid #e0e0e0;
}

.btn-secondary:hover {
    border-color: var(--primary-color);
    color: var(--primary-color);
    transform: translateY(-2px);
}

@media (max-width: 768px) {
    .payment-result-container {
        margin: 20px auto;
        padding: 12px;
    }

    .payment-result-card {
        border-radius: 16px;
    }

    .result-header {
        padding: 40px 20px;
    }

    .result-header h1 {
        font-size: 24px;
    }

    .result-body {
        padding: 30px 20px;
    }

    .btn {
        width: 100%;
        min-height: 56px;
    }
}
</style>

<div class="payment-result-container">
    <div class="payment-result-card">
        <!-- Header -->
        <div class="result-header">
            <div class="result-icon">
                <i class="fa fa-exclamation-triangle"></i>
            </div>
            <h1>Erreur de Paiement</h1>
            <p>Une erreur s'est produite lors du traitement</p>
        </div>

        <!-- Body -->
        <div class="result-body">
            <!-- Error Box -->
            <div class="error-box">
                <h3>
                    <i class="fa fa-times-circle"></i>
                    Détails de l'erreur
                </h3>
                <p>
                    <?php echo htmlspecialchars($error_message ?? 'Une erreur technique est survenue lors du traitement de votre paiement.'); ?>
                </p>
            </div>

            <!-- Help Box -->
            <div class="help-box">
                <h3><i class="fa fa-life-ring"></i> Que faire maintenant ?</h3>
                <ul class="help-list">
                    <li>
                        <i class="fa fa-check-circle"></i>
                        <span>Vérifiez que votre compte PayPal dispose de fonds suffisants</span>
                    </li>
                    <li>
                        <i class="fa fa-check-circle"></i>
                        <span>Assurez-vous que vos informations PayPal sont à jour</span>
                    </li>
                    <li>
                        <i class="fa fa-check-circle"></i>
                        <span>Réessayez le paiement dans quelques minutes</span>
                    </li>
                    <li>
                        <i class="fa fa-check-circle"></i>
                        <span>Si le problème persiste, contactez notre support</span>
                    </li>
                </ul>
            </div>

            <!-- Invoice Details -->
            <div class="invoice-details">
                <div class="invoice-detail-row">
                    <span class="detail-label">Facture N°</span>
                    <span class="detail-value">#<?php echo $invoice->id; ?></span>
                </div>
                <div class="invoice-detail-row">
                    <span class="detail-label">Montant</span>
                    <span class="detail-value"><?php echo format_money_safe($invoice->total); ?></span>
                </div>
                <div class="invoice-detail-row">
                    <span class="detail-label">Statut</span>
                    <span class="detail-value" style="color: var(--danger-color);">
                        <i class="fa fa-times-circle"></i> Échec du paiement
                    </span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="<?php echo site_url('dietetic/portal/pay/' . $invoice->id . '/paypal'); ?>" class="btn btn-primary">
                    <i class="fa fa-refresh"></i>
                    Réessayer le paiement
                </a>
                <a href="<?php echo site_url('dietetic/portal/invoices'); ?>" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i>
                    Retour aux factures
                </a>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('portal/includes/portal_footer'); ?>
