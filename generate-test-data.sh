#!/bin/bash

echo "========================================="
echo "  REBENCIA - Génération des données test"
echo "========================================="
echo ""

# Couleurs
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Vérifier que nous sommes dans le bon répertoire
if [ ! -f "spark" ]; then
    echo -e "${RED}Erreur: Veuillez exécuter ce script depuis la racine du projet Rebencia${NC}"
    exit 1
fi

echo -e "${YELLOW}Voulez-vous générer les données de test?${NC}"
echo "  - 10 agents avec des données réalistes"
echo "  - 100 biens immobiliers complets"
echo ""
read -p "Continuer? (o/n) " -n 1 -r
echo ""

if [[ ! $REPLY =~ ^[OoYy]$ ]]
then
    echo "Opération annulée."
    exit 0
fi

echo ""
echo -e "${GREEN}Exécution du seeder...${NC}"
echo ""

php spark db:seed DatabaseSeeder

echo ""
echo -e "${GREEN}✓ Terminé !${NC}"
echo ""
echo "Vous pouvez maintenant:"
echo "  1. Vous connecter en tant qu'admin"
echo "  2. Aller sur la page 'Utilisateurs'"
echo "  3. Cliquer sur l'icône verte pour vous connecter en tant qu'un agent"
echo ""
