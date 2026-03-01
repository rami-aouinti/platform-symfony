# Swagger
Ce document explique comment utiliser [Swagger](https://swagger.io/) dans le projet, et comment contribuer à une documentation OpenAPI homogène.

## Utiliser Swagger en local
* [Service Swagger local](http://localhost/api/doc) - Ouvrir l’URL `http://localhost/api/doc`.

## Convention de tags
Les tags doivent être normalisés pour rendre l’API lisible par audience:

* `Admin - <Module>` : endpoints d’administration d’un module (rôle minimum explicite, ex. `ROLE_ADMIN`).
* `Me - <Module>` : endpoints centrés sur l’utilisateur courant (`/me`, `/profile`, etc., rôle minimum `IS_AUTHENTICATED_FULLY`).

Exemples:
* `Admin - User`
* `Me - Profile`

## Convention d’annotations des opérations
Chaque endpoint V1 doit déclarer explicitement:

* l’opération HTTP via `OA\Get`, `OA\Post`, `OA\Put`, `OA\Patch`, `OA\Delete`;
* `summary` et `description`;
* `security` (Bearer / ApiKey) ;
* `responses` standards.

Dans `description`, indiquer systématiquement:
* audience cible ;
* rôle minimal ;
* périmètre des données.

## Réponses réutilisables
Utiliser les composants réutilisables déclarés dans `nelmio_api_doc.yaml`:

* `#/components/responses/UnauthorizedError` (401)
* `#/components/responses/ForbiddenError` (403)
* `#/components/responses/NotFoundError` (404)

Et la contrainte d’accès partagée:

* `#/components/schemas/AccessConstraint`

## Endpoints REST génériques (traits)
Pour les contrôleurs basés sur les traits REST génériques (`Actions\Admin\*`, `Actions\Authenticated\*`):

* annoter les méthodes exposées directement dans les traits pour propager une documentation homogène à tous les contrôleurs consommateurs ;
* inclure des exemples de payload pour `create`, `update`, `patch` ;
* inclure des exemples de critères de filtrage (`where`, `search`, `order`, `limit`, `offset`) sur les endpoints de liste.
