<?php
/***************************************************************
* Catabatic Technology Pvt. Ltd.
* File Name     : IndexController.php
* File Desc.    : General helper to including supporting functions/methods
* Created By    : Piyush Tiwari <piyush@catpl.co.in>
* Created Date  : 27 Oct 2018
* Updated Date  : 01 Dec 2018
***************************************************************/


class Zend_Controller_Action_Helper_General extends Zend_Controller_Action_Helper_Abstract
{

    /**
     * @var Zend_Loader_PluginLoader
     */

    public $pluginLoader;
    private $db = NULL;
    private $hotelTypeArr;
    private $tourTypeArr;
    protected $currentDateTime;

    public $baseUrl;
    const USER_NAMESPACE = 'PSESS';
    public $_storage;
    public $packageSession;

    public $packageTypeStatic;
    public $myNamespace;


    /**
     * Constructor: initialize plugin loader
     * 
     * @return void
     */

    public function __construct()
    { 
        $this->pluginLoader = new Zend_Loader_PluginLoader();
        $this->db = Zend_Db_Table::getDefaultAdapter();
        
        $this->tourTypeArr = unserialize(CONST_TOURTYPE);

        
        $BootStrap    = $this->config();
        
        $this->siteName = $BootStrap['siteName'];
        $this->baseUrl  = $BootStrap['siteUrl'];
        
        $this->currentDateTime      = date('Y-m-d');
        $this->packageTypeStatic    = $BootStrap['packageTypeDynamic'];

        $this->_storage = new Zend_Session_Namespace(self::USER_NAMESPACE);

        
    }


    public function config()
    {
        $front = $this->getFrontController();
        $bootstrap = $front->getParam('bootstrap');
        if (null === $bootstrap) {
            throw new Exception('Unable to find bootstrap');
        }
//        return $bootstrap->getOptions();
        return $bootstrap->getOptions()['bootstrap'];
    }

public function getLatitudeLongitude($strAddress) {
        // Get lat and long by address         
        $prepAddr = str_replace(' ', '+', $strAddress);
        
        $geocode = file_get_contents( GOOGLE_MAPS_API_GEOCODE_URL .'?address=' . $prepAddr . '&sensor=false');
        $output = json_decode($geocode);
        $arrLocation = array();
        if (count($output->results) > 0) {
            $latitude = $output->results[0]->geometry->location->lat;
            $longitude = $output->results[0]->geometry->location->lng;
            $arrLocation['latitude'] = $latitude;
            $arrLocation['longitude'] = $longitude;
            return $arrLocation;
        }
        return $arrLocation;
    }
    public function getPlaceDetails($strAddress) {
        // Get lat and long by address         
        $prepAddr = str_replace(' ', '+', $strAddress);
        
        $geocode = file_get_contents( GOOGLE_MAPS_API_GEOCODE_URL .'?address=' . $prepAddr . '&sensor=true');
        $output = json_decode($geocode);
       
        $arrLocation = array();
        if (count($output->results) > 0) {
            $arrLocation = $output->results;
            return $arrLocation;
        }
        return $arrLocation;
    }
    
    public function getFileExtension($file) {
        if(!empty($file)) {
            $arrInfo = pathinfo($file);
            return strtolower($arrInfo['extension']);
        }
    }
    
    // get tour type char here
    public function getTourTypeChar( $defaultTourType ) {
//        if ( !$defaultTourType ) {
//            throw new Exception(" function " . __FUNCTION__ . " need parameter as int [ 1 or 2 ] " );
//        }
        return ($defaultTourType ==1) ? 'P' : 'G';
    }
    
    
    // params : array [key , value] , array
    /*
        [0] => Array
        (
            [id] => 101
            [name] => ranvir
        )
        [1] => Array
        (
            [id] => 102
            [name] => amit
        )
    */

    public function filterArrayByValueKeyPair( $keyValuePair , array $array )
    {
//        $arr = $this->array_change_key_case_recursive($array,CASE_LOWER);
        $arr = $array;
        $returnArr  = [];
        $tempArr    = explode('|', $keyValuePair[1]);

//        echo '<pre>'; print_r($keyValuePair); die;
//        echo '<pre>'; print_r($array); die;

        foreach ( $arr as $k => $val ) {
            if(isset($val[$keyValuePair[0]]) && !empty($val[$keyValuePair[0]])) {
                if(in_array(($val[$keyValuePair[0]]) , $tempArr ))
                $returnArr[] = $val;
            }
        }
//        echo '<pre>'; print_r($returnArr); die;

        return $returnArr;
    }
    
    

    public function mergeMultiArray($OriginalArray , $SecondArray) {
        $i=0;
        $NewArray = array();
        foreach($OriginalArray as $value) {
            $NewArray[] = array_merge($value,($SecondArray[$i]));
            $i++;
        }
        return $NewArray;
    }
    


    public function getArrayKeyByValue( $val , array $arr )
    {
        return array_search($val , $arr);
    }

    
    public function getArrayValueByKey( $key , array $array )
    {
        $value  = $array[$key];
        if(!is_array($value))
            return $value;
        else{
            return $value;
        }
    }

    
    public function getValueByKeyFromArray( $key , array $array )
    {
//        $data = $this->array_change_key_case_recursive($array,CASE_LOWER);
        return $array[$key];
    }

    
    
    // converts all keys in a multidimensional array to lower or upper case
    public function array_change_key_case_recursive($arr, $case=CASE_LOWER)
    {
      return array_map(function($item)use($case){
        if(is_array($item))
            $item = $this->array_change_key_case_recursive($item, $case);
        return $item;
      },array_change_key_case($arr, $case));
    }
    
    

    public function getRatingImage($rating , $fullRating = 5) {

        $ratingStar       = '<img src="'. $this->baseUrl .'public/images/red-star1.png">';
        //$ratingStar_half  = '<img src="'. $this->baseUrl .'public/images/smtRating_half.png">';
        $ratingStar_light = '<img src="'. $this->baseUrl .'public/images/gray-star1.png">';

        $ratingImage = '';

        for($i = 0; $i < $rating; $i++ ) {
            $ratingImage .= $ratingStar;
        }

        $grayStars = $fullRating - $rating;
        
        for($i = 0; $i < $grayStars; $i++ ) {
            $ratingImage .= $ratingStar_light;
            $ratingImage;
        }

        return $ratingImage;
    }

    
    public function getInclusionIcon($Inclusions , $stringSeparator )
    {
        $ret = $icon = '';
        $InclusionsMaster = unserialize( CONST_INCLUSIONS_MASTER );
        
        foreach (explode($stringSeparator , $Inclusions) as $inc) {
            $icon = (isset($InclusionsMaster[$inc])) ? $InclusionsMaster[$inc] :"question" ;

            if($icon != 'question') {
            $ret .= "<i class=\"fa fa-{$icon}\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"{$inc}\"></i>";
            }
        }
        return $ret;
    }

       
    public function round_up($value, $places) 
    {
        $mult = pow(10, abs($places)); 
         return $places < 0 ?
        ceil($value / $mult) * $mult :
            ceil($value * $mult) / $mult;
    }

    
    
    public function createRange($rangeLimits=null) {
    
        if($rangeLimits != null)
        sort($rangeLimits);
        
        //Setting range limits.
        //$rangeLimits = array(0,10,32,50,250,500,2000);
        $ranges = array();

        for($i = 0; $i < count($rangeLimits); $i++){
            if($i == count($rangeLimits)-1){
                break;
            }
            $lowLimit = $rangeLimits[$i];
            $highLimit = $rangeLimits[$i+1];

            $ranges[$i]['ranges']['min'] = $lowLimit;
            $ranges[$i]['ranges']['max'] = $highLimit;
            //echo '<pre>';echo ($ranges[$i]['ranges']['min']);
            //echo '<pre>';echo($ranges[$i]['ranges']['max']);
            foreach($rangeLimits as $perPrice){

                if($perPrice >= $lowLimit && $perPrice < $highLimit){
                    $ranges[$i]['values'][] = $perPrice;
                }
            }
        }
        //die('mm');
        return $ranges;
    }
    
    
    
    public function filterDuplicateItinerary( $array_key ,  array $array )
    {
        
        $temp = []; $res = [];
        if(count($array)) {
            foreach ($array as $key => $val) {
                $res[$val['day']["$array_key"]] = $val;
            }
        }
        $res = array_values($res); // resetting the array keys
        return $res;
    }

    
    
    public function moneyFormatINR($number)
    {
//        var_dump($number);
        $sign = '';
        if( $number <0) {
            $sign = '-';
            $number = trim($number , '-');
        }
//        echo (int)$number;
        
        if(is_float($number)) {
            $n  = explode('.', $number);
            $number = $n[0];
            $decimal= isset($n[1]) ? $n[1] : 0;
        }
        else {
            $number     = $number;
            $decimal    = 0;
        }

        $explrestunits = "";
        if (strlen($number) > 3) {
            $lastthree = substr($number, strlen($number) - 3, strlen($number));
            $restunits = substr($number, 0, strlen($number) - 3); // extracts the last three digits
            $restunits = (strlen($restunits) % 2 == 1) ? "0" . $restunits : $restunits; // explodes the remaining digits in 2's formats, adds a zero in the beginning to maintain the 2's grouping.
            $expunit = str_split($restunits, 2);
            for ($i = 0; $i < sizeof($expunit); $i++) {
                // creates each of the 2's group and adds a comma to the end
                if ($i == 0) {
                    $explrestunits .= (int) $expunit[$i] . ","; // if is first value , convert into integer
                } else {
                    $explrestunits .= $expunit[$i] . ",";
                }
            }
            $thecash = $explrestunits . $lastthree;
        } else {
            $thecash = $number;
        }
//        $thecash = number_format($number, 2);
        if($decimal > 0)
            return $sign . $thecash .'.'. $decimal;
        else
            return $sign . $thecash; // writes the final format where $currency is the currency symbol.
    }
    
    
    public function array_filter_rv($array)
    {
        return array_values(array_intersect_key($array, array_unique(array_map('serialize', $array))));
    }

    
    // change date format | params : datetime , delimiter[ default : - ] | returns : formated date
    public function changeDateFormat( $dt, $delimiter="-" )
    {
        return date( "d" .$delimiter. "m" .$delimiter. "Y", strtotime($dt) );
    }


    
    // add days into given date
    // params : date [Y/m/d] , number of days | returns : formated date
    public function dateAddDays( $date , $days )
    {
        return date('d/m/Y', strtotime($date . " +$days day"));
    }





    // creates price range params : start value , End value , fractions/multiples of number
    function createRangeRv ( $start , $end , $fraction )
    {
//        echo $start ."|". $end ."|". $fraction;
        
        if($fraction > $end)
            return "Invalid";

        $total      = ceil($end/$fraction);
//        $loopstart  = (floor($start/$fraction) ==0) ? 1 : 0;
        $loopstart  = 1;

        $start	= ($fraction*$loopstart);
        $end 	= ($fraction*$total);

        return range($start, $end , $fraction);
    }
    

    // creates price range dropdown | params : start value , End value , fractions/multiples of number
    function getPriceDropdown( $min, $max , $fraction , $minPriceArray = [] )
    {
        $min = ( int ) str_replace( ',', '', $min );
        $max = ( int ) str_replace( ',', '', $max );

        $limit = $this->createRangeRv( $min , $max , $fraction );
//        print_r($limit);
//        echo "<pre>";print_r($minPriceArray);die;

        $newarr = [];
        $next   = 1;
        if($limit !== 'Invalid') {
          foreach ($limit as $key => $value) {
              // check if the price range is exists or not
              if( $this->checkRangeExists( $value , $minPriceArray) ) {
                $newarr[$next .'-'. $value] = $next .'-'. $value;
                $next = $value+1;
              }
//              if($value==30000) {
////                  break;
//                  echo $value;
//                    var_dump($this->checkRangeExists( $value , $minPriceArray));
//              }
          }
        }
        return $newarr;
    }

    
    public function checkRangeExists( $num , array $arr )
    {
        $returnval = false;
        
        foreach ($arr as $value) {
            if( $num < $value ) {
                $returnval = true;
                break;
            }
        }

        return $returnval;
    }

    
    public function getPackagePrice( $defaultCategory , $tourTypeChar , array $priceArray , $discounted = false)
    {
//        echo $defaultCategory , $tourTypeChar;
//        print_r($priceArray);
//            die();
        $PriceResultArr = $priceArray[$tourTypeChar][$defaultCategory]['price'][0];
        
        // discounted true means lesser amount | discount excluded in amount 
        if($discounted) {
            $finalp = (float)$PriceResultArr['PricePerPerson'];
        }
        else {
            $discountValue = 0;

            if( $PriceResultArr['DiscountType']=== 1 ) {
                $discountValue = (float)$PriceResultArr['DiscountVal'];
            }
            else if( $PriceResultArr['DiscountType']=== 2 ){
                $discountValue = (float)$PriceResultArr['DiscountVal'];
            }
            else {
                $discountValue = 0;
            }

            $finalp = (float)$PriceResultArr['PricePerPerson'] + $discountValue;
        }
        
        return $finalp;
    }

    
  
     
    public function filterTransportItinerary( array $masterData , $type='' )
    {
        $tempArr = [];
        if(count($masterData))
        {
            foreach ($masterData as $key => $value) {
//                print_r($value);
                if( $value['transType'] == strtolower($type) ) {
                    $tempArr[] = $value;
                }
            }
        }
        return $tempArr;
    }

    public function getTransportItinerary( array $array , array $masterData , $type='' )
    {
        
        $tempArr = $this->filterTransportItinerary($masterData, $type);
        $masterData = $tempArr;
         
        $itineraryArr   = $array;
        $count_Itinerary= count($itineraryArr);

//        echo "<pre>"; print_r($itineraryArr[0]['ItineraryItem']); die;

        $itineraryArrCustom = $SightSeeingItineraryArr = $SightSeeingITINERARY_ITEM_Result = [];
        $SightSeeingITINERARY = $SightSeeingITINERARY_ITEM = $SightSeeingITINERARY_Result = $SSItiArray = '';

        for ($i=0; $i < $count_Itinerary; $i++ ) {
            
            $SightSeeingITINERARY   = $this->filterArrayByValueKeyPair( ['Type', 'TRANSFERS' ], $itineraryArr[$i]['ItineraryItem'] );
//                    echo "<pre>"; print_r($SightSeeingITINERARY); die;
            
            // get sightseeing of the days Itinerary
            if($SightSeeingITINERARY[0]['Type'] === 'TRANSFERS' ) {

                // fetch which transfer is included true / false
                if( count($SightSeeingITINERARY[0]['Items']) ) {

                    $SSItiArray = $SightSeeingITINERARY[0]['Items']; // get single day sightsing list
                    
                    $TransferArr = [];
                    foreach ($SSItiArray as $key => $value) {
//                        echo '<pre>'; print_r($value);
                        if( isset($value['Type']) && (strtolower($type) === $value['Type'])  )
                        {
//                            echo '<pre>'; print_r($value); die;
                            $TransferArrInner = [];
                            foreach ($value['Item'] as $k => $v) {
                                $TransferArrInner[] = ['itemid' => $v['Id'] , 'IsIncluded'=> $v['IsIncluded']];
                            }
                            $TransferArr[] = $TransferArrInner;
                        }
                    }
                    
                    
                    $itineraryArrCustom[$i] = ['Day' => $itineraryArr[$i]['Day'], 'FinalArray'=> $TransferArr];
                }

            }
        }
        
//        echo '<pre>'; print_r( $itineraryArrCustom ); die;
        
//        return $transfersArray;
        return $itineraryArrCustom;
        
    }
    
    
    
    public function createArrayDetailDynamic( array $array , array $param)
    {
        if( !is_array($array) )
        {
            throw new Exception("function 'createArrayDetailDynamic(array , array)' expects array only as input parameter.");
        }
        else {

            $catId  = $param['catid'];
            $gtxId  = $param['gtxid'];
            $packageId  = $param['pkgid'];
            $tourtype   = $param['tourtype'];

            $detail = $array; // assign the input array to $detail 
            
            $dayViewNew     = $detail['dayViewNew'];
//        echo '<pre>'; print_r( $dayViewNew ); die;

            // start : array for day itinerary view
            $dayItineraryArray = [];
            $iti = ''; $tempArr = [];
            for( $r = 0; $r< count($dayViewNew); $r++)
            {
                $iti = $dayViewNew[$r]['day']['Day'];
                if(!in_array($iti, $tempArr)) {
                  $dayItineraryArray[$iti] = $dayViewNew[$r];
                  $tempArr[] = $iti;
                }
                else {
                  $dayItineraryArray[$iti]['repeat'] = 1;
                  $dayItineraryArray[$iti]['day']['Title'] = [$dayViewNew[$r-1]['day']['Title'] , $dayViewNew[$r]['day']['Title']];
                  $dayItineraryArray[$iti]['day']['Program'] = [$dayViewNew[$r-1]['day']['Program'] , $dayViewNew[$r]['day']['Program']];
                  $dayItineraryArray[$iti]['Activities'] = [$dayViewNew[$r-1]['Activities'] , $dayViewNew[$r]['Activities']];
                  $dayItineraryArray[$iti]['SightSeeings'] = [$dayViewNew[$r-1]['SightSeeings'] , $dayViewNew[$r]['SightSeeings']];
                }
            }
            // end : array for day itinerary view
            
            $PackageType = $detail['PackageType'];
            $GTXPkgId = $gtxId;
            $PkgSysId = $packageId;

    
    $BookingValidUntil = $detail['BookingValidUntil'];
//    $TPId   = $detail['tourType'][$catId]; 

    // dynamic package for | 2
    if( $detail['PackageType'] == 2 ) {
        $TPId   = $detail['TPId'];
    } else {
        $TPId   = @$detail['tourType'][$catId];
    }
    
// do dynamic
    $CitiesArray = isset($detail['finalArray']['city'])?$detail['finalArray']['city']:array();;
    $hotelHotelArray = array();
    $transfersArray = $ItineraryIdArray = $hotelIdArray = [];
    
    
    $itinerariesArray = [];
    $detail['itementries']['Itineraries']['Itinerary'] = isset($detail['itementries']['Itineraries']['Itinerary'])?$detail['itementries']['Itineraries']['Itinerary']:array();
    foreach ($detail['itementries']['Itineraries']['Itinerary'] as $itineraries) {
        $itinerariesDays = $itineraries['Day'];
        $ItineraryId    = $itineraries['ItineraryId'];
        
        $cityArray = [];
        foreach ($itineraries['ItineraryItem'] as $itinerariesItem) {
            
            if ($itinerariesItem['Type'] == 'CITY') {
                $cityArray[] = [ 'itemid'=>$itinerariesItem['Id'] ];
            }
            
            if ($itinerariesItem['Type'] == 'HOTEL') {
                $hotelHotelArray = [];
                foreach ($itinerariesItem['Items'] as $itinerariesHotel) {
                    if ($itinerariesHotel['Id'] == $catId) {
                        if( isset($itinerariesHotel['Item'] )) {
                            foreach ($itinerariesHotel['Item'] as $itinerariesDayHotel) {

                                if($itinerariesDayHotel['Id']) {
                                    $hotelHotelArray[] = [
                                        'itemid'=> $itinerariesDayHotel['Id'],
                                        'MasterIntSysId'=> $itinerariesDayHotel['MasterIntSysId'],
                                        'MealPlanId'=> isset($itinerariesDayHotel['MealPlanId']) ? $itinerariesDayHotel['MealPlanId'] : '',
                                        'IsIncluded'=> ($itinerariesDayHotel['IsIncluded']) ? $itinerariesDayHotel['IsIncluded'] : false
                                        ];
                                    $hotelIdArray[] = $itinerariesDayHotel['Id'];
                                }
                            }
                        }
                    }
                }
            }
            
            if ($itinerariesItem['Type'] == 'ACTIVITY') {
                foreach ($itinerariesItem['Items'] as $itinerariesHotel) {
                    $ActivitiesArray = [];
                    foreach ($itinerariesHotel['Item'] as $itinerariesDayHotel) {
                        if($itinerariesDayHotel['Id']) {
                            $ActivitiesArray[] = ['itemid'=> $itinerariesDayHotel['Id'], 'IsIncluded'=> ($itinerariesDayHotel['IsIncluded']) ? $itinerariesDayHotel['IsIncluded'] : false ];
                        }
                    }
                }
            }
            
            if ($itinerariesItem['Type'] == 'SIGHTSEEING') {
                foreach ($itinerariesItem['Items'] as $itinerariesHotel) {
                    $SightSeeingsArray = [];
                    foreach ($itinerariesHotel['Item'] as $itinerariesDayHotel) {
                        if($itinerariesDayHotel['Id']) {
                            $SightSeeingsArray[] = ['itemid'=> $itinerariesDayHotel['Id'], 'IsIncluded'=> ($itinerariesDayHotel['IsIncluded']) ? $itinerariesDayHotel['IsIncluded'] : false ];
                        }
                    }
                }
            }
            
            if ($itinerariesItem['Type'] == 'TRANSFERS') {
//                        echo count($itinerariesItem['Items']);
                $transfersITIFinalArray =[];
                foreach ($itinerariesItem['Items'] as $itinerariesHotel) {

                        $transfersITIArray = [];
                        foreach ($itinerariesHotel['Item'] as $itinerariesDayHotel) {

                            $transfersITIArray[] = [ 'itemid'=> $itinerariesDayHotel['Id'], 'IsIncluded'=> ($itinerariesDayHotel['IsIncluded']) ? $itinerariesDayHotel['IsIncluded'] : false ];

                            if ($itinerariesDayHotel['IsIncluded'] == true) {
                                $transfersArray[$itinerariesHotel['Type']][$itineraries['Day']][] = $itinerariesDayHotel['Id'];
                            }
                        }
                        if( count($transfersITIArray) ) {
                            $transfersITIFinalArray[$itinerariesHotel['Type']] = $transfersITIArray;
                        }

                }
            }
            
        }
        
        $itinerariesArray[$ItineraryId]['day']          = $itinerariesDays;
        $itinerariesArray[$ItineraryId]['city']         = $cityArray;
        $itinerariesArray[$ItineraryId]['hotel']        = $hotelHotelArray;
        $itinerariesArray[$ItineraryId]['activity']     = $ActivitiesArray;
        $itinerariesArray[$ItineraryId]['sightSeeing']  = $SightSeeingsArray;
        $itinerariesArray[$ItineraryId]['transfers']    = @$transfersITIFinalArray;
        
    }

    $itinerariesDays = $ItineraryIdArray = [];


// start : code for masters activity / sightseeing / hotels etc.
    
    $cityMaster = $hotelsMaster = $activityMaster = $ssMaster = [];
    $detail['itementries']['Cities']  = isset($detail['itementries']['Cities'])?$detail['itementries']['Cities']:array();
    foreach( $detail['itementries']['Cities'] as $key => $value )
    {
        if(isset($value['Hotels']['Hotel'])) {
            foreach( $value['Hotels']['Hotel'] as $keyH => $valueH )
            {
                $hotelsMaster[$valueH['RefHotelId']] = $valueH;
            }
        }
        
        if(isset($value['Activities']['Activity'])) {
            foreach( $value['Activities']['Activity'] as $keyH => $valueH )
            {
                $activityMaster[$valueH['RefActivityId']] = $valueH;
            }
        }
        
        if(isset($value['SightSeeings']['SightSeeing'])) {
            foreach( $value['SightSeeings']['SightSeeing'] as $keyH => $valueH )
            {
                $ssMaster[$valueH['RefSSId']] = $valueH;
            }
        }
        
    }
        $cityMaster['Hotels']       = $hotelsMaster;
        $cityMaster['Activities']   = $activityMaster;
        $cityMaster['SightSeeings'] = $ssMaster;

// end : code for masters activity / sightseeing / hotels etc.

//echo "<pre>"; print_r($hotelsMaster); exit;   



//    start : merge car transfers into transferArray
//
    $carTrans = [];
    if( isset($detail['itementries']['TransfersMaster']) && count($detail['itementries']['TransfersMaster']) ) {
        foreach($detail['itementries']['TransfersMaster'] as $key => $val )
        {
            if(strtolower($val['transType']) == 'car') {
                $carTrans['car'][] = $val;
            }
        }
    }
//    
//    end : merge car transfers into transferArray
    
    $transfersArray = array_merge( $transfersArray , $carTrans );
    
    
//echo "<pre>";print_r($detail['itementries']['TourType']);exit;   
//echo "<pre>"; print_r($carTrans); exit;   
//echo "<pre>"; print_r($transfersArray); exit;   
    
/*    
    $CategoriesArray = array();
    foreach ($detail['itementries']['TourType'] as $TourType) {
        if ($TourType['TourType'] == $tourtype) {
            foreach ($TourType['Categories'] as $Categories) {
                foreach ($Categories as $CategoriesType) {
                    if ($CategoriesType['CategoryId'] == $catId) {
                        $CategoriesArray['TourTypeTitle'] = $TourType['TourTypeTitle'];
                        $CategoriesArray['TourType'] = $TourType['TourType'];
                        $CategoriesArray['Type'] = $CategoriesType['Type'];
                        foreach ($CategoriesType['PriceAdditional'] as $PriceAdditional) {
                           // $CategoriesArray['NetPrice'] = $PriceAdditional['PricePerPerson'];
                          //  $CategoriesArray['DiscountNetPrice'] = (float) $PriceAdditional['PricePerPerson'] + (float) $PriceAdditional['DiscountVal'];
                        }
                          $CategoriesArray['NetPrice'] = $CategoriesType['PriceAdditional'][0]['PricePerPerson'];
                          $CategoriesArray['DiscountNetPrice'] = (float) $CategoriesType['PriceAdditional'][0]['PricePerPerson'] + (float) $CategoriesType['PriceAdditional'][0]['DiscountVal'];
                     
                    }
                }
            }
        }
    }
 * 
 */
    
    $CategoriesArray = array();
    foreach ($detail['itementries']['TourType'] as $TourType) {
        if ($TourType['TourType'] == $tourtype) {
            foreach ($TourType['Categories'] as $Categories) {
                foreach ($Categories as $CategoriesType) {
                    if ($CategoriesType['CategoryId'] == $catId) {
                        $CategoriesArray['TourTypeTitle'] = $TourType['TourTypeTitle'];
                        $CategoriesArray['TourType'] = $TourType['TourType'];
                        $CategoriesArray['Type'] = $CategoriesType['Type'];
                        foreach ($CategoriesType['PriceAdditional'] as $PriceAdditional) {
                           // $CategoriesArray['NetPrice'] = $PriceAdditional['PricePerPerson'];
                          //  $CategoriesArray['DiscountNetPrice'] = (float) $PriceAdditional['PricePerPerson'] + (float) $PriceAdditional['DiscountVal'];
                        }
                          $CategoriesArray['NetPrice'] = $CategoriesType['PriceAdditional'][0]['PricePerPerson'];
                          $CategoriesArray['DiscountNetPrice'] = (float) $CategoriesType['PriceAdditional'][0]['PricePerPerson'] + (float) $CategoriesType['PriceAdditional'][0]['DiscountVal'];
                          
                        if($CategoriesType['MPType']) {
                            $mparray = [];
                            foreach ( $CategoriesType['MPType'] as $key => $value ) {
                                $mparray[] = [
                                    'id'=> $value['MPTypeId'],
                                    'txt'=> $value['MPTypeText'],
                                    'sfp'=> $value['SeletedForPackage'],
                                    'price'=> $value['PriceAdditional'],
                                ];
                            }
                        }
                     
                    }
                }
            }
        }
    }
    
    /* get default category 
    * hotel standard array
    * Tour type defualt
    * 
    */
    $categoryDetails = $this->getCategoryAndPriceArray ( $detail['itementries']['TourType'], 'B2C'  );
    $CategoriesArray = $categoryDetails['priceArrJson'];
    
    
    
    
    $ActivitiesArray = array();
    foreach ($detail['dayView'] as $itineraries) {
        foreach ($itineraries['Activities'] as $itinerariesItem) {
            //echo "<pre>";print_r($itinerariesItem);exit;
            if ($itinerariesItem['Type'] == 'ACTIVITY') {
                foreach ($itinerariesItem['Items'] as $itinerariesHotel) {
                    foreach ($itinerariesHotel['Item'] as $itinerariesDayHotel) {
                        if ($itinerariesDayHotel['IsIncluded'] == true) {
                            $ActivitiesArray[] = $itinerariesDayHotel['Id'];
                        }
                    }
                }
            }
        }
    }
    $SightSeeingsArray = array();
    foreach ($detail['dayView'] as $itineraries) {
        foreach ($itineraries['SightSeeings'] as $itinerariesItem) {
            // echo "<pre>";print_r($itinerariesItem);exit;
            if ($itinerariesItem['Type'] == 'SIGHTSEEING') {
                foreach ($itinerariesItem['Items'] as $itinerariesHotel) {
                    foreach ($itinerariesHotel['Item'] as $itinerariesDayHotel) {
                        if ($itinerariesDayHotel['IsIncluded'] == true) {
                            $SightSeeingsArray[] = $itinerariesDayHotel['Id'];
                        }
                    }
                }
            }
        }
    }

    $TransfersMaster    = isset($detail['itementries']['TransfersMaster'])?$detail['itementries']['TransfersMaster']:'';
    $OtherServices      = isset($detail['itementries']['OtherServices'])?$detail['itementries']['OtherServices']:'';

            
            
            
            $finalarray = [
                'dayItineraryArray' => $dayItineraryArray ,
                'CitiesArray' => $CitiesArray ,
                'CategoriesArray' => $CategoriesArray ,
                'ActivitiesArray' => $ActivitiesArray ,
                'SightSeeingsArray' => $SightSeeingsArray ,
                'hotelHotelArray' => $hotelHotelArray ,
                'hotelIdArray' => $hotelIdArray ,
                'OtherServices' => $OtherServices ,
                'TransfersMaster' => $TransfersMaster ,
                'transfersArray' => $transfersArray ,
//                'transfersITIArray' => $transfersITIArray ,
                'itinerariesDays' => $itinerariesDays ,
                'ItineraryIdArray' => $ItineraryIdArray ,
                'itementries' => $detail['itementries'] ,
                'dayView' => $detail['dayView'] ,
                'dayViewNew' => $detail['dayViewNew'] ,
                'finalArray' => $detail['finalArray'] ,
                'PackageType' => $detail['PackageType'] ,
                'BookingValidUntil' => $detail['BookingValidUntil'] ,
                'tourType' => $detail['tourType'] ,
                'imageUrl' => $detail['imageUrl'] ,
                'itinerariesArray' => $itinerariesArray ,
                'cityMaster' => $cityMaster ,
                ];
        }
        return $finalarray;
    }
    
    
    public function getTransfersArray( $type , array $array )
    {
        $tempArr = [];
        if( strtolower($type) === 'car' )
        {
            if(count($array))
            {
                foreach ($array as $key => $value) {
                    if( $value['transType'] == 'car')
                    {
//                        $tempArr[] = $value;
                        $tempArr[] = ['itemid'=>$value['fixTransSysId'] , 'IsIncluded'=> ($value['isIncluded']) ? true : false ];
                    }
                }
            }
        }
        else if( strtolower($type) === 'otherservices' )
        {
            if(count($array))
            {
                foreach ($array as $key => $value) {
                    $tempArr[] = ['itemid'=>$value['otherSrvSysId'] , 'IsIncluded'=> ($value['isCostInclInTP']) ? true : false ];
                }
            }
        }
        return $tempArr;
    }
    
    
    public function getSelectedItemRate( $itemid , array $array , $type )
    {
//        echo $itemid;
//        echo '<pre>'; print_r( $array ); die;
//        echo '<pre>'; print_r( $array[$itemid] ); die;
        $price = 0;
        
        if(strtolower($type) === 'car' )
        {
            if(array_key_exists($itemid, $array)) {
                $price = $array[$itemid]['costPerson'];
            }
        }
        else {
            if(array_key_exists($itemid, $array)) {
//                $price = $array[$itemid]['priceaditionals']['NetCost'];
                $price = $array[$itemid]['priceaditionals']['netCost'];
            }
        }
        return $price;
    }

    
    public function getMasterData( $packageid , $type )
    {
        $array = $this->getPackageJSONDataArray($packageid);
        $returnResult = [];
        
        if( strtolower( $type ) === 'city' )
        {
            $hotelArr = [];
            foreach ($array['Cities']['City'] as $key => $value) {
                $hotelArr[$value['CityId']] = [ 'CityId' => $value['CityId'] ,  'Title' => $value['Title'] ];
            }
            $returnResult = $hotelArr;
        }
        else if( strtolower( $type ) === 'h' )
        {
            $hotelArr = [];
            foreach ($array['Cities']['City'] as $key => $value) {
                foreach ($value['Hotels']['Hotel'] as $keyINN => $valueINN) {
                    
                    $hotelArr[$valueINN['RefHotelId']] = $valueINN;
                }
            }
            $returnResult = $hotelArr;
        }
        else if( strtolower( $type ) === 'a' )
        {
            $hotelArr = [];
            foreach ($array['Cities']['City'] as $key => $value) {
                if(isset($value['Activities']['Activity'])) {
                    foreach ($value['Activities']['Activity'] as $keyINN => $valueINN) {

                        $hotelArr[$valueINN['RefActivityId']] = $valueINN;
                    }
                }
            }
            $returnResult = $hotelArr;
        }
        else if( strtolower( $type ) === 's' )
        {
//            echo '<pre>'; print_r($array['Cities']['City']);
            $hotelArr = [];
            foreach ($array['Cities']['City'] as $key => $value) {
                if( isset($value['SightSeeings']['SightSeeing']) ){ 
                    foreach ($value['SightSeeings']['SightSeeing'] as $keyINN => $valueINN) {
                        $hotelArr[$valueINN['RefSSId']] = $valueINN;
                    }
                }
            }
            $returnResult = $hotelArr;
        }
        else if( strtolower( $type ) === 'car' )
        {
            $hotelArr = [];
            foreach ($array['Transfers'] as $key => $value) {
                if( $value['transType']=== 'car' ) {
                    $hotelArr[$value['fixTransSysId']] = $value;
                }
            }
            $returnResult = $hotelArr;
        }
        else if( strtolower( $type ) === 'services' )
        {
            $hotelArr = [];
            foreach ($array['OtherServices'] as $key => $value) {
                $hotelArr[$value['otherSrvSysId']] = $value;
            }
            $returnResult = $hotelArr;
        }
        return $returnResult;
    }
    
    
    public function getGroupCountForRate( $itinerary , $pkgid , $tourtype , $catid , $itemid )
    {
        $PSESS  = $this->_storage->packageSession;
        $countITIHotel = 1;
        
        if(count($PSESS[$pkgid][$tourtype][$catid])) {
            foreach ($PSESS[$pkgid][$tourtype][$catid] as $keyO => $valueO) {
              if($keyO === 'itineraries') {
                foreach ($valueO as $keyH => $valueH ) {
                    if( is_array($valueH['hotel'] ) && count( $valueH['hotel'] ) ) {
                        foreach ($valueH['hotel'] as $key => $value) {
                            if( $value['MasterIntSysId'] == $itinerary ) {
                               if($value['itemid'] == $itemid){
                                    $countITIHotel++;
                               }
                            }
                        }
                    }
                }
              }
            }
        }
        return $countITIHotel;
    }


    public function getMarkupDetailsArray( $packageid , $tourtype , $catid , $market = 'B2C'  )
    {
        $array = $this->getPackageJSONDataArray($packageid);
//               echo "<pre>";print_r($array['TourTypes']['MarketType']);exit;
        $priceArray = $this->getCategoryAndPriceArray( $array['TourTypes']['MarketType'] ,  $market );
//                       echo "<pre>";print_r($priceArray);exit;
        $returnResult = [];
        $tourTypeChar = ($tourtype ==1 ) ? 'P' : 'G';
        $returnResult = [
            'MarkType' => $priceArray['priceArrJson'][$tourTypeChar][Catabatic_Helper::getPackageType($catid)]['price'][0]['MarkType'],
            'MarkValue' => $priceArray['priceArrJson'][$tourTypeChar][Catabatic_Helper::getPackageType($catid)]['price'][0]['MarkValue']
        ];
//        echo $priceArray['priceArrJson'][$tourTypeChar][Catabatic_Helper::getPackageType($catid)]['price'][0]['MarkType'];
        return $returnResult;
    }
    
    
    // $countit = number of nights / itinerary
    public function calculateMarkupOnPrice ( array $markupDetialsArray , $adjustmentPrice , $countit )
    {
        if(array_key_exists('MarkType', $markupDetialsArray)) {
            if( $markupDetialsArray['MarkType'] == 2) {
                $markupvalue = ($adjustmentPrice * $markupDetialsArray['MarkValue'])/100 ;
            }
            $adjustmentPrice += $markupvalue;
            $adjustmentPrice *= $countit;
        }
        return round($adjustmentPrice);
    }

    
    public function getPackagePriceArray( $packageid )
    {
        $array = $this->getPackageJSONDataArray($packageid);
//               echo "<pre>";print_r($array['TourTypes']['TourType']);exit;
        $returnResult = [];
        $tourtype = $category = [];
        return $returnResult;
    }
    
    
     
    
    public function getPackageJSONData( $packageid )
    {
        $model  = new Admin_Model_CRUD();
        $result = $model->rv_select_row( 'tb_tbb2c_packages_master' , ['LongJsonInfo'],
            [ 'IsMarkForDel'=>0 , 'IsActive'=>1, 'IsPublish'=>1 , 'PkgSysId' => $packageid ], ['PkgSysId'=>'DESC'] );
        echo '<pre>'; print_r( $result ); die;
        return $result;
    }
    
    

    public function getPackageJSONDataArray( $packageid )
    {
        $model  = new Admin_Model_CRUD();
        $result = $model->rv_select_row( 'tb_tbb2c_packages_master' , ['LongJsonInfo'],
            [ 'IsMarkForDel'=>0 , 'IsActive'=>1, 'IsPublish'=>1 , 'PkgSysId' => $packageid ], ['PkgSysId'=>'DESC'] );

        $return = [];
        if($result['LongJsonInfo']) {
            $return = Zend_Json::decode($result['LongJsonInfo']);
        }
//        echo '<pre>'; print_r( $return ); die;
        return (array_key_exists('package', $return)) ? $return['package'] : $return;
    }

        
    
    public function writeLogTrack($data) {
        $fileName = date("Y-m-d") . ".txt";
        $fp = fopen("data/track/" . $fileName, 'a+');
        $data = date("Y-m-d H:i:s") . " - " .$data;
        fwrite($fp, $data);
        fclose($fp);
    }
    
    
    public function writeLogs( $data , $type , $filename ) {

        if($type == 'webservice_package') {
            if(!file_exists("data/webservices/package")) {
                mkdir("data/webservices/package", 0777);
            }
            $directory = "data/webservices/package/";
        }
        else if($type == 'webservice_activity') {
            if(!file_exists("data/webservices/activity")) {
                mkdir("data/webservices/activity", 0777);
            }
            $directory = "data/webservices/activity/";
        }
        file_put_contents( $directory . $filename , $data );
    }
    
    
    public function sessionObjectToArray( $obj_namespace )
    {
        return Zend_Session::namespaceGet( $obj_namespace );
    }
    

    /*
    * This function is used to check if the session is created for dynamic package 
    * @param  $packageid , $tourtype , $catid
    * @return session array
    */
    public function checksession( $packageid , $tourtype , $catid )
    {
        if( !is_int($packageid) )
        {
            throw new Exception("function 'hashingg()' expects int only as input parameter.");
        }
        else {
            $hasAlreadySession = $splitID = $code = 0;

//            echo $packageid , $tourtype , $catid;

//            if( isset($this->_storage->packageSession) && (array_key_exists($packageid, $this->_storage->packageSession)) )
//            {
//                if( isset($this->_storage->packageSession[$packageid]['tourtype']) && ($this->_storage->packageSession[$packageid]['tourtype'] == $tourtype ) )
//                {
//                    if( isset($this->_storage->packageSession[$packageid][$tourtype]['category']) && ($this->_storage->packageSession[$packageid][$tourtype]['category'] == $catid ) )
//                    {
//                        $hasAlreadySession = true;
//                    }
//                }
//            }
            if( isset($this->_storage->packageSession) && (array_key_exists($packageid, $this->_storage->packageSession)) )
            {
                if( isset($this->_storage->packageSession[$packageid]) && array_key_exists( $tourtype , $this->_storage->packageSession[$packageid]) )
                {
                    if( isset($this->_storage->packageSession[$packageid][$tourtype]) && array_key_exists( $catid , $this->_storage->packageSession[$packageid][$tourtype] ) )
                    {
                        $hasAlreadySession = true;
                    }
                }
            }
            
        }
//        var_dump($hasAlreadySession); die;
        return $hasAlreadySession;
    }

    
    /*
    * This function is used to copy the session if dynamic package 
    * @param $pkgid , $tourtypeAAA , $catid , array $itinerariesArray , $price=0 , array $others
    * @return void
    */
    public function copysession( $pkgid , $tourtypeAAA , $catid , array $itinerariesArray , $price=0 , array $others )
    {
        if( !$pkgid ) {
            throw new Exception("Package id missing");
        }
        else
        {
//            $this->flushSession(); // remove all previous session
//            $PSESS = [];
//                echo '<pre>'; print_r($others); die;

            $tourtype = ($tourtypeAAA ===2) ? 2 : 1;
//            $PSESS['tourtype'] = $tourtype;
//            $PSESS['category'] = $catid;

            $PSESS['itineraries']           = $itinerariesArray;
            $PSESS['others']['price']       = $price ;
            $PSESS['others']['services']    = $others['services'];
            $PSESS['others']['transfers']   = $others['transfers'];

            $this->_storage->packageSession[$pkgid][$tourtype][$catid] = $PSESS;

//            $this->_storage->setExpirationSeconds( 10, $this->_storage->packageSession ); // set expiration time in seconds
        }
    }
    
    
    
    /*
    * This function is used to remove the session if dynamic package
    * @param null
    * @return void
    */
    public function flushSession()
    {
        $this->_storage->unsetAll();
    }
    
    
    
    
    
    // prepare the json for front end as per need for listing page
    // params : B2B | B2C (default)
    public function customiseForJsonV2 ( array $resultset , $market = 'B2C' )
    {
        
        $result = $myCategoryArray = $hotelStandardArr = $tourTypeArray = [];
        $shortJSON = $longJSON = $PackageCategoryStr = $PackageDestinationStr = '';
        
$temp=array();
        $displayFinalPrice = $displayFinalPriceDisc = '';

        $defaultCategoryId  = 0;
        $defaultTourType    = 0;
                    
        $jsonData = $PackageNights = $PackagePriceRange = [];
        foreach ($resultset as $resultkey => $resultval)
        {
            try {
                 $jsonData[$resultkey]   = Zend_Json::decode($resultval['LongJsonInfo'], true);
            } catch( Zend_Exception $e ){
                 $jsonData[$resultkey] = "error";
            }
        }
        
        $PackageType = $PackageSubType = $tourTypeRadio = $categoryDetails  = $destinationTitleCustomFinalStr = $MPType = '';
        $priceArrJson   = $tourTypeArrayOfIds = $destinationNightCountsArray = $destinationTitleCustomArray  = [];
        
        foreach ($resultset as $resultkey => $resultval)
        {
            // get package type array from json string
//            $shortJSON  = Zend_Json::decode($resultval['ShortJsonInfo'], true);

            $PackageType    = $resultval['PackageType'];
            $PackageSubType = $resultval['PackageSubType'];

            $longJSON = $jsonData[$resultkey];
            
          if( $longJSON != "error" ) {
            
            $temp['package']        = $longJSON['package']; // get package type array
            $temp['packageTypeArr'] = $longJSON['package']['PackageType']; // get package type array
            $temp['cityArr']            = $longJSON['package']['Cities']['City'];   // get cities included in package
            $temp['inclusionsArr']      = $longJSON['package']['Inclusions']; // get inclusions of package

            $temp['tourType']           = $longJSON['package']['TourTypes']['MarketType']; // get package validity
            $temp['Validity']           = $longJSON['package']['Validity']; // get package validity
            $temp['itineraryArr']       = $longJSON['package']['Itineraries']['Itinerary']; // get Itineraries 

            $defaultCategoryId = 0;
            $defaultCategory = '';
            $hotelStandardArr   = [];

            
            // used for getting | category | meal plan change options
            $priceJsonViewFile  = $this->getCategoryAndPriceArrayJSON( $temp['tourType'] , $market , $PackageType , $PackageSubType );
            $MPTypeArray = $priceJsonViewFile['priceArrJson'];

        
            // start : code for getting price in all category and tour type
            //
            
            $category = $TPId = '';
            
            /* get default category 
             * hotel standard array
             * Tour type defualt
             */
            $categoryDetails = $this->getCategoryAndPriceArray ( $temp['tourType'] , $market , $PackageType , $PackageSubType );

            
            // if dynamic package then all type has same tp id
            $TPId = ($resultval['PackageType'] == $this->packageTypeStatic) ? $longJSON['package']['TPId'] : $categoryDetails['TPId']; // get tpid all same for dynamic package
            
            $defaultCategoryId  = $categoryDetails['defaultCategoryId'];
            $defaultCategory    = $categoryDetails['defaultCategory'];
            $defaultTourType    = $categoryDetails['defaultTourType'];

            $hotelStandardArr   = $categoryDetails['hotelStandardArr'];
            $tourTypeArrayOfIds = $categoryDetails['tourTypeArrayOfIds'];
            $priceArrJson       = $categoryDetails['priceArrJson'];
            
            $MPType = (!empty($categoryDetails['MPType']) && ($categoryDetails['MPType']!='LowestCost') && ($PackageType == $this->packageTypeStatic)) ? array_search($categoryDetails['MPType'], unserialize(CONST_MEAL_PLAN_ARR)) : 0;

            // end : code for getting price in all category and tour type

            $this->hotelTypeArr = $hotelStandardArr; // get hotel standard value dynamic
            
            $tourType = $this->tourTypeArr; // static value private and group

            
            // get market type here B2B/B2C
            $marketType = [];
            $tourTypeRadio  = [];
            
            $getTourTypeResult = $this->getTourTypeV2( $tourType , $tourTypeArrayOfIds,  'RADIO_BUTTON' ); // for radio button
            $tourTypeRadio     = $getTourTypeResult['tourTypeRadio'];
            
            // end : tour type + hotel type price calculations

            //  start : prepare the array for itineray
            
            $itineraryArr       = $temp['itineraryArr'];
            $count_Itinerary    = count($itineraryArr) ;
            
            $itineraryArrCustom = $hotelTypeArr = $hotelsArr = $ActivityItineraryArr = $ActivityITINERARY_ITEM_Result = [];

            $cityITINERARY = $cityITINERARYTitle = $hotelsInCity = $destinationTitleCustomStr = '';
            $SightSeeingITINERARY = $SightSeeingITINERARY_ITEM = $SightSeeingITINERARY_Result = $SSItiArray = '';
                
            $destinationTitleCustomArray1 = [];
            for ( $i=0; $i < $count_Itinerary; $i++ ) {
                
                $cityITINERARY = $this->filterArrayByValueKeyPair( ['Type', 'CITY' ], $itineraryArr[$i]['ItineraryItem'] );
                $cityITINERARYTitle = $this->filterArrayByValueKeyPair( ['CityId', $cityITINERARY[0]['Id'] ], $temp['cityArr'] );

                $HotelITINERARY     = $this->filterArrayByValueKeyPair( ['Type', 'HOTEL' ], $itineraryArr[$i]['ItineraryItem'] );
                
                // filter the days Itinerary on the basis of hotel || hotel must be in last day of repeated day
                if(($HotelITINERARY[0]['Type']) == 'HOTEL' ) {

                    $HotelITINERARY_ITEM = $hotelsInCity = $hotelsArrInner = $hotelPriceResultArr = $HotelITINERARY_ITEM_Result = [];
                    $hotelDetailsResult = $hotelID = $hotelName = $hotelStar = $hotelTARating = $hotelPrice = $hotelPriceAdditional = '';

                    $hasHotelInItineray = false;
                    foreach ( $this->hotelTypeArr as $hotelTypeKey => $hotelTypeValue ) {

                        // fetch hotel ids only
                        $HotelITINERARY_ITEM = $this->filterArrayByValueKeyPair( ['Type', ($hotelTypeValue) ], $HotelITINERARY[0]['Items'] );

                        // fetch which hotel is included true / false
                        if( isset($HotelITINERARY_ITEM[0]['Item']) && count($HotelITINERARY_ITEM[0]['Item']) ) {
                          $HotelITINERARY_ITEM_Result = $this->filterArrayByValueKeyPair( ['IsIncluded', true ], $HotelITINERARY_ITEM[0]['Item'] );
                        }
                        else {
                          $HotelITINERARY_ITEM_Result = [];
                        }
                        
                        $hotelID = ( $HotelITINERARY_ITEM_Result ) ?  $HotelITINERARY_ITEM_Result[0]['Id']: 0;

                        $hotelsInCity = (isset($cityITINERARYTitle[0]['Hotels']['Hotel'])) ? $cityITINERARYTitle[0]['Hotels']['Hotel'] : [];

                        if(count($hotelsInCity)) {

                            $hotelDetailsResult = $this->filterArrayByValueKeyPair( ['RefHotelId', $hotelID ], $hotelsInCity ); // fetch hotel details

                            $hotelName      = (isset($hotelDetailsResult[0]['Name'])) ? $hotelDetailsResult[0]['Name'] : '-';
                            $hotelStar      = (isset($hotelDetailsResult[0]['Star'])) ? $hotelDetailsResult[0]['Star'] : 0;
                            $hotelTARating  = (isset($hotelDetailsResult[0]['Rating'])) ? floor($hotelDetailsResult[0]['Rating']) : 0;

                        }

                        $tourTypeChar = ($defaultTourType ==1) ? 'P' : 'G'; // if private than P else G for Group tour type
                        $hotelPriceResultArr    = $this->filterArrayByValueKeyPair( ['Type', ($hotelTypeValue) ], $priceArrJson[$tourTypeChar] );
                        $hotelPrice = (isset($hotelPriceResultArr[0]) && $hotelPriceResultArr[0]['price']) ? $hotelPriceResultArr[0]['price'] : [];
                        
                        // start : calculate the price array
                        // 
                        $hotelPrice = isset($hotelPrice) ? $hotelPrice : []; // get the first node value
//                        echo '<pre>'; print_r( $priceArrJson['P'] ); die;

                        $hotelPriceFiltered = [];
                        $tmpFrom = $tmpTo = '';
                        
                        foreach ($hotelPrice as $hpkey => $hpvalue) {
                            $tmpFrom    = substr( $hpvalue['To'], 0 , 10);
                            $tmpTo      = substr( $hpvalue['From'], 0 , 10);
                            if( ($this->currentDateTime >= $tmpFrom) && ($this->currentDateTime <= $tmpTo) ) {
                                $hotelPriceFiltered[] = $hpvalue;
                            }
                        }
                        // 
                        // end : calculate the price array
                        
                        
                        $TPSysId    = (isset($hotelPriceResultArr[0]['TPSysId'])) ? $hotelPriceResultArr[0]['TPSysId'] : 0;
                        
                        
                        if( $hotelID ) {
                            $hotelsArrInner["$hotelTypeValue"] = 
                            [
                                'TPSysId' => $TPSysId,
                                'hotelID' => $hotelID,
                                'hotelName' => $hotelName,
                                'hotelStar' => $hotelStar,
                                'hotelTARating' => $hotelTARating,
//                                'PriceAdditional' => $hotelPrice,
                            ];
                            $hasHotelInItineray = true;
                        }
                        else {
                            $hotelsArrInner["$hotelTypeValue"] = 
                            [
                                'hotelID' => $hotelID,
                            ];
                        }
                        
                        $hotelTypeArr[] = [ 'type' => $hotelTypeValue ];
                        
                        if($hotelID) {
                            $itineraryArrCustom[$hotelTypeValue][] = [
                                'hid' => $hotelID,
                                'hotel' => $hotelName,
                                'star' => (int)$hotelStar,
                                'city' => $cityITINERARYTitle[0]['Title'],
                                'day' => $this->getValueByKeyFromArray('Day' , $itineraryArr[$i] ),
                            ];
                        }
                        
                    }
                    
//                    $hotelTypeArr = array_keys($hotelsArrInner);
//                    print_r($a);
//                    echo '<pre>'; print_r( $hotelsArrInner ); die('sdfsd');


                    
                    
//                    echo '<pre>'; print_r($hotelsArrInner);
                    
                    if($hasHotelInItineray) {
                        // skip for last day ( because its departure day )
                        if($i < $count_Itinerary-1) {
                            $destinationTitleCustomArray1[] = $cityITINERARYTitle[0]['Title'];
                        }
                    }

                }
                

            } // outer for loop ends here


            $destinationTitleCustomArray = $destinationTitleCustomArray1;
            $r = 0;
            $destinationTitleCustom = '';
            foreach (array_count_values($destinationTitleCustomArray) as $key => $value) {
                $icon = ($r) ? "|" : "";
                $destinationTitleCustom .= " $icon $key ({$value}N)";
                $r++;
            }
            $destinationTitleCustomFinalStr = $destinationTitleCustom;

//            echo '<pre>'; print_r($itineraryArrCustom); die;
            
            $itineraryArrCustomNew = $itineraryArrCustom;
            
//            echo '<pre>'; print_r(array_count_values($itineraryArrCustomNew)); die('dd');
            
            $itineraryNewArray = [];

                $tempArray = [];
                foreach ($itineraryArrCustomNew as $k => $val) {
                    $daymerge = '';
                  foreach ($val as $key => $value) {
                    
                    if( !in_array( $k.$value['hid'] , $tempArray )) {
                        $exists = true;
                        $hid = $value['hid'];
                        $hotel = $value['hotel'];
                        $star = $value['star'];
                        $city = $value['city'];
                        $day = $value['day'];
                    } else {
                        $exists = false;
                    }
                    
                    $daymerge .= $value['day'];
                    
                    if( $exists ) {
                        $itineraryNewArray[$k][] = [
                            'hid' => $hid,
                            'hotel' => $hotel,
                            'star' => $star,
                            'city' => $city,
                            'day' => $value['day'],
                        ];
                    }
                    
                    $tempArray[] = ($value['hid']) ? $k.$value['hid'] : ''; // insert the standard and hotel id too ( hotel id is duplicate in multiple category )
                  }
                }
            
            //  end : prepare the array for itineray
// echo '<pre>';            print_r($itineraryNewArray); die;
            

            // filter the itinerary array to display
            $itineraryArrCustom = $this->filterDuplicateItinerary( 'dayNumber' , $itineraryArrCustom);
            
            if(count($itineraryArrCustom) > 2)
            array_pop($itineraryArrCustom); // remove the last day | because last day should not show in table of hotels grid

            
            // Start : price calculation baseed on Hotel Type 
//            $PriceResultArr = $this->filterArrayByValueKeyPair( ['type', 'standard' ], $temp['hotelTypeArr'] );
//            $this->filterArrayByValueKeyPair( ['Type', ($hotelTypeValue) ], $temp['hotelTypeArr'] );

            $myPriceArray[$resultval['PkgSysId']] = $priceArrJson;

//            echo '<pre>';            print_r($myPriceArray); die;
            
//            print_r($hotelsArrInner);
//            var_dump($defaultStandard);
//            echo $displayFinalPrice , $displayFinalPriceDisc ; die;
            
//            echo $defaultTourType;
            $tourTypeChar = ($defaultTourType ==1) ? 'P' : 'G'; // if private than P else G for Group tour type
            $displayFinalPrice      = $this->getPackagePriceV2( $defaultCategory , $tourTypeChar , $priceArrJson ,  true ); // Param 4: true ( if calculate discounted price )
            $displayFinalPriceDisc  = $this->getPackagePriceV2( $defaultCategory , $tourTypeChar , $priceArrJson  ); // get with discount included
//echo $displayFinalPrice; die;
//            price to show - for private and group tour type 
            // End : price calculation baseed on Hotel Type 

            
//            start : generate the category json here
//            
            if($resultval['PackageCategory'] != ""){
                $PackageCategoryStr .= ",".$resultval['PackageCategory'];
            }
//            
//            end : generate the category json here
            
//            start : generate the destinations json here
//            
            $PackageDestinationStr .= ",".$resultval['Destinations'];
//            
//            end : generate the destinations json here
            
//            start : generate for the destinations json here
//            
             $PackageNights[] = $resultval['Nights'];
//            
//            end : generate for the destinations json here
            

//            start : generate price range filter for the package json here
//            
             $PackagePriceRange[] = $pricerangetemp = $this->getRangeByValue( unserialize(CONST_PRICE_RANGE_5000) , $resultval['MinPrice']);
             //echo $resultval['MinPrice'];
             //print_r(unserialize(CONST_PRICE_RANGE_5000));die;
//            end : generate price range filter for the package json here
                if(trim($temp['inclusionsArr'], ',')){
                    $inclusionsArr = explode(',', trim($temp['inclusionsArr'], ',')); 
                } else {
                    $inclusionsArr = [];
                }
                
                $image = explode(',', $resultval['Image']);
                $ImgThumbnail1 = $image[0];
                
                if($ImgThumbnail1) {
                    $thumb_icon = $this->baseUrl . "public/upload/tours/". $resultval['PkgSysId'] .'/images/medium/'. $ImgThumbnail1;
                }
                else {
                    $thumb_icon = '';
                }
             
            $result[] = [
                'PkgSysId' => $resultval['PkgSysId'],
                'isFixedDeparturesPackage' => (int)trim($temp['package']['IsFixedDeparturePackage']),
                'Countries' => $resultval['Countries'],
                'TPId' => $TPId,
                'GTXPkgId' => $resultval['GTXPkgId'],

                'PackageType' => $resultval['PackageType'],
//                'StarRating' => $resultval['StarRating'],
                'Destinations' => $resultval['Destinations'], // variable used in json
                'des' => trim($destinationTitleCustomFinalStr), // variable used in json
                'desSaArr' => array_count_values($destinationTitleCustomArray), // variable used in json
//                'BookingValidUntil' => $this->changeDateFormat($resultval['BookingValidUntil'], '/'),

                'PriceRange' => $pricerangetemp ,
                'Duration' => $resultval['Nights'],
                'PackageTypeArr' => explode(',', trim($temp['packageTypeArr'], ',')) , // custom field
                'hotelTypeArr' => $this->array_filter_rv($hotelTypeArr), // custom field
//                'hotelTypeArr' => ($hotelTypeArr), // custom field

                'inclusionsArr' => $inclusionsArr, // custom field
                'package' => ['Name' => trim(str_replace('/', ' ', $temp['package']['Name'])), 'Tagline1' => (isset($temp['package']['Tagline1']) ? trim($temp['package']['Tagline1']) : "") ], // custom field
                'ImgThumbnail' => trim($temp['package']['ImgThumbnail']) ,
                'ImgThumbnail1' => $thumb_icon ,
                'defaultCategoryId' => $defaultCategoryId , // custom field

//                'itineraryArr' => $itineraryArrCustom, // custom field
                'itineraryArr1' => $itineraryNewArray, // custom field
                'Price' => $displayFinalPrice , // custom field
                'PriceDis' => $displayFinalPriceDisc, // custom field price includes discount too
                'defaultStandard' => $defaultCategory , //$hotelTypeArr[0]['type'] ,

                'defaultTourType' => $defaultTourType , //$hotelTypeArr[0]['type'] ,
                'tourType' => $tourTypeRadio ,
                'deal' => $resultval['HotDeal'] ,
//                'mp' => $resultval['PackageSubType'] , // $resultval['PackageSubType'] // 0 : lowest cost | 1 : Meal plan
                'mp' => $MPType , //  // 0 : lowest cost | 6 , 7 , 9 : Meal plan
                'mpArr' => $MPTypeArray , //  // 0 : lowest cost | 6 , 7 , 9 : Meal plan
                ];
            }
            

        }
        
        
        if(count($myPriceArray))
        $this->createJsonFile( $myPriceArray , 'package_price.json' ); // create json file ui manipulation

        // package category for filter     
        $PackageCategory = array_unique(explode(',', str_replace('"', '', trim($PackageCategoryStr , ','))));
        asort($PackageCategory);
        $PackageCategory1 = array_values($PackageCategory); // reset the array key

        // package destination for filter
        $PackageDestination = array_unique(explode(',', str_replace('"', '', trim($PackageDestinationStr , ','))));
        asort($PackageDestination);
        $PackageDestination1 = array_values($PackageDestination); // reset the array key
        
        // package number of nights for filter
        $PackageNightsU = (array_unique($PackageNights));
        asort($PackageNightsU);
        $PackageNights1 = array_values($PackageNightsU);
        
        // package price range for filter
        $PackagePriceRangeU = (array_unique($PackagePriceRange));
        ksort($PackagePriceRangeU); // sort array by key
        $PackagePriceRange1 = array_values($PackagePriceRangeU);
//        Zend_Debug::dump($PackagePriceRange1);

        
        // params : data array , file name , path where to create file
        if(count($result))
//        $this->createJsonFile( [ 'rows' => $result , 'filterCat'=> $PackageCategory1 , 'filterDest'=> $PackageDestination1, 'filterNight'=> $PackageNights1 , 'filterPrice'=> $PackagePriceRange1 ] ,
//                'tours_package.json' , 'public/data/dynamic/' ); // create package json file for ui
        
        return Zend_Json::encode( [ 'rows' => $result , 'filterCat'=> $PackageCategory1 , 'filterDest'=> $PackageDestination1, 'filterNight'=> $PackageNights1, 'filterPrice'=> $PackagePriceRange1  ] );

    }
       
    
    function getCategoryAndPriceArray ( array $tourType=null , $market=null , $PackageType=null , $PackageSubType=null  ) {
        
        $defaultCategoryId = $priceArrJson = $defaultTourType = '';
                
        foreach ( $tourType as $tourKey => $tourVal ) {

            $priceArrJsonInner1 = [];

            // check the market type is b2b or b2c
            if( $tourVal['MarketTypeTitle'] === $market ) {

                $priceArrJsonInner = $categoryArray = $tourtypeArray = [];
                foreach ($tourVal['TourType'] as $k => $v) {

                    $category   = $v['Categories']['Category'];

                    if(is_array($category) && count($category)) {
                        
                        $tempCategory= [];
                        $MPType = '';
                        
                        foreach ($category as $keyCat => $valueCat) {
//                              echo $valueCat['Type'] = $keyCat.'SSSS'; // this is temporary code need to delete this line of code

                            // if dynamic package
                            if( ($PackageType) && ($PackageType == $this->packageTypeStatic) ) {
                                $MPTypeArray = [];
                                foreach ($valueCat['MPType'] as $valueMP) {
                                    if(isset($valueMP['SeletedForPackage']) && !empty($valueMP['SeletedForPackage'])) {
                                        $MPType = $valueMP['MPTypeText'];
                                    }
                                    if($valueMP['MPTypeText'] !="LowestCost") {
                                        $MPTypeArray[$valueMP['MPTypeId']] = $valueMP['MPTypeText'];
                                    }
                                }
                            }
                            
                            // create hotel type/standard array whether in group or private tour
                            if( $k == 0) {
                                $hotelStandardArr[] = $valueCat['Type'] ; // create hotel type array for display radio button on view
                                $categoryArray[$valueCat['CategoryId']] = ['Type' => $valueCat['CategoryId'] , 'Title' => $valueCat['Type'] ];
                                if( isset($valueMP['MPTypeText']) && ($valueMP['MPTypeText'] !="LowestCost") && ($valueMP['MPTypeText'] !="ITENARYWISE") ) {
                                    $categoryArray[$valueCat['CategoryId']]['mptype'][] = $MPTypeArray;
                                }
                            }

                            $tempCategory[$valueCat['Type']] = [
                                'TPId'=> $valueCat['TPSysId'] ,
                                'SFP'=> $valueCat['SeletedForPackage'],
//                                    'price'=> $valueCat['PriceAdditional'] // this was old pattern
                                'price'=> $valueCat['MPType'][0]['PriceAdditional']
                            ];

//                                echo '<pre>';print_r($valueCat); die;
                            if($keyCat==0){
                                $TPId = $valueCat['TPSysId'];
                                $defaultCategory = $valueCat['Type'];
                                $defaultCategoryId = $valueCat['CategoryId']; // set default category for package on load
                            }
                        }
                    }
                    $tourTypeArrayOfIds[] = $v['TourType']; // get the current loop package tour type id only to create radio button
                    $priceArrJsonInner[$v['TourTypeTitle'][0]] = $tempCategory;
//                    echo '<pre>'; print_r($categoryArray); die;

                    if($k==0){
                        $defaultTourType = $v['TourType'];
                    }
                    
                    $tourtypeArray[$v['TourType']] = ['TourTypeTitle' => $v['TourTypeTitle'] , 'TourType' => $v['TourType']];
                    
                }
//                echo '<pre>'; print_r($priceArrJsonInner);

//                    die('dddddd');
                $priceArrJsonInner1 = $priceArrJsonInner;
            }
//                            echo '<pre>'; print_r($priceArrJsonInner);

//            echo '<pre>'; print_r($categoryArray); die;
        }

//                    echo '<pre>'; print_r(reset($categoryArray[$defaultCategoryId]['mptype'][0])); die;

//            $MPType = reset($categoryArray[$defaultCategoryId]['mptype'][0]);
//            $MPType = key($categoryArray[$defaultCategoryId]['mptype'][0]); // get default mp type id here
        
        $MPType = $this->getMpTypeByCategory(!empty($categoryArray)?$categoryArray:array(), $defaultCategoryId);

            $priceArrJson = !empty($priceArrJsonInner)?$priceArrJsonInner:array(); // assign the inner created to outer loop variable
            
        return [
            "TPId" => isset($TPId)?$TPId:'',
            "priceArrJson" => $priceArrJson,
            "hotelStandardArr" => isset($hotelStandardArr)?$hotelStandardArr:array(),
            "defaultCategory" => isset($defaultCategory)?$defaultCategory:'',
            "defaultCategoryId" => $defaultCategoryId,
            "tourTypeArrayOfIds" => isset($tourTypeArrayOfIds)?$tourTypeArrayOfIds:array(),
            "defaultTourType" => $defaultTourType,
            "category" => isset($categoryArray)?$categoryArray:array(),
            "tourtype" => isset($tourtypeArray)?$tourtypeArray:array(),
            "MPType" => $MPType,
        ];
    }


    /*
     * getPriceArrayFullyDynamic function is used to get array
     */
    function getPriceArrayFullyDynamicJSON ( array $tourType , $market , $PackageType=null , $PackageSubType=null  ) {
        
        $priceArrJson = [];
//                                                                            echo '<pre>'; print_r($tourType); die('here');

        foreach ( $tourType as $tourKey => $tourVal ) {

            // check the market type is b2b or b2c
            if( $tourVal['MarketTypeTitle'] === $market ) {

                $priceArrJsonInner = $categoryArray = $tourtypeArray = [];
                foreach ($tourVal['TourType'] as $k => $v) {

                    $category   = $v['Categories']['Category'];

                    if(is_array($category) && count($category)) {
                        
                        $tempCategory= []; $MPType = '';
                        
                        foreach ( $category as $keyCat => $valueCat ) {
                             
                            $tempCategory[$valueCat['Type']] = [
                                'TPId'=> $valueCat['TPSysId'] ,
                                'SFP'=> $valueCat['SeletedForPackage'],
//                                    'price'=> $valueCat['PriceAdditional'] // this was old pattern
//                                'price'=> $valueCat['MPType'][0]['PriceAdditional']
                            ];


                            // check only if dynamic package
                            // Lowest cost and meal plan type package : Dynamic package case only
                            if( $PackageType == $this->packageTypeStatic ) {

                                if ($valueMP['MPTypeText'] == "LowestCost") {
//                                                            echo '<pre>'; print_r($valueCat['MPType']); die('here');
                                     $tempCategory[$valueCat['Type']]['price'] = $this->getPriceFromMultiDatewise($valueCat['MPType'][0]['PriceAdditional']);
//                                    echo '<pre>'; print_r( $tempCategory[$valueCat['Type']]['price'] ); die;
                                } else {
                                    $MPTypeArray = [];
                                    foreach ($valueCat['MPType'] as $valueMP) {
                                        if(isset($valueMP['SeletedForPackage']) && !empty($valueMP['SeletedForPackage'])) {
                                            $MPType = $valueMP['MPTypeText'];
                                        }
                                        if($valueMP['MPTypeText'] !="LowestCost") {
                                            $MPTypeArray[$valueMP['MPTypeId']] = $valueMP['MPTypeText'];
                                        }

                                        $MPTypeArray[$valueMP['MPTypeId']] = [
                                            'id' => $valueMP['MPTypeId'] ,
                                            'txt' => $valueMP['MPTypeText'] ,
                                            'SFP' => $valueMP['SeletedForPackage'] , // SFP* referse that this is selected for this package
                                            'price' => $this->getPriceFromMultiDatewise($valueMP['PriceAdditional'])
                                        ];
                                    }
    //                                echo '<pre>'; print_r( $valueCat['MPType'] ); die;
                                    $tempCategory[$valueCat['Type']]['mptype'] = $MPTypeArray;
                                }

                            } else {
//                                echo '<pre>'; print_r( $valueCat ); die;
                                if(is_array($valueCat['MPType']) && count($valueCat['MPType'])) {
                                    
                                    // ITENARYWISE : readymade package case only
                                    $tempCategory[$valueCat['Type']]['price'] = $this->getPriceFromMultiDatewise($valueCat['MPType'][0]['PriceAdditional']);
                                } else {
                                    // Hotel only : readymade package case only
                                    $tempCategory[$valueCat['Type']]['price'] = $this->getPriceFromMultiDatewise($valueCat['PriceAdditional']);
                                }
                            }
                        }
                    }
                    $priceArrJsonInner[$v['TourTypeTitle'][0]] = $tempCategory;
                }
                           
                $this->createJsonFile($tempCategory, 'ra.json'); // remove this line
                
            }
        }

        $priceArrJson = $priceArrJsonInner; // assign the inner created to outer loop variable
//    echo '<pre>'; print_r($priceArrJson); die('here');

//        return [ "priceArrJson" => $priceArrJson ];
        return $priceArrJson;
    }


    function getMpTypeByCategory( array $categoryArray=null , $categoryid  ) {
        if(isset($categoryArray[$categoryid]['mptype'][0]) && ($categoryArray[$categoryid]['mptype'][0] != NULL))
            return reset($categoryArray[$categoryid]['mptype'][0]);
        else
            return [];
    }
            
    
    function getCategoryAndPriceArrayJSON ( array $tourType=null , $market , $PackageType=null , $PackageSubType=null  ) {

        $defaultCategoryId = $priceArrJson = $defaultTourType = '';
                
        foreach ( $tourType as $tourKey => $tourVal) {

            $priceArrJsonInner1 = [];

            // check the market type is b2b or b2c
            if( $tourVal['MarketTypeTitle'] === $market ) {

                $priceArrJsonInner = $categoryArray = $tourtypeArray = [];
                foreach ($tourVal['TourType'] as $k => $v) {

                    $category   = $v['Categories']['Category'];

                    if(is_array($category) && count($category)) {
                        
                        $tempCategory= [];
                        $MPType = '';
                        
                        foreach ($category as $keyCat => $valueCat) {
//                              echo $valueCat['Type'] = $keyCat.'SSSS'; // this is temporary code need to delete this line of code

                            // if dynamic package
                            if( ($PackageType) && ($PackageType == $this->packageTypeStatic) ) {
                                $MPTypeArray = [];
                                foreach ($valueCat['MPType'] as $valueMP) {
                                    if(isset($valueMP['SeletedForPackage']) && !empty($valueMP['SeletedForPackage'])) {
                                        $MPType = $valueMP['MPTypeText'];
                                    }
                                    if($valueMP['MPTypeText'] !="LowestCost") {
                                        
                                        $MPTypeArray[$valueMP['MPTypeId']] = [
                                            'id' => $valueMP['MPTypeId'] ,
                                            'txt' => $valueMP['MPTypeText'] ,
                                            'sfp' => $valueMP['SeletedForPackage'] ,
                                            'price' => $valueMP['PriceAdditional'] ,
                                        ];
                                    }
                                }
                            }
                            
//                            echo '<pre>'; print_r($valueCat['MPType']); die;
                            $tempCategory[$valueCat['Type']] = [
                                'TPId'=> $valueCat['TPSysId'] ,
                                'SFP'=> $valueCat['SeletedForPackage'],
//                                    'price'=> $valueCat['PriceAdditional'] // this was old pattern
                                'price'=> $valueCat['MPType'][0]['PriceAdditional'] ,
                            ];

                            // if meal plan type array has value
                            if(isset($MPTypeArray) && count($MPTypeArray)) {
                                $tempCategory[$valueCat['Type']]['mptype'] = $MPTypeArray;
                            }
                            
                        }
                    }
                    $priceArrJsonInner[$v['TourTypeTitle'][0]] = $tempCategory;
                    
                }
                $priceArrJsonInner1 = $priceArrJsonInner;
            }
        }
            $priceArrJson = isset($priceArrJsonInner)?$priceArrJsonInner:array(); // assign the inner created to outer loop variable
            
        return [
            "priceArrJson" => $priceArrJson,
        ];
    }

    

    public function getTourType( array $tourType , array $tourTypeArrayOfIds , $type = 'RADIO_BUTTON' )
    {
//        echo '<pre>'; print_r($tourTypeArrayOfIds); die;

        if( $type === 'RADIO_BUTTON' ) {
            $tempActive = $active ='';
            foreach( $tourType as $ky => $valu ) {

                if( in_array( $valu['TourType'] , $tourTypeArrayOfIds) ){
//                if( in_array( $valu['TourType'] , $tourTypeArrayOfIds) && !($active)){
                    $active = true;
                    $tempActive = 1;
                    $defaultTourType = $valu['TourType'];
                } else {
                    $active = false;
                }

                $tourTypeRadio[] = [ 'TourType'=> $valu['TourType'] , 'TourTypeTitle'=> $valu['TourTypeTitle'] , 'active' => $active ];
            }

            return [
                "defaultTourType"=> 1 , // $defaultTourType
                "tourTypeRadio"=> $tourTypeRadio ,
            ];
        }
    }
    
    public function getTourTypeV2( array $tourType , array $tourTypeArrayOfIds , $type = 'RADIO_BUTTON' )
    {
//        echo '<pre>'; print_r($tourType); die;

        if( $type === 'RADIO_BUTTON' ) {
            $tempActive = $active = $defaultTourType = '';
            foreach( $tourType as $ky => $valu ) {

                if( in_array( $valu['TourType'] , $tourTypeArrayOfIds) ) {
//                if( in_array( $valu['TourType'] , $tourTypeArrayOfIds) && !($active)){
                    $active = true;
                    $tempActive = 1;
                    
                    if($defaultTourType == '') {
                        $defaultTourType = $valu['TourType'];
                    }
                    $tourTypeRadio[] = [ 'TourType'=> $valu['TourType'] , 'TourTypeTitle'=> $valu['TourTypeTitle'] , 'active' => $active ];
                }
            }
//echo $defaultTourType;
//            echo '<pre>'; print_r($tourTypeRadio); die;

            return [
                "defaultTourType"=> $defaultTourType , // $defaultTourType // defalut tour type
                "tourTypeRadio"=> $tourTypeRadio ,
            ];
        }
    }

    
    public function getPackageCardDetails( array $relatedPackages , $market)
    {
        $relatedPackagesArray = [];
        
        if(isset($relatedPackages)) {
            
          foreach ($relatedPackages as $key => $value) {
            
            if( $value['LongJsonInfo'] ){
                $LongJsonInfo = Zend_Json::decode($value['LongJsonInfo']);
            }

            $categoryDetails = $this->getCategoryAndPriceArray( $LongJsonInfo['package']['TourTypes']['MarketType'] , $market , $value['PackageType'] , $value['PackageSubType'] ); // get default category
// echo '<pre>'; print_r($categoryDetails); die;

            $defaultCategoryId  = $categoryDetails['defaultCategoryId'];
            $defaultCategory    = $categoryDetails['defaultCategory'];
            $defaultTourType    = $categoryDetails['defaultTourType'];
            $TPId    = $categoryDetails['TPId'];
            $MPType = (!empty($categoryDetails['MPType']) && ($categoryDetails['MPType']!='LowestCost')) ? array_search($categoryDetails['MPType'], unserialize(CONST_MEAL_PLAN_ARR)) : 0;

            $tourTypeChar = $this->getTourTypeChar($defaultTourType); // if private than P else G for Group tour type
            $priceArrJson = $categoryDetails['priceArrJson'];

            $displayFinalPrice      = $this->getPackagePriceV2( $defaultCategory , $tourTypeChar , $priceArrJson , true );  // Param 4: true ( if calculate discounted price )
            $displayFinalPriceDisc  = $this->getPackagePriceV2( $defaultCategory , $tourTypeChar , $priceArrJson ); // get with discount included
            
            $relatedPackagesArray[] = [
                'name' => $this->trimContent($LongJsonInfo['package']['Name'] , 18) ,
                'nameF' => $LongJsonInfo['package']['Name'], // full name of package name
                'img' =>  ($value['Image']) ? $value['Image'] : $this->getImageFromJson( 'ImgThumbnail' , $LongJsonInfo['package']) ,
                'night' =>  $value['Nights'] ,
                'price' =>  $this->moneyFormatINR(round($displayFinalPrice)) ,
                'priceDisc' =>  $this->moneyFormatINR($displayFinalPriceDisc) ,
                'star' =>  $value['StarRating'] ,
                'Destination' =>  $value['Destinations'] ,
                'PkgSysId' =>  $value['PkgSysId'] ,
                'GTXPkgId' =>  $value['GTXPkgId'] ,
                'tourtype' =>  $defaultTourType ,
                'PackageType' =>  $value['PackageType'] ,
                'TPId' => ( $value['PackageType'] == $this->packageTypeStatic ) ? $value['GTXPkgId'] : $TPId , // if dynamic package then tpid is same for all
                'HotDeal' => $value['HotDeal'] ,
                'defaultCategoryId' => $defaultCategoryId ,
                'defaultCategory' => $defaultCategory ,
                'mp' =>  $MPType ,
            ];
          }
        }
        return $relatedPackagesArray;
    }

    
    public function getImageFromJson( $keyname ,  array $json ) {
        
        if(array_key_exists( $keyname, $json) ) {
            return $json[$keyname];
        }
        return null;
    }
            
// incomplete function     
    public function deleteJsonFile( $filename , $path ) {
        
        if ( empty( $filename ) ) {
            throw new Exception("Unable to delete file, please give file name to function : " . __FUNCTION__ );
            return;
        } else {
            file_put_contents($folder . $filename , Zend_Json::encode( $dataArray ));
        }
    }
            
    
    public function createJsonFile( array $dataArray , $filename , $path=false ) {
        
        $folder = ( $path ) ? $path : "public/data/";
        
        if ( !is_array( $dataArray )) {
            throw new Exception("Unable to create array, please give price array as [ parameter 1 ] to " . __FUNCTION__ );
            return;
        } else {
            file_put_contents($folder . $filename , Zend_Json::encode( $dataArray ));
        }
    }

    
    public function createPriceJson( array $myPriceArray ) {
        if ( !is_array( $myPriceArray )) {
            throw new Exception("Unable to create array, please give price array as [ parameter 1 ] to " . __FUNCTION__ );
            return;
        } else {
            file_put_contents('public/data/package_price.json', Zend_Json::encode( $myPriceArray ));
        }
    }

    

    public function getPackagePriceV2( $defaultCategory , $tourTypeChar , array $priceArray , $discounted = false )
    {
//        echo $defaultCategory , $tourTypeChar; die;
//echo '<pre>'; print_r($priceArray); die();
        $PriceResultArr = $priceArray[$tourTypeChar][$defaultCategory]['price'][0];
        
        // discounted true means lesser amount | discount excluded in amount 
        if($discounted) {
            $finalp = (float)$PriceResultArr['PricePerPerson'];
        }
        else {
            $discountValue = 0;

            if( $PriceResultArr['DiscountType']=== 1 ) {
                $discountValue = (float)$PriceResultArr['DiscountVal'];
            }
            else if( $PriceResultArr['DiscountType']=== 2 ){
                $discountValue = (float)$PriceResultArr['DiscountVal'];
            }
            else {
                $discountValue = 0;
            }

            $finalp = (float)$PriceResultArr['PricePerPerson'] + $discountValue;
        }
        
        return $finalp;
    }

    
    public function getPackagePriceV3( $defaultCategory , $tourTypeChar , array $priceArray=null , $mptype , $discounted = false )
    {
        
        if( $mptype ) {
            $PriceResultArr = $priceArray[$tourTypeChar][$defaultCategory]['mptype'][$mptype]['price'][0];
        } else {
            $PriceResultArr = $priceArray[$tourTypeChar][$defaultCategory]['price'][0];
        }
        
        // discounted true means lesser amount | discount excluded in amount 
        if($discounted) {
            $finalp = (float)$PriceResultArr['PricePerPerson'];
        }
        else {
            $discountValue = 0;

            if( $PriceResultArr['DiscountType']=== 1 ) {
                $discountValue = (float)$PriceResultArr['DiscountVal'];
            }
            else if( $PriceResultArr['DiscountType']=== 2 ){
                $discountValue = (float)$PriceResultArr['DiscountVal'];
            }
            else {
                $discountValue = 0;
            }

            $finalp = (float)$PriceResultArr['PricePerPerson'] + $discountValue;
        }
        
        return $finalp;
    }

    
    
    // prepare the json for front end as per need for listing page
    public function customiseForJsonSendquery( array $resultset )
    {
        error_reporting(0);
//       echo '<pre>'; print_r($resultset); die;
        
        $result = $myCategoryArray = $hotelStandardArr = [];
        $temp   = $shortJSON = $longJSON = '';
        
        $resultval = $resultset;
            
            // get package type array from json string
//            $shortJSON  = Zend_Json::decode($resultval['ShortJsonInfo'], true);
            $longJSON   = Zend_Json::decode($resultval['LongJsonInfo'], true);
//                                die('herere');

            $temp['package']        = $longJSON['package']; // get package type array
            $temp['packageTypeArr'] = $longJSON['package']['PackageType']; // get package type array

            $temp['hotelTypeArr']       = $longJSON['package']['TourTypes']['TourType'][0]['Categories']['Category'];   // get package Category
            $temp['cityArr']            = $longJSON['package']['Cities']['City'];   // get cities included in package
            $temp['inclusionsArr']      = $longJSON['package']['Inclusions']; // get inclusions of package

            $temp['tourType']           = $longJSON['package']['TourTypes']['TourType']; // get package validity
//            $temp['Validity']           = $longJSON['package']['Validity']; // get package validity
            $temp['itineraryArr']       = $longJSON['package']['Itineraries']['Itinerary']; // get Itineraries 


            $package_hotelcategoryid= $resultval['package_hotelcategoryid'];
            
            if( $package_hotelcategoryid ) {
                $package_hotelcategory  = Catabatic_Helper::getPackageType($package_hotelcategoryid);

                foreach ( $temp['hotelTypeArr'] as $keyHS => $valueHS ) {

                    if($valueHS['Type'] === $package_hotelcategory) {
                        $hotelStandardArr[$package_hotelcategoryid] = $valueHS['Type'];
                    }
                }
            }
            else {
                foreach ( $temp['hotelTypeArr'] as $keyHS => $valueHS ) {
                        $hotelStandardArr[$valueHS['CategoryId']] = $valueHS['Type'];
                }
            }
            
//            echo '<pre>'; print_r($hotelStandardArr); die;

            $this->hotelTypeArr = $hotelStandardArr; // get hotel standard value dynamic
//            $resultval['package_hotelcategoryid']
//            echo count($hotelStandardArr);
            //  start : prepare the array for itineray
            
            $itineraryArr       = $temp['itineraryArr'];
            $count_Itinerary    = count($itineraryArr) ;
            
            $itineraryArrCustom = $hotelTypeArr = $hotelsArr = $ActivityItineraryArr = $ActivityITINERARY_ITEM_Result = [];
            $SightSeeingItineraryArr = $SightSeeingITINERARY_ITEM_Result = [];

            $cityITINERARY = $cityITINERARYTitle = $hotelsInCity = '';
            $ActivityITINERARY = $ActivityITINERARY_ITEM = $ActivityITINERARY_Result = $ActItiArray = '';
            $SightSeeingITINERARY = $SightSeeingITINERARY_ITEM = $SightSeeingITINERARY_Result = $SSItiArray = '';
            
            for ($i=0; $i < $count_Itinerary; $i++ ) {

                $cityITINERARY = $this->filterArrayByValueKeyPair( ['Type', 'CITY' ], $itineraryArr[$i]['ItineraryItem'] );

                $cityITINERARYTitle = $this->filterArrayByValueKeyPair( ['CityId', $cityITINERARY[0]['Id'] ], $temp['cityArr'] );

                $HotelITINERARY     = $this->filterArrayByValueKeyPair( ['Type', 'HOTEL' ], $itineraryArr[$i]['ItineraryItem'] );
                
                $ActivityITINERARY  = $this->filterArrayByValueKeyPair( ['Type', 'ACTIVITY' ], $itineraryArr[$i]['ItineraryItem'] );
                
                $SightSeeingITINERARY   = $this->filterArrayByValueKeyPair( ['Type', 'SIGHTSEEING' ], $itineraryArr[$i]['ItineraryItem'] );

                // filter the days Itinerary on the basis of hotel || hotel must be in last day of repeated day
                if(($HotelITINERARY[0]['Type']) == 'HOTEL' ) {
                

                    $HotelITINERARY_ITEM = $hotelsInCity = $hotelsArrInner = $hotelPriceResultArr = $HotelITINERARY_ITEM_Result = [];
                    $hotelDetailsResult = $hotelID = $hotelName = $hotelStar = $hotelTARating = $hotelPrice = $hotelPriceAdditional = $hotelIncluded = '';
                    
                    foreach ($this->hotelTypeArr as $hotelTypeKey => $hotelTypeValue) {

                        // fetch hotel ids only
                        $HotelITINERARY_ITEM = $this->filterArrayByValueKeyPair( ['Type', ($hotelTypeValue) ], $HotelITINERARY[0]['Items'] );

                        // fetch which hotel is included true / false
                        if( count($HotelITINERARY_ITEM[0]['Item']) ) {
                          $HotelITINERARY_ITEM_Result = $this->filterArrayByValueKeyPair( ['IsIncluded', true ], $HotelITINERARY_ITEM[0]['Item'] );
                        }

                        
//                        echo $hotelID    = ( $HotelITINERARY_ITEM_Result ) ?  $HotelITINERARY_ITEM_Result[0]['Id']: 0;
//                        echo '<pre>';
//                        print_r($HotelITINERARY_ITEM_Result[0]);
//                        die;
                        
                        $hotelIncluded  = ( $hotelID ) ?  $HotelITINERARY_ITEM_Result[0]['IsIncluded']: false;

                        if(count($hotelsInCity)) {
                          $hotelDetailsResult = $this->filterArrayByValueKeyPair( ['RefHotelId', $hotelID ], $hotelsInCity ); // fetch hotel details
                        }

//                        echo '<pre>'; print_r( $hotelDetailsResult ); die;

                        $hotelName      = ($hotelDetailsResult[0]['Name']) ? $hotelDetailsResult[0]['Name'] : '-';

                        
                        $hotelsArrInner[$hotelTypeKey] = 
                            [
//                                'TPSysId' => $TPSysId,
                                'hotelID' => $hotelID,
                                'hotelName' => $hotelName,
                                'hotelIncluded' => $hotelIncluded,
                            ];
                    }
//                                                echo '<pre>'; print_r( $hotelsArrInner ); die;
                    $ItineraryId = $this->getValueByKeyFromArray('ItineraryId' , $itineraryArr[$i] );
                    
                    $itineraryArrCustom[$ItineraryId] = [
                        'hotel' => $hotelsArrInner , 
                    ];
                }


                // get acitivities of the days Itinerary
                if($ActivityITINERARY[0]['Type'] === 'ACTIVITY' ) {


                    // fetch which hotel is included true / false
                    if( count($ActivityITINERARY[0]['Items']) ) {

                        $ActItiArray = $ActivityITINERARY[0]['Items'][0]['Item']; // get single day activities list
                        
//                                            echo '<pre>'; print_r( $ActItiArray ); die;

                        // if result go to fetch Activity Ids
                        if( count( $ActItiArray ) ) {

                            // note : multi array result may be multi for single day
                            $ActivityITINERARY_ITEM_Result = $this->filterArrayByValueKeyPair( ['IsIncluded', 1 ], $ActItiArray );
//                                            echo '<pre>'; print_r( $ActivityITINERARY_ITEM_Result ); die;

                            // if has results than create array
                            if(is_array($ActivityITINERARY_ITEM_Result)) {

                                $ActivityItineraryArrTemp = $ActivityItineraryArrResultsTemp = $ActivityItineraryArr = [];
                                foreach ($ActivityITINERARY_ITEM_Result as $keyAct => $valueAct) {
                                    
                                    $activitiesInCity   = $cityITINERARYTitle[0]['Activities']['Activity']; // get all activities in city
                                    
                                    $ActivityItineraryArrResultsTemp = $this->filterArrayByValueKeyPair( ['RefActivityId', $valueAct['Id'] ], $activitiesInCity )[0];
                                    $ActivityItineraryArrTemp[] = ['RefActivityId' => $ActivityItineraryArrResultsTemp['RefActivityId'] , 'Title'=> $ActivityItineraryArrResultsTemp['Title'] ];
                                    
                                }
//                                            echo '<pre>'; print_r( $ActivityItineraryArrResultsTemp ); die;

                                $ActivityItineraryArr = $ActivityItineraryArrTemp;
                            }

                        }
                        // else part of : result go to fetch Activity Ids (define array as blank)
                        else {
                            $ActivityItineraryArr = [];
                        }

                    }
                    $itineraryArrCustom[$ItineraryId]['activity'] = $ActivityItineraryArr;
                }


                // get sightseeing of the days Itinerary
                if($SightSeeingITINERARY[0]['Type'] === 'SIGHTSEEING' ) {


                    // fetch which hotel is included true / false
                    if( count($SightSeeingITINERARY[0]['Items']) ) {

                        $SSItiArray = $SightSeeingITINERARY[0]['Items'][0]['Item']; // get single day sightsing list

                        // if result go to fetch Activity Ids
                        if( count( $SSItiArray ) ) {

                            // note : multi array result may be multi for single day
                            $SightSeeingITINERARY_ITEM_Result = $this->filterArrayByValueKeyPair( ['IsIncluded', 1 ], $SSItiArray);

                            // if has results than create array
                            if(is_array($SightSeeingITINERARY_ITEM_Result)) {

                                $SightSeeingItineraryArrTemp = $SightSeeingItineraryArrResultsTemp = $SightSeeingItineraryArr = [];
                                foreach ($SightSeeingITINERARY_ITEM_Result as $keySS => $valueSS) {
                                    
                                    $sightseeingInCity   = $cityITINERARYTitle[0]['SightSeeings']['SightSeeing']; // get all SightSeeing in city
                                    
                                    $SightSeeingItineraryArrResultsTemp = $this->filterArrayByValueKeyPair( ['RefSSId', $valueSS['Id'] ], $sightseeingInCity )[0];
                                    $SightSeeingItineraryArrTemp[] = ['RefSSId' => $SightSeeingItineraryArrResultsTemp['RefSSId'] , 'Title'=> $SightSeeingItineraryArrResultsTemp['Title'] ];

                                    
                                }
                                $SightSeeingItineraryArr = $SightSeeingItineraryArrTemp;
                            }

                        }
                        // else part of : result go to fetch Activity Ids (define array as blank)
                        else {
                            $SightSeeingItineraryArr = [];
                        }

                    }
                    $itineraryArrCustom[$ItineraryId]['sightSeeing'] = $SightSeeingItineraryArr;

                }


                // prepare the hotel type
                if( $temp['hotelTypeArr'][$i]['Type'] != null) {
                $hotelTypeArr[] = [
                    'type' => $temp['hotelTypeArr'][$i]['Type']
                    ];
                }

$priceArr[] = $hotelPrice;

            } // outer for loop ends here
                    
            //  end : prepare the array for itineray

            
            // start : tour type + hotel type price calculations
            $tourType = $this->tourTypeArr;
            
            $tempArray  = [];
            for( $a = 0; $a < count($tourType); $a++ ) {
//                $tourType[] = [ 'TourType' => $temp['tourType'][$a]['TourType'] , 'TourTypeTitle' => $temp['tourType'][$a]['TourTypeTitle'] ];
                $tourType[$a]['active'] = (isset($temp['tourType'][$a]['TourType'])) ? true : false;

            }
            // end : tour type + hotel type price calculations

            
            
            // start : code for getting price in all category and tour type
            // 
            $priceArrJson = [];
            $category   = '';
            foreach ( $temp['tourType'] as $tourKey => $tourVal) {
                $category   = $tourVal['Categories']['Category'];
                if(is_array($category) && count($category)) {
                    $tempCategory= [];
                    foreach ($category as $keyCat => $valueCat) {
                        $tempCategory[$valueCat['Type']] = [ 'price'=> $valueCat['PriceAdditional']];
                    }
                }
                $priceArrJson[$tourVal['TourTypeTitle'][0]] = $tempCategory;
            }
            
            // 
            // end : code for getting price in all category and tour type
            
            //$defaultTourtype = ($tourType[0]['TourType']) ? $tourType[0]['TourType'] : $tourType[1]['TourType'];

//            echo '<pre>';
//            print_r($tourType);
//            echo '<hr/>';
//            print_r($priceArrJson);
//            die;


            
//            $itineraryArrCustomUnfiltered   = $itineraryArrCustom;


            // filter the itinerary array to display
//            $itineraryArrCustom = $this->filterDuplicateItinerary( 'dayNumber' , $itineraryArrCustom);
            

            
            $result = [
//                'package_hotelcategoryid' => $resultval['package_hotelcategoryid'],
                'MinPax' => $resultval['MinPax'],
                'Nights' => $resultval['Nights'],
                'status' => $resultval['status'],
                'Destinations' => $resultval['Destinations'],
                'DestinationID' => $resultval['DestinationsId'], 
                'PriceRange' => $resultval['MinPrice']. '-' .$resultval['MaxPrice'],
                'hotelTypeArr' => $this->array_filter_rv($hotelTypeArr), // custom field
                'package' => ['Name' => trim(str_replace('/', ' ', $temp['package']['Name'])), 'Tagline1' => trim($temp['package']['Tagline1'])], // custom field
                'itineraryArr' => $itineraryArrCustom, // custom field
                //'tourtype' => $defaultTourtype, // custom field
                    ];
            

        return Zend_Json::encode($result);

    }
    
    
    
    // prepare the json for front end as per need for listing page
    public function customiseForJsonSendqueryV2( array $resultval , $market ='B2C' , $package_hotelcategoryid ,  $package_mealplantype =0 )
    {

        $result = $myCategoryArray = $hotelStandardArr = $tourTypeArray = [];
        $temp   = $shortJSON = $longJSON = $PackageCategoryStr = $PackageDestinationStr = '';
        
        $temp = array();
        $PackageType = $PackageSubType = $tourTypeRadio = $categoryDetails  = $destinationTitleCustomFinalStr = $MPType = '';
        $priceArrJson   = $tourTypeArrayOfIds = $destinationNightCountsArray = $destinationTitleCustomArray  = [];
        
            
        $longJSON   = Zend_Json::decode($resultval['LongJsonInfo'], true);
        $PackageType    = $resultval['PackageType'];
        $PackageSubType = $resultval['PackageSubType'];

          
          if( $longJSON != "error" ) {
            
            $temp['package']        = $longJSON['package']; // get package type array
            $temp['packageTypeArr'] = $longJSON['package']['PackageType']; // get package type array
            $temp['cityArr']            = $longJSON['package']['Cities']['City'];   // get cities included in package

            $temp['tourType']       = $longJSON['package']['TourTypes']['MarketType']; // get package validity
            $temp['itineraryArr']   = $longJSON['package']['Itineraries']['Itinerary']; // get Itineraries 
            $temp['Transfers']      = $longJSON['package']['Transfers']; // get package transfers
            $temp['OtherServices']  = $longJSON['package']['OtherServices']; // get package other services if available

            $hotelStandardArr   = [];
            
            
            // start : code for getting price in all category and tour type
            //
            
            $category = $TPId = '';
            
            /* get default category 
             * hotel standard array
             * Tour type defualt
             */
            $categoryDetails = $this->getCategoryAndPriceArray ( $temp['tourType'] , $market , $PackageType , $PackageSubType );
            
            
            $TPId = ($resultval['PackageType'] == 2) ? $longJSON['package']['TPId'] : $categoryDetails['TPId']; // get tpid all same for dynamic package
            
            $defaultCategoryId  = $categoryDetails['defaultCategoryId'];
            $defaultCategory    = $categoryDetails['defaultCategory'];
            $defaultTourType    = $categoryDetails['defaultTourType'];

            $hotelStandardArr   = $categoryDetails['hotelStandardArr'];
            $tourTypeArrayOfIds = $categoryDetails['tourTypeArrayOfIds'];
            $priceArrJson       = $categoryDetails['priceArrJson'];
            
//            $MPType = (!empty($categoryDetails['MPType']) && ($categoryDetails['MPType']!='LowestCost')) ? array_search($categoryDetails['MPType'], unserialize(CONST_MEAL_PLAN_ARR)) : 0;

            $MPType  = ($package_mealplantype) ? $package_mealplantype : '0';

            
            //
            // end : code for getting price in all category and tour type
//            echo $defaultCategory . $TPId . $defaultCategoryId; 
            
//            echo '<pre>'; print_r(); die('here');
//            echo '<pre>'; print_r($hotelStandardArr); die('here');

            $this->hotelTypeArr = $hotelStandardArr; // get hotel standard value dynamic
            
            $tourType = $this->tourTypeArr; // static value private and group

             

            //  start : prepare the array for itineray
            
            $itineraryArr       = $temp['itineraryArr'];
            $count_Itinerary    = count($itineraryArr) ;

            $itineraryArrCustom = $hotelTypeArr = $hotelsArr = $ActivityItineraryArr = $ActivityITINERARY_ITEM_Result = [];

            $cityITINERARY = $cityITINERARYTitle = $hotelsInCity = $destinationTitleCustomStr = '';
            $SightSeeingITINERARY = $SightSeeingITINERARY_ITEM = $SightSeeingITINERARY_Result = $SSItiArray = '';
             
            $destinationTitleCustomArray1 = [];
             for ($i=0; $i < $count_Itinerary; $i++ ) {
                 
                $cityITINERARY = $this->filterArrayByValueKeyPair( ['Type', 'CITY' ], $itineraryArr[$i]['ItineraryItem'] );

                $cityITINERARYTitle = $this->filterArrayByValueKeyPair( ['CityId', $cityITINERARY[0]['Id'] ], $temp['cityArr'] );

                $HotelITINERARY     = $this->filterArrayByValueKeyPair( ['Type', 'HOTEL' ], $itineraryArr[$i]['ItineraryItem'] );
                
                $ActivityITINERARY  = $this->filterArrayByValueKeyPair( ['Type', 'ACTIVITY' ], $itineraryArr[$i]['ItineraryItem'] );
                
                $SightSeeingITINERARY   = $this->filterArrayByValueKeyPair( ['Type', 'SIGHTSEEING' ], $itineraryArr[$i]['ItineraryItem'] );

                // filter the days Itinerary on the basis of hotel || hotel must be in last day of repeated day
                if(($HotelITINERARY[0]['Type']) == 'HOTEL' ) {
                    
                    $HotelITINERARY_ITEM = $hotelsInCity = $hotelsArrInner = $hotelPriceResultArr = $HotelITINERARY_ITEM_Result = [];
                    $hotelDetailsResult = $hotelID = $hotelName = $hotelStar = $hotelTARating = $hotelPrice = $hotelPriceAdditional = $hotelIncluded = '';
                    
                    foreach ($this->hotelTypeArr as $hotelTypeKey => $hotelTypeValue) {

                        // select only selected category hotels only

//echo Catabatic_Helper::getPackageType($package_hotelcategoryid) ."=". $hotelTypeValue ;

                        if( Catabatic_Helper::getPackageType($package_hotelcategoryid) == $hotelTypeValue ) {
                            // fetch hotel ids only
                            $HotelITINERARY_ITEM = $this->filterArrayByValueKeyPair( ['Type', ($hotelTypeValue) ], $HotelITINERARY[0]['Items'] );
                             
                            // fetch which hotel is included true / false
                            if( count($HotelITINERARY_ITEM[0]['Item']) ) {
                              $HotelITINERARY_ITEM_Result = $this->filterArrayByValueKeyPair( ['IsIncluded', true ], $HotelITINERARY_ITEM[0]['Item'] );
                            }
                            
                            // filter the data because here are multy true condition in all type of meal plan 
                            if( ($MPType) ) {
                                $HotelITINERARY_ITEM_Result = $this->filterArrayByValueKeyPair( ['MealPlanId' , $MPType ] , $HotelITINERARY_ITEM_Result);
                            }

                                                       
                            
                            $hotelID    = ( $HotelITINERARY_ITEM_Result ) ?  $HotelITINERARY_ITEM_Result[0]['Id']: 0;
                            $MealPlanId    = isset($HotelITINERARY_ITEM_Result[0]['MealPlanId']) ?  $HotelITINERARY_ITEM_Result[0]['MealPlanId']: $HotelITINERARY_ITEM_Result[0]['MealPlanId'] = '';
                            $hotelIncluded  = ( $hotelID ) ?  $HotelITINERARY_ITEM_Result[0]['IsIncluded']: 0;
                            
//echo $hotelID ."# ";

//echo $MealPlanId , $MPType;
                            if($hotelID) {
                                if( ($MPType) && ($MealPlanId == $MPType) ) {
                                    $hotelsArrInner[$hotelTypeKey] = [ 'itemid' => $hotelID, 'MealPlanId' => $MealPlanId, 'IsIncluded' => $hotelIncluded ];
                                } else {
                                    $hotelsArrInner[$hotelTypeKey] = [ 'itemid' => $hotelID, 'MealPlanId' => $MealPlanId, 'IsIncluded' => $hotelIncluded ];
                                }
                            }
                        }
                    }
//                        echo '<pre>'; print_r( $hotelsArrInner ); die;

                    $ItineraryId = $this->getValueByKeyFromArray('ItineraryId' , $itineraryArr[$i] );
                    
                    $itineraryArrCustom[$ItineraryId] = [
                        'hotel' => $hotelsArrInner , 
                    ];
                }
                
                
                // get acitivities of the days Itinerary
                if($ActivityITINERARY[0]['Type'] === 'ACTIVITY' ) {


                    // fetch which hotel is included true / false
                    if( count($ActivityITINERARY[0]['Items']) ) {

                        $ActItiArray = $ActivityITINERARY[0]['Items'][0]['Item']; // get single day activities list
                        

                        // if result go to fetch Activity Ids
                        if( count( $ActItiArray ) ) {

                            // note : multi array result may be multi for single day
                            $ActivityITINERARY_ITEM_Result = $this->filterArrayByValueKeyPair( ['IsIncluded', 1 ], $ActItiArray );
//                                            echo '<pre>'; print_r( $ActivityITINERARY_ITEM_Result ); die;

                            // if has results than create array
                            if(is_array($ActivityITINERARY_ITEM_Result)) {

                                $ActivityItineraryArrTemp = $ActivityItineraryArrResultsTemp = $ActivityItineraryArr = [];
                                foreach ($ActivityITINERARY_ITEM_Result as $keyAct => $valueAct) {
                                    
                                    $activitiesInCity   = (isset($cityITINERARYTitle[0]['Activities']['Activity'])?$cityITINERARYTitle[0]['Activities']['Activity']:array()); // get all activities in city
                                    
                                    $ActivityItineraryArrResultsTemp = $this->filterArrayByValueKeyPair( ['RefActivityId', $valueAct['Id'] ], $activitiesInCity )[0];
                                    $ActivityItineraryArrTemp[] = [
                                        'itemid' => $ActivityItineraryArrResultsTemp['RefActivityId'] ,
//                                        'Title'=> $ActivityItineraryArrResultsTemp['Title'] ,
                                        'IsIncluded' => $valueAct['IsIncluded']
                                            ];
                                   
                                }
                                           
                                $ActivityItineraryArr = $ActivityItineraryArrTemp;
                            }

                        }
                        // else part of : result go to fetch Activity Ids (define array as blank)
                        else {
                            $ActivityItineraryArr = [];
                        }

                    }
                    $itineraryArrCustom[$ItineraryId]['activity'] = $ActivityItineraryArr;
                    
                }


                // get sightseeing of the days Itinerary
                if($SightSeeingITINERARY[0]['Type'] === 'SIGHTSEEING' ) {


                    // fetch which hotel is included true / false
                    if( count($SightSeeingITINERARY[0]['Items']) ) {

                        $SSItiArray = $SightSeeingITINERARY[0]['Items'][0]['Item']; // get single day sightsing list

                        // if result go to fetch Activity Ids
                        if( count( $SSItiArray ) ) {

                            // note : multi array result may be multi for single day
                            $SightSeeingITINERARY_ITEM_Result = $this->filterArrayByValueKeyPair( ['IsIncluded', 1 ], $SSItiArray);

                            // if has results than create array
                            if(is_array($SightSeeingITINERARY_ITEM_Result)) {

                                $SightSeeingItineraryArrTemp = $SightSeeingItineraryArrResultsTemp = $SightSeeingItineraryArr = [];
                                foreach ($SightSeeingITINERARY_ITEM_Result as $keySS => $valueSS) {
                                    
                                    $sightseeingInCity   = isset($cityITINERARYTitle[0]['SightSeeings']['SightSeeing'])?$cityITINERARYTitle[0]['SightSeeings']['SightSeeing']:array(); // get all SightSeeing in city
                                    
                                    $SightSeeingItineraryArrResultsTemp = $this->filterArrayByValueKeyPair( ['RefSSId', $valueSS['Id'] ], $sightseeingInCity )[0];
                                    $SightSeeingItineraryArrTemp[] = [
                                        'itemid' => $SightSeeingItineraryArrResultsTemp['RefSSId'] ,
//                                        'Title'=> $SightSeeingItineraryArrResultsTemp['Title'] ,
                                        'IsIncluded' => $valueSS['IsIncluded']
                                            ];

                                    
                                }
                                $SightSeeingItineraryArr = $SightSeeingItineraryArrTemp;
                            }

                        }
                        // else part of : result go to fetch Activity Ids (define array as blank)
                        else {
                            $SightSeeingItineraryArr = [];
                        }

                    }
                    $itineraryArrCustom[$ItineraryId]['sightSeeing'] = $SightSeeingItineraryArr;

                }

// itinerary transfers is not in use right now (02 Nov 2017)
// 
                // get sightseeing of the days Itinerary
                if($SightSeeingITINERARY[0]['Type'] === 'TRANSFERS' ) {


                    // fetch which hotel is included true / false
                    if( count($SightSeeingITINERARY[0]['Items']) ) {

                        $SSItiArray = $SightSeeingITINERARY[0]['Items'][0]['Item']; // get single day sightsing list

                        // if result go to fetch Activity Ids
                        if( count( $SSItiArray ) ) {

                            // note : multi array result may be multi for single day
                            $SightSeeingITINERARY_ITEM_Result = $this->filterArrayByValueKeyPair( ['IsIncluded', 1 ], $SSItiArray);

                            // if has results than create array
                            if(is_array($SightSeeingITINERARY_ITEM_Result)) {

                                $SightSeeingItineraryArrTemp = $SightSeeingItineraryArrResultsTemp = $SightSeeingItineraryArr = [];
                                foreach ($SightSeeingITINERARY_ITEM_Result as $keySS => $valueSS) {
                                    
                                    $sightseeingInCity   = $cityITINERARYTitle[0]['SightSeeings']['SightSeeing']; // get all SightSeeing in city
                                    
                                    $SightSeeingItineraryArrResultsTemp = $this->filterArrayByValueKeyPair( ['RefSSId', $valueSS['Id'] ], $sightseeingInCity )[0];
                                    $SightSeeingItineraryArrTemp[] = [
                                        'itemid' => $SightSeeingItineraryArrResultsTemp['RefSSId'] ,
//                                        'Title'=> $SightSeeingItineraryArrResultsTemp['Title'] ,
                                        'IsIncluded' => $valueSS['IsIncluded']
                                            ];

                                    
                                }
                                $SightSeeingItineraryArr = $SightSeeingItineraryArrTemp;
                            }

                        }
                        // else part of : result go to fetch Activity Ids (define array as blank)
                        else {
                            $SightSeeingItineraryArr = [];
                        }

                    }
                    $itineraryArrCustom[$ItineraryId]['sightSeeing'] = $SightSeeingItineraryArr;

                }


                // prepare the hotel type
                if( $temp['hotelTypeArr'][$i]['Type'] != null) {
                    $hotelTypeArr[] = [
                        'type' => $temp['hotelTypeArr'][$i]['Type']
                    ];
                }

                $priceArr[] = $hotelPrice;
            } // outer for loop ends here
             
           //echo '<pre>'; print_r($itineraryArrCustom); die('end');
             
             $itineraryArrCustom1['itineraries'] = $itineraryArrCustom;
                

                // collect transfers here in package
                if(isset($temp['Transfers']) && count($temp['Transfers'])) {
                    $transfersOUTArr = [];
                    foreach ($temp['Transfers'] as $key2 => $value2) {
                       $transfersOUTArr[] = ['itemid' => $value2['fixTransSysId'] , 'IsIncluded'=> $value2['isIncluded'] ];
                    }
                }
                // collect other services here in package
                if(isset($temp['OtherServices']) && count($temp['OtherServices'])) {
                    $servicesArr = [];
                    foreach ($temp['OtherServices'] as $key2 => $value2) {
                        $servicesArr[] = ['itemid' => $value2['otherSrvSysId'] , 'IsIncluded'=> $value2['isCostInclInTP'] ];
                    }
                }

                $itineraryArrCustom1['others'] = ['services' => $servicesArr , 'transfers' => $transfersOUTArr ];  // append iteration array here for other services and transfers
            }
            

            $result = [
//                'package_hotelcategoryid' => $resultval['package_hotelcategoryid'],
                'MinPax' => $resultval['MinPax'],
                'Nights' => $resultval['Nights'],
                'status' => $resultval['status'],
                'Destinations' => $resultval['Destinations'],
                'DestinationID' => $resultval['DestinationsId'], 
                'PriceRange' => $resultval['MinPrice']. '-' .$resultval['MaxPrice'],
                'hotelTypeArr' => $this->array_filter_rv($hotelTypeArr), // custom field
                'package' => ['Name' => trim(str_replace('/', ' ', $temp['package']['Name'])), 'Tagline1' => trim($temp['package']['Tagline1'])], // custom field
                'itineraryArr' => $itineraryArrCustom1, // custom field
                //'tourtype' => $defaultTourtype, // custom field
                ];
            
        return Zend_Json::encode($result);

    }
    
    
    /*
     * Function to get price after itenary change
     */
    public function prepareItineraryArrayForSendingQuery( array $array )
    {
        $tempArray = [];
        foreach ( $array as $key => $value ) {

            if( $key == 'itineraries' ) {
                $itiArr = [];
                foreach ($value as $key1 => $value1) {
                    // collect city here
                    if(isset($value1['city'])) {
                        $cityArr = [];
                        foreach ($value1['city'] as $key2 => $value2) {
                            $cityArr[] = $value2;
                        }
                    }
                    // collect hotels here
                    if(isset($value1['hotel'])) {
                        $hotelArr = [];
                        foreach ($value1['hotel'] as $key2 => $value2) {
                            if($value2['IsIncluded']) {
                                $hotelArr[] = $value2;
                            }
                        }
                    }
//                     collect activity here
                    if(isset($value1['activity'])) {
                        $activityArr = [];
                        foreach ($value1['activity'] as $key2 => $value2) {
                            $activityArr[] = ['itemid' => $value2['itemid'] , 'IsIncluded'=> ( (isset($value2['IsIncludedNew']) && $value2['IsIncludedNew'] ) ? $value2['IsIncludedNew'] : $value2['IsIncluded'] ) ];
                        }
                    }
//                    // collect sightseeing here
                    if(isset($value1['sightSeeing'])) {
                        $sightSeeingArr = [];
                        foreach ($value1['sightSeeing'] as $key2 => $value2) {
                            $sightSeeingArr[] = ['itemid' => $value2['itemid'] , 'IsIncluded'=> ( (isset($value2['IsIncludedNew']) && $value2['IsIncludedNew'] ) ? $value2['IsIncludedNew'] : $value2['IsIncluded'] ) ];
                        }
                    }
//                    // collect sightseeing here
                    if(isset($value1['transfers'])) {
                        $transfersArr = [];
                        foreach ($value1['transfers'] as $key2 => $value2) {
                            $transfersArr[] = ['itemid' => $value2['itemid'] , 'IsIncluded'=> ( (isset($value2['IsIncludedNew']) && $value2['IsIncludedNew'] ) ? $value2['IsIncludedNew'] : $value2['IsIncluded'] ) ];
                        }
                    }
                    
                    $itiArr[$key1] = [
                        'day' => $value1['day'] ,
                        'city' => $cityArr ,
                        'hotel' => $hotelArr ,
                        'activity' => $activityArr ,
                        'sightSeeing' => $sightSeeingArr ,
                        'transfers' => $transfersArr ,
                    ];
                }
                $tempArray[$key] = $itiArr;
            }
            
            if( $key == 'others') {
                // collect city here
                if(isset($array['others']['services'])) {
                    $servicesArr = [];
                    foreach ($array['others']['services'] as $key2 => $value2) {
                        $servicesArr[] = ['itemid' => $value2['itemid'] , 'IsIncluded'=> ( (isset($value2['IsIncludedNew']) && $value2['IsIncludedNew'] ) ? $value2['IsIncludedNew'] : $value2['IsIncluded'] ) ];
                    }
                }
                // collect city here
                if(isset($array['others']['transfers'])) {
                    $transfersOUTArr = [];
                    foreach ($array['others']['transfers'] as $key2 => $value2) {
                        $transfersOUTArr[] = $value2;
                    }
                }

                $tempArray['others'] = ['services' => $servicesArr , 'transfers' => $transfersOUTArr ];
            }
        }
        
//        echo '<pre>'; print_r( $tempArray ); die('here');

        return $tempArray;
    }
    

    



    public function trimContent ( $content , $limit ) {
        return substr($content , 0 , $limit) . ( ($limit < strlen($content)) ? "..." : "" ) ;
    }
 
    // arrry to find range by any value
    public function getRangeByValue( $ratearray , $input )
    {
        foreach ($ratearray as $key => $value){
            $keyex = explode('-',$value);
            if($keyex[0] <= $input && $keyex[1] >= $input){
                return $value;
            }
        }
    }
    
    public function count_values_2d_array( array $arr , $index ){
 
        $out = $out1 = [];
        foreach ( $arr as $key => $value ){

            foreach ( $value as $key2 => $value2 ){
                if (array_key_exists($value2, $out)){
                    $out[$value2] = $out[$value2]+1;
                } else {
                    $out[$value2] = 1;
                }
            }
//            $out[] = array_count_values($value);
            $out1[] = $out;
        }
        return $out;
    }

    public function update_json_footer_file( $resultset , $footer_destination ) {
        
        $mergeArray = ["social_links" => $resultset , "footer_destination" => $footer_destination ];
        $this->createJsonFile( $mergeArray , 'footer.json' , "public/data/static/" ); // create json file
    }
    
    public function getHours( $min )
    {
//        return gmdate("H:i", $min);
        $init   = $min*60; // seconds
        $hours  = floor($init / 3600);
        $minutes= floor(($init / 60) % 60);
        $seconds= $init % 60;
        
        $minlen = strlen($minutes);
        $minutes = ($minlen==1) ? '0'.$minutes : $minutes;
        
        $return = '';

        if($hours) {
            $return .= "{$hours} Hours";
        }
        if($minutes) {
            if($hours && $minutes != '00') {
                $return .= " : ";
            }
            if( $minutes != '00') {
                $return .= "{$minutes} Minutes";
            }
        }
        
        return $return;;
    }
 
    public function getDevice()
    {
        $mobile_detect = new Catabatic_MobileDetect();

        if( $mobile_detect->isMobile() =="mobile" ) {
          $deviceType = "mobile";  
        }
        else if( $mobile_detect->isTablet() =="tablet" ) {
          $deviceType = "tablet";  
        } else {
          $deviceType = "desktop";    
        }
        return $deviceType;
    }


    /*
     * function is used to get price from multiple date wise price arry
     */
    public function getPriceFromMultiDatewise( $priceArray ) {
//        return ['price' => $priceArray];
        return $priceArray;
    }


    /*
     * Get popup sessions for send enquiry
     */
    public function getMypopSess() {
        $this->myNamespace = new Zend_Session_Namespace('MypopSess'); // get user end infomations
        return $this->myNamespace->MypopSess;
    }
    
    public function getMypopCookie($name) {
        if(isset($_COOKIE[$name])){
            $MyCookies = $_COOKIE[$name]; // get user end infomations
            return Zend_Json::decode($MyCookies);
        }
        else{
            return false;
        }
        
    }
    
    
    /*
     * Params : config for to email, cc email, subject, body | type (package/hotel/activity)
     */

    public function sendEmail(array $configs , $type )
    {
        $fromName   = $configs['fromName'];
        $emailId    = $configs['to'];
        $emailIdcc  = $configs['cc'];
        $mailSubject= $configs['subject'];
        $bodyText   = $configs['body'];

        $mail = new Zend_Mail("iso-8859-1");
        $mail->addTo($emailId)
//            ->addCc($emailIdcc)
            ->setSubject($mailSubject)
            ->setBodyHtml($bodyText)
            ->setFrom( $emailId , $fromName );

        if(isset($configs['to']) && ($configs['to'] !='')) {
            $mail->addCc($emailIdcc);
        }

        $mail->addHeader('X-Priority', '1');
        $mail->addHeader('X-MSMail-Priority', 'High');
        $mail->addHeader('Importance', 'High');

        try {
            $issend = $mail->send(); //send mail
        } catch (Exception $err) {
//                print_r($err, true);
            $err->getMessage();
            $issend = FALSE;
        }

//            echo "<pre>";
//            var_dump($issend);
            
    }
    
    
    public function sendEmailPackage(array $configs , $type )
    {
        $fromName   = $configs['fromName'];
        $fromEmail   = $configs['fromEmail'];
        $emailId    = $configs['to'];
        $emailIdcc  = $configs['cc'];
        $mailSubject= $configs['subject'];
        $bodyText   = $configs['bodyHtml'];

        $mail = new Zend_Mail("iso-8859-1");
        $mail->addTo($emailId)
//            ->addCc($emailIdcc)
            ->setSubject($mailSubject)
            ->setBodyHtml($bodyText)
            ->setFrom( $fromEmail , $fromName );

        if(isset($configs['to']) && ($configs['to'] !='')) {
            $mail->addCc($emailIdcc);
        }

        $mail->addHeader('X-Priority', '1');
        $mail->addHeader('X-MSMail-Priority', 'High');
        $mail->addHeader('Importance', 'High');

        try {
            $issend = $mail->send(); //send mail
        } catch (Exception $err) {
//                print_r($err, true);
            $err->getMessage();
            $issend = FALSE;
        }

//            echo "<pre>";
//            var_dump($issend);
            
    }
    
    
    public function mailSentByElastice($emailData,$arrEmailStatistics = array()) {
        //echo '<pre>';print_r($emailData);die;

        $url = 'https://api.elasticemail.com/v2/email/send';      

        $to = implode(";",$emailData['to']);
        try {
            $emailSenderKey = trim('def51ec9-0f32-418c-9f33-e8751ded6f98');
            $post = array(
                'from' => $emailData['fromEmail'],
                'fromName' => $emailData['fromName'],
                'apikey' => $emailSenderKey,
                'subject' => $emailData['subject'],
                'to' => $emailData['to'],
                'bodyHtml' => $emailData['bodyHtml'],
                //'bodyText' => $emailData['bodyText'],
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
    
    public function sortArrayByColumn(&$arr = [], $col, $order = SORT_ASC){
        $sort_col = array();
        
        if(count($arr)>0){
            foreach ($arr as $key => $row) {
                @$sort_col[$key] = $row[$col];
            }
        }
        
        @array_multisort($sort_col, $order, $arr);
    }
    
    public function footerOurPartner(){
        $this->objMdl   = new Admin_Model_CRUD();
        $ourPartners  = $this->objMdl->rv_select_all( 'tbl_ourpartner' , ['*'] , [ 'status'=>1 ] ,  ['id'=>'ASC']);
        return $ourPartners;
    }
    
     public function deleteSearchJsonFile($path){
        $now   = time();
        if(is_dir($path)){
            $files = scandir($path,1);
            foreach ($files as $file) {
              if ($now - @filemtime($file) >= 60 * 60 * 24 * 1) {
                unlink($path."/".$file);
              }
            }
        }
    } 
    
    /// for flight some helpers
    
    public function getDateFormatFromDbDates($string) { // get date & time
        $arrFormatedDateTime = array();
        $arr = explode(" ", $string);
        if(count($arr) > 0){
            $date = new DateTime($arr[0]);
            $strDate = $date->format('d M y');
            $strTime = @substr($arr[1],0,5);
            $arrFormatedDateTime = array(
                'strDate' => $strDate,
                'strTime' => $strTime
            );  
        }
        
        return $arrFormatedDateTime;
    }
    public function getDateTimeFormatFromApiString($string) { // get date & time
        $arrFormatedDateTime = array();
        $arr = explode("T", $string);
        if(count($arr) > 0){
            $date = new DateTime($arr[0]);
            $strDate = $date->format('d M y');
            $strTime = @substr($arr[1],0,5);
            $arrFormatedDateTime = array(
                'strDate' => $strDate,
                'strTime' => $strTime
            );  
        }
        
        return $arrFormatedDateTime;
    }
    
     public function convertAmountToWords($number) {

        $hyphen = '-';
        $conjunction = ' and ';
        $separator = ', ';
        $negative = 'negative ';
        $decimal = ' point ';
        $dictionary = array(
            0 => 'zero',
            1 => 'one',
            2 => 'two',
            3 => 'three',
            4 => 'four',
            5 => 'five',
            6 => 'six',
            7 => 'seven',
            8 => 'eight',
            9 => 'nine',
            10 => 'ten',
            11 => 'eleven',
            12 => 'twelve',
            13 => 'thirteen',
            14 => 'fourteen',
            15 => 'fifteen',
            16 => 'sixteen',
            17 => 'seventeen',
            18 => 'eighteen',
            19 => 'nineteen',
            20 => 'twenty',
            30 => 'thirty',
            40 => 'fourty',
            50 => 'fifty',
            60 => 'sixty',
            70 => 'seventy',
            80 => 'eighty',
            90 => 'ninety',
            100 => 'hundred',
            1000 => 'thousand',
            1000000 => 'million',
            1000000000 => 'billion',
            1000000000000 => 'trillion',
            1000000000000000 => 'quadrillion',
            1000000000000000000 => 'quintillion'
        );

        if (!is_numeric($number)) {
            return false;
        }

        if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
            // overflow
            trigger_error(
                    'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX, E_USER_WARNING
            );
            return false;
        }

        if ($number < 0) {
            return $negative . $this->convertAmountToWords(abs($number));
        }

        $string = $fraction = null;

        if (strpos($number, '.') !== false) {
            list($number, $fraction) = explode('.', $number);
        }

        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens = ((int) ($number / 10)) * 10;
                $units = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen . $dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds = $number / 100;
                $remainder = $number % 100;
                $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
                if ($remainder) {
                    $string .= $conjunction . $this->convertAmountToWords($remainder);
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int) ($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = $this->convertAmountToWords($numBaseUnits) . ' ' . $dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= $this->convertAmountToWords($remainder);
                }
                break;
        }

        if (null !== $fraction && is_numeric($fraction)) {
            $string .= $decimal;
            $words = array();
            foreach (str_split((string) $fraction) as $number) {
                $words[] = $dictionary[$number];
            }
            $string .= implode(' ', $words);
        }

        return $string;
    }
}
