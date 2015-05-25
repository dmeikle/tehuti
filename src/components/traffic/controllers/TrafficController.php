<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace components\traffic\controllers;

use Gossamer\Tehuti\Core\AbstractController;
use components\traffic\serialization\TwitterSerializer;
use components\traffic\lib\TrafficFeed;
use Gossamer\Tehuti\Core\AbstractModel;
use Gossamer\Tehuti\Core\AbstractView;
use Gossamer\Horus\Core\Request;
use Monolog\Logger;


/**
 * TrafficController
 *
 * @author Dave Meikle
 */
class TrafficController extends AbstractController {

    public function __construct(AbstractModel $model, AbstractView $view, Request &$request, Logger $logger) {
        parent::__construct($model, $view, $request, $logger);
        
    }
    
    public function getTraffic($numRows) {
        $result = $this->model->getTraffic($numRows);

        $serializer = new TwitterSerializer();

        return $this->view->renderList($this->request->getClientId(), $serializer->formatResults($result));
    }

    /**
     * gets the most recent traffic feed, compares it to the previously held
     * feed and returns only the unique entries
     * 
     * @return view
     */
    public function getTrafficUpdates($lastRow) {
        $this->container->set('TrafficFeed', 'components\traffic\lib\TrafficFeed');

        $result = $this->model->getTrafficUpdates($lastRow);

        $serializer = new TwitterSerializer();
        $list = $serializer->formatResults($result);

        $this->container->get('TrafficFeed')->addUpdate($list);

        $list = $this->container->get('TrafficFeed')->getMostRecentRows($lastRow);
        
        return $this->view->renderList($this->request->getClientId(), $list);
    }

}
