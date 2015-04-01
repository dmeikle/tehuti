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
use Monolog\Logger;



use core\datasources\DataSourceInterface;
use libraries\utils\Pagination;
use Gossamer\Caching\CacheManager;
use core\components\mappings\models\MappingModel;
use libraries\utils\preferences\UserPreferencesManager;
use libraries\utils\preferences\UserPreferences;

/**
 * abstract base class for models
 * 
 * @author Dave Meikle
 */
abstract class AbstractModel {

    use \Gossamer\Tehuti\Utils\ContainerTrait;
    
    protected $view = null;
    protected $dataSource = null;
    protected $datasourcekey;
    protected $navigation = null;
    protected $httpRequest = null;
    protected $httpResponse = null;
    protected $logger = null;

    /**
     * property: lang
     * used for loading locale strings
     */
    protected $lang = null;

    const METHOD_DELETE = 'delete';
    const METHOD_SAVE = 'save';
    const METHOD_PUT = 'put';
    const METHOD_POST = 'post';
    const METHOD_GET = 'get';
    const VERB_LIST = 'list';
    const VERB_DELETE = 'delete';
    const VERB_GET = 'get';
    const VERB_SAVE = 'save';
    const DIRECTIVES = 'directives';

    protected $entity;
    protected $childNamespace;
    protected $tablename;

    /**
     * 
     * @param HTTPRequest $httpRequest
     * @param HTTPResponse $httpResponse
     * @param Logger $logger
     */
    public function __construct(Request $request, Logger $logger) {
        $this->request = $request;
        $this->logger = $logger;
        $this->entity = get_called_class();
    }

    /**
     * 
     * @return boolean
     */
    protected function isFailedValidationAttempt() {
        return !is_null($this->httpRequest->getAttribute('ERROR_RESULT'));
    }

    /**
     * accessor
     * 
     * @return string
     */
    public function getComponentName() {
        $pieces = explode(DIRECTORY_SEPARATOR, $this->childNamespace);
        array_pop($pieces);

        return array_pop($pieces);
    }

    /**
     * accessor
     * 
     * @return string
     */
    function getTablename() {
        return $this->tablename;
    }

    

    /**
     * 
     * @param type $stripNamespace
     * 
     * @return string
     */
    public function getEntity($stripNamespace = false) {
        if ($stripNamespace) {
            $pieces = explode('\\', $this->entity);

            return array_pop($pieces);
        }
        return $this->entity;
    }

    /**
     * accessor
     * 
     * @param DataSourceInterface $dataSource
     */
    public function setDataSource(DataSourceInterface $dataSource) {
        $this->dataSource = $dataSource;
    }

    /**
     * 
     * @param array $params
     * 
     * @return array
     */
    public function index(array $params) {
        return $params;
    }
    
    /**
     * queries the datasource and deletes the record
     * 
     * @param type $offset
     * @param type $rows
     * 
     * @return array
     */
    public function delete($id) {
        $params = array(
            'id' => intval($id)
        );


        return $this->dataSource->query(self::METHOD_DELETE, $this, self::VERB_DELETE, $params);
    }

    /**
     * queries the datasource and returns the result
     * 
     * @param type $offset
     * @param type $rows
     * 
     * @return array
     */
    public function listallArray($offset = 0, $rows = 20) {
        $params = array(
            'offset' => $offset, 'rows' => $rows
        );


        return $this->dataSource->query(self::METHOD_GET, $this, self::VERB_LIST, $params);
    }

    /**
     * performs a save to the datasource
     * 
     * @param int $id
     * 
     * @return type
     */
    public function save($id) {

        $params = $this->httpRequest->getPost();
        $params[$this->entity]['id'] = intval($id);

        $data = $this->dataSource->query(self::METHOD_POST, $this, self::VERB_SAVE, $params[$this->entity]);

        return $data;
    }

    /**
     * 
     * @param type $offset
     * @param type $rows
     * @param type $customVerb
     * @return type
     */
    public function listall($offset = 0, $rows = 20, $customVerb = null) {

        return $this->listallWithParams($offset, $rows, array(), $customVerb);
    }

    /**
     * queries the datasource in reverse order
     * 
     * @param int $offset
     * @param int $rows
     * @param string $customVerb
     * 
     * @return array
     */
    public function listallReverse($offset = 0, $rows = 20, $customVerb = null) {
        $params = array(
            'directive::ORDER_BY' => 'id',
            'directive::DIRECTION' => 'desc'
        );

        return $this->listallWithParams($offset, $rows, $params, $customVerb);
    }

    /**
     * queries the database with custom passed in params and returns the result
     * 
     * @param int $offset
     * @param int $rows
     * @param array $params
     * @param string $customVerb
     * 
     * @return array
     */
    public function listallWithParams($offset = 0, $rows = 20, array $params, $customVerb = null) {

        $params['directive::OFFSET'] = $offset;
        $params['directive::LIMIT'] = $rows;
        $defaultLocale = $this->getDefaultLocale();
        $params['locale'] = $defaultLocale['locale'];
        if(!array_key_exists('isActive', $params)) {
            $params['isActive'] = '1';
        }
        
        $data = $this->dataSource->query(self::METHOD_GET, $this, (is_null($customVerb) ? self::VERB_LIST : $customVerb), $params);

        return $data;
    }

    /**
     * sets a row inactive (soft delete) in the database
     * 
     * @param int $id
     * 
     */
    public function setInactive($id) {


        $params = array(
            'id' => intval($id),
            'isActive' => '0'
        );

        $data = $this->dataSource->query(self::METHOD_PUT, $this, self::VERB_SAVE, $params);

        return $data;
    }

    /**
     * retrieves a row from the datasource for editing
     * 
     * @param int $id
     * 
     * @return array
     */
    public function edit($id) {


        if ($this->isFailedValidationAttempt()) {

            return $this->httpRequest->getAttribute('POSTED_PARAMS');
        }

        $params = array(
            'id' => intval($id)
        );

        $data = $this->dataSource->query(self::METHOD_GET, $this, self::VERB_GET, $params);

        if (is_array($data) && array_key_exists($this->entity, $data)) {
            $data = current($data[$this->entity]);
        }

        return $data;
    }

    /**
     * gets the currently selected user locale
     * 
     * @return array
     */
    public function getDefaultLocale() {

        $manager = new UserPreferencesManager($this->httpRequest);
        $userPreferences = $manager->getPreferences();

        if (!is_null($userPreferences) && $userPreferences instanceof UserPreferences) {
            return array('locale' => $userPreferences->getDefaultLocale());
        }

        $config = $this->httpRequest->getAttribute('defaultPreferences');

        return $config['default_locale'];
    }

    /**
     * retrieves a list of files within a directory
     * 
     * @param string $dir
     * @param boolean $recurse
     * 
     * @return array
     */
    protected function getFileList($dir, $recurse = false) {
        # array to hold return value
        $retval = array();

        # add trailing slash if missing
        if (substr($dir, -1) != "/")
            $dir .= "/";

        # open pointer to directory and read list of files
        $d = @dir($dir) or die("getFileList: Failed opening directory $dir for reading");
        while (false !== ($entry = $d->read())) {
            # skip hidden files
            if ($entry[0] == "." || strpos($entry[0], 'thumbnails'))
                continue;
            if (is_dir("$dir$entry")) {
                $retval[] = array(
                    "name" => "$entry/",
                    "type" => filetype("$dir$entry"),
                    "size" => 0,
                    "lastmod" => filemtime("$dir$entry")
                );
                if ($recurse && is_readable("$dir$entry/")) {
                    $retval = array_merge($retval, getFileList("$dir$entry/", true));
                }
            } elseif (is_readable("$dir$entry")) {
                $retval[] = array(
                    "name" => "$entry",
                    "type" => mime_content_type("$dir$entry"),
                    "size" => filesize("$dir$entry"),
                    "lastmod" => filemtime("$dir$entry")
                );
            }
        }
        $d->close();

        return $retval;
    }

    /**
     * TODO: deprecate this in favor of Serialization classes
     * 
     * @param array $options
     * @param array $selectedOptions
     * @param string $subKey
     * 
     * @return string
     */
    protected function formatSelectionBoxOptions(array $options, array $selectedOptions, $subKey = '') {

        if (strlen($subKey) > 0) {
            $options = $this->extractSubNode($options, $subKey);
        }

        $retval = '';
        foreach ($options as $key => $option) {
            if (!in_array($key, $selectedOptions)) {
                $retval .= "<option value=\"{$key}\">{$option}</option>\r\n";
            } else {
                $retval .= "<option value=\"{$key}\" selected>{$option}</option>\r\n";
            }
        }


        return $retval;
    }

    /**
     * navigates the sub arrays to extract the child elements by subkey
     * @param array $array
     * @param type $key
     * 
     * @return array
     */
    private function extractSubNode(array $array, $key) {

        $output = array();
        foreach ($array as $row) {
            $output[$row['id']] = $row[$key];
        }

        return $output;
    }

    /**
     * accessor
     * 
     * @return HttpRequest
     */
    public function getHttpRequest() {
        return $this->httpRequest;
    }

    /**
     * accessor
     * 
     * @return HttpResponse
     */
    public function getHttpResponse() {
        return $this->httpResponse;
    }

    /**
     * gets the pagination results for a page
     * 
     * @param int $rawRowCount
     * @param int $offset
     * @param int $limit
     * 
     * @return string
     */
    protected function getPagination($rawRowCount, $offset, $limit) {
        if (is_null($rawRowCount)) {
            return;
        }

        $pagination = new Pagination($this->logger);
        $rowCount2 = array_shift($rawRowCount);

        $rowCount = $rowCount2['rowCount'];

        $retval = $pagination->getPagination($rowCount, $offset, $limit);

        return $retval;
    }

    /**
     * accessor
     * 
     * @return SecurityToken
     */
    protected function getSecurityToken() {
        $serializedToken = getSession('_security_secured_area');
        $token = unserialize($serializedToken);

        return $token;
    }

    /**
     * 
     * @return int
     */
    public function getLoggedInStaffId() {
        $token = $this->getSecurityToken();
        
        if(is_object($token) && $token->getClient() instanceof components\security\core\Client) {
            return $token->getClient()->getId();
        }
        return 0;
    }

    /**
     * retrieves a table structure from a datasource and caches the result
     * 
     * @return array
     */
    public function getEmptyModelStructure() {
        $key = 'mappings_' . $this->getComponentName() . '_' . $this->entity;
        $cacheManager = new CacheManager($this->logger);
        $structure = $cacheManager->retrieveFromCache($key);

        if (!$structure) {
            $params = array('entity' => $this->entity,
                'component' => $this->getComponentName());

            $structure = $this->dataSource->query(self::METHOD_GET, new MappingModel($this->httpRequest, $this->httpResponse, $this->logger), self::VERB_GET, $params);
            $cacheManager->saveToCache($key, $structure);
        }

        return $structure;
    }

}
