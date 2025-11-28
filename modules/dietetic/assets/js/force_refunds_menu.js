/**
 * Force l'ajout du menu Remboursements dans la sidebar
 * Ce script contourne le système de hooks en ajoutant directement le menu dans le DOM
 */
(function() {
    'use strict';

    // Attendre que le DOM soit chargé
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', addRefundsMenu);
    } else {
        addRefundsMenu();
    }

    function addRefundsMenu() {
        console.log('[Force Refunds Menu] Script lancé');

        // Chercher le menu Dietetic dans la sidebar
        var dieteticMenu = document.querySelector('li.menu-item-dietetic') ||
                          document.querySelector('li[data-id="dietetic"]') ||
                          document.querySelector('a[href*="dietetic"]');

        if (!dieteticMenu) {
            console.error('[Force Refunds Menu] Menu Dietetic non trouvé');
            return;
        }

        console.log('[Force Refunds Menu] Menu Dietetic trouvé', dieteticMenu);

        // Chercher le sous-menu ul
        var submenu = dieteticMenu.querySelector('ul.nav-second-level') ||
                     dieteticMenu.querySelector('ul.submenu') ||
                     dieteticMenu.nextElementSibling;

        if (!submenu || submenu.tagName !== 'UL') {
            console.error('[Force Refunds Menu] Sous-menu non trouvé');
            return;
        }

        console.log('[Force Refunds Menu] Sous-menu trouvé', submenu);

        // Vérifier si le menu Remboursements existe déjà
        var existingRefunds = submenu.querySelector('a[href*="dietetic/refunds"]');
        if (existingRefunds) {
            console.log('[Force Refunds Menu] Menu Remboursements existe déjà');
            return;
        }

        // Créer le menu Remboursements
        var li = document.createElement('li');
        li.className = 'menu-item-dietetic-refunds';

        var a = document.createElement('a');
        a.href = admin_url + 'dietetic/refunds';

        var icon = document.createElement('i');
        icon.className = 'fa fa-undo menu-icon';

        var span = document.createElement('span');
        span.textContent = 'Remboursements';

        a.appendChild(icon);
        a.appendChild(document.createTextNode(' '));
        a.appendChild(span);
        li.appendChild(a);

        // Trouver la position après "Paiements Récurrents" (Recurring Payments)
        var recurringItem = submenu.querySelector('a[href*="dietetic/recurring_payments"]');
        if (recurringItem && recurringItem.parentElement) {
            // Insérer après Recurring Payments
            var recurringLi = recurringItem.parentElement;
            if (recurringLi.nextElementSibling) {
                submenu.insertBefore(li, recurringLi.nextElementSibling);
            } else {
                submenu.appendChild(li);
            }
            console.log('[Force Refunds Menu] ✅ Menu Remboursements ajouté après Paiements Récurrents');
        } else {
            // Sinon, chercher après Revenue Dashboard
            var revenueItem = submenu.querySelector('a[href*="dietetic/revenue_dashboard"]');
            if (revenueItem && revenueItem.parentElement) {
                var revenueLi = revenueItem.parentElement;
                if (revenueLi.nextElementSibling) {
                    submenu.insertBefore(li, revenueLi.nextElementSibling);
                } else {
                    submenu.appendChild(li);
                }
                console.log('[Force Refunds Menu] ✅ Menu Remboursements ajouté après Dashboard Revenus');
            } else {
                // Sinon, ajouter à la fin
                submenu.appendChild(li);
                console.log('[Force Refunds Menu] ✅ Menu Remboursements ajouté à la fin');
            }
        }

        // Ajouter un style pour le mettre en évidence (temporaire)
        li.style.backgroundColor = '#fce4ec';
        li.style.borderLeft = '4px solid #d81b60';
        setTimeout(function() {
            li.style.transition = 'all 1s ease';
            li.style.backgroundColor = '';
            li.style.borderLeft = '';
        }, 3000);
    }
})();
