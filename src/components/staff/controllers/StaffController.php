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
      
        $token = $this->model->getNewToken();
        
        return $this->view->render(array('message' => 'NewToken: ' . $token));
    }
    
    public function notify() {
      
        $message = $this->model->notify();        
        $message['messageId'] = $this->request->getAttribute('messageId');
        
        return $this->view->render($message);
    }
    
    public function connect() {
        
        return $this->view->render();
    }
    
    public function listNotificationHistory() {
       
        
        $params = $this->request->getRequestParameters();
        $result = $this->model->getNotificationHistory($this->request->getClientId(), $params['start'], $params['rows']);
       
        if(is_null($result)) {
            $result = array();
        }
        return $this->view->renderList($this->request->getClientId(), $result);
    }
}
