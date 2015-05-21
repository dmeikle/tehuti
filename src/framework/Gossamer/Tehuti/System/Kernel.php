<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Gossamer\Tehuti\System;


use Gossamer\Horus\EventListeners\Event;
use Gossamer\Horus\EventListeners\EventDispatcher;
use Gossamer\Tehuti\Servers\Server;
use Gossamer\Tehuti\System\KernelEvents;
use Monolog\Logger;
use Gossamer\Tehuti\Utils\Container;

/**
 * the core of the Framework. Called to execute the program once the bootstrap
 * processes have completed.
 * 
 * @author Dave Meikle
 */
class Kernel {

    const DEBUG_MODE = 'debug';
    
    const PRODUCTION_MODE = 'prod';
    
    private $container = null;
    
    public function __construct(Container $container) {
        $this->container = $container;
       
    }

    /**
     * main entry point for this class
     * 
     * @return string|JSON - the completed html or json array
     */
    public function run($mode = 'prod', $port = null) {

        //load the server config and start the service
        $config = loadConfiguration('config');
        if(is_null($port)) {
            //if they're not overriding in bootstrap, look in the standard configs
            $port = $config['server']['port'];
        }
        $this->container->get('EventDispatcher')->dispatch('all', KernelEvents::KERNEL_SERVER_INITIATE, new Event(KernelEvents::KERNEL_SERVER_INITIATE, array()));
        
        $server = new Server($config['server']['host'], $port);
        $server->setContainer($this->container);
        $server->execute($mode);

        $this->container->get('EventDispatcher')->dispatch('all', KernelEvents::KERNEL_SERVER_SHUTDOWN, new Event(KernelEvents::KERNEL_SERVER_SHUTDOWN, array()));
    }


}
