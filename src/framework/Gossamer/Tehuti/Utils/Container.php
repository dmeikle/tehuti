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

use exceptions\ObjectNotFoundException;

/**
 * a container for all items to be passed throughout the sytem for dependency
 * injection
 */
class Container {

    private $directory = null;

    /**
     * accessor
     * 
     * @param string $key
     * @param string $defaultType
     * 
     * @return \libraries\utils\item
     * 
     * @throws ObjectNotFoundException
     */
    public function get($key, $defaultType = null) {
        $directory = $this->getDirectory();

        if (!array_key_exists($key, $directory)) {
            if (is_null($defaultType)) {
                throw new ObjectNotFoundException($key . ' does not exist in container');
            }
            
            return $defaultType;
        }

        $item = $directory[$key];

        if (is_null($item['object'])) {
            $item['object'] = new $item['objectPath']();
        }

        $directory[$key] = $item;
        $this->directory = $directory;

        return $item['object'];
    }

    /**
     * accessor
     * 
     * @return array
     */
    private function getDirectory() {
        if (is_null($this->directory)) {
            $this->directory = array();
        }

        return $this->directory;
    }

    /**
     * accessor
     * 
     * @param string $key
     * @param string $objectPath
     * @param mixed $object
     */
    public function set($key, $objectPath = null, $object = null) {
        $directory = $this->getDirectory();
        $directory[$key] = array('objectPath' => $objectPath, 'object' => $object);
        $this->directory = $directory;
    }

}
