<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Test Final Diagnosis - Diagnostic complet
 */
class Test_final_diagnosis extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        echo '<h1>Diagnostic Complet Final</h1>';
        echo '<style>body{font-family:Arial;padding:20px;} .success{color:green;} .error{color:red;} .warning{color:orange;} pre{background:#f5f5f5;padding:10px;border-radius:4px;} table{border-collapse:collapse;margin:20px 0;} th,td{border:1px solid #ddd;padding:8px;}</style>';

        $this->load->helper('dietetic/dietetic');
        $this->load->model('dietetic/dietetic_patients_model');

        echo '<h2>Test 1: Page /admin/dietetic/patients</h2>';
        echo '<p><a href="' . admin_url('dietetic/patients') . '" target="_blank">Cliquez ici pour ouvrir la page patients</a></p>';
        echo '<p class="warning">⚠️ Vérifiez si la page se charge ou si vous avez une erreur 500</p>';

        echo '<h2>Test 2: Données patients disponibles</h2>';
        $patients = $this->dietetic_patients_model->get_all();
        echo '<p>Nombre de patients: <strong>' . count($patients) . '</strong></p>';

        if (count($patients) > 0) {
            echo '<table>';
            echo '<tr><th>ID</th><th>Nom</th><th>Email</th><th>Diététicien</th></tr>';
            foreach ($patients as $p) {
                echo '<tr>';
                echo '<td>' . $p->id . '</td>';
                echo '<td>' . htmlspecialchars($p->client_name) . '</td>';
                echo '<td>' . htmlspecialchars($p->email) . '</td>';
                echo '<td>' . htmlspecialchars($p->dietitian_name) . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        }

        echo '<h2>Test 3: Test AJAX get_patients() directement</h2>';
        echo '<button id="test-ajax" class="btn btn-primary">Tester le chargement AJAX des patients</button>';
        echo '<div id="ajax-result" style="margin-top:10px;"></div>';

        echo '<h2>Test 4: Vérifier les fichiers modifiés</h2>';

        $files_to_check = [
            'modules/dietetic/views/admin/patients/list.php' => 'Vue liste patients',
            'modules/dietetic/controllers/Recipes.php' => 'Contrôleur Recipes',
            'modules/dietetic/views/admin/recipes/view.php' => 'Vue recette'
        ];

        echo '<table>';
        echo '<tr><th>Fichier</th><th>Description</th><th>Existe?</th><th>Modifié récemment?</th></tr>';
        foreach ($files_to_check as $file => $desc) {
            $fullpath = FCPATH . $file;
            $exists = file_exists($fullpath);
            $modified = $exists ? date('Y-m-d H:i:s', filemtime($fullpath)) : 'N/A';

            echo '<tr>';
            echo '<td>' . htmlspecialchars($file) . '</td>';
            echo '<td>' . $desc . '</td>';
            echo '<td>' . ($exists ? '<span class="success">✅ Oui</span>' : '<span class="error">❌ Non</span>') . '</td>';
            echo '<td>' . $modified . '</td>';
            echo '</tr>';
        }
        echo '</table>';

        echo '<h2>Test 5: Vérifier le code de get_patients()</h2>';
        $recipes_controller = file_get_contents(FCPATH . 'modules/dietetic/controllers/Recipes.php');
        if (strpos($recipes_controller, 'header(\'Content-Type: application/json\')') !== false) {
            echo '<p class="success">✅ Header JSON trouvé dans get_patients()</p>';
        } else {
            echo '<p class="error">❌ Header JSON MANQUANT dans get_patients()</p>';
        }

        echo '<h2>Test 6: Vérifier le code JavaScript de view.php</h2>';
        $view_file = file_get_contents(FCPATH . 'modules/dietetic/views/admin/recipes/view.php');
        if (strpos($view_file, 'dataType: \'json\'') !== false) {
            echo '<p class="success">✅ AJAX avec dataType: json trouvé</p>';
        } else {
            echo '<p class="error">❌ AJAX avec dataType: json MANQUANT</p>';
        }

        if (strpos($view_file, 'console.log(\'[Recipe View]') !== false) {
            echo '<p class="success">✅ Logs console [Recipe View] trouvés</p>';
        } else {
            echo '<p class="error">❌ Logs console [Recipe View] MANQUANTS</p>';
        }

        echo '<h2>Instructions:</h2>';
        echo '<ol>';
        echo '<li>Testez le bouton AJAX ci-dessus</li>';
        echo '<li>Ouvrez la console (F12 → Console) et regardez les messages</li>';
        echo '<li>Ouvrez l\'onglet Network (F12 → Réseau) et surveillez les requêtes</li>';
        echo '<li>Essayez d\'ouvrir /admin/dietetic/patients</li>';
        echo '<li>Copiez-moi TOUS les messages de la console ET les résultats de ce diagnostic</li>';
        echo '</ol>';

        ?>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
        $(document).ready(function() {
            $('#test-ajax').click(function() {
                console.log('[Test] Début du test AJAX');
                $('#ajax-result').html('<p class="warning">⏳ Chargement...</p>');

                $.ajax({
                    url: '<?php echo admin_url('dietetic/recipes/get_patients'); ?>',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        console.log('[Test] Succès! Données reçues:', response);

                        var html = '<p class="success">✅ AJAX réussi! Patients reçus: ' + response.length + '</p>';
                        html += '<table>';
                        html += '<tr><th>ID</th><th>Nom</th><th>Email</th></tr>';

                        response.forEach(function(patient) {
                            html += '<tr>';
                            html += '<td>' + patient.id + '</td>';
                            html += '<td>' + patient.name + '</td>';
                            html += '<td>' + patient.email + '</td>';
                            html += '</tr>';
                        });

                        html += '</table>';
                        $('#ajax-result').html(html);
                    },
                    error: function(xhr, status, error) {
                        console.error('[Test] Erreur AJAX:', status, error);
                        console.error('[Test] Response:', xhr.responseText);

                        var html = '<p class="error">❌ Erreur AJAX!</p>';
                        html += '<p>Status: ' + status + '</p>';
                        html += '<p>Error: ' + error + '</p>';
                        html += '<p>Response Text:</p>';
                        html += '<pre>' + xhr.responseText + '</pre>';

                        $('#ajax-result').html(html);
                    }
                });
            });

            console.log('[Test] Page de diagnostic chargée. Cliquez sur le bouton pour tester AJAX.');
        });
        </script>
        <?php
    }
}
