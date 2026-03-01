# Matrice d'accès transverse par module

Référence transverse (legacy `/api/v1/*`) alignée sur la convention d'audience (`admin` / `me`) et le modèle du module Task.

## Légende

- **Audience**: `admin` (transverse), `me/profile` (self-service), `mixte` (les deux existent selon action).
- **Rôle requis**: rôle technique attendu au niveau endpoint (`ROLE_ADMIN`, `ROLE_ROOT`) ou permission métier (voter/service).
- **Ownership**: règle de filtrage basée sur l'utilisateur courant.
- **Voter/service**: composant principal qui porte la décision d'accès.

## ApiKey

| endpoint | action | audience | rôle requis | règle de ownership | voter/service |
|---|---|---|---|---|---|
| `/api/v1/api_key` | list/create | mixte | `ROLE_ADMIN` (global) / user connecté (self) | en `me`: `apiKey.user = currentUser` | `ApiKeyResource` + couche sécurité Symfony |
| `/api/v1/api_key/{id}` | read/update/delete | mixte | `ROLE_ADMIN` ou owner | owner sur les routes `me` | `ApiKeyResource` |

## Blog

| endpoint | action | audience | rôle requis | règle de ownership | voter/service |
|---|---|---|---|---|---|
| `/api/v1/blog-posts` | CRUD/list | mixte | `ROLE_ADMIN` (admin) + permissions métier (me) | auteur = currentUser ou visibilité liée | `Blog*Resource` |
| `/api/v1/blog-comments` | CRUD/list | mixte | `ROLE_ADMIN` (admin) + permission métier | auteur = currentUser sur `me` | `Blog*Resource` |
| `/api/v1/blog-tags`, `/blog-post-links` | read/write | mixte | `ROLE_ADMIN` pour opérations globales | n/a ou lié au post de l'utilisateur | `Blog*Resource` |
| `/api/v1/tasks/{id}/blog-posts` | list par task | mixte | permission métier Task | accès à la task selon policy task | `TaskAccessService` + bridge Blog |
| `/api/v1/task-requests/{id}/blog-posts` | list par request | mixte | permission métier TaskRequest | accès à la request selon policy task | `TaskAccessService` + bridge Blog |

## Calendar

| endpoint | action | audience | rôle requis | règle de ownership | voter/service |
|---|---|---|---|---|---|
| `/api/v1/calendar/events` | CRUD/list | mixte | `ROLE_ADMIN` (vue globale) / permissions métier | en `me`: participant/owner = currentUser | `EventResource` |

## Chat

| endpoint | action | audience | rôle requis | règle de ownership | voter/service |
|---|---|---|---|---|---|
| `/api/v1/chat/conversations` | list/create | mixte | `ROLE_ADMIN` (global) / user connecté (self) | membre de conversation = currentUser | `Conversation*` service + voter conversation |
| `/api/v1/chat/conversations/{id}` | read/update | mixte | `ROLE_ADMIN` ou membre | membre = currentUser | voter conversation |
| `/api/v1/chat/conversations/{id}/messages` | list/create | mixte | `ROLE_ADMIN` ou membre | membre = currentUser | voter conversation/message |

## Company

| endpoint | action | audience | rôle requis | règle de ownership | voter/service |
|---|---|---|---|---|---|
| `/api/v1/companies` | CRUD/list | mixte | `ROLE_ADMIN` (global) + permission métier | en `me`: company membership = currentUser | `Company*Resource` + permission service |
| `/api/v1/companies/{id}/projects` | list/create | mixte | permission métier company/project | membership dans company | `CompanyPermission*` |
| `/api/v1/companies/{id}/sprints` | list/create | mixte | permission métier company/sprint | membership dans company | `CompanyPermission*` |
| `/api/v1/companies/{id}/members` | list/manage | mixte | `ROLE_ADMIN` ou manage company | company owner/admin local | `CompanyPermission*` |

## Configuration

| endpoint | action | audience | rôle requis | règle de ownership | voter/service |
|---|---|---|---|---|---|
| `/api/v1/configuration`, `/configurations` | read/update | mixte | `ROLE_ADMIN` (global) / utilisateur connecté (me) | en `me`: paramètres du currentUser | `ConfigurationResource` |

## Media

| endpoint | action | audience | rôle requis | règle de ownership | voter/service |
|---|---|---|---|---|---|
| `/api/v1/media` | upload/list | mixte | `ROLE_ADMIN` (global) + `ROLE_USER`/permission métier (self) | en `me`: `media.owner = currentUser` | `MediaResource` + voter media |
| `/api/v1/media/{id}` | read/delete | mixte | `ROLE_ADMIN` ou owner | owner = currentUser | voter media |

## Notification

| endpoint | action | audience | rôle requis | règle de ownership | voter/service |
|---|---|---|---|---|---|
| `/api/v1/notifications` | list | me/profile | utilisateur connecté | `notification.user = currentUser` | `NotificationResource` |
| `/api/v1/notifications/{id}/read|unread` | mutation état | me/profile | utilisateur connecté | owner uniquement | `NotificationResource` |
| `/api/v1/notifications/read-all`, `/unread-count` | agrégats self | me/profile | utilisateur connecté | owner uniquement | `NotificationResource` |

## Quiz

| endpoint | action | audience | rôle requis | règle de ownership | voter/service |
|---|---|---|---|---|---|
| `/api/v1/quizzes` | CRUD/list | mixte | `ROLE_ADMIN` (global) + permission métier | auteur/participant = currentUser en `me` | `QuizResource` |

## Recruit

| endpoint | action | audience | rôle requis | règle de ownership | voter/service |
|---|---|---|---|---|---|
| `/api/v1/job-offers` | list/create/update | mixte | `ROLE_ADMIN` (global) + permissions métier recrutement | `/my` ou `me`: recruteur/candidat = currentUser | `JobOffer*` + voter métier |
| `/api/v1/job-applications` | list/create/decision | mixte | permission métier recrutement (`accept/reject` admin/recruteur) | candidat/recruteur = currentUser | `JobApplication*` + voter métier |
| `/api/v1/offers`, `/candidates` | list/manage | mixte | `ROLE_ADMIN` prioritaire | en `me`: owner courant | `OfferResource`, `CandidateResource` |
| `/api/v1/resumes` + sous-ressources | CRUD/list | mixte | `ROLE_ADMIN` (global) / user connecté (self) | CV owner = currentUser | `Resume*` + `ResumeVoter` |

## Role

| endpoint | action | audience | rôle requis | règle de ownership | voter/service |
|---|---|---|---|---|---|
| `/api/v1/role` | list/count/ids/schema | admin | `ROLE_ADMIN` | n/a | `RoleResource` |
| `/api/v1/role/{role}` | read one | admin | `ROLE_ADMIN` | n/a | `RoleResource` |
| `/api/v1/role/{role}/inherited` | inherited roles | admin | `ROLE_ADMIN` | n/a | `RolesServiceInterface` |

## Statistic

| endpoint | action | audience | rôle requis | règle de ownership | voter/service |
|---|---|---|---|---|---|
| `/api/v1/statistics/overview` | dashboard summary | admin | `ROLE_ADMIN` | n/a | `StatisticService` |
| `/api/v1/statistics/entities` | entity counters | admin | `ROLE_ADMIN` | n/a | `StatisticService` |
| `/api/v1/statistics/timeseries` | global timeseries | admin | `ROLE_ADMIN` | n/a | `StatisticService` |
| `/api/v1/statistics/timeseries/{entity}` | entity timeseries | admin | `ROLE_ADMIN` | n/a | `StatisticService` |
| `/api/v1/statistics/distributions/statuses` | statuses distribution | admin | `ROLE_ADMIN` | n/a | `StatisticService` |

## Task

| endpoint | action | audience | rôle requis | règle de ownership | voter/service |
|---|---|---|---|---|---|
| `/api/v1/tasks` | CRUD/list | mixte | `ROLE_ADMIN` (global) + permissions métier task | owner/requester/reviewer/projectOwner = currentUser | `TaskAccessService` |
| `/api/v1/tasks/{id}/task-requests` | list/create | mixte | permission métier task/request | lié à la task de l'utilisateur | `TaskAccessService` |
| `/api/v1/task-requests` | CRUD/list/actions review | mixte | permission métier task request | requester/reviewer liés à currentUser | `TaskAccessService` |
| `/api/v1/projects` + `/sprints` | CRUD/list | mixte | `ROLE_ADMIN` (global) + permission métier | project/sprint membership en `me` | `TaskAccessService` |

## Tool (Localization)

| endpoint | action | audience | rôle requis | règle de ownership | voter/service |
|---|---|---|---|---|---|
| `/api/v1/localization/language|locale|timezone` | list références | mixte | utilisateur connecté (ou ouvert selon config) | n/a | contrôleurs localization |

## User

| endpoint | action | audience | rôle requis | règle de ownership | voter/service |
|---|---|---|---|---|---|
| `/api/v1/admin/users` | list/count/ids/schema | admin | `ROLE_ADMIN` | n/a | `UserResource` |
| `/api/v1/admin/users` | create/update/patch/delete | admin | `ROLE_ROOT` | n/a | `UserResource` |
| `/api/v1/admin/users/{user}/roles`, `/groups` | read lié utilisateur | admin | `ROLE_ROOT` ou `ROLE_ADMIN` selon endpoint | n/a | `IS_USER_HIMSELF` non utilisé en admin |
| `/api/v1/user_group*` | group management | admin | `ROLE_ADMIN`/`ROLE_ROOT` selon mutation | n/a | `UserGroupResource` |
| `/api/v1/me/profile*` | self profile (profil, avatar, adresses) | profile | utilisateur connecté | currentUser uniquement | contrôleurs `Profile/*` |
| `/api/v1/me/social-accounts*` | social accounts (list/link/unlink) | profile | utilisateur connecté | currentUser uniquement | `SocialAccountsController` |

## Auth

| endpoint | action | audience | rôle requis | règle de ownership | voter/service |
|---|---|---|---|---|---|
| `/api/v1/auth/get_token` | login | public | aucune | n/a | sécurité Symfony |
| `/api/v1/auth/social-connect` | social auth | public | aucune | n/a | sécurité Symfony |
