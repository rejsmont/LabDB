LabDB using Symfony 2
=====================

1) Pre installation
-------------------

Check out this project from GitHUB.
Setup PostgreSQL 9. See app/config/parameters.ini for database configuration.
Setup Apache2 virtual host.

2) Installation
---------------

### a) Check your System Configuration

Before you begin, make sure that your local system is properly configured
for Symfony. To do this, execute the following:

    php app/check.php

If you get any warnings or recommendations, fix these now before moving on.

### b) Install the Vendor Libraries

Run the following:

    php bin/vendors install

Note that you **must** have git installed and be able to execute the `git`
command to execute this script.

### c) Create database and ACL schema

    php app/console doctrine:schema:create
    php app/console init:acl

### d) Access the Application via the Browser

http://localhost/app_dev.php/

3) Post installation
---------------

Enjoy!
