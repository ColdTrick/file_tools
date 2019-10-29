/**
 * Provides interactive tree structure for browsing the files
 */
define(function(require) {
	
	var elgg = require('elgg');
	var $ = require('jquery');
	var Ajax = require('elgg/Ajax');
	var serializer = require('jquery.serializejson');

	var move_file = function(file_guid, to_folder_guid, draggable) {
		elgg.action('file_tools/file/move', {
			data: {
				file_guid: file_guid,
				folder_guid: to_folder_guid
			},
			success: function(result) {
				draggable.remove();
			}
		});
	};

	var bulk_delete = function(e) {
		e.preventDefault();

		$checkboxes = $('#file_tools_list_files input[type="checkbox"]:checked');

		if (!$checkboxes.length) {
			return;
		}

		if (!confirm(elgg.echo('deleteconfirm'))) {
			return;
		}
		
		var postData = $checkboxes.serializeJSON();

		if ($('#file_tools_list_files input[type="checkbox"][name="folder_guids[]"]:checked').length && confirm(elgg.echo('file_tools:folder:delete:confirm_files'))) {
			postData.files = 'yes';
		}

		$('#file_tools_list_files_container .elgg-ajax-loader').show();

		elgg.action('file_tools/bulk_delete', {
			data: postData,
			success: function(res){
				$.each($checkboxes, function(key, value) {
					$('#elgg-object-' + $(value).val()).remove();
				});

				$('#file_tools_list_files_container .elgg-ajax-loader').hide();
			}
		});
	};
	
	/**
	 * Makes all directories in the folder menu droppable
	 */
	$('#file-tools-folder-tree .elgg-menu-content').droppable({
		accept: '.file-tools-file-list > .elgg-item',
		tolerance: 'pointer',
		drop: function(event, ui) {
			droppable = $(this);
			draggable = ui.draggable;

			drop_id = droppable.attr('id').split('-').pop();
			drag_id = draggable.attr('id').split('-').pop();

			move_file(drag_id, drop_id, draggable);
		}
	});

	// folders in the list are droppable
	$('.file-tools-file-list .elgg-item-object-folder').droppable({
		accept: '.file-tools-file-list > .elgg-item',
		drop: function(event, ui){
			droppable = $(this);
			draggable = ui.draggable;

			drop_id = droppable.attr('id').split('-').pop();
			drag_id = draggable.attr('id').split('-').pop();

			move_file(drag_id, drop_id, draggable);
		}
	});
	
	// files and folders in list are draggable
	$('.file-tools-file-list > .elgg-item').draggable({
		revert: 'invalid',
		opacity: 0.8,
		appendTo: 'body',
	});

	$(document).on('click', '#file_tools_action_bulk_delete', bulk_delete);
});
