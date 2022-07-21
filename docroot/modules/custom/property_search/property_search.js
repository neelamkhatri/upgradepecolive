(function ($) {
  Drupal.behaviors.ajaxViewCustom = {
    attach: function (context, settings) {
        // Attach ajax action click event of each view column.
        //console.log('view-property'); 
        $('#ajax-view-property').once('attach-links').each(this.attachLink);
        //js-view-dom-id-ajax-view-property
        
        /*$('#ajax-view-property .prop-block-ajax').each(function(index) {
            var nid = $(this).data('nid');
            var prop_cls = 'ajax-prop-'+nid;
            var column = $('.'+prop_cls);
            console.log( index + ": " + $( this ).text() );
        });*/
        //$('#ajax-view-property .prop-block-ajax').once('attach-links').each(this.attachProp);
    },
    attachProp: function (idx, column) {
        var nid = $(this).data('nid');
        var prop_cls = 'ajax-prop-'+nid;
        //console.log('attachProp', idx, prop_cls, column);
        // Everything we need to specify about the view.
        
        //var column = '#searchList';
        var view_info = {
          view_name: 'property_ajax',
          view_display_id: 'property_detail',
          view_args: nid,
          view_dom_id: prop_cls
        };
 
        // Details of the ajax action.
        var ajax_settings = {
          async: true,
          submit: view_info,
          url: '/views/ajax',
          element: column,
          event: 'click',
          method: 'replaceWith', // Will be rewritten with ajax command.
          progress: {
            type: 'throbber'
          }
        };

        Drupal.ajax(ajax_settings);
    },
    attachLink: function (idx, column) {
        //console.log('attachLink', idx, column);
        // Everything we need to specify about the view.
        //var nid = '679';
        //var column = '#searchList';
        var currentPath = drupalSettings.path.currentPath;
            currentPath = currentPath.replace('properties/','');
            currentPath = currentPath.replace('property-search/','');
            currentPath = currentPath.replace('property-search','');
        //var currentQuery = drupalSettings.path.currentQuery;
        var queryString = location.search;
        //console.log('URL', drupalSettings.path, queryString);
        
        var view_info = {
          view_name: 'property_search',
          view_display_id: 'list',
          //view_args: nid,
          //view_args: 'arizona/all/all?contact=All&population=All&gla=All&available=All&income=All&sqft[min]=&sqft[max]=&use_type=821&title=&overview=&tenant=&agent_name=&state=&city=&address=',
          view_args: currentPath,
          view_dom_id: 'ajax-view-property',
          //view_dom_id: 'search-result'
        };
 
        // Details of the ajax action.
        var ajax_settings = {
          submit: view_info,
          //data: currentQuery,
          url: '/views/ajax'+queryString,
          element: column,
          event: 'click',
          method: 'replaceWith', // Will be rewritten with ajax command.
          progress: {
            type: 'throbber'
          }
        };

        Drupal.ajax(ajax_settings);
    }
  };
})(jQuery);