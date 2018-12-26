<?php
/***************************************************************
 * Catabatic Technology Pvt. Ltd.
 * File Name   : IndexController.php
 * File Description  : Managed all users settings
 * Created By : Piyush Tiwari <piyush@catpl.co.in>
 * Created Date: 03-September-2018
 ***************************************************************/

class Admin_DashboardController extends Zend_Controller_Action
{
   
    public $dbAdapter;
    public function init()
    {
       $this->dbAdapter = Zend_Db_Table::getDefaultAdapter();
    }
    
    /**
    * index() method is used to admin login for form call
    * @param Null
    * @return Array 
    */
    public function indexAction()
    {
        $this->checklogin();
        
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
    
    /**
    * changepassword() method is used to admin user change password
    * @param password string
    * @return ture 
    */
    public function changepasswordAction()
    {
        //Check admin logedin or not
        $this->checklogin();
        //Get db adapter
        $dbAdapter = $this->dbAdapter;
        $admin = new Admin_Model_Admin();
        $form = new Admin_Form_Password();
        $form->setAction("admin/dashboard/changepassword");
	$form->setMethod("POST");
        $errorMessage = ""; 
        $successMessage = ""; 
        $this->view->form = $form;
        
        if($this->getRequest()->isPost()){
            if($form->isValid($_POST)){
                $data = $form->getValues();
                $old_pass = md5($data['old_pass']); 
                $password = $data['password']; 
                $cpassword = $data['cpassword']; 
                //Admin user session data
                $auth = Zend_Auth::getInstance();
                $authStorage = $auth->getStorage();
                $admin_id = $authStorage->read()->user_id;
                $admin_password = $authStorage->read()->password; 

                if($old_pass != $admin_password)
                {
                  $this->view->errorMessage = "Old password does not match";
                }
                if($password != $cpassword)
                {
                  $this->view->errorMessage = "Confirm password does not match with new password";
                }

                if(($password == $cpassword) && ($old_pass == $admin_password)) 
                {
                    $result = $admin->updateChangePasswordByAdminId($password,$admin_id);
                    Zend_Auth::getInstance()->getStorage()->write($authStorage->read(),array('password' => md5($password)));
                    $authStorage->read()->password = md5($password);

                    $this->view->successMessage = "Password has been changed successfully.";
                }	
           }
        } 
    }
    
    /**
    * checklogin() method is used to check admin logedin or not
    * @param Null
    * @return Array 
    */
    public function checklogin()
    {
        $auth = Zend_Auth::getInstance();
        /*************** check admin identity ************/
        if(!$auth->hasIdentity())  
        {  
            $this->_redirect('admin/index/index');  
        } 
    }
}