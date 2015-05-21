<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace components\traffic\controllers;

use Gossamer\Tehuti\Core\AbstractController;
use components\traffic\serialization\TwitterSerializer;

/**
 * TrafficController
 *
 * @author Dave Meikle
 */
class TrafficController extends AbstractController {
    
   public function getTraffic($numRows) {
       $result = $this->model->getTraffic($numRows);
       
       $serializer = new TwitterSerializer();
     
       return $this->view->renderList($this->request->getClientId(), $serializer->formatResults($result));
   }
}
