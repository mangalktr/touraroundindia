<?php

class Catabatic_CheckSession extends Zend_Controller_Action {
    public function init() {
        $sess = new Zend_Session_Namespace('MypopSess');
        //echo $sess->session_time;exit;
        //echo "<pre>";print_r($_SESSION);
        
        if(isset($sess->setPopup) && ($sess->setPopup == true)){
            $sess->setPopup=true;
        } else {
            $sess->setPopup=false;
        }
        $session_logout = time() + 1000000;
        if ($session_logout >= $sess->session_time) {
            $sess->setPopup=true;
        }
    }

}

?>