# Guide de Diagnostic des Notifications Portail Patient

**Date** : 2 Décembre 2025
**Problème** : Notifications ne s'affichent pas / Badge ne s'affiche pas
**Solution** : Page de diagnostic complète

---

## 🎯 Symptômes Reportés

Le patient ne peut pas :
1. ❌ Voir les notifications quand il clique sur la cloche 🔔
2. ❌ Voir le badge de compteur de notifications non lues

---

## 🔍 Page de Diagnostic

Une page de diagnostic complète a été créée pour identifier rapidement le problème.

### Accès

**URL** : `https://app.dietsenegal.net/dietetic/portal/diagnostic_notifications`

ou en local :
```
http://localhost/dietetic/portal/diagnostic_notifications
```

### Qui peut y accéder ?

- ✅ **Patients connectés** (via le portail client)
- ✅ **Staff/Admin** (pour diagnostic à distance)

---

## 🧪 Tests Effectués par la Page

### Test 1 : Statut de Connexion
- Vérifie si l'utilisateur est connecté (staff ou client)
- Affiche le Client ID si connecté

**Résultat attendu** : Client connecté = OUI (badge vert)

**Si échoue** : Le patient n'est pas correctement authentifié
- Solution : Déconnexion puis reconnexion
- Vérifier les cookies/session

---

### Test 2 : Enregistrement Patient
- Vérifie qu'il existe un enregistrement patient lié au client
- Affiche Patient ID, Nom, Prénom

**Résultat attendu** : Patient trouvé = OUI (badge vert)

**Si échoue** : Aucun enregistrement patient pour ce client
- **Cause possible** : Client créé mais pas de fiche patient
- **Solution** :
  1. Admin > Dietetic > Patients
  2. Créer une fiche patient en liant le client
  3. Vérifier que le champ `client_id` est rempli

---

### Test 3 : Tables de Base de Données

#### Table `tbldietic_notification_logs`
**Rôle** : Logs de toutes les notifications envoyées (historique)

**Résultat attendu** : EXISTE (badge vert) + Nombre de notifications > 0

**Si échoue** :
- **Cause** : Migration notifications pas appliquée
- **Solution** :
  1. Admin > Dietetic > Notifications > Migrations
  2. Cliquer sur "Appliquer" pour la migration notifications

---

#### Table `tbldietic_patient_notifications`
**Rôle** : Notifications destinées spécifiquement aux patients (avec is_read, url, etc.)

**Résultat attendu** : EXISTE (badge vert)

**Si échoue** :
- **Cause** : Migration pas appliquée
- **Impact** : Le système utilisera `notification_logs` en fallback (fonctionnalité réduite)
- **Solution** :
  1. Aller sur : Admin > Dietetic > Notifications > Migrations
  2. Chercher la migration `add_patient_notifications.sql`
  3. L'appliquer manuellement

**Note** : Le système peut fonctionner sans cette table (mode fallback) mais avec limitations :
- ❌ Pas de marquage "lu/non lu"
- ❌ Pas de suppression de notifications
- ❌ Toutes les notifications apparaissent comme "non lues"

---

#### Table `tbldietic_notification_settings`
**Rôle** : Configuration globale du système de notifications

**Résultat attendu** : EXISTE (badge vert)

**Si échoue** :
- **Cause** : Migration notifications pas complète
- **Solution** : Même procédure que pour `notification_logs`

---

### Test 4 : API get_notifications

Ce test fait un appel AJAX à l'API qui charge les notifications.

**Bouton** : "Tester l'API maintenant"

**Résultat attendu** :
```json
{
  "success": true,
  "notifications": [...],
  "unread_count": X,
  "total": Y
}
```

**Si échoue avec `success: false`** :

1. **Message : "Not authenticated"**
   - Le patient n'est pas connecté
   - Solution : Reconnexion

2. **Message : "Patient not found"**
   - Pas d'enregistrement patient lié
   - Solution : Créer fiche patient (Test 2)

3. **Message : "Notifications system not yet installed"**
   - Tables manquantes
   - Solution : Appliquer migrations (Test 3)

4. **Message : "Database error"**
   - Erreur SQL
   - Solution : Consulter les logs PHP/MySQL

**Si échoue avec erreur réseau** :
- Vérifier que l'URL `/dietetic/portal/get_notifications` est accessible
- Vérifier les logs serveur

---

### Test 5 : Console JavaScript

Instructions pour diagnostic en temps réel dans le navigateur.

**Procédure** :
1. Ouvrir le portail patient
2. Appuyer sur **F12** (Console développeur)
3. Aller dans l'onglet **Console**
4. Cliquer sur la cloche 🔔
5. Observer les messages `[NOTIF]`

**Messages attendus** :
```
📥 [NOTIF] API Response: {success: true, ...}
✅ [NOTIF] Success! Found 5 notifications, unread: 3
🔍 [NOTIF] Filtered notifications (filter=all): 5
🎨 [NOTIF] displayNotifications called with 5 notifications
✅ [NOTIF] Found notification-panel-content element
📝 [NOTIF] Building HTML for 5 notifications
```

**Messages d'erreur possibles** :

1. **❌ [NOTIF] API returned error: ...**
   - L'API a retourné `success: false`
   - Vérifier le message d'erreur
   - Faire Test 4 ci-dessus

2. **⚠️ [NOTIF] No notifications to display**
   - Aucune notification dans la base
   - Créer des notifications de test

3. **❌ [NOTIF] notification-panel-content element not found!**
   - Problème DOM/HTML
   - Vérifier que `portal_header.php` est bien chargé
   - Vérifier qu'il n'y a pas d'erreur JavaScript avant

4. **Fetch failed / Network error**
   - Problème de connexion serveur
   - Vérifier URL de l'API
   - Vérifier logs serveur

---

## 🔧 Solutions aux Problèmes Courants

### Problème 1 : Badge ne s'affiche pas

**Code concerné** : `portal_header.php` ligne 850
```html
<span class="notification-badge" style="display: none;">0</span>
```

**Cause** : Badge caché par défaut, affiché uniquement si `unread_count > 0`

**JavaScript concerné** : `portal_footer.php` ligne 637-651
```javascript
function updateNotificationBadge(count) {
    const badge = document.querySelector('.notification-badge');
    if (badge) {
        const unreadCount = count !== undefined ? count : ...;
        if (unreadCount > 0) {
            badge.textContent = unreadCount;
            badge.style.display = 'flex';
        } else {
            badge.style.display = 'none';
        }
    }
}
```

**Diagnostic** :
1. Ouvrir la console (F12)
2. Chercher l'appel à `updateNotificationBadge(X)`
3. Vérifier la valeur de `X`

**Si X = 0** : Aucune notification non lue
- Créer des notifications de test

**Si fonction pas appelée** : API n'a pas retourné de données
- Voir Test 4 ci-dessus

---

### Problème 2 : Cloche ne fait rien au clic

**JavaScript concerné** : `portal_footer.php` ligne 401-421

**Cause possible** :
1. Event listener pas attaché
2. Élément `notificationPanel` introuvable
3. Erreur JavaScript avant l'initialisation

**Diagnostic** :
1. Console (F12)
2. Taper : `document.getElementById('notificationPanel')`
3. Si retourne `null` : Problème DOM

**Solution** :
- Vérifier que `portal_header.php` est bien inclus
- Vérifier qu'il n'y a pas d'erreur PHP qui casse le HTML
- Vérifier les logs d'erreurs JavaScript

---

### Problème 3 : Panneau vide "Aucune notification"

**Causes possibles** :

1. **Aucune notification en base**
   - Vérifier Test 3 (compteur = 0)
   - Solution : Créer notifications de test

2. **API retourne tableau vide**
   - Faire Test 4
   - Vérifier `notifications: []` dans la réponse

3. **Notifications existent mais filtrées**
   - Vérifier que `patient_id` du log correspond au patient connecté
   - SQL dans la page diagnostic montre la requête exacte

**Requête SQL attendue** (mode fallback) :
```sql
SELECT id, patient_id, notification_type, message, channel, status, created_at
FROM tbldietic_notification_logs
WHERE patient_id IN (X, 0)  -- X = patient_id, 0 = system notifications
  AND status = 'sent'
ORDER BY created_at DESC
LIMIT 50
```

**Vérification manuelle en BDD** :
```sql
-- Remplacer X par le patient_id affiché dans la page diagnostic
SELECT COUNT(*) as total
FROM tbldietic_notification_logs
WHERE patient_id IN (X, 0)
  AND status = 'sent';
```

Si retourne 0 : Aucune notification pour ce patient
- Solution : Créer une notification de test depuis l'admin

---

### Problème 4 : Notifications s'affichent mais badge reste à 0

**Cause** : `unread_count` retourné par l'API = 0

**Mode fallback** (`tbldietic_patient_notifications` pas installé) :
- Ligne 2412 de `Portal.php` :
```php
'unread_count' => count($formatted_notifications)
```
- Devrait compter toutes les notifications comme "non lues"

**Mode normal** (table existe) :
- Appel à `get_unread_count($patient_id)` dans le modèle
- Vérifie `is_read = 0`

**Diagnostic** :
1. Faire Test 4 (API)
2. Vérifier la valeur de `unread_count` dans la réponse JSON

**Si `unread_count = 0` mais `total > 0`** :
- Toutes les notifications sont marquées comme lues
- Solution :
  - Créer de nouvelles notifications
  - ou Reset la colonne `is_read` en BDD :
```sql
UPDATE tbldietic_patient_notifications
SET is_read = 0
WHERE patient_id = X;
```

---

## 📊 Résumé du Flux de Fonctionnement

```
1. Page chargée → DOMContentLoaded
2. JavaScript appelle loadNotifications() (ligne 374)
3. Fetch vers /dietetic/portal/get_notifications
4. API vérifie is_client_logged_in() → récupère patient
5. API compte notifications (mode normal ou fallback)
6. API retourne JSON {success, notifications, unread_count}
7. JavaScript reçoit réponse → displayNotifications()
8. JavaScript met à jour badge → updateNotificationBadge()
9. Badge devient visible si unread_count > 0
```

**Points de défaillance possibles** :
- ❌ Étape 2 : JavaScript pas chargé (erreur avant)
- ❌ Étape 3 : URL incorrecte, réseau
- ❌ Étape 4 : Pas authentifié, patient introuvable
- ❌ Étape 5 : Table manquante, SQL error
- ❌ Étape 7 : JSON invalide, erreur parsing
- ❌ Étape 8 : Élément badge introuvable

---

## 🛠️ Créer des Notifications de Test

Si aucune notification n'existe en base, créer des notifications de test :

### Via l'Admin

1. Admin > Dietetic > Notifications > Dashboard
2. Cliquer sur "Envoyer une notification de test"

### Via SQL

```sql
-- Remplacer X par le patient_id
INSERT INTO tbldietic_notification_logs
(patient_id, notification_type, message, channel, status, created_at)
VALUES
(X, 'test', 'Ceci est une notification de test', 'email', 'sent', NOW()),
(X, 'info', 'Votre profil a été mis à jour', 'email', 'sent', NOW()),
(X, 'recommendation', 'Nouvelle recommandation disponible', 'email', 'sent', DATE_SUB(NOW(), INTERVAL 1 HOUR));
```

**Note** : En mode fallback, ces notifications s'afficheront immédiatement dans le portail patient.

---

## ✅ Checklist de Validation

Après avoir appliqué les corrections :

- [ ] Page diagnostic accessible (`/dietetic/portal/diagnostic_notifications`)
- [ ] Test 1 : Client connecté = OUI ✅
- [ ] Test 2 : Patient trouvé = OUI ✅
- [ ] Test 3 : Table `notification_logs` EXISTE ✅
- [ ] Test 3 : Au moins 1 notification dans la table
- [ ] Test 4 : API retourne `{"success": true}` ✅
- [ ] Test 4 : API retourne `unread_count > 0`
- [ ] Test 5 : Console affiche `✅ [NOTIF] Success!`
- [ ] Badge de notification visible avec le bon compteur
- [ ] Clic sur cloche ouvre le panneau
- [ ] Notifications affichées dans le panneau
- [ ] Clic sur notification la marque comme lue (si table patient_notifications existe)

---

## 📞 Support

Si le problème persiste après avoir suivi ce guide :

1. **Capturer les informations suivantes** :
   - Screenshot de la page diagnostic
   - Contenu de la console JavaScript (F12)
   - Résultat du Test 4 (réponse API)
   - Logs PHP/Apache

2. **Vérifier les logs serveur** :
   ```bash
   # Logs PHP
   tail -f /var/log/php/error.log

   # Logs Apache
   tail -f /var/log/apache2/error.log

   # Logs application (Perfex CRM)
   cat application/logs/*.php
   ```

3. **Vérifier les logs d'activité Perfex** :
   ```
   Admin > Utilities > Activity Log
   Filtrer par : "NOTIF"
   ```

4. **Contacter le support technique** avec toutes les infos ci-dessus

---

## 📝 Fichiers Modifiés

| Fichier | Modification | Description |
|---------|--------------|-------------|
| `modules/dietetic/controllers/Portal.php` | Ligne 151 | Ajout `'diagnostic_notifications'` dans `valid_methods` |
| `modules/dietetic/controllers/Portal.php` | Ligne 2779-2861 | Nouvelle méthode `diagnostic_notifications()` |
| `modules/dietetic/views/portal_diagnostic_notifications.php` | Nouveau fichier | Vue HTML de la page diagnostic |
| `NOTIFICATIONS_DIAGNOSTIC_GUIDE.md` | Nouveau fichier | Ce guide |

---

## 🎯 Conclusion

La page de diagnostic permet d'identifier rapidement :
- ✅ Si l'utilisateur est correctement authentifié
- ✅ Si la fiche patient existe
- ✅ Si les tables de notifications existent
- ✅ Si l'API fonctionne correctement
- ✅ Ce que voit le JavaScript en temps réel

**Prochaines étapes** :
1. Le patient accède à `/dietetic/portal/diagnostic_notifications`
2. Suit les résultats des tests
3. Applique les solutions recommandées
4. Vérifie que le système fonctionne normalement

---

**Développé par** : Claude AI - Lead Developer
**Session** : `claude/continue-dietzone-project-0171nEu5nhNU4akFNbfjvoHq`
**Date** : 2 Décembre 2025
**Statut** : ✅ PRÊT À TESTER
