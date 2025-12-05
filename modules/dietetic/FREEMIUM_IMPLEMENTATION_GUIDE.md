# 🎯 Guide d'Implémentation du Modèle Freemium Dietzone

## Vue d'ensemble

Ce guide explique comment utiliser le système freemium de Dietzone qui distingue les utilisateurs **gratuits** (programme expiré) et **premium** (programme actif).

---

## 📚 Fonctions Disponibles

### 1. `is_premium_user($patient_id = null)`

Vérifie si un patient a un programme actif (premium) ou non (gratuit).

**Paramètres :**
- `$patient_id` (int, optional) : ID du patient. Si null, utilise le patient connecté.

**Retour :**
- `true` : Utilisateur premium (a un programme actif)
- `false` : Utilisateur gratuit (aucun programme actif ou expiré)

**Exemples d'utilisation :**

```php
// Dans un contrôleur
public function add_meal()
{
    if (!is_premium_user()) {
        set_alert('warning', 'Fonctionnalité réservée aux abonnés Premium');
        redirect('dietetic/portal/upgrade');
    }

    // Code pour ajouter un repas...
}

// Dans une vue
<?php if (is_premium_user()): ?>
    <button class="btn btn-primary">Ajouter un Repas</button>
<?php else: ?>
    <button class="btn btn-secondary" disabled>
        <i class="fa fa-lock"></i> Premium Requis
    </button>
<?php endif; ?>

// Vérifier pour un patient spécifique
$patient_id = 123;
if (is_premium_user($patient_id)) {
    echo "Patient #123 est Premium";
}
```

---

### 2. `verify_premium_feature($feature_name, $redirect = true, $redirect_url = null)`

Middleware pour protéger une fonctionnalité premium. Bloque l'accès et redirige automatiquement.

**Paramètres :**
- `$feature_name` (string) : Nom de la fonctionnalité pour le message d'erreur
- `$redirect` (bool) : Si true, redirige vers upgrade. Si false, retourne boolean
- `$redirect_url` (string, optional) : URL de redirection personnalisée

**Retour :**
- `true` : Accès autorisé (premium)
- `false` : Accès refusé (gratuit)
- Redirect : Si `$redirect = true` et utilisateur gratuit

**Exemples d'utilisation :**

```php
// Dans un contrôleur - Protection stricte avec redirection
public function send_message()
{
    verify_premium_feature('Messagerie'); // Redirige automatiquement si gratuit

    // Ce code n'est exécuté que pour les utilisateurs premium
    $this->load->model('dietetic/messages_model');
    // ...
}

// Dans un contrôleur - Vérification conditionnelle sans redirection
public function dashboard()
{
    $data['can_message'] = verify_premium_feature('Messagerie', false);

    if ($data['can_message']) {
        $data['messages'] = $this->messages_model->get_recent();
    }

    $this->load->view('dashboard', $data);
}

// Redirection personnalisée
public function book_appointment()
{
    verify_premium_feature(
        'Prise de rendez-vous',
        true,
        site_url('dietetic/portal/pricing')
    );

    // Code de prise de RDV...
}
```

---

### 3. `get_premium_badge($patient_id = null, $size = 'medium', $show_free = true)`

Génère un badge HTML stylisé "Premium" ou "Gratuit".

**Paramètres :**
- `$patient_id` (int, optional) : ID du patient (null = patient connecté)
- `$size` (string) : Taille du badge : 'small', 'medium', 'large'
- `$show_free` (bool) : Afficher badge "Gratuit" ou rien pour utilisateurs gratuits

**Retour :**
- HTML badge stylisé

**Exemples d'utilisation :**

```php
<!-- Dans une vue - Badge par défaut -->
<div class="user-profile">
    <h3>Mon Profil <?php echo get_premium_badge(); ?></h3>
</div>

<!-- Badge petit -->
<span class="user-name">
    Jean Dupont <?php echo get_premium_badge(null, 'small'); ?>
</span>

<!-- Badge large dans header -->
<div class="portal-header">
    <h1>Bienvenue <?php echo get_premium_badge(null, 'large'); ?></h1>
</div>

<!-- Afficher seulement pour premium (cacher pour gratuit) -->
<?php if (is_premium_user()): ?>
    <?php echo get_premium_badge(null, 'medium', false); ?>
<?php endif; ?>

<!-- Pour un patient spécifique -->
<?php
$patient_id = 456;
echo get_premium_badge($patient_id, 'medium');
?>
```

**Rendu visuel :**

- **Premium** : Badge doré avec icône couronne 👑
- **Gratuit** : Badge gris avec icône utilisateur

---

### 4. `get_premium_lock($feature_name, $upgrade_url = null, $inline = false)`

Génère un composant de verrouillage pour fonctionnalités premium.

**Paramètres :**
- `$feature_name` (string) : Nom de la fonctionnalité verrouillée
- `$upgrade_url` (string, optional) : URL vers page upgrade
- `$inline` (bool) : Si true, affichage inline. Si false, overlay complet

**Retour :**
- HTML composant lock

**Exemples d'utilisation :**

```php
<!-- Overlay pour section complète -->
<?php if (!is_premium_user()): ?>
    <?php echo get_premium_lock('La messagerie diététicien'); ?>
<?php else: ?>
    <div class="messages-section">
        <!-- Contenu messagerie -->
    </div>
<?php endif; ?>

<!-- Lock inline pour bouton -->
<?php if (!is_premium_user()): ?>
    <?php echo get_premium_lock('Les rappels automatiques', null, true); ?>
<?php else: ?>
    <button class="btn btn-primary">
        <i class="fa fa-bell"></i> Activer les Rappels
    </button>
<?php endif; ?>

<!-- URL personnalisée -->
<?php
echo get_premium_lock(
    'Les plans de repas personnalisés',
    site_url('dietetic/portal/offers'),
    false
);
?>
```

---

### 5. `get_premium_features_comparison()`

Retourne un tableau comparatif des fonctionnalités Free vs Premium.

**Retour :**
- Array associatif avec toutes les fonctionnalités

**Exemple d'utilisation :**

```php
// Dans un contrôleur
public function pricing()
{
    $data['features'] = get_premium_features_comparison();
    $this->load->view('portal/pricing', $data);
}
```

```php
<!-- Dans une vue - Tableau de comparaison -->
<table class="table">
    <thead>
        <tr>
            <th>Fonctionnalité</th>
            <th>Gratuit</th>
            <th>Premium</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($features as $key => $feature): ?>
        <tr>
            <td>
                <i class="fa <?php echo $feature['icon']; ?>"></i>
                <strong><?php echo $feature['name']; ?></strong>
                <br>
                <small class="text-muted"><?php echo $feature['description']; ?></small>
            </td>
            <td>
                <?php if ($feature['free'] === true): ?>
                    <i class="fa fa-check text-success"></i> Inclus
                <?php elseif ($feature['free'] === false): ?>
                    <i class="fa fa-times text-danger"></i>
                <?php else: ?>
                    <?php echo $feature['free']; ?>
                <?php endif; ?>
            </td>
            <td>
                <?php if ($feature['premium'] === true): ?>
                    <i class="fa fa-check text-success"></i> Inclus
                <?php elseif ($feature['premium'] === false): ?>
                    <i class="fa fa-times text-danger"></i>
                <?php else: ?>
                    <?php echo $feature['premium']; ?>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
```

---

## 🎨 Cas d'Usage Complets

### Exemple 1 : Protéger l'ajout de pesées (limite 1/semaine pour gratuit)

```php
// Dans modules/dietetic/controllers/Portal.php

public function add_weight()
{
    if (!is_premium_user()) {
        // Vérifier si le patient a déjà ajouté une pesée cette semaine
        $last_weight = $this->get_last_weight_date();

        if ($last_weight && $this->is_within_week($last_weight)) {
            set_alert('warning', 'Limite gratuite : 1 pesée par semaine. Passez à Premium pour un suivi illimité.');
            redirect('dietetic/portal/upgrade');
        }
    }

    // Code normal d'ajout de pesée...
}
```

### Exemple 2 : Dashboard avec sections premium/gratuit

```php
// Dans modules/dietetic/controllers/Portal.php

public function index()
{
    $data['is_premium'] = is_premium_user();
    $data['can_message'] = verify_premium_feature('Messagerie', false);
    $data['can_book'] = verify_premium_feature('Rendez-vous', false);

    if ($data['can_message']) {
        $data['unread_messages'] = $this->messages_model->count_unread();
    }

    $this->load->view('portal/dashboard', $data);
}
```

```php
<!-- Dans modules/dietetic/views/portal/dashboard.php -->

<div class="dashboard-header">
    <h1>Tableau de Bord <?php echo get_premium_badge(null, 'large'); ?></h1>
</div>

<!-- Section Messagerie -->
<div class="card">
    <div class="card-header">
        <h3>Messagerie Diététicien</h3>
    </div>
    <div class="card-body">
        <?php if ($can_message): ?>
            <p>Vous avez <?php echo $unread_messages; ?> messages non lus.</p>
            <a href="<?php echo site_url('dietetic/portal/messages'); ?>" class="btn btn-primary">
                Voir les messages
            </a>
        <?php else: ?>
            <?php echo get_premium_lock('La messagerie', null, false); ?>
        <?php endif; ?>
    </div>
</div>

<!-- Section Rendez-vous -->
<div class="card">
    <div class="card-header">
        <h3>Mes Rendez-vous</h3>
    </div>
    <div class="card-body">
        <?php if ($can_book): ?>
            <a href="<?php echo site_url('dietetic/portal/book'); ?>" class="btn btn-primary">
                Prendre Rendez-vous
            </a>
        <?php else: ?>
            <?php echo get_premium_lock('La prise de rendez-vous', null, true); ?>
        <?php endif; ?>
    </div>
</div>
```

### Exemple 3 : Bannière upgrade pour utilisateurs gratuits

```php
<!-- Dans modules/dietetic/views/portal/includes/portal_header.php -->

<?php if (!is_premium_user()): ?>
<div class="upgrade-banner" style="
    background: linear-gradient(135deg, #01807B 0%, #01655f 100%);
    color: white;
    padding: 15px 20px;
    text-align: center;
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 4px 12px rgba(1, 128, 123, 0.3);
">
    <div style="display: flex; align-items: center; justify-content: center; gap: 20px; flex-wrap: wrap;">
        <div>
            <i class="fa fa-crown" style="font-size: 32px;"></i>
        </div>
        <div style="flex: 1; min-width: 200px; text-align: left;">
            <h4 style="margin: 0 0 5px 0;">Version Gratuite</h4>
            <p style="margin: 0; opacity: 0.9; font-size: 14px;">
                Passez à Premium pour débloquer la messagerie, les rappels automatiques et bien plus !
            </p>
        </div>
        <div>
            <a href="<?php echo site_url('dietetic/portal/upgrade'); ?>"
               class="btn"
               style="
                   background: white;
                   color: #01807B;
                   padding: 10px 24px;
                   border-radius: 25px;
                   font-weight: 600;
                   text-decoration: none;
                   box-shadow: 0 2px 8px rgba(0,0,0,0.2);
               ">
                Découvrir Premium
            </a>
        </div>
    </div>
</div>
<?php endif; ?>
```

---

## 🚀 Déploiement

### Étapes pour activer le freemium :

1. ✅ **Fonctions helper créées** (`dietetic_helper.php`)
2. ⏳ **Créer page `/portal/upgrade`** (à faire)
3. ⏳ **Protéger fonctionnalités premium** (à faire)
4. ⏳ **Ajouter bannière dans portal_header** (à faire)
5. ⏳ **Tester avec programmes actifs/expirés** (à faire)

---

## 📊 Logique du Modèle Freemium

### Critères Premium :
Un patient est **Premium** si et seulement si :
- Il a au moins 1 programme avec `status = 'active'`
- ET (`end_date IS NULL` OU `end_date >= aujourd'hui`)

### Fonctionnalités par Tier :

| Fonctionnalité | Gratuit | Premium |
|----------------|---------|---------|
| Historique complet | ✅ Oui | ✅ Oui |
| Profil & paramètres | ✅ Oui | ✅ Oui |
| Pesées | ⚠️ 1/semaine | ✅ Illimité |
| Journal alimentaire | ⚠️ Basique | ✅ Avec analyse |
| Photos progression | ⚠️ Lecture seule | ✅ Ajout illimité |
| Messagerie diététicien | ❌ Non | ✅ Oui |
| Plans de repas | ❌ Non | ✅ Oui |
| Notifications | ❌ Non | ✅ Oui |
| Rendez-vous | ❌ Non | ✅ Oui |
| Recettes | ⚠️ 5 max | ✅ Toutes |

---

## 🛡️ Sécurité

- Les fonctions vérifient TOUJOURS le statut du programme en base de données
- Logs automatiques des tentatives d'accès bloquées
- Pas de bypass côté client (vérification serveur)
- Messages d'erreur clairs et professionnels

---

## 📝 Notes Importantes

1. **Toujours vérifier côté serveur** : Ne jamais se fier uniquement aux vérifications JavaScript/CSS
2. **UX positive** : Messages encourageants plutôt que restrictifs
3. **Upgrade facile** : Toujours proposer un lien vers la page upgrade
4. **Respect des données** : Utilisateurs gratuits gardent accès à leur historique

---

## 🎯 Prochaines Étapes

Pour compléter l'implémentation freemium :

1. Créer la page `/portal/upgrade` avec tableau de comparaison
2. Protéger les méthodes premium dans `Portal.php`
3. Ajouter la bannière upgrade dans `portal_header.php`
4. Implémenter la limitation des pesées (1/semaine pour gratuit)
5. Tester avec différents scénarios (programme actif, expiré, multiple)

---

**Documentation créée le :** <?php echo date('d/m/Y'); ?>
**Version :** 1.0
**Auteur :** Claude AI pour Dietzone
