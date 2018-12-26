<?php
/***************************************************************
* Catabatic Technology Pvt. Ltd.
* File Name     : ContactusController.php
* File Desc.    : Contactus controller managed all contact queries
* Created By    : Piyush Tiwari <piyush@catpl.co.in>
* Created Date  : 05 July 2018
* Updated Date  : 05 July 2018
***************************************************************/


class Admin_SyncController extends Zend_Controller_Action
{
    
    public $dbAdapter;
    public $perPageLimit;

    
    public function init()
    {
       /*Initialize db and session access */
       $aConfig = $this->getInvokeArg('bootstrap')->getOptions();
       $this->siteurl = $aConfig['bootstrap']['siteUrl']; 
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
        $this->view->baseUrl = $this->siteurl;
    }
    
    
    /**
    * packagesAction() method is used to admin login for form call
    * @param Null
    * @return json
    */
    public function packagesAction()
    {
        //Check admin logedin or not
        $this->checklogin();
        
        $apiData = [];
        try {
            $curl = curl_init($this->siteurl . "api/sync/index?type=packages" );
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($apiData));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $curl_response = curl_exec($curl);
            curl_close($curl);
            $this->view->response = $curl_response; // send response to the view page
        } catch (Exception $error) {
            $this->view->error_msg = $error->getMessage();
            die;
        }
        
        $response_array  = [];
        
        $response_array = Zend_Json::decode($curl_response);
        
        if($response_array['status']) {
            $this->view->msg = $response_array['msg'];
        }
        
    }

    
    /**
    * destinationsAction() method is used to admin login for form call
    * @param Null
    * @return json
    */
    public function destinationsAction()
    {
        //Check admin logedin or not
        $this->checklogin();
        
        $apiData = [];
        try {
            $curl = curl_init($this->siteurl . "api/sync/index?type=destinations" );
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($apiData));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $curl_response = curl_exec($curl);
            curl_close($curl);
            $this->view->response = $curl_response; // send response to the view page
        } catch (Exception $error) {
            $this->view->error_msg = $error->getMessage();
            die;
        }
        
        $response_array  = [];
        
        $response_array = Zend_Json::decode($curl_response);
        
        if($response_array['status']) {
            $this->view->msg = $response_array['msg'];
        }

    }

    
    /**
    * hotelsAction() method is used to admin login for form call
    * @param Null
    * @return json
    */
    public function hotelsAction()
    {
        //Check admin logedin or not
        $this->checklogin();
        
        $apiData = [];
        try {
            $curl = curl_init($this->siteurl . "api/sync/index?type=hotels" );
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($apiData));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $curl_response = curl_exec($curl);
            curl_close($curl);
            $this->view->response = $curl_response; // send response to the view page
        } catch (Exception $error) {
            $this->view->error_msg = $error->getMessage();
            die;
        }
        
        $response_array  = [];
        
        $response_array = Zend_Json::decode($curl_response);
        
        if($response_array['status']) {
            $this->view->msg = $response_array['msg'];
        }
        
    }

    
    
    /**
    * activitiesAction() method is used to admin login for form call
    * @param Null
    * @return json
    */
    public function activitiesAction()
    {
        //Check admin logedin or not
        $this->checklogin();

        $apiData = [];
        try {
            $curl = curl_init($this->siteurl . "api/sync/index?type=activities" );
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($apiData));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $curl_response = curl_exec($curl);
            curl_close($curl);
            $this->view->response = $curl_response; // send response to the view page
        } catch (Exception $error) {
            $this->view->error_msg = $error->getMessage();
            die;
        }
        
        $response_array  = [];
        
        $response_array = Zend_Json::decode($curl_response);
        
        if($response_array['status']) {
            $this->view->msg = $response_array['msg'];
        }
        
    }

    
    public function filterdestinamtionAction()
    {
        //Check admin logedin or not
        $this->checklogin();
        $status = false;
        $apiData = [];
        $apiData["AgencySysId"] = Catabatic_Helper::getAgencyId();
        $url = Catabatic_Helper::gtxBtoBsite();
        try {
            $curl = curl_init($url."gtxwebservices/createxml/package-destinations");
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($apiData));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $curl_response = curl_exec($curl);
             curl_close($curl);
            $this->view->errorMessage = ""; 
            $this->view->successMessage = "Filter destination successfully updated.";
            $status = true;
// send response to the view page
        } catch (Exception $error) {
            $status = false;
            $this->view->successMessage = "";
            $this->view->errorMessage = $error->getMessage();
           
        }
        
        $JSONFileName = "public/data/dynamic/package_destinations.json";
        file_put_contents($JSONFileName, $curl_response);
        
        $responseArray = Zend_Json::decode($curl_response);
//        echo "<pre>";print_r($responseArray);die('here');
        
        $crud   = new Admin_Model_CRUD();
        $resionArr = array();
        $resionResult  = $crud->rv_select_all("tbl_regions",['label'] ,  ['status'] , ['title'=>'ASC' ] );
        foreach ($resionResult as $resultkey => $resultvalue) {
            $resionArr[] = $resultvalue['label'];
        }
      
        $reasonInsertArray = array();
        
        foreach ($responseArray as $resionkey => $resionvalue) {
    
            if($resionvalue['TypeTitle'] == 'region'){
                if (!in_array($resionvalue['label'], $resionArr)) {
               
            $reasonInsertArray = [
                'title' => $resionvalue['value'],
                'label' => $resionvalue['label'],
                'image' => '',
                'UpdateDate' => date('Y-m-d H:i:s'),
                'CreateDate' => date('Y-m-d H:i:s'),
                'IsActive' => 1,
                'IsMarkForDel' => 0,
            ];
             $resionArr[] = $resionvalue['label'];
             
             $resionIDS[] = $crud->rv_insert('tbl_regions', $reasonInsertArray);
            } 
            }
        }
//        echo "<pre>";print_r($reasonInsertArray);die('hhhh');
        if($status) {
            $this->view->msg = $response_array['msg'];
        }
        
       
        
    }
    
    
    /**
    * footerdestinationsAction() method is used to admin login for form call
    * @param Null
    * @return json
    */
    public function footerdestinationsAction()
    {
        //Check admin logedin or not
        $this->checklogin();
        
        $apiData = [];
        try {
            $curl = curl_init($this->siteurl . "api/sync/index?type=footerdestinations" );
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($apiData));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $curl_response = curl_exec($curl);
            curl_close($curl);
            $this->view->response = $curl_response; // send response to the view page
        } catch (Exception $error) {
            $this->view->error_msg = $error->getMessage();
            die;
        }
        
        $response_array  = [];
        
        $response_array = Zend_Json::decode($curl_response);
        
        if($response_array['status']) {
            $this->view->msg = $response_array['msg'];
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