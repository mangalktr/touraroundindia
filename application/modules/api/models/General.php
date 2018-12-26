<?php
/* Zend Framework
    * @category   Zend
    * @package    Zend_Controller_Action
    * @copyright  Copyright (c) 2008-2014 Zend Technologies USA Inc. (http://www.zend.com)
    * @license    http://framework.zend.com/license/new-bsd     New BSD License
    * @version    1.0
    * @author     Piyush Tiwari <piyush@catpl.co.in>
    * Create Date 01 July 2018
    * Update Date 01 July 2018
 **************************************************************
 */

class Api_Model_General extends Zend_Db_Table_Abstract
{
    
    function __construct() {
        $this->db = Zend_Db_Table::getDefaultAdapter(); 
    }
       
    public function __destruct() {
        $this->db->closeConnection(); 
    }
    
    
    /**
    * getDestinations() method is used to fetch all the destinations
    * @params : not required
    * @return : array of resultset
    */
    
    public function getDestinations( $tbl )
    {
//        $select = $this->db->select();
//        $select->from(array("t1" => "$tbl") , ['Destinations'])->distinct();
//        $select->where('IsMarkForDel =?' , '0')
//                ->where('IsActive =?' , '1' )
//                ->where('IsPublish =?' , '1')
//                ->where('ItemType=?' , '1');
//        $result = $this->db->fetchAll($select);
        
//        $sql = " SELECT `t1`.`Destinations` , count(`t1`.`Destinations`) as Tours , `LongJsonInfo` FROM `TB_TBB2C_Packages_Master` AS `t1` 
//            WHERE (IsMarkForDel ='0') AND (IsActive ='1') AND (IsPublish ='1') AND (ItemType='1')
//            Group by `t1`.`Destinations` order by Tours Desc LIMIT 20 ";
//        $result = $this->db->query($sql)->fetchAll();
//        return $result;

        $select = $this->db->select();
        $select->from(array("t1" => "$tbl") , ['Title' , 'Hotels' , 'Tours', 'Activities' , 'Image']);
        $select->where('IsMarkForDel =?' , '0')
                ->where('IsActive =?' , '1' )
                ->where('IsPublish =?' , '1')
                ->where('IsFeatured =?' , '1');
        
        $select->limit(30);
//        echo $select;
//        $select->order(new Zend_Db_Expr(' RAND() ')); // get random rows
        $result = $this->db->fetchAll($select);
        return $result;
    } # getDestinations
    
    
    public function trendingTours( $tbl )
    {
        $select = $this->db->select();
        $select->from(array("t1" => "$tbl") , ['PkgSysId' ,'Image' ,'GTXPkgId' ,'Destinations' , 'LongJsonInfo' , 'Nights', 'StarRating', 'PackageType']);
        $select->where('IsMarkForDel =?' , '0')
                ->where('IsActive =?' , '1' )
                ->where('IsPublish =?' , '1')
                ->where('IsFeatured =?' , '1')
                ->where('ItemType=?' , '1');
        $select->limit(3);
//        $select->order(new Zend_Db_Expr(' RAND() ')); // get random rows
        $result = $this->db->fetchAll($select);
        return $result;
    } # trendingTours
    

    
    # to get records from 2 tables
    
    public function getRecordFromTwoTablesById($t1, $colsArr1, $t2, $colsArr2, $joinCols1, $whereArr, $orderby='', $order="ASC")
    {
        $select = $this->db->select();
        $select->from(array("t1" => "$t1") , $colsArr1);
        $select->joinLeft(array('t2' => "$t2"), "t1.". $joinCols1[0]. "= t2.". $joinCols1[1] , $colsArr2);

        foreach($whereArr as $col=>$val)
        {
            $select->where("t1.$col =?", $val);
        }
        
        if(!empty($orderby))
        $select->order("$orderby $order");
//echo $select;die;
        $result = $this->db->fetchRow($select);
        return $result;

    } # getRecordListingFromTwoTablesWhere



    # selectSomeColumnsByUniqId

    public function selectSomeColumnsByUniqId($table, $column, $whereArr)
    {
        $select = $this->db->select()->from("$table", $column);
        
        foreach($whereArr as $col=>$val)
        {
            $select->where("$col = ?", $val);
        }
        
        return $result = $this->db->fetchRow($select);
    } # selectSomeColumnsByUniqId
    
    
    public function getRecordForPDF( $sid, $trxGroupId )
    {
        $sql    = ' SELECT "t1"."CustPolicySysId", "t1"."CustomerSysId", "t1"."IssueDate", "t1"."EndDate", "t1"."SumInsured", "t1"."Priminum", "t1"."GeoLocation",
            "t1"."PolicyNo", "t1"."MemberId", "t1"."Insurer", "t1"."PremiumWithTax", "t1"."CreateDate" AS "insuCreateDate", "t1"."UpdateDate" AS "insuUpdateDate",
            "t2"."Salutation", "t2"."FirstName", "t2"."LastName", "t2"."DOB", "t2"."IDType", "t2"."IDNumber", "t2"."Nominee", "t2"."CreateDate", "t2"."FullAddress",
            "t3"."Title" AS "CITY" , "t4"."PolicyId" AS "MasterPolicy"
            FROM "TB_Agency_Customer_TravelPlan_Insurance" AS "t1"
            LEFT JOIN "TB_Agency_Customer_TravelPlan_InsuMember" AS "t2" ON t1.CustPolicySysId= t2.CustPolicySysId
            LEFT JOIN "TB_Master_Geo_City" AS "t3" ON t2.CitySysId = t3.CityId
            LEFT JOIN "TB_Insurance_Policy_Master" AS "t4" ON t1.Insurer = t4.InsuCompSysId
            WHERE (t1.IsApproved =1) AND (t1.IsActive =1) AND (t1.IsMarkForDelete =0) AND (t1.CustPolicySysId ='.$sid.') AND (t1.TrxGroupId ='.$trxGroupId.') ';
    
        $select = $this->db->query($sql)->fetchAll();
        return $select;
    }
    
    
} # end of class Api_Model_General