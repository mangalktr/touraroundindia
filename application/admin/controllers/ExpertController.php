<?php
/***************************************************************
* Catabatic Technology Pvt. Ltd.
* File Name     : StaticpageController.php
* File Desc.    : Staticpage controller managed all staic content pages
* Created By    : Piyush Tiwari <piyush@catpl.co.in>
* Created Date  : 23 May 2018
* Updated Date  : 23 May 2018
***************************************************************/



class Admin_ExpertController extends Zend_Controller_Action
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
        $this->imageUrl     = (($this->appmode == 'MODE_BETA') ? 'beta/' : '') . 'public/upload/expert/';

        $this->img_w_small  = 120;
        $this->img_h_small  = 120;

        $this->DIR_WRITE_MODE = 0777;
        
       $this->tablename = 'tbl_our_expert';
       $this->tablenameRegion = "tbl_regions";
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
        $resultset  = $crud->rv_select_all($this->tablename, ['*'],['ExpertName'], ['ExpertId'=>'DESC']);
//        echo "<pre>";print_r($resultset);die;
        //Fetch data for the destination
        //$result_destination = $crud->rv_select_all("tb_tbb2c_destinations", ['*'] ,['IsPublish'=>1,'IsMarkForDel'=>0], ['Title'=>'ASC'] );
        $resultRegion = $crud->rv_select_all( $this->tablenameRegion , ['sid','Title','IsActive'] , ['IsMarkForDel'=>0], ['Title'=>'ASC'] );
           
//echo "<pre>";print_r($resultRegion);die;
        
# Start : Pagination 
        $page       = $this->_getParam('page', 1);
        $resultset  = Zend_Paginator::factory($resultset);
        $resultset->setItemCountPerPage($this->per_page_record);
        $resultset->setCurrentPageNumber($page);
        # End : Pagination
        
        //$this->view->result_destination  = $result_destination;
        $this->view->resultset  = $resultset;
        $this->view->resultRegion  = $resultRegion;
        $this->view->messages   = $this->_helper->flashMessenger->getMessages();
    }
    
    
    
    /**
    * editpage() method is used to admin can edit cms static page
    * @param password string
    * @return ture 
    */
    
    public function editexpertAction()
    {
        
//Check admin logedin or not
        $this->checklogin();
        $crud = new Admin_Model_CRUD();
        $form = new Admin_Form_Editexpert();
        $pId = (int)$this->getRequest()->getParam("id");
        $form->setMethod("POST");
        $form->setAction("admin/expert/editexpert/id/".$pId);
        
        $form->setName("edit_expert");
               
        if( $this->getRequest()->isPost() ) {
            $getData = $this->getRequest()->getPost();
          
            if($form->isValid($getData)) {
                //$resultRegions = $crud->rv_select_all( 'tbl_regions' , ['sid','Title','IsActive'] , ['IsMarkForDel'=>0], ['Title'=>'ASC'] );
                $resultRegions = $crud->rv_select_row('tbl_regions', ['Title'], ['sid' => $getData['destination']], ['sid' => 'asc']);
                //echo '<pre>';print_r($resultRegions['Title']);die('up');
                //-------Start Code for Approve and Publish content---------//
               if(isset($getData['save'])=="Save") {
//                        echo "<pre>";print_r($getData);die;
                    //Code for check page alias name already exists or not
                    $page_id = (int) @$getData['ExpertId'];
//                    $checkPageName = $crud->getCmsdata('tbl_static_pages', ['sid'], ['page_name'=>strtoupper($getData['page_name'])], ['sid'=>'asc']);
//                    echo "<pre>";print_r($checkPageName);die;
                    //if(count($checkPageName)<=0){
                           
                            $orignalFIleName = $_FILES['ExpertImage']['name']; 
//                            $ext = @substr($_FILES['ExpertImage']['name'], strrpos($_FILES['ExpertImage']['name'], '.'));
//                            $image = time().$ext;
//                            $upload = new Zend_File_Transfer_Adapter_Http();
//                            $upload->setDestination("public/upload/expert/");
//                            $upload->addFilter('Rename', "public/upload/expert/".$image);
//                            $file = $upload->getFileName();
                            
                            
                             if (!empty($orignalFIleName)) {
                                 $orignalFolderName  = $_SERVER["DOCUMENT_ROOT"] . "/" . $this->imageUrl ;
                                 $fileExt    = $this->_helper->General->getFileExtension($orignalFIleName);
                                 $fileName   = $this->current_time . '.' . $fileExt;
                                 
                                   $originalSmallFolder    = $orignalFolderName. "/small";
                                    if (!file_exists($originalSmallFolder)) {
                                        mkdir($originalSmallFolder, $this->DIR_WRITE_MODE, true);
                                        }
                                  $temp_file_name = $_FILES["ExpertImage"]["tmp_name"]; // temprary file name

                                    @move_uploaded_file($temp_file_name, $orignalFolderName . "/" . $fileName);
                                   
                                     @copy($orignalFolderName . '/' . $fileName, $originalSmallFolder . "/" . $fileName); // copy uploaded file into this location directory
                                    $objImageResize3 = new Catabatic_Imageresize($originalSmallFolder . '/' . $fileName);
                                    $objImageResize3->resizeImage($this->img_w_small, $this->img_h_small, 'exact'); // param : width , height , (exact|portrait|landscape|auto|crop)
                                    $objImageResize3->saveImage($originalSmallFolder . '/' . $fileName);
                                    
                                    
                                    $path_image = "public/upload/expert/".$fileName;
                                    @unlink($path_image);
                                 
                             }
                            
                            
//                            echo $file;die;
//                            $result = $crud->getStaticPageDetailsByPageId($getData['id']);
                            $result = $crud->getCmsdata($this->tablename, ['*'], ['ExpertId'=>$pId], ['ExpertId'=>'DESC']);
                            
                            if($fileName!=""){ 
                                $path_image1 = "public/upload/expert/small/".$result->ExpertImage;
                                @unlink($path_image1);
                                $image_edit = $fileName;    
                            }else {
                                $image_edit = $result->ExpertImage;
                            }

                            try {
//                                $upload->receive();

                                $page_id = $getData['ExpertId'];

                                $editPageData = [
                                    'ExpertDestination'=>$getData['destination'],
                                    'ExpertDestinationTitle'=> isset($resultRegions['Title'])?$resultRegions['Title']:'',
                                    'ExpertName'=>$getData['ExpertName'],
                                    'ExpertEmail'=>$getData['ExpertEmail'],
                                    'ExpertPhone'=>$getData['ExpertPhone'],
                                    'ExpertImage'=>$image_edit,
                                     'ExpertDesig'=>$getData['ExpertDesig'],
                                    'ExpertDescription'=>$getData['ExpertDescription'],
                                    ];
                                //echo '<pre>';print_r($editPageData);die('up');
                                $crud->rv_update($this->tablename, $editPageData, ['ExpertId =?'=>$page_id]);
                                $this->view->successMessage ="Page content has been saved successfully.";
                                $this->_helper->flashMessenger->addMessage("Page content has been updated successfully.");
                                $this->_redirect("/admin/expert/index");
                            }
                            catch (Zend_File_Transfer_Exception $e) {
                                  $e->getMessage();
                            }
                       
//                    }else {
//                        $this->view->errorMessage ="Page name already exists, please choose another name"; 
//                    }     
             }  
             
          }
        }

        $result = $crud->getCmsdata($this->tablename, ['*'], ['ExpertId'=>$pId], ['ExpertId'=>'DESC']);
//        echo "<pre>";print_r($result);die;
        $editdata["ExpertId"] = @$result->ExpertId;
        $editdata["destination"] = @$result->ExpertDestination;
        $editdata["ExpertName"] = @$result->ExpertName;
        $editdata["ExpertEmail"] = @$result->ExpertEmail;
        $editdata["ExpertPhone"] = @$result->ExpertPhone;        
        $editdata["ExpertImage"] = @$result->ExpertImage;
        $editdata["ExpertDesig"] = @$result->ExpertDesig;
        $editdata["ExpertDescription"] = @$result->ExpertDescription;
//        $editdata["status"] = @$result->status;
//        echo "<pre>";print_r($editdata);die;
        $form->populate($editdata);
        
        $this->view->ExpertImage = @$result->ExpertImage; 
        $this->view->form = $form;
        
                    
    }
    
     
    public function addexpertAction()
    {
       
//Check admin logedin or not
        $this->checklogin();
        $crud = new Admin_Model_CRUD();
        $form = new Admin_Form_Addexpert();
        $pId = (int)$this->getRequest()->getParam("id");
        $form->setMethod("POST");
        $form->setAction("admin/expert/addexpert");
        $form->setName("add_expert");
               
        if( $this->getRequest()->isPost() ) {
            $getData = $this->getRequest()->getPost();
          
            if($form->isValid($getData)) {
              $resultRegions = $crud->rv_select_row('tbl_regions', ['Title'], ['sid' => $getData['destination']], ['sid' => 'asc']);
              $checkexpert = $crud->rv_select_row('tbl_our_expert', ['ExpertDestination'], ['ExpertDestination' => $getData['destination']], ['ExpertDestination' => 'asc']);
              if($checkexpert['ExpertDestination'] != '' && $checkexpert['ExpertDestination']>0){
                    $this->view->successMessage ="In this destination has already expert added please select another destination";
                    $this->_helper->flashMessenger->addMessage("In this destination has already expert added please select another destination.");
                    $this->_redirect("/admin/expert/addexpert");
                    
              }
              //echo '<pre>';print_r($resultRegions['Title']);die('up');
                //-------Start Code for Approve and Publish content---------//
               if(isset($getData['save'])=="Save") {
//                     echo "<pre>";print_r($getData);die;
                    //Code for check page alias name already exists or not
                    $page_id = (int) @$getData['ExpertId'];
//                    $checkPageName = $crud->getCmsdata('tbl_static_pages', ['ExpertId'], ['sid'=>'asc']);
//                    echo "<pre>";print_r($checkPageName);die;
//                    if(count($checkPageName)<=0){
                            
                            $orignalFIleName = $image =$_FILES['ExpertImage']['name']; 
//                            $ext = @substr($_FILES['ExpertImage']['name'], strrpos($_FILES['ExpertImage']['name'], '.'));
//                            $image = time().$ext;
//                            $upload = new Zend_File_Transfer_Adapter_Http();
//                            $upload->setDestination("public/upload/expert/");
//                            $upload->addFilter('Rename', "public/upload/expert/".$image);
//                            $file = $upload->getFileName();
                            
                            
                             if (!empty($orignalFIleName)) {
                                 $orignalFolderName  = $_SERVER["DOCUMENT_ROOT"] . "/" . $this->imageUrl ;
                                 $fileExt    = $this->_helper->General->getFileExtension($orignalFIleName);
                                 $fileName   = $this->current_time . '.' . $fileExt;
                                 
                                   $originalSmallFolder    = $orignalFolderName. "/small";
                                    if (!file_exists($originalSmallFolder)) {
                                        mkdir($originalSmallFolder, $this->DIR_WRITE_MODE, true);
                                        }
                                  $temp_file_name = $_FILES["ExpertImage"]["tmp_name"]; // temprary file name

                                    @move_uploaded_file($temp_file_name, $orignalFolderName . "/" . $fileName);
                                   
                                     @copy($orignalFolderName . '/' . $fileName, $originalSmallFolder . "/" . $fileName); // copy uploaded file into this location directory
                                    $objImageResize3 = new Catabatic_Imageresize($originalSmallFolder . '/' . $fileName);
                                    $objImageResize3->resizeImage($this->img_w_small, $this->img_h_small, 'exact'); // param : width , height , (exact|portrait|landscape|auto|crop)
                                    $objImageResize3->saveImage($originalSmallFolder . '/' . $fileName);
                                    
                                    $path_image = "public/upload/expert/".$fileName;
                                    @unlink($path_image);
                                    
                                 
                             }
                          
                            
//                            echo $file;die;
//                            $result = $crud->getStaticPageDetailsByPageId($getData['id']);
//                            $result = $crud->getCmsdata($this->tablename, ['*'], ['sid'=>$pId], ['sid'=>'DESC']);
                            
                            if($fileName!=""){ 
//                                $path_image = "public/upload/expert/".$result->ExpertImage;
//                                @unlink($path_image);
                                $image_edit = $fileName;    
                            }else {
                                $image_edit = "";
//                                $image_edit = $result->ExpertImage;
                            }

                            try {
//                                $upload->receive();

                                $ExpertId = $getData['ExpertId'];

                                $editPageData = [
                                    'ExpertDestination'=>$getData['destination'],
                                    'ExpertDestinationTitle'=> isset($resultRegions['Title'])?$resultRegions['Title']:'',
                                    'ExpertName'=>$getData['ExpertName'],
                                    'ExpertEmail'=>$getData['ExpertEmail'],
                                    'ExpertPhone'=>$getData['ExpertPhone'],
                                   'ExpertImage'=>$image_edit,
                                   
                                    'ExpertDesig'=>$getData['ExpertDesig'],
                                    'ExpertDescription'=>$getData['ExpertDescription'],
                                    
                                    ];
//                                 echo "<pre>";print_r($editPageData);die;
                                $crud->rv_insert($this->tablename, $editPageData);
                                $this->view->successMessage ="Page content has been saved successfully.";
                                $this->_helper->flashMessenger->addMessage("Page content has been added successfully.");
                                $this->_redirect("/admin/expert/index");
                            }
                            catch (Zend_File_Transfer_Exception $e) {
                                  $e->getMessage();
                            }
                       
//                    }else {
//                        $this->view->errorMessage ="Page name already exists, please choose another name"; 
//                    }     
             }  
             
          }
        }

         $this->view->form = $form;
        $this->view->messages   = $this->_helper->flashMessenger->getMessages();
        
                    
    }
    
//    function createPreview($image_path, $filename) {
//
//    header('Content-Type: image/jpeg');
//    $thumb = imagecreatetruecolor(64, 64);
//    $source = imagecreatefromjpeg($image_path);
//
//    list($width, $height) = getimagesize($image_path);
//
//    imagecopyresized($thumb, $source, 0, 0, 0, 0, 64, 64, $width, $height);
//
//    imagejpeg($thumb, "images/".$filename."_prev.jpg");
////    header("Location:index.php");
//}
    
    public function deleteexpertAction() {
        $this->checklogin();
        $crud = new Admin_Model_CRUD();
        $tId = (int) $this->getRequest()->getParam("id");
        //echo $tId;die;
        if ($tId) {
            $checkdata = $crud->rv_select_row($this->tablename, ['ExpertId'], ['ExpertId' => $tId], ['ExpertId' => 'asc']);
            if (count($checkdata) > 0) {
                $crud->rv_delete($this->tablename, ['	ExpertId =?' => $tId]);
                $this->_helper->flashMessenger->addMessage("Delete successfully.");
                $this->_redirect("/admin/expert/index");
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