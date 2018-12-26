<?php

/***************************************************************
 * Catabatic Technology Pvt. Ltd.
 * File Name : Cms.php
 * File Description : CMS Model
 * Created By : Praveen Kumar
 * Created Date: 05-September-2014
 ***************************************************************/

class Admin_Model_Cms extends Zend_Db_Table_Abstract
{
    function __construct() 
    { 	  
        
    }
       
    /**
    * add() method is used to add menu
    * @param array
    * @return true 
    */
    public function add($addData=array())
    {   
        $dbtable = new Zend_Db_Table('hierarchy_menu');
        $dbtable->insert($addData);
    }
    
    /**
    * edit() method is used to add menu
    * @param array
    * @return true 
    */
    public function edit($addData=array(),$where)
    {   
        $dbtable = new Zend_Db_Table('hierarchy_menu');
        $dbtable->update($addData,$where);
    }
    
    /**
    * checkEditLabelNameOrUrlNameExistsOrNot()method is used to check laben name exists or not 
    * @param email string
    * @return object 
    */	
    public function checkEditLabelNameOrUrlNameExistsOrNot($menu_id,$column_name,$content)
    {   
        $dbtable = new Zend_Db_Table('hierarchy_menu');
        $select = $dbtable->select()
                          ->where("$column_name =?",$content)
                          ->where("id !=?",$menu_id);
        $result = $dbtable->fetchRow($select);
        $total_row = count($result);
        return $total_row;
    }
    
    /**
    * checkLabelNameOrUrlNameExistsOrNot()method is used to check laben name exists or not 
    * @param email string
    * @return object 
    */	
    public function checkLabelNameOrUrlNameExistsOrNot($column_name,$content)
    {   
        $dbtable = new Zend_Db_Table('hierarchy_menu');
        $select = $dbtable->select()
                          ->where("$column_name =?",$content);
        $result = $dbtable->fetchRow($select);
        $total_row = count($result);
        return $total_row;
    }
    
    /**
    * getAllMenus() method is used to get all menus list
    * @param email string
    * @return object 
    */	
    public function getAllMenus()
    {   
        $dbtable = new Zend_Db_Table('hierarchy_menu');
        $select = $dbtable->select()
                          ->where('status = ?',1)
                          ->where('parent_id = ?',0)
                          ->order('id');
        $result = $dbtable->fetchAll($select);
        return $result;
    }
    
    /**
    * getAllMenusList() method is used to get all menus list
    * @param email string
    * @return object 
    */	
    public function getAllMenusList()
    {   
        $dbtable = new Zend_Db_Table('hierarchy_menu');
        $select = $dbtable->select()
                          ->order('id');
        $result = $dbtable->fetchAll($select);
        return $result;
    }
    
    /**
    * getMenuDetailsById() method is used to get all menus details by hierarchy menu id
    * @param email string
    * @return object 
    */	
    public function getMenuDetailsById($id)
    {   
        $dbtable = new Zend_Db_Table('hierarchy_menu');
        $select = $dbtable->select()->where('id = ?',$id);
        $result = $dbtable->fetchRow($select);
        return $result;
    }
    
    /**
    * getSubFourthlbMenuDetailsById() method is used to get menus details by hierarchy sub menu id
    * @param email string
    * @return object 
    */	
    public function getSubFourthlbMenuDetailsById($parent_id)
    {   
        $dbtable = new Zend_Db_Table('hierarchy_menu');
        $select = $dbtable->select()->where('id = ?',$parent_id);
        $result = $dbtable->fetchRow($select);
        return $result;
    }
    
    /**
    * getSubThreelbMenuDetailsById() method is used to get menus details by hierarchy sub menu id
    * @param email string
    * @return object 
    */	
    public function getSubThreelbMenuDetailsById($parent_id)
    {   
        $dbtable = new Zend_Db_Table('hierarchy_menu');
        $select = $dbtable->select()->where('id = ?',$parent_id);
        $result = $dbtable->fetchRow($select);
        return $result;
    }
    
    /**
    * getSubTwolbMenuDetailsById() method is used to get menus details by hierarchy sub menu id
    * @param email string
    * @return object 
    */	
    public function getSubTwolbMenuDetailsById($parent_id)
    {   
        $dbtable = new Zend_Db_Table('hierarchy_menu');
        $select = $dbtable->select()->where('id = ?',$parent_id);
        $result = $dbtable->fetchRow($select);
        return $result;
    }
    
    /**
    * getSubMenuDetailsById() method is used to get menus details by hierarchy sub menu id
    * @param email string
    * @return object 
    */	
    public function getSubMenuDetailsById($parent_id)
    {   
        $dbtable = new Zend_Db_Table('hierarchy_menu');
        $select = $dbtable->select()->where('id = ?',$parent_id);
        $result = $dbtable->fetchRow($select);
        return $result;
    }
    
    /**
    * getAllSubMenuList() method is used to get all menus list
    * @param email string
    * @return object 
    */	
    public function getAllSubMenuList($hierarchy_menu_id)
    {   
        $dbtable = new Zend_Db_Table('hierarchy_menu');
        $select = $dbtable->select()->where('parent_id =?',$hierarchy_menu_id);
        $result = $dbtable->fetchAll($select);
        //echo "<pre>";print_r($result);die;
        return $result;
    }
    
    /**
    * getAllSubThreelbMenuList() method is used to get all sub menues list
    * @param email string
    * @return object 
    */	
    public function getAllSubThreelbMenuList($hierarchy_sub_menu_id)
    {   
        $dbtable = new Zend_Db_Table('hierarchy_menu');
        $select = $dbtable->select()->where('parent_id =?',$hierarchy_sub_menu_id);
        $result = $dbtable->fetchAll($select);
        //echo "<pre>";print_r($result);die;
        return $result;
    }
    
    /**
    * getAllSubFourthlbMenuList() method is used to get all sub fourth level menues list
    * @param email string
    * @return object 
    */	
    public function getAllSubFourthlbMenuList($hierarchy_sub_menu_id)
    {   
        $dbtable = new Zend_Db_Table('hierarchy_menu');
        $select = $dbtable->select()->where('parent_id =?',$hierarchy_sub_menu_id);
        $result = $dbtable->fetchAll($select);
        //echo "<pre>";print_r($result);die;
        return $result;
    }
    
    /**
    * getMenuDetailsByMenuId() method is used to get all menus details by hierarchy menu id
    * @param email string
    * @return object 
    */	
    public function getMenuDetailsByMenuId($hmId)
    {   
        $dbtable = new Zend_Db_Table('hierarchy_menu');
        $select = $dbtable->select()->where('md5(concat("DCB",id)) = ?',$hmId);
        $result = $dbtable->fetchRow($select);
        return $result;
    }
    
    
    /**
    * delete() method is used to delete menu
    * @param array
    * @return true 
    */
    public function delete($hmId)
    {   
        $dbtable = new Zend_Db_Table('hierarchy_menu');
        $where = array('md5(concat("DCB",id))=?'=>$hmId);
        $dbtable->delete($where);
    }
    
    /**
    * getHierarchyMenuTypeList()method is used to get all menus list
    * @param menu_id integer
    * @return object 
    */	
    public function getHierarchyMenuTypeList($type)
    {   
        if($type=='quick_link')
        {
           $offset_val = "LIMIT 0,7";
        }else if($type=='tiled_menu'){
           $offset_val = "LIMIT 0,14";
        }else if($type=='footer_menu'){
           $offset_val = "LIMIT 0,15";
        }else if($type=='content_menu'){
           $offset_val ="";
        }
        $db = Zend_Db_Table::getDefaultAdapter(); 
        $select ="select id from hierarchy_menu where status='1' and FIND_IN_SET('$type',hierarchy_menu_type) order by id $offset_val";
        /*$select = $db->select()
                          ->from('hierarchy_menu',array('id'))
                          ->where('status = ?',1)
                          ->where("FIND_IN_SET('hierarchy_menu_type',(?))",$type)
                          ->limit(7, 0);*/
        
        $result = $db->fetchAll($select);
        //echo "<pre>";print_r($result);die;
        return $result;
    }
    
    /**
    * getQuickLinkMenuAndSubMenu()method is used to get all menus list
    * @param menu_id integer
    * @return object 
    */	
    public function getQuickLinkMenuAndSubMenu($hierarchy_menu_id)
    {   
        //$hierarchy_menu_id = array(1,2,3,4,5,6,7);
        $dbtable = new Zend_Db_Table('hierarchy_menu');
        $select = $dbtable->select()->where('id IN (?)',$hierarchy_menu_id);
        $result = $dbtable->fetchAll($select);
        //echo "<pre>";print_r($result);die;
        return $result;
    }
    
    /**
    * getQuickLinkTwolbMenuDetailsById() method is used to get sub menu list by id
    * @param email string
    * @return object 
    */	
    public function getQuickLinkTwolbMenuDetailsById($id)
    {   
        $dbtable = new Zend_Db_Table('hierarchy_menu');
        $select = $dbtable->select()->where('parent_id = ?',$id);
        $result = $dbtable->fetchAll($select);
        //echo "<pre>";print_r($result);die;
        return $result;
    }
        
    /**
    * getQuickLinkTwolbMenuListById() method is used to get sub menu list by id
    * @param email string
    * @return object 
    */	
    public function getQuickLinkTwolbMenuListById($quick_link_menu_id)
    {   
        $quick_link_menu_id_arr = @explode(",",$quick_link_menu_id);
        $dbtable = new Zend_Db_Table('hierarchy_menu');
        $select = $dbtable->select()->where('id IN (?)',$quick_link_menu_id_arr);
        $result = $dbtable->fetchAll($select);
        return $result;
    }
    
    /**
    * getQuickLinkDataList() method is used to get sub menu list by id
    * @param email string
    * @return object 
    */	
    public function getQuickLinkDataList($type)
    {   
        $dbtable = new Zend_Db_Table('tbl_menu');
        $select = $dbtable->select()->where('hierarchy_menu_type = ?',$type);
        $result = $dbtable->fetchAll($select);
        //echo "<pre>";print_r($result);die;
        return $result;
    }
    
    /**
    * getQuickLinkTwolbMenuDetailsByHierarchyMenuId() method is used to get sub menu list by id
    * @param email string
    * @return object 
    */	
    public function getQuickLinkTwolbMenuDetailsByHierarchyMenuId($type,$hierarchy_menu_id)
    {   
        $dbtable = new Zend_Db_Table('tbl_menu');
        $select = $dbtable->select()
                          ->where('hierarchy_menu_type = ?',$type)
                          ->where('hierarchy_menu_id = ?',$hierarchy_menu_id);
        $result = $dbtable->fetchRow($select);
        //echo "<pre>";print_r($result);die;
        return $result;
    }
    
    /**
    * add_quick_link() method is used to add menu
    * @param array
    * @return true 
    */
    public function add_quick_link($addData=array())
    {   
        $dbtable = new Zend_Db_Table('tbl_menu');
        $dbtable->insert($addData);
    }
    
    /**
    * update_quick_link() method is used to add menu
    * @param array
    * @return true 
    */
    public function update_quick_link($editData1=array(),$hierarchy_tiled_menu_update_id,$type)
    {   
        $dbtable = new Zend_Db_Table('tbl_menu');
        $where = array('hierarchy_menu_id =?'=>$hierarchy_tiled_menu_update_id,'hierarchy_menu_type =?'=>$type);
        $dbtable->update($editData1,$where);
    }
        
    /**
    * getTiledMenuAndSubMenu()method is used to get all tiled menus list
    * @param menu_id integer
    * @return object 
    */	
    public function getTiledMenuAndSubMenu($tiled_hierarchy_menu_id)
    {   
        $dbtable = new Zend_Db_Table('hierarchy_menu');
        $select = $dbtable->select()->where('id IN (?)',$tiled_hierarchy_menu_id);
        $result = $dbtable->fetchAll($select);
        //echo "<pre>";print_r($result);die;
        return $result;
    }
    
    
    /**
    * add_static_page() method is used to add static page content
    * @param array
    * @return true 
    */
    public function add_static_page($addPageData=array())
    {   
        $dbtable = new Zend_Db_Table('tbl_static_pages');
        $dbtable->insert($addPageData);
    }
    
    /**
    * getAllStaticPagesList() method is used to get all menus list
    * @param email string
    * @return object 
    */	
    public function getAllUnPublishStaticPagesList()
    {   
        $dbtable = new Zend_Db_Table('tbl_static_pages');
        $select = $dbtable->select()
                          ->where('is_publish =?',0)
                          ->order('id');
        $result = $dbtable->fetchAll($select);
        return $result;
    }
    
    /**
    * getAllStaticPagesList() method is used to get all menus list
    * @param email string
    * @return object 
    */	
    public function getAllStaticPagesList()
    {   
        $dbtable = new Zend_Db_Table('tbl_static_pages');
        $select = $dbtable->select()
                          ->order('id');
        $result = $dbtable->fetchAll($select);
        return $result;
    }
    
    /**
    * edit_statis_page() method is used to edit static page content
    * @param array
    * @return true 
    */
    public function edit_statis_page($editPageData=array(),$page_id)
    {   
        $dbtable = new Zend_Db_Table('tbl_static_pages');
        $where = array('id =?'=>$page_id);
        $dbtable->update($editPageData,$where);
    }
    
    
    /**
    * getQuickLinkColumn123DataList() method is used to get sub menu list by id
    * @param email string
    * @return object 
    */	
    public function getQuickLinkColumn123DataList($type)
    {   
        $dbtable = Zend_Db_Table::getDefaultAdapter(); 
        $select ="select * from hierarchy_menu where status='1' and FIND_IN_SET('$type',hierarchy_menu_type)order by id";
        $result = $dbtable->fetchAll($select);
        //echo "<pre>";print_r($result);die;
        return $result;
    }
    
    /**
    * getStaticPageDetailsByPageId() method is used to get all menus list
    * @param email string
    * @return object 
    */	
    public function getStaticPageDetailsByPageId($pId)
    {   
        $dbtable = new Zend_Db_Table('tbl_static_pages');
        $select = $dbtable->select()
                          ->where('id =?',$pId);
        $result = $dbtable->fetchRow($select);
        return $result;
    }
    
    /**
    * getStaticPageDetailsByPageMdId() method is used to get all menus list
    * @param email string
    * @return object 
    */	
    public function getStaticPageDetailsByPageMdId($pId)
    {   
        $dbtable = new Zend_Db_Table('tbl_static_pages');
        $select = $dbtable->select()
                          ->where('md5(concat("DCBPAGE",id))=?',$pId);
        $result = $dbtable->fetchRow($select);
        return $result;
    }
    
    /**
    * checkContentExistsOrNot() method is used to get all menus list
    * @param email string
    * @return object 
    */	
    public function checkContentExistsOrNot($column_name,$content)
    {   
        $dbtable = new Zend_Db_Table('tbl_static_pages');
        $select = $dbtable->select()
                          ->where("$column_name =?",$content);
        $result = $dbtable->fetchRow($select);
        return $result;
    }
    
    /**
    * checkEditPageContentExistsOrNot() method is used to get all menus list
    * @param email string
    * @return object 
    */	
    public function checkEditPageContentExistsOrNot($page_id,$column_name,$content)
    {   
        $dbtable = new Zend_Db_Table('tbl_static_pages');
        $select = $dbtable->select()
                          ->where("$column_name =?",$content)
                          ->where("id !=?",$page_id);
        $result = $dbtable->fetchRow($select);
        return $result;
    }
    
    
    /**
    * getAllStaticPendingApprovalPagesList() method is used to get all menus list
    * @param email string
    * @return object 
    */	
    public function getAllStaticPendingApprovalPagesList()
    {   
        $dbtable = new Zend_Db_Table('tbl_static_pages');
        $select = $dbtable->select()
                          ->where('page_description <> page_description_temporary')
                          ->order('id');
        $result = $dbtable->fetchAll($select);
        return $result;
    }
    
   
    
    
    /**
    * getQuickLinkMenuColumn123List() method is used to get sub menu list by id
    * @param email string
    * @return object 
    */	
    public function getQuickLinkMenuColumn123List($quick_link_menu_id)
    {   
        $dbtable = new Zend_Db_Table('tbl_menu');
        $select = $dbtable->select()
                          ->where("quick_link_menu_id =?",$quick_link_menu_id);
        //echo $select; die;
        $result = $dbtable->fetchRow($select);
        return $result;
    }

    /**
    * deleteQuickLinkColumn123Data() method is used to add menu
    * @param array
    * @return true
    */
    public function deleteQuickLinkColumn123Data($type)
    {
        $dbtable = new Zend_Db_Table('tbl_menu');
        $where = array('hierarchy_menu_type =?'=>$type);
        $dbtable->delete($where);
    }
    
}
