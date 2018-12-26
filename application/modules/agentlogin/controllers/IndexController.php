<?php

/* * *************************************************************
 * Catabatic Technology Pvt. Ltd.
 * File Name     : IndexController.php
 * File Desc.    : Index controller for home page front end
 * Created By    : Piyush Tiwari <piyush@catpl.co.in>
 * Created Date  : 27 June 2018
 * Updated Date  : ------------
 * ************************************************************* */

class Agentlogin_IndexController extends Catabatic_CheckSession {

    protected $objMdl;
    protected $baseUrl;
    protected $objHelperGeneral;
    public $_session;
    public $customerbookinglistAPIUrl;
    public $myNamespace;

    public function init() {
        parent::init();
        $aConfig = $this->getInvokeArg('bootstrap')->getOptions();
        $BootStrap = $aConfig['bootstrap'];
        $this->baseUrl = $BootStrap['siteUrl'];
         $this->siteName = $BootStrap['siteName'];
        $this->objMdl = new Admin_Model_CRUD();
        $this->objHelperGeneral = $this->_helper->General;
        $this->_session = new Zend_Session_Namespace('TravelAgent');
        $this->gtxagencysysid = $BootStrap['gtxagencysysid']; // get gtxagencysysid from application config
        $this->agentauthlogin = API_AGENT_B2B_AUTH_LOGIN; // from constant file
        $this->contactEmail = $BootStrap['contactEmail'];
        $this->agentforgotpasswordAPIUrl = API_AGENT_FORGOTPASSWORD; // from constant file
    }

    public function indexAction() {
        $this->checklogin();
    }

    public function agentcustomerloginAction() {
        header('Access-Control-Allow-Origin: *');
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getParams();

            $apiData = array(
                'userName' => $data['username'],
                'userPassword' => $data['pass'],
                'AgencySysId' => $this->gtxagencysysid
            );

            try {
                $curl_p = curl_init($this->agentauthlogin);
                curl_setopt($curl_p, CURLOPT_POST, true);
                curl_setopt($curl_p, CURLOPT_POSTFIELDS, http_build_query($apiData));
                curl_setopt($curl_p, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl_p, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl_p, CURLOPT_TIMEOUT, 300);
                $response = curl_exec($curl_p);
                curl_close($curl_p);
            } catch (Exception $error) {
                $this->view->error_msg = $error->getMessage();
                die;
            }
            $response_decode = Zend_Json::decode($response, true);

            if ($response_decode == 1) {
                $reply = ['status' => false, 'message' => 'Invalid login credentials'];
                echo Zend_Json::encode($reply);
                exit;
            } elseif ($response_decode == 2) {
                $reply = ['status' => false, 'message' => 'Your account is inactive. Please Contact to the administrator.'];
                echo Zend_Json::encode($reply);
                exit;
            } else {
                echo Zend_Json::encode($response_decode);
                exit;
            }
        }
    }

    public function forgotpasswordAction() {
        if ($this->getRequest()->isPost()) {
            $param = $this->getRequest()->getParams();
            $contactDetail_mail = Zend_Controller_Action_HelperBroker::getStaticHelper('Custom')->getContactDetailForFooter();

            $apiData = array(
                "forget" => $param['forget'],
                "AgencySysId" => $this->gtxagencysysid
            );
//            echo "<pre>";print_r($apiData);die;
            //  die($this->agentforgotpasswordAPIUrl);die;
            try {
                $curl = curl_init($this->agentforgotpasswordAPIUrl);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($apiData));
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                $response = curl_exec($curl);
                // echo '<pre>';print_r($response);die;
                curl_close($curl);
            } catch (Exception $error) {
                $this->view->error_msg = $error->getMessage();
                die;
            }
            $ResponseDecode = Zend_Json::decode($response, true);

            if ($ResponseDecode['status'] == 1) {
                $datetime = date('d-m-y h:i:s');
                $Password = $ResponseDecode['password'];
                $EmailId = $ResponseDecode['EmailId'];
                $FirstName = $ResponseDecode['FirstName'];

                $name = $FirstName;

                //$password = '1254';
                $from_email = $contactDetail_mail['email'];

                $subject = "Password Change Request";
                $message = "Hello, $name<br><br>";
                $message .= "Greetings from $this->siteName team.<br><br>";
                $message .= "Email Id -   $EmailId <br><br>";
                $message .= "Password -  $Password<br><br>";

                $message .= "Thank you<br><br>";
                $message .= "Team $this->siteName .";

                $configs = [
                    'to' => $EmailId,
                    'fromName' =>  $this->siteName,
                    'fromEmail' => $from_email,
                    'subject' => $subject,
                    'bodyHtml' => $message,
                ];

                $returnMail = $this->_helper->General->mailSentByElastice($configs, 'Forgot');

                $reply = ['status' => true, 'message' => 'Email has been sent successfully.'];
                echo Zend_Json::encode($reply);
                exit;
            } else {
                $reply = ['status' => false, 'message' => 'Invalid email. Please try again.'];
                echo Zend_Json::encode($reply);
                exit;
            }
            //print_r($param);die;
        } else {
            echo 'Oops wrong request';
            exit;
        }
    }

    public function agentloginAction() {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        if ($this->getRequest()->isPost()) {
            $data = $request->getPost();
            $this->_session->session = $data;
            $this->_redirect('/');
        }
    }

    public function logoutAction() {
        $storage = new Zend_Session_Namespace('TravelAgent');
        $storage->unsetAll();
        $this->_redirect('/');
    }

    public function checklogin() {
        /*         * ************* check admin identity *********** */
        if ($_SESSION['TravelAgent']['session']) {
            $this->_redirect('/');
        }
    }

}
