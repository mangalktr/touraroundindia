<?php

/* * ***********************************************************
 * Catabatic Technology Pvt. Ltd.
 * File Name     : PackageController.php
 * File Desc.    : Index controller for home page front end
 * Created By    : Piyush Tiwari <piyush@catpl.co.in>
 * Created Date  : 04 July 2018
 * Updated Date  : 21 July 2018

 * Tours_PackageController | Tours Module / Index controller
 * *************************************************************
 */

class Tours_PackageController extends Zend_Controller_Action {

    protected $objMdl;
    protected $objHelperGeneral;
    protected $tablename;
    public $baseUrl = '';
    protected $gtxwebservicesURL;
    protected $gtxagencysysid;
    protected $CONST_PACKAGE_TRAVELER_MAX_ROOM;
    protected $CONST_PACKAGE_TRAVELER_MAX_ADULT_IN_ROOM;
    protected $CONST_PACKAGE_TRAVELER_MAX_CHILD_IN_ROOM;
    protected $CONST_PACKAGE_TRAVELER_MAX_INFANT_IN_ROOM;
    public $defaultTourListingImage;

    public function init() {

        $aConfig = $this->getInvokeArg('bootstrap')->getOptions();
        $BootStrap = $aConfig['bootstrap'];

        $this->siteName = $BootStrap['siteName'];
        $this->baseUrl = $BootStrap['siteUrl'];

        $this->gtxwebservicesURL = $BootStrap['gtxwebserviceurl']; // get gtxwebserviceurl from application config
        $this->gtxagencysysid = $BootStrap['gtxagencysysid']; // get gtxagencysysid from application config

        $this->CONST_PACKAGE_TRAVELER_MAX_ROOM = $BootStrap['CONST_PACKAGE_TRAVELER_MAX_ROOM']; // get variable from application config
        $this->CONST_PACKAGE_TRAVELER_MAX_ADULT_IN_ROOM = $BootStrap['CONST_PACKAGE_TRAVELER_MAX_ADULT_IN_ROOM']; // get variable from application config
        $this->CONST_PACKAGE_TRAVELER_MAX_CHILD_IN_ROOM = $BootStrap['CONST_PACKAGE_TRAVELER_MAX_CHILD_IN_ROOM']; // get variable from application config
        $this->CONST_PACKAGE_TRAVELER_MAX_INFANT_IN_ROOM = $BootStrap['CONST_PACKAGE_TRAVELER_MAX_INFANT_IN_ROOM']; // get variable from application config


        $this->per_page_record = '100';
        $this->objMdl = new Admin_Model_CRUD();

        $this->tablename = "tb_tbb2c_packages_master";

        $this->objHelperGeneral = $this->_helper->General;

        $this->defaultTourListingImage = "default-tour-listing.jpg";
    }

    /*
     * This function is used to search packages based on keywords typed by user on home page..
     * @param  void
     * @return void
     */

    public function indexAction() {

        $params = $this->getRequest()->getParams();
        $keywords = !empty($params['key']) ? trim($params['key']) : '';
        $category = $_GET['cat'];
        // Get maximum total night
        $getMaxResults = $this->objMdl->rv_select_row($this->tablename, ['GROUP_CONCAT(MinPrice) as minPriceArray', 'MAX(Nights) as maxnight', 'MAX(MinPrice) as MaxPrice', 'MIN(MinPrice) as MinPrice'], ['IsFeatured' => 1, 'IsMarkForDel' => 0, 'IsActive' => 1, 'IsPublish' => 1], ['PkgSysId' => 'DESC']);
        $staticPage = $this->objMdl->rv_select_all("tbl_pack_type", ['packType','banner_image'], ['Title'=>$category,'IsActive' => 1, 'IsMarkForDel' => 0], ['packType' => 'DESC'], '');
        $minPriceArray = explode(',', $getMaxResults['minPriceArray']);

        $priceRange = $this->objHelperGeneral->getPriceDropdown($getMaxResults['MinPrice'], $getMaxResults['MaxPrice'], 5000, $minPriceArray);

        $getData = array(
            'destination' => trim($this->getRequest()->getParam("key")),
            'noofday' => $this->getRequest()->getParam("noofday"),
            'pricerange' => $this->getRequest()->getParam("pricerange"),
            'maxnight' => $getMaxResults['maxnight'],
        );

        if ($getData['destination'] != "") {
            $selectTitle = explode(",", $getData['destination']);
            $checkdata = $this->objMdl->selectOne('tb_tbb2c_destinations', ['Bannerimg', 'DesSysId'], ['Title' => $selectTitle[0]]);
            $bannerImage = $checkdata['Bannerimg'];
            $this->view->bannerImage = $bannerImage;
            $this->view->DesSysId = $checkdata['DesSysId'];
        }

        $noofday = isset($params['noofday']) ? $params['noofday'] : 1;
        $noofdayMax = 6;
        $filterDurationListArr = [];

        for ($a = $noofday; $a <= $noofdayMax; $a++) {
            $filterDurationListArr[] = $a;
        }
        
        $this->view->hcode = $hcode = !empty($this->getRequest()->getParam("hcode")) ? trim($this->getRequest()->getParam("hcode")) : '';
        $this->view->cityid = $cityid = !empty($this->getRequest()->getParam("cid")) ? trim($this->getRequest()->getParam("cid")) : '';
        $this->view->strcat = $strcat = ($this->getRequest()->getParam("cat")) ? trim($this->getRequest()->getParam("cat")) : '';
        $this->view->citytxt = $citytxt = ($this->getRequest()->getParam("key")) ? trim($this->getRequest()->getParam("key")) : '';
        $this->view->UniqueSessionId = Zend_Session::getId();       
        $this->view->getData = $getData;
        $this->view->priceRange = $priceRange;
        $this->view->filterDurationListArr = $filterDurationListArr;
        $this->view->baseUrl = $this->baseUrl;
        $this->view->params = $params;
        $this->view->CONST_PACKAGE_TRAVELER_MAX_ROOM = $this->CONST_PACKAGE_TRAVELER_MAX_ROOM;
        $this->view->CONST_PACKAGE_TRAVELER_MAX_ADULT_IN_ROOM = $this->CONST_PACKAGE_TRAVELER_MAX_ADULT_IN_ROOM;
        $this->view->CONST_PACKAGE_TRAVELER_MAX_CHILD_IN_ROOM = $this->CONST_PACKAGE_TRAVELER_MAX_CHILD_IN_ROOM;
        $this->view->CONST_PACKAGE_TRAVELER_MAX_INFANT_IN_ROOM = $this->CONST_PACKAGE_TRAVELER_MAX_INFANT_IN_ROOM;
        $this->view->defaultTourListingImage = $this->defaultTourListingImage;
        $this->view->mydevice = $this->objHelperGeneral->getDevice();
        $this->view->keywords = $keywords;
        $this->view->themeImage = $staticPage;
    }

    public function getsearchdataAction() {
        try {
            $arrResponse = array();
            if ($this->getRequest()->getParam("key")) {
                $params = $this->getRequest()->getParams();
                $keywords = !empty($params['key']) ? trim($params['key']) : '';

                $objPackagesMaster = new Travel_Model_PackagesMaster();
                $arrResponse = $objPackagesMaster->packageSearch($keywords);
                if (isset($_SESSION['TravelAgent']['session']) && !empty($_SESSION['TravelAgent']['session'])) {
                    $data = $this->objHelperGeneral->customiseForJsonV2($arrResponse, 'B2B');
                } else {
                    $data = $this->objHelperGeneral->customiseForJsonV2($arrResponse, 'B2C');
                }
                $resultFinalArr = Zend_Json::decode($data, true);
                $imgIndex = 1;
                $arr = [];
                $MyMparr = [];
                foreach ($resultFinalArr['rows'] as $k => $val) {
                    $MyMparr[$val['PkgSysId']] = $val['mpArr'];
                    $arr[$k] = $imgIndex;
                    if ($imgIndex >= 3) {
                        $imgIndex = 1;
                    } else {
                        $imgIndex++;
                    }
                }

                $resultFinalArr['imgIndex'] = $arr;
                $resultFinalArr['MyMparr'] = $MyMparr;
                $resultFinalArr = Zend_Json::encode($resultFinalArr, true);
                echo ($resultFinalArr);
                exit;
            } else {
                $objPackagesMaster = new Travel_Model_PackagesMaster();
                $arrResponse = $objPackagesMaster->packageSearch($keywords);
                if (isset($_SESSION['TravelAgent']['session']) && !empty($_SESSION['TravelAgent']['session'])) {
                    $data = $this->objHelperGeneral->customiseForJsonV2($arrResponse, 'B2B');
                } else {
                    $data = $this->objHelperGeneral->customiseForJsonV2($arrResponse, 'B2C');
                }
                $resultFinalArr = Zend_Json::decode($data, true);
                $imgIndex = 1;
                $arr = [];
                $MyMparr = [];
                foreach ($resultFinalArr['rows'] as $k => $val) {
                    $MyMparr[$val['PkgSysId']] = $val['mpArr'];
                    $arr[$k] = $imgIndex;
                    if ($imgIndex >= 3) {
                        $imgIndex = 1;
                    } else {
                        $imgIndex++;
                    }
                }

                $resultFinalArr['imgIndex'] = $arr;
                $resultFinalArr['MyMparr'] = $MyMparr;
                $resultFinalArr = Zend_Json::encode($resultFinalArr, true);
                echo ($resultFinalArr);
                exit;
            }
            //echo '<pre>';print_r($packageInclusion);die('data');
        } catch (Exception $e) {
            $response = array('success' => false, 'msg' => $e->getMessage());
            echo json_encode($response);
            exit;
        }
    }

    /*
     * This function is used to search packages based on keywords typed by user on home page..
     * @param  void
     * @return void
     */

    public function newAction() {

        $params = $this->getRequest()->getParams();
//        print_r($params); die;
        // Get maximum total night
        $getMaxResults = $this->objMdl->rv_select_row($this->tablename, ['GROUP_CONCAT(MinPrice) as minPriceArray', 'MAX(Nights) as maxnight', 'MAX(MinPrice) as MaxPrice', 'MIN(MinPrice) as MinPrice'], ['IsFeatured' => 1, 'IsMarkForDel' => 0, 'IsActive' => 1, 'IsPublish' => 1], ['PkgSysId' => 'DESC']);

        $minPriceArray = explode(',', $getMaxResults['minPriceArray']);

        $priceRange = $this->objHelperGeneral->getPriceDropdown($getMaxResults['MinPrice'], $getMaxResults['MaxPrice'], 10000, $minPriceArray);

        $getData = array(
            'destination' => trim($this->getRequest()->getParam("des")),
            'noofday' => $this->getRequest()->getParam("noofday"),
            'pricerange' => $this->getRequest()->getParam("pricerange"),
            'maxnight' => $getMaxResults['maxnight'],
        );

        if ($getData['destination'] != "") {
            $selectTitle = explode(",", $getData['destination']);
            $checkdata = $this->objMdl->selectOne('tb_tbb2c_destinations', ['Bannerimg'], ['Title' => $selectTitle[0]]);
            $bannerImage = $checkdata['Bannerimg'];
            $this->view->bannerImage = $bannerImage;
        }

        $this->view->getData = $getData;
        $this->view->priceRange = $priceRange;



        $noofday = isset($params['noofday']) ? $params['noofday'] : 1;
        $noofdayMax = 6;
        $filterDurationListArr = [];

        for ($a = $noofday; $a <= $noofdayMax; $a++) {
            $filterDurationListArr[] = $a;
        }

        $this->view->filterDurationListArr = $filterDurationListArr;


        $this->view->baseUrl = $this->baseUrl;

        $this->view->CONST_PACKAGE_TRAVELER_MAX_ROOM = $this->CONST_PACKAGE_TRAVELER_MAX_ROOM;
        $this->view->CONST_PACKAGE_TRAVELER_MAX_ADULT_IN_ROOM = $this->CONST_PACKAGE_TRAVELER_MAX_ADULT_IN_ROOM;
        $this->view->CONST_PACKAGE_TRAVELER_MAX_CHILD_IN_ROOM = $this->CONST_PACKAGE_TRAVELER_MAX_CHILD_IN_ROOM;
        $this->view->CONST_PACKAGE_TRAVELER_MAX_INFANT_IN_ROOM = $this->CONST_PACKAGE_TRAVELER_MAX_INFANT_IN_ROOM;

        $this->view->defaultTourListingImage = $this->defaultTourListingImage;
    }

    /**
     * This function is used to fetch all records from database
     * @param  void
     * @return json
     * */
    public function fetchallAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->getRequest()->getParams();


//        print_r($params); die;

        $sid = $this->getRequest()->getParam('id');
        $whereNights = $whereDestination = $wherePrice = '';

        $where = [
            'IsMarkForDel' => 0,
            'IsActive' => 1,
            'IsPublish' => 1,
            'ItemType' => 1 // for Tour Package 1
        ];
        if ($sid) {
            $where['PkgSysId'] = $sid;
        }

        // param conditions
        $str2 = '';
        if (isset($params['noofday']) && !empty($params['noofday'])) {
            $val = $params['noofday'];
            $whereNights .= ' ( Nights >= ' . $val . ') ';
        }

        if ((isset($params['des']) && !empty($params['des'])) && ($params['des'] != 'all')) {

            $temp = explode("RVSTR", $params['des']);

            $str = $operator = '';
            foreach ($temp as $k => $val) {
                $operator = ($k != 0) ? ' OR ' : '';
                if ($val) {
                    $str .= " $operator Destinations LIKE ('%" . $val . "%') OR Countries LIKE ('%" . $val . "%') ";
                }
            }
            if ($str) {
                $whereDestination .= ' (' . $str . ') ';
            }
        }

        $str1 = '';
        if (isset($params['pricerange']) && !empty($params['pricerange'])) {

            if (strtolower($params['pricerange']) != 'all') {
                $temp = explode("-", $params['pricerange']);

                $operator = ( isset($temp[1]) && ( isset($temp[0]) && $temp[0]) ) ? ' AND ' : '';

                if ($temp[0]) {
                    $val = $temp[0];
                    $str1 .= " MinPrice >= ('" . $val . "') ";
                }
                if ($temp[1]) {
                    $val = $temp[1];
                    $str1 .= " $operator MinPrice <= ('" . $val . "') ";
                }

                if ($str1) {
                    $wherePrice .= '  (' . $str1 . ')';
                }
            }
        }

        $whereCustom = " (1=1) ";
        $whereCustom .= ($whereNights) ? " AND $whereNights " : "";
        $whereCustom .= ($whereDestination) ? " AND $whereDestination " : "";
        $whereCustom .= ($wherePrice) ? " AND $wherePrice " : "";

        $currentTime = date('Y-m-d 00:00:00');

        $whereCustom .= " AND ( (`PkgValidFrom` <= '$currentTime') AND (`PkgValidUntil` >= '$currentTime') ) ";


        $whereCustom .= " AND ( `BookingValidUntil` >= '$currentTime')  ";


        $resultset = $this->objMdl->rv_select_all_custom_query($this->tablename, ['*'], $where, $whereCustom, ['MinPrice' => 'ASC'], $this->per_page_record);
//        Zend_Debug::dump($resultset);

        echo $this->objHelperGeneral->customiseForJsonV2($resultset, 'B2C');
    }

}
