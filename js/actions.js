// JSON editor 'add'
$('body').on('click', '.jsonedit .btn-success', function() {
  var tr = $(this).closest('tr');
  var field = tr.find('#field').val();
  var value = tr.find('#value').val();
  if (field === "")
    return;
  $(this).removeClass('btn-success');
  $(this).addClass('btn-danger');
  $(this).children().removeClass('fa-plus');
  $(this).children().addClass('fa-times');
  tr.parent().append(jsonfield('', ''));
  autosize(tr.parent().find('tr').last().find('textarea'));
});

// JSON editor 'remove'
$('body').on('click', '.jsonedit .btn-danger', function() {
  $(this).closest('tr').remove();
});

// Section tabs 'click'
$('#sectabs').on('click', 'a', function() {
  var secid = $(this).attr('section');
  if (secid == "add")
    addSection();
  else if (secid != section) {
    var a = $(this);
    $.getJSON("get/section.php?id=" + secid, function(obj) {
      refreshUnits(secid);
      secobj = obj;
      section = obj.id;
      style = JSON.parse(obj.style);
      if (style == null)
        style = {};
      weight = JSON.parse(obj.weight);
      if (weight == null)
        weight = {};
      a.closest('nav').find('.btn-warning').remove();
      a.append('<button class="btn btn-xs btn-warning"><span class="fa fa-pencil"></span></button>');
      $('#edit_section').hide(ani);
      $('#edit_word').show(ani);
    });
  }
});

// Section tabs 'edit'
$('#sectabs').on('click', '.btn-warning', function() {
  $('#edit_section').toggle(ani, function() {
    refreshEditSection();
  });
});

// Section editor header 'click'
$('#edit_section .card-header').click(function() {$('#edit_section').hide(ani);});

// Section editor 'cancel'
$('#section_name .nameeditor .btn-warning').click(function() {$('#edit_section').hide(ani);});

// Section editor 'submit'
$('#section_name .nameeditor .btn-success').click(function() {
  var obj = {id: section};
  obj['name'] = $('#section_name textarea').val();
  obj['style'] = JSON.stringify(jsonobj($('#section_style')));
  obj['weight'] = JSON.stringify(jsonobj($('#section_weight')));
  $.post("set/section.php", JSON.stringify(obj), function(res) {
    if (res !== 'OK')
      alert("Save unsuccessful");
    else
      refreshSections();
  });
});

// Section editor 'remove'
$('#section_name .nameeditor .btn-danger').click(function() {
  if (confirm('DELETE section #' + section + '?'))
    $.post('set/delete_section.php', 'id=' + section, refreshSections);
});

// Unit tabs 'click'
$('#unittabs').on('click', 'a', function() {
  if ($(this).attr('type') == 'import') {
    importUnit();
    return;
  }
  unit = $(this).attr('name');
  if (unit == '')
    return;
  refreshWords(unit);
  refreshEditWord(unit);
});

// Unit tabs 'import'
$('#unit_import').on('shown.bs.modal', function() {autosize.update($('#unit_import textarea'));});

// Unit import Anki CSV file 'open'
$('#unit_import #csv_file').on('change', function(e) {
  var file = e.target.files[0];
  if (!file)
    return;
  var reader = new FileReader();
  reader.onload = function(e) {
    var contents = e.target.result;
    importCSV(contents);
  }
  reader.readAsText(file);
});

// Unit import button 'submit'
$('#unit_import .btn-primary').click(function() {
  if (import_words.length == 0) {
    alert('No words imported');
    return;
  }
  var obj = {};
  obj.id = section;
  obj.unit = $('#unit_import .texteditor input').val();
  obj.payload = import_words;
  $.post('set/import_unit.php', JSON.stringify(obj), function(ret) {
    try {
      var obj = JSON.parse(ret);
    } catch (e) {
      alert(e);
    }
    if (obj == null)
      return;
    if (obj.unit == unit)
      refreshWords(unit);
    else
      refreshUnits(section);
    alert('Imported ' + obj.cnt + ' of ' + import_words.length + ' words to unit ' + obj.unit);
  });
  $('#unit_import').modal('hide');
});

// Word list 'edit' or word editor 'cancel'
$('.word_list').on('click', '.btn-warning', function() {
  var item = $(this).closest('li');
  if (item.hasClass('list-group-item-warning')) {
    // Discard changes
    refreshWord(item);
  } else {
    // Display editing panel
    var wid = item.attr('wid');
    $.getJSON('get/word.php?id=' + section + '&wid=' + wid, function(obj) {
      if (obj == null)
        return;
      item.addClass('list-group-item-warning');
      item.html(textEditor('Unit', unit == '(default)' ? '' : unit) + '<p>' + nameEditor('Word', obj.word) + '<p><table class="table"><tbody class="jsonedit info">' + jsonedit(obj['info']) + '</tbody></table>');
      autosize(item.find('textarea'));
    });
  }
});

// Word editor 'submit'
$('.word_list').on('click', '.nameeditor .btn-success', function() {
  var item = $(this).closest('li');
  var obj = {id: section, wid: item.attr('wid'), unit: item.find('.texteditor input').val(),
    word: item.find('.nameeditor textarea').val(), info: JSON.stringify(jsonobj(item.find('tbody.info')))};
  if (obj.id == '' || obj.word == '')
    return;
  $.post('set/word.php', JSON.stringify(obj), function(ret) {
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
});

// Word editor 'remove'
$('.word_list').on('click', '.nameeditor .btn-danger', function() {
  var item = $(this).closest('li');
  var wid = item.attr('wid');
  if (confirm('DELETE word #' + wid + '?'))
    $.post('set/delete_word.php', 'id=' + section + '&wid=' + wid, function() {
      refreshWord(item);
      refreshUnits(section);
    });
});

// Word copy dialog 'show'
$('#word_copy').on('shown.bs.modal', function() {autosize.update($('#word_copy textarea'));});

// Word list 'copy'
$('.word_list').on('click', '.btn-info', function() {
  var wid = $(this).closest('li').attr('wid');
  var html = $(this).closest('.list-group-item-heading').html().replace(/<div((?!<\/div>).)*<\/div>/g, '');
  var text = html.replace(/<br[^>]*>/g, '\n').replace(/<rp>((?!<\/rp>).)*<\/rp>/g, '').replace(/<rt>((?!<\/rt>).)*<\/rt>/g, '').replace(/<[^>]*>/g, '');
  var ruby = html.replace(/<br[^>]*>/g, '\n').replace(/<rp>[^<]*<\/rp>/g, '').replace(/<ruby>((?!(<rt>|<\/ruby>)).)*<rt>/g, '').replace(/<\/rt>((?!(<rt>|<\/ruby>)).)*<rt>/g, '').replace(/<[^>]*>/g, '');
  var plain = html.replace(/<br[^>]*>/g, '\n').replace(/<\/?rt>|<\/?rp>|<\/?ruby>/g, '');
  var ta = $('#word_copy textarea');
  var f = $('#word_copy .copy_field');
  if (plain != text)
    f.eq(0).show(0);
  else
    f.eq(0).hide(0);
  if (ruby != text)
    f.eq(1).show(0);
  else
    f.eq(1).hide(0);
  if (html != text)
    f.eq(3).show(0);
  else
    f.eq(3).hide(0);
  ta.eq(0).val(plain);
  ta.eq(1).val(ruby);
  ta.eq(2).val(text);
  ta.eq(3).val(html);
  $('#word_copy .modal-title').text('Word #' + wid + ':');
  $('#word_copy').modal();
});

// New word editor header 'click'
$('#edit_word .card-header').click(function() {$('#word_body').toggle(ani);});
// New word editor 'cancel'
$('#word_body .nameeditor .btn-warning').click(function() {$('#word_body').hide(ani);});

// New word editor 'submit'
$('#word_body .nameeditor .btn-success').click(function() {
  var obj = {id: section, wid: null, unit: $('#word_body .texteditor input').val(), word: $('#word_body .nameeditor textarea').val(), info: JSON.stringify(jsonobj($('#word_info')))};
  if (obj.id == '' || obj.word == '')
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

// Initialise
autosize($('textarea'));
refreshSections();
