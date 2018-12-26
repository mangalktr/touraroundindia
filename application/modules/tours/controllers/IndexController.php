<?php

/* * ***********************************************************
 * Catabatic Technology Pvt. Ltd.
 * File Name     : IndexController.php
 * File Desc.    : Index controller for home page front end
 * Created By    : Piyush Tiwari <piyush@catpl.co.in>
 * Created Date  : 12 July 2018
 * Updated Date  : 19 July 2018

 * Tours_IndexController | Tours Module / Index controller
 * *************************************************************
 */

class Tours_IndexController extends Zend_Controller_Action {

    public $baseUrl = '';
    public $AgencyId;
    protected $objMdl;
    protected $objHelperGeneral;
    protected $tablename;

    public function init() {

        $aConfig = $this->getInvokeArg('bootstrap')->getOptions();
        $BootStrap = $aConfig['bootstrap'];
        $this->siteName = $BootStrap['siteName'];
        $this->baseUrl = $BootStrap['siteUrl'];
        $this->AgencyId = $BootStrap['gtxagencysysid'];

        $this->objMdl = new Admin_Model_CRUD();
        $this->tablename = "tb_tbb2c_packages_master";
        $this->objHelperGeneral = $this->_helper->General;
        $this->_sessioncallbackPop = new Zend_Session_Namespace('callbackPop');
    }

    public function indexAction() {
        // code here
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

    public function sendQueryDetailsAction() {

        $this->_helper->layout()->disableLayout('');
        $this->_helper->viewRenderer->setNoRender(true);
        if ($this->getRequest()->isPost()) {

            $param = $this->getRequest()->getParams();
            $contactDetail_mail = Zend_Controller_Action_HelperBroker::getStaticHelper('Custom')->getContactDetailForFooter();

//            echo "<pre>";print_r($param);die;
            //check if any of the inputs are empty
            if (empty($param['inputName']) || empty($param['inputEmail']) || empty($param['inputPhone'])) {
                $result = ['status' => false, 'message' => 'Please fill out the form completely.'];
            } else {

                $queryArr = array(
                    'AgencySysId' => $this->AgencyId,
                    'AgentSysId' => $this->AgentSysId,
                    'AgencyName' => '',
                    'Destination' => isset($param['Destination']) && !empty($param['Destination']) ? $param['Destination'] : '',
                    'DestinationID' => isset($param['cityid']) && !empty($param['cityid']) ? $param['cityid'] : '',
                    'PlanType' => isset($param['planType']) && !empty($param['planType']) ? $param['planType'] : '',
                    'Email' => $param['inputEmail'],
                    'MobileNumber' => $param['inputPhone'],
                    'FirstName' => $param['inputName'],
                    'LastName' => '',
                    'NoofTraveler' => '2',
                    'PKGCheckInDate' => date('m/d/Y'),
                    'Noofdays' => '2',
                    'remark' => ''
                );
//                echo '<pre>';print_r($queryArr);die;
                $this->postFields = "";
            $this->postFields .= "&AgencySysId=$this->AgencyId";
            $this->postFields .= "&FirstName=" . $param['inputName'];
            $this->postFields .= "&LastName=";
            $this->postFields .= "&message=" . $param['message'];
            $this->postFields .= "&Email=" . $param['inputEmail'];
            $this->postFields .= "&MobileNumber=" . $param['inputPhone'];
//            $resultset = $this->objMdl->rv_insert($this->tablename, $insertdata);
            
            
                try {
                $model = new Gtxwebservices_Model_Webservices();
                $getPackagesData = $model->sendLead($this->postFields);

                // echo "<pre>";print_r($getPackagesData);exit;
                $this->_sessioncallbackPop->sessionCallback = 1;
                $message = "Your query has been sent successfully.";
                $status = true;
            } catch (Zend_Exception $error) {
                $message = $this->view->error_msg = $error->getMessage();
                $status = false;
            }
            

                $MypopEnquiryCookie = [
                    'from_destination_id' => $param['cityid'],
                    'email' => $param['inputEmail'],
                    'mobile' => $param['inputPhone'],
                    'name' => $param['inputName']
                ];

                setcookie("MyCookies", json_encode($MypopEnquiryCookie), time()+(3600*24*2), "/");

                $result = ['status' => $status, 'message' => $message];
                
                
                $flname = explode(' ',$param['inputName']);
                $paramdata = [
                    'name' =>$param['inputName'],
                    'fname' => $flname[0],
                    'lname' => $flname[1],
                    'email' =>$param['inputEmail'],
                    'mobile' =>$param['inputPhone'],
                ];
                $params = array('param'=>$paramdata,'baseUrl'=>$this->baseUrl,'callusnumber'=>$this->callusnumber,'emailId'=>$this->contactEmail,'contactDetail_mail'=>$contactDetail_mail,'siteName' => $this->siteName);
                
            $cust_subject = 'Thanks for contacting us';
                $cust_html = new Zend_View();
                $cust_html->setScriptPath(APPLICATION_PATH . '/views/');
                $cust_html->assign($params);
                $cust_mailBody = $cust_html->render('customer_mail_master.phtml');

               $cust_configs = [
                'to' => trim($paramdata['email']),
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

}