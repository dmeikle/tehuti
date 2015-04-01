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

use Gossamer\Tehuti\Core\AbstractModel;
use Gossamer\Horus\Core\Request;
use Monolog\Logger;


/**
 * AbstractController Class 
 *
 * Author: Dave Meikle
 * Copyright: Quantum Unit Solutions 2013
 */
abstract class AbstractController {

    use \Gossamer\Tehuti\Utils\ContainerTrait;
    
    
    /**
     * Property: model
     * The model to be implemented within the MVC framework
     */
    protected $model = null;

    /**
     * Property: request
     * The request object to contain filtered parameters from the $_REQUEST
     */
    protected $request;

    
    protected $view = null;
    
    protected $logger = null;

    /**
     * default constructor
     * 
     * @param Request filtered request values
     * @param Registry values to be built upon throughout the response
     * 
     */
    public function __construct(AbstractModel $model, AbstractView $view, Request &$request, Logger $logger) {

        $this->controllerName = strtolower(get_class($this));

        $this->logger = $logger;
        $this->model = $model;
        $this->view = $view;
        $this->request = $request;
    }

    /**
     * creates a default entity and populates it
     * 
     * @param string $key
     * @param array $values
     * 
     * @return array
     */
    protected function getEntity($key, array $values = array()) {
        $entity = new Entity();
        $entity->populate($key, $values);

        return $entity->getArray();
    }

   

    /**
     * index method - landing page or list view 
     * 
     */
    public function index() {
        $result = $this->model->index(array());

        $this->render($result);
    }

    /**
     * listall - retrieves rows based on offset, limit
     * 
     * @param int offset    database page to start at
     * @param int limit     max rows to return
     */
    public function listall($offset = 0, $limit = 20) {
        $result = $this->model->listall($offset, $limit);

        if (is_array($result) && array_key_exists($this->model->getEntity() . 'sCount', $result)) {
            $pagination = new Pagination($this->logger);
            $result['pagination'] = $pagination->paginate($result[$this->model->getEntity() . 'sCount'], $offset, $limit, $this->getUriWithoutOffsetLimit());
            unset($pagination);
        }

        $this->render($result);
    }

    /**
     * listall - retrieves rows based on offset, limit
     * 
     * @param int offset    database page to start at
     * @param int limit     max rows to return
     */
    public function listallReverse($offset = 0, $limit = 20) {
        $result = $this->model->listallReverse($offset, $limit);

        if (is_array($result) && array_key_exists($this->model->getEntity() . 'sCount', $result)) {
            $pagination = new Pagination($this->logger);
            $result['pagination'] = $pagination->paginate($result[$this->model->getEntity() . 'sCount'], $offset, $limit, $this->getUriWithoutOffsetLimit());
            unset($pagination);
        }

        $this->render($result);
    }

    /**
     * 
     * @return string
     */
    protected function getUriWithoutOffsetLimit() {
        $pieces = explode('/', __URI);
        array_pop($pieces);
        array_pop($pieces);

        return '/' . implode('/', $pieces);
    }

    /**
     * edit - display an input form based on requested id
     * 
     * @param int id    primary key of item to edit
     */
    public function edit($id) {
        $result = $this->model->edit($id);

        $this->render($result);
    }

    /**
     * save - saves/updates row
     * 
     * @param int id    primary key of item to save
     */
    public function save($id) {

        $result = $this->model->save($id);

        $params = array('entity' => $this->model->getEntity(true), 'result' => $result, 'id' => $id);
        $event = new Event('save_success', $params);
        $this->container->get('EventDispatcher')->dispatch(__YML_KEY, 'save_success', $event);

        $this->render($result);
    }

    /**
     * saves values and performs a redirect upon completion
     * 
     * @param int $id
     * @param string $ymlKey
     * @param array $params
     */
    protected function saveAndRedirect($id, $ymlKey, array $params) {
        $result = $this->model->save($id);

        $eventParams = array('entity' => $this->model->getEntity(true));
        $event = new Event('save_success', $eventParams);
        $this->container->get('EventDispatcher')->dispatch(__YML_KEY, 'save_success', $event);

        $router = new Router($this->logger, $this->httpRequest);
        $router->redirect($ymlKey, $params);
    }

    /**
     * delete - removes a row from the database
     * 
     * @param int id    primary key of item to delete
     */
    public function delete($id) {
        $result = $this->model->delete($id);

        $this->render($result);
    }

    /**
     * 
     * @param string $uri
     */
    protected function redirect($uri) {

        /* Redirect browser */
        header("Location: $uri");

        /* Make sure that code below does not get executed when we redirect. */
        exit;
    }

    /**
     * method for building forms within the view to be called if needed by
     * child classes
     * 
     * @param FormBuilderInterface $model
     * @param array $values
     * @throws Exception
     */
    protected function drawForm(FormBuilderInterface $model, array $values = null) {
        throw \Exception('drawFrom now overwritten by child class');
    }

    /**
     * 
     * @param AbstractModel $model
     * 
     * @return datasource
     */
    protected function getDatasource(AbstractModel $model) {

        $factory = $this->container->get('datasourceFactory');

        $sources = $this->container->get('datasources');
        $datasource = $factory->getDatasource($sources[get_class($model)], $this->logger);
        $datasource->setDatasourceKey($sources[get_class($model)]);

        return $datasource;
    }

    public function setInactive($id) {
        $this->model->setInactive($id);
        
        $this->render();
    }

    protected function setInactiveAndRedirect($id, $ymlKey, array $params = array()) {
        $this->model->setInactive($id);
        
        $eventParams = array('entity' => $this->model->getEntity(true));
        $event = new Event('set_inactive_success', $eventParams);
        $this->container->get('EventDispatcher')->dispatch(__YML_KEY, 'set_inactive_success', $event);

        $router = new Router($this->logger, $this->httpRequest);
        $router->redirect($ymlKey, $params);
    }
}
