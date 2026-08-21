# CineGroupe — systeme de programmation d'un groupe de cinemas

Projet realise pour le CC "Projet : site web CRUD MVC" (ME Expert IT — Applications
Intelligentes et Big Data, 2e annee). Application web CRUD construite avec **Symfony 7**,
**Doctrine ORM** et **PostgreSQL 16**.

## Sommaire

- [Architecture](#architecture)
- [Du MCD aux entites](#du-mcd-aux-entites)
- [Installation](#installation)
- [Lancer le projet](#lancer-le-projet)
- [Comptes de demonstration](#comptes-de-demonstration)
- [Tests](#tests)
- [Qualite / SonarQube](#qualite--sonarqube)
- [Simplifications assumees](#simplifications-assumees)
- [Grille d'evaluation — ou trouver chaque critere](#grille-devaluation--ou-trouver-chaque-critere)

## Architecture

Le projet est organise en couches strictes, chacune ne connaissant que la couche
immediatement inferieure :

```
src/
  Entity/        Doctrine entities (Cinema, Salle, Horaire, Film, Seance, User, ResetPasswordRequest)
  Repository/     Requetes Doctrine (recherche, pagination, requetes metier "aujourd'hui")
  Service/        Logique metier + regles de gestion, independante d'HTTP et de Twig
    Contract/     Interfaces des services (Dependency Inversion)
  Form/           FormTypes Symfony (validation + rendu des formulaires CRUD)
  Controller/
    Public/       Espace public (accueil, detail cinema)
    Admin/         Espace administrateur (CRUD cinemas/salles/films/seances)
  EventListener/  Gestion globale des exceptions (kernel.exception)
  Exception/      Exceptions metier (DomainException et sous-classes)
  DataFixtures/   Jeu de donnees de demo (compte admin + quelques cinemas/films/seances)
templates/        Vues Twig (Bootstrap 5, responsive)
migrations/       Migration Doctrine + schema.sql de reference
tests/
  Unit/           Tests unitaires des services (mocks, sans base de donnees)
  Functional/     Tests fonctionnels des controleurs (WebTestCase)
```

Points cles de conception :

- **Controleurs -> Services -> Repositories -> Entites** : les controleurs ne contiennent
  aucune requete Doctrine ni regle metier ; ils appellent des services via leurs
  **interfaces** (`Service/Contract/*Interface.php`), ce qui respecte le principe
  d'inversion de dependance (SOLID) et rend les services testables unitairement sans
  base de donnees (voir `tests/Unit/Service`).
- **Genericite** : `AbstractCrudService` (couche service) et `AbstractCrudController`
  (couche controleur) factorisent le cycle create/read/update/delete + validation +
  pagination + recherche commun aux quatre entites administrables. Chaque service/
  controleur concret ne fournit que ce qui lui est propre (classe d'entite, formulaire,
  templates, regles metier specifiques via `assertBusinessRules()`).
- **Tell, don't ask** : par exemple `Cinema::getAdresseComplete()` ou
  `Seance::isAujourdhui()` exposent un comportement plutot que de laisser l'appelant
  reconstruire la logique a partir des attributs bruts.
- **Gestion des exceptions** : les regles metier levent des exceptions dediees
  (`DuplicateEntityException`, `EntityNotFoundException`, `ValidationFailedException`,
  toutes filles de `DomainException`). Les controleurs CRUD les rattrapent localement
  pour afficher un message flash ; celles qui remonteraient malgre tout sont
  interceptees globalement par `EventListener\ExceptionListener` (`kernel.exception`),
  loggees, puis transformees en redirection propre ou en page d'erreur 404/403/500
  personnalisee (`templates/bundles/TwigBundle/Exception/`).

## Du MCD aux entites

Le MCD fourni dans l'enonce a ete traduit ainsi :

| Entite MCD | Entite Doctrine | Cle | Remarque |
|---|---|---|---|
| Cinema (nom, ville, rue, numero) | `Cinema` | `id` technique, `nom` unique | `nom` est l'identifiant "metier" du MCD (attribut souligne) ; un id technique est ajoute pour des cles etrangeres et des URL courtes/stables, pratique standard lors du passage MCD -> MLD. |
| Salle (capacite, dateConstr.) relie a Cinema via l'association "no" | `Salle` | `id` technique + contrainte unique `(cinema_id, numero)` | Dans le MCD, le numero de salle est porte par l'association avec Cinema : cardinalites 1,1 (Cinema) — 0,n (Salle). Traduit en cle "metier" composite `(cinema, numero)`, implementee comme un index unique plutot que comme cle primaire composite, pour garder des relations Doctrine simples. |
| Horaire (heureDebut, heureFin) | `Horaire` | `id` | Reutilisable par plusieurs Seance (1,1 — 0,n), fidele au MCD. |
| Film (titre, annee, genre) | `Film` | `id` | Traduction directe. |
| Seance (tarif) | `Seance` | `id` | **Extension assumee du modele** : le MCD ne porte aucune date calendaire (seul `Horaire` a une heure de debut/fin). Or l'enonce demande d'afficher "les films programmes **a la date du jour**". Un attribut `date` a donc ete ajoute a `Seance` — c'est la seule deviation par rapport au MCD fourni, necessaire pour couvrir une exigence fonctionnelle explicite. |

Une table technique supplementaire `User` (+ `ResetPasswordRequest`) a ete ajoutee,
hors MCD, pour l'authentification de l'espace administrateur (non demandee par le MCD
mais requise par la grille d'evaluation : "Gestion de l'authentification/mot de passe
oublie").

Le schema SQL complet (issu de la migration Doctrine) est documente dans
`migrations/schema.sql` et a ete **execute avec succes sur une instance PostgreSQL 16
reelle** pendant le developpement pour verifier la validite du DDL et des contraintes
(unicite `(cinema_id, numero)` testee avec un insert en conflit -> rejet confirme).

## Installation

Prerequis : PHP >= 8.2, Composer, Docker (pour PostgreSQL) ou une instance PostgreSQL
16 accessible.

```bash
composer install

# Lancer PostgreSQL + Adminer (interface web de gestion de BDD) via Docker
docker compose up -d

# Le fichier .env fourni pointe deja vers la base "cinema_crud" du docker-compose.
# Adapter DATABASE_URL si vous utilisez votre propre instance PostgreSQL.

php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate --no-interaction

# Jeu de donnees de demo (compte admin + cinemas/films/seances d'exemple)
php bin/console doctrine:fixtures:load --no-interaction
```

## Lancer le projet

```bash
symfony server:start
# ou, sans Symfony CLI :
php -S 127.0.0.1:8000 -t public
```

- Espace public : http://127.0.0.1:8000/
- Espace administrateur : http://127.0.0.1:8000/admin/login

## Comptes de demonstration

Cree par `AppFixtures` :

| Email | Mot de passe |
|---|---|
| admin@cinegroupe.local | ChangeMe123! |

## Tests

```bash
# Necessite une base de test dediee (voir .env.test), migree comme la base de dev :
php bin/console doctrine:database:create --env=test --if-not-exists
php bin/console doctrine:migrations:migrate --env=test --no-interaction

php bin/phpunit
```

- `tests/Unit/Service` : services testes en isolation totale (repositories/EntityManager/
  Validator mockes), sans dependance a une base de donnees.
- `tests/Functional/Controller` : verifie que l'accueil public repond (200) et est
  responsive, et que `/admin/*` redirige un visiteur anonyme vers la connexion mais
  reste accessible a un utilisateur `ROLE_ADMIN` authentifie.

## Qualite / SonarQube

Un fichier `sonar-project.properties` est fourni a la racine. Avec un sonar-scanner
installe et un serveur SonarQube (local ou SonarCloud) :

```bash
sonar-scanner \
  -Dsonar.host.url=http://localhost:9000 \
  -Dsonar.login=<votre_token>
```

## Simplifications assumees

- `Horaire` n'a pas d'ecran CRUD dedie dans l'enonce ; il est gere directement au sein
  du formulaire `Seance` (`SeanceType` embarque `HoraireType`), chaque seance
  possedant/editant son propre creneau. Le modele de donnees reste conforme au MCD
  (Horaire reste une entite/table a part, potentiellement partageable).
- Le service `SalleService`/`SeanceService` expose des `QueryBuilder` Doctrine aux
  controleurs (pour la pagination KnpPaginator) : une abstraction totale du
  QueryBuilder derriere l'interface de service aurait ete possible mais alourdissait
  le projet sans benefice pedagogique supplementaire pour ce format d'exercice.

## Grille d'evaluation — ou trouver chaque critere

| Critere | Ou / comment |
|---|---|
| Configuration du projet | `composer.json`, `config/packages/*.yaml`, `docker-compose.yml`, `.env` |
| Organisation des packages | `src/{Entity,Repository,Service,Controller,Form,EventListener,Exception}` |
| Bonnes pratiques (SOLID, Tell don't ask, genericite) | Interfaces `Service/Contract/*`, `AbstractCrudService`, `AbstractCrudController`, `Cinema::getAdresseComplete()`, `Seance::isAujourdhui()` |
| Documentation du code | PHPDoc sur chaque classe/entite expliquant son role et son origine MCD |
| Vues responsives | Bootstrap 5 (CDN) + `base.html.twig` (navbar `navbar-toggler`), grille `row-cols-*` |
| Couverture des exigences / code operationnel | Espace public (`Controller/Public`) + espace admin CRUD complet (`Controller/Admin`) avec pagination/recherche |
| Maitrise de la dette technique | `sonar-project.properties`, code type-hinte strict (`declare(strict_types=1)`), pas de duplication (services/controleurs generiques) |
| Gestion des exceptions | `src/Exception/*`, `EventListener/ExceptionListener`, pages d'erreur `templates/bundles/TwigBundle/Exception/` |
| Authentification / mot de passe oublie | `SecurityController`, `ResetPasswordController`, `config/packages/security.yaml`, `config/packages/reset_password.yaml` |
| Tests | `tests/Unit/Service`, `tests/Functional/Controller` |
