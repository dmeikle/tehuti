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
 * ResponseList
 *
 * @author Dave Meikle
 */
class ResponseList extends Response{
    
    private $responses = array();
    
  
    public function __construct(array $responses = null) {
        if(!is_null($responses)) {
            $this->responses = $responses;
        }
    }
    
    public function add(Response $response) {
        $this->responses[] = $response->toArray();
    }
    
    public function toArray() {
        return array('type' => 'list', 'rows' => $this->responses);
    }
   

}
