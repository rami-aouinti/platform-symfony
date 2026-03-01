# Roadmap API Audience

## Objectif
Structurer l'API par audience (`admin`, `me`, `public`) en garantissant un niveau homogène de sécurité, de documentation et de couverture de tests sur l'ensemble des modules métiers.

## Epics

### Epic 1 — Segmentation des routes
- Formaliser la convention de nommage et de préfixage des routes par audience.
- Cartographier les endpoints existants et identifier les incohérences de segmentation.
- Mettre en conformité les routes pour distinguer clairement les usages `admin`, `me` et `public`.

### Epic 2 — Durcissement des autorisations
- Vérifier les contrôles d'accès associés à chaque endpoint.
- Ajouter des gardes explicites par rôle/audience (notamment `admin`).
- Éliminer les expositions de données globales depuis les endpoints destinés à `me`.

### Epic 3 — Documentation Swagger détaillée
- Documenter chaque opération API dans OpenAPI/Swagger.
- Renseigner les schémas de sécurité, les réponses de succès/erreur et des exemples utiles.
- Harmoniser les descriptions et conventions documentaires entre modules.

### Epic 4 — Tests de non-régression
- Créer/mettre à jour des tests fonctionnels couvrant la segmentation d'audience.
- Vérifier les cas autorisés/interdits pour `admin`, `me` et `public`.
- Ajouter des assertions sur les erreurs d'autorisation (401/403) et de validation métier.

## Plan standard par module

Chaque module suit les tâches suivantes :
1. inventaire endpoints actuels,
2. classification `admin` / `me` / `public`,
3. adaptation route + sécurité,
4. documentation OpenAPI,
5. tests fonctionnels.

| Module | T1 Inventaire endpoints | T2 Classification admin/me/public | T3 Adaptation route + sécurité | T4 Documentation OpenAPI | T5 Tests fonctionnels |
|---|---|---|---|---|---|
| ApiKey | ☐ | ☐ | ☐ | ☐ | ☐ |
| Blog | ☐ | ☐ | ☐ | ☐ | ☐ |
| Calendar | ☐ | ☐ | ☐ | ☐ | ☐ |
| Chat | ☐ | ☐ | ☐ | ☐ | ☐ |
| Company | ☐ | ☐ | ☐ | ☐ | ☐ |
| Configuration | ☐ | ☐ | ☐ | ☐ | ☐ |
| Media | ☐ | ☐ | ☐ | ☐ | ☐ |
| Notification | ☐ | ☐ | ☐ | ☐ | ☐ |
| Quiz | ☐ | ☐ | ☐ | ☐ | ☐ |
| Recruit | ☐ | ☐ | ☐ | ☐ | ☐ |
| Role | ☐ | ☐ | ☐ | ☐ | ☐ |
| Statistic | ☐ | ☐ | ☐ | ☐ | ☐ |
| Task | ☐ | ☐ | ☐ | ☐ | ☐ |
| User | ☐ | ☐ | ☐ | ☐ | ☐ |

## Critères d'acceptation uniformes
- Aucun endpoint `admin` sans garde explicite `admin`.
- Aucun endpoint `me` exposant des données globales.
- Chaque opération visible dans Swagger avec :
  - sécurité déclarée,
  - réponses d'erreur documentées,
  - exemple de payload (requête et/ou réponse).
