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
 * iterates yml configurations for a matching URI pattern
 * 
 * @author Dave Meikle
 */
class URIComparator {

    /**
     * 
     * @param array $config
     * @param string $uri
     * 
     * @return string|boolean(false)
     */
    public function findPattern($config, $uri) {

        foreach ($config as $outerkey => $grouping) {

            if (array_key_exists('methods', $grouping)) {
                $method = current($grouping['methods']);

                if ($method != __REQUEST_METHOD) {
                    continue;
                }
            }
            if (array_key_exists('pattern', $grouping)) {

                if ($grouping['pattern'] == $uri) {

                    return $outerkey;
                }
                if ($this->parseWildCard($uri, $grouping['pattern'])) {

                    return $outerkey;
                }
            }
        }

        return false;
    }

    /**
     * finds a matching pattern with a wildcard in it
     * 
     * @param string $uri
     * @param string $pageName
     * 
     * @return boolean
     */
    protected function parseWildCard($uri, $pageName) {

        //knock of the trailing parameters at end of URI
        $chunks = explode('?', $uri);
        $trimmedChunks = rtrim($chunks[0], '/');
        //this is based on URI
        $uriPieces = (explode('/', $trimmedChunks));

        if (current($uriPieces) == '') {
            array_shift($uriPieces);
        }
        //this is based on config file - remove array_filter as it was dropping last chunk
        $pagePieces = (explode('/', $pageName));

        if (count($uriPieces) != count($pagePieces) || count($pagePieces) < 1) {

            return false;
        }


        for ($i = 0; $i < count($uriPieces); $i++) {
            if (array_key_exists($i, $pagePieces)) {
                if ($pagePieces[$i] == '*') {

                    continue;
                }

                if ($pagePieces[$i] != $uriPieces[$i]) {

                    return false;
                }
            }
        }

        return true;
    }

}
