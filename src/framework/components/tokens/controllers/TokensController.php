<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
namespace framework\components\tokens\controllers;

use Gossamer\Tehuti\Core\AbstractController;
/**
 * StaffController
 *
 * @author Dave Meikle
 */
class TokensController extends AbstractController{
   
    public function getNewToken() {
        
        return "NewToken: " . $this->model->getNewToken();
    }
}
