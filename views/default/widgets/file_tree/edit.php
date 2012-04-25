<?php 

	$widget = $vars["entity"];
	
	if($folders = file_tools_get_folders($widget->owner_guid)){
		$selected_folders = $widget->getMetadata("folder_guids");
		if(!empty($selected_folders) && !is_array($selected_folders)){
			$selected_folders = array($selected_folders);
		} elseif(empty($selected_folders)){
			$selected_folders = array();
		}
		
		echo elgg_echo("widgets:file_tree:edit:select");
		echo "<div class='file_tree_widget_edit_folder_wrapper'>";
		echo elgg_view("input/hidden", array("name" => "params[folder_guids][]", "value" => "")); // needed to be able to empty the list
		echo file_tools_build_widget_options($folders, "params[folder_guids][]", $selected_folders);
		echo "</div>";
	}
