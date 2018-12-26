<?php
/*************************************************************
* Catabatic Technology Pvt. Ltd.
* File Name     : IndexController.php
* File Desc.    : Index controller for home page front end
* Created By    : Piyush Tiwari <piyush@catpl.co.in>
* Created Date  : 14 Jul 2018
* Updated Date  : 27 Jul 2018

* Api_IndexController | APi Module / Index controller
**************************************************************
*/


class Api_IndexController extends Zend_Rest_Controller
{
    
# variable declarations

    
/* Initialize action controller here */

    public function init()
    {
//        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout(); // disable layouts

    } # init


    public function indexAction() {
      // action body
    }

    public function getAction() {
      // action body
    } # end : getAction


    public function postAction() {
      // action body
    }

    public function putAction() {
      // action body
    }

    public function deleteAction() {
      // action body
    }
    

}