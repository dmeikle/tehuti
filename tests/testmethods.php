<?php

/* 
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class TestMethods extends tests\BaseTest {
    
    public function testIt() {
        $ary1 = array("1","2","3","4","5");
        $ary2= array("1"=>"dave", "4"=>"mike");
        
        $result = array_intersect_key($ary2, $ary1);
        print_r($result);
    }
}