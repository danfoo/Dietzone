<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Test_favorites extends App_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        echo '<!DOCTYPE html><html><head><title>Test Favorites</title>';
        echo '<style>body{font-family:Arial;padding:20px;} .success{color:green;} .error{color:red;} pre{background:#f5f5f5;padding:15px;}</style>';
        echo '<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>';
        echo '</head><body>';

        echo '<h1>🔍 Test Système Favoris</h1>';

        if (!is_client_logged_in()) {
            echo '<p class="error">❌ Non connecté. <a href="' . site_url('authentication/login') . '">Se connecter</a></p>';
            echo '</body></html>';
            return;
        }

        $client_id = get_client_user_id();
        echo '<p class="success">✅ Client connecté - ID: ' . $client_id . '</p>';

        $this->load->model('dietetic/dietetic_patients_model');
        $this->load->model('dietetic/dietetic_recipes_model');

        $patient = $this->dietetic_patients_model->get_by_client($client_id);

        if (!$patient) {
            echo '<p class="error">❌ Patient non trouvé</p>';
            echo '</body></html>';
            return;
        }

        echo '<p class="success">✅ Patient trouvé - ID: ' . $patient->id . '</p>';

        // Get recipes
        $recipes = $this->dietetic_recipes_model->get_approved([]);

        if (empty($recipes)) {
            echo '<p class="error">❌ Aucune recette trouvée</p>';
            echo '</body></html>';
            return;
        }

        $recipe = $recipes[0];
        echo '<p class="success">✅ Recette de test: ' . $recipe->name . ' (ID: ' . $recipe->id . ')</p>';

        echo '<hr>';
        echo '<h2>Test des URLs</h2>';

        $add_url = site_url('dietetic/portal/add_to_favorites');
        $remove_url = site_url('dietetic/portal/remove_from_favorites');

        echo '<p><strong>URL add_to_favorites:</strong> ' . $add_url . '</p>';
        echo '<p><strong>URL remove_from_favorites:</strong> ' . $remove_url . '</p>';

        echo '<hr>';
        echo '<h2>Test AJAX</h2>';

        echo '<button id="btn-add" class="btn btn-primary">Ajouter aux favoris (Recette #' . $recipe->id . ')</button>';
        echo '<button id="btn-remove" class="btn btn-danger">Retirer des favoris (Recette #' . $recipe->id . ')</button>';

        echo '<div id="result" style="margin-top:20px; padding:15px; background:#f5f5f5; border-radius:4px;"></div>';

        echo '<script>
        $(document).ready(function() {
            $("#btn-add").click(function() {
                $("#result").html("<p>⏳ Envoi de la requête add_to_favorites...</p>");

                $.ajax({
                    url: "' . $add_url . '",
                    method: "POST",
                    data: {
                        recipe_id: ' . $recipe->id . ',
                        "' . $this->security->get_csrf_token_name() . '": "' . $this->security->get_csrf_hash() . '"
                    },
                    success: function(response) {
                        console.log("Response:", response);
                        try {
                            const data = JSON.parse(response);
                            if (data.success) {
                                $("#result").html("<p class=\"success\">✅ Succès: " + data.message + "</p>");
                            } else {
                                $("#result").html("<p class=\"error\">❌ Échec: " + data.message + "</p>");
                            }
                        } catch(e) {
                            $("#result").html("<p class=\"error\">❌ Erreur parsing JSON: " + e.message + "</p><pre>" + response + "</pre>");
                        }
                    },
                    error: function(xhr, status, error) {
                        let errorHtml = "<p class=\"error\">❌ Erreur AJAX</p>";
                        errorHtml += "<p><strong>Status:</strong> " + status + "</p>";
                        errorHtml += "<p><strong>Error:</strong> " + error + "</p>";
                        errorHtml += "<p><strong>HTTP Status Code:</strong> " + xhr.status + "</p>";
                        errorHtml += "<p><strong>Ready State:</strong> " + xhr.readyState + "</p>";

                        if (xhr.responseText) {
                            errorHtml += "<p><strong>Response Text:</strong></p>";
                            errorHtml += "<pre>" + xhr.responseText + "</pre>";
                        } else {
                            errorHtml += "<p><strong>Response Text:</strong> (vide)</p>";
                        }

                        errorHtml += "<p><strong>All Headers:</strong></p>";
                        errorHtml += "<pre>" + xhr.getAllResponseHeaders() + "</pre>";

                        $("#result").html(errorHtml);
                        console.log("XHR Error Details:", xhr);
                    }
                });
            });

            $("#btn-remove").click(function() {
                $("#result").html("<p>⏳ Envoi de la requête remove_from_favorites...</p>");

                $.ajax({
                    url: "' . $remove_url . '",
                    method: "POST",
                    data: {
                        recipe_id: ' . $recipe->id . ',
                        "' . $this->security->get_csrf_token_name() . '": "' . $this->security->get_csrf_hash() . '"
                    },
                    success: function(response) {
                        console.log("Response:", response);
                        try {
                            const data = JSON.parse(response);
                            if (data.success) {
                                $("#result").html("<p class=\"success\">✅ Succès: " + data.message + "</p>");
                            } else {
                                $("#result").html("<p class=\"error\">❌ Échec: " + data.message + "</p>");
                            }
                        } catch(e) {
                            $("#result").html("<p class=\"error\">❌ Erreur parsing JSON: " + e.message + "</p><pre>" + response + "</pre>");
                        }
                    },
                    error: function(xhr, status, error) {
                        let errorHtml = "<p class=\"error\">❌ Erreur AJAX</p>";
                        errorHtml += "<p><strong>Status:</strong> " + status + "</p>";
                        errorHtml += "<p><strong>Error:</strong> " + error + "</p>";
                        errorHtml += "<p><strong>HTTP Status Code:</strong> " + xhr.status + "</p>";
                        errorHtml += "<p><strong>Ready State:</strong> " + xhr.readyState + "</p>";

                        if (xhr.responseText) {
                            errorHtml += "<p><strong>Response Text:</strong></p>";
                            errorHtml += "<pre>" + xhr.responseText + "</pre>";
                        } else {
                            errorHtml += "<p><strong>Response Text:</strong> (vide)</p>";
                        }

                        errorHtml += "<p><strong>All Headers:</strong></p>";
                        errorHtml += "<pre>" + xhr.getAllResponseHeaders() + "</pre>";

                        $("#result").html(errorHtml);
                        console.log("XHR Error Details:", xhr);
                    }
                });
            });
        });
        </script>';

        echo '</body></html>';
    }
}
