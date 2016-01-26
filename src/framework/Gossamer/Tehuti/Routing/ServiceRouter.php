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
class ServiceRouter extends Router {

    private $config = null;

    public function __construct(YAMLParser $parser) {
        //first init the parent parameters
        parent::__construct($parser);
        $this->addDebug('loading component config' . "\r\n");
        //load all configurations in 1 shot since we want to keep the
        //config loaded to avoid file system access on a busy system
        $this->initComponentConfigurations($parser);
    }

    private function initComponentConfigurations(YAMLParser $parser) {
        foreach ($this->routingConfig as $component => $filePath) {
            $this->addDebug('path: ' . __SITE_PATH . '/' . current($filePath) . "\r\n");
            $this->parser->setFilePath(__SITE_PATH . '/' . current($filePath));
            $config = $this->parser->loadConfig();
            $this->addDebug("adding $component\r\n");
            $this->config[$component] = $config;
        }
    }

    public function handleRequest(SocketRequest &$request) {

        if (is_null($request->getComponent()) || strlen($request->getComponent()) == 0) {

            //no rest style URI found
            return null;
        }

        try {
            //get the configuration for this component
            $config = $this->config[$request->getComponent()];
        } catch (\Exception $e) {

            return null;
        }

        if ($config == null) {
            $this->addDebug('config was null for ' . $request->getComponent() . ' - returning from handleRequest');

            return null;
        }

        //add any event listeners that are required for this component
        $this->container->get('EventDispatcher')->configListeners($config);
        $this->container->get('EventDispatcher')->dispatch('server', ServerEvents::COMPONENT_INITIATE, new Event(ServerEvents::COMPONENT_INITIATE, array('request' => $request)));

        $nodeConfig = $this->findNodeByUri($config, $request);
        $request->setComponentConfig($nodeConfig);
        $this->setNamespace($nodeConfig, $request);
        //we need a local-only dispatcher so we don't start building a
        //graveyard of dead component configs on the main service dispatcher
        $nodeDispatcher = new \Gossamer\Horus\EventListeners\EventDispatcher(null, $this->container->get('Logger'), $request);
        $nodeDispatcher->configListeners($config);

        $componentName = $nodeConfig['defaults']['component'];
        $component = new $componentName($request, $this->container->get('Logger'));
        $component->setContainer($this->container);

        //we want any results from the event dispatcher related to this component so we can return them to server if needed
        $event = new Event(ServerEvents::COMPONENT_REQUEST_START, array('request' => $request, 'TokenFactory' => $this->container->get('TokenFactory')));

        $nodeDispatcher->dispatch($request->getYmlKey(), ServerEvents::COMPONENT_REQUEST_START, $event);

        $result = $component->handleRequest($nodeConfig);
        if (is_null($result)) {
            throw new \Exception('Component rendered null - check for return statement');
        }
        $nodeDispatcher->dispatch($request->getYmlKey(), ServerEvents::COMPONENT_REQUEST_COMPLETE, new Event(ServerEvents::COMPONENT_REQUEST_COMPLETE, array('request' => $request)));
        unset($nodeDispatcher);

        return array('eventParams' => $event->getParams(), 'Response' => $result);
    }

    private function setNamespace(array $config, SocketRequest &$request) {
        $component = $config['defaults']['component'];
        $tmp = explode('\\', $component);
        array_pop($tmp);
        $request->setNamespace('namespace', implode('\\', $tmp));
    }

    private function addDebug($msg) {
        echo $msg;
    }

}
