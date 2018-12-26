<?php
/***************************************************************
* Catabatic Technology Pvt. Ltd.
* File Name     : Cache.php
* File Desc.    : Cache helper for managing cache
* Created By    : Ranvir Singh <ranvir@catpl.co.in>
* Created Date  : 03 Nov 2017
* Updated Date  : 03 Nov 2017
***************************************************************/


class Zend_Controller_Action_Helper_Cache extends Zend_Controller_Action_Helper_Abstract
{

    /**
     * @var Zend_Loader_PluginLoader
     */

    public $pluginLoader;
    private $db = NULL;

    public $baseUrl;
    public $_storage;
    public $packageSession;

    public $objCache;


    /**
     * Constructor: initialize plugin loader
     * 
     * @return void
     */

    public function __construct()
    { 
        $this->pluginLoader = new Zend_Loader_PluginLoader();
//        $this->db = Zend_Db_Table::getDefaultAdapter();
        
//        $this->tourTypeArr = unserialize(CONST_TOURTYPE);

        
        $BootStrap    = $this->config();
        
        $this->siteName = $BootStrap['siteName'];
        $this->baseUrl  = $BootStrap['siteUrl'];
        

        $this->objCache = Zend_Registry::get('cache');
        
    }


    public function config()
    {
        $front = $this->getFrontController();
        $bootstrap = $front->getParam('bootstrap');
        if (null === $bootstrap) {
            throw new Exception('Unable to find bootstrap');
        }
//        return $bootstrap->getOptions();
        return $bootstrap->getOptions()['bootstrap'];
    }


    /* 
     * cacheLoad() for loading cache if exists
     * param : name
     */
    public function cacheLoad( $cacheName )
    {
        if (!$cacheName ) {
            throw new Exception("Unable to find cache, please give cache name as parameter to " . __FUNCTION__ );
            return;
        }
        return $this->objCache->load($cacheName);
    }
 

    /* 
     * cacheSave() for saving cache if not already exists
     * param : name , data
     */
    public function cacheSave( $cacheName , $data )
    {
        if (!$cacheName ) {
            throw new Exception("Unable to save cache, please give cache name as parameter to " . __FUNCTION__ );
            return;
        }
        
        return $this->objCache->save($data, $cacheName);
    }
 

    /* 
     * cacheCheck() for checking cache if exists or not
     * param : name , data
     */
    public function cacheCheck( $cacheName )
    {
        if (!$cacheName ) {
            throw new Exception("Unable to check cache, please give cache name as parameter to " . __FUNCTION__ );
            return;
        }

        // check for cache here
        if( false === $this->objCache->load( $cacheName ) ) {
            return false;
        } else {
            return true;
        }
    }



}


