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
class ClientView extends AbstractView {
    
    public function render(array $message) {
        
        $response = new Response();
        
        $response->setMessage(array('type' => 'usermsg', 'name' => 'server', 'message' => $message['message'], 'color' => '#000000'));
        
        return $response;
        
    }


}
