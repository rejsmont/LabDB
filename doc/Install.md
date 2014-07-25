# Installation instructions for Ubuntu Server 14.04:

Before you install, please read the [requirements] (Requirements.md).

### Install the required software

In this example we start with a minimal installation of Ubuntu Server 14.04.
Usually you will have some of the required software already present on your
server. Install command will in such cases ignore already installed packages.
We will use apache2 web server running mod-php5 and MySQL database backend.

Let's start by installing the web server, the database server and the required PHP modules:

```
$ sudo apt-get install libapache2-mod-php5 mysql-server memcached
$ sudo apt-get install php5-intl php5-mysql php5-sqlite php5-xcache php5-memcache
$ sudo apt-get install default-jre acl git
``` 

The SQLite is required for tests and developmental mode to run.

### Install the LabDB

Now that you have required software installed, let's download the LabDB:

```
$ git clone https://github.com/rejsmont/LabDB.git
$ cd LabDB
$ ln -s security.form.yml app/config/security.yml
$ curl -s https://getcomposer.org/installer | php
$ php composer.phar install
```

You can run builtin tests to ensure that everything was installed properly.

```
$ bin/phpunit -c app
```

### Configure your webserver

Now it's time to configure your webserver to serve the LabDB. Let's start by
setting write permissions to cache, log and test database directories:

```
$ mkdir -p app/Resources/db
$ APACHEUSER=`ps aux | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data' | grep -v root | head -1 | cut -d\  -f1`
$ sudo setfacl -R -m u:"$APACHEUSER":rwX -m u:`whoami`:rwX app/cache app/logs app/Resources/db
$ sudo setfacl -dR -m u:"$APACHEUSER":rwX -m u:`whoami`:rwX app/cache app/logs app/Resources/db
```

Then configure apache2 and set PHP timezone:

```
$ sudo a2enmod ssl rewrite
$ sudo cp doc/examples/001-labdb.conf /etc/apache2/sites-available/
$ sudo sed -i 's#/home/labdb/LabDB#'`pwd`'#' /etc/apache2/sites-available/001-labdb.conf
$ sudo a2dissite 000-default.conf
$ sudo a2ensite 001-labdb.conf
$ sudo sed -i 's/^;date.timezone =$/date.timezone = Europe\/Brussels/' /etc/php5/apache2/php.ini
$ cp doc/examples/htaccess.sample web/.htaccess
```

Finally you should restart your webserver and verify that content is served properly.

```
$ sudo service apache2 restart
```

### Test your installation (optional)

If you have run tests, the test database has been populated with example data and you can
access it. Since the test version is only accessible from to localhost, first you have to
install lynx and disable self-signed certificate and cookie prompts.

Alternatively you can setup [ssh port forwarding]
(https://help.ubuntu.com/community/SSH/OpenSSH/PortForwarding)
and access the test database from your computer.

```
$ sudo apt-get install lynx
$ sudo sed -i 's/#FORCE_SSL_PROMPT:PROMPT/FORCE_SSL_PROMPT:yes/' /etc/lynx-cur/lynx.cfg
$ sudo sed -i 's/#ACCEPT_ALL_COOKIES:FALSE/ACCEPT_ALL_COOKIES:TRUE/' /etc/lynx-cur/lynx.cfg
$ lynx http://localhost/app_dev.php
```

There are two example users: "jdoe" (regular user) and "asmith" (admin).
Password for both is "password".

### Deploy the production version

Now that you have verified that everything works you can deploy the production version:

```
$ mysql -u root -e "CREATE database labdb"
$ app/console cache:clear --env=prod
$ app/console doctrine:schema:create --env=prod
$ app/console assets:install --env=prod
$ app/console assetic:dump --env=prod
```

To populate database with example data (including two sample users) execute:

```
$ app/console doctrine:fixtures:load \
  --fixtures=src/VIB/UserBundle/Tests/DataFixtures \
  --fixtures=src/VIB/FliesBundle/Tests/DataFixtures \
  --env=prod
```

Now you can access the production version under

http://yourhost.yourdomain/

Replace yourhost.yourdomain with your server's host and domain names.

**ENJOY**
