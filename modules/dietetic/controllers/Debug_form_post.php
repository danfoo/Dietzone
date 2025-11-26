<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Debug_form_post extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('dietetic/dietetic');

        if (!is_admin()) {
            access_denied('Debug - Admin only');
        }
    }

    /**
     * Capture and display POST data from patient form
     */
    public function capture()
    {
        header('Content-Type: text/html; charset=utf-8');

        echo "<!DOCTYPE html><html><head><title>Debug Form POST</title>";
        echo "<style>
            body{font-family:Arial;margin:20px;background:#f5f5f5;}
            .container{max-width:1200px;margin:0 auto;background:white;padding:30px;border-radius:8px;}
            .success{background:#d4edda;color:#155724;padding:15px;margin:10px 0;border-radius:4px;}
            .error{background:#f8d7da;color:#721c24;padding:15px;margin:10px 0;border-radius:4px;}
            .info{background:#cce5ff;color:#004085;padding:15px;margin:10px 0;border-radius:4px;}
            pre{background:#f8f9fa;padding:15px;border-radius:5px;overflow-x:auto;font-size:11px;max-height:500px;overflow-y:auto;}
            h2{color:#333;border-bottom:2px solid #007bff;padding-bottom:10px;margin-top:30px;}
            table{width:100%;border-collapse:collapse;margin:15px 0;}
            th,td{padding:8px;text-align:left;border-bottom:1px solid #ddd;font-size:12px;}
            th{background:#f8f9fa;font-weight:bold;}
            .empty{color:#999;font-style:italic;}
        </style></head><body><div class='container'>";

        echo "<h1>🔍 Debug: Données POST du Formulaire Patient</h1>";

        if ($this->input->post()) {
            $raw_post = $this->input->post();

            echo "<div class='success'>";
            echo "✅ Données POST reçues : <strong>" . count($raw_post) . " champs</strong>";
            echo "</div>";

            // Count empty vs filled
            $empty_count = 0;
            $filled_count = 0;
            foreach ($raw_post as $key => $value) {
                if (empty($value) && $value !== '0') {
                    $empty_count++;
                } else {
                    $filled_count++;
                }
            }

            echo "<div class='info'>";
            echo "<strong>Statistiques:</strong><br>";
            echo "• Champs remplis: <strong>{$filled_count}</strong><br>";
            echo "• Champs vides: <strong>{$empty_count}</strong>";
            echo "</div>";

            // Show all POST data
            echo "<h2>📋 Données POST complètes</h2>";
            echo "<table>";
            echo "<thead><tr><th>#</th><th>Nom du champ</th><th>Valeur</th><th>Type</th><th>Longueur</th></tr></thead>";
            echo "<tbody>";

            $index = 1;
            foreach ($raw_post as $key => $value) {
                echo "<tr>";
                echo "<td>{$index}</td>";
                echo "<td><code>" . htmlspecialchars($key) . "</code></td>";

                if (empty($value) && $value !== '0') {
                    echo "<td class='empty'>(vide)</td>";
                } else {
                    $display_value = is_array($value) ? json_encode($value) : $value;
                    if (strlen($display_value) > 100) {
                        $display_value = substr($display_value, 0, 100) . '...';
                    }
                    echo "<td>" . htmlspecialchars($display_value) . "</td>";
                }

                echo "<td>" . gettype($value) . "</td>";

                if (is_string($value)) {
                    echo "<td>" . strlen($value) . " chars</td>";
                } elseif (is_array($value)) {
                    echo "<td>" . count($value) . " items</td>";
                } else {
                    echo "<td>-</td>";
                }

                echo "</tr>";
                $index++;
            }
            echo "</tbody></table>";

            // Show RAW data
            echo "<h2>🔬 Données brutes (format PHP)</h2>";
            echo "<pre>" . print_r($raw_post, true) . "</pre>";

            // Check for potential issues
            echo "<h2>⚠️ Vérifications</h2>";

            $issues = [];

            // Check client_id
            if (!isset($raw_post['client_id']) || empty($raw_post['client_id'])) {
                $issues[] = "❌ <strong>client_id</strong> manquant ou vide";
            }

            // Check dietitian_id
            if (!isset($raw_post['dietitian_id']) || empty($raw_post['dietitian_id'])) {
                $issues[] = "⚠️ <strong>dietitian_id</strong> manquant (sera défini automatiquement)";
            }

            // Check status
            if (!isset($raw_post['status']) || empty($raw_post['status'])) {
                $issues[] = "⚠️ <strong>status</strong> manquant ou vide";
            }

            // Check $_FILES
            if (!empty($_FILES)) {
                echo "<div class='info'>";
                echo "<strong>📎 Fichiers uploadés détectés:</strong><br>";
                echo "<pre>" . print_r($_FILES, true) . "</pre>";
                echo "</div>";
            }

            if (!empty($issues)) {
                echo "<div class='error'>";
                echo "<strong>Problèmes potentiels détectés:</strong><ul>";
                foreach ($issues as $issue) {
                    echo "<li>{$issue}</li>";
                }
                echo "</ul></div>";
            } else {
                echo "<div class='success'>✅ Aucun problème évident détecté</div>";
            }

            // Test filtering
            echo "<h2>🔍 Test de filtrage des données</h2>";

            $table_name = db_prefix() . 'dietic_patients';
            $query = $this->db->query("DESCRIBE `{$table_name}`");
            $valid_columns = [];
            foreach ($query->result_array() as $row) {
                $valid_columns[] = $row['Field'];
            }

            $filtered_data = [];
            $skipped_fields = [];
            foreach ($raw_post as $key => $value) {
                if (in_array($key, $valid_columns)) {
                    $filtered_data[$key] = $value;
                } else {
                    $skipped_fields[] = $key;
                }
            }

            echo "<div class='success'>";
            echo "✅ Champs valides après filtrage: <strong>" . count($filtered_data) . "</strong>";
            echo "</div>";

            if (!empty($skipped_fields)) {
                echo "<div class='info'>";
                echo "<strong>Champs ignorés (" . count($skipped_fields) . "):</strong><br>";
                echo "<code>" . implode(', ', $skipped_fields) . "</code>";
                echo "</div>";
            }

            echo "<h2>💾 Données filtrées (prêtes pour insertion)</h2>";
            echo "<pre>" . print_r($filtered_data, true) . "</pre>";

        } else {
            echo "<div class='info'>";
            echo "<p><strong>ℹ️ Instructions:</strong></p>";
            echo "<ol>";
            echo "<li>Allez sur la page de création de patient: <a href='" . admin_url('dietetic/patients/create') . "' target='_blank'>Créer un patient</a></li>";
            echo "<li>Ouvrez les outils de développement du navigateur (F12)</li>";
            echo "<li>Allez dans l'onglet <strong>Console</strong></li>";
            echo "<li>Collez ce code JavaScript et appuyez sur Entrée :</li>";
            echo "</ol>";

            echo "<pre style='background:#333;color:#0f0;padding:20px;border-radius:8px;'>
// Change l'action du formulaire pour capturer les données
document.querySelector('form').action = '" . admin_url('dietetic/debug_form_post/capture') . "';
console.log('✅ Formulaire modifié ! Maintenant soumettez-le normalement.');
</pre>";

            echo "<p><strong>5.</strong> Remplissez le formulaire patient normalement</p>";
            echo "<p><strong>6.</strong> Cliquez sur <strong>Enregistrer</strong></p>";
            echo "<p><strong>7.</strong> Vous serez redirigé ici avec toutes les données POST analysées</p>";
            echo "</div>";
        }

        echo "</div></body></html>";
    }
}
