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

/**
 * ClientListener
 *
 * @author Dave Meikle
 */
class ClientListener extends AbstractListener {
    
    public function on_new_token_request(Event $event) {
        //first check to see if the token already exists for this client
        $this->container
    }
}
