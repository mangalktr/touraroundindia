<?php
/***************************************************************
* Catabatic Technology Pvt. Ltd.
* File Name     : HomePromotionController.php
* File Desc.    : HomePromotion Controller  managed all Home Promotion content pages
* Created By    : Mangal katiyar <mangal.co.in>
* Created Date  : 19 Nov 2018
* Updated Date  : 19 Nov 2018
***************************************************************/



class Admin_HomepromotionController extends Zend_Controller_Action
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
        $this->promoimageUrl     = (($this->appmode == 'MODE_BETA') ? 'beta/' : '') . 'public/upload/homepromotion/';

        $this->img_w_banner1  = 583;
        $this->img_h_banner1  = 350;
        $this->img_w_banner2  = 360;
        $this->img_h_banner2  = 350;
        $this->img_w_banner3  = 200;
        $this->img_h_banner3  = 350;

        $this->DIR_WRITE_MODE = 0777;
        
       $this->table =  'tbl_home_promotion';
     
    }
    
    
    
    /**
    * index() method is used to admin login for form call
    * @param Null
    * @return Array 
    */
    
    public function indexAction()
    {
        $this->checklogin();
        $getData = array();
        if ($this->getRequest()->isPost()) {
            $getData = $this->getRequest()->getPost();
//            $searchArr = array(
//                'Title' => $getData['Title'],
//                'rows' => $getData['rows'],
//                'page' => $getData['page'],
//            );
            
            $crud = new Admin_Model_CRUD();
//            $crud->searchArrt = $searchArr;
            $resulsetold = $crud->getCount($this->table, ['IsmarkForDel' => 0], 'promotionId');
            
            $resultset = $crud->rv_select_all($this->table, ['*'], ['IsmarkForDel' => 0], ['promotionId' => 'DESC']);
            $resultCategory = $crud->getCmsdata('tbl_promotion_category', ['*'], ['prom_cat_id'], ['prom_cat_id'=>'DESC']);
            
            
            $resultsetArr = array();
            
            foreach ($resultset as $reskey => $resvalue) {
                if($resvalue['templatetype'] == 1){
                    $resultsetArr[] =   [
                                    'promotionId' =>$resvalue['promotionId'],
                                    'templatetype' => $resultCategory->prom_cat_one,
                                    'templatetypeId' => 1,
                                    
                                    'promotion_name' => $resvalue['promotion_name'],
                                    'tag_name' => '--',
                                    'promotion_image' => $resvalue['promotion_image'],
                                    'IsActive' => ($resvalue['IsActive'] == 1) ? 'Active' : 'Deactive',
                                    'IsFeatured' => $resvalue['IsFeatured'],
                                ];
                }else if($resvalue['templatetype'] == 2){
                    
                    $tag_name = json_decode($resvalue['promotion_name']);
                
                    $tag_nameFin = $tag_name->promotion_tag1.'<br>'.$tag_name->promotion_tag2.'<br>'.$tag_name->promotion_tag3.'<br>'.$tag_name->promotion_tag4;
                    $resultsetArr[] =   [
                                    'promotionId' =>$resvalue['promotionId'],
                                    'templatetype' => $resultCategory->prom_cat_two,
                         'templatetypeId' => 2,
                                    'promotion_name' => '--',
                                    'tag_name' => $tag_nameFin,
                                    'promotion_image' => $resvalue['promotion_image'],
                                    'IsActive' => ($resvalue['IsActive'] == 1) ? 'Active' : 'Deactive',
                                    'IsFeatured' => $resvalue['IsFeatured'],
                                ];
                }else if($resvalue['templatetype'] == 3){
                     $resultsetArr[] =   [
                                    'promotionId' =>$resvalue['promotionId'],
                                     'templatetype' => $resultCategory->prom_cat_three,
                          'templatetypeId' => 3,
                                    'promotion_name' => '--',
                                    'tag_name' => '--',
                                    'promotion_image' => $resvalue['promotion_image'],
                                    'IsActive' => ($resvalue['IsActive'] == 1) ? 'Active' : 'Deactive',
                                    'IsFeatured' => $resvalue['IsFeatured'],
                                ];
                }
                 
            }

            $result = Zend_Json::encode($resultsetArr);
            $newResult = Zend_Json::decode($result, false);
            $finalResult["total"] = $resulsetold[0]['promotionId'];
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
    
    public function editpromotionAction()
    {
//Check admin logedin or not
        $this->checklogin();
        $crud = new Admin_Model_CRUD();

        $pId = (int)$this->getRequest()->getParam("id");
          $resultCategory = $crud->getCmsdata('tbl_promotion_category', ['*'], ['prom_cat_id'], ['prom_cat_id'=>'DESC']);
             
         if( $this->getRequest()->isPost() ) {
            $getData = $this->getRequest()->getPost();          
            if($getData) {
//                echo "<pre>"; print_r($getData); die; 
                //-------Start Code for Approve and Publish content---------//
               if (isset($getData['save']) == "Save") {
                
                if($getData['templatetype'] == 1){
                    
                    $promotion_image1 = $_FILES['promotion_image1']['name'];

                    
                    if (!empty($promotion_image1)) {
                        $orignalFolderName = $_SERVER["DOCUMENT_ROOT"] . "/" . $this->promoimageUrl;
                        $fileExtion = $this->_helper->General->getFileExtension($promotion_image1);
                        $bfileName = $this->current_time . 'promo'.'_1.' . $fileExtion;
                        if (!file_exists($orignalFolderName)) {
                            mkdir($orignalFolderName, $this->DIR_WRITE_MODE, true);
                        }
                        $originalSmallFolder = $orignalFolderName . "/small";
                        if (!file_exists($originalSmallFolder)) {
                            mkdir($originalSmallFolder, $this->DIR_WRITE_MODE, true);
                        }

                        $temp_file_name = $_FILES["promotion_image1"]["tmp_name"]; // temprary file name

                        @move_uploaded_file($temp_file_name, $orignalFolderName . "/" . $bfileName);

                        @copy($orignalFolderName . '/' . $bfileName, $originalSmallFolder . "/" . $bfileName); // copy uploaded file into this location directory
                        $objImageResize3 = new Catabatic_Imageresize($originalSmallFolder . '/' . $bfileName);
                        $objImageResize3->resizeImage($this->img_w_banner1, $this->img_h_banner1, 'exact'); // param : width , height , (exact|portrait|landscape|auto|crop)
                        $objImageResize3->saveImage($originalSmallFolder . '/' . $bfileName);

                    }
                                        
                  $editPageData = [
                      'templatetype' =>$getData['templatetype'],
                      'promotion_name' =>$getData['promotion_name1'],
                      'promotion_url' =>$getData['promotion_url1'],
                      'tab_type' =>isset($getData['opt1']) ? $getData['opt1'] : 0 ,
                      'promotion_description' => $getData['promotion_description'],
                      'IsActive' =>1,
                      'IsmarkForDel' =>0,
                  ];  
                  if($bfileName != ''){
                      $editPageData['promotion_image'] = $bfileName;
                  }
                  
                }else if($getData['templatetype'] == 2){
                     
                     $promotion_image2 = $_FILES['promotion_image2']['name'];
                   
                    if (!empty($promotion_image2)) {
                        $orignalFolderName = $_SERVER["DOCUMENT_ROOT"] . "/" . $this->promoimageUrl;
                        $fileExtion = $this->_helper->General->getFileExtension($promotion_image2);
                        $bfileName = $this->current_time . 'promo'.'_2.' . $fileExtion;
                        if (!file_exists($orignalFolderName)) {
                            mkdir($orignalFolderName, $this->DIR_WRITE_MODE, true);
                        }
                        $originalSmallFolder = $orignalFolderName . "/small";
                        if (!file_exists($originalSmallFolder)) {
                            mkdir($originalSmallFolder, $this->DIR_WRITE_MODE, true);
                        }

                        $temp_file_name = $_FILES["promotion_image2"]["tmp_name"]; // temprary file name

                        @move_uploaded_file($temp_file_name, $orignalFolderName . "/" . $bfileName);

                        @copy($orignalFolderName . '/' . $bfileName, $originalSmallFolder . "/" . $bfileName); // copy uploaded file into this location directory
                        $objImageResize3 = new Catabatic_Imageresize($originalSmallFolder . '/' . $bfileName);
                        $objImageResize3->resizeImage($this->img_w_banner2, $this->img_h_banner2, 'exact'); // param : width , height , (exact|portrait|landscape|auto|crop)
                        $objImageResize3->saveImage($originalSmallFolder . '/' . $bfileName);

                    }
                    
                    $promotion_urlArr = ['promotion_tag_url1'=>$getData['promotion_tag_url1'],'promotion_tag_url2'=>$getData['promotion_tag_url2'],'promotion_tag_url3'=>$getData['promotion_tag_url3'],'promotion_tag_url4'=>$getData['promotion_tag_url4']];
                    $promotion_tagArr = ['promotion_tag1'=>$getData['promotion_tag1'],'promotion_tag2'=>$getData['promotion_tag2'],'promotion_tag3'=>$getData['promotion_tag3'],'promotion_tag4'=>$getData['promotion_tag4']];
                    $tabTypelArr = ['tagopt1'=>isset($getData['tagopt1']) ? 1:0,'tagopt2'=>isset($getData['tagopt2']) ? 1: 0,'tagopt3'=>isset($getData['tagopt3']) ? 1: 0,'tagopt4'=>isset($getData['tagopt4']) ? 1 :0];
                     
                  $editPageData = [
                      'templatetype' =>$getData['templatetype'],
                      'promotion_name' =>  json_encode($promotion_tagArr),
                      'promotion_url' =>  json_encode($promotion_urlArr),
                      'tab_type' =>  json_encode($tabTypelArr) ,
                      'promotion_description' => '',
                      'IsActive' =>1,
                      'IsmarkForDel' =>0,
                  ];    
                  if($bfileName != ''){
                      $editPageData['promotion_image'] = $bfileName;
                  }
                }else if($getData['templatetype'] == 3){
                    $promotion_image3 = $_FILES['promotion_image3']['name'];
                   
                    if (!empty($promotion_image3)) {
                        $orignalFolderName = $_SERVER["DOCUMENT_ROOT"] . "/" . $this->promoimageUrl;
                        $fileExtion = $this->_helper->General->getFileExtension($promotion_image3);
                        $bfileName = $this->current_time . 'promo'.'_3.' . $fileExtion;
                        if (!file_exists($orignalFolderName)) {
                            mkdir($orignalFolderName, $this->DIR_WRITE_MODE, true);
                        }
                        $originalSmallFolder = $orignalFolderName . "/small";
                        if (!file_exists($originalSmallFolder)) {
                            mkdir($originalSmallFolder, $this->DIR_WRITE_MODE, true);
                        }

                        $temp_file_name = $_FILES["promotion_image3"]["tmp_name"]; // temprary file name

                        @move_uploaded_file($temp_file_name, $orignalFolderName . "/" . $bfileName);

                        @copy($orignalFolderName . '/' . $bfileName, $originalSmallFolder . "/" . $bfileName); // copy uploaded file into this location directory
                        $objImageResize3 = new Catabatic_Imageresize($originalSmallFolder . '/' . $bfileName);
                        $objImageResize3->resizeImage($this->img_w_banner3, $this->img_h_banner3, 'exact'); // param : width , height , (exact|portrait|landscape|auto|crop)
                        $objImageResize3->saveImage($originalSmallFolder . '/' . $bfileName);

                    }
                    
                    
                  $editPageData = [
                      'templatetype' =>$getData['templatetype'],
                      'promotion_name' =>'',
                      'promotion_url' =>$getData['promotion_url3'],
                      'tab_type' =>isset($getData['opt3']) ? $getData['opt3'] : 0 ,
                       'promotion_description' => '',
                      'IsActive' =>1,
                      'IsmarkForDel' =>0,
                  ];    
                    if($bfileName != ''){
                      $editPageData['promotion_image'] = $bfileName;
                    }
                }
//                                    echo "<pre>";print_r($savePageData);die;
                
                 //echo "<pre>"; print_r($editPageData); die;
                    $crud->rv_update($this->table, $editPageData, ['promotionId =?' => $pId]);
                $this->view->successMessage = "Page content has been saved successfully.";
                $this->_helper->flashMessenger->addMessage("Page content has been added successfully.");
                $this->_redirect("/admin/homepromotion/index");
            
             }  
             
          }
        }

        $result = $crud->getCmsdata($this->table, ['*'], ['promotionId'=>$pId], ['promotionId'=>'DESC']);
//        echo "<pre>";print_r($result);die;
        if($result->templatetype == 2){
            
        
        $promotion_name = json_decode($result->promotion_name);
        $promotion_url = json_decode($result->promotion_url);
        $tab_type = json_decode($result->tab_type);
           $editdata["promotionId"] = @$result->promotionId;
        $editdata["templatetype"] = @$result->templatetype;
        foreach ($promotion_name as $prkey => $prvalue) {
            $editdata[$prkey] = $prvalue;
        }
        foreach ($promotion_url as $urkey => $urvalue) {
            $editdata[$urkey] = $urvalue;
        }
        
        foreach ($tab_type as $trkey => $trvalue) {
            $editdata[$trkey] = $trvalue;
        }
//        echo "<pre>";print_r($editdata);die;  
        

        $editdata["promotion_image"] = @$result->promotion_image;

        $editdata["IsActive"] = @$result->IsActive;
        }else{
            $editdata["promotionId"] = @$result->promotionId;
        $editdata["templatetype"] = @$result->templatetype;
        $editdata["promotion_name"] = @$result->promotion_name;
        $editdata["promotion_description"] = @$result->promotion_description;
        $editdata["promotion_image"] = @$result->promotion_image;
        $editdata["promotion_url"] = @$result->promotion_url;
        $editdata["tab_type"] = @$result->tab_type;
        $editdata["IsActive"] = @$result->IsActive;
        }
        
//       echo "<pre>";print_r($editdata);die;
        $this->view->promotionId = @$result->promotionId; 
        $this->view->promotion_image = @$result->promotion_image; 
        $this->view->editdata = $editdata;
        $this->view->templatetype = $result->templatetype;
        
           $this->view->resultCategory   = $resultCategory;          
    }
    
     
    public function addpromotionAction()
    {
       
//Check admin logedin or not
        $this->checklogin();
        $crud = new Admin_Model_CRUD();
        $pId = (int)$this->getRequest()->getParam("id");
        $resultCategory = $crud->getCmsdata('tbl_promotion_category', ['*'], ['prom_cat_id'], ['prom_cat_id'=>'DESC']);
            
               
        if( $this->getRequest()->isPost() ) {
            $getData = $this->getRequest()->getPost();          
            if($getData) {
                
                //-------Start Code for Approve and Publish content---------//
               if (isset($getData['save']) == "Save") {
                
                if($getData['templatetype'] == 1){
                    
                    $promotion_image1 = $_FILES['promotion_image1']['name'];

                    $bfileName = '';
                    if (!empty($promotion_image1)) {
                        $orignalFolderName = $_SERVER["DOCUMENT_ROOT"] . "/" . $this->promoimageUrl;
                        $fileExtion = $this->_helper->General->getFileExtension($promotion_image1);
                        $bfileName = $this->current_time . 'promo'.'_1.' . $fileExtion;
                        if (!file_exists($orignalFolderName)) {
                            mkdir($orignalFolderName, $this->DIR_WRITE_MODE, true);
                        }
                        $originalSmallFolder = $orignalFolderName . "/small";
                        if (!file_exists($originalSmallFolder)) {
                            mkdir($originalSmallFolder, $this->DIR_WRITE_MODE, true);
                        }

                        $temp_file_name = $_FILES["promotion_image1"]["tmp_name"]; // temprary file name

                        @move_uploaded_file($temp_file_name, $orignalFolderName . "/" . $bfileName);

                        @copy($orignalFolderName . '/' . $bfileName, $originalSmallFolder . "/" . $bfileName); // copy uploaded file into this location directory
                        $objImageResize3 = new Catabatic_Imageresize($originalSmallFolder . '/' . $bfileName);
                        $objImageResize3->resizeImage($this->img_w_banner1, $this->img_h_banner1, 'exact'); // param : width , height , (exact|portrait|landscape|auto|crop)
                        $objImageResize3->saveImage($originalSmallFolder . '/' . $bfileName);

                    }
                                        
                  $savePageData = [
                      'templatetype' =>$getData['templatetype'],
                      
                      'promotion_name' =>$getData['promotion_name1'],
                      'promotion_image' =>$bfileName,
                      'promotion_url' =>$getData['promotion_url1'],
                      'tab_type' =>isset($getData['opt1']) ? $getData['opt1'] : 0 ,
                      'promotion_description' => $getData['promotion_description'],
                      'IsActive' =>1,
                      'IsmarkForDel' =>0,
                  ];     
                }else if($getData['templatetype'] == 2){
                     
                     $promotion_image2 = $_FILES['promotion_image2']['name'];
                    $bfileName = '';
                    if (!empty($promotion_image2)) {
                        $orignalFolderName = $_SERVER["DOCUMENT_ROOT"] . "/" . $this->promoimageUrl;
                        $fileExtion = $this->_helper->General->getFileExtension($promotion_image2);
                        $bfileName = $this->current_time . 'promo'.'_2.' . $fileExtion;
                        if (!file_exists($orignalFolderName)) {
                            mkdir($orignalFolderName, $this->DIR_WRITE_MODE, true);
                        }
                        $originalSmallFolder = $orignalFolderName . "/small";
                        if (!file_exists($originalSmallFolder)) {
                            mkdir($originalSmallFolder, $this->DIR_WRITE_MODE, true);
                        }

                        $temp_file_name = $_FILES["promotion_image2"]["tmp_name"]; // temprary file name

                        @move_uploaded_file($temp_file_name, $orignalFolderName . "/" . $bfileName);

                        @copy($orignalFolderName . '/' . $bfileName, $originalSmallFolder . "/" . $bfileName); // copy uploaded file into this location directory
                        $objImageResize3 = new Catabatic_Imageresize($originalSmallFolder . '/' . $bfileName);
                        $objImageResize3->resizeImage($this->img_w_banner2, $this->img_h_banner2, 'exact'); // param : width , height , (exact|portrait|landscape|auto|crop)
                        $objImageResize3->saveImage($originalSmallFolder . '/' . $bfileName);

                    }
                    
                    $promotion_urlArr = ['promotion_tag_url1'=>$getData['promotion_tag_url1'],'promotion_tag_url2'=>$getData['promotion_tag_url2'],'promotion_tag_url3'=>$getData['promotion_tag_url3'],'promotion_tag_url4'=>$getData['promotion_tag_url4']];
                    $promotion_tagArr = ['promotion_tag1'=>$getData['promotion_tag1'],'promotion_tag2'=>$getData['promotion_tag2'],'promotion_tag3'=>$getData['promotion_tag3'],'promotion_tag4'=>$getData['promotion_tag4']];
                    $tabTypelArr = ['tagopt1'=>isset($getData['tagopt1']) ? 1:0,'tagopt2'=>isset($getData['tagopt2']) ? 1: 0,'tagopt3'=>isset($getData['tagopt3']) ? 1: 0,'tagopt4'=>isset($getData['tagopt4']) ? 1 :0];
                     
                  $savePageData = [
                      'templatetype' =>$getData['templatetype'],
                     
                      'promotion_name' =>  json_encode($promotion_tagArr),
                      'promotion_image' =>$bfileName,
                      'promotion_url' =>  json_encode($promotion_urlArr),
                      'tab_type' =>  json_encode($tabTypelArr) ,
                      'promotion_description' =>'',
                      'IsActive' =>1,
                      'IsmarkForDel' =>0,
                  ];     
                }else if($getData['templatetype'] == 3){
                    $promotion_image3 = $_FILES['promotion_image3']['name'];
                    $bfileName = '';
                    if (!empty($promotion_image3)) {
                        $orignalFolderName = $_SERVER["DOCUMENT_ROOT"] . "/" . $this->promoimageUrl;
                        $fileExtion = $this->_helper->General->getFileExtension($promotion_image3);
                        $bfileName = $this->current_time . 'promo'.'_3.' . $fileExtion;
                        if (!file_exists($orignalFolderName)) {
                            mkdir($orignalFolderName, $this->DIR_WRITE_MODE, true);
                        }
                        $originalSmallFolder = $orignalFolderName . "/small";
                        if (!file_exists($originalSmallFolder)) {
                            mkdir($originalSmallFolder, $this->DIR_WRITE_MODE, true);
                        }

                        $temp_file_name = $_FILES["promotion_image3"]["tmp_name"]; // temprary file name

                        @move_uploaded_file($temp_file_name, $orignalFolderName . "/" . $bfileName);

                        @copy($orignalFolderName . '/' . $bfileName, $originalSmallFolder . "/" . $bfileName); // copy uploaded file into this location directory
                        $objImageResize3 = new Catabatic_Imageresize($originalSmallFolder . '/' . $bfileName);
                        $objImageResize3->resizeImage($this->img_w_banner3, $this->img_h_banner3, 'exact'); // param : width , height , (exact|portrait|landscape|auto|crop)
                        $objImageResize3->saveImage($originalSmallFolder . '/' . $bfileName);

                    }
                    
                    
                  $savePageData = [
                      'templatetype' =>$getData['templatetype'],
                      
                      'promotion_name' =>'',
                      'promotion_image' =>$bfileName,
                      'promotion_url' =>$getData['promotion_url3'],
                      'tab_type' =>isset($getData['opt3']) ? $getData['opt3'] : 0 ,
                      'promotion_description' => '',
                      'IsActive' =>1,
                      'IsmarkForDel' =>0,
                  ];    
 
                }
//                                    echo "<pre>";print_r($savePageData);die;
                

                
              
                $crud->rv_insert($this->table, $savePageData);
                $this->view->successMessage = "Page content has been saved successfully.";
                $this->_helper->flashMessenger->addMessage("Page content has been added successfully.");
                $this->_redirect("/admin/homepromotion/index");
            
             }  
             
          }
        }

      
        $this->view->messages   = $this->_helper->flashMessenger->getMessages();
        $this->view->resultCategory   = $resultCategory;
        
                    
    }
    public function promotioncategoryAction(){
        
        $this->checklogin();
        $crud = new Admin_Model_CRUD();
        
        if( $this->getRequest()->isPost() ) {
            $getData = $this->getRequest()->getPost(); 
            if($getData) {
                //-------Start Code for Approve and Publish content---------//
        if (isset($getData['save']) == "Save") {
            $pId = $getData['prom_cat_id'];
            $editPageData = [
                      'prom_cat_one' =>$getData['categoryheadingOne'],
                      'prom_cat_two' =>$getData['categoryheadingTwo'],
                      'prom_cat_three' =>$getData['categoryheadingThree'],
                      'IsActive' =>1,
                      'IsmarkForDel' =>0,
                  ];    

           $prom_cat_id = $crud->rv_update('tbl_promotion_category', $editPageData, ['prom_cat_id =?' => $pId]);

           
        }
        
        }
         $this->view->successMessage = "Page content has been updated successfully.";
        }
         $result = $crud->getCmsdata('tbl_promotion_category', ['*'], ['prom_cat_id'], ['prom_cat_id'=>'DESC']);
         $editdata["prom_cat_one"] = @$result->prom_cat_one;
         $editdata["prom_cat_two"] = @$result->prom_cat_two;
         $editdata["prom_cat_three"] = @$result->prom_cat_three;
         $editdata["prom_cat_id"] = @$result->prom_cat_id;
         $this->view->editdata = $editdata;
    }
    
    
    
    public function deletepromotionAction() {
        $this->checklogin();
        $crud = new Admin_Model_CRUD();
        $tId = (int) $this->getRequest()->getParam("id");
        //echo $tId;die;
        if ($tId) {
            $checkdata = $crud->rv_select_row($this->table, ['promotionId'], ['promotionId' => $tId], ['promotionId' => 'asc']);
            if (count($checkdata) > 0) {
                $crud->rv_update($this->table, ['IsMarkForDel'=> 1], ['promotionId =?'=>$tId]);
                $this->_helper->flashMessenger->addMessage("Delete successfully.");
                $this->_redirect("/admin/homepromotion/index");
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
        $type = (int) $this->getRequest()->getParam("type");
        if ($tId) {
            try {
                $updatedata = [
                    'IsFeatured' => $val
                ];
                
                $result = $crud->rv_update($this->table, $updatedata, ['promotionId =?' => $tId]);
                if($type == 1){
                    $resultset = $crud->rv_select_all($this->table, ['*'] ,['templatetype'=>$type,'IsFeatured'=>1,'IsMarkForDel'=>0], ['promotionId'=>'ASC'] );
                if(count($resultset)>10){
                   $result_message = ['status' => false, 'message' => 'Limit Exceed!!'];
                    echo Zend_Json::encode($result_message);
                    $updatedata = [
                    'IsFeatured' => 0
                ];
                    $result = $crud->rv_update($this->table, $updatedata, ['promotionId =?' => $tId]);
                    exit; 
                } 
                }elseif($type == 2){
                     $resultset = $crud->rv_select_all($this->table, ['*'] ,['templatetype'=>$type,'IsFeatured'=>1,'IsMarkForDel'=>0], ['promotionId'=>'ASC'] );
                if(count($resultset)>1){
                   $result_message = ['status' => false, 'message' => 'Limit Exceed!!'];
                    echo Zend_Json::encode($result_message);
                    $updatedata = [
                    'IsFeatured' => 0
                ];
                    $result = $crud->rv_update($this->table, $updatedata, ['promotionId =?' => $tId]);
                    exit; 
                }
                }else if($type == 3){
                     $resultset = $crud->rv_select_all($this->table, ['*'] ,['templatetype'=>$type,'IsFeatured'=>1,'IsMarkForDel'=>0], ['promotionId'=>'ASC'] );
                if(count($resultset)>1){
                   $result_message = ['status' => false, 'message' => 'Limit Exceed!!'];
                    echo Zend_Json::encode($result_message);
                    $updatedata = [
                    'IsFeatured' => 0
                ];
                    $result = $crud->rv_update($this->table, $updatedata, ['promotionId =?' => $tId]);
                    exit; 
                }
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