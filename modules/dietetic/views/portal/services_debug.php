<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!DOCTYPE html>
<html>
<head>
    <title>Debug - Services Page</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #f5f5f5; }
        .debug-section { background: white; padding: 15px; margin: 10px 0; border-radius: 8px; border-left: 4px solid #01807B; }
        .debug-title { font-weight: bold; color: #01807B; margin-bottom: 10px; font-size: 16px; }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .warning { color: #ffc107; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 4px; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { padding: 8px; text-align: left; border: 1px solid #ddd; }
        th { background: #01807B; color: white; }
    </style>
</head>
<body>
    <h1>🔍 Debug - Page Services</h1>

    <!-- 1. Authentification -->
    <div class="debug-section">
        <div class="debug-title">1. Authentification</div>
        <?php
        $is_logged_in = $this->session->userdata('client_logged_in');
        $client_id = $this->session->userdata('client_user_id');
        ?>
        <p>Session logged in: <strong class="<?php echo $is_logged_in ? 'success' : 'error'; ?>"><?php echo $is_logged_in ? 'OUI ✓' : 'NON ✗'; ?></strong></p>
        <p>Client ID: <strong><?php echo $client_id ? $client_id : 'N/A'; ?></strong></p>
    </div>

    <!-- 2. Tables disponibles -->
    <div class="debug-section">
        <div class="debug-title">2. Tables de base de données</div>
        <?php
        $tables_to_check = [
            'items',
            'items_groups',
            'invoices',
            'itemable',
            'dietic_consultations',
            'dietetic_patients'
        ];

        echo '<table>';
        echo '<tr><th>Table</th><th>Préfixe</th><th>Nom complet</th><th>Existe</th><th>Nombre de lignes</th></tr>';

        foreach ($tables_to_check as $table) {
            $full_table = db_prefix() . $table;
            $exists = $this->db->table_exists($full_table);
            $count = 'N/A';

            if ($exists) {
                try {
                    $count = $this->db->count_all($full_table);
                } catch (Exception $e) {
                    $count = 'Erreur: ' . $e->getMessage();
                }
            }

            echo '<tr>';
            echo '<td>' . $table . '</td>';
            echo '<td>' . db_prefix() . '</td>';
            echo '<td>' . $full_table . '</td>';
            echo '<td class="' . ($exists ? 'success' : 'error') . '">' . ($exists ? 'OUI ✓' : 'NON ✗') . '</td>';
            echo '<td>' . $count . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        ?>
    </div>

    <!-- 3. Patient -->
    <div class="debug-section">
        <div class="debug-title">3. Récupération du patient</div>
        <?php
        if ($client_id) {
            try {
                $patient = $this->dietetic_patients_model->get_by_client($client_id);
                if ($patient) {
                    echo '<p class="success">✓ Patient trouvé</p>';
                    echo '<pre>';
                    print_r([
                        'ID' => $patient->id,
                        'Nom' => isset($patient->name) ? $patient->name : 'N/A',
                        'Email' => isset($patient->email) ? $patient->email : 'N/A'
                    ]);
                    echo '</pre>';
                } else {
                    echo '<p class="error">✗ Patient non trouvé pour client_id: ' . $client_id . '</p>';
                }
            } catch (Exception $e) {
                echo '<p class="error">✗ Erreur: ' . $e->getMessage() . '</p>';
            }
        } else {
            echo '<p class="warning">⚠ Pas de client_id dans la session</p>';
        }
        ?>
    </div>

    <!-- 4. Groupe Services -->
    <div class="debug-section">
        <div class="debug-title">4. Groupe "Services" dans items_groups</div>
        <?php
        try {
            $this->db->select('*');
            $this->db->from(db_prefix() . 'items_groups');
            $this->db->where('name', 'Services');
            $group = $this->db->get()->row();

            if ($group) {
                echo '<p class="success">✓ Groupe "Services" trouvé</p>';
                echo '<pre>';
                print_r($group);
                echo '</pre>';
            } else {
                echo '<p class="error">✗ Groupe "Services" non trouvé</p>';
                echo '<p>Tous les groupes disponibles :</p>';
                $all_groups = $this->db->get(db_prefix() . 'items_groups')->result();
                echo '<pre>';
                print_r($all_groups);
                echo '</pre>';
            }
        } catch (Exception $e) {
            echo '<p class="error">✗ Erreur: ' . $e->getMessage() . '</p>';
        }
        ?>
    </div>

    <!-- 5. Services (Items) -->
    <div class="debug-section">
        <div class="debug-title">5. Services disponibles</div>
        <?php
        try {
            $this->db->select('i.*, ig.name as group_name');
            $this->db->from(db_prefix() . 'items i');
            $this->db->join(db_prefix() . 'items_groups ig', 'ig.id = i.group_id', 'left');
            $this->db->where('ig.name', 'Services');
            $this->db->where('i.active', 1);
            $this->db->order_by('i.rate', 'DESC');
            $services = $this->db->get()->result();

            if ($services) {
                echo '<p class="success">✓ ' . count($services) . ' service(s) trouvé(s)</p>';
                echo '<table>';
                echo '<tr><th>ID</th><th>Description</th><th>Prix</th><th>Actif</th><th>Groupe</th></tr>';
                foreach ($services as $service) {
                    echo '<tr>';
                    echo '<td>' . $service->id . '</td>';
                    echo '<td>' . htmlspecialchars($service->description) . '</td>';
                    echo '<td>' . number_format($service->rate, 0, ',', ' ') . ' FCFA</td>';
                    echo '<td>' . ($service->active ? 'Oui' : 'Non') . '</td>';
                    echo '<td>' . $service->group_name . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            } else {
                echo '<p class="warning">⚠ Aucun service trouvé</p>';

                // Show all items regardless of group
                echo '<p>Tous les items disponibles :</p>';
                $this->db->select('i.*, ig.name as group_name');
                $this->db->from(db_prefix() . 'items i');
                $this->db->join(db_prefix() . 'items_groups ig', 'ig.id = i.group_id', 'left');
                $all_items = $this->db->get()->result();
                echo '<pre>';
                print_r($all_items);
                echo '</pre>';
            }
        } catch (Exception $e) {
            echo '<p class="error">✗ Erreur: ' . $e->getMessage() . '</p>';
            echo '<pre>' . $e->getTraceAsString() . '</pre>';
        }
        ?>
    </div>

    <!-- 6. Factures impayées -->
    <div class="debug-section">
        <div class="debug-title">6. Factures impayées du client</div>
        <?php
        if ($client_id) {
            try {
                $this->db->select('id, invoicenumber, total, status, date, duedate');
                $this->db->from(db_prefix() . 'invoices');
                $this->db->where('clientid', $client_id);
                $all_invoices = $this->db->get()->result();

                echo '<p>Total factures: ' . count($all_invoices) . '</p>';

                if ($all_invoices) {
                    echo '<table>';
                    echo '<tr><th>ID</th><th>Numéro</th><th>Montant</th><th>Status</th><th>Date</th></tr>';
                    foreach ($all_invoices as $inv) {
                        $status_label = '';
                        switch($inv->status) {
                            case 1: $status_label = 'Unpaid'; break;
                            case 2: $status_label = 'Paid'; break;
                            case 3: $status_label = 'Partially Paid'; break;
                            case 4: $status_label = 'Overdue'; break;
                            case 5: $status_label = 'Cancelled'; break;
                            default: $status_label = 'Unknown (' . $inv->status . ')';
                        }

                        echo '<tr>';
                        echo '<td>' . $inv->id . '</td>';
                        echo '<td>' . $inv->invoicenumber . '</td>';
                        echo '<td>' . number_format($inv->total, 0, ',', ' ') . ' FCFA</td>';
                        echo '<td>' . $status_label . '</td>';
                        echo '<td>' . $inv->date . '</td>';
                        echo '</tr>';
                    }
                    echo '</table>';
                }

                // Check unpaid
                $this->db->select('id, invoicenumber, total, status');
                $this->db->from(db_prefix() . 'invoices');
                $this->db->where('clientid', $client_id);
                $this->db->where_in('status', [1, 2, 4, 5]);
                $unpaid = $this->db->get()->result();

                echo '<p class="' . (count($unpaid) > 0 ? 'warning' : 'success') . '">';
                echo count($unpaid) > 0 ? '⚠ ' . count($unpaid) . ' facture(s) impayée(s)' : '✓ Aucune facture impayée';
                echo '</p>';

            } catch (Exception $e) {
                echo '<p class="error">✗ Erreur: ' . $e->getMessage() . '</p>';
            }
        }
        ?>
    </div>

    <!-- 7. Consultations -->
    <div class="debug-section">
        <div class="debug-title">7. Consultations du patient</div>
        <?php
        if (isset($patient) && $patient) {
            try {
                // Try both table names
                $consultation_count = 0;
                $table_used = '';

                if ($this->db->table_exists(db_prefix() . 'dietic_consultations')) {
                    $consultation_count = $this->db
                        ->where('patient_id', $patient->id)
                        ->where('status !=', 'cancelled')
                        ->count_all_results(db_prefix() . 'dietic_consultations');
                    $table_used = db_prefix() . 'dietic_consultations';
                } elseif ($this->db->table_exists(db_prefix() . 'dietetic_consultations')) {
                    $consultation_count = $this->db
                        ->where('patient_id', $patient->id)
                        ->where('status !=', 'cancelled')
                        ->count_all_results(db_prefix() . 'dietetic_consultations');
                    $table_used = db_prefix() . 'dietetic_consultations';
                }

                echo '<p>Table utilisée: <strong>' . ($table_used ? $table_used : 'AUCUNE') . '</strong></p>';
                echo '<p class="' . ($consultation_count > 0 ? 'success' : 'warning') . '">';
                echo $consultation_count > 0 ? '✓ ' . $consultation_count . ' consultation(s) trouvée(s)' : '⚠ Aucune consultation';
                echo '</p>';

            } catch (Exception $e) {
                echo '<p class="error">✗ Erreur: ' . $e->getMessage() . '</p>';
            }
        }
        ?>
    </div>

    <!-- 8. Modèles chargés -->
    <div class="debug-section">
        <div class="debug-title">8. Modèles disponibles</div>
        <?php
        $models_to_check = [
            'dietetic_patients_model',
            'invoices_model'
        ];

        foreach ($models_to_check as $model) {
            $loaded = isset($this->$model);
            echo '<p class="' . ($loaded ? 'success' : 'error') . '">';
            echo $model . ': ' . ($loaded ? 'CHARGÉ ✓' : 'NON CHARGÉ ✗');
            echo '</p>';
        }
        ?>
    </div>

    <!-- 9. Erreurs PHP -->
    <div class="debug-section">
        <div class="debug-title">9. Configuration PHP</div>
        <p>Error Reporting: <strong><?php echo ini_get('display_errors') ? 'ON' : 'OFF'; ?></strong></p>
        <p>Log Errors: <strong><?php echo ini_get('log_errors') ? 'ON' : 'OFF'; ?></strong></p>
        <p>Error Log: <strong><?php echo ini_get('error_log'); ?></strong></p>
    </div>

    <hr style="margin: 30px 0;">
    <p><a href="<?php echo site_url('dietetic/portal/services'); ?>" style="background: #01807B; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">← Retour à la page Services</a></p>
</body>
</html>
