<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
namespace tests\components\traffic\controllers;


use components\traffic\controllers\TrafficController;
use components\traffic\models\TrafficModel;
use Gossamer\Horus\Core\Request;
use Gossamer\Tehuti\Core\ClientView;

/**
 * TrafficController
 *
 * @author Dave Meikle
 */
class TrafficControllerTest extends \tests\BaseTest {
    
    public function testGetTraffic() {
        $request = new Request();
        $model = new TrafficModel($request, $this->getLogger());
        
        $controller = new TrafficController($model, new ClientView(), $request, $this->getLogger());
        $result = $controller->getTraffic(10);
    }
}
