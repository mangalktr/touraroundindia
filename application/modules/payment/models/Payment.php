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

class Payment_Model_Payment extends Zend_Db_Table_Abstract {
    private $key = "GTX";
    private $secureCode = "SECURE";
    public $CommssionArray = array();
    public $TDSArray = array();

    function __construct() {
        $this->db = Zend_Db_Table::getDefaultAdapter();
    }

    public function __destruct() {
        $this->db->closeConnection();
    }
function sanitize_data($input_data) {
        $searchArr = array("document", "write", "alert", "%", "@", "$", ";", "+", "|", "#", "<", ">", ")", "(", "'", "\'", ",");
        $input_data = str_replace("script", "", $input_data);
        $input_data = str_replace("iframe", "", $input_data);
        $input_data = str_replace($searchArr, "", $input_data);
        return htmlentities(stripslashes($input_data), ENT_QUOTES);
    }

    public function walletCode($id, $guid, $amount, $userID, $planID, $BaseAmount, $StAMOUNT) {
        $requestedId = $this->sanitize_data($id);
        $checkCode = $this->sanitize_data($guid);
        $amount = $this->sanitize_data($amount);
        $planID = $this->sanitize_data($planID);
        $userID = $this->sanitize_data($userID);
        $BaseAmount = $this->sanitize_data(trim($BaseAmount));
        $StAMOUNT = $this->sanitize_data(trim($StAMOUNT));
        $finalCode = $checkCode . $this->secureCode . $requestedId . $this->secureCode . 'AMOUNT' . $amount . $this->secureCode . 'USERID' . $userID . $this->secureCode . 'PLANID' . $planID . $BaseAmount . $StAMOUNT;
        $checkId = hash('sha256', "$this->key-$finalCode");
        return $checkId;
    }

    public function sendInfo($data, $url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_PORT, 443);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $returnData = curl_exec($ch);
        curl_close($ch);
        return $returnData;
    }
   
    public function writeLog($data) {
        $fileName = date("Y-m-d") . ".txt";
        $fp = fopen("data/" . $fileName, 'a+');
        $data = date("Y-m-d H:i:s") . " - " . $data;
        fwrite($fp, $data);
        fclose($fp);
    }

    function xmltoarray($data) {
        $parser = xml_parser_create('');
        xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8");
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($parser, trim($data), $xml_values);
        xml_parser_free($parser);
        $returnArray = array();
        $returnArray['url'] = $xml_values[3]['value'];
        $returnArray['tempTxnId'] = $xml_values[5]['value'];
        $returnArray['token'] = $xml_values[6]['value'];
        return $returnArray;
    }
    
    public function randomString() {
         $length = 6;
         $chars = "0123456789ABCDEFGHI";
         $str = "";
         for ($i = 0; $i < $length; $i++) {
             $str .= $chars[mt_rand(0, strlen($chars) - 1)];
         }
         return $str;
    }
   

}
