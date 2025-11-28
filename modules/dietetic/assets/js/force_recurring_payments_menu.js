/**
 * Force l'ajout du menu Paiements Récurrents dans la sidebar
 * Ce script contourne le système de hooks en ajoutant directement le menu dans le DOM
 */
(function() {
    'use strict';

    // Attendre que le DOM soit chargé
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', addRecurringPaymentsMenu);
    } else {
        addRecurringPaymentsMenu();
    }

    function addRecurringPaymentsMenu() {
        console.log('[Force Recurring Payments Menu] Script lancé');

        // Chercher le menu Dietetic dans la sidebar
        var dieteticMenu = document.querySelector('li.menu-item-dietetic') ||
                          document.querySelector('li[data-id="dietetic"]') ||
                          document.querySelector('a[href*="dietetic"]');

        if (!dieteticMenu) {
            console.error('[Force Recurring Payments Menu] Menu Dietetic non trouvé');
            return;
        }

        console.log('[Force Recurring Payments Menu] Menu Dietetic trouvé', dieteticMenu);

        // Chercher le sous-menu ul
        var submenu = dieteticMenu.querySelector('ul.nav-second-level') ||
                     dieteticMenu.querySelector('ul.submenu') ||
                     dieteticMenu.nextElementSibling;

        if (!submenu || submenu.tagName !== 'UL') {
            console.error('[Force Recurring Payments Menu] Sous-menu non trouvé');
            return;
        }

        console.log('[Force Recurring Payments Menu] Sous-menu trouvé', submenu);

        // Vérifier si le menu Paiements Récurrents existe déjà
        var existingRecurring = submenu.querySelector('a[href*="dietetic/recurring_payments"]');
        if (existingRecurring) {
            console.log('[Force Recurring Payments Menu] Menu Paiements Récurrents existe déjà');
            return;
        }

        // Créer le menu Paiements Récurrents
        var li = document.createElement('li');
        li.className = 'menu-item-dietetic-recurring-payments';

        var a = document.createElement('a');
        a.href = admin_url + 'dietetic/recurring_payments';

        var icon = document.createElement('i');
        icon.className = 'fa fa-refresh menu-icon';

        var span = document.createElement('span');
        span.textContent = 'Paiements Récurrents';

        a.appendChild(icon);
        a.appendChild(document.createTextNode(' '));
        a.appendChild(span);
        li.appendChild(a);

        // Trouver la position après "Dashboard Revenus" (Revenue Dashboard)
        var revenueItem = submenu.querySelector('a[href*="dietetic/revenue_dashboard"]');
        if (revenueItem && revenueItem.parentElement) {
            // Insérer après Revenue Dashboard
            var revenueLi = revenueItem.parentElement;
            if (revenueLi.nextElementSibling) {
                submenu.insertBefore(li, revenueLi.nextElementSibling);
            } else {
                submenu.appendChild(li);
            }
            console.log('[Force Recurring Payments Menu] ✅ Menu Paiements Récurrents ajouté après Dashboard Revenus');
        } else {
            // Sinon, chercher après Invoices
            var invoicesItem = submenu.querySelector('a[href*="dietetic/invoices"]');
            if (invoicesItem && invoicesItem.parentElement) {
                var invoicesLi = invoicesItem.parentElement;
                if (invoicesLi.nextElementSibling) {
                    submenu.insertBefore(li, invoicesLi.nextElementSibling);
                } else {
                    submenu.appendChild(li);
                }
                console.log('[Force Recurring Payments Menu] ✅ Menu Paiements Récurrents ajouté après Factures');
            } else {
                // Sinon, ajouter à la fin
                submenu.appendChild(li);
                console.log('[Force Recurring Payments Menu] ✅ Menu Paiements Récurrents ajouté à la fin');
            }
        }

        // Ajouter un style pour le mettre en évidence (temporaire)
        li.style.backgroundColor = '#e1f5fe';
        li.style.borderLeft = '4px solid #0288d1';
        setTimeout(function() {
            li.style.transition = 'all 1s ease';
            li.style.backgroundColor = '';
            li.style.borderLeft = '';
        }, 3000);
    }
})();
