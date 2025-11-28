/**
 * Force l'ajout du menu Dashboard Revenus dans la sidebar
 * Similaire aux menus Billing, Activities et Recipes
 */
(function() {
    'use strict';

    // Attendre que le DOM et jQuery soient prêts
    $(document).ready(function() {
        // Vérifier si le menu existe déjà
        if ($('#side-menu a[href*="revenue_dashboard"]').length > 0) {
            console.log('Menu Dashboard Revenus déjà présent');
            return;
        }

        // Trouver le parent menu "Diététique"
        var dieteticMenu = $('#side-menu li.menu-item-dietetic');

        if (dieteticMenu.length === 0) {
            console.log('Menu Diététique non trouvé');
            return;
        }

        // Trouver le sous-menu ul
        var submenu = dieteticMenu.find('ul.nav-second-level');

        if (submenu.length === 0) {
            console.log('Sous-menu Diététique non trouvé');
            return;
        }

        // Créer l'élément du menu Dashboard Revenus
        var revenueMenuItem = $('<li class="menu-item-dietetic-revenue-dashboard">' +
            '<a href="' + admin_url + 'dietetic/revenue_dashboard">' +
            '<i class="fa fa-line-chart menu-icon"></i> ' +
            '<span class="menu-text">Dashboard Revenus</span>' +
            '</a>' +
            '</li>');

        // Trouver où insérer (après Commissions si présent, sinon après Factures)
        var commissionsItem = submenu.find('li.menu-item-dietetic-commissions');
        var invoicesItem = submenu.find('li.menu-item-dietetic-invoices');

        if (commissionsItem.length > 0) {
            // Insérer après Commissions
            commissionsItem.after(revenueMenuItem);
            console.log('Menu Dashboard Revenus ajouté après Commissions');
        } else if (invoicesItem.length > 0) {
            // Insérer après Factures
            invoicesItem.after(revenueMenuItem);
            console.log('Menu Dashboard Revenus ajouté après Factures');
        } else {
            // Ajouter à la fin du sous-menu
            submenu.append(revenueMenuItem);
            console.log('Menu Dashboard Revenus ajouté à la fin');
        }

        // Activer le menu si on est sur la page
        if (window.location.href.indexOf('revenue_dashboard') !== -1) {
            revenueMenuItem.addClass('active');
            dieteticMenu.addClass('active');
        }
    });
})();
