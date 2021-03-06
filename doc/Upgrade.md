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

And if you are happy with the SQL you see, run the update and clear memcached cache:

```
$ app/console doctrine:schema:update --env=prod --force
$ echo 'flush_all' | nc localhost 11211
```

**Now you are up to date**

## If something goes wrong

Sometimes, especially when you have skipped some upgrades cache contants may prevent upgrade from succeeding. If you get an error during your upgrade, try manually clearing cache:

```
$ rm -rf app/cache/*
$ echo 'flush_all' | nc localhost 11211
```

After these steps, repeat the whole upgrade procedure and you should be all set.
