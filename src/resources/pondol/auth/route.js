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

var AUTH = {
  routetostring: function(route, callback) {
    var routedata = $.param( route);

    $.ajax({
      url: '/auth/route-url',
      type: 'GET',
      data: routedata,
      success: function(url) {
        callback({error: false, url});
      }
    });
  },
  ajaxroute: function(type, route, data, callback) {
    var routedata = $.param( route);
    onprocessing = true;

    console.log('ajaxroute >> csrf_token >>', csrf_token);
    console.log('ajaxroute >> data >>', data);
    $.ajax({
      url: '/auth/route-url',
      type: 'GET',
      data: routedata,
      success: function(url) {
        data._token = csrf_token;
        $.ajax({
          url: url,
          type: type,
          data: data,
          success: function(resp) {
            onprocessing = false;
            callback(resp);
          }
        });
      }
    });
  }
};