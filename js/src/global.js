var ani = 120;  // Animation speed
var style = {};
var hideStyle = 'color: black; background-color: black';

// Text functions
function rubyConvert(a, b) {
  var s = 'x' + a + 'x|x' + b + 'x';
  s = s.replace(/(.+)(.+?)(?=(.+).*\|.*\1(((?!\2).)+?)\3)/g,
      function(s0, s1, s2, s3, s4, s5) {
        if (s2.includes(s4))
          return s1 + s2;
        else
          return s1 + '|' + s2 + '`' + s4 + '|';
      }).replace(/\|[^|]*$/, '').replace(/^\|*x\|*/, '').replace(/\|*x\|*$/, '');
  if (s.replace(/`[^`|]*(?:[`|]|$)/g, '').replace(/[`|]/g, '') == a) {
    if (s.replace(/(?:^|[`|]+)[^`|]*`/g, '').replace(/[`|]/g, '') == b)
      return s;
    alert('Ruby mismatch');
  }
  alert('Base mismatch');
  return a + '`' + b;
}

// HTML word display
function disp(s, hide) {
  hide = hide ? ' style="' + hideStyle + '"' : '';
  return s.replace(/\\n/g, '\n').replace(/<[^>]*>/g, function(s) {
    var href = s.slice(1, -1);
    return '<a href="' + href + '">' + href + '</a>'
  }).replace(/`[^|`\n]+`/g, function(s) {
    return s.slice(0, -1) + '|';
  }).replace(/[^|`\n]+`[^|`\n]+/g, function(s) {
    return '<ruby>' + s.replace('`', '<rp>(</rp><rt' + hide + '>') + '</rt><rp>)</rp></ruby>';
  }).replace(/\|/g, '').replace(/\n/g, '<br>');
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
  return '<li class="list-group-item" wid="' + word.id + '"><h4 class="list-group-item-heading"><span ' + dispStyle('word', hide) + '>' + disp(word.word, hide) + '</span>' + (hide ? '' : '<div class="btn-group"><button class="btn btn-sm btn-warning"><span class="fa fa-pencil"></span></button><button class="btn btn-sm btn-info word_copy"><span class="fa fa-copy"></span></button></div>') + '</h4><p class="list-group-item-text"><table><tbody>' + wordDisp(word) + '</tbody></table></li>';
}

// Common editors
function nameEditor(name, value)
{
  return '<div class="input-group nameeditor"><div class="input-group-prepend"><button class="btn btn-danger"><span class="fa fa-trash"></span></button><button class="btn btn-warning"><span class="fa fa-times"></span></button><button class="btn btn-success"><span class="fa fa-check"></span></button></div><textarea class="form-control" type="text" rows=1 placeholder="' + name + '">' + value + '</textarea></div>';
}

function textEditor(name, value)
{
  return '<div class="input-group texteditor"><div class="input-group-prepend"><span class="input-group-text">' + name + '</span></div><input class="form-control" type="text" value="' + value + '"></div>';
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
  return '<tr><th><div class="input-group"><div class="input-group-prepend"><button class="btn ' +
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
