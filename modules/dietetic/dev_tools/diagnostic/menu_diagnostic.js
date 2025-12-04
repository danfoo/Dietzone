/**
 * Script de diagnostic pour analyser le menu Diététique
 * Ce script affiche des informations détaillées sur la structure du menu
 */
(function() {
    'use strict';

    // Vérifier que jQuery est chargé
    if (typeof jQuery === 'undefined') {
        console.error("❌ jQuery n'est pas chargé! Le diagnostic ne peut pas s'exécuter.");
        return;
    }

    console.log("========================================");
    console.log("DIAGNOSTIC MENU DIÉTÉTIQUE");
    console.log("========================================");
    console.log("✅ jQuery version:", jQuery.fn.jquery);

    // Attendre que le DOM soit chargé
    jQuery(document).ready(function($) {
        setTimeout(function() {

        // 1. Analyser la structure du sidebar
        console.log("\n1. STRUCTURE DU SIDEBAR:");
        console.log("Side menu existe?", $("#side-menu").length > 0);
        console.log("Nombre total de menus:", $("#side-menu > li").length);

        // 2. Trouver le menu Diététique
        console.log("\n2. RECHERCHE DU MENU DIÉTÉTIQUE:");
        var $dieteticMenu = null;

        $("#side-menu > li").each(function(index) {
            var $li = $(this);
            var $link = $li.find("> a");
            var text = $link.text().trim();
            var href = $link.attr("href");
            var classes = $li.attr("class");

            console.log("Menu #" + index + ":", {
                text: text,
                href: href,
                classes: classes,
                hasSubmenu: $li.find("> ul").length > 0
            });

            if (text === "Diététique" || text.indexOf("Dietetic") !== -1) {
                $dieteticMenu = $li;
                console.log(">>> MENU DIÉTÉTIQUE TROUVÉ! <<<");
            }
        });

        if (!$dieteticMenu) {
            console.error("❌ Menu Diététique NON TROUVÉ!");
            return;
        }

        // 3. Analyser le menu Diététique en détail
        console.log("\n3. ANALYSE DU MENU DIÉTÉTIQUE:");
        var $link = $dieteticMenu.find("> a");
        var $submenu = $dieteticMenu.find("> ul");

        console.log("Lien principal:", {
            href: $link.attr("href"),
            text: $link.text().trim(),
            classes: $link.attr("class")
        });

        console.log("Sous-menu:", {
            existe: $submenu.length > 0,
            classes: $submenu.attr("class"),
            id: $submenu.attr("id"),
            nombre_items: $submenu.find("> li").length,
            visible: $submenu.is(":visible"),
            hasClass_in: $submenu.hasClass("in"),
            hasClass_collapse: $submenu.hasClass("collapse")
        });

        if ($submenu.length > 0) {
            console.log("Items du sous-menu:");
            $submenu.find("> li").each(function(idx) {
                var $item = $(this);
                var $itemLink = $item.find("> a");
                console.log("  - " + idx + ":", {
                    text: $itemLink.text().trim(),
                    href: $itemLink.attr("href")
                });
            });
        }

        // 4. Vérifier les événements attachés
        console.log("\n4. ÉVÉNEMENTS ATTACHÉS:");
        var events = $._data($link[0], "events");
        if (events) {
            console.log("Événements sur le lien principal:", Object.keys(events));
            if (events.click) {
                console.log("Handlers de click:", events.click.length);
            }
        } else {
            console.log("⚠️ Aucun événement trouvé sur le lien principal");
        }

        // 5. Comparer avec un autre menu qui fonctionne
        console.log("\n5. COMPARAISON AVEC D'AUTRES MENUS:");
        $("#side-menu > li").each(function() {
            var $li = $(this);
            var $otherSubmenu = $li.find("> ul");

            if ($otherSubmenu.length > 0 && !$li.is($dieteticMenu)) {
                var $otherLink = $li.find("> a");
                console.log("Autre menu avec sous-menu:", {
                    text: $otherLink.text().trim(),
                    href: $otherLink.attr("href"),
                    submenu_classes: $otherSubmenu.attr("class"),
                    submenu_visible: $otherSubmenu.is(":visible"),
                    submenu_has_in: $otherSubmenu.hasClass("in")
                });

                // Vérifier les événements sur cet autre menu
                var otherEvents = $._data($otherLink[0], "events");
                if (otherEvents) {
                    console.log("  Événements:", Object.keys(otherEvents));
                }

                return false; // Arrêter après le premier
            }
        });

        // 6. Tester le clic manuellement
        console.log("\n6. TEST MANUEL:");
        console.log("Cliquez sur le menu Diététique dans la sidebar...");

        $link.on("click.diagnostic", function(e) {
            console.log(">>> CLIC DÉTECTÉ SUR LE MENU DIÉTÉTIQUE <<<");
            console.log("Event:", {
                type: e.type,
                target: e.target,
                currentTarget: e.currentTarget,
                defaultPrevented: e.isDefaultPrevented(),
                propagationStopped: e.isPropagationStopped()
            });

            setTimeout(function() {
                console.log("État du sous-menu après clic:", {
                    visible: $submenu.is(":visible"),
                    hasClass_in: $submenu.hasClass("in"),
                    display: $submenu.css("display"),
                    height: $submenu.height()
                });
            }, 100);
        });

        // 7. Vérifier si Bootstrap collapse est initialisé
        console.log("\n7. VÉRIFICATION BOOTSTRAP COLLAPSE:");
        console.log("Bootstrap défini?", typeof $.fn.collapse !== "undefined");
        if (typeof $.fn.collapse !== "undefined") {
            var collapseData = $submenu.data("bs.collapse");
            console.log("Collapse initialisé sur le sous-menu?", collapseData !== undefined);
            if (collapseData) {
                console.log("Collapse data:", collapseData);
            }
        }

        console.log("\n========================================");
        console.log("FIN DU DIAGNOSTIC");
        console.log("========================================");

        }, 1500); // Attendre 1.5s pour être sûr que tout est chargé
    }); // Fin jQuery(document).ready

})(); // Fin IIFE
