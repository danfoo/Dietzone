<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Test Facture</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        pre { background: #f5f5f5; padding: 10px; }
    </style>
</head>
<body>
    <h1>🧪 Test Facture - Vue Minimaliste</h1>

    <h2>Données Facture</h2>
    <pre><?php print_r($invoice); ?></pre>

    <h2>Données Patient</h2>
    <pre><?php print_r($patient); ?></pre>

    <h2>Données Client</h2>
    <pre><?php print_r($client); ?></pre>

    <p><a href="<?php echo site_url('dietetic/portal/invoices'); ?>">← Retour aux factures</a></p>
</body>
</html>
