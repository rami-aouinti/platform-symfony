# Module Media

## Objectif
Le module `App\Media\...` centralise la gestion des médias utilisateurs (CRUD + upload), avec des conventions de sécurité alignées sur le framework interne.

## Routes API
Préfixe commun : `/api/v1/media`

### CRUD REST (contrôleur standard)
- `GET /api/v1/media` : liste des médias
- `GET /api/v1/media/{id}` : détail d’un média
- `POST /api/v1/media` : création manuelle d’un média (payload JSON)
- `PUT /api/v1/media/{id}` : remplacement complet
- `PATCH /api/v1/media/{id}` : mise à jour partielle
- `DELETE /api/v1/media/{id}` : suppression d’un média

### Upload
- `POST /api/v1/media/upload`
- Authentification requise (`IS_AUTHENTICATED_FULLY` + `ROLE_USER`)
- Content-Type : `multipart/form-data`
- Champ attendu : `file` (fichier uploadé)

## Payloads

### `POST /api/v1/media` (JSON)
```json
{
  "name": "avatar.png",
  "path": "/uploads/media/2026/06/<owner-id>/avatar-uuid.png",
  "mimeType": "image/png",
  "size": 1024,
  "status": "active"
}
```

### `POST /api/v1/media/upload` (multipart)
- `file`: binaire

Réponse : entité `Media` sérialisée (groupes `Media.show`/`Media.edit` selon le contexte de réponse).

## Validation
- `Media` DTO (REST JSON):
  - `name`: requis, longueur 2..255
  - `path`: requis, longueur 2..1024
  - `mimeType`: requis, longueur 3..255
  - `size`: requis, entier >= 0
  - `status`: requis, longueur 2..64
- Upload:
  - `file` obligatoire
  - taille max : `10M`
  - validation type via contrainte Symfony `Assert\File`

## Sécurité
- Contrôleur protégé par `#[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]`.
- Upload protégé en plus par `#[IsGranted('ROLE_USER')]`.
- Ownership checks dans `MediaResource` :
  - Admin (`ROLE_ADMIN`, `ROLE_ROOT`) : accès global
  - Utilisateur standard : accès restreint à ses propres médias
- Les opérations `find`, `findOne`, `update`, `patch`, `delete` appliquent ces contrôles.

## Stockage
Le provider de stockage est encapsulé dans le service applicatif :
- Interface: `App\Media\Application\Service\Interfaces\MediaStorageServiceInterface`
- Implémentation locale: `App\Media\Infrastructure\Service\MediaStorageService`

### Comportement implémentation locale
- Dossier cible : `${MEDIA_STORAGE_PATH}/YYYY/MM/<owner-id|anonymous>`
- Nom de fichier : slug du nom original + UUID v7
- Chemin public enregistré en base : `${MEDIA_STORAGE_PUBLIC_PREFIX}/YYYY/MM/<owner>/filename.ext`
- Suppression physique tentée lors d’un `DELETE` logique du média.

## Variables d’environnement
Ajouter/configurer :
- `MEDIA_STORAGE_PATH` (ex: `%kernel.project_dir%/public/uploads/media`)
- `MEDIA_STORAGE_PUBLIC_PREFIX` (ex: `/uploads/media`)

## Erreurs fréquentes
- `400 Bad Request`
  - fichier manquant (`file` absent)
  - validation upload échouée (ex: taille > 10MB)
- `403 Forbidden`
  - utilisateur non propriétaire d’un média
  - utilisateur non authentifié / rôle insuffisant
- `404 Not Found`
  - média inexistant
