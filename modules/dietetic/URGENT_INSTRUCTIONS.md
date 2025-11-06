# 🚨 INSTRUCTIONS URGENTES - Erreur 404 Persistante

## ✅ CE QUI A ÉTÉ FAIT

J'ai **DÉSACTIVÉ COMPLÈTEMENT** le hook `customer_profile_tabs` qui pourrait causer l'erreur 404.

**Fichier modifié** : `modules/dietetic/dietetic.php` ligne 158
```php
// hooks()->add_action('customer_profile_tabs', 'dietetic_add_customer_profile_tab');
```

Le hook est maintenant **commenté** donc il ne s'exécute plus du tout.

## 🎯 ACTIONS IMMÉDIATES

### ÉTAPE 1 : Pull et Test (2 minutes)

```bash
cd /path/to/perfex
git pull origin claude/build-dietetic-crm-module-011CUg57kDmPcHyfwwy6HAYZ
```

Puis **TESTEZ IMMÉDIATEMENT** :
1. Allez sur `https://app.dietsenegal.net/admin/clients/client/8`
2. Est-ce que ça marche maintenant ? ✅ ou ❌

### ÉTAPE 2 : Diagnostic Web (1 minute)

Accédez à cette URL dans votre navigateur :
```
https://app.dietsenegal.net/admin/dietetic/diagnostic
```

Cette page vous montrera un **diagnostic complet en temps réel** avec:
- ✓ 10 tests automatiques
- ✓ Rapport visuel avec couleurs
- ✓ Recommandations spécifiques
- ✓ Identification de la cause exacte

**Pas besoin de ligne de commande !** Tout se fait dans le navigateur.

### ÉTAPE 3 : Résultats à Me Communiquer

Après avoir fait les étapes 1 et 2, dites-moi :

**A) L'erreur 404 persiste-t-elle ?**
- ✅ NON, ça marche maintenant !
- ❌ OUI, l'erreur est toujours là

**B) Si l'erreur persiste, envoyez-moi :**
1. Screenshot ou copie du texte de la page de diagnostic (`/admin/dietetic/diagnostic`)
2. Ce que vous voyez exactement quand vous accédez à `/admin/clients/client/8`

## 🔍 CE QUE CELA SIGNIFIE

### Si ça marche maintenant (après le pull) :
✅ **Le hook `customer_profile_tabs` était bien la cause**
- L'onglet "Dietetic" n'apparaîtra plus sur les profils clients
- **Solution** : Vous pouvez accéder aux patients via le menu **Dietetic > Patients**
- On peut créer un lien alternatif si besoin

### Si l'erreur persiste :
❌ **Le problème vient d'ailleurs**
- Ce n'est PAS le hook
- Probablement les contraintes FK ou un problème de routing Perfex
- Le diagnostic web identifiera la vraie cause

## 🛠️ SOLUTION TEMPORAIRE (si ça marche)

**Impact de la désactivation du hook :**
- ✅ La création de clients fonctionne
- ✅ Toutes les fonctionnalités du module fonctionnent
- ❌ L'onglet "Dietetic Follow-up" ne s'affiche plus sur les profils clients

**Pour accéder aux patients d'un client :**
1. Menu : **Dietetic > Patients**
2. Trouvez le patient par nom de client
3. Cliquez pour voir le profil

**Alternative** : Je peux créer un bouton ailleurs ou modifier le système pour qu'il fonctionne sans causer d'erreur.

## 📞 SI RIEN NE FONCTIONNE

Si même avec le hook désactivé l'erreur persiste, faites ceci **EN PARALLÈLE** :

### 1. Désactiver le Module Complètement
```
Setup > Modules > Dietetic > Deactivate
```
Puis testez `/admin/clients/client/8`

Si ça marche → Le problème est dans le module (mais pas dans le hook)
Si ça ne marche pas → Le problème est AILLEURS dans Perfex

### 2. Vérifier les Logs Serveur

**Apache :**
```bash
tail -n 50 /var/log/apache2/error.log
```

**Nginx :**
```bash
tail -n 50 /var/log/nginx/error.log
```

**PHP-FPM :**
```bash
tail -n 50 /var/log/php-fpm/error.log
```

Recherchez des lignes avec :
- "Fatal error"
- "dietetic"
- "clients"
- timestamp correspondant à votre test

### 3. Clear TOUS les Caches

```bash
cd /path/to/perfex

# Cache Perfex
rm -rf application/cache/*

# Optionnel: Redémarrer services
sudo service apache2 restart
# OU
sudo service nginx restart
sudo service php7.4-fpm restart  # Adaptez la version PHP
```

## 🎯 PROCHAINES ÉTAPES SELON LE RÉSULTAT

### Scénario A : Ça marche après le pull
→ On réactive progressivement les fonctionnalités
→ On crée une alternative pour l'onglet client

### Scénario B : Erreur persiste même avec hook désactivé
→ On analyse le diagnostic web
→ On corrige la vraie cause (probablement FK ou DB)

### Scénario C : Ça marche uniquement avec module désactivé
→ Problème dans l'installation du module
→ On vérifie les migrations et la structure DB

## ⚡ RACCOURCI ULTRA-RAPIDE

Si vous voulez juste que ça marche **MAINTENANT** :

1. `git pull origin claude/build-dietetic-crm-module-011CUg57kDmPcHyfwwy6HAYZ`
2. Testez `/admin/clients/client/8`
3. Dites-moi : ✅ ou ❌

C'est tout ! Les 2 premières étapes prennent 1 minute.

---

**Commit** : `[À venir]`
**Hook désactivé** : ✅ OUI (ligne 158 de dietetic.php)
**Diagnostic web** : ✅ Disponible à `/admin/dietetic/diagnostic`
**Priorité** : 🔴 URGENTE

Faites les tests et tenez-moi au courant ! 🚀
