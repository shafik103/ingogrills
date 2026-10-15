jQuery(function ($) {
  var frame;
  $('#ig_gallery_btn').on('click', function (e) {
    e.preventDefault();
    if (!frame) {
      frame = wp.media({ title: 'Product gallery', multiple: true, library: { type: 'image' } });
      frame.on('select', function () {
        var ids = [], html = '';
        frame.state().get('selection').each(function (a) {
          ids.push(a.id);
          var s = a.attributes.sizes;
          html += '<img src="' + ((s && s.thumbnail) ? s.thumbnail.url : a.attributes.url) + '" width="80" height="80" style="border:1px solid #ccd">';
        });
        $('#ig_gallery_ids').val(ids.join(','));
        $('#ig_gallery_preview').html(html);
      });
    }
    frame.open();
  });
  $('#ig_gallery_clear').on('click', function () {
    $('#ig_gallery_ids').val('');
    $('#ig_gallery_preview').empty();
  });
});
