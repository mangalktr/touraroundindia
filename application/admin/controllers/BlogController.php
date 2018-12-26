<?php
/***************************************************************
* Catabatic Technology Pvt. Ltd.
* File Name     : StaticpageController.php
* File Desc.    : Staticpage controller managed all staic content pages
* Created By    : Piyush Tiwari <piyush@catpl.co.in>
* Created Date  : 23 May 2018
* Updated Date  : 23 May 2018
***************************************************************/



class Admin_BlogController extends Zend_Controller_Action
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
        $this->imageUrl     = (($this->appmode == 'MODE_BETA') ? 'beta/' : '') . 'public/upload/blog/';

        $this->img_w_small  = 120;
        $this->img_h_small  = 120;

        $this->DIR_WRITE_MODE = 0777;
        
       $this->table =  'tbl_blog';
     
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
        $resultset  = $crud->rv_select_all($this->table, ['*'],  ['IsMarkForDel'=>0], ['BlogId'=>'DESC']);
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
    
    public function editblogAction()
    {
        
//Check admin logedin or not
        $this->checklogin();
        $crud = new Admin_Model_CRUD();
        $form = new Admin_Form_Editblog();
        $pId = (int)$this->getRequest()->getParam("id");
        $form->setMethod("POST");
        $form->setAction("admin/blog/editblog/id/".$pId);
        
        $form->setName("edit_blog");
               
        if( $this->getRequest()->isPost() ) {
            $getData = $this->getRequest()->getPost();
          
            if($form->isValid($getData)) {
                
                //-------Start Code for Approve and Publish content---------//
               if(isset($getData['save'])=="Save") {
//                         echo "<pre>";print_r($getData);
                   $BlogId = $getData['BlogId'];
               $orignalFIleName = $image = $_FILES["BlogImage"]["name"];

                if (!empty($orignalFIleName)) {
                                 $orignalFolderName  = $_SERVER["DOCUMENT_ROOT"] . "/" . $this->imageUrl ;
                                 $fileExt    = $this->_helper->General->getFileExtension($orignalFIleName);
                                 $fileName   = $this->current_time . '.' . $fileExt;
                                    if (!file_exists($orignalFolderName)) {
                                        mkdir($orignalFolderName, $this->DIR_WRITE_MODE, true);
                                        }
                                  $temp_file_name = $_FILES["BlogImage"]["tmp_name"]; // temprary file name
                                    @move_uploaded_file($temp_file_name, $orignalFolderName . "/" . $fileName);
     
                             }

                            if($fileName!=""){ 
                                $image_edit = $fileName;    
                            }
                
                $editPageData = [                    
                    
                        'BlogTitle' => ($getData['BlogTitle']),                       

                        'BlogDate' => ($getData['BlogDate']),
                        'PostedBy' => ($getData['PostedBy']),                     
                        'BlogDescription' => ($getData['BlogDescription']),
                        'UpdateDate'=> date('Y-m-d H:i:s') ,
                        'status' => ($getData['status']),
                ];
                                
                     if( $fileName) {
                        $editPageData['BlogImage'] = $fileName;
                                }
//                  echo "<pre>";print_r($editPageData);die;
                                $crud->rv_update($this->table, $editPageData, ['BlogId =?'=>$BlogId]);
                                $this->view->successMessage ="Page content has been saved successfully.";
                                $this->_helper->flashMessenger->addMessage("Page content has been updated successfully.");
                                $this->_redirect("/admin/blog/index");
                            }
                           
                       
  
              
             
          }
        }

        $result = $crud->getCmsdata($this->table, ['*'], ['BlogId'=>$pId], ['BlogId'=>'DESC']);
//        echo "<pre>";print_r($result);die;
        $editdata["BlogId"] = @$result->BlogId;
        $editdata["BlogTitle"] = @$result->BlogTitle;
         $editdata["BlogDate"] = @$result->BlogDate;
        $editdata["PostedBy"] = @$result->PostedBy;
        $editdata["BlogImage"] = @$result->BlogImage;
        $editdata["BlogDescription"] = @$result->BlogDescription;
        $editdata["status"] = @$result->status;
//        echo "<pre>";print_r($editdata);die;
        $form->populate($editdata);        
        $this->view->BlogImage = @$result->BlogImage; 
        $this->view->form = $form;
        
                    
    }
    
     
    public function addblogAction()
    {
       
//Check admin logedin or not
        $this->checklogin();
        $crud = new Admin_Model_CRUD();
        $form = new Admin_Form_Editblog();
        $pId = (int)$this->getRequest()->getParam("id");
        $form->setMethod("POST");
        $form->setAction("admin/blog/addblog");
        $form->setName("edit_blog");
               
        if( $this->getRequest()->isPost() ) {
            $getData = $this->getRequest()->getPost();          
            if($form->isValid($getData)) {
                
                //-------Start Code for Approve and Publish content---------//
               if (isset($getData['save']) == "Save") {
//                echo "<pre>";print_r($getData);die;

                $orignalFIleName = $image = $_FILES["BlogImage"]["name"];


                
                if (!empty($orignalFIleName)) {
                                 $orignalFolderName  = $_SERVER["DOCUMENT_ROOT"] . "/" . $this->imageUrl ;
                                 $fileExt    = $this->_helper->General->getFileExtension($orignalFIleName);
                                 $fileName   = $this->current_time . '.' . $fileExt;
                                    if (!file_exists($orignalFolderName)) {
                                        mkdir($orignalFolderName, $this->DIR_WRITE_MODE, true);
                                        }
                                  $temp_file_name = $_FILES["BlogImage"]["tmp_name"]; // temprary file name
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
                    'BlogTitle' => ($getData['BlogTitle']),
                    'BlogDate' => ($getData['BlogDate']),
                    'PostedBy' => ($getData['PostedBy']),
                    'BlogImage' =>   $image_add ,
                    'BlogDescription' => ($getData['BlogDescription']),
                    'CreateDate'=> date('Y-m-d H:i:s') ,
                    'status' => ($getData['status']),
                    'isMarkForDel' => 0,
                ];
//                echo "<pre>";print_r($savePageData);die;
                $crud->rv_insert($this->table, $savePageData);
                $this->view->successMessage = "Page content has been saved successfully.";
                $this->_helper->flashMessenger->addMessage("Page content has been added successfully.");
                $this->_redirect("/admin/blog/index");
            
             }  
             
          }
        }

         $this->view->form = $form;
        $this->view->messages   = $this->_helper->flashMessenger->getMessages();
        
                    
    }
    
    
    
    public function deleteblogAction() {
        $this->checklogin();
        $crud = new Admin_Model_CRUD();
        $tId = (int) $this->getRequest()->getParam("id");
        //echo $tId;die;
        if ($tId) {
            $checkdata = $crud->rv_select_row($this->table, ['BlogId'], ['BlogId' => $tId], ['BlogId' => 'asc']);
            if (count($checkdata) > 0) {
                $crud->rv_update($this->table, ['isMarkForDel'=> 1], ['BlogId =?'=>$tId]);
                $this->_helper->flashMessenger->addMessage("Delete successfully.");
                $this->_redirect("/admin/blog/index");
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