# Swagger conventions (API V1)

## Convention de nommage des tags (par audience)

- Administration / back-office: `Admin - {Module}`
  - Exemples: `Admin - User`, `Admin - Company Management`, `Admin - Task Management`.
- Self-service utilisateur connecté: `Me/Profile - {Module}`
  - Exemples: `Me/Profile - Profile`, `Me/Profile - Project`, `Me/Profile - Resume`.
- Endpoints frontend génériques (app/web/mobile, non admin): `Frontend - {Module}`
  - Exemples: `Frontend - Authentication`, `Frontend - Chat`, `Frontend - Catalog Skill`.
- Le suffixe `{Module}` doit rester stable et correspondre au bounded context métier.

## Groupes de documentation OpenAPI

La documentation est segmentée pour éviter le mélange Admin vs Frontend:

- **Global**: `/api/doc` et `/api/doc.json`
- **Admin**: `/api/doc/admin` et `/api/doc/admin.json`
- **Frontend (incluant Me/Profile)**: `/api/doc/frontend` et `/api/doc/frontend.json`

Règle de filtrage:

- section `admin`: tags préfixés `Admin - ...`
- section `frontend`: tags préfixés `Frontend - ...` et `Me/Profile - ...`

## Format des exemples request / response

- Fournir des exemples JSON réalistes (UUID v7, dates ISO-8601, statuts métier valides).
- Les payloads d'écriture (POST/PUT/PATCH) doivent inclure:
  - un exemple minimal valide,
  - un exemple complet (quand pertinent).
- Les réponses de succès utilisent:
  - `200` pour lecture/mise à jour/suppression logique,
  - `201` pour création.
- Les erreurs standardisées documentées par endpoint:
  - `400` requête invalide,
  - `401` authentification manquante/invalide,
  - `403` accès refusé,
  - `404` ressource introuvable,
  - `422` violation des règles métier/validation.

## Checklist de validation par endpoint

Chaque endpoint doit explicitement documenter:

- [ ] **Audience**: `Admin`, `Me/Profile`, ou `Frontend` (via tag `... - {Module}`).
- [ ] **Sécurité**: schéma(s) `Bearer` et/ou `ApiKey` + endpoint public explicite si aucun.
- [ ] **Cas d'usage Admin**: ce qu'un opérateur/back-office peut faire et voir.
- [ ] **Cas d'usage Frontend**: parcours utilisateur couvert côté app/web/mobile.
- [ ] **Périmètre des données**: global (admin) vs restreint au user connecté (me/profile).
- [ ] **Rôle minimum**: rôle Symfony ou contrainte d'accès (ACL/voter/ownership).
- [ ] **Erreurs métier attendues**: au minimum `400/401/403/404/422` selon pertinence.

## Règles de documentation des permissions

- Chaque endpoint doit préciser dans `description`:
  - audience (`Admin`, `Me/Profile`, `Frontend`, `Authenticated`, `Anonymous`),
  - rôle minimum requis,
  - périmètre des données exposées.
- La section `security` doit expliciter les schémas supportés (`Bearer` et/ou `ApiKey`).
- Les endpoints publics doivent documenter explicitement l'absence d'authentification.
- Lorsqu'un endpoint dépend de permissions fines (voter, ACL, ownership), ajouter un encart dans `description` avec les règles de décision.
