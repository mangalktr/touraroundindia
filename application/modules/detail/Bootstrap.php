<?php

class Detail_Bootstrap extends Zend_Application_Module_Bootstrap {

    protected function _initAutoload() {
        $resourceLoader = new Zend_Loader_Autoloader_Resource(array(
                    'basePath' => dirname(__FILE__),
                    'namespace' => 'Detail',
                    'resourceTypes' => array(
                        'form' => array(
                            'path' => 'forms/',
                            'namespace' => 'Form',
                        ),
                        'model' => array(
                            'path' => 'model/',
                            'namespace' => 'Model',
                        ),
                    ),
                ));
        return $resourceLoader;
    }

}
