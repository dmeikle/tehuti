<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Gossamer\Tehuti\Routing;

/**
 * finds a pattern in yml configs based on subfolders within a URI pattern
 *
 * @author Dave Meikle
 */
class URISectionComparator extends URIComparator {

    /**
     * 
     * @param array $config
     * @param string $uri
     * 
     * @return boolean
     */
    public function findPattern($config, $uri) {

        //break the uri into an array so we can pop it off in each iteration
        //this time we're simply looking for a matching parent folder
        $pieces = explode('/', $uri);
        $key = false;
        while (!$key && count($pieces) > 0) {

            $key = parent::findPattern($config, implode('/', $pieces));
            //is it holding a matched value or did it return false?
            if ($key === false) {
                array_pop($pieces);
            } else {

                return $key;
            }
        }
        return false;
    }

}
