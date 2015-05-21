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
use Gossamer\Pesedget\Commands\SaveCommand;
use Gossamer\Pesedget\Commands\BulkSaveCommand;
use components\staff\entities\TickerFeed;
use components\staff\entities\TickerFeedRecipient;
use Gossamer\Pesedget\Database\EntityManager;

/**
 * SaveNotificationListener
 *
 * @author Dave Meikle
 */
class SaveNotificationListener extends AbstractListener{
    
  
    public function on_component_request_start(Event $event) {
       
        $request = $event->getParam('request');
        $conn = $this->getConnection();
        //{"typeId":2,"subject":"testing the subject","message":"this is a new request object","date":"now","priorityLevel":1,"staffId":[2]}
        $params = $this->sanitize(json_decode($request->getHeader('Message'), true), $conn);
        $params['dateEntered'] = date('Y-m-d H:i:s');
        $cmd = new SaveCommand(new TickerFeed, null, $conn);        
        $result = $cmd->execute($params);
        
        if(is_array($result) && array_key_exists('components\\staff\\entities\\TickerFeed', $result)) {            
            $cmd = new BulkSaveCommand(new TickerFeedRecipient(), null, $conn);
            $tickerFeed = current($result);
            $cmd->execute($this->buildRecipientArray($params['staffId'], $tickerFeed['id']));
        }
        
        $this->request->setAttribute('messageId', $result);
    }
    
    private function buildRecipientArray(array $staffIdList, $tickerId) {
       
        $retval = array();
        foreach($staffIdList as $staffId) {
            
            $retval[] = array('Staff_id' => $staffId, 'TickerFeeds_id' => $tickerId);
        }
       
        return $retval;
    }
    
    private function getConnection() {
        if(array_key_exists('datasource', $this->listenerConfig)) {
            return EntityManager::getInstance()->getConnection($this->listenerConfig['datasource']);
        }
        
        return EntityManager::getInstance()->getConnection();        
    }
    private function cleanInput($input) {

        $search = array(
        '@<script[^>]*?>.*?</script>@si',   // Strip out javascript
        '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
        '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
        '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments
      );

        $output = preg_replace($search, '', $input);
        return $output;
    }

    private function sanitize($input, $conn = null) {
        if (is_array($input)) {
            foreach($input as $var=>$val) {
                $output[$var] = $this->sanitize($val, $conn);
            }
        }
        else {
            if (get_magic_quotes_gpc()) {
                $input = stripslashes($input);
            }
            $input  = $this->cleanInput($input);
            $output = str_replace("'", '`', $input);
        }
        return $output;
    }
}
