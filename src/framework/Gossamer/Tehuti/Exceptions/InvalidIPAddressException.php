<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Gossamer\Tehuti\Exceptions;

/**
 * InvalidIPAddressException
 *
 * @author Dave Meikle
 */
class InvalidIPAddressException extends \Exception {
    
    public function __construct($message = 'Invalid IP Address specified', $code = 4505, $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
