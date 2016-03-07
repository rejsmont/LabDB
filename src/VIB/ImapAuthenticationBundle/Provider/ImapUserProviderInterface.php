<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace VIB\ImapAuthenticationBundle\Provider;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Description of ImapUserProviderInterface
 *
 * @author u0078517
 */
interface ImapUserProviderInterface {
    function createUser(UsernamePasswordToken $token);
    function updateUser(UsernamePasswordToken $token);
}
