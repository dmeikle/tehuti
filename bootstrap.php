<?php


define('__SITE_PATH', realpath(dirname(__FILE__)));

require_once('vendor/autoload.php');
require_once('src/framework/includes/configuration.php');
require_once('src/framework/includes/init.php');



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