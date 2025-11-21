<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Test_file_path extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        echo '<!DOCTYPE html><html><head><title>Test File Path</title>';
        echo '<style>body{font-family:Arial;padding:20px;background:#f5f5f5;} .box{background:white;padding:20px;margin:20px 0;border-radius:8px;box-shadow:0 2px 4px rgba(0,0,0,0.1);} .success{color:green;} .error{color:red;} pre{background:#f8f9fa;padding:15px;border-radius:4px;overflow:auto;max-height:400px;} code{background:#e9ecef;padding:2px 6px;border-radius:3px;}</style>';
        echo '</head><body>';

        echo '<h1>🔍 Diagnostic Chemin des Fichiers</h1>';

        echo '<div class="box">';
        echo '<h2>Chemins du système</h2>';
        echo '<p><strong>FCPATH:</strong> <code>' . FCPATH . '</code></p>';
        echo '<p><strong>APPPATH:</strong> <code>' . APPPATH . '</code></p>';
        echo '<p><strong>BASEPATH:</strong> <code>' . BASEPATH . '</code></p>';
        echo '</div>';

        echo '<div class="box">';
        echo '<h2>Fichier view.php des recettes</h2>';
        
        // Chemin attendu
        $expected_path = FCPATH . 'modules/dietetic/views/admin/recipes/view.php';
        echo '<p><strong>Chemin attendu:</strong><br><code>' . $expected_path . '</code></p>';
        
        if (file_exists($expected_path)) {
            echo '<p class="success">✅ Fichier existe</p>';
            echo '<p><strong>Taille:</strong> ' . filesize($expected_path) . ' bytes</p>';
            echo '<p><strong>Dernière modification:</strong> ' . date('Y-m-d H:i:s', filemtime($expected_path)) . '</p>';
            echo '<p><strong>Nombre de lignes:</strong> ' . count(file($expected_path)) . '</p>';
            
            // Check dernières lignes
            $lines = file($expected_path);
            $last_10_lines = array_slice($lines, -10);
            
            echo '<p><strong>Dernières 10 lignes du fichier:</strong></p>';
            echo '<pre>' . htmlspecialchars(implode('', $last_10_lines)) . '</pre>';
            
            // Check si JavaScript est présent
            $content = file_get_contents($expected_path);
            
            if (strpos($content, 'init_tail()') !== false) {
                echo '<p class="success">✅ init_tail() trouvé dans le fichier</p>';
            } else {
                echo '<p class="error">❌ init_tail() NON trouvé!</p>';
            }
            
            if (strpos($content, '[Recipe View] Initialisation du formulaire') !== false) {
                echo '<p class="success">✅ JavaScript de chargement des patients trouvé</p>';
            } else {
                echo '<p class="error">❌ JavaScript NON trouvé!</p>';
            }
            
        } else {
            echo '<p class="error">❌ Fichier n\'existe pas à cet emplacement!</p>';
        }
        echo '</div>';

        echo '<div class="box">';
        echo '<h2>Test de chargement de la vue</h2>';
        echo '<p>Essayons de charger la vue comme le fait le contrôleur:</p>';
        
        try {
            // Simulate what the controller does
            $view_path = 'admin/recipes/view';
            
            // Get the full path that CI will use
            $ci_view_path = APPPATH . 'modules/dietetic/views/' . $view_path . '.php';
            echo '<p><strong>Chemin CI (via APPPATH):</strong><br><code>' . $ci_view_path . '</code></p>';
            
            if (file_exists($ci_view_path)) {
                echo '<p class="success">✅ Fichier trouvé via APPPATH</p>';
                echo '<p><strong>Taille:</strong> ' . filesize($ci_view_path) . ' bytes</p>';
                echo '<p><strong>Nombre de lignes:</strong> ' . count(file($ci_view_path)) . '</p>';
            } else {
                echo '<p class="error">❌ Fichier non trouvé via APPPATH</p>';
            }
            
            // Try the other common path
            $modules_view_path = FCPATH . 'modules/dietetic/views/' . $view_path . '.php';
            echo '<p><strong>Chemin modules (via FCPATH):</strong><br><code>' . $modules_view_path . '</code></p>';
            
            if (file_exists($modules_view_path)) {
                echo '<p class="success">✅ Fichier trouvé via FCPATH/modules</p>';
                echo '<p><strong>Taille:</strong> ' . filesize($modules_view_path) . ' bytes</p>';
                echo '<p><strong>Nombre de lignes:</strong> ' . count(file($modules_view_path)) . '</p>';
            } else {
                echo '<p class="error">❌ Fichier non trouvé via FCPATH/modules</p>';
            }
            
        } catch (Exception $e) {
            echo '<p class="error">❌ Erreur: ' . $e->getMessage() . '</p>';
        }
        echo '</div>';

        echo '<div class="box">';
        echo '<h2>Tous les fichiers view.php pour recettes</h2>';
        echo '<p>Recherche de tous les fichiers view.php liés aux recettes:</p>';
        
        $search_paths = [
            FCPATH . 'modules/dietetic/views/admin/recipes/view.php',
            APPPATH . 'views/admin/recipes/view.php',
            APPPATH . 'modules/dietetic/views/admin/recipes/view.php',
            FCPATH . 'application/views/admin/recipes/view.php',
        ];
        
        foreach ($search_paths as $path) {
            echo '<p><code>' . $path . '</code><br>';
            if (file_exists($path)) {
                echo '<span class="success">✅ Existe (' . filesize($path) . ' bytes, ' . count(file($path)) . ' lignes)</span>';
            } else {
                echo '<span class="error">❌ N\'existe pas</span>';
            }
            echo '</p>';
        }
        echo '</div>';

        echo '</body></html>';
    }
}
