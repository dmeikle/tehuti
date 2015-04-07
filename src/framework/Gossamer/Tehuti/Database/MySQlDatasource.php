<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
namespace Gossamer\Tehuti\Database;

use Gossamer\Pesedget\Database\DBConnection;

/**
 * MySQlDatasource
 *
 * @author Dave Meikle
 */
class MySQlDatasource implements DataSourceInterface {
    
    private $connection;

    public function __construct(DBConnection $connection) {
        $this->connection = $connection;
    }

    public function preparedQuery($query, array $params) {
        return $this->connection->preparedQuery($query, $params);
    }

    public function query($query) {
        return $this->connection->query($query);
    }

}
