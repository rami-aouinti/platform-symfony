# Task module

## Matrice de règles d'accès

Cette matrice sert de référence unique pour les contrôles d'accès côté `Task` (resources + use cases).

| Action | Admin (`ROLE_ADMIN`/`ROLE_ROOT`) | Task owner | Requester | Reviewer | Project owner |
|---|---|---|---|---|---|
| `canViewTask` | ✅ | ✅ | ❌ | ❌ | ✅ |
| `canManageTask` | ✅ | ✅ | ❌ | ❌ | ✅ |
| `canViewTaskRequest` | ✅ | ✅ (via task) | ✅ | ✅ | ✅ (via task/project) |
| `canReviewTaskRequest` | ✅ | ✅ (via task) | ❌ | ✅ | ✅ (via task/project) |
| `scopeTasksQuery` | pas de filtre | `owner = currentUser` | `owner = currentUser` | `owner = currentUser` | `owner = currentUser` |
| `scopeTaskRequestsQuery` | pas de filtre | `requester = currentUser` | `requester = currentUser` | `requester = currentUser` | `requester = currentUser` |

## Notes d'implémentation

- Les règles sont centralisées dans `TaskAccessService` (policy applicative).
- Les resources `ProjectResource`, `TaskResource`, `TaskRequestResource`, `SprintResource` délèguent désormais leurs décisions à cette policy (directement ou via use case).
- Toute évolution des rôles doit mettre à jour **cette matrice** et les tests associés pour éviter les régressions.
