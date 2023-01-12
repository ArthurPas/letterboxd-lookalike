# Guide de deploiement


## Git

Pour commencer, cloner ce dépot dans un dossier 

## Symfony
Installer Symfony. Pour ce faire télécharger l'outil en ligne de commande symfony (choisissez les binaires AMD64), placez les dans un dossier (par exemple symfony) et ajoutez ce dossier dans votre $PATH, en ajoutant par exemple:
> export PATH=$PATH:$HOME/symfony
Dans votre fichier .bashrc
Puis dans un terminal ecrire 
> symfony composer update

## Lancer le serveur 
Pour ce faire aller dans le dossier où vous avez cloné le dépot puis lancer la commande
> symfony serve 

## Se connecter au serveur 
Pour se connecter aller dans votre navigateur puis taper dans l'url 127.0.0.1:8080/series

## Utilisateur admin
Pour creer un utilisateur admin il faut aller dans PhpMyAdmin se connecter à votre base et dans la table user modifier le 0 de votre profil dans l'attribut admin par un 1



