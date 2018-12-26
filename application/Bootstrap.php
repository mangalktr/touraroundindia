<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    protected function _initAutoload() {
        
        $autoloader = new Zend_Application_Module_Autoloader(array(
            'namespace' => 'Travel',
            'basePath' => dirname(__FILE__),
        ));
        Zend_Controller_Action_HelperBroker::addPrefix('Helper');	
        Zend_Session::start();
        date_default_timezone_set("Asia/Kolkata"); 
    }
    protected function _initControllers() {
        $this->bootstrap('frontController');
        $this->_front = $this->getResource('frontController');
        $this->_front->addControllerDirectory(APPLICATION_PATH . '/admin/controllers', 'admin');
    }
        
    public function _initRoutes() {

        $CONST_DESTINATION_MASTER = unserialize(CONST_DESTINATION_MASTER);

        // start : get request here
            $this->bootstrap('frontController');
            $front = $this->getResource('frontController');
            $front->setRequest(new Zend_Controller_Request_Http());

            $des = ltrim($front->getRequest()->getRequestUri() , '/' );
//            print_r($des); die('here');

        // end : get request here 
            
            $routerArray = [];
            
            $routerArray[0] = new Zend_Controller_Router_Route('cms/p/:id', array('module' => 'cms', 'controller' => 'index', 'action' => 'index'));
//            $routerArray[1] = new Zend_Controller_Router_Route(':countryname/:name/:pkgid/:catid/:gtxid/:tourtype/', array('module' => 'detail', 'controller' => 'index', 'action' => 'index'));
            $routerArray[1] = new Zend_Controller_Router_Route('detail/:countryname/:name/', array('module' => 'detail', 'controller' => 'index', 'action' => 'index'));
            
            if(in_array( $des , $CONST_DESTINATION_MASTER ) ) {
              $routerArray[2] = new Zend_Controller_Router_Route(':key', array('module' => 'tours', 'controller' => 'package', 'action' => 'index'));
            } else {
              $routerArray[2] = new Zend_Controller_Router_Route('destination/:key', array('module' => 'tours', 'controller' => 'package', 'action' => 'index'));
            }

            $routerArray[3] = new Zend_Controller_Router_Route('blog/:id/:title', array('module' => 'blog', 'controller' => 'index', 'action' => 'index'));
            $routerArray[4] = new Zend_Controller_Router_Route('activities/:name/', array('module' => 'activities', 'controller' => 'index', 'action' => 'index'));
            $routerArray[5] = new Zend_Controller_Router_Route('hotels/:name/', array('module' => 'hotels', 'controller' => 'search', 'action' => 'hotel-detail'));
            $routerArray[6] = new Zend_Controller_Router_Route('tours/package/', array('module' => 'tours', 'controller' => 'package', 'action' => 'index'));
            //$routerArray[7] = new Zend_Controller_Router_Route('destination/india', array('module' => 'destination', 'controller' => 'index', 'action' => 'india'));
            //$routerArray[7] = new Zend_Controller_Router_Route('travelogue/index/detail', array('module' => 'travelogue', 'controller' => 'index', 'action' => 'index'));
            $routerArray[7] = new Zend_Controller_Router_Route('customer/agencycustomerlogin/', array('module' => 'default', 'controller' => 'customer', 'action' => 'agencycustomerlogin'));
            $routerArray[8] = new Zend_Controller_Router_Route('index/customerlogin/', array('module' => 'default', 'controller' => 'index', 'action' => 'customerlogin'));
            $routerArray[9] = new Zend_Controller_Router_Route('users/', array('module' => 'users', 'controller' => 'index', 'action' => 'index'));
             $routerArray[10] = new Zend_Controller_Router_Route('detail/index/index-ajax-data', array('module' => 'detail', 'controller' => 'index', 'action' => 'index-ajax-data'));

        $router = Zend_Controller_Front::getInstance()->getRouter();
        $router->addRoutes( $routerArray );
        $controller = Zend_Controller_Front::getInstance();
        $controller->setRouter($router);
    }
    
    
    protected function _initCache() {
        
        Zend_Controller_Front::getInstance()->setParam('disableOutputBuffering', true);
        
        $frontend= array(
            'lifetime' => 7200,
            'automatic_serialization' => true
        );

        $backend= array(
            'cache_dir' => APPLICATION_PATH . '/../cache',
        );
    
        $cache = Zend_Cache::factory('core',
            'File',
            $frontend,
            $backend
        );
        //Zend_Registry::set('cache',$cache);
    }
  
    
    
}
