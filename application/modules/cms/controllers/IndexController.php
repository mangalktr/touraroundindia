<?php

class Cms_IndexController extends Zend_Controller_Action {

    public $baseUrl = '';
    protected $objMdl;
    public $smtpUserName;
    public $smtpPassword;
    public $smtpPort;
    public $smtpHost;
    public $AgencyId;
    public $tablenamePage;

    public function init() {
        $this->objMdl = new Admin_Model_CRUD();
        $object = Zend_Controller_Front::getInstance();
        $this->action = $action = $object->getRequest()->getActionName();
        $this->modulename = $modulename = $object->getRequest()->getModuleName();

        $aConfig = $this->getInvokeArg('bootstrap')->getOptions();
        $BootStrap = $aConfig['bootstrap'];

        $this->siteName = $BootStrap['siteName'];
        $this->baseUrl = $BootStrap['siteUrl'];
        $this->gtxBtoBsite = $BootStrap['gtxBtoBsite'];
        $this->AgencyId = $BootStrap['gtxagencysysid'];
        $this->AgentSysId = $BootStrap['gtxagentsysid'];
        $this->LeadURL = $BootStrap['siteUrl'] . 'index/query';
        $this->tablename = 'tbl_enquiry';
        $this->tablenamePage = 'tbl_static_pages';
         $this->adminEmail = $BootStrap['adminEmail'];
          $this->objHelperGeneral = $this->_helper->General;
    }

    public function indexAction() {
        $param = $this->getRequest()->getParams();
        //echo "<pre>";print_r($param);exit;
        $pageid = explode(".", $param['id']);
//        print_r($pageid);die;
        $model = new Admin_Model_CRUD();
        $getPageDetail = $model->rv_select_row($this->tablenamePage, ['*'], ['identifier' => $pageid[0], 'status' => 'Activate'], ['sid' => 'desc']);
        $getAboutUsDetailForContactUs = $model->rv_select_row($this->tablenamePage, ['page_description'], ['identifier' => 'about-us', 'status' => 'Activate'], ['sid' => 'desc']);

        /* SEO KEYWORD */
        $resultsetSeoForCmsPages = array();
        $resultsetSeoForCmsPages['Keyword'] = $getPageDetail['meta_keywords']; // get Keyword
        $resultsetSeoForCmsPages['Description'] = $getPageDetail['meta_description']; // get Description
        $resultsetSeoForCmsPages['Title'] = $getPageDetail['meta_title']; // get name

        
        $getMypopCookie = Zend_Controller_Action_HelperBroker::getStaticHelper('General')->getMypopCookie('MyCookies'); // get the popup sessions
        $this->view->getMypopCookie = $getMypopCookie;
        $this->view->resultsetSeoForCmsPages = $resultsetSeoForCmsPages;
        $this->view->pagedetail = $getPageDetail;
        $this->view->getAboutUsDetailForContactUs = $getAboutUsDetailForContactUs;
        $this->view->baseUrl = $this->baseUrl;
        $this->view->siteName = $this->siteName;
    }

    public function sendenquiryAction() {
        if ($this->getRequest()->isPost()) {
            $param = $this->getRequest()->getParams();
            $contactDetail_mail = Zend_Controller_Action_HelperBroker::getStaticHelper('Custom')->getContactDetailForFooter();

            if (isset($param['captcha']) && strtolower($param['captcha']) != $_SESSION['captcha_contact']) {
                    $result = ['status' => 'captcha', 'message' => 'Captcha code invalid.'];
                    echo Zend_Json::encode($result);
                    exit;
            }else{
                
            $insertdata = [
                'name' => $param['name'],
                'email' => trim($param['email']),
                'phone' => trim($param['mobile']),
                'message' => $param['message'],
                'created_at' => date('Y-m-d h:i:s'),
            ];
            $this->postFields = "";
            $this->postFields .= "&AgencySysId=$this->AgencyId";
            $this->postFields .= "&FirstName=" . $param['name'];
            $this->postFields .= "&LastName=";
            $this->postFields .= "&message=" . $param['message'];
            $this->postFields .= "&Email=" . $param['email'];
            $this->postFields .= "&MobileNumber=" . $param['mobile'];
//            $resultset = $this->objMdl->rv_insert($this->tablename, $insertdata);
            
            if(isset($param['form_type']) && (trim($param['form_type']) == 'contact_us') ){
                $message = "Your query has been sent successfully.";
                $status = true;
            }else{
                try {
                $model = new Gtxwebservices_Model_Webservices();
                $getPackagesData = $model->sendLead($this->postFields);

                // echo "<pre>";print_r($getPackagesData);exit;

                $message = "Your query has been sent successfully.";
                $status = true;
            } catch (Zend_Exception $error) {
                $message = $this->view->error_msg = $error->getMessage();
                $status = false;
            }
            }
            

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

            setcookie("MyCookies", json_encode($MypopC), time() + (3600 * 24 * 2), "/");

            $result = ['status' => true, 'message' => $message, 'name' => $param['name']];
            

            $params = array('param'=>$param,'baseUrl'=>$this->baseUrl,'callusnumber'=>$this->callusnumber,'emailId'=>$this->contactEmail,'contactDetail_mail'=>$contactDetail_mail,'siteName'=>$this->siteName);
               if(isset($param['packagedesname']) && $param['packagedesname']!=''){
                    $cust_subject = 'Travel Enquiry for '.$param['packagedesname'];
               }else{
                    $cust_subject = 'Thanks for contacting us';
               } 
           
                $cust_html = new Zend_View();
                $cust_html->setScriptPath(APPLICATION_PATH . '/views/');
                $cust_html->assign($params);
                $cust_mailBody = $cust_html->render('customer_mail_master.phtml');

               $cust_configs = [
                'to' => trim($param['email']),
                'fromName' => $this->siteName,
                'fromEmail' => $contactDetail_mail['email'],
                'subject' => $cust_subject,
                'bodyHtml' => $cust_mailBody,
            ];
            
            $cust_Mail = $this->objHelperGeneral->mailSentByElastice( $cust_configs , 'Package' );
            
             if(isset($param['packagedesname']) && $param['packagedesname']!=''){
                    $admin_subject = 'Travel Enquiry for '.$param['packagedesname'];
               }else{
                    $admin_subject = 'New Request for Contact us';
               } 
            
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
    
            echo Zend_Json::encode($result);
            exit;
            }
        } else {
            $result = ['status' => false, 'message' => 'Unable to send enquiry'];
            echo Zend_Json::encode($result);
            exit;
        }
    }

    public function sendQueryDetailsAction() {

        $this->_helper->layout()->disableLayout('');
        $this->_helper->viewRenderer->setNoRender(true);
        if ($this->getRequest()->isPost()) {

            $param = $this->getRequest()->getParams();
             $contactDetail_mail = Zend_Controller_Action_HelperBroker::getStaticHelper('Custom')->getContactDetailForFooter();

//            echo "<pre>";print_r($param);die;
            //check if any of the inputs are empty
            if (empty($param['name']) || empty($param['email']) || empty($param['mobile'])) {
                $result = ['status' => false, 'message' => 'Please fill out the form completely.'];
            } else {

                $queryArr = array(
                    'AgencySysId' => $this->AgencyId,
                    'AgentSysId' => $this->AgentSysId,
                    'AgencyName' => '',
                    'Destination' => isset($param['Destination']) && !empty($param['Destination']) ? $param['Destination'] : '',
                    'DestinationID' => isset($param['DestinationID']) && !empty($param['DestinationID']) ? $param['DestinationID'] : '',
                    'PlanType' => isset($param['planType']) && !empty($param['planType']) ? $param['planType'] : '',
                    'Email' => $param['email'],
                    'MobileNumber' => $param['mobile'],
                    'FirstName' => $param['name'],
                    'LastName' => '',
                    'NoofTraveler' => '2',
                    'PKGCheckInDate' => date('m/d/Y'),
                    'Noofdays' => '2',
                    'remark' => ''
                );

                try {

                    $curl = curl_init($this->gtxBtoBsite . "gtxwebservices/b2b-query"); // b2c demo site url
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($queryArr));
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

                    $response = curl_exec($curl);

                    $message = "Your query has been sent successfully.";
                    $status = true;
                    curl_close($curl);
                } catch (Exception $ex) {
                    $message = $this->view->error_msg = $ex->getMessage();
                    $status = false;
                }
                $flname = explode(' ',$param['name']);
                $MypopC = [
                    'name' => $param['name'],
                    'fname' => $flname[0],
                    'lname' => $flname[1],
                    'email' => $param['email'],
                    'mobile' => $param['mobile'],
                ];

                setcookie("MyCookies", json_encode($MypopC), time() + (3600 * 24 * 2), "/");

                $result = ['status' => $status, 'message' => $message];
                
                $params = array('param'=>$param,'baseUrl'=>$this->baseUrl,'callusnumber'=>$this->callusnumber,'emailId'=>$this->contactEmail,'contactDetail_mail'=>$contactDetail_mail,'siteName'=>$this->siteName);
             
                $cust_subject = 'Thanks for contacting us';
                $cust_html = new Zend_View();
                $cust_html->setScriptPath(APPLICATION_PATH . '/views/');
                $cust_html->assign($params);
                $cust_mailBody = $cust_html->render('customer_mail_master.phtml');

               $cust_configs = [
                'to' => trim($param['email']),
                'fromName' => $this->siteName,
                'fromEmail' => $contactDetail_mail['email'],
                'subject' => $cust_subject,
                'bodyHtml' => $cust_mailBody,
            ];
            
            $cust_Mail = $this->objHelperGeneral->mailSentByElastice( $cust_configs , 'Package' );
            
            
            $admin_subject = 'New Request for Callback';
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
            }
            
            
            
            
            
            
            
            
        } else {

            $result = ['status' => false, 'message' => 'Invalid Request!'];
        }

        
        
        
        
        
        echo Zend_Json::encode($result);
        exit;
    }

    public function aboutDetailsAction() {

//         $aboutResult = $this->objMdl->getCmsdata($this->tableabout, ['*'], ['AboutId'=>$pId], ['AboutId'=>'DESC']);
        $aboutResult = $this->objMdl->rv_select_all($this->tableabout, ['*'], [ 'status' => 1, 'isMarkForDel' => 0], ['AboutId' => 'DESC'], 4);
        $this->view->aboutResult = $aboutResult;



        $this->view->baseUrl = $this->baseUrl;
        $this->view->CONST_YEAR_NAME = $this->CONST_YEAR_NAME;
    }

    public function getHotelDetailAction() {

        $this->_helper->layout()->disableLayout();
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {
                $param = $this->getRequest()->getParams();
                $hotelId = $param['hotelId'];
                $type = $param['type'];
                $categoryId = $param['categoryId'];
                $packageId = $param['packageId'];
                $gtxID = $param['gtxID'];
                $model = new Detail_Model_PackageMapper();
                if ($type == 'H') {
                    $getDetail = $model->fetchHotelDetails($categoryId, $gtxID, $packageId, $hotelId);
                } else if ($type == 'A') {
                    $getDetail = $model->fetchActivityDetails($categoryId, $gtxID, $packageId, $hotelId);
                } else {
                    $getDetail = $model->fetchSightSeeingDetails($categoryId, $gtxID, $packageId, $hotelId);
                }
                $this->view->type = $type;
                $this->view->hotelData = $getDetail;
                $this->view->baseUrl = $this->baseUrl;
            }
        }
    }

}
