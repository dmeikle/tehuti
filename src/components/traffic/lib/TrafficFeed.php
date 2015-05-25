<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
namespace components\traffic\lib;

/**
 * TrafficFeed
 *
 * @author Dave Meikle
 */
class TrafficFeed {
    
    const MAX_TRAFFIC_HISTORY = 10;
    
    private $feed = array();
    
    public function getFeed() {
        return $this->feed;
    }

    public function setFeed(array $feed) {
        $this->feed = $feed;
        
       
        return $this;
    }

    /**
     * merges the new items with existing removing duplicates, then
     * trims the array down to max size
     * 
     * @param array $feed
     * 
     * @return array
     */
    public function addUpdate(array $feed) {
        //$diff = array_udiff($feed, $this->feed, array($this, 'compare')); 
        $diff = $this->compareByKeys($feed, $this->feed);
        
        //use replace not merge - preserves the keys
        $this->feed = array_replace($this->feed, $diff);
        
        if(count($this->feed) > self::MAX_TRAFFIC_HISTORY) {
            $start = count($this->feed) - self::MAX_TRAFFIC_HISTORY;
            $this->feed = array_slice($this->feed, $start);
        }
       
        return $diff;
    }

    /**
     * compares possibly nested arrays to see which are different
     * 
     * @param string|array $list1
     * @param string|array $list2
     * 
     * @return string|array
     */
    private function compare($list1, $list2) {
        if(!is_array($list1) && !is_array($list2)) {
            return strcmp($list1, $list2);
        }
        
        return strcmp( implode('', $list1), implode('', $list2) );
    }
    
    /**
     * compares the new items by key against the existing list
     * 
     * @param array $list
     * @return array
     */
    private function compareByKeys(array $list) {
      
        $retval = array();
        foreach($list as $key => $row) {
            
            if(!array_key_exists($key, $this->feed)) {
                $retval[$key] = $row;
            }
        }
        
        return $retval;
    }
    
    public function getMostRecentRows($lastRow) {
        
        $keys = array_keys($this->feed);
       
        //find the position of the key we received
        $index = array_search($lastRow, $keys);
        
        if($index !== false) {           
            //index +1 since we don't want the lastRow received
            return array_slice($this->feed, $index+1, null, true);            
        }
       
        return $this->feed;
    }
}
