<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 *
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace components\messaging\controllers;

use Gossamer\Tehuti\Core\AbstractController;

/**
 * MessagingController
 *
 * @author Dave Meikle
 */
class MessagingController extends AbstractController {

    public function getNewMessages($numRows) {

        $results = $this->model->getNewMessages(intval($numRows));

        return $this->view->render(array('message' => 'messages: ' . $results));
    }

}
