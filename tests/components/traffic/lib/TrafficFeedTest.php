<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
namespace tests\components\traffic\lib;

use components\traffic\lib\TrafficFeed;

/**
 * TrafficFeedTest
 *
 * @author Dave Meikle
 */
class TrafficFeedTest extends \tests\BaseTest{
    
    public function testAddUpdate() {
        $trafficFeed = new TrafficFeed();
        
        $list1 = array ('1' => 'red', '2' => 'orange', '3' => 'yellow', '4' => 'blue');
        $list2 = array('3' => 'yellow', '4' => 'blue', '5' => 'indigo', '6' => 'violet');
        
        $trafficFeed->setFeed($list1);
        $diff = $trafficFeed->addUpdate($list2);
        
        $this->assertTrue(count($diff) == 2);
        $this->assertTrue($diff[5] == 'indigo');
        
    }
    
    /**
     * @group update
     */
    public function testAddUpdateEmptyFeed() {
        $trafficFeed = new TrafficFeed();
        
        $list1 = array(  
                 '601775719857803265' => array
                (
                    'dateEntered' => '16-05-22 07:07:44',
                    'date' => '15-05-22 07:07:44',
                    'subject' =>'',
                    'message' => '#Chilliwack - An accident on Highway One WB east of No. 3 Road in the left lane ^JennT',
                    'priorityLevel' => 1,
                    'TickerTypes_id' => 3
                ),

    '601777818213687297' => array
        (
            'dateEntered' => '15-05-22 07:28:09',
            'date' => '15-05-22 07:28:09',
            'subject' =>'',
            'message' => 'Police incident at the #AlexFraser Bridge northbound at the south end blocking the right lane.^ms #BCHWY91 #DeltaBC',
            'priorityLevel' => '1',
            'TickerTypes_id' => 3
        ),

    '601781219811004418' => array
        (
            'dateEntered' => '15-05-22 07:33:48',
            'date' => '15-05-22 07:33:48',
            'subject' =>'',
            'message' => 'Quickly cleared! "Police incident at the #AlexFraser Bridge northbound at the south end blocking the right lane.^ms #BCHWY91 #DeltaBC"',
            'priorityLevel' => 1,
            'TickerTypes_id' => 3
        ));
        $list2 = array(  
                 '601775719857803266' => array
                (
                    'dateEntered' => '15-05-22 07:07:44',
                    'date' => '15-05-22 07:07:44',
                    'subject' =>'',
                    'message' => '#Chilliwack - An accident on Highway One WB east of No. 3 Road in the left lane ^JennT',
                    'priorityLevel' => 1,
                    'TickerTypes_id' => 3
                ),

    '601777818213687297' => array
        (
            'dateEntered' => '15-05-22 07:28:09',
            'date' => '15-05-22 07:28:09',
            'subject' =>'',
            'message' => 'Police incident at the #AlexFraser Bridge northbound at the south end blocking the right lane.^ms #BCHWY91 #DeltaBC',
            'priorityLevel' => '1',
            'TickerTypes_id' => 3
        ),

    '601781219811004418' => array
        (
            'dateEntered' => '15-05-22 07:33:48',
            'date' => '15-05-22 07:33:48',
            'subject' =>'',
            'message' => 'Quickly cleared! "Police incident at the #AlexFraser Bridge northbound at the south end blocking the right lane.^ms #BCHWY91 #DeltaBC"',
            'priorityLevel' => 1,
            'TickerTypes_id' => 3
        ));

                
        
        $trafficFeed->addUpdate($list1);
        $diff = $trafficFeed->addUpdate($list2);
        
        $this->assertEquals(1, count($diff));
        $this->assertEquals(601775719857803266, key($diff));
        
    }
    
    /**
     * @group same
     */
    public function testSameResults() {
        $trafficFeed = new TrafficFeed();
        
        $list1 = array(  
            '601775719857803265' => array
           (
               'dateEntered' => '16-05-22 07:07:44',
               'date' => '15-05-22 07:07:44',
               'subject' =>'',
               'message' => '#Chilliwack - An accident on Highway One WB east of No. 3 Road in the left lane ^JennT',
               'priorityLevel' => 1,
               'TickerTypes_id' => 3
           ),
            '601777818213687297' => array
                (
                    'dateEntered' => '15-05-22 07:28:09',
                    'date' => '15-05-22 07:28:09',
                    'subject' =>'',
                    'message' => 'Police incident at the #AlexFraser Bridge northbound at the south end blocking the right lane.^ms #BCHWY91 #DeltaBC',
                    'priorityLevel' => '1',
                    'TickerTypes_id' => 3
                ),
            '601781219811004418' => array
                (
                    'dateEntered' => '15-05-22 07:33:48',
                    'date' => '15-05-22 07:33:48',
                    'subject' =>'',
                    'message' => 'Quickly cleared! "Police incident at the #AlexFraser Bridge northbound at the south end blocking the right lane.^ms #BCHWY91 #DeltaBC"',
                    'priorityLevel' => 1,
                    'TickerTypes_id' => 3
                ));
        
        $list2 = $list1;
        $trafficFeed->addUpdate($list1);
        $diff = $trafficFeed->addUpdate($list2);
        
        $this->assertEquals(count($diff), 0);
    }
    
    /**
     * @group updates
     */
    public function testUpdates() {
        $trafficFeed = new TrafficFeed();
        $list1 = array(  
            '601775719857803265' => array
           (
               'dateEntered' => '16-05-22 07:07:44',
               'date' => '15-05-22 07:07:44',
               'subject' =>'',
               'message' => '#Chilliwack - An accident on Highway One WB east of No. 3 Road in the left lane ^JennT',
               'priorityLevel' => 1,
               'TickerTypes_id' => 3
           ),
            '601777818213687297' => array
                (
                    'dateEntered' => '15-05-22 07:28:09',
                    'date' => '15-05-22 07:28:09',
                    'subject' =>'',
                    'message' => 'Police incident at the #AlexFraser Bridge northbound at the south end blocking the right lane.^ms #BCHWY91 #DeltaBC',
                    'priorityLevel' => '1',
                    'TickerTypes_id' => 3
                ),
            '601781219811004418' => array
                (
                    'dateEntered' => '15-05-22 07:33:48',
                    'date' => '15-05-22 07:33:48',
                    'subject' =>'',
                    'message' => 'Quickly cleared! "Police incident at the #AlexFraser Bridge northbound at the south end blocking the right lane.^ms #BCHWY91 #DeltaBC"',
                    'priorityLevel' => 1,
                    'TickerTypes_id' => 3
                ));
        
        $trafficFeed->addUpdate($list1);
        
        $result = $trafficFeed->getMostRecentRows('601777818213687297');
        
        $this->assertEquals(1, count($result));
        $this->assertEquals('601781219811004418', key($result));
    }
    
  
}
