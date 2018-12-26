<?php

/* * *************************************************************
 * Catabatic Technology Pvt. Ltd.
 * File Name     : IndexController.php
 * File Desc.    : Index controller for home page front end
 * Created By    : Piyush Tiwari <piyush@catpl.co.in>
 * Created Date  : 27 June 2018
 * Updated Date  : ------------
 * ************************************************************* */

class IndexController extends Catabatic_CheckSession {

    protected $objMdl;
    protected $tablename;
    protected $tablenameDestination;
    protected $baseUrl;
    protected $tollfreenumber;
    protected $objHelperGeneral;
    protected $per_page_record;
    public $_session;
    public $customerbookinglistAPIUrl;
    public $uploadPakcagePath;
    public $uploadDestinationPath;
    public $dummyImagePackage;
    public $dummyImageDestination;
    public $myNamespace;

    public function init() {
        parent::init();
        $aConfig = $this->getInvokeArg('bootstrap')->getOptions();
        $BootStrap = $aConfig['bootstrap'];

        $this->siteName = $BootStrap['siteName'];
        $this->baseUrl = $BootStrap['siteUrl'];
        $this->tollfreenumber = $BootStrap['tollfreenumber'];

        $this->objMdl = new Admin_Model_CRUD();

        $this->tablename = "tb_tbb2c_packages_master";
        $this->tablenameTes = "tbl_testimonials";
        $this->tablenameDestination = "tb_tbb2c_destinations";
        $this->hotelTypeArr = ['Standard', 'Deluxe', 'Luxury'];

        $this->objHelperGeneral = $this->_helper->General;
        $this->per_page_record = 10;
        $this->_session = new Zend_Session_Namespace('User');

        $this->uploadPakcagePath = 'public/upload/tours/';
        $this->uploadDestinationPath = 'public/upload/destinations/';

        $this->dummyImagePackage = 'default-tour.jpg';
        $this->dummyImageDestination = 'default-destination.jpg';

        $this->enableCache = $BootStrap['enableCache'];
        $this->packageTypeStatic = $BootStrap['packageTypeDynamic'];


        $this->customerbookinglistAPIUrl = API_CUSTOMER_LIST; // from constant file

        $this->myNamespace = new Zend_Session_Namespace('MypopSess'); // get user end infomations
    }

    public function indexAction() {

        $packageDestinationArray = file_get_contents($this->baseUrl . 'public/data/dynamic/package_destinations.json');
        $decodedPackageDestinationArray = Zend_Json::decode($packageDestinationArray);
//        echo "<pre>";print_r($decodedPackageDestinationArray);die;    
         $destinatonTop = $this->objMdl->rv_select_all($this->tablenameDestination, ['DesSysId', 'Title', 'Activities', 'Hotels', 'Tours', 'Image', 'DestDescription'], ['IsActive' => 1, 'IsFeatured' => 1, 'IsPublish' => 1, 'IsMarkForDel' => 0], ['rand()' => ''], 12);
       
        $destinatonTopArr = array();
        foreach ($destinatonTop as $topdesKey => $topdesValue) {
            $desTitleValue =$topdesValue['Title'];
            $whereCustom = ($desTitleValue) ? " ( Destinations LIKE '%{$desTitleValue}%' )" : "";

                $tourResult = $this->objMdl->rv_select_row_where_custom($this->tablename, ['count(*) as totalCount'], ['IsMarkForDel' => 0, 'IsActive' => 1, 'IsPublish' => 1, 'ItemType' => 1], $whereCustom, [], '');
            
            
            $destinatonTopArr[] = [
                    'DesSysId' => $topdesValue['DesSysId'],
                    'Title' => $topdesValue['Title'],
                    'Activities' => $topdesValue['Activities'],
                    'Hotels' => $topdesValue['Hotels'],
                    'Tours' => $topdesValue['Tours'],
                    'Image' => $topdesValue['Image'],
                    'DestDescription' => $topdesValue['DestDescription'],
                    'totalCount' => $tourResult['totalCount'],
                    
                ];
        }
        
//        echo "<pre>";print_r($destinatonTopArr);die;
        $this->view->destinations = $destinatonTopArr;
        $this->view->baseUrl = $this->baseUrl;
        $this->view->tollfreenumber = $this->tollfreenumber;
        $this->view->siteName = $this->siteName;

        $trendingTours = $this->objMdl->rv_select_all('tb_tbb2c_packages_master', ['PkgSysId', 'Image', 'GTXPkgId', 'Destinations', 'Countries', 'BookingValidUntil', 'LongJsonInfo', 'Nights', 'StarRating', 'PackageType', 'PackageSubType'], ['IsActive' => 1, 'IsPublish' => 1, 'IsMarkForDel' => 0, 'ItemType' => 1, 'IsFeatured' => 1], ['PkgSysId' => 'DESC'], 6);
        $lastMinuteTours = $this->objMdl->rv_select_all('tb_tbb2c_packages_master', ['PkgSysId', 'Image', 'GTXPkgId', 'Destinations', 'Countries', 'BookingValidUntil', 'LongJsonInfo', 'Nights', 'StarRating', 'PackageType', 'PackageSubType'], ['IsActive' => 1, 'IsPublish' => 1, 'IsMarkForDel' => 0, 'ItemType' => 1, 'lastMinuteDeal' => 1], ['PkgSysId' => 'DESC'], 6);
        foreach ($trendingTours as $key => $value) {

            $destinationArr = explode(',', $value['Destinations']); // get the first destination only by extracting array
            $LongJsonInfo = Zend_Json::decode($value['LongJsonInfo']);

            if(isset($_SESSION['TravelAgent']['session']) && !empty($_SESSION['TravelAgent']['session'])){
                $categoryDetails = $this->objHelperGeneral->getCategoryAndPriceArray($LongJsonInfo['package']['TourTypes']['MarketType'], 'B2B', $value['PackageType'], $value['PackageSubType']); // get default category
            }else {
                $categoryDetails = $this->objHelperGeneral->getCategoryAndPriceArray($LongJsonInfo['package']['TourTypes']['MarketType'], 'B2C', $value['PackageType'], $value['PackageSubType']); // get default category
            }
            
            $tourImage1 = explode(',', $value['Image']);
            $tourImage = $tourImage1[0];

            $defaultCategoryId = $categoryDetails['defaultCategoryId'];
            $defaultCategory = $categoryDetails['defaultCategory'];
            $defaultTourType = $categoryDetails['defaultTourType'];
            $TPId = ($value['PackageType'] == $this->packageTypeStatic) ? $LongJsonInfo['package']['TPId'] : $categoryDetails['TPId'];
            $MPType = (!empty($categoryDetails['MPType']) && ($categoryDetails['MPType'] != 'LowestCost')) ? array_search($categoryDetails['MPType'], unserialize(CONST_MEAL_PLAN_ARR)) : 0;

            $tourTypeChar = ($defaultTourType == 1) ? 'P' : 'G'; // if private than P else G for Group tour type
            $priceArrJson = $categoryDetails['priceArrJson'];

            $displayFinalPrice = $this->objHelperGeneral->getPackagePrice($defaultCategory, $tourTypeChar, $priceArrJson, true);  // Param 4: true ( if calculate discounted price )

            $toursFinal[] = [
                'name' => $this->objHelperGeneral->trimContent($LongJsonInfo['package']['Name'], 24),
                'nameF' => $LongJsonInfo['package']['Name'], // full name of package name
                'img' => $tourImage,
                'night' => $value['Nights'],
                'price' => $this->objHelperGeneral->moneyFormatINR(round($displayFinalPrice)),
                'star' => $value['StarRating'],
                'Destination' => $value['Destinations'],
                'PkgSysId' => $value['PkgSysId'],
                'GTXPkgId' => $value['GTXPkgId'],
                'defaultCategoryId' => $defaultCategoryId,
                'defaultCategory' => $defaultCategory,
                'tourtype' => $defaultTourType,
                'TPId' => $TPId,
                'PackageType' => $value['PackageType'],
                'mp' => $MPType,
                'Countries' => $value['Countries'],
                'isFixedDeparturesPackage' => $LongJsonInfo['package']['IsFixedDeparturePackage'],
                'BookingValidUntil' => $value['BookingValidUntil'],
            ];
        }
        foreach ($lastMinuteTours as $key => $values) {

            $destinationArr = explode(',', $values['Destinations']); // get the first destination only by extracting array
            $LongJsonInfo = Zend_Json::decode($values['LongJsonInfo']);

            $categoryDetails = $this->objHelperGeneral->getCategoryAndPriceArray($LongJsonInfo['package']['TourTypes']['MarketType'], 'B2C', $values['PackageType'], $values['PackageSubType']); // get default category

            $tourImage1 = explode(',', $values['Image']);
            $tourImage = $tourImage1[0];

            $defaultCategoryId = $categoryDetails['defaultCategoryId'];
            $defaultCategory = $categoryDetails['defaultCategory'];
            $defaultTourType = $categoryDetails['defaultTourType'];
            $TPId = ($values['PackageType'] == $this->packageTypeStatic) ? $LongJsonInfo['package']['TPId'] : $categoryDetails['TPId'];
            $MPType = (!empty($categoryDetails['MPType']) && ($categoryDetails['MPType'] != 'LowestCost')) ? array_search($categoryDetails['MPType'], unserialize(CONST_MEAL_PLAN_ARR)) : 0;

            $tourTypeChar = ($defaultTourType == 1) ? 'P' : 'G'; // if private than P else G for Group tour type
            $priceArrJson = $categoryDetails['priceArrJson'];

            $displayFinalPrice = $this->objHelperGeneral->getPackagePrice($defaultCategory, $tourTypeChar, $priceArrJson, true);  // Param 4: true ( if calculate discounted price )

            $lastMinuteToursFinal[] = [
                'name' => $this->objHelperGeneral->trimContent($LongJsonInfo['package']['Name'], 24),
                'nameF' => $LongJsonInfo['package']['Name'], // full name of package name
                'img' => $tourImage,
                'night' => $values['Nights'],
                'price' => $this->objHelperGeneral->moneyFormatINR(round($displayFinalPrice)),
                'star' => $values['StarRating'],
                'Destination' => $values['Destinations'],
                'PkgSysId' => $values['PkgSysId'],
                'GTXPkgId' => $values['GTXPkgId'],
                'defaultCategoryId' => $defaultCategoryId,
                'defaultCategory' => $defaultCategory,
                'tourtype' => $defaultTourType,
                'TPId' => $TPId,
                'PackageType' => $values['PackageType'],
                'mp' => $MPType,
                'Countries' => $values['Countries'],
                'isFixedDeparturesPackage' => $LongJsonInfo['package']['IsFixedDeparturePackage'],
                'BookingValidUntil' => $values['BookingValidUntil'],
            ];
        }

        $testimonials = $this->objMdl->rv_select_all('tbl_testimonials', ['*'], [ 'status' => 1, 'IsFeatured' => 1], ['id' => 'DESC'], 5);
        $bannerDetail = $this->objMdl->rv_select_all('tb_homebanner_detail', ['banner_id', 'image', 'heading', 'description', 'url', 'opt'], [ 'status' => 1, 'isDisplayOnHome' => 1, 'isMarkForDel' => 0], ['banner_id' => 'DESC'], 10);
        $resultset = $this->objMdl->rv_select_blog_all_home('tbl_travelogues', ['*'], ['isMarkForDel' => 0,'status'=>1,'displayOnBanner'=>1], ['TravId' => 'DESC'], 10);

        $homePromotionTypeOne = $this->objMdl->rv_select_all('tbl_home_promotion', ['*'], [ 'IsmarkForDel' => 0, 'IsFeatured' => 1,'templatetype'=>1], ['promotionId' => 'DESC'],10);
        $homePromotionTypeTwo = $this->objMdl->rv_select_all('tbl_home_promotion', ['*'], [ 'IsmarkForDel' => 0, 'IsFeatured' => 1,'templatetype'=>2], ['promotionId' => 'DESC'],1);
        $homePromotionTypeThree = $this->objMdl->rv_select_all('tbl_home_promotion', ['*'], [ 'IsmarkForDel' => 0, 'IsFeatured' => 1,'templatetype'=>3], ['promotionId' => 'DESC'],1);
        $tagName = json_decode($homePromotionTypeTwo[0]['promotion_name']);
        $promotionUrl = json_decode($homePromotionTypeTwo[0]['promotion_url']);
        $tabType = json_decode($homePromotionTypeTwo[0]['tab_type']);
        $finalArrayTwo = array();
            $finalArrayTwo[0] = ['tagNAme' => $tagName->promotion_tag1,'promotionUrl' => $promotionUrl->promotion_tag_url1, 'tabType' => $tabType->tagopt1];
            $finalArrayTwo[1] = ['tagNAme' => $tagName->promotion_tag2,'promotionUrl' => $promotionUrl->promotion_tag_url2, 'tabType' => $tabType->tagopt2];
            $finalArrayTwo[2] = ['tagNAme' => $tagName->promotion_tag3,'promotionUrl' => $promotionUrl->promotion_tag_url3, 'tabType' => $tabType->tagopt3];
            $finalArrayTwo[3] = ['tagNAme' => $tagName->promotion_tag4,'promotionUrl' => $promotionUrl->promotion_tag_url4, 'tabType' => $tabType->tagopt4];
        $homePromotionTypeTwoArr = array();
        if($homePromotionTypeTwo[0]['promotionId']){
           $homePromotionTypeTwoArr = [
            'promotionId' => $homePromotionTypeTwo[0]['promotionId'],
            'templatetype' => $homePromotionTypeTwo[0]['templatetype'],
            'promotion_image' => $homePromotionTypeTwo[0]['promotion_image'],
            'finalArrayTwo' =>$finalArrayTwo,
        ]; 
        }
        
        $resultPromotionCategory = $this->objMdl->getCmsdata('tbl_promotion_category', ['*'], ['prom_cat_id'], ['prom_cat_id'=>'DESC']);
         
//        echo "<pre>";print_r($homePromotionTypeOne);die;
        $this->view->resultPromotionCategory = $resultPromotionCategory;
        $this->view->homePromotionTypeOne = $homePromotionTypeOne;
        $this->view->homePromotionTypeTwo = $homePromotionTypeTwoArr;
        $this->view->homePromotionTypeThree = $homePromotionTypeThree[0];
        $this->view->testimonials = $testimonials;
        $this->view->bannerDetail = $bannerDetail;
        $this->view->baseUrl = $this->baseUrl;
        $this->view->toursFinal = $toursFinal;
        $this->view->bolglist = $resultset;
        $this->view->lastMinuteTours = $lastMinuteToursFinal;
        $this->view->packageDestinationArray = $decodedPackageDestinationArray;
        $this->view->MobileDetect = $this->objHelperGeneral->getDevice();
        
        
        $obj = new Admin_Model_Admin();
        
        $getitems  = $obj->dashboardItems();
        
        $items = [];
              
//        echo "<pre>"; print_r($getitems); exit;

        foreach ($getitems as $key => $value) {
            $items[$value['itemname']] = $value['total'];

            // get value at once
            if($key==0) {
                $items['destination'] = $value['destination'];
            }
        }
//        echo "<pre>"; print_r($items); exit;
        
        $this->view->items = $items;
        
    }

    public function destinationAction() {
        $destinations = $this->objMdl->getDestinationsHeader(['tbl.IsActive' => 1, 'tbl.IsPublish' => 1, 'tbl.IsMarkForDel' => 0, 'tb2.IsMarkForDel' => 0], ['tbl.DesSysId' => 'ASC'], 100);
//        echo "<pre>";print_r($destinations);die;
        $destinationsAll = $this->objMdl->getDestinationsHeader(['tbl.IsActive' => 1, 'tbl.IsPublish' => 1, 'tbl.IsMarkForDel' => 0], ['tbl.DesSysId' => 'ASC'], 100);
      
        
        
        
        $region_names = $finalDestination = $finalDestinationAll = [];
        $regionnew = '';
        $region = trim($this->getRequest()->getParam('region'));
       
        if(isset($region) && $region != ''){
            
       
        foreach ($destinations as $key => $value) {
            if(isset($region) && !empty($region)){
                if($region == $value['region_label']){
            if (($value['region_label'] != NULL) && !in_array($value['region_label'], $region_names)) {
                $region_names[] = $value['region_label'];
                if($region == $value['region_label']){
                    $regionnew = $value['region_name'];
                }
            }

            $finalDestination[$value['region_label']][] = [
                'DesSysId' => $value['DesSysId'],
                'Title' => $value['Title'],
                'Image' => $value['Image'],
                'Tours' => $value['Tours'],
            ];
            }
            }
        }
//                echo "<pre>";print_r($finalDestination);die;
 }else{
    
     foreach ($destinationsAll as $allkey => $allvalue) {
          
            $finalDestinationAll[] = [
                'DesSysId' => $allvalue['DesSysId'],
                'Title' => $allvalue['Title'],
                'Image' => $allvalue['Image'],
                'Tours' => $allvalue['Tours'],
            ];
            }

 }
       $regionImage = $this->objMdl->getRegionImage($region);
      
        $this->view->baseUrl = $this->baseUrl;
        $this->view->finalDestinationAll = $finalDestinationAll;
        $this->view->finalDestination = $finalDestination;
        $this->view->region_names = $region_names;
//        $this->view->region = $region;
        $this->view->region = $regionnew;
    }

    public function saveLetterAction() {
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        if ($this->getRequest()->isPost()) {
            $param = $this->getRequest()->getParams();
            $date = new Zend_Date();
            $currentDate = $date->get('YYYY-MM-dd HH:mm:ss');
            $email = $param['email'];

            $newsletter = new Travel_Model_PackagesMaster();

            $savePageData = [
                'created_date' => $currentDate,
                'news_letter_email' => $email,
                'status' => 1,
            ];

            $resultset = $newsletter->checkLetter("tb_tbb2c_newsletter", ['*'], ['news_letter_email' => $savePageData['news_letter_email']]);

            if (isset($resultset) && !empty($resultset)) {
                $response = array('success' => false, 'msg' => 'This Email Id Already Exists!!!');
                echo json_encode($response);
                exit;
            } else {
                $returnId = $newsletter->sendNewsLetter('tb_tbb2c_newsletter', $savePageData);
                if ($returnId) {
                    $response = array('success' => true, 'msg' => 'Your query has been submitted successfully!');
                    echo json_encode($response);
                    exit;
                }
            }
        }
    }

    public function autosuggestdesAction() {
        try {
            $arrResponse = array();
            if ($this->getRequest()->getParam("term")) {
                $term = $this->getRequest()->getParam("term");
                $objDes = new Travel_Model_PackagesMaster();
                $arrResponse = $objDes->getDestinationAutoSuggest($term, $this->tablename);
            }
            //print_r($arrResponse);die('okkk');
            echo json_encode($arrResponse);
            exit;
        } catch (Exception $e) {
            $response = array('success' => false, 'msg' => $e->getMessage());
            echo json_encode($response);
            exit;
        }
    }

    public function autosuggestAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
        try {
            $arrResponse = array();
            if ($this->getRequest()->getParam("term") or $this->getRequest()->getParam("query")) {
                $term = $this->getRequest()->getParam("term") ? $this->getRequest()->getParam("term") : $this->getRequest()->getParam("query");
                $countryId = $this->getRequest()->getParam("countryId") ? $this->getRequest()->getParam("countryId") : '';

                $condCity = "tbl.Title like '" . $term . "%'";
                if (isset($countryId) && !empty($countryId)) {
                    $condCity .= " AND tbl.ContSysId = " . $countryId . "";
                }
                $arrResponse = $this->objMdl->getBuyHotelCityAutoSuggest($condCity);
            }
            echo json_encode($arrResponse);
            exit;
        } catch (Exception $e) {
            $response = array('success' => false, 'msg' => $e->getMessage());
            echo json_encode($response);
            exit;
        }
    }

    public function customerloginAction() {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        if ($this->getRequest()->isPost()) {
            $data = $request->getPost();
            $this->_session->session = $data;
//            echo $data['redirect_link_r'];die;
//            $this->_redirect($data['redirect_link_r']);
            $response = array('status'=>true,'msg'=>'success');
            echo json_encode($response);
            exit;
        }
    }

    public function logoutAction() {
        $storage = new Zend_Session_Namespace('User');
        $storage->unsetAll();
        $this->_redirect('/');
    }

    /*
     * writeSessionPopup is used to show the request a callback popup window after interval on landing page
     */

    public function writeSessionPopupAction() {
        $myNamespace = new Zend_Session_Namespace('MypopSess');
        $myNamespace->setPopup = true;
        $myNamespace->session_time = time();
        exit;
    }

}
