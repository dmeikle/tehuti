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
use Gossamer\Tehuti\Routing\Router;
use Gossamer\Horus\EventListeners\Event;

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
        $this->container->set('Router', 'Gossamer\\Tehuti\\Routing\\Router');
        
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
              //  $this->listenForMessages($changed);
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
            
            $request = new SocketRequest($header);
            $handler = $this->container->get('Router')->getHandler($request);
            print_r($handler);
            
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
}
