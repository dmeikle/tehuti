<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Gossamer\Tehuti\Clients;

use Gossamer\Aker\Components\Security\Core\ClientInterface;

/**
 * Client
 *
 * @author Dave Meikle
 */
class Client implements ClientInterface {

    private $id;
    private $ipAddress;
    private $password;
    private $roles;
    private $status;
    private $credentials;

    public function getId() {
        return $this->id;
    }

    public function getIpAddress() {
        return $this->ipAddress;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getRoles() {
        return $this->roles;
    }

    public function getStatus() {
        return $this->status;
    }

    public function getCredentials() {
        return $this->credentials;
    }

    public function setId($id) {
        $this->id = $id;
        
        return $this;
    }

    public function setIpAddress($ipAddress) {
        $this->ipAddress = $ipAddress;
        
        return $this;
    }

    public function setPassword($password) {
        $this->password = $password;
        
        return $this;
    }

    public function setRoles(array $roles) {
        $this->roles = $roles;
        
        return $this;
    }

    public function setStatus($status) {
        $this->status = $status;
        
        return $this;
    }

    public function setCredentials($credentials) {
        $this->credentials = $credentials;
        
        return $this;
    }

}
