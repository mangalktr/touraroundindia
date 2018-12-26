<?php
/***************************************************************
* Catabatic Technology Pvt. Ltd.
* File Name     : ContactusController.php
* File Desc.    : Contactus controller managed all contact queries
* Created By    : Piyush Tiwari <piyush@catpl.co.in>
* Created Date  : 23 May 2018
* Updated Date  : 23 May 2018
***************************************************************/


class Admin_FooterController extends Zend_Controller_Action
{
    
    public $dbAdapter;
    public $perPageLimit;

    
    public function init()
    {
       /*Initialize db and session access */
       $aConfig = $this->getInvokeArg('bootstrap')->getOptions();
       $this->siteurl = $aConfig['bootstrap']['siteUrl']; 
       $this->per_page_record = 20; 
       $this->dbAdapter = Zend_Db_Table::getDefaultAdapter(); 
       
       $auth        = Zend_Auth::getInstance();
       $authStorage = $auth->getStorage()->read();
       $this->username      = $authStorage->username;
       $this->admin_type    = $authStorage->role;
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
       
        $resultset  = $crud->rv_select_destination_all("tbl_footer_links", ['*'], ['status'=>1]);
        //echo "<pre>";print_r($resultset);die;
                
        # Start : Pagination 
        $page       = $this->_getParam('page', 1);
        $resultset  = Zend_Paginator::factory($resultset);
        $resultset->setItemCountPerPage($this->per_page_record);
        $resultset->setCurrentPageNumber($page);
        # End : Pagination
        
        $this->view->resultset  = $resultset;
        $this->view->messages   = $this->_helper->flashMessenger->getMessages();
       
        
    }
    
    
    public function editfooterAction(){
        //Check admin logedin or not
        $this->checklogin();
        $crud = new Admin_Model_CRUD();
        $form = new Admin_Form_Editfooter();
        $pId = (int)$this->getRequest()->getParam("id");
        $form->setMethod("POST");
        $form->setAction("admin/footer/editfooter/id/".$pId);
        $form->setName("edit_footer_page");
               
        if( $this->getRequest()->isPost() ) {
            $getData = $this->getRequest()->getPost();
            //echo "<pre>";print_r($getData);die;
            if($form->isValid($getData)) {
                
                //-------Start Code for Approve and Publish content---------//
               if(isset($getData['save'])=="Save") {
                   $editPageData = [
                            'name'=>($getData['name']),
                            'link'=>($getData['link']),
                            'status'=>($getData['status_number']),
                            'footer_column'=>($getData['column_number'])
                            
                        ];
                        $crud->rv_update('tbl_footer_links', $editPageData, ['id =?'=>$pId]);
                        $this->view->successMessage ="Page content has been saved successfully.";
                        $this->_helper->flashMessenger->addMessage("Page content has been updated successfully.");
                        $this->update_json_footer('footer_links');
                        $this->_redirect("/admin/footer/index");   
             }  
             
          }
        }

        $result = $crud->getCmsdata('tbl_footer_links', ['*'], ['id'=>$pId], ['id'=>'DESC']);
        //echo "<pre>";print_r($result);die;
        
        $editdata["id"] = @$result->id;
        $editdata["name"] = @$result->name;
        $editdata["column_number"] = @$result->footer_column;
        if($editdata["column_number"]==6){
           $editdata["prelink"] = @$result->link;
           
        }
        else{
          $editdata["link"] = @$result->link;
        }
        $editdata["status_number"] = @$result->status;
        $form->populate($editdata);
        
         
        $this->view->form = $form;
	//die('ok');
    }
    public function addfooterAction(){
        $this->checklogin();
        $crud = new Admin_Model_CRUD();
         $form = new Admin_Form_Addfooter();
        $tId = (int)$this->getRequest()->getParam("id");
        $form->setAction("admin/footer/addfooter");
        $form->setMethod("POST");
        $form->setName("add_footer");
        
        
               
//        echo "<pre>";print_r($previous);
//        die;
            if( $this->getRequest()->isPost() ) {
                $getData = $this->getRequest()->getPost();
                //echo "<pre>";print_r($getData);die;
                if($form->isValid($getData)) {
                if(isset($getData['save'])=="Save") {
                        $savePageData = [
                            'name'=>($getData['name']),
                            'link'=>($getData['link']),
                            'status'=>($getData['status_number']),
                            'footer_column'=>$getData['column_number']
                        ];

                        $crud->rv_insert('tbl_footer_links', $savePageData);
                        $this->view->successMessage ="Page content has been saved successfully.";
                        $this->_helper->flashMessenger->addMessage("Page content has been added successfully.");
                        $this->update_json_footer('footer_links');
                        $this->_redirect("/admin/footer/index");
                        
                }
                }
                
            }
       
        $this->view->form = $form;
        $this->view->messages   = $this->_helper->flashMessenger->getMessages();
    }


    public function deletefooterAction(){
        $this->checklogin();
        $crud = new Admin_Model_CRUD();
        $tId = (int)$this->getRequest()->getParam("id");
        if($tId){
            $checkdata = $crud->rv_select_row('tbl_footer_links', ['id'], ['id'=>$tId], ['id'=>'asc']);
            if(count($checkdata)>0){
                $crud->rv_delete('tbl_footer_links', ['id =?'=>$tId]);
                $this->_helper->flashMessenger->addMessage("Delete successfully.");
                $this->_redirect("/admin/footer/index");
            }else{
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
    
    
    public function update_json_footer( $type )
    {
        $crud   = new Admin_Model_CRUD();
//        if($type == 'social_links') {
//            $resultset1  = $crud->rv_select_all("tbl_social_links",['name','link'] ,  ['status'=>1] , ['name'=>'ASC' ] );
//                $resultset1_json = Zend_Json::encode($resultset1);
//                file_put_contents( 'public/data/static/social.json', $resultset1_json); // create file here
//        }
//        else if($type == 'footer_links') {
//            $resultset1  = $crud->rv_select_all("tbl_footer_links",['name','link'] ,  ['status'=>1] , ['name'=>'ASC' ] );
//            $resultset1_json = Zend_Json::encode($resultset1);
//                file_put_contents( 'public/data/static/footer.json', $resultset1_json); // create file here
//        }
         
        $resultset  = $crud->rv_select_all("tbl_social_links",['name','link'] ,  ['status'=>1] , ['name'=>'ASC' ] );
        $resultset1 = $crud->rv_select_all("tbl_footer_links",['name','link','footer_column'] ,  ['status'=>1] , ['name'=>'ASC' ] );
        $footer_destination = $crud->rv_select_all("tb_tbb2c_destinations",[ 'Title'] ,  ['IsActive'=>1 , 'IsPublish' => 1, 'IsMarkForDel' => 0] , ['Tours'=>'DESC' ] , 5 );
        $this->_helper->General->update_json_footer_file( $resultset , $resultset1 , $footer_destination );
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