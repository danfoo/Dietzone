#!/bin/bash
# Script de déploiement pour le module Dietetic
# À exécuter sur le serveur app.dietsenegal.net

echo "======================================"
echo "  Déploiement Module Dietetic"
echo "======================================"
echo ""

# Couleurs pour les messages
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# 1. Vérifier qu'on est dans le bon dossier
if [ ! -f "dietetic.php" ]; then
    echo -e "${RED}❌ Erreur: Vous n'êtes pas dans le dossier modules/dietetic/${NC}"
    echo "Naviguez vers le bon dossier avec:"
    echo "cd /chemin/vers/perfex/modules/dietetic"
    exit 1
fi

echo -e "${GREEN}✅ Dossier correct détecté${NC}"
echo ""

# 2. Afficher la branche actuelle
echo "Branche actuelle:"
git branch | grep "^*"
echo ""

# 3. Récupérer les changements
echo "Récupération des changements depuis GitHub..."
git fetch origin

# 4. Basculer sur la bonne branche
echo "Basculement vers la branche de correction..."
git checkout claude/incomplete-request-011CUrrfkZGfQkdXijx3fAVD

# 5. Tirer les derniers changements
echo "Application des derniers changements..."
git pull origin claude/incomplete-request-011CUrrfkZGfQkdXijx3fAVD

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Changements appliqués avec succès${NC}"
else
    echo -e "${RED}❌ Erreur lors du pull${NC}"
    exit 1
fi

echo ""

# 6. Vérifier que les fichiers clés sont à jour
echo "Vérification des fichiers clés..."

# Vérifier Portal.php
if grep -q "Check if we're viewing a specific meal plan" controllers/Portal.php; then
    echo -e "${GREEN}✅ controllers/Portal.php - À jour${NC}"
else
    echo -e "${RED}❌ controllers/Portal.php - Non à jour${NC}"
fi

# Vérifier portal_meal_plans.php
if grep -q "meal_plans?view=" views/portal_meal_plans.php; then
    echo -e "${GREEN}✅ views/portal_meal_plans.php - À jour${NC}"
else
    echo -e "${RED}❌ views/portal_meal_plans.php - Non à jour${NC}"
fi

echo ""

# 7. Afficher le dernier commit
echo "Dernier commit appliqué:"
git log -1 --oneline

echo ""
echo "======================================"
echo -e "${GREEN}✅ Déploiement terminé !${NC}"
echo "======================================"
echo ""
echo "URLs à tester:"
echo "1. https://app.dietsenegal.net/dietetic/portal/meal_plans"
echo "2. https://app.dietsenegal.net/dietetic/portal/meal_plans?view=2"
echo ""
