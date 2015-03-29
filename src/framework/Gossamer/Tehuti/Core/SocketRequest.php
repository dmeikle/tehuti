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

use Gossamer\Horus\Core\Request;

/**
 * SocketRequest
 *
 * @author Dave Meikle
 */
class SocketRequest extends Request {
    
    protected $headers;
    
    protected $uri;
    
    protected $component = null;

    protected $ymlKey;
    

    public function __construct($header) {
        
        $this->parseHeader($header);
        $this->parseUri();
        $this->setComponent($this->getComponentName());
        
    }
    
    public function getComponent() {
        return $this->component;
    }

    public function getYmlKey() {
        return $this->ymlKey;
    }

    public function setComponent($component) {
        $this->component = $component;
    }

    public function setYmlKey($ymlKey) {
        $this->ymlKey = $ymlKey;
    }

    protected function parseUri() {
        $host = $this->headers['Host'];
        
        $pieces = explode('/', $host);
        //knock the URL and port off
        array_shift($pieces);
        
        $this->uri = implode('/', $pieces);
    }
    
    public function getUri() {
        return $this->uri;
    }
    
    private function getComponentName() {
         
        $pieces = explode('/', $this->getUri());
        
        return array_shift($pieces);
    }
    
    protected function parseHeader($receivedHeader) {
        $this->headers = array();
       
        $lines = preg_split("/\r\n/", $receivedHeader);
        
        foreach($lines as $line)
        {
            $line = chop($line);
            if(preg_match('/\A(\S+): (.*)\z/', $line, $matches))
            {               
                $this->headers[$matches[1]] = $matches[2];
            }
        }
    }
    
    public function getHeader($key) {
        if(array_key_exists($key, $this->headers)) {
            return $this->headers[$key];
        }
        
        return null; 
    }
}
