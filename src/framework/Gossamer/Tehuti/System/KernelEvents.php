<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Gossamer\Tehuti\System;

/**
 * KernelEvents
 *
 * @author Dave Meikle
 */
class KernelEvents {
    
    const KERNEL_REQUEST_INITIATE = 'request_initiate';
    
    CONST KERNEL_REQUEST_START = 'request_start';
    
    CONST KERNEL_REQUEST_COMPLETE    = 'request_complete';
    
    const KERNEL_SERVER_INITIATE = 'server_initiate';
    
    const KERNEL_SERVER_START = 'server_start';
    
    const KERNEL_SERVER_SHUTDOWN = 'server_shutdown';
}
