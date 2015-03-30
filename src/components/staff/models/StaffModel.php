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

/**
 * StaffModel
 *
 * @author Dave Meikle
 */
class StaffModel extends AbstractModel {
    
    public function getNewToken() {
        return \uniqid();
    }
    
    public function notify() {
        $messageHeader = $this->request->getHeader('Message');
        
        if(is_null($messageHeader)) {
            throw new HeaderMissingException();
        }
        
        //convert it to an array
        return json_decode($messageHeader, true);
    }
}
