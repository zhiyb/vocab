var unit = '';

function refreshUnits(secid) {
  if (secid == null) {
    $('#unittabs').html('');
    unit = '';
    $('.container > .word_list').hide(ani);
    return;
  }
  if (secid != section)
      unit = '';
  $.getJSON('get/unit_list.php?id=' + secid, function(array) {
    var found = false;
    var html = '';
    for (i in array) {
      var name = array[i][0];
      if (name == unit) {
        html = html.concat('<a class="nav-item nav-link align-self-end" href="#unit" name="', name, '" data-toggle="pill" role="tab">', disp(name), '</a>');
        found = true;
      } else {
        html = html.concat('<a class="nav-item nav-link align-self-end" href="#unit" name="', name, '" data-toggle="pill" role="tab">', disp(name), '</a>');
      }
    }
    html = html.concat('<a class="nav-item nav-link align-self-end" href="#unit" type="import"><span class="fa fa-upload"></span></a>');
    $('#unittabs').html(html);
    if (found == false)
      $('.container > .word_list').hide(ani);
  });
}
