<?php

class Flexsin_ValidateAdmin extends Zend_Controller_Action {

    public function init() {
         if(Admin_Model_Login::getIdentity()->role != 1) {
            $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
            $redirector->gotoUrl('admin/login');
            return;
        }
    }

}