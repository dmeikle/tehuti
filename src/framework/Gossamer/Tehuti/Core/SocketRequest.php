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

use Gossamer\Horus\Core\Request;

/**
 * SocketRequest
 *
 * @author Dave Meikle
 */
class SocketRequest extends Request {

    protected $headers;
    protected $uri;
    protected $component = null;
    protected $ymlKey;
    protected $token;
    protected $parameters;
    protected $verb;
    protected $namespace;
    protected $componentConfig;

    public function __construct($header) {

        $this->parseHeader($header);
        $this->parseUri();

        $this->setParameters($header);
        $this->setComponent($this->getComponentName());
    }

    public function setComponentConfig(array $config) {
        $this->componentConfig = $config;
    }

    public function getComponentConfig() {
        return $this->componentConfig;
    }

    public function setNamespace($namespace) {
        $this->namespace = $namespace;
    }

    public function getNamespace() {
        return $this->namespace;
    }

    public function getToken() {
        return $this->token;
    }

    public function setToken($token) {
        $this->token = $token;
        return $this;
    }

    protected function setParameters($header) {

        $lines = preg_split("/\r\n/", $header);

        $get = urldecode(array_shift($lines));
        $pieces = explode(' ', $get);

        if (count($pieces) == 3) {
            $chunks = explode('?', $pieces[1]);

            $this->token = array_pop($chunks);
            $this->uri = implode('/', $chunks);
            print_r($chunks);
            list($empty, $this->component, $this->verb, $parameter) = array_pad(explode('/', current($chunks)), 4, null);
            $this->parameters = array($parameter);
        }
    }

    public function getParameters() {
        return $this->parameters;
    }

    public function getComponent() {
        return $this->component;
    }

    public function getYmlKey() {
        return $this->ymlKey;
    }

    public function setComponent($component) {
        $this->component = $component;
    }

    public function setYmlKey($ymlKey) {
        $this->ymlKey = $ymlKey;
    }

    protected function parseUri() {
        $origin = $this->headers['Origin'];

        $pieces = explode('/', $origin);
        //knock the URL and port off
        array_shift($pieces);

        $this->uri = implode('/', $pieces);
    }

    public function getUri() {
        return $this->uri;
    }

    protected function getComponentName() {

        $pieces = explode('/', $this->getUri());

        if (count($pieces) > 0 && strlen($pieces[0]) == 0) {
            array_shift($pieces);
        }
        return array_shift($pieces);
    }

    protected function parseHeader($receivedHeader) {
        $this->headers = array();

        $lines = preg_split("/\r\n/", $receivedHeader);

        foreach ($lines as $line) {
            $line = chop($line);
            if (preg_match('/\A(\S+): (.*)\z/', $line, $matches)) {
                $this->headers[$matches[1]] = $matches[2];
            }
        }
        if (array_key_exists('Message', $this->headers)) {
            $this->headers['Message'] = str_replace('"dateEntered":"now"', '"dateEntered":"' . date('Y-m-d') . '<br>' . date('h:i:s') . '"', $this->headers['Message']);
        }
    }

    public function getHeader($key) {
        if (array_key_exists($key, $this->headers)) {
            return $this->headers[$key];
        }

        return null;
    }

}
