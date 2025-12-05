<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!-- FIX JQUERY: Charger jQuery AVANT init_head() pour éviter les erreurs CSRF -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <i class="fa fa-gavel"></i> Gestion des Pages Légales
                        </h4>
                        <hr class="hr-panel-heading">

                        <p class="text-muted">
                            Gérez ici le contenu de la Politique de Confidentialité et des Conditions Générales d'Utilisation.
                            Ces pages seront accessibles à tous les utilisateurs depuis le header du portail client.
                        </p>

                        <div class="alert alert-warning">
                            <i class="fa fa-shield"></i> <strong>Protection WAF/ModSecurity Activée</strong><br>
                            Le contenu HTML/CSS est automatiquement encodé en Base64 lors de la soumission pour éviter les erreurs "403 Forbidden".
                            Vous pouvez utiliser tout le HTML et CSS sans restriction - l'encodage/décodage est transparent.
                        </div>

                        <?php echo form_open(admin_url('dietetic/legal_pages/manage'), ['id' => 'legal-pages-form']); ?>

                        <!-- Hidden fields for Base64 encoded content (bypass WAF/ModSecurity) -->
                        <input type="hidden" name="privacy_policy_encoded" id="privacy_policy_encoded" value="">
                        <input type="hidden" name="terms_of_service_encoded" id="terms_of_service_encoded" value="">

                        <!-- Privacy Policy Section -->
                        <div class="row">
                            <div class="col-md-12">
                                <h4 style="margin-top: 30px;">
                                    <i class="fa fa-shield"></i> Politique de Confidentialité
                                </h4>
                                <hr>

                                <div class="form-group">
                                    <label>
                                        Contenu de la Politique de Confidentialité
                                        <small class="text-success"><strong>(HTML et CSS complets acceptés - aucune restriction)</strong></small>
                                    </label>
                                    <textarea
                                        name="privacy_policy"
                                        id="privacy_policy"
                                        rows="20"
                                        class="form-control"
                                        style="font-family: 'Courier New', monospace; font-size: 13px;"
                                    ><?php echo htmlspecialchars($privacy_policy); ?></textarea>
                                    <small class="text-success">
                                        <i class="fa fa-check-circle"></i> <strong>Aucun filtrage appliqué</strong> - Vous pouvez utiliser tout HTML/CSS y compris :
                                        &lt;style&gt;, &lt;div&gt;, &lt;span&gt;, classes, IDs, styles inline, etc.
                                    </small>
                                </div>

                                <div class="alert alert-info">
                                    <strong><i class="fa fa-info-circle"></i> Aperçu disponible:</strong>
                                    Une fois enregistré, vous pouvez voir la page en direct ici:
                                    <a href="<?php echo admin_url('dietetic/legal_pages/privacy'); ?>" target="_blank">
                                        Voir la Politique de Confidentialité
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Terms of Service Section -->
                        <div class="row">
                            <div class="col-md-12">
                                <h4 style="margin-top: 40px;">
                                    <i class="fa fa-file-text"></i> Conditions Générales d'Utilisation
                                </h4>
                                <hr>

                                <div class="form-group">
                                    <label>
                                        Contenu des Conditions d'Utilisation
                                        <small class="text-success"><strong>(HTML et CSS complets acceptés - aucune restriction)</strong></small>
                                    </label>
                                    <textarea
                                        name="terms_of_service"
                                        id="terms_of_service"
                                        rows="20"
                                        class="form-control"
                                        style="font-family: 'Courier New', monospace; font-size: 13px;"
                                    ><?php echo htmlspecialchars($terms_of_service); ?></textarea>
                                    <small class="text-success">
                                        <i class="fa fa-check-circle"></i> <strong>Aucun filtrage appliqué</strong> - Vous pouvez utiliser tout HTML/CSS y compris :
                                        &lt;style&gt;, &lt;div&gt;, &lt;span&gt;, classes, IDs, styles inline, etc.
                                    </small>
                                </div>

                                <div class="alert alert-info">
                                    <strong><i class="fa fa-info-circle"></i> Aperçu disponible:</strong>
                                    Une fois enregistré, vous pouvez voir la page en direct ici:
                                    <a href="<?php echo admin_url('dietetic/legal_pages/terms'); ?>" target="_blank">
                                        Voir les Conditions d'Utilisation
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Save Button -->
                        <div class="row">
                            <div class="col-md-12 text-right" style="margin-top: 30px;">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fa fa-check"></i> Enregistrer les Pages Légales
                                </button>
                            </div>
                        </div>

                        <?php echo form_close(); ?>
                    </div>
                </div>

                <!-- Help Section -->
                <div class="panel_s mtop20">
                    <div class="panel-body">
                        <h4><i class="fa fa-question-circle"></i> Aide</h4>
                        <hr>

                        <div class="alert alert-success" style="margin-bottom: 20px;">
                            <h5><i class="fa fa-star"></i> <strong>Mode Sans Restriction Activé</strong></h5>
                            <p>Le filtrage XSS est complètement désactivé pour ces champs. Vous avez un contrôle total sur le HTML et CSS !</p>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <h5><strong>HTML Complet Accepté</strong></h5>
                                <ul>
                                    <li><code>&lt;style&gt;...&lt;/style&gt;</code> - CSS intégré</li>
                                    <li><code>&lt;div&gt;, &lt;span&gt;</code> - Conteneurs avec classes et IDs</li>
                                    <li><code>style="..."</code> - Styles inline sans restriction</li>
                                    <li><code>&lt;h1&gt; à &lt;h6&gt;</code> - Tous les titres</li>
                                    <li><code>&lt;p&gt;, &lt;ul&gt;, &lt;ol&gt;, &lt;li&gt;</code> - Paragraphes et listes</li>
                                    <li><code>&lt;strong&gt;, &lt;em&gt;, &lt;u&gt;</code> - Mise en forme texte</li>
                                    <li><code>&lt;a href="..."&gt;</code> - Liens hypertextes</li>
                                    <li><code>&lt;table&gt;, &lt;tr&gt;, &lt;td&gt;</code> - Tableaux</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h5><strong>CSS Complet Accepté</strong></h5>
                                <ul>
                                    <li><code>margin, padding, border</code> - Espacements</li>
                                    <li><code>background, color</code> - Couleurs</li>
                                    <li><code>font-family, font-size</code> - Typographie</li>
                                    <li><code>display, position, flex</code> - Layout</li>
                                    <li><code>max-width, width, height</code> - Dimensions</li>
                                    <li><code>box-shadow, border-radius</code> - Effets visuels</li>
                                    <li><code>@media queries</code> - Responsive design</li>
                                    <li>Et tout autre propriété CSS valide !</li>
                                </ul>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 20px;">
                            <div class="col-md-12">
                                <h5><strong>Conseils de Contenu</strong></h5>
                                <ul>
                                    <li><i class="fa fa-building"></i> Incluez les informations de contact de votre organisation</li>
                                    <li><i class="fa fa-database"></i> Mentionnez comment les données sont collectées et utilisées</li>
                                    <li><i class="fa fa-shield"></i> Précisez les droits des utilisateurs (RGPD si applicable)</li>
                                    <li><i class="fa fa-calendar"></i> Mettez à jour la date de dernière modification</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
textarea {
    font-family: 'Courier New', monospace;
    font-size: 13px;
}

.alert-info a {
    color: #31708f;
    text-decoration: underline;
    font-weight: bold;
}

.alert-info a:hover {
    color: #245269;
}

code {
    background: #f4f4f4;
    padding: 2px 6px;
    border-radius: 3px;
    color: #c7254e;
    font-size: 90%;
}
</style>

<script>
/**
 * Base64 Encoding to Bypass WAF/ModSecurity
 * Encodes HTML/CSS content before form submission to avoid 403 Forbidden errors
 */
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('legal-pages-form');

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            // Get textarea values
            const privacyPolicy = document.getElementById('privacy_policy').value;
            const termsOfService = document.getElementById('terms_of_service').value;

            // Encode to Base64 (UTF-8 safe)
            const privacyPolicyEncoded = btoa(unescape(encodeURIComponent(privacyPolicy)));
            const termsOfServiceEncoded = btoa(unescape(encodeURIComponent(termsOfService)));

            // Set encoded values in hidden fields
            document.getElementById('privacy_policy_encoded').value = privacyPolicyEncoded;
            document.getElementById('terms_of_service_encoded').value = termsOfServiceEncoded;

            // Clear textarea values to avoid sending raw HTML
            document.getElementById('privacy_policy').value = '';
            document.getElementById('terms_of_service').value = '';

            // Submit form
            console.log('Submitting form with Base64 encoded content to bypass WAF...');
            form.submit();
        });
    }
});
</script>

<?php init_tail(); ?>
