<?php

namespace VIB\ImapUserBundle\Manager;

use Monolog\Logger;

interface ImapConnectionInterface
{
    function __construct(array $params, Logger $logger);
    function search(array $params);
    function bind($user_dn, $password);
    function getParameters();
    function getHost();
    function getPort();
    function getBaseDn($index);
    function getFilter($index);
    function getNameAttribute($index);
    function getUserAttribute($index);
    function getErrno($resource = null);
    function getError($resource = null);
}
