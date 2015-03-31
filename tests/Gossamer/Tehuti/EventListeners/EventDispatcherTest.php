<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace tests\Gossamer\Tehuti\EventListeners;

use Gossamer\Horus\EventListeners\EventDispatcher;
use Gossamer\Horus\Core\Request;
use Gossamer\Horus\EventListeners\Event;
use Gossamer\Tehuti\Core\SocketRequest;
use Gossamer\Tehuti\Clients\Client;

/**
 * EventDispatcherTest
 *
 * @author Dave Meikle
 */
class EventDispatcherTest extends \tests\BaseTest {
    
    public function testAddListener() {
        $request = $this->getRequest();
        
        $dispatcher = new EventDispatcher(null, $this->getLogger(), $request);
        $dispatcher->configListeners($this->getListenerConfig());
        $dispatcher->dispatch('all', 'client_connect', array());
              
        $this->assertNotNull($request->getAttribute('result'));
        $this->assertEquals($request->getAttribute('result'), 'TestListener loaded successfully');
    }
    
    /**
     * @group client
     */
    public function testClientConnectListener() {
        //first create a token to check against
        $tokenFactory = new \Gossamer\Tehuti\Tokens\TokenFactory();
        $token = $tokenFactory->requestToken($this->getClient());
        
        $request = $this->getSocketRequest();
        $request->setToken($token);
        
        $dispatcher = new EventDispatcher(null, $this->getLogger(), $request);
      
        $dispatcher->configListeners($this->getListenerConfig());
        
        $event = new Event('client_connect', array( 'TokenFactory' => $tokenFactory));
        $dispatcher->dispatch('client', 'client_connect', $event);
        $clientToken = $event->getParam('clientToken');
        
        $this->assertNotNull($clientToken);
        $this->assertTrue($clientToken instanceof \Gossamer\Tehuti\Tokens\ClientToken);
    }
    
    
    private function getClient() {
        $client = new Client();
        $client->setId('123');
        $client->setIpAddress('test');
       
        return $client;
    }
    
    
    /**
     * @group initiate
     */
    public function testServerInitiate() {
        $request = $this->getRequest();
        
        $dispatcher = new EventDispatcher(null, $this->getLogger(), $request);
        $dispatcher->configListeners($this->getListenerConfig());
        $dispatcher->dispatch('server', 'server_initiate', array('host' => 'local', 'port' => '123'));
        $this->assertNotNull($request->getAttribute('result_on_server_initiate'));
      
    }
    
    /**
     * @group startup
     */
    public function testServerStartup() {
        $request = $this->getRequest();
        
        $dispatcher = new EventDispatcher(null, $this->getLogger(), $request);
        $dispatcher->configListeners($this->getListenerConfig());
        
        $dispatcher->dispatch('server', 'server_startup', array('host' => 'local', 'port' => '123'));
        $this->assertNotNull($request->getAttribute('result_on_server_startup'));
      
    }
    
    /**
     * @group connect
     */
    public function testServerConnect() {
        $request = $this->getRequest();
        
        $dispatcher = new EventDispatcher(null, $this->getLogger(), $request);
        $dispatcher->configListeners($this->getListenerConfig());
        
        $dispatcher->dispatch('server', 'client_server_connect', array('host' => 'local', 'port' => '123'));
        $this->assertNotNull($request->getAttribute('result_on_client_server_connect'));
      
    }
    

    private function getListenerConfig() {
        return array( 
            'all' => array(
                'listeners' => array (
                    array(
                        'event' => 'new_connection',
                        'listener' => 'Gossamer\\Tehuti\\EventListeners\\ConnectionListener' 
                    )
                )
            ),
            'client' => array(  
                'listeners' => array(
                    array(
                        'event' => 'client_connect',
                        'listener' => 'Gossamer\\Tehuti\\EventListeners\\CheckClientCredentialsListener' 
                    )
                )
            )
        );
    }    
    
    private function getRequest() {
        $request = new Request();
        
        return $request;
    }
    
    private function getSocketRequest() {
        
		$header = "GET staff/news/$1M8Q7BX8AEybTtZq4KezdKmwwiuI8d HTTP/1.1\r\n";
                $header .= "Host: 192.168.2.252:9000\r\n";
                $header .= "Connection: Upgrade\r\n";
                $header .= "Pragma: no-cache\r\n";
                $header .= "Cache-Control: no-cache\r\n";
                $header .= "Upgrade: websocket\r\n";
                $header .= "Origin: http://192.168.2.251\r\n";
                $header .= "Sec-WebSocket-Version: 13\r\n";
                $header .= "User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.115 Safari/537.36\r\n";
                $header .= "Accept-Encoding: gzip, deflate, sdch\r\n";
                $header .= "Accept-Language: en-US,en;q=0.8\r\n";
                $header .= "Sec-WebSocket-Key: WEfxKV2kbHy4r9NHTWmS5g==\r\n";
                $header .= "Sec-WebSocket-Extensions: permessage-deflate; client_max_window_bits\r\n\r\n";
                $header .= "\r\n";
                
        return new SocketRequest($header);
        
    }
}
//listeners:
//        
//        - { 'event': 'request_start', 'listener': 'components\staff\listeners\LoadEmergencyContactsListener', 'datasource': 'datasource1' }
//        - { 'event': 'request_start', 'listener': 'core\eventlisteners\LoadListListener', 'datasource': 'datasource1', 'class': 'components\geography\models\ProvinceModel', 'cacheKey': 'Provinces' }
    