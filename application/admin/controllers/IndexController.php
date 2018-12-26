<?php

/* * *************************************************************
 * Catabatic Technology Pvt. Ltd.
 * File Name     : IndexController.php
 * File Desc.    : Index Controller managed all dashboard and index page
 * Created By    : Piyush Tiwari <piyush@catpl.co.in>
 * Created Date  : 23 May 2018
 * Updated Date  : 23 May 2018
 * ************************************************************* */

class Admin_IndexController extends Zend_Controller_Action {

    public $dbAdapter;

    public function init() {
        /* Initialize action controller here */
        $this->dbAdapter = Zend_Db_Table::getDefaultAdapter();
        $aConfig = $this->getInvokeArg('bootstrap')->getOptions();
        $this->superAdminEmail = $aConfig['bootstrap']['superAdminEmail'];

        $auth = Zend_Auth::getInstance();
        $authStorage = $auth->getStorage();
        $authStorage->read();
    }

    /**
     * index() method is used to admin login for form call
     * @param Null
     * @return Array 
     */
    public function indexAction() {
        $this->_helper->layout()->disableLayout('');
        $dbAdapter = $this->dbAdapter;
        $auth = Zend_Auth::getInstance();
        //$admin = new Admin_Model_Admin();
        $form = new Admin_Form_Login();
        $form->setAction("admin/index/index");
        $form->setMethod("POST");
        $this->errorMessage = "";

        /*         * ************* check user identity *********** */
        if ($auth->hasIdentity()) {
            $this->_redirect('admin/dashboard/index');
        }
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
//                echo "<pre>";print_r($_POST);die;
                if (strtolower($_POST['captcha']) != $_SESSION['captcha']) {
                    $this->view->errorMessage = "Captcha code invalid.";
                } else {


                    $data = $form->getValues();
                    $username = $data['username'];
                    $password = $data['password'];

//                echo $username , $password ; die;
                    $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);

                    //Set the input credential values
                    $authAdapter->setTableName('admin_user')
                            ->setIdentityColumn('username')
                            ->setCredentialColumn('password')
                            ->setCredentialTreatment("MD5(?) AND is_active='1' ");
                    $authAdapter->setIdentity($username)
                            ->setCredential($password);
                    //echo "<pre>"; print_r($authAdapter);die;

                    $result = $auth->authenticate($authAdapter);
                    if ($result->isValid()) {
                        $storage = new Zend_Auth_Storage_Session();
                        $storage->write($authAdapter->getResultRowObject());
                        $auth = Zend_Auth::getInstance();
                        $authStorage = $auth->getStorage();
                        $this->_redirect('admin/dashboard/index');
                    } else {
                        $this->view->errorMessage = "Invalid username and/or password";
                    }
                }
            }
        }
    }

    /**
     * forgotpassword() method is used to admin can forgot password
     * @param Null
     * @return Array 
     */
    public function forgotpasswordAction() {
        $this->_helper->layout()->disableLayout('');
//       $this->view->headTitle('DCB Bank Admin');
        $admin = new Admin_Model_Admin();
        $form = new Admin_Form_Forgot();
        $form->setAction("admin/index/forgotpassword");
        $form->setMethod("POST");
        $this->view->form = $form;
        $message = "";
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
                $data = $form->getValues();
                $email = $data['email'];
                $result = $admin->getAdminUserListByEmail($email);
//                echo"<pre>";print_r($result);die;
                if (isset($result) && !empty($result)) {
                    $admin_id = $result->user_id;
                    $admin_username = ucfirst($result->username);
                    $admin_email = $result->email;
                    $password = $result->password;
                    $from_email = $this->superAdminEmail;
                    $randomString = $this->randomString();
                    $admin->updateChangePasswordByAdminId($randomString, $admin_id);

                    $subject = "Forgot Password Email";
                    $message .= "Here is your admin login details:<br><br>";
                    $message .= "Username: $admin_username <br>";
                    $message .= "New Password: $randomString <br><br>";
                    $message .= "Thanks,<br>";

                    // To send HTML mail, the Content-type header must be set
                    $headers = 'MIME-Version: 1.0' . "\r\n";
                    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                    $headers .= 'From: ' . $admin_username . ' (' . $from_email . ')' . "\r\n";
                    // Mail it
                    $retval = mail($admin_email, $subject, $message, $headers);
//                    echo"<pre>";print_r($retval);die;
                    if ($retval == true) {
                        $this->view->successMessage = "Email has been sent successfully.";
                    } else {
                        $this->view->errorMessage = "Message could not be sent.";
                    }
                } else {
                    $this->view->errorMessage = "Invalid email. Please try again.";
                }
            }
        }
    }

    public function randomString() {
        $length = 6;
        $chars = "0123456789ABCDEFGHI";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $str;
    }

    /**
     * checklogin() method is used to check admin logedin or not
     * @param Null
     * @return Array 
     */
    public function checklogin() {
        $auth = Zend_Auth::getInstance();
        /*         * ************* check user identity *********** */
        if (!$auth->hasIdentity()) {
            $this->_redirect('admin/index/index');
        }
    }

    /*     * ** logout ********* */

    public function logoutAction() {
        if ($this->getRequest()->getParam('module') == 'admin') {
            $storage = new Zend_Auth_Storage_Session();
            $storage->clear();
            $this->_redirect('admin/index/index');
        } else {
            $this->_redirect('admin/index/index');
        }
    }

}
