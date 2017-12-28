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
      var active = '';
      if (name == unit) {
        active = 'active';
        found = true;
      }
      html = html.concat('<a class="nav-item ' + active + ' nav-link align-self-end" href="#unit" name="', name, '" data-toggle="pill" role="tab">', disp(name), '</a>');
    }
    html = html.concat('<a class="nav-item nav-link align-self-end" href="#unit" type="import"><span class="fa fa-upload"></span></a>');
    $('#unittabs').html(html);
    if (found == false)
      $('.container > .word_list').hide(ani);
  });
}

function switchUnit(u, act) {
  unit = u;
  if (unit == '')
    return;
  refreshWords(unit);
  refreshEditWord(unit);
  if (!act)
    return;
  $('#unittabs nav-link').removeClass("active");
  $('#unittabs nav-link[name="' + unit + '"]').addClass("active");
}

// Unit tabs 'click'
$('#unittabs').on('click', 'a', function() {
  if ($(this).attr('type') == 'import') {
    importUnit();
    return;
  }
  switchUnit($(this).attr('name'), false);
});

// Unit tabs 'import'
$('#unit_import').on('shown.bs.modal', function() {autosize.update($('#unit_import textarea'));});
// Unit import Anki CSV file 'open'
$('#unit_import #csv_file').on('change', function(e) {importCSVFile(e.target.files[0]);});
// Unit import button 'submit'
$('#unit_import .btn-primary').click(importSubmit);
