var ani = 120;  // Animation speed
var style = {};
var hideStyle = 'color: black; background-color: black';

function nameEditor(name, value)
{
  return '<div class="input-group nameeditor"><span class="input-group-btn"><button class="btn btn-danger"><span class="fa fa-trash"></span></button><button class="btn btn-warning"><span class="fa fa-times"></span></button><button class="btn btn-success"><span class="fa fa-check"></span></button></span><textarea class="form-control" type="text" rows=1 placeholder="' + name + '">' + value + '</textarea></div>';
}

function textEditor(name, value)
{
  return '<div class="input-group texteditor"><span class="input-group-addon">' + name + '</span><input class="form-control" type="text" value="' + value + '"></div>';
}

// HTML word display
function disp(s, hide) {
  hide = hide ? hideStyle : '';
  return s.replace(/\\n/g, '\n').replace(/<[^>]*>/g, function(s) {
    var href = s.substring(1, s.length - 1);
    return '<a href="' + href + '">' + href + '</a>'
  }).replace(/\n/g, '|\n').replace(/[^|\n]*(\||$)/g, function(s) {
    if (s.search('`') == -1)
      return s;
    return '<ruby>' + s.replace(/`[^`]*?(`|$)/g, function(s) {
      return '<rp>(</rp><rt style="' + hide + '">' + s + '</rt><rp>)</rp>';
    }) + '</ruby>';
  }).replace(/[|`]/g, '').replace(/\n/g, '<br>');
}

function dispStyle(type, hide) {
  hide = hide ? hideStyle : '';
  var s = 'style="';
  if (type in style)
    s = s.concat(style[type]);
  return s.concat(hide, '"');
}

function wordDisp(obj) {
  var info = {};
  try {
    info = JSON.parse(obj.info);
  } catch (e) {
    alert(e);
    return e;
  };
  var html = '<table class="table table-condensed"><tbody>';
  for (field in info) {
    html = html.concat('<tr><th class="fit">', field, '</th><td ' + dispStyle(field) + '>', disp(info[field]), '</td></tr>');
  }
  return html.concat('</tbody></table>');
}

function wordElement(word, hide) {
  return '<li class="list-group-item" wid="' + word.id + '"><h4 class="list-group-item-heading"><span ' + dispStyle('word', hide) + '>' + disp(word.word, hide) + '</span><div class="btn-group"><button class="btn btn-sm btn-warning"><span class="fa fa-pencil"></span></button><button class="btn btn-sm btn-info word_copy"><span class="fa fa-copy"></span></button></div></h4><p class="list-group-item-text"><table><tbody>' + wordDisp(word) + '</tbody></table></li>';
}

// JSON editor functions
function jsonedit(json) {
  if (json === "")
    return '';
  var obj = {};
  try {
    obj = JSON.parse(json);
  } catch (e) {
    alert(e);
  };
  var html = '';
  for (field in obj)
    html = html.concat(jsonfield(field, obj[field]));
  return html.concat(jsonfield('', ''));
}

function jsonfield(field, value) {
  return '<tr><th class="fit"><div class="input-group"><div class="input-group-btn"><button class="btn ' +
    (field ? 'btn-danger' : 'btn-success') + '"><span class="fa ' + (field ? 'fa-times' : 'fa-plus') +
    '"></span></button></div><input class="form-control" type="text" id="field" value="' + field +
    '"></div></th><td><textarea class="form-control" type="text" rows=1 id="value">' + value + '</textarea></td></tr>';
}

function jsonobj(table) {
  var obj = {};
  $(table).find("tr").each(function() {
    var field = $(this).find("th input").val();
    var value = $(this).find("td textarea").val();
    if (field != '' && value != '')
      obj[field] = value;
  });
  return obj;
}
