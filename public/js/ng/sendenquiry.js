app.controller("SendQueryController", ["$scope", "$http", function($scope, $http) {
        var deafualtForm = {inputName: '', inputEmail: '', inputPhone: '', inputMessage: '',inputDestination:''}
        $scope.reset = function(formData) {
            $scope.formData = angular.copy(deafualtForm);
        };
        $scope.resultMessage = '';
        $scope.formData;
        $scope.submitButtonDisabled = false;
        $scope.submitted = false;
        $scope.querysent = false;
        $scope.sendQuery = function(contactform) {
            $scope.submitted = true;
            $scope.submitButtonDisabled = true;
            $scope.querysent = false;
            if (contactform.$valid) {
                var gtxpackageid = $('#PkgSysIdHTML').val();
                var TravelPlanId = $('#packagetpid_' + gtxpackageid).val();
                var cityid = $('#cityid').val();
                var planType = $('#planType').val();
                var PriceRange = '';
                var PKGCheckInDate = '';
                var PKGCheckOutDate = '';
                var MinPrice = '';
                var MaxPrice = '';
                $scope.formData.TravelPlanId = TravelPlanId;
                $scope.formData.cityid = cityid;
                $scope.formData.planType = planType;
                $scope.formData.PriceRange = PriceRange;
                $scope.formData.PKGCheckInDate = PKGCheckInDate;
                $scope.formData.PKGCheckOutDate = PKGCheckOutDate;
                $scope.formData.MinPrice = MinPrice;
                $scope.formData.MaxPrice = MaxPrice;
                $http({method: 'POST', url: conf.SITEURL + 'detail/index/send-query-details', data: $.param($scope.formData), headers: {'Content-Type': 'application/x-www-form-urlencoded'}}).success(function(data) {
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
                $scope.resultMessage = 'Please fill out all the fields mark asterisk.';
            }
        }
    }]);