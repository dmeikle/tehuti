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

use Gossamer\Horus\EventListeners\AbstractListener;
use Gossamer\Horus\EventListeners\Event;

/**
 * ConnectionListener
 *
 * @author Dave Meikle
 */
class ConnectionListener extends AbstractListener{
    
    /**
     * used to determine the type of connection received - whether
     * it is client or server
     * 
     * @param Event $event
     */
    public function on_new_connection(Event $event) {
        
        $request = $event->getParam('request');
        
        $serverAuthToken = $request->getHeader('ServerAuthToken');
        $event->getParam('request')->setAttribute('isServer', ((is_null($serverAuthToken))? false : true));
       
    }
}
