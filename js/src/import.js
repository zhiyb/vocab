var import_words = [];

function importWordTable(e) {
  var html = '<table class="table table-condensed"><tbody>';
  var field;
  for (field in e) {
    if (field == 'word')
      continue;
    html = html.concat('<tr><th class="fit">', field, '</th><td ' + dispStyle(field) + '>', disp(e[field]), '</td></tr>');
  }
  return html.concat('</tbody></table>');
}

function importShowWord(word) {
  return '<li wid="N/A" class="list-group-item"><h4 class="list-group-item-heading" ' + dispStyle('word') + '>' + disp(word.word) + '<div class="btn-group"><button class="btn btn-sm btn-info word_copy"><span class="fa fa-copy"></span></button></div></h4><p class="list-group-item-text"><table><tbody>' + importWordTable(word) + '</tbody></table></li>';
}

function importCSV(contents) {
  var arrays = $.csv.toArrays(contents, {delimiter: '"', separator: ','});
  var html = '';
  import_words = [];
  var i;
  for (i in arrays) {
    var a = arrays[i];
    switch (a[2]) {
      case '': a[2] = a[1];
      case a[1]: a[1] = '';
    }
    var word = {word: a[2]};
    if (a[0])
      word.english = a[0];
    if (a[1])
      word.kana = a[1];
    if (a[3])
      word.type = a[3];
    import_words.push(word);
    html = html.concat(importShowWord(word));
  }
  var wl = $('#unit_import .word_list');
  wl.children('ul').html(html);
  wl.show(ani);
}

function importUnit() {
  import_words = [];
  $('#unit_import .texteditor input').val(unit);
  $('#unit_import #csv_file').val('');
  $('#unit_import .word_list').children('ul').html('');
  $('#unit_import').modal();
}

function importCSVFile(file) {
  if (!file)
    return;
  var reader = new FileReader();
  reader.onload = function(e) {importCSV(e.target.result);}
  reader.readAsText(file);
}

function importSubmit() {
  if (import_words.length == 0) {
    alert('No words imported');
    return;
  }
  var obj = {};
  obj.sid = section;
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
    if (obj.unit == unit) {
      refreshWords(unit);
    } else {
      refreshUnits(section);
      switchUnit(obj.unit, true);
    }
    alert('Imported ' + obj.cnt + ' of ' + import_words.length + ' words to unit ' + obj.unit);
  });
  $('#unit_import').modal('hide');
}
