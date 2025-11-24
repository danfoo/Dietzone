<!DOCTYPE html>
<html>
<head>
    <title>Vérification Champs Formulaire Patient</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 3px solid #28a745; padding-bottom: 10px; }
        h2 { color: #555; margin-top: 30px; }
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        .warning { color: #ffc107; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f8f9fa; font-weight: bold; }
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-danger { background: #f8d7da; color: #721c24; }
        .stats { display: flex; gap: 20px; margin: 20px 0; }
        .stat-box { flex: 1; padding: 20px; border-radius: 8px; text-align: center; }
        .stat-box.success { background: #d4edda; color: #155724; }
        .stat-box.error { background: #f8d7da; color: #721c24; }
        .stat-number { font-size: 36px; font-weight: bold; }
        .stat-label { font-size: 14px; margin-top: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Diagnostic: Vérification Champs Formulaire Patient</h1>

        <?php
        // Use CodeIgniter database instance
        $CI =& get_instance();

        // Get table name with prefix
        $table_name = db_prefix() . 'dietic_patients';

        // Get table structure
        $query = $CI->db->query("DESCRIBE `{$table_name}`");

        if (!$query) {
            die('<div class="error">Erreur lors de la récupération de la structure de la table</div>');
        }

        $db_columns = [];
        foreach ($query->result_array() as $row) {
            $db_columns[] = $row['Field'];
        }

        // Form fields from the HTML form
        $form_fields = [
            'activity_level',
            'address',
            'alcohol_per_week',
            'allergies',
            'arm_circumference',
            'barriers_to_change',
            'birth_date',
            'bowel_frequency',
            'breakfast_time',
            'breastfeeding',
            'budget_level',
            'caffeine_sensitivity',
            'calf_circumference',
            'chest_circumference',
            'client_id',
            'coffee_per_day',
            'cooking_skills',
            'cultural_food_preferences',
            'dietary_preferences',
            'dietitian_id',
            'digestive_details',
            'digestive_issues',
            'dinner_time',
            'eating_disorders_history',
            'eating_environment',
            'eating_speed',
            'email',
            'emergency_contact',
            'emergency_phone',
            'family_history',
            'favorite_foods',
            'fodmap_sensitivity',
            'food_dislikes',
            'fructose_intolerance',
            'gender',
            'gluten_intolerance',
            'height',
            'hip_circumference',
            'histamine_intolerance',
            'initial_weight',
            'is_pregnant',
            'lactose_intolerance',
            'lifestyle_notes',
            'meal_preparation',
            'meals_per_day',
            'medical_conditions',
            'medications',
            'menstrual_cycle',
            'mental_health',
            'motivation_level',
            'neck_circumference',
            'objective',
            'occupation',
            'phone',
            'physical_activity_details',
            'pregnancy_months',
            'previous_diets',
            'recent_blood_work',
            'salt_preference',
            'sleep_hours',
            'sleep_quality',
            'smoking',
            'snacks_per_day',
            'soda_per_week',
            'status',
            'stress_eating',
            'stress_level',
            'supplements',
            'surgeries',
            'sweet_cravings',
            'target_weight',
            'tea_per_day',
            'thigh_circumference',
            'time_for_cooking',
            'title',
            'typical_day_diet',
            'waist_circumference',
            'water_intake',
            'weight_gain_triggers',
            'weight_history',
            'work_type'
        ];

        // Check which form fields are missing in DB
        $missing_in_db = [];
        $present_in_db = [];

        foreach ($form_fields as $field) {
            if (in_array($field, $db_columns)) {
                $present_in_db[] = $field;
            } else {
                $missing_in_db[] = $field;
            }
        }

        // Statistics
        $total_fields = count($form_fields);
        $present_count = count($present_in_db);
        $missing_count = count($missing_in_db);
        $percentage = round(($present_count / $total_fields) * 100, 1);

        echo '<div class="stats">';
        echo '<div class="stat-box success">';
        echo '<div class="stat-number">' . $present_count . '</div>';
        echo '<div class="stat-label">Champs OK dans DB</div>';
        echo '</div>';

        echo '<div class="stat-box error">';
        echo '<div class="stat-number">' . $missing_count . '</div>';
        echo '<div class="stat-label">Champs MANQUANTS</div>';
        echo '</div>';

        echo '<div class="stat-box ' . ($percentage >= 90 ? 'success' : 'error') . '">';
        echo '<div class="stat-number">' . $percentage . '%</div>';
        echo '<div class="stat-label">Complétude</div>';
        echo '</div>';
        echo '</div>';

        // Missing fields
        if (count($missing_in_db) > 0) {
            echo '<h2 class="error">❌ Champs du formulaire MANQUANTS dans la DB (' . count($missing_in_db) . ')</h2>';
            echo '<p>Ces champs sont dans le formulaire HTML mais n\'existent pas dans la table <code>' . $table_name . '</code>. <strong>C\'est ce qui cause l\'erreur 500 !</strong></p>';
            echo '<table>';
            echo '<thead><tr><th>#</th><th>Nom du champ</th><th>Action</th></tr></thead>';
            echo '<tbody>';
            foreach ($missing_in_db as $index => $field) {
                echo '<tr>';
                echo '<td>' . ($index + 1) . '</td>';
                echo '<td><code>' . htmlspecialchars($field) . '</code></td>';
                echo '<td><span class="badge badge-danger">MANQUANT</span></td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';

            echo '<div style="background: #fff3cd; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #ffc107;">';
            echo '<h3 style="margin-top: 0; color: #856404;">🔧 Solution</h3>';
            echo '<p>Pour corriger l\'erreur 500, vous devez :</p>';
            echo '<ol>';
            echo '<li><strong>Soit</strong> : Retirer ces champs du formulaire HTML</li>';
            echo '<li><strong>Soit</strong> : Ajouter ces colonnes à la table de base de données</li>';
            echo '<li><strong>Soit</strong> : Filtrer les données POST dans le contrôleur pour exclure ces champs</li>';
            echo '</ol>';
            echo '</div>';
        }

        // Present fields
        echo '<h2 class="success">✅ Champs présents dans la DB (' . count($present_in_db) . ')</h2>';
        echo '<table>';
        echo '<thead><tr><th>#</th><th>Nom du champ</th><th>Statut</th></tr></thead>';
        echo '<tbody>';
        foreach ($present_in_db as $index => $field) {
            echo '<tr>';
            echo '<td>' . ($index + 1) . '</td>';
            echo '<td><code>' . htmlspecialchars($field) . '</code></td>';
            echo '<td><span class="badge badge-success">OK</span></td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';

        // Database columns not in form
        $db_only_fields = array_diff($db_columns, $form_fields);

        if (count($db_only_fields) > 0) {
            echo '<h2 class="warning">⚠️ Colonnes DB non utilisées dans le formulaire (' . count($db_only_fields) . ')</h2>';
            echo '<p>Ces colonnes existent dans la DB mais ne sont pas dans le formulaire (normal pour les champs système).</p>';
            echo '<table>';
            echo '<thead><tr><th>#</th><th>Nom de la colonne</th></tr></thead>';
            echo '<tbody>';
            foreach ($db_only_fields as $index => $field) {
                echo '<tr>';
                echo '<td>' . ($index + 1) . '</td>';
                echo '<td><code>' . htmlspecialchars($field) . '</code></td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
        }
        ?>

        <div style="margin-top: 40px; padding: 20px; background: #e7f3ff; border-radius: 8px; border-left: 4px solid #007bff;">
            <h3 style="margin-top: 0; color: #004085;">📋 Recommandation</h3>
            <?php if ($missing_count > 0): ?>
                <p><strong>PRIORITAIRE :</strong> Corriger les <strong><?php echo $missing_count; ?> champs manquants</strong> avant de pouvoir créer des patients.</p>
            <?php else: ?>
                <p><strong>Excellent !</strong> Tous les champs du formulaire existent dans la base de données. Le problème 500 vient d'ailleurs.</p>
            <?php endif; ?>
        </div>

        <div style="margin-top: 20px; text-align: center; color: #666; font-size: 12px;">
            <p>Diagnostic généré le <?php echo date('d/m/Y H:i:s'); ?></p>
        </div>
    </div>
</body>
</html>
