<?php

	global $CONFIG;

	$old_context = elgg_get_context();

	$page_owner_guid 	= get_input("page_owner");
	$page_owner 		= get_entity($page_owner_guid);
	$folder_guid 		= get_input("folder_guid", 0);
	$draw_page 			= get_input("draw_page", true);

	$sort_by 			= get_input('sort_by');
	$direction 			= get_input('direction');
	
	if(empty($sort_by)){
		$sort_value = 'e.time_created';
		if($page_owner instanceof ElggGroup && !empty($page_owner->file_tools_sort)){
			$sort_value = $page_owner->file_tools_sort;
		} elseif($site_sort_default = elgg_get_plugin_setting("sort", "file_tools")){
			$sort_value = $site_sort_default;
		}
		
		$sort_by = $sort_value;
	} 
	
	if(empty($direction)){
		$sort_direction_value = 'asc';
		if($page_owner instanceof ElggGroup && !empty($page_owner->file_tools_sort_direction)){
			$sort_direction_value = $page_owner->file_tools_sort_direction;
		} elseif($site_sort_direction_default = elgg_get_plugin_setting("sort_direction", "file_tools")){
			$sort_direction_value = $site_sort_direction_default;
		}
		
		$direction = $sort_direction_value;
	}
	
	if(!empty($page_owner) && (($page_owner instanceof ElggUser) || ($page_owner instanceof ElggGroup)))
	{
		// set page owner & context
		set_page_owner($page_owner_guid);
		elgg_set_context("file");

		group_gatekeeper();

		$wheres = array();
		$wheres[] = "NOT EXISTS (
					SELECT 1 FROM {$CONFIG->dbprefix}entity_relationships r 
					WHERE r.guid_two = e.guid AND
					r.relationship = '" . FILE_TOOLS_RELATIONSHIP . "')";

		$files_options = array(
				"type" => "object",
				"subtype" => "file",
				"limit" => false,
				"container_guid" => $page_owner_guid
			);

		$files_options["joins"][] = "JOIN {$CONFIG->dbprefix}objects_entity oe on oe.guid = e.guid";

		if($sort_by == 'simpletype')
		{
			$files_options["order_by_metadata"] = array('name' => 'mimetype', 'direction' => $direction);
		}
		else
		{
			$files_options["order_by"] = $sort_by . ' ' . $direction;
		}

		if($folder_guid !== false)
		{
			if($folder_guid == 0)
			{
				if(elgg_get_plugin_setting("user_folder_structure", "file_tools") == "yes")
				{
					$files_options["wheres"] = $wheres;
				}
				
				$files = elgg_get_entities($files_options);	
			} 
			else
			{
				$folder = get_entity($folder_guid);

				$files_options["relationship"] = FILE_TOOLS_RELATIONSHIP;
				$files_options["relationship_guid"] = $folder_guid;
				$files_options["inverse_relationship"] = false;

				$files = elgg_get_entities_from_relationship($files_options);	
			}	
		}

		if(elgg_get_plugin_setting("user_folder_structure", "file_tools") == "yes")
		{
			if(!$draw_page)
			{
				echo elgg_view("file_tools/list/files", array("folder" => $folder, "files" => $files, 'sort_by' => $sort_by, 'direction' => $direction));
			}
			else
			{			
				// get data for tree
				$folders = file_tools_get_folders($page_owner_guid, $vars['vars']);
	
				// default lists all unsorted files
				if($folder_guid === false)
				{
					if(elgg_get_plugin_setting("user_folder_structure", "file_tools") == "yes")
					{
						$files_options["wheres"] = $wheres;
					}
					
					$files = elgg_get_entities($files_options);
				}
				
				
				// build page elements
				$tree = '';
				if(elgg_get_plugin_setting("user_folder_structure", "file_tools") == "yes")
				{
					$tree = elgg_view("file_tools/list/tree", array("folder" => $folder, "folders" => $folders));
				}
	
				$body = '<div id="file_tools_list_files_container">' . elgg_view("ajax/loader") . '</div>
						<div class="contentWrapper">';
	
				if(elgg_get_page_owner_entity()->canEdit())
				{
					$body .= '<a id="file_tools_action_bulk_delete" href="javascript:void(0);">' . elgg_echo("file_tools:list:delete_selected") . '</a> | ';
				} 
	
				$body .= '<a id="file_tools_action_bulk_download" href="javascript:void(0);">' . elgg_echo("file_tools:list:download_selected") . '</a>
							<a id="file_tools_select_all" style="float: right;" href="javascript:void(0);">' . elgg_echo("file_tools:list:select_all") . '</a>
						</div>';
			
	
				$title = elgg_view_title($title_text);

				echo elgg_view_page($title_text, elgg_view_layout("one_sidebar", array('content' => $title . $body, 'sidebar' => $tree)));
			}
		}
		else
		{
			$title_text = elgg_echo("file:yours");

			$offset = (int)get_input('offset', 0);
			$title = elgg_view_title($title_text);

			$body = elgg_list_entities(array('types' => 'object', 'subtypes' => 'file', 'container_guid' => elgg_get_page_owner_guid(), 'limit' => 10, 'offset' => $offset, 'full_view' => false));

			echo elgg_view_page($title_text, elgg_view_layout("one_sidebar", array('content' => $title . $body, 'sidebar' => $tree)));
		}
	}
	else
	{
		forward();
	}