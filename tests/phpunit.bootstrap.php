<?php

date_default_timezone_set('America/Vancouver');
$testPath = realpath(dirname(__FILE__));
$site_path =  substr($testPath, 0, strlen($testPath) - 6);// strip the /web from it

define ('__SITE_PATH', $site_path);
define ('__CACHE_DIRECTORY', $site_path . '/app/cache');
define ('__CONFIG_DIRECTORY', $site_path . '/app/config');
define('__COMPONENT_PATH', __SITE_PATH . '/src/components/');
//since we are using websockets let's define everything as a GET method
define('__METHOD', 'GET');


//include_once('phpunit.configuration.php');
require_once(__SITE_PATH . '/vendor/composer/ClassLoader.php');
require_once('vendor/j7mbo/twitter-api-php/TwitterAPIExchange.php');
 $loader = new Composer\Autoload\ClassLoader();

      // register classes with namespaces
      $loader->add('components', __SITE_PATH .'/src');
      $loader->add('framework', __SITE_PATH .'/src');
      $loader->add('Gossamer\\Tehuti', __SITE_PATH .'/src/framework');
      $loader->add('Gossamer\\Horus', __SITE_PATH .'/vendor/gossamer/horus/src');
      $loader->add('Gossamer\\Pesedget', __SITE_PATH .'/vendor/gossamer/pesedget/src');
      $loader->add('Gossamer\\Caching', __SITE_PATH .'/vendor/gossamer/caching/src');
      $loader->add('Gossamer\\Aker', __SITE_PATH .'/vendor/gossamer/aker/src');
    
      $loader->add('Monolog', __SITE_PATH.'/vendor/monolog/monolog/src');

      // activate the autoloader
      $loader->register();

      // to enable searching the include path (eg. for PEAR packages)
      $loader->setUseIncludePath(true);
