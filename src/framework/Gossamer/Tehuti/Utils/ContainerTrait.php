<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Gossamer\Tehuti\Utils;

use Gossamer\Tehuti\Utils\Container;

/**
 * ContainerTrait
 *
 * @author Dave Meikle
 */
trait ContainerTrait {
    
    protected $container;
    
    public function setContainer(Container &$container) {
        $this->container = $container;
    }
    
    public function getContainer() {
        return $this->container;
    }
    
}
