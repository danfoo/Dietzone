/**
 * Force le menu Diététique à fonctionner
 * Ce script attend que jQuery soit disponible et force le comportement du menu
 */
(function() {
    'use strict';

    // Fonction pour attendre que jQuery soit chargé
    function waitForjQuery(callback) {
        if (typeof jQuery !== 'undefined') {
            callback(jQuery);
        } else {
            setTimeout(function() {
                waitForjQuery(callback);
            }, 100);
        }
    }

    // Attendre que jQuery soit disponible
    waitForjQuery(function($) {
        console.log("✅ jQuery chargé, initialisation du menu Diététique...");

        // Attendre que le DOM soit prêt
        $(document).ready(function() {

            // Attendre un peu plus pour être sûr que le sidebar est chargé
            setTimeout(function() {

                console.log("🔍 Recherche du menu Diététique...");

                // Trouver le menu Diététique par son texte
                var $dieteticMenu = null;
                $("#side-menu > li").each(function() {
                    var $li = $(this);
                    var $link = $li.find("> a");
                    var text = $link.text().trim();

                    if (text === "Diététique" || text.indexOf("Dietetic") !== -1) {
                        $dieteticMenu = $li;
                        console.log("✅ Menu Diététique trouvé!");
                        return false; // break
                    }
                });

                if (!$dieteticMenu || $dieteticMenu.length === 0) {
                    console.error("❌ Menu Diététique non trouvé dans le sidebar");
                    return;
                }

                var $link = $dieteticMenu.find("> a");
                var $submenu = $dieteticMenu.find("> ul");

                console.log("📋 Menu trouvé:", {
                    hasLink: $link.length > 0,
                    hasSubmenu: $submenu.length > 0,
                    submenuItems: $submenu.find("> li").length
                });

                if ($submenu.length === 0) {
                    console.error("❌ Aucun sous-menu trouvé");
                    return;
                }

                // S'assurer que le sous-menu a les bonnes classes
                if (!$submenu.hasClass("nav-second-level")) {
                    $submenu.addClass("nav nav-second-level collapse");
                    console.log("✅ Classes ajoutées au sous-menu");
                }

                // Ajouter un ID unique au sous-menu pour Bootstrap collapse
                var submenuId = "dietetic-submenu";
                $submenu.attr("id", submenuId);

                // Ajouter les attributs data pour Bootstrap collapse sur le lien
                $link.attr({
                    "data-toggle": "collapse",
                    "data-target": "#" + submenuId,
                    "aria-expanded": "false"
                });

                console.log("✅ Attributs Bootstrap collapse ajoutés");

                // Supprimer tout gestionnaire d'événement existant
                $link.off("click.dietetic");

                // Ajouter notre propre gestionnaire de clic
                $link.on("click.dietetic", function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    console.log("🖱️ Clic sur le menu Diététique");

                    var isOpen = $submenu.hasClass("in");

                    if (isOpen) {
                        // Fermer le menu
                        $submenu.removeClass("in");
                        $dieteticMenu.removeClass("active");
                        $link.attr("aria-expanded", "false");
                        console.log("📤 Menu fermé");
                    } else {
                        // Fermer les autres menus d'abord
                        $("#side-menu .nav-second-level.in").removeClass("in");
                        $("#side-menu > li.active").removeClass("active");

                        // Ouvrir ce menu
                        $submenu.addClass("in");
                        $dieteticMenu.addClass("active");
                        $link.attr("aria-expanded", "true");
                        console.log("📥 Menu ouvert");
                    }

                    return false;
                });

                // Auto-ouvrir si on est sur une page diététique
                var currentPath = window.location.pathname;
                if (currentPath.indexOf("/dietetic/") !== -1) {
                    $submenu.addClass("in");
                    $dieteticMenu.addClass("active");
                    $link.attr("aria-expanded", "true");
                    console.log("✅ Menu auto-ouvert (page diététique détectée)");

                    // Marquer l'élément actif du sous-menu
                    $submenu.find("> li > a").each(function() {
                        var href = $(this).attr("href");
                        if (href && currentPath.indexOf(href) !== -1) {
                            $(this).parent("li").addClass("active");
                        }
                    });
                }

                console.log("✅ Menu Diététique initialisé avec succès!");

            }, 2000); // Attendre 2 secondes pour être sûr que tout est chargé
        });
    });
})();
