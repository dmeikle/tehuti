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
use Gossamer\Tehuti\Core\SocketRequest;
use Gossamer\Tehuti\Servers\ServerEvents;
use Gossamer\Horus\EventListeners\Event;
use Gossamer\Tehuti\Exceptions\ConfigNodeNotFoundException;

/**
 * ServiceRouter
 *
 * @author Dave Meikle
 */
class ServiceRouter extends Router{
    
    private $config = null;
    
    public function __construct(YAMLParser $parser) {
        echo "new servicerouter\r\n";
        //first init the parent parameters
        parent::__construct($parser);
        //load all configurations in 1 shot since we want to keep the
        //config loaded to avoid file system access on a busy system
        $this->initComponentConfigurations($parser);
    }
    
    private function initComponentConfigurations(YAMLParser $parser) {

        foreach($this->routingConfig as $component => $filePath) {           
            $this->parser->setFilePath(__SITE_PATH . '/' . current($filePath));
            $config = $this->parser->loadConfig();
            
            $this->config[$component] = $config;
        }
        
    }
    
    public function handleRequest(SocketRequest &$request) {
      
        if(is_null($request->getComponent()) || strlen($request->getComponent()) == 0) {
           echo "no component found\r\n";
            //no rest style URI found
            return null;
        }
        try{
            //get the configuration for this component 
            $config = $this->config[$request->getComponent()];
        }catch(\Exception $e) {
            
            return null;
        }
  
        //add any event listeners that are required for this component
        $this->container->get('EventDispatcher')->configListeners($config);
        $this->container->get('EventDispatcher')->dispatch('server', ServerEvents::COMPONENT_INITIATE, new Event(ServerEvents::COMPONENT_INITIATE, array('request' => $request)));
        
        $nodeConfig = $this->findNodeByUri($config, $request);
        
        $componentName = $nodeConfig['defaults']['component'];
        $component = new $componentName($request, $this->container->get('Logger'));
        $component->setContainer($this->container);
        
        $this->container->get('EventDispatcher')->dispatch('server', ServerEvents::COMPONENT_REQUEST_START, new Event(ServerEvents::COMPONENT_REQUEST_START, array('request' => $request)));
       
        $result = $component->handleRequest($nodeConfig);
        $this->container->get('EventDispatcher')->dispatch('server', ServerEvents::COMPONENT_REQUEST_COMPLETE, new Event(ServerEvents::COMPONENT_REQUEST_COMPLETE, array('request' => $request)));
      
        return $result;
    }
}
