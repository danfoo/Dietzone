# Instructions de Debug - Problème de Persistence des Cases à Cocher

## Étape 1: Vérifier la contrainte UNIQUE

1. **Ouvrez phpMyAdmin**
2. **Sélectionnez votre base de données**
3. **Cliquez sur l'onglet "SQL"**
4. **Copiez et exécutez ce code**:

```sql
SELECT
    CONSTRAINT_NAME,
    CONSTRAINT_TYPE,
    TABLE_NAME
FROM
    information_schema.TABLE_CONSTRAINTS
WHERE
    TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'tbldietic_daily_tracking'
    AND CONSTRAINT_TYPE = 'UNIQUE';
```

**Résultat attendu**: Vous devriez voir une ligne avec `unique_patient_date`

**Si la contrainte n'existe PAS**, exécutez la migration complète depuis le fichier `add_unique_constraint_daily_tracking.sql`

## Étape 2: Tester avec le Debug

1. **Ouvrez** : https://app.dietsenegal.net/dietetic/portal/portal_dashboard
2. **Ouvrez la console du navigateur** (F12 → onglet Console)
3. **Cochez une case** (Petit déjeuner, Déjeuner ou Dîner)
4. **Regardez la console** - Vous verrez :
   ```
   🔍 DEBUG - Toggle Meal Response: {...}
   📊 DEBUG INFO: {
       Patient ID: X,
       Meal: "breakfast",
       Requested: "CHECKED",
       Actual in DB: "CHECKED",
       ...
   }
   ```

5. **Actualisez la page** (F5)
6. **Vérifiez si la case est toujours cochée**

## Étape 3: Analyser les Logs

### Informations à vérifier dans la console:

1. **Success = true ?**
   - Si `false`, regardez l'erreur

2. **Requested vs Actual**:
   - `Requested: CHECKED` et `Actual in DB: CHECKED` = ✅ CORRECT
   - `Requested: CHECKED` et `Actual in DB: UNCHECKED` = ❌ PROBLÈME

3. **Tracking ID**:
   - Doit avoir une valeur (pas `null`)
   - Note le numéro

4. **Date**:
   - Doit être la date d'aujourd'hui

## Étape 4: Vérifier dans la base de données

Si le problème persiste, vérifiez directement en base:

```sql
-- Remplacez X par votre patient_id (visible dans le DEBUG INFO)
SELECT * FROM `tbldietic_daily_tracking`
WHERE patient_id = X
AND tracking_date = CURDATE()
ORDER BY id DESC;
```

**Vérifiez**:
- Il doit y avoir **UNE SEULE ligne** pour aujourd'hui
- Les champs `breakfast_checked`, `lunch_checked`, `dinner_checked` doivent être à `1` si cochés

## Étape 5: Vérifier les doublons

```sql
SELECT
    patient_id,
    tracking_date,
    COUNT(*) as count
FROM `tbldietic_daily_tracking`
GROUP BY patient_id, tracking_date
HAVING COUNT(*) > 1;
```

**Résultat attendu**: Aucune ligne (pas de doublons)

**Si vous avez des doublons**: La migration n'a pas été exécutée correctement.

## Étape 6: Rapporter les résultats

Envoyez-moi:
1. ✅ ou ❌ La contrainte UNIQUE existe ?
2. Les informations de la console (screenshot ou copie)
3. Le résultat de la requête SQL de vérification
4. ✅ ou ❌ Y a-t-il des doublons ?

Cela me permettra d'identifier exactement où est le problème !
