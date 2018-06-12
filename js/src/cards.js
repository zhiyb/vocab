var uid = null;
var index = 0;
var hide = true;
var words = [];
var section = 0;
var unit = '';
var text = '', annot = '';
var pass = false;
var progress = [];

function reduce(s)
{
  return s.replace(/^(.+)[(\[{「［（｛](.+)[-~―－‐＿～][)\]}」］）｝]/gm, function(m0, m1, m2) {return m2 + m1;})
    .replace(/[\s()\[\]{}\-_/\\~,.;:?]/g, ' ')
    .replace(/[―－‐＿　／・～、。；：？「」［］（）｛｝]/g, ' ')
    .replace(/(?:^|\s)([^\s]+)\s+([^\s]*?\1)/, function(m0, m1, m2) {return m2;})
    .replace(/(([^\s]+)[^\s]*?)\s+\2(?:$|\s)/, function(m0, m1) {return m1;})
    .replace(/\s/g, '');
}

function start(ws)
{
  index = 0;
  words = ws;
  update();

  // Word list
  var html = '';
  var i;
  for (i in words) {
    var word = words[i];
    html = html.concat('<li class="list-group-item">#' + word.id
        + ' ' + word.word + ' | ' + word.unit + ' #' + word.sid + '</li>');
  }
  $('div#words > ul').html(html);

  // Hide unit selection, show word card
  $('div#card').show(ani);
  $('div#sections').hide(ani);
  $("html, body").animate({scrollTop: 0}, "slow");
}

function back()
{
  // Hide word card, show section selection
  $('div#sections').show(ani);
  $('div#card').hide(ani);
  $("html, body").animate({scrollTop: 0}, "slow");
}

function update()
{
  if (index >= words.length) {
    back();
    return;
  }
  var word = words[index];
  var obj = {id: word.id, sid: word.sid};
  $.post('get/stats.php?uid=' + uid, JSON.stringify(obj), function(ret) {
    try {
      updateButtons(JSON.parse(ret));
    } catch (e) {
      alert(e);
    }
  });

  // Section info
  section = word.sid;
  unit = word.unit;
  try {
    style = JSON.parse(sections[word.sid].style);
    if (style == null)
      style = {};
  } catch (e) {
    style = {};
  }

  show(true);
  pass = false;
  $('#test').val('');
  $('#test').trigger('change');
}

function updateButtons(stats)
{
  if (!stats)
    stats = {yes: 0, skip: 0, no: 0};
  var num = function(i) {return i ? ' (' + i + ')' : '';};
  $('#buttons .btn-success').text('Yes' + num(stats.yes));
  $('#buttons .btn-warning').text('Skip' + num(stats.skip));
  $('#buttons .btn-danger').text('No' + num(stats.no));
}

function submit(type)
{
  if (index >= words.length) {
    back();
    return;
  }
  var word = words[index];
  var obj = {id: word.id, sid: word.sid, field: type};
  $.post('set/stats_increment.php?uid=' + uid, JSON.stringify(obj), function(ret) {
    try {
      updateButtons(JSON.parse(ret));
    } catch (e) {
      alert(e);
    }
    index++;
    update();
  });
}

function show(h)
{
  if (index >= words.length) {
    back();
    return;
  }
  var bar = $('div#card .progress > .bg-info');
  bar.text((index + 1) + '/' + words.length);
  bar.css('width', ((index + 1) * 100 / words.length) + '%');
  var word = words[index];
  $('div#card > ul').html(wordElement(word, h));
  hide = h;
  var html = disp(word.word);
  text = reduce(html2text(html));
  annot = reduce(html2annot(html));
}

function refreshSections(refresh)
{
}

function refreshUnits(sid)
{
  refreshWord($('.word_list li[wid]:first'));
}

function getSelections()
{
  var secs = [];
  $('li').each(function() {
    var sec = {sid: $(this).attr('sid'), units: []};
    $(this).find('input').each(function() {
      if (!$(this).is(':checked'))
        return;
      sec.units.push({unit: $(this).parents('.progress-btn').attr('unit')});
    });
    if (sec.units.length)
      secs.push(sec);
  });
  return secs;
}

// Start button
$('button#submit').click(function() {
  var secs = getSelections();
  if (!secs.length)
    alert('Please select one or some units');
  else
    $.post('get/random.php?uid=' + uid, JSON.stringify(secs), function(ret) {
      try {
        var obj = JSON.parse(ret);
        start(obj);
        // Calculate progress bars
        var po = {'total': 0, 'new': 0, 'pass': 0, 'fail': 0};
        for (i in secs) {
          var sec = secs[i];
          for (j in sec.units) {
            var p = progress.filter(function(a) {return a.sid == sec.sid && a.unit == sec.units[j].unit})[0];
            po.total += p.total;
            po['new'] += p['new'];
            po.pass += p.pass;
            po.fail += p.fail;
          }
        }
        var pg = $('div#card .progress');
        pg.children('.bg-secondary').css('width', 100 * po['new'] / po.total + '%');
        pg.children('.bg-danger').css('width', 100 * po.fail / po.total + '%');
        pg.children('.bg-warning').css('width', 100 * (po.total - po['new'] - po.pass - po.fail) / po.total + '%');
        console.log(JSON.stringify(po));
      } catch (e) {
        alert(e + ":\n" + ret);
      }
    });
});

// Clean button
$('button#clean').click(function() {
  var secs = getSelections();
  if (!secs.length) {
    alert('Please select one or some units to clean statistics');
  } else {
    var i, unit, s = 'Please confirm cleaning stats for:\n';
    for (i in secs) {
      var sec = secs[i];
      s = s.concat('#' + sec.sid + ': ');
      for (j in sec.units)
        s = s.concat((j != 0 ? ', ' : '') + sec.units[j].unit);
      s = s.concat('\n');
    }
    if (confirm(s) == true)
      $.post('set/stats_clean.php?uid=' + uid, JSON.stringify(secs), function(ret) {
        location.reload();
      });
  }
});

// Text input field
$('#test').on('input', function(e) {
  var s = reduce(e.target.value);
  if (e.target.value === '?' || e.target.value === '？') {
    show(false);
  } else if (s === text || s === annot) {
    if (hide) {
      $(this).removeClass('text-warning text-danger');
      $(this).addClass('text-success');
      pass = true;
      show(false);
    } else if (!pass) {
      $(this).removeClass('text-success text-danger');
      $(this).addClass('text-warning');
    }
  } else {
    $(this).removeClass('text-success text-warning');
    $(this).addClass('text-danger');
    pass = false;
  }
});

$('#test').on('keyup', function(e) {
  var s = reduce(e.target.value);
  if (e.which == 13) {
    if (s === text || s === annot) {
      if (pass)
        submit('yes');
      else
        submit('skip');
    } else if (e.shiftKey) {
      submit('no');
    }
  }
});

// Control buttons
$('#buttons .btn-primary').click(function() {show(!hide);});
$('#buttons .btn-success').click(function() {submit('yes');});
$('#buttons .btn-warning').click(function() {submit('skip');});
$('#buttons .btn-danger').click(function() {submit('no');});
$('#buttons .btn-secondary').click(back);

// Update progress
function updateProgress() {
  $.getJSON('get/progress.php?uid=' + uid, function(obj) {
    progress = obj;
    for (var i in obj) {
      var o = obj[i];
      var sid = o.sid;
      var unit = o.unit;
      var e = $('#sections li[sid="' + sid + '"] .progress-btn[unit="' + unit + '"]');
      e.find('.bg-success').css('width', 100 * o.pass / o.total + '%');
      e.find('.bg-warning').css('width', 100 * (o.total - o['new'] - o.pass - o.fail) / o.total + '%');
      e.find('.bg-danger').css('width', 100 * o.fail / o.total + '%');
    }
  });
}

// Show login dialog
function userLogin() {
  $('#ulogin').modal();
}

// Login dialog
$('#ulogin').on('shown.bs.modal', function() {$('#ulogin input').focus();});

// Login submit
$('#ulogin .btn-primary').click(function() {
  val = $('#ulogin input').val().trim();
  if (!val) {
    alert('Please provide a token string as identifier');
    return;
  }
  $.post('get/hash.php', $('#ulogin input').val(), function(ret) {
    uid = ret;
    updateProgress();
    $('#uid').text(uid);
    $('#ulogin').modal('hide');
  });
});

if (!uid)
  userLogin();
