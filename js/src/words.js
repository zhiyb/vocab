function refreshWords(unit)
{
  var wl = $('.container > .word_list');
  if (unit == '') {
    wl.children('ul').html('');
    return;
  }
  $.getJSON('get/word_list.php?sid=' + section + '&unit=' + unit, function(array) {
    var html = '';
    for (i in array)
      html = html.concat(wordElement(array[i]));
    wl.children('ul').html(html);
    wl.show(ani);
  });
}

function refreshWord(item)
{
  $.getJSON('get/word.php?sid=' + section + '&id=' + item.attr('wid'), function(word) {
    if (word == null) {
      item.hide(ani, item.remove);
      return;
    }
    item.removeClass('list-group-item-warning');
    var h = false;
    if (typeof hide !== 'undefined')
      h = hide;
    item.replaceWith(wordElement(word, h));
    if (typeof words !== 'undefined')
      words[index] = word;
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

function editWord(word, item)
{
  if (!word.id || !word.sid || word.word == '')
    return;
  $.post('set/word.php', JSON.stringify(word), function(ret) {
    try {
      var obj = JSON.parse(ret);
    } catch (e) {
      alert(e);
    }
    if (obj == null || obj.cnt != 1)
      return;
    // Remove the word if unit is different, otherwise refresh it
    if (obj.unit != unit) {
      refreshUnits(section);
      item.hide(ani, item.remove);
    } else
      refreshWord(item);
  });
}

// Word list 'edit' or word editor 'cancel'
$('.word_list').on('click', '.btn-warning', function() {
  var item = $(this).closest('li');
  if (item.hasClass('list-group-item-warning')) {
    // Discard changes
    refreshWord(item);
  } else {
    // Display editing panel
    var wid = item.attr('wid');
    $.getJSON('get/word.php?id=' + wid, function(word) {
      if (word == null)
        return;
      item.addClass('list-group-item-warning');
      item.html(textEditor('Unit', word.unit == '(default)' ? '' : word.unit) + '<p>' + nameEditor('Word', word.word) + '<p><table class="table"><tbody class="jsonedit info">' + jsonedit(word.info) + '</tbody></table>');
      autosize(item.find('textarea'));
    });
  }
});

// Word editor 'submit'
$('.word_list').on('click', '.nameeditor .btn-success', function() {
  var item = $(this).closest('li');
  var obj = {sid: section, id: item.attr('wid'), unit: item.find('.texteditor input').val(),
    word: item.find('.nameeditor textarea').val(), info: JSON.stringify(jsonobj(item.find('tbody.info')))};
  editWord(obj, item);
});

// Word editor 'remove'
$('.word_list').on('click', '.nameeditor .btn-danger', function() {
  var item = $(this).closest('li');
  var wid = item.attr('wid');
  if (confirm('DELETE word #' + wid + '?'))
    $.post('set/delete_word.php', 'id=' + wid, function() {
      refreshWord(item);
      refreshUnits(section);
    });
});

// Word list 'magic'
$('.word_list').on('click', '.word_magic', function() {
  var item = $(this).closest('li');
  var id = item.attr('wid');
  if (!id)
    return;
  $.getJSON('get/word.php?id=' + id, function(word) {
      if (word == null)
        return;
      var info = JSON.parse(word.info);
      if (!word.word || !info.kana)
        return;
      word.word = rubyConvert(word.word, info.kana);
      delete info.kana;
      word.info = JSON.stringify(info);
      editWord(word, item);
  });
});

// New word editor header 'click'
$('#edit_word .card-header').click(function() {$('#word_body').toggle(ani);});
// New word editor 'cancel'
$('#word_body .nameeditor .btn-warning').click(function() {$('#word_body').hide(ani);});

// New word editor 'submit'
$('#word_body .nameeditor .btn-success').click(function() {
  var obj = {sid: section, wid: null, unit: $('#word_body .texteditor input').val(), word: $('#word_body .nameeditor textarea').val(), info: JSON.stringify(jsonobj($('#word_info')))};
  if (obj.sid == '' || obj.word == '')
    return;
  $.post('set/word.php', JSON.stringify(obj), function(ret) {
    try {
      var obj = JSON.parse(ret);
    } catch (e) {
      alert(e);
    }
    if (obj == null || obj.cnt != 1)
      return;
    if (obj.unit == unit)
      refreshWords(unit);
    else
      refreshUnits(section);
    $('#word_body .nameeditor textarea').val('');
    $('#word_info td textarea').val('');
    autosize.update($('#edit_word textarea'));
  });
});

// New word editor 'clean'
$('#word_body .nameeditor .btn-danger').click(function() {refreshEditWord(unit);});
