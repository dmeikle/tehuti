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

class ClientRequest {
    
    private $typeId;
    
    private $subject;
    
    private $message;
    
    private $date;
    
    private $priorityLevel;
    
    private $staffId = array();
    
    public function getTypeId() {
        return $this->typeId;
    }

    public function getSubject() {
        return $this->subject;
    }

    public function getMessage() {
        return $this->message;
    }

    public function getDate() {
        return $this->date;
    }

    public function getPriorityLevel() {
        return $this->priorityLevel;
    }

    public function getStaffId() {
        return $this->staffId;
    }

    public function setTypeId($typeId) {
        $this->typeId = $typeId;
        return $this;
    }

    public function setSubject($subject) {
        $this->subject = $subject;
        return $this;
    }

    public function setMessage($message) {
        $this->message = $message;
        return $this;
    }

    public function setDate($date) {
        $this->date = $date;
        return $this;
    }

    public function setPriorityLevel($priorityLevel) {
        $this->priorityLevel = $priorityLevel;
        return $this;
    }

    public function setStaffId(array $staffId) {
        $this->staffId = $staffId;
        return $this;
    }

    public function toArray() {
        return array(
            'TickerTypes_id' => $this->typeId, 
            'subject' => $this->subject, 
            'message' => $this->message, 
            'dateEntered' => $this->date, 
            'priorityLevel' => $this->priorityLevel, 
            'staffId' => $this->staffId
            );
    }
}