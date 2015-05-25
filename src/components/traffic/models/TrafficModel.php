<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
namespace components\traffic\models;


use Gossamer\Tehuti\Core\AbstractModel;
use TwitterAPIExchange;

/**
 * TrafficModel
 *
 * @author Dave Meikle
 */
class TrafficModel extends AbstractModel {
    
    

    public function getTraffic($numRows) {
        $config = $this->getCredentials();
        
        $url = $config['connection']['url'];
        $count = $config['connection']['count'];
        $screenName = $config['connection']['screen_name'];
        $requestMethod = $config['connection']['method'];
        
        $getfield = "?screen_name=$screenName&count=$count";

        $twitter = new TwitterAPIExchange($config['credentials']);


        $string = json_decode($twitter->setGetfield($getfield)
                 ->buildOauth($url, $requestMethod)
                 ->performRequest(),$assoc = TRUE);    

        if(array_key_exists('errors', $string) && $string["errors"][0]["message"] != "") {
            echo "<h3>Sorry, there was a problem.</h3><p>Twitter returned the following error message:</p><p><em>".$string[errors][0]["message"]."</em></p>";
            exit();

        }
        
        return array_reverse($string);
    }
    
    public function getTrafficUpdates($lastRow) {
        $config = $this->getCredentials();
      
        $url = $config['connection']['url'];
        $count = $config['connection']['count'];
        $screenName = $config['connection']['screen_name'];
        $requestMethod = $config['connection']['method'];
        
        $getfield = "?screen_name=$screenName&count=$count";

        $twitter = new TwitterAPIExchange($config['credentials']);
        //lastrow slicing is not implemented - may not be needed

        $string = json_decode($twitter->setGetfield($getfield)
                 ->buildOauth($url, $requestMethod)
                 ->performRequest(),$assoc = TRUE);    

        if(array_key_exists('errors', $string) && $string["errors"][0]["message"] != "") {
            echo "<h3>Sorry, there was a problem.</h3><p>Twitter returned the following error message:</p><p><em>".$string[errors][0]["message"]."</em></p>";
            exit();

        }
        
        return array_reverse($string);
    }
    
    private function getCredentials() {
        $loader = new \Gossamer\Tehuti\Utils\YAMLParser($this->logger);
        $loader->setFilePath(__CONFIG_DIRECTORY . '\config.yml');
        
        $config = $loader->loadConfig();
        if(!array_key_exists('twitter', $config)) {
            throw new \exceptions\KeyNotSetException('twitter key missing from config');
        }
        
        return $config['twitter'];
        
    }
}
