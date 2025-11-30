-- ================================================================
-- ARTICLES DE DÉMONSTRATION - SYSTÈME BLOG/CONSEILS
-- ================================================================
-- IMPORTANT: Définissez votre ID staff AVANT d'exécuter ce script
-- ================================================================

-- ⚠️ MODIFIEZ CETTE LIGNE avec votre ID staff (trouvez-le dans tblstaff)
-- Pour trouver votre ID: SELECT staffid, CONCAT(firstname, ' ', lastname) FROM tblstaff;
SET @author_id = 1;  -- ← CHANGEZ CE NOMBRE !

-- ================================================================
-- Si vous ne connaissez pas votre staff_id, exécutez d'abord:
-- SELECT staffid, CONCAT(firstname, ' ', lastname) as nom FROM tblstaff;
-- Puis remplacez le 1 ci-dessus par votre staffid
-- ================================================================

-- Article 1: Nutrition
INSERT INTO `tbldietic_blog_articles` (`title`, `slug`, `excerpt`, `content`, `category`, `tags`, `author_id`, `status`, `views_count`, `published_at`, `created_at`) VALUES
(
    '10 Aliments Riches en Protéines pour une Alimentation Équilibrée',
    '10-aliments-riches-en-proteines',
    'Découvrez les meilleures sources de protéines pour maintenir une alimentation saine et équilibrée.',
    '<h2>Pourquoi les protéines sont-elles importantes ?</h2>
<p>Les protéines sont essentielles pour la construction et la réparation des tissus musculaires, la production d\'hormones et d\'enzymes, et le maintien d\'un système immunitaire fort.</p>

<h3>Les 10 meilleures sources de protéines :</h3>
<ol>
<li><strong>Poulet</strong> - 31g de protéines pour 100g</li>
<li><strong>Œufs</strong> - 13g de protéines pour 100g</li>
<li><strong>Saumon</strong> - 25g de protéines pour 100g</li>
<li><strong>Quinoa</strong> - 14g de protéines pour 100g (cuit)</li>
<li><strong>Lentilles</strong> - 9g de protéines pour 100g (cuites)</li>
<li><strong>Fromage blanc</strong> - 10g de protéines pour 100g</li>
<li><strong>Amandes</strong> - 21g de protéines pour 100g</li>
<li><strong>Tofu</strong> - 8g de protéines pour 100g</li>
<li><strong>Yaourt grec</strong> - 10g de protéines pour 100g</li>
<li><strong>Thon</strong> - 30g de protéines pour 100g</li>
</ol>

<h3>Conseils pratiques :</h3>
<ul>
<li>Variez vos sources de protéines entre animales et végétales</li>
<li>Consommez des protéines à chaque repas</li>
<li>Les besoins moyens sont de 0,8g par kg de poids corporel</li>
</ul>

<p><em>Consultez votre diététicien pour un plan nutritionnel personnalisé.</em></p>',
    'nutrition',
    'protéines, alimentation, nutrition, santé',
    @author_id,
    'published',
    127,
    NOW(),
    NOW()
),

-- Article 2: Recettes Santé
(
    'Smoothie Bowl Énergétique : La Recette Parfaite pour le Petit-Déjeuner',
    'smoothie-bowl-energetique-recette',
    'Une recette facile et délicieuse pour commencer la journée avec énergie et vitalité.',
    '<h2>Ingrédients (pour 1 personne) :</h2>
<ul>
<li>1 banane congelée</li>
<li>150g de fruits rouges (framboises, myrtilles, fraises)</li>
<li>200ml de lait d\'amande</li>
<li>1 cuillère à soupe de beurre d\'amande</li>
<li>1 cuillère à soupe de graines de chia</li>
<li>Toppings : granola, noix de coco râpée, fruits frais</li>
</ul>

<h2>Préparation :</h2>
<ol>
<li>Mixer la banane congelée avec les fruits rouges et le lait d\'amande jusqu\'à obtenir une texture lisse et crémeuse</li>
<li>Verser dans un bol</li>
<li>Ajouter le beurre d\'amande et les graines de chia</li>
<li>Décorer avec vos toppings préférés</li>
</ol>

<h2>Valeurs nutritionnelles (approximatives) :</h2>
<ul>
<li><strong>Calories :</strong> 380 kcal</li>
<li><strong>Protéines :</strong> 12g</li>
<li><strong>Glucides :</strong> 52g</li>
<li><strong>Lipides :</strong> 15g</li>
<li><strong>Fibres :</strong> 11g</li>
</ul>

<h3>Les bienfaits :</h3>
<p>Ce smoothie bowl est riche en antioxydants, fibres et acides gras essentiels. Parfait pour un petit-déjeuner nutritif qui vous donnera de l\'énergie pour toute la matinée !</p>',
    'recettes-sante',
    'recette, smoothie bowl, petit-déjeuner, healthy',
    @author_id,
    'published',
    89,
    DATE_SUB(NOW(), INTERVAL 2 DAY),
    DATE_SUB(NOW(), INTERVAL 2 DAY)
),

-- Article 3: Perte de Poids
(
    '5 Erreurs Courantes qui Empêchent la Perte de Poids',
    '5-erreurs-perte-de-poids',
    'Évitez ces erreurs fréquentes pour atteindre vos objectifs de perte de poids de manière saine et durable.',
    '<h2>Introduction</h2>
<p>Perdre du poids peut être un défi, surtout lorsque certaines habitudes sabotent inconsciemment vos efforts. Voici les 5 erreurs les plus courantes à éviter.</p>

<h3>1. Sauter des repas</h3>
<p>Beaucoup pensent que sauter des repas aide à perdre du poids, mais cela ralentit le métabolisme et augmente la faim, conduisant à des excès alimentaires plus tard.</p>

<h3>2. Éliminer complètement les glucides</h3>
<p>Les glucides complexes sont essentiels pour l\'énergie. Privilégiez les grains entiers, les légumineuses et les légumes plutôt que de les éliminer complètement.</p>

<h3>3. Ne pas boire assez d\'eau</h3>
<p>L\'hydratation est cruciale pour le métabolisme. Visez 1,5 à 2 litres d\'eau par jour. Parfois, la soif est confondue avec la faim.</p>

<h3>4. Se concentrer uniquement sur le poids</h3>
<p>Le poids sur la balance ne reflète pas toujours les progrès. Prenez également vos mensurations et observez comment vos vêtements vous vont.</p>

<h3>5. Avoir des attentes irréalistes</h3>
<p>Une perte de poids saine se situe entre 0,5 et 1 kg par semaine. Les résultats rapides sont rarement durables.</p>

<h2>Conclusion</h2>
<p>La clé d\'une perte de poids réussie réside dans une approche équilibrée, progressive et personnalisée. N\'hésitez pas à consulter un diététicien pour un accompagnement adapté.</p>',
    'perte-de-poids',
    'perte de poids, erreurs, conseils, régime',
    @author_id,
    'published',
    203,
    DATE_SUB(NOW(), INTERVAL 5 DAY),
    DATE_SUB(NOW(), INTERVAL 5 DAY)
),

-- Article 4: Bien-être
(
    'Alimentation et Sommeil : Comment Mieux Dormir Grâce à votre Assiette',
    'alimentation-sommeil-mieux-dormir',
    'Découvrez comment certains aliments peuvent améliorer la qualité de votre sommeil.',
    '<h2>Le lien entre alimentation et sommeil</h2>
<p>Ce que vous mangez influence directement la qualité de votre sommeil. Certains aliments favorisent la production de mélatonine et de sérotonine, hormones essentielles pour un bon repos.</p>

<h3>Aliments qui favorisent le sommeil :</h3>
<ul>
<li><strong>Amandes et noix :</strong> Riches en magnésium, elles aident à la relaxation musculaire</li>
<li><strong>Bananes :</strong> Contiennent du tryptophane et du magnésium</li>
<li><strong>Kiwis :</strong> Riches en sérotonine</li>
<li><strong>Tisanes :</strong> Camomille, valériane, passiflore</li>
<li><strong>Poissons gras :</strong> Saumon, maquereau (riches en vitamine D et oméga-3)</li>
<li><strong>Lait chaud :</strong> Contient du tryptophane</li>
</ul>

<h3>Aliments à éviter le soir :</h3>
<ul>
<li>Caféine (café, thé, chocolat)</li>
<li>Alcool (perturbe les cycles de sommeil)</li>
<li>Repas trop copieux ou gras</li>
<li>Aliments épicés</li>
<li>Sucres raffinés</li>
</ul>

<h3>Conseils pratiques :</h3>
<ol>
<li>Dînez 2-3 heures avant le coucher</li>
<li>Privilégiez un repas léger le soir</li>
<li>Évitez les écrans 1h avant de dormir</li>
<li>Créez une routine de coucher relaxante</li>
</ol>

<p><strong>Astuce :</strong> Une tisane de camomille avec une poignée d\'amandes 1h avant le coucher peut significativement améliorer votre sommeil !</p>',
    'bien-etre',
    'sommeil, alimentation, bien-être, santé',
    @author_id,
    'published',
    156,
    DATE_SUB(NOW(), INTERVAL 7 DAY),
    DATE_SUB(NOW(), INTERVAL 7 DAY)
),

-- Article 5: Sport & Activité
(
    'Nutrition Pré et Post-Entraînement : Optimisez vos Performances',
    'nutrition-pre-post-entrainement',
    'Apprenez quoi manger avant et après votre séance de sport pour maximiser vos résultats.',
    '<h2>L\'importance de la nutrition sportive</h2>
<p>Une bonne nutrition autour de l\'entraînement peut améliorer vos performances, accélérer la récupération et maximiser les résultats.</p>

<h3>Avant l\'entraînement (1-2h avant) :</h3>
<h4>Objectifs :</h4>
<ul>
<li>Fournir de l\'énergie</li>
<li>Prévenir la faim</li>
<li>Hydrater le corps</li>
</ul>

<h4>Quoi manger :</h4>
<ul>
<li><strong>Glucides complexes :</strong> Flocons d\'avoine, patate douce, riz complet</li>
<li><strong>Protéines maigres :</strong> Poulet, dinde, tofu</li>
<li><strong>Fruits :</strong> Banane, pomme</li>
<li><strong>Exemple :</strong> Toast de pain complet + beurre d\'amande + banane</li>
</ul>

<h3>Pendant l\'entraînement :</h3>
<ul>
<li>Hydratation : 150-250ml d\'eau toutes les 15-20 minutes</li>
<li>Si effort > 1h : Boisson isotonique</li>
</ul>

<h3>Après l\'entraînement (dans les 30-60 min) :</h3>
<h4>Objectifs :</h4>
<ul>
<li>Reconstituer les réserves de glycogène</li>
<li>Réparer les muscles</li>
<li>Réhydrater</li>
</ul>

<h4>Quoi manger :</h4>
<ul>
<li><strong>Ratio idéal :</strong> 3:1 glucides/protéines</li>
<li><strong>Protéines :</strong> 20-30g (shake, poulet, œufs)</li>
<li><strong>Glucides :</strong> 60-90g (riz, pâtes, fruits)</li>
<li><strong>Exemple :</strong> Smoothie (banane + whey + flocons d\'avoine + lait d\'amande)</li>
</ul>

<h3>Hydratation post-entraînement :</h3>
<p>Buvez 150% du poids perdu pendant l\'effort (pesez-vous avant et après).</p>

<h2>Supplémentation (optionnelle) :</h2>
<ul>
<li><strong>Créatine :</strong> 3-5g par jour pour force et masse musculaire</li>
<li><strong>BCAA :</strong> Pendant l\'effort pour endurance</li>
<li><strong>Whey :</strong> Protéines rapides post-entraînement</li>
</ul>

<p><em>Note : Consultez toujours un professionnel avant de commencer une supplémentation.</em></p>',
    'sport-activite',
    'sport, nutrition sportive, entraînement, performance',
    @author_id,
    'published',
    178,
    DATE_SUB(NOW(), INTERVAL 10 DAY),
    DATE_SUB(NOW(), INTERVAL 10 DAY)
),

-- Article 6: Nutrition (Draft article for testing)
(
    'Les Superaliments : Mythe ou Réalité ?',
    'superaliments-mythe-realite',
    'Analyse scientifique des aliments dits "super" et de leurs véritables bienfaits pour la santé.',
    '<h2>Qu\'est-ce qu\'un superaliment ?</h2>
<p>Le terme "superaliment" est un terme marketing désignant des aliments riches en nutriments. Mais sont-ils vraiment supérieurs ?</p>

<h3>Les superaliments populaires :</h3>
<ul>
<li>Baies de goji</li>
<li>Spiruline</li>
<li>Graines de chia</li>
<li>Açaï</li>
<li>Curcuma</li>
<li>Matcha</li>
</ul>

<p><em>Article en cours de rédaction... Plus de contenu à venir !</em></p>',
    'nutrition',
    'superaliments, nutrition, santé',
    @author_id,
    'draft',
    0,
    NULL,
    NOW()
);
