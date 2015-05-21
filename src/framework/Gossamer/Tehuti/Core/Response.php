<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Gossamer\Tehuti\Core;

/**
 * Response
 *
 * @author Dave Meikle
 */
class Response {
    
    private $messageId;
    
    private $respondToServer = false;
    
    private $message = null;
    
    private $recipientList;
    
    private $typeId;
    
    private $subject;
    
    private $timestamp;
    
    private $priorityLevel;
    
    private $messageType;
    
    private $senderName;
    
    private $senderColor;
    
    
    public function __construct(array $message = null) {
        if(!is_null($message)) {
            $this->setMessageId($message['messageId']);
            $this->setMessage($message['message']);
            $this->setPriorityLevel($message['priorityLevel']);
            $this->setRecipientList($message['staffId']);
            $this->setSubject($message['subject']);
            $this->setTimestamp(date("Y-m-dPDTh:iA"));
            $this->setTypeId($message['TickerTypes_id']);
        }
    }
    
    public function getMessageId() {
        return $this->messageId;
    }

    public function setMessageId($messageId) {
        $this->messageId = $messageId;
        
        return $this;
    }

        
    public function getRecipientList() {
        return $this->recipientList;
    }

    public function setRecipientList($recipientList) {
        $this->recipientList = $recipientList;
        return $this;
    }

    public function getRespondToServer() {
        return $this->respondToServer;
    }

    public function getMessage() {
        return $this->message;
    }

    public function setRespondToServer($respondToServer) {
        $this->respondToServer = $respondToServer;
        return $this;
    }

    public function setMessage($message) {
        $this->message = $message;
        return $this;
    }

    public function getTypeId() {
        return $this->typeId;
    }

    public function getSubject() {
        return $this->subject;
    }

    public function getTimestamp() {
        return $this->timestamp;
    }

    public function getPriorityLevel() {
        return $this->priorityLevel;
    }

    public function setTypeId($typeId) {
        $this->typeId = $typeId;
        return $this;
    }

    public function setSubject($subject) {
        $this->subject = $subject;
        return $this;
    }

    public function setTimestamp($timestamp) {
        $this->timestamp = $timestamp;
        return $this;
    }

    public function setPriorityLevel($priorityLevel) {
        $this->priorityLevel = $priorityLevel;
        return $this;
    }

    public function getMessageType() {
        return $this->messageType;
    }

    public function getSenderName() {
        return $this->senderName;
    }

    public function getSenderColor() {
        return $this->senderColor;
    }

    public function setMessageType($messageType) {
        $this->messageType = $messageType;
        return $this;
    }

    public function setSenderName($senderName) {
        $this->senderName = $senderName;
        return $this;
    }

    public function setSenderColor($senderColor) {
        $this->senderColor = $senderColor;
        return $this;
    }

    public function toArray() {
        return array('type' => 'single', 'name' => 'server', 'message' => $this->message, 'color' => '#000000', 'typeId' => $this->typeId, 'subject' => $this->subject,
            'dateEntered' => $this->timestamp, 'priority' => $this->priorityLevel, 'id' => $this->messageId);
    }

}
