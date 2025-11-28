/**
 * Force l'ajout des menus Billing System dans la sidebar
 * Ce script contourne le système de hooks en ajoutant directement les menus dans le DOM
 */
(function() {
    'use strict';

    // Attendre que le DOM soit chargé
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', addBillingMenus);
    } else {
        addBillingMenus();
    }

    function addBillingMenus() {
        console.log('[Force Billing Menu] Script lancé');

        // Chercher le menu Dietetic dans la sidebar
        var dieteticMenu = document.querySelector('li.menu-item-dietetic') ||
                          document.querySelector('li[data-id="dietetic"]') ||
                          document.querySelector('a[href*="dietetic"]');

        if (!dieteticMenu) {
            console.error('[Force Billing Menu] Menu Dietetic non trouvé');
            return;
        }

        console.log('[Force Billing Menu] Menu Dietetic trouvé', dieteticMenu);

        // Chercher le sous-menu ul
        var submenu = dieteticMenu.querySelector('ul.nav-second-level') ||
                     dieteticMenu.querySelector('ul.submenu') ||
                     dieteticMenu.nextElementSibling;

        if (!submenu || submenu.tagName !== 'UL') {
            console.error('[Force Billing Menu] Sous-menu non trouvé');
            return;
        }

        console.log('[Force Billing Menu] Sous-menu trouvé', submenu);

        // Vérifier si les menus existent déjà
        if (submenu.querySelector('a[href*="dietetic/service_plans"]')) {
            console.log('[Force Billing Menu] Menus de billing existent déjà');
            return;
        }

        // Trouver le point d'insertion (après Activités Sportives)
        var activitiesItem = submenu.querySelector('a[href*="dietetic/activities"]');
        var insertPoint = null;

        if (activitiesItem && activitiesItem.parentElement) {
            insertPoint = activitiesItem.parentElement.nextElementSibling;
        }

        // Créer les 4 menus de billing
        var menus = [
            {
                slug: 'dietetic-service-plans',
                name: 'Plans de Service',
                icon: 'fa-cube',
                href: admin_url + 'dietetic/service_plans',
                adminOnly: true
            },
            {
                slug: 'dietetic-subscriptions',
                name: 'Abonnements',
                icon: 'fa-refresh',
                href: admin_url + 'dietetic/subscriptions',
                adminOnly: false
            },
            {
                slug: 'dietetic-invoices',
                name: 'Factures',
                icon: 'fa-file-text-o',
                href: admin_url + 'dietetic/invoices',
                adminOnly: false
            },
            {
                slug: 'dietetic-commissions',
                name: 'Commissions',
                icon: 'fa-percent',
                href: admin_url + 'dietetic/commissions/settings',
                adminOnly: true
            }
        ];

        // Vérifier si l'utilisateur est admin (pour les menus admin-only)
        var isAdmin = typeof is_admin !== 'undefined' ? is_admin : true; // Par défaut true pour afficher

        menus.forEach(function(menu) {
            // Skip admin-only menus if user is not admin
            if (menu.adminOnly && !isAdmin) {
                return;
            }

            var li = document.createElement('li');
            li.className = 'menu-item-' + menu.slug;

            var a = document.createElement('a');
            a.href = menu.href;

            var icon = document.createElement('i');
            icon.className = 'fa ' + menu.icon + ' menu-icon';

            var span = document.createElement('span');
            span.textContent = menu.name;

            a.appendChild(icon);
            a.appendChild(document.createTextNode(' '));
            a.appendChild(span);
            li.appendChild(a);

            // Insérer à la bonne position
            if (insertPoint) {
                submenu.insertBefore(li, insertPoint);
            } else {
                submenu.appendChild(li);
            }

            console.log('[Force Billing Menu] ✅ Menu "' + menu.name + '" ajouté');

            // Ajouter un style pour le mettre en évidence (temporaire)
            li.style.backgroundColor = '#e3f2fd';
            li.style.borderLeft = '4px solid #2196F3';
            setTimeout(function() {
                li.style.transition = 'all 1s ease';
                li.style.backgroundColor = '';
                li.style.borderLeft = '';
            }, 3000);
        });

        console.log('[Force Billing Menu] ✅ Tous les menus de billing ont été ajoutés');
    }
})();
