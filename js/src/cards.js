var uid = null;
var index = 0;
var hide = true;
var words = [];
var word = [];
var section = 0;
var unit = '';
var text = '', annot = '';
var pass = false;
var progress = [];
var pbar = {'total': 0, 'new': 0, 'pass': 0, 'fail': 0};
var maxweight = 5;    // Maximum absolute weight value
var scale = 2;        // Slider value between [-scale, scale]
var weight = 0;
var origin = 0;
var submitting = false;

function reduce(s)
{
  return s.replace(/^(.+)[(\[{「［（｛](.+)[-~―－‐＿～][)\]}」］）｝]/gm, function(m0, m1, m2) {return m2 + m1;})
    .replace(/[\s()\[\]{}\-_/\\~,.;:?]/g, ' ')
    .replace(/[―－‐＿　／・～、。；：？「」［］（）｛｝＊…]/g, ' ')
    .replace(/(?:^|\s)([^\s]+)\s+([^\s]*?\1)/, function(m0, m1, m2) {return m2;})
    .replace(/(([^\s]+)[^\s]*?)\s+\2(?:$|\s)/, function(m0, m1) {return m1;})
    .replace(/\s/g, '');
}

function sessionSave()
{
  // Clear session data at the end
  if (index >= words.length)
    $.post('set/session_update.php?uid=' + uid);
  else
    $.post('set/session_update.php?uid=' + uid + '&index=' + index, JSON.stringify({pbar: pbar, words: words}));
}

function sessionUpdate()
{
  // Clear session data at the end
  if (index >= words.length)
    $.post('set/session_update.php?uid=' + uid);
  else
    $.get('set/session_update.php?uid=' + uid + '&index=' + index);
}

function sessionRestore()
{
  $.getJSON('get/session.php?uid=' + uid, function (obj) {
    if (obj == null || obj.data == null || obj.data === '')
      return;
    try {
      obj.data = JSON.parse(obj.data);
      if (obj.data != null) {
        pbar = obj.data.pbar;
        start(obj.index, obj.data.words);
      }
    } catch (e) {
      alert(e);
    }
  });
}

function start(idx, ids)
{
  index = idx;
  words = ids;
  update();

  // Update progress bar
  var pg = $('div#card .progress');
  pg.children('.bg-secondary').css('width', 100 * pbar['new'] / pbar.total + '%');
  pg.children('.bg-danger').css('width', 100 * pbar.fail / pbar.total + '%');
  pg.children('.bg-warning').css('width', 100 * (pbar.total - pbar['new'] - pbar.pass - pbar.fail) / pbar.total + '%');

  // Hide unit selection, show word card
  $('div#card > ul').html("<h1>Loading, please wait...</h1>");
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
  // Update progress bars
  updateProgress();
}

function update()
{
  if (index >= words.length) {
    back();
    return;
  }

  id = words[index];
  $.getJSON('get/word_stats.php?uid=' + uid + '&id=' + id, function(obj) {
    word = obj;
    updateButtons(word);

    // Section info
    section = word.sid;
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
  });
}

function updateButtons(stats)
{
  if (!stats)
    stats = {skip: 0, weight: 0};
  weight = stats.weight;

  origin = Math.max(Math.min(stats.weight, maxweight - scale), -maxweight + scale);
  var sscale = 50 / scale;
  var yellow = 50;    // Weight 0 centre at 50%
  yellow -= sscale * origin;
  var red = yellow - maxweight * sscale;
  var green = yellow + maxweight * sscale;
  $('#slider').val(50 + (stats.weight - origin) * sscale);
  $('#slider').css("background", "linear-gradient(to right, red " + red + "%, yellow " + yellow + "%, lime " + green + "%)");

  var num = function(i) {return i ? ' (' + i + ')' : '';};
  $('#buttons .btn-warning').text('Skip' + num(stats.skip));
}

function submit(skip, weight)
{
  if (index >= words.length) {
    back();
    return;
  }
  if (submitting)
    return;
  submitting = true;
  weight = Math.max(Math.min(weight, maxweight), -maxweight);
  $.getJSON('set/stats_update.php?uid=' + uid + '&id=' + words[index] + '&skip=' + skip + '&weight=' + weight, function(obj) {
    console.log(obj);
    updateButtons(obj);
    index++;
    update();
    sessionUpdate();
    submitting = false;
  });
}

function submitSlider()
{
  submit(false, ($("#slider").val() - 50) / 50 * scale + origin);
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
        // Calculate progress bars
        pbar = {'total': 0, 'new': 0, 'pass': 0, 'fail': 0};
        for (i in secs) {
          var sec = secs[i];
          for (j in sec.units) {
            var p = progress.filter(function(a) {return a.sid == sec.sid && a.unit == sec.units[j].unit})[0];
            pbar.total += p.total;
            pbar['new'] += p['new'];
            pbar.pass += p.pass;
            pbar.fail += p.fail;
          }
        }
        start(0, obj);
        sessionSave();
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
        // Update progress bars
        updateProgress();
      });
  }
});

// Text input field
$('#test').on('input', function(e) {
  var s = reduce(e.target.value);
  if (e.target.value === '?' || e.target.value === '？') {
    show(false);
  } else if (s === text || s === annot) {
    if (hide || pass) {
      $(this).removeClass('text-warning text-danger');
      $(this).addClass('text-success');
      pass = true;
      show(false);
    } else {
      $(this).removeClass('text-success text-danger');
      $(this).addClass('text-warning');
    }
  } else {
    $(this).removeClass('text-success text-warning');
    $(this).addClass('text-danger');
  }
});

$('#test').on('keyup', function(e) {
  var s = reduce(e.target.value);
  if (e.which == 13) {
    if (s === text || s === annot) {
      if (pass)
        submit(false, weight + 1);
      else
        submit(true, weight - 1);
    } else if (e.shiftKey) {
      submit(false, weight - 2);
    }
  }
});

// Progress bar
$('#slider').on('mouseup', submitSlider);
$('#slider').on('touchend', submitSlider);
//$('#slider').on('change', submitSlider);

// Control buttons
$('#buttons .btn-primary').click(function() {show(!hide);});
$('#buttons .btn-warning').click(function() {submit(true, weight - 1);});
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
  if (uid)
    $('#utoken').val('#' + uid);
  $('#ulogin').modal();
}

function updateUID(u) {
  uid = u;
  updateProgress();
  $('#uid').text(uid);
  $('#ulogin').modal('hide');
  if ($('#ucookies').is(':checked'))
    setCookie('uid', u, 30);
  sessionRestore();
}

function setCookie(cname, cvalue, exdays) {
  var d = new Date();
  d.setTime(d.getTime() + (exdays*24*60*60*1000));
  var expires = "expires="+ d.toUTCString();
  document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
  var name = cname + "=";
  var decodedCookie = decodeURIComponent(document.cookie);
  var ca = decodedCookie.split(';');
  for(var i = 0; i <ca.length; i++) {
    var c = ca[i];
    while (c.charAt(0) == ' ') {
      c = c.substring(1);
    }
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }
  return "";
}

// Login dialog
$('#ulogin').on('shown.bs.modal', function() {$('#utoken').focus();});

// Login submit
$('#ulogin .btn-primary').click(function() {
  val = $('#utoken').val().trim();
  if (!val) {
    alert('Please provide a token string as identifier');
    return;
  }
  if (val.length == 33 && val.startsWith('#'))
    // Hashed user ID
    updateUID(val.slice(1));
  else
    $.post('get/hash.php', $('#utoken').val(), updateUID);
});

// User ID label
$('#uid').on('click', userLogin);

uid=getCookie("uid");
if (!uid)
  userLogin();
else
  updateUID(uid);
