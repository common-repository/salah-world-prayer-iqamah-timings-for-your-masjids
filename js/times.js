
jQuery(document).ready(function($) {

  var data = {
    action: 'my_action',
    security : MyAjax.security,
    whatever: 1234
  };

  // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
  $.post(MyAjax.ajaxurl, data, function(response) {
    alert('Got this from the server: ' + response);
  });
});