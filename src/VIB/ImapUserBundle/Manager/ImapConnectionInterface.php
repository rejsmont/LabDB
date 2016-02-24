<?php

namespace VIB\ImapUserBundle\Manager;

use Monolog\Logger;

interface ImapConnectionInterface
{
    function __construct(array $params, Logger $logger);
    function bind($user, $password = '');
    function getParameters();
    function getHost();
    function getPort();
    function isSecure();
    function isEncrypted();
    function getEncryption();
    function getValidateCert();
    function getNretries();
}
