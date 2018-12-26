<?php

/* * *************************************************************
 * Catabatic Technology Pvt. Ltd.
 * File Name     : IndexController.php
 * File Desc.    : Index controller for home page front end
 * Created By    : Piyush Tiwari <piyush@catpl.co.in>
 * Created Date  : 05 Jul 201
 * Updated Date  : 06 Jul 2018
 * ************************************************************* */

class Gtxwebservices_SendEnquiryController extends Zend_Rest_Controller {

    public $baseUrl = '';
    protected $gtxwebservicesURL;
    protected $gtxagencysysid;
    protected $gtxagentsysid;
    protected $CONST_PACKAGE_TRAVELER_MAX_ROOM;
    protected $CONST_PACKAGE_TRAVELER_MAX_ADULT_IN_ROOM;
    protected $CONST_PACKAGE_TRAVELER_MAX_CHILD_IN_ROOM;
    protected $CONST_PACKAGE_TRAVELER_MAX_INFANT_IN_ROOM;
    protected $objHelperGeneral;

    const USER_NAMESPACE = 'PSESS';

    public $_storage;
    public $myNamespace;
    public $packageSession;
    public $currenttimestamp;
    public $tollfreenumber;

    public function init() {

        $this->_helper->layout()->disableLayout();

        $aConfig = $this->getInvokeArg('bootstrap')->getOptions();
        $BootStrap = $aConfig['bootstrap'];

        $this->siteName = $BootStrap['siteName'];
        $this->baseUrl = $BootStrap['siteUrl'];
        $this->tollfreenumber = $BootStrap['tollfreenumber'];
        $this->adminEmail = $BootStrap['adminEmail'];

        $this->gtxwebservicesURL = $BootStrap['gtxwebserviceurl'] . "DynamicQuery"; // get gtxwebserviceurl from application config
        $this->gtxagencysysid = $BootStrap['gtxagencysysid']; // get gtxagencysysid from application config
        $this->gtxagentsysid = $BootStrap['gtxagentsysid']; // get gtxagentsysid from application config

        $this->CONST_PACKAGE_TRAVELER_MAX_ROOM = $BootStrap['CONST_PACKAGE_TRAVELER_MAX_ROOM']; // get variable from application config
        $this->CONST_PACKAGE_TRAVELER_MAX_ADULT_IN_ROOM = $BootStrap['CONST_PACKAGE_TRAVELER_MAX_ADULT_IN_ROOM']; // get variable from application config
        $this->CONST_PACKAGE_TRAVELER_MAX_CHILD_IN_ROOM = $BootStrap['CONST_PACKAGE_TRAVELER_MAX_CHILD_IN_ROOM']; // get variable from application config
        $this->CONST_PACKAGE_TRAVELER_MAX_INFANT_IN_ROOM = $BootStrap['CONST_PACKAGE_TRAVELER_MAX_INFANT_IN_ROOM']; // get variable from application config

        $this->objHelperGeneral = $this->_helper->General;

        $this->currenttimestamp = time();

        $this->_storage = new Zend_Session_Namespace(self::USER_NAMESPACE);

        $this->myNamespace = new Zend_Session_Namespace('MypopSess'); // get user end infomations
    }

    public function indexAction() {
        echo "API ;)";
//        $this->myNamespace->MypopSess = ['name' => 'ranvir singh'];
//        echo '<pre>'; print_r( $this->myNamespace->MypopSess );
//        echo '</pre>';
        die;
    }

    public function getAction() {
        //
    }

    /**
     * This function is used to submit enquiry 
     * @param mixed array
     * @return json
     * */
    public function GUID() {
        if (function_exists('com_create_guid') === true) {
            return trim(com_create_guid(), '{}');
        }
        return sprintf('%04X%04X%04X%04X%04X%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }

    public function postAction() {
        if ($this->getRequest()->isPost()) {

            $param = $this->getRequest()->getParams();
            $contactDetail_mail = Zend_Controller_Action_HelperBroker::getStaticHelper('Custom')->getContactDetailForFooter();

             if (!isset($_SESSION['TravelAgent']['session']) && empty($_SESSION['TravelAgent']['session']) && isset($param['check_TravelAgent']) &&  $param['check_TravelAgent'] == 1 ) {
                           $result_redirect = ['status' => false, 'redirect' => true];
                           echo Zend_Json::encode($result_redirect);
                           exit;
             } else {
  
            $email = isset($param['email']) ? $param['email'] : '';
            $mobile = isset($param['mobile']) ? $param['mobile'] : '';
            $date = isset($param['date']) ? $param['date'] : '';

            if (empty($email)) {
                $response = ['status' => 'error', 'message' => 'Please enter email address.'];
                $this->printMsg($response);
            } else if (empty($mobile)) {
                $response = ['status' => 'error', 'message' => 'Please enter mobile number.'];
                $this->printMsg($response);
            } else if (empty($date)) {
                $response = ['status' => 'error', 'message' => 'Please enter travel date.'];
                $this->printMsg($response);
            }

            $noOfAdultsar = array();
            $selectnoOfChildar = array();
            $countRooms = count($param['room']);
            $roominfojson = [];
            for ($i = 0, $k = 1; $k <= $countRooms; $k++, $i++) {
                $roominfojson[$k]['Adult'] = $param['adult'][$i];
                $roominfojson[$k]['Child'] = $param['child'][$i];
                $roominfojson[$k]['Infant'] = $param['infant'][$i];
                $noOfAdultsar[$k] = $param['adult'][$i];

                if ($param['adult'][$i] == 3) {
                    $roominfojson[$k]['bedtype'] = $param['adult_bed_type'][$i];
                }
                if ($param['child'][$i] > 0) {
                    for ($c = 1; $c <= $param['child'][$i]; $c++) {
                        $roominfojson[$k]['ChildBedType_' . $c] = $param['child' . $c . '_bed_type'][$i];
                        $roominfojson[$k]['ChildAge_' . $c] = '';
                        $selectnoOfChildar[$k] = $param['child'][$i];
                    }
                }
            }
            $noOfAdults = array_sum($noOfAdultsar);
            $selectnoOfChild = array_sum($selectnoOfChildar);
            $numberofpax = ($noOfAdults + $selectnoOfChild);
            $package_sid = $param['package_sid'];
            $from_destination = $param['from_destination'];
            $from_destination_id = $param['from_destination_id'];
            $name = $param['name'];
            $packagetype_id = $param['packagetype_id'];
            $tourtype = $param['package_tourtype'];
            $package_sid = $param['packagesys_id'];
            $package_tpid = $param['package_tpid'];
            $package_hotelcategoryid = $param['package_hotelcategoryid'];
            $package_mealplantype = (isset($param['package_mealplantype'])) ? $param['package_mealplantype'] : '';
            $packagedesname = (isset($param['packagedesname'])) ? $param['packagedesname'] : '';
            $isFixedDeparture = (isset($param['isFixedDeparture'])) ? $param['isFixedDeparture'] : 0;

            // start : get package details
            if ($package_sid) {
                $getData['pkgid'] = $package_sid;
                $getData['package_hotelcategoryid'] = $package_hotelcategoryid;
                $getData['package_mealplantype'] = $package_mealplantype;

                try {
                    $curl = curl_init($this->baseUrl . "gtxwebservices/package-details/get"); // b2c site url
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($getData));
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

                    $service_response = curl_exec($curl);
                    curl_close($curl);

                    $resultArray = Zend_Json::decode($service_response); // get result array format
                    if ($resultArray['status'] == 1) {
                        $statusPackageDetails = true;
                    }
                } catch (Exception $error) {
                    $statusPackageDetails = false;
                    $message = $error->getMessage();
                }
            }
            // end : get package details                        
            // static data
            $DestinationIDTemp = explode(',', $resultArray['DestinationID']); // static
            $DestinationsTemp = explode(',', $resultArray['Destinations']);
            $DestinationID = $DestinationIDTemp[0]; // static
            $Destinations = $packagedesname;
            $PriceRange = 0;
            $MinPrice = 0;
            $MaxPrice = 0;
            $pkgCheckinDate = $date;
            // start : calculate checkout date here
            $tempDate = explode('/', $pkgCheckinDate);
            $tempCheckoutDate = $tempDate[2] . '/' . $tempDate[1] . '/' . $tempDate[0];
            $pkgCheckoutDate = $this->objHelperGeneral->dateAddDays($tempCheckoutDate, $resultArray['Nights']);
            // end : calculate checkout date here                                     
            // destinations calculations
            $DestinationIDTemp = explode(",", (($DestinationID) ? $DestinationID : 0));
            $DestinationsTemp = explode(",", (($Destinations) ? $Destinations : ''));
            $DestinationsMerged = array_combine($DestinationIDTemp, $DestinationsTemp);
            $DestinationPlaces = base64_encode(json_encode($DestinationsMerged));


            /*
             * write cookie here for first time | and use values for next enquiries respectively
             */
            if (isset($_SESSION['TravelAgent']['session']) && !empty($_SESSION['TravelAgent']['session'])) {
                
            } else {
                $MypopC = [
                    'name' => $param['name'],
                    'email' => $param['email'],
                    'mobile' => $param['mobile'],
                    'from_destination_id' => $param['from_destination_id'],
                    'from_destination' => $param['from_destination'],
                    'from_destination_name' => $param['from_destination_name'],
                    'date' => $param['date'],
                    'room' => isset($param['room']) && !empty($param['room']) ? count($param['room']) : '',
                    'adult' => isset($param['adult']) && !empty($param['adult']) ? implode(",", $param['adult']) : '',
                    'adult_bed_type' => isset($param['adult_bed_type']) && !empty($param['adult_bed_type']) ? implode(",", $param['adult_bed_type']) : '',
                    'child' => isset($param['child']) && !empty($param['child']) ? implode(",", $param['child']) : '',
                    'child1_bed_type' => isset($param['child1_bed_type']) && !empty($param['child1_bed_type']) ? implode(",", $param['child1_bed_type']) : '',
                    'child2_bed_type' => isset($param['child2_bed_type']) && !empty($param['child2_bed_type']) ? implode(",", $param['child2_bed_type']) : '',
                    'infant' => isset($param['infant']) && !empty($param['infant']) ? implode(",", $param['infant']) : '',
                ];
//            echo "<pre>";print_r($MypopC);die;
                setcookie("MyCookies", json_encode($MypopC), time() + (3600 * 24 * 2), "/");
            }
            // $packagetype_id : 1 is for readymade package
            if ($packagetype_id == 1) {

                $this->postFields = "";
                $this->postFields .= "&AgencySysId=" . $this->gtxagencysysid;
                $this->postFields .= "&TravelPlanId=" . $package_tpid;
                $this->postFields .= "&FirstName=" . $name;
                $this->postFields .= "&LastName=";
                $this->postFields .= "&Email=" . $email;
                $this->postFields .= "&MobileNumber=" . $mobile;
                $this->postFields .= "&PriceRange=" . $PriceRange; // 2000-50000
                $this->postFields .= "&PKGCheckInDate=" . $pkgCheckinDate; // 12/08/2017
                $this->postFields .= "&PKGCheckOutDate=" . $pkgCheckinDate; // 19/08/2017
                $this->postFields .= "&MinPrice=" . $MinPrice; // 2000
                $this->postFields .= "&MaxPrice=" . $MaxPrice; // 50000
                $this->postFields .= "&DestinationID=" . $DestinationID; // destination id
                $this->postFields .= "&Destination=" . $Destinations; // destination value
                $this->postFields .= "&IsFixedDeparturePackage=" . $isFixedDeparture; 
                $this->postFields .= "&roomjson=" . Zend_Json::encode($roominfojson); // destination value
                if (isset($_SESSION['TravelAgent']['session']) && !empty($_SESSION['TravelAgent']['session'])) {
                    $this->postFields .= "&aId=" . $_SESSION['TravelAgent']['session']['UserSysId'];
                    $this->postFields .= "&selectedCustomerId=" . base64_encode($_SESSION['TravelAgent']['session']['CustomerSysId']);
                }
//                echo '<pre>';print_r($this->postFields);die('ddd');
                parse_str($this->postFields, $get_array_params);
                $apiData = $get_array_params;

                // log the request
                try {
                    $model = new Gtxwebservices_Model_Webservices();
                    
                    if (isset($_SESSION['TravelAgent']['session']) && !empty($_SESSION['TravelAgent']['session'])) {
                        $getPackagesData = $model->sendQueryB2B($this->postFields);
                    } else {
                        if($isFixedDeparture == 1){
                           $getPackagesData = $model->sendQueryProposal($this->postFields);
                    }else{
                        $getPackagesData = $model->sendQuery($this->postFields);
                    }
                    }
                    
                    // log the response 
                    $jsonDataArray = Zend_Json::decode($getPackagesData, true);
                    $status = $jsonDataArray['status']; // gives success if submitted successfully
                    $message = "Your query has been sent successfully.";
                    $message_err = ( $status != 'success') ? $status : false;
                } catch (Zend_Exception $error) {
                    echo $message_err = $error->getMessage();
                    $status = false;
                }

                $status = ($status == 'success') ? $status : false;
                $message = ($status == 'success') ? $message : $message_err;
    if($isFixedDeparture == 1){
        $result_submit = ['status' => $status, 'url' => $jsonDataArray['url'],'ProposalId' => $jsonDataArray['ProposalId'], 'message' => $message, 'availability' => @$jsonDataArray['availability'], 'addtional' => @$jsonDataArray['addtional']];
    }else{
        $result_submit = ['status' => $status, 'message' => $message, 'ProposalId' => '', 'availability' => @$jsonDataArray['availability'], 'addtional' => @$jsonDataArray['addtional']];
 
    }
               

//                echo "<pre>";print_r($result_submit);die;
            }
            // for dynamic query 
            else if ($packagetype_id == 2) {
                // for dynamic package code below
                $apiData = [];

                $apiData['packid'] = $package_sid;
                $apiData['TravelPlanId'] = $package_tpid;
                $apiData['minpax'] = $resultArray['MinPax'];
                $apiData['catID'] = $package_hotelcategoryid;
                $apiData['FirstName'] = $name;
                $apiData['TotalNights'] = $resultArray['Nights'];
                $apiData['DestinationPlaces'] = $DestinationPlaces; // 'eyI3NzAxIjoiRGVsaGkiLCIxMDU3NiI6IkdvYSJ9';
                $apiData['search_going_to'] = $from_destination_id . '__' . $from_destination;   // '7701__Delhi';
                $apiData['pkgCheckInDate'] = $pkgCheckinDate;
                $apiData['pkgCheckOutDate'] = $pkgCheckoutDate;
                $apiData['tripstartdate'] = $date;
                $apiData['custemail'] = $email;
                $apiData['custname'] = $name;
                $apiData['custphone'] = $mobile;
                $apiData['tripaddress'] = str_replace(',', ';', $resultArray['Destinations']);
                $apiData['aboutpackage'] = '';
                $apiData['pricetype'] = 'wp';
                $apiData['AgencySysId'] = $this->gtxagencysysid;
                $apiData['Email'] = $email;
                $apiData['MobileNumber'] = $mobile;
                $apiData['leadsource'] = 'Website';
                $apiData['mealplantype'] = $package_mealplantype;
                if (isset($_SESSION['TravelAgent']['session']) && !empty($_SESSION['TravelAgent']['session'])) {
                $apiData['selectedCustomerId']  = base64_encode($_SESSION['TravelAgent']['session']['CustomerSysId']);
                $apiData['aId']  = $_SESSION['TravelAgent']['session']['UserSysId'];
                $apiData['IsB2BCustomer']  = 1;
                $apiData['IsB2BAgent']  = 1;
                }

                for ($k = 1; $k <= count($roominfojson); $k++) {
                    $roominfojson[$k]['departuredate'] = $pkgCheckinDate;
                    $roominfojson[$k]['returndate'] = $pkgCheckoutDate;
                }


                $apiData['roomjson'] = Zend_Json::encode($roominfojson);
                $apiData['ItineraryArray'] = $resultArray['itineraryArr'];

                $package_modified = $this->_storage->packageSession[$package_sid][$tourtype][$apiData['catID']]['others']['modified'];

                // only true if the query form submitted from detail page
                if ($package_modified) {
                    $apiData['ItineraryArray'] = $this->objHelperGeneral->prepareItineraryArrayForSendingQuery($this->_storage->packageSession[$package_sid][$tourtype][$apiData['catID']]); // assign value to itinerary array
//                    $apiData['ItineraryArray'] = $this->_storage->packageSession[$package_sid][$tourtype][$apiData['catID']];
                } else {
//                    $apiData['ItineraryArray'] = $this->_storage->packageSession[$package_sid][$tourtype][$apiData['catID']];
                }

                // log the request 

                try {
                    $curl = curl_init($this->gtxwebservicesURL);
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($apiData));
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

                    $getPackagesData = curl_exec($curl);

                    // log the response 

                    $jsonDataArray = Zend_Json::decode($getPackagesData, true);
                    curl_close($curl);

                    $status = $jsonDataArray['status']; // gives success if submitted successfully
                    $message = "Your query has been sent successfully.";
                    $message_err = ( $status != 'success') ? $status : false;
                } catch (Zend_Exception $error) {
                    echo $message_err = $error->getMessage();
                    $status = false;
                }

                $status = ($status == 'success') ? $status : false;
                $message = ($status == 'success') ? $message : $message_err;

                $result_submit = ['status' => $status, 'message' => $message, 'availability' => $jsonDataArray['availability'], 'addtional' => $jsonDataArray['addtional']];
            }
            /*
             * send mail here to the customer
             * 
             * Params : config for to email, cc email, subject, body | type (package/hotel/activity)
             */
            
            $subject = ucfirst($name) . " Your travel enquiry for " . $Destinations . " [$package_tpid]";
            $mailBody = '<table width="60%" border="0" align="center" cellpadding="0" cellspacing="0" style=" font-size:14px; font-family: Roboto, sans-serif;">
  
  <tr>
    <td colspan="2" style=" padding:5px 40px;vertical-align: middle;">Dear ' . ucfirst($name) . ', <br>
      <br>
      Greetings from '.$this->siteName.'!  <br> <br>
      Thank you for your query! We have received your travel query for ' . $Destinations . ' as below:</td>
  </tr>
  <tr>
    <td width="28%" style=" padding:5px 40px;vertical-align: middle;">Destination : </td>
    <td width="72%" style=" padding:5px 40px;vertical-align: middle;">' . $Destinations . '</td>
  </tr>
  <tr>
    <td style=" padding:5px 40px;vertical-align: middle;">No of Pax : </td>
    <td style=" padding:5px 40px;vertical-align: middle;">' . $numberofpax . '</td>
  </tr>
  <tr>
    <td style=" padding:5px 40px;vertical-align: middle;">Travel Dates : </td>
    <td style=" padding:5px 40px;vertical-align: middle;">' . $date . '</td>
  </tr>
  <tr>
    <td style=" padding:5px 40px;vertical-align: middle;">Query type : </td>
    <td style=" padding:5px 40px;vertical-align: middle;">Package <br></td>
  </tr>
  <tr>
    <td colspan="2" style=" padding:5px 40px;vertical-align: middle;"> We will call you as quickly as possible with best options for you. If you require immediate assistance, please call us on ' .$contactDetail_mail['mobile'] . '.<br><br>
      Your opinion matters to us! We\'d love to hear what you have to say about our services!<br>
      Write in to us at email id '.$contactDetail_mail['email'].'<br><br></td>
  </tr>
  <tr>
    <td colspan="2" style=" padding:5px 40px;vertical-align: middle;">Thanks ! <br>
      <hr / style="border-width: 0.6px;">
      <br>
      ' . $this->baseUrl . '<br>
      contact number: ' .$contactDetail_mail['mobile'] . '<br>
      Email:'.$contactDetail_mail['email'].'</td>
  </tr>
</table>
<div style="text-align:center;">
     <img style="width:100px;" src="' . $this->baseUrl . 'public/images/logo.png" />
  </div>
<table width="60%" border="0" align="center" cellpadding="0" cellspacing="0" >
<tr>
    <td style=" text-align:right;">Powered by &#45; <a  href="http://www.hellogtx.com/" target="_blank" style="font-family:AvantGarde Bk BT;color:#0b74c4; text-decoration:none">hello<span style="font-family:AvantGarde Bk BT;color:#fb5a2d;">GTX</span></a></td>
  </tr>
</table>
';


            $configs = [
                'to' => $email,
                'fromName' => $this->siteName,
                'fromEmail' => $contactDetail_mail['email'],
                'subject' => $subject,
                'bodyHtml' => $mailBody,
            ];

            $custMail = $this->objHelperGeneral->mailSentByElastice($configs, 'package');
            
            
            $paramdata = [
                'name' => $name,
                'email' => $param['email'],
                'mobile' => $param['mobile'],
                'destination' =>$Destinations,
                'travel_from' =>$from_destination,
                'travel_date' =>$date,
                'noofpax' =>$numberofpax,
            ];
            $params = array('param'=>$paramdata,'baseUrl'=>$this->baseUrl,'callusnumber'=>$this->callusnumber,'emailId'=>$this->contactEmail,'contactDetail_mail'=>$contactDetail_mail,'siteName'=>$this->siteName);
                
             $admin_subject = 'Travel Enquiry for '.($Destinations);
                $admin_html = new Zend_View();
                $admin_html->setScriptPath(APPLICATION_PATH . '/views/');
                $admin_html->assign($params);
                $admin_mailBody = $admin_html->render('admin_mail_master.phtml');

               $admin_configs = [
                'to' => $contactDetail_mail['email'],
                'fromName' => $this->siteName,
                'fromEmail' => $contactDetail_mail['email'],
                'subject' => $admin_subject,
                'bodyHtml' => $admin_mailBody,
            ];
            
            $admin_Mail = $this->objHelperGeneral->mailSentByElastice( $admin_configs , 'Package' );
            // send the json finally
            echo Zend_Json::encode($result_submit);
            exit;
             } 
        } else {
            die('Something went wrong.');
        }
    }

    public function putAction() {
        // action body
    }

    public function deleteAction() {
        // action body
    }

    public function printMsg($response) {
        echo $xmlData = Zend_Json::encode($response);
        exit;
    }

    public function thanksAction() {
        $this->_helper->layout()->disableLayout();
    }

}
