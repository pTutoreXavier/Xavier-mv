# Projet tutoré Xavier - LP CIASIE - IUT Nancy-Charlemagne
Groupe : Mailard Louis, Toussaint Yvann, Costa Adrien, Guebel Marc

Installation :
  Prérequis :
  
    -Un serveur PHP
    -Composer
    
  1) Clonner le projet dans un repertoire local de votre machine
  2) A la racine du projet, ouvrir un terminal, 'composer install'
  3) Ouvrir le dossier bootstrap, créer un dossier conf, dans le dossier conf, créer conf.ini pour la connection à la base de données sous la forme suivante :
  
    driver = [driverSql]
    host = [adresse]
    database = [votreBase]
    username = [username]
    password = [password]
    charset = utf8
    collation = utf8_general_ci
    prefix = xv_
    
  4) Enjoy !
