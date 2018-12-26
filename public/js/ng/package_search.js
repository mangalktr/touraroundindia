app.directive('bsTooltip', function () {
    return {
        restrict: 'A',
        link: function (scope, element, attrs) {
            $(element).hover(function () {
                // on mouseenter
                $(element).tooltip('show');
            }, function () {
                // on mouseleave
                $(element).tooltip('hide');
            });
        }
    };
});
app.directive('icheck', function () {
    return {
        restrict: 'A',
        link: function ($scope, element, $attrs) {
            element.iCheck({
                checkboxClass: "icheckbox_flat",
                radioClass: "iradio_flat"
            }).on('ifClicked', function (event) {
                $(event.target).trigger('click');
            });
        }
    };
});
app.directive("scroll", function ($window) {
    return function (scope, element, attrs) {
        angular.element($window).bind("scroll", function () {
//             if (this.pageYOffset >= 350) {
            if ($(window).scrollTop() >= ($(document).height() - $(window).height()) * 0.75) {
                element.addClass('min');
                $('#loadmore').click();
            }
        });
    };
});
app.controller("PackageListingController", ["$scope", "$location", "$http", '$filter', 'rvServices', '$timeout', function ($scope, $location, $http, $filter, rvServices, $timeout) {
        $scope.loading = true;
        $scope.outerforloop = {};
        $scope.outerforloop.Price = [];
        $scope.outerforloop.PriceDiscounted = [];
        $scope.hotelType = false;
        $scope.packageCategoryTypeLimit = 2;
        $scope.limitToResult = 0;
        $scope.totalResult = 0;
        $scope.tempPriceArray = [];
        var $fullUrl, $fullUrlTemp, $url, $urlparams;
        $scope.getUrlParameter2 = function (sParam) {
            var sPageURL = decodeURIComponent(window.location.search.substring(1)),
                    sURLVariables = sPageURL.split('&'),
                    sParameterName,
                    i;

            for (i = 0; i < sURLVariables.length; i++) {
                sParameterName = sURLVariables[i].split('=');

                if (sParameterName[0] === sParam) {
                    return sParameterName[1] === undefined ? true : sParameterName[1];
                }
            }
        };

        var Keydes = ($scope.getUrlParameter2('key')) ? $scope.getUrlParameter2('key') : '';


        $scope.packageInclusionLimit = 3;

        $scope.filterCat, $scope.filterDest, $scope.filterNight, $scope.filterPrice, $scope.tempArr = [];

//        $http({method: 'GET', url: conf.SITEURL + 'public/data/dynamic/tours_package.json?'+Math.random() , headers: {'Content-Type': 'application/json', 'Access-Control-Allow-Origin': '*', 'Access-Control-Allow-Methods': 'GET ', 'Access-Control-Allow-Headers': 'Content-Type, X-Requested-With', }, /*cache: true*/}).then(function successCallback(response) {
        $http({method: 'GET', url: conf.SITEURL + 'tours/package/getsearchdata?key=' + Keydes, headers: {'Content-Type': 'application/json', 'Access-Control-Allow-Origin': '*', 'Access-Control-Allow-Methods': 'GET ', 'Access-Control-Allow-Headers': 'Content-Type, X-Requested-With', }, /*cache: true*/}).then(function successCallback(response) {

            var des = ($scope.getUrlParameter('key')) ? $scope.getUrlParameter('key') : '';
            var noofday1 = ($scope.getUrlParameter('noofday')) ? $scope.getUrlParameter('noofday') : '';
            var noofday = (noofday1 !='') ? parseInt(noofday1)-1 : '';
            var pricerange = ($scope.getUrlParameter('pricerange')) ? $scope.getUrlParameter('pricerange') : '';
            var category = ($scope.getUrlParameter('cat')) ? $scope.getUrlParameter('cat') : '';
            //alert(des.split('+').join(' '));
            if(des === 'INT' || des === 'DOM' || des === 'FIX' || des === 'PRIVATE'){
                des = '';
            }
            
            $scope.resultset = $scope.resultsetFull = $scope.searchInResponse(response.data.rows, des.split('+').join(' '), noofday, pricerange, category); // get rows here
            $scope.filterCat = response.data.filterCat; // filter array
            $scope.filterDest = response.data.filterDest; // filter array
            $scope.filterNight = response.data.filterNight; // filter array
            $scope.filterPrice = response.data.filterPrice; // filter array
          

        }, function errorCallback(response) {
        }).finally(function () {
            $scope.loading = false;
            $scope.hotelType = 'Standard';
            $scope.orderby = 'Price';
            $scope.orderval = false;
            $scope.limitToResult = 12; // total result
            $scope.totalResult = $scope.resultset.length; // total result

            $scope.filterNight = $scope.searchInResponseForFilter($scope.resultset, $scope.filterNight, 'Nights').sort(rvServices.sortNumber); // filter array
            $scope.filterCat = $scope.searchInResponseForFilter($scope.resultset, $scope.filterCat, 'Category'); // filter array
            $scope.filterDest = $scope.searchInResponseForFilter($scope.resultset, $scope.filterDest, 'Destination'); // filter array
            $scope.filterPrice = $scope.searchInResponseForFilter($scope.resultset, $scope.filterPrice, 'Price'); // filter array
        });


        $scope.getUrlParameter = function (sParam) {
            var sPageURL = decodeURIComponent(window.location.search.substring(1)),
                    sURLVariables = sPageURL.split('&'),
                    sParameterName,
                    i;

            for (i = 0; i < sURLVariables.length; i++) {
                sParameterName = sURLVariables[i].split('=');

                if (sParameterName[0] === sParam) {
                    return sParameterName[1] === undefined ? true : sParameterName[1];
                }
            }
        };



        $scope.searchInResponseForFilter = function (data, filterValues, type) {
            var finaldata = [];
            var innerkey = '';

            if (type == 'Nights') {
                innerkey = 'Duration';
                $.each(data, function (index, element) {
                    if ($.inArray(element[innerkey], filterValues) != -1) {
                        // push data in final array if already not exists
                        if (($.inArray(element[innerkey], finaldata)) == -1) {
                            finaldata.push(element[innerkey]);
                        }
                    }
                });
                return finaldata;
            }
            else if (type == 'Category') {
                innerkey = 'PackageTypeArr';
                $.each(data, function (index, element) {
                    $.each(element[innerkey], function (i, ele) {
                        if ($.inArray(ele, filterValues) != -1) {
                            // push data in final array if already not exists
                            if (($.inArray(ele, finaldata)) == -1) {
                                finaldata.push(ele);
                            }
                        }
                    });
                });
                return finaldata;
            }
            else if (type == 'Destination') {
                innerkey = 'Destinations';
                $.each(data, function (index, element) {
//                    console.log(element[innerkey]);
                    var desti = element[innerkey];
//                    desti.split(',');
//                    console.log(desti.split(','));
                    $.each(desti.split(','), function (i, ele) {
                        if ($.inArray(ele, filterValues) != -1) {
                            // push data in final array if already not exists
                            if (($.inArray(ele, finaldata)) == -1) {
                                finaldata.push(ele);
                            }
                        }
                    });
                });
                return finaldata;
            }
            else if (type == 'Price') {
                innerkey = 'PriceRange';
                $.each(data, function (index, element) {
                    if ($.inArray(element[innerkey], filterValues) != -1) {
                        // push data in final array if already not exists
                        if (($.inArray(element[innerkey], finaldata)) == -1) {
                            finaldata.push(element[innerkey]);
                        }
                    }
                });
                return finaldata;
            }
            else {
                return data;
            }
        };

        $scope.searchInResponse = function (data, destination, noofnights, pricerange, category) {
            var data1 = [];
            var data2 = [];
            var data3 = [];
            var data4 = [];
            var finaldata = [];
            var filteredArray = [];

            if ((destination == '') && (noofnights === '') && (pricerange == '') && (category == '')) {
                return data;
            }

            if (destination !== '') {
                $.each(data, function (index, element) {
                    if ($scope.checkSubstrInString(destination, element.Destinations) || $scope.checkSubstrInString(destination, element.Countries)) {
                        data1.push(element);
                    }
                });
                filteredArray = data1;
            } else {
                filteredArray = data;
            }

            var filteredArray1 = [];
            if (parseInt(noofnights) >= 0) {
               
                $.each(filteredArray, function (index, element) {
                    
                    if (element.Duration == noofnights) {
                        data2.push(element);
                    }
                });
                filteredArray1 = data2;
            }
            else {
                filteredArray1 = filteredArray;
            }

            var filteredArray2 = [];
            if ((pricerange !== '') && (pricerange.toLowerCase() !== 'all')) {

                $.each(filteredArray1, function (index, element) {
                    if (element.PriceRange == pricerange) {
                        data3.push(element);
                    }
                });
                filteredArray2 = data3;
            } else {
                filteredArray2 = filteredArray1;
            }


            var filteredArray3 = [];
            if ((category !== '')) {

                $.each(filteredArray2, function (index, element) {

                    if ($.inArray(category, element.PackageTypeArr) != -1) {
                        data4.push(element);
                    }
                });
                filteredArray3 = data4;
            } else {
                filteredArray3 = filteredArray2;
            }

            return filteredArray3;
        };


        $scope.applyFilterBoxSize = function (type, tag, lengthh, $index) {
//console.log(lengthh);
            if (lengthh > 5) {
                if (lengthh - 1 == $index) {
                    var $selector = angular.element('.box-filter-' + type + ' .filterbox-' + tag);
                    $selector.css({'height': '180px', 'overflow': 'hidden', 'position': 'relative'});
                    $selector.append('<a href="javascript:void(0);" style="position: absolute; bottom: 5px; right: 8px;" class="iamplusminus" title="More..."><i class="fa fa-plus"></i></a>');
                }
            }
        };


//        start : code for filter data 

        // function is used for filtering package result data
        $scope.filterData = function () {

//            $scope.showLoader('result_area'); // show loader

            // declare the variable here
            var filterSTR = {'night': [], 'cat': [], 'desti': [], 'price': []};

            angular.element('.filter-box input:checkbox').each(function () {
                if ($(this).is(":checked")) {
                    if ($(this).hasClass('filter-nn')) {
                        filterSTR.night.push($(this).val());
                    }
                    else if ($(this).hasClass('filter-cat')) {
                        filterSTR.cat.push($(this).val());
                    }
                    else if ($(this).hasClass('filter-destination')) {
                        filterSTR.desti.push($(this).val());
                    }
                    else if ($(this).hasClass('filter-price')) {
                        filterSTR.price.push($(this).val());
                    }
                }
            });

            // if filter has any value
            if ((filterSTR.night.length > 0) || (filterSTR.cat.length > 0) || (filterSTR.desti.length > 0) || (filterSTR.price.length > 0)) {
                $scope.resultset = this.filterFromArray($scope.resultsetFull, filterSTR);
            }
            else {
                $scope.resultset = $scope.resultsetFull;
            }
//            $scope.hideLoader('result_area'); // hide loader
        };
        
        $scope.sortData = function (key){
//            console.log($scope.resultsetFull);
            if(key === 'lowPrice'){
                $scope.term = 'Price';
            }
            if(key === 'highPrice'){
                $scope.term = '-Price';
            }
            if(key === 'lowDuration'){
                $scope.term = 'Duration';
            }
            if(key === 'highDuration'){
                $scope.term = '-Duration';
            }
            $scope.filtered = $filter('orderBy')($scope.resultsetFull, $scope.term);
            $scope.resultset = $scope.filtered;
            console.log($scope.resultset);
            
        };

        $scope.applyDurationFilter = function (data, nightArr) {
            // number of nights
            var resultsetFiltered = [];
            if (nightArr.length == 0) {
                return data;
            }
            $.each(data, function (index, element) {
                if (nightArr.length > 0) {
                    $.each(nightArr, function (indexinn, elementinn) {
                        if (elementinn == element.Duration) {
                            if ($scope.checkAlreadyExistsInArray(element.PkgSysId, resultsetFiltered, 'PkgSysId') == false) {
                                resultsetFiltered.push(element);
                            }
                        }
                    });
                }
            });
            return resultsetFiltered;
        };

        $scope.applyCategoryFilter = function (data, catArr) {
            // filter by category
            var resultsetFiltered = [];
            if (catArr.length == 0) {
                return data;
            }
            $.each(data, function (index, element) {
                if (catArr.length > 0) {
                    $.each(catArr, function (indexinn, elementinn) {
                        if ($.inArray(elementinn, element.PackageTypeArr) != -1) {

                            // push into array if not exists
                            if ($scope.checkAlreadyExistsInArray(element.PkgSysId, resultsetFiltered, 'PkgSysId') == false) {
                                resultsetFiltered.push(element);
                            } else {
                                //resultsetFiltered.slice(element , 1);
                            }

                        }
                    });
                }
            });
            return resultsetFiltered;
        };

        $scope.applyDestinationFilter = function (data, DestiArr) {
            // filter by category
            var resultsetFiltered = [];
            if (DestiArr.length == 0) {
                return data;
            }
            $.each(data, function (index, element) {
                if (DestiArr.length > 0) {
                    $.each(DestiArr, function (indexinn, elementinn) {
                        if ($scope.checkSubstrInString(elementinn, element.Destinations)) {
                            resultsetFiltered.push(element);
                        }
                    });
                }
            });
            return resultsetFiltered;
        };

        $scope.applyPriceRangeFilter = function (data, priceArr) {
            // number of nights
            var resultsetFiltered = [];
            if (priceArr.length == 0) {
                return data;
            }
            $.each(data, function (index, element) {
                if (priceArr.length > 0) {
                    $.each(priceArr, function (indexinn, elementinn) {
                        if (elementinn == element.PriceRange) {
                            if ($scope.checkAlreadyExistsInArray(element.PkgSysId, resultsetFiltered, 'PkgSysId') == false) {
                                resultsetFiltered.push(element);
                            }
                        }
                    });
                }
            });
            return resultsetFiltered;
        };


        $scope.filterFromArray = function (data, filterSTR) {
            var resultsetFiltered = [];

            resultsetFiltered = $scope.applyDurationFilter(data, filterSTR.night);


            resultsetFiltered = $scope.applyCategoryFilter(resultsetFiltered, filterSTR.cat);


            resultsetFiltered = $scope.applyDestinationFilter(resultsetFiltered, filterSTR.desti);


            resultsetFiltered = $scope.applyPriceRangeFilter(resultsetFiltered, filterSTR.price);

//                console.log(resultsetFiltered);
            return resultsetFiltered;
        };


//        end : code for filter data 


        $scope.checkAlreadyExistsInArray = function (value, arr, innerkey) {
            var ret = false;
            // if single dimension array
            if (innerkey == null) {
                if ($.inArray(value, arr) != -1) {
                    ret = true;
                }
            }
            // if multi dimenstion array then pass inner key also
            else {
                $.each(arr, function (index, element) {
                    if (value == element[innerkey]) {
                        ret = true;
                    }
                });
            }
            return ret;
        };

        // param : substr , full string
        $scope.checkSubstrInString = function (substr, str) {
            if (str && substr) {
                if (str.toLowerCase().indexOf(substr.toLowerCase()) == -1)
                    return false;
                else
                    return true;
            }
        };



        // load all data after 7 seconds only if production mode
        if (appmode == 'MODE_PROD') {
            $timeout(function () {
//                $scope.displayall();
            }, 7000);
        }

        $scope.loadmore = function () {
            if ($scope.limitToResult <= $scope.totalResult) {
                $scope.limitToResult = $scope.summ($scope.limitToResult, 10);
            }
            if ($scope.limitToResult > $scope.totalResult) {
//                $('#loadmorepaging').parent().css({'display' : 'none'});
                $('#pagging_loader').css({'display': 'none'});
//                angular.element(document.querySelector('#id_div_filter_option')).removeClass('div_filter_option');
            }
//            $('#pagging_loader').html('');
            $scope.hideLoader();
        };

        $scope.displayall = function () {
            $scope.showLoader();
            $scope.limitToResult = $scope.totalResult;
            $('#loadmorepaging').parent().css({'display': 'none'});
            $scope.hideLoader();
        };


        $scope.alert = function (text) {
            alert(text);
        };
        $scope.sortListings = function ($event, orderby, orderval) {
            var currentElem = angular.element($event.currentTarget);
            angular.element(document.querySelector('#sortingContainer')).find('a').removeClass('active');
            currentElem.addClass('active');
            $scope.showLoader();
            $scope.orderby = orderby;
            $scope.orderval = orderval;
            $scope.hideLoader();
        };
        $scope.FilterPriceRange = {};
        $scope.FilterDuration = {};
        $scope.FilterDestination = {};

        $scope.showLoader = function (divid) {
//            $('#'+divid).next().text('Loading...');
            var divid1 = (divid) ? divid : 'overlay';
            var myHeight = window.innerHeight;
            var padding = 0;
            padding = parseInt(myHeight / 2);
            padding = padding + 'px';
            $('#' + divid1).css({'display': 'block', 'height': 'auto', 'padding-top': padding, 'text-align': 'center', 'background': '#f3f3f3'}).html('<img src="' + SITEURL + 'public/images/loader.gif" />');
        };

        $scope.hideLoader = function (divid) {
            var divid1 = (divid) ? divid : 'overlay';
            $('#' + divid1).css({'display': 'none', 'height': 'auto', 'text-align': 'center', 'background': '#ffffff'});
        };

        $scope.openmodal = function (popid)
        {
            var ar = {'p1': ['p1 data'], 'p2': ['p2 data'], 'p3': ['p3 data'], };
            var ret = ar[popid];
            var clicked_pack_id = 33;
            $('#overlay').show();
            $(".popup_" + clicked_pack_id).css({'right': '0px', 'opacity': '1', 'z-index': '9999', 'background-color': '#FFFFFF', 'display': 'block'});
            $('.package_det_d_side').html(ret);
        };
        $scope.showItineraryTable = function (itiPkgSysId, itidefaultHotelStandard) {
            $('.hotelCategoryTable' + itiPkgSysId).hide();
            $('#hotelCategoryTable' + itidefaultHotelStandard + itiPkgSysId).show().removeClass('ng-hide');
        };
        $scope.summ = function (a, b) {
            return parseInt(a) + parseInt(b);
        };
        $scope.rvGetSeoUrl = function (str) {
            return rvServices.rvSanitizeForUrl(str);
        };

        $scope.changePrices = function (PkgSysId, pType, categoryName, changeType) {

//            console.log(PkgSysId);
//            console.log(pType);
//            console.log(categoryName);
//            console.log(changeType);
//          
            var changed_mpid = $('#mptype_select_' + categoryName + "_" + PkgSysId).val();
            if (changed_mpid) {
                $("#mealplantype_" + PkgSysId).val(changed_mpid);
            }

            pType = parseInt(pType);

            var pTypeChar = (pType === 1) ? 'P' : 'G';

            if ((('pr' in $scope.tempPriceArray[PkgSysId][pTypeChar][categoryName]) == true)) {

                var rt, tpid = '';
                var $price;
                rt = $scope.tempPriceArray[PkgSysId];
                if (pType === 1) {
                    $price = rt.P[categoryName].pr;
                    tpid = rt.P[categoryName].TPId;
                }
                else if (pType === 2) {
                    $price = rt.G[categoryName].pr;
                    tpid = rt.G[categoryName].TPId;
                }

                if (tpid) {
                    $('#packagetpid_' + PkgSysId).val(tpid);
                }
                $scope.outerforloop.Price[parseInt(PkgSysId)] = $scope.moneyformatinrhtml($price);
                $scope.outerforloop.PriceDiscounted[parseInt(PkgSysId)] = $scope.moneyformatinrhtml($price);
            }
            else {

                return $http.get(conf.SITEURL + 'public/data/package_price.json').then(function (data) {
                    var rt, tpid = '';
                    var $price;
                    rt = rvServices.rvFilterArrayByKey(PkgSysId, data.data);

                    var aa = rt[pTypeChar];
                    $price = aa[categoryName].price[0];
                    tpid = aa[categoryName].TPId;

                    // start : write the data here
                    var prd, DiscountValInPerc, DiscountVal;

                    angular.forEach(rt, function (value, key) {

                        $scope.tempPriceArray[PkgSysId][key] = {};

                        angular.forEach(value, function (valueINN, keyINN) {

                            $price = valueINN.price[0];

                            if ($price['DiscountType'] === 1) {
                                prd = parseFloat($price['PricePerPerson']) + parseFloat($price['DiscountVal']);
                            }
                            else if ($price['DiscountType'] === 2) {
                                DiscountValInPerc = ($price['PricePerPerson'] * $price['DiscountVal']) / 100;
                                prd = parseFloat($price['PricePerPerson']) + parseFloat(DiscountValInPerc);
                            }
                            else {
                                prd = parseFloat($price['PricePerPerson']);
                            }
                            $scope.tempPriceArray[PkgSysId][key][keyINN] = {TPId: valueINN.TPId, pr: parseFloat($price['PricePerPerson']), prd: prd}; // write variable
                        });
                    });


                    // end : write the data here

                    var rate = $scope.tempPriceArray[PkgSysId];
                    var aa = rate[pTypeChar];
                    var bb = aa[categoryName];
//console.log($scope.tempPriceArray);
                    $price = bb.pr;
                    tpid = bb.TPId;

                    if (tpid) {
                        $('#packagetpid_' + PkgSysId).val(tpid);
                    }
//                    $scope.outerforloop.Price[parseInt(PkgSysId)] = rvServices.rvMoneyFormatINR(rvServices.rvChangePriceValue(parseInt(PkgSysId), $price), 2, '');
//                    $scope.outerforloop.PriceDiscounted[parseInt(PkgSysId)] = rvServices.rvMoneyFormatINR(rvServices.rvChangePriceDiscountedValue(parseInt(PkgSysId), $price), 2, '');
                    $scope.outerforloop.Price[parseInt(PkgSysId)] = rvServices.rvMoneyFormatINR($price, 2, '');
                    $scope.outerforloop.PriceDiscounted[parseInt(PkgSysId)] = rvServices.rvMoneyFormatINR($price, 2, '');
                    return rt;
                });
            }
        };

        $scope.changeType = function (ev, PkgSysId) {
//            $location.path('/ranvir'+PkgSysId);
            var categoryName = ev;
            var temp, pType, packageName, Countries, mp = '';
            pType = parseInt($('input[name="tType_' + PkgSysId + '"]:checked').val());
            packageName = $('#packagename_' + PkgSysId).val();
            Countries = $('#package_location_' + PkgSysId).val();

            $scope.showItineraryTable(PkgSysId, categoryName);
            var categoryId = '';

            categoryId = conf.CATEGORY[categoryName];

            // write the data here 
            var pTypeChar = (pType === 1) ? 'P' : 'G';

            if ((PkgSysId in $scope.tempPriceArray) === false) {
                $scope.tempPriceArray[PkgSysId] = {};
            }
            if ((pTypeChar in $scope.tempPriceArray[PkgSysId]) == false) {
                $scope.tempPriceArray[PkgSysId][pTypeChar] = {};
            }
            if ((categoryName in $scope.tempPriceArray[PkgSysId][pTypeChar]) == false) {
                $scope.tempPriceArray[PkgSysId][pTypeChar][categoryName] = {};
            }

            $("#hotelCategory" + PkgSysId).val(categoryId);
            $scope.changePrices(PkgSysId, pType, categoryName, 'category');

            mp = $('#mealplantype_' + PkgSysId).val();
            rvServices.rvChangeUrl(PkgSysId, categoryName, packageName, Countries, mp);
        };

        $scope.changePackageType = function (type, PkgSysId) {
            var pType = type;
            var categoryName, temp, packageName, Countries, mp = '';
            categoryName = $('input[name="hotelTypeElement_' + PkgSysId + '"]:checked').val();
            packageName = $('#packagename_' + PkgSysId).val();
            Countries = $('#package_location_' + PkgSysId).val();

            // write the data here 
            var pTypeChar = (pType === 1) ? 'P' : 'G';

            if ((PkgSysId in $scope.tempPriceArray) === false) {
                $scope.tempPriceArray[PkgSysId] = {};
            }
            if ((pTypeChar in $scope.tempPriceArray[PkgSysId]) == false) {
                $scope.tempPriceArray[PkgSysId][pTypeChar] = {};
            }
            if ((categoryName in $scope.tempPriceArray[PkgSysId][pTypeChar]) == false) {
                $scope.tempPriceArray[PkgSysId][pTypeChar][categoryName] = {};
            }

            $("#packageTourType" + PkgSysId).val(pType);
            $("#tourtype_" + PkgSysId).val(pType);
            $scope.changePrices(PkgSysId, pType, categoryName, 'package');
            mp = $('#mealplantype_' + PkgSysId).val();
            rvServices.rvChangeUrl(PkgSysId, categoryName, packageName, Countries, mp);
        };
        $scope.rvFromJson = function (data) {
            return angular.fromJson(data);
        };
        $scope.rvToArray = function (arr) {
            return arr;
        };
        $scope.print_rv = function (val)
        {
            console.log(val);
        };
        $scope.moneyformatinrhtml = function (n) {
            return rvServices.rvMoneyFormatINR(n, 2, '')
        };

        $scope.filterByPropertiesMatchingAND = function (data) {
            var matchesAND = true;
            for (var obj in $scope.filter) {
                if ($scope.filter.hasOwnProperty(obj)) {
                    if (noSubFilter($scope.filter[obj]))
                        continue;
                    if (!$scope.filter[obj][data[obj]]) {
                        matchesAND = false;
                        break;
                    }
                }
            }
            return matchesAND;
        };
        function noSubFilter(obj) {
            for (var key in obj) {
                if (obj[key])
                    return false;
            }
            return true;
        }
        $scope.filterSingle = '';
        $scope.resultset1 = $scope.resultset;




        // start : activity search listing here
        // 

        $scope.ngActivityTab = function () {

            $scope.loadingAct = true;
            $scope.activityContentLimit = 200;

            $http({method: 'GET', url: conf.SITEURL + 'public/data/dynamic/activities.json?' + Math.random(),
                headers: {'Content-Type': 'application/json', 'Access-Control-Allow-Origin': '*', 'Access-Control-Allow-Methods': 'GET ', 'Access-Control-Allow-Headers': 'Content-Type, X-Requested-With', },
                /*cache: true*/}).then(function successCallback(response) {

                var des = ($scope.getUrlParameter('key')) ? $scope.getUrlParameter('key') : '';
                var pricerange = ($scope.getUrlParameter('pricerange')) ? $scope.getUrlParameter('pricerange') : '';
                var category = ($scope.getUrlParameter('cat')) ? $scope.getUrlParameter('cat') : '';
                var noofday = '';

//                console.log(des);

//                $scope.resultsetACT = $scope.resultsetActFull = $scope.searchInResponse( response.data.rows , des , noofday , pricerange , category ); // get rows here
//                $scope.resultsetACT = $scope.resultsetActFull = response.data.rows; // get rows here
                $scope.resultsetACT = $scope.resultsetActFull = $scope.searchInResponseActivity(response.data.rows, des); // get rows here
                $scope.filterCatAct = response.data.filterCat; // filter array
                $scope.filterPriceAct = response.data.filterPrice; // filter array
//                                                console.log( $scope.filterPriceAct );
//                                                console.log( response.data );

//                console.log( $scope.resultsetACT );

//                rvServices.rvTrimcontent(50 , 'more' , 'morelink');
                $scope.moreContent = true;
                $scope.resultsetActFull.sort(sortAsc);
                //console.log($scope.resultsetActFull);

            }, function errorCallback(response) {
            }).finally(function () {
                $scope.loadingAct = false;
                $scope.limitToResultAct = 10; // total result
//                console.log( $scope.resultsetACT );
                $scope.totalResultAct = $scope.resultsetACT.length; // total result
                $scope.filterCatAct = $scope.searchInResponseForFilterActivity($scope.resultsetACT, $scope.filterCatAct, 'Category'); // filter array
                $scope.filterPriceAct = $scope.searchInResponseForFilterActivity($scope.resultsetACT, $scope.filterPriceAct, 'Price'); // filter array
//                                console.log( $scope.filterPriceAct );

            });
        };

        function sortAsc(a, b) {
            a = parseInt(a.PriceOrderBy);
            b = parseInt(b.PriceOrderBy);
            return a > b ? 1 : (a === b ? 0 : -1);
        }

        function sortDesc(a, b) {
            a = parseInt(a.PriceOrderBy);
            b = parseInt(b.PriceOrderBy);
            return a > b ? -1 : (a === b ? 0 : 1);
        }

        $scope.loadmoreAct = function () {
            if ($scope.limitToResultAct <= $scope.totalResultAct) {
                $scope.limitToResultAct = $scope.summ($scope.limitToResultAct, 10);
            }
            if ($scope.limitToResult > $scope.totalResult) {
//                $('#loadmorepaging').parent().css({'display' : 'none'});
                $('#pagging_loader').css({'display': 'none'});
//                angular.element(document.querySelector('#id_div_filter_option')).removeClass('div_filter_option');
            }
//            $('#pagging_loader').html('');
            $scope.hideLoader();
        };



        // 
        // end : activity search listing here



        //        start : code for filter activity data 

        $scope.filterActivityFromArray = function (data, filterSTR) {
            var resultsetFiltered = [];

            resultsetFiltered = $scope.applyCategoryFilterActivity(data, filterSTR.cat);

            resultsetFiltered = $scope.applyPriceRangeFilterActivity(resultsetFiltered, filterSTR.price);

            return resultsetFiltered;
        };


        $scope.searchInResponseForFilterActivity = function (data, filterValues, type) {
            var finaldata = [];
            var innerkey = '';
//                            console.log(filterValues);

            if (type == 'Category') {
                innerkey = 'ActivityGrType';
                $.each(data, function (index, element) {
                    $.each(element[innerkey], function (i, ele) {
                        if ($.inArray(ele, filterValues) != -1) {
                            // push data in final array if already not exists
                            if (($.inArray(ele, finaldata)) == -1) {
                                finaldata.push(ele);
                            }
                        }
                    });
                });
//                console.log(finaldata);
                return finaldata;
            }
            else if (type == 'Price') {
                innerkey = 'pRng';
                $.each(data, function (index, element) {
                    if ($.inArray(element[innerkey], filterValues) != -1) {
                        // push data in final array if already not exists
                        if (($.inArray(element[innerkey], finaldata)) == -1) {
                            finaldata.push(element[innerkey]);
                        }
                    }
                });
                return finaldata;
            }
            else {
                return data;
            }
        };


        $scope.applyCategoryFilterActivity = function (data, catArr) {
            // filter by category
            var resultsetFiltered = [];
            if (catArr.length == 0) {
                return data;
            }
//            console.log(data);
//            console.log(catArr);

            $.each(data, function (index, element) {
                if (catArr.length > 0) {
                    $.each(catArr, function (indexinn, elementinn) {
                        if ($.inArray(elementinn, element.ActivityGrType) != -1) {

                            // push into array if not exists
                            if ($scope.checkAlreadyExistsInArray(element.PkgSysId, resultsetFiltered, 'PkgSysId') == false) {
                                resultsetFiltered.push(element);
                            }

                        }
                    });
                }
            });
            return resultsetFiltered;
        };

        $scope.applyPriceRangeFilterActivity = function (data, priceArr) {
            // number of nights
            var resultsetFiltered = [];
            if (priceArr.length == 0) {
                return data;
            }
            $.each(data, function (index, element) {
                if (priceArr.length > 0) {
                    $.each(priceArr, function (indexinn, elementinn) {
                        if (elementinn == element.pRng) {
                            if ($scope.checkAlreadyExistsInArray(element.PkgSysId, resultsetFiltered, 'PkgSysId') == false) {
                                resultsetFiltered.push(element);
                            }
                        }
                    });
                }
            });
            return resultsetFiltered;
        };

        //        end : code for filter activity data 


        $scope.searchInResponseActivity = function (data, destination) {
            var data1 = [];

            var finaldata = [];
            var filteredArray = [];

            if ((destination == '')) {
                return data;
            }
//             console.log(data);

            if (destination !== '') {
                $.each(data, function (index, element) {
                    if ($scope.checkSubstrInString(destination, element.city)) {
                        data1.push(element);
                    }
                });
                filteredArray = data1;
            } else {
                filteredArray = data;
            }
//             console.log(filteredArray);

            return filteredArray;
        };




    }]);

app.filter('limitChar', function () {
    return function (content, length, tail) {
        if (isNaN(length))
            length = 50;

        if (tail === undefined)
            tail = "...";

        if (content.length <= length || content.length - tail.length <= length) {
            return content;
        }
        else {
            return String(content).substring(0, length - tail.length) + tail;
        }
    };
});


app.filter('trusted', ['$sce', function ($sce) {
        var div = document.createElement('div');
        return function (text) {
//        div.innerHTML = text;
            div.innerHTML = text.substring(0, 200);
            return $sce.trustAsHtml(div.textContent);
        };
    }]);


app.controller("SendQueryController", ["$scope", "$http", function ($scope, $http) {
        var deafualtForm = {inputName: '', inputEmail: '', inputPhone: '', inputMessage: ''}
        $scope.reset = function (formData) {
            $scope.formData = angular.copy(deafualtForm);
        }
        $scope.resultClass, $scope.resultMessage = '';
        $scope.formData;
        $scope.submitButtonDisabled = false;
        $scope.submitted = false;
        $scope.querysent = false;
        $scope.sendQuery = function (contactform) {
            $scope.submitted = true;
            $scope.submitButtonDisabled = true;
            $scope.querysent = false;
            if (contactform.$valid) {
                var gtxpackageid = $('#PkgSysIdHTML').val();
                var TravelPlanId = $('#packagetpid_' + gtxpackageid).val();
                var PriceRange = '';
                var PKGCheckInDate = '';
                var PKGCheckOutDate = '';
                var MinPrice = '';
                var MaxPrice = '';
                $scope.formData.TravelPlanId = TravelPlanId;
                $scope.formData.PriceRange = PriceRange;
                $scope.formData.PKGCheckInDate = PKGCheckInDate;
                $scope.formData.PKGCheckOutDate = PKGCheckOutDate;
                $scope.formData.MinPrice = MinPrice;
                $scope.formData.MaxPrice = MaxPrice;
                $http({method: 'POST', url: conf.SITEURL + 'detail/index/send-query', data: $.param($scope.formData), headers: {'Content-Type': 'application/x-www-form-urlencoded'}}).success(function (data) {
                    if (data.status) {
                        $scope.submitButtonDisabled = true;
                        $scope.resultMessage = data.message;
                        $scope.resultClass = 'bg-success';
                        $scope.submitButtonDisabled = false;
                        contactform.$setPristine(true);
                        $scope.reset(contactform);
                        $scope.querysent = true;
                        $scope.submitted = false;
                    } else {
                        $scope.submitButtonDisabled = false;
                        $scope.resultMessage = data.message;
                        $scope.resultClass = 'bg-danger';
                    }
                });
            } else {
                $scope.submitButtonDisabled = false;
                $scope.resultMessage = 'Please fill out all the fields mark red';
                $scope.resultClass = 'bg-danger';
            }
        }
    }]);


$(document).ready(function () {
    $(window).scroll(function () {
        if ($(window).scrollTop() >= ($(document).height() - $(window).height()) * 0.40) {
            $("#loadmore").click();
        }
    });
});

