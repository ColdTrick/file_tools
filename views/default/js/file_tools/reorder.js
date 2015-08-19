/**
 * Allows users to reorder files using drag & drop
 */
define(function(require) {
	var elgg = require('elgg');
	var $ = require('jquery');
	var tree = require('file_tools/tree');
	var file_tools = require('file_tools/site');

	$("#file_tools_list_tree a").droppable({
		"accept": ".file_tools_file",
		"hoverClass": "ui-state-hover",
		"tolerance": "pointer",
		"drop": function(event, ui) {
			var file_move_url = elgg.get_site_url() + "file_tools/proc/file/move";
			var file_guid = $(ui.draggable).prev("input").val();

			if (file_guid == undefined) {
				file_guid = $(ui.draggable).attr('id').replace('file_','');
			}

			var folder_guid = $(this).attr("id");
			var selected_folder_guid = tree.get_selected_tree_folder_id();

			file_tools.show_loader($(ui.draggable));

			$(ui.draggable).hide();

			$.post(file_move_url, {
				"file_guid": file_guid,
				"folder_guid": folder_guid
			}, function(data) {
				file_tools.load_folder(selected_folder_guid);
			});
		},
		"greedy": true
	});
});