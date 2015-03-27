<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
namespace Gossamer\Tehuti\Routing;

use Gossamer\Tehuti\Routing\Router;
use Gossamer\Tehuti\Core\SocketRequest;
use Gossamer\Tehuti\Utils\YAMLParser;

/**
 * RouterTest
 *
 * @author Dave Meikle
 */
class RouterTest extends \tests\BaseTest {
 
    public function testGetHandler() {
        $router = new Router(new YAMLParser());
        
        $router->getHandler(new SocketRequest($this->getHeader()));
    }

    
    private function getHeader() {
        $host = 'localhost';
        $port = '9000';
        $key1 = $this->_generateRandomString(32);
        $key2 = $this->_generateRandomString(32);
        $key3 = $this->_generateRandomString(8, false, true);		

        $header = "GET /echo HTTP/1.1\r\n";
        $header.= "Upgrade: WebSocket\r\n";
        $header.= "Connection: Upgrade\r\n";
        $header.= "Host: ".$host.":".$port."/clients/newtoken\r\n";
        $header.= "Origin: http://foobar.com\r\n";
        $header.= "ServerAuthToken: 12345\r\n";
        $header.= 'Sec-WebSocket-Key: ' . $key1 . "\r\n";
        $header.= "Sec-WebSocket-Key1: " . $key1 . "\r\n";
        $header.= "Sec-WebSocket-Key2: " . $key2 . "\r\n";
        $header.= "\r\n";
        $header.= $key3;
        
        return $header;
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
