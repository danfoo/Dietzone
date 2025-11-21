<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Clear_cache extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if (!is_admin()) {
            access_denied('Clear Cache');
        }

        echo "<!DOCTYPE html><html><head><title>Supprimer Cache</title>";
        echo "<style>body{font-family:Arial;padding:20px;background:#f5f5f5;} .success{color:#27ae60;font-weight:bold;} .error{color:#e74c3c;font-weight:bold;} .warning{color:#f39c12;font-weight:bold;} .section{background:white;padding:30px;margin:20px 0;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,0.1);} h2{color:#2c3e50;border-bottom:2px solid #e74c3c;padding-bottom:10px;} .btn{display:inline-block;background:#e74c3c;color:white;padding:15px 30px;text-decoration:none;border-radius:5px;margin:10px;font-size:16px;border:none;cursor:pointer;} .btn:hover{background:#c0392b;}</style>";
        echo "</head><body>";

        echo "<h1>🗑️ Suppression du Cache Perfex</h1>";

        $cache_dir = FCPATH . 'application/cache/';
        $deleted_count = 0;
        $errors = [];

        echo "<div class='section'>";
        echo "<h2>Suppression des fichiers de cache</h2>";

        if (!is_dir($cache_dir)) {
            echo "<p class='error'>❌ Dossier cache non trouvé: $cache_dir</p>";
        } else {
            echo "<p class='success'>✅ Dossier cache trouvé</p>";

            // Lister tous les fichiers
            $files = glob($cache_dir . '*');

            if (empty($files)) {
                echo "<p class='warning'>⚠️ Aucun fichier de cache trouvé (déjà vide)</p>";
            } else {
                echo "<p><strong>Fichiers trouvés:</strong> " . count($files) . "</p>";

                foreach ($files as $file) {
                    if (is_file($file)) {
                        $filename = basename($file);

                        // Ne pas supprimer index.html
                        if ($filename === 'index.html' || $filename === '.htaccess') {
                            continue;
                        }

                        if (unlink($file)) {
                            $deleted_count++;
                            echo "<p class='success'>✅ Supprimé: $filename</p>";
                        } else {
                            $errors[] = $filename;
                            echo "<p class='error'>❌ Impossible de supprimer: $filename</p>";
                        }
                    }
                }

                echo "<hr>";
                echo "<p class='success' style='font-size:18px;'>✅ $deleted_count fichiers supprimés</p>";

                if (!empty($errors)) {
                    echo "<p class='error'>❌ " . count($errors) . " erreurs</p>";
                }
            }
        }
        echo "</div>";

        // Clear app cache via Perfex
        echo "<div class='section'>";
        echo "<h2>Clear Cache Perfex (via API)</h2>";

        try {
            $this->app->clear_all_cache();
            echo "<p class='success'>✅ Cache Perfex vidé via API</p>";
        } catch (Exception $e) {
            echo "<p class='error'>❌ Erreur: " . $e->getMessage() . "</p>";
        }
        echo "</div>";

        // Vérification
        echo "<div class='section'>";
        echo "<h2>Vérification</h2>";

        $remaining = glob($cache_dir . '*');
        $remaining_count = 0;
        foreach ($remaining as $file) {
            if (is_file($file) && basename($file) !== 'index.html' && basename($file) !== '.htaccess') {
                $remaining_count++;
            }
        }

        if ($remaining_count === 0) {
            echo "<p class='success' style='font-size:20px;'>✅ CACHE COMPLÈTEMENT VIDE!</p>";
        } else {
            echo "<p class='warning'>⚠️ $remaining_count fichiers restants</p>";
        }
        echo "</div>";

        // Instructions suivantes
        echo "<div class='section' style='background:#fff3cd;border:2px solid #f39c12;'>";
        echo "<h2>📋 ÉTAPES SUIVANTES (TRÈS IMPORTANT)</h2>";
        echo "<ol style='font-size:16px;line-height:1.8;'>";
        echo "<li><strong>Déconnectez-vous</strong> de l'interface admin (cliquez sur votre nom → Logout)</li>";
        echo "<li><strong>Fermez TOUS les onglets</strong> de votre navigateur</li>";
        echo "<li><strong>Videz le cache du navigateur:</strong><br>";
        echo "   - Chrome/Edge: Ctrl+Shift+Delete → Cochez tout → Effacer<br>";
        echo "   - Firefox: Ctrl+Shift+Delete → Cochez tout → Effacer<br>";
        echo "   - Safari: Cmd+Option+E</li>";
        echo "<li><strong>Redémarrez le navigateur complètement</strong></li>";
        echo "<li><strong>Reconnectez-vous</strong> à l'interface admin</li>";
        echo "<li><strong>Vérifiez le menu Dietetic</strong> dans la sidebar</li>";
        echo "</ol>";
        echo "<hr>";
        echo "<p style='font-size:18px;font-weight:bold;color:#e74c3c;'>";
        echo "⚠️ Si le menu n'apparaît TOUJOURS PAS après ces étapes, ";
        echo "le problème est que le hook admin_init ne s'exécute PAS.";
        echo "</p>";
        echo "</div>";

        echo "<div class='section' style='text-align:center;'>";
        echo "<a href='" . admin_url('authentication/logout') . "' class='btn'>🚪 SE DÉCONNECTER MAINTENANT</a>";
        echo "</div>";

        echo "</body></html>";
    }
}
