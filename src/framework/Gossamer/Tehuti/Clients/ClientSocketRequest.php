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

use Gossamer\Tehuti\Core\SocketRequest;

/**
 * ClientRequest
 *
 * @author Dave Meikle
 */

class ClientSocketRequest extends SocketRequest {

    private $clientId;
    
    private $requestParameters;
    
    public function __construct($clientId, $receivedText) {
       
        $this->clientId = $clientId;
        
        $header = $this->parseHeader($receivedText);
       
        $this->setComponent($this->getComponentName());
        
    }

    protected function parseHeader($receivedText) {
        
        $header = json_decode($receivedText, true); //json decode 
        if(!is_array($header)) {
            return;
        }
        if(array_key_exists('uri', $header)) {
            $this->uri = $header['uri'];
            unset($header['uri']);
        }
        
        $this->requestParameters = $header;
    }
    

    
    public function getClientId() {
        return $this->clientId;
    }

    public function setClientId($clientId) {
        $this->clientId = $clientId;
        
        return $this;
    }

    public function getRequestParameters() {
        return $this->requestParameters;
    }

    public function setRequestParameters($requestParameters) {
        $this->requestParameters = $requestParameters;
        
        return $this;
    }



}