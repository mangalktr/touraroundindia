<?php
/*************************************************************
* Catabatic Technology Pvt. Ltd.
* File Name     : IndexController.php
* File Desc.    : Index controller for home page front end
* Created By    : Piyush Tiwari <piyush@catpl.co.in>
* Created Date  : 27 July 2018
* Updated Date  : 27 July 2018

* Tours_IndexController | Tours Module / Index controller
**************************************************************
*/


class Blogs_IndexController extends Zend_Controller_Action {

    public $baseUrl = '';
    
    public $AgencyId;
    
    protected $objMdl;
    protected $objHelperGeneral;
    protected $tablename;
    

    public function init() {
        
        $aConfig    = $this->getInvokeArg('bootstrap')->getOptions();
        $BootStrap  = $aConfig['bootstrap'];
        $this->siteName = $BootStrap['siteName'];
        $this->baseUrl  = $BootStrap['siteUrl'];
        $this->AgencyId = $BootStrap['gtxagencysysid'];

        $this->objMdl   = new Admin_Model_CRUD();
        $this->table= "tbl_travelogues";
        $this->objHelperGeneral = $this->_helper->General;
        $this->commentable='tbl_comments';
        $this->per_page_record = 10;
    }
    
    
    public function indexAction()
    {
        $this->view->baseUrl=$this->baseUrl;
        $crud   = new Admin_Model_CRUD();
        $resultset  = $crud->rv_select_blog_all($this->table, ['*'],  ['IsMarkForDel'=>0], ['TravId'=>'DESC']);
      
        if(count($resultset)>0 && !empty($resultset)){
            $cnt= count($resultset); 
            $pageNumber = $this->_getParam('frontpage', 1);
            $paginator = Zend_Paginator::factory($resultset);        
            $paginator->setCurrentPageNumber($this->getRequest()->getParam('frontpage')); // page number
            $perPage = $paginator->setItemCountPerPage($this->per_page_record); // number of items to show per page                      
            //echo "<pre>"; print_r($pageNumber); die;
        }
        //echo "<pre>=="; print_r($paginator); die;
         $this->view->resultset = $paginator;  
         //$this->view->resultset  = $resultset;
        
    }
     
         
    public function blogDetailAction() {
        $this->view->baseUrl=$this->baseUrl;
        $tId = (int) $this->getRequest()->getParam("id");
        $crud   = new Admin_Model_CRUD();
        $resulsetold = $crud->getCount($this->commentable,['blogId' => $tId],'commentId');          
        $resultset = $crud->rv_select_row($this->table, ['*'], ['TravId' => $tId], ['TravId' => 'DESC']);
        $resultsetSeoForBlog = $crud->rv_select_row($this->table, ['TravTitle','keyword','description','metatag'], ['TravId' => $tId], ['TravId' => 'DESC']);
        $this->view->resultset  = $resultset;       
        $this->view->resultsetSeoForBlog = $resultsetSeoForBlog;
        $this->view->totalcooment  = $resulsetold[0]['commentId'];
    }
    public function savecommentAction(){
        $crud   = new Admin_Model_CRUD();
         if( $this->getRequest()->isPost() ) {
            $getData = $this->getRequest()->getPost();    
          //  echo "<pre>"; print_r($getData); die;
            $savePageData = [                    
                    'blogId' => ($getData['blogId']),
                    'name' => $getData['username'],
                    'emailId' => $getData['email'],
                    'phone' => $getData['phone'],
                    'comment' => $getData['comment'],                  
                    'status' => 1,
                    'createDate'=> date('Y-m-d H:i:s'),
                    
                ];
                //echo "<pre>";print_r($savePageData);die;
                $crud->rv_insert($this->commentable, $savePageData);
                echo "<span style='color:green'>Comment has been added successfully.</span><br>"; exit;
         }
         
    }
}