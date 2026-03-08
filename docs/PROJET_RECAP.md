# 📦 AMG — Plateforme de Gestion de Stocks (SaaS Multi-Tenant)

> Dernière mise à jour : 2026-03-08
> Stack : Laravel + Sanctum | Base : MySQL | Architecture : Multi-tenant (shared DB, isolation par `compagnie_id`)

---

## 🎯 Objectif du Projet

Plateforme SaaS permettant à plusieurs entreprises (tenants) de gérer leurs stocks de manière isolée.
Chaque entreprise possède ses branches (dépôts), ses produits et ses mouvements de stock.

---

## ✅ Logiques Métier Validées

### 1. Authentification & Onboarding
- **Register** (`POST /api/register`) : Création atomique (transaction DB) de :
  1. La `Compagnie`
  2. Une `Branche` principale ("Dépôt Principal")
  3. L'`User` lié à la compagnie et à la branche
  - Retourne un **Personal Access Token** (Sanctum) + user + branche
- **Login** (`POST /api/login`) : Auth par email/password → retourne un token Sanctum
- **Isolation des tokens** : chaque token est lié à un user, lui-même lié à une `compagnie_id`

### 2. Isolation Multi-Tenant (CompagnieScope)
- Un `CompagnieScope` (Global Scope Eloquent) est appliqué sur le modèle `Product`
- Toute requête sur `Product` filtre automatiquement par `compagnie_id` de l'utilisateur connecté
- Le `compagnie_id` est auto-injecté à la création via un hook `creating` dans `booted()`
- **Principe** : un tenant ne peut jamais voir ou toucher les données d'un autre tenant

### 3. Création de Produit avec Stock Initial
- **Endpoint** : `POST /api/product` (protégé par `auth:sanctum`)
- Validation : `name`, `sku` (unique), `price`, `branche_id`, `quantity` (optionnel)
- **Vérification d'autorisation** : `BranchePolicy@view` → l'utilisateur ne peut utiliser que les branches de sa compagnie
- Transaction DB :
  1. Création du `Product` (avec `compagnie_id` auto-injecté)
  2. Création d'un `Stock` (liaison `product_id` + `branche_id`)
  3. Si `quantity > 0` → création d'un `StockMovement` de type `in` (traçabilité obligatoire)
- **Règle fondamentale** : on ne modifie jamais le stock directement — tout passe par un `StockMovement`

### 4. Observer StockMovement (Mise à jour automatique du stock)
- `StockMovementObserver` écoute l'événement `created` sur `StockMovement`
- Logique :
  - `type = in` ou `adjustment` → `stock.quantity += movement.quantity`
  - `type = out` → `stock.quantity -= abs(movement.quantity)`
- Avantage : le calcul du stock est centralisé, découplé des controllers

### 5. Politique de Sécurité sur les Branches (BranchePolicy)
- `view()` : un user ne peut accéder/utiliser une branche que si `user.compagnie_id === branche.compagnie_id`
- Utilisée dans `ProductController@store` pour empêcher l'injection d'un `branche_id` d'une autre compagnie

---

## ⚙️ Architecture Technique

### Schéma des Relations
```
Compagnie
  ├── hasMany Users
  ├── hasMany Branches
  └── hasMany Products
       └── hasMany Stocks (product_id + branche_id, unique)
                └── hasMany StockMovements
```

### Modèles & Tables
| Modèle | Table | Points clés |
|--------|-------|-------------|
| `Compagnie` | `compagnies` | name, slug, email, phone |
| `User` | `users` | compagnie_id, branche_id |
| `Branche` | `branches` | compagnie_id, name, address |
| `Product` | `products` | compagnie_id, name, sku (unique), price |
| `Stock` | `stocks` | product_id + branche_id (unique), quantity, softDeletes |
| `StockMovement` | `stock_movements` | reference (unique), stock_id, user_id, type (in/out/adjustment/transfert), quantity, reason |

### Types de mouvements définis (enum)
- `in` — Entrée de stock
- `out` — Sortie de stock
- `adjustment` — Correction/inventaire
- `transfert` — Transfert entre branches (prévu)

---

## 🐛 Problèmes Identifiés / Code à Corriger

### 🔴 Bugs actifs
1. **`ProductController@index`** : double `return` → code mort, mais le filtre tenant **fonctionne quand même** grâce au `CompagnieScope` appliqué automatiquement sur `Product`.
   ```php
   return Product::with('stocks')->get(); // ✅ CompagnieScope filtre bien par compagnie_id
   return Product::where('compagnie_id', ...) // ← jamais exécuté (redondant et inutile)
   ```
   → À nettoyer : supprimer la deuxième ligne, le `CompagnieScope` suffit.

2. **`ProductController@store`** : le champ validé est `initial_quantity` mais le code utilise `$request->quantity` → le stock initial sera toujours 0.

3. **`StockMovementController@store`** : utilise `Stock` sans l'importer (`use App\Models\Stock`) → erreur fatale à l'exécution.

4. **`ProductService`** : `compagnie_id` est assigné avec `auth()->id()` au lieu de `auth()->user()->compagnie_id`.

5. **`BrancheController@store`** : `Branche::create($request)` passe l'objet Request entier au lieu de `$request->validated()` ou `$request->all()`.

### 🟡 Incohérences mineures
- `StockMovementController` utilise `company_id` / `branch_id` (anglais) alors que le reste du projet utilise `compagnie_id` / `branche_id` (français)
- La référence du mouvement dans `ProductController` : `date("YYYY/MM/DD")` est une syntaxe incorrecte en PHP (devrait être `date("Y/m/d")`)
- `ClobaleScope` (fichier vide) — à supprimer ou implémenter

---

## 🚀 Fonctionnalités & Systèmes à Venir

### Priorité Haute
- [ ] **Transfert inter-branches** : logique `type=transfert` → décrémenter le stock de la branche source, incrémenter la branche destination, en une seule transaction atomique. L'observer actuel ne gère pas encore ce cas.
- [ ] **BrancheController complet** : CRUD des branches (create, list, update, delete) avec policy
- [ ] **StockMovementController complet** : entrée/sortie de stock depuis une branche existante (le controller existe mais est vide ou cassé)
- [ ] **ProductController complet** : `show`, `update`, `destroy` — actuellement vides

### Priorité Moyenne
- [ ] **Listing des produits** : nettoyer le code mort dans `index()` (deuxième `return` inutile)
- [ ] **Historique des mouvements** par produit ou par branche (`GET /api/product/{id}/movements`)
- [ ] **Gestion des rôles utilisateurs** : Admin compagnie, Manager branche, Opérateur — actuellement tous les users ont les mêmes droits
- [ ] **Multi-utilisateurs par compagnie** : inviter d'autres utilisateurs dans sa compagnie

### Priorité Basse / Future
- [ ] **Alertes stock bas** : déclencher une notification quand `quantity < seuil`
- [ ] **Rapport / Dashboard** : stock total par branche, valeur du stock (qty × price)
- [ ] **Catégories de produits**
- [ ] **Unités de mesure** (`PIECE`, `KG`, `L`...)
- [ ] **Export CSV/PDF** des mouvements
- [ ] **Super Admin plateforme** : gestion des tenants depuis un back-office global

---

## 📡 Routes API actuelles

| Méthode | Endpoint | Auth | Status |
|---------|----------|------|--------|
| POST | `/api/register` | Non | ✅ Fonctionnel |
| POST | `/api/login` | Non | ✅ Fonctionnel |
| GET | `/api/user` | Sanctum | ✅ Fonctionnel |
| GET | `/api/product` | Sanctum | ✅ Fonctionnel (CompagnieScope actif) — code mort à nettoyer |
| POST | `/api/product` | Sanctum | ⚠️ Partiel (quantity non transmise) |
| GET | `/api/product/{id}` | Sanctum | 🟡 Vide |
| PUT | `/api/product/{id}` | Sanctum | 🟡 Vide |
| DELETE | `/api/product/{id}` | Sanctum | 🟡 Vide |

---

## 📊 État Global du Projet

| Couche | État |
|--------|------|
| Schéma base de données | ✅ Validé |
| Modèles & Relations | ✅ Validés |
| Migrations | ✅ Validées |
| Seeders | ✅ Présents (CompagnieSeeder, BrancheSeeder, ProductSeeder, StockSeeder) |
| Auth (register/login) | ✅ Fonctionnel |
| Isolation multi-tenant | ✅ Implémentée (CompagnieScope sur Product) |
| Création produit + stock | ⚠️ Partiel (bug quantity) |
| Observer stock | ✅ Implémenté |
| BranchePolicy | ✅ Implémentée |
| Transfert inter-branches | ❌ Non implémenté |
| Gestion des rôles | ❌ Non implémentée |
| CRUD Branches | ❌ Non implémenté |

---

## 🔄 Mise à jour — 2026-03-08 (Rôles & Pivot)

### Logiques ajoutées

**Système de Rôles (admin / manager / operator)**
- Colonne `role` (enum) ajoutée dans `users` — valeur par défaut `operator`
- `branche_id` supprimé de `users` — remplacé par la table pivot `branche_user`
- Table pivot `branche_user` (`user_id` + `branche_id`, unique)
- **Admin** : accède à toutes les branches de sa compagnie via `compagnie_id`
- **Manager** : accède à ses branches assignées dans la pivot (N branches)
- **Operator** : accède à sa branche assignée dans la pivot (1 branche)
- Le fondateur lors du `register` reçoit automatiquement `role = admin`
- Helpers sur `User` : `isAdmin()`, `isManager()`, `isOperator()`, `accessibleBrancheIds()`, `canAccessBranche($id)`
- `BranchePolicy` : méthode `before()` court-circuite toutes les vérifications pour l'admin

### Fichiers modifiés / créés
| Fichier | Action |
|---------|--------|
| `migrations/..._add_role_to_users_table.php` | ✅ Créé |
| `migrations/..._create_branche_user_table.php` | ✅ Créé |
| `app/Models/User.php` | ✅ Mis à jour (role, pivot branches, helpers) |
| `app/Models/Branche.php` | ✅ Mis à jour (relation pivot users) |
| `app/Http/Controllers/AuthController.php` | ✅ Mis à jour (role=admin au register, attach pivot) |
| `app/Policies/BranchePolicy.php` | ✅ Mis à jour (before() pour admin) |

### Prochaine étape
- [ ] **BrancheController** : CRUD complet (create/list/update/delete) — réservé admin
- [ ] **UserController** : inviter un user, lui assigner rôle + branche(s)

---

## 🔄 Mise à jour — 2026-03-08 (BrancheController)

### Logiques ajoutées

**CRUD Branches (réservé admin, filtré par rôle)**
- `GET /api/branche` → retourne toutes les branches (admin) ou uniquement les branches assignées (manager/operator)
- `POST /api/branche` → crée une branche — admin uniquement, `compagnie_id` auto-injecté
- `GET /api/branche/{id}` → détail + users assignés — vérifié via `BranchePolicy@view`
- `PUT /api/branche/{id}` → modification — admin uniquement, vérifié via `BranchePolicy@update`
- `DELETE /api/branche/{id}` → soft delete — admin uniquement, **bloqué si dernière branche**
- Validation extraite dans `StoreBrancheRequest` et `UpdateBrancheRequest` (Form Requests)
- Réponses formatées via `BrancheResource` et `UserResource` (API Resources)

### Fichiers créés
| Fichier | Rôle |
|---------|------|
| `Http/Requests/StoreBrancheRequest.php` | Validation + authorize create |
| `Http/Requests/UpdateBrancheRequest.php` | Validation + authorize update |
| `Http/Resources/BrancheResource.php` | Format réponse branche |
| `Http/Resources/UserResource.php` | Format réponse user (utilisé dans BrancheResource) |
| `Http/Controllers/BrancheController.php` | CRUD complet |
| `routes/api.php` | Ajout route `branche` |

### Prochaine étape
- [ ] **UserController** : inviter un user dans la compagnie, lui assigner rôle + branche(s)

---

## 🔄 Mise à jour — 2026-03-08 (UserController)

### Logiques ajoutées

**Gestion des utilisateurs de la compagnie (admin uniquement)**
- `GET  /api/user-compagnie` → liste tous les users de la compagnie avec leurs branches
- `POST /api/user-compagnie` → invite un user (manager ou operator), valide que les branches fournies appartiennent à la compagnie, attache via pivot
- `GET  /api/user-compagnie/{id}` → détail d'un user avec ses branches
- `PUT  /api/user-compagnie/{id}` → modifie rôle et/ou branches — `sync()` remplace entièrement les branches assignées
- `DELETE /api/user-compagnie/{id}` → retire le user, détache ses branches — impossible de supprimer un admin ou soi-même

**Règles de sécurité (UserPolicy)**
- `before()` : l'admin passe partout sur les users de SA compagnie
- `update()` : impossible de modifier un autre admin (évite l'escalade de privilèges)
- `delete()` : impossible de supprimer un admin ou de se supprimer soi-même

**Points clés**
- Un admin ne peut pas être invité via ce endpoint (sécurité volontaire) — seul le `register` crée un admin
- Les `branche_ids` fournis sont toujours revalidés contre la `compagnie_id` de l'admin avant insertion
- `sync()` utilisé sur update pour éviter les incohérences de pivot

### Fichiers créés / modifiés
| Fichier | Action |
|---------|--------|
| `Http/Requests/StoreUserRequest.php` | ✅ Créé |
| `Http/Requests/UpdateUserRequest.php` | ✅ Créé |
| `Http/Resources/UserResource.php` | ✅ Mis à jour (branches, created_at) |
| `Http/Controllers/UserController.php` | ✅ Créé |
| `Policies/UserPolicy.php` | ✅ Créé |
| `routes/api.php` | ✅ Mis à jour (user-compagnie) |
| `database/seeders/UserSeeder.php` | ✅ Mis à jour (rôles + pivot branches) |

### Prochaine étape
- [ ] **StockMovementController** : entrées/sorties de stock avec vérification du périmètre branche selon le rôle
- [ ] **Transfert inter-branches** : logique `type=transfert` dans l'observer