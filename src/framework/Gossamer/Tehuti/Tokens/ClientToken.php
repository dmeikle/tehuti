<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
namespace Gossamer\Tehuti\Tokens;

use Gossamer\Aker\Components\Security\Core\SecurityToken;
use Gossamer\Aker\Components\Security\Core\Client;

/**
 * ClientToken
 *
 * @author Dave Meikle
 */
class ClientToken extends SecurityToken {

    
    protected $tokenString;
    protected $tokenTimestamp;
  

    /**
     * 
     * @param \Gossamer\Aker\Components\Security\Core\Client $client
     */
    public function __construct(Client $client) {
        $this->setClient($client);
        $this->tokenTimestamp = time();
        
    }

    
    /**
     * accessor 
     * 
     * @param string
     */
    public function setIPAddress($ipAddress) {
        $this->getClient()->setIpAddress($ipAddress);
    }

    /**
     * accessor 
     * 
     * @param string
     */
    public function setTokenString($token) {
        $this->tokenString = $token;
    }

    /**
     * accessor 
     * 
     * @return string
     */
    public function getTimestamp() {
        return $this->tokenTimestamp;
    }

    /**
     * accessor 
     * 
     * @return string
     */
    public function toString() {
        
        return $this->getClient()->getIpAddress() . '|' . $this->getClient()->getCredentials() . '|' . $this->getClient()->getId();
    }

    /**
     * accessor 
     * 
     * @param string
     */
    public function setCredentials($credentials) {
        $this->credentials = $credentials;
    }

    /**
     * accessor 
     * 
     * @param int
     */
    public function setClientId($id) {
        $this->clientId = $id;
    }

    /**
     * accessor 
     * 
     * @return encrypted string
     */
    public function generateTokenString() {
        $this->tokenString = crypt($this->toString());

        return $this->tokenString;
    }

    /**
     * accessor 
     * 
     * @return string
     */
    public function getTokenString() {
        return $this->tokenString;
    }


}
