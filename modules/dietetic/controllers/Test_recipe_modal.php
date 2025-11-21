<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Test Recipe Modal - Diagnostic dropdown assignation
 */
class Test_recipe_modal extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('dietetic/dietetic');
        $this->load->model('dietetic/dietetic_recipes_model');
    }

    public function index()
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        echo '<!DOCTYPE html><html><head>';
        echo '<title>Test Recipe Modal</title>';
        echo '<link href="' . base_url('assets/css/bootstrap.css') . '" rel="stylesheet">';
        echo '<link href="' . base_url('assets/plugins/bootstrap-select/css/bootstrap-select.min.css') . '" rel="stylesheet">';
        echo '<style>body{font-family:Arial;padding:20px;} .success{color:green;} .error{color:red;} .warning{color:orange;} pre{background:#f5f5f5;padding:10px;border-radius:4px;overflow:auto;max-height:400px;} table{border-collapse:collapse;margin:20px 0;} th,td{border:1px solid #ddd;padding:8px;text-align:left;} th{background:#f0f0f0;}</style>';
        echo '</head><body>';

        echo '<h1>Diagnostic Recipe Modal & Dropdown</h1>';

        echo '<h2>Test 1: Permissions</h2>';
        echo '<table>';
        echo '<tr><th>Permission</th><th>Status</th></tr>';
        
        $has_view = function_exists('dietetic_has_permission') ? dietetic_has_permission('view') : false;
        $has_edit = function_exists('dietetic_has_permission') ? dietetic_has_permission('edit') : false;
        $is_admin = is_admin();
        
        echo '<tr><td>dietetic_has_permission(\'view\')</td><td>' . ($has_view ? '<span class="success">✅ TRUE</span>' : '<span class="error">❌ FALSE</span>') . '</td></tr>';
        echo '<tr><td>dietetic_has_permission(\'edit\')</td><td>' . ($has_edit ? '<span class="success">✅ TRUE</span>' : '<span class="error">❌ FALSE</span>') . '</td></tr>';
        echo '<tr><td>is_admin()</td><td>' . ($is_admin ? '<span class="success">✅ TRUE</span>' : '<span class="error">❌ FALSE</span>') . '</td></tr>';
        echo '</table>';

        if (!$has_edit) {
            echo '<p class="error">⚠️ PROBLÈME: Pas de permission \'edit\'! Le bouton d\'assignation ne s\'affichera pas.</p>';
        }

        echo '<h2>Test 2: Recettes disponibles</h2>';
        $recipes = $this->dietetic_recipes_model->get_all();
        echo '<p>Nombre de recettes: <strong>' . count($recipes) . '</strong></p>';

        if (count($recipes) > 0) {
            echo '<table>';
            echo '<tr><th>ID</th><th>Nom</th><th>Status</th><th>Bouton visible?</th></tr>';
            foreach ($recipes as $recipe) {
                $show_button = $has_edit && $recipe->status == 'approved';
                echo '<tr>';
                echo '<td>' . $recipe->id . '</td>';
                echo '<td>' . htmlspecialchars($recipe->name) . '</td>';
                echo '<td><strong>' . $recipe->status . '</strong></td>';
                echo '<td>' . ($show_button ? '<span class="success">✅ OUI</span>' : '<span class="error">❌ NON (status=' . $recipe->status . ')</span>') . '</td>';
                echo '</tr>';
            }
            echo '</table>';

            // Get first approved recipe
            $approved_recipe = null;
            foreach ($recipes as $recipe) {
                if ($recipe->status == 'approved') {
                    $approved_recipe = $recipe;
                    break;
                }
            }

            if (!$approved_recipe) {
                echo '<p class="error">❌ PROBLÈME: Aucune recette avec status="approved"! Le bouton d\'assignation ne sera jamais visible.</p>';
                echo '<p class="warning">Approuvez au moins une recette pour tester l\'assignation.</p>';
            }
        } else {
            echo '<p class="error">❌ Aucune recette trouvée!</p>';
        }

        echo '<h2>Test 3: Test du Modal Bootstrap</h2>';
        echo '<p>Cliquez sur le bouton ci-dessous et vérifiez si le modal s\'ouvre et si les patients se chargent:</p>';
        
        echo '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#testAssignModal">';
        echo '<i class="fa fa-plus"></i> Ouvrir le Modal de Test';
        echo '</button>';

        echo '<div id="console-output" style="margin-top:20px;padding:10px;background:#000;color:#0f0;font-family:monospace;min-height:100px;max-height:300px;overflow-y:auto;"></div>';

        echo '<h2>Test 4: Vérifier les ressources chargées</h2>';
        echo '<div id="resources-check"></div>';

        // Modal
        echo '<div class="modal fade" id="testAssignModal" tabindex="-1" role="dialog">';
        echo '<div class="modal-dialog" role="document">';
        echo '<div class="modal-content">';
        echo '<div class="modal-header">';
        echo '<button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>';
        echo '<h4 class="modal-title"><i class="fa fa-users"></i> Test Modal - Assigner Recette</h4>';
        echo '</div>';
        echo '<div class="modal-body">';
        echo '<div class="form-group">';
        echo '<label for="test_patient_id">Patient</label>';
        echo '<select class="form-control selectpicker" id="test_patient_id" name="patient_id" data-live-search="true" required>';
        echo '<option value="">-- Chargement... --</option>';
        echo '</select>';
        echo '</div>';
        echo '<div id="patient-count" class="alert alert-info" style="display:none;"></div>';
        echo '</div>';
        echo '<div class="modal-footer">';
        echo '<button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';

        ?>
        <script src="<?php echo base_url('assets/plugins/jquery/jquery.min.js'); ?>"></script>
        <script src="<?php echo base_url('assets/js/bootstrap.min.js'); ?>"></script>
        <script src="<?php echo base_url('assets/plugins/bootstrap-select/js/bootstrap-select.min.js'); ?>"></script>
        <script>
        // Console override to display in page
        var originalLog = console.log;
        var originalError = console.error;
        var consoleOutput = $('#console-output');

        function addToConsole(type, message) {
            var color = type === 'error' ? '#f00' : '#0f0';
            var timestamp = new Date().toLocaleTimeString();
            consoleOutput.append('<div style="color:' + color + ';">[' + timestamp + '] ' + type.toUpperCase() + ': ' + message + '</div>');
            consoleOutput.scrollTop(consoleOutput[0].scrollHeight);
        }

        console.log = function(message) {
            originalLog.apply(console, arguments);
            addToConsole('log', typeof message === 'object' ? JSON.stringify(message) : message);
        };

        console.error = function(message) {
            originalError.apply(console, arguments);
            addToConsole('error', typeof message === 'object' ? JSON.stringify(message) : message);
        };

        $(document).ready(function() {
            console.log('=== DÉBUT DU TEST ===');
            console.log('jQuery version: ' + $.fn.jquery);
            console.log('Bootstrap loaded: ' + (typeof $.fn.modal !== 'undefined'));
            console.log('Selectpicker loaded: ' + (typeof $.fn.selectpicker !== 'undefined'));

            // Check resources
            var resourcesHtml = '<table>';
            resourcesHtml += '<tr><th>Resource</th><th>Status</th></tr>';
            resourcesHtml += '<tr><td>jQuery</td><td><span class="success">✅ ' + $.fn.jquery + '</span></td></tr>';
            resourcesHtml += '<tr><td>Bootstrap Modal</td><td>' + (typeof $.fn.modal !== 'undefined' ? '<span class="success">✅ Loaded</span>' : '<span class="error">❌ Not loaded</span>') + '</td></tr>';
            resourcesHtml += '<tr><td>Bootstrap Selectpicker</td><td>' + (typeof $.fn.selectpicker !== 'undefined' ? '<span class="success">✅ Loaded</span>' : '<span class="error">❌ Not loaded</span>') + '</td></tr>';
            resourcesHtml += '</table>';
            $('#resources-check').html(resourcesHtml);

            // Initialize selectpicker
            $('.selectpicker').selectpicker();

            // Test: Load patients when modal opens
            $('#testAssignModal').on('show.bs.modal', function() {
                console.log('✅ Modal event "show.bs.modal" triggered!');
                console.log('Chargement des patients via AJAX...');

                $.ajax({
                    url: '<?php echo admin_url('dietetic/recipes/get_patients'); ?>',
                    type: 'GET',
                    dataType: 'json',
                    beforeSend: function() {
                        console.log('AJAX request sent to: ' + '<?php echo admin_url('dietetic/recipes/get_patients'); ?>');
                    },
                    success: function(patients) {
                        console.log('✅ AJAX SUCCESS!');
                        console.log('Patients received: ' + patients.length);
                        console.log('Data: ' + JSON.stringify(patients));

                        const select = $('#test_patient_id');
                        select.empty();
                        select.append('<option value="">-- Sélectionner un patient --</option>');

                        if (patients.error) {
                            console.error('Error in response: ' + patients.error);
                            $('#patient-count').show().removeClass('alert-info').addClass('alert-danger').text('Erreur: ' + patients.error);
                            return;
                        }

                        if (patients.length === 0) {
                            console.error('❌ PROBLÈME: Le tableau est vide!');
                            $('#patient-count').show().removeClass('alert-info').addClass('alert-warning').text('Aucun patient trouvé');
                            select.selectpicker('refresh');
                            return;
                        }

                        patients.forEach(function(patient) {
                            select.append('<option value="' + patient.id + '">' + patient.name + '</option>');
                            console.log('Patient added: ' + patient.name + ' (ID: ' + patient.id + ')');
                        });

                        select.selectpicker('refresh');
                        console.log('✅ Selectpicker refreshed');

                        $('#patient-count').show().removeClass('alert-warning alert-danger').addClass('alert-success').text('✅ ' + patients.length + ' patient(s) chargé(s) avec succès!');
                    },
                    error: function(xhr, status, error) {
                        console.error('❌ AJAX ERROR!');
                        console.error('Status: ' + status);
                        console.error('Error: ' + error);
                        console.error('Response: ' + xhr.responseText);

                        $('#patient-count').show().removeClass('alert-info').addClass('alert-danger').text('Erreur AJAX: ' + error);
                    }
                });
            });

            console.log('Event handler attached to #testAssignModal');
        });
        </script>
        <?php

        echo '</body></html>';
    }
}
