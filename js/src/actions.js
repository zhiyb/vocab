// JSON editor 'add'
$('body').on('click', '.jsonedit .btn-success', function() {
  var tr = $(this).closest('tr');
  var field = tr.find('#field').val();
  var value = tr.find('#value').val();
  if (field === "")
    return;
  $(this).removeClass('btn-success');
  $(this).addClass('btn-danger');
  $(this).children().removeClass('fa-plus');
  $(this).children().addClass('fa-times');
  tr.parent().append(jsonfield('', ''));
  autosize(tr.parent().find('tr').last().find('textarea'));
});

// JSON editor 'remove'
$('body').on('click', '.jsonedit .btn-danger', function() {
  $(this).closest('tr').remove();
});

// Initialise
autosize($('textarea'));
refreshSections(true);
