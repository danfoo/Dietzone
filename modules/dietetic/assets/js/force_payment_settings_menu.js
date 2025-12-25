/**
 * Force l'ajout du menu "Passerelles de Paiement" dans la sidebar
 * Ce script contourne le système de hooks en ajoutant directement le menu dans le DOM
 */
(function() {
    'use strict';

    // Attendre que le DOM soit chargé
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', addPaymentSettingsMenu);
    } else {
        addPaymentSettingsMenu();
    }

    function addPaymentSettingsMenu() {
        console.log('[Force Payment Settings Menu] Initialisation...');

        // Chercher le menu Dietetic dans la sidebar
        var dieteticMenu = document.querySelector('li.menu-item-dietetic') ||
                          document.querySelector('li[data-id="dietetic"]') ||
                          document.querySelector('a[href*="dietetic"]');

        if (!dieteticMenu) {
            console.error('[Force Payment Settings Menu] Menu Dietetic non trouvé');
            return;
        }

        // Chercher le sous-menu ul
        var submenu = dieteticMenu.querySelector('ul.nav-second-level') ||
                     dieteticMenu.querySelector('ul.submenu') ||
                     dieteticMenu.nextElementSibling;

        if (!submenu || submenu.tagName !== 'UL') {
            console.error('[Force Payment Settings Menu] Sous-menu non trouvé');
            return;
        }

        // Vérifier si le menu Passerelles de Paiement existe déjà
        var existingPayment = submenu.querySelector('a[href*="dietetic/payment_settings"]');
        if (existingPayment) {
            console.log('[Force Payment Settings Menu] Menu déjà présent');
            return;
        }

        // Créer le menu Passerelles de Paiement
        var li = document.createElement('li');
        li.className = 'menu-item-dietetic-payment-settings';

        var a = document.createElement('a');
        a.href = admin_url + 'dietetic/payment_settings';

        var icon = document.createElement('i');
        icon.className = 'fa fa-credit-card menu-icon';

        var span = document.createElement('span');
        span.textContent = 'Passerelles de Paiement';

        a.appendChild(icon);
        a.appendChild(document.createTextNode(' '));
        a.appendChild(span);
        li.appendChild(a);

        // Ajouter à la fin du submenu (position 99 dans le code PHP)
        submenu.appendChild(li);
        console.log('[Force Payment Settings Menu] Menu ajouté à la fin');

        // Ajouter un style pour le mettre en évidence (temporaire)
        li.style.backgroundColor = '#fff3e0';
        li.style.borderLeft = '4px solid #ff9800';
        setTimeout(function() {
            li.style.transition = 'all 1s ease';
            li.style.backgroundColor = '';
            li.style.borderLeft = '';
        }, 3000);

        console.log('[Force Payment Settings Menu] Menu ajouté avec succès ✓');
    }
})();
