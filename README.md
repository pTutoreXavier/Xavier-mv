# Projet tutoré Xavier - LP CIASIE - IUT Nancy-Charlemagne
Groupe : Mailard Louis, Toussaint Yvann, Costa Adrien, Guebel Marc

### Pré-requis

Avoir docker, docker-compose et composer

### Installation

Après avoir récupérer le dépot Git, se placer à la racine, ouvrir un terminal et exécuter la commande:
```
docker-compose up -d
```
Afficher les machines docker avec:
```
docker ps -a
```
Dans la machine xavier-mv-master_application, exécuter:
```
composer install
composer dump-autoload -o
```

Dans le fichier /etc/hosts de votre machine hôte ajouter:
```
127.0.0.1 xavier.local
```

## Utilisation

L'application est accessible avec l'url: xavier.local (port 10080 par défaut)
