<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
.install-container {
    max-width: 800px;
    margin: 50px auto;
    padding: 40px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.install-header {
    text-align: center;
    margin-bottom: 40px;
}

.install-header i {
    font-size: 64px;
    color: #01807B;
    margin-bottom: 20px;
}

.install-header h1 {
    color: #2d3748;
    font-size: 32px;
    margin-bottom: 10px;
}

.install-header p {
    color: #718096;
    font-size: 16px;
}

.feature-list {
    background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
    padding: 30px;
    border-radius: 8px;
    margin-bottom: 30px;
    border-left: 4px solid #01807B;
}

.feature-list h3 {
    color: #01807B;
    margin-bottom: 20px;
    font-size: 20px;
}

.feature-list ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.feature-list li {
    padding: 10px 0;
    color: #2d3748;
    display: flex;
    align-items: center;
    gap: 10px;
}

.feature-list li i {
    color: #48bb78;
    font-size: 18px;
}

.install-info {
    background: #fff3cd;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 30px;
    border-left: 4px solid #ffc107;
}

.install-info h4 {
    margin-top: 0;
    color: #856404;
}

.install-info p {
    margin-bottom: 0;
    color: #856404;
    line-height: 1.6;
}

.install-actions {
    text-align: center;
    padding-top: 20px;
}

.btn-install {
    background: linear-gradient(135deg, #01807B 0%, #026660 100%);
    color: white;
    padding: 15px 50px;
    font-size: 18px;
    font-weight: 600;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-install:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px rgba(0, 0, 0, 0.2);
    color: white;
}

.requirements {
    background: #e3f2fd;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 30px;
    border-left: 4px solid #4299e1;
}

.requirements h4 {
    margin-top: 0;
    color: #2c5282;
}

.requirements ul {
    margin: 0;
    padding-left: 20px;
    color: #2c5282;
}
</style>

<div id="wrapper">
    <div class="content">
        <div class="install-container">
            <div class="install-header">
                <i class="fa fa-camera"></i>
                <h1>Installation Enquêtes Alimentaires</h1>
                <p>Installez les tables nécessaires pour activer la fonctionnalité d'enquêtes alimentaires</p>
            </div>

            <div class="feature-list">
                <h3>🎯 Fonctionnalités incluses</h3>
                <ul>
                    <li>
                        <i class="fa fa-check-circle"></i>
                        <span>Création d'enquêtes alimentaires personnalisées par les diététiciens</span>
                    </li>
                    <li>
                        <i class="fa fa-check-circle"></i>
                        <span>Upload de 3 photos de repas par jour (petit-déjeuner, déjeuner, dîner)</span>
                    </li>
                    <li>
                        <i class="fa fa-check-circle"></i>
                        <span>Suivi de la consommation d'eau et de boissons</span>
                    </li>
                    <li>
                        <i class="fa fa-check-circle"></i>
                        <span>Système de recommandations personnalisées du diététicien</span>
                    </li>
                    <li>
                        <i class="fa fa-check-circle"></i>
                        <span>Commentaires et échanges entre patient et diététicien</span>
                    </li>
                    <li>
                        <i class="fa fa-check-circle"></i>
                        <span>Calcul automatique de la progression de l'enquête</span>
                    </li>
                    <li>
                        <i class="fa fa-check-circle"></i>
                        <span>Interface mobile-first responsive et moderne</span>
                    </li>
                </ul>
            </div>

            <div class="requirements">
                <h4><i class="fa fa-info-circle"></i> Prérequis</h4>
                <ul>
                    <li>Perfex CRM avec module Dietetic installé</li>
                    <li>PHP GD extension (pour la génération de miniatures)</li>
                    <li>Permissions d'écriture sur le dossier uploads/</li>
                </ul>
            </div>

            <div class="install-info">
                <h4><i class="fa fa-database"></i> Ce qui sera installé</h4>
                <p>
                    Cette installation créera <strong>5 tables</strong> dans votre base de données :
                </p>
                <ul style="margin-top: 10px; margin-bottom: 0;">
                    <li>tbldietic_food_surveys (enquêtes principales)</li>
                    <li>tbldietic_food_survey_entries (entrées quotidiennes)</li>
                    <li>tbldietic_food_survey_beverages (boissons consommées)</li>
                    <li>tbldietic_food_survey_recommendations (recommandations diététicien)</li>
                    <li>tbldietic_food_survey_comments (commentaires patients)</li>
                </ul>
                <p style="margin-top: 15px; margin-bottom: 0;">
                    Le répertoire <code>uploads/dietetic/food_surveys/</code> sera également créé pour stocker les photos.
                </p>
            </div>

            <form method="post" action="<?php echo admin_url('dietetic/food_surveys/install_tables'); ?>">
                <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                <input type="hidden" name="do_install" value="1">

                <div class="install-actions">
                    <button type="submit" class="btn btn-primary btn-install" onclick="return confirm('Êtes-vous sûr de vouloir installer les tables Food Surveys ?');">
                        <i class="fa fa-rocket"></i>
                        Installer maintenant
                    </button>
                </div>
            </form>

            <div style="text-align: center; margin-top: 30px; color: #718096; font-size: 14px;">
                <i class="fa fa-clock-o"></i> L'installation prend environ 10 secondes
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
