/**
 * Generate a CSV file through multiple XHR request
 */
define(function(require){
	var elgg = require('elgg');
	var $ = require('jquery');

	var total = 0;

	// Get the total amount of users
	total = $('#userexport-data').data('total');

	/**
	 * Start CSV file generation and hide the form
	 *
	 * @param {Object} event
	 */
	var submitTest = function(event) {
		event.preventDefault();

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
			max: total
		});

		processBatch(0);

		// Hide the form
		form.hide();

		// Show progressbar, status text, etc.
		$('.userexport-hidden').show();
	};

	/**
	 * Call the action via XHR until CSV file generation has been finished.
	 *
	 * @param {Number} $offset
	 */
	processBatch = function(offset) {
		$('.elgg-form-userexport-export').find('input[name=offset]').val(offset);
		var data = $('.elgg-form-userexport-export').serialize();

		elgg.action('userexport/export', {
			data: data,
			dataType: 'json',
			success: function(json) {
				// Increase progressbar
				$('#elgg-progressbar-userexport').progressbar({value: json.offset});

				// Continue if there are still users left
				if (json.offset < total) {
					visibleOffset = json.offset;
					processBatch(json.offset);
				} else {
					visibleOffset = total;
					$('#userexport-download').show();
					$('#userexport-redo').show();
				}

				$('#userexport-user-counter').html(elgg.echo('userexport:progress', [visibleOffset, total]));
			}
		});
	};

	// Bind form submit button to the javascript method
	$(document).on('submit', '.elgg-form-userexport-export', submitTest);
});
