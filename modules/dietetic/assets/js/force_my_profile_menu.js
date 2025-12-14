/**
 * Force l'ajout du menu "Mon Profil" dans la sidebar
 * Ce script contourne le système de hooks en ajoutant directement le menu dans le DOM
 */
(function() {
    'use strict';

    // Attendre que le DOM soit chargé
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', addMyProfileMenu);
    } else {
        addMyProfileMenu();
    }

    function addMyProfileMenu() {
        console.log('[Force My Profile Menu] Initialisation...');

        // Chercher le menu Dietetic dans la sidebar
        var dieteticMenu = document.querySelector('li.menu-item-dietetic') ||
                          document.querySelector('li[data-id="dietetic"]') ||
                          document.querySelector('a[href*="dietetic"]');

        if (!dieteticMenu) {
            console.error('[Force My Profile Menu] Menu Dietetic non trouvé');
            return;
        }

        // Chercher le sous-menu ul
        var submenu = dieteticMenu.querySelector('ul.nav-second-level') ||
                     dieteticMenu.querySelector('ul.submenu') ||
                     dieteticMenu.nextElementSibling;

        if (!submenu || submenu.tagName !== 'UL') {
            console.error('[Force My Profile Menu] Sous-menu non trouvé');
            return;
        }

        // Vérifier si le menu Mon Profil existe déjà
        var existingProfile = submenu.querySelector('a[href*="dietetic/my_profile"]');
        if (existingProfile) {
            console.log('[Force My Profile Menu] Menu déjà présent');
            return;
        }

        // Créer le menu Mon Profil
        var li = document.createElement('li');
        li.className = 'menu-item-dietetic-my-profile';

        var a = document.createElement('a');
        a.href = admin_url + 'dietetic/my_profile';

        var icon = document.createElement('i');
        icon.className = 'fa fa-user-circle menu-icon';

        var span = document.createElement('span');
        span.textContent = 'Mon Profil';

        a.appendChild(icon);
        a.appendChild(document.createTextNode(' '));
        a.appendChild(span);
        li.appendChild(a);

        // Trouver la position après "Activités Sportives"
        var activitiesItem = submenu.querySelector('a[href*="dietetic/activities"]');
        if (activitiesItem && activitiesItem.parentElement) {
            // Insérer après Activités Sportives
            var activitiesLi = activitiesItem.parentElement;
            if (activitiesLi.nextElementSibling) {
                submenu.insertBefore(li, activitiesLi.nextElementSibling);
                console.log('[Force My Profile Menu] Menu inséré après Activités Sportives');
            } else {
                submenu.appendChild(li);
                console.log('[Force My Profile Menu] Menu ajouté à la fin');
            }
        } else {
            // Sinon, chercher après Food Surveys
            var foodSurveysItem = submenu.querySelector('a[href*="dietetic/food_surveys"]');
            if (foodSurveysItem && foodSurveysItem.parentElement) {
                var foodSurveysLi = foodSurveysItem.parentElement;
                if (foodSurveysLi.nextElementSibling) {
                    submenu.insertBefore(li, foodSurveysLi.nextElementSibling);
                    console.log('[Force My Profile Menu] Menu inséré après Food Surveys');
                } else {
                    submenu.appendChild(li);
                    console.log('[Force My Profile Menu] Menu ajouté à la fin');
                }
            } else {
                // Sinon, ajouter à la fin
                submenu.appendChild(li);
                console.log('[Force My Profile Menu] Menu ajouté à la fin (fallback)');
            }
        }

        // Ajouter un style pour le mettre en évidence (temporaire)
        li.style.backgroundColor = '#e3f2fd';
        li.style.borderLeft = '4px solid #01807B';
        setTimeout(function() {
            li.style.transition = 'all 1s ease';
            li.style.backgroundColor = '';
            li.style.borderLeft = '';
        }, 3000);

        console.log('[Force My Profile Menu] Menu ajouté avec succès ✓');
    }
})();
