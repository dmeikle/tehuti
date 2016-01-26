<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 *
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Gossamer\Tehuti\Core;

use Gossamer\Tehuti\Core\SocketRequest;
use Monolog\Logger;
use Gossamer\Tehuti\Exceptions\ParameterNotPassedException;
use Gossamer\Pesedget\Database\EntityManager;
use Gossamer\Tehuti\Database\MySQlDatasource;
use Gossamer\Tehuti\Exceptions\HandlerNotCallableException;
use core\eventlisteners\Event;

/**
 *
 * class AbstractComponent -    this is the base class for the drop in components used to
 *                              preload any listeners for the selected component as well as
 *                              any pre-config.
 *
 * @author Dave Meikle
 *
 * @Copyright: Quantum Unit Solutions 2014
 */
abstract class AbstractComponent {

    use \Gossamer\Tehuti\Utils\ContainerTrait;

    protected $controllerName = null;
    protected $request;
    protected $modelName = null;
    protected $method = null;
    protected $params = null;
    protected $logger = null;

    /**
     *
     * @param string $controllerName
     * @param string $viewName
     * @param string $modelName
     * @param string $method
     * @param array $params
     * @param Logger $logger
     * @param array $agentType
     *
     * @throws ParameterNotPassedException
     */
    public function __construct(SocketRequest $request, Logger $logger) {
        //$this->logger->addDebug("abstractComponent: command:$command  entity:$entity" );
        $this->request = $request;

        $this->logger = $logger;
    }

    /**
     * handleRequest - entry point for the class
     *
     * @param Request   the filtered request object
     * @param Registry  the registry object
     *
     */
    public function handleRequest(array $config) {
        $controllerName = $config['defaults']['controller'];
        $method = $config['defaults']['method'];
        $modelName = $config['defaults']['model'];
        $viewName = $config['defaults']['view'];

        $handler = array(
            $controllerName,
            $method
        );

        if (is_callable($handler)) {

            //$commandName = $this->command;
            $model = new $modelName($this->request, $this->logger);
            $view = new $viewName($this->request, $this->logger);

            $static = $this->request->getAttribute($this->modelName . '_static');
            if (!is_null($static) && strlen($static) > 0) {

                echo $static;
                $this->container->get('EventDispatcher')->dispatch('all', system\KernelEvents::RENDER_BYPASS, new Event());
                $this->container->get('EventDispatcher')->dispatch($this->request->getYmlKey(), system\KernelEvents::RENDER_BYPASS, new Event());

                return;
            }
            $model->setContainer($this->container);
            print_r($config);
            if (array_key_exists('datasource', $config['defaults'])) {

                //$model->setDatasource(new MySQlDatasource(EntityManager::getInstance()->getConnection($config['defaults']['datasource'])));
                $datasource = EntityManager::getInstance()->getConnection($config['defaults']['datasource']);
                $datasource->setRequest($this->request);
                $model->setDatasource($datasource);
            }
            //$model->setDatasource($this->getDatasource());

            $controller = new $controllerName($model, $view, $this->request, $this->logger);

            $controller->setContainer($this->container);
            try {

                return call_user_func_array(array(
                    $controller,
                    $method
                        ), is_null($this->request->getParameters()) ? array() : $this->request->getParameters());
            } catch (\Exception $e) {
                echo ($e->getMessage());
                //TODO: this currently is only for the template view
                //$view = new TemplateExceptionView($this->logger, __YML_KEY, $this->agentType, $httpRequest, $e);
            }
        } else {

            throw new HandlerNotCallableException('unable to match method ' . $this->method . ' to controller');
        }
    }

    /**
     *
     * @return datasource
     */
    private function getDatasource() {
        $factory = $this->container->get('datasourceFactory');

        $sources = $this->container->get('datasources');
        $datasource = $factory->getDatasource($sources[$this->modelName], $this->logger);
        $datasource->setDatasourceKey($sources[$this->modelName]);

        return $datasource;
    }

    /** the __NAMESPACE__ is determined at compile time so we need to place this in the child:
     * return str_replace('\\', DIRECTORY_SEPARATOR, __NAMESPACE__);
     */
    protected function getChildNamespace() {
        return get_class($this);
    }

}
