var index = 0;
var hide = true;
var words = [];
var section = 0;
var unit = "";

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
  $.post('get/stats.php', JSON.stringify(obj), function(ret) {
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
  } catch (e) {
    style = {};
  }

  show(true);
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
  $.post('set/stats_increment.php', JSON.stringify(obj), function(ret) {
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
  var bar = $('div#card .progress-bar');
  bar.text((index + 1) + '/' + words.length);
  bar.css('width', ((index + 1) * 100 / words.length) + '%');
  var word = words[index];
  $('div#card > ul').html(wordElement(word, h));
  hide = h;
}

function refreshSections(refresh)
{
}

function refreshUnits(sid)
{
  refreshWord($('.word_list li[wid]:first'));
}

// Start button
$('button#submit').click(function() {
  var secs = [];
  $('li').each(function() {
    var sec = {sid: $(this).attr('sid'), units: []};
    $(this).find('input').each(function() {
      if (!$(this).is(':checked'))
        return;
      sec.units.push({unit: $(this).attr('unit')});
    });
    if (sec.units.length)
      secs.push(sec);
  });
  if (!secs.length)
    alert('Please select units');
  else
    $.post('get/random.php', JSON.stringify(secs), function(ret) {
      try {
        var obj = JSON.parse(ret);
        start(obj);
      } catch (e) {
        alert(e);
      }
    });
});

// Control buttons
$('#buttons .btn-primary').click(function() {show(!hide);});
$('#buttons .btn-success').click(function() {submit('yes');});
$('#buttons .btn-warning').click(function() {submit('skip');});
$('#buttons .btn-danger').click(function() {submit('no');});
$('#buttons .btn-secondary').click(back);
