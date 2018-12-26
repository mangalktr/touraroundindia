$(document).ready(function () {

    $("#auth_login").on('submit', function (e) {
        e.preventDefault();
        var data = $(this).serialize();
        if ($('#auth_login input[name="userName"]').val() == '') {
            $('.login_msg').html('Please enter your email id');
            $('#auth_login input[name="userName"]').focus();
            return false;
        }
        if ($('#auth_login input[name="userPassword"]').val() == '') {
            $('.login_msg').html('Please enter your password');
            $('#auth_login input[name="userPassword"]').focus();
            return false;
        }
       
    
        $('.loging').attr('disabled', 'disabled');
        $.ajax({
            url: SITEURL + 'customer/agencycustomerlogin',
            type: 'POST', data: data, dataType: 'json',
            beforeSend: function () {
                $('.loging').html('<i class="ace-icon fa fa-spinner fa-spin orange bigger-125"></i> Authentication...');
            },
            success: function (result) {
                if (result.status) {
//                    $('.login_msg').html(result.message);
                    $('#AgencySysId_').val(result.AgencySysId);
                    $('#CustomerSysId').val(result.CustomerSysId);
                    $('#EmailId').val(result.EmailId);
                    $('#FirstName').val(result.FirstName);
                    $('#LastName').val(result.LastName);
                    $('#Password_cu').val(result.Password);
                    $('#Contacts').val(result.Contacts);
                   
                    $('#auth_login_submit').submit();
                } else {
                    $('.loging').html('Login');
                    $('.loging').removeAttr('disabled', 'disabled');
                    $('.login_msg').html(result.message);
                    return false;
                }
            },
            error: function () {
                $('.loging').html('Login');
                $('.loging').removeAttr('disabled', 'disabled');
                alert('Oops unable to connect with server!!');
            }});
    });

    $("#forgot_password").on('submit', function (e) {
        e.preventDefault();
        var data = $(this).serialize();
        if ($('#forgot_password input[name="forget"]').val() == '') {
            $('.forgot_msg').html('Please enter your email id').fadeIn().delay(1000).fadeOut();
            $('#forgot_password input[name="forget"]').focus();
            return false;
        }
        $('.forgot').attr('disabled', 'disabled');
        $.ajax({
            url: SITEURL + 'customer/forgotpassword',
            type: 'POST', data: data, dataType: 'json',
            beforeSend: function () {
                $('.forgot').html('<i class="ace-icon fa fa-spinner fa-spin orange bigger-125"></i> Please wait..');
            },
            success: function (result) {
                console.log(result);
                if (result.status) {
                    $('#successmsgforget').html("<br><br><center>Your password reset link has been sent to the email address you have provided. This link will expire within 10 minutes.</center><br><br>").css('color','green');
                    //$('.hidedivs').hide(); 
                } else {
                    $('.forgot').html('Submit');
                    $('.forgot').removeAttr('disabled', 'disabled');
                    $('.forgot_msg').html(result.message);
                    return false;
                }
            },
            error: function () {
                $('.forgot').html('Submit');
                $('.forgot').removeAttr('disabled', 'disabled');
                alert('Oops unable to connect with server!!');
            }});
    });

    $("#reset_password").on('submit', function (e) {
        e.preventDefault();
        var data = $(this).serialize();
        if ($('#reset_password input[name="npass"]').val() === '') {
            $('.passchange_msg').html('Please enter new password');
            $('#reset_password input[name="npass"]').focus();
            return false;
        }
        if ($('#reset_password input[name="copass"]').val() === '') {
            $('.passchange_msg').html('Please enter confirm password');
            $('#reset_password input[name="copass"]').focus();
            return false;
        }
        if ($('#reset_password input[name="npass"]').val() !== $('#reset_password input[name="copass"]').val()) {
            $('.passchange_msg').html('Confirm password does not match with new password');
            $('#reset_password input[name="copass"]').focus();
            return false;
        }
        $('.change').attr('disabled', 'disabled');
        $.ajax({url: SITEURL + 'customer/resetpassword',
            type: 'POST', data: data, dataType: 'json',
            beforeSend: function () {
                $('.change').html('<i class="ace-icon fa fa-spinner fa-spin orange bigger-125"></i> Please wait...');
            },
            success: function (result) {
                if (result.status) {
                    $('.change').html('Submit');
                    $('.change').removeAttr('disabled', 'disabled');
                    $('.passchange_msg').html(result.message);
                    window.location.href = SITEURL + "customer/unsetresetdata";
                } else {
                    $('.change').html('Submit');
                    $('.change').removeAttr('disabled', 'disabled');
                    $('.passchange_msg').html(result.message);
                    return false;
                }
            },
            error: function () {
                $('.change').html('Submit');
                $('.change').removeAttr('disabled', 'disabled');
                alert('Oops unable to connect with server!!');
            }});
    });

    $("#change_password").on('submit', function (e) {
        e.preventDefault();
        var data = $(this).serialize();
        if ($('#change_password input[name="cpass"]').val() === '') {
            $('.passchange_msg').html('Please enter old password').css('color', 'red').fadeIn().delay(4000).fadeOut();
            $('#change_password input[name="cpass"]').focus();
            return false;
        }
        if ($('#change_password input[name="npass"]').val() === '') {
            $('.passchange_msg').html('Please enter new password').css('color', 'red').fadeIn().delay(4000).fadeOut();
            $('#change_password input[name="npass"]').focus();
            return false;
        }
        if ($('#change_password input[name="copass"]').val() === '') {
            $('.passchange_msg').html('Please enter confirm password').css('color', 'red').fadeIn().delay(4000).fadeOut();
            $('#change_password input[name="copass"]').focus();
            return false;
        }
        if ($('#change_password input[name="npass"]').val() !== $('#change_password input[name="copass"]').val()) {
            $('.passchange_msg').html('Confirm password does not match with new password').css('color', 'red').fadeIn().delay(4000).fadeOut();
            $('#change_password input[name="copass"]').focus();
            return false;
        }
        $('.change').attr('disabled', 'disabled');
        $.ajax({url: SITEURL + 'customer/changepassword',
            type: 'POST', data: data, dataType: 'json',
            beforeSend: function () {
                $('.change').html('<i class="ace-icon fa fa-spinner fa-spin orange bigger-125"></i> Please wait...');
            },
            success: function (result) {
                if (result.status) {
                    $('.change').html('Update');
                    $('.change').removeAttr('disabled', 'disabled');
                    $('.passchange_msg').html(result.message).css('color', 'green').fadeIn().delay(5000).fadeOut();
//                    window.location.href = SITEURL + "customer/unsetdata";
                } else {
                    $('.change').html('Update');
                    $('.change').removeAttr('disabled', 'disabled');
                    $('.passchange_msg').html(result.message).css('color', 'red').fadeIn().delay(5000).fadeOut();
                    return false;
                }
            },
            error: function () {
                $('.change').html('Update');
                $('.change').removeAttr('disabled', 'disabled');
                alert('Oops unable to connect with server!!');
            }});
    });

    $("#update_profile").on('submit', function (e) {
        e.preventDefault();
        var data = $(this).serialize();
        //alert(SITEURL);
        if ($('#update_profile input[name="FirstName"]').val() === '') {
            $('.alrt_msg').html('Please enter your first name').fadeIn().fadeOut(5000);;
            $('#update_profile input[name="FirstName"]').focus();
            return false;
        }
        if ($('#update_profile input[name="LastName"]').val() === '') {
            $('.alrt_msg').html('Please enter your last name').fadeIn().fadeOut(5000);
            $('#update_profile input[name="LastName"]').focus();
            return false;
        }
        if ($('#update_profile input[name="contacts"]').val() === '') {
            $('.alrt_msg').html('Please enter your mobile number').fadeIn().fadeOut(5000);
            $('#update_profile input[name="contacts"]').focus();
            return false;
        }
        if (!$.isNumeric($('#update_profile input[name="contacts"]').val())) {
            $('.alrt_msg').html('Mobile should be numeric!!').fadeIn().fadeOut(5000);
            $('#update_profile input[name="contacts"]').focus();
            return false;
        }
        if ($('#update_profile input[name="contacts"]').val().length != '10') {
            $('.alrt_msg').html('Mobile should be 10 digit?').fadeIn().fadeOut(5000);
            $('#update_profile input[name="contacts"]').focus();
            return false;
        }
        if ($('#update_profile select[name="country"]').val() === '') {
            $('.alrt_msg').html('Please select country').fadeIn().fadeOut(5000);
            $('#update_profile select[name="country"]').focus();
            return false;
        }
        if ($('#update_profile select[name="city"]').val() === '') {
            $('.alrt_msg').html('Please select city').fadeIn().fadeOut(5000);
            $('#update_profile select[name="city"]').focus();
            return false;
        }
        $('.change').attr('disabled', 'disabled');
       $('.alrt_msg_success').html('');
       $('.alrt_msg').html('');
        $.ajax({
            url: SITEURL + 'customer/updateprofile',
            type: 'POST',
            data: data,
            dataType: 'json',
            beforeSend: function () {
                $('.change').html('<i class="ace-icon fa fa-spinner fa-spin orange bigger-125"></i> Please wait...');
            },
            success: function (result) {
                if (result.status) {
                    //Thank you message
                    $('.change').html('Update Profile');
                    $('.change').removeAttr('disabled', 'disabled');
                    $('.alrt_msg p').css('color', '#ff0000');
                    $('.alrt_msg_success').html(result.message).css('color', 'green').fadeIn().fadeOut(8000);
                } else {
                    // error response
                    $('.change').html('Update Profile');
                    $('.change').removeAttr('disabled', 'disabled');
                    $('.alrt_msg').html(result.message).fadeIn().fadeOut(8000);
                    return false;
                }
            },
            error: function (result) {
                $('.change').html('Update Profile');
                $('.change').removeAttr('disabled', 'disabled');
                alert('Oops unable to connect with server!!');
            }

        });
        //alert('fsdfds');return false;
    });

    $("#user_signup").on('submit', function (e) {
        e.preventDefault();
        var data = $(this).serialize();
        var regex = /^\+(?:[0-9] ?){1,4}[0-9]$/;

        if ($('#user_signup input[name="firstName"]').val() == '') {
            $('.signuperror_msg').html('Please enter your first name');
            $('#user_signup input[name="firstName"]').focus();
            return false;
        }
        if ($('#user_signup input[name="lastName"]').val() == '') {
            $('.signuperror_msg').html('Please enter your last name');
            $('#user_signup input[name="lastName"]').focus();
            return false;
        }

        if ($('#user_signup input[name="EmailId"]').val() == '') {
            $('.signuperror_msg').html('Please enter your Email Id');
            $('#user_signup input[name="EmailId"]').focus();
            return false;
        }
        if ($('#user_signup input[name="countrycode"]').val() == '') {
            $('.signuperror_msg').html('Please enter your country code');
            $('#user_signup input[name="countrycode"]').focus();
            return false;
        }
        if (regex.test($('#user_signup input[name="countrycode"]').val())) {
        } else {
            $('.signuperror_msg').html('Please enter valid country code with + symbol!');
            $('#user_signup input[name="countrycode"]').focus();
            return false;
        }
        if ($('#user_signup input[name="mobilenum"]').val() == '') {
            $('.signuperror_msg').html('Please enter your mobile number');
            $('#user_signup input[name="mobilenum"]').focus();
            return false;
        }

        if ($('#user_signup input[name="mobilenum"]').val().length != '10') {
            $('.signuperror_msg').html('Mobile number should be 10 digits');
            $('#user_signup input[name="mobilenum"]').focus();
            return false;
        }
        if ($('#user_signup input[name="password"]').val() == '') {
            $('.signuperror_msg').html('Please enter your password');
            $('#user_signup input[name="password"]').focus();
            return false;
        }
        if ($('#user_signup input[name="copassword"]').val() == '') {
            $('.signuperror_msg').html('Please enter your confirm password');
            $('#user_signup input[name="copassword"]').focus();
            return false;
        }
        $('.signup').attr('disabled', 'disabled');
        $.ajax({
            url: SITEURL + 'customer/usersignup',
            type: 'POST',
            data: data,
            dataType: 'json',
            beforeSend: function () {
                $('.signup').html('<i class="ace-icon fa fa-spinner fa-spin orange bigger-125"></i> Please wait...');
            },
            success: function (result) {
                //console.log(result);return false;
                if (result.status) {
                    $('.signupsuccess_msg').html(result.message);
                    $('#AgencySysId_').val(result.data.AgencySysId);
                    $('#CustomerSysId').val(result.data.CustomerSysId);
                    $('#EmailId').val(result.data.EmailId);
                    $('#FirstName').val(result.data.FirstName);
                    $('#LastName').val(result.data.LastName);
                    $('#Password_cu').val(result.data.Password);
                    $('#Contacts').val(result.data.Contacts);
                    $('#auth_login_submit').submit();
                } else {
                    $('.signup').html('Sign Up');
                    $('.signup').removeAttr('disabled', 'disabled');
                    $('.signuperror_msg').html(result.message);
                    return false;
                }
            },
            error: function () {
                $('.signup').html('Sign Up');
                $('.signup').removeAttr('disabled', 'disabled');
                alert('Oops unable to connect with server!!');
            }
        });
    });

    $("#agentLoginForm").on('submit', function (e) {
        e.preventDefault();
        var data = $(this).serialize();
        if ($('#agentLoginForm input[name="username"]').val() == '') {
            $('#msg').html('Please enter your user name').css('color', 'red').fadeIn().delay(3000).fadeOut();
            $('#agentLoginForm input[name="username"]').focus();
            return false;
        }
        if ($('#agentLoginForm input[name="pass"]').val() == '') {
            $('#msg').html('Please enter your password').css('color', 'red').fadeIn().delay(3000).fadeOut();
            $('#agentLoginForm input[name="pass"]').focus();
            return false;
        }
        $('.loging').attr('disabled', 'disabled');
        $.ajax({
            url: SITEURL + 'agentlogin/index/agentcustomerlogin',
            type: 'POST',
            data: data,
            dataType: 'json',
            beforeSend: function () {
                $('.loging').html('<i class="ace-icon fa fa-spinner fa-spin orange bigger-125"></i> Authentication...');
            },
            success: function (result) {

                if (result.status) {
                    $('#msg').html(result.message + '<br/>Please wait while we are redirecting you to home page').css('color', 'green').fadeIn().delay(5000).fadeOut();
                    $('.loging').hide();
                    $('#forgotpass_agent').hide();
                    $('#AgencySysId_B2B').val(result.AgencySysId);
                    $('#MasterAgencySysIdB2B').val(result.MasterAgencySysId);
                    $('#UserSysIdB2B').val(result.UserSysId);
                    $('#CustomerSysIdB2B').val(result.CustomerSysId);
                    $('#EmailIdB2B').val(result.EmailId);
                    $('#FirstNameB2B').val(result.FirstName);
                    $('#LastNameB2B').val(result.LastName);
                    $('#AgencyCodeB2B').val(result.AgencyCode);
                    $('#AgencyNameB2B').val(result.AgencyName);
                    $('#Password_cuB2B').val(result.Password);
                    $('#Contact_noB2B').val(result.ContactNo1);
                    $('#auth_login_B2B_submit').submit();
                } else {
                    $('.loging').html('Login');
                    $('.loging').removeAttr('disabled', 'disabled');
                    $('#msg').html(result.message).css('color', 'red').fadeIn().delay(3000).fadeOut();
//                    location.reload(true);
                    return false;
                }
            },
            error: function () {
                $('.loging').html('Login');
                $('.loging').removeAttr('disabled', 'disabled');
                alert('Oops unable to connect with server!!');
            }
        });
    });

    $("#forgot_password_agent").on('submit', function (e) {
        e.preventDefault();
        var data = $(this).serialize();
        if ($('#forgot_password_agent input[name="forget"]').val() == '') {
            $('#msgs').html('Please enter your registered email id').css('color', 'red').fadeIn().delay(3000).fadeOut();
            $('#forgot_password_agent input[name="forget"]').focus();
            return false;
        }

        $('.forgotpasswordagent').attr('disabled', 'disabled');
        $.ajax({
            url: SITEURL + 'agentlogin/index/forgotpassword',
            type: 'POST',
            data: data,
            dataType: 'json',
            beforeSend: function () {
                $('.forgotpasswordagent').html('<i class="ace-icon fa fa-spinner fa-spin orange bigger-125"></i> Please wait..');
            },
            success: function (result) {
                if (result.status) {
                    $('#msgs').html("Your password has been sent. Please check your mailbox.").css('color', 'green').fadeIn().delay(5000).fadeOut();
                    $('.hidedivs').hide();
                    $('.forgotpasswordagent').hide();
                } else {
                    $('.forgotpasswordagent').html('Submit');
                    $('.forgotpasswordagent').removeAttr('disabled', 'disabled');
                    $('#msgs').html(result.message).css('color', 'red').fadeIn().delay(3000).fadeOut();
                    return false;
                }
            },
            error: function () {
                $('.forgot').html('Submit');
                $('.forgot').removeAttr('disabled', 'disabled');
                alert('Oops unable to connect with server!!');
            }
        });
    });

    $(document).on('click', '.forgotpass', function () {
        $('#loginPanel').hide();
        $('#forgot_password').trigger("reset");
        $('#auth_login').trigger("reset");
        $('#forgot_password').show();
    });

    $(document).on('click', '.login', function () {
        $('#loginPanel').show();
        $('#forgot_password').trigger("reset");
        $('#auth_login').trigger("reset");
        $('#forgot_password').hide();
    });

    $(document).on('click', '.forgotpass_agent', function () {
        $('#loginPanel').hide();
        $('#forgot_password_agent').trigger("reset");
        $('#agentLoginForm').trigger("reset");
        $('#forgot_password_agent').show();
    });

    $(document).on('click', '.login_agent', function () {
        $('#loginPanel').show();
        $('#forgot_password_agent').trigger("reset");
        $('#agentLoginForm').trigger("reset");
        $('#forgot_password_agent').hide();
    });

    $(document).on('keypress', '.numericonly', function () {
        if (event.which != 8 && isNaN(String.fromCharCode(event.which))) {
            event.preventDefault();
        }
    });
    $("#select-country").change();

    var d = new Date();
    var year = d.getFullYear();
    var year2 = d.getFullYear() + 15;
    $('.Passdatepicket').datepicker({
        dateFormat: 'dd/mm/yy',
        numberOfMonths: 1,
        changeYear: true,
        changeMonth: true,
        yearRange: year + ':' + year2

    });
    $('.dateofbirth').datepicker({
        dateFormat: 'dd/mm/yy',
        numberOfMonths: 1,
        changeYear: true,
        changeMonth: true,
        yearRange: '1940:' + year

    });
    $('.MarriageAnniversary').datepicker({
        dateFormat: 'dd/mm/yy',
        numberOfMonths: 1,
        changeYear: true,
        changeMonth: true,
        yearRange: '1940:' + year

    });
    
    
    
    $("#auth_login_submit").on('submit', function (e) {
        e.preventDefault();
        var data = $(this).serialize();
        $.ajax({
            url: SITEURL + 'index/customerlogin',
            type: 'POST', data: data, dataType: 'json',
            beforeSend: function () {
//                $('.forgot').html('<i class="ace-icon fa fa-spinner fa-spin orange bigger-125"></i> Please wait..');
            },
            success: function (result) {
                if (result.status) {
                    window.location.reload();
                } else {
                    window.location.reload();
                }
            },
            error: function () {
               
                alert('Oops unable to connect with server!!');
            }});
    });
});
function populateCity(e) {
    var city_id = $('#city_id').val();
    var data = e.value;
    if (data !== '') {
        $.ajax({
            url: SITEURL + 'customer/getcitylist',
            type: 'POST',
            dataType: 'json',
            data: {country: data},
            success: function (result) {
                if (result.status) {
                    //console.log(result.countryId.cityArr);
                    var html = '<option value="">--Select--</option>';
                    $.each(result.countryId.cityArr, function (key, val) {
                        //console.log(val);
                        if (val.CityId === city_id) {
                            html += '<option selected="selected" value="' + val.CityId + '">' + val.Title + '</option>';
                        } else {
                            html += '<option value="' + val.CityId + '">' + val.Title + '</option>';
                        }

                    });
                    $('#select-city').html(html);
                } else {
                    alert(result.message);
                }
            },
            error: function () {
                alert('Request not completed');
            }
        });
    }

}

