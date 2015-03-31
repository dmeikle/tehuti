<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Gossamer\Tehuti\EventListeners;

use Gossamer\Horus\EventListeners\Event;
use Gossamer\Horus\EventListeners\AbstractListener;
use Gossamer\Tehuti\Tokens\ClientToken;
use Gossamer\Tehuti\Clients\Client;
use Gossamer\Tehuti\Core\SocketRequest;

/**
 * ClientTokenListener
 *
 * @author Dave Meikle
 */
class CheckClientCredentialsListener extends AbstractListener{
    
    public function on_client_connect(Event $event) {
       
        $requestToken = new ClientToken(new Client());
        $requestToken->setTokenString($event->getParam('request')->getToken());
        echo "request token: ".$event->getParam('request')->getToken()."\r\n";
        $clientToken = $event->getParam('TokenFactory')->checkToken($requestToken);
        print_r($clientToken);
        $event->setParam('clientToken', $clientToken);
    }
    
    
}
