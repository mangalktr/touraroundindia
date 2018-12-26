<?php

/* * *************************************************************
 * Catabatic Technology Pvt. Ltd.
 * File Name     : ContactusController.php
 * File Desc.    : Contactus controller managed all contact queries
 * Created By    : Piyush Tiwari <piyush@catpl.co.in>
 * Created Date  : 23 May 2018
 * Updated Date  : 23 May 2018
 * ************************************************************* */

class Admin_PackController extends Zend_Controller_Action {

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
        $this->imageUrl = (($this->appmode == 'MODE_BETA') ? 'beta/' : '') . 'public/upload/pack/';

        $this->img_w_small = 44;
        $this->img_h_small = 44;
        
        $this->banner_w_large  = 1349;
        $this->banner_h_large  = 208;

        $this->DIR_WRITE_MODE = 0777;
        $this->table = "tbl_pack_type";
    }

    /**
     * index() method is used to admin login for form call
     * @param Null
     * @return Array 
     */
    public function indexAction() {
        $this->checklogin();
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
        $crud = new Admin_Model_CRUD();
        $getData = array();
        if ($this->getRequest()->isPost()) {
            $getData = $this->getRequest()->getPost();
            $searchArr = array(
                'Titles' => $getData['Title'],
                'rows' => $getData['rows'],
                'page' => $getData['page'],
            );
            $resulsetold = $crud->getCount($this->table, ['IsActive' => 1, 'IsMarkForDel' => 0], 'packType');
            $crud->searchArr = $searchArr;
            $resultset = $crud->rv_select_static($this->table, ['Title', 'packType', 'DisplayOnFooter'], ['IsActive' => 1, 'IsMarkForDel' => 0], ['Title' => 'DESC']);
            $result = Zend_Json::encode($resultset);
            $newResult = Zend_Json::decode($result, false);
            $finalResult["total"] = $resulsetold[0]['packType'];
            $finalResult["rows"] = $newResult;
            echo json_encode($finalResult);
            exit;
        }
    }

    public function editpackAction() {
        //Check admin logedin or not
        $this->checklogin();
        $crud = new Admin_Model_CRUD();
        $form = new Admin_Form_Editpack();
        $packType = (int) $this->getRequest()->getParam("id");
        $form->setMethod("POST");
        $form->setAction("admin/pack/editpack/id/" . $packType);
        $form->setName("edit_pack");

        if ($this->getRequest()->isPost()) {

            $getData = $this->getRequest()->getPost();
//            echo"<pre>";print_r($getData);die;
//            explode();
            //-------Start Code for Approve and Publish content---------//
            if (isset($getData['save']) == "Save") {

                //Code for check page alias name already exists or not
                $packType = (int) @$getData['packType'];

                $images = $_FILES['banner_image']['name'];

                if (isset($images) && !empty($images)) {
                    $orignalFolderName = $_SERVER["DOCUMENT_ROOT"] . "/" . $this->imageUrl . $packType;
                    $fileExt = $this->_helper->General->getFileExtension($images);
                    $fileName = $packType . '_' . $this->current_time . '.' . $fileExt;

                    $originalLargeFolder = $orignalFolderName . "/large";

                    if (!file_exists($orignalFolderName)) {
                        mkdir($orignalFolderName, $this->DIR_WRITE_MODE, true);
                    }
                    if (!file_exists($originalLargeFolder)) {
                        mkdir($originalLargeFolder, $this->DIR_WRITE_MODE, true);
                    }
                    $temp_file_name = $_FILES["banner_image"]["tmp_name"]; // temprary file name

                    @move_uploaded_file($temp_file_name, $orignalFolderName . "/" . $fileName);

                    @copy($orignalFolderName . '/' . $fileName, $originalLargeFolder . "/" . $fileName); // copy uploaded file into this location directory
                    $objImageResize4 = new Catabatic_Imageresize($originalLargeFolder . '/' . $fileName);
                    $objImageResize4->resizeImage($this->banner_w_large, $this->banner_h_large, 'exact'); // param : width , height , (exact|portrait|landscape|auto|crop)
                    $objImageResize4->saveImage($originalLargeFolder . '/' . $fileName);
                }

                if ($fileName != "") {
                    $image_edit = $fileName;
                }


                if ($fileName) {
                    $editPageData['banner_image'] = $image_edit;
                }

                $crud->rv_update($this->table, $editPageData, ['packType =?' => $packType]);
                $this->view->successMessage = "Page content has been saved successfully.";
                $this->_helper->flashMessenger->addMessage("Page content has been updated successfully.");
                $this->_redirect("/admin/pack/index");
            }
        }


        $result = $crud->getCmsdata($this->table, ['packType', 'banner_image'], ['packType' => $packType], ['packType' => 'DESC']);
//        echo"<pre>";print_r($result);die;
        $editdata["packType"] = @$result->packType;
        $editdata["banner_image"] = @$result->banner_image;
        $form->populate($editdata);
        $this->view->form = $form;
        $this->view->packType = @$result->packType;
        $this->view->banner_image = @$result->banner_image;
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
                $result = $crud->rv_update($this->table, $updatedata, ['packType =?' => $tId]);

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
