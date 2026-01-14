# Dashboard FoodFacts

Ce projet est un tableau de bord permettant de visualiser des données issues de l'API OpenFoodFacts via des widgets configurables. Il est développé avec Symfony et utilise une architecture hexagonale (DDD-lite).

## 🚀 Démarrage rapide avec Docker

Pré-requis : Docker et Docker Compose installés sur votre machine.

1.  **Lancer les conteneurs :**

    ```bash
    docker compose up
    ```

    L'application sera accessible à l'adresse : `http://localhost` (ou `https://localhost` selon la configuration Caddy).

2.  **Créer un utilisateur administrateur :**

    Une fois les conteneurs lancés, exécutez la commande suivante pour créer un utilisateur (nécessaire pour se connecter) :

    ```bash
    docker exec dashboard-foodfacts-app-1 bin/console app:create-user root@example.com test
    ```

    Le mot de passe vous sera demandé ou généré lors de l'exécution de la commande.

3.  **Accéder à l'application :**

    Rendez-vous sur http://localhost/login et connectez-vous avec l'utilisateur créé.

## ✅ Exécuter les tests

Le projet utilise PHPUnit pour les tests unitaires et fonctionnels.

Pour lancer la suite de tests dans le conteneur :

```bash
docker exec dashboard-foodfacts-app-1 bin/phpunit
```

## 🏗️ Architecture

Le projet suit les principes de l'**Architecture Hexagonale** (Ports & Adapters) pour séparer le métier de l'infrastructure.

Le code source est organisé comme suit dans le dossier `src/` :

*   **Domain/** (Le Cœur) : Contient la logique métier pure, les Entités (`Widget`, `Dashboard`, `User`), les Enums, et les Interfaces (Ports) pour les repositories et services externes. Aucune dépendance à Symfony ou Doctrine ici (idéalement).
*   **Application/** (La Logique) : Contient les Cas d'Utilisation (`UseCase`) qui orchestrer la logique métier (ex: `CreateWidget`, `ResolveWidgetData`, `ReorderWidgets`). Contient aussi les DTOs.
*   **Infrastructure/** (Les Adaptateurs) : Contient les implémentations concrètes des interfaces du Domaine.
    *   `Doctrine/` : Implémentation des repositories.
    *   `Framework/` : Adaptateurs liés à Symfony.
*   **UI/** (L'Interface Utilisateur) : Point d'entrée de l'application.
    *   `Controller/` : Contrôleurs HTTP Symfony.
    *   `Command/` : Commandes CLI (ex: `app:create-user`).

Côté Frontend, **Stimulus** est utilisé pour gérer l'interactivité (Drag & Drop, chargement asynchrone des widgets) de manière légère.

## 🔒 Sécurité

Les éléments clés de la sécurité implémentés sont :

*   **Authentification** : L'accès au tableau de bord est restreint aux utilisateurs connectés (`IS_AUTHENTICATED_FULLY`).
*   **Protection CSRF** : Toutes les actions modifiant des données (création, suppression de widget, réorganisation) sont protégées par des jetons CSRF.
*   **Vérification des droits** : Les actions sur les widgets (suppression, résolution) vérifient que l'utilisateur connecté est bien le propriétaire du tableau de bord associé au widget.

## 🌐 API Interne

L'application expose quelques endpoints API utilisés principalement par le frontend (AJAX) :

*   **Résolution d'un widget** :
    *   `POST /widget/resolve/{id}`
    *   Renvoie les données calculées (ex: compte de produits) pour un widget donné.
    *   Format de réponse : JSON.

*   **Réorganisation des widgets** :
    *   `POST /widgets/reorder`
    *   Permet de sauvegarder le nouvel ordre des widgets après un Drag & Drop.
    *   Payload : `{ "order": [id1, id2, id3...] }`

