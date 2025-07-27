# Recipe Backend API

Une API REST développée avec Symfony pour une application de gestion de recettes culinaires. Cette API permet aux utilisateurs de créer, gérer et partager leurs recettes, ainsi que d'interagir avec celles des autres utilisateurs.

## 🍳 Fonctionnalités

- **Gestion des utilisateurs**
  - Inscription et authentification
  - Système de rôles (utilisateur, admin)
  - Authentification JWT

- **Gestion des recettes**
  - Création, modification, suppression de recettes
  - Upload d'images pour les recettes
  - Catégorisation des recettes
  - Recherche et filtrage

- **Interactions sociales**
  - Système de commentaires
  - Notation des recettes
  - Mise en favoris
  - Profils utilisateurs

## 🛠️ Technologies utilisées

### Framework & Core
- **PHP 8.2+**
- **Symfony 6.4** - Framework web principal
- **Doctrine ORM** - Gestion de la base de données

### Sécurité & Authentification
- **firebase/php-jwt** - Génération et validation des tokens JWT
- **Symfony Security** - Hachage des mots de passe et gestion des rôles

### Base de données
- **MySQL/MariaDB** - Base de données principale
- **Doctrine Migrations** - Gestion des versions de la base

### Développement
- **Symfony Web Server** - Serveur de développement
- **Composer** - Gestionnaire de dépendances PHP

## 📋 Prérequis

- PHP 8.1 ou supérieur
- Composer
- MySQL 8.0 ou MariaDB 10.4+
- Extension PHP : `pdo_mysql`, `intl`, `json`

## 🚀 Installation

### 1. Cloner le repository
```bash
git clone [git@github.com:PepMama/recipe_back.git]
cd recipe_back
```

### 2. Installer les dépendances
```bash
composer install
```

### 3. Configuration de l'environnement
Créer un fichier `.env.local` :
```env
# Database
DATABASE_URL="mysql://username:password@127.0.0.1:3306/recipe_db?serverVersion=8.0"

# JWT Secret Key
JWT_SECRET_KEY=your-very-long-and-secure-secret-key-for-jwt-tokens

# App Environment
APP_ENV=dev
APP_SECRET=your-app-secret-key
```

### 4. Créer la base de données
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### 5. (Optionnel) Charger les données de test
```bash
php bin/console doctrine:fixtures:load
```

### 6. Démarrer le serveur de développement
```bash
symfony server:start
# ou
php -S localhost:8000 -t public/
```

L'API sera accessible à l'adresse : `http://localhost:8000`

## 📡 Endpoints API

### Authentification

#### Inscription
```http
POST /user/create-user
Content-Type: application/json

{
    "email": "user@example.com",
    "firstname": "John",
    "lastname": "Doe",
    "password": "securepassword",
    "roles": "ROLE_USER"
}
```

**Réponse (201) :**
```json
{
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "role": ["ROLE_USER"]
}
```

#### Connexion
```http
POST /user/login
Content-Type: application/json

{
    "email": "user@example.com",
    "password": "securepassword"
}
```

**Réponse (200) :**
```json
{
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "role": ["ROLE_USER"]
}
```

## 🗄️ Structure de la base de données

### Entités principales

- **User** - Utilisateurs de l'application
- **Recipe** - Recettes culinaires
- **Comment** - Commentaires sur les recettes
- **Rating** - Notes attribuées aux recettes
- **Favorite** - Recettes mises en favoris
- **Ingredient** - Lister les ingrédients de la recette
- **Category** - Filtrer par categorie (entrée, plat, dessert,etc)
- **Recipe_step** - 

### Relations
- Un utilisateur peut avoir plusieurs recettes (1:N)
- Un utilisateur peut commenter plusieurs recettes (1:N)
- Un utilisateur peut noter plusieurs recettes (1:N)
- Un utilisateur peut mettre plusieurs recettes en favoris (N:M)

## 🔐 Authentification JWT

L'API utilise des tokens JWT pour l'authentification. Après connexion/inscription, incluez le token dans l'header :

```http
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
```

## 🧪 Tests

Pour exécuter les tests :
```bash
php bin/phpunit
```

## 📁 Structure du projet

```
src/
├── Controller/         # Contrôleurs API
│   └── UserController.php
├── Entity/            # Entités Doctrine
│   ├── User.php
│   ├── Recipe.php
│   ├── Comment.php
│   ├── Rating.php
│   └── Favorite.php
├── Repository/        # Repositories Doctrine
├── Services/          # Services

config/
├── packages/         # Configuration des bundles
├── routes.yaml      # Routes
└── services.yaml    # Services

migrations/          # Migrations de base de données
public/             # Point d'entrée web
```

## 🔧 Configuration

### Parameters principaux (config/services.yaml)
```yaml
parameters:
    jwt_key: '%env(JWT_SECRET_KEY)%'
```

### Variables d'environnement requises
- `DATABASE_URL` - URL de connexion à la base de données
- `JWT_SECRET_KEY` - Clé secrète pour les tokens JWT
- `APP_SECRET` - Clé secrète de l'application Symfony

## 🚨 Gestion des erreurs

L'API retourne des réponses JSON structurées :

**Succès (2xx) :**
```json
{
    "data": {...},
    "message": "Success"
}
```

**Erreur (4xx/5xx) :**
```json
{
    "error": "Description de l'erreur",
    "trace": "..." // En mode développement uniquement
}
```

## 📈 Roadmap

- [x] Authentification
- [ ] CRUD complet pour les controller
- [ ] Système de commentaires
- [ ] Upload d'images
- [ ] API de recherche avancée
- [ ] Système de notifications
- [ ] Cache Redis
- [ ] Tests automatisés
- [ ] Documentation API avec Swagger

## 🤝 Contribution

1. Fork le projet
2. Créez une branche pour votre fonctionnalité (`git checkout -b feature/nouvelle-fonctionnalite`)
3. Committez vos changements (`git commit -am 'Ajout nouvelle fonctionnalité'`)
4. Push vers la branche (`git push origin feature/nouvelle-fonctionnalite`)
5. Ouvrez une Pull Request

## 📄 Licence

Ce projet est sous licence MIT.

## 👨‍💻 Auteur

[Pépin Maëlic] - [pmaelic@outlook.fr]

---

**Note :** Cette API est en cours de développement. Certaines fonctionnalités peuvent être incomplètes ou manquantes.