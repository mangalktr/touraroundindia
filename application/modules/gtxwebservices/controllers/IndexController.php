<?php

class Gtxwebservices_IndexController extends Zend_Controller_Action {

    public function init() {
        
    }

    public function indexAction() {
        $model = new Gtxwebservices_Model_Webservices();
        $AgencySysId = Catabatic_Helper::getAgencyId();
        $param = $this->getRequest()->getParams();
        if (!empty($param)) {
            $packageId = isset($param['packageId']) ? $param['packageId'] : '';
        } else {
            $packageId = "";
        }
        try {
            $getPackagesData = $model->getPackagesData($AgencySysId, $packageId);
        } catch (Zend_Exception $e) {
            $getPackagesData = $e->getMessage();
        }
        echo Zend_Json::Encode($getPackagesData);
        exit;
    }

    public function deactivateAction() {
        $model = new Gtxwebservices_Model_Webservices();
        $AgencySysId = Catabatic_Helper::getAgencyId();
        $param = $this->getRequest()->getParams();
        $errorlog = array();
        if (!empty($param)) {
            $packageId = isset($param['packageId']) ? $param['packageId'] : '';

            try {
                $updateData = array(
                    "UpdateDate" => date("Y-m-d H:i:s"),
                    "IsPublish" => 0,
                    "IsActive" => 0,
                    "IsMarkForDel" => 0
                );
                $where = array('GTXPkgId =?' => $packageId, 'AgencySysId=?' => $AgencySysId);
                $model->updateTable("tb_tbb2c_packages_master",$updateData, $where);
                $errorlog["success"][] = $packageId;
                $getPackagesData = Zend_Json::Encode($errorlog);
                $updateXMLUrl = Catabatic_Helper::getSiteUrl().'api/sync';
                $model->updateXml($updateXMLUrl);
            } catch (Zend_Exception $e) {
                $getPackagesData = Zend_Json::Encode($e->getMessage());
            }
        } else {
            $getPackagesData = Zend_Json::Encode($e->getMessage());
        }
        echo $getPackagesData;
        exit;
    }

    public function queryAction() {

        $param = $this->getRequest()->getParams();

//        print_r($param); die;

        $this->postFields = "";
        $this->postFields .= "&AgencySysId=16";
        $this->postFields .= "&TravelPlanId=824";
        $this->postFields .= "&FirstName=Prashant";
        $this->postFields .= "&LastName=Kumar";
        $this->postFields .= "&Email=prashant@catpl.co.in";
        $this->postFields .= "&MobileNumber=9015562063";
        $this->postFields .= "&PriceRange=2000-50000";
        $this->postFields .= "&PKGCheckInDate=12/08/2017"; // 12/08/2017
        $this->postFields .= "&PKGCheckOutDate=19/08/2017";  //$param['PKGCheckOutDate']; // 19/08/2017
        $this->postFields .= "&MinPrice=2000"; //. $param['MinPrice']; // 2000
        $this->postFields .= "&MaxPrice=50000"; //. $param['MaxPrice']; // 50000
        $model = new Gtxwebservices_Model_Webservices();
        $getPackagesData = $model->sendQuery($this->postFields);
        echo "<pre>";
        print_r($getPackagesData);
        exit;
    }   
}