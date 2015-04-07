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

ini_set('display_errors', 1); 
error_reporting(E_ALL);

use Gossamer\Horus\EventListeners\EventDispatcher;
use Gossamer\Tehuti\Core\SocketRequest;
use Gossamer\Horus\EventListeners\Event;
use Gossamer\Tehuti\Routing\ServiceRouter;
use Gossamer\Tehuti\Clients\ClientFactory;
use Gossamer\Tehuti\Tokens\TokenFactory;
use Gossamer\Tehuti\Core\Response;

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
    
    private $clientFactory;
    
    private $mode;
    
    public function __construct($host, $port) {
        $this->host = $host;
        $this->port = $port;
        $this->clientFactory = new ClientFactory();
        $this->clients = array();
    }
    
    public function setEventDispatcher(EventDispatcher $eventDispatcher) {
        $this->eventDispatcher = $eventDispatcher;
    }

    private function log($msg) {
        $msg = ">> " . date("m/d/Y h:i:s",  strtotime("now")) . " $msg \r\n";
        
        if($this->mode == \Gossamer\Tehuti\System\Kernel::DEBUG_MODE) {
            echo $msg;
        }
        
        $this->container->get('Logger')->addDebug($msg);
    }
    
    public function execute($mode) {
        $this->mode = $mode;
        
        $this->container->set('Router', null, new ServiceRouter($this->container->get('YamlParser')));
        $this->container->get('Router')->setContainer($this->container);
        $this->container->set('TokenFactory', null, new TokenFactory());
        $this->container->get('TokenFactory')->setEventDispatcher($this->container->get('EventDispatcher'));
        
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
        $this->log('Starting Tehuti Messaging Service');
        //start endless loop, so that our script doesn't stop
        while (true) {
            //manage multiple connections
            $changed = $this->clients;
            try{
                $this->checkNewSockets($socket, $changed);
                $this->listenForMessages($changed);
            }catch(\Exception $e) {
                
                $this->log("Error occurred: " . $e->getMessage() . "\r\n");
            }                
        }
        // close the listening socket
        socket_close($sock);
    }
    
    private function getClientRequest($buffer) {
        $receivedText = $this->unmask($buffer);
        //turn it into a standard array
        $rawRequest = json_decode($receivedText, true);
        
        $request = null;
        //ok - now to determine if they are sending a message or
        //asking for more results
        if(array_key_exists('message', $clientRequest)) {
            $request = new \Gossamer\Tehuti\Clients\ClientRequest();
        } else {
            
        }
    }
    private function listenForMessages(array $list) {
        
        //loop through all connected sockets
        foreach ($list as $clientId => $changed_socket) {	
           
            //check for any incomming data
            while(socket_recv($changed_socket, $buf, 1024, 0) >= 1)
            {              
                $received_text = $this->unmask($buf); //unmask data
                $clientRequest = new \Gossamer\Tehuti\Clients\ClientSocketRequest($clientId, $received_text);
                
//                $tst_msg = json_decode($received_text); //json decode 
//                
//                $user_name = $tst_msg->name; //sender name
//                $user_message = $tst_msg->message; //message text
//                $user_color = $tst_msg->color; //color
//                //prepare data to be sent to client
//                $response_text = $this->mask(json_encode(array('type'=>'usermsg', 'name'=>$user_name, 'message'=>$user_message, 'color'=>$user_color)));

                
             //   $this->sendMessage($response_text); //send data
                
//                $request = new SocketRequest($header);
//            $event = new Event(ServerEvents::NEW_CONNECTION, array('ipAddress' => $ip, 'request' => $request));
//            $this->container->get('EventDispatcher')->dispatch('all', ServerEvents::NEW_CONNECTION, $event);
//            
           $rawResponse = $this->container->get('Router')->handleRequest($clientRequest); 
           $response = $rawResponse['Response'];
           if(!is_null($response) && $response instanceof Response) {
               $this->sendFilteredListMessage($response);
           }
           
          // print_r($rawResponse);
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
        
        foreach($this->clients as $socket)
        {
            @socket_write($socket,$msg,strlen($msg));
        }
        
        return true;
    }
    
    private function sendFilteredListMessage(Response $response) {
        
        $msg = $this->mask(json_encode($response->toArray()));
       
        $clients = array_intersect_key($this->clients, array_flip($response->getRecipientList()));
       
        foreach ($clients as $socket) {
            @socket_write($socket,$msg,strlen($msg));
        }
        
    }
   
    
    //Unmask incoming framed message
    private function unmask($text) {
       
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
            
            $rawResponse = $this->container->get('Router')->handleRequest($request);                
            $eventParams = $rawResponse['eventParams'];
            $response = $rawResponse['Response'];
           
            if(($event->getParam('request')->getAttribute('isServer'))) {
                if($response->getRespondToServer()) {
                    if(!is_null($response->getMessage())) {
                        $message = $this->mask(json_encode($response->getMessage()));
                        @socket_write($socket_new, $message, strlen($message));
                    }                    
                } else {
                    
                    //ok - let's see if there's something to broadcast to our list
                    $this->sendFilteredListMessage($response);
                }
                //$this->clients[] = $socket_new;                
            }else{
              
                $event = new Event(ServerEvents::CLIENT_CONNECT, array('ipAddress' => $ip, 'request' => $request));
                $event->setParam('TokenFactory', $this->container->get('TokenFactory'));
                $this->container->get('EventDispatcher')->dispatch('client', ServerEvents::CLIENT_CONNECT, $event);
               
                $this->clients[$eventParams['ClientToken']->getClient()->getId()] = $socket_new; //add socket to client array
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
