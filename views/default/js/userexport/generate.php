/* <script> */

elgg.provide('elgg.userexport');

elgg.userexport.total = 0;

/**
 * Initialize userexport feature
 */
elgg.userexport.init = function() {
	// Bind form submit button to the javascript method
	$('.elgg-form-userexport-export').live('submit', elgg.userexport.submit);

	// Get the total amount of users
	elgg.userexport.total = $('#userexport-data').data('total'); 
};

/**
 * Start csv file generation and hide the form
 * 
 * @param {Object} event
 */
elgg.userexport.submit = function(event) {
	var form = $('.elgg-form-userexport-export');

	// Verify that user has selected at least one field
	var checked = form.find('input[type=checkbox]:checked').length;
	if (checked === 0) {
		elgg.register_error(elgg.echo('userexport:error:nofields'));
		event.preventDefault();
		return;
	}

	// Initialize the progressbar
	$('#elgg-progressbar-userexport').progressbar({
		value: 0,
		max: elgg.userexport.total
	});

	elgg.userexport.processBatch(0);
	// Hide the form
	form.hide();
	// Show progressbar, status text, etc.
	$('.userexport-hidden').show();
	event.preventDefault();
};

/**
 * Call the action via XHR until csv file generation has been finished.
 * 
 * @param {Number} $offset
 */
elgg.userexport.processBatch = function(offset) {
	$('.elgg-form-userexport-export').find('input[name=offset]').val(offset);
	var data = $('.elgg-form-userexport-export').serialize();

	elgg.action('userexport/export', {
		data: data,
		dataType: 'json',
		success: function(json) {
			// Increase progressbar
			$('#elgg-progressbar-userexport').progressbar({value: json.offset});

			// Continue if there are still users left
			if (json.offset < elgg.userexport.total) {
				visibleOffset = json.offset;
				elgg.userexport.processBatch(json.offset);
			} else {
				visibleOffset = elgg.userexport.total;
				$('#userexport-download').show();
				$('#userexport-redo').show();
			}

			$('#userexport-user-counter').html(elgg.echo('userexport:progress', [visibleOffset, elgg.userexport.total]));
		}
	});
};

elgg.register_hook_handler('init', 'system', elgg.userexport.init);