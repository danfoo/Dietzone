# Diagnostic: Problème de Déconnexion sur Page Factures

## 🔍 Problème Identifié

Les patients se font déconnecter lorsqu'ils essaient d'accéder à la page des factures (`/dietetic/portal/invoices`).

## 📊 Outils de Diagnostic Créés

### 1. Script de Test de Session (`/modules/dietetic/test_session.php`)

**URL:** `https://app.dietsenegal.net/modules/dietetic/test_session.php`

**Objectif:** Vérifier si les sessions persistent correctement entre les pages.

**Utilisation:**
1. Accédez à l'URL ci-dessus
2. Cliquez sur "Définir la session" - cela définit des variables de test
3. Attendez 2-3 secondes
4. Cliquez sur "Vérifier la session" - cela vérifie si les variables persistent
5. Si les variables sont présentes, les sessions fonctionnent
6. Si les variables sont vides, il y a un problème de persistance de session

**Ce que ce test révèle:**
- Si les cookies de session sont correctement configurés
- Si les sessions persistent entre les requêtes
- Si le chemin/domaine des cookies est correct
- La configuration PHP des sessions

### 2. Script de Test d'Authentification (`/modules/dietetic/test_auth.php`)

**URL:** `https://app.dietsenegal.net/modules/dietetic/test_auth.php`

**Objectif:** Vérifier l'authentification du patient connecté.

**Utilisation:**
1. Connectez-vous d'abord au portail patient
2. Accédez à l'URL ci-dessus
3. Vérifiez si les fonctions d'authentification fonctionnent
4. Vérifiez si le patient est trouvé dans la base de données

**Ce que ce test révèle:**
- Si `is_client_logged_in()` fonctionne correctement
- Si `get_client_user_id()` retourne le bon ID
- Si le patient existe dans la base de données
- Toutes les variables de session actuelles

### 3. Logs d'Activité Ajoutés

Des logs détaillés ont été ajoutés dans:
- `Portal::index()` - Page d'accueil du portail
- `Portal::invoices()` - Liste des factures
- `Portal::invoice($id)` - Détail d'une facture
- Après chaque login (login_patient, index POST, verify_registration_otp)

**Où consulter les logs:**
- Admin Perfex → Setup → Activity Log
- Filtrer par description contenant: "INVOICES PAGE", "INVOICE VIEW", "INDEX PAGE", "LOGIN"

## 🔎 Procédure de Diagnostic

### Étape 1: Tester la Persistance de Session

```
1. Aller sur: https://app.dietsenegal.net/modules/dietetic/test_session.php
2. Cliquer "Définir la session"
3. Attendre 3 secondes
4. Cliquer "Vérifier la session"
5. Noter si les variables persistent
```

**Résultat attendu:** Les variables `test_timestamp`, `test_value`, `client_logged_in`, et `client_user_id` doivent être présentes.

**Si échec:** Il y a un problème de configuration de session PHP ou de cookies.

### Étape 2: Reproduire le Problème avec Logs

```
1. Se déconnecter complètement
2. Se connecter de nouveau au portail patient
3. Vérifier les logs: chercher "LOGIN - Session avant redirect"
   → Doit montrer client_logged_in=true et client_user_id={nombre}
4. Cliquer sur "Mes Factures" dans le menu
5. Vérifier les logs: chercher "INVOICES PAGE - Session data"
   → Doit montrer les mêmes données de session
```

**Si les logs montrent session vide sur INVOICES PAGE:**
→ Problème de persistance entre pages

**Si les logs montrent client_logged_in=false ou NULL:**
→ Quelque chose modifie la session entre les pages

### Étape 3: Tester l'Authentification

```
1. Se connecter au portail patient
2. Aller sur: https://app.dietsenegal.net/modules/dietetic/test_auth.php
3. Vérifier:
   - is_client_logged_in() retourne TRUE
   - get_client_user_id() retourne un ID valide
   - Patient trouvé dans la base de données
```

## 📝 Informations à Collecter

Pour analyser le problème, collectez ces informations:

1. **Résultat du test de session** (test_session.php)
2. **Résultat du test d'authentification** (test_auth.php)
3. **Logs d'activité** contenant:
   - "LOGIN - Session avant redirect"
   - "INDEX PAGE - Session data"
   - "INVOICES PAGE - Session data"
   - "INVOICE VIEW - Session data"

## 🔧 Hypothèses et Causes Possibles

### Hypothèse 1: Sessions ne persistent pas
**Symptômes:**
- test_session.php montre variables vides après vérification
- Session ID change à chaque page

**Causes possibles:**
- Configuration PHP session incorrecte
- Cookies bloqués
- Problème de chemin/domaine des cookies
- Permissions du dossier session_save_path

**Solution:**
Vérifier la configuration PHP dans test_session.php (section "Configuration Session")

### Hypothèse 2: Session régénérée entre pages
**Symptômes:**
- test_session.php fonctionne
- Mais session vide sur page invoices

**Causes possibles:**
- Perfex régénère les sessions sur certaines routes
- Code qui appelle session_regenerate_id()
- Conflit avec authentification Perfex native

**Solution:**
Désactiver session regeneration ou intégrer avec système auth Perfex

### Hypothèse 3: Helper functions non chargées
**Symptômes:**
- test_auth.php montre "Fonction n'existe pas"

**Causes possibles:**
- Helper dietetic_helper.php non chargé
- Perfex a ses propres fonctions qui écrasent les nôtres

**Solution:**
Vérifier que helper est chargé dans constructor

### Hypothèse 4: Problème de timing
**Symptômes:**
- Login montre session définie
- Mais INDEX PAGE ne la voit pas

**Causes possibles:**
- Redirect trop rapide, session pas écrite
- Race condition

**Solution:**
Appeler $this->session->sess_write_close() avant redirect

## 🎯 Solutions Potentielles

### Solution 1: Utiliser l'authentification native Perfex

Au lieu d'utiliser nos propres variables de session, intégrer avec le système client de Perfex:

```php
// Au lieu de:
$this->session->set_userdata('client_logged_in', true);
$this->session->set_userdata('client_user_id', $client_id);

// Utiliser:
$this->load->library('clients_auth');
$this->clients_auth->login($contact_id);
```

### Solution 2: Forcer l'écriture de session

Avant chaque redirect, forcer l'écriture:

```php
$this->session->set_userdata([
    'client_logged_in' => true,
    'client_user_id' => $client_id
]);

// Forcer l'écriture
$CI->session->sess_write_close();
```

### Solution 3: Utiliser les cookies comme fallback

En plus de la session, stocker dans un cookie sécurisé:

```php
set_cookie([
    'name'   => 'dietetic_auth_token',
    'value'  => hash('sha256', $client_id . '|' . time()),
    'expire' => 3600,
    'secure' => TRUE,
    'httponly' => TRUE
]);
```

### Solution 4: Modifier configuration session Perfex

Vérifier `application/config/config.php`:

```php
$config['sess_driver'] = 'database'; // ou 'files'
$config['sess_cookie_name'] = 'ci_session';
$config['sess_expiration'] = 7200;
$config['sess_save_path'] = NULL; // ou chemin spécifique
$config['sess_match_ip'] = FALSE;
$config['sess_time_to_update'] = 300;
$config['sess_regenerate_destroy'] = FALSE;
```

## 📞 Prochaines Étapes

1. Exécuter les tests ci-dessus
2. Collecter les résultats et logs
3. Identifier quelle hypothèse correspond aux symptômes
4. Appliquer la solution correspondante

## 📚 Fichiers Modifiés

- `modules/dietetic/controllers/Portal.php`:
  - Ajout de logs dans `index()`, `invoices()`, `invoice()`
  - Ajout de logs avant chaque redirect après login

- `modules/dietetic/test_session.php`:
  - Nouveau script de test de persistance de session

- `modules/dietetic/test_auth.php`:
  - Nouveau script de test d'authentification

- `modules/dietetic/helpers/dietetic_helper.php`:
  - Fonctions `is_client_logged_in()` et `get_client_user_id()` déjà présentes

---

**Date de création:** 2025-12-11
**Auteur:** Claude (Assistant IA)
**Version:** 1.0
