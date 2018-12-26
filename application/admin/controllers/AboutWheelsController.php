<?php
/***************************************************************
* Catabatic Technology Pvt. Ltd.
* File Name     : StaticpageController.php
* File Desc.    : Staticpage controller managed all staic content pages
* Created By    : Piyush Tiwari <piyush@catpl.co.in>
* Created Date  : 23 May 2018
* Updated Date  : 23 May 2018
***************************************************************/



class Admin_AboutWheelsController extends Zend_Controller_Action
{
    

    public $dbAdapter;
    public $perPageLimit;
    public $siteurl;
    public $DIR_WRITE_MODE;
    
    
    
    public function init()
    {
       /*Initialize db and session access */
       $aConfig = $this->getInvokeArg('bootstrap')->getOptions();
       $this->siteurl           = $aConfig['bootstrap']['siteUrl']; 
		$this->appmode = $aConfig['bootstrap']['appmode'];
		$this->per_page_record   = 20;

        $this->dbAdapter = Zend_Db_Table::getDefaultAdapter();
       
       $auth        = Zend_Auth::getInstance();
       $authStorage = $auth->getStorage()->read();
       $this->username      = $authStorage->username;
       $this->admin_type    = $authStorage->role;
       
       $this->current_time = time();
        $this->imageUrl     = (($this->appmode == 'MODE_BETA') ? 'beta/' : '') . 'public/upload/aboutWheels/';

        $this->img_w_small  = 120;
        $this->img_h_small  = 120;

        $this->DIR_WRITE_MODE = 0777;
        
       $this->table =  'tbl_about_wheels';
     
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
        $resultset  = $crud->rv_select_all($this->table, ['*'],  ['IsMarkForDel'=>0], ['AboutId'=>'DESC']);
//        echo "<pre>";print_r($resultset);die;
        $result_destination = $crud->rv_select_all("tb_tbb2c_destinations", ['*'] ,['IsPublish'=>1,'IsMarkForDel'=>0], ['Title'=>'ASC'] );     
              
        # Start : Pagination 
        $page       = $this->_getParam('page', 1);
        $resultset  = Zend_Paginator::factory($resultset);
        $resultset->setItemCountPerPage($this->per_page_record);
        $resultset->setCurrentPageNumber($page);
        # End : Pagination
        $this->view->page  = $page;
        $this->view->per_page_record  = $this->per_page_record;
        $this->view->resultset  = $resultset;
        $this->view->result_destination  = $result_destination;
        $this->view->messages   = $this->_helper->flashMessenger->getMessages();
    }
    
    
    
    /**
    * editpage() method is used to admin can edit cms static page
    * @param password string
    * @return ture 
    */
    
    public function editaboutwheelsAction()
    {
//Check admin logedin or not
        $this->checklogin();
        $crud = new Admin_Model_CRUD();
        $form = new Admin_Form_Editaboutwheels();
        $pId = (int)$this->getRequest()->getParam("id");
        $form->setMethod("POST");
        $form->setAction("admin/aboutwheels/editaboutwheels/id/".$pId);
        
        $form->setName("edit_aboutwheels");
               
        if( $this->getRequest()->isPost() ) {
            $getData = $this->getRequest()->getPost();
          
            if($form->isValid($getData)) {
                
                //-------Start Code for Approve and Publish content---------//
               if(isset($getData['save'])=="Save") {
//                         echo "<pre>";print_r($getData);die;
                   $AboutId = $getData['AboutId'];
               $orignalFIleName = $image = $_FILES["AboutImage"]["name"];

                if (!empty($orignalFIleName)) {
                                 $orignalFolderName  = $_SERVER["DOCUMENT_ROOT"] . "/" . $this->imageUrl ;
                                 $fileExt    = $this->_helper->General->getFileExtension($orignalFIleName);
                                 $fileName   = $this->current_time . '.' . $fileExt;
                                    if (!file_exists($orignalFolderName)) {
                                        mkdir($orignalFolderName, $this->DIR_WRITE_MODE, true);
                                        }
                                  $temp_file_name = $_FILES["AboutImage"]["tmp_name"]; // temprary file name
                                    @move_uploaded_file($temp_file_name, $orignalFolderName . "/" . $fileName);
     
                             }

                            if($fileName!=""){ 
                                $image_edit = $fileName;    
                            }
                
                $editPageData = [                    
                    
                        'AboutTitle' => ($getData['AboutTitle']),                                                               
                        'AboutDescription' => ($getData['AboutDescription']),
                        'UpdateDate'=> date('Y-m-d H:i:s') ,
                        'status' => ($getData['status']),
                ];
                                
                     if( $fileName) {
                        $editPageData['AboutImage'] = $fileName;
                                }
//                  echo "<pre>";print_r($editPageData);die;
                                $crud->rv_update($this->table, $editPageData, ['AboutId =?'=>$AboutId]);
                                $this->view->successMessage ="Page content has been saved successfully.";
                                $this->_helper->flashMessenger->addMessage("Page content has been updated successfully.");
                                $this->_redirect("/admin/aboutwheels/index");
                            }
                           
                       
  
              
             
          }
        }

        $result = $crud->getCmsdata($this->table, ['*'], ['AboutId'=>$pId], ['AboutId'=>'DESC']);
//        echo "<pre>";print_r($result);die;
        $editdata["AboutId"] = @$result->AboutId;
        $editdata["AboutTitle"] = @$result->AboutTitle;
        $editdata["AboutImage"] = @$result->AboutImage;
        $editdata["AboutDescription"] = @$result->AboutDescription;
        $editdata["status"] = @$result->status;
//        echo "<pre>";print_r($editdata);die;
        $form->populate($editdata);        
        $this->view->AboutImage = @$result->AboutImage; 
        $this->view->form = $form;
        
                    
    }
    
     
    public function addaboutwheelsAction()
    {
       
//Check admin logedin or not
        $this->checklogin();
        $crud = new Admin_Model_CRUD();
        $form = new Admin_Form_Editaboutwheels();
        $pId = (int)$this->getRequest()->getParam("id");
        $form->setMethod("POST");
        $form->setAction("admin/aboutwheels/addaboutwheels");
        $form->setName("edit_aboutwheels");
               
        if( $this->getRequest()->isPost() ) {
            $getData = $this->getRequest()->getPost();          
            if($form->isValid($getData)) {
                
                //-------Start Code for Approve and Publish content---------//
               if (isset($getData['save']) == "Save") {
//                echo "<pre>";print_r($getData);die;

                $orignalFIleName = $image = $_FILES["AboutImage"]["name"];


                
                if (!empty($orignalFIleName)) {
                                 $orignalFolderName  = $_SERVER["DOCUMENT_ROOT"] . "/" . $this->imageUrl ;
                                 $fileExt    = $this->_helper->General->getFileExtension($orignalFIleName);
                                 $fileName   = $this->current_time . '.' . $fileExt;
                                    if (!file_exists($orignalFolderName)) {
                                        mkdir($orignalFolderName, $this->DIR_WRITE_MODE, true);
                                        }
                                  $temp_file_name = $_FILES["AboutImage"]["tmp_name"]; // temprary file name
                                    @move_uploaded_file($temp_file_name, $orignalFolderName . "/" . $fileName);
     
                             }

                            if($fileName!=""){ 
                                $image_add = $fileName;    
                            }else {
                                $image_add = "";
                            }
//                $BlogDate1 = explode('/', $getData['BlogDate']);
//                $BlogDate = $BlogDate1[2]."-".$BlogDate1[1]."-".$BlogDate[0];
                
                $savePageData = [                    
                    'AboutTitle' => ($getData['AboutTitle']),
                    'AboutImage' =>   $image_add ,
                    'AboutDescription' => ($getData['AboutDescription']),
                    'CreateDate'=> date('Y-m-d H:i:s') ,
                    'status' => ($getData['status']),
                    'isMarkForDel' => 0,
                ];
//                echo "<pre>";print_r($savePageData);die;
                $crud->rv_insert($this->table, $savePageData);
                $this->view->successMessage = "Page content has been saved successfully.";
                $this->_helper->flashMessenger->addMessage("Page content has been added successfully.");
                $this->_redirect("/admin/aboutwheels/index");
            
             }  
             
          }
        }

         $this->view->form = $form;
        $this->view->messages   = $this->_helper->flashMessenger->getMessages();
        
                    
    }
    
    
    
    public function deleteaboutwheelsAction() {
        $this->checklogin();
        $crud = new Admin_Model_CRUD();
        $tId = (int) $this->getRequest()->getParam("id");
        //echo $tId;die;
        if ($tId) {
            $checkdata = $crud->rv_select_row($this->table, ['AboutId'], ['AboutId' => $tId], ['AboutId' => 'asc']);
            if (count($checkdata) > 0) {
                $crud->rv_update($this->table, ['isMarkForDel'=> 1], ['AboutId =?'=>$tId]);
                $this->_helper->flashMessenger->addMessage("Delete successfully.");
                $this->_redirect("/admin/aboutwheels/index");
            } else {
                die('Oops some thing wrong!!.');
            }
        }
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