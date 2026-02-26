# Règle d'architecture — `General/Domain` neutre transport

## Décision

- Le code sous `src/General/Domain` doit rester **neutre transport** (pas de dépendance au serializer/API layer).
- Les groupes de sérialisation API (`#[Groups(...)]`) restent dans les entités concrètes des bounded contexts (ex: `User`, `ApiKey`) ou dans des metadata dédiées (`config/serializer/*.yaml`).
- Les traits partagés de `General/Domain` (ex: `NameTrait`, `SlugTrait`, `Timestampable`) ne doivent pas embarquer de groupes de sérialisation figés.
- Si une exposition différente est nécessaire selon le contexte, préférer des DTO dédiés dans `Application/DTO`.

## Garde-fou

Un test d'architecture (`tests/Unit/Architecture/GeneralDomainTransportCouplingTest.php`) interdit l'introduction de `Groups` et de dépendances serializer dans `src/General/Domain`.
