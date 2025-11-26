<!DOCTYPE html>
<html>
<head>
    <title>Liste des colonnes DB</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; }
        h1 { color: #333; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
        .column { padding: 8px; margin: 4px 0; background: #e9ecef; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📋 Colonnes de la table tbldietic_patients</h1>

        <?php
        $CI =& get_instance();
        $table_name = db_prefix() . 'dietic_patients';

        echo "<p><strong>Table:</strong> <code>{$table_name}</code></p>";

        $query = $CI->db->query("DESCRIBE `{$table_name}`");

        if ($query) {
            $columns = $query->result_array();
            echo "<p><strong>Total colonnes:</strong> " . count($columns) . "</p>";

            echo "<h2>Liste des colonnes:</h2>";
            foreach ($columns as $col) {
                echo "<div class='column'>";
                echo "<strong>" . htmlspecialchars($col['Field']) . "</strong> ";
                echo "(" . htmlspecialchars($col['Type']) . ") ";
                if ($col['Null'] == 'YES') echo "NULL ";
                if ($col['Key'] == 'PRI') echo "PRIMARY KEY ";
                if (!empty($col['Default'])) echo "DEFAULT: " . htmlspecialchars($col['Default']);
                echo "</div>";
            }

            echo "<h2>Format array PHP:</h2>";
            echo "<pre>";
            $field_names = array_column($columns, 'Field');
            echo "[\n";
            foreach ($field_names as $name) {
                echo "    '{$name}',\n";
            }
            echo "]";
            echo "</pre>";
        } else {
            echo "<p style='color: red;'>Erreur lors de la récupération de la structure</p>";
        }
        ?>
    </div>
</body>
</html>
