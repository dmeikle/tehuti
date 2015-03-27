<?php

namespace Gossamer\Tehuti\Utils;

use Symfony\Component\Yaml\Yaml;
use Monolog\Logger;

class YAMLParser
{
    protected $ymlFilePath = null;
    
    protected $logger = null;
    
    public function __construct(Logger $logger = null) {
        $this->logger = $logger;
    }
    
    public function findNodeByURI( $uri, $searchFor) {
        $this->logger->addDebug('YAMLParser opening ' . $this->ymlFilePath);
       
        $config = $this->loadConfig();
        if(!is_array($config)) {
            return null;
        }
       
        if(array_key_exists($uri, $config) && array_key_exists($searchFor, $config[$this->getSectionKey($uri)])) {
          
            return $config[$this->getSectionKey($uri)][$searchFor];
                        
        }
        return null;
    }
//    
//    public function findNodeByURIPattern($uri, $searchfor) {
//
//        $this->logger->addDebug('YAMLParser opening ' . $this->ymlFilePath);
//       
//        $config = $this->loadConfig();
//
//        if(!is_array($config)) {
//            return null;
//        }
//     
//        if(array_key_exists($uri, $config) && array_key_exists($searchFor, $config[$this->getSectionKey($uri)])) {
//          
//            return $config[$this->getSectionKey($uri)][$searchFor];
//                        
//        }
//        return null;   
//    }
    
    public function loadConfig() {
       
        return Yaml::parse($this->ymlFilePath);
    }
    
    private function getSectionKey($uri) {
        
        $pieces = explode('/',strtolower($uri));
        $pieces = array_filter($pieces);

        return implode('_', $pieces);
    }
    
    public function setFilePath($ymlFilePath) {
        $this->ymlFilePath = str_replace('\\', '/', $ymlFilePath);
    }
}
