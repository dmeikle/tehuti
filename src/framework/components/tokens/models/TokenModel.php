<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
namespace framework\components\tokens\models;

use Gossamer\Tehuti\Core\AbstractModel;
/**
 * StaffModel
 *
 * @author Dave Meikle
 */
class TokenModel extends AbstractModel {
    
    public function getNewToken() {
        return \uniqid();
    }
}
