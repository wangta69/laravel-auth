var csrf_token = $("meta[name=csrf-token]" ).attr("content");
var onprocessing = false;
// serializeObject for jQuery 
$.fn.serializeObject = function() {
  "use strict"
  var result = {}
  var extend = function(i, element) {
    var node = result[element.name]
    if ("undefined" !== typeof node && node !== null) {
      if (Array.isArray(node)) {
        node.push(element.value)
      } else {
        result[element.name] = [node, element.value]
      }
    } else {
      result[element.name] = element.value
    }
  }

  $.each(this.serializeArray(), extend)
  return result
}

var ROUTE = {
  routetostring: function(params, callback) {

    $.ajax({
      url: '/route-url',
      type: 'GET',
      data: $.param({route: params.route, segments: params.segments}),
      success: function(url) {
        callback({error: false, url});
      }
    });
  }, 
  ajaxroute: function(type, params, callback) {
    // {route: '', segments: [], data: {}}
    // var routedata = $.param( route);
    if (onprocessing === false) {
      onprocessing = true;
      params.segments = params.segments || [];
      params.data = params.data || {};
      
      $.ajax({
        url: '/route-url',
        type: 'GET',
        data: $.param({route: params.route, segments: params.segments}),
        success: function(url) {
          params.data._token = csrf_token;
          $.ajax({
            url: url,
            type: type,
            data: params.data,
            success: function(resp) {
              onprocessing = false;
              callback(resp);
            },
            error : function(xhr, ajaxSettings, thrownError) 
            {
              console.log('xhr:', xhr);
              console.log('ajaxSettings:', ajaxSettings);
              console.log('thrownError:', thrownError);
            },
            complete : function()
            {
              onprocessing = false;
            }
          });
        }
      });
    }
  }
};