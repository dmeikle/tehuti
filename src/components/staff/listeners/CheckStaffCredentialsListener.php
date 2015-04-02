<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
namespace components\staff\listeners;

use Gossamer\Horus\EventListeners\AbstractListener;
use Gossamer\Horus\EventListeners\Event;
use Gossamer\Tehuti\Tokens\ClientToken;
use Gossamer\Tehuti\Clients\Client;

/**
 * StaffListener
 *
 * @author Dave Meikle
 */
class CheckStaffCredentialsListener extends AbstractListener{
    
    public function on_component_request_start(Event &$event) {
        
        $requestToken = new ClientToken(new Client());
        $requestToken->setTokenString($event->getParam('request')->getToken());
        
        $clientToken = $event->getParam('TokenFactory')->checkToken($requestToken);       
        
        $this->request->setAttribute('clientToken', $clientToken);
      
        $event->setParam('clientToken', $clientToken);
        echo "request in credentialslistener\r\n";
        print_r($this->request);
    }
    
}
