# MyForum

faire un forum en Symfony. 

## TODO : forum pages
* PAGE FORUM => afficher les topics avec pagination
* PAGE TOPIC => afficher les messages d'un topic avec pagination
* PAGE INDEX => categoryOrder => admin peut choisir ordre d'affichage des catégories

=> ***Les forums et les catégories organisés en fonction des permissions***

## fonctionnalités : utilisateur
* PAGE LOGIN
* PAGE REGISTER
* PAGE LISTE DES MEMBRES
* PAGE PROFIL
* PAGE MON COMPTE
* PAGE MESSAGERIE PRIVEE

## fonctionnalités : permissions
* permission de groupe
* chaque membre peut appartenir à plusieurs groupes
* admin peut ajouter groupe
* groupe par defaut : administrateurs, modérateurs généraux, utilisateurs inscrit

## fonctionnalités : gestion
* panneau d'administration
* action de modération
* action d'édition de ses messages et de création

## type de permission : 
* ROLE_FULL_ADMIN 
* ROLE_ADMIN
* ROLE_FULL_MODERATOR
* ROLE_MODERATOR
* ROLE_USER_STANDARD
* ROLE_USER_LIMITED
