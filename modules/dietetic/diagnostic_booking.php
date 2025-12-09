<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Diagnostic script for appointment booking system
 * Checks and initializes availability data
 */

// Load CodeIgniter
require_once(dirname(__FILE__) . '/../../../application/config/database.php');

$CI = &get_instance();

echo "<h2>Diagnostic - Système de Prise de Rendez-vous</h2>";
echo "<hr>";

// 1. Check if availability table exists
echo "<h3>1. Vérification de la table de disponibilités</h3>";
$table_exists = $CI->db->table_exists(db_prefix() . 'dietic_dietitian_availability');
echo "<p>Table " . db_prefix() . "dietic_dietitian_availability : " . ($table_exists ? '<span style="color: green;">✓ Existe</span>' : '<span style="color: red;">✗ N\'existe pas</span>') . "</p>";

if (!$table_exists) {
    echo "<p style='color: red;'>ERREUR : La table de disponibilités n'existe pas. Veuillez exécuter la migration availability_system.</p>";
    exit;
}

// 2. Check consultation types table
echo "<h3>2. Vérification des types de consultation</h3>";
$types_table_exists = $CI->db->table_exists(db_prefix() . 'dietic_consultation_types');
echo "<p>Table " . db_prefix() . "dietic_consultation_types : " . ($types_table_exists ? '<span style="color: green;">✓ Existe</span>' : '<span style="color: red;">✗ N\'existe pas</span>') . "</p>";

if ($types_table_exists) {
    $CI->db->select('COUNT(*) as count');
    $types_count = $CI->db->get(db_prefix() . 'dietic_consultation_types')->row()->count;
    echo "<p>Nombre de types de consultation : <strong>{$types_count}</strong></p>";

    if ($types_count > 0) {
        $CI->db->select('id, name, slug, duration');
        $types = $CI->db->get(db_prefix() . 'dietic_consultation_types')->result();
        echo "<ul>";
        foreach ($types as $type) {
            echo "<li>{$type->name} ({$type->slug}) - {$type->duration} min</li>";
        }
        echo "</ul>";
    }
}

// 3. Check dietitians (staff members)
echo "<h3>3. Vérification des diététiciens</h3>";
$CI->db->select('staffid, CONCAT(firstname, " ", lastname) as name, email');
$CI->db->where('active', 1);
$staff = $CI->db->get(db_prefix() . 'staff')->result();
echo "<p>Nombre de membres du staff actifs : <strong>" . count($staff) . "</strong></p>";

if (count($staff) > 0) {
    echo "<ul>";
    foreach ($staff as $member) {
        echo "<li>ID: {$member->staffid} - {$member->name} ({$member->email})</li>";
    }
    echo "</ul>";
}

// 4. Check existing availabilities
echo "<h3>4. Disponibilités configurées</h3>";
$CI->db->select('da.*, CONCAT(s.firstname, " ", s.lastname) as dietitian_name');
$CI->db->from(db_prefix() . 'dietic_dietitian_availability da');
$CI->db->join(db_prefix() . 'staff s', 's.staffid = da.dietitian_id');
$availabilities = $CI->db->get()->result();

echo "<p>Nombre total de créneaux de disponibilité : <strong>" . count($availabilities) . "</strong></p>";

if (count($availabilities) > 0) {
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr><th>Diététicien</th><th>Jour</th><th>Début</th><th>Fin</th><th>Durée slot</th><th>Actif</th></tr>";

    $days = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];

    foreach ($availabilities as $avail) {
        $day_name = $days[$avail->day_of_week];
        $active = $avail->is_active ? '<span style="color: green;">Oui</span>' : '<span style="color: red;">Non</span>';
        echo "<tr>";
        echo "<td>{$avail->dietitian_name}</td>";
        echo "<td>{$day_name}</td>";
        echo "<td>{$avail->start_time}</td>";
        echo "<td>{$avail->end_time}</td>";
        echo "<td>{$avail->slot_duration} min</td>";
        echo "<td>{$active}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<div style='background: #ffebee; padding: 15px; border-left: 4px solid #f44336; margin: 15px 0;'>";
    echo "<h4 style='margin-top: 0; color: #d32f2f;'>⚠ Aucune disponibilité configurée</h4>";
    echo "<p>Les diététiciens n'ont pas encore de créneaux de disponibilité configurés.</p>";
    echo "<p><strong>Solution :</strong> Vous devez créer des disponibilités pour chaque diététicien.</p>";

    // Offer to create sample data
    if (count($staff) > 0 && isset($_GET['create_sample'])) {
        echo "<hr>";
        echo "<h4>Création de disponibilités d'exemple...</h4>";

        $first_staff = $staff[0];
        $sample_availabilities = [
            ['day_of_week' => 1, 'start_time' => '09:00:00', 'end_time' => '12:00:00'], // Lundi matin
            ['day_of_week' => 1, 'start_time' => '14:00:00', 'end_time' => '18:00:00'], // Lundi après-midi
            ['day_of_week' => 2, 'start_time' => '09:00:00', 'end_time' => '12:00:00'], // Mardi matin
            ['day_of_week' => 2, 'start_time' => '14:00:00', 'end_time' => '18:00:00'], // Mardi après-midi
            ['day_of_week' => 3, 'start_time' => '09:00:00', 'end_time' => '12:00:00'], // Mercredi matin
            ['day_of_week' => 4, 'start_time' => '09:00:00', 'end_time' => '12:00:00'], // Jeudi matin
            ['day_of_week' => 4, 'start_time' => '14:00:00', 'end_time' => '18:00:00'], // Jeudi après-midi
            ['day_of_week' => 5, 'start_time' => '09:00:00', 'end_time' => '12:00:00'], // Vendredi matin
        ];

        $inserted = 0;
        foreach ($sample_availabilities as $avail) {
            $data = [
                'dietitian_id' => $first_staff->staffid,
                'day_of_week' => $avail['day_of_week'],
                'start_time' => $avail['start_time'],
                'end_time' => $avail['end_time'],
                'slot_duration' => 60,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ];

            if ($CI->db->insert(db_prefix() . 'dietic_dietitian_availability', $data)) {
                $inserted++;
            }
        }

        echo "<p style='color: green;'>✓ <strong>{$inserted}</strong> créneaux de disponibilité créés pour {$first_staff->name}</p>";
        echo "<p><a href='?'>Rafraîchir la page</a></p>";
    } else if (count($staff) > 0) {
        echo "<p><a href='?create_sample=1' class='btn btn-success'>Créer des disponibilités d'exemple pour {$staff[0]->name}</a></p>";
    }

    echo "</div>";
}

// 5. Check patient-dietitian assignments
echo "<h3>5. Assignations patient-diététicien</h3>";
$assignments_table_exists = $CI->db->table_exists(db_prefix() . 'dietic_patient_dietitians');
if ($assignments_table_exists) {
    $CI->db->select('COUNT(*) as count');
    $assignments_count = $CI->db->get(db_prefix() . 'dietic_patient_dietitians')->row()->count;
    echo "<p>Nombre d'assignations : <strong>{$assignments_count}</strong></p>";
} else {
    echo "<p style='color: red;'>Table d'assignations n'existe pas</p>";
}

// 6. Check booked_by_patient field
echo "<h3>6. Migration patient booking</h3>";
$columns = $CI->db->list_fields(db_prefix() . 'dietic_consultations');
$has_field = in_array('booked_by_patient', $columns);
echo "<p>Champ 'booked_by_patient' : " . ($has_field ? '<span style="color: green;">✓ Existe</span>' : '<span style="color: red;">✗ Manquant</span>') . "</p>";

echo "<hr>";
echo "<h3>Résumé</h3>";

$all_ok = $table_exists && $types_table_exists && count($availabilities) > 0 && $has_field;

if ($all_ok) {
    echo "<div style='background: #e8f5e9; padding: 15px; border-left: 4px solid #4caf50;'>";
    echo "<h4 style='margin-top: 0; color: #2e7d32;'>✓ Système prêt</h4>";
    echo "<p>Le système de prise de rendez-vous est correctement configuré et prêt à l'emploi.</p>";
    echo "</div>";
} else {
    echo "<div style='background: #fff3e0; padding: 15px; border-left: 4px solid #ff9800;'>";
    echo "<h4 style='margin-top: 0; color: #e65100;'>⚠ Configuration incomplète</h4>";
    echo "<p>Certains éléments nécessitent une configuration supplémentaire.</p>";
    echo "</div>";
}

echo "<hr>";
echo "<p><a href='" . admin_url('dietetic') . "'>← Retour au module Dietetic</a></p>";
