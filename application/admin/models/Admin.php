<?php

/***************************************************************
 * Catabatic Technology Pvt. Ltd.
 * File Name : Admin.php
 * File Description : Admin Model
 * Created By : Praveen Kumar
 * Created Date: 04-September-2014
 ***************************************************************/

class Admin_Model_Admin extends Zend_Db_Table_Abstract
{
    
    function __construct() {
        $this->db = Zend_Db_Table::getDefaultAdapter(); 
    }
       
    public function __destruct() {
        $this->db->closeConnection(); 
    }
    
    
    /**
    * getAdminUserListByEmail() method is used to get admin details by email
    * @param email string
    * @return object 
    */	
    public function getAdminUserListByEmail($email)
    {   
        $dbtable = new Zend_Db_Table('admin_user');
        $select = $dbtable->select()->where('email = ?',$email);
        $result = $dbtable->fetchRow($select);
        return $result;
    }
    
    /**
    * updateChangePasswordByAdminId() method is used to update password od admin user
    * @param password string and admin_id integer
    * @return true 
    */
//    public function updateChangePasswordByAdminId($password,$admin_id)
//    {   
//        $dbtable = new Zend_Db_Table('tbl_user');
//        $val = array(
//           'password' => md5($password),
//           'password2' =>$password
//        );
//        $dbtable->update($val,"id = $admin_id");
//    }
    

    public function updateChangePasswordByAdminId($password,$admin_id)
    {
        $dbtable = new Zend_Db_Table('admin_user');
        $val = array(
           'password' => md5($password),
//           'password2' =>$password
        );
        $dbtable->update($val,"user_id = $admin_id");
    }
    
    
    public function dashboardItems() {
        $sql = "SELECT itemtype , count(*) as total , 
            (case when (itemtype =1 ) then 'package' when (itemtype = 2 ) then 'hotel' when (itemtype = 3 ) then 'activity' else 'na' end) as itemname ,
            (select count(*) as total_d from `tb_tbb2c_destinations` where `IsPublish`='1' and `IsActive`='1' and `IsMarkForDel`='0' ) as destination 
            FROM `tb_tbb2c_packages_master` where `IsPublish`='1' and `IsActive`='1' and `IsMarkForDel`='0' group by itemtype";
        $result = $this->db->fetchAll($sql);
        return $result;
    }

    
}
