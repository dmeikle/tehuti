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
 * ClientFactory
 *
 * @author Dave Meikle
 */
class ClientFactory {
    
    private $clientList = array();
    
    public function getClient($id) {
        if(!array_key_exists($id, $this->clientList)) {
            return null;
        }
        
        return $this->clientList[$id]->getClient();
    }
    
    public function addClient(Client $client) {
        $clientTicket = new ClientTicket($client);
        
        $this->clientList[$client->getId()] = $clientTicket;
    }
}
