# Configuration module alignment: `bro-world-backend-configuration` vs `platform-symfony`

## Contexte

Le dépôt source `bro-world-backend-configuration` n'était pas accessible depuis cet environnement (accès réseau GitHub refusé), donc l'alignement a été réalisé à partir du module `Configuration` déjà présent dans ce dépôt et des conventions locales existantes.

## Différences conservées volontairement

Aucune différence fonctionnelle supplémentaire n'a été introduite dans cette itération en dehors d'un alias de route pour compatibilité (`/v1/configurations`).

## Compatibilité d'URL

Pour limiter les risques de rupture de contrat entre implémentations utilisant soit le singulier soit le pluriel, le contrôleur `Configuration` expose désormais les deux préfixes :

- `/v1/configuration`
- `/v1/configurations`

Les deux préfixes pointent vers les mêmes actions REST et les mêmes formats JSON.
