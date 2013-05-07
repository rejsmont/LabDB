FlyDB
=========================
"!https://travis-ci.org/rejsmont/LabDB.png!":https://travis-ci.org/rejsmont/LabDB
=========================

To install FlyDB, do the following:

```
git clone https://github.com/rejsmont/LabDB.git
cd LabDB
cp app/config/parameters.yml.default app/config/parameters.yml
curl -s https://getcomposer.org/installer | php
composer.phar install
app/console assetic:dump
```

Remember to adjust your parameters.yml.
By default authentication uses shibboleth.
Have a look into security.yml if you need other authentication method.
