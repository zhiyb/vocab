function start(words)
{
    // Show words
    var html = '';
    var i;
    for (i in words) {
        var word = words[i];
        html = html.concat('<li class="list-group-item">#' + word.id
            + ' ' + word.word + ' | ' + word.unit + ' #' + word.wid + '</li>');
    }
    $('div#words > ul').html(html);

    // Hide sections, show words
    $('div#words').show(ani);
    $('div#sections').hide(ani);
    $("html, body").animate({scrollTop: 0}, "slow");
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
            } catch (e) {
                alert(e);
            }
            start(obj);
        });
});
