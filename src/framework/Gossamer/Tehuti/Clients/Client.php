<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Gossamer\Tehuti\Clients;

use Gossamer\Aker\Components\Security\Core\Client as BaseClient;
/**
 * Client
 *
 * @author Dave Meikle
 */
class Client extends BaseClient {

    public function setId($id) {
        $this->id = $id;
    }

}
