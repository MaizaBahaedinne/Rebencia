#!/bin/bash
# Script pour corriger les permissions sur le serveur Rebencia

# Se connecter au serveur et exécuter ces commandes :
# ssh votre_user@rebencia.com

# Une fois connecté, exécutez :

cd /home/rebencia.com/public_html

# Corriger les permissions du dossier writable et ses sous-dossiers
chmod -R 775 writable/
chown -R www-data:www-data writable/

# Ou si www-data n'existe pas, utilisez le user Apache/Nginx :
# chown -R apache:apache writable/
# ou
# chown -R nginx:nginx writable/

# Vérifier les permissions
ls -la writable/

# Spécifiquement pour les sessions
chmod 775 writable/session/
chmod 664 writable/session/*

# Pour les logs
chmod 775 writable/logs/
chmod 664 writable/logs/*

# Pour le cache
chmod 775 writable/cache/
chmod 664 writable/cache/* 2>/dev/null || true

echo "Permissions corrigées !"
