function make_uploader(button, urlbox, title) {
	var file_frame;

	jQuery(button).live('click', function(event) {

		event.preventDefault();

		// If the media frame already exists, reopen it.
		if(file_frame) {
			file_frame.open();
			return;
		}

		// Create the media frame.
		file_frame = wp.media.frames.file_frame = wp.media({
			title: title,
			button: { text: "Select" },
			multiple: false  // Set to true to allow multiple files to be selected
		});

		// When an image is selected, run a callback.
		file_frame.on( 'select', function() {
			// We set multiple to false so only get one image from the uploader
			attachment = file_frame.state().get('selection').first().toJSON();

			// Save the returned url
			jQuery(urlbox).val(attachment['url']);
		});

		// Finally, open the modal
		file_frame.open();
	});
};

make_uploader('#mbt_upload_sample_button', '#mbt_sample_url', 'Sample Chapter Image');
make_uploader('#mbt_upload_tax_image_button', '#mbt_tax_image_url', 'Taxonomy Image');