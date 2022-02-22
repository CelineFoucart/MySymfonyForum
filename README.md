# MySymfonyForum

MySymfonyForum est un forum développé en symfony. 

## Prérequis
* PHP >=8.0.2
* Composer
* Symfony CLI
* Mariadb

## Installation de la base de données
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migration:migrate
```
## Lancer l'environnement de développement

```bash
symfony serve -d
```

## TODO 
* refactorisation
* affiner les rôles : 
ROLE_FULL_ADMIN, ROLE_ADMIN, ROLE_FULL_MODERATOR, ROLE_MODERATOR, ROLE_USER, ROLE_USER_LIMITED ?

## License
[MIT](https://choosealicense.com/licenses/mit/)
