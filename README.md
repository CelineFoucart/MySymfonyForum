# MySymfonyForum

MySymfonyForum est un forum développé en symfony. 

## Environnement de développement

### Prérequis

* PHP >=8.0.2
* Composer
* Symfony CLI
* Mariadb
* Docker
* Docker-compose

### Installation de la base de données

```bash
symfony console doctrine:migrations:migrate
symfony console doctrine:fixtures:load
```
### Lancer l'environnement de développement

```bash
symfony console make:docker:database
docker-compose up -d
symfony serve -d
```

## TODO
* affiner les rôles : 
ROLE_FULL_ADMIN, ROLE_ADMIN, ROLE_FULL_MODERATOR, ROLE_MODERATOR, ROLE_USER, ROLE_USER_LIMITED ?

## License
[MIT](https://choosealicense.com/licenses/mit/)
