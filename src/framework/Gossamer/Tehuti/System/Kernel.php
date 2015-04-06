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
    public function run($mode = 'prod') {

        //load the server config and start the service
        $config = loadConfiguration('config');
        
        $this->container->get('EventDispatcher')->dispatch('all', KernelEvents::KERNEL_SERVER_INITIATE, new Event(KernelEvents::KERNEL_SERVER_INITIATE, array()));
        
        $server = new Server($config['server']['host'], $config['server']['port']);
        $server->setContainer($this->container);
        $server->execute($mode);

        $this->container->get('EventDispatcher')->dispatch('all', KernelEvents::KERNEL_SERVER_SHUTDOWN, new Event(KernelEvents::KERNEL_SERVER_SHUTDOWN, array()));
    }

    /**
     * determines if we are dealing with a computer or mobile device
     * 
     * @return array
     */
    private function getLayoutType() {
        $detector = new MobileDetect();
        $isMobile = $detector->isMobile();
        $isTablet = $detector->isTablet();
        unset($detector);

        return array('isMobile' => $isMobile, 'isTablet' => $isTablet);
    }

    /**
     * creates any session params in the event we are reloading the page so
     * the params are available for access after the redirect.
     * 
     * @param HTTPRequest $request
     */
    private function configSessionParamsToRequest(HTTPRequest &$request) {
        $request->setAttribute('ERROR_RESULT', getSession('ERROR_RESULT'));
        $request->setAttribute('POSTED_PARAMS', getSession('POSTED_PARAMS'));
       
        setSession('ERROR_RESULT', null);
        setSession('POSTED_PARAMS', null);
    }

}
