define(function(require) {
	var elgg = require('elgg');
	var $ = require('jquery');

	$(document).on('click', '#file_tools_select_all', function(e) {
		e.preventDefault();
		
		var select_all_visible = $(this).find('.elgg-anchor-label span:first').is(':visible');
		$('#file_tools_list_files input[type="checkbox"]').prop('checked', select_all_visible);
		
		$(this).find('.elgg-anchor-label > span').toggle();
	});
	
	$(document).on('click', '#file_tools_action_bulk_download', function(e) {
		e.preventDefault();

		$checkboxes = $('#file_tools_list_files input[type="checkbox"]:checked');

		if ($checkboxes.length) {
			elgg.forward(elgg.security.addToken('action/file_tools/bulk_download?' + $checkboxes.serialize()));
		}
	});
});
