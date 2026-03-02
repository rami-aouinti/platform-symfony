# Règle d'architecture — `dtoClasses` (scope Admin vs User/Profile)

## Inventaire actuel (`src/`, recherche `dtoClasses`)

Contrôleurs exposant `protected static array $dtoClasses` :

- `src/ApiKey/Transport/Controller/Api/V1/ApiKey/ApiKeyController.php`
- `src/ApiKey/Transport/Controller/Api/V2/ApiKey/ApiKeyCreateController.php`
- `src/ApiKey/Transport/Controller/Api/V2/ApiKey/ApiKeyPatchController.php`
- `src/ApiKey/Transport/Controller/Api/V2/ApiKey/ApiKeyUpdateController.php`
- `src/Blog/Transport/Controller/Api/V1/BlogComment/BlogCommentController.php`
- `src/Blog/Transport/Controller/Api/V1/BlogPostLink/BlogPostLinkController.php`
- `src/Blog/Transport/Controller/Api/V1/BlogTag/BlogTagController.php`
- `src/Company/Transport/Controller/Api/V1/Company/CompanyController.php`
- `src/Company/Transport/Controller/Api/V1/Company/ProfileCompanyController.php`
- `src/Configuration/Transport/Controller/Api/V1/Configuration/ConfigurationController.php`
- `src/Quiz/Transport/Controller/Api/V1/Quiz/QuizController.php`
- `src/Recruit/Transport/Controller/Api/V1/JobApplication/JobApplicationController.php`
- `src/Recruit/Transport/Controller/Api/V1/JobOffer/JobOfferController.php`
- `src/Recruit/Transport/Controller/Api/V1/Offer/OfferController.php`
- `src/Recruit/Transport/Controller/Api/V1/Resume/ProfileResumeController.php`
- `src/Recruit/Transport/Controller/Api/V1/Resume/ProfileResumeEducationController.php`
- `src/Recruit/Transport/Controller/Api/V1/Resume/ProfileResumeExperienceController.php`
- `src/Recruit/Transport/Controller/Api/V1/Resume/ProfileResumeSkillController.php`
- `src/Recruit/Transport/Controller/Api/V1/Resume/ResumeController.php`
- `src/Recruit/Transport/Controller/Api/V1/Resume/ResumeEducationController.php`
- `src/Recruit/Transport/Controller/Api/V1/Resume/ResumeExperienceController.php`
- `src/Recruit/Transport/Controller/Api/V1/Resume/ResumeLanguageController.php`
- `src/Recruit/Transport/Controller/Api/V1/Resume/ResumeProjectController.php`
- `src/Recruit/Transport/Controller/Api/V1/Resume/ResumeReferenceController.php`
- `src/Recruit/Transport/Controller/Api/V1/Resume/ResumeSkillController.php`
- `src/Task/Transport/Controller/Api/V1/Project/ProfileProjectController.php`
- `src/Task/Transport/Controller/Api/V1/Project/ProjectController.php`
- `src/Task/Transport/Controller/Api/V1/Sprint/SprintController.php`
- `src/Task/Transport/Controller/Api/V1/Task/TaskController.php`
- `src/Task/Transport/Controller/Api/V1/TaskRequest/TaskRequestController.php`
- `src/User/Transport/Controller/Api/V1/User/UserController.php`
- `src/User/Transport/Controller/Api/V1/UserGroup/UserGroupController.php`

## Convention retenue

On officialise **deux conventions explicites** pour `dtoClasses` :

1. **Scope admin (CRUD back-office)**
    - Le contrôleur doit exposer au moins une route avec un préfixe `.../admin/...`.
2. **Scope user-owned / profile (CRUD utilisateur connecté)**
    - Le contrôleur doit exposer au moins une route avec un préfixe `.../me/...`.
    - Si la route est dans `.../me/profile/...`, le nom de classe doit commencer par `Profile`.

Règles de cohérence supplémentaires :

- Tout contrôleur `Profile*Controller` avec `dtoClasses` doit être routé sous `.../me/profile/...`.
- Tout contrôleur avec `dtoClasses` doit être dans l’un des deux scopes ci-dessus (admin ou user-owned), jamais hors de ces préfixes.

## Contrôle automatique

Le test d’architecture `tests/Unit/Architecture/DtoClassesScopeConventionTest.php` vérifie cette convention.
