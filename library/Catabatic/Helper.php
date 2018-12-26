<?php

class Catabatic_Helper {

    static public function findUrl($cityId, $dealId, $headline) {
        $browse = array("_", "~", "'", "@", "#", "$", "%", "^", "&", "*", "/", ";", ",", "|", "(", ")", "/", "\\");
        $page_alias_name = strtolower(str_replace(" ", "-", str_replace($browse, "", substr($headline, 0, 50))));
        $page = $page_alias_name . "-" . "-" . $cityId . ".html";
        return $page;
    }

    static public function getSiteUrl() {

        $options = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption('bootstrap');
        return $options["siteUrl"];
    }

    static public function getNoImage( $type ='') {

        $finalpath  = '';
        $img        = ( isset($type) && !empty($type) ) ? $type : '';
        
        $options    = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption('bootstrap');
        
        $finalpath .= $options["siteUrl"];
        $finalpath .= "public/images/icon-$img.jpg";
                
        return $finalpath;
    }
    
    
     static public function getAgencyId() {

        $options = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption('bootstrap');
        return $options["gtxagencysysid"];
    }
    
      static public function gtxBtoBsite() {

        $options = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption('bootstrap');
        return $options["gtxBtoBsite"];
    }
    
    
    static public function getPackageType($packageId) {
        switch ($packageId) {
            case '1': {
                    $val = "Budget";
                    break;
                }
            case '2': {
                    $val = "Standard";
                    break;
                }
            case '3' : {
                    $val = "Deluxe";
                    break;
                }
            case '4' : {
                    $val = "Luxury";
                    break;
                }
            case '5' : {
                    $val = "Premium";
                    break;
                }
            default : {
                    $val = "";
                    break;
                }
        }
        return $val;
    }
    
    
    static public function getMealPlanType( $name ) {
        $CONST_MEAL_PLAN_ARR = unserialize(CONST_MEAL_PLAN_ARR);
        return (array_search($name, $CONST_MEAL_PLAN_ARR));
    }
    
    /*****************************************  ATOM PAYMENT ********************/
    
     static public function getATOMPAYMENTURL() {
        $options = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption('bootstrap');
        return $options["ATOMPAYMENTURL"];
    }
     static public function getATOMLOGIN() {
        $options = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption('bootstrap');
        return $options["ATOMLOGIN"];
    }
     static public function getATOMPASS() {
        $options = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption('bootstrap');
        return $options["ATOMPASS"];
    }
     static public function getATOMPRODID() {
        $options = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption('bootstrap');
        return $options["ATOMPRODID"];
    }
    static public function getReqHashKey() {
        $options = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption('bootstrap');
        return $options["REQHASHKEY"];
    }
    static public function getRespHashKey() {
        $options = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption('bootstrap');
        return $options["RESPHASHKEY"];
    }
        /*****************************************END ATOM PAYMENT ********************/
    static public function getSeoName($headline) {
        //echo $headline;
        $browse = array("_", "~", "'", "@", "#", "$", "%", "^", "&", "*", "/", ";", ",", "|", "(", ")", "/", "\\");
        $page_alias_name = strtolower(str_replace(" ", "-", str_replace($browse, "-", substr($headline, 0, 50))));
        return str_replace(['---','--'], '-', $page_alias_name);
    }
            
}
