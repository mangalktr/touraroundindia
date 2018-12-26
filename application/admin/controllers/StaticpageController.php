<?php

/* * *************************************************************
 * Catabatic Technology Pvt. Ltd.
 * File Name     : StaticpageController.php
 * File Desc.    : Staticpage controller managed all staic content pages
 * Created By    : Piyush Tiwari <piyush@catpl.co.in>
 * Created Date  : 23 May 2018
 * Updated Date  : 23 May 2018
 * ************************************************************* */

class Admin_StaticpageController extends Zend_Controller_Action {

    public $per_page_record;

    public function init() {
        /* Initialize db and session access */
        $aConfig = $this->getInvokeArg('bootstrap')->getOptions();
        $this->siteurl = $aConfig['bootstrap']['siteUrl'];
        $this->appmode = $aConfig['bootstrap']['appmode'];
        $this->per_page_record = 20;
        $this->dbAdapter = Zend_Db_Table::getDefaultAdapter();

        $auth = Zend_Auth::getInstance();
        $authStorage = $auth->getStorage()->read();
        $this->username = $authStorage->username;
        $this->admin_type = $authStorage->role;

        $this->current_time = time();
        $this->imageUrl = (($this->appmode == 'MODE_BETA') ? 'beta/' : '') . 'public/upload/static_pages/';
        $this->imageUrlHome = (($this->appmode == 'MODE_BETA') ? 'beta/' : '') . 'public/upload/home/';

        $this->img_w_small = 120;
        $this->img_h_small = 120;

        $this->img_w_large = 2100;
        $this->img_h_large = 796;

        $this->DIR_WRITE_MODE = 0777;
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
            $resulsetold = $crud->getCount('tbl_static_pages', ['status' => 'Activate'], 'sid');
            $crud->searchArr = $searchArr;
            $resultset = $crud->rv_select_static('tbl_static_pages', ['sid', 'page_title', 'status', 'createdOn', 'updatedOn'], [''], ['sid' => 'DESC']);
            $result = Zend_Json::encode($resultset);
            $newResult = Zend_Json::decode($result, false);
            $finalResult["total"] = $resulsetold[0]['sid'];
            $finalResult["rows"] = $newResult;
            echo json_encode($finalResult);
            exit;
        }
    }

    public function homeAction() {
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
            $resulsetold = $crud->getCount('tb_homebanner_detail', ['isMarkForDel' => 0], 'banner_id');
            $crud->searchArr = $searchArr;
            $resultset = $crud->rv_select_static('tb_homebanner_detail', ['banner_id', 'image', 'heading', 'description', 'isDisplayOnHome'], ['isMarkForDel' => 0], ['banner_id' => 'DESC']);
            $result = Zend_Json::encode($resultset);
            $newResult = Zend_Json::decode($result, false);
            $finalResult["total"] = $resulsetold[0]['banner_id'];
            $finalResult["rows"] = $newResult;
            echo json_encode($finalResult);
            exit;
        }
    }

    public function queryAction() {

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
            $resulsetold = $crud->getCount('tbl_query', [' '], 'id');
            $crud->searchArr = $searchArr;
            $resultset = $crud->rv_select_static('tbl_query', ['id','email', 'secondEmail', 'phone', 'mobile', 'location','whatsapp_no'], [' '], ['id' => 'DESC']);
            $result = Zend_Json::encode($resultset);
            $newResult = Zend_Json::decode($result, false);
            $finalResult["total"] = $resulsetold[0]['id'];
            $finalResult["rows"] = $newResult;
            echo json_encode($finalResult);
            exit;
    }
    }
    
    public function addhomeAction() {
        $this->checklogin();
        $crud = new Admin_Model_CRUD();
        $form = new Admin_Form_Addhome();
        $form->setAction("admin/staticpage/addhome");
        $form->setMethod("POST");
        $form->setName("add_home");

        if ($this->getRequest()->isPost()) {
            $getData = $this->getRequest()->getPost();
            if ($form->isValid($getData)) {
                if (isset($getData['save']) == "Save") {

                    $images = $_FILES['image']['name'];
                   
                    $savePageData = [
                        'heading' => trim($getData['heading']),
                        'description' => trim($getData['description']),
                        'url' => trim($getData['url']),
                        'opt' => $getData['opt'][0],
                        'status' => $getData['status'],
                    ];

                    $banner_id = $crud->rv_insert('tb_homebanner_detail', $savePageData);
                    
                    if (isset($images) && !empty($images)) {
                    $orignalFolderName = $_SERVER["DOCUMENT_ROOT"] . "/" . $this->imageUrlHome . $banner_id;
                    $fileExt = $this->_helper->General->getFileExtension($images);
                    $fileName = $banner_id . '_' . $this->current_time . '.' . $fileExt;

                    $originalLargeFolder = $orignalFolderName . "/large";

                    if (!file_exists($orignalFolderName)) {
                        mkdir($orignalFolderName, $this->DIR_WRITE_MODE, true);
                    }
                    if (!file_exists($originalLargeFolder)) {
                        mkdir($originalLargeFolder, $this->DIR_WRITE_MODE, true);
                    }
                    $temp_file_name = $_FILES["image"]["tmp_name"]; // temprary file name

                    @move_uploaded_file($temp_file_name, $orignalFolderName . "/" . $fileName);

                    @copy($orignalFolderName . '/' . $fileName, $originalLargeFolder . "/" . $fileName); // copy uploaded file into this location directory
                    $objImageResize4 = new Catabatic_Imageresize($originalLargeFolder . '/' . $fileName);
                    $objImageResize4->resizeImage($this->img_w_large, $this->img_h_large, 'exact'); // param : width , height , (exact|portrait|landscape|auto|crop)
                    $objImageResize4->saveImage($originalLargeFolder . '/' . $fileName);
                }
                    $editPageData = [
                        'image' => $fileName
                    ];    
                    $crud->rv_update('tb_homebanner_detail', $editPageData, ['banner_id =?' => $banner_id]);
                    $this->view->successMessage = "Content has been saved successfully.";
                    $this->_helper->flashMessenger->addMessage("Content has been added successfully.");
                    $this->_redirect("/admin/staticpage/home");
                }
            }
        }

        $this->view->form = $form;
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
    }

    public function edithomeAction(){
        //Check admin logedin or not
        $this->checklogin();
        $crud = new Admin_Model_CRUD();
        $form = new Admin_Form_Edithome();
        $banner_id = (int) $this->getRequest()->getParam("id");
        $form->setMethod("POST");
        $form->setAction("admin/staticpage/edithome/id/" . $banner_id);
        $form->setName("edit_home");

        if ($this->getRequest()->isPost()) {

            $getData = $this->getRequest()->getPost();
//            echo"<pre>";print_r($getData);die;
//            explode();
            //-------Start Code for Approve and Publish content---------//
            if (isset($getData['save']) == "Save") {

                //Code for check page alias name already exists or not
                $banner_id = (int) @$getData['banner_id'];

                $images = $_FILES['image']['name'];

                if (isset($images) && !empty($images)) {
                    $orignalFolderName = $_SERVER["DOCUMENT_ROOT"] . "/" . $this->imageUrlHome . $banner_id;
                    $fileExt = $this->_helper->General->getFileExtension($images);
                    $fileName = $banner_id . '_' . $this->current_time . '.' . $fileExt;

                    $originalLargeFolder = $orignalFolderName . "/large";

                    if (!file_exists($orignalFolderName)) {
                        mkdir($orignalFolderName, $this->DIR_WRITE_MODE, true);
                    }
                    if (!file_exists($originalLargeFolder)) {
                        mkdir($originalLargeFolder, $this->DIR_WRITE_MODE, true);
                    }
                    $temp_file_name = $_FILES["image"]["tmp_name"]; // temprary file name

                    @move_uploaded_file($temp_file_name, $orignalFolderName . "/" . $fileName);

                    @copy($orignalFolderName . '/' . $fileName, $originalLargeFolder . "/" . $fileName); // copy uploaded file into this location directory
                    $objImageResize4 = new Catabatic_Imageresize($originalLargeFolder . '/' . $fileName);
                    $objImageResize4->resizeImage($this->img_w_large, $this->img_h_large, 'exact'); // param : width , height , (exact|portrait|landscape|auto|crop)
                    $objImageResize4->saveImage($originalLargeFolder . '/' . $fileName);
                }
                
                if ($fileName != "") {
                        $image_edit = $fileName;
                }

                $editPageData = [
                    'heading' => trim($getData['heading']),
                    'description' => trim($getData['description']),
                    'url' => trim($getData['url']),
                    'opt' => $getData['opt'][0],
                    'status' => $getData['status'],
                ];
                
                if ($fileName) {
                        $editPageData['image'] = $image_edit;
                }
                
                $crud->rv_update('tb_homebanner_detail', $editPageData, ['banner_id =?' => $banner_id]);
                $this->view->successMessage = "Page content has been saved successfully.";
                $this->_helper->flashMessenger->addMessage("Page content has been updated successfully.");
                $this->_redirect("/admin/staticpage/home");
            }
        }


        $result = $crud->getCmsdata('tb_homebanner_detail', ['*'], ['banner_id' => $banner_id], ['banner_id' => 'DESC']);

        $editdata["banner_id"] = @$result->banner_id;
        $editdata["heading"] = @$result->heading;
        $editdata["description"] = @$result->description;
        $editdata["url"] = @$result->url;
        $editdata["opt"] = @$result->opt;
        $editdata["status"] = @$result->status;
        $editdata["image"] = @$result->image;
        $form->populate($editdata);
        $this->view->banner_id = @$result->banner_id;
        $this->view->form = $form;
        $this->view->image = @$result->image;
    }
    
    public function editqueryAction() {
        //Check admin logedin or not
        $this->checklogin();
        $crud = new Admin_Model_CRUD();
        $form = new Admin_Form_Editquery();
        $pId = (int) $this->getRequest()->getParam("id");
        $form->setMethod("POST");
        $form->setAction("admin/staticpage/editquery/id/" . $pId);
        $form->setName("edit_query");
        

        if ($this->getRequest()->isPost()) {

            $getData = $this->getRequest()->getPost();

            //-------Start Code for Approve and Publish content---------//
            if (isset($getData['save']) == "Save") {

                //Code for check page alias name already exists or not
                $page_id = (int) @$getData['id'];

                $editPageData = [
                    'phone' => trim($getData['phone']),
                    'mobile' => trim($getData['mobile']),
                    'email' => trim($getData['email']),
                    'secondEmail' => trim($getData['secondEmail']),
                    'location' => $getData['location'],
                    'status' => $getData['status'],
                     'whatsapp_no' => $getData['whatsapp_no'],
                ];
                $crud->rv_update('tbl_query', $editPageData, ['id =?' => $page_id]);
                $this->view->successMessage = "Page content has been saved successfully.";
                $this->_helper->flashMessenger->addMessage("Page content has been updated successfully.");
                $this->_redirect("/admin/staticpage/query");
            }
        }

        $result = $crud->getCmsdata('tbl_query', ['*'], ['id' => $pId], ['id' => 'DESC']);
//        echo "<pre>";print_r($result);die;
        $editdata["id"] = @$result->id;
        $editdata["phone"] = @$result->phone;
        $editdata["mobile"] = @$result->mobile;
        $editdata["location"] = @$result->location;
        $editdata["email"] = @$result->email;
        $editdata["secondEmail"] = @$result->secondEmail;
        $editdata["status"] = @$result->status;
        $editdata["whatsapp_no"] = @$result->whatsapp_no;
        $form->populate($editdata);
        $this->view->form = $form;
    }

    /**
     * editpage() method is used to admin can edit cms static page
     * @param password string
     * @return ture 
     */
    public function editpageAction() {
        //Check admin logedin or not
        $this->checklogin();
        $crud = new Admin_Model_CRUD();
        $form = new Admin_Form_Editstaticpage();
        $pId = (int) $this->getRequest()->getParam("id");
        $form->setMethod("POST");
        $form->setAction("admin/staticpage/editpage/id/" . $pId);
        $form->setName("edit_static_page");

        if ($this->getRequest()->isPost()) {

            $getData = $this->getRequest()->getPost();

//          print_r($getData);
//          echo "<pre>";
//            print_r($form->getErrors());
////            var_dump($form);
//            die;
            if ($form->isValid($getData)) {

                //-------Start Code for Approve and Publish content---------//
                if (isset($getData['save']) == "Save") {

                    //Code for check page alias name already exists or not
                    $page_id = (int) @$getData['sid'];


                    $images = $_FILES['background_image']['name'];
                    if (!empty($images)) {
                        $orignalFolderName = $_SERVER["DOCUMENT_ROOT"] . "/" . $this->imageUrl;
                        $fileExt = $this->_helper->General->getFileExtension($images);
                        $fileName = $page_id . '_' . $this->current_time . '.' . $fileExt;

                        if (!file_exists($orignalFolderName)) {
                            mkdir($orignalFolderName, $this->DIR_WRITE_MODE, true);
                        }
                        $temp_file_name = $_FILES["background_image"]["tmp_name"]; // temprary file name

                        @move_uploaded_file($temp_file_name, $orignalFolderName . "/" . $fileName);
                    }
                    if ($fileName != "") {
                        $image_edit = $fileName;
                    }
                    $page_id = $getData['sid'];

                    $editPageData = [
                        'page_title' => strtoupper($getData['page_title']),
                        'identifier' => strtolower(Catabatic_Helper::getSeoName($getData['page_title'])),
                        'meta_title' => $getData['meta_title'],
                        'meta_keywords' => $getData['meta_keywords'],
                        'meta_description' => $getData['meta_description'],
                        'page_description' => $getData['page_description'],
                        'status' => $getData['status'],
                        'updatedOn' => date("Y-m-d H:i:s"),
                    ];

                    if ($fileName) {
                        $editPageData['background_image'] = $image_edit;
                    }
                    $crud->rv_update('tbl_static_pages', $editPageData, ['sid =?' => $page_id]);
                    $this->view->successMessage = "Page content has been saved successfully.";
                    $this->_helper->flashMessenger->addMessage("Page content has been updated successfully.");
                    $this->_redirect("/admin/staticpage/index");
                }
            } else {
                die('form invalid');
            }
        }

        $result = $crud->getCmsdata('tbl_static_pages', ['*'], ['sid' => $pId], ['sid' => 'DESC']);
//        echo "<pre>";print_r($result);die;
        $editdata["sid"] = @$result->sid;
        $editdata["page_title"] = @$result->page_title;
        $editdata["meta_title"] = @$result->meta_title;
        $editdata["meta_keywords"] = @$result->meta_keywords;
        $editdata["meta_description"] = @$result->meta_description;
        $editdata["background_image"] = @$result->background_image;
        $editdata["page_description"] = @$result->page_description;
        $editdata["status"] = @$result->status;
        $form->populate($editdata);

        $this->view->background_image = @$result->background_image;
        $this->view->form = $form;
    }

    public function deletequeryAction() {
        $this->checklogin();
        $crud = new Admin_Model_CRUD();
        $tId = (int) $this->getRequest()->getParam("id");
        if ($tId) {
            $checkdata = $crud->rv_select_row('tbl_query', ['id'], ['id' => $tId], ['id' => 'asc']);
            if (count($checkdata) > 0) {
                $crud->rv_delete('tbl_query', ['id =?' => $tId]);
                $this->_helper->flashMessenger->addMessage("Delete successfully.");
                $this->_redirect("/admin/staticpage/query");
            } else {
                die('Oops some thing wrong!!.');
            }
        }
    }
    
    public function deletehomeAction() {
        $this->checklogin();
        $crud = new Admin_Model_CRUD();
        $tId = (int) $this->getRequest()->getParam("id");
        if ($tId) {
            $checkdata = $crud->rv_select_row('tb_homebanner_detail', ['banner_id'], ['banner_id' => $tId], ['banner_id' => 'asc']);
            if (count($checkdata) > 0) {
                $crud->rv_delete('tb_homebanner_detail', ['banner_id =?' => $tId]);
                $this->_helper->flashMessenger->addMessage("Delete successfully.");
                $this->_redirect("/admin/staticpage/home");
            } else {
                die('Oops some thing wrong!!.');
            }
        }
    }
    
    public function deleteimageAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout(); // disable layouts

        $param = $this->getRequest()->getParams();

        $id = $param['id'];
        $images = $param['images'];

        unlink("public/upload/home/$id/$images");
        unlink("public/upload/home/$id/large/$images");
        
        
        $crud = new Admin_Model_CRUD();
        $crud->rv_update('tb_homebanner_detail', ['image' => NULL], ['banner_id =?' => $id]);
        $response = array("status"=>true,"msg"=>"Deleted Successfully");
        echo json_encode($response);
        exit;
    }

    public function activeAction() {
        $this->checklogin();
        $crud = new Admin_Model_CRUD();
        $tId = (int) $this->getRequest()->getParam("id");
        $val = (int) $this->getRequest()->getParam("val");
        if ($tId) {
            try {
                $updatedata = [
                    'isDisplayOnHome' => $val
                ];
                $result = $crud->rv_update('tb_homebanner_detail', $updatedata, ['banner_id =?' => $tId]);
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
                $this->_redirect('admin/index');
            }
        } else {
            $this->_redirect('admin/index');
        }
    }

}
