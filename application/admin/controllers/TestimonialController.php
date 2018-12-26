<?php

/* * *************************************************************
 * Catabatic Technology Pvt. Ltd.
 * File Name     : ContactusController.php
 * File Desc.    : Contactus controller managed all contact queries
 * Created By    : Piyush Tiwari <piyush@catpl.co.in>
 * Created Date  : 23 May 2018
 * Updated Date  : 23 May 2018
 * ************************************************************* */

class Admin_TestimonialController extends Zend_Controller_Action {

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
        $this->current_time = time();
        $this->imageUrl = (($this->appmode == 'MODE_BETA') ? 'beta/' : '') . 'public/upload/testimonial/';
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
            $resulsetold = $crud->getCount('tbl_testimonials', [' '], 'id');
            $crud->searchArr = $searchArr;
            $resultset = $crud->rv_select_static('tbl_testimonials', ['id', 'name', 'message', 'Image', 'IsFeatured'], [''], ['id' => 'DESC']);
            $result = Zend_Json::encode($resultset);
            $newResult = Zend_Json::decode($result, false);
            $finalResult["total"] = $resulsetold[0]['id'];
            $finalResult["rows"] = $newResult;
            echo json_encode($finalResult);
            exit;
        }
    }

    public function edittestimonialAction() {
        //Check admin logedin or not
        $this->checklogin();
        $crud = new Admin_Model_CRUD();
        $form = new Admin_Form_Edittestimonial();
        $tId = (int) $this->getRequest()->getParam("id");
        if (isset($tId) && !empty($tId)) {
//            die("here");
            $form->setAction("admin/testimonial/edittestimonial/".$tId);
            $form->setMethod("POST");
            $form->setName("edit_testimonial");

            if ($this->getRequest()->isPost()) {
                $getData = $this->getRequest()->getPost();
                if ($tId) {
                    if (isset($getData['save']) == "Save") {
                        $testi_id = (int) @$getData['id'];
                        $images = $_FILES["TestiImage"]["name"];

                        try {
                            $page_id = $getData['sid'];
                            if (!empty($images)) {
                                $orignalFolderName = $_SERVER["DOCUMENT_ROOT"] . "/" . $this->imageUrl;
                                $fileExt = $this->_helper->General->getFileExtension($images);
                                $fileName = $this->current_time . '.' . $fileExt;
                                if (!file_exists($orignalFolderName)) {
                                    mkdir($orignalFolderName, $this->DIR_WRITE_MODE, true);
                                }
                                $temp_file_name = $_FILES["TestiImage"]["tmp_name"]; // temprary file name

                                @move_uploaded_file($temp_file_name, $orignalFolderName . "/" . $fileName);
                            }

                            if ($fileName != "") {
                                $image_edit = $fileName;
                            }
                            $editPageData = [
                                'name' => ($getData['name']),
                                'message' => $getData['message'],
                                'status' => $getData['status'],
                                'created_at' => date('Y-m-d h:i:s')
                            ];

                            if ($fileName) {
                                $editPageData['Image'] = $image_edit;
                            }

                            $crud->rv_update('tbl_testimonials', $editPageData, ['id =?' => $testi_id]);
                            $this->view->successMessage = "Page content has been saved successfully.";
                            $this->_helper->flashMessenger->addMessage("Page content has been updated successfully.");
                            $this->_redirect("/admin/testimonial/index");
                        } catch (Zend_File_Transfer_Exception $e) {
                            $e->getMessage();
                        }
                    }
                } else {
                    try {
                        $editPageData = [
                            'name' => ($getData['name']),
                            'message' => $getData['message'],
                            'status' => $getData['status'],
                            'created_at' => date('Y-m-d h:i:s')
                        ];

                        $crud->rv_insert('tbl_testimonials', $editPageData);
                        $this->view->successMessage = "Page content has been saved successfully.";
                        $this->_helper->flashMessenger->addMessage("Page content has been Added successfully.");
                        $this->_redirect("/admin/testimonial/index");
                    } catch (Zend_File_Transfer_Exception $e) {
                        $e->getMessage();
                    }
                    // echo "<pre>";print_r($getData);die('fd');
                }
            }
        } else {
            $form->setAction("admin/testimonial/edittestimonial/");
            $form->setMethod("POST");
            $form->setName("add_testimonial");

            if ($this->getRequest()->isPost()) {
                $getData = $this->getRequest()->getPost();
                if ($form->isValid($getData)) {
                    if (isset($getData['save']) == "Save") {

                        $images = $_FILES["TestiImage"]["name"];
                        if (!empty($images)) {
                            $orignalFolderName = $_SERVER["DOCUMENT_ROOT"] . "/" . $this->imageUrl;
                            $fileExt = $this->_helper->General->getFileExtension($images);
                            $fileName = $this->current_time . '.' . $fileExt;

                            if (!file_exists($orignalFolderName)) {
                                mkdir($orignalFolderName, $this->DIR_WRITE_MODE, true);
                            }
                            $temp_file_name = $_FILES["TestiImage"]["tmp_name"]; // temprary file name

                            @move_uploaded_file($temp_file_name, $orignalFolderName . "/" . $fileName);
                        }

                        $savePageData = [
                            'name' => ($getData['name']),
                            'message' => $getData['message'],
                            'Image' => ($fileName),
                            'status' => $getData['status'],
                            'created_at' => date('Y-m-d h:i:s')
                        ];

                        $crud->rv_insert('tbl_testimonials', $savePageData);
                        $this->view->successMessage = "Content has been saved successfully.";
                        $this->_helper->flashMessenger->addMessage("Content has been added successfully.");

                        $this->_redirect("/admin/testimonial/index");
                    }
                }
            }
        }

        $result = $crud->rv_select_row('tbl_testimonials', ['*'], ['id' => $tId], ['id' => 'DESC']);
        //print_r($result);die;
        $editdata["id"] = @$result['id'];
        $editdata["name"] = @$result['name'];
        $editdata["message"] = @$result['message'];
        $editdata["status"] = @$result['status'];
        $editdata["TestiImage"] = @$result['Image'];
        $form->populate($editdata);

        $this->view->form = $form;
        $this->view->TestiImage = @$result['Image'];
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
        //die('ok');
    }

    public function deletetestiAction() {
        $this->checklogin();
        $crud = new Admin_Model_CRUD();
        $tId = (int) $this->getRequest()->getParam("id");
        if ($tId) {
            $checkdata = $crud->rv_select_row('tbl_testimonials', ['id'], ['id' => $tId], ['id' => 'asc']);
            if (count($checkdata) > 0) {
                $crud->rv_delete('tbl_testimonials', ['id =?' => $tId]);
                $this->_helper->flashMessenger->addMessage("Delete successfully.");
                $this->_redirect("/admin/testimonial/index");
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
                $result = $crud->rv_update('tbl_testimonials', $updatedata, ['id =?' => $tId]);
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
