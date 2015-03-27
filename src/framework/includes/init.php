<?php


 $loader = new \Composer\Autoload\ClassLoader();
 
 
      // register classes with namespaces
      $loader->add('components', __SITE_PATH .'/src');
      $loader->add('Gossamer\\Tehuti', __SITE_PATH . '/src/framework');
 
      // activate the autoloader
      $loader->register();
 
      // to enable searching the include path (eg. for PEAR packages)
      $loader->setUseIncludePath(true);

 
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Gossamer\Tehuti\Utils\YAMLParser;



function pr($item){
    echo '<pre>\r\n';
    print_r($item);
    echo'</pre>\r\n';
}


function getSession($key) {
    $session = $_SESSION;

    return fixObject($session[$key]);
}

function setSession($key, $value) {
    $_SESSION[$key] = $value;
}
    
function fixObject (&$object)
{
    if (!is_object ($object) && gettype ($object) == 'object'){

        return ($object = unserialize (serialize ($object)));
    }

    return $object;
}


function loadConfiguration($filename) {
    $parser = new YAMLParser();
    $parser->setFilePath(__CONFIG_DIRECTORY . "$filename.yml" );
    
    return $parser->loadConfig();
}

function buildLogger() {
      
    $siteConfig = loadConfiguration('config');
    $loggerConfig = $siteConfig['logger'];
    unset($siteConfig);
   
    $loggerClass = $loggerConfig['class'];    
    $logger = new $loggerClass('client-site');
    
    $handlerClass = $loggerConfig['handler']['class'];
    $logLevel = $loggerConfig['handler']['loglevel'];
    $logFile = $loggerConfig['handler']['logfile'];
    
    $maxFiles = null;
        if(array_key_exists('maxfiles', $loggerConfig['handler'])) {
        $maxFiles = $loggerConfig['handler']['maxfiles'];
    }
    if(is_null($maxFiles)) {
        $logger->pushHandler(new $handlerClass( __LOG_PATH . $logFile, $logLevel));
    } else {
        $logger->pushHandler(new $handlerClass( __LOG_PATH . $logFile, $maxFiles, $logLevel));
    }
        
    $logger->addInfo('logger built successfully');
    
    return $logger;
}