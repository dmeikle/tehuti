<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Gossamer\Tehuti\Core;

/**
 * ServerView
 *
 * @author Dave Meikle
 */
class ServerView extends AbstractView {
    
    public function render(array $message) {
        
        $response = new Response();
        
        $response->setRespondToServer(true);
        $response->setMessage(array('message' => $message['message']));
        
        return $response;
        
    }


}
