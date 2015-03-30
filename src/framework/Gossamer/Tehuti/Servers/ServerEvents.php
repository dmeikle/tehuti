<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Gossamer\Tehuti\Servers;

/**
 * ServerEvents
 *
 * @author Dave Meikle
 */
class ServerEvents {
    
    /* component events */
    const COMPONENT_INITIATE = 'component_initiate';
    
    const COMPONENT_REQUEST_START = 'component_request_start';
    
    const COMPONENT_REQUEST_COMPLETE = 'component_request_complete';
    
    /* server events */
    const SERVER_STARTUP = 'server_startup';
    
    const SERVER_INITIATE = 'server_initiate';
    
    const CLIENT_SERVER_CONNECT = 'client_server_connect';
    
    const CLIENT_SERVER_REQUEST = 'client_server_request';
    
    const NEW_CONNECTION = 'new_connection';
    
    /* client events */
    const CLIENT_CONNECT = 'client_connect';
    
    const CLIENT_NEW_TOKEN_REQUEST = 'new_token_request';
}
