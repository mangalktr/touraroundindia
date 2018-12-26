<?php

/***************************************************************
 * Catabatic Technology Pvt. Ltd.
 * File Name : Faq.php
 * File Description : Faq Model
 * Created By : Praveen Kumar
 * Created Date: 05-September-2014
 ***************************************************************/

class Admin_Model_User extends Zend_Db_Table_Abstract
{
    function __construct() 
    { 	  
        
    }
     
    /**
    * checkUsernameExistsOrNot() method is used to get check username exists or not
    * @param email string
    * @return object 
    */	
    public function checkUsernameExistsOrNot($content)
    {   
        $dbtable = Zend_Db_Table::getDefaultAdapter(); 
        $select = $dbtable->select()
                          ->from("tbl_user",array('count(*) AS total_records'))
                          ->where("username =?",$content);
        $result = $dbtable->fetchOne($select);
        return $result;
    }
    
    /**
    * add() method is used to add state name
    * @param array
    * @return true 
    */
    public function add($addData=array())
    {   
        $dbtable = new Zend_Db_Table('tbl_user');
        $dbtable->insert($addData);
    }
    
    /**
    * checkEditUsernameExistsOrNot() method is used to get check faq queation exists or not
    * @param email string
    * @return object 
    */	
    public function checkEditUsernameExistsOrNot($user_id,$username)
    {   
        $dbtable = Zend_Db_Table::getDefaultAdapter(); 
        $select = $dbtable->select()
                          ->from("tbl_user",array('count(*) AS total_records'))
                          ->where("username =?",$username)
                          ->where('md5(concat("DCBUSER",id))!=?',$user_id);
        $result = $dbtable->fetchOne($select);
        return $result;
    }
    
    /**
    * edit() method is used to add menu
    * @param array
    * @return true 
    */
    public function edit($editData,$where)
    {   
        $dbtable = new Zend_Db_Table('tbl_user');
        $dbtable->update($editData,$where);
    }
    
    /**
    * getAllUsersList() method is used to get all users list
    * @param email string
    * @return object 
    */	
    public function getAllUsersList()
    {   
        $db = Zend_Db_Table::getDefaultAdapter(); 
        $select = $db->select()
                     ->from(array("tbl_user"), array('*'))
                     ->where('type !=?','superadmin')
                     ->order('username');
        
        $result = $db->fetchAll($select);
       
        return $result;
    }
    
    /**
    * getUserDetailsById() method is used to get all menus details by hierarchy menu id
    * @param email string
    * @return object 
    */	
    public function getUserDetailsById($usrId)
    {   
        $dbtable = new Zend_Db_Table('tbl_user');
        $select = $dbtable->select()
                          ->where('md5(concat("DCBUSER",id))= ?',$usrId);
        $result = $dbtable->fetchRow($select);
        return $result;
    }
    
    /**
    * delete() method is used to delete user
    * @param array
    * @return true 
    */
    public function delete($usrId)
    {   
        $dbtable = new Zend_Db_Table('tbl_user');
        $where = array('md5(concat("DCBUSER",id))=?'=>$usrId);
        $dbtable->delete($where);
    }
            
}
