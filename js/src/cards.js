var index = 0;
var hide = true;
var words = [];

function start(ws)
{
    index = 0;
    words = ws;
    show(true);

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

function next()
{
    index++;
    show(true);
}

function submit(type)
{
    if (index >= words.length) {
        back();
        return;
    }
    //alert(type);
    next();
}

function show(h)
{
    if (index >= words.length) {
        back();
        return;
    }
    var word = words[index];
    $('div#card > ul').html(wordElement(word, h));
    hide = h;
}

// Start button
$('button#submit').click(function() {
    var secs = [];
    $('li').each(function() {
        var sec = {id: $(this).attr('id'), units: []};
        $(this).find('input').each(function() {
            if (!$(this).is(':checked'))
                return;
            sec.units.push({unit: $(this).attr('id')});
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
