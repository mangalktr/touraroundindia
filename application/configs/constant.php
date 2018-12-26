<?php
ini_set('MAX_EXECUTION_TIME', -1);
#define AdminEmail
define('Admin_Mail','piyush@catpl.co.in');
define('DEVELOPER_EMAIL','piyush@catpl.co.in');

define('ARR_SALUTION' ,serialize(array("1" => "Mr","2" => "Mrs","3" => "Miss")));
define('CONST_SALUTION', serialize(array(1=>'Mr','Ms','Mrs')));

define('CONST_FOOTER_COL', serialize( ['1'=>'Safari Tours','2'=>'European Tours','3'=>'Wild Life Tours', '6'=>'Blog Pages'] ));

define('CONST_YEAR_NAME', serialize( ['01'=>'January','02'=>'February','03'=>'March','04'=>'April','05'=>'May','06'=>'June','07'=>'July','08'=>'August','09'=>'September','10'=>'October','11'=>'November','12'=>'December'] ));

define('CONST_TOURTYPE', serialize([
            ['TourType'=> 1, 'TourTypeTitle'=> 'Private'],
            ['TourType'=> 2, 'TourTypeTitle'=> 'Group']
            ]));

define('CONST_PRICE_RANGE_1000', serialize([
            '1-1000' ,'1001-2000' , '2001-3000' , '3001-4000' , '3001-4000' , '3001-4000' , '4001-5000' , '5001-6000' , '6001-7000' , '7001-8000' , '8001-9000'
            ]
        ));

define('CONST_PRICE_RANGE_2000', serialize([
            '1-2000' ,'2001-4000' , '4001-6000' , '6001-8000' , '8001-10000' , '10001-12000' , '12001-14000' , '14001-16000' , '16001-18000' , '18001-20000' , '20001-22000'
            ]
        ));

define('CONST_PRICE_RANGE_5000', serialize([
            '1-5000' ,'5001-10000' , '10001-15000' , '15001-20000' , '20001-25000' , '25001-30000' , '30001-35000' , '35001-40000' , '40001-45000' ,'45001-50000' ,
            '51000-55000' ,'55001-60000' , '61001-65000' , '65001-70000' , '70001-75000' , '75001-80000' , '80001-85000' , '85001-90000' , '90001-95000' ,'95001-100000' 
            ]
        ));

define('CONST_PRICE_RANGE_10000', serialize([
            '1-10000' , '10001-20000' , '20001-30000' , '30001-40000' , '40001-50000' , '50001-60000' , '60001-70000' , '70001-80000' , '80001-90000' , '90001-100000'
            ]
        ));

define('CONST_INCLUSIONS_MASTER', serialize([
            'Airport Transfers' => 'paper-plane' ,
            'Hotel' => 'hotel',
             'Sightseeing' => 'tripadvisor', 
             'Activity' => 'tripadvisor',
            'Only Breakfast' => 'coffee' ,
            'Guided Tour' => 'tripadvisor' ,
            'Air Fare' => 'plane' ,
            'Flight' => 'plane' ,
            'Intercity Transfers' => 'exchange' ,
            'All Meals' => 'cutlery' ,
            'BF & Dinner' => 'spoon' ,
            'Welcome Drink' => 'glass' ,
            'Visa' => 'cc-visa' ,
            'Cab' => 'taxi' ,
            'Train station transfers' => 'train' ,
            'Train Station Transfers' => 'train' ,
            'Internet' => 'wifi' ,
            'Restaurant' => 'cutlery' ,
            'Kids Friendly' => 'child' ,
            'Transfers' => 'exchange' ,
            'Business Friendly' => 'briefcase' ,
            'Smoking' => 'fire' ,
            'Disabled Friendly' => 'wheelchair' ,
            'Cafe' => 'coffee' ,
            'Bar' => 'glass' ,
            'Sports' => 'futbol-o' ,
//            'Laundry'=>'laundry' 
            
            ]
        ));

define('CONST_AMENITIES_MASTER', serialize([
            'Airport Transfers' => 'paper-plane' ,
            'Only Breakfast' => 'inc-meal' ,
            'Guided Tour' => 'tripadvisor' ,
            'Air Fare' => 'flight' ,
            'Intercity Transfers' => 'exchange' ,
            'All Meals' => 'inc-meal' ,
            'BF & Dinner' => 'spoon' ,
            'Welcome Drink' => 'glass' ,
            'Visa' => 'cc-visa' ,
            'Cab' => 'transport' ,
            'Train station transfers' => 'train' ,
            'Internet' => 'wifi' ,
            'Restaurant' => 'inc-meal' ,
            'Kids Friendly' => 'child' ,
            'Transfers' => 'exchange' ,
            'Business Friendly' => 'briefcase' ,
            'Smoking' => 'fire' ,
            'Disabled Friendly' => 'wheelchair' ,
            'Cafe' => '' ,
            'Bar' => 'glass' ,
            'Sports' => 'futbol-o' ,
//            'Laundry'=>'laundry' 
            
            ]
        ));

define('CONST_DESTINATION_MASTER', serialize(['goa']));

define('CONST_MEAL_PLAN', serialize([
    'AP' => 'Breakfast, Lunch & Dinner',
    'CP' => 'Breakfast Only',
    'MAP' => 'Breakfast & Lunch/Dinner',
    'EP' => ''
    ]));

define('CONST_MEAL_PLAN_ARR', serialize([6 => "CP", 7 => "MAP", 8 => "AP", 9 => "EP"]));
$apiUrl = 'http://local.b2bzend.com';
//$apiUrl = 'https://globaltravelexchange.com';
define('API_AGENT_B2B_AUTH_LOGIN', $apiUrl.'/gtxwebservices/agentapi/agencyagentloginforb2c/');
define('API_AGENT_FORGOTPASSWORD', $apiUrl.'/gtxwebservices/agentapi/agentforgotpassword/');
define('API_AGENT_AUTH_LOGIN', $apiUrl.'/gtxwebservices/agentapi/agencyagentlogin/');
define('API_AGENT_AUTH_LOGIN_SOCIAL', $apiUrl.'/gtxwebservices/customerapi/agencycustomerloginsocial/');
define('API_CUSTOMER_AUTH_SIGNUP', $apiUrl.'/gtxwebservices/customerapi/agencycustomersignup/');
define('API_AGENT_PROFILE', $apiUrl.'/gtxwebservices/agentapi/getagentprofiles/');
define('API_AGENT_UPDATE_FORGOTPASSWORD', $apiUrl.'/gtxwebservices/agentapi/updateagentforgotpassword/');
define('API_AGENT_UPDATE_PROFILE', $apiUrl.'/gtxwebservices/agentapi/updateprofile/');
define('API_AGENT_CHANGEPASSWORD', $apiUrl.'/gtxwebservices/agentapi/changepassword/');
define('API_CUSTOMER_AUTH_LOGIN', $apiUrl.'/gtxwebservices/customerapi/agencycustomerlogin/');
define('API_CUSTOMER_LIST', $apiUrl.'/gtxwebservices/customerapi/getcustomerbooking/');
define('API_CUSTOMER_FLIGHTLIST', $apiUrl.'/gtxwebservices/customerapi/getcustomerbookingflight/');
define('API_CUSTOMER_FLIGHTINVOICE', $apiUrl.'/gtxwebservices/customerapi/customerinvoiceflight/');
define('API_CUSTOMER_FLIGHTVOUCHER', $apiUrl.'/gtxwebservices/customerapi/viewbookingvoucherflight/');
define('API_CUSTOMER_PROFILE', $apiUrl.'/gtxwebservices/customerapi/getcustomerprofiles/');
define('API_CUSTOMER_PROFILE_BYEMAIL_MOBILE', $apiUrl.'/gtxwebservices/customerapi/getcustomerprofilesbyemailmobile/');
define('API_CUSTOMER_CHANGEPASSWORD', $apiUrl.'/gtxwebservices/customerapi/changepassword/');
define('API_CUSTOMER_FORGOTPASSWORD', $apiUrl.'/gtxwebservices/customerapi/customerforgotpassword/');
define('API_CUSTOMER_UPDATE_FORGOTPASSWORD', $apiUrl.'/gtxwebservices/customerapi/updatecustomerforgotpassword/');
define('API_CUSTOMER_UPDATE_PROFILE', $apiUrl.'/gtxwebservices/customerapi/updateprofile/');
define('API_CUSTOMER_COUNTRYLIST', $apiUrl.'/gtxwebservices/customerapi/getcountrylist/');
define('API_CUSTOMER_CITYLIST', $apiUrl.'/gtxwebservices/customerapi/ajaxgetcitylist/');