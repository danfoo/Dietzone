<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Test_recipes_portal extends App_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        echo '<!DOCTYPE html><html><head><title>Test Portal Recipes</title>';
        echo '<style>body{font-family:Arial;padding:20px;} .success{color:green;} .error{color:red;} .warning{color:orange;} pre{background:#f5f5f5;padding:15px;border-radius:4px;overflow:auto;max-height:400px;}</style>';
        echo '</head><body>';

        echo '<h1>🔍 Diagnostic Portal Recipes Error</h1>';

        echo '<h2>Test 1: Client Login Status</h2>';

        if (!is_client_logged_in()) {
            echo '<p class="error">❌ Client NON connecté</p>';
            echo '<p>Vous devez être connecté en tant que client pour tester.</p>';
            echo '</body></html>';
            return;
        }

        $client_id = get_client_user_id();
        echo '<p class="success">✅ Client connecté - ID: ' . $client_id . '</p>';

        echo '<h2>Test 2: Charger les modèles</h2>';

        try {
            $this->load->model('dietetic/dietetic_recipes_model');
            echo '<p class="success">✅ dietetic_recipes_model chargé</p>';
        } catch (Exception $e) {
            echo '<p class="error">❌ Erreur chargement dietetic_recipes_model:</p>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
            echo '</body></html>';
            return;
        }

        try {
            $this->load->model('dietetic/dietetic_patients_model');
            echo '<p class="success">✅ dietetic_patients_model chargé</p>';
        } catch (Exception $e) {
            echo '<p class="error">❌ Erreur chargement dietetic_patients_model:</p>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
            echo '</body></html>';
            return;
        }

        echo '<h2>Test 3: Récupérer le patient</h2>';

        try {
            $patient = $this->dietetic_patients_model->get_by_client($client_id);

            if (!$patient) {
                echo '<p class="error">❌ Aucun patient trouvé pour client ID: ' . $client_id . '</p>';
                echo '</body></html>';
                return;
            }

            echo '<p class="success">✅ Patient trouvé - ID: ' . $patient->id . '</p>';
            echo '<pre>' . print_r($patient, true) . '</pre>';

        } catch (Exception $e) {
            echo '<p class="error">❌ EXCEPTION lors de la récupération du patient:</p>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
            echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
            echo '</body></html>';
            return;
        }

        echo '<h2>Test 4: Charger les recettes assignées</h2>';

        try {
            $assigned_recipes = $this->dietetic_recipes_model->get_patient_recipes($patient->id);
            echo '<p class="success">✅ Recettes assignées chargées: ' . count($assigned_recipes) . '</p>';

            if (count($assigned_recipes) > 0) {
                echo '<h3>Première recette assignée:</h3>';
                echo '<pre>' . print_r($assigned_recipes[0], true) . '</pre>';
            } else {
                echo '<p class="warning">⚠️ Aucune recette assignée à ce patient.</p>';
            }

        } catch (Exception $e) {
            echo '<p class="error">❌ EXCEPTION lors du chargement des recettes assignées:</p>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
            echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';

            // Afficher l'erreur SQL
            $error = $this->db->error();
            if (!empty($error['message'])) {
                echo '<p class="error"><strong>Erreur SQL:</strong></p>';
                echo '<pre>' . htmlspecialchars(print_r($error, true)) . '</pre>';
            }

            // Afficher la dernière requête
            echo '<p><strong>Dernière requête SQL:</strong></p>';
            echo '<pre>' . htmlspecialchars($this->db->last_query()) . '</pre>';
        }

        echo '<h2>Test 5: Charger toutes les recettes approuvées</h2>';

        try {
            $filters = [];
            $all_recipes = $this->dietetic_recipes_model->get_approved($filters);
            echo '<p class="success">✅ Recettes approuvées chargées: ' . count($all_recipes) . '</p>';

            if (count($all_recipes) > 0) {
                echo '<h3>Première recette approuvée:</h3>';
                echo '<pre>' . print_r($all_recipes[0], true) . '</pre>';
            } else {
                echo '<p class="warning">⚠️ Aucune recette approuvée dans la base.</p>';
            }

        } catch (Exception $e) {
            echo '<p class="error">❌ EXCEPTION lors du chargement des recettes approuvées:</p>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
            echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';

            // Afficher l'erreur SQL
            $error = $this->db->error();
            if (!empty($error['message'])) {
                echo '<p class="error"><strong>Erreur SQL:</strong></p>';
                echo '<pre>' . htmlspecialchars(print_r($error, true)) . '</pre>';
            }

            // Afficher la dernière requête
            echo '<p><strong>Dernière requête SQL:</strong></p>';
            echo '<pre>' . htmlspecialchars($this->db->last_query()) . '</pre>';
        }

        echo '<h2>Test 6: Charger les favoris</h2>';

        try {
            $favorites = $this->dietetic_recipes_model->get_favorites($patient->id);
            echo '<p class="success">✅ Favoris chargés: ' . count($favorites) . '</p>';

            if (count($favorites) > 0) {
                echo '<h3>Favoris:</h3>';
                echo '<pre>' . print_r($favorites, true) . '</pre>';
            } else {
                echo '<p class="warning">⚠️ Aucun favori pour ce patient.</p>';
            }

        } catch (Exception $e) {
            echo '<p class="error">❌ EXCEPTION lors du chargement des favoris:</p>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
            echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
        }

        echo '<h2>Test 7: Charger les tags</h2>';

        try {
            $all_tags = $this->dietetic_recipes_model->get_all_tags();
            echo '<p class="success">✅ Tags chargés: ' . count($all_tags) . '</p>';

            if (count($all_tags) > 0) {
                echo '<h3>Tags disponibles:</h3>';
                echo '<pre>' . print_r($all_tags, true) . '</pre>';
            } else {
                echo '<p class="warning">⚠️ Aucun tag trouvé.</p>';
            }

        } catch (Exception $e) {
            echo '<p class="error">❌ EXCEPTION lors du chargement des tags:</p>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
            echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
        }

        echo '<h2>Test 8: Charger les données client pour header</h2>';

        try {
            $this->load->model('clients_model');
            $client = $this->clients_model->get($patient->client_id);

            if ($client) {
                echo '<p class="success">✅ Client chargé: ' . $client->company . '</p>';
            } else {
                echo '<p class="error">❌ Client non trouvé pour patient->client_id: ' . $patient->client_id . '</p>';
            }

        } catch (Exception $e) {
            echo '<p class="error">❌ EXCEPTION lors du chargement du client:</p>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
        }

        echo '<h2>Test 9: Simuler le chargement complet de la page</h2>';

        try {
            // Préparer toutes les données comme le contrôleur
            $data = [];
            $data['title'] = 'Bibliothèque de Recettes';
            $data['patient'] = $patient;
            $data['client'] = $client;
            $data['page_title'] = 'Bibliothèque de Recettes';
            $data['active_page'] = 'recipes';
            $data['assigned_recipes'] = $this->dietetic_recipes_model->get_patient_recipes($patient->id);
            $data['all_recipes'] = $this->dietetic_recipes_model->get_approved($filters);
            $data['favorites'] = $this->dietetic_recipes_model->get_favorites($patient->id);
            $data['all_tags'] = $this->dietetic_recipes_model->get_all_tags();

            echo '<p class="success">✅ Toutes les données préparées avec succès!</p>';
            echo '<h3>Résumé des données:</h3>';
            echo '<ul>';
            echo '<li>Recettes assignées: ' . count($data['assigned_recipes']) . '</li>';
            echo '<li>Toutes les recettes: ' . count($data['all_recipes']) . '</li>';
            echo '<li>Favoris: ' . count($data['favorites']) . '</li>';
            echo '<li>Tags: ' . count($data['all_tags']) . '</li>';
            echo '</ul>';

            echo '<h3>Structure complète des données:</h3>';
            echo '<pre>' . htmlspecialchars(print_r($data, true)) . '</pre>';

        } catch (Exception $e) {
            echo '<p class="error">❌ EXCEPTION lors de la préparation des données:</p>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
            echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
        }

        echo '<hr>';
        echo '<h2>✅ Diagnostic terminé</h2>';
        echo '<p>Si tous les tests sont verts, le problème vient probablement de la vue.</p>';
        echo '<p><a href="' . site_url('dietetic/portal/recipes') . '">Tester la page réelle</a></p>';

        echo '</body></html>';
    }
}
