<?php

/* 
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

define('__CONFIG_DIRECTORY', __SITE_PATH . '/app/config/');
define('__LOG_PATH', __SITE_PATH . '/app/logs/');
define('__CACHE_DIRECTORY', __SITE_PATH . '/app/cache/');
define('__COMPONENT_PATH', __SITE_PATH . '/src/components/');
//since we are using websockets let's define everything as a GET method
define('__METHOD', 'GET');

