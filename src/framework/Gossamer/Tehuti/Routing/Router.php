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

use Gossamer\Tehuti\Utils\YAMLParser;
use Gossamer\Horus\Core\Request;

/**
 * Router
 *
 * @author Dave Meikle
 */
class Router {
    
    private $config = null;
    
    public function __construct(YAMLParser $parser) {
        $parser->setFilePath(__CONFIG_DIRECTORY . '/routing.yml');
        
        $this->config = $parser->loadConfig();
    }
    
    public function getHandler(Request $request) {
        print_r($this->config);
    }
    
    private function getContext(Request $request) {
        if(!is_null($request->getAttribute('ServerAuthToken'))) {
            return new ServerContext($request);
        } else {
            return new ClientContext($request);
        }
    }
}
