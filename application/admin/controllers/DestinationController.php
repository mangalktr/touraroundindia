<?php

/* * *************************************************************
 * Catabatic Technology Pvt. Ltd.
 * File Name     : ContactusController.php
 * File Desc.    : Contactus controller managed all contact queries
 * Created By    : Piyush Tiwari <piyush@catpl.co.in>
 * Created Date  : 23 May 2018
 * Updated Date  : 23 May 2018
 * ************************************************************* */

class Admin_DestinationController extends Zend_Controller_Action {

    public $dbAdapter;
    public $perPageLimit;
    public $siteurl;
    public $DIR_WRITE_MODE;
    protected $tablenameRegion;

    public function init() {
        /* Initialize db and session access */
        $aConfig = $this->getInvokeArg('bootstrap')->getOptions();
        $this->siteurl = $aConfig['bootstrap']['siteUrl'];
        $this->appmode = $aConfig['bootstrap']['appmode'];
        
        $this->per_page_record = 25;
        $this->dbAdapter = Zend_Db_Table::getDefaultAdapter();

        $auth = Zend_Auth::getInstance();
        $authStorage = $auth->getStorage()->read();
        $this->username = $authStorage->username;
        $this->admin_type = $authStorage->role;
        
        
        
        $this->current_time = time();
        $this->imageUrl     = (($this->appmode == 'MODE_BETA') ? 'beta/' : '') . 'public/upload/destinations/';
        $this->regionImageUrl     = (($this->appmode == 'MODE_BETA') ? 'beta/' : '') . 'public/upload/region/';
        
        $this->DIR_WRITE_MODE = 0777;

        $this->img_w_thumb  = 99;
        $this->img_h_thumb  = 100;
        
        $this->img_w_medium = 254;
        $this->img_h_medium = 240;

        $this->img_w_large  = 254;
        $this->img_h_large  = 309;
        
        
        $this->banner_w_thumb  = 256;
        $this->banner_h_thumb  = 64;
        
        $this->banner_w_large  = 1349;
        $this->banner_h_large  = 320;
        $this->img_w_regionsmall  = 120;
        $this->img_h_regionsmall  = 233;
        
        $this->tablenameRegion = "tbl_regions";
        
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
        if($this->getRequest()->isPost())
        {
        $getData = $this->getRequest()->getPost();
        $searchArr = array(
                        'Title'=>$getData['Title'],
                        'Countries'=>$getData['Country'],
                        'Region'=>$getData['Region'],
                        'rows'=>$getData['rows'],
                        'page'=>$getData['page'],
            );
        $resulsetold = $crud->getCount('tb_tbb2c_destinations',['tbl.IsPublish' => 1,'tbl.IsMarkForDel'=>0],'DesSysId');       
        $crud->searchArr = $searchArr;
        $resultset = $crud->getDestinationsIndex(['tbl.IsMarkForDel'=>0] , ['Title'=> 'ASC']); 

        $result1 = array();
        if (count($resultset) > 0) {
                foreach ($resultset as $resultkey => $resultval) {
                    $result1[] = [
                        'DesSysId'=>$resultval['DesSysId'],
                        'Title' => $resultval['Title'],
                        'region_name' => $resultval['region_name'],
                        'Countries' => $resultval['Countries'],
                        'Activities' => $resultval['Activities'],
                        'Tours' => $resultval['Tours'],
                        'Hotels' => $resultval['Hotels'],
                        'IsFeatured' => $resultval['IsFeatured'],
                        'DisplayOnFooter' => $resultval['DisplayOnFooter'],
                        'DisplayOnHeader' => isset($resultval['region_name'])&&!empty($resultval['region_name'])?$resultval['DisplayOnHeader']:"",
                        'IsActive' => $resultval['IsActive']==1?'Active':'Deactive',
                        'Image' => $resultval['Image'],
                        'Bannerimg' => $resultval['Bannerimg'],
                    ];
                }
            }
        $result = Zend_Json::encode($result1);
        $newResult = Zend_Json::decode($result,false);     
        $finalResult["total"]=$resulsetold[0]['DesSysId'];
        $finalResult["rows"]=$newResult;
        echo json_encode($finalResult);
        exit;        
        }
    }

    public function editdestiAction() {

        $this->checklogin();
        $crud = new Admin_Model_CRUD();
        $form = new Admin_Form_Editdestinationpage();
        $pId = (int) $this->getRequest()->getParam("id");
        $page = ($this->getRequest()->getParam("page")) ? $this->getRequest()->getParam("page") : 1;
        $form->setMethod("POST");
        $form->setAction("admin/destination/editdesti/id/" . $pId . "/page/$page");
        $form->setName("edit_destination_page");

        if ($this->getRequest()->isPost()) {
            $getData = $this->getRequest()->getPost();
//            echo "<pre>";print_r($getData);die;
            if ($form->isValid($getData)) {

                //-------Start Code for Approve and Publish content---------//
                if (isset($getData['save']) == "Save") {
//                    echo "<pre>";print_r($getData);die;
                     $target_dir = "public/upload/destinations/";
                $target_file = $target_dir . basename($_FILES["image"]["name"]);
                $target_file1 = $target_dir . basename($_FILES["banner_image"]["name"]);
                $uploadOk = 1;
                
                $orignalFileName = $image = $_FILES['image']['name'];
                $bannerimage = $_FILES['banner_image']['name'];


                /*  * ****************** Starts : destinations Image upload here **************** */
                if (!empty($orignalFileName)) {

                    $orignalFolderName  = $_SERVER["DOCUMENT_ROOT"] . "/" . $this->imageUrl . $pId . "/images"; // root folder for destination images


                    /* Get File Extension */
                    $fileExt    = $this->_helper->General->getFileExtension($orignalFileName);
                    $fileName   = $pId . '_' . $this->current_time . '.' . $fileExt;

                    $originalThumbFolder    = $orignalFolderName. "/thumb";
                    $originalMediumFolder   = $orignalFolderName. "/medium";
                    $originalLargeFolder    = $orignalFolderName. "/large";
                    $originalHomeFolder    = $orignalFolderName. "/home";


                    /* Create directory if not exists */
                    if (!file_exists($orignalFolderName)) {
                        mkdir($orignalFolderName, $this->DIR_WRITE_MODE, true);
                    }
                    if (!file_exists($originalThumbFolder)) {
                        mkdir($originalThumbFolder, $this->DIR_WRITE_MODE, true);
                    }
                    if (!file_exists($originalMediumFolder)) {
                        mkdir($originalMediumFolder, $this->DIR_WRITE_MODE, true);
                    }
                    if (!file_exists($originalLargeFolder)) {
                        mkdir($originalLargeFolder, $this->DIR_WRITE_MODE, true);
                    }
                    if (!file_exists($originalHomeFolder)) {
                        mkdir($originalHomeFolder, $this->DIR_WRITE_MODE, true);
                    }

                    $temp_file_name = $_FILES["image"]["tmp_name"]; // temprary file name

                    @move_uploaded_file($temp_file_name, $orignalFolderName . "/" . $fileName);

                    @copy($orignalFolderName . '/' . $fileName, $originalLargeFolder . "/" . $fileName); // copy uploaded file into this location directory
                    $objImageResize4 = new Catabatic_Imageresize($originalLargeFolder . '/' . $fileName);
                    $objImageResize4->resizeImage($this->img_w_large, $this->img_h_large, 'exact'); // param : width , height , (exact|portrait|landscape|auto|crop)
                    $objImageResize4->saveImage($originalLargeFolder . '/' . $fileName);
                    
                    @copy($originalLargeFolder . '/' . $fileName, $originalThumbFolder . "/" . $fileName); // copy uploaded file into this location directory
                    $objImageResize1 = new Catabatic_Imageresize($originalThumbFolder . '/' . $fileName);
                    $objImageResize1->resizeImage($this->img_w_thumb, $this->img_h_thumb, 'exact'); // param : width , height , (exact|portrait|landscape|auto|crop)
                    $objImageResize1->saveImage($originalThumbFolder . '/' . $fileName);

                    @copy($originalLargeFolder . '/' . $fileName, $originalMediumFolder . "/" . $fileName); // copy uploaded file into this location directory
                    $objImageResize2 = new Catabatic_Imageresize($originalMediumFolder . '/' . $fileName);
                    $objImageResize2->resizeImage($this->img_w_medium, $this->img_h_medium, 'exact'); // param : width , height , (exact|portrait|landscape|auto|crop)
                    $objImageResize2->saveImage($originalMediumFolder . '/' . $fileName);
                    
                    @copy($originalLargeFolder . '/' . $fileName, $originalHomeFolder . "/" . $fileName); // copy uploaded file into this location directory
                    $objImageResize2 = new Catabatic_Imageresize($originalHomeFolder . '/' . $fileName);
                    $objImageResize2->resizeImage(120, 233, 'exact'); // param : width , height , (exact|portrait|landscape|auto|crop)
                    $objImageResize2->saveImage($originalHomeFolder . '/' . $fileName);

                }

                /*  * ****************** End : destinations Image upload here **************** */
                    

                /*  * ****************** Starts : destinations Image upload here **************** */
                if (!empty($bannerimage)) {

                    $orignalFolderName  = $_SERVER["DOCUMENT_ROOT"] . "/" . $this->imageUrl . $pId . "/banner"; // root folder for destination images


                    /* Get File Extension */
                    $fileExt    = $this->_helper->General->getFileExtension($bannerimage);
                    $fileNameBanner   = $pId . '_' . $this->current_time . '.' . $fileExt;

                    $originalThumbFolder    = $orignalFolderName. "/thumb";
                    $originalLargeFolder    = $orignalFolderName. "/large";


                    /* Create directory if not exists */
                    if (!file_exists($orignalFolderName)) {
                        mkdir($orignalFolderName, $this->DIR_WRITE_MODE, true);
                    }
                    if (!file_exists($originalThumbFolder)) {
                        mkdir($originalThumbFolder, $this->DIR_WRITE_MODE, true);
                    }
                    if (!file_exists($originalLargeFolder)) {
                        mkdir($originalLargeFolder, $this->DIR_WRITE_MODE, true);
                    }

                    $temp_file_name = $_FILES["banner_image"]["tmp_name"]; // temprary file name

                    @move_uploaded_file($temp_file_name, $orignalFolderName . "/" . $fileNameBanner);

                    @copy($orignalFolderName . '/' . $fileNameBanner, $originalLargeFolder . "/" . $fileNameBanner); // copy uploaded file into this location directory
                    $objImageResize4 = new Catabatic_Imageresize($originalLargeFolder . '/' . $fileNameBanner);
                    $objImageResize4->resizeImage($this->banner_w_large, $this->banner_h_large, 'exact'); // param : width , height , (exact|portrait|landscape|auto|crop)
                    $objImageResize4->saveImage($originalLargeFolder . '/' . $fileNameBanner);
                    
                    @copy($originalLargeFolder . '/' . $fileNameBanner, $originalThumbFolder . "/" . $fileNameBanner); // copy uploaded file into this location directory
                    $objImageResize1 = new Catabatic_Imageresize($originalThumbFolder . '/' . $fileNameBanner);
                    $objImageResize1->resizeImage($this->banner_w_thumb, $this->banner_h_thumb, 'exact'); // param : width , height , (exact|portrait|landscape|auto|crop)
                    $objImageResize1->saveImage($originalThumbFolder . '/' . $fileNameBanner);

                }

                /*  * ****************** End : destinations Image upload here **************** */
                    

                    $editDestinationData = [
                        'region_id' => ($getData['region_id']),
                        'Title' => ($getData['title']),
                        'Activities' => ($getData['activities']),
                        'Tours' => ($getData['tours']),
                        'Hotels' => ($getData['hotel']),
                        'Countries' => ($getData['countries']),
                        'IsActive' => ($getData['status']),
                        'IsFeatured' => ($getData['feature']),
                    ];
                    
                    if( $image) 
                        $editDestinationData['Image'] = $fileName;
                    
                    if( $bannerimage) 
                        $editDestinationData['Bannerimg'] = $fileNameBanner;
                    
                    //echo "<pre>";print_r($editDestinationData);die;
                    $crud->rv_update('tb_tbb2c_destinations', $editDestinationData, ['DesSysId =?' => $pId]);
                    $this->view->successMessage = "Destination has been Updated successfully.";
                    $this->_helper->flashMessenger->addMessage("Destination has been updated successfully.");
                    $this->_redirect("/admin/destination/index/page/$page");
                }
            }
        }


//        $result = $crud->getCmsdata('tb_tbb2c_destinations', ['*'], ['DesSysId' => $pId], ['DesSysId' => 'DESC']);
        $result = $crud->getDestinations( ['tbl.IsPublish'=>1,'tbl.IsMarkForDel'=>0 ,'DesSysId' => $pId ], ['tbl.DesSysId'=>'DESC'] );
//        echo "<pre>";print_r($result);die;
        $result = $result[0];
        
        $this->view->pId = $pId;

        $editdata["id"] = $result['DesSysId'];
        $editdata["title"] = $result['Title'];
        $editdata["activities"] = $result['Activities'];
        $editdata["tours"] = $result['Tours'];
        $editdata["hotel"] = $result['Hotels'];
        $editdata["image"] = $result['Image'];
        $editdata["banner_image"] = $result['Bannerimg'];
        $editdata["countries"] = $result['Countries'];
        $editdata["feature"] = $result['IsFeatured'];
        $editdata["status"] = $result['IsActive'];
        $editdata["region_id"] = $result['region_id'];
        $form->populate($editdata);
        $this->view->image = $result['Image'];
        $this->view->banner_image = $result['Bannerimg'];
        $this->view->form = $form;
        $this->view->page = $page;
    }

    public function adddestinationAction() {
        $this->checklogin();
        $crud = new Admin_Model_CRUD();
        $form = new Admin_Form_Adddestination();
        $tId = (int) $this->getRequest()->getParam("id");
        $form->setAction("admin/destination/adddestination");
        $form->setMethod("POST");
        $form->setName("add_destination");

        if ($this->getRequest()->isPost()) {
            $getData = $this->getRequest()->getPost();

            if (isset($getData['save']) == "Save") {
//                echo "<pre>";print_r($getData);die;
//                            $image = $_FILES['image']['name']; 
                $target_dir = "public/upload/destinations/";
                $target_file = $target_dir . basename($_FILES["image"]["name"]);
                $target_file1 = $target_dir . basename($_FILES["banner_image"]["name"]);
                $uploadOk = 1;
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    "The file " . basename($_FILES["image"]["name"]) . " has been uploaded.";
                }
                if (move_uploaded_file($_FILES["banner_image"]["tmp_name"], $target_file1)) {
                     "The file " . basename($_FILES["banner_image"]["name"]) . " has been uploaded.";
                }
                $image = $_FILES["image"]["name"];

                $bannerimage = $_FILES['banner_image']['name'];

                $savePageData = [
                    'region_id' => ($getData['region_id']),
                    'Title' => ($getData['title']),
                    'Activities' => 0,
                    'Tours' => 0,
                    'Hotels' => 0,
                    'Image' => '',
                    'Bannerimg' => '',
                    'Countries' => '',
                    'IsPublish' => 1,
                    'IsActive' => ($getData['status']),
                    'IsFeatured' => ($getData['feature']),
                    'IsMarkForDel' => 0,
                ];
//                echo "<pre>";print_r($savePageData);die;
                $crud->rv_insert('tb_tbb2c_destinations', $savePageData);
                $this->view->successMessage = "Destination has been saved successfully.";
                $this->_helper->flashMessenger->addMessage("Destination has been added successfully.");
                $this->_redirect("/admin/destination/index");
            }
        }

        $this->view->form = $form;
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
    }

    public function deletedestiAction() {
        $this->checklogin();
        $crud = new Admin_Model_CRUD();
        $tId = (int) $this->getRequest()->getParam("id");
        //echo $tId;die;
        if ($tId) {
            $checkdata = $crud->rv_select_row('tb_tbb2c_destinations', ['DesSysId'], ['DesSysId' => $tId], ['DesSysId' => 'asc']);
            if (count($checkdata) > 0) {
                $crud->rv_delete('tb_tbb2c_destinations', ['DesSysId =?' => $tId]);
                $this->_helper->flashMessenger->addMessage("Deleted successfully.");
                $this->_redirect("/admin/destination/index");
            } else {
                die('Oops some thing wrong!!.');
            }
        }
    }

    public function activeAction() {
        $this->checklogin();
        $crud = new Admin_Model_CRUD();
        $tId = (int) $this->getRequest()->getParam("id");
        $val = (int) $this->getRequest()->getParam("val");
        if ($tId) {
            try {
                $updatedata = [
                    'IsFeatured' => $val
                ];
                $result = $crud->rv_update('tb_tbb2c_destinations', $updatedata, ['DesSysId =?' => $tId]);
//                $resultset = $crud->rv_select_all("tb_tbb2c_destinations", ['*'] ,['IsFeatured'=>1,'IsPublish'=>1,'IsMarkForDel'=>0], ['Title'=>'ASC'] );
//                if(count($resultset)>6){
//                   $result_message = ['status' => false, 'message' => 'Limit Exceed!!'];
//                    echo Zend_Json::encode($result_message);
//                    $updatedata = [
//                    'IsFeatured' => 0
//                ];
//                    $result = $crud->rv_update('tb_tbb2c_destinations', $updatedata, ['DesSysId =?' => $tId]);
//                    exit; 
//                }
                if (!$result) {
                    $result_message = ['status' => false, 'message' => 'Oops something wrong!!'];
                    echo Zend_Json::encode($result_message);
                    exit;
                } else {
                    $result_message = ['status' => true, 'message' => 'Active successfully'];
                    echo Zend_Json::encode($result_message);
                    exit;
                }
            } catch (Exception $ex) {
                $ex->getMessage();
            }
        }
    }
    
    
    public function displayAction() {
        $this->checklogin();
        $crud = new Admin_Model_CRUD();
        $tId = (int) $this->getRequest()->getParam("id");
        $val = (int) $this->getRequest()->getParam("val");
        if ($tId) {
            try {
                $updatedata = [
                    'DisplayOnFooter' => $val
                ];
                $result = $crud->rv_update('tb_tbb2c_destinations', $updatedata, ['DesSysId =?' => $tId]);
                $resultset = $crud->rv_select_all("tb_tbb2c_destinations", ['*'] ,['DisplayOnFooter'=>1,'IsPublish'=>1,'IsMarkForDel'=>0], ['Title'=>'ASC'] );
                if(count($resultset)>12){
                   $result_message = ['status' => false, 'message' => 'Limit Exceed!!'];
                    echo Zend_Json::encode($result_message);
                    $updatedata = [
                    'DisplayOnFooter' => 0
                ];
                    $result = $crud->rv_update('tb_tbb2c_destinations', $updatedata, ['DesSysId =?' => $tId]);
                    exit; 
                }
                if (!$result) {
                    $result_message = ['status' => false, 'message' => 'Oops something wrong!!'];
                    echo Zend_Json::encode($result_message);
                    exit;
                }
                
                else {
                    $result_message = ['status' => true, 'message' => 'Active successfully'];
                    echo Zend_Json::encode($result_message);
                    exit;
                }
            } catch (Exception $ex) {
                $ex->getMessage();
            }
        }
    }
    
    public function displayHeaderAction() {
        $this->checklogin();
        $crud = new Admin_Model_CRUD();
        $tId = (int) $this->getRequest()->getParam("id");
        $val = (int) $this->getRequest()->getParam("val");
        if ($tId) {
            try {
                $updatedata = [
                    'DisplayOnHeader' => $val
                ];
                $result = $crud->rv_update('tb_tbb2c_destinations', $updatedata, ['DesSysId =?' => $tId]);

                if (!$result) {
                    $result_message = ['status' => false, 'message' => 'Oops something wrong!!'];
                    echo Zend_Json::encode($result_message);
                    exit;
                }
                
                else {
                    $result_message = ['status' => true, 'message' => 'Active successfully'];
                    echo Zend_Json::encode($result_message);
                    exit;
                }
            } catch (Exception $ex) {
                $ex->getMessage();
            }
        }
    }

    
    
    
    /**
     * regionAction() method is used to list all regions
     * @param Null
     * @return Array 
     */
    
    public function regionAction() {

        //Check admin logedin or not
        $this->checklogin();
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
        $crud   = new Admin_Model_CRUD();
        $getData = array();
        $results = array();
        if($this->getRequest()->isPost())
        {
        $getData = $this->getRequest()->getPost();
        $searchArr = array(
//                        'Title'=>$getData['Title'],
                        'rows'=>$getData['rows'],
                        'page'=>$getData['page'],
            );
        $resulsetold = $crud->getCount($this->tablenameRegion,['IsMarkForDel'=>0],'sid');  
        $crud->searchArr = $searchArr;
        $resultset = $crud->rv_select_all($this->tablenameRegion,['sid','title','IsActive'],['IsMarkForDel'=>0],['Title'=>'ASC'] );
            if (count($resultset) > 0) {
                foreach ($resultset as $resultkey => $resultval) {
                    $results[] = [
                        'sid' => $resultval['sid'],     
                        'IsActive' => $resultval['IsActive']==1?'Active':'Deactive',
                        'Title' => $resultval['title'],
                    ];
                }
            }
        $result = Zend_Json::encode($results);
        $newResult = Zend_Json::decode($result,false);     
        $finalResult["total"]=$resulsetold[0]['sid'];
        $finalResult["rows"]=$newResult;
        echo json_encode($finalResult);
        exit;        
        }
        
    }

      /**
     * addregionAction() method is used to add regions 
     * @param array
     * @return boolean
     */
    public function addregionAction() {
        $this->checklogin();
        $crud = new Admin_Model_CRUD();
        $form = new Admin_Form_Addregion();
        $tId = (int) $this->getRequest()->getParam("id");
        $form->setAction("admin/destination/addregion");
        $form->setMethod("POST");
        $form->setName("add_region");

        if ($this->getRequest()->isPost()) {
            $getData = $this->getRequest()->getPost();

            if (isset($getData['save']) == "Save") {
                $savePageData = [ 'title' => ($getData['title']), 'IsActive' => ($getData['status']) ];
                $crud->rv_insert( $this->tablenameRegion , $savePageData);
                $this->view->successMessage = "Region has been saved successfully.";
                $this->_helper->flashMessenger->addMessage("Region has been saved successfully.");
                $this->_redirect("/admin/destination/region");
            }
        }

        $this->view->form = $form;
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
    }

    
      /**
     * editregionAction() method is used to edit regions 
     * @param array
     * @return boolean
     */
    public function editregionAction() {

        $this->checklogin();
        $crud = new Admin_Model_CRUD();
        $form = new Admin_Form_Editregion();
        $pId = (int) $this->getRequest()->getParam("id");
        $form->setMethod("POST");
        $form->setAction("admin/destination/editregion/id/" . $pId);
        $form->setName("edit_region");

        if ($this->getRequest()->isPost()) {
            $getData = $this->getRequest()->getPost();
            //echo "<pre>";print_r($getData);die;
            if ($form->isValid($getData)) {

                //-------Start Code for Approve and Publish content---------//
                if (isset($getData['save']) == "Save") {
                    $orignalFIleName = $_FILES['image']['name']; 
                    
                    if (!empty($orignalFIleName)) {
                                 $orignalFolderName  = $_SERVER["DOCUMENT_ROOT"] . "/" . $this->regionImageUrl ;
                                 $fileExt    = $this->_helper->General->getFileExtension($orignalFIleName);
                                 $fileName   = $this->current_time . '.' . $fileExt;
                                 
                                   $originalSmallFolder    = $orignalFolderName. "/small";
                                    if (!file_exists($originalSmallFolder)) {
                                        mkdir($originalSmallFolder, $this->DIR_WRITE_MODE, true);
                                        }
                                  $temp_file_name = $_FILES["image"]["tmp_name"]; // temprary file name

                                    @move_uploaded_file($temp_file_name, $orignalFolderName . "/" . $fileName);
                                   
                                     @copy($orignalFolderName . '/' . $fileName, $originalSmallFolder . "/" . $fileName); // copy uploaded file into this location directory
                                    $objImageResize3 = new Catabatic_Imageresize($originalSmallFolder . '/' . $fileName);
                                    $objImageResize3->resizeImage($this->img_w_regionsmall, $this->img_h_regionsmall, 'exact'); // param : width , height , (exact|portrait|landscape|auto|crop)
                                    $objImageResize3->saveImage($originalSmallFolder . '/' . $fileName);

                                 
                             }
                    
                    
                    
                    $editDestinationData = [
                        'Title' => ($getData['title']),
                        'UpdateDate' => date("Y-m-d H:i:s"),
                        'IsActive' => ($getData['status']),
                    ];
                    if($fileName!=""){ 
                        $editDestinationData['image'] = $fileName;    
                    }
                    //echo "<pre>";print_r($editDestinationData);die;
                    $crud->rv_update( $this->tablenameRegion , $editDestinationData, ['sid =?' => $pId]);
                    $this->view->successMessage = "Region has been saved successfully.";
                    $this->_helper->flashMessenger->addMessage("Region has been updated successfully.");
                    $this->_redirect("/admin/destination/region");
                }
            }
        }


        $result = $crud->getCmsdata( $this->tablenameRegion , ['title','sid','IsActive','image'], ['sid' => $pId], ['sid' => 'DESC']);
//        echo "<pre>";print_r($result);die;
        
        $this->view->pId = $pId;

        $editdata["sid"] = @$result->sid;
        $editdata["title"] = @$result->title;
        $editdata["status"] = @$result->IsActive;
        $editdata["image"] = @$result->image;
        $this->view->image = @$result->image;
//        echo "<pre>";print_r($editdata);die;
        $form->populate($editdata);
        $this->view->form = $form;
    }

  
      /**
     * deleteregionAction() method is used to delete regions 
     * @param array
     * @return boolean
     */
    public function deleteregionAction() {
        $this->checklogin();
        $crud = new Admin_Model_CRUD();
        $tId = (int) $this->getRequest()->getParam("id");
        //echo $tId;die;
        if ($tId) {
            $checkdata = $crud->rv_select_row( $this->tablenameRegion , ['sid'], ['sid' => $tId], ['sid' => 'asc']);
            if (count($checkdata) > 0) {
                $crud->rv_update( $this->tablenameRegion , ['IsMarkForDel' => 1] ,  ['sid =?' => $tId] );
                $this->_helper->flashMessenger->addMessage("Deleted successfully.");
                $this->_redirect("/admin/destination/region");
            } else {
                die('Oops some thing wrong!!.');
            }
        }
    }

    
    public function sanitize_data($string) {
        $searchArr = array("iframe", "script", "document", "write", "alert", "%", "@", "$", ";", "+", "|", "#", "<", ">", ")", "(", "'", "\'", ",", "and ", " &", "& ", "and", " and", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
        $input_data = strtolower($string);
        $input_data = str_replace($searchArr, "", $input_data);

        $input_data = str_replace(" ", "-", $input_data);
        //echo $input_data; die;
        return $input_data;
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
