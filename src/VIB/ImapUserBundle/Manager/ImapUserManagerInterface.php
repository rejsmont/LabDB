<?php

namespace VIB\ImapUserBundle\Manager;

interface ImapUserManagerInterface
{
  function __construct(ImapConnectionInterface $conn);
  function exists($username);
  function auth();
  function doPass();
  function getDn();
  function getCn();
  function getEmail();
  function getAttributes();
  function getLdapUser();
  function getDisplayName();
  function getGivenName();
  function getSurname();
  function getUsername();
  function getRoles();
  function setUsername($username);
  function setPassword($password);
}
