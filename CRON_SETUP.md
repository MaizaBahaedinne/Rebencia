cron: '0 0 * * *'
description: Synchroniser automatiquement tous les objectifs chaque minuit

Pour activer dans CodeIgniter 4, ajouter dans `app/Config/CronJobs.php` ou utiliser un vrai cron système :

```bash
# Ajouter au crontab système (crontab -e)
0 0 * * * cd /var/www/rebencia && php spark sync:objectives-ca > /dev/null 2>&1
```

Ou avec supervisord pour robustesse :

```ini
[program:rebencia-sync-objectives]
command=php spark sync:objectives-ca
directory=/var/www/rebencia
autorestart=true
startsecs=10
stopasgroup=true
stdout_logfile=/var/log/rebencia/sync-objectives.log
```
