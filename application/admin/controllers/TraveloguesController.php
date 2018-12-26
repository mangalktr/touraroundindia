<?php

/* * *************************************************************
 * Catabatic Technology Pvt. Ltd.
 * File Name     : StaticpageController.php
 * File Desc.    : Staticpage controller managed all staic content pages
 * Created By    : Piyush Tiwari <piyush@catpl.co.in>
 * Created Date  : 23 May 2018
 * Updated Date  : 23 May 2018
 * ************************************************************* */

class Admin_TraveloguesController extends Zend_Controller_Action {

    public $dbAdapter;
    public $perPageLimit;
    public $siteurl;
    public $DIR_WRITE_MODE;

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
        $this->imageUrl = (($this->appmode == 'MODE_BETA') ? 'beta/' : '') . 'public/upload/travelogues/';

        $this->img_w_small = 120;
        $this->img_h_small = 120;

        $this->img_w_trav = 350;
        $this->img_h_trav = 250;

        $this->img_w_ltrav = 870;
        $this->img_h_ltrav = 579;

        $this->img_w_btrav = 1600;
        $this->img_h_btrav = 300;

        $this->DIR_WRITE_MODE = 0777;

        $this->table = 'tbl_travelogues';
        $this->commenttable = 'tbl_comments';
    }

    /**
     * index() method is used to admin login for form call
     * @param Null
     * @return Array 
     */
    public function indexAction() {
        //Check admin logedin or not
        $this->checklogin();
        $getData = array();
        if ($this->getRequest()->isPost()) {
            $getData = $this->getRequest()->getPost();
            $searchArr = array(
                'Title' => $getData['Title'],
                'rows' => $getData['rows'],
                'page' => $getData['page'],
            );
            $crud = new Admin_Model_CRUD();
            $crud->searchArrt = $searchArr;
            $resulsetold = $crud->getCount($this->table, ['isMarkForDel' => 0], 'TravId');
            $resultset = $crud->rv_select_blog_all($this->table, ['*'], ['isMarkForDel' => 0], ['TravId' => 'DESC']);
            $result = Zend_Json::encode($resultset);
            $newResult = Zend_Json::decode($result, false);
            $finalResult["total"] = $resulsetold[0]['TravId'];
            $finalResult["rows"] = $newResult;
            echo json_encode($finalResult);
            exit;
        }
    }

    /**
     * editpage() method is used to admin can edit cms static page
     * @param password string
     * @return ture 
     */
    public function edittraveloguesAction() {

//Check admin logedin or not
        $this->checklogin();
        $crud = new Admin_Model_CRUD();
        $form = new Admin_Form_Edittravelogues();
        $pId = (int) $this->getRequest()->getParam("id");
        $form->setMethod("POST");
        $form->setAction("admin/travelogues/edittravelogues/id/" . $pId);

        $form->setName("edit_travelogues");

        if ($this->getRequest()->isPost()) {
            $getData = $this->getRequest()->getPost();

            if ($form->isValid($getData)) {

                //-------Start Code for Approve and Publish content---------//
                if (isset($getData['save']) == "Save") {
//                         echo "<pre>";print_r($getData);die;
                    //Code for check page alias name already exists or not
                    $TravId = $getData['TravId'];

                    $blogimage = $_FILES['TravBlogImage']['name'];


                    if (!empty($blogimage)) {
                        $orignalFolderName = $_SERVER["DOCUMENT_ROOT"] . "/" . $this->imageUrl;
                        $fileExtion = $this->_helper->General->getFileExtension($blogimage);
                        $bfileName = $this->current_time . '_blog.' . $fileExtion;
                        if (!file_exists($orignalFolderName)) {
                            mkdir($orignalFolderName, $this->DIR_WRITE_MODE, true);
                        }
                        $originalSmallFolder = $orignalFolderName . "/small";
                        if (!file_exists($originalSmallFolder)) {
                            mkdir($originalSmallFolder, $this->DIR_WRITE_MODE, true);
                        }

                        $originalLargeFolder = $orignalFolderName . "/large";
                        if (!file_exists($originalLargeFolder)) {
                            mkdir($originalLargeFolder, $this->DIR_WRITE_MODE, true);
                        }

                        $temp_file_name = $_FILES["TravBlogImage"]["tmp_name"]; // temprary file name

                        @move_uploaded_file($temp_file_name, $orignalFolderName . "/" . $bfileName);

                        @copy($orignalFolderName . '/' . $bfileName, $originalSmallFolder . "/" . $bfileName); // copy uploaded file into this location directory
                        $objImageResize3 = new Catabatic_Imageresize($originalSmallFolder . '/' . $bfileName);
                        $objImageResize3->resizeImage($this->img_w_trav, $this->img_h_trav, 'exact'); // param : width , height , (exact|portrait|landscape|auto|crop)
                        $objImageResize3->saveImage($originalSmallFolder . '/' . $bfileName);

                        @copy($orignalFolderName . '/' . $bfileName, $originalLargeFolder . "/" . $bfileName); // copy uploaded file into this location directory
                        $objImageResize3 = new Catabatic_Imageresize($originalLargeFolder . '/' . $bfileName);
                        $objImageResize3->resizeImage($this->img_w_ltrav, $this->img_h_ltrav, 'exact'); // param : width , height , (exact|portrait|landscape|auto|crop)
                        $objImageResize3->saveImage($originalLargeFolder . '/' . $bfileName);
                    }


                    $bannerimage = $_FILES['TravBannerImage']['name'];


                    if (!empty($bannerimage)) {
                        $orignalFolderName = $_SERVER["DOCUMENT_ROOT"] . "/" . $this->imageUrl;
                        $fileExt = $this->_helper->General->getFileExtension($bannerimage);
                        $fileName = $this->current_time . '.' . $fileExt;
                        if (!file_exists($orignalFolderName)) {
                            mkdir($orignalFolderName, $this->DIR_WRITE_MODE, true);
                        }
                        $originalSmallFolder = $orignalFolderName . "/banner";
                        if (!file_exists($originalSmallFolder)) {
                            mkdir($originalSmallFolder, $this->DIR_WRITE_MODE, true);
                        }

                        $temp_file_name = $_FILES["TravBannerImage"]["tmp_name"]; // temprary file name

                        @move_uploaded_file($temp_file_name, $orignalFolderName . "/" . $fileName);

                        @copy($orignalFolderName . '/' . $fileName, $originalSmallFolder . "/" . $fileName); // copy uploaded file into this location directory
                        $objImageResize3 = new Catabatic_Imageresize($originalSmallFolder . '/' . $fileName);
                        $objImageResize3->resizeImage($this->img_w_btrav, $this->img_h_btrav, 'exact'); // param : width , height , (exact|portrait|landscape|auto|crop)
                        $objImageResize3->saveImage($originalSmallFolder . '/' . $fileName);
                    }

                    $TravDate1 = explode('/', $getData['TravDate']);
                    $TravDate = $TravDate1[2] . "-" . $TravDate1[1] . "-" . $TravDate1[0];

                    $editPageData = [

                        'TravTitle' => ($getData['TravTitle']),
                        'TravDestination' => (implode(",", $getData['TravDestination'])),
                        'TravUploadedBy' => ($getData['TravUploadedBy']),
                        'tags' => ($getData['TravTags']),
                        'TravDescription' => ($getData['TravDescription']),
                        'status' => ($getData['status']),
                        'keyword' => ($getData['keyword']),
                        'description' => ($getData['description']),
                        'metatag' => ($getData['metatag']),
                    ];

                    if ($blogimage) {
                        $editPageData['TravImage'] = $bfileName;
                    }

                    if ($bannerimage) {
                        $editPageData['TravBannerImage'] = $fileName;
                    }
                    //echo "<pre>"; print_r($editPageData); die;
                    $crud->rv_update($this->table, $editPageData, ['TravId =?' => $TravId]);
                    $this->view->successMessage = "Page content has been saved successfully.";
                    $this->_helper->flashMessenger->addMessage("Page content has been updated successfully.");
                    $this->_redirect("/admin/travelogues/index");
                }
            }
        }

        $result = $crud->getCmsdata($this->table, ['*'], ['TravId' => $pId], ['TravId' => 'DESC']);
//        echo "<pre>";print_r($result);die;
        $editdata["TravId"] = @$result->TravId;
        $editdata["TravTitle"] = @$result->TravTitle;
        $editdata["TravImage"] = @$result->TravImage;
        $editdata["TravBannerImage"] = @$result->TravBannerImage;
        $editdata["TravDestination"] = @explode(",", $result->TravDestination);
        $editdata["TravUploadedBy"] = @$result->TravUploadedBy;

        $TravDate2 = explode('-', $result->TravDate);
        $result->TravDate = $TravDate2[2] . "/" . $TravDate2[1] . "/" . $TravDate2[0];
        $editdata["TravDate"] = @$result->TravDate;
        $editdata["TravDays"] = @$result->TravDays;
        $editdata["TravTraveller"] = @$result->TravTraveller;
        $editdata["TravCost"] = @$result->TravCost;
        $editdata["TravDescription"] = @$result->TravDescription;
        $editdata["status"] = @$result->status;
        $editdata["TravTags"] = @$result->tags;
        $editdata["keyword"] = @$result->keyword;
        $editdata["description"] = @$result->description;
        $editdata["metatag"] = @$result->metatag;
//        echo "<pre>";print_r($editdata);die;
        $form->populate($editdata);


        $this->view->TravBannerImage = @$result->TravBannerImage;
        $this->view->TravBlogImage = @$result->TravImage;
        $this->view->form = $form;
    }

    public function addtraveloguesAction() {

//Check admin logedin or not
        $this->checklogin();
        $crud = new Admin_Model_CRUD();
        $form = new Admin_Form_Addtravelogues();
        $pId = (int) $this->getRequest()->getParam("id");
        $form->setMethod("POST");
        $form->setAction("admin/travelogues/addtravelogues");
        $form->setName("add_travelogues");

        if ($this->getRequest()->isPost()) {
            $getData = $this->getRequest()->getPost();
            if ($form->isValid($getData)) {
                //-------Start Code for Approve and Publish content---------//
                if (isset($getData['save']) == "Save") {
//                echo "<pre>";print_r($getData);die;


                    $blogimage = $_FILES['TravBlogImage']['name'];
                    $bannerimage = $_FILES['TravBannerImage']['name'];

                    if (!empty($blogimage)) {
                        $orignalFolderName = $_SERVER["DOCUMENT_ROOT"] . "/" . $this->imageUrl;
                        $fileExtion = $this->_helper->General->getFileExtension($blogimage);
                        $bfileName = $this->current_time . '_blog.' . $fileExtion;
                        if (!file_exists($orignalFolderName)) {
                            mkdir($orignalFolderName, $this->DIR_WRITE_MODE, true);
                        }
                        $originalSmallFolder = $orignalFolderName . "/small";
                        if (!file_exists($originalSmallFolder)) {
                            mkdir($originalSmallFolder, $this->DIR_WRITE_MODE, true);
                        }

                        $temp_file_name = $_FILES["TravBlogImage"]["tmp_name"]; // temprary file name

                        @move_uploaded_file($temp_file_name, $orignalFolderName . "/" . $bfileName);

                        @copy($orignalFolderName . '/' . $bfileName, $originalSmallFolder . "/" . $bfileName); // copy uploaded file into this location directory
                        $objImageResize3 = new Catabatic_Imageresize($originalSmallFolder . '/' . $bfileName);
                        $objImageResize3->resizeImage($this->img_w_trav, $this->img_h_trav, 'exact'); // param : width , height , (exact|portrait|landscape|auto|crop)
                        $objImageResize3->saveImage($originalSmallFolder . '/' . $bfileName);
                    }
                    if ($bfileName != "") { //                                
                        $bimage_add = $bfileName;
                    } else {
                        $bimage_add = ""; //                               
                    }


                    if (!empty($bannerimage)) {
                        $orignalFolderName = $_SERVER["DOCUMENT_ROOT"] . "/" . $this->imageUrl;
                        $fileExt = $this->_helper->General->getFileExtension($bannerimage);
                        $fileName = $this->current_time . '.' . $fileExt;
                        if (!file_exists($orignalFolderName)) {
                            mkdir($orignalFolderName, $this->DIR_WRITE_MODE, true);
                        }
                        $originalSmallFolder = $orignalFolderName . "/banner";
                        if (!file_exists($originalSmallFolder)) {
                            mkdir($originalSmallFolder, $this->DIR_WRITE_MODE, true);
                        }

                        $temp_file_name = $_FILES["TravBannerImage"]["tmp_name"]; // temprary file name

                        @move_uploaded_file($temp_file_name, $orignalFolderName . "/" . $fileName);

                        @copy($orignalFolderName . '/' . $fileName, $originalSmallFolder . "/" . $fileName); // copy uploaded file into this location directory
                        $objImageResize3 = new Catabatic_Imageresize($originalSmallFolder . '/' . $fileName);
                        $objImageResize3->resizeImage($this->img_w_btrav, $this->img_h_btrav, 'exact'); // param : width , height , (exact|portrait|landscape|auto|crop)
                        $objImageResize3->saveImage($originalSmallFolder . '/' . $fileName);
                    }

                    if ($fileName != "") {
                        $image_add = $fileName;
                    } else {
                        $image_add = "";
                    }

                    $TravDate1 = explode('/', $getData['TravDate']);
                    $TravDate = $TravDate1[2] . "-" . $TravDate1[1] . "-" . $TravDate1[0];

                    $savePageData = [
                        'TravTitle' => ($getData['TravTitle']),
                        'TravImage' => $bimage_add,
                        'TravBannerImage' => $image_add,
                        'TravDestination' => (implode(",", $getData['TravDestination'])),
                        'TravUploadedBy' => ($getData['TravUploadedBy']),
                        'tags' => ($getData['TravTags']),
                        'TravDescription' => ($getData['TravDescription']),
                        'status' => ($getData['status']),
                        'keyword' => ($getData['keyword']),
                        'description' => ($getData['description']),
                        'metatag' => ($getData['metatag']),
                        'CreateDate' => date('Y-m-d H:i:s'),
                        'isMarkForDel' => 0,
                    ];
                    //echo "<pre>";print_r($savePageData);die;
                    $crud->rv_insert($this->table, $savePageData);
                    $this->view->successMessage = "Page content has been saved successfully.";
                    $this->_helper->flashMessenger->addMessage("Page content has been added successfully.");
                    $this->_redirect("/admin/travelogues/index");
                }
            }
        }

        $this->view->form = $form;
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
    }

    public function deletetraveloguesAction() {
        $this->checklogin();
        $crud = new Admin_Model_CRUD();
        $tId = (int) $this->getRequest()->getParam("id");
        //echo $tId;die;
        if ($tId) {
            $checkdata = $crud->rv_select_row($this->table, ['TravId'], ['TravId' => $tId], ['TravId' => 'asc']);
            if (count($checkdata) > 0) {
                $crud->rv_update($this->table, ['isMarkForDel' => 1], ['TravId =?' => $tId]);
                $this->_helper->flashMessenger->addMessage("Delete successfully.");
                $this->_redirect("/admin/travelogues/index");
            } else {
                die('Oops some thing wrong!!.');
            }
        }
    }

    public function commentsAction() {
        $pId = (int) $this->getRequest()->getParam("id");
        $this->checklogin();
        $crud = new Admin_Model_CRUD();
        $resultset = $crud->rv_select_all($this->commenttable, ['*'], ['IsMarkForDel' => 0, 'blogId' => $pId], ['commentId' => 'DESC']);
        $result = $crud->rv_select_row($this->table, ['*'], ['TravId' => $pId], ['TravId' => 'asc']);
        # Start : Pagination 
        $page = $this->_getParam('page', 1);
        $resultset = Zend_Paginator::factory($resultset);
        $resultset->setItemCountPerPage($this->per_page_record);
        $resultset->setCurrentPageNumber($page);
        # End : Pagination
        $this->view->page = $page;
        $this->view->per_page_record = $this->per_page_record;
        $this->view->resultset = $resultset;
        $this->view->blogDetails = $result;
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
    }

    public function editcommentAction() {


//Check admin logedin or not
        $this->checklogin();
        $crud = new Admin_Model_CRUD();
        $form = new Admin_Form_Editcomment();
        $pId = (int) $this->getRequest()->getParam("id");
        $form->setMethod("POST");
        $form->setAction("admin/travelogues/editcomment/id/" . $pId);

        $form->setName("edit_comment");

        if ($this->getRequest()->isPost()) {
            $getData = $this->getRequest()->getPost();

            if ($form->isValid($getData)) {

                //-------Start Code for Approve and Publish content---------//
                if (isset($getData['save']) == "Save") {
                    //    echo "<pre>";print_r($getData);die;
                    //Code for check page alias name already exists or not
                    $TravId = $getData['commentId'];

                    $editPageData = [
                        'blogId' => ($getData['blogId']),
                        'name' => ($getData['name']),
                        'emailId' => ($getData['emailId']),
                        'phone' => ($getData['phone']),
                        'comment' => ($getData['comment']),
                        'status' => ($getData['status']),
                        'UpdateDate' => date('Y-m-d h:i:s'),
                    ];
                    $crud->rv_update($this->commenttable, $editPageData, ['commentId =?' => $TravId]);
                    $this->view->successMessage = "Comment has been saved successfully.";
                    $this->_helper->flashMessenger->addMessage("Comment has been updated successfully.");
                    $this->_redirect("/admin/travelogues/comments/id/" . $getData['blogId']);
                }
            }
        }

        $result = $crud->getCmsdata($this->commenttable, ['*'], ['commentId' => $pId], ['commentId' => 'DESC']);
//        echo "<pre>";print_r($result);die;
        $editdata["commentId"] = @$result->commentId;
        $editdata["blogId"] = @$result->blogId;
        $editdata["name"] = @$result->name;
        $editdata["emailId"] = @$result->emailId;
        $editdata["phone"] = @$result->phone;
        $editdata["comment"] = @$result->comment;
        $editdata["status"] = @$result->status;
        $this->view->blogId = @$result->blogId;
        $form->populate($editdata);
        $this->view->form = $form;
    }

    public function deletecommentAction() {
        $this->checklogin();
        $crud = new Admin_Model_CRUD();
        $tId = (int) $this->getRequest()->getParam("id");
        //echo $tId;die;
        if ($tId) {
            $checkdata = $crud->rv_select_row($this->commenttable, ['commentId', 'blogId'], ['commentId' => $tId], ['commentId' => 'asc']);
            if (count($checkdata) > 0) {
                $crud->rv_update($this->commenttable, ['isMarkForDel' => 1], ['commentId =?' => $tId]);
                $this->_helper->flashMessenger->addMessage("Delete successfully.");
                $this->_redirect("/admin/travelogues/comments/id/" . $checkdata['blogId']);
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
                    'displayOnBanner' => $val
                ];
                $result = $crud->rv_update('tbl_travelogues', $updatedata, ['TravId =?' => $tId]);
                $resultset = $crud->rv_select_all("tbl_travelogues", ['*'], ['displayOnBanner' => 1, 'status' => 1, 'isMarkForDel' => 0], ['TravId' => 'ASC']);
                if (count($resultset) > 3) {
                    $result_message = ['status' => false, 'message' => 'Limit Exceed!!'];
                    echo Zend_Json::encode($result_message);
                    $updatedata = [
                        'displayOnBanner' => 0
                    ];
                    $result = $crud->rv_update('tbl_travelogues', $updatedata, ['TravId =?' => $tId]);
                    exit;
                }
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
                $this->_redirect('admin/index/index');
            }
        } else {
            $this->_redirect('admin/index/index');
        }
    }

}
