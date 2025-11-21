<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Test Simple AJAX - Diagnostic minimal
 */
class Test_simple_ajax extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Test Simple AJAX</title>
            <style>
                body { font-family: Arial; padding: 20px; background: #f5f5f5; }
                .box { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
                .success { color: green; font-weight: bold; }
                .error { color: red; font-weight: bold; }
                pre { background: #000; color: #0f0; padding: 15px; border-radius: 4px; overflow: auto; }
                select { width: 100%; padding: 10px; font-size: 16px; margin: 10px 0; }
                button { background: #3498db; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
                button:hover { background: #2980b9; }
            </style>
        </head>
        <body>
            <h1>🔍 Test Simple AJAX - Chargement Patients</h1>
            
            <div class="box">
                <h2>Test 1: Appel direct de l'endpoint</h2>
                <p>URL testée: <code><?php echo admin_url('dietetic/recipes/get_patients'); ?></code></p>
                <button id="test-endpoint">Tester l'endpoint</button>
                <div id="endpoint-result"></div>
            </div>

            <div class="box">
                <h2>Test 2: Remplir le dropdown</h2>
                <label>Sélectionner un patient:</label>
                <select id="patient_select">
                    <option value="">-- Chargement... --</option>
                </select>
                <div id="select-status"></div>
            </div>

            <div class="box">
                <h2>Test 3: Console en direct</h2>
                <pre id="console"></pre>
            </div>

            <script src="<?php echo base_url('assets/plugins/jquery/jquery.min.js'); ?>"></script>
            <script>
                var consoleDiv = $('#console');
                
                function log(message) {
                    var timestamp = new Date().toLocaleTimeString();
                    consoleDiv.append('[' + timestamp + '] ' + message + '\n');
                    consoleDiv.scrollTop(consoleDiv[0].scrollHeight);
                    console.log(message);
                }

                log('=== DÉBUT DES TESTS ===');
                log('jQuery version: ' + $.fn.jquery);
                log('URL endpoint: <?php echo admin_url('dietetic/recipes/get_patients'); ?>');

                // Test automatique au chargement
                $(document).ready(function() {
                    log('Document ready - Chargement automatique des patients...');
                    loadPatients();
                });

                // Test manuel
                $('#test-endpoint').click(function() {
                    log('=== TEST MANUEL DÉCLENCHÉ ===');
                    $('#endpoint-result').html('<p style="color:orange;">⏳ Chargement...</p>');
                    loadPatients();
                });

                function loadPatients() {
                    log('Envoi de la requête AJAX...');

                    $.ajax({
                        url: '<?php echo admin_url('dietetic/recipes/get_patients'); ?>',
                        type: 'GET',
                        dataType: 'json',
                        beforeSend: function() {
                            log('beforeSend: Requête envoyée');
                        },
                        success: function(response, textStatus, jqXHR) {
                            log('✅ SUCCESS callback appelé');
                            log('Response type: ' + typeof response);
                            log('Response length: ' + (Array.isArray(response) ? response.length : 'N/A'));
                            log('Response data: ' + JSON.stringify(response));
                            
                            $('#endpoint-result').html(
                                '<p class="success">✅ Succès!</p>' +
                                '<p><strong>Nombre de patients:</strong> ' + response.length + '</p>' +
                                '<pre>' + JSON.stringify(response, null, 2) + '</pre>'
                            );

                            // Fill select
                            var select = $('#patient_select');
                            select.empty();

                            if (!response || response.length === 0) {
                                log('⚠️ Aucun patient dans la réponse');
                                select.append('<option value="">Aucun patient disponible</option>');
                                $('#select-status').html('<p class="error">❌ Aucun patient trouvé</p>');
                            } else {
                                select.append('<option value="">-- Sélectionner un patient --</option>');
                                
                                response.forEach(function(patient) {
                                    log('Ajout patient: ' + patient.name + ' (ID: ' + patient.id + ')');
                                    select.append('<option value="' + patient.id + '">' + patient.name + ' (' + patient.email + ')</option>');
                                });

                                $('#select-status').html('<p class="success">✅ ' + response.length + ' patient(s) chargé(s)</p>');
                                log('✅ Dropdown rempli avec ' + response.length + ' patients');
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            log('❌ ERROR callback appelé');
                            log('Status: ' + textStatus);
                            log('Error: ' + errorThrown);
                            log('HTTP Status Code: ' + jqXHR.status);
                            log('Response Text: ' + jqXHR.responseText);

                            $('#endpoint-result').html(
                                '<p class="error">❌ Erreur!</p>' +
                                '<p><strong>Status:</strong> ' + textStatus + '</p>' +
                                '<p><strong>Error:</strong> ' + errorThrown + '</p>' +
                                '<p><strong>HTTP Code:</strong> ' + jqXHR.status + '</p>' +
                                '<pre>' + jqXHR.responseText + '</pre>'
                            );

                            $('#select-status').html('<p class="error">❌ Erreur de chargement</p>');
                        },
                        complete: function() {
                            log('complete: Requête terminée');
                        }
                    });
                }
            </script>
        </body>
        </html>
        <?php
    }
}
