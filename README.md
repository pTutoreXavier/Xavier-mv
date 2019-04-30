# Projet tutoré Xavier - LP CIASIE - IUT Nancy-Charlemagne
Groupe : Mailard Louis, Toussaint Yvann, Costa Adrien, Guebel Marc

### Pré-requis

Avoir docker et docker-compose

### Installation

Récupération du dépôt:
```
git clone https://github.com/pTutoreXavier/Xavier-mv.git
```

A la racine du projet:
```
docker-compose up -d
docker ps -a
docker exec -it [CONTAINER ID] bash
```

Une fois dans la machine docker:
```
composer install
composer dump-autoload -o
```

Dans la machine hôte, ajout du virtualhost:
```
nano /etc/hosts
```
et ajouter: 127.0.0.1 xavier.local

Dans un navigateur:

localhost:8080 pour accéder à la base de données et pour importer le sql

xavier.local:10080 (port par défaut) pour accéder à l'application

## Utilisation

L'application est accessible avec l'url: xavier.local (port 10080 par défaut)
