<?php

class Detail_IndexController extends Zend_Controller_Action {

    public $baseUrl = '';
    public $AgencyId;
    protected $objMdl;
    protected $objHelperGeneral;
    protected $tablename;

    const USER_NAMESPACE = 'PSESS';

    public $_storage;
    public $packageSession;
    public $packageTypeStatic = 2;
    public $callusnumber;

    public function init() {

        $aConfig = $this->getInvokeArg('bootstrap')->getOptions();
        $BootStrap = $aConfig['bootstrap'];
        $this->siteName = $BootStrap['siteName'];
        $this->baseUrl = $BootStrap['siteUrl'];
        $this->gtxBtoBsite = $BootStrap['gtxBtoBsite'];
        $this->AgencyId = $BootStrap['gtxagencysysid'];
        $this->AgentSysId = $BootStrap['gtxagentsysid'];
        $this->LeadURL = $BootStrap['siteUrl'] . 'gtxwebservices/index/query';
        $object = Zend_Controller_Front::getInstance();
//        -------
        $this->modulename = $object->getRequest()->getModuleName();
        define('CONST_PACKAGE_TRAVELER_MAX_ROOM', $BootStrap['CONST_PACKAGE_TRAVELER_MAX_ROOM']);
        define('CONST_PACKAGE_TRAVELER_MAX_ADULT_IN_ROOM', $BootStrap['CONST_PACKAGE_TRAVELER_MAX_ADULT_IN_ROOM']);
        define('CONST_PACKAGE_TRAVELER_MAX_CHILD_IN_ROOM', $BootStrap['CONST_PACKAGE_TRAVELER_MAX_CHILD_IN_ROOM']);
        define('CONST_PACKAGE_TRAVELER_MAX_INFANT_IN_ROOM', $BootStrap['CONST_PACKAGE_TRAVELER_MAX_INFANT_IN_ROOM']);
//        -------


        $this->callusnumber = $BootStrap['callusnumber'];
        $this->tollfreenumber = $BootStrap['tollfreenumber'];
        $this->adminEmail = $BootStrap['adminEmail'];

        $this->objMdl = new Admin_Model_CRUD();
        $this->tablename = "tb_tbb2c_packages_master";
        $this->objHelperGeneral = $this->_helper->General;
    }

    public function indexAction() {
        $params = $this->getRequest()->getParams();
        $this->view->CONST_PACKAGE_TRAVELER_MAX_ROOM = CONST_PACKAGE_TRAVELER_MAX_ROOM;
        $this->view->CONST_PACKAGE_TRAVELER_MAX_ADULT_IN_ROOM = CONST_PACKAGE_TRAVELER_MAX_ADULT_IN_ROOM;
        $this->view->CONST_PACKAGE_TRAVELER_MAX_CHILD_IN_ROOM = CONST_PACKAGE_TRAVELER_MAX_CHILD_IN_ROOM;
        $this->view->CONST_PACKAGE_TRAVELER_MAX_INFANT_IN_ROOM = CONST_PACKAGE_TRAVELER_MAX_INFANT_IN_ROOM;
        $this->view->modulename = $this->modulename;

        $paramsss = explode('-', trim($params['name'], '.html'));
        krsort($paramsss); // reverse sort the array to find all url param ids for fetching recor
        $listkeys = ['mp', 'tourtype', 'gtxid', 'catid', 'pkgid', 'destname']; // array of parameters

        $i = 0;
        foreach ($paramsss as $value) {
            if ($i == count($listkeys))
                break;
            $param[$listkeys[$i]] = $value;
            $i++;
        }

        $catId = isset($param['catid']) ? (int) $param['catid'] : '';
        $gtxId = isset($param['gtxid']) ? (int) $param['gtxid'] : '';
        $packageId = isset($param['pkgid']) ? (int) $param['pkgid'] : '';
        $tourtype = isset($param['tourtype']) ? (int) $param['tourtype'] : '';
        $mealplantype = (int) $param['mp'];
        $keywords = $param['destname'];


        $customize = (isset($param['customize'])) ? $param['customize'] : 0; // for customization [ 1 , 0]

        $this->view->mealplantype = $mealplantype;

        $model = new Detail_Model_PackageMapper();

        // check package for inactive case
        $checkPackaageSysID = $model->checkPackaageSysID($gtxId);
        if (!empty($checkPackaageSysID)) {
            $packageId = $checkPackaageSysID['PkgSysId'];
        }

        $isModified = 0;

        if (isset($_SESSION['TravelAgent']['session']) && !empty($_SESSION['TravelAgent']['session'])) {
            $getDetail = $model->fetchDetails($catId, $gtxId, $packageId, 'B2B'); // 4th optional parameter [ B2B | B2C ]
        } else {
            $getDetail = $model->fetchDetails($catId, $gtxId, $packageId, 'B2C'); // 4th optional parameter [ B2B | B2C ]
        }

//        echo "<pre>";print_r($getDetail['PackageCategory']);die;
        if (!$getDetail) {
            
        }

        if (!$getDetail['tourType']) {
            $this->_redirect($this->baseUrl);
        }

        $PackageType = $getDetail['PackageType']; // get package type [ Readymade , Dynamic ]
//        $PackageSubType = $getDetail['PackageSubType']; // get package Sub type 
        $detail['PackageSubType'] = $getDetail['PackageSubType']; // get the package sub category
        // used for update package and send enquiry | category | meal plan change options
        if (isset($_SESSION['TravelAgent']['session']) && !empty($_SESSION['TravelAgent']['session'])) {
            $priceJsonViewFile = $this->objHelperGeneral->getCategoryAndPriceArrayJSON($getDetail['tourTypeFull'], 'B2B', $PackageType, $detail['PackageSubType']);
        } else {
            $priceJsonViewFile = $this->objHelperGeneral->getCategoryAndPriceArrayJSON($getDetail['tourTypeFull'], 'B2C', $PackageType, $detail['PackageSubType']);
        }
        //echo "<pre>";print_r($priceJsonViewFile);exit;
        $this->view->priceJsonViewFile = $priceJsonViewFile['priceArrJson'];

        $detail = $this->objHelperGeneral->createArrayDetailDynamic($getDetail, ['catid' => $catId, 'gtxid' => $gtxId, 'pkgid' => $packageId, 'tourtype' => $tourtype]);

        $detail['PackageSubType'] = $getDetail['PackageSubType']; // get the package sub category

        $detail['TPId'] = $getDetail['TPId']; // get tpid same for all in Dynamic package case
        $detail['Destinations'] = $getDetail['Destinations']; // get package destinations


        $CONST_MEAL_PLAN_ARR = unserialize(CONST_MEAL_PLAN_ARR);
        $detail['MPType'] = $CONST_MEAL_PLAN_ARR[$mealplantype]; // get package meal plan type

        $detail['hotels_array_included_only'] = $getDetail['hotels_array_included_only']; // get hotels included in all itineraries

        $detail['CategoriesArray'] = $priceJsonViewFile['priceArrJson'];  // override the variable here new version is included cp,ap,map plan prices too



        $priceArrJson = $detail['CategoriesArray'];
        $tourTypeChar = ($tourtype == 1) ? 'P' : 'G'; // if private than P else G for Group tour type
        $pkgprice = $this->objHelperGeneral->getPackagePriceV3(Catabatic_Helper::getPackageType($catId), $tourTypeChar, $priceArrJson, $mealplantype, true);  // Param 4: true ( if calculate discounted price )
        $pkgpriceDiscount = $this->objHelperGeneral->getPackagePriceV3(Catabatic_Helper::getPackageType($catId), $tourTypeChar, $priceArrJson, $mealplantype);  // Param 4: true ( if calculate discounted price )

        $detail['CategoriesArray']['NetPrice'] = $pkgprice;
        $detail['CategoriesArray']['DiscountNetPrice'] = $pkgpriceDiscount;
        $detail['PackageCategory'] = $getDetail['PackageCategory'];
        $detail['Image'] = $getDetail['Image'];
        $detail['DestinationsId'] = $getDetail['DestinationsId'];
        $detail['isFixedDeparture'] = $getDetail['isFixedDeparture']; 

        if ($PackageType == $this->packageTypeStatic) {

            // get transfers array details 
            $TransfersArrayCar = $this->objHelperGeneral->getTransfersArray('car', $detail['TransfersMaster']);
            $OtherServicesArray = $this->objHelperGeneral->getTransfersArray('otherservices', $detail['OtherServices']);
//                    echo '<pre>'; print_r( $OtherServicesArray ); die;

            $customize = 1;

            if ($customize) {
                $sessionid = $this->objHelperGeneral->checksession((int) $packageId, $tourtype, $catId); // create the session

                if (!$sessionid) {
                    //                $this->PSESS_TIMESTAMP  = $sessionid;
                    $others = ['services' => $OtherServicesArray, 'transfers' => $TransfersArrayCar];
                    $this->objHelperGeneral->copysession($packageId, $tourtype, $catId, $detail['itinerariesArray'], $pkgprice, $others); // copy itinerary details to session
                }

                $detail['itinerariesArray'] = $this->_storage->packageSession[$packageId][$tourtype][$catId]['itineraries'];
            }


            @$isModified = $this->_storage->packageSession[$packageId][$tourtype][$catId]['others']['modified']; // get if the package is modified or not
            // show session price only if the package is modified
            if ($isModified) {
                $this->view->sessionPrice = $this->_storage->packageSession[$packageId][$tourtype][$catId]['others']['price'];
            }
        }

        // start : fetch related destinations here 

        $Destinations = $getDetail['Destinations']; // get the destination here

        $whereDestination = '';

        foreach (explode(',', $Destinations) as $key => $value) {
            $whereDestinationArr[] = $value;
        }



        $where = [
            'IsMarkForDel' => 0,
            'IsActive' => 1,
            'IsPublish' => 1,
            'ItemType' => 1 // for Tour Package 1
        ];


        // param conditions

        if (count($whereDestinationArr) > 0) {

            $str = $operator = '';
            foreach ($whereDestinationArr as $k => $val) {
                $operator = ($k != 0) ? ' OR ' : '';
                if ($val) {
                    $str .= " $operator ( Destinations LIKE ('%" . $val . "%') OR Countries LIKE ('%" . $val . "%') ) ";
                }
            }
            if ($str) {
                $whereDestination .= ' (' . $str . ') ';
            }
        }
        $whereCustom = " (1=1) and (`PkgSysId` <> '$packageId') ";
        $whereCustom .= ($whereDestination) ? " AND $whereDestination " : "";

        $currentTime = date('Y-m-d 00:00:00');

        $whereCustom .= " AND ( (`PkgValidFrom` <= '$currentTime') AND (`PkgValidUntil` >= '$currentTime') ) ";
        $whereCustom .= " AND ( `BookingValidUntil` >= '$currentTime')  ";

        $relatedPackages = $this->objMdl->rv_select_all_custom_query($this->tablename, ['Destinations', 'HotDeal', 'LongJsonInfo', 'StarRating', 'Nights', 'GTXPkgId', 'PackageType', 'Image', 'PkgSysId'], $where, $whereCustom, ['MinPrice' => 'ASC'], 4);

//        start : get package price here
        if (isset($_SESSION['TravelAgent']['session']) && !empty($_SESSION['TravelAgent']['session'])) {
            $relatedPackagesArray = $this->objHelperGeneral->getPackageCardDetails($relatedPackages, 'B2B');
        } else {
            $relatedPackagesArray = $this->objHelperGeneral->getPackageCardDetails($relatedPackages, 'B2C');
        }

//        end : get package price here
        // end : fetch related destinations here 
        // Get region id from destination table
        $whereCustomDes = (" FIND_IN_SET (Title, '" . $Destinations . "')");
        $whereDes = ['IsActive' => 1];
        $resultRegionId = $this->objMdl->rv_select_all_custom_query('tb_tbb2c_destinations', ['region_id'], $whereDes, $whereCustomDes, ['Title' => 'ASC'], 6);
        if ($resultRegionId) {
            $RegionId = array();
            foreach ($resultRegionId as $value) {
                $RegionId[] = $value['region_id'];
            }
        }
        $RegionId = array_unique($RegionId);

        if ($Destinations) {

            $selectTitle = explode(",", $Destinations);
            foreach ($selectTitle as $value) {
                $checkdata = $this->objMdl->selectOne('tb_tbb2c_destinations', ['Bannerimg', 'DesSysId'], [ 'Title' => $value]);
                if (!empty($checkdata['Bannerimg']) && !empty($checkdata)) {
                    $array[] = [
                        'Bannerimg' => $checkdata['Bannerimg'],
                        'DesSysId' => $checkdata['DesSysId'],
                    ];
                }
            }
            $this->view->bannerArray = $array;
            $this->view->DesSysId = $checkdata['DesSysId'];
        }

        /* SEO KEYWORD */
        $detailLayout = array();
        $detailLayout['Keyword'] = $getDetail['Keyword']; // get package Keyword
        $detailLayout['Description'] = $getDetail['Description']; // get package Description
        $detailLayout['Metatag'] = $getDetail['Metatag']; // get package Metatag
        $detailLayout['PackageTitle'] = $getDetail['itementries']['Name'] . ' (' . Catabatic_Helper::getPackageType($catId) . ')'; // get package name

        $packageTheme = $this->objMdl->rv_select_all('tbl_pack_type', ['packType', 'Title'], [ 'IsActive' => 1], ['packType' => 'ASC']);
//        echo "<pre>";print_r($packageTheme);die;
        $this->view->packageTheme = $packageTheme;
        $this->view->catId = $catId;
        $this->view->gtxId = $gtxId;
        $this->view->packageId = $packageId;
        $this->view->tourtype = $tourtype;
        $this->view->detail = $detail;
        $this->view->baseUrl = $this->baseUrl;
        $this->view->callusnumber = $this->callusnumber;
        $this->view->keywords = $keywords;
        $this->view->detailLayout = $detailLayout;
        $this->view->relatedPackages = $relatedPackagesArray;
        $this->view->resultExpert = $resultExpert;
        $this->view->isModified = $isModified;
        $this->view->params = $params;
        $this->view->actionurl = $this->baseUrl . 'detail/index/index/pkgid/' . $packageId . '/gtxid/' . $gtxId . '/catid/' . $catId . '/tourtype/' . $tourtype . '/';
    }

    
    
    public function indexAjaxDataAction() {
        $params = $this->getRequest()->getParams();
      
        $this->view->layout()->disableLayout();
        $this->view->CONST_PACKAGE_TRAVELER_MAX_ROOM = CONST_PACKAGE_TRAVELER_MAX_ROOM;
        $this->view->CONST_PACKAGE_TRAVELER_MAX_ADULT_IN_ROOM = CONST_PACKAGE_TRAVELER_MAX_ADULT_IN_ROOM;
        $this->view->CONST_PACKAGE_TRAVELER_MAX_CHILD_IN_ROOM = CONST_PACKAGE_TRAVELER_MAX_CHILD_IN_ROOM;
        $this->view->CONST_PACKAGE_TRAVELER_MAX_INFANT_IN_ROOM = CONST_PACKAGE_TRAVELER_MAX_INFANT_IN_ROOM;
        $this->view->modulename = $this->modulename;

        $paramsss = explode('-', trim($params['name'], '.html'));
        krsort($paramsss); // reverse sort the array to find all url param ids for fetching recor
        $listkeys = ['mp', 'tourtype', 'gtxid', 'catid', 'pkgid', 'destname']; // array of parameters

        $i = 0;
        foreach ($paramsss as $value) {
            if ($i == count($listkeys))
                break;
            $param[$listkeys[$i]] = $value;
            $i++;
        }

        $catId = isset($param['catid']) ? (int) $param['catid'] : '';
        $gtxId = isset($param['gtxid']) ? (int) $param['gtxid'] : '';
        $packageId = isset($param['pkgid']) ? (int) $param['pkgid'] : '';
        $tourtype = isset($param['tourtype']) ? (int) $param['tourtype'] : '';
        $mealplantype = (int) $param['mp'];
        $keywords = $param['destname'];


        $customize = (isset($param['customize'])) ? $param['customize'] : 0; // for customization [ 1 , 0]

        $this->view->mealplantype = $mealplantype;

        $model = new Detail_Model_PackageMapper();

        // check package for inactive case
        $checkPackaageSysID = $model->checkPackaageSysID($gtxId);
        if (!empty($checkPackaageSysID)) {
            $packageId = $checkPackaageSysID['PkgSysId'];
        }

        $isModified = 0;

        if (isset($_SESSION['TravelAgent']['session']) && !empty($_SESSION['TravelAgent']['session'])) {
            $getDetail = $model->fetchDetails($catId, $gtxId, $packageId, 'B2B'); // 4th optional parameter [ B2B | B2C ]
        } else {
            $getDetail = $model->fetchDetails($catId, $gtxId, $packageId, 'B2C'); // 4th optional parameter [ B2B | B2C ]
        }

//        echo "<pre>";print_r($getDetail['PackageCategory']);die;
        if (!$getDetail) {
            
        }

        if (!$getDetail['tourType']) {
            $this->_redirect($this->baseUrl);
        }

        $PackageType = $getDetail['PackageType']; // get package type [ Readymade , Dynamic ]
//        $PackageSubType = $getDetail['PackageSubType']; // get package Sub type 
        $detail['PackageSubType'] = $getDetail['PackageSubType']; // get the package sub category
        // used for update package and send enquiry | category | meal plan change options
        if (isset($_SESSION['TravelAgent']['session']) && !empty($_SESSION['TravelAgent']['session'])) {
            $priceJsonViewFile = $this->objHelperGeneral->getCategoryAndPriceArrayJSON($getDetail['tourTypeFull'], 'B2B', $PackageType, $detail['PackageSubType']);
        } else {
            $priceJsonViewFile = $this->objHelperGeneral->getCategoryAndPriceArrayJSON($getDetail['tourTypeFull'], 'B2C', $PackageType, $detail['PackageSubType']);
        }
        //echo "<pre>";print_r($priceJsonViewFile);exit;
        $this->view->priceJsonViewFile = $priceJsonViewFile['priceArrJson'];

        $detail = $this->objHelperGeneral->createArrayDetailDynamic($getDetail, ['catid' => $catId, 'gtxid' => $gtxId, 'pkgid' => $packageId, 'tourtype' => $tourtype]);

        $detail['PackageSubType'] = $getDetail['PackageSubType']; // get the package sub category

        $detail['TPId'] = $getDetail['TPId']; // get tpid same for all in Dynamic package case
        $detail['Destinations'] = $getDetail['Destinations']; // get package destinations


        $CONST_MEAL_PLAN_ARR = unserialize(CONST_MEAL_PLAN_ARR);
        $detail['MPType'] = $CONST_MEAL_PLAN_ARR[$mealplantype]; // get package meal plan type

        $detail['hotels_array_included_only'] = $getDetail['hotels_array_included_only']; // get hotels included in all itineraries

        $detail['CategoriesArray'] = $priceJsonViewFile['priceArrJson'];  // override the variable here new version is included cp,ap,map plan prices too



        $priceArrJson = $detail['CategoriesArray'];
        $tourTypeChar = ($tourtype == 1) ? 'P' : 'G'; // if private than P else G for Group tour type
        $pkgprice = $this->objHelperGeneral->getPackagePriceV3(Catabatic_Helper::getPackageType($catId), $tourTypeChar, $priceArrJson, $mealplantype, true);  // Param 4: true ( if calculate discounted price )
        $pkgpriceDiscount = $this->objHelperGeneral->getPackagePriceV3(Catabatic_Helper::getPackageType($catId), $tourTypeChar, $priceArrJson, $mealplantype);  // Param 4: true ( if calculate discounted price )

        $detail['CategoriesArray']['NetPrice'] = $pkgprice;
        $detail['CategoriesArray']['DiscountNetPrice'] = $pkgpriceDiscount;
        $detail['PackageCategory'] = $getDetail['PackageCategory'];
        $detail['Image'] = $getDetail['Image'];
        $detail['DestinationsId'] = $getDetail['DestinationsId'];


        if ($PackageType == $this->packageTypeStatic) {

            // get transfers array details 
            $TransfersArrayCar = $this->objHelperGeneral->getTransfersArray('car', $detail['TransfersMaster']);
            $OtherServicesArray = $this->objHelperGeneral->getTransfersArray('otherservices', $detail['OtherServices']);
//                    echo '<pre>'; print_r( $OtherServicesArray ); die;

            $customize = 1;

            if ($customize) {
                $sessionid = $this->objHelperGeneral->checksession((int) $packageId, $tourtype, $catId); // create the session

                if (!$sessionid) {
                    //                $this->PSESS_TIMESTAMP  = $sessionid;
                    $others = ['services' => $OtherServicesArray, 'transfers' => $TransfersArrayCar];
                    $this->objHelperGeneral->copysession($packageId, $tourtype, $catId, $detail['itinerariesArray'], $pkgprice, $others); // copy itinerary details to session
                }

                $detail['itinerariesArray'] = $this->_storage->packageSession[$packageId][$tourtype][$catId]['itineraries'];
            }


            @$isModified = $this->_storage->packageSession[$packageId][$tourtype][$catId]['others']['modified']; // get if the package is modified or not
            // show session price only if the package is modified
            if ($isModified) {
                $this->view->sessionPrice = $this->_storage->packageSession[$packageId][$tourtype][$catId]['others']['price'];
            }
        }

        // start : fetch related destinations here 

        $Destinations = $getDetail['Destinations']; // get the destination here

        $whereDestination = '';

        foreach (explode(',', $Destinations) as $key => $value) {
            $whereDestinationArr[] = $value;
        }



        $where = [
            'IsMarkForDel' => 0,
            'IsActive' => 1,
            'IsPublish' => 1,
            'ItemType' => 1 // for Tour Package 1
        ];


        // param conditions

        if (count($whereDestinationArr) > 0) {

            $str = $operator = '';
            foreach ($whereDestinationArr as $k => $val) {
                $operator = ($k != 0) ? ' OR ' : '';
                if ($val) {
                    $str .= " $operator ( Destinations LIKE ('%" . $val . "%') OR Countries LIKE ('%" . $val . "%') ) ";
                }
            }
            if ($str) {
                $whereDestination .= ' (' . $str . ') ';
            }
        }
        $whereCustom = " (1=1) and (`PkgSysId` <> '$packageId') ";
        $whereCustom .= ($whereDestination) ? " AND $whereDestination " : "";

        $currentTime = date('Y-m-d 00:00:00');

        $whereCustom .= " AND ( (`PkgValidFrom` <= '$currentTime') AND (`PkgValidUntil` >= '$currentTime') ) ";
        $whereCustom .= " AND ( `BookingValidUntil` >= '$currentTime')  ";

        $relatedPackages = $this->objMdl->rv_select_all_custom_query($this->tablename, ['Destinations', 'HotDeal', 'LongJsonInfo', 'StarRating', 'Nights', 'GTXPkgId', 'PackageType', 'Image', 'PkgSysId'], $where, $whereCustom, ['MinPrice' => 'ASC'], 4);

//        start : get package price here
        if (isset($_SESSION['TravelAgent']['session']) && !empty($_SESSION['TravelAgent']['session'])) {
            $relatedPackagesArray = $this->objHelperGeneral->getPackageCardDetails($relatedPackages, 'B2B');
        } else {
            $relatedPackagesArray = $this->objHelperGeneral->getPackageCardDetails($relatedPackages, 'B2C');
        }

//        end : get package price here
        // end : fetch related destinations here 
        // Get region id from destination table
        $whereCustomDes = (" FIND_IN_SET (Title, '" . $Destinations . "')");
        $whereDes = ['IsActive' => 1];
        $resultRegionId = $this->objMdl->rv_select_all_custom_query('tb_tbb2c_destinations', ['region_id'], $whereDes, $whereCustomDes, ['Title' => 'ASC'], 6);
        if ($resultRegionId) {
            $RegionId = array();
            foreach ($resultRegionId as $value) {
                $RegionId[] = $value['region_id'];
            }
        }
        $RegionId = array_unique($RegionId);

        if ($Destinations) {

            $selectTitle = explode(",", $Destinations);
            foreach ($selectTitle as $value) {
                $checkdata = $this->objMdl->selectOne('tb_tbb2c_destinations', ['Bannerimg', 'DesSysId'], [ 'Title' => $value]);
                if (!empty($checkdata['Bannerimg']) && !empty($checkdata)) {
                    $array[] = [
                        'Bannerimg' => $checkdata['Bannerimg'],
                        'DesSysId' => $checkdata['DesSysId'],
                    ];
                }
            }
            $this->view->bannerArray = $array;
            $this->view->DesSysId = $checkdata['DesSysId'];
        }

        /* SEO KEYWORD */
        $detailLayout = array();
        $detailLayout['Keyword'] = $getDetail['Keyword']; // get package Keyword
        $detailLayout['Description'] = $getDetail['Description']; // get package Description
        $detailLayout['Metatag'] = $getDetail['Metatag']; // get package Metatag
        $detailLayout['PackageTitle'] = $getDetail['itementries']['Name'] . ' (' . Catabatic_Helper::getPackageType($catId) . ')'; // get package name

        $packageTheme = $this->objMdl->rv_select_all('tbl_pack_type', ['packType', 'Title'], [ 'IsActive' => 1], ['packType' => 'ASC']);
//        echo "<pre>";print_r($packageTheme);die;
        $this->view->packageTheme = $packageTheme;
        $this->view->catId = $catId;
        $this->view->gtxId = $gtxId;
        $this->view->packageId = $packageId;
        $this->view->tourtype = $tourtype;
        $this->view->detail = $detail;
        $this->view->baseUrl = $this->baseUrl;
        $this->view->callusnumber = $this->callusnumber;
        $this->view->keywords = $keywords;
        $this->view->detailLayout = $detailLayout;
        $this->view->relatedPackages = $relatedPackagesArray;
        $this->view->resultExpert = $resultExpert;
        $this->view->isModified = $isModified;
        $this->view->params = $params;
        $this->view->actionurl = $this->baseUrl . 'detail/index/index/pkgid/' . $packageId . '/gtxid/' . $gtxId . '/catid/' . $catId . '/tourtype/' . $tourtype . '/';
    }
    public function sendQueryAction() {

        $this->_helper->layout()->disableLayout('');
        $this->_helper->viewRenderer->setNoRender(true);
        if ($this->getRequest()->isPost()) {

            $param = $this->getRequest()->getParams();


//            echo '<pre>'; print_r($param); die;
            //check if any of the inputs are empty
            if (empty($param['inputName']) || empty($param['inputEmail']) || empty($param['inputPhone']) || empty($param['inputMessage'])) {
                $result = ['status' => false, 'message' => 'Please fill out the form completely.'];
            } else {

                $DestinationID = '';
                $Destinations = '';
                $roomjson = '';

                $this->postFields = "";
                $this->postFields .= "&AgencySysId=" . $this->AgencyId;
                $this->postFields .= "&TravelPlanId=" . $param['TravelPlanId'];
                $this->postFields .= "&FirstName=" . $param['inputName'];
                $this->postFields .= "&LastName=";
                $this->postFields .= "&Email=" . $param['inputEmail'];
                $this->postFields .= "&MobileNumber=" . $param['inputPhone'];
                $this->postFields .= "&PriceRange=" . $param['PriceRange']; // 2000-50000
                $this->postFields .= "&PKGCheckInDate=" . $param['PKGCheckInDate']; // 12/08/2017
                $this->postFields .= "&PKGCheckOutDate=" . $param['PKGCheckOutDate']; // 19/08/2017
                $this->postFields .= "&MinPrice=" . $param['MinPrice']; // 2000
                $this->postFields .= "&MaxPrice=" . $param['MaxPrice']; // 50000
                $this->postFields .= "&DestinationID=" . $DestinationID; // destination id
                $this->postFields .= "&Destination=" . $Destinations; // destination value
                $this->postFields .= "&roomjson=" . $roomjson; // destination value

                try {
                    $model = new Gtxwebservices_Model_Webservices();
                    $getPackagesData = $model->sendQuery($this->postFields);
//                    print_r($getPackagesData); die;
                    $message = "Your query has been sent successfully.";
                    $status = true;
                } catch (Zend_Exception $error) {
                    $message = $this->view->error_msg = $error->getMessage();
                    $status = false;
                }
                $result = ['status' => $status, 'message' => $message];
            }
        } else {

            $result = ['status' => false, 'message' => 'Invalid Request!'];
        }

        echo Zend_Json::encode($result);
        exit;
    }

    public function getOptionalHotelAction() {

        $this->_helper->layout()->disableLayout();
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {
                $param = $this->getRequest()->getParams();
                $day = $param['day'];

                $categoryId = $param['categoryId'];
                $packageId = $param['packageId'];
                $gtxID = $param['gtxID'];
                $model = new Detail_Model_PackageMapper();



                $getDetail = $model->fetchDayWiseHotelDetails($categoryId, $gtxID, $packageId, $day);



//                 if($type == 'H'){
//                     $getDetail = $model->fetchHotelDetails($categoryId, $gtxID, $packageId,$hotelId);        
//                 } else if($type == 'A') {
//                     $getDetail = $model->fetchActivityDetails($categoryId, $gtxID, $packageId,$hotelId);        
//                 } else {
//                     $getDetail = $model->fetchSightSeeingDetails($categoryId, $gtxID, $packageId,$hotelId); 
//                 }

                $this->view->hotelData = $getDetail;
            }
        }
    }

    // get all the other options available for hotel per itinerary
    public function changeOptionsAction() {
        $this->_helper->layout()->disableLayout();

        $getDetail = [];
        $message = '';

        if ($this->getRequest()->getMethod() === 'GET') {

            $param = $this->getRequest()->getParams();

//            echo '<pre>'; print_r($param); die;

            $day = (isset($param['day'])) ? $param['day'] : 0;
            $categoryid = (isset($param['categoryid'])) ? $param['categoryid'] : 1;
            $packageid = (isset($param['packageid'])) ? $param['packageid'] : 0;
            $gtxid = (isset($param['gtxid'])) ? $param['gtxid'] : 0;
            $type = (isset($param['type'])) ? $param['type'] : '';
            $tourtype = (isset($param['tourtype'])) ? $param['tourtype'] : 0;
            $itinerary = (isset($param['itinerary'])) ? $param['itinerary'] : 0;
            $sid = (isset($param['sid']) && !empty($param['sid'])) ? $param['sid'] : 0;
            $group = (isset($param['group']) && !empty($param['group'])) ? $param['group'] : '';
            $countit = (isset($param['countit']) && !empty($param['countit'])) ? $param['countit'] : 1;

            $mp = (isset($param['mp'])) ? $param['mp'] : 0;

            $resultData = $includedItemPriceArray = $markupDetialsArray = [];
            $itemName = '';

            // check the change options
            if (strtolower($type) === 'h') {
                $model = new Detail_Model_PackageMapper();
                $resultData = $model->fetchDayWiseOptionsDetails($categoryid, $gtxid, $packageid, $day, $sid, 'h');
                $itemName = 'hotel';
            } else if (strtolower($type) === 'a') {
                $model = new Detail_Model_PackageMapper();
                $resultData = $model->fetchDayWiseOptionsDetails($categoryid, $gtxid, $packageid, $day, $sid, 'a');
                $itemName = 'activity';
            } else if (strtolower($type) === 's') {
                $model = new Detail_Model_PackageMapper();
                $resultData = $model->fetchDayWiseOptionsDetails($categoryid, $gtxid, $packageid, $day, $sid, 's');
                $itemName = 'sightSeeing';
            } else if (strtolower($type) === 'car') {
                $model = new Detail_Model_PackageMapper();
                $resultData = $model->fetchTransfersDetails($packageid, $gtxid, $tourtype);
                $itemName = 'transfers';
            } else if (strtolower($type) === 'cc') {
                $model = new Detail_Model_PackageMapper();
                $resultData = $model->fetchPackageCateogies($packageid, $gtxid);
//                $itemName   = 'transfers';
            }

//            $type   = 'hotel';
            $masterDataArray = $this->objHelperGeneral->getMasterData($packageid, $type);

            $markupDetialsArray = $this->objHelperGeneral->getMarkupDetailsArray($packageid, $tourtype, $categoryid);

//            $masterDataArray1 = $this->objHelperGeneral->getPackagePriceArray( $packageid  );
//            $this->getPackageMasterData( $packageid );
//            $this->objHelperGeneral->getPackageJSONDataArray( $packageid );
//            echo '<pre>'; print_r( $markupDetialsArray ); die;
//echo $includedItemPriceArray; die;
//        echo '<pre>'; print_r($resultData); die;
            // check for the option type | only if not the category chage popup
            if (strtolower($type) !== 'cc') {

                if ($this->objHelperGeneral->checksession((int) $packageid, $tourtype, $categoryid)) {
                    if (count($resultData['itemArray'])) {

                        $PSESS = $this->_storage->packageSession; // get the session values

                        $tempArr = [];
                        $IsIncluded = false;

                        if (strtolower($type) === 'car') {

                            foreach ($resultData['itemArray'][0] as $key => $value) {
//            echo '<pre>'; print_r( $resultData['itemArray'][0] ); die;
//                                echo $key;
//            echo '<pre>'; print_r( $PSESS[$packageid][$tourtype][$categoryid]['others'][$itemName]);
// echo '</pre>'; 
//                                if( isset($PSESS[$packageid][$tourtype][$categoryid]['others'][$itemName]) ) {
                                $IsIncluded = $PSESS[$packageid][$tourtype][$categoryid]['others'][$itemName][$key]['IsIncluded']; // check the session here
//                                }
//                                else {
//                                    $IsIncluded = false;
//                                }
//                                var_dump($IsIncluded);
                                if ($IsIncluded) {
                                    $itemid = $value['fixTransSysId']; // get recent updated item id
                                }

                                $tempArr[] = [
                                    'fixTransSysId' => $value['fixTransSysId'],
                                    'vehSysId' => $value['vehSysId'],
                                    'cityCovered' => $value['cityCovered'],
                                    'capacity' => $value['capacity'],
                                    'costPerson' => $value['costPerson'],
                                    'routeName' => @$value['routeName'],
                                    'vehicleName' => @$value['vehicleName'],
//                                    'IsIncluded'=> ($IsIncluded) ? true : false
                                    'IsIncluded' => ($value['IsIncluded']) ? true : false
                                ];
                            }
                            $resultData['itemArray'][0] = $tempArr;
//echo $itemid;die;
                        } else {
//echo $packageid , $tourtype , $categoryid ; die;
//                            echo count($resultData['itemArray'][0]);
                            if (count($resultData['itemArray'][0]) && ($resultData['itemArray'][0])) {
//                                            echo '<pre>'; print_r( $resultData['itemArray'][0] ); die;

                                foreach ($resultData['itemArray'][0] as $key => $value) {

                                    $IsIncluded = $PSESS[$packageid][$tourtype][$categoryid]['itineraries'][$itinerary][$itemName][$key]['IsIncluded']; // check the session here

                                    if ($IsIncluded) {
                                        $itemid = $value['Id']; // get recent updated item id
                                    }
                                    $tempArr[] = ['Id' => $value['Id'], 'MasterIntSysId' => $value['MasterIntSysId'], 'IsIncluded' => ($IsIncluded) ? true : false];
                                }
                                $resultData['itemArray'][0] = $tempArr;
                            } else if (count($resultData['itemArray'][1])) {
                                foreach ($resultData['itemArray'][1] as $key => $value) {

                                    $IsIncluded = $PSESS[$packageid][$tourtype][$categoryid]['itineraries'][$itinerary][$itemName][$key]['IsIncluded']; // check the session here

                                    if ($IsIncluded) {
                                        $itemid = $value['Id']; // get recent updated item id
                                    }
                                    $tempArr[] = ['Id' => $value['Id'], 'MasterIntSysId' => $value['MasterIntSysId'], 'IsIncluded' => ($IsIncluded) ? true : false];
                                }
                                $resultData['itemArray'][1] = $tempArr;
                            }
                        }
                    }
                }
            }
//                        echo '<pre>'; print_r($resultData['itemArray']); die;
//                echo $itemid ;
            if ((strtolower($type) === 'h') || (strtolower($type) === 'car')) {
                if (isset($itemid)) {
                    $itemprice = $this->objHelperGeneral->getSelectedItemRate($itemid, $masterDataArray, $type); // get included item price
                    $includedItemPriceArray[$type] = $itemprice;
                }
            }
//                    echo '<pre>'; print_r($masterDataArray); die;
            $status = true;
        } else {
            $status = false;
            $message = 'Invalid request!';
        }

//        echo '<pre>'; print_r($resultData); die;
//        echo '<pre>'; var_dump($resultData);



        $this->view->status = $status;
        $this->view->message = $message;
        $this->view->resultData = $resultData;
        $this->view->param = $param;
        $this->view->sessionPrice = $this->_storage->packageSession[$packageid][$tourtype][$categoryid]['others']['price']; // 
        $this->view->markupDetialsArray = $markupDetialsArray;
//        $this->view->itemPriceArray = $itemPriceArray;
        $this->view->includedItemPriceArray = $includedItemPriceArray;
        $this->view->baseUrl = $this->baseUrl;
        $this->view->mealplantype = $mp;
    }

    // view all the other options available in modal popup
    public function viewOptionsAction() {
        $this->_helper->layout()->disableLayout();

        $getDetail = [];
        $message = '';

        if ($this->getRequest()->getMethod() === 'GET') {

            $param = $this->getRequest()->getParams();

//            var_dump($this->PSESS->packageid); die;
//            echo '<pre>'; print_r($param); die;

            $day = (isset($param['day'])) ? $param['day'] : '';
            $categoryid = (isset($param['categoryid'])) ? $param['categoryid'] : '';
            $packageid = $param['packageid'];
            $gtxid = $param['gtxid'];
            $type = $param['type'];
            $tourtype = (isset($param['tourtype'])) ? $param['tourtype'] : 0;
            $itinerary = (isset($param['itinerary'])) ? $param['itinerary'] : 0;
            $sid = (isset($param['sid']) && !empty($param['sid'])) ? $param['sid'] : 0;

            $markupDetialsArray = [];

            $markupDetialsArray = $this->objHelperGeneral->getMarkupDetailsArray($packageid, $tourtype, $categoryid);


            // view all options available to add into services tab
            if ($type == 'services') {
                $getDetail = $this->objMdl->rv_select_row($this->tablename, ['LongJsonInfo'], ['PkgSysId' => $packageid, 'GTXPkgId' => $gtxid, 'IsMarkForDel' => 0, 'IsActive' => 1, 'IsPublish' => 1], ['PkgSysId' => 'DESC']);

                if ($getDetail['LongJsonInfo']) {

                    $LongJsonInfo = $getDetail['LongJsonInfo'];
                    $dataArray = Zend_Json::decode($LongJsonInfo);
                    $resultData = $dataArray['package']['OtherServices'];
                } else {
                    $resultData = [];
                }
            } else {

                $getDetail = $this->objMdl->rv_select_row($this->tablename, ['LongJsonInfo'], ['PkgSysId' => $packageid, 'GTXPkgId' => $gtxid, 'IsMarkForDel' => 0, 'IsActive' => 1, 'IsPublish' => 1], ['PkgSysId' => 'DESC']);

                if ($getDetail['LongJsonInfo']) {

                    $LongJsonInfo = $getDetail['LongJsonInfo'];
                    $dataArray = Zend_Json::decode($LongJsonInfo);

                    $transferMaster = $dataArray['package']['Transfers'];

                    // get transport itinerarys
                    $getTransportDetails = $this->getTransportDetails($packageid, $gtxid, strtolower($type));

//                        echo '<pre>'; print_r($transferMaster); die('here');
                    $resultData = [];
                    foreach ($getTransportDetails as $k => $v) {

                        $innerArr = $masterTemp = [];
                        foreach ($v['FinalArray'] as $key => $value) {
//                                echo '<pre>';  print_r($value); die;
                            $tempArr = [];
                            foreach ($value as $keyInner => $valueInner) {

//                                    print_r($valueInner['itemid']);
//                                $innerArr[] = [ 'Day'=>$v['Day'] , 'FromPlace'=> $value['FromPlace'], 'ToPlace'=> $value['ToPlace'], 'CostPerson'=> $value['CostPerson'] ];
                                $masterTemp = $this->objHelperGeneral->filterArrayByValueKeyPair(['fixTransSysId', $valueInner['itemid']], $transferMaster);
//                                $masterTemp = $this->objHelperGeneral->filterArrayByValueKeyPair( ['Id', $valueInner['itemid'] ] , $transferMaster );1
//                                echo '<pre>';  print_r($masterTemp);
//                                    $tempArr[] = $masterTemp[0];
//                                    $masterTemp[0];
                                $tempArr[] = [
                                    'Day' => $v['Day'],
                                    'FromPlace' => $masterTemp[0]['fromPlace'],
                                    'ToPlace' => $masterTemp[0]['toPlace'],
                                    'CostPerson' => $masterTemp[0]['costPerson']
                                ];
                            }
//                                                                echo '<pre>';  print_r($tempArr);

                            $innerArr[] = $tempArr;
//                                $innerArr[] = [ 'Day'=>$v['Day'] , 'FromPlace'=> $value['FromPlace'], 'ToPlace'=> $value['ToPlace'], 'CostPerson'=> $value['CostPerson'] ];
                        }
//                            $resultData[] = $innerArr;
                        $resultData[] = ['Day' => $v['Day'], 'FinalArray' => $innerArr];
                    }
//                        echo '<pre>'; print_r($resultData); die;
//                        $resultData     = $dataArray['package']['Transfers'];
                } else {
                    $resultData = [];
                }

//                        echo '<pre>'; print_r($resultData); die;
            }
            $status = true;

//            echo '<pre>'; print_r($resultData); die;
//            echo '<pre>'; print_r($this->_storage->packageSession[$packageid][$tourtype][$categoryid]['others']['services']);

            if ($this->objHelperGeneral->checksession((int) $packageid, $tourtype, $categoryid)) {
                if (count($resultData)) {

                    $PSESS = $this->_storage->packageSession; // get the session values

                    $tempArr = [];
                    $IsIncluded = false;

                    if (strtolower($type) === 'services') {

                        foreach ($resultData as $key => $value) {

                            $IsIncluded = $PSESS[$packageid][$tourtype][$categoryid]['others']['services'][$key]['IsIncluded']; // check the session here
                            $IsIncludedNew = isset($PSESS[$packageid][$tourtype][$categoryid]['others']['services'][$key]['IsIncludedNew']) ? $PSESS[$packageid][$tourtype][$categoryid]['others']['services'][$key]['IsIncludedNew'] : ""; // check the session here

                            if ($IsIncluded) {
                                $itemid = $value['otherSrvSysId']; // get recent updated item id
                            }
//                            print_r($value);
                            $tempArr[] = [
                                'otherSrvSysId' => $value['otherSrvSysId'],
                                'tpIntSysId' => $value['tpIntSysId'],
                                'serviceTitle' => $value['serviceTitle'],
                                'comment' => $value['comment'],
                                'currencyType' => $value['currencyType'],
                                'paxCount' => $value['paxCount'],
                                'cost' => $value['cost'],
                                'supplierName' => $value['supplierName'],
                                'rateType' => $value['rateType'],
                                'isCostInclInTP' => ($IsIncluded) ? true : false,
                                'IsIncludedNew' => ($IsIncludedNew) ? true : false
                            ];
                        }
                        $resultData = $tempArr;
                    }
                }
            }
        } else {
            $status = false;
            $message = 'Invalid request!';
        }


//        echo '<pre>'; print_r($includedItemPriceArray); die;

        $this->view->baseUrl = $this->baseUrl;
        $this->view->status = $status;
        $this->view->message = $message;
        $this->view->resultData = $resultData;
        $this->view->sessionPrice = $this->_storage->packageSession[$packageid][$tourtype][$categoryid]['others']['price']; // 
        $this->view->markupDetialsArray = $markupDetialsArray;
        $this->view->param = $param;
    }

    public function addServicesAction() {

        $this->_helper->layout()->disableLayout();

        $resultData = [];
        $message = '';

        if ($this->getRequest()->getMethod() === 'GET') {

            $param = $this->getRequest()->getParams();

//            echo '<pre>'; print_r($param); die;

            $packageid = $param['packageid'];
            $gtxid = $param['gtxid'];

            $getDetail = $this->objMdl->rv_select_row($this->tablename, ['LongJsonInfo'], ['PkgSysId' => $packageid, 'GTXPkgId' => $gtxid, 'IsMarkForDel' => 0, 'IsActive' => 1, 'IsPublish' => 1], ['PkgSysId' => 'DESC']);

            if ($getDetail['LongJsonInfo']) {

                $LongJsonInfo = $getDetail['LongJsonInfo'];
                $dataArray = Zend_Json::decode($LongJsonInfo);
                $resultData = $dataArray['package']['OtherServices'];
            } else {
                $resultData = [];
            }

//            echo '<pre>'; print_r( ($resultData)); die;

            $status = true;
        } else {
            $status = false;
            $message = 'Invalid request!';
        }

        $this->view->status = $status;
        $this->view->message = $message;
        $this->view->resultData = $resultData;
        $this->view->baseUrl = $this->baseUrl;
    }

    // helper functions below

    public function getTransportDetails($packageid, $gtxid, $type) {
        $getDetail = $this->objMdl->rv_select_row($this->tablename, ['LongJsonInfo'], ['PkgSysId' => $packageid, 'GTXPkgId' => $gtxid, 'IsMarkForDel' => 0, 'IsActive' => 1, 'IsPublish' => 1], ['PkgSysId' => 'DESC']);

        if ($getDetail['LongJsonInfo']) {
            $LongJsonInfo = $getDetail['LongJsonInfo'];
            $dataArray = Zend_Json::decode($LongJsonInfo);
            $resultData = $dataArray['package']['Itineraries']['Itinerary'];
            $masterData = $dataArray['package']['Transfers'];
//            echo '<pre>'; print_r( ($resultData)); die;
            // param : itinerary array 
            // return : array of transports
            $result = $this->objHelperGeneral->getTransportItinerary($resultData, $masterData, $type);

//            echo '<pre>'; print_r( $result); die;
        } else {
            $result = [];
        }

        return $result;
    }

    public function getPackageMasterData($packageid) {
        $model = new Admin_Model_CRUD();
        $result = $model->rv_select_row($this->tablename, ['LongJsonInfo'], [ 'IsMarkForDel' => 0, 'IsActive' => 1, 'IsPublish' => 1, 'PkgSysId' => $packageid], ['PkgSysId' => 'DESC']);


//        echo '<pre>'; print_r( $result ); die;

        return $result;
    }

    public function viewAction() {
        $this->view->url = '';
        if ($this->getRequest()->isGet()) {
            $this->_helper->layout()->disableLayout();
            $param = $this->getRequest()->getParams();
            $this->view->url = isset($param['id']) ? $param['id'] : '';
        }
    }
    
    public function viewproposalAction() {
//        $this->view->url = '';
//        if ($this->getRequest()->isGet()) {
//            $this->_helper->layout()->disableLayout();
            $param = $this->getRequest()->getParams();
            $this->view->url = isset($param['code']) ? $param['code'] : '';
//        }
    }
    
    

//    update or write the session here 
    public function writeSessionAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $param = $this->getRequest()->getParams();
//            echo '<pre>'; print_r($param); 
//            die;

        $type = $param['type'];
        $catid = $param['catid'];
//        $gtxid      = $param['gtxid'];
        $pkgid = $param['pkgid'];
        $tourtype = $param['tourtype']; // Group or Private
        $itinerary = (isset($param['itinerary'])) ? $param['itinerary'] : 0;
        $myaction = (isset($param['myaction'])) ? $param['myaction'] : '';
        $group = (isset($param['group'])) ? $param['group'] : '';

        $ismulti = 0;
        if ($type === 'services') {
            $itemid = $param['itemid'];
            $price = ($param['price']);
            $ismulti = 1;
        } else {
            $itemid = $param['itemid'];
            $price = ($param['price']);
        }

//        die($itemid);

        if (strtolower($type) === 'h') {
            $typeAlpha = 'hotel';
        } else if (strtolower($type) === 'a') {
            $typeAlpha = 'activity';
        } else if (strtolower($type) === 's') {
            $typeAlpha = 'sightSeeing';
        } else if (strtolower($type) === 'car') {
            $typeAlpha = 'transfers';
        } else if (strtolower($type) === 'services') {
            $typeAlpha = 'services';
        }
//        echo $type , $typeAlpha;

        $PSESS = $this->_storage->packageSession;

        $markupDetialsArray = $this->objHelperGeneral->getMarkupDetailsArray($pkgid, $tourtype, $catid); // get markup details
//        echo '<pre>'; print_r($markupDetialsArray);
//        if ( $PSESS['ID'] == $pkgid )
        if (array_key_exists($pkgid, $PSESS)) {
            // chaeck tour type
            if (array_key_exists($tourtype, $PSESS[$pkgid])) {
                // chaeck type category
                if (array_key_exists($catid, $PSESS[$pkgid][$tourtype])) {

                    if ($typeAlpha == 'hotel') {

                        if ($group === 'yes') {
//                            print_r($PSESS[$pkgid][$tourtype][$catid]); die;
                            // iterate the array
                            $tempArr = [];
                            $countITIHotel = 0;
                            foreach ($PSESS[$pkgid][$tourtype][$catid] as $keyO => $valueO) {
                                if ($keyO === 'itineraries') {
                                    $tempArr1 = [];
                                    foreach ($valueO as $keyH => $valueH) {
                                        // $keyH = itinerary id here
//echo $keyH ; print_r($valueH); die;
                                        if (is_array($valueH['hotel']) && count($valueH['hotel'])) {

                                            $innerArr = [];
                                            $MasterIntSysId = $itemidID = 0;
                                            $IsIncluded = false;
                                            foreach ($valueH['hotel'] as $key => $value) {
//                                                print_r($value);
                                                if ($keyH == $itinerary) {
                                                    $IsIncluded = ($value['itemid'] == $itemid) ? true : false;
                                                } else {
                                                    if ($value['MasterIntSysId'] == $itinerary) {
                                                        $IsIncluded = ($value['itemid'] == $itemid) ? true : false;
                                                        $countITIHotel++;
                                                    } else {
                                                        $IsIncluded = $value['IsIncluded'];
                                                    }
                                                }
                                                $MealPlanId = $value['MealPlanId'];
                                                $MasterIntSysId = $value['MasterIntSysId'];
                                                $itemidID = $value['itemid'];
                                                $innerArr[] = ['itemid' => $itemidID, 'MasterIntSysId' => $MasterIntSysId, 'MealPlanId' => $MealPlanId, 'IsIncluded' => $IsIncluded];
                                            }

                                            $PSESS[$pkgid][$tourtype][$catid]['itineraries'][$keyH][$typeAlpha] = $innerArr; // rewrite the session here
                                        }
                                    }
                                }
                            }
//                             print_r($tempArr);
//                              die('here');
//                            $PSESS[$pkgid][$tourtype][$catid]['itineraries'][$itinerary][$typeAlpha] = $tempArr;
//                            $PSESS[$pkgid][$tourtype][$catid]['itineraries'][$itinerary][$typeAlpha] = $tempArr;

                            $PSESS[$pkgid][$tourtype][$catid]['others']['price'] = $PSESS[$pkgid][$tourtype][$catid]['others']['price'] + ( $price); // update price here in session
                        } else {
                            foreach ($PSESS[$pkgid][$tourtype][$catid]['itineraries'][$itinerary][$typeAlpha] as $key => $value) {
                                $tempArr[] = ['itemid' => $value['itemid'], 'MasterIntSysId' => $value['MasterIntSysId'], 'IsIncluded' => ($value['itemid'] == $itemid) ? true : false];
                            }
                            $PSESS[$pkgid][$tourtype][$catid]['itineraries'][$itinerary][$typeAlpha] = $tempArr;

                            $PSESS[$pkgid][$tourtype][$catid]['others']['price'] = $PSESS[$pkgid][$tourtype][$catid]['others']['price'] + ( $price); // update price here in session
                        }
                    } else if (($typeAlpha == 'activity') || ($typeAlpha == 'sightSeeing')) {

                        $masterDataArray = $this->objHelperGeneral->getMasterData($pkgid, $type); // get master data for price calculation
//                        echo '<pre>'; print_r($masterDataArray); die;

                        $tempArr = [];
                        $IsIncluded = '';
                        foreach ($PSESS[$pkgid][$tourtype][$catid]['itineraries'][$itinerary][$typeAlpha] as $key => $value) {

                            if ($value['itemid'] == $itemid) {
                                $IsIncludedNew = true;
                            } else {
                                $IsIncludedNew = false;
                            }

                            // this is case only in remove action
                            if (($myaction === 'remove') && ($value['itemid'] == $itemid)) {
                                $IsIncludedNew = false;
                            }

                            $tempArr[] = ['itemid' => $value['itemid'], 'IsIncluded' => $value['IsIncluded'], 'IsIncludedNew' => $IsIncludedNew];
                        }
                        $price = $masterDataArray[$param['itemid']]['priceaditionals']['netCost']; // get price from master data array
                        // if markup type is % than add here
                        if ((array_key_exists('MarkType', $markupDetialsArray)) && ($markupDetialsArray['MarkType'] == 2)) {
                            $price = $this->objHelperGeneral->calculateMarkupOnPrice($markupDetialsArray, $price, 1);
                        }

//                        echo $price;
                        $PSESS[$pkgid][$tourtype][$catid]['itineraries'][$itinerary][$typeAlpha] = $tempArr;

                        if ($myaction === 'remove')
                            $PSESS[$pkgid][$tourtype][$catid]['others']['price'] = $PSESS[$pkgid][$tourtype][$catid]['others']['price'] - ( $price); // update price here in session
                        else
                            $PSESS[$pkgid][$tourtype][$catid]['others']['price'] = $PSESS[$pkgid][$tourtype][$catid]['others']['price'] + ( $price); // update price here in session
                    }

                    else if (($typeAlpha == 'services')) {

                        $masterDataArray = $this->objHelperGeneral->getMasterData($pkgid, $type); // get master data for price calculation
//echo $pkgid , $type;
//  echo '<pre>';
//                print_r($masterDataArray); //die;
//                        echo $itemid;
                        $tempArr = [];
                        $recentChecked = [];
                        $recentPrice = $currentPrice = 0;

                        foreach ($PSESS[$pkgid][$tourtype][$catid]['others']['services'] as $key => $value) {
                            $tempArr[] = [
                                'itemid' => $value['itemid'],
                                'IsIncluded' => $value['IsIncluded'],
                                'IsIncludedNew' => (in_array($value['itemid'], explode(',', $itemid)) ) ? true : false
                            ];

                            if ($value['IsIncludedNew']) {
                                $recentChecked[] = $value['itemid'];
                                $recentPrice += $masterDataArray[$value['itemid']]['cost'];
                            }
                            if (in_array($value['itemid'], explode(',', $itemid))) {
//                                var_dump($value['IsIncludedNew']);
//                                if(isset($value['IsIncludedNew']) && ($value['IsIncludedNew'] !=null )) {
                                $currentPrice += $masterDataArray[$value['itemid']]['cost'];
//                                }
                            }
                        }
//                        echo $currentPrice ."/". $recentPrice;

                        $price = $currentPrice - $recentPrice;

//                        print_r($recentChecked);
//                        print_r($tempArr);
                        $PSESS[$pkgid][$tourtype][$catid]['others']['services'] = $tempArr;
                        $PSESS[$pkgid][$tourtype][$catid]['others']['price'] = $PSESS[$pkgid][$tourtype][$catid]['others']['price'] + ( $price); // update price here in session
                    } else if (($typeAlpha == 'transfers')) {
                        $tempArr = [];
                        foreach ($PSESS[$pkgid][$tourtype][$catid]['others']['transfers'] as $key => $value) {
                            $tempArr[] = ['itemid' => $value['itemid'], 'IsIncluded' => ($value['itemid'] == $itemid) ? true : false];
                        }
                        $PSESS[$pkgid][$tourtype][$catid]['others']['transfers'] = $tempArr;
                        $PSESS[$pkgid][$tourtype][$catid]['others']['price'] = $PSESS[$pkgid][$tourtype][$catid]['others']['price'] + ( $price); // update price here in session
                    }

                    // write a variable here to check whether the package is modified or not
                    $PSESS[$pkgid][$tourtype][$catid]['others']['modified'] = 1;
                }
            }
        }

        $this->_storage->packageSession = $PSESS;

//                echo '<pre>';
//                print_r($this->_storage->packageSession);
//        $this->PSESS_VAL = $PSESS;
//        print_r($this->PSESS_VAL);

        echo Zend_Json::encode(['status' => 'success', 'price' => $price]);
    }

    public function flushSessionAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        /*
          if(Zend_Session::namespaceUnset($this->SESSION_NAMESPACE) !== null)
          {
          echo 'Flushed all session.';
          }
          else {
          echo 'Already Flushed.';
          }
         */
        $this->objHelperGeneral->flushSession();
        echo 'Flushed';
        exit;
    }

    public function showSessionAction() {
        echo "<pre>";
        print_r($this->_storage->packageSession);
        exit;
    }

}
