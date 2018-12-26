<?php

/* * *************************************************************
 * Catabatic Technology Pvt. Ltd.
 * File Name     : ContactusController.php
 * File Desc.    : Contactus controller managed all contact queries
 * Created By    : Piyush Tiwari <piyush@catpl.co.in>
 * Created Date  : 23 June 2018
 * Updated Date  : 23 June 2018
 * ************************************************************* */

class Admin_NewsletterController extends Zend_Controller_Action {

    public $dbAdapter;
    public $perPageLimit;

    public function init() {
        /* Initialize db and session access */
        $aConfig = $this->getInvokeArg('bootstrap')->getOptions();
        $this->siteurl = $aConfig['bootstrap']['siteUrl'];
        $this->per_page_record = 20;
        $this->dbAdapter = Zend_Db_Table::getDefaultAdapter();

        $auth = Zend_Auth::getInstance();
        $authStorage = $auth->getStorage()->read();
        $this->username = $authStorage->username;
        $this->admin_type = $authStorage->role;
        $this->tablenews = 'tb_tbb2c_newsletter';
    }

    /**
     * index() method is used to admin login for form call
     * @param Null
     * @return Array 
     */
    public function indexAction() {
        //Check admin logedin or not

        $this->checklogin();
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
        $crud = new Admin_Model_CRUD();
        $getData = array();
        if ($this->getRequest()->isPost()) {
            $getData = $this->getRequest()->getPost();
            $searchArr = array(
                'Title' => $getData['Title'],
                'rows' => $getData['rows'],
                'page' => $getData['page'],
            );
            $resulsetold = $crud->getCount($this->tablenews, [''], 'news_letter_id');
            $crud->searchArr = $searchArr;
            $resultset = $crud->rv_select_static($this->tablenews, ['news_letter_id', 'news_letter_email', 'created_date', 'status'], [''], ['news_letter_id' => 'DESC']);
            if (count($resultset) > 0) {
                foreach ($resultset as $resultkey => $resultval) {
                    $result1[] = [
                        'news_letter_id' => $resultval['news_letter_id'],
                        'news_letter_email' => $resultval['news_letter_email'],
                        'created_date' => $resultval['created_date'],
                        'status' => $resultval['status'] == 1 ? 'Active' : 'Deactive',
                    ];
                }
            }
            $result = Zend_Json::encode($result1);
            $newResult = Zend_Json::decode($result, false);
            $finalResult["total"] = $resulsetold[0]['news_letter_id'];
            $finalResult["rows"] = $newResult;
            echo json_encode($finalResult);
            exit;
        }
    }
    
        public function editletterAction(){
        //Check admin logedin or not
        $this->checklogin();
        $crud = new Admin_Model_CRUD();
        $form = new Admin_Form_Editletter();
        $pId = (int)$this->getRequest()->getParam("id");
        $form->setMethod("POST");
        $form->setAction("admin/newsletter/editletter/id/".$pId);
        $form->setName("edit_letter_page");
               
        if( $this->getRequest()->isPost() ) {
            $getData = $this->getRequest()->getPost();
            
            if($form->isValid($getData)) {
                
                //-------Start Code for Approve and Publish content---------//
               if(isset($getData['save'])=="Save") {
                   $editPageData = [
//                            'news_letter_id'=>($getData['news_letter_id']),
                            'news_letter_email'=>($getData['news_letter_email']),
//                            'created_date'=>($getData['created_date']),
                            'status'=>"{$getData['status']}"
                        ];
//                            echo "<pre>";print_r($editPageData);die;
                        $crud->rv_update($this->tablenews, $editPageData, ['news_letter_id =?'=>$pId]);
                        $this->_helper->flashMessenger->addMessage("Content has been updated successfully.");
                        $this->_redirect("/admin/newsletter/index");   
             }  
             
          }
        }

        $result = $crud->getCmsdata($this->tablenews, ['*'], ['news_letter_id'=>$pId], ['news_letter_id'=>'DESC']);
        //echo "<pre>";print_r($result);die;
        $editdata["news_letter_id"] = @$result->news_letter_id;
        $editdata["news_letter_email"] = @$result->news_letter_email;
        $editdata["created_date"] = @$result->created_date;
        $editdata["status"] = @$result->status;
        
        $form->populate($editdata);
        
         
        $this->view->form = $form;
	//die('ok');
    }
    
    public function deleteletterAction() {
        $this->checklogin();
        $crud = new Admin_Model_CRUD();
        $tId = (int)$this->getRequest()->getParam("id");
        if($tId){
            $checkdata = $crud->rv_select_row($this->tablenews, ['news_letter_id'], ['news_letter_id'=>$tId], ['news_letter_id'=>'asc']);
            if(count($checkdata)>0){
                $crud->rv_delete($this->tablenews, ['news_letter_id =?'=>$tId]);
                $this->_helper->flashMessenger->addMessage("Delete successfully.");
                $this->_redirect("/admin/newsletter/index");
            }else{
                die('Oops some thing wrong!!.');
            }  
        }
    }
    
    public function exportexcelreportAction(){
         $this->checklogin();
        $crud = new Admin_Model_CRUD();
        $resultset  = $crud->rv_select_all('tb_tbb2c_newsletter', ['*'],['status'=>1], ['news_letter_id'=>'DESC']);
        
        $resultsetArr = array();
$m =1;
            foreach ($resultset as $reskey => $resvalue) {
            $resultsetArr[] = [
                        'no' => $m,
                        'email' => $resvalue['news_letter_email'],
                        'created_date' => $resvalue['created_date'],
                       
                            ];
            $m++;}
        
        $sheetTitle = "newsletter_subscription_list";
        $arrFieldLabel = array('Sr.No.','Email Id', 'Created Date');
        Zend_Controller_Action_HelperBroker::getStaticHelper("Custom")->exportToExcel($sheetTitle, $arrFieldLabel, $resultsetArr);
       $this->_redirect('admin/newsletter/index');
        
    }

    /**
     * checklogin() method is used to check admin logedin or not
     * @param Null
     * @return Array 
     */
    public function checklogin() {
        if (($this->admin_type == "superadmin") || ($this->admin_type == "admin")) {
            $auth = Zend_Auth::getInstance();
            $hasIdentity = $auth->hasIdentity();
            /*             * ************* check admin identity *********** */
            if (!$hasIdentity) {
                $this->_redirect('admin/index/index');
            }
        } else {
            $this->_redirect('admin/index/index');
        }
    }

}
