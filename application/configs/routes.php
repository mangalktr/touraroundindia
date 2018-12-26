<?php

    $frontController  = Zend_Controller_Front::getInstance();

    /* Create router for Privacy Policy */

    $route = new Zend_Controller_Router_Route(

           ':action',

           array(

                'controller' => 'index'

           )

    );

    $frontController->getRouter()->addRoute('call', $route);

   

    /* Create router for Privacy Policy */

  /*  $route = new Zend_Controller_Router_Route(

           'privacy-policy/:id/*',

           array(

               'id'       => 1,

               'controller' => 'cms',

               'action'     => 'index'

           ),

           array('id' => '\d+')

    );

    $frontController->getRouter()->addRoute('privacy_policy', $route);

    

      /* Create router for Privacy Policy 

    $route = new Zend_Controller_Router_Route(

           'about-us/:id/*',

           array(

               'controller' => 'cms',

               'action'     => 'about-us'

           )

    );

    $frontController->getRouter()->addRoute('about_us', $route);

    /* Create router for Privacy Policy 

    $route = new Zend_Controller_Router_Route(

           'career/:id/*',

           array(

               'controller' => 'cms',

               'action'     => 'career'

           )

    );

    $frontController->getRouter()->addRoute('career', $route);

    /* Create router for Privacy Policy 

    $route = new Zend_Controller_Router_Route(

           'site-map/:id/*',

           array(

              'controller' => 'site-map',

               'action'     => 'index'

           )

    );

    $frontController->getRouter()->addRoute('site_map', $route);

    

    /* Create route for About us page */

     /* Create router for City */

   

    

    

    

     /* Create route for Agents */

    $route = new Zend_Controller_Router_Route(

            'agent/:agentId/*',

            array(

                'id'       => 1,

                'controller' => 'agent',

                'action'     => 'packagelist'

            ),

            array('agentId' => '\d+')

     );

    $frontController->getRouter()->addRoute('agent_packages', $route);

    

            /* Create router for Packages */

          $route = new Zend_Controller_Router_Route(

                  'package/:id/*',

                  array(

                      'id'       => 1,

                      'controller' => 'package',

                      'action'     => 'detail'

                  ),

                  array('id' => '\d+')

           ); 

           $frontController->getRouter()->addRoute('package_detail_without_SEO', $route);

           

           

  

     /*  $route = new Zend_Controller_Router_Route(

          ':packagedetail/',

          array(

              'controller' => 'package',

              'action'     => 'detail'

          ),

          array('package' => '\w+')

        ); 
//        $route = new Zend_Controller_Router_Route_Regex(

//            ':(\d+)/page/(\d+)',

//            array(

//              'controller' => 'package',

//              'action'     => 'detail'

//          ),

//          array('package' => '\w+')

//        );
          
          

   $frontController->getRouter()->addRoute('package_detail', $route);

   $route = new Zend_Controller_Router_Route(

          'package/detail/:id/*',

          array(

              'controller' => 'package',

              'action'     => 'detail'

          ),

          array('package1' => '\w+')

    ); 

    $frontController->getRouter()->addRoute('package_withoutseo_detail', $route);

  

    /* Create router for search result page */
/*
    $route = new Zend_Controller_Router_Route(

           'tour-packages/:packagename/*',

           array(

               'controller' => 'tour-packages',

               'action'     => 'index'

           ),

           array('id' => '\d+')

    );

    $frontController->getRouter()->addRoute('search_result', $route);

 */ 

     /* Create router for City */

        $route = new Zend_Controller_Router_Route(

            'city/:id/*',

            array(

                'id'       => 1,

                'controller' => 'city',

                'action'     => 'detail'

            ),

            array('id' => '\d+')

     );

     $frontController->getRouter()->addRoute('city_detail', $route);

         

    /* Create router for Destination */

    $route = new Zend_Controller_Router_Route(

           'destination/:id/*',

           array(

               'id'       => 1,

               'controller' => 'destination',

               'action'     => 'detail'

           ),

           array('id' => '\d+')

    );

    $frontController->getRouter()->addRoute('destination_detail', $route);

    

      /* Create router for Privacy Policy */

//    $route = new Zend_Controller_Router_Route(

//           'terms-of-use/:id/*',

//           array(

//               'id'       => 1,

//               'controller' => 'cms',

//               'action'     => 'index'

//           ),

//           array('id' => '\d+')

//    );

//    $frontController->getRouter()->addRoute('terms_of_use', $route);

//         

         

    /* Create router for search */

   /* $route = new Zend_Controller_Router_Route(

           'search/:lookingfor/*',

           array(

               'id'       => 1,

               'controller' => 'search',

               'action'     => 'index'

           ),

           array('id' => '\s+')

    );

    $frontController->getRouter()->addRoute('search_result', $route);

   */

   

    /* Create router for attractions */

   $route = new Zend_Controller_Router_Route(

          'attraction/:id/*',

          array(

              'id'       => 1,

              'controller' => 'city',

              'action'     => 'attraction'

          ),

          array('id' => '\d+')

   );

   $frontController->getRouter()->addRoute('attractions', $route);

   

//    /* Create router for search */

//   $route = new Zend_Controller_Router_Route(

//          'search/:lookingfor/*',

//          array(

//              'id'       => 1,

//              'controller' => 'search',

//              'action'     => 'index'

//          )

//         // array('lookingfor' => '\s')

//   );

//   $frontController->getRouter()->addRoute('search', $route);

         

         

          /* Create router for Search */

       /*  $route = new Zend_Controller_Router_Route(

                'search/:lookingfor/*',

                array(

                    'id'       => 1,

                    'controller' => 'search',

                    'action'     => 'index'

                ),

                array('lookingfor' => '([a-zA-Z-_0-9-]+)')

         );

         $frontController->getRouter()->addRoute('search_page', $route);

         */

         /*****************************************************/

         

         

      // get instance of front controller 

//echo "ravi";exit;

                      // define new route class 

                     // this route with define the route for 

                     // http://www.example.com/explore-product-10.html

                    // the id of the product found under variable name ‘id’

                    // to retrive it $this->getRequest->getParam(‘id)

                   // in the index action of product controller 

//            $router = Zend_Controller_Front::getInstance()->getRouter();

//            $route = new Zend_Controller_Router_Route(':action', array(

//                'module'     => 'default',

//                'controller' => 'package',

//                'action'     => 'detail'

//            ));

//            $router->addRoute('default-override', $route);

//    

    

//            $frontController  = Zend_Controller_Front::getInstance();

//           // $route = new Zend_Controller_Router_Route(

//            $route = new Zend_Controller_Router_Route_Regex(

//                //'package/detail/(\d+)\.html',

//                '(\d+)/travel-package-(\s.html)',

//                array(

//                    'controller' => 'package',

//                    'action'     => 'detail'

//                ),

//                array(

//                    1 => 'id',

//                 ),

//                'package/detail/%d-%s.html'

//                    // Package detail page: www.showmetrips.com/travel-package-DestinationName/PackageName

//                    //  (If required use ID as well after package name)

//            );

//            

//            $frontController->getRouter()->addRoute('package', $route);

            //add route

//            $router = $frontController->getRouter();

//            $router->addRoute(

//              'package', new Zend_Controller_Router_Route_Regex('package/(\d+)([a-zA-Z0-9-]+).html',

//                array(

//                  'module' => 'default',

//                  'controller' => 'package',

//                  'action' => 'detail',

//                ),

//                array('package' => 1),

//                'package/%s.html'

//              )

//            );

  

//        $router = $frontController->getRouter();

//        $router->addRoute(

//            'package',

//            new Zend_Controller_Router_Route('package/:id',

//                                             array('controller' => 'package',

//                                                   'action' => 'detail'))

//        );

      //  http://local.showmetrips.com/agent/packagelist/agentId/57

//        $route = new Zend_Controller_Router_Route(

//            'agent/:agentId/',

//            array(

//                'id'       => 1,

//                'controller' => 'agent',

//                'action'     => 'packagelist'

//            ),

//            array('agentId' => '\d+')

//        );

//        $frontController->getRouter()->addRoute('agent_packages', $route);

//        

       /* $router = Zend_Controller_Front::getInstance()->getRouter();

        $route = new Zend_Controller_Router_Route_Regex(

            'agent/(\d+)',

            array(

                'controller' => 'agent',

                'action'     => 'packagelist'

            ),

            array('agentId' => '\d+')

        );

        $router->addRoute('agent', $route);

        /*

        $router = $frontController->getRouter();

            $router->addRoute(

              'agent_packages', new Zend_Controller_Router_Route_Regex('agent/:agentId/([a-zA-Z0-9-]+).html',

                array(

                  'controller' => 'agent',

                  'action' => 'packagelist',

                ),

                array('package' => 1),

                'agent/:agentId/%s.html'

              )

          );

        */

        