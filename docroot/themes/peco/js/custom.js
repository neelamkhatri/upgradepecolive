(function ($, Drupal) {        
  // It's best practice to use strict mode, can help avoid some browser issues.
  'use strict';
  Drupal.behaviors = Drupal.behaviors || {};
  // Generally you always want to use behaviors, remember to depend on Drupal js.
  Drupal.behaviors.custom = {
    // Called on document ready and when no elements is added to the page.
    attach: function (context, settings) {
                
    }
  }
  
  Drupal.behaviors.customAjax = {
    // Called on document ready and when no elements is added to the page.
    attach: function (context, settings) {
              
        
    }
  }
})(window.jQuery, window.Drupal);


(function ($, Drupal) {

  var $animation_elements = $('.animation-element');
  var $window = $(window);

  function check_if_in_view() {
    var window_height = $window.height();
    var window_top_position = $window.scrollTop();
    var window_bottom_position = (window_top_position + window_height);
   
    $.each($animation_elements, function() {
      var $element = $(this);
      var element_height = $element.outerHeight();
      var element_top_position = $element.offset().top;
      var element_bottom_position = (element_top_position + element_height);
   
      //check to see if this current container is within viewport
      if ((element_bottom_position >= window_top_position) &&
          (element_top_position <= window_bottom_position)) {
        $element.addClass('in-view');
      } else {
        $element.removeClass('in-view');
      }
    });
  }
  $window.on('scroll resize', check_if_in_view);
  $window.trigger('scroll');

  var totalHeight = 0;

  jQuery('header').each(function(index ,element ){

      totalHeight +=  jQuery(element).height();

  });

  //console.log(totalHeight);

  $(window).bind('scroll',function(e){
      parallaxScroll();
  });

  function parallaxScroll(){
     var scrolled = $(window).scrollTop(); 
    //  $('.in-view').css('margin-top',((scrolled*.1))+'px');
     $('.banner-animation.in-view').css('margin-top',((scrolled*.6))+'px');
     $('.in-view .rock-3').css('margin-top',((-scrolled*.1))+'px');
     $('.in-view .rock-2').css('margin-top',((scrolled*0))+'px');
     $('.in-view .rock-1').css('margin-bottom',((-scrolled*.1))+'px'); 
     $('.topbuildAnimation .in-view .rock-4').css('margin-top',((-scrolled*0))+'px');
     $('.topbuildAnimation .in-view .rock-5').css('margin-top',((-scrolled*.2))+'px');
     $('.topbuildAnimation .in-view .rock-6').css('margin-top',((-scrolled*.2))+'px'); 
     $('.bottombuildAnimation .in-view .rock-4').css('margin-top',((-scrolled*0))+'px');
     $('.bottombuildAnimation .in-view .rock-5').css('margin-top',((-scrolled*.03))+'px');
     $('.bottombuildAnimation .in-view .rock-6').css('margin-top',((-scrolled*.03))+'px'); 
  }

// document.onreadystatechange = function () {
//     var state = document.readyState
//     if (state == 'complete') {
//      document.getElementById('interactive');
//      document.getElementById('load').style.visibility="hidden";
//    }
//  }

  $(".mapeast").hover(
    function(){
      $('svg').addClass('mapEastHover');
    }, function(){
      $('svg').removeClass('mapEastHover');
    }
  );

  $(".mapnorth").hover(
  function(){
    $('svg').addClass('mapNorthHover')
  }, function(){
    $('svg').removeClass('mapNorthHover')
  }
  );
  $(".mapwest").hover(
  function(){
    $('svg').addClass('mapWestHover')
  }, function(){
    $('svg').removeClass('mapWestHover')
  }
  );
  
  $(".mapmid").hover(
    function(){
      $('svg').addClass('mapMidHover')
    }, function(){
      $('svg').removeClass('mapMidHover')
    }
  );
   // Blog Filter Date
   var input1 = document.getElementById('published_date');
   var input2 = document.getElementById('edit-published-on-val--2');
   if(input1){
     input1.addEventListener('change', function() {
       input2.value = input1.value;
     });
   }
   // BLog filter End
  //  $(document).ready(function () {
      // $(window).resize(function() {
      //   updatePlanViewerHeight();
      // });

      // if (navigator.userAgent.match(/(iPod|iPhone|iPad)/)) {
      //   var ratio = $(".jsviewer").data("ratio");
      //   var height = $(".jsviewer").data("height");
      //   var width = $('.sitePlanLeft').width();
      //   var newHeight = width * ratio;
      //   $('.jsviewer').css('height', newHeight + 'px');
      //   $('.jsviewer').css('width', width + 'px');
      // };
      updatePlanViewerHeight();

      // var ratio = $(".jsviewer").data("ratio");
      //   var height = $(".jsviewer").data("height");
      //   //console.log(ratio);
      //   //console.log(height);
      //   var start = 1270;
      //   var width = $('.container-fluid').width();
      //   //console.log(width);

      //   $('.jsviewer').css('height', width * ratio);
      //   $(window).on('resize', _.debounce(function() {
      //       if($(window).width() > 1175) {
      //           $('.jsviewer').css('height', $('.sitePlanLeft-platWrapper').width() * ratio);
      //       } else if($(window).width() > 1023) {
      //           $('.jsviewer').css('height', $('.sitePlanLeft-platWrapper').width() * ratio);
      //       } else if($(window).width() > 700){
      //           $('.jsviewer').css('height', $('.sitePlanLeft-platWrapper').width() * ratio);
      //       } else {
      //           $('.jsviewer').css('height', $('.sitePlanLeft-platWrapper').width() * ratio);
      //       }
      //   }, 150));
    // });

    function updatePlanViewerHeight() {
      // if($(window).width() < 450) {
      //     console.log('window was resized and lestt than 450');
      //     $("[id^=jsviewer]").attr("style", "width:100% !important;height:300px !important;");
      //     // console.log($("[id^=jsviewer]"));
      // }
      // if($(window).width() > 451 && $(window).width() < 750) {
      //      console.log('window was resized and greater than 451');
      //     $("[id^=jsviewer]").attr("style", "width:100% !important;height:600px !important;");
      // }
      // if($(window).width() > 751) {
        if( $('.jsviewer').length )         // use this if you are using id to check
        {
          console.log('window was resized and greater than 701');
          // $("[id^=jsviewer]").attr("style", "width:100% !important;height:860px !important;");
          console.log($("[id^=jsviewer]")[0].id);
          var idiframe = $("[id^=jsviewer]")[0].id;
          var url = document.getElementById($("[id^=jsviewer]")[0].id).src;
        //   var url = document.getElementsByTagName('iframe')[1].src; 
          var new_url = url.split('width:500/height:500/')[0];
          console.log($('.jsviewer').parent().width());
          var iwidth = $('#'+idiframe).parent().width();
          $('#'+idiframe).attr('src',new_url+'width:'+iwidth+'/height:550/');
          console.log(new_url);
        }
           
      // }
  }

  $("#block-breadcrumbs").find("li a").each(function(){
    $(this)[0].innerHTML = $(this).text().replace(/-/g, " ");
      var oldUrl = $(this).attr("href"); // Get current url
      var spliturl = oldUrl.split('.com/');
      console.log(spliturl);
      var newPathname = '';
      for (i = 1; i < spliturl.length; i++) {
        newPathname += "/";
        newPathname += spliturl[i].replace(/-/g, " ");
          $(this).attr("href", newPathname);
      }
  });
   
  $('#block-breadcrumbs li:last')[0].innerHTML = $('#block-breadcrumbs li:last')[0].innerHTML.replace(/-/g, " ");
  
   /* Space Available API Property detail page - jsviewer */
    $(".highlightJS").mouseover(function(){
        var sitemapid = $(this).data("sitemapid");
        var spaceId = $(this).data('spaceid');
        var s = spaceId.toString();
        document.getElementById('jsviewer' + sitemapid).contentWindow.PlanWidget.publicAPI.highlightSpaces(s);
    });
    $("div.highlightJS").click(function(){
        var sitemapid = $(this).data('sitemapid');
        var spaceId = $(this).data('spaceid');
        var s = spaceId.toString();
        document.getElementById('jsviewer' + sitemapid).contentWindow.PlanWidget.publicAPI.selectSpaces(s);
    });
    /* Space Available API Property detail page - jsviewer end */
  
    /* Property Form and Map */
    // search modal popup
    
    // Disable/Enable form elements on ajax call.
    $(document)
    .ajaxStart(function() {
      if ($('#edit-submit').length) {
          //alert(1);
        // Disable elements.
        if($('#edit-submit').attr('disabled') !== 'disabled') {
            $('#edit-submit').attr('disabled', 'disabled');
        }
      }
    })
    .ajaxComplete(function() {
      if ($('#edit-submit').length) {
          //alert(2);
        // Enable elements.
        if($('#edit-submit').attr('disabled') == 'disabled') {
            $('#edit-submit').removeAttr('disabled');
        }
      }
    });
    
    $('.searchform-button').on('click', function () {   
        //$('#modalSearchForm').modal('hide');
        
        
        $('.searchform-button').removeClass('active');
        $(this).addClass('active');
        var filter_id = $(this).attr('id');
        var target_id = $(this).data('target');
        console.log(filter_id,$('#searchform > #frm-'+filter_id));
        $('#selected-filter-class').text(filter_id);
        
        $('#filterModalPopup .searchby-item').addClass('hidden');
        $('#filterModalPopup #frm-'+filter_id).removeClass('hidden');
        //return;       
        
        
        var modal_html = $('#searchform').find('.modal-content').html();  //searchform, modalSearchForm    
        //$(target_id).html(modal_html);
        //$('#searchform1').html('');
        
        var popup_id = '#'+filter_id+'-popup';
        //popup_id = '#searchform12';
        //console.log(popup_id);
        $('#searchform').prependTo(popup_id);
        //$(popup_id+'-data').html(modal_html);
        //$(popup_id).find('.modal-content').html(modal_html);
        //$(popup_id).removeClass('hidden');
        
        /*$( "#searchform-buttons button" ).each(function( index ) {
            var button_id = $( this ).attr('id');
            //console.log( index + ": " + button_id );
            $('#searchform #modalSearchForm').removeClass(button_id);
        });*/
        
        //$('#searchform #modalSearchForm').addClass(filter_id);
        
        //var modal_html = $('#searchform').html();        
        //$('#searchby-contact-popup').removeClass('hidden');
        //$('#searchby-contact-popup').html(modal_html);
        
        //var modal_html = $('#searchform1').html();  //searchform, modalSearchForm    
        //$(target_id).html(modal_html);
        //$('#searchform1').html('');
        
        //var popup_id = '#'+filter_id+'-popup';
        //popup_id = '#searchform';
        //console.log(popup_id);
        //$(popup_id+'-data').html(modal_html);
        //$(popup_id).html(modal_html);
        //$(popup_id).removeClass('hidden');
        
        $('#btn-'+filter_id).trigger('click');
        //$('#modalSearchForm').modal('show');
        //$('#searchby-contact-popup-data').html(modal_html);
    });
    
    $('.filterModalClose').on('click', function () {   
        
    });
    
    $('#modalSearchForm').on('shown.bs.modal', function () {
       $('body').addClass('hide-modal-backdrop');
    }).on('hide.bs.modal', function(){
        //var selected_modal = $('#selected-filter-class').text();
        //console.log('selected-filter-class',selected_modal);
        //$(this).removeClass(selected_modal);
        $('body').removeClass('hide-modal-backdrop');        
        
        //var modal_html = $(selected_modal).html();      
        //$('#searchform').html(modal_html);
        //$(selected_modal).html(''); 
    });
    // search modal popup end

    $('#space-available-map-hide').click(function(){
      $('#sitePlanRight').toggleClass('hidden');
      $('#sitePlanLeft').toggleClass('col-md-7 col-md-12');
      $('.svg-close').toggleClass('hidden');
    });
    
    $('#property-listing-map-hide').on('click', function () {
        $(this).parent().addClass('hidden');
        $('#property-listing-map').addClass('hidden');
        $('#property-listing').removeClass('col-md-7').addClass('col-md-12');
        $('#property-listing-map-show').parent().removeClass('hidden');
        //$('#searchList').removeClass('searchList');
    });
    $('#property-listing-map-show').on('click', function () {
        $(this).parent().addClass('hidden');
        $('#property-listing-map').removeClass('hidden');
        $('#property-listing').removeClass('col-md-12').addClass('col-md-7');
        $('#property-listing-map-hide').parent().removeClass('hidden');
        //$('#searchList').addClass('searchList');
    });
    
    var map;
    var prop_map_id = 'property-map-widget';
    function initMap() {
      var $ = jQuery; 

      // properties listing map
      if ($('#'+prop_map_id).length) {
        //var properties = $('#property-listing-page .prop-block, #property-listing .property-preview'); // dynamic listing page or search results page
        //var properties = $('#property-listing .prop-block'); // dynamic listing page or search results page
        
        var properties = $("#ajax-view-property .prop-block-ajax"); // working
        //var properties = $("#ajax-view-property").find(".prop-block-ajax");
        //var properties = $(".property-pins .prop-block-ajax");
        
        console.log('Total Property', properties.length);
        if (properties.length == 0) {
            //console.log('No Propties');
            properties = $('#property-listing .prop-block')
        }
        
        var mapConfig;

        //console.log(properties.length);
        //var myLatlng = {lat: matchedAddressLat, lng: matchedAddressLng};
        
        if (properties.length) {
            var bounds = new google.maps.LatLngBounds();  
            var markers = [];
            var infoWindows = [];
            var totalLat = 0;
            var totalLng = 0;
            properties.each(function () {
                var nid = $(this).data('nid');
                var prop_title = $(this).data('title');
                var lat = parseFloat($(this).data('latitude'));
                var lng = parseFloat($(this).data('longitude'));
                totalLat += lat;
                totalLng += lng;

                //console.log('Lat Long',lat,lng);

                var latLng = new google.maps.LatLng(lat, lng);
                markers.push(new google.maps.Marker({
                    position: latLng,
                    //title: $(this).find('h3').text(),
                    title: prop_title,
                    nid: nid,
                    icon: '/themes/peco/images/marker.svg'
                    /*
                    icon: {
                    url: '/themes/peco/images/map.svg',
                    size: new google.maps.Size(16, 16),            
                    origin: new google.maps.Point(0, 0),
                    anchor: new google.maps.Point(16, 16)
                    }*/
                }));
                //$(this).parent('.views-prop-block').fadeIn();
                //$(this).find('.prop-image').fadeIn();
            });  

            // center map at average latitude and longitude
            var avgLat = totalLat / properties.length;
            var avgLng = totalLng / properties.length;

            mapConfig = {
              center: new google.maps.LatLng(avgLat, avgLng),
              zoom: 10,
              backgroundColor: '#0384fc'
            };
            map = new google.maps.Map(document.getElementById(prop_map_id), mapConfig);

            var bounds = new google.maps.LatLngBounds();
            var infoWindow = new google.maps.InfoWindow({ content: '' });
            for(var i=0;i<markers.length;i++) {
                  var myMarker = markers[i];
                  var myWindow = infoWindows[i];
                  // add markers to map
                  myMarker.setMap(map);

                  // fit the map to markers
                  bounds.extend(myMarker.getPosition());

                  // attach info windows to markers
                  myMarker.addListener('click', function () {
                    infoWindow.close();  
                    infoWindow.setContent(getPropertyInfo(this));
                    infoWindow.open(map, this);
                  });      
                  myMarker.addListener('mouseover', function () {
                      //infoWindow.close();
                      //infoWindow.setContent(getPropertyInfo(this));
                      //infoWindow.open(map, this);

                      var selMarker = this;
                      var nid = selMarker.nid;
                      var prop_title = selMarker.title.trim();
                      var prop_element = 'ajax-prop-'+nid;
                      var prop_text = $('#'+prop_element).text().trim();
                      var prop_html = 'Loading...';
                      infoWindow.setContent(prop_html);

                      //console.log('Marker', this.title, this);
                      //console.log(prop_text,prop_title);

                      infoWindow.close();
                      if (prop_text == prop_title) {   
                          $('#'+prop_element).once('attach-links').each(Drupal.behaviors.ajaxViewCustom.attachProp);                          
                          $('#'+prop_element).trigger('click');
                          setTimeout(
                              function(){
                                  infoWindow.setContent(getPropertyInfo(selMarker));
                                  infoWindow.open(map, selMarker);
                              }, 400);
                      } else {
                          infoWindow.setContent(getPropertyInfo(selMarker));
                          infoWindow.open(map, selMarker);
                      }

                  });
                  /*myMarker.addListener('mouseout', function () {
                  infoWindow.close();
                  });*/
            }
            map.fitBounds(bounds);

              // make sure we're not zoomed in too far
              var zoomChangeBoundsListener = 
              google.maps.event.addListenerOnce(map, 'bounds_changed', function(event) {
                if (this.getZoom() > 16){
                  this.setZoom(16);
                }
              });
            setTimeout(function(){google.maps.event.removeListener(zoomChangeBoundsListener)}, 2000);

            function getPropertyInfo(selMarker) {
                  var info;
                  var nid = selMarker.nid;
                  var prop_element = 'ajax-prop-'+nid;

                  /*
                  var prop_title = selMarker.title.trim();            
                  var prop_text = $('#'+prop_element).text().trim();
                  //console.log(prop_text,prop_title);
                  if (prop_text == prop_title) {   
                      $('#'+prop_element).trigger('click');
                  }*/

                  var prop_html = $('#'+prop_element).html();

                  //var prop_html = 'Prop '+nid;
                  info = '<div class="searchResultBox shadow-none"><div class="card">'+prop_html+'</div></div>';

                  return info;
            }

            /*properties.on('click mouseover', function () {
            infoWindow.close();  
            infoWindow.setContent(getPropertyInfo(markers[$(this).index()]));
            infoWindow.open(map, markers[$(this).index()]);
            });*/
        } else {
          // no properties, show US
          mapConfig = {
            center: new google.maps.LatLng(39, -95),
            zoom: 4
          };
          map = new google.maps.Map(document.getElementById(prop_map_id), mapConfig);
        }
        //$('.views-prop-block').fadeIn();
        //$('.prop-image').fadeIn();
      }

      // property detail map
      if ($('#property-detail-map-widget').length) {
        var lat = $('#property-detail-map-widget').data('latitude');
        var lng = $('#property-detail-map-widget').data('longitude');
        var latLng = new google.maps.LatLng(lat, lng);
        mapConfig = {
          center: latLng,
          zoom: 16,
          mapTypeId: google.maps.MapTypeId.HYBRID
        };
        map = new google.maps.Map(document.getElementById('property-detail-map-widget'), mapConfig);

        // single marker
        new google.maps.Marker({
          position: latLng,
          map: map
        });
      }
    }
    
    function initMapPropertyPortfolio () {
        
        var prop_map_id = 'map_property_portfolio';
        
        // if no map div then return
        if ($('#'+prop_map_id).length == 0) {            
            return;
        }
        
        //console.log('Map loading');
        var latlng = new google.maps.LatLng(39.5, -98.35);
        var myOptions = {
            zoom: 4,
            center: latlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };

        var map = new google.maps.Map(document.getElementById(prop_map_id),myOptions);
        /*
        var country = "United States"
        var geocoder = new google.maps.Geocoder();
	geocoder.geocode( { 'address': country }, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                map.setCenter(results[0].geometry.location);
            } else {
                alert("Could not find location: " + location);
            }
	});*/
    }
    
    $(document).ready(function ($) {    
        // property map pins by view ajax
        $('#ajax-view-property').trigger('click');
        //console.log('prop ajax');
        // property map pins by view ajax end 
        
        // initialize map, if present
        //initMap();
        //initMapPropertyPortfolio ();
        var interested_in_leasing_space_email = $(".prop-profile .modal .email a").text();
        $('input[name=interested_in_leasing_space]').val(interested_in_leasing_space_email);
        
        // property detail page
        var callrail = $('#callrail');
        
        if (callrail.length) {
            var callrail_html = callrail.html();
            var callrail_text = callrail.text().trim();
            
            var primary_mobile = $(".primary_leasing_contact .modal-body .mobile");
            
            if (primary_mobile.length) {
                $(".primary_leasing_contact .modal-body .mobile").remove();
            } 
            
            if (callrail_text != ''){
                //alert(callrail_html);
                $(".primary_leasing_contact .modal-body .agent-info").prepend(callrail_html);
            }
        }
        
        window.addEventListener('dfMessengerLoaded', function (event) {
          $r1 = document.querySelector("df-messenger");
          $r2 = $r1.shadowRoot.querySelector("df-messenger-chat");
          $r3 = $r2.shadowRoot.querySelector("df-messenger-user-input"); //for other mods
  
          var sheet = new CSSStyleSheet;
          sheet.replaceSync( `div.chat-wrapper[opened="true"] { max-height: 500px; height: calc(100vh - 120px); }`);
          $r2.shadowRoot.adoptedStyleSheets = [ sheet ];
  
          // MORE OF YOUR DIALOGFLOW MESSENGER CODE
      });
    });    
    /* Property Form and Map end */    
    $(window).on('load', function () {
        //$('.views-prop-block').fadeIn();
        //$('.searchResultBox').fadeIn('slow');
        $( ".searchResultBox" ).fadeIn(2000, function() {
            $('.prop-image').fadeIn("slow");
        });
        //$('.prop-image').fadeIn('slow');   
        
        //var properties = $("#ajax-view-property .prop-block-ajax");        
        //console.log('All Prop', properties.length);
        
        initMap();
        initMapPropertyPortfolio ();
        
        // again call map if properties list not available during running ajax call
        $(document).ajaxComplete(function(event, xhr, settings) {
            //console.log(settings.data);
            // see if it is from our view
            if (settings.data != undefined && settings.data.indexOf( "view_name=property_search&view_display_id=list") != -1) {
                //console.log('View Prop List ajax results!');
                initMap();
                initMapPropertyPortfolio ();
            }
        });
        
        
        // ajax call - don't call on load
        /*$('#ajax-get-property .prop-block-ajax').each(function(index) {
            var element_id = $(this).attr('id');
            //console.log('Ele Id', element_id);
            //$('#'+element_id).once('attach-links').each(this_item.attachProp);
            $('#'+element_id).trigger('click');
        });*/
        // ajax call end
    });
    /* Property Form and Map end */
    $(window).on('load', function () {
        //$('.views-prop-block').fadeIn();
        //$('.searchResultBox').fadeIn('slow');
        $( ".searchResultBox" ).fadeIn(2000, function() {
            $('.prop-image').fadeIn("slow");
        });
        //$('.prop-image').fadeIn('slow');   
        
        //var properties = $("#ajax-view-property .prop-block-ajax");        
        //console.log('All Prop', properties.length);
        
        initMap();
        initMapPropertyPortfolio ();
        
        // again call map if properties list not available during running ajax call
        $(document).ajaxComplete(function(event, xhr, settings) {
            //console.log(settings.data);
            // see if it is from our view
            if (settings.data.indexOf( "view_name=property_search&view_display_id=list") != -1) {
                //console.log('View Prop List ajax results!');
                initMap();
                initMapPropertyPortfolio ();
            }
        });
        
        
        // ajax call - don't call on load
        /*$('#ajax-get-property .prop-block-ajax').each(function(index) {
            var element_id = $(this).attr('id');
            //console.log('Ele Id', element_id);
            //$('#'+element_id).once('attach-links').each(this_item.attachProp);
            $('#'+element_id).trigger('click');
        });*/
        // ajax call end
    });
})(window.jQuery, window.Drupal);