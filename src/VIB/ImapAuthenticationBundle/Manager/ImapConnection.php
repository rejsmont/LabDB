<?php

namespace VIB\ImapAuthenticationBundle\Manager;

use Monolog\Logger;
use VIB\ImapAuthenticationBundle\Exception\ConnectionException;

class ImapConnection implements ImapConnectionInterface
{
    private $params;
    private $logger;

    protected $ress;

    public function __construct(array $params, Logger $logger)
    {
        $this->params = $params;
        $this->logger = $logger;
    }

    /**
     * @return true
     * @throws \VIB\ImapAuthenticationBundle\Exceptions\ConnectionException
     */
    public function bind($user, $password = '')
    {
        if ($this->ress === null) {
            //$this->connect($user, $password);
        }

        return true;
    }

    public function getParameters()
    {
        return $this->params;
    }

    public function getHost()
    {
        return isset($this->params['connection']['host']) ?
            $this->params['connection']['host'] : 'localhost';
    }

    public function getPort()
    {
        if (isset($this->params['connection']['port'])) {
            $port = $this->params['connection']['port'];
        } else {
            $port = $this->getEncryption() == 'ssl' ? '993' : '143';
        }
        
        return $port;
    }

    public function isSecure()
    {
        return isset($this->params['connection']['secure']) ?
            $this->params['connection']['secure'] : TRUE;
    }

    public function isEncrypted()
    {
        return $this->getEncryption() == 'ssl' || $this->getEncryption() == 'tls';
    }
    
    public function getEncryption()
    {
        return isset($this->params['connection']['encryption']) ?
            strtolower($this->params['connection']['encryption']) : 'none';
    }

    public function getValidateCert()
    {
        return isset($this->params['connection']['validate_cert']) ?
            $this->params['connection']['validate_cert'] : TRUE;
    }
    
    public function getNretries()
    {
        return isset($this->params['connection']['n_retries']) ?
            $this->params['connection']['n_retries'] : 0;
    }

    private function getImapString()
    {        
        $host = $this->getHost();
        $port = $this->getPort();
        
        $string = "{" . $host . ':' . $port . '/imap';
        $string .= $this->isSecure() ? '/secure' : '';
 
        if ($this->isEncrypted()) {
            $string .= '/' . $this->getEncryption();
            $string .= $this->getValidateCert() ? '/validate-cert' : '/novalidate-cert';
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
        imap_errors();
        
        $ress = @imap_open($this->getImapString(), $user, $password, OP_HALFOPEN, $this->getNretries());
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
