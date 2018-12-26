<?php
class Admin_Bootstrap extends Zend_Application_Module_Bootstrap {

    protected function _initAutoload() {
        $autoloader = new Zend_Application_Module_Autoloader(array(
                    'namespace' => 'Admin',
                    'basePath' => APPLICATION_PATH."/admin",
                    'resourceTypes' => array(
                        'form' => array(
                            'path' => APPLICATION_PATH.'admin/forms/',
                            'namespace' => 'Form',
                        ),
                      'model' => array(
                            'path' => APPLICATION_PATH.'admin/models/',
                            'namespace' => 'Model',
                        ),
                       'modules' => array(
                            'path' => APPLICATION_PATH.'admin/modules/',
                            'namespace' => '',
                        ),
                    ),
            )
        );
       return $autoloader;
    }

    protected function _initView() {

       $layoutModulePlugin = new Catabatic_AdminLayout();
       $layoutModulePlugin->registerModuleLayout('admin',APPLICATION_PATH.'/admin/layouts');
       $controller = Zend_Controller_Front::getInstance();
       $controller->registerPlugin($layoutModulePlugin);
       $view = new Zend_View();
       $view->doctype('XHTML1_STRICT');
       //$view->headTitle("Admin");
        $viewRenderer =
                Zend_Controller_Action_HelperBroker::getStaticHelper(
                        'ViewRenderer'
        );
      $viewRenderer->setView($view);
        $this->_view = $view;

        return $this->_view;
    }
    




}
