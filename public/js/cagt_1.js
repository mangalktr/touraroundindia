!function(e){"use strict";jQuery.each(jQuery.browser,function(s,a){e("body").addClass(s)}),e(window).on("load",function(){var s=e(".site-branding .container").outerHeight();e(".main-menu .sub-menu,.main-menu  .mega-menu").animate({paddingTop:s}),e(".main-menu li,.main-menu li .sub-menu").on("hover",function(){e(this).children(".sub-menu").stop(!0,!0).fadeToggle(),e(this).children(".inner-menu").stop(!0,!0).fadeToggle()}),e(".main-menu .mega-menu-nav").hover(function(){e(this).children(".mega-menu").stop(!0,!0).fadeIn(),e(this).children(".mega-menu").css("display","flex")},function(){e(this).children(".mega-menu").stop(!0,!0).fadeOut()}),e(".header-search").on("hover",function(){e(this).children(".search").stop(!0,!0).slideToggle()}),e(".header-tour-package").on("click",function(){e(this).children(".header-tour-listing").stop(!0,!0).slideToggle()}),e(window).width()<992&&e("#adv-search legend").on("click",function(){e(".form-wrap").stop(!0,!0).slideToggle()}),window.onresize=function(){e(window).width()<992&&e("#adv-search legend").on("click",function(){e(".form-wrap").stop(!0,!0).slideToggle()})}}),jQuery().slick&&(e(".main-slider, .tour-by-destination").slick({dots:!1,infinite:!0,speed:500,fade:!0,cssEase:"linear",adaptiveHeight:!0,prevArrow:'<span class="slick-prev"><i class="fa fa-angle-left"></i></span>',nextArrow:'<span class="slick-next"><i class="fa fa-angle-right"></i></span>'}),e(".tour-carousel").slick({slidesToShow:4,slidesToScroll:4,dots:!1,prevArrow:'<span class="slick-prev"><i class="fa fa-angle-left"></i></span>',nextArrow:'<span class="slick-next"><i class="fa fa-angle-right"></i></span>',responsive:[{breakpoint:991,settings:{slidesToShow:3,slidesToScroll:3}},{breakpoint:700,settings:{slidesToShow:2,slidesToScroll:2}},{breakpoint:500,settings:{slidesToShow:1,slidesToScroll:1}}]}),e(".partners-carousel").slick({slidesToShow:5,slidesToScroll:1,dots:!1,arrows:!1,infinite:!0,speed:500,autoplay:!0,adaptiveHeight:!0,responsive:[{breakpoint:991,settings:{slidesToShow:3,slidesToScroll:3}},{breakpoint:700,settings:{slidesToShow:2,slidesToScroll:2}},{breakpoint:500,settings:{slidesToShow:1,slidesToScroll:1}}]}),e(".tour-single-slider-for").slick({slidesToShow:1,slidesToScroll:1,arrows:!1,fade:!0,asNavFor:".tour-single-slider-nav",autoplay:!0,adaptiveHeight:!0}),e(".tour-single-slider-nav").slick({slidesToShow:3,slidesToScroll:1,asNavFor:".tour-single-slider-for",arrows:!1,focusOnSelect:!0})),e("#myTab a").click(function(s){s.preventDefault(),e(this).tab("show")}),e("#accordion .panel-title").click(function(){e(this).hasClass("current")?e(this).closest(".panel-heading").removeClass("current"):e(this).closest(".panel-heading").addClass("current")}),e(function(){e('[data-toggle="tooltip"]').tooltip()}),e(".skillbar").each(function(){jQuery(this).find(".skillbar-bar").animate({width:jQuery(this).attr("data-percent")},2e3)}),jQuery().datepicker&&e(".date-picker").datepicker({format:"mm/dd/yyyy"}),jQuery().select2&&(e("#adv-search select, .sorting select").select2({speed:400}),window.onresize=function(){e("#adv-search select, .sorting select").select2({speed:400})});var s=e(".tour-item"),a=e(".layout-control a"),i=e(".booking-layout a"),l=e(".tour-post"),o=e(".c-layouts");a.on("click",function(){var i="col-xs-"+e(this).data("layout");return s.removeClass("col-md-4 col-xs-12 col-xs-4 col-md-4 col-sm-6"),s.addClass(i),s.hasClass("col-xs-12")?l.addClass("list-style"):l.removeClass("list-style"),a.removeClass("active"),e(this).addClass("active"),!1}),e(".calender-show").on("click",function(){var s=e(this).data("layout");return o.removeClass("show"),e(".calender-layout").addClass(s),i.removeClass("active"),e(this).addClass("active"),!1}),e(".list-show").on("click",function(){var s=e(this).data("layout");return o.removeClass("show"),e(".calender-listing").addClass(s),i.removeClass("active"),e(this).addClass("active"),!1});var n=e("#slider-range");jQuery().slider&&(n.slider({range:!0,min:0,max:500,values:[75,300],slide:function(s,a){e("#amount").val("$"+a.values[0]+" - $"+a.values[1])}}),e("#amount").val("$"+n.slider("values",0)+" - $"+n.slider("values",1))),jQuery().slicknav&&e(".main-nav .main-menu").slicknav({prependTo:".site-branding",allowParentLinks:!0}),e("#location-map").length>0&&e(window).on("load",function(){new google.maps.Map(document.getElementById("location-map"),{center:{lat:-33.8818464,lng:151.205348},zoom:18})})}(jQuery);