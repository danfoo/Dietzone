<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Diagnostic extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Check subscriptions system health
     */
    public function subscriptions_check()
    {
        if (!is_admin()) {
            echo "You must be an admin to view this page";
            return;
        }

        echo "<h1>Diagnostic du Système d'Abonnements</h1>";
        echo "<style>
            .success { color: green; }
            .error { color: red; }
            .warning { color: orange; }
            table { border-collapse: collapse; width: 100%; margin: 20px 0; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #4CAF50; color: white; }
        </style>";

        // 1. Check tables existence
        echo "<h2>1. Vérification des tables</h2>";
        echo "<table>";
        echo "<tr><th>Table</th><th>Status</th></tr>";

        $required_tables = [
            'dietic_patients' => 'Patients',
            'dietic_service_plans' => 'Plans de service',
            'dietic_subscriptions' => 'Abonnements',
            'dietic_invoices' => 'Factures',
            'dietic_payments' => 'Paiements',
            'dietic_commission_settings' => 'Paramètres commissions',
            'dietic_revenue_shares' => 'Partages revenus',
            'staff' => 'Staff (Perfex)',
            'clients' => 'Clients (Perfex)'
        ];

        foreach ($required_tables as $table => $label) {
            $exists = $this->db->table_exists(db_prefix() . $table);
            $status = $exists ? '<span class="success">✓ Existe</span>' : '<span class="error">✗ Manquante</span>';
            echo "<tr><td>$label ({$table})</td><td>$status</td></tr>";
        }
        echo "</table>";

        // 2. Check models
        echo "<h2>2. Vérification des modèles</h2>";
        echo "<table>";
        echo "<tr><th>Modèle</th><th>Status</th></tr>";

        $models = [
            'dietetic/dietetic_patients_model' => 'Patients Model',
            'dietetic/dietetic_service_plans_model' => 'Service Plans Model',
            'dietetic/dietetic_subscriptions_model' => 'Subscriptions Model',
            'dietetic/dietetic_invoices_model' => 'Invoices Model'
        ];

        foreach ($models as $model => $label) {
            try {
                $this->load->model($model);
                echo "<tr><td>$label</td><td><span class='success'>✓ Chargé</span></td></tr>";
            } catch (Exception $e) {
                echo "<tr><td>$label</td><td><span class='error'>✗ Erreur: " . htmlspecialchars($e->getMessage()) . "</span></td></tr>";
            }
        }
        echo "</table>";

        // 3. Check helper functions
        echo "<h2>3. Vérification des fonctions helper</h2>";
        echo "<table>";
        echo "<tr><th>Fonction</th><th>Status</th></tr>";

        $this->load->helper('dietetic/dietetic');

        $functions = [
            'dietetic_has_permission',
            'dietetic_can_access_patient',
            'dietetic_apply_dietitian_filter',
            'dietetic_get_staff_user_id',
            'dietetic_is_admin'
        ];

        foreach ($functions as $func) {
            $exists = function_exists($func);
            $status = $exists ? '<span class="success">✓ Existe</span>' : '<span class="error">✗ Manquante</span>';
            echo "<tr><td>$func()</td><td>$status</td></tr>";
        }
        echo "</table>";

        // 4. Test database queries
        echo "<h2>4. Test des requêtes</h2>";
        echo "<table>";
        echo "<tr><th>Requête</th><th>Status</th><th>Résultat</th></tr>";

        // Test 1: Count patients
        try {
            $count = $this->db->count_all_results(db_prefix() . 'dietic_patients');
            echo "<tr><td>Count patients</td><td><span class='success'>✓ OK</span></td><td>$count patients</td></tr>";
        } catch (Exception $e) {
            echo "<tr><td>Count patients</td><td><span class='error'>✗ Erreur</span></td><td>" . htmlspecialchars($e->getMessage()) . "</td></tr>";
        }

        // Test 2: Count service plans
        try {
            $count = $this->db->count_all_results(db_prefix() . 'dietic_service_plans');
            echo "<tr><td>Count service plans</td><td><span class='success'>✓ OK</span></td><td>$count plans</td></tr>";
        } catch (Exception $e) {
            echo "<tr><td>Count service plans</td><td><span class='error'>✗ Erreur</span></td><td>" . htmlspecialchars($e->getMessage()) . "</td></tr>";
        }

        // Test 3: Count subscriptions
        try {
            $count = $this->db->count_all_results(db_prefix() . 'dietic_subscriptions');
            echo "<tr><td>Count subscriptions</td><td><span class='success'>✓ OK</span></td><td>$count abonnements</td></tr>";
        } catch (Exception $e) {
            echo "<tr><td>Count subscriptions</td><td><span class='error'>✗ Erreur</span></td><td>" . htmlspecialchars($e->getMessage()) . "</td></tr>";
        }

        // Test 4: Get all with join
        try {
            $this->db->select('s.*, p.company as patient_name');
            $this->db->from(db_prefix() . 'dietic_subscriptions s');
            $this->db->join(db_prefix() . 'dietic_patients p', 'p.id = s.patient_id', 'left');
            $this->db->limit(1);
            $result = $this->db->get()->result();
            $count = count($result);
            echo "<tr><td>Subscriptions with patient join</td><td><span class='success'>✓ OK</span></td><td>$count résultat(s)</td></tr>";
        } catch (Exception $e) {
            echo "<tr><td>Subscriptions with patient join</td><td><span class='error'>✗ Erreur</span></td><td>" . htmlspecialchars($e->getMessage()) . "</td></tr>";
        }

        // Test 5: Model method call
        try {
            $this->load->model('dietetic/dietetic_subscriptions_model');
            $stats = $this->dietetic_subscriptions_model->get_statistics();
            echo "<tr><td>get_statistics()</td><td><span class='success'>✓ OK</span></td><td>Total: " . $stats['total'] . "</td></tr>";
        } catch (Exception $e) {
            echo "<tr><td>get_statistics()</td><td><span class='error'>✗ Erreur</span></td><td>" . htmlspecialchars($e->getMessage()) . "</td></tr>";
        }

        echo "</table>";

        // 5. PHP Error log
        echo "<h2>5. Configuration PHP</h2>";
        echo "<table>";
        echo "<tr><th>Paramètre</th><th>Valeur</th></tr>";
        echo "<tr><td>display_errors</td><td>" . ini_get('display_errors') . "</td></tr>";
        echo "<tr><td>error_reporting</td><td>" . error_reporting() . "</td></tr>";
        echo "<tr><td>PHP Version</td><td>" . phpversion() . "</td></tr>";
        echo "</table>";

        echo "<h2>6. Recommandations</h2>";
        echo "<p>Si vous voyez des erreurs ci-dessus, cela indique le problème exact.</p>";
        echo "<p>Pour activer l'affichage des erreurs PHP, ajoutez ceci au début du fichier index.php :</p>";
        echo "<pre>ini_set('display_errors', 1);
error_reporting(E_ALL);</pre>";
    }
}
