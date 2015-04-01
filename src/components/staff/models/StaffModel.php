<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
namespace components\staff\models;

use Gossamer\Tehuti\Core\AbstractModel;
use Gossamer\Tehuti\Exceptions\HeaderMissingException;
use Gossamer\Tehuti\Exceptions\InvalidIPAddressException;
use Gossamer\Tehuti\Clients\Client;

/**
 * StaffModel
 *
 * @author Dave Meikle
 */
class StaffModel extends AbstractModel {
    
    public function getNewToken() {
        $staffId = $this->request->getHeader('StaffId');
        $ipAddress = $this->request->getHeader('StaffIp');
        
        return $this->container->get('TokenFactory')->requestToken($this->getClient($staffId, $ipAddress));
    }
    
    public function notify() {
        $messageHeader = $this->request->getHeader('Message');
        
        if(is_null($messageHeader)) {
            throw new HeaderMissingException();
        }
         
        //convert it to an array
        return json_decode($messageHeader, true);
    }
    
    private function getClient($staffId, $ipAddress) {
        if(filter_var($ipAddress, FILTER_VALIDATE_IP) === FALSE) {
            throw new InvalidIPAddressException();
        }
        
        $client = new Client();
        $client->setId(intval($staffId));
        $client->setIpAddress($ipAddress);
        
        return $client;
    }
    
}
