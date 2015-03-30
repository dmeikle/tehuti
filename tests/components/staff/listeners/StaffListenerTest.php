<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
namespace tests\components\staff\listeners;

use components\staff\listeners\StaffListener;
use Gossamer\Horus\EventListeners\Event;
use Gossamer\Horus\Core\Request;

/**
 * StaffListenerTest
 *
 * @author Dave Meikle
 */
class StaffListenerTest extends \tests\BaseTest {
    
    public function testHandler() {
        $request = new Request();
        $listener = new StaffListener($this->getLogger(), $request);
        $listener->setConfig($this->getConfig());
        
        $event = new Event('request_new_token', array('StaffId' => 2));
        
        $listener->on_component_request_start($event);
        $this->assertNotNull($request->getAttribute('components\staff\entities\Staff'));        
        $this->assertTrue(is_array($request->getAttribute('components\staff\entities\Staff')));
    }
    
    private function getConfig() {
        return array('datasource' => 'mysql');
    }
}
