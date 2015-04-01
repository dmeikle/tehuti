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

/**
 * Router
 *
 * @author Dave Meikle
 */
class Router {
    
    use \Gossamer\Tehuti\Utils\ContainerTrait;
    
    protected $routingConfig = null;
    
    protected $parser = null;
    
    public function __construct(YAMLParser $parser) {
        $this->parser = $parser;
        
        $parser->setFilePath(__CONFIG_DIRECTORY . '/routing.yml');
        
        $this->routingConfig = $parser->loadConfig();
    }
    
    public function handleRequest(SocketRequest &$request) {
       
        if(is_null($request->getComponent()) || strlen($request->getComponent()) == 0) {
           
            //no rest style URI found
            return null;
        }
        try{
            $config = ($this->loadComponentConfig($this->routingConfig[$request->getComponent()], $request->getUri()));
        }catch(\Exception $e) {
            
            return null;
        }
        
        //add any event listeners that are required for this component
        $this->container->get('EventDispatcher')->configListeners($config);
        $this->container->get('EventDispatcher')->dispatch('server', ServerEvents::COMPONENT_INITIATE, new Event(ServerEvents::COMPONENT_INITIATE, array('request' => $request)));
       
        $componentName = $config['defaults']['component'];
        $component = new $componentName($request, $this->container->get('Logger'));
        $component->setContainer($this->container);
        
        $this->container->get('EventDispatcher')->dispatch('server', ServerEvents::COMPONENT_REQUEST_START, new Event(ServerEvents::COMPONENT_REQUEST_START, array('request' => $request)));
       
        $result = $component->handleRequest($config);
        $this->container->get('EventDispatcher')->dispatch('server', ServerEvents::COMPONENT_REQUEST_COMPLETE, new Event(ServerEvents::COMPONENT_REQUEST_COMPLETE, array('request' => $request)));
       
    }
    
    protected function loadComponentConfig(array $component, $uri) {
      
        $this->parser->setFilePath(__SITE_PATH . '/' . current($component));
        $config = $this->parser->loadConfig();
        
        return $this->findNodeByUri($config, $uri);
    }   
    
    protected function findNodeByUri(array $config, SocketRequest &$request) {
        $uriComparator = new URIComparator();
        $offset = $uriComparator->findPattern($config, $request->getUri());
        
        if(is_null($offset) || strlen($offset) == 0) {
            throw new \Gossamer\Tehuti\Exceptions\ConfigNodeNotFoundException();
        }
        
        $request->setYmlKey($uriComparator->findPattern($config, $request->getUri()));
        
        return $config[$request->getYmlKey()];        
    }
    


    protected function getContext(SocketRequest $request) {
        if(!is_null($request->getAttribute('ServerAuthToken'))) {
            return new ServerContext($request);
        } else {
            return new ClientContext($request);
        }
    }
}
