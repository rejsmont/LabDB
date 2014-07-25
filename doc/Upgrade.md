# Upgrading LabDB

**Always backup your database before upgrading**

Most upgrades require simply pulling the latest version from GitHub and updating the vendors:

```
$ cd LabDB
$ git pull
$ composer install
$ app/console cache:clear --env=prod
$ app/console assets:install --env=prod
$ app/console assetic:dump --env=prod
```

Verify upgrade by running builtin tests (the **test database** will be wiped when tests are run):

```
$ bin/phpunit -c app
```

Sometimes, especially on major updates it may be necessary to upgrade the database schema. First
see if an update is necessary:

```
$ app/console doctrine:schema:update --env=prod --dump-sql
```

And if you are happy with the SQL you see, run the update:

```
$ app/console doctrine:schema:update --env=prod --force
```

**Now you are up to date**
