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
 * Response
 *
 * @author Dave Meikle
 */
class Response {
    
    private $respondToServer = false;
    
    private $message = null;
    
    public function getRespondToServer() {
        return $this->respondToServer;
    }

    public function getMessage() {
        return $this->message;
    }

    public function setRespondToServer($respondToServer) {
        $this->respondToServer = $respondToServer;
        return $this;
    }

    public function setMessage(array $message) {
        $this->message = $message;
        return $this;
    }


}
