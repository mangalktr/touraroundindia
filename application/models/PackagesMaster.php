<?php

class Travel_Model_PackagesMaster {

    protected $db = NULL;
    public $intId = NULL;
    
    
    

    /*     * ************************************ */

    public function __construct() {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $this->baseUrl = $request->getScheme() . '://' . $request->getHttpHost();
        $this->db = Zend_Db_Table::getDefaultAdapter();
        
    }

    public function __destruct() {
        $this->db->closeConnection();
    }
    
    
    
    public function packageSearch($keyword) {

        $keyword = str_replace('-', ' ', trim($keyword));

        $response = array();
        $whereCondition = " T1.IsMarkForDel = '0' AND T1.IsActive='1' AND T1.IsPublish = 1 AND T1.ItemType = 1 ";

        if ($keyword === 'DOM') {
            $whereCondition .= " AND T1.CountryIds = '101' ";
        } else if ($keyword === 'INT') {
            $whereCondition .= " AND T1.CountryIds != '101' ";
        } else if ($keyword != 'FIX') {
            if($keyword != 'PRIVATE') {
                $whereCondition .= " AND T1.PackageSearchString LIKE '%$keyword%' ";
            }
        }else{
            $whereCondition .= " ";
        }
        //$whereCondition .= " AND ',' + T1.Destinations + ',' like '%,' + '$keyword' + ',%' ";
        $select = $this->db->select()
                ->from(array("T1" => "tb_tbb2c_packages_master"), array('T1.GTXPkgId', 'T1.CountryIds', 'T1.PkgSysId', 'T1.AgencySysId', 'T1.PackageCategory', 'T1.ItemType', 'T1.PackageType', 'T1.GTXPkgSourceId', 'T1.PackageSubType', 'T1.HotDeal', 'T1.DestinationsId', 'T1.ShortJsonInfo', 'T1.LongJsonInfo', 'T1.PackageSearchString AS Destinations', 'T1.Countries', 'T1.MinPrice', 'T1.MaxPrice', 'T1.Nights', 'T1.Image', 'T1.MinPax', 'T1.PkgValidFrom', 'T1.PkgValidUntil', 'T1.BookingValidUntil', 'T1.StarRating'))
                ->where($whereCondition)
                ->order("T1.MinPrice ASC");
      //  echo $select; exit;
        $response = $this->db->fetchAll($select);
      //  echo "<pre>";print_r($response);exit;
        if ($keyword === 'FIX') {
            foreach ($response as $key => $value) {
                $array = Zend_Json::decode($value['LongJsonInfo'], true);
                if ($array['package']['IsFixedDeparturePackage'] === 1) {
                    $responses[] = $value;
                }
            }
            return $responses;
        } else if ($keyword === 'PRIVATE') {
           foreach ($response as $key => $valuei) {
                $array3 = Zend_Json::decode($valuei['LongJsonInfo'], true);
                if ($array3['package']['IsFixedDeparturePackage'] == '0') {
                    $responsest[] = $valuei;
                } 
            }
            return $responsest;
        }else {
            return $response;
        }
    }

    public function packageInclusion() {
        $response = array();
        $whereCondition = " T1.status = 1 ";
        $select = $this->db->select()
                ->from(array("T1" => "tbl_inclusion_icon"),array('T1.Title','T1.Icon'))
                ->where($whereCondition)
                ->order("T1.Title ASC");
        //echo $select;
        $data = $this->db->fetchAll($select);
        if($data){
            foreach ($data as $value) {
                $response[$value['Title']] = $value['Icon'];
            }
        }
        return $response;
    }
    
    public function packageThemes() {
        $response = array();
        $whereCondition = " T1.IsActive = '1' ";
        $select = $this->db->select()
                ->from(array("T1" => "tbl_pack_type"),array('T1.Title','T1.Icon'))
                ->where($whereCondition)
                ->order("T1.Title ASC");
        //echo $select;
        $data = $this->db->fetchAll($select);
        if($data){
            foreach ($data as $value) {
                $response[$value['Title']] = $value['Icon'];
            }
        }
        return $response;
    }
    
    
    
    public function getDestinationAutoSuggest($keyword , $table) {
        $keyword = trim($keyword);
        $response = array();
        
        $sql = "select DISTINCT(A.Destinations) as Destinations FROM $table A WHERE Destinations LIKE '%" .$keyword."%' ";
        $res = $this->db->query($sql)->fetchAll();
        
        if (count($res) > 0) {
            foreach ($res as $row) {
                $expDes = explode(',',$row['Destinations']);
                $response[] = array (
                    'label' => $row['Destinations'],
                    'value' => $row['Destinations'],
                );
            }
        }
        //echo '<pre>'; print_r($response); die;
        return $response;
    }
    
    public function getdestinationhome($table) {
        $response = array();
        
        $sql = "select DISTINCT(A.Destinations) as Destinations, A.Nights,A.MinPrice,A.MaxPrice FROM $table A WHERE 1=1 ";
        $res = $this->db->query($sql)->fetchAll();
        
        if (count($res) > 0) {
            foreach ($res as $row) {
                $expDes = explode(',',$row['Destinations']);
                $response[] = array (
                    'label' => $row['Destinations'],
                    'value' => $row['Destinations'],
                );
            }
        }
        //echo '<pre>'; print_r($response); die;
        return $res;
    }
    
    
    
    
    /* Added By Pardeep Panchal Ends */
    

    
   public function sendNewsLetter($tablename, array $addData) {
      
       $dbtable = new Zend_Db_Table($tablename);
        
        return $dbtable->insert($addData); // return inserted id
    }

    public function checkLetter($tablename, array $columns, array $where) {
        $dbtable = new Zend_Db_Table($tablename);
        $select = $dbtable->select()->from("$tablename as tbl", $columns);
        
        if(count($where)){
            foreach ($where as $k => $v)
            $select->where("$k =?","$v");
        }
        $result = $dbtable->fetchAll($select);
        if($result==NULL)
            return false;
        else
            return $result->toArray();
    }

    

}
