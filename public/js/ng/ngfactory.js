app.factory('rvServices', function($http) {
    return{
        alrt: function(str) {
            if (!str)
                alert("this is test alert");
            else
                alert(str);
        }, rvFilterArrayByKey: function(key, array) {
            return array[key];
        }, rvReadJsonFile: function(url) {
            var rt = '';
            rt = $http.get(url).then(function(response) {
                return response.data;
            }). catch (function(err) {
                alert('error while reading json file');
                alert(err);
            });
            return rt;
        }, rvWriteHTML: function() {
            return;
        }, rvSum: function(a,b) {
            return parseInt(a) + parseInt(b);
        }, rvChangePriceValue: function(PkgSysId, object) {
            var returnval, PriceWithoutDiscount, otherCharges, tax, NetPrice = '';
            NetPrice = parseFloat(object.PricePerPerson);
            return(NetPrice);
        }, rvChangePriceDiscountedValue: function(PkgSysId, object) {
            var PriceWithoutDiscount, PricePerPerson, DiscountVal, DiscountType, DiscountValInPerc = '';
            DiscountType = object.DiscountType;
            PricePerPerson = object.PricePerPerson;
            DiscountVal = object.DiscountVal;
            if (DiscountType === 1) {
                PricePerPerson = parseFloat(PricePerPerson) + parseFloat(DiscountVal);
            }
            else if (DiscountType === 2) {
                DiscountValInPerc = (PricePerPerson * DiscountVal) / 100;
                PricePerPerson = parseFloat(PricePerPerson) + parseFloat(DiscountValInPerc);
            }
            else {
                PricePerPerson = parseFloat(PricePerPerson);
            }
            return(PricePerPerson);
        }, rvMoneyFormatINR: function(n, prec, currSign) {
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
            return(currSign == null ? "" : currSign + "") + num;
        }, rvRemoveCommas: function(str) {
            return parseInt(str.replace(',', ''));
        }, rvGetRecords: function(url) {
            var returnResult = '';
            return $http.get(url).then(function(response) {
                return response.data;
            }). catch (function(err) {
                alert('error while reading json file');
                alert(err);
            });
        }, rvSortListings: function(sorttype, sortby) {
            return alert(sorttype + sortby);
        }, rvFilterListings: function(ftype, fvalue) {
            return alert(ftype + fvalue);
        }, rvChangeUrl: function(PkgSysId, categoryName , packageName, Countries , mp) {
            var tourtype, category, gtxid, catid, newurl, tpid = '';
            tourtype = $('input[name="tType_' + PkgSysId + '"]:checked').val();
            gtxid = $('#GTX_package_id_' + PkgSysId).val();
            catid = conf.CATEGORY[categoryName];
            tpid = $('#package_tpid').val();
//            newurl = conf.SITEURL + 'detail/index/index/pkgid/' + PkgSysId + '/gtxid/' + gtxid + '/catid/' + catid + '/tourtype/' + tourtype;
            newurl = conf.SITEURL + 'tour-package-for-' + this.rvSanitizeForUrl(Countries) + '/' + this.rvSanitizeForUrl(packageName) +  '-' + PkgSysId + '-' + catid + '-' + gtxid + '-' + tourtype ;
            
            newurl += (mp) ? ( "-"+mp) : "";
            newurl += ".html";
            
            $('.detail_link_' + PkgSysId).attr('href', newurl);
            $('#hotelcategoryid_' + PkgSysId).val(catid);
        }, rvGetAllChecked: function(obj, type) {
            if (type === null) {
                var array = $.map(obj, function(value, index) {
                    return[value];
                });
                return array.toString();
            }
            else {
                var array = $.map(obj, function(value, index) {
                    return[value];
                });
                return array.join('RVSTR');
            }
        }, rvSanitizeForUrl: function( str ) {
            str = str.replace( /\s\s+/g, ' ' ).toLowerCase();
            str = str.replace(/#|(|)|,|\/|_|  +/g, '');
            str = str.split(',').join('-');
            str = str.split(' ').join('-');
            return str.replace('--', '-');
        }, jsObjectToArray : function( data ) {
            var result = Object.keys(data).map(function(k) {
                return [+k, data[k]];
              });
              return result;
        }, sortNumber : function (a,b) {
            return a - b;
        }, rvCheckCharExistsInSring : function( char , string ) {
            if ( string.indexOf(char) > -1 ) {
                return true;
            } else {
                return false;
            }
        },
        rvTrimcontent : function ( showChar , classname , linkClassname ) {
            var moretext = "+More";
            var lesstext = "-Less";

var d = $('.activity-section').find('p.more');

//console.log(document.querySelector('.'+classname));
//console.log(d);

angular.forEach(d , function(item ) {

//                var content = $(this).html();
console.log(item);
                if(content.length > showChar) {

                    var c = content.substr(0, showChar);
                    var h = content.substr(showChar, content.length - showChar);

                    var html = '<span class="defaultcontent" >' + c + '</span><span class="morecontent">' + h + '</span>\n\
                        &nbsp;&nbsp;&nbsp;<a href="javascript:void();" class="'+ linkClassname +' btn-link btnmore">' + moretext + '</a>';
                    $(this).html(html);
                }
            });

            $("."+linkClassname).click(function(){
                if($(this).hasClass("less")) {
                    $(this).removeClass("less").html(moretext);
                } else {
                    $(this).addClass("less").html(lesstext);
                }
                $(this).prev().toggle();
                return false;
            });

            $('span.morecontent').hide();
            $('.defaultcontent , .morecontent').css({'font-size':'12px' ,'text-transform':'none' ,'font-weight':'normal' });
        }
 };
});