<?php

namespace VIB\ImapUserBundle\Manager;

use Monolog\Logger;
use VIB\ImapUserBundle\Exception\ConnectionException;

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
     * @throws \VIB\ImapUserBundle\Exceptions\ConnectionException
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

    public function getHost()
    {
        return isset($this->params['client']['host']) ?
            $this->params['client']['host'] : 'localhost';
    }

    public function getPort()
    {
        if (isset($this->params['client']['port'])) {
            $port = $this->params['client']['port'];
        } else {
            $port = $this->getEncryption() == 'ssl' ? '993' : '143';
        }
        
        return $port;
    }

    public function isSecure()
    {
        return isset($this->params['client']['secure']) ?
            $this->params['client']['secure'] : TRUE;
    }

    public function isEncrypted()
    {
        return $this->getEncryption() == 'ssl' || $this->getEncryption() == 'tls';
    }
    
    public function getEncryption()
    {
        return isset($this->params['client']['encryption']) ?
            strtolower($this->params['client']['encryption']) : 'none';
    }

    public function getValidateCert()
    {
        return isset($this->params['client']['validate_cert']) ?
            $this->params['client']['validate_cert'] : TRUE;
    }
    
    public function getNretries()
    {
        return isset($this->params['client']['n_retries']) ?
            $this->params['client']['n_retries'] : 0;
    }

    private function getImapString()
    {        
        $host = $this->getHost();
        $port = $this->getPort();
        
        $string = "{" . $host . ':' $port . '/imap';
        $string .= $this->isSecure() ? '/secure' : '';
 
        if ($this->isEncrypted()) {
            $string .= '/' . $this->getEncryption();
            $string .= $this->validateCert() ? '/validate-cert' : '/novalidate-cert';
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
        
        $ress = @imap_open($this->getImapString(), $user, $password, OP_HALFOPEN, $this->getNretries())
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
     * @throws \VIB\ImapUserBundle\Exception\ConnectionException
     */
    private function checkImapError($ress = null)
    {
        $errors = imap_errors();
        
        if (count($errors)) {
            $message = $errors[0];
            $this->err('IMAP returned an error :' . $message);
            throw new ConnectionException($message);
        }
    }
}
