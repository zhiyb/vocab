// Word copy dialog 'show'
$('#word_copy').on('shown.bs.modal', function() {autosize.update($('#word_copy textarea'));});

// Word list 'copy'
$('.word_list').on('click', '.word_copy', function() {
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
