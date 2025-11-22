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

                        <?php echo form_open(admin_url('dietetic/legal_pages/manage')); ?>

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
                                        <small class="text-muted">(HTML accepté)</small>
                                    </label>
                                    <?php echo render_textarea('privacy_policy', '', $privacy_policy, [
                                        'rows' => 15,
                                        'class' => 'form-control'
                                    ]); ?>
                                    <small class="text-muted">
                                        Vous pouvez utiliser du HTML pour formater le contenu (balises &lt;h1&gt;, &lt;p&gt;, &lt;ul&gt;, &lt;li&gt;, etc.)
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
                                        <small class="text-muted">(HTML accepté)</small>
                                    </label>
                                    <?php echo render_textarea('terms_of_service', '', $terms_of_service, [
                                        'rows' => 15,
                                        'class' => 'form-control'
                                    ]); ?>
                                    <small class="text-muted">
                                        Vous pouvez utiliser du HTML pour formater le contenu (balises &lt;h1&gt;, &lt;p&gt;, &lt;ul&gt;, &lt;li&gt;, etc.)
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

                        <div class="row">
                            <div class="col-md-6">
                                <h5><strong>Format HTML accepté</strong></h5>
                                <ul>
                                    <li><code>&lt;h1&gt;...&lt;/h1&gt;</code> - Titre principal</li>
                                    <li><code>&lt;h2&gt;...&lt;/h2&gt;</code> - Sous-titre</li>
                                    <li><code>&lt;p&gt;...&lt;/p&gt;</code> - Paragraphe</li>
                                    <li><code>&lt;ul&gt;&lt;li&gt;...&lt;/li&gt;&lt;/ul&gt;</code> - Liste à puces</li>
                                    <li><code>&lt;strong&gt;...&lt;/strong&gt;</code> - Texte en gras</li>
                                    <li><code>&lt;a href="..."&gt;...&lt;/a&gt;</code> - Lien</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h5><strong>Conseils</strong></h5>
                                <ul>
                                    <li>Incluez les informations de contact de votre organisation</li>
                                    <li>Mentionnez comment les données sont collectées et utilisées</li>
                                    <li>Précisez les droits des utilisateurs (RGPD si applicable)</li>
                                    <li>Mettez à jour la date de dernière modification</li>
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

<?php init_tail(); ?>
