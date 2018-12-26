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
