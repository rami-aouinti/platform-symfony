# Roadmap Endpoints Admin/Me

## Objectif
Structurer la migration/harmonisation des endpoints par audience (`admin` vs `me`) par lots fonctionnels, avec une couverture documentaire et de sécurité complète.

---

## Lot 1 — Recruit + User + Company (domaines les plus riches)

### 1. Inventaire endpoints existants
- Recenser tous les endpoints des modules **Recruit**, **User** et **Company**.
- Produire une matrice par endpoint:
  - Méthode HTTP
  - URI
  - Contrôleur/action
  - Audience actuelle (admin, utilisateur connecté, public, mixte)
  - Voter/permission réellement appliqué(e)

### 2. Endpoints manquants à créer
- Identifier les cas d’usage non couverts côté admin et côté me.
- Ajouter les endpoints nécessaires pour couvrir:
  - lecture liste/détail,
  - création,
  - mise à jour,
  - suppression/archivage,
  - actions métier spécifiques.

### 3. Endpoints à scinder admin/me
- Lister les endpoints “mixtes” avec logique conditionnelle selon le rôle.
- Scinder vers des routes explicites:
  - `/admin/...` pour usages back-office,
  - `/me/...` pour usages self-service utilisateur.
- Harmoniser les payloads, statuts HTTP et règles d’autorisation.

### 4. Annotations Swagger à compléter
- Compléter toutes les annotations OpenAPI/Swagger:
  - summary/description,
  - paramètres path/query,
  - schémas request/response,
  - exemples,
  - codes d’erreur (`400`, `401`, `403`, `404`, `409`, `422`, `500` selon contexte).

### 5. Tests sécurité/régression
- Écrire/compléter les tests d’accès par profil:
  - anonyme,
  - user standard,
  - admin.
- Ajouter des tests de non-régression fonctionnelle sur les endpoints scindés.

---

## Lot 2 — Chat + Notification + Media

### 1. Inventaire endpoints existants
- Cartographier les endpoints de **Chat**, **Notification** et **Media**.
- Identifier ceux exposés en lecture/écriture et leurs audiences actuelles.

### 2. Endpoints manquants à créer
- Ajouter les endpoints absents, notamment pour:
  - gestion des conversations/messages (chat),
  - lecture/acknowledge/paramétrage (notification),
  - upload/consultation/suppression (media).

### 3. Endpoints à scinder admin/me
- Séparer les endpoints supervisés/admin de ceux en usage utilisateur.
- Clarifier les routes et la granularité des droits (accès à ses ressources vs accès global).

### 4. Annotations Swagger à compléter
- Documenter exhaustivement les contrats API, y compris les erreurs métiers.
- Ajouter des exemples réalistes (fichiers media, pagination, filtres, statuts de lecture).

### 5. Tests sécurité/régression
- Tests d’accès croisés (isolation des données entre utilisateurs).
- Tests de régression sur flux critiques (chat temps réel simulé, notifications, media).

---

## Lot 3 — Task + Blog + Calendar + Quiz

### 1. Inventaire endpoints existants
- Recenser les endpoints des modules **Task**, **Blog**, **Calendar**, **Quiz**.
- Distinguer endpoints CRUD, endpoints workflow, endpoints de consultation.

### 2. Endpoints manquants à créer
- Compléter les manques pour chaque module:
  - Task: assignation, statut, priorisation,
  - Blog: publication, modération, versionning,
  - Calendar: événements, invitations, disponibilités,
  - Quiz: sessions, réponses, résultats.

### 3. Endpoints à scinder admin/me
- Extraire les opérations de pilotage/admin des opérations self-service.
- Éviter les endpoints “fourre-tout” conditionnés par rôle à l’exécution.

### 4. Annotations Swagger à compléter
- Compléter la doc OpenAPI sur les workflows (transitions d’état, validations).
- Standardiser les schémas de réponse et erreurs entre modules.

### 5. Tests sécurité/régression
- Vérifier les contrôles d’accès par audience et par ownership.
- Mettre en place des scénarios de régression multi-modules.

---

## Lot 4 — ApiKey + Configuration + Role + Statistic + harmonisation finale

### 1. Inventaire endpoints existants
- Auditer les endpoints de **ApiKey**, **Configuration**, **Role**, **Statistic**.
- Identifier les incohérences de conventions (naming, pagination, erreurs).

### 2. Endpoints manquants à créer
- Ajouter les endpoints nécessaires pour administration complète:
  - cycle de vie des clés API,
  - configuration applicative,
  - gestion des rôles/permissions,
  - endpoints de statistiques et exports.

### 3. Endpoints à scinder admin/me
- Isoler strictement les surfaces d’administration.
- Réserver les endpoints `/me` aux informations/paramètres strictement personnels.

### 4. Annotations Swagger à compléter
- Finaliser la documentation OpenAPI transverse:
  - conventions communes,
  - composants partagés,
  - uniformisation des réponses d’erreur.

### 5. Tests sécurité/régression
- Vérifier l’absence d’accès administrateur via droits insuffisants.
- Lancer une campagne de régression globale post-harmonisation.

---

## Done Criteria

- **100% des endpoints classés par audience** (`admin`, `me`, `public`, `interne`).
- **100% des endpoints documentés OpenAPI**, y compris les cas d’erreur.
- **0 endpoint admin protégé uniquement par `IS_AUTHENTICATED_FULLY`** (permissions/voters explicites obligatoires).
- **Tests d’accès verts pour chaque module du lot** avant clôture.
