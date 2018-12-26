<?php

class Payment_IndexController extends Zend_Controller_Action {

    private $postFields;
    private $ATOMPAYMENTURL;
    private $ATOMLOGIN;
    private $ATOMPASS;
    private $ATOMPRODID;

    public function init() {
        $this->returnbycustomerURL = Catabatic_Helper::getSiteUrl() . "payment/index/return";
        $this->returnbycustomerURLFromWebsite = Catabatic_Helper::getSiteUrl() . "payment/index/return-webite";
        $this->ATOMPAYMENTURL = Catabatic_Helper::getATOMPAYMENTURL();
        $this->ATOMPASS = Catabatic_Helper::getATOMPASS();
        $this->ATOMLOGIN = Catabatic_Helper::getATOMLOGIN();
        $this->ATOMPRODID = Catabatic_Helper::getATOMPRODID();
        $this->paymentMdl = new Payment_Model_Payment();
    }

    public function returnAction() {
        $this->_helper->layout()->disableLayout();
        $returnPerameter = $this->getRequest()->getPost();
        if (!empty($returnPerameter)) {
            $status = $returnPerameter["f_code"];
            $AgencySysId = $returnPerameter["clientcode"];
            $this->postFields = "";
            $this->postFields .= "&f_code=$status";
            $this->postFields .= "&clientcode=$AgencySysId";
            $this->postFields .= "&udf9=" . $returnPerameter["udf9"];
            $this->postFields .= "&udf4=" . $returnPerameter["udf4"];
            $this->postFields .= "&mmp_txn=" . $returnPerameter["mmp_txn"];
            $this->postFields .= "&mer_txn=" . $returnPerameter["mer_txn"];
            $this->postFields .= "&desc=" . $returnPerameter["desc"];
            $paymenturl = Catabatic_Helper::gtxBtoBsite() . "payment/atom-payment/customer-return";
            $this->postFields .= "&discriminator=" . $returnPerameter["discriminator"];
            $this->postFields .= "&surcharge=" . $returnPerameter["surcharge"];
            $webModel = new Gtxwebservices_Model_Webservices();
            $returnData = $webModel->customerPayment($this->postFields, $paymenturl);
            $decodePerameter = Zend_Json::decode($returnData, true);
            $contactForm = new Payment_Form_Return();
            $contactForm->setMethod('post');
            $contactForm->setName('PAYMENTFORM');
            $contactForm->setAttrib('target', 'iframename');
            $contactForm->setAction($decodePerameter['udf5']);
            $paymentInfo["status"] = $decodePerameter['status'];
            $paymentInfo["GUID"] = $decodePerameter['GUID'];
            $paymentInfo["TpSysId"] = $decodePerameter['TpSysId'];
            $paymentInfo["TrxId"] = $decodePerameter['TrxId'];
            $paymentInfo["udf2"] = $decodePerameter['udf2'];
            $paymentInfo["error"] = $decodePerameter['error'];
            $paymentInfo["error_Message"] = $decodePerameter['error_Message'];
            $contactForm->populate($paymentInfo);
            $this->view->url = $decodePerameter['udf5'];
            $this->view->paymentInfo = $contactForm;
        }
    }

    public function indexAction() {

        if ($this->getRequest()->isPost()) {
            //echo "<pre>";print_r($_REQUEST);exit;
            $stringData = $this->getRequest()->getPost("stringData");
            $amount = $this->getRequest()->getPost("amount");
            $GUID = $this->getRequest()->getPost("GUID");
            $Firstname = $this->getRequest()->getPost("Firstname");
            $email = $this->getRequest()->getPost("email");
            $phone = $this->getRequest()->getPost("phone");
            $TPSysId = $this->getRequest()->getPost("TPSysId");
            $encodeUdfNineValue = $this->getRequest()->getPost("encodeUdfNineValue");
            $AgencySysId = $this->getRequest()->getPost("AgencySysId");
            $securecode = $this->getRequest()->getPost("securecode");
            $CustomerSysId = $this->getRequest()->getPost("CustomerSysId");
            $AgencyUserSysId = $this->getRequest()->getPost("AgencyUserSysId");
            $checkCode = $this->paymentMdl->walletCode($AgencyUserSysId, $GUID, $amount, $AgencySysId, $TPSysId, $CustomerSysId, $stringData);
            if ($securecode == $checkCode) {
                $datenow = date("d/m/Y h:m:s");
                $modifiedDate = str_replace(" ", "%20", $datenow);
                $paymenturl = $this->ATOMPAYMENTURL;
                $ru = $this->returnbycustomerURL;
                $this->postFields = "";
                //$this->postFields .= "&login=$this->ATOMLOGIN";
                $this->postFields .= "&login=52655";
                
                $this->postFields .= "&pass=$this->ATOMPASS";
                $this->postFields .= "&ttype=NBFundTransfer";
                $this->postFields .= "&prodid=$this->ATOMPRODID";
                $this->postFields .= "&amt=$amount";
                $this->postFields .= "&txncurr=INR";
                $this->postFields .= "&txnscamt=0";
                $this->postFields .= "&clientcode=" . urlencode(base64_encode($AgencySysId));
                $this->postFields .= "&txnid=" . $GUID;
                $this->postFields .= "&date=" . $modifiedDate;
                $this->postFields .= "&custacc=123456789";
                $this->postFields .= "&udf1=$Firstname";
                $this->postFields .= "&udf2=$email";
                $this->postFields .= "&udf3=$phone";
                $this->postFields .= "&udf4=$TPSysId";
                $this->postFields .= "&udf9=$encodeUdfNineValue";
                $this->postFields .= "&ru=$ru";
                $sendUrl = $paymenturl . "?" . substr($this->postFields, 1) . "\n";
                $this->paymentMdl->writeLog($sendUrl);
                $returnData = $this->paymentMdl->sendInfo($this->postFields, $paymenturl);
                //echo "<pre>";print_r($returnData);exit;
                $this->paymentMdl->writeLog($returnData . "\n");
                $xmlObjArray = $this->paymentMdl->xmltoarray($returnData);
                if ($xmlObjArray['tempTxnId'] != "") {
                    $url = $xmlObjArray['url'];
                    $this->postFields = "";
                    $this->postFields .= "&ttype=NBFundTransfer";
                    $this->postFields .= "&tempTxnId=" . $xmlObjArray['tempTxnId'];
                    $this->postFields .= "&token=" . $xmlObjArray['token'];
                    $this->postFields .= "&txnStage=1";
                    $url = $paymenturl . "?" . $this->postFields;
                    $this->paymentMdl->writeLog($url . "\n");
                    
                    // sleep(20);
                    //echo $url; exit;
                    header("LOCATION:".$url);exit;
                  // $this->view->url =$url; 
                } else {
                    echo $xmlObjArray['token']; exit;
                }
            } else {
                echo "w Invalid Request"; exit;
            }
        }
        echo "Invalid Request"; exit;
    }
    
     public function paymentFromWebsiteAction() {

        if ($this->getRequest()->isPost()) {
            $stringData = $this->getRequest()->getPost("stringData");
            $amount = $this->getRequest()->getPost("amount");
            $GUID = $this->getRequest()->getPost("GUID");
            $Firstname = $this->getRequest()->getPost("Firstname");
            $email = $this->getRequest()->getPost("email");
            $phone = $this->getRequest()->getPost("phone");
            $TPSysId = $this->getRequest()->getPost("TPSysId");
            $udf5ReturnURL = $this->getRequest()->getPost("returnURL");
            $AgencySysId = $this->getRequest()->getPost("AgencySysId");
            $securecode = $this->getRequest()->getPost("securecode");
            $CustomerSysId = $this->getRequest()->getPost("CustomerSysId");
            $AgencyUserSysId = $this->getRequest()->getPost("AgencyUserSysId");
            $checkCode = $this->paymentMdl->walletCode($AgencyUserSysId, $GUID, $amount, $AgencySysId, $TPSysId, $CustomerSysId, $stringData);
            if ($securecode == $checkCode) {
                
                $udf9 = array(
                            "1" => $TPSysId,
                            "2" => $AgencyUserSysId,
                            "3" => $udf5ReturnURL,
                            "4" => $amount
                        );
                        $encodeUdfNineValue = base64_encode(Zend_Json::encode($udf9));

                
                
                
                $datenow = date("d/m/Y h:m:s");
                $modifiedDate = str_replace(" ", "%20", $datenow);
                $paymenturl = $this->ATOMPAYMENTURL;
                $ru = $this->returnbycustomerURLFromWebsite;
                $this->postFields = "";
                $this->postFields .= "&login=$this->ATOMLOGIN";
                $this->postFields .= "&pass=$this->ATOMPASS";
                $this->postFields .= "&ttype=NBFundTransfer";
                $this->postFields .= "&prodid=$this->ATOMPRODID";
                $this->postFields .= "&amt=$amount";
                $this->postFields .= "&txncurr=INR";
                $this->postFields .= "&txnscamt=0";
                $this->postFields .= "&clientcode=" . urlencode(base64_encode($AgencySysId));
                $this->postFields .= "&txnid=" . $GUID;
                $this->postFields .= "&date=" . $modifiedDate;
                $this->postFields .= "&custacc=123456789";
                $this->postFields .= "&udf1=$Firstname";
                $this->postFields .= "&udf2=$email";
                $this->postFields .= "&udf3=$phone";
                $this->postFields .= "&udf4=$TPSysId";
                $this->postFields .= "&udf9=$encodeUdfNineValue";
                $this->postFields .= "&ru=$ru";
                $sendUrl = $paymenturl . "?" . substr($this->postFields, 1) . "\n";
                $this->paymentMdl->writeLog($sendUrl);
                $returnData = $this->paymentMdl->sendInfo($this->postFields, $paymenturl);
                $this->paymentMdl->writeLog($returnData . "\n");
                $xmlObjArray = $this->paymentMdl->xmltoarray($returnData);
                if ($xmlObjArray['tempTxnId'] != "") {
                    $url = $xmlObjArray['url'];
                    $this->postFields = "";
                    $this->postFields .= "&ttype=NBFundTransfer";
                    $this->postFields .= "&tempTxnId=" . $xmlObjArray['tempTxnId'];
                    $this->postFields .= "&token=" . $xmlObjArray['token'];
                    $this->postFields .= "&txnStage=1";
                    $url = $paymenturl . "?" . $this->postFields;
                    $this->paymentMdl->writeLog($url . "\n");
                    header("Location: " . $url);
                    exit;
                } else {
                    echo $xmlObjArray['token']; exit;
                }
            } else {
                echo "w Invalid Request"; exit;
            }
        }
        echo "Invalid Request"; exit;
    }
     public function returnWebiteAction() { 
        $this->_helper->layout()->disableLayout();
        $returnPerameter = $this->getRequest()->getPost();
        //echo "<pre>";print_r($this->getRequest()->getPost());exit;
        if (!empty($returnPerameter)) {  
            $status = $returnPerameter["f_code"];
            $AgencySysId = $returnPerameter["clientcode"];
            $this->postFields = "";
            $this->postFields .= "&f_code=$status";
            $this->postFields .= "&clientcode=$AgencySysId";
            $this->postFields .= "&udf9=" . $returnPerameter["udf9"];
            $this->postFields .= "&udf4=" . $returnPerameter["udf4"];
            $this->postFields .= "&mmp_txn=" . $returnPerameter["mmp_txn"];
            $this->postFields .= "&mer_txn=" . $returnPerameter["mer_txn"];
            $this->postFields .= "&desc=" . $returnPerameter["desc"];
           // $paymenturl = Catabatic_Helper::gtxBtoBsite() . "payment/atom-payment/customer-return";
          //  $this->postFields .= "&discriminator=" . $returnPerameter["discriminator"];
          //  $this->postFields .= "&surcharge=" . $returnPerameter["surcharge"];
          //  $webModel = new Gtxwebservices_Model_Webservices();
         //   $returnData = $webModel->customerPayment($this->postFields, $paymenturl);
            //echo "HI";exit;
            $decodePerameter = Zend_Json::decode(base64_decode($returnPerameter["udf9"]), true);
          //  echo "<pre>";print_r($decodePerameter);
          //  echo "<br>";
         //  echo "<pre>";print_r($returnPerameter);exit;
            $contactForm = new Payment_Form_Return();
            $contactForm->setMethod('post');
            $contactForm->setName('PAYMENTFORM');
           // $contactForm->setAttrib('target', 'iframename');
            $contactForm->setAction($decodePerameter[3]);
            $paymentInfo["status"] = $status;
            $paymentInfo["GUID"] = $returnPerameter["mmp_txn"];
            $paymentInfo["TpSysId"] = $decodePerameter[1];
            $paymentInfo["TrxId"] = $returnPerameter["mer_txn"];
            $paymentInfo["udf2"] = $returnPerameter["udf2"];
            $paymentInfo["error"] = $returnPerameter['error'];
            $paymentInfo["error_Message"] = $returnPerameter['error_Message'];
            $contactForm->populate($paymentInfo);
            $this->view->url = $decodePerameter[3];
            $this->view->paymentInfo = $contactForm;
        }
    }

}
