# apptuteur
Application de gestion des visites d’entreprise pour les tuteurs qui suivent les étudiants en stage ou en alternance. (Projet universitaire - Programmation Web)

## Installation du projet

### 1. Cloner le dépot

``` bash
git clone https://github.com/AdiyoFouad/apptuteur.git
cd apptuteur
```

### 2. Accéder au contener PHP

``` bash
docker compose build –-no-cache app
docker compose up -d
docker exec -it symfony_app bash
```

### 3. Installer les dépendances

Placer vous dans le répertoire /var/www/apptuteur
``` bash
composer install
```

### 4. Créer la base de données et exécuter les migrations
``` bash
symfony console doctrine:database:create
symfony console make:migration
symfony console doctrine:migrations:migrate
```

### 5. Démarrez le serveur Symfony
``` bash
symfony server:start
```

### 6. Ouvrez dans votre navigateur :
- http://localhost:8000/ → Page de démarrage de l'app
- http://localhost:8000/api → Interface Swagger d’API Platform

Accéder à phpMyAdmin via http://localhost:8081 et ajoutez un tuteur manuellement dans la base :
``` bash
INSERT INTO tuteur (nom, prenom, entreprise, email, telephone, password)
VALUES ('Dupont', 'Jean', 'Entreprise X', 'tuteur@test.com', '0601020304', 'secret');
```


