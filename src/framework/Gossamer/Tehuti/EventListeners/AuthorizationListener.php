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

/**
 * AuthorizationListener
 *
 * @author Dave Meikle
 */
class CheckServerCredentialsListener {
   
    public function on_client_server_connect(Event $event) {
        echo "on client_server_connect\r\n";
    }
}
