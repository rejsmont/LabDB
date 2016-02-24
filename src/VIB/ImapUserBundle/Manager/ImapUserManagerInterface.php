<?php

namespace VIB\ImapUserBundle\Manager;

interface ImapUserManagerInterface
{
  function __construct(ImapConnectionInterface $conn);
  function exists($username);
  function auth();
  function getUsername();
  function getRoles();
  function setUsername($username);
  function setPassword($password);
}
