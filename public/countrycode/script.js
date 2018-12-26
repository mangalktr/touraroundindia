

$(".country_details").click(function(){
    $(".country_dropdown").toggleClass('show');
    $("#countries_list, .countries_list").focus();
    $("#countries_list, .countries_list").val('');
    $(".allcountries li").show();
    // countryDorpdownClick();
});

$(".country_details2").click(function(){
    $(".country_dropdown2").toggleClass('show');
    $("#countries_list2, .countries_list2").focus();
    $("#countries_list2, .countries_list2").val('');
    $(".allcountries2 li").show();
    // countryDorpdownClick();
});

$(document).click(function(event) { 
    if(!$(event.target).closest('.textfieldMain_mobile').length) {
        if($('.country_dropdown').is(":visible")) {
            $('.country_dropdown').removeClass('show');
        }
    }        
});

$('.textfieldMain_mobile2').click(function(event) { 
    if(!$(event.target).closest('.textfieldMain_mobile2').length) {
        if($('.country_dropdown2').is(":visible")) {
            $('.country_dropdown2').removeClass('show');
        }
    }        
});






$( function() {

    var allCountries = [{"country":"Afghanistan","code":"af","ext":"93"},{"country":"Albania","code":"al","ext":"355"},{"country":"Algeria","code":"dz","ext":"213"},{"country":"American Samoa","code":"as","ext":"1684"},{"country":"Andorra","code":"ad","ext":"376"},{"country":"Angola","code":"ao","ext":"244"},{"country":"Anguilla","code":"ai","ext":"1264"},{"country":"Antigua and Barbuda","code":"ag","ext":"1268"},{"country":"Argentina","code":"ar","ext":"54"},{"country":"Armenia","code":"am","ext":"374"},{"country":"Aruba","code":"aw","ext":"297"},{"country":"Australia","code":"au","ext":"61"},{"country":"Austria","code":"at","ext":"43"},{"country":"Azerbaijan","code":"az","ext":"994"},{"country":"Bahamas","code":"bs","ext":"1242"},{"country":"Bahrain","code":"bh","ext":"973"},{"country":"Bangladesh","code":"bd","ext":"880"},{"country":"Barbados","code":"bb","ext":"1246"},{"country":"Belarus","code":"by","ext":"375"},{"country":"Belgium","code":"be","ext":"32"},{"country":"Belize","code":"bz","ext":"501"},{"country":"Benin","code":"bj","ext":"229"},{"country":"Bermuda","code":"bm","ext":"1441"},{"country":"Bhutan","code":"bt","ext":"975"},{"country":"Bolivia","code":"bo","ext":"591"},{"country":"Bosnia and Herzegovina ","code":"ba","ext":"387"},{"country":"Botswana","code":"bw","ext":"267"},{"country":"Brazil","code":"br","ext":"55"},{"country":"British Indian Ocean Territory","code":"io","ext":"246"},{"country":"British Virgin Islands","code":"vg","ext":"1284"},{"country":"Brunei","code":"bn","ext":"673"},{"country":"Bulgaria","code":"bg","ext":"359"},{"country":"Burkina Faso","code":"bf","ext":"226"},{"country":"Burundi","code":"bi","ext":"257"},{"country":"Cambodia","code":"kh","ext":"855"},{"country":"Cameroon","code":"cm","ext":"237"},{"country":"Canada","code":"ca","ext":"1"},{"country":"Cape Verde","code":"cv","ext":"238"},{"country":"Caribbean Netherlands","code":"bq","ext":"599"},{"country":"Cayman Islands","code":"ky","ext":"1345"},{"country":"Central African Republic","code":"cf","ext":"236"},{"country":"Chad","code":"td","ext":"235"},{"country":"Chile","code":"cl","ext":"56"},{"country":"China","code":"cn","ext":"86"},{"country":"Christmas Island","code":"cx","ext":"61"},{"country":"Cocos (Keeling) Islands","code":"cc","ext":"61"},{"country":"Colombia","code":"co","ext":"57"},{"country":"Comoros","code":"km","ext":"269"},{"country":"Congo (DRC) (Jamhuri ya Kidemokrasia ya Kongo)","code":"cd","ext":"243"},{"country":"Congo (Republic) (Congo-Brazzaville)","code":"cg","ext":"242"},{"country":"Cook Islands","code":"ck","ext":"682"},{"country":"Costa Rica","code":"cr","ext":"506"},{"country":"Ivory Coast","code":"ci","ext":"225"},{"country":"Croatia","code":"hr","ext":"385"},{"country":"Cuba","code":"cu","ext":"53"},{"country":"Curacao","code":"cw","ext":"599"},{"country":"Cyprus","code":"cy","ext":"357"},{"country":"Czech Republic","code":"cz","ext":"420"},{"country":"Denmark","code":"dk","ext":"45"},{"country":"Djibouti","code":"dj","ext":"253"},{"country":"Dominica","code":"dm","ext":"1767"},{"country":"Dominican Republic","code":"do","ext":"1"},{"country":"Ecuador","code":"ec","ext":"593"},{"country":"Egypt","code":"eg","ext":"20"},{"country":"El Salvador","code":"sv","ext":"503"},{"country":"Equatorial Guinea","code":"gq","ext":"240"},{"country":"Eritrea","code":"er","ext":"291"},{"country":"Estonia","code":"ee","ext":"372"},{"country":"Ethiopia","code":"et","ext":"251"},{"country":"Falkland Islands","code":"fk","ext":"500"},{"country":"Faroe Islands","code":"fo","ext":"298"},{"country":"Fiji","code":"fj","ext":"679"},{"country":"Finland","code":"fi","ext":"358"},{"country":"France","code":"fr","ext":"33"},{"country":"French Guiana","code":"gf","ext":"594"},{"country":"French Polynesia","code":"pf","ext":"689"},{"country":"Gabon","code":"ga","ext":"241"},{"country":"Gambia","code":"gm","ext":"220"},{"country":"Georgia","code":"ge","ext":"995"},{"country":"Germany","code":"de","ext":"49"},{"country":"Ghana","code":"gh","ext":"233"},{"country":"Gibraltar","code":"gi","ext":"350"},{"country":"Greece","code":"gr","ext":"30"},{"country":"Greenland","code":"gl","ext":"299"},{"country":"Grenada","code":"gd","ext":"1473"},{"country":"Guadeloupe","code":"gp","ext":"590"},{"country":"Guam","code":"gu","ext":"1671"},{"country":"Guatemala","code":"gt","ext":"502"},{"country":"Guernsey","code":"gg","ext":"44"},{"country":"Guinea","code":"gn","ext":"224"},{"country":"Guinea-Bissau","code":"gw","ext":"245"},{"country":"Guyana","code":"gy","ext":"592"},{"country":"Haiti","code":"ht","ext":"509"},{"country":"Honduras","code":"hn","ext":"504"},{"country":"Hong Kong","code":"hk","ext":"852"},{"country":"Hungary","code":"hu","ext":"36"},{"country":"Iceland","code":"is","ext":"354"},{"country":"India","code":"in","ext":"91"},{"country":"Indonesia","code":"id","ext":"62"},{"country":"Iran","code":"ir","ext":"98"},{"country":"Iraq","code":"iq","ext":"964"},{"country":"Ireland","code":"ie","ext":"353"},{"country":"Isle of Man","code":"im","ext":"44"},{"country":"Israel","code":"il","ext":"972"},{"country":"Italy","code":"it","ext":"39"},{"country":"Jamaica","code":"jm","ext":"1876"},{"country":"Japan","code":"jp","ext":"81"},{"country":"Jersey","code":"je","ext":"44"},{"country":"Jordan","code":"jo","ext":"962"},{"country":"Kazakhstan","code":"kz","ext":"7"},{"country":"Kenya","code":"ke","ext":"254"},{"country":"Kiribati","code":"ki","ext":"686"},{"country":"Kuwait","code":"kw","ext":"965"},{"country":"Kyrgyzstan","code":"kg","ext":"996"},{"country":"Laos","code":"la","ext":"856"},{"country":"Latvia","code":"lv","ext":"371"},{"country":"Lebanon","code":"lb","ext":"961"},{"country":"Lesotho","code":"ls","ext":"266"},{"country":"Liberia","code":"lr","ext":"231"},{"country":"Libya","code":"ly","ext":"218"},{"country":"Liechtenstein","code":"li","ext":"423"},{"country":"Lithuania","code":"lt","ext":"370"},{"country":"Luxembourg","code":"lu","ext":"352"},{"country":"Macau","code":"mo","ext":"853"},{"country":"Macedonia","code":"mk","ext":"389"},{"country":"Madagascar","code":"mg","ext":"261"},{"country":"Malawi","code":"mw","ext":"265"},{"country":"Malaysia","code":"my","ext":"60"},{"country":"Maldives","code":"mv","ext":"960"},{"country":"Mali","code":"ml","ext":"223"},{"country":"Malta","code":"mt","ext":"356"},{"country":"Marshall Islands","code":"mh","ext":"692"},{"country":"Martinique","code":"mq","ext":"596"},{"country":"Mauritania","code":"mr","ext":"222"},{"country":"Mauritius","code":"mu","ext":"230"},{"country":"Mayotte","code":"yt","ext":"262"},{"country":"Mexico","code":"mx","ext":"52"},{"country":"Micronesia","code":"fm","ext":"691"},{"country":"Moldova","code":"md","ext":"373"},{"country":"Monaco","code":"mc","ext":"377"},{"country":"Mongolia","code":"mn","ext":"976"},{"country":"Montenegro","code":"me","ext":"382"},{"country":"Montserrat","code":"ms","ext":"1664"},{"country":"Morocco","code":"ma","ext":"212"},{"country":"Mozambique","code":"mz","ext":"258"},{"country":"Myanmar","code":"mm","ext":"95"},{"country":"Namibia","code":"na","ext":"264"},{"country":"Nauru","code":"nr","ext":"674"},{"country":"Nepal","code":"np","ext":"977"},{"country":"Netherlands","code":"nl","ext":"31"},{"country":"New Caledonia","code":"nc","ext":"687"},{"country":"New Zealand","code":"nz","ext":"64"},{"country":"Nicaragua","code":"ni","ext":"505"},{"country":"Niger","code":"ne","ext":"227"},{"country":"Nigeria","code":"ng","ext":"234"},{"country":"Niue","code":"nu","ext":"683"},{"country":"Norfolk Island","code":"nf","ext":"672"},{"country":"North Korea","code":"kp","ext":"850"},{"country":"Northern Mariana Islands","code":"mp","ext":"1670"},{"country":"Norway","code":"no","ext":"47"},{"country":"Oman","code":"om","ext":"968"},{"country":"Pakistan","code":"pk","ext":"92"},{"country":"Palau","code":"pw","ext":"680"},{"country":"Palestine","code":"ps","ext":"970"},{"country":"Panama","code":"pa","ext":"507"},{"country":"Papua New Guinea","code":"pg","ext":"675"},{"country":"Paraguay","code":"py","ext":"595"},{"country":"Peru","code":"pe","ext":"51"},{"country":"Philippines","code":"ph","ext":"63"},{"country":"Poland (Polska)","code":"pl","ext":"48"},{"country":"Portugal","code":"pt","ext":"351"},{"country":"Puerto Rico","code":"pr","ext":"1"},{"country":"Qatar","code":"qa","ext":"974"},{"country":"Reunion","code":"re","ext":"262"},{"country":"Romania","code":"ro","ext":"40"},{"country":"Russia","code":"ru","ext":"7"},{"country":"Rwanda","code":"rw","ext":"250"},{"country":"Saint Barthelemy","code":"bl","ext":"590"},{"country":"Saint Helena","code":"sh","ext":"290"},{"country":"Saint Kitts and Nevis","code":"kn","ext":"1869"},{"country":"Saint Lucia","code":"lc","ext":"1758"},{"country":"Saint Martin","code":"mf","ext":"590"},{"country":"Saint Pierre and Miquelon","code":"pm","ext":"508"},{"country":"Saint Vincent and the Grenadines","code":"vc","ext":"1784"},{"country":"Samoa","code":"ws","ext":"685"},{"country":"San Marino","code":"sm","ext":"378"},{"country":"Sao Tome and Principe","code":"st","ext":"239"},{"country":"Saudi Arabia","code":"sa","ext":"966"},{"country":"Senegal","code":"sn","ext":"221"},{"country":"Serbia","code":"rs","ext":"381"},{"country":"Seychelles","code":"sc","ext":"248"},{"country":"Sierra Leone","code":"sl","ext":"232"},{"country":"Singapore","code":"sg","ext":"65"},{"country":"Sint Maarten","code":"sx","ext":"1721"},{"country":"Slovakia","code":"sk","ext":"421"},{"country":"Slovenia","code":"si","ext":"386"},{"country":"Solomon Islands","code":"sb","ext":"677"},{"country":"Somalia","code":"so","ext":"252"},{"country":"South Africa","code":"za","ext":"27"},{"country":"South Korea","code":"kr","ext":"82"},{"country":"South Sudan","code":"ss","ext":"211"},{"country":"Spain","code":"es","ext":"34"},{"country":"Sri Lanka","code":"lk","ext":"94"},{"country":"Sudan","code":"sd","ext":"249"},{"country":"Suriname","code":"sr","ext":"597"},{"country":"Svalbard and Jan Mayen","code":"sj","ext":"47"},{"country":"Swaziland","code":"sz","ext":"268"},{"country":"Sweden","code":"se","ext":"46"},{"country":"Switzerland","code":"ch","ext":"41"},{"country":"Syria","code":"sy","ext":"963"},{"country":"Taiwan","code":"tw","ext":"886"},{"country":"Tajikistan","code":"tj","ext":"992"},{"country":"Tanzania","code":"tz","ext":"255"},{"country":"Thailand","code":"th","ext":"66"},{"country":"Timor-Leste","code":"tl","ext":"670"},{"country":"Togo","code":"tg","ext":"228"},{"country":"Tokelau","code":"tk","ext":"690"},{"country":"Tonga","code":"to","ext":"676"},{"country":"Trinidad and Tobago","code":"tt","ext":"1868"},{"country":"Tunisia","code":"tn","ext":"216"},{"country":"Turkey","code":"tr","ext":"90"},{"country":"Turkmenistan","code":"tm","ext":"993"},{"country":"Turks and Caicos Islands","code":"tc","ext":"1649"},{"country":"Tuvalu","code":"tv","ext":"688"},{"country":"U.S. Virgin Islands","code":"vi","ext":"1340"},{"country":"Uganda","code":"ug","ext":"256"},{"country":"Ukraine","code":"ua","ext":"380"},{"country":"United Arab Emirates","code":"ae","ext":"971"},{"country":"United Kingdom","code":"gb","ext":"44"},{"country":"United States","code":"us","ext":"1"},{"country":"Uruguay","code":"uy","ext":"598"},{"country":"Uzbekistan","code":"uz","ext":"998"},{"country":"Vanuatu","code":"vu","ext":"678"},{"country":"Vatican City","code":"va","ext":"39"},{"country":"Venezuela","code":"ve","ext":"58"},{"country":"Vietnam","code":"vn","ext":"84"},{"country":"Wallis and Futuna","code":"wf","ext":"681"},{"country":"Western Sahara","code":"eh","ext":"212"},{"country":"Yemen","code":"ye","ext":"967"},{"country":"Zambia","code":"zm","ext":"260"},{"country":"Zimbabwe","code":"zw","ext":"263"},{"country":"Finland Islands","code":"ax","ext":"358"}];
    var $searchBox = $("#countries_list, .countries_list");
    for (var i = 0; i < allCountries.length; i++) {
        var html = "<li data-country="+allCountries[i].country+" data-code="+allCountries[i].code+" data-ext="+allCountries[i].ext+"><div class='iti-flag "+ allCountries[i].code + "'></div><span> " + allCountries[i].country +  " +" + allCountries[i].ext  +"</span></li>";
        $('.allcountries').append(html);
        $('.allcountries2').append(html);
    }
    $(document).ready(function(){
        $("#countries_list, .countries_list").keyup(function(){
     
            // Retrieve the input field text and reset the count to zero
            var filter = $(this).val(), count = 0;
     
            // Loop through the comment list
            $(".allcountries li").each(function(){
     
                // If the list item does not contain the text phrase fade it out
                if ($(this).text().search(new RegExp(filter, "i")) < 0) {
                    $(this).fadeOut();
     
                // Show the list item if the phrase matches and increase the count by 1
                } else {
                    $(this).show();
                    count++;
                }
            });
        });
		
		 $("#countries_list2, .countries_list2").keyup(function(){
     
            // Retrieve the input field text and reset the count to zero
            var filter = $(this).val(), count = 0;
     
            // Loop through the comment list
            $(".allcountries2 li").each(function(){
     
                // If the list item does not contain the text phrase fade it out
                if ($(this).text().search(new RegExp(filter, "i")) < 0) {
                    $(this).fadeOut();
     
                // Show the list item if the phrase matches and increase the count by 1
                } else {
                    $(this).show();
                    count++;
                }
            });
        });
        $(".allcountries li").click(function(){
            $("#iti-flag").attr('class','');
            $("#iti-flag1").attr('class','');
            var countryname = $(this).attr("data-country");
            var countrycode = $(this).attr("data-code");
            var countryextn = $(this).attr("data-ext");
            $("#iti-flag").attr('class','iti-flag '+countrycode+'');
            $("#iti-flag1").attr('class','iti-flag '+countrycode+'');
            $(".country_code").val("+" + countryextn);
            $(".code_icons").val(countrycode);
            $("#countries_list, .countries_list").val('');
            /* Lead query*/
            $("#iti-flag_leadq").attr('class','iti-flag '+countrycode+'');
            /* Lead query pop*/
            $("#iti-flag_leadqp").attr('class','iti-flag '+countrycode+'');
			
            $("#mobileno").focus();
            $("#mobilenumLead").focus();
            $("#b2bagent_mobilenum").focus();
            $("#mobile_number").focus();
            $(".country_dropdown").toggleClass('show');
        });
		
		
		$(".allcountries2 li").click(function(){
            $("#iti-flag_2").attr('class','');
            var countryname = $(this).attr("data-country");
            var countrycode = $(this).attr("data-code");
            var countryextn = $(this).attr("data-ext");
            $(".country_code2").val("+" + countryextn);
            $(".code_icons").val(countrycode);
            $("#countries_list2, .countries_list2").val('');
            /* Lead query*/
            $("#iti-flag_2").attr('class','iti-flag '+countrycode+'');
            
			
            $(".mobile_code").focus();
            
            $(".country_dropdown2").toggleClass('show');
        })
    });
  } );
   
