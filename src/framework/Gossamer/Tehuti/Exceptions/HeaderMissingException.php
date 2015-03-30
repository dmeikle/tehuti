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
 * ObjectNotFoundException
 *
 * @author Dave Meikle
 */
class HeaderMissingException extends \Exception{
    
    public function __construct($message = 'Bad Request - Header missing', $code = 4400, $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
