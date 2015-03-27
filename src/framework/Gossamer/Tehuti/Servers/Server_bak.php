<?php

namespace Gossamer\Tehuti\Servers;

use Gossamer\Sockets\Ticker\Events;
use Gossamer\Horus\EventListeners\Event;
use Gossamer\Horus\EventListeners\EventDispatcher;
use Gossamer\Sockets\Ticker\Concierge;
use Gossamer\Sockets\Actions\Actions;
use Gossamer\Sockets\Authorization\TokenManager;

class Server_bak {
    
    use \Gossamer\Sockets\Traits\SendMessageTrait;
    
    private $host;
    
    private $port;
        
    private $clients;
    
    private $tokenManager;
    
    private $concierge;
    
    private $eventDispatcher = null;
    
    public function __construct($host, $port) {
        $this->host = $host;
        $this->port = $port;
        $this->concierge = new Concierge();        
    }
    
    public function setEventDispatcher(EventDispatcher $dispatcher) {
        $this->eventDispatcher = $dispatcher;
    }
    
    public function execute() {        
        
        $this->tokenManager = new TokenManager();
       
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
        $this->eventDispatcher->dispatch('server', Events::SERVER_STARTUP, new Event(Events::SERVER_STARTUP, array('host' => $this->host, 'port' => $this->port)));
        
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
    
    private function checkNewSockets($socket, array &$list) {
        $null = NULL;
 
        //returns the socket resources in $changed array
        socket_select($list, $null, $null, 0, 10);

        //check for new socket
        if (in_array($socket, $list)) {
            $socket_new = socket_accept($socket); //accept new socket
            
            $header = socket_read($socket_new, 1024); //read data sent by the socket
           
            socket_getpeername($socket_new, $ip); //get ip address of connected socket
            socket_set_option($socket_new, SOL_SOCKET, SO_KEEPALIVE, 1);
            $token = $this->checkIsServerConnect($header);
            $response = null;

            if($token !== false) {
                $event = new Event(Events::CLIENT_SERVER_CONNECT, 
                        array(
                            'token' => $token, 
                            'ipAddress' => $ip, 
                            'header' => $header, 
                            'tokenManager' => $this->tokenManager,
                            'concierge' => $this->concierge
                        ));
                //throws an error if token invalid
                $this->eventDispatcher->dispatch('server', Events::CLIENT_SERVER_CONNECT, $event);
                $this->eventDispatcher->dispatch('server', Events::CLIENT_SERVER_REQUEST, $event);
               
               //a new token has been generated in one of the handlers
               $response = $event->getParam(Actions::ACTION_RESPONSE);
            } else {                
                $event = new Event(Events::CLIENT_CONNECT, array('ipAddress' => $ip, 'header' => $header, 'tokenManager' => $this->tokenManager));
                $this->eventDispatcher->dispatch('client', Events::CLIENT_CONNECT, $event);
                $response = $this->mask(json_encode(array('type'=>'system', 'message'=>$ip.' connected'))); //prepare json data
                $this->sendMessage($response); //notify all users about new connection
                $this->concierge->addSocket($event->getParam('ClientToken'), $socket_new, $this->getClientId($header));
            }
            $this->performHandshaking($header, $socket_new, $this->host, $this->port, $response); //perform websocket handshake
            $this->clients[] = $socket_new; //add socket to client array

            //make room for new socket
            $found_socket = array_search($socket, $list);
            unset($list[$found_socket]);
        }
    }
    
    private function getClientId($header) {
        $headers = explode("\r\n", $header);
      
        foreach($headers as $row) {
           
            if(substr($row, 0, 8) == 'StaffId:') {
                $tmp = explode(': ', $row);
              
                return trim($tmp[1]);
            }
        }
        
        return false;
    }
      
    private function checkIsServerConnect($header) {
        $headers = explode("\r\n", $header);
       
        foreach($headers as $row) {
           
            if(substr($row, 0, 16) == 'ServerAuthToken:') {
                $tmp = explode(':', $row);
               
                return trim($tmp[1]);
            }
        }
        
        return false;
    }
    
    private function listenForMessages(array $list) {
        //loop through all connected sockets
        foreach ($list as $changed_socket) {
            //check for any incomming data
            while(socket_recv($changed_socket, $buf, 1024, 0) >= 1)
            {               
                $received_text = $this->unmask($buf); //unmask data
               
                $tst_msg = json_decode($received_text); //json decode
               
                if(is_null($tst_msg)) {
                    break;
                }
                $user_name = $tst_msg->name; //sender name
               
                $user_message = $tst_msg->message; //message text
                $user_color = $tst_msg->color; //color
                $roomId = $tst_msg->roomId;
               
                //prepare data to be sent to client
                $response_text = $this->mask(json_encode(array('type'=>'usermsg', 'name'=>$user_name, 'message'=>$user_message, 'color'=>$user_color)));
               
                $this->sendMessage($response_text); //send data
                //$this->concierge->sendMessage($roomId, $response_text);
                
                break 2; //exit this loop
            }

            $buf = @socket_read($changed_socket, 1024, PHP_NORMAL_READ);
            if ($buf === false) { // check disconnected client
                // remove client for $clients array
                $found_socket = array_search($changed_socket, $this->clients);
                @socket_getpeername($changed_socket, $ip);
                unset($this->clients[$found_socket]);
               
                $this->concierge->removeSocket($ip);
                //notify all users about disconnected connection
//                $response = $this->mask(json_encode(array('type'=>'system', 'message'=>$ip.' disconnected')));
//                $this->sendMessage($response);
            }
        }
    }


    //handshake new client.
    private function performHandshaking($receved_header,$client_conn, $host, $port, $response = null)
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
	"WebSocket-Location: ws://$host:$port\r\n".
        "$response\r\n".
	"Sec-WebSocket-Accept:$secAccept\r\n\r\n";
	socket_write($client_conn,$upgrade,strlen($upgrade));
    }
    
    
}