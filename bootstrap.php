<?php


define('__SITE_PATH', realpath(dirname(__FILE__)));

require_once('vendor/autoload.php');
require_once('src/framework/includes/configuration.php');
require_once('src/framework/includes/init.php');

//    
//use Gossamer\Sockets\Utils\YAMLParser;
//use Gossamer\Horus\EventListeners\Event;
//use Gossamer\Horus\EventListeners\EventDispatcher;
//use Gossamer\Sockets\Servers\Server;
//use Gossamer\Horus\Core\Request;
//use Gossamer\Sockets\Ticker\Events;
//
////use Monolog\Logger;
////use Monolog\Handler\StreamHandler;
//
//$config = loadConfiguration();
//
//$eventDispatcher = new EventDispatcher($config, buildLogger($config), new Request() );
//
//$eventDispatcher->dispatch('server', Events::SERVER_INITIATE, new Event(Events::SERVER_INITIATE, array('host' => $config['server']['host'], 'port' => $config['server']['port'])));
//$server = new Server($config['server']['host'], $config['server']['port']);
//$server->setEventDispatcher($eventDispatcher);
//$server->execute();

use Gossamer\Horus\EventListeners\EventDispatcher;
use Gossamer\Horus\Core\Request;
use Gossamer\Tehuti\System\Kernel;
use Gossamer\Tehuti\Utils\Container;
use Gossamer\Pesedget\Database\EntityManager;

$logger = buildLogger();
$request = new Request();

$container = new Container();
$container->set('EventDispatcher', null, new EventDispatcher(loadConfiguration('config'), $logger, $request));
$container->set('Logger', null, $logger);
$container->set('YamlParser', 'Gossamer\\Tehuti\\Utils\\YAMLParser', new Gossamer\Tehuti\Utils\YAMLParser($logger));
$container->set('DBConnection', null, EntityManager::getInstance()->getConnection());

$kernel = new Kernel($container);
$kernel->run(Kernel::DEBUG_MODE);
?>