<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
namespace components\staff\controllers;

use Gossamer\Tehuti\Core\AbstractController;
/**
 * StaffController
 *
 * @author Dave Meikle
 */
class StaffController extends AbstractController{
   
    public function getNewToken() {
        echo "getnewtoken\r\n";
        $token = $this->model->getNewToken();
        
        return $this->view->render(array('message' => 'NewToken: ' . $token));
    }
    
    public function notify() {
        echo "notify\r\n";
        $message = $this->model->notify();
        
        return $this->view->render(array('message' => $message['message']));
    }
    
    public function connect() {
        
        return $this->view->render();
    }
    
    public function addStaff() {
        
    }
}
