<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Gossamer\Tehuti\Clients;

/**
 * ClientRequest
 *
 * @author Dave Meikle
 */

class ClientRequest extends Request {
//    
//   $user_name = $tst_msg->name; //sender name
//                $user_message = $tst_msg->message; //message text
//                $user_color = $tst_msg->color; //color
//                //prepare data to be sent to client
//                $response_text = $this->mask(json_encode(array('type'=>'usermsg', 'name'=>$user_name, 'message'=>$user_message, 'color'=>$user_color)));

    private $name;
    
    private $color;
    
    public function getName() {
        return $this->name;
    }

    public function getColor() {
        if(strlen($this->color) == 0) {
            $this->color = '#ffffff';
        }
        
        return $this->color;
    }

    public function setName($name) {
        $this->name = $name;
        
        return $this;
    }

    public function setColor($color) {
        $this->color = $color;
        
        return $this;
    }

    public function toArray() {
        return array(
            'TickerTypes_id' => $this->getTypeID(), 
            'subject' => $this->getSubject(), 
            'message' => $this->getMessage(), 
            'dateEntered' => $this->getDate(), 
            'priorityLevel' => $this->getPriorityLevel(), 
            'staffId' => $this->getStaffId(),
            'name' => $this->name,
            'color' => $this->getColor()
        );
    }
}