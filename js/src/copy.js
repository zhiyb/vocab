function html2html(html)
{
  return html.replace(/<div((?!<\/div>).)*<\/div>/g, '').replace(/<\/*span[^>]*>/g, '').replace(/\s*style="[^"]*"/g, '');
}

function html2text(html)
{
  return html.replace(/<br[^>]*>/g, '\n').replace(/<rp>((?!<\/rp>).)*<\/rp>/g, '').replace(/<rt>((?!<\/rt>).)*<\/rt>/g, '').replace(/<[^>]*>/g, '');
}

function html2annot(html)
{
  return html.replace(/<br[^>]*>/g, '\n').replace(/<rp>[^<]*<\/rp>/g, '').replace(/<ruby>((?!(<rt>|<\/ruby>)).)*<rt>/g, '').replace(/<\/rt>((?!(<rt>|<\/ruby>)).)*<rt>/g, '').replace(/<[^>]*>/g, '');
}

function html2plain(html)
{
  return html.replace(/<br[^>]*>/g, '\n').replace(/<\/?rt>|<\/?rp>|<\/?ruby>/g, '');
}

// Word copy dialog 'show'
$('#word_copy').on('shown.bs.modal', function() {autosize.update($('#word_copy textarea'));});

// Word list 'copy'
$('.word_list').on('click', '.word_copy', function() {
  var wid = $(this).closest('li').attr('wid');
  var html = html2html($(this).closest('.list-group-item-heading').html());
  var text = html2text(html);
  var ruby = html2annot(html);
  var plain = html2plain(html);
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
