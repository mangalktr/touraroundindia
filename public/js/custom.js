$(document).ready(function () {

    $(document).on('click', '.sendEnuiryPackage', function () {
        var PkgSysId = $(this).attr('data-rv');
        var packagetpid = $('#packagetpid_' + PkgSysId).val();
        var packagedesname = $('#packagedesname_' + PkgSysId).val();
        var packagedesid = $('#packagedesid_' + PkgSysId).val();

        $('.msg').hide().html('');
        $("#leadsend").val('0');
        $('#packagesys_id').val(PkgSysId);
        $('#package_tpid').val(packagetpid);
        $('#Destinations').val(packagedesname);
        $('#DestinationIDs').val(packagedesid);
    });

    $(document).on('click', '.sendRateEnuiryPackage', function () {
        var PkgSysId = $(this).attr('data-rvs');
        var PackageType = $('#packagetype_' + PkgSysId).val();
        var GTXPkgId = $('#gtxpackage_' + PkgSysId).val();
        var hotelcategoryid = $('#hotelcategoryid_' + PkgSysId).val();
        var packagetpid = $('#packagetpid_' + PkgSysId).val();
        var tourtype = $('#tourtype_' + PkgSysId).val();
        var packagenamemodal = $('#packagename_' + PkgSysId).val();
        var mealplantype = $('#mealplantype_' + PkgSysId).val();
        var packagedesname = $('#packagedesname_' + PkgSysId).val();
        $('.msgs').hide().html('');
        $('.smsg1').hide().html('');
        $("#leadsends").val('0');
        $('#expandsave').hide();
        $('#packagesys_ids').val(PkgSysId);
        $('#packagetype_ids').val(PackageType);
        $('#package_sids').val(GTXPkgId);
        $('#package_hotelcategoryids').val(hotelcategoryid);
        $('#package_mealplantypeids').val(mealplantype);
        $('#package_tpids').val(packagetpid);
        $('#package_tourtype_ids').val(tourtype);
        $('#packagenamemodal').html(packagenamemodal);
        $('#packagedesname').val(packagedesname);
        $('#displaySendRateEnquiryForm').show();
        $('.hidebutton').find('.sending1').show().html('Check Rate & Send Enquiry').attr('disabled', false);
        $('.package_rate_enquiry #tableCost, .class-modify-enquiry').hide();
        $('.package_rate_enquiry .smsg1').html('');
        $('.inserted-room-row').remove();
        $('.addmore').show();
        $('.package_rate_enquiry #traveler_adult_1').val(1);
        $('.package_rate_enquiry #traveler_kids_1 , .package_rate_enquiry #traveler_infant_1').val(0);
        $('#itinerary_inputs').val(1);
        $('#itinerary_rooms').val(2);
        $('.hidebutton').find('.class-modify-enquiry').hide();
        if ($('#check_TravelAgent').val() == 1) {
            $('#Send_Enquiry').click();
        }
        $('#putCategorydateselect').html('');
        var getCategorydateselect = $('#getCategorydateselect').html();
       
       $('#putCategorydateselect').html('<label for="date" class="Lable">Travel Date <strong class="text-danger">*</strong></label>'+getCategorydateselect);
        $('.continueTOPay').hide();
    });

    $(document).on('click', '.selectedTpId', function () {
        var PkgSysId = $('#PkgSysIdHTML').val();
        var TpSysId = $(this).attr('dataTpId');
        $('#packagetpid_' + PkgSysId).val(TpSysId);
    });

    $(document).on('click', '.removeit', function () {
        var count = $('#itinerary_inputs').val();
        var rooms = $('#itinerary_rooms').val();
        if (count == 1) {
            return false;
        }
        $('#itinerary_inputs').val(parseInt(count) - 1);
        $('#itinerary_rooms').val(parseInt(rooms) - 1);
        $('.room-rows-container').find('.inserted-room-row:last').remove();
    });

    $('.talktous input[name="mobile"]').keypress(function (e) {
        if (e.which !== 8 && e.which !== 0 && (e.which < 48 || e.which > 57)) {
            return false;
        }
    });

    $(".talktous").on('submit', function (e) {
        e.preventDefault();
        var data = $(this).serialize();
        var EmailReg = new RegExp(/^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/);
        if ($('.talktous input[name="name"]').val() == '') {
            $('.msg').html('Please enter your name').css('color', 'red').fadeIn().delay(2000).fadeOut();
            $('.talktous input[name="name"]').focus();
            return false;
        }
        if ($('.talktous input[name="email"]').val() == '') {
            $('.msg').html('Please enter your email id').css('color', 'red').fadeIn().delay(2000).fadeOut();
            $('.talktous input[name="email"]').focus();
            return false;
        }
        if (!EmailReg.test($('.talktous input[name="email"]').val())) {
            $('.msg').html('Please enter valid email address!!!').css('color', 'red').fadeIn().delay(2000).fadeOut();
            $('.talktous input[name="email"]').focus();
            return false;
        }
        if ($('.talktous input[name="mobile"]').val() == '') {
            $('.msg').html('Please enter your Mobile').css('color', 'red').fadeIn().delay(2000).fadeOut();
            $('.talktous input[name="mobile"]').focus();
            return false;
        }
        if (!$.isNumeric($('.talktous input[name="mobile"]').val())) {
            $('.msg').html('Mobile should be numeric!!').css('color', 'red').fadeIn().delay(2000).fadeOut();
            $('.talktous input[name="mobile"]').focus();
            return false;
        }
        if ($('.talktous input[name="mobile"]').val().length != '10') {
            $('.msg').html('Mobile should be 10 digit?').css('color', 'red').fadeIn().delay(2000).fadeOut();
            $('.talktous input[name="mobile"]').focus();
            return false;
        }
        if ($('.talktous textarea[name="message"]').val() == '') {
            $('.msg').html('Please enter your query!').css('color', 'red').fadeIn().delay(2000).fadeOut();
            $('.talktous textarea[name="message"]').focus();
            return false;
        }
         if ($('.talktous input[name="captcha"]').val() == '') {
            $('.msg').html('Please enter captcha code!').css('color', 'red').fadeIn().delay(2000).fadeOut();
            $('.talktous input[name="captcha"]').focus();
            return false;
        }
        $('.msg').html('');
        $('.sendingP').attr('disabled', 'disabled');
        $.ajax({url: SITEURL + 'cms/index/sendenquiry', type: 'POST', data: data, dataType: 'json', beforeSend: function () {
                $('.sendingP').val('Sending...');
            }, success: function (result) {
                if (result.status) {
                    if(result.status =='captcha'){
                          $('.sendingP').val('POST COMMENT').attr('disabled', false);
                        $('.msg').show().html(result.message).css({'color': 'red'}).fadeIn().delay(2000).fadeOut();;
                         return false;
                    }else{
                        $('.sendingP').val('POST COMMENT');
                    $('.msg').show().html('Your query has been sent successfully.').css({'color': '#5cb85c'});
//                    $('#successmsg').html("<center>Thank you <br>Your enquiry send successfully.<br> we'll be in touch with you shortly.</center>");

                    $('.talktous input[name="mobile"]').val('');
                    $('.talktous textarea[name="message"]').val('');
                    $('.talktous input[name="email"]').val('');
                    $('.talktous input[name="name"]').val('');
                    $('.talktous input[name="captcha"]').val('');
                    setTimeout(function () {
                        $('.msg').html('').css({'color': '#ff0000'});
                    }, 5000);
                }
                } else {
                    $('.sendingP').html('Send Enquiry').attr('disabled', false);
                    $('.msg').html(result.message)
                }


            }, error: function (result) {
                $('.sendingP').html('Send Enquiry').attr('disabled', false);
                alert('Some error occured.');
            }});

    });

    $(".sending").on('click', function (e) {
        var EmailReg = new RegExp(/^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/);
        e.preventDefault();
        var data = $(".package_enquiry").serialize();

        var leadsend = $("#leadsend").val();
        if ($('.package_enquiry input[name="email"]').val() == '') {
            $('.msg').show().html('Please enter your email address').css('color', 'red');
            $('.package_enquiry input[name="email"]').focus();
            return false;
        }
        if (!EmailReg.test($('.package_enquiry input[name="email"]').val())) {
            $('.msg').show().html('Please enter valid email address!!!').css('color', 'red');
            $('.package_enquiry input[name="email"]').focus();
            return false;
        }
        if ($('.package_enquiry input[name="mobile"]').val() == '') {
            $('.msg').show().html('Please enter your mobile number').css('color', 'red');
            $('.package_enquiry input[name="mobile"]').focus();
            return false;
        }
        if ($('.package_enquiry input[name="mobile"]').val().length != '10') {
            $('.msg').show().html('Mobile should be 10 digit?').css('color', 'red');
            $('.package_enquiry input[name="mobile"]').focus();
            return false;
        }
        if (!$.isNumeric($('.package_enquiry input[name="mobile"]').val())) {
            $('.msg').show().html('Mobile should be numeric!!').css('color', 'red');
            $('.package_enquiry input[name="mobile"]').focus();
            return false;
        }

        if (leadsend == '0') {
            $.ajax({url: SITEURL + 'cms/index/send-query-details', type: 'POST', data: data, dataType: 'json', beforeSend: function () {
                    $('.sending').html('<i class="ace-icon fa fa-spinner fa-spin orange bigger-125"></i> Sending...').attr('disabled', 'disabled');
                }, success: function (result) {
                    $("#leadsend").val('1');
                    $('.smsg').show().html(result.message).css('color', 'green');
                    $('.sending').hide();
                }, error: function (result) {
                    $('.smsg').show().html('Sorry, System encountered some error.\n\ We will revert back with suitable suggestions.').css({'color': '#ff0000'});
                }});
        }
    });

    $(".sending1").on('click', function (e) {
        var EmailReg = new RegExp(/^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/);
        e.preventDefault();
        var data = $(".package_rate_enquiry").serialize();
        var leadsend = $("#leadsends").val();
        if ($('.package_rate_enquiry input[name="email"]').val() == '') {
            $('.msgs').show().html('Please enter your email address');
            $('.package_rate_enquiry input[name="email"]').focus();
            return false;
        }
        if (!EmailReg.test($('.package_rate_enquiry input[name="email"]').val())) {
            $('.msgs').show().html('Please enter valid email address!!!');
            $('.package_rate_enquiry input[name="email"]').focus();
            return false;
        }
        if ($('.package_rate_enquiry input[name="mobile"]').val() == '') {
            $('.msgs').show().html('Please enter your mobile number');
            $('.package_rate_enquiry input[name="mobile"]').focus();
            return false;
        }
        if ($('.package_rate_enquiry input[name="mobile"]').val().length != '10') {
            $('.msgs').show().html('Mobile should be 10 digit?');
            $('.package_rate_enquiry input[name="mobile"]').focus();
            return false;
        }
        if (!$.isNumeric($('.package_rate_enquiry input[name="mobile"]').val())) {
            $('.msgs').show().html('Mobile should be numeric!!');
            $('.package_rate_enquiry input[name="mobile"]').focus();
            return false;
        }

        if (leadsend == '0') {
            if ($('#check_TravelAgent').val() == 1) {
                $("#leadsends").val('1');
                $('.package_rate_enquiry').find('.msgs').html('');
                $('.sending1').html('Calculate Cost &amp; Get Proposal').attr('disabled', false);
                $('#expandsave').show();
            } else {

                $.ajax({url: SITEURL + 'cms/index/sendenquiry', type: 'POST', data: data, dataType: 'json', beforeSend: function () {
                        $('.sending1').html('<i class="ace-icon fa fa-spinner fa-spin orange bigger-125"></i> Sending...').attr('disabled', 'disabled');
                    }, success: function (result) {
                        $("#leadsends").val('1');
                        $('.package_rate_enquiry').find('.msgs').html('');
                        $('.sending1').html('Calculate Cost &amp; Send Enquiry').attr('disabled', false);
                        $('#expandsave').show();
                    }, error: function (result) {
                        $('.smsg1').show().html('Sorry, System encountered some error.\n\ We will revert back with suitable suggestions.').css({'color': '#ff0000'});
                    }});
            }
        } else {
            if ($('#check_TravelAgent').val() == 1) {
                $('#expandsave').show();
            }

            if ($("#hidden_selected_hotel_cityid").val() == '') {
                $('.msgs').show().html('Please enter traveling from');
                $('.package_rate_enquiry input[name="from_destination_name"]').focus();
                return false;
            }
            if ($("#travelerDatePicker").val() == '') {
                $('.msgs').show().html('Please enter travel date');
                $('.package_rate_enquiry input[name="date"]').focus();
                return false;
            }
            var count_total_rooms = parseInt($('#itinerary_rooms').val());
            $('.noofpaxmsg').hide()
            
            var totaladult ;var totalchild ;
            for(var m=1; m<count_total_rooms; m++){
                var traveler_adult = $('#traveler_adult_'+parseInt(m)).val();
                var traveler_adult_bedtype = $('#traveler_adult_bedtype_'+parseInt(m)).val();
                var traveler_kids = $('#traveler_kids_'+parseInt(m)).val();
                var room_row_child_bedtype1 = $('#selectroom-row-child-bedtype-'+parseInt(m)+'-1').val();
                var room_row_child_bedtype2 =  $('#selectroom-row-child-bedtype-'+parseInt(m)+'-2').val();
                totaladult = parseInt(traveler_adult);
                totalchild = parseInt(traveler_kids) ;
                var totalTravellerss = totaladult + totalchild;
                var adultsbedtypeval = 0;
                var kidsbedtype1val = 0;
                var kidsbedtype2val = 0;
                if(traveler_adult_bedtype == 'extrabed'){
                    adultsbedtypeval = 1;
                }
                if(room_row_child_bedtype1 == 'extrabed'){
                    kidsbedtype1val = 1;
                }
                if(room_row_child_bedtype2 == 'extrabed'){
                    kidsbedtype2val = 1;
                }
                if(m==1){
                if(totaladult <= 1){
                    $('.noofpaxmsg').show().html('Please enter more than one travellers for room '+parseInt(m)).css({'color': 'red'});
                    return false;
                }   
            }
                if(parseInt(totaladult) == 2){
                   
                    var totalnooftravellers_ = parseInt(totaladult) + parseInt(kidsbedtype1val) + parseInt(kidsbedtype2val)  ;
                    if(totalnooftravellers_ >3){
                        $('.noofpaxmsg').show().html('Please select different bed type for childs').css({'color': 'red'});
                        return false;
                    }
                }
                if(parseInt(totaladult) == 1){
                    var totalnooftravellers1_ = 1 + parseInt(kidsbedtype1val) + parseInt(kidsbedtype2val)  ;
                    if(totalnooftravellers1_ > 2){
                        $('.noofpaxmsg').show().html('Please select different bed type for childs ').css({'color': 'red'});
                        return false;
                    }
                }
    
        }

            $('.msgs').hide().html('');
            $.ajax({url: SITEURL + 'gtxwebservices/send-enquiry/post', type: 'POST', data: data, dataType: 'json', beforeSend: function () {
                    $('.sending1').html('<i class="ace-icon fa fa-spinner fa-spin orange bigger-125"></i> Getting Cost...').attr('disabled', 'disabled');
                }, success: function (result) {

                    if ((result.status == 'success') && (result.availability == true)) {
                        $('.package_rate_enquiry .smsg1').show().html("Thanks! Your enquiry submitted successfully. Soon our expert will contact you.").css({'color': '#5cb85c'});
                        var priceTax = result.addtional['GSTAmount']; // Tax
                        var sumRoomWise = result.addtional['MyCost']; // TotalPrice
                        var priceGT = result.addtional['AmountWithGST']; // GrandTotal
                        if (priceTax == 0) {
                            $('#totalBasicCostTR , #totalTaxCostTR').hide();
                            $('#GSTI').show().html('GST (Included)');
                        } else {
                            $('#totalBasicCostTR , #totalTaxCostTR').show();
                            $('#GSTI').hide().html('');
                        }
                        var priceTaxFormatted, priceBCFormatted, priceGTFormatted = '';
                        priceTaxFormatted = ptMoneyFormatINR(priceTax, 2, null);
                        priceBCFormatted = ptMoneyFormatINR(sumRoomWise, 2, null);
                        priceGTFormatted = ptMoneyFormatINR((priceGT), 2, null);
                        $('#totalBasicCost').html(priceBCFormatted);
                        $('#totalTaxCost').html(priceTaxFormatted);
                        $('#totalGrandCost').html(priceGTFormatted);
                        $('#tableCost').slideDown();
                        console.log(result.ProposalId);
                        if(result.ProposalId != ''){
                            $('.sending1').hide();
                            var ProposalId = atob(result.ProposalId);
                            $('#continueTOPayVal').val(ProposalId);
                            var code = btoa(result.url);
                            var mRedirectUrl = SITEURL+'detail/index/viewproposal/code/'+code;
//                            $('#continueTOPayAppend').append( '<a class="continueTOPay btn btn-danger" href="'+mRedirectUrl+'" target="_blank">Continue to Pay</a>');
                        }
                    }
                    else if ((result.status == 'success') && (result.availability == "false")) {
                        $('.smsg1').show().html("Thanks! Your enquiry submitted successfully. Soon our expert will contact you.").css({'color': '#5cb85c'});
                    }
                    else if ((result.status == false) && (result.availability == "false")) {
                        $('.package_rate_enquiry .smsg1').show().html('Sorry, This package is not available on selected date.\n\ But we will revert back with suitable suggestions.').css({'color': '#5cb85c'});
                    }
                    else if((result.status == false) && (result.redirect == true)){
                       $('.smsg1').show().html("You are not login.").css({'color': '#5cb85c'});
                       window.location.href = SITEURL;
                    }
                    else {
                        $('.smsg1').show().html('Thanks! Your enquiry submitted successfully.\n\ We will revert back with suitable suggestions.').css({'color': '#5cb85c'});
                    }

                    $('.hidebutton').find('.sending1').hide();
                    $('.hidebutton').find('.class-modify-enquiry').show();

                }, error: function (result) {
                    $('.smsg1').show().html('The Rates are not available for the requested date. Our Staff will contact you.').css({'color': 'green'});
                    $('.sending1').hide();
                    $('.hidebutton').find('.sending1').hide();
                    $('.hidebutton').find('.class-modify-enquiry').show();
                }});
        }
    });

    $('.addmore').click(function () {
        var count = 1;
        var room = 2;
        count = parseInt($('#itinerary_inputs').val());
        room = parseInt($('#itinerary_rooms').val());
        CONST_PACKAGE_TRAVELER_MAX_ROOM = parseInt(CONST_PACKAGE_TRAVELER_MAX_ROOM);
        if (room > CONST_PACKAGE_TRAVELER_MAX_ROOM) {
            alert("Can not add more than " + CONST_PACKAGE_TRAVELER_MAX_ROOM + " rooms.");
            return false;
        }
        $('.room-rows-container').append('<div class="col-lg-12 room-row inserted-room-row">' + '<div class="row">' + '<div class="col-lg-12 colspan-booking">' + '<div class="table-bordered"></div>' + '<label >Room ' + room + ' </label><button style="right:15px;" type="button" class="pull-right rounded text-danger removeit" aria-hidden="true" title="Remove Room">x</button>' + ' <input type="hidden" name="room[]" value="' + room + '" />' + '</div>' + '  <div class="cl"></div>' + '<div class="col-md-2 form-group">' + '<label class="Lable">Adults(+12Yrs)</label>' + '<select name="adult[]" id="traveler_adult_' + room + '"  class="traveler_adult form-control" onchange="checkTravellers(this ,' + room + ');">' + '<option value="1"> 1</option>' + '<option value="2"> 2</option>' + '<option value="3"> 3</option>' + '</select>' + '  </div>' + '<div class="col-md-2 form-group" style="display: none" id="room-row-adult-bedtype-' + room + '"><label class="Lable">Adult Bedtype</label><select name="adult_bed_type[]" class="traveler_child form-control" onchange="checkAdultBedType(this);" id="traveler_adult_bedtype_'+room+'"><option value="">Select</option><option value="withoutbed">Without Bed</option><option value="extrabed">With Bed</option></select></div><div class="col-md-2 form-group traveler_kids_div_'+room+'">' + '<label class="Lable">Kids(2 - 12Yrs)</label>' + '<select name="child[]" class="traveler_child form-control" id="traveler_kids_' + room + '" onchange="checkTravellers(this,' + room + ');">' + '<option value="0"> 0</option>' + '<option value="1"> 1</option>' + '<option value="2"> 2</option>' + '</select>' + ' </div>' + '<div class="col-md-2 form-group room-row-child-bedtype-' + room + '" style="display: none" id="room-row-child-bedtype-' + room + '-1"><label class="Lable">Child 1 Bedtype</label><select name="child1_bed_type[]" class="traveler_child form-control" id="selectroom-row-child-bedtype-'+room+'-1" onchange="checkAdultBedType(this);"><option value="">Select</option><option value="withoutbed">Without Bed</option><option value="extrabed">With Bed</option><option value="none">None</option></select></div><div class="col-md-2 form-group room-row-child-bedtype-' + room + '" style="display: none" id="room-row-child-bedtype-' + room + '-2"><label class="Lable">Child 2 Bedtype</label><select name="child2_bed_type[]" class="traveler_child form-control" id="selectroom-row-child-bedtype-'+room+'-2" onchange="checkAdultBedType(this);"><option value="">Select</option><option value="withoutbed">Without Bed</option><option value="extrabed">With Bed</option><option value="none">None</option></select></div> <div class="col-md-2 form-group" id="divtraveler_infant_'+room+'">' + '<label class="Lable" >Infant(0 - 2Yrs)</label>' + '<select name="infant[]" class="traveler_infant form-control" id="traveler_infant_' + room + '" onchange="checkTravellers(this,' + room + ');">' + '<option value="0"> 0</option>' + '<option value="1"> 1</option>' + '<option value="2"> 2</option>' + '<option value="3"> 3</option>' + '<option value="4"> 4</option>' + '</select>' + '</div>' + '</div>');
        $('#itinerary_inputs').val(parseInt(count) + 1);
        $('#itinerary_rooms').val(parseInt(room) + 1);
    });

    $("#sendEnquiry").on('submit', function (e) {
        e.preventDefault();
        var data = $(this).serialize();
//        if ($.trim($('#sendEnquiry input[name="Destination"]').val()) === '') {
//            $('#msg').html('Please choose your desired destination.').css('color', 'red').fadeIn().delay(3000).fadeOut();
//            $('#sendEnquiry input[name="Destination"]').focus();
//            return false;
//        }
        if ($.trim($('#sendEnquiry input[name="inputName"]').val()) === '') {
            $('#msg').html('Please enter your name').css('color', 'red').fadeIn().delay(3000).fadeOut();
            $('#sendEnquiry input[name="inputName"]').focus();
            return false;
        }
        if ($.trim($('#sendEnquiry input[name="inputPhone"]').val()) === '') {
            $('#msg').html('Please enter your contact number').css('color', 'red').fadeIn().delay(3000).fadeOut();
            $('#sendEnquiry input[name="inputPhone"]').focus();
            return false;
        }
        if ($.trim($('#sendEnquiry input[name="inputEmail"]').val()) === '') {
            $('#msg').html('Please enter your Email-Id').css('color', 'red').fadeIn().delay(3000).fadeOut();
            $('#sendEnquiry input[name="inputEmail"]').focus();
            return false;
        }
        if ($('#optionscheckbox1').prop('checked') === false)
        {
           $('#msg').html('Please accept and authorize for contact you?').css('color', 'red').fadeIn().delay(3000).fadeOut();
            $('#optionscheckbox1').focus();
            return false;
        }
                          
        $('.sendEnquiry').attr('disabled', 'disabled');
        $.ajax({
            url: SITEURL + 'tours/index/send-query-details',
            type: 'POST',
            data: data,
            dataType: 'json',
            beforeSend: function () {
                $('.sendEnquiry').val('Please wait...');
            },
            success: function (result) {
                if (result.status === true) {
                    $('#msg').html(result.message).css('color', 'green').fadeIn().delay(3000).fadeOut();
                } else {
                    $('#msg').html(result.message).css('color', 'red').fadeIn().delay(3000).fadeOut();
                }
                 $('.sendEnquiry').val('Send Enquiry').attr('disabled', false);
            },
            error: function () {
                $('.sendEnquiry').removeAttr('disabled', 'disabled');
                $('.sendEnquiry').html('Send Enquiry');
                alert('Oops unable to connect with server!!');
            }
        });
    });

    $("#subscribe-form").submit(function () {
        var email = $.trim($("#send_email").val());
        if (email.length == 0)
        {
            $("#eml").css('color', 'red').html("E-Mail Can't be Empty").fadeIn().delay(3000).fadeOut();
            return false;
        }
        else {
            var at = email.indexOf("@");
            var dot = email.lastIndexOf(".");
            if (at < 3 || dot - at < 3 || email.length - (dot) < 3)
            {
                $("#eml").css('color', 'red').html("Enter valid email address").fadeIn().delay(3000).fadeOut();
                return false;
            }
        }
        $.ajax({
            url: SITEURL + 'index/save-letter',
            type: 'POST',
            data: {email: email},
            dataType: 'json',
            beforeSend: function () {
                //            alert("hello");
            },
            success: function (result) {
                alert(result.msg);
                return false;
            },
            error: function () {
                alert('Oops unable to connect with server!!');
                return false;
            }
        });
        return false;
    });
});

function checkTravellers(that, rownumber) {

    var div = $(that).closest('.room-rows-container');
    var totalpax = 0;
    var adults = div.find('#traveler_adult_' + rownumber).val();
    var adultsbedtype = div.find('#traveler_adult_bedtype_' + rownumber).val();
    var kids = div.find('#traveler_kids_' + rownumber).val();
    var kidsbedtype1 = div.find('#selectroom-row-child-bedtype-'+rownumber+'-1' ).val();
    var kidsbedtype2 = div.find('#selectroom-row-child-bedtype-'+rownumber+'-2' ).val();
    var infant = div.find('#traveler_infant_' + rownumber).val();
    totalpax = parseInt(adults) + parseInt(kids) + parseInt(infant);

    if (parseInt(totalpax) > 4) {
        alert("Total pax can not be more than 4 in a room. Please modify travellers in room " + rownumber);

        if ($(that).hasClass('traveler_adult'))
            $(that).val(1); // reset the current value
        else
            $(that).val(0); // reset the current value

        return false;
    }
    else {
         div.find('#divtraveler_infant_' + rownumber).show(); // hide all select box
        if (parseInt(adults) == 3) {
            div.find('#room-row-adult-bedtype-' + rownumber).hide();
            if(adultsbedtype == ''){
                div.find('#room-row-adult-bedtype-' + rownumber + ' select').val('extrabed');
            }else{
                div.find('#room-row-adult-bedtype-' + rownumber + ' select').val(adultsbedtype);
            }
            div.find('.room-row-child-bedtype-' + rownumber).hide(); // hide all select box
            div.find('.traveler_kids_div_' + rownumber).hide(); // hide all select box
            div.find('#divtraveler_infant_' + rownumber).hide(); // hide all select box
        } else {
           
            div.find('.room-row-child-bedtype-' + rownumber).show(); // hide all select box
            div.find('.traveler_kids_div_' + rownumber).show(); // hide all select box
            div.find('#room-row-adult-bedtype-' + rownumber).hide();
            div.find('#room-row-adult-bedtype-' + rownumber + ' select').val('');
          
            
        }

        div.find('.room-row-child-bedtype-' + rownumber).hide(); // hide all select box
        
        if (parseInt(kids) > 0) {
            for (var i = 1; i <= parseInt(kids); i++) {
                div.find('#room-row-child-bedtype-' + rownumber + "-" + i).show();
                if(i==1){
                if(kidsbedtype1 == ''){
                    div.find('#room-row-child-bedtype-'+ rownumber+ "-"+ i + ' select').val('extrabed');
                }else{
                    div.find('#room-row-child-bedtype-'+ rownumber+ "-"+ i + ' select').val(kidsbedtype1);
                }
            }else{
                if(kidsbedtype2 == ''){
                    div.find('#room-row-child-bedtype-'+ rownumber+ "-"+ i + ' select').val('extrabed');
                }else{
                    div.find('#room-row-child-bedtype-'+ rownumber+ "-"+ i + ' select').val(kidsbedtype2);
                } 
            }
                 
            }
        }
    }

}

function trimcontent(showChar) {
    var moretext = "+More";
    var lesstext = "-Less";

    $('.more').each(function () {
        var content = $(this).html();

        if (content.length > showChar) {

            var c = content.substr(0, showChar);
            var h = content.substr(showChar, content.length - showChar);

            var html = '<span class="defaultcontent" >' + c + '</span><span class="morecontent">' + h + '</span>\n\
                &nbsp;&nbsp;&nbsp;<a href="javascript:void();" class="morelink btn-link btnmore">' + moretext + '</a>';
            $(this).html(html);
        }
    });

    $(".morelink").click(function () {
        if ($(this).hasClass("less")) {
            $(this).removeClass("less").html(moretext);
        } else {
            $(this).addClass("less").html(lesstext);
        }
//        $(this).parent().prev().toggle();
        $(this).prev().toggle();
        return false;
    });

    $('span.morecontent').hide();
    $('.defaultcontent , .morecontent').css({'font-size': '12px', 'text-transform': 'none', 'font-weight': 'normal'});
}

function ptMoneyFormatINR(n, prec, currSign) {
    if (prec == null)
        prec = 2;
    var n = ('' + parseFloat(n).toFixed(prec).toString()).split('.');
    var num = n[0];
    var dec = n[1];
    var r, s, t;
    if (num.length > 3) {
        s = num.length % 3;
        if (s) {
            t = num.substring(0, s);
            num = t + num.substring(s).replace(/(\d{3})/g, ",$1");
        } else {
            num = num.substring(s).replace(/(\d{3})/g, ",$1").substring(1);
        }
    }
//    return(currSign == null ? "" : currSign + "") + num + '.' + dec;
    return(currSign == null ? "" : currSign + "") + num;
}

function modify_enquiry(that)
{
    $(that).hide();
    $(that).prev().html('Check Rate &amp; Send Enquiry').attr('disabled', false).show();
    $('.continueTOPay').hide();
}

function checkAdultBedType(that){
    if($(that).val() == ''){
        alert('Please select bed type');
        $(that).val('extrabed');
        return false;
    }
}


function changeContentByCategory(cname,desname,packageId,catId,gtxId,tourType,mp){
    
//     var data = SITEURL + 'detail/'+cname+'/'+desname+'-'+packageId+'-'+catId+'-'+gtxId+'-'+tourType+'-'+mp+'.html';
    var countryname = cname;
    var name = desname+'-'+packageId+'-'+catId+'-'+gtxId+'-'+tourType+'-'+mp+'.html';
    
   $.ajax({
            url: SITEURL + 'detail/index/index-ajax-data',
            type: 'POST',
            data: {countryname: countryname,name: name},
            dataType: 'html',
            beforeSend: function () {
                //            alert("hello");
            },
            success: function (result) {
                $('#changeContentByajax').html('');
                $('#changeContentByajax').html(result);
                return false;
            },
            error: function () {
                alert('Oops unable to connect with server!!');
                return false;
            }
        });
}


$(document).ready(function()
{
	"use strict";

	/* 

	1. Vars and Inits

	*/

	var menu = $('.menu');
	var menuActive = false;
	var header = $('.header-scroll');
	var searchActive = false;

	setHeader();

	$(window).on('resize', function()
	{
		setHeader();
	});

	$(document).on('scroll', function()
	{
		setHeader();
	});

	initHomeSlider();
	initMenu();
	initSearch();
	initCtaSlider();
	initTestSlider();
	initSearchForm();

	/* 

	2. Set Header

	*/

	function setHeader()
	{
		if(window.innerWidth < 992)
		{
			if($(window).scrollTop() > 100)
			{
				header.addClass('scrolled');
			}
			else
			{
				header.removeClass('scrolled');
			}
		}
		else
		{
			if($(window).scrollTop() > 100)
			{
				header.addClass('scrolled');
			}
			else
			{
				header.removeClass('scrolled');
			}
		}
		if(window.innerWidth > 991 && menuActive)
		{
			closeMenu();
		}
	}

	/* 

	3. Init Home Slider

	*/

	function initHomeSlider()
	{
		if($('.home_slider').length)
		{
			var homeSlider = $('.home_slider');

			homeSlider.owlCarousel(
			{
				items:1,
				loop:true,
				autoplay:false,
				smartSpeed:1200,
				dotsContainer:'main_slider_custom_dots'
			});

			/* Custom nav events */
			if($('.home_slider_prev').length)
			{
				var prev = $('.home_slider_prev');

				prev.on('click', function()
				{
					homeSlider.trigger('prev.owl.carousel');
				});
			}

			if($('.home_slider_next').length)
			{
				var next = $('.home_slider_next');

				next.on('click', function()
				{
					homeSlider.trigger('next.owl.carousel');
				});
			}

			/* Custom dots events */
			if($('.home_slider_custom_dot').length)
			{
				$('.home_slider_custom_dot').on('click', function()
				{
					$('.home_slider_custom_dot').removeClass('active');
					$(this).addClass('active');
					homeSlider.trigger('to.owl.carousel', [$(this).index(), 300]);
				});
			}

			/* Change active class for dots when slide changes by nav or touch */
			homeSlider.on('changed.owl.carousel', function(event)
			{
				$('.home_slider_custom_dot').removeClass('active');
				$('.home_slider_custom_dots li').eq(event.page.index).addClass('active');
			});	

			// add animate.css class(es) to the elements to be animated
			function setAnimation ( _elem, _InOut )
			{
				// Store all animationend event name in a string.
				// cf animate.css documentation
				var animationEndEvent = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';

				_elem.each ( function ()
				{
					var $elem = $(this);
					var $animationType = 'animated ' + $elem.data( 'animation-' + _InOut );

					$elem.addClass($animationType).one(animationEndEvent, function ()
					{
						$elem.removeClass($animationType); // remove animate.css Class at the end of the animations
					});
				});
			}

			// Fired before current slide change
			homeSlider.on('change.owl.carousel', function(event)
			{
				var $currentItem = $('.home_slider_item', homeSlider).eq(event.item.index);
				var $elemsToanim = $currentItem.find("[data-animation-out]");
				setAnimation ($elemsToanim, 'out');
			});

			// Fired after current slide has been changed
			homeSlider.on('changed.owl.carousel', function(event)
			{
				var $currentItem = $('.home_slider_item', homeSlider).eq(event.item.index);
				var $elemsToanim = $currentItem.find("[data-animation-in]");
				setAnimation ($elemsToanim, 'in');
			})
		}
	}

	/* 

	4. Init Menu

	*/

	function initMenu()
	{
		if($('.hamburger').length && $('.menu').length)
		{
			var hamb = $('.hamburger');
			var close = $('.menu_close_container');

			hamb.on('click', function()
			{
				if(!menuActive)
				{
					openMenu();
				}
				else
				{
					closeMenu();
				}
			});

			close.on('click', function()
			{
				if(!menuActive)
				{
					openMenu();
				}
				else
				{
					closeMenu();
				}
			});

	
		}
	}

	function openMenu()
	{
		menu.addClass('active');
		menuActive = true;
	}

	function closeMenu()
	{
		menu.removeClass('active');
		menuActive = false;
	}

	/* 

	5. Init Search

	*/

	function initSearch()
	{
		if($('.search_tab').length)
		{
			$('.search_tab').on('click', function()
			{
				$('.search_tab').removeClass('active');
				$(this).addClass('active');
				var clickedIndex = $('.search_tab').index(this);

				var panels = $('.search_panel');
				panels.removeClass('active');
				$(panels[clickedIndex]).addClass('active');
			});
		}
	}

	/* 

	6. Init CTA Slider

	*/

	function initCtaSlider()
	{
		if($('.cta_slider').length)
		{
			var ctaSlider = $('.cta_slider');

			ctaSlider.owlCarousel(
			{
				items:1,
				loop:true,
				autoplay:false,
				nav:false,
				dots:false,
				smartSpeed:1200
			});

			/* Custom nav events */
			if($('.cta_slider_prev').length)
			{
				var prev = $('.cta_slider_prev');

				prev.on('click', function()
				{
					ctaSlider.trigger('prev.owl.carousel');
				});
			}

			if($('.cta_slider_next').length)
			{
				var next = $('.cta_slider_next');

				next.on('click', function()
				{
					ctaSlider.trigger('next.owl.carousel');
				});
			}
		}
	}

	/* 

	7. Init Testimonials Slider

	*/

	function initTestSlider()
	{
		if($('.test_slider').length)
		{
			var testSlider = $('.test_slider');

			testSlider.owlCarousel(
			{
				loop:true,
				nav:false,
				dots:false,
				smartSpeed:1200,
				margin:30,
				responsive:
				{
					0:{items:1},
					480:{items:1},
					768:{items:2},
					992:{items:3}
				}
			});

			/* Custom nav events */
			if($('.test_slider_prev').length)
			{
				var prev = $('.test_slider_prev');

				prev.on('click', function()
				{
					testSlider.trigger('prev.owl.carousel');
				});
			}

			if($('.test_slider_next').length)
			{
				var next = $('.test_slider_next');

				next.on('click', function()
				{
					testSlider.trigger('next.owl.carousel');
				});
			}
		}
	}

	/* 

	8. Init Search Form

	*/

	function initSearchForm()
	{
		if($('.search_form').length)
		{
			var searchForm = $('.search_form');
			var searchInput = $('.search_content_input');
			var searchButton = $('.content_search');

			searchButton.on('click', function(event)
			{
				event.stopPropagation();

				if(!searchActive)
				{
					searchForm.addClass('active');
					searchActive = true;

					$(document).one('click', function closeForm(e)
					{
						if($(e.target).hasClass('search_content_input'))
						{
							$(document).one('click', closeForm);
						}
						else
						{
							searchForm.removeClass('active');
							searchActive = false;
						}
					});
				}
				else
				{
					searchForm.removeClass('active');
					searchActive = false;
				}
			});	
		}
	}
});