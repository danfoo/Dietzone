/**
 * Force l'ajout du menu Recettes dans la sidebar
 * Ce script contourne le système de hooks en ajoutant directement le menu dans le DOM
 */
(function() {
    'use strict';

    // Attendre que le DOM soit chargé
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', addRecipesMenu);
    } else {
        addRecipesMenu();
    }

    function addRecipesMenu() {

        // Chercher le menu Dietetic dans la sidebar
        var dieteticMenu = document.querySelector('li.menu-item-dietetic') ||
                          document.querySelector('li[data-id="dietetic"]') ||
                          document.querySelector('a[href*="dietetic"]');

        if (!dieteticMenu) {
            console.error('[Force Recipes Menu] Menu Dietetic non trouvé');
            return;
        }


        // Chercher le sous-menu ul
        var submenu = dieteticMenu.querySelector('ul.nav-second-level') ||
                     dieteticMenu.querySelector('ul.submenu') ||
                     dieteticMenu.nextElementSibling;

        if (!submenu || submenu.tagName !== 'UL') {
            console.error('[Force Recipes Menu] Sous-menu non trouvé');
            return;
        }


        // Vérifier si le menu Recettes existe déjà
        var existingRecipes = submenu.querySelector('a[href*="dietetic/recipes"]');
        if (existingRecipes) {
            return;
        }

        // Créer le menu Recettes
        var li = document.createElement('li');
        li.className = 'menu-item-dietetic-recipes';

        var a = document.createElement('a');
        a.href = admin_url + 'dietetic/recipes';

        var icon = document.createElement('i');
        icon.className = 'fa fa-book menu-icon';

        var span = document.createElement('span');
        span.textContent = 'Bibliothèque de Recettes';

        a.appendChild(icon);
        a.appendChild(document.createTextNode(' '));
        a.appendChild(span);
        li.appendChild(a);

        // Trouver la position après "Foods" (Aliments)
        var foodsItem = submenu.querySelector('a[href*="dietetic/foods"]');
        if (foodsItem && foodsItem.parentElement) {
            // Insérer après Foods
            var foodsLi = foodsItem.parentElement;
            if (foodsLi.nextElementSibling) {
                submenu.insertBefore(li, foodsLi.nextElementSibling);
            } else {
                submenu.appendChild(li);
            }
        } else {
            // Sinon, ajouter à la fin
            submenu.appendChild(li);
        }

        // Ajouter un style pour le mettre en évidence (temporaire)
        li.style.backgroundColor = '#fff3cd';
        li.style.borderLeft = '4px solid #f39c12';
        setTimeout(function() {
            li.style.transition = 'all 1s ease';
            li.style.backgroundColor = '';
            li.style.borderLeft = '';
        }, 3000);
    }
})();
