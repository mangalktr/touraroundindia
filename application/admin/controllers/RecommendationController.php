<?php
/***************************************************************
* Catabatic Technology Pvt. Ltd.
* File Name     : ContactusController.php
* File Desc.    : Contactus controller managed all contact queries
* Created By    : Piyush Tiwari <piyush@catpl.co.in>
* Created Date  : 23 May 2018
* Updated Date  : 23 May 2018
***************************************************************/


class Admin_RecommendationController extends Zend_Controller_Action
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
       $this->tablefootermnage = 'tbl_footer_management';
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
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
        $crud   = new Admin_Model_CRUD();
        $getData = array();
        if($this->getRequest()->isPost())
        {
        $getData = $this->getRequest()->getPost();
        $searchArr = array(
                        'Title'=>$getData['Title'],
                        'rows'=>$getData['rows'],
                        'page'=>$getData['page'],
            );
        $resulsetold = $crud->getCount( $this->tablefootermnage,['status' =>1 , 'columnType' =>'recommendation'],'id');  
        $crud->searchArr = $searchArr;
        $resultset = $crud->rv_select_static( $this->tablefootermnage,['*'],['columnType'=>'recommendation' ],['id'=> 'DESC']);  
                            if (count($resultset) > 0) {
                foreach ($resultset as $resultkey => $resultval) {
                    $result1[] = [
                        'id' => $resultval['id'],
                        'title' => $resultval['title'],
                        'url' => $resultval['url'],
                        'openType' => $resultval['openType']==1?'New Tab':'Same Tab',
                        'status' => $resultval['status']==1?'Active':'Deactive',
                    ];
                }
            }
        $result = Zend_Json::encode($result1);
        $newResult = Zend_Json::decode($result,false);     
        $finalResult["total"]=$resulsetold[0]['id'];
        $finalResult["rows"]=$newResult;
        echo json_encode($finalResult);
        exit;        
        }
    }
    
    
    public function editrecommendationAction(){
        //Check admin logedin or not
        $this->checklogin();
        $crud = new Admin_Model_CRUD();
        $form = new Admin_Form_Editrecommendation();
        $pId = (int)$this->getRequest()->getParam("id");
        $form->setMethod("POST");
        $form->setAction("admin/recommendation/editrecommendation/id/".$pId);
        $form->setName("edit_recommendation_page");
               
        if( $this->getRequest()->isPost() ) {
            $getData = $this->getRequest()->getPost();
//             echo "<pre>";print_r($getData);die;
            if($form->isValid($getData)) {
//                echo "<pre>";print_r($getData);die;
                //-------Start Code for Approve and Publish content---------//
               if(isset($getData['save'])=="Save") {
                   $editPageData = [
                            'columnType'=> 'recommendation',
                            'title'=>($getData['title']),
                            'url'=>($getData['url']),
                            'openType'=>"{$getData['open_link']}",
                            'status'=>"{$getData['status_number']}"
                        ];
//                            echo "<pre>";print_r($editPageData);die;
                        $crud->rv_update($this->tablefootermnage , $editPageData, ['id =?'=>$pId]);
                        $this->view->successMessage ="Content has been saved successfully.";
                        $this->_helper->flashMessenger->addMessage("Content has been updated successfully.");
                        
                        $this->_redirect("/admin/recommendation/index");   
             }  
             
          }
        }

        $result = $crud->getCmsdata($this->tablefootermnage, ['*'], ['id'=>$pId], ['id'=>'DESC']);
//        echo "<pre>";print_r($result);die;
        $editdata["id"] = @$result->id;
        $editdata["title"] = @$result->title;
        $editdata["url"] = @$result->url;
        $editdata["open_link"] = @$result->openType;
        $editdata["status_number"] = @$result->status;
//        echo "<pre>";print_r($editdata);die;
        $form->populate($editdata);
        
         
        $this->view->form = $form;
	//die('ok');
    }
    public function addrecommendationAction(){
         
        $this->checklogin();
        $crud = new Admin_Model_CRUD();
        $form = new Admin_Form_Addrecommendation();
        $tId = (int)$this->getRequest()->getParam("id");
        $form->setAction("admin/recommendation/addrecommendation");
        $form->setMethod("POST");
        $form->setName("add_recommendation");
      
            if( $this->getRequest()->isPost() ) {
                $getData = $this->getRequest()->getPost();
                if($form->isValid($getData)) {
                if(isset($getData['save'])=="Save") {
                        $savePageData = [
                            'columnType'=> 'recommendation',
                            'title'=>($getData['title']),
                            'url'=>($getData['link']),
                            'openType'=>$getData['open_link'],
                            'status'=>$getData['status_number']
                        ];
// echo "<pre>";print_r($savePageData);die;
                        $crud->rv_insert($this->tablefootermnage, $savePageData);
                        $this->view->successMessage ="Content has been saved successfully.";
                        $this->_helper->flashMessenger->addMessage("Content has been added successfully.");
                        
                        $this->_redirect("/admin/recommendation/index");
                        
                }
                
                }
                
            }
       
        $this->view->form = $form;
        $this->view->messages   = $this->_helper->flashMessenger->getMessages();
    }


    public function deleterecommendationAction(){
        $this->checklogin();
        $crud = new Admin_Model_CRUD();
        $tId = (int)$this->getRequest()->getParam("id");
        if($tId){
            $checkdata = $crud->rv_select_row($this->tablefootermnage, ['id'], ['id'=>$tId], ['id'=>'asc']);
            if(count($checkdata)>0){
                $crud->rv_delete($this->tablefootermnage, ['id =?'=>$tId]);
                $this->_helper->flashMessenger->addMessage("Delete successfully.");
                $this->_redirect("/admin/recommendation/index");
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
////            if( !file_exists('public/data/static/footer.json') ) {
//                $resultset1_json = Zend_Json::encode($resultset1);
//                file_put_contents( 'public/data/static/social.json', $resultset1_json); // create file here
////            }
//        }
//        else if($type == 'footer_links') {
//            $resultset1  = $crud->rv_select_all("tbl_footer_links",['name','link'] ,  ['status'=>1] , ['name'=>'ASC' ] );
//            $resultset1_json = Zend_Json::encode($resultset1);
//                file_put_contents( 'public/data/static/footer.json', $resultset1_json); // create file here
//            
//        }

        $resultset  = $crud->rv_select_all("tbl_social_links",['name','link'] ,  ['status'=>1] , ['name'=>'ASC' ] );
        $footer_destination = $crud->rv_select_all("tb_tbb2c_destinations",[ 'Title'] ,  ['IsActive'=>1 , 'IsPublish' => 1, 'IsMarkForDel' => 0 , 'DisplayOnFooter' => 1 ] , ['Tours'=>'DESC' ] , 10 );
        $this->_helper->General->update_json_footer_file( $resultset , $footer_destination );
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