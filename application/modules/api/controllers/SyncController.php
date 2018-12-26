<?php

/* * ***********************************************************
 * Catabatic Technology Pvt. Ltd.
 * File Name     : SyncController.php
 * File Desc.    : Index controller for home page front end
 * Created By    : Piyush Tiwari <piyush@catpl.co.in>
 * Created Date  : 05 July 2018
 * Updated Date  : 05 July 2018

 * Api_SyncController | APi Module / Index controller
 * *************************************************************
 */

class Api_SyncController extends Zend_Rest_Controller {
    # variable declarations

    public $uploadPakcagePath;
    public $uploadDestinationPath;
    public $dummyImagePackage;
    public $dummyImageDestination;
    public $tablePackages;
    public $tableDestinatios;
    public $objHelperHotel;
    public $objHelperGeneral;

    /* Initialize action controller here */

    public function init() {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout(); // disable layouts

        $this->uploadPakcagePath = 'public/upload/tours/';
        $this->uploadDestinationPath = 'public/upload/destinations/';

        $this->dummyImagePackage = 'goa.jpg';
        $this->dummyImageDestination = '';

        $this->tablePackages = 'tb_tbb2c_packages_master';
        $this->tableDestinatios = 'tb_tbb2c_destinations';
    }

# init

    public function indexAction() {
        // action body

        $params = $this->getRequest()->getParams();
        $type = $params['type'];
        $tpid = (isset($params['tpid']) && !empty($params['tpid'])) ? $params['tpid'] : ''; // get tpid

        $crud = new Admin_Model_CRUD;



        ini_set('max_execution_time', 0);

        if ($type === 'destinations') {

            $where = [
                'IsMarkForDel' => 0,
                'IsActive' => 1,
                'IsPublish' => 1,
                'ItemType' => 1 // for Tour Package 1
            ];

            $destinationsResult = $crud->rv_select_all($this->tablePackages, [ 'Destinations as destination'], $where, []);


            $destinationsNew = [];
            foreach ($destinationsResult as $key => $value) {

                if (!in_array($value['destination'], $destinationsNew)) {
                    $destinationsNew[] = $value['destination'];
                }
            }

            $destinationsNew1 = implode(',', $destinationsNew);
            $destinationsNew = explode(',', $destinationsNew1);


            $destinationsArray = (array_unique($destinationsNew));
            asort($destinationsArray); // sort array by value name

            $destinationsArray = array_values(array_filter($destinationsArray)); // remove empty elements

            $destinationsArrayWithDetails = [];
            $whereCustom = "";

            foreach ($destinationsArray as $key => $value) {
                // get count of packages
                $whereCustom = ($value) ? " ( Destinations LIKE '%{$value}%' )" : "";

                $tourResult = $crud->rv_select_row_where_custom($this->tablePackages, ['count(*) as totaltours', 'Countries', 'CountryIds'], ['IsMarkForDel' => 0, 'IsActive' => 1, 'IsPublish' => 1, 'ItemType' => 1], $whereCustom, [], '');
                $actResult = $crud->rv_select_row_where_custom($this->tablePackages, ['count(*) as totalact'], ['IsMarkForDel' => 0, 'IsActive' => 1, 'IsPublish' => 1, 'ItemType' => 3], $whereCustom, [], '');
                $hotelResult = $crud->rv_select_row_where_custom($this->tablePackages, ['count(*) as totalhotel'], ['IsMarkForDel' => 0, 'IsActive' => 1, 'IsPublish' => 1, 'ItemType' => 2], $whereCustom, [], '');

                $destinationsArrayWithDetails[] = [
                    'Title' => $value,
                    'Countries' => $tourResult['Countries'],
                    'CountryIds' => $tourResult['CountryIds'],
                    'Tours' => $tourResult['totaltours'],
                    'Hotels' => $hotelResult['totalhotel'],
                    'Activities' => $actResult['totalact']
                ];
            }


            $errormsg = $destinationIDS = [];
            foreach ($destinationsArrayWithDetails as $key => $value) {
                try {
                    $resultExists = $crud->rv_select_row($this->tableDestinatios, ['Title', 'DesSysId'], ['IsMarkForDel' => 0, 'IsActive' => 1, 'IsPublish' => 1, 'Title' => $value['Title']], []);

                    // if no rows found than insert into db
                    if (!$resultExists['DesSysId']) {
                        $saveData = [
                            'region_id' => 1,
                            'Title' => ($value['Title']),
                            'Activities' => ($value['Activities']),
                            'Tours' => ($value['Tours']),
                            'Hotels' => ($value['Hotels']),
                            'Image' => $this->dummyImageDestination,
                            'Countries' => ($value['Countries']),
                            'CountryIds' => ($value['CountryIds']),
                            'IsPublish' => 1,
                            'IsActive' => 1,
                            'IsMarkForDel' => 0,
                            'UpdateDate' => date('Y-m-d H:i:s'),
                            'CreateDate' => date('Y-m-d H:i:s'),
                        ];
                        $destinationIDS[] = $crud->rv_insert($this->tableDestinatios, $saveData);
                    } else {
                        $crud->rv_update($this->tableDestinatios, ['Tours' => $value['Tours'], 'Activities' => $value['Activities'], 'Hotels' => $value['Hotels']], ['DesSysId =?' => $resultExists['DesSysId']]);
                    }
                } catch (Exception $error) {
                    $errormsg[] = $temperror = ($error->getMessage());
                    throw new Exception("I can not insert into Destination table due to error : " . $temperror);
                }
            }
            //        $this->_helper->General->writeLogs();


            $status = TRUE;
            $msg = "XML for destinations updated successfully.";
            $data = "";
        }

        // this is the else part of all conditions
        else {
            $status = FALSE;
            $msg = "Invalid action.";
            $data = ":(";
        }
        // conditions end here
        echo Zend_Json::encode(['status' => $status, 'msg' => $msg, 'data' => $data]);
        die;
    }

    public function getAction() {
        // action body
    }

# end : getAction

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