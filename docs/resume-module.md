# Resume Module — Documentation API complète (backend Symfony)

> Objectif: fournir une base claire pour créer un ticket frontend **Nuxt 4 + Vuetify 3**.

## 1) Vue d’ensemble fonctionnelle

Le module `Resume` est découpé en 4 ressources REST:

1. **Resume** (`/api/v1/resumes`) : CV principal d’un utilisateur.
2. **ResumeExperience** (`/api/v1/resume-experiences`) : expériences pro liées à un CV.
3. **ResumeEducation** (`/api/v1/resume-education`) : formations liées à un CV.
4. **ResumeSkill** (`/api/v1/resume-skills`) : compétences liées à un CV.

Tous les endpoints sont protégés par authentification (utilisateur connecté). Les règles métier principales:
- création d’un resume: autorisée utilisateur authentifié;
- édition/suppression: propriétaire uniquement (ou rôles globaux admin/root);
- lecture d’un resume: propriétaire, admin/root, ou tout utilisateur si `isPublic = true`.

## 2) Authentification et conventions HTTP

- **Base URL API**: `/api`
- **Content-Type**: `application/json`
- **Auth**: header `Authorization: Bearer <token>`

Exemple d’entêtes côté frontend:

```http
Authorization: Bearer eyJ... 
Content-Type: application/json
X-Requested-With: XMLHttpRequest
```

## 3) Modèle de données (payloads)

## 3.1 Resume

Champs utilisés:
- `title` (string, requis, 2..255)
- `summary` (string, requis, 10..10000)
- `experiences` (array d’objets)
- `education` (array d’objets)
- `skills` (array de string)
- `links` (array d’objets `{label, url}`)
- `isPublic` (bool)

Exemple payload create/update:

```json
{
  "title": "Senior Backend Engineer",
  "summary": "8+ ans d'expérience Symfony, API Platform, architecture DDD.",
  "experiences": [
    {
      "company": "ACME",
      "position": "Backend Engineer",
      "startDate": "2022-01-01",
      "endDate": "2024-05-31",
      "description": "Conception APIs RH"
    }
  ],
  "education": [
    {
      "institution": "Université de Tunis",
      "degree": "Master Informatique",
      "startDate": "2016-09-01",
      "endDate": "2018-06-30"
    }
  ],
  "skills": ["PHP", "Symfony", "Docker"],
  "links": [
    {
      "label": "GitHub",
      "url": "https://github.com/example"
    }
  ],
  "isPublic": true
}
```

## 3.2 ResumeExperience

Champs:
- `resume` (UUID du resume cible)
- `title` (string, requis)
- `companyName` (string, requis)
- `employmentType` (enum)
- `startDate` (date, requis)
- `endDate` (date|null)
- `isCurrent` (bool)
- `location` (string|null)
- `description` (string|null)
- `sortOrder` (int)

Enum `employmentType`:
- `full_time`, `part_time`, `freelance`, `contract`, `internship`

## 3.3 ResumeEducation

Champs:
- `resume` (UUID du resume cible)
- `schoolName` (string, requis)
- `degree` (string, requis)
- `fieldOfStudy` (string|null)
- `level` (enum)
- `startDate` (date, requis)
- `endDate` (date|null)
- `isCurrent` (bool)
- `description` (string|null)
- `sortOrder` (int)

Enum `level`:
- `high_school`, `associate`, `bachelor`, `master`, `doctorate`, `other`

## 3.4 ResumeSkill

Champs:
- `resume` (UUID du resume cible)
- `name` (string, requis)
- `level` (enum)
- `yearsExperience` (int|null)
- `sortOrder` (int)

Enum `level`:
- `beginner`, `intermediate`, `advanced`, `expert`

## 4) Query params communs (endpoints de liste)

Sur les endpoints `GET` de liste:
- `limit` (int)
- `offset` (int)
- `search` (string ou JSON and/or)
- `order` (ex: `?order=-createdAt`)
- `where` (JSON de filtres)
- `tenant` (optionnel multi-tenant)

Exemple:

```http
GET /api/v1/resume-skills?limit=20&offset=0&order=-sortOrder&where={"resume":"60000000-0000-1000-8000-000000000001"}
```

## 5) Endpoints détaillés + exemples

## 5.1 Resume (`/api/v1/resumes`)

### 5.1.1 Create resume
- **POST** `/api/v1/resumes`

Exemple:

```bash
curl -X POST "$API/api/v1/resumes" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Bob Application Resume",
    "summary": "Resume generated in integration test.",
    "experiences": [{"company":"ACME","position":"Backend Engineer"}],
    "education": [{"institution":"Tech University","degree":"MSc"}],
    "skills": ["PHP", "Symfony"],
    "links": [{"label":"GitHub","url":"https://example.test/bob-github"}],
    "isPublic": false
  }'
```

### 5.1.2 Mes resumes
- **GET** `/api/v1/resumes/my`

Exemple:

```bash
curl -X GET "$API/api/v1/resumes/my?limit=10&offset=0&order=-createdAt" \
  -H "Authorization: Bearer $TOKEN"
```

### 5.1.3 Détail resume
- **GET** `/api/v1/resumes/{id}`

Exemple:

```bash
curl -X GET "$API/api/v1/resumes/60000000-0000-1000-8000-000000000001" \
  -H "Authorization: Bearer $TOKEN"
```

### 5.1.4 Update complet
- **PUT** `/api/v1/resumes/{id}`

Exemple:

```bash
curl -X PUT "$API/api/v1/resumes/60000000-0000-1000-8000-000000000001" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Updated Resume",
    "summary": "Résumé complet mis à jour.",
    "experiences": [],
    "education": [],
    "skills": ["Symfony"],
    "links": [],
    "isPublic": true
  }'
```

### 5.1.5 Update partiel
- **PATCH** `/api/v1/resumes/{id}`

Exemple:

```bash
curl -X PATCH "$API/api/v1/resumes/60000000-0000-1000-8000-000000000001" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"title":"Bob Application Resume Updated","isPublic":true}'
```

### 5.1.6 Suppression
- **DELETE** `/api/v1/resumes/{id}`

Exemple:

```bash
curl -X DELETE "$API/api/v1/resumes/60000000-0000-1000-8000-000000000001" \
  -H "Authorization: Bearer $TOKEN"
```

## 5.2 Resume Experiences (`/api/v1/resume-experiences`)

### 5.2.1 Create
- **POST** `/api/v1/resume-experiences`

```json
{
  "resume": "60000000-0000-1000-8000-000000000001",
  "title": "Lead Backend Engineer",
  "companyName": "ACME",
  "employmentType": "full_time",
  "startDate": "2021-01-01",
  "endDate": null,
  "isCurrent": true,
  "location": "Remote",
  "description": "Développement d'APIs RH",
  "sortOrder": 1
}
```

### 5.2.2 Liste
- **GET** `/api/v1/resume-experiences`

```bash
curl -X GET "$API/api/v1/resume-experiences?where={\"resume\":\"60000000-0000-1000-8000-000000000001\"}&order=sortOrder" \
  -H "Authorization: Bearer $TOKEN"
```

### 5.2.3 Détail
- **GET** `/api/v1/resume-experiences/{id}`

### 5.2.4 Update complet
- **PUT** `/api/v1/resume-experiences/{id}` (payload create complet)

### 5.2.5 Update partiel
- **PATCH** `/api/v1/resume-experiences/{id}`

```json
{
  "isCurrent": false,
  "endDate": "2024-12-31"
}
```

### 5.2.6 Suppression
- **DELETE** `/api/v1/resume-experiences/{id}`

## 5.3 Resume Education (`/api/v1/resume-education`)

### 5.3.1 Create
- **POST** `/api/v1/resume-education`

```json
{
  "resume": "60000000-0000-1000-8000-000000000001",
  "schoolName": "Université de Tunis",
  "degree": "Master Informatique",
  "fieldOfStudy": "Génie logiciel",
  "level": "master",
  "startDate": "2016-09-01",
  "endDate": "2018-06-30",
  "isCurrent": false,
  "description": "Spécialité architecture logicielle",
  "sortOrder": 1
}
```

### 5.3.2 Liste
- **GET** `/api/v1/resume-education`

### 5.3.3 Détail
- **GET** `/api/v1/resume-education/{id}`

### 5.3.4 Update complet
- **PUT** `/api/v1/resume-education/{id}` (payload create complet)

### 5.3.5 Update partiel
- **PATCH** `/api/v1/resume-education/{id}`

```json
{
  "description": "Mention Très Bien"
}
```

### 5.3.6 Suppression
- **DELETE** `/api/v1/resume-education/{id}`

## 5.4 Resume Skills (`/api/v1/resume-skills`)

### 5.4.1 Create
- **POST** `/api/v1/resume-skills`

```json
{
  "resume": "60000000-0000-1000-8000-000000000001",
  "name": "Symfony",
  "level": "advanced",
  "yearsExperience": 6,
  "sortOrder": 1
}
```

### 5.4.2 Liste
- **GET** `/api/v1/resume-skills`

### 5.4.3 Détail
- **GET** `/api/v1/resume-skills/{id}`

### 5.4.4 Update complet
- **PUT** `/api/v1/resume-skills/{id}` (payload create complet)

### 5.4.5 Update partiel
- **PATCH** `/api/v1/resume-skills/{id}`

```json
{
  "level": "expert",
  "yearsExperience": 8
}
```

### 5.4.6 Suppression
- **DELETE** `/api/v1/resume-skills/{id}`

## 6) Erreurs et statuts HTTP attendus

- `200 OK` : lecture / update / delete OK
- `201 Created` : création OK
- `400 Bad Request` : payload/paramètres invalides
- `401 Unauthorized` : token absent/invalide
- `403 Forbidden` : pas le droit de modifier
- `404 Not Found` : ressource absente ou non accessible (ex: resume privé d’un autre user)

## 7) Spécification de ticket frontend (Nuxt 4 + Vuetify 3)

## 7.1 Scope écrans recommandé

1. **Liste “Mes CV”** (`GET /resumes/my`)
2. **Créer/éditer un CV** (`POST`, `PUT`, `PATCH`, `DELETE`)
3. **Bloc Expériences** (CRUD sur `/resume-experiences`)
4. **Bloc Formations** (CRUD sur `/resume-education`)
5. **Bloc Compétences** (CRUD sur `/resume-skills`)

## 7.2 Contrat technique frontend

- Créer un composable `useResumeApi()`:
  - `getMyResumes(params)`
  - `getResume(id)`
  - `createResume(payload)`
  - `updateResume(id, payload)`
  - `patchResume(id, payload)`
  - `deleteResume(id)`
  - idem pour `experience/education/skill`
- Gérer loading / error global
- Mapper erreurs 403/404 en messages UX clairs

## 7.3 Critères d’acceptation

- Un utilisateur voit ses CV via `/my`.
- Un utilisateur ne peut pas modifier le CV d’un autre.
- Un CV public est lisible par un autre utilisateur.
- Les enums sont validées côté formulaire (type, niveau, etc.).
- Chaque action CRUD affiche toast succès/erreur.

## 8) Notes d’implémentation importantes

- Le module supporte **deux styles de stockage**:
  - blocs JSON dans `resume` (`experiences`, `education`, `skills`, `links`);
  - entités dédiées (`resume_experience`, `resume_education`, `resume_skill`).
- Pour le frontend, il faut décider une stratégie UX:
  - soit tout gérer via `Resume` JSON,
  - soit utiliser les 3 sous-ressources dédiées pour un CRUD fin.
- Recommandé: choisir **une seule stratégie** dans un premier ticket pour limiter la complexité.

