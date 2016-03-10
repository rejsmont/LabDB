<?php

namespace VIB\ImapAuthenticationBundle\Manager;

use Monolog\Logger;
use Egulias\EmailValidator\EmailParser;
use Egulias\EmailValidator\EmailLexer;

use VIB\ImapAuthenticationBundle\Exception\ConnectionException;

class ImapConnection implements ImapConnectionInterface
{
    private $params;
    private $logger;
    private $emailParser;

    protected $ress;

    public function __construct(array $params, Logger $logger)
    {
        $this->params = $params;
        $this->logger = $logger;
        $this->emailParser = new EmailParser(new EmailLexer());
    }

    /**
     * @return true
     * @throws \VIB\ImapAuthenticationBundle\Exceptions\ConnectionException
     */
    public function bind($user, $password = '')
    {
        if ($this->ress === null) {
            $this->connect($user, $password);
        }

        return true;
    }

    public function getParameters()
    {
        return $this->params;
    }

    public function getHost($connection = 0)
    {
        return isset($this->params['connections'][$connection]['host']) ?
            $this->params['connections'][$connection]['host'] : 'localhost';
    }

    public function getPort($connection = 0)
    {
        if (isset($this->params['connections'][$connection]['port'])) {
            $port = $this->params['connections'][$connection]['port'];
        } else {
            $port = $this->getEncryption() == 'ssl' ? '993' : '143';
        }
        
        return $port;
    }

    public function isSecure($connection = 0)
    {
        return isset($this->params['connections'][$connection]['secure']) ?
            $this->params['connections'][$connection]['secure'] : true;
    }

    public function isEncrypted($connection = 0)
    {
        return $this->getEncryption($connection) == 'ssl' ||
                $this->getEncryption($connection) == 'tls';
    }
    
    public function getEncryption($connection = 0)
    {
        return isset($this->params['connections'][$connection]['encryption']) ?
            strtolower($this->params['connections'][$connection]['encryption']) : 'none';
    }

    public function getValidateCert($connection = 0)
    {
        return isset($this->params['connections'][$connection]['validate_cert']) ?
            $this->params['connections'][$connection]['validate_cert'] : true;
    }
    
    public function getNretries($connection = 0)
    {
        return isset($this->params['connections'][$connection]['n_retries']) ?
            $this->params['connections'][$connection]['n_retries'] : 0;
    }

    public function isEmailLogin($connection = 0)
    {
        return isset($this->params['connections'][$connection]['email_login']) ?
            $this->params['connections'][$connection]['email_login'] : false;
    }
    
    public function supports($user)
    {
        return ($this->getConnection($user) !== null);
    }
    
    private function getCannonicalUser($user, $connection = 0)
    {        
        if ($this->isEmailLogin($connection)) {
            
            return $user;
        } else {
            $parts = $this->emailParser->parse($user);
            
            return $parts['local'];
        }
    }
    
    private function getCannonicalHost($user)
    {
        $parts = $this->emailParser->parse($user);

        return $parts['domain'];
    }
    
    private function getConnection($user)
    {
        $userDomain = $this->getCannonicalHost($user);
        
        foreach ($this->params['connections'] as $index => $connection) {
            foreach ($connection['domains'] as $domain) {
                if (strtolower($domain) == strtolower($userDomain)) {
                    
                    return $index;
                }
            }
        }
        
        return null;
    }
    
    private function getImapString($connection = 0)
    {        
        $host = $this->getHost($connection);
        $port = $this->getPort($connection);
        
        $string = "{" . $host . ':' . $port . '/imap';
        $string .= $this->isSecure($connection) ? '/secure' : '';
 
        if ($this->isEncrypted($connection)) {
            $string .= '/' . $this->getEncryption($connection);
            $string .= $this->getValidateCert($connection) ? '/validate-cert' : '/novalidate-cert';
        }
        
        $string .= "}";
        
        return $string;
    }
    
    private function connect($user, $password = '')
    {
        if (empty($user) || ! is_string($user)) {
            throw new ConnectionException("Username must be provided (as a string).");
        }
        
        if (empty($password) || ! is_string($password)) {
            throw new \Exception('You must uncomment password key');
        }
        
        $connection = $this->getConnection($user);
        if (null === $connection) {
            throw new ConnectionException("No valid connection for specified user found.");
        }
        
        imap_errors();
        
        $ress = @imap_open($this->getImapString($connection),
                $this->getCannonicalUser($user, $connection), $password,
                OP_HALFOPEN, $this->getNretries($connection),
                array('DISABLE_AUTHENTICATOR' => 'GSSAPI'));
        
        $this->checkImapError($ress);
        $this->ress = $ress;

        return $this;
    }

    private function info($message)
    {
        if ($this->logger) {
            $this->logger->info($message);
        }
    }

    private function err($message)
    {
        if ($this->logger) {
            $this->logger->err($message);
        }
    }

    /**
     * Checks if there were an error during last imap call
     *
     * @throws \VIB\ImapAuthenticationBundle\Exception\ConnectionException
     */
    private function checkImapError($ress = null)
    {
        $errors = imap_errors();
        
        if ($errors) {
            $message = $errors[0];
            $this->err('IMAP returned an error :' . $message);
            throw new ConnectionException($message);
        }
    }
}
