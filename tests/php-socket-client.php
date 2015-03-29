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
//            $data = $this->mask(json_encode(array (
//                'serverAuthToken' => '123',
//		'method' => 'request_new_token',
//		'clientIp' => '192.168.2.120',
//		'name' => 'Dave'
//		)));
		$data = (json_encode(array (
                    'roomId' => '1',
                    'message' => 'asdasd',
                    'name' => 'dave',
                    'color' => 'FF7000'
                )));
		// send actual data:
		fwrite($this->_Socket, "\x00" . $data . "\xff" ) or die('Error:' . $errno . ':' . $errstr); 
                echo $data."\r\n";
		//$wsData = fread($this->_Socket, 2000);
		//$retData = trim($wsData,"\x00\xff");        
		//return $retData;
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
	private function _connect($host, $port)
	{
		$key1 = $this->_generateRandomString(32);
		$key2 = $this->_generateRandomString(32);
		$key3 = $this->_generateRandomString(8, false, true);		
 
		$header = "GET /echo HTTP/1.1\r\n";
		$header.= "Upgrade: WebSocket\r\n";
		$header.= "Connection: Upgrade\r\n";
		$header.= "Host: ".$host.":".$port."/staff/newtoken\r\n";
		$header.= "Origin: http://foobar.com\r\n";
		$header.= "ServerAuthToken: 12345\r\n";
                $header.= 'Sec-WebSocket-Key: ' . $key1 . "\r\n";
		$header.= "Sec-WebSocket-Key1: " . $key1 . "\r\n";
		$header.= "Sec-WebSocket-Key2: " . $key2 . "\r\n";
		$header.= "\r\n";
		$header.= $key3;
 
 
		$this->_Socket = fsockopen($host, $port, $errno, $errstr, 2); 
		fwrite($this->_Socket, $header) or die('Error: ' . $errno . ':' . $errstr); 
		$response = fread($this->_Socket, 2000);
 
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
}
 echo "new websocket\r\n";
$WebSocketClient = new WebsocketClient('192.168.1.24', 9000);
echo "sending data\r\n";
echo $WebSocketClient->sendData('1337');
echo "data sent\r\n";
unset($WebSocketClient);
?>