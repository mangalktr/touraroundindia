<?php
/***************************************************************
* Catabatic Technology Pvt. Ltd.
* File Name     : IndexController.php
* File Desc.    : Index controller for home page front end
* Created By    : Piyush Tiwari <piyush@catpl.co.in>
* Created Date  : 06 Jul 2018
* Updated Date  : 06 Jul 2018
***************************************************************/



class Gtxwebservices_PackageDetailsController extends Zend_Rest_Controller {




    public $baseUrl = '';

    protected $gtxwebservicesURL;
    protected $gtxagencysysid;
    
    protected $CONST_PACKAGE_TRAVELER_MAX_ROOM;
    protected $CONST_PACKAGE_TRAVELER_MAX_ADULT_IN_ROOM;
    protected $CONST_PACKAGE_TRAVELER_MAX_CHILD_IN_ROOM;
    protected $CONST_PACKAGE_TRAVELER_MAX_INFANT_IN_ROOM;

    protected $objMdl;
    protected $tablename;
    protected $objHelperGeneral;


    
    public function init() {


        $aConfig    = $this->getInvokeArg('bootstrap')->getOptions();
        $BootStrap  = $aConfig['bootstrap'];
        
        $this->siteName = $BootStrap['siteName'];
        $this->baseUrl  = $BootStrap['siteUrl'];
        
        $this->gtxwebservicesURL    = $BootStrap['gtxwebserviceurl']; // get gtxwebserviceurl from application config
        $this->gtxagencysysid       = $BootStrap['gtxagencysysid']; // get gtxagencysysid from application config

        $this->CONST_PACKAGE_TRAVELER_MAX_ROOM              = $BootStrap['CONST_PACKAGE_TRAVELER_MAX_ROOM']; // get variable from application config
        $this->CONST_PACKAGE_TRAVELER_MAX_ADULT_IN_ROOM     = $BootStrap['CONST_PACKAGE_TRAVELER_MAX_ADULT_IN_ROOM']; // get variable from application config
        $this->CONST_PACKAGE_TRAVELER_MAX_CHILD_IN_ROOM     = $BootStrap['CONST_PACKAGE_TRAVELER_MAX_CHILD_IN_ROOM']; // get variable from application config
        $this->CONST_PACKAGE_TRAVELER_MAX_INFANT_IN_ROOM    = $BootStrap['CONST_PACKAGE_TRAVELER_MAX_INFANT_IN_ROOM']; // get variable from application config
        
        $this->objMdl       = new Admin_Model_CRUD();
        $this->tablename    = "tb_tbb2c_packages_master";
        $this->objHelperGeneral = $this->_helper->General;

   }


    public function indexAction()
    {
        echo "API ;)";
    }

   
    public function getAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $param = $this->getRequest()->getParams();
        $PkgSysId = (int)$param['pkgid'];
        $package_hotelcategoryid = $param['package_hotelcategoryid'];
        $package_mealplantype = !empty($param['package_mealplantype']) ? $param['package_mealplantype'] : '';
        
        $where  = ['IsMarkForDel'=>0 , 'IsActive'=>1, 'IsPublish'=>1 , 'PkgSysId' => $PkgSysId ];
        $whereCustom = '';
        $result = [];
        
        if($PkgSysId) {
            $result = $this->objMdl->rv_select_row($this->tablename,
                    ['PkgSysId','Nights','Destinations','DestinationsId','MinPrice','MaxPrice','MinPax','LongJsonInfo','PackageType','PackageSubType'], $where, ['PkgSysId'=>'DESC']);
        }
        if( ($result !== false) && count($result)) {
            $result['status']   = true;
        }
        else {
            $result['status']   = false;
        }       
        echo $resultJson = $this->objHelperGeneral->customiseForJsonSendqueryV2( $result , 'B2C' , $package_hotelcategoryid,  $package_mealplantype );
        die;
    }
    
    public function getActivityAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
//        $this->_helper->viewRenderer("index");
        
        $param = $this->getRequest()->getParams();
        //print_r($param); die;
        $PkgSysId = (int)$param['pkgid'];
        $package_hotelcategoryid = $param['package_hotelcategoryid'];
        $package_mealplantype = $param['package_mealplantype'];
        
        $where  = ['IsMarkForDel'=>0 , 'IsActive'=>1, 'IsPublish'=>1 , 'PkgSysId' => $PkgSysId ];
        $whereCustom = '';
        $result = [];
        
        if($PkgSysId) {
            $result = $this->objMdl->rv_select_row($this->tablename,
                    ['PkgSysId','Nights','Destinations','DestinationsId','MinPrice','MaxPrice','MinPax','LongJsonInfo','PackageType','PackageSubType'], $where, ['PkgSysId'=>'DESC']);
//            $result = $this->objMdl->rv_select_row($this->tablename, ['*'], $where, ['PkgSysId'=>'DESC']);
        }
        
        if( ($result !== false) && count($result)) {
            $result['status']   = true;
        }
        else {
            $result['status']   = false;
        }
        echo Zend_Json::encode($result);
        die;
    }
    
    
    public function gethotelAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
//        $this->_helper->viewRenderer("index");
        
        $param = $this->getRequest()->getParams();
//        print_r($param); die;
        $XRefAccoSysId = (int)$param['XRefAccoSysId'];
        
        
        $where  = ['IsMarkForDel'=>0 , 'IsActive'=>1, 'IsPublish'=>1 , 'GTXPkgId' => $XRefAccoSysId ];
        $whereCustom = '';
        $result = [];
        
        if($XRefAccoSysId) {
            $result = $this->objMdl->rv_select_row($this->tablename,
                    ['PkgSysId','Nights','Destinations','DestinationsId'], $where, ['GTXPkgId'=>'DESC']);
//            $result = $this->objMdl->rv_select_row($this->tablename, ['*'], $where, ['PkgSysId'=>'DESC']);
        }

        if( ($result !== false) && count($result)) {
            $result['status']   = true;
        }
        else {
            $result['status']   = false;
        }
//        print_r($result);
        echo $resultJson = json_encode($result);
        die;
    }



    public function postAction()
    {
      // action body
    }
    
        
    public function putAction()
    {
      // action body
    }

    public function deleteAction()
    {
      // action body
    }
    
    
    public function printMsg($response)
    {
        echo $xmlData = Zend_Json::encode($response);
        exit;
    }
    

}