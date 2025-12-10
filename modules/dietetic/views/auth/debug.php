<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?> - DietZone</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #01807B;
            margin-bottom: 10px;
            font-size: 28px;
        }
        .subtitle {
            color: #6c757d;
            margin-bottom: 30px;
            font-size: 16px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
            font-size: 14px;
        }
        input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 16px;
        }
        input:focus {
            outline: none;
            border-color: #01807B;
        }
        button {
            background: #01807B;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
        }
        button:hover {
            background: #016663;
        }
        .info {
            margin-top: 10px;
            padding: 12px;
            background: #e7f3f1;
            border-radius: 8px;
            font-size: 13px;
            color: #01807B;
        }
        .result {
            margin-top: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            border-left: 4px solid #01807B;
        }
        .result h3 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 20px;
        }
        .result h4 {
            color: #01807B;
            margin: 20px 0 10px;
            font-size: 16px;
        }
        .success-box {
            background: #d4edda;
            border-left-color: #28a745;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            color: #155724;
            font-weight: 600;
        }
        .error-box {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            color: #721c24;
            font-weight: 600;
        }
        .warning-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
            color: #856404;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            background: white;
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        th {
            background: #f8f9fa;
            font-weight: 600;
            color: #2c3e50;
            width: 40%;
        }
        td {
            color: #495057;
        }
        tr:last-child td {
            border-bottom: none;
        }
        code {
            background: #f4f4f4;
            padding: 3px 8px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            color: #e83e8c;
        }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-success {
            background: #28a745;
            color: white;
        }
        .badge-danger {
            background: #dc3545;
            color: white;
        }
        .back-link {
            display: inline-block;
            margin-top: 30px;
            color: #01807B;
            text-decoration: none;
            font-weight: 600;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .help-section {
            margin-top: 30px;
            padding: 20px;
            background: #e7f3f1;
            border-radius: 10px;
        }
        .help-section h4 {
            color: #01807B;
            margin-bottom: 15px;
        }
        .help-section ul {
            margin-left: 20px;
        }
        .help-section li {
            margin-bottom: 10px;
            color: #2c3e50;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 <?php echo $title; ?></h1>
        <p class="subtitle">Ce diagnostic vous aide à identifier les problèmes de connexion.</p>

        <form method="POST">
            <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>

            <div class="form-group">
                <label>Numéro de téléphone :</label>
                <input type="text" name="phone" placeholder="+221 77 123 45 67" required
                       value="<?php echo htmlspecialchars($phone_input ?? ''); ?>">
                <div class="info">
                    <strong>Formats acceptés :</strong> +221771234567, 771234567, +221 77 123 45 67, etc.
                </div>
            </div>
            <button type="submit">🔍 Lancer le diagnostic</button>
        </form>

        <?php if ($result): ?>
        <div class="result">
            <h3>📊 Résultats du diagnostic</h3>

            <h4>1️⃣ Nettoyage du numéro</h4>
            <table>
                <tr>
                    <th>Entrée</th>
                    <td><code><?php echo htmlspecialchars($result['phone_input']); ?></code></td>
                </tr>
                <tr>
                    <th>Nettoyé (format BDD)</th>
                    <td><code><?php echo htmlspecialchars($result['phone_cleaned']); ?></code></td>
                </tr>
            </table>

            <h4>2️⃣ Recherche dans la base de données</h4>

            <?php if ($result['patient']): ?>
                <div class="success-box">
                    ✅ <strong>Patient trouvé dans la base de données !</strong>
                </div>

                <table>
                    <tr>
                        <th>ID Patient</th>
                        <td><?php echo $result['patient']['patient_id']; ?></td>
                    </tr>
                    <tr>
                        <th>ID Client</th>
                        <td><?php echo $result['patient']['client_id']; ?></td>
                    </tr>
                    <tr>
                        <th>ID Contact</th>
                        <td><?php echo $result['patient']['contactid']; ?></td>
                    </tr>
                    <tr>
                        <th>Nom complet</th>
                        <td><?php echo htmlspecialchars($result['patient']['firstname'] . ' ' . $result['patient']['lastname']); ?></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><?php echo htmlspecialchars($result['patient']['email']); ?></td>
                    </tr>
                    <tr>
                        <th>Téléphone (en base)</th>
                        <td><code><?php echo htmlspecialchars($result['patient']['phonenumber']); ?></code></td>
                    </tr>
                    <tr>
                        <th>Téléphone correspond ?</th>
                        <td>
                            <?php if ($result['patient']['phone_match']): ?>
                                <span class="badge badge-success">✅ OUI</span>
                            <?php else: ?>
                                <span class="badge badge-danger">❌ NON</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Contact primaire</th>
                        <td><?php echo $result['patient']['is_primary'] ? '✅' : '❌'; ?></td>
                    </tr>
                    <tr>
                        <th>Contact actif</th>
                        <td><?php echo $result['patient']['active'] ? '✅' : '❌'; ?></td>
                    </tr>
                    <tr>
                        <th>Mot de passe défini</th>
                        <td>
                            <?php if ($result['patient']['has_password']): ?>
                                <span class="badge badge-success">✅ OUI</span>
                                (<?php echo $result['patient']['password_length']; ?> caractères)
                            <?php else: ?>
                                <span class="badge badge-danger">❌ NON</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>

                <?php if (!$result['patient']['has_password']): ?>
                <div class="warning-box">
                    <strong>⚠️ PROBLÈME DÉTECTÉ : Aucun mot de passe</strong><br>
                    Ce compte patient n'a pas de mot de passe défini. Vous devez en créer un via :
                    <ul style="margin-top: 10px; margin-left: 20px;">
                        <li>L'onglet "Inscription" pour créer un nouveau mot de passe</li>
                        <li>Ou "Mot de passe oublié" pour réinitialiser</li>
                    </ul>
                </div>
                <?php elseif ($result['patient']['password_length'] > 50): ?>
                <div class="success-box">
                    ✅ <strong>Mot de passe bien sécurisé</strong><br>
                    Le mot de passe est correctement hashé (<?php echo $result['patient']['password_length']; ?> caractères).
                    Si la connexion échoue, c'est que le mot de passe est incorrect.
                </div>
                <?php endif; ?>

            <?php else: ?>
                <div class="error-box">
                    ❌ <strong>Aucun patient trouvé pour ce numéro</strong>
                </div>

                <?php if (!empty($result['similar_numbers'])): ?>
                <h4>3️⃣ Numéros similaires trouvés</h4>
                <p style="color: #6c757d; margin-bottom: 10px;">
                    Voici les numéros enregistrés avec des chiffres similaires :
                </p>
                <table>
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Téléphone</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($result['similar_numbers'] as $similar): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($similar['firstname'] . ' ' . $similar['lastname']); ?></td>
                            <td><code><?php echo htmlspecialchars($similar['phonenumber']); ?></code></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="info" style="margin-top: 15px;">
                    <strong>💡 Suggestion :</strong> Vérifiez le format de votre numéro.
                    Il doit correspondre exactement à celui en base de données.
                </div>
                <?php else: ?>
                <div class="info" style="margin-top: 15px;">
                    <strong>💡 Ce compte n'existe pas encore.</strong><br>
                    Utilisez l'onglet "Inscription" pour créer un nouveau compte.
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="help-section">
            <h4>📝 Comment corriger les problèmes ?</h4>
            <ul>
                <li><strong>Patient non trouvé :</strong> Le compte n'existe pas, créez-le via "Inscription"</li>
                <li><strong>Pas de mot de passe :</strong> Utilisez "Mot de passe oublié" pour en créer un</li>
                <li><strong>Numéro différent :</strong> Le format doit être identique à celui en base (+221...)</li>
                <li><strong>Mot de passe incorrect :</strong> Utilisez "Mot de passe oublié" pour le réinitialiser</li>
            </ul>
        </div>

        <a href="<?php echo site_url('dietetic/auth'); ?>" class="back-link">
            ← Retour à la page de connexion
        </a>
    </div>
</body>
</html>
