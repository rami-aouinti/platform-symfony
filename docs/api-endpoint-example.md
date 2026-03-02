# Exemple complet de documentation d'un endpoint API

Ce guide montre comment documenter un endpoint existant de manière homogène avec Swagger/OpenAPI et les conventions du projet.

## Endpoint choisi
- **Contrôleur**: `App\User\Transport\Controller\Api\V1\Profile\IndexController::patchAddressAction`
- **Chemin**: `PATCH /api/v1/me/profile/addresses/{addressId}`
- **Audience**: `Me` (utilisateur authentifié sur ses propres données).

## 1) Annotations du contrôleur

### Annotation Symfony `#[Route(...)]`
```php
#[Route(
    path: '/v1/me/profile/addresses/{addressId}',
    requirements: [
        'addressId' => Requirement::UUID_V1,
    ],
    methods: [Request::METHOD_PATCH],
)]
#[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
```

Points clés :
- `addressId` est un paramètre **path** obligatoire, typé UUID.
- Le endpoint est protégé par `IS_AUTHENTICATED_FULLY`.
- Le périmètre est limité aux adresses de l'utilisateur connecté (ownership).

### Annotation OpenAPI (`OA\Patch`) recommandée
Exemple d'annotation OpenAPI complète (style utilisé dans le module `Me - Profile`) :

```php
#[OA\Patch(
    summary: 'Mettre à jour une adresse du profil courant',
    description: 'Audience cible: utilisateurs connectés (Me). Rôle minimal: IS_AUTHENTICATED_FULLY. Périmètre: adresses du profil de l'utilisateur authentifié uniquement (ownership strict).',
    tags: ['Me - Profile'],
    security: [[
        'Bearer' => [],
    ], [
        'ApiKey' => [],
    ]],
)]
```

## 2) Security

Section `security` recommandée :

```php
security: [[
    'Bearer' => [],
], [
    'ApiKey' => [],
]]
```

Cela indique que l'endpoint accepte :
- un JWT Bearer valide, **ou**
- une API key valide,
avec utilisateur final authentifié (`IS_AUTHENTICATED_FULLY`).

## 3) Paramètres

### a) Paramètre `path`
- `addressId` (string, format UUID)
- Obligatoire.

### b) Paramètres `query`
- **Aucun paramètre query** sur cet endpoint.
- Bonne pratique : le préciser explicitement dans la doc pour éviter les ambiguïtés côté consommateurs.

### c) Corps de requête (`requestBody`)
Exemple réaliste :

```json
{
  "type": "billing",
  "streetLine1": "10 rue de Rivoli",
  "streetLine2": "Bâtiment A",
  "postalCode": "75004",
  "city": "Paris",
  "region": "Île-de-France",
  "countryCode": "FR"
}
```

## 4) Réponses documentées (avec exemples JSON)

### `200 OK`
```json
{
  "id": "0195f8f8-9c2e-7c2f-9d91-43ed0d2f7a4f",
  "username": "ada.lovelace",
  "email": "ada.lovelace@example.com",
  "userProfile": {
    "addresses": [
      {
        "id": "0195f901-8c8f-75d9-9f59-9626ecf9e18a",
        "type": "billing",
        "streetLine1": "10 rue de Rivoli",
        "streetLine2": "Bâtiment A",
        "postalCode": "75004",
        "city": "Paris",
        "region": "Île-de-France",
        "countryCode": "FR"
      }
    ]
  }
}
```

### `401 Unauthorized`
```json
{
  "code": 401,
  "message": "JWT Token not found"
}
```

### `403 Forbidden`
```json
{
  "code": 403,
  "message": "Access Denied."
}
```

### `404 Not Found` (ownership / ressource absente)
```json
{
  "code": 404,
  "message": "Address not found for current user."
}
```

### `422 Unprocessable Entity` (erreur métier/validation)
```json
{
  "code": 422,
  "message": "Validation failed.",
  "errors": {
    "type": [
      "The value must be one of: billing, shipping."
    ]
  }
}
```

## 5) Exemple HTTP (curl)

### Requête
```bash
curl -X PATCH 'http://localhost/api/v1/me/profile/addresses/0195f901-8c8f-75d9-9f59-9626ecf9e18a' \
  -H 'Authorization: Bearer <jwt>' \
  -H 'Content-Type: application/json' \
  -d '{
    "type": "billing",
    "streetLine1": "10 rue de Rivoli",
    "postalCode": "75004",
    "city": "Paris",
    "countryCode": "FR"
  }'
```

### Réponse succès
```json
{
  "id": "0195f8f8-9c2e-7c2f-9d91-43ed0d2f7a4f",
  "username": "ada.lovelace",
  "userProfile": {
    "addresses": [
      {
        "id": "0195f901-8c8f-75d9-9f59-9626ecf9e18a",
        "city": "Paris"
      }
    ]
  }
}
```

### Réponse erreur métier
```json
{
  "code": 422,
  "message": "Validation failed.",
  "errors": {
    "countryCode": [
      "This value should be a valid ISO 3166-1 alpha-2 country code."
    ]
  }
}
```

## 6) Alignement avec `docs/swagger-conventions.md`

Cet exemple respecte les conventions du projet :
- **Tag** : `Me - Profile` (format `Me - X`).
- **Audience** : utilisateur connecté (`Me`).
- **Rôle minimal** : `IS_AUTHENTICATED_FULLY`.
- **Ownership** : accès strict aux données du profil courant, jamais à celles d'un autre utilisateur.
- **Réponses standards** : `401`, `403`, `404`, `422` + payloads JSON réalistes.

> Pour les endpoints back-office, appliquer le même schéma avec des tags `Admin - X` et un rôle minimal explicite (`ROLE_ADMIN`, `ROLE_ROOT`, etc.).
