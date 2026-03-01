# API Catalog v1

| Module | Entité | Endpoint | Audience | Permission requise | Ownership rule | Statut |
|---|---|---|---|---|---|---|
| ApiKey | ApiKey | `GET /api/v2/api-key` | admin | `ROLE_ADMIN`/`ROLE_ROOT` | none | existing |
| ApiKey | ApiKey | `POST /api/v2/api-key` | admin | `ROLE_ADMIN`/`ROLE_ROOT` | none | existing |
| Blog | BlogPost | `GET /api/v1/admin/blog-posts` | admin | `ROLE_ADMIN`/`ROLE_ROOT` | none | existing |
| Blog | BlogPost | `GET /v1/blog-posts` | authenticated shared | ambiguous (mixed legacy prefix) | none | to split |
| Calendar | Event | `GET /api/v1/admin/calendar/events` | admin | `ROLE_ADMIN`/`ROLE_ROOT` | none | existing |
| Calendar | Event | `GET /v1/calendar/events` | authenticated shared | ambiguous (mixed legacy prefix) | none | to split |
| Chat | Conversation | `GET /api/v1/me/chat/conversations` | me | `IS_AUTHENTICATED_FULLY` | participant only | existing |
| Chat | Conversation | `GET /v1/chat/conversations` | authenticated shared | `IS_AUTHENTICATED_FULLY` | participant only | to split |
| Company | Company | `GET /api/v1/admin/companies` | admin | `ROLE_ADMIN`/`ROLE_ROOT` | none | existing |
| Company | Company | `GET /v1/companies` | authenticated shared | ambiguous (mixed legacy prefix) | none | to split |
| Configuration | Configuration | `GET /api/v1/admin/configurations` | admin | `ROLE_ADMIN`/`ROLE_ROOT` | none | existing |
| Configuration | Configuration | `GET /v1/configurations` | authenticated shared | ambiguous (mixed legacy prefix) | none | to split |
| Media | Media | `GET /api/v1/admin/medias` | admin | `ROLE_ADMIN`/`ROLE_ROOT` | none | existing |
| Media | Media | `GET /v1/medias` | authenticated shared | ambiguous (mixed legacy prefix) | none | to split |
| Notification | Notification | `GET /api/v1/me/notifications` | me | `IS_AUTHENTICATED_FULLY` | owner = logged user | existing |
| Notification | Notification | `GET /api/v1/admin/notifications/users/{id}/unread-count` | admin | `ROLE_ADMIN`/`ROLE_ROOT` | none | existing |
| Quiz | Quiz | `GET /api/v1/admin/quizzes` | admin | `ROLE_ADMIN`/`ROLE_ROOT` | none | existing |
| Quiz | Quiz | `GET /v1/quizzes` | authenticated shared | ambiguous (mixed legacy prefix) | none | to split |
| Recruit | JobApplication | `GET /api/v1/admin/job-applications` | admin | `ROLE_ADMIN`/`ROLE_ROOT` | none | existing |
| Recruit | JobApplication | `GET /api/v1/me/job-applications/my-offers` | me | `IS_AUTHENTICATED_FULLY` | owner = candidate | existing |
| Role | Role | `GET /api/v1/admin/roles` | admin | `ROLE_ADMIN`/`ROLE_ROOT` | none | existing |
| Role | Role | `GET /v1/roles` | authenticated shared | ambiguous (mixed legacy prefix) | none | to split |
| Statistic | Statistic | `GET /api/v1/admin/statistics/overview` | admin | `ROLE_ADMIN`/`ROLE_ROOT` | none | existing |
| Statistic | Statistic | `GET /v1/statistics/overview` | authenticated shared | ambiguous (mixed legacy prefix) | none | to split |
| Task | Task | `GET /api/v1/admin/tasks` | admin | `ROLE_ADMIN`/`ROLE_ROOT` | none | existing |
| Task | Task | `GET /v1/tasks` | authenticated shared | ambiguous (mixed legacy prefix) | none | to split |
| User | User | `GET /api/v1/admin/users` | admin | `ROLE_ADMIN`/`ROLE_ROOT` | none | existing |
| User | Profile | `GET /v1/me` | me | `IS_AUTHENTICATED_FULLY` | owner = logged user | existing |

## Actions dérivées du catalogue

- **Endpoints manquants créés**: `GET /api/v1/admin/notifications/users/{id}/unread-count`.
- **Endpoints shared ambigus scindés**: pour Notification, distinction explicite entre endpoint `me` et endpoint `admin`.
- **Tests d'accès ajoutés**: couverture `200 / 403 / 404` pour le nouvel endpoint admin Notification selon audience.
