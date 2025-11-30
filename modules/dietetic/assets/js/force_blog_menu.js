/**
 * Force l'ajout du menu Blog & Conseils dans la sidebar
 * Ce script contourne le système de hooks en ajoutant directement le menu dans le DOM
 * Structure IDENTIQUE à force_recipes_menu.js pour garantir le fonctionnement
 */
(function() {
    'use strict';

    // Attendre que le DOM soit chargé
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', addBlogMenu);
    } else {
        addBlogMenu();
    }

    function addBlogMenu() {
        console.log('[Force Blog Menu] Script lancé');

        // Chercher le menu Dietetic dans la sidebar
        var dieteticMenu = document.querySelector('li.menu-item-dietetic') ||
                          document.querySelector('li[data-id="dietetic"]') ||
                          document.querySelector('a[href*="dietetic"]');

        if (!dieteticMenu) {
            console.error('[Force Blog Menu] Menu Dietetic non trouvé');
            return;
        }

        console.log('[Force Blog Menu] Menu Dietetic trouvé', dieteticMenu);

        // Chercher le sous-menu ul
        var submenu = dieteticMenu.querySelector('ul.nav-second-level') ||
                     dieteticMenu.querySelector('ul.submenu') ||
                     dieteticMenu.nextElementSibling;

        if (!submenu || submenu.tagName !== 'UL') {
            console.error('[Force Blog Menu] Sous-menu non trouvé');
            return;
        }

        console.log('[Force Blog Menu] Sous-menu trouvé', submenu);

        // Vérifier si le menu Blog existe déjà
        var existingBlog = submenu.querySelector('a[href*="dietetic/blog"]');
        if (existingBlog) {
            console.log('[Force Blog Menu] Menu Blog existe déjà');
            return;
        }

        // Créer le menu Blog & Conseils - STRUCTURE IDENTIQUE à force_recipes_menu.js
        var li = document.createElement('li');
        li.className = 'menu-item-dietetic-blog';

        var a = document.createElement('a');
        a.href = admin_url + 'dietetic/blog';

        // ICÔNE - Utiliser une icône Font Awesome 4.x standard
        var icon = document.createElement('i');
        icon.className = 'fa fa-file-text-o menu-icon'; // Changé de newspaper-o à file-text-o

        var span = document.createElement('span');
        span.textContent = 'Blog & Conseils';

        // Assemblage - Même ordre que recipes
        a.appendChild(icon);
        a.appendChild(document.createTextNode(' '));
        a.appendChild(span);
        li.appendChild(a);

        // Trouver la position après "Recettes" (Bibliothèque de Recettes)
        var recipesItem = submenu.querySelector('a[href*="dietetic/recipes"]');
        if (recipesItem && recipesItem.parentElement) {
            // Insérer après Recettes
            var recipesLi = recipesItem.parentElement;
            if (recipesLi.nextElementSibling) {
                submenu.insertBefore(li, recipesLi.nextElementSibling);
            } else {
                submenu.appendChild(li);
            }
            console.log('[Force Blog Menu] ✅ Menu Blog ajouté après Recettes');
        } else {
            // Sinon, chercher après Foods
            var foodsItem = submenu.querySelector('a[href*="dietetic/foods"]');
            if (foodsItem && foodsItem.parentElement) {
                var foodsLi = foodsItem.parentElement;
                if (foodsLi.nextElementSibling) {
                    submenu.insertBefore(li, foodsLi.nextElementSibling);
                } else {
                    submenu.appendChild(li);
                }
                console.log('[Force Blog Menu] ✅ Menu Blog ajouté après Foods');
            } else {
                // Sinon, ajouter à la fin
                submenu.appendChild(li);
                console.log('[Force Blog Menu] ✅ Menu Blog ajouté à la fin');
            }
        }

        // Ajouter un style pour le mettre en évidence (temporaire)
        li.style.backgroundColor = '#d1ecf1';
        li.style.borderLeft = '4px solid #17a2b8';
        setTimeout(function() {
            li.style.transition = 'all 1s ease';
            li.style.backgroundColor = '';
            li.style.borderLeft = '';
        }, 3000);
    }
})();
