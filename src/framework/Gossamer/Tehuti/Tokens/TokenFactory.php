<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Gossamer\Tehuti\Tokens;

use Gossamer\Aker\Components\Security\Core\ClientInterface;
use Gossamer\Tehuti\Tokens\ClientToken;
use Gossamer\Tehuti\Exceptions\InvalidSecurityTokenException;
use Gossamer\Tehuti\Exceptions\TokenExpiredException;

/**
 * TokenFactory
 *
 * @author Dave Meikle
 */
class TokenFactory {
    
    private $tokens;
    
    const MAX_DECAY_TIME = 300;
    
    public function checkToken(ClientToken $clientToken) {
       if(!($this->checkTokenValid($clientToken, $this->tokens[$clientToken->getClient()->getId()]))) {
           throw new InvalidSecurityTokenException();
       }
       if(!($this->checkTokenDecayTime($clientToken))) {
           throw new TokenExpiredException();
       }
    }
    
    /**
     * checks a token to see if it is expired. Notifies event dispatcher if not found
     * in case system is configured to handle this type of event.
     * 
     * @param FormToken $token
     */
    private function checkTokenDecayTime(FormToken $token) {
        $currentTime = time();
        $tokenTime = $token->getTimestamp();
        if(($currentTime - $tokenTime) > self::MAX_DECAY_TIME) {
            
            $this->eventDispatcher->dispatch('all', 'token_expired');
        }                
    }
    
    /**
     * checks to see if a token is valid. Notifies event dispatcher if not found
     * in case system is configured to handle this type of event.
     * 
     * @param FormToken $token     
     * @param FormToken $defaultToken
     */
    private function checkTokenValid(ClientToken $token, ClientToken $defaultToken) {
       echo "check ".$token->getTokenString(). " against " . $defaultToken->getTokenString()."\r\n";
        if(!crypt($token->getTokenString(), $defaultToken->toString() == $defaultToken->toString())) {
          echo "******************\r\n".crypt($token->getTokenString(), $defaultToken->toString())."\r\n".$defaultToken->toString()."\r\n***************\r\n";
            $this->eventDispatcher->dispatch('all', 'token_missing');
        }
    }
    
    public function requestToken(ClientInterface $client) {
        $token = new ClientToken($client);
        
        $this->tokens[$client->getId()] = $token;
        
        return $token->generateTokenString();
    }
}
