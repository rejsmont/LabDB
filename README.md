LabDB
=========================
[![Build Status](https://travis-ci.org/rejsmont/LabDB.png)](https://travis-ci.org/rejsmont/LabDB)

LabDB is a laboratory inventory management system created with a strong focus
on labs using *Drosophila* as a model organism. In fact, currently most of
the available functionality is dedicated to fly stock management.

The system goes far beyond simple stock list. In fact it is a fully featured
**vial management** system, where every single vial you keep in your lab  has
its unique identifier and an entry in the database.

## Hardware requirements

For best user experience we recommend purchasing a 2D barcode scanner and a
dedicated label printer. In our setup we are using the following hardware:

* Zebra GX430t Thermal Transfer label printer
* Zebra Z-Perform 1000T Paper 51mm x 25mm with Zebra 2300 Wax ribbons
* Motorola / Symbol DS4208 SR barcode scanner

Of course you can use any other equipment. Currently the label size is
fixed at 51mm x 25mm, so please take it into account when choosing labels.

## Software Requirements:
* web server 
* php 5.3
* php5-memcache and memcached server
* php5 driver for your database platform (php5-mysql, php5-pgsql)
* apache (or any other web server supporting php)

Node.js lessc is no longer required - we are using native less.php now!

## Installation instructions for Ubuntu Server 14.04:

### Install required software

In this example we start with a minimal installation of the Ubuntu Server.
Usually you will have some of the required software already present on your
server. Install command will in such cases ignore already installed packages.
We will use apache2 web server running mod-php5 and MySQL database backend.

Let's start by installing the web server, the database server and the required PHP modules:
```
$ sudo apt-get install libapache2-mod-php5 mysql-server memcached
$ sudo apt-get install php5-intl php5-mysql php5-sqlite php5-xcache php5-memcache
$ sudo apt-get install acl git
``` 

The SQLite is required for tests and developmental mode to run.

Now that we have required software installed, let's download the LabDB:
```
$ git clone https://github.com/rejsmont/LabDB.git
$ cd LabDB
$ ln -s security.form.yml app/config/security.yml
$ curl -s https://getcomposer.org/installer | php
$ composer.phar install
```

We can run builtin tests to ensure that everything was installed properly.

```
$ bin/phpunit -c app
```

Now it's time to configure our webserver to serve the LabDB. Let's start by
setting write permissions to cache and log directories:

```
$ mkdir -p app/Resources/db
$ APACHEUSER=`ps aux | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data' | grep -v root | head -1 | cut -d\  -f1`
$ sudo setfacl -R -m u:"$APACHEUSER":rwX -m u:`whoami`:rwX app/cache app/logs app/Resources/db
$ sudo setfacl -dR -m u:"$APACHEUSER":rwX -m u:`whoami`:rwX app/cache app/logs app/Resources/db
```

Then configure apache2 and set PHP timezone:

```
$ sudo a2enmod ssl
$ sudo a2enmod rewrite
$ sudo cp doc/examples/001-labdb.conf /etc/apache2/sites-available/
$ sudo sed -i 's#/home/labdb/LabDB#'${LABROOT}'#' /etc/apache2/sites-available/001-labdb.conf
$ sudo a2dissite 000-default.conf
$ sudo a2ensite 001-labdb.conf
$ sudo sed -i 's/^;date.timezone =$/date.timezone = Europe\/Brussels/' /etc/php5/apache2/php.ini
$ cp -s doc/examples/htaccess.sample web/.htaccess
```

Finally you should restart your webserver and verify that content is served properly.

```
$ sudo service apache2 restart
```

If you have run tests, the test database has been populated with example data and you can access it.
Point your browser to the following address (replacing yourhost.yourdomain with your server's host
and domain names):

* http://yourhost.yourdomain/app_dev.php

There are two example users: "jdoe" (regular user) and "asmith" (admin). Password for both is "password".


You can also use lynx locally on the server, but first you have to install it and disable
self-signed certificate and cookie prompts.

```
$ sudo apt-get install lynx
$ sudo sed -i 's/#FORCE_SSL_PROMPT:PROMPT/FORCE_SSL_PROMPT:yes/' /etc/lynx-cur/lynx.cfg
$ sudo sed -i 's/#ACCEPT_ALL_COOKIES:FALSE/ACCEPT_ALL_COOKIES:TRUE/' /etc/lynx-cur/lynx.cfg
$ lynx http://localhost/app_dev.php
```

Now that you have verified that everything works you can deploy the production version:

```
$ app/console doctrine:schema:create --env=prod
$ app/console assets:install --env=prod
$ app/console assetic:dump --env=prod
```

By default users are authenticated against SQL database.
Please take a look at [FOS user bundle documentation](https://github.com/FriendsOfSymfony/FOSUserBundle)
to learn how to create user entries.

To populate database with example data (including two sample users) execute:

```
$ app/console doctrine:fixtures:load --fixtures=src/VIB/UserBundle/Tests/DataFixtures --fixtures=src/VIB/FliesBundle/Tests/DataFixtures --env=prod

```

## Upgrading is easy

Just pull the latest version from github and update vendors

```
$ git pull
$ composer install 

```

## Need more help?

Have a look at [Symfony framework documentation](http://symfony.com/doc/current/book/index.html) - LabDB uses Symfony
framework. You can also request support from the author. Contributions are welcome. Donations are even more welcome :)
