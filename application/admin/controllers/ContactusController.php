<?php
/***************************************************************
* Catabatic Technology Pvt. Ltd.
* File Name     : ContactusController.php
* File Desc.    : Contactus controller managed all contact queries
* Created By    : Piyush Tiwari <piyush@catpl.co.in>
* Created Date  : 23 May 2017
* Updated Date  : 23 May 2017
***************************************************************/


class Admin_ContactusController extends Zend_Controller_Action
{
    
    public $dbAdapter;
    public $perPageLimit;

    
    public function init()
    {
       /*Initialize db and session access */
       $aConfig = $this->getInvokeArg('bootstrap')->getOptions();
       $this->siteurl = $aConfig['bootstrap']['siteUrl']; 
       $this->perPageLimit = $aConfig['bootstrap']['perPageLimit']; 
       $this->dbAdapter = Zend_Db_Table::getDefaultAdapter(); 
       
       $auth        = Zend_Auth::getInstance();
       $authStorage = $auth->getStorage()->read();
       $this->username      = $authStorage->username;
       $this->admin_type    = $authStorage->role;
    }
    
    /**
    * index() method is used to admin login for form call
    * @param Null
    * @return Array 
    */
    public function indexAction()
    {
        //Check admin logedin or not
        $this->checklogin();
        $crud   = new Admin_Model_CRUD();
        $resultset  = $crud->rv_select_all("tbl_contactus", ['*'], ['status'=>1], ['sid'=>'DESC']);
//        echo "<pre>";print_r($resultset);die;
                
        # Start : Pagination 
        $page       = $this->_getParam('page', 1);
        $resultset  = Zend_Paginator::factory($resultset);
        $resultset->setItemCountPerPage($this->per_page_record);
        $resultset->setCurrentPageNumber($page);
        # End : Pagination
        
        $this->view->resultset  = $resultset;
        $this->view->messages   = $this->_helper->flashMessenger->getMessages();
        
    }
    
    
    public function sanitize_data($string) {
	$searchArr=array("iframe","script","document","write","alert","%","@","$",";","+","|","#","<",">",")","(","'","\'",",","and "," &","& ","and"," and","0","1","2","3","4","5","6","7","8","9");
	$input_data = strtolower($string);
	$input_data = str_replace($searchArr,"",$input_data);
        
        $input_data= str_replace(" ","-",$input_data);
        //echo $input_data; die;
        return $input_data;
    }
    
    
   
    /**
    * checklogin() method is used to check admin logedin or not
    * @param Null
    * @return Array 
    */
    public function checklogin()
    {
        if(($this->admin_type == "superadmin") || ($this->admin_type == "admin"))
        {
            $auth = Zend_Auth::getInstance();
            $hasIdentity = $auth->hasIdentity();
            /*************** check admin identity ************/
            if(!$hasIdentity)  
            {  
                   $this->_redirect('admin/index/index');  
            } 
        }  else {
            $this->_redirect('admin/index/index');   
        } 
    }
}