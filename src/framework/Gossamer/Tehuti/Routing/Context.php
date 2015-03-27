<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Gossamer\Tehuti\Routing;

/**
 * Context
 *
 * @author Dave Meikle
 */
class Context {
    
    protected $request;
    
    public function __construct(Request $request) {
        $this->request = $request;
        
        print_r($request);
    }
    
    public function getRequestUri() {
        
    }
}
