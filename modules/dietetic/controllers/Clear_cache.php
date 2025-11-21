<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Clear Cache - Vider le cache OPcache
 */
class Clear_cache extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        echo '<!DOCTYPE html><html><head><title>Clear Cache</title>';
        echo '<style>body{font-family:Arial;padding:20px;background:#f5f5f5;} .box{background:white;padding:20px;margin:20px 0;border-radius:8px;box-shadow:0 2px 4px rgba(0,0,0,0.1);} .success{color:green;font-weight:bold;} .error{color:red;font-weight:bold;} pre{background:#f8f9fa;padding:15px;border-radius:4px;overflow:auto;} button{background:#e74c3c;color:white;padding:15px 30px;border:none;border-radius:4px;cursor:pointer;font-size:16px;margin:10px 0;} button:hover{background:#c0392b;}</style>';
        echo '</head><body>';

        echo '<h1>🧹 Clear PHP Cache</h1>';

        echo '<div class="box">';
        echo '<h2>Cache PHP / OPcache</h2>';

        // Check if OPcache is enabled
        if (function_exists('opcache_get_status')) {
            $status = opcache_get_status();
            
            echo '<p><strong>OPcache Status:</strong> ';
            if ($status !== false) {
                echo '<span class="success">✅ ENABLED</span></p>';
                
                echo '<p><strong>Memory Usage:</strong> ' . round($status['memory_usage']['used_memory'] / 1024 / 1024, 2) . ' MB / ' . round($status['memory_usage']['free_memory'] / 1024 / 1024, 2) . ' MB free</p>';
                echo '<p><strong>Cached Scripts:</strong> ' . $status['opcache_statistics']['num_cached_scripts'] . '</p>';
                echo '<p><strong>Hits:</strong> ' . $status['opcache_statistics']['hits'] . '</p>';
                echo '<p><strong>Misses:</strong> ' . $status['opcache_statistics']['misses'] . '</p>';
            } else {
                echo '<span class="error">❌ DISABLED or not working</span></p>';
            }
        } else {
            echo '<p><span class="error">❌ OPcache not available</span></p>';
        }

        echo '<form method="post">';
        echo '<button type="submit" name="clear_opcache">🧹 Vider le cache OPcache</button>';
        echo '</form>';

        // Handle cache clear
        if (isset($_POST['clear_opcache'])) {
            echo '<div style="margin-top:20px;padding:15px;background:#d4edda;border:1px solid #c3e6cb;border-radius:4px;">';
            
            if (function_exists('opcache_reset')) {
                if (opcache_reset()) {
                    echo '<p class="success">✅ OPcache vidé avec succès!</p>';
                } else {
                    echo '<p class="error">❌ Impossible de vider OPcache (permissions?)</p>';
                }
            } else {
                echo '<p class="error">❌ opcache_reset() non disponible</p>';
            }
            
            echo '</div>';
        }

        echo '</div>';

        // File check
        echo '<div class="box">';
        echo '<h2>Vérification du fichier view.php</h2>';
        
        $view_file = FCPATH . 'modules/dietetic/views/admin/recipes/view.php';
        
        if (file_exists($view_file)) {
            echo '<p class="success">✅ Fichier existe</p>';
            echo '<p><strong>Dernière modification:</strong> ' . date('Y-m-d H:i:s', filemtime($view_file)) . '</p>';
            echo '<p><strong>Taille:</strong> ' . round(filesize($view_file) / 1024, 2) . ' KB</p>';
            
            // Check if the new JavaScript is present
            $content = file_get_contents($view_file);
            
            if (strpos($content, '[Recipe View] Initialisation du formulaire') !== false) {
                echo '<p class="success">✅ Nouveau JavaScript détecté dans le fichier</p>';
            } else {
                echo '<p class="error">❌ Nouveau JavaScript NON trouvé dans le fichier!</p>';
            }
            
            if (strpos($content, 'Assigner la Recette à un Patient') !== false) {
                echo '<p class="success">✅ Nouveau HTML (formulaire inline) détecté</p>';
            } else {
                echo '<p class="error">❌ Nouveau HTML NON trouvé!</p>';
            }
        } else {
            echo '<p class="error">❌ Fichier non trouvé: ' . $view_file . '</p>';
        }
        
        echo '</div>';

        echo '<div class="box">';
        echo '<h2>📋 Instructions</h2>';
        echo '<ol>';
        echo '<li>Cliquez sur le bouton "Vider le cache OPcache" ci-dessus</li>';
        echo '<li>Attendez la confirmation</li>';
        echo '<li>Retournez sur la page de recette: <a href="' . admin_url('dietetic/recipes/view/7') . '" target="_blank">View Recipe</a></li>';
        echo '<li>Videz le cache du navigateur (Ctrl+Shift+R)</li>';
        echo '<li>Vérifiez si les patients se chargent maintenant</li>';
        echo '</ol>';
        echo '</div>';

        echo '</body></html>';
    }
}
