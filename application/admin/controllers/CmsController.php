<?php
/***************************************************************
* Catabatic Technology Pvt. Ltd.
* File Name :CmsController.php
* File Description :Cms controller managed all staic content
* Created By : Praveen Kumar
* Created Date: 04-September-2014
***************************************************************/

class Admin_CmsController extends Zend_Controller_Action
{
    var $dbAdapter;
    var $perPageLimit;
    public function init()
    {
       /*Initialize db and session access */
       $aConfig = $this->getInvokeArg('bootstrap')->getOptions();
       $this->siteurl = $aConfig['bootstrap']['siteUrl']; 
       $this->perPageLimit = $aConfig['bootstrap']['perPageLimit']; 
       $this->dbAdapter = Zend_Db_Table::getDefaultAdapter(); 
       $auth = Zend_Auth::getInstance();
       $authStorage = $auth->getStorage()->read();
       $this->username = $authStorage->username;
       $this->admin_type = $authStorage->type;
    }
    
    /**
    * index() method is used to admin login for form call
    * @param Null
    * @return Array 
    */
    public function indexAction()
    {
       $this->checklogin();
    }
    
    /**
    * addmenu() method is used to admin can add menu
    * @param string
    * @return ture 
    */
    public function addmenuAction()
    {
        //Check admin logedin or not
        $this->checklogin();
        $cms = new Admin_Model_Cms();
        $form = new Admin_Form_Menu();
        //echo "<pre>";print_r($form);die;
        $this->view->form = $form;
        $successMessage ="";
        
        if ($this->getRequest()->isPost()) {   
            $getData = $this->getRequest()->getPost();
            //echo "<pre>";print_r($getData); die;
         if($form->isValid($getData)){
             
                //-----------------Check label name exists or not-------------//
                //$checkLabelName = $cms->checkLabelNameOrUrlNameExistsOrNot($column_name="level",strtoupper($getData['level']));
                //echo "<pre>";print_r($checkPageName);die;
                //if($checkLabelName<=0) {
                    
                    //-----------------Check url name exists or not-------------//
                    //$checkUrlName = $cms->checkLabelNameOrUrlNameExistsOrNot($column_name="url",$getData['url']);
                    //echo "<pre>";print_r($checkPageName);die;
                    //if($checkUrlName<=0) { 
                        $orignalFIleName = $_FILES['image']['name']; 
                        $ext = @substr($_FILES['image']['name'], strrpos($_FILES['image']['name'], '.'));
                        $image = "menu_".time().$ext;
                        $upload = new Zend_File_Transfer_Adapter_Http();
                        $upload->setDestination("upload/menu/");
                        $upload->addFilter('Rename', "upload/menu/".$image);
                        $file = $upload->getFileName();
                        if($orignalFIleName!="")
                        {
                           $addimage = $image;
                        } else {
                            $addimage = "";
                        }
                        try { 
                        $upload->receive();
                        if($getData['hierarchy_menu_id']!="" && $getData['hierarchy_sub_menu_id']=="" && $getData['hierarchy_subthreelb_menu_id']=="")
                        {
                          $parent_id = $getData['hierarchy_menu_id'];
                        } else if($getData['hierarchy_menu_id']!="" && $getData['hierarchy_sub_menu_id']!="" && $getData['hierarchy_subthreelb_menu_id']=="")
                        {
                          $parent_id = $getData['hierarchy_sub_menu_id'];
                        } else if($getData['hierarchy_menu_id']!="" && $getData['hierarchy_sub_menu_id']!="" && $getData['hierarchy_subthreelb_menu_id']!="")
                        {
                          $parent_id = $getData['hierarchy_subthreelb_menu_id'];
                        }

                        $addData = array(
                                     'parent_id'=>$parent_id,
                                     'level'=>strtoupper($getData['level']),
                                'sub_level_name'=>$getData['sub_level_name'],
                                     'url'=>$getData['url'],
                               'external_url'=>$getData['external_url'],
                                     'image'=>$addimage,
                                    'status'=>$getData['status'],
                                     'createdBy'=>$this->username,
                                     'createdOn'=>time()
                                   );
                        //echo "<pre>";print_r($addData); die;
                        $cms->add($addData);
                        $this->view->successMessage ="Menu has been added";           
                        }
                        catch (Zend_File_Transfer_Exception $e) {
                              $e->getMessage();
                        }
                    //}else {
                       // $this->view->error1Message ="url name already exists, please choose another url";
                    //}
               // }else {
                    // $this->view->errorMessage ="Link text already exists, please choose another name"; 
               // }      
          }   
        }
    }
    
    /**
    * editmenu() method is used to admin can add menu
    * @param string
    * @return ture 
    */
    public function editmenuAction()
    {
        //Check admin logedin or not
        $this->checklogin();
        $cms = new Admin_Model_Cms();
        $mId = (int) $this->getRequest()->getParam("id");
        $form = new Admin_Form_Editmenu();
        $form->setMethod("POST");
        $form->setAction("admin/cms/editmenu/id/".$mId);
        $form->setName("edit_menu");
        $successMessage ="";
        if ($this->getRequest()->isPost()) {   
            $getData = $this->getRequest()->getPost();
            //echo "<pre>";print_r($getData); die;
         if($getData){ 
             
                //-----------------Check label name exists or not-------------//
                $menu_id = (int) @$getData['id'];
                //$checkLabelName = $cms->checkEditLabelNameOrUrlNameExistsOrNot($menu_id,$column_name="level",strtoupper($getData['level']));
                //echo $checkLabelName;die;
                //if($checkLabelName<=0) { 
                    
                    //-----------------Check url name exists or not-------------//
                    //$checkUrlName = $cms->checkEditLabelNameOrUrlNameExistsOrNot($menu_id,$column_name="url",$getData['url']);
                    //echo $checkUrlName;die;
                    //if($checkUrlName<=0) { 
                        $orignalFIleName = $_FILES['image']['name']; 
                        $ext = @substr($_FILES['image']['name'], strrpos($_FILES['image']['name'], '.'));
                        $image = "menu_".time().$ext; 
                        $upload = new Zend_File_Transfer_Adapter_Http();
                        $upload->setDestination("upload/menu/");
                        $upload->addFilter('Rename', "upload/menu/".$image);
                        $file = $upload->getFileName();
                        $result = $cms->getMenuDetailsById($getData['id']);
                        //echo "<pre>";print_r($result); die;
                        if($orignalFIleName!=""){ 
                            $path_image = "upload/menu/".$result->image;
                            @unlink($path_image);
                            $image_edit = $image;    
                        }else {
                            $image_edit = $result->image;
                        }
                        try {
                        $upload->receive();
                        if($result->id !="" && $result->parent_id ==0)
                        {
                          $parent_id = 0;
                        }else if($result->id !="" && $result->parent_id !=0)
                        { 
                          $parent_id = $result->parent_id;
                        }

                        $hierarchy_menu_type_st = @implode(",",@$getData['hierarchy_menu_type']);

                        $editData = array(
                                    'parent_id'=>$parent_id,
                                    'level'=>strtoupper($getData['level']),
                            'sub_level_name'=>$getData['sub_level_name'],
                                    'url'=>$getData['url'],
                            'external_url'=>$getData['external_url'],
                                    'image'=>$image_edit,
                                    'hierarchy_menu_type'=>$hierarchy_menu_type_st,
                                    'status'=>$getData['status'],
                                    'updatedBy'=>$this->username,
                                    'updatedOn'=>time()
                                  );
                        //echo "<pre>";print_r($editData);die;
                        $where = array('id =?'=>$getData['id']);
                        $cms->edit($editData,$where);
                        //$this->view->successMessage ="Menu has been updated"; 
                        $this->_redirect("admin/cms/managemenu");
                        }
                        catch (Zend_File_Transfer_Exception $e) {
                              $e->getMessage();
                        }
                    //}else {
                     //   $this->view->error1Message ="url name already exists, please choose another url";
                    //}     
               // } else{
                 //   $this->view->errorMessage ="Link text already exists, please choose another name";
               // }      
          }   
        }
        
        $result = $cms->getMenuDetailsById($mId);
        $editdata["id"] = @$result["id"];
        $editdata["hierarchy_menu_id"] = @$result["parent_id"];
        $editdata["level"] = @$result["level"];
        $editdata["sub_level_name"] = @$result["sub_level_name"];
        $editdata["url"] = @$result["url"];
        $editdata["external_url"] = @$result["external_url"];
        $editdata["page_key"] = @$result["url"];
        $editdata["image"] = @$result["image"];
        $editdata["status"] = @$result["status"];
        $editdata["hierarchy_menu_type"] = @explode(",",@$result["hierarchy_menu_type"]);
        $form->populate($editdata);
        
        if(@$result["parent_id"]!="") { 
        $res_subthreelb_menu_data = $cms->getSubThreelbMenuDetailsById($result["parent_id"]);
        $this->view->threelb_level_name = @$res_subthreelb_menu_data["level"];
        }
        if(@$res_subthreelb_menu_data["parent_id"]!="") { 
            $res_subtwolb_menu_data = $cms->getSubTwolbMenuDetailsById($res_subthreelb_menu_data["parent_id"]);
            $this->view->twolb_level_name = @$res_subtwolb_menu_data["level"];
        }
        if(@$res_subtwolb_menu_data["parent_id"]!=""){
            $res_sub_menu_data = $cms->getSubMenuDetailsById($res_subtwolb_menu_data["parent_id"]);
            //echo "<pre>";print_r($res_sub_menu_data); die;
            $this->view->sub_level_name = @$res_sub_menu_data["level"];  
        }    
        
        $this->view->image = @$result["image"];
        $this->view->parent_id = @$result["parent_id"];
        $this->view->hierarchy_menu_type = @$result["hierarchy_menu_type"];
        $this->view->form = $form;
    }
    
    /**
    * managemenu() method is used to get all menu list
    * @param string
    * @return ture 
    */
    public function managemenuAction()
    {
        //Check admin logedin or not
        $this->checklogin();
        $cms = new Admin_Model_Cms();
        $result = $cms->getAllMenusList();
        //echo "<pre>";print_r($result);die;
                
//        $page=$this->_getParam('page',1);
//        $paginator = Zend_Paginator::factory($result);      
//        $paginator->setCurrentPageNumber($this->getRequest()->getParam('page')); // page number
//        $perPage = $paginator->setItemCountPerPage($this->perPageLimit); // number of items to show per page
//        $this->view->paginator = $paginator;
        $this->view->totalrec = $result;
    }
    
    public function getSubMenuAction() { 
    
        $this->_helper->layout()->disableLayout('');
        if ($this->_request->isXmlHttpRequest()) { 
            $hierarchy_menu = $this->getRequest()->getParam('hierarchy_menu');
            $hierarchy_menu_id = $this->getRequest()->getParam('mid');

            $cms = new Admin_Model_Cms();
            $submenuList = $cms->getAllSubMenuList($hierarchy_menu_id);
            //echo "<pre>";print_r($submenuList);die;
            
            $select = '<label style="padding-left:20px;">Hierarchy Sub Menu</label>
                       <img src="'.$this->siteurl.'images/curve_arrow.png" border="0">
                       <select name="hierarchy_sub_menu_id" id="hierarchy_sub_menu_id" class="input-xlarge" onclick="getSubThreelavelMenu(this.value)">
                       <option value="">--Please Select--</option>';
            if($hierarchy_menu_id!=""){
                foreach($submenuList as $key=>$val){
                 $select .= '<option value="'.$val['id'].'">'.$val['level'].'</option>';  
                }
            }    
            $select .='</select>';
            
            echo $select;  
            //$this->_helper->json($subcatList);
        }
        exit;
     }
     
     public function getSubThreelbMenuAction() { 
    
        $this->_helper->layout()->disableLayout('');
        if ($this->_request->isXmlHttpRequest()) { 
            $hierarchy_sub_menu = $this->getRequest()->getParam('hierarchy_sub_menu');
            $hierarchy_sub_menu_id = $this->getRequest()->getParam('smid');
            
            $cms = new Admin_Model_Cms();
            $subthreelbmenuList = $cms->getAllSubThreelbMenuList($hierarchy_sub_menu_id);
            //echo "<pre>";print_r($subthreelbmenuList);die;
            
            $sub_select = '<label style="padding-left:15px;">Hierarchy Sub Sub Menu</label>
                       <img src="'.$this->siteurl.'images/curve_arrow.png" border="0">
                       <select name="hierarchy_subthreelb_menu_id" id="hierarchy_subthreelb_menu_id" class="input-xlarge" onclick="getSubFourthlavelMenu(this.value)">
                       <option value="">--Please Select--</option>';
            if($hierarchy_sub_menu_id!=""){
                foreach($subthreelbmenuList as $key1=>$val1){
                 $sub_select .= '<option value="'.$val1['id'].'">'.$val1['level'].'</option>';  
                }
            }    
            $sub_select .='</select>';
            
            echo $sub_select;  
            //$this->_helper->json($subcatList);
        }
        exit;
     }
     
     public function getSubFourthlbMenuAction() { 
    
        $this->_helper->layout()->disableLayout('');
        if ($this->_request->isXmlHttpRequest()) { 
            $hierarchy_sub_fourthlb_menu = $this->getRequest()->getParam('hierarchy_sub_fourthlb_menu');
            $hierarchy_fourth_sub_menu_id = $this->getRequest()->getParam('ssmid');
            
            $cms = new Admin_Model_Cms();
            $subfourthlbmenuList = $cms->getAllSubFourthlbMenuList($hierarchy_fourth_sub_menu_id);
            //echo "<pre>";print_r($subthreelbmenuList);die;
            
            $fourthlb_sub_select = '<label style="padding-left:15px;">Hierarchy Sub Sub Menu</label>
                       <img src="'.$this->siteurl.'images/curve_arrow.png" border="0">
                       <select name="hierarchy_subfourthlb_menu_id" id="hierarchy_subfourthlb_menu_id" class="input-xlarge">
                       <option value="">--Please Select--</option>';
            if($hierarchy_fourth_sub_menu_id!=""){
                foreach($subfourthlbmenuList as $key1=>$val1){
                 $fourthlb_sub_select .= '<option value="'.$val1['id'].'">'.$val1['level'].'</option>';  
                }
            }    
            $fourthlb_sub_select .='</select>';
            
            echo $fourthlb_sub_select;  
            //$this->_helper->json($subcatList);
        }
        exit;
     }
     
    /**
    * deleteMenu() method is used to delete menu details
    * @param string
    * @return ture 
    */
    public function deleteMenuAction()
    {
        //Check admin logedin or not
        $this->checklogin();
        //$this->_helper->layout()->disableLayout('');
        $hmId = (int) $this->getRequest()->getParam("id"); 
        $cms = new Admin_Model_Cms();
        $result = $cms->getMenuDetailsByMenuId($hmId);
        //echo "<pre>";print_r($result);die;
        if($result->image!=""){ 
            $path_image = "upload/menu/".$result->image;
            @unlink($path_image); 
        }
        $cms->delete($hmId);      
        $this->_redirect("admin/cms/managemenu");
    }
    
    
    /**
    * addquicklink() method is used to admin can add quick menu links
    * @param string
    * @return ture 
    */
    public function addquicklinkAction()
    {
        //Check admin logedin or not
        $this->checklogin();
        $cms = new Admin_Model_Cms();
        $form = new Admin_Form_Quicklinkmenu();
        $this->view->form =$form;
        $successMessage ="";
        
        if ($this->getRequest()->isPost()) {   
            $getData = $this->getRequest()->getPost();
            //echo "<pre>";print_r($getData);
         if($form->isValid($getData)){ 
                try { 
                 
                 //Start Code for quick_link_column1
                 $quickLinkColumn1EditData = $cms->getQuickLinkColumn123DataList($type='quick_link_column1');
                 if(count($quickLinkColumn1EditData)>0){

                     $quick_link_column1del = $cms->deleteQuickLinkColumn123Data($type="quick_link_column1");

                     if(count(@$getData['quick_link_menu_id1']) > 0)
                     {
                        foreach(@$getData['quick_link_menu_id1'] as $quick_link_val1)
                        {
                           $addData1 = array(
                                     'quick_link_menu_id'=>$quick_link_val1,
                                     'hierarchy_menu_type'=>'quick_link_column1',
                                     'createdBy'=>$this->username,
                                     'createdOn'=>date('Y-m-d H:i:s')
                                   );
                           //echo "<pre>";print_r($addData);die;
                           $cms->add_quick_link($addData1);
                        }
                     }
                 }else{

                     if(count(@$getData['quick_link_menu_id1']) > 0)
                     {
                        foreach(@$getData['quick_link_menu_id1'] as $quick_link_val1)
                        {
                           $addData1 = array(
                                     'quick_link_menu_id'=>$quick_link_val1,
                                     'hierarchy_menu_type'=>'quick_link_column1',
                                     'createdBy'=>$this->username,
                                     'createdOn'=>date('Y-m-d H:i:s')
                                   );
                           //echo "<pre>";print_r($addData);die;
                           $cms->add_quick_link($addData1);
                        }
                     } 
                 }
                 //End Code for quick_link_column1
                 
                 //Start Code for quick_link_column2
                 $quickLinkColumn2EditData = $cms->getQuickLinkColumn123DataList($type='quick_link_column2');
                 if(count($quickLinkColumn2EditData)>0){

                     $quick_link_column2del = $cms->deleteQuickLinkColumn123Data($type="quick_link_column2");
                     
                     if(count(@$getData['quick_link_menu_id2']) > 0)
                     {
                        foreach(@$getData['quick_link_menu_id2'] as $quick_link_val2)
                        {
                           $addData2 = array(
                                     'quick_link_menu_id'=>$quick_link_val2,
                                     'hierarchy_menu_type'=>'quick_link_column2',
                                     'createdBy'=>$this->username,
                                     'createdOn'=>date('Y-m-d H:i:s')
                                   );
                           //echo "<pre>";print_r($addData);die;
                           $cms->add_quick_link($addData2);
                        }
                     }
                 }else{

                     if(count(@$getData['quick_link_menu_id2']) > 0)
                     {
                        foreach(@$getData['quick_link_menu_id2'] as $quick_link_val2)
                        {
                           $addData2 = array(
                                     'quick_link_menu_id'=>$quick_link_val2,
                                     'hierarchy_menu_type'=>'quick_link_column2',
                                     'createdBy'=>$this->username,
                                     'createdOn'=>date('Y-m-d H:i:s')
                                   );
                           //echo "<pre>";print_r($addData);die;
                           $cms->add_quick_link($addData2);
                        }
                     }
                 }
                  //End Code for quick_link_column2
                 
                 //Start Code for quick_link_column3
                 $quickLinkColumn3EditData = $cms->getQuickLinkColumn123DataList($type='quick_link_column3');
                 if(count($quickLinkColumn3EditData)>0){

                     $quick_link_column3del = $cms->deleteQuickLinkColumn123Data($type="quick_link_column3");
                     
                     if(count(@$getData['quick_link_menu_id3']) > 0)
                     {
                        foreach(@$getData['quick_link_menu_id3'] as $quick_link_val3)
                        {
                           $addData3 = array(
                                     'quick_link_menu_id'=>$quick_link_val3,
                                     'hierarchy_menu_type'=>'quick_link_column3',
                                     'createdBy'=>$this->username,
                                     'createdOn'=>date('Y-m-d H:i:s')
                                   );
                           //echo "<pre>";print_r($addData);die;
                           $cms->add_quick_link($addData3);
                        }
                     }
                 }else{
                     if(count(@$getData['quick_link_menu_id3']) > 0)
                     {
                        foreach(@$getData['quick_link_menu_id3'] as $quick_link_val3)
                        {
                           $addData3 = array(
                                     'quick_link_menu_id'=>$quick_link_val3,
                                     'hierarchy_menu_type'=>'quick_link_column3',
                                     'createdBy'=>$this->username,
                                     'createdOn'=>date('Y-m-d H:i:s')
                                   );
                           //echo "<pre>";print_r($addData);die;
                           $cms->add_quick_link($addData3);
                        }
                     }
                 }
                //End Code for quick_link_column3
                 
                     
                $this->view->successMessage ="Quick link menu has been saved successfully.";  
                
            }
            catch (Zend_File_Transfer_Exception $e) {
                  $e->getMessage();
            }
            
          }   
        }
        
        $quickLinkColumn1Data = $cms->getQuickLinkColumn123DataList($type='quick_link_column1');
        $this->view->quickLinkColumn1 = $quickLinkColumn1Data; 
        
        $quickLinkColumn2Data = $cms->getQuickLinkColumn123DataList($type='quick_link_column2');
        $this->view->quickLinkColumn2 = $quickLinkColumn2Data;
        
        $quickLinkColumn3Data = $cms->getQuickLinkColumn123DataList($type='quick_link_column3');
        $this->view->quickLinkColumn3 = $quickLinkColumn3Data;
        
        //$total_rows = 0;
        
//        $quickLinkMenuColumn1 = $cms->getQuickLinkMenuColumn123List($type='quick_link_column1');
//        $total_rows_Column1 = count($quickLinkMenuColumn1); 
//        if($total_rows_Column1 > 0){
//           $this->view->quickLinkMenuColumn1 = $quickLinkMenuColumn1; 
//        }else{
//           
//        }
//        echo "<pre>";print_r($quickLinkMenuColumn1);die;
//        $this->view->quickLinkColumn1 = $quickLinkColumn1Data;
//
//        if($total_rows>0){
//            $hierarchyMenuTypeList = $cms->getHierarchyMenuTypeList($type='quick_link');
//            $menuList = $cms->getQuickLinkMenuAndSubMenu($hierarchyMenuTypeList);
//            $this->view->menuList = $menuList;
//            $this->view->quickLinkMenuListCount = $total_rows;
//        } else {
//            $this->view->quickLinkMenuListCount = $total_rows;
//            $hierarchyMenuTypeList = $cms->getHierarchyMenuTypeList($type='quick_link');
//            $menuList = $cms->getQuickLinkMenuAndSubMenu($hierarchyMenuTypeList);
//            $this->view->menuList = $menuList;
//        }
     
    }
    
    
    /**
    * addtiledmenu() method is used to admin can add and edit tiled menu
    * @param string
    * @return ture 
    */
    public function addtiledmenuAction()
    {
        //Check admin logedin or not
        $this->checklogin();
        $cms = new Admin_Model_Cms();
        $form = new Admin_Form_Tiledmenu();
        $this->view->form =$form;
        $successMessage ="";
        
        if ($this->getRequest()->isPost()) {   
            $getData = $this->getRequest()->getPost();
            //echo "<pre>";print_r($getData); 
         if($form->isValid($getData)){ 
                try { 
                $quickLinkData = $cms->getQuickLinkDataList($type='tiled_menu');
                $hierarchyMenuTypeList = $cms->getHierarchyMenuTypeList($type='tiled_menu');
                //echo "<pre>";print_r($quickLinkData); die;
                $total_data_rows = count($hierarchyMenuTypeList);
                if(count($quickLinkData) <=0) { 
                    for($i=1;$i<=$total_data_rows;$i++)
                    {
                       if(@$getData['quick_link_menu_id_'.$i]!="")
                       {
                          $quick_link_menu_id = @implode(",",@$getData['quick_link_menu_id_'.$i]);
                       } else {
                          $quick_link_menu_id = "";
                       }
                       $addData = array(
                                 'hierarchy_menu_id'=>$getData['hierarchy_menu_id_'.$i],
                                 'quick_link_menu_id'=>$quick_link_menu_id,
                                 'hierarchy_menu_type'=>'tiled_menu',
                                 'createdBy'=>$this->username,
                                 'createdOn'=>date('Y-m-d H:i:s')
                               );
                       //echo "<pre>";print_r($addData);die;
                       $cms->add_quick_link($addData);
                    }    
                    $this->view->successMessage ="Tiled menu has been saved successfully.";  
                   } else {
                         
                        for($j=1;$j<=$total_data_rows;$j++)
                        {
                           if(@$getData['quick_link_menu_id_'.$j]!="")
                           {
                              $quick_link_menu_id = @implode(",",@$getData['quick_link_menu_id_'.$j]);
                           } else {
                              $quick_link_menu_id = "";
                           }
                           $hierarchy_tiled_menu_update_id = $getData['hierarchy_menu_id_'.$j];
                           $editData1 = array(
                                     'quick_link_menu_id'=>$quick_link_menu_id,
                                     'updatedBy'=>$this->username,
                                     'updatedOn'=>date('Y-m-d H:i:s')
                                   );
                           //echo "<pre>";print_r($addData1);die;
                           $cms->update_quick_link($editData1,$hierarchy_tiled_menu_update_id,$type='tiled_menu');
                        }    
                        $this->view->successMessage ="Tiled menu has been saved successfully."; 
                   } 
                }
                catch (Zend_File_Transfer_Exception $e) {
                      $e->getMessage();
                }
          }   
        }
        
        $tiledMenuData = $cms->getQuickLinkDataList($type='tiled_menu');
        $total_rows = count($tiledMenuData); 
        if($total_rows>0){ 
            $hierarchyMenuTypeList = $cms->getHierarchyMenuTypeList($type='tiled_menu');
            $tiledMenuList = $cms->getTiledMenuAndSubMenu($hierarchyMenuTypeList);
            $this->view->menuList = $tiledMenuList;
            $this->view->tiledMenuListCount = $total_rows;
        } else { 
            $this->view->tiledMenuListCount = $total_rows;
            $hierarchyMenuTypeList = $cms->getHierarchyMenuTypeList($type='tiled_menu');
            $tiledMenuList = $cms->getTiledMenuAndSubMenu($hierarchyMenuTypeList);
            //echo "<pre>";print_r($tiledMenuList);die;
            $this->view->menuList = $tiledMenuList;
        }
     
    }
    
    
    /**
    * addfootermenu() method is used to admin can add and edit tiled menu
    * @param string
    * @return ture 
    */
    public function addfootermenuAction()
    {
        //Check admin logedin or not
        $this->checklogin();
        $cms = new Admin_Model_Cms();
        $form = new Admin_Form_Footermenu();
        $this->view->form =$form;
        $successMessage ="";
        
        if ($this->getRequest()->isPost()) {   
            $getData = $this->getRequest()->getPost();
            //echo "<pre>";print_r($getData); 
         if($form->isValid($getData)){ 
                try { 
                $quickLinkData = $cms->getQuickLinkDataList($type='footer_menu');
                $hierarchyMenuTypeList = $cms->getHierarchyMenuTypeList($type='footer_menu');
                //echo "<pre>";print_r($quickLinkData); die;
                $total_data_rows = count($hierarchyMenuTypeList);
                if(count($quickLinkData) <=0) { 
                    for($i=1;$i<=$total_data_rows;$i++)
                    {
                       if(@$getData['quick_link_menu_id_'.$i]!="")
                       {
                          $quick_link_menu_id = @implode(",",@$getData['quick_link_menu_id_'.$i]);
                       } else {
                          $quick_link_menu_id = "";
                       }
                       $addData = array(
                                 'hierarchy_menu_id'=>$getData['hierarchy_menu_id_'.$i],
                                 'quick_link_menu_id'=>$quick_link_menu_id,
                                 'hierarchy_menu_type'=>'footer_menu',
                                 'createdBy'=>$this->username,
                                 'createdOn'=>date('Y-m-d H:i:s')
                               );
                       //echo "<pre>";print_r($addData);die;
                       $cms->add_quick_link($addData);
                    }    
                    $this->view->successMessage ="Footer menu has been saved successfully.";  
                   } else {
                         
                        for($j=1;$j<=$total_data_rows;$j++)
                        {
                           if(@$getData['quick_link_menu_id_'.$j]!="")
                           {
                              $quick_link_menu_id = @implode(",",@$getData['quick_link_menu_id_'.$j]);
                           } else {
                              $quick_link_menu_id = "";
                           }
                           $hierarchy_tiled_menu_update_id = $getData['hierarchy_menu_id_'.$j];
                           $editData1 = array(
                                     'quick_link_menu_id'=>$quick_link_menu_id,
                                     'updatedBy'=>$this->username,
                                     'updatedOn'=>date('Y-m-d H:i:s')
                                   );
                           //echo "<pre>";print_r($addData1);die;
                           $cms->update_quick_link($editData1,$hierarchy_tiled_menu_update_id,$type='footer_menu');
                        }    
                        $this->view->successMessage ="Footer menu has been saved successfully."; 
                   } 
                }
                catch (Zend_File_Transfer_Exception $e) {
                      $e->getMessage();
                }
          }   
        }
        
        $footerMenuData = $cms->getQuickLinkDataList($type='footer_menu');
        $total_rows = count($footerMenuData); 
        if($total_rows>0){ 
            $hierarchyMenuTypeList = $cms->getHierarchyMenuTypeList($type='footer_menu');
            $footerMenuList = $cms->getTiledMenuAndSubMenu($hierarchyMenuTypeList);
            $this->view->menuList = $footerMenuList;
            $this->view->footerMenuListCount = $total_rows;
        } else { 
            $this->view->tiledMenuListCount = $total_rows;
            $hierarchyMenuTypeList = $cms->getHierarchyMenuTypeList($type='footer_menu');
            $footerMenuList = $cms->getTiledMenuAndSubMenu($hierarchyMenuTypeList);
            //echo "<pre>";print_r($tiledMenuList);die;
            $this->view->menuList = $footerMenuList;
        }
     
    }
        
    
    /**
    * addpage() method is used to admin can add cms static page
    * @param password string
    * @return ture 
    */
    public function addpageAction()
    {
        //Check admin logedin or not
        $this->pagechecklogin();
        $cms = new Admin_Model_Cms();
        $form = new Admin_Form_Staticpage();
        $this->view->form = $form;
                
        if ($this->getRequest()->isPost()) {   
            $getData = $this->getRequest()->getPost();
            //echo "<pre>";print_r($getData);die;
         if($form->isValid($getData)){
             
                //-------Start Code for save content---------//
                if(isset($getData['submit'])=="Save") { 
                
                    //Code for check page name already exists or not
                    $checkPageName = $cms->checkContentExistsOrNot($column_name="page_name",strtoupper($getData['pageName']));
                    //echo "<pre>";print_r($checkPageName);die;
                    if(count($checkPageName)<=0) {  
                    
                                $orignalFIleName = $_FILES['background_image']['name']; 
                                $ext = @substr($_FILES['background_image']['name'], strrpos($_FILES['background_image']['name'], '.'));
                                $image = "page_".time().$ext;
                                $upload = new Zend_File_Transfer_Adapter_Http();
                                $upload->setDestination("upload/static_pages/");
                                $upload->addFilter('Rename', "upload/static_pages/".$image);
                                $file = $upload->getFileName();
                                if($orignalFIleName!="")
                                {
                                   $addimage = $image;
                                } else {
                                   $addimage = "";
                                }
                                try {
                                $upload->receive();

                                if(@$getData['content_menu_link_id']){
                                    $content_menu_link_id = @implode(",",@$getData['content_menu_link_id']);
                                }else{
                                    $content_menu_link_id ="";
                                }
                                $seo_name = str_replace(" ","_",$getData['staticTitle']);
                                $page_key = $this->sanitize_data($getData['pageName']);
                                //$page_key = strtolower($getData['pageKey']);

                                $addPageData = array(
                                             //'parent_id'=>$parent_id,
                                             'page_name'=>strtoupper($getData['pageName']),
                                             'page_key'=>$page_key,
                                             'page_title'=>strtoupper($getData['staticTitle']),
                                             'meta_title'=>$getData['metaTitle'],
                                             'meta_keywords'=>$getData['metaKeywords'],
                                             'meta_description'=>$getData['metaDescription'],
                                             'background_image'=>$addimage,
                                             'page_description_temporary'=>$getData['staticDescription'],
                                             'content_template_type'=>$getData['content_template_type'],
                                             'content_menu_link_id'=>$content_menu_link_id,
                                             'seo_name'=>$seo_name,
                                             'status'=>$getData['status'],
                                             'createdBy'=>$this->username,
                                             'createdOn'=>time()
                                           );
                                //echo "<pre>";print_r($addPageData);die;
                                $cms->add_static_page($addPageData);
                                $this->view->successMessage ="Static page has been created, Page will be display after super admin approve"; 
                                //$this->_redirect("/admin/cms/managepages");
                                }
                                catch (Zend_File_Transfer_Exception $e) {
                                      $e->getMessage();
                                }
                            
                    } else {
                        $this->view->errorMessage ="Page name already exists, please choose another name"; 
                    }
             } else { 
                 
                    //-------Start Code for save and approve content---------//
                    
                    //Code for check page name already exists or not
                    $checkPageName = $cms->checkContentExistsOrNot($column_name="page_name",strtoupper($getData['pageName']));
                    //echo "<pre>";print_r($checkPageName);die;
                    if(count($checkPageName)<=0) {  
                    
                            $orignalFIleName = $_FILES['background_image']['name']; 
                            $ext = @substr($_FILES['background_image']['name'], strrpos($_FILES['background_image']['name'], '.'));
                            $image = "page_".time().$ext;
                            $upload = new Zend_File_Transfer_Adapter_Http();
                            $upload->setDestination("upload/static_pages/");
                            $upload->addFilter('Rename', "upload/static_pages/".$image);
                            $file = $upload->getFileName();
                            if($orignalFIleName!="")
                            {
                               $addimage = $image;
                            } else {
                               $addimage = "";
                            }
                            try {
                            $upload->receive();
                            
                            if(@$getData['content_menu_link_id']){
                                $content_menu_link_id = @implode(",",@$getData['content_menu_link_id']);
                            }else{
                                $content_menu_link_id ="";
                            }
                            
                            $seo_name = str_replace(" ","_",$getData['staticTitle']);
                            $page_key = $this->sanitize_data($getData['pageName']);

                            $addPageData = array(
                                         //'parent_id'=>$parent_id,
                                         'page_name'=>strtoupper($getData['pageName']),
                                         'page_key'=>$page_key,
                                         'page_title'=>strtoupper($getData['staticTitle']),
                                         'meta_title'=>$getData['metaTitle'],
                                         'meta_keywords'=>$getData['metaKeywords'],
                                         'meta_description'=>$getData['metaDescription'],
                                         'background_image'=>$addimage,
                                         'page_description'=>$getData['staticDescription'],
                                         'content_template_type'=>$getData['content_template_type'],
                                         'content_menu_link_id'=>$content_menu_link_id,
                                         'seo_name'=>$seo_name,
                                         'status'=>$getData['status'],
                                         'is_publish'=>'1',
                                         'createdBy'=>$this->username,
                                         'createdOn'=>time()
                                       );
                            //echo "<pre>";print_r($addPageData);die;
                            $cms->add_static_page($addPageData);
                            //$this->view->successMessage ="Static page has been added"; 
                            $this->_redirect("/admin/cms/managepages");
                            }
                            catch (Zend_File_Transfer_Exception $e) {
                                  $e->getMessage();
                            }
                      
                    } else {
                        $this->view->errorMessage ="Page name already exists, please choose another name"; 
                    }
             }  
          }   
        }
    }
    
    /**
    * managepages() method is used to get all static pages list
    * @param string
    * @return ture 
    */
    public function managepagesAction()
    {
        //Check admin logedin or not
        $this->pagechecklogin();
        $cms = new Admin_Model_Cms();
        $result = $cms->getAllStaticPagesList();
        //echo "<pre>";print_r($result);die;
                
//        $page=$this->_getParam('page',1);
//        $paginator = Zend_Paginator::factory($result);      
//        $paginator->setCurrentPageNumber($this->getRequest()->getParam('page')); // page number
//        $perPage = $paginator->setItemCountPerPage($this->perPageLimit); // number of items to show per page
//        $this->view->paginator = $paginator;
        $this->view->totalrec = $result;
    }
    
    /**
    * editpage() method is used to admin can edit cms static page
    * @param password string
    * @return ture 
    */
    public function editpageAction()
    {
        //Check admin logedin or not
        $this->pagechecklogin();
        $cms = new Admin_Model_Cms();
        $form = new Admin_Form_Editstaticpage();
        $pId = (int)$this->getRequest()->getParam("id");
        $form->setMethod("POST");
        $form->setAction("admin/cms/editpage/id/".$pId);
        $form->setName("edit_static_page");
               
        if ($this->getRequest()->isPost()) {   
            $getData = $this->getRequest()->getPost();
          
         if($form->isValid($getData)){ 
                
                //-------Start Code for save content---------//
                if(@$getData['submit']=="Save") {  
                        
                    //Code for check page alias name already exists or not
                    $page_id = (int) @$getData['id'];
                    $checkPageName = $cms->checkEditPageContentExistsOrNot($page_id,$column_name="page_name",strtoupper($getData['pageName']));
                    //echo "<pre>";print_r($checkPageName);die;
                    if(count($checkPageName)<=0) { 
                        
                            $orignalFIleName = $_FILES['background_image']['name']; 
                            $ext = @substr($_FILES['background_image']['name'], strrpos($_FILES['background_image']['name'], '.'));
                            $image = "page_".time().$ext;
                            $upload = new Zend_File_Transfer_Adapter_Http();
                            $upload->setDestination("upload/static_pages/");
                            $upload->addFilter('Rename', "upload/static_pages/".$image);
                            $file = $upload->getFileName();

                            $result = $cms->getStaticPageDetailsByPageId($getData['id']);
                            if($orignalFIleName!=""){ 
                                $path_image = "upload/static_pages/".$result->background_image;
                                @unlink($path_image);
                                $image_edit = $image;    
                            }else {
                                $image_edit = $result->background_image;
                            }

                            try {
                            $upload->receive();

                            if(@$getData['content_menu_link_id']){
                                $content_menu_link_id = @implode(",",@$getData['content_menu_link_id']);
                            }else{
                                $content_menu_link_id ="";
                            }
                            $seo_name = str_replace(" ","_",$getData['staticTitle']);
                            $page_key = $this->sanitize_data($getData['pageName']);
                            $page_id = $getData['id'];
                            
                            $editPageData = array(
                                         'page_name'=>strtoupper($getData['pageName']),
                                         'page_key'=>$page_key,
                                         'page_title'=>strtoupper($getData['staticTitle']),
                                         'meta_title'=>$getData['metaTitle'],
                                         'meta_keywords'=>$getData['metaKeywords'],
                                         'meta_description'=>$getData['metaDescription'],
                                         'background_image'=>$image_edit,
                                         'page_description_temporary'=>$getData['staticDescription'],
                                         'content_template_type'=>$getData['content_template_type'],
                                         'content_menu_link_id'=>$content_menu_link_id,
                                         'seo_name'=>$seo_name,
                                         'status'=>$getData['status'],
                                         'updatedBy'=>$this->username,
                                         'updatedOn'=>time()
                                       );
                            //echo "<pre>";print_r($editPageData);die;
                            $cms->edit_statis_page($editPageData,$page_id);
                            $this->view->successMessage ="Static page has been saved successfully."; 
                            //$this->_redirect("/admin/cms/managepages");
                            }
                            catch (Zend_File_Transfer_Exception $e) {
                                  $e->getMessage();
                            }
                        
                    }else {
                        $this->view->errorMessage ="Page name already exists, please choose another name"; 
                    }     
             }
             else if(@$getData['pending']=="Pending") {
                    
                    //-------Start Code for save and approve content---------//
                     
                    //Code for check page alias name already exists or not
                    $page_id = (int) @$getData['id'];
                    $checkPageName = $cms->checkEditPageContentExistsOrNot($page_id,$column_name="page_name",strtoupper($getData['pageName']));
                    //echo "<pre>";print_r($checkPageName);die;
                    if(count($checkPageName)<=0) { 
                        
                            $orignalFIleName = $_FILES['background_image']['name']; 
                            $ext = @substr($_FILES['background_image']['name'], strrpos($_FILES['background_image']['name'], '.'));
                            $image = "page_".time().$ext;
                            $upload = new Zend_File_Transfer_Adapter_Http();
                            $upload->setDestination("upload/static_pages/");
                            $upload->addFilter('Rename', "upload/static_pages/".$image);
                            $file = $upload->getFileName();

                            $result = $cms->getStaticPageDetailsByPageId($getData['id']);
                            if($orignalFIleName!=""){ 
                                $path_image = "upload/static_pages/".$result->background_image;
                                @unlink($path_image);
                                $image_edit = $image;    
                            }else {
                                $image_edit = $result->background_image;
                            }

                            try {
                            $upload->receive();

                            if(@$getData['content_menu_link_id']){
                                $content_menu_link_id = @implode(",",@$getData['content_menu_link_id']);
                            }else{
                                $content_menu_link_id ="";
                            }
                            $seo_name = str_replace(" ","_",$getData['staticTitle']);
                            $page_key = $this->sanitize_data($getData['pageName']);
                            $page_id = $getData['id'];
                            $page_description = $result->page_description;
                            $page_description_temporary = $result->page_description_temporary;
                            
                            $editPageData = array(
                                         'page_name'=>strtoupper($getData['pageName']),
                                         'page_key'=>$page_key,
                                         'page_title'=>strtoupper($getData['staticTitle']),
                                         'meta_title'=>$getData['metaTitle'],
                                         'meta_keywords'=>$getData['metaKeywords'],
                                         'meta_description'=>$getData['metaDescription'],
                                         'background_image'=>$image_edit,
                                         'page_description_temporary'=>$page_description_temporary,
                                         'content_template_type'=>$getData['content_template_type'],
                                         'content_menu_link_id'=>$content_menu_link_id,
                                         'seo_name'=>$seo_name,
                                         'status'=>$getData['status'],
                                         'is_publish'=>1,
                                         'updatedBy'=>$this->username,
                                         'updatedOn'=>time()
                                       );
                            //echo "<pre>";print_r($editPageData);die;
                            $cms->edit_statis_page($editPageData,$page_id);
                            $this->view->successMessage ="Page content can't change as approval pending from super admin."; 
                            //$this->_redirect("/admin/cms/managepages");
                            }
                            catch (Zend_File_Transfer_Exception $e) {
                                  $e->getMessage();
                            }
                        
                    }else {
                        $this->view->errorMessage ="Page name already exists, please choose another name"; 
                    }
             }      
          }   
        }
        
        $result = $cms->getStaticPageDetailsByPageId($pId);
        //echo "<pre>";print_r($result);die;
        $editdata["id"] = @$result->id;
        $editdata["pageName"] = @$result->page_name;
        $editdata["pageKey"] = @$result->page_key;
        $editdata["staticTitle"] = @$result->page_title;
        $editdata["metaTitle"] = @$result->meta_title;
        $editdata["metaKeywords"] = @$result->meta_keywords;
        $editdata["metaDescription"] = @$result->meta_description;
        $editdata["background_image"] = @$result->background_image;
        $editdata["staticDescription"] = @$result->page_description_temporary;
        $editdata["content_template_type"] = @$result->content_template_type;
        $editdata["content_menu_link_id"] = @explode(",",@$result->content_menu_link_id);
        $editdata["status"] = @$result->status;
        $form->populate($editdata);
        
        $this->view->background_image = @$result->background_image; 
        $this->view->page_description = @$result->page_description; 
        $this->view->page_description_temporary = @$result->page_description_temporary; 
        $this->view->form = $form;
    }
    
    /**
    * view() method is used to get view page content
    * @param string
    * @return ture 
    */
    public function viewAction()
    {
        //Check admin logedin or not
        $this->_helper->layout()->disableLayout('');
        $this->pagechecklogin();
        $cms = new Admin_Model_Cms();
        $pId = $this->getRequest()->getParam("id");
        $result = $cms->getStaticPageDetailsByPageMdId($pId);
        $this->view->result = $result;
        //echo "<pre>";print_r($result);die;
             
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
    
    
    /**
    * pagechecklogin() method is used to check admin logedin or not
    * @param Null
    * @return Array 
    */
    public function pagechecklogin()
    {
        if($this->admin_type == "content_writer")
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