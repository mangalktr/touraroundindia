<?php
/***************************************************************
* Catabatic Technology Pvt. Ltd.
* File Name     : CustomerController.php
* File Desc.    : Customer controller for home page front end
* Created By    : Piyush Tiwari <piyush@catpl.co.in>
* Created Date  : 05 Sep 2018
* Updated Date  : 05 Sep 2018
***************************************************************/



class CustomerController extends Zend_Controller_Action
{

    protected $objMdl;
    protected $tablename;

    protected $objHelperGeneral;
    protected $per_page_record;
    protected $_session;
    protected $_sessionSocial;
    protected $_sessionSocialFB;
    public $customerbookinglistAPIUrl;
    protected $objHelperLoginwithGoogle;
//    protected $objHelperLoginwithFacebook;

    public $contactEmail;



    public function init() {
        
        $aConfig = $this->getInvokeArg('bootstrap')->getOptions();
        $BootStrap  = $aConfig['bootstrap'];
        
        $this->siteName = $BootStrap['siteName'];
        $this->baseUrl  = $BootStrap['siteUrl'];
        $this->gtxbaseUrl   = $BootStrap['gtxBtoBsite'];
        $this->contactEmail = $BootStrap['contactEmail'];
        $this->gtxagencysysid       = $BootStrap['gtxagencysysid']; // get gtxagencysysid from application config
        $this->gtxagentsysid       = $BootStrap['gtxagentsysid']; // get gtxagentsysid from application config        
        $this->objMdl   = new Admin_Model_CRUD();
        $this->_user = new Zend_Session_Namespace('User');
        $this->_sessionSocial = new Zend_Session_Namespace('SocialGoogle');
        $this->_sessionSocialFB = new Zend_Session_Namespace('SocialFacebook');
        $this->tablename    = "TB_TBB2C_Packages_Master";
        $this->tablenameTes = "tbl_testimonials";
        $this->hotelTypeArr = ['Standard','Deluxe','Luxury'];       
        $this->objHelperGeneral = $this->_helper->General;
        $this->objHelperLoginwithGoogle = $this->_helper->LoginwithGoogle;
        $this->per_page_record = 10;
        $this->_resetsession = new Zend_Session_Namespace('UserResetEmail');        
        $this->customerauthlogin = API_CUSTOMER_AUTH_LOGIN; // from constant file
        $this->customerauthloginSocial = API_AGENT_AUTH_LOGIN_SOCIAL; // from constant file
        $this->customerauthsignup = API_CUSTOMER_AUTH_SIGNUP; // from constant file
        $this->customerbookinglistAPIUrl = API_CUSTOMER_LIST; // from constant file
        $this->customerbookingFlightAPIUrl = API_CUSTOMER_FLIGHTLIST; // from constant file
        $this->customerFlightInvoiceAPIUrl = API_CUSTOMER_FLIGHTINVOICE; // from constant file
        $this->customerFlightVoucherAPIUrl = API_CUSTOMER_FLIGHTVOUCHER; // from constant file
        $this->customerprofileAPIUrl = API_CUSTOMER_PROFILE; // from constant file
        $this->customerprofilebyemailMobileAPIUrl = API_CUSTOMER_PROFILE_BYEMAIL_MOBILE; // from constant file
        $this->customerchangepasswordAPIUrl = API_CUSTOMER_CHANGEPASSWORD; // from constant file
        $this->customerforgotpasswordAPIUrl = API_CUSTOMER_FORGOTPASSWORD; // from constant file
        $this->customerupdateforgotpasswordAPIUrl = API_CUSTOMER_UPDATE_FORGOTPASSWORD; // from constant file
        $this->customerupdateprofilePIUrl = API_CUSTOMER_UPDATE_PROFILE; // from constant file
        $this->getcitylistAPIUrl = API_CUSTOMER_CITYLIST; // from constant file
        $this->salutation = ARR_SALUTION; // from constant file

    }


    public function indexAction()
    {
        die('index');
    }
    
    public function agencycustomerloginAction(){
        header('Access-Control-Allow-Origin: *');
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getParams();
            $apiData = array(
                'userName' => $data['userName'],
                'userPassword' => $data['userPassword'],
                'AgencySysId' => $this->gtxagencysysid
            );

            try {
                $curl_p = curl_init($this->customerauthlogin);
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
//                        print_r($response);die('fsdfdsfdsf');
            $response_decode   = Zend_Json::decode($response, true);

            if($response_decode == 1){
                $reply = ['status' => false, 'message' => 'Invalid login credentials'];
                echo Zend_Json::encode($reply);exit;
            }elseif($response_decode == 2){
                $reply = ['status' => false, 'message' => 'Your account is inactive. Please Contact to the administrator.'];
                echo Zend_Json::encode($reply);exit;  
            }else{
                echo Zend_Json::encode($response_decode);exit;
            }
        }else{
            die('oops wrong request');
        }
    }
    
    
    public function usersignupAction(){
        header('Access-Control-Allow-Origin: *');
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getParams();
            $contactDetail_mail = Zend_Controller_Action_HelperBroker::getStaticHelper('Custom')->getContactDetailForFooter();


            if($data['password'] !== $data['copassword']){
                $reply = ['status' => false, 'message' => "Confirm password does't matched"];
                echo Zend_Json::encode($reply);exit;
            }
            
            $apiData = array(
                'fname' => $data['firstName'],
                'lname' => $data['lastName'],
                'customerEmail' => $data['EmailId'],
                'countrycode' => $data['countrycode'],
                'mobilenumber' => $data['mobilenum'],
                'source' => '',
                'password' => $data['password'],
                'AgencySysId' => $this->gtxagencysysid,
                'AgentSysId' => $this->gtxagentsysid
            );
            //echo '<pre>';print_r($apiData);die('dd');
            try {
                $curl_p = curl_init($this->customerauthsignup);
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
            
            $response_decode   = Zend_Json::decode($response, true);
//            echo "<pre>";print_r($response_decode);die;
            if($response_decode['CustomerSysId'] == '' || empty($response_decode['CustomerSysId'])){
                $reply = ['status' => false, 'message' => 'Someone already has that credentials. Try another?'];
                echo Zend_Json::encode($reply);exit;
            }else{
                $signup_data = array(
                    'CustomerSysId' => base64_decode($response_decode['CustomerSysId']),
                    'EmailId' => $data['EmailId'],
                    'AgencySysId' => $this->gtxagencysysid,
                    'FirstName' => $response_decode['fname'],
                    'LastName' => $response_decode['lname'],
                    'Password' => md5($data['password']),
                    'Contacts' => ($response_decode['mobilenumber']),
                );
                
                
                
                $subject = "Register successful";
                $message = '<table width="610" border="0" align="center" cellpadding="15" cellspacing="0">
                            <tr><td style="background:#f17524;"><table width="610" border="0" cellspacing="0" cellpadding="0">

                            <tr><td valign="top" bgcolor="#FFFFFF"><table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
                            <tr><td colspan="2" align="center" style=" padding:18px 40px;vertical-align: middle;"><img src="'.$this->baseUrl.'public/images/logo.png" /></td>
                              </tr> <tr><td>&nbsp;</td></tr>
                                    <tr><td><span style="font:bold 14px Tahoma, Geneva, sans-serif;">Dear Customer,</span></td>
                                    </tr><tr><td>&nbsp;</td></tr>
                                    <tr><td ><span style="font:bold 14px Tahoma, Geneva, sans-serif;">Greetings from '.$this->siteName.'.</span></td>
                                    </tr><tr> <td>&nbsp;</td></tr>
                                    </tr><tr> <td>Your registration has been successful!</td></tr>
                                    </tr><tr> <td>&nbsp;</td></tr>
                                    
                                    <tr><td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                <tr><td width="60%" align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                            <tr><td style="color:#7f7f7f; font:normal 14px Tahoma, Geneva, sans-serif;">Email Id: '.$data['EmailId'].'</td>
                                                            </tr><tr><td style="color:#7f7f7f; font:normal 14px Tahoma, Geneva, sans-serif;">Password : '.$data['password'].'</td>
                                                            </tr><tr><td>&nbsp;</td> </tr></table></td></tr></table></td>
                                    </tr><tr><td ><span style="font:normal 14px Tahoma, Geneva, sans-serif;">In case of any assistance please feel free to reach us by email at '.$contactDetail_mail['email'].'  or call us at '.$contactDetail_mail['phone'].'</span></td>
                                    </tr><tr> <td>&nbsp;</td></tr><tr><td>&nbsp;</td></tr>
                                    
                                    <tr> <td ><span style="font:normal 14px Tahoma, Geneva, sans-serif;">Regards,</span></td>
                                    </tr><tr><td>&nbsp;</td>
                                    </tr><tr><td ><span style="font:normal 14px Tahoma, Geneva, sans-serif;">'.$this->siteName.'</span></td>
                                    </tr> <tr> <td>&nbsp;</td></tr></table></td></tr></table></td></tr></table>';



               
                $configs = [
                    'to' => $data['EmailId'],
//                    'to' => 'mangal@catpl.co.in',
                    'fromName' => $this->siteName,
                    'fromEmail' => $contactDetail_mail['email'],
                    'subject' => $subject,
                    'bodyHtml' => $message,
                ];

                $this->_helper->General->mailSentByElastice($configs, 'Registration');
         
                $reply = ['status' => true, 'data'=>$signup_data, 'message' => 'Register successfully. please wait redirecting for first time login!'];
                echo Zend_Json::encode($reply);exit;
            } 
            
        }else{
            die('oops wrong request');
        }
    }
    
    public function myprofileAction()
    {
        $this->checklogin();

        $salutation = unserialize($this->salutation);

        $apiData = array(
            "CustomerSysId" => $_SESSION['User']['session']['CustomerSysId'],
            "AgencySysId" => $_SESSION['User']['session']['AgencySysId']
        );
        
        try {
            $curl = curl_init($this->customerbookinglistAPIUrl);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($apiData));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($curl);

            curl_close($curl);
        } catch (Exception $error) {
            $this->view->error_msg = $error->getMessage();
            die;
        }
        try {
            $curl = curl_init($this->customerbookingFlightAPIUrl);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($apiData));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $responseFlight = curl_exec($curl);

            curl_close($curl);
        } catch (Exception $error) {
            $this->view->error_msg = $error->getMessage();
            die;
        }
//        For profile
        try {
            $curl_p = curl_init($this->customerprofileAPIUrl);
            curl_setopt($curl_p, CURLOPT_POST, true);
            curl_setopt($curl_p, CURLOPT_POSTFIELDS, http_build_query($apiData));
            curl_setopt($curl_p, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl_p, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl_p, CURLOPT_TIMEOUT, 300);
            $response_pro = curl_exec($curl_p);
            curl_close($curl_p);
        } catch (Exception $error) {
            $this->view->error_msg = $error->getMessage();
            die;
        }
        
                
        $result = array();
        $decodeJSON   = Zend_Json::decode($response, true);
        $decode_profile   = Zend_Json::decode($response_pro, true);
        $responseFlight   = Zend_Json::decode($responseFlight, true);
        if(count($decodeJSON['getdata']) >0) {
            foreach($decodeJSON['getdata'] as $key=>$val){
                $RoomInfoJson   = Zend_Json::decode($val['RoomInfoJson'], true);
                $result[] = [
                    'all'=>$val,
                    'roominfo'=>$RoomInfoJson
                ];
            }  
        }
        
        $this->view->alldata = $result;
        $this->view->responseFlight = $responseFlight;
        $this->view->salutation = $salutation;
        $this->view->profile = $decode_profile['profile'];
        $this->view->countryArr = $decode_profile['countryArr'];       
    }
       
    public function updateprofileAction(){
        $this->checklogin();
        if ($this->getRequest()->isPost()) {
            $this->checklogin();
            $data = $this->getRequest()->getParams();
            $DOB = (isset($data['DOB']) && !empty($data['DOB'])?explode('/',$data['DOB']):explode('/','01/01/1900'));
            $PAE = (isset($data['PassportExpiry']) && !empty($data['PassportExpiry'])?explode('/',$data['PassportExpiry']):explode('/','01/01/1900'));
            $MAR = (isset($data['MarriageAnniversary']) && !empty($data['MarriageAnniversary'])?explode('/',$data['MarriageAnniversary']):explode('/','01/01/1900'));
            $apiData = array(
                'Title' => $data['title'],
                'FirstName' => $data['FirstName'],
                'LastName' => $data['LastName'],
                'contacts' => $data['contacts'],
                'PassportNo' => $data['PassportNo'],
                'PassportExpiry' => $PAE[2].'-'.$PAE[1].'-'.$PAE[0],
                'DOB' => $DOB[2].'-'.$DOB[1].'-'.$DOB[0],
                'MarriageAnniversary' => $MAR[2].'-'.$MAR[1].'-'.$MAR[0],
                "CustomerSysId" => $this->_user->session['CustomerSysId'],
                "AgencySysId" => $this->gtxagencysysid,
                "country" => $data['country'],
                "city" => $data['city']
            );
//            echo '<pre>';print_r($apiData);die;
            try {
                $curl_p = curl_init($this->customerupdateprofilePIUrl);
                curl_setopt($curl_p, CURLOPT_POST, true);
                curl_setopt($curl_p, CURLOPT_POSTFIELDS, http_build_query($apiData));
                curl_setopt($curl_p, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl_p, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl_p, CURLOPT_TIMEOUT, 300);
                $response_pro = curl_exec($curl_p);
                curl_close($curl_p);
            } catch (Exception $error) {
                $this->view->error_msg = $error->getMessage();
                die;
            }
//            print_r($response_pro);die;
            if($response_pro == 1){
                $reply = ['status' => true, 'message' => 'Your Profile has been successfully updated.'];
                echo Zend_Json::encode($reply);exit;
            }else{
                $reply = ['status' => false, 'message' => 'Unable to update your profile. try again'];
                echo Zend_Json::encode($reply);exit;
            }
        }
    }
    
    public function getcitylistAction(){
        if ($this->getRequest()->isPost()) {
            $this->checklogin();
            $param = $this->getRequest()->getParams();
            $apiData = array(
                "country" => $param['country']
            );
            try {
                $curl = curl_init($this->getcitylistAPIUrl);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($apiData));
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                $response = curl_exec($curl);
                curl_close($curl);
            } catch (Exception $error) {
                $this->view->error_msg = $error->getMessage();
                die;
            }
            $ResponseDecode   = Zend_Json::decode($response, true);
            $reply = ['status' => true, 'message' => 'Getting city list please wait...','countryId'=>$ResponseDecode];
            echo Zend_Json::encode($reply);exit;
        }
    }
    
    public function changepasswordAction(){
        if ($this->getRequest()->isPost()) {
            $this->checklogin();
            $param = $this->getRequest()->getParams();
            $apiData = array(
                "cpass" => $param['cpass'],
                "npass" => $param['npass'],
                "copass" => $param['copass'],
                "CustomerSysId" => $this->_user->session['CustomerSysId'],
                "AgencySysId" => $this->gtxagencysysid,
            );
            //echo '<pre>';print_r($apiData);die;
            try {
                $curl = curl_init($this->customerchangepasswordAPIUrl);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($apiData));
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                $response = curl_exec($curl);
                curl_close($curl);
            } catch (Exception $error) {
                $this->view->error_msg = $error->getMessage();
                die;
            }
//            echo '<pre>';print_r($response);die;
            if($response == 1){
                $reply = ['status' => false, 'message' => 'Old password does not match'];
                echo Zend_Json::encode($reply);exit;
            }else if($response == 2){
                $reply = ['status' => false, 'message' => 'Confirm password does not match with new password'];
                echo Zend_Json::encode($reply);exit;
            }else if($response == 3){
                $reply = ['status' => true, 'message' => 'Password has been changed successfully.'];
                echo Zend_Json::encode($reply);exit;
            }else{
                $reply = ['status' => false, 'message' => 'Oops there is no response'];
                echo Zend_Json::encode($reply);exit;
            }            
        }
    }
    
    /**
    * forgotpassword() method is used to B2B customer can forgot password
    * @param Null
    * @return Array 
    */
    public function forgotpasswordAction(){
        if($this->getRequest()->isPost()){
            $param = $this->getRequest()->getParams();
            $contactDetail_mail = Zend_Controller_Action_HelperBroker::getStaticHelper('Custom')->getContactDetailForFooter();

            $apiData = array(
                "forget" => $param['forget'],
                "AgencySysId" => $this->gtxagencysysid
            );
            
            try {
                $curl = curl_init($this->customerforgotpasswordAPIUrl);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($apiData));
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                $response = curl_exec($curl);
                curl_close($curl);
            } catch (Exception $error) {
                $this->view->error_msg = $error->getMessage();
                die;
            }
            $ResponseDecode   = Zend_Json::decode($response, true);
            //echo '<pre>';print_r($ResponseDecode);die;
            if($ResponseDecode['status'] == 1){
                $datetime = date('d-m-y h:i:s');
                $time_str = strtotime($datetime);
                $token = md5($ResponseDecode['data']['CustomerSysId']);
                $CustomerSysId = base64_encode($ResponseDecode['data']['CustomerSysId']);
                $EmailId = $ResponseDecode['data']['EmailId'];
                $FirstName = $ResponseDecode['data']['FirstName'];
                $CheckEmailId = base64_encode($ResponseDecode['data']['EmailId']);
                $AgencySysId = $ResponseDecode['data']['AgencySysId'];
                
                $reseturlclick = $this->baseUrl."customer/checkresetlink?token=$token&ag=$AgencySysId&eid=$CheckEmailId&CTR=$time_str&cd=$CustomerSysId";
                //echo '<pre>';print_r($ResponseDecode);die;
                $name = $FirstName;
                $customer_email = $EmailId;
                $from_email = $contactDetail_mail['email'];

                $subject = "Password Change Request";
                $message = "Hello $name<br><br>";
                $message .= "Greetings from $this->siteName team.<br><br>";
                $message .= "It is our pleasure to fulfill your request for new password.<br><br>";
                $message .= "To change your account password at $this->siteName please click this link or copy and paste the following link into your browser. This link expire within 10 minutes: <br><br>";
                $message .= " <a href='$reseturlclick'>Click here to reset your password</a> <br><br><br>";
                $message .= "Thank you for customer with us.<br><br>";
                $message .= "$this->siteName Team.";                
               
                $configs = [
                    'to' => $customer_email ,
                    'fromName' => $this->siteName ,
                    'fromEmail' => $from_email ,
                    'subject' => $subject ,
                    'bodyHtml' => $message ,
                ];

                $this->_helper->General->mailSentByElastice( $configs , 'Forgot' );
                // Mail it
                $reply = ['status' => true, 'message' => 'Email has been sent successfully.'];
                echo Zend_Json::encode($reply);exit;
           } else {
                $reply = ['status' => false, 'message' => 'Invalid email. Please try again.'];
                echo Zend_Json::encode($reply);exit;
            }
        }else{
            echo 'Oops wrong request';exit;
        }
    }
    
    public function checkresetlinkAction(){
        if($_SESSION['User']['session'])  
        {  
            $this->_redirect('customer/myprofile');  
        }
        $param = $this->getRequest()->getParams();
        $datetime = date('d-m-y h:i:s');
        $seconds = strtotime($datetime) - ($param['CTR']);
        $days    = floor($seconds / 86400);
        $hours   = floor(($seconds - ($days * 86400)) / 3600);
        $minutes = floor(($seconds - ($days * 86400) - ($hours * 3600))/60);
        if($minutes <= 10){
            $eid = base64_decode($param['eid']);
            $cd = base64_decode($param['cd']);
            $token = ($param['token']);
            $ag = ($param['ag']);
            $SubmitData = array(
                "eid" => $eid,
                "cd" => $cd,
                "ag" => $ag,
                "token" => $token,
                "CTR" => $param['CTR']
            );
            $this->_resetsession->resetpass = $SubmitData;
            $this->_redirect('customer/resetpassword');
        }else{
            die('Oops your reset password link is expired!! try again.');
        }
        
    }
    
    public function resetpasswordAction(){
        if(isset($_SESSION['UserResetEmail']['resetpass'])){
            //print_r($_SESSION['UserResetEmail']);die;
            $datetime = date('d-m-y h:i:s');
            $seconds = strtotime($datetime) - ($_SESSION['UserResetEmail']['resetpass']['CTR']);
            $days    = floor($seconds / 86400);
            $hours   = floor(($seconds - ($days * 86400)) / 3600);
            $minutes = floor(($seconds - ($days * 86400) - ($hours * 3600))/60);
            if($minutes <= 10){
                $this->view->data = $_SESSION['UserResetEmail']['resetpass'];
            }else{
               $this->_redirect('customer/unsetresetdata'); 
            }

            if($this->getRequest()->isPost()){
                $param = $this->getRequest()->getParams();
                $apiData = array(
                    "npass" => $param['npass'],
                    "copass" => $param['copass'],
                    "email" => $_SESSION['UserResetEmail']['resetpass']['eid'],
                    "CustomerSysId" => $_SESSION['UserResetEmail']['resetpass']['cd'],
                    "AgencySysId" => $_SESSION['UserResetEmail']['resetpass']['ag']
                );
                
                try {
                    $curl = curl_init($this->customerupdateforgotpasswordAPIUrl);
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($apiData));
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                    $response = curl_exec($curl);
                    curl_close($curl);
                } catch (Exception $error) {
                    $this->view->error_msg = $error->getMessage();
                    die;
                }
                //print_r($response);die;
                if($response == 4){
                    $reply = ['status' => false, 'message' => 'All field required!!'];
                    echo Zend_Json::encode($reply);exit;
                }elseif($response == 3){
                    $reply = ['status' => false, 'message' => 'Confirm password does not match with new password'];
                    echo Zend_Json::encode($reply);exit;
                }elseif($response == 2){
                    $reply = ['status' => false, 'message' => 'Password update not response!!'];
                    echo Zend_Json::encode($reply);exit;
                }elseif($response == 1){
                    $reply = ['status' => true, 'message' => 'Password has been reset successfully. Now Login and continue.'];
                    echo Zend_Json::encode($reply);exit;
                }else{
                    $reply = ['status' => false, 'message' => 'Oops there is no response'];
                    echo Zend_Json::encode($reply);exit;
                }
                //print_r($response);die;
            }
        }else{
            echo('Oops! There seems to be some problem in processing your request!');exit;
        }
        
        //print_r($_SESSION['UserResetEmail']['resetpass']);
        
        //exit;
        
        //print_r($param);die;
    }
       
    public function unsetresetdataAction()
    {
        $storage = new Zend_Session_Namespace('UserResetEmail');
        $storage->unsetAll(); 
        $this->_redirect('index');
    }
    
    public function unsetdataAction()
    {
        $storage = new Zend_Session_Namespace('User');
        $storage->unsetAll(); 
        $this->_redirect('index');
    }
    
    /**
    * checklogin() method is used to check admin logedin or not
    * @param Null
    * @return Array 
    */
    public function checklogin()
    {
        /*************** check admin identity ************/
        if(!$_SESSION['User']['session'])  
        {  
            $this->_redirect('index');  
        } 
    }
    
    /******Google authentication code by sibo*****/
    public function loginwithGoogleAction()
    { 
        $Loginwithgoogle = $this->objHelperLoginwithGoogle->Loginwithgoogle();
        //print_r($Loginwithgoogle);die('dd');
        $this->_redirect($Loginwithgoogle);
    }
    
    public function googleAuthenticationAction()
    {
        $userData = $this->objHelperLoginwithGoogle->redirectgoogle();
        $apiData = array(
            'id' => $userData['id'],
            'email' => $userData['email'],
            'gender' => $userData['gender'],
            'picture' => $userData['picture'],
            'familyName' => $userData['familyName'],
            'givenName' => $userData['givenName'],
                
        );
        $this->_sessionSocial->sessionSocial = $apiData;
        $this->_redirect('customer/login');
    }
    
    public function loginAction(){
        if(isset($this->_sessionSocial->sessionSocial) && !empty($this->_sessionSocial->sessionSocial)){
//        $storage = new Zend_Session_Namespace('Social');
//        $storage->unsetAll(); 
//        $this->_redirect('/');
            $apiData = array(
                'fname' => $this->_sessionSocial->sessionSocial['givenName'],
                'lname' => $this->_sessionSocial->sessionSocial['familyName'],
                'customerEmail' => $this->_sessionSocial->sessionSocial['email'],
                'countrycode' => '',
                'mobilenumber' => '',
                'source' => '',
                'password' => date('d-m-y h:s:i'),
                'AgencySysId' => $this->gtxagencysysid,
                'AgentSysId' => $this->gtxagentsysid
            );
            $this->view->apiData = $apiData;
            //echo '<pre>';print_r($apiData);die;
            if($this->getRequest()->isPost()){
                $param = $this->getRequest()->getParams();
                
                $apiDataLogin = array(
                    'fname' => $this->_sessionSocial->sessionSocial['givenName'],
                    'lname' => $this->_sessionSocial->sessionSocial['familyName'],
                    'customerEmail' => $this->_sessionSocial->sessionSocial['email'],
                    'countrycode' => isset($param['ountryCode'])?$param['ountryCode']:'',
                    'mobilenumber' => isset($param['mobilenumber'])?$param['mobilenumber']:'',
                    'source' => '',
                    'password' => date('d-m-y h:s:i'),
                    'AgencySysId' => $this->gtxagencysysid,
                    'AgentSysId' => $this->gtxagentsysid
                );
                try {
                    $curl_p = curl_init($this->customerauthsignup);
                    curl_setopt($curl_p, CURLOPT_POST, true);
                    curl_setopt($curl_p, CURLOPT_POSTFIELDS, http_build_query($apiDataLogin));
                    curl_setopt($curl_p, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl_p, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($curl_p, CURLOPT_TIMEOUT, 300);
                    $response = curl_exec($curl_p);
                    curl_close($curl_p);
                } catch (Exception $error) {
                    $this->view->error_msg = $error->getMessage();
                    die;
                }
                $response_decode   = Zend_Json::decode($response, true);
                //echo '<pre>';print_r($response_decode);die('ff');
                if($response_decode['CustomerSysId'] == '' || empty($response_decode['CustomerSysId'])){
                    $datas = array(
                        'CustomerEmail'=> $response_decode['customerEmail'],
                        'CustomerMobile'=> $response_decode['mobilenumber'],
                        'AgencySysId'=> $this->gtxagencysysid,
                    );
                    try {
                        $curl_p = curl_init($this->customerprofilebyemailMobileAPIUrl);
                        curl_setopt($curl_p, CURLOPT_POST, true);
                        curl_setopt($curl_p, CURLOPT_POSTFIELDS, http_build_query($datas));
                        curl_setopt($curl_p, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($curl_p, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($curl_p, CURLOPT_TIMEOUT, 300);
                        $response_user = curl_exec($curl_p);
                        curl_close($curl_p);
                    } catch (Exception $error) {
                        $this->view->error_msg = $error->getMessage();
                        die;
                    }
                    $users_decode   = Zend_Json::decode($response_user, true);
                    if($users_decode['status']=='1'){
                        $Login_Data = array(
                            'userName' => $users_decode['profile']['EmailId'],
                            'userPassword' => $users_decode['profile']['Password'],
                            'AgencySysId' => $this->gtxagencysysid
                        );
                    }
                    
                }else{
                    //echo '<pre>';print_r($response_decode);die('ff');
                   if($response_decode['status']=='success'){
                        $Login_Data = array(
                            'userName' => $response_decode['customerEmail'],
                            'userPassword' => $response_decode['_token'],
                            'AgencySysId' => $this->gtxagencysysid
                        );
                    }  
                }
                try {
                    $curl_p = curl_init($this->customerauthloginSocial);
                    curl_setopt($curl_p, CURLOPT_POST, true);
                    curl_setopt($curl_p, CURLOPT_POSTFIELDS, http_build_query($Login_Data));
                    curl_setopt($curl_p, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl_p, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($curl_p, CURLOPT_TIMEOUT, 300);
                    $response = curl_exec($curl_p);
                    curl_close($curl_p);
                } catch (Exception $error) {
                    $this->view->error_msg = $error->getMessage();
                    die;
                }
                $response_login_decode   = Zend_Json::decode($response, true);
                if($response_login_decode == 2){
                    $reply = ['status' => false, 'message' => 'Your account is inactive. Please Contact to the administrator.'];
                    echo Zend_Json::encode($reply);exit;  
                }else{
                    echo Zend_Json::encode($response_login_decode);exit;
                }
            }
            //echo '<pre>';print_r($response_decode);die('ff');
        }else{
            die('Access Denied');
        }
    }
    
    
    public function loginfacebookAction(){
        if(isset($this->_sessionSocialFB->_sessionSocialFB) && !empty($this->_sessionSocialFB->_sessionSocialFB)){
//        $storage = new Zend_Session_Namespace('Social');
//        $storage->unsetAll(); 
//        $this->_redirect('/');
            $apiData = array(
                'fname' => $this->_sessionSocialFB->_sessionSocialFB['givenName'],
                'lname' => $this->_sessionSocialFB->_sessionSocialFB['familyName'],
                'customerEmail' => $this->_sessionSocialFB->_sessionSocialFB['email'],
                'countrycode' => '',
                'mobilenumber' => '',
                'source' => '',
                'password' => date('d-m-y h:s:i'),
                'AgencySysId' => $this->gtxagencysysid,
                'AgentSysId' => $this->gtxagentsysid
            );
            $this->view->apiData = $apiData;
            //echo '<pre>';print_r($apiData);die;
            if($this->getRequest()->isPost()){
                $param = $this->getRequest()->getParams();
                
                $apiDataLogin = array(
                    'fname' => $this->_sessionSocialFB->_sessionSocialFB['givenName'],
                    'lname' => $this->_sessionSocialFB->_sessionSocialFB['familyName'],
                    'customerEmail' => $this->_sessionSocialFB->_sessionSocialFB['email'],
                    'countrycode' => isset($param['ountryCode'])?$param['ountryCode']:'',
                    'mobilenumber' => isset($param['mobilenumber'])?$param['mobilenumber']:'',
                    'source' => '',
                    'password' => date('d-m-y h:s:i'),
                    'AgencySysId' => $this->gtxagencysysid,
                    'AgentSysId' => $this->gtxagentsysid
                );
                try {
                    $curl_p = curl_init($this->customerauthsignup);
                    curl_setopt($curl_p, CURLOPT_POST, true);
                    curl_setopt($curl_p, CURLOPT_POSTFIELDS, http_build_query($apiDataLogin));
                    curl_setopt($curl_p, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl_p, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($curl_p, CURLOPT_TIMEOUT, 300);
                    $response = curl_exec($curl_p);
                    curl_close($curl_p);
                } catch (Exception $error) {
                    $this->view->error_msg = $error->getMessage();
                    die;
                }
                $response_decode   = Zend_Json::decode($response, true);
                //echo '<pre>';print_r($response_decode);die('ff');
                if($response_decode['CustomerSysId'] == '' || empty($response_decode['CustomerSysId'])){
                    $datas = array(
                        'CustomerEmail'=> $response_decode['customerEmail'],
                        'CustomerMobile'=> $response_decode['mobilenumber'],
                        'AgencySysId'=> $this->gtxagencysysid,
                    );
                    try {
                        $curl_p = curl_init($this->customerprofilebyemailMobileAPIUrl);
                        curl_setopt($curl_p, CURLOPT_POST, true);
                        curl_setopt($curl_p, CURLOPT_POSTFIELDS, http_build_query($datas));
                        curl_setopt($curl_p, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($curl_p, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($curl_p, CURLOPT_TIMEOUT, 300);
                        $response_user = curl_exec($curl_p);
                        curl_close($curl_p);
                    } catch (Exception $error) {
                        $this->view->error_msg = $error->getMessage();
                        die;
                    }
                    $users_decode   = Zend_Json::decode($response_user, true);
                    if($users_decode['status']=='1'){
                        $Login_Data = array(
                            'userName' => $users_decode['profile']['EmailId'],
                            'userPassword' => $users_decode['profile']['Password'],
                            'AgencySysId' => $this->gtxagencysysid
                        );
                    }
                    
                }else{
                    //echo '<pre>';print_r($response_decode);die('ff');
                   if($response_decode['status']=='success'){
                        $Login_Data = array(
                            'userName' => $response_decode['customerEmail'],
                            'userPassword' => $response_decode['_token'],
                            'AgencySysId' => $this->gtxagencysysid
                        );
                    }  
                }
                try {
                    $curl_p = curl_init($this->customerauthloginSocial);
                    curl_setopt($curl_p, CURLOPT_POST, true);
                    curl_setopt($curl_p, CURLOPT_POSTFIELDS, http_build_query($Login_Data));
                    curl_setopt($curl_p, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl_p, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($curl_p, CURLOPT_TIMEOUT, 300);
                    $response = curl_exec($curl_p);
                    curl_close($curl_p);
                } catch (Exception $error) {
                    $this->view->error_msg = $error->getMessage();
                    die;
                }
                $response_login_decode   = Zend_Json::decode($response, true);
                if($response_login_decode == 2){
                    $reply = ['status' => false, 'message' => 'Your account is inactive. Please Contact to the administrator.'];
                    echo Zend_Json::encode($reply);exit;  
                }else{
                    echo Zend_Json::encode($response_login_decode);exit;
                }
            }
            //echo '<pre>';print_r($response_decode);die('ff');
        }else{
            die('Access Denied');
        }
    }
    
    /*******Login with Facebook*********/
    
//    public function loginwithFacebookAction()
//    {
//        $Loginwithgoogle = $this->objHelperLoginwithFacebook->Loginwithfacebook();
//        print_r($Loginwithgoogle);die('dd');
//        $this->_redirect($Loginwithgoogle);
//    }
    
    public function facebookAuthenticationAction(){
        if($this->getRequest()->isPost()){
            $param = $this->getRequest()->getParams();
            
            $apiDatalogin = array(
                'id' => $param['fbid'],
                'email' => $param['femail'],
                'picture' => $param['profilephoto'],
                'familyName' => $param['flast_name'],
                'givenName' => $param['ffirst_name'],

            );
            $this->_sessionSocialFB->_sessionSocialFB = $apiDatalogin;
            $this->_redirect('customer/loginfacebook');
            echo '<pre>';print_r( $apiDatalogin);die('dd');  
        }
//        if(isset($_SESSION['Fbdata']) && !empty($_SESSION['Fbdata'])){
//            $apiData = array(
//                'id' => $_SESSION['Fbdata']['fbid'],
//                'email' => $_SESSION['Fbdata']['femail'],
//                'picture' => $_SESSION['Fbdata']['profilephoto'],
//                'familyName' => $_SESSION['Fbdata']['flast_name'],
//                'givenName' => $_SESSION['Fbdata']['ffirst_name'],
//
//            );
//            //$this->_redirect('customer/login');
//            //echo '<pre>';print_r( $apiData);die('dd');
//            $this->sessionSocial->sessionSocial = $apiData;
//            $this->_redirect('customer/login');
//        }else{
//          die('Access Denied');  
//        }
    }
    
}

