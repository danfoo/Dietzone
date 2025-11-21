<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Test Patient Filter - Diagnostic permission filtering
 */
class Test_patient_filter extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('dietetic/dietetic');
        $this->load->model('dietetic/dietetic_patients_model');
    }

    public function index()
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        echo '<h1>Diagnostic Permission Filtering</h1>';
        echo '<style>body{font-family:Arial;padding:20px;} .success{color:green;} .error{color:red;} .warning{color:orange;} pre{background:#f5f5f5;padding:10px;border-radius:4px;overflow:auto;} table{border-collapse:collapse;margin:20px 0;} th,td{border:1px solid #ddd;padding:8px;text-align:left;} th{background:#f0f0f0;}</style>';

        echo '<h2>Test 1: User Information</h2>';
        $staff_id = get_staff_user_id();
        $is_admin = is_admin();

        echo '<table>';
        echo '<tr><th>Property</th><th>Value</th></tr>';
        echo '<tr><td>Staff ID</td><td><strong>' . $staff_id . '</strong></td></tr>';
        echo '<tr><td>is_admin()</td><td><strong>' . ($is_admin ? 'TRUE' : 'FALSE') . '</strong></td></tr>';
        echo '<tr><td>Super Admin Check (staff_id == 1)</td><td><strong>' . ($staff_id == 1 ? 'TRUE ✅' : 'FALSE ❌') . '</strong></td></tr>';
        echo '</table>';

        if ($is_admin && $staff_id != 1) {
            echo '<p class="warning">⚠️ Vous êtes admin mais pas super admin (staff_id != 1). Le filtre de permissions va s\'appliquer!</p>';
        } elseif ($is_admin && $staff_id == 1) {
            echo '<p class="success">✅ Vous êtes super admin. Le filtre ne devrait PAS s\'appliquer.</p>';
        } else {
            echo '<p class="warning">⚠️ Vous n\'êtes pas admin. Le filtre va s\'appliquer normalement.</p>';
        }

        echo '<h2>Test 2: Table dietic_patient_dietitians</h2>';
        $table_exists = $this->db->table_exists(db_prefix() . 'dietic_patient_dietitians');
        echo '<p>Table exists: <strong>' . ($table_exists ? 'YES ✅' : 'NO ❌') . '</strong></p>';

        if ($table_exists) {
            $assignments = $this->db->get(db_prefix() . 'dietic_patient_dietitians')->result();
            echo '<p>Nombre d\'assignations: <strong>' . count($assignments) . '</strong></p>';

            if (count($assignments) > 0) {
                echo '<table>';
                echo '<tr><th>ID</th><th>Patient ID</th><th>Dietitian ID</th><th>Status</th></tr>';
                foreach ($assignments as $a) {
                    $highlight = ($a->dietitian_id == $staff_id) ? 'background:#ffffcc;' : '';
                    echo '<tr style="' . $highlight . '">';
                    echo '<td>' . $a->id . '</td>';
                    echo '<td>' . $a->patient_id . '</td>';
                    echo '<td>' . $a->dietitian_id . ($a->dietitian_id == $staff_id ? ' (VOUS)' : '') . '</td>';
                    echo '<td>' . $a->status . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            }
        }

        echo '<h2>Test 3: Patients SANS filtre (direct SQL)</h2>';
        $this->db->select('p.id, p.client_id, p.dietitian_id, c.company as client_name, CONCAT(s.firstname, " ", s.lastname) as dietitian_name', false);
        $this->db->from(db_prefix() . 'dietic_patients p');
        $this->db->join(db_prefix() . 'clients c', 'c.userid = p.client_id', 'left');
        $this->db->join(db_prefix() . 'staff s', 's.staffid = p.dietitian_id', 'left');
        $all_patients = $this->db->get()->result();

        echo '<p>Nombre de patients (sans filtre): <strong>' . count($all_patients) . '</strong></p>';
        if (count($all_patients) > 0) {
            echo '<table>';
            echo '<tr><th>ID</th><th>Client ID</th><th>Dietitian ID</th><th>Client Name</th><th>Dietitian Name</th></tr>';
            foreach ($all_patients as $p) {
                echo '<tr>';
                echo '<td>' . $p->id . '</td>';
                echo '<td>' . $p->client_id . '</td>';
                echo '<td>' . $p->dietitian_id . '</td>';
                echo '<td>' . htmlspecialchars($p->client_name) . '</td>';
                echo '<td>' . htmlspecialchars($p->dietitian_name) . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        }

        echo '<h2>Test 4: Patients AVEC filtre (via model get_all())</h2>';
        $filtered_patients = $this->dietetic_patients_model->get_all();
        echo '<p>Nombre de patients (avec filtre): <strong>' . count($filtered_patients) . '</strong></p>';

        if (count($filtered_patients) > 0) {
            echo '<table>';
            echo '<tr><th>ID</th><th>Client Name</th><th>Dietitian Name</th><th>Email</th></tr>';
            foreach ($filtered_patients as $p) {
                echo '<tr>';
                echo '<td>' . $p->id . '</td>';
                echo '<td>' . htmlspecialchars($p->client_name) . '</td>';
                echo '<td>' . htmlspecialchars($p->dietitian_name) . '</td>';
                echo '<td>' . htmlspecialchars($p->email ?? 'N/A') . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo '<p class="error">❌ AUCUN patient retourné! C\'est ici le problème!</p>';
        }

        echo '<h2>Test 5: Simuler la requête SQL générée</h2>';

        // Enable query logging
        $this->db->select('p.*, c.company as client_name, CONCAT(s.firstname, " ", s.lastname) as dietitian_name', false);
        $this->db->from(db_prefix() . 'dietic_patients p');
        $this->db->join(db_prefix() . 'clients c', 'c.userid = p.client_id', 'left');
        $this->db->join(db_prefix() . 'staff s', 's.staffid = p.dietitian_id', 'left');

        // Apply the filter manually
        if ($table_exists) {
            echo '<p>Application du filtre dietetic_apply_dietitian_filter()...</p>';
            dietetic_apply_dietitian_filter($this->db, 'pd');
        }

        $this->db->group_by('p.id');

        // Get the SQL query without executing
        $sql = $this->db->get_compiled_select();

        echo '<p><strong>Requête SQL générée:</strong></p>';
        echo '<pre>' . htmlspecialchars($sql) . '</pre>';

        echo '<h2>Test 6: Vérifier le résultat de get_patients() du contrôleur Recipes</h2>';
        echo '<button id="test-ajax-permissions" class="btn btn-primary">Tester get_patients() AJAX</button>';
        echo '<div id="ajax-permissions-result" style="margin-top:10px;"></div>';

        echo '<h2>Diagnostic et Recommandations</h2>';
        echo '<div style="background:#f9f9f9;padding:15px;border-left:4px solid #0066cc;">';

        if ($is_admin && $staff_id != 1) {
            echo '<p class="error"><strong>⚠️ PROBLÈME IDENTIFIÉ:</strong></p>';
            echo '<p>Vous êtes admin (is_admin = true) mais votre staff_id est <strong>' . $staff_id . '</strong> et non pas 1.</p>';
            echo '<p>La fonction dietetic_apply_dietitian_filter() vérifie:</p>';
            echo '<pre>if (dietetic_is_admin() && $staff_id == 1) { return; }</pre>';
            echo '<p>Comme votre staff_id != 1, le filtre s\'applique quand même et limite les patients visibles.</p>';
            echo '<p><strong>Solutions possibles:</strong></p>';
            echo '<ol>';
            echo '<li>Modifier la condition pour accepter tous les admins: <code>if (dietetic_is_admin()) { return; }</code></li>';
            echo '<li>Assigner tous les patients à votre staff_id dans la table dietic_patient_dietitians</li>';
            echo '<li>Créer une permission spécifique "view_all_patients"</li>';
            echo '</ol>';
        } elseif (count($filtered_patients) == 0 && count($all_patients) > 0) {
            echo '<p class="error"><strong>⚠️ PROBLÈME IDENTIFIÉ:</strong></p>';
            echo '<p>Il y a ' . count($all_patients) . ' patients dans la base, mais le filtre en retourne 0.</p>';
            echo '<p>Vérifiez les assignations dans dietic_patient_dietitians ou ajustez la logique de permission.</p>';
        } else {
            echo '<p class="success"><strong>✅ Permissions OK</strong></p>';
            echo '<p>Le problème n\'est probablement pas lié aux permissions.</p>';
        }

        echo '</div>';

        ?>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
        $(document).ready(function() {
            $('#test-ajax-permissions').click(function() {
                console.log('[Test Permissions] Testing get_patients() AJAX...');
                $('#ajax-permissions-result').html('<p class="warning">⏳ Chargement...</p>');

                $.ajax({
                    url: '<?php echo admin_url('dietetic/recipes/get_patients'); ?>',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        console.log('[Test Permissions] Success:', response);

                        var html = '<p class="success">✅ AJAX réussi! Patients reçus: ' + response.length + '</p>';

                        if (response.length > 0) {
                            html += '<table>';
                            html += '<tr><th>ID</th><th>Name</th><th>Email</th></tr>';
                            response.forEach(function(patient) {
                                html += '<tr>';
                                html += '<td>' + patient.id + '</td>';
                                html += '<td>' + patient.name + '</td>';
                                html += '<td>' + patient.email + '</td>';
                                html += '</tr>';
                            });
                            html += '</table>';
                        } else {
                            html += '<p class="error">❌ Le tableau est vide!</p>';
                        }

                        $('#ajax-permissions-result').html(html);
                    },
                    error: function(xhr, status, error) {
                        console.error('[Test Permissions] Error:', status, error);
                        console.error('[Test Permissions] Response:', xhr.responseText);

                        var html = '<p class="error">❌ Erreur AJAX!</p>';
                        html += '<p>Status: ' + status + '</p>';
                        html += '<p>Error: ' + error + '</p>';
                        html += '<pre>' + xhr.responseText + '</pre>';

                        $('#ajax-permissions-result').html(html);
                    }
                });
            });
        });
        </script>
        <?php
    }
}
