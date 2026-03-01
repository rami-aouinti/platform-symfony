# Swagger conventions (API V1)

## Convention de nommage des tags

- Back-office / administration: `Admin - {Module}`
  - Exemples: `Admin - User`, `Admin - Company`, `Admin - Task`.
- Endpoints liés à l'utilisateur connecté: `Me - {Module}`
  - Exemples: `Me - Profile`, `Me - Notification`.
- Le `{Module}` doit rester stable et correspondre au bounded context métier.

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

## Règles de documentation des permissions

- Chaque endpoint doit préciser dans `description`:
  - audience (`Admin`, `Me`, `Authenticated`, `Anonymous`),
  - rôle minimum requis,
  - périmètre des données exposées.
- La section `security` doit expliciter les schémas supportés (`Bearer` et/ou `ApiKey`).
- Les endpoints publics doivent documenter explicitement l'absence d'authentification.
- Lorsqu'un endpoint dépend de permissions fines (voter, ACL, ownership), ajouter un encart dans `description` avec les règles de décision.
