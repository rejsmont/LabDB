FlyDB
=========================
[![Build Status](https://travis-ci.org/rejsmont/LabDB.png)](https://travis-ci.org/rejsmont/LabDB)

To install FlyDB, do the following:

```
git clone https://github.com/rejsmont/LabDB.git
cd LabDB
cp app/config/parameters.default.yml app/config/parameters.yml
cp app/config/security.form.yml app/config/security.yml
curl -s https://getcomposer.org/installer | php
composer.phar install
app/console assetic:dump
```

Remember to adjust your parameters.yml.
By default authentication uses login form against SQL database.
Please take a look at FOS user bundle documentation to learn how
to create user entries.
