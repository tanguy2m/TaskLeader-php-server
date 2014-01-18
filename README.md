TaskLeader-php-server
=====================

PHP server compliant to TaskLeader API and providing TaskLeader HTML client files.

Installation
============

1. Git clone / unzip repository
2. php composer.phar install
3. Mettre à jour l'instruction RewriteBase du .htaccess avec le sous dossier éventuel de l'URL 
4. Git clone / unzip repository TaskLeader-html dans /src/client/
5. Faire pointer le VirtualHost ou l'alias vers /src/
6. Modifier le fichier src/conf.inc.php