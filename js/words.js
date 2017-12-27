function refreshWords(unit)
{
  var wl = $('.container > .word_list');
  if (unit == '') {
    wl.children('ul').html('');
    return;
  }
  $.getJSON('get/word_list.php?id=' + section + '&unit=' + unit, function(array) {
    var html = '';
    for (i in array)
      html = html.concat(wordElement(array[i]));
    wl.children('ul').html(html);
    wl.show(ani);
  });
}

function refreshWord(item)
{
  $.getJSON('get/word.php?id=' + section + '&wid=' + item.attr('wid'), function(obj) {
    if (obj == null) {
      item.hide(ani, item.remove);
      return;
    }
    item.removeClass('list-group-item-warning');
    item.replaceWith(wordElement(obj));
  });
}

function refreshEditWord(unit)
{
  $('#word_body .texteditor input').val(unit == '(default)' ? '' : unit);
  $('#word_body .nameeditor textarea').val('');
  autosize.update($('#word_body .nameeditor textarea'));
  $('#word_info').html(jsonfield('', ''));
  autosize($('#edit_word textarea'));
}
