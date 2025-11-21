<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Check_code extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if (!is_admin()) {
            access_denied('Check Code');
        }

        echo "<!DOCTYPE html><html><head><title>Vérification Code</title>";
        echo "<style>body{font-family:Arial;padding:20px;background:#f5f5f5;} .success{color:#27ae60;font-weight:bold;} .error{color:#e74c3c;font-weight:bold;font-size:18px;} .warning{color:#f39c12;font-weight:bold;} .section{background:white;padding:30px;margin:20px 0;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,0.1);} h2{color:#2c3e50;border-bottom:2px solid #e74c3c;padding-bottom:10px;} pre{background:#f8f9fa;padding:15px;border-left:4px solid #3498db;overflow-x:auto;}</style>";
        echo "</head><body>";

        echo "<h1>🔍 Vérification du Code sur le Serveur</h1>";

        // Vérification 1: Lire le fichier dietetic.php
        echo "<div class='section'>";
        echo "<h2>1. Code actuel dans dietetic.php</h2>";

        $dietetic_file = FCPATH . 'modules/dietetic/dietetic.php';
        if (!file_exists($dietetic_file)) {
            echo "<p class='error'>❌ Fichier dietetic.php n'existe pas!</p>";
        } else {
            $content = file_get_contents($dietetic_file);

            // Chercher la ligne du menu Recettes
            if (strpos($content, 'Bibliothèque de Recettes') === false) {
                echo "<p class='error'>❌ Le code du menu Recettes n'est PAS présent dans le fichier!</p>";
                echo "<p class='warning'>⚠️ Votre serveur n'a PAS les derniers commits!</p>";
            } else {
                echo "<p class='success'>✅ Le code du menu 'Bibliothèque de Recettes' est présent</p>";

                // Vérifier si c'est avec ou sans condition
                if (strpos($content, 'FORCÉ sans condition') !== false) {
                    echo "<p class='success'>✅ Version FORCÉE (sans condition table_exists)</p>";
                } elseif (preg_match('/table_exists.*dietic_recipes.*Bibliothèque de Recettes/s', $content)) {
                    echo "<p class='warning'>⚠️ Version ANCIENNE (avec condition table_exists)</p>";
                    echo "<p class='error'>Vous devez faire un git pull!</p>";
                }

                // Afficher les lignes concernées
                $lines = explode("\n", $content);
                $found = false;
                echo "<p><strong>Lignes 170-185:</strong></p>";
                echo "<pre>";
                for ($i = 169; $i < 185 && $i < count($lines); $i++) {
                    echo ($i + 1) . ": " . htmlspecialchars($lines[$i]) . "\n";
                    if (strpos($lines[$i], 'Bibliothèque de Recettes') !== false) {
                        $found = true;
                    }
                }
                echo "</pre>";
            }
        }
        echo "</div>";

        // Vérification 2: Dernier commit
        echo "<div class='section'>";
        echo "<h2>2. Information Git</h2>";

        $git_dir = FCPATH . '.git';
        if (is_dir($git_dir)) {
            echo "<p class='success'>✅ Dépôt Git trouvé</p>";

            // Lire HEAD
            $head_file = $git_dir . '/HEAD';
            if (file_exists($head_file)) {
                $head = trim(file_get_contents($head_file));
                echo "<p><strong>HEAD actuel:</strong> <code>" . htmlspecialchars($head) . "</code></p>";
            }

            // Essayer de lire le dernier commit
            exec('cd ' . escapeshellarg(FCPATH) . ' && git log -1 --oneline 2>&1', $output, $return_var);
            if ($return_var === 0 && !empty($output)) {
                echo "<p><strong>Dernier commit:</strong></p>";
                echo "<pre>" . htmlspecialchars(implode("\n", $output)) . "</pre>";
            } else {
                echo "<p class='warning'>⚠️ Impossible d'exécuter git log (normal si git n'est pas disponible)</p>";
            }

            // Lire le dernier commit hash
            exec('cd ' . escapeshellarg(FCPATH) . ' && git rev-parse HEAD 2>&1', $output2, $return_var2);
            if ($return_var2 === 0 && !empty($output2)) {
                echo "<p><strong>Commit hash:</strong> <code>" . htmlspecialchars($output2[0]) . "</code></p>";
            }

        } else {
            echo "<p class='warning'>⚠️ Pas de dépôt Git trouvé (normal en production)</p>";
        }
        echo "</div>";

        // Vérification 3: Fichiers récents
        echo "<div class='section'>";
        echo "<h2>3. Fichiers modifiés récemment</h2>";

        $files_to_check = [
            'modules/dietetic/dietetic.php',
            'modules/dietetic/controllers/Recipes.php',
            'modules/dietetic/models/Dietetic_recipes_model.php',
            'modules/dietetic/views/admin/recipes/form.php',
        ];

        foreach ($files_to_check as $file) {
            $full_path = FCPATH . $file;
            if (file_exists($full_path)) {
                $mtime = filemtime($full_path);
                $time_ago = time() - $mtime;
                $minutes_ago = round($time_ago / 60);

                if ($minutes_ago < 30) {
                    echo "<p class='success'>✅ $file - Modifié il y a $minutes_ago minutes</p>";
                } elseif ($minutes_ago < 1440) {
                    $hours_ago = round($minutes_ago / 60);
                    echo "<p class='warning'>⚠️ $file - Modifié il y a $hours_ago heures</p>";
                } else {
                    $days_ago = round($minutes_ago / 1440);
                    echo "<p class='error'>❌ $file - Modifié il y a $days_ago jours</p>";
                }
                echo "<small style='margin-left:20px;'>Date: " . date('Y-m-d H:i:s', $mtime) . "</small><br>";
            } else {
                echo "<p class='error'>❌ $file - N'existe PAS</p>";
            }
        }
        echo "</div>";

        // SOLUTION
        echo "<div class='section' style='background:#fff3cd;border:2px solid #f39c12;'>";
        echo "<h2>🔧 SOLUTION</h2>";

        if (strpos(file_get_contents($dietetic_file), 'FORCÉ sans condition') === false) {
            echo "<p class='error' style='font-size:18px;'>❌ Votre serveur n'a PAS les derniers commits!</p>";
            echo "<p><strong>Vous DEVEZ faire un git pull:</strong></p>";
            echo "<pre style='background:#2c3e50;color:#ecf0f1;padding:15px;'>";
            echo "cd /home/trpuftja/app\n";
            echo "git pull origin claude/continue-dietzone-project-01S24KTSE5AMoww38BXPw6r8\n";
            echo "</pre>";
            echo "<p>OU accéder à votre serveur via SSH et exécuter ces commandes.</p>";
        } else {
            echo "<p class='success'>✅ Le code est à jour!</p>";
            echo "<p>Le problème du menu est probablement le CACHE de Perfex.</p>";
            echo "<p><strong>Actions à faire:</strong></p>";
            echo "<ol>";
            echo "<li>Setup → Settings → General → <strong>Clear All Cache</strong></li>";
            echo "<li>Supprimer le dossier: <code>application/cache/</code></li>";
            echo "<li>Déconnexion complète</li>";
            echo "<li>Reconnexion</li>";
            echo "</ol>";
        }
        echo "</div>";

        echo "</body></html>";
    }
}
