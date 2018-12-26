<?php

class Detail_Model_PackageMapper {

    protected $_dbTable;
    protected $objHelperGeneral;

    public function __construct() {

        $this->objHelperGeneral = Zend_Controller_Action_HelperBroker::getStaticHelper('General');
    }

    public function setDbTable($dbTable) {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
    }

    public function getDbTable() {
        if (null === $this->_dbTable) {
            $this->setDbTable('Detail_Model_DbTable_Package');
        }

        return $this->_dbTable;
    }

    public function fetchDetails($catId, $gtxId, $packageId, $market = 'B2C') {
        $resultSet = $this->getDbTable()->select()->where("GTXPkgId=?", $gtxId)->where("PkgSysId=?", $packageId)->query()->fetchAll();
//        echo "<pre>";print_r($resultSet);die;
        $entry = array();
        $entries = array();

        $Countries = '';
        foreach ($resultSet as $row) {
            $entry["LongJsonInfo"] = Zend_Json::decode($row["LongJsonInfo"], true);
            $Countries = $row["Countries"];
            $entries = $entry;
        }

        $PackageType = isset($resultSet[0]['PackageType']) ? $resultSet[0]['PackageType'] : '';
        $PackageSubType = isset($resultSet[0]['PackageSubType']) ? $resultSet[0]['PackageSubType'] : '';

//        echo "<pre>"; print_r($resultSet); exit;
        $entries['LongJsonInfo'] = isset($entries['LongJsonInfo']) ? $entries['LongJsonInfo'] : array();
        $dayView = array();
        foreach ($entries['LongJsonInfo'] as $itinerariesDayVise) {
            foreach ($itinerariesDayVise['Itineraries']['Itinerary'] as $dayViewVal) {
                $dayView[$dayViewVal['Day']]['day'][] = $dayViewVal['Day'];
                $dayView[$dayViewVal['Day']]['day'][] = $dayViewVal['Title'];
                $dayView[$dayViewVal['Day']]['day'][] = $dayViewVal['Program'];
                $dayView[$dayViewVal['Day']]['City'][] = $dayViewVal['ItineraryItem'][0];
                $dayView[$dayViewVal['Day']]['Hotel'][] = $dayViewVal['ItineraryItem'][1];
                $dayView[$dayViewVal['Day']]['Activities'][] = $dayViewVal['ItineraryItem'][2];
                $dayView[$dayViewVal['Day']]['SightSeeings'][] = $dayViewVal['ItineraryItem'][3];
                $dayView[$dayViewVal['Day']]['Transfers'][] = (isset($dayViewVal['ItineraryItem'][4])) ? $dayViewVal['ItineraryItem'][4] : '';
            }
        }
//                echo "<pre>";print_r($dayView);exit;

        $dayViewNew = [];
        foreach ($entries['LongJsonInfo'] as $itinerariesDayVise) {
            foreach ($itinerariesDayVise['Itineraries']['Itinerary'] as $dayViewKey => $dayViewVal) {
                $dayViewNew[$dayViewKey]['day'] = ['Day' => $dayViewVal['Day'], 'Title' => $dayViewVal['Title'], 'Program' => $dayViewVal['Program']];
                $dayViewNew[$dayViewKey]['City'][] = $dayViewVal['ItineraryItem'][0];
                $dayViewNew[$dayViewKey]['Hotels'][] = $dayViewVal['ItineraryItem'][1];
                $dayViewNew[$dayViewKey]['Activities'][] = $dayViewVal['ItineraryItem'][2];
                $dayViewNew[$dayViewKey]['SightSeeings'][] = $dayViewVal['ItineraryItem'][3];
                $dayViewNew[$dayViewKey]['ItineraryId'] = $dayViewVal['ItineraryId'];
            }
        }


//        start : code for included hotels list only 
//    echo "<pre>";print_r($dayViewNew);exit;
        $hotels_array_included_only = [];
        foreach ($dayViewNew as $key => $value) {
//        echo "<pre>"; print_r($value); die;

            if (is_array($value['Hotels'])) {
                $hotels_array_included_only1 = [];
                foreach ($value['Hotels'] as $val) {
//        echo "<pre>"; print_r($val); die;
                    if ($val['Type'] == 'HOTEL') {
                        if (is_array($val['Items'])) {
                            $hotels_array_included_only2 = [];
                            foreach ($val['Items'] as $k => $v) {
//        echo "<pre>"; print_r($v); die;
//                                var_dump($v['Item']);
//                                echo count($v['Item']);
//                                echo '<br>';
                                $hotels_array_inner = [];
                                if (isset($v['Item']) && ( is_array($v['Item']) && ( count($v['Item']) ) )) {
                                    foreach ($v['Item'] as $val_hotel) {
//                                        echo "<pre>"; print_r($val_hotel);
                                        if ($val_hotel['IsIncluded']) {
//                                            $hotels_array_inner[] = $val_hotel['Id'];
                                            $hotels_array_inner[] = ['id' => $val_hotel['Id'], 'type' => $v['Type'], 'inc' => $val_hotel['IsIncluded'], 'mp' => ( isset($val_hotel['MealPlanId']) ? $val_hotel['MealPlanId'] : '')];
                                        }
                                    }
//                                    echo "<pre>"; print_r($hotels_array_inner); die;
//                                    $hotels_array_included_only[$v['Type']][] = $hotels_array_inner;
                                } else {
                                    $hotels_array_inner = [];
                                }

                                $hotels_array_included_only2[$v['Type']] = $hotels_array_inner;
                            }

//                            echo "<pre>"; print_r($hotels_array_included_only2); die;

                            $hotels_array_included_only1 = $hotels_array_included_only2;
                        }
                    }
                } // end foreach
                $hotels_array_included_only[] = $hotels_array_included_only1;
//                                            echo "<pre>"; print_r($hotels_array_included_only); die;
            }
        }
//        echo "<pre>"; print_r($hotels_array_included_only); die;
//        end : code for included hotels list only 






        $itemArray = array();
        $TPId = '';
        $itementries = array();
        foreach ($entries['LongJsonInfo'] as $rowItem) {
            $itemArray["Terms"] = $rowItem["Terms"];
            $itemArray["Name"] = $rowItem["Name"];
            $itemArray["PackageSpec"] = (int)$rowItem['PackageSpec']["SpecificationId"];
            $itemArray["AllowMinPax"] = $rowItem["AllowMinPax"];
            $itemArray["GroupSize"] = $rowItem["GroupSize"];
            $itemArray["Countries"] = $Countries;
            $itemArray["Inclusions"] = $rowItem["Inclusions"];
            $itemArray["Itineraries"] = $rowItem["Itineraries"];
            $itemArray["Cities"] = $rowItem['Cities']['City'];
//            $itemArray["Categories"] = $rowItem['TourTypes']['TourType'][0]["Categories"]['Category'];
            $itemArray["Categories"] = $rowItem['TourTypes']['MarketType'];
            $itemArray["TourType"] = $rowItem['TourTypes']['MarketType'];
            $itemArray["TransfersMaster"] = (isset($rowItem['Transfers'])) ? $rowItem['Transfers'] : null;
            $itemArray["OtherServices"] = (isset($rowItem['OtherServices'])) ? $rowItem['OtherServices'] : null;
             $itemArray["IsBusRoutePackage"] = (isset($rowItem['IsBusRoutePackage'])) ? $rowItem['IsBusRoutePackage'] : '';
             $itemArray["BuspickupLocation"] = (isset($rowItem['BuspickupLocation'])) ? $rowItem['BuspickupLocation'] : '';
            $itementries = $itemArray;
            $TPId = (isset($rowItem['TPId'])) ? $rowItem['TPId'] : null;
            $isFixedDeparture = (isset($rowItem['IsFixedDeparturePackage'])) ? $rowItem['IsFixedDeparturePackage'] : 0;
           
        }
//                echo "<pre>";print_r($itemArray);exit;
//        echo "<pre>";print_r( $itementries );exit;

        $tourTypeArr = array();

        /* get default category 
         * hotel standard array
         * Tour type defualt
         * 
         */
        $itementries['TourType'] = isset($itementries['TourType']) ? $itementries['TourType'] : array();
        $categoryDetails = $this->objHelperGeneral->getCategoryAndPriceArray($itementries['TourType'], 'B2C', $PackageType, $PackageSubType);
        $MPType = $categoryDetails['MPType'];

//        $tourTypeArr = unserialize( CONST_TOURTYPE );
        $tourTypeArr = $categoryDetails['priceArrJson'];

//        echo "<pre>";print_r($categoryDetails);exit;
//        echo "<pre>";print_r($tourTypeArr);exit;
//        echo "<pre>";print_r($hotelStandardArr);exit;
        $itemArray["Cities"] = isset($itemArray["Cities"]) ? $itemArray["Cities"] : array();
        $finalArray = array();
        foreach ($itemArray["Cities"] as $finalData) {
            $finalArray['city'][$finalData['CityId']] = $finalData['Title'];
            $finalArray['Hotel'][] = $finalData['Hotels'];
            $finalArray['Activities'][] = $finalData['Activities'];
            $finalArray['SightSeeings'][] = $finalData['SightSeeings'];
        }

//                echo "<pre>";print_r($finalArray['Hotel']);exit;


        $imageUrl = array();

        $finalArray['Activities'] = isset($finalArray['Activities']) ? $finalArray['Activities'] : array();
        foreach ($finalArray['Activities'] as $ActivitieImage) {
            $u = 0;
            if (isset($ActivitieImage['Activity'])) {
                foreach ($ActivitieImage['Activity'] as $ActivityImageURL) {
                    $imageUrl['Activity'][$u]['ImagePath'] = trim($ActivityImageURL['Image']);
                    $imageUrl['Activity'][$u]['CityName'] = trim($ActivityImageURL['CityId']);
                    $imageUrl['Activity'][$u]['Title'] = trim($ActivityImageURL['Title']);

                    $u++;
                }
            }
        }
        $finalArray['SightSeeings'] = isset($finalArray['SightSeeings']) ? $finalArray['SightSeeings'] : array();
        foreach ($finalArray['SightSeeings'] as $SightSeeingsImage) {
            $u = 0;
            if (isset($SightSeeingsImage['SightSeeing'])) {
                foreach ($SightSeeingsImage['SightSeeing'] as $SightSeeingImageURL) {
                    $imageUrl['SightSeeing'][$u]['ImagePath'] = trim($SightSeeingImageURL['Image']);
                    $imageUrl['SightSeeing'][$u]['CityName'] = trim($SightSeeingImageURL['CityId']);
                    $imageUrl['SightSeeing'][$u]['Title'] = trim($SightSeeingImageURL['Title']);

                    $u++;
                }
            }
        }
        $finalArray['Hotel'] = isset($finalArray['Hotel']) ? $finalArray['Hotel'] : array();
        foreach ($finalArray['Hotel'] as $hotelImage) {
            foreach ($hotelImage['Hotel'] as $hotelImageURL) {


                if (($hotelImageURL['Images'])) {
                    foreach ($hotelImageURL['Images'] as $ImageURL) {
                        $u = 0;
                        foreach ($ImageURL as $finalImage) {
                            $imageUrl['Hotel'][$u]['ImagePath'] = trim($finalImage['URL']);
                            $imageUrl['Hotel'][$u]['CityName'] = $hotelImageURL['CityId'];
                            $imageUrl['Hotel'][$u]['Title'] = $hotelImageURL['Name'];

                            $u++;
                        }
                    }
                }
            }
        }

        $resultSetArray = array(
            'tourType' => $tourTypeArr,
            'tourTypeFull' => $itementries['TourType'],
            'PackageType' => isset($resultSet[0]['PackageType']) ? $resultSet[0]['PackageType'] : '',
            'PackageSubType' => isset($resultSet[0]['PackageSubType']) ? $resultSet[0]['PackageSubType'] : '',
            'PackageSubType' => isset($resultSet[0]['PackageSubType']) ? $resultSet[0]['PackageSubType'] : '',
            'Nights' => isset($resultSet[0]['Nights']) ? $resultSet[0]['Nights'] : '',
            'BookingValidUntil' => $this->objHelperGeneral->changeDateFormat(isset($resultSet[0]['BookingValidUntil']) ? $resultSet[0]['BookingValidUntil'] : '', '/'),
            'imageUrl' => $imageUrl,
            'dayView' => $dayView,
            'dayViewNew' => $dayViewNew,
            'itementries' => $itementries,
            'finalArray' => $finalArray,
            'TPId' => $TPId,
            'Destinations' => isset($resultSet[0]['Destinations']) ? $resultSet[0]['Destinations'] : '',
            'DestinationsId' => $resultSet[0]['DestinationsId'],
            'Keyword' => $resultSet[0]['Keyword'],
            'Description' => $resultSet[0]['Description'],
            'Metatag' => $resultSet[0]['Metatag'],
//            'MPType' => $MPType , // variable is wrong calculated
            'hotels_array_included_only' => $hotels_array_included_only,
            'PackageCategory' => $resultSet[0]['PackageCategory'],
            'Image' => $resultSet[0]['Image'],
             'isFixedDeparture' =>$isFixedDeparture,
        );
        return $resultSetArray;
    }

    public function fetchHotelDetails($categoryId, $gtxID, $packageId, $hotelId) {

        $resultSet = $this->getDbTable()->select()->where("GTXPkgId=?", $gtxID)->where("PkgSysId=?", $packageId)->query()->fetchAll();
        $entry = array();
        foreach ($resultSet as $row) {
            $entry["LongJsonInfo"] = Zend_Json::decode($row["LongJsonInfo"], true);
            $entries = $entry;
        }
        $itemArray = array();
        foreach ($entries['LongJsonInfo'] as $rowItem) {
            $itemArray["Cities"] = $rowItem['Cities']['City'];
        }

        $finalArray = array();
        foreach ($itemArray["Cities"] as $finalData) {
            $finalArray[] = $finalData['Hotels'];
        }
        $hotelArrayData = array();
        foreach ($finalArray as $hotelDetailvalF) {
            foreach ($hotelDetailvalF['Hotel'] as $hotelDetailval) {
                if ($hotelDetailval['RefHotelId'] == $hotelId) {

                    $find = array("_A.jpg", "_B.jpg", "_C.jpg");
                    $finds = array("_t.jpg");
                    $hotelArray['Brief'] = html_entity_decode($hotelDetailval['Brief']);
                    $hotelArray['Name'] = $hotelDetailval['Name'];
                    $hotelArray['MainImg'] = trim($hotelDetailval['MainImg']);
                    $hotelArray['AccoAminities'] = $hotelDetailval['AccoAminities'];
                    $hotelArray['Location'] = $hotelDetailval['Location'];
//                    $hotelArray['Images'] = $hotelDetailval['Images'];
                    
                    if (!empty($hotelDetailval['Images'])) {
                        foreach ($hotelDetailval['Images'] as $key => $value) {
                            foreach ($value as $values) {
                                $hotelArray['Images'][$key][] = [
                                            'ImagId' => $values['ImagId'],
                                            'Type' => $values['Type'],
                                            'Order' => $values['Order'],
                                            'ShortDesc' => $values['ShortDesc'],
                                ];
                                $replace = str_replace($find, "_G.jpg", $values['URL']);
                                if($replace == $values['URL']){
                                    $replace2 = str_replace($finds, "_b.jpg", $values['URL']);
                                }else{
                                    $replace2 = str_replace($find, "_G.jpg", $values['URL']);
                                }
                                $hotelArray['Images'][$key][] = [
                                            'URL' => isset($replace2) ? $replace2  : '',
                                ];
                            }
                        }
                    }
//                    echo"<pre>";print_r($hotelArray);die;
                    $hotelArrayData[] = $hotelArray;
                }
            }
        }
        return $hotelArrayData[0];
    }

    public function fetchActivityDetails($categoryId, $gtxID, $packageId, $hotelId) {
        $resultSet = $this->getDbTable()->select()->where("GTXPkgId=?", $gtxID)->where("PkgSysId=?", $packageId)->query()->fetchAll();
        $entry = array();
        foreach ($resultSet as $row) {
            $entry["LongJsonInfo"] = Zend_Json::decode($row["LongJsonInfo"], true);
            $entries = $entry;
        }
        $itemArray = array();
        foreach ($entries['LongJsonInfo'] as $rowItem) {
            $itemArray["Cities"] = $rowItem['Cities']['City'];
        }

        $finalArray = array();
        foreach ($itemArray["Cities"] as $finalData) {
            $finalArray[] = $finalData['Activities'];
        }
        $hotelArrayData = array();
        foreach ($finalArray as $hotelDetailvalF) {
            if (isset($hotelDetailvalF['Activity'])) {
                foreach ($hotelDetailvalF['Activity'] as $hotelDetailval) {
                    if ($hotelDetailval['RefActivityId'] == $hotelId) {
                        $hotelArray['Brief'] = $hotelDetailval['Description'];
                        $hotelArray['Title'] = $hotelDetailval['Title'];
                        $hotelArray['MainImg'] = trim($hotelDetailval['Image']);
                        $hotelArray['AccoAminities'] = "";
                        $hotelArray['Location'] = "";
                        $hotelArray['Images'] = "";
                        $hotelArrayData[] = $hotelArray;
                    }
                }
            }
        }
        return $hotelArrayData[0];
    }

    public function fetchSightSeeingDetails($categoryId, $gtxID, $packageId, $hotelId) {
        $resultSet = $this->getDbTable()->select()->where("GTXPkgId=?", $gtxID)->where("PkgSysId=?", $packageId)->query()->fetchAll();
        $entry = array();
        foreach ($resultSet as $row) {
            $entry["LongJsonInfo"] = Zend_Json::decode($row["LongJsonInfo"], true);
            $entries = $entry;
        }
        $itemArray = array();
        foreach ($entries['LongJsonInfo'] as $rowItem) {
            $itemArray["Cities"] = $rowItem['Cities']['City'];
        }

        $finalArray = array();
        foreach ($itemArray["Cities"] as $finalData) {
            $finalArray[] = $finalData['SightSeeings'];
        }
        $hotelArrayData = array();
        foreach ($finalArray as $hotelDetailvalF) {
            foreach ($hotelDetailvalF['SightSeeing'] as $hotelDetailval) {
                if ($hotelDetailval['RefSSId'] == $hotelId) {


                    $hotelArray['Brief'] = $hotelDetailval['Description'];
                    $hotelArray['Title'] = $hotelDetailval['Title'];
                    $hotelArray['MainImg'] = trim($hotelDetailval['Image']);
                    $hotelArray['AccoAminities'] = "";
                    $hotelArray['Location'] = "";
                    $hotelArray['Images'] = "";

                    $hotelArrayData[] = $hotelArray;
                }
            }
        }
        return $hotelArrayData[0];
    }

    public function fetchTransportDetails($categoryId, $gtxID, $packageId, $hotelId) {
        $resultSet = $this->getDbTable()->select()->where("GTXPkgId=?", $gtxID)->where("PkgSysId=?", $packageId)->query()->fetchAll();
        $entry = array();
        foreach ($resultSet as $row) {
            $entry["LongJsonInfo"] = Zend_Json::decode($row["LongJsonInfo"], true);
            $entries = $entry;
        }
        $itemArray = array();
        foreach ($entries['LongJsonInfo'] as $rowItem) {
            $itemArray["Cities"] = $rowItem['Cities']['City'];
        }

        $finalArray = array();
        foreach ($itemArray["Cities"] as $finalData) {
            $finalArray[] = $finalData['SightSeeings'];
        }
        $hotelArrayData = array();
        foreach ($finalArray as $hotelDetailvalF) {
            foreach ($hotelDetailvalF['SightSeeing'] as $hotelDetailval) {
                if ($hotelDetailval['RefSSId'] == $hotelId) {


                    $hotelArray['Brief'] = $hotelDetailval['Description'];
                    $hotelArray['Title'] = $hotelDetailval['Title'];
                    $hotelArray['MainImg'] = trim($hotelDetailval['Image']);
                    $hotelArray['AccoAminities'] = "";
                    $hotelArray['Location'] = "";
                    $hotelArray['Images'] = "";

                    $hotelArrayData[] = $hotelArray;
                }
            }
        }
        return $hotelArrayData[0];
    }

    // get opttion on day itinerary by type of options
    public function fetchDayWiseHotelDetails($categoryId, $gtxID, $packageId, $day, $type = 'h') {
        if ($type === 'h') {
            $arrayKey = 'Hotel';
            $arrayKeyMaster = 'Hotels';
            $ItineraryItemNumber = 1;
        } else if ($type === 'a') {
            $arrayKey = 'Activity';
            $arrayKeyMaster = 'Activities';
            $ItineraryItemNumber = 2;
        }

        $resultSet = $this->getDbTable()->select()->where("GTXPkgId=?", $gtxID)->where("PkgSysId=?", $packageId)->query()->fetchAll();
        $entry = array();
        foreach ($resultSet as $row) {
            $entry["LongJsonInfo"] = Zend_Json::decode($row["LongJsonInfo"], true);
            $entries = $entry;
        }
        $dayView = array();
        foreach ($entries['LongJsonInfo'] as $itinerariesDayVise) {
            foreach ($itinerariesDayVise['Itineraries']['Itinerary'] as $dayViewVal) {
                $dayView[$dayViewVal['Day']][$arrayKey][] = $dayViewVal['ItineraryItem'][$ItineraryItemNumber];
            }
        }


        $itemArray1 = array();
        foreach ($entries['LongJsonInfo'] as $rowItem) {
            $itemArray1["Cities"] = $rowItem['Cities']['City'];
        }

        $finalArray = array();
        foreach ($itemArray1["Cities"] as $finalData) {
            $finalArray[] = $finalData[$arrayKeyMaster];
        }



        $itemArray = [];
        foreach ($dayView[$day][$arrayKey] as $rowItem) {
//            echo '<pre>';
//            print_r($rowItem['Items']);
//            echo '</pre>';
            if ($type === 'h') {
                if ($rowItem['Items'][0]['Id'] == $categoryId) {
                    $itemArray[] = $rowItem['Items'][0]['Item'];
                }
            } else {
                $itemArray[] = $rowItem['Items'][0]['Item'];
            }
        }

        $optionsArray = [ "itemArray" => $itemArray, "finalArray" => $finalArray];
        // echo "<pre>";print_r($optionsArray);exit;
        return $optionsArray;
    }

    // get opttion on day itinerary by type of options added By Piyush
    public function fetchDayWiseOptionsDetails($categoryId, $gtxID, $packageId, $day, $sid, $type = 'h') {
        if ($type === 'h') {
            $arrayKey = 'Hotel';
            $arrayKeyMaster = 'Hotels';
            $ItineraryItemNumber = 1;
        } else if ($type === 'a') {
            $arrayKey = 'Activity';
            $arrayKeyMaster = 'Activities';
            $ItineraryItemNumber = 2;
        } else if ($type === 's') {
            $arrayKey = 'SightSeeing';
            $arrayKeyMaster = 'SightSeeings';
            $ItineraryItemNumber = 3;
        }


        $resultSet = $this->getDbTable()->select()->where("GTXPkgId=?", $gtxID)->where("PkgSysId=?", $packageId)->query()->fetchAll();
        $entry = [];
        foreach ($resultSet as $row) {
            $entry["LongJsonInfo"] = Zend_Json::decode($row["LongJsonInfo"], true);
            $entries = $entry;
        }

        $dayView = [];
        foreach ($entries['LongJsonInfo'] as $itinerariesDayVise) {
            foreach ($itinerariesDayVise['Itineraries']['Itinerary'] as $dayViewVal) {
                $dayView[$dayViewVal['Day']][$arrayKey][] = $dayViewVal['ItineraryItem'][$ItineraryItemNumber];
            }
        }

        $itemArray1 = [];
        foreach ($entries['LongJsonInfo'] as $rowItem) {
            $itemArray1["Cities"] = $rowItem['Cities']['City'];
        }

        $finalArray = [];
        foreach ($itemArray1["Cities"] as $finalData) {
            $finalArray[] = $finalData[$arrayKeyMaster];
        }


        $itemArray = $arrayOfItems = [];
        foreach ($dayView[$day][$arrayKey] as $rowItem) {
//            echo '<pre>';
//            print_r($rowItem['Items']);
//            echo '</pre>';
            if ($type === 'h') {
//                if($rowItem['Items'][0]['Id'] == $categoryId){
//                    $itemArray[] = $rowItem['Items'][0]['Item'];
//                }
                foreach ($rowItem['Items'] as $key => $value) {
                    if ($value['Id'] == $categoryId) {
                        $itemArray[] = (isset($value['Item'])) ? $value['Item'] : '';
                    }
                }
            } else {
//                print_r($rowItem['Items'][0]['Item']);
                $arrayOfItems = $this->objHelperGeneral->filterArrayByValueKeyPair(['Id', $sid], $rowItem['Items'][0]['Item']);
//                if( $arrayOfItems[0]['Id'] == $sid){
//                }
//                print_r($arrayOfItems);
                $itemArray[] = $arrayOfItems;
            }
        }

        $optionsArray = [ "itemArray" => $itemArray, "finalArray" => $finalArray];
//       echo "<pre>";print_r($itemArray);exit;
        return $optionsArray;
    }

    // get options of transfers | added By Piyush
    public function fetchTransfersDetails($packageid, $gtxid, $tourtype = 0) {
        $isGroup = ($tourtype == 2) ? 1 : 0;

        $resultSet = $this->getDbTable()->select()->where("GTXPkgId=?", $gtxid)->where("PkgSysId=?", $packageid)->query()->fetchAll();
        $entry = [];
        foreach ($resultSet as $row) {
            $entry["LongJsonInfo"] = Zend_Json::decode($row["LongJsonInfo"], true);
            $entries = $entry;
        }

        $itemArray = [];
        foreach ($entries['LongJsonInfo'] as $rowItem) {
            $itemArray["Transfers"] = $rowItem['Transfers'];
        }

        $finalArray = $itemArray1 = [];
        foreach ($itemArray["Transfers"] as $rowItem) {
//                   echo "<pre>";print_r($rowItem);

            if ($rowItem['transType'] === 'car') {

                if ($isGroup == $rowItem['isGroup']) {
                    $itemArray1[] = [
                        'IsIncluded' => $rowItem['isIncluded'],
                        'fixTransSysId' => $rowItem['fixTransSysId'],
                        'cityCovered' => $rowItem['cityCovered'],
                        'vehSysId' => $rowItem['vehSysId'],
                        'vehicleName' => $rowItem['vehicleName'],
                        'routeName' => $rowItem['routeName'],
                        'costPerson' => $rowItem['costPerson'],
                        'isGroup' => $rowItem['isGroup'],
                        'capacity' => $rowItem['capacity'],
                    ];
                }
            }
        }


        $optionsArray = [ "itemArray" => [$itemArray1], "finalArray" => [$finalArray]];
//       echo "<pre>";print_r($optionsArray);exit;
        return $optionsArray;
    }

    // get package category options here | added By Piyush on 28 Aug 2018
    public function fetchPackageCateogies($packageid, $gtxid) {
        $resultSet = $this->getDbTable()->select()->where("GTXPkgId=?", $gtxid)->where("PkgSysId=?", $packageid)->query()->fetchAll();
        $entry = [];
        foreach ($resultSet as $row) {
            $entry["LongJsonInfo"] = Zend_Json::decode($row["LongJsonInfo"], true);
            $entry["PackageType"] = ($row["PackageType"]);
            $entries = $entry;
        }

        $tourtype = $category = [];

        $getCat = $this->objHelperGeneral->getCategoryAndPriceArray($entries['LongJsonInfo']['package']['TourTypes']['MarketType'], 'B2C', $entries['PackageType']);
//        echo "<pre>";print_r($getCat['category']);exit;

        $optionsArray = [ "itemArray" => ['tourtype' => $getCat['tourtype'], 'category' => $getCat['category']], "finalArray" => []];
        return $optionsArray;
    }

    public function checkPackaageSysID($gtxId) {
        $select = $this->getDbTable()->select()
                        ->from("tb_tbb2c_packages_master", array("PkgSysId"))
                        ->where("GTXPkgId=?", $gtxId)->where("IsActive=?", '1')->where("IsPublish=?", '1')->where("IsMarkForDel=?", '0');

        $result = $this->getDbTable()->fetchRow($select);

        if ($result == NULL)
            return false;
        else
            return $result->toArray();
    }

}
