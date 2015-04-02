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
    
    use \Gossamer\Horus\EventListeners\EventDispatcherTrait;
    
    private $tokens = array();
    
    const MAX_DECAY_TIME = 300;
    
    public function checkToken(ClientToken $clientToken) {
       //with websockets there's no real way (yet) to pass header information
        //that would contain staffId - so for now we will just grab the matching
        //token in our list since we cannot (yet) generate a new token with info
        //other than their IP address unless we want to start exposing keys
        //used within our hash (such as staffId) which is just as pointless        
//       if(!($this->checkTokenValid($clientToken, $this->tokens[$clientToken->getClient()->getId()]))) {
//           throw new InvalidSecurityTokenException();
//       }
        //so for now, we will assume no one on the same WAN ip address has stolen
        //our token for their own listening  (websocket fail...). That makes 
        //this factory an identity lookup and not a security check... :(
        
        $clientId = $this->findMatchingToken($clientToken);
        if($clientId == 0) {
            throw new InvalidSecurityTokenException();
        }
        if(!($this->checkTokenDecayTime($this->tokens[$clientId]))) {
            throw new TokenExpiredException();
        }
        
        return $this->tokens[$clientId];
    }
    
    private function findMatchingToken(ClientToken $clientToken) {
      
        foreach ($this->tokens as $clientId => $token) {
            if($token->getTokenString() == $clientToken->getTokenString()) {
                return $clientId;
            }
        }
        
        return 0;
    }
    
    /**
     * checks a token to see if it is expired. Notifies event dispatcher if not found
     * in case system is configured to handle this type of event.
     * 
     * @param FormToken $token
     */
    private function checkTokenDecayTime(ClientToken $token) {
        
        $currentTime = time();
        $tokenTime = $token->getTimestamp();
        if(($currentTime - $tokenTime) > self::MAX_DECAY_TIME) {
            
            $this->eventDispatcher->dispatch('all', \Gossamer\Tehuti\Servers\ServerEvents::TOKEN_EXPIRED, new Event(\Gossamer\Tehuti\Servers\ServerEvents::TOKEN_EXPIRED, array() ));
            return false;
        }                
        
        return true;
    }
    
    /**
     * checks to see if a token is valid. Notifies event dispatcher if not found
     * in case system is configured to handle this type of event.
     * 
     * @param FormToken $token     
     * @param FormToken $defaultToken
     */
    private function checkTokenValid(ClientToken $token, ClientToken $defaultToken) {
    
        if(!crypt($token->getTokenString(), $defaultToken->toString() == $defaultToken->toString())) {
            
            $this->eventDispatcher->dispatch('all', \Gossamer\Tehuti\Servers\ServerEvents::TOKEN_MISSING, new Event(\Gossamer\Tehuti\Servers\ServerEvents::TOKEN_MISSING, array() ));
            
            return false;
        }
        
        return true;
    }
    
    public function requestToken(ClientInterface $client) {
        $token = new ClientToken($client);
        
        $this->tokens[$client->getId()] = $token;
        $tokenString = null;
        do {
            $tokenString = $token->generateTokenString();        
        }while (strpos($tokenString, '/') > 0);
                
        return $tokenString;
    }
}
