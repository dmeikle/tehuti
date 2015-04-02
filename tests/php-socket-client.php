
<?php
/**
 * Very basic websocket client.
 * Supporting handshake from drafts:
 *	draft-hixie-thewebsocketprotocol-76
 *	draft-ietf-hybi-thewebsocketprotocol-00
 * 
 * @author Simon Samtleben <web@lemmingzshadow.net>
 * @version 2011-09-15
 */
 
class WebsocketClient
{
	private $_Socket = null;
 
	public function __construct($host, $port)
	{
		$this->_connect($host, $port);	
	}
 
	public function __destruct()
	{
		$this->_disconnect();
	}
 
	public function sendData($data)
	{
            ini_set('display_errors', 1); 
error_reporting(E_ALL);
            $data = json_encode(array (
                'type' => 'usermsg',
		'name' => 'server2',
		'message' => 'this is a test',
		'color' => '#666666'
		));
		// send actual data:
		fwrite($this->_Socket, "\x00" . $data . "\xff" ) or die('Error:' . $errno . ':' . $errstr); 
               // echo $data."\r\n";
		$wsData = fread($this->_Socket, 2000);
		$retData = trim($wsData,"\x00\xff");        
		return $retData;
	}
 
        private function getRequest() {
            $request = new ClientRequest();
            $request->setDate(strtolower("now"));
            $request->setMessage("this is a new request object");
            $request->setPriorityLevel(1);
            $request->setSubject("testing the subject");
            $request->setTypeId(2);
            $request->setStaffId(array(85));
            
            return json_encode($request->toArray());
        }
	private function _connect($host, $port)
	{
		$key1 = $this->_generateRandomString(32);
		$key2 = $this->_generateRandomString(32);
		$key3 = $this->_generateRandomString(8, false, true);		
 
		$header = "GET /staff/notify?12345 HTTP/1.1\r\n";
		$header.= "Host: ".$host.":".$port."/staff/notify\r\n";
                $header.= "Message: " . $this->getRequest() . "\r\n";
		$header.= "Connection: Upgrade\r\n";
                $header.= "Pragma: no-cache\r\n";
                $header.= "Cache-Control: nocache\r\n";
		$header.= "Upgrade: WebSocket\r\n";
		$header.= "Origin: http://192.168.2.252\r\n";
                $header.= "Sec-WebSocket-Version: 13\r\n";
		$header.= "ServerAuthToken: 12345\r\n";
                $header.= "User-Agent: CommandLine\r\n";
                $header.= 'Sec-WebSocket-Key: ' . $key1 . "\r\n";
//		$header.= "Sec-WebSocket-Key1: " . $key1 . "\r\n";
//		$header.= "Sec-WebSocket-Key2: " . $key2 . "\r\n";
		$header.= "\r\n";
 

 
		$this->_Socket = fsockopen($host, $port, $errno, $errstr, 2); 
		fwrite($this->_Socket, $header) or die('Error: ' . $errno . ':' . $errstr); 
		$response = fread($this->_Socket, 2000);
 echo $response;
		/**
		 * @todo: check response here. Currently not implemented cause "2 key handshake" is already deprecated.
		 * See: http://en.wikipedia.org/wiki/WebSocket#WebSocket_Protocol_Handshake
		 */		
 
		return true;
	}
 
	private function _disconnect()
	{
		fclose($this->_Socket);
	}
 
	private function _generateRandomString($length = 10, $addSpaces = true, $addNumbers = true)
	{  
		$characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!"§$%&/()=[]{}';
		$useChars = array();
		// select some random chars:    
		for($i = 0; $i < $length; $i++)
		{
			$useChars[] = $characters[mt_rand(0, strlen($characters)-1)];
		}
		// add spaces and numbers:
		if($addSpaces === true)
		{
			array_push($useChars, ' ', ' ', ' ', ' ', ' ', ' ');
		}
		if($addNumbers === true)
		{
			array_push($useChars, rand(0,9), rand(0,9), rand(0,9));
		}
		shuffle($useChars);
		$randomString = trim(implode('', $useChars));
		$randomString = substr($randomString, 0, $length);
		return $randomString;
	}
        
        private function mask($text)
        {
            $b1 = 0x80 | (0x1 & 0x0f);
            $length = strlen($text);

            if($length <= 125)
                    $header = pack('CC', $b1, $length);
            elseif($length > 125 && $length < 65536)
                    $header = pack('CCn', $b1, 126, $length);
            elseif($length >= 65536)
                    $header = pack('CCNN', $b1, 127, $length);
            return $header.$text;
        }
}
 echo "new websocket\r\n";
$WebSocketClient = new WebsocketClient('192.168.2.252', 9000);
sleep(5);
echo "sending data\r\n";
echo $WebSocketClient->sendData('1337');
echo "data sent\r\n";
unset($WebSocketClient);

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
            'typeId' => $this->typeId, 
            'subject' => $this->subject, 
            'message' => $this->message, 
            'date' => $this->date, 
            'priorityLevel' => $this->priorityLevel, 
            'staffId' => $this->staffId
            );
    }
}

?>
