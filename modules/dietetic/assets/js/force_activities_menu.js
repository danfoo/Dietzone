/**
 * Force l'ajout du menu Activités Sportives dans la sidebar
 * Ce script contourne le système de hooks en ajoutant directement le menu dans le DOM
 */
(function() {
    'use strict';

    // Attendre que le DOM soit chargé
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', addActivitiesMenu);
    } else {
        addActivitiesMenu();
    }

    function addActivitiesMenu() {

        // Chercher le menu Dietetic dans la sidebar
        var dieteticMenu = document.querySelector('li.menu-item-dietetic') ||
                          document.querySelector('li[data-id="dietetic"]') ||
                          document.querySelector('a[href*="dietetic"]');

        if (!dieteticMenu) {
            console.error('[Force Activities Menu] Menu Dietetic non trouvé');
            return;
        }


        // Chercher le sous-menu ul
        var submenu = dieteticMenu.querySelector('ul.nav-second-level') ||
                     dieteticMenu.querySelector('ul.submenu') ||
                     dieteticMenu.nextElementSibling;

        if (!submenu || submenu.tagName !== 'UL') {
            console.error('[Force Activities Menu] Sous-menu non trouvé');
            return;
        }


        // Vérifier si le menu Activités existe déjà
        var existingActivities = submenu.querySelector('a[href*="dietetic/activities"]');
        if (existingActivities) {
            return;
        }

        // Créer le menu Activités Sportives
        var li = document.createElement('li');
        li.className = 'menu-item-dietetic-activities';

        var a = document.createElement('a');
        a.href = admin_url + 'dietetic/activities/manage';

        var icon = document.createElement('i');
        icon.className = 'fa fa-heartbeat menu-icon';

        var span = document.createElement('span');
        span.textContent = 'Activités Sportives';

        a.appendChild(icon);
        a.appendChild(document.createTextNode(' '));
        a.appendChild(span);
        li.appendChild(a);

        // Trouver la position après "Enquêtes Alimentaires" (Food Surveys)
        var foodSurveysItem = submenu.querySelector('a[href*="dietetic/food_surveys"]');
        if (foodSurveysItem && foodSurveysItem.parentElement) {
            // Insérer après Food Surveys
            var foodSurveysLi = foodSurveysItem.parentElement;
            if (foodSurveysLi.nextElementSibling) {
                submenu.insertBefore(li, foodSurveysLi.nextElementSibling);
            } else {
                submenu.appendChild(li);
            }
        } else {
            // Sinon, chercher après Programs
            var programsItem = submenu.querySelector('a[href*="dietetic/programs"]');
            if (programsItem && programsItem.parentElement) {
                var programsLi = programsItem.parentElement;
                if (programsLi.nextElementSibling) {
                    submenu.insertBefore(li, programsLi.nextElementSibling);
                } else {
                    submenu.appendChild(li);
                }
            } else {
                // Sinon, ajouter à la fin
                submenu.appendChild(li);
            }
        }

        // Ajouter un style pour le mettre en évidence (temporaire)
        li.style.backgroundColor = '#e8f5e9';
        li.style.borderLeft = '4px solid #01807B';
        setTimeout(function() {
            li.style.transition = 'all 1s ease';
            li.style.backgroundColor = '';
            li.style.borderLeft = '';
        }, 3000);
    }
})();
