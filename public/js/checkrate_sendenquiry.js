$(document).ready(function () {

    $(document).on('click', '.mSendEnuiryCheckRatePackageClose', function () {
            $("#expandSendEnuiryCheckRatePackage").slideUp('slow');

    });
 $(document).on('click', '.mSendEnuiryCheckRatePackage', function () {
      $("#expandSendEnuiryCheckRatePackage").slideDown('slow');
     
      if(dvc == 'd'){
           $('.booking-price-single').addClass('booking-price-single-addbyjs');
      }
      
        var PkgSysId = $(this).attr('data-rvs');
        var PackageType = $('#packagetype_' + PkgSysId).val();
        var GTXPkgId = $('#gtxpackage_' + PkgSysId).val();
        var hotelcategoryid = $('#hotelcategoryid_' + PkgSysId).val();
        var packagetpid = $('#packagetpid_' + PkgSysId).val();
        var tourtype = $('#tourtype_' + PkgSysId).val();
        var packagenamemodal = $('#packagename_' + PkgSysId).val();
        var mealplantype = $('#mealplantype_' + PkgSysId).val();
        var packagedesname = $('#packagedesname_' + PkgSysId).val();
        var priceTripleOccVal = $('#priceTripleOccVal' + hotelcategoryid).val();
        var priceDoubleOccVal = $('#priceDoubleOccVal' + hotelcategoryid).val();
        var priceSingleOccVal = $('#priceSingleOccVal' + hotelcategoryid).val();
        var priceExtraBedVal = $('#priceExtraBedVal' + hotelcategoryid).val();
        var priceWithoutBedVal = $('#priceWithoutBedVal' + hotelcategoryid).val();
        
        $('.msgs').hide().html('');
        $('.enqmsgs1').hide().html('');
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
        $('.package_rate_enquiry .enqmsgs1').html('');
        $('.inserted-room-row').remove();
        $('.addmore').show();
        $('.package_rate_enquiry #traveler_adult_1').val(1);
        $('.package_rate_enquiry #traveler_kids_1 , .package_rate_enquiry #traveler_infant_1').val(0);
        $('#itinerary_inputs').val(1);
        $('#itinerary_rooms').val(2);
        $('.hidebutton').find('.class-modify-enquiry').hide();

        $('.Categorybgcolor').css({'background':'url("public/images/radio-button-min.png") center no-repeat'});
        $('#Categorybgcolor'+parseInt(hotelcategoryid)).css({'background':'url("public/images/radio-button-checked-min.png") center no-repeat'});
    
        $('#putPriceDoubleOccVal').html(priceDoubleOccVal);
        $('#putPriceTripleOccVal').html(priceTripleOccVal);
        $('#putPriceSingleOccVal').html(priceSingleOccVal);
        $('#putPriceExtraBedVal').html(priceExtraBedVal);
        $('#putPriceWithoutBedVal').html(priceWithoutBedVal);
        if(priceTripleOccVal == 0){$('#hideIfpriceIsZeroTriple').hide();}
        if(priceDoubleOccVal == 0){$('#hideIfpriceIsZeroTwin').hide();}
        if(priceSingleOccVal == 0){ $('#hideIfpriceIsZeroSingle').hide();}
        if(priceExtraBedVal == 0){$('#hideIfpriceIsZeroChild').hide();}
        
        $('.submitSendEnquiryCheckRateBtn').attr('disabled',true);
        $('.modifySendEnquiryCheckRateBtn').hide();
        $('.continueTOPay').hide();
    });
    
    
    
   $(".submitSendEnquiryCheckRateBtn").on('click', function (e) {
       $('.submitSendEnquiryCheckRate').submit();
   })
   $(".submitSendEnquiryCheckRate").on('submit', function (e) {
        e.preventDefault();
        var data = $(this).serialize();
        var EmailReg = new RegExp(/^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/);
        
        $('.emailEnqMsg').html('');
        $('.mobileEnqMsg').html('');
        $('.enqmsgs').html('');
        if ($('.submitSendEnquiryCheckRate input[name="email"]').val() == '') {
            $('.emailEnqMsg').show().html('Please enter your email address').css({'color':'red'});
            $('.submitSendEnquiryCheckRate input[name="email"]').focus();
            return false;
        }
        if (!EmailReg.test($('.submitSendEnquiryCheckRate input[name="email"]').val())) {
            $('.emailEnqMsg').show().html('Please enter valid email address!!!').css({'color':'red'});;
            $('.submitSendEnquiryCheckRate input[name="email"]').focus();
            return false;
        }
        if ($('.submitSendEnquiryCheckRate input[name="mobile"]').val() == '') {
            $('.mobileEnqMsg').show().html('Please enter your mobile number').css({'color':'red'});;
            $('.submitSendEnquiryCheckRate input[name="mobile"]').focus();
            return false;
        }
        if ($('.submitSendEnquiryCheckRate input[name="mobile"]').val().length != '10') {
            $('.mobileEnqMsg').show().html('Mobile should be 10 digit?').css({'color':'red'});;
            $('.submitSendEnquiryCheckRate input[name="mobile"]').focus();
            return false;
        }
        if (!$.isNumeric($('.submitSendEnquiryCheckRate input[name="mobile"]').val())) {
            $('.mobileEnqMsg').show().html('Mobile should be numeric!!').css({'color':'red'});;
            $('.submitSendEnquiryCheckRate input[name="mobile"]').focus();
            return false;
        }
        if ($('.submitSendEnquiryCheckRate input[name="date"]').val() == '') {
            $('.travelEnqMsg').show().html('Please enter travel date').css({'color':'red'});
            $('.submitSendEnquiryCheckRate input[name="date"]').focus();
            return false;
        }
         if ($('#pick_up_locations').val() == '') {
            $('.locationEnqMsg').show().html('Please select Location').css({'color':'red'});
            $('#pick_up_locations').focus();
            return false;
        }
        
      
         $('.enqmsgs').hide().html('');
        $.ajax({url: SITEURL + 'gtxwebservices/send-enquiry/post', type: 'POST', data: data, dataType: 'json', beforeSend: function () {
                    $('.submitSendEnquiryCheckRateBtn').html('<i class="ace-icon fa fa-spinner fa-spin orange bigger-125"></i> Getting Cost...').attr('disabled', 'disabled');
                }, success: function (result) {
                    $('.submitSendEnquiryCheckRateBtn').html('Check Rate &amp; Continue');
                    if ((result.status == 'success') && (result.availability == true)) {
                        $('.package_rate_enquiry .enqmsgs1').show().html("Thanks! Your enquiry submitted successfully. Soon our expert will contact you.").css({'color': '#5cb85c'});
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
                        $('#getEnquiryPriceDetail').show().html('<div class="col-md-12 table-responsive no-margin">'+
                            '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table book-collps-table no-margin">'+
                                '<tr><tr><td colspan="2" align="left" class="alert alert-info"><strong class="table-large-text">Basic Cost : INR '+priceBCFormatted+'</strong></td>'+
                                    '<td  align="center" class="alert alert-info"><strong class="table-large-text">GST : INR '+priceTaxFormatted+'</strong></td>'+
                                    '<td  align="right" class="alert alert-info"><strong class="table-large-text">Grand Total : INR '+priceGTFormatted+'</strong></td>'+
                                '</tr></table></div>');
                        $('.submitSendEnquiryCheckRateBtn').hide();
                        
                       var ProposalId = atob(result.ProposalId);
                       $('#continueTOPayVal').val(ProposalId);
                       var code = btoa(result.url);
                       var mRedirectUrl = SITEURL+'detail/index/viewproposal/code/'+code;
                       $('#continueTOPayAppend').append( '<a class="continueTOPay btn btn-danger" href="'+mRedirectUrl+'" target="_blank">Continue to Pay</a>');
                      
                    }
                    else if ((result.status == 'success') && (result.availability == "false")) {
                        $('.enqmsgs1').show().html("Thanks! Your enquiry submitted successfully. Soon our expert will contact you.").css({'color': '#5cb85c'});
                 $('.submitSendEnquiryCheckRateBtn').html('Check Rate &amp; Continue');    
                }
                    else if ((result.status == false) && (result.availability == "false")) {
                        $('.package_rate_enquiry .enqmsgs1').show().html('Sorry, This package is not available on selected date.\n\ But we will revert back with suitable suggestions.').css({'color': '#5cb85c'});
                    }
                    else if((result.status == false) && (result.redirect == true)){
                       $('.enqmsgs1').show().html("You are not login.").css({'color': '#5cb85c'});
                       window.location.href = SITEURL;
                    }
                    else if((result.status == 'error')){
                       $('.enqmsgs1').show().html(result.message).css({'color': 'red'});
                       $('.submitSendEnquiryCheckRateBtn').html('Check Rate &amp; Continue').attr('disabled', false);;
                    }
                    else {
                        $('.enqmsgs1').show().html('Thanks! Your enquiry submitted successfully.\n\ We will revert back with suitable suggestions.').css({'color': '#5cb85c'});
                 $('.submitSendEnquiryCheckRateBtn').html('Check Rate &amp; Continue');    
                }

                    $('.hidebutton').find('.sending1').hide();
                    $('.hidebutton').find('.class-modify-enquiry').show();
                    $('.modifySendEnquiryCheckRateBtn').show();
                }, error: function (result) {
                    $('.enqmsgs1').show().html('The Rates are not available for the requested date. Our Staff will contact you.').css({'color': 'green'});
                    $('.sending1').hide();
                     $('.submitSendEnquiryCheckRateBtn').html('Check Rate &amp; Continue');
                    $('.hidebutton').find('.sending1').hide();
                    $('.hidebutton').find('.class-modify-enquiry').show();
                    $('.modifySendEnquiryCheckRateBtn').show();
                }});
   })



});

