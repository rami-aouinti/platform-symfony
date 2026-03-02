# API Catalog v1

> Guide contributeurs: voir [Exemple complet de documentation d'un endpoint](./api-endpoint-example.md) et [Swagger](./swagger.md).

| Méthode | Endpoint complet | Module | Contrôleur::méthode | Audience | Permission minimale | Notes |
|---|---|---|---|---|---|---|
| `ANY` | `/api` | ApiKey | `ApiKeyCountController::(inherited)` | auth | `IS_AUTHENTICATED_FULLY` | legacy/non exposé: suffixe de méthode hérité (non défini dans ce contrôleur) |
| `ANY` | `/api` | ApiKey | `ApiKeyCreateController::(inherited)` | auth | `IS_AUTHENTICATED_FULLY` | legacy/non exposé: suffixe de méthode hérité (non défini dans ce contrôleur) |
| `ANY` | `/api` | ApiKey | `ApiKeyDeleteController::(inherited)` | auth | `IS_AUTHENTICATED_FULLY` | legacy/non exposé: suffixe de méthode hérité (non défini dans ce contrôleur) |
| `ANY` | `/api` | ApiKey | `ApiKeyIdsController::(inherited)` | auth | `IS_AUTHENTICATED_FULLY` | legacy/non exposé: suffixe de méthode hérité (non défini dans ce contrôleur) |
| `ANY` | `/api` | ApiKey | `ApiKeyListController::(inherited)` | auth | `IS_AUTHENTICATED_FULLY` | legacy/non exposé: suffixe de méthode hérité (non défini dans ce contrôleur) |
| `ANY` | `/api` | ApiKey | `ApiKeyPatchController::(inherited)` | auth | `IS_AUTHENTICATED_FULLY` | legacy/non exposé: suffixe de méthode hérité (non défini dans ce contrôleur) |
| `ANY` | `/api` | ApiKey | `ApiKeyUpdateController::(inherited)` | auth | `IS_AUTHENTICATED_FULLY` | legacy/non exposé: suffixe de méthode hérité (non défini dans ce contrôleur) |
| `ANY` | `/api` | ApiKey | `ApiKeyViewController::(inherited)` | auth | `IS_AUTHENTICATED_FULLY` | legacy/non exposé: suffixe de méthode hérité (non défini dans ce contrôleur) |
| `ANY` | `/api/v1/api_key` | ApiKey | `ApiKeyController::(inherited)` | auth | `IS_AUTHENTICATED_FULLY` | legacy/non exposé: suffixe de méthode hérité (non défini dans ce contrôleur) |
| `ANY` | `/api/v1/blog-comments` | Blog | `BlogCommentController::(inherited)` | auth | `ROLE_ADMIN/ROLE_ROOT` | legacy/non exposé: suffixe de méthode hérité (non défini dans ce contrôleur) |
| `ANY` | `/api/v1/blog-post-links` | Blog | `BlogPostLinkController::(inherited)` | auth | `ROLE_ADMIN/ROLE_ROOT` | legacy/non exposé: suffixe de méthode hérité (non défini dans ce contrôleur) |
| `ANY` | `/api/v1/blog-posts` | Blog | `BlogPostController::(inherited)` | auth | `ROLE_ADMIN/ROLE_ROOT` | legacy/non exposé: suffixe de méthode hérité (non défini dans ce contrôleur) |
| `ANY` | `/api/v1/blog-tags` | Blog | `BlogTagController::(inherited)` | auth | `ROLE_ADMIN/ROLE_ROOT` | legacy/non exposé: suffixe de méthode hérité (non défini dans ce contrôleur) |
| `GET` | `/api/v1/task-requests/{id}/blog-posts` | Blog | `TaskRequestBlogPostsController::taskRequestBlogPostsAction` | auth | `ROLE_ADMIN/ROLE_ROOT` |  |
| `GET` | `/api/v1/tasks/{id}/blog-posts` | Blog | `TaskBlogPostsController::taskBlogPostsAction` | auth | `ROLE_ADMIN/ROLE_ROOT` |  |
| `ANY` | `/api/v1/calendar/events` | Calendar | `EventController::(inherited)` | auth | `ROLE_ADMIN/ROLE_ROOT` | legacy/non exposé: suffixe de méthode hérité (non défini dans ce contrôleur) |
| `GET` | `/api/v1/admin/chat/messages` | Chat | `MessageController::adminListAction` | admin | `ROLE_ADMIN/ROLE_ROOT` |  |
| `DELETE` | `/api/v1/admin/chat/messages/{id}` | Chat | `MessageController::adminDeleteAction` | admin | `ROLE_ADMIN/ROLE_ROOT` |  |
| `GET` | `/api/v1/chat/conversations` | Chat | `ConversationController::listAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `GET` | `/api/v1/chat/conversations/{id}` | Chat | `ConversationController::detailAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `GET` | `/api/v1/me/chat/conversations/{id}/messages` | Chat | `MessageController::listAction` | me | `IS_AUTHENTICATED_FULLY` |  |
| `POST` | `/api/v1/me/chat/conversations/{id}/messages` | Chat | `MessageController::createAction` | me | `IS_AUTHENTICATED_FULLY` |  |
| `POST` | `/api/v1/me/chat/conversations/{id}/participants` | Chat | `ConversationParticipantController::addAction` | me | `IS_AUTHENTICATED_FULLY` |  |
| `DELETE` | `/api/v1/me/chat/conversations/{id}/participants/{userId}` | Chat | `ConversationParticipantController::removeAction` | me | `IS_AUTHENTICATED_FULLY` |  |
| `DELETE` | `/api/v1/me/chat/messages/{messageId}` | Chat | `MessageController::deleteAction` | me | `IS_AUTHENTICATED_FULLY` |  |
| `PATCH` | `/api/v1/me/chat/messages/{messageId}` | Chat | `MessageController::patchAction` | me | `IS_AUTHENTICATED_FULLY` |  |
| `GET` | `/api` | Company | `MyCompanyMembershipController::companiesAction` | auth | `custom voter/expression` |  |
| `GET` | `/api` | Company | `ProfileCompanyController::findAction` | auth | `custom voter/expression` |  |
| `ANY` | `/api/v1/companies` | Company | `CompanyController::(inherited)` | auth | `ROLE_ADMIN/ROLE_ROOT` | legacy/non exposé: suffixe de méthode hérité (non défini dans ce contrôleur) |
| `GET` | `/api/v1/companies/{companyId}/members` | Company | `CompanyMembersController::membersAction` | auth | `custom voter/expression` |  |
| `POST` | `/api/v1/companies/{companyId}/members` | Company | `CompanyMembersController::inviteOrAttachAction` | auth | `custom voter/expression` |  |
| `DELETE` | `/api/v1/companies/{companyId}/members/{userId}` | Company | `CompanyMembersController::removeMembershipAction` | auth | `custom voter/expression` |  |
| `PATCH` | `/api/v1/companies/{companyId}/members/{userId}` | Company | `CompanyMembersController::updateMembershipAction` | auth | `custom voter/expression` |  |
| `GET` | `/api/v1/companies/{companyId}/memberships` | Company | `CompanyMembersController::membershipsAction` | auth | `custom voter/expression` |  |
| `GET` | `/api/v1/companies/{id}/projects` | Company | `CompanyProjectsController::projectsAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `GET` | `/api/v1/companies/{id}/sprints` | Company | `CompanySprintsController::sprintsAction` | auth | `ROLE_ADMIN/ROLE_ROOT` |  |
| `GET` | `/api/{companyId}/membership` | Company | `MyCompanyMembershipController::membershipAction` | auth | `custom voter/expression` |  |
| `ANY` | `/api/v1/admin/configurations` | Configuration | `ConfigurationController::(inherited)` | admin | `ROLE_ADMIN/ROLE_ROOT` | legacy/non exposé: suffixe de méthode hérité (non défini dans ce contrôleur) |
| `ANY` | `/api/v1/configuration` | Configuration | `ConfigurationController::(inherited)` | auth | `ROLE_ADMIN/ROLE_ROOT` | legacy/non exposé: suffixe de méthode hérité (non défini dans ce contrôleur) |
| `ANY` | `/api/v1/configurations` | Configuration | `ConfigurationController::(inherited)` | auth | `ROLE_ADMIN/ROLE_ROOT` | legacy/non exposé: suffixe de méthode hérité (non défini dans ce contrôleur) |
| `GET` | `/api/v1/media/export/{configurationId}/excel` | Media | `MediaController::exportExcelAction` | auth | `custom voter/expression` |  |
| `GET` | `/api/v1/media/export/{configurationId}/pdf` | Media | `MediaController::exportPdfAction` | auth | `custom voter/expression` |  |
| `POST` | `/api/v1/media/upload` | Media | `MediaController::uploadAction` | auth | `custom voter/expression` |  |
| `ANY` | `/api/v1/quizzes` | Quiz | `QuizController::(inherited)` | auth | `ROLE_ADMIN/ROLE_ROOT` | legacy/non exposé: suffixe de méthode hérité (non défini dans ce contrôleur) |
| `GET` | `/api` | Recruit | `ProfileResumeController::findAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `GET` | `/api` | Recruit | `ProfileResumeEducationController::findAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `GET` | `/api` | Recruit | `ProfileResumeExperienceController::findAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `GET` | `/api` | Recruit | `ProfileResumeSkillController::findAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `GET` | `/api` | Recruit | `ResumeLanguageController::findAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `GET` | `/api` | Recruit | `ResumeProjectController::findAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `GET` | `/api` | Recruit | `ResumeReferenceController::findAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `POST` | `/api` | Recruit | `ProfileResumeController::createAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `POST` | `/api` | Recruit | `ProfileResumeEducationController::createAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `POST` | `/api` | Recruit | `ProfileResumeExperienceController::createAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `POST` | `/api` | Recruit | `ProfileResumeSkillController::createAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `POST` | `/api` | Recruit | `ResumeLanguageController::createAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `POST` | `/api` | Recruit | `ResumeProjectController::createAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `POST` | `/api` | Recruit | `ResumeReferenceController::createAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `GET` | `/api/schema/{method}` | Recruit | `ProfileResumeController::schemaAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `ANY` | `/api/v1/candidates` | Recruit | `CandidateController::(inherited)` | auth | `ROLE_ADMIN/ROLE_ROOT` | legacy/non exposé: suffixe de méthode hérité (non défini dans ce contrôleur) |
| `ANY` | `/api/v1/catalog/job-categories` | Recruit | `JobCategoryCatalogController::(inherited)` | auth | `IS_AUTHENTICATED_FULLY` | legacy/non exposé: suffixe de méthode hérité (non défini dans ce contrôleur) |
| `ANY` | `/api/v1/catalog/languages` | Recruit | `LanguageCatalogController::(inherited)` | auth | `IS_AUTHENTICATED_FULLY` | legacy/non exposé: suffixe de méthode hérité (non défini dans ce contrôleur) |
| `ANY` | `/api/v1/catalog/skills` | Recruit | `SkillCatalogController::(inherited)` | auth | `IS_AUTHENTICATED_FULLY` | legacy/non exposé: suffixe de méthode hérité (non défini dans ce contrôleur) |
| `GET` | `/api/v1/job-applications` | Recruit | `JobApplicationController::findAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `POST` | `/api/v1/job-applications` | Recruit | `JobApplicationController::createAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `GET` | `/api/v1/job-applications/my-offers` | Recruit | `JobApplicationController::findForMyOffersAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `DELETE` | `/api/v1/job-applications/{id}` | Recruit | `JobApplicationController::deleteAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `GET` | `/api/v1/job-applications/{id}` | Recruit | `JobApplicationController::findOneAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `PATCH` | `/api/v1/job-applications/{id}` | Recruit | `JobApplicationController::patchAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `PUT` | `/api/v1/job-applications/{id}` | Recruit | `JobApplicationController::updateAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `PATCH` | `/api/v1/job-applications/{id}/accept` | Recruit | `JobApplicationController::acceptAction` | auth | `ROLE_ADMIN/ROLE_ROOT` |  |
| `PATCH` | `/api/v1/job-applications/{id}/reject` | Recruit | `JobApplicationController::rejectAction` | auth | `ROLE_ADMIN/ROLE_ROOT` |  |
| `PATCH` | `/api/v1/job-applications/{id}/withdraw` | Recruit | `JobApplicationController::withdrawAction` | auth | `ROLE_ADMIN/ROLE_ROOT` |  |
| `GET` | `/api/v1/job-offers` | Recruit | `JobOfferController::findAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `POST` | `/api/v1/job-offers` | Recruit | `JobOfferController::createAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `GET` | `/api/v1/job-offers/available` | Recruit | `JobOfferController::findAvailableAction` | auth | `ROLE_ADMIN/ROLE_ROOT` |  |
| `GET` | `/api/v1/job-offers/facets` | Recruit | `JobOfferController::facetsAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `GET` | `/api/v1/job-offers/my` | Recruit | `JobOfferController::findMyAction` | auth | `ROLE_ADMIN/ROLE_ROOT` |  |
| `DELETE` | `/api/v1/job-offers/{id}` | Recruit | `JobOfferController::deleteAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `GET` | `/api/v1/job-offers/{id}` | Recruit | `JobOfferController::findOneAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `PATCH` | `/api/v1/job-offers/{id}` | Recruit | `JobOfferController::patchAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `PUT` | `/api/v1/job-offers/{id}` | Recruit | `JobOfferController::updateAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `POST` | `/api/v1/job-offers/{id}/apply` | Recruit | `OfferApplicationController::createForOfferAction` | auth | `ROLE_ADMIN/ROLE_ROOT` |  |
| `ANY` | `/api/v1/offers` | Recruit | `OfferController::(inherited)` | auth | `ROLE_ADMIN/ROLE_ROOT` | legacy/non exposé: suffixe de méthode hérité (non défini dans ce contrôleur) |
| `ANY` | `/api/v1/resume-education` | Recruit | `ResumeEducationController::(inherited)` | auth | `ROLE_ADMIN/ROLE_ROOT` | legacy/non exposé: suffixe de méthode hérité (non défini dans ce contrôleur) |
| `ANY` | `/api/v1/resume-experiences` | Recruit | `ResumeExperienceController::(inherited)` | auth | `ROLE_ADMIN/ROLE_ROOT` | legacy/non exposé: suffixe de méthode hérité (non défini dans ce contrôleur) |
| `ANY` | `/api/v1/resume-skills` | Recruit | `ResumeSkillController::(inherited)` | auth | `ROLE_ADMIN/ROLE_ROOT` | legacy/non exposé: suffixe de méthode hérité (non défini dans ce contrôleur) |
| `GET` | `/api/v1/resumes/my` | Recruit | `ResumeController::findMyAction` | auth | `ROLE_ADMIN/ROLE_ROOT` |  |
| `GET` | `/api/v1/resumes/{id}` | Recruit | `ResumeController::findOneAction` | auth | `ROLE_ADMIN/ROLE_ROOT` |  |
| `DELETE` | `/api/{id}` | Recruit | `ProfileResumeController::deleteAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `DELETE` | `/api/{id}` | Recruit | `ProfileResumeEducationController::deleteAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `DELETE` | `/api/{id}` | Recruit | `ProfileResumeExperienceController::deleteAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `DELETE` | `/api/{id}` | Recruit | `ProfileResumeSkillController::deleteAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `DELETE` | `/api/{id}` | Recruit | `ResumeLanguageController::deleteAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `DELETE` | `/api/{id}` | Recruit | `ResumeProjectController::deleteAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `DELETE` | `/api/{id}` | Recruit | `ResumeReferenceController::deleteAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `GET` | `/api/{id}` | Recruit | `ProfileResumeController::findOneAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `GET` | `/api/{id}` | Recruit | `ProfileResumeEducationController::findOneAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `GET` | `/api/{id}` | Recruit | `ProfileResumeExperienceController::findOneAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `GET` | `/api/{id}` | Recruit | `ProfileResumeSkillController::findOneAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `GET` | `/api/{id}` | Recruit | `ResumeLanguageController::findOneAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `GET` | `/api/{id}` | Recruit | `ResumeProjectController::findOneAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `GET` | `/api/{id}` | Recruit | `ResumeReferenceController::findOneAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `PATCH` | `/api/{id}` | Recruit | `ProfileResumeController::patchAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `PATCH` | `/api/{id}` | Recruit | `ProfileResumeEducationController::patchAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `PATCH` | `/api/{id}` | Recruit | `ProfileResumeExperienceController::patchAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `PATCH` | `/api/{id}` | Recruit | `ProfileResumeSkillController::patchAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `PATCH` | `/api/{id}` | Recruit | `ResumeLanguageController::patchAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `PATCH` | `/api/{id}` | Recruit | `ResumeProjectController::patchAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `PATCH` | `/api/{id}` | Recruit | `ResumeReferenceController::patchAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `PUT` | `/api/{id}` | Recruit | `ProfileResumeController::updateAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `PUT` | `/api/{id}` | Recruit | `ProfileResumeEducationController::updateAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `PUT` | `/api/{id}` | Recruit | `ProfileResumeExperienceController::updateAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `PUT` | `/api/{id}` | Recruit | `ProfileResumeSkillController::updateAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `PUT` | `/api/{id}` | Recruit | `ResumeLanguageController::updateAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `PUT` | `/api/{id}` | Recruit | `ResumeProjectController::updateAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `PUT` | `/api/{id}` | Recruit | `ResumeReferenceController::updateAction` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `ANY` | `/api/v1/role` | Role | `RoleController::(inherited)` | auth | `custom voter/expression` | legacy/non exposé: suffixe de méthode hérité (non défini dans ce contrôleur) |
| `GET` | `/api/v1/role/{role}` | Role | `FindOneRoleController::__invoke` | auth | `custom voter/expression` |  |
| `GET` | `/api/v1/role/{role}/inherited` | Role | `InheritedRolesController::__invoke` | auth | `custom voter/expression` |  |
| `GET` | `/api/v1/statistics/distributions/statuses` | Statistic | `StatisticController::statusDistributions` | auth | `custom voter/expression` |  |
| `GET` | `/api/v1/statistics/entities` | Statistic | `StatisticController::entities` | auth | `custom voter/expression` |  |
| `GET` | `/api/v1/statistics/overview` | Statistic | `StatisticController::overview` | auth | `custom voter/expression` |  |
| `GET` | `/api/v1/statistics/timeseries` | Statistic | `StatisticController::timeSeries` | auth | `custom voter/expression` |  |
| `GET` | `/api/v1/statistics/timeseries/{entity}` | Statistic | `StatisticController::timeSeriesByEntity` | auth | `custom voter/expression` |  |
| `ANY` | `/api` | Task | `ProfileProjectController::(inherited)` | auth | `custom voter/expression` | legacy/non exposé: suffixe de méthode hérité (non défini dans ce contrôleur) |
| `ANY` | `/api/v1/projects` | Task | `ProjectController::(inherited)` | auth | `ROLE_ADMIN/ROLE_ROOT` | legacy/non exposé: suffixe de méthode hérité (non défini dans ce contrôleur) |
| `GET` | `/api/v1/projects/{id}/tasks` | Task | `ProjectTasksController::tasksAction` | auth | `ROLE_ADMIN/ROLE_ROOT` |  |
| `ANY` | `/api/v1/sprints` | Task | `SprintController::(inherited)` | auth | `ROLE_ADMIN/ROLE_ROOT` | legacy/non exposé: suffixe de méthode hérité (non défini dans ce contrôleur) |
| `GET` | `/api/v1/task-requests/sprints/{sprintId}/grouped-by-task` | Task | `TaskRequestController::listBySprintGroupedByTaskAction` | auth | `ROLE_ADMIN/ROLE_ROOT` |  |
| `PATCH` | `/api/v1/task-requests/{id}/requested-status/{status}` | Task | `TaskRequestController::changeRequestedStatusAction` | auth | `ROLE_ADMIN/ROLE_ROOT` |  |
| `PATCH` | `/api/v1/task-requests/{id}/requester/{requesterId}` | Task | `TaskRequestController::assignRequesterAction` | auth | `ROLE_ADMIN/ROLE_ROOT` |  |
| `PATCH` | `/api/v1/task-requests/{id}/reviewer/{reviewerId}` | Task | `TaskRequestController::assignReviewerAction` | auth | `ROLE_ADMIN/ROLE_ROOT` |  |
| `PATCH` | `/api/v1/task-requests/{id}/sprint/{sprintId}` | Task | `TaskRequestController::assignSprintAction` | auth | `ROLE_ADMIN/ROLE_ROOT` |  |
| `PATCH` | `/api/v1/tasks/{id}/archive` | Task | `TaskController::archiveAction` | auth | `ROLE_ADMIN/ROLE_ROOT` |  |
| `PATCH` | `/api/v1/tasks/{id}/complete` | Task | `TaskController::completeAction` | auth | `ROLE_ADMIN/ROLE_ROOT` |  |
| `PATCH` | `/api/v1/tasks/{id}/reopen` | Task | `TaskController::reopenAction` | auth | `ROLE_ADMIN/ROLE_ROOT` |  |
| `PATCH` | `/api/v1/tasks/{id}/start` | Task | `TaskController::startAction` | auth | `ROLE_ADMIN/ROLE_ROOT` |  |
| `GET` | `/api/v1/tasks/{id}/task-requests` | Task | `TaskRequestsController::taskRequestsAction` | auth | `ROLE_ADMIN/ROLE_ROOT` |  |
| `GET` | `/api` | Tool | `IndexController::__invoke` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `GET` | `/api/health` | Tool | `HealthController::__invoke` | public | `PUBLIC_ACCESS` |  |
| `GET` | `/api/v1/localization/language` | Tool | `LanguageController::__invoke` | public | `PUBLIC_ACCESS` |  |
| `GET` | `/api/v1/localization/locale` | Tool | `LocaleController::__invoke` | public | `PUBLIC_ACCESS` |  |
| `GET` | `/api/v1/localization/timezone` | Tool | `TimeZoneController::__invoke` | public | `PUBLIC_ACCESS` |  |
| `GET` | `/api/version` | Tool | `VersionController::__invoke` | public | `PUBLIC_ACCESS` |  |
| `ANY` | `/api/v1/admin/users` | User | `UserController::(inherited)` | admin | `ROLE_ADMIN/ROLE_ROOT` | legacy/non exposé: suffixe de méthode hérité (non défini dans ce contrôleur) |
| `DELETE` | `/api/v1/admin/users/{user}` | User | `DeleteUserController::__invoke` | admin | `custom voter/expression` |  |
| `DELETE` | `/api/v1/admin/users/{user}/group/{userGroup}` | User | `DetachUserGroupController::__invoke` | admin | `custom voter/expression` |  |
| `POST` | `/api/v1/admin/users/{user}/group/{userGroup}` | User | `AttachUserGroupController::__invoke` | admin | `custom voter/expression` |  |
| `GET` | `/api/v1/admin/users/{user}/groups` | User | `UserGroupsController::__invoke` | admin | `ROLE_ROOT` |  |
| `GET` | `/api/v1/admin/users/{user}/roles` | User | `UserRolesController::__invoke` | admin | `ROLE_ROOT` |  |
| `POST` | `/api/v1/auth/get_token` | User | `GetTokenController::__invoke` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `POST` | `/api/v1/auth/social/connect` | User | `SocialConnectController::__invoke` | auth | `IS_AUTHENTICATED_FULLY` |  |
| `GET` | `/api/v1/me/profile` | User | `IndexController::__invoke` | me | `IS_AUTHENTICATED_FULLY` |  |
| `PATCH` | `/api/v1/me/profile` | User | `IndexController::patchProfileAction` | me | `IS_AUTHENTICATED_FULLY` |  |
| `POST` | `/api/v1/me/profile/addresses` | User | `IndexController::addAddressAction` | me | `IS_AUTHENTICATED_FULLY` |  |
| `DELETE` | `/api/v1/me/profile/addresses/{addressId}` | User | `IndexController::deleteAddressAction` | me | `IS_AUTHENTICATED_FULLY` |  |
| `PATCH` | `/api/v1/me/profile/addresses/{addressId}` | User | `IndexController::patchAddressAction` | me | `IS_AUTHENTICATED_FULLY` |  |
| `PUT` | `/api/v1/me/profile/avatar` | User | `IndexController::updateAvatarAction` | me | `IS_AUTHENTICATED_FULLY` |  |
| `GET` | `/api/v1/me/profile/companies` | User | `CompaniesController::companiesAction` | me | `IS_AUTHENTICATED_FULLY` |  |
| `GET` | `/api/v1/me/profile/groups` | User | `GroupsController::__invoke` | me | `IS_AUTHENTICATED_FULLY` |  |
| `GET` | `/api/v1/me/profile/projects` | User | `CompaniesController::projectsAction` | me | `IS_AUTHENTICATED_FULLY` |  |
| `GET` | `/api/v1/me/profile/roles` | User | `RolesController::__invoke` | me | `IS_AUTHENTICATED_FULLY` |  |
| `GET` | `/api/v1/me/social-accounts` | User | `SocialAccountsController::listAction` | me | `IS_AUTHENTICATED_FULLY` |  |
| `POST` | `/api/v1/me/social-accounts/link` | User | `SocialAccountsController::linkAction` | me | `IS_AUTHENTICATED_FULLY` |  |
| `DELETE` | `/api/v1/me/social-accounts/{provider}` | User | `SocialAccountsController::unlinkAction` | me | `IS_AUTHENTICATED_FULLY` |  |
| `ANY` | `/api/v1/user_group` | User | `UserGroupController::(inherited)` | auth | `ROLE_ADMIN/ROLE_ROOT` | legacy/non exposé: suffixe de méthode hérité (non défini dans ce contrôleur) |
| `DELETE` | `/api/v1/user_group/{userGroup}/user/{user}` | User | `DetachUserController::__invoke` | auth | `custom voter/expression` |  |
| `POST` | `/api/v1/user_group/{userGroup}/user/{user}` | User | `AttachUserController::__invoke` | auth | `custom voter/expression` |  |
| `GET` | `/api/v1/user_group/{userGroup}/users` | User | `UsersController::__invoke` | auth | `custom voter/expression` |  |
