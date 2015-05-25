<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
namespace components\traffic\serialization;

/**
 * TicketCategorySerializer
 *
 * @author Dave Meikle
 */
class TwitterSerializer {
    
    public function formatResults(array $result = null) {
       
        if(is_null($result)) {
            return array();
        }
        $retval = array();
     
        foreach($result as $value) {
            
            if(!array_key_exists('created_at', $value)) {
                continue;
            }
            $date = strtotime($value['created_at']);
      
            //$tmp['dateEntered'] = date("F j \<\b\\r\>h:iA", $date);
           // $tmp['date'] = date("F j \<\b\\r\>h:iA", $date);
            $tmp['dateEntered'] = date("y-m-d h:i:s", $date);
            $tmp['date'] = date("y-m-d h:i:s", $date);
            $tmp['subject'] = '';
            $tmp['message'] = $value['text'];
            $tmp['priorityLevel'] = 1;
            $tmp['TickerTypes_id'] = 3;
         
            $retval[$value['id']] = $tmp;
        }
      
        return $retval;
    }
}
 
