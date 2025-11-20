<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Force_menu extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if (!is_admin()) {
            access_denied('Force Menu');
        }

        echo "<!DOCTYPE html><html><head><title>Force Menu</title>";
        echo "<style>body{font-family:Arial;padding:20px;background:#f5f5f5;} .success{color:#27ae60;font-weight:bold;font-size:18px;} .error{color:#e74c3c;font-weight:bold;} .section{background:white;padding:30px;margin:20px 0;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,0.1);text-align:center;} h1{color:#2c3e50;} .btn{display:inline-block;background:#3498db;color:white;padding:15px 30px;text-decoration:none;border-radius:5px;margin:10px;font-size:16px;} .btn:hover{background:#2980b9;}</style>";
        echo "</head><body>";

        echo "<h1>⚡ Forcer l'ajout du menu Recettes</h1>";

        echo "<div class='section'>";
        echo "<h2>Ajout MANUEL du menu</h2>";

        try {
            $CI = &get_instance();

            // Forcer l'ajout du menu directement
            $CI->app_menu->add_sidebar_children_item('dietetic', [
                'slug'     => 'dietetic-recipes',
                'name'     => '📚 Recettes (FORCÉ)',
                'icon'     => 'fa fa-book',
                'href'     => admin_url('dietetic/recipes'),
                'position' => 7.5,
            ]);

            echo "<p class='success'>✅ MENU AJOUTÉ AVEC SUCCÈS!</p>";
            echo "<p style='font-size:16px;'>Le menu '📚 Recettes (FORCÉ)' a été ajouté à la sidebar.</p>";
            echo "<hr>";
            echo "<p><strong>REGARDEZ MAINTENANT dans la sidebar à gauche!</strong></p>";
            echo "<p>Vous devriez voir '📚 Recettes (FORCÉ)' dans le menu Dietetic</p>";
            echo "<hr>";
            echo "<a href='" . admin_url('dietetic/recipes') . "' class='btn'>Aller à la page Recettes</a>";

        } catch (Exception $e) {
            echo "<p class='error'>❌ ERREUR: " . $e->getMessage() . "</p>";
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
        }

        echo "</div>";

        echo "</body></html>";
    }
}
