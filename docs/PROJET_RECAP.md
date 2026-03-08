# Objectif

Je suis entrain de dévélopper une plateform de gestion de stocks multi-tenant.

## Fonctionnalitées

Sur la plateforme on doiT pouvoir:

- Créer son compte
- crée son entreorise
- créér une branche principale et d'autre si possible
- gérer ses stocks entre les branches

## Sécurité & Auth

- **Système :** Laravel Sanctum (Personnal Access Tokens).
- **Logique d'inscription :**
Création simultanée de `Compagnie` et `User`.
- **Endpoints** `Post /api/register`,`Post /api/login`.

## Logique Métier Validée
- Authentification : login et registere avec personnal Token
- Un utilisateur est lié à une `compagnie_id`

