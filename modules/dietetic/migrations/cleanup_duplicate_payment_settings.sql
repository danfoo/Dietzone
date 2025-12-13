-- Cleanup duplicate payment settings
-- Remove settings with 'payment_' prefix (they are duplicates)

DELETE FROM `tbldietic_settings`
WHERE `setting_key` IN (
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
);

-- Verify remaining payment settings
SELECT * FROM `tbldietic_settings`
WHERE `setting_key` LIKE 'wave_%'
   OR `setting_key` LIKE 'paypal_%'
   OR `setting_key` LIKE 'orange_money_%'
ORDER BY `setting_key`;
