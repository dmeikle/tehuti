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

use Gossamer\Aker\Components\Security\Core\SecurityToken;
use Gossamer\Aker\Components\Security\Core\Client;

/**
 * ClientToken
 *
 * @author Dave Meikle
 */
class ClientToken extends SecurityToken {

    /**
     * 
     * @param Client $client
     * @param string $ymlKey
     * @param array $roles
     */
    public function __construct(Client $client, array $roles = array()) {
        parent::__construct($client, '', $roles);
    }
    
}
