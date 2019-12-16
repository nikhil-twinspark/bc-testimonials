jQuery(document).ready(function (jQuery) {
  // Instantiates the variable that holds the media library frame.
  var meta_image_frame;
  // Runs when the image button is clicked.
  jQuery('.bc-testimonial-image-upload').click(function (e) {
    // Get preview pane
    var meta_image_preview = jQuery(this).parent().children('.image-preview');
    // Prevents the default action from occuring.
    e.preventDefault();
    var meta_image = jQuery(this).parent().children('.meta-image');
    // If the frame already exists, re-open it.
    if (meta_image_frame) {
      meta_image_frame.close();
      // return;
    }
    // Sets up the media library frame
    meta_image_frame = wp.media.frames.meta_image_frame = wp.media({
      title: meta_image.title,
      button: {
        text: meta_image.defaultValue
      }
    });
    // Runs when an image is selected.
    meta_image_frame.on('select', function () {
      // Grabs the attachment selection and creates a JSON representation of the model.
      var media_attachment = meta_image_frame.state().get('selection').first().toJSON();
      // Sends the attachment URL to our custom image input field.
      meta_image.val(media_attachment.url);
      meta_image_preview.children('img').attr('src', media_attachment.url);
    });
    // Opens the media library frame.
    meta_image_frame.open();
  });

  jQuery('.bc-testimonial-image-remove').click(function (e) {
    // Get preview pane
    var meta_image_preview = jQuery(this).parent().children('.image-preview');
    // Prevents the default action from occuring.
    e.preventDefault();
    var meta_image = jQuery(this).parent().children('.meta-image');

    meta_image.val('');
    meta_image_preview.children('img').attr('src', '');
    
    
  });
});
