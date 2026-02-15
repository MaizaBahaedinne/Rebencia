#!/bin/bash

# ============================================================================
# Script de configuration - Zones géographiques
# ============================================================================
# Ce script crée la table zones manquante et insère les données du Grand Tunis

echo "🔧 Configuration des zones géographiques..."
echo ""

# Vérifier les variables d'environnement
if [ ! -f ".env" ]; then
    echo "❌ Erreur: fichier .env non trouvé!"
    exit 1
fi

# Source le fichier .env pour récupérer les paramètres de base de données
source .env

# Paramètres de base de données
DB_HOST=${database_hostname:-"localhost"}
DB_NAME=${database_database:-"rebe_RebenciaDB"}
DB_USER=${database_username:-"root"}
DB_PASS=${database_password:-""}

echo "📊 Base de données: $DB_NAME"
echo "🔗 Hôte: $DB_HOST"
echo ""

# Option 1: Utiliser mysql directement
if command -v mysql &> /dev/null; then
    echo "▶️  Exécution du script SQL..."
    mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < setup_zones_complete.sql
    
    if [ $? -eq 0 ]; then
        echo ""
        echo "✅ Les zones géographiques ont été insérées avec succès!"
        echo ""
        echo "📈 Résumé:"
        echo "  - 4 gouvernorats (régions)"
        echo "  - 57 villes réparties"
        echo "  - Scores de popularité configurés"
        echo ""
        echo "✨ Votre plateforme est maintenant prête avec les données géographiques!"
    else
        echo "❌ Erreur lors de l'exécution du script SQL"
        exit 1
    fi
else
    echo "⚠️  MySQL n'est pas installé ou n'est pas dans le PATH"
    echo ""
    echo "Instructions manuelles:"
    echo "1. Ouvrez votre client MySQL (mysql-cli, phpMyAdmin, DBeaver, etc.)"
    echo "2. Connectez-vous à la base de données: $DB_NAME"
    echo "3. Copiez et collez le contenu du fichier: setup_zones_complete.sql"
    echo "4. Exécutez le script"
    exit 1
fi
