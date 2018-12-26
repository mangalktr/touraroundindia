<?php
class Flexsin_CheckCorset extends Zend_Controller_Action {

    public function init() {
        $sess = new Zend_Session_Namespace('pageId');
        $sess->pageName['pagename'] = 'measurement';
         if(empty(Application_Model_Login::getIdentity()->email)) {
            $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
            $redirector->gotoUrl('login/');
            return;
        }
    }

}
?>