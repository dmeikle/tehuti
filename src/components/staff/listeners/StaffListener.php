<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
namespace components\staff\listeners;

use Gossamer\Horus\EventListeners\AbstractListener;
use Gossamer\Horus\EventListeners\Event;
use Gossamer\Pesedget\Database\EntityManager;
use Gossamer\Pesedget\Commands\GetCommand;
use components\staff\entities\Staff;

/**
 * StaffListener
 *
 * @author Dave Meikle
 */
class StaffListener extends AbstractListener{
    
    public function on_component_request_start(Event $event) {
        
        $cmd = new GetCommand(new Staff, null, $this->getConnection());
        $params = array('id' => $event->getParam('StaffId'));
        $staff = $cmd->execute($params);
        
        $this->request->setAttribute(key($staff), $staff);
        
    }
    
    private function getConnection() {
        if(array_key_exists('datasource', $this->listenerConfig)) {
            return EntityManager::getInstance()->getConnection($this->listenerConfig['datasource']);
        }
        
        return EntityManager::getInstance()->getConnection();        
    }
    
    public function on_component_initiate(Event $event) {
     
        $requestToken = new ClientToken(new Client());
        $requestToken->setTokenString($event->getParam('request')->getToken());
        echo "request token: ".$event->getParam('request')->getToken()."\r\n";
        $clientToken = $event->getParam('TokenFactory')->checkToken($requestToken);
        print_r($clientToken);
        $event->setParam('clientToken', $clientToken);
    }
}
