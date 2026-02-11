# Tableau des Dashboards - Rebencia

## Structure des données disponibles

### Tables principales :
- **users** - Utilisateurs (agents, coordinateurs, etc.)
- **roles** - Rôles (admin, directeur, chef d'agence, coordinateur, collaborateur)
- **agencies** - Agences
- **clients** - Clients
- **properties** - Biens immobiliers
- **transactions** - Transactions
- **commissions** - Commissions
- **objectives** - Objectifs (personnels et agence)
- **property_requests** - Demandes clients (visite, info)
- **property_estimations** - Demandes d'estimation
- **search_alerts** - Alertes de recherche
- **appointments** - Rendez-vous
- **tasks** - Tâches
- **property_views** - Vues des biens (traffic)
- **notifications** - Notifications
- **audit_logs** - Logs d'audit

---

## 1. Dashboard ADMIN (Système)

### Métriques disponibles :
| Métrique | Source | Requête |
|----------|--------|---------|
| **Utilisateurs actifs** | `users` | `SELECT COUNT(*) FROM users WHERE status='active'` |
| **Traffic site (vues biens)** | `property_views` | `SELECT COUNT(*) FROM property_views WHERE DATE(viewed_at) >= DATE_SUB(NOW(), INTERVAL 30 DAY)` |
| **Nouvelles inscriptions** | `users` | `SELECT COUNT(*) FROM users WHERE DATE(created_at) >= DATE_SUB(NOW(), INTERVAL 30 DAY)` |
| **Demandes de support** | `tasks` | `SELECT COUNT(*) FROM tasks WHERE type='support' AND status='pending'` |
| **Logs d'erreur** | `audit_logs` | `SELECT COUNT(*) FROM audit_logs WHERE level='error' AND DATE(created_at) = CURDATE()` |
| **Charge serveur** | Système | PHP: `sys_getloadavg()`, Mémoire, Disque |
| **Emails envoyés** | `email_logs` | `SELECT COUNT(*) FROM email_logs WHERE DATE(sent_at) >= DATE_SUB(NOW(), INTERVAL 7 DAY)` |
| **SMS envoyés** | `sms_logs` | `SELECT COUNT(*) FROM sms_logs WHERE DATE(sent_at) >= DATE_SUB(NOW(), INTERVAL 7 DAY)` |

### KPIs :
- Utilisateurs en ligne (dernière heure)
- Temps de réponse moyen
- Taux d'erreur système
- Espace disque utilisé

---

## 2. Dashboard DIRECTEUR (Vue Groupe)

### Métriques disponibles :
| Métrique | Source | Requête |
|----------|--------|---------|
| **Total clients** | `clients` | `SELECT COUNT(*) FROM clients` |
| **Clients ce mois** | `clients` | `SELECT COUNT(*) FROM clients WHERE MONTH(created_at) = MONTH(NOW())` |
| **Total agences** | `agencies` | `SELECT COUNT(*) FROM agencies WHERE status='active'` |
| **Total biens** | `properties` | `SELECT COUNT(*) FROM properties` |
| **Biens par type** | `properties` | `SELECT property_type, COUNT(*) as count FROM properties GROUP BY property_type` |
| **Total transactions** | `transactions` | `SELECT COUNT(*) FROM transactions WHERE status='completed'` |
| **CA total** | `transactions` | `SELECT SUM(total_amount) FROM transactions WHERE status='completed'` |
| **CA ce mois** | `transactions` | `SELECT SUM(total_amount) FROM transactions WHERE status='completed' AND MONTH(completed_at) = MONTH(NOW())` |
| **CA par agence** | `transactions + agencies` | `SELECT a.name, SUM(t.total_amount) FROM transactions t JOIN agencies a ON t.agency_id=a.id WHERE t.status='completed' GROUP BY a.id` |
| **Objectifs groupe** | `objectives` | `SELECT * FROM objectives WHERE type='agency' AND status='active'` |
| **Progression objectifs** | Calcul | Comparaison target vs achieved |
| **Top agents** | `transactions + users` | `SELECT u.first_name, u.last_name, COUNT(t.id) as deals FROM transactions t JOIN users u ON t.agent_id=u.id WHERE t.status='completed' GROUP BY u.id ORDER BY deals DESC LIMIT 10` |

### Graphiques :
- Evolution CA mensuelle (12 derniers mois)
- Répartition biens par type (pie chart)
- Performance par agence (bar chart)
- Taux de conversion (deals/leads)

---

## 3. Dashboard CHEF D'AGENCE

### Métriques disponibles (filtrées par agency_id) :
| Métrique | Source | Requête |
|----------|--------|---------|
| **Clients agence** | `clients` | `SELECT COUNT(*) FROM clients WHERE agency_id = ?` |
| **Biens agence** | `properties` | `SELECT COUNT(*) FROM properties WHERE agency_id = ?` |
| **Biens actifs** | `properties` | `SELECT COUNT(*) FROM properties WHERE agency_id = ? AND status='available'` |
| **Transactions** | `transactions` | `SELECT COUNT(*) FROM transactions WHERE agency_id = ? AND status='completed'` |
| **CA agence** | `transactions` | `SELECT SUM(total_amount) FROM transactions WHERE agency_id = ? AND status='completed'` |
| **CA ce mois** | `transactions` | `SELECT SUM(total_amount) FROM transactions WHERE agency_id = ? AND status='completed' AND MONTH(completed_at) = MONTH(NOW())` |
| **Objectif agence** | `objectives` | `SELECT * FROM objectives WHERE type='agency' AND agency_id = ? AND status='active'` |
| **Agents agence** | `users` | `SELECT COUNT(*) FROM users WHERE agency_id = ? AND status='active'` |
| **Performance agents** | `transactions + users` | `SELECT u.first_name, u.last_name, COUNT(t.id) as deals, SUM(t.total_amount) as revenue FROM transactions t JOIN users u ON t.agent_id=u.id WHERE u.agency_id = ? AND t.status='completed' GROUP BY u.id` |
| **Demandes en attente** | `property_requests` | `SELECT COUNT(*) FROM property_requests pr JOIN properties p ON pr.property_id=p.id WHERE p.agency_id = ? AND pr.status='pending'` |

### Graphiques :
- Evolution CA mensuelle
- Performance par agent
- Progression objectifs
- Biens par statut (disponible, vendu, loué)

---

## 4. Dashboard COORDINATEUR

### Métriques disponibles (filtrées par user_id) :
| Métrique | Source | Requête |
|----------|--------|---------|
| **Mes demandes clients** | `property_requests` | `SELECT COUNT(*) FROM property_requests WHERE assigned_to = ?` |
| **Demandes en attente** | `property_requests` | `SELECT COUNT(*) FROM property_requests WHERE assigned_to = ? AND status='pending'` |
| **Demandes traitées** | `property_requests` | `SELECT COUNT(*) FROM property_requests WHERE assigned_to = ? AND status='completed'` |
| **Mes estimations** | `property_estimations` | `SELECT COUNT(*) FROM property_estimations WHERE agent_id = ?` |
| **Estimations en attente** | `property_estimations` | `SELECT COUNT(*) FROM property_estimations WHERE agent_id = ? AND status='pending'` |
| **Mes clients** | `clients` | `SELECT COUNT(*) FROM clients WHERE agent_id = ?` |
| **Clients ce mois** | `clients` | `SELECT COUNT(*) FROM clients WHERE agent_id = ? AND MONTH(created_at) = MONTH(NOW())` |
| **Mes biens** | `properties` | `SELECT COUNT(*) FROM properties WHERE agent_id = ?` |
| **Biens actifs** | `properties` | `SELECT COUNT(*) FROM properties WHERE agent_id = ? AND status='available'` |
| **Mes transactions** | `transactions` | `SELECT COUNT(*) FROM transactions WHERE agent_id = ? AND status='completed'` |
| **Mon CA** | `transactions` | `SELECT SUM(total_amount) FROM transactions WHERE agent_id = ? AND status='completed'` |
| **CA ce mois** | `transactions` | `SELECT SUM(total_amount) FROM transactions WHERE agent_id = ? AND status='completed' AND MONTH(completed_at) = MONTH(NOW())` |
| **Mes commissions** | `commissions` | `SELECT SUM(amount) FROM commissions WHERE user_id = ? AND status='paid'` |
| **Commissions en attente** | `commissions` | `SELECT SUM(amount) FROM commissions WHERE user_id = ? AND status='pending'` |
| **Mon objectif** | `objectives` | `SELECT * FROM objectives WHERE type='personal' AND user_id = ? AND status='active'` |
| **Mes rendez-vous** | `appointments` | `SELECT COUNT(*) FROM appointments WHERE agent_id = ? AND appointment_date >= CURDATE()` |
| **Mes tâches** | `tasks` | `SELECT COUNT(*) FROM tasks WHERE assigned_to = ? AND status!='completed'` |

### Graphiques :
- Evolution CA mensuelle
- Progression objectif personnel
- Répartition demandes par statut
- Pipeline deals

---

## 5. Dashboard COLLABORATEUR

### Métriques disponibles (identiques au Coordinateur) :
| Métrique | Source | Requête |
|----------|--------|---------|
| **Mes demandes clients** | `property_requests` | `SELECT COUNT(*) FROM property_requests WHERE assigned_to = ?` |
| **Mes estimations** | `property_estimations` | `SELECT COUNT(*) FROM property_estimations WHERE agent_id = ?` |
| **Mes clients** | `clients` | `SELECT COUNT(*) FROM clients WHERE agent_id = ?` |
| **Mes biens** | `properties` | `SELECT COUNT(*) FROM properties WHERE agent_id = ?` |
| **Mes transactions** | `transactions` | `SELECT COUNT(*) FROM transactions WHERE agent_id = ? AND status='completed'` |
| **Mon CA** | `transactions` | `SELECT SUM(total_amount) FROM transactions WHERE agent_id = ? AND status='completed'` |
| **Mes commissions** | `commissions` | `SELECT SUM(amount) FROM commissions WHERE user_id = ?` |
| **Mon objectif** | `objectives` | `SELECT * FROM objectives WHERE type='personal' AND user_id = ? AND status='active'` |
| **Mes rendez-vous** | `appointments` | `SELECT COUNT(*) FROM appointments WHERE agent_id = ? AND appointment_date >= CURDATE()` |
| **Mes tâches** | `tasks` | `SELECT COUNT(*) FROM tasks WHERE assigned_to = ? AND status!='completed'` |

### Graphiques :
- Evolution CA mensuelle
- Progression objectif personnel
- Activité quotidienne
- Performance vs équipe

---

## Composants visuels communs

### Cards statistiques :
- Icône + Valeur + Label + Évolution %
- Couleurs : Primary (bleu), Success (vert), Warning (orange), Danger (rouge)

### Graphiques :
- **Line Chart** - Evolution temporelle (CA, clients, etc.)
- **Bar Chart** - Comparaisons (agences, agents, etc.)
- **Pie/Donut Chart** - Répartitions (types biens, statuts, etc.)
- **Progress Bar** - Objectifs (target vs achieved)

### Listes rapides :
- Dernières transactions
- Demandes en attente
- Prochains rendez-vous
- Tâches à faire

---

## Implémentation recommandée

### Structure fichiers :
```
app/Controllers/Admin/
├── DashboardAdmin.php
├── DashboardDirector.php
├── DashboardManager.php (Chef d'agence)
├── DashboardCoordinator.php
└── DashboardAgent.php (Collaborateur)

app/Views/admin/dashboards/
├── admin.php
├── director.php
├── manager.php
├── coordinator.php
└── agent.php

app/Models/
└── DashboardModel.php (requêtes communes)
```

### Librairies :
- **Chart.js** - Graphiques
- **ApexCharts** - Alternative moderne
- **DataTables** - Tableaux interactifs

### Temps réel :
- **Server-Sent Events (SSE)** - Push notifications
- **AJAX polling** - Actualisation auto toutes les 30s

---

## Prochaines étapes

1. Créer les contrôleurs pour chaque dashboard
2. Créer le DashboardModel avec les métriques
3. Créer les vues avec cards et graphiques
4. Ajouter les routes et menus
5. Implémenter les graphiques Chart.js
6. Ajouter l'actualisation temps réel

Prêt à commencer ? 🚀
