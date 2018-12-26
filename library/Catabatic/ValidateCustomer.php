<?php

class Catabatic_ValidateCustomer extends Zend_Controller_Action {

    public $requestedId;
    public $checkCode;
    public $checkId;
    public $finalCode;
    public $input_data;
    private $secureCode = "SECURE";
    private $key = "GTX";
    public $amount;
    public $userID;
    private $ENC_KEY = "tripsbanklockkey";
    private $VECTOR = "myvector";

    public function init() {
        
    }

    function sanitize_data($input_data) {
        $searchArr = array("document", "write", "alert", "%", "@", "$", ";", "+", "|", "#", "<", ">", ")", "(", "'", "\'", ",");
        $input_data = str_replace("script", "", $input_data);
        $input_data = str_replace("iframe", "", $input_data);
        $input_data = str_replace($searchArr, "", $input_data);
        return htmlentities(stripslashes($input_data), ENT_QUOTES);
    }
    
    public function GUID() {
        if (function_exists('com_create_guid') === true) {
            return trim(com_create_guid(), '{}');
        }
        return sprintf('%04X%04X%04X%04X%04X%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }

    public function secureCode($id, $guid) {
        $requestedId = $this->sanitize_data($id);
        $checkCode = $this->sanitize_data($guid);
        $finalCode = $checkCode . $this->secureCode . $requestedId;
        $checkId = hash('sha256', "GTX-$finalCode");
        return $checkId;
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

    public function getDec($input) {
        $filter = new Zend_Filter_Decrypt(array('adapter' => 'mcrypt', 'key' => $this->ENC_KEY));
        $filter->setVector($this->VECTOR);
        $decoded = pack('H*', $input);
        $decrypted = trim($filter->filter($decoded));
        return $decrypted;
    }

    public function getEnc($input) {
        $filter = new Zend_Filter_Encrypt(array('adapter' => 'mcrypt', 'key' => $this->ENC_KEY));
        $filter->setVector($this->VECTOR);
        $encrypted = $filter->filter($input);
        $encrypted = bin2hex($encrypted); //hints: rawurlencode(..) works
        return $encrypted;
    }

    
    
     public function writeLogEmail($data) {
        $fileName = date("Y-m-d") . "_email.txt";
        $fp = fopen("data/" . $fileName, 'a+');
        $data = date("Y-m-d H:i:s") . " - " . $data;
        fwrite($fp, $data);
        fclose($fp);
    }
    
    public function mailSentByElastice($emailData,$arrEmailStatistics = array()) {
        
        $url = 'https://api.elasticemail.com/v2/email/send';       
        $to = implode(";",$emailData['to']);       
        try {
            $post = array('from' => $emailData['fromEmail'],
                'fromName' => $emailData['fromName'],
               'apikey' => 'def51ec9-0f32-418c-9f33-e8751ded6f98',
               // 'apikey' =>   '0b32ebfc-4cb2-4bde-a2f6-5de357fdfb9c',
                'subject' => $emailData['subject'],
                'to' => $to,
                'bodyHtml' => $emailData['bodyHtml'],
                'bodyText' => $emailData['bodyText'],
                'isTransactional' => false);
            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL => $url,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $post,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER => false,
                CURLOPT_SSL_VERIFYPEER => false
            ));
            $result = curl_exec($ch);
            curl_close($ch);
            
            if(count($arrEmailStatistics) > 0){
                $mailResponse = json_decode($result,1);
                $arrEmailStatistics['Title'] = $emailData['subject'];
                if($mailResponse['success']){
                    $arrEmailStatistics['Status'] = $mailResponse['success'];
                    $arrEmailStatistics['RefSysId'] = isset($mailResponse['data']['transactionid'])?$mailResponse['data']['transactionid']:'';
                }
                $objAgency = new Travel_Model_TblAgency();
                $objAgency->insertData("TB_Agency_Sent_Sms_Email", $arrEmailStatistics);
            }
            
            
            
            
        } catch (Exception $ex) {
            $result = $ex->getMessage();
        }
        return $result;
    }
	/*  By Md sabir */
	public function mailSentByElasticeEnquirySupplier($emailData,$arrEmailStatistics = array()) {
        $url = 'https://api.elasticemail.com/v2/email/send';       
        $to = implode(";",$emailData['to']);  
		//print_r($to); die('eee');		
        try {
            $post = array('from' => $emailData['fromEmail'],
                'fromName' => $emailData['fromName'],
               'apikey' => 'def51ec9-0f32-418c-9f33-e8751ded6f98',
               // 'apikey' =>   '0b32ebfc-4cb2-4bde-a2f6-5de357fdfb9c',
                'subject' => $emailData['subject'],
                'to' => $to,
                'bodyHtml' => $emailData['bodyHtml'],
                'bodyText' => $emailData['bodyText'],
                'isTransactional' => false);
            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL => $url,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $post,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER => false,
                CURLOPT_SSL_VERIFYPEER => false
            ));
            $result = curl_exec($ch);
            curl_close($ch);            
            
            
        } catch (Exception $ex) {
            $result = $ex->getMessage();
        }
        return $result;
    }
    public function mailSentByElasticeWithAttachement($emailData) {
        $url = 'https://api.elasticemail.com/v2/email/send';
        $filename = $emailData['fileName'];
        $file_name_with_full_path = realpath($emailData['filePath'].$filename);
        $filetype = "text/plain"; 
        try {
            $post = array('from' => $emailData['fromEmail'],
                'fromName' => $emailData['fromName'],
                'apikey' => 'def51ec9-0f32-418c-9f33-e8751ded6f98',
                'subject' => $emailData['subject'],
                'to' => $emailData['to'][0],
                'bodyHtml' => $emailData['bodyHtml'],
                'bodyText' => $emailData['bodyText'],
                'isTransactional' => false,
                'file_1' => new CurlFile($file_name_with_full_path, $filetype, $filename));

            $ch = curl_init();

            curl_setopt_array($ch, array(
                CURLOPT_URL => $url,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $post,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER => false,
                CURLOPT_SSL_VERIFYPEER => false
            ));

            $result = curl_exec($ch);
            curl_close($ch);
        } catch (Exception $ex) {
            $result = $ex->getMessage();
        }
        return $result;
    }

}
