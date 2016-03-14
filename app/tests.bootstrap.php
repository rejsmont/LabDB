<?php

/*
 * Copyright 2013 Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

if (false !== ($env = getenv('BOOTSTRAP_CLEAR_CACHE_ENV'))) {
    passthru(sprintf(
        'php "%s/console" cache:clear --env=%s --no-warmup',
        __DIR__,
        $env
    ));
}

if (false !== ($env = getenv('BOOTSTRAP_TEST_ENV'))) {
    passthru(sprintf(
        'php "%s/console" test:bootstrap --env=%s',
        __DIR__,
        $env
    ));
}

if (false !== ($env = getenv('BOOTSTRAP_DROP_DB_ENV'))) {
    passthru(sprintf(
        'php "%s/console" doctrine:schema:drop --force --env=%s',
        __DIR__,
        $env
    ));
}

if (false !== ($env = getenv('BOOTSTRAP_CREATE_DB_ENV'))) {
    passthru(sprintf(
        'php "%s/console" doctrine:schema:create --env=%s',
        __DIR__,
        $env
    ));
}

if (false !== ($env = getenv('BOOTSTRAP_LOAD_FIXTURES_ENV'))) {
    passthru(sprintf(
        'php "%s/console" doctrine:fixtures:load --no-interaction --fixtures=%s/../src/VIB/UserBundle/Tests/DataFixtures --fixtures=%s/../src/VIB/FliesBundle/Tests/DataFixtures --env=%s',
        __DIR__,
        __DIR__,
        __DIR__,
        $env
    ));
}

require __DIR__.'/bootstrap.php.cache';
