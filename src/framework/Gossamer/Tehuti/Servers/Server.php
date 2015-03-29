<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Gossamer\Tehuti\Servers;

use Gossamer\Horus\EventListeners\EventDispatcher;
use Gossamer\Tehuti\Core\SocketRequest;
use Gossamer\Horus\EventListeners\Event;
use Gossamer\Tehuti\Routing\ServiceRouter;

/**
 * Server
 *
 * @author Dave Meikle
 */
class Server {
    
    use \Gossamer\Tehuti\Utils\ContainerTrait;
    
    private $eventDispatcher = null;
    
    private $host;
    
    private $port;
    
    private $clients;
    
    public function __construct($host, $port) {
        $this->host = $host;
        $this->port = $port;
    }
    
    public function setEventDispatcher(EventDispatcher $eventDispatcher) {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function execute() {
        $this->container->set('Router', null, new ServiceRouter($this->container->get('YamlParser')));
        $this->container->get('Router')->setContainer($this->container);
       // $this->tokenManager = new TokenManager();
       
        //Create TCP/IP sream socket
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        //reuseable port
        socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);

        //bind socket to specified host
        socket_bind($socket, 0, $this->port);

        //listen to port
        socket_listen($socket);

        //create & add listning socket to the list
        $this->clients = array($socket);
        $this->container->get('EventDispatcher')->dispatch('server', ServerEvents::SERVER_STARTUP, new Event(ServerEvents::SERVER_STARTUP, array('host' => $this->host, 'port' => $this->port)));
        echo "starting service\r\n";
        //start endless loop, so that our script doesn't stop
        while (true) {
            //manage multiple connections
            $changed = $this->clients;
            try{
                $this->checkNewSockets($socket, $changed);
                $this->listenForMessages($changed);
            }catch(\Exception $e) {
                
                echo " error occurred: " . $e->getMessage();
            }                
        }
        // close the listening socket
        socket_close($sock);
    }
    
    private function listenForMessages(array $list) {
        //loop through all connected sockets
        foreach ($list as $changed_socket) {	
            //check for any incomming data
            while(socket_recv($changed_socket, $buf, 1024, 0) >= 1)
            {
                echo "\r\n$buf\r\n";
                $received_text = $this->unmask($buf); //unmask data
                echo "received:\r\n$received_text\r\n";
                $tst_msg = json_decode($received_text); //json decode 
                print_r($tst_msg);
                $user_name = $tst_msg->name; //sender name
                $user_message = $tst_msg->message; //message text
                $user_color = $tst_msg->color; //color
                //prepare data to be sent to client
                $response_text = $this->mask(json_encode(array('type'=>'usermsg', 'name'=>$user_name, 'message'=>$user_message, 'color'=>$user_color)));
                $this->sendMessage($response_text); //send data
                break 2; //exit this loop
            }
            $buf = @socket_read($changed_socket, 1024, PHP_NORMAL_READ);
            if ($buf === false) { // check disconnected client
                // remove client for $clients array
                $found_socket = array_search($changed_socket, $this->clients);
                socket_getpeername($changed_socket, $ip);
                unset($this->clients[$found_socket]);
                //notify all users about disconnected connection
                $response = $this->mask(json_encode(array('type'=>'system', 'message'=>$ip.' disconnected')));
                $this->sendMessage($response);
            }
        }
    }
    
    
    private function sendMessage($msg)
    {
            foreach($this->clients as $changed_socket)
            {
                    @socket_write($changed_socket,$msg,strlen($msg));
            }
            return true;
    }
    
    
    //Unmask incoming framed message
    private function unmask($text) {
       echo "\r\n>>$text<<\r\n";
	$length = ord($text[1]) & 127;
	if($length == 126) {
		$masks = substr($text, 4, 4);
		$data = substr($text, 8);
	}
	elseif($length == 127) {
		$masks = substr($text, 10, 4);
		$data = substr($text, 14);
	}
	else {
		$masks = substr($text, 2, 4);
		$data = substr($text, 6);
	}
	$text = "";
	for ($i = 0; $i < strlen($data); ++$i) {
		$text .= $data[$i] ^ $masks[$i%4];
	}
	return $text;
    }
    //Encode message for transfer to client.
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

    
    private function checkNewSockets($socket, array &$list) {
        $null = NULL;
 
        //returns the socket resources in $changed array
        socket_select($list, $null, $null, 0, 10);

        //check for new socket
        if (in_array($socket, $list)) {
            $socket_new = socket_accept($socket); //accept new socket
            
            $header = socket_read($socket_new, 1024); //read data sent by the socket
            $this->performHandshaking($header, $socket_new, $this->host, $this->port); //perform websocket handshake
           
            socket_getpeername($socket_new, $ip); //get ip address of connected socket
            socket_set_option($socket_new, SOL_SOCKET, SO_KEEPALIVE, 1);
            
            $request = new SocketRequest($header);
            $event = new Event(ServerEvents::NEW_CONNECTION, array('ipAddress' => $ip, 'request' => $request));
            $this->container->get('EventDispatcher')->dispatch('all', ServerEvents::NEW_CONNECTION, $event);
                         
            if(($event->getParam('request')->getAttribute('isServer'))) {
                $result = $this->container->get('Router')->handleRequest($request);                
                if(!is_null($result)) {
                    @socket_write($socket_new,$result,strlen($result));
                }
            }else{
                //TODO: add token auth check here
                $this->clients[] = $socket_new; //add socket to client array
            }
           
            //make room for new socket
            $found_socket = array_search($socket, $list);
            unset($list[$found_socket]);
        }
    }
    
    //handshake new client.
    private function performHandshaking($receved_header,$client_conn, $host, $port)
    {
	$headers = array();
	$lines = preg_split("/\r\n/", $receved_header);
	foreach($lines as $line)
	{
		$line = chop($line);
		if(preg_match('/\A(\S+): (.*)\z/', $line, $matches))
		{
			$headers[$matches[1]] = $matches[2];
		}
	}
	$secKey = $headers['Sec-WebSocket-Key'];
	$secAccept = base64_encode(pack('H*', sha1($secKey . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
	//hand shaking header
	$upgrade  = "HTTP/1.1 101 Web Socket Protocol Handshake\r\n" .
	"Upgrade: websocket\r\n" .
	"Connection: Upgrade\r\n" .
	"WebSocket-Origin: $host\r\n" .
	"WebSocket-Location: ws://$host:$port/demo/shout.php\r\n".
	"Sec-WebSocket-Accept:$secAccept\r\n\r\n";
	socket_write($client_conn,$upgrade,strlen($upgrade));
    }
}
