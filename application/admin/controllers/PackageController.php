<?php

/* * *************************************************************
 * Catabatic Technology Pvt. Ltd.
 * File Name     : ContactusController.php
 * File Desc.    : Contactus controller managed all contact queries
 * Created By    : Piyush Tiwari <piyush@catpl.co.in>
 * Created Date  : 23 May 2018
 * Updated Date  : 09 Jan 2018
 * ************************************************************* */

class Admin_PackageController extends Catabatic_Rvadmin {

    public $dbAdapter;
    public $perPageLimit;
    public $siteurl;
    public $DIR_WRITE_MODE;

    
    public function init() {

        parent::init(); // 
        
        /* Initialize db and session access */
        $aConfig = $this->getInvokeArg('bootstrap')->getOptions();
        $this->siteurl = $aConfig['bootstrap']['siteUrl'];
        $this->appmode = $aConfig['bootstrap']['appmode'];
        $this->AgencyId = $aConfig['bootstrap']['gtxagencysysid'];
        $this->per_page_record = 25;
        $this->dbAdapter = Zend_Db_Table::getDefaultAdapter();

        $auth = Zend_Auth::getInstance();
        $authStorage = $auth->getStorage()->read();
        $this->username = $authStorage->username;
        $this->admin_type = $authStorage->role;

        $this->current_time = time();
        $this->imageDirectory = (($this->appmode == 'MODE_BETA') ? 'beta/' : '') . 'public/upload/tours/';

        $this->img_w_thumb = 85;
        $this->img_h_thumb = 62;

        $this->img_w_medium = 220;
        $this->img_h_medium = 180;
        
        $this->img_w_large = 470;
        $this->img_h_large = 341;
        
        $this->img_w_small = 230;
        $this->img_h_small = 152;

        $this->DIR_WRITE_MODE = 0777;
        $this->tablename = 'tb_tbb2c_packages_master';
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
                        'Destinations'=>$getData['Destinations'],
                        'GTXPkgId'=>$getData['packagenumber'],
                        'name'=>$getData['name'],
                        'rows'=>$getData['rows'],
                        'page'=>$getData['page'],
                        'sort'=>$getData['sort'],
                        'order'=>$getData['order']
            );
            $resulsetold = $crud->getCount($this->tablename,['tbl.IsActive' => 1, 'tbl.IsPublish' => 1, 'tbl.IsMarkForDel' => 0, 'tbl.ItemType' => 1],'PkgSysId');
            $crud->searchArr = $searchArr;
            $resultset = $crud->rv_select_all_package($this->tablename, ['PkgSysId', 'lastMinuteDeal', 'GTXPkgId', 'LongJsonInfo', 'Destinations', 'Countries','Image', 'HotDeal', 'PkgValidUntil', 'Nights', 'StarRating','IsFeatured','IsActive','IsPublish','IsMarkForDel'], ['IsActive' => 1, 'IsPublish' => 1, 'IsMarkForDel' => 0, 'ItemType' => 1]);
//            print_r($resultset);
            $result = array();
            $jsonarray = array();
            foreach ($resultset as $resultkey => $resultval) {
                try {
                    $jsonarray[$resultkey] = Zend_Json::decode($resultval['LongJsonInfo'], true);
                } catch (Zend_Exception $e) {
                    $jsonarray[$resultkey] = "error";
                }
            }


            if (count($resultset) > 0) {
                foreach ($resultset as $resultkey => $resultval) {

                    $longJSON = $jsonarray[$resultkey];
                    if ($longJSON != 'error') {
                        $temp['package'] = $longJSON['package']; // get package type array
                    } else {
                        $temp['package']['Name'] = $resultval['GTXPkgId'] . " - error";
                    }
                    $result[] = [
                        'PkgSysId' => $resultval['PkgSysId'],
                        'GTXPkgId' => $resultval['GTXPkgId'],
                        'Destinations' => $resultval['Destinations'],
                        'IsFeatured' => $resultval['IsFeatured'],
                        'lastMinuteDeal' => $resultval['lastMinuteDeal'],
                        'HotDeal' => $resultval['HotDeal'],
                        'Countries' => $resultval['Countries'],
                        'PkgValidUntil' => $resultval['PkgValidUntil'],
                        'IsActive' => $resultval['IsActive'],
                        'Nights' => $resultval['Nights'],
                        'StarRating' => $resultval['StarRating'].' star', // custom field
                        'package' => $temp['package']['Name'],
                        'Image' => $resultval['Image'],
                    ];
                }
            }

            $result1 = Zend_Json::encode($result);
            $newResult1 = Zend_Json::decode($result1, false);
            $finalResult1["total"] = $resulsetold[0]['PkgSysId'];
            $finalResult1["rows"] = $newResult1;
            echo json_encode($finalResult1);
            exit;
        }
        
        
        
        
        
        
    }

    public function editpackageAction() {

        $this->checklogin();
        $crud = new Admin_Model_CRUD();
        $form = new Admin_Form_Editpackagepage();
        $pId = (int) $this->getRequest()->getParam("id");
        $page = ($this->getRequest()->getParam("page")) ? $this->getRequest()->getParam("page") : 1;
        
        $form->setMethod("POST");
        $form->setAction("admin/package/editpackage/id/" . $pId . "/page/$page");
        $form->setName("edit_package_page");

        if ($this->getRequest()->isPost()) {
            $getData = $this->getRequest()->getPost();
            //echo "<pre>";print_r($getData);die;
            if ($form->isValid($getData)) {

                //-------Start Code for Approve and Publish content---------//
                if (isset($getData['save']) == "Save") {
                    
                    $result = $crud->getCmsdata($this->tablename, ['*'], ['PkgSysId' => $pId], ['PkgSysId' => 'DESC']);

                    $images = $_FILES['image']['name'];
//                    print_r($images);die;
//                    $bannerimage = $_FILES['banner_image']['name'];


                    /*                     * ****************** Starts : destinations Image upload here **************** */
//               var_dump(count($images)); die();

                    foreach ($images as $key => $orignalFileName) {

                        if (!empty($orignalFileName)) {

                            $orignalFolderName = $_SERVER["DOCUMENT_ROOT"] . "/" . $this->imageDirectory . $pId . "/images"; // root folder for destination images


                            /* Get File Extension */
                            $fileExt = $this->_helper->General->getFileExtension($orignalFileName);
                            $fileName = $pId . '_' . $this->current_time . '_' . $key . '.' . $fileExt;
                            //echo $fileName;
                            $originalThumbFolder = $orignalFolderName . "/thumb";
                            $originalMediumFolder = $orignalFolderName . "/medium";
                            $originalLargeFolder = $orignalFolderName . "/large";
                            $originalSmallFolder = $orignalFolderName . "/small";


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
                            if (!file_exists($originalSmallFolder)) {
                                mkdir($originalSmallFolder, $this->DIR_WRITE_MODE, true);
                            }
                            
                            foreach ($_FILES["image"]["tmp_name"] as $key1 => $image) {
                                if ($key == $key1) {
                                    $temp_file_name = $image; // temprary file name
                                }
                            }
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
                            
                            @copy($originalLargeFolder . '/' . $fileName, $originalSmallFolder . "/" . $fileName); // copy uploaded file into this location directory
                            $objImageResize2 = new Catabatic_Imageresize($originalSmallFolder . '/' . $fileName);
                            $objImageResize2->resizeImage($this->img_w_small, $this->img_h_small, 'exact'); // param : width , height , (exact|portrait|landscape|auto|crop)
                            $objImageResize2->saveImage($originalSmallFolder . '/' . $fileName);

                        } else {
                            //                    echo 'else'; die;
                        }
                    }
//                var_dump($fileName); die;
                    /*                     * ****************** End : destinations Image upload here **************** */

                     $editActivitiesData = [

                        'HotDeal' => ($getData['hot_deal']),
                        'Keyword' => ($getData['keyword']),
                        'Description' => ($getData['description']),
                        'Metatag' => ($getData['metatag']),
//                        'IsActive' => ($getData['status_number']),
                    ];

                    if ($fileName) {
                        foreach ($images as $key => $image) {

                            $fileExt = $this->_helper->General->getFileExtension($image);
                            $fileName = $pId . '_' . $this->current_time . '_' . $key . '.' . $fileExt;
                            $editActivitiesData['Image'][] = $fileName;
                        }
                        $editActivitiesData['Image'] = $newimagenames = @implode(",", @$editActivitiesData['Image']);
                    }

                    $oldimagenames = $result["Image"];
                    $editActivitiesData['Image'] = "$oldimagenames";

                    if (trim($newimagenames))
                        $editActivitiesData['Image'] .= ",$newimagenames";
                    $editActivitiesData['Image'] = trim($editActivitiesData['Image'], ',');

                    $crud->rv_update($this->tablename, $editActivitiesData, ['PkgSysId =?' => $pId]);
                    // delete old images from folder too
                    $this->view->successMessage = "Package has been saved successfully.";
                    $this->_helper->flashMessenger->addMessage("Package has been updated successfully.");
                    $this->_redirect("/admin/package/index?page=$page");
                }
            }
        }

         $result = $crud->getCmsdata($this->tablename, ['*'], ['PkgSysId' => $pId], ['PkgSysId' => 'DESC']);
        
        $editdata["hot_deal"] = @$result->HotDeal;
        $editdata["keyword"] = @$result->Keyword;
        $editdata["description"] = @$result->Description;
        $editdata["metatag"] = @$result->Metatag;
        $editdata["Image"] = '';
        $editdata["Image"] .= @$result->Image;
        $form->populate($editdata);
        $this->view->pId = $pId;
        $this->view->image .= @$result->Image;
        $this->view->form = $form;
    }

    public function activeAction() {
        $this->checklogin();
        $crud = new Admin_Model_CRUD();
        $tId = (int) $this->getRequest()->getParam("id");
        $val = (int) $this->getRequest()->getParam("val");
        if ($tId && $val === 1) {
            try {
                $pId = $tId;
                $result = $crud->getCmsdata($this->tablename, ['*'], ['PkgSysId' => $pId], ['PkgSysId' => 'DESC']);
                $LongJsonInfo = Zend_Json::decode($result['LongJsonInfo']);
                $defaultImage = $LongJsonInfo['package']['ImgThumbnail'];
                $ImgCheck = end(explode('_', $defaultImage));
                $fileName = $pId . '_' . $ImgCheck;
                $previousImage=  explode(",",$result['Image']);
                if(in_array($fileName, $previousImage)){
                    $updatedata = [
                    'IsFeatured' => $val
                ];

                $result = $crud->rv_update($this->tablename, $updatedata, ['PkgSysId =?' => $tId]);
//                $resultset1 = $crud->rv_select_all($this->tablename, ['*'], ['IsFeatured' => 1, 'ItemType' => 1, 'IsMarkForDel' => 0], ['PkgSysId' => 'ASC']);
//                
////                print_r(count($resultset1));die;
//                if (count($resultset1) > 6) {
//                    $result_message = ['status' => false, 'message' => 'Limit Exceed!!'];
//                    echo Zend_Json::encode($result_message);
//                    $updatedata = [
//                        'IsFeatured' => 0
//                    ];
//                    $result = $crud->rv_update($this->tablename, $updatedata, ['PkgSysId =?' => $tId]);
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
                }
                else{
                if (isset($defaultImage) && !empty($defaultImage) &&  empty($result['Image'])) {
                    $ImgThumbnailContent = file_get_contents($defaultImage);
                    $fileExt = $this->_helper->General->getFileExtension($defaultImage);
                    $ImgThumbnail = end(explode('_', $defaultImage));
                    $fileName = $pId . '_' . $ImgThumbnail;
                    $orignalFolderName = $_SERVER["DOCUMENT_ROOT"] . "/" . $this->imageDirectory . $pId . "/images/";
                    $originalThumbFolder = $orignalFolderName . "/thumb";
                    $originalSmallFolder = $orignalFolderName . "/small";
                    $originalMediumFolder = $orignalFolderName . "/medium";
                    $originalLargeFolder = $orignalFolderName . "/large";
                    if (!file_exists($orignalFolderName)) {
                        mkdir($orignalFolderName, $this->DIR_WRITE_MODE, true);
                    }
                    if (!file_exists($originalThumbFolder)) {
                        mkdir($originalThumbFolder, $this->DIR_WRITE_MODE, true);
                    }
                    if (!file_exists($originalSmallFolder)) {
                        mkdir($originalSmallFolder, $this->DIR_WRITE_MODE, true);
                    }
                    if (!file_exists($originalMediumFolder)) {
                        mkdir($originalMediumFolder, $this->DIR_WRITE_MODE, true);
                    }
                    if (!file_exists($originalLargeFolder)) {
                        mkdir($originalLargeFolder, $this->DIR_WRITE_MODE, true);
                    }
                    if (!file_exists($fileName)) {
                        file_put_contents($orignalFolderName . $fileName, $ImgThumbnailContent);
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

                        @copy($originalLargeFolder . '/' . $fileName, $originalSmallFolder . "/" . $fileName); // copy uploaded file into this location directory
                        $objImageResize3 = new Catabatic_Imageresize($originalSmallFolder . '/' . $fileName);
                        $objImageResize3->resizeImage($this->img_w_small, $this->img_h_small, 'exact'); // param : width , height , (exact|portrait|landscape|auto|crop)
                        $objImageResize3->saveImage($originalSmallFolder . '/' . $fileName);
                    }
                    $updatedata = [
                    'IsFeatured' => $val, 'Image' => $fileName
                ];
//                $resultset = $crud->rv_select_all($this->tablename, ['*'], ['IsFeatured' => 1, 'ItemType' => 1, 'IsMarkForDel' => 0], ['PkgSysId' => 'ASC']);
                $result = $crud->rv_update($this->tablename, $updatedata, ['PkgSysId =?' => $tId]);
//                if (count($resultset) > 6) {
//                    $result_message = ['status' => false, 'message' => 'Limit Exceed!!'];
//                    echo Zend_Json::encode($result_message);
//                    $updatedata = [
//                        'IsFeatured' => 0
//                    ];
//                    $result = $crud->rv_update($this->tablename, $updatedata, ['PkgSysId =?' => $tId]);
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
                }
                else{
                    $ImgThumbnailContent = file_get_contents($defaultImage);
                    $fileExt = $this->_helper->General->getFileExtension($defaultImage);
                    $ImgThumbnail = end(explode('_', $defaultImage));
                    $fileName = $pId . '_' . $ImgThumbnail;
                    $orignalFolderName = $_SERVER["DOCUMENT_ROOT"] . "/" . $this->imageDirectory . $pId . "/images/";
                    $originalThumbFolder = $orignalFolderName . "/thumb";
                    $originalSmallFolder = $orignalFolderName . "/small";
                    $originalMediumFolder = $orignalFolderName . "/medium";
                    $originalLargeFolder = $orignalFolderName . "/large";
                    if (!file_exists($fileName)) {
                        file_put_contents($orignalFolderName . $fileName, $ImgThumbnailContent);
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

                        @copy($originalLargeFolder . '/' . $fileName, $originalSmallFolder . "/" . $fileName); // copy uploaded file into this location directory
                        $objImageResize3 = new Catabatic_Imageresize($originalSmallFolder . '/' . $fileName);
                        $objImageResize3->resizeImage($this->img_w_small, $this->img_h_small, 'exact'); // param : width , height , (exact|portrait|landscape|auto|crop)
                        $objImageResize3->saveImage($originalSmallFolder . '/' . $fileName);
                    }
                    $updatedata = [
                    'IsFeatured' => $val
                ];
                $oldimagenames = $result['Image'];
                    $updatedata['Image'] = "$oldimagenames";

                    if (trim($fileName))
                        $updatedata['Image'] .= ",$fileName";
                    $updatedata['Image'] = trim($updatedata['Image'], ',');

                $result = $crud->rv_update($this->tablename, $updatedata, ['PkgSysId =?' => $tId]);
//                $resultset = $crud->rv_select_all($this->tablename, ['*'], ['IsFeatured' => 1, 'ItemType' => 1, 'IsMarkForDel' => 0], ['PkgSysId' => 'ASC']);
//                if (count($resultset) > 6) {
//                    $result_message = ['status' => false, 'message' => 'Limit Exceed!!'];
//                    echo Zend_Json::encode($result_message);
//                    $updatedata = [
//                        'IsFeatured' => 0
//                    ];
//                    $result = $crud->rv_update($this->tablename, $updatedata, ['PkgSysId =?' => $tId]);
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
                }
                }
                
            } catch (Exception $ex) {
                $ex->getMessage();
            }
        } else {
            try {
                $updatedata = [
                    'IsFeatured' => $val
                ];
                $result = $crud->rv_update($this->tablename, $updatedata, ['PkgSysId =?' => $tId]);
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
    

    public function downloadImagesAction() {

        $this->checklogin();
        $crud = new Admin_Model_CRUD();
        $PkgSysId = (int) $this->getRequest()->getParam("id");
        
        $resultset  = $crud->rv_select_row( $this->tablename, ['Image','LongJsonInfo'], ['ItemType' => 1, 'IsMarkForDel' => 0, 'PkgSysId'=> $PkgSysId ], ['PkgSysId' => 'ASC'] );

        if( $resultset['LongJsonInfo'] ){
            $LongJsonInfo = Zend_Json::decode($resultset['LongJsonInfo']);
        }
        
        $sourceURL   = $LongJsonInfo['package']['ImgThumbnail'];
        $destination = 'public/upload/tours/';
        $clonesArray = ['thumb', 'large','medium']; // give the sizes of images

        if( $sourceURL ) {
            // copy image to local server from third party urls
            $result = $this->downloadImagesFromServer( $PkgSysId , $sourceURL , $this->imageDirectory , $clonesArray );
            $crud->rv_update($this->tablename , ['Image'=> $result['img']], ['PkgSysId =?' => $PkgSysId ] ); // update into database
        } else {
            $result = ['status' => FALSE, 'message' => "Image Not Available.", 'img'=> '' ];
        }
        
        echo Zend_Json::encode($result);
        die;
    }
    
    
    public function activedealsAction() {
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->checklogin();
        if ($this->getRequest()->isPost()) {
        $param = $this->getRequest()->getParams();
        $crud = new Admin_Model_CRUD();
//        print_r($param);die;
        $tId = $param["id"];
        $val = $param["val"];
        if ($tId) {
            try {
                $updatedata = [
                    'lastMinuteDeal' => $val
                ];
                $result = $crud->rv_update($this->tablename, $updatedata, ['PkgSysId =?' => $tId]);
//                $resultset = $crud->rv_select_all($this->tablename, ['*'], ['lastMinuteDeal' => 1, 'ItemType' => 1, 'IsMarkForDel' => 0], ['PkgSysId' => 'ASC']);
//                if(count($resultset)>6){
//                   $result_message = ['status' => false, 'message' => 'Limit Exceed!!'];
//                    echo Zend_Json::encode($result_message);
//                    $updatedata = [
//                    'lastMinuteDeal' => 0
//                ];
//                    $result = $crud->rv_update($this->tablename, $updatedata, ['PkgSysId =?' => $tId]);
//                    exit; 
//                }
//                print_r($tId);die;
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
    }
    
    
     public function deleteimageAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout(); // disable layouts

        $param = $this->getRequest()->getParams();

        $id = $param['id'];
        $images = $param['images'];

        unlink("public/upload/tours/$id/images/$images");
        unlink("public/upload/tours/$id/images/large/$images");
        unlink("public/upload/tours/$id/images/medium/$images");
        unlink("public/upload/tours/$id/images/small/$images");
        unlink("public/upload/tours/$id/images/thumb/$images");

        $crud = new Admin_Model_CRUD();
        $result = $crud->getCmsdata($this->tablename, ['Image'], ['PkgSysId' => $id], ['PkgSysId' => 'DESC']);
        $strImages = $result['Image'];
        $arrImages = explode(",", $strImages);

        foreach ($arrImages as $key => $value) {
            if (trim($value) == trim($images)) {
                unset($arrImages[$key]);
            }
        }
        $strImages = trim(implode(",", $arrImages), ",");
        if($strImages === ""){
            $strImages = null;
        }
        $crud->rv_update($this->tablename, ['Image' => $strImages], ['PkgSysId =?' => $id]);
        $response = array("status"=>true,"msg"=>"Deleted Successfully");
        echo json_encode($response);
        exit;
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
