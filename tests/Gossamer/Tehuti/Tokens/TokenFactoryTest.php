<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
namespace tests\Gossamer\Tehuti\Tokens;

use Gossamer\Tehuti\Tokens\TokenFactory;
use Gossamer\Tehuti\Tokens\ClientToken;
use Gossamer\Tehuti\Clients\Client;

/**
 * TokenFactoryTest
 *
 * @author Dave Meikle
 */
class TokenFactoryTest  extends \tests\BaseTest {
    
    
    public function testGetToken() {
        $tokenFactory = new TokenFactory();
        
        $token = $tokenFactory->requestToken($this->getClient());
        print_r("'$token'");
    }
    
    public function testCheckToken() {
        //generate a dummy token inside the factory list
        $tokenFactory = new TokenFactory();        
        $token = $tokenFactory->requestToken($this->getClient());
        echo "$token\r\n";
        
       $token = '$1$zwO5VV1u$.E/inQN1oUXIBe2aHFs5g/';
        $clientToken = new ClientToken($this->getClient());
        $clientToken->setTokenString($token);
        $tokenFactory->checkToken($clientToken);
    }
    
    private function getClient() {
        $client = new Client();
        $client->setId(1);
        $client->setIpAddress('localhost');
       
        return $client;
    }
}
