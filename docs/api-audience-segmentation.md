# API audience segmentation (`admin` / `me`)

## Convention cible

- **Administration (lecture/écriture globale)**: `/api/v1/admin/...`
- **Utilisateur connecté (self-service)**: `/api/v1/me/...` (ou `/api/v1/profile/...` quand la ressource est déjà profil-centric)

## Migration progressive

### Étape 1 — Ajout des nouvelles routes

Les alias d'audience sont ajoutés :

- `/api/v1/admin/{legacyPath}` → redirect interne temporaire vers `/api/v1/{legacyPath}`
- `/api/v1/me/{legacyPath}` → redirect interne temporaire vers `/api/v1/{legacyPath}`

### Étape 2 — Anciennes routes conservées et marquées deprecated

Toutes les routes legacy `/api/v1/*` des modules concernés restent actives mais renvoient des en-têtes de dépréciation :

- `Deprecation`
- `Sunset`
- `Link: </docs/api-audience-segmentation.md>; rel="deprecation"`

### Étape 3 — Retrait planifié

- **Début de dépréciation**: `2026-04-01`
- **Sunset / retrait prévu des routes legacy**: `2026-10-01`

## Table de référence “endpoint actuel → endpoint cible”

> Règle de transformation: préfixer les endpoints legacy `/api/v1/...` par `/api/v1/admin/...` (admin) ou `/api/v1/me/...` (utilisateur connecté).

### ApiKey

| Endpoint actuel | Endpoint cible admin | Endpoint cible me/profile | Notes |
|---|---|---|---|
| `/api/v1/api_key` (+ REST `/{id}`, `/count`, `/ids`, `/schema`) | `/api/v1/admin/api_key` | `/api/v1/me/api_key` | **Mixte à scinder**: listing global admin vs clés de l'utilisateur |

### Blog

| Endpoint actuel | Endpoint cible admin | Endpoint cible me/profile | Notes |
|---|---|---|---|
| `/api/v1/blog-posts` | `/api/v1/admin/blog-posts` | `/api/v1/me/blog-posts` | **Mixte à scinder** |
| `/api/v1/blog-comments` | `/api/v1/admin/blog-comments` | `/api/v1/me/blog-comments` | **Mixte à scinder** |
| `/api/v1/blog-tags` | `/api/v1/admin/blog-tags` | `/api/v1/me/blog-tags` | |
| `/api/v1/blog-post-links` | `/api/v1/admin/blog-post-links` | `/api/v1/me/blog-post-links` | |
| `/api/v1/tasks/{id}/blog-posts` | `/api/v1/admin/tasks/{id}/blog-posts` | `/api/v1/me/tasks/{id}/blog-posts` | |
| `/api/v1/task-requests/{id}/blog-posts` | `/api/v1/admin/task-requests/{id}/blog-posts` | `/api/v1/me/task-requests/{id}/blog-posts` | |

### Calendar

| Endpoint actuel | Endpoint cible admin | Endpoint cible me/profile | Notes |
|---|---|---|---|
| `/api/v1/calendar/events` | `/api/v1/admin/calendar/events` | `/api/v1/me/calendar/events` | **Mixte à scinder** |

### Chat

| Endpoint actuel | Endpoint cible admin | Endpoint cible me/profile | Notes |
|---|---|---|---|
| `/api/v1/chat/conversations` | `/api/v1/admin/chat/conversations` | `/api/v1/me/chat/conversations` | **Mixte à scinder** |
| `/api/v1/chat/conversations/{id}` | `/api/v1/admin/chat/conversations/{id}` | `/api/v1/me/chat/conversations/{id}` | |
| `/api/v1/chat/conversations/{id}/messages` | `/api/v1/admin/chat/conversations/{id}/messages` | `/api/v1/me/chat/conversations/{id}/messages` | |

### Company

| Endpoint actuel | Endpoint cible admin | Endpoint cible me/profile | Notes |
|---|---|---|---|
| `/api/v1/companies` (+ REST `/{id}`) | `/api/v1/admin/companies` | `/api/v1/me/companies` | **Mixte à scinder** |
| `/api/v1/companies/{id}/projects` | `/api/v1/admin/companies/{id}/projects` | `/api/v1/me/companies/{id}/projects` | |
| `/api/v1/companies/{id}/sprints` | `/api/v1/admin/companies/{id}/sprints` | `/api/v1/me/companies/{id}/sprints` | |
| `/api/v1/companies/{id}/members` | `/api/v1/admin/companies/{id}/members` | `/api/v1/me/companies/{id}/members` | |

### Configuration

| Endpoint actuel | Endpoint cible admin | Endpoint cible me/profile | Notes |
|---|---|---|---|
| `/api/v1/configuration` | `/api/v1/admin/configuration` | `/api/v1/me/configuration` | |
| `/api/v1/configurations` | `/api/v1/admin/configurations` | `/api/v1/me/configurations` | |

### Media

| Endpoint actuel | Endpoint cible admin | Endpoint cible me/profile | Notes |
|---|---|---|---|
| `/api/v1/media` (+ REST `/{id}`) | `/api/v1/admin/media` | `/api/v1/me/media` | **Mixte à scinder** |

### Notification

| Endpoint actuel | Endpoint cible admin | Endpoint cible me/profile | Notes |
|---|---|---|---|
| `/api/v1/notifications` | `/api/v1/admin/notifications` | `/api/v1/me/notifications` | |
| `/api/v1/notifications/{id}/read` | `/api/v1/admin/notifications/{id}/read` | `/api/v1/me/notifications/{id}/read` | |
| `/api/v1/notifications/{id}/unread` | `/api/v1/admin/notifications/{id}/unread` | `/api/v1/me/notifications/{id}/unread` | |
| `/api/v1/notifications/read-all` | `/api/v1/admin/notifications/read-all` | `/api/v1/me/notifications/read-all` | |
| `/api/v1/notifications/unread-count` | `/api/v1/admin/notifications/unread-count` | `/api/v1/me/notifications/unread-count` | |

### Quiz

| Endpoint actuel | Endpoint cible admin | Endpoint cible me/profile | Notes |
|---|---|---|---|
| `/api/v1/quizzes` (+ REST `/{id}`) | `/api/v1/admin/quizzes` | `/api/v1/me/quizzes` | **Mixte à scinder** |

### Recruit

| Endpoint actuel | Endpoint cible admin | Endpoint cible me/profile | Notes |
|---|---|---|---|
| `/api/v1/job-offers` (+ `/facets`, `/{id}`, `/my`, `/available`, `/{id}/apply`) | `/api/v1/admin/job-offers` | `/api/v1/me/job-offers` | **Mixte à scinder** (`/my` devient naturellement `me`) |
| `/api/v1/job-applications` (+ `/{id}`, `/{id}/accept`, `/{id}/reject`, `/{id}/withdraw`, `/my-offers`) | `/api/v1/admin/job-applications` | `/api/v1/me/job-applications` | **Mixte à scinder** |
| `/api/v1/offers` | `/api/v1/admin/offers` | `/api/v1/me/offers` | |
| `/api/v1/candidates` | `/api/v1/admin/candidates` | `/api/v1/me/candidates` | |
| `/api/v1/resumes` (+ `/my`, `/{id}`) | `/api/v1/admin/resumes` | `/api/v1/me/resumes` | |
| `/api/v1/resume-education` | `/api/v1/admin/resume-education` | `/api/v1/me/resume-education` | |
| `/api/v1/resume-skills` | `/api/v1/admin/resume-skills` | `/api/v1/me/resume-skills` | |
| `/api/v1/resume-experiences` | `/api/v1/admin/resume-experiences` | `/api/v1/me/resume-experiences` | |

### Role

| Endpoint actuel | Endpoint cible admin | Endpoint cible me/profile | Notes |
|---|---|---|---|
| `/api/v1/role` (+ `/{role}`, `/{role}/inherited`) | `/api/v1/admin/role` | `/api/v1/me/role` | Principalement admin |

### Statistic

| Endpoint actuel | Endpoint cible admin | Endpoint cible me/profile | Notes |
|---|---|---|---|
| `/api/v1/statistics/overview` | `/api/v1/admin/statistics/overview` | `/api/v1/me/statistics/overview` | **Mixte à scinder** |
| `/api/v1/statistics/entities` | `/api/v1/admin/statistics/entities` | `/api/v1/me/statistics/entities` | |
| `/api/v1/statistics/timeseries` | `/api/v1/admin/statistics/timeseries` | `/api/v1/me/statistics/timeseries` | |
| `/api/v1/statistics/timeseries/{entity}` | `/api/v1/admin/statistics/timeseries/{entity}` | `/api/v1/me/statistics/timeseries/{entity}` | |
| `/api/v1/statistics/distributions/statuses` | `/api/v1/admin/statistics/distributions/statuses` | `/api/v1/me/statistics/distributions/statuses` | |

### Task

| Endpoint actuel | Endpoint cible admin | Endpoint cible me/profile | Notes |
|---|---|---|---|
| `/api/v1/tasks` (+ `/{id}`, `/{id}/start`, `/{id}/complete`, `/{id}/archive`, `/{id}/reopen`) | `/api/v1/admin/tasks` | `/api/v1/me/tasks` | **Mixte à scinder** |
| `/api/v1/tasks/{id}/task-requests` | `/api/v1/admin/tasks/{id}/task-requests` | `/api/v1/me/tasks/{id}/task-requests` | |
| `/api/v1/task-requests` (+ `/{id}` + mutations spécifiques) | `/api/v1/admin/task-requests` | `/api/v1/me/task-requests` | **Mixte à scinder** |
| `/api/v1/task-requests/sprints/{sprintId}/grouped-by-task` | `/api/v1/admin/task-requests/sprints/{sprintId}/grouped-by-task` | `/api/v1/me/task-requests/sprints/{sprintId}/grouped-by-task` | |
| `/api/v1/projects` (+ `/{id}`, `/{id}/tasks`) | `/api/v1/admin/projects` | `/api/v1/me/projects` | |
| `/api/v1/sprints` (+ `/{id}`) | `/api/v1/admin/sprints` | `/api/v1/me/sprints` | |

### User

| Endpoint actuel | Endpoint cible admin | Endpoint cible me/profile | Notes |
|---|---|---|---|
| `/api/v1/user` (+ `/{user}`) | `/api/v1/admin/user` | `/api/v1/me/user` | **Mixte à scinder** |
| `/api/v1/user/{user}/roles` | `/api/v1/admin/user/{user}/roles` | `/api/v1/me/user/{user}/roles` | |
| `/api/v1/user/{user}/groups` | `/api/v1/admin/user/{user}/groups` | `/api/v1/me/user/{user}/groups` | |
| `/api/v1/user/{user}/group/{userGroup}` | `/api/v1/admin/user/{user}/group/{userGroup}` | `/api/v1/me/user/{user}/group/{userGroup}` | |
| `/api/v1/user_group` (+ `/{userGroup}`) | `/api/v1/admin/user_group` | `/api/v1/me/user_group` | |
| `/api/v1/user_group/{userGroup}/users` | `/api/v1/admin/user_group/{userGroup}/users` | `/api/v1/me/user_group/{userGroup}/users` | |
| `/api/v1/user_group/{userGroup}/user/{user}` | `/api/v1/admin/user_group/{userGroup}/user/{user}` | `/api/v1/me/user_group/{userGroup}/user/{user}` | |
| `/api/v1/profile` | `/api/v1/admin/profile` | `/api/v1/profile` | Self-service: conserver `/profile` |
| `/api/v1/profile/avatar` | `/api/v1/admin/profile/avatar` | `/api/v1/profile/avatar` | Self-service: conserver `/profile` |
| `/api/v1/profile/address` | `/api/v1/admin/profile/address` | `/api/v1/profile/address` | Self-service: conserver `/profile` |
| `/api/v1/profile/groups` | `/api/v1/admin/profile/groups` | `/api/v1/profile/groups` | Self-service: conserver `/profile` |
| `/api/v1/profile/roles` | `/api/v1/admin/profile/roles` | `/api/v1/profile/roles` | Self-service: conserver `/profile` |

## Endpoints explicitement mixtes à scinder

- `GET` global/listing en `admin`: vision transverse (toutes entités).
- `GET` restreint en `me`/`profile`: uniquement les entités liées à l'utilisateur connecté.
- Endpoints concernés en priorité: `tasks`, `task-requests`, `blog-posts`, `chat/conversations`, `companies`, `media`, `quizzes`, `statistics`, `job-offers`, `job-applications`, `user`.

## Matrice par contrôleur (routes implémentées)

| Contrôleur | Route actuelle | Route cible |
|---|---|---|
| `src/Blog/Transport/Controller/Api/V1/BlogComment/BlogCommentController.php` | `/v1/blog-comments` | `/api/v1/admin/blog-comments` |
| `src/Blog/Transport/Controller/Api/V1/BlogPost/BlogPostController.php` | `/v1/blog-posts` | `/api/v1/admin/blog-posts` |
| `src/Blog/Transport/Controller/Api/V1/BlogPostLink/BlogPostLinkController.php` | `/v1/blog-post-links` | `/api/v1/admin/blog-post-links` |
| `src/Blog/Transport/Controller/Api/V1/BlogTag/BlogTagController.php` | `/v1/blog-tags` | `/api/v1/admin/blog-tags` |
| `src/Blog/Transport/Controller/Api/V1/Task/TaskBlogPostsController.php` | `/v1/tasks` | `/api/v1/admin/tasks` |
| `src/Blog/Transport/Controller/Api/V1/TaskRequest/TaskRequestBlogPostsController.php` | `/v1/task-requests` | `/api/v1/admin/task-requests` |
| `src/Calendar/Transport/Controller/Api/V1/Event/EventController.php` | `/v1/calendar/events` | `/api/v1/admin/calendar/events` |
| `src/Chat/Transport/Controller/Api/V1/Chat/ConversationController.php` | `/v1/chat/conversations` | `/api/v1/me/chat/conversations` |
| `src/Company/Transport/Controller/Api/V1/Company/CompanyController.php` | `/v1/companies` | `/api/v1/admin/companies` |
| `src/Company/Transport/Controller/Api/V1/Company/CompanyMembersController.php` | `/v1/companies` | `/api/v1/admin/companies` |
| `src/Company/Transport/Controller/Api/V1/Company/CompanyProjectsController.php` | `/v1/companies` | `/api/v1/admin/companies` |
| `src/Company/Transport/Controller/Api/V1/Company/CompanySprintsController.php` | `/v1/companies` | `/api/v1/admin/companies` |
| `src/Configuration/Transport/Controller/Api/V1/Configuration/ConfigurationController.php` | `/v1/configuration` | `/api/v1/admin/configuration` |
| `src/Media/Transport/Controller/Api/V1/Media/MediaController.php` | `/v1/media` | `/api/v1/admin/media` |
| `src/Notification/Transport/Controller/Api/V1/Notification/NotificationController.php` | `/v1/notifications` | `/api/v1/me/notifications` |
| `src/Quiz/Transport/Controller/Api/V1/Quiz/QuizController.php` | `/v1/quizzes` | `/api/v1/admin/quizzes` |
| `src/Recruit/Transport/Controller/Api/V1/Candidate/CandidateController.php` | `/v1/candidates` | `/api/v1/admin/candidates` |
| `src/Recruit/Transport/Controller/Api/V1/JobApplication/JobApplicationController.php` | `/v1/job-applications` | `/api/v1/admin/job-applications` |
| `src/Recruit/Transport/Controller/Api/V1/JobApplication/OfferApplicationController.php` | `/v1/job-offers` | `/api/v1/admin/job-offers` |
| `src/Recruit/Transport/Controller/Api/V1/JobOffer/JobOfferController.php` | `/v1/job-offers` | `/api/v1/admin/job-offers` |
| `src/Recruit/Transport/Controller/Api/V1/Offer/OfferController.php` | `/v1/offers` | `/api/v1/admin/offers` |
| `src/Recruit/Transport/Controller/Api/V1/Resume/ResumeController.php` | `/v1/resumes` | `/api/v1/admin/resumes` |
| `src/Recruit/Transport/Controller/Api/V1/Resume/ResumeEducationController.php` | `/v1/resume-education` | `/api/v1/admin/resume-education` |
| `src/Recruit/Transport/Controller/Api/V1/Resume/ResumeExperienceController.php` | `/v1/resume-experiences` | `/api/v1/admin/resume-experiences` |
| `src/Recruit/Transport/Controller/Api/V1/Resume/ResumeSkillController.php` | `/v1/resume-skills` | `/api/v1/admin/resume-skills` |
| `src/Statistic/Transport/Controller/Api/V1/Statistic/StatisticController.php` | `/v1/statistics` | `/api/v1/admin/statistics` |
| `src/Task/Transport/Controller/Api/V1/Project/ProjectController.php` | `/v1/projects` | `/api/v1/admin/projects` |
| `src/Task/Transport/Controller/Api/V1/Project/ProjectTasksController.php` | `/v1/projects` | `/api/v1/admin/projects` |
| `src/Task/Transport/Controller/Api/V1/Sprint/SprintController.php` | `/v1/sprints` | `/api/v1/admin/sprints` |
| `src/Task/Transport/Controller/Api/V1/Task/TaskController.php` | `/v1/tasks` | `/api/v1/admin/tasks` |
| `src/Task/Transport/Controller/Api/V1/Task/TaskRequestsController.php` | `/v1/tasks` | `/api/v1/admin/tasks` |
| `src/Task/Transport/Controller/Api/V1/TaskRequest/TaskRequestController.php` | `/v1/task-requests` | `/api/v1/admin/task-requests` |
