<?php

/* Zend Framework
 * @category   Zend
 * @package    Zend_Controller_Action
 * @copyright  Copyright (c) 2008-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    1.0
 * @author     Ranvir singh <ranvir@catpl.co.in>
 * Create Date 06-09-2016
 * Update Date 06-09-2016
 * ************************************************************* */

class Gtxwebservices_Model_Webservices extends Zend_Db_Table_Abstract {

    private $postFields;
    private  $url; 
    function __construct() {
        $this->db = Zend_Db_Table::getDefaultAdapter();
    }
    public function __destruct() {
        $this->db->closeConnection();
    }
    public function getPackagesData($agencyId,$packageId) {
        $this->postFields = "";
        $this->postFields .= "&AgencySysId=$agencyId&packageId=$packageId";
        $this->url = Catabatic_Helper::gtxBtoBsite()."gtxwebservices"; 
        $data = $this->sendInfo($this->postFields, $this->url); 
        $getZsontoArray = Zend_Json::decode($data, true);
        $errorlog = array();
        for ($i = 0; $i < count($getZsontoArray); $i++) {          
        $insertData = array(
                "GTXPkgId" => $getZsontoArray[$i]["GTXPkgId"],
                "GTXPkgSourceId" => $getZsontoArray[$i]["GTXPkgSourceId"],
                "AgencySysId" => $getZsontoArray[$i]["AgencySysId"],
                "IsFeatured" => $getZsontoArray[$i]["IsFeatured"],
                "DisplayIndex" => $getZsontoArray[$i]["DisplayIndex"],
                "ShortJsonInfo" => $getZsontoArray[$i]["ShortJsonInfo"],
                "LongJsonInfo" => $getZsontoArray[$i]["LongJsonInfo"],
                "Destinations" => $getZsontoArray[$i]["Destinations"],
                "DestinationsId" => $getZsontoArray[$i]["DestinationsId"],
                "MinPrice" => $getZsontoArray[$i]["MinPrice"],
                "MaxPrice" => $getZsontoArray[$i]["MaxPrice"],
                "Nights" => $getZsontoArray[$i]["Nights"],
                "MinPax" => $getZsontoArray[$i]["MinPax"],
                "PackTypeMask" => $getZsontoArray[$i]["PackTypeMask"],
                "PackRangeType" => $getZsontoArray[$i]["PackRangeType"],
                "PkgValidFrom" => $getZsontoArray[$i]["PkgValidFrom"],
                "PkgValidUntil" => $getZsontoArray[$i]["PkgValidUntil"],
                "BookingValidUntil" => $getZsontoArray[$i]["BookingValidUntil"],
                "Countries" =>$getZsontoArray[$i]["Countries"],
                "CountryIds" =>$getZsontoArray[$i]["CountryIds"],
                "StarRating" => $getZsontoArray[$i]["StarRating"],
                "UpdateDate" => $getZsontoArray[$i]["UpdateDate"],
                "CreateDate" => $getZsontoArray[$i]["CreateDate"],
                "CreatedBy" => $getZsontoArray[$i]["CreatedBy"],
                "UpdatedBy" => $getZsontoArray[$i]["UpdatedBy"],
                "IsPublish" => 1,
                "IsActive" => $getZsontoArray[$i]["IsActive"],
                "IsMarkForDel" => $getZsontoArray[$i]["IsMarkForDel"]
            );
            try {
                $updateData = array(
                    "UpdateDate" => date("Y-m-d H:i:s"),
                    "IsPublish" => 0,
                    "IsActive" => 0,
                    "IsMarkForDel" => 1
                );
                $where = array('GTXPkgId =?' => $getZsontoArray[$i]["GTXPkgId"]);
                try{
                   $this->updateTable("tb_tbb2c_packages_master",$updateData,$where); 
                } catch (Zend_Exception $e) {
                     $errorlog["error"][] = "update".$e->getMessage();
                }

                $this->insertTable("tb_tbb2c_packages_master", $insertData);
                $errorlog["success"][] = $getZsontoArray[$i]["GTXPkgId"];
            } catch (Zend_Exception $e) {
                $errorlog["error"][] = $e->getMessage();
            }
        }
        return $errorlog;
    }

    public function insertTable($table, $data) {
        $dbtable = new Zend_Db_Table("$table");
        return $dbtable->insert($data);
    }

    public function updateTable($table, $editData, $where) {
        $dbtable = new Zend_Db_Table("$table");
        $dbtable->update($editData, $where);
    }

    public function sendInfo($data, $url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }
    public function sendQuery($data){
        $this->url = Catabatic_Helper::gtxBtoBsite()."gtxwebservices"; 
        return  $this->sendInfo($data, $this->url.'/query');
    }
    public function sendLead($data){
        $this->url = Catabatic_Helper::gtxBtoBsite()."gtxwebservices"; 
        return  $this->sendInfo($data, $this->url.'/lead');
    }

}
