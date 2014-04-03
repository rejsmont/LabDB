FlyDB
=========================
[![Build Status](https://travis-ci.org/rejsmont/LabDB.png)](https://travis-ci.org/rejsmont/LabDB)

### Requirements:

* php 5.3
* php5-memcache and memcached server
* php5 driver for your database platform (php5-mysql, php5-pgsql)
* apache (or any other web server supporting php)

Node.js lessc is no longer required - we are using native less.php now!

### To install FlyDB, do the following:

```
$ git clone https://github.com/rejsmont/LabDB.git
$ cd LabDB
$ cp app/config/security.form.yml app/config/security.yml
$ curl -s https://getcomposer.org/installer | php
$ composer.phar install
$ app/console assetic:dump --env=prod
$ app/console doctrine:schema:create --env=prod

```

You may need to adjust permissions of app/cache and app/logs directories

```
$ APACHEUSER=`ps aux | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data' | grep -v root | head -1 | cut -d\  -f1`
$ sudo setfacl -R -m u:"$APACHEUSER":rwX -m u:`whoami`:rwX app/cache app/logs
$ sudo setfacl -dR -m u:"$APACHEUSER":rwX -m u:`whoami`:rwX app/cache app/logs

```

By default authentication uses login form against SQL database.
Please take a look at [FOS user bundle documentation](https://github.com/FriendsOfSymfony/FOSUserBundle)
to learn how to create user entries
.

To populate database with example data (including two example users) execute:

```
$ app/console doctrine:fixtures:load --fixtures=src/VIB/UserBundle/Tests/DataFixtures --fixtures=src/VIB/FliesBundle/Tests/DataFixtures --env=prod

```

Of course you will have to set up your web server and point the document root to `LabDB/web`.

### Upgrading is easy

Just pull the latest version from github and update vendors

```
$ git pull
$ composer update 

```

### Need more help?

Have a look at [Symfony framework documentation](http://symfony.com/doc/current/book/index.html) - LabDB uses Symfony
framework. You can also request support from the author. Contributions are welcome. Donations are even more welcome :)
