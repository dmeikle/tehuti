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

/**
 * ClientTicket
 *
 * @author Dave Meikle
 */
class ClientTicket {
    
    private $client;
    
    private $decayTime;
    
    private $token;
    
    public function getToken() {
        return $this->token;
    }

    public function setToken($token) {
        $this->token = $token;
        
        return $this;
    }

        
    public function __construct(Client $client) {
        $this->client = $client;
        $this->decayTime = strtotime("+ 20 minutes");
    }
    
    public function getClient() {
        return $this->client;
    }

    public function getDecayTime() {
        return $this->decayTime;
    }

    public function setClient(Client $client) {
        $this->client = $client;
        
        return $this;
    }

    public function setDecayTime($decayTime) {
        $this->decayTime = $decayTime;
        
        return $this;
    }


}
