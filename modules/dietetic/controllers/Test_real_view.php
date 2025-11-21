<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Test Real View - Charger la vraie vue avec capture d'erreurs
 */
class Test_real_view extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        // Enable FULL error reporting
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        ini_set('log_errors', 1);

        // Capture PHP errors
        set_error_handler(function($errno, $errstr, $errfile, $errline) {
            throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
        });

        echo '<h1>Test Real View - Chargement de list.php</h1>';
        echo '<style>body{font-family:Arial;padding:20px;} .success{color:green;} .error{color:red;} pre{background:#f5f5f5;padding:10px;border-radius:4px;overflow:auto;}</style>';

        // Load dependencies
        $this->load->helper('dietetic/dietetic');
        $this->load->model('dietetic/dietetic_patients_model');

        echo '<h2>Préparation des données...</h2>';
        try {
            $data['title'] = _l('dietetic_patients');
            $data['patients'] = $this->dietetic_patients_model->get_all();

            echo '<p class="success">✅ Données préparées: ' . count($data['patients']) . ' patients</p>';
        } catch (Exception $e) {
            echo '<p class="error">❌ Erreur préparation données: ' . htmlspecialchars($e->getMessage()) . '</p>';
            return;
        }

        echo '<h2>Tentative de chargement de la vue...</h2>';
        try {
            ob_start();
            $this->load->view('admin/patients/list', $data);
            $output = ob_get_clean();

            echo '<p class="success">✅ VUE CHARGÉE AVEC SUCCÈS!</p>';
            echo '<p>Longueur du HTML généré: ' . strlen($output) . ' caractères</p>';

            echo '<h3>Affichage de la vue:</h3>';
            echo '<hr>';
            echo $output;

        } catch (Exception $e) {
            ob_end_clean();
            echo '<p class="error">❌ ERREUR lors du chargement de la vue!</p>';
            echo '<p><strong>Type:</strong> ' . get_class($e) . '</p>';
            echo '<p><strong>Message:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '<p><strong>Fichier:</strong> ' . htmlspecialchars($e->getFile()) . '</p>';
            echo '<p><strong>Ligne:</strong> ' . $e->getLine() . '</p>';

            echo '<h3>Stack Trace:</h3>';
            echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';

            echo '<h3>Contexte du fichier (lignes autour de l\'erreur):</h3>';
            $file_lines = file($e->getFile());
            $error_line = $e->getLine();
            $start = max(0, $error_line - 10);
            $end = min(count($file_lines), $error_line + 10);

            echo '<pre>';
            for ($i = $start; $i < $end; $i++) {
                $line_number = $i + 1;
                $prefix = ($line_number == $error_line) ? '>>> ' : '    ';
                $style = ($line_number == $error_line) ? 'background:yellow;font-weight:bold;' : '';
                echo '<span style="' . $style . '">' . $prefix . str_pad($line_number, 4, ' ', STR_PAD_LEFT) . ' | ' . htmlspecialchars($file_lines[$i]) . '</span>';
            }
            echo '</pre>';
        } catch (Error $e) {
            ob_end_clean();
            echo '<p class="error">❌ ERREUR FATALE (Error)!</p>';
            echo '<p><strong>Message:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '<p><strong>Fichier:</strong> ' . htmlspecialchars($e->getFile()) . '</p>';
            echo '<p><strong>Ligne:</strong> ' . $e->getLine() . '</p>';
            echo '<h3>Stack Trace:</h3>';
            echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
        }

        // Restore error handler
        restore_error_handler();
    }
}
