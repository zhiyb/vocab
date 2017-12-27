var section = ''
var secobj = {};
var style = {}, weight = {};

function addSection()
{
  var name = prompt('Add new section:');
  if (name == null || name == '')
    return;
  $.post('set/new_section.php', 'name=' + name, refreshSections);
}

function refreshSections()
{
  $.getJSON("get/section_list.php", function(res) {
    var html = '';
    for (i in res) {
      var obj = res[i];
      var id = obj['id'];
      var name = obj['name'];
      html = html.concat('<a class="nav-item nav-link align-self-end" href="#section" section="', id, '" data-toggle="tab">', disp(name), '</a>');
    }
    html = html.concat('<a class="nav-item nav-link align-self-end" href="#section" section="add"><span class="fa fa-plus"></span></a>');
    $('#sectabs').html(html);
    refreshUnits(null);
    section = '';
    $('#edit_section').hide(ani);
    $('#edit_word').hide(ani);
  });
}

function refreshEditSection()
{
  $('#section_id').text('Edit section #' + secobj.id);
  $('#section_name textarea').val(secobj.name);
  autosize.update($('#section_name textarea'));
  $('#section_style').html(jsonedit(secobj.style));
  $('#section_weight').html(jsonedit(secobj.weight));
  autosize($('#edit_section textarea'));
}
